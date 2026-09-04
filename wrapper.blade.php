@verbatim
<style>
/* ============================================================================
   JomaTheme — runtime animations & enhancements
   Served as a <style> tag via the Blueprint dashboard wrapper (every page).
   Heavy keyframes live here so they can never break the React CSS bundle.
   v4.1 — midnight marine / cyan-teal-sky / liquid glass / file-manager dock
   ============================================================================ */

/* ---- web fonts: Inter (UI) + Bootstrap Icons ---- */
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap");
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css");

/* ---- ambient background: subtle sea-bubble grid + deep-sea caustics ---- */
body::before {
  content: "";
  position: fixed; inset: 0; z-index: -2; pointer-events: none;
  background-image: radial-gradient(rgb(103 232 249 / 0.05) 1px, transparent 1px);
  background-size: 30px 30px;
  opacity: 0.4;
}
body::after {
  content: "";
  position: fixed; inset: -10%; z-index: -1; pointer-events: none;
  background:
    radial-gradient(42% 46% at 12% 12%, rgb(6 182 212 / 0.11), transparent 70%),
    radial-gradient(40% 45% at 88% 10%, rgb(14 165 233 / 0.10), transparent 70%),
    radial-gradient(46% 48% at 55% 112%, rgb(45 212 191 / 0.08), transparent 70%);
  filter: blur(6px);
  opacity: 0.75;
  animation: joma-ambient 36s ease-in-out infinite alternate;
}

/* ============================================================================
   Keyframes (referenced by dashboard.css + components below)
   ============================================================================ */
