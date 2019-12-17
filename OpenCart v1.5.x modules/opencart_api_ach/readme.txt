====================================================
PlugnPay API Module (ACH/eCheck Payments)
Payment Module for OpenCart v1.5.x
====================================================

***** IMPORTANT NOTES *****
This module is being provided "AS IS".  Limited technical support assistance will
be given to help diagnose/address problems with this module.  The amount of
support provided is up to PlugnPay's staff.

It is recommended if you experience a problem with this module, first seek
assistance through this module's readme file, then check with the OpenCart community
at 'www.opencart.com', and if you are still unable to resolve the issue, contact us
via PlugnPay's Online Helpdesk.

This module requires your server to have SSL abilities & CURL.  OpenCart will ask
the customer for all of their ACH/eCheck info on your server's SSL secured
billing pages. The authorization will be done via PlugnPay's API payment method.

At this time, the module does not intended itemize the order within the PlugnPay
system using available information from the cart.  This will likely be addressed in
future releases to this payment module.

If you want to change the behavior of this module, please feel free to make changes
to the files yourself.  However, customized OpenCart modules will not be provided
support assistance.
***************************

This module is designed to use PlugnPay's API payment method, via cURL.
Its also designed for processing ACH/eCheck payments only.
Please install other PlugnPay modules for alternative payment abilities.

This module is intended to charge customer's order as a single payment.
This single payment is applied at time of the customer's purchase.


To Install:

1. Unzip the coments of this zip file into the root directory of OpenCart on your server.

* Ensure you preserve the folder structure, so that the files are dropped into the correct locations.

They should be as follows:

catalog/controller/payment/plugnpay_api_ach.php
catalog/language/english/payment/plugnpay_api_ach.php
catalog/model/payment/plugnpay_api_ach.php
catalog/view/theme/default/template/payment/plugnpay_api_ach.tpl
admin/controller/payment/plugnpay_api_ach.php
admin/language/english/payment/plugnpay_api_ach.php
admin/view/template/payment/plugnpay_api_ach.tpl
admin/view/image/payment/plugnpay.png

2. Go into your OpenCart admin panel.

3. Proceed to the "Extensions -> Payment" page.

4. Use the [Install] option to install the "PlugnPay (API) - ACH/eCheck" extension to the cart.

5. Use the [Edit] option to edit the "PlugnPay (API) - ACH/eCheck" extension settings.

5. Fill in the appropriate data

Publisher-Name => enter your PlugnPay username.
Publisher-Password => enter your Remote Client Password.
Authorization Type => select to auto-settle transactions at time of the order or not.
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
This would tell the customer why PnP could not process the order.  If this is blank, then you should check your cURL connection. 

