import React, { useEffect, useState } from 'react';

/**
 * JomaWelcome — premium dashboard hero (JomaTheme v4.0 · Midnight Marine)
 *
 * Injected via Blueprint's Components.yml at the `Dashboard.Serverlist.BeforeContent`
 * slot. Renders the "JOMAMC · CONTROL PANEL" hero above the server list with a
 * live server count + online count pulled from the real Pterodactyl client API.
 *
 * Everything degrades gracefully:
 *  - username falls back to a static greeting if `window.PterodactylUser` is missing
 *  - stats are fetched best-effort; if the API call fails the stats row is hidden
 *  - "online" is computed from each server's /resources state (capped to avoid
 *    runaway requests); if any step fails it shows an em-dash
 *  - the fetch is abortable and never blocks Pterodactyl's own rendering
 */

interface ServerAttributes {
  identifier?: string;
  name?: string;
}
interface ApiServersResponse {
  data?: Array<{ attributes?: ServerAttributes }>;
}
interface ResourcesResponse {
  attributes?: {
    current_state?: string;
    is_suspended?: boolean;
  };
}

type Stats = { total: number | null; online: number | null };

/** ES2019-safe settle helper (replaces Promise.allSettled, which needs ES2020). */
async function settle<T>(p: Promise<T>): Promise<{ ok: boolean; value: T | null }> {
  try {
    return { ok: true, value: await p };
  } catch {
    return { ok: false, value: null };
  }
}

const QUICK_ACTIONS: Array<{ label: string; href: string; icon: string; primary?: boolean }> = [
  { label: 'Neuen Server erstellen', href: '/', icon: 'bi-plus-circle', primary: true },
  { label: 'Account', href: '/account', icon: 'bi-person' },
  { label: 'API Keys', href: '/account/api', icon: 'bi-key' },
];

export default function JomaWelcome(): JSX.Element {
  const [name, setName] = useState<string>('');
  const [stats, setStats] = useState<Stats>({ total: null, online: null });
  const [loading, setLoading] = useState<boolean>(true);

  // --- username ---
  useEffect(() => {
    try {
      const w = window as unknown as { PterodactylUser?: Record<string, unknown> };
      const user = w.PterodactylUser;
      if (user && typeof user === 'object') {
        const candidate =
          (user.username as string | undefined) ||
          (user.first_name as string | undefined) ||
          (user.name as string | undefined) ||
          '';
        const cleaned = String(candidate).trim();
        if (cleaned) setName(cleaned);
      }
    } catch {
      /* fall back to static greeting */
    }
  }, []);

  // --- live stats (real Pterodactyl data, best-effort) ---
  useEffect(() => {
    const controller = new AbortController();
    let mounted = true;

    async function loadStats(): Promise<void> {
      try {
        const listRes = await fetch('/api/client/servers?per_page=500', {
          headers: { Accept: 'application/json' },
          credentials: 'include',
          signal: controller.signal,
        });
        if (!listRes.ok) throw new Error('servers list failed');
        const json = (await listRes.json()) as ApiServersResponse;
        const servers = (json && json.data) || [];
        const total = servers.length;

        // count "running" servers via per-server resources (capped for perf)
        const MAX = 25;
        const sample = servers.slice(0, MAX);
        const states = await Promise.all(
          sample.map(async (s) =>
            settle(
              (async () => {
                const id = s.attributes && s.attributes.identifier;
                if (!id) return null;
                const r = await fetch(`/api/client/servers/${id}/resources`, {
                  headers: { Accept: 'application/json' },
                  credentials: 'include',
                  signal: controller.signal,
                });
                if (!r.ok) return null;
                const body = (await r.json()) as ResourcesResponse;
                return body.attributes && body.attributes.current_state ? body.attributes.current_state : null;
              })(),
            ),
          ),
        );
        let online = 0;
        let counted = 0;
        for (const r of states) {
          if (r.ok && r.value !== null) {
            counted++;
            if (r.value === 'running') online++;
          }
        }
        if (!mounted) return;
        // If we sampled fewer than total, online is a partial estimate — show total honestly
        // and online only when we counted all of them.
        const onlineFinal = counted === total ? online : null;
        setStats({ total, online: onlineFinal });
        setLoading(false);
      } catch {
        if (!mounted) return;
        setStats({ total: null, online: null });
        setLoading(false);
      }
    }

    loadStats();
    return () => {
      mounted = false;
      controller.abort();
    };
  }, []);

  const onlineLabel =
    stats.online === null ? '–' : stats.online === 0 ? '0' : String(stats.online);

  return (
    <div className="jomatheme-welcome" role="region" aria-label="JomaMC dashboard welcome">
      <div className="jomatheme-welcome__aurora" aria-hidden="true" />
      <div className="jomatheme-welcome__grid" aria-hidden="true" />

      {/* decorative sparkline */}
      <svg className="jomatheme-welcome__spark" width="160" height="56" viewBox="0 0 160 56" fill="none" aria-hidden="true">
        <path
          d="M2 44 L20 38 L38 42 L56 26 L74 32 L92 18 L110 24 L128 12 L146 20 L158 8"
          stroke="rgb(56 189 248)"
          strokeWidth="2.5"
          strokeLinecap="round"
          strokeLinejoin="round"
          fill="none"
        />
        <circle cx="158" cy="8" r="3.5" fill="rgb(45 212 191)" />
      </svg>

      <div className="jomatheme-welcome__inner">
        <p className="jomatheme-welcome__eyebrow">
          <span className="jomatheme-welcome__dot" aria-hidden="true" />
          JomaMC &middot; Control Panel
        </p>

        <h2 className="jomatheme-welcome__title">
          Willkommen zurück
          {name ? (
            <>
              {', '}
              <span className="jomatheme-welcome__name">{name}</span>
            </>
          ) : null}
          <span className="jomatheme-welcome__sparkle" aria-hidden="true">
            {' '}
            <i className="bi bi-stars" />
          </span>
        </h2>

        <p className="jomatheme-welcome__sub">
          Deine Infrastruktur auf einen Blick — Server, Ressourcen und Aktionen einen Klick entfernt.
        </p>

        {(stats.total !== null || loading) && (
          <div className="jomatheme-welcome__stats" aria-live="polite">
            <div className="jomatheme-welcome__stat">
              <i className="bi bi-hdd-stack jomatheme-welcome__stat-icon" aria-hidden="true" />
              <span>
                <span className="jomatheme-welcome__stat-num">
                  {loading ? <span className="jomatheme-spinner" aria-label="lädt" /> : stats.total ?? '–'}
                </span>
                <span className="jomatheme-welcome__stat-label"> Server</span>
              </span>
            </div>
            <div className="jomatheme-welcome__stat">
              <i className="bi bi-broadcast jomatheme-welcome__stat-icon" aria-hidden="true" />
              <span>
                <span className="jomatheme-welcome__stat-num">{loading ? <span className="jomatheme-spinner" aria-label="lädt" /> : onlineLabel}</span>
                <span className="jomatheme-welcome__stat-label"> Online</span>
              </span>
            </div>
          </div>
        )}

        <div className="jomatheme-welcome__actions">
          {QUICK_ACTIONS.map((a) => (
            <a
              key={a.href}
              href={a.href}
              className={`jomatheme-welcome__btn${a.primary ? ' jomatheme-welcome__btn--primary' : ''}`}
            >
              <i className={`bi ${a.icon}`} aria-hidden="true" /> {a.label}
            </a>
          ))}
        </div>
      </div>
    </div>
  );
}
