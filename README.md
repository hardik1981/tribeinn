# TribeInn — approved V1 homepage foundation

PHP 8.x, semantic HTML, CSS and vanilla JavaScript. No packages, build process or database.

## Run locally

From this directory:

```sh
php -S 127.0.0.1:8080 router.php
```

Open http://127.0.0.1:8080/. Stop with Ctrl+C in the server terminal.
On this workstation PHP is installed at `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`.

With the server running, run `php tools/check.php` for the HTTP/link/image smoke checks.
The optional first argument selects another local origin, e.g. `php tools/check.php http://127.0.0.1:8090`.

## Scope

Only Home is fully implemented. `/stay`, `/experience`, `/guest-guide`, `/about`, `/check-dates` and `/faq-policies` use one minimal placeholder template. Stay anchors `#rio-de-goa` and `#why-stay-longer`, policy anchors `#faq` and `#policies`, and the contact anchor `#contact` resolve. Unknown routes return HTTP 404.

Check Dates is not a live availability checker or a working enquiry form. No contacts, social accounts, policies, distances, prices or Wi-Fi specifications were invented. No booking database, iCal, payment integration or Journal was added.

## Structure

- `index.php`: allowlisted page routing and layout composition.
- `includes/config.php`: shared route map, escaping, URL and photo helpers, consistent outline icons.
- `includes/header.php`, `includes/footer.php`: shared accessible site navigation and layout.
- `config/properties.php`: current property identity, image map, alt text and contact placeholder. A future host map can select another property entry without duplicating templates or branches.
- `pages/home.php`: completed homepage.
- `pages/placeholder.php`: shared unfinished-page and 404 presentation.
- `assets/css/site.css`: tokens, components and responsive styles; reduced-motion support.
- `assets/js/site.js`: progressively enhanced mobile menu with Escape and focus handling.
- `assets/images/`: unchanged supplied V2 WebPs plus a small code-native SVG favicon.
- `router.php`: development server routing; only public assets are served directly.
- `.htaccess`: Apache/LiteSpeed routing and private-directory restrictions.
- `docs/`: original V2 image documentation and implementation/verification records.
- `tools/check.php`: local HTTP and provenance checks.
- `source-assets/`: extracted archive and masters, ignored by Git; not a deployment directory.

## Hostinger deployment (not performed)

Use PHP 8.x. Upload `index.php`, `.htaccess`, `includes/`, `config/`, `pages/` and `assets/` into the site's document root, normally `public_html`. Include the hidden `.htaccess` file. Enable URL rewriting on Apache/LiteSpeed. Do not upload `.git`, `source-assets`, `docs`, `tools`, `router.php`, or local logs.

Root hosting requires no extra configuration. For a subdirectory, set the server environment variable `TRIBE_BASE_PATH` to that directory (e.g. `/tribeinn`); the shared URL helper prefixes internal links and asset URLs. Future property-host selection belongs in configuration.

The current preview still has unfinished destinations and is for visual review. The Hostinger deployment itself has not been tested or performed.

## Photography and verification

See `docs/HOMEPAGE-REVIEW.md` for the exact homepage photo map and verification results, and `docs/FILE-INVENTORY.txt` for the created file inventory. Images were not generated or altered. Responsive presentation uses CSS cropping and the supplied separate mobile hero. Below-the-fold images are lazy loaded with intrinsic dimensions; the hero is high priority. Fonts use local system sans-serif and Georgia editorial accents without external font requests.

The homepage, responsive behaviour, design system, typography, palette, spacing, components and real V2 photography are the owner-approved V1 baseline. Preserve this baseline; homepage design changes require an explicit owner request. Remaining page implementations await separate instructions. No deployment has been performed.
