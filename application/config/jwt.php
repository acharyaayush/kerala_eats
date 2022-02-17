<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Store your secret key here
// Make sure you use better, long, more random key than this
$config['jwt_key'] = 'epyoqgqiddgckgqebhyaibamhghsponbkyvh';

/*Generated token will expire in 1440 minute for sample code
* Increase this value as per requirement for production
*/
// $config['token_timeout'] = 5; // minute
//$config['token_timeout'] = 1440;
// $config['token_timeout'] = 10080; // 7 Days
$config['token_timeout'] = 43200; // 30 Days
