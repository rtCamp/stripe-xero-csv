## Stripe Xero CSV Export

The php script `stripe-xero.php` exports all Stripe balance affecting transactions into a CSV file recognisable by Xero Bank Account's import a CSV statement feature.

Currently it supports:

1. Charges
2. Refunds 
3. Payouts

## Usage

Preparation

```
git clone something
cd something
composer install
```

Edit `.env` file to add correct [Stripe API Key](https://dashboard.stripe.com/account/apikeys).

Finally run the script:

```
php stripe-xero.php
```

It will create a file `stripe-xero-{date}.csv` with all balance affecting transactions from your Stripe account.

Xero will skip duplicates during import so you need to worry about previously imported transactions appearing in CSV. 

## TODO

- [ ] Add support to generate CSV for a specific duration. Something like _"this month"_, _"last month"_ should be enough to start with.



