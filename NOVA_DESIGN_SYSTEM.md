# NOVA — Ville & Eau · Design System & UI Generation Spec

> **Purpose of this file.** This is the single source of truth for the look, feel, layout and behaviour of every NOVA page. When you (the AI) generate a new page, screen, component or feature for NOVA, you MUST follow this document exactly. Do not invent new colors, fonts, radii, shadows, spacing, icon styles or layouts. If something is not covered here, derive it from the closest existing pattern. Consistency beats creativity.

---

## 0. Product context

- **Product:** NOVA — "Ville & Eau", a smart city water-leak reporting and management platform (Tunis, Tunisie).
- **Tagline:** *"Une ville attentive à chaque goutte d'eau."* / *"Chaque goutte compte."*
- **Language:** French (UI copy, dates, labels). Keep new pages in French unless told otherwise.
- **Three role-based spaces share ONE design language:**

| Role | Space label (sidebar) | Purpose |
|---|---|---|
| Citizen (Citoyen) | `ESPACE CITOYEN` | Report leaks, follow reports, view water consumption, earn rewards |
| Technician / Plumber (Plombier) | `MON ESPACE` | See assigned interventions, planning, history, update statuses |
| Back office / Admin | `PILOTAGE` | Dashboard, manage reports, assign plumbers, citizens, consumption, rewards, settings |

- **Tone of voice:** warm, reassuring, civic, concise. Friendly greeting with a 👋 emoji on home pages ("Bonjour, Salma 👋"). Short sentences. Encouraging microcopy ("Ensemble, prenons soin de l'eau de notre ville.", "Agissons ensemble."). Admin pages are more neutral/factual but keep the same visual language.
- **Mood:** clean, calm, "water" — deep navy + fresh teal on a very pale blue-white canvas, generous white space, soft rounded cards, almost no heavy shadows.

---

## 1. Design tokens (use these exact values)

Always define and consume these as CSS custom properties. Never hard-code raw hex values in components.

```css
:root {
  /* ---------- Brand: Navy (sidebar, headings, hero) ---------- */
  --navy-950: #042650;   /* darkest: hero gradient start, deep accents */
  --navy-900: #063B6E;   /* SIDEBAR background */
  --navy-800: #0A4678;   /* sidebar promo card / hover surface */
  --navy-700: #0E5A8F;   /* hero gradient end, lighter navy */
  --ink-900:  #0A3560;   /* all headings, titles, numbers (text on white) */
  --ink-700:  #3F5C78;   /* body text on white */
  --ink-500:  #6B8399;   /* secondary / muted text, subtitles, meta */
  --ink-400:  #8FA3B5;   /* placeholders, table headers, disabled */

  /* ---------- Brand: Teal (primary accent) ---------- */
  --teal-500: #14B8A6;   /* PRIMARY: buttons, active bars, donut ring, dots */
  --teal-600: #0FA192;   /* primary hover */
  --teal-700: #0B8579;   /* teal text on white (eyebrow, links) */
  --teal-300: #6EDDD3;   /* secondary chart series, soft highlights */
  --teal-100: #DDF5F1;   /* icon tiles, avatar bg, active tab bg */
  --teal-50:  #EEFAF8;   /* very light teal wash */
  --on-teal:  #05294D;   /* text color on teal buttons (dark navy, bold) */

  /* ---------- Surfaces ---------- */
  --bg-page:   #F4F9FC;  /* main app canvas (very pale blue-white) */
  --surface:   #FFFFFF;  /* cards, topbar, tables */
  --border:    #E3EDF4;  /* 1px card & divider border */
  --border-soft: #EDF3F8;/* row dividers inside cards */
  --track:     #D9EEF0;  /* donut / progress track */

  /* ---------- Sidebar ---------- */
  --sidebar-bg: var(--navy-900);
  --sidebar-text: #FFFFFF;
  --sidebar-muted: #7FA6C9;               /* section labels like PILOTAGE */
  --sidebar-active-bg: rgba(20,184,166,.22);
  --sidebar-active-border: rgba(20,184,166,.55);
  --sidebar-hover-bg: rgba(255,255,255,.06);
  --sidebar-card-bg: #0A4678;
  --sidebar-card-border: rgba(20,184,166,.35);

  /* ---------- Status colors (pill background / text) ---------- */
  --st-pending-bg:  #FEF1D3;  --st-pending-fg:  #B26A00;  /* À traiter */
  --st-todo-bg:     #FDE6D3;  --st-todo-fg:     #C25A12;  /* À démarrer */
  --st-progress-bg: #DCEEFB;  --st-progress-fg: #1C6CA8;  /* En cours */
  --st-done-bg:     #D6F5E8;  --st-done-fg:     #0B8A63;  /* Résolu / Résolue / Terminé(e) */
  --st-neutral-bg:  #EAF0F5;  --st-neutral-fg:  #5B7389;  /* Non attribué / archived */

  /* ---------- Feedback / deltas ---------- */
  --positive: #0B8A63;   /* ↑/↓ good trend text (e.g. "↓ 4 % vs mois dernier") */
  --warning:  #B26A00;   /* attention text (e.g. "8 en attente depuis plus de 24 h") */
  --danger:   #D64545;   /* errors, destructive */

  /* ---------- Gradients ---------- */
  --hero-gradient: linear-gradient(110deg, #042650 0%, #07407A 50%, #0E6A94 100%);
  --drop-gradient: linear-gradient(160deg, #62E3E8 0%, #4AA8F0 55%, #8B7CF6 100%); /* water-drop illustration */

  /* ---------- Radius ---------- */
  --r-sm: 8px;     /* small chips, inputs inside tables */
  --r-md: 10px;    /* buttons, nav items, icon tiles, inputs */
  --r-lg: 16px;    /* cards */
  --r-xl: 20px;    /* hero banner */
  --r-pill: 999px; /* status pills, tabs, location selector */

  /* ---------- Shadow (always soft, barely visible) ---------- */
  --shadow-card: 0 1px 2px rgba(8,50,90,.04), 0 4px 14px rgba(8,50,90,.04);
  --shadow-pop:  0 8px 24px rgba(8,50,90,.12);

  /* ---------- Spacing scale (4px base) ---------- */
  --s-1: 4px;  --s-2: 8px;  --s-3: 12px; --s-4: 16px;
  --s-5: 20px; --s-6: 24px; --s-8: 32px; --s-10: 40px;

  /* ---------- Layout ---------- */
  --sidebar-w: 232px;
  --topbar-h: 64px;
  --content-pad: 32px;
  --grid-gap: 20px;
}
```

