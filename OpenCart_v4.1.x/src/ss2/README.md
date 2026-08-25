# PlugnPay Smart Screens v2 for OpenCart 4.1.x

Module version **1.0.1**. Hosted checkout at `https://pay1.plugnpay.com/pay/`.

OpenCart does **not** collect card data. Transactions are authorization-only
(`pb_post_auth=no`). Capture, void, and refund are performed in PlugnPay
Merchant Admin.

## Package

Upload `plugnpay_ss2.ocmod.zip` via Admin → Extensions → Installer. The zip
filename must stay `plugnpay_ss2.ocmod.zip` so files land in
`extension/plugnpay_ss2/`.

Then Admin → Extensions → Payments → Install **PlugnPay Smart Screens v2**.

Payment class/files are named `smart_screens` (no digits). OpenCart 4 cannot
autoload a class such as `PlugnpaySs2` because it looks for
`plugnpay_ss_2.php`.

## Configuration

| Setting | Notes |
|---|---|
| Gateway Account | PlugnPay publisher username (required) |
| Success-Link URL | Callback shown for reference |
| Total | Minimum order total to offer this method |
| Order Status | Use Pending / unpaid |
| Geo Zone | Optional restriction |
| Debug Logging | Sanitized logs under `storage/logs/plugnpay_ss2_YYYYMMDD.log` |

## Return handling

PlugnPay POSTs to `extension/plugnpay_ss2/payment/smart_screens.callback`.
The module verifies:

- `pi_response_status=success`
- gateway account
- amount
- order id (`pt_account_code_1` / custom `ocorderid`)

The shopper session cookie may be missing after a cross-site POST. Set
**Session Samesite Cookie** to **None** (Settings → Server). Order matching
does not depend on the session cookie.

## Support

Provided AS IS. See [PlugnPay docs](https://docs.plugnpay.com/).

For onsite card or ACH collection on OpenCart 4.1.x, see the Remote API
modules (`plugnpay_api_cc.ocmod.zip` / `plugnpay_api_ach.ocmod.zip`).
