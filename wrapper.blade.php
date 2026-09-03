@verbatim
<style>
/* ============================================================================
   JomaTheme — runtime animations & enhancements
   Served as a <style> tag via the Blueprint dashboard wrapper (every page).
   Heavy keyframes live here so they can never break the React CSS bundle.
   ============================================================================ */

/* ---- keyframes referenced by dashboard.css ---- */
@keyframes joma-aurora-drift {
  0%   { transform: translate3d(-4%, -2%, 0) scale(1.05); }
  50%  { transform: translate3d(6%, 4%, 0) scale(1.12); }
  100% { transform: translate3d(-4%, -2%, 0) scale(1.05); }
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
@keyframes joma-shimmer {
  100% { transform: translateX(100%); }
}
@keyframes joma-spin {
  to { transform: rotate(360deg); }
}
@keyframes joma-slide-up {
  0%   { opacity: 0; transform: translate3d(0, 18px, 0); filter: blur(6px); }
  100% { opacity: 1; transform: translate3d(0, 0, 0); filter: blur(0); }
}
@keyframes joma-fade-up {
  0%   { opacity: 0; transform: translate3d(0, 12px, 0); }
  100% { opacity: 1; transform: translate3d(0, 0, 0); }
}
@keyframes joma-toast-in {
  0%   { opacity: 0; transform: translate3d(120%, 0, 0) scale(0.96); }
  100% { opacity: 1; transform: translate3d(0, 0, 0) scale(1); }
}
@keyframes joma-toast-out {
  0%   { opacity: 1; transform: translate3d(0, 0, 0) scale(1); }
  100% { opacity: 0; transform: translate3d(120%, 0, 0) scale(0.96); }
}
@keyframes joma-toast-bar {
  from { transform: scaleX(1); }
  to   { transform: scaleX(0); }
}
@keyframes joma-progress-grow {
  0%   { transform: translateX(-100%); }
  50%  { transform: translateX(-30%); }
  100% { transform: translateX(0%); }
}
@keyframes joma-gradient-shift {
  0%, 100% { background-position: 0% 50%; }
  50%      { background-position: 100% 50%; }
}
@keyframes joma-rise {
  0%   { opacity: 0; transform: translate3d(0, 16px, 0); }
  100% { opacity: 1; transform: translate3d(0, 0, 0); }
}

/* ---- whole-app entrance (runs once on full page load) ---- */
#app { animation: joma-fade-up 0.6s cubic-bezier(0.22, 1, 0.36, 1) both; }

/* ---- subtle glass-surface hover polish (no transform on unknown els) ---- */
.bg-white,
[class*="bg-white"] {
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.bg-white:hover,
[class*="bg-white"]:hover {
  border-color: rgb(124 92 252 / 0.28) !important;
  box-shadow: 0 14px 38px rgb(0 0 0 / 0.5), 0 0 0 1px rgb(124 92 252 / 0.18) !important;
}

/* ---- primary button shine sweep on hover ---- */
button[class*="primary"]::after,
.btn-primary::after,
[class*="PrimaryButton"]::after {
  content: "";
  position: absolute; inset: 0;
  background: linear-gradient(110deg, transparent 30%, rgb(255 255 255 / 0.35) 50%, transparent 70%);
  transform: translateX(-120%);
  opacity: 0;
  pointer-events: none;
  border-radius: inherit;
}
button[class*="primary"],
.btn-primary,
[class*="PrimaryButton"] { position: relative; overflow: hidden; }
button[class*="primary"]:not(:disabled):hover::after,
.btn-primary:hover::after,
[class*="PrimaryButton"]:not(:disabled):hover::after {
  opacity: 1;
  transform: translateX(120%);
  transition: transform 0.7s ease, opacity 0.2s ease;
}

/* ---- top loading bar ---- */
#jomatheme-progress {
  position: fixed; top: 0; left: 0; right: 0; height: 3px; z-index: 99998;
  pointer-events: none; opacity: 0;
  transition: opacity 0.3s ease;
}
#jomatheme-progress.is-loading { opacity: 1; }
#jomatheme-progress__bar {
  height: 100%; width: 100%; transform-origin: left center; transform: translateX(-100%);
  background: linear-gradient(90deg, rgb(124 92 252), rgb(56 189 248), rgb(217 70 239), rgb(124 92 252));
  background-size: 200% 100%;
  animation: joma-gradient-shift 1.6s linear infinite;
  box-shadow: 0 0 12px rgb(124 92 252 / 0.7);
  transition: transform 0.25s ease;
}
#jomatheme-progress.is-loading #jomatheme-progress__bar { transform: translateX(-30%); }
#jomatheme-progress.is-done #jomatheme-progress__bar { transform: translateX(0%); }