### Color usage rules
1. **Navy** = structure & authority (sidebar, hero, headings, numbers). **Teal** = action & life (buttons, active states, data, positive accents).
2. Page background is always `--bg-page`; every content block sits on a white `--surface` card. Never put content directly on the page background except the page header (eyebrow, H1, subtitle).
3. Text hierarchy on white: headings/numbers `--ink-900` → body `--ink-700` → meta `--ink-500` → hints `--ink-400`.
4. Teal buttons always use **dark navy bold text** (`--on-teal`), never white text.
5. Do not introduce new hues (no purple UI chrome, no red buttons, no orange primary). The only multi-color element is the decorative water-drop illustration (cyan → blue → violet).
6. Status colors are fixed (see §6.6). Use the same status → same color on every page.

---

## 2. Typography

- **Font family:** `"Plus Jakarta Sans", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif`
- Load from Google Fonts: weights **400, 500, 600, 700, 800**.
- Rendering: `-webkit-font-smoothing: antialiased;`
- Numbers in KPIs/donuts use `font-variant-numeric: tabular-nums;`.

| Role | Size / Line-height | Weight | Color | Notes |
|---|---|---|---|---|
| Eyebrow (date / section kicker) | 11px / 16px | 700 | `--teal-700` | UPPERCASE, `letter-spacing: .12em` — e.g. `MARDI 10 NOVEMBRE 2026`, `VUE D'ENSEMBLE` |
| Page title H1 | 28px / 36px | 800 | `--ink-900` | e.g. "Bonjour, Salma 👋", "Tableau de bord" |
| Page subtitle | 14px / 22px | 400 | `--ink-500` | One sentence under H1 |
| Hero title | 26px / 34px | 800 | `#FFFFFF` | Max 2 lines |
| Hero body | 13px / 20px | 400 | `rgba(255,255,255,.85)` | |
| Card title | 16px / 22px | 700 | `--ink-900` | |
| Card subtitle | 12px / 18px | 400 | `--ink-500` | |
| List item title | 13.5px / 20px | 700 | `--ink-900` | |
| List item meta | 11.5px / 16px | 400 | `--ink-500` | Separated with ` · ` |
| KPI number (big) | 30px / 36px | 800 | `--ink-900` | Dashboard KPI cards |
| KPI number (medium) | 24px / 30px | 800 | `--ink-900` | Technician stat cards |
| Donut center number | 32px / 36px | 800 | `--ink-900` | with unit below at 11px `--ink-500` |
| Table header | 10.5px / 14px | 600 | `--ink-400` | UPPERCASE, `letter-spacing: .08em` |
| Table cell | 12.5px / 18px | 500 | `--ink-700` | Reference / name cell: 700 `--ink-900` |
| Button | 13.5px / 20px | 700 | see buttons | |
| Nav item | 14px / 20px | 500 (active 600) | `#fff` | |
| Sidebar section label | 10.5px / 14px | 700 | `--sidebar-muted` | UPPERCASE, `letter-spacing: .14em` |
| Status pill | 11px / 14px | 700 | per status | |
| Link ("Tout voir →") | 12.5px / 18px | 700 | `--ink-900` or `--teal-700` | Always ends with ` →` |
| Footer micro text | 11px / 16px | 400 | `--ink-400` | centered |

