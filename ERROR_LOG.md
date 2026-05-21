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

---
