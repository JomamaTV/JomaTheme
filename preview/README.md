# JomaTheme — Local Preview

Zero-dependency Node server that serves a clickable mock of the Pterodactyl panel using the **real theme files**, re-read live on every request. No panel, no database, no build step.

```bash
node preview/server.js            # http://localhost:7575 (opens your browser)
node preview/server.js 3000       # custom port
node preview/server.js --no-open  # don't auto-open the browser
```

## Pages

| Route | What it previews |
|-------|------------------|
| `/` | Dashboard — hero (aurora, liquid ring, stats), server cards, badges, inputs |
| `/server/a1b2c3d4` | Console — glass sidebar, terminal incl. copy button, power buttons (fire real toasts), CPU/RAM/Disk rail |
| `/server/a1b2c3d4/files` | **File manager** — always-visible checkboxes, selection dock (select all/none, Shift+Klick range, two-step delete), per-row download/delete |
| `/server/a1b2c3d4/backups` | Backups — table with icon actions |
| `/server/a1b2c3d4/schedules` | Schedules — cron table |
| `/server/a1b2c3d4/users` | Subusers — table with roles |
| `/server/a1b2c3d4/network` | Network — allocations table |
| `/server/a1b2c3d4/startup` | Startup — variables + container info |
| `/server/a1b2c3d4/settings` | Settings — general + danger zone |
| `/account` | Profile form + API keys |
| `/admin/extensions/jomatheme` | The real admin page rendered from `view.blade.php` + `admin.css` |

All server tabs share the Pterodactyl-like layout: sticky glass navbar, glass sidebar with Bootstrap-Icon navigation, page header with actions, rounded glass cards. Layout-only CSS lives in `preview/preview.css` (also read live); every color/surface/button style comes from the actual theme.

## How it works

- `dashboard.css` and the `<style>`/`<script>` blocks from `wrapper.blade.php` are inlined per request — **edit the theme, refresh the browser, see the result** (no `blueprint -build` needed).
- Mock API endpoints (`POST …/power`, `DELETE …/files/delete`) return 204 so the wrapper's toast system and the file dock behave exactly like on the real panel.
- File deletions are in-memory only and reset when the server restarts.
- Binds to `127.0.0.1` only; nothing is exposed to the network.

The preview is a **development aid**, not a pixel-perfect Pterodactyl clone — the real panel renders its own React markup; this mock reproduces the class structures the theme targets.
