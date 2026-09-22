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
$configUrl = $_CONF['site_admin_url'] . '/configuration.php';
$privacyUrl = $status['privacy_url'] !== ''
    ? cookieconsent_escape($status['privacy_url'])
    : cookieconsent_escape($LANG_COOKIECONSENT_1['not_configured']);
$audience = $status['show_logged_in']
    ? $LANG_COOKIECONSENT_1['all_visitors']
    : $LANG_COOKIECONSENT_1['anonymous_only'];

$display = COM_startBlock($LANG_COOKIECONSENT_1['plugin_name']);
$display .= '<p>' . cookieconsent_escape($LANG_COOKIECONSENT_1['admin_intro']) . '</p>';
$display .= '<div class="uk-alert-warning" uk-alert><p>'
    . cookieconsent_escape($LANG_COOKIECONSENT_1['notice_only_warning']) . '</p></div>';
$display .= '<h2>' . cookieconsent_escape($LANG_COOKIECONSENT_1['admin_status']) . '</h2>';
$display .= '<dl>';
$display .= '<dt>' . cookieconsent_escape($LANG_COOKIECONSENT_1['mode']) . '</dt><dd>'
    . cookieconsent_escape($LANG_COOKIECONSENT_1['mode_notice_only']) . '</dd>';
$display .= '<dt>' . cookieconsent_escape($LANG_COOKIECONSENT_1['expiry']) . '</dt><dd>'
    . (int) $status['expiry_days'] . ' ' . cookieconsent_escape($LANG_COOKIECONSENT_1['days']) . '</dd>';
$display .= '<dt>' . cookieconsent_escape($LANG_COOKIECONSENT_1['audience']) . '</dt><dd>'
    . cookieconsent_escape($audience) . '</dd>';
$display .= '<dt>' . cookieconsent_escape($LANG_COOKIECONSENT_1['privacy_url']) . '</dt><dd>'
    . $privacyUrl . '</dd>';
$display .= '</dl>';
$display .= '<form method="get" action="' . cookieconsent_escape($configUrl) . '">';
$display .= '<input type="hidden" name="conf_group" value="cookieconsent">';
$display .= '<p><button type="submit" class="uk-button uk-button-primary">'
    . cookieconsent_escape($LANG_COOKIECONSENT_1['configuration']) . '</button></p>';
$display .= '</form>';
$display .= COM_endBlock();

if (function_exists('COM_createHTMLDocument')) {
    echo COM_createHTMLDocument(
        $display,
        array('pagetitle' => $LANG_COOKIECONSENT_1['plugin_name'])
    );
} else {
    echo COM_siteHeader('menu', $LANG_COOKIECONSENT_1['plugin_name'])
        . $display
        . COM_siteFooter();
}
