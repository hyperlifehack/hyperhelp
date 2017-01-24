<?php

/* TODO:
 * do not use GLOBAL variables to store MySQL database & username credentials... possible security flaw.
 *
 * also test: $this->translation_type = "database"; // specify path and filename (have a look at translations.php) or "database" which means, the translations for all texts will be stored in database
 **/

/* project wide settings */
error_reporting(E_ALL); // turn the reporting of php errors on
date_default_timezone_set('UTC');

class config {
	
	function __construct()
	{
		// nothing here
	}

    private static $config = array();

    public static function set( $key, $value ) {
        self::$config[$key] = $value;
    }

    public static function get( $key ) {
    	if( config::isKeySet( $key ) ) {
        	return isset( self::$config[$key] ) ? self::$config[$key] : null;
    	}
    	else
    	{
    		trigger_error ( "key: ".$key." does not exist in config.php");
    	}
    }

    public static function setAll( array $array ) {
        self::$config = $array;
    }

    public static function isKeySet( $key ) {
        return isset( self::$config[ $key ] );
    }
}

// set valuable values

/* ======================= ABOUT THE PLATFORM */
config::set('platform_name'		, 'hyperhelp.org');		# name of the platform (may appear in title="" tag
config::set('platform_logo'		, 'images/star.png');	# logo of platform
config::set('platform_url'		, 'http://hyperhelp.org'); # base-url of platform
config::set('log_errors'		, './log/errors.log');	# put empty string here if you do not want errors to get logged to file
config::set('log_operations'	, '');					# leave empty string here if you do not want database operations to be logged, per default only errors are logged. you could put log.operations.txt here
config::set('force_ssl'			, false);				# if users coming via http should be redirected to https (SSL is recommended with or without TOR)

/* ======================= DEVELOPMENT */
config::set('debug_mode',		true);					# if you want additional info about whats going on. will also perserve xdebug ?Session parameters.

/* ======================= DATABASE */
config::set("db_srv_address","localhost");				# address of database server
config::set("db_datasource","mysql");					# right now can only be "mysql", could be postgress (not implemented) sqlite (not implemented)
config::set("db_name","db_hyperhelp");					# the database one will deal with, for conveniance same name as platform
config::set("db_charset","utf8");						# if you want special chars to be properly displayed in the database/phpmyadmin etc.
config::set("db_user","root");							# what database user to use for accessing the database
config::set("db_pass","root");							# what database password to use for accessing the database
config::set("db_auth_table","passwd"); 					# name of table where platform's usernames & passwords (md5 hashed) are stored (passwd)
config::set("db_groups_table","groups");				# what the table is called, where the groups are stored (groups)

// will be reset to defaults before every query of database
config::set("db_result",null);							# -> mysql-result-pointer, pointing to RAW mysql result of last query, no post-processing (sometimes you can not work directly with that), can be any type
config::set("db_output",null);							# -> data extracted from RAW mysql result, "the result" ready for further processing, can be any type
config::set('db_log_errors', './log/errors_db.log');	# put empty string here if you do not want database query errors to be logged
config::set("db_errors_output",true);					# true = output errors as html to browser to screen, false = return them as function values for further processing (json encode -> client -> let client display that stuff)
config::set("db_worked",false);							# -> this is the status of the last query possible values are true (worked) false (failed, mysql error will be thrown)
config::set("db_last_id",'');							# -> if there was an insert, return the auto-generated id of the record inserted.

config::set("answer",'');								# if there is any error, output it to the user

/* will hold the link to mysql, as soon as it is initialized like this:
 * config::set('database')['name'] = "MyDataBaseName"; // overwrite settings from config.php
 * $lib_mysqli_commands_instance = new lib_mysqli_commands(); # create instance from class
 */ 
config::set('lib_mysqli_interface_instance',null);	// actually only handles the buildup of connection and contains only one command: public static function query($query)
config::set('lib_mysqli_commands_instance',null);	// extends interface

/* ======================= WHO IS THE ADMIN? WHO IS RESPONSIBLE? */
config::set('mail_admin', "admin@server.org");		// where notification go
config::set('login_session_timeout', (3600)); // 86400*3sec = 3 days, 3600sec = 1h, 1800sec = 30min, 0 = no timeout, amounts of seconds that login-cookies are valid, after login (time until user has to re-login)

/*
$url = $_SERVER['PHP_SELF']; // php way to determine the current filename (fails sometimes?)
$filename_and_ending = explode('/', $url);
$filename_and_ending = $filename_and_ending[count($filename_and_ending) - 1];
$filename_and_ending = explode('.', $filename_and_ending);
config::set('current_filename', $filename_and_ending[0]); // automatically load filename.js
 */
?>