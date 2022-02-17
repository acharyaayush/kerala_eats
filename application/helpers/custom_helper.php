<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**This function used for generate random code */
function generate_verification_code() {
    $Allowed_Chars = '1234567890';
    $Chars_Len = 4;
    $Salt_Length = 4;
    $salt = "";
    for($i=0; $i<$Salt_Length; $i++)
    {
        $salt .= $Allowed_Chars[mt_rand(0,$Chars_Len)];
    }
    return (int)$salt;
}

/**This function used for generate random token with time*/
function generate_token() {
    $Allowed_Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $Chars_Len = 10;
    $Salt_Length = 10;
    $salt = "";
    for($i=0; $i<=$Salt_Length; $i++)
    {
        $salt .= $Allowed_Chars[mt_rand(0,$Chars_Len)];
    }
    return $salt.time();
}

/**This function used for check extension valid or not*/
function check_extension($extension, $type="") {

    if($type=='image'){
        $allowedImageExtension = array('jpeg', 'jpg', 'png', 'gif');
        if(!in_array(strtolower($extension), $allowedImageExtension))
        {
            return false;
        }
    }else if($type=='audio'){
        $allowedAudioExtension = array('mp3', 'wma', 'aac', 'wav', 'm4a');
        if(!in_array(strtolower($extension), $allowedAudioExtension))
        {
            return false;
        }
    }else if($type=='video'){
        $allowedVideoExtension = array('mp4', 'mov', 'wmv', 'ogg', 'avi');
        if(!in_array(strtolower($extension), $allowedVideoExtension))
        {
            return false;
        }
    }else{
        $allowedAllExtension = array('jpeg', 'jpg', 'png', 'gif', 'mp4', 'mov', 'wmv', 'ogg', 'avi', 'mp3', 'wma', 'aac', 'wav', 'm4a');

        if(!in_array(strtolower($extension), $allowedAllExtension))
        {
            return false;
        }
    } 

    return true;
}

/**This function used for get file type */
/*file type 1 for image, 2 for audio, 3 for video*/
function get_file_type($mimeType) {

    $tmp = explode('/', $mimeType);
    
    if(strtolower($tmp[0]) == 'image'){
        return 1;
    }else if(strtolower($tmp[0]) == 'audio'){
        return 2;
    }else if(strtolower($tmp[0]) == 'video'){
        return 3;
    }else {
        return 0;
    }
     
}


/**This function used for send mail */
function send_mail($email,$subject,$message,$file_path = ""){
    $ci=& get_instance();
    $ci->load->database();
    $ci->load->model("Common");
    $setting_data=$ci->Common->getData('settings','*',"name = 'smtp_email' or name = 'smtp_password'");

    $smtp_user='';
    $smtp_password='';

    foreach ($setting_data as $value) {
        if($value['name'] == 'smtp_email')
        {
            $smtp_user = $value['value'];
        }else if($value['name'] == 'smtp_password'){
            $smtp_password = $value['value'];
        }
    }
    // echo $smtp_user."<br>";
    // echo $smtp_password;die;
    if($smtp_user!="" && $smtp_password!=""){

        $config['protocol']     = 'smtp';
        // $config['smtp_host']    = 'ssl://smtp.gmail.com'; 
        $config['smtp_host']    = 'ssl://smtppro.zoho.com'; 
        $config['smtp_port']    = '465';
        $config['smtp_user']    = $smtp_user;
        $config['smtp_pass']    = $smtp_password;
        $config['charset']        = 'utf-8';
        $config['newline']        = "\r\n";
        $config['mailtype']     = 'html';
        //$config['validation']     = TRUE; // bool whether to validate email or not      
        $CI = get_instance();
        $CI->email->initialize($config);
        $CI->email->from($smtp_user,"Kerala Eats Team",'');
        $CI->email->reply_to('', "Kerala Eats");
        $CI->email->to($email);
        $CI->email->subject("$subject");
        $CI->email->message($message);
        if($file_path != ""){
            $CI->email->attach($file_path);
        }
        $mail = $CI->email->send();
        // echo $CI->email->print_debugger();die;

    }
    // return true;
}

