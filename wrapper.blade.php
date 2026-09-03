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
@endverbatim