/* ---- staggered rise utility ---- */
.jomatheme-rise { animation: joma-rise 0.55s cubic-bezier(0.22, 1, 0.36, 1) both; }
.jomatheme-rise:nth-child(1) { animation-delay: 0.04s; }
.jomatheme-rise:nth-child(2) { animation-delay: 0.10s; }
.jomatheme-rise:nth-child(3) { animation-delay: 0.16s; }
.jomatheme-rise:nth-child(4) { animation-delay: 0.22s; }
.jomatheme-rise:nth-child(5) { animation-delay: 0.28s; }
.jomatheme-rise:nth-child(6) { animation-delay: 0.34s; }

/* ---- console tweaks (safe, selector-tolerant) ---- */
[class*="terminal"],
[class*="console"] {
  border-radius: 12px !important;
  background: rgb(6 6 12 / 0.85) !important;
  border: 1px solid rgb(255 255 255 / 0.08) !important;
  box-shadow: inset 0 0 40px rgb(0 0 0 / 0.6) !important;
}

/* ============================================================================
   JomaTheme v1.1 — premium button system · ripple · command palette
   ============================================================================ */
:root {
  --joma-radius-sm: 10px; --joma-radius-md: 14px; --joma-radius-lg: 18px;
  --joma-radius-xl: 22px; --joma-radius-modal: 24px;
  --joma-blur-sm: 10px; --joma-blur-md: 18px; --joma-blur-lg: 28px;
  --joma-transition-fast: 120ms; --joma-transition-normal: 200ms;
}

/* ---- button foundation (enables ripple + shine, keeps box-shadow) ---- */
button, .btn, [class*="Button"], a[class*="btn"], [role="button"] {
  position: relative;
  overflow: hidden;
}

/* ---- click ripple (spawned by JS at the cursor) ---- */
.jomatheme-ripple {
  position: absolute; border-radius: 50%; pointer-events: none;
  transform: scale(0); opacity: 0.9; z-index: 0;
  background: radial-gradient(circle, rgb(255 255 255 / 0.55), transparent 72%);
  animation: joma-ripple 0.62s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}
button[class*="primary"] .jomatheme-ripple,
.btn-primary .jomatheme-ripple,
[class*="PrimaryButton"] .jomatheme-ripple {
  background: radial-gradient(circle, rgb(255 255 255 / 0.75), transparent 72%);
}
@keyframes joma-ripple { to { transform: scale(2.8); opacity: 0; } }

/* ---- premium primary: animated gradient + glow on hover, 3D press on click ---- */
button[class*="primary"], .btn-primary, [class*="PrimaryButton"] {
  background-size: 200% 200% !important;
  transition: transform var(--joma-transition-normal) cubic-bezier(0.34,1.56,0.64,1),
              box-shadow var(--joma-transition-normal) ease,
              filter var(--joma-transition-normal) ease,
              background-position 0.6s ease !important;
}
button[class*="primary"]:not(:disabled):hover,
.btn-primary:hover,
[class*="PrimaryButton"]:not(:disabled):hover {
  background-position: 100% 0% !important;
  filter: brightness(1.07);
  box-shadow: 0 16px 36px rgb(124 92 252 / 0.5), 0 0 0 1px rgb(165 139 252 / 0.45),
              inset 0 1px 0 rgb(255 255 255 / 0.25) !important;
}
button[class*="primary"]:not(:disabled):active,
.btn-primary:active,
[class*="PrimaryButton"]:not(:disabled):active {
  transform: translateY(0) scale(0.96);
  box-shadow: 0 4px 14px rgb(124 92 252 / 0.4) !important;
}

/* ---- secondary / ghost / danger press ---- */
button[class*="secondary"]:not(:disabled):active,
.btn-secondary:active,
[class*="SecondaryButton"]:not(:disabled):active,
button[class*="ghost"]:not(:disabled):active,
button[class*="danger"]:not(:disabled):active,
.btn-danger:active,
[class*="DangerButton"]:not(:disabled):active {
  transform: translateY(0) scale(0.96);
}