/**This function used for get time ago */
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}


/**This function used for get duration in seconds  input format is(0:00:30.024000
)*/
function getSecondsFromHMS($time) {

    $timeArr = array_reverse(explode(":", $time));    
    $seconds = 0;
    //echo '<pre>';print_r($timeArr);echo '</pre>';
    foreach ($timeArr as $key => $value) {
        if ($key > 2)
            break;
        $seconds += pow(60, $key) * $value;
    }
    return (int)$seconds;
}

/**This function used for generate random password */
function generate_random_password( $length = 10 ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,?";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}

/**This function used for generate pin for device with time*/
function generate_pin() {
    $Allowed_Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ12345678';
    $Chars_Len = 5;
    $Salt_Length = 5;
    $salt = "";
    for($i=0; $i<=$Salt_Length; $i++)
    {
        $salt .= $Allowed_Chars[mt_rand(0,$Chars_Len)];
    }
    return $salt.time();
}

function sendPushNotification($token,$data,$bundleid = '',$send_alarm = '') 
{
     if(strlen($token) != 64){
        /* For Android push notification */
        $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
        $API_SERVER_KEY = API_SERVER_KEY;
         $fields = array (
                'to' => $token,
                 "data"=> $data
             );

        $headers = array(
            'Authorization:key=' . $API_SERVER_KEY,
            'Content-Type:application/json'
        );  
         
        // Open connection  
        $ch = curl_init(); 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // Execute post   
        $result = curl_exec($ch); 
        
        curl_close($ch);
        return $result;

    }else{  
        // echo "bundleid".$bundleid;
        // $bundleid = 'com.mr.merchant';
        /* For IOS push notification */
        $keyfile = APPPATH.'helpers/AuthKey_G8D6RH3X3J.p8'; # <- Your AuthKey file
        $keyid = 'xyz';                            # <- Your Key ID
        $teamid = 'xyz';                           # <- Your Team ID (see Developer Portal)
        $bundleid = $bundleid;                # <- Your Bundle ID
        $url = 'https://api.development.push.apple.com';  # <- development url, or use http://api.push.apple.com for production environment
        // $url = 'https://api.push.apple.com';  # <- development url, or use http://api.push.apple.com for production environment
        $msg = array
        (
            'message' => $data['message'],
            'title' => $data['title'],
            'order_id' => $data['order_id'],
            'notification_type' => $data['notification_type'],
        );

        if($send_alarm != '') # That means new order placed hence sending alarm sound
        {
            $payload['aps'] = array('alert' => $msg, 'badge' => intval(0),'sound' => 'iphone_notification.mp3');
        }else 
        {
            $payload['aps'] = array('alert' => $msg, 'badge' => intval(0),'sound' => 'default');
        }
        $message = json_encode($payload);
        $key = openssl_pkey_get_private('file://'.$keyfile);
        $header = ['alg'=>'ES256','kid'=>$keyid];
        $claims = ['iss'=>$teamid,'iat'=>time()];

        $header_encoded = base64($header);
        $claims_encoded = base64($claims);

        $signature = '';
        openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
        $jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);

        $http2ch = curl_init();
        curl_setopt_array($http2ch, array(
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_URL => "$url/3/device/$token",
            CURLOPT_PORT => 443,
            CURLOPT_HTTPHEADER => array(
              "apns-topic: {$bundleid}",
              "authorization: bearer $jwt"
            ),
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $message,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HEADER => 1
        ));

        $result = curl_exec($http2ch);
        // print_r($result);
        if ($result === FALSE) {
            throw new Exception("Curl failed: ".curl_error($http2ch));
        }

        $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
        // echo "in customhelper";
        // print_r($status);die;
    }
}

