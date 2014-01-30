<?php

// Preboot application configurations

//
// SETUP YOUR APP HERE
// * timezone
// * site path (SITE_PATH)
// * base path (BASE_PATH)
// * app path (PROTECTED_FOLDER)
//
// Remaining configuration in common.conf.php
// 

// Set your time zone
date_default_timezone_set('America/New_York');

// MUST BE DEFINED FOR FRAMEWORK USE.
// Use full absolute paths and end them with '/'
// eg. /var/www/project/

// This should point to the folder where the application boots
// from.  That is where it is running right now.
$config['SITE_PATH'] = realpath('./') . '/';

// This should point to the folder where the Doo Framework
// is located. It can live outside your public_html folder.
// This is relative to the SITE_PATH above.
$config['BASE_PATH'] = realpath('../../') . '/dooframework/';

// This should point to the folder where the 
// application folders (ie. model, view, controller) are located
// relative to the SITE_PATH above.  This can/should live
// outside your public_html folder for more security.
$config['PROTECTED_FOLDER'] = '../../DPvMP7kUZj8/';

//
// Load framework configurations
// * common
// * routes
// * database
//

include '../../DPvMP7kUZj8/config/common.conf.php';
include '../../DPvMP7kUZj8/config/routes.conf.php';
include '../../DPvMP7kUZj8/config/db.conf.php';

// Just include this for production mode
// include $config['BASE_PATH'].'deployment/deploy.php';
include $config['BASE_PATH'].'Doo.php';
include $config['BASE_PATH'].'app/DooConfig.php';

# Uncomment for auto loading the framework classes.
spl_autoload_register('Doo::autoload');

// Save framework configuration
Doo::conf()->set($config);

// This provides a more verbose error reporting.
// Remove this if you wish to see the normal PHP error reporting.
include $config['BASE_PATH'].'diagnostic/debug.php';

# database usage
//Doo::useDbReplicate();	#for db replication master-slave usage
//Doo::db()->setMap($dbmap);
//Doo::db()->setDb($dbconfig, $config['APP_MODE']);
//Doo::db()->sql_tracking = true;	#for debugging/profiling purpose

Doo::app()->route = $route;

# Uncomment for DB profiling
//Doo::logger()->beginDbProfile('doowebsite');
Doo::app()->run();
//Doo::logger()->endDbProfile('doowebsite');
//Doo::logger()->rotateFile(20);
//Doo::logger()->writeDbProfiles();
