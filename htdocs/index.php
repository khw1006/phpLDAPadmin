<?php
// $Header: /cvsroot/phpldapadmin/phpldapadmin/htdocs/index.php,v 1.49.2.5 2008/01/12 10:01:28 wurley Exp $

/**
 * @package phpLDAPadmin
 */

/*******************************************
<pre>

If you are seeing this in your browser,
PHP is not installed on your web server!!!

</pre>
*******************************************/

/**
 * We will perform some sanity checking here, since this file is normally loaded first when users
 * first access the application.
 */
# The index we will store our config in $_SESSION
define('APPCONFIG','plaConfig');

define('LIBDIR',sprintf('%s/',realpath('../lib/')));
ini_set('display_errors',1);
error_reporting(E_ALL);

# General functions needed to proceed.
ob_start();
if (! file_exists(LIBDIR.'functions.php')) {
	if (ob_get_level()) ob_end_clean();
	die(sprintf("Fatal error: Required file '<b>%sfunctions.php</b>' does not exist.",LIBDIR));
}

if (! is_readable(LIBDIR.'functions.php')) {
	if (ob_get_level()) ob_end_clean();
	die(sprintf("Cannot read the file '<b>%sfunctions.php</b>' its permissions may be too strict.",LIBDIR));
}

if (ob_get_level())
	ob_end_clean();

require LIBDIR.'functions.php';

# Define the path to our configuration file.
if (defined('CONFDIR'))
	$app['config_file'] = CONFDIR.'config.php';
else
	$app['config_file'] = 'config.php';

# Make sure this PHP install has gettext, we use it for language translation
if (! extension_loaded('gettext'))
	error('<p>Your install of PHP appears to be missing GETTEXT support.</p><p>GETTEXT is used for language translation.</p><p>Please install GETTEXT support before using phpLDAPadmin.<br /><small>(Dont forget to restart your web server afterwards)</small></p>','error',true);

/**
 * Helper functions.
 * Our required helper functions are defined in functions.php
 */
if (isset($app['function_files']) && is_array($app['function_files']))
	foreach ($app['function_files'] as $file_name ) {
		if (! file_exists($file_name))
			error(sprintf('Fatal error: Required file "%s" does not exist.',$file_name),'error',true);

		if (! is_readable($file_name))
			error(sprintf('Fatal error: Cannot read the file "%s", its permissions may be too strict.',$file_name),'error',true);

		ob_start();
		require $file_name;
		if (ob_get_level()) ob_end_clean();
	}

# Configuration File check
if (! file_exists($app['config_file'])) {
	error(sprintf(_('You need to configure %s. Edit the file "%s" to do so. An example config file is provided in "%s.example".'),'phpLDAPadmin',$app['config_file'],$app['config_file']),'error',true);

} elseif (! is_readable($app['config_file'])) {
	error(sprintf('Fatal error: Cannot read your configuration file "%s", its permissions may be too strict.',$app['config_file']),'error',true);
}

# If our config file fails the sanity check, then stop now.
if (! check_config($app['config_file'])) {
	$www['page'] = new page();
	$www['body'] = new block();
	$www['page']->block_add('body',$www['body']);
	$www['page']->display();

	exit;
}

include './cmd.php';
?>
