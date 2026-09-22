# Release notes

## Cookie Consent 1.3.0 — Unreleased

Cookie Consent 1.3.0 combines the original stabilization work with the consent-engine and ecosystem-integration work previously planned for 1.2.x and 1.3.x.

### Consent engine

- Replaces the historical acknowledgement-only Silktide behavior with a self-hosted category-based consent interface.
- Adds explicit **Accept all**, **Reject optional** and **Preferences** actions.
- Adds Necessary, Analytics and Advertising categories.
- Necessary remains always active; optional categories can be enabled/disabled by the administrator.
- Adds a permanent **Manage cookies** button so visitors can revise their choice.
- Adds consent policy versioning: changing the version requires a new choice.
- Stores the current choice client-side in `cookieconsent_preferences`.
- Detects the historical `cookieconsent_dismissed` cookie but does not reinterpret it as category consent.
- Removes the legacy acknowledgement cookie once a new explicit choice is saved.

### Controlled scripts

- Adds `type="text/plain" data-cookieconsent="analytics|advertising"` integration.
- Adds `data-cookieconsent-src` for remote scripts so they are not fetched before consent.
- Adds the PHP helper `cookieconsent_script_attributes()`.
- Activates allowed scripts after the initial choice.
- Reloads the page when an existing choice is changed, allowing revocation to take effect from the next document load.

### Browser integration

Adds `window.GeeklogCookieConsent` with preference, consent, activation and diagnostic methods.

Adds browser events:

- `cookieconsent:ready`
- `cookieconsent:change`
- `cookieconsent:category-activated`

### Geeklog / ecosystem integration

Capabilities now include:

- `consent.status`
- `consent.policy.read`
- `consent.categories.read`
- `consent.integration.read`
- `consent.diagnostics`
- `dashboard.summary`

Eclipse receives richer provider-owned metrics and configuration warnings. Agent and Hub can consume the same read-only contracts without plugin-specific SQL.

The plugin also implements Geeklog's native `plugin_configchange_cookieconsent()` callback for administrator configuration lifecycle changes.

### Configuration

Adds:

- consent policy version;
- Analytics category toggle;
- Advertising category toggle;
- permanent Manage cookies button toggle.

The default consent lifetime is 180 days for new installations. Existing configured values are preserved during upgrade.

### Compatibility

- Geeklog 2.1.1 through 2.2.2.
- PHP 5.6 through 8.1.
- No custom database tables.
- No server-side visitor consent history.

### Important integration requirement

Cookie Consent can defer only scripts that are marked as consent-controlled before browser execution. Existing integrations that inject ordinary executable `<script>` tags must be adapted to the documented marker contract.
