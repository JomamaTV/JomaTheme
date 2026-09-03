#!/bin/ash
# =============================================================================
# Velocity Proxy - install script (Java 25 egg)
# -----------------------------------------------------------------------------
# v1.1 - robustness fix: every curl call has connect/max timeouts + retries,
#        so a flaky fill.papermc.io connection FAILS FAST instead of hanging
#        the Pterodactyl install forever.
# Uses the current PaperMC downloads service (fill.papermc.io / v3).
# A descriptive User-Agent is MANDATORY for fill.papermc.io.
# Shell: POSIX/ash (runs in ghcr.io/parkervcp/installers:alpine).
# Server files: /mnt/server
# =============================================================================

set -u

PROJECT="velocity"
USER_AGENT="JomaMC-Velocity-Egg/1.1.0 (https://github.com/JomamaTV/JomaTheme)"

# --- curl wrapper: short connect timeout, hard max-time, retries -------------
# usage: api_get OUTVAR URL   -> stores body (or empty) in OUTVAR, never hangs
api_get() {
    outvar="$1"; url="$2"
    body=$(curl --connect-timeout 10 --max-time 45 --retry 3 --retry-delay 2 \
                 -sSL -A "${USER_AGENT}" "$url" 2>/dev/null) || body=""
    eval "$outvar=\$body"
}

# download to a file: longer max-time (jar ~42MB), follow redirects, show meter
dl_file() {
    outfile="$1"; url="$2"
    curl --connect-timeout 10 --max-time 240 --retry 3 --retry-delay 2 \
         -L -A "${USER_AGENT}" -o "$outfile" "$url"
}

cd /mnt/server || { echo "ERROR: /mnt/server not mounted"; exit 1; }

echo "[JomaMC] Velocity install starting..."

# -----------------------------------------------------------------------------
# 0. Sanity: jq must exist
# -----------------------------------------------------------------------------
if ! command -v jq >/dev/null 2>&1; then
    echo "ERROR: jq is not installed in the install container."
    echo "Use ghcr.io/parkervcp/installers:alpine (has jq) as the install image."
    exit 1
fi

# -----------------------------------------------------------------------------
# 1. Resolve the download URL
# -----------------------------------------------------------------------------
if [ -n "${DL_PATH:-}" ]; then
    echo "[JomaMC] Using supplied download url: ${DL_PATH}"
    DOWNLOAD_URL=$(eval echo "$(echo "${DL_PATH}" | sed -e 's/{{/${/g' -e 's/}}/}/g')")