Rules: never use more than 2 font weights in one text block; titles are sentence case (not Title Case); only eyebrows, table headers and sidebar section labels are UPPERCASE.

---

## 3. Global layout (app shell)

Every page uses the same shell:

```
┌───────────────┬───────────────────────────────────────────────┐
│               │  [Topbar – citizen only]                      │
│   SIDEBAR     ├───────────────────────────────────────────────┤
│   (navy,      │  Page header (eyebrow + H1 + subtitle | actions)
│   fixed,      │  Hero banner (home pages) / KPI row           │
│   232px)      │  Card grid (2 columns or 4 KPI columns)       │
│               │  Bottom info cards / footer text              │
└───────────────┴───────────────────────────────────────────────┘
```

- `body { background: var(--bg-page); }`
- Sidebar: `position: fixed; inset: 0 auto 0 0; width: var(--sidebar-w); background: var(--sidebar-bg); padding: 24px 16px; display:flex; flex-direction:column;`
- Main: `margin-left: var(--sidebar-w); padding: 28px var(--content-pad) 40px; min-height: 100vh;`
- Main content max-width ≈ 1180px for readability on very wide screens (center within the remaining area).
- Grid gap everywhere: **20px**. Card inner padding: **20px–24px**.
- Responsive: ≤1024px sidebar collapses to icon-only (72px) or off-canvas drawer; ≤768px all grids become 1 column and KPI rows become 2 columns.

### 3.1 Sidebar (identical structure on all roles)

Top → bottom:
1. **Logo block** — NOVA logo mark (a stylized city skyline of 4–5 vertical bars of varying height, in teal/white with a small water-drop/wave) + wordmark `NOVA` (white, 800, ~22px, letter-spacing .08em) and below it `VILLE & EAU` (10px, `--sidebar-muted`, letter-spacing .2em, UPPERCASE). Height ~48px, margin-bottom 28px.
2. **Section label** — `ESPACE CITOYEN` / `MON ESPACE` / `PILOTAGE` (see typography). margin-bottom 8px.
3. **Nav list** — vertical, gap 4px. Each item: height 42px, padding 0 14px, radius `--r-md`, icon 18px + 12px gap + label. Icons are line icons, 1.75px stroke, color white at 85% opacity.
   - **Default:** transparent bg, white text.
   - **Hover:** `--sidebar-hover-bg`.
   - **Active:** `background: var(--sidebar-active-bg); border: 1px solid var(--sidebar-active-border);` text white 600, icon teal-300. (Teal-tinted translucent "glass" pill.)
4. *(Back office only)* a second section label `SYSTÈME` with its own items (e.g. Paramètres), margin-top 24px.
5. **Bottom block** (pushed down with `margin-top:auto`), depends on role:
   - **Citizen:** promo card — `--sidebar-card-bg`, 1px `--sidebar-card-border`, radius 14px, padding 16px. Title "Chaque goutte compte" (white, 700, 13.5px), text "Un signalement peut aider tout votre quartier." (`#B9D3EA`, 12px), full-width teal button "Signaler une fuite →".
   - **Technician:** availability card, same card style: teal dot + "Disponible" (white 700), text "Votre statut permet à l'administration de vous attribuer de nouvelles interventions." (`#B9D3EA`, 12px).
   - **Back office:** plain footer text, no card: "NOVA · Gestion intelligente de l'eau" / "Version de démonstration" (11px, `--sidebar-muted`).

