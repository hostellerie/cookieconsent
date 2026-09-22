# Cookie Consent roadmap

## 1.1.0 — Geeklog stabilization

Status: **Completed in the 1.3.0 working branch**

- standard Geeklog plugin package;
- Geeklog 2.1.1–2.2.2 and PHP 5.6–8.1 transition support;
- configuration, administration, upgrade and uninstall support;
- provider-neutral capability declaration;
- Eclipse dashboard support;
- multisite/shared-files safe defaults.

## 1.2.x — Consent engine modernization

Status: **Implemented in 1.3.0**

- self-hosted category-based consent engine;
- explicit Accept all, Reject optional and Preferences actions;
- Necessary, Analytics and Advertising categories;
- category-controlled script deferral;
- remote scripts use `data-cookieconsent-src` so they are not fetched before consent;
- permanent Manage cookies entry point;
- policy revision/versioning;
- visitor-facing strings in language files;
- keyboard-focusable native controls and ARIA dialog semantics;
- historical acknowledgement cookie detected but never promoted to granular consent;
- revocation reloads the page so previously granted third-party scripts stay blocked on the next load.

## 1.3.x — Ecosystem integration

Status: **Implemented in 1.3.0**

- bounded configuration diagnostics;
- `consent.categories.read`;
- `consent.integration.read`;
- `consent.diagnostics`;
- richer Eclipse `dashboard.summary`;
- browser diagnostics for consent-controlled scripts;
- shared integration markers for Hub/provider relationships;
- browser events for consent runtime changes;
- native Geeklog `plugin_configchange_cookieconsent()` handling for administrator configuration lifecycle changes.

## Next stabilization work

Status: **Active**

- validate fresh installation on Geeklog 2.1.1 / PHP 5.6;
- validate fresh installation on Geeklog 2.2.2 / PHP 8.1+;
- validate upgrade from the historical plugin;
- test enable/disable/re-enable and uninstall;
- test policy-version renewal;
- test inline and remote controlled scripts for each optional category;
- test consent revocation and reload;
- test shared-files staggered upgrades;
- test Eclipse/Agent/Hub consumption in real installations;
- audit accessibility with keyboard and screen-reader tooling.

## 2.0 — Post-migration baseline

Status: **Architectural concept**

After historical sites complete migration to Geeklog 2.2.2 / PHP 8.1+:

- raise the runtime baseline;
- remove transition compatibility code;
- reassess current browser privacy APIs and consent standards;
- preserve provider ownership and shared capabilities.

## Non-goals

- storing visitor marketing profiles;
- server-side consent analytics by default;
- making Agent, Eclipse or Hub dependencies;
- pretending unmarked third-party scripts can be blocked after they have already executed;
- claiming technical controls alone guarantee regulatory compliance.
