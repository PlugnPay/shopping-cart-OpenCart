# Shopping Cart - OpenCart Payment Modules

Easy to install payment modules for the OpenCart shopping cart.
**OpenCart 4.1** includes Smart Screens v2 (hosted checkout). **OpenCart 3.0** has Remote API (onsite card and ACH) and Smart Screens v2. **OpenCart 2.3** has the same methods for the 2.x extension path. **OpenCart 1.5** is Remote API only.

## Downloads by OpenCart version

### OpenCart v4.1.x (current — target 4.1.0.3)

* **Smart Screens v2** — gateway hosted checkout
  - [Download](./OpenCart_v4.1.x/plugnpay_ss2.ocmod.zip)
  - Source: [./OpenCart_v4.1.x/src/](./OpenCart_v4.1.x/src/)
  - Docs: [package README](./OpenCart_v4.1.x/README.md) · [INSTALL_SS2.txt](./OpenCart_v4.1.x/INSTALL_SS2.txt) · [module README](./OpenCart_v4.1.x/src/README.md)

Package overview: [./OpenCart_v4.1.x/README.md](./OpenCart_v4.1.x/README.md)

### OpenCart v3.x (legacy — target 3.0.3.2)

* **Remote API (Credit Card)** — onsite card collection via `pnpremote.cgi`
  - [Download](./OpenCart_v3.x/opencart_3.0.3_api_cc_module.zip)
  - Source: [./OpenCart_v3.x/src/api_cc/](./OpenCart_v3.x/src/api_cc/)
  - Docs: [package README](./OpenCart_v3.x/README.md) · [INSTALL_API_CC.txt](./OpenCart_v3.x/INSTALL_API_CC.txt) · [module readme](./OpenCart_v3.x/src/api_cc/readme.txt)
* **Remote API (ACH / eCheck)** — onsite bank-account collection via `pnpremote.cgi`
  - [Download](./OpenCart_v3.x/opencart_3.0.3_api_ach_module.zip)
  - Source: [./OpenCart_v3.x/src/api_ach/](./OpenCart_v3.x/src/api_ach/)
  - Docs: [package README](./OpenCart_v3.x/README.md) · [INSTALL_API_ACH.txt](./OpenCart_v3.x/INSTALL_API_ACH.txt) · [module readme](./OpenCart_v3.x/src/api_ach/readme.txt)
* **Smart Screens v2** — gateway hosted checkout
  - [Download](./OpenCart_v3.x/opencart_3.0.3_ss2_module.zip)
  - Source: [./OpenCart_v3.x/src/ss2/](./OpenCart_v3.x/src/ss2/)
  - Docs: [package README](./OpenCart_v3.x/README.md) · [INSTALL_SS2.txt](./OpenCart_v3.x/INSTALL_SS2.txt) · [module readme](./OpenCart_v3.x/src/ss2/readme.txt)

Package overview: [./OpenCart_v3.x/README.md](./OpenCart_v3.x/README.md)

### OpenCart v2.x (legacy — target 2.3.0.2)

* **Remote API (Credit Card)** — onsite card collection via `pnpremote.cgi`
  - [Download](./OpenCart_v2.x/opencart_2.3_api_cc_module.zip)
  - Source: [./OpenCart_v2.x/src/api_cc/](./OpenCart_v2.x/src/api_cc/)
  - Docs: [package README](./OpenCart_v2.x/README.md) · [INSTALL_API_CC.txt](./OpenCart_v2.x/INSTALL_API_CC.txt)
* **Remote API (ACH / eCheck)** — onsite bank-account collection via `pnpremote.cgi`
  - [Download](./OpenCart_v2.x/opencart_2.3_api_ach_module.zip)
  - Source: [./OpenCart_v2.x/src/api_ach/](./OpenCart_v2.x/src/api_ach/)
  - Docs: [package README](./OpenCart_v2.x/README.md) · [INSTALL_API_ACH.txt](./OpenCart_v2.x/INSTALL_API_ACH.txt)
* **Smart Screens v2** — gateway hosted checkout (`plugnpay_ss`)
  - [Download](./OpenCart_v2.x/opencart_2.3_ss_module.zip)
  - Source: [./OpenCart_v2.x/src/ss/](./OpenCart_v2.x/src/ss/)
  - Docs: [package README](./OpenCart_v2.x/README.md) · [INSTALL_SS.txt](./OpenCart_v2.x/INSTALL_SS.txt)

Package overview: [./OpenCart_v2.x/README.md](./OpenCart_v2.x/README.md)

### OpenCart v1.5.x (legacy — target 1.5.6.3)

* **Remote API (Credit Card)** — onsite card collection via `pnpremote.cgi`
  - [Download](./OpenCart_v1.5.x/opencart_1.5_api_cc_module.zip)
  - Source: [./OpenCart_v1.5.x/src/api_cc/](./OpenCart_v1.5.x/src/api_cc/)
  - Docs: [package README](./OpenCart_v1.5.x/README.md) · [INSTALL_API_CC.txt](./OpenCart_v1.5.x/INSTALL_API_CC.txt)
