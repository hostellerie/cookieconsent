# Cookie Consent plugin for Geeklog

Cookie Consent is a self-hosted Geeklog consent-management plugin for the current modernization baseline:

- Geeklog 2.1.1 through 2.2.2
- PHP 5.6 through 8.1

The working branch now implements the functionality originally planned for 1.2.x and 1.3.x and is prepared as **Cookie Consent 1.3.0**.

## Consent model

The plugin provides three categories:

- **Necessary** — always active.
- **Analytics** — optional and configurable.
- **Advertising** — optional and configurable.

Visitors can:

- accept all optional categories;
- reject all optional categories;
- choose categories individually;
- reopen their preferences at any time through the permanent Manage cookies button.

The choice is stored client-side in `cookieconsent_preferences` together with the configured policy version. Changing the policy version causes the visitor to be asked again.

The historical `cookieconsent_dismissed=yes` acknowledgement is detected but is **never converted into consent**. The visitor is asked to make a new explicit choice, and the historical cookie is removed after the new choice is saved.

## Blocking controlled scripts

Cookie Consent can prevent a third-party script from executing **only when the provider/theme marks that script as consent-controlled before it reaches the browser**.

Inline analytics example:

```html
<script type="text/plain" data-cookieconsent="analytics">
  // analytics code
</script>
```

Remote advertising example:

```html
<script
  type="text/plain"
  data-cookieconsent="advertising"
  data-cookieconsent-src="https://example.com/ad.js">
</script>
```

Do not put the remote URL in `src`: use `data-cookieconsent-src` so the browser does not fetch it before consent.

PHP providers can also use:

```php
cookieconsent_script_attributes('analytics');
cookieconsent_script_attributes('advertising', $remoteUrl);
```

When the relevant category is granted, the plugin activates the controlled script. If an already granted category is later revoked, the page reloads because previously executed third-party JavaScript cannot be undone safely in-place.

## Browser API and events

The runtime exposes:

```text
window.GeeklogCookieConsent.openPreferences()
window.GeeklogCookieConsent.getPreferences()
window.GeeklogCookieConsent.hasConsent(category)
window.GeeklogCookieConsent.activateAllowedScripts()
window.GeeklogCookieConsent.getDiagnostics()
```

Browser events:

```text
cookieconsent:ready
cookieconsent:change
cookieconsent:category-activated
```

No visitor consent history is stored in Geeklog database tables by this plugin.

## Geeklog configuration

Configuration includes:

- display for logged-in users;
- privacy-information URL;
- consent lifetime;
- consent policy version;
- Analytics category enabled/disabled;
- Advertising category enabled/disabled;
- permanent Manage cookies button enabled/disabled.

Administrator configuration changes are observed through Geeklog's native `plugin_configchange_cookieconsent()` callback. Individual visitor choices are deliberately not written to the Geeklog log.

## Shared capabilities

The plugin declares:

```text
consent.status
consent.policy.read
consent.categories.read
consent.integration.read
consent.diagnostics
dashboard.summary
```

Roles:

```text
service
infrastructure
```

These provider-owned contracts can be reused independently by Agent, Eclipse, Hub and future consumers.

### Eclipse

`dashboard.summary` reports:

- category-consent mode;
- current policy version;
- number of enabled optional categories;
- consent lifetime;
- alerts for incomplete configuration.

### Agent and Hub

Read-only services expose policy, categories, diagnostics and the integration contract without exposing visitor-specific history.

`consent.integration.read` also exposes the category relationship markers Hub can use to describe relationships between consent categories and cooperating providers without querying private plugin tables.

## Multisite / shared files

The plugin has no custom tables or persistent files. All server-side configuration is resolved in the active Geeklog site context.

New code falls back safely when newly introduced configuration keys are missing, so shared plugin files can be deployed before every site has completed its plugin upgrade.

## Compliance scope

This plugin provides technical consent controls. Actual legal compliance still depends on how the site classifies integrations, which scripts are correctly placed behind categories, the privacy information presented to visitors, and the applicable jurisdiction.
