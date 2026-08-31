# Mockup WordPress — Liga Antioqueña de Balonmano

Create a polished, implementation-ready website mockup in Spanish for the existing WordPress block theme in this workspace. Use `website-liga-antioquena-wordpress.md` as the functional source of truth and `docs/Logo liga - Logo liga.pdf` as the official brand asset. Do not invent institutional facts; label demo content clearly as `Contenido demo`.

## Required deliverable

Create an editable `.pen` design with reusable components and these top-level screens:

1. `Inicio — Desktop`, width 1440 px, full page.
2. `Inicio — Mobile`, width 390 px, full page.
3. `Nosotros — Desktop`, width 1440 px.
4. `Actualidad — Desktop`, width 1440 px.
5. `Detalle de actualidad — Desktop`, width 1440 px.
6. `Selecciones — Desktop`, width 1440 px.
7. `Documentos — Desktop`, width 1440 px.
8. `Contacto — Desktop`, width 1440 px.
9. `Componentes LABM`, containing reusable buttons, navigation, news cards, document rows, filters, logo-marquee items, and form controls.

Place components above the screens and arrange screens with generous canvas separation. Every root screen must be clipped and fully expanded vertically so no content is hidden.

## Brand and visual direction

- Sports-oriented, institutional, contemporary, bold but sober.
- Primary lime `#AECD25`.
- Primary dark `#789614`.
- Ink `#000000`.
- Surface `#FFFFFF`.
- Soft surface `#F3F6E8`.
- Body text `#202020`.
- Use lime primarily for actions, active states, focus, lines and small accents. Use black text on lime.
- Headings: Barlow Condensed, bold/semibold.
- Body and UI: Inter.
- Use strong editorial hierarchy, large sports photography, diagonal accents and oversized numeric/date treatments.
- Avoid generic grids of rounded cards, excessive gradients, heavy shadows or decorative clutter.
- Ensure WCAG AA contrast. Body text must be at least 16 px.
- Use stock photography relevant to team handball. Never use football/soccer, basketball, American football, or generic gym imagery.
- Preserve the official logo; do not redraw, recolor, crop or distort it. If the PDF cannot be imported faithfully, use a clearly labeled neutral placeholder reading `LOGO OFICIAL LABM` rather than fabricating a logo.

## Global header

- Compact black institutional top bar with demo contact and social placeholders.
- Main white navigation immediately below: logo, Inicio, Nosotros, Actualidad, Selecciones with Piso/Playa concept, Documentos, Contacto, and lime CTA `Vincúlate`.
- Mobile screen uses an accessible hamburger control.

## Inicio — exact section order

1. Institutional top bar.
2. Main header/navigation.
3. Full-width hero slider directly under the navigation. Show the first slide plus visible previous/next controls, position indicators and a pause control. Hero height approximately 620 px desktop and 480 px mobile. The active slide uses a handball action photograph with dark contrast overlay, heading `El balonmano se vive en Antioquia`, concise copy and CTAs `Conoce la Liga` and `Ver actualidad`. Indicate that the slider supports three editable slides for actualidad, próximos eventos and institutional messaging.
4. Brief league introduction with editorial composition and CTA to Nosotros.
5. Associated clubs area with adaptable logo row.
6. Featured event or achievement with oversized date, image, location/category metadata and CTA.
7. Latest news: one lead story plus secondary stories, not a repetitive equal-card grid.
8. Black contact/membership CTA with lime accent.
9. `Aliados Oficiales` logo marquee immediately above the footer. Visually communicate an infinite right-to-left carousel using duplicated logo sequences and directional cues. Include visible pause/restart affordance and a note/state for reduced motion where logos become static. Logos must have uniform visual height but preserve aspect ratios.
10. Footer with institutional identity, secondary navigation, contact, social links, privacy and dynamic copyright concept.

Do not include the following sections on the Home screen:

- Balonmano de piso / Balonmano playa promotional section.
- Training schedules.
- Sports venues or map.

The Piso and Playa categories must still exist in the Selecciones screen and navigation; they are removed only from Home.

## Other screens

- `Nosotros`: who we are, mission, vision, executive committee, coaches and configurable member groups.
- `Actualidad`: chronological listing, category filters, featured story and pagination.
- `Detalle de actualidad`: hero, date/location/category metadata, rich body, gallery and share actions.
- `Selecciones`: a strong overview with tabs or filters for Balonmano Piso and Balonmano Playa, followed by participations, tournaments, convocations and achievements.
- `Documentos`: search, category filter, year filter, sort control, accessible responsive list, PDF metadata, distinct `Ver PDF` and `Descargar` actions, pagination and empty-state example.
- `Contacto`: institutional details, optional location link, social links, privacy copy and form fields for name, last name, email, optional phone, subject and message, including success/error examples.

## Responsive and accessibility behavior to express visually

- Desktop max content width approximately 1200 px.
- Mobile usable at 390 px and compatible with 320 px.
- Visible keyboard focus, 44 px minimum interactive targets and semantic heading hierarchy.
- Slider and marquee expose pause controls and honor reduced motion.
- Mobile tables become stacked content rather than horizontal overflow.
- Images reserve stable proportions.

Build each root screen with `placeholder: true` while editing it and clear the placeholder immediately when that screen is complete. Verify each completed screen for clipping, alignment, spacing and contrast. Use variables for the color and typography tokens.
