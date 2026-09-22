<?php

global $LANG32, $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_fs;

$LANG_COOKIECONSENT_1 = array(
    'plugin_name' => 'Cookie Consent',
    'message' => 'Nous utilisons des cookies nécessaires au fonctionnement du site. Avec votre accord, des cookies optionnels peuvent aussi être utilisés pour la mesure d’audience et la publicité.',
    'learnmore' => 'Informations de confidentialité',
    'configuration' => 'Configuration',
    'manage' => 'Gérer Cookie Consent',
    'admin_intro' => 'Cookie Consent utilise maintenant un consentement par catégories avec choix explicite : accepter, refuser ou personnaliser.',
    'admin_status' => 'État actuel',
    'mode' => 'Mode',
    'mode_category_consent' => 'Consentement par catégories',
    'expiry' => 'Durée du consentement',
    'days' => 'jours',
    'audience' => 'Public',
    'all_visitors' => 'Tous les visiteurs',
    'anonymous_only' => 'Visiteurs anonymes uniquement',
    'privacy_url' => 'URL des informations de confidentialité',
    'not_configured' => 'Non configurée',
    'policy_version' => 'Version de la politique',
    'categories' => 'Catégories activées',
    'manage_button' => 'Bouton permanent de gestion du consentement',
    'enabled' => 'Activé',
    'disabled' => 'Désactivé',
    'accept_all' => 'Tout accepter',
    'reject_optional' => 'Refuser les optionnels',
    'preferences' => 'Préférences',
    'save_preferences' => 'Enregistrer mes préférences',
    'manage_preferences' => 'Gérer les cookies',
    'dialog_title' => 'Préférences de cookies',
    'always_active' => 'Toujours actif',
    'close' => 'Fermer',
    'legacy_choice_notice' => 'Votre ancien accusé de lecture ne peut pas être converti en consentement par catégories. Merci de choisir vos préférences.',
    'category_necessary' => 'Nécessaires',
    'category_necessary_desc' => 'Indispensables au fonctionnement et à la sécurité du site. Ils ne peuvent pas être désactivés.',
    'category_analytics' => 'Mesure d’audience',
    'category_analytics_desc' => 'Permet de mesurer l’utilisation du site lorsque vous l’autorisez.',
    'category_advertising' => 'Publicité',
    'category_advertising_desc' => 'Autorise les scripts et stockages liés à la publicité lorsque vous l’autorisez.',
    'dashboard_mode' => 'Mode de consentement',
    'dashboard_expiry' => 'Durée en jours',
    'dashboard_policy_version' => 'Version de politique',
    'dashboard_categories' => 'Catégories optionnelles',
    'diagnostics' => 'Diagnostics',
    'integration_contract' => 'Contrat d’intégration',
    'browser_api' => 'API navigateur',
    'diagnostic_privacy_url' => 'L’URL des informations de confidentialité n’est pas configurée.',
    'diagnostic_policy_version' => 'La version de la politique de consentement est vide.',
    'diagnostic_categories' => 'Aucune catégorie de consentement optionnelle n’est activée.'
);

$LANG_configsections['cookieconsent'] = array('label' => 'Cookie Consent', 'title' => 'Configuration de Cookie Consent');
$LANG_configsubgroups['cookieconsent']['sg_0'] = 'Cookie Consent';
$LANG_fs['cookieconsent']['fs_01'] = 'Réglages du consentement';
$LANG_confignames['cookieconsent']['show_logged_in'] = 'Afficher le consentement aux utilisateurs connectés';
$LANG_confignames['cookieconsent']['privacy_url'] = 'URL des informations de confidentialité';
$LANG_confignames['cookieconsent']['expiry_days'] = 'Durée du consentement (jours)';
$LANG_confignames['cookieconsent']['policy_version'] = 'Version de la politique de consentement';
$LANG_confignames['cookieconsent']['analytics_enabled'] = 'Activer la catégorie Mesure d’audience';
$LANG_confignames['cookieconsent']['advertising_enabled'] = 'Activer la catégorie Publicité';
$LANG_confignames['cookieconsent']['show_manage_button'] = 'Afficher le bouton permanent Gérer les cookies';

$PLG_cookieconsent_MESSAGE3002 = isset($LANG32[9]) ? $LANG32[9] : 'Une version plus récente de Geeklog est nécessaire.';
