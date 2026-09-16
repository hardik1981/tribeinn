# Homepage implementation and review notes

## Visual direction

Refined Original: warm cream, forest green, sand, small terracotta and yellow accents; large real photographs; clean sans-serif UI; selective italic editorial type; restrained line drawings. The hero pairs copy with a real living-room photograph instead of covering the room with marketing text. No image generation or stock imagery was used.

All requested home sections are included. Rio De Goa links to Stay. Goa destination content links to Experience. The other pages remain simple placeholders.

## Exact V2 homepage image map

All paths below are relative to `assets/images/`. Each production WebP is byte-for-byte identical to its V2 source. The rest of the supplied web-ready library is retained for later pages but is not displayed.

| Image | Homepage use |
| --- | --- |
| `hero/home-hero-desktop.webp` | Desktop/tablet living-room hero |
| `hero/home-hero-mobile.webp` | Mobile hero at 600px and below |
| `the-beginning/living-room/living-room-wide-01.webp` | The Home story card and Living Room preview |
| `the-beginning/bedroom/bedroom-wide-01.webp` | Bedroom preview |
| `the-beginning/kitchen/kitchen-wide-01.webp` | Kitchen preview |
| `the-beginning/bedroom/bedroom-work-desk-01.webp` | Work Space preview |
| `the-beginning/utility-laundry/utility-laundry-wide-02.webp` | Practical living/utility balcony note |
| `rio-de-goa/rio-colourful-buildings-courtyard-01.webp` | The Community story card |
| `rio-de-goa/rio-landscaped-garden-01.webp` | Life at Rio De Goa lead image |
| `rio-de-goa/rio-gym-01.webp` | Rio gym preview |
| `rio-de-goa/rio-terrace-water-view-01.webp` | Shared terrace/viewing-area preview |
| `goa-around-us/goa-beach-01.webp` | The Goa Around Us story card and destination section |

The SVG favicon and outline icons are simple original vector UI marks, not property photography.

## Content decisions / remaining inputs

- No dedicated main-pool image is in V2. The owner-confirmed main pool is mentioned in the Rio copy and amenities list without a substitute photograph.
- The lounge photo is not displayed and no lounge access is claimed. No rooftop infinity pool is advertised or displayed.
- The terrace image is explicitly labelled as a shared Rio viewing area. Beach imagery is labelled as Goa destination context, not the apartment view.
- The utility balcony is presented as practical laundry space, not as a leisure balcony or scenic view.
- The image guide's request to defer Goa content was treated as source-document guidance. The user's explicit homepage brief takes precedence: a concise destination preview is included, without fabricated recommendations or distances.
- Contact data and social handles are unset. Contact links to an honest placeholder; no inactive fake social links.
- Enquiry form and all remaining full pages are deferred for homepage approval.

## Verification

Locally run on PHP 8.3.30 with the built-in server.

- PHP syntax checks passed for every application PHP file. JavaScript syntax check passed.
- `php tools/check.php`: 104 checks passed, covering HTTP pages, navigation and anchor targets, image URLs, alt text, dimensions, 404 responses, private-file non-disclosure through the local router and V2 source hashes.
- Browser inspected at 1440×1000 desktop, 768×1024 tablet, 390×844 mobile, and 320×740 narrow mobile.
- Desktop and mobile sections visually inspected for image crops, stacking, spacing and booking/footer layout.
- No horizontal overflow at the inspected widths. No broken loaded images or browser warnings/errors on the home page.
- Mobile picture selects `home-hero-mobile.webp`; menu opens and closes, Escape returns focus to Menu, and Check Dates navigation reaches the unfinished enquiry page.
- Mobile hero source dimensions corrected to the actual 1200×1500. The postcard annotation uses an opaque cream background for predictable contrast over the photograph.
- Responsive checks are browser viewport checks on this workstation, not a claim of exhaustive testing on physical iOS/Android devices.
- Hostinger deployment/rewrite configuration is documented but not tested against a live host.

## Approval and version control

The owner approved the current homepage and shared foundation as the V1 design baseline. Preserve the homepage design, responsive behaviour, typography, palette, spacing, components and real V2 photography unless the owner explicitly requests changes. All remaining implementations await separate instructions.

Origin is `https://github.com/hardik1981/tribeinn.git`; the approved foundation is intended for `main`. No deployment has been performed.