function base64($data) {
    return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
}


//getting dashboard data------------------------START------------------

#getting total sale
function get_dashboard_total_sales_according_year($restaurant_id="",$role = 1,$year= ""){
    $ci=& get_instance();
    $ci->load->database();
    $ci->load->model("Common");

    if($restaurant_id != "" && $role == 2){
        $query_part = ' AND restaurant_id = '.$restaurant_id.'';
    }else{
        $query_part = '';
    }

    $current_month_number = date("m");

    $store_month_name = array();
    $store_month_data = array();
    
    for ($i=1; $i <= 12; $i++) { 

        //finding first and last date of specific month -------START----------
        $date    =    ''.$year.'-'.$i.'-10'; //given 10 date becouse it is available in all month
        $first_date_find = strtotime(date("d-m-Y", strtotime($date)) . ", first day of this month");
        $first_date = date("d-m-Y",$first_date_find);

        $last_date_find = strtotime(date("d-m-Y", strtotime($date)) . ", last day of this month");
        $last_date = date("d-m-Y",$last_date_find);
        //finding first and last date of specific month -------END----------
       
        #get month wise data
        $query = 'SELECT  SUM(`dc_amount`+`sub_total`) - SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_sale FROM `orders` WHERE order_status != 2 AND ( `updated_at` between "'.strtotime($first_date.'00:00:00').'" AND "'.strtotime($last_date.' 23:59:59').'") '.$query_part.'';
        $data =  $ci->Common->custom_query($query,"get");

        #getting month name list 
        #check if given year is current year then we  will display accroding to january to current month other chart will be  appeat according to jan to dec.
        if($year == date('Y')){
            if($i <= $current_month_number){
                $store_month_name[] =  date("F",strtotime(date("Y")."-".$i."-01")); //all month
                 if($data[0]['total_sale'] == ""){
                    $store_month_data[] = "0.00"; #store last month date values
                }else{
                    $store_month_data[] =  $data[0]['total_sale']; #store last month date values
                }
            }
        }else{
            $store_month_name[] =  date("F",strtotime(date("Y")."-".$i."-01")); //all month

             if($data[0]['total_sale'] == ""){
                $store_month_data[] = "0.00"; #store last month date values
            }else{
                $store_month_data[] =  $data[0]['total_sale']; #store last month date values
            }
        }
       
    }
    return array($store_month_name,$store_month_data);
}

#getting last month data
function get_dashboard_last_month_sale_chart_data($restaurant_id="",$role = 1){
    $ci=& get_instance();
    $ci->load->database();
    $ci->load->model("Common");

    if($restaurant_id != "" && $role == 2){
        $query_part = ' AND restaurant_id = '.$restaurant_id.'';
    }else{
        $query_part = '';
    }

    // Declare two dates
    $first_date_of_month = date('d-m-Y',strtotime("first day of previous month"));
    $last_date_of_month = date("d-m-Y",strtotime("last day of previous month")); 
      
    // Declare an empty array
    $last_month_all_date_array = array();
    $last_month_all_date_value_array = array();
      
    // Use strtotime function
    $Variable1 = strtotime($first_date_of_month);
    $Variable2 = strtotime($last_date_of_month);
      
    // Use for loop to store dates into array
    // 86400 sec = 24 hrs = 60*60*24 = 1 day
    //getting last month all dates accroring to first and last date of month
    for ($currentDate = $Variable1; $currentDate <= $Variable2; $currentDate += (86400)) {

        $Date = date('d-m-Y', $currentDate);
        $last_month_all_date_array[] = $Date; #store last month all dates

        #getting date wise data
        $query = 'SELECT SUM(`total_amount`) AS total_sale FROM `orders` WHERE order_status != 2  AND ( `updated_at` between "'.strtotime($Date.'00:00:00').'" AND "'.strtotime($Date.' 23:59:59').'") '.$query_part.'';
        $data =  $ci->Common->custom_query($query,"get");
        if($data[0]['total_sale'] == ""){
            $last_month_all_date_value_array[] = "0.00"; #store last month date values
        }else{
            $last_month_all_date_value_array[] =  $data[0]['total_sale']; #store last month date values
        }
    }
      
    // Display the dates and value in array format
    //print_r($last_month_all_date_array);
    //print_r($last_month_all_date_value_array);

    return array($last_month_all_date_array,$last_month_all_date_value_array);
}

