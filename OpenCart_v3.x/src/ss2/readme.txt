====================================================
PlugnPay Smart Screens v2
Payment Module for OpenCart v3.0.3.2
====================================================

***** IMPORTANT NOTES *****
This module is being provided "AS IS".  Limited technical support assistance will
be given to help diagnose/address problems with this module.  The amount of
support provided is up to PlugnPay's staff.

It is recommended if you experience a problem with this module, first seek
assistance through this module's readme file, then check with the OpenCart community
at 'www.opencart.com', and if you are still unable to resolve the issue, contact us
via PlugnPay's Online Helpdesk.

This module doesn't requires your server to have SSL abilities, but is recommended.
PlugnPay will ask the customer for all of credit card info on our server's SSL secured
billing pages. The authorization will be done via PlugnPay's Smart Screens payment method.

At this time, the module does not intended itemize the order within the PlugnPay
system using available information from the cart.  This will likely be addressed in
future releases to this payment module.

If you want to change the behavior of this module, please feel free to make changes
to the files yourself.  However, customized OpenCart modules will not be provided
support assistance.
***************************

This module is designed to use PlugnPay's Smart Screens payment method.
It's designed for to process credit cards, ACH/eChecks & other payment types allowed.
Please install other PlugnPay modules for alternative payment abilities.

This module is intended to charge customer's order as a single payment.
This single payment is applied at time of the customer's purchase.


To Install:

1. Unzip the coments of this zip file into the root directory of OpenCart on your server.

* Ensure you preserve the folder structure, so that the files are dropped into the correct locations.

They should be as follows:

catalog/view/theme/default/template/extension/payment/plugnpay_ss2.twig
catalog/model/extension/payment/plugnpay_ss2.php
catalog/controller/extension/payment/plugnpay_ss2.php
catalog/language/en-gb/extension/payment/plugnpay_ss2.php
admin/view/template/extension/payment/plugnpay_ss2.twig
admin/view/image/payment/plugnpay.png
admin/controller/extension/payment/plugnpay_ss2.php
admin/language/en-gb/extension/payment/plugnpay_ss2.php

2. Go into your OpenCart admin panel.

3. Proceed to the "Extensions -> Payment" page.

4. Use the [Install] option to install the "PlugnPay (SS2)" extension to the cart.

5. Use the [Edit] option to edit the "PlugnPay (SS2)" extension settings.

5. Fill in the appropriate data

Gateway Account => [REQUIRED] enter username of your PlugnPay acccount
Success-Link URL => [shows callback URL to cart - shouldn't require editing]
Total => enter amount in which is required to active the payment module. (i.e. "0.01")
Order Status => select how OpenCart shows, if the approved authorizations. (i.e. "Processed")
Geo Zone => select geographical zone the payment module applies (i.e. "All Zones")
Status => use "Enabled" to turn-on the module & 'Disabled' to turn-off the module.
Sort Order => enter # in which the module will be listed (i.e. "0" lists it first)


If you run into problems:

Check to be sure you actually uploaded the files in the correct folders.

Check the uploaded file's permissions:
-- .php files should be chmod 755
   (read/write/execute by owner, read/execute by all others)
-- .png files should be chmod 644
   (read/write by owner, read by all others)

When processing a transaction and it fails, there should be a PnP generated error message in the response to your shopping cart.
This would tell the customer why PnP could not process the order.  If this is blank, then you should check your cart/connection. 

Should any customizations you make not appear, after initially installing the payment module, disable OpenCart's cache.
1. Login to the OpenCart Admin area
2. Click on the Dashboard option in left menu
3. Click on the blue box with the gear in it, at the top-right of the page
4. Under Component, set the Cache to 'off' for Theme, and click on the orange sync button to its right.
5. Repeat this for the SASS  Cache option as well.
6. Any customizations you make will shop up immediatly.
7. When completed making changes, re-enable the Theme & SASS cache options.


History:

12/30/2019
- initial release

08/07/2023
- added code to clear cart basket upon successful payment