**Navigation sets (keep labels exactly):**
- Citizen: Accueil · Signaler une fuite · Mes signalements · Consommation · Mes récompenses
- Technician: Accueil · Mes interventions · Mon planning · Historique · Mon profil
- Back office: Tableau de bord · Signalements · Interventions · Citoyens · Plombiers · Consommation · Récompenses · *(SYSTÈME)* Paramètres

### 3.2 Topbar (citizen space)

- Height 64px, white, `border-bottom:1px solid var(--border)`, padding 0 32px, flex space-between.
- Left: breadcrumb `Espace citoyen / **Accueil**` — 13px, parent in `--ink-500`, current page in `--ink-900` 700, separator `/` in `--ink-400`.
- Right: notification icon button (36px, white, 1px border, radius 10, bell icon) + user chip: avatar (36px circle, bg `--teal-100`, initials in `--teal-700` 700) + name (13px 700 `--ink-900`) over role (11px `--ink-500`, e.g. "Compte citoyen").
- Technician and back-office pages have **no topbar**; their page header sits at the top of the content area and the location selector sits at the top-right.

### 3.3 Page header

```
EYEBROW (teal, uppercase)                       [location pill | action buttons]
H1 title (navy, 800)
Subtitle (muted)
```
- Margin-bottom 20–24px.
- **Location selector** (home pages): white pill, 1px border, radius `--r-pill`, padding 8px 14px, pink/red map-pin icon + "Tunis, Tunisie" (12.5px, `--ink-700`) + chevron-down. Top-right of the page header.
- **Admin actions** (right of header on back office): secondary button `Exporter` (white, 1px border, with upload/download icon) + primary button `+ Nouveau signalement`.

---

## 4. Signature components

### 4.1 Hero banner (home pages only)

- Full content width, min-height 150–170px, radius `--r-xl`, padding 32px 36px, background `--hero-gradient`, `overflow:hidden; position:relative`.
- **Decoration (right side):** 3–4 concentric circles/ripples (`border:1px solid rgba(255,255,255,.06)`, or filled `rgba(255,255,255,.03)`) centered around the right third, evoking water ripples, plus a large illustrative icon:
  - Citizen: a glossy **water drop** filled with `--drop-gradient` (~90px) and a small white **four-point sparkle ✦** at its top-right.
  - Technician: a soft lavender/pink-silver **wrench** icon (~80px, rotated ~-45°), slightly translucent.
  - (For other roles/pages, use a relevant single glyph in the same soft, glossy, semi-transparent style.)
- **Content (left):** Title (2 lines max, white 800 26px) → body (13px white 85%) → teal primary button.
- Copy pattern: bold question/statement + a second line completing it ("Vous avez repéré une fuite ? / Agissons ensemble.").

### 4.2 Cards

```css
.card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--r-lg);
  box-shadow: var(--shadow-card);
  padding: 22px 24px;
}
```
- **Card header:** left = title + subtitle (stacked); right = text link (`Tout voir →`, `Détails →`, `Gérer l'équipe →`, `Voir tout →`) or a small dropdown (`Cette semaine ⌄`, `7 derniers jours ⌄`, teal text). Margin-bottom 16px.
- Two-column card rows: `grid-template-columns: 1fr 1fr; gap: 20px;` (back office main row uses ≈ 1.45fr / 1fr).

### 4.3 Icon tile
- 40×40, radius `--r-md`, background `--teal-100`, icon 18px in `--teal-700`. Used before list items, stats, info cards. For quick-action tiles in sidebars/KPI corners use the same shape; KPI corner tiles are 32px with teal icon.

### 4.4 Buttons

| Variant | Style |
|---|---|
| **Primary** | bg `--teal-500`, text `--on-teal` 700, radius `--r-md`, height 40px, padding 0 18px, optional leading `+` icon or trailing `→`; hover `--teal-600`; no border; subtle shadow `0 4px 12px rgba(20,184,166,.28)` |
| **Secondary / outline** | bg `#fff`, 1px `--border`, text `--ink-900` 600, radius `--r-md`, height 40px; hover bg `--bg-page` |
| **Small outline (row action)** | height 30px, padding 0 12px, 12px 700 text in `--teal-700`, bg `--teal-50`, 1px `rgba(20,184,166,.35)` border, radius 8px — e.g. "Mettre à jour le statut", "Voir les détails", "Consulter" |
| **Icon button** | 36×36, white, 1px border, radius 10 |
| **Full-width** (sidebar promo) | primary, `width:100%`, height 40px |

