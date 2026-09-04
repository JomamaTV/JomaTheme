#!/usr/bin/env node
'use strict';
/* ============================================================================
   JomaTheme local preview — zero-dependency Pterodactyl mock server.
   ----------------------------------------------------------------------------
   Serves a faithful impression of the panel using the REAL theme files,
   re-read live on every request: edit dashboard.css / wrapper.blade.php /
   admin.css / view.blade.php and just refresh the browser.

   Pages:  /                          dashboard (hero + server cards)
           /server/:id                console (power toasts + terminal)
           /server/:id/files          file manager (selection dock, delete)
           /server/:id/backups|schedules|users|network|startup|settings
           /account                   profile + API keys
           /admin/extensions/jomatheme   real admin page (view.blade.php)

   Mock API:
     POST   /api/client/servers/:id/power         -> 204 (fires theme toasts)
     DELETE /api/client/servers/:id/files/delete  -> 204 (in-memory delete)

   Usage:  node preview/server.js [port] [--no-open]   (default port 7575)
   Binds to 127.0.0.1 only. Mock files reset on restart.
   ============================================================================ */

const http = require('http');
const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');

const ROOT = path.join(__dirname, '..');
const BASE_PORT = Number(process.argv[2]) > 0 ? Number(process.argv[2]) : 7575;
const NO_OPEN = process.argv.includes('--no-open');

/* --------------------------------------------------------------------------
   Mock state
   -------------------------------------------------------------------------- */
const servers = [
  { id: 'a1b2c3d4', name: 'Survival-01', egg: 'Paper 1.21.1', desc: '8 GB RAM · 42 Slots', state: 'running', cpu: 34, mem: 61, disk: 47 },
  { id: 'b2c3d4e5', name: 'Skyblock-02', egg: 'Paper 1.21.1', desc: '4 GB RAM · 24 Slots', state: 'running', cpu: 12, mem: 40, disk: 23 },
  { id: 'c3d4e5f6', name: 'Proxy-Velocity', egg: 'Velocity 3.4', desc: '1 GB RAM · Java 25', state: 'offline', cpu: 0, mem: 0, disk: 8 },
  { id: 'd4e5f6a7', name: 'Bedwars-Arena', egg: 'Paper 1.20.4', desc: '6 GB RAM · 16 Slots', state: 'starting', cpu: 8, mem: 22, disk: 31 },
];
const byId = (id) => servers.find((s) => s.id === id);

const seedFiles = () => [
  { name: 'plugins', size: '—', mod: '04.09.2026 14:02', dir: true },
  { name: 'world', size: '—', mod: '04.09.2026 16:45', dir: true },
  { name: 'logs', size: '—', mod: '04.09.2026 16:50', dir: true },
  { name: 'server.properties', size: '2 KB', mod: '03.09.2026 09:12', dir: false },
  { name: 'paper-1.21.1.jar', size: '48 MB', mod: '28.08.2026 18:30', dir: false },
  { name: 'eula.txt', size: '1 KB', mod: '27.08.2026 12:00', dir: false },
];
const filesByServer = {};
servers.forEach((s) => { filesByServer[s.id] = seedFiles(); });

/* --------------------------------------------------------------------------
   Live theme loading (re-read on every request)
   -------------------------------------------------------------------------- */
function readTheme() {
  const css = fs.readFileSync(path.join(ROOT, 'dashboard.css'), 'utf8');
  const shellCss = fs.readFileSync(path.join(__dirname, 'preview.css'), 'utf8');
  const wrapper = fs.readFileSync(path.join(ROOT, 'wrapper.blade.php'), 'utf8');
  const style = (wrapper.match(/<style>([\s\S]*?)<\/style>/) || [])[1] || '';
  const scripts = [];
  const re = /<script>([\s\S]*?)<\/script>/g;
  let m;
  while ((m = re.exec(wrapper))) scripts.push(m[1]);
  return { css, shellCss, style, scripts };
}

