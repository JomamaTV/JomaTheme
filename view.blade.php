@extends('layouts.admin')

@section('title')
    JomaTheme
@endsection

@section('content-header')
    <h1 class="page-header">JomaTheme <small>JomaMC Panel Theme</small></h1>
@endsection

@section('content')
@verbatim
<style>
.jomatheme-admin { --ja-bg: 9 9 16; --ja-primary: 124 92 252; color: rgb(236 236 245); }
.jomatheme-admin * { box-sizing: border-box; }

.ja-hero {
  position: relative; overflow: hidden; border-radius: 18px;
  padding: 2rem 2.25rem; margin-bottom: 1.5rem;
  border: 1px solid rgb(255 255 255 / 0.1);
  background: linear-gradient(135deg, rgb(124 92 252 / 0.16), rgb(56 189 248 / 0.06) 55%, transparent);
  box-shadow: 0 18px 50px rgb(0 0 0 / 0.45);
}
.ja-hero::before {
  content: ""; position: absolute; inset: -50% -10% auto -10%; height: 200%;
  background:
    radial-gradient(40% 60% at 15% 20%, rgb(124 92 252 / 0.5), transparent 70%),
    radial-gradient(40% 60% at 85% 15%, rgb(56 189 248 / 0.4), transparent 70%),
    radial-gradient(50% 60% at 60% 95%, rgb(217 70 239 / 0.3), transparent 70%);
  filter: blur(46px); opacity: 0.5; z-index: 0;
  animation: ja-drift 14s ease-in-out infinite alternate;
}
.ja-hero::after {
  content: ""; position: absolute; inset: 0; z-index: 0;
  background-image: linear-gradient(rgb(255 255 255 / 0.04) 1px, transparent 1px),
                    linear-gradient(90deg, rgb(255 255 255 / 0.04) 1px, transparent 1px);
  background-size: 30px 30px;
  mask-image: radial-gradient(120% 90% at 0% 0%, #000 30%, transparent 75%);
  -webkit-mask-image: radial-gradient(120% 90% at 0% 0%, #000 30%, transparent 75%);
}
.ja-hero__inner { position: relative; z-index: 1; display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap; }
.ja-mark {
  width: 64px; height: 64px; border-radius: 18px; display: grid; place-items: center;
  background: linear-gradient(135deg, rgb(124 92 252), rgb(56 189 248));
  box-shadow: 0 12px 30px rgb(124 92 252 / 0.5), inset 0 1px 0 rgb(255 255 255 / 0.3);
  font-size: 1.9rem; font-weight: 800; color: #fff; flex: 0 0 auto;
  animation: ja-float 5s ease-in-out infinite;
}
.ja-hero h1 {
  margin: 0; font-size: 1.8rem; font-weight: 800; letter-spacing: -0.02em;
  background: linear-gradient(120deg, #fff, rgb(198 179 254) 55%, rgb(56 189 248));
  -webkit-background-clip: text; background-clip: text; color: transparent;
}
.ja-hero p { margin: .25rem 0 0; color: rgb(180 180 198); font-size: .95rem; }
.ja-pill {
  margin-left: auto; font-size: .72rem; font-weight: 700; padding: .35rem .8rem;
  border-radius: 999px; color: rgb(34 197 94); background: rgb(34 197 94 / 0.12);
  border: 1px solid rgb(34 197 94 / 0.35); display: inline-flex; align-items: center; gap: .4rem;
}
.ja-pill__dot { width: 7px; height: 7px; border-radius: 999px; background: rgb(34 197 94); box-shadow: 0 0 0 4px rgb(34 197 94 / 0.2); animation: ja-pulse 2s infinite; }

.ja-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.ja-stat {
  border-radius: 14px; padding: 1.1rem 1.2rem; border: 1px solid rgb(255 255 255 / 0.08);
  background: rgb(28 28 42 / 0.6); backdrop-filter: blur(12px);
  transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
}
.ja-stat:hover { transform: translateY(-2px); border-color: rgb(124 92 252 / 0.3); box-shadow: 0 14px 34px rgb(124 92 252 / 0.15); }
.ja-stat__label { font-size: .7rem; text-transform: uppercase; letter-spacing: .1em; color: rgb(150 150 170); margin: 0 0 .35rem; }
.ja-stat__value { font-size: 1.15rem; font-weight: 700; color: rgb(248 248 252); margin: 0; }
.ja-stat__hint { font-size: .78rem; color: rgb(140 140 160); margin: .25rem 0 0; }

.ja-section { border-radius: 14px; padding: 1.4rem 1.5rem; margin-bottom: 1.5rem; border: 1px solid rgb(255 255 255 / 0.08); background: rgb(24 24 36 / 0.55); backdrop-filter: blur(12px); }
.ja-section h2 { margin: 0 0 1rem; font-size: 1.05rem; font-weight: 700; color: rgb(248 248 252); display: flex; align-items: center; gap: .5rem; }
.ja-section h2::before { content: ""; width: 4px; height: 16px; border-radius: 4px; background: linear-gradient(rgb(124 92 252), rgb(56 189 248)); }

.ja-features { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: .75rem; }
.ja-feature { display: flex; gap: .75rem; align-items: flex-start; padding: .8rem .9rem; border-radius: 12px; background: rgb(255 255 255 / 0.03); border: 1px solid rgb(255 255 255 / 0.06); transition: border-color .2s ease, background .2s ease; }
.ja-feature:hover { border-color: rgb(124 92 252 / 0.3); background: rgb(124 92 252 / 0.06); }
.ja-feature__icon { font-size: 1.1rem; line-height: 1.4; }
.ja-feature__t { font-weight: 600; color: rgb(236 236 245); font-size: .9rem; }
.ja-feature__d { color: rgb(150 150 170); font-size: .82rem; margin: .1rem 0 0; }

.ja-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .6rem 0; border-bottom: 1px solid rgb(255 255 255 / 0.06); }
.ja-row:last-child { border-bottom: 0; }
.ja-row__label { color: rgb(190 190 206); font-size: .9rem; }
.ja-row__value { color: rgb(248 248 252); font-weight: 600; font-size: .88rem; }
.ja-code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: .82rem; background: rgb(8 8 14 / 0.6); border: 1px solid rgb(255 255 255 / 0.08); border-radius: 8px; padding: .15rem .45rem; color: rgb(198 179 254); }

.ja-preview { display: flex; gap: .75rem; align-items: center; flex-wrap: wrap; margin-top: .5rem; }
.ja-swatch { width: 30px; height: 30px; border-radius: 8px; border: 1px solid rgb(255 255 255 / 0.15); box-shadow: 0 4px 12px rgb(0 0 0 / 0.4); }
.ja-color { display: flex; align-items: center; gap: .6rem; }
.ja-color input[type="color"] { width: 42px; height: 30px; border: 1px solid rgb(255 255 255 / 0.15); border-radius: 8px; background: none; cursor: pointer; padding: 0; }
.ja-note { font-size: .8rem; color: rgb(140 140 160); margin: .8rem 0 0; line-height: 1.5; }
.ja-note code { background: rgb(8 8 14 / 0.6); border: 1px solid rgb(255 255 255 / 0.08); border-radius: 6px; padding: .05rem .35rem; color: rgb(198 179 254); font-size: .78rem; }

.ja-btn { display: inline-flex; align-items: center; gap: .4rem; padding: .45rem .9rem; border-radius: 10px; font-size: .82rem; font-weight: 600; cursor: pointer; border: 1px solid rgb(124 92 252 / 0.5); background: linear-gradient(135deg, rgb(124 92 252), rgb(91 63 211)); color: #fff; box-shadow: 0 8px 22px rgb(124 92 252 / 0.32); transition: filter .2s ease, transform .2s ease; }
.ja-btn:hover { filter: brightness(1.08); transform: translateY(-1px); }

@keyframes ja-drift { 0% { transform: translate3d(-3%, -2%, 0) scale(1.05); } 50% { transform: translate3d(5%, 4%, 0) scale(1.12); } 100% { transform: translate3d(-3%, -2%, 0) scale(1.05); } }
@keyframes ja-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
@keyframes ja-pulse { 0%, 100% { box-shadow: 0 0 0 0 rgb(34 197 94 / 0.5); } 50% { box-shadow: 0 0 0 6px rgb(34 197 94 / 0); } }
@media (prefers-reduced-motion: reduce) { .ja-hero::before, .ja-mark, .ja-pill__dot { animation: none; } }
</style>
@endverbatim

<div class="jomatheme-admin">

  <div class="ja-hero">
    <div class="ja-hero__inner">
      <div class="ja-mark">J</div>
      <div>
        <h1>JomaTheme</h1>
        <p>Modern dark SaaS-style theme for JomaMC — glassmorphism, animated aurora, layout slides &amp; live notifications.</p>
      </div>
      <span class="ja-pill"><span class="ja-pill__dot"></span> Active</span>
    </div>
  </div>

  <div class="ja-grid">
    <div class="ja-stat">
      <p class="ja-stat__label">Version</p>
      <p class="ja-stat__value">1.1.0</p>
      <p class="ja-stat__hint">Premium button system · command palette</p>
    </div>
    <div class="ja-stat">
      <p class="ja-stat__label">Blueprint Target</p>
      <p class="ja-stat__value">beta-2026-08</p>
      <p class="ja-stat__hint">Required framework</p>
    </div>
    <div class="ja-stat">
      <p class="ja-stat__label">Pterodactyl</p>
      <p class="ja-stat__value">1.15.1</p>
      <p class="ja-stat__hint">Compatible</p>
    </div>
    <div class="ja-stat">
      <p class="ja-stat__label">Identifier</p>
      <p class="ja-stat__value">jomatheme</p>
      <p class="ja-stat__hint">Extension id</p>
    </div>
  </div>

  <div class="ja-section">
    <h2>Features</h2>
    <div class="ja-features">
      <div class="ja-feature"><span class="ja-feature__icon">🌌</span><div><div class="ja-feature__t">Animated Aurora Background</div><div class="ja-feature__d">Drifting gradient mesh on every page.</div></div></div>
      <div class="ja-feature"><span class="ja-feature__icon">🪟</span><div><div class="ja-feature__t">Glassmorphism Surfaces</div><div class="ja-feature__d">Frosted cards, blur &amp; subtle borders.</div></div></div>
      <div class="ja-feature"><span class="ja-feature__icon">✨</span><div><div class="ja-feature__t">Layout Slide Transitions</div><div class="ja-feature__d">Content slides on every route change.</div></div></div>
      <div class="ja-feature"><span class="ja-feature__icon">🔔</span><div><div class="ja-feature__t">Toast Notifications</div><div class="ja-feature__d">Power actions surface as toasts.</div></div></div>
      <div class="ja-feature"><span class="ja-feature__icon">⏳</span><div><div class="ja-feature__t">Loading States</div><div class="ja-feature__d">Top progress bar + skeleton shimmer.</div></div></div>
      <div class="ja-feature"><span class="ja-feature__icon">🖥️</span><div><div class="ja-feature__t">Console Polish</div><div class="ja-feature__d">Auto-scroll &amp; one-click copy.</div></div></div>
      <div class="ja-feature"><span class="ja-feature__icon">🎯</span><div><div class="ja-feature__t">Live Welcome Banner</div><div class="ja-feature__d">Personalised dashboard header.</div></div></div>
      <div class="ja-feature"><span class="ja-feature__icon">♿</span><div><div class="ja-feature__t">Accessible &amp; Responsive</div><div class="ja-feature__d">Reduced-motion guard, 320px+.</div></div></div>
    </div>
  </div>

  <div class="ja-section">
    <h2>Theme Settings</h2>
    <div class="ja-row">
      <span class="ja-row__label">Primary color (live preview)</span>
      <div class="ja-color">
        <input type="color" id="ja-primary" value="#7c5cfc" aria-label="Primary color live preview">
        <span class="ja-row__value" id="ja-primary-val">#7C5CFC</span>
      </div>
    </div>
    <div class="ja-row">
      <span class="ja-row__label">Border radius</span>
      <span class="ja-row__value">16px</span>
    </div>
    <div class="ja-row">
      <span class="ja-row__label">Glass effect</span>
      <span class="ja-row__value">Enabled</span>
    </div>
    <div class="ja-row">
      <span class="ja-row__label">Animations</span>
      <span class="ja-row__value">Enabled (reduced-motion aware)</span>
    </div>
    <div class="ja-preview" aria-hidden="true">
      <span class="ja-swatch" id="ja-sw1" style="background:#7c5cfc"></span>
      <span class="ja-swatch" id="ja-sw2" style="background:#38bdf8"></span>
      <span class="ja-swatch" style="background:#22c55e"></span>
      <span class="ja-swatch" style="background:#f59e0b"></span>
      <span class="ja-swatch" style="background:#ef4444"></span>
    </div>
    <p class="ja-note">
      This page live-previews the accent color for the current session. To make a change permanent, edit the
      <code>--blueprint-primary-*</code> values in <code>dashboard.css</code> (JomaMC palette section) and run
      <code>blueprint -build</code>. A persisted settings controller can be added via the <code>admin.controller</code> + <code>database.migrations</code> fields.
    </p>
  </div>

  <div class="ja-section">
    <h2>Files &amp; Installation</h2>
    <div class="ja-row"><span class="ja-row__label">Dev folder</span><span class="ja-code">.blueprint/dev/</span></div>
    <div class="ja-row"><span class="ja-row__label">Build (live)</span><span class="ja-code">blueprint -build</span></div>
    <div class="ja-row"><span class="ja-row__label">Export package</span><span class="ja-code">blueprint -export</span></div>
    <div class="ja-row"><span class="ja-row__label">Remove</span><span class="ja-code">blueprint -remove jomatheme</span></div>
    <p class="ja-note">After install/update run <code>sudo -u www-data php artisan optimize:clear</code> so cached views pick up the themed wrapper.</p>
  </div>

</div>

@verbatim
<script>
(function () {
  "use strict";
  function hexToRgb(h) { h = h.replace("#", ""); if (h.length === 3) h = h.split("").map(function (c) { return c + c; }).join(""); var n = parseInt(h, 16); return [n >> 16 & 255, n >> 8 & 255, n & 255]; }
  function shade(rgb, f) { return rgb.map(function (v) { return Math.max(0, Math.min(255, Math.round(v * f))); }); }
  function toCss(rgb) { return rgb[0] + " " + rgb[1] + " " + rgb[2]; }
  function apply(hex) {
    var base = hexToRgb(hex);
    var scale = { 400: 1.18, 500: 1.0, 600: 0.87, 700: 0.73 };
    var root = document.documentElement;
    Object.keys(scale).forEach(function (b) { root.style.setProperty("--blueprint-primary-" + b, toCss(shade(base, scale[b]))); });
    var val = document.getElementById("ja-primary-val"); if (val) val.textContent = hex.toUpperCase();
    var sw1 = document.getElementById("ja-sw1"); if (sw1) sw1.style.background = hex;
  }
  var input = document.getElementById("ja-primary");
  if (input) input.addEventListener("input", function () { apply(input.value); });
})();
</script>
@endverbatim
@endsection
