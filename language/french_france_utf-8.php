<?php

global $LANG32, $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_fs;

$LANG_COOKIECONSENT_1 = array(
    'plugin_name' => 'Cookie Consent',
    'message' => 'Ce site utilise des cookies. Consultez les informations de confidentialité du site avant de poursuivre votre navigation.',
    'dismiss' => 'Continuer',
    'learnmore' => 'Plus d’informations',
    'configuration' => 'Configuration',
    'manage' => 'Gérer Cookie Consent',
    'admin_intro' => 'Cookie Consent fonctionne actuellement en mode historique d’information uniquement.',
    'admin_status' => 'État actuel',
    'mode' => 'Mode',
    'mode_notice_only' => 'Information uniquement',
    'expiry' => 'Durée de l’accusé de lecture',
    'days' => 'jours',
    'audience' => 'Public',
    'all_visitors' => 'Tous les visiteurs',
    'anonymous_only' => 'Visiteurs anonymes uniquement',
    'privacy_url' => 'URL des informations de confidentialité',
    'not_configured' => 'Non configurée',
    'notice_only_warning' => 'Le bandeau historique enregistre un accusé de lecture, mais ne bloque, ne classe et ne diffère pas les cookies tiers avant cet accusé. Ce mode ne doit pas être présenté comme une plateforme complète de gestion du consentement.',
    'dashboard_mode' => 'Mode de consentement',
    'dashboard_expiry' => 'Durée en jours'
);

$LANG_configsections['cookieconsent'] = array(
    'label' => 'Cookie Consent',
    'title' => 'Configuration de Cookie Consent'
);
$LANG_configsubgroups['cookieconsent']['sg_0'] = 'Cookie Consent';
$LANG_fs['cookieconsent']['fs_01'] = 'Réglages du bandeau';
$LANG_confignames['cookieconsent']['show_logged_in'] = 'Afficher le bandeau aux utilisateurs connectés';
$LANG_confignames['cookieconsent']['privacy_url'] = 'URL des informations de confidentialité';
$LANG_confignames['cookieconsent']['expiry_days'] = 'Durée de l’accusé de lecture (jours)';

$PLG_cookieconsent_MESSAGE3002 = isset($LANG32[9]) ? $LANG32[9] : 'Une version plus récente de Geeklog est nécessaire.';
