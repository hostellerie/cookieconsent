<?php

if (isset($_SERVER['PHP_SELF']) && strpos(strtolower($_SERVER['PHP_SELF']), 'autoinstall.php') !== false) {
    die('This file cannot be used on its own.');
}

function plugin_autoinstall_cookieconsent($pi_name)
{
    $piName = 'cookieconsent';
    $displayName = 'Cookie Consent';
    $adminGroup = 'Cookieconsent Admin';

    return array(
        'info' => array(
            'pi_name' => $piName,
            'pi_display_name' => $displayName,
            'pi_version' => '1.3.0',
            'pi_gl_version' => '2.1.1',
            'pi_homepage' => 'https://github.com/hostellerie/cookieconsent'
        ),
        'groups' => array(
            $adminGroup => 'Users in this group can administer the Cookie Consent plugin'
        ),
        'features' => array(
            'cookieconsent.admin' => 'Full access to the Cookie Consent plugin',
            'config.cookieconsent.tab_main' => 'Access to Cookie Consent configuration'
        ),
        'mappings' => array(
            'cookieconsent.admin' => array($adminGroup),
            'config.cookieconsent.tab_main' => array($adminGroup)
        ),
        'tables' => array()
    );
}

function plugin_load_configuration_cookieconsent($pi_name)
{
    global $_CONF;

    $defaults = $_CONF['path'] . 'plugins/' . $pi_name . '/install_defaults.php';
    if (!file_exists($defaults)) {
        return false;
    }

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $defaults;

    return function_exists('plugin_initconfig_cookieconsent') && plugin_initconfig_cookieconsent();
}

function plugin_compatible_with_this_version_cookieconsent($pi_name)
{
    if (!defined('VERSION')) {
        return false;
    }

    if (function_exists('COM_versionCompare')) {
        if (!COM_versionCompare(VERSION, '2.1.1', '>=')) {
            return false;
        }
    } elseif (version_compare(VERSION, '2.1.1', '<')) {
        return false;
    }

    return version_compare(PHP_VERSION, '5.6.0', '>=');
}
