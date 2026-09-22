<?php

global $LANG32, $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_fs;

$LANG_COOKIECONSENT_1 = array(
    'plugin_name' => 'Cookie Consent',
    'message' => 'We use necessary cookies to operate this site. With your permission, optional cookies may also be used for analytics and advertising.',
    'learnmore' => 'Privacy information',
    'configuration' => 'Configuration',
    'manage' => 'Manage Cookie Consent',
    'admin_intro' => 'Cookie Consent uses category-based consent with explicit accept, reject and preference choices.',
    'admin_status' => 'Current status',
    'mode' => 'Mode',
    'mode_category_consent' => 'Category consent',
    'expiry' => 'Consent lifetime',
    'days' => 'days',
    'audience' => 'Audience',
    'all_visitors' => 'All visitors',
    'anonymous_only' => 'Anonymous visitors only',
    'privacy_url' => 'Privacy information URL',
    'not_configured' => 'Not configured',
    'policy_version' => 'Policy version',
    'categories' => 'Enabled categories',
    'manage_button' => 'Permanent manage-consent button',
    'enabled' => 'Enabled',
    'disabled' => 'Disabled',
    'accept_all' => 'Accept all',
    'reject_optional' => 'Reject optional',
    'preferences' => 'Preferences',
    'save_preferences' => 'Save preferences',
    'manage_preferences' => 'Manage cookies',
    'dialog_title' => 'Cookie preferences',
    'always_active' => 'Always active',
    'close' => 'Close',
    'legacy_choice_notice' => 'Your previous acknowledgement cannot be converted into category consent. Please choose your preferences.',
    'category_necessary' => 'Necessary',
    'category_necessary_desc' => 'Required for core site operation and security. These cannot be disabled.',
    'category_analytics' => 'Analytics',
    'category_analytics_desc' => 'Helps measure site usage when you allow it.',
    'category_advertising' => 'Advertising',
    'category_advertising_desc' => 'Allows advertising-related scripts and storage when you allow it.',
    'dashboard_mode' => 'Consent mode',
    'dashboard_expiry' => 'Consent days',
    'dashboard_policy_version' => 'Policy version',
    'dashboard_categories' => 'Optional categories'
);

$LANG_configsections['cookieconsent'] = array('label' => 'Cookie Consent', 'title' => 'Cookie Consent Configuration');
$LANG_configsubgroups['cookieconsent']['sg_0'] = 'Cookie Consent';
$LANG_fs['cookieconsent']['fs_01'] = 'Consent settings';
$LANG_confignames['cookieconsent']['show_logged_in'] = 'Show consent UI to logged-in users';
$LANG_confignames['cookieconsent']['privacy_url'] = 'Privacy information URL';
$LANG_confignames['cookieconsent']['expiry_days'] = 'Consent lifetime (days)';
$LANG_confignames['cookieconsent']['policy_version'] = 'Consent policy version';
$LANG_confignames['cookieconsent']['analytics_enabled'] = 'Enable Analytics category';
$LANG_confignames['cookieconsent']['advertising_enabled'] = 'Enable Advertising category';
$LANG_confignames['cookieconsent']['show_manage_button'] = 'Show permanent Manage cookies button';

$PLG_cookieconsent_MESSAGE3002 = isset($LANG32[9]) ? $LANG32[9] : 'A newer version of Geeklog is required.';