### 4.5 Avatar
- 36px circle, bg `--teal-100`, initials 12px 700 in `--ink-900` (lists) or `--teal-700` (topbar). Never use photos unless told.

### 4.6 Status pill
```css
.pill { display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font:700 11px/14px var(--font); }
```
| Label | Where | bg / fg |
|---|---|---|
| À traiter | back office, new reports | `--st-pending-*` (amber) |
| À démarrer | technician, scheduled | `--st-todo-*` (orange) |
| En cours | all roles | `--st-progress-*` (blue) |
| Résolu / Résolue / Terminé / Terminée | all roles | `--st-done-*` (mint green) |
| Non attribué, archivé | neutral | `--st-neutral-*` (grey) |

> Note: in the first citizen mock "En cours" appeared amber; the canonical rule going forward is **En cours = blue** on every page so the same status always has the same color.

Availability indicators (plumber list): small dot (8px) + text — `● Disponible` in `--positive`, `● En intervention` in `--warning`, both 11px 600.

### 4.7 Tabs / filter chips
- Row of pills, gap 8px. Inactive: white, 1px `--border`, text `--ink-500` 600 12px, radius pill, padding 6px 14px. **Active:** bg `--teal-100`, 1px `rgba(20,184,166,.5)`, text `--teal-700`. Counts in parentheses: `Tous (128)`, `À traiter (24)`, `En cours (17)`, `Résolus (86)`.

### 4.8 List row (signalements / interventions)
```
[icon tile 40]  Title (bold 13.5)                              [status pill / button]
                meta · meta · meta (11.5 muted)
```
- Padding 14px 0, `border-bottom:1px solid var(--border-soft)` (none on last).
- Meta items may be prefixed with a tiny 12px calendar/clock icon in `--ink-400`.
- Technician list rows can have two lines of meta and a right-aligned status pill above a small outline action button.

### 4.9 Stat card (technician) — 3 across
Card with icon tile left (40px) + small muted label above a big number (24px 800): "Interventions aujourd'hui **3**", "À effectuer **2**", "Terminées ce mois **18**".

### 4.10 KPI card (back office) — 4 across
- Card, padding 20px. Top: muted label (12.5px `--ink-500`) with a 32px icon tile at top-right. Middle: number 30px 800. Bottom: delta line 11.5px — green for good trend (`↑ 12 % ce mois-ci`), amber for warnings (`8 en attente depuis plus de 24 h`), muted for neutral (`11 plombiers disponibles`).
- Standard set: Signalements reçus · À traiter · Interventions en cours · Fuites résolues.

### 4.11 Donut / ring progress (consumption)
- Size ≈ 150px, stroke 14px, track `--track`, progress `--teal-500`, rounded caps, starts at top, clockwise.
- Center: value 32px 800 `--ink-900` + unit 11px `--ink-500` ("m³ ce mois").
- Right of ring: heading "Ce mois-ci" (15px 700), description 12.5px `--ink-500`, delta `↓ 4 %  vs mois dernier` in `--positive` 700.

### 4.12 Data table
- Inside a card, no outer border. Header row: UPPERCASE 10.5px `--ink-400`, padding 10px 0, border-bottom 1px `--border`. Body rows: height ~54px, border-bottom 1px `--border-soft`.
- Standard columns for reports: `RÉFÉRENCE` (e.g. `#NV-0248`, bold navy) · `EMPLACEMENT` (street bold + district muted below) · `DATE` (`10 nov. · 09:42`) · `STATUT` (pill) · `ASSIGNÉ À` (name or `Non attribué` muted) · overflow `⋯` button.
- Reference format: `#NV-0000` (4 digits, zero-padded). Dates: `10 nov. · 09:42` in tables, `Signalée le 8 nov.` in lists, `Aujourd'hui, 10:30` / `Hier, 16:15` for relative.