* **Remote API (ACH / eCheck)** — onsite bank-account collection via `pnpremote.cgi`
  - [Download](./OpenCart_v1.5.x/opencart_1.5_api_ach_module.zip)
  - Source: [./OpenCart_v1.5.x/src/api_ach/](./OpenCart_v1.5.x/src/api_ach/)
  - Docs: [package README](./OpenCart_v1.5.x/README.md) · [INSTALL_API_ACH.txt](./OpenCart_v1.5.x/INSTALL_API_ACH.txt)

Package overview: [./OpenCart_v1.5.x/README.md](./OpenCart_v1.5.x/README.md)

## Installation

For complete instructions, open the README inside the zip (or the linked docs above).

### OpenCart 4.1.x — Smart Screens v2

1. Download [plugnpay_ss2.ocmod.zip](./OpenCart_v4.1.x/plugnpay_ss2.ocmod.zip) (do not rename the file).
2. Admin → Extensions → Installer → upload, then Install.
3. Admin → Extensions → Payments → Install and configure **PlugnPay Smart Screens v2**.
4. Set Session Samesite Cookie to **None** (Settings → Server) and use storefront HTTPS.

- Quick install: [OpenCart_v4.1.x/INSTALL_SS2.txt](./OpenCart_v4.1.x/INSTALL_SS2.txt)

### OpenCart 3.x

1. Download the zip for the module you want (API CC, API ACH, or Smart Screens v2).
2. Unzip into the OpenCart **root** so `admin/` and `catalog/` merge in place.
3. Admin → **Extensions** → **Payments** → Install and configure the method.

- Credit Card API: [OpenCart_v3.x/INSTALL_API_CC.txt](./OpenCart_v3.x/INSTALL_API_CC.txt)
- ACH API: [OpenCart_v3.x/INSTALL_API_ACH.txt](./OpenCart_v3.x/INSTALL_API_ACH.txt)
- Smart Screens v2: [OpenCart_v3.x/INSTALL_SS2.txt](./OpenCart_v3.x/INSTALL_SS2.txt)

### OpenCart 2.x / 1.5.x

Same overlay install. Use the matching `INSTALL_*.txt` in that version folder. OpenCart 1.5 uses the older `admin/.../payment/` and `catalog/.../payment/` paths (no `extension/`).

## Usage

### Smart Screens v2 (OpenCart 4.1 / 3.x / 2.x)

* Hosted checkout on PlugnPay; the cart does **not** collect sensitive payment data.
* Customer is redirected to PlugnPay, then returned to OpenCart after approval or decline.
* Storefront HTTPS is required for a reliable return URL.
* For OpenCart 4.1: transactions are authorization-only (`pb_post_auth=no`); successful orders stay Pending until settled in PlugnPay Admin. Set Session Samesite Cookie to **None**.
* Capture / void / refund are done in PlugnPay Merchant Admin.

### Remote API (Credit Card)

* OpenCart collects card data on your storefront (increases PCI scope).
* Authorization is submitted to PlugnPay via cURL (`pnpremote.cgi`).
* Storefront HTTPS and PHP cURL are required.
* Transaction method: Authorization or Capture (auto-settle), depending on the package.
* Capture / void / refund are done in PlugnPay Merchant Admin (not from OpenCart).

### Remote API (ACH / eCheck)

* OpenCart collects bank-account data on your storefront (increases PCI / ACH scope).
* Same Remote API path as credit card; install only if ACH is enabled on the gateway account.
* Storefront HTTPS and PHP cURL are required.

## Repository layout

```
shopping-cart-OpenCart/
  README.md
  .gitignore
  OpenCart_v4.1.x/            # current (4.1.0.3) — Smart Screens v2
    README.md
    INSTALL_SS2.txt
    plugnpay_ss2.ocmod.zip
    src/
    tests/
  OpenCart_v3.x/              # legacy (3.0.3.2) — Remote API CC/ACH + Smart Screens v2
    README.md
    INSTALL_API_CC.txt
    INSTALL_API_ACH.txt
    INSTALL_SS2.txt
    opencart_3.0.3_*_module.zip
    src/{api_cc,api_ach,ss2}/
  OpenCart_v2.x/              # legacy (2.3.0.2) — Remote API CC/ACH + Smart Screens v2
    README.md
    INSTALL_API_CC.txt
    INSTALL_API_ACH.txt
    INSTALL_SS.txt
    opencart_2.3_*_module.zip
    src/{api_cc,api_ach,ss}/
  OpenCart_v1.5.x/            # legacy (1.5.6.3) — Remote API CC/ACH
    README.md
    INSTALL_API_CC.txt
    INSTALL_API_ACH.txt
    opencart_1.5_*_module.zip
    src/{api_cc,api_ach}/
```

## Support

Provided AS IS. See [PlugnPay docs](https://docs.plugnpay.com/) and the module README for integration details.
