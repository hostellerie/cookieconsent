# Cookie Consent plugin for Geeklog

Cookie Consent is a small Geeklog infrastructure plugin that displays a cookie/privacy notice and exposes its status through the shared interoperability conventions documented in `hostellerie/memorandum`.

## Current compatibility target

- Geeklog 2.1.1 through 2.2.2
- PHP 5.6 through 8.1

This is the transition baseline used by the current Geeklog plugin modernization work. Newer-only syntax is intentionally avoided.

## Important limitation of 1.1.0

Version 1.1.0 keeps the historical Silktide Cookie Consent 2 front-end engine so existing sites can migrate without an abrupt behavior change.

That engine is **notice-only**: it records that the visitor dismissed/acknowledged the banner, but it does not itself block, categorize, or defer third-party scripts and cookies before acknowledgement. It must therefore not be described as a complete consent-management platform or as automatic legal compliance.

A category-based consent engine and script-control layer are planned separately; see `ROADMAP.md`.

## Geeklog package layout

The repository follows the standard Geeklog plugin source layout used by the modernized plugins:

```text
autoinstall.php
functions.inc
install_defaults.php
language/
admin/
public_html/
```

The Geeklog plugin installer copies the plugin root to `plugins/cookieconsent/`, `admin/` to `public_html/admin/plugins/cookieconsent/`, and `public_html/` to `public_html/cookieconsent/`.

## Configuration

The plugin adds a Geeklog configuration group with:

- display to logged-in users;
- privacy-information URL;
- acknowledgement lifetime in days.

The administration page provides a configuration button and explicitly reports the current notice-only mode.

## Shared capabilities

The plugin declares one provider-owned capability set:

```text
consent.status
consent.policy.read
dashboard.summary
```

Roles:

```text
service
infrastructure
```

These declarations are intentionally consumer-neutral. Agent, Eclipse, Hub and future consumers can reuse the same provider contract without querying plugin internals or maintaining separate plugin-specific registries.

### Services

`consent.status`
: Read-only implementation/runtime status. It exposes the banner mode, acknowledgement cookie name, configured lifetime, audience and current-request acknowledgement state.

`consent.policy.read`
: Read-only banner/policy presentation metadata. It does not expose private user data.

`dashboard.summary`
: Admin-only operational summary for Eclipse and other compatible administration dashboards. It reports the legacy mode as a warning and links back to plugin management/configuration.

## Multisite and shared files

The plugin stores no custom database tables or persistent files. Configuration is resolved through the active Geeklog site context. No sibling-site paths or global cross-site state are introduced.

When shared plugin files are deployed before every site has run the 1.1.0 upgrade, the runtime uses safe defaults when the new configuration values are not yet present. This keeps staggered site upgrades viable.

## Security and privacy

- Administration requires `cookieconsent.admin`.
- Configuration access is mapped through `config.cookieconsent.tab_main`.
- Shared services are read-only.
- The dashboard service re-checks the plugin administration permission.
- JavaScript options are serialized with `json_encode()` rather than interpolating raw translated/configured values into JavaScript.
- No consent or visitor history is stored in plugin database tables.

## Origin

The original plugin package dates from 2015 and bundled Silktide Cookie Consent 2 assets. Version 1.1.0 is a stabilization/modernization release rather than a claim that the historical JavaScript library is a modern CMP.