### 4.13 Bar chart (activity)
- Inside a card; light horizontal grid lines `--border-soft`; bars with top radius 4px, width ≈ 18–22px.
- Series 1 (primary) `--teal-500` ("Signalements reçus"); Series 2 (secondary) `--teal-300` ("Interventions débutantes"). Legend below: 8px colored dots + 11.5px `--ink-500`.
- X axis labels: `Lun. Mar. Mer. Jeu. Ven. Sam. Dim.` in 11px `--ink-500`. No y-axis numbers unless needed.
- Range dropdown at card top-right: `7 derniers jours ⌄` (teal, 12px 700).

### 4.14 Person list (plombiers disponibles)
Row: avatar (36) + name (13px 700) over `N interventions · Zone` (11.5 muted) + right-aligned availability dot/text. Divider between rows.

### 4.15 Quick actions list
Rows with left label (13px `--ink-700`) and right bold count/CTA in `--ink-900` followed by `→`: `Attribuer les demandes en attente  24 →`, `Consulter les comptes citoyens  1 284 →`, `Gérer les récompenses  6 offres →`, `Voir le suivi de consommation  Ouvrir →`.

### 4.16 Planning / timeline
Rows: time (`10:30`, 13px 700 navy, fixed 48px width) · colored dot (teal = current, orange = upcoming urgent, grey = available slot) · title (13px 700) over subtitle (11.5 muted). Header right dropdown `Cette semaine ⌄`.

### 4.17 Info / shortcut cards (bottom row)
Card, padding 18px 20px, icon tile 40px left + title (13.5px 700) + one-line description (12px muted). Row of 2–3 cards, equal width. Used for tips/shortcuts: "Signaler une fuite — Localisez le problème", "Suivre une réparation — Consultez son statut", "Mes points : 120 — Voir mes récompenses", "Avant de partir…", "Après la réparation…".

### 4.18 Footer
Centered, 11px `--ink-400`, margin-top 24px: `NOVA · Une ville attentive à chaque goutte d'eau`.

### 4.19 Form controls (for new pages — extend in the same style)
- Inputs/selects/textareas: height 44px, radius `--r-md`, 1px `--border`, bg `#fff`, padding 0 14px, text 13.5px `--ink-900`, placeholder `--ink-400`. Focus: border `--teal-500` + `0 0 0 3px rgba(20,184,166,.18)`. Labels: 12.5px 700 `--ink-900`, margin-bottom 6px. Helper text 11.5px `--ink-500`. Error text `--danger`.
- Checkbox/radio/toggle accent: `--teal-500`.
- Search field: same input with leading search icon.
- Modals: white, radius 20px, `--shadow-pop`, overlay `rgba(4,38,80,.45)`.
- Toasts: white card, left 4px teal bar (success) / amber (warning) / `--danger` (error).

---

## 5. Iconography

- **Library:** Lucide (or any outline set with the same look). Stroke **1.75px**, round caps/joins, size 18px in nav/tiles, 16px inline, 14px in meta.
- Icon colors: sidebar → white/85%; tiles → `--teal-700`; meta → `--ink-400`; location pin → rose `#E5487A`.
- Signature motif: the **four-point sparkle ✦** is used for "leak / report / reward" concepts (Signaler une fuite, Mes points). Use `sparkles`/`sparkle` for these.
- Suggested mapping: Accueil `home` · Signaler une fuite `sparkle` · Mes signalements `clock` · Consommation `table/list` · Récompenses `sparkles` · Interventions `wrench` · Planning `calendar` · Historique `history/clock` · Profil `user` · Tableau de bord `layout-dashboard` · Citoyens `user` · Plombiers `wrench/hard-hat` · Paramètres `settings` · Notifications `bell` · Statut ok `check`.

---

## 6. Patterns & page recipes

### 6.1 Home page recipe (any role)
1. Sidebar (role-specific).
2. *(Citizen)* topbar with breadcrumb + user chip.
3. Page header: eyebrow date (`MARDI 10 NOVEMBRE 2026`) → "Bonjour, {Prénom} 👋" → one-line subtitle; location pill on the right.
4. Hero banner with a CTA.
5. *(Technician)* 3 stat cards row.
6. Two-column cards: left = main list (reports/interventions), right = secondary (consumption donut / planning).
7. Bottom row of 2–3 info/shortcut cards.
8. *(Citizen)* centered footer tagline.

