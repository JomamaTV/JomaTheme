#!/usr/bin/env node
'use strict';
/* ============================================================================
   JomaTheme local preview — zero-dependency Pterodactyl mock server.
   ----------------------------------------------------------------------------
   Serves a static impression of the panel (dashboard, server console, file
   manager with the working selection dock, account + admin page) using the
   REAL theme files, read live on every request: edit dashboard.css /
   wrapper.blade.php / admin.css / view.blade.php and just refresh the browser.

   Mock API endpoints make the interactive parts work:
     POST   /api/client/servers/:id/power         -> 204 (fires theme toasts)
     DELETE /api/client/servers/:id/files/delete  -> 204 (in-memory delete)

   Usage:  node preview/server.js [port] [--no-open]
   Default port: 7575. Binds to 127.0.0.1 only. Files reset on restart.
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
  { id: 'a1b2c3d4', name: 'Survival-01', desc: 'Paper 1.21.1 · 8 GB RAM', state: 'running', cpu: 34, mem: 61, disk: 47 },
  { id: 'b2c3d4e5', name: 'Skyblock-02', desc: 'Paper 1.21.1 · 4 GB RAM', state: 'running', cpu: 12, mem: 40, disk: 23 },
  { id: 'c3d4e5f6', name: 'Proxy-Velocity', desc: 'Velocity 3.4 · Java 25 · 1 GB RAM', state: 'offline', cpu: 0, mem: 0, disk: 8 },
  { id: 'd4e5f6a7', name: 'Bedwars-Arena', desc: 'Paper 1.20.4 · 6 GB RAM', state: 'starting', cpu: 8, mem: 22, disk: 31 },
];
const seedFiles = () => [
  { name: 'plugins', size: '—', mod: '04.09.2026 14:02' },
  { name: 'world', size: '—', mod: '04.09.2026 16:45' },
  { name: 'logs', size: '—', mod: '04.09.2026 16:50' },
  { name: 'server.properties', size: '2 KB', mod: '03.09.2026 09:12' },
  { name: 'paper-1.21.1.jar', size: '48 MB', mod: '28.08.2026 18:30' },
  { name: 'eula.txt', size: '1 KB', mod: '27.08.2026 12:00' },
];
const filesByServer = {};
servers.forEach((s) => { filesByServer[s.id] = seedFiles(); });

/* --------------------------------------------------------------------------
   Live theme loading (re-read on every request)
   -------------------------------------------------------------------------- */
