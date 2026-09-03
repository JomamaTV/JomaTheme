import React, { useEffect, useState } from 'react';

/**
 * JomaWelcome
 *
 * Injected into the dashboard via Blueprint's Components.yml at the
 * `Dashboard.Serverlist.BeforeContent` slot. Renders a premium, animated
 * "welcome back" banner above the server list.
 *
 * The component is deliberately self-contained: it reads the logged-in
 * user from the global `window.PterodactylUser` object (populated by the
 * panel wrapper) with heavy guarding, so it degrades gracefully to a
 * static greeting if the shape is unavailable.
 */
export default function JomaWelcome(): JSX.Element {
  const [name, setName] = useState<string>('');

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
      /* no-op: fall back to the static greeting */
    }
  }, []);

  return (
    <div className="jomatheme-welcome" role="region" aria-label="JomaMC dashboard welcome">
      <div className="jomatheme-welcome__aurora" aria-hidden="true" />
      <div className="jomatheme-welcome__grid" aria-hidden="true" />
      <div className="jomatheme-welcome__inner">
        <p className="jomatheme-welcome__eyebrow">
          <span className="jomatheme-welcome__dot" aria-hidden="true" />
          JomaMC &middot; Control Panel
        </p>
        <h2 className="jomatheme-welcome__title">
          Willkommen zurück{name ? <>, <span className="jomatheme-welcome__name">{name}</span></> : null}
          <span className="jomatheme-welcome__wave" aria-hidden="true"> <i className="bi bi-stars"></i></span>
        </h2>
        <p className="jomatheme-welcome__sub">
          Deine Server sind bereit — CPU, RAM und Speicher live im Blick, Aktionen einen Klick entfernt.
        </p>
        <div className="jomatheme-welcome__pills" aria-hidden="true">
          <span className="jomatheme-welcome__pill">Online-Status</span>
          <span className="jomatheme-welcome__pill">Live-Ressourcen</span>
          <span className="jomatheme-welcome__pill">Quick Actions</span>
        </div>
      </div>
    </div>
  );
}
