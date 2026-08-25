# OpenCart v3.x — PlugnPay Payment Modules

Payment modules for OpenCart Shopping Cart **v3.x** (target **3.0.3.2**). Current packages: Remote API (credit card and ACH) and Smart Screens v2.

## Choose a module

| | Remote API CC | Remote API ACH | Smart Screens v2 |
|---|---|---|---|
| Code | `plugnpay_api_cc` | `plugnpay_api_ach` | `plugnpay_ss2` |
| Download | [opencart_3.0.3_api_cc_module.zip](./opencart_3.0.3_api_cc_module.zip) | [opencart_3.0.3_api_ach_module.zip](./opencart_3.0.3_api_ach_module.zip) | [opencart_3.0.3_ss2_module.zip](./opencart_3.0.3_ss2_module.zip) |
| Checkout | Onsite card fields → `pnpremote.cgi` | Onsite ACH fields → `pnpremote.cgi` | Redirect → PlugnPay hosted checkout |
| Card/ACH on your server | Yes | Yes | No |
| PCI scope | Higher | Higher | Lower |
| Admin Capture / Void / Refund | No (use PlugnPay Admin) | No (use PlugnPay Admin) | No (use PlugnPay Admin) |
| Status | Current for OpenCart 3.x | Current for OpenCart 3.x | Current for OpenCart 3.x |

You may install more than one module; enable only the method(s) you need.

## Remote API — Credit Card (onsite)

- Source: [src/api_cc/](./src/api_cc/)
- Download: [opencart_3.0.3_api_cc_module.zip](./opencart_3.0.3_api_cc_module.zip)
- Quick install: [INSTALL_API_CC.txt](./INSTALL_API_CC.txt)
- Docs: [src/api_cc/readme.txt](./src/api_cc/readme.txt)

Collects card data on your OpenCart payment page and posts from the server to PlugnPay Remote API. Requires SSL and cURL.

## Remote API — ACH / eCheck (onsite)

- Source: [src/api_ach/](./src/api_ach/)
- Download: [opencart_3.0.3_api_ach_module.zip](./opencart_3.0.3_api_ach_module.zip)
- Quick install: [INSTALL_API_ACH.txt](./INSTALL_API_ACH.txt)
- Docs: [src/api_ach/readme.txt](./src/api_ach/readme.txt)

Collects bank-account data on your OpenCart payment page and posts from the server to PlugnPay Remote API. ACH must be enabled on the gateway account.

## Smart Screens v2 (hosted)

- Source: [src/ss2/](./src/ss2/)
- Download: [opencart_3.0.3_ss2_module.zip](./opencart_3.0.3_ss2_module.zip)
- Quick install: [INSTALL_SS2.txt](./INSTALL_SS2.txt)
- Docs: [src/ss2/readme.txt](./src/ss2/readme.txt)

Redirects customers to PlugnPay hosted Smart Screens. OpenCart does **not** collect sensitive payment data. Supports credit card, ACH, and other methods enabled on the account.

### Requirements

- OpenCart **3.x** (tested target **3.0.3.2**)
- PHP cURL + OpenSSL for Remote API
- Storefront HTTPS (required for Remote API; recommended for SS2)

### Install steps (all)

1. Download the zip for the module you want (or use `src/`).
2. Unzip into the OpenCart root so `admin/` and `catalog/` merge in place.
3. Admin → Extensions → Payments → Install and configure credentials / options.

## Development layout

```
OpenCart_v3.x/
  README.md
  INSTALL_API_CC.txt
  INSTALL_API_ACH.txt
  INSTALL_SS2.txt
  opencart_3.0.3_api_cc_module.zip
  opencart_3.0.3_api_ach_module.zip
  opencart_3.0.3_ss2_module.zip
  src/
    api_cc/
    api_ach/
    ss2/
```
