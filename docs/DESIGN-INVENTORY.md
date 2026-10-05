# Design inventory

> **Important:** the build environment's network policy blocked direct access to `www.caba.org.sg` (and to web archives), so live pages could not be measured pixel by pixel. Structure, navigation and copy come from the site's indexed public pages (see `CONTENT-SOURCES.md`). Colour, font and spacing values are a corporate baseline defined in **one place**, so they can be matched to the live site in minutes:
>
> * **Elementor → Site Settings → Global Colors / Global Fonts / Theme Style / Layout**. Elementor pages, the theme header/footer and the PHP templates all follow these.
> * Fallback values live in `:root` at the top of `assets/css/main.css`.
>
> To match the live site, sample its logo/nav/button colours and its heading/body fonts with browser dev tools. Then update the four system colours and the Headings/Body Text fonts in Site Settings. Nothing else needs to change.

## 1. Colour tokens

| Token | Elementor global | Default | Used for |
|---|---|---|---|
| Primary (Navy) | `primary` | `#0B2D5B` | Navigation text, primary buttons, CTA band, banner overlay, icons |
| Secondary (Blue) | `secondary` | `#1D5FA8` | Links, hover of primary buttons, focus rings |
| Body Text | `text` | `#3A4453` | Paragraphs |
| Accent (Red) | `accent` | `#C8102E` | Main CTA buttons, "Register Your Interest", underline rules, eyebrow labels, active nav state |
| Heading | `apbahead` | `#0B2D5B` | H1–H6 |
| Background | `apbabg` | `#FFFFFF` | Page |
| Surface | `apbasurf` | `#F3F5F9` | Alternate sections, related posts, form messages |
| Border | `apbabord` | `#DCE2EB` | Cards, header divider, inputs |
| Muted Text | `apbamute` | `#5F6B7A` | Metadata, captions |
| Dark | `apbadark` | `#071D3B` | Top bar and footer background |

Contrast (WCAG 2.1): body text on white 9.9:1, muted on white 5.4:1, accent on white 5.9:1, white on navy 13.6:1, white on accent 5.9:1, white on footer dark 16.8:1. All pass AA for normal text.

## 2. Typography

| Role | Font | Weight | Desktop / Tablet / Mobile | Line height |
|---|---|---|---|---|
| Hero headline (H1, uppercase) | Montserrat | 800 | 52 / 40 / 30 px | 1.15 |
| H1 (page banner) | Montserrat | 700 | 44 / 36 / 30 px | 1.2 |
| H2 (section titles) | Montserrat | 700 | 34 / 30 / 26 px | 1.25 |
| H3 (card/feature titles) | Montserrat | 600 | 22 / 22 / 20 px | 1.35 |
| Card titles (post grid) | Montserrat | 700 | 18 px | 1.4 |
| Navigation | Montserrat | 600, uppercase, +0.02em | 14 px (13 px at 992–1199) | — |
| Body | Open Sans | 400 | 16 px (articles 17 px) | 1.7 (articles 1.8) |
| Metadata / breadcrumbs | Open Sans | 400 | 13 px | — |
| Buttons | Montserrat | 700, uppercase, +0.5px | 14 px | — |
| Eyebrow labels | Montserrat | 700, uppercase, +0.12em | 13 px | — |
| Form labels | Montserrat | 600 | 14 px | — |

## 3. Layout and spacing

* Content container: **1200 px**, with side gutters of 24 px (desktop), 20 px (tablet) and 16 px (mobile). Article text column: 860 px.
* Spacing scale: 4 · 8 · 16 · 24 · 32 · 48 · 64 · 80–88 px.
* Section padding: 80 px desktop, 64 px tablet, 48 px mobile. Inner-page banner: 56/52 px (min-height 260 px). Hero: 96 px (min-height 640 / 540 / 480 px).
* Grid gaps: 30 px desktop, 24 px tablet.
* Header: 38 px top bar + 88 px main bar (72 px tablet, 64 px mobile). Sticky, with a shadow after scrolling.
* Logo: max height 64 px (48 px on mobile). The text fallback uses a 56 px square monogram.
* Buttons: min-height 46 px, padding 12×26, radius 4 px, 2 px border on outline variants.
* Cards: 1 px border, radius 4 px, 16:9 image, body padding 22/24 px. On hover they lift 2 px and get a soft shadow.
* Borders: 1 px `#DCE2EB`. Accent rule: 56×3 px red under section headings.

## 4. Breakpoints (Elementor Site Settings and theme CSS)

| Device | Range | Behaviour |
|---|---|---|
| Desktop | ≥ 1200 px | Full nav with hover/focus dropdowns, 3-column grids, 4-column footer |
| Laptop | 992–1199 px | Tighter nav spacing, logo tagline hidden |
| Tablet | 768–991 px | Hamburger off-canvas menu, 2-column grids, columns stack, 2-column footer |
| Mobile | < 768 px | 1-column grids and forms, top bar shows contact only, 1-column footer |

## 5. Components

| Component | Implementation |
|---|---|
| Header / navigation / dropdown / mobile menu | `header.php`, `APBA_Nav_Walker` (adds `aria-expanded` sub-menu toggle buttons), `navigation.js` (Esc to close, click outside, breakpoint reset) |
| Hero banner | Elementor container `.apba-hero`: background image + navy gradient overlay |
| Inner page banner + breadcrumbs | Elementor container `.apba-page-banner` + `[apba_breadcrumbs]`; PHP templates use `apba_page_banner()` |
| Section heading | Eyebrow (`.apba-eyebrow`) + H2 (`.apba-title-rule`) + intro |
| Pillar / benefit / leadership cards | Elementor containers `.apba-box` holding Icon Box / Icon List / Image widgets |
| Event feature | Two-column image + heading + icon list + button |
| News / article cards + pagination | `[apba_posts]` → `apba_post_card()`, `apba_pagination()` |
| Article metadata | `apba_post_meta()` (date, category, author) |
| CTA band | Navy full-width container, heading + text + red button |
| Forms + privacy consent | Contact Form 7 markup using `.apba-form-grid`/`.apba-field`, styled in `main.css` (focus ring, invalid state, success/error/spam message colours) |
| Footer | `footer.php`: brand + about, Quick Links menu, contact list, membership CTA, legal menu, dynamic © year |

## 6. Interaction

* Hover: link colour changes to the accent; the nav underline grows from the left; cards lift 2 px; the card image zooms 3%.
* Focus: a 3 px accent outline on every interactive element (`:focus-visible`); inputs get a blue focus ring.
* Motion is disabled under `prefers-reduced-motion`. There are no scroll animations.
