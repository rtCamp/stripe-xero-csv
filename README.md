## Stripe Xero CSV Export

The php script `stripe-xero.php` exports all Stripe balance affecting transactions into a CSV file recognisable by Xero Bank Account's import a CSV statement feature.

Currently it supports:

1. Charges
2. Refunds 
3. Payouts

## Usage

Preparation

```
git clone https://github.com/SCGlobal/stripe-xero-csv
cd stripe-xero-csv
composer install
```

Edit `.env` file to add correct [Stripe API Key](https://dashboard.stripe.com/account/apikeys).

Finally run the script:

```
php stripe-xero.php
```

It will create a file `stripe-xero-{date}.csv` inside the `statements` folder with all balance affecting transactions from your Stripe account.

Xero will skip duplicates during import so you need to worry about previously imported transactions appearing in CSV. 

## Known Issues and Limitations

1. [Xero doesn't provide support for adding Stripe as a bank account](https://community.xero.com/business/discussion/2014947/). So we need to create a normal bank account with manual feed. 
2. Stripe doesn't provide Xero friendly statements. [Xero supports many formats](https://help.xero.com/int/BankAccounts_Details_ImportTrans).
3. Xero API doesn't have provision to insert bank statement lines. It's second most popular [feature request](https://xero.uservoice.com/forums/5528-xero-accounting-api/suggestions/340274-import-bank-statement-lines-via-the-api) pending from eternity i.e. 2009! This is main reason that you need to run this script locally and import CSV files manually. 


## TODO

- [ ] Add support to generate CSV for a specific duration. Something like _"this month"_, _"last month"_ should be enough to start with.

## LICENSE

[MIT](https://opensource.org/licenses/mit-license.php)