<?php

require_once( 'vendor/autoload.php' );

use League\Csv\Writer;

/**
 * Config
 */
$dotenv = new Dotenv\Dotenv( __DIR__ );
$dotenv->load();

//TODO :: move api key to dotenv
\Stripe\Stripe::setApiKey(getenv('STRIPE_API_KEY'));

$timezone = getenv('STRIPE_TIMEZONE');

/**
 * Preparation
 */

//timezone
date_default_timezone_set( $timezone  );

//macOS CSV line ending fix
if ( ! ini_get( "auto_detect_line_endings" ) ) {
	ini_set( "auto_detect_line_endings", '1' );
}

//csv
$header = [ 'Date', 'Amount', 'Payee', 'Description', 'Reference' ];
$csv    = Writer::createFromString( '' );
$csv->insertOne( $header );


/**
 * STRIPE CUSTOMER <--> EMAIL MAPPING
 */
echo "Fetching all Stripe customers... \n";

$customer_email = array();

$customers = \Stripe\Customer::all( array( "limit" => 100 ) );

foreach ( $customers->autoPagingIterator() as $customer ) {
	$customer_email [ $customer->id ] = $customer->email;
}

/**
 * STRIPE CHARGE <--> EMAIL MAPPING
 */
echo "Fetching all Stripe charges... \n";

$charge_email = array();

$charges = \Stripe\Charge::all( array( "limit" => 100 ) );

foreach ( $charges->autoPagingIterator() as $charge ) {
	$charge_email[ $charge->id ] = $customer_email[ $charge->customer ];
}

/**
 * STRIPE REFUND <--> EMAIL MAPPING
 */
echo "Fetching all Stripe refunds... \n";

$refund_email = array();

$refunds = \Stripe\Refund::all( array( "limit" => 100 ) );

foreach ( $refunds->autoPagingIterator() as $refund ) {
	$refund_email [ $refund->id ] = $charge_email[ $refund->charge ];
}


//date object
$txn_date = new DateTime();

echo "Fetching all Stripe balance affecting transactions... \n";

//Get Stripe balance affecting transactions
$transactions = \Stripe\BalanceTransaction::all( array( "limit" => 100 ) );

foreach ( $transactions->autoPagingIterator() as $transaction ) {
	$row = array();

	//csv-date
	$row[] = ( new DateTime( '@' . $transaction->created ) )->setTimezone( new DateTimeZone( $timezone ) )->format( 'd/m/Y' );

	//csv-amount
	$row[] = $transaction->amount / 100;

	//csv-Payee
	switch ( $transaction->type ) {
		case 'charge':
			$row[] = $charge_email[ $transaction->source ];
			break;
		case 'refund':
			$row[] = $refund_email[ $transaction->source ];
			break;
		case 'payout':
			$row[] = 'Stripe';
			break;
		default :
			echo "TODO :: unknown transaction of type $transaction->type encountered \n";
			var_dump( $transaction->__toArray( true ) );
	}

	//csv-Description
	$row[] = $transaction->description;

	//csv-reference
	$row[] = $transaction->source;

	//insert $row into CSV
	$csv->insertOne( $row );

	//Handle non-zero fees
	if ( $transaction->fee ) {
		//only if it's stripe fee
		if ( 'stripe_fee' === $transaction->fee_details[0]['type'] ) {
			$row = array();

			//csv-date
			$row[] = ( new DateTime( '@' . $transaction->created ) )->setTimezone( new DateTimeZone( $timezone ) )->format( 'd/m/Y' );

			//csv-amount
			$row[] = $transaction->fee / 100;

			//csv-Payee
			$row[] = 'Stripe';

			//csv-Description
			$row[] = $transaction->fee_details[0]['description'];

			//csv-reference
			$row[] = $transaction->source;

			//insert $row into CSV
			$csv->insertOne( $row );
		} else {
			// non stripe fee
			echo "TODO :: unknown fees of type" . $transaction->fee_details[0]['type'] . "encountered \n";
			var_dump( $transaction->__toArray( true ) );
		}
	}
}

echo $csv; //returns the CSV document as a string

file_put_contents( "stripe-xero-" . ( new DateTime() )->format( 'd-M-Y-h-i-a' ) . ".csv", $csv );