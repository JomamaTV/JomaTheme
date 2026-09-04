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
| `/server/a1b2c3d4` | Console page — power buttons (fire real toasts), stat cards, glass terminal incl. copy button |
| `/server/a1b2c3d4/files` | **File manager** — always-visible checkboxes, selection dock (select all/none, Shift+Klick range, two-step delete) |
| `/account` | Forms — inputs, labels, buttons |
| `/admin/extensions/jomatheme` | The real admin page rendered from `view.blade.php` + `admin.css` |

## How it works

- `dashboard.css` and the `<style>`/`<script>` blocks from `wrapper.blade.php` are inlined per request — **edit the theme, refresh the browser, see the result** (no `blueprint -build` needed).
- Mock API endpoints (`POST …/power`, `DELETE …/files/delete`) return 204 so the wrapper's toast system and the file dock behave exactly like on the real panel.
- File deletions are in-memory only and reset when the server restarts.
- Binds to `127.0.0.1` only; nothing is exposed to the network.

The preview is a **development aid**, not a pixel-perfect Pterodactyl clone — the real panel renders its own React markup; this mock reproduces the class structures the theme targets.