/* ---- icon nudge inside buttons on hover ---- */
button:not(:disabled):hover svg,
.btn:hover svg,
[class*="Button"]:not(:disabled):hover svg {
  transform: translateX(2px);
  transition: transform var(--joma-transition-normal) ease;
}

/* ---- card micro-interactions (scoped to safe selectors) ---- */
.jomatheme-welcome { transition: transform var(--joma-transition-normal) ease, box-shadow var(--joma-transition-normal) ease; }
.jomatheme-welcome:hover { transform: translateY(-2px); }

[class*="ServerCard"], [class*="server-card"], [class*="ServerRow"] {
  transition: transform var(--joma-transition-normal) cubic-bezier(0.34,1.56,0.64,1),
              box-shadow var(--joma-transition-normal) ease,
              border-color var(--joma-transition-normal) ease !important;
}
[class*="ServerCard"]:hover, [class*="server-card"]:hover, [class*="ServerRow"]:hover {
  transform: translateY(-3px) !important;
  border-color: rgb(124 92 252 / 0.28) !important;
  box-shadow: 0 22px 46px rgb(0 0 0 / 0.5), 0 0 0 1px rgb(124 92 252 / 0.22) !important;
}

/* ---- gradient-border utility ---- */
.jomatheme-grad-border { position: relative; border: 1px solid transparent !important; background-clip: padding-box; }
.jomatheme-grad-border::before {
  content: ""; position: absolute; inset: 0; z-index: -1; border-radius: inherit;
  padding: 1px; margin: -1px; pointer-events: none;
  background: linear-gradient(135deg, rgb(124 92 252), rgb(56 189 248), rgb(217 70 239));
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor; mask-composite: exclude;
}

/* ---- status glow dots ---- */
.jomatheme-dot { width: 8px; height: 8px; border-radius: 999px; display: inline-block; }
.jomatheme-dot--online { background: rgb(34 197 94); box-shadow: 0 0 0 4px rgb(34 197 94 / 0.18), 0 0 12px rgb(34 197 94 / 0.6); animation: joma-pulse 2s infinite; }
.jomatheme-dot--offline { background: rgb(239 68 68); box-shadow: 0 0 0 4px rgb(239 68 68 / 0.18); }
.jomatheme-dot--starting { background: rgb(245 158 11); box-shadow: 0 0 0 4px rgb(245 158 11 / 0.18), 0 0 12px rgb(245 158 11 / 0.6); animation: joma-pulse 1.4s infinite; }

/* ---- animated progress bar ---- */
.jomatheme-progress { height: 6px; border-radius: 999px; background: rgb(255 255 255 / 0.08); overflow: hidden; }
.jomatheme-progress__bar {
  height: 100%; border-radius: 999px; transition: width 0.4s ease;
  background: linear-gradient(90deg, rgb(124 92 252), rgb(56 189 248));
  background-size: 200% 100%; animation: joma-gradient-shift 2.4s linear infinite;
}

/* ---- tooltip utility ---- */
.jomatheme-tip { position: relative; }
.jomatheme-tip::after {
  content: attr(data-joma-tip); position: absolute; left: 50%; bottom: calc(100% + 8px);
  transform: translateX(-50%) translateY(4px); z-index: 50;
  background: rgb(8 8 14 / 0.92); color: rgb(236 236 245);
  border: 1px solid rgb(255 255 255 / 0.1); border-radius: 8px;
  padding: .25rem .5rem; font-size: .72rem; white-space: nowrap;
  opacity: 0; pointer-events: none;
  transition: opacity var(--joma-transition-fast) ease, transform var(--joma-transition-fast) ease;
}
.jomatheme-tip:hover::after { opacity: 1; transform: translateX(-50%) translateY(0); }

