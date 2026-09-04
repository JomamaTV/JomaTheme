# JomaTheme

> A premium, dark, **liquid glass** Blueprint extension theme for **Pterodactyl 1.15.1** running **Blueprint beta-2026-08**. **Midnight Marine**: an abyssal midnight-blue base with cyan→teal→sky gradients, true liquid glass (frosted blur, specular top-light, refraction hairline edges), animated deep-sea caustics, live dashboard statistics, quick actions, premium glass server cards, a cloud-style console, command palette, toast notifications and page transitions — without touching a single Pterodactyl core file.

![status](https://img.shields.io/badge/status-active-22c55e) ![version](https://img.shields.io/badge/version-4.1.0-06b6d4) ![target](https://img.shields.io/badge/blueprint-beta--2026--08-06b6d4) ![pterodactyl](https://img.shields.io/badge/pterodactyl-1.15.1-38bdf8)

---

## Screenshots

> Place screenshots here once installed.

| Dashboard | Server console | Admin page |
|-----------|----------------|------------|
| _todo_    | _todo_         | _todo_     |

---

## Features

- 🌊 **Animated deep-sea caustics** — a drifting cyan/teal/sky gradient mesh on every page, with a subtle sea-bubble dot-grid + ambient marine blobs.
- 🪟 **True liquid glass** — frosted, blurred floating layers (navbar, modals, dropdowns, toasts) plus crisp content cards with a static specular top-light layer and bright inset edges.
- 🎮 **Premium server cards** — translucent glass cards with a gradient border that brightens on hover, status glow dots and smooth elevation.
- 🖱️ **Premium button system** — cyan→teal marine gradient + sea-glow on hover, 3D press on click, and a **click ripple** that fires on every button across the panel.
- ⌘ **Command palette** — `Ctrl`/`⌘` + `K` opens a glassy, keyboard-navigable palette (server-aware: on `/server/<id>` it lists that server's tabs first; admin commands appear for admins).
- ✨ **Layout slide transitions** — content slides + fades on every client-side route change (history-aware).
- 🔔 **Toast notifications** — power actions (start/stop/restart/kill) surface as auto-dismissing toasts; full JS API (`window.JomaTheme.toast`).
- ⏳ **Loading states** — a top gradient progress bar tracks in-flight `fetch`/XHR requests; skeleton shimmer utility for your own content.
- 🖥️ **Cloud console** — dark glass terminal with inner glow, auto-scroll to the latest line and a one-click **Copy** button.
- 🗂️ **File-manager selection dock** — on `/server/<id>/files`: selection checkboxes are always visible (not hover-only), plus a floating glass dock with **Alle auswählen / Auswahl aufheben**, **Shift+Klick range select** and a two-step **mass delete** via the official client API.
- 🫧 **Extra rounded** — 24–28px glass radii, pill-rounded table rows and pill buttons for a cleaner, softer look.
- 🎯 **Premium dashboard hero** — "JOMAMC · CONTROL PANEL" with an animated gradient heading, sparkle, **live server/online stats** (real Pterodactyl data) and quick-action buttons, injected above the server list (`Dashboard.Serverlist.BeforeContent`).
- 📊 **Statistics cards** — glass stat tiles with gradient icons and subtle ambient glow.
- 🎛️ **Animated admin page** at `/admin/extensions/jomatheme` with a live accent-color preview.
- ♿ **Accessible & responsive** — `prefers-reduced-motion` guard disables heavy animation; works from 320px up.
- 🛡️ **Non-destructive** — pure CSS + a runtime `<style>`/`<script>` wrapper + one small React component. Cannot break Pterodactyl's React rendering; removing the extension leaves zero residue.

---

## Compatibility

| Component      | Version           |
|----------------|-------------------|
| Pterodactyl    | `1.15.1`          |
| Blueprint      | `beta-2026-08`    |
| Laravel        | `12.x`            |
| Webserver      | Nginx             |
| Panel path     | `/var/www/pterodactyl` |
| Domain         | `panel.jomamc.de` |

> The theme uses only the officially documented Blueprint extension fields (`conf.yml`: `dashboard.css`, `dashboard.wrapper`, `admin.css`, `admin.view`, `dashboard.components`) and the `--blueprint-*` color-variable theming model. See <https://blueprint.zip/docs/themes/colors>.

---

## Project structure

```text
JomaTheme/
├── conf.yml                 # Blueprint extension manifest
├── dashboard.css            # global theme CSS (compiled into the React bundle)
├── admin.css                # admin-panel theme CSS
├── wrapper.blade.php        # dashboard wrapper: animations + runtime JS
├── view.blade.php           # admin page at /admin/extensions/jomatheme
├── components/
│   ├── Components.yml       # React component injection map
│   ├── tsconfig.json        # TS config for injected components
│   └── elements/
│       └── JomaWelcome.tsx  # personalised welcome banner
├── .gitignore
├── LICENSE
└── README.md
```

---

## Installation

### From source (development)

These files mirror the contents of Blueprint's **`.blueprint/dev/`** folder (`conf.yml` lives at the root of the dev folder).

```bash
# 1. Put the extension files into the dev folder (merge with existing conf.yml)
sudo rsync -a ./JomaTheme/ /var/www/pterodactyl/.blueprint/dev/

# 2. Enable developer mode (Admin → Extensions → Blueprint → developer: true)

# 3. Build live onto the panel
cd /var/www/pterodactyl
sudo blueprint -build

# 4. Clear cached views so the themed wrapper is picked up
sudo -u www-data php artisan optimize:clear
```

### From a packaged `.blueprint` file

```bash
cd /var/www/pterodactyl
sudo blueprint -install jomatheme        # auto-detects install vs update
sudo -u www-data php artisan optimize:clear
```

---

## Build / Export

```bash
cd /var/www/pterodactyl

# Rebuild dev files live after every change (or use -watch)
sudo blueprint -build

# Package a distributable jomatheme.blueprint file
sudo blueprint -export
```

`blueprint -export` writes `jomatheme.blueprint` into the panel web directory.

---

## Update

```bash
cd /var/www/pterodactyl
sudo blueprint -install jomatheme        # re-running install performs an update
sudo -u www-data php artisan optimize:clear
```

---

## Deinstallation

```bash
cd /var/www/pterodactyl
sudo blueprint -remove jomatheme
sudo -u www-data php artisan optimize:clear
```

Because the theme is delivered through CSS + a wrapper `<style>`/`<script>` (and one small React component), removal leaves **no broken styles, routes or React components** behind.

---

## Configuration

The JomaTheme palette lives at the top of [`dashboard.css`](./dashboard.css) in the `:root` block:

```css
:root {
  --blueprint-primary-500:  6 182 212;   /* marine cyan accent (#06B6D4)   */
  --blueprint-neutral-50:    3 9 18;     /* midnight abyss base (#030912)  */
  --blueprint-white:        12 32 52;    /* marine glass / raised surfaces */
  --joma-secondary:        45 212 191;   /* teal accent (#2DD4BF)          */
  --joma-accent:           56 189 248;   /* sky accent (#38BDF8)           */
  /* ... full 50–950 scale + JomaTheme tokens inside the file             */
}
```

Color values **must be plain space-separated RGB channels** (e.g. `124 92 252`) — this is the Tailwind `rgb(var(--x) / <alpha>)` convention Blueprint requires. CSS color functions (`#hex`, `rgb()`, `hsl()`) are **not** supported for these variables.

Editable settings:

| Setting          | Where                    |
|------------------|--------------------------|
| Primary color    | `--blueprint-primary-*` in `dashboard.css` |
| Surfaces / text  | `--blueprint-neutral-*`, `--blueprint-white` |
| Border radius    | `--joma-radius` in `dashboard.css` |
| Animations       | keyframes in `wrapper.blade.php` |
| Welcome banner   | `components/elements/JomaWelcome.tsx` |
| Live accent preview | color picker on the admin page |

For a persisted admin settings UI (saved to DB), wire up the `admin.controller` and `database.migrations` `conf.yml` fields — see <https://blueprint.zip/guides/dev/adminconfiguration>.

---

## Development

```bash
# 1. Yarn is the documented Pterodactyl build tool
cd /var/www/pterodactyl
yarn install

# 2. Watch the panel frontend (port 5173 HMR) while editing
yarn run watch

# 3. Or rebuild for production
yarn run build:production
```

Then `blueprint -build` to apply the extension onto the live panel.

### Architecture notes

- **`dashboard.css`** is compiled *into the Pterodactyl React bundle* — keep it plain, valid CSS (no SCSS). A syntax error here can break client rendering, so the heavy keyframes and runtime JS live in the wrapper instead.
- **`wrapper.blade.php`** is included on every page via `@yield('blueprint.wrappers')`. Its `<style>`/`<script>` are wrapped in `@verbatim` so Blade never touches the CSS/JS. This is the safe home for animations and enhancements.
- **Navigation** is rendered by Pterodactyl's React `NavigationBar.tsx`. JomaTheme restyles it purely via CSS (targeting stable Tailwind classes and `#NavigationBar`), never by editing the component.
- **`JomaWelcome.tsx`** is injected through `Components.yml` at the `Dashboard.Serverlist.BeforeContent` slot. It reads `window.PterodactylUser` with heavy guarding for the greeting, and fetches `/api/client/servers` (+ per-server `/resources`, capped) to show **real** server and online counts. All fetches are abortable and best-effort — if the API is unavailable the stats row is simply hidden, never faked.
- **Notifications** hook `window.fetch` and `XMLHttpRequest` to observe power-action requests (`/api/client/servers/{id}/power`) and emit success/error toasts — purely observational, never modifying the request or response.

---

## Troubleshooting

| Symptom | Fix |
|--------|-----|
| Theme not visible after install | `sudo -u www-data php artisan optimize:clear`, hard-refresh (cached views/JS). |
| Dashboard blank / React error | A `dashboard.css` syntax error can break the bundle. Temporarily clear `dashboard.css` to confirm, then fix and `blueprint -build`. |
| Welcome banner missing | The React component needs `dashboard.components: "components"` in `conf.yml` and a valid `components/Components.yml`. Remove that line to run CSS-only. |
| Power-action toasts not appearing | The panel may use a transport the interceptor doesn't catch; the toast API (`window.JomaTheme.toast`) still works manually. |
| Too much animation | Respects `prefers-reduced-motion`; enable it in OS settings to disable heavy effects. |

---

## License

MIT © JomaMC. See [LICENSE](./LICENSE).

---

*Built against the official Blueprint documentation. Where this README and the live Blueprint docs disagree, **the official docs win**.*