#getting last week data 
function get_dashboard_last_week_sale_chart_data($restaurant_id="",$role = 1){
    $ci=& get_instance();
    $ci->load->database();
    $ci->load->model("Common");

    if($restaurant_id != "" && $role == 2){
        $query_part = ' AND restaurant_id = '.$restaurant_id.'';
    }else{
        $query_part = '';
    }

    $last_to_last_week_sunday_date = date('d-m-Y',strtotime('last sunday -7 days'));  
    $last_week_monday_date =  date("d-m-Y",strtotime('monday',strtotime('last week')));  
    $last_week_tuesday_date =  date("d-m-Y",strtotime('tuesday',strtotime('last week')));  
    $last_week_wednesday_date =  date("d-m-Y",strtotime('wednesday',strtotime('last week')));  
    $last_week_thursday_date =  date("d-m-Y",strtotime('thursday',strtotime('last week')));  
    $last_week_friday_date =  date("d-m-Y",strtotime('friday',strtotime('last week')));  
    $last_week_saturday_date =  date("d-m-Y",strtotime('saturday',strtotime('last week'))); 
    
    #sunday
    $sunday_query = 'SELECT SUM(`total_amount`) AS total_sale FROM `orders` WHERE order_status = 5 AND paid_status = 1 AND ( `updated_at` between "'.strtotime($last_to_last_week_sunday_date.'00:00:00').'" AND "'.strtotime($last_to_last_week_sunday_date.' 23:59:59').' '.$query_part.'")';
    $sunday_data =  $ci->Common->custom_query($sunday_query,"get");
    if($sunday_data[0]['total_sale'] == ""){
        $sunday_value = "0.00";
    }else{
        $sunday_value =  $sunday_data[0]['total_sale']; 
    }
    
    #monday
    $monday_query = 'SELECT SUM(`total_amount`) AS total_sale FROM `orders` WHERE order_status = 5 AND paid_status = 1 AND  ( `updated_at` between "'.strtotime($last_week_monday_date.'00:00:00').'" AND "'.strtotime($last_week_monday_date.' 23:59:59').'") '.$query_part.'';
    $monday_data =  $ci->Common->custom_query($monday_query,"get");
    if($monday_data[0]['total_sale'] == ""){
        $monday_value = "0.00";
    }else{
        $monday_value =  $monday_data[0]['total_sale']; 
    }

    #tuesday
    $tuesday_query = 'SELECT SUM(`total_amount`) AS total_sale FROM `orders` WHERE order_status = 5  AND ( `updated_at` between "'.strtotime($last_week_tuesday_date.'00:00:00').'" AND "'.strtotime($last_week_tuesday_date.' 23:59:59').'") '.$query_part.'';
    $tuesday_data =  $ci->Common->custom_query($tuesday_query,"get");
    
    if($tuesday_data[0]['total_sale'] == ""){
        $tuesday_value =  "0.00";
    }else{
        $tuesday_value =  $tuesday_data[0]['total_sale']; 
    }

    #wednesday
    $wednesday_query = 'SELECT SUM(`total_amount`) AS total_sale FROM `orders` WHERE order_status = 5 AND paid_status = 1 AND  ( `updated_at` between "'.strtotime($last_week_wednesday_date.'00:00:00').'" AND "'.strtotime($last_week_wednesday_date.' 23:59:59').'") '.$query_part.'';
    $wednesday_data =  $ci->Common->custom_query($wednesday_query,"get");

    if($wednesday_data[0]['total_sale'] == ""){
        $wednesday_value =  "0.00";
    }else{
        $wednesday_value =  $wednesday_data[0]['total_sale']; 
    }

    #thursday
    $thursday_query = 'SELECT SUM(`total_amount`) AS total_sale FROM `orders` WHERE order_status = 5 AND paid_status = 1 AND  ( `updated_at` between "'.strtotime($last_week_thursday_date.'00:00:00').'" AND "'.strtotime($last_week_thursday_date.' 23:59:59').'") '.$query_part.'';
    $thursday_data =  $ci->Common->custom_query($thursday_query,"get");

    if($thursday_data[0]['total_sale'] == ""){
        $thursday_value = "0.00";
    }else{
        $thursday_value =  $thursday_data[0]['total_sale']; 
    }

    #friday
    $friday_query = 'SELECT SUM(`total_amount`) AS total_sale FROM `orders` WHERE order_status = 5 AND paid_status = 1 AND  ( `updated_at` between "'.strtotime($last_week_friday_date.'00:00:00').'" AND "'.strtotime($last_week_friday_date.' 23:59:59').'") '.$query_part.'';
    $friday_data =  $ci->Common->custom_query($friday_query,"get");

    if($friday_data[0]['total_sale'] == ""){
        $friday_value =  "0.00";
    }else{
        $friday_value =  $friday_data[0]['total_sale']; 
    }

    #saturday
    $saturday_query = 'SELECT SUM(`total_amount`) AS total_sale FROM `orders` WHERE order_status = 5 AND paid_status = 1 AND  ( `updated_at` between "'.strtotime($last_week_saturday_date.'00:00:00').'" AND "'.strtotime($last_week_saturday_date.' 23:59:59').'") '.$query_part.'';
    $saturday_data =  $ci->Common->custom_query($saturday_query,"get");

    if($saturday_data[0]['total_sale'] == ""){
        $saturday_value = "0.00";
    }else{
        $saturday_value =  $saturday_data[0]['total_sale']; 
    }

    #passing last week dates and thats values
    return array(array($last_to_last_week_sunday_date,$last_week_monday_date,$last_week_tuesday_date,$last_week_wednesday_date,$last_week_thursday_date,$last_week_friday_date,$last_week_saturday_date),array($sunday_value,$monday_value,$tuesday_value,$wednesday_value,$thursday_value,$friday_value,$saturday_value));
}

