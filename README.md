## Stripe Xero CSV Export

The php script `stripe-xero.php` exports all Stripe balance affecting transactions into a CSV file recognisable by Xero Bank Account's import a CSV statement feature.

Currently it supports:

1. Charges
2. Refunds
3. Payouts

## Usage

Preparation

```
git clone https://github.com/rtcamp/stripe-xero-csv
cd stripe-xero-csv
composer install
```

Edit `.env` file to add correct [Stripe API Key](https://dashboard.stripe.com/account/apikeys).

Finally run the script:

```
php stripe-xero.php
```

It will create a file `stripe-xero-{date}.csv` with all balance affecting transactions from your Stripe account.

Xero will skip duplicates during import so you need to worry about previously imported transactions appearing in CSV.

## Email

The script supports emailing CSV as attachment to predefined email address.
This comes handy if you are running script as a CRON job.

As of now only Amazon AWS SES is supported.

You need to set values of following variables in `.env` file:

- `EMAIL_FROM`
- `EMAIL_TO`
- `AWS_ACCESS_KEY`
- `AWS_SECRET_KEY`

Please make sure:

1. `EMAIL_FROM` address must be verified already in SES console.
2. You are using IAM API user to generate `AWS_` credentials. SES SMTP credentials won't work.

### crontab

Add a line like below for weekly emails @ Monday 10am.

```
0 10 * * 1	cd /path/to/stripe-xero-csv && /usr/bin/php stripe-xero.php >> stripe-xero.log 2>&1
```

## Known Issues and Limitations

1. [Xero doesn't provide support for adding Stripe as a bank account](https://community.xero.com/business/discussion/2014947/). So we need to create a normal bank account with manual feed.
2. Stripe doesn't provide Xero friendly statements. [Xero supports many formats](https://help.xero.com/int/BankAccounts_Details_ImportTrans).
3. Xero API doesn't have provision to insert bank statement lines. It's second most popular [feature request](https://xero.uservoice.com/forums/5528-xero-accounting-api/suggestions/340274-import-bank-statement-lines-via-the-api) pending from eternity i.e. 2009! This is main reason that you need to run this script locally and import CSV files manually.


## TODO

- [ ] Add support to generate CSV for a specific duration. Something like _"this month"_, _"last month"_ should be enough to start with.

## LICENSE

[MIT](https://opensource.org/licenses/mit-license.php)

## Does this interest you?

<a href="https://rtcamp.com/"><img src="https://rtcamp.com/wp-content/uploads/2019/04/github-banner@2x.png" alt="Join us at rtCamp, we specialize in providing high performance enterprise WordPress solutions"></a>