### 6.2 Admin dashboard recipe
1. Header with eyebrow `VUE D'ENSEMBLE`, H1 "Tableau de bord", subtitle, actions (Exporter / + Nouveau signalement).
2. 4 KPI cards.
3. Row: wide card "Signalements récents" (filter tabs + table) + card "Activité des signalements" (bar chart).
4. Row: "Plombiers disponibles" + "Actions rapides".

### 6.3 Generic inner page (list, detail, form)
- Same shell; header with eyebrow = section name (UPPERCASE), H1 = page name, subtitle; optional header actions on the right.
- Content in white cards. Lists → §4.8, tables → §4.12, forms → §4.19, KPIs → §4.9/4.10, charts → §4.11/4.13.
- Detail pages: left wide card (main info), right narrow column of cards (status, assignee, timeline). Status shown with the standard pill. Add a "← Retour" text link above the header when drilling in.
- Empty states: centered icon tile (56px), title 15px 700, description 12.5px muted, one primary button.
- Always keep at least one clear teal primary action per page.

### 6.4 Domain vocabulary (reuse verbatim)
Signalement(s) · Fuite · Intervention(s) · Plombier(s) · Citoyen(s) · Récompenses · Points · Consommation · Compteur · m³ · Attribuer · Mettre à jour le statut · Compte rendu envoyé · Priorité normale / élevée · Créneau disponible · Centre-ville · Mutuelleville · Lafayette · Tunis Centre.

### 6.5 Data formatting
- Numbers: French format — space thousands separator, comma decimal (`1 284`, `8,4`). Percent with space: `4 %`.
- Dates: `10 novembre 2026` (long, eyebrow UPPERCASE), `10 nov.` (short), times 24h `09:42`.
- IDs: `#NV-0247`.
- Trend arrows: `↑` / `↓` followed by percent and comparison phrase (`vs mois dernier`, `ce mois-ci`).

### 6.6 Priority labels
`Priorité normale` (muted text), `Priorité élevée` (text in `--st-todo-fg`, 600). Shown in meta lines, not as pills.

---

## 7. Interaction & motion
- Transitions: `150–200ms ease` on background, border, transform, shadow.
- Card hover (clickable only): `translateY(-1px)` + slightly stronger shadow.
- Buttons: hover darken per §4.4; active `translateY(1px)`; focus-visible ring `0 0 0 3px rgba(20,184,166,.35)`.
- Donut and bar chart animate on load (draw/grow 600ms ease-out).
- Respect `prefers-reduced-motion`.
- Accessibility: minimum text contrast 4.5:1 (never put `--ink-400` on colored backgrounds for essential text); hit targets ≥ 36px; every icon-only button has `aria-label`; nav active item uses `aria-current="page"`.

---

## 8. Reference code skeleton (copy this structure)

