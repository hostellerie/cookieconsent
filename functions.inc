<?php

if (isset($_SERVER['PHP_SELF']) && strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die('This file cannot be used on its own.');
}

global $_CONF, $_COOKIECONSENT_CONF;

$cookieconsentPluginPath = $_CONF['path'] . 'plugins/cookieconsent/';
$cookieconsentLanguage = isset($_CONF['language']) ? $_CONF['language'] : 'english';
$cookieconsentLanguageFile = $cookieconsentPluginPath . 'language/' . $cookieconsentLanguage . '.php';
if (file_exists($cookieconsentLanguageFile)) {
    require_once $cookieconsentLanguageFile;
} else {
    require_once $cookieconsentPluginPath . 'language/english.php';
}

function cookieconsent_get_config($refresh = false)
{
    global $_CONF, $_COOKIECONSENT_CONF;

    if ($refresh || !isset($_COOKIECONSENT_CONF) || !is_array($_COOKIECONSENT_CONF)) {
        require_once $_CONF['path_system'] . 'classes/config.class.php';
        $config = config::get_instance();
        $_COOKIECONSENT_CONF = $config->get_config('cookieconsent');
    }

    return is_array($_COOKIECONSENT_CONF) ? $_COOKIECONSENT_CONF : array();
}

function cookieconsent_escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function cookieconsent_bool_config($config, $name, $default)
{
    if (!array_key_exists($name, $config)) {
        return (bool) $default;
    }
    return !empty($config[$name]);
}

function cookieconsent_get_categories()
{
    global $LANG_COOKIECONSENT_1;

    $config = cookieconsent_get_config();

    return array(
        'necessary' => array(
            'id' => 'necessary',
            'label' => $LANG_COOKIECONSENT_1['category_necessary'],
            'description' => $LANG_COOKIECONSENT_1['category_necessary_desc'],
            'required' => true,
            'enabled' => true
        ),
        'analytics' => array(
            'id' => 'analytics',
            'label' => $LANG_COOKIECONSENT_1['category_analytics'],
            'description' => $LANG_COOKIECONSENT_1['category_analytics_desc'],
            'required' => false,
            'enabled' => cookieconsent_bool_config($config, 'analytics_enabled', true)
        ),
        'advertising' => array(
            'id' => 'advertising',
            'label' => $LANG_COOKIECONSENT_1['category_advertising'],
            'description' => $LANG_COOKIECONSENT_1['category_advertising_desc'],
            'required' => false,
            'enabled' => cookieconsent_bool_config($config, 'advertising_enabled', true)
        )
    );
}

function cookieconsent_get_policy_version()
{
    $config = cookieconsent_get_config();
    $version = isset($config['policy_version']) ? trim((string) $config['policy_version']) : '1';
    return $version === '' ? '1' : substr($version, 0, 64);
}

function cookieconsent_get_expiry_days()
{
    $config = cookieconsent_get_config();
    $expiryDays = isset($config['expiry_days']) ? (int) $config['expiry_days'] : 180;
    if ($expiryDays < 1) {
        $expiryDays = 180;
    }
    if ($expiryDays > 730) {
        $expiryDays = 730;
    }
    return $expiryDays;
}

function cookieconsent_read_preferences_cookie()
{
    if (empty($_COOKIE['cookieconsent_preferences'])) {
        return array();
    }

    $raw = rawurldecode((string) $_COOKIE['cookieconsent_preferences']);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return array();
    }

    $categories = cookieconsent_get_categories();
    $normalized = array(
        'version' => isset($data['version']) ? (string) $data['version'] : '',
        'necessary' => true,
        'analytics' => false,
        'advertising' => false,
        'updated' => isset($data['updated']) ? (int) $data['updated'] : 0
    );

    foreach (array('analytics', 'advertising') as $id) {
        if (!empty($categories[$id]['enabled'])) {
            $normalized[$id] = !empty($data[$id]);
        }
    }

    return $normalized;
}

function cookieconsent_get_runtime_status()
{
    $config = cookieconsent_get_config();
    $preferences = cookieconsent_read_preferences_cookie();
    $policyVersion = cookieconsent_get_policy_version();
    $validConsent = !empty($preferences)
        && isset($preferences['version'])
        && (string) $preferences['version'] === $policyVersion;

    return array(
        'schema' => 2,
        'mode' => 'category-consent',
        'cookie_name' => 'cookieconsent_preferences',
        'legacy_cookie_name' => 'cookieconsent_dismissed',
        'expiry_days' => cookieconsent_get_expiry_days(),
        'policy_version' => $policyVersion,
        'show_logged_in' => cookieconsent_bool_config($config, 'show_logged_in', true),
        'show_manage_button' => cookieconsent_bool_config($config, 'show_manage_button', true),
        'privacy_url' => isset($config['privacy_url']) ? trim((string) $config['privacy_url']) : '',
        'categories' => cookieconsent_get_categories(),
        'has_choice' => $validConsent,
        'preferences' => $validConsent ? $preferences : array(),
        'legacy_acknowledgement' => isset($_COOKIE['cookieconsent_dismissed'])
            && $_COOKIE['cookieconsent_dismissed'] === 'yes'
    );
}

