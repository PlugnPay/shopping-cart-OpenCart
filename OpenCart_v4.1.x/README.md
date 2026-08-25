# OpenCart v4.1.x — PlugnPay Smart Screens v2

Payment module for OpenCart Shopping Cart **v4.1.x** (target **4.1.0.3**). Current package: Smart Screens v2 hosted checkout.

Module version **1.0.1**.

## Choose a module

| | Smart Screens v2 |
|---|---|
| Extension code | `plugnpay_ss2` |
| Download | [plugnpay_ss2.ocmod.zip](./plugnpay_ss2.ocmod.zip) |
| Checkout | Redirect → `https://pay1.plugnpay.com/pay/` |
| Card data on your server | No |
| PCI scope | Lower |
| Authorization | Authorization-only (`pb_post_auth=no`) |
| Admin Capture / Void / Refund | No (use PlugnPay Admin) |
| Status | Current for OpenCart 4.1.x |

## Smart Screens v2 (hosted)

- Source: [src/](./src/)
- Download: [plugnpay_ss2.ocmod.zip](./plugnpay_ss2.ocmod.zip)
- Quick install: [INSTALL_SS2.txt](./INSTALL_SS2.txt)
- Docs: [src/README.md](./src/README.md)
- Module version: **1.0.1**

Redirects customers to PlugnPay hosted Smart Screens. OpenCart does **not** collect sensitive payment data. Successful auths leave the order **Pending** until settled in PlugnPay Admin.

### Requirements

- OpenCart **4.1.x** (tested target **4.1.0.3**)
- Storefront HTTPS (required for the return POST)
- Admin → Settings → Server → **Session Samesite Cookie** = **None** (PlugnPay returns via POST)
- PHP 8.1+

### Install steps

1. Download [plugnpay_ss2.ocmod.zip](./plugnpay_ss2.ocmod.zip) (do not rename — the installer uses the filename as the extension code).
2. Admin → Extensions → Installer → upload the zip, then Install.
3. Admin → Extensions → Payments → Install **PlugnPay Smart Screens v2**.
4. Enter Gateway Account. Set Order Status to Pending. Enable the method.
5. Place a test order using merchant-supplied PlugnPay credentials (no public demo publisher).

If a previous upload showed **page can't be found** after Install or Edit: Uninstall the payment method, Uninstall/remove the zip in Extensions → Installer, delete leftover `extension/plugnpay_ss2/` if present, then upload this 1.0.1 zip. OpenCart 4 cannot autoload a payment class whose name contains a digit (`PlugnpaySs2`); this package uses `smart_screens`.

## Development layout

```
OpenCart_v4.1.x/
  README.md
  INSTALL_SS2.txt
  plugnpay_ss2.ocmod.zip
  src/
    install.json
    admin/
    catalog/
    system/library/
  tests/
```
