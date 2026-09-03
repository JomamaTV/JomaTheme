#!/usr/bin/env python3
"""Build the importable Pterodactyl/Pelican egg JSON for Velocity (Java 25).

Reads install.sh (plain, human-editable) and emits egg-velocity-java25.json
with all string escaping handled by json.dump so the embedded shell script and
the stringified config-JSON are guaranteed valid.
"""
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parent
script = (ROOT / "install.sh").read_text().rstrip("\n")

# config.files / config.startup are stringified JSON (the panel parses them again)
config_files = json.dumps({
    "velocity.toml": {
        "parser": "file",
        "find": {"bind = ": 'bind = "0.0.0.0:{{server.build.default.port}}"'},
    }
})
config_startup = json.dumps({"done": "Listening on"})

egg = {
    "_comment": (
        "Velocity proxy egg (Java 25) for Pterodactyl/Pelican. Built against the "
        "current PaperMC downloads service (fill.papermc.io / v3). The legacy "
        "papermc.io/api/v2 endpoint is sunset. v1.1: every curl has connect/max "
        "timeouts + retries so a flaky API fails fast instead of hanging the "
        "install; includes a fallback direct URL and a jar-size sanity check. "
        "Startup-done token = 'Listening on' (Velocity logs 'Listening on <addr>' "
        "when the proxy is ready)."
    ),
    "meta": {"version": "PTDL_v2", "update_url": None},
    "exported_at": "2026-09-04T00:00:00+00:00",
    "name": "Velocity",
    "author": "admin@jomamc.de",
    "description": (
        "Velocity is a Minecraft server proxy with unparalleled server support, "
        "scalability, and flexibility. Configured for Java 25 and the current "
        "PaperMC downloads service (fill.papermc.io)."
    ),
    "features": ["java_version", "pid_limit"],
    "docker_images": {
        "Java 25": "ghcr.io/pterodactyl/yolks:java_25",
        "Java 21": "ghcr.io/pterodactyl/yolks:java_21",
    },
    "file_denylist": [],
    "startup": (
        "java -Xms128M -Xmx{{SERVER_MEMORY}}M "
        "-XX:+UseG1GC -XX:+AlwaysPreTouch "
        "-XX:+UnlockExperimentalVMOptions -XX:+ParallelRefProcEnabled "
        "-jar {{SERVER_JARFILE}}"
    ),
    "config": {
        "files": config_files,
        "startup": config_startup,
        "logs": "{}",
        "stop": "end",
    },
    "scripts": {
        "installation": {
            "script": script,
            "container": "ghcr.io/parkervcp/installers:alpine",
            "entrypoint": "ash",
        }
    },
    "variables": [
        {
            "name": "Velocity Version",
            "description": (
                "The Velocity Proxy version to download. Set to 'latest' to always "
                "fetch the newest stable release from the PaperMC downloads service."
            ),
            "env_variable": "VELOCITY_VERSION",
            "default_value": "latest",
            "user_viewable": True,
            "user_editable": False,
            "rules": "required|string|max:20",
            "field_type": "text",
        },
        {
            "name": "Server Jar File",
            "description": (
                "The jar file name. Defaults to 'velocity.jar'. Must end in .jar."
            ),
            "env_variable": "SERVER_JARFILE",
            "default_value": "velocity.jar",
            "user_viewable": True,
            "user_editable": False,
            "rules": r"required|string|max:32|regex:/^([\w\d._-]+)(\.jar)$/",
            "field_type": "text",
        },
        {
            "name": "Download Path",
            "description": (
                "A URL to download a custom server.jar instead of using the PaperMC "
                "downloads service. Supports templating with environment variables, "
                "e.g. https://example.com/${VELOCITY_VERSION}/velocity.jar."
            ),
            "env_variable": "DL_PATH",
            "default_value": "",
            "user_viewable": False,
            "user_editable": False,
            "rules": "nullable|string",
            "field_type": "text",
        },
        {
            "name": "Build Number",
            "description": (
                "The Velocity build number. Leave at 'latest' to always get the "
                "newest stable build. Invalid values default to latest."
            ),
            "env_variable": "BUILD_NUMBER",
            "default_value": "latest",
            "user_viewable": True,
            "user_editable": True,
            "rules": "required|string|max:20",
            "field_type": "text",
        },
    ],
}

out = ROOT / "egg-velocity-java25.json"
out.write_text(json.dumps(egg, indent=4) + "\n")
print(f"egg written: {out}")