/* ---- command palette (Ctrl + K) ---- */
#jomatheme-cmdk { position: fixed; inset: 0; z-index: 99997; display: none; }
#jomatheme-cmdk.is-open { display: block; }
.jomatheme-cmdk__backdrop {
  position: absolute; inset: 0;
  background: rgb(5 5 9 / 0.55);
  backdrop-filter: blur(8px) saturate(120%); -webkit-backdrop-filter: blur(8px) saturate(120%);
  animation: joma-fade 0.2s ease both;
}
.jomatheme-cmdk__panel {
  position: relative; width: calc(100% - 2rem); max-width: 38rem; margin: 12vh auto 0;
  background: rgb(18 18 28 / 0.86);
  backdrop-filter: blur(var(--joma-blur-lg)) saturate(160%); -webkit-backdrop-filter: blur(var(--joma-blur-lg)) saturate(160%);
  border: 1px solid rgb(255 255 255 / 0.1); border-radius: var(--joma-radius-modal);
  box-shadow: 0 30px 80px rgb(0 0 0 / 0.6), 0 0 0 1px rgb(124 92 252 / 0.12), inset 0 1px 0 rgb(255 255 255 / 0.06);
  overflow: hidden; animation: joma-cmdk-in 0.32s cubic-bezier(0.34,1.56,0.64,1) both;
}
.jomatheme-cmdk__head {
  display: flex; align-items: center; gap: .6rem; padding: .9rem 1rem;
  border-bottom: 1px solid rgb(255 255 255 / 0.07);
}
.jomatheme-cmdk__icon { color: rgb(150 150 170); font-size: 1.05rem; }
.jomatheme-cmdk__input {
  flex: 1; background: transparent !important; border: 0 !important; box-shadow: none !important;
  color: rgb(236 236 245) !important; font-size: 1rem; outline: none !important;
}
.jomatheme-cmdk__input::placeholder { color: rgb(120 120 138) !important; }
.jomatheme-cmdk__kbd {
  font-size: .68rem; color: rgb(150 150 170); border: 1px solid rgb(255 255 255 / 0.12);
  border-radius: 6px; padding: .1rem .35rem; background: rgb(255 255 255 / 0.04);
}
.jomatheme-cmdk__list { max-height: 22rem; overflow-y: auto; padding: .4rem; }
.jomatheme-cmdk__group { font-size: .66rem; text-transform: uppercase; letter-spacing: .1em; color: rgb(120 120 138); padding: .6rem .6rem .3rem; }
.jomatheme-cmdk__item {
  display: flex; align-items: center; gap: .65rem; padding: .55rem .6rem; border-radius: var(--joma-radius-sm);
  color: rgb(210 210 222); cursor: pointer; border: 1px solid transparent;
  transition: background var(--joma-transition-fast) ease, border-color var(--joma-transition-fast) ease, transform var(--joma-transition-fast) ease, color var(--joma-transition-fast) ease;
}
.jomatheme-cmdk__item.is-active {
  background: linear-gradient(135deg, rgb(124 92 252 / 0.22), rgb(56 189 248 / 0.1));
  border-color: rgb(124 92 252 / 0.35); color: rgb(248 248 252); transform: translateX(2px);
}
.jomatheme-cmdk__item-icon { width: 1.2rem; text-align: center; }
.jomatheme-cmdk__item-label { flex: 1; }
.jomatheme-cmdk__item-hint { font-size: .7rem; color: rgb(120 120 138); }
.jomatheme-cmdk__empty { padding: 1.4rem; text-align: center; color: rgb(140 140 160); font-size: .85rem; }
@keyframes joma-fade { from { opacity: 0; } to { opacity: 1; } }
@keyframes joma-cmdk-in { from { opacity: 0; transform: translateY(-12px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }

/* ---- command palette hint (bottom-left, subtle) ---- */
#jomatheme-cmdk-hint {
  position: fixed; left: 1.25rem; bottom: 1.25rem; z-index: 99990;
  display: inline-flex; align-items: center; gap: .4rem;
  padding: .35rem .6rem; border-radius: 8px; cursor: pointer;
  background: rgb(22 22 34 / 0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgb(255 255 255 / 0.1); color: rgb(200 200 214); font-size: .72rem;
  box-shadow: 0 8px 24px rgb(0 0 0 / 0.35);
  transition: border-color var(--joma-transition-normal) ease, transform var(--joma-transition-normal) ease, color var(--joma-transition-normal) ease;
}
#jomatheme-cmdk-hint:hover { border-color: rgb(124 92 252 / 0.5); color: rgb(248 248 252); transform: translateY(-1px); }
#jomatheme-cmdk-hint kbd { font-size: .66rem; border: 1px solid rgb(255 255 255 / 0.14); border-radius: 5px; padding: .05rem .3rem; background: rgb(255 255 255 / 0.06); color: rgb(165 139 252); }
@media (max-width: 640px) { #jomatheme-cmdk-hint { display: none; } }
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
  var ICONS = { success: "✓", warning: "⚠", error: "✕", info: "ℹ" };
  var container = document.getElementById("jomatheme-toasts");

  function ensureContainer() {
    if (!container) {
      container = document.createElement("div");
      container.id = "jomatheme-toasts";
      document.body.appendChild(container);
    }
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
      icon.className = "jomatheme-toast__icon";
      icon.textContent = ICONS[type] || "ℹ";

      var body = document.createElement("div");
      body.className = "jomatheme-toast__body";
      if (title) {
        var t = document.createElement("div");
        t.className = "jomatheme-toast__title";
        t.textContent = title;
        body.appendChild(t);
      }
      if (message) {
        var m = document.createElement("div");
        m.className = "jomatheme-toast__message";
        m.textContent = message;
        body.appendChild(m);
      }

      var close = document.createElement("button");
      close.className = "jomatheme-toast__close";
      close.setAttribute("aria-label", "Close");
      close.textContent = "✕";
      close.addEventListener("click", function () { dismiss(); });

      el.appendChild(icon);
      el.appendChild(body);
      el.appendChild(close);

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

  /* expose a tiny public API */
  window.JomaTheme = {
    toast: toast,
    notify: toast,
    success: function (t, m) { toast({ type: "success", title: t, message: m }); },
    warning: function (t, m) { toast({ type: "warning", title: t, message: m }); },
    error:   function (t, m) { toast({ type: "error",   title: t, message: m }); },
    info:    function (t, m) { toast({ type: "info",    title: t, message: m }); }
  };

  /* ---- Top loading bar ------------------------------------------------ */
  var progress = document.getElementById("jomatheme-progress");
  var inflight = 0;
  var progressTimer = null;
  function progressStart() {
    safe(function () {
      inflight++;
      if (!progress) progress = document.getElementById("jomatheme-progress");
      if (!progress) return;
      progress.classList.remove("is-done");
      progress.classList.add("is-loading");
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
      progressTimer = setTimeout(function () {
        progress.classList.remove("is-loading");
        progress.classList.remove("is-done");
      }, 320);
    });
  }

  /* ---- fetch interceptor (power-action notifications + progress) ----- */
  safe(function () {
    if (!window.fetch || window.fetch.__joma) return;
    var orig = window.fetch;
    function wrapped(input, init) {
      var url = "";
      var method = "GET";
      try {
        if (typeof input === "string") { url = input; }
        else if (input && input.url) { url = input.url; method = input.method || "GET"; }
        if (init && init.method) method = (init.method || "GET").toUpperCase();
      } catch (e) {}

      var isPower = false, signal = null;
      try {
        if (method !== "GET" && /\/api\/client\/servers\/[^\/]+\/power/.test(url)) {
          isPower = true;
          if (init && init.body) {
            try { signal = JSON.parse(init.body).signal; }
            catch (e) { try { signal = String(init.body); } catch (e2) {} }
          }
        }
      } catch (e) {}

      var isWrite = method !== "GET" && method !== "HEAD";
      if (isWrite) progressStart();

      var p = orig.apply(this, arguments);
      if (isWrite) p.then(progressDone, progressDone);
      if (isPower && signal) {
        p.then(function (res) {
          safe(function () {
            if (res && res.ok) {
              toast({ type: "success", title: "Server", message: humanSignal(signal) + " erfolgreich." });
            } else {
              toast({ type: "error", title: "Aktion fehlgeschlagen", message: "Status " + (res && res.status) });
            }
          });
        }, function () {
          safe(function () { toast({ type: "error", title: "Netzwerkfehler", message: "Server nicht erreichbar." }); });
        });
      }
      return p;
    }
    wrapped.__joma = true;
    window.fetch = wrapped;
  });

  /* ---- XHR interceptor (covers axios-style requests) ------------------ */
  safe(function () {
    if (!window.XMLHttpRequest) return;
    if (XMLHttpRequest.prototype.__joma) return;
    var open = XMLHttpRequest.prototype.open;
    var send = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.__joma = true;
    XMLHttpRequest.prototype.open = function (method, url) {
      try {
        this.__joma_method = (method || "GET").toUpperCase();
        this.__joma_url = String(url || "");
      } catch (e) {}
      return open.apply(this, arguments);
    };
    XMLHttpRequest.prototype.send = function (body) {
      var self = this;
      var isWrite = false, isPower = false, signal = null;
      try {
        isWrite = self.__joma_method && self.__joma_method !== "GET" && self.__joma_method !== "HEAD";
        if (isWrite && /\/api\/client\/servers\/[^\/]+\/power/.test(self.__joma_url)) {
          isPower = true;
          try { signal = JSON.parse(body).signal; } catch (e) { try { signal = String(body); } catch (e2) {} }
        }
      } catch (e) {}
      if (isWrite) progressStart();
      if (isPower) {
        self.addEventListener("loadend", function () {
          safe(function () {
            if (self.status >= 200 && self.status < 300) {
              toast({ type: "success", title: "Server", message: humanSignal(signal) + " erfolgreich." });
            } else {
              toast({ type: "error", title: "Aktion fehlgeschlagen", message: "Status " + self.status });
            }
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
      try {
        target.classList.remove("jomatheme-slide");
        void target.offsetWidth; /* reflow to restart animation */
        target.classList.add("jomatheme-slide");
      } catch (e) {}
    }
    function wrapHistory(fn) {
      return function () {
        var r = fn.apply(this, arguments);
        try { setTimeout(triggerSlide, 0); } catch (e) {}
        return r;
      };
    }
    if (history.pushState) history.pushState = wrapHistory(history.pushState);
    if (history.replaceState) history.replaceState = wrapHistory(history.replaceState);
    window.addEventListener("popstate", function () { try { triggerSlide(); } catch (e) {} });
    /* initial paint */
    setTimeout(triggerSlide, 30);
  });

  /* ---- Console auto-scroll + copy button ----------------------------- */
  safe(function () {
    function findConsole() {
      var sels = ["[class*='terminal']", "[class*='Terminal']", "[class*='console']", "#terminal", ".terminal"];
      for (var i = 0; i < sels.length; i++) {
        var el = document.querySelector(sels[i]);
        if (el) return el;
      }
      return null;
    }
    function textOf(el) {
      return (el && (el.innerText || el.textContent)) || "";
    }
    function maybeAddCopy() {
      var con = findConsole();
      if (!con || con.__jomaCopy) return;
      con.__jomaCopy = true;
      var wrap = con.parentNode;
      if (!wrap) return;
      var btn = document.createElement("button");
      btn.type = "button";
      btn.textContent = "Copy";
      btn.setAttribute("aria-label", "Copy console output");
      btn.style.cssText = "position:absolute;top:8px;right:8px;z-index:5;" +
        "padding:.25rem .6rem;font-size:.72rem;border-radius:8px;" +
        "background:rgb(255 255 255 / 0.06);color:rgb(210 210 222);" +
        "border:1px solid rgb(255 255 255 / 0.12);cursor:pointer;";
      btn.addEventListener("click", function () {
        safe(function () {
          var txt = textOf(con);
          var done = function () { btn.textContent = "✓ Copied"; setTimeout(function () { btn.textContent = "Copy"; }, 1400); };
          if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(txt).then(done, function () {});
          }
        });
      });
      if (getComputedStyle(wrap).position === "static") wrap.style.position = "relative";
      wrap.appendChild(btn);
    }
    function autoscroll() {
      var con = findConsole();
      if (!con) return;
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
    /* re-check as React mounts the console lazily */
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
    function scan(root) {
      var els = (root || document).querySelectorAll("[data-joma-count]");
      for (var i = 0; i < els.length; i++) run(els[i]);
    }
    if (document.readyState !== "loading") scan(document);
    document.addEventListener("DOMContentLoaded", function () { scan(document); });
  });

  /* ---- Welcome toast on first load ----------------------------------- */
  safe(function () {
    setTimeout(function () {
      toast({
        type: "info",
        title: "JomaTheme aktiv",
        message: "Willkommen beim JomaMC Panel-Theme. 🔥",
        duration: 4200
      });
    }, 900);
  });

})();
</script>

<script>
/* ============================================================================
   JomaTheme v1.1 runtime — button ripples + command palette (Ctrl/⌘ + K).
   Purely additive; everything is guarded so Pterodactyl is never affected.
   ============================================================================ */
(function () {
  "use strict";
  function safe(fn) { try { fn(); } catch (e) { /* silent */ } }

  /* ---- Button click ripples (event delegation, every button-like el) ---- */
  safe(function () {
    var SEL = "button, .btn, [class*='Button'], a[class*='btn'], [role='button']";
    document.addEventListener("pointerdown", function (e) {
      safe(function () {
        if (window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
        var t = e.target && e.target.closest ? e.target.closest(SEL) : null;
        if (!t) return;
        if (t.disabled) return;
        var rect = t.getBoundingClientRect();
        var size = Math.max(rect.width, rect.height) * 1.1;
        var x = (e.clientX - rect.left) - size / 2;
        var y = (e.clientY - rect.top) - size / 2;
        var r = document.createElement("span");
        r.className = "jomatheme-ripple";
        r.style.width = r.style.height = size + "px";
        r.style.left = x + "px";
        r.style.top = y + "px";
        t.appendChild(r);
        setTimeout(function () { if (r.parentNode) r.parentNode.removeChild(r); }, 680);
      });
    }, true);
  });

  /* ---- Command palette (Ctrl/⌘ + K) -------------------------------- */
  safe(function () {
    function go(p) { try { window.location.href = p; } catch (e) {} }

    var cmds = [
      { group: "Navigate", icon: "🏠", label: "Dashboard", hint: "/", run: function () { go("/"); } },
      { group: "Account",  icon: "👤", label: "Account Settings", hint: "/account", run: function () { go("/account"); } },
      { group: "Account",  icon: "🔑", label: "API Credentials", hint: "/account/api", run: function () { go("/account/api"); } },
      { group: "Account",  icon: "🧩", label: "SSH Keys", hint: "/account/ssh", run: function () { go("/account/ssh"); } },
      { group: "Actions",   icon: "✨", label: "Create Server", hint: "new", run: function () { go("/"); } },
      { group: "Actions",   icon: "🔄", label: "Reload page", hint: "refresh", run: function () { window.location.reload(); } }
    ];

    /* server-aware: if we're on /server/<id>, offer that server's tabs first */
    var m = location.pathname.match(/\/server\/([^/]+)/);
    if (m && m[1]) {
      var id = m[1];
      var tabs = [
        ["Console", ""], ["Files", "/files"], ["Backups", "/backups"],
        ["Schedules", "/schedules"], ["Users", "/users"], ["Network", "/network"],
        ["Startup", "/startup"], ["Settings", "/settings"]
      ];
      var serverCmds = tabs.map(function (t) {
        var path = "/server/" + id + t[1];
        var ic = ({
          "Console": "🖥️", "Files": "📁", "Backups": "💾", "Schedules": "⏰",
          "Users": "👥", "Network": "🌐", "Startup": "🚀", "Settings": "⚙️"
        })[t[0]];
        return { group: "Server", icon: ic, label: t[0], hint: path, run: function () { go(path); } };
      });
      cmds = serverCmds.concat(cmds);
    }

    /* admin-only commands */
    var isAdmin = false;
    try {
      var u = window.PterodactylUser;
      isAdmin = !!(u && (u.root_admin || u.rootAdmin || u.admin));
    } catch (e) {}
    if (isAdmin) {
      var admin = [
        ["Admin Dashboard", "/admin", "⚙️"], ["Admin Users", "/admin/users", "👤"],
        ["Admin Servers", "/admin/servers", "🖥️"], ["Admin Nodes", "/admin/nodes", "🌐"],
        ["Admin Locations", "/admin/locations", "📍"], ["Admin Nests", "/admin/nests", "🪆"],
        ["Admin Eggs", "/admin/eggs", "🥚"], ["Admin Databases", "/admin/databases", "🗄️"],
        ["Admin Mounts", "/admin/mounts", "📦"], ["Blueprint Extensions", "/admin/extensions", "🧩"],
        ["JomaTheme Settings", "/admin/extensions/jomatheme", "🎨"]
      ];
      admin.forEach(function (a) {
        cmds.push({ group: "Admin", icon: a[2], label: a[0], hint: a[1], run: function () { go(a[1]); } });
      });
    }

    /* build DOM */
    var root = document.createElement("div");
    root.id = "jomatheme-cmdk";
    root.innerHTML =
      '<div class="jomatheme-cmdk__backdrop" data-cmdk-close></div>' +
      '<div class="jomatheme-cmdk__panel" role="dialog" aria-modal="true" aria-label="JomaMC command palette">' +
        '<div class="jomatheme-cmdk__head">' +
          '<span class="jomatheme-cmdk__icon" aria-hidden="true">⌕</span>' +
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
      filtered = cmds.filter(function (c) {
        return !q || (c.label + " " + c.group + " " + c.hint).toLowerCase().indexOf(q) > -1;
      });
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
            '<span class="jomatheme-cmdk__item-icon">' + c.icon + '</span>' +
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
@endverbatim
