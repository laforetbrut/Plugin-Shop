# Error Log

Internal log of issues found and fixed, with prevention rules.

---

## [2026-05-21] — Unrestricted package file upload (M7)

**Context:** Security review of the package creation/edition flow (`PackageController::store` / `update`).
**Error:** The `file` field in `PackageRequest` was validated only with `['nullable', 'file']`. Combined with `ManageFiles::storeFile()`, an admin holding `shop.packages` could upload an arbitrary file (including `.php`) to a web-accessible disk while keeping its original extension.
**Root cause:** Missing `mimes` / `extensions` / `max` constraints on the upload rule.
**Fix:** Restricted the rule to `['nullable', 'file', 'mimes:zip,rar,7z,jar,txt,pdf', 'max:51200']`.
**Prevention:** Every file upload rule must declare an explicit allow-list of extensions and a size limit. Never validate an upload with `file` alone.

## [2026-05-21] — SVG allowed for package/offer images (L1)

**Context:** Security review of image validation in `PackageRequest` and `OfferRequest`.
**Error:** Image rules used `image:allow_svg`, accepting SVG files which can embed JavaScript and lead to stored XSS when served from the webroot.
**Root cause:** Explicit opt-in to SVG without sanitization or a forced `Content-Disposition: attachment`.
**Fix:** Removed `allow_svg` from both rules → `['nullable', 'image']`. SVG is now rejected.
**Prevention:** Do not enable `allow_svg` unless SVG content is sanitized and served as an attachment under a restrictive CSP.

## [2026-05-21] — Shop login by username only (L2)

**Context:** Security review of `LoginController::login`.
**Error:** When `shop.guest_purchases` is enabled, a shop session is attached to a user from the username alone, with no password — allowing username impersonation in the shop context.
**Root cause:** Upstream Azuriom behavior of the guest-purchase feature.
**Fix:** No code change. Mitigation is configuration-only: keep `shop.guest_purchases` disabled unless strictly required.
**Prevention:** Review the implications of `shop.guest_purchases` before enabling it; if needed, add an e-mail/token verification step before binding an existing username.

## [2026-05-21] — Package files stored on a web-accessible disk (V2)

**Context:** Security re-audit of `ManageFiles::storeFile()` and the package download flow.
**Error:** `filesDisk()` returned `Storage::disk()` (the default `public` disk, mapped to `public/storage`). The purchase check in `PackageController::downloadFile()` could be bypassed by requesting the file directly at `/storage/shop/packages/<name>`.
**Root cause:** Downloadable files were stored on a web-accessible disk instead of a private one.
**Fix:** `filesDisk()` now returns `Storage::disk('local')` (private, outside the webroot). Migration `2026_05_21_000000_move_shop_package_files_to_private_disk` moves existing files.
**Prevention:** Files gated by an access check must be stored on a non-public disk and served only through the controller that enforces the check.

## [2026-05-21] — PayPal capture amount not re-verified (V3)

**Context:** Security re-audit of `PayPalCheckoutMethod::capturePayPalOrder()`.
**Error:** The capture handler checked the `COMPLETED` status but never compared the captured `amount.value` / `currency_code` with `$payment->price` / `$payment->currency`.
**Root cause:** Missing amount and currency verification in the payment capture logic.
**Fix:** Added `isCapturedAmountValid()`; a capture whose amount or currency differs from the expected payment is rejected via `invalidPayment()`.
**Prevention:** Always re-verify the amount and currency reported by the payment provider against the local payment before delivering an order.

## [2026-05-21] — Operator precedence bug in Mollie webhook (V4)

**Context:** Security re-audit of `MollieMethod::notification()`.
**Error:** `$molliePayment->metadata?->mode ?? '' === 'subscription_first'` — `??` binds tighter than `===`, so the expression evaluated as `$mode ?? ('' === 'subscription_first')`.
**Root cause:** Missing parentheses around the null-coalescing expression.
**Fix:** Parenthesized: `(($molliePayment->metadata?->mode ?? '') === 'subscription_first')`.
**Prevention:** Always parenthesize `??` when combined with comparison operators.

## [2026-05-21] — Inconsistent PayPal gateway config keys (V5)

**Context:** Security re-audit of `PayPalCheckoutMethod` configuration handling.
**Error:** Gateway config keys mixed separators (`client-id` with a hyphen, `webhook_id` with an underscore), making a wrong key name easy to introduce and silently break webhook verification.
**Root cause:** Inconsistent naming convention for gateway configuration keys.
**Fix:** Unified `client-id` to `client_id` in the method, validation rules and admin view. Migration `2026_05_21_100000_rename_paypal_checkout_client_id_key` renames the key in existing gateways.
**Prevention:** Keep a single, consistent naming convention for all configuration keys.

## [2026-05-21] — TypeError in PayPal webhook verification

**Context:** Re-audit of `PayPalCheckoutMethod::verifyPayPalWebhook()` after the upstream commit `010ce59`.
**Error:** The method is typed `: string` but returns `response()->json(...)` on invalid JSON and on a failed signature check, causing a `TypeError` at runtime whenever a webhook fails verification.
**Root cause:** Upstream replaced `abort()` (which throws and never returns) with `return response()->json(...)` without widening the declared return type.
**Fix:** Changed the return type to `string|JsonResponse`; `notification()` relays the response when verification fails, otherwise uses the returned event type.
**Prevention:** When changing a control-flow statement from `abort()`/`throw` to `return`, update the method return type accordingly.

## [2026-05-21] — Package file migration skipped on local default disk (F1)

**Context:** Re-audit of migration `2026_05_21_000000_move_shop_package_files_to_private_disk`.
**Error:** `up()` returned early when `config('filesystems.default') === 'local'` and used `Storage::disk()` (the default disk) instead of explicitly targeting the web-accessible `public` disk. Existing files left on the `public` disk would never be relocated.
**Root cause:** The migration relied on the default disk name instead of targeting the disk where the vulnerable code actually stored files.
**Fix:** `up()` now moves files explicitly from `public` to `local` (and `down()` the reverse), with no guard on `filesystems.default`.
**Prevention:** A data migration must target the concrete source/destination explicitly, not infer it from environment-dependent defaults.

## [2026-05-21] — Upload file name stored without sanitization (F2)

**Context:** Re-audit of `ManageFiles::storeFile()`.
**Error:** The client-provided `getClientOriginalName()` was stored verbatim as the file label, keeping control characters and non-ASCII spoofing characters (e.g. RTL override).
**Root cause:** Missing sanitization of the client-controlled file name.
**Fix:** Added `sanitizeFileName()` — `Str::ascii()` normalization, control-character removal and a 255-character cap — applied in `storeFile()`.
**Prevention:** Always sanitize client-provided names before persisting them, even when downstream output is escaped.

---
