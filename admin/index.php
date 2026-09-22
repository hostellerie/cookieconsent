<?php

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

if (!SEC_hasRights('cookieconsent.admin')) {
    COM_accessLog(
        'User ' . (isset($_USER['username']) ? $_USER['username'] : '')
        . ' tried to access Cookie Consent administration without permission.'
    );
    $display = COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
    if (function_exists('COM_createHTMLDocument')) {
        echo COM_createHTMLDocument($display, array('pagetitle' => $MESSAGE[30]));
    } else {
        echo COM_siteHeader('menu', $MESSAGE[30]) . $display . COM_siteFooter();
    }
    exit;
}

$status = cookieconsent_get_runtime_status();
$diagnostics = array();
$diagnosticMessage = '';
service_consent_diagnostics_cookieconsent(array(), $diagnostics, $diagnosticMessage);

$configUrl = $_CONF['site_admin_url'] . '/configuration.php';
$privacyUrl = $status['privacy_url'] !== ''
    ? '<a href="' . cookieconsent_escape($status['privacy_url']) . '">'
        . cookieconsent_escape($status['privacy_url']) . '</a>'
    : cookieconsent_escape($LANG_COOKIECONSENT_1['not_configured']);
$audience = $status['show_logged_in']
    ? $LANG_COOKIECONSENT_1['all_visitors']
    : $LANG_COOKIECONSENT_1['anonymous_only'];

$enabledCategories = array();
foreach ($status['categories'] as $category) {
    if ($category['enabled']) {
        $enabledCategories[] = $category['label'];
    }
}

$display = COM_startBlock($LANG_COOKIECONSENT_1['plugin_name']);
$display .= '<p>' . cookieconsent_escape($LANG_COOKIECONSENT_1['admin_intro']) . '</p>';
$display .= '<h2>' . cookieconsent_escape($LANG_COOKIECONSENT_1['admin_status']) . '</h2><dl>';
$display .= '<dt>' . cookieconsent_escape($LANG_COOKIECONSENT_1['mode']) . '</dt><dd>'
    . cookieconsent_escape($LANG_COOKIECONSENT_1['mode_category_consent']) . '</dd>';
$display .= '<dt>' . cookieconsent_escape($LANG_COOKIECONSENT_1['policy_version']) . '</dt><dd>'
    . cookieconsent_escape($status['policy_version']) . '</dd>';
$display .= '<dt>' . cookieconsent_escape($LANG_COOKIECONSENT_1['expiry']) . '</dt><dd>'
    . (int) $status['expiry_days'] . ' ' . cookieconsent_escape($LANG_COOKIECONSENT_1['days']) . '</dd>';
$display .= '<dt>' . cookieconsent_escape($LANG_COOKIECONSENT_1['audience']) . '</dt><dd>'
    . cookieconsent_escape($audience) . '</dd>';
$display .= '<dt>' . cookieconsent_escape($LANG_COOKIECONSENT_1['categories']) . '</dt><dd>'
    . cookieconsent_escape(implode(', ', $enabledCategories)) . '</dd>';
$display .= '<dt>' . cookieconsent_escape($LANG_COOKIECONSENT_1['manage_button']) . '</dt><dd>'
    . cookieconsent_escape($status['show_manage_button'] ? $LANG_COOKIECONSENT_1['enabled'] : $LANG_COOKIECONSENT_1['disabled']) . '</dd>';
$display .= '<dt>' . cookieconsent_escape($LANG_COOKIECONSENT_1['privacy_url']) . '</dt><dd>'
    . $privacyUrl . '</dd></dl>';

if (!empty($diagnostics['alerts'])) {
    $display .= '<h2>' . cookieconsent_escape($LANG_COOKIECONSENT_1['diagnostics']) . '</h2><ul>';
    foreach ($diagnostics['alerts'] as $alert) {
        $display .= '<li>' . cookieconsent_escape($alert['message']) . '</li>';
    }
    $display .= '</ul>';
}

$display .= '<h2>' . cookieconsent_escape($LANG_COOKIECONSENT_1['integration_contract']) . '</h2>';
$display .= '<p><code>&lt;script type="text/plain" data-cookieconsent="analytics"&gt;...&lt;/script&gt;</code></p>';
$display .= '<p><code>&lt;script type="text/plain" data-cookieconsent="advertising" data-cookieconsent-src="https://example.com/script.js"&gt;&lt;/script&gt;</code></p>';
$display .= '<p>' . cookieconsent_escape($LANG_COOKIECONSENT_1['browser_api']) . ': <code>window.GeeklogCookieConsent</code></p>';
$display .= '<form method="get" action="' . cookieconsent_escape($configUrl) . '">';
$display .= '<input type="hidden" name="conf_group" value="cookieconsent">';
$display .= '<p><button type="submit" class="uk-button uk-button-primary">'
    . cookieconsent_escape($LANG_COOKIECONSENT_1['configuration']) . '</button></p>';
$display .= '</form>';
$display .= COM_endBlock();

if (function_exists('COM_createHTMLDocument')) {
    echo COM_createHTMLDocument($display, array('pagetitle' => $LANG_COOKIECONSENT_1['plugin_name']));
} else {
    echo COM_siteHeader('menu', $LANG_COOKIECONSENT_1['plugin_name']) . $display . COM_siteFooter();
}
