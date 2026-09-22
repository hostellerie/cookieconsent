(function () {
  'use strict';

  if (window.GeeklogCookieConsent) {
    return;
  }

  var options = window.geeklogCookieConsentOptions || {};
  var cookieName = options.cookieName || 'cookieconsent_preferences';
  var legacyCookieName = options.legacyCookieName || 'cookieconsent_dismissed';
  var policyVersion = String(options.policyVersion || '1');
  var categories = options.categories || {};
  var strings = options.strings || {};
  var current = null;
  var initialChoice = false;
  var dialog = null;
  var manageButton = null;

  function getCookie(name) {
    var prefix = name + '=';
    var parts = document.cookie ? document.cookie.split(';') : [];
    for (var i = 0; i < parts.length; i++) {
      var value = parts[i].replace(/^\s+/, '');
      if (value.indexOf(prefix) === 0) {
        return value.substring(prefix.length);
      }
    }
    return '';
  }

  function setCookie(name, value, days) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (days * 86400000));
    var cookie = name + '=' + encodeURIComponent(value)
      + '; expires=' + expires.toUTCString()
      + '; path=/; SameSite=Lax';
    if (window.location.protocol === 'https:') {
      cookie += '; Secure';
    }
    document.cookie = cookie;
  }

  function expireCookie(name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax';
  }

  function normalizePreferences(data) {
    var normalized = {
      version: policyVersion,
      necessary: true,
      analytics: false,
      advertising: false,
      updated: 0
    };

    if (!data || String(data.version || '') !== policyVersion) {
      return normalized;
    }

    if (categories.analytics && data.analytics === true) {
      normalized.analytics = true;
    }
    if (categories.advertising && data.advertising === true) {
      normalized.advertising = true;
    }
    normalized.updated = parseInt(data.updated, 10) || 0;

    return normalized;
  }

  function readPreferences() {
    var raw = getCookie(cookieName);
    if (!raw) {
      return null;
    }
    try {
      var parsed = JSON.parse(decodeURIComponent(raw));
      if (String(parsed.version || '') !== policyVersion) {
        return null;
      }
      return normalizePreferences(parsed);
    } catch (e) {
      return null;
    }
  }

  function savePreferences(preferences) {
    preferences.version = policyVersion;
    preferences.necessary = true;
    preferences.updated = Math.floor(new Date().getTime() / 1000);
    current = normalizePreferences(preferences);
    current.updated = preferences.updated;
    setCookie(cookieName, JSON.stringify(current), parseInt(options.expiryDays, 10) || 180);
    expireCookie(legacyCookieName);
  }

  function emit(name, detail) {
    var event;
    try {
      event = new CustomEvent(name, { detail: detail });
    } catch (e) {
      event = document.createEvent('CustomEvent');
      event.initCustomEvent(name, false, false, detail);
    }
    document.dispatchEvent(event);
  }

  function isAllowed(category) {
    if (category === 'necessary') {
      return true;
    }
    return !!(current && current[category] === true);
  }

  function activateScript(source) {
    if (!source || source.getAttribute('data-cookieconsent-activated') === '1') {
      return false;
    }

    var category = source.getAttribute('data-cookieconsent');
    if (!category || !isAllowed(category)) {
      return false;
    }

    var script = document.createElement('script');
    var attrs = source.attributes;
    var src = source.getAttribute('data-cookieconsent-src');

    for (var i = 0; i < attrs.length; i++) {
      var name = attrs[i].name;
      if (name === 'type' || name === 'src' || name.indexOf('data-cookieconsent') === 0) {
        continue;
      }
      script.setAttribute(name, attrs[i].value);
    }

    if (src) {
      script.src = src;
    } else {
      script.text = source.text || source.textContent || source.innerHTML || '';
    }

    source.setAttribute('data-cookieconsent-activated', '1');
    source.parentNode.insertBefore(script, source.nextSibling);
    emit('cookieconsent:category-activated', {
      category: category,
      remote: !!src
    });

    return true;
  }

  function activateAllowedScripts() {
    var scripts = document.querySelectorAll('script[type="text/plain"][data-cookieconsent]');
    var activated = 0;
    for (var i = 0; i < scripts.length; i++) {
      if (activateScript(scripts[i])) {
        activated++;
      }
    }
    return activated;
  }

  function countControlledScripts() {
    var result = {
      total: 0,
      analytics: 0,
      advertising: 0,
      activated: 0
    };
    var scripts = document.querySelectorAll('script[data-cookieconsent]');
    result.total = scripts.length;
    for (var i = 0; i < scripts.length; i++) {
      var category = scripts[i].getAttribute('data-cookieconsent');
      if (result.hasOwnProperty(category)) {
        result[category]++;
      }
      if (scripts[i].getAttribute('data-cookieconsent-activated') === '1') {
        result.activated++;
      }
    }
    return result;
  }

  function button(label, className, handler) {
    var el = document.createElement('button');
    el.type = 'button';
    el.className = className;
    el.appendChild(document.createTextNode(label));
    el.onclick = handler;
    return el;
  }

  function privacyLink() {
    if (!options.privacyUrl) {
      return null;
    }
    var link = document.createElement('a');
    link.className = 'glcc-privacy';
    link.href = options.privacyUrl;
    link.appendChild(document.createTextNode(strings.privacy || 'Privacy information'));
    return link;
  }

  function closeDialog() {
    if (dialog && dialog.parentNode) {
      dialog.parentNode.removeChild(dialog);
    }
    dialog = null;
  }

  function categoryRow(category, preferences) {
    var row = document.createElement('div');
    row.className = 'glcc-category';

    var text = document.createElement('div');
    text.className = 'glcc-category-text';

    var title = document.createElement('strong');
    title.appendChild(document.createTextNode(category.label || category.id));
    text.appendChild(title);

    var description = document.createElement('p');
    description.appendChild(document.createTextNode(category.description || ''));
    text.appendChild(description);
    row.appendChild(text);

    if (category.required) {
      var required = document.createElement('span');
      required.className = 'glcc-required';
      required.appendChild(document.createTextNode(strings.required || 'Always active'));
      row.appendChild(required);
    } else {
      var input = document.createElement('input');
      input.type = 'checkbox';
      input.className = 'glcc-toggle';
      input.setAttribute('data-glcc-category', category.id);
      input.checked = !!preferences[category.id];
      input.setAttribute('aria-label', category.label || category.id);
      row.appendChild(input);
    }

    return row;
  }

  function collectPreferences(container) {
    var prefs = {
      version: policyVersion,
      necessary: true,
      analytics: false,
      advertising: false
    };
    var inputs = container.querySelectorAll('input[data-glcc-category]');
    for (var i = 0; i < inputs.length; i++) {
      prefs[inputs[i].getAttribute('data-glcc-category')] = !!inputs[i].checked;
    }
    return prefs;
  }

  function commitChoice(preferences) {
    var hadChoice = !!current;
    var before = current ? JSON.stringify(current) : '';
    savePreferences(preferences);
    closeDialog();

    if (!hadChoice) {
      activateAllowedScripts();
    } else if (before !== JSON.stringify(current)) {
      // Revocation cannot undo already executed third-party JavaScript.
      // Reload so denied categories remain blocked from the start.
      window.location.reload();
      return;
    }

    emit('cookieconsent:change', {
      preferences: current,
      diagnostics: countControlledScripts()
    });
    renderManageButton();
  }

  function openPreferences(forcePreferences) {
    closeDialog();

    var prefs = current || normalizePreferences(null);
    dialog = document.createElement('div');
    dialog.className = 'glcc-overlay';

    var panel = document.createElement('div');
    panel.className = 'glcc-dialog';
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-modal', 'true');
    panel.setAttribute('aria-labelledby', 'glcc-title');

    var title = document.createElement('h2');
    title.id = 'glcc-title';
    title.appendChild(document.createTextNode(strings.title || 'Cookie preferences'));
    panel.appendChild(title);

    var message = document.createElement('p');
    message.className = 'glcc-message';
    message.appendChild(document.createTextNode(strings.message || ''));
    panel.appendChild(message);

    if (!current && getCookie(legacyCookieName)) {
      var legacy = document.createElement('p');
      legacy.className = 'glcc-legacy';
      legacy.appendChild(document.createTextNode(strings.legacyNotice || 'Please choose your preferences.'));
      panel.appendChild(legacy);
    }

    var list = document.createElement('div');
    list.className = 'glcc-categories';
    for (var id in categories) {
      if (categories.hasOwnProperty(id)) {
        list.appendChild(categoryRow(categories[id], prefs));
      }
    }
    panel.appendChild(list);

    var privacy = privacyLink();
    if (privacy) {
      panel.appendChild(privacy);
    }

    var actions = document.createElement('div');
    actions.className = 'glcc-actions';
    actions.appendChild(button(strings.rejectOptional || 'Reject optional', 'glcc-btn glcc-btn-secondary', function () {
      commitChoice({ necessary: true, analytics: false, advertising: false });
    }));
    actions.appendChild(button(strings.savePreferences || 'Save preferences', 'glcc-btn glcc-btn-secondary', function () {
      commitChoice(collectPreferences(panel));
    }));
    actions.appendChild(button(strings.acceptAll || 'Accept all', 'glcc-btn glcc-btn-primary', function () {
      commitChoice({
        necessary: true,
        analytics: !!categories.analytics,
        advertising: !!categories.advertising
      });
    }));
    panel.appendChild(actions);

    if (current || forcePreferences) {
      var close = button(strings.close || 'Close', 'glcc-close', closeDialog);
      close.setAttribute('aria-label', strings.close || 'Close');
      panel.insertBefore(close, panel.firstChild);
    }

    dialog.appendChild(panel);
    document.body.appendChild(dialog);

    var first = panel.querySelector('button');
    if (first && first.focus) {
      first.focus();
    }
  }

  function renderManageButton() {
    if (!options.showManageButton) {
      return;
    }
    if (manageButton && manageButton.parentNode) {
      return;
    }
    manageButton = button(strings.manage || 'Manage cookies', 'glcc-manage', function () {
      openPreferences(true);
    });
    document.body.appendChild(manageButton);
  }

  function getDiagnostics() {
    return {
      policyVersion: policyVersion,
      hasChoice: !!current,
      preferences: current,
      controlledScripts: countControlledScripts(),
      categories: categories
    };
  }

  window.GeeklogCookieConsent = {
    openPreferences: function () { openPreferences(true); },
    getPreferences: function () { return current; },
    hasConsent: function (category) { return isAllowed(category); },
    activateAllowedScripts: activateAllowedScripts,
    getDiagnostics: getDiagnostics
  };

  function init() {
    initialChoice = !!readPreferences();
    current = readPreferences();

    if (current) {
      activateAllowedScripts();
      renderManageButton();
    } else {
      openPreferences(false);
    }

    emit('cookieconsent:ready', {
      preferences: current,
      hadStoredChoice: initialChoice,
      diagnostics: getDiagnostics()
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