//total earning report data
function total_earning_report_data($restaurant_id="",$role = 1,$fromdate="",$todate=""){
    $ci=& get_instance();
    $ci->load->database();
    $ci->load->model("Common");

    if($restaurant_id != "" && $role == 2){
        $query_part = ' AND restaurant_id = '.$restaurant_id.'';
    }else{
        $query_part = '';
    }

    #total earning and orders--------------------START--------------------------
    /*$total_earning = $ci->Common->getData('orders','SUM(total_amount) as total_earning','order_status = 5 AND paid_status = 1 '.$query_part.'');
    $total_completed_paid_order = $ci->Common->getData('orders','count(id) as total_order','order_status = 5 AND paid_status = 1 '.$query_part.'');*/
    $fromdate = strtotime($fromdate.'00:00:00');
    $todate = strtotime($todate.'23:59:59');
    $total_earning_query = "SELECT count(`id`) as total_order , SUM(`dc_amount`+`sub_total`) - SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_earning FROM `orders` WHERE order_status != 2 ".$query_part." AND created_at BETWEEN ".$fromdate." AND ".$todate;

    $earning = $ci->Common->custom_query($total_earning_query,'get');
    $total_earning['total_earning'] = number_format($earning[0]['total_earning'],2,'.','');
    $total_earning_order_count['total_order']  = $earning[0]['total_order'];
    #total earning and orders--------------------END----------------------------

    #total cancel order---------------START--------------------------
    $total_cancel_order_amount = $ci->Common->getData('orders','SUM(total_amount) as total_cancel_order_amount','order_status = 4 AND paid_status = 1 AND created_at BETWEEN '.$fromdate.' AND '.$todate.' '.$query_part.'');
    $total_cancel_order = $ci->Common->getData('orders','count(id) as total_cancel_order','order_status = 4 AND paid_status = 1 AND created_at BETWEEN '.$fromdate.' AND '.$todate.''.$query_part.'');
    #total cancel order---------------END--------------------------

    #getting gross sale ---------------------START----------------------
    /*$gross_sale = $ci->Common->getData('orders','SUM(    promo_subtotal_discounted_value+promo_dc_discounted_value+sub_total) as gross_sale','order_status = 5 AND paid_status = 1 '.$query_part.'');
    $total_gross_order = $ci->Common->getData('orders','count(id) as total_gross_order','order_status = 5 AND paid_status = 1 AND (promo_subtotal_is_applied =1 OR   promo_dc_is_applied =1) '.$query_part.'');*/

    $gross_sale_query = "SELECT count(`id`) as gs_total_order , SUM(`dc_amount`+`sub_total`) as gross_sale FROM `orders` WHERE order_status != 2 ".$query_part." AND created_at BETWEEN ".$fromdate." AND ".$todate;
    $gross_sale = $ci->Common->custom_query($gross_sale_query,'get');

    $gr_sale['gross_sale'] = number_format($gross_sale[0]['gross_sale'],2,'.','');
    $gs_order_count['total_gross_order'] = $gross_sale[0]['gs_total_order'];
    #getting gross sale ---------------------END------------------------

    return array($total_earning,$total_earning_order_count,$gr_sale,$gs_order_count,$total_cancel_order_amount[0],$total_cancel_order[0]);
}