@keyframes joma-aurora-drift {
  0%   { transform: translate3d(-4%, -2%, 0) scale(1.05); }
  50%  { transform: translate3d(6%, 4%, 0) scale(1.12); }
  100% { transform: translate3d(-4%, -2%, 0) scale(1.05); }
}
@keyframes joma-ambient {
  0%   { transform: translate3d(-2%, -1%, 0) scale(1.04); }
  50%  { transform: translate3d(3%, 3%, 0) scale(1.08); }
  100% { transform: translate3d(-2%, -1%, 0) scale(1.04); }
}
@keyframes joma-status-pulse {
  0%, 100% { box-shadow: 0 0 0 0 currentColor; opacity: 1; }
  50%      { box-shadow: 0 0 0 5px transparent; opacity: 0.85; }
}
@keyframes joma-hero-gradient {
  0%, 100% { background-position: 0% 50%; }
  50%      { background-position: 100% 50%; }
}
@keyframes joma-twinkle {
  0%, 100% { opacity: 0.55; transform: scale(0.92) rotate(0deg); }
  50%      { opacity: 1;    transform: scale(1.12) rotate(12deg); }
}
@keyframes joma-modal-in {
  from { opacity: 0; transform: translateY(8px) scale(0.98); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes joma-pulse {
  0%, 100% { box-shadow: 0 0 0 0 rgb(34 197 94 / 0.5); }
  50%      { box-shadow: 0 0 0 6px rgb(34 197 94 / 0); }
}
@keyframes joma-wave {
  0%, 60%, 100% { transform: rotate(0deg); }
  10%           { transform: rotate(14deg); }
  20%           { transform: rotate(-8deg); }
  30%           { transform: rotate(14deg); }
  40%           { transform: rotate(-4deg); }
  50%           { transform: rotate(10deg); }
}
@keyframes joma-shimmer { 100% { transform: translateX(100%); } }
@keyframes joma-spin { to { transform: rotate(360deg); } }
@keyframes joma-slide-up {
  0%   { opacity: 0; transform: translate3d(0, 18px, 0); filter: blur(6px); }
  100% { opacity: 1; transform: translate3d(0, 0, 0); filter: blur(0); }
}
@keyframes joma-fade-up { 0% { opacity: 0; transform: translate3d(0, 12px, 0); } 100% { opacity: 1; transform: translate3d(0, 0, 0); } }
@keyframes joma-toast-in { 0% { opacity: 0; transform: translate3d(120%, 0, 0) scale(0.96); } 100% { opacity: 1; transform: translate3d(0, 0, 0) scale(1); } }
@keyframes joma-toast-out { 0% { opacity: 1; transform: translate3d(0, 0, 0) scale(1); } 100% { opacity: 0; transform: translate3d(120%, 0, 0) scale(0.96); } }
@keyframes joma-toast-bar { from { transform: scaleX(1); } to { transform: scaleX(0); } }
@keyframes joma-gradient-shift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
@keyframes joma-liquid-edge { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
@keyframes joma-rise { 0% { opacity: 0; transform: translate3d(0, 16px, 0); } 100% { opacity: 1; transform: translate3d(0, 0, 0); } }
@keyframes joma-fade { from { opacity: 0; } to { opacity: 1; } }
@keyframes joma-cmdk-in { from { opacity: 0; transform: translateY(-12px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes joma-ripple { to { transform: scale(2.8); opacity: 0; } }

/* ---- whole-app entrance (runs once on full page load) ---- */
#app { animation: joma-fade-up 0.6s cubic-bezier(0.22, 1, 0.36, 1) both; }

/* ---- glass-surface hover polish (skip server cards — they have their own
   hover treatment; animating box-shadow on a backdrop-filter element causes a
   costly blur repaint that flickers "through" the card) ---- */
.bg-white, [class*="bg-white"] {
  transition: border-color 0.2s ease, filter 0.2s ease;
}
.bg-white:not([class*="ServerCard"]):not([class*="server-card"]):not([class*="ServerRow"]):hover,
[class*="bg-white"]:not([class*="ServerCard"]):not([class*="server-card"]):not([class*="ServerRow"]):hover {
  border-color: rgb(6 182 212 / 0.32) !important;
  filter: brightness(1.04);
}

/* ---- primary button gloss sweep on hover (subtle, symmetric) ---- */
button[class*="primary"]::after, .btn-primary::after, [class*="PrimaryButton"]::after {
  content: ""; position: absolute; inset: 0; pointer-events: none; border-radius: inherit;
  background: linear-gradient(110deg, transparent 38%, rgb(255 255 255 / 0.18) 50%, transparent 62%);
  transform: translateX(-130%); opacity: 0;
  transition: transform 0.45s ease, opacity 0.2s ease;
}
button[class*="primary"], .btn-primary, [class*="PrimaryButton"] { position: relative; overflow: hidden; }
button[class*="primary"]:not(:disabled):hover::after, .btn-primary:hover::after, [class*="PrimaryButton"]:not(:disabled):hover::after {
  transform: translateX(130%); opacity: 1;
  transition: transform 0.45s ease, opacity 0.15s ease;
}

/* ---- top loading bar ---- */
#jomatheme-progress {
  position: fixed; bottom: 0; left: 0; right: 0; height: 2px; z-index: 99998;
  pointer-events: none; opacity: 0; transition: opacity 0.3s ease;
}
#jomatheme-progress.is-loading { opacity: 1; }
#jomatheme-progress__bar {
  height: 100%; width: 100%; transform-origin: left center; transform: translateX(-100%);
  background: linear-gradient(90deg, rgb(14 165 233), rgb(6 182 212), rgb(45 212 191));
  box-shadow: 0 0 12px rgb(6 182 212 / 0.8);
  transition: transform 0.25s ease;
}
#jomatheme-progress.is-loading #jomatheme-progress__bar { transform: translateX(-30%); }
#jomatheme-progress.is-done #jomatheme-progress__bar { transform: translateX(0%); }

/* ---- staggered rise utility ---- */
.jomatheme-rise { animation: joma-rise 0.3s cubic-bezier(0.22, 1, 0.36, 1) both; opacity: 0; }
.jomatheme-rise:nth-child(1) { animation-delay: 0ms; }
.jomatheme-rise:nth-child(2) { animation-delay: 30ms; }
.jomatheme-rise:nth-child(3) { animation-delay: 60ms; }
.jomatheme-rise:nth-child(4) { animation-delay: 90ms; }
.jomatheme-rise:nth-child(5) { animation-delay: 120ms; }
.jomatheme-rise:nth-child(6) { animation-delay: 150ms; }
.jomatheme-rise:nth-child(7) { animation-delay: 180ms; }
.jomatheme-rise:nth-child(8) { animation-delay: 210ms; }

/* ============================================================================
   JomaTheme runtime UI — ripples · command palette · tooltips
   ============================================================================ */
:root {
  --joma-radius-sm: 14px; --joma-radius-md: 18px; --joma-radius-lg: 24px;
  --joma-radius-xl: 28px; --joma-radius-modal: 28px;
  --joma-blur-sm: 10px; --joma-blur-md: 16px; --joma-blur-lg: 26px;
  --joma-transition-fast: 120ms; --joma-transition-normal: 200ms;
}

/* button foundation (enables ripple + shine) */
button, .btn, [class*="Button"], a[class*="btn"], [role="button"] { position: relative; overflow: hidden; }

/* click ripple (spawned by JS at the cursor) */
.jomatheme-ripple {
  position: absolute; border-radius: 50%; pointer-events: none;
  transform: scale(0); opacity: 0.9; z-index: 0;
  background: radial-gradient(circle, rgb(255 255 255 / 0.55), transparent 72%);
  animation: joma-ripple 0.62s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}
button[class*="primary"] .jomatheme-ripple, .btn-primary .jomatheme-ripple, [class*="PrimaryButton"] .jomatheme-ripple {
  background: radial-gradient(circle, rgb(255 255 255 / 0.75), transparent 72%);
}

/* icon nudge inside buttons on hover */
button:not(:disabled):hover svg, .btn:hover svg, [class*="Button"]:not(:disabled):hover svg {
  transform: translateX(2px); transition: transform var(--joma-transition-normal) ease;
}

/* gradient-border utility */
.jomatheme-grad-border { position: relative; border: 1px solid transparent !important; background-clip: padding-box; }
.jomatheme-grad-border::before {
  content: ""; position: absolute; inset: 0; z-index: -1; border-radius: inherit;
  padding: 1px; margin: -1px; pointer-events: none;
  background: linear-gradient(135deg, rgb(6 182 212), rgb(45 212 191), rgb(14 165 233));
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor; mask-composite: exclude;
}

/* tooltip utility */
.jomatheme-tip { position: relative; }
.jomatheme-tip::after {
  content: attr(data-joma-tip); position: absolute; left: 50%; bottom: calc(100% + 8px);
  transform: translateX(-50%) translateY(4px); z-index: 50;
  background: rgb(8 22 38 / 0.94); color: rgb(226 242 250);
  border: 1px solid rgb(255 255 255 / 0.1); border-radius: 8px;
  padding: .25rem .5rem; font-size: .72rem; white-space: nowrap;
  opacity: 0; pointer-events: none;
  transition: opacity var(--joma-transition-fast) ease, transform var(--joma-transition-fast) ease;
}
.jomatheme-tip:hover::after { opacity: 1; transform: translateX(-50%) translateY(0); }

/* command palette (Ctrl + K) */
#jomatheme-cmdk { position: fixed; inset: 0; z-index: 99997; display: none; }
#jomatheme-cmdk.is-open { display: block; }
.jomatheme-cmdk__backdrop {
  position: absolute; inset: 0; background: rgb(3 9 18 / 0.6);
  backdrop-filter: blur(8px) saturate(120%); -webkit-backdrop-filter: blur(8px) saturate(120%);
  animation: joma-fade 0.2s ease both;
}
.jomatheme-cmdk__panel {
  position: relative; width: calc(100% - 2rem); max-width: 38rem; margin: 12vh auto 0;
  background: linear-gradient(135deg, rgb(13 34 54 / 0.92), rgb(9 26 42 / 0.95));
  backdrop-filter: blur(var(--joma-blur-lg)) saturate(180%); -webkit-backdrop-filter: blur(var(--joma-blur-lg)) saturate(180%);
  border: 1px solid rgb(255 255 255 / 0.1); border-radius: var(--joma-radius-modal);
  box-shadow: 0 30px 80px rgb(0 0 0 / 0.6), 0 0 60px rgb(6 182 212 / 0.08), 0 0 0 1px rgb(6 182 212 / 0.16), inset 0 1px 0 rgb(255 255 255 / 0.1);
  overflow: hidden; animation: joma-cmdk-in 0.32s cubic-bezier(0.34,1.56,0.64,1) both;
}
.jomatheme-cmdk__head { display: flex; align-items: center; gap: .6rem; padding: .9rem 1rem; border-bottom: 1px solid rgb(255 255 255 / 0.07); }
.jomatheme-cmdk__icon { color: rgb(34 211 238); font-size: 1.05rem; }
.jomatheme-cmdk__input {
  flex: 1; background: transparent !important; border: 0 !important; box-shadow: none !important;
  color: rgb(226 242 250) !important; font-size: 1rem; outline: none !important;
}
.jomatheme-cmdk__input::placeholder { color: rgb(100 132 158) !important; }
.jomatheme-cmdk__kbd {
  font-size: .68rem; color: rgb(122 152 178); border: 1px solid rgb(255 255 255 / 0.12);
  border-radius: 6px; padding: .1rem .35rem; background: rgb(255 255 255 / 0.04);
}
.jomatheme-cmdk__list { max-height: 22rem; overflow-y: auto; padding: .4rem; }
.jomatheme-cmdk__group { font-size: .66rem; text-transform: uppercase; letter-spacing: .1em; color: rgb(100 132 158); padding: .6rem .6rem .3rem; }
.jomatheme-cmdk__item {
  display: flex; align-items: center; gap: .65rem; padding: .55rem .6rem; border-radius: var(--joma-radius-sm);
  color: rgb(192 214 230); cursor: pointer; border: 1px solid transparent;
  transition: background var(--joma-transition-fast) ease, border-color var(--joma-transition-fast) ease, transform var(--joma-transition-fast) ease, color var(--joma-transition-fast) ease;
}
.jomatheme-cmdk__item.is-active {
  background: linear-gradient(135deg, rgb(6 182 212 / 0.24), rgb(45 212 191 / 0.1));
  border-color: rgb(6 182 212 / 0.38); color: rgb(240 250 253); transform: translateX(2px);
}
.jomatheme-cmdk__item-icon { width: 1.2rem; text-align: center; color: rgb(34 211 238); }
.jomatheme-cmdk__item-label { flex: 1; }
.jomatheme-cmdk__item-hint { font-size: .7rem; color: rgb(100 132 158); }
.jomatheme-cmdk__empty { padding: 1.4rem; text-align: center; color: rgb(122 152 178); font-size: .85rem; }

/* command palette hint (bottom-left) */
#jomatheme-cmdk-hint {
  position: fixed; left: 1.25rem; bottom: 1.25rem; z-index: 99990;
  display: inline-flex; align-items: center; gap: .4rem;
  padding: .35rem .6rem; border-radius: 8px; cursor: pointer;
  background: rgb(10 28 46 / 0.78); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgb(255 255 255 / 0.1); color: rgb(192 214 230); font-size: .72rem;
  box-shadow: 0 8px 24px rgb(0 0 0 / 0.35);
  transition: border-color var(--joma-transition-normal) ease, transform var(--joma-transition-normal) ease, color var(--joma-transition-normal) ease;
}
#jomatheme-cmdk-hint:hover { border-color: rgb(6 182 212 / 0.5); color: rgb(240 250 253); transform: translateY(-1px); }
#jomatheme-cmdk-hint kbd { font-size: .66rem; border: 1px solid rgb(255 255 255 / 0.14); border-radius: 5px; padding: .05rem .3rem; background: rgb(255 255 255 / 0.06); color: rgb(34 211 238); }
@media (max-width: 640px) { #jomatheme-cmdk-hint { display: none; } }

/* ============================================================================
   File manager — always-visible selection checkboxes + selection dock
   (body.joma-files is toggled by JS on /server/<id>/files pages only)
   ============================================================================ */
body.joma-files table td:first-child,
body.joma-files table th:first-child { opacity: 1 !important; }
body.joma-files input[type="checkbox"] {
  opacity: 1 !important; visibility: visible !important;
  width: 1.15em; height: 1.15em;
}

#jomatheme-filebar {
  position: fixed; left: 50%; bottom: 1.1rem; transform: translateX(-50%);
  z-index: 99989; display: none; align-items: center; gap: .5rem;
  padding: .55rem .8rem; border-radius: var(--joma-radius-pill);
  background: linear-gradient(135deg, rgb(13 34 54 / 0.92), rgb(9 26 42 / 0.95));
  backdrop-filter: blur(var(--joma-blur-md)) saturate(180%); -webkit-backdrop-filter: blur(var(--joma-blur-md)) saturate(180%);
  border: 1px solid rgb(255 255 255 / 0.1);
  box-shadow: 0 18px 44px rgb(0 0 0 / 0.55), 0 0 40px rgb(6 182 212 / 0.08), inset 0 1px 0 rgb(255 255 255 / 0.1);
  max-width: calc(100vw - 2rem);
}
#jomatheme-filebar.is-visible { display: flex; animation: joma-rise 0.3s cubic-bezier(0.22, 1, 0.36, 1) both; }
.jomatheme-filebar__count {
  font-size: .8rem; font-weight: 700; color: rgb(226 242 250);
  padding: 0 .3rem; white-space: nowrap;
}
.jomatheme-filebar__count.is-some { color: rgb(103 232 249); }
.jomatheme-filebar__btn { font-size: .74rem !important; padding: .38rem .85rem !important; }
.joma-fb-del.is-confirm { animation: joma-del-pulse 1s ease-in-out infinite; }
.jomatheme-filebar__hint { font-size: .68rem; color: rgb(122 152 178); padding: 0 .3rem; white-space: nowrap; }
@media (max-width: 720px) {
  .jomatheme-filebar__hint { display: none; }
  #jomatheme-filebar { flex-wrap: wrap; justify-content: center; }
}
@keyframes joma-del-pulse { 0%, 100% { filter: brightness(1); } 50% { filter: brightness(1.4); } }

@media (prefers-reduced-motion: reduce) {
  body::after, .jomatheme-ripple { animation: none !important; }
  #jomatheme-filebar.is-visible, .joma-fb-del.is-confirm { animation: none !important; }
}
</style>

<div id="jomatheme-progress" aria-hidden="true"><div id="jomatheme-progress__bar"></div></div>
<div id="jomatheme-toasts" aria-live="polite" aria-atomic="false"></div>

<script>
/* ============================================================================
   JomaTheme runtime — purely additive. Every block is wrapped in try/catch so
   a single failure can never break Pterodactyl's own rendering or requests.
   ============================================================================ */
(function () {
  "use strict";

  var REDUCED = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  function safe(fn) { try { fn(); } catch (e) { /* silent */ } }

  /* ---- Toast system --------------------------------------------------- */
  var ICONS = { success: "bi-check-circle-fill", warning: "bi-exclamation-triangle-fill", error: "bi-x-circle-fill", info: "bi-info-circle-fill" };
  var container = document.getElementById("jomatheme-toasts");
  function ensureContainer() {
    if (!container) { container = document.createElement("div"); container.id = "jomatheme-toasts"; document.body.appendChild(container); }
    return container;
  }
  function toast(opts) {
    safe(function () {
      var o = opts || {};
      var type = o.type || "info";
      var title = o.title || "";
      var message = o.message || "";
      var duration = o.duration || 3600;
      var host = ensureContainer();
      var el = document.createElement("div");
      el.className = "jomatheme-toast";
      el.setAttribute("data-type", type);
      el.setAttribute("role", type === "error" ? "alert" : "status");

      var icon = document.createElement("span");
      icon.className = "jomatheme-toast__icon jomatheme-toast__icon--" + type;
      icon.setAttribute("aria-hidden", "true");
      icon.innerHTML = '<i class="bi ' + (ICONS[type] || ICONS.info) + '"></i>';

      var body = document.createElement("div");
      body.className = "jomatheme-toast__body";
      if (title) { var t = document.createElement("div"); t.className = "jomatheme-toast__title"; t.textContent = title; body.appendChild(t); }
      if (message) { var m = document.createElement("div"); m.className = "jomatheme-toast__message"; m.textContent = message; body.appendChild(m); }

      var close = document.createElement("button");
      close.className = "jomatheme-toast__close";
      close.setAttribute("aria-label", "Close");
      close.innerHTML = '<i class="bi bi-x"></i>';
      close.addEventListener("click", function () { dismiss(); });

      el.appendChild(icon); el.appendChild(body); el.appendChild(close);

      var bar = document.createElement("span");
      bar.className = "jomatheme-toast__bar";
      bar.style.animationDuration = duration + "ms";
      if (REDUCED) bar.style.display = "none";
      el.appendChild(bar);

      host.appendChild(el);
      var timer = null;
      function dismiss() {
        if (timer) { clearTimeout(timer); timer = null; }
        if (el.classList.contains("is-leaving")) return;
        el.classList.add("is-leaving");
        setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 360);
      }
      if (!o.sticky) timer = setTimeout(dismiss, duration);
      el._dismiss = dismiss;
    });
  }

  var SIGNALS = { start: "Start", stop: "Stop", restart: "Neustart", kill: "Kill" };
  function humanSignal(s) { return SIGNALS[(s || "").toLowerCase()] || s; }

  window.JomaTheme = {
    toast: toast, notify: toast,
    success: function (t, m) { toast({ type: "success", title: t, message: m }); },
    warning: function (t, m) { toast({ type: "warning", title: t, message: m }); },
    error:   function (t, m) { toast({ type: "error",   title: t, message: m }); },
    info:    function (t, m) { toast({ type: "info",    title: t, message: m }); }
  };

  /* ---- Top loading bar ------------------------------------------------ */
  var progress = document.getElementById("jomatheme-progress");
  var inflight = 0; var progressTimer = null;
  function progressStart() {
    safe(function () {
      inflight++;
      if (!progress) progress = document.getElementById("jomatheme-progress");
      if (!progress) return;
      progress.classList.remove("is-done"); progress.classList.add("is-loading");
    });
  }
  function progressDone() {
    safe(function () {
      inflight = Math.max(0, inflight - 1);
      if (inflight > 0) return;
      if (!progress) progress = document.getElementById("jomatheme-progress");
      if (!progress) return;
      progress.classList.add("is-done");
      if (progressTimer) clearTimeout(progressTimer);
      progressTimer = setTimeout(function () { progress.classList.remove("is-loading"); progress.classList.remove("is-done"); }, 320);
    });
  }

  /* ---- fetch interceptor (power-action notifications + progress) ----- */
  safe(function () {
    if (!window.fetch || window.fetch.__joma) return;
    var orig = window.fetch;
    function wrapped(input, init) {
      var url = ""; var method = "GET";
      try {
        if (typeof input === "string") { url = input; }
        else if (input && input.url) { url = input.url; method = input.method || "GET"; }
        if (init && init.method) method = (init.method || "GET").toUpperCase();
      } catch (e) {}
      var isPower = false, signal = null;
      try {
        if (method !== "GET" && /\/api\/client\/servers\/[^\/]+\/power/.test(url)) {
          isPower = true;
          if (init && init.body) { try { signal = JSON.parse(init.body).signal; } catch (e) { try { signal = String(init.body); } catch (e2) {} } }
        }
      } catch (e) {}
      var isWrite = method !== "GET" && method !== "HEAD";
      if (isWrite) progressStart();
      var p = orig.apply(this, arguments);
      if (isWrite) p.then(progressDone, progressDone);
      if (isPower && signal) {
        p.then(function (res) {
          safe(function () {
            if (res && res.ok) { toast({ type: "success", title: "Server", message: humanSignal(signal) + " erfolgreich." }); }
            else { toast({ type: "error", title: "Aktion fehlgeschlagen", message: "Status " + (res && res.status) }); }
          });
        }, function () { safe(function () { toast({ type: "error", title: "Netzwerkfehler", message: "Server nicht erreichbar." }); }); });
      }
      return p;
    }
    wrapped.__joma = true; window.fetch = wrapped;
  });

  /* ---- XHR interceptor (covers axios-style requests) ------------------ */
  safe(function () {
    if (!window.XMLHttpRequest) return;
    if (XMLHttpRequest.prototype.__joma) return;
    var open = XMLHttpRequest.prototype.open;
    var send = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.__joma = true;
    XMLHttpRequest.prototype.open = function (method, url) {
      try { this.__joma_method = (method || "GET").toUpperCase(); this.__joma_url = String(url || ""); } catch (e) {}
      return open.apply(this, arguments);
    };
    XMLHttpRequest.prototype.send = function (body) {
      var self = this; var isWrite = false, isPower = false, signal = null;
      try {
        isWrite = self.__joma_method && self.__joma_method !== "GET" && self.__joma_method !== "HEAD";
        if (isWrite && /\/api\/client\/servers\/[^\/]+\/power/.test(self.__joma_url)) {
          isPower = true; try { signal = JSON.parse(body).signal; } catch (e) { try { signal = String(body); } catch (e2) {} }
        }
      } catch (e) {}
      if (isWrite) progressStart();
      if (isPower) {
        self.addEventListener("loadend", function () {
          safe(function () {
            if (self.status >= 200 && self.status < 300) { toast({ type: "success", title: "Server", message: humanSignal(signal) + " erfolgreich." }); }
            else { toast({ type: "error", title: "Aktion fehlgeschlagen", message: "Status " + self.status }); }
          });
        });
      }
      return send.apply(this, arguments);
    };
  });

  /* ---- Route-change layout slide -------------------------------------- */
  safe(function () {
    function triggerSlide() {
      if (REDUCED) return;
      var app = document.getElementById("app");
      if (!app) return;
      var target = app.firstElementChild || app;
      try { target.classList.remove("jomatheme-slide"); void target.offsetWidth; target.classList.add("jomatheme-slide"); } catch (e) {}
    }
    function wrapHistory(fn) { return function () { var r = fn.apply(this, arguments); try { setTimeout(triggerSlide, 0); } catch (e) {} return r; }; }
    if (history.pushState) history.pushState = wrapHistory(history.pushState);
    if (history.replaceState) history.replaceState = wrapHistory(history.replaceState);
    window.addEventListener("popstate", function () { try { triggerSlide(); } catch (e) {} });
    setTimeout(triggerSlide, 30);
  });

  /* ---- Console auto-scroll + copy button ----------------------------- */
  safe(function () {
    function findConsole() {
      var sels = ["[class*='terminal']", "[class*='Terminal']", "[class*='console']", "#terminal", ".terminal"];
      for (var i = 0; i < sels.length; i++) { var el = document.querySelector(sels[i]); if (el) return el; }
      return null;
    }
    function textOf(el) { return (el && (el.innerText || el.textContent)) || ""; }
    function maybeAddCopy() {
      var con = findConsole();
      if (!con || con.__jomaCopy) return;
      con.__jomaCopy = true;
      var wrap = con.parentNode; if (!wrap) return;
      var btn = document.createElement("button");
      btn.type = "button";
      btn.innerHTML = '<i class="bi bi-clipboard"></i>';
      btn.setAttribute("aria-label", "Copy console output");
      btn.style.cssText = "position:absolute;top:10px;right:10px;z-index:5;display:inline-flex;align-items:center;gap:.3rem;" +
        "padding:.3rem .6rem;font-size:.72rem;font-weight:600;border-radius:8px;" +
        "background:rgb(10 28 46 / 0.8);color:rgb(192 214 230);" +
        "border:1px solid rgb(255 255 255 / 0.12);cursor:pointer;backdrop-filter:blur(8px);" +
        "transition:color .2s ease,border-color .2s ease,background .2s ease;";
      btn.addEventListener("mouseenter", function () { btn.style.color = "rgb(240 250 253)"; btn.style.borderColor = "rgb(6 182 212 / 0.5)"; });
      btn.addEventListener("mouseleave", function () { btn.style.color = "rgb(192 214 230)"; btn.style.borderColor = "rgb(255 255 255 / 0.12)"; });
      btn.addEventListener("click", function () {
        safe(function () {
          var txt = textOf(con);
          var done = function () { btn.innerHTML = '<i class="bi bi-check2"></i> Kopiert'; setTimeout(function () { btn.innerHTML = '<i class="bi bi-clipboard"></i>'; }, 1400); };
          if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(txt).then(done, function () {});
        });
      });
      if (getComputedStyle(wrap).position === "static") wrap.style.position = "relative";
      wrap.appendChild(btn);
    }
    function autoscroll() {
      var con = findConsole(); if (!con) return;
      var near = (con.scrollHeight - con.scrollTop - con.clientHeight) < 80;
      if (near) con.scrollTop = con.scrollHeight;
    }
    var mo = new MutationObserver(function () { safe(autoscroll); });
    function boot() {
      maybeAddCopy();
      var con = findConsole();
      if (con) mo.observe(con, { childList: true, subtree: true, characterData: true });
    }
    if (document.readyState !== "loading") boot();
    document.addEventListener("DOMContentLoaded", boot);
    var retries = 0; var iv = setInterval(function () {
      if (findConsole()) { boot(); if (++retries > 8) clearInterval(iv); }
      else if (++retries > 12) clearInterval(iv);
    }, 700);
  });

  /* ---- Number count-up (opt-in via [data-joma-count]) ---------------- */
  safe(function () {
    function run(el) {
      var target = parseFloat(el.getAttribute("data-joma-count"));
      if (isNaN(target)) return;
      var dur = 900, start = null, from = 0;
      var suffix = el.getAttribute("data-joma-suffix") || "";
      function step(ts) {
        if (!start) start = ts;
        var p = Math.min(1, (ts - start) / dur);
        var e = 1 - Math.pow(1 - p, 3);
        el.textContent = (from + (target - from) * e).toFixed(target % 1 === 0 ? 0 : 1) + suffix;
        if (p < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    }
    function scan(root) { var els = (root || document).querySelectorAll("[data-joma-count]"); for (var i = 0; i < els.length; i++) run(els[i]); }
    if (document.readyState !== "loading") scan(document);
    document.addEventListener("DOMContentLoaded", function () { scan(document); });
  });

  /* ---- Welcome toast on first load ----------------------------------- */
  safe(function () {
    setTimeout(function () {
      toast({ type: "info", title: "JomaTheme aktiv", message: "Willkommen beim JomaMC Control Panel.", duration: 4200 });
    }, 900);
  });

})();
</script>

<script>
/* ============================================================================
   JomaTheme runtime UI — button ripples + command palette (Ctrl/⌘ + K).
   Purely additive; everything is guarded so Pterodactyl is never affected.
   ============================================================================ */
(function () {
  "use strict";
  function safe(fn) { try { fn(); } catch (e) { /* silent */ } }

  /* ---- Button click ripples (event delegation) ---- */
  safe(function () {
    var SEL = "button, .btn, [class*='Button'], a[class*='btn'], [role='button']";
    document.addEventListener("pointerdown", function (e) {
      safe(function () {
        if (window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
        var t = e.target && e.target.closest ? e.target.closest(SEL) : null;
        if (!t) return; if (t.disabled) return;
        var rect = t.getBoundingClientRect();
        var size = Math.max(rect.width, rect.height) * 1.1;
        var x = (e.clientX - rect.left) - size / 2;
        var y = (e.clientY - rect.top) - size / 2;
        var r = document.createElement("span");
        r.className = "jomatheme-ripple";
        r.style.width = r.style.height = size + "px";
        r.style.left = x + "px"; r.style.top = y + "px";
        t.appendChild(r);
        setTimeout(function () { if (r.parentNode) r.parentNode.removeChild(r); }, 680);
      });
    }, true);
  });

  /* ---- Command palette (Ctrl/⌘ + K) -------------------------------- */
  safe(function () {
    function go(p) { try { window.location.href = p; } catch (e) {} }
    var cmds = [
      { group: "Navigate", icon: "bi-house", label: "Dashboard", hint: "/", run: function () { go("/"); } },
      { group: "Account",  icon: "bi-person", label: "Account Settings", hint: "/account", run: function () { go("/account"); } },
      { group: "Account",  icon: "bi-key", label: "API Credentials", hint: "/account/api", run: function () { go("/account/api"); } },
      { group: "Account",  icon: "bi-terminal", label: "SSH Keys", hint: "/account/ssh", run: function () { go("/account/ssh"); } },
      { group: "Actions",   icon: "bi-arrow-clockwise", label: "Reload page", hint: "refresh", run: function () { window.location.reload(); } }
    ];
    var m = location.pathname.match(/\/server\/([^/]+)/);
    if (m && m[1]) {
      var id = m[1];
      var tabs = [["Console",""],["Files","/files"],["Backups","/backups"],["Schedules","/schedules"],["Users","/users"],["Network","/network"],["Startup","/startup"],["Settings","/settings"]];
      var serverCmds = tabs.map(function (t) {
        var path = "/server/" + id + t[1];
        var ic = ({"Console":"bi-terminal","Files":"bi-folder","Backups":"bi-archive","Schedules":"bi-clock","Users":"bi-people","Network":"bi-globe","Startup":"bi-rocket-takeoff","Settings":"bi-gear"})[t[0]];
        return { group: "Server", icon: ic, label: t[0], hint: path, run: function () { go(path); } };
      });
      cmds = serverCmds.concat(cmds);
    }
    var isAdmin = false;
    try { var u = window.PterodactylUser; isAdmin = !!(u && (u.root_admin || u.rootAdmin || u.admin)); } catch (e) {}
    if (isAdmin) {
      var admin = [["Admin Dashboard","/admin","bi-speedometer2"],["Admin Users","/admin/users","bi-people"],["Admin Servers","/admin/servers","bi-server"],["Admin Nodes","/admin/nodes","bi-hdd-network"],["Admin Locations","/admin/locations","bi-geo-alt"],["Admin Nests","/admin/nests","bi-collection"],["Admin Eggs","/admin/eggs","bi-egg"],["Admin Databases","/admin/databases","bi-database"],["Admin Mounts","/admin/mounts","bi-box"],["Blueprint Extensions","/admin/extensions","bi-puzzle"],["JomaTheme Settings","/admin/extensions/jomatheme","bi-palette"]];
      admin.forEach(function (a) { cmds.push({ group: "Admin", icon: a[2], label: a[0], hint: a[1], run: function () { go(a[1]); } }); });
    }

    var root = document.createElement("div");
    root.id = "jomatheme-cmdk";
    root.innerHTML =
      '<div class="jomatheme-cmdk__backdrop" data-cmdk-close></div>' +
      '<div class="jomatheme-cmdk__panel" role="dialog" aria-modal="true" aria-label="JomaMC command palette">' +
        '<div class="jomatheme-cmdk__head">' +
          '<span class="jomatheme-cmdk__icon" aria-hidden="true"><i class="bi bi-search"></i></span>' +
          '<input class="jomatheme-cmdk__input" id="jomatheme-cmdk-input" placeholder="Search or run a command..." autocomplete="off" spellcheck="false">' +
          '<span class="jomatheme-cmdk__kbd">ESC</span>' +
        '</div>' +
        '<div class="jomatheme-cmdk__list" id="jomatheme-cmdk-list"></div>' +
      '</div>';
    document.body.appendChild(root);

    var hint = document.createElement("button");
    hint.type = "button"; hint.id = "jomatheme-cmdk-hint"; hint.setAttribute("aria-label", "Open command palette");
    hint.innerHTML = 'Menu <kbd>Ctrl K</kbd>';
    hint.addEventListener("click", open);
    document.body.appendChild(hint);

    var input = document.getElementById("jomatheme-cmdk-input");
    var list = document.getElementById("jomatheme-cmdk-list");
    var active = 0; var filtered = [];

    function open() { root.classList.add("is-open"); setTimeout(function () { input.focus(); input.select(); }, 30); render(""); }
    function close() { root.classList.remove("is-open"); input.value = ""; }
    function isOpen() { return root.classList.contains("is-open"); }

    function render(q) {
      q = (q || "").toLowerCase().trim();
      filtered = cmds.filter(function (c) { return !q || (c.label + " " + c.group + " " + c.hint).toLowerCase().indexOf(q) > -1; });
      active = 0;
      if (!filtered.length) { list.innerHTML = '<div class="jomatheme-cmdk__empty">No commands found</div>'; return; }
      var groups = {}; var order = [];
      filtered.forEach(function (c) { if (!groups[c.group]) { groups[c.group] = []; order.push(c.group); } groups[c.group].push(c); });
      var html = "";
      order.forEach(function (g) {
        html += '<div class="jomatheme-cmdk__group">' + g + '</div>';
        groups[g].forEach(function (c) {
          var idx = filtered.indexOf(c);
          html += '<div class="jomatheme-cmdk__item' + (idx === active ? " is-active" : "") + '" data-idx="' + idx + '" role="option">' +
            '<span class="jomatheme-cmdk__item-icon"><i class="bi ' + c.icon + '"></i></span>' +
            '<span class="jomatheme-cmdk__item-label">' + c.label + '</span>' +
            '<span class="jomatheme-cmdk__item-hint">' + c.hint + '</span>' +
          '</div>';
        });
      });
      list.innerHTML = html;
      var items = list.querySelectorAll(".jomatheme-cmdk__item");
      for (var i = 0; i < items.length; i++) {
        (function (el) {
          el.addEventListener("mouseenter", function () { setActive(parseInt(el.dataset.idx, 10)); });
          el.addEventListener("click", function () { runActive(parseInt(el.dataset.idx, 10)); });
        })(items[i]);
      }
    }
    function setActive(i) {
      active = Math.max(0, Math.min(filtered.length - 1, i));
      var items = list.querySelectorAll(".jomatheme-cmdk__item");
      for (var k = 0; k < items.length; k++) {
        var idx = parseInt(items[k].dataset.idx, 10);
        var on = idx === active;
        items[k].classList.toggle("is-active", on);
        if (on) items[k].scrollIntoView({ block: "nearest" });
      }
    }
    function runActive(i) { var c = filtered[i]; if (c) { close(); c.run(); } }

    input.addEventListener("input", function () { render(input.value); });
    root.querySelector("[data-cmdk-close]").addEventListener("click", close);

    document.addEventListener("keydown", function (e) {
      if ((e.ctrlKey || e.metaKey) && (e.key === "k" || e.key === "K" || e.keyCode === 75)) {
        e.preventDefault(); if (isOpen()) close(); else open(); return;
      }
      if (!isOpen()) return;
      if (e.key === "Escape") { e.preventDefault(); close(); }
      else if (e.key === "ArrowDown") { e.preventDefault(); setActive(active + 1); }
      else if (e.key === "ArrowUp") { e.preventDefault(); setActive(active - 1); }
      else if (e.key === "Enter") { e.preventDefault(); runActive(active); }
    });
  });
})();
</script>

<script>
/* ============================================================================
   JomaTheme runtime UI — file manager selection dock (/server/<id>/files).
   Adds: always-visible checkboxes (CSS via body.joma-files), select all/none,
   shift-click range selection and a two-step mass delete that calls the
   official Pterodactyl client API (DELETE .../files/delete). Selection is
   driven through native .click() so Pterodactyl's React state stays in sync.
   Purely additive; every block is guarded.
   ============================================================================ */
(function () {
  "use strict";
  function safe(fn) { try { fn(); } catch (e) { /* silent */ } }

  function pathOK() { return /^\/server\/[^\/]+\/files/.test(location.pathname); }

  var bar = null, countEl = null, delBtn = null, confirmTimer = null, lastIdx = null;

  function boxes() {
    return Array.prototype.slice.call(
      document.querySelectorAll("table tbody input[type='checkbox'], [role='table'] input[type='checkbox']")
    );
  }
  function selected() {
    return boxes().filter(function (b) { return b.checked; });
  }
  function dir() {
    var d = "/";
    safe(function () { d = new URLSearchParams(location.search).get("dir") || "/"; });
    if (!d) d = "/";
    if (d.charAt(0) !== "/") d = "/" + d;
    d = d.replace(/\/+$/, "");
    return d || "/";
  }
  /* file name = first non-checkbox cell text of the row */
  function fileName(box) {
    var tr = box.closest ? box.closest("tr, [role='row']") : null;
    if (!tr) return "";
    var cells = tr.cells || tr.children || [];
    for (var i = 0; i < cells.length; i++) {
      if (cells[i].contains(box)) continue;
      var t = (cells[i].textContent || "").replace(/\s+/g, " ").trim();
      if (t) return t;
    }
    return "";
  }
  function csrf() {
    var m = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
    return m ? decodeURIComponent(m[1]) : "";
  }

  function resetConfirm() {
    if (confirmTimer) { clearTimeout(confirmTimer); confirmTimer = null; }
    if (delBtn) { delBtn.classList.remove("is-confirm"); delBtn.textContent = "Löschen"; }
  }
  function refresh() {
    safe(function () {
      if (!bar) return;
      var bs = boxes();
      var show = pathOK() && bs.length > 0;
      bar.classList.toggle("is-visible", show);
      if (!show) { lastIdx = null; return; }
      var n = selected().length;
      countEl.textContent = n === 0 ? "Keine Datei ausgewählt" : n + (n === 1 ? " Datei" : " Dateien") + " ausgewählt";
      countEl.classList.toggle("is-some", n > 0);
      resetConfirm();
      delBtn.disabled = n === 0;
    });
  }

  function doDelete() {
    safe(function () {
      var files = selected().map(fileName).filter(function (n) {
        return n && n !== ".." && n !== "../" && n !== ".";
      });
      if (!files.length) return;
      var id = (location.pathname.match(/^\/server\/([^\/]+)/) || [])[1];
      if (!id) return;
      delBtn.disabled = true; delBtn.textContent = "Lösche…";
      fetch("/api/client/servers/" + encodeURIComponent(id) + "/files/delete", {
        method: "DELETE",
        credentials: "same-origin",
        headers: {
          "Accept": "application/json",
          "Content-Type": "application/json",
          "X-XSRF-TOKEN": csrf()
        },
        body: JSON.stringify({ root: dir(), files: files })
      }).then(function (res) {
        safe(function () {
          if (res.ok) {
            if (window.JomaTheme) window.JomaTheme.toast({ type: "success", title: "Dateien", message: files.length + " gelöscht." });
            setTimeout(function () { window.location.reload(); }, 650);
          } else {
            if (window.JomaTheme) window.JomaTheme.toast({ type: "error", title: "Löschen fehlgeschlagen", message: "Status " + res.status });
            delBtn.disabled = false; resetConfirm();
          }
        });
      }, function () {
        safe(function () {
          if (window.JomaTheme) window.JomaTheme.toast({ type: "error", title: "Netzwerkfehler", message: "Server nicht erreichbar." });
          delBtn.disabled = false; resetConfirm();
        });
      });
    });
  }

  function build() {
    if (bar || !document.body) return;
    bar = document.createElement("div");
    bar.id = "jomatheme-filebar";
    bar.setAttribute("role", "toolbar");
    bar.setAttribute("aria-label", "Datei-Auswahl");
    bar.innerHTML =
      '<span class="jomatheme-filebar__count">Keine Datei ausgewählt</span>' +
      '<button type="button" class="btn-secondary jomatheme-filebar__btn" data-act="all">Alle auswählen</button>' +
      '<button type="button" class="btn-secondary jomatheme-filebar__btn" data-act="none">Auswahl aufheben</button>' +
      '<button type="button" class="btn-danger jomatheme-filebar__btn joma-fb-del" data-act="delete" disabled>Löschen</button>' +
      '<span class="jomatheme-filebar__hint">Shift+Klick = Bereich</span>';
    document.body.appendChild(bar);
    countEl = bar.querySelector(".jomatheme-filebar__count");
    delBtn = bar.querySelector(".joma-fb-del");
    bar.addEventListener("click", function (e) {
      safe(function () {
        var t = e.target && e.target.closest ? e.target.closest("[data-act]") : null;
        if (!t) return;
        var act = t.getAttribute("data-act");
        if (act === "all") {
          boxes().forEach(function (b) { if (!b.checked) b.click(); });
          refresh();
        } else if (act === "none") {
          boxes().forEach(function (b) { if (b.checked) b.click(); });
          refresh();
        } else if (act === "delete" && t === delBtn) {
          if (!selected().length) return;
          if (!t.classList.contains("is-confirm")) {
            t.classList.add("is-confirm");
            t.textContent = "Wirklich löschen?";
            if (confirmTimer) clearTimeout(confirmTimer);
            confirmTimer = setTimeout(resetConfirm, 3500);
          } else {
            doDelete();
          }
        }
      });
    });
  }

  /* shift-click range select on file checkboxes */
  document.addEventListener("click", function (e) {
    safe(function () {
      if (!pathOK()) return;
      var t = e.target;
      if (!t || t.type !== "checkbox" || !t.closest || !t.closest("tbody, [role='table']")) return;
      var bs = boxes();
      var idx = bs.indexOf(t);
      if (e.shiftKey && lastIdx !== null && lastIdx !== idx) {
        var a = Math.min(lastIdx, idx), b = Math.max(lastIdx, idx);
        for (var i = a; i <= b; i++) {
          if (bs[i] && bs[i].checked !== t.checked) bs[i].click();
        }
      }
      lastIdx = idx;
      setTimeout(refresh, 60);
    });
  }, true);

  function syncBody() {
    safe(function () { document.body.classList.toggle("joma-files", pathOK()); });
  }
  function boot() { syncBody(); build(); refresh(); }

  if (document.readyState !== "loading") boot();
  else document.addEventListener("DOMContentLoaded", boot);
  window.addEventListener("popstate", syncBody);
  setInterval(function () { safe(function () { syncBody(); if (!bar) build(); refresh(); }); }, 800);
})();
</script>
@endverbatim
