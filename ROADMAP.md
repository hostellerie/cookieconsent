# Cookie Consent roadmap

## 1.1.0 — Stabilization and Geeklog modernization

Status: **Active**

- restore the standard Geeklog plugin package tree;
- support Geeklog 2.1.1–2.2.2 and PHP 5.6–8.1;
- add Geeklog configuration and a useful administration page;
- remove anonymous-only behavior by default while preserving an option for legacy deployments;
- use safe PHP-to-JavaScript serialization;
- make upgrade/configuration migration explicit and repeatable;
- declare shared provider capabilities;
- expose read-only status/policy services for Agent and Hub;
- expose `dashboard.summary` for Eclipse;
- document the notice-only limitation instead of implying full consent compliance;
- verify install, upgrade, enable/disable, uninstall and multisite/shared-files behavior.

## 1.2.x — Consent engine modernization

Status: **Planned**

- replace the historical Silktide notice engine with a maintained, self-hostable consent implementation;
- support explicit accept, reject and preference actions;
- support configurable categories such as necessary, analytics and advertising;
- defer category-controlled scripts until the relevant choice is granted;
- provide a permanent “manage consent” entry point;
- support consent revision/versioning so policy changes can request a new choice;
- keep all visitor-facing strings in language files;
- preserve accessible keyboard/screen-reader behavior;
- provide migration from the historical acknowledgement cookie where appropriate without falsely converting acknowledgement into granular consent.

## 1.3.x — Ecosystem integration

Status: **Planned**

- add bounded diagnostics for detected consent-controlled integrations;
- expose category/consent configuration through shared provider-neutral read capabilities;
- allow Hub to report relationships between consent categories and compatible providers without querying their private tables;
- expose richer Eclipse alerts for incomplete privacy URL/category configuration;
- define optional lifecycle events for consent-configuration changes, not individual visitor tracking.

## 2.0 — Post-migration baseline

Status: **Architectural concept**

After the historical sites have completed the Geeklog 2.2.2 / PHP 8.1 migration:

- raise the minimum runtime baseline to Geeklog 2.2.2 and PHP 8.1+;
- remove transition compatibility code that is no longer needed;
- evaluate modern browser privacy APIs and current consent standards at that time;
- keep consent state owned by this provider and consumed through shared capabilities rather than consumer-specific APIs.

## Non-goals

- storing marketing profiles or consent analytics by default;
- making Agent, Eclipse or Hub mandatory dependencies;
- claiming that a banner alone guarantees regulatory compliance;
- adding direct dependencies on a specific advertising or analytics provider.
