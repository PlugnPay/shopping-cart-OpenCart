# PlugnPay Remote API (Credit Card) for OpenCart 4.1.x

Module version **1.0.0**. Target: OpenCart **4.1.0.3**.

Credit card payments via PlugnPay Remote API
(`https://pay1.plugnpay.com/payment/pnpremote.cgi`).

OpenCart collects card data on the storefront and posts from the server with
PHP cURL. This increases PCI DSS scope. For hosted (lower PCI) checkout, use
Smart Screens v2 (`plugnpay_ss2.ocmod.zip`).

## When to use this vs Smart Screens v2

| | Remote API (this module) | Smart Screens v2 |
|---|---|---|
| Card data | Collected on your store | Collected on PlugnPay |
| Customer experience | Stays on your checkout | Redirect to hosted billing page |
| PCI scope | Higher | Lower |
| Checkout endpoint | `pnpremote.cgi` (server-to-server) | `https://pay1.plugnpay.com/pay/` |
| Transaction mode | `authonly` or `authpostauth` | Authorization-only |
| Admin Capture / Void / Refund in OpenCart | No | No |
| Public demo account | None | None |

## Requirements

- OpenCart **4.1.x** (tested target **4.1.0.3**)
- PHP **8.1+** with **cURL + OpenSSL**
- Storefront **HTTPS** (the method is hidden at checkout without it)
- PlugnPay **gateway account username** (`publisher-name`)
- **Remote Client Password** from PlugnPay Security Administration
  (`publisher-password` — not your admin login password)

There is no public demo / test publisher and no Test/Production toggle.

## Install

1. Download `plugnpay_api_cc.ocmod.zip`. **Do not rename it.** OpenCart 4 uses
   the filename as the extension code (`extension/plugnpay_api_cc/`).
2. Admin → Extensions → Installer → Upload, then Install.
3. Admin → Extensions → Payments → Install **PlugnPay Remote API (Credit Card)**.
4. Edit and enter Gateway Account and Remote Client Password. Set Authorization
   Type and Order Status. Enable the method.
5. Place a test order over HTTPS using merchant-supplied credentials.

Payment class/files are named `plugnpay_cc` (no digits). OpenCart 4 cannot
autoload a PHP class whose name contains a digit.

## Configuration

| Setting | Notes |
|---|---|
| Gateway Account | PlugnPay username (`publisher-name`) |
| Remote Client Password | `publisher-password` from Security Administration |
| Authorization Type | `authonly` or `authpostauth` |
| Request CVV | Collect card security code (on by default) |
| Total | Optional minimum order total |
| Order Status | Pending for authonly; Processing / paid for authpostauth |
| Geo Zone | Optional restriction |
| Debug Logging | Sanitized logs under `storage/logs/plugnpay_api_cc_YYYYMMDD.log` |
| Status / Sort Order | Enable and ordering among payment methods |

**Authorization Type**

- `authonly` — authorize only; use a Pending / unpaid order status; settle in PlugnPay Admin
- `authpostauth` — authorize and settle (sale); use a paid / processing status

The method is hidden at checkout when Gateway Account or Remote Client Password
is empty, PHP cURL is missing, or the storefront is not HTTPS.

## Checkout flow

1. Customer enters card owner, number, expiration (MM/YY), and optional CVV on
   the OpenCart confirm step.
2. Pay Now POSTs to `extension/plugnpay_api_cc/payment/plugnpay_cc.confirm`.
3. The module POSTs hyphenated Remote API fields to `pnpremote.cgi` with PHP cURL.
4. On `FinalStatus=success`, order history is updated (AUTH + orderID) and the
   customer is sent to checkout success.
5. On decline / error, the gateway message is shown and checkout stays on the
   confirm step so the customer can retry.

### Key fields submitted to `pnpremote.cgi`

| Field | Purpose |
|---|---|
| `publisher-name` | Merchant gateway account |
| `publisher-password` | Remote Client Password (never logged) |
| `mode` / `paymethod` | `auth` / `credit` (`checkcard` when amount is `$0`) |
| `authtype` | `authonly` or `authpostauth` |
| `card-amount` / `currency` | Order total |
| `card-number` / `card-exp` / `card-name` / `card-cvv` | Card data (never logged in full) |
| `card-address*` / `phone` / `email` | Billing |
| `shipname` / `address*` | Shipping (when present) |
| `orderID` / `acct_code` | OpenCart order id |
| `ipaddress` | Client IP |
| `client` | `OpenCart_API_CC` |
| `dontsndmail` | `yes` (OpenCart sends its own confirmation) |
| `itemN` / `costN` / `quantityN` / `descriptionN` | Line items |

## Logging

Enable **Debug Logging**. Sanitized logs are written as:

```
storage/logs/plugnpay_api_cc_YYYYMMDD.log
```

Never logged in full: card number, CVV, or publisher-password.

## Troubleshooting

Test connectivity from the web server:

```bash
curl -d "publisher-name=YOUR_ACCOUNT&publisher-password=YOUR_REMOTE_PASSWORD&mode=auth&authtype=authonly&card-name=cardtest&card-number=4111111111111111&card-exp=01/30&card-cvv=123&card-amount=1.23" https://pay1.plugnpay.com/payment/pnpremote.cgi
```

You should receive a URL-encoded response containing `FinalStatus=…`.
A blank response usually means firewall / outbound HTTPS / DNS / missing cURL.

| Symptom | What to check |
|---|---|
| Method does not appear at checkout | Empty Gateway Account or Remote Client Password, Status disabled, geo zone mismatch, missing cURL, or storefront is not HTTPS |
| “page can't be found” after Install / Edit | Confirm the zip was not renamed (`plugnpay_api_cc.ocmod.zip`) and files are `payment/plugnpay_cc.php` |
| Unable to communicate with the payment gateway | PHP cURL, outbound HTTPS to `pay1.plugnpay.com`, firewall |
| Decline / fraud message | Expected; customer stays on confirm and can retry |
| Need to capture / void / refund | Use PlugnPay Merchant Admin (not this OpenCart module) |

## Manual test checklist

- [ ] Module installs under Admin → Extensions → Payments
- [ ] Empty Gateway Account or Remote Client Password hides the method
- [ ] HTTP (non-HTTPS) storefront hides the method
- [ ] Approved `authonly` creates an unpaid/pending order with AUTH + orderID comment
- [ ] Approved `authpostauth` uses the configured paid/processing status
- [ ] Declined card shows the gateway message and stays on checkout
- [ ] Debug log redacts PAN / CVV / password
- [ ] Geo Zone restriction works
- [ ] Order edit screen has **no** Capture / Void / Refund tools for this module

## Support

Provided AS IS. See [PlugnPay docs](https://docs.plugnpay.com/).
