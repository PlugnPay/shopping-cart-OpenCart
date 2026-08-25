# OpenCart v2.x — PlugnPay Payment Modules

Packages for **OpenCart 2.x** (target **2.3.0.2**). Choose Remote API (credit card or ACH) or Smart Screens v2.

## Choose a module

| | Remote API CC | Remote API ACH | Smart Screens v2 |
|---|---|---|---|
| Code | `plugnpay_api_cc` | `plugnpay_api_ach` | `plugnpay_ss` |
| Download | [opencart_2.3_api_cc_module.zip](./opencart_2.3_api_cc_module.zip) | [opencart_2.3_api_ach_module.zip](./opencart_2.3_api_ach_module.zip) | [opencart_2.3_ss_module.zip](./opencart_2.3_ss_module.zip) |
| Checkout | Onsite card → `pnpremote.cgi` | Onsite ACH → `pnpremote.cgi` | Redirect → PlugnPay hosted checkout |
| Card/ACH on your server | Yes | Yes | No |
| Storefront SSL | Required (cURL) | Required (cURL) | Recommended |
| Status | Legacy for 2.3.x | Legacy for 2.3.x | Legacy for 2.3.x |

## Remote API — Credit Card

- Source: [src/api_cc/](./src/api_cc/)
- Download: [opencart_2.3_api_cc_module.zip](./opencart_2.3_api_cc_module.zip)
- Quick install: [INSTALL_API_CC.txt](./INSTALL_API_CC.txt)
- Vendor notes: [src/api_cc/readme.txt](./src/api_cc/readme.txt)

Overlay `admin/` and `catalog/` onto the OpenCart root. Requires PHP cURL and storefront HTTPS. Install **PlugnPay (API)** under Extensions → Payments.

## Remote API — ACH / eCheck

- Source: [src/api_ach/](./src/api_ach/)
- Download: [opencart_2.3_api_ach_module.zip](./opencart_2.3_api_ach_module.zip)
- Quick install: [INSTALL_API_ACH.txt](./INSTALL_API_ACH.txt)
- Vendor notes: [src/api_ach/readme.txt](./src/api_ach/readme.txt)

Same overlay as credit card. Install **PlugnPay (API ACH)**. ACH must be enabled on the gateway account.

## Smart Screens v2

- Source: [src/ss/](./src/ss/)
- Download: [opencart_2.3_ss_module.zip](./opencart_2.3_ss_module.zip)
- Quick install: [INSTALL_SS.txt](./INSTALL_SS.txt)
- Vendor notes: [src/ss/readme.txt](./src/ss/readme.txt)

Hosted billing on PlugnPay. Overlay `admin/` and `catalog/`; payment module code is `plugnpay_ss`. Customer returns to OpenCart after payment.

## Development layout

```
OpenCart_v2.x/
  README.md
  INSTALL_API_CC.txt
  INSTALL_API_ACH.txt
  INSTALL_SS.txt
  opencart_2.3_api_cc_module.zip
  opencart_2.3_api_ach_module.zip
  opencart_2.3_ss_module.zip
  src/
    api_cc/
    api_ach/
    ss/
```