/**
 * Attributes for a consent-controlled script.
 *
 * Providers can render:
 *   <script type="text/plain" data-cookieconsent="analytics">...</script>
 *
 * For remote scripts use data-cookieconsent-src instead of src so browsers do
 * not fetch the resource before the category is granted.
 */
function cookieconsent_script_attributes($category, $src = '')
{
    $category = strtolower(trim((string) $category));
    if (!in_array($category, array('analytics', 'advertising'), true)) {
        return '';
    }

    $attributes = 'type="text/plain" data-cookieconsent="' . cookieconsent_escape($category) . '"';
    if ($src !== '') {
        $attributes .= ' data-cookieconsent-src="' . cookieconsent_escape($src) . '"';
    }

    return $attributes;
}

function plugin_getadminoption_cookieconsent()
{
    global $_CONF, $LANG_COOKIECONSENT_1;

    if (!SEC_hasRights('cookieconsent.admin')) {
        return array();
    }

    return array(
        $LANG_COOKIECONSENT_1['plugin_name'],
        $_CONF['site_admin_url'] . '/plugins/cookieconsent/index.php',
        ''
    );
}

function plugin_geticon_cookieconsent()
{
    global $_CONF;
    return $_CONF['site_admin_url'] . '/plugins/cookieconsent/images/cookieconsent.png';
}

function plugin_getversion_cookieconsent()
{
    return '1.3.0';
}

function plugin_chkVersion_cookieconsent()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/cookieconsent/autoinstall.php';
    $instParms = plugin_autoinstall_cookieconsent('cookieconsent');

    return $instParms['info']['pi_version'];
}

function plugin_upgrade_cookieconsent()
{
    global $_CONF, $_TABLES;

    $installedVersion = DB_getItem($_TABLES['plugins'], 'pi_version', "pi_name = 'cookieconsent'");
    $codeVersion = plugin_chkVersion_cookieconsent();

    if ($installedVersion === $codeVersion) {
        return true;
    }
    if ($installedVersion === '' || version_compare($installedVersion, $codeVersion, '>')) {
        COM_errorLog(
            'Cookie Consent Plugin: unsupported upgrade direction from '
            . $installedVersion . ' to ' . $codeVersion . '.'
        );
        return false;
    }

    require_once $_CONF['path'] . 'plugins/cookieconsent/autoinstall.php';
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $_CONF['path'] . 'plugins/cookieconsent/install_defaults.php';

    if (!plugin_compatible_with_this_version_cookieconsent('cookieconsent')) {
        return 3002;
    }
    if (!plugin_initconfig_cookieconsent()) {
        COM_errorLog('Cookie Consent Plugin: configuration migration failed.');
        return false;
    }

    $instParms = plugin_autoinstall_cookieconsent('cookieconsent');
    $glVersion = DB_escapeString($instParms['info']['pi_gl_version']);
    $safeCodeVersion = DB_escapeString($codeVersion);
    DB_query(
        "UPDATE {$_TABLES['plugins']} SET pi_version = '{$safeCodeVersion}', "
        . "pi_gl_version = '{$glVersion}' WHERE pi_name = 'cookieconsent'",
        1
    );
    if (DB_error()) {
        COM_errorLog('Cookie Consent Plugin: failed to record upgraded version.');
        return false;
    }

    COM_errorLog(
        'Cookie Consent Plugin: upgrade completed from '
        . $installedVersion . ' to ' . $codeVersion . '.'
    );
    return true;
}

function plugin_autouninstall_cookieconsent()
{
    return array(
        'tables' => array(),
        'groups' => array('Cookieconsent Admin'),
        'features' => array('cookieconsent.admin', 'config.cookieconsent.tab_main'),
        'php_blocks' => array(),
        'vars' => array()
    );
}

/**
 * Native Geeklog configuration lifecycle callback.
 *
 * Individual visitor choices are deliberately not logged or emitted here.
 * Only administrator configuration changes are recorded.
 */
