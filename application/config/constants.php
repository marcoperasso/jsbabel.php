<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',                        'x+b');

define('VALIDATION_KEY_REASON_REGISTER',                        0);
define('VALIDATION_KEY_REASON_RESET_PWD',                       1);
define('VALIDATION_KEY_REASON_CONNECT',                         2);

define('GENDER_UNSPECIFIED',                                    0);
define('GENDER_FEMALE',                                         1);
define('GENDER_MALE',                                           2);


define('SHOW_POSITION_ALL',                                     0);
define('SHOW_POSITION_GROUP',                                   1);
define('SHOW_POSITION_NONE',                                    2);


define('SHOW_NAME_ALL',                                         0);
define('SHOW_NAME_GROUP',                                       1);
define('SHOW_NAME_NONE',                                        2);


define('POST_BLOCK_SIZE',                                       10);

define('CLIENT_CACHE_VERSION',                                  1);


include __DIR__ . '/../../privateconst.php';
/* End of file constants.php */
/* Location: ./application/config/constants.php */