<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

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
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

# Creating as per requirement
define("PROFILE_IMAGE_RESIZE_WIDTH", "500");
define("PRODUCT_IMAGE_RESIZE_WIDTH", "374");
define("PRODUCT_IMAGE_RESIZE_HEIGHT", "560");
define("APP_NAME", "Kerala Eats");
define("ADMIN_PER_PAGE_RECORDS", '100');
define("MOBILE_PAGE_LIMIT", '10');
define('API_SERVER_KEY', 'xyz');
define('NOTIFICATION_TITLE', 'Kerala Eats | Order status changed');
define('NOTIFICATION_TITLE_PLACED', 'Kerala Eats | Order placed');
define('NOTIFICATION_TITLE_PICKUP_TIME', 'Kerala Eats | Pickup time changed');
define('NOTIFICATION_CASHBACK_RECVD', 'Kerala Eats | Cashback Received');
define('NOTIFICATION_MONEY_ADDED', 'Kerala Eats | Money added to wallet');
define('NOTIFICATION_MONEY_DEDUCTED', 'Kerala Eats | Money deducted from wallet');
define('NOTIFICATION_TITLE_ORDER_CUSTOMIZED', 'Kerala Eats | One order is customized');
define('NOTIFICATION_TITLE_ORDER_CUSTOMIZED', 'Kerala Eats | Outstanding Paid');
define('AUTOCOMPLETE_SEARCH_LIMIT', '5');
define('ORDER_RECEIVED', 'Kerala Eats | You have received a new order');
define('IOS_BUNDLE_ID_CUSTOMER', 'com.kerala.eats'); # https://3.basecamp.com/4024001/buckets/20159244/messages/3744853497#__recording_3754731698
define('IOS_BUNDLE_ID_MERCHANT', 'com.mr.merchant');
define('PER_KM_CHARGE', 15);

# DINE IN
define('NOTIFICATION_TITLE_DINEIN_UPDATED', 'Kerala Eats | Something updated with the booked dine in');
define('NOTIFICATION_TITLE_DINEIN_ACCEPTED', 'Kerala Eats | Your Dine in request is accepted by the restaurant');
define('NOTIFICATION_TITLE_DINEIN_REJECTED', 'Kerala Eats | Your Dine in request is rejected by the restaurant');
define('NOTIFICATION_TITLE_DINEIN_BOOKED', 'Kerala Eats | New dinein request received');
define('DINE_IN_ADDED', 'DINE_IN_ADDED');
/* LALAMOVE */

/*SANDBOX*/
define('LALAMOVE_API_KEY', 'xyz');
define('LALAMOVE_SECRET_KEY', 'xyz');

/*SHARED BY CLIENT and use https://rest.lalamove.com instead of https://rest.sandbox.lalamove.com */
define('LALAMOVE_API_LIVE_KEY', 'xyz');
define('LALAMOVE_SECRET_LIVE_KEY', 'xyz');
define('LALAMOVE_SUPPORT_NUMBER', '+600000000000');

define('MERCHANT_BASE_URL', 'https://developer.webvilleedemo.xyz/mrmerchant/admin/');

/*For pick up  to time */
// define('Pickup_To_time_block', 30);
define('TIME_RANGE_BLOCK', 30);