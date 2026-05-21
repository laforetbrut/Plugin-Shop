# Changelog

All notable changes to Shop are documented here.

---

## [1.2.23] - 2026-05-21

### Fixed

- **Package file upload validation (M7)** — Added strict `mimes` and `max` rules to the package `file` field. Only `zip`, `rar`, `7z`, `jar`, `txt` and `pdf` archives/documents up to 50 MB can be uploaded; arbitrary files (including `.php`) are now rejected.
- **SVG rejected for images (L1)** — Removed `allow_svg` from the package and offer `image` validation rules to prevent stored XSS through scriptable SVG files served from the webroot.
- **Package files stored on a private disk (V2)** — Package downloadable files are now stored on the private `local` disk instead of the web-accessible `public` disk, so they can only be retrieved through the purchase-gated download controller. A migration moves existing files.
- **PayPal Checkout capture amount verification (V3)** — The amount and currency actually captured by PayPal are now compared against the expected payment before delivery; mismatching captures are rejected.
- **Mollie subscription webhook operator precedence (V4)** — Fixed an operator precedence bug (`??` bound tighter than `===`) that caused the subscription branch of the Mollie webhook to be evaluated incorrectly.
- **PayPal Checkout config key naming (V5)** — Unified the gateway configuration key `client-id` to `client_id` for naming consistency; a migration renames the key in existing gateways.
- **PayPal webhook verification return type** — Fixed a `TypeError` in `verifyPayPalWebhook()`, which returned an HTTP error response from a method typed to return a `string`. The method now returns `string|JsonResponse` and the error response is relayed by `notification()`.

### Correctifs

- **Validation de l'upload de fichier de package (M7)** — Ajout de règles strictes `mimes` et `max` sur le champ `file` d'un package. Seuls les archives/documents `zip`, `rar`, `7z`, `jar`, `txt` et `pdf` jusqu'à 50 Mo peuvent être déposés ; les fichiers arbitraires (dont `.php`) sont désormais refusés.
- **SVG refusé comme image (L1)** — Suppression de `allow_svg` des règles de validation `image` des packages et offres pour empêcher le XSS stocké via des fichiers SVG scriptables servis depuis le webroot.
- **Fichiers de package stockés sur un disque privé (V2)** — Les fichiers téléchargeables des packages sont désormais stockés sur le disque privé `local` au lieu du disque `public` accessible en HTTP ; ils ne sont récupérables que via le contrôleur de téléchargement contrôlant l'achat. Une migration déplace les fichiers existants.
- **Vérification du montant capturé PayPal Checkout (V3)** — Le montant et la devise réellement capturés par PayPal sont désormais comparés au paiement attendu avant la livraison ; les captures incohérentes sont rejetées.
- **Précédence d'opérateur dans le webhook d'abonnement Mollie (V4)** — Correction d'une erreur de précédence d'opérateur (`??` prioritaire sur `===`) qui faisait évaluer de travers la branche abonnement du webhook Mollie.
- **Nommage des clés de configuration PayPal Checkout (V5)** — Uniformisation de la clé de configuration `client-id` en `client_id` par cohérence ; une migration renomme la clé dans les passerelles existantes.
- **Type de retour de la vérification du webhook PayPal** — Correction d'un `TypeError` dans `verifyPayPalWebhook()`, qui renvoyait une réponse HTTP d'erreur depuis une méthode typée pour renvoyer une `string`. La méthode renvoie désormais `string|JsonResponse` et la réponse d'erreur est relayée par `notification()`.

---
