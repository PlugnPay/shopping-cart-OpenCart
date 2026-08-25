# OpenCart v1.5.x — PlugnPay Payment Modules

Packages for **OpenCart 1.5.x** (credit-card package target **1.5.6.3**). Remote API only — onsite credit card and ACH / eCheck.

## Choose a module

| | Remote API CC | Remote API ACH |
|---|---|---|
| Code | `plugnpay_api` | `plugnpay_api_ach` |
| Download | [opencart_1.5_api_cc_module.zip](./opencart_1.5_api_cc_module.zip) | [opencart_1.5_api_ach_module.zip](./opencart_1.5_api_ach_module.zip) |
| Checkout | Onsite card → `pnpremote.cgi` | Onsite ACH → `pnpremote.cgi` |
| Card/ACH on your server | Yes | Yes |
| Storefront SSL | Required (cURL) | Required (cURL) |
| Status | Legacy for 1.5.x | Legacy for 1.5.x |

These packages use the OpenCart 1.5 paths (`admin/.../payment/` and `catalog/.../payment/`, not `extension/payment/`).

## Remote API — Credit Card

- Source: [src/api_cc/](./src/api_cc/)
- Download: [opencart_1.5_api_cc_module.zip](./opencart_1.5_api_cc_module.zip)
- Quick install: [INSTALL_API_CC.txt](./INSTALL_API_CC.txt)
- Vendor notes: [src/api_cc/readme.txt](./src/api_cc/readme.txt)

Overlay `admin/` and `catalog/` onto the OpenCart root. Install **PlugnPay (API)** under Extensions → Payments.

## Remote API — ACH / eCheck

- Source: [src/api_ach/](./src/api_ach/)
- Download: [opencart_1.5_api_ach_module.zip](./opencart_1.5_api_ach_module.zip)
- Quick install: [INSTALL_API_ACH.txt](./INSTALL_API_ACH.txt)
- Vendor notes: [src/api_ach/readme.txt](./src/api_ach/readme.txt)

Same overlay as credit card. Install **PlugnPay (API) - ACH/eCheck**. ACH must be enabled on the gateway account.

## Development layout

```
OpenCart_v1.5.x/
  README.md
  INSTALL_API_CC.txt
  INSTALL_API_ACH.txt
  opencart_1.5_api_cc_module.zip
  opencart_1.5_api_ach_module.zip
  src/
    api_cc/
    api_ach/
```
