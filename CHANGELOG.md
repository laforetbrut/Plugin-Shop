# Changelog

All notable changes to Shop are documented here.

---

## [1.2.22] - 2026-05-21

### Fixed

- **Package file upload validation (M7)** — Added strict `mimes` and `max` rules to the package `file` field. Only `zip`, `rar`, `7z`, `jar`, `txt` and `pdf` archives/documents up to 50 MB can be uploaded; arbitrary files (including `.php`) are now rejected.
- **SVG rejected for images (L1)** — Removed `allow_svg` from the package and offer `image` validation rules to prevent stored XSS through scriptable SVG files served from the webroot.

### Correctifs

- **Validation de l'upload de fichier de package (M7)** — Ajout de règles strictes `mimes` et `max` sur le champ `file` d'un package. Seuls les archives/documents `zip`, `rar`, `7z`, `jar`, `txt` et `pdf` jusqu'à 50 Mo peuvent être déposés ; les fichiers arbitraires (dont `.php`) sont désormais refusés.
- **SVG refusé comme image (L1)** — Suppression de `allow_svg` des règles de validation `image` des packages et offres pour empêcher le XSS stocké via des fichiers SVG scriptables servis depuis le webroot.

---