#for earning report chart
function earning_report_chart($restaurant_id="",$role = 1,$fromdate="",$todate=""){
    $ci=& get_instance();
    $ci->load->database();
    $ci->load->model("Common");  

    if($restaurant_id != "" && $role == 2){
        $query_part = ' AND restaurant_id = '.$restaurant_id.'';
    }else{
        $query_part = '';
    }

    $starting_month_number = date('m', strtotime($fromdate));
    $end_month_number = date('m', strtotime($todate));
    $store_month_year_name = array();

    $d1 = new DateTime($fromdate. " 00:00:00");
    $d2 = new DateTime($todate. "23:59:59");
    $interval = $d1->diff($d2);
    $diffInMonths  = $interval->m;

    $selected_year =  date('Y', strtotime($fromdate));

    $selected_start_year =  date('Y', strtotime($fromdate));
    $selected_start_year_increase =  date('Y', strtotime($fromdate));
    $selected_end_year =  date('Y', strtotime($todate));
    
    $year_count = 1;
    for ($i=1; $i <= $year_count; $i++) { 
        if($selected_start_year_increase <= $selected_end_year){
            $selected_start_year_increase++; 
            $year_count++;
        }
    }
    
    $diffInYears = $year_count-1;
    if($diffInYears > 1 ){
        $diffIntime = $diffInYears;
    }else{
        $diffIntime = $diffInMonths+1;
    }
    
    
    $store_with_commission_data =  array();
    $store_with_out_commission_data =  array();
    $gross_sale_data =  array();
    

    for ($i=1; $i <= $diffIntime; $i++) { 

        //echo $diffInYears.'=='.$starting_month_number.'=='.$end_month_number.'<br>' ;
        if($diffInYears > 1){
            $store_month_year_name[] = $selected_start_year++;
        }else{
           $store_month_year_name[] = date("F", mktime(0, 0, 0, $starting_month_number, 10)); //output: October //geting month name 
        }
        //echo $starting_month_number.'<br>';

        if($starting_month_number <= $end_month_number && $diffInYears == 1){#single year

            
            if($i == 1){
                $first_date = date("d-m-Y", strtotime($fromdate));
            }else{
                $f_date    =    ''.$selected_year.'-'.$starting_month_number.'-1';
                $first_date_find = strtotime(date("d-m-Y", strtotime($f_date)));
                $first_date = date("d-m-Y",$first_date_find);
            }

           $last_date_of_month=date('d-m-Y', strtotime(''.$selected_year.'-'.$starting_month_number.'-1
    +'.(cal_days_in_month(CAL_GREGORIAN,$starting_month_number,$selected_year)-1).' day'));

            $last_date_of_month_or_year = $last_date_of_month;
            $starting_month_number++;
           
        }else if($diffInYears > 1){
            if($i == 1){
                $first_date = date("d-m-Y", strtotime($fromdate));
            }else{
                $f_date    =    ''.$selected_year.'-1-1';
                $first_date_find = strtotime(date("d-m-Y", strtotime($f_date)));
                $first_date = date("d-m-Y",$first_date_find);
            }

            if($i < $diffIntime){
                //echo 'small';
                $last_date_of_year = '31-12-'.$selected_year;
            }else{
                //echo 'equal';
                $last_date_of_year =  date("d-m-Y", strtotime($todate));
            }
            $last_date_of_month_or_year = $last_date_of_year;
           
            //$last_date_of_year++;
            $selected_year++;
        }

        //echo $first_date.'=='.$last_date_of_month_or_year.'<br>';

         #get WITH commission data --------start-----------
            $with_commission_query = 'SELECT  SUM(`dc_amount`+`sub_total`) + SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_sale FROM `orders` WHERE order_status != 2 AND ( `updated_at` between "'.strtotime($first_date.'00:00:00').'" AND "'.strtotime($last_date_of_month_or_year.' 23:59:59').'") '.$query_part.'';
            $data1 =  $ci->Common->custom_query($with_commission_query,"get");

            if($data1[0]['total_sale'] == ""){
                $store_with_commission_data[] = "0.00"; #store last month date values
            }else{
                $store_with_commission_data[] =  number_format($data1[0]['total_sale'],'2','.',''); #store last month date values
            }
            #get WITH commission data --------end-----------

            #get WITH OUT commission data --------start-----------
            $with_commission_query = 'SELECT  SUM(`dc_amount`+`sub_total`) - SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_sale FROM `orders` WHERE order_status != 2 AND ( `updated_at` between "'.strtotime($first_date.'00:00:00').'" AND "'.strtotime($last_date_of_month_or_year.' 23:59:59').'") '.$query_part.'';
            $data2 =  $ci->Common->custom_query($with_commission_query,"get");

            if($data2[0]['total_sale'] == ""){
                $store_with_out_commission_data[] = "0.00"; #store last month date values
            }else{
                $store_with_out_commission_data[] =  number_format($data2[0]['total_sale'],'2','.',''); #store last month date values
            }
            #get  WITH OUT commission data --------end-----------

            #get gross sale----------START------------
            $gorss_sale_query = 'SELECT  SUM(`dc_amount`+`sub_total`) as gross_sale FROM `orders` WHERE order_status != 2 AND ( `updated_at` between "'.strtotime($first_date.'00:00:00').'" AND "'.strtotime($last_date_of_month_or_year.' 23:59:59').'") '.$query_part.'';
            $data3 =  $ci->Common->custom_query($gorss_sale_query,"get");

            if($data3[0]['gross_sale'] == ""){
                $gross_sale_data[] = "0.00"; #store last month date values
            }else{
                $gross_sale_data[] =  number_format($data3[0]['gross_sale'],'2','.',''); #store last month date values
            }
            #get gross sale----------END--------------
    }
 
    return array('month_name'=>$store_month_year_name,'with_commission'=>$store_with_commission_data,'with_out_commission'=>$store_with_out_commission_data,'gross_sale'=>$gross_sale_data);
}
//getting dashboard data------------------------END------------------


