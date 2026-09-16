<?php
declare(strict_types=1);

// One shared codebase. A future host-to-property map can select another entry here.
return [
    'the-beginning' => [
        'name' => 'The Beginning',
        'location' => 'Tata Housing Rio De Goa · Goa',
        'description' => 'A cosy home in Goa for travellers, remote workers, creators and anyone looking for a slower, sunnier pace of life.',
        'images' => [
            'hero' => ['hero/home-hero-desktop.webp', 'The Beginning living room, with a patterned coffee table, sofa and colourful wall decorations'],
            'hero_mobile' => ['hero/home-hero-mobile.webp', 'The Beginning living room'],
            'living' => ['the-beginning/living-room/living-room-wide-01.webp', 'The living room with its sofa, patterned table and personal decorative touches'],
            'bedroom' => ['the-beginning/bedroom/bedroom-wide-01.webp', 'Bedroom with a double bed, bedside lamp and sea-inspired artwork'],
            'kitchen' => ['the-beginning/kitchen/kitchen-wide-01.webp', 'Apartment kitchen with storage, sink and countertop appliances'],
            'workspace' => ['the-beginning/bedroom/bedroom-work-desk-01.webp', 'Compact work table and patterned chair beside the bedroom window'],
            'laundry' => ['the-beginning/utility-laundry/utility-laundry-wide-02.webp', 'Practical utility balcony with washing machine and laundry space'],
            'community' => ['rio-de-goa/rio-colourful-buildings-courtyard-01.webp', 'Colourful Rio De Goa buildings surrounding the landscaped courtyard'],
            'gardens' => ['rio-de-goa/rio-landscaped-garden-01.webp', 'A path among palms and tropical planting at Rio De Goa'],
            'gym' => ['rio-de-goa/rio-gym-01.webp', 'Treadmills and exercise equipment in the Rio De Goa gym'],
            'terrace' => ['rio-de-goa/rio-terrace-water-view-01.webp', 'The shared Rio De Goa terrace viewing area overlooking the Zuari-side water'],
            'goa' => ['goa-around-us/goa-beach-01.webp', 'Waves and an open sandy beach in Goa, away from the property'],
        ],
        // Contact/social details intentionally unset until supplied by the owner.
        'contact' => null,
    ],
];
