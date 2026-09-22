<?php

if (isset($_SERVER['PHP_SELF']) && strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file cannot be used on its own.');
}

global $_COOKIECONSENT_DEFAULT;
$_COOKIECONSENT_DEFAULT = array();
$_COOKIECONSENT_DEFAULT['show_logged_in'] = 1;
$_COOKIECONSENT_DEFAULT['privacy_url'] = '';
$_COOKIECONSENT_DEFAULT['expiry_days'] = 365;

function cookieconsent_config_item_exists($name)
{
    global $_TABLES;
    if (!isset($_TABLES['conf_values'])) {
        return false;
    }
    return DB_count($_TABLES['conf_values'], array('name', 'group_name'), array($name, 'cookieconsent')) > 0;
}

function plugin_initconfig_cookieconsent()
{
    global $_COOKIECONSENT_DEFAULT;
    $c = config::get_instance();

    if (!$c->group_exists('cookieconsent')) {
        $c->add('sg_0', NULL, 'subgroup', 0, 0, NULL, 0, true, 'cookieconsent');
        $c->add('tab_main', NULL, 'tab', 0, 0, NULL, 0, true, 'cookieconsent', 0);
        $c->add('fs_01', NULL, 'fieldset', 0, 0, NULL, 0, true, 'cookieconsent', 0);
        $c->add('show_logged_in', $_COOKIECONSENT_DEFAULT['show_logged_in'], 'select', 0, 0, 1, 10, true, 'cookieconsent', 0);
        $c->add('privacy_url', $_COOKIECONSENT_DEFAULT['privacy_url'], 'text', 0, 0, 0, 20, true, 'cookieconsent', 0);
        $c->add('expiry_days', $_COOKIECONSENT_DEFAULT['expiry_days'], 'text', 0, 0, 0, 30, true, 'cookieconsent', 0);
        return true;
    }

    if (!cookieconsent_config_item_exists('tab_main')) {
        $c->add('tab_main', NULL, 'tab', 0, 0, NULL, 0, true, 'cookieconsent', 0);
    }
    if (!cookieconsent_config_item_exists('fs_01')) {
        $c->add('fs_01', NULL, 'fieldset', 0, 0, NULL, 0, true, 'cookieconsent', 0);
    }
    if (!cookieconsent_config_item_exists('show_logged_in')) {
        $c->add('show_logged_in', $_COOKIECONSENT_DEFAULT['show_logged_in'], 'select', 0, 0, 1, 10, true, 'cookieconsent', 0);
    }
    if (!cookieconsent_config_item_exists('privacy_url')) {
        $c->add('privacy_url', $_COOKIECONSENT_DEFAULT['privacy_url'], 'text', 0, 0, 0, 20, true, 'cookieconsent', 0);
    }
    if (!cookieconsent_config_item_exists('expiry_days')) {
        $c->add('expiry_days', $_COOKIECONSENT_DEFAULT['expiry_days'], 'text', 0, 0, 0, 30, true, 'cookieconsent', 0);
    }
    return true;
}
