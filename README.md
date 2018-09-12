# SilverStripe Sisow Method #

Addon for SilverStripe Omnipay. This addon gives you the option to manage the different Sisow pay methods in the CMS. You can enable/disable each method, give you own name for the method and change the display order.
Sisow is a Dutch payment provider with many international payment methods. See www.sisow.nl.

### Requirements ###

SilverStripe 4
silverstripe-omnipay 3
hestec/omnipay-sisow (the gateway for Omnipay)

### Version ###

Using Semantic Versioning.

### Installation ###

Install via Composer:

composer require "hestec/silverstripe-sisowmethod": "2.*"

### Configuration ###

Add the Sisow keys to your yaml file:
```
SilverStripe\Omnipay\GatewayInfo:
  Sisow:
    parameters:
      merchantId: 'xxxxxxxxxxx'
      merchantKey: 'xxxxxxxxxxxxxxxxxxxxxxxxxx'
      testMode: false
  ```

do a dev/build and flush.

### Usage ###

In your CMS Settings you will find a tab Sisow Method.

### Issues ###

No known issues.

### Todo ###
