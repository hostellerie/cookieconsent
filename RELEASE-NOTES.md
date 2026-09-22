# Release notes

## Cookie Consent 1.1.0 — Unreleased

Cookie Consent 1.1.0 re-establishes the old 2015 plugin as a maintainable Geeklog package and prepares it for the current Geeklog interoperability architecture.

### Changed

- Rebuilt the repository using the standard Geeklog plugin source layout.
- Raised the declared minimum Geeklog version to 2.1.1 while keeping the transition target through Geeklog 2.2.2.
- Kept code compatible with PHP 5.6–8.1.
- Added a Geeklog configuration group for logged-in-user display, privacy URL and acknowledgement lifetime.
- Added a real administration/status page with direct access to configuration.
- The banner is shown to all visitors by default; legacy anonymous-only behavior remains configurable.
- JavaScript options are now passed through JSON serialization instead of string concatenation.
- Upgrade handling now initializes/repairs configuration before recording the new plugin version.

### Interoperability

- Added `plugin_getcapabilities_cookieconsent()`.
- Added read-only `consent.status` service.
- Added read-only `consent.policy.read` service.
- Added admin-only `dashboard.summary` for generic Eclipse dashboard discovery.
- The same contracts are reusable by Agent, Hub and future consumers; none of them is a plugin dependency.

### Documentation and safety

- Added README, roadmap, release notes and a static `plugin.json` manifest.
- Documented multisite/shared-files behavior and transition-safe defaults.
- Explicitly documented that the bundled historical Silktide engine is notice-only and does not block/category-control third-party cookies before acknowledgement.

### Upgrade note

The historical `cookieconsent_dismissed=yes` cookie remains recognized by the bundled front-end library. Version 1.1.0 does not reinterpret that acknowledgement as granular consent.