else
    if [ -z "${VELOCITY_VERSION:-}" ] || [ "${VELOCITY_VERSION}" = "latest" ]; then
        VELOCITY_VERSION="latest"
    fi

    echo "[JomaMC] Fetching project metadata from fill.papermc.io..."
    api_get PROJECT_JSON "https://fill.papermc.io/v3/projects/${PROJECT}"
    if [ -z "${PROJECT_JSON}" ]; then
        echo "WARN: PaperMC project API unreachable; trying fallback direct URL."
        DOWNLOAD_URL="https://fill-data.papermc.io/v1/objects/846411d2d0560fed0f23496ffb89681be528d2c0650ecdcf21724d2d7bd9c1ee/velocity-4.1.1-24.jar"
        VELOCITY_VERSION="4.1.1"; BUILD_NUMBER="24"
    fi

    if [ -z "${DOWNLOAD_URL:-}" ]; then
        # Resolve "latest" -> highest non-snapshot release (numeric-aware sort)
        if [ "${VELOCITY_VERSION}" = "latest" ]; then
            VELOCITY_VERSION=$(printf '%s' "${PROJECT_JSON}" \
                | jq -r '.versions[].[]' 2>/dev/null \
                | grep -v -- '-SNAPSHOT' \
                | awk -F. '{printf "%03d%03d%03d %s\n", $1, $2, $3, $0}' \
                | sort | tail -n1 | cut -d' ' -f2-)
            if [ -z "${VELOCITY_VERSION}" ]; then
                echo "WARN: could not resolve latest version; using fallback 4.1.1"
                VELOCITY_VERSION="4.1.1"
            else
                echo "[JomaMC] Resolved latest stable ${PROJECT} version: ${VELOCITY_VERSION}"
            fi
        else
            EXISTS=$(printf '%s' "${PROJECT_JSON}" \
                | jq -r --arg V "${VELOCITY_VERSION}" '.versions[].[] | select(. == $V)' 2>/dev/null)
            if [ -z "${EXISTS}" ]; then
                echo "WARN: version ${VELOCITY_VERSION} not found; using latest stable"
                VELOCITY_VERSION=$(printf '%s' "${PROJECT_JSON}" \
                    | jq -r '.versions[].[]' 2>/dev/null \
                    | grep -v -- '-SNAPSHOT' \
                    | awk -F. '{printf "%03d%03d%03d %s\n", $1, $2, $3, $0}' \
                    | sort | tail -n1 | cut -d' ' -f2-)
                [ -z "${VELOCITY_VERSION}" ] && VELOCITY_VERSION="4.1.1"
            else
                echo "[JomaMC] Version is valid. Using version ${VELOCITY_VERSION}"
            fi
        fi
    fi

    if [ -z "${DOWNLOAD_URL:-}" ]; then
        echo "[JomaMC] Fetching builds for ${VELOCITY_VERSION}..."
        api_get BUILDS_JSON "https://fill.papermc.io/v3/projects/${PROJECT}/versions/${VELOCITY_VERSION}/builds"
        if [ -z "${BUILDS_JSON}" ] || [ "${BUILDS_JSON}" = "null" ]; then
            echo "WARN: PaperMC builds API unreachable; trying fallback direct URL."
            DOWNLOAD_URL="https://fill-data.papermc.io/v1/objects/846411d2d0560fed0f23496ffb89681be528d2c0650ecdcf21724d2d7bd9c1ee/velocity-4.1.1-24.jar"
            BUILD_NUMBER="24"
        fi
    fi

    if [ -z "${DOWNLOAD_URL:-}" ]; then
        if [ -z "${BUILD_NUMBER:-}" ] || [ "${BUILD_NUMBER}" = "latest" ]; then
            BUILD_NUMBER=$(printf '%s' "${BUILDS_JSON}" \
                | jq -r '[.[] | select(.channel == "STABLE")] | .[0].id // empty' 2>/dev/null)
            if [ -z "${BUILD_NUMBER}" ]; then
                echo "WARN: no STABLE build; using newest build"
                BUILD_NUMBER=$(printf '%s' "${BUILDS_JSON}" | jq -r '.[0].id // empty' 2>/dev/null)
            fi
        else
            MATCH=$(printf '%s' "${BUILDS_JSON}" \
                | jq -r --arg B "${BUILD_NUMBER}" \
                '[.[] | select((.id | tostring) == $B)] | .[0].id // empty' 2>/dev/null)
            if [ -z "${MATCH}" ]; then
                echo "WARN: build ${BUILD_NUMBER} not found; using latest stable"
                BUILD_NUMBER=$(printf '%s' "${BUILDS_JSON}" \
                    | jq -r '[.[] | select(.channel == "STABLE")] | .[0].id // empty' 2>/dev/null)
                [ -z "${BUILD_NUMBER}" ] && BUILD_NUMBER=$(printf '%s' "${BUILDS_JSON}" | jq -r '.[0].id // empty' 2>/dev/null)
            fi
        fi

        echo "[JomaMC] ${PROJECT} version: ${VELOCITY_VERSION}  build: ${BUILD_NUMBER}"

        DOWNLOAD_URL=$(printf '%s' "${BUILDS_JSON}" \
            | jq -r --arg B "${BUILD_NUMBER}" \
            '[.[] | select((.id | tostring) == $B)] | .[0].downloads["server:default"].url // empty' 2>/dev/null)

        if [ -z "${DOWNLOAD_URL}" ]; then
            echo "WARN: could not resolve build download URL; trying fallback direct URL."
            DOWNLOAD_URL="https://fill-data.papermc.io/v1/objects/846411d2d0560fed0f23496ffb89681be528d2c0650ecdcf21724d2d7bd9c1ee/velocity-4.1.1-24.jar"
            BUILD_NUMBER="24"; VELOCITY_VERSION="4.1.1"
        fi
    fi
fi

# -----------------------------------------------------------------------------
# 2. Download the jar (keep the previous one as .old), then sanity-check size
# -----------------------------------------------------------------------------
echo "[JomaMC] Downloading: ${DOWNLOAD_URL}"
if [ -f "${SERVER_JARFILE}" ]; then
    mv "${SERVER_JARFILE}" "${SERVER_JARFILE}.old"
fi
if ! dl_file "${SERVER_JARFILE}" "${DOWNLOAD_URL}"; then
    echo "ERROR: jar download failed. Check the node's outbound network to fill-data.papermc.io."
    exit 1
fi

# Sanity: a real Velocity jar is ~30-50MB. <1MB = error page saved as jar.
SIZE=$(wc -c < "${SERVER_JARFILE}" 2>/dev/null | tr -d ' ')
if [ -n "${SIZE}" ] && [ "${SIZE}" -lt 1000000 ] 2>/dev/null; then
    echo "ERROR: downloaded jar is only ${SIZE} bytes — expected ~40MB. The URL likely returned an error page."
    echo "First bytes of the bad file:"
    head -c 200 "${SERVER_JARFILE}"; echo
    exit 1
fi
echo "[JomaMC] Downloaded jar: ${SIZE:-unknown} bytes"

# -----------------------------------------------------------------------------
# 3. velocity.toml (download from pelican-eggs, fall back to a minimal file)
# -----------------------------------------------------------------------------
if [ ! -f velocity.toml ]; then
    echo "[JomaMC] Downloading velocity.toml"
    if ! curl --connect-timeout 10 --max-time 30 --retry 2 -sSL \
            -o velocity.toml \
            "https://raw.githubusercontent.com/pelican-eggs/eggs/master/game_eggs/minecraft/proxy/java/velocity/velocity.toml"; then
        echo "WARN: could not download velocity.toml; writing a minimal default"
        cat > velocity.toml <<'TOML'
config-version = "2.6"
bind = "0.0.0.0:25577"
motd = "&3A Velocity Server"
show-max-players = 500
online-mode = true

[servers]
TOML
    fi
fi

# -----------------------------------------------------------------------------
# 4. forwarding.secret (Velocity needs this for modern IP forwarding)
# -----------------------------------------------------------------------------
if [ ! -f forwarding.secret ]; then
    echo "[JomaMC] Creating forwarding.secret"
    date +%s | sha256sum | base64 | head -c 12 > forwarding.secret
fi

echo "[JomaMC] install complete"