function readAdminPage() {
  const blade = fs.readFileSync(path.join(ROOT, 'view.blade.php'), 'utf8');
  const css = fs.readFileSync(path.join(ROOT, 'admin.css'), 'utf8');
  const start = blade.indexOf("@section('content')");
  const end = blade.lastIndexOf('@endsection');
  let body = blade.slice(start + "@section('content')".length, end);
  body = body.split('\n').filter((l) => l.trim() !== '@verbatim' && l.trim() !== '@endverbatim').join('\n');
  return { css, body };
}

function esc(s) {
  return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

/* --------------------------------------------------------------------------
   Shell pieces
   -------------------------------------------------------------------------- */
function navbar(active) {
  const link = (href, label) =>
    '<a href="' + href + '"' + (href === active ? ' aria-current="page"' : '') + '>' + label + '</a>';
  return (
    '<nav id="NavigationBar" class="jpv-navbar">' +
    '<a class="jpv-logo" href="/"><span class="jpv-logo__mark">J</span> JomaMC</a>' +
    '<div class="jpv-nav">' + link('/', 'Dashboard') + link('/account', 'Account') + link('/admin/extensions/jomatheme', 'Theme Admin') + '</div>' +
    '<div class="jpv-user"><span class="jpv-user__ava">L</span> Lasse <i class="bi bi-chevron-down" style="font-size:.6rem;color:rgb(122 152 178)"></i></div>' +
    '</nav>'
  );
}

const TABS = [
  ['console', 'bi-terminal', 'Console'],
  ['files', 'bi-folder', 'Files'],
  ['backups', 'bi-archive', 'Backups'],
  ['schedules', 'bi-clock', 'Schedules'],
  ['users', 'bi-people', 'Users'],
  ['network', 'bi-ethernet', 'Network'],
  ['startup', 'bi-rocket-takeoff', 'Startup'],
  ['settings', 'bi-gear', 'Settings'],
];

function serverShell(opts) {
  const s = opts.server;
  const links = TABS.map(([key, icon, label]) => {
    const href = key === 'console' ? '/server/' + s.id : '/server/' + s.id + '/' + key;
    return '<a href="' + href + '"' + (key === opts.tab ? ' class="is-active"' : '') + '><i class="bi ' + icon + '"></i> ' + label + '</a>';
  }).join('');
  const dot = s.state === 'running' ? 'joma-dot--online' : s.state === 'starting' ? 'joma-dot--starting' : 'joma-dot--offline';

  const body =
    '<div class="jpv-container"><div class="jpv-server">' +
    '<aside class="jpv-side">' +
    '<div class="jpv-side__head"><span class="jpv-side__icon"><i class="bi bi-hdd-stack"></i></span>' +
    '<div><div class="jpv-side__name">' + esc(s.name) + '</div><div class="jpv-side__sub">' + esc(s.egg) + '</div></div></div>' +
    '<nav>' + links + '</nav>' +
    '</aside>' +
    '<div>' + opts.body + '</div>' +
    '</div></div>';

  return shell({ title: s.name + ' · ' + opts.tab, active: '/server/' + s.id, body, pageScript: opts.pageScript, head: s, dot });
}

function shell(opts) {
  const t = readTheme();
  return [
    '<!doctype html><html lang="de"><head><meta charset="utf-8">',
    '<meta name="viewport" content="width=device-width, initial-scale=1">',
    '<title>' + esc(opts.title) + ' · JomaTheme Preview</title>',
    '<style>' + t.css + '</style>',
    '<style>' + t.shellCss + '</style>',
    '<style>' + t.style + '</style>',
    '</head><body>',
    '<div id="jomatheme-progress" aria-hidden="true"><div id="jomatheme-progress__bar"></div></div>',
    '<div id="jomatheme-toasts" aria-live="polite" aria-atomic="false"></div>',
    navbar(opts.active),
    opts.body,
    '<footer class="jpv-footer">JomaTheme local preview — theme files are read <strong>live</strong>, just refresh · mock data resets on restart</footer>',
    '<script>window.PterodactylUser = { username: "Lasse", first_name: "Lasse", root_admin: true };</script>',
    '<script>' + (opts.pageScript || '') + '</script>',
  ].concat(t.scripts.map((s) => '<script>' + s + '</script>'))
    .concat(['</body></html>'])
    .join('\n');
}

function badge(text) { return '<span class="Badge" style="padding:.22rem .7rem">' + esc(text) + '</span>'; }
function iconBtn(icon, title, extra) {
  return '<button type="button" class="' + (extra || '') + ' jpv-iconbtn" title="' + title + '"><i class="bi ' + icon + '"></i></button>';
}

/* --------------------------------------------------------------------------
   Pages — dashboard
   -------------------------------------------------------------------------- */
function dashboardPage() {
  const cards = servers.map((s) => {
    const dot = s.state === 'running' ? 'joma-dot--online' : s.state === 'starting' ? 'joma-dot--starting' : 'joma-dot--offline';
    return (
      '<div class="ServerCard" style="padding:1.2rem 1.3rem">' +
      '<div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.55rem"><span class="joma-dot ' + dot + '"></span>' +
      '<h3 style="margin:0;font-size:1.05rem">' + esc(s.name) + '</h3>' +
      '<span style="margin-left:auto">' + badge(s.state) + '</span></div>' +
      '<p class="text-neutral-500" style="margin:0 0 1rem;font-size:.86rem">' + esc(s.egg) + ' · ' + esc(s.desc) + '</p>' +
      '<div class="progress" style="height:7px;margin-bottom:.55rem"><div class="progress-bar" style="width:' + s.cpu + '%"></div></div>' +
      '<p class="text-neutral-500" style="margin:0 0 1rem;font-size:.75rem">CPU ' + s.cpu + '% · RAM ' + s.mem + '%</p>' +
      '<a class="btn-primary" href="/server/' + s.id + '" style="padding:.5rem 1.1rem;text-decoration:none;display:inline-block;font-size:.84rem">Verwalten</a> ' +
      '<a class="btn-secondary" href="/server/' + s.id + '/files" style="padding:.5rem 1.1rem;text-decoration:none;display:inline-block;font-size:.84rem">Dateien</a>' +
      '</div>'
    );
  }).join('\n');

  const body =
    '<div class="jpv-container">' +
    '<div class="jomatheme-welcome">' +
    '<div class="jomatheme-welcome__aurora"></div><div class="jomatheme-welcome__grid"></div>' +
    '<div class="jomatheme-welcome__inner">' +
    '<p class="jomatheme-welcome__eyebrow"><span class="jomatheme-welcome__dot"></span> JOMAMC &middot; CONTROL PANEL</p>' +
    '<h2 class="jomatheme-welcome__title">Willkommen zurück, <span class="jomatheme-welcome__name">Lasse</span></h2>' +
    '<p class="jomatheme-welcome__sub">Deine Infrastruktur auf einen Blick — Server, Ressourcen und Aktionen einen Klick entfernt.</p>' +
    '<div class="jomatheme-welcome__stats">' +
    '<div class="jomatheme-welcome__stat"><span><span class="jomatheme-welcome__stat-num">4</span><span class="jomatheme-welcome__stat-label"> Server</span></span></div>' +
    '<div class="jomatheme-welcome__stat"><span><span class="jomatheme-welcome__stat-num">2</span><span class="jomatheme-welcome__stat-label"> Online</span></span></div>' +
    '</div>' +
    '<div class="jomatheme-welcome__actions">' +
    '<a class="jomatheme-welcome__btn jomatheme-welcome__btn--primary" href="/server/a1b2c3d4/files"><i class="bi bi-folder2-open"></i> Dateien öffnen</a>' +
    '<a class="jomatheme-welcome__btn" href="/account"><i class="bi bi-person"></i> Account</a>' +
    '</div></div></div>' +
    '<div class="jpv-grid">' + cards + '</div>' +
    '</div>';

  return shell({ title: 'Dashboard', active: '/', body });
}

/* --------------------------------------------------------------------------
   Pages — server tabs
   -------------------------------------------------------------------------- */
function consolePage(id) {
  const s = byId(id);
  if (!s) return notFoundPage();
  const stat = (label, val, unit) =>
    '<div class="joma-stat"><p class="joma-stat__label">' + label + '</p><p class="joma-stat__value">' + val + '<span style="font-size:.9rem;font-weight:600"> ' + unit + '</span></p>' +
    '<div class="progress" style="height:7px;margin-top:.6rem"><div class="progress-bar" style="width:' + val + '%"></div></div></div>';

  const lines = [
    '[16:44:02 INFO]: Starting minecraft server version 1.21.1',
    '[16:44:03 INFO]: Loading properties',
    '[16:44:05 INFO]: Preparing level "world"',
    '[16:44:11 INFO]: Done (6.312s)! For help, type "help"',
    '[16:44:32 INFO]: Lasse joined the game',
    '[16:45:10 INFO]: [EssentialsX] AFK: toggled for Lasse',
    '[16:47:41 INFO]: [LuckPerms] User data loaded for Lasse',
    '[16:52:03 INFO]: Saving the game (this may take a moment!)',
    '[16:52:04 INFO]: Saved the game',
  ].map((l) => '<div style="padding:.08rem 0">' + esc(l) + '</div>').join('');

  const body =
    '<div class="jpv-pagehead"><h1><span class="joma-dot joma-dot--online"></span> Console ' + badge(s.state) + '</h1></div>' +
    '<div class="jpv-console">' +
    '<div class="bg-white jpv-card" style="padding:1rem"><div class="terminal" style="height:430px;overflow-y:auto;padding:1rem 1.1rem;font-family:ui-monospace,Menlo,Consolas,monospace;font-size:.82rem;line-height:1.55">' + lines + '</div></div>' +
    '<div class="jpv-rail">' +
    '<div class="bg-white jpv-card"><h2>Power</h2><div class="jpv-power">' +
    '<button type="button" class="primary" data-power="start"><i class="bi bi-play-fill"></i> Start</button>' +
    '<button type="button" class="secondary" data-power="restart"><i class="bi bi-arrow-clockwise"></i> Restart</button>' +
    '<button type="button" data-power="stop"><i class="bi bi-stop-fill"></i> Stop</button>' +
    '<button type="button" class="danger" data-power="kill"><i class="bi bi-x-octagon"></i> Kill</button>' +
    '</div></div>' +
    stat('CPU', s.cpu, '%') +
    stat('Memory', s.mem, '%') +
    stat('Disk', s.disk, '%') +
    '</div></div>';

  const pageScript =
    'document.querySelectorAll("[data-power]").forEach(function (b) {' +
    '  b.addEventListener("click", function () {' +
    '    fetch("/api/client/servers/' + id + '/power", { method: "POST", headers: { "Content-Type": "application/json", "Accept": "application/json" }, body: JSON.stringify({ signal: b.dataset.power }) });' +
    '  });' +
    '});';

  return serverShell({ server: s, tab: 'console', body, pageScript });
}

function filesPage(id) {
  const s = byId(id);
  if (!s) return notFoundPage();
  const files = filesByServer[id] || [];
  const rows = files.map((f) => {
    const icon = f.dir ? 'bi-folder-fill' : 'bi-file-earmark-text';
    return (
      '<tr><td><input type="checkbox" aria-label="' + esc(f.name) + ' auswählen"></td>' +
      '<td><span class="jpv-fname"><i class="bi ' + icon + '"></i><strong>' + esc(f.name) + '</strong></span></td>' +
      '<td>' + esc(f.size) + '</td><td>' + esc(f.mod) + '</td>' +
      '<td><div class="jpv-rowactions">' +
      iconBtn('bi-download', 'Download') +
      '<button type="button" class="danger jpv-iconbtn" data-file="' + esc(f.name) + '" title="Löschen"><i class="bi bi-trash3"></i></button>' +
      '</div></td></tr>'
    );
  }).join('');

  const body =
    '<div class="jpv-pagehead"><h1><i class="bi bi-folder" style="color:rgb(103 232 249)"></i> Files</h1>' +
    '<div class="jpv-pagehead__actions">' +
    '<button type="button" class="primary"><i class="bi bi-upload"></i> Hochladen</button>' +
    '<button type="button" class="secondary"><i class="bi bi-folder-plus"></i> Neuer Ordner</button>' +
    '</div></div>' +
    '<div class="bg-white jpv-card">' +
    '<div class="jpv-toolbar"><span class="jpv-crumb"><i class="bi bi-hdd-stack"></i> / <strong>' + esc(s.name) + '</strong></span>' +
    '<span class="jpv-spacer"></span>' + badge(files.length + ' Objekte') + '</div>' +
    '<table><thead><tr><th style="width:44px"></th><th>Name</th><th>Größe</th><th>Geändert</th><th style="width:110px"></th></tr></thead>' +
    '<tbody>' + rows + '</tbody></table>' +
    '<p class="jpv-hint">Dock unten: Alle/Keine auswählen · Shift+Klick = Bereich · Löschen mit Bestätigung (In-Memory-Demo).</p>' +
    '</div>';

  const pageScript =
    'document.querySelectorAll("[data-file]").forEach(function (b) {' +
    '  b.addEventListener("click", function () {' +
    '    var n = b.getAttribute("data-file");' +
    '    fetch("/api/client/servers/' + id + '/files/delete", { method: "DELETE", credentials: "same-origin", headers: { "Content-Type": "application/json", "Accept": "application/json" }, body: JSON.stringify({ root: "/", files: [n] }) })' +
    '      .then(function (r) { if (window.JomaTheme && r.ok) { window.JomaTheme.toast({ type: "success", title: "Dateien", message: n + " gelöscht." }); setTimeout(function () { location.reload(); }, 500); } });' +
    '  });' +
    '});';

  return serverShell({ server: s, tab: 'files', body, pageScript });
}

function backupsPage(id) {
  const s = byId(id);
  if (!s) return notFoundPage();
  const backups = [
    { name: 'auto-2026-09-04-0400.tar.gz', size: '1.2 GB', mod: '04.09.2026 04:00' },
    { name: 'pre-1.21.1-upgrade.tar.gz', size: '980 MB', mod: '30.08.2026 17:22' },
  ];
  const rows = backups.map((b) =>
    '<tr><td><span class="jpv-fname"><i class="bi bi-file-earmark-zip"></i><strong>' + esc(b.name) + '</strong></span></td>' +
    '<td>' + esc(b.size) + '</td><td>' + esc(b.mod) + '</td>' +
    '<td><div class="jpv-rowactions">' + iconBtn('bi-download', 'Download') + iconBtn('bi-trash3', 'Löschen', 'danger') + '</div></td></tr>'
  ).join('');
  const body =
    '<div class="jpv-pagehead"><h1><i class="bi bi-archive" style="color:rgb(103 232 249)"></i> Backups</h1>' +
    '<div class="jpv-pagehead__actions"><button type="button" class="primary"><i class="bi bi-plus-lg"></i> Backup erstellen</button></div></div>' +
    '<div class="bg-white jpv-card"><table><thead><tr><th>Name</th><th>Größe</th><th>Erstellt</th><th style="width:110px"></th></tr></thead><tbody>' + rows + '</tbody></table></div>';
  return serverShell({ server: s, tab: 'backups', body });
}

function schedulesPage(id) {
  const s = byId(id);
  if (!s) return notFoundPage();
  const rows = [
    ['<span class="jpv-fname"><i class="bi bi-arrow-clockwise"></i><strong>Täglicher Restart</strong></span>', '0 5 * * *', '05.09.2026 05:00', badge('aktiv')],
    ['<span class="jpv-fname"><i class="bi bi-database"></i><strong>Welt-Backup</strong></span>', '0 4 * * 3', '04.09.2026 04:00', badge('aktiv')],
  ].map((r) => '<tr><td>' + r[0] + '</td><td><code>' + r[1] + '</code></td><td>' + r[2] + '</td><td>' + r[3] + '</td></tr>').join('');
  const body =
    '<div class="jpv-pagehead"><h1><i class="bi bi-clock" style="color:rgb(103 232 249)"></i> Schedules</h1>' +
    '<div class="jpv-pagehead__actions"><button type="button" class="primary"><i class="bi bi-plus-lg"></i> Neuer Schedule</button></div></div>' +
    '<div class="bg-white jpv-card"><table><thead><tr><th>Name</th><th>Cron</th><th>Nächster Run</th><th>Status</th></tr></thead><tbody>' + rows + '</tbody></table></div>';
  return serverShell({ server: s, tab: 'schedules', body });
}

function usersPage(id) {
  const s = byId(id);
  if (!s) return notFoundPage();
  const rows = [
    ['<span class="jpv-fname"><span class="jpv-user__ava">L</span><strong>Lasse</strong></span>', 'lasse@jomamc.de', badge('Owner')],
    ['<span class="jpv-fname"><span class="jpv-user__ava">M</span><strong>Mia</strong></span>', 'mia@jomamc.de', badge('Subuser')],
  ].map((r) => '<tr><td>' + r[0] + '</td><td>' + r[1] + '</td><td>' + r[2] + '</td><td><div class="jpv-rowactions">' + iconBtn('bi-trash3', 'Entfernen', 'danger') + '</div></td></tr>').join('');
  const body =
    '<div class="jpv-pagehead"><h1><i class="bi bi-people" style="color:rgb(103 232 249)"></i> Users</h1>' +
    '<div class="jpv-pagehead__actions"><button type="button" class="primary"><i class="bi bi-person-plus"></i> User hinzufügen</button></div></div>' +
    '<div class="bg-white jpv-card"><table><thead><tr><th>Name</th><th>E-Mail</th><th>Rolle</th><th style="width:90px"></th></tr></thead><tbody>' + rows + '</tbody></table></div>';
  return serverShell({ server: s, tab: 'users', body });
}

function networkPage(id) {
  const s = byId(id);
  if (!s) return notFoundPage();
  const rows = [
    ['play.jomamc.de', '25565', 'Java — Survival', badge('Primary')],
    ['play.jomamc.de', '25566', 'Java — Skyblock', ''],
    ['bedrock.jomamc.de', '19132', 'Bedrock (Geyser)', ''],
  ].map((r) => '<tr><td><strong>' + r[0] + '</strong></td><td><code>' + r[1] + '</code></td><td>' + r[2] + '</td><td>' + r[3] + '</td></tr>').join('');
  const body =
    '<div class="jpv-pagehead"><h1><i class="bi bi-ethernet" style="color:rgb(103 232 249)"></i> Network</h1>' +
    '<div class="jpv-pagehead__actions"><button type="button" class="secondary"><i class="bi bi-plus-lg"></i> Allocation</button></div></div>' +
    '<div class="bg-white jpv-card"><table><thead><tr><th>Adresse</th><th>Port</th><th>Notiz</th><th></th></tr></thead><tbody>' + rows + '</tbody></table></div>';
  return serverShell({ server: s, tab: 'network', body });
}

function startupPage(id) {
  const s = byId(id);
  if (!s) return notFoundPage();
  const body =
    '<div class="jpv-pagehead"><h1><i class="bi bi-rocket-takeoff" style="color:rgb(103 232 249)"></i> Startup</h1></div>' +
    '<div class="jpv-grid">' +
    '<div class="bg-white jpv-card"><h2>Variables</h2>' +
    '<p><label style="display:block;margin-bottom:.3rem">SERVER_JVMFLAGS</label><input type="text" value="-Xms128M -XX:+UseG1GC" style="padding:.5rem .8rem;width:100%"></p>' +
    '<p><label style="display:block;margin-bottom:.3rem">MOTD</label><input type="text" value="JomaMC Survival — viel Spaß!" style="padding:.5rem .8rem;width:100%"></p>' +
    '<div style="margin-top:1rem"><button type="button" class="primary">Speichern</button></div></div>' +
    '<div class="bg-white jpv-card"><h2>Container</h2>' +
    '<div class="jpv-kv"><span>Egg</span><span>' + esc(s.egg) + '</span></div>' +
    '<div class="jpv-kv"><span>Docker Image</span><span>ghcr.io/pterodactyl/yolks:java_21</span></div>' +
    '<div class="jpv-kv"><span>Startup Command</span><span>java -jar server.jar</span></div>' +
    '<div class="jpv-kv"><span>RAM</span><span>' + esc(s.desc) + '</span></div>' +
    '</div></div>';
  return serverShell({ server: s, tab: 'startup', body });
}

function settingsPage(id) {
  const s = byId(id);
  if (!s) return notFoundPage();
  const body =
    '<div class="jpv-pagehead"><h1><i class="bi bi-gear" style="color:rgb(103 232 249)"></i> Settings</h1></div>' +
    '<div class="jpv-grid">' +
    '<div class="bg-white jpv-card"><h2>Allgemein</h2>' +
    '<p><label style="display:block;margin-bottom:.3rem">Servername</label><input type="text" value="' + esc(s.name) + '" style="padding:.5rem .8rem;width:100%"></p>' +
    '<p><label style="display:block;margin-bottom:.3rem">Beschreibung</label><input type="text" value="' + esc(s.egg) + ' · ' + esc(s.desc) + '" style="padding:.5rem .8rem;width:100%"></p>' +
    '<div style="margin-top:1rem"><button type="button" class="primary">Speichern</button></div></div>' +
    '<div class="bg-white jpv-card jpv-card--danger"><h2>Danger Zone</h2>' +
    '<p class="text-neutral-500" style="font-size:.85rem;margin:0 0 1rem">Reinstalliert den Server mit dem aktuellen Egg. Dateien werden zurückgesetzt.</p>' +
    '<button type="button" class="danger"><i class="bi bi-exclamation-triangle"></i> Server reinstallieren</button></div>' +
    '</div>';
  return serverShell({ server: s, tab: 'settings', body });
}

/* --------------------------------------------------------------------------
   Pages — account / admin / 404
   -------------------------------------------------------------------------- */
function accountPage() {
  const keys = [
    ['CI Deploy', 'joma_ci_****3f9a', '04.09.2026 12:30'],
    ['Backup Script', 'joma_bak_****71c2', '01.09.2026 03:00'],
  ].map((k) =>
    '<tr><td><strong>' + k[0] + '</strong></td><td><code>' + k[1] + '</code></td><td>' + k[2] + '</td>' +
    '<td><div class="jpv-rowactions">' + iconBtn('bi-trash3', 'Widerrufen', 'danger') + '</div></td></tr>'
  ).join('');

  const body =
    '<div class="jpv-container">' +
    '<div class="jpv-pagehead"><h1><i class="bi bi-person" style="color:rgb(103 232 249)"></i> Account</h1></div>' +
    '<div class="jpv-grid">' +
    '<div class="bg-white jpv-card"><h2>Profil</h2>' +
    '<p><label style="display:block;margin-bottom:.3rem">Benutzername</label><input type="text" value="Lasse" style="padding:.5rem .8rem;width:100%"></p>' +
    '<p><label style="display:block;margin-bottom:.3rem">E-Mail</label><input type="email" value="lasse@jomamc.de" style="padding:.5rem .8rem;width:100%"></p>' +
    '<p><label style="display:block;margin-bottom:.3rem">Neues Passwort</label><input type="password" placeholder="••••••••" style="padding:.5rem .8rem;width:100%"></p>' +
    '<div style="display:flex;gap:.5rem;margin-top:1.2rem"><button type="button" class="primary">Speichern</button><button type="button" class="secondary">Abbrechen</button></div></div>' +
    '<div class="bg-white jpv-card"><h2>API Keys</h2>' +
    '<table><thead><tr><th>Name</th><th>Token</th><th>Zuletzt genutzt</th><th style="width:90px"></th></tr></thead><tbody>' + keys + '</tbody></table>' +
    '<div style="margin-top:1rem"><button type="button" class="primary"><i class="bi bi-plus-lg"></i> Key erstellen</button></div></div>' +
    '</div></div>';

  return shell({ title: 'Account', active: '/account', body });
}

function adminPage() {
  const a = readAdminPage();
  return [
    '<!doctype html><html lang="de"><head><meta charset="utf-8">',
    '<meta name="viewport" content="width=device-width, initial-scale=1">',
    '<title>Admin · JomaTheme Preview</title>',
    '<style>' + a.css + '</style>',
    '</head><body style="padding:1.5rem 1rem">',
    a.body,
    '</body></html>',
  ].join('\n');
}

function notFoundPage() {
  const body =
    '<div class="jpv-container"><div class="bg-white jpv-card" style="max-width:560px;margin:3rem auto;text-align:center">' +
    '<div class="jpv-empty" style="padding:1.5rem"><i class="bi bi-compass"></i></div>' +
    '<h2 style="margin:0 0 .4rem">404 — nicht gefunden</h2>' +
    '<p class="text-neutral-500" style="margin:0 0 1.2rem">Diese Route gibt es in der Preview nicht.</p>' +
    '<a class="btn-primary" href="/" style="padding:.5rem 1.2rem;text-decoration:none;display:inline-block">Zum Dashboard</a>' +
    '</div></div>';
  return shell({ title: '404', active: '', body });
}

/* --------------------------------------------------------------------------
   Server + router
   -------------------------------------------------------------------------- */
function json404(res) {
  res.writeHead(404, { 'Content-Type': 'application/json' });
  res.end(JSON.stringify({ error: 'Not found' }));
}

const TAB_ROUTES = {
  files: filesPage, backups: backupsPage, schedules: schedulesPage, users: usersPage,
  network: networkPage, startup: startupPage, settings: settingsPage,
};

const server = http.createServer((req, res) => {
  const u = new URL(req.url, 'http://localhost');
  const p = decodeURIComponent(u.pathname);

  if (req.method === 'POST' && /^\/api\/client\/servers\/([^/]+)\/power$/.test(p)) {
    res.writeHead(204);
    return res.end();
  }

  if (req.method === 'DELETE' && /^\/api\/client\/servers\/([^/]+)\/files\/delete$/.test(p)) {
    const id = p.match(/^\/api\/client\/servers\/([^/]+)\//)[1];
    let body = '';
    req.on('data', (c) => { body += c; });
    req.on('end', () => {
      try {
        const j = JSON.parse(body || '{}');
        (j.files || []).forEach((n) => {
          if (filesByServer[id]) filesByServer[id] = filesByServer[id].filter((f) => f.name !== n);
        });
      } catch (e) { /* ignore malformed demo payloads */ }
      res.writeHead(204);
      res.end();
    });
    return;
  }

  if (req.method !== 'GET') { return json404(res); }

  let html;
  let status = 200;
  let m;
  if (p === '/' || p === '/index.html') html = dashboardPage();
  else if (p === '/account' || p.indexOf('/account/') === 0) html = accountPage();
  else if (p === '/admin/extensions/jomatheme') html = adminPage();
  else if ((m = p.match(/^\/server\/([^/]+)\/([a-z]+)$/)) && TAB_ROUTES[m[2]] && byId(m[1])) html = TAB_ROUTES[m[2]](m[1]);
  else if ((m = p.match(/^\/server\/([^/]+)$/)) && byId(m[1])) html = consolePage(m[1]);
  else { html = notFoundPage(); status = 404; }

  res.writeHead(status, { 'Content-Type': 'text/html; charset=utf-8' });
  res.end(html);
});

function listenAt(port, tries) {
  server.once('error', (e) => {
    if (e.code === 'EADDRINUSE' && tries > 0) {
      console.log('Port ' + port + ' belegt — versuche ' + (port + 1) + ' …');
      listenAt(port + 1, tries - 1);
    } else {
      console.error('Preview-Server konnte nicht starten: ' + e.message);
      process.exit(1);
    }
  });
  server.listen(port, '127.0.0.1', () => {
    const url = 'http://localhost:' + port;
    console.log('JomaTheme preview: ' + url);
    console.log('Seiten:  /  ·  /server/a1b2c3d4  ·  /server/a1b2c3d4/files  ·  …/backups|schedules|users|network|startup|settings  ·  /account  ·  /admin/extensions/jomatheme');
    console.log('Theme-Dateien werden live gelesen — editieren + Browser-Refresh genügt. Beenden: Ctrl+C');
    if (!NO_OPEN) {
      const cmd = process.platform === 'win32' ? 'start "" "' + url + '"'
        : process.platform === 'darwin' ? 'open "' + url + '"' : 'xdg-open "' + url + '"';
      exec(cmd, () => {});
    }
  });
}

listenAt(BASE_PORT, 10);
