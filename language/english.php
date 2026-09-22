<?php

global $LANG32, $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_fs;

$LANG_COOKIECONSENT_1 = array(
    'plugin_name' => 'Cookie Consent',
    'message' => 'This website uses cookies. Please review the site privacy information before continuing.',
    'dismiss' => 'Continue',
    'learnmore' => 'More information',
    'configuration' => 'Configuration',
    'manage' => 'Manage Cookie Consent',
    'admin_intro' => 'Cookie Consent currently runs in legacy notice-only mode.',
    'admin_status' => 'Current status',
    'mode' => 'Mode',
    'mode_notice_only' => 'Notice only',
    'expiry' => 'Acknowledgement lifetime',
    'days' => 'days',
    'audience' => 'Audience',
    'all_visitors' => 'All visitors',
    'anonymous_only' => 'Anonymous visitors only',
    'privacy_url' => 'Privacy information URL',
    'not_configured' => 'Not configured',
    'notice_only_warning' => 'The legacy banner records acknowledgement but does not block, categorize, or defer third-party cookies before acknowledgement. Do not treat this mode as a complete consent-management platform.',
    'dashboard_mode' => 'Consent mode',
    'dashboard_expiry' => 'Acknowledgement days'
);

$LANG_configsections['cookieconsent'] = array(
    'label' => 'Cookie Consent',
    'title' => 'Cookie Consent Configuration'
);
$LANG_configsubgroups['cookieconsent']['sg_0'] = 'Cookie Consent';
$LANG_fs['cookieconsent']['fs_01'] = 'Banner settings';
$LANG_confignames['cookieconsent']['show_logged_in'] = 'Show the banner to logged-in users';
$LANG_confignames['cookieconsent']['privacy_url'] = 'Privacy information URL';
$LANG_confignames['cookieconsent']['expiry_days'] = 'Acknowledgement lifetime (days)';

$PLG_cookieconsent_MESSAGE3002 = isset($LANG32[9]) ? $LANG32[9] : 'A newer version of Geeklog is required.';
