# Stay page review

Review: http://127.0.0.1:8080/stay

Run from the project root with PHP 8.x: `php -S 0.0.0.0:8080 router.php`. The server is already running for this review. Run `php tools/check-stay.php` in another terminal.

## Implementation

Compact editorial introduction; fourteen apartment photographs with visible descriptions; six supplied utility illustrations; six Rio photographs, main-pool text and community summary; seven supplied long-stay sketches; green enquiry CTA. Separate apartment and community lightbox galleries support buttons, arrow keys, Home/End, Escape, focus trapping/restoration, swipe gestures and native image links without JavaScript.

Reusable slim inner chrome serves inner routes. The original home chrome was extracted into dedicated components. Homepage source, CSS and JavaScript are unchanged; rendered Home markup matches the pre-change snapshot after intertag whitespace normalization. Property-specific Stay content is selected through property configuration.

## Exact files changed

- `README.md`
- `index.php`
- `config/properties.php`
- `includes/header.php`
- `includes/footer.php`

## Exact files created

- `config/stays/the-beginning.php`
- `pages/stay.php`
- `includes/header-home.php`
- `includes/header-inner.php`
- `includes/footer-home.php`
- `includes/footer-inner.php`
- `includes/navigation.php`
- `includes/stay-gallery.php`
- `assets/css/inner.css`
- `assets/css/stay.css`
- `assets/js/lightbox.js`
- `tools/check-stay.php`
- `docs/STAY-REVIEW.md`
- The thirteen illustration files listed below.

## Photography provenance

All twenty photographs use existing, unchanged files under `assets/images/`, supplied by `tribeinn-website-assets-v2.zip`. No mockup photographs were extracted. No stock/generated photographs were used. The mockup supplied composition guidance only.

| Section / title | Original path relative to assets/images |
|---|---|
| Living Room | `the-beginning/living-room/living-room-wide-01.webp` |
| TV & Entertainment | `the-beginning/living-room/tv-wall-wide-01.webp` |
| Bedroom | `the-beginning/bedroom/bedroom-wide-01.webp` |
| Bedroom Storage | `the-beginning/bedroom/bedroom-wardrobe-01.webp` |
| Kitchen | `the-beginning/kitchen/kitchen-wide-01.webp` |
| Kitchen Appliances | `the-beginning/kitchen/kitchen-appliances-01.webp` |
| Crockery | `the-beginning/kitchen/kitchen-crockery-01.webp` |
| Glassware & Kitchen Storage | `the-beginning/kitchen/kitchen-storage-glassware-01.webp` |
| Cutlery & Kitchen Tools | `the-beginning/kitchen/kitchen-cutlery-tools-01.webp` |
| Workspace | `the-beginning/bedroom/bedroom-work-desk-01.webp` |
| Sofa-cum-bed | `the-beginning/living-room/sofa-work-table-01.webp` |
| Laundry | `the-beginning/utility-laundry/utility-laundry-01.webp` |
| Bathroom | `the-beginning/bathroom/bathroom-01.webp` |
| The Little Things | `the-beginning/practical/entrance-door-01.webp` |
| A Colourful Community | `rio-de-goa/rio-colourful-buildings-courtyard-01.webp` |
| Gym | `rio-de-goa/rio-gym-01.webp` |
| Gardens | `rio-de-goa/rio-landscaped-garden-01.webp` |
| Central Courtyard | `rio-de-goa/rio-central-courtyard-01.webp` |
| Terrace | `rio-de-goa/rio-terrace-seating-01.webp` |
| The Zuari-side Outlook | `rio-de-goa/rio-terrace-water-view-01.webp` |

No verified main-pool, standalone cookware or dining photograph was available, so these facilities are described without invented photographic tiles. The terrace outlook is explicitly a shared-community view. The rooftop infinity pool and unconfirmed common lounge are absent. Soundbar wording is boAt throughout Stay.

## Supplied illustrations

Copied unchanged from `tribeinn-utility-icons-6-webp.zip` into `assets/images/illustrations/utilities/`:

- `01-cook-eat.webp`
- `02-sleep-relax.webp`
- `03-work-connect.webp`
- `04-practical-living.webp`
- `05-entertainment.webp`
- `06-comfort.webp`

Copied unchanged from `tribeinn-why-stay-longer-final-7-webp.zip` into `assets/images/illustrations/why-stay-longer/`:

- `01-build-something.webp`
- `02-create-something.webp`
- `03-learn-something.webp`
- `04-reset-restart.webp`
- `05-rejuvenate.webp`
- `06-live-in-goa.webp`
- `07-do-absolutely-nothing.webp`

Utility icons use CSS framing to remove excess transparent padding and an edge artifact. Original bytes remain unchanged. Source extraction and local baseline snapshots remain under ignored `source-assets/` and `var/` directories, respectively.

## Verification

- 369 combined automated checks passed: routes, anchors, image responses, dimensions, alt text, gallery captions, exact image-source hashes, all six utilities and seven sketches, wording exclusions and homepage preservation.
- PHP syntax and lightbox JavaScript syntax checks passed.
- Actual browser review at desktop 1440px, tablet 768px, mobile 390px and narrow 320px. No horizontal page overflow; narrow inner-header wrap found and fixed.
- Desktop and mobile lightbox: previous/next controls, wraparound, captions, keyboard arrows, Escape, focus cycle/restoration and pointer swipe verified. This is browser viewport testing, not physical-device certification.
- Mobile menu expansion, active Stay state and Escape dismissal verified.
- Homepage checked visually at desktop and mobile; original automated Home checks pass.
- No broken images or browser console warnings/errors observed.

## Boundaries and repository status

Check Dates still points to the existing, clearly marked enquiry placeholder. Other unfinished pages remain placeholders; no booking engine, iCal, Journal, payments or other content page was built.

Stay changes are uncommitted and unpushed pending review. The earlier approved-homepage commit `8a49015` exists locally, but its authorized push was rejected by GitHub with HTTP 403: the authenticated account `parmarhardik8one-pixyshare` lacks permission to `hardik1981/tribeinn`. Credentials were not changed.
