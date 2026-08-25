# PlugnPay Remote API (ACH / eCheck) for OpenCart 4.1.x

Module version **1.0.0**. Target: OpenCart **4.1.0.3**.

ACH / eCheck payments via PlugnPay Remote API
(`https://pay1.plugnpay.com/payment/pnpremote.cgi`).

OpenCart collects bank-account data on the storefront and posts from the
server with PHP cURL. This increases PCI / ACH scope. **ACH must be enabled**
on the gateway account. For hosted (lower PCI) checkout, use Smart Screens v2
(`plugnpay_ss2.ocmod.zip`).

## When to use this vs credit card Remote API

| | ACH / eCheck (this module) | Credit Card Remote API |
|---|---|---|
| Data collected | Routing, account, check number | Card number, expiry, CVV |
| `paymethod` | `onlinecheck` | `credit` |
| Extra requirement | ACH enabled on the gateway account | Card processing enabled |
| Check type | `WEB` | n/a |

Neither module includes OpenCart admin Capture / Void / Refund.

## Requirements

- OpenCart **4.1.x** (tested target **4.1.0.3**)
- PHP **8.1+** with **cURL + OpenSSL**
- Storefront **HTTPS** (the method is hidden at checkout without it)
- PlugnPay **gateway account username** with ACH enabled
- **Remote Client Password** from PlugnPay Security Administration
  (`publisher-password` — not your admin login password)

There is no public demo / test publisher and no Test/Production toggle.

## Install

1. Download `plugnpay_api_ach.ocmod.zip`. **Do not rename it.** OpenCart 4 uses
   the filename as the extension code (`extension/plugnpay_api_ach/`).
2. Admin → Extensions → Installer → Upload, then Install.
3. Admin → Extensions → Payments → Install **PlugnPay Remote API (ACH / eCheck)**.
4. Edit and enter Gateway Account and Remote Client Password. Set Authorization
   Type and Order Status. Enable the method.
5. Place a test order over HTTPS using merchant-supplied credentials.

Payment class/files are named `plugnpay_ach` (no digits). OpenCart 4 cannot
autoload a PHP class whose name contains a digit.

## Configuration

| Setting | Notes |
|---|---|
| Gateway Account | PlugnPay username (`publisher-name`); ACH must be enabled |
| Remote Client Password | `publisher-password` from Security Administration |
| Authorization Type | `authonly` or `authpostauth` |
| Total | Optional minimum order total |
| Order Status | Pending for authonly; Processing / paid for authpostauth |
| Geo Zone | Optional restriction |
| Debug Logging | Sanitized logs under `storage/logs/plugnpay_api_ach_YYYYMMDD.log` |
| Status / Sort Order | Enable and ordering among payment methods |

**Authorization Type**

- `authonly` — authorize only; use a Pending / unpaid order status; settle in PlugnPay Admin
- `authpostauth` — authorize and settle; use a paid / processing status

The method is hidden at checkout when Gateway Account or Remote Client Password
is empty, PHP cURL is missing, or the storefront is not HTTPS.

## Checkout flow

1. Customer enters account owner, checking/savings, routing number, account
   number, check number, and an optional business-account flag.
2. Pay Now POSTs to `extension/plugnpay_api_ach/payment/plugnpay_ach.confirm`.
3. The module POSTs Remote API fields to `pnpremote.cgi` with PHP cURL
   (`paymethod=onlinecheck`, `checktype=WEB`).
4. On `FinalStatus=success`, order history is updated and the customer is sent
   to checkout success.
5. On decline / error, the gateway message is shown and checkout stays on the
   confirm step so the customer can retry.

### Key fields submitted to `pnpremote.cgi`

| Field | Purpose |
|---|---|
| `publisher-name` | Merchant gateway account |
| `publisher-password` | Remote Client Password (never logged) |
| `mode` / `paymethod` | `auth` / `onlinecheck` |
| `authtype` | `authonly` or `authpostauth` |
| `card-amount` / `currency` | Order total |
| `card-name` | Account owner |
| `accttype` | `checking` or `savings` |
| `routingnum` / `accountnum` / `checknum` | Bank data (never logged in full) |
| `checktype` | `WEB` |
| `acctclass` / `commcardtype` | `business` when the business checkbox is set |
| `orderID` / `acct_code` | OpenCart order id |
| `client` | `OpenCart_API_ACH` |
| `dontsndmail` | `yes` |

## Logging

Enable **Debug Logging**. Sanitized logs are written as:

```
storage/logs/plugnpay_api_ach_YYYYMMDD.log
```

Never logged in full: routing number, account number, check number, or
publisher-password.

## Troubleshooting

| Symptom | What to check |
|---|---|
| Method does not appear at checkout | Empty credentials, Status disabled, geo zone mismatch, missing cURL, or storefront is not HTTPS |
| Gateway declines ACH | Confirm ACH is enabled on the PlugnPay account |
| “page can't be found” after Install / Edit | Confirm the zip was not renamed (`plugnpay_api_ach.ocmod.zip`) and files are `payment/plugnpay_ach.php` |
| Unable to communicate with the payment gateway | PHP cURL, outbound HTTPS to `pay1.plugnpay.com`, firewall |
| Need to capture / void / refund | Use PlugnPay Merchant Admin (not this OpenCart module) |

## Manual test checklist

- [ ] Module installs under Admin → Extensions → Payments
- [ ] Empty Gateway Account or Remote Client Password hides the method
- [ ] HTTP (non-HTTPS) storefront hides the method
- [ ] Approved `authonly` creates an unpaid/pending order with AUTH + orderID comment
- [ ] Declined ACH shows the gateway message and stays on checkout
- [ ] Debug log redacts routing / account / check number / password
- [ ] Business-account checkbox sends `acctclass=business`
- [ ] Order edit screen has **no** Capture / Void / Refund tools for this module

## Support

Provided AS IS. See [PlugnPay docs](https://docs.plugnpay.com/).
