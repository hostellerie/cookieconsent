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

function cookieconsent_get_runtime_status()
{
    $config = cookieconsent_get_config();
    $expiryDays = isset($config['expiry_days']) ? (int) $config['expiry_days'] : 365;
    if ($expiryDays < 1) {
        $expiryDays = 365;
    }

    return array(
        'schema' => 1,
        'mode' => 'notice-only',
        'cookie_name' => 'cookieconsent_dismissed',
        'expiry_days' => $expiryDays,
        'show_logged_in' => !isset($config['show_logged_in']) || !empty($config['show_logged_in']),
        'privacy_url' => isset($config['privacy_url']) ? trim((string) $config['privacy_url']) : '',
        'acknowledged' => isset($_COOKIE['cookieconsent_dismissed']) && $_COOKIE['cookieconsent_dismissed'] === 'yes'
    );
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
    return '1.1.0';
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
        COM_errorLog('Cookie Consent Plugin: unsupported upgrade direction from ' . $installedVersion . ' to ' . $codeVersion . '.');
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

    COM_errorLog('Cookie Consent Plugin: upgrade completed from ' . $installedVersion . ' to ' . $codeVersion . '.');
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
 * Legacy banner integration.
 *
 * Version 1.1.0 intentionally keeps the existing Silktide notice engine for
 * compatibility. It records acknowledgement but does not block or categorize
 * third-party cookies before acknowledgement. See ROADMAP.md.
 */
function plugin_getheadercode_cookieconsent()
{
    global $_CONF, $_SCRIPTS, $LANG_COOKIECONSENT_1;

    $status = cookieconsent_get_runtime_status();
    if (!$status['show_logged_in'] && !COM_isAnonUser()) {
        return '';
    }

    $options = array(
        'message' => $LANG_COOKIECONSENT_1['message'],
        'dismiss' => $LANG_COOKIECONSENT_1['dismiss'],
        'learnMore' => $LANG_COOKIECONSENT_1['learnmore'],
        'theme' => $_CONF['site_url'] . '/cookieconsent/styles/cookieconsent.css',
        'expiryDays' => $status['expiry_days']
    );
    if ($status['privacy_url'] !== '') {
        $options['link'] = $status['privacy_url'];
    }

    $json = json_encode($options, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if ($json === false) {
        return '';
    }

    $inline = 'window.cookieconsent_options = ' . $json . ';';

    if (isset($_SCRIPTS) && is_object($_SCRIPTS)
        && method_exists($_SCRIPTS, 'setJavaScript')
        && method_exists($_SCRIPTS, 'setJavaScriptFile')) {
        $_SCRIPTS->setJavaScript($inline, false);
        $_SCRIPTS->setJavaScriptFile('cookieconsent', '/cookieconsent/cookieconsent.js', true);
        return '';
    }

    return '<script type="text/javascript">' . $inline . '</script>'
        . '<script type="text/javascript" src="'
        . cookieconsent_escape($_CONF['site_url'] . '/cookieconsent/cookieconsent.js')
        . '"></script>';
}

function plugin_getcapabilities_cookieconsent()
{
    return array(
        'schema' => 1,
        'roles' => array('service', 'infrastructure'),
        'capabilities' => array(
            'consent.status',
            'consent.policy.read',
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
        'schema' => 1,
        'mode' => $status['mode'],
        'message' => $LANG_COOKIECONSENT_1['message'],
        'dismiss_label' => $LANG_COOKIECONSENT_1['dismiss'],
        'learn_more_label' => $LANG_COOKIECONSENT_1['learnmore'],
        'privacy_url' => $status['privacy_url'],
        'expiry_days' => $status['expiry_days']
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
    $configUrl = $_CONF['site_admin_url'] . '/configuration.php?conf_group=cookieconsent';

    $output = array(
        'schema' => 1,
        'status' => 'warning',
        'metrics' => array(
            array('id' => 'mode', 'label' => $LANG_COOKIECONSENT_1['dashboard_mode'], 'value' => $LANG_COOKIECONSENT_1['mode_notice_only']),
            array('id' => 'expiry_days', 'label' => $LANG_COOKIECONSENT_1['dashboard_expiry'], 'value' => $status['expiry_days'])
        ),
        'alerts' => array(
            array(
                'id' => 'notice_only',
                'status' => 'warning',
                'message' => $LANG_COOKIECONSENT_1['notice_only_warning'],
                'url' => $_CONF['site_admin_url'] . '/plugins/cookieconsent/index.php'
            )
        ),
        'links' => array(
            array('label' => $LANG_COOKIECONSENT_1['manage'], 'url' => $_CONF['site_admin_url'] . '/plugins/cookieconsent/index.php'),
            array('label' => $LANG_COOKIECONSENT_1['configuration'], 'url' => $configUrl)
        ),
        'updated' => time()
    );

    return true;
}