function readTheme() {
  const css = fs.readFileSync(path.join(ROOT, 'dashboard.css'), 'utf8');
  const wrapper = fs.readFileSync(path.join(ROOT, 'wrapper.blade.php'), 'utf8');
  const style = (wrapper.match(/<style>([\s\S]*?)<\/style>/) || [])[1] || '';
  const scripts = [];
  const re = /<script>([\s\S]*?)<\/script>/g;
  let m;
  while ((m = re.exec(wrapper))) scripts.push(m[1]);
  return { css, style, scripts };
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
   Page shell
   -------------------------------------------------------------------------- */
function nav(active) {
  const link = (href, label) =>
    '<a href="' + href + '"' + (href === active ? ' aria-current="page"' : '') +
    ' style="padding:.45rem .85rem;text-decoration:none">' + label + '</a>';
  return (
    '<nav id="NavigationBar" style="display:flex;gap:.4rem;padding:.7rem 1.2rem;align-items:center">' +
    '<strong style="margin-right:1.2rem;font-size:1.05rem">JomaMC</strong>' +
    link('/', 'Dashboard') +
    link('/server/a1b2c3d4', 'Survival-01') +
    link('/server/a1b2c3d4/files', 'Files') +
    link('/account', 'Account') +
    link('/admin/extensions/jomatheme', 'Admin') +
    '<span style="flex:1"></span>' +
    '<button class="secondary" style="padding:.45rem 1rem" onclick="location.reload()">Reload</button>' +
    '</nav>'
  );
}

function shell(opts) {
  const t = readTheme();
  return [
    '<!doctype html><html lang="de"><head><meta charset="utf-8">',
    '<meta name="viewport" content="width=device-width, initial-scale=1">',
    '<title>' + esc(opts.title) + ' · JomaTheme Preview</title>',
    '<style>' + t.css + '</style>',
    '<style>' + t.style + '</style>',
    '</head><body>',
    '<div id="jomatheme-progress" aria-hidden="true"><div id="jomatheme-progress__bar"></div></div>',
    '<div id="jomatheme-toasts" aria-live="polite" aria-atomic="false"></div>',
    nav(opts.active),
    '<main style="max-width:1120px;margin:1.4rem auto;padding:0 1rem">' + opts.body + '</main>',
    '<footer style="display:block;text-align:center;padding:2rem 1rem;font-size:.78rem">',
    'JomaTheme v4.1 local preview — theme files are read <strong>live</strong>, just refresh. ',
    'Mock deletions are in-memory and reset on server restart.',
    '</footer>',
    '<script>window.PterodactylUser = { username: "Lasse", first_name: "Lasse", root_admin: true };</script>',
    '<script>' + opts.pageScript + '</script>',
  ].concat(t.scripts.map((s) => '<script>' + s + '</script>'))
    .concat(['</body></html>'])
    .join('\n');
}

/* --------------------------------------------------------------------------
   Pages
   -------------------------------------------------------------------------- */
function dashboardPage() {
  const cards = servers.map((s) => {
    const dot = s.state === 'running' ? 'joma-dot--online' : s.state === 'starting' ? 'joma-dot--starting' : 'joma-dot--offline';
    const badge = s.state === 'running' ? 'online' : s.state;
    return (
      '<div class="ServerCard" style="padding:1.15rem 1.25rem">' +
      '<div style="display:flex;align-items:center;gap:.6rem"><span class="joma-dot ' + dot + '"></span>' +
      '<h3 style="margin:0">' + esc(s.name) + '</h3>' +
      '<span class="Badge" style="padding:.22rem .7rem;margin-left:auto">' + badge + '</span></div>' +
      '<p class="text-neutral-500" style="margin:.45rem 0 .9rem">' + esc(s.desc) + '</p>' +
      '<div class="progress" style="height:8px;margin-bottom:1rem"><div class="progress-bar" style="width:' + s.cpu + '%"></div></div>' +
      '<a class="btn-primary" href="/server/' + s.id + '" style="padding:.45rem 1rem;text-decoration:none;display:inline-block">Console</a> ' +
      '<a class="btn-secondary" href="/server/' + s.id + '/files" style="padding:.45rem 1rem;text-decoration:none;display:inline-block">Files</a>' +
      '</div>'
    );
  }).join('\n');

  const body =
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
    '<a class="jomatheme-welcome__btn jomatheme-welcome__btn--primary" href="/server/a1b2c3d4/files">Dateien öffnen</a>' +
    '<a class="jomatheme-welcome__btn" href="/account">Account</a>' +
    '</div></div></div>' +
    '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem">' + cards + '</div>' +
    '<div class="bg-white" style="padding:1.15rem 1.25rem;margin-top:1rem;display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">' +
    '<input type="text" placeholder="Server suchen&hellip;" style="padding:.5rem .8rem;width:240px">' +
    '<button class="secondary" style="padding:.45rem 1rem">Filter</button>' +
    '<span class="Badge" style="padding:.25rem .7rem">4 Server</span>' +
    '<span class="Badge" style="padding:.25rem .7rem">2 online</span>' +
    '</div>';

  return shell({ title: 'Dashboard', active: '/', body, pageScript: '' });
}

function consolePage(id) {
  const s = servers.find((x) => x.id === id);
  if (!s) return notFoundPage();
  const stat = (label, val) =>
    '<div class="joma-stat"><p class="joma-stat__label">' + label + '</p><p class="joma-stat__value">' + val + '</p>' +
    '<div class="progress" style="height:8px;margin-top:.6rem"><div class="progress-bar" style="width:' + val + '%"></div></div></div>';

  const lines = [
    '[16:44:02 INFO]: Starting minecraft server version 1.21.1',
    '[16:44:03 INFO]: Loading properties',
    '[16:44:05 INFO]: Preparing level "world"',
    '[16:44:11 INFO]: Done (6.312s)! For help, type "help"',
    '[16:44:32 INFO]: Lasse joined the game',
    '[16:45:10 INFO]: [EssentialsX]AFK: toggled for Lasse',
  ].map((l) => '<div style="padding:.1rem 0">' + esc(l) + '</div>').join('');

  const body =
    '<div class="bg-white" style="padding:1.15rem 1.25rem;margin-bottom:1rem;display:flex;align-items:center;gap:.8rem;flex-wrap:wrap">' +
    '<span class="joma-dot joma-dot--online"></span><h2 style="margin:0">' + esc(s.name) + '</h2>' +
    '<span class="Badge" style="padding:.25rem .7rem">' + esc(s.state) + '</span>' +
    '<span style="flex:1"></span>' +
    '<button class="primary" data-power="start" style="padding:.45rem 1rem">Start</button>' +
    '<button class="secondary" data-power="restart" style="padding:.45rem 1rem">Restart</button>' +
    '<button data-power="stop" style="padding:.45rem 1rem">Stop</button>' +
    '<button class="danger" data-power="kill" style="padding:.45rem 1rem">Kill</button>' +
    '</div>' +
    '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;margin-bottom:1rem">' +
    stat('CPU', s.cpu) + stat('RAM', s.mem) + stat('Disk', s.disk) + '</div>' +
    '<div style="position:relative">' +
    '<div class="terminal" style="height:340px;overflow-y:auto;padding:1rem 1.1rem;font-family:ui-monospace,Menlo,Consolas,monospace;font-size:.82rem;line-height:1.55">' + lines + '</div>' +
    '</div>';

  const pageScript =
    'document.querySelectorAll("[data-power]").forEach(function (b) {' +
    '  b.addEventListener("click", function () {' +
    '    fetch("/api/client/servers/' + id + '/power", { method: "POST", headers: { "Content-Type": "application/json", "Accept": "application/json" }, body: JSON.stringify({ signal: b.dataset.power }) });' +
    '  });' +
    '});';

  return shell({ title: s.name, active: '/server/' + id, body, pageScript });
}

function filesPage(id) {
  const s = servers.find((x) => x.id === id);
  if (!s) return notFoundPage();
  const files = filesByServer[id] || [];
  const rows = files.map((f) =>
    '<tr><td><input type="checkbox"></td>' +
    '<td><strong>' + esc(f.name) + '</strong></td>' +
    '<td>' + esc(f.size) + '</td><td>' + esc(f.mod) + '</td></tr>'
  ).join('');

  const body =
    '<div class="bg-white" style="padding:1.25rem">' +
    '<div style="display:flex;align-items:center;gap:.7rem;margin-bottom:1rem;flex-wrap:wrap">' +
    '<h2 style="margin:0;font-size:1.15rem">/ &middot; ' + esc(s.name) + '</h2>' +
    '<span class="Badge" style="padding:.25rem .7rem">' + files.length + ' Objekte</span>' +
    '<span style="flex:1"></span>' +
    '<button class="primary" style="padding:.45rem 1rem">Hochladen</button>' +
    '<button class="secondary" style="padding:.45rem 1rem">Neuer Ordner</button>' +
    '</div>' +
    '<table><thead><tr><th style="width:44px"></th><th>Name</th><th>Größe</th><th>Geändert</th></tr></thead>' +
    '<tbody>' + rows + '</tbody></table>' +
    '<p style="font-size:.78rem;color:rgb(122 152 178);margin:.9rem 0 0">Dock unten: Alle/Keine auswählen, Shift+Klick = Bereich, Löschen mit Bestätigung (In-Memory-Demo).</p>' +
    '</div>';

  return shell({ title: 'Files', active: '/server/' + id + '/files', body, pageScript: '' });
}

function accountPage() {
  const body =
    '<div class="bg-white" style="padding:1.4rem 1.5rem;max-width:640px">' +
    '<h2 style="margin:0 0 1rem">Account</h2>' +
    '<p><label style="display:block;margin-bottom:.3rem">Benutzername</label>' +
    '<input type="text" value="Lasse" style="padding:.5rem .8rem;width:100%"></p>' +
    '<p><label style="display:block;margin-bottom:.3rem">E-Mail</label>' +
    '<input type="email" value="lasse@jomamc.de" style="padding:.5rem .8rem;width:100%"></p>' +
    '<p><label style="display:block;margin-bottom:.3rem">Neues Passwort</label>' +
    '<input type="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" style="padding:.5rem .8rem;width:100%"></p>' +
    '<div style="display:flex;gap:.6rem;margin-top:1.2rem">' +
    '<button class="primary" style="padding:.5rem 1.2rem">Speichern</button>' +
    '<button class="secondary" style="padding:.5rem 1.2rem">Abbrechen</button></div>' +
    '</div>';

  return shell({ title: 'Account', active: '/account', body, pageScript: '' });
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
    '<div class="bg-white" style="padding:2rem;text-align:center">' +
    '<h2 style="margin:0 0 .5rem">404</h2>' +
    '<p class="text-neutral-500">Diese Preview-Route existiert nicht (echte Panel-Unterseiten werden hier nicht nachgebaut).</p>' +
    '<p><a class="btn-primary" href="/" style="padding:.5rem 1.2rem;text-decoration:none;display:inline-block">Zum Dashboard</a></p>' +
    '</div>';
  return shell({ title: '404', active: '', body, pageScript: '' });
}

/* --------------------------------------------------------------------------
   Server + router
   -------------------------------------------------------------------------- */
function json404(res) {
  res.writeHead(404, { 'Content-Type': 'application/json' });
  res.end(JSON.stringify({ error: 'Not found' }));
}

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
  if (p === '/' || p === '/index.html') html = dashboardPage();
  else if (p === '/account' || p.indexOf('/account/') === 0) html = accountPage();
  else if (p === '/admin/extensions/jomatheme') html = adminPage();
  else if (/^\/server\/([^/]+)\/files/.test(p)) html = filesPage(p.match(/^\/server\/([^/]+)\//)[1]);
  else if (/^\/server\/([^/]+)$/.test(p)) html = consolePage(p.match(/^\/server\/([^/]+)$/)[1]);
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
    console.log('Seiten:  /  ·  /server/a1b2c3d4  ·  /server/a1b2c3d4/files  ·  /account  ·  /admin/extensions/jomatheme');
    console.log('Theme-Dateien werden live gelesen — editieren + Browser-Refresh genügt. Beenden: Ctrl+C');
    if (!NO_OPEN) {
      const cmd = process.platform === 'win32' ? 'start "" "' + url + '"'
        : process.platform === 'darwin' ? 'open "' + url + '"' : 'xdg-open "' + url + '"';
      exec(cmd, () => {});
    }
  });
}

listenAt(BASE_PORT, 10);