function plugin_configchange_cookieconsent($group, $changes = array())
{
    global $_COOKIECONSENT_CONF;

    if ($group !== 'cookieconsent') {
        return;
    }

    $_COOKIECONSENT_CONF = null;
    cookieconsent_get_config(true);

    if (is_array($changes) && count($changes) > 0) {
        COM_errorLog(
            'Cookie Consent Plugin: configuration changed: '
            . implode(', ', array_map('strval', $changes))
        );
    }
}

function plugin_getheadercode_cookieconsent()
{
    global $_CONF, $_SCRIPTS, $LANG_COOKIECONSENT_1;

    $status = cookieconsent_get_runtime_status();
    if (!$status['show_logged_in'] && !COM_isAnonUser()) {
        return '';
    }

    $categories = array();
    foreach ($status['categories'] as $id => $category) {
        if (!$category['enabled']) {
            continue;
        }
        $categories[$id] = array(
            'id' => $id,
            'label' => $category['label'],
            'description' => $category['description'],
            'required' => $category['required']
        );
    }

    $options = array(
        'cookieName' => $status['cookie_name'],
        'legacyCookieName' => $status['legacy_cookie_name'],
        'policyVersion' => $status['policy_version'],
        'expiryDays' => $status['expiry_days'],
        'privacyUrl' => $status['privacy_url'],
        'showManageButton' => $status['show_manage_button'],
        'categories' => $categories,
        'strings' => array(
            'title' => $LANG_COOKIECONSENT_1['dialog_title'],
            'message' => $LANG_COOKIECONSENT_1['message'],
            'acceptAll' => $LANG_COOKIECONSENT_1['accept_all'],
            'rejectOptional' => $LANG_COOKIECONSENT_1['reject_optional'],
            'preferences' => $LANG_COOKIECONSENT_1['preferences'],
            'savePreferences' => $LANG_COOKIECONSENT_1['save_preferences'],
            'manage' => $LANG_COOKIECONSENT_1['manage_preferences'],
            'privacy' => $LANG_COOKIECONSENT_1['learnmore'],
            'required' => $LANG_COOKIECONSENT_1['always_active'],
            'close' => $LANG_COOKIECONSENT_1['close'],
            'legacyNotice' => $LANG_COOKIECONSENT_1['legacy_choice_notice']
        )
    );

    $json = json_encode($options, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if ($json === false) {
        return '';
    }

    $inline = 'window.geeklogCookieConsentOptions = ' . $json . ';';
    $cssUrl = $_CONF['site_url'] . '/cookieconsent/styles/cookieconsent.css';
    $jsUrl = $_CONF['site_url'] . '/cookieconsent/cookieconsent.js';

    if (isset($_SCRIPTS) && is_object($_SCRIPTS)) {
        if (method_exists($_SCRIPTS, 'setCSSFile')) {
            $_SCRIPTS->setCSSFile('cookieconsent', $cssUrl);
        }
        if (method_exists($_SCRIPTS, 'setJavaScript')) {
            $_SCRIPTS->setJavaScript($inline, false);
        }
        if (method_exists($_SCRIPTS, 'setJavaScriptFile')) {
            $_SCRIPTS->setJavaScriptFile('cookieconsent', '/cookieconsent/cookieconsent.js', true);
            return '';
        }
    }

    return '<link rel="stylesheet" type="text/css" href="' . cookieconsent_escape($cssUrl) . '">'
        . '<script type="text/javascript">' . $inline . '</script>'
        . '<script type="text/javascript" src="' . cookieconsent_escape($jsUrl) . '"></script>';
}

function plugin_getcapabilities_cookieconsent()
{
    return array(
        'schema' => 1,
        'roles' => array('service', 'infrastructure'),
        'capabilities' => array(
            'consent.status',
            'consent.policy.read',
            'consent.categories.read',
            'consent.integration.read',
            'consent.diagnostics',
            'dashboard.summary'
        )
    );
}

function service_consent_status_cookieconsent($args, &$output, &$svc_msg)
{
    $output = cookieconsent_get_runtime_status();
    $svc_msg = '';
    return true;
}

function service_consent_policy_read_cookieconsent($args, &$output, &$svc_msg)
{
    global $LANG_COOKIECONSENT_1;

    $status = cookieconsent_get_runtime_status();
    $output = array(
        'schema' => 2,
        'mode' => $status['mode'],
        'message' => $LANG_COOKIECONSENT_1['message'],
        'privacy_url' => $status['privacy_url'],
        'policy_version' => $status['policy_version'],
        'expiry_days' => $status['expiry_days'],
        'categories' => array_values($status['categories'])
    );
    $svc_msg = '';
    return true;
}

function service_consent_categories_read_cookieconsent($args, &$output, &$svc_msg)
{
    $output = array(
        'schema' => 1,
        'categories' => array_values(cookieconsent_get_categories())
    );
    $svc_msg = '';
    return true;
}

function service_consent_integration_read_cookieconsent($args, &$output, &$svc_msg)
{
    $output = array(
        'schema' => 1,
        'controlled_script_contract' => array(
            'inline' => '<script type="text/plain" data-cookieconsent="analytics">...</script>',
            'remote' => '<script type="text/plain" data-cookieconsent="advertising" data-cookieconsent-src="https://example.invalid/script.js"></script>',
            'categories' => array('analytics', 'advertising')
        ),
        'browser_api' => 'window.GeeklogCookieConsent',
        'browser_events' => array(
            'cookieconsent:ready',
            'cookieconsent:change',
            'cookieconsent:category-activated'
        ),
        'relationships' => array(
            array('category' => 'analytics', 'marker' => 'data-cookieconsent="analytics"'),
            array('category' => 'advertising', 'marker' => 'data-cookieconsent="advertising"')
        )
    );
    $svc_msg = '';
    return true;
}

function service_consent_diagnostics_cookieconsent($args, &$output, &$svc_msg)
{
    $status = cookieconsent_get_runtime_status();
    $alerts = array();

    if ($status['privacy_url'] === '') {
        $alerts[] = array('id' => 'privacy_url', 'status' => 'warning', 'message' => 'Privacy information URL is not configured.');
    }
    if ($status['policy_version'] === '') {
        $alerts[] = array('id' => 'policy_version', 'status' => 'warning', 'message' => 'Consent policy version is empty.');
    }

    $optional = 0;
    foreach ($status['categories'] as $category) {
        if (!$category['required'] && $category['enabled']) {
            $optional++;
        }
    }
    if ($optional === 0) {
        $alerts[] = array('id' => 'optional_categories', 'status' => 'info', 'message' => 'No optional consent category is enabled.');
    }

    $output = array(
        'schema' => 1,
        'status' => count($alerts) > 0 ? 'warning' : 'ok',
        'policy_version' => $status['policy_version'],
        'optional_categories' => $optional,
        'privacy_url_configured' => $status['privacy_url'] !== '',
        'manage_button_enabled' => $status['show_manage_button'],
        'legacy_acknowledgement_present' => $status['legacy_acknowledgement'],
        'alerts' => $alerts
    );
    $svc_msg = '';
    return true;
}

function service_dashboard_summary_cookieconsent($args, &$output, &$svc_msg)
{
    global $_CONF, $LANG_COOKIECONSENT_1;

    $output = array();
    $svc_msg = '';

    if (!SEC_hasRights('cookieconsent.admin')) {
        $svc_msg = 'Access denied.';
        return false;
    }

    $status = cookieconsent_get_runtime_status();
    $diagnostics = array();
    $diagnosticMessage = '';
    service_consent_diagnostics_cookieconsent(array(), $diagnostics, $diagnosticMessage);

    $enabledOptional = 0;
    foreach ($status['categories'] as $category) {
        if (!$category['required'] && $category['enabled']) {
            $enabledOptional++;
        }
    }

    $alerts = isset($diagnostics['alerts']) ? $diagnostics['alerts'] : array();
    foreach ($alerts as $index => $alert) {
        $alerts[$index]['url'] = $_CONF['site_admin_url'] . '/plugins/cookieconsent/index.php';
    }

    $output = array(
        'schema' => 1,
        'status' => empty($alerts) ? 'ok' : 'warning',
        'metrics' => array(
            array('id' => 'mode', 'label' => $LANG_COOKIECONSENT_1['dashboard_mode'], 'value' => $LANG_COOKIECONSENT_1['mode_category_consent']),
            array('id' => 'policy_version', 'label' => $LANG_COOKIECONSENT_1['dashboard_policy_version'], 'value' => $status['policy_version']),
            array('id' => 'optional_categories', 'label' => $LANG_COOKIECONSENT_1['dashboard_categories'], 'value' => $enabledOptional),
            array('id' => 'expiry_days', 'label' => $LANG_COOKIECONSENT_1['dashboard_expiry'], 'value' => $status['expiry_days'])
        ),
        'alerts' => $alerts,
        'links' => array(
            array(
                'label' => $LANG_COOKIECONSENT_1['manage'],
                'url' => $_CONF['site_admin_url'] . '/plugins/cookieconsent/index.php'
            ),
            array(
                'label' => $LANG_COOKIECONSENT_1['configuration'],
                'url' => $_CONF['site_admin_url'] . '/configuration.php?conf_group=cookieconsent'
            )
        ),
        'updated' => time()
    );

    return true;
}
