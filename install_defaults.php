<?php

if (isset($_SERVER['PHP_SELF']) && strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file cannot be used on its own.');
}

global $_COOKIECONSENT_DEFAULT;
$_COOKIECONSENT_DEFAULT = array();
$_COOKIECONSENT_DEFAULT['show_logged_in'] = 1;
$_COOKIECONSENT_DEFAULT['privacy_url'] = '';
$_COOKIECONSENT_DEFAULT['expiry_days'] = 180;
$_COOKIECONSENT_DEFAULT['policy_version'] = '1';
$_COOKIECONSENT_DEFAULT['analytics_enabled'] = 1;
$_COOKIECONSENT_DEFAULT['advertising_enabled'] = 1;
$_COOKIECONSENT_DEFAULT['show_manage_button'] = 1;

function cookieconsent_config_item_exists($name)
{
    global $_TABLES;
    if (!isset($_TABLES['conf_values'])) {
        return false;
    }

    return DB_count(
        $_TABLES['conf_values'],
        array('name', 'group_name'),
        array($name, 'cookieconsent')
    ) > 0;
}

function cookieconsent_add_config_item($c, $name, $value, $type, $fieldset, $order, $selection = 0)
{
    if (!cookieconsent_config_item_exists($name)) {
        $c->add($name, $value, $type, 0, $fieldset, $selection, $order, true, 'cookieconsent', 0);
    }
}

function plugin_initconfig_cookieconsent()
{
    global $_COOKIECONSENT_DEFAULT;

    $c = config::get_instance();

    if (!$c->group_exists('cookieconsent')) {
        $c->add('sg_0', NULL, 'subgroup', 0, 0, NULL, 0, true, 'cookieconsent');
        $c->add('tab_main', NULL, 'tab', 0, 0, NULL, 0, true, 'cookieconsent', 0);
        $c->add('fs_01', NULL, 'fieldset', 0, 0, NULL, 0, true, 'cookieconsent', 0);
    } else {
        if (!cookieconsent_config_item_exists('tab_main')) {
            $c->add('tab_main', NULL, 'tab', 0, 0, NULL, 0, true, 'cookieconsent', 0);
        }
        if (!cookieconsent_config_item_exists('fs_01')) {
            $c->add('fs_01', NULL, 'fieldset', 0, 0, NULL, 0, true, 'cookieconsent', 0);
        }
    }

    cookieconsent_add_config_item($c, 'show_logged_in', $_COOKIECONSENT_DEFAULT['show_logged_in'], 'select', 0, 10, 1);
    cookieconsent_add_config_item($c, 'privacy_url', $_COOKIECONSENT_DEFAULT['privacy_url'], 'text', 0, 20);
    cookieconsent_add_config_item($c, 'expiry_days', $_COOKIECONSENT_DEFAULT['expiry_days'], 'text', 0, 30);
    cookieconsent_add_config_item($c, 'policy_version', $_COOKIECONSENT_DEFAULT['policy_version'], 'text', 0, 40);
    cookieconsent_add_config_item($c, 'analytics_enabled', $_COOKIECONSENT_DEFAULT['analytics_enabled'], 'select', 0, 50, 1);
    cookieconsent_add_config_item($c, 'advertising_enabled', $_COOKIECONSENT_DEFAULT['advertising_enabled'], 'select', 0, 60, 1);
    cookieconsent_add_config_item($c, 'show_manage_button', $_COOKIECONSENT_DEFAULT['show_manage_button'], 'select', 0, 70, 1);

    return true;
}
