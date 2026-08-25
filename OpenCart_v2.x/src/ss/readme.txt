====================================================
PlugnPay Smart Screens v2 Module (CC & ACH)
Payment Module for OpenCart v2.3.0.2
====================================================

***** IMPORTANT NOTES *****
This module is being provided "AS IS".  Limited technical support assistance will
be given to help diagnose/address problems with this module.  The amount of
support provided is up to PlugnPay's staff.

It is recommended if you experience a problem with this module, first seek
assistance through this module's readme file, then check with the OpenCart community
at 'www.opencart.com', and if you are still unable to resolve the issue, contact us
via PlugnPay's Online Helpdesk.

This module does not require your server to have SSL abilities.  OpenCart will ask
the customer for all of their contact info, before redirecting them to a SSL secured
billing page hosted on our server. The authorization will be done via PlugnPay's
Smart Screens v2 payment method & redirects the customer back to OpenCart after the
payment has been processed.

At this time, the module is not intended to itemize the order within the PlugnPay
system using available information from the cart.  This will likely be addressed in
future releases to this payment module.

If you want to change the behavior of this module, please feel free to make changes
to the files yourself.  However, customized OpenCart modules will not be provided
support assistance.
***************************

This module is designed to use PlugnPay's Smart Screens v2 payment method.
Its designed for allow for processing of credit cards & ACH/eCheck payments.
Please install other PlugnPay modules for alternative payment abilities.

This module is intended to charge customer's order as a single payment.
This single payment is applied at time of the customer's purchase.


To Install:

1. Unzip the coments of this zip file into the root directory of OpenCart on your server.

* Ensure you preserve the folder structure, so that the files are dropped into the correct locations.

They should be as follows:

admin/language/en-gb/extension/payment/plugnpay_ss.php
admin/controller/extension/payment/plugnpay_ss.php
admin/view/template/extension/payment/plugnpay_ss.tpl
admin/view/image/payment/plugnpay.png
catalog/language/en-gb/extension/payment/plugnpay_ss.php
catalog/controller/extension/payment/plugnpay_ss.php
catalog/model/extension/payment/plugnpay_ss.php
catalog/view/theme/default/template/extension/payment/plugnpay_ss.tpl

2. Go into your OpenCart admin panel.

3. Proceed to the "Extensions -> Payment" page.

4. Use the [Install] option to install the "PlugnPay (SSv2)" extension to the cart.

5. Use the [Edit] option to edit the "PlugnPay (SSv2)" extension settings.

5. Fill in the appropriate data

PlugnPay Username => [REQUIRED] enter username of your PlugnPay acccount
Authorization Verification Hash Key => [optional] key to use with PnP's authorization hash ability
Callback URL => (cart defined) where to redirect to OpenCart after payment is completed
Response Verification Hash Key => [optional] key to use with PnP response hash ability
Auto Settle Transactions => set if PnP should auto-mark successful payments for settlement
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



History:

01/21/2017
- initial release

01/23/2017
- minor bug fixes
- simplified the callback process
- cleaned up some related code

