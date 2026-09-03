# Velocity Egg — Java 25 (Pterodactyl / Pelican)

A Pterodactyl/Pelican egg for the **Velocity** Minecraft proxy, configured for **Java 25**, using the **current** PaperMC downloads service (`fill.papermc.io` / v3).

> Built fresh against the live PaperMC API and the Pterodactyl yolks registry. The stock community Velocity egg ships a broken install script (the old `papermc.io/api/v2` endpoint is **sunset** → HTTP 403/410) and a stale `"Done ("` startup token that Velocity never logs. This egg fixes both.

## Files

| File | Purpose |
|------|---------|
| `egg-velocity-java25.json` | **The importable egg.** Import this into your panel. |
| `install.sh` | The install script (plain, human-editable). Already embedded in the egg. |
| `build_egg.py` | Rebuilds the egg JSON from `install.sh` with correct escaping. |

## What's in it

- **Java image:** `ghcr.io/pterodactyl/yolks:java_25` (base `eclipse-temurin:25-jdk`, verified published). Java 21 included as a fallback image.
- **Install image:** `ghcr.io/parkervcp/installers:alpine` (has `curl` + `jq`), entrypoint `ash`.
- **Downloads:** `https://fill.papermc.io/v3/projects/velocity/...` with a descriptive `User-Agent` (mandatory on the new service). Picks the first `STABLE` build, follows the opaque object-store URL in `downloads["server:default"].url`.
- **Version default:** `latest` → resolves to the newest non-snapshot release (numeric-aware sort across all version groups).
- **Build default:** `latest` → newest stable build (falls back to newest build if no stable exists).
- **Startup command:** `java -Xms128M -Xmx{{SERVER_MEMORY}}M -XX:+UseG1GC -XX:+AlwaysPreTouch -XX:+UnlockExperimentalVMOptions -XX:+ParallelRefProcEnabled -jar {{SERVER_JARFILE}}`
- **Startup-done token:** `Listening on` — the line Velocity actually prints when the proxy is ready (`Listening on /0.0.0.0:25577`). The stock egg's `"Done ("` is a Bukkit leftover and never matched.
- **Stop command:** `end` (a verified alias of Velocity's `shutdown`).
- **Config:** rewrites `bind = ` in `velocity.toml` to the allocated server port; generates `forwarding.secret` if missing (needed for modern IP forwarding).

## Variables

| Name | Env | Default | Editable | Notes |
|------|-----|---------|----------|-------|
| Velocity Version | `VELOCITY_VERSION` | `latest` | no | `latest` = newest stable release |
| Server Jar File | `SERVER_JARFILE` | `velocity.jar` | no | must end in `.jar` |
| Download Path | `DL_PATH` | *(empty)* | no (hidden) | custom jar URL, supports `${ENV}` templating |
| Build Number | `BUILD_NUMBER` | `latest` | yes | invalid values fall back to latest stable |

## Import

**Pterodactyl panel:**
1. Admin → **Nests** → create a nest (e.g. `Proxies`) or reuse an existing one.
2. Admin → **Eggs** → **Import Egg** → upload `egg-velocity-java25.json` → assign to the nest.
3. In the egg settings, ensure the **Java 25** docker image (`ghcr.io/pterodactyl/yolks:java_25`) is selected under the egg's images (it's pre-listed).
4. Make sure your nodes can pull `ghcr.io/pterodactyl/yolks:java_25` and `ghcr.io/parkervcp/installers:alpine`.

**Pelican panel:** the same JSON imports the same way.

## Test it (curl the API yourself)

```bash
# all available versions (note: grouped object, e.g. {"4.0.0": ["4.1.1", ...]})
curl -sSL -A "my-egg/1.0 (https://example.com)" https://fill.papermc.io/v3/projects/velocity

# builds for a version → download URL is downloads["server:default"].url
curl -sSL -A "my-egg/1.0 (https://example.com)" \
  https://fill.papermc.io/v3/projects/velocity/versions/4.1.1/builds
```

## Rebuild after editing install.sh

```bash
python build_egg.py        # regenerates egg-velocity-java25.json
```

## Troubleshooting

| Symptom | Fix |
|---------|-----|
| **Install hangs forever** | Fixed in v1.1 — every `curl` now has `--connect-timeout 10`, a hard `--max-time` (45s API / 240s jar / 30s toml), and retries. If the node genuinely can't reach `fill.papermc.io`, the install now **fails fast** (~minutes, with clear `[JomaMC]` log lines) instead of hanging until Pterodactyl's install timeout. Check the node's outbound network (DNS + TLS to `fill.papermc.io` / `fill-data.papermc.io`). |
| Install fails: `jq: not found` | The install image must have `jq`. `ghcr.io/parkervcp/installers:alpine` does. Don't swap to a bare yolks image (no `jq`). |
| Install fails: `jar is only N bytes` | The resolved URL returned an error page. v1.1 sanity-checks the jar size (rejects <1 MB) and falls back to a verified direct URL. |
| Install fails: 403 / "Endpoint Unsupported" | You're hitting the dead `papermc.io/api/v2`. This egg uses `fill.papermc.io` — re-import the current egg. |
| Server never shows as "started" | The `done` token is `Listening on`. If a future Velocity build changes the log line, update `config.startup.done`. |
| Wrong Java at runtime | In the server's **Startup** tab, ensure the docker image dropdown is set to **Java 25**. |
| Port not applied | The egg rewrites `bind = ` in `velocity.toml`. If you supply a custom `velocity.toml`, keep a `bind =` line so the parser can find it. |

## Compatibility

Verified 2026-09 against:
- PaperMC downloads service `fill.papermc.io` (v3) — `papermc.io/api/v2` is sunset.
- `ghcr.io/pterodactyl/yolks:java_25` — published.
- `ghcr.io/parkervcp/installers:alpine` — pullable.
- Velocity `4.1.1` build `24` (example; `latest` will track newer builds).

*Format: PTDL_v2. Where this README and the live PaperMC/Pterodactyl docs disagree, the live docs win.*
