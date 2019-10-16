<?php
//Allow database update script to be run from the cloud so long
//as it is not production, i.e. on dev or staging
if (isset($_ENV['AH_SITE_ENVIRONMENT']) && $_ENV['AH_SITE_ENVIRONMENT'] != 'prod') {
$settings['update_free_access'] = TRUE;
}

// Configure the public and private files directories.
$settings['file_public_path'] = 'sites/default/files';
$settings['file_private_path'] = "/mnt/files/{$_ENV['AH_SITE_GROUP']}.{$_ENV['AH_SITE_ENVIRONMENT']}/files-private";

// Host URL patterns for Acquia.
$settings['trusted_host_patterns'] = [
  '^ukhealthcaredev.prod.acquia-sites.com',
  '^ukhealthcarestg.prod.acquia-sites.com',
  '^ukhealthcare.prod.acquia-sites.com',
  '^ukhealthcare.uky.edu',
];

// Include the Acquia database connection and other config.
if (file_exists('/var/www/site-php')) {
  require '/var/www/site-php/ukhealthcare/ukhealthcare-settings.inc';
}

// Configure tmp directory
$config['system.file']['path']['temporary'] = "/mnt/gfs/{$_ENV['AH_SITE_GROUP']}.{$_ENV['AH_SITE_ENVIRONMENT']}/tmp";

// Set the SAML SP entityID
$sp_entity_id = 'http://ukhealthcare'.($_ENV['AH_SITE_ENVIRONMENT'] == 'prod' ? '.uky.edu' : $_ENV['AH_SITE_ENVIRONMENT'].'.prod.acquia-sites.com');
$config['samlauth.authentication']['sp_entity_id'] = $sp_entity_id;
  
// Enable Memcache
/*if (isset($settings['memcache']['servers'])) { 
  $settings['cache']['default'] = 'cache.backend.memcache';
}
*/
// Include config that shouldn't be in the code repository.  See https://docs.acquia.com/resource/secrets
$secrets_file = sprintf('/mnt/files/%s.%s/secrets.settings.php', $_ENV['AH_SITE_GROUP'],$_ENV['AH_SITE_ENVIRONMENT']);
if (file_exists($secrets_file)) {
    require $secrets_file;
}

//// Add an htaccess prompt on dev.
//// @see https://docs.acquia.com/articles/password-protect-your-non-production-environments-acquia-hosting#phpfpm

// Make sure Drush keeps working.
// Modified from function drush_verify_cli()
$cli = (php_sapi_name() == 'cli');

/*
// PASSWORD-PROTECT NON-PRODUCTION SITES (i.e. staging/dev)
if (!$cli && ($_SERVER['REQUEST_URI'] != '/saml/metadata') && (isset($_ENV['AH_NON_PRODUCTION']) && $_ENV['AH_NON_PRODUCTION'])) {
  $username = 'ukhealthcare';
  $password = 'spoonful of sugar';
  if (!(isset($_SERVER['PHP_AUTH_USER']) && ($_SERVER['PHP_AUTH_USER']==$username && $_SERVER['PHP_AUTH_PW']==$password))) {
    header('WWW-Authenticate: Basic realm="This site is protected"');
    header('HTTP/1.0 401 Unauthorized');
    // Fallback message when the user presses cancel / escape
    echo 'Access denied';
    exit;
  }
}
*/