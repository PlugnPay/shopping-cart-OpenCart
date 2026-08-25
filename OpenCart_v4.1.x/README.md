# OpenCart v4.1.x — PlugnPay payment modules

Payment modules for OpenCart Shopping Cart **v4.1.x** (target **4.1.0.3**).

| | Smart Screens v2 | Remote API (Credit Card) | Remote API (ACH / eCheck) |
|---|---|---|---|
| Extension code | `plugnpay_ss2` | `plugnpay_api_cc` | `plugnpay_api_ach` |
| Download | [plugnpay_ss2.ocmod.zip](./plugnpay_ss2.ocmod.zip) | [plugnpay_api_cc.ocmod.zip](./plugnpay_api_cc.ocmod.zip) | [plugnpay_api_ach.ocmod.zip](./plugnpay_api_ach.ocmod.zip) |
| Checkout | Redirect → `https://pay1.plugnpay.com/pay/` | Onsite card form → `pnpremote.cgi` | Onsite ACH form → `pnpremote.cgi` |
| Card / bank data on your server | No | Yes | Yes |
| PCI scope | Lower | Higher | Higher (ACH must be enabled) |
| Authorization | Authorization-only (`pb_post_auth=no`) | `authonly` or `authpostauth` | `authonly` or `authpostauth` |
| Admin Capture / Void / Refund | No (use PlugnPay Admin) | No (use PlugnPay Admin) | No (use PlugnPay Admin) |
| Module version | **1.0.1** | **1.0.0** | **1.0.0** |

Do **not** rename the `.ocmod.zip` files. OpenCart 4 uses the filename as the extension code.

## Smart Screens v2 (hosted)

- Source: [src/ss2/](./src/ss2/)
- Download: [plugnpay_ss2.ocmod.zip](./plugnpay_ss2.ocmod.zip)
- Quick install: [INSTALL_SS2.txt](./INSTALL_SS2.txt)
- Docs: [src/ss2/README.md](./src/ss2/README.md)

Redirects customers to PlugnPay hosted Smart Screens. OpenCart does **not** collect sensitive payment data. Successful auths leave the order **Pending** until settled in PlugnPay Admin.

Requires storefront HTTPS and Session Samesite Cookie = **None**.

### Install steps

1. Download [plugnpay_ss2.ocmod.zip](./plugnpay_ss2.ocmod.zip) (do not rename).
2. Admin → Extensions → Installer → upload, then Install.
3. Admin → Extensions → Payments → Install **PlugnPay Smart Screens v2**.
4. Enter Gateway Account. Order Status defaults to Pending. Enable the method.
5. Set Session Samesite Cookie to **None**. Place a test order over HTTPS.

## Remote API (Credit Card)

- Source: [src/api_cc/](./src/api_cc/)
- Download: [plugnpay_api_cc.ocmod.zip](./plugnpay_api_cc.ocmod.zip)
- Quick install: [INSTALL_API_CC.txt](./INSTALL_API_CC.txt)
- Docs: [src/api_cc/README.md](./src/api_cc/README.md)

Collects card data on your storefront and posts with PHP cURL. Requires HTTPS
and PHP cURL. Use **authonly** with a Pending status, or **authpostauth** (sale)
with a paid/processing status.

### Install steps

1. Download [plugnpay_api_cc.ocmod.zip](./plugnpay_api_cc.ocmod.zip) (do not rename).
2. Admin → Extensions → Installer → upload, then Install.
3. Admin → Extensions → Payments → Install **PlugnPay Remote API (Credit Card)**.
4. Enter Gateway Account and Remote Client Password. Enable the method.
5. Place a test order over HTTPS using merchant-supplied credentials.

## Remote API (ACH / eCheck)

- Source: [src/api_ach/](./src/api_ach/)
- Download: [plugnpay_api_ach.ocmod.zip](./plugnpay_api_ach.ocmod.zip)
- Quick install: [INSTALL_API_ACH.txt](./INSTALL_API_ACH.txt)
- Docs: [src/api_ach/README.md](./src/api_ach/README.md)

Same Remote API path as credit card. Install only if ACH is enabled on the
gateway account. Requires HTTPS and PHP cURL.

### Install steps

1. Download [plugnpay_api_ach.ocmod.zip](./plugnpay_api_ach.ocmod.zip) (do not rename).
2. Admin → Extensions → Installer → upload, then Install.
3. Admin → Extensions → Payments → Install **PlugnPay Remote API (ACH / eCheck)**.
4. Enter Gateway Account and Remote Client Password. Enable the method.
5. Place a test order over HTTPS using merchant-supplied credentials.

### Shared requirements (all modules)

- OpenCart **4.1.x** (tested target **4.1.0.3**)
- PHP **8.1+**
- Storefront **HTTPS**
- Merchant-supplied PlugnPay credentials (no public demo publisher)

Remote API also requires PHP **cURL + OpenSSL**.

## Development layout

```
OpenCart_v4.1.x/
  README.md
  INSTALL_SS2.txt
  INSTALL_API_CC.txt
  INSTALL_API_ACH.txt
  plugnpay_ss2.ocmod.zip
  plugnpay_api_cc.ocmod.zip
  plugnpay_api_ach.ocmod.zip
  src/ss2/
  src/api_cc/
  src/api_ach/
  tests/
```