```html
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NOVA — Ville & Eau</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    /* 1) paste :root tokens from §1 here */
    * { box-sizing: border-box; margin: 0; }
    body { font-family: "Plus Jakarta Sans","Inter",system-ui,sans-serif; background: var(--bg-page); color: var(--ink-700); -webkit-font-smoothing: antialiased; }

    .sidebar { position: fixed; inset: 0 auto 0 0; width: var(--sidebar-w); background: var(--sidebar-bg); padding: 24px 16px; display: flex; flex-direction: column; color: #fff; }
    .nav-label { font-size: 10.5px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--sidebar-muted); margin: 8px 12px; }
    .nav-item { display: flex; align-items: center; gap: 12px; height: 42px; padding: 0 14px; border-radius: var(--r-md); color: #fff; font-size: 14px; font-weight: 500; text-decoration: none; border: 1px solid transparent; }
    .nav-item:hover { background: var(--sidebar-hover-bg); }
    .nav-item[aria-current="page"] { background: var(--sidebar-active-bg); border-color: var(--sidebar-active-border); font-weight: 600; }

    .main { margin-left: var(--sidebar-w); padding: 28px var(--content-pad) 40px; }
    .eyebrow { font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--teal-700); }
    h1 { font-size: 28px; font-weight: 800; color: var(--ink-900); line-height: 1.25; }
    .subtitle { font-size: 14px; color: var(--ink-500); margin-top: 4px; }

    .hero { background: var(--hero-gradient); color: #fff; border-radius: var(--r-xl); padding: 32px 36px; position: relative; overflow: hidden; }
    .hero h2 { font-size: 26px; font-weight: 800; line-height: 1.3; }
    .hero p { font-size: 13px; color: rgba(255,255,255,.85); margin: 8px 0 18px; }

    .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-lg); box-shadow: var(--shadow-card); padding: 22px 24px; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: var(--grid-gap); }
    .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--grid-gap); }

    .btn { display: inline-flex; align-items: center; gap: 8px; height: 40px; padding: 0 18px; border-radius: var(--r-md); font: 700 13.5px var(--font, inherit); border: 0; cursor: pointer; transition: .15s ease; }
    .btn-primary { background: var(--teal-500); color: var(--on-teal); box-shadow: 0 4px 12px rgba(20,184,166,.28); }
    .btn-primary:hover { background: var(--teal-600); }
    .btn-outline { background: #fff; color: var(--ink-900); border: 1px solid var(--border); }

    .tile { width: 40px; height: 40px; border-radius: var(--r-md); background: var(--teal-100); color: var(--teal-700); display: grid; place-items: center; }
    .pill { display: inline-flex; padding: 4px 10px; border-radius: 999px; font: 700 11px/14px inherit; }
    .pill.pending  { background: var(--st-pending-bg);  color: var(--st-pending-fg); }
    .pill.todo     { background: var(--st-todo-bg);     color: var(--st-todo-fg); }
    .pill.progress { background: var(--st-progress-bg); color: var(--st-progress-fg); }
    .pill.done     { background: var(--st-done-bg);     color: var(--st-done-fg); }
  </style>
</head>
<body>
  <aside class="sidebar"> <!-- logo, .nav-label, .nav-item list, bottom block --> </aside>
  <main class="main">
    <header> <!-- .eyebrow, h1, .subtitle, right-side actions --> </header>
    <!-- hero? / KPI row / .grid-2 of .card / bottom info cards / footer -->
  </main>
</body>
</html>
```

---

## 9. DO / DON'T

**DO**
- Reuse the exact tokens, radii, paddings and component patterns above.
- Keep every page inside the sidebar + main shell with a pale-blue canvas and white cards.
- Use teal for the single primary action and for positive data; navy for text and structure.
- Keep copy in French, friendly and short; keep vocabulary from §6.4.
- Keep whitespace generous (20px gaps, 22–24px card padding).
- Use the same status color for the same status everywhere.

**DON'T**
- Don't use pure black (`#000`) text or pure grey neutrals — greys are always blue-tinted (`--ink-*`).
- Don't use heavy shadows, thick borders, gradients on cards, or sharp (0–4px) corners.
- Don't add new accent colors, new fonts, or emoji inside UI chrome (only the 👋 in the greeting).
- Don't use white text on teal buttons.
- Don't center-align body content or use full-width edge-to-edge cards without the page padding.
- Don't change the sidebar width, nav order, or active-state style between pages.
- Don't use Title Case or ALL CAPS for titles (only eyebrows, table headers, sidebar section labels).

---

## 10. Pre-delivery checklist (the AI must verify before finishing a page)

- [ ] Sidebar is present, navy `#063B6E`, correct role nav, correct active item.
- [ ] Page background `#F4F9FC`; all content is in white 16px-radius cards with 1px `#E3EDF4` border.
- [ ] Font is Plus Jakarta Sans; H1 28px/800 navy; eyebrow teal uppercase.
- [ ] Only one primary teal button style, with dark navy bold text.
- [ ] Status pills use the fixed mapping; En cours is blue.
- [ ] Icons are 1.75px outline, 18px, consistent set.
- [ ] Numbers/dates are in French format; IDs are `#NV-0000`.
- [ ] Spacing uses the 4px scale; gaps are 20px; no stray colors.
- [ ] Responsive behaviour for ≤1024px and ≤768px is handled.
- [ ] Hover, focus and empty states are defined.

---

## 11. How to use this file (prompt template)

Give this file to the AI together with your request:

> "Using the attached **NOVA_DESIGN_SYSTEM.md**, build the page **[page name]** for the **[citizen / technician / back office]** space. Purpose: **[what the page is for]**. Content/features: **[list of data, actions, sections]**. Follow the design system exactly — same tokens, fonts, sidebar, cards, buttons, status pills, copy tone and French formatting. Output a single self-contained HTML file (or [React/Vue/etc.]) with CSS variables from §1. Run the checklist in §10 before answering."

*Keep this file in the project root and attach it to every new generation request so all NOVA pages stay identical in style and behaviour.*
