<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Bcrypt.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Api extends REST_Controller 
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model("Common");
        $this->lang->load('english','english'); # first param english specifies the file english_lang and second specfies the language. So english,english => language folder/english folder/english_lang.php file and english language. We won't get english_lang file in our code. _lang is added by CI itself
        # Below method is added to check the token in constructor because if user is logged in and then deleted by admin then in the next api calling action it will check the token and will return 203 if user is deleted.
        $tokenData = $this->verify_request();
        if($tokenData == '203')
        {
            $data['status']	 = 401;
            $data['message'] = "Unauthorized Access!";
        	echo json_encode($data);
        	die;
        }
    }

    # verify_request method start
    # This method used to verify JWT token
    private function verify_request()
    {
        # Get all the headers
        $headers = $this->input->request_headers();
        
        if(isset($headers['Authorization']))
        {
        	# Extract the token
	        $token = $headers['Authorization'];
	    	# Use try-catch
	        # JWT library throws exception if the token is not valid
	        try {
	            # Validate the token
	            # Successfull validation will return the decoded user data else returns false
	            $data = AUTHORIZATION::validateToken($token);
	             //check token is expire or not
            	$tokeExpire = AUTHORIZATION::validateTimestamp($token);
            	
	            if ($data === false) {
                return $data;
	            }else if($tokeExpire === false) {
	                return $tokeExpire;
	            } else {
	            	# Success; Now check user status ; if it is the status which is not allowed to login (ex 3 and 4) so send false
	            	$token_user_status = $this->Common->getData('users','status','id = "'.$data->id.'"');
	            	if($token_user_status[0]['status'] == 2 || $token_user_status[0]['status'] == 3 || $token_user_status[0]['status'] == 5) 
	            	# DB_user_status 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
	            	{
	            		return $data = '203';
	            	}
	            	else
	            	{
	                	return $data;
	            	}
	            }
	            // if ($data === false) {
	            //     return $data;
	            // } else {
	            //     return $data;
	            // }
	        } catch (\Exception $e) {

	            # make error log
	            log_message('error', $e);
	            
	            #  Token is invalid
	            #  Send the unathorized access message
	            return $data = false;
	        }
        }else #for the apis where token is not mendatory (can be present or can not be present)
        {
        	return $data = false;
        }
    }
    # verify_request method end

    # Function to get minimum allowed os version start
    # This method used to get minimum allowed app version for ios and android
    
    public function app_version_post()
    {
        try{
            $setting_data = $this->Common->getData('settings','*',"name = 'android_version' or name = 'ios_version' or name = 'android_version_merchant' or name = 'ios_version_merchant'");
            $setting_array = array();
            foreach ($setting_data as $value) 
            {
                $setting_array[$value['name']] = $value['value'];
            }

            $data['status']     =200;
            $data['message']    =$this->lang->line('success');
            $data['data']       =$setting_array;

            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);

        } catch (\Exception $e) {

            log_message('error', $e);

            $data['status']     =500;
            $data['message']    =$this->lang->line('internal_server_error');
            $data['data']       =array(); 

            $this->response($data, $data['status']);
        }
    }
    # Function to get minimum allowed os version end

    # Function to get base url of the app start
    public function base_url_get()
    {
        try{

            $res_arr = [
                'app_base_url' => base_url(),
            ];

            $data['status']		=200;
            $data['message']	=$this->lang->line('success');
            $data['data']		=$res_arr; 

            $this->response($data, $data['status']);

        } catch (\Exception $e) {

            #make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # Function to get base url of the app end

    // ----------------------------------------- CUSTOMER APP API START -------------------------------------------------------- //

    # Function to get hear about us start
    public function hear_about_us_get()
    {
        try{

        	$hear_about_us = $this->Common->getData('hear_about_us','*','status = 1');

            $data['status']		=200;
            $data['message']	=$this->lang->line('success');
            $data['data']		=$hear_about_us; 

            $this->response($data, $data['status']);

        } catch (\Exception $e) {

            #make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # Function to get hear about us end

    # register method start
    # This method used for customer registration when customer click on get otp button
    # Sign-up->Get OTP Action 02_signup screen
    public function customer_get_otp_post()
    {
		try
		{
	        $fullname = !empty($_POST['fullname'])?$this->db->escape_str($_POST['fullname']):'';
	        $email = !empty($_POST['email'])?$this->db->escape_str($_POST['email']):'';
	        $mobile = !empty($_POST['mobile'])?$this->db->escape_str($_POST['mobile']):'';

	        if($email == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('email_missing');
	            $data['data']		=array();
	        }else if($mobile == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('contact_missing');
	            $data['data']		=array();
	        }if($fullname==''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('name_missing');
	            $data['data']		=array();
	        }
	        else
	        {
		        $email = strtolower(trim($email));
		        # get user details for email and mobile already exists check
		        $where = '(email = "'.$email.'" OR mobile = '.$mobile.') AND ';
		        $email_and_mob_check = $this->get_user_details($where);
		        
		        if(count($email_and_mob_check) > 0)
		        {
		            $data['status']		=201;
		            $data['message']	=$this->lang->line('user_already_exists');
		            $data['data']		=array(); 
		        }else
		        {
			        $otp_code = generate_verification_code();
			        // $otp_code = 1234;
			        //$this->send_otp_on_mobile($otp_code , $mobile);
			        # Create Insert Array
			        $insert_array = [
			        	'fullname' => trim($fullname),
	                    'email' => $email,
	                    'role' => 3, # DB_User_role 1 - Super Admin 2- Restaurant Admin 3- Customer
	                    'otp_code' => $otp_code,
	                    'mobile' => $mobile,
	                    'is_otp_verified' => 0,
	                    'number_id' => '',
	                    'status' => 0,
	                    'is_online' => 0,
	                    'created_at' => time(),
	                    'updated_at' => time(),
	                ];
	        		# insert record in users table
	                $user_id = $this->Common->insertData("users",$insert_array);
	                $display_id_start = 10000; # Static value and it will be added by the last Id in increasing way
	                $sr_display_id = $display_id_start + $user_id;
	                $update_array = [
	                    'number_id' => $sr_display_id,
	                ];
	                $this->Common->updateData('users',$update_array , 'id = "'.$user_id.'"');

	                if($user_id > 0) # Insert Success
	                {
	                    # mail_send code start. This mail sends the generated 4 digit otp to user on entered email
	                    $mail_data['user_name'] = trim($fullname);
	                    $mail_data['header_title'] = APP_NAME.' : Verification Code';
	                    $email = $email;
	                    $subject = "Welcome to ".APP_NAME;
	                    $mail_data['verification_code'] = $otp_code;

	                    # Get Social urls from Database settings table
	                    $social_urls =  $this->get_social_urls();

	                    $mail_data['facebook_url'] = $social_urls['facebook'];
	                    $mail_data['google_url'] = $social_urls['google'];
	                    $mail_data['insta_url'] = $social_urls['insta'];
	                    $mail_data['website_url'] = $social_urls['website'];

	                    # load template view
	                    $message = $this->load->view('email/verification_mail', $mail_data, TRUE);
	                    // echo $message;die;
	                    // send_mail($email,$subject,$message);
	                    # mail send code end 

	                    // DEV_PENDING : SMS

	                    # Generate Response array and send
	                    $res_arr = [
	                        'id' => $user_id,
	                        'name' => trim($fullname),
	                        'email' => $email,
	                        'role' => 3, # DB_User_role 1 - Super Admin 2- Restaurant Admin 3- Customer
	                        'is_otp_verified' => 0,
	                        'verification_code' => $otp_code,
	                    ];

	                    $data['status']		=200;
	                    $data['message']	=$this->lang->line('otp_sent_success');
	                    $data['data']		=$res_arr;
		        	}else
		        	{
		        		$data['status']		=201;
			            $data['message']	=$this->lang->line('something_went_wrong');
			            $data['data']		=array(); 
		        	}
	        	}
	    	}	    	
	    	# REST_Controller provide this method to send responses
	        $this->response($data, $data['status']);
		} # Try End
		catch (\Exception $e) {
            # make error log
            log_message('error', $e);
            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 
            $this->response($data, $data['status']);
        }
    }

    # This api will be called when user click on signup button
    # signup_post method start
    public function customer_signup_post()
    {
        try
        {
	    	$code = !empty($_POST['code'])?$this->db->escape_str($_POST['code']):'';

	    	$fullname = !empty($_POST['fullname'])?$this->db->escape_str($_POST['fullname']):'';    	
	        $email = !empty($_POST['email'])?$this->db->escape_str($_POST['email']):'';
	        $email = strtolower(trim($email));
	        $mobile = !empty($_POST['mobile'])?$this->db->escape_str($_POST['mobile']):'';
	        
	        $device_id = !empty($_POST['device_id'])?$this->db->escape_str($_POST['device_id']):'';
	        $device_type = !empty($_POST['device_type'])?$this->db->escape_str($_POST['device_type']):'';
	        $device_token = !empty($_POST['device_token'])?$this->db->escape_str($_POST['device_token']):'';
	        
	        # hear_about_us
	        $hear_about_us_id = !empty($_POST['hear_about_us'])?$this->db->escape_str($_POST['hear_about_us']):'';
	        $hear_about_us_val = !empty($_POST['hear_about_us_val'])?$this->db->escape_str($_POST['hear_about_us_val']):'';

            if($code == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('verification_code_missing');
                $data['data']		=array();
            }elseif($email ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('email_missing');
                $data['data']		=array();
            }else if($device_id == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('device_id_missing');
	            $data['data']		=array();
	        }else if($device_type == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('device_type_missing');
	            $data['data']		=array();
	        }else if($device_token == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('device_token_missing');
	            $data['data']		=array();
	        }else if($fullname == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('name_missing');
	            $data['data']		=array();
	        }else if($mobile == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('contact_missing');
	            $data['data']		=array();
	        }
	        /* NOT making below two keys mandatory because it may cause issue to the already existing customer that are using old app version 28Jul2021 */
	        // else if($hear_about_us_id == ''){
	        //     $data['status']		=201;
	        //     $data['message']	=$this->lang->line('hear_about_us_id_missing');
	        //     $data['data']		=array();
	        // }else if($hear_about_us_val == ''){
	        //     $data['status']		=201;
	        //     $data['message']	=$this->lang->line('hear_about_us_val_missing');
	        //     $data['data']		=array();
	        // }
	        else
            {
            	# First of all check whether this email and mobile combination exists in Database or not (So we will use AND condition)
            	$where = '(email = "'.$email.'" AND mobile = '.$mobile.') AND ';

            	$email_check = $this->get_user_details($where);
	        	if(count($email_check) > 0) # User valid or not
	            {
	            	if($email_check[0]['is_otp_verified'] == 1) # 0 - Default 1 - Verified by OTP 2 - Verification failed(wrong otp)
	            	{
	            		$data['status']		=201;
		                $data['message']	=$this->lang->line('already_verified');
		                $data['data']		=array(); 
	            	}
	            	else
	            	{
	            		if($code == $email_check[0]['otp_code']) 
		            	{
		            		# DB_user_status 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
		            		$this->Common->updateData('users' , array('hear_about_id' => $hear_about_us_id,'hear_about_text' => $hear_about_us_val,'is_otp_verified' => 1 , 'status' => 1 ,'is_online' => 1 , 'updated_at' => time(), 'device_id' => $device_id, 'device_type' => $device_type, 'device_token' => $device_token, 'fullname' => trim($fullname)),array('email'=> $email));

		            		$token_data = [
		            			'id' => $email_check[0]['id'],
	                        	'name' => trim($fullname), # It will contain user full name as John Doe
	                        	'email' => $email,
	                        	'role' => 3, # DB_User_role 1 - Super Admin 2- Restaurant Admin 3- Customer
	                        	'timestamp' => time()
	                        ];
	                        # Create a token from the user data and send it as reponse
	                        $token = AUTHORIZATION::generateToken($token_data);

	                        $res_arr = [
	                            'id' => $email_check[0]['id'],
	                            'name' => trim($fullname),
	                            'email' => $email,
	                            'is_otp_verified' => 1,
	                            'token' => $token
	                        ];
		            		$data['status']		=200;
			                $data['message']	=$this->lang->line('verification_success');
			                $data['data']		=$res_arr;

		            	}else
		            	{
			                $data['status']		=201;
			                $data['message']	=$this->lang->line('verification_fail');
			                $data['data']		=array(); 
		            	}
	            	}
	            }else
	            {
	            	$data['status']		=201;
	                $data['message']	=$this->lang->line('user_not_valid');
	                $data['data']		=array(); 
	            }
            }
	        # REST_Controller provide this method to send responses
	    	$this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # signup_post method end

    # This function will be called when customer is verified and click on confirm & Proceed button
    # confirm_location_post method start
    public function customer_confirm_location_post()
    {
    	try
        {
	    	$lat = !empty($_POST['latitude'])?($_POST['latitude']):'';
	    	$lng = !empty($_POST['longitude'])?($_POST['longitude']):''; 
	    	$pin_address = !empty($_POST['pin_address'])?$this->db->escape_str(trim($_POST['pin_address'])):'';
	    	$unit_number = !empty($_POST['unit_number'])?$this->db->escape_str(trim($_POST['unit_number'])):'';
	    	$street_address = !empty($_POST['street_address'])?$this->db->escape_str(trim($_POST['street_address'])):'';
	    	$postal_code = !empty($_POST['postal_code'])?$this->db->escape_str(trim($_POST['postal_code'])):'';

            $tokenData = $this->verify_request();

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message'] = $this->lang->line('unauthorized_access');
            }else if($lat == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('latitude_missing');
                $data['data']		=array();
            }else if($lng == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('longitude_missing');
                $data['data']		=array();
            }else if($pin_address == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('pin_address_missing');
                $data['data']		=array();
            }else if($unit_number == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('unit_number_missing');
                $data['data']		=array();
            }
            else if($street_address == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('street_address_missing');
                $data['data']		=array();
            }
            else if($postal_code == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('postal_code_missing');
                $data['data']		=array();
            }else
            {
            	# Token passed means user exists in Database so update the below fields in Database for this user id
            	$upd = [
		        	'latitude' => $lat,
		        	'longitude' => $lng,
		        	'user_pin_address' => $pin_address,
		        	'user_unit_number' => $unit_number,
		        	'user_street_address' => $street_address,
		        	'user_postal_code' => $postal_code,
		        	'updated_at' => time(),
        		];
	        	$this->Common->updateData('users',$upd,'id = "'.$tokenData->id.'"');

	        	# After this check whether this user has any entry in delivery address? If not then add this same details in delivery_address table else NOT
	        	$has_any_del_add = $this->Common->getData('delivery_address','id','user_id = "'.$tokenData->id.'"');
	        	if(count($has_any_del_add) == 0)
	        	{
	        		# That is no delivery address added yet
	        		$insert_array = [
	        			'user_id' => $tokenData->id,
	        			'pin_address' => $pin_address,
	        			'unit_number' => $unit_number,
	        			'street_address' => $street_address,
	        			'postal_code' => $postal_code,
	        			'label_type' => 1, # Default giving  1 as Home
	        			'del_latitude' => $lat, # Default giving  1 as Home
	        			'del_longitude' => $lng, # Default giving  1 as Home
	        			'created_at' => time(), # Default giving  1 as Home
	        			'updated_at' => time(), # Default giving  1 as Home
	        		];
	        		$this->Common->insertData('delivery_address',$insert_array);
	        	}

	        	$res = [
	        		'lat' => $lat,
	        		'lng' => $lng,
	        		'pin_address' => $pin_address,
	        		'unit_number' => $unit_number,
	        		'street_address' => $street_address,
	        		'postal_code' => $postal_code,
	        	];
	        	$data['status']		=200;
                $data['message']	=$this->lang->line('location_success');
                $data['data']		=$res;
            }
	    # REST_Controller provide this method to send responses
	    $this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # confirm_location_post method end

    # This function is used when user clicks on Next button from Login screen. This api send sms on the entered mobile number after validating entered mobile number
    public function customer_login_otp_post()
    {
    	try
		{
			$contact = !empty($_POST['contact'])?$this->db->escape_str($_POST['contact']):'';

	        if($contact == '')
	        {
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('contact_missing');
	            $data['data']		=array();
	        }else
	        {
		        $where = '(mobile = '.$contact.') AND role = 3 AND ';
            	$mob_check = $this->get_user_details($where);
            	#change here
            	// echo $this->db->last_query();die;
		        if(count($mob_check) > 0)
		        {
		        	if($mob_check[0]['status'] == 3)
		        	{
		        		$data['status']		=201;
			            $data['message']	=$this->lang->line('inactive_user');
			            $data['data']		=array(); 		
		        	}else
		        	{
		        		# Mobile number exists in Database AND status is NOT 5(deleted) and NOT 3(In-active) AND role is 3(customer); So send sms and update Database
		        		$otp_code = generate_verification_code();
		        		// $otp_code = 1234;
		        		//$this->send_otp_on_mobile($otp_code , $contact);
				        # Create Insert Array
				        $update_array = [
		                    'otp_code' => $otp_code,
		                    'is_otp_verified' => 0,
		                    'status' => 0,
		                    'is_online' => 0,
		                    'updated_at' => time(),
		                ];
		        		# update record in users table
		                $this->Common->updateData('users',$update_array , 'mobile = "'.$contact.'" AND status NOT IN (2,5)');
		                $res_arr = [
		                	'otp_code' => $otp_code,
		                	'mobile' => $contact,
		                ];

		                # mail_send code start. This mail sends the generated 4 digit otp to user on entered email
	                    $mail_data['user_name'] = trim($mob_check[0]['fullname']);
	                    $mail_data['header_title'] = APP_NAME.' : Verification Code';
	                    $email = trim($mob_check[0]['email']);
	                    $subject = "Welcome to ".APP_NAME;
	                    $mail_data['verification_code'] = $otp_code;

	                    $social_urls =  $this->get_social_urls();

	                    $mail_data['facebook_url'] = $social_urls['facebook'];
	                    $mail_data['google_url'] = $social_urls['google'];
	                    $mail_data['insta_url'] = $social_urls['insta'];
	                    $mail_data['website_url'] = $social_urls['website'];

	                    # load template view
	                    $message = $this->load->view('email/verification_mail_login', $mail_data, TRUE);
	                    
	                    // send_mail($email,$subject,$message);
	                    # mail send code end 

	                    // DEV_PENDING : SMS

		                $data['status']		=200;
	                    $data['message']	=$this->lang->line('otp_sent_success');
	                    $data['data']		=$res_arr;
		        	}
		        }else
		        {
		        	$data['status']		=201;
		            $data['message']	=$this->lang->line('user_not_valid');
		            $data['data']		=array(); 
		        }
	    	}
	    	# REST_Controller provide this method to send responses
	        $this->response($data, $data['status']);
		} # Try End
		catch (\Exception $e) {
            # make error log
            log_message('error', $e);
            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 
            $this->response($data, $data['status']);
        }
    }

    # Verify and Proceed method start
    # This method is used when user opt for login and after putting OTP and click on Verify and proceed button
    public function customer_verify_and_proceed_post()
    {
    	try
		{
			$contact = !empty($_POST['contact'])?trim($this->db->escape_str($_POST['contact'])):'';
			$code = !empty($_POST['code'])?trim($this->db->escape_str($_POST['code'])):'';

			$device_id = !empty($_POST['device_id'])?trim($this->db->escape_str($_POST['device_id'])):'';
	        $device_type = !empty($_POST['device_type'])?trim($this->db->escape_str($_POST['device_type'])):'';
	        $device_token = !empty($_POST['device_token'])?trim($this->db->escape_str($_POST['device_token'])):'';

	        if($contact == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('contact_missing');
	            $data['data']		=array();
	        }elseif($code == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('verification_code_missing');
	            $data['data']		=array();
	        }else if($device_id == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('device_id_missing');
	            $data['data']		=array();
	        }else if($device_type == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('device_type_missing');
	            $data['data']		=array();
	        }else if($device_token == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('device_token_missing');
	            $data['data']		=array();
	        }else
	        {
		        $where = '(mobile = '.$contact.') AND ';
            	$mob_check = $this->get_user_details($where);

		        if(count($mob_check) > 0)
		        {
		        	if($mob_check[0]['status'] == 3)
		        	{
		        		$data['status']		=201;
			            $data['message']	=$this->lang->line('inactive_user');
			            $data['data']		=array(); 		
		        	}else
		        	{
		        		# Below code is being added for APPLE REVIEW to provide demo account
		        		if($contact == '98954564')
		        		{
		        			$code = '0000';	
		        			$mob_check[0]['otp_code'] = '0000';
		        		}
		        		if($code == $mob_check[0]['otp_code'])
		        		{
		        			# If mactes then Create token , updated_at , is_online , is_otp_verified
		        			# Create token
		        			$update_array = [
		        				'updated_at' => time(),
		        				'is_online' => 1,
		        				'is_otp_verified' => 1,
		        				'status' => 1, # DB_user_status 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
		        				'device_id' => $device_id, 
		        				'device_type' => $device_type, 
		        				'device_token' => $device_token
		        			];

							// $this->Common->updateData('users' , $update_array ,array('mobile' => $contact , 'role' => 3 , 'status' != 'NOT IN (2,3,5)'));
							$updated_at = time();
							$query = "UPDATE `users` SET `updated_at` = ".$updated_at.", `is_online` = 1, `is_otp_verified` = 1, `status` = 1, `device_id` = '".$device_id."', `device_type` = '".$device_type."', `device_token` = '".$device_token."' WHERE `mobile` = '".$contact."' AND `role` = 3 AND `status` NOT IN (2,3,5)";
							$this->Common->custom_query($query,"");
							// echo $query;die;
							// echo $this->db->last_query();die;

		        			$token_data = [
		            			'id' => $mob_check[0]['id'],
	                        	'name' => trim($mob_check[0]['fullname']), # It will contain user full name as John Doe
	                        	'email' => trim($mob_check[0]['email']),
	                        	'role' => 3, # DB_User_role 1 - Super Admin 2- Restaurant Admin 3- Customer
	                        	'timestamp' => time(),
	                        	'is_otp_verified' => 1
	                        ];
	                        # Create a token from the user data and send it as reponse
	                        $token = AUTHORIZATION::generateToken($token_data);

	                        $res_arr = [
	                        	'id' => $mob_check[0]['id'],
	                        	'name' => trim($mob_check[0]['fullname']), # It will contain user full name as John Doe
	                        	'email' => trim($mob_check[0]['email']),
	                        	'latitude' => trim($mob_check[0]['latitude']),
	                        	'longitude' => trim($mob_check[0]['longitude']),
	                        	'user_pin_address' => trim($mob_check[0]['user_pin_address']),
	                        	'user_postal_code' => trim($mob_check[0]['user_postal_code']),
	                        	'user_street_address' => trim($mob_check[0]['user_street_address']),
	                        	'user_unit_number' => trim($mob_check[0]['user_unit_number']),
	                        	'role' => 3, # DB_User_role 1 - Super Admin 2- Restaurant Admin 3- Customer
	                        	'token' => $token
	                        ];

			                $data['status']		=200;
		                    $data['message']	=$this->lang->line('login_success');
		                    $data['data']		=$res_arr;
		        		}else
		        		{
		        			$data['status']		=201;
				            $data['message']	=$this->lang->line('verification_fail');
				            $data['data']		=array();
		        		}
		        	}
		        }else
		        {
		        	$data['status']		=201;
		            $data['message']	=$this->lang->line('user_not_valid');
		            $data['data']		=array(); 
		        }
	    	}
	    	# REST_Controller provide this method to send responses
	        $this->response($data, $data['status']);
		} # Try End
		catch (\Exception $e) {
            # make error log
            log_message('error', $e);
            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 
            $this->response($data, $data['status']);
        }
    }
    # Verify and Proceed method End

    // ---------------------------------------- SOCIAL LOGIN START ------------------------------------------------------ //

    # SOCIAL LOGIN START
    # check_social_signin method start
    # This method used for check social signin(facebook,gmail,apple)
    public function check_social_signin_post()
    {
        try{

            $register_type = !empty($_POST['social_type'])?$this->db->escape_str($_POST['social_type']):'';
            # 0->normal 1-> FB 2-> gmail 3-> apple AND 0 is default
            $social_id = !empty($_POST['social_id'])?$this->db->escape_str($_POST['social_id']):'';
            # allowed social types
            $socialTypes = array('1', '2', '3');

            $device_id = !empty($_POST['device_id'])?$this->db->escape_str($_POST['device_id']):'';
            $device_type = !empty($_POST['device_type'])?$this->db->escape_str($_POST['device_type']):'';
            $device_token = !empty($_POST['device_token'])?$this->db->escape_str($_POST['device_token']):'';

            if($register_type ==''){
                $data['status']     =201;
                $data['message']    =$this->lang->line('social_type_missing');
                $data['data']       =array();
            }else if(!in_array(strtolower($register_type), $socialTypes)){
                $data['status']     =201;
                $data['message']    =$this->lang->line('invalid_social_type');
                $data['data']       =array();
            }else if($social_id == ''){
                $data['status']     =201;
                $data['message']    =$this->lang->line('social_id_missing');
                $data['data']       =array();
            }else if($device_id == ''){
                $data['status']     =201;
                $data['message']    =$this->lang->line('device_id_missing');
                $data['data']       =array();
            }else if($device_type == ''){
                $data['status']     =201;
                $data['message']    =$this->lang->line('device_type_missing');
                $data['data']       =array();
            }else if($device_token == ''){
                $data['status']     =201;
                $data['message']    =$this->lang->line('device_token_missing');
                $data['data']       =array();
            }else{
                # get user details from social id
                $where = '(social_id = "'.$social_id.'") AND ';
                $user_details = $this->get_user_details($where);
                
                if(!empty($user_details))
                {
                    # token details
                    $token_data = [
                        'id' => $user_details[0]['id'],
                        'name' => $user_details[0]['fullname'],
                        'email' => $user_details[0]['email'],
                        'role' => $user_details[0]['role'],
                        'timestamp' => time()
                    ];

                    # Create a token from the user data and send it as reponse
                    $token = AUTHORIZATION::generateToken($token_data);

                    $updateArr = [
                        'register_type' => $register_type,
                        'updated_at' => time(),
                        'is_online' => 1,
                        'device_id'=> $device_id,
                        'device_type'=> $device_type,
                        'device_token'=> $device_token
                    ];

                    # update data in users table
                    $this->Common->updateData('users',$updateArr,"id = ".$user_details[0]['id']);

                    # response data array
                    $res_arr = [
                        'id' => $user_details[0]['id'],
                        'role' => $user_details[0]['role'],
                        'fullname' => $user_details[0]['fullname'],
                        'email' => $user_details[0]['email'],
                        'status' => $user_details[0]['status'],
                        'is_otp_verified' => $user_details[0]['is_otp_verified'],
                        'latitude' => $user_details[0]['latitude'],
                        'longitude' => $user_details[0]['longitude'],
                        'token' => $token
                    ];

                    $data['status']     =200;
                    $data['message']    =$this->lang->line('success');
                    $data['data']       =$res_arr;

                }else{
                    $data['status']     =201;
                    $data['message']    =$this->lang->line('no_data_found');
                    $data['data']       =array();
                }
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']     =500;
            $data['message']    =$this->lang->line('internal_server_error');
            $data['data']       =array(); 

            $this->response($data, $data['status']);
        }
    }
    # check_social_signin method end 

    # social_signup method start
    # This method used for social signup by this api(google,facebook and apple)
    public function social_signup_post()
    {
        try
        {
            # email optional in facebook login 
            $email = !empty($_POST['email'])?$this->db->escape_str($_POST['email']):'';
            $email = strtolower(trim($email));
            $contact = !empty($_POST['mobile'])?$this->db->escape_str($_POST['mobile']):'';
            $fullname = !empty($_POST['fullname'])?$this->db->escape_str($_POST['fullname']):'';
            // $last_name = !empty($_POST['last_name'])?$this->db->escape_str($_POST['last_name']):'';
            $social_id = !empty($_POST['social_id'])?$this->db->escape_str($_POST['social_id']):'';
            # 0->normal 1->FB 2-> gmail 3-> apple AND 0 is default
            $register_type = !empty($_POST['social_type'])?$this->db->escape_str($_POST['social_type']):'';

            $device_id = !empty($_POST['device_id'])?$this->db->escape_str($_POST['device_id']):'';
            $device_type = !empty($_POST['device_type'])?$this->db->escape_str($_POST['device_type']):'';
            $device_token = !empty($_POST['device_token'])?$this->db->escape_str($_POST['device_token']):'';

            //allowed social types
            $socialTypes = array('1', '2', '3');

            if($register_type==''){
                $data['status']     =201;
                $data['message']    =$this->lang->line('social_type_missing');
                $data['data']       =array();
            }else if(!in_array(strtolower($register_type), $socialTypes)){
                $data['status']     =201;
                $data['message']    =$this->lang->line('invalid_social_type');
                $data['data']       =array();
            }else if($social_id == ''){
                $data['status']     =201;
                $data['message']    =$this->lang->line('social_id_missing');
                $data['data']       =array();
            }else if($email == ''){
                $data['status']     =201;
                $data['message']    =$this->lang->line('email_missing');
                $data['data']       =array();
            }
            # Check removed as requested by tosif
            // else if($contact == ''){
            //     $data['status']     =201;
            //     $data['message']    =$this->lang->line('contact_missing');
            //     $data['data']       =array();
            // }
            else{

                # make lowercase email
                // $where = '(email = "'.$email.'" OR mobile = '.$mobile.') AND ';
                $where = '(email = "'.$email.'") AND ';
                $user_details = $this->get_user_details($where);
                # get user details to check already existence
                // $user_details = $this->Common->getData("users","*","email = '$email' OR mobile = '$contact' and status NOT IN(2,3,5) AND role = 3");

                # user login in if condition and new registration in else condition
                if(!empty($user_details)) # That means user already available in Database
                {

                    # make update array
                    $updateArr = [
                        'social_id' => $social_id,
                        'register_type' => $register_type,
                        'is_online' => 1,
                        'updated_at' => time()
                    ];

                    # update data in users table
                    $this->Common->updateData('users',$updateArr,"id = ".$user_details[0]['id']);

                    # token details
                    $token_data = [
                        'id' => $user_details[0]['id'],
                        'email' => $user_details[0]['email'],
                        'name' => $user_details[0]['fullname'],
                        'role' => $user_details[0]['role'],
                        'timestamp' => time()
                    ];

                    # Create a token from the user data and send it as reponse
                    $token = AUTHORIZATION::generateToken($token_data);

                    #response data array
                    $res_arr = [
                        'id' => $user_details[0]['id'],
                        'role' => $user_details[0]['role'],
                        'name' => $user_details[0]['fullname'],
                        'email' => $user_details[0]['email'],
                        'status' => $user_details[0]['status'],
                        'is_otp_verified' => $user_details[0]['is_otp_verified'],
                        'latitude' => $user_details[0]['latitude'],
                        'longitude' => $user_details[0]['longitude'],
                        'token' => $token
                    ];
                    $data['status']     =200;
                    $data['message']    =$this->lang->line('success');
                    $data['data']       =$res_arr;

                }else{

                    if($fullname == ''){
		                $data['status']     =201;
		                $data['message']    =$this->lang->line('name_missing');
		                $data['data']       =array();
		            }else if($device_id == ''){
		                $data['status']     =201;
		                $data['message']    =$this->lang->line('device_id_missing');
		                $data['data']       =array();
		            }else if($device_type == ''){
		                $data['status']     =201;
		                $data['message']    =$this->lang->line('device_type_missing');
		                $data['data']       =array();
		            }else if($device_token == ''){
		                $data['status']     =201;
		                $data['message']    =$this->lang->line('device_token_missing');
		                $data['data']       =array();
		            }
                    else
                    {
                    	# make insert array
	                    $insert_array = [
		                    'fullname' => trim($fullname),
		                    'email' => $email,
		                    'role' => 3, # Customer
		                    'social_id' => $social_id,
		                    'is_otp_verified' => 1, # Directly to 1
		                    'mobile' => $contact,
		                    'register_type' => $register_type, #register_type
		                    'device_id' => $device_id,
		                    'device_type' => $device_type,
		                    'device_token' =>$device_token,
		                    'status' => 0,
		                    'is_online' => 1,
		                    'number_id' => '',
		                    'created_at' => time(),
		                    'updated_at' => time(),
		                ];
	                    # insert record in users table
	                    $user_id = $this->Common->insertData("users",$insert_array);

		                $display_id_start = 10000; # Static value and it will be added by the last Id in increasing way
		                $sr_display_id = $display_id_start + $user_id;
		                $update_array = [
		                    'number_id' => $sr_display_id,
		                ];
		                $this->Common->updateData('users',$update_array , 'id = "'.$user_id.'"');

	                    if($user_id > 0)
	                    {
	                        # get user details
	                        $where = '(id = "'.$user_id.'") AND ';
                			$user_details = $this->get_user_details($where);

	                        # token details
	                        $token_data = [
	                            'id' => $user_id,
	                            'name' => trim($fullname),
	                            'email' => $email,
	                            'role' => $user_details[0]['role'],
	                            'timestamp' => time()
	                        ];

	                        # Create a token
	                        $token = AUTHORIZATION::generateToken($token_data);

	                        # response data array
	                        $res_arr = [
	                            'id' => $user_id,
	                            'name' => trim($fullname),
	                            'email' => $email,
	                            'role' => $user_details[0]['role'],
	                            'latitude' => $user_details[0]['latitude'],
                        		'longitude' => $user_details[0]['longitude'],
	                            'is_otp_verified' => 1, # As this is social so 1
	                            'token' => $token
		                    ];

	                        $data['status']     =200;
	                        $data['message']    =$this->lang->line('success');
	                        $data['data']       =$res_arr;

	                    }else{
	                        $data['status']     =500;
	                        $data['message']    =$this->lang->line('internal_server_error');
	                        $data['data']       =array(); 
	                    }
                    }
                }
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);

        } catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']     =500;
            $data['message']    =$this->lang->line('internal_server_error');
            $data['data']       =array(); 

            $this->response($data, $data['status']);
        }
    }
    # social_signup method end 

    # SOCIAL LOGIN END

    // ---------------------------------------- SOCIAL LOGIN END ------------------------------------------------------ //

    // ----------------------------------------  PROFILE SECTION START  ---------------------------------------------- //

    # myprofile function start
    # This function is used to get user profile detail through the token details and also get the rating details given TO RESTAURANT by user (if any).
    /* Important Notes -
	Explanation about response keys of rating_details array
	rest_name : Name of the restaurant
	id : primay id of the rating table
	from_user_id : id of the customer who has given the rating
	given_rating : rating like 3.5 or 4.5
	review : string (text)
	to_type : 1 - to restaurant 2 - Kerala Eats (We are fetching only for to_type = 1)
	to_id : id of the restaurant if rating given to restaturant else it will contain 0 (We are fetching where to_id != 0)
	*/

    public function customer_my_profile_get()
    {
    	try{
    		$tokenData = $this->verify_request();
    		# For pagination of reviews
    		$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
    		$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;

	    	if($tokenData === false)
	    	{
	                $status = parent::HTTP_UNAUTHORIZED;
	                $data['status']	 = $status;
	                $data['message'] = $this->lang->line('unauthorized_access');
	            }else
	            {
					$where = '(id = "'.$tokenData->id.'") AND ';
		        	$user_details = $this->get_user_details($where);
		        	$user_details = $user_details[0];

		        	# DB_to_type to_type in ratings table 	1 - to restaurant 2 - Kerala Eats
		        	# to_id id of the restaurant if rating to restaurant else 0 for Kerala Eats
		        	$ratings = $this->Common->getData('ratings','restaurants.rest_name , ratings.*','from_user_id = "'.$tokenData->id.'" AND to_type = 1 AND to_id != 0' , array('restaurants'),array('ratings.to_id =  restaurants.id'),'','',$limit,$page);

	            	if($user_details > 0)
	            	{

		                $data['status']	= 200;
		                $data['message'] = $this->lang->line('success'); 
		                $data['data'] = array('user_details' => $user_details , 'rating_details' => $ratings);
	            	}else
	            	{
	            		$data['status']		=201;
		                $data['message']	=$this->lang->line('user_not_valid');
		                $data['data']		=array(); 
	            	}
	            }
	            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # myprofile function end

    # Update profile function start
    # This function id used to update the user profile details
    public function customer_update_profile_post()
    {
    	try{
    		$tokenData = $this->verify_request();
    		
    		$name = !empty($_POST['fullname'])?$this->db->escape_str($_POST['fullname']):'';
    		$email = !empty($_POST['email'])?$this->db->escape_str($_POST['email']):'';
    		$contact = !empty($_POST['mobile'])?$this->db->escape_str($_POST['mobile']):'';

    		if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($name == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('name_missing');
                $data['data']		=array();
            }else if($email == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('email_missing');
                $data['data']		=array();
            }else if($contact == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('contact_missing');
                $data['data']		=array();
            }else
            {
            	# Check whether newly entered email already exist
            	// $where = '(email = "'.$email.'" AND id != '.$tokenData->id.') AND ';
            	$where = '(email = "'.$email.'" OR mobile = '.$contact.') AND (id != "'.$tokenData->id.'") AND ';
		        $email_check = $this->get_user_details($where);

		        if(count($email_check) > 0)
		        {
		            $data['status']		=201;
		            $data['message']	=$this->lang->line('user_already_exists');
		            $data['data']		=array(); 
		        }else
		        {
		        	# Check whether image uploaded
		        	if(isset($_FILES['profile_pic']['name']) && $_FILES['profile_pic']['name'] != '')
		        	{
		        		# First get old image to unlink
		        		$where = '(id = '.$tokenData->id.') AND ';
		        		$image = $this->get_user_details($where);

		        		$tmp_name = $_FILES['profile_pic']['tmp_name'];
						$extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
						
						$f_name = basename($_FILES['profile_pic']['name'], '.'.$extension).PHP_EOL;
						$image_name = trim($f_name)."_".time().'.'.$extension;
						$file_path = "assets/customer/profile_pic/".$image_name;
						# replace space with _ from image name
                    	$file_path = str_replace(" ","_",$file_path);
						$moved = move_uploaded_file($_FILES['profile_pic']['tmp_name'], $file_path);

						if(!$moved)
						{
							$data['status']		=201;
					        $data['message']	=$this->lang->line('something_went_wrong');
					        $data['data']		=array();
						}
						else
						{
							# That means image uploaded successfully so we can unlink old imge
							$update_array['profile_pic'] = $file_path;
	                        if(!empty($image[0]['profile_pic']))
	                        {
	                        	// echo "pic displ".$image[0]['profile_pic'];
	                            unlink($image[0]['profile_pic']);
	                        }
						}
		        	}
		        	$update_array['fullname'] = $name;
		        	$update_array['email'] = $email;
		        	$update_array['mobile'] = $contact;
		        	$update_array['updated_at'] = time();

            		$this->Common->updateData('users',$update_array,'id="'.$tokenData->id.'"');

	                $data['status']		=200;
	                $data['message']	=$this->lang->line('profile_updated_successfully');
	                $data['data']		=array();
		        }
            }
		    $this->response($data, $data['status']);
    	}	catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # Update profile function end

    // ----------------------------------------  PROFILE SECTION END  ------------------------------------------------ //
    
    // ----------------------------------------  DELIVERY ADDRESS SECTION START  ------------------------------------- //

    # Add delivery address Start
    # This function is used to add the delivery address
    public function customer_add_delivery_address_post()
    {
    	try
        {
	    	$lat = !empty($_POST['latitude'])?($_POST['latitude']):'';
	    	$lng = !empty($_POST['longitude'])?($_POST['longitude']):''; 
	    	$pin_address = !empty($_POST['pin_address'])?$this->db->escape_str(trim($_POST['pin_address'])):'';
	    	$unit_number = !empty($_POST['unit_number'])?$this->db->escape_str(trim($_POST['unit_number'])):'';
	    	$street_address = !empty($_POST['street_address'])?$this->db->escape_str(trim($_POST['street_address'])):'';
	    	$postal_code = !empty($_POST['postal_code'])?$this->db->escape_str(trim($_POST['postal_code'])):'';
	    	$label_type = !empty($_POST['address_type'])?$this->db->escape_str(trim($_POST['address_type'])):'';
	    	# DB_label_type = 1 - Home 2 - Office 3 - Other
            $tokenData = $this->verify_request();

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message'] = $this->lang->line('unauthorized_access');
            }else if($lat == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('latitude_missing');
                $data['data']		=array();
            }else if($lng == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('longitude_missing');
                $data['data']		=array();
            }else if($pin_address == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('pin_address_missing');
                $data['data']		=array();
            }else if($unit_number == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('unit_number_missing');
                $data['data']		=array();
            }
            else if($street_address == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('street_address_missing');
                $data['data']		=array();
            }
            else if($postal_code == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('postal_code_missing');
                $data['data']		=array();
            }else if($label_type == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('address_type_missing');
                $data['data']		=array();
            }else
            {
            	# Token passed means user exists in Database so insert the below fields in Database for this user id
            	$insert = [
		        	'user_id' => $tokenData->id,
		        	'pin_address' => $pin_address,
		        	'unit_number' => $unit_number,
		        	'street_address' => $street_address,
		        	'postal_code' => $postal_code,
		        	'label_type' => $label_type,
		        	'del_latitude' => $lat,
		        	'del_longitude' => $lng,
		        	'created_at' => time(),
		        	'updated_at' => time(),
        		];
	        	$this->Common->insertData('delivery_address',$insert);

	        	$res = [
	        		'lat' => $lat,
	        		'lng' => $lng,
	        		'pin_address' => $pin_address,
	        		'unit_number' => $unit_number,
	        		'street_address' => $street_address,
	        		'postal_code' => $postal_code,
	        		'label_type' => $label_type,
	        	];

	        	$data['status']		=200;
                $data['message']	=$this->lang->line('address_added');
                $data['data']		=$res;
            }
	    # REST_Controller provide this method to send responses
	    $this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # Add delivery address End
    
    # Edit delivery address Start
    # This function is used to update the selected delivery address
    public function customer_update_delivery_address_post()
    {
    	try
        {
	    	$address_id = !empty($_POST['address_id'])?($_POST['address_id']):'';
	    	$lat = !empty($_POST['latitude'])?($_POST['latitude']):'';
	    	$lng = !empty($_POST['longitude'])?($_POST['longitude']):''; 
	    	$pin_address = !empty($_POST['pin_address'])?$this->db->escape_str(trim($_POST['pin_address'])):'';
	    	$unit_number = !empty($_POST['unit_number'])?$this->db->escape_str(trim($_POST['unit_number'])):'';
	    	$street_address = !empty($_POST['street_address'])?$this->db->escape_str(trim($_POST['street_address'])):'';
	    	$postal_code = !empty($_POST['postal_code'])?$this->db->escape_str(trim($_POST['postal_code'])):'';
	    	$label_type = !empty($_POST['address_type'])?$this->db->escape_str(trim($_POST['address_type'])):'';
	    	# DB_label_type = 1 - Home 2 - Office 3 - Other
            $tokenData = $this->verify_request();

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message'] = $this->lang->line('unauthorized_access');
            }else if($address_id == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('address_id_missing');
                $data['data']		=array();
            }else if($lat == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('latitude_missing');
                $data['data']		=array();
            }else if($lng == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('longitude_missing');
                $data['data']		=array();
            }else if($pin_address == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('pin_address_missing');
                $data['data']		=array();
            }else if($unit_number == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('unit_number_missing');
                $data['data']		=array();
            }
            else if($street_address == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('street_address_missing');
                $data['data']		=array();
            }
            else if($postal_code == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('postal_code_missing');
                $data['data']		=array();
            }else if($label_type == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('address_type_missing');
                $data['data']		=array();
            }else
            {

            	$check_address = $this->Common->getData('delivery_address','id','id = "'.$address_id.'"');
            	if(count($check_address) > 0)
            	{
            		# Token passed means user exists in Database so udpate the below fields in Database for this user id
	            	$udpate = [
			        	'pin_address' => $pin_address,
			        	'unit_number' => $unit_number,
			        	'street_address' => $street_address,
			        	'postal_code' => $postal_code,
			        	'label_type' => $label_type,
			        	'del_latitude' => $lat,
			        	'del_longitude' => $lng,
			        	'updated_at' => time(),
	        		];
		        	$this->Common->updateData('delivery_address',$udpate,'id = "'.$address_id.'" AND user_id = "'.$tokenData->id.'"');
		        	$res = [
		        		'lat' => $lat,
		        		'lng' => $lng,
		        		'pin_address' => $pin_address,
		        		'unit_number' => $unit_number,
		        		'street_address' => $street_address,
		        		'postal_code' => $postal_code,
		        		'label_type' => $label_type,
		        	];

		        	$data['status']		=200;
	                $data['message']	=$this->lang->line('address_update_success');
	                $data['data']		=$res;
            	}else
            	{
            		$data['status']		=201;
	                $data['message']	=$this->lang->line('invalid_add_id');
	                $data['data']		=array();
            	}
            }
	    # REST_Controller provide this method to send responses
	    $this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # Edit delivery address End

    # delete delivery address Start
    # This function is used to delete customer's delivery address
    public function customer_delete_delivery_address_post()
    {
    	try{
            $tokenData = $this->verify_request();
            $address_id = !empty($_POST['address_id'])?$this->db->escape_str($_POST['address_id']):'';

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']  = $status;
                $data['message']    =$this->lang->line('unauthorized_access');
            }else if($address_id == ''){
                $data['status']     =201;
                $data['message']    =$this->lang->line('address_id_missing');
                $data['data']       =array();
            }else
            {
            	$check_address = $this->Common->getData('delivery_address','id','id = "'.$address_id.'"');
            	if(count($check_address) > 0)
            	{
            		$this->Common->deleteData('delivery_address',array('id' => $address_id , 'user_id' => $tokenData->id));
            		$data['status']     =200;
		            $data['message']    =$this->lang->line('delete_success');
		            $data['data']       =array();
            	}else
            	{
            		$data['status']		=201;
	                $data['message']	=$this->lang->line('invalid_add_id');
	                $data['data']		=array();
            	}
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
        } catch (\Exception $e) {
            log_message('error', $e);

            $data['status']     =500;
            $data['message']    =$this->lang->line('internal_server_error');
            $data['data']       =array(); 

            $this->response($data, $data['status']);
        }
    }
    # delete delivery address End

    # Get delivery_address_by_id start
    # This function is used to get the delivery address detail by id for edit purpose when customer clicks on edit icon in manage address
    public function customer_delivery_address_by_id_get()
    {
    	try{
            $tokenData = $this->verify_request();
            $address_id = !empty($_GET['address_id'])?$this->db->escape_str($_GET['address_id']):'';

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']  = $status;
                $data['message']    =$this->lang->line('unauthorized_access');
            }else if($address_id == ''){
                $data['status']     =201;
                $data['message']    =$this->lang->line('address_id_missing');
                $data['data']       =array();
            }else
            {
            	$check_address = $this->Common->getData('delivery_address','id','id = "'.$address_id.'"');
            	if(count($check_address) > 0)
            	{
            		$address_data = $this->Common->getData('delivery_address' , '*' , array('id' => $address_id , 'user_id' => $tokenData->id));
            		if(count($address_data) > 0)
            		{
	            		$data['status']     =200;
			            $data['message']    =$this->lang->line('success');
			            $data['data']       =$address_data[0];
            		}else
            		{
            			$data['status']     =201;
			            $data['message']    =$this->lang->line('no_data_found');
			            $data['data']       =array();
            		}
            	}else
            	{
            		$data['status']		=201;
	                $data['message']	=$this->lang->line('invalid_add_id');
	                $data['data']		=array();
            	}
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
        } catch (\Exception $e) {
            log_message('error', $e);

            $data['status']     =500;
            $data['message']    =$this->lang->line('internal_server_error');
            $data['data']       =array(); 

            $this->response($data, $data['status']);
        }
    }

    # Get delivery_address_by_id end

    # List my all delivery address start
    # This function uses pagination and return all the delivery addresses by the logged in user
    public function customer_all_delivery_address_get()
    {
    	try{
 			$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
 			$tokenData = $this->verify_request();

			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }
			else
		    {
		    	$all_address = $this->Common->getData('delivery_address','*','user_id = "'.$tokenData->id.'"','','','','',$limit,$page);

		    	$data['status']		=200;
				$data['message']	=$this->lang->line('success');
				$data['data']		=$all_address; 
		    }
 			# REST_Controller provide this method to send responses
			$this->response($data, $data['status']);
 		} catch (\Exception $e) {

			# make error log
			log_message('error', $e);
			$data['status']		=500;
			$data['message']	=$this->lang->line('internal_server_error');
			$data['data']		=array(); 
			$this->response($data, $data['status']);
		}
    }
    # List my all delivery address end

    // ----------------------------------------  DELIVERY ADDRESS SECTION END  --------------------------------------- //

    // ----------------------------------------  NOTIFICATION SECTION START  --------------------------------------- //

    # List my notifications start
    # This method is used to list all the notifications received by logged in user. Same API will be used for Merchant app also
    public function list_all_notification_get()
    {
    	try{
 			$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
 			$tokenData = $this->verify_request();

			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }
			else
		    {
		    	$all_notifications = $this->Common->getData('notifications','*','to_user_id = "'.$tokenData->id.'"','','','id','DESC',$limit,$page);
                if(count($all_notifications) > 0)
                {
                    $data['status']		=200;
            		$data['message']	=$this->lang->line('success');
            		$data['data']		=$all_notifications;
                }else
                {
                    $data['status']		=201;
            		$data['message']	=$this->lang->line('no_data_found');
            		$data['data']		=array();
                }
		    }
 			# REST_Controller provide this method to send responses
			$this->response($data, $data['status']);
 		} catch (\Exception $e) {

			# make error log
			log_message('error', $e);
			$data['status']		=500;
			$data['message']	=$this->lang->line('internal_server_error');
			$data['data']		=array(); 
			$this->response($data, $data['status']);
		}
    }
    # List my notifications end

    // ----------------------------------------  NOTIFICATION SECTION END  --------------------------------------- //

    # wishlist_action_post Start
    # This function is used to perform action on wishlist, either remove from wishlist or add to wishlist based on the action passed.
    # 1 : Add to wishlist 2 : Remove from wishlist
    public function wishlist_action_post()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	$restaurant_id = !empty($_POST['restaurant_id'])?$this->db->escape_str($_POST['restaurant_id']):'';
	    	$action_type = !empty($_POST['action_type'])?$this->db->escape_str($_POST['action_type']):'';

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($restaurant_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }else if($action_type ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('action_missing');
                $data['data']		=array();
            }else
            {
            	if($action_type == 1) # ADD TO WISHLIST
            	{
	        		$insert_array = [
	        			'user_id' => $tokenData->id,
	        			'restaurant_id' => $restaurant_id,
	        			'created_at' => time(),
	        			'updated_at' => time()
	        		];
	        		$this->Common->insertData("wishlist",$insert_array);
	        		$data['status']		=200;
	                $data['message']	=$this->lang->line('wishlist_added');
	                $data['data']		=array('isWishList' => 1);
            	}else if($action_type == 2) # REMOVE FROM WISHLIST
            	{
					$where = array('restaurant_id' => $restaurant_id , 'user_id' => $tokenData->id);
	        		$this->Common->deleteData("wishlist",$where);

	        		$data['status']		=200;
	                $data['message']	=$this->lang->line('wishlist_removed');
	                $data['data']		=array('isWishList' => 0);
            	}else
            	{
            		$data['status']		=200;
	                $data['message']	=$this->lang->line('something_went_wrong');
	                $data['data']		=array();
            	}
            }
            //REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # wishlist_action_post End

    # wishlist_get Start
    # This function is used to get the restaurant list that are added by the customer to wishlist.
    # It gets all the relavant data of the restaurant
    public function wishlist_get()
    {
    	try{
    		$tokenData = $this->verify_request();
    		$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
    		$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;
    		$date_timestamp = !empty($_GET['date_timestamp'])?$this->db->escape_str($_GET['date_timestamp']):'';
    		
    		if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($date_timestamp == '')
        	{
        		$data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
        	}else
            {
            	# Get restaurant data which is in wishlist
            	$rest = $this->Common->getData('restaurants','restaurants.*,users.latitude,users.longitude,wishlist.*','restaurants.rest_status = 1 AND wishlist.user_id = "'.$tokenData->id.'"',array('wishlist','users'),array('wishlist.restaurant_id = restaurants.id' , 'users.id = restaurants.admin_id'),'','',$limit,$page);

            	if(count($rest) > 0)
            	{
            		# That is there are some items in user's wishlist
	            	
	            	# Calling common function to Get basic delivery time and basic preparation time from setting table
	            	$latlong = $this->Common->getData('users','latitude,longitude','id = "'.$tokenData->id.'"');
	            	$lat = $latlong[0]['latitude'];
	            	$lng = $latlong[0]['longitude'];
	            	$hours = $this->delivery_preparation_time();
            		$response = $this->get_restaurant_data($rest,$hours,$tokenData,$lat,$lng,$date_timestamp);

	            	$data['status']		=200;
		            $data['message']	=$this->lang->line('success');
		            $data['data']		=$response; 
            	}else
            	{
            		# That is no items in user's wishlist
            		$data['status']		=201;
		            $data['message']	=$this->lang->line('no_data_found');
		            $data['data']		=array(); 
            	}
            }
    		$this->response($data, $data['status']);

    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # wishlist_get End

    # Home page API LANDING PAGE API
    # This is the landnig page API.
    public function homepage_post()
    {
    	try{
    		$tokenData = $this->verify_request();
    		$page = !empty($_POST['page'])?$this->db->escape_str($_POST['page']):0;
    		$limit = !empty($_POST['limit'])?$this->db->escape_str($_POST['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;
            
            $lat = !empty($_POST['lat'])?($_POST['lat']):'';
    		$lng = !empty($_POST['lng'])?($_POST['lng']):''; 

    		# DB _business_type = 1 Food 2 Grocery 3 Alcohol
    		$business_type = !empty($_POST['business_type'])?$this->db->escape_str($_POST['business_type']):1;
    		# DB Food type : 1 (Restaurant) 2 (Kitchen/homemade)
        	$food_type = !empty($_POST['food_type'])?$this->db->escape_str($_POST['food_type']):1;
        	$date_timestamp = !empty($_POST['date_timestamp'])?$this->db->escape_str($_POST['date_timestamp']):'';
        	$is_valid = 0;


        	# NEW CHECK INCLUDED
        	if($date_timestamp == '')
        	{
        		$data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
        	}elseif($tokenData === false)
    		{
    			$tokenData = ''; # Pass empty string if token is false
    			# This fn can be called in both cases (Logged in and guest) so we will use token in fn_getrestaurant_data function to check wishlist status
                # If token is not present that it may be a guest user so in such case we need lat long
    			if($lat == ''){
	                $data['status']		=201;
	                $data['message']	=$this->lang->line('latitude_missing');
	                $data['data']		=array();
            	}else if($lng == ''){
	                $data['status']		=201;
	                $data['message']	=$this->lang->line('longitude_missing');
	                $data['data']		=array();
            	}
            	else
            	{
            		$is_valid = 1;
            	}
            }else
            {
            	$is_valid = 1;
            	$latlong = $this->Common->getData('users','latitude,longitude','id = "'.$tokenData->id.'"');
            	$lat = $latlong[0]['latitude'];
            	$lng = $latlong[0]['longitude'];
            }

            if($is_valid == 1)
            {
            	# Get Advertisement Banners
			    $ad_banners = $this->Common->getData('ad_banners','*','status = 1');
            	
            	$response = array();
            	$response['ad_banners'] = $ad_banners;
            	# Best seller Starts
	        	# Now we have 3 business type 1 : Food 2 : Grocery 3 : Alcohol
	        	# For home screen we will select the restaurant that has business type as 1 : Food (No matter for food type)
	        	# For Best seller we are considering the avg_rating parameter. First target the restaurants having avg rating between 3 to 5. If no results then target rating between 0 to 2.5
				
				// $where = '(business_type = "'.$business_type.'" AND restaurants.rest_status = 1) AND (avg_rating BETWEEN 3 AND 5 OR is_best_seller = 1)';
				$where = '(restaurants.rest_status = 1) AND (avg_rating BETWEEN 1 AND 5 OR is_best_seller = 1)';
				$rest_one = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC',$limit,$page);
				
				if(count($rest_one) > 0)
				{
					# Calling common function to Get basic delivery time and basic preparation time from setting table
		        	$hours = $this->delivery_preparation_time();
		        	# Get all the basic restaurant from common funtion
		    		$response_best_seller = $this->get_restaurant_data($rest_one,$hours,$tokenData,$lat,$lng,$date_timestamp);
		    		$response['best_sellers'] = $response_best_seller;
				}else
	        	{
	        		# rating between 0 to 2.5
	        		// $where = array('business_type' => $business_type , 'avg_rating' => 'BETWEEN 0 AND 2.5' , 'restaurants.rest_status' => 1);
	        		$where = array('avg_rating' => 'BETWEEN 0 AND 2.5' , 'restaurants.rest_status' => 1);
					$rest_two = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC',$limit,$page);
					if(count($rest_two) > 0)
					{
						# Calling common function to Get basic delivery time and basic preparation time from setting table
			        	$hours = $this->delivery_preparation_time();
			        	# Get all the basic restaurant from common funtion
			    		$response_best_seller = $this->get_restaurant_data($rest_two,$hours,$tokenData,$lat,$lng,$date_timestamp);
			    		$response['best_sellers'] = $response_best_seller;
					}else
					{
		        		$data['status']		=201;
			            $data['message']	=$this->lang->line('no_data_found');
			            $data['data']		=array(); 
					}
	        	}

	        	# TRENDING START
	        	# TARGET ORDERS (Most ordered restaurant)

	        	// $where = 'business_type = "'.$business_type.'" AND restaurants.rest_status = 1 AND is_trending = 1';
	        	$where = 'restaurants.rest_status = 1 AND is_trending = 1';
				$trending_o = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC',$limit,$page);

				if(count($trending_o) > 0)
				{
					# Calling common function to Get basic delivery time and basic preparation time from setting table
		        	$hours = $this->delivery_preparation_time();
		        	# Get all the basic restaurant from common funtion
		    		$response_best_seller = $this->get_restaurant_data($trending_o,$hours,$tokenData,$lat,$lng,$date_timestamp);
		    		$response['trending'] = $response_best_seller;	
				}else
				{
					// $where = 'restaurants.rest_status = 1 AND restaurants.business_type = "'.$business_type.'"';
					$where = 'restaurants.rest_status = 1';
		        	$query = "SELECT orders.restaurant_id,restaurants.*,COUNT(orders.restaurant_id) AS order_count,users.latitude,users.longitude FROM orders INNER JOIN restaurants ON restaurants.id = orders.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY orders.restaurant_id ORDER BY order_count DESC LIMIT ".$page.','.$limit;
		        	$trending_one = $this->Common->custom_query($query,"get");
		        	if(count($trending_one) > 0)
		        	{
		        		# Calling common function to Get basic delivery time and basic preparation time from setting table
			        	$hours = $this->delivery_preparation_time();
			        	# Get all the basic restaurant from common funtion
			    		$response_best_seller = $this->get_restaurant_data($trending_one,$hours,$tokenData,$lat,$lng,$date_timestamp);
			    		$response['trending'] = $response_best_seller;
		        	}else
		        	{
		        		// $where = 'restaurants.rest_status = 1 AND restaurants.business_type = "'.$business_type.'"';
		        		$where = 'restaurants.rest_status = 1';
		        		# TARGET WISHLIST (Most LIKED restaurant)
		        		$query = "SELECT wishlist.restaurant_id,restaurants.*,COUNT(wishlist.restaurant_id) AS wishlist_count,users.latitude,users.longitude FROM wishlist INNER JOIN restaurants ON restaurants.id = wishlist.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY wishlist.restaurant_id ORDER BY wishlist_count DESC LIMIT ".$page.','.$limit;
		        		$trending_two = $this->Common->custom_query($query,"get");
		        		if(count($trending_two) > 0)
			        	{
			        		# Calling common function to Get basic delivery time and basic preparation time from setting table
				        	$hours = $this->delivery_preparation_time();
				        	# Get all the basic restaurant from common funtion
				    		$response_trending = $this->get_restaurant_data($trending_two,$hours,$tokenData,$lat,$lng,$date_timestamp);
				    		$response['trending'] = $response_trending;
			        	}else
			        	{
			        		# TARGET RATING (0 to 2.5)
			        		// $where = array('business_type' => $business_type , 'avg_rating' => 'BETWEEN 0 AND 2.5' , 'restaurants.rest_status' => 1);
			        		$where = array('avg_rating' => 'BETWEEN 0 AND 2.5' , 'restaurants.rest_status' => 1);
							$trending_three = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC',$limit,$page);
							if(count($trending_three) > 0)
							{
								# Calling common function to Get basic delivery time and basic preparation time from setting table
					        	$hours = $this->delivery_preparation_time();
					        	# Get all the basic restaurant from common funtion
					    		$response_trending = $this->get_restaurant_data($trending_three,$hours,$tokenData,$lat,$lng,$date_timestamp);
					    		$response['trending'] = $response_trending;
							}else
							{
								$data['status']		=201;
					            $data['message']	=$this->lang->line('no_data_found');
					            $data['data']		=array(); 
							}
			        	}
		        	}
				}
	        	
	        	# TRENDING END

	        	# Get offer Popup
	        	$offer_popup = $this->Common->getData('cms','*','page_key = "offer_popup" AND status = 1');
	        	$offer_popup = $offer_popup[0];
	        	if(count($offer_popup) > 0)
	        	{
	        		$response['offer_popup'] = $offer_popup;
	        	}else
	        	{
	        		$x = new stdClass();
	        		$response['offer_popup'] = $x;
	        	}

	        	# Check cart status for items and total price
	        	if($tokenData === false || $tokenData == '') // Either token not sent or is not valid
	        	{
	        		$response['item_total'] = 0;
	        		$response['items'] = 0;
	        		$response['rest_id'] = 0;
	        		$response['rest_image'] = '';
	        	}else
	        	{
	            	$cart_response = $this->cart_calculation($tokenData);
	        		$response['item_total'] = $cart_response['item_total'];
	        		$response['items'] = $cart_response['items'];
	        		$response['rest_id'] = $cart_response['restaurant_id'];
	        		$logo_image = $this->Common->getData('restaurants','logo_image','id = "'.$cart_response['restaurant_id'].'"');
	        		$response['rest_image'] = $logo_image[0]['logo_image'];
	        	}

	        	$data['status']		=200;
		        $data['message']	=$this->lang->line('success');
		        $data['data']		=$response;
	        	
            }
    		$this->response($data, $data['status']);

    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # homepage_post End

    # restaurant_by_category START business_type
    # This function is used to get the data by category (1 Food 2 Grocery 3 Alcohol 4 Dine In)
    public function restaurant_by_category_post()
    {
    	try{
    		$tokenData = $this->verify_request();
    		
    		$page = !empty($_POST['page'])?$this->db->escape_str($_POST['page']):0;
    		$limit = !empty($_POST['limit'])?$this->db->escape_str($_POST['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;
            $order_type = !empty($_POST['order_type'])?$this->db->escape_str($_POST['order_type']):'0';
            # NEW CHANES 
            
    		# 1 : Order now 2 : self pickup_time_from 3 : Order for later
            $lat = !empty($_POST['lat'])?($_POST['lat']):'';
    		$lng = !empty($_POST['lng'])?($_POST['lng']):''; 

    		# DB _business_type = 1 Food 2 Grocery 3 Alcohol 4 Dine in
    		$business_type = !empty($_POST['category_type'])?$this->db->escape_str($_POST['category_type']):'';
    		# DB Food type : 1 (Restaurant) 2 (Kitchen)
        	$food_type = !empty($_POST['food_type'])?$this->db->escape_str($_POST['food_type']):0;
        	$date_timestamp = !empty($_POST['date_timestamp'])?$this->db->escape_str($_POST['date_timestamp']):'';
        	$is_valid = 0;
        	if($business_type == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('business_type_missing');
                $data['data']		=array();
        	}else if($date_timestamp == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
        	}
        	// else if($order_type ==''){
         //        $data['status']		=201;
         //        $data['message']	=$this->lang->line('order_type_missing');
         //        $data['data']		=array();
         //    }
            else if($tokenData === false){
    			$tokenData = ''; # Pass empty string if token is false
    			# This fn can be called in both cases (Logged in and guest) so we will use token in fn_getrestaurant_data function to check wishlist status
               
                # If token is not present that it may be a guest user so in such case we need lat long
    			if($lat == ''){
	                $data['status']		=201;
	                $data['message']	=$this->lang->line('latitude_missing');
	                $data['data']		=array();
            	}else if($lng == ''){
	                $data['status']		=201;
	                $data['message']	=$this->lang->line('longitude_missing');
	                $data['data']		=array();
            	}
            	else
            	{
            		$is_valid = 1;
            	}
            }else
            {
            	$is_valid = 1;
            	$latlong = $this->Common->getData('users','latitude,longitude','id = "'.$tokenData->id.'"');
            	$lat = $latlong[0]['latitude'];
            	$lng = $latlong[0]['longitude'];
            }

            if($is_valid == 1)
            {
            	$response = array();

            	if($business_type == 1 || $business_type == 2 || $business_type == 3)
            	{
            		if($business_type == 2 || $business_type == 3)
	            	{
	            		$food_type = 0;
	            	}else if($business_type == 1 && $food_type == 0)
	            	{
	            		$food_type = 1;
	            	}

	            	if($order_type == 1)
	            	{
		        		$where = 'restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_now_accept = 1 ';
	            	}elseif($order_type == 2)
	            	{
		        		$where = 'restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_self_pickup_accept = 1 ';
	            	}elseif($order_type == 3)
	            	{
	            		$where = 'restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_later_accept = 1 ';
	            	}elseif($order_type == 0) # Means this request is irrespective of order type so pass all type of restrurant based on food type and business type will be returned
	            	{
	            		$where = 'restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 ';
	            	}
		        	// $query = "SELECT restaurants.*,restaurants.id AS restaurant_id,users.latitude,users.longitude FROM restaurants INNER JOIN users ON users.id = restaurants.admin_id WHERE $where  LIMIT ".$page.",".$limit;
		        	$query = "SELECT restaurants.*,restaurants.id AS restaurant_id,users.latitude,users.longitude FROM restaurants INNER JOIN users ON users.id = restaurants.admin_id WHERE ".$where;
		        	$rest_by_cat = $this->Common->custom_query($query,"get");
		        	
		        	// echo $this->db->last_query();
		        	// echo "<pre>";
		        	// print_r($rest_by_cat);
		        	// die;
		        	if(count($rest_by_cat) > 0)
		        	{
		        		# Calling common function to Get basic delivery time and basic preparation time from setting table
			        	$hours = $this->delivery_preparation_time();
			        	# Get all the basic restaurant from common funtion
			    		$response = $this->get_restaurant_data($rest_by_cat,$hours,$tokenData,$lat,$lng,$date_timestamp);

			    		// echo "RESTULLL<pre>";
			    		// print_r($response);
			    		// die;
			    		# Now we need to send list as per open and close status like restro that are open will come first then closed will come
			    		# So for this we have to check whether restro is open or Closed
			    		# First check for offline status key
			    		# Then if found open then check open close time
			    		# Then if found opne then check break start time AND break end time

			    		if(count($response) > 0)
			    		{
			    			# START FROM HERE
			    			foreach ($response as $key => $value) 
			    			{
			    				# Check whether offline status key is empty?
			    				$arr = (array)$value['offline_status'];
			    				if(!isset($arr['offline_tag']))
			    				{
			    					// echo "1<br>";
			    					# That means there is no entry in offline table so now check for open close time AND then break start and end time
			    					// echo "current_time ".$current_time = date("H:i"); # Ex : 15:20 (3H : 20m)
			    					// echo "CHANCHAL ".time();
			    					$from = new DateTimeZone('Asia/Singapore');
			    					$currDate  = new DateTime('now', $from);
			    					// $current_time = date($date,"H:i");
			    					$current_time = $currDate->format('H:i');
			    					// echo "Current time " . $current_time;
			    					// echo "<br>The time in " . date_default_timezone_get() . " is " . date("H:i");
			    					$open_time = $value['restaurant']['open_time'];
			    					$close_time = $value['restaurant']['close_time'];
			    					// echo "<br>";
			    					if($current_time >= $open_time && $current_time <= $close_time) 
			    					# Ex open time is 10 and close is 18 and current time is 13
			    					# So 13 >= 10 AND 13 <= 18 ====> It is OPEN
			    					{
			    						// echo "3<br>";
				    					$break_start_time = $value['restaurant']['break_start_time'];
				    					$break_end_time = $value['restaurant']['break_end_time'];
			    						# Check for break start and break end
			    						# If current time is in between Open and close then restro is OPEN
			    						# BUT if current time is in between Break start and Break end then restro is CLOSED
			    						if($current_time >= $break_start_time && $current_time <= $break_end_time)
			    						{
			    							// echo "4<br>";
			    							# It is closed
			    							// $response_return[$key]['closed_restro'] = $value;
			    							$response_return[$key] = $value;
			    							$response_return[$key]['is_open'] = 0;
			    						}else
			    						{
			    							// echo "5<br>";
			    							# Send it in open 
			    							$response_return[$key] = $value;
			    							$response_return[$key]['is_open'] = 1;
			    						}
			    					}else
			    					{
			    						// echo "6<br>";
			    						# It is closed
			    						$response_return[$key] = $value;
			    						$response_return[$key]['is_open'] = 0;
			    					}
			    				}else
			    				{
			    					// echo "2<br>";
			    					$arr_o = (array)$value['offline_status'];
			    					# That is entry in offline table exists. So if current time is less than offline_from and greater than offline_to that means restaurant is OPEN
			    					if($arr_o['offline_from'] != 0)
			    					{
			    						# That is some valid value exists
			    						$offline_from = $arr_o['offline_from'];
			    						$offline_to = $arr_o['offline_to'];
			    						// echo "<br>timeis ".time();
			    						// echo "TIMESTAMP".time();
			    						if($offline_from <= time() && time() >= $offline_to)
			    						{
			    							// echo "CAMEHEREEEE?";
			    							# That is it is OPEN now.
			    							# So check for open close time and THEN break start end time
			    							$from = new DateTimeZone('Asia/Singapore');
					    					$currDate  = new DateTime('now', $from);
					    					$current_time = $currDate->format('H:i');

					    					$open_time = $value['restaurant']['open_time'];
					    					$close_time = $value['restaurant']['close_time'];

					    					if($current_time >= $open_time && $current_time <= $close_time) 
					    					# Ex open time is 10 and close is 18 and current time is 13
					    					# So 13 >= 10 AND 13 <= 18 ====> It is OPEN
					    					{
					    						// echo "WW";
						    					$break_start_time = $value['restaurant']['break_start_time'];
						    					$break_end_time = $value['restaurant']['break_end_time'];
					    						# Check for break start and break end
					    						# If current time is in between Open and close then restro is OPEN
					    						# BUT if current time is in between Break start and Break end then restro is CLOSED
					    						if($current_time >= $break_start_time && $current_time <= $break_end_time)
					    						{
					    							// echo "QQ";
					    							# It is closed
					    							$response_return[$key] = $value;
					    							$response_return[$key]['is_open'] = 0;
					    						}else
					    						{
					    							// echo "EE";
					    							# Send it in open 
					    							$response_return[$key] = $value;
					    							$response_return[$key]['is_open'] = 1;
					    						}
					    					}else
					    					{
					    						// echo "RR";
					    						# It is closed
					    						$response_return[$key] = $value;
					    						$response_return[$key]['is_open'] = 0;
					    					}
			    						}else
			    						{
			    							// echo "TT";
			    							# It is closed
		    								$response_return[$key] = $value;
		    								$response_return[$key]['is_open'] = 0;
			    						}
			    					}
			    				}
			    			}
			    		}
			    		# Now we need to first send open and then closed restaurants
						if(count($response) > 0)
						{
							$open = array();
							$close = array();
							foreach ($response as $key => $value) 
							{
								if($value['restro_is_open'] == 1)
								{
									$open[] = $value;
								}else
								{
									$close[] = $value;
								}
							}
						}

						# If order_type is Order Now then DONOT send closed restaurants
						if($order_type == 1)
						{
							$final_array = $open;							
						}else
						{
							$final_array = array_merge($open,$close);
						}

			        	$data['status']		=200;
				        $data['message']	=$this->lang->line('success');
				        $data['data']		=$final_array;
		        	}else
		        	{
		        		$data['status']		=201;
			            $data['message']	=$this->lang->line('no_data_found');
			            $data['data']		=array(); 
		        	}
            	}else # DINE IN
            	{
            		# Dine In
            		# For dine in we need to pass Trending restro also
            		# is_dinein_accept must be 1 that is get those restro who are trending AND accepts dine in
            		# TRENDING START
		        	# TARGET ORDERS (Most ordered restaurant)
		        	$where = 'restaurants.is_dinein_accept = 1 AND restaurants.rest_status = 1 AND is_trending = 1';
					// $trending_o = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC',$limit,$page);
					$trending_o = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC');

					if(count($trending_o) > 0)
					{
						# Calling common function to Get basic delivery time and basic preparation time from setting table
			        	$hours = $this->delivery_preparation_time();
			        	# Get all the basic restaurant from common funtion
			    		$response_best_seller = $this->get_restaurant_data($trending_o,$hours,$tokenData,$lat,$lng,$date_timestamp);
			    		$response['trending'] = $response_best_seller;	
					}else
					{
						$where = 'restaurants.is_dinein_accept = 1 AND restaurants.rest_status = 1 ';
			        	// $query = "SELECT orders.restaurant_id,restaurants.*,COUNT(orders.restaurant_id) AS order_count,users.latitude,users.longitude FROM orders INNER JOIN restaurants ON restaurants.id = orders.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY orders.restaurant_id ORDER BY order_count DESC LIMIT ".$page.','.$limit;
			        	$query = "SELECT orders.restaurant_id,restaurants.*,COUNT(orders.restaurant_id) AS order_count,users.latitude,users.longitude FROM orders INNER JOIN restaurants ON restaurants.id = orders.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY orders.restaurant_id ORDER BY order_count DESC";
			        	$trending_one = $this->Common->custom_query($query,"get");
			        	if(count($trending_one) > 0)
			        	{
			        		# Calling common function to Get basic delivery time and basic preparation time from setting table
				        	$hours = $this->delivery_preparation_time();
				        	# Get all the basic restaurant from common funtion
				    		$response_best_seller = $this->get_restaurant_data($trending_one,$hours,$tokenData,$lat,$lng,$date_timestamp);
				    		$response['trending'] = $response_best_seller;
			        	}else
			        	{
			        		$where = 'restaurants.is_dinein_accept = 1 AND restaurants.rest_status = 1 ';
			        		# TARGET WISHLIST (Most LIKED restaurant)
			        		// $query = "SELECT wishlist.restaurant_id,restaurants.*,COUNT(wishlist.restaurant_id) AS wishlist_count,users.latitude,users.longitude FROM wishlist INNER JOIN restaurants ON restaurants.id = wishlist.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY wishlist.restaurant_id ORDER BY wishlist_count DESC LIMIT ".$page.','.$limit;
			        		$query = "SELECT wishlist.restaurant_id,restaurants.*,COUNT(wishlist.restaurant_id) AS wishlist_count,users.latitude,users.longitude FROM wishlist INNER JOIN restaurants ON restaurants.id = wishlist.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY wishlist.restaurant_id ORDER BY wishlist_count DESC";
			        		$trending_two = $this->Common->custom_query($query,"get");
			        		if(count($trending_two) > 0)
				        	{
				        		# Calling common function to Get basic delivery time and basic preparation time from setting table
					        	$hours = $this->delivery_preparation_time();
					        	# Get all the basic restaurant from common funtion
					    		$response_trending = $this->get_restaurant_data($trending_two,$hours,$tokenData,$lat,$lng,$date_timestamp);
					    		$response['trending'] = $response_trending;
				        	}else
				        	{
				        		# TARGET RATING (0 to 2.5)
				        		$where = array('restaurants.is_dinein_accept' => 1 , 'avg_rating' => 'BETWEEN 0 AND 2.5' , 'restaurants.rest_status' => 1);
								// $trending_three = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC',$limit,$page);
								$trending_three = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC');
								if(count($trending_three) > 0)
								{
									// echo "here15";
									# Calling common function to Get basic delivery time and basic preparation time from setting table
						        	$hours = $this->delivery_preparation_time();
						        	# Get all the basic restaurant from common funtion
						    		$response_trending = $this->get_restaurant_data($trending_three,$hours,$tokenData,$lat,$lng,$date_timestamp);
						    		$response['trending'] = $response_trending;
								}else
								{
									// echo "here16";
									$data['status']		=201;
						            $data['message']	=$this->lang->line('no_data_found');
						            $data['data']		=array(); 
								}
				        	}
			        	}
					}
		        	
		        	# TRENDING END

		        	# Also get all the restaurant who accepts dinein
		        	$where = 'restaurants.is_dinein_accept = 1 AND restaurants.rest_status = 1 ';
		        	// $query = "SELECT restaurants.*,restaurants.id AS restaurant_id,users.latitude,users.longitude FROM restaurants INNER JOIN users ON users.id = restaurants.admin_id WHERE $where  LIMIT ".$page.",".$limit;
		        	$query = "SELECT restaurants.*,restaurants.id AS restaurant_id,users.latitude,users.longitude FROM restaurants INNER JOIN users ON users.id = restaurants.admin_id WHERE ".$where;
		        	$rest_by_cat_dinein = $this->Common->custom_query($query,"get");
		        	
		        	if(count($rest_by_cat_dinein) > 0)
		        	{
		        		# Calling common function to Get basic delivery time and basic preparation time from setting table
			        	$hours = $this->delivery_preparation_time();
			        	# Get all the basic restaurant from common funtion
			    		$dine_in_arr = $this->get_restaurant_data($rest_by_cat_dinein,$hours,$tokenData,$lat,$lng,$date_timestamp);
		        	}

		        	# Now we need to first send open and then closed restaurants
					if(count($dine_in_arr) > 0)
					{
						$open = array();
						$close = array();
						foreach ($dine_in_arr as $key => $value) 
						{
							if($value['restro_is_open'] == 1)
							{
								$open[] = $value;
							}else
							{
								$close[] = $value;
							}
						}
					}

					# If order_type is Order Now then DONOT send closed restaurants
					if($order_type == 1)
					{
						$final_array = $open;							
					}else
					{
						$response['dine_in'] = array_merge($open,$close);
					}
                    
                    if(count($response) > 0)
                    {
                        $data['status']		=200;
        		        $data['message']	=$this->lang->line('success');
        		        $data['data']		=$response;    
                    }else
                    {
                        $data['status']		=201;
        		        $data['message']	=$this->lang->line('no_data_found');
        		        $data['data']		=$response;    
                    }
            	}
            }
    		$this->response($data, $data['status']);

    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # restaurant_by_category END

    # view_all_post START
    
    # This function is used to get all Trending restaurant when user click on view all
    public function view_all_post()
    {
    	try{
    		$tokenData = $this->verify_request();
    		$page = !empty($_POST['page'])?$this->db->escape_str($_POST['page']):0;
    		$limit = !empty($_POST['limit'])?$this->db->escape_str($_POST['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;
            
            $lat = !empty($_POST['lat'])?($_POST['lat']):'';
    		$lng = !empty($_POST['lng'])?($_POST['lng']):''; 

    		# DB _business_type = 1 Food 2 Grocery 3 Alcohol
    		$business_type = !empty($_POST['business_type'])?$this->db->escape_str($_POST['business_type']):1;
    		# DB Food type : 1 (Restaurant) 2 (Kitchen)
        	$food_type = !empty($_POST['food_type'])?$this->db->escape_str($_POST['food_type']):1;
        	$date_timestamp = !empty($_POST['date_timestamp'])?$this->db->escape_str($_POST['date_timestamp']):'';
        	$is_valid = 0;

        	if($date_timestamp == '')
        	{
        		$data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
        	}else if($tokenData === false)
    		{
    			$tokenData = ''; # Pass empty string if token is false
    			# This fn can be called in both cases (Logged in and guest) so we will use token in fn_getrestaurant_data function to check wishlist status
               
                # If token is not present that it may be a guest user so in such case we need lat long
    			if($lat == ''){
	                $data['status']		=201;
	                $data['message']	=$this->lang->line('latitude_missing');
	                $data['data']		=array();
            	}else if($lng == ''){
	                $data['status']		=201;
	                $data['message']	=$this->lang->line('longitude_missing');
	                $data['data']		=array();
            	}
            	else
            	{
            		$is_valid = 1;
            	}
            }else
            {
            	$is_valid = 1;
            	$latlong = $this->Common->getData('users','latitude,longitude','id = "'.$tokenData->id.'"');
            	$lat = $latlong[0]['latitude'];
            	$lng = $latlong[0]['longitude'];
            }

            if($is_valid == 1)
            {
            	$response = array();

	        	# TRENDING START
	        	# TARGET ORDERS (Most ordered restaurant)
	        	
	        	// $where = 'business_type = "'.$business_type.'" AND restaurants.rest_status = 1 AND is_trending = 1';
	        	$where = 'restaurants.rest_status = 1 AND is_trending = 1';
				// $trending_o = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC',$limit,$page);
				$trending_o = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC');

				if(count($trending_o) > 0)
				{
					# Calling common function to Get basic delivery time and basic preparation time from setting table
		        	$hours = $this->delivery_preparation_time();
		        	# Get all the basic restaurant from common funtion
		    		$response_best_seller = $this->get_restaurant_data($trending_o,$hours,$tokenData,$lat,$lng,$date_timestamp);
		    		$response_trending = $response_best_seller;	
				}else
				{
					// $where = 'restaurants.rest_status = 1 AND restaurants.business_type = "'.$business_type.'"';
					$where = 'restaurants.rest_status = 1';
		        	// $query = "SELECT orders.restaurant_id,restaurants.*,COUNT(orders.restaurant_id) AS order_count,users.latitude,users.longitude FROM orders INNER JOIN restaurants ON restaurants.id = orders.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY orders.restaurant_id ORDER BY order_count DESC LIMIT ".$page.",".$limit;
		        	$query = "SELECT orders.restaurant_id,restaurants.*,COUNT(orders.restaurant_id) AS order_count,users.latitude,users.longitude FROM orders INNER JOIN restaurants ON restaurants.id = orders.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY orders.restaurant_id ORDER BY order_count DESC";
		        	$trending_one = $this->Common->custom_query($query,"get");
		        	if(count($trending_one) > 0)
		        	{
		        		# Calling common function to Get basic delivery time and basic preparation time from setting table
			        	$hours = $this->delivery_preparation_time();
			        	# Get all the basic restaurant from common funtion
			    		$response_trending = $this->get_restaurant_data($trending_one,$hours,$tokenData,$lat,$lng,$date_timestamp);
		        	}else
		        	{
		        		// $where = 'restaurants.rest_status = 1 AND restaurants.business_type = "'.$business_type.'"';
		        		$where = 'restaurants.rest_status = 1';
		        		# TARGET WISHLIST (Most LIKED restaurant)
		        		// $query = "SELECT wishlist.restaurant_id,restaurants.*,COUNT(wishlist.restaurant_id) AS wishlist_count,users.latitude,users.longitude FROM wishlist INNER JOIN restaurants ON restaurants.id = wishlist.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY wishlist.restaurant_id ORDER BY wishlist_count DESC LIMIT ".$page.",".$limit;
		        		$query = "SELECT wishlist.restaurant_id,restaurants.*,COUNT(wishlist.restaurant_id) AS wishlist_count,users.latitude,users.longitude FROM wishlist INNER JOIN restaurants ON restaurants.id = wishlist.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY wishlist.restaurant_id ORDER BY wishlist_count DESC";
		        		$trending_two = $this->Common->custom_query($query,"get");
		        		if(count($trending_two) > 0)
			        	{
			        		# Calling common function to Get basic delivery time and basic preparation time from setting table
				        	$hours = $this->delivery_preparation_time();
				        	# Get all the basic restaurant from common funtion
				    		$response_trending = $this->get_restaurant_data($trending_two,$hours,$tokenData,$lat,$lng,$date_timestamp);
			        	}else
			        	{
			        		# TARGET RATING (0 to 2.5)
			        		// $where = array('business_type' => $business_type , 'avg_rating' => 'BETWEEN 0 AND 2.5' , 'restaurants.rest_status' => 1);
			        		$where = array('avg_rating' => 'BETWEEN 0 AND 2.5' , 'restaurants.rest_status' => 1);
							// $trending_three = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC',$limit,$page);
							$trending_three = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC');
							if(count($trending_three) > 0)
							{
								# Calling common function to Get basic delivery time and basic preparation time from setting table
					        	$hours = $this->delivery_preparation_time();
					        	# Get all the basic restaurant from common funtion
					    		$response_trending = $this->get_restaurant_data($trending_three,$hours,$tokenData,$lat,$lng,$date_timestamp);
							}else
							{
								$data['status']		=201;
					            $data['message']	=$this->lang->line('no_data_found');
					            $data['data']		=array(); 
							}
			        	}
		        	}
				}

				# Now we need to first send open and then closed restaurants
				if(count($response_trending) > 0)
				{
					$open = array();
					$close = array();
					foreach ($response_trending as $key => $value) 
					{
						if($value['restro_is_open'] == 1)
						{
							$open[] = $value;
						}else
						{
							$close[] = $value;
						}
					}
				}

				# If order_type is Order Now then DONOT send closed restaurants
				if($order_type == 1)
				{
					$final_array = $open;							
				}else
				{
					$final_array = array_merge($open,$close);
				}
	        	
	        	# TRENDING END
	        	$data['status']		=200;
		        $data['message']	=$this->lang->line('success');
		        $data['data']		=$final_array;
	        	
            }
    		$this->response($data, $data['status']);

    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # view_all_post END

    # product_listing_by_restaurant start
    # This function is used to get the restaurant basic detail and categories of that restaurant along with products details
    # This function is called when user click on any restaurant
    public function product_listing_by_restaurant_post()
    {
    	try{
    		$tokenData = $this->verify_request();
    		// echo "<pre>";
    		// print_r($tokenData);
    		// die;
    		$page = !empty($_POST['page'])?$this->db->escape_str($_POST['page']):0;
    		$limit = !empty($_POST['limit'])?$this->db->escape_str($_POST['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;

    		$lat = !empty($_POST['lat'])?($_POST['lat']):'';
    		$lng = !empty($_POST['lng'])?($_POST['lng']):''; 

    		$restaurant_id = !empty($_POST['restaurant_id'])?$this->db->escape_str($_POST['restaurant_id']):'';
    		$date_timestamp = !empty($_POST['date_timestamp'])?$this->db->escape_str($_POST['date_timestamp']):'';

    		// get_restaurant_open_close_status($rest_id , $date_timestamp)
    		# 1 - Veg 2 - Non veg
    		$is_veg = !empty($_POST['is_veg'])?$this->db->escape_str($_POST['is_veg']):1;

    		$is_valid = 0;

    		if($date_timestamp == '')
        	{
        		$data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
        	}else if($tokenData === false)
    		{
    			$tokenData = ''; # Pass empty string if token is false
    			# This fn can be called in both cases (Logged in and guest) so we will use token in fn_getrestaurant_data function to check wishlist status
                # If token is not present that it may be a guest user so in such case we need lat long
    			if($lat == ''){
	                $data['status']		=201;
	                $data['message']	=$this->lang->line('latitude_missing');
	                $data['data']		=array();
            	}else if($lng == ''){
	                $data['status']		=201;
	                $data['message']	=$this->lang->line('longitude_missing');
	                $data['data']		=array();
            	}else if($restaurant_id == ''){
	                $data['status']		=201;
	                $data['message']	=$this->lang->line('rest_id_missing');
	                $data['data']		=array();
            	}else
            	{
            		$is_valid = 1;
            	}
            }else
            {
            	$is_valid = 1;
            	$latlong = $this->Common->getData('users','latitude,longitude','id = "'.$tokenData->id.'"');
            	$lat = $latlong[0]['latitude'];
            	$lng = $latlong[0]['longitude'];
            }
            if($is_valid == 1)
            {
            	$response = array();
            	# Check Wishlist status
				if($tokenData == '')
				{
					$response['isWishList'] = 0;
				}else
				{
					$wishlist_check = $this->Common->getData('wishlist','id','restaurant_id = "'.$restaurant_id.'" AND user_id = "'.$tokenData->id.'"');
					if(count($wishlist_check) > 0)
					{
						$response['isWishList'] = 1;	
					}else
					{
						$response['isWishList'] = 0;
					}
				}
            	# Fiest of all get restaurant detail by Id
            	# DB_to_type = 1 - For food to restaurant 2 - For food to Kerala Eats 3 - For delivery to restaurant
            	$rest = $this->Common->getData('restaurants','restaurants.*,restaurants.id AS restaurant_id,users.latitude,users.longitude','restaurants.id = "'.$restaurant_id.'"',array('users'),array('users.id = restaurants.admin_id'));
            	// echo $this->db->last_query();die;
            	$response['restaurant_detail'] = $rest[0];
            	$response['restaurant_detail']['rest_name'] = stripslashes($rest[0]['rest_name']);
            	$restro_is_open = $this->get_restaurant_open_close_status($restaurant_id , $date_timestamp); # $restro_is_open 1 YES ELSE CLOSED
				// $response['restaurant_detail']['restro_is_open'] = $restro_is_open;
				$response['restaurant_detail']['restro_is_open'] = $restro_is_open['status'];
				$response['restaurant_detail']['next_open_time'] = $restro_is_open['next_open_time'];
            	# Check whether any offline entry for the listed restaurant is available?
				$offline_status = $this->Common->getData('rest_offline','rest_offline.offline_tag,rest_offline.offline_value,rest_offline.offline_from,rest_offline.offline_to','rest_offline.rest_id = "'.$restaurant_id.'"',array('restaurants'),array('restaurants.id = rest_offline.rest_id'));
				if(count($offline_status) > 0)
				{
					$response['restaurant_detail']['offline_status'] = $offline_status[0];
				}else
				{
					$x = new stdClass();
					$response['restaurant_detail']['offline_status'] = $x;
				}
            	$del_rating = $this->Common->getData('ratings','AVG(given_rating) as del_rating','to_type = 1 AND to_id = "'.$restaurant_id.'"');
            	if(count($del_rating) > 0 && $del_rating[0]['deli_rating'] != '')
            	{
            		$response['restaurant_detail']['del_rating'] = $del_rating[0]['del_rating'];
            	}else
            	{
            		$response['restaurant_detail']['del_rating'] = 0;
            	}
            	$query_rating_cnt = "SELECT count(id) as del_rating_count FROM `ratings` WHERE to_id = ".$restaurant_id." and to_type = 3";
            	$rating_cnt = $this->Common->custom_query($query_rating_cnt,'get');
            	$deli_rating_cnt = $rating_cnt[0]['del_rating_count'];

				$query_rating_cnt = "SELECT count(id) as dinein_rating_count FROM `ratings` WHERE to_id = ".$restaurant_id." and to_type = 1";
				$rating_cnt = $this->Common->custom_query($query_rating_cnt,'get');
            	$dinin_rating_cnt = $rating_cnt[0]['dinein_rating_count'];
            	$response['deli_rating_cnt'] = $deli_rating_cnt;
            	$response['dinin_rating_cnt'] = $dinin_rating_cnt;


            	# Now get AVAILABLE Categories and products
            	// $categories = $this->Common->getData('categories','id,category_name,category_status','category_status = 1 AND restaurant_id = "'.$restaurant_id.'"','','','','',$limit,$page);
            	$categories = $this->Common->getData('categories','id,category_name,category_status','category_status = 1 AND restaurant_id = "'.$restaurant_id.'"','','','cat_position_order','ASC');
            	// echo "<pre>cattttt";
            	// echo $this->db->last_query();
            	// print_r($categories);
            	if(count($categories) > 0)
            	{
	            	$enable_id = array();
	            	$super_keyv = 0;
	            	
	            	$abc = 0;

	            	foreach ($categories as $index => $cat) 
	            	{
	            		$super_key = 0;
						$status['category_status'] = $this->Common->getData('categories_offline','*','category_id = "'.$cat['id'].'"');
						# Here we will have only one key as we are fetching category by its unique id so we can use 0 index
						if(count($status['category_status']) > 0)
						{
							# DB_offline_tag : 1 - Hour 2 - Day 3 - Multiple days
		            		$offline_tag = $status['category_status'][0]['offline_tag'];
		            		$offline_value = $status['category_status'][0]['offline_value'];
		            		$offline_from = $status['category_status'][0]['offline_from'];
		            		$offline_to = $status['category_status'][0]['offline_to'];

		            		if ((time() >= $offline_from) && (time() <= $offline_to))
		            		{
    							# "CURRENTLY DISABLE" so no need to pass its data
							}else{
    							// $response['other_detail'][$index]['id'] = $cat['id'];
	          					// $response['other_detail'][$index]['name'] = $cat['category_name'];
	            				
	            				if($is_veg == 1)
	            				{
	            					# If veg is on then return only veg product else return both
	            					$veg_where = ' AND is_veg = "'.$is_veg.'"';
	            				}else
	            				{
	            					$veg_where = '';
	            				}
    							
    							$prod_query = "SELECT products.id AS product_id,products.product_name ,products.min_qty, products.product_position_order,products.short_desc AS product_description,products.price,products.offer_price,products.product_image,products.is_veg AS is_product_veg,products_offline.id AS off_id ,products_offline.rest_id , products_offline.category_id , products_offline.offline_tag,products_offline.offline_value,products_offline.offline_from,products_offline.offline_to FROM products LEFT OUTER JOIN products_offline ON products.id = products_offline.product_id WHERE products.product_status = 1 AND products.category_id = ".$cat['id']." AND products.restaurant_id = ".$restaurant_id.$veg_where." ORDER BY product_position_order ASC";
	            				
	            				$product_status = $this->Common->custom_query($prod_query,'get');
	            				
	            				if(count($product_status) > 0)
	            				{
	            					
	            					$response['other_detail'][$abc]['id'] = $cat['id'];
	            					$response['other_detail'][$abc]['name'] = $cat['category_name'];
		            				# Here we may have multiple products so we can not directly use 0 index so we need to use foreach loop
		            				foreach ($product_status as $key=> $pd_status)
		            				{
		    							$pd_offline_tag = $pd_status['offline_tag'];
					            		$pd_offline_value = $pd_status['offline_value'];
					            		$pd_offline_from = $pd_status['offline_from'];
					            		$pd_offline_to = $pd_status['offline_to'];

					            		if ((time() >= $pd_offline_from) && (time() <= $pd_offline_to))
					            		{
			    							# "CURRENTLY DISABLE" so no need to pass its data
										}else
										{
					            			$response['other_detail'][$abc]['product'][$super_key] = $pd_status;
					            			$response['other_detail'][$abc]['is_product_avl'] = 1;
					            			if($tokenData === false || $tokenData == '' ) # Empty check because we made it as empty in 2123 line
					            			{
					            				$response['other_detail'][$abc]['product'][$super_key]['isCart'] = 0;
					            				$is_variant = $this->check_if_variant_available($pd_status['product_id']);
					            				if(!empty($is_variant))
					            				{
					            					$response['other_detail'][$abc]['product'][$super_key]['is_variant_avl'] = 1;
					            				}else
					            				{
					            					$response['other_detail'][$abc]['product'][$super_key]['is_variant_avl'] = 0;
					            				}
					            			}else
					            			{
						            			$cart_status = $this->Common->getData('cart' , 'product_quantity,product_id' , array('product_id' => $pd_status['product_id'] , 'user_id' => $tokenData->id));
					            				if(!empty($cart_status))
					            				{
					            					$response['other_detail'][$abc]['product'][$super_key]['isCart'] = 1;
					            					$response['other_detail'][$abc]['product'][$super_key]['cart'] = $cart_status;
					            				}else
					            				{
					            					$response['other_detail'][$abc]['product'][$super_key]['isCart'] = 0;
					            					$response['other_detail'][$abc]['product'][$super_key]['cart'] = array();
					            				}
					            				$is_variant = $this->check_if_variant_available($pd_status['product_id']);
					            				if(!empty($is_variant))
					            				{
					            					$response['other_detail'][$abc]['product'][$super_key]['is_variant_avl'] = 1;
					            				}else
					            				{
					            					$response['other_detail'][$abc]['product'][$super_key]['is_variant_avl'] = 0;
					            				}
					            			}
					            			$super_key++;
										}
			            				// $super_key++;
		            				}
		            				$abc++;

	            				}else
	            				{
	            					// $response['other_detail'][$abc]['is_product_avl'] = 0;
	            					// $response['other_detail'][$abc]['product'] = array();
	            				}
							}
						}else
						{
							// $response['other_detail'][$abc]['id'] = $cat['id'];
	            			// $response['other_detail'][$abc]['name'] = $cat['category_name'];
							if($is_veg == 1)
            				{
            					$veg_where = ' AND is_veg = "'.$is_veg.'"';
            				}else
            				{
            					$veg_where = '';
            				}
							$prod_query = "SELECT products.id AS product_id,products.product_name ,products.min_qty, products.product_position_order,products.short_desc AS product_description,products.price,products.offer_price,products.product_image,products.is_veg AS is_product_veg,products_offline.id AS off_id ,products_offline.rest_id , products_offline.category_id , products_offline.offline_tag,products_offline.offline_value,products_offline.offline_from,products_offline.offline_to FROM products LEFT OUTER JOIN products_offline ON products.id = products_offline.product_id WHERE products.product_status = 1 AND products.category_id = ".$cat['id']." AND products.restaurant_id = ".$restaurant_id.$veg_where." ORDER BY product_position_order ASC";

	            			$product_status = $this->Common->custom_query($prod_query,'get');
	            			// echo "Myquery".$this->db->last_query();
	            			if(count($product_status) > 0)
	            			{
	            				
	            				$response['other_detail'][$abc]['id'] = $cat['id'];
	            				$response['other_detail'][$abc]['name'] = $cat['category_name'];
		            			foreach ($product_status as $keyv=>$pd_status)
	            				{
	    							$pd_offline_tag = $pd_status['offline_tag'];
				            		$pd_offline_value = $pd_status['offline_value'];
				            		$pd_offline_from = $pd_status['offline_from'];
				            		$pd_offline_to = $pd_status['offline_to'];

				            		if ((time() >= $pd_offline_from) && (time() <= $pd_offline_to))
				            		{
		    							# "CURRENTLY DISABLE" so no need to pass its data
									}else
									{
										$response['other_detail'][$abc]['is_product_avl'] = 1;
				            			$response['other_detail'][$abc]['product'][$super_key] = $pd_status;
				            			if($tokenData === false || $tokenData == '' ) # Empty check because we made it as empty in 2123 line
				            			{
				            				$response['other_detail'][$abc]['product'][$super_key]['isCart'] = 0;
				            				$is_variant = $this->check_if_variant_available($pd_status['product_id']);
				            				if(!empty($is_variant))
				            				{
				            					$response['other_detail'][$abc]['product'][$super_key]['is_variant_avl'] = 1;
				            				}else
				            				{
				            					$response['other_detail'][$abc]['product'][$super_key]['is_variant_avl'] = 0;
				            				}
				            			}else
				            			{
					            			$cart_status = $this->Common->getData('cart' , 'product_quantity,product_id' , array('product_id' => $pd_status['product_id'] , 'user_id' => $tokenData->id));
				            				if(!empty($cart_status))
				            				{
				            					$response['other_detail'][$abc]['product'][$super_key]['isCart'] = 1;
				            					$response['other_detail'][$abc]['product'][$super_key]['cart'] = $cart_status;
				            				}else
				            				{
				            					$response['other_detail'][$abc]['product'][$super_key]['isCart'] = 0;
				            					$response['other_detail'][$abc]['product'][$super_key]['cart'] = array();
				            				}
				            				$is_variant = $this->check_if_variant_available($pd_status['product_id']);
				            				if(!empty($is_variant))
				            				{
				            					$response['other_detail'][$abc]['product'][$super_key]['is_variant_avl'] = 1;
				            				}else
				            				{
				            					$response['other_detail'][$abc]['product'][$super_key]['is_variant_avl'] = 0;
				            				}
				            			}
				            			$super_key++;
									}
		            				// $super_key++;
	            				}
	            				$abc++;
	            			}else
	            			{
	            				// $response['other_detail'][$abc]['is_product_avl'] = 0;
	            				// $response['other_detail'][$abc]['product'] = array();
	            			}
						}
	            	}
	            	# Check cart status for items and total price
	            	if($tokenData === false || $tokenData == '') // Either token not sent or is not valid
	            	{
	            		$response['item_total'] = 0;
	            		$response['items'] = 0;
	            	}else
	            	{
		            	$cart_response = $this->cart_calculation($tokenData);
	            		$response['item_total'] = $cart_response['item_total'];
	            		$response['items'] = $cart_response['items'];
	        			$response['cart_restaurant_id'] = $cart_response['restaurant_id'];
	            	}

	                $data['status']	= 200;
	                $data['message'] = $this->lang->line('success'); 
	                $data['data'] = $response;
            	}else
            	{
            		$response['other_detail'] = array();
            		$data['status']		=200;
	                $data['message']	=$this->lang->line('success');
	                $data['data']		=$response;
            	}
            }
    		$this->response($data, $data['status']);
    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # product_listing_by_restaurant end


    # add_remove_product_cart_post start
    # SCREEN_14_product_listing
    # This function does the following
    # 1. If product_qty == 0 then remove that product from cart(deleterow)
    # 2. If product_qty >= 1  Then checks whether given prodcut id exists in DB for this user. If yes then update else insert
    public function add_remove_product_cart_post()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	$prod_id = !empty($_POST['product_id'])?$this->db->escape_str($_POST['product_id']):'';
	    	$restaurant_id = !empty($_POST['restaurant_id'])?$this->db->escape_str($_POST['restaurant_id']):'';

	    	$is_variant_selected = !empty($_POST['is_variant_selected'])?$this->db->escape_str($_POST['is_variant_selected']):''; # is_variant_selected : 1 YES 2 NO
	    	
	    	# Variant info
	    	$variant_info = !empty($_POST['variant_info'])?$this->db->escape_str($_POST['variant_info']):'';
	    	# [{"product_id" : "2" , "variant_id" : "1" , "variant_type_id" : "1"} , {"product_id" : "2" , "variant_id" : "2" , "variant_type_id" : "1"}]

	    	#HERE
            $prod_quantity = $_POST['prod_quantity'];
            
            $page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
    		$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($prod_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('product_missing');
                $data['data']		=array();
            }else if($restaurant_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }else if($prod_quantity == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('quantity_missing');
                $data['data']		=array();
            }else if($is_variant_selected == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('is_variant_selected_missing');
                $data['data']		=array();
            }else
            {
            	if($prod_quantity == 0)
            	{
            		# Remove this data
            		$this->Common->deleteData('cart' , array('product_id' => $prod_id , 'user_id' => $tokenData->id));
            		$cart_response = $this->cart_calculation($tokenData);
            		$response_return['item_total'] = $cart_response['item_total'];
    				$response_return['items'] = $cart_response['items'];
    				$data['status']		=200;
	                $data['message']	=$this->lang->line('success');
	                $data['data']		=$response_return;
            	}else
            	{
            		if($is_variant_selected == 1 && $variant_info == '')
					{
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('variant_info_missing');
		                $data['data']		=array();
		            }else
		            {
	            		$check = $this->Common->getData('cart','*','user_id = "'.$tokenData->id.'"');
	            		if(!empty($check))
	            		{
	            			# We will simply update the qunatity sent from mobile side.
			            	# First check whether this entry exists in the cart and if yes then what is the restro id.
			            	# If that restro id is different from the given one then delete old entries and insert new entry
			            	$check_cart = $this->Common->getData('cart','*',array('user_id' => $tokenData->id , 'product_id' => $prod_id));
			            	if(!empty($check_cart))
			            	{
				            	$del_cart_id = $check_cart[0]['id'];
			            		# Check restaurant id here
			            		if($restaurant_id == $check_cart[0]['rest_id'])
			            		{
				            		# That means we just need to fire update query
				            		$this->Common->updateData('cart' , array('product_quantity' => $prod_quantity , 'updated_at' => time()),array('user_id'=> $tokenData->id , 'product_id' => $prod_id));

				            		if(!empty($variant_info))
				            		{
					            		# Now for variant first we will delete if any variant for this cart Id exists in cart_variant table and THEN we will insert the new one sent in variant_info variable
					            		$this->Common->deleteData('cart_variant','cart_id = "'.$del_cart_id.'"');

					            		# Insert to cart_variant
					            		$variant_info = stripslashes($variant_info); // added to strip slashes
						        		$someArray = json_decode($variant_info);
						        		foreach ($someArray as $detail) 
						            	{
						            		# [{"product_id" : "2" , "variant_id" : "1" , "variant_type_id" : "1"} , {"product_id" : "2" , "variant_id" : "2" , "variant_type_id" : "1"}]
						            		$insert_variant = [
												'cart_id' => $del_cart_id,
												'variant_id' => $detail->variant_id,
												'variant_type_id' => $detail->variant_type_id,
												'product_id' => $detail->product_id,
												'created_at' => time(),
												'updated_at' => time()
											];
											$return_id = $this->Common->insertData('cart_variant',$insert_variant);
						            	}
				            		}

				            		$cart_response = $this->cart_calculation($tokenData);
				            		$response_return['is_restro_change'] = 0;
			            		}else
			            		{
			            			# That means now user has selected product which does NOT belong to old restro so remove old cart data of this user and add new one
			            			# Remove this data
				            		$this->Common->deleteData('cart' , array('user_id' => $tokenData->id));
				            		# Now cart is empty so add new data
				            		# Need to fire Insert Query
				            		$insert_array = [
				            			'product_id' => $prod_id,
				            			'product_quantity ' => $prod_quantity,
				            			'user_id' => $tokenData->id,
				            			'rest_id' => $restaurant_id, # We will now add restaurant id also
				            			'created_at' => time(),
				            			'updated_at' => time()
				            		];
				            		$cart_id = $this->Common->insertData("cart",$insert_array);
				            		# Now this is fresh entry so no need to delete any old as we have already done this in above statement. Deleting from cart table results in delteing from cart_variant table
				            		# Insert to cart_variant
				            		if(!empty($variant_info))
				            		{
					            		$variant_info = stripslashes($variant_info); // added to strip slashes
						        		$someArray = json_decode($variant_info);
						        		foreach ($someArray as $detail) 
						            	{
						            		# [{"product_id" : "2" , "variant_id" : "1" , "variant_type_id" : "1"} , {"product_id" : "2" , "variant_id" : "2" , "variant_type_id" : "1"}]
						            		$insert_variant = [
												'cart_id' => $cart_id,
												'variant_id' => $detail->variant_id,
												'variant_type_id' => $detail->variant_type_id,
												'product_id' => $detail->product_id,
												'created_at' => time(),
												'updated_at' => time()
											];
											$return_id = $this->Common->insertData('cart_variant',$insert_variant);
						            	}
				            		}

				            		$cart_response = $this->cart_calculation($tokenData);
				            		$response_return['is_restro_change'] = 1;
			            		}
			            	}
			            	else
			            	{
			            		if($restaurant_id == $check[0]['rest_id'])
			            		{
				            		# That means we just need to fire update query
				            		$insert_array = [
				            			'product_id' => $prod_id,
				            			'product_quantity ' => $prod_quantity,
				            			'user_id' => $tokenData->id,
				            			'rest_id' => $restaurant_id, # We will now add restaurant id also
				            			'created_at' => time(),
				            			'updated_at' => time()
				            		];
				            		$cart_id = $this->Common->insertData("cart",$insert_array);

				            		# Now this is fresh entry so no need to delete any old
				            		# Insert to cart_variant
				            		if(!empty($variant_info))
				            		{
					            		$variant_info = stripslashes($variant_info); // added to strip slashes
						        		$someArray = json_decode($variant_info);
						        		foreach ($someArray as $detail) 
						            	{
						            		# [{"product_id" : "2" , "variant_id" : "1" , "variant_type_id" : "1"} , {"product_id" : "2" , "variant_id" : "2" , "variant_type_id" : "1"}]
						            		$insert_variant = [
												'cart_id' => $cart_id,
												'variant_id' => $detail->variant_id,
												'variant_type_id' => $detail->variant_type_id,
												'product_id' => $detail->product_id,
												'created_at' => time(),
												'updated_at' => time()
											];
											$return_id = $this->Common->insertData('cart_variant',$insert_variant);
						            	}
				            		}

				            		$cart_response = $this->cart_calculation($tokenData);
				            		$response_return['is_restro_change'] = 0;
			            		}
			            		else
			            		{
			            			# That means now user has selected product which does NOT belong to old restro so remove old cart data of this user and add new one
			            			# Remove this data
				            		$this->Common->deleteData('cart' , array('user_id' => $tokenData->id));
				            		# Now cart is empty so add new data
				            		# Need to fire Insert Query
				            		$insert_array = [
				            			'product_id' => $prod_id,
				            			'product_quantity ' => $prod_quantity,
				            			'user_id' => $tokenData->id,
				            			'rest_id' => $restaurant_id, # We will now add restaurant id also
				            			'created_at' => time(),
				            			'updated_at' => time()
				            		];
				            		$cart_id = $this->Common->insertData("cart",$insert_array);
				            		# Insert to cart_variant
				            		if(!empty($variant_info))
				            		{
					            		$variant_info = stripslashes($variant_info); // added to strip slashes
						        		$someArray = json_decode($variant_info);
						        		foreach ($someArray as $detail) 
						            	{
						            		# [{"product_id" : "2" , "variant_id" : "1" , "variant_type_id" : "1"} , {"product_id" : "2" , "variant_id" : "2" , "variant_type_id" : "1"}]
						            		$insert_variant = [
												'cart_id' => $cart_id,
												'variant_id' => $detail->variant_id,
												'variant_type_id' => $detail->variant_type_id,
												'product_id' => $detail->product_id,
												'created_at' => time(),
												'updated_at' => time()
											];
											$return_id = $this->Common->insertData('cart_variant',$insert_variant);
						            	}
				            		}
				            		$cart_response = $this->cart_calculation($tokenData);
				            		$response_return['is_restro_change'] = 1;
			            		}
			            	}
	            		}else
	            		{
	            			$insert_array = [
		            			'product_id' => $prod_id,
		            			'product_quantity ' => $prod_quantity,
		            			'user_id' => $tokenData->id,
		            			'rest_id' => $restaurant_id, # We will now add restaurant id also
		            			'created_at' => time(),
		            			'updated_at' => time()
		            		];
		            		$cart_id = $this->Common->insertData("cart",$insert_array);
		            		# Insert to cart_variant
		            		if(!empty($variant_info))
		            		{
			            		$variant_info = stripslashes($variant_info); // added to strip slashes
				        		$someArray = json_decode($variant_info);
				        		foreach ($someArray as $detail) 
				            	{
				            		# [{"product_id" : "2" , "variant_id" : "1" , "variant_type_id" : "1"} , {"product_id" : "2" , "variant_id" : "2" , "variant_type_id" : "1"}]
				            		$insert_variant = [
										'cart_id' => $cart_id,
										'variant_id' => $detail->variant_id,
										'variant_type_id' => $detail->variant_type_id,
										'product_id' => $detail->product_id,
										'created_at' => time(),
										'updated_at' => time()
									];
									$return_id = $this->Common->insertData('cart_variant',$insert_variant);
				            	}
		            		}

		            		$cart_response = $this->cart_calculation($tokenData);
		            		$response_return['is_restro_change'] = 0;
            			}
            			
            			$response_return['item_total'] = $cart_response['item_total'];
    					$response_return['items'] = $cart_response['items'];
    					$data['status']		=200;
		                $data['message']	=$this->lang->line('success');
		                $data['data']		=$response_return;
		            }
            	}
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # add_remove_product_cart_post end

    # more_info_recommended_products start
    # This api will be called when customer click on more info text from product_listing screen
    # We are just giving recommended restaurant list here because all other data we have already send in product_listing_by_restaurant_post API.

    public function more_info_recommended_products_get()
    {
    	try{
    		$tokenData = $this->verify_request();

    		$restaurant_id = !empty($_GET['restaurant_id'])?$this->db->escape_str($_GET['restaurant_id']):'';
    		$lat = !empty($_GET['lat'])?($_GET['lat']):'';
    		$lng = !empty($_GET['lng'])?($_GET['lng']):''; 

    		$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
			$date_timestamp = !empty($_GET['date_timestamp'])?$this->db->escape_str($_GET['date_timestamp']):'';

    		$is_valid = 0;
    		
    		if($date_timestamp == '')
        	{
        		$data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
        	}else if($restaurant_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }else if($tokenData === false)
			{
				$tokenData = ''; # Pass empty string if token is false
				# This fn can be called in both cases (Logged in and guest) so we will use token in fn_getrestaurant_data function to check wishlist status
			    # Secondly, If token is not present that it may be a guest user so in such case we need lat long
				if($lat == ''){
			        $data['status']		=201;
			        $data['message']	=$this->lang->line('latitude_missing');
			        $data['data']		=array();
				}else if($lng == ''){
			        $data['status']		=201;
			        $data['message']	=$this->lang->line('longitude_missing');
			        $data['data']		=array();
				}
				else
				{
					$is_valid = 1;
				}
			}else
			{
				$is_valid = 1;
				$latlong = $this->Common->getData('users','latitude,longitude','id = "'.$tokenData->id.'"');
				$lat = $latlong[0]['latitude'];
				$lng = $latlong[0]['longitude'];
			}
			if($is_valid == 1)
			{
				# For recommended restaurants we will use business type parameter.
	        	# Get business type of restaurant_id
	        	$business_type = $this->Common->getData('restaurants','id,business_type','id = "'.$restaurant_id.'"');
	        	$business_type = $business_type[0]['business_type'];

	        	$where = 'business_type = "'.$business_type.'" AND restaurants.id != "'.$restaurant_id.'" AND restaurants.rest_status = 1';
				$recom_restro = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC',$limit,$page);

				if(count($recom_restro) > 0)
				{
					# Calling common function to Get basic delivery time and basic preparation time from setting table
					$hours = $this->delivery_preparation_time();
					# Get all the basic restaurant from common funtion
					$recommended = $this->get_restaurant_data($recom_restro,$hours,$tokenData,$lat,$lng,$date_timestamp);
					
					$data['status']		=200;
		            $data['message']	=$this->lang->line('success');
		            $data['data']		=$recommended; 
					
				}else
	        	{
	        		# That is no items in user's wishlist
	        		$data['status']		=201;
		            $data['message']	=$this->lang->line('no_data_found');
		            $data['data']		=array(); 
	        	}
			}
	    	$this->response($data, $data['status']);

    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # more_info_recommended_products end

    # rate_dinein_post start
    # This function is used to give rating to the selected restaurant from restaurant_detail screen
    public function rate_dinein_post()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	$restaurant_id = !empty($_POST['restaurant_id'])?$this->db->escape_str($_POST['restaurant_id']):'';
	    	$rating = !empty($_POST['rating'])?$this->db->escape_str($_POST['rating']):'';
	    	$review = !empty($_POST['review'])?$this->db->escape_str($_POST['review']):'NA';
	    	$order_id = !empty($_POST['order_id'])?$this->db->escape_str($_POST['order_id']):'';
	    	# This will not be mendatory because if rating to an order is being given then only it will be used. BEcause this api is also being used to give rating to the selected restaurant from restaurant_detail screen

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($restaurant_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }else if($rating ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rating_missing');
                $data['data']		=array();
            }
            # REVIEW TEXT IS NOT MANDATORY
            // else if($review ==''){
            //     $data['status']		=201;
            //     $data['message']	=$this->lang->line('review_missing');
            //     $data['data']		=array();
            // }
            else
            {
            	if($order_id == '')
            	{
            		$order_id = 0;
            	}

            	# First we need to check whehtehr this user has already given rating to this restaurant then send 201 in such case
            	$is_already_rated = $this->Common->getData('ratings','id','from_user_id = "'.$tokenData->id.'" AND to_id = "'.$restaurant_id.'"');
            	if(count($is_already_rated) > 0)
            	{
            		$rest_name = $this->Common->getData('restaurants','rest_name','id = "'.$restaurant_id.'"');
            		$rest_name = $rest_name[0]['rest_name'];
            		$data['status']		=201;
		            $data['message']	="You have already rated ".$rest_name;
		            $data['data']		=array(); 
            	}
            	else
            	{
            		$insert_array = [
	            		'from_user_id' => $tokenData->id,
	            		'given_rating' => $rating,
	            		'review' => $review,
	            		'to_type' => 1, # 1 - For food to restaurant 2 - For food to Kerala Eats 3 - For delivery to restaurant
	            		'to_id' => $restaurant_id, 
	            		'order_id' => $order_id, 
	            		'created_at' => time(),
	            		'updated_at' => time(), 
	            	];
	            	$this->Common->insertData('ratings',$insert_array);
		            # When anytime rating is given then we have to udpate avg rating of restaurant
		            $query = "SELECT AVG(given_rating) AS avg_rating FROM `ratings` WHERE to_id = ".$restaurant_id;
		            $avg = $this->Common->custom_query($query,'get');
		            $avg = $avg[0]['avg_rating'];
		            $update_array = [
		            	'avg_rating' => number_format($avg)
		            ];
		            $this->Common->updateData('restaurants',$update_array,'id = "'.$restaurant_id.'"');

		            $data['status']		=200;
		            $data['message']	=$this->lang->line('rating_success');
		            $data['data']		=array(); 
            	}
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # rate_dinein_post end

    # report_problem_post start
    # This function is used to report problem to any restaurant
    public function report_problem_post()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	$restaurant_id = !empty($_POST['restaurant_id'])?$this->db->escape_str($_POST['restaurant_id']):'';
	    	$report_text = !empty($_POST['report_text'])?$this->db->escape_str($_POST['report_text']):'';

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($restaurant_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }else if($report_text ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('report_text_missing');
                $data['data']		=array();
            }else
            {
            	$insert_array = [
            		'to_rest_id' => $restaurant_id,
            		'from_id' => $tokenData->id,
            		'text' => $report_text,
            		'created_at' => time(),
            		'updated_at' => time()
            	];
            	$this->Common->insertData('reports',$insert_array);
	            $data['status']		=200;
	            $data['message']	=$this->lang->line('report_success');
	            $data['data']		=array(); 
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # report_problem_post end

    # manage_product_qty_checkout_screen_get start
    public function checkout_screen_get()
    {
    	try{
    		$tokenData = $this->verify_request();
    		$restaurant_id = !empty($_GET['restaurant_id'])?$this->db->escape_str($_GET['restaurant_id']):'';
    		$order_type = !empty($_GET['order_type'])?$this->db->escape_str($_GET['order_type']):'';
    		# 1 : Order now 2 : self pickup_time_from 3 : Order for later 4 : Dine In
    		$pickup_time = !empty($_GET['pickup_time'])?$this->db->escape_str($_GET['pickup_time']):'';
    		$latitude = !empty($_GET['latitude'])?$this->db->escape_str($_GET['latitude']):'';
    		$longitude = !empty($_GET['longitude'])?$this->db->escape_str($_GET['longitude']):'';
    		$pin_address = !empty($_GET['pin_address'])?$this->db->escape_str($_GET['pin_address']):'';
    		$delivery_name = !empty($_GET['delivery_name'])?$this->db->escape_str($_GET['delivery_name']):'';
    		$delivery_email = !empty($_GET['delivery_email'])?$this->db->escape_str($_GET['delivery_email']):'';
    		$delivery_mobile = !empty($_GET['delivery_mobile'])?$this->db->escape_str($_GET['delivery_mobile']):'';
    		$unit_number = !empty($_GET['unit_number'])?$this->db->escape_str(trim($_GET['unit_number'])):'';
	    	$street_address = !empty($_GET['street_address'])?$this->db->escape_str(trim($_GET['street_address'])):'';
	    	$postal_code = !empty($_GET['postal_code'])?$this->db->escape_str(trim($_GET['postal_code'])):'';
	    	$date_timestamp = !empty($_GET['date_timestamp'])?$this->db->escape_str(trim($_GET['date_timestamp'])):'';

    		if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($restaurant_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }else if($order_type ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('order_type_missing');
                $data['data']		=array();
            }else if($pickup_time ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('pickup_time_from_missing');
                $data['data']		=array();
            }else
            {
            	$rest_active_status = $this->Common->getData('restaurants','id,rest_status','id = "'.$restaurant_id.'"');
            	$rest_active_status = $rest_active_status[0]['rest_status'];

            	if($rest_active_status == 1)
            	{
            		$response = array();
	            	# First get the address from order table for the last ordered.
	            	# get address start
	            	
	            	# UPDATES
	            	# If lat long and pin address is empty then only we will find pin these details else not. Why because when user first time come to checkout screen then we can find these details as per the last order (and other like delivery address or user;s own address) But suppose user change the address from his addresses then again we need to know what the delivery address is now.
	            	if($latitude == '' || $longitude == '' || $pin_address == '')
	            	{
	            		# That means user is coming first time on checkout screen so we can find delivery address as per our logic
		            	$last_order = $this->Common->getData('orders','delivery_latitude,delivery_longitude,delivery_address,delivery_name,delivery_email,delivery_mobile,delivery_street_address,delivery_postal_code,delivery_unit_number','user_id = "'.$tokenData->id.'"','','','id','DESC',1,0);
		            	
		            	$user_basic_delivery_add = $this->Common->getData('users','fullname,email,mobile','id = "'.$tokenData->id.'"');
		            	if(count($last_order) > 0)
		            	{
		            		# That means this user has some records in order table so get delivery address related data from there
		            		$pin_address = $last_order[0]['delivery_address'];
		            		$delivery_name = $user_basic_delivery_add[0]['fullname'];
		            		$delivery_email = $user_basic_delivery_add[0]['email'];
		            		$delivery_mobile = $user_basic_delivery_add[0]['mobile'];
		            		$latitude = $last_order[0]['delivery_latitude'];
		            		$longitude = $last_order[0]['delivery_longitude'];
		            		$street_address = $last_order[0]['delivery_street_address'];
		            		$postal_code = $last_order[0]['delivery_postal_code'];
		            		$unit_number = $last_order[0]['delivery_unit_number'];
		            	}else
		            	{
		            		# That means user has not made any order yet. So get last entered record from delivery address table
		            		$delivery_add = $this->Common->getData('delivery_address','*','user_id = "'.$tokenData->id.'"','','','id','DESC',1,0);
		            		if(count($delivery_add) > 0)
		            		{
		            			$pin_address = $delivery_add[0]['pin_address'];
			            		$delivery_name = $user_basic_delivery_add[0]['fullname'];
			            		$delivery_email = $user_basic_delivery_add[0]['email'];
			            		$delivery_mobile = $user_basic_delivery_add[0]['mobile'];
			            		$latitude = $delivery_add[0]['del_latitude'];
			            		$longitude = $delivery_add[0]['del_longitude'];
			            		$street_address = $delivery_add[0]['street_address'];
			            		$postal_code = $delivery_add[0]['postal_code'];
			            		$unit_number = $delivery_add[0]['unit_number'];
		            		}else
		            		{
		            			# No address added by user. So get data from user table
		            			$user_delivery_add = $this->Common->getData('users','latitude,longitude,user_pin_address,fullname,email,mobile','id = "'.$tokenData->id.'"','','','id','DESC',1,0);
		            			$pin_address = $user_delivery_add[0]['user_pin_address'];
			            		$delivery_name = $user_basic_delivery_add[0]['fullname'];
			            		$delivery_email = $user_basic_delivery_add[0]['email'];
			            		$delivery_mobile = $user_basic_delivery_add[0]['mobile'];
			            		$latitude = $user_delivery_add[0]['latitude'];
			            		$longitude = $user_delivery_add[0]['longitude'];
			            		$street_address = $user_delivery_add[0]['user_street_address'];
			            		$postal_code = $user_delivery_add[0]['user_postal_code'];
			            		$unit_number = $user_delivery_add[0]['user_unit_number'];
		            		}
		            	}
	            	} 

	            	$response['delivery_address']['pin_address'] = $pin_address;
	            	$response['delivery_address']['delivery_name'] = $delivery_name;
	            	$response['delivery_address']['delivery_email'] = $delivery_email;
	            	$response['delivery_address']['delivery_mobile'] = $delivery_mobile;
	            	$response['delivery_address']['latitude'] = $latitude;
	            	$response['delivery_address']['longitude'] = $longitude;
	            	$response['delivery_address']['street_address'] = $street_address;
	            	$response['delivery_address']['postal_code'] = $postal_code;
	            	$response['delivery_address']['unit_number'] = $unit_number;

	            	$accept_data = $this->Common->getData('restaurants','is_order_now_accept,is_self_pickup_accept,is_order_later_accept,is_dinein_accept','id = "'.$restaurant_id.'"');
	            	$response['is_order_now_accept'] = $accept_data[0]['is_order_now_accept'];
	            	$response['is_self_pickup_accept'] = $accept_data[0]['is_self_pickup_accept'];
	            	$response['is_order_later_accept'] = $accept_data[0]['is_order_later_accept'];
	            	$response['is_dinein_accept'] = $accept_data[0]['is_dinein_accept'];
	            	# get address End

	            	# Now it is required to send restro details also on checkout screen

	            	# Now get the number of items and item total price as per user cart
	            	$cart_response = $this->cart_calculation($tokenData);
	        		$response['items'] = $cart_response['items'];
	            	
	            	# Get cart items start

	        		# Now get complete details of the product available in cart
	        		# DB_Product_Status 1 - Enable 2 - Disable 3 - Deleted
	        		$cart_data = $this->Common->getData('cart','products.*,cart.*,cart.id AS cart_id,products.id AS product_id','cart.user_id = "'.$tokenData->id.'" AND products.product_status = 1',array('products'),array('cart.product_id = products.id'));
	        		$response['cart_products'] = $cart_data;

	        		# Now we will check whehter any variant for cart product available
	        		if(!empty($response['cart_products']))
	        		{
	        			foreach ($response['cart_products'] as $key => $value) 
	        			{
			        		$cart_var_query = "SELECT `cart`.`id` AS `cart_tbl_id`,variants.variant_name,variant_types.variant_type_name,variant_types_for_products.variant_type_price,cart_variant.variant_id,cart_variant.variant_type_id FROM `cart` INNER JOIN `cart_variant` ON `cart`.`id` = `cart_variant`.`cart_id` INNER JOIN `variants` ON `variants`.`variant_id` = `cart_variant`.`variant_id` INNER JOIN `variant_types` ON `variant_types`.`variant_type_id` = `cart_variant`.`variant_type_id` INNER JOIN `variant_types_for_products` ON `variant_types_for_products`.`variant_type_id` = `variant_types`.`variant_type_id` WHERE `cart`.`user_id` = ".$tokenData->id." AND `cart`.`product_id` = ".$value['product_id']." AND `variants`.`variant_status` = 1 AND variant_types_for_products.product_id = ".$value['product_id'];
			        		$cart_var_data = $this->Common->custom_query($cart_var_query,'get');
			        		if(count($cart_var_data) > 0)
			        		{
			        			$response['cart_products'][$key]['variants'] = $cart_var_data;
			        		}else
			        		{
			        			$response['cart_products'][$key]['variants'] = array();
			        		}
	        			}
	        		}

	        		if(!empty($cart_data))
	        		{
		        		# Now it may happen that when customer added a product to cart that time it was enabled and was not offline but when customer wishes to checkout the same then it may go offline for that period so we can send offline status for the cart product so mobile team will check whether product is currently available or not.

		        		# We will check only products (NOT CATEGORY) because if category goes offline then we have already made entries for products_offline table for that category's products
						$item_total = 0;
		        		foreach ($cart_data as $key => $value) 
		        		{
		        			$response['cart_products'][$key]['offline_status'] = $this->Common->getData('products_offline','*','product_id = "'.$value['product_id'].'"');
		        			$offline_status = $this->Common->getData('products_offline','*','product_id = "'.$value['product_id'].'"');
		        			if(count($offline_status) > 0)
		        			{
			        			if ((time() >= $offline_status[0]['offline_from']) && (time() <= $offline_status[0]['offline_to']))
			        			{
			        				# "CURRENTLY DISABLED"
			        				$response['cart_products'][$key]['is_offline'] = 1; # Yes : it is offline (Not available now)
			        			}else
			        			{
			        				$response['cart_products'][$key]['is_offline'] = 0; # No : It is available
			        				if($value['offer_price'] != 0)
			        				{
			        					$item_total += $value['offer_price'] * $value['product_quantity'];
			        				}else
			        				{
			        					$item_total += $value['price'] * $value['product_quantity'];
			        				}
			        			}
		        			}else
		        			{
		        				$response['cart_products'][$key]['is_offline'] = 0; # No : It is available
		        				if($value['offer_price'] != 0)
		        				{
		        					$item_total += $value['offer_price'] * $value['product_quantity'];
		        				}else
		        				{
		        					$item_total += $value['price'] * $value['product_quantity'];
		        				}
		        			}
		        			# Create category id array for checking promotion
		        			$category_ids[] = $value['category_id'];
		        			$product_ids[] = $value['product_id'];
		        		}
		        		$cart_var_data = $this->Common->getData('cart','cart.id AS cart_tbl_id ,cart.product_quantity,products.price,variant_types_for_products.variant_type_price','cart.user_id = "'.$tokenData->id.'" AND cart.product_id = variant_types_for_products.product_id',array('products','cart_variant','variant_types_for_products'),array('products.id = cart.product_id' , 'cart.id = cart_variant.cart_id','variant_types_for_products.variant_type_id = cart_variant.variant_type_id'));

				    	foreach ($cart_var_data as $key => $value) 
				    	{
				    		
				    		$item_total += $value['variant_type_price'] * $value['product_quantity'];
				    	}
		        		$response['item_total'] = $item_total;
		        		$response['item_total'] = number_format($item_total,2, '.', '');
		            	# Get cart items end

		        		# Check for Delivery Charge start
		        		# Here we are currently doing when delivery is handled by the restaurant. 
		        		# Table: restaurants - Column : delivery_handled_by (DB_del_handel_status 1 - restaurant 2 - By Kerala Eats ie lalamove) and column per_km_charge value
		        		# First we have to pass lat long of restaturant and lat long of the destination to calculate distance between two lat longs
		        		# Get admin id of the restaurant
		        		$some_checks = $this->Common->getData('restaurants','admin_id,delivery_handled_by,per_km_charge,business_type AS business_category,food_type','id = "'.$restaurant_id.'"');

		        		$rest_lat_lng = $this->Common->getData('users','latitude,longitude','id = "'.$some_checks[0]['admin_id'].'"');
		        		
		        		$rest_lat = $rest_lat_lng[0]['latitude'];
		        		$rest_lng = $rest_lat_lng[0]['longitude'];
		        		
		        		$destination_lat = $latitude;
		        		$destination_lng = $longitude;

						# ----------------------------- LALAMOVE HANDLING PART --------------------------------------- #

		            	# LALAMOVE will be included only if $order_type is NOT selfpickup and if delivery is handeled by Kerala eats
		            	# Here we need to check for schedleAt. omit this if you are placing an immediate order
		            	# 1 : Order now 2 : self pickup 3 : Order for later 4 : Dine In
		            	// echo "dfhdff";
		            	// echo $order_type;
		            	// echo "<br>";
		            	// echo $some_checks[0]['delivery_handled_by'];
		            	// if($order_type != 2 && $some_checks[0]['delivery_handled_by'] == 2) # That is when it is not a Self pickup order and delivery need to be handeled by Kerala eats
		            	if($order_type != 2) # We are removing the delivery_handled_by check because client wants that whosoever handle the delivey ; charges should come from lalamove and use of selected delivery_handled_by should be on app side only. "If the delivery is being handled by restaurant. The delivery charge calculations can be remained same. But only at the app side, we need to display it."
		            	{
		            		# Get requesterContact information
		            		$lalamove_rest_details = $this->Common->getData('restaurants','restaurants.rest_pin_address,restaurants.rest_unit_number,restaurants.rest_postal_code,restaurants.rest_name AS rest_name,users.mobile,users.latitude,users.longitude','restaurants.id = "'.$restaurant_id.'"',array('users'),array('users.id = restaurants.admin_id'));
		            		$req_phone = $lalamove_rest_details[0]['mobile'];
		            		$req_phone = '+65'.$req_phone;
		            		$to_stop_phone = $this->Common->getData('users','mobile','id = "'.$tokenData->id.'"');
		            		$to_stop_phone = $to_stop_phone[0]['mobile'];
		            		$to_stop_phone = '+65'.$to_stop_phone;
							# requesterContact : Contact person at pick up point
							# stops : The index of waypoint in stops this information associates with, has to be >= 1, since the first stop's Delivery Info is tided to requesterContact
		            		if($order_type == 1) # Order now so dont pass scheduleAt
		            		{
		            			$body = array(
								  // "scheduleAt" => gmdate('Y-m-d\TH:i:s\Z', time() + 60 * 30), // ISOString with the format YYYY-MM-ddTHH:mm:ss.000Z at UTC time
								  "serviceType" => "MOTORCYCLE", // string to pick the available service type
								  "specialRequests" => array(), // array of strings available for the service type
								  "requesterContact" => array(
								    "phone" => $req_phone, // Phone number format must follow the format of your country
								    "name" => $lalamove_rest_details[0]['rest_name'],
								  ),  
								  "stops" => array(
								    array(
								      "location" => array("lat" => $lalamove_rest_details[0]['latitude'], "lng" => $lalamove_rest_details[0]['longitude']),
								      "addresses" => array(
								        "en_SG" => array(
								          "displayString" => $lalamove_rest_details[0]['rest_pin_address'].", ".$lalamove_rest_details[0]['rest_unit_number'].", ".$lalamove_rest_details[0]['rest_postal_code'],
								          "country" => "SG" // Country code must follow the country you are at
								        )   
								      )   
								    ),  
								    array(
								      "location" => array("lat" => $latitude, "lng" => $longitude),
								      "addresses" => array(
								        "en_SG" => array(
								          "displayString" => $pin_address.", ".$unit_number.", ".$postal_code,
								          "country" => "SG" // Country code must follow the country you are at
								        )   
								      )   
								    )   
								  ),  
								  "deliveries" => array(
								    array(
								      "toStop" => 1,
								      "toContact" => array(
								        "phone" => $to_stop_phone, // Phone number format must follow the format of your country
								        "name" => $tokenData->name,
								      ),
								      "remarks" => "1. Kerala Eats Food Order ID: [KEXXXX] \n2. Customer Name: ".$tokenData->name." \n3. Support Number: 90298605\n4. Tips pay by Kerala Eats"
								    )   
								  )   
								);
								// echo "<pre> 3020";
		            		}else # 3 : Order for later
		            		{
		            			// $pickup_time = $pickup_time - 27900; # 8 hours and 15 minutes minus
		            			// $start = new DateTime(date('r', $pickup_time));
		            			// $start = $start->format('Y-m-d\TH:i:s\Z');

		            			$less_15_mint = $pickup_time-900;//we need to less 15 mint
					            $start = new DateTime(date('r', $less_15_mint));
					            $start = $start->format('Y-m-d\TH:i:s\Z');
					            $final_pickup_time  = $start;

			            		$body = array(
								  "scheduleAt" => $final_pickup_time, // ISOString with the format YYYY-MM-ddTHH:mm:ss.000Z at UTC time ## We will make order 15 minutes before for lalamove
								  "serviceType" => "MOTORCYCLE", // string to pick the available service type
								  "specialRequests" => array(), // array of strings available for the service type
								  "requesterContact" => array(
								    "phone" => $req_phone, // Phone number format must follow the format of your country
								    "name" => $lalamove_rest_details[0]['rest_name'],
								  ),  
								  "stops" => array(
								    array(
								      "location" => array("lat" => $lalamove_rest_details[0]['latitude'], "lng" => $lalamove_rest_details[0]['longitude']),
								      "addresses" => array(
								        "en_SG" => array(
								          "displayString" => $lalamove_rest_details[0]['rest_pin_address'].", ".$lalamove_rest_details[0]['rest_unit_number'].", ".$lalamove_rest_details[0]['rest_postal_code'],
								          "country" => "SG" // Country code must follow the country you are at
								        )   
								      )   
								    ),  
								    array(
								      "location" => array("lat" => $latitude, "lng" => $longitude),
								      "addresses" => array(
								        "en_SG" => array(
								          "displayString" => $pin_address.", ".$unit_number.", ".$postal_code,
								          "country" => "SG" // Country code must follow the country you are at
								        )   
								      )   
								    )   
								  ),  
								  "deliveries" => array(
								    array(
								      "toStop" => 1,
								      "toContact" => array(
								        "phone" => $to_stop_phone, // Phone number format must follow the format of your country
								        "name" => $tokenData->name,
								      ),  
								      "remarks" => "1. Kerala Eats Food Order ID: [KEXXXX] \n2. Customer Name: ".$tokenData->name." \n3. Support Number: 90298605\n4. Tips pay by Kerala Eats"
								    )   
								  )   
								);
		            		}

		            		// echo "<pre> 3058";
		            		// echo json_encode($body);
		            		// print_r($body);
		            		// die;

			            	# Now we need to get some information regarding lalamove ordering
			            	$lalamove_order_response = $this->lalamove_quotation_generate($body);
			            	// die;
			            	// echo "<pre> FINAL ";
			            	// print_r($lalamove_order_response);
			            	// die;
			            	// Sample response : array('lalamove_order_id' => $lalamove_order_id , 'lalamove_order_amount' => $amount,'failed_reason' => '' , 'lalamove_track_link' => $track_link);
			            	/*Array
							(
							    [lalamove_order_id] => 172800508026
							    [lalamove_order_amount] => 11.80
							    [failed_reason] => 
							    [lalamove_track_link] => https://share.sandbox.lalamove.com?SG100210602153645628310010070780999&lang=en_SG&version=2&sign=471d582a8cbc30d6172d546bb67eab8d
							)*/
			            	if($lalamove_order_response['failed_reason'] == '')
			            	{
			            		# That means order placed successfully as it has nothing in failed
				            	/*$track_link = $lalamove_order_response['lalamove_track_link'];
								$lalamove_order_id = $lalamove_order_response['lalamove_order_id'];
			            		$lalamove_order_failed_reason = '';
			            		$response['lalamove_order_reference_id'] = $lalamove_order_id;
			            		$response['lalamove_order_failed_reason'] = $lalamove_order_failed_reason;
			            		$response['lalamove_order_status'] = $lalamove_order_status;
			            		$response['lalamove_order_amount'] = $lalamove_order_amount;
			            		$response['lalamove_track_link'] = $track_link;*/
								$lalamove_order_status = 1; #  Success
								$lalamove_order_amount = $lalamove_order_response['lalamove_order_amount'];
			            		$track_link = '';
								$lalamove_order_id = '';
								$lalamove_order_failed_reason = '';
								$response['lalamove_order_reference_id'] = $lalamove_order_id;
								$response['lalamove_order_failed_reason'] = $lalamove_order_failed_reason;
								$response['lalamove_order_status'] = $lalamove_order_status;
			            		$response['lalamove_order_amount'] = $lalamove_order_amount;
			            		$response['lalamove_track_link'] = '';
			            	}else
			            	{
			            		$lalamove_order_status = 2; #  Fail
			            		$lalamove_order_id = '';
			            		$lalamove_order_failed_reason = $lalamove_order_response['failed_reason'];
			            		$response['lalamove_order_reference_id'] = $lalamove_order_id;
			            		$response['lalamove_order_failed_reason'] = $lalamove_order_failed_reason;
			            		$response['lalamove_order_status'] = $lalamove_order_status;
			            		$response['lalamove_order_amount'] = '';
			            		$response['lalamove_track_link'] = '';
			            		# IN CASE OF FAIL WE ARE SENDING A STATIC VALUE
			            		$response['lalamove_order_amount'] = '15.00';
			            	}
		            	}else
		            	{
		            		$track_link = '';
							$lalamove_order_id = '';
							$lalamove_order_status = 3; # Not for lalamove
							$lalamove_order_failed_reason = '';
							$response['lalamove_order_reference_id'] = $lalamove_order_id;
							$response['lalamove_order_failed_reason'] = $lalamove_order_failed_reason;
							$response['lalamove_order_status'] = $lalamove_order_status;
		            		$response['lalamove_order_amount'] = '';
		            		$response['lalamove_track_link'] = '';
		            	}
		        		$response['distance_in_km'] = $this->calculate_distance_between_latlong($latitude,$rest_lat,$longitude,$rest_lng);

		        		if($some_checks[0]['delivery_handled_by'] == 1) # DB_del_handel_status 1 - restaurant 2 - By Kerala Eats ie lalamove
		        		{
			        		$response['per_km_charge'] = $some_checks[0]['per_km_charge'];
		        		}else
		        		{
							$response['per_km_charge'] = PER_KM_CHARGE; // STATIC_CODE_NEED_TO_CHANGE
		        		}
		        		$response['delivery_handled_by'] = $some_checks[0]['delivery_handled_by'];

		        		# Check wallet amount start
		        		# This get_wallet_balance API mysql query will get data only for those values who are valid.
		        		$wallet = $this->Common->getData('wallet','*','user_id = "'.$tokenData->id.'"');
		        		// if(count($wallet) > 0 && $wallet[0]['wallet_balance'] != null)
		        		if(count($wallet) > 0)
		        		// if(count($wallet) > 0)
		        		{
		        			$wallet_balance = $this->get_wallet_balance($tokenData->id);
		        			if(count($wallet_balance) > 0 && $wallet_balance[0]['wallet_balance'] != null)
		        			{
			        			// $wallet_balance = (int)$wallet_balance[0]['wallet_balance'];
			        			// echo "<hr>".$this->db->last_query();
			        			// echo "<pre>";
			        			// print_r($wallet_balance);
			        			$wallet_balance = str_replace(",", "", $wallet_balance[0]['wallet_balance']);
			        			$response['wallet_balance'] = number_format($wallet_balance,2, '.', '');
		        			}else
		        			{
		        				$response['wallet_balance'] = 0.00;		
		        			}
		        		}else
		        		{
		        			$response['wallet_balance'] = 0.00;
		        		}
		        		$response['business_category'] = $some_checks[0]['business_category'];
		        		$response['food_type'] = $some_checks[0]['food_type'];
		        		# Check wallet amount end

		        		# Check for discount applied start
		        		/* AS per client requirement 
			        		1. Platform Promotion & Promotions based on Services (Pick-up/self Collect) Global
							2. Restaurant Promo (Overwrite all the other promotions available below within a restaurant) 
							3. Category Promo (Overwrite all the other promotions available below within a restaurant) 
							4. Product Promo
		        		*/
						# Check whether any global promotion is available and set by kerala eats?
						# Db_added_by_status : 1 : added by master admin 2 : By merchant

						# DB_promo_level : 1:Delivery 2:Restaurant 3:Product 4:Category 5:Global

						# 1. Check any GLOBAL DISCOUNT set by Kerala Eats
						# We have two global level promo (One for overall and other for product wise promotion)
						$check_global_promo = 1;
						# Setting all to 0
						$check_all_restro_promo = 0;
						# promotion_mode_status 1- Promo Code, 2- Discount, 3 - Referral
						$check_restaurant_promo = 0;
						$check_category_promo = 0;
						$check_product_promo = 0;
						$promo_response = array();

						$global_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'level_id' => 5 , 'promotion_mode_status' => 2));
						// echo $this->db->last_query();
						// echo "<pre>global_level_promo";
						// print_r($global_level_promo);
						if(count($global_level_promo) > 0)
						{
							$promo_response = array();
							foreach ($global_level_promo as $key => $global_level) 
							{
								$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $global_level , $tokenData,$pickup_time);
							}
							// echo "<pre>promo_response";
							// print_r($promo_response);
							$current_item_total = $response['item_total'];
							if(count($promo_response) > 0  ) # Multiple promo available as per multiple categories
							{
								# That mens we have promotion on two categories so we need to find which one has high value of discount
								$check_high_promo = array();
								foreach ($promo_response as $key => $selfp_value)
								{
									if(!empty($selfp_value))
									{
										if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
										{
											$disc_val = $selfp_value['discount_value'];
											$disc_val = number_format($disc_val,2, '.', '');
											$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
										}else
										{
											$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
											$disc_val = number_format($disc_val,2, '.', '');
											$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
										}
									}
								}
								// echo "qqqqqq";
								// print_r($check_high_promo);
								if(isset($check_high_promo['high_promo']))
								{
									$max_dis = 0;
									foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
									{
										if($cvalue['disc_val'] > $max_dis)
										{
											$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
											$max_dis = $cvalue['disc_val'];
										}
									}
									$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 2));
									$response['promocode_details'] = $promocode_details;
								}else
								{
									$check_all_restro_promo = 1;		
								}
								// echo "WWWWWW";
								// print_r($response['promocode_details']);die;
							}else
							{
								$check_all_restro_promo = 1;		
							}
						}else
						{
							$check_all_restro_promo = 1;
						}

						if($check_all_restro_promo == 1)
						{
							$all_restro_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => 0 , 'is_auto_apply' => 1 , 'level_id' => 2, 'promotion_mode_status' => 1,'if_promo_for_all_rest' => 1));
							// echo $this->db->last_query();
							// echo "<pre>HELLOo";
							// print_r($all_restro_promo);
							if(count($all_restro_promo) > 0)
							{
								$promo_response = array();
								foreach ($all_restro_promo as $key => $all_restro) 
								{
									$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $all_restro , $tokenData,$pickup_time);
								}
								// echo "<pre>POPPPPO";
								// print_r($promo_response);
								$current_item_total = $response['item_total'];
								if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
								{
									# That mens we have promotion on two categories so we need to find which one has high value of discount
									$check_high_promo = array();
									foreach ($promo_response as $key => $selfp_value)
									{
										if(!empty($selfp_value))
										{
											if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
											{
												$disc_val = $selfp_value['discount_value'];
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}else
											{
												$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}
										}
									}
									// echo "<pre>check_high_promo";
									// print_r($check_high_promo);
									if(isset($check_high_promo['high_promo']))
									{
										$max_dis = 0;
										foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
										{
											if($cvalue['disc_val'] > $max_dis)
											{
												$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
												$max_dis = $cvalue['disc_val'];
											}
										}
										$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										$response['promocode_details'] = $promocode_details;
									}else
									{
										$check_restaurant_promo = 1;		
									}
								}else
								{
									// echo "YHAAYAKYA";
									$check_restaurant_promo = 1;
								}
							}else
							{
								// echo "ORCAMEHERE";
								$check_restaurant_promo = 1;
							}
						}
						
						// die;

						if($check_restaurant_promo == 1)
						{
							$rest_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'level_id' => 2, 'promotion_mode_status' => 1));
							if(count($rest_level_promo) > 0)
							{
								$promo_response = array();
								foreach ($rest_level_promo as $key => $rest_level) 
								{
									$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $rest_level , $tokenData,$pickup_time);
								}
								$current_item_total = $response['item_total'];
								if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
								{
									# That mens we have promotion on two categories so we need to find which one has high value of discount
									$check_high_promo = array();
									foreach ($promo_response as $key => $selfp_value)
									{
										if(!empty($selfp_value))
										{
											if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
											{
												$disc_val = $selfp_value['discount_value'];
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}else
											{
												$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}
										}
									}
									if(isset($check_high_promo['high_promo']))
									{
										$max_dis = 0;
										foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
										{
											if($cvalue['disc_val'] > $max_dis)
											{
												$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
												$max_dis = $cvalue['disc_val'];
											}
										}
										$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										$response['promocode_details'] = $promocode_details;
									}else
									{
										$check_category_promo = 1;		
									}
								}else
								{
									$check_category_promo = 1;		
								}
							}else
							{
								$check_category_promo = 1;
							}
						}
						
						if($check_category_promo == 1)
						{
							# 3. CATEGORY LEVEL
							# Check any promotion on category is available?
							# level id for category is 4
							# It may possible that when product added to cart of any category then that category was enabled but while doing checkout it goes offline
							# It may also happen that there are products of different categories and both are having promotions so we need to check which is most applicable
							# It may possible that two product may be of same category so this array will contain two same value so take unique value array
							
							$category_ids = array_unique($category_ids);
							foreach($category_ids as $key => $value)
				    		{
								$category_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'promotion_mode_status' => 1 ,'is_auto_apply' => 1 , 'level_id' => 4,'applied_on_id = "'.$value.'"'));
								if(count($category_level_promo) > 0)
								{
									foreach ($category_level_promo as $index => $cat_promo) 
				    				{
				    					$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $cat_promo , $tokenData,$pickup_time);
				    				}
			    					$current_item_total = $response['item_total'];
			    					if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
			    					{
			    						# That mens we have promotion on two categories so we need to find which one has high value of discount
										$check_high_promo = array();
										foreach ($promo_response as $key => $catp_value)
										{
											if(!empty($catp_value))
											{
												if($catp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
												{
													// $disc_val = $current_item_total - $catp_value['discount_value'];
													$disc_val = $catp_value['discount_value'];
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $catp_value['id'] , 'disc_val' => $disc_val);
												}else
												{
													$disc_val = ($current_item_total * $catp_value['discount_value']) / 100;
													// $disc_val = $current_item_total - $disc_val;
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $catp_value['id'] , 'disc_val' => $disc_val);
												}
											}
										}
										if(isset($check_high_promo['high_promo']))
										{
											$max_dis = 0;
											foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
											{
												if($cvalue['disc_val'] > $max_dis)
												{
													$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
													$max_dis = $cvalue['disc_val'];
												}
											}
											$response['promocode_details'] = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										}else
										{
											$check_product_promo = 1;		
										}
			    					}else
			    					{
			    						$check_product_promo = 1;
			    					}
								}else
								{
									$check_product_promo = 1;
								}
				    		}
						}

						if($check_product_promo == 1)
						{
							# 4. PRODUCT LEVEL
			    			# Check for OFFER ON PRODUCTS
			    			$product_ids = array_unique($product_ids);
							foreach($product_ids as $key => $value)
				    		{
								$product_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'promotion_mode_status' => 1 ,'level_id' => 3,'applied_on_id' => $value));
								// echo $this->db->last_query();
								// echo "product_level_promo<pre>";
								// print_r($product_level_promo);
								if(count($product_level_promo) > 0)
								{
									foreach ($product_level_promo as $index => $prod_promo) 
				    				{
				    					$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $prod_promo , $tokenData,$pickup_time);
				    				}
				    				// echo "promo response<pre>";
				    				// print_r($promo_response);
				    				if(count($promo_response) > 0)
				    				{
				    					$check_high_promo_prod = array();
				    					foreach ($promo_response as $keyv => $catprd_value)
										{
											if(!empty($catprd_value))
											{
												if($catprd_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
												{
													// $disc_val = $current_item_total_prd - $catprd_value['discount_value'];
													$disc_val = $catprd_value['discount_value'];
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo_prod['high_promo'][$keyv] = array('promo_id' => $catprd_value['id'] , 'disc_val' => $disc_val);
												}else
												{
													$disc_val = ($current_item_total_prd * $catprd_value['discount_value']) / 100;
													// $disc_val = $current_item_total_prd - $disc_val;
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo_prod['high_promo'][$keyv] = array('promo_id' => $catprd_value['id'] , 'disc_val' => $disc_val);
												}
											}
										}
										// echo "<pre>check_high_promo_prod";
										// print_r($check_high_promo_prod);
										if(isset($check_high_promo_prod['high_promo']))
										{
											$max_dis = 0;
											foreach ($check_high_promo_prod['high_promo'] as $keyv => $cvalue) 
											{
												if($cvalue['disc_val'] > $max_dis)
												{
													$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
													$max_dis = $cvalue['disc_val'];
												}
											}
											$response['promocode_details'] = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										}else
										{
											$response['promocode_details'] = array();		
										}
				    				}else
				    				{
				    					$response['promocode_details'] = array();
				    				}
								}else
			    				{
			    					$response['promocode_details'] = array();
			    				}
				    		}
						}

						# Now check for promotion that any promotion for delivery charge to this restaturant is available?
		        		# If order_type is self pickup then we have separate promo type for self pickup users
		        		if($order_type == 2) # Self pickup : So check any promo for selfpickup user available? 
		        		{
		        			$check_merchant_self_pickup_promo = 0;	
		        			$super_admin_self_pickup_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => 0 , 'is_auto_apply' => 1 , 'level_id' => 8 , 'promotion_mode_status' => 1));
		        			# restaurant_id = 0 Means it is added by Super Admin for all restaurants
		        			# It may possible that promo on self pickup can either be added by super admin or by merchant
		        			if(count($super_admin_self_pickup_promo) > 0)
		        			{
		        				$promo_response = array();
		        				foreach ($super_admin_self_pickup_promo as $key => $self_pickup) 
		        				{
		        					$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $self_pickup , $tokenData,$pickup_time);
		        				}
								$current_item_total = $response['item_total'];
		    					// if(count($promo_response) > 0 && !empty($promo_response[0])) # Multiple promo available as per multiple categories
		    					if(count($promo_response) > 0) # Multiple promo available as per multiple categories
		    					{
		    						# That mens we have promotion on so we need to find which one has high value of discount
									$check_high_promo = array();
									foreach ($promo_response as $key => $selfp_value)
									{
										if(!empty($selfp_value))
										{
											if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
											{
												// echo "testing";
												$disc_val = $selfp_value['discount_value'];
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}else
											{
												// echo "debug";
												$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}
										}
									}
									if(isset($check_high_promo['high_promo']))
									{
										$max_dis = 0;
										foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
										{
											if($cvalue['disc_val'] > $max_dis)
											{
												$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
												$max_dis = $cvalue['disc_val'];
											}
										}
										$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										$response['delivery_charge_promotion'] = $promocode_details[0];
									}else
									{
										$check_merchant_self_pickup_promo = 1;		
									}
		    					}else
		    					{
		    						$check_merchant_self_pickup_promo = 1;		
		    					}
		        			}else
		        			{
								$check_merchant_self_pickup_promo = 1;
		        			}
		        			
		        			if($check_merchant_self_pickup_promo == 1)
		        			{
		        				$merchant_self_pickup_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'level_id' => 8 , 'promotion_mode_status' => 1));
		        				if(count($merchant_self_pickup_promo) > 0)
		        				{
		        					$promo_response = array();
		        					foreach ($merchant_self_pickup_promo as $key => $mer_self_pickup) 
			        				{
										$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $mer_self_pickup , $tokenData,$pickup_time);
			        				}
									
									$current_item_total = $response['item_total'];
			    					if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
			    					{
			    						# That mens we have promotion on two categories so we need to find which one has high value of discount
										$check_high_promo = array();
										foreach ($promo_response as $key => $selfp_value)
										{
											if(!empty($selfp_value))
											{
												if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
												{
													$disc_val = $selfp_value['discount_value'];
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
												}else
												{
													$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
												}
											}
										}
										if(isset($check_high_promo['high_promo']))
										{
											$max_dis = 0;
											foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
											{
												if($cvalue['disc_val'] > $max_dis)
												{
													$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
													$max_dis = $cvalue['disc_val'];
												}
											}
											$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
											$response['delivery_charge_promotion'] = $promocode_details[0];
										}else
										{
											$x = new stdClass();
											$response['delivery_charge_promotion'] = $x;
										}
			    					}
		        				}else
		        				{
									$x = new stdClass();
									$response['delivery_charge_promotion'] = $x;
		        				}
		        			}

		        			# PLACE HERE
			            	$restaurant = $this->Common->getData('restaurants','restaurants.*,restaurants.id AS restaurant_id,users.latitude,users.longitude','restaurants.id = "'.$restaurant_id.'"',array('users'),array('users.id = restaurants.admin_id'));
			            	$response['restaurant_detail'] = $restaurant[0];
		        		}else
		        		{
		        			# ABOVE WE HAVE FOUDN THE PROMOTIONS. NOW HERE WE NEED TO CHECK WHETHER ANY PROMOTION APPLICABLE AND IS YES THEN DEDUCT THE VALUE FROM ITEM TOTAL
		        			if(count($response['promocode_details']) > 0)
		        			{
		        				# THAT IS SOME PROMOTIONS WE GOT SO DEDUCT IT
		        				$actual_item_total = $response['item_total'];
		        				$found_promo = $response['promocode_details'];
		        				// echo "<pre>";
		        				// print_r($found_promo);die;
		        				if($found_promo[0]['promo_type'] == 2) # PERCENT
		        				{
		        					# Get discounted value
		        					$dicounted_value = ($actual_item_total * $found_promo[0]['discount_value']) / 100;
		        				}else
		        				{
		        					$dicounted_value = $found_promo[0]['discount_value'];
		        				}
		        				# Now compare it with max discount
		        				if(($found_promo[0]['max_discount'] > 0) && ($dicounted_value > $found_promo[0]['max_discount']))
	        					{
	        						$final_value_to_be_deducted = $found_promo[0]['max_discount'];
	        					}else
	        					{
	        						$final_value_to_be_deducted = $dicounted_value;
	        					}

		        				# Now deduct it from actual item total then send new discounted item total to get delivery charges
		        				$actual_item_total = $actual_item_total - $final_value_to_be_deducted;
		        				$actual_item_total = number_format($actual_item_total,2, '.', '');
		        				# We will calcualte discounted value of item total as per the promo found and we will use it for checking DC but we will send actual item total (without discount) to mobile team.
		        				// $response['item_total'] = $actual_item_total;
		        				// $response['item_total'] = number_format($response['item_total'],2, '.', '');
		        			} # IN ELSE CASE WE ARE ALREADY HAVING $response['item_total'] made above so if no promo applied then the above generated values will be sent.
		        			else
		        			{
		        				$actual_item_total = $response['item_total'];
		        			}


		        			# get_delivery_charge_promotion will return object
			        		// $delivery_promotion = $this->get_delivery_charge_promotion($restaurant_id , $response['item_total'] , $tokenData,$pickup_time);
			        		$delivery_promotion = $this->get_delivery_charge_promotion($restaurant_id , $actual_item_total , $tokenData,$pickup_time);
			        		$response['delivery_charge_promotion'] = $delivery_promotion;
			        		$restaurant = $this->Common->getData('restaurants','restaurants.*,restaurants.id AS restaurant_id,users.latitude,users.longitude','restaurants.id = "'.$restaurant_id.'"',array('users'),array('users.id = restaurants.admin_id'));
			            	$response['restaurant_detail'] = $restaurant[0];
		        		}

		        		$settings_data = $this->Common->getData('settings','value','name = "basic_delivery_time" OR name = "basic_preparation_time"');
		        		
		        		if($response['restaurant_detail']['delivery_time'] == '')
		        		{
		        			# Get this from settings table
		        			$response['restaurant_detail']['delivery_time'] = $settings_data[0]['value'];
		        		}
		        		if($response['restaurant_detail']['preparation_time'] == '')
		        		{
		        			# Get this from settings table
		        			$response['restaurant_detail']['preparation_time'] = $settings_data[1]['value'];
		        		}

		        		# Check for Delivery Charge end

						// echo $check_all_restro_promo;
						// echo "<br>".$check_restaurant_promo;
						// echo "<br>".$check_category_promo;
						// echo "<br>".$check_product_promo;

						// echo "<pre>FINAL promocode_details";
						// print_r($response['promocode_details']);
						// die;
						# More keys needed
						$offline_status = $this->Common->getData('rest_offline','rest_offline.offline_tag,rest_offline.offline_value,rest_offline.offline_from,rest_offline.offline_to','rest_offline.rest_id = "'.$restaurant_id.'"',array('restaurants'),array('restaurants.id = rest_offline.rest_id'));
						if(count($offline_status) > 0)
						{
							$response['offline_status'] = $offline_status[0];
						}else
						{
							$x = new stdClass();
							$response['offline_status'] = $x;
						}

						if($date_timestamp != '')
						{
							$restro_is_open = $this->get_restaurant_open_close_status($restaurant_id , $date_timestamp); # $restro_is_open 1 YES ELSE CLOSED
							$response['restro_is_open'] = $restro_is_open['status']; # 1 Means open other than 1 and 0 is Closed

						}else
						{
							$response['restro_is_open'] = '0'; # 0 means date_timestamp is not passed.

						}

						$res_data = $this->Common->getData('restaurants','*','id = "'.$restaurant_id.'"');
						$response['open_time'] = $res_data[0]['open_time'];
						$response['close_time'] = $res_data[0]['close_time'];
						$response['break_start_time'] = $res_data[0]['break_start_time'];
						$response['break_end_time'] = $res_data[0]['break_end_time'];
	        		}else
	        		{
	        			$response = array();
	        		}
					# Now we need to check 

	        		# Check for discount applied end
	        		
	            	$data['status']		=200;
		            $data['message']	=$this->lang->line('success');
		            $data['data']		=$response;
            	}else
            	{
            		$data['status']		= 201;
		            $data['message']	= $this->lang->line('restaurant_not_active');
		            $data['data']		= array();
            	}
            }
    		$this->response($data, $data['status']);

    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # checkout_screen_get end

    public function checkout_screen_post()
    {
    	try{
    		$tokenData = $this->verify_request();
    		$restaurant_id = !empty($_POST['restaurant_id'])?$this->db->escape_str($_POST['restaurant_id']):'';
    		$order_type = !empty($_POST['order_type'])?$this->db->escape_str($_POST['order_type']):'';
    		# 1 : Order now 2 : self pickup_time_from 3 : Order for later 4 : Dine In
    		$pickup_time = !empty($_POST['pickup_time'])?$this->db->escape_str($_POST['pickup_time']):'';
    		$latitude = !empty($_POST['latitude'])?$this->db->escape_str($_POST['latitude']):'';
    		$longitude = !empty($_POST['longitude'])?$this->db->escape_str($_POST['longitude']):'';
    		$pin_address = !empty($_POST['pin_address'])?$this->db->escape_str($_POST['pin_address']):'';
    		$delivery_name = !empty($_POST['delivery_name'])?$this->db->escape_str($_POST['delivery_name']):'';
    		$delivery_email = !empty($_POST['delivery_email'])?$this->db->escape_str($_POST['delivery_email']):'';
    		$delivery_mobile = !empty($_POST['delivery_mobile'])?$this->db->escape_str($_POST['delivery_mobile']):'';
    		$unit_number = !empty($_POST['unit_number'])?$this->db->escape_str(trim($_POST['unit_number'])):'';
	    	$street_address = !empty($_POST['street_address'])?$this->db->escape_str(trim($_POST['street_address'])):'';
	    	$postal_code = !empty($_POST['postal_code'])?$this->db->escape_str(trim($_POST['postal_code'])):'';
	    	$date_timestamp = !empty($_POST['date_timestamp'])?$this->db->escape_str(trim($_POST['date_timestamp'])):'';

    		if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($restaurant_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }else if($order_type ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('order_type_missing');
                $data['data']		=array();
            }else if($pickup_time ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('pickup_time_from_missing');
                $data['data']		=array();
            }else
            {
            	$rest_active_status = $this->Common->getData('restaurants','id,rest_status','id = "'.$restaurant_id.'"');
            	$rest_active_status = $rest_active_status[0]['rest_status'];

            	if($rest_active_status == 1)
            	{
            		$response = array();
	            	# First get the address from order table for the last ordered.
	            	# get address start
	            	
	            	# UPDATES
	            	# If lat long and pin address is empty then only we will find pin these details else not. Why because when user first time come to checkout screen then we can find these details as per the last order (and other like delivery address or user;s own address) But suppose user change the address from his addresses then again we need to know what the delivery address is now.
	            	if($latitude == '' || $longitude == '' || $pin_address == '')
	            	{
	            		# That means user is coming first time on checkout screen so we can find delivery address as per our logic
		            	$last_order = $this->Common->getData('orders','delivery_latitude,delivery_longitude,delivery_address,delivery_name,delivery_email,delivery_mobile,delivery_street_address,delivery_postal_code,delivery_unit_number','user_id = "'.$tokenData->id.'"','','','id','DESC',1,0);
		            	
		            	$user_basic_delivery_add = $this->Common->getData('users','fullname,email,mobile','id = "'.$tokenData->id.'"');
		            	if(count($last_order) > 0)
		            	{
		            		# That means this user has some records in order table so get delivery address related data from there
		            		$pin_address = $last_order[0]['delivery_address'];
		            		$delivery_name = $user_basic_delivery_add[0]['fullname'];
		            		$delivery_email = $user_basic_delivery_add[0]['email'];
		            		$delivery_mobile = $user_basic_delivery_add[0]['mobile'];
		            		$latitude = $last_order[0]['delivery_latitude'];
		            		$longitude = $last_order[0]['delivery_longitude'];
		            		$street_address = $last_order[0]['delivery_street_address'];
		            		$postal_code = $last_order[0]['delivery_postal_code'];
		            		$unit_number = $last_order[0]['delivery_unit_number'];
		            	}else
		            	{
		            		# That means user has not made any order yet. So get last entered record from delivery address table
		            		$delivery_add = $this->Common->getData('delivery_address','*','user_id = "'.$tokenData->id.'"','','','id','DESC',1,0);
		            		if(count($delivery_add) > 0)
		            		{
		            			$pin_address = $delivery_add[0]['pin_address'];
			            		$delivery_name = $user_basic_delivery_add[0]['fullname'];
			            		$delivery_email = $user_basic_delivery_add[0]['email'];
			            		$delivery_mobile = $user_basic_delivery_add[0]['mobile'];
			            		$latitude = $delivery_add[0]['del_latitude'];
			            		$longitude = $delivery_add[0]['del_longitude'];
			            		$street_address = $delivery_add[0]['street_address'];
			            		$postal_code = $delivery_add[0]['postal_code'];
			            		$unit_number = $delivery_add[0]['unit_number'];
		            		}else
		            		{
		            			# No address added by user. So get data from user table
		            			$user_delivery_add = $this->Common->getData('users','latitude,longitude,user_pin_address,fullname,email,mobile','id = "'.$tokenData->id.'"','','','id','DESC',1,0);
		            			$pin_address = $user_delivery_add[0]['user_pin_address'];
			            		$delivery_name = $user_basic_delivery_add[0]['fullname'];
			            		$delivery_email = $user_basic_delivery_add[0]['email'];
			            		$delivery_mobile = $user_basic_delivery_add[0]['mobile'];
			            		$latitude = $user_delivery_add[0]['latitude'];
			            		$longitude = $user_delivery_add[0]['longitude'];
			            		$street_address = $user_delivery_add[0]['user_street_address'];
			            		$postal_code = $user_delivery_add[0]['user_postal_code'];
			            		$unit_number = $user_delivery_add[0]['user_unit_number'];
		            		}
		            	}
	            	} 

	            	$response['delivery_address']['pin_address'] = $pin_address;
	            	$response['delivery_address']['delivery_name'] = $delivery_name;
	            	$response['delivery_address']['delivery_email'] = $delivery_email;
	            	$response['delivery_address']['delivery_mobile'] = $delivery_mobile;
	            	$response['delivery_address']['latitude'] = $latitude;
	            	$response['delivery_address']['longitude'] = $longitude;
	            	$response['delivery_address']['street_address'] = $street_address;
	            	$response['delivery_address']['postal_code'] = $postal_code;
	            	$response['delivery_address']['unit_number'] = $unit_number;

	            	$accept_data = $this->Common->getData('restaurants','is_order_now_accept,is_self_pickup_accept,is_order_later_accept,is_dinein_accept','id = "'.$restaurant_id.'"');
	            	$response['is_order_now_accept'] = $accept_data[0]['is_order_now_accept'];
	            	$response['is_self_pickup_accept'] = $accept_data[0]['is_self_pickup_accept'];
	            	$response['is_order_later_accept'] = $accept_data[0]['is_order_later_accept'];
	            	$response['is_dinein_accept'] = $accept_data[0]['is_dinein_accept'];
	            	# get address End

	            	# Now it is required to send restro details also on checkout screen

	            	# Now get the number of items and item total price as per user cart
	            	$cart_response = $this->cart_calculation($tokenData);
	        		$response['items'] = $cart_response['items'];
	            	
	            	# Get cart items start

	        		# Now get complete details of the product available in cart
	        		# DB_Product_Status 1 - Enable 2 - Disable 3 - Deleted
	        		$cart_data = $this->Common->getData('cart','products.*,cart.*,cart.id AS cart_id,products.id AS product_id','cart.user_id = "'.$tokenData->id.'" AND products.product_status = 1',array('products'),array('cart.product_id = products.id'));
	        		$response['cart_products'] = $cart_data;

	        		# Now we will check whehter any variant for cart product available
	        		if(!empty($response['cart_products']))
	        		{
	        			foreach ($response['cart_products'] as $key => $value) 
	        			{
			        		$cart_var_query = "SELECT `cart`.`id` AS `cart_tbl_id`,variants.variant_name,variant_types.variant_type_name,variant_types_for_products.variant_type_price,cart_variant.variant_id,cart_variant.variant_type_id FROM `cart` INNER JOIN `cart_variant` ON `cart`.`id` = `cart_variant`.`cart_id` INNER JOIN `variants` ON `variants`.`variant_id` = `cart_variant`.`variant_id` INNER JOIN `variant_types` ON `variant_types`.`variant_type_id` = `cart_variant`.`variant_type_id` INNER JOIN `variant_types_for_products` ON `variant_types_for_products`.`variant_type_id` = `variant_types`.`variant_type_id` WHERE `cart`.`user_id` = ".$tokenData->id." AND `cart`.`product_id` = ".$value['product_id']." AND `variants`.`variant_status` = 1 AND variant_types_for_products.product_id = ".$value['product_id'];
			        		$cart_var_data = $this->Common->custom_query($cart_var_query,'get');
			        		if(count($cart_var_data) > 0)
			        		{
			        			$response['cart_products'][$key]['variants'] = $cart_var_data;
			        		}else
			        		{
			        			$response['cart_products'][$key]['variants'] = array();
			        		}
	        			}
	        		}

	        		if(!empty($cart_data))
	        		{
		        		# Now it may happen that when customer added a product to cart that time it was enabled and was not offline but when customer wishes to checkout the same then it may go offline for that period so we can send offline status for the cart product so mobile team will check whether product is currently available or not.

		        		# We will check only products (NOT CATEGORY) because if category goes offline then we have already made entries for products_offline table for that category's products
						$item_total = 0;
		        		foreach ($cart_data as $key => $value) 
		        		{
		        			$response['cart_products'][$key]['offline_status'] = $this->Common->getData('products_offline','*','product_id = "'.$value['product_id'].'"');
		        			$offline_status = $this->Common->getData('products_offline','*','product_id = "'.$value['product_id'].'"');
		        			if(count($offline_status) > 0)
		        			{
			        			if ((time() >= $offline_status[0]['offline_from']) && (time() <= $offline_status[0]['offline_to']))
			        			{
			        				# "CURRENTLY DISABLED"
			        				$response['cart_products'][$key]['is_offline'] = 1; # Yes : it is offline (Not available now)
			        			}else
			        			{
			        				$response['cart_products'][$key]['is_offline'] = 0; # No : It is available
			        				if($value['offer_price'] != 0)
			        				{
			        					$item_total += $value['offer_price'] * $value['product_quantity'];
			        				}else
			        				{
			        					$item_total += $value['price'] * $value['product_quantity'];
			        				}
			        			}
		        			}else
		        			{
		        				$response['cart_products'][$key]['is_offline'] = 0; # No : It is available
		        				if($value['offer_price'] != 0)
		        				{
		        					$item_total += $value['offer_price'] * $value['product_quantity'];
		        				}else
		        				{
		        					$item_total += $value['price'] * $value['product_quantity'];
		        				}
		        			}
		        			# Create category id array for checking promotion
		        			$category_ids[] = $value['category_id'];
		        			$product_ids[] = $value['product_id'];
		        		}
		        		$cart_var_data = $this->Common->getData('cart','cart.id AS cart_tbl_id ,cart.product_quantity,products.price,variant_types_for_products.variant_type_price','cart.user_id = "'.$tokenData->id.'" AND cart.product_id = variant_types_for_products.product_id',array('products','cart_variant','variant_types_for_products'),array('products.id = cart.product_id' , 'cart.id = cart_variant.cart_id','variant_types_for_products.variant_type_id = cart_variant.variant_type_id'));

				    	foreach ($cart_var_data as $key => $value) 
				    	{
				    		
				    		$item_total += $value['variant_type_price'] * $value['product_quantity'];
				    	}
		        		$response['item_total'] = $item_total;
		        		$response['item_total'] = number_format($item_total,2, '.', '');
		            	# Get cart items end

		        		# Check for Delivery Charge start
		        		# Here we are currently doing when delivery is handled by the restaurant. 
		        		# Table: restaurants - Column : delivery_handled_by (DB_del_handel_status 1 - restaurant 2 - By Kerala Eats ie lalamove) and column per_km_charge value
		        		# First we have to pass lat long of restaturant and lat long of the destination to calculate distance between two lat longs
		        		# Get admin id of the restaurant
		        		$some_checks = $this->Common->getData('restaurants','admin_id,delivery_handled_by,per_km_charge,business_type AS business_category,food_type','id = "'.$restaurant_id.'"');

		        		$rest_lat_lng = $this->Common->getData('users','latitude,longitude','id = "'.$some_checks[0]['admin_id'].'"');
		        		
		        		$rest_lat = $rest_lat_lng[0]['latitude'];
		        		$rest_lng = $rest_lat_lng[0]['longitude'];
		        		
		        		$destination_lat = $latitude;
		        		$destination_lng = $longitude;

						# ----------------------------- LALAMOVE HANDLING PART --------------------------------------- #

		            	# LALAMOVE will be included only if $order_type is NOT selfpickup and if delivery is handeled by Kerala eats
		            	# Here we need to check for schedleAt. omit this if you are placing an immediate order
		            	# 1 : Order now 2 : self pickup 3 : Order for later 4 : Dine In
		            	// echo "dfhdff";
		            	// echo $order_type;
		            	// echo "<br>";
		            	// echo $some_checks[0]['delivery_handled_by'];
		            	// if($order_type != 2 && $some_checks[0]['delivery_handled_by'] == 2) # That is when it is not a Self pickup order and delivery need to be handeled by Kerala eats
		            	if($order_type != 2) # We are removing the delivery_handled_by check because client wants that whosoever handle the delivey ; charges should come from lalamove and use of selected delivery_handled_by should be on app side only. "If the delivery is being handled by restaurant. The delivery charge calculations can be remained same. But only at the app side, we need to display it."
		            	{
		            		# Get requesterContact information
		            		$lalamove_rest_details = $this->Common->getData('restaurants','restaurants.rest_pin_address,restaurants.rest_unit_number,restaurants.rest_postal_code,restaurants.rest_name AS rest_name,users.mobile,users.latitude,users.longitude','restaurants.id = "'.$restaurant_id.'"',array('users'),array('users.id = restaurants.admin_id'));
		            		$req_phone = $lalamove_rest_details[0]['mobile'];
		            		$req_phone = '+65'.$req_phone;
		            		$to_stop_phone = $this->Common->getData('users','mobile','id = "'.$tokenData->id.'"');
		            		$to_stop_phone = $to_stop_phone[0]['mobile'];
		            		$to_stop_phone = '+65'.$to_stop_phone;
							# requesterContact : Contact person at pick up point
							# stops : The index of waypoint in stops this information associates with, has to be >= 1, since the first stop's Delivery Info is tided to requesterContact
		            		if($order_type == 1) # Order now so dont pass scheduleAt
		            		{
		            			$body = array(
								  // "scheduleAt" => gmdate('Y-m-d\TH:i:s\Z', time() + 60 * 30), // ISOString with the format YYYY-MM-ddTHH:mm:ss.000Z at UTC time
								  "serviceType" => "MOTORCYCLE", // string to pick the available service type
								  "specialRequests" => array(), // array of strings available for the service type
								  "requesterContact" => array(
								    "phone" => $req_phone, // Phone number format must follow the format of your country
								    "name" => $lalamove_rest_details[0]['rest_name'],
								  ),  
								  "stops" => array(
								    array(
								      "location" => array("lat" => $lalamove_rest_details[0]['latitude'], "lng" => $lalamove_rest_details[0]['longitude']),
								      "addresses" => array(
								        "en_SG" => array(
								          "displayString" => $lalamove_rest_details[0]['rest_pin_address'].", ".$lalamove_rest_details[0]['rest_unit_number'].", ".$lalamove_rest_details[0]['rest_postal_code'],
								          "country" => "SG" // Country code must follow the country you are at
								        )   
								      )   
								    ),  
								    array(
								      "location" => array("lat" => $latitude, "lng" => $longitude),
								      "addresses" => array(
								        "en_SG" => array(
								          "displayString" => $pin_address.", ".$unit_number.", ".$postal_code,
								          "country" => "SG" // Country code must follow the country you are at
								        )   
								      )   
								    )   
								  ),  
								  "deliveries" => array(
								    array(
								      "toStop" => 1,
								      "toContact" => array(
								        "phone" => $to_stop_phone, // Phone number format must follow the format of your country
								        "name" => $tokenData->name,
								      ),
								      "remarks" => "1. Kerala Eats Food Order ID: [KEXXXX] \n2. Customer Name: ".$tokenData->name." \n3. Support Number: 90298605\n4. Tips pay by Kerala Eats"
								    )   
								  )   
								);
								// echo "<pre> 3020";
		            		}else # 3 : Order for later
		            		{
		            			// $pickup_time = $pickup_time - 27900; # 8 hours and 15 minutes minus
		            			// $start = new DateTime(date('r', $pickup_time));
		            			// $start = $start->format('Y-m-d\TH:i:s\Z');

		            			$less_15_mint = $pickup_time-900;//we need to less 15 mint
					            $start = new DateTime(date('r', $less_15_mint));
					            $start = $start->format('Y-m-d\TH:i:s\Z');
					            $final_pickup_time  = $start;

			            		$body = array(
								  "scheduleAt" => $final_pickup_time, // ISOString with the format YYYY-MM-ddTHH:mm:ss.000Z at UTC time ## We will make order 15 minutes before for lalamove
								  "serviceType" => "MOTORCYCLE", // string to pick the available service type
								  "specialRequests" => array(), // array of strings available for the service type
								  "requesterContact" => array(
								    "phone" => $req_phone, // Phone number format must follow the format of your country
								    "name" => $lalamove_rest_details[0]['rest_name'],
								  ),  
								  "stops" => array(
								    array(
								      "location" => array("lat" => $lalamove_rest_details[0]['latitude'], "lng" => $lalamove_rest_details[0]['longitude']),
								      "addresses" => array(
								        "en_SG" => array(
								          "displayString" => $lalamove_rest_details[0]['rest_pin_address'].", ".$lalamove_rest_details[0]['rest_unit_number'].", ".$lalamove_rest_details[0]['rest_postal_code'],
								          "country" => "SG" // Country code must follow the country you are at
								        )   
								      )   
								    ),  
								    array(
								      "location" => array("lat" => $latitude, "lng" => $longitude),
								      "addresses" => array(
								        "en_SG" => array(
								          "displayString" => $pin_address.", ".$unit_number.", ".$postal_code,
								          "country" => "SG" // Country code must follow the country you are at
								        )   
								      )   
								    )   
								  ),  
								  "deliveries" => array(
								    array(
								      "toStop" => 1,
								      "toContact" => array(
								        "phone" => $to_stop_phone, // Phone number format must follow the format of your country
								        "name" => $tokenData->name,
								      ),  
								      "remarks" => "1. Kerala Eats Food Order ID: [KEXXXX] \n2. Customer Name: ".$tokenData->name." \n3. Support Number: 90298605\n4. Tips pay by Kerala Eats"
								    )   
								  )   
								);
		            		}

		            		// echo "<pre> 3058";
		            		// echo json_encode($body);
		            		// print_r($body);
		            		// die;

			            	# Now we need to get some information regarding lalamove ordering
			            	$lalamove_order_response = $this->lalamove_quotation_generate($body);
			            	// die;
			            	// echo "<pre> FINAL ";
			            	// print_r($lalamove_order_response);
			            	// die;
			            	// Sample response : array('lalamove_order_id' => $lalamove_order_id , 'lalamove_order_amount' => $amount,'failed_reason' => '' , 'lalamove_track_link' => $track_link);
			            	/*Array
							(
							    [lalamove_order_id] => 172800508026
							    [lalamove_order_amount] => 11.80
							    [failed_reason] => 
							    [lalamove_track_link] => https://share.sandbox.lalamove.com?SG100210602153645628310010070780999&lang=en_SG&version=2&sign=471d582a8cbc30d6172d546bb67eab8d
							)*/
			            	if($lalamove_order_response['failed_reason'] == '')
			            	{
			            		# That means order placed successfully as it has nothing in failed
				            	/*$track_link = $lalamove_order_response['lalamove_track_link'];
								$lalamove_order_id = $lalamove_order_response['lalamove_order_id'];
			            		$lalamove_order_failed_reason = '';
			            		$response['lalamove_order_reference_id'] = $lalamove_order_id;
			            		$response['lalamove_order_failed_reason'] = $lalamove_order_failed_reason;
			            		$response['lalamove_order_status'] = $lalamove_order_status;
			            		$response['lalamove_order_amount'] = $lalamove_order_amount;
			            		$response['lalamove_track_link'] = $track_link;*/
								$lalamove_order_status = 1; #  Success
								$lalamove_order_amount = $lalamove_order_response['lalamove_order_amount'];
			            		$track_link = '';
								$lalamove_order_id = '';
								$lalamove_order_failed_reason = '';
								$response['lalamove_order_reference_id'] = $lalamove_order_id;
								$response['lalamove_order_failed_reason'] = $lalamove_order_failed_reason;
								$response['lalamove_order_status'] = $lalamove_order_status;
			            		$response['lalamove_order_amount'] = $lalamove_order_amount;
			            		$response['lalamove_track_link'] = '';
			            	}else
			            	{
			            		$lalamove_order_status = 2; #  Fail
			            		$lalamove_order_id = '';
			            		$lalamove_order_failed_reason = $lalamove_order_response['failed_reason'];
			            		$response['lalamove_order_reference_id'] = $lalamove_order_id;
			            		$response['lalamove_order_failed_reason'] = $lalamove_order_failed_reason;
			            		$response['lalamove_order_status'] = $lalamove_order_status;
			            		$response['lalamove_order_amount'] = '';
			            		$response['lalamove_track_link'] = '';
			            		# IN CASE OF FAIL WE ARE SENDING A STATIC VALUE
			            		$response['lalamove_order_amount'] = '15.00';
			            	}
		            	}else
		            	{
		            		$track_link = '';
							$lalamove_order_id = '';
							$lalamove_order_status = 3; # Not for lalamove
							$lalamove_order_failed_reason = '';
							$response['lalamove_order_reference_id'] = $lalamove_order_id;
							$response['lalamove_order_failed_reason'] = $lalamove_order_failed_reason;
							$response['lalamove_order_status'] = $lalamove_order_status;
		            		$response['lalamove_order_amount'] = '';
		            		$response['lalamove_track_link'] = '';
		            	}
		        		$response['distance_in_km'] = $this->calculate_distance_between_latlong($latitude,$rest_lat,$longitude,$rest_lng);

		        		if($some_checks[0]['delivery_handled_by'] == 1) # DB_del_handel_status 1 - restaurant 2 - By Kerala Eats ie lalamove
		        		{
			        		$response['per_km_charge'] = $some_checks[0]['per_km_charge'];
		        		}else
		        		{
							$response['per_km_charge'] = PER_KM_CHARGE; // STATIC_CODE_NEED_TO_CHANGE
		        		}
		        		$response['delivery_handled_by'] = $some_checks[0]['delivery_handled_by'];

		        		# Check wallet amount start
		        		# This get_wallet_balance API mysql query will get data only for those values who are valid.
		        		$wallet = $this->Common->getData('wallet','*','user_id = "'.$tokenData->id.'"');
		        		// if(count($wallet) > 0 && $wallet[0]['wallet_balance'] != null)
		        		if(count($wallet) > 0)
		        		// if(count($wallet) > 0)
		        		{
		        			$wallet_balance = $this->get_wallet_balance($tokenData->id);
		        			if(count($wallet_balance) > 0 && $wallet_balance[0]['wallet_balance'] != null)
		        			{
			        			// $wallet_balance = (int)$wallet_balance[0]['wallet_balance'];
			        			// echo "<hr>".$this->db->last_query();
			        			// echo "<pre>";
			        			// print_r($wallet_balance);
			        			$wallet_balance = str_replace(",", "", $wallet_balance[0]['wallet_balance']);
			        			$response['wallet_balance'] = number_format($wallet_balance,2, '.', '');
		        			}else
		        			{
		        				$response['wallet_balance'] = 0.00;		
		        			}
		        		}else
		        		{
		        			$response['wallet_balance'] = 0.00;
		        		}
		        		$response['business_category'] = $some_checks[0]['business_category'];
		        		$response['food_type'] = $some_checks[0]['food_type'];
		        		# Check wallet amount end

		        		# Check for discount applied start
		        		/* AS per client requirement 
			        		1. Platform Promotion & Promotions based on Services (Pick-up/self Collect) Global
							2. Restaurant Promo (Overwrite all the other promotions available below within a restaurant) 
							3. Category Promo (Overwrite all the other promotions available below within a restaurant) 
							4. Product Promo
		        		*/
						# Check whether any global promotion is available and set by kerala eats?
						# Db_added_by_status : 1 : added by master admin 2 : By merchant

						# DB_promo_level : 1:Delivery 2:Restaurant 3:Product 4:Category 5:Global

						# 1. Check any GLOBAL DISCOUNT set by Kerala Eats
						# We have two global level promo (One for overall and other for product wise promotion)
						$check_global_promo = 1;
						# Setting all to 0
						$check_all_restro_promo = 0;
						# promotion_mode_status 1- Promo Code, 2- Discount, 3 - Referral
						$check_restaurant_promo = 0;
						$check_category_promo = 0;
						$check_product_promo = 0;
						$promo_response = array();

						$global_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'level_id' => 5 , 'promotion_mode_status' => 2));
						// echo $this->db->last_query();
						// echo "<pre>global_level_promo";
						// print_r($global_level_promo);
						if(count($global_level_promo) > 0)
						{
							$promo_response = array();
							foreach ($global_level_promo as $key => $global_level) 
							{
								$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $global_level , $tokenData,$pickup_time);
							}
							// echo "<pre>promo_response";
							// print_r($promo_response);
							$current_item_total = $response['item_total'];
							if(count($promo_response) > 0  ) # Multiple promo available as per multiple categories
							{
								# That mens we have promotion on two categories so we need to find which one has high value of discount
								$check_high_promo = array();
								foreach ($promo_response as $key => $selfp_value)
								{
									if(!empty($selfp_value))
									{
										if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
										{
											$disc_val = $selfp_value['discount_value'];
											$disc_val = number_format($disc_val,2, '.', '');
											$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
										}else
										{
											$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
											$disc_val = number_format($disc_val,2, '.', '');
											$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
										}
									}
								}
								// echo "qqqqqq";
								// print_r($check_high_promo);
								if(isset($check_high_promo['high_promo']))
								{
									$max_dis = 0;
									foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
									{
										if($cvalue['disc_val'] > $max_dis)
										{
											$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
											$max_dis = $cvalue['disc_val'];
										}
									}
									$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 2));
									$response['promocode_details'] = $promocode_details;
								}else
								{
									$check_all_restro_promo = 1;		
								}
								// echo "WWWWWW";
								// print_r($response['promocode_details']);die;
							}else
							{
								$check_all_restro_promo = 1;		
							}
						}else
						{
							$check_all_restro_promo = 1;
						}

						if($check_all_restro_promo == 1)
						{
							$all_restro_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => 0 , 'is_auto_apply' => 1 , 'level_id' => 2, 'promotion_mode_status' => 1,'if_promo_for_all_rest' => 1));
							// echo $this->db->last_query();
							// echo "<pre>HELLOo";
							// print_r($all_restro_promo);
							if(count($all_restro_promo) > 0)
							{
								$promo_response = array();
								foreach ($all_restro_promo as $key => $all_restro) 
								{
									$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $all_restro , $tokenData,$pickup_time);
								}
								// echo "<pre>POPPPPO";
								// print_r($promo_response);
								$current_item_total = $response['item_total'];
								if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
								{
									# That mens we have promotion on two categories so we need to find which one has high value of discount
									$check_high_promo = array();
									foreach ($promo_response as $key => $selfp_value)
									{
										if(!empty($selfp_value))
										{
											if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
											{
												$disc_val = $selfp_value['discount_value'];
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}else
											{
												$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}
										}
									}
									// echo "<pre>check_high_promo";
									// print_r($check_high_promo);
									if(isset($check_high_promo['high_promo']))
									{
										$max_dis = 0;
										foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
										{
											if($cvalue['disc_val'] > $max_dis)
											{
												$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
												$max_dis = $cvalue['disc_val'];
											}
										}
										$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										$response['promocode_details'] = $promocode_details;
									}else
									{
										$check_restaurant_promo = 1;		
									}
								}else
								{
									// echo "YHAAYAKYA";
									$check_restaurant_promo = 1;
								}
							}else
							{
								// echo "ORCAMEHERE";
								$check_restaurant_promo = 1;
							}
						}


						if($check_restaurant_promo == 1)
						{
							$rest_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'level_id' => 2, 'promotion_mode_status' => 1));
							if(count($rest_level_promo) > 0)
							{
								$promo_response = array();
								foreach ($rest_level_promo as $key => $rest_level) 
								{
									$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $rest_level , $tokenData,$pickup_time);
								}
								$current_item_total = $response['item_total'];
								if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
								{
									# That mens we have promotion on two categories so we need to find which one has high value of discount
									$check_high_promo = array();
									foreach ($promo_response as $key => $selfp_value)
									{
										if(!empty($selfp_value))
										{
											if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
											{
												$disc_val = $selfp_value['discount_value'];
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}else
											{
												$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}
										}
									}
									if(isset($check_high_promo['high_promo']))
									{
										$max_dis = 0;
										foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
										{
											if($cvalue['disc_val'] > $max_dis)
											{
												$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
												$max_dis = $cvalue['disc_val'];
											}
										}
										$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										$response['promocode_details'] = $promocode_details;
									}else
									{
										$check_category_promo = 1;		
									}
								}else
								{
									$check_category_promo = 1;		
								}
							}else
							{
								$check_category_promo = 1;
							}
						}
						
						if($check_category_promo == 1)
						{
							# 3. CATEGORY LEVEL
							# Check any promotion on category is available?
							# level id for category is 4
							# It may possible that when product added to cart of any category then that category was enabled but while doing checkout it goes offline
							# It may also happen that there are products of different categories and both are having promotions so we need to check which is most applicable
							# It may possible that two product may be of same category so this array will contain two same value so take unique value array
							
							$category_ids = array_unique($category_ids);
							foreach($category_ids as $key => $value)
				    		{
								$category_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'promotion_mode_status' => 1 ,'is_auto_apply' => 1 , 'level_id' => 4,'applied_on_id = "'.$value.'"'));
								if(count($category_level_promo) > 0)
								{
									foreach ($category_level_promo as $index => $cat_promo) 
				    				{
				    					$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $cat_promo , $tokenData,$pickup_time);
				    				}
			    					$current_item_total = $response['item_total'];
			    					if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
			    					{
			    						# That mens we have promotion on two categories so we need to find which one has high value of discount
										$check_high_promo = array();
										foreach ($promo_response as $key => $catp_value)
										{
											if(!empty($catp_value))
											{
												if($catp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
												{
													// $disc_val = $current_item_total - $catp_value['discount_value'];
													$disc_val = $catp_value['discount_value'];
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $catp_value['id'] , 'disc_val' => $disc_val);
												}else
												{
													$disc_val = ($current_item_total * $catp_value['discount_value']) / 100;
													// $disc_val = $current_item_total - $disc_val;
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $catp_value['id'] , 'disc_val' => $disc_val);
												}
											}
										}
										if(isset($check_high_promo['high_promo']))
										{
											$max_dis = 0;
											foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
											{
												if($cvalue['disc_val'] > $max_dis)
												{
													$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
													$max_dis = $cvalue['disc_val'];
												}
											}
											$response['promocode_details'] = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										}else
										{
											$check_product_promo = 1;		
										}
			    					}else
			    					{
			    						$check_product_promo = 1;
			    					}
								}else
								{
									$check_product_promo = 1;
								}
				    		}
						}

						if($check_product_promo == 1)
						{
							# 4. PRODUCT LEVEL
			    			# Check for OFFER ON PRODUCTS
			    			$product_ids = array_unique($product_ids);
							foreach($product_ids as $key => $value)
				    		{
								$product_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'promotion_mode_status' => 1 ,'level_id' => 3,'applied_on_id' => $value));
								// echo $this->db->last_query();
								// echo "product_level_promo<pre>";
								// print_r($product_level_promo);
								if(count($product_level_promo) > 0)
								{
									foreach ($product_level_promo as $index => $prod_promo) 
				    				{
				    					$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $prod_promo , $tokenData,$pickup_time);
				    				}
				    				// echo "promo response<pre>";
				    				// print_r($promo_response);
				    				if(count($promo_response) > 0)
				    				{
				    					$check_high_promo_prod = array();
				    					foreach ($promo_response as $keyv => $catprd_value)
										{
											if(!empty($catprd_value))
											{
												if($catprd_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
												{
													// $disc_val = $current_item_total_prd - $catprd_value['discount_value'];
													$disc_val = $catprd_value['discount_value'];
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo_prod['high_promo'][$keyv] = array('promo_id' => $catprd_value['id'] , 'disc_val' => $disc_val);
												}else
												{
													$disc_val = ($current_item_total_prd * $catprd_value['discount_value']) / 100;
													// $disc_val = $current_item_total_prd - $disc_val;
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo_prod['high_promo'][$keyv] = array('promo_id' => $catprd_value['id'] , 'disc_val' => $disc_val);
												}
											}
										}
										// echo "<pre>check_high_promo_prod";
										// print_r($check_high_promo_prod);
										if(isset($check_high_promo_prod['high_promo']))
										{
											$max_dis = 0;
											foreach ($check_high_promo_prod['high_promo'] as $keyv => $cvalue) 
											{
												if($cvalue['disc_val'] > $max_dis)
												{
													$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
													$max_dis = $cvalue['disc_val'];
												}
											}
											$response['promocode_details'] = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										}else
										{
											$response['promocode_details'] = array();		
										}
				    				}else
				    				{
				    					$response['promocode_details'] = array();
				    				}
								}else
			    				{
			    					$response['promocode_details'] = array();
			    				}
				    		}
						}

						# Now check for promotion that any promotion for delivery charge to this restaturant is available?
		        		# If order_type is self pickup then we have separate promo type for self pickup users
		        		if($order_type == 2) # Self pickup : So check any promo for selfpickup user available? 
		        		{
		        			$check_merchant_self_pickup_promo = 0;	
		        			$super_admin_self_pickup_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => 0 , 'is_auto_apply' => 1 , 'level_id' => 8 , 'promotion_mode_status' => 1));
		        			# restaurant_id = 0 Means it is added by Super Admin for all restaurants
		        			# It may possible that promo on self pickup can either be added by super admin or by merchant
		        			if(count($super_admin_self_pickup_promo) > 0)
		        			{
		        				$promo_response = array();
		        				foreach ($super_admin_self_pickup_promo as $key => $self_pickup) 
		        				{
		        					$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $self_pickup , $tokenData,$pickup_time);
		        				}
								$current_item_total = $response['item_total'];
		    					// if(count($promo_response) > 0 && !empty($promo_response[0])) # Multiple promo available as per multiple categories
		    					if(count($promo_response) > 0) # Multiple promo available as per multiple categories
		    					{
		    						# That mens we have promotion on so we need to find which one has high value of discount
									$check_high_promo = array();
									foreach ($promo_response as $key => $selfp_value)
									{
										if(!empty($selfp_value))
										{
											if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
											{
												// echo "testing";
												$disc_val = $selfp_value['discount_value'];
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}else
											{
												// echo "debug";
												$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}
										}
									}
									if(isset($check_high_promo['high_promo']))
									{
										$max_dis = 0;
										foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
										{
											if($cvalue['disc_val'] > $max_dis)
											{
												$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
												$max_dis = $cvalue['disc_val'];
											}
										}
										$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										$response['delivery_charge_promotion'] = $promocode_details[0];
									}else
									{
										$check_merchant_self_pickup_promo = 1;		
									}
		    					}else
		    					{
		    						$check_merchant_self_pickup_promo = 1;		
		    					}
		        			}else
		        			{
								$check_merchant_self_pickup_promo = 1;
		        			}
		        			
		        			if($check_merchant_self_pickup_promo == 1)
		        			{
		        				$merchant_self_pickup_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'level_id' => 8 , 'promotion_mode_status' => 1));
		        				if(count($merchant_self_pickup_promo) > 0)
		        				{
		        					$promo_response = array();
		        					foreach ($merchant_self_pickup_promo as $key => $mer_self_pickup) 
			        				{
										$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $mer_self_pickup , $tokenData,$pickup_time);
			        				}
									
									$current_item_total = $response['item_total'];
			    					if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
			    					{
			    						# That mens we have promotion on two categories so we need to find which one has high value of discount
										$check_high_promo = array();
										foreach ($promo_response as $key => $selfp_value)
										{
											if(!empty($selfp_value))
											{
												if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
												{
													$disc_val = $selfp_value['discount_value'];
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
												}else
												{
													$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
												}
											}
										}
										if(isset($check_high_promo['high_promo']))
										{
											$max_dis = 0;
											foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
											{
												if($cvalue['disc_val'] > $max_dis)
												{
													$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
													$max_dis = $cvalue['disc_val'];
												}
											}
											$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
											$response['delivery_charge_promotion'] = $promocode_details[0];
										}else
										{
											$x = new stdClass();
											$response['delivery_charge_promotion'] = $x;
										}
			    					}
		        				}else
		        				{
									$x = new stdClass();
									$response['delivery_charge_promotion'] = $x;
		        				}
		        			}

		        			# PLACE HERE
			            	$restaurant = $this->Common->getData('restaurants','restaurants.*,restaurants.id AS restaurant_id,users.latitude,users.longitude','restaurants.id = "'.$restaurant_id.'"',array('users'),array('users.id = restaurants.admin_id'));
			            	$response['restaurant_detail'] = $restaurant[0];
		        		}else
		        		{
		        			# ABOVE WE HAVE FOUDN THE PROMOTIONS. NOW HERE WE NEED TO CHECK WHETHER ANY PROMOTION APPLICABLE AND IS YES THEN DEDUCT THE VALUE FROM ITEM TOTAL
		        			if(count($response['promocode_details']) > 0)
		        			{
		        				# THAT IS SOME PROMOTIONS WE GOT SO DEDUCT IT
		        				$actual_item_total = $response['item_total'];
		        				$found_promo = $response['promocode_details'];
		        				// echo "<pre>";
		        				// print_r($found_promo);die;
		        				if($found_promo[0]['promo_type'] == 2) # PERCENT
		        				{
		        					# Get discounted value
		        					$dicounted_value = ($actual_item_total * $found_promo[0]['discount_value']) / 100;
		        				}else
		        				{
		        					$dicounted_value = $found_promo[0]['discount_value'];
		        				}
		        				# Now compare it with max discount
		        				if(($found_promo[0]['max_discount'] > 0) && ($dicounted_value > $found_promo[0]['max_discount']))
	        					{
	        						$final_value_to_be_deducted = $found_promo[0]['max_discount'];
	        					}else
	        					{
	        						$final_value_to_be_deducted = $dicounted_value;
	        					}

		        				# Now deduct it from actual item total then send new discounted item total to get delivery charges
		        				$actual_item_total = $actual_item_total - $final_value_to_be_deducted;
		        				$actual_item_total = number_format($actual_item_total,2, '.', '');
		        				# We will calcualte discounted value of item total as per the promo found and we will use it for checking DC but we will send actual item total (without discount) to mobile team.
		        				// $response['item_total'] = $actual_item_total;
		        				// $response['item_total'] = number_format($response['item_total'],2, '.', '');
		        			} # IN ELSE CASE WE ARE ALREADY HAVING $response['item_total'] made above so if no promo applied then the above generated values will be sent.
		        			else
		        			{
		        				$actual_item_total = $response['item_total'];
		        			}

		        			# get_delivery_charge_promotion will return object
			        		// $delivery_promotion = $this->get_delivery_charge_promotion($restaurant_id , $response['item_total'] , $tokenData,$pickup_time);
			        		$delivery_promotion = $this->get_delivery_charge_promotion($restaurant_id , $actual_item_total , $tokenData,$pickup_time);
			        		$response['delivery_charge_promotion'] = $delivery_promotion;
			        		$restaurant = $this->Common->getData('restaurants','restaurants.*,restaurants.id AS restaurant_id,users.latitude,users.longitude','restaurants.id = "'.$restaurant_id.'"',array('users'),array('users.id = restaurants.admin_id'));
			            	$response['restaurant_detail'] = $restaurant[0];
		        		}

		        		$settings_data = $this->Common->getData('settings','value','name = "basic_delivery_time" OR name = "basic_preparation_time"');
		        		
		        		if($response['restaurant_detail']['delivery_time'] == '')
		        		{
		        			# Get this from settings table
		        			$response['restaurant_detail']['delivery_time'] = $settings_data[0]['value'];
		        		}
		        		if($response['restaurant_detail']['preparation_time'] == '')
		        		{
		        			# Get this from settings table
		        			$response['restaurant_detail']['preparation_time'] = $settings_data[1]['value'];
		        		}

		        		# Check for Delivery Charge end

						// echo $check_all_restro_promo;
						// echo "<br>".$check_restaurant_promo;
						// echo "<br>".$check_category_promo;
						// echo "<br>".$check_product_promo;

						// echo "<pre>FINAL promocode_details";
						// print_r($response['promocode_details']);
						// die;
						# More keys needed
						$offline_status = $this->Common->getData('rest_offline','rest_offline.offline_tag,rest_offline.offline_value,rest_offline.offline_from,rest_offline.offline_to','rest_offline.rest_id = "'.$restaurant_id.'"',array('restaurants'),array('restaurants.id = rest_offline.rest_id'));
						if(count($offline_status) > 0)
						{
							$response['offline_status'] = $offline_status[0];
						}else
						{
							$x = new stdClass();
							$response['offline_status'] = $x;
						}

						if($date_timestamp != '')
						{
							$restro_is_open = $this->get_restaurant_open_close_status($restaurant_id , $date_timestamp); # $restro_is_open 1 YES ELSE CLOSED
							$response['restro_is_open'] = $restro_is_open['status']; # 1 Means open other than 1 and 0 is Closed

						}else
						{
							$response['restro_is_open'] = '0'; # 0 means date_timestamp is not passed.

						}
						$res_data = $this->Common->getData('restaurants','*','id = "'.$restaurant_id.'"');
						$response['open_time'] = $res_data[0]['open_time'];
						$response['close_time'] = $res_data[0]['close_time'];
						$response['break_start_time'] = $res_data[0]['break_start_time'];
						$response['break_end_time'] = $res_data[0]['break_end_time'];
	        		}else
	        		{
	        			$response = array();
	        		}
					# Now we need to check 

	        		# Check for discount applied end
	        		
	            	$data['status']		=200;
		            $data['message']	=$this->lang->line('success');
		            $data['data']		=$response;
            	}else
            	{
            		$data['status']		= 201;
		            $data['message']	= $this->lang->line('restaurant_not_active');
		            $data['data']		= array();
            	}
            }
    		$this->response($data, $data['status']);

    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # checkout_screen_get end

    # COMMON PROMOTION FUNCTION
    public function check_promotion_all_condition($item_total , $promo_data , $tokenData,$pickup_time = 'NA')
    {
    	# We have to check if any order is being made for future date then is there any promo available which has validity and fall in the same date.
    	# Ex today date is aug 11 and customer makes an order on 11 aug for 15 aug and there is promo ABC which has a validity of 14 aug to 16 aug so at this point ABC promo muse come after checking all other validations
    	# That is why we have added extra param as $pickup_time. In case of order now $pickup_time will be passed as NA and in rest two case it will be passed with valid value. We also need not to check it in case of order now.
    	$response['item_total'] = $item_total;
    	if($response['item_total'] >= $promo_data['min_value']) # This is the only mendatory check
		{
			$is_forever = $promo_data['valid_from'] == 0 ? 1:2; # 1 : yes means it is a forever promo 2 : It has validity
			$allow_multiple_time_use = $promo_data['allow_multiple_time_use'] == 1 ? 1:2; # 1 : It can be used mutiple time by a single user 2 : One user can use it only once
			# max_allowed_times : How many times this can be used
			# If == 0 (That means admin has not given any limit) Else IT has a limit
			$max_allowed_times = $promo_data['max_allowed_times'] == 0 ? 1:$promo_data['max_allowed_times']; # 1 : No max allowed value given ELSE the value given by admin
			$promo_used_times = $promo_data['promo_used_times'];
			$max_discount = $promo_data['max_discount'] == 0 ? 1:$promo_data['max_discount']; # 1 :  No max discount is given

			$is_applicable = false;

			if($max_allowed_times != 1) # 1 : No max allowed value given
			{
				if($promo_used_times < $max_allowed_times)
				{
					$is_applicable = true;
				}else
				{
					$is_applicable = false;
				}
			}else
			{
				$is_applicable = true;
			}

			if($is_applicable)
			{
				if($is_forever == 2) # NO not a forever promotion
				{
					if($pickup_time != "NA" && $pickup_time != 'na')
					{
						// echo "<br>here";
						# THAT MEANS Its NOT an ORDER NOW CASE so check with pickup time
						if(($pickup_time > $promo_data['valid_from']) && ($pickup_time < $promo_data['valid_till']))
						{
							// echo "11111";
							$is_applicable = true;
						}else
						{
							// echo "22222";
							$is_applicable = false;		
						}
					}else # ORDER NOW
					{
						if((time() > $promo_data['valid_from']) && (time() < $promo_data['valid_till']))
						{
							$is_applicable = true;
						}else
						{
							$is_applicable = false;
						}
					}
				}else  # Yes a forever promotion
				{
					$is_applicable = true;
				}
			}

			if($is_applicable)
    		{
    			if($promo_data['promo_type'] == 1) # FLAT
    			{
    				if($response['item_total'] >=  $promo_data['discount_value'])
    				{
    					$is_applicable = true;
    				}else
    				{
    					$is_applicable = false;
    				}
    			}else
    			{
    				$is_applicable = true;
    			}
    		}

			if($is_applicable)
			{
				if($allow_multiple_time_use == 1) # 1 : It can be used mutiple time by a single user
				{
					$is_applicable = true;
				}else # One user can use it only once
				{
					# That is one user can use this only once. So check whether this promo is used by this customer earlier?
					$usr_used_pc = $this->Common->getData('used_promotions','id',array('promotion_id' => $promo_data['id'] , 'user_id' => $tokenData->id));
					if(count($usr_used_pc) > 0)
					{
						$is_applicable = false;
					}else
					{
						$is_applicable = true;
					}
				}
			}
		}else
		{
			$is_applicable = false;
		}

		/* FINAL SATEMENT */
		if($is_applicable)
		{
			return $promo_data;
		}else
		{
			return array();
		}
    }

    # manage_product_qty_checkout_screen start
    # This function is used to either of three action : 1. Decrease product qty from checkout screen 2. Increase product qty from checkout screen 3. Delete any product from checkout screen
    # These actions will be based on the quantity and Id passed.
    # If qty 0 is passed that means simply delete the product from cart else just update the qty in DB
    public function manage_product_qty_checkout_screen_post()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	$prod_id = !empty($_POST['product_id'])?$this->db->escape_str($_POST['product_id']):'';
	    	$restaurant_id = !empty($_POST['restaurant_id'])?$this->db->escape_str($_POST['restaurant_id']):'';
	    	$order_type = !empty($_POST['order_type'])?$this->db->escape_str($_POST['order_type']):'';
	    	# 1 : Order now 2 : self pickup_time_from 3 : Order for later 4 : Dine In
            $prod_quantity = $_POST['prod_quantity'];
            
            /*$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
    		$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;*/

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($prod_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('product_missing');
                $data['data']		=array();
            }else if($prod_quantity == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('quantity_missing');
                $data['data']		=array();
            }else if($restaurant_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }else if($order_type ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('order_type_missing');
                $data['data']		=array();
            }else
            {
            	if($prod_quantity == 0)
            	{
            		# Remove this data
            		$this->Common->deleteData('cart' , array('product_id' => $prod_id , 'user_id' => $tokenData->id));
            	}else
            	{
	            	# We will simply update the qunatity sent from mobile side.
	            	$this->Common->updateData('cart' , array('product_quantity' => $prod_quantity , 'updated_at' => time()),array('user_id'=> $tokenData->id , 'product_id' => $prod_id));
	        		
            	}
            	$cart_response = $this->cart_calculation($tokenData);
            	if($cart_response['items'] > 0)
            	{

	        		$response_return['item_total'] = $cart_response['item_total'];
	        		$response_return['items'] = $cart_response['items'];

	        		# Get cart items start

	        		# Now get complete details of the product available in cart
	        		# DB_Product_Status 1 - Enable 2 - Disable 3 - Deleted
	        		$cart_data = $this->Common->getData('cart','products.*,cart.*,cart.id AS cart_id,products.id AS product_id','cart.user_id = "'.$tokenData->id.'" AND products.product_status = 1',array('products'),array('cart.product_id = products.id'));
	        		$response_return['cart_products'] = $cart_data;

	        		if(!empty($cart_data))
	        		{
	        			$item_total = 0;
		        		foreach ($cart_data as $key => $value) 
		        		{
		        			$response['cart_products'][$key]['offline_status'] = $this->Common->getData('products_offline','*','product_id = "'.$value['product_id'].'"');
		        			$offline_status = $this->Common->getData('products_offline','*','product_id = "'.$value['product_id'].'"');
		        			if(count($offline_status) > 0)
		        			{
			        			if ((time() >= $offline_status[0]['offline_from']) && (time() <= $offline_status[0]['offline_to']))
			        			{
			        				# "CURRENTLY DISABLED"
			        				$response['cart_products'][$key]['is_offline'] = 1; # Yes : it is offline (Not available now)
			        			}else
			        			{
			        				$response['cart_products'][$key]['is_offline'] = 0; # No : It is available
			        				if($value['offer_price'] != 0)
			        				{
			        					$item_total += $value['offer_price'] * $value['product_quantity'];
			        				}else
			        				{
			        					$item_total += $value['price'] * $value['product_quantity'];
			        				}
			        			}
		        			}else
		        			{
		        				$response['cart_products'][$key]['is_offline'] = 0; # No : It is available
		        				if($value['offer_price'] != 0)
		        				{
		        					$item_total += $value['offer_price'] * $value['product_quantity'];
		        				}else
		        				{
		        					$item_total += $value['price'] * $value['product_quantity'];
		        				}
		        			}
		        			# Create category id array for checking promotion
		        			$category_ids[] = $value['category_id'];
		        			$product_ids[] = $value['product_id'];
		        		}
		        		$cart_var_data = $this->Common->getData('cart','cart.id AS cart_tbl_id ,cart.product_quantity,products.price,variant_types_for_products.variant_type_price','cart.user_id = "'.$tokenData->id.'" AND cart.product_id = variant_types_for_products.product_id',array('products','cart_variant','variant_types_for_products'),array('products.id = cart.product_id' , 'cart.id = cart_variant.cart_id','variant_types_for_products.variant_type_id = cart_variant.variant_type_id'));

				    	foreach ($cart_var_data as $key => $value) {
				    		$item_total += $value['variant_type_price'] * $value['product_quantity'];
				    	}
				    	$response['item_total'] = $item_total;
		        		$response['item_total'] = number_format($item_total,2, '.', '');

		        		$response_return['item_total'] = $response['item_total'];
	        			$response_return['items'] = $cart_response['items'];

		        		# Now it may happen that when customer added a product to cart that time it was enabled and was not offline but when customer wishes to checkout the same then it may go offline for that period so we can send offline status for the cart product so mobile team will check whether product is currently available or not.

		        		# We will check only products (Not category) because if category goes offline then we are also making entries for products_offline table for that category's products

		        		foreach ($cart_data as $key => $value) 
		        		{
		        			$response_return['cart_products'][$key]['offline_status'] = $offline_status = $this->Common->getData('products_offline','*','product_id = "'.$value['product_id'].'"');
		        		}
		            	# Get cart items end

		            	# Now check for promotion that any promotion for delivery charge to this restaturant is available?
		        		# If order_type is self pickup then we have separate promo type for self pickup users
		        		if($order_type == 2) # Self pickup : So check any promo for selfpickup user available? 
		        		{
		        			$check_merchant_self_pickup_promo = 0;	
		        			$super_admin_self_pickup_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => 0 , 'is_auto_apply' => 1 , 'level_id' => 8 , 'promotion_mode_status' => 1));
		        			# restaurant_id = 0 Means it is added by Super Admin for all restaurants
		        			# It may possible that promo on self pickup can either be added by super admin or by merchant
		        			if(count($super_admin_self_pickup_promo) > 0)
		        			{
		        				$promo_response = array();
		        				foreach ($super_admin_self_pickup_promo as $key => $self_pickup) 
		        				{
		        					$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $self_pickup , $tokenData);
		        				}
								$current_item_total = $response['item_total'];
		    					if(count($promo_response) > 0  ) # Multiple promo available as per multiple categories
		    					{
		    						# That mens we have promotion on two categories so we need to find which one has high value of discount
									$check_high_promo = array();
									foreach ($promo_response as $key => $selfp_value)
									{
										if(!empty($selfp_value))
										{
											if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
											{
												$disc_val = $selfp_value['discount_value'];
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}else
											{
												$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}
										}
									}
									if(isset($check_high_promo['high_promo']))
									{
										$max_dis = 0;
										foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
										{
											if($cvalue['disc_val'] > $max_dis)
											{
												$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
												$max_dis = $cvalue['disc_val'];
											}
										}
										$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										$response['delivery_charge_promotion'] = $promocode_details;
									}
		    					}else
		    					{
		    						$check_merchant_self_pickup_promo = 1;		
		    					}
		        			}else
		        			{
								$check_merchant_self_pickup_promo = 1;
		        			}
		        			
		        			if($check_merchant_self_pickup_promo == 1)
		        			{
		        				$merchant_self_pickup_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'level_id' => 8 , 'promotion_mode_status' => 1));
		        				if(count($merchant_self_pickup_promo) > 0)
		        				{
		        					$promo_response = array();
									foreach ($merchant_self_pickup_promo as $key => $mer_self_pickup) 
			        				{
										$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $mer_self_pickup , $tokenData);
			        				}
									
									$current_item_total = $response['item_total'];
			    					if(count($promo_response) > 0  ) # Multiple promo available as per multiple categories
			    					{
			    						# That mens we have promotion on two categories so we need to find which one has high value of discount
										$check_high_promo = array();
										foreach ($promo_response as $key => $selfp_value)
										{
											if(!empty($selfp_value))
											{
												if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
												{
													$disc_val = $selfp_value['discount_value'];
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
												}else
												{
													$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
												}
											}
										}
										if(isset($check_high_promo['high_promo']))
										{
											$max_dis = 0;
											foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
											{
												if($cvalue['disc_val'] > $max_dis)
												{
													$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
													$max_dis = $cvalue['disc_val'];
												}
											}
											$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
											$response['delivery_charge_promotion'] = $promocode_details[0];
										}
			    					}
		        				}
		        				else
		        				{
									$x = new stdClass();
									$response['delivery_charge_promotion'] = $x;
		        				}
								// if(count($promo_response) > 0)
								// {
								// 	$response['delivery_charge_promotion'] = $merchant_self_pickup_promo[0];
								// }else
								// {
								// }
		        			}
		        		}else
		        		{
			        		# get_delivery_charge_promotion will return object
			        		$delivery_promotion = $this->get_delivery_charge_promotion($restaurant_id , $response['item_total'] , $tokenData);
			        		// echo "<pre>delivery";
			        		// print_r($delivery_promotion);die;
			        		$response['delivery_charge_promotion'] = $delivery_promotion;
		        		}

		        		$check_global_promo = 1;
						# Setting all to 0
						$check_all_restro_promo = 0;
						# promotion_mode_status 1- Promo Code, 2- Discount, 3 - Referral
						$check_restaurant_promo = 0;
						$check_category_promo = 0;
						$check_product_promo = 0;
						$promo_response = array();

						$global_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'level_id' => 5 , 'promotion_mode_status' => 2));
						// echo $this->db->last_query();
						// echo "<pre>global_level_promo";
						// print_r($global_level_promo);
						if(count($global_level_promo) > 0)
						{
							$promo_response = array();
							foreach ($global_level_promo as $key => $global_level) 
							{
								$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $global_level , $tokenData);
							}
							// echo "<pre>promo_response";
							// print_r($promo_response);
							$current_item_total = $response['item_total'];
							if(count($promo_response) > 0  ) # Multiple promo available as per multiple categories
							{
								# That mens we have promotion on two categories so we need to find which one has high value of discount
								$check_high_promo = array();
								foreach ($promo_response as $key => $selfp_value)
								{
									if(!empty($selfp_value))
									{
										if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
										{
											$disc_val = $selfp_value['discount_value'];
											$disc_val = number_format($disc_val,2, '.', '');
											$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
										}else
										{
											$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
											$disc_val = number_format($disc_val,2, '.', '');
											$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
										}
									}
								}
								// echo "qqqqqq";
								// print_r($check_high_promo);
								if(isset($check_high_promo['high_promo']))
								{
									$max_dis = 0;
									foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
									{
										if($cvalue['disc_val'] > $max_dis)
										{
											$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
											$max_dis = $cvalue['disc_val'];
										}
									}
									$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 2));
									$response['promocode_details'] = $promocode_details;
								}else
								{
									$check_all_restro_promo = 1;		
								}
								// echo "WWWWWW";
								// print_r($response['promocode_details']);die;
							}else
							{
								$check_all_restro_promo = 1;		
							}
						}else
						{
							$check_all_restro_promo = 1;
						}

						if($check_all_restro_promo == 1)
						{
							$all_restro_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => 0 , 'is_auto_apply' => 1 , 'level_id' => 2, 'promotion_mode_status' => 1,'if_promo_for_all_rest' => 1));
							// echo $this->db->last_query();
							// echo "<pre>HELLOo";
							// print_r($all_restro_promo);
							if(count($all_restro_promo) > 0)
							{
								$promo_response = array();
								foreach ($all_restro_promo as $key => $all_restro) 
								{
									$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $all_restro , $tokenData);
								}
								// echo "<pre>POPPPPO";
								// print_r($promo_response);
								$current_item_total = $response['item_total'];
								if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
								{
									# That mens we have promotion on two categories so we need to find which one has high value of discount
									$check_high_promo = array();
									foreach ($promo_response as $key => $selfp_value)
									{
										if(!empty($selfp_value))
										{
											if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
											{
												$disc_val = $selfp_value['discount_value'];
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}else
											{
												$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}
										}
									}
									// echo "<pre>check_high_promo";
									// print_r($check_high_promo);
									if(isset($check_high_promo['high_promo']))
									{
										$max_dis = 0;
										foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
										{
											if($cvalue['disc_val'] > $max_dis)
											{
												$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
												$max_dis = $cvalue['disc_val'];
											}
										}
										$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										$response['promocode_details'] = $promocode_details;
									}else
									{
										$check_restaurant_promo = 1;		
									}
								}else
								{
									// echo "YHAAYAKYA";
									$check_restaurant_promo = 1;
								}
							}else
							{
								// echo "ORCAMEHERE";
								$check_restaurant_promo = 1;
							}
						}
						
						// die;

						if($check_restaurant_promo == 1)
						{
							$rest_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'level_id' => 2, 'promotion_mode_status' => 1));
							if(count($rest_level_promo) > 0)
							{
								$promo_response = array();
								foreach ($rest_level_promo as $key => $rest_level) 
								{
									$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $rest_level , $tokenData);
								}
								$current_item_total = $response['item_total'];
								if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
								{
									# That mens we have promotion on two categories so we need to find which one has high value of discount
									$check_high_promo = array();
									foreach ($promo_response as $key => $selfp_value)
									{
										if(!empty($selfp_value))
										{
											if($selfp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
											{
												$disc_val = $selfp_value['discount_value'];
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}else
											{
												$disc_val = ($current_item_total * $selfp_value['discount_value']) / 100;
												$disc_val = number_format($disc_val,2, '.', '');
												$check_high_promo['high_promo'][$key] = array('promo_id' => $selfp_value['id'] , 'disc_val' => $disc_val);
											}
										}
									}
									if(isset($check_high_promo['high_promo']))
									{
										$max_dis = 0;
										foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
										{
											if($cvalue['disc_val'] > $max_dis)
											{
												$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
												$max_dis = $cvalue['disc_val'];
											}
										}
										$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										$response['promocode_details'] = $promocode_details;
									}else
									{
										$check_category_promo = 1;		
									}
								}else
								{
									$check_category_promo = 1;		
								}
							}else
							{
								$check_category_promo = 1;
							}
						}
						
						if($check_category_promo == 1)
						{
							# 3. CATEGORY LEVEL
							# Check any promotion on category is available?
							# level id for category is 4
							# It may possible that when product added to cart of any category then that category was enabled but while doing checkout it goes offline
							# It may also happen that there are products of different categories and both are having promotions so we need to check which is most applicable
							# It may possible that two product may be of same category so this array will contain two same value so take unique value array
							
							$category_ids = array_unique($category_ids);
							foreach($category_ids as $key => $value)
				    		{
								$category_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'promotion_mode_status' => 1 ,'is_auto_apply' => 1 , 'level_id' => 4,'applied_on_id = "'.$value.'"'));
								if(count($category_level_promo) > 0)
								{
									foreach ($category_level_promo as $index => $cat_promo) 
				    				{
				    					$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $cat_promo , $tokenData);
				    				}
			    					$current_item_total = $response['item_total'];
			    					if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
			    					{
			    						# That mens we have promotion on two categories so we need to find which one has high value of discount
										$check_high_promo = array();
										foreach ($promo_response as $key => $catp_value)
										{
											if(!empty($catp_value))
											{
												if($catp_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
												{
													// $disc_val = $current_item_total - $catp_value['discount_value'];
													$disc_val = $catp_value['discount_value'];
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $catp_value['id'] , 'disc_val' => $disc_val);
												}else
												{
													$disc_val = ($current_item_total * $catp_value['discount_value']) / 100;
													// $disc_val = $current_item_total - $disc_val;
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo['high_promo'][$key] = array('promo_id' => $catp_value['id'] , 'disc_val' => $disc_val);
												}
											}
										}
										if(isset($check_high_promo['high_promo']))
										{
											$max_dis = 0;
											foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
											{
												if($cvalue['disc_val'] > $max_dis)
												{
													$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
													$max_dis = $cvalue['disc_val'];
												}
											}
											$response['promocode_details'] = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										}else
										{
											$check_product_promo = 1;		
										}
			    					}else
			    					{
			    						$check_product_promo = 1;
			    					}
								}else
								{
									$check_product_promo = 1;
								}
				    		}
						}

						if($check_product_promo == 1)
						{
							# 4. PRODUCT LEVEL
			    			# Check for OFFER ON PRODUCTS
			    			$product_ids = array_unique($product_ids);
							foreach($product_ids as $key => $value)
				    		{
								$product_level_promo = $this->Common->getData('promotions','*',array('promo_status' => 1 , 'restaurant_id' => $restaurant_id , 'is_auto_apply' => 1 , 'promotion_mode_status' => 1 ,'level_id' => 3,'applied_on_id' => $value));
								// echo $this->db->last_query();
								// echo "product_level_promo<pre>";
								// print_r($product_level_promo);
								if(count($product_level_promo) > 0)
								{
									foreach ($product_level_promo as $index => $prod_promo) 
				    				{
				    					$promo_response[] = $this->check_promotion_all_condition($response['item_total'] , $prod_promo , $tokenData);
				    				}
				    				// echo "promo response<pre>";
				    				// print_r($promo_response);
				    				if(count($promo_response) > 0)
				    				{
				    					$check_high_promo_prod = array();
				    					foreach ($promo_response as $keyv => $catprd_value)
										{
											if(!empty($catprd_value))
											{
												if($catprd_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
												{
													// $disc_val = $current_item_total_prd - $catprd_value['discount_value'];
													$disc_val = $catprd_value['discount_value'];
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo_prod['high_promo'][$keyv] = array('promo_id' => $catprd_value['id'] , 'disc_val' => $disc_val);
												}else
												{
													$disc_val = ($current_item_total_prd * $catprd_value['discount_value']) / 100;
													// $disc_val = $current_item_total_prd - $disc_val;
													$disc_val = number_format($disc_val,2, '.', '');
													$check_high_promo_prod['high_promo'][$keyv] = array('promo_id' => $catprd_value['id'] , 'disc_val' => $disc_val);
												}
											}
										}
										// echo "<pre>check_high_promo_prod";
										// print_r($check_high_promo_prod);
										if(isset($check_high_promo_prod['high_promo']))
										{
											$max_dis = 0;
											foreach ($check_high_promo_prod['high_promo'] as $keyv => $cvalue) 
											{
												if($cvalue['disc_val'] > $max_dis)
												{
													$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
													$max_dis = $cvalue['disc_val'];
												}
											}
											$response['promocode_details'] = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
										}else
										{
											$response['promocode_details'] = array();		
										}
				    				}else
				    				{
				    					$response['promocode_details'] = array();
				    				}
								}else
			    				{
			    					$response['promocode_details'] = array();
			    				}
				    		}
						}

						// echo "<br>".$check_all_restro_promo;
						// echo "<br>".$check_restaurant_promo;
						// echo "<br>".$check_category_promo;
						// echo "<br>".$check_product_promo;
						// die;

						$response_return['delivery_charge_promotion'] = $response['delivery_charge_promotion'];
						$response_return['promocode_details'] = $response['promocode_details'];
	        		}
	        		$data['status']		=200;
	                $data['message']	=$this->lang->line('success');
	                $data['data']		=$response_return;
            	}else
            	{
            		$data['status']		=201;
		            $data['message']	=$this->lang->line('no_data_found');
		            $data['data']		=array(); 
            	}	
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # manage_product_qty_checkout_screen end

    # check_and_apply_promo_code start
    # This function is used to check whether promo code entered by the user on checkout screen is applicable or not
    public function check_and_apply_promo_code_post()
    {
    	try{
    		$tokenData = $this->verify_request();
    		$promo_code = !empty($_POST['promo_code'])?$this->db->escape_str($_POST['promo_code']):'';
    		$promo_code = trim(strtoupper($promo_code));
    		$restaurant_id = !empty($_POST['restaurant_id'])?$this->db->escape_str($_POST['restaurant_id']):'';
    		$item_total = !empty($_POST['item_total'])?$this->db->escape_str($_POST['item_total']):'';
    		# NEW KEYS ADDED
    		$order_type = !empty($_POST['order_type'])?$this->db->escape_str($_POST['order_type']):'';
    		$pickup_time = !empty($_POST['pickup_time'])?$this->db->escape_str($_POST['pickup_time']):'';
    		
    		if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	= $this->lang->line('unauthorized_access');
            }else if($promo_code == ''){
                $data['status']		=201;
                $data['message']	= $this->lang->line('promo_code_missing');
                $data['data']		= array();
            }else if($restaurant_id == ''){
                $data['status']		=201;
                $data['message']	= $this->lang->line('rest_id_missing');
                $data['data']		= array();
            }else if($item_total == ''){
                $data['status']		=201;
                $data['message']	= $this->lang->line('item_total_missing');
                $data['data']		= array();
            }else
		    {
		    	$response = array();
		    	if($order_type == '')
		    	{
	            	$promo_cd = $this->Common->getData('promotions','*',array('code_name' => $promo_code , 'promo_status' => 1 , 'is_auto_apply' => 2, 'promotion_mode_status' => 1)); # Promo status 1 - Enable 2 - Disable 3 - Deleted  is_auto_apply : 1 - Auto apply 2 - Not auto apply (Getting data of promo which is NOT AUTO APPLY)
	            	
	            	if(!empty($promo_cd))
	            	{
	            		# time() > valid_from AND time() < valid_till
		            	if($item_total >= $promo_cd[0]['min_value']) # This is the only mendatory check
						{
							$is_forever = $promo_cd[0]['valid_from'] == 0 ? 1:2; # 1 : yes means it is a forever promo 2 : It has validity
							$allow_multiple_time_use = $promo_cd[0]['allow_multiple_time_use'] == 1 ? 1:2; # 1 : It can be used mutiple time by a single user 2 : One user can use it only once
							# max_allowed_times : How many times this can be used
							# If == 0 (That means admin has not given any limit) Else IT has a limit
							$max_allowed_times = $promo_cd[0]['max_allowed_times'] == 0 ? 1:$promo_cd[0]['max_allowed_times']; # 1 : No max allowed value given ELSE the value given by admin
							$promo_used_times = $promo_cd[0]['promo_used_times'];
							$max_discount = $promo_cd[0]['max_discount'] == 0 ? 1:$promo_cd[0]['max_discount']; # 1 :  No max discount is given

							$is_applicable = false;

							if($promo_cd[0]['if_promo_for_all_rest'] == 1 )
		            		{
		            			$is_applicable = true;
		            		}else
		            		{
		            			# This promo code is restaurant specific ok so we have to again get the promo code details from db with restaurant Id condition
		            			# It may happen that promo with same name can exist multiple time because let suppose if a promotion is created for only 2 restaurant then we have to make 2 entries for this same propmo name. Everything will  be same only applied on id and resturant id will be changed so if rpomotion is not for all restro then we will append restaurant condition check and will get data in same variable as promo_cd
		            			# We will use same variabel as promo_cd with restuarant Id conidtion because eveything will be same
		            			$promo_cd = $this->Common->getData('promotions','*',array('code_name' => $promo_code , 'restaurant_id' => $restaurant_id,'promo_status' => 1 , 'is_auto_apply' => 2, 'promotion_mode_status' => 1)); # Promo status 1 - Enable 2 - Disable 3 - Deleted  is_auto_apply : 1 - Auto apply 2 - Not auto apply (Getting data of promo which is NOT AUTO APPLY)
		            			if($promo_cd[0]['restaurant_id'] == $restaurant_id)
		            			{
		            				$is_applicable = true;
		            			}else
			            		{
			            			$is_applicable = false;
			            		}
		            		}

		            		if($is_applicable)
		            		{
		            			if($promo_cd[0]['promo_type'] == 1) # FLAT
		            			{
		            				if($item_total >=  $promo_cd[0]['discount_value'])
		            				{
		            					$is_applicable = true;
		            				}else
		            				{
		            					$is_applicable = false;
		            				}
		            			}else
		            			{
		            				$is_applicable = true;
		            			}
		            		}

		            		if($is_applicable)
		            		{
								if($max_allowed_times != 1) # 1 : No max allowed value given
								{
									if($promo_used_times < $max_allowed_times)
									{
										$is_applicable = true;
									}else
									{
										$is_applicable = false;
									}
								}else
								{
									$is_applicable = true;
								}
		            		}

							if($is_applicable)
							{
								if($is_forever == 2) # NO not a forever promotion
								{
									# New condition addded
									if($pickup_time != '')
									{
										if($pickup_time != "NA" && $pickup_time != 'na')
										{
											# THAT MEANS Its NOT an ORDER NOW CASE so check with pickup time
											if(($pickup_time > $promo_cd['valid_from']) && ($pickup_time < $promo_cd['valid_till']))
											{
												$is_applicable = true;
											}else
											{
												$is_applicable = false;		
											}
										}else # ORDER NOW
										{
											if((time() > $promo_cd['valid_from']) && (time() < $promo_cd['valid_till']))
											{
												$is_applicable = true;
											}else
											{
												$is_applicable = false;
											}
										}
									}else
									{
										# It will work as it was already working
										if((time() > $promo_cd[0]['valid_from']) && (time() < $promo_cd[0]['valid_till']))
										{
											$is_applicable = true;
										}else
										{
											$is_applicable = false;
										}
									}
								}else  # Yes a forever promotion
								{
									$is_applicable = true;
								}
							}
							if($is_applicable)
							{
								if($allow_multiple_time_use == 1) # 1 : It can be used mutiple time by a single user
								{
									$is_applicable = true;
								}else # One user can use it only once
								{
									# That is one user can use this only once. So check whether this promo is used by this customer earlier?
									$usr_used_pc = $this->Common->getData('used_promotions','id',array('promotion_id' =>$promo_cd[0]['id'] , 'user_id' => $tokenData->id));
									if(count($usr_used_pc) > 0)
									{
										$is_applicable = false;
									}else
									{
										$is_applicable = true;
									}
								}
							}
						}else
						{
							$is_applicable = false;
						}

						if($is_applicable)
						{
							$response = array();
							$level_id = $promo_cd[0]['level_id'];
							if($level_id == 1 || $level_id == 8)
							{
								$response['delivery_charge_promotion'] = $promo_cd[0];
							}else
							{
								$response['promocode_details'] = $promo_cd;
							}

							$data['status']		= 200;
			                $data['message']	= $this->lang->line('promo_success');
			                $data['data']		= $response;
						}else
						{
							$x = new stdClass();
							$data['status']		= 201;
			                $data['message']	= $this->lang->line('promo_code_not_valid');
			                $data['data']		= $x;
						}
	            	}else
	            	{
	            		$x = new stdClass();
	            		$data['status']		= 201;
		                $data['message']	= $this->lang->line('promo_code_not_valid');
		                $data['data']		= $x;
	            	}
		    	}else
		    	{
		    		// echo "1111<br>";
		    		# That means new updation where promo of self pickup will be appliable only if the order type is self pickup
		    		$promo_cd = $this->Common->getData('promotions','*',array('code_name' => $promo_code , 'promo_status' => 1 , 'is_auto_apply' => 2, 'promotion_mode_status' => 1)); # Promo status 1 - Enable 2 - Disable 3 - Deleted  is_auto_apply : 1 - Auto apply 2 - Not auto apply (Getting data of promo which is NOT AUTO APPLY)
	            	
	            	if(!empty($promo_cd))
	            	{
	            		// echo "22222<br>";
	            		# time() > valid_from AND time() < valid_till
		            	if($item_total >= $promo_cd[0]['min_value']) # This is the only mendatory check
						{
							// echo "3333<br>";
							$is_forever = $promo_cd[0]['valid_from'] == 0 ? 1:2; # 1 : yes means it is a forever promo 2 : It has validity
							$allow_multiple_time_use = $promo_cd[0]['allow_multiple_time_use'] == 1 ? 1:2; # 1 : It can be used mutiple time by a single user 2 : One user can use it only once
							# max_allowed_times : How many times this can be used
							# If == 0 (That means admin has not given any limit) Else IT has a limit
							$max_allowed_times = $promo_cd[0]['max_allowed_times'] == 0 ? 1:$promo_cd[0]['max_allowed_times']; # 1 : No max allowed value given ELSE the value given by admin
							$promo_used_times = $promo_cd[0]['promo_used_times'];
							$max_discount = $promo_cd[0]['max_discount'] == 0 ? 1:$promo_cd[0]['max_discount']; # 1 :  No max discount is given

							$is_applicable = false;

							if($promo_cd[0]['if_promo_for_all_rest'] == 1 )
		            		{
		            			// echo "4444<br>";
		            			$is_applicable = true;
		            		}else
		            		{
		            			# This promo code is restaurant specific ok so we have to again get the promo code details from db with restaurant Id condition
		            			# It may happen that promo with same name can exist multiple time because let suppose if a promotion is created for only 2 restaurant then we have to make 2 entries for this same propmo name. Everything will  be same only applied on id and resturant id will be changed so if rpomotion is not for all restro then we will append restaurant condition check and will get data in same variable as promo_cd
		            			# We will use same variabel as promo_cd with restuarant Id conidtion because eveything will be same
		            			$promo_cd = $this->Common->getData('promotions','*',array('code_name' => $promo_code , 'restaurant_id' => $restaurant_id,'promo_status' => 1 , 'is_auto_apply' => 2, 'promotion_mode_status' => 1)); # Promo status 1 - Enable 2 - Disable 3 - Deleted  is_auto_apply : 1 - Auto apply 2 - Not auto apply (Getting data of promo which is NOT AUTO APPLY)
		            			// echo "55555<br>";
		            			if($promo_cd[0]['restaurant_id'] == $restaurant_id)
		            			{
		            				// echo "666666<br>";
		            				$is_applicable = true;
		            			}else
			            		{
			            			// echo "777777<br>";
			            			$is_applicable = false;
			            		}
		            		}

		            		if($is_applicable)
		            		{
		            			// echo "88888<br>";
		            			if($promo_cd[0]['promo_type'] == 1) # FLAT
		            			{
		            				// echo "99999<br>";
		            				if($item_total >=  $promo_cd[0]['discount_value'])
		            				{
		            					// echo "qqqqqq<br>";
		            					$is_applicable = true;
		            				}else
		            				{
		            					// echo "www<br>";
		            					$is_applicable = false;
		            				}
		            			}else
		            			{
		            				// echo "eeeee<br>";
		            				$is_applicable = true;
		            			}
		            		}

		            		if($is_applicable)
		            		{
		            			// echo "rrrrr<br>";
								if($max_allowed_times != 1) # 1 : No max allowed value given
								{
									if($promo_used_times < $max_allowed_times)
									{
										$is_applicable = true;
									}else
									{
										$is_applicable = false;
									}
								}else
								{
									$is_applicable = true;
								}
		            		}

							if($is_applicable)
							{
								// echo "ttttttt<br>";
								if($is_forever == 2) # NO not a forever promotion
								{
									# New condition addded
									if($pickup_time != '')
									{
										if($pickup_time != "NA" && $pickup_time != 'na')
										{
											# THAT MEANS Its NOT an ORDER NOW CASE so check with pickup time
											if(($pickup_time > $promo_cd[0]['valid_from']) && ($pickup_time < $promo_cd[0]['valid_till']))
											{
												$is_applicable = true;
											}else
											{
												$is_applicable = false;		
											}
										}else # ORDER NOW
										{
											if((time() > $promo_cd[0]['valid_from']) && (time() < $promo_cd[0]['valid_till']))
											{
												$is_applicable = true;
											}else
											{
												$is_applicable = false;
											}
										}
									}else
									{
										# It will work as it was already working
										if((time() > $promo_cd[0]['valid_from']) && (time() < $promo_cd[0]['valid_till']))
										{
											$is_applicable = true;
										}else
										{
											$is_applicable = false;
										}
									}
								}else  # Yes a forever promotion
								{
									$is_applicable = true;
								}
							}

							if($is_applicable)
							{
								// echo "yyyyy<br>";
								if($allow_multiple_time_use == 1) # 1 : It can be used mutiple time by a single user
								{
									$is_applicable = true;
								}else # One user can use it only once
								{
									// echo "PPP<br>";
									# That is one user can use this only once. So check whether this promo is used by this customer earlier?
									$usr_used_pc = $this->Common->getData('used_promotions','id',array('promotion_id' =>$promo_cd[0]['id'] , 'user_id' => $tokenData->id));
									if(count($usr_used_pc) > 0)
									{
										// echo "SSSSS<br>";
										$is_applicable = false;
									}else
									{
										// echo "GGGGGG<br>";
										$is_applicable = true;
									}
								}
							}

							# 28JUL2021 : If promotion is for selfpickup then only selfpickup order type can avail this.
							if($is_applicable)
							{
								// echo "mmmmmm<br>";
								if($promo_cd[0]['level_id'] == 8)
								{
									if($order_type == 2)
									{
										$is_applicable = true;
									}else
									{
										$is_applicable = false;	
									}
								}else
								{
									// echo "rrrrrr<br>";
									$is_applicable = true;
								}
							}
						}else
						{
							// echo "TTTTTT<br>";
							$is_applicable = false;
						}

						if($is_applicable)
						{
							// echo "nnn<br>";
							$response = array();
							$level_id = $promo_cd[0]['level_id'];
							if($level_id == 1 || $level_id == 8)
							{
								// echo "WWWWWWW<br>";
								$response['delivery_charge_promotion'] = $promo_cd[0];
							}else
							{
								// echo "XXXXX<br>";
								$response['promocode_details'] = $promo_cd;
							}

							$data['status']		= 200;
			                $data['message']	= $this->lang->line('promo_success');
			                $data['data']		= $response;
						}else
						{
							// echo "kkkkkk<br>";
							$x = new stdClass();
							$data['status']		= 201;
			                $data['message']	= $this->lang->line('promo_code_not_valid');
			                $data['data']		= $x;
						}
	            	}else
	            	{
	            		// echo "jjjjjjj<br>";
	            		$x = new stdClass();
	            		$data['status']		= 201;
		                $data['message']	= $this->lang->line('promo_code_not_valid');
		                $data['data']		= $x;
	            	}
		    	}
        	}
            # Send Response 
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # check_and_apply_promo_code end

    # First we will call this function to check what is the payment option used by the customer.
    # Here we will return the payment response and if payment is successful then we will call record_transaction_details api
    public function place_order_post()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	$payment_mode = !empty($_POST['payment_mode'])?$this->db->escape_str($_POST['payment_mode']):'';
    		# 1 : Stripe 2 : Hitpay
			$total_amount = !empty($_POST['total_amount'])?$this->db->escape_str($_POST['total_amount']):''; # This is the amount which is to be paid through stripe (also exclude wallet used money)
			// $order_id = !empty($_POST['order_id'])?$this->db->escape_str($_POST['order_id']):''; # This is the amount which is to be paid through stripe (also exclude wallet used money)

			$check_restro_status = !empty($_POST['check_restro_status'])?$this->db->escape_str($_POST['check_restro_status']):'0';
			#check_restro_status is the key which tells us whether to check resturant status or not. Because this key is used while adding money to wallet also so that time there is no need to check the restaurant status
			# check_restro_status : 1 i.e. YES check status ELSE NO need to check

			// $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			# Here we will check whether already any idem key exists in Database. If so that means charge to stripe has been created but due some issue like network error we did not get response.
			// $idem_key = $this->Common->getData('idempotency_key','*','user_id = "'.$tokenData->id.'" AND amount = "'.$total_amount.'"');
			// if(count($idem_key) > 0)
			// {
			// 	$idem_key = $idem_key[0]['idem_key'];
			// }else
			// {
			// 	# That means this is first time charge to srtipe being added first time
			// 	$idem_key = substr(str_shuffle($str_result), 0, 120);
			// 	$this->Common->insertData('idempotency_key',array('amount' => $total_amount,'idem_key' => $idem_key , 'user_id' => $tokenData->id ,'created_at' => time() , 'updated_at' => time()));
			// }

			$last_txn_number = 'NA';
			# Here last txn number refers to the number that is like ch_. Here we will set NA first time

			if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($payment_mode ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('payment_mode_missing');
                $data['data']		=array();
            }else if($total_amount ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('grand_total_amount_missing');
                $data['data']		=array();
            }else if($payment_mode == 1) # STRIPE
            {
            	if($check_restro_status == '0') # No need to check resturant status (add_money_to_wallet may be triggered)
            	{
            		$rest_active_status = 1;
            	}else
            	{
	            	$check_cart = $this->Common->getData('cart','*',array('user_id' => $tokenData->id));
					if(!empty($check_cart))
					{
						$restaurant_id = $check_cart[0]['rest_id'];
					}
					# Check restaurant current active status
					$rest_active_status = $this->Common->getData('restaurants','id,rest_status','id = "'.$restaurant_id.'"');
					$rest_active_status = $rest_active_status[0]['rest_status'];
            	}

				if($rest_active_status == 1) # Restaurant is Active
				{
					$stripe_token = !empty($_POST['stripe_token'])?$this->db->escape_str($_POST['stripe_token']):'';
					if($stripe_token ==''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('stripe_token_missing');
		                $data['data']		=array();
		            }else
		            {
		            	# Stripe Payment
		            	# First we will check and handle exception
		            	$transaction_status = $this->process_payment($stripe_token,$tokenData->email,$total_amount,$idem_key);
		            	// echo "<pre>";
		            	// print_r($transaction_status);
		            	if($transaction_status)
		                {
		                	# Here check whether entry already exists then return the txn number (ch_)
		                	$last = $this->Common->getData('idempotency_key','*','idem_key = "'.$idem_key.'" AND user_id = "'.$tokenData->id.'"');
		                	if(count($last) > 0)
		                	{
		                		$last_txn_number = $last[0]['txn_number'];
		                	}
		                    if($transaction_status['status'] == 'error')
		                    {
		                        $response = array('received_status' => 'fail' , 'pay_response' => $transaction_status['msg'],'last_txn_number' => $last_txn_number);
		                        $data['status']		= 201;
					            $data['message']	= $this->lang->line('transaction_record_fail');
					            $data['data']		= $response;
		                    }else if($transaction_status['status'] == 'success')
		                    {
		                    	$response = array('received_status' => 'success' , 'pay_response' => $transaction_status['msg'],'last_txn_number' => $last_txn_number);
		                    	# Here we have now got the txn numbner (ch_) from stripe as the status is success so update the ch_ txn number to Database because we need to revert it to the mobile team when they call next api after getting duplicay error.
		                    	/*{
									"last_txn_number" = "ch_3JQA99EC6fe22WYg1vUS1r4j"; # THIS IS THE TXN_NUMBER
									"pay_response" = "Keys for idempotent requests can only be used with the same parameters they were first used with. Try using a key other than 'wa1tlO2Hz8u47iGZTUqQINL0pV5WRKg9hsrPnMdJeoCyvXkAFmxf63EDSBYcjb' if you meant to execute a different request.";
									"received_status" = fail;
									}*/
		                    	// $this->Common->updateData('idempotency_key' , array('txn_number' => $transaction_status['msg']),'idem_key = "'.$idem_key.'" AND user_id = "'.$tokenData->id.'"');
		                    	$data['status']		= 200;
					            $data['message']	= $this->lang->line('transaction_record_success');
					            $data['data']		= $response;
		                    }
		                }else
		                {
		                	$data['status']		= 201;
				            $data['message']	= $this->lang->line('transaction_record_fail');
				            $data['data']		= array();
		                }
		            }	
				}else
				{
					$data['status']		= 201;
		            $data['message']	= $this->lang->line('restaurant_not_active');
		            $data['data']		= array();
				}
			}else
            {
            	if($check_restro_status == '0') # No need to check resturant status (add_money_to_wallet may be triggered)
            	{
            		$rest_active_status = 1;
            	}else
            	{
	            	$check_cart = $this->Common->getData('cart','*',array('user_id' => $tokenData->id));
					if(!empty($check_cart))
					{
						$restaurant_id = $check_cart[0]['rest_id'];
					}
					# Check restaurant current active status
					$rest_active_status = $this->Common->getData('restaurants','id,rest_status','id = "'.$restaurant_id.'"');
					$rest_active_status = $rest_active_status[0]['rest_status'];
            	}

				if($rest_active_status == 1) # Restaurant is Active
				{
					# If payment mode is hitpay then we need to send the checout web page url to mobile team and then mobile team will take payment. Afer that mobile team will call another hit pay api which will return the status through payment id. If that status is true then only mobile team will call record_transaction_api to make DB entries
	        		$name = $tokenData->name;
					$email = $tokenData->email;
					$amount = $total_amount;
					$payFor = APP_NAME;

					$redirect_url = base_url().'api/';
					$transaction_status = $this->hitpay_create_payment($name, $email, $amount, $payFor);
					if($transaction_status != '' && is_array($transaction_status))
					{
						if(isSet($transaction_status['id']))
						{
							# Sample hit pay success response
							# SAMPLE RESPONSE
							/*Array
							(
							    [id] => 93535792-67c6-4cd6-995b-7e20ee7a1aa4
							    [name] => chanchal
							    [email] => chanchal.webvillee@gmail.com
							    [phone] => 
							    [amount] => 1.00
							    [currency] => SGD
							    [status] => pending
							    [purpose] => www.google.com
							    [reference_number] => 
							    [payment_methods] => Array
							        (
							            [0] => paynow_online
							        )

							    [url] => https://securecheckout.sandbox.hit-pay.com/payment-request/@demo/93535792-67c6-4cd6-995b-7e20ee7a1aa4/checkout
							    - - - - - - - - - - - - FOR LIVE THE URL WILL BE LIKE - - - - - - - - - - - - 
							    [url] => https://securecheckout.hit-pay.com/payment-request/@one-digital-it-solutions-pte-ltd/9353952a-7e8e-4907-a7d8-a899b9a1914a/checkout

							    [redirect_url] => 
							    [webhook] => 
							    [send_sms] => 1
							    [send_email] => 
							    [sms_status] => pending
							    [email_status] => pending
							    [allow_repeated_payments] => 
							    [expiry_date] => 
							    [created_at] => 2021-05-01T17:33:27Z
							    [updated_at] => 2021-05-01T17:33:27Z
							)
							# Sample hit pay error
							Array
							(
							    [message] => Invalid business api key.
							)
							*/
							$response = array('received_status' => 'success' , 'pay_response' => $transaction_status,'last_txn_number' => $last_txn_number);
	                    	$data['status']		= 200;
				            $data['message']	= $this->lang->line('transaction_record_success');
				            $data['data']		= $response;
							# So we have to send this response to mobile team
						}else
						{
							$response = array('received_status' => 'fail' , 'pay_response' => $transaction_status,'last_txn_number' => $last_txn_number);
	                    	$data['status']		= 201;
				            $data['message']	= $this->lang->line('transaction_record_fail');
				            $data['data']		= $response;
						}
					}else
					{
						$response = array('received_status' => 'fail' , 'pay_response' => $transaction_status,'last_txn_number' => $last_txn_number);
	                	$data['status']		= 201;
			            $data['message']	= $this->lang->line('transaction_record_fail');
			            $data['data']		= $response;
					}
				}else
				{
					$data['status']		= 201;
		            $data['message']	= $this->lang->line('restaurant_not_active');
		            $data['data']		= array();
				}
           	}
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # This function will be called by mobile team to check the payment status done through hitpay
    public function hitpay_payment_status_get()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	$payment_id = !empty($_GET['payment_id'])?$this->db->escape_str($_GET['payment_id']):'';

			if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($payment_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('payment_id_missing');
                $data['data']		=array();
            }else
            {
            	$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => "https://api.sandbox.hit-pay.com/v1/payment-requests/{$payment_id}", # HITPAY_SANDBOX
					// CURLOPT_URL => "https://api.hit-pay.com/v1/payment-requests/{$payment_id}", # HITPAY_LIVE
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => 0,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => array(
					  "content-type: application/x-www-form-urlencoded",
					  "X-BUSINESS-API-KEY:d8678689319e649d686925a6bf6c8b532ac1147ff2ab2f2a21bb14291f44b4c8",
					  'X-Requested-With: XMLHttpRequest',
					),
					# LIVE KEY a69306ae72ca3c5aad41cab4bb97424963ede65fbb8451b1c1a205b25ab71d30
					# SANDBOX d8678689319e649d686925a6bf6c8b532ac1147ff2ab2f2a21bb14291f44b4c8
				));
				
				$response = curl_exec($curl);
				$err = curl_error($curl);
				$result = $err ? false : json_decode($response,true);
				if(isset($result['id']))
				{
					# SUCCESS TO GET DATA
					$data['status']		= 200;
		            $data['message']	= $this->lang->line('payment_status_get_success');
		            $data['data']		= $result;
				}else
				{
					# SOMETHING FAILED
					$data['status']		= 201;
		            $data['message']	= $this->lang->line('payment_status_get_fail');
		            $data['data']		= $result;
				}
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # record transaction detail function start
    # This function is used to make entries to the transaction table for record
    public function record_transaction_details_post()
    {
    	try{

    		$payment_mode = !empty($_POST['payment_mode'])?$this->db->escape_str($_POST['payment_mode']):''; # 1 : Stripe 2 : Hitpay 3 : Paying completely from wallet
    		# HITPAY_CHANGE
    		# For hitpay first record transaction will be called then place order will be called hence we are sending NA in such case
    		$transaction_unique_number = !empty($_POST['transaction_unique_number'])?$this->db->escape_str($_POST['transaction_unique_number']):'NA'; # returned from payment gateway
    		# 1 : Stripe 2 : Hitpay
    		// $total_amount = !empty($_POST['total_amount'])?$this->db->escape_str($_POST['total_amount']):'';
    		$total_amount = $_POST['total_amount']; # This is the amount which is to be paid through stripe (also exclude wallet used money)
    		$delivery_charges = $_POST['delivery_charge_paid'];
    		$is_wallet_used = !empty($_POST['is_wallet_used'])?$this->db->escape_str($_POST['is_wallet_used']):''; #1 - Yes 2 - No
    		$wallet_debited_value = $_POST['wallet_debited_value']; # Pass 0 if not used
    		$restaurant_id = !empty($_POST['restaurant_id'])?$this->db->escape_str($_POST['restaurant_id']):'';
    		$ordered_product_id_and_qty_and_price = !empty($_POST['id_qty_price'])?$_POST['id_qty_price']:''; # [{"id":"2","qty":"3","price":"16"},{"id":"1","qty":"2","price":"16"},{"id":"3":"qty":"16","price":"16"}]
    		
    		# Below variant is not mendatory that every product has this so we are passing 0 in else and not placing any mendatory check for this
    		$ordered_product_id_and_varid_and_vartypeid = !empty($_POST['id_varid_vartype_id'])?$_POST['id_varid_vartype_id']:'0'; # [{"id":"2","var_id":"3","var_type_id":"5","price":"13"},{"id":"2","var_id":"3","var_type_id":"6","price":"15"}]

    		# DELIVERY ADDRESS DETAIL
    		$delivery_address = !empty($_POST['delivery_address'])?$this->db->escape_str($_POST['delivery_address']):'';
    		# Actual delivery charges applied (after promo code if any)
			$delivery_latitude = !empty($_POST['delivery_latitude'])?$this->db->escape_str($_POST['delivery_latitude']):'';
			$delivery_longitude = !empty($_POST['delivery_longitude'])?$this->db->escape_str($_POST['delivery_longitude']):'';
			$delivery_name = !empty($_POST['delivery_name'])?$this->db->escape_str($_POST['delivery_name']):'';
			$delivery_email = !empty($_POST['delivery_email'])?$this->db->escape_str($_POST['delivery_email']):'';
			$delivery_mobile = !empty($_POST['delivery_mobile'])?$this->db->escape_str($_POST['delivery_mobile']):'';

			$unit_number = !empty($_POST['unit_number'])?$this->db->escape_str(trim($_POST['unit_number'])):'';
	    	$street_address = !empty($_POST['street_address'])?$this->db->escape_str(trim($_POST['street_address'])):'';
	    	$postal_code = !empty($_POST['postal_code'])?$this->db->escape_str(trim($_POST['postal_code'])):'';

			# Below three values will be stored locally by the mobile team and they will pass the data
			$order_type = !empty($_POST['order_type'])?$this->db->escape_str($_POST['order_type']):''; #  `rest_accept_types table` 1 : Order now 2 : self pickup 3 : Order for later 4 : Dine In
			$pickup_time_from = !empty($_POST['pickup_time_from'])?$this->db->escape_str($_POST['pickup_time_from']):''; # 11-12 so taking two timestamp
			$pickup_time_to = !empty($_POST['pickup_time_to'])?$this->db->escape_str($_POST['pickup_time_to']):'';
			
			$business_category = !empty($_POST['business_category'])?$this->db->escape_str($_POST['business_category']):''; #merchant_categories table
			$is_cutlery_needed = !empty($_POST['is_cutlery_needed'])?$this->db->escape_str($_POST['is_cutlery_needed']):''; #1 - YES 2 - No
			$sub_total = !empty($_POST['sub_total'])?$this->db->escape_str($_POST['sub_total']):''; # Item total
			$item_quantity = !empty($_POST['item_quantity'])?$this->db->escape_str($_POST['item_quantity']):'';
			$remark = !empty($_POST['remark'])?$this->db->escape_str($_POST['remark']):'NA';

			$promo_subtotal_is_applied = !empty($_POST['promo_subtotal_is_applied'])?$this->db->escape_str($_POST['promo_subtotal_is_applied']):''; #   Is any promotion auto applied on item total so 1 - YES and 2 - NO
			$promo_subtotal_code_id = $_POST['promo_subtotal_code_id']; # primary id of the promo code which is applied
			$promo_subtotal_discounted_value = $_POST['promo_subtotal_discounted_value']; #Discounted Value on subtotal as per the promo applied (if any) : Pass 0 if not applied
			$promo_dc_is_applied = !empty($_POST['promo_dc_is_applied'])?$this->db->escape_str($_POST['promo_dc_is_applied']):''; #i.e. Is any promotion auto applied on delivery charges so 1 - YES and 2 - No
			$promo_dc_code_id = $_POST['promo_dc_code_id']; # primary id of the promo code which is applied
			$promo_dc_discounted_value = $_POST['promo_dc_discounted_value']; # Discounted Value on DC as per the promo applied (if any) : Pass 0 if not applied

			/* LALAMOVE RELATED DETAILS */
			$track_link = !empty($_POST['track_link'])?$_POST['track_link']:'NA';
			$lalamove_order_id = !empty($_POST['lalamove_order_id'])?$this->db->escape_str($_POST['lalamove_order_id']):'NA';
			$lalamove_order_status = !empty($_POST['lalamove_order_status'])?$this->db->escape_str($_POST['lalamove_order_status']):'NA'; # 1 Pass 2 Fail 3 Not for lalamove
			$lalamove_order_failed_reason = !empty($_POST['lalamove_order_failed_reason'])?$this->db->escape_str($_POST['lalamove_order_failed_reason']):'NA';
			$actual_dc_amount = !empty($_POST['actual_dc_amount'])?$this->db->escape_str($_POST['actual_dc_amount']):0; #Actual delivery charge returned from lalamove
			/* LALAMOVE RELATED DETAILS */
			
			$ordering_platform = !empty($_POST['ordering_platform'])?$this->db->escape_str($_POST['ordering_platform']):''; # (1 for iOS and 2 for android)

    		$tokenData = $this->verify_request();

    		if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	= $this->lang->line('unauthorized_access');
            }else if($transaction_unique_number==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('transaction_number_missing');
                $data['data']		=array();
            }
            // else if($total_amount==''){
            //     $data['status']		=201;
            //     $data['message']	=$this->lang->line('grand_total_amount_missing');
            //     $data['data']		=array();
            // }
            else if($is_wallet_used==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('is_wallet_used_missing');
                $data['data']		=array();
            }else if($wallet_debited_value==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('wallet_debited_value_missing');
                $data['data']		=array();
            }else if($delivery_charges==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('delivery_charges_missing');
                $data['data']		=array();
            }else if($ordered_product_id_and_qty_and_price==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('product_price_qty_missing');
                $data['data']		=array();
            }else if($restaurant_id ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('rest_id_missing');
			    $data['data']		=array();
			}else if($delivery_address ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('delivery_address_missing');
			    $data['data']		=array();
			}else if($delivery_latitude ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('delivery_latitude_missing');
			    $data['data']		=array();
			}else if($delivery_longitude ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('delivery_longitude_missing');
			    $data['data']		=array();
			}
			// else if($delivery_name ==''){
			//     $data['status']		=201;
			//     $data['message']	=$this->lang->line('delivery_name_missing');
			//     $data['data']		=array();
			// }
			else if($delivery_email ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('delivery_email_missing');
			    $data['data']		=array();
			}else if($delivery_mobile ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('delivery_mobile_missing');
			    $data['data']		=array();
			}else if($order_type ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('order_type_missing');
			    $data['data']		=array();
			}else if($business_category ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('business_category_missing');
			    $data['data']		=array();
			}else if($payment_mode ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('payment_mode_missing');
			    $data['data']		=array();
			}else if($is_cutlery_needed ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('is_cutlery_needed_missing');
			    $data['data']		=array();
			}else if($sub_total ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('sub_total_missing');
			    $data['data']		=array();
			}else if($item_quantity ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('item_quantity_missing');
			    $data['data']		=array();
			}else if($promo_subtotal_is_applied ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('promo_subtotal_is_applied_missing');
			    $data['data']		=array();
			}else if($promo_subtotal_code_id ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('promo_subtotal_code_id_missing');
			    $data['data']		=array();
			}else if($promo_subtotal_discounted_value ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('promo_subtotal_discounted_value_missing');
			    $data['data']		=array();
			}else if($promo_dc_is_applied ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('promo_dc_is_applied_value_missing');
			    $data['data']		=array();
			}else if($promo_dc_code_id ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('promo_dc_code_id_value_missing');
			    $data['data']		=array();
			}else if($promo_dc_discounted_value ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('promo_dc_discounted_value_value_missing');
			    $data['data']		=array();
			}else if($delivery_charges ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('delivery_charge_paid_missing');
			    $data['data']		=array();
			}else if($remark ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('remark_missing');
			    $data['data']		=array();
			}else if($track_link ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('track_link_missing');
			    $data['data']		=array();
			}else if($lalamove_order_id ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('lalamove_order_id_missing');
			    $data['data']		=array();
			}else if($lalamove_order_status ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('lalamove_order_status_missing');
			    $data['data']		=array();
			}else if($lalamove_order_failed_reason ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('lalamove_order_failed_reason_missing');
			    $data['data']		=array();
			}
			// else if($unit_number == ''){
   //              $data['status']		=201;
   //              $data['message']	=$this->lang->line('unit_number_missing');
   //              $data['data']		=array();
   //          }
            # MAKING THIS AS NON MANDATORY BECAUSE OLD CUSTOMERS ON LIVE ARE HAVING ISSUE
            // else if($street_address == ''){
            //     $data['status']		=201;
            //     $data['message']	=$this->lang->line('street_address_missing');
            //     $data['data']		=array();
            // }
            else if($postal_code == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('postal_code_missing');
                $data['data']		=array();
            }
			/*else if($pickup_time_from ==''){ removing this check because for order now 0 will be passed in these both keys
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('pickup_time_from_missing');
			    $data['data']		=array();
			}else if($pickup_time_to ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('pickup_time_to_missing');
			    $data['data']		=array();
			}*/
			else
            {
            	# First check whether the restaurant is active or not
            	$rest_active_status = $this->Common->getData('restaurants','id,rest_status','id = "'.$restaurant_id.'"');
            	$rest_active_status = $rest_active_status[0]['rest_status'];

            	if($rest_active_status == 1)
            	{
            		#$idem_key delete
            		# DELETE THE KEY THEN
            		# OTHER DEPENDENT API LIKE update_paid_outstanding_amount and add_money_to_wallet and record_transaction_details we need to delete the created entry of idempotency key from the Database for this User.
            		// $this->Common->deleteData('idempotency_key','user_id = "'.$tokenData->id.'"');

            		# If payment is success then proceed with DB Entries
	            	# That means payment is successful so we can proceed to make Database entries
	            	# First entry to transaction table with order number as blank
	            	$insert_txn_table = [
	            		'user_id' => $tokenData->id,
	            		'restaurant_id' => $restaurant_id,
	            		'order_id' => '',
	            		'number' => $transaction_unique_number,
	            		'mode' => $payment_mode,
	            		'total_amount_paid' => $total_amount,
	            		'delivery_charge_paid' => $delivery_charges,
	            		'is_wallet_used' => $is_wallet_used,
	            		'wallet_debited_value' => $wallet_debited_value,
	            		'txn_date' => time(),
	            		'txn_status' => 1,
	            		'created_at' => time(),
	            		'updated_at' => time()
	            	];
	            	$txn_table_id = $this->Common->insertData('transactions',$insert_txn_table);
	            	$response['transaction_id'] = $txn_table_id;

	            	# Next , clear cart
	            	# Also we need to clear the cart for this user
	            	# This will also clear data from cart variant table
	            	$this->Common->deleteData('cart',array('user_id' => $tokenData->id));

	            	# Next make entry to ORDER TABLE
	            	# For this we need many information so whatever we have as of now I am sending them
	            	# Delivery address details

	            	# get value for delivery_handled_by	, admin_commission , restaurant_commission and preparation time
	            	$some_rest_data = $this->Common->getData('restaurants','admin_id,delivery_handled_by,preparation_time','id = "'.$restaurant_id.'"');
	            	$delivery_handled_by = $some_rest_data[0]['delivery_handled_by'];
	            	if($some_rest_data[0]['preparation_time'] != '' || $some_rest_data[0]['preparation_time'] != 0)
	            	{
	            		$preparation_time_when_ordered = $some_rest_data[0]['preparation_time'];
	            	}else
	            	{
	            		$prep_time = $this->Common->getData('settings','value','name = "basic_preparation_time"');
	            		$preparation_time_when_ordered = $prep_time[0]['value'];
	            	}

	            	$prep = $this->Common->getData('settings','value','name = "kerela_eats_commission" OR name = "restaurant_commission"');

	            	$commission = $this->Common->getData('settings','value','name = "kerela_eats_commission" OR name = "restaurant_commission"');
	            	$kerela_eats_commission = $commission[0]['value'];
	            	$restaurant_commission = $commission[1]['value'];

	            	if($total_amount == 0)
	            	{
	            		# That means wallet is used to make payment i.e. no payment gateway used for making payment only wallet used. So we will send wallet_debited_value to total_amount in orders table only.
	            		$total_amount = $wallet_debited_value;
	            		# Also we need to dedcut this value from user's wallet
	            	}
	            	$paid_status = 1; # PAID
	            	if($payment_mode == 2) # HITPAY_CHANGE then paid_status will be 0
	            	{
	            		$paid_status = 0;
	            	}

	            	$insert_order_table = [
	            		'user_id' => $tokenData->id,
	            		'restaurant_id' => $restaurant_id,
	            		'lalamove_order_id' => $lalamove_order_id,
	            		'promo_subtotal_is_applied' => $promo_subtotal_is_applied,
	            		'promo_subtotal_code_id' => $promo_subtotal_code_id,
	            		'promo_subtotal_discounted_value' => $promo_subtotal_discounted_value,
	            		'promo_dc_is_applied' => $promo_dc_is_applied,
	            		'promo_dc_code_id' => $promo_dc_code_id,
	            		'promo_dc_discounted_value' => $promo_dc_discounted_value,
	            		'order_type' => $order_type,
	            		'pickup_time_from' => $pickup_time_from,
	            		'pickup_time_to' => $pickup_time_to,
	            		'delivery_handled_by' => $delivery_handled_by,
	            		'admin_commission' => $kerela_eats_commission,
	            		'restaurant_commission' => $restaurant_commission,
	            		'business_category' => $business_category,
	            		'preparation_time_when_ordered' => $preparation_time_when_ordered,
	            		'payment_mode' => $payment_mode,
	            		'is_cutlery_needed' => $is_cutlery_needed,
	            		'order_status' => 0,
	            		'paid_status' => $paid_status,
	            		'total_amount' => $total_amount,
	            		'dc_amount' => $delivery_charges,
	            		'sub_total' => $sub_total,
	            		'item_quantity' => $item_quantity,
		            	'track_link' => $track_link,
		            	'delivery_address' => $delivery_address,
		            	'delivery_latitude' => $delivery_latitude,
		            	'delivery_longitude' => $delivery_longitude,
		            	'delivery_name' => $delivery_name,
		            	'delivery_email' => $delivery_email,
		            	'delivery_mobile' => $delivery_mobile,
		            	'delivery_unit_number' => $unit_number,
		            	'delivery_street_address' => $street_address,
		            	'delivery_postal_code' => $postal_code,
		            	'lalamove_order_status' => $lalamove_order_status,
		            	'lalamove_order_failed_reason' => $lalamove_order_failed_reason,
		            	'actual_dc_amount' => $actual_dc_amount,
		            	'ordering_platform' => $ordering_platform,
	            		'remark' => $remark,
		            	'created_at' => time(),
		            	'updated_at' => time(),
	            	];

	            	$order_id = $this->Common->insertData('orders',$insert_order_table);
	            	$response['order_id'] = $order_id;
	            	# Generate Auto imcrement order number and update	

	            	$order_start = 10000; # Static value and it will be added by the last Id in increasing way
	                $sr_order_number = $order_start + $order_id;
	                $sr_order_number = 'KE'.$sr_order_number.'';

	            	/* LALAMOVE PART AGAIN HERE START */
	            	$some_checks = $this->Common->getData('restaurants','admin_id,delivery_handled_by,per_km_charge,business_type AS business_category,food_type','id = "'.$restaurant_id.'"');

	            	$response['lalamove_order_reference_id'] = 'NAA';
					$response['lalamove_order_failed_reason'] = 'NAA';
					$response['lalamove_order_status'] = 'NAA';
	        		$response['lalamove_order_amount'] = 'NAA';
	        		$response['lalamove_track_link'] = 'NAA';
	            	/* LALAMOVE PART AGAIN HERE END */

	            	$update_array=[
	                    'order_number' => $sr_order_number
	                ];
	                // echo "<pre> Print update array";
	                // print_r($update_array);
	                
	                $this->Common->updateData('orders',$update_array , 'id = "'.$order_id.'"');
	                $response['order_number'] = $sr_order_number;

	                # Now as we have order id so update order_id in txn table also
	        		$this->Common->updateData('transactions',array('order_id' => $order_id) , 'id = "'.$txn_table_id.'"');

	        		# Make entry to order_products_table
	        		$ordered_product_id_and_qty_and_price = stripslashes($ordered_product_id_and_qty_and_price); // added to strip slashes
	        		$someArray = json_decode($ordered_product_id_and_qty_and_price);
	        		foreach ($someArray as $detail) 
	            	{
	            		$this->Common->insertData('order_product_details',array('product_id' => $detail->id,'product_name' => $detail->name,'product_quantity'=>$detail->qty,'order_id' => $order_id,'product_unit_price' => $detail->price ,'created_at' => time() , 'updated_at' => time()));
	            	}

	            	if($ordered_product_id_and_varid_and_vartypeid != '0')
	            	{
	            		# [{"id":"2","var_id":"3","var_type_id":"5","price":"13"},{"id":"2","var_id":"3","var_type_id":"6","price":"15"}]
	            		# SO Make entry for VARIANTS in order_product_variant_details table
	            		$ordered_product_id_and_varid_and_vartypeid = stripslashes($ordered_product_id_and_varid_and_vartypeid); // added to strip slashes
	            		
			    		$someArray = json_decode($ordered_product_id_and_varid_and_vartypeid);
			    		foreach ($someArray as $detail) 
			        	{
			        		$this->Common->insertData('order_product_variant_details',array('product_id' => $detail->id,'variant_id'=>$detail->var_id,'variant_name'=>$detail->name,'variant_type_name'=>$detail->type_name,'variant_type_id'=>$detail->var_type_id,'order_id' => $order_id,'variant_price' => $detail->price ,'created_at' => time() , 'updated_at' => time()));
			        	}
	            	}

	        		# DC and subtotal promo check
	        		# Checking promo on DELIVERY CHARGE 
	        		if($promo_dc_is_applied == 1)
	        		{
	        			$insert_used_dc_promo = [
	            			'promotion_id' => $promo_dc_code_id,
	            			'user_id' => $tokenData->id,
	            			'order_number' => $sr_order_number,
	            			'availed_on' => time(),
	            		];

	            		$this->Common->insertData('used_promotions',$insert_used_dc_promo);
	        			# We also need to update the no of used count if promo is used in promotion table
	        			$no_of_used = $this->Common->getData('promotions','promo_used_times','id = "'.$promo_dc_code_id.'"');
	        			$no_of_used = $no_of_used[0]['promo_used_times'];
	        			$no_of_used = $no_of_used + 1;

	        			$this->Common->updateData('promotions',array('promo_used_times' => $no_of_used) , 'id = "'.$promo_dc_code_id.'"');
	        		}


	        		# If any promo on SUBTOTAL is auto applied and user also tries to write a promo and apply it using apply button and if promo is valid then auto applied promo will be rejected.

	        		if($promo_subtotal_is_applied == 1)
	        		{
	        			$insert_used_subtotal_promo = [
	            			'promotion_id' => $promo_subtotal_code_id,
	            			'user_id' => $tokenData->id,
	            			'order_number' => $sr_order_number,
	            			'availed_on' => time(),
	            		];

	            		$this->Common->insertData('used_promotions',$insert_used_subtotal_promo);

	            		# We also need to update the no of used count if promo is used in promotion table
	        			$no_of_used = $this->Common->getData('promotions','promo_used_times','id = "'.$promo_subtotal_code_id.'"');
	        			$no_of_used = $no_of_used[0]['promo_used_times'];
	        			$no_of_used = $no_of_used + 1;

	        			$this->Common->updateData('promotions',array('promo_used_times' => $no_of_used) , 'id = "'.$promo_subtotal_code_id.'"');	
	        		}


	        		# Check if wallet is used then make an entry to wallet table for a DEBIT ENTRY
	        		if($is_wallet_used == 1) # YES
	        		{
	        			$insert_wallet = [
	        				'user_id' => $tokenData->id,
	        				'order_id' => $order_id,
	        				'wallet_date' => time(),
	        				'debited' => $wallet_debited_value,
	        				'credited' => 0,
	        				'type' => 3,
	        				'added_by' => 0,
	        				'valid_till' => 0,
	        				'created_at' => time(),
	        				'updated_at' => time(),
	        			];
	        			$this->Common->insertData('wallet', $insert_wallet);
	        		}

	        		# Make Mendatory entry for order cashback
	        		# But now cashback will be done from admin side when ADMIN accepts the order

	        		# Send notification code To customer for order placing start
	        		$token = $this->Common->getData('users','device_token','id='.$tokenData->id);
	        		$token = $token[0]['device_token'];
	        		$notification_data_fields = array(
	                    'message' => 'Order placed successfully',
	                    'title' => NOTIFICATION_TITLE_PLACED,
	                    # 0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed
	                    'order_status'=> 0,
	                    'order_number'=> $sr_order_number,
	                    'order_id'=> $order_id,
	                    'notification_type' => 'ORDER_STATUS_UPDATED'
	                );
	                if($token != "")
	                {
	                  sendPushNotification($token, $notification_data_fields,IOS_BUNDLE_ID_CUSTOMER);
	                }

	                # Now insert notification to Database
	                $insertData = [
	                	'title' => "Order ".$sr_order_number." ".$this->lang->line('order_placed'),
	                	'to_user_id' => $tokenData->id,
	                	'type' => 1,
	                	'order_id' => $order_id,
	                	'is_read' => 1,
	                	'created_at' => time(),
	                	'updated_at' => time(),
	                ];
	                $this->Common->insertData('notifications',$insertData);

	                # Also we need to send notofication to MERCHANT ALSO
	                $restro_user_id = $some_rest_data[0]['admin_id'];
	                $merchant_token = $this->Common->getData('users','device_token','id='.$restro_user_id);
	        		$merchant_token = $merchant_token[0]['device_token'];
	        		$merchant_notification_data_fields = array(
	                    'message' => 'New Order received',
	                    'title' => ORDER_RECEIVED,
	                    # 0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed
	                    'order_status'=> 0,
	                    'order_number'=> $sr_order_number,
	                    'order_id'=> $order_id,
	                    'notification_type' => 'ORDER_STATUS_UPDATED'
	                );
	                # Only in this case we need to send alarm sound . So last param is sending as 1 that means Yes play alarm sound
	                if($merchant_token != "")
	                {
	                  sendPushNotification($merchant_token, $merchant_notification_data_fields,IOS_BUNDLE_ID_MERCHANT,'1');
	                }

	                # Now insert notification to Database FOR MERCHANT ID
	                $insertData = [
	                	'title' => "Order ".$sr_order_number." ".$this->lang->line('order_placed'),
	                	'to_user_id' => $restro_user_id,
	                	'type' => 1,
	                	'order_id' => $order_id,
	                	'is_read' => 1,
	                	'created_at' => time(),
	                	'updated_at' => time(),
	                ];
	                $this->Common->insertData('notifications',$insertData);
	               	
	               	# Now we need to send Browser notification to admin
	                # Notification will be sent to super admin (Role 1) in all case but for sending notification to merchant (Role 2) we need to check the restuarant id to which the order is placed. Then we need to take device token only for that merchant.

	               	// $tokens = $this->Common->getData('users','device_token',"role = 1 AND device_token != ''");
	               	$tokens_data = $this->Common->getData('users','id,device_token',"role IN(1,2) AND status = 1 AND device_token != ''");

	               	# Here we got the data of super admin and merchant.
	               	# Now if role is 2 then what is the merchant's restaurant id?
                    if(!empty($tokens_data))
                    {
                        foreach($tokens_data as $tk)
                        {
                        	$token = '';
                        	if($tk['role'] == 2) # MERCHANT
                        	{
                        		$rest_id = $this->Common->getData('restaurants','id','admin_id = "'.$tk['id'].'"');
                        		$rest_id = $rest_id[0]['id'];
                        		if($rest_id == $restaurant_id) # That is merchant's restro AND the resto to which order is placed matches so get its token in array else no
                        		{
                            		$token = $tk['device_token'];
                        		}
                        	}else # This is SUPER ADMIN so no need to check for restaurant Id
                        	{
                        		$token = $tk['device_token'];
                        	}
                            
                            if($token!="")
                            {
                               	/** Google URL with which notifications will be pushed */
								$url = "https://fcm.googleapis.com/fcm/send";
								/** 
								 * Firebase Console -> Select Projects From Top Naviagation 
								 *      -> Left Side bar -> Project Overview -> Project Settings
								 *      -> General -> Scroll Down and you will be able to see KEYS
								 */
								$subscription_key  = "key=AAAA2_hNI7U:APA91bE0SzhrQI_-XTDRodGvsJ_PBx8dOHTI-J4WLBi_75KSZW1E0SOycOffQVvsXXYT8dwIhu_vuq9yvTY2VrH0D4Lk7LBqNwRRrfVK5n-fKNVhDhWc9ewTq-ozKsxyX2Dz_VJbBF8b";

								/** We will need to set the following header to make request work */
								$request_headers = array(
								    "Authorization:" . $subscription_key,
								    "Content-Type: application/json"
								);

								/** Data that will be shown when push notifications get triggered */
								$postRequest = [
								    "to" =>  $token,
								    "notification" => [
								        "title" =>  "New order received",
								        "body" =>  "\r\nA new order has been placed.",
								        "icon" =>  base_url('assets/img/favicon.png'),
								        "click_action" =>  base_url('admin/orders')
								    ],
								    /** Customer Token, As of now I got from console. You might need to pull from database */
								];

								/** CURL POST code */
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $url);
								curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

								$season_data = curl_exec($ch);

								if (curl_errno($ch)) {
								    print "Error: " . curl_error($ch);
								    exit();
								}
								// Show me the result
								curl_close($ch);
								$json = json_decode($season_data, true);
                            }
                        }
                    }

	                # Send Notification Complete

	                # HITPAY_CHANGE
	                # NOW IF $payment_mode is HITPAY then we will add order amount as an outstanding amount for this order
	                if($payment_mode == 2) # Hitpay
	                {
	                	$update_order_for_outstanding = [
	                		'outstanding_amount' => $total_amount,
							'who_will_pay_outstanding_amount' => 3,//2- restaurant will pay to customer, 3 - customer will pay to restaurant,default 0
							'is_paid_outstanding_amount' => 0,
	                	];
	                	$this->Common->updateData('orders',$update_order_for_outstanding , 'id = "'.$order_id.'"');
	                }

	                $data['status']		= 200;
		            $data['message']	= $this->lang->line('order_placed');
		            $data['data']		= $response;

	                # send notification code end
            	}else
            	{
            		$data['status']		= 201;
		            $data['message']	= $this->lang->line('restaurant_not_active');
		            $data['data']		= array();
            	}
            }
    		$this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # record transaction detail function end

    # HITPAY_CHANGE If HITPAY was used and now payment through hitpay is success then mobile team will call this api to update the transaction number and to deduct the outstanding amount added while placing order
    public function update_order_after_hitpay_success_post()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	$order_id = !empty($_POST['order_id'])?$this->db->escape_str($_POST['order_id']):'';
	    	$transaction_number = !empty($_POST['transaction_number'])?$this->db->escape_str($_POST['transaction_number']):'';
	    	// $order_amount = !empty($_POST['order_amount'])?$this->db->escape_str($_POST['order_amount']):'';

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($order_id == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('order_id_missing');
                $data['data']		=array();
			}else if($transaction_number == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('transaction_number_missing');
                $data['data']		=array();
			}
			else
            {

            	$update_array = [
            		'outstanding_amount' => '0.00',
					'who_will_pay_outstanding_amount' => 0,//2- restaurant will pay to customer, 3 - customer will pay to restaurant,default 0
					'is_paid_outstanding_amount' => 1,
					'paid_status' => 1, # PAID
            	];
            	$this->Common->updateData('orders',$update_array , 'id = "'.$order_id.'"');

            	# update transaction number
            	$update_array_txn = [
            		'number' => $transaction_number
            	];
            	$this->Common->updateData('transactions',$update_array_txn , 'order_id = "'.$order_id.'"');

            	$x = new stdClass();
        		$data['status']		=200;
                $data['message']	=$this->lang->line('success');
                $data['data']		=$x;
            	
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

    # This function is used to prcees the payment through stripe and to create charge
    private function process_payment($token,$user_email,$amount,$idem_key)
    {
		$chargeID = 0 ;
		// echo "amount is : ".ceil($amount*100);
	    if($token != "")
	    {
	        require_once(APPPATH.'libraries/stripe-php/init.php');
	        $stripe_api_key = $this->config->item('stripe_secret');
	        // $unique = substr(md5(time()), 0, '255'); // Idempotency keys can be up to 255 characters long.
	        // $uniqueID = random_strings(120); // Idempotency keys can be up to 255 characters long.
	        \Stripe\Stripe::setApiKey($stripe_api_key);
	        // echo "unique id is : ".$uniqueID;
	       	try 
	       	{
	       		$user = \Stripe\Customer::create(array(
		            'email' => $user_email, // member email id
		            'card'  => $token
		        ));

	       		// echo "Printing  users <pre>";
	       		// print_r($user);

		        // $charge = \Stripe\Charge::create(array(
		        //     'customer'  => $user->id,
		        //     'amount'    => $amount*100, # *100 is used because stripe accepts value in cents so we convert tha amount to cent by multiplying by *100.
		        //     'currency'  => 'SGD' // Singapore dollor
		        // ));

		  //       $charge = \Stripe\Charge::create([
				//     'customer'  => $user->id,
		  //           'amount'    => $amount*100,
		  //           'currency'  => 'SGD' // Singapore dollor
				// 	],
				// 	[
				// 		'idempotency_key' => $idem_key
				// 	]
				// );
				$charge = \Stripe\Charge::create([
				    'customer'  => $user->id,
		            'amount'    => $amount*100,
		            'currency'  => 'SGD' // Singapore dollor
					]
				);
		        // echo "PRINT CHARGE <pre>";
		        // print_r($charge);
		        if($charge->paid == true) 
		        {
		        	$chargeId = $charge->id;
		            return array('status' => 'success' , 'msg' => $chargeId);
		        }else
		        {
		       		return array('status' => 'error' , 'msg' => 'fail'); 	
		        }
	       	}catch(Exception $e)
	       	{
	           return array('status' => 'error' , 'msg' => $e->getMessage());
	       	}
	    }
	}

	# This funciton is used to return the intent key to the  mobile team
	public function process_payment_threeds_post()
    {
    	try
    	{
    		$total_amount = !empty($_POST['total_amount'])?$this->db->escape_str($_POST['total_amount']):'';
	    	$tokenData = $this->verify_request();

			if($tokenData === false){
	            $status = parent::HTTP_UNAUTHORIZED;
	            $data['status']	 = $status;
	            $data['message']	= $this->lang->line('unauthorized_access');
	        }else if($total_amount ==''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('grand_total_amount_missing');
	            $data['data']		=array();
	        }
	        else
	        {
		    	try 
		    	{
		    		require_once(APPPATH.'libraries/stripe-php/init.php');
			        $stripe_api_key = $this->config->item('stripe_secret');
			        $stripe_pk_key = $this->config->item('stripe_key');
			        \Stripe\Stripe::setApiKey($stripe_api_key);

			        /* OLD CODE 
			        	$customer = \Stripe\Customer::create(array(
			            'email' => $tokenData->email
			        ));

			        $ephemeralKey = \Stripe\EphemeralKey::create(
					  ['customer' => $customer->id],
					  ['stripe_version' => '2018-07-27']
					);

					$paymentIntent = \Stripe\PaymentIntent::create([
					    'amount' => $total_amount * 100,
					    'currency' => 'SGD',
					    'customer' => $customer->id
					]);
					$output = [
					    'clientSecret' => $paymentIntent->client_secret,
					    'publishableKey' => $stripe_pk_key,
					    'ephemeralKey' => $ephemeralKey->secret,
					    'customer' => $customer->id,
					];
					*/

					# NEW CODE

					# SAVECARDCHANGE
			   		# If this customer is not created on stripe then only create customer over stripe

			   		$check_stripe_id = $this->Common->getData('users','stripe_id','id = "'.$tokenData->id.'"');
			   		// print_r($check_stripe_id);die;
			   		if(isset($check_stripe_id[0]['stripe_id']) && $check_stripe_id[0]['stripe_id'] != '')
			   		{
			   			$cust_id = $check_stripe_id[0]['stripe_id'];
			   		}else
			   		{
			   			$customer = \Stripe\Customer::create(array(
			            	'email' => $tokenData->email
			        	));
			   			$cust_id = $customer->id;
						$this->Common->updateData('users',array('stripe_id' => $cust_id), 'id = "'.$tokenData->id.'"');
			   		}

			   		// print_r($cust_id);die;
					$ephemeralKey = \Stripe\EphemeralKey::create(
					  	['customer' => $cust_id],
					  	['stripe_version' => '2018-07-27']
					);

					$paymentIntent = \Stripe\PaymentIntent::create([
					    'amount' => $total_amount * 100,
					    'currency' => 'SGD',
					    'customer' => $cust_id
					]);

					$output = [
					    'clientSecret' => $paymentIntent->client_secret,
					    'publishableKey' => $stripe_pk_key,
					    'ephemeralKey' => $ephemeralKey->secret,
					    'customer' => $cust_id,
					];

					$data['status']		= 200;
		            $data['message']	= 'Data get Successfully';
		            $data['data']		= $output;

				} catch (Error $e) {
				  // http_response_code(500);
				  return array('status' => 'error' , 'msg' => $e->getMessage());
				}
				$this->response($data, $data['status']);
	        }
		} catch (\Exception $e) {
	        //make error log
	        log_message('error', $e);

	        $data['status']		=500;
	        $data['message']	=$this->lang->line('internal_server_error').$e;
	        $data['data']		=array(); 

	        $this->response($data, $data['status']);
	    }
	}

	# Hitpay payment create
	private function hitpay_create_payment($name, $email, $amount, $payFor)
	{
		$redirect_url = base_url('Hitpay_redirect');
		$webhook_base_url = base_url('api/success_webhook');
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sandbox.hit-pay.com/v1/payment-requests/", # SANDBOX
			// CURLOPT_URL => "https://api.hit-pay.com/v1/payment-requests/", # LIVE
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "name={$name}&email={$email}&amount={$amount}&currency=SGD&purpose={$payFor}&redirect_url=$redirect_url&webhook=$webhook_base_url",
			CURLOPT_HTTPHEADER => array(
			  "content-type: application/x-www-form-urlencoded",
			  "X-BUSINESS-API-KEY:d8678689319e649d686925a6bf6c8b532ac1147ff2ab2f2a21bb14291f44b4c8", // YOUR_API_KEY
			  'X-Requested-With: XMLHttpRequest',
			),
			# LIVE KEY a69306ae72ca3c5aad41cab4bb97424963ede65fbb8451b1c1a205b25ab71d30
			# SANDBOX d8678689319e649d686925a6bf6c8b532ac1147ff2ab2f2a21bb14291f44b4c8
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		$result = $err ? $err : json_decode($response,true);
		// return isSet($result['id']) ? $result : null;
		return $result;
	}



	# This function is used to get customer's order details
    # order_type : `rest_accept_types table` 1 : Order now 2 : self pickup 3 : Order for later 4 : Dine In
	# order_status : 0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed
    public function my_orders_get()
    {
    	try{
    		$tokenData = $this->verify_request();
    		# For pagination of reviews
    		$selected_tab = !empty($_GET['selected_tab'])?$this->db->escape_str($_GET['selected_tab']):'1'; # 1 for ORDERS 2 for TABLERESERVATIONS
    		$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
    		$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;

	    	if($tokenData === false)
	    	{
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message'] = $this->lang->line('unauthorized_access');
            }else
            {
            	if($selected_tab == 1) # ORDERS
            	{
            		log_message('error', 'TAB IS 1');
            		$result = $this->Common->getData('orders','restaurants.*,orders.*,orders.id AS order_id,orders.created_at AS order_date_time,restaurants.id AS rest_id','user_id = "'.$tokenData->id.'"',array('restaurants'),array('orders.restaurant_id = restaurants.id'),'orders.id','DESC',$limit,$page);
            		if(count($result) > 0)
            		{
            			$response = $result;
            			$outstanding = 0;
            			foreach ($result as $key => $value) 
            			{
            				$response[$key]['rest_name'] = stripslashes($value['rest_name']);
            				# Now we also need to check whether any outstanding amount needs to be paid by the customer to the Admin
			        		# who_will_pay_outstanding_amount 2- restaurant will pay to customer, 3 - customer will pay to restaurant
			        		if($value['outstanding_amount'] > 0 && $value['who_will_pay_outstanding_amount'] == 3 && $value['is_paid_outstanding_amount'] == 0)
			        		{
			        			$response[$key]['display_order_outstanding_amount'] = (string)$value['outstanding_amount'];
			        		}else
			        		{
			        			$response[$key]['display_order_outstanding_amount'] = '0.00';
			        		}

			        		# Also get data from rating to know whether any rating to this order is given?
			        		$is_rated = $this->Common->getData('ratings','ratings.id','order_id = "'.$value['order_id'].'"');
			        		if(count($is_rated) > 0)
			        		{
			        			$response[$key]['is_rated'] = 1;
			        		}else
			        		{
			        			$response[$key]['is_rated'] = 0;
			        		}

			        		# Calling common function to Get basic delivery time and basic preparation time from setting table
		        			// $hours = $this->delivery_preparation_time();
		        			$time = $this->Common->getData('restaurants','delivery_time,preparation_time','id = "'.$value['rest_id'].'"');
		     				//Array
							// (
							//     [0] => Array
							//         (
							//             [delivery_time] => 
							//             [preparation_time] => 30
							//         )

							// )
					    	$basic_delivery_time = $time[0]['delivery_time'];
					    	$basic_preparation_time = $time[0]['preparation_time'];

					    	$settings_data = $this->Common->getData('settings','value','name = "basic_delivery_time" OR name = "basic_preparation_time" OR name = "window_time"');
					    	if($basic_delivery_time == '' || $basic_delivery_time == 0)
					    	{
					    		$basic_delivery_time = $settings_data[0]['value'];	
					    	}
					    	if($basic_preparation_time == '' || $basic_preparation_time == 0)
					    	{
					    		$basic_preparation_time = $settings_data[1]['value'];
					    	}

					    	$time = $basic_preparation_time + $basic_delivery_time;
					    	# We do not require hours as there is need to return delivery and preparation time as per the restaurant. But as this was used at many places so we did not change therer. We just overrided it.

					    	// $hours = floor($time / 60).'hr '.($time -   floor($time / 60) * 60).'min';
							$response[$key]['del_prep_time'] = $time;
							$response[$key]['basic_delivery_time'] = $basic_delivery_time;
							$response[$key]['basic_preparation_time'] = $basic_preparation_time;

            			}
            		}
            	}else # TABLE RESERVATIONS
            	{
            		log_message('error', 'TAB IS 2');
            		$response = $this->Common->getData('table_reservations','table_reservations.*,table_reservations.id as dinein_id,table_reservations.created_at as dining_date_time,restaurants.*,users.fullname','user_id = "'.$tokenData->id.'"',array('restaurants','users'),array('table_reservations.restaurant_id = restaurants.id','users.id = table_reservations.user_id'),'table_reservations.id','DESC',$limit,$page);	
            		if(count($response) > 0)
            		{
            			foreach ($response as $key => $res)
            			{
            				$response[$key] = $res;
            				$response[$key]['rest_name'] = stripslashes($res['rest_name']);
            			} 
            		}
            	}

            	if(empty($response))
            	{
            		$data['status']		=201;
			        $data['message']	=$this->lang->line('no_data_found');
			        $data['data']		=array(); 
            	}else
            	{
            		$data['status']		=200;
			        $data['message']	=$this->lang->line('success');
			        $data['data']		=$response; 
            	}
            }
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {
            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }


    # dine_in page
    # Landing screen of dine in
    public function dine_in_screen_post()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	$date = !empty($_POST['date'])?$this->db->escape_str($_POST['date']):'';
	    	$time_slot = !empty($_POST['time_slot'])?$this->db->escape_str($_POST['time_slot']):''; # If today then pass current time in regards with even time like if current time is 15:35 then pass 16:00 likewise as per singapore zone and if tomorrow or any future date with 10AM
	    	// $is_today = !empty($_POST['is_today'])?$this->db->escape_str($_POST['is_today']):0; # 1 yes 0 No # NON MANDATORY
	    	$restaurant_id = !empty($_POST['restaurant_id'])?$this->db->escape_str($_POST['restaurant_id']):'';
	    	$is_today = !empty($_POST['is_today'])?$this->db->escape_str($_POST['is_today']):''; # 1 : checking for today and 2 means checking for future date

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($time_slot ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('time_slot_missing');
                $data['data']		=array();
            }else if($restaurant_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }else if($date ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('dinein_date_missing');
                $data['data']		=array();
            }else if($is_today ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('is_today_missing');
                $data['data']		=array();
            }else
            {
            	$rest_basic_details = $this->Common->getData('restaurants','open_time,close_time,time_mode,max_capacity','id = "'.$restaurant_id.'"');
            	$max_capacaity = $rest_basic_details[0]['max_capacity'];

            	date_default_timezone_set('Asia/Singapore');

            	# We need to get the slots ONLY based on open time and close time and current time (in case of TODAY)
            	if($rest_basic_details[0]['time_mode'] == 1) # 1 - for every day, 2 - for Specific day
				{
				    $open_time = $rest_basic_details[0]['open_time'];
				    $close_time = $rest_basic_details[0]['close_time'];
				    $open_time_exp = explode(":",$open_time); # 11:30
				    $open_time_hr = $open_time_exp[0]; # 11
				    $open_time_min = $open_time_exp[1]; # 30

				    $open_time = $date + ($open_time_hr * 60 * 60); # Adding hours
				    $open_time = $open_time + ($open_time_min * 60); # ADD MINUTES
				    // echo "<br>opentimestamp".$open_time;

				    $close_time_exp = explode(":",$close_time); # 11:30
				    $close_time_hr = $close_time_exp[0]; # 11
				    $close_time_min = $close_time_exp[1]; # 30

				    $close_time = $date + ($close_time_hr * 60 * 60); # Adding hours
				    $close_time = $close_time + ($close_time_min * 60); # ADD MINUTES
				}else if($rest_basic_details[0]['time_mode'] == 2)
				{
				    $weekday = date('l', $date);
				    $weekday = strtolower($weekday);
				    // echo "weekday is ".$weekday;
				    $rest_time_daywise = $this->Common->getData('rest_time_daywise','*','rest_id = "'.$restaurant_id.'"');
				    // echo "<pre>";
				    // print_r($rest_time_daywise);

				    if(count($rest_time_daywise) > 0)
				    {
				        if($weekday == 'monday'){
				            $open_close_time = $rest_time_daywise[0]['mon_open_close_time'];
				        }elseif($weekday == 'tuesday'){
				            $open_close_time = $rest_time_daywise[0]['tue_open_close_time'];
				        }elseif($weekday == 'wednesday'){
				            $open_close_time = $rest_time_daywise[0]['wed_open_close_time'];
				        }elseif($weekday == 'thursday'){
				            $open_close_time = $rest_time_daywise[0]['thu_open_close_time'];
				        }elseif($weekday == 'friday'){
				            $open_close_time = $rest_time_daywise[0]['fri_open_close_time'];
				        }elseif($weekday == 'saturday'){
				            $open_close_time = $rest_time_daywise[0]['sat_open_close_time'];
				        }elseif($weekday == 'sunday'){
				            $open_close_time = $rest_time_daywise[0]['sun_open_close_time'];
				        }

				        $exp_all = explode("-",$open_close_time);

				        $open_time = $exp_all[0];
				        $open_time_exp = explode(":",$open_time); # 11:30
				        $open_time_hr = $open_time_exp[0]; # 11
				        $open_time_min = $open_time_exp[1]; # 30

				        $open_time = $date + ($open_time_hr * 60 * 60); # Adding hours
				        $open_time = $open_time + ($open_time_min * 60); # ADD MINUTES

				        $close_time = $exp_all[1];
				        $close_time_exp = explode(":",$close_time); # 11:30
				        $close_time_hr = $close_time_exp[0]; # 11
				        $close_time_min = $close_time_exp[1]; # 30

				        $close_time = $date + ($close_time_hr * 60 * 60); # Adding hours
				        $close_time = $close_time + ($close_time_min * 60); # ADD MINUTES
				    }
				}

				if($is_today == 2) # FUTURE DATE so we will make time_slot as open time
				{
					$time_slot = $open_time;
				}
				// echo "CLOSE TIME is ".$close_time;
            	# We need to add 30 minutes that is 1800 seconds to this time stamp
            	$k = 0;
            	$time_slot = $time_slot + 00;
            	for($i = $time_slot; $time_slot < $close_time - 1800; $i++)
            	{
            		if($k <= 60) # FOR A SAFER SIDE
            		{
	            		if($k > 0) # Not first slot
	            		{
	            			$time_slot = ($time_slot + 1800); # Add 30 minutes for next slot
	            		}
            			$query = "SELECT SUM(no_of_people) AS no_of_booked_seats FROM table_reservations WHERE restaurant_id = ".$restaurant_id." and time_slot = ".$time_slot." and is_accepted NOT IN(2,3)"; # 0 - Action pending 1 - Accepted by merchant 2 - Rejected by merchant 3 - cancelled by customer
            			$check_existing_data = $this->Common->custom_query($query,'get');
            			// echo "<br>QUERY ".$this->db->last_query();
            			// echo "<br>no_of_booked_seats ".$no_of_booked_seats;
            			$no_of_booked_seats = $check_existing_data[0]['no_of_booked_seats'];
            			# If already booked seat
            			# Get capacity and send the status
            			if($no_of_booked_seats < $max_capacaity)
            			{
            				$response[$k]['is_full'] = 2; # SEATS ARE AVAILABLE TO BOOK
            			}else
            			{
            				$response[$k]['is_full'] = 1; # ALREADY FULL
            			}
	            		
	            		// $time_slot = $time_slot * 30; # Add 30 minutes for next slot
	            		# Check whether any reservation made for this restaurant
	            		$response[$k]['time_slot'] = $time_slot;
	            		$response[$k]['display'] = date("H:i",$time_slot);
	            		
            		}
            		$k++;
            	}
            	if(count($response) > 0)
            	{
	            	$data['status']		=200;
			        $data['message']	=$this->lang->line('success');
			        $data['data']		=$response; 
            	}else
            	{
            		$data['status']		=201;
			        $data['message']	=$this->lang->line('no_data_found');
			        $data['data']		=array(); 
            	}
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

	# This API is used to book or edit table reservation
	# action : # 1 : Add 2 : edit
	# dine_in_id : primary key id of the table reservations
	# Use this API when user clicks on change button in my order screen 
	public function add_edit_dinein_post()
	{
		try{
	    	$tokenData = $this->verify_request();
	    	$date = !empty($_POST['date'])?$this->db->escape_str($_POST['date']):'';
	    	$no_of_people = !empty($_POST['no_of_people'])?$this->db->escape_str($_POST['no_of_people']):'';
	    	$time_slot = !empty($_POST['time_slot'])?$this->db->escape_str($_POST['time_slot']):'';
	    	$action = !empty($_POST['action'])?$this->db->escape_str($_POST['action']):''; # 1 : Add 2 : edit
	    	$restaurant_id = !empty($_POST['restaurant_id'])?$this->db->escape_str($_POST['restaurant_id']):'';

	        if($tokenData === false){
	            $status = parent::HTTP_UNAUTHORIZED;
	            $data['status']	 = $status;
	            $data['message']	=$this->lang->line('unauthorized_access');
	        }else if($date ==''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('dinein_date_missing');
	            $data['data']		=array();
	        }else if($no_of_people ==''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('no_of_people_missing');
	            $data['data']		=array();
	        }else if($time_slot ==''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('time_slot_missing');
	            $data['data']		=array();
	        }else if($action ==''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('dinein_action_missing');
	            $data['data']		=array();
	        }else if($restaurant_id ==''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('rest_id_missing');
	            $data['data']		=array();
	        }else
	        {

	        	# REMOVE THIS. CURRENTL

	        	$rest_basic_details = $this->Common->getData('restaurants','*','id = "'.$restaurant_id.'"');
	        	$max_capacaity = $rest_basic_details[0]['max_capacity'];
	        	# We need to check whether this restaurant is available or not for the given date and slot
	        	$offline_data = $this->Common->getData('rest_offline','*','rest_id = "'.$restaurant_id.'"');
	        	// echo "<br><pre>";
	        	// print_r($offline_data);
				if(count($offline_data) > 0)
				{
					$offline_from = $offline_data[0]['offline_from'];
					$offline_to = $offline_data[0]['offline_to'];
					

					$proceed_further = 0; # 0 No 1 Yes
					$proceed_further_more = 0; # 0 No 1 Yes

					if($offline_data[0]['offline_tag'] != 1)
					{
						# BELOW SCENE IS HAPPENING WHEN TAGE IS NOT 1

						# Timestamp of offline from and offline to is going into the database as per the UTC timezone and created_at and update_at going as per the local timezone so the value in $date and $time_slot will be given as per the local timezone (If device is on India then india local time will be given and if device is in singapore then singapore local time will be given)
						# So here I am having problem because India and UTC having 5 hours and 30 minutes difference and UTC and Singapore is 8 hours difference,
						# So currently in order to proceed further I am deducting 5 hours and 30 minutes from DB offline values (from and to) and then I have to disable 5 ho urs and 30 mins code and enable 8 hours when send code on live
						
						# ######## FOR INDIA UNCOMMENT BELOW ########
						// $offline_from_val = $offline_from - (5*60*60); # DEDUCT 5 HOURS
						// $offline_from = $offline_from_val - (30 *60 ); # AND THEN 30 MINUTES

						// $offline_to_val = $offline_to - (5*60*60); # DEDUCT 5 HOURS
						// $offline_to = $offline_to_val - (30 *60 ); # AND THEN 30 MINUTES
						# ######## FOR INDIA ########
						# Why we are subtracting because UTC and singapore having 8  hours difference and the localtime passed as timestamp will be 8 hours less than the UTC so either dedcut 8 hours from UTC or add 8 hours to the given timestamp
						# ######## FOR SINGAPORE UNCOMMENT BELOW ######### 
						$offline_from = $offline_from - (8*60*60); # DEDUCT 8 HOURS
						$offline_to = $offline_to - (8*60*60); # DEDUCT 8 HOURS
						# ######## FOR SINGAPORE ######### 



						// echo "offline_from IS ".$offline_from;
						// echo "<br>offline_to IS ".$offline_to;
						// echo "<br>time_slot IS ".$time_slot;
						if($date >= $offline_from  && $date <= $offline_to) # $date is midnight timestamp like 00:00
						{
							$proceed_further = 0;
							$data['status']		=201;
					        $data['message']	="Restaurant is set to Offline and not offering dinein on this day";
					        $data['data']		=array();
						}else
						{
							$proceed_further = 1;
						}
					}elseif($time_slot >= $offline_from  && $time_slot <= $offline_to) # HOURS $time_slot is exact timestamp with hours like 23aug11:30 (24 hours format)
					{
						// echo " <br>M I HERE ";
						$proceed_further = 0;
						$data['status']		=201;
				        $data['message']	="Restaurant is set to Offline and not offering dinein in this duration";
				        $data['data']		=array();
					}else
					{
						// echo " <br>QQQQQQQQ ";
						$proceed_further = 1;
					}
				}else # OPEN # No entry in offline table
				{
				    $proceed_further = 1;
				}
				// echo "<br>proceed_further".$proceed_further;
				// die;
				if($proceed_further == 1)
				{
					# Get Time mode
					if($rest_basic_details[0]['time_mode'] == 1) # 1 - for every day, 2 - for Specific day
					{
						$open_time = $rest_basic_details[0]['open_time'];
						$close_time = $rest_basic_details[0]['close_time'];

						$open_time_exp = explode(":",$open_time); # 11:30
						$open_time_hr = $open_time_exp[0]; # 11
						$open_time_min = $open_time_exp[1]; # 30

						$open_time = $date + ($open_time_hr * 60 * 60); # Adding hours
						$open_time = $open_time + ($open_time_min * 60); # ADD MINUTES
						// echo "<br>opentimestamp".$open_time;

						$close_time_exp = explode(":",$close_time); # 11:30
						$close_time_hr = $close_time_exp[0]; # 11
						$close_time_min = $close_time_exp[1]; # 30

						$close_time = $date + ($close_time_hr * 60 * 60); # Adding hours
						$close_time = $close_time + ($close_time_min * 60); # ADD MINUTES
						// echo "<br>closetimestamp".$close_time;

						if($rest_basic_details[0]['break_start_time'] == '')
						{
							# NO BREAK IS GIVEN
							$break_from = '';
							$break_to = '';
						}else
						{
							$break_start_time = $rest_basic_details[0]['break_start_time'];
							$break_end_time = $rest_basic_details[0]['break_end_time'];
							$break_start_exp = explode(":",$break_start_time);

							$break_start_hr = $break_start_exp[0];
							$break_start_min = $break_start_exp[1];

							$break_end_exp = explode(":",$break_end_time);
							$break_end_hr = $break_end_exp[0];
							$break_end_min = $break_end_exp[1];

							# So here we need to add hours to the $date param
							$break_from = $date + ($break_start_hr * 60 * 60); # Adding hours
							$break_from = $break_from + ($break_start_min * 60); # ADD MINUTES

							$break_to = $date + ($break_end_hr * 60 * 60); # Adding HOURS
							$break_to = $break_to + ($break_end_min * 60);
						}
						// if((10:00 <= 22:00) && (22:00 <= 23:30)) # IT IS OPEN
						// if((10:00 <= 23:00) && (23:00 <= 22:30)) # IT IS CLOSED
						//(($startTime < $currentTime) && ($currentTime < $endTime)) google
						// if(($open_time <= $time_slot) && ($time_slot <= $close_time)) # 
						// {
						// 	echo "OPEN";
						// }else
						// {
						// 	echo "Close";
						// }
						// die;

						if(($open_time <= $time_slot) && ($time_slot <= $close_time)) # OPEN
						{
							$proceed_further_more = 1;
						}else
						{
							$proceed_further_more = 0;
							# Not open
							$data['status']		=201;
					        $data['message']	="Restaurant is not open for the selected time";
					        $data['data']		=array();

						}
						if($proceed_further_more == 1)
						{
							if(($break_from != '') && ($time_slot >= $break_from  && $time_slot <= $break_to))
							{
								$proceed_further_more = 0;
								# On Break
								$data['status']		=201;
						        $data['message']	="Restaurant is on break during this time";
						        $data['data']		=array();
							}else
							{
								$proceed_further_more = 1;
							}
						}
					}else if($rest_basic_details[0]['time_mode'] == 2)
					{
						// echo "WWWWWWWw";
						# Get what is the Day of the passed date_timestamp
						# COMMENT THIS TIMEZONE VALUE
						// date_default_timezone_set('Asia/Kolkata');
						date_default_timezone_set('Asia/Singapore');
						$weekday = date('l', $date);
						$weekday = strtolower($weekday);
						// echo "weekday is ".$weekday;
						$rest_time_daywise = $this->Common->getData('rest_time_daywise','*','rest_id = "'.$restaurant_id.'"');

						if(count($rest_time_daywise) > 0)
						{
							if($weekday == 'monday'){
								$full_day_close_status = $rest_time_daywise[0]['mon_close_status'];
								$open_close_time = $rest_time_daywise[0]['mon_open_close_time'];
								$brk_status = $rest_time_daywise[0]['mon_break_status'];
								$brk_start_brk_end = $rest_time_daywise[0]['mon_break_start_end_time'];
							}elseif($weekday == 'tuesday'){
								$full_day_close_status = $rest_time_daywise[0]['tue_close_status'];
								$open_close_time = $rest_time_daywise[0]['tue_open_close_time'];
								$brk_status = $rest_time_daywise[0]['tue_break_status'];
								$brk_start_brk_end = $rest_time_daywise[0]['tue_break_start_end_time'];
							}elseif($weekday == 'wednesday'){
								$full_day_close_status = $rest_time_daywise[0]['wed_close_status'];
								$open_close_time = $rest_time_daywise[0]['wed_open_close_time'];
								$brk_status = $rest_time_daywise[0]['wed_break_status'];
								$brk_start_brk_end = $rest_time_daywise[0]['wed_break_start_end_time'];
							}elseif($weekday == 'thursday'){
								$full_day_close_status = $rest_time_daywise[0]['thu_close_status'];
								$open_close_time = $rest_time_daywise[0]['thu_open_close_time'];
								$brk_status = $rest_time_daywise[0]['thu_break_status'];
								$brk_start_brk_end = $rest_time_daywise[0]['thu_break_start_end_time'];
							}elseif($weekday == 'friday'){
								$full_day_close_status = $rest_time_daywise[0]['fri_close_status'];
								$open_close_time = $rest_time_daywise[0]['fri_open_close_time'];
								$brk_status = $rest_time_daywise[0]['fri_break_status'];
								$brk_start_brk_end = $rest_time_daywise[0]['fri_break_start_end_time'];
							}elseif($weekday == 'saturday'){
								$full_day_close_status = $rest_time_daywise[0]['sat_close_status'];
								$open_close_time = $rest_time_daywise[0]['sat_open_close_time'];
								$brk_status = $rest_time_daywise[0]['sat_break_status'];
								$brk_start_brk_end = $rest_time_daywise[0]['sat_break_start_end_time'];
							}elseif($weekday == 'sunday'){
								$full_day_close_status = $rest_time_daywise[0]['sun_close_status'];
								$open_close_time = $rest_time_daywise[0]['sun_open_close_time'];
								$brk_status = $rest_time_daywise[0]['sun_break_status'];
								$brk_start_brk_end = $rest_time_daywise[0]['sun_break_start_end_time'];
							}

							// echo "<br>brk_status is ".$brk_status;
							if($full_day_close_status == 2) # 2- on this day restaurant will be closed, 1 - restaurant will be opend
							{
								$proceed_further_more = 0;
								# Restaurant is closed on this day
								$data['status']		=201;
						        $data['message']	="Restaurant is closed on ".ucfirst($weekday)."";
						        $data['data']		=array();
							}else
							{
								if($brk_status == 1) # 2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time) and 0 when restaurant is closed in full day
								{
									$brk_start_brk_end = explode("-",$brk_start_brk_end);
					    			$break_start_time = $brk_start_brk_end[0];
									$break_end_time = $brk_start_brk_end[1];

									$break_start_exp = explode(":",$break_start_time);
									$break_start_hr = $break_start_exp[0];
									$break_start_min = $break_start_exp[1];

									$break_end_exp = explode(":",$break_end_time);
									$break_end_hr = $break_end_exp[0];
									$break_end_min = $break_end_exp[1];

									# So here we need to hours to the $date param
									$break_from = $date + ($break_start_hr * 60 * 60); # Adding hours
									$break_from = $break_from + ($break_start_min * 60); # ADD MINUTES
									// echo "<br>break_from is ".$break_from = $break_from + ($break_start_min * 60); # ADD MINUTES

									$break_to = $date + ($break_end_hr * 60 * 60); # Adding HOURS
									$break_to = $break_to + ($break_end_min * 60);
									// echo "<br>time_slot is ".$time_slot;
									if($time_slot >= $break_from  && $time_slot <= $break_to)
									{
										$proceed_further_more = 0;
										// echo "<br>HERE";
										# Running on break
										$data['status']		=201;
								        $data['message']	="Restaurant is on break during these hours";
								        $data['data']		=array();
									}else
									{
										$proceed_further_more = 1;
									}
								}else
								{
									$proceed_further_more = 1;

								}
							}
						}
					}	
				}

				if($proceed_further_more == 1)
				{
					# Before add or edit we need to check that customer has selected how many number of people and we need to compare with max capacity and seats left

					$query = "SELECT SUM(no_of_people) AS no_of_booked_seats FROM table_reservations WHERE restaurant_id = ".$restaurant_id." and time_slot = ".$time_slot." and is_accepted NOT IN(2,3)"; # 0 - Action pending 1 - Accepted by merchant 2 - Rejected by merchant 3 - cancelled by customer
					$check_existing_data = $this->Common->custom_query($query,'get');
					$no_of_booked_seats = $check_existing_data[0]['no_of_booked_seats'];

					/* max capacity : 40
					booked seats : 35

					left : max - booked

					if(selected_no_of_people <= left)
					{
						OK WE CAN BOOK
					}else
					{
						SORRY only $left seats are available to book
					}*/
					$seats_left = $max_capacaity - $no_of_booked_seats;
					if($no_of_people <= $seats_left) # OK WE CAN BOOK
					{
						if($action == 1) # ADD
		            	{
		            		$book_dinein = [
		            			'restaurant_id' => $restaurant_id,
		            			'booking_date' => $date,
		            			'no_of_people' => $no_of_people,
		            			'time_slot' => $time_slot,
		            			'user_id' => $tokenData->id,
		            			'is_accepted' => 0,
		            			'created_at' => time(),
		            			'updated_at' => time(),
		            		];

		            		$dinein_id = $this->Common->insertData("table_reservations",$book_dinein);

		            		$booking_start = 1000; # Static value and it will be added by the last Id in increasing way
			                $sr_booking_number = $booking_start + $dinein_id;
			                $update_array=[
			                    'booking_id' => $sr_booking_number,
			                ];
			                $this->Common->updateData('table_reservations',$update_array , 'id = "'.$dinein_id.'"');

			                $response = array('booking_id' => $sr_booking_number , 'dinein_primary_id' => $dinein_id);

			                # Send notification to merchant for dine in booking
			                $merchant_id = $this->Common->getData('restaurants','admin_id','id = "'.$restaurant_id.'"');
			                $merchant_id = $merchant_id[0]['admin_id'];
			                $token_merchant = $this->Common->getData('users', 'device_token', 'id = "' . $merchant_id . '"');
			                # We will pass $date value in order_id becaue table_reservation_get api will be called for redirection so date param will be required as key. we donot have separate open page for dine in details.
			                $notification_data_fields = array('message' => 'Dinein ' . $sr_booking_number . ' is booked', 'title' => NOTIFICATION_TITLE_DINEIN_BOOKED,
				            'order_id' => $date, 'notification_type' => 'DINE_IN_ADDED');
			                if (!empty($token_merchant)) {
				                foreach ($token_merchant as $tk) {
				                    $token = $tk['device_token'];
				                    if ($token != "") {
				                        sendPushNotification($token, $notification_data_fields, IOS_BUNDLE_ID_MERCHANT);
				                    }
				                }
				            }
				            # Send for Merchant
				            $insertData = ['title' => 'Dinein ' . $sr_booking_number . ' is booked', 'to_user_id' => $merchant_id, 'type' => 1, # Order related
				            'order_id' => $sr_booking_number, 'is_read' => 1, 'created_at' => time(), 'updated_at' => time() ];
				            $this->Common->insertData('notifications', $insertData);

			                $data['status']		= 200;
				            $data['message']	= $this->lang->line('dinein_add_success');
				            $data['data']		= $response;
		            	}else if($action == 2) #EDIT
		            	{
		            		$dine_in_id = !empty($_POST['dine_in_id'])?$this->db->escape_str($_POST['dine_in_id']):'';
							if($dine_in_id == '')
							{
								$data['status']		=201;
				                $data['message']	=$this->lang->line('dinein_id_missing');
				                $data['data']		=array();
							}else
							{
			            		$edit_dinein = [
			            			'booking_date' => $date,
			            			'no_of_people' => $no_of_people,
			            			'time_slot' => $time_slot,
			            			'updated_at' => time(),
			            		];
			            		
			            		$this->Common->updateData('table_reservations',$edit_dinein,'id = "'.$dine_in_id.'"');
			            		
			            		$data['status']		= 200;
					            $data['message']	= $this->lang->line('dinein_edit_success');
					            $data['data']		= array();
							}
		            	}
					}else
					{
						if($seats_left > 0)
						{
							$data['status']		=201;
					        $data['message']	="Sorry only ".$seats_left." seats are left to book for this slot";
					        $data['data']		=array();
						}else
						{
							$data['status']		=201;
					        $data['message']	="Sorry this slot is fully booked.";
					        $data['data']		=array();
						}

					}
				}
	        }
	        # REST_Controller provide this method to send responses
	        $this->response($data, $data['status']);
		} catch (\Exception $e) {

	        # make error log
	        log_message('error', $e);

	        $data['status']		=500;
	        $data['message']	=$this->lang->line('internal_server_error');
	        $data['data']		=array(); 

	        $this->response($data, $data['status']);
	    }
	}

	# This function is used to view more detail of an order
	# order_type : 1 : Order now 2 : self pickup 3 : Order for later 4 : Dine In
	public function view_more_order_detail_get()
	{
		try{
    		$tokenData = $this->verify_request();
    		$order_id = !empty($_GET['order_id'])?$this->db->escape_str($_GET['order_id']):'';

	    	if($tokenData === false)
	    	{
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message'] = $this->lang->line('unauthorized_access');
            }else if($order_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('order_id_missing');
                $data['data']		=array();
            }else
            {
            	$response = array();
            	# Get data from orders and order product details table
            	$detail = $this->Common->getData('orders','*,orders.created_at AS order_date_time','orders.id = "'.$order_id.'"');
            	$response['order'] = $detail[0];

            	# CHANGE HERE FOR PRODUCT AND VARIANT NAME
            	// $order_product_details = $this->Common->getData('order_product_details','order_product_details.*,products.product_name','order_id = "'.$order_id.'"',array('products'),array('products.id = order_product_details.product_id'));
            	$order_product_details = $this->Common->getData('order_product_details','order_product_details.*','order_id = "'.$order_id.'" AND status != 1');
            	$response['product_detail'] = $order_product_details;


            	# Now we will check whehter any variant for cart product available
        		if(!empty($response['product_detail']))
        		{
        			foreach ($response['product_detail'] as $key => $value) 
        			{
		        		$cart_var_query = "SELECT * FROM order_product_variant_details 
		        		INNER JOIN orders ON orders.id = order_product_variant_details.order_id 
		        		-- INNER JOIN `variants` ON `variants`.`variant_id` = `order_product_variant_details`.`variant_id` 
		        		-- INNER JOIN `variant_types` ON `variant_types`.`variant_type_id` = `order_product_variant_details`.`variant_type_id` 
		        		WHERE `order_product_variant_details`.`order_id` = ".$order_id." AND `order_product_variant_details`.`product_id` = ".$value['product_id']." AND order_product_variant_details.status != 1";

		        		$cart_var_data = $this->Common->custom_query($cart_var_query,'get');
		        		// echo $this->db->last_query();
		        		// echo "<pre>";
		        		// print_r($cart_var_data);
		        		if(count($cart_var_data) > 0)
		        		{
		        			$response['product_detail'][$key]['variants'] = $cart_var_data;
		        		}else
		        		{
		        			$response['product_detail'][$key]['variants'] = array();
		        		}
        			}
        		}

        		$rest_id = $this->get_restaurant_id($tokenData->id);
        		$rest_delivery_time  =  $this->Common->getData('restaurants','delivery_time,preparation_time','id = "'.$rest_id.'"'); 
            	$rest_delivery_time = $rest_delivery_time[0]['delivery_time'];
            	$rest_prep_time = $rest_delivery_time[0]['preparation_time'];
			   	# If restaurant has not set any delivery time then we need to take value from settings table
			   	if($rest_delivery_time == 0 || $rest_delivery_time == '')
			   	{
			        $basic_delv_time = $this->Common->getData('settings','value','name = "basic_delivery_time"');
			        $rest_delivery_time = $basic_delv_time[0]['value'];
			   	}
			   	if($rest_prep_time == 0 || $rest_prep_time == '')
			   	{
			        $basic_preparation_time = $this->Common->getData('settings','value','name = "basic_preparation_time"');
			        $rest_prep_time = $basic_preparation_time[0]['value'];
			   	}

			   	$response['order']['delivery_time'] = $rest_delivery_time;;
			   	$response['order']['basic_preparation_time'] = $rest_prep_time;;
            	if(!empty($response))
            	{
	            	$data['status']		=200;
			        $data['message']	=$this->lang->line('success');
			        $data['data']		=$response; 
            	}else
            	{
            		$data['status']		=201;
			        $data['message']	=$this->lang->line('no_data_found');
			        $data['data']		=array(); 
            	}

            }
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# This function is used by the customer to cancel the booked dine in
	public function cancel_dinein_post()
	{
		try{
	    	$tokenData = $this->verify_request();
	    	$dine_in_id = !empty($_POST['dine_in_id'])?$this->db->escape_str($_POST['dine_in_id']):'';

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($dine_in_id == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('dinein_id_missing');
                $data['data']		=array();
			}else
            {
        		$edit_dinein = ['is_accepted' => 3 , 'updated_at' => time()];
        		
        		$this->Common->updateData('table_reservations',$edit_dinein,'id = "'.$dine_in_id.'"');
        		
        		$data['status']		= 200;
	            $data['message']	= $this->lang->line('dinein_cancel_success');
	            $data['data']		= array();
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# This function is used by the customer to get wallet details along with all transactions
	public function wallet_details_get()
	{
		try{
	    	$tokenData = $this->verify_request();

	    	$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
            
            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else
            {
        		$wallet_balance = $this->get_wallet_balance($tokenData->id);
        		$wallet_balance = str_replace(",", "", $wallet_balance[0]['wallet_balance']);
        		// $wallet_balance = (int)$wallet_balance[0]['wallet_balance'];
	        	$total_balance = number_format($wallet_balance,2, '.', '');
	        	$response['total_balance'] = $total_balance;

	        	# 1 - Cashback 2 - Money Added 3 - debited
	        	# Now check how much total of MONEY ADDED i.e. type 2

	        	$result = "SELECT FORMAT((sum(COALESCE(credited, 0))-sum(COALESCE(debited, 0))),2) AS wallet_balance FROM wallet WHERE IF (valid_till != 0 , UNIX_TIMESTAMP() <= valid_till AND user_id = $tokenData->id , user_id = $tokenData->id) AND type = 2";
	    		$money_added = $this->Common->custom_query($result,"get");
	    		if($money_added[0]['wallet_balance'] == null)
	    		{
	    			$response['total_money_added'] = '0.00';
	    		}else
	    		{
	    			$money_added = str_replace(",", "", $money_added[0]['wallet_balance']);
	    			$response['total_money_added'] = number_format($money_added,2, '.', '');
	    		}

	    		# CASHBACK
	    		$result = "SELECT FORMAT((sum(COALESCE(credited, 0))-sum(COALESCE(debited, 0))),2) AS wallet_balance FROM wallet WHERE IF (valid_till != 0 , UNIX_TIMESTAMP() <= valid_till AND user_id = $tokenData->id , user_id = $tokenData->id) AND type = 1";
	    		$cashback = $this->Common->custom_query($result,"get");
	    		if($cashback[0]['wallet_balance'] === null)
	    		{
	    			$response['total_cashback'] = '0.00';
	    		}else
	    		{
	    			$response['total_cashback'] = number_format($cashback[0]['wallet_balance'],2, '.', '');
	    		}
	    		

	    		# debited
	    		$result = "SELECT FORMAT((sum(COALESCE(credited, 0))-sum(COALESCE(debited, 0))),2) AS wallet_balance FROM wallet WHERE IF (valid_till != 0 , UNIX_TIMESTAMP() <= valid_till AND user_id = $tokenData->id , user_id = $tokenData->id) AND type = 3";
	    		$debited = $this->Common->custom_query($result,"get");
	    		// echo $this->db->last_query();die;
	    		if($debited[0]['wallet_balance'] == null)
	    		{
	    			$response['total_debited'] = '0.00';
	    		}else
	    		{
	    			$response['total_debited'] = number_format($debited[0]['wallet_balance'],2, '.', '');
	    		}
	    		
	    		# All transaction list
	    		$all_transactions = $this->Common->getData('wallet','*','user_id = "'.$tokenData->id.'"','','','id','DESC',$limit,$page);
	    		$response['all_transactions'] = $all_transactions;
	    		if(!empty($response['all_transactions']))
	    		{
	    			foreach ($response['all_transactions'] as $key => $value) 
	    			{
	    				if($value['order_id'] == 0)
	    				{
	    					$response['all_transactions'][$key]['display_name'] = $tokenData->name;
	    				}else
	    				{
		    				$name = $this->Common->getData('orders','restaurants.rest_name','orders.id = "'.$value['order_id'].'"',array('restaurants'),array('restaurants.id = orders.restaurant_id'));
		    				$response['all_transactions'][$key]['display_name'] = $name[0]['rest_name'];
	    				}
	    			}
	    		}

	        	$data['status']		=200;
                $data['message']	=$this->lang->line('success');
                $data['data']		=$response;
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# This function is used to filter wallet details
	public function filter_wallet_post()
	{
		try{
	    	$tokenData = $this->verify_request();
	    	$filter_type = !empty($_POST['filter_type'])?$this->db->escape_str($_POST['filter_type']):''; # 1 : By date 2 : By type

	    	$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
            
            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($filter_type == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('filter_type_missing');
                $data['data']		=array();
			}else
            {
            	# If filter_type == 1
            	if($filter_type == 1) # By date
            	{
					$from_date = !empty($_POST['from_date'])?$this->db->escape_str($_POST['from_date']):'';
					$to_date = !empty($_POST['to_date'])?$this->db->escape_str($_POST['to_date']):'';
					if($from_date == '')
					{
						$data['status']		=201;
		                $data['message']	=$this->lang->line('from_date_missing');
		                $data['data']		=array();
					}else if($to_date == '')
					{
						$data['status']		=201;
		                $data['message']	=$this->lang->line('to_date_missing');
		                $data['data']		=array();	
					}else
					{
						# All transaction list
			    		$all_transactions = $this->Common->getData('wallet','*','user_id = "'.$tokenData->id.'" AND (wallet_date >= "'.$from_date.'" AND wallet_date < "'.$to_date.'")','','','','',$limit,$page);
					}
            	}else
            	{
            		$filter_by = !empty($_POST['filter_by'])?$this->db->escape_str($_POST['filter_by']):''; # 1 : Money Added 2 : Cashback
            		if($filter_by == '')
					{
						$data['status']		=201;
		                $data['message']	=$this->lang->line('filter_by_missing');
		                $data['data']		=array();
					}else
					{
						# All transaction list
			    		$all_transactions = $this->Common->getData('wallet','*','user_id = "'.$tokenData->id.'" AND type = "'.$filter_by.'"','','','','',$limit,$page);
					}
            	}
            	if(!empty($all_transactions))
            	{
            		$data['status']		=200;
	                $data['message']	=$this->lang->line('success');
	                $data['data']		=$all_transactions;
            	}else
            	{
            		$data['status']		=201;
	                $data['message']	=$this->lang->line('no_data_found');
	                $data['data']		=array();
            	}
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# This function is used but customer itself to add Money to user's wallet
	# Call this api after place_order if transaction is successful
	public function add_money_to_wallet_post()
	{
		try{
	    	$tokenData = $this->verify_request();
	    	$amount = !empty($_POST['amount'])?$this->db->escape_str($_POST['amount']):'';

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($amount == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('wallet_amount_missing');
                $data['data']		=array();
			}else
            {
            	$insert_wallet = [
					'user_id' => $tokenData->id,
					'order_id' => 0,
					'wallet_date' => time(),
					'debited' => 0,
					'credited' => $amount,
					'type' => 2,
					'added_by' => 2,
					'valid_till' => '',
					'created_at' => time(),
					'updated_at' => time(),
				];
				$this->Common->insertData('wallet', $insert_wallet);
				# Send notification code start
        		$device_token = $this->Common->getData('users','device_token','id='.$tokenData->id);
        		$device_token = $device_token[0]['device_token'];

        		$notification_data_fields = array(
                    'message' => 'S$'.$amount.' added to your wallet',
                    'title' => NOTIFICATION_MONEY_ADDED,
                    'notification_type' => 'WALLET_RECHARGE'
                );

        		# DELETE THE KEY THEN
        		# OTHER DEPENDENT API LIKE update_paid_outstanding_amount and add_money_to_wallet and record_transaction_details we need to delete the created entry of idempotency key from the Database for this User.
        		// $this->Common->deleteData('idempotency_key','user_id = "'.$tokenData->id.'"');
        		# We have seperate bundle ids for customer and merchant. So if push needs to be sent to customer then use IOS_BUNDLE_ID_CUSTOMER constant

                if($device_token != "")
                {
                  sendPushNotification($device_token, $notification_data_fields,IOS_BUNDLE_ID_CUSTOMER);
                }

                # Now insert notification to Database
                $insertData = [
                	'title' => 'S$'.$amount.' added to your wallet',
                	'to_user_id' => $tokenData->id,
                	'type' => 2,
                	'order_id' => 0,
                	'is_read' => 1,
                	'created_at' => time(),
                	'updated_at' => time(),
                ];
                $this->Common->insertData('notifications',$insertData);

        		$data['status']		=200;
                $data['message']	=$this->lang->line('addmoney_success');
                $data['data']		=array();
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# FILTER RESTAURANTS STARTS
	# This api will be used to filter restaurant by rating
	public function filter_restaurants_by_rating_post()
	{
		try{
			$tokenData = $this->verify_request(); # Pass token if user is logged in

	    	$page = !empty($_POST['page'])?$this->db->escape_str($_POST['page']):0;
			$limit = !empty($_POST['limit'])?$this->db->escape_str($_POST['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;

			# DB _business_type = 1 Food 2 Grocery 3 Alcohol
    		$business_type = !empty($_POST['business_type'])?$this->db->escape_str($_POST['business_type']):'';
    		# DB Food type : 1 (Restaurant) 2 (Kitchen/homemade)
        	$food_type = !empty($_POST['food_type'])?$this->db->escape_str($_POST['food_type']):'';
        	$order_type = !empty($_POST['order_type'])?$this->db->escape_str($_POST['order_type']):'0'; #  `rest_accept_types table` 1 : Order now 2 : self pickup_time_from 3 : Order for later 4 : Dine In
        	# 0 : Not given
			$lat = !empty($_POST['lat'])?($_POST['lat']):'';
			$lng = !empty($_POST['lng'])?($_POST['lng']):'';
			$is_valid = 0;
			$date_timestamp = !empty($_POST['date_timestamp'])?$this->db->escape_str($_POST['date_timestamp']):'';

			if($date_timestamp == '')
        	{
        		$data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
        	}else if($tokenData === false)
			{
				$tokenData = ''; # Pass empty string if token is false
				# This fn can be called in both cases (Logged in and guest) so we will use token in fn_getrestaurant_data function to check wishlist status
			    # If token is not present that it may be a guest user so in such case we need lat long
				if($lat == ''){
			        $data['status']		=201;
			        $data['message']	=$this->lang->line('latitude_missing');
			        $data['data']		=array();
				}else if($lng == ''){
			        $data['status']		=201;
			        $data['message']	=$this->lang->line('longitude_missing');
			        $data['data']		=array();
				}
				else
				{
					$is_valid = 1;
				}
			}else
			{
				$is_valid = 1;
				$latlong = $this->Common->getData('users','latitude,longitude','id = "'.$tokenData->id.'"');
				$lat = $latlong[0]['latitude'];
				$lng = $latlong[0]['longitude'];
			}

			if($is_valid == 1)
			{
				$rating_from = $_POST['rating_from']; # It can be 0 hence not using escape_str
				$rating_to = $_POST['rating_to'];
				if($rating_from == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('rating_from_missing');
	                $data['data']		=array();
				}else if($rating_to == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('rating_to_missing');
	                $data['data']		=array();	
				}else if($business_type == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('business_type_missing');
	                $data['data']		=array();	
				}else if($food_type == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('food_type_missing');
	                $data['data']		=array();	
				}else
				{
					if($order_type == 1)
					{
						// $rating_filter = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND avg_rating BETWEEN "'.$rating_from.'" AND "'.$rating_to.'" AND is_order_now_accept = 1 AND restaurants.rest_status = 1',array('users'),array('users.id = restaurants.admin_id'),'','',$limit,$page);	
						$rating_filter = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND avg_rating BETWEEN "'.$rating_from.'" AND "'.$rating_to.'" AND is_order_now_accept = 1 AND restaurants.rest_status = 1',array('users'),array('users.id = restaurants.admin_id'));	
					}elseif($order_type == 2)
					{
						// $rating_filter = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND avg_rating BETWEEN "'.$rating_from.'" AND "'.$rating_to.'" AND is_self_pickup_accept = 1 AND restaurants.rest_status = 1',array('users'),array('users.id = restaurants.admin_id'),'','',$limit,$page);
						$rating_filter = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND avg_rating BETWEEN "'.$rating_from.'" AND "'.$rating_to.'" AND is_self_pickup_accept = 1 AND restaurants.rest_status = 1',array('users'),array('users.id = restaurants.admin_id'));
					}elseif($order_type == 3)
					{
						// $rating_filter = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND avg_rating BETWEEN "'.$rating_from.'" AND "'.$rating_to.'" AND is_order_later_accept = 1 AND restaurants.rest_status = 1',array('users'),array('users.id = restaurants.admin_id'),'','',$limit,$page);
						$rating_filter = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND avg_rating BETWEEN "'.$rating_from.'" AND "'.$rating_to.'" AND is_order_later_accept = 1 AND restaurants.rest_status = 1',array('users'),array('users.id = restaurants.admin_id'));
					}else
					{
						// $rating_filter = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND avg_rating BETWEEN "'.$rating_from.'" AND "'.$rating_to.'"',array('users'),array('users.id = restaurants.admin_id'),'','',$limit,$page);
						$rating_filter = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND avg_rating BETWEEN "'.$rating_from.'" AND "'.$rating_to.'"',array('users'),array('users.id = restaurants.admin_id'));
					}
					// echo $this->db->last_query();
					if(count($rating_filter) > 0)
					{
						# Calling common function to Get basic delivery time and basic preparation time from setting table
			        	$hours = $this->delivery_preparation_time();
			        	# Get all the basic restaurant from common funtion
			    		$rating_filter_result = $this->get_restaurant_data($rating_filter,$hours,'',$lat,$lng,$date_timestamp);
			    		$rating_filter_result = $this->apply_open_close_check_and_get_data($rating_filter_result,$order_type);
		    			
		    			$data['status']		=200;
		                $data['message']	=$this->lang->line('success');
		                $data['data']		=$rating_filter_result;
					}else
		    		{
		    			$data['status']		=201;
		                $data['message']	=$this->lang->line('no_data_found');
		                $data['data']		=array();
		    		}
				}
			}
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# FILTER RESTAURANTS ENDS

	# Filter products starts
	# This function is used to filter products
	public function sort_rest_product_post()
	{
		try{
			$tokenData = $this->verify_request(); # Pass token if user is logged in

	    	$page = !empty($_POST['page'])?$this->db->escape_str($_POST['page']):0;
			$limit = !empty($_POST['limit'])?$this->db->escape_str($_POST['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
			$order_type = !empty($_POST['order_type'])?$this->db->escape_str($_POST['order_type']):'0'; #  `rest_accept_types table` 1 : Order now 2 : self pickup_time_from 3 : Order for later 4 : Dine In
			# 0 : Not given

			$sort_type = !empty($_POST['sort_type'])?$this->db->escape_str($_POST['sort_type']):'';
			/* 
				1 : Popularity (restaurant) 
				2 : Rating - High to low (restaurant) 
				3 : Rating - Low to High (restaurant)
				4 : Price - High to low (product)
				5 : Price - Low to high (product)
			*/

			$lat = !empty($_POST['lat'])?($_POST['lat']):'';
			$lng = !empty($_POST['lng'])?($_POST['lng']):'';
			$is_valid = 0;
			$date_timestamp = !empty($_POST['date_timestamp'])?$this->db->escape_str($_POST['date_timestamp']):'';

			if($date_timestamp == '')
        	{
        		$data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
        	}else if($tokenData === false)
			{
				$tokenData = ''; # Pass empty string if token is false
				# This fn can be called in both cases (Logged in and guest) so we will use token in fn_getrestaurant_data function to check wishlist status
			    # If token is not present that it may be a guest user so in such case we need lat long
				if($lat == ''){
			        $data['status']		=201;
			        $data['message']	=$this->lang->line('latitude_missing');
			        $data['data']		=array();
				}else if($lng == ''){
			        $data['status']		=201;
			        $data['message']	=$this->lang->line('longitude_missing');
			        $data['data']		=array();
				}
				else
				{
					$is_valid = 1;
				}
			}else
			{
				$is_valid = 1;
				$latlong = $this->Common->getData('users','latitude,longitude','id = "'.$tokenData->id.'"');
				$lat = $latlong[0]['latitude'];
				$lng = $latlong[0]['longitude'];
			}

			if($is_valid == 1)
			{
				# DB _business_type = 1 Food 2 Grocery 3 Alcohol
	    		$business_type = !empty($_POST['business_type'])?$this->db->escape_str($_POST['business_type']):'';
	    		# DB Food type : 1 (Restaurant) 2 (Kitchen/homemade)
	        	$food_type = !empty($_POST['food_type'])?$this->db->escape_str($_POST['food_type']):'';
				
				if($sort_type == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('sort_type_missing');
	                $data['data']		=array();	
				}else if($business_type == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('business_type_missing');
	                $data['data']		=array();	
				}else if($food_type == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('food_type_missing');
	                $data['data']		=array();	
				}
				else
				{
					if($sort_type == 1) # Sort restaurant by popularity. So here we will get data for trending restaurants
					{
						# POPULAR START
			        	# TARGET ORDERS (Most ordered restaurant)
			        	if($order_type == 1)
		            	{
		            		$where = 'restaurants.business_type = "'.$business_type.'" AND food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_now_accept = 1 ';
		            	}elseif($order_type == 2)
		            	{
		            		$where = 'restaurants.business_type = "'.$business_type.'" AND food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_self_pickup_accept = 1 ';
		            	}elseif($order_type == 3)
		            	{
		            		$where = 'restaurants.business_type = "'.$business_type.'" AND food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_later_accept = 1 ';
		            	}else
		            	{
		            		$where = 'restaurants.business_type = "'.$business_type.'" AND food_type = "'.$food_type.'" AND restaurants.rest_status = 1  ';
		            	}
			        	
			        	// $query = "SELECT orders.restaurant_id,restaurants.*,COUNT(orders.restaurant_id) AS order_count,users.latitude,users.longitude FROM orders INNER JOIN restaurants ON restaurants.id = orders.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY orders.restaurant_id ORDER BY order_count DESC LIMIT ".$page.','.$limit;
			        	$query = "SELECT orders.restaurant_id,restaurants.*,COUNT(orders.restaurant_id) AS order_count,users.latitude,users.longitude FROM orders INNER JOIN restaurants ON restaurants.id = orders.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY orders.restaurant_id ORDER BY order_count DESC";
			        	$popularity_one = $this->Common->custom_query($query,"get");
			        	if(count($popularity_one) > 0)
			        	{
			        		# Calling common function to Get basic delivery time and basic preparation time from setting table
				        	$hours = $this->delivery_preparation_time();
				        	# Get all the basic restaurant from common funtion
				    		$response_pop_one = $this->get_restaurant_data($popularity_one,$hours,$tokenData,$lat,$lng,$date_timestamp);

				    		$response_open_close = $this->apply_open_close_check_and_get_data($response_pop_one,$order_type);

				    		$response = $response_open_close;
			        	}else
			        	{
			        		// $where = 'restaurants.business_type = "'.$business_type.'" AND food_type = "'.$food_type.'"';
			        		# TARGET WISHLIST (Most LIKED restaurant)

			        		// $query = "SELECT wishlist.restaurant_id,restaurants.*,COUNT(wishlist.restaurant_id) AS wishlist_count,users.latitude,users.longitude FROM wishlist INNER JOIN restaurants ON restaurants.id = wishlist.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY wishlist.restaurant_id ORDER BY wishlist_count DESC LIMIT ".$page.','.$limit;
			        		$query = "SELECT wishlist.restaurant_id,restaurants.*,COUNT(wishlist.restaurant_id) AS wishlist_count,users.latitude,users.longitude FROM wishlist INNER JOIN restaurants ON restaurants.id = wishlist.restaurant_id INNER JOIN users ON users.id = restaurants.admin_id WHERE $where GROUP BY wishlist.restaurant_id ORDER BY wishlist_count DESC";
			        		$popularity_two = $this->Common->custom_query($query,"get");
			        		if(count($popularity_two) > 0)
				        	{
				        		# Calling common function to Get basic delivery time and basic preparation time from setting table
					        	$hours = $this->delivery_preparation_time();
					        	# Get all the basic restaurant from common funtion
					    		$response_pop_two = $this->get_restaurant_data($popularity_two,$hours,$tokenData,$lat,$lng,$date_timestamp);
					    		$response_pop_two_open_close = $this->apply_open_close_check_and_get_data($response_pop_two,$order_type);
					    		$response = $response_pop_two_open_close;
				        	}else
				        	{
				        		if($order_type == 1)
				            	{
				            		$where = array('business_type' => $business_type , 'avg_rating' => 'BETWEEN 3 AND 5' , 'food_type' => $food_type ,'restaurants.rest_status' => 1 , 'is_order_now_accept' => 1 );
				            	}elseif($order_type == 2)
				            	{
				            		$where = array('business_type' => $business_type , 'avg_rating' => 'BETWEEN 3 AND 5' , 'food_type' => $food_type ,'restaurants.rest_status' => 1 , 'is_self_pickup_accept' => 1 );
				            	}elseif($order_type == 3)
				            	{
				            		$where = array('business_type' => $business_type , 'avg_rating' => 'BETWEEN 3 AND 5' , 'food_type' => $food_type ,'restaurants.rest_status' => 1 , 'is_order_later_accept' => 1 );
				            	}else
				            	{
				            		$where = array('business_type' => $business_type , 'avg_rating' => 'BETWEEN 3 AND 5' , 'food_type' => $food_type ,'restaurants.rest_status' => 1);
				            	}
				        		# TARGET RATING (3 to 5)
				        		
								// $popularity_three = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC',$limit,$page);
								$popularity_three = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude',$where,array('users'),array('users.id = restaurants.admin_id'),'restaurants.avg_rating','DESC');
								if(count($popularity_three) > 0)
								{
									# Calling common function to Get basic delivery time and basic preparation time from setting table
						        	$hours = $this->delivery_preparation_time();
						        	# Get all the basic restaurant from common funtion
						    		$response_pop_three = $this->get_restaurant_data($popularity_three,$hours,$tokenData,$lat,$lng,$date_timestamp);
						    		$response_pop_three_open_close = $this->apply_open_close_check_and_get_data($response_pop_three,$order_type);
						    		$response = $response_pop_three_open_close;
								}else
								{
									$data['status']		=201;
						            $data['message']	=$this->lang->line('no_data_found');
						            $data['data']		=array(); 
								}
				        	}
			        	}
			        	# POPULAR END
					}else if($sort_type == 2) # 2 : Rating - High to low (restaurant) 
					{
						if($order_type == 1)
		            	{
		            		// $rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_now_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_now_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}elseif($order_type == 2)
		            	{
		            		// $rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_self_pickup_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_self_pickup_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}elseif($order_type == 3)
		            	{
		            		// $rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_later_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_later_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}else
		            	{
		            		// $rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 ',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 ',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}

						if(count($rating_hightolow) > 0)
			        	{
			        		# Calling common function to Get basic delivery time and basic preparation time from setting table
				        	$hours = $this->delivery_preparation_time();
				        	# Get all the basic restaurant from common funtion
				    		$rating_htl = $this->get_restaurant_data($rating_hightolow,$hours,$tokenData,$lat,$lng,$date_timestamp);

				    		$response_htl_open_close = $this->apply_open_close_check_and_get_data($rating_htl,$order_type);
				    		$response = $response_htl_open_close;
			        	}
					}else if($sort_type == 3) # Rating - Low to High (restaurant)
					{
						if($order_type == 1)
		            	{
		            		// $rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_now_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','ASC',$limit,$page);
		            		$rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_now_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','ASC');
		            	}elseif($order_type == 2)
		            	{
		            		// $rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_self_pickup_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','ASC',$limit,$page);
		            		$rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_self_pickup_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','ASC');
		            	}elseif($order_type == 3)
		            	{
		            		// $rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_later_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','ASC',$limit,$page);
		            		$rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_later_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','ASC');
		            	}else
		            	{
		            		// $rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 ',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','ASC',$limit,$page);
		            		$rating_hightolow = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 ',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','ASC');
		            	}
						if(count($rating_hightolow) > 0)
			        	{
			        		# Calling common function to Get basic delivery time and basic preparation time from setting table
				        	$hours = $this->delivery_preparation_time();
				        	# Get all the basic restaurant from common funtion
				    		$rating_htl = $this->get_restaurant_data($rating_hightolow,$hours,$tokenData,$lat,$lng,$date_timestamp);
				    		$response_htl_open_close = $this->apply_open_close_check_and_get_data($rating_htl,$order_type);
				    		$response = $response_htl_open_close;
			        	}
					}else if($sort_type == 4) # Price - High to low (product) (First get restaturant high to low rating along with their products based on price high to low)
					{
						if($order_type == 1)
		            	{
		            		// $type_four = $this->Common->getData('restaurants','restaurants.id as restaurant_id,restaurants.rest_name,restaurants.avg_rating,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'"',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		// $type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_now_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_now_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}elseif($order_type == 2)
		            	{
		            		// $type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_self_pickup_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_self_pickup_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}elseif($order_type == 3)
		            	{
		            		// $type_four = $this->Common->getData('restaurants','restaurants.id as restaurant_id,restaurants.*,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_later_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$type_four = $this->Common->getData('restaurants','restaurants.id as restaurant_id,restaurants.*,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_later_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}else
		            	{
		            		// $type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 ',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 ',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}
		            	// echo "<pre>";
		            	// print_r($type_four);

						if(count($type_four) > 0)
			        	{
			        		# Calling common function to Get basic delivery time and basic preparation time from setting table
				        	$hours = $this->delivery_preparation_time();
				        	# Get all the basic restaurant from common funtion
				        	# sort_type_price is the last param : 1 : High to low DESC and 2 : Low to high : ASC
				    		$response = $this->get_restaurant_data_with_products($type_four,$hours,$tokenData,$lat,$lng,1,$page,$limit,$date_timestamp);
				    		$response = $this->apply_open_close_check_and_get_data($response,$order_type);
			        	}
			        	// echo "<pre>";
			        	// print_r($response);
					}else if($sort_type == 5) # Price - Low to High (product) (Here also we will get restaurant as per their ratin gin order HIGH to Low but we will sort product as low to high price so First get restaturant high to low rating along with their products based on price high to low)
					{
						// $type_four = $this->Common->getData('restaurants','restaurants.id as restaurant_id,restaurants.rest_name,restaurants.avg_rating,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'"',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
						if($order_type == 1)
		            	{
		            		// $type_four = $this->Common->getData('restaurants','restaurants.id as restaurant_id,restaurants.rest_name,restaurants.avg_rating,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'"',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		// $type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_now_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_now_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}elseif($order_type == 2)
		            	{
		            		// $type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_self_pickup_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_self_pickup_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}elseif($order_type == 3)
		            	{
		            		// $type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_later_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 AND is_order_later_accept = 1',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}else
		            	{
		            		// $type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 ',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC',$limit,$page);
		            		$type_four = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.business_type = "'.$business_type.'" AND restaurants.food_type = "'.$food_type.'" AND restaurants.rest_status = 1 ',array('users'),array('users.id = restaurants.admin_id'),'avg_rating','DESC');
		            	}
						if(count($type_four) > 0)
			        	{
			        		# Calling common function to Get basic delivery time and basic preparation time from setting table
				        	$hours = $this->delivery_preparation_time();
				        	# Get all the basic restaurant from common funtion
				        	# sort_type_price is the last param : 1 : High to low DESC and 2 : Low to high : ASC
				    		$response = $this->get_restaurant_data_with_products($type_four,$hours,$tokenData,$lat,$lng,2,$page,$limit,$date_timestamp);
				    		// echo "<pre> send resposne";
				    		// print_r($response);die;
				    		$response = $this->apply_open_close_check_and_get_data($response,$order_type);
			        	}
					}

					$data['status']		=200;
			        $data['message']	=$this->lang->line('success');
			        $data['data']		=$response;
				}
			}
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}
	# Filter products End

	# Search screen get


	# This function used to search and return the data as per the written search keyword by the customer based on the selected tab
	# selected_tab = 1 : Restaurant 2 : Product
	public function search_by_keyword_post()
	{
		try{
			$tokenData = $this->verify_request(); # Pass token if user is logged in

	    	$page = !empty($_POST['page'])?$this->db->escape_str($_POST['page']):0;
			$limit = !empty($_POST['limit'])?$this->db->escape_str($_POST['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;

			# DB _business_type = 1 Food 2 Grocery 3 Alcohol
    		$business_type = !empty($_POST['business_type'])?$this->db->escape_str($_POST['business_type']):'';
    		# DB Food type : 1 (Restaurant) 2 (Kitchen/homemade)
        	$food_type = !empty($_POST['food_type'])?$this->db->escape_str($_POST['food_type']):'0';
        	$selected_tab = !empty($_POST['selected_tab'])?$this->db->escape_str($_POST['selected_tab']):''; # 1 for Restaurants 2 Products
        	$keyword = !empty($_POST['keyword'])?$this->db->escape_str(trim(strtolower($_POST['keyword']))):'';
        	$date_timestamp = !empty($_POST['date_timestamp'])?$this->db->escape_str($_POST['date_timestamp']):'';

			$lat = !empty($_POST['lat'])?($_POST['lat']):'';
			$lng = !empty($_POST['lng'])?($_POST['lng']):'';
			$is_valid = 0;

			if($date_timestamp == '')
        	{
        		$data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
        	}else if($tokenData === false)
			{
				$tokenData = ''; # Pass empty string if token is false
				# This fn can be called in both cases (Logged in and guest) so we will use token in fn_getrestaurant_data function to check wishlist status
			    # If token is not present that it may be a guest user so in such case we need lat long
				if($lat == ''){
			        $data['status']		=201;
			        $data['message']	=$this->lang->line('latitude_missing');
			        $data['data']		=array();
				}else if($lng == ''){
			        $data['status']		=201;
			        $data['message']	=$this->lang->line('longitude_missing');
			        $data['data']		=array();
				}
				else
				{
					$is_valid = 1;
				}
			}else
			{
				$is_valid = 1;
				$latlong = $this->Common->getData('users','latitude,longitude','id = "'.$tokenData->id.'"');
				$lat = $latlong[0]['latitude'];
				$lng = $latlong[0]['longitude'];
			}

			if($is_valid == 1)
			{
				if($keyword == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('keyword_missing');
	                $data['data']		=array();
				}else if($selected_tab == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('selected_tab_missing');
	                $data['data']		=array();
				}else if($business_type == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('business_type_missing');
	                $data['data']		=array();	
				}
				// else if($food_type == '')
				// {
				// 	$data['status']		=201;
   				//              $data['message']	=$this->lang->line('food_type_missing');
	   			//              $data['data']		=array();	
				// }
				else
				{
					$response = array();
					$keyword = $this->removeStopWords($keyword);
            		if($selected_tab == 1) # RESTAURANT
            		{
						if(strpos($keyword, " ") !== false) # i.e. space exists : User has provided string with space like (Veg biryani)
	            		{
	            			$keyword_part = explode(" ", $keyword);
	            			$query = "SELECT `restaurants`.*, `restaurants`.`id` as `restaurant_id`, `users`.`latitude`, `users`.`longitude` FROM `restaurants` INNER JOIN `users` ON `users`.`id` = `restaurants`.`admin_id` WHERE `restaurants`.`business_type` = ".$business_type." AND `restaurants`.`food_type` = ".$food_type." AND restaurants.rest_status = 1 AND (";
	            			foreach ($keyword_part as $word) 
		            		{
		            			if($word  != '')
		            			{
		            				$word = rtrim($word,"s");
		            				$query .= "(rest_name LIKE '%".$word."%' OR res_description LIKE '%".$word."%')";
		            				$query .= " OR ";
		            			}
		            		}
		            		$query .= "(rest_name LIKE '%".$word."%' OR res_description LIKE '%".$word."%')) ORDER BY `avg_rating` ASC LIMIT ".$page.",".$limit;
		            		$search_rest = $this->Common->custom_query($query,"get");
	            		}else
	            		{
	            			$keyword = rtrim($keyword,"s"); # In case thali is written as thalis so we will remove s for more results
	            			# Now we will get restaurant ids and data where this keyword exists
	            			$query = "SELECT `restaurants`.*, `restaurants`.`id` as `restaurant_id`, `users`.`latitude`, `users`.`longitude` FROM `restaurants` INNER JOIN `users` ON `users`.`id` = `restaurants`.`admin_id` WHERE `restaurants`.`business_type` = ".$business_type." AND `restaurants`.`food_type` = ".$food_type." AND restaurants.rest_status = 1 AND (rest_name LIKE '%".$keyword."%' OR res_description LIKE '%".$keyword."%') ORDER BY `avg_rating` ASC LIMIT ".$page.",".$limit;
	            			$search_rest = $this->Common->custom_query($query,"get");
	            		}
						if(count($search_rest) > 0)
			        	{
			        		# Calling common function to Get basic delivery time and basic preparation time from setting table
				        	$hours = $this->delivery_preparation_time();
				        	# Get all the basic restaurant from common funtion
				    		$search_result = $this->get_restaurant_data($search_rest,$hours,$tokenData,$lat,$lng,$date_timestamp);
				    		$response = $search_result;

				    		# Also if there is some result found then insert this search in database
				    		# But insert only if there is no search exists with exact keyword
				    		$search_exists = $this->Common->getData('searches','text','text = "'.$keyword.'"');
				    		if(count($search_exists) == 0)
				    		{
			            		$insert_search = [
			            			'text' => $keyword, //already to lower and trimmed
			            			'created_at' => time(),
			            		];
			            		$this->Common->insertData('searches',$insert_search);
				    		}
							$data['status']		=200;
					        $data['message']	=$this->lang->line('success');
					        $data['data']		=$response;
			        	}else
			        	{
			        		$data['status']		=201;
					        $data['message']	=$this->lang->line('no_data_found');
					        $data['data']		=array();		
			        	}
            		}else if($selected_tab == 2) # PRODUCT
            		{
            			if(strpos($keyword, " ") !== false) # i.e. space exists : User has provided string with space like (Veg biryani)
	            		{
	            			$keyword_part = explode(" ", $keyword);
	            			$query_part_one = "SELECT products.id AS product_id,products.product_name ,products.min_qty,products.price,products.offer_price,products.product_image,products.restaurant_id AS product_rest_id , products_offline.id AS off_id ,products_offline.rest_id , products_offline.category_id , products_offline.offline_tag,products_offline.offline_value,products_offline.offline_from,products_offline.offline_to 
	            			FROM products 
	            			INNER JOIN categories ON categories.id = products.category_id
	            			INNER JOIN restaurants ON restaurants.id = products.restaurant_id
	            			LEFT OUTER JOIN products_offline ON products.id = products_offline.product_id WHERE restaurants.business_type = ".$business_type." AND restaurants.food_type = ".$food_type." AND restaurants.rest_status = 1 AND (";

	            			foreach ($keyword_part as $word) 
		            		{
		            			if($word  != '')
		            			{
		            				$word = rtrim($word,"s");
		            				$query_part_one .= "(product_name LIKE '%".$word."%' OR short_desc LIKE '%".$word."%' OR long_desc LIKE '%".$word."%' OR category_name LIKE '%".$word."%' OR categories.description LIKE '%".$word."%')";
		            				$query_part_one .= " OR ";
		            			}
		            		}
		            		$query_part_one .= "(product_name LIKE '%".$keyword."%' OR short_desc LIKE '%".$keyword."%' OR long_desc LIKE '%".$keyword."%' OR category_name LIKE '%".$keyword."%' OR categories.description LIKE '%".$keyword."%')) ";

		            		$query = $query_part_one." ORDER BY products.price ASC LIMIT ".$page.",".$limit;
		            		$search_product = $this->Common->custom_query($query,"get");
	            		}else
	            		{
	            			$keyword = rtrim($keyword,"s"); # In case thali is written as thalis so we will remove s for more results
	            			# Now we will get restaurant ids and data where this keyword exists
	            			$query_part_one = "SELECT products.id AS product_id,products.product_name ,products.min_qty,products.price,products.offer_price,products.product_image,products.restaurant_id AS product_rest_id , products_offline.id AS off_id ,products_offline.rest_id , products_offline.category_id , products_offline.offline_tag,products_offline.offline_value,products_offline.offline_from,products_offline.offline_to 
	            			FROM products 
	            			INNER JOIN categories ON categories.id = products.category_id
	            			INNER JOIN restaurants ON restaurants.id = products.restaurant_id
	            			LEFT OUTER JOIN products_offline ON products.id = products_offline.product_id WHERE restaurants.business_type = ".$business_type." AND restaurants.food_type = ".$food_type." AND restaurants.rest_status = 1 AND (product_name LIKE '%".$keyword."%' OR short_desc LIKE '%".$keyword."%' OR long_desc LIKE '%".$keyword."%' OR category_name LIKE '%".$keyword."%' OR categories.description LIKE '%".$keyword."%')";
	            			$query = $query_part_one." ORDER BY products.price ASC LIMIT ".$page.",".$limit;
	            			$search_product = $this->Common->custom_query($query,"get");
	            		}
	            		
	            		# Now it may possible that some products may have same restro id so it may created duplicacy in data so first get restro id and find their unique array and then run forach for the unique array of restro ids
	            		foreach ($search_product as $key => $value) 
		        		{
		        			$found_restro[] = $value['product_rest_id'];
		        		}
		        		$unique_resto_id =  array_unique($found_restro);
		        		
	            		if(count($unique_resto_id) > 0)
			        	{
			        		# Now get restro id of the found products becacuse we need to send resto info also
			        		foreach ($unique_resto_id as $key => $value) 
			        		{
			        			$get_restro_query = "SELECT restaurants.*,restaurants.id AS restaurant_id, users.latitude, users.longitude FROM restaurants INNER JOIN users ON users.id = restaurants.admin_id WHERE restaurants.id = ".$value;
	            				$search_rest_prdct = $this->Common->custom_query($get_restro_query,"get");
	            				$search_rest_prd[] = $search_rest_prdct[0];
	            				
			        		}
			        		# Calling common function to Get basic delivery time and basic preparation time from setting table
				        	$hours = $this->delivery_preparation_time();
				        	# Get all the basic restaurant from common funtion
				    		$search_result_pr = $this->get_restaurant_data_with_searched_products($search_rest_prd,$hours,$tokenData,$lat,$lng,$query_part_one,$page,$limit,$date_timestamp);
				    		$response = $search_result_pr;

				    		# Also if there is some result found then insert this search in database
				    		# But insert only if there is no search exists with exact keyword
				    		$search_exists = $this->Common->getData('searches','text','text = "'.$keyword.'"');
				    		if(count($search_exists) == 0)
				    		{
			            		$insert_search = [
			            			'text' => $keyword, //already to lower and trimmed
			            			'created_at' => time(),
			            		];
			            		$this->Common->insertData('searches',$insert_search);
				    		}
							$data['status']		=200;
					        $data['message']	=$this->lang->line('success');
					        $data['data']		=$response;
			        	}else
			        	{
			        		$data['status']		=201;
					        $data['message']	=$this->lang->line('no_data_found');
					        $data['data']		=array();		
			        	}
            		}
				}
			}
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}


	public function apply_open_close_check_and_get_data($response , $order_type)
	{
		// echo "newfn<pre>";
		// print_r($response);die;
		if(count($response) > 0)
		{
			# START FROM HERE
			foreach ($response as $key => $value) 
			{
				# Check whether offline status key is empty?
				$arr = (array)$value['offline_status'];
				if(!isset($arr['offline_tag']))
				{
					// echo "1<br>";
					# That means there is no entry in offline table so now check for open close time AND then break start and end time
					// echo "current_time ".$current_time = date("H:i"); # Ex : 15:20 (3H : 20m)
					// echo "CHANCHAL ".time();
					$from = new DateTimeZone('Asia/Singapore');
					$currDate  = new DateTime('now', $from);
					// $current_time = date($date,"H:i");
					$current_time = $currDate->format('H:i');
					// echo "Current time " . $current_time;
					// echo "<br>The time in " . date_default_timezone_get() . " is " . date("H:i");
					$open_time = $value['restaurant']['open_time'];
					$close_time = $value['restaurant']['close_time'];
					// echo "<br>";
					if($current_time >= $open_time && $current_time <= $close_time) 
					# Ex open time is 10 and close is 18 and current time is 13
					# So 13 >= 10 AND 13 <= 18 ====> It is OPEN
					{
						// echo "3<br>";
						$break_start_time = $value['restaurant']['break_start_time'];
						$break_end_time = $value['restaurant']['break_end_time'];
						# Check for break start and break end
						# If current time is in between Open and close then restro is OPEN
						# BUT if current time is in between Break start and Break end then restro is CLOSED
						if($current_time >= $break_start_time && $current_time <= $break_end_time)
						{
							// echo "4<br>";
							# It is closed
							// $response_return[$key]['closed_restro'] = $value;
							$response_return[$key] = $value;
							$response_return[$key]['is_open'] = 0;
						}else
						{
							// echo "5<br>";
							# Send it in open 
							$response_return[$key] = $value;
							$response_return[$key]['is_open'] = 1;
						}
					}else
					{
						// echo "6<br>";
						# It is closed
						$response_return[$key] = $value;
						$response_return[$key]['is_open'] = 0;
					}
				}else
				{
					// echo "2<br>";
					$arr_o = (array)$value['offline_status'];
					# That is entry in offline table exists. So if current time is less than offline_from and greater than offline_to that means restaurant is OPEN
					if($arr_o['offline_from'] != 0)
					{
						# That is some valid value exists
						$offline_from = $arr_o['offline_from'];
						$offline_to = $arr_o['offline_to'];
						// echo "<br>timeis ".time();
						// echo "TIMESTAMP".time();
						if($offline_from <= time() && time() >= $offline_to)
						{
							// echo "CAMEHEREEEE?";
							# That is it is OPEN now.
							# So check for open close time and THEN break start end time
							$from = new DateTimeZone('Asia/Singapore');
							$currDate  = new DateTime('now', $from);
							$current_time = $currDate->format('H:i');

							$open_time = $value['restaurant']['open_time'];
							$close_time = $value['restaurant']['close_time'];

							if($current_time >= $open_time && $current_time <= $close_time) 
							# Ex open time is 10 and close is 18 and current time is 13
							# So 13 >= 10 AND 13 <= 18 ====> It is OPEN
							{
								// echo "WW";
		    					$break_start_time = $value['restaurant']['break_start_time'];
		    					$break_end_time = $value['restaurant']['break_end_time'];
								# Check for break start and break end
								# If current time is in between Open and close then restro is OPEN
								# BUT if current time is in between Break start and Break end then restro is CLOSED
								if($current_time >= $break_start_time && $current_time <= $break_end_time)
								{
									// echo "QQ";
									# It is closed
									$response_return[$key] = $value;
									$response_return[$key]['is_open'] = 0;
								}else
								{
									// echo "EE";
									# Send it in open 
									$response_return[$key] = $value;
									$response_return[$key]['is_open'] = 1;
								}
							}else
							{
								// echo "RR";
								# It is closed
								$response_return[$key] = $value;
								$response_return[$key]['is_open'] = 0;
							}
						}else
						{
							// echo "TT";
							# It is closed
							$response_return[$key] = $value;
							$response_return[$key]['is_open'] = 0;
						}
					}
				}
			}
		}
		# Now we need to first send open and then closed restaurants
		if(count($response) > 0)
		{
			$open = array();
			$close = array();
			foreach ($response as $key => $value) 
			{
				if($value['restro_is_open'] == 1)
				{
					$open[] = $value;
				}else
				{
					$close[] = $value;
				}
			}
		}

		# If order_type is Order Now then DONOT send closed restaurants
		if($order_type == 1)
		{
			$final_array = $open;							
		}else
		{
			$final_array = array_merge($open,$close);
		}

		return $final_array;
	}

	# This functions returns thet text based on the characters written by the customer to give auto complete suggestions also returns recent searches
	public function search_autocomplete_suggestion_post()
	{
		try{
			$tokenData = $this->verify_request(); # Pass token if user is logged in

			# DB _business_type = 1 Food 2 Grocery 3 Alcohol
    		$business_type = !empty($_POST['business_type'])?$this->db->escape_str($_POST['business_type']):'';
    		# DB Food type : 1 (Restaurant) 2 (Kitchen/homemade)
        	$food_type = !empty($_POST['food_type'])?$this->db->escape_str($_POST['food_type']):'0';
        	$selected_tab = !empty($_POST['selected_tab'])?$this->db->escape_str($_POST['selected_tab']):''; # 1 for Restaurants 2 Products
        	$keyword = !empty($_POST['keyword'])?$this->db->escape_str(trim(strtolower($_POST['keyword']))):'';
        	$is_valid = 1;
			if($is_valid == 1)
			{
				if($business_type == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('business_type_missing');
	                $data['data']		=array();	
				}
				// else if($food_type == '')
				// {
				// 	$data['status']		=201;
	   //              $data['message']	=$this->lang->line('food_type_missing');
	   //              $data['data']		=array();	
				// }
				else if($selected_tab == '')
				{
					$data['status']		=201;
	                $data['message']	=$this->lang->line('selected_tab_missing');
	                $data['data']		=array();	
				}else
				{
					$autocomplete_search_result = array();
					if($keyword != '')
					{
						if($selected_tab == 1)
						{
							if(strpos($keyword, " ") !== false) # i.e. space exists : User has provided string with space like (Veg biryani)
		            		{
		            			$keyword_part = explode(" ", $keyword);
		            			$query = "SELECT `restaurants`.`rest_name`, `restaurants`.`id` as `restaurant_id` , `restaurants`.`business_type` , `restaurants`.`food_type` , `restaurants`.`rest_status` FROM `restaurants` WHERE `restaurants`.`business_type` = ".$business_type." AND `restaurants`.`food_type` = ".$food_type." AND restaurants.rest_status = 1 AND (";
		            			foreach ($keyword_part as $word) 
			            		{
			            			if($word  != '')
			            			{
			            				$word = rtrim($word,"s");
			            				$query .= "(rest_name LIKE '%".$word."%' OR res_description LIKE '%".$word."%')";
			            				$query .= " OR ";
			            			}
			            		}
			            		$query .= "(rest_name LIKE '%".$word."%' OR res_description LIKE '%".$word."%')) LIMIT ".AUTOCOMPLETE_SEARCH_LIMIT;
			            		$autocomplete_search_result = $this->Common->custom_query($query,"get");
		            		}else
		            		{
		            			$keyword = rtrim($keyword,"s"); # In case thali is written as thalis so we will remove s for more results
		            			# Now we will get restaurant ids and data where this keyword exists
		            			$query = "SELECT `restaurants`.`rest_name`, `restaurants`.`id` as `restaurant_id` , `restaurants`.`business_type` , `restaurants`.`food_type` , `restaurants`.`rest_status` FROM `restaurants` WHERE `restaurants`.`business_type` = ".$business_type." AND `restaurants`.`food_type` = ".$food_type." AND restaurants.rest_status = 1 AND (rest_name LIKE '%".$keyword."%' OR res_description LIKE '%".$keyword."%') LIMIT ".AUTOCOMPLETE_SEARCH_LIMIT;
		            			$autocomplete_search_result = $this->Common->custom_query($query,"get");
		            		}
						}else if($selected_tab == 2) # PRODUCT
						{
							if(strpos($keyword, " ") !== false) # i.e. space exists : User has provided string with space like (Veg biryani)
		            		{
		            			$keyword_part = explode(" ", $keyword);
		            			$query_part_one = "SELECT products.id AS product_id,products.product_name,products.min_qty
		            			FROM products 
		            			INNER JOIN categories ON categories.id = products.category_id
		            			INNER JOIN restaurants ON restaurants.id = products.restaurant_id
		            			LEFT OUTER JOIN products_offline ON products.id = products_offline.product_id WHERE restaurants.business_type = ".$business_type." AND restaurants.food_type = ".$food_type." AND restaurants.rest_status = 1 AND (";

		            			foreach ($keyword_part as $word) 
			            		{
			            			if($word  != '')
			            			{
			            				$word = rtrim($word,"s");
			            				$query_part_one .= "(product_name LIKE '%".$word."%' OR short_desc LIKE '%".$word."%' OR long_desc LIKE '%".$word."%' OR category_name LIKE '%".$word."%' OR categories.description LIKE '%".$word."%')";
			            				$query_part_one .= " OR ";
			            			}
			            		}
			            		$query_part_one .= "(product_name LIKE '%".$keyword."%' OR short_desc LIKE '%".$keyword."%' OR long_desc LIKE '%".$keyword."%' OR category_name LIKE '%".$keyword."%' OR categories.description LIKE '%".$keyword."%')) ";
			            		$query = $query_part_one." LIMIT ".AUTOCOMPLETE_SEARCH_LIMIT;
			            		$autocomplete_search_result = $this->Common->custom_query($query,"get");
		            		}else
		            		{
		            			$keyword = rtrim($keyword,"s"); # In case thali is written as thalis so we will remove s for more results
		            			# Now we will get restaurant ids and data where this keyword exists
		            			$query_part_one = "SELECT products.id AS product_id,products.product_name,products.min_qty
		            			FROM products 
		            			INNER JOIN categories ON categories.id = products.category_id
		            			INNER JOIN restaurants ON restaurants.id = products.restaurant_id
		            			LEFT OUTER JOIN products_offline ON products.id = products_offline.product_id WHERE restaurants.business_type = ".$business_type." AND restaurants.food_type = ".$food_type." AND restaurants.rest_status = 1 AND (product_name LIKE '%".$keyword."%' OR short_desc LIKE '%".$keyword."%' OR long_desc LIKE '%".$keyword."%' OR category_name LIKE '%".$keyword."%' OR categories.description LIKE '%".$keyword."%')";
		            			$query = $query_part_one." LIMIT ".AUTOCOMPLETE_SEARCH_LIMIT;
		            			$autocomplete_search_result = $this->Common->custom_query($query,"get");
		            		}
						}
					}

					if(count($autocomplete_search_result) > 0)
					{
						foreach($autocomplete_search_result as $index => $result)
						{
							$response[$index] = $result;
							$response[$index]['rest_name'] = stripslashes($result['rest_name']);
						}
					}

					# RECENT SEARCH
					// $query = "SELECT searches.text FROM searches ORDER BY id DESC LIMIT 5";
					// $recent_search = $this->Common->custom_query($query,'get');

					$data['status']		=200;
	                $data['message']	=$this->lang->line('success');
	                $data['data']		=$response;
				}
			}
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# Remove common stop words function start
    # This function is used to remove the common stop words
    private function removeStopWords($input)
    {
		$commonWords = array('is','am','are','a','for','to','the','this','then','there','here','these','those','it','do','does','from','etc','than');
		$input = strtolower($input);
		return preg_replace('/\b('.implode('|',$commonWords).')\b/','',$input);
	}
    # Remove common stop words function end


	# VARIANT SECTION #
	public function customize_items_get()
	{
		try{
	    	$tokenData = $this->verify_request();

	    	$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
			$product_id = !empty($_GET['product_id'])?$this->db->escape_str($_GET['product_id']):'';
            
            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($product_id == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('product_missing');
                $data['data']		=array();	
			}else
            {
        		# Get variant from Database if available
        		$query = "SELECT `variant_id`,`variant_type_id`,single_select , multi_select_limit , is_mandatory FROM `variant_types_for_products` WHERE `variant_types_for_products`.`product_id` = ".$product_id." GROUP BY variant_id";
        		$has_variant = $this->Common->custom_query($query , 'get');
        		// echo $this->db->last_query();
        		// echo "<pre>";
        		// print_r($has_variant);
        		// echo "<hr>";
            	if(count($has_variant) > 0)
            	{
            		foreach ($has_variant as $key => $value) 
            		{
            			$sub_name = array();
            			# single_select : 1 : single_select 2 : multi_select
            			# multi_select_limit : If "single_select" column value is 2 (It is multiselect) , and this column value is 0 then user can select unlimited variant types, but value greater than 0 , (means how many variant type can be selected by user in a variant)
            			$only_name = $this->Common->getData('variants','variants.variant_id,variants.variant_name','variants.variant_id = "'.$value['variant_id'].'" AND variant_status = 1'); # 1- enable, 2 - disable
            			if(count($only_name) > 0)
            			{
	            			$response[$key] = $only_name[0];
							$response[$key]['single_select'] = $value['single_select'];
							$response[$key]['multi_select_limit'] = $value['multi_select_limit'];
							$response[$key]['is_mandatory'] = $value['is_mandatory'];
	            			# variant_type_status : 1 - Enable, 2- Disable
	            			$query_ab = "SELECT `variants`.`variant_id`,`variant_types`.`variant_type_id`, `variant_types`.`variant_type_name`, `variant_types_for_products`.`variant_type_price` , `variant_types_for_products`.`default_variant_status` FROM `variants` INNER JOIN `variant_types` ON `variant_types`.`variant_id` = `variants`.`variant_id` INNER JOIN `variant_types_for_products` ON `variant_types_for_products`.`variant_type_id` = `variant_types`.`variant_type_id` WHERE `variants`.`variant_id` = ".$value['variant_id']." AND `variant_types`.`variant_type_status` = 1 AND `variant_types_for_products`.`product_id` = ".$product_id." GROUP BY variant_type_id";
	            			$ab = $this->Common->custom_query($query_ab,'get');
	            			// echo $this->db->last_query();
	            			// echo "PRINTAB<pre>";
	            			// print_r($ab);
    						
	            			if(count($ab) > 0)
	            			{
		            			# Also we need to check whether this variant is added by user for this product?
	            				# First get cart Id as per product Id and user id(token id)
								$cart_id = $this->Common->getData('cart','id','product_id = "'.$product_id.'" AND user_id = "'.$tokenData->id.'"');
								foreach ($ab as $ab_key => $ab_value) 
								{
									if(count($cart_id) > 0)
									{
										# That means this product is available in user's cart (obviously)
										$user_has_this_variant = $this->Common->getData('cart_variant','id','cart_id = "'.$cart_id[0]['id'].'" AND product_id = "'.$product_id.'" AND variant_id = "'.$ab_value['variant_id'].'" AND variant_type_id = "'.$ab_value['variant_type_id'].'"');
										# is_this_variant_used that mean if user has selected this then send 1 else 0 to display checked or unchecked for mobile screen
										if(count($user_has_this_variant) > 0)
										{
											$sub_name[$ab_key] = $ab_value;
											$sub_name[$ab_key]['is_this_variant_used'] = 1;
										}else
										{
											$sub_name[$ab_key] = $ab_value;
											$sub_name[$ab_key]['is_this_variant_used'] = 0;
										}
									}else
									{
										$sub_name[$ab_key] = $ab_value;
										$sub_name[$ab_key]['is_this_variant_used'] = 0;			
									}
								}
								$response[$key]['sub_name'] = $sub_name;
	            			}

            			}
            		}
            		
            		# Cart detail get
            		$cart_response = $this->cart_calculation($tokenData);
	        		$cart_detail['item_total'] = $cart_response['item_total'];
	        		$cart_detail['items'] = $cart_response['items'];
	        		$cart_detail['rest_id'] = $cart_response['restaurant_id'];

            		if(count($response) > 0)
            		{
			        	$data['status']		=200;
		                $data['message']	=$this->lang->line('success');
		                $data['data']		=array('items' => $response , 'cart' => $cart_detail);
            		}else
            		{
            			$data['status']		=201;
		                $data['message']	=$this->lang->line('no_data_found');
		                $data['data']		=array();
            		}
            	}else
            	{
            		$data['status']		=201;
	                $data['message']	=$this->lang->line('no_data_found');
	                $data['data']		=array();
            	}
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# This function is used to add or remove vaiants from customize screen
	public function add_remove_variant_cart_post()
	{
		try{
	    	$tokenData = $this->verify_request();
	    	$prod_id = !empty($_POST['product_id'])?$this->db->escape_str($_POST['product_id']):'';
	    	$variant_id = !empty($_POST['variant_id'])?$this->db->escape_str($_POST['variant_id']):'';
	    	$variant_type_id = !empty($_POST['variant_type_id'])?$this->db->escape_str($_POST['variant_type_id']):'';
	    	$single_select = !empty($_POST['single_select'])?$this->db->escape_str($_POST['single_select']):''; #1 - single select 2 multi select
	    	$action = !empty($_POST['action'])?$this->db->escape_str($_POST['action']):'';# 1 add 2 remove
	    	$multi_select_limit = $_POST['multi_select_limit']; # $multi_select_limit == 0 that means unlimited can be added but is $multi_select_limit > 0 then only that much can be added

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($prod_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('product_missing');
                $data['data']		=array();
            }else if($variant_id == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('variant_id_missing');
                $data['data']		=array();
            }else if($variant_type_id == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('variant_type_id_missing');
                $data['data']		=array();
            }else if($action == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('action_missing');
                $data['data']		=array();
            }else if($single_select == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('single_select_missing');
                $data['data']		=array();
            }else if($multi_select_limit == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('multi_select_limit_missing');
                $data['data']		=array();
            }else
            {
            	# First get cart Id as per product Id and user id(token id)
				$cart_id = $this->Common->getData('cart','id','product_id = "'.$prod_id.'" AND user_id = "'.$tokenData->id.'"');
				# It should be make sure that variants can be added only after adding product to cart.
				# $cart_id is the unique idenity for WHERE checks.
				if(count($cart_id) > 0)
				{
					$cart_id = $cart_id[0]['id'];
					if($action == 1) # Add
					{
						# Now we have cart id and other details so we can add this variant to table. 
						# But we need to check if this variant allow only single select to first remove other and then add this.
						if($single_select == 1) #1 - single select 2 multi select
						{
							# Single select means a radio behavior
							# Check whether given prod_id exists with this variant of given cart id? If yes then first remove that and then add this one
							$check_avl = $this->Common->getData('cart_variant','id', 'cart_id = "'.$cart_id.'" AND variant_id = "'.$variant_id.'" AND product_id = "'.$prod_id.'"');
							if(count($check_avl) > 0)
							{
								# That means we first need to remove 
								$this->Common->deleteData('cart_variant','cart_id = "'.$cart_id.'" AND variant_id = "'.$variant_id.'" AND product_id = "'.$prod_id.'"');
							}
							# Now add after delete
							$insert_variant = [
								'cart_id' => $cart_id,
								'variant_id' => $variant_id,
								'variant_type_id' => $variant_type_id,
								'product_id' => $prod_id,
								'created_at' => time(),
								'updated_at' => time()
							];
							$return_id = $this->Common->insertData('cart_variant',$insert_variant);
							$var_data = $this->Common->getData('variants','*','variants.variant_id = "'.$variant_id.'" AND variant_types_for_products.variant_type_id = "'.$variant_type_id.'"',array('variant_types_for_products'),array('variant_types_for_products.variant_id = variants.variant_id'));
							$data['status']		=200;
				            $data['message']	=$this->lang->line('variant_add_success');
				            $data['data']		=$var_data;
						}else
						{
							# Now in multiselect we have limit that how many can be selected by user. If $single_select == 2 that is it is multi select and if $multi_select_limit == 0 that means unlimited can be added but is $multi_select_limit > 0 then only that much can be added
							# Multiselect to direct insert
							if($multi_select_limit == 0) # unlimited
							{
								$insert_variant = [
									'cart_id' => $cart_id,
									'variant_id' => $variant_id,
									'variant_type_id' => $variant_type_id,
									'product_id' => $prod_id,
									'created_at' => time(),
									'updated_at' => time()
								];
								$this->Common->insertData('cart_variant',$insert_variant);
							}else # i.e. it has limit
							{
								# So first check how many are added already
								# Suppose we have limit of 3 in case of multiselection.
								# So if count of already added is less than 3 then its okay ; add this one also
								# Else if count of already added == limit then remove the first one and add this one
								
								$check_avl = $this->Common->getData('cart_variant','id', 'cart_id = "'.$cart_id.'" AND variant_id = "'.$variant_id.'" AND product_id = "'.$prod_id.'"');
								if(count($check_avl) < $multi_select_limit)
								{
									$insert_variant = [
										'cart_id' => $cart_id,
										'variant_id' => $variant_id,
										'variant_type_id' => $variant_type_id,
										'product_id' => $prod_id,
										'created_at' => time(),
										'updated_at' => time()
									];
									$this->Common->insertData('cart_variant',$insert_variant);
								}else
								{
									# Remove 0 index and then add this newly added
									$this->Common->deleteData('cart_variant','id = "'.$check_avl[0]['id'].'"');
									$insert_variant = [
										'cart_id' => $cart_id,
										'variant_id' => $variant_id,
										'variant_type_id' => $variant_type_id,
										'product_id' => $prod_id,
										'created_at' => time(),
										'updated_at' => time()
									];
									$this->Common->insertData('cart_variant',$insert_variant);
								}
							}
							$var_data = $this->Common->getData('variants','*','variants.variant_id = "'.$variant_id.'" AND variant_types_for_products.variant_type_id = "'.$variant_type_id.'"',array('variant_types_for_products'),array('variant_types_for_products.variant_id = variants.variant_id'));
							$data['status']		=200;
				            $data['message']	=$this->lang->line('variant_add_success');
				            $data['data']		=$var_data;
						}
					}else if($action == 2) # Remove
					{
						$this->Common->deleteData('cart_variant','cart_id = "'.$cart_id.'" AND variant_id = "'.$variant_id.'" AND product_id = "'.$prod_id.'" AND variant_type_id = "'.$variant_type_id.'"');
						$data['status']		=200;
				        $data['message']	=$this->lang->line('variant_remove_success');
				        $data['data']		=array();

					}
				}else
				{
					$data['status']		=201;
				    $data['message']	=$this->lang->line('first_add_to_cart');
				    $data['data']		=array();		
				}
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# This function is used to return the cart vale for the logged in user and this is requested as a seperate api from mobile team(TK)
	public function cart_value_get()
	{
		try{
    		$tokenData = $this->verify_request();
    		// echo "<pre>";
    		// print_r($tokenData);
    		// die;
    		if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else
            {
            	$cart_response = $this->cart_calculation($tokenData);
        		$response['item_total'] = $cart_response['item_total'];
        		$response['items'] = $cart_response['items'];
        		$response['rest_id'] = $cart_response['restaurant_id'];
            	if($cart_response['restaurant_id'] != 0)
            	{
	        		$logo_image = $this->Common->getData('restaurants','logo_image','id = "'.$cart_response['restaurant_id'].'"');
	        		$response['rest_image'] = $logo_image[0]['logo_image'];
            	}else
            	{
            		$response['rest_image'] = '';
            	}

        		# Now we also need to check whether any outstanding amount needs to be paid by the customer to the Admin
        		# who_will_pay_outstanding_amount 2- restaurant will pay to customer, 3 - customer will pay to restaurant
        		$outstanding = $this->Common->getData('orders','id AS order_id,order_number,outstanding_amount','user_id = "'.$tokenData->id.'" AND outstanding_amount > 0 AND is_paid_outstanding_amount = 0 AND who_will_pay_outstanding_amount = 3');
        		# It may possible that more than one order may have outstanding to be paid
        		$outstanding_amount = 0;
        		if(count($outstanding) > 0)
        		{
        			$response['outstanding_detail'] = $outstanding;
        			foreach ($outstanding as $key => $value) 
        			{
        				$outstanding_amount += $value['outstanding_amount'];
        			}
        			$response['outstanding_amount'] =  (string)$outstanding_amount;
        		}else
        		{
        			$response['outstanding_detail'] = array();
					$response['outstanding_amount'] = '0.00';
        		}

        		$data['status']		=200;
		        $data['message']	=$this->lang->line('success');
		        $data['data']		=$response;
            }

    		$this->response($data, $data['status']);

    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# This function is used to update database for paid status of outstanding value.
	# First call place order and then call this. 
	public function update_paid_outstanding_amount_post()
	{
		try{
    		$tokenData = $this->verify_request();
    		$is_multiple = !empty($_POST['is_multiple'])?$this->db->escape_str($_POST['is_multiple']):''; # 1 yes 2 No
    		$order_id = !empty($_POST['order_id'])?$this->db->escape_str($_POST['order_id']):'';
    		$is_paid_through_wallet = !empty($_POST['is_paid_through_wallet'])?$this->db->escape_str($_POST['is_paid_through_wallet']):''; # 1 Yes 2 No
    		$wallet_debited_value = $_POST['wallet_debited_value']; # wallet_debited_value.Pass 0 if not used
    		if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($order_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('order_id_missing');
                $data['data']		=array();
            }else if($is_paid_through_wallet ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('is_paid_through_wallet_missing');
                $data['data']		=array();
            }else if($wallet_debited_value ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('wallet_debited_value_missing');
                $data['data']		=array();
            }else
            {
            	$update_array = [
            		'updated_at' => time(),
            		'outstanding_amount' => 0.00,
            		'who_will_pay_outstanding_amount' => 0,
            		'is_paid_outstanding_amount' => 1,
            		'paid_status' => 1
            	];
            	if($is_multiple == 1) # 1 : Yes there are multiple orders
            	{
            		# We need to send notification to merchant only.

            		$order = explode(",", $order_id);
            		foreach ($order as $key => $orderId) 
            		{
            			$not_data = $this->Common->getData('orders','restaurant_id','id = "'.$orderId.'"');
            			$restaurant_id = $not_data[0]['restaurant_id'];
            			$merchant_id = $this->Common->getData('restaurants','admin_id','id = "'.$restaurant_id.'"');
						$merchant_id = $merchant_id[0]['admin_id'];
						$tokens = $this->Common->getData('users','device_token','id = "'.$merchant_id.'"');

						$notification_data_fields = array(
							'message' => 'Outstanding amount paid for Order '.$orderId,
							'title' => NOTIFICATION_TITLE_OUTSTANDING_PAID,
							# 0 => pending , 1 => processed , 2 => Packing, 3 => shipped 4 => Completed 5 => Unfulfilled  6 => Delivered
							'order_id' => $orderId,
							'notification_type' => 'ORDER_STATUS_UPDATED'
						);
						# Send notification to Merchant
						if(!empty($tokens)){
							foreach($tokens as $tk){
								$token = $tk['device_token'];
								if($token!="")
								{
								   sendPushNotification($token, $notification_data_fields,IOS_BUNDLE_ID_MERCHANT);
								}
							}
						}
						# Send to Merchant
						$insertData = [
							'title' => 'Outstanding amount paid for Order '.$orderId,
							'to_user_id' => $merchant_id,
							'type' => 1, # Order related
							'order_id' => $orderId,
							'is_read' => 1,
							'created_at' => time(),
							'updated_at' => time(),
						 ];
						 $this->Common->insertData('notifications',$insertData);

            			$this->Common->updateData('orders',$update_array,'id = "'.$orderId.'"');
            		}
            	}else # 2 : No only a single order has outstanding amount
            	{
            		$not_data = $this->Common->getData('orders','restaurant_id','id = "'.$order_id.'"');
        			$restaurant_id = $not_data[0]['restaurant_id'];
        			$merchant_id = $this->Common->getData('restaurants','admin_id','id = "'.$restaurant_id.'"');
					$merchant_id = $merchant_id[0]['admin_id'];
					$tokens = $this->Common->getData('users','device_token','id = "'.$merchant_id.'"');

					$notification_data_fields = array(
						'message' => 'Outstanding amount paid for Order '.$order_id,
						'title' => NOTIFICATION_TITLE_OUTSTANDING_PAID,
						# 0 => pending , 1 => processed , 2 => Packing, 3 => shipped 4 => Completed 5 => Unfulfilled  6 => Delivered
						'order_id' => $order_id,
						'notification_type' => 'ORDER_STATUS_UPDATED'
					);
					# Send notification to Merchant
					if(!empty($tokens)){
						foreach($tokens as $tk){
							$token = $tk['device_token'];
							if($token!="")
							{
							   sendPushNotification($token, $notification_data_fields,IOS_BUNDLE_ID_MERCHANT);
							}
						}
					}
					# Send to Merchant
					$insertData = [
						'title' => 'Outstanding amount paid for Order '.$order_id,
						'to_user_id' => $merchant_id,
						'type' => 1, # Order related
						'order_id' => $order_id,
						'is_read' => 1,
						'created_at' => time(),
						'updated_at' => time(),
					 ];
					 $this->Common->insertData('notifications',$insertData);
            		$this->Common->updateData('orders',$update_array,'id = "'.$order_id.'"');
            	}

            	# Now we need to check if outstanding paid through wallet then dedcut from wallet
            	if($is_paid_through_wallet == 1) # YES
            	{
            		$insert_wallet = [
        				'user_id' => $tokenData->id,
        				'order_id' => 0,
        				'wallet_date' => time(),
        				'debited' => $wallet_debited_value,
        				'credited' => 0,
        				'type' => 3, # DEBITED
        				'added_by' => 0,
        				'valid_till' => 0,
        				'created_at' => time(),
        				'updated_at' => time(),
        			];
        			$this->Common->insertData('wallet', $insert_wallet);
            	}

            	# DELETE THE KEY THEN
            	# OTHER DEPENDENT API LIKE update_paid_outstanding_amount and add_money_to_wallet and record_transaction_details we need to delete the created entry of idempotency key from the Database for this User.
            	// $this->Common->deleteData('idempotency_key','user_id = "'.$tokenData->id.'"');

        		$data['status']		=200;
		        $data['message']	=$this->lang->line('outstanding_paid_success');
		        $data['data']		=array();
            }

    		$this->response($data, $data['status']);

    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}


    // ----------------------------------------- CUSTOMER APP API END -------------------------------------------------------- //

    // ----------------------------------------- MERCHANT APP API START -------------------------------------------------------- //

    # IMPORTANT : For MERCHANT APP always create id in TOKEN and RESPONSE from users table primary id AND use resturants.admin_id in query.

    # MERCHANT_AUTH_START
    # Singup start
    # This method is used to register the merchant
    public function merchant_signup_post()
    {
    	try
        {
	    	$restaurant_name = !empty($_POST['restaurant_name'])?$this->db->escape_str($_POST['restaurant_name']):'';    	
	        $email = !empty($_POST['email'])?$this->db->escape_str($_POST['email']):'';
	        $email = strtolower(trim($email));
	        $mobile = !empty($_POST['mobile'])?$this->db->escape_str($_POST['mobile']):'';
	        $password = !empty($_POST['password'])?$_POST['password']:'';
	        # $this->bcrypt->hash_password(trim($password))
	        $business_type = !empty($_POST['business_type'])?$this->db->escape_str($_POST['business_type']):'';
	        #DB_business_type = 1 - Food , 2 - Grocery , 3 - Alcohol
	        
	        # Food type = 0 : Not applicable 1 : Restaurant 2 : Kitchen
            $food_type = !empty($_POST['food_type'])?$this->db->escape_str($_POST['food_type']):'';

			# Address
			$lat = !empty($_POST['latitude'])?($_POST['latitude']):'';
	    	$lng = !empty($_POST['longitude'])?($_POST['longitude']):''; 
	    	$pin_address = !empty($_POST['pin_address'])?$this->db->escape_str(trim($_POST['pin_address'])):'';
	    	$unit_number = !empty($_POST['unit_number'])?$this->db->escape_str(trim($_POST['unit_number'])):'';
	    	$street_address = !empty($_POST['street_address'])?$this->db->escape_str(trim($_POST['street_address'])):'';
	    	$postal_code = !empty($_POST['postal_code'])?$this->db->escape_str(trim($_POST['postal_code'])):'';

	        $device_id = !empty($_POST['device_id'])?$this->db->escape_str($_POST['device_id']):'';
	        $device_type = !empty($_POST['device_type'])?$this->db->escape_str($_POST['device_type']):'';
	        $device_token = !empty($_POST['device_token'])?$this->db->escape_str($_POST['device_token']):'';

            if($restaurant_name == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('restaurant_name_missing');
                $data['data']		=array();
            }elseif($email ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('email_missing');
                $data['data']		=array();
            }else if($device_id == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('device_id_missing');
	            $data['data']		=array();
	        }else if($device_type == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('device_type_missing');
	            $data['data']		=array();
	        }else if($device_token == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('device_token_missing');
	            $data['data']		=array();
	        }else if($business_type == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('business_type_missing');
	            $data['data']		=array();
	        }else if($business_type == 1 && $food_type == ''){
	        	$data['status']		=201;
	            $data['message']	=$this->lang->line('select_foodtype');
	            $data['data']		=array();
	        }else if($mobile == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('contact_missing');
	            $data['data']		=array();
	        }else if($lat == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('latitude_missing');
                $data['data']		=array();
            }else if($lng == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('longitude_missing');
                $data['data']		=array();
            }else if($pin_address == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('pin_address_missing');
                $data['data']		=array();
            }else if($unit_number == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('unit_number_missing');
                $data['data']		=array();
            }
            # Asked to not to make mandatory
            // else if($street_address == ''){
            //     $data['status']		=201;
            //     $data['message']	=$this->lang->line('street_address_missing');
            //     $data['data']		=array();
            // }
            else if($postal_code == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('postal_code_missing');
                $data['data']		=array();
            }else if($password == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('password_missing');
                $data['data']		=array();
            }else
            {
            	# First Make sure that this email or mobile does not exists in Database
            	$check = $this->Common->getData('users','id','(email = "'.$email.'" OR mobile = "'.$mobile.'") AND status != 5');
            	# DB_user_status 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
            	if(count($check) > 0)
            	{
            		# already exists
            		$data['status']		=201;
		            $data['message']	=$this->lang->line('user_already_exists');
		            $data['data']		=array(); 
            	}else
            	{
            		if($business_type != 1) # If merchant is not Food (So it may be alcohol OR Grocrey ; in such case food type will be 0)
            		{
            			$food_type = 0; # Food type = 0 : Not applicable 1 : Restaurant 2 : Kitchen
            		}
			        # We will put below field as blank for MERCHANT because address of the restaurant will be available in restaurant table. #FIELDS : 'user_pin_address 'user_unit_number 'user_street_address 'user_postal_code
			        # Lat long for restaurant will be accessible from user table and rest will be accessed from restaurant tbl
			        # Create Insert Array and make entry to User table first
			        $insert_array = [
			        	'fullname' => trim($restaurant_name),
	                    'email' => $email,
	                    'role' => 2, # DB_User_role 1 - Super Admin 2- Restaurant Admin 3- Customer
	                    'mobile' => $mobile,
	                    'password' => $this->bcrypt->hash_password(trim($password)),
	                    'is_otp_verified' => 1,// Static for merchant
	                    'number_id' => '',
	                    'status' => 4,
	                    'is_online' => 1,
	                    'latitude' => $lat,
	                    'longitude' => $lng,
	                    'user_pin_address' => trim($pin_address),
	                    'user_unit_number' => trim($unit_number),
	                    'user_street_address' => trim($street_address),
	                    'user_postal_code' => trim($postal_code),
	                    'device_id' => trim($device_id),
	                    'device_type' => trim($device_type),
	                    'device_token' => trim($device_token),
	                    'created_at' => time(),
	                    'updated_at' => time(),
	                ];
	        		# insert record in users table
	                $user_id = $this->Common->insertData("users",$insert_array);
	                $ins = $this->Common->getData('users','id,status','id = "'.$user_id.'"');
	                $display_id_start = 10000; # Static value and it will be added by the last Id in increasing way
	                $sr_display_id = $display_id_start + $user_id;
	                $update_array = [
	                    'number_id' => $sr_display_id,
	                ];
	                $this->Common->updateData('users',$update_array , 'id = "'.$user_id.'"');

	                $is_profile_completed = $this->Common->getData('users','is_profile_completed','id = "'.$user_id.'"');

	                # Now make entry to RESTAURANT table
	                $merchant_insert = [
	                	'admin_id' => $user_id,
	                	'rest_name' => trim($restaurant_name),
	                	'business_type' => $business_type,
	                	'food_type' => $food_type,
	                	'rest_pin_address' => trim($pin_address),
	                	'rest_unit_number' => trim($unit_number),
	                	'rest_street_address' => trim($street_address),
	                	'rest_postal_code' => trim($postal_code),
	                	'created_at' => time(),
	                    'updated_at' => time(),
	                ];	
	                $rest_id = $this->Common->insertData('restaurants',$merchant_insert);

	                if($user_id > 0) # Insert Success
	                {
	                    # mail_send code start. This mail sends just a thankyou mail to merchant email Id
	                    $mail_data['restaurant_name'] = trim($restaurant_name);
	                    $mail_data['header_title'] = APP_NAME.' : Thank you for registering !';
	                    $mail_data['email'] = $email;
	                    $email = $email;
	                    $subject = "Welcome to ".APP_NAME;

	                    # Get Social urls from Database settings table
	                    $social_urls =  $this->get_social_urls();

	                    $mail_data['facebook_url'] = $social_urls['facebook'];
	                    $mail_data['google_url'] = $social_urls['google'];
	                    $mail_data['insta_url'] = $social_urls['insta'];
	                    $mail_data['website_url'] = $social_urls['website'];

	                    # load template view
	                    $message = $this->load->view('email/merchant_registration', $mail_data, TRUE);
	                    // echo $message;die;
	                    send_mail($email,$subject,$message);
	                    # mail send code end 

	                    $token_data = [
	            			'id' => $ins[0]['id'], # Id from users table
                        	'name' => trim($restaurant_name), # It will contain restaurant name
                        	'email' => $email,
                        	'role' => 2, # DB_User_role 1 - Super Admin 2- Restaurant Admin 3- Customer
                        	'timestamp' => time()
	                    ];
	                    # Create a token from the user data and send it as reponse
	                    $token = AUTHORIZATION::generateToken($token_data);

	                    # Generate Response array and send
	                    $res_arr = [
	                        'id' => $ins[0]['id'], # Id from users table
	                        'name' => trim($restaurant_name),
	                        'email' => $email,
	                        'role' => 2, # DB_User_role 1 - Super Admin 2- Restaurant Admin 3- Customer
	                        'is_profile_completed' => $is_profile_completed[0]['is_profile_completed'],
	                        'status' => $ins[0]['status'],
	                        'token' => $token,
	                    ];

	                    $data['status']		=200;
	                    $data['message']	=$this->lang->line('register_success');
	                    $data['data']		=$res_arr;
		        	}else
		        	{
		        		$data['status']		=201;
			            $data['message']	=$this->lang->line('something_went_wrong');
			            $data['data']		=array(); 
		        	}
            	}
            }
	        # REST_Controller provide this method to send responses
	    	$this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # Merchant Login start
    # This method is used for Merchant Login
    public function merchant_login_post()
    {
    	try{
            $mobile = !empty($_POST['contact'])?$this->db->escape_str($_POST['contact']):'';
            $password = !empty($_POST['password'])?$_POST['password']:'';
            $device_id = !empty($_POST['device_id'])?$this->db->escape_str($_POST['device_id']):'';
            $device_type = !empty($_POST['device_type'])?$this->db->escape_str($_POST['device_type']):'';
            $device_token = !empty($_POST['device_token'])?$this->db->escape_str($_POST['device_token']):'';

            if($mobile==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('contact_missing');
                $data['data']		=array();
            }else if($password == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('password_missing');
                $data['data']		=array();
            }else if($device_id == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('device_id_missing');
                $data['data']		=array();
            }else if($device_type == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('device_type_missing');
                $data['data']		=array();
            }else if($device_token == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('device_token_missing');
                $data['data']		=array();
            }else{

                # get user details
                $check_email = $this->Common->getData("users","*","mobile = '$mobile' and status != 5 and role = 2"); # DB_user_status 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted

                # check email exists or not
                if(!empty($check_email))
                {
                    # check password match or not
                    if ($this->bcrypt->check_password($password, $check_email[0]['password']))
                    {
                        if($check_email[0]['status'] == 2){
                            $data['status']	 = 201;
                            $data['message'] = $this->lang->line('approval_rejected');
                            $data['data'] =array();
                        }elseif($check_email[0]['status'] == 3)
                        {
                        	$data['status']	 = 201;
                            $data['message'] = $this->lang->line('inactive_user');
                            $data['data'] =array();
                        }else{

                        	# Get restaturant Data from RESTAURANT table whose admin id is the id of $check_email array variable
                        	$restaurant = $this->Common->getData('restaurants','*','admin_id = "'.$check_email[0]['id'].'"');

                            # token details
                            $token_data = [
                                'id' => $check_email[0]['id'], # Id from users table
                                'mobile' => $mobile,
                                'role' => $check_email[0]['role'],
                                'timestamp' => time()
                            ];

                            # Create a token from the user data and send it as reponse
                            $token = AUTHORIZATION::generateToken($token_data);

                            # update user status in users table
                            $this->Common->updateData('users',array('updated_at' => time(),'is_online' => 1,'device_id' => $device_id,'device_type' =>$device_type,'device_token' => $device_token),"id = ".$check_email[0]['id']);
                            
                            $res_arr = [
                                'is_profile_completed' => $check_email[0]['is_profile_completed'], # users tbl
                                'role' => $check_email[0]['role'], # users tbl
                                'email' => $check_email[0]['email'],
                                'id' => $check_email[0]['id'], # Id from users table
                                'name' => $restaurant[0]['rest_name'],
                                'status' => $check_email[0]['status'],
                                'token' => $token
                            ];

                            $data['status']	 = 200;
                            $data['message'] = $this->lang->line('login_success');
                            $data['data'] =$res_arr;

                        }
                    }else{
                        $data['status']	 = 201;
                        $data['message'] = $this->lang->line('invalid_credentials');
                        $data['data'] =array();
                    }
                }else{
                    $data['status']	 = 201;
                    $data['message'] = $this->lang->line('invalid_credentials');
                    $data['data'] =array();
                }
            }

            // REST_Controller provide this method to send responses
            $this->response($data, $data['status']);

        } catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # Merchant Login END

    # Merchant Forgot password start
    # This method is used to send password reset link on merchant's app when merchant click on Send button of forgot password screen
    public function merchant_forgot_password_post()
    {
    	try{
            $email = !empty($_POST['email'])?$this->db->escape_str($_POST['email']):'';

		    if($email == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('email_missing');
                $data['data']		=array();
            }else
            {
                $check_email = $this->Common->getData('users','id, fullname, email, role,status',"email='".$email."' and status != 5 and role = 2"); 
                #DB_user_status 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
                if(!empty($check_email))
                {
                    if($check_email[0]['status'] == 2)
	                {
                		$data['status']		=201;
		                $data['message']	=$this->lang->line('approval_rejected');
		                $data['data']		=array();
                    }elseif($check_email[0]['status'] == 3)
                    {
                    	$data['status']		=201;
		                $data['message']	=$this->lang->line('inactive_user');
		                $data['data']		=array();
                    }else
                    {
                    	# generate new password for forgot password
	                    $token = generate_token();
	                    $token_expire_time = strtotime('+1 day');
	                    # make update array
	                    $update_array = [
		                    'token'=>$token,
		                    'token_expire_time' => $token_expire_time, 
		                    'updated_at' => time()
	                	];

	                    # update data in users table
	            		$this->Common->updateData('users',$update_array,"id = ".$check_email[0]['id']);

	                    $mail_data['url'] = base_url().'admin/resetPasswordChange/'.$token.'/'.$check_email[0]['role'];
			            $mail_data['user_name'] = $check_email[0]['fullname'];
			            $mail_data['header_title'] = APP_NAME.' Password Reset Instructions';
			            $email = $check_email[0]['email'];
			            $subject = APP_NAME."Password Reset Instructions";
			            
	                    //load template view
	                    $message = $this->load->view('email/forgot_password', $mail_data, TRUE);
	                    
	                    send_mail($email,$subject,$message);
	                    /*mail send code end */

	                    $data['status']		=200;
	                    $data['message']	=$this->lang->line('forgot_password_sent');
	                    $data['data']		= array();
                    }
                }else
                {
                    $data['status']		=201;
                    $data['message']	=$this->lang->line('no_account_exists');
                    $data['data']		=array();
                }
            }
            // REST_Controller provide this method to send responses
            $this->response($data, $data['status']);

		} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # Merchant Forgot password end
    # MERCHANT_AUTH_END

    # MERCHANT_ADDRESS_START
    # merchant_list_address_get method start
    # This method is used to list the merchant address.
    public function merchant_list_address_get()
    {
    	try{
 			/*$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
			As of now no need to limit because only one entry will be returned.But code placed anc commented if anything changed.
			*/
 			$tokenData = $this->verify_request();

			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }
			else
		    {
		    	$merchant_address = $this->Common->getData('restaurants','restaurants.id,restaurants.rest_pin_address,restaurants.rest_unit_number,restaurants.rest_street_address,restaurants.rest_postal_code,users.latitude,users.longitude','restaurants.admin_id = "'.$tokenData->id.'"',array('users'),array('users.id = restaurants.admin_id'));

		    	$data['status']		=200;
				$data['message']	=$this->lang->line('success');
				$data['data']		=$merchant_address[0]; 
		    }
 			# REST_Controller provide this method to send responses
			$this->response($data, $data['status']);
 		} catch (\Exception $e) {

			# make error log
			log_message('error', $e);
			$data['status']		=500;
			$data['message']	=$this->lang->line('internal_server_error');
			$data['data']		=array(); 
			$this->response($data, $data['status']);
		}
    }

    # update_merchant_address
    # This menthod is used to udpate the merchant address
    public function merchant_update_address_post()
    {
    	try
        {
	    	$lat = !empty($_POST['latitude'])?($_POST['latitude']):'';
	    	$lng = !empty($_POST['longitude'])?($_POST['longitude']):''; 
	    	$pin_address = !empty($_POST['pin_address'])?$this->db->escape_str(trim($_POST['pin_address'])):'';
	    	$unit_number = !empty($_POST['unit_number'])?$this->db->escape_str(trim($_POST['unit_number'])):'';
	    	$street_address = !empty($_POST['street_address'])?$this->db->escape_str(trim($_POST['street_address'])):'';
	    	$postal_code = !empty($_POST['postal_code'])?$this->db->escape_str(trim($_POST['postal_code'])):'';
            $tokenData = $this->verify_request();

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message'] = $this->lang->line('unauthorized_access');
            }else if($lat == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('latitude_missing');
                $data['data']		=array();
            }else if($lng == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('longitude_missing');
                $data['data']		=array();
            }else if($pin_address == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('pin_address_missing');
                $data['data']		=array();
            }else if($unit_number == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('unit_number_missing');
                $data['data']		=array();
            }
            # Asked to remove validation
            // else if($street_address == ''){
            //     $data['status']		=201;
            //     $data['message']	=$this->lang->line('street_address_missing');
            //     $data['data']		=array();
            // }
            else if($postal_code == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('postal_code_missing');
                $data['data']		=array();
            }else
            {
        		# Token passed means user exists in Database so udpate the below fields in Database for this user id
            	$udpate_rest = [
		        	'rest_pin_address' => $pin_address,
		        	'rest_unit_number' => $unit_number,
		        	'rest_street_address' => $street_address,
		        	'rest_postal_code' => $postal_code,
		        	'updated_at' => time(),
        		];
	        	$this->Common->updateData('restaurants',$udpate_rest,'admin_id = "'.$tokenData->id.'"');

	        	$update_user = [
	        		'latitude' => $lat,
		        	'longitude' => $lng,
		        	'user_pin_address' => trim($pin_address),
                    'user_unit_number' => trim($unit_number),
                    'user_street_address' => trim($street_address),
                    'user_postal_code' => trim($postal_code),
		        	'updated_at' => time(),
	        	];
	        	$this->Common->updateData('users',$update_user,'id = "'.$tokenData->id.'"');
	        	$res = [
	        		'lat' => $lat,
	        		'lng' => $lng,
	        		'pin_address' => $pin_address,
	        		'unit_number' => $unit_number,
	        		'street_address' => $street_address,
	        		'postal_code' => $postal_code,
	        	];

	        	$data['status']		=200;
                $data['message']	=$this->lang->line('address_update_success');
                $data['data']		=$res;
            }
	    # REST_Controller provide this method to send responses
	    $this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # MERCHANT_ADDRESS_END

    # RATING REVIEW SCREEN START
    # My review screen start
    # This method is used to display data for merchant review screen
    # THere are two review screen. One in customer app when customer sees restaurant review And other in merchant app where merchant sees own review
    public function merchant_review_screen_get()
    {
    	try{
 			$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
			$restaurant_id = !empty($_GET['restaurant_id'])?$this->db->escape_str($_GET['restaurant_id']):'';
 			$tokenData = $this->verify_request();

			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }
		    // else if($restaurant_id ==''){
      //           $data['status']		=201;
      //           $data['message']	=$this->lang->line('rest_id_missing');
      //           $data['data']		=array();
      //       }
            else
		    {
		    	$rest_id = $this->get_restaurant_id($tokenData->id);
		    	// $all_rating = $this->Common->getData('ratings','ratings.*,restaurants.id,restaurants.rest_name,restaurants.avg_rating,restaurants.rest_pin_address,restaurants.rest_unit_number,restaurants.rest_street_address,restaurants.rest_postal_code,users.latitude,users.longitude,users.fullname','ratings.to_id = "'.$tokenData->id.'"',array('users','restaurants'),array('users.id = ratings.to_id' , 'restaurants.admin_id = ratings.to_id'),'','',$limit,$page);
		    	$all_rating = $this->Common->getData('ratings','ratings.*,restaurants.admin_id,restaurants.id,restaurants.rest_name,restaurants.avg_rating,restaurants.rest_pin_address,restaurants.rest_unit_number,restaurants.rest_street_address,restaurants.rest_postal_code,users.latitude,users.longitude,users.fullname','ratings.to_id = "'.$rest_id.'"',array('restaurants','users'),array('restaurants.id = ratings.to_id','ratings.from_user_id = users.id'),'','',$limit,$page);
		    	// echo $this->db->last_query();
		    	$data['status']		=200;
				$data['message']	=$this->lang->line('success');
				$data['data']		=$all_rating; 
		    }
 			# REST_Controller provide this method to send responses
			$this->response($data, $data['status']);
 		} catch (\Exception $e) {

			# make error log
			log_message('error', $e);
			$data['status']		=500;
			$data['message']	=$this->lang->line('internal_server_error');
			$data['data']		=array(); 
			$this->response($data, $data['status']);
		}
    }
    # My review screen end
    # RATING REVIEW SCREEN END

    # ------------------------------- TABLE_RESERVATIONS_START (DINE IN) --------------------------- #
    # table_reservation_get
    # This method is used to get the table reservation entries as per the date.
    public function table_reservation_get()
    {
    	try{
 			$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
 			
 			$date = !empty($_GET['date'])?$this->db->escape_str($_GET['date']):'';
 			$tokenData = $this->verify_request();

			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }
			else
		    {
		    	$rest_id = $this->get_restaurant_id($tokenData->id);
		    	date_default_timezone_set('Asia/Singapore');
		    	$today_midnight = strtotime("midnight today");
		    	$query = "SELECT DATE_FORMAT(FROM_UNIXTIME(`booking_date`), '%d-%b-%y') AS booking_date , booking_date as actual_timestamp FROM table_reservations WHERE restaurant_id = '$rest_id' AND booking_date >= '".$today_midnight."' GROUP BY DATE_FORMAT(FROM_UNIXTIME(`booking_date`), '%d-%b-%y') ORDER BY actual_timestamp ASC";
                $response['booked_dates'] = $this->Common->custom_query($query,"get");
                // echo "<pre>";
                // print_r($response['booked_dates']);die;
                if(count($response['booked_dates']) > 0)
                {
	                $b = $response['booked_dates'];
                	foreach($b as $index => $booked_dates)
                	{
                		// print_r($booked_dates);
                		// echo "<br>";
                		$response['booked_dates'][$index]['booking_date'] = date('d-M-y',$booked_dates['actual_timestamp']);
                		$response['booked_dates'][$index]['actual_timestamp'] = $booked_dates['actual_timestamp'];
                	}
                }
                // die;
                // echo "<pre>";
                // print_r($response);die;

                // echo $this->db->last_query();
                if(count($response['booked_dates']) > 0)
                {
	                $first_date = $response['booked_dates'][0]['actual_timestamp'];
	                # If date is not given then in such case return the reservations of first found date.
	                if($date == '')
	                {
	                	$q_date = $first_date;
	                }else
	                # If date is given then in such case return the reservations of selected date.
	                {
			    		$q_date = $date;
	                }
		    		//$query_tbl = "SELECT table_reservations.*,users.fullname FROM table_reservations INNER JOIN users ON users.id = table_reservations.user_id WHERE table_reservations.restaurant_id = ".$rest_id." AND DATE_FORMAT(FROM_UNIXTIME(`booking_date`), '%d-%b-%y') = '".date('d-M-y' , $q_date)."' LIMIT ".$page.",".$limit;
		    		$query_tbl = "SELECT table_reservations.*,users.fullname FROM table_reservations INNER JOIN users ON users.id = table_reservations.user_id WHERE table_reservations.restaurant_id = ".$rest_id." AND `booking_date` = '".$q_date."' LIMIT ".$page.",".$limit;
		    		$response['tbl_reservation'] = $this->Common->custom_query($query_tbl,"get");
		    		//echo $this->db->last_query();

			    	$data['status']		=200;
					$data['message']	=$this->lang->line('success');
					$data['data']		=$response; 
                }else
                {
                	$data['status']		=201;
	                $data['message']	=$this->lang->line('no_reservation');
	                $data['data']		=array();
                }

		    }
 			# REST_Controller provide this method to send responses
			$this->response($data, $data['status']);
 		} catch (\Exception $e) {

			# make error log
			log_message('error', $e);
			$data['status']		=500;
			$data['message']	=$this->lang->line('internal_server_error');
			$data['data']		=array(); 
			$this->response($data, $data['status']);
		}
    }

    # action_table_reservation
    # This method is used to either ACCEPT/REJECT the table reservations based on the action_type parameter
    public function action_table_reservation_post()
    {
    	try{
 			
 			$action_type = !empty($_POST['action_type'])?$this->db->escape_str(trim($_POST['action_type'])):'';
 			$reservation_id = !empty($_POST['reservation_id'])?$this->db->escape_str(trim($_POST['reservation_id'])):'';
 			
 			$tokenData = $this->verify_request();

			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }else if($action_type == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('action_missing');
                $data['data']		=array();
            }else if($reservation_id == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('id_missing');
                $data['data']		=array();
            }
			else
		    {
		    	$rest_id = $this->get_restaurant_id($tokenData->id);
		    	$check_data = $this->Common->getData('table_reservations','id','id = "'.$reservation_id.'" AND restaurant_id = "'.$rest_id.'"');
		    	if(count($check_data) > 0)
		    	{
		    		# $action_type = 1 (ACCEPT) AND $action_type = 2 (REJECT)
		    		if($action_type == 1)
		    		{
		    			$update_array = array('is_accepted' => 1 , 'updated_at' => time()); # ACCEPTED
		    			$response_message = $this->lang->line('reservation_accepted');
		    		}else
		    		{
		    			$update_array = array('is_accepted' => 2 , 'updated_at' => time()); # REJECTED
		    			$response_message = $this->lang->line('reservation_rejected');
		    		}

		    		$this->Common->updateData('table_reservations',$update_array,array('id' => $reservation_id));

			    	$data['status']		=200;
					$data['message']	=$response_message;
					$data['data']		=array(); 
		    	}else
		    	{
		    		$data['status']		=201;
	                $data['message']	=$this->lang->line('no_reservation');
	                $data['data']		=array();
		    	}

		    }
 			# REST_Controller provide this method to send responses
			$this->response($data, $data['status']);
 		} catch (\Exception $e) {

			# make error log
			log_message('error', $e);
			$data['status']		=500;
			$data['message']	=$this->lang->line('internal_server_error');
			$data['data']		=array(); 
			$this->response($data, $data['status']);
		}
    }
    # TABLE RESERVATIONS END

    # ----------------- MERCHANT_PROFILE_SECTION START -------------------- #

    # create_merchant_profile start
    # This method is used by the merchant to create merchant profile
    public function create_merchant_profile_post()
    {
    	try{

    		$is_everyday = !empty($_POST['is_everyday'])?$this->db->escape_str($_POST['is_everyday']):''; # 1 : SAME FOR ALL DAY 2 : NOT SAME FOR ALL DAY
    		$is_daywise = !empty($_POST['is_daywise'])?$this->db->escape_str($_POST['is_daywise']):''; # 1 : YES 2 : NO

    		# MONDAY
    		$mon_close_status = !empty($_POST['mon_close_status'])?$this->db->escape_str($_POST['mon_close_status']):''; # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
    		$mon_open_close_time = !empty($_POST['mon_open_close_time'])?$this->db->escape_str($_POST['mon_open_close_time']):''; # OPEN-CLOSE EX 10:00-22:00 24 HOURS FORMAT
    		$mon_break_status = !empty($_POST['mon_break_status'])?$this->db->escape_str($_POST['mon_break_status']):''; # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
    		$mon_break_start_end_time = !empty($_POST['mon_break_start_end_time'])?$this->db->escape_str($_POST['mon_break_start_end_time']):''; # BRK_START-BRK_END EX 15:00-16:00 24 HOURS FORMAT

    		# TUESDAY
    		$tue_close_status = !empty($_POST['tue_close_status'])?$this->db->escape_str($_POST['tue_close_status']):'';
    		$tue_open_close_time = !empty($_POST['tue_open_close_time'])?$this->db->escape_str($_POST['tue_open_close_time']):'';
    		$tue_break_status = !empty($_POST['tue_break_status'])?$this->db->escape_str($_POST['tue_break_status']):'';
    		$tue_break_start_end_time = !empty($_POST['tue_break_start_end_time'])?$this->db->escape_str($_POST['tue_break_start_end_time']):'';

    		# WEDNESDAY
    		$wed_close_status = !empty($_POST['wed_close_status'])?$this->db->escape_str($_POST['wed_close_status']):'';
    		$wed_open_close_time = !empty($_POST['wed_open_close_time'])?$this->db->escape_str($_POST['wed_open_close_time']):'';
    		$wed_break_status = !empty($_POST['wed_break_status'])?$this->db->escape_str($_POST['wed_break_status']):'';
    		$wed_break_start_end_time = !empty($_POST['wed_break_start_end_time'])?$this->db->escape_str($_POST['wed_break_start_end_time']):'';

    		# THURSDAY
    		$thu_close_status = !empty($_POST['thu_close_status'])?$this->db->escape_str($_POST['thu_close_status']):'';
    		$thu_open_close_time = !empty($_POST['thu_open_close_time'])?$this->db->escape_str($_POST['thu_open_close_time']):'';
    		$thu_break_status = !empty($_POST['thu_break_status'])?$this->db->escape_str($_POST['thu_break_status']):'';
    		$thu_break_start_end_time = !empty($_POST['thu_break_start_end_time'])?$this->db->escape_str($_POST['thu_break_start_end_time']):'';

    		# FRIDAY
    		$fri_close_status = !empty($_POST['fri_close_status'])?$this->db->escape_str($_POST['fri_close_status']):'';
    		$fri_open_close_time = !empty($_POST['fri_open_close_time'])?$this->db->escape_str($_POST['fri_open_close_time']):'';
    		$fri_break_status = !empty($_POST['fri_break_status'])?$this->db->escape_str($_POST['fri_break_status']):'';
    		$fri_break_start_end_time = !empty($_POST['fri_break_start_end_time'])?$this->db->escape_str($_POST['fri_break_start_end_time']):'';

    		# SATURDAY
    		$sat_close_status = !empty($_POST['sat_close_status'])?$this->db->escape_str($_POST['sat_close_status']):'';
    		$sat_open_close_time = !empty($_POST['sat_open_close_time'])?$this->db->escape_str($_POST['sat_open_close_time']):'';
    		$sat_break_status = !empty($_POST['sat_break_status'])?$this->db->escape_str($_POST['sat_break_status']):'';
    		$sat_break_start_end_time = !empty($_POST['sat_break_start_end_time'])?$this->db->escape_str($_POST['sat_break_start_end_time']):'';

    		# SUNDAY
    		$sun_close_status = !empty($_POST['sun_close_status'])?$this->db->escape_str($_POST['sun_close_status']):'';
    		$sun_open_close_time = !empty($_POST['sun_open_close_time'])?$this->db->escape_str($_POST['sun_open_close_time']):'';
    		$sun_break_status = !empty($_POST['sun_break_status'])?$this->db->escape_str($_POST['sun_break_status']):'';
    		$sun_break_start_end_time = !empty($_POST['sun_break_start_end_time'])?$this->db->escape_str($_POST['sun_break_start_end_time']):'';

    		# BELOW FIELD IS FOR EVERY DAY
    		$restaurant_open_time = !empty($_POST['restaurant_open_time'])?$this->db->escape_str($_POST['restaurant_open_time']):'';
	        $restaurant_close_time = !empty($_POST['restaurant_close_time'])?$this->db->escape_str($_POST['restaurant_close_time']):'';
	        $brk_start_time = !empty($_POST['brk_start_time'])?$this->db->escape_str($_POST['brk_start_time']):'';
	        $brk_end_time = !empty($_POST['brk_end_time'])?$this->db->escape_str($_POST['brk_end_time']):'';

	        $is_order_now_accept = $_POST['is_order_now_accept'];
	        $is_self_pickup_accept = $_POST['is_self_pickup_accept'];
	        $is_order_later_accept = $_POST['is_order_later_accept'];
	        $is_dinein_accept = $_POST['is_dinein_accept'];
	        
	        $delivery_handeled_by = !empty($_POST['delivery_handeled_by'])?$this->db->escape_str($_POST['delivery_handeled_by']):'';
	    	$logo_image = !empty($_FILES['logo_image'])?$_FILES['logo_image']:'';
	    	//$documents = !empty($_FILES['documents'])?$_FILES['documents']:'';
	    	$documents = !empty($_POST['documents'])?$_POST['documents']:'';

	    	$tokenData = $this->verify_request();

			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }else if($is_order_now_accept == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('is_order_now_accept');
	            $data['data']		=array();
	        }else if($is_self_pickup_accept == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('is_self_pickup_accept');
	            $data['data']		=array();
	        }else if($is_order_later_accept == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('is_order_later_accept');
	            $data['data']		=array();
	        }else if($is_dinein_accept == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('is_dinein_accept');
	            $data['data']		=array();
	        }else if($delivery_handeled_by == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('delivery_handeled_by_missing');
	            $data['data']		=array();
	        }else if($is_everyday == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('is_everyday_missing');
	            $data['data']		=array();
	        }else if($is_daywise == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('is_daywise_missing');
	            $data['data']		=array();
	        }else if($documents == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('documents_missing');
                $data['data']		=array();
            }
            else
            {
            	$rest_id = $this->get_restaurant_id($tokenData->id);
		        # Get basic_preparation_time set by admin
		        # As we are now creating merchant profile so we will get basic preparation time from admin settings
		        $preparation_time = $this->get_from_settings('basic_preparation_time');

		        if($is_everyday == 1) # THAT MEANS SAME OPEN CLOSE BREAK START AND BREAK END FOR ALL DAYS MON TO SUN
		        {
		        	# restaurant_open_time restaurant_close_time brk_start_time brk_end_time
		        	# If is_everyday = 1 then only restaurant_open_time restaurant_close_time will be mandaotry. No mandatory check for brk_start_time and brk_end_time
		        	if($restaurant_open_time == ''){
		            	$data['status']		=201;
		                $data['message']	=$this->lang->line('restaurant_open_time_missing');
		                $data['data']		=array();
		            }else if($restaurant_close_time ==''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('restaurant_close_time_missing');
		                $data['data']		=array();
		            }else
		            {
		            	# First upload logo image
			        	if(isset($_FILES['logo_image']['name']) && $_FILES['logo_image']['name'] != '')
			        	{
			        		$tmp_name = $_FILES['logo_image']['tmp_name'];
							$extension = pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION);
							
							$f_name = basename($_FILES['logo_image']['name'], '.'.$extension).PHP_EOL;
							$image_name = trim($f_name)."_".time().'.'.$extension;
							$logo_file_path = "assets/merchant/merchant_logo_image/".$image_name; // Send this string to database

							# replace space with _ from image name
		                    $logo_file_path = str_replace(" ","_",$logo_file_path);

							$moved = move_uploaded_file($_FILES['logo_image']['tmp_name'], $logo_file_path);
			        	}
		            	$update_array = array(
			        		'open_time' => $restaurant_open_time , 
			        		'close_time' => $restaurant_close_time,
			        		'break_start_time' => $brk_start_time,
			        		'break_end_time' => $brk_end_time,
			        		'preparation_time' => $preparation_time,
			        		'is_order_now_accept' => $is_order_now_accept,
			        		'is_self_pickup_accept' => $is_self_pickup_accept,
			        		'is_order_later_accept' => $is_order_later_accept,
			        		'is_dinein_accept' => $is_dinein_accept,
			        		'delivery_handled_by' => $delivery_handeled_by,
			        		'uploaded_document' => $documents,
			        		'time_mode' => 1, # 1 - for every day, 2 - for Specific day
			        		'logo_image' => trim($logo_file_path),
			        		'updated_at' => time(),
		     		   	);
				        
			        	$this->Common->updateData('restaurants',$update_array,'id = "'.$rest_id.'"');
			        	# Also update is_profile_completed in users table
				        $this->Common->updateData('users',array('is_profile_completed' => 1 , 'updated_at' => time()),'id = "'.$tokenData->id.'"');
				        $data['status']		=200;
						$data['message']	=$this->lang->line('marchant_profile_success');
						$data['data']		=array();
		            }
		        }
		        else if($is_daywise == 1)
		        {
		        	$is_correct = 0; # FALSE

		        	if($mon_close_status == ''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('mon_close_status_missing');
		                $data['data']		=array();
		            }else if($tue_close_status == ''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('tue_close_status_missing');
		                $data['data']		=array();
		            }else if($wed_close_status == ''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('wed_close_status_missing');
		                $data['data']		=array();
		            }
		            else if($thu_close_status == ''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('thu_close_status_missing');
		                $data['data']		=array();
		            }
		            else if($fri_close_status == ''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('fri_close_status_missing');
		                $data['data']		=array();
		            }
		            else if($sat_close_status == ''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('sat_close_status_missing');
		                $data['data']		=array();
		            }
		            else if($sun_close_status == ''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('sun_close_status_missing');
		                $data['data']		=array();
		            }
		            else
		            {
			        	# MONDAY
			        	if($mon_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
			        	{
			        		if($mon_open_close_time =='')
			        		{
			        			$is_correct = 0;
				                $data['status']		=201;
				                $data['message']	=$this->lang->line('mon_open_close_time');
				                $data['data']		=array();
			            	}else
			            	{
			            		$is_correct = 1;
			            		if($mon_break_status == ''){
			            			$is_correct = 0;
					                $data['status']		=201;
					                $data['message']	=$this->lang->line('mon_break_status_missing');
					                $data['data']		=array();
					            }else
					            {
				            		if($mon_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
						        	{
						        		if($mon_break_start_end_time =='')
						        		{
						        			$is_correct = 0;
							                $data['status']		=201;
							                $data['message']	=$this->lang->line('mon_break_start_end_time');
							                $data['data']		=array();
						            	}
						        	}else
						        	{
						        		$is_correct = 1;
						        	}
					            }
			            	}
			        	}else
			        	{
			        		$is_correct = 1;
			        	}

				        # TUESDAY
			        	if($is_correct == 1)
			        	{
				        	if($tue_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
				        	{
				        		if($tue_open_close_time =='')
				        		{
				        			$is_correct = 0;
					                $data['status']		=201;
					                $data['message']	=$this->lang->line('tue_open_close_time');
					                $data['data']		=array();
				            	}else
				            	{
				            		if($tue_break_status == ''){
				            			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('tue_break_status_missing');
						                $data['data']		=array();
						            }else
						            {
					            		if($tue_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
							        	{
							        		if($tue_break_start_end_time ==''){
							        			$is_correct = 0;
								                $data['status']		=201;
								                $data['message']	=$this->lang->line('tue_break_start_end_time');
								                $data['data']		=array();
							            	}
							        	}
						            }
				            	}
				        	}
			        	}

				        # WEDNESDAY
			        	if($is_correct == 1)
			        	{
				        	if($wed_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
				        	{
				        		if($wed_open_close_time =='')
				        		{
				        			$is_correct = 0;
					                $data['status']		=201;
					                $data['message']	=$this->lang->line('wed_open_close_time');
					                $data['data']		=array();
				            	}else
				            	{
				            		if($wed_break_status == ''){
				            			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('wed_break_status_missing');
						                $data['data']		=array();
						            }else
						            {
					            		if($wed_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
							        	{
							        		if($wed_break_start_end_time ==''){
							        			$is_correct = 0;
								                $data['status']		=201;
								                $data['message']	=$this->lang->line('wed_break_start_end_time');
								                $data['data']		=array();
							            	}
							        	}
						            }
				            	}
				        	}
			        	}

			        	# THURSDAY
			        	if($is_correct == 1)
			        	{
				        	if($thu_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
				        	{
				        		if($thu_open_close_time =='')
				        		{
				        			$is_correct = 0;
					                $data['status']		=201;
					                $data['message']	=$this->lang->line('thu_open_close_time');
					                $data['data']		=array();
				            	}else
				            	{
				            		if($thu_break_status == ''){
				            			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('thu_break_status_missing');
						                $data['data']		=array();
						            }else
						            {
					            		if($thu_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
							        	{
							        		if($thu_break_start_end_time ==''){
							        			$is_correct = 0;
								                $data['status']		=201;
								                $data['message']	=$this->lang->line('thu_break_start_end_time');
								                $data['data']		=array();
							            	}
							        	}
						            }
				            	}
				        	}
			        	}

				        # FRIDAY
			        	if($is_correct == 1)
			        	{
				        	if($fri_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
				        	{
				        		if($fri_open_close_time =='')
				        		{
				        			$is_correct = 0;
					                $data['status']		=201;
					                $data['message']	=$this->lang->line('fri_open_close_time');
					                $data['data']		=array();
				            	}else
				            	{
				            		if($fri_break_status == ''){
				            			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('fri_break_status_missing');
						                $data['data']		=array();
						            }else
						            {
					            		if($fri_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
							        	{
							        		if($fri_break_start_end_time ==''){
							        			$is_correct = 0;
								                $data['status']		=201;
								                $data['message']	=$this->lang->line('fri_break_start_end_time');
								                $data['data']		=array();
							            	}
							        	}
						            }
				            	}
				        	}
			        	}

				        # SATURDAY
			        	if($is_correct == 1)
			        	{
				        	if($sat_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
				        	{
				        		if($sat_open_close_time =='')
				        		{
				        			$is_correct = 0;
					                $data['status']		=201;
					                $data['message']	=$this->lang->line('sat_open_close_time');
					                $data['data']		=array();
				            	}else
				            	{
				            		if($sat_break_status == ''){
				            			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('sat_break_status_missing');
						                $data['data']		=array();
						            }else
						            {
					            		if($sat_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
							        	{
							        		if($sat_break_start_end_time ==''){
							        			$is_correct = 0;
								                $data['status']		=201;
								                $data['message']	=$this->lang->line('sat_break_start_end_time');
								                $data['data']		=array();
							            	}
							        	}
						            }
				            	}
				        	}
			        	}

				        # SUNDAY
			        	if($is_correct == 1)
			        	{
				        	if($sun_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
				        	{
				        		if($sun_open_close_time =='')
				        		{
				        			$is_correct = 0;
					                $data['status']		=201;
					                $data['message']	=$this->lang->line('sun_open_close_time');
					                $data['data']		=array();
				            	}else
				            	{
				            		if($sun_break_status == ''){
				            			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('sun_break_status_missing');
						                $data['data']		=array();
						            }else
						            {
					            		if($sun_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
							        	{
							        		if($sun_break_start_end_time ==''){
							        			$is_correct = 0;
								                $data['status']		=201;
								                $data['message']	=$this->lang->line('sun_break_start_end_time');
								                $data['data']		=array();
							            	}
							        	}
						            }
				            	}
				        	}
			        	}

			        	# FINAL UDPATES IN RESTURANT TABLE AND DAY WISE TABLE
			        	if($is_correct == 1)
			        	{
			        		# First upload logo image
				        	if(isset($_FILES['logo_image']['name']) && $_FILES['logo_image']['name'] != '')
				        	{
				        		$tmp_name = $_FILES['logo_image']['tmp_name'];
								$extension = pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION);
								
								$f_name = basename($_FILES['logo_image']['name'], '.'.$extension).PHP_EOL;
								$image_name = trim($f_name)."_".time().'.'.$extension;
								$logo_file_path = "assets/merchant/merchant_logo_image/".$image_name; // Send this string to database

								# replace space with _ from image name
			                    $logo_file_path = str_replace(" ","_",$logo_file_path);

								$moved = move_uploaded_file($_FILES['logo_image']['tmp_name'], $logo_file_path);
				        	}

			        		$update_array_restro = array(
				        		'preparation_time' => $preparation_time,
				        		'is_order_now_accept' => $is_order_now_accept,
				        		'is_self_pickup_accept' => $is_self_pickup_accept,
				        		'is_order_later_accept' => $is_order_later_accept,
				        		'is_dinein_accept' => $is_dinein_accept,
				        		'delivery_handled_by' => $delivery_handeled_by,
				        		'uploaded_document' => $documents,
				        		'time_mode' => 2, # 1 - for every day, 2 - for Specific day
				        		'logo_image' => trim($logo_file_path),
				        		'updated_at' => time(),
		     		   		);
				        
			        		$this->Common->updateData('restaurants',$update_array_restro,'id = "'.$rest_id.'"');
			        		# Also update is_profile_completed in users table
				        	$this->Common->updateData('users',array('is_profile_completed' => 1 , 'updated_at' => time()),'id = "'.$tokenData->id.'"');

				        	$insert_day_wise_array = array(
				        		'rest_id' => $rest_id,
								'mon_close_status' => $mon_close_status,
								'mon_open_close_time' => $mon_open_close_time,
								'mon_break_status' => $mon_break_status,
								'mon_break_start_end_time' => $mon_break_start_end_time,
								'tue_close_status' => $tue_close_status,
								'tue_open_close_time' => $tue_open_close_time,
								'tue_break_status' => $tue_break_status,
								'tue_break_start_end_time' => $tue_break_start_end_time,
								'wed_close_status' => $wed_close_status,
								'wed_open_close_time' => $wed_open_close_time,
								'wed_break_status' => $wed_break_status,
								'wed_break_start_end_time' => $wed_break_start_end_time,
								'thu_close_status' => $thu_close_status,
								'thu_open_close_time' => $thu_open_close_time,
								'thu_break_status' => $thu_break_status,
								'thu_break_start_end_time' => $thu_break_start_end_time,
								'fri_close_status' => $fri_close_status,
								'fri_open_close_time' => $fri_open_close_time,
								'fri_break_status' => $fri_break_status,
								'fri_break_start_end_time' => $fri_break_start_end_time,
								'sat_close_status' => $sat_close_status,
								'sat_open_close_time' => $sat_open_close_time,
								'sat_break_status' => $sat_break_status,
								'sat_break_start_end_time' => $sat_break_start_end_time,
								'sun_close_status' => $sun_close_status,
								'sun_open_close_time' => $sun_open_close_time,
								'sun_break_status' => $sun_break_status,
								'sun_break_start_end_time' => $sun_break_start_end_time,
								'created_at' => time(),
								'updated_at' => time()
				        	);
				        	$this->Common->insertData('rest_time_daywise',$insert_day_wise_array);

			        		$data['status']		=200;
							$data['message']	=$this->lang->line('marchant_profile_success');
							$data['data']		=array();
			        	}
		            }
		        }
	        }

	        # REST_Controller provide this method to send responses
	    	$this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # create_merchant_profile end

    # This function is used to get restaurant details for profile screen
    # merchant_profile_get START
    
    public function merchant_profile_get()
    {
    	try{
    		$tokenData = $this->verify_request();

	    	if($tokenData === false)
	    	{
	                $status = parent::HTTP_UNAUTHORIZED;
	                $data['status']	 = $status;
	                $data['message'] = $this->lang->line('unauthorized_access');
            }else
            {
				$response = $this->Common->getData('restaurants','users.fullname,users.email,users.mobile,restaurants.*','users.id = "'.$tokenData->id.'"',array('users'),array('users.id = restaurants.admin_id'));
				
				$rest_id = $this->get_restaurant_id($tokenData->id);
				$check_offline = $this->Common->getData('rest_offline','*','rest_id = "'.$rest_id.'"');
				if(count($check_offline) > 0)
				{
					$check_offline =  $check_offline[0];
				}

				#TIME MODE - # 1 - for every day, 2 - for Specific day

				$exp = explode(',', $response[0]['uploaded_document']);

				if($response[0]['time_mode'] == 2) # SPECIFIC DAYS
				{
					$specific_day_wise = $this->Common->getData('rest_time_daywise','*','rest_id = "'.$rest_id.'"');
					$specific_day_wise = $specific_day_wise[0];
				}else
				{
					$x = new stdClass();
					$specific_day_wise = $x;
				}
				
				if(count($exp) > 0)
				{
					foreach ($exp as $value)
					{
						$file_path[] = $value;
					}
				}
                
                $data['status']	= 200;
                $data['message'] = $this->lang->line('success'); 
                $data['data'] = array('details' => $response , 'file_path' => $file_path , 'offline_status' => $check_offline , 'specific_day_wise' => $specific_day_wise );
            }
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # merchant_profile_get END

    # upload_merchant_document
    # This function will be used when user click on + icon to upload document image
    # This saves the uploaded image in folder path only
    public function merchant_document_upload_post()
    {
        try{
            $tokenData = $this->verify_request();

            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']  = $status;
                $data['message'] =$this->lang->line('unauthorized_access');
            }else{
            	
                if(isset($_FILES['document']['name']) && $_FILES['document']['name'] != "")
                {
                    $tmp_name = $_FILES['document']['tmp_name'];
                    $extension = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
                    $image_name = basename($_FILES['document']['name'], '.'.$extension);
                    $image_name = $image_name.'.'.$extension;
                    $file_path = "assets/merchant/merchant_documents/".$image_name; # Send this string to database
                    $file_path = str_replace(" ","_",$file_path);

                    $moved = move_uploaded_file($_FILES['document']['tmp_name'], $file_path);
                    // $response_file_path = array('file_path' => $file_path);

                    $data['status']     =200;
                    $data['message']    =$this->lang->line('success');
                    $data['data']       =array($file_path); 
                }else{
                    $data['status']     =201;
                    $data['message']    =$this->lang->line('documents_missing');
                    $data['data']       =array(); 
                }
            }

            // REST_Controller provide this method to send responses
            $this->response($data, $data['status']);

        }catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']     =500;
            $data['message']    =$this->lang->line('internal_server_error');
            $data['data']       =array(); 

            $this->response($data, $data['status']);
        }
    }

    public function merchant_update_profile_post()
    {
    	try{
    		$tokenData = $this->verify_request();
    		$email = !empty($_POST['email'])?$this->db->escape_str($_POST['email']):'';
    		$contact = !empty($_POST['mobile'])?$this->db->escape_str($_POST['mobile']):'';
    		$name = !empty($_POST['name'])?$this->db->escape_str($_POST['name']):'';
    		
    		$restaurant_open_time = !empty($_POST['restaurant_open_time'])?$this->db->escape_str($_POST['restaurant_open_time']):'';
	        $restaurant_close_time = !empty($_POST['restaurant_close_time'])?$this->db->escape_str($_POST['restaurant_close_time']):'';
	        $brk_start_time = !empty($_POST['brk_start_time'])?$this->db->escape_str($_POST['brk_start_time']):'';
	        $brk_end_time = !empty($_POST['brk_end_time'])?$this->db->escape_str($_POST['brk_end_time']):'';

	        $is_order_now_accept = $_POST['is_order_now_accept'];
	        $is_self_pickup_accept = $_POST['is_self_pickup_accept'];
	        $is_order_later_accept = $_POST['is_order_later_accept'];
	        $is_dinein_accept = $_POST['is_dinein_accept'];
	        
	        $delivery_handeled_by = !empty($_POST['delivery_handeled_by'])?$this->db->escape_str($_POST['delivery_handeled_by']):'';
	    	$documents = !empty($_POST['documents'])?$_POST['documents']:'';
	    	$logo_image = !empty($_FILES['logo_image'])?$_FILES['logo_image']:'';

	    	$is_everyday = !empty($_POST['is_everyday'])?$this->db->escape_str($_POST['is_everyday']):''; # 1 : SAME FOR ALL DAY 2 : NOT SAME FOR ALL DAY
			$is_daywise = !empty($_POST['is_daywise'])?$this->db->escape_str($_POST['is_daywise']):''; # 1 : YES 2 : NO

			# MONDAY
			$mon_close_status = !empty($_POST['mon_close_status'])?$this->db->escape_str($_POST['mon_close_status']):''; # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
			$mon_open_close_time = !empty($_POST['mon_open_close_time'])?$this->db->escape_str($_POST['mon_open_close_time']):''; # OPEN-CLOSE EX 10:00-22:00 24 HOURS FORMAT
			$mon_break_status = !empty($_POST['mon_break_status'])?$this->db->escape_str($_POST['mon_break_status']):''; # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
			$mon_break_start_end_time = !empty($_POST['mon_break_start_end_time'])?$this->db->escape_str($_POST['mon_break_start_end_time']):''; # BRK_START-BRK_END EX 15:00-16:00 24 HOURS FORMAT

			# TUESDAY
			$tue_close_status = !empty($_POST['tue_close_status'])?$this->db->escape_str($_POST['tue_close_status']):'';
			$tue_open_close_time = !empty($_POST['tue_open_close_time'])?$this->db->escape_str($_POST['tue_open_close_time']):'';
			$tue_break_status = !empty($_POST['tue_break_status'])?$this->db->escape_str($_POST['tue_break_status']):'';
			$tue_break_start_end_time = !empty($_POST['tue_break_start_end_time'])?$this->db->escape_str($_POST['tue_break_start_end_time']):'';

			# WEDNESDAY
			$wed_close_status = !empty($_POST['wed_close_status'])?$this->db->escape_str($_POST['wed_close_status']):'';
			$wed_open_close_time = !empty($_POST['wed_open_close_time'])?$this->db->escape_str($_POST['wed_open_close_time']):'';
			$wed_break_status = !empty($_POST['wed_break_status'])?$this->db->escape_str($_POST['wed_break_status']):'';
			$wed_break_start_end_time = !empty($_POST['wed_break_start_end_time'])?$this->db->escape_str($_POST['wed_break_start_end_time']):'';

			# THURSDAY
			$thu_close_status = !empty($_POST['thu_close_status'])?$this->db->escape_str($_POST['thu_close_status']):'';
			$thu_open_close_time = !empty($_POST['thu_open_close_time'])?$this->db->escape_str($_POST['thu_open_close_time']):'';
			$thu_break_status = !empty($_POST['thu_break_status'])?$this->db->escape_str($_POST['thu_break_status']):'';
			$thu_break_start_end_time = !empty($_POST['thu_break_start_end_time'])?$this->db->escape_str($_POST['thu_break_start_end_time']):'';

			# FRIDAY
			$fri_close_status = !empty($_POST['fri_close_status'])?$this->db->escape_str($_POST['fri_close_status']):'';
			$fri_open_close_time = !empty($_POST['fri_open_close_time'])?$this->db->escape_str($_POST['fri_open_close_time']):'';
			$fri_break_status = !empty($_POST['fri_break_status'])?$this->db->escape_str($_POST['fri_break_status']):'';
			$fri_break_start_end_time = !empty($_POST['fri_break_start_end_time'])?$this->db->escape_str($_POST['fri_break_start_end_time']):'';

			# SATURDAY
			$sat_close_status = !empty($_POST['sat_close_status'])?$this->db->escape_str($_POST['sat_close_status']):'';
			$sat_open_close_time = !empty($_POST['sat_open_close_time'])?$this->db->escape_str($_POST['sat_open_close_time']):'';
			$sat_break_status = !empty($_POST['sat_break_status'])?$this->db->escape_str($_POST['sat_break_status']):'';
			$sat_break_start_end_time = !empty($_POST['sat_break_start_end_time'])?$this->db->escape_str($_POST['sat_break_start_end_time']):'';

			# SUNDAY
			$sun_close_status = !empty($_POST['sun_close_status'])?$this->db->escape_str($_POST['sun_close_status']):'';
			$sun_open_close_time = !empty($_POST['sun_open_close_time'])?$this->db->escape_str($_POST['sun_open_close_time']):'';
			$sun_break_status = !empty($_POST['sun_break_status'])?$this->db->escape_str($_POST['sun_break_status']):'';
			$sun_break_start_end_time = !empty($_POST['sun_break_start_end_time'])?$this->db->escape_str($_POST['sun_break_start_end_time']):'';

    		if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }
            # ASKED TO REMOVE CHECK
         	// else if($brk_start_time == ''){
	        //     $data['status']		=201;
	        //     $data['message']	=$this->lang->line('brk_start_time_missing');
	        //     $data['data']		=array();
	        // }else if($brk_end_time == ''){
	        //     $data['status']		=201;
	        //     $data['message']	=$this->lang->line('brk_end_time_missing');
	        //     $data['data']		=array();
	        // }
	        else if($is_order_now_accept == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('is_order_now_accept');
	            $data['data']		=array();
	        }else if($is_self_pickup_accept == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('is_self_pickup_accept');
	            $data['data']		=array();
	        }else if($is_order_later_accept == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('is_order_later_accept');
	            $data['data']		=array();
	        }else if($is_dinein_accept == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('is_dinein_accept');
	            $data['data']		=array();
	        }else if($delivery_handeled_by == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('delivery_handeled_by_missing');
	            $data['data']		=array();
	        }else if($email == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('email_missing');
	            $data['data']		=array();
	        }else if($contact == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('contact_missing');
	            $data['data']		=array();
	        }else if($name == ''){
	            $data['status']		=201;
	            $data['message']	=$this->lang->line('name_missing');
	            $data['data']		=array();
	        }else if($is_everyday == ''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('is_everyday_missing');
			    $data['data']		=array();
			}else if($is_daywise == ''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('is_daywise_missing');
			    $data['data']		=array();
			}
            else
            {
            	# Check whether newly entered email already exist
		        $check_exist = $this->Common->getData('users','id','(email = "'.$email.'" OR mobile = "'.$contact.'") AND (id != "'.$tokenData->id.'" AND status != 5)');
		        # DB_user_status 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted

		        if(count($check_exist) > 0)
		        {
		            $data['status']		=201;
		            $data['message']	=$this->lang->line('user_already_exists');
		            $data['data']		=array(); 
		        }else
		        {
		        	# COMMONLY HAPPENING THINGS
		        	$rest_id = $this->get_restaurant_id($tokenData->id);
		        	$is_common_correct = 0; # FALSE FAIL

		        	if($is_everyday == 1) # THAT MEANS SAME OPEN CLOSE BREAK START AND BREAK END FOR ALL DAYS MON TO SUN
		        	{
		        		# restaurant_open_time restaurant_close_time brk_start_time brk_end_time
						# If is_everyday = 1 then only restaurant_open_time restaurant_close_time will be mandaotry. No mandatory check for brk_start_time and brk_end_time
		        		if($restaurant_open_time == ''){
			            	$data['status']		=201;
			                $data['message']	=$this->lang->line('restaurant_open_time_missing');
			                $data['data']		=array();
			            }else if($restaurant_close_time ==''){
			                $data['status']		=201;
			                $data['message']	=$this->lang->line('restaurant_close_time_missing');
			                $data['data']		=array();
			            }else
			            {
			            	# update_user_tbl_array
				        	$update_user_tbl['fullname'] = $name;
				        	$update_user_tbl['email'] = $email;
				        	$update_user_tbl['mobile'] = $contact;
				        	$update_user_tbl['updated_at'] = time();
				        	$this->Common->updateData('users',$update_user_tbl,'id = "'.$tokenData->id.'"');

				        	# Check whether logo image uploaded
				        	if(isset($_FILES['logo_image']['name']) && $_FILES['logo_image']['name'] != '')
				        	{
				        		# That means new profile image uploaded
				        		# Get old image to unlink
		                    	$image = $this->Common->getData('restaurants','logo_image','admin_id = "'.$tokenData->id.'"');

				        		$tmp_name = $_FILES['logo_image']['tmp_name'];
								$extension = pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION);
								
								$f_name = basename($_FILES['logo_image']['name'], '.'.$extension);
								$image_name = trim($f_name)."_".time().'.'.$extension;
								$logo_file_path = "assets/merchant/merchant_logo_image/".$image_name; // Send this string to database
								# replace space with _ from image name
		                    	$logo_file_path = str_replace(" ","_",$logo_file_path);
								$moved = move_uploaded_file($_FILES['logo_image']['tmp_name'], $logo_file_path);

								if(!$moved)
								{
									$data['status']		=201;
							        $data['message']	=$this->lang->line('something_went_wrong');
							        $data['data']		=array();
								}
								else
								{
									# Create update array key for logo image
									$update_array_restro_table['logo_image'] = trim($logo_file_path);
									# That means image uploaded successfully so we can unlink old imge
			                        if(!empty($image[0]['logo_image']))
			                        {
			                            unlink($image[0]['logo_image']);
			                        }
								}
				        	}

				        	# Check whether Document image uploaded
				        	if($documents != '')
				        	{
								$update_array_restro_table['uploaded_document'] = $documents;		        		
				        	}

				        	$update_array_restro_table['rest_name'] = $name;
				        	$update_array_restro_table['is_order_now_accept'] = $is_order_now_accept;
				        	$update_array_restro_table['is_self_pickup_accept'] = $is_self_pickup_accept;
				        	$update_array_restro_table['is_order_later_accept'] = $is_order_later_accept;
				        	$update_array_restro_table['is_dinein_accept'] = $is_dinein_accept;
				        	$update_array_restro_table['delivery_handled_by'] = $delivery_handeled_by;
				        	$update_array_restro_table['updated_at'] = time();
				        	
				        	$update_array_restro_table['open_time'] = $restaurant_open_time;
				        	$update_array_restro_table['close_time'] = $restaurant_close_time;
				        	$update_array_restro_table['break_start_time'] = $brk_start_time;
				        	$update_array_restro_table['break_end_time'] = $brk_end_time;
				        	$update_array_restro_table['time_mode'] = 1; # 1 - for every day, 2 - for Specific day
					        
				        	$this->Common->updateData('restaurants',$update_array_restro_table,'id = "'.$rest_id.'"');

				        	$is_common_correct = 1; # PASS
			                $data['status']		=200;
			                $data['message']	=$this->lang->line('profile_updated_successfully');
			                $data['data']		=array();
			            }
		        	}else if($is_daywise == 1)
		        	{
		        		$is_correct = 0; # FALSE

						if($mon_close_status == ''){
					        $data['status']		=201;
					        $data['message']	=$this->lang->line('mon_close_status_missing');
					        $data['data']		=array();
					    }else if($tue_close_status == ''){
					        $data['status']		=201;
					        $data['message']	=$this->lang->line('tue_close_status_missing');
					        $data['data']		=array();
					    }else if($wed_close_status == ''){
					        $data['status']		=201;
					        $data['message']	=$this->lang->line('wed_close_status_missing');
					        $data['data']		=array();
					    }
					    else if($thu_close_status == ''){
					        $data['status']		=201;
					        $data['message']	=$this->lang->line('thu_close_status_missing');
					        $data['data']		=array();
					    }
					    else if($fri_close_status == ''){
					        $data['status']		=201;
					        $data['message']	=$this->lang->line('fri_close_status_missing');
					        $data['data']		=array();
					    }
					    else if($sat_close_status == ''){
					        $data['status']		=201;
					        $data['message']	=$this->lang->line('sat_close_status_missing');
					        $data['data']		=array();
					    }
					    else if($sun_close_status == ''){
					        $data['status']		=201;
					        $data['message']	=$this->lang->line('sun_close_status_missing');
					        $data['data']		=array();
					    }
					    else
					    {
					    	# MONDAY
					    	if($mon_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
					    	{
					    		if($mon_open_close_time =='')
					    		{
					    			$is_correct = 0;
					                $data['status']		=201;
					                $data['message']	=$this->lang->line('mon_open_close_time');
					                $data['data']		=array();
					        	}else
					        	{
					        		$is_correct = 1;
					        		if($mon_break_status == ''){
					        			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('mon_break_status_missing');
						                $data['data']		=array();
						            }else
						            {
					            		if($mon_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
							        	{
							        		if($mon_break_start_end_time =='')
							        		{
							        			$is_correct = 0;
								                $data['status']		=201;
								                $data['message']	=$this->lang->line('mon_break_start_end_time');
								                $data['data']		=array();
							            	}
							        	}else
							        	{
							        		$is_correct = 1;
							        	}
						            }
					        	}
					    	}else
					    	{
					    		$is_correct = 1;
					    	}

					        # TUESDAY
					    	if($is_correct == 1)
					    	{
					        	if($tue_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
					        	{
					        		if($tue_open_close_time =='')
					        		{
					        			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('tue_open_close_time');
						                $data['data']		=array();
					            	}else
					            	{
					            		if($tue_break_status == ''){
					            			$is_correct = 0;
							                $data['status']		=201;
							                $data['message']	=$this->lang->line('tue_break_status_missing');
							                $data['data']		=array();
							            }else
							            {
						            		if($tue_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
								        	{
								        		if($tue_break_start_end_time ==''){
								        			$is_correct = 0;
									                $data['status']		=201;
									                $data['message']	=$this->lang->line('tue_break_start_end_time');
									                $data['data']		=array();
								            	}
								        	}
							            }
					            	}
					        	}
					    	}

					        # WEDNESDAY
					    	if($is_correct == 1)
					    	{
					        	if($wed_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
					        	{
					        		if($wed_open_close_time =='')
					        		{
					        			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('wed_open_close_time');
						                $data['data']		=array();
					            	}else
					            	{
					            		if($wed_break_status == ''){
					            			$is_correct = 0;
							                $data['status']		=201;
							                $data['message']	=$this->lang->line('wed_break_status_missing');
							                $data['data']		=array();
							            }else
							            {
						            		if($wed_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
								        	{
								        		if($wed_break_start_end_time ==''){
								        			$is_correct = 0;
									                $data['status']		=201;
									                $data['message']	=$this->lang->line('wed_break_start_end_time');
									                $data['data']		=array();
								            	}
								        	}
							            }
					            	}
					        	}
					    	}

					    	# THURSDAY
					    	if($is_correct == 1)
					    	{
					        	if($thu_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
					        	{
					        		if($thu_open_close_time =='')
					        		{
					        			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('thu_open_close_time');
						                $data['data']		=array();
					            	}else
					            	{
					            		if($thu_break_status == ''){
					            			$is_correct = 0;
							                $data['status']		=201;
							                $data['message']	=$this->lang->line('thu_break_status_missing');
							                $data['data']		=array();
							            }else
							            {
						            		if($thu_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
								        	{
								        		if($thu_break_start_end_time ==''){
								        			$is_correct = 0;
									                $data['status']		=201;
									                $data['message']	=$this->lang->line('thu_break_start_end_time');
									                $data['data']		=array();
								            	}
								        	}
							            }
					            	}
					        	}
					    	}

					        # FRIDAY
					    	if($is_correct == 1)
					    	{
					        	if($fri_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
					        	{
					        		if($fri_open_close_time =='')
					        		{
					        			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('fri_open_close_time');
						                $data['data']		=array();
					            	}else
					            	{
					            		if($fri_break_status == ''){
					            			$is_correct = 0;
							                $data['status']		=201;
							                $data['message']	=$this->lang->line('fri_break_status_missing');
							                $data['data']		=array();
							            }else
							            {
						            		if($fri_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
								        	{
								        		if($fri_break_start_end_time ==''){
								        			$is_correct = 0;
									                $data['status']		=201;
									                $data['message']	=$this->lang->line('fri_break_start_end_time');
									                $data['data']		=array();
								            	}
								        	}
							            }
					            	}
					        	}
					    	}

					        # SATURDAY
					    	if($is_correct == 1)
					    	{
					        	if($sat_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
					        	{
					        		if($sat_open_close_time =='')
					        		{
					        			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('sat_open_close_time');
						                $data['data']		=array();
					            	}else
					            	{
					            		if($sat_break_status == ''){
					            			$is_correct = 0;
							                $data['status']		=201;
							                $data['message']	=$this->lang->line('sat_break_status_missing');
							                $data['data']		=array();
							            }else
							            {
						            		if($sat_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
								        	{
								        		if($sat_break_start_end_time ==''){
								        			$is_correct = 0;
									                $data['status']		=201;
									                $data['message']	=$this->lang->line('sat_break_start_end_time');
									                $data['data']		=array();
								            	}
								        	}
							            }
					            	}
					        	}
					    	}

					        # SUNDAY
					    	if($is_correct == 1)
					    	{
					        	if($sun_close_status == 1) # 1 : ON 2 : YES CLOSED FOR FULL MONDAY
					        	{
					        		if($sun_open_close_time =='')
					        		{
					        			$is_correct = 0;
						                $data['status']		=201;
						                $data['message']	=$this->lang->line('sun_open_close_time');
						                $data['data']		=array();
					            	}else
					            	{
					            		if($sun_break_status == ''){
					            			$is_correct = 0;
							                $data['status']		=201;
							                $data['message']	=$this->lang->line('sun_break_status_missing');
							                $data['data']		=array();
							            }else
							            {
						            		if($sun_break_status == 1) # 1 : RESTRO HAS BREAK TIME 2 : NO BREAK TIME
								        	{
								        		if($sun_break_start_end_time ==''){
								        			$is_correct = 0;
									                $data['status']		=201;
									                $data['message']	=$this->lang->line('sun_break_start_end_time');
									                $data['data']		=array();
								            	}
								        	}
							            }
					            	}
					        	}
					    	}

					    	# FINAL UPDATE TO DATABASE ONLY IF VALIDATED COMPLETELY
					    	if($is_correct == 1)
					    	{
					    		# update_user_tbl_array
					        	$update_user_tbl['fullname'] = $name;
					        	$update_user_tbl['email'] = $email;
					        	$update_user_tbl['mobile'] = $contact;
					        	$update_user_tbl['updated_at'] = time();
					        	$this->Common->updateData('users',$update_user_tbl,'id = "'.$tokenData->id.'"');

					        	# Check whether logo image uploaded
					        	if(isset($_FILES['logo_image']['name']) && $_FILES['logo_image']['name'] != '')
					        	{
					        		# That means new profile image uploaded
					        		# Get old image to unlink
			                    	$image = $this->Common->getData('restaurants','logo_image','admin_id = "'.$tokenData->id.'"');

					        		$tmp_name = $_FILES['logo_image']['tmp_name'];
									$extension = pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION);
									
									$f_name = basename($_FILES['logo_image']['name'], '.'.$extension);
									$image_name = trim($f_name)."_".time().'.'.$extension;
									$logo_file_path = "assets/merchant/merchant_logo_image/".$image_name; // Send this string to database
									# replace space with _ from image name
			                    	$logo_file_path = str_replace(" ","_",$logo_file_path);
									$moved = move_uploaded_file($_FILES['logo_image']['tmp_name'], $logo_file_path);

									if(!$moved)
									{
										$data['status']		=201;
								        $data['message']	=$this->lang->line('something_went_wrong');
								        $data['data']		=array();
									}
									else
									{
										# Create update array key for logo image
										$update_array_restro_table['logo_image'] = trim($logo_file_path);
										# That means image uploaded successfully so we can unlink old imge
				                        if(!empty($image[0]['logo_image']))
				                        {
				                            unlink($image[0]['logo_image']);
				                        }
									}
					        	}

					        	# Check whether Document image uploaded
					        	if($documents != '')
					        	{
									$update_array_restro_table['uploaded_document'] = $documents;		        		
					        	}


					        	$update_array_restro_table['rest_name'] = $name;
					        	$update_array_restro_table['is_order_now_accept'] = $is_order_now_accept;
					        	$update_array_restro_table['is_self_pickup_accept'] = $is_self_pickup_accept;
					        	$update_array_restro_table['is_order_later_accept'] = $is_order_later_accept;
					        	$update_array_restro_table['is_dinein_accept'] = $is_dinein_accept;
					        	$update_array_restro_table['delivery_handled_by'] = $delivery_handeled_by;
					        	$update_array_restro_table['updated_at'] = time();
						    	$update_array_restro_table['time_mode'] = 2; # 1 - for every day, 2 - for Specific day
						    	$this->Common->updateData('restaurants',$update_array_restro_table,'id = "'.$rest_id.'"');
				                

						    	# FOR rest_time_daywise table first we need to check whether entry for this restaurant exists in rest_time_daywise table because it may possible that merchant chooses everyday on create profile and chooses SPECIFIC on update profile so we can not directly update the table.
						    	# If entry exists then update else insert
						    	$insert_upd_day_wise['mon_close_status'] = $mon_close_status;
								$insert_upd_day_wise['mon_open_close_time'] = $mon_open_close_time;
								$insert_upd_day_wise['mon_break_status'] = $mon_break_status;
								$insert_upd_day_wise['mon_break_start_end_time'] = $mon_break_start_end_time;
								$insert_upd_day_wise['tue_close_status'] = $tue_close_status;
								$insert_upd_day_wise['tue_open_close_time'] = $tue_open_close_time;
								$insert_upd_day_wise['tue_break_status'] = $tue_break_status;
								$insert_upd_day_wise['tue_break_start_end_time'] = $tue_break_start_end_time;
								$insert_upd_day_wise['wed_close_status'] = $wed_close_status;
								$insert_upd_day_wise['wed_open_close_time'] = $wed_open_close_time;
								$insert_upd_day_wise['wed_break_status'] = $wed_break_status;
								$insert_upd_day_wise['wed_break_start_end_time'] = $wed_break_start_end_time;
								$insert_upd_day_wise['thu_close_status'] = $thu_close_status;
								$insert_upd_day_wise['thu_open_close_time'] = $thu_open_close_time;
								$insert_upd_day_wise['thu_break_status'] = $thu_break_status;
								$insert_upd_day_wise['thu_break_start_end_time'] = $thu_break_start_end_time;
								$insert_upd_day_wise['fri_close_status'] = $fri_close_status;
								$insert_upd_day_wise['fri_open_close_time'] = $fri_open_close_time;
								$insert_upd_day_wise['fri_break_status'] = $fri_break_status;
								$insert_upd_day_wise['fri_break_start_end_time'] = $fri_break_start_end_time;
								$insert_upd_day_wise['sat_close_status'] = $sat_close_status;
								$insert_upd_day_wise['sat_open_close_time'] = $sat_open_close_time;
								$insert_upd_day_wise['sat_break_status'] = $sat_break_status;
								$insert_upd_day_wise['sat_break_start_end_time'] = $sat_break_start_end_time;
								$insert_upd_day_wise['sun_close_status'] = $sun_close_status;
								$insert_upd_day_wise['sun_open_close_time'] = $sun_open_close_time;
								$insert_upd_day_wise['sun_break_status'] = $sun_break_status;
								$insert_upd_day_wise['sun_break_start_end_time'] = $sun_break_start_end_time;
								$insert_upd_day_wise['updated_at'] = time();


						    	$is_avl_in_daywise = $this->Common->getData('rest_time_daywise','id','rest_id = "'.$rest_id.'"');
						    	if(count($is_avl_in_daywise) > 0)
						    	{
						    		# So we need to update
						        	$this->Common->updateData('rest_time_daywise',$insert_upd_day_wise,'rest_id = "'.$rest_id.'"');
						    	}else
						    	{
						    		# Need to insert
						    		$insert_upd_day_wise['created_at'] = time();
						    		$insert_upd_day_wise['rest_id'] = $rest_id;
						    		$this->Common->insertData('rest_time_daywise',$insert_upd_day_wise);
						    	}

				                $data['status']		=200;
				                $data['message']	=$this->lang->line('profile_updated_successfully');
				                $data['data']		=array();
					    	}
					    }
		        	}
            	}
            }
		    $this->response($data, $data['status']);
    	}	catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # ----------------- MERCHANT_PROFILE_SECTION END -------------------- #

	#offline_action_post start
	
	# This function is used to set restautant online or offline based on offline_type variable.
	# If offline_type == 1 and offline_tag == 3 then take comma seperated timestamps (From and To)
	#offline_type  1 - GOING OFFLINE 2 - COMING BACK ONLINE
	/*If offline_type = 2 then no need to pass offline_tag and offline_value
	#DB_offline_tag 1 - Hour 2 - Day 3 - Multiple days
	=> If offline_tag is equal to 2 then pass single timstamp for selected date.
	=> If offline_tag is equal to 3 then pass two timestamp COMMA seperated*/
    public function merchant_offline_action_post()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	$offline_type = !empty($_POST['offline_type'])?$this->db->escape_str($_POST['offline_type']):'';
	    	#offline_type	1 - GOING OFFLINE 2 - COMING BACK ONLINE
    		$offline_tag = !empty($_POST['offline_tag'])?$this->db->escape_str($_POST['offline_tag']):'';
    		#DB_offline_tag	1 - Hour 2 - Day 3 - Multiple days
	        $offline_value = !empty($_POST['offline_value'])?$this->db->escape_str($_POST['offline_value']):'';

			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }else if($offline_type == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('offline_type_missing');
                $data['data']		=array();
            }else{
		    	$rest_id = $this->get_restaurant_id($tokenData->id);
			    if($offline_type == 1) # GOING OFFLINE
			    {
			    	# One important case noted here is that suppose any restaurant went offline till 5 pm now 5 pm is passed and toggle is also on due to front end check but still we have an entry in offline table because we delete entry only when merchant manually come back online. So in going offline also we will first delete data from offlne table for this restaurant and add new entry as per given data
			    	$this->Common->deleteData('rest_offline','rest_id = "'.$rest_id.'"');

			    	if($offline_tag == ''){
		            	$data['status']		=201;
		                $data['message']	=$this->lang->line('offline_tag_missing');
		                $data['data']		=array();
		            }else if($offline_value ==''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('offline_value_missing');
		                $data['data']		=array();
		            }else
		            {
		            	if($offline_tag == 1) # HOURS : If value is 3 that means go offline FOR NEXT 3 HOURS
		            	{
		            		# Value will always be passed in HOURS:MINUTES Format. Ex 4 hours and 30 minutes (4:30) and 4 hours (4:00)
		            		if(strpos($offline_value, ":") !== false) # We are keeping : to separate the hours and minutes value
		            		{
		            			# That means minutes also exists (Ex 4 hours and 30 minutes 4:30)
		            			$hm = explode(":", $offline_value);
		            			$hours = $hm[0];
		            			$minutes = $hm[1];

		            			$offline_from = time();
								$to_add_h = $hours * (60 * 60); # Convert the hours into seconds.
								$to_add_m = $minutes * 60; # Convert the minutes into seconds.
								$offline_till = $offline_from + $to_add_h + $to_add_m;

		            		}else # However it won't be used but just kept it here.
		            		{
		            			# Only Hours value exists (Ex Next 4 hours)
		            			$hours = $offline_value;
			            		$offline_from =  time();
								$to_add = $hours * (60 * 60); # Convert the hours into seconds.
								$offline_till = $offline_from + $to_add;
		            		}
		            	}else if($offline_tag == 2) # A day i.e. Single timstamp value of selected date
		            	{
		            		$offline_from =  $offline_value;
		            		# For offline_till here mobile team should pass timestamp for selected date. Ex today's date is 22/03/2021 and selected date is 25/03/2021 that means restaurant will be offline on 25/03/2021 so mobile team will pass timstamp for 25/03/2021 00:00:00 (1616630400) and we will set to date of 25/03/2021 as 25/03/2021 23:59:59 that we will add 24 hours to this date.
		            		$to_add = 24 * (60 * 60); # Convert the hours into seconds.
							$offline_till = $offline_from + $to_add;
		            	}else if($offline_tag == 3) # Multiple days i.e. From and To , two timestamp will be passed from mobile team
		            	{
		            		# Ex : Today's date is 22nd march and Restaurant is going offline from 23rd to 25th of march so how we will proceed with it? Simple we have two timstamps one is from 23/03/2021 00:00:00 to 25/03/2021 23:59:59 so this type of timestamp must be provided from the mobile team COMMA Separated and we are going to simply explode and update them in DB
		            		$exp = explode(",", $offline_value);
		            		$offline_from = $exp[0];
		            		$offline_till = $exp[1];
		            	}else
		            	{
		            		$data['status']		=201;
			                $data['message']	=$this->lang->line('invalid_offline_tag');
			                $data['data']		=array();
		            	}

		            	$insert_array['rest_id'] = $rest_id;
		            	$insert_array['offline_tag'] = $offline_tag;
		        		$insert_array['offline_value'] = $offline_value;
		        		$insert_array['offline_from'] = $offline_from;
		        		$insert_array['offline_to'] = $offline_till;
		        		$insert_array['created_at'] = time();
		        		$insert_array['updated_at'] = time();

        				$this->Common->insertData('rest_offline',$insert_array);

        				$data['status']		=200;
						$data['message']	=$this->lang->line('update_success');
						$data['data']		=array();
		            }
			    }else if($offline_type == 2) # COMING BACK ONLINE
			    {
			    	$this->Common->deleteData('rest_offline','rest_id = "'.$rest_id.'"');

			    	$data['status']		=200;
					$data['message']	=$this->lang->line('update_success');
					$data['data']		=array();
			    }
		    }
	        # REST_Controller provide this method to send responses
	    	$this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    #offline_action_post End

    # ----------- MENU_MANAGEMENT START ------------ #
    # category_product_get Start
    # This function is used to get the restaurant categories and their prducts with status (For both; Category status and product status)
    public function category_product_get()
    {
    	try{
    		$tokenData = $this->verify_request();

    		$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
    		$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;

	    	if($tokenData === false)
	    	{
	                $status = parent::HTTP_UNAUTHORIZED;
	                $data['status']	 = $status;
	                $data['message'] = $this->lang->line('unauthorized_access');
            }else
            {
            	$rest_id = $this->get_restaurant_id($tokenData->id);
            	$categories = $this->Common->getData('categories','id,category_name,category_status','category_status = 1 AND restaurant_id = "'.$rest_id.'"','','','','',$limit,$page);

            	if(count($categories) > 0)
            	{
	            	foreach ($categories as $index=>$cat) 
	            	{
	            		$response[$index]['id'] = $cat['id'];
	            		$response[$index]['name'] = $cat['category_name'];
						$status['category_status'] = $this->Common->getData('categories_offline','*','category_id = "'.$cat['id'].'"');
						if(count($status['category_status']) > 0)
						{
		            		$response[$index]['offline_tag'] = $status['category_status'][0]['offline_tag'];
		            		$response[$index]['offline_value'] = $status['category_status'][0]['offline_value'];
		            		$response[$index]['offline_from'] = $status['category_status'][0]['offline_from'];
		            		$response[$index]['offline_to'] = $status['category_status'][0]['offline_to'];
						}else
						{
							$response[$index]['offline_tag'] = 0;
		            		$response[$index]['offline_value'] = 0;
		            		$response[$index]['offline_from'] = 0;
		            		$response[$index]['offline_to'] = 0;
						}

						$prod_query = "SELECT products.id AS product_id,products.product_name ,products.price,products.offer_price,products.product_image,products_offline.id AS off_id ,products_offline.rest_id , products_offline.category_id , products_offline.offline_tag,products_offline.offline_value,products_offline.offline_from,products_offline.offline_to FROM products LEFT OUTER JOIN products_offline ON products.id = products_offline.product_id WHERE products.category_id = ".$cat['id']." AND products.restaurant_id = ".$rest_id." LIMIT ". $page.",".$limit;
	            		$response[$index]['product'] = $this->Common->custom_query($prod_query,'get');
	            		if(count($response[$index]['product']) > 0)
	            		{
	            			foreach ($response[$index]['product'] as $key => $value) 
	            			{
	            				$is_variant = $this->check_if_variant_available($value['product_id']);
	            				if(!empty($is_variant))
	            				{
	            					$response[$index]['product'][$key]['is_variant_avl'] = 1;
	            				}else
	            				{
	            					$response[$index]['product'][$key]['is_variant_avl'] = 0;
	            				}
	            			}
	            		}
	            	}
	                $data['status']	= 200;
	                $data['message'] = $this->lang->line('success'); 
	                $data['data'] = $response;
            	}else
            	{
            		$data['status']	= 201;
	                $data['message'] = $this->lang->line('no_data_found'); 
	                $data['data'] = array();
            	}
            }
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # category_product_get End

    # Disable Category
    # enable_disable_category_post start
    # This function is used to Enable OR Disable any category using toggle. It will also Enable / Disable the products associated with the category. Enabling and Disabling is based on the action passed
    # Action_status 1 : TO ENABLE 2 : TO DISABLE
    public function enable_disable_category_post()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	$offline_type = !empty($_POST['offline_type'])?$this->db->escape_str($_POST['offline_type']):'';
	    	#offline_type	1 - DISABLE 2 - ENABLE
    		$offline_tag = !empty($_POST['offline_tag'])?$this->db->escape_str($_POST['offline_tag']):'';
    		#DB_offline_tag	1 - Hour 2 - Day 3 - Multiple days
	        $offline_value = !empty($_POST['offline_value'])?$this->db->escape_str($_POST['offline_value']):'';
	        $category_id = !empty($_POST['category_id'])?$this->db->escape_str($_POST['category_id']):'';
	        

			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }else if($offline_type == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('offline_type_missing');
                $data['data']		=array();
            }else if($category_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('category_missing');
                $data['data']		=array();
            }else{
		    	$rest_id = $this->get_restaurant_id($tokenData->id);
			    if($offline_type == 1) # DISABLE
			    {
			    	if($offline_tag == ''){
		            	$data['status']		=201;
		                $data['message']	=$this->lang->line('offline_tag_missing');
		                $data['data']		=array();
		            }else if($offline_value ==''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('offline_value_missing');
		                $data['data']		=array();
		            }else
		            {
		            	if($offline_tag == 1) # HOURS : If value is 3 that means DISABLE FOR NEXT 3 HOURS
		            	{
		            		# Value will always be passed in HOURS:MINUTES Format. Ex 4 hours and 30 minutes (4:30) and 4 hours (4:00)
		            		if(strpos($offline_value, ":") !== false) # We are keeping : to separate the hours and minutes value
		            		{
		            			# That means minutes also exists (Ex 4 hours and 30 minutes 4:30)
		            			$hm = explode(":", $offline_value);
		            			$hours = $hm[0];
		            			$minutes = $hm[1];

		            			$offline_from = time();
								$to_add_h = $hours * (60 * 60); # Convert the hours into seconds.
								$to_add_m = $minutes * 60; # Convert the minutes into seconds.
								$offline_till = $offline_from + $to_add_h + $to_add_m;

		            		}else # However it won't be used but just kept it here.
		            		{
		            			# Only Hours value exists (Ex Next 4 hours)
		            			$hours = $offline_value;
			            		$offline_from =  time();
								$to_add = $hours * (60 * 60); # Convert the hours into seconds.
								$offline_till = $offline_from + $to_add;
		            		}
		            	}else if($offline_tag == 2) # A day i.e. Single timstamp value of selected date
		            	{
		            		$offline_from =  $offline_value;
		            		# For offline_till here mobile team should pass timestamp for selected date. Ex today's date is 22/03/2021 and selected date is 25/03/2021 that means restaurant will be offline on 25/03/2021 so mobile team will pass timstamp for 25/03/2021 00:00:00 (1616630400) and we will set to date of 25/03/2021 as 25/03/2021 12:59:59 that we will add 24 hours to this date.
		            		$to_add = 24 * (60 * 60); # Convert the hours into seconds.
							$offline_till = $offline_from + $to_add;
		            	}else if($offline_tag == 3) # Multiple days i.e. From and To , two timestamp will be passed from mobile team
		            	{
		            		# Ex : Today's date is 22nd march and Restaurant is going offline from 23rd to 25th of march so how we will proceed with it? Simple we have two timstamps one is from 23/03/2021 00:00:00 to 25/03/2021 11:59:59 so this type of timestamp must be provided from the mobile team COMMA Separated and we are going to simply explode and update them in DB
		            		$exp = explode(",", $offline_value);
		            		$offline_from = $exp[0];
		            		$offline_till = $exp[1];
		            	}else
		            	{
		            		$data['status']		=201;
			                $data['message']	=$this->lang->line('invalid_offline_tag');
			                $data['data']		=array();
		            	}

		            	$insert_array['rest_id'] = $rest_id;
		            	$insert_array['category_id'] = $category_id;
		            	$insert_array['offline_tag'] = $offline_tag;
		        		$insert_array['offline_value'] = $offline_value;
		        		$insert_array['offline_from'] = $offline_from;
		        		$insert_array['offline_to'] = $offline_till;
		        		$insert_array['created_at'] = time();
		        		$insert_array['updated_at'] = time();

        				$id = $this->Common->insertData('categories_offline',$insert_array);

        				# Now if category is going offline then at the same time products associated with that category will also go offline for the same time.

        				# Get all the products of given category id and restaurant id.
        				$products = $this->Common->getData('products','id','category_id = "'.$category_id.'" AND restaurant_id = "'.$rest_id.'"');
    					
    					# Here we need to apply a check for stopping multiple entries of same product in below scenario.
    					/* 
    					# First we disable product id 5 of category id 1 so for product id 5 there will be an entry to ONLY products_offline table as per enable_disable_product_post API.
    					Now we disable category id 1. So this category Id 1 already contain product id 5 which is already disabled in first step and its entry already exists in products_offline table. So if we again  make same entry then it will be WRONG. 
    					So first we have to check whether any entry with this cat id and this prod id already exists then we need to update the ROW ELSE insert the row
    					*/
    					$product_arr['rest_id'] = $rest_id;
		            	$product_arr['category_id'] = $category_id;
		            	$product_arr['offline_tag'] = $offline_tag;
		        		$product_arr['offline_value'] = $offline_value;
		        		$product_arr['offline_from'] = $offline_from;
		        		$product_arr['offline_to'] = $offline_till;
		        		$product_arr['created_at'] = time();
		        		$product_arr['updated_at'] = time();
        				
        				# When category is going offline then all products will also go offline for same period of time so need to work for products
        				foreach ($products as $product) 
        				{
    						$check = $this->Common->getData('products_offline','id','category_id = "'.$category_id.'" AND product_id = "'.$product['id'].'"');
    						if(count($check) > 0) # That means this product already exists
    						{
    							# update it
    							$this->Common->updateData('products_offline',$product_arr,'id = "'.$check[0]['id'].'"');
    						}else
    						{
    							# insert the data
				            	$product_arr['product_id'] = $product['id'];
				        		$this->Common->insertData('products_offline',$product_arr);
    						}
        				}

        				$data['status']		=200;
						$data['message']	=$this->lang->line('update_success');
						$data['data']		=array();
		            }
			    }else if($offline_type == 2) # TO ENABLE
			    {
			    	# Delete from categories offline table
			    	$this->Common->deleteData('categories_offline','rest_id = "'.$rest_id.'" AND category_id = "'.$category_id.'"');
			    	# Delte from products_offline table
			    	$this->Common->deleteData('products_offline','rest_id = "'.$rest_id.'" AND category_id = "'.$category_id.'"');

			    	$data['status']		=200;
					$data['message']	=$this->lang->line('update_success');
					$data['data']		=array();
			    }
		    }
	        # REST_Controller provide this method to send responses
	    	$this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # enable_disable_category_post end

    # enable_disable_product_post start
    # This function is used to Enable OR Disable any product using toggle.Enabling and Disabling is based on the action passed
    # Action_status 1 : TO ENABLE 2 : TO DISABLE
    public function enable_disable_product_post()
    {
        try{
	    	$tokenData = $this->verify_request();
	    	$offline_type = !empty($_POST['offline_type'])?$this->db->escape_str($_POST['offline_type']):'';
	    	#offline_type	1 - DISABLE 2 - ENABLE
    		$offline_tag = !empty($_POST['offline_tag'])?$this->db->escape_str($_POST['offline_tag']):'';
    		#DB_offline_tag	1 - Hour 2 - Day 3 - Multiple days
	        $offline_value = !empty($_POST['offline_value'])?$this->db->escape_str($_POST['offline_value']):'';
	        $product_id = !empty($_POST['product_id'])?$this->db->escape_str($_POST['product_id']):'';
	        
			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }else if($offline_type == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('offline_type_missing');
                $data['data']		=array();
            }else if($product_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('product_missing');
                $data['data']		=array();
            }else{
		    	$rest_id = $this->get_restaurant_id($tokenData->id);
			    if($offline_type == 1) # DISABLE
			    {
			    	if($offline_tag == ''){
		            	$data['status']		=201;
		                $data['message']	=$this->lang->line('offline_tag_missing');
		                $data['data']		=array();
		            }else if($offline_value ==''){
		                $data['status']		=201;
		                $data['message']	=$this->lang->line('offline_value_missing');
		                $data['data']		=array();
		            }else
		            {
		            	if($offline_tag == 1) # HOURS : If value is 3 that means DISABLE FOR NEXT 3 HOURS
		            	{
		            		# Value will always be passed in HOURS:MINUTES Format. Ex 4 hours and 30 minutes (4:30) and 4 hours (4:00)
		            		if(strpos($offline_value, ":") !== false) # We are keeping : to separate the hours and minutes value
		            		{
		            			# That means minutes also exists (Ex 4 hours and 30 minutes 4:30)
		            			$hm = explode(":", $offline_value);
		            			$hours = $hm[0];
		            			$minutes = $hm[1];

		            			$offline_from = time();
								$to_add_h = $hours * (60 * 60); # Convert the hours into seconds.
								$to_add_m = $minutes * 60; # Convert the minutes into seconds.
								$offline_till = $offline_from + $to_add_h + $to_add_m;

		            		}else # However it won't be used but just kept it here.
		            		{
		            			# Only Hours value exists (Ex Next 4 hours)
		            			$hours = $offline_value;
			            		$offline_from =  time();
								$to_add = $hours * (60 * 60); # Convert the hours into seconds.
								$offline_till = $offline_from + $to_add;
		            		}
		            	}else if($offline_tag == 2) # A day i.e. Single timstamp value of selected date
		            	{
		            		$offline_from =  $offline_value;
		            		# For offline_till here mobile team should pass timestamp for selected date. Ex today's date is 22/03/2021 and selected date is 25/03/2021 that means restaurant will be offline on 25/03/2021 so mobile team will pass timstamp for 25/03/2021 00:00:00 (1616630400) and we will set to date of 25/03/2021 as 25/03/2021 12:59:59 that we will add 24 hours to this date.
		            		$to_add = 24 * (60 * 60); # Convert the hours into seconds.
							$offline_till = $offline_from + $to_add;
		            	}else if($offline_tag == 3) # Multiple days i.e. From and To , two timestamp will be passed from mobile team
		            	{
		            		# Ex : Today's date is 22nd march and Restaurant is going offline from 23rd to 25th of march so how we will proceed with it? Simple we have two timstamps one is from 23/03/2021 00:00:00 to 25/03/2021 11:59:59 so this type of timestamp must be provided from the mobile team COMMA Separated and we are going to simply explode and update them in DB
		            		$exp = explode(",", $offline_value);
		            		$offline_from = $exp[0];
		            		$offline_till = $exp[1];
		            	}else
		            	{
		            		$data['status']		=201;
			                $data['message']	=$this->lang->line('invalid_offline_tag');
			                $data['data']		=array();
		            	}

        				# Get category id of the product
        				$category = $this->Common->getData('products','id,category_id','id = "'.$product_id.'"');
    					$category_id = $category[0]['category_id'];

    					$insert_product['rest_id'] = $rest_id;
		            	$insert_product['category_id'] = $category_id;
    					$insert_product['product_id'] = $product_id;
		            	$insert_product['offline_tag'] = $offline_tag;
		        		$insert_product['offline_value'] = $offline_value;
		        		$insert_product['offline_from'] = $offline_from;
		        		$insert_product['offline_to'] = $offline_till;
		        		$insert_product['created_at'] = time();
		        		$insert_product['updated_at'] = time();

			        	$this->Common->insertData('products_offline',$insert_product);
        				
        				$data['status']		=200;
						$data['message']	=$this->lang->line('update_success');
						$data['data']		=array();
		            }
			    }else if($offline_type == 2) # TO ENABLE
			    {
			    	$this->Common->deleteData('products_offline','rest_id = "'.$rest_id.'" AND product_id = "'.$product_id.'"');
			    	# If any single product is coming back online then we need to Enable the category also.
			    	# First check whether category belongs to this product is also in category_offlie table? If yes then delete else no need
			    	# But we can directly fire delete query 
			    	# Get category id 

			    	$cat_id = $this->Common->getData('products','category_id','id = "'.$product_id.'"');
			    	$category_id = $cat_id[0]['category_id'];

			    	$this->Common->deleteData('categories_offline','rest_id = "'.$rest_id.'" AND category_id = "'.$category_id.'"');

			    	$data['status']		=200;
					$data['message']	=$this->lang->line('update_success');
					$data['data']		=array();
			    }
		    }
	        # REST_Controller provide this method to send responses
	    	$this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # enable_disable_product_post end

    # view_all_disabled_item start
    # This function is used to get the disabled items for category and products.
    # It will always return the category with the status and will return products only if they are disabled.
    public function view_all_disabled_item_get()
    {
    	try{
    		$tokenData = $this->verify_request();

    		$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
    		$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;

	    	if($tokenData === false)
	    	{
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message'] = $this->lang->line('unauthorized_access');
            }else
            {
            	$rest_id = $this->get_restaurant_id($tokenData->id);
            	// $categories = $this->Common->getData('categories','id,category_name,category_status','category_status = 1 AND restaurant_id = "'.$rest_id.'"','','','','',$limit,$page);
            	$categories = $this->Common->getData('categories','id,category_name,category_status','category_status = 1 AND restaurant_id = "'.$rest_id.'"');
            	// echo $this->db->last_query();
            	// echo "<pre>";
            	// print_r($categories);
            	if(count($categories) > 0)
            	{
	            	$key = 0;
	            	foreach ($categories as $index=>$cat) 
	            	{
						$status['category_status'] = $this->Common->getData('categories_offline','*','category_id = "'.$cat['id'].'"');
						if(count($status['category_status']) > 0)
						{
		            		$offline_tag = $status['category_status'][0]['offline_tag'];
		            		$offline_value = $status['category_status'][0]['offline_value'];
		            		$offline_from = $status['category_status'][0]['offline_from'];
		            		$offline_to = $status['category_status'][0]['offline_to'];

		            		if($offline_from < time() && time() < $offline_to)
		            		{
		            			# CURRENTLY OFFLINE
			            		$response[$key]['id'] = $cat['id'];
			            		$response[$key]['name'] = $cat['category_name'];
		            			
		            			$response[$key]['offline_tag'] = $status['category_status'][0]['offline_tag'];
			            		$response[$key]['offline_value'] = $status['category_status'][0]['offline_value'];
			            		$response[$key]['offline_from'] = $status['category_status'][0]['offline_from'];
			            		$response[$key]['offline_to'] = $status['category_status'][0]['offline_to'];
								
								$prod_query = "SELECT products.id AS product_id,products.product_name ,products.price,products.offer_price,products.product_image,products_offline.id AS off_id ,products_offline.rest_id , products_offline.category_id , products_offline.offline_tag,products_offline.offline_value,products_offline.offline_from,products_offline.offline_to FROM products INNER JOIN products_offline ON products.id = products_offline.product_id WHERE products.product_status = 1 AND products.category_id = ".$cat['id']." AND products.restaurant_id = ".$rest_id." LIMIT ". $page.",".$limit;
			            		$product_array = $this->Common->custom_query($prod_query,'get');
			            		if(count($product_array) > 0)
			            		{
			            			$response[$key]['product'] = $product_array;
			            			foreach ($response[$key]['product'] as $keyv => $value) 
			            			{
			            				$is_variant = $this->check_if_variant_available($value['product_id']);
			            				if(!empty($is_variant))
			            				{
			            					$response[$key]['product'][$keyv]['is_variant_avl'] = 1;
			            				}else
			            				{
			            					$response[$key]['product'][$keyv]['is_variant_avl'] = 0;
			            				}
			            			}
			            			$key++;
			            		}
		            		}
						}else
						{
							$prod_query = "SELECT products.id AS product_id,products.product_name ,products.price,products.offer_price,products.product_image,products_offline.id AS off_id ,products_offline.rest_id , products_offline.category_id , products_offline.offline_tag,products_offline.offline_value,products_offline.offline_from,products_offline.offline_to FROM products INNER JOIN products_offline ON products.id = products_offline.product_id WHERE products.product_status = 1 AND products.category_id = ".$cat['id']." AND products.restaurant_id = ".$rest_id." LIMIT ". $page.",".$limit;
		            		$product_array = $this->Common->custom_query($prod_query,'get');
		            		if(count($product_array) > 0)
		            		{
		            			// $response[$key]['id'] = $cat['id'];
		            			// $response[$key]['name'] = $cat['category_name'];

		            			// $response[$key]['offline_tag'] = 'null';
			            		// $response[$key]['offline_value'] = 'null';
			            		// $response[$key]['offline_from'] = 'null';
			            		// $response[$key]['offline_to'] = 'null';
		            			
		            			foreach ($product_array as $keyv_p => $value) 
		            			{
		            				$do_plus = 0;
		            				if($value['offline_from'] < time() && time() < $value['offline_to'] )
		            				{
		            					$response[$key]['id'] = $cat['id'];
		            					$response[$key]['name'] = $cat['category_name'];
		            					$response[$key]['offline_tag'] = 'null';
					            		$response[$key]['offline_value'] = 'null';
					            		$response[$key]['offline_from'] = 'null';
					            		$response[$key]['offline_to'] = 'null';
		            					$response[$key]['product'] = $product_array;
			            				$is_variant = $this->check_if_variant_available($value['product_id']);
			            				if(!empty($is_variant))
			            				{
			            					$response[$key]['product'][$keyv_p]['is_variant_avl'] = 1;
			            				}else
			            				{
			            					$response[$key]['product'][$keyv_p]['is_variant_avl'] = 0;
			            				}
		            				}
		            			}
		            			if($do_plus == 1)
		            			{
		            				$key++;
		            			}
		            		}
						}
	            	}

	            	// echo "<pre>";
	            	// print_r($response);
	            	if(!empty($response))
	            	{
		                $data['status']	= 200;
		                $data['message'] = $this->lang->line('success'); 
		                $data['data'] = $response;
	            	}else
	            	{
	            		$data['status']	= 201;
		                $data['message'] = $this->lang->line('no_data_found'); 
		                // $data['message'] = 'test_no_data_found'; 
		                $data['data'] = array();		
	            	}
            	}else
            	{
            		$data['status']	= 201;
	                $data['message'] = $this->lang->line('no_data_found'); 
	                $data['data'] = array();
            	}
            }
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }
    # view_all_disabled_item end

    # ----------- MENU_MANAGEMENT END ------------ #
    # CMS Start
    # This function is used to get the content for page based on the predefined passed Keys
    # termsandconditions , privacypolicy , aboutus , contactus , faq
    public function cms_content_get()
    {
    	try{
			$page_key = !empty($_GET['page_key'])?$this->db->escape_str($_GET['page_key']):'';
			# termsandconditions , privacypolicy , aboutus , contactus , faq
			$page_key = strtolower(trim($page_key));
			if($page_key == '')
			{
				$data['status']		=201;
	            $data['message']	=$this->lang->line('give_page_key');
	            $data['data']		=array();
			}else{
            	
            	$response = $this->Common->getData('cms','*','page_key = "'.$page_key.'"');
            	if(!empty($response))
            	{
		    		$data['status']     =200;
		            $data['message']    =$this->lang->line('success');
		            $data['data']       =$response[0];
            	}
            	else
            	{
            		$data['status']     =200;
		            $data['message']    =$this->lang->line('invalid_page_key');
		            $data['data']       =array();	
            	}
            }
    	
    		# REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {
            log_message('error', $e);

            $data['status']     =500;
            $data['message']    =$this->lang->line('internal_server_error');
            $data['data']       =array(); 
            $this->response($data, $data['status']);
        }
    }
    # CMS END

    # This function is used to get all orders as per the order status provided
    public function merchant_all_orders_screen_get()
    {
    	try{
    		$tokenData = $this->verify_request();

    		$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
    		$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;
    		# 0 - Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready
    		# Here for PENDING tab we need to return order that are either 0 status or 1 (i.e. pending or accepted)
    		$order_status = $_GET['order_status'];
	    	
	    	if($tokenData === false)
	    	{
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message'] = $this->lang->line('unauthorized_access');
            }else if($order_status == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('order_status_missing');
                $data['data']		=array();
            }else
            {
            	$rest_id = $this->get_restaurant_id($tokenData->id);
            	// echo "rest id is ".$rest_id;
            	if($order_status == 0) # So return pending as well as accepted
            	{
            		// $order = $this->Common->getData('orders','*','restaurant_id = "'.$rest_id.'" AND (order_status = 1 OR order_status = 0)','','','id','DESC',$limit,$page);
            		$query = "SELECT orders.*,id,order_type,order_status,created_at,pickup_time_from, ( CASE WHEN order_type = '1' AND preparation_time_when_accepted = '' THEN created_at + preparation_time_when_ordered WHEN order_type = '1' AND preparation_time_when_accepted != '' THEN created_at + preparation_time_when_accepted ELSE pickup_time_from END) AS sort_pickup_time FROM orders WHERE paid_status = 1 AND restaurant_id = '".$rest_id."' AND (order_status = 1 OR order_status = 0) ORDER BY `sort_pickup_time` ASC";
            		$order = $this->Common->custom_query($query,'get');
            		// echo $this->db->last_query();
            	}else if ($order_status == 3) # COMPLETED
            	{
            		/* OLD CODE */
            		/*$query = "SELECT orders.*,id,order_type,order_status,created_at,pickup_time_from, ( CASE WHEN order_type = '1' AND preparation_time_when_accepted = '' THEN created_at + preparation_time_when_ordered WHEN order_type = '1' AND preparation_time_when_accepted != '' THEN created_at + preparation_time_when_accepted ELSE pickup_time_from END) AS sort_pickup_time FROM orders WHERE restaurant_id = '".$rest_id."' AND (order_status = 3 OR order_status = 5) ORDER BY `sort_pickup_time` ASC";
            		$order = $this->Common->custom_query($query,'get');*/

            		/* New code */
            		$query = "SELECT orders.*,id,order_type,order_status,created_at,updated_at,pickup_time_from FROM orders WHERE paid_status = 1 AND restaurant_id = '".$rest_id."' AND (order_status = 3 OR order_status = 5) ORDER BY `updated_at` DESC";
            		$order = $this->Common->custom_query($query,'get');
            		/* New code */
            	}else
            	{
            		// $order = $this->Common->getData('orders','*','restaurant_id = "'.$rest_id.'" AND order_status = "'.$order_status.'"','','','id','DESC',$limit,$page);
            		$query = "SELECT orders.*,id,order_type,order_status,created_at,pickup_time_from, ( CASE WHEN order_type = '1' AND preparation_time_when_accepted = '' THEN created_at + preparation_time_when_ordered WHEN order_type = '1' AND preparation_time_when_accepted != '' THEN created_at + preparation_time_when_accepted ELSE pickup_time_from END) AS sort_pickup_time FROM orders WHERE paid_status = 1 AND restaurant_id = '".$rest_id."'  AND order_status = ".$order_status." ORDER BY `sort_pickup_time` ASC";
            		$order = $this->Common->custom_query($query,'get');
            		// echo $this->db->last_query();
            	}

            	$rest_delivery_time  =  $this->Common->getData('restaurants','delivery_time','id = "'.$rest_id.'"'); 
            	$rest_delivery_time = $rest_delivery_time[0]['delivery_time'];
			   	# If restaurant has not set any delivery time then we need to take value from settings table
			   	if($rest_delivery_time == 0 || $rest_delivery_time == '')
			   	{
			        $basic_delv_time = $this->Common->getData('settings','value','name = "basic_delivery_time"');
			        $rest_delivery_time = $basic_delv_time[0]['value'];
			   	}

			   	// echo "<pre>";
			   	// print_r($order);die;
            	if(count($order) > 0)
            	{
            		$order_product_details = array();
            		foreach($order as $key => $ord)
            		{
            			// echo $ord['id'];
            			// print_r($ord['id']);
            			// echo "<br>";
            			$order[$key]['delivery_time'] = $rest_delivery_time;
            			$order_product_details = $this->Common->getData('order_product_details','order_product_details.*','order_id = "'.$ord['id'].'" AND status != 1');
						$order[$key]['product_detail'] = $order_product_details;
						// echo "<pre>";
						// print_r($order_product_details);


						if(!empty($order_product_details))
						{
							foreach ($order_product_details as $in_key => $value) 
							{
								$cart_var_query = "SELECT * FROM order_product_variant_details 
								INNER JOIN orders ON orders.id = order_product_variant_details.order_id 
								
								WHERE `order_product_variant_details`.`order_id` = ".$value['order_id']." AND `order_product_variant_details`.`product_id` = ".$value['product_id']." AND order_product_variant_details.status != 1";

								$cart_var_data = $this->Common->custom_query($cart_var_query,'get');
								// echo $this->db->last_query();
								// echo "<pre>";
								// print_r($cart_var_data);
								if(count($cart_var_data) > 0)
								{
									$order[$key]['product_detail'][$in_key]['variants'] = $cart_var_data;
								}else
								{
									$order[$key]['product_detail'][$in_key]['variants'] = array();
								}
							}
						}
					}
		            $data['status']	= 200;
		            $data['message'] = $this->lang->line('success'); 
		            $data['data'] = $order;
            	}else
            	{
            		$data['status']	= 201;
	                $data['message'] = $this->lang->line('no_data_found'); 
	                $data['data'] = array();
            	}            	
            }
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # This function is used to accept or Reject the order by the merchant
    public function accept_reject_ready_complete_order_post()
    {
    	try{
    		$tokenData = $this->verify_request();
			$order_id = !empty($_POST['order_id'])?$this->db->escape_str($_POST['order_id']):'';
    		$action = !empty($_POST['action'])?$this->db->escape_str($_POST['action']):''; # 1 : ACCEPT 2 : REJECT 3 : READY 
    		$order_type = !empty($_POST['order_type'])?$this->db->escape_str($_POST['order_type']):''; #  `rest_accept_types table` 1 : Order now 2 : self pickup_time_from 3 : Order for later 4 : Dine In

	    	if($tokenData === false)
			{
			    $status = parent::HTTP_UNAUTHORIZED;
			    $data['status']	 = $status;
			    $data['message'] = $this->lang->line('unauthorized_access');
			}else if($order_id ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('order_id_missing');
			    $data['data']		=array();
			}else if($action ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('action_missing');
			    $data['data']		=array();
			}else
            {
            	if($action == 1) # ACCEPT i.e. send to preparing
            	{
            		$pickup_time_from = !empty($_POST['pickup_time_from'])?$this->db->escape_str($_POST['pickup_time_from']):''; # Timestamp
					$pickup_time_to = !empty($_POST['pickup_time_to'])?$this->db->escape_str($_POST['pickup_time_to']):''; # Timestamp
					$preparation_time = !empty($_POST['preparation_time'])?$this->db->escape_str($_POST['preparation_time']):''; # Pass in minutes like for 1hr pass 60 for 1 hr 20 mins pass 80
					if($pickup_time_from == ''){
					    $data['status']		=201;
					    $data['message']	=$this->lang->line('pickup_time_from_missing');
					    $data['data']		=array();
					}else if($pickup_time_to == ''){
					    $data['status']		=201;
					    $data['message']	=$this->lang->line('pickup_time_to_missing');
					    $data['data']		=array();
					}else if($preparation_time == ''){
					    $data['status']		=201;
					    $data['message']	=$this->lang->line('preparation_time_missing');
					    $data['data']		=array();
					}else if($order_type ==''){
					    $data['status']		=201;
					    $data['message']	=$this->lang->line('order_type_missing');
					    $data['data']		=array();
					}else
					{
						# First get user id of the user who ordered this order
		            	$customer_user_id = $this->Common->getData('orders','user_id,order_number','id = "'.$order_id.'"');
		            	if(count($customer_user_id) > 0)
		            	{
			            	$cust_user_id = $customer_user_id[0]['user_id'];
			            	$order_number = $customer_user_id[0]['order_number'];

			            	if($order_type == 1) # That means it is order now so we will send from pending to preparing directly (i.e. 0 TO 6 ) else we will send to accepted state only (i.e. 0 to 1)
			            	{
			            		$order_status_db = 6;
			            	}else
			            	{
			            		$order_status_db = 1;
			            	}

			            	# Get current preparation time for preparation_time_when_accepted

			            	$update_array=[
			            	 	'order_status' => $order_status_db, # 6 Preparing and 1 Accepted only
			            		'updated_at' => time(),
			            		'pickup_time_from' => $pickup_time_from,
	            				'pickup_time_to' => $pickup_time_to,
	            				'preparation_time_when_accepted' => $preparation_time,
							];
							$this->Common->updateData('orders',$update_array , 'id = "'.$order_id.'"');

							# Send notification code start
			        		$token = $this->Common->getData('users','device_token','id='.$cust_user_id);
			        		$token = $token[0]['device_token'];

			        		$notification_data_fields = array(
					            'message' => "Order ".$order_number." ".$this->lang->line('order_accepted'),
					            'title' => NOTIFICATION_TITLE,
					            'order_id' => $order_id,
					            'notification_type' => 'ORDER_STATUS_UPDATED'
					        );

			        		# We have seperate bundle ids for customer and merchant. So if push needs to be sent to customer then use IOS_BUNDLE_ID_CUSTOMER constant

			                if($token != "")
			                {
			                  sendPushNotification($token, $notification_data_fields,IOS_BUNDLE_ID_CUSTOMER);
			                }
			                # send notification code end
			                # Now insert notification to Database
			                $insertData = [
			                	'title' => "Order ".$order_number." ".$this->lang->line('order_accepted'),
			                	'to_user_id' => $cust_user_id,
			                	'type' => 1,
			                	'order_id' => $order_id,
			                	'is_read' => 1,
			                	'created_at' => time(),
			                	'updated_at' => time(),
			                ];
			                $this->Common->insertData('notifications',$insertData);

			                # Get restaurant Id of this order
			                $rest_id = $this->Common->getData('orders','restaurant_id','id = "'.$order_id.'"');
			                $restaurant_id = $rest_id[0]['restaurant_id'];
			                $delivery_handled_by = $this->Common->getData('restaurants','delivery_handled_by','id = "'.$restaurant_id.'"');
							$delivery_handled_by = $delivery_handled_by[0]['delivery_handled_by'];


			                # CHECK FOR LALAMOVE BOOKING also
			                # LALMOVE CHECK START
			                if($order_type != 2 && $delivery_handled_by == 2)
			                {
			                	$user_id = $cust_user_id;
			                	$order_data = $this->Common->getData('orders','*','id = "'.$order_id.'"');

			                	$lalamove_rest_details = $this->Common->getData('restaurants', 'restaurants.rest_pin_address,restaurants.rest_unit_number,restaurants.rest_postal_code,restaurants.rest_name AS rest_name,users.mobile,users.latitude,users.longitude', 'restaurants.id = "' . $restaurant_id . '"', array('users'), array('users.id = restaurants.admin_id'));

			                	$req_phone = $lalamove_rest_details[0]['mobile'];
		                        $req_phone = '+65' . $req_phone;
		                        $to_stop_phone = $this->Common->getData('users', 'mobile', 'id = "' . $user_id . '"');
		                        $to_stop_phone = $to_stop_phone[0]['mobile'];
		                        $to_stop_phone = '+65' . $to_stop_phone;
		                        # Get dropping information
		                        $delivery_latitude = $order_data[0]['delivery_latitude'];
		                        $delivery_longitude = $order_data[0]['delivery_longitude'];
		                        $delivery_address = $order_data[0]['delivery_address'];
		                        $unit_number = $order_data[0]['delivery_unit_number'];
		                        $postal_code = $order_data[0]['delivery_postal_code'];
		                        $delivery_name = $order_data[0]['delivery_name'];
		                        $sr_order_number = $order_data[0]['order_number'];

		                        if ($order_type == 1) # Order now so dont pass scheduleAt
		                        {
		                            $lalamove_support_number = LALAMOVE_SUPPORT_NUMBER;
		                            $body = array(
		                            "serviceType" => "MOTORCYCLE", 
		                            "specialRequests" => array(), 
		                            "requesterContact" => array("phone" => $req_phone,
		                            "name" => $lalamove_rest_details[0]['rest_name'],), "stops" => array(array("location" => array("lat" => $lalamove_rest_details[0]['latitude'], "lng" => $lalamove_rest_details[0]['longitude']), "addresses" => array("en_SG" => array("displayString" => $lalamove_rest_details[0]['rest_pin_address'] . ", " . $lalamove_rest_details[0]['rest_unit_number'] . ", " . $lalamove_rest_details[0]['rest_postal_code'], "country" => "SG"))), array("location" => array("lat" => $delivery_latitude, "lng" => $delivery_longitude), "addresses" => array("en_SG" => array("displayString" => $delivery_address . ", " . $unit_number . ", " . $postal_code, "country" => "SG")))), 
		                            "deliveries" => array(array("toStop" => 1, "toContact" => array("phone" => $to_stop_phone, 
		                            "name" => $delivery_name,), "remarks" => "1. Kerala Eats Food Order ID: [" . $sr_order_number . "] \n2. Customer Name: " . $delivery_name . " \n3. Support Number: " . $lalamove_support_number . "\n4. Tips pay by Kerala Eats")));
		                            
		                        } else # 3 : Order for later
		                        {
		                            $lalamove_support_number = LALAMOVE_SUPPORT_NUMBER;
		                            
		                            $less_15_mint = $pickup_time_from - 900; //we need to less 15 mint
		                            $start = new DateTime(date('r', $less_15_mint));
		                            $start = $start->format('Y-m-d\TH:i:s\Z');
		                            $final_pickup_time = $start;
		                            $body = array("scheduleAt" => $final_pickup_time, 
		                            "serviceType" => "MOTORCYCLE", 
		                            "specialRequests" => array(), 
		                            "requesterContact" => array("phone" => $req_phone, 
		                            "name" => $lalamove_rest_details[0]['rest_name'],), "stops" => array(array("location" => array("lat" => $lalamove_rest_details[0]['latitude'], "lng" => $lalamove_rest_details[0]['longitude']), "addresses" => array("en_SG" => array("displayString" => $lalamove_rest_details[0]['rest_pin_address'] . ", " . $lalamove_rest_details[0]['rest_unit_number'] . ", " . $lalamove_rest_details[0]['rest_postal_code'], "country" => "SG"
		                            
		                            ))), array("location" => array("lat" => $delivery_latitude, "lng" => $delivery_longitude), "addresses" => array("en_SG" => array("displayString" => $delivery_address . ", " . $unit_number . ", " . $postal_code, "country" => "SG"
		                            )))), "deliveries" => array(array("toStop" => 1, "toContact" => array("phone" => $to_stop_phone, 
		                            "name" => $delivery_name,), "remarks" => "1. Kerala Eats Food Order ID: " . $sr_order_number . " \n2. Customer Name: " . $delivery_name . " \n3. Support Number: " . $lalamove_support_number . "\n4. Tips pay by Kerala Eats")));
		                        }
		                        $lalamove_order_response = $this->lalamove_quotation_place_order($body);

		                        if ($lalamove_order_response['failed_reason'] == '') 
		                        {
		                            // echo "qqqqqqqqq";
		                            # That means order placed successfully as it has nothing in failed
		                            $track_link = $lalamove_order_response['lalamove_track_link'];
		                            $lalamove_order_id = $lalamove_order_response['lalamove_order_id'];;
		                            $lalamove_order_amount = $lalamove_order_response['lalamove_order_amount'];;
		                            $lalamove_order_status = 1; #  Success
		                            $lalamove_order_failed_reason = '';
		                            $response['lalamove_order_reference_id'] = $lalamove_order_id;
		                            $response['lalamove_order_failed_reason'] = $lalamove_order_failed_reason;
		                            $response['lalamove_order_status'] = $lalamove_order_status;
		                            $response['lalamove_order_amount'] = $lalamove_order_amount;
		                            $response['lalamove_track_link'] = $track_link;
		                        } else {
		                            // echo "WWWWWWWWW";
		                            $lalamove_order_status = 2; #  Fail
		                            $lalamove_order_id = '';
		                            $lalamove_order_failed_reason = $lalamove_order_response['failed_reason'];
		                            $response['lalamove_order_reference_id'] = $lalamove_order_id;
		                            $response['lalamove_order_failed_reason'] = $lalamove_order_failed_reason;
		                            $response['lalamove_order_status'] = $lalamove_order_status;
		                            $response['lalamove_order_amount'] = '';
		                            $response['lalamove_track_link'] = '';
		                        }

			                } else {
		                        // echo "EEEEEEEEE";
		                        $track_link = '';
		                        $lalamove_order_id = '';
		                        $lalamove_order_status = 3; # Not for lalamove
		                        $lalamove_order_failed_reason = '';
		                        $response['lalamove_order_reference_id'] = $lalamove_order_id;
		                        $response['lalamove_order_failed_reason'] = $lalamove_order_failed_reason;
		                        $response['lalamove_order_status'] = $lalamove_order_status;
		                        $response['lalamove_order_amount'] = '';
		                        $response['lalamove_track_link'] = '';
		                    }

		                    # And then update order table as per the response retuned from Lalamove
		                    $update_array = ['lalamove_order_id' => $lalamove_order_id, 'track_link' => $track_link, 'lalamove_order_status' => $lalamove_order_status, 'lalamove_order_failed_reason' => $lalamove_order_failed_reason, ];
		                    $this->Common->updateData('orders', $update_array, 'id = "' . $order_id . '"');
		                    # LALMOVE CHECK END

							$data['status']	= 200;
			                $data['message'] = $this->lang->line('order_udpate_success'); 
			                $data['data'] = array();
		            	}else
		            	{
		            		$data['status']	= 201;
			                $data['message'] = $this->lang->line('wrong_order_id'); 
			                $data['data'] = array();
		            	}
					}
            	}
            	else if($action == 2) # REJECT
            	{
            		# First get user id of the user who ordered this order
	            	$customer_user_id = $this->Common->getData('orders','user_id,order_number,total_amount,payment_mode','id = "'.$order_id.'"');
	            	if(count($customer_user_id) > 0)
	            	{
		            	$cust_user_id = $customer_user_id[0]['user_id'];
		            	$order_number = $customer_user_id[0]['order_number'];

		            	$update_array=[
		            		'order_status' => 2,
		            		'updated_at' => time()
						];
						$this->Common->updateData('orders',$update_array , 'id = "'.$order_id.'"');

						# Send notification code start
		        		$token = $this->Common->getData('users','device_token','id='.$cust_user_id);
		        		$token = $token[0]['device_token'];

		        		$notification_data_fields = array(
				            'message' => "Order ".$order_number." ".$this->lang->line('order_rejected'),
				            'title' => NOTIFICATION_TITLE,
				            'order_id' => $order_id,
				            'notification_type' => 'ORDER_STATUS_UPDATED'
				        );

		        		# We have seperate bundle ids for customer and merchant. So if push needs to be sent to customer then use IOS_BUNDLE_ID_CUSTOMER constant

		                if($token != "")
		                {
		                  sendPushNotification($token, $notification_data_fields,IOS_BUNDLE_ID_CUSTOMER);
		                }
		                # send notification code end
		                # Now insert notification to Database
		                $insertData = [
		                	'title' => "Order ".$order_number." ".$this->lang->line('order_rejected'),
		                	'to_user_id' => $cust_user_id,
		                	'type' => 1,
		                	'order_id' => $order_id,
		                	'is_read' => 1,
		                	'created_at' => time(),
		                	'updated_at' => time(),
		                ];
		                $this->Common->insertData('notifications',$insertData);


		                // # After reject we need to send money to customer's wallet : NOT NEEDED NOW "when we Reject the order, don't need to credit the amount back to the customer's wallet. Only when we cancel the orders. Please help to change back and apologies for the trouble. "
		                // $amount = $customer_user_id[0]['total_amount'];
		                // $payment_mode = $customer_user_id[0]['payment_mode'];
		                // $insert_wallet_table = [
		                //     'user_id' => $customer_user_id,
		                //     'order_id' => $order_id,
		                //     'credited' => $amount, 
		                //     'type' => 2, //1 - Cashback 2 - Money Added 3 debited
		                //     'added_by' => 1, //1 - By Admin 2 - By Customer
		                //     'wallet_date' => time(),
		                //     'created_at' => time(),
		                //     'updated_at' => time()
		                // ];
		                    
		                // $this->Common->insertdata('wallet', $insert_wallet_table);

		                if ($payment_mode == 2) # HITPAY (Order made using hitpay but did not pay through hitpay and now order is cancelled by admin so we need to remove the outstanding amount for this order id)
	                    {
	                        # HITPAY_CHANGE
	                        # If selected payment mode is hitpay then we need to check whether any outstanding amount on this cancelled order is avaialable? if yes then remove that outstanding amount
	                        $update_array = ['outstanding_amount' => '0.00', 'who_will_pay_outstanding_amount' => 0, 'is_paid_outstanding_amount' => 1];
	                        $this->Common->updateData('orders', $update_array, 'id = "' . $order_id . '"');
	                    }

						$data['status']	= 200;
		                $data['message'] = $this->lang->line('order_udpate_success'); 
		                $data['data'] = array();
	            	}else
	            	{
	            		$data['status']	= 201;
		                $data['message'] = $this->lang->line('wrong_order_id'); 
		                $data['data'] = array();
	            	}
            	}
            	else if($action == 3) # READY
            	{
            		# First get user id of the user who ordered this order
	            	$customer_user_id = $this->Common->getData('orders','user_id,order_number','id = "'.$order_id.'"');
	            	if(count($customer_user_id) > 0)
	            	{
		            	$cust_user_id = $customer_user_id[0]['user_id'];
		            	$order_number = $customer_user_id[0]['order_number'];

		            	$update_array=[
		            		'order_status' => 7, # Ready
		            		'updated_at' => time()
						];
						$this->Common->updateData('orders',$update_array , 'id = "'.$order_id.'"');

						# Send notification code start
		        		$token = $this->Common->getData('users','device_token','id='.$cust_user_id);
		        		$token = $token[0]['device_token'];

		        		$notification_data_fields = array(
				            'message' => "Order ".$order_number." ".$this->lang->line('order_is_ready'),
				            'title' => NOTIFICATION_TITLE,
				            'order_id' => $order_id,
				            'notification_type' => 'ORDER_STATUS_UPDATED'
				        );

		        		# We have seperate bundle ids for customer and merchant. So if push needs to be sent to customer then use IOS_BUNDLE_ID_CUSTOMER constant

		                if($token != "")
		                {
		                  sendPushNotification($token, $notification_data_fields,IOS_BUNDLE_ID_CUSTOMER);
		                }
		                # send notification code end
		                # Now insert notification to Database
		                $insertData = [
		                	'title' => "Order ".$order_number." ".$this->lang->line('order_is_ready'),
		                	'to_user_id' => $cust_user_id,
		                	'type' => 1,
		                	'order_id' => $order_id,
		                	'is_read' => 1,
		                	'created_at' => time(),
		                	'updated_at' => time(),
		                ];
		                $this->Common->insertData('notifications',$insertData);
						$data['status']	= 200;
		                $data['message'] = $this->lang->line('order_udpate_success'); 
		                $data['data'] = array();
	            	}else
	            	{
	            		$data['status']	= 201;
		                $data['message'] = $this->lang->line('wrong_order_id'); 
		                $data['data'] = array();
	            	}
            	}
            	else if($action == 4) # Completed 5 - Completed 
            	{
            		# First get user id of the user who ordered this order
	            	$customer_user_id = $this->Common->getData('orders','user_id,order_number','id = "'.$order_id.'"');
	            	if(count($customer_user_id) > 0)
	            	{
		            	$cust_user_id = $customer_user_id[0]['user_id'];
		            	$order_number = $customer_user_id[0]['order_number'];

		            	$update_array=[
		            		'order_status' => 3, # Completed means dispatched
		            		'updated_at' => time()
						];
						$this->Common->updateData('orders',$update_array , 'id = "'.$order_id.'"');

						# Send notification code start
		        		$token = $this->Common->getData('users','device_token','id='.$cust_user_id);
		        		$token = $token[0]['device_token'];

		        		$notification_data_fields = array(
				            'message' => "Order ".$order_number." ".$this->lang->line('order_completed'),
				            'title' => NOTIFICATION_TITLE,
				            'order_id' => $order_id,
				            'notification_type' => 'ORDER_STATUS_UPDATED'
				        );

		        		# We have seperate bundle ids for customer and merchant. So if push needs to be sent to customer then use IOS_BUNDLE_ID_CUSTOMER constant

		                if($token != "")
		                {
		                  sendPushNotification($token, $notification_data_fields,IOS_BUNDLE_ID_CUSTOMER);
		                }
		                # send notification code end
		                # Now insert notification to Database
		                $insertData = [
		                	'title' => "Order ".$order_number." ".$this->lang->line('order_completed'),
		                	'to_user_id' => $cust_user_id,
		                	'type' => 1,
		                	'order_id' => $order_id,
		                	'is_read' => 1,
		                	'created_at' => time(),
		                	'updated_at' => time(),
		                ];
		                $this->Common->insertData('notifications',$insertData);
						$data['status']	= 200;
		                $data['message'] = $this->lang->line('order_udpate_success'); 
		                $data['data'] = array();
	            	}else
	            	{
	            		$data['status']	= 201;
		                $data['message'] = $this->lang->line('wrong_order_id'); 
		                $data['data'] = array();
	            	}
            	}
            	else if(($action == 5 && $order_type == 3) || ($action == 5 && $order_type == 2))  # Action $action = 5 means send to preparing (order status 6 and action is 5 and order type = 3 OR order_type is self pickup) for order for later because order for later will  first come to accepeted and then they will go to preparing when merchant clicks the action
            	{
            		# First get user id of the user who ordered this order
	            	$customer_user_id = $this->Common->getData('orders','user_id,order_number','id = "'.$order_id.'"');
	            	if(count($customer_user_id) > 0)
	            	{
		            	$cust_user_id = $customer_user_id[0]['user_id'];
		            	$order_number = $customer_user_id[0]['order_number'];

		            	$update_array=[
		            		'order_status' => 6, # Preparing
		            		'updated_at' => time()
						];
						$this->Common->updateData('orders',$update_array , 'id = "'.$order_id.'"');

						# Send notification code start
		        		$token = $this->Common->getData('users','device_token','id='.$cust_user_id);
		        		$token = $token[0]['device_token'];

		        		$notification_data_fields = array(
				            'message' => "Order ".$order_number." ".$this->lang->line('order_completed'),
				            'title' => NOTIFICATION_TITLE,
				            'order_id' => $order_id,
				            'notification_type' => 'ORDER_STATUS_UPDATED'
				        );

		        		# We have seperate bundle ids for customer and merchant. So if push needs to be sent to customer then use IOS_BUNDLE_ID_CUSTOMER constant

		                if($token != "")
		                {
		                  sendPushNotification($token, $notification_data_fields,IOS_BUNDLE_ID_CUSTOMER);
		                }
		                # send notification code end
		                # Now insert notification to Database
		                $insertData = [
		                	'title' => "Order ".$order_number." ".$this->lang->line('order_completed'),
		                	'to_user_id' => $cust_user_id,
		                	'type' => 1,
		                	'order_id' => $order_id,
		                	'is_read' => 1,
		                	'created_at' => time(),
		                	'updated_at' => time(),
		                ];
		                $this->Common->insertData('notifications',$insertData);
						$data['status']	= 200;
		                $data['message'] = $this->lang->line('order_udpate_success'); 
		                $data['data'] = array();
	            	}else
	            	{
	            		$data['status']	= 201;
		                $data['message'] = $this->lang->line('wrong_order_id'); 
		                $data['data'] = array();
	            	}
            	}
            }
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {
            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

	# This function is used to filter completed orders based on dates
	public function filter_completed_orders_post()
	{
		try{
	    	$tokenData = $this->verify_request();
	    	$from_date = !empty($_POST['from_date'])?$this->db->escape_str($_POST['from_date']):'';
	    	$to_date = !empty($_POST['to_date'])?$this->db->escape_str($_POST['to_date']):'';

	    	$page = !empty($_POST['page'])?$this->db->escape_str($_POST['page']):0;
 			$limit = !empty($_POST['limit'])?$this->db->escape_str($_POST['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
            
            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($from_date == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('from_date_missing');
                $data['data']		=array();
			}else if($to_date == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('to_date_missing');
                $data['data']		=array();
			}else
            {
            	$rest_id = $this->get_restaurant_id($tokenData->id);
            	# 5 - Completed
            	$order = $this->Common->getData('orders','*','restaurant_id = "'.$rest_id.'" AND order_status = 5 AND (created_at >= "'.$from_date.'" AND created_at <= "'.$to_date.'")','','','','',$limit,$page);

            	if(count($order) > 0)
            	{
	                $data['status']	= 200;
	                $data['message'] = $this->lang->line('success'); 
	                $data['data'] = $order;
            	}else
            	{
            		$data['status']	= 201;
	                $data['message'] = $this->lang->line('no_data_found'); 
	                $data['data'] = array();
            	}            	
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# This function get business screen detail for the merchant
	public function merchant_business_screen_get()
	{
		try{
	    	$tokenData = $this->verify_request();

	    	$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
            
            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else
            {
            	$rest_id = $this->get_restaurant_id($tokenData->id);
            	$restaurant = $this->Common->getData('restaurants','restaurants.id,restaurants.rest_name,restaurants.res_description','id = "'.$rest_id.'"');
            	$response['restaurant_detail'] = $restaurant[0];
            	# Total Earnings only for today 10 AM to 5 PM
            	# Get data of order EXCEPT REJETED ORDERS
            	$date = new DateTime(); # This will return today's date 12.00 AM timstamp
				$date->setTime(10,0,0); # Adding 10 hours to make it 10 AM
				$from = $date->getTimestamp(); # This will give you final value

				# Now add 7 hours more to $from to make it 5 pm
				$date = new DateTime();
				$date->setTime(17,0,0);
				$to = $date->getTimestamp();

				# We will return this value because on tab change we need it
            	$response['timestamp_from'] = $from;
            	$response['timestamp_to'] = $to;
				# Now we have from as todays date 10 am and todate as todays date 5pm
				# Get orders (not only completed ,  EXCEPT REJETED ORDERS (order_status 2))
				# Total earning will NOT include commision amount hence we are getting data using maths formula
				# Ex :dc+sub = 145 and admin_commission is 2 so 145*2/100 = 2.9 so 145-2.9 = 142.1 is the earning to merchant and this is total earning

				# TOTAL EARNING
            	$query = "SELECT count(`id`) as total_order , SUM(`dc_amount`+`sub_total`) - SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_earning FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id." AND created_at BETWEEN ".$from." AND ".$to;

            	$earning = $this->Common->custom_query($query,'get');
            	$earnings = $earning[0]['total_earning'];
            	$order_count = $earning[0]['total_order'];
            	$response['total_earning']['total_earnings_amount'] = number_format($earnings,2, '.', '');
            	$response['total_earning']['total_earnings_order_count'] = $order_count;
            	
            	$all_order_query = "SELECT id,order_number,created_at,dc_amount,sub_total,dc_amount+sub_total as amount , is_paid_to_restaurant AS is_paid_to_merchant FROM `orders` WHERE restaurant_id = ".$rest_id." AND order_status != 2 AND created_at BETWEEN ".$from." AND ".$to." LIMIT ".$page.','.$limit;
            	$all_order = $this->Common->custom_query($all_order_query,'get');
            	$response['total_earning']['total_earnings_order_detail'] = $all_order;

            	# GROSS SALE
            	$query = "SELECT count(`id`) as gs_total_order , SUM(`dc_amount`+`sub_total`) as gross_sale FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id." AND created_at BETWEEN ".$from." AND ".$to;
            	$gross_sale = $this->Common->custom_query($query,'get');

            	$gr_sale = $gross_sale[0]['gross_sale'];
            	$gs_order_count = $gross_sale[0]['gs_total_order'];
            	$response['gross_sale']['gross_sale_amount'] = number_format($gr_sale,2, '.', '');
            	$response['gross_sale']['gross_sale_order_count'] = $gs_order_count;

            	# COMMISSION PAID (That means how much money merchant has paid to kerala eats in terms of commission)
            	$query = "SELECT count(`id`) as com_total_order , SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as com_paid FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id." AND created_at BETWEEN ".$from." AND ".$to;
            	$commission_paid = $this->Common->custom_query($query,'get');
            	$com_paid = $commission_paid[0]['com_paid'];
            	$com_paid_order_count = $commission_paid[0]['com_total_order'];
            	
            	$response['commission_paid']['commission_paid_amount'] = number_format($com_paid,2, '.', '');
            	$response['commission_paid']['commission_paid_order_count'] = $com_paid_order_count;

            	# gross_sale_amount - commission_paid_amount MUST BE EQUAL TO total_earnings_amount

            	# TOTAL SALES ( till date)
            	$query = "SELECT count(`id`) as total_order , SUM(`dc_amount`+`sub_total`) - SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_sales FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id;

            	$sales = $this->Common->custom_query($query,'get');
            	$t_sales = $sales[0]['total_sales'];
            	$order_count = $sales[0]['total_order'];
            	$response['total_sales_till_date']['total_sales_amount'] = number_format($t_sales,2, '.', '');
            	$response['total_sales_till_date']['total_sales_order_count'] = $order_count;
            	
            	# LAST MONTH SALES ( We will display gross value (including commission))
            	$first_day_of_previous_month = strtotime("midnight first day of previous month"); # Ex : April 1, 2021 12:00:00 AM
            	$last_day_of_previous_month = strtotime("midnight first day of this month"); # EX : May 1, 2021 12:00:00 AM

            	$query = "SELECT count(`id`) as total_order , SUM(`dc_amount`+`sub_total`) as total_sales FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id." AND created_at BETWEEN ".$first_day_of_previous_month." AND ".$last_day_of_previous_month;
            	$last_month_sales = $this->Common->custom_query($query,'get');

            	$t_sales = $last_month_sales[0]['total_sales'];
            	$order_count = $last_month_sales[0]['total_order'];
            	$response['last_month_sales']['last_month_sales_amount'] = number_format($t_sales,2, '.', '');
            	$response['last_month_sales']['last_month_sales_order_count'] = $order_count;

            	# LAST WEEK PAID (i.e. How much money merchant paid to the kerala eats in terms of commission in last 7 days)
            	$last_week_from = strtotime("midnight -7 days");
            	$last_week_to = time();
            	$query = "SELECT count(`id`) as com_total_order , SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as com_paid FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id." AND created_at BETWEEN ".$last_week_from." AND ".$last_week_to;
            	$last_week_commission_paid = $this->Common->custom_query($query,'get');
            	$com_paid = $last_week_commission_paid[0]['com_paid'];
            	$com_paid_order_count = $last_week_commission_paid[0]['com_total_order'];
            	
            	$response['last_week_commission_paid']['commission_paid_amount'] = number_format($com_paid,2, '.', '');
            	$response['last_week_commission_paid']['commission_paid_order_count'] = $com_paid_order_count;
            	

            	$data['status']	= 200;
                $data['message'] = $this->lang->line('success'); 
                $data['data'] = $response;
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# This function is used to get tab wise data on business screen
	public function business_screen_order_detail_by_tab_get()
	{
		try{
    		$tokenData = $this->verify_request();

    		$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
    		$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;
    		$selected_tab = !empty($_GET['selected_tab'])?$this->db->escape_str($_GET['selected_tab']):''; # 1 Gross sales 2 commission paid
    		$from = !empty($_GET['from'])?$this->db->escape_str($_GET['from']):''; # This both are retunred in above api
    		$to = !empty($_GET['to'])?$this->db->escape_str($_GET['to']):'';
	    	
	    	if($tokenData === false)
	    	{
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message'] = $this->lang->line('unauthorized_access');
            }else if($selected_tab == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('selected_tab_missing');
                $data['data']		=array();
            }else if($from == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('from_missing');
                $data['data']		=array();
            }else if($to == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('to_missing');
                $data['data']		=array();
            }else
            {
            	$rest_id = $this->get_restaurant_id($tokenData->id);
            	if($selected_tab == 1) # GROSS
            	{
	            	$all_order_query = "SELECT count(`id`) as gs_total_order , SUM(`dc_amount`+`sub_total`) as gross_sale FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id." AND created_at BETWEEN ".$from." AND ".$to." LIMIT ".$page.','.$limit;
	            	$all_order = $this->Common->custom_query($all_order_query,'get');
	            	$response['order_detail'] = $all_order;
            	}else if($selected_tab == 2) # commission paid
            	{
            		$query = "SELECT count(`id`) as com_total_order , SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as com_paid FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id." AND created_at BETWEEN ".$from." AND ".$to." LIMIT ".$page.','.$limit;
            		$commission_paid = $this->Common->custom_query($query,'get');
            		$response['order_detail'] = $commission_paid;
            	}

            	if(count($response['order_detail']) > 0)
            	{
            		$data['status']	= 200;
	                $data['message'] = $this->lang->line('success'); 
	                $data['data'] = $response;
            	}else
            	{
            		$data['status']	= 201;
	                $data['message'] = $this->lang->line('no_data_found'); 
	                $data['data'] = array();
            	}
            }
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}
   
	# This function is used to filter the sales on business screen
	public function filter_business_sale_get()
	{
		try{
	    	$tokenData = $this->verify_request();

	    	$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
			$from = !empty($_GET['from'])?$this->db->escape_str($_GET['from']):''; # This both are retunred in above api
    		$to = !empty($_GET['to'])?$this->db->escape_str($_GET['to']):'';
            
            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($from == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('from_missing');
                $data['data']		=array();
            }else if($to == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('to_missing');
                $data['data']		=array();
            }else
            {
            	$rest_id = $this->get_restaurant_id($tokenData->id);
            	$restaurant = $this->Common->getData('restaurants','restaurants.id,restaurants.rest_name,restaurants.res_description','id = "'.$rest_id.'"');
            	$response['restaurant_detail'] = $restaurant[0];
            	# Total Earnings for selected dates
            	# Get data of order EXCEPT REJETED ORDERS

				# We will return this value because on tab change we need it
            	$response['timestamp_from'] = $from;
            	$response['timestamp_to'] = $to;
				# Now we have from as todays date 10 am and todate as todays date 5pm
				# Get orders (not only completed ,  EXCEPT REJETED ORDERS (order_status 2))
				# Total earning will NOT include commision amount hence we are getting data using maths formula
				# Ex :dc+sub = 145 and admin_commission is 2 so 145*2/100 = 2.9 so 145-2.9 = 142.1 is the earning to merchant and this is total earning

				# TOTAL EARNING
            	$query = "SELECT count(`id`) as total_order , SUM(`dc_amount`+`sub_total`) - SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_earning FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id." AND created_at BETWEEN ".$from." AND ".$to;

            	$earning = $this->Common->custom_query($query,'get');
            	$earnings = $earning[0]['total_earning'];
            	$order_count = $earning[0]['total_order'];
            	$response['total_earning']['total_earnings_amount'] = number_format($earnings,2, '.', '');
            	$response['total_earning']['total_earnings_order_count'] = $order_count;
            	
            	$all_order_query = "SELECT id,order_number,created_at,dc_amount,sub_total,dc_amount+sub_total as amount , is_paid_to_restaurant AS is_paid_to_merchant FROM `orders` WHERE restaurant_id = ".$rest_id." AND order_status != 2 AND created_at BETWEEN ".$from." AND ".$to." LIMIT ".$page.','.$limit;
            	$all_order = $this->Common->custom_query($all_order_query,'get');
            	$response['total_earning']['total_earnings_order_detail'] = $all_order;

            	# GROSS SALE
            	$query = "SELECT count(`id`) as gs_total_order , SUM(`dc_amount`+`sub_total`) as gross_sale FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id." AND created_at BETWEEN ".$from." AND ".$to;
            	$gross_sale = $this->Common->custom_query($query,'get');

            	$gr_sale = $gross_sale[0]['gross_sale'];
            	$gs_order_count = $gross_sale[0]['gs_total_order'];
            	$response['gross_sale']['gross_sale_amount'] = number_format($gr_sale,2, '.', '');
            	$response['gross_sale']['gross_sale_order_count'] = $gs_order_count;

            	# COMMISSION PAID (That means how much money merchant has paid to kerala eats in terms of commission)
            	$query = "SELECT count(`id`) as com_total_order , SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as com_paid FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id." AND created_at BETWEEN ".$from." AND ".$to;
            	$commission_paid = $this->Common->custom_query($query,'get');
            	$com_paid = $commission_paid[0]['com_paid'];
            	$com_paid_order_count = $commission_paid[0]['com_total_order'];
            	
            	$response['commission_paid']['commission_paid_amount'] = number_format($com_paid,2, '.', '');
            	$response['commission_paid']['commission_paid_order_count'] = $com_paid_order_count;

            	# gross_sale_amount - commission_paid_amount MUST BE EQUAL TO total_earnings_amount
            	
            	$data['status']	= 200;
                $data['message'] = $this->lang->line('success'); 
                $data['data'] = $response;
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# This function is used to get payment record data
	public function business_screen_payment_record_get()
	{
		try{
	    	$tokenData = $this->verify_request();

	    	$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
            
            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else
            {
            	$rest_id = $this->get_restaurant_id($tokenData->id);
            	$restaurant = $this->Common->getData('restaurants','restaurants.id,restaurants.rest_name,restaurants.res_description','id = "'.$rest_id.'"');
            	$response['restaurant_detail'] = $restaurant[0];
            	
            	# Total BALANCE till date
            	
				# Get orders (not only completed , EXCEPT REJETED ORDERS (order_status 2))
				# Total balance will NOT include commision amount hence we are getting data using maths formula
				# Ex :dc+sub = 145 and admin_commission is 2 so 145*2/100 = 2.9 so 145-2.9 = 142.1 is the earning to merchant and this is total earning

				# TOTAL Balance
            	$query = "SELECT count(`id`) as total_order , SUM(`dc_amount`+`sub_total`) - SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_earning FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id;

            	$earning = $this->Common->custom_query($query,'get');
            	$earnings = $earning[0]['total_earning'];
            	$response['total_balance'] = number_format($earnings,2, '.', '');
            	
            	# PENDING AMOUNT
            	$query = "SELECT count(`id`) as total_order , SUM(`dc_amount`+`sub_total`) - SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_earning FROM `orders` WHERE order_status != 2 AND restaurant_id = ".$rest_id." AND is_paid_to_restaurant = 0";

            	$pen_amnt = $this->Common->custom_query($query,'get');
            	$pending_amount = $pen_amnt[0]['total_earning'];
            	$response['pending_amount'] = number_format($pending_amount,2, '.', '');

            	# Transaction (Means all orders till date includes commission)
            	$all_order_query = "SELECT id,order_number,created_at,dc_amount,sub_total,dc_amount+sub_total as amount , is_paid_to_restaurant AS is_paid_to_merchant FROM `orders` WHERE restaurant_id = ".$rest_id." AND order_status != 2 LIMIT ".$page.','.$limit;
            	$all_order = $this->Common->custom_query($all_order_query,'get');
            	$response['transaction'] = $all_order;

            	$data['status']	= 200;
                $data['message'] = $this->lang->line('success'); 
                $data['data'] = $response;
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}

	# This function is used to filter the payment_record transactions as per the selected date
	public function payment_record_filter_transaction_get()
	{
		try{
	    	$tokenData = $this->verify_request();

	    	$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
			$from = !empty($_GET['from'])?$this->db->escape_str($_GET['from']):''; # This both are retunred in above api
    		$to = !empty($_GET['to'])?$this->db->escape_str($_GET['to']):'';
            
            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($from == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('from_missing');
                $data['data']		=array();
            }else if($to == ''){
            	$data['status']		=201;
                $data['message']	=$this->lang->line('to_missing');
                $data['data']		=array();
            }else
            {
            	$rest_id = $this->get_restaurant_id($tokenData->id);

            	# Transaction (Means all orders till date includes commission as per the selected date filters)
            	$all_order_query = "SELECT id,order_number,created_at,dc_amount,sub_total,dc_amount+sub_total as amount , is_paid_to_restaurant AS is_paid_to_merchant FROM `orders` WHERE restaurant_id = ".$rest_id." AND order_status != 2 AND created_at BETWEEN ".$from." AND ".$to." LIMIT ".$page.','.$limit;
            	$all_order = $this->Common->custom_query($all_order_query,'get');
            	$response['transaction'] = $all_order;

            	$data['status']	= 200;
                $data['message'] = $this->lang->line('success'); 
                $data['data'] = $response;
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}
    // ----------------------------------------- MERCHANT APP API END ---------------------------------------------------------- //


    // ------------------------------------------------ COMMON FUNCTIONS ----------------------------------------------------------//
    # Commonly used functions START
    # get_social_urls method is used to get the footer icon href urls for mail function. Instead of writing same code again and again , we are writing this function and will just call it in single line
    public function get_social_urls()
    {
    	$get_social_urls = $this->Common->getData('settings','value','name IN("facebook" , "google" , "instagram" , "website")');
    	
    	$facebook = isset($get_social_urls[0]) ? $get_social_urls[0]['value'] : '';
    	$google = isset($get_social_urls[1]) ? $get_social_urls[1]['value'] : '';
    	$insta = isset($get_social_urls[2]) ? $get_social_urls[2]['value'] : '';
    	$website = isset($get_social_urls[3]) ? $get_social_urls[3]['value'] : '';

    	return array('facebook' => $facebook , 'google' => $google , 'insta' => $insta , 'website' => $website);

    }

    public function get_user_details($where = '')
    {
    	# DB_user_status 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
    	// $mendatory_where = '(status NOT IN (2,3,5) AND role = 3)';
    	$mendatory_where = '(status NOT IN (2,3,5))';

    	$q_where = '';
    	if($where != '')
    	{
    		$q_where .= $where;
    	}
    	if($q_where != '')
    	{
    		$final_where = $q_where.$mendatory_where;
    	}else
    	{
    		$final_where = $mendatory_where;
    	}
    	$query = "SELECT * FROM users WHERE $final_where";
    	return $detail = $this->Common->custom_query($query,'get');
    }

    # Below funtion is used to get restaurant id using token id. Because id in token is from user table.
    public function get_restaurant_id($admin_id)
    {
    	$rest_id = $this->Common->getData('restaurants','id','admin_id = "'.$admin_id.'"');
		return $rest_id[0]['id'];
    }

    # Below function is used to get values from setting table as per the key
    public function get_from_settings($key)
    {
    	$setting = $this->Common->getData('settings','value','name = "'.$key.'"');
    	return $setting[0]['value'];
    }
    # Commonly used functions END

    # delivery_preparation_time
    # This function is used to calculate delivery+preparation time as per set by admin in setting table
    public function delivery_preparation_time()
    {
    	$time = $this->Common->getData('settings','value','name = "basic_delivery_time" OR name = "basic_preparation_time"');
    	$basic_delivery_time = $time[0]['value'];
    	$basic_preparation_time = $time[1]['value'];
    	$time = $basic_preparation_time + $basic_delivery_time;
    	return $hours = floor($time / 60).'hr '.($time -   floor($time / 60) * 60).'min';
    }

    # This function is used to send the open close break start and break end time for the sent timestamp
    public function get_specific_open_close_break_time($date_timestamp,$rest_id)
    {
    	$weekday = date('l', $date_timestamp);
		$weekday = strtolower($weekday);
		$day_wise = $this->Common->getData('rest_time_daywise','*','rest_id = "'.$rest_id.'"');

		if($weekday == 'monday')
		{
			$open_close_time = $day_wise[0]['mon_open_close_time'];
			$break_start_end_time = $day_wise[0]['mon_break_start_end_time'];
		}elseif($weekday == 'tuesday')
		{
			$open_close_time = $day_wise[0]['mon_open_close_time'];
			$break_start_end_time = $day_wise[0]['mon_break_start_end_time'];
		}elseif($weekday == 'wednesday')
		{
			$open_close_time = $day_wise[0]['wed_open_close_time'];
			$break_start_end_time = $day_wise[0]['wed_break_start_end_time'];
		}elseif($weekday == 'thursday')
		{
			$open_close_time = $day_wise[0]['thu_open_close_time'];
			$break_start_end_time = $day_wise[0]['thu_break_start_end_time'];
		}elseif($weekday == 'friday')
		{
			$open_close_time = $day_wise[0]['fri_open_close_time'];
			$break_start_end_time = $day_wise[0]['fri_break_start_end_time'];
		}elseif($weekday == 'saturday')
		{
			$open_close_time = $day_wise[0]['sat_open_close_time'];
			$break_start_end_time = $day_wise[0]['sat_break_start_end_time'];
		}elseif($weekday == 'sunday')
		{
			$open_close_time = $day_wise[0]['sun_open_close_time'];
			$break_start_end_time = $day_wise[0]['sun_break_start_end_time'];
		}
			
		return $this->get_open_close_breakstart_breakend_timings($open_close_time,$break_start_end_time);
    }

    # This is common function that is used to get basic required data for a restaurant. $rest is a variable that already fetched data as per the needed where condition
    public function get_restaurant_data($rest,$hours,$tokenData,$lat = '',$lng = '',$date_timestamp = '')
    {
    	foreach ($rest as $key => $restaurant) 
		{
			$response[$key]['restaurant'] = $restaurant;
			$response[$key]['restaurant']['rest_name'] = stripslashes($restaurant['rest_name']);
			if($restaurant['time_mode'] == 2) # Specific Days
			{
				$specific = $this->get_specific_open_close_break_time($date_timestamp,$restaurant['restaurant_id']);
				// echo "<pre>";
				// print_r($specific);
				# return array('open_time' => $open_time , 'close_time' => $close_time , 'break_start_time' => $break_start_time , 'break_end_time' => $break_end_time);
				/*"open_time": "21:20",
	            "close_time": "23:58",
	            "break_start_time": "",
	            "break_end_time": "",*/
				$response[$key]['restaurant']['open_time'] = (string)$specific['open_time'];
				$response[$key]['restaurant']['close_time'] = (string)$specific['close_time'];
				$response[$key]['restaurant']['break_start_time'] = (string)$specific['break_start_time'];
				$response[$key]['restaurant']['break_end_time'] = (string)$specific['break_end_time'];
			}
			$time = $this->Common->getData('restaurants','delivery_time,preparation_time','id = "'.$restaurant['restaurant_id'].'"');
	    	$basic_delivery_time = $time[0]['delivery_time'];
	    	$basic_preparation_time = $time[0]['preparation_time'];
	    	$time = $basic_preparation_time + $basic_delivery_time;
	    	# We do not require hours as there is need to return delivery and preparation time as per the restaurant. But as this was used at many places so we did not change therer. We just overrided it.

	    	$hours = floor($time / 60).'hr '.($time -   floor($time / 60) * 60).'min';
			$response[$key]['del_prep_time'] = $hours;
			$response[$key]['basic_delivery_time'] = $basic_delivery_time;
			$response[$key]['basic_preparation_time'] = $basic_preparation_time;
			# Check whether any offer to any of the liked restaurant is given?
			// $promotions = $this->Common->getData('promotions','id,promo_type,discount_value,valid_from,valid_till',' promotion_mode_status = 1 AND promo_status = 1 AND (restaurant_id = "'.$restaurant['restaurant_id'].'") AND (valid_from < "'.time().'" AND valid_till > "'.time().'") AND level_id = 2'); # level id 2 is for restaurant
			$promotions = $this->Common->getData('promotions','id,promo_type,discount_value,valid_from,valid_till',' promotion_mode_status = 1 AND promo_status = 1 AND (restaurant_id = "'.$restaurant['restaurant_id'].'") AND level_id = 2'); # level id 2 is for restaurant
			if(count($promotions) > 0)
			{
				if($promotions[0]['valid_from'] != 0 && ($promotions[0]['valid_from'] < time() && $promotions[0]['valid_till'] > time()))
				{
					$response[$key]['promotions'] = $promotions[0];	
				}else if($promotions[0]['valid_from'] == 0)
				{
					$response[$key]['promotions'] = $promotions[0];	
				}else
				{
					$x = new stdClass();
					$response[$key]['promotions'] = $x;		
				}
			}else
			{
				$x = new stdClass();
				$response[$key]['promotions'] = $x;
			}
			# Check whether any offline entry for the listed restaurant is available?
			$offline_status = $this->Common->getData('rest_offline','rest_offline.offline_tag,rest_offline.offline_value,rest_offline.offline_from,rest_offline.offline_to','rest_offline.rest_id = "'.$restaurant['restaurant_id'].'"',array('restaurants'),array('restaurants.id = rest_offline.rest_id'));
			if(count($offline_status) > 0)
			{
				$response[$key]['offline_status'] = $offline_status[0];
			}else
			{
				$x = new stdClass();
				$response[$key]['offline_status'] = $x;
			}

			/* # DB_to_type = 1 - For food to restaurant 2 - For food to Kerala Eats 3 - For delivery to restaurant
			$avg_del_review = $this->Common->getData('ratings','AVG(given_rating) AS del_rating','to_id = "'.$restaurant['restaurant_id'].'"');
			$avg_del_review[0]['del_rating'];
			$response[$key]['delivery_rating'] = $avg_del_review;*/

			# Check Wishlist status
			if($tokenData == '')
			{
				$response[$key]['isWishList'] = 0;
			}else
			{
				$wishlist_check = $this->Common->getData('wishlist','id','restaurant_id = "'.$restaurant['restaurant_id'].'" AND user_id = "'.$tokenData->id.'"');
				if(count($wishlist_check) > 0)
				{
					$response[$key]['isWishList'] = 1;	
				}else
				{
					$response[$key]['isWishList'] = 0;
				}
			}

			if($lat != '' && $lng != '')
			{
				$rest_lat = $restaurant['latitude'];
				$rest_lng = $restaurant['longitude'];

				# Calculate Distance between user's latlong and restaurants latlong
				$response[$key]['distance'] = $this->calculate_distance_between_latlong($lat,$rest_lat,$lng,$rest_lng);
			}

			# We also need to check day wise open close timings of restaurant

			$restro_is_open = $this->get_restaurant_open_close_status($restaurant['restaurant_id'],$date_timestamp); # $restro_is_open 1 YES 2 CLOSED
			// echo "CALLINGFN <pre>";
			// print_r($restro_is_open);
			$response[$key]['restro_is_open'] = $restro_is_open['status'];
			$response[$key]['next_open_time'] = $restro_is_open['next_open_time'];

			# This is done because avg_rating from mobile app was not working so we need to display 5 rating to each restro so wea re sengin static value of rating
			# Static pass
			// $response[$key]['restaurant']['avg_rating'] = '5';

		}
		return $response;
    }

    # Get restaurant data with their product informations also with product offline status
    public function get_restaurant_data_with_products($rest,$hours,$tokenData,$lat = '',$lng = '',$sort_type_price,$page = '' , $limit = '',$date_timestamp = '')
    {
    	# Here we need to check whether this restraurant has any product If yes then only we will add it to response
    	$super_key = 0;
    	foreach ($rest as $anykey => $restaurant) 
		{
    		$has_products = $this->Common->getData('products','products.id','products.restaurant_id = "'.$restaurant['restaurant_id'].'" AND product_status = 1');
    		if(count($has_products) > 0)
    		{
    			$response[$super_key]['restaurant'] = $restaurant;
    			$response[$super_key]['restaurant']['rest_name'] = stripslashes($restaurant['rest_name']);
    			# This is done because avg_rating from mobile app was not working so we need to display 5 rating to each restro so wea re sengin static value of rating
				# Static pass
				// $response[$super_key]['restaurant']['avg_rating'] = '5';

				// $response[$super_key]['del_prep_time'] = $hours;
				$time = $this->Common->getData('restaurants','delivery_time,preparation_time','id = "'.$restaurant['restaurant_id'].'"');
		    	$basic_delivery_time = $time[0]['delivery_time'];
		    	$basic_preparation_time = $time[0]['preparation_time'];
		    	$time = $basic_preparation_time + $basic_delivery_time;
		    	# We do not require hours as there is need to return delivery and preparation time as per the restaurant. But as this was used at many places so we did not change therer. We just overrided it.

		    	$hours = floor($time / 60).'hr '.($time -   floor($time / 60) * 60).'min';
				$response[$super_key]['del_prep_time'] = $hours;
				$response[$super_key]['basic_delivery_time'] = $basic_delivery_time;
				$response[$super_key]['basic_preparation_time'] = $basic_preparation_time;
				# Check whether any offer to any of the liked restaurant is given?
				$promotions = $this->Common->getData('promotions','promo_type,discount_value,valid_from,valid_till',' promotion_mode_status = 1 AND promo_status = 1 AND (restaurant_id = "'.$restaurant['restaurant_id'].'") AND (valid_from < "'.time().'" AND valid_till > "'.time().'") AND level_id = 2'); # level id 2 is for restaurant
				if(count($promotions) > 0)
				{
					$response[$super_key]['promotions'] = $promotions[0];	
				}else
				{
					$x = new stdClass();
					$response[$super_key]['promotions'] = $x;
				}
				# Check whether any offline entry for the listed restaurant is available?
				$offline_status = $this->Common->getData('rest_offline','rest_offline.offline_tag,rest_offline.offline_value,rest_offline.offline_from,rest_offline.offline_to','rest_offline.rest_id = "'.$restaurant['restaurant_id'].'"',array('restaurants'),array('restaurants.id = rest_offline.rest_id'));
				if(count($offline_status) > 0)
				{
					$response[$super_key]['offline_status'] = $offline_status[0];
				}else
				{
					$x = new stdClass();
					$response[$super_key]['offline_status'] = $x;
				}

				/* # DB_to_type = 1 - For food to restaurant 2 - For food to Kerala Eats 3 - For delivery to restaurant
				$avg_del_review = $this->Common->getData('ratings','AVG(given_rating) AS del_rating','to_id = "'.$restaurant['restaurant_id'].'"');
				$avg_del_review[0]['del_rating'];
				$response[$key]['delivery_rating'] = $avg_del_review;*/

				# Check Wishlist status
				if($tokenData == '')
				{
					$response[$super_key]['isWishList'] = 0;
				}else
				{
					$wishlist_check = $this->Common->getData('wishlist','id','restaurant_id = "'.$restaurant['restaurant_id'].'" AND user_id = "'.$tokenData->id.'"');
					if(count($wishlist_check) > 0)
					{
						$response[$super_key]['isWishList'] = 1;	
					}else
					{
						$response[$super_key]['isWishList'] = 0;
					}
				}

				if($lat != '' && $lng != '')
				{
					$rest_lat = $restaurant['latitude'];
					$rest_lng = $restaurant['longitude'];

					# Calculate Distance between user's latlong and restaurants latlong
					$response[$super_key]['distance'] = $this->calculate_distance_between_latlong($lat,$rest_lat,$lng,$rest_lng);
				}

				# We also need to check day wise open close timings of restaurant

				$restro_is_open = $this->get_restaurant_open_close_status($restaurant['restaurant_id'],$date_timestamp); # $restro_is_open 1 YES 2 CLOSED
				// $response[$key]['restro_is_open'] = $restro_is_open;
				$response[$key]['restro_is_open'] = $restro_is_open['status'];
				$response[$key]['next_open_time'] = $restro_is_open['next_open_time'];

				if($sort_type_price == 1) # HIGH TO LOW i.e. DESC
				{
					$prod_query = "SELECT products.id AS product_id,products.product_name ,products.price,products.offer_price,products.product_image,products_offline.id AS off_id ,products_offline.rest_id , products_offline.category_id , products_offline.offline_tag,products_offline.offline_value,products_offline.offline_from,products_offline.offline_to FROM products LEFT OUTER JOIN products_offline ON products.id = products_offline.product_id WHERE products.restaurant_id = ".$restaurant['restaurant_id']." ORDER BY products.price DESC LIMIT ".$page.",".$limit;
				}else
				{
					$prod_query = "SELECT products.id AS product_id,products.product_name ,products.price,products.offer_price,products.product_image,products_offline.id AS off_id ,products_offline.rest_id , products_offline.category_id , products_offline.offline_tag,products_offline.offline_value,products_offline.offline_from,products_offline.offline_to FROM products LEFT OUTER JOIN products_offline ON products.id = products_offline.product_id WHERE products.restaurant_id = ".$restaurant['restaurant_id']." ORDER BY products.price ASC LIMIT ".$page.",".$limit;
				}
			            				
				$product_status[] = $this->Common->custom_query($prod_query,'get');
				// echo "<pre>";
				// print_r($product_status);
				$key = 0;
				// foreach ($product_status[0] as $index => $pd_status)
				foreach ($product_status as $index => $pd_status)
				{
					// if(!empty($pd_status))
					if(!empty($pd_status) && isset($pd_status))
					{
						$pd_offline_tag = $pd_status[$key]['offline_tag'];
	            		$pd_offline_value = $pd_status[$key]['offline_value'];
	            		$pd_offline_from = $pd_status[$key]['offline_from'];
	            		$pd_offline_to = $pd_status[$key]['offline_to'];

	            		if ((time() >= $pd_offline_from) && (time() <= $pd_offline_to))
	            		{
							# "CURRENTLY DISABLE" so no need to pass its data
						}else
						{
	            			$response[$key]['product'] = $pd_status;
	            			if($tokenData === false || $tokenData == '' ) # Empty check because we made it as empty in 2123 line
	            			{
	            				$response[$key]['product'][$key]['isCart'] = 0;
	            				$is_variant = $this->check_if_variant_available($pd_status[$key]['product_id']);
	            				if(!empty($is_variant))
	            				{
	            					$response[$key]['product'][$key]['is_variant_avl'] = 1;
	            				}else
	            				{
	            					$response[$key]['product'][$key]['is_variant_avl'] = 0;
	            				}
	            			}else
	            			{
		            			$cart_status = $this->Common->getData('cart' , 'product_quantity,product_id' , array('product_id' => $pd_status[$key]['product_id'] , 'user_id' => $tokenData->id));
	            				if(!empty($cart_status))
	            				{
	            					$response[$key]['product'][$key]['isCart'] = 1;
	            					$response[$key]['product'][$key]['cart'] = $cart_status;
	            				}else
	            				{
	            					$response[$key]['product'][$key]['isCart'] = 0;
	            				}
	            				$is_variant = $this->check_if_variant_available($pd_status[$key]['product_id']);
	            				if(!empty($is_variant))
	            				{
	            					$response[$key]['product'][$key]['is_variant_avl'] = 1;
	            				}else
	            				{
	            					$response[$key]['product'][$key]['is_variant_avl'] = 0;
	            				}
	            			}
						}
						$key++;
					}
				}
				$super_key++;
    		}
		}
		return $response;
    }

    public function get_restaurant_data_with_searched_products($rest,$hours,$tokenData,$lat = '',$lng = '',$query_part_one='',$page , $limit,$date_timestamp = '')
    {

    	# Here we need to check whether this restraurant has any product If yes then only we will add it to response
    	$super_key = 0;
    	foreach ($rest as $anykey => $restaurant) 
		{
			$query_run = $query_part_one;
    		$has_products = $this->Common->getData('products','products.id','products.restaurant_id = "'.$restaurant['restaurant_id'].'" AND product_status = 1');

    		if(count($has_products) > 0)
    		{
    			$response[$super_key]['restaurant'] = $restaurant;
    			$response[$super_key]['restaurant']['rest_name'] = stripslashes($restaurant['rest_name']);
				$response[$super_key]['del_prep_time'] = $hours;
				# Check whether any offer to any of the liked restaurant is given?
				# This is done because avg_rating from mobile app was not working so we need to display 5 rating to each restro so wea re sengin static value of rating
				# Static pass
				// $response[$super_key]['restaurant']['avg_rating'] = '5';

				$promotions = $this->Common->getData('promotions','promo_type,discount_value,valid_from,valid_till',' promotion_mode_status = 1 AND promo_status = 1 AND (restaurant_id = "'.$restaurant['restaurant_id'].'") AND (valid_from < "'.time().'" AND valid_till > "'.time().'") AND level_id = 2');  # level id 2 is for restaurant
				if(count($promotions) > 0)
				{
					$response[$super_key]['promotions'] = $promotions[0];	
				}else
				{
					$x = new stdClass();
					$response[$super_key]['promotions'] = $x;
				}
				# Check whether any offline entry for the listed restaurant is available?
				$offline_status = $this->Common->getData('rest_offline','rest_offline.offline_tag,rest_offline.offline_value,rest_offline.offline_from,rest_offline.offline_to','rest_offline.rest_id = "'.$restaurant['restaurant_id'].'"',array('restaurants'),array('restaurants.id = rest_offline.rest_id'));
				if(count($offline_status) > 0)
				{
					$response[$super_key]['offline_status'] = $offline_status[0];
				}else
				{
					$x = new stdClass();
					$response[$super_key]['offline_status'] = $x;
				}

				/* # DB_to_type = 1 - For food to restaurant 2 - For food to Kerala Eats 3 - For delivery to restaurant
				$avg_del_review = $this->Common->getData('ratings','AVG(given_rating) AS del_rating','to_id = "'.$restaurant['restaurant_id'].'"');
				$avg_del_review[0]['del_rating'];
				$response[$key]['delivery_rating'] = $avg_del_review;*/

				# Check Wishlist status
				if($tokenData == '')
				{
					$response[$super_key]['isWishList'] = 0;
				}else
				{
					$wishlist_check = $this->Common->getData('wishlist','id','restaurant_id = "'.$restaurant['restaurant_id'].'" AND user_id = "'.$tokenData->id.'"');
					if(count($wishlist_check) > 0)
					{
						$response[$super_key]['isWishList'] = 1;	
					}else
					{
						$response[$super_key]['isWishList'] = 0;
					}
				}

				if($lat != '' && $lng != '')
				{
					$rest_lat = $restaurant['latitude'];
					$rest_lng = $restaurant['longitude'];

					# Calculate Distance between user's latlong and restaurants latlong
					$response[$super_key]['distance'] = $this->calculate_distance_between_latlong($lat,$rest_lat,$lng,$rest_lng);
				}
				$append_to_query = $query_run.' AND (products.restaurant_id = "'.$restaurant['restaurant_id'].'") ORDER BY products.price ASC LIMIT '.$page.','.$limit;
				$product_status = array();
				$product_status[] = $this->Common->custom_query($append_to_query,'get');
				// echo "<pre>";
				// print_r($product_status);die;
				$key = 0;
				$prod_info = array();
				foreach ($product_status[0] as $index => $pd_status)
				{
					if(!empty($pd_status) && isset($pd_status))
					{
						// echo "<br>KEYVAL".$key;
						$pd_offline_tag = $pd_status['offline_tag'];
	            		$pd_offline_value = $pd_status['offline_value'];
	            		$pd_offline_from = $pd_status['offline_from'];
	            		$pd_offline_to = $pd_status['offline_to'];

	            		if ((time() >= $pd_offline_from) && (time() <= $pd_offline_to))
	            		{
							# "CURRENTLY DISABLE" so no need to pass its data
						}else
						{
	            			// $response[$super_key]['product'] = $pd_status;
	            			// $prod_info[] = $pd_status;
	            			if($tokenData === false || $tokenData == '' ) # Empty check because we made it as empty in 2123 line
	            			{
	            				// $response[$key]['product']['isCart'] = 0;
	            				$pd_status['isCart'] = 0;
	            				$is_variant = $this->check_if_variant_available($pd_status['product_id']);
	            				if(!empty($is_variant))
	            				{
	            					// $response[$key]['product']['is_variant_avl'] = 1;
	            					$pd_status['is_variant_avl'] = 1;
	            				}else
	            				{
	            					// $response[$key]['product']['is_variant_avl'] = 0;
	            					$pd_status['is_variant_avl'] = 0;
	            				}
	            			}else
	            			{
		            			$cart_status = $this->Common->getData('cart' , 'product_quantity,product_id' , array('product_id' => $pd_status['product_id'] , 'user_id' => $tokenData->id));
		            			// echo $this->db->last_query();
	            				if(!empty($cart_status))
	            				{
	            					// $response[$key]['product']['isCart'] = 1;
	            					// $response[$key]['product']['cart'] = $cart_status;
	            					$pd_status['isCart'] = 1;
	            					$pd_status['cart'] = $cart_status;
	            				}else
	            				{
	            					// $response[$key]['product']['isCart'] = 0;
	            					$pd_status['isCart'] = 0;
	            				}
	            				$is_variant = $this->check_if_variant_available($pd_status['product_id']);
	            				if(!empty($is_variant))
	            				{
	            					// $response[$key]['product']['is_variant_avl'] = 1;
	            					$pd_status['is_variant_avl'] = 1;
	            				}else
	            				{
	            					// $response[$key]['product']['is_variant_avl'] = 0;
	            					$pd_status['is_variant_avl'] = 0;
	            				}
	            			}
	            			$prod_info[] = $pd_status;
						}
						$key++;
					}else
					{
						// echo "<br>HEREE";
					}
				} # END OF FOREACH
				$response[$super_key]['product'] = $prod_info;
				$super_key++;
    		}
		}
		return $response;
		// echo "<pre>";
		// print_r($response);
    }

    public function get_restaurant_open_close_status($rest_id , $date_timestamp)
	{
		// echo "<hr>";
		// echo "<br>REST ID IS ".$rest_id;
		date_default_timezone_set('Asia/Singapore');
		// echo "<br>Passed timestamp is ".$date_timestamp;
		$offline_data = $this->Common->getData('rest_offline','*','rest_id = "'.$rest_id.'"');
		// echo "<br> PRINTING OFFLINE DATA <pre>";
		// print_r($offline_data);

		// echo "<br>CHECK IT ".date('y-m-d',$date_timestamp);
		$make_midnight = date('y-m-d',$date_timestamp);
		$date = strtotime($make_midnight);

		if(count($offline_data) > 0)
		{
			$rest_basic_details = $this->Common->getData('restaurants','*','id = "'.$rest_id.'"');

			$offline_from = $offline_data[0]['offline_from'];
			$offline_to = $offline_data[0]['offline_to'];
			
			$proceed_further = 0; # 0 No 1 Yes
			$proceed_further_more = 0; # 0 No 1 Yes

			if($offline_data[0]['offline_tag'] != 1)
			{
				# ######## FOR INDIA UNCOMMENT BELOW ########
				// $offline_from_val = $offline_from - (5*60*60); # DEDUCT 5 HOURS
				// $offline_from = $offline_from_val - (30 *60 ); # AND THEN 30 MINUTES

				// $offline_to_val = $offline_to - (5*60*60); # DEDUCT 5 HOURS
				// $offline_to = $offline_to_val - (30 *60 ); # AND THEN 30 MINUTES
				# ######## FOR INDIA ########

				# Why we are subtracting because UTC and singapore having 8  hours difference and the localtime passed as timestamp will be 8 hours less than the UTC so either dedcut 8 hours from UTC or add 8 hours to the given timestamp
				# ######## FOR SINGAPORE UNCOMMENT BELOW ######### 
				$offline_from = $offline_from - (8*60*60); # DEDUCT 8 HOURS
				$offline_to = $offline_to - (8*60*60); # DEDUCT 8 HOURS
				# ######## FOR SINGAPORE ######### 
			}
			// echo "<hr>";
			// echo "<br>REST ID IS ".$rest_id;
			// echo "<br>date_timestamp".$date_timestamp;
			// echo "<br>offline_from".$offline_from;
			// echo "<br>offline_to".$offline_to;
			if($date_timestamp >= $offline_from  && $date_timestamp <= $offline_to) # $date_timestamp is midnight timestamp like 00:00
			{
				if($rest_basic_details[0]['time_mode'] == 1) # 1 - for every day, 2 - for Specific day
				{
					$open_time = $rest_basic_details[0]['open_time'];
					$close_time = $rest_basic_details[0]['close_time'];

					$open_time_exp = explode(":",$open_time); # 11:30
					$open_time_hr = $open_time_exp[0]; # 11
					$open_time_min = $open_time_exp[1]; # 30

					$open_time = $date + ($open_time_hr * 60 * 60); # Adding hours
					$open_time = $open_time + ($open_time_min * 60); # ADD MINUTES

					$close_time_exp = explode(":",$close_time); # 11:30
					$close_time_hr = $close_time_exp[0]; # 11
					$close_time_min = $close_time_exp[1]; # 30

					$close_time = $date + ($close_time_hr * 60 * 60); # Adding hours
					$close_time = $close_time + ($close_time_min * 60); # ADD MINUTES
					if($rest_basic_details[0]['break_start_time'] == '')
					{
						# NO BREAK IS GIVEN
						$break_from = '';
						$break_to = '';
					}else
					{
						$break_start_time = $rest_basic_details[0]['break_start_time'];
						$break_end_time = $rest_basic_details[0]['break_end_time'];
						$break_start_exp = explode(":",$break_start_time);
						// echo "<pre>";
						// print_r($break_start_exp);
						$break_start_hr = $break_start_exp[0];
						$break_start_min = $break_start_exp[1];

						$break_end_exp = explode(":",$break_end_time);
						$break_end_hr = $break_end_exp[0];
						$break_end_min = $break_end_exp[1];

						# So here we need to add hours to the $date_timestamp param
						$break_from = $date + ($break_start_hr * 60 * 60); # Adding hours
						$break_from = $break_from + ($break_start_min * 60); # ADD MINUTES

						$break_to = $date + ($break_end_hr * 60 * 60); # Adding HOURS
						$break_to = $break_to + ($break_end_min * 60);
					}
				}else # MODE is 2
				{
					$weekday = date('l', $date_timestamp);
					$weekday = strtolower($weekday);
					// echo "<br>weekday is".$weekday;
					$rest_time_daywise = $this->Common->getData('rest_time_daywise','*','rest_id = "'.$rest_id.'"');
					if($weekday == 'monday')
					{
						$full_day_close_status = $rest_time_daywise[0]['mon_close_status'];
						$open_close_time = $rest_time_daywise[0]['mon_open_close_time'];
						$brk_status = $rest_time_daywise[0]['mon_break_status'];
						$brk_start_brk_end = $rest_time_daywise[0]['mon_break_start_end_time'];
					}elseif($weekday == 'tuesday'){
						$full_day_close_status = $rest_time_daywise[0]['tue_close_status'];
						$open_close_time = $rest_time_daywise[0]['tue_open_close_time'];
						$brk_status = $rest_time_daywise[0]['tue_break_status'];
						$brk_start_brk_end = $rest_time_daywise[0]['tue_break_start_end_time'];
					}elseif($weekday == 'wednesday'){
						$full_day_close_status = $rest_time_daywise[0]['wed_close_status'];
						$open_close_time = $rest_time_daywise[0]['wed_open_close_time'];
						$brk_status = $rest_time_daywise[0]['wed_break_status'];
						$brk_start_brk_end = $rest_time_daywise[0]['wed_break_start_end_time'];
					}elseif($weekday == 'thursday'){
						$full_day_close_status = $rest_time_daywise[0]['thu_close_status'];
						$open_close_time = $rest_time_daywise[0]['thu_open_close_time'];
						$brk_status = $rest_time_daywise[0]['thu_break_status'];
						$brk_start_brk_end = $rest_time_daywise[0]['thu_break_start_end_time'];
					}elseif($weekday == 'friday'){
						$full_day_close_status = $rest_time_daywise[0]['fri_close_status'];
						$open_close_time = $rest_time_daywise[0]['fri_open_close_time'];
						$brk_status = $rest_time_daywise[0]['fri_break_status'];
						$brk_start_brk_end = $rest_time_daywise[0]['fri_break_start_end_time'];
					}elseif($weekday == 'saturday'){
						$full_day_close_status = $rest_time_daywise[0]['sat_close_status'];
						$open_close_time = $rest_time_daywise[0]['sat_open_close_time'];
						$brk_status = $rest_time_daywise[0]['sat_break_status'];
						$brk_start_brk_end = $rest_time_daywise[0]['sat_break_start_end_time'];
					}elseif($weekday == 'sunday'){
						$full_day_close_status = $rest_time_daywise[0]['sun_close_status'];
						$open_close_time = $rest_time_daywise[0]['sun_open_close_time'];
						$brk_status = $rest_time_daywise[0]['sun_break_status'];
						$brk_start_brk_end = $rest_time_daywise[0]['sun_break_start_end_time'];
					}

					$exp_all = explode("-",$open_close_time);
					$open_time = $exp_all[0];
					$open_time_exp = explode(":",$open_time); # 11:30
					$open_time_hr = $open_time_exp[0]; # 11
					$open_time_min = $open_time_exp[1]; # 30

					$open_time = $date + ($open_time_hr * 60 * 60); # Adding hours
					$open_time = $open_time + ($open_time_min * 60); # ADD MINUTES

					$close_time = $exp_all[1];
					$close_time_exp = explode(":",$close_time); # 11:30
					$close_time_hr = $close_time_exp[0]; # 11
					$close_time_min = $close_time_exp[1]; # 30

					$close_time = $date + ($close_time_hr * 60 * 60); # Adding hours
					$close_time = $close_time + ($close_time_min * 60); # ADD MINUTES
					if($brk_status == 1) # 2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time) and 0 when restaurant is closed in full day
					{
						$brk_start_brk_end = explode("-",$brk_start_brk_end);
						$break_start_time = $brk_start_brk_end[0];
						$break_end_time = $brk_start_brk_end[1];

						$break_start_exp = explode(":",$break_start_time);
						// echo "<br>break_start_hr".$break_start_hr = $break_start_exp[0];
						// echo "<br>break_start_min".$break_start_min = $break_start_exp[1];
						$break_start_hr = $break_start_exp[0];
						$break_start_min = $break_start_exp[1];

						$break_end_exp = explode(":",$break_end_time);
						// echo "<br>break_end_hr".$break_end_hr = $break_end_exp[0];
						// echo "<br>break_end_min".$break_end_min = $break_end_exp[1];
						$break_end_hr = $break_end_exp[0];
						$break_end_min = $break_end_exp[1];

						# So here we need to add hours to the $date_timestamp param
						// echo "<br> FIRST STEP".$date_timestamp_mid = date('y-m-d',$date_timestamp);
						// echo "<br>date_timestamp_mid".$date_timestamp_mid = strtotime($date_timestamp_mid);
						$date_timestamp_mid = date('y-m-d',$date_timestamp);
						$date_timestamp_mid = strtotime($date_timestamp_mid);
						$break_from = $date_timestamp_mid + ($break_start_hr * 60 * 60); # Adding hours
						$break_from = $break_from + ($break_start_min * 60); # ADD MINUTES

						$break_to = $date_timestamp_mid + ($break_end_hr * 60 * 60); # Adding HOURS
						$break_to = $break_to + ($break_end_min * 60);
					}else
					{
						$break_from = '';
						$break_to = '';
					}
				}
				$check_further = 0;
				// echo "<br>open_time".$open_time;
				// echo "<br>close_time".$close_time;
				// echo "<br>break_from".$break_from;
				// echo "<br>break_to".$break_to;
				// echo "<br>offline_to".$offline_to;
				// die;
				$check_further = 0;
				
				if($offline_to >= $open_time && $offline_to <= $close_time)
				{
					# Coming in between open close so Okay
					if($break_from != '')
					{
						if($offline_to >= $break_from && $offline_to <= $break_to)
						{
							# Coming in break
							$proceed_further = 0;
							// echo "<br>QQQQQ".$send_next_open_time = $break_to;
							$send_next_open_time = $break_to;
							return array('status' => '2','next_open_time' => (string)$send_next_open_time);
						}else
						{
							$proceed_further = 0;
							$send_next_open_time = $offline_to;
							// echo "<br>PPPPP".$send_next_open_time = $offline_to;
							return array('status' => '2','next_open_time' => (string)$send_next_open_time);
						}
					}else
					{
						# NO BREAK and already in between open close so open time send
						$proceed_further = 0;
						// echo "<br>TTTTT".$send_next_open_time = $offline_to;
						$send_next_open_time = $offline_to;
						return array('status' => '2','next_open_time' => (string)$send_next_open_time);
					}
				}elseif($offline_to > $close_time)
				{
					$check_further = 1;
				}
				
				
				// echo "<br>CHECK FUTHER".$check_further;
				
				if($check_further == 1)
				{
					# Find next open time as per the time mode
					if($rest_basic_details[0]['time_mode'] == 1)
					{
						// echo "hererrrrrr";die;
						$send_next_open_time = $open_time;
						return array('status' => '2','next_open_time' => (string)$send_next_open_time);
					}else
					{
						// DAYWISE 
						$mon_full_day_close_status = $rest_time_daywise[0]['mon_close_status'];
						$tue_full_day_close_status = $rest_time_daywise[0]['tue_close_status'];
						$wed_full_day_close_status = $rest_time_daywise[0]['wed_close_status'];
						$thu_full_day_close_status = $rest_time_daywise[0]['thu_close_status'];
						$fri_full_day_close_status = $rest_time_daywise[0]['fri_close_status'];
						$sat_full_day_close_status = $rest_time_daywise[0]['sat_close_status'];
						$sun_full_day_close_status = $rest_time_daywise[0]['sun_close_status'];

						$mon_open_close_time = $rest_time_daywise[0]['mon_open_close_time'];
						$tue_open_close_time = $rest_time_daywise[0]['tue_open_close_time'];
						$wed_open_close_time = $rest_time_daywise[0]['wed_open_close_time'];
						$thu_open_close_time = $rest_time_daywise[0]['thu_open_close_time'];
						$fri_open_close_time = $rest_time_daywise[0]['fri_open_close_time'];
						$sat_open_close_time = $rest_time_daywise[0]['sat_open_close_time'];
						$sun_open_close_time = $rest_time_daywise[0]['sun_open_close_time'];

						// echo "HERE";die;
						$open_status = 2; # 1 : OPEN 2 : CLOSE
						$given_timestamp = $date_timestamp;
						// while($open_status != 1)
						$x = 0;
						while($x <=500)
						{
							// echo "<br>CHANCCHAL".$x;
							$next_day_timestamp = strtotime('tomorrow',$given_timestamp);
							$next_day = date('l', $next_day_timestamp);
							$given_timestamp = $next_day_timestamp;
							$next_day = strtolower($next_day);
							// echo "<br> next day IS ".$next_day;
							if($next_day == 'monday')
							{
								if($mon_full_day_close_status == 1)
								{
									$open_close_response = $this->get_open_close_time($next_day_timestamp , $mon_open_close_time);
									$send_next_open_time = $open_close_response['open_time'];
									if($send_next_open_time > $offline_to)
									{
										$open_status = 1;
										return array('status' => '2','next_open_time' => (string)$send_next_open_time);
									}else
									{
										$open_status = 2;		
									}
								}else
								{
									$open_status = 2;
								}
							}

							if($next_day == 'tuesday')
							{
								if($tue_full_day_close_status == 1)
								{
									$open_close_response = $this->get_open_close_time($next_day_timestamp ,$tue_open_close_time);
									$send_next_open_time = $open_close_response['open_time'];
									if($send_next_open_time > $offline_to)
									{
										$open_status = 1;
										return array('status' => '2','next_open_time' => (string)$send_next_open_time);
									}else
									{
										$open_status = 2;		
									}
								}else
								{
									$open_status = 2;
								}
							}

							if($next_day == 'wednesday')
							{
								if($wed_full_day_close_status == 1)
								{
									$open_close_response = $this->get_open_close_time($next_day_timestamp ,$wed_open_close_time);
									$send_next_open_time = $open_close_response['open_time'];
									if($send_next_open_time > $offline_to)
									{
										$open_status = 1;
										return array('status' => '2','next_open_time' => (string)$send_next_open_time);
									}else
									{
										$open_status = 2;		
									}
								}else
								{
									$open_status = 2;
								}
							}

							if($next_day == 'thursday')
							{
								if($thu_full_day_close_status == 1)
								{
									$open_close_response = $this->get_open_close_time($next_day_timestamp ,$thu_open_close_time);
									$send_next_open_time = $open_close_response['open_time'];
									if($send_next_open_time > $offline_to)
									{
										$open_status = 1;
										return array('status' => '2','next_open_time' => (string)$send_next_open_time);
									}else
									{
										$open_status = 2;		
									}
								}else
								{
									$open_status = 2;
								}
							}

							if($next_day == 'friday')
							{
								if($fri_full_day_close_status == 1)
								{
									// echo $fri_open_close_time;
									$open_close_response = $this->get_open_close_time($next_day_timestamp ,$fri_open_close_time);
									// echo "<pre>";
									// print_r($open_close_response);
									$send_next_open_time = $open_close_response['open_time'];
									if($send_next_open_time > $offline_to)
									{
										$open_status = 1;
										return array('status' => '2','next_open_time' => (string)$send_next_open_time);
									}else
									{
										$open_status = 2;		
									}
								}else
								{
									$open_status = 2;
								}
							}

							if($next_day == 'saturday')
							{
								if($sat_full_day_close_status == 1)
								{
									$open_close_response = $this->get_open_close_time($next_day_timestamp ,$sat_open_close_time);
									$send_next_open_time = $open_close_response['open_time'];
									if($send_next_open_time > $offline_to)
									{
										$open_status = 1;
										return array('status' => '2','next_open_time' => (string)$send_next_open_time);
									}else
									{
										$open_status = 2;		
									}
								}else
								{
									$open_status = 2;
								}
							}

							if($next_day == 'sunday')
							{
								if($sun_full_day_close_status == 1)
								{
									$open_close_response = $this->get_open_close_time($next_day_timestamp ,$sun_open_close_time);
									$send_next_open_time = $open_close_response['open_time'];
									if($send_next_open_time > $offline_to)
									{
										$open_status = 1;
										return array('status' => '2','next_open_time' => (string)$send_next_open_time);
									}else
									{
										$open_status = 2;		
									}
								}else
								{
									$open_status = 2;
								}
							}
							$x++;
						}
					}
				}
				die;

				/* $data['status']		=201;
		        $data['message']	="Restaurant is set to Offline and not offering dinein on this day";
		        $data['data']		=array();*/
		        # return 2; # CLOSED
			}else
			{
				$proceed_further = 1;
			}
		}else # OPEN # No entry in offline table
		{
		    $proceed_further = 1;
		}
		// echo "<hr>";
		// echo "<br>proceed_further".$proceed_further;
		// die;
		if($proceed_further == 1)
		{
			$rest_basic_details = $this->Common->getData('restaurants','*','id = "'.$rest_id.'"');
			# Get Time mode
			// echo "<br>TIME MODE ".$rest_basic_details[0]['time_mode'];
			if($rest_basic_details[0]['time_mode'] == 1) # 1 - for every day, 2 - for Specific day
			{
				# Here these values will be in hrs:min format so we have to convert them to timestamp (open,close,break start,break end)
				$open_time = $rest_basic_details[0]['open_time'];
				$close_time = $rest_basic_details[0]['close_time'];

				$open_time_exp = explode(":",$open_time); # 11:30
				$open_time_hr = $open_time_exp[0]; # 11
				$open_time_min = $open_time_exp[1]; # 30

				$open_time = $date + ($open_time_hr * 60 * 60); # Adding hours
				$open_time = $open_time + ($open_time_min * 60); # ADD MINUTES

				$close_time_exp = explode(":",$close_time); # 11:30
				$close_time_hr = $close_time_exp[0]; # 11
				$close_time_min = $close_time_exp[1]; # 30

				$close_time = $date + ($close_time_hr * 60 * 60); # Adding hours
				$close_time = $close_time + ($close_time_min * 60); # ADD MINUTES

				if($rest_basic_details[0]['break_start_time'] == '')
				{
					# NO BREAK IS GIVEN
					$break_from = '';
					$break_to = '';
				}else
				{
					$break_start_time = $rest_basic_details[0]['break_start_time'];
					$break_end_time = $rest_basic_details[0]['break_end_time'];
					$break_start_exp = explode(":",$break_start_time);
					// echo "<pre>";
					// print_r($break_start_exp);
					$break_start_hr = $break_start_exp[0];
					$break_start_min = $break_start_exp[1];

					$break_end_exp = explode(":",$break_end_time);
					$break_end_hr = $break_end_exp[0];
					$break_end_min = $break_end_exp[1];

					# So here we need to add hours to the $date_timestamp param
					$break_from = $date + ($break_start_hr * 60 * 60); # Adding hours
					$break_from = $break_from + ($break_start_min * 60); # ADD MINUTES

					$break_to = $date + ($break_end_hr * 60 * 60); # Adding HOURS
					$break_to = $break_to + ($break_end_min * 60);
				}
				// echo "<br>date_timestamp IS ".$date_timestamp;
				// echo "<br>open_time IS ".$open_time;
				// echo "<br>close_time IS ".$close_time;
				// echo "<br>break_from IS ".$break_from;
				// echo "<br>break_to IS ".$break_to;
				// echo $break_from;die;
				if($date_timestamp >= $open_time  && $date_timestamp <= $close_time) # OPEN
				{
					// echo "<br>QQQQQ";
					if(($break_from != '') && ($date_timestamp >= $break_from  && $date_timestamp <= $break_to))
					{
						// echo "<br>WWWWWWW";
						// echo "<br>CHANCHAL";
						// echo "<br>break_from".$break_from;
						// echo "<br>break_to".$break_to;
						$proceed_further_more = 0;
						# return 3; # CLOSED
						return array('status' => '3','next_open_time' => (string)$break_to);
						# On Break
						/* $data['status']		=201;
				        $data['message']	="Restaurant is on break during this time";
				        $data['data']		=array(); */	
					}else
					{
						$proceed_further_more = 1;
					}
				}else
				{
					$proceed_further_more = 0;
					if($date_timestamp < $open_time)
					{
						// echo "<br>less than open".$rest_id;
						# return 4; # CLOSED # NOT IN OPEN CLOSE
						return array('status' => '4','next_open_time' => (string)$open_time);
					}else if($date_timestamp > $close_time)
					{
						# Pass next day's open time
						// echo "<br>greater than close".$rest_id;
						// echo "<br>tomorrow timestamp is ".$tomorrow = strtotime('tomorrow',$date_timestamp);
						// echo "<br>OPEN TIME HOURS  ".$open_time_hr;
						// echo "<br>OPEN TIME MINUTES  ".$open_time_min;
						// echo "<br>added hours ".$tomorrow = $tomorrow + ($open_time_hr * 60 * 60); # Adding hours
						// echo "<br>added miutes ".$tomorrow = $tomorrow + ($open_time_min * 60); # ADD MINUTES
						// echo "<br>SEE IT ".$tomorrow;
						# Pass next day's open time

						$tomorrow_open_time = strtotime('tomorrow',$date_timestamp);
						$tomorrow_open_time = $tomorrow_open_time + ($open_time_hr * 60 * 60); # Adding hours
						$tomorrow_open_time = $tomorrow_open_time + ($open_time_min * 60); # ADD MINUTES

						return array('status' => '4','next_open_time' => (string)$tomorrow_open_time);
					}

				}
			}else if($rest_basic_details[0]['time_mode'] == 2)
			{
				// echo "here";
				# Get what is the Day of the passed date_timestamp
			
				$weekday = date('l', $date_timestamp);
				$weekday = strtolower($weekday);
				// echo "<br>weekday is ".$weekday;
				$rest_time_daywise = $this->Common->getData('rest_time_daywise','*','rest_id = "'.$rest_id.'"');

				if(count($rest_time_daywise) > 0)
				{
					# We have to get fullday open status for all days
					$mon_full_day_close_status = $rest_time_daywise[0]['mon_close_status'];
					$tue_full_day_close_status = $rest_time_daywise[0]['tue_close_status'];
					$wed_full_day_close_status = $rest_time_daywise[0]['wed_close_status'];
					$thu_full_day_close_status = $rest_time_daywise[0]['thu_close_status'];
					$fri_full_day_close_status = $rest_time_daywise[0]['fri_close_status'];
					$sat_full_day_close_status = $rest_time_daywise[0]['sat_close_status'];
					$sun_full_day_close_status = $rest_time_daywise[0]['sun_close_status'];
					// echo "FRIDAY STATUS".$fri_full_day_close_status;
					# Here check wehther restro is closed on ALL days?
					if($mon_full_day_close_status == 2 && $tue_full_day_close_status == 2 && $wed_full_day_close_status == 2 && $thu_full_day_close_status == 2 && $fri_full_day_close_status == 2 && $sat_full_day_close_status == 2 && $sun_full_day_close_status == 2)
					{
						return array('status' => '5','next_open_time' => ''); # PASSING EMPTY STRING IF RESTAURANT IS ALL 'next_open_time' => DAY CLOSED
					}else
					{
						$mon_open_close_time = $rest_time_daywise[0]['mon_open_close_time'];
						$tue_open_close_time = $rest_time_daywise[0]['tue_open_close_time'];
						$wed_open_close_time = $rest_time_daywise[0]['wed_open_close_time'];
						$thu_open_close_time = $rest_time_daywise[0]['thu_open_close_time'];
						$fri_open_close_time = $rest_time_daywise[0]['fri_open_close_time'];
						$sat_open_close_time = $rest_time_daywise[0]['sat_open_close_time'];
						$sun_open_close_time = $rest_time_daywise[0]['sun_open_close_time'];

						if($weekday == 'monday'){
							$full_day_close_status = $rest_time_daywise[0]['mon_close_status'];
							$open_close_time = $rest_time_daywise[0]['mon_open_close_time'];
							$brk_status = $rest_time_daywise[0]['mon_break_status'];
							$brk_start_brk_end = $rest_time_daywise[0]['mon_break_start_end_time'];
						}elseif($weekday == 'tuesday'){
							$full_day_close_status = $rest_time_daywise[0]['tue_close_status'];
							$open_close_time = $rest_time_daywise[0]['tue_open_close_time'];
							$brk_status = $rest_time_daywise[0]['tue_break_status'];
							$brk_start_brk_end = $rest_time_daywise[0]['tue_break_start_end_time'];
						}elseif($weekday == 'wednesday'){
							$full_day_close_status = $rest_time_daywise[0]['wed_close_status'];
							$open_close_time = $rest_time_daywise[0]['wed_open_close_time'];
							$brk_status = $rest_time_daywise[0]['wed_break_status'];
							$brk_start_brk_end = $rest_time_daywise[0]['wed_break_start_end_time'];
						}elseif($weekday == 'thursday'){
							$full_day_close_status = $rest_time_daywise[0]['thu_close_status'];
							$open_close_time = $rest_time_daywise[0]['thu_open_close_time'];
							$brk_status = $rest_time_daywise[0]['thu_break_status'];
							$brk_start_brk_end = $rest_time_daywise[0]['thu_break_start_end_time'];
						}elseif($weekday == 'friday'){
							$full_day_close_status = $rest_time_daywise[0]['fri_close_status'];
							$open_close_time = $rest_time_daywise[0]['fri_open_close_time'];
							$brk_status = $rest_time_daywise[0]['fri_break_status'];
							$brk_start_brk_end = $rest_time_daywise[0]['fri_break_start_end_time'];
						}elseif($weekday == 'saturday'){
							$full_day_close_status = $rest_time_daywise[0]['sat_close_status'];
							$open_close_time = $rest_time_daywise[0]['sat_open_close_time'];
							$brk_status = $rest_time_daywise[0]['sat_break_status'];
							$brk_start_brk_end = $rest_time_daywise[0]['sat_break_start_end_time'];
						}elseif($weekday == 'sunday'){
							$full_day_close_status = $rest_time_daywise[0]['sun_close_status'];
							$open_close_time = $rest_time_daywise[0]['sun_open_close_time'];
							$brk_status = $rest_time_daywise[0]['sun_break_status'];
							$brk_start_brk_end = $rest_time_daywise[0]['sun_break_start_end_time'];
						}

						// echo "<br>brk_status is ".$brk_status;
						if($full_day_close_status == 2) # 2- on this day restaurant will be closed, 1 - restaurant will be opend
						{
							$proceed_further_more = 0;
							$open_status = 2; # 1 : OPEN 2 : CLOSE
							$x = 1;
							$given_timestamp = $date_timestamp;
							while($open_status != 1)
							{
								$next_day_timestamp = strtotime('tomorrow',$given_timestamp);
								$next_day = date('l', $next_day_timestamp);
								$given_timestamp = $next_day_timestamp;
								$next_day = strtolower($next_day);
								// echo "<br> next day IS ".$next_day;
								if($next_day == 'monday')
								{
									if($mon_full_day_close_status == 1)
									{
										$open_close_response = $this->get_open_close_time($next_day_timestamp , $mon_open_close_time);
										$next_open_time = $open_close_response['open_time'];
										$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
										$open_status = 1;
									}else
									{
										$open_status = 2;
									}
								}

								if($next_day == 'tuesday')
								{
									if($tue_full_day_close_status == 1)
									{
										$open_close_response = $this->get_open_close_time($next_day_timestamp ,$tue_open_close_time);
										$next_open_time = $open_close_response['open_time'];
										$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
										$open_status = 1;
									}else
									{
										$open_status = 2;
									}
								}

								if($next_day == 'wednesday')
								{
									if($wed_full_day_close_status == 1)
									{
										$open_close_response = $this->get_open_close_time($next_day_timestamp ,$wed_open_close_time);
										$next_open_time = $open_close_response['open_time'];
										$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
										$open_status = 1;
									}else
									{
										$open_status = 2;
									}
								}

								if($next_day == 'thursday')
								{
									if($thu_full_day_close_status == 1)
									{
										$open_close_response = $this->get_open_close_time($next_day_timestamp ,$thu_open_close_time);
										$next_open_time = $open_close_response['open_time'];
										$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
										$open_status = 1;
									}else
									{
										$open_status = 2;
									}
								}

								if($next_day == 'friday')
								{
									if($fri_full_day_close_status == 1)
									{
										// echo $fri_open_close_time;
										$open_close_response = $this->get_open_close_time($next_day_timestamp ,$fri_open_close_time);
										// echo "<pre>";
										// print_r($open_close_response);
										$next_open_time = $open_close_response['open_time'];
										$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
										$open_status = 1;
									}else
									{
										$open_status = 2;
									}
								}

								if($next_day == 'saturday')
								{
									if($sat_full_day_close_status == 1)
									{
										$open_close_response = $this->get_open_close_time($next_day_timestamp ,$sat_open_close_time);
										$next_open_time = $open_close_response['open_time'];
										$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
										$open_status = 1;
									}else
									{
										$open_status = 2;
									}
								}

								if($next_day == 'sunday')
								{
									if($sun_full_day_close_status == 1)
									{
										$open_close_response = $this->get_open_close_time($next_day_timestamp ,$sun_open_close_time);
										$next_open_time = $open_close_response['open_time'];
										$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
										$open_status = 1;
									}else
									{
										$open_status = 2;
									}
								}
								// $x++;
							}
							// echo "RETURN ARRAY <pre>";
							// print_r($return_array);
							// die;
							return $return_array;
							# GET NEXT DAY HERE

							# Restaurant is closed on this day
							/* $data['status']		=201;
					        $data['message']	="Restaurant is closed on (".ucfirst($weekday).")";
					        $data['data']		=array(); */
						}else # ELSE OF RESTRO NOT CLOSED IN FULL DAY
						{
							$check_and_send_next = 0; # 0 : NO NEED TO CHECK 1 : CHECK
							if($brk_status == 1) # 2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time) and 0 when restaurant is closed in full day
							{
								// echo "<br>CAME HERE";
								# MEANS RESTAURANT IS OPEN ON THE SELECTED DAY BUT HAS GIVEN BREAK TIME
								$brk_start_brk_end = explode("-",$brk_start_brk_end);
				    			$break_start_time = $brk_start_brk_end[0];
								$break_end_time = $brk_start_brk_end[1];

								$break_start_exp = explode(":",$break_start_time);
								// echo "<br>break_start_hr".$break_start_hr = $break_start_exp[0];
								// echo "<br>break_start_min".$break_start_min = $break_start_exp[1];
								$break_start_hr = $break_start_exp[0];
								$break_start_min = $break_start_exp[1];

								$break_end_exp = explode(":",$break_end_time);
								// echo "<br>break_end_hr".$break_end_hr = $break_end_exp[0];
								// echo "<br>break_end_min".$break_end_min = $break_end_exp[1];
								$break_end_hr = $break_end_exp[0];
								$break_end_min = $break_end_exp[1];

								# So here we need to add hours to the $date_timestamp param
								// echo "<br> FIRST STEP".$date_timestamp_mid = date('y-m-d',$date_timestamp);
								// echo "<br>date_timestamp_mid".$date_timestamp_mid = strtotime($date_timestamp_mid);
								$date_timestamp_mid = date('y-m-d',$date_timestamp);
								$date_timestamp_mid = strtotime($date_timestamp_mid);
								$break_from = $date_timestamp_mid + ($break_start_hr * 60 * 60); # Adding hours
								$break_from = $break_from + ($break_start_min * 60); # ADD MINUTES

								$break_to = $date_timestamp_mid + ($break_end_hr * 60 * 60); # Adding HOURS
								$break_to = $break_to + ($break_end_min * 60);

								// echo "<br>break_from".$break_from;
								// echo "<br>break_to".$break_to;
								// echo "<br> date_timestamp".$date_timestamp;


								if($date_timestamp >= $break_from  && $date_timestamp <= $break_to)
								{
									$proceed_further_more = 0;
									# return 6;
									return array('status' => '6','next_open_time' => (string)$break_to);
									# Running on break
									/* $data['status']		=201;
							        $data['message']	="Restaurant is on break during these hours";
							        $data['data']		=array(); */

								}else 
								{
									# That is given timestamp does not lie between break time so now check whether lies between open close ?
									$exp_all = explode("-",$open_close_time);
							        $open_time = $exp_all[0];
							        $open_time_exp = explode(":",$open_time); # 11:30
							        $open_time_hr = $open_time_exp[0]; # 11
							        $open_time_min = $open_time_exp[1]; # 30

							        $open_time = $date + ($open_time_hr * 60 * 60); # Adding hours
							        $open_time = $open_time + ($open_time_min * 60); # ADD MINUTES

							        $close_time = $exp_all[1];
							        $close_time_exp = explode(":",$close_time); # 11:30
							        $close_time_hr = $close_time_exp[0]; # 11
							        $close_time_min = $close_time_exp[1]; # 30

							        $close_time = $date + ($close_time_hr * 60 * 60); # Adding hours
							        $close_time = $close_time + ($close_time_min * 60); # ADD MINUTES

							        // echo "<br>open_time IS ".$open_time;
							        // echo "<br>close_time IS ".$close_time;

									if($date_timestamp >= $open_time  && $date_timestamp <= $close_time)
									{
										// echo "<br>COMING HEREEEEEEE";
										$proceed_further_more = 1;
									}else
									{
										$check_and_send_next = 1;
									}
								}
							}else # No Break given so we need to check whether given timestamp lies between open and close? If yes then closed else open
							{
								# MEANS RESTAURANT IS OPEN ON THE SELECTED DAY AND HAS NO BREAK TIME SO FOR NEXT OPEN TIME WE HAVE TO CHECK NEXT OPEN WEEK DAY STATUS AND OPEN TIME ACCORDINGLY
								// echo "<br> NO BREAK";
								// echo "<br>open_close_time IS ".$open_close_time;
								$exp_all = explode("-",$open_close_time);

						        $open_time = $exp_all[0];
						        $open_time_exp = explode(":",$open_time); # 11:30
						        $open_time_hr = $open_time_exp[0]; # 11
						        $open_time_min = $open_time_exp[1]; # 30

						        $open_time = $date + ($open_time_hr * 60 * 60); # Adding hours
						        $open_time = $open_time + ($open_time_min * 60); # ADD MINUTES

						        $close_time = $exp_all[1];
						        $close_time_exp = explode(":",$close_time); # 11:30
						        $close_time_hr = $close_time_exp[0]; # 11
						        $close_time_min = $close_time_exp[1]; # 30

						        $close_time = $date + ($close_time_hr * 60 * 60); # Adding hours
						        $close_time = $close_time + ($close_time_min * 60); # ADD MINUTES

						        // echo "<br>open_time IS ".$open_time;
						        // echo "<br>close_time IS ".$close_time;

								if($date_timestamp >= $open_time  && $date_timestamp <= $close_time)
								{
									// echo "<br>COMING HERE ";
									$proceed_further_more = 1;
									
								}else
								{
									$proceed_further_more = 0;
									$check_and_send_next = 1;
								}
							}

							if($check_and_send_next == 1)
							{
								// echo "<br> IN ELSE";
								# return 7; # Given time is NOT in between Open and close time
								# Here we have to check that if timestamp iS less than the open time of current day then we can pass open time but If timestamp is greater than Close time then we have to check the next day open status and if next day restro open status is ON then we have to pass open time of next day else again we have to check on which next day restro is open
								# EXAMPLE TODAY IS MONDAY AND RESTRO OS OPEN ON MONDAY FROM 11 AM TO 22 PM. NOW I AS A CUSTOMER SEEING THE RESTRO ON 10AM SO NEXT TIME WILL BE 11 AM FOR SAME DAY BUT IF I AM SEEING RESTRO AFTER 22PM SO NEXT TIME WILL BE OPEN TIME OF NEXT OPEN WEEK DAY.
								if($date_timestamp < $open_time)
								{
									// echo "<br>WWWWWWWW";
									# SO OPEN TIME OF CURRENT WEEK DAY
									return array('status' => '7','next_open_time' => (string)$open_time);
								}elseif($date_timestamp > $close_time)
								{
									// echo "<br>RRRRRR";
									# HERE WE HAVE TO CHECK NEXT OPEN WEEKDAY STATUS
									$open_status = 2; # 1 : OPEN 2 : CLOSE
									$given_timestamp = $date_timestamp;
									while($open_status != 1)
									{
										$next_day_timestamp = strtotime('tomorrow',$given_timestamp);
										$next_day = date('l', $next_day_timestamp);
										$given_timestamp = $next_day_timestamp;
										$next_day = strtolower($next_day);
										// echo "<br> next day IS ".$next_day;
										if($next_day == 'monday')
										{
											if($mon_full_day_close_status == 1)
											{
												$open_close_response = $this->get_open_close_time($next_day_timestamp , $mon_open_close_time);
												$next_open_time = $open_close_response['open_time'];
												$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
												$open_status = 1;
											}else
											{
												$open_status = 2;
											}
										}

										if($next_day == 'tuesday')
										{
											if($tue_full_day_close_status == 1)
											{
												$open_close_response = $this->get_open_close_time($next_day_timestamp ,$tue_open_close_time);
												$next_open_time = $open_close_response['open_time'];
												$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
												$open_status = 1;
											}else
											{
												$open_status = 2;
											}
										}

										if($next_day == 'wednesday')
										{
											if($wed_full_day_close_status == 1)
											{
												$open_close_response = $this->get_open_close_time($next_day_timestamp ,$wed_open_close_time);
												$next_open_time = $open_close_response['open_time'];
												$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
												$open_status = 1;
											}else
											{
												$open_status = 2;
											}
										}

										if($next_day == 'thursday')
										{
											if($thu_full_day_close_status == 1)
											{
												$open_close_response = $this->get_open_close_time($next_day_timestamp ,$thu_open_close_time);
												$next_open_time = $open_close_response['open_time'];
												$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
												$open_status = 1;
											}else
											{
												$open_status = 2;
											}
										}

										if($next_day == 'friday')
										{
											if($fri_full_day_close_status == 1)
											{
												// echo $fri_open_close_time;
												$open_close_response = $this->get_open_close_time($next_day_timestamp ,$fri_open_close_time);
												// echo "<pre>";
												// print_r($open_close_response);
												$next_open_time = $open_close_response['open_time'];
												$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
												$open_status = 1;
											}else
											{
												$open_status = 2;
											}
										}

										if($next_day == 'saturday')
										{
											if($sat_full_day_close_status == 1)
											{
												$open_close_response = $this->get_open_close_time($next_day_timestamp ,$sat_open_close_time);
												$next_open_time = $open_close_response['open_time'];
												$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
												$open_status = 1;
											}else
											{
												$open_status = 2;
											}
										}

										if($next_day == 'sunday')
										{
											if($sun_full_day_close_status == 1)
											{
												$open_close_response = $this->get_open_close_time($next_day_timestamp ,$sun_open_close_time);
												$next_open_time = $open_close_response['open_time'];
												$return_array = array('status' => '5','next_open_time' => (string)$next_open_time);
												$open_status = 1;
											}else
											{
												$open_status = 2;
											}
										}
									}
									// echo "RETURN ARRAY SECOND CONDITION <pre>";
									// print_r($return_array);
									// die;
									return $return_array;
								}
							}
						}
					}
				}
			}	
		}

		if($proceed_further_more == 1)
		{
			# OPEN
			return array('status' => 1 , 'next_open_time' => '');
		}
	}

    # This function is used to calculate distance between two lat long (One lat long is of user and second is of restaurant)
    # calculate_distance_between_latlong Start
    public function calculate_distance_between_latlong($lat1,$lat2,$lon1,$lon2,$unit='K')
    {
    	$theta = $lon1 - $lon2;
	    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	    $dist = acos($dist);
	    $dist = rad2deg($dist);
	    $miles = $dist * 60 * 1.1515;
	    $unit = strtoupper($unit);
	    // if ($unit == "K") {
	    return number_format(($miles * 1.609344),2);
	    // }
    }
    # calculate_distance_between_latlong End

    # This function is used to make cart calculation for the registered user
    # cart_calculation_start
    public function cart_calculation($tokenData)
    {
    	$cart_data = $this->Common->getData('cart','*','products.product_status = 1 AND user_id = "'.$tokenData->id.'"',array('products'),array('products.id = cart.product_id'));
    	
    	$cart_detail = 0;
    	foreach ($cart_data as $key => $value) {
    		if($value['offer_price'] != 0)
			{
				$cart_detail += $value['offer_price'] * $value['product_quantity'];
			}else
			{
				$cart_detail += $value['price'] * $value['product_quantity'];
			}
    		// $cart_detail += $value['product_quantity'] * $value['price'];
    	}

    	$cart_var_data = $this->Common->getData('cart','cart.id AS cart_tbl_id ,cart.product_quantity,products.price,variant_types_for_products.variant_type_price','cart.user_id = "'.$tokenData->id.'" AND cart.product_id = variant_types_for_products.product_id AND variants.variant_status = 1',array('products','cart_variant','variant_types_for_products','variants'),array('products.id = cart.product_id' , 'cart.id = cart_variant.cart_id','variant_types_for_products.variant_type_id = cart_variant.variant_type_id','variants.variant_id = variant_types_for_products.variant_id'));

    	# HERE if product qty is 2 then if any variant is added for this product so its variant qty will also be 2.
    	# EX :  Chicken Fry Dum Biriyani costs 12.60 and variant jeera rice is 2 so if 
    	/*
			Chicken Fry Dum Biriyani X 1 + jeera rice = 12.60*1+2*1 = 14.6
			Chicken Fry Dum Biriyani X 2 + jeera rice = 12.60*2+2*2 = 29.20 I have matched it with live running website and it is calcualting liek this only

			So we will multiply variant price by PRODUCT QTY product_quantity
    	*/

    	foreach ($cart_var_data as $key => $value) {
    		$cart_detail += $value['variant_type_price'] * $value['product_quantity'];
    	}
    	# To get how many items are there total in this user's cart
 		$cart_lst = $this->Common->getData('cart' , 'id,rest_id' , array('user_id' => $tokenData->id));
		if(!empty($cart_lst))
		{
			$cart_quantity =  count($cart_lst);
			$rest_id = $cart_lst[0]['rest_id'];
		}else
		{
			$cart_quantity = 0;
			$rest_id = 0;
		}
    	return array('item_total' => number_format($cart_detail,2, '.', '') , 'items' => $cart_quantity , 'restaurant_id' => $rest_id);
    }
    # cart_calculation_end

    # Get wallet balance start
    # This function will get the existing wallet amount with a check of validity also.
    public function get_wallet_balance($user_id)
    {
    	# Here we are checking validity in query itself using IFELSE
    	# If valid_till != '' that means validity exists so get amount only of those whose validity is not expired hence we used UNIX_TIMESTAMP() <= valid_till
    	$result = "SELECT FORMAT((sum(COALESCE(credited, 0))-sum(COALESCE(debited, 0))),2) AS wallet_balance FROM wallet WHERE IF (valid_till != 0 , UNIX_TIMESTAMP() <= valid_till AND user_id = $user_id , user_id = $user_id) ";
	    $query = $this->db->query($result);
	    if ($query){          
	       return $query->result_array();
	    } else{           
	         return array();
	    }
    }
    # Get wallet balance end

    # Delivery promotion Start
    public function get_delivery_charge_promotion($restaurant_id , $item_total , $tokenData = '',$pickup_time = 'NA')
    {
    	# DB_promotion_level : 1 : Delivery charge 2 Restaurant 3 Product 4 Category
		# DB_auto_apply : 1 - Auto apply 2 - Not auto apply
		# DB_allow_multiple_time_use  1 - YES 2 - NO
		
		# We have to check if any order is being made for future date then is there any promo available which has validity and fall in the same date.
    	# Ex today date is aug 11 and customer makes an order on 11 aug for 15 aug and there is promo ABC which has a validity of 14 aug to 16 aug so at this point ABC promo muse come after checking all other validations
    	# That is why we have added extra param as $pickup_time. In case of order now $pickup_time will be passed as NA and in rest two case it will be passed with valid value. We also need not to check it in case of order now.

		# Excluding AND restaurant_id from where condition 
		$delivery_promotion_all = $this->Common->getData('promotions','*','promotion_mode_status = 1 AND (promo_status = 1 AND is_auto_apply = 1 AND level_id = 1)');
		if(!empty($delivery_promotion_all))
		{
			foreach ($delivery_promotion_all as $key => $delivery_promotion) 
			{
				# time() > valid_from AND time() < valid_till
				if($item_total >= $delivery_promotion['min_value']) # This is the only mendatory check
				{
					$is_forever = $delivery_promotion['valid_from'] == 0 ? 1:2; # 1 : yes means it is a forever promo 2 : It has validity
					$allow_multiple_time_use = $delivery_promotion['allow_multiple_time_use'] == 1 ? 1:2; # 1 : It can be used mutiple time by a single user 2 : One user can use it only once
					# max_allowed_times : How many times this can be used
					# If == 0 (That means admin has not given any limit) Else IT has a limit
					$max_allowed_times = $delivery_promotion['max_allowed_times'] == 0 ? 1:$delivery_promotion['max_allowed_times']; # 1 : No max allowed value given ELSE the value given by admin
					$promo_used_times = $delivery_promotion['promo_used_times'];
					$max_discount = $delivery_promotion['max_discount'] == 0 ? 1:$delivery_promotion['max_discount']; # 1 :  No max discount is given

					$is_applicable = false;

					if($delivery_promotion['if_promo_for_all_rest'] == 1 )
					{
						$is_applicable = true;
					}else
					{
						if($delivery_promotion['restaurant_id'] == $restaurant_id)
						{
							$is_applicable = true;
						}else
			    		{
			    			$is_applicable = false;
			    		}
					}

					if($is_applicable)
					{
						if($max_allowed_times != 1) # 1 : No max allowed value given
						{
							if($promo_used_times < $max_allowed_times)
							{
								$is_applicable = true;
							}else
							{
								$is_applicable = false;
							}
						}else
						{
							$is_applicable = true;
						}
					}

					if($is_applicable)
					{
						if($is_forever == 2) # NO not a forever promotion
						{
							// if((time() > $delivery_promotion['valid_from']) && (time() < $delivery_promotion['valid_till']))
							// {
							// 	$is_applicable = true;
							// }else
							// {
							// 	$is_applicable = false;
							// }
							if($pickup_time != "NA" && $pickup_time != 'na')
							{
								# THAT MEANS Its NOT an ORDER NOW CASE so check with pickup time
								if(($pickup_time > $delivery_promotion['valid_from']) && ($pickup_time < $delivery_promotion['valid_till']))
								{
									$is_applicable = true;
								}else
								{
									$is_applicable = false;		
								}
							}else # ORDER NOW
							{
								if((time() > $delivery_promotion['valid_from']) && (time() < $delivery_promotion['valid_till']))
								{
									$is_applicable = true;
								}else
								{
									$is_applicable = false;
								}
							}
						}else  # Yes a forever promotion
						{
							$is_applicable = true;
						}
					}
					if($is_applicable)
					{
						if($allow_multiple_time_use == 1) # 1 : It can be used mutiple time by a single user
						{
							$is_applicable = true;
						}else # One user can use it only once
						{
							# That is one user can use this only once. So check whether this promo is used by this customer earlier?
							$usr_used_pc = $this->Common->getData('used_promotions','id',array('promotion_id' =>$delivery_promotion['id'] , 'user_id' => $tokenData->id));
							if(count($usr_used_pc) > 0)
							{
								$is_applicable = false;
							}else
							{
								$is_applicable = true;
							}
						}
					}
					// if($is_applicable)
					// {
					// 	if($delivery_promotion['promo_type'] == 1) #1 : FLAT 2 : PERCENT
					// 	{
					// 		$disc_val = $delivery_promotion['discount_value'];
					// 		$disc_val = number_format($disc_val,2, '.', '');
					// 	}else
					// 	{
					// 		$disc_val = ($item_total * $delivery_promotion['discount_value']) / 100;
					// 		$disc_val = number_format($disc_val,2, '.', '');
					// 	}

					// 	if($max_discount != 1)
					// 	{
					// 		if($disc_val > $max_discount)
					// 		{
					// 			# That means it can not be applied because its offer value is going bigger than max discount value
					// 			$is_applicable = false;
					// 		}else
					// 		{
					// 			$is_applicable = true;
					// 		}
					// 	}else
					// 	{
					// 		$is_applicable = true;
					// 	}
					// }
				}else
				{
					$is_applicable = false;
				}

				if($is_applicable)
				{
					$promo_response[] = $delivery_promotion;
				}else
				{
					$promo_response[] =  array();
				}
			}
		}else
		{
			$promo_response[] =  array();
		}
		if(count($promo_response) > 0 ) # Multiple promo available as per multiple categories
		{
			# That mens we have promotion on two categories so we need to find which one has high value of discount
			$check_high_promo = array();
			foreach ($promo_response as $key => $del_value)
			{
				if(!empty($del_value))
				{
					if($del_value['promo_type'] == 1) #1 : FLAT 2 : PERCENT
					{
						// $disc_val = $current_item_total - $catp_value['discount_value'];
						$disc_val = $del_value['discount_value'];
						$disc_val = number_format($disc_val,2, '.', '');
						$check_high_promo['high_promo'][$key] = array('promo_id' => $del_value['id'] , 'disc_val' => $disc_val);
					}else
					{
						$disc_val = ($item_total  * $del_value['discount_value']) / 100;
						// $disc_val = $current_item_total - $disc_val;
						$disc_val = number_format($disc_val,2, '.', '');
						$check_high_promo['high_promo'][$key] = array('promo_id' => $del_value['id'] , 'disc_val' => $disc_val);
					}
				}
			}
			if(isset($check_high_promo['high_promo']))
			{
				$max_dis = 0;
				foreach ($check_high_promo['high_promo'] as $key => $cvalue) 
				{
					if($cvalue['disc_val'] > $max_dis)
					{
						$max_dis_arr = array('id' => $cvalue['promo_id'] , 'disc_val' => $cvalue['disc_val']);
						$max_dis = $cvalue['disc_val'];
					}
				}
				$promocode_details = $this->Common->getData('promotions','*',array('id' => $max_dis_arr['id'], 'promotion_mode_status' => 1));
				return $promocode_details[0];
			}else
			{
				$x = new stdClass();
				return $x;
			}
		}else
		{
			$x = new stdClass();
			return $x;
		}
    }
    # Delivery promotion End

    # This function is used to get all the available business categories
    public function business_categories_get()
    {
        try{

        	$business = $this->Common->getData('merchant_categories','*');

            $data['status']		=200;
            $data['message']	=$this->lang->line('success');
            $data['data']		=$business; 

            $this->response($data, $data['status']);

        } catch (\Exception $e) {

            #make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # This is a common function that is used to check whether any variant for the given product id is present
    public function check_if_variant_available($product_id)
    {
    	return $this->Common->getData('variant_types_for_products','variant_type_product_id','variant_types_for_products.product_id = "'.$product_id.'"');
    }

    public function getOrderCashbackValue($sub_total)
    {
    	$cashback = $this->Common->getData('settings','*','name = "order_cashback"');
    	$cashback_type = $cashback[0]['type'];
    	$cashback_value = $cashback[0]['value'];
    	#  1 : Flat 2 percent
    	if($cashback_type == 1)
    	{
    		return $cashback_value;
    	}else
    	{
    		$ab = ($sub_total * $cashback_value) / 100;
    		return $ab;
    	}
    }

    public function lalamove_place_order_get()
    {
    	try{

        	$res = $this->order_lalamove();

        	$data['status']		=200;
            $data['message']	=$this->lang->line('success');
            $data['data']		=$res; 

            $this->response($data, $data['status']);

        } catch (\Exception $e) {

            #make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }


	public function lalamove_quotation_generate($body)
	{
		$key = 'da10edb6ed6936c7baccba922c308776'; #SANDBOX
        $secret = 'b3aVjAP/dvbi4vxy0PfeaBpakpPMBtlqDPgwZJJ6tQKlHoyGGm+2EFCf9AK7sQfR'; #SANDBOX
		// $key = 'be9812303d424e11811afec2dd2e627f';
	 //    $secret = 'MC8CAQACBQC5IZplAgMBAAECBQC4PkhBAgMAzVECAwDm1QIDAIwhAgMAs90C'; 
	    $time = time() * 1000;
	    
	    $method = 'POST';
	    $path = '/v2/quotations';
	    $region = 'SG';
	    $order_body = $body;
	    $body = json_encode($body , true);
	    $_encryptBody = '';
	    $_encryptBody = "{$time}\r\n{$method}\r\n/v2/quotations\r\n\r\n{$body}";
	    //$_encryptBody = $time."\r\n".$method."\r\n".$path."\r\n\r\n".json_encode((object)$body);
	    
	    $signature = hash_hmac("sha256", $_encryptBody, $secret);
	    $token = $key.':'.$time.':'.$signature;
	    // echo "<pre> BODY PRINT";
	    // print_r($body);
	   	$curl_response = $this->initiate_curlfn($path, $body ,$token ,$region);
	   	// echo "<pre> 9406 ";
	   	// print_r($curl_response);
	    $quotation_response = json_decode($curl_response);

	    if(!empty($quotation_response))
	    {
	    	if(isSet($quotation_response->message))
	    	{
	    		# That means we have some error
	    		// echo "LALAMOVE ORder placing failed due to ".$quotation_response->message;
	    		return array('lalamove_order_id' => '' , 'lalamove_order_amount' => '','failed_reason' => $quotation_response->message , 'lalamove_track_link' => '');
	    		exit();
	    	}else
	    	{
		    	$amount = $quotation_response->totalFee;
		    	$currency = $quotation_response->totalFeeCurrency;
	    		return array('lalamove_order_id' => '' , 'lalamove_order_amount' => $amount,'failed_reason' => '' , 'lalamove_track_link' => '');
	    	}
	    }
	}

	public function lalamove_quotation_and_place_order($body)
	{
		$key = 'da10edb6ed6936c7baccba922c308776'; #SANDBOX
        $secret = 'b3aVjAP/dvbi4vxy0PfeaBpakpPMBtlqDPgwZJJ6tQKlHoyGGm+2EFCf9AK7sQfR'; #SANDBOX
		// $key = 'be9812303d424e11811afec2dd2e627f';
		// $secret = 'MC8CAQACBQC5IZplAgMBAAECBQC4PkhBAgMAzVECAwDm1QIDAIwhAgMAs90C'; 
		$time = time() * 1000;

		$method = 'POST';
		$path = '/v2/quotations';
		$region = 'SG';
		$order_body = $body;
		$body = json_encode($body , true);
		$_encryptBody = '';
		$_encryptBody = "{$time}\r\n{$method}\r\n/v2/quotations\r\n\r\n{$body}";
		//$_encryptBody = $time."\r\n".$method."\r\n".$path."\r\n\r\n".json_encode((object)$body);

		$signature = hash_hmac("sha256", $_encryptBody, $secret);
		$token = $key.':'.$time.':'.$signature;
		// echo "<pre> BODY PRINT";
		// print_r($body);
			$curl_response = $this->initiate_curlfn($path, $body ,$token ,$region);
			// echo "<pre> 9406 ";
			// print_r($curl_response);
		$quotation_response = json_decode($curl_response);

		if(!empty($quotation_response))
		{
			if(isSet($quotation_response->message))
			{
				# That means we have some error
				// echo "LALAMOVE ORder placing failed due to ".$quotation_response->message;
				return array('lalamove_order_id' => '' , 'lalamove_order_amount' => '','failed_reason' => $quotation_response->message , 'lalamove_track_link' => '');
				exit();
			}else
			{
		    	$amount = $quotation_response->totalFee;
		    	$currency = $quotation_response->totalFeeCurrency;
		    	# Now place Lalamove Order
		    	$path = '/v2/orders';
			    $region = 'SG';

			    # Here we need to add quotedTotalFee array
			    // echo "TO ORDER ";
			    $order_body['quotedTotalFee'] = array("amount" => $amount , "currency" => $currency);
			    $order_body['sms'] = false;
			    $order_body['pod'] = false;
			    $order_body['fleetOption'] = "FLEET_ALL";
			    // print_r($order_body);
			    $order_body = json_encode($order_body);
			    // echo "JSON PRINTING";
			    // print_r($order_body);

			    // die;
			    $_encryptBody = '';
			    $_encryptBody = "{$time}\r\n{$method}\r\n/v2/orders\r\n\r\n{$order_body}";
			    //$_encryptBody = $time."\r\n".$method."\r\n".$path."\r\n\r\n".json_encode((object)$body);
			    
			    $signature = hash_hmac("sha256", $_encryptBody, $secret);
			    // echo $time,PHP_EOL;
			    // echo $signature,PHP_EOL;
			    
			    $token = $key.':'.$time.':'.$signature;
			    $curl_response = $this->initiate_curlfn($path, $order_body ,$token ,$region);
			    // echo "<pre> 9433 ";
			    // print_r($curl_response);
			    // die;

			    $order_response = json_decode($curl_response);
			    // echo "<pre> 9431 line";
			    // print_r($order_response);
			    // die;
			    if(!empty($order_response))
			    {
			    	if(isSet($order_response->message))
			    	{
			    		# SOMETHING FAILED
			    		return array('lalamove_order_id' => '' , 'lalamove_order_amount' => $amount,'failed_reason' => $order_response->message , 'lalamove_track_link' => '');
			    	}else
			    	{
			    		$lalamove_order_id = $order_response->orderRef;
			    		# Also we need shareLink(track link)
			    		$order_detail = $this->lalamove_order_deails($lalamove_order_id);
			    		// echo "<pre> racklinkkk";
			    		// print_r($order_detail);die;
			    		if($order_detail == null)
			    		{
			    			return array('lalamove_order_id' => $lalamove_order_id , 'lalamove_order_amount' => $amount,'failed_reason' => '' , 'lalamove_track_link' => '');
			    		}else
			    		{
			    			$track_link = $order_detail->shareLink;	
			    			return array('lalamove_order_id' => $lalamove_order_id , 'lalamove_order_amount' => $amount,'failed_reason' => '' , 'lalamove_track_link' => $track_link);
			    		}
			    	}
			    }
			}
		}
	}

    public function lalamove_driver_details_get()
    {
    	try{

    		$lalamove_order_id = !empty($_GET['lalamove_order_id'])?$this->db->escape_str($_GET['lalamove_order_id']):'';
    		$lalamove_driver_id = !empty($_GET['lalamove_driver_id'])?$this->db->escape_str($_GET['lalamove_driver_id']):'';
    		// $tokenData = $this->verify_request();

    		// if($tokenData === false){
      //           $status = parent::HTTP_UNAUTHORIZED;
      //           $data['status']	 = $status;
      //           $data['message']	= $this->lang->line('unauthorized_access');
      //       }else 
            if($lalamove_order_id ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('lalamove_order_id_missing');
			    $data['data']		=array();
			}else if($lalamove_driver_id ==''){
			    $data['status']		=201;
			    $data['message']	=$this->lang->line('lalamove_driver_id_missing');
			    $data['data']		=array();
			}else
			{	
				$key = "be9812303d424e11811afec2dd2e627f";
			    $secret = "MC8CAQACBQC5IZplAgMBAAECBQC4PkhBAgMAzVECAwDm1QIDAIwhAgMAs90C"; 
			    $time = time() * 1000;
			    
			    $method = 'GET';
			    $path = '/v2/orders/'.$lalamove_order_id.'/drivers/'.$lalamove_driver_id;
			    $region = 'SG';
			    $body = array();
			    $body = json_encode($body , true);
	        	// $_encryptBody = "{$time}\r\n{$method}\r\n/v2/orders/".$lalamove_order_id;
	        	$_encryptBody = "{$time}\r\n{$method}\r\n/v2/orders/".$lalamove_order_id."/drivers/".$lalamove_driver_id."\r\n\r\n{$body}";
			    
			    $signature = hash_hmac("sha256", $_encryptBody, $secret);
			    
			    $token = $key.':'.$time.':'.$signature;
			    $curl = curl_init();
			    curl_setopt_array($curl, array(
			      // CURLOPT_URL => 'https://sandbox-rest.lalamove.com'.$path,
			      // CURLOPT_URL => 'https://rest.sandbox.lalamove.com'.$path,
			      CURLOPT_URL => 'https://rest.lalamove.com'.$path,
			      CURLOPT_RETURNTRANSFER => true,
			      CURLOPT_ENCODING => '',
			      CURLOPT_MAXREDIRS => 10,
			      CURLOPT_TIMEOUT => 3,
			      CURLOPT_FOLLOWLOCATION => true,
			      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			      CURLOPT_CUSTOMREQUEST => 'GET',
			      CURLOPT_POSTFIELDS => $body,
			      CURLOPT_HTTPHEADER => array(
			        "Content-type: application/json; charset=utf-8",
			        "Authorization: hmac ".$token,
			        "Accept: application/json",
			        "X-LLM-Country: ".$region
			      ),
			    ));
			    
			    $response = curl_exec($curl);
			    curl_close($curl);
			    // echo "<pre>";
			    print_r($response);

			    $resposta = json_decode($response); 
			    // echo "<pre>";
			    // print_r($resposta);die;

	        	$data['status']		=200;
	            $data['message']	=$this->lang->line('success');
	            $data['data']		=$resposta; 
			}

            $this->response($data, $data['status']);

        } catch (\Exception $e) {

            #make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # This function is used to Empty user cart completely
    public function empty_cart_post()
    {
    	try{
	    	$tokenData = $this->verify_request();
	    	
            if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else
            {
            	$this->Common->deleteData('cart' , array('user_id' => $tokenData->id));
        		
        		$data['status']		=200;
                $data['message']	=$this->lang->line('success');
                $data['data']		=array();
        		
            }
            # REST_Controller provide this method to send responses
            $this->response($data, $data['status']);
    	} catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # This funcion is used to send sms for OTP on users mobile number
    public function send_otp_on_mobile($otp_code , $destination_number)
    {
		
		/* New twilio for CURL */
		$id = "xxxxx";
		$token = "xxxxxx";
		$url = "https://api.twilio.com/2010-04-01/Accounts/$id/SMS/Messages";
		$from = "+00000000";
		$to = "+00000000"; // twilio trial verified number
		$body = "using twilio rest api from Fedrick";
		$data = array (
		    'From' => $from,
		    'To' => $to,
		    'Body' => $body,
		);
		$post = http_build_query($data);
		$x = curl_init($url );
		curl_setopt($x, CURLOPT_POST, true);
		curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
		curl_setopt($x, CURLOPT_POSTFIELDS, $post);
		$y = curl_exec($x);
		curl_close($x);
		var_dump($post);
		var_dump($y);
    }

    /* This function is used to get restaurant data as per restaurant Id */
    public function get_full_restro_detail_get()
    {
    	try{
    		$tokenData = $this->verify_request();
    		$restaurant_id = !empty($_GET['restaurant_id'])?$this->db->escape_str($_GET['restaurant_id']):'';
    		$lat = !empty($_GET['lat'])?$this->db->escape_str($_GET['lat']):'';
    		$lng = !empty($_GET['lng'])?$this->db->escape_str($_GET['lng']):'';
    		$is_valid = 0;
    		$date_timestamp = !empty($_GET['date_timestamp'])?$this->db->escape_str($_GET['date_timestamp']):'';

    		if($date_timestamp == '')
        	{
        		$data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
        	}else if($tokenData === false)
			{
				$tokenData = ''; # Pass empty string if token is false
				# This fn can be called in both cases (Logged in and guest) so we will use token in fn_getrestaurant_data function to check wishlist status
			    # If token is not present that it may be a guest user so in such case we need lat long
				if($lat == ''){
			        $data['status']		=201;
			        $data['message']	=$this->lang->line('latitude_missing');
			        $data['data']		=array();
				}else if($lng == ''){
			        $data['status']		=201;
			        $data['message']	=$this->lang->line('longitude_missing');
			        $data['data']		=array();
				}
				else
				{
					$is_valid = 1;
				}
			}else
			{
				$is_valid = 1;
				$latlong = $this->Common->getData('users','latitude,longitude','id = "'.$tokenData->id.'"');
				$lat = $latlong[0]['latitude'];
				$lng = $latlong[0]['longitude'];
			}
			if($is_valid == 1)
			{
	    		if($restaurant_id ==''){
	                $data['status']		=201;
	                $data['message']	=$this->lang->line('rest_id_missing');
	                $data['data']		=array();
	            }else
	            {
	            	$rest_one = $this->Common->getData('restaurants','restaurants.*,restaurants.id as restaurant_id,users.latitude,users.longitude','restaurants.id = "'.$restaurant_id.'"',array('users'),array('users.id = restaurants.admin_id'));
	            	if(count($rest_one) > 0)
	            	{
		            	$latlong = $this->Common->getData('users','latitude,longitude','id = "'.$tokenData->id.'"');
			        	$lat = $latlong[0]['latitude'];
			        	$lng = $latlong[0]['longitude'];
			        	$hours = $this->delivery_preparation_time();
			    		$response = $this->get_restaurant_data($rest_one,$hours,$tokenData,$lat,$lng,$date_timestamp);
		            	
			    		$del_rating = $this->Common->getData('ratings','AVG(given_rating) as del_rating','to_type = 1 AND to_id = "'.$restaurant_id.'"');
		            	if(count($del_rating) > 0 && $del_rating[0]['deli_rating'] != '')
		            	{
		            		$response[0]['del_rating'] = $del_rating[0]['del_rating'];
		            	}else
		            	{
		            		$response[0]['del_rating'] = 0;
		            	}
		            	$query_rating_cnt = "SELECT count(id) as del_rating_count FROM `ratings` WHERE to_id = ".$restaurant_id." and to_type = 3";
		            	$rating_cnt = $this->Common->custom_query($query_rating_cnt,'get');
		            	$deli_rating_cnt = $rating_cnt[0]['del_rating_count'];

						$query_rating_cnt = "SELECT count(id) as dinein_rating_count FROM `ratings` WHERE to_id = ".$restaurant_id." and to_type = 1";
						$rating_cnt = $this->Common->custom_query($query_rating_cnt,'get');
		            	$dinin_rating_cnt = $rating_cnt[0]['dinein_rating_count'];
		            	$response[0]['deli_rating_cnt'] = $deli_rating_cnt;
		            	$response[0]['dinin_rating_cnt'] = $dinin_rating_cnt;

		            	$data['status']		=200;
			            $data['message']	=$this->lang->line('success');
			            $data['data']		=$response[0]; 
	            	}else
	            	{
	            		$data['status']		=201;
			            $data['message']	=$this->lang->line('no_data_found');
			            $data['data']		=$response; 
	            	}
	            }
			}
    		$this->response($data, $data['status']);

    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    function testNotification_get($token){

    	$amount = '6';
    	$order_number_id = 'KEXXXXXXXXXXXX';
        $notification_data_fields = array(
            'message' => 'S$'.$amount.' added to your wallet',
            'title' => NOTIFICATION_MONEY_ADDED,
            'notification_type' => 'WALLET_RECHARGE'
        );

        # Below token provided by sanjaysir and tested
        // $token = 'c30fb0b2cab6360d22c277fa619dc7754369d50d89be46b9af6cfaf1f9284bd4'; For Customer
        // $token = '689bbc000034cd7d0d9112a8cfadd07437822334b3b3b6ffa09c632367b3ea65'; For Merchant
        $token = 'bc592ef18a1c6be096041e389de83e86f9419127a7c6299a851efeb6f714ab37';
        // sendPushNotification($tokens, $notification_data_fields);
        $res = sendPushNotification($token, $notification_data_fields,IOS_BUNDLE_ID_CUSTOMER);
        echo "Test function <br>".$res;
        die;
    }

    # This function will be called from customer side app so we need to take restaurant id from mobile team
    # This function is used to get the booking timeslot
    public function get_dynamic_timeslots_get()
    {
    	try
        {
	    	$rest_id = !empty($_GET['rest_id'])?($_GET['rest_id']):'';
	    	$date_timestamp = !empty($_GET['date_timestamp'])?($_GET['date_timestamp']):''; 
	    	
            if($rest_id == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }else if($date_timestamp == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
            }else
            {
            	date_default_timezone_set('Asia/Singapore');
	    		// echo "SINGAPORE CURRENT TIME IS ".time();
	    		$current_time = time();
            	# First of all the timestamp that we get from mobile team should be as per the Singaporet timezone. Because we have to validate that this timestamp exists in restaurant offline table? Also we are adding 8 hours to the DB entry as entry in DB is as per UTC
		    	# From rest_offline table we will only check for offline_tag is 2 or 3 because its a complete day close 1 - Hour 2 - Day 3 - Multiple days
		    	$offline_data = $this->Common->getData('rest_offline','*','rest_id = "'.$rest_id.'"');
		    	if(count($offline_data) > 0)
		    	{
		    		$proceed_further = 0; # 0 No 1 Yes
		    		$offline_from = $offline_data[0]['offline_from'];
		    		$offline_to = $offline_data[0]['offline_to'];
		    		if($offline_data[0]['offline_tag'] != 1)
		    		{
		    			# ######## FOR INDIA UNCOMMENT BELOW ########
						// $offline_from_val = $offline_from - (5*60*60); # DEDUCT 5 HOURS
						// $offline_from = $offline_from_val - (30 *60 ); # AND THEN 30 MINUTES

						// $offline_to_val = $offline_to - (5*60*60); # DEDUCT 5 HOURS
						// $offline_to = $offline_to_val - (30 *60 ); # AND THEN 30 MINUTES
						# ######## FOR INDIA ########

		    			# Why we are subtracting because UTC and singapore having 8  hours difference and the localtime passed as timestamp will be 8 hours less than the UTC so either dedcut 8 hours from UTC or add 8 hours to the given timestamp
						# ######## FOR SINGAPORE UNCOMMENT BELOW ######### 
						$offline_from = $offline_from - (8*60*60); # DEDUCT 8 HOURS
						$offline_to = $offline_to - (8*60*60); # DEDUCT 8 HOURS
						# ######## FOR SINGAPORE ######### 
			    		if($date_timestamp >= $offline_from  && $date_timestamp <= $offline_to)
			    		{
			    			$data['status']		=201;
					        // $data['message']	=$this->lang->line('no_data_found');
					        $data['message']	="Restaurant is set to Offline and not receiving order on this day";
					        $data['data']		=array();
			    		}else
			    		{
			    			$proceed_further = 1;
			    		}
		    		}else
		    		{
		    			$proceed_further = 1;
		    		}
		    	}else # OPEN
	    		{
    			    $proceed_further = 1;
	    		}
	    		// echo "<br>proceed_further ".$proceed_further;
	    		if($proceed_further == 1)
		    	{
		    		# Get delivery time of the resturant
			    	$rest_basic_details = $this->Common->getData('restaurants','*','id = "'.$rest_id.'"');

			    	$settings_data = $this->Common->getData('settings','value','name = "basic_delivery_time" OR name = "basic_preparation_time" OR name = "window_time"');
			    	$basic_delivery_time = $time[0]['value'];
			    	$basic_preparation_time = $time[1]['value'];

			    	if($rest_basic_details[0]['delivery_time'] != '' && $rest_basic_details[0]['follow_global_del_time'] == 0)
			    	{
			    		# That means restaurant has its own set delivery time
			    		$delivery_time = $rest_basic_details[0]['delivery_time'];
			    	}else
			    	{
			    		$delivery_time = $settings_data[0]['value'];
			    	}

			    	# Get preparation time of the resturant
			    	if($rest_basic_details[0]['preparation_time'] != '' && $rest_basic_details[0]['preparation_time'] != 0)
			    	{
			    		# That means restaurant has its own set preparation_time
			    		$preparation_time = $rest_basic_details[0]['preparation_time'];
			    	}else
			    	{
			    		$preparation_time = $settings_data[1]['value'];
			    	}

			    	# BUFFER TIME = DELIVERY TIME + PREPARTION TIME
			    	$buffer_time = $delivery_time + $preparation_time;
			    	# DYNAMIC WINDOW TIME
			    	$dynamic_window_time = $settings_data[2]['value'];

			    	# Get Time mode
			    	if($rest_basic_details[0]['time_mode'] == 1) # 1 - for every day, 2 - for Specific day
			    	{
			    		// echo "QQQQ";
			    		$open_time = $rest_basic_details[0]['open_time'];
			    		$close_time = $rest_basic_details[0]['close_time'];
			    		if($rest_basic_details[0]['break_start_time'] != '')
			    		{
				    		$break_start_time = $rest_basic_details[0]['break_start_time'];
				    		$break_end_time = $rest_basic_details[0]['break_end_time'];
			    		}else
			    		{
			    			$break_start_time = '';
			    			$break_end_time = '';
			    		}
			    		$proceed_further_more = 1;
			    	}else if($rest_basic_details[0]['time_mode'] == 2)
			    	{
			    		// echo "WWWWWWWw";
			    		# Get what is the Day of the passed date_timestamp
			    		# COMMENT THIS TIMEZONE VALUE
			    		// date_default_timezone_set('Asia/Kolkata');
			    		// date_default_timezone_set('Asia/Singapore');
			    		// echo "SINGAPORE CURRENT TIME IS ".time();
			    		// $current_time = time();
			    		$weekday = date('l', $date_timestamp);
			    		$weekday = strtolower($weekday);
			    		$rest_time_daywise = $this->Common->getData('rest_time_daywise','*','rest_id = "'.$rest_id.'"');

			    		if(count($rest_time_daywise) > 0)
			    		{
				    		if($weekday == 'monday'){
				    			$full_day_close_status = $rest_time_daywise[0]['mon_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['mon_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['mon_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['mon_break_start_end_time'];
				    		}elseif($weekday == 'tuesday'){
				    			$full_day_close_status = $rest_time_daywise[0]['tue_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['tue_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['tue_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['tue_break_start_end_time'];
				    		}elseif($weekday == 'wednesday'){
				    			$full_day_close_status = $rest_time_daywise[0]['wed_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['wed_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['wed_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['wed_break_start_end_time'];
				    		}elseif($weekday == 'thursday'){
				    			$full_day_close_status = $rest_time_daywise[0]['thu_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['thu_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['thu_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['thu_break_start_end_time'];
				    		}elseif($weekday == 'friday'){
				    			$full_day_close_status = $rest_time_daywise[0]['fri_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['fri_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['fri_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['fri_break_start_end_time'];
				    		}elseif($weekday == 'saturday'){
				    			$full_day_close_status = $rest_time_daywise[0]['sat_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['sat_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['sat_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['sat_break_start_end_time'];
				    		}elseif($weekday == 'sunday'){
				    			$full_day_close_status = $rest_time_daywise[0]['sun_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['sun_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['sun_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['sun_break_start_end_time'];
				    		}

				    		if($full_day_close_status == 2) # 2- on this day restaurant will be closed, 1 - restaurant will be opend
				    		{
				    			// echo "12121211";
				    			$proceed_further_more = 0;
				    			# Restaurant is closed on this day
				    			// $data['message']	="Restaurant is closed on (".ucfirst($weekday).")";
				    			$data['status']		=201;
						        // $data['message']	=$this->lang->line('no_data_found');
						        $data['message']	="Restaurant is closed on ".ucfirst($weekday)."";
						        $data['data']		=array();
						        // exit();
				    		}else
				    		{
				    			// echo "33333";
				    			$proceed_further_more = 1;
				    			$open_close_time = explode("-",$open_close_time);
				    			$open_time = $open_close_time[0];
								$close_time = $open_close_time[1];

								if($brk_status == 1) # 2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time) and 0 when restaurant is closed in full day
								{
									$brk_start_brk_end = explode("-",$brk_start_brk_end);
					    			$break_start_time = $brk_start_brk_end[0];
									$break_end_time = $brk_start_brk_end[1];
								}else
								{
									# NO BREAK HOURS
									$break_start_time = '';
									$break_end_time = '';
								}
				    		}
			    		}
			    	} # END OF TIME MODE
		    		// echo "<br>proceed_further_more".$proceed_further_more;
		    		if($proceed_further_more == 1)
		    		{
		    			# REMOVE THIS TIMEZONE SET BEFORE GO ON LIVE
						// date_default_timezone_set('Asia/Kolkata');
						// $dynamic_window_time = 15;
			   			// echo "<br> OPEN TIME IS ".$open_time;
						// echo "<br> CLOSE TIME IS ".$close_time;
						// echo "<br> WINDOW IS ".$dynamic_window_time;
						// echo "<br> DELIVERY TIME IS ".$delivery_time;
						// echo "<br> PREPARATION TIME IS ".$preparation_time;
						// echo "<br>BREAK START AT ".$break_start_time;
						// echo "<br>BREAK ENDS AT ".$break_end_time;
						// echo "<br>BUFFER TIME ".$buffer_time;

			    		$exp = explode(":",$open_time);
						$open_time_hrs = $exp[0];
						$open_time_min = $exp[1];
						$slot_to_timestamp = '';
						$last_slot_timestamp = $dynamic_window_time + $delivery_time ;
						$slot_to = '00:00';
						if($break_start_time != '')
						{
						    $close_time_exp = explode(":",$break_start_time);
						    $close_time_hrs = $close_time_exp[0];
						    $close_time_mins = $close_time_exp[1];
						    $close_time_timsetamp_fresh = strtotime("+".$close_time_hrs." hours +".$close_time_mins." minutes",$date_timestamp);
						    $close_time_timsetamp = strtotime("+".$last_slot_timestamp." minutes",$close_time_timsetamp_fresh);
						    $final_close_time_loop = date('H:i', $close_time_timsetamp);
						    $final_close_time_cond = strtotime("+".$delivery_time." minutes",$close_time_timsetamp_fresh);
						    $final_close_time_cond = date('H:i', $final_close_time_cond);
						    $flag = 0;
						    for ($i=0; $slot_to < $break_start_time ; $i++) 
						    {
						        if($slot_to_timestamp == '') # FIRST SLOT
						        {
						            $slot_from_timestamp = strtotime("+".$open_time_hrs." hours +".$open_time_min." minutes",$date_timestamp);
						            $slot_from_timestamp = strtotime("+".$buffer_time." minutes",$slot_from_timestamp);
						        }else
						        {
						            $slot_exp = explode(":",$slot_to);
						            $slot_to_hrs = $slot_exp[0];
						            $slot_to_mins = $slot_exp[1];
						            $slot_from_timestamp = strtotime("+".$slot_to_hrs." hours +".$slot_to_mins." minutes",$date_timestamp);
						        }
						        $slot_from = date('H:i', $slot_from_timestamp);
						        $slot_to_timestamp = strtotime("+".$dynamic_window_time." minutes",$slot_from_timestamp);
						        $slot_to = date('H:i', $slot_to_timestamp);
						        if($slot_from < $break_start_time && $slot_to <= $break_start_time)
						        {
						            // echo "<br>SEND TO ARRAY ".$slot_from." - ".$slot_to."<br>";
						            // $slot[$i] = $slot_from."-".$slot_to;
						            // $flag++;
						            if((count($offline_data) > 0) && ($offline_data[0]['offline_tag'] == 1))
					            	{
					            		// echo "STEP 1<hr>";
					            		// echo $slot_from."-".$slot_to;
					            		// echo "<br>slot_from_timestamp".$slot_from_timestamp;
					            		// echo "<br>offline_from".$offline_data[0]['offline_from'];
					            		// echo "<br>slot_to_timestamp".$slot_to_timestamp;
					            		// echo "<br>offline_to".$offline_data[0]['offline_to'];
					            		// echo "<hr>";
					            		// if($slot_from_timestamp >= $offline_data[0]['offline_from'] && $slot_to_timestamp <= $offline_data[0]['offline_to'] )
					            		// if($offline_data[0]['offline_from'] >= $slot_from_timestamp && $offline_data[0]['offline_to'] >= $slot_to_timestamp )
					            		if(($slot_from_timestamp > $offline_data[0]['offline_from'] && $slot_from_timestamp < $offline_data[0]['offline_to']) || ($slot_to_timestamp > $offline_data[0]['offline_from'] && $slot_to_timestamp < $offline_data[0]['offline_to']))
					            		{
					            			// echo "<br> STEP 1 DO NOT SEND THIS ".$slot_from."-".$slot_to;
					            		}else
					            		{
					            			// echo "<br>current_time IS ".$current_time;
					            			// echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
					            			// echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
					            			# Do not pass the timestamp that are already passed
					            			if($current_time > $slot_from_timestamp)
					            			{
					            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
					            			}else
					            			{
						            			$slot[$flag] = $slot_from."-".$slot_to;		
						            			$flag++;
					            			}
					            		}
					            	}else
					            	{
					            		// echo "<br>current_time IS ".$current_time;
				            			// echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
				            			// echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
					            		# Do not pass the timestamp that are already passed
				            			if($current_time > $slot_from_timestamp)
				            			{
				            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
				            			}else
				            			{
					            			$slot[$flag] = $slot_from."-".$slot_to;		
					            			$flag++;
				            			}
					            	}
						        }else
						        {
						            // echo "<br>DO NOT SEND TO ARRAY ".$slot_from." - ".$slot_to."<br>";
						        }
						    }

						    # AFTER BREAK

						    $slot_to_timestamp = '';
						    $exp = explode(":",$break_end_time);
						    $open_time_hrs = $exp[0];
						    $open_time_min = $exp[1];
						    $open_time_hrs.":".$open_time_min;
						    $close_time_exp = explode(":",$close_time);
						    $close_time_hrs = $close_time_exp[0];
						    $close_time_mins = $close_time_exp[1];
						    $slot_to = '00:00';
						    $prev_slot_to = $slot_to;
						    
						    // $key = $flag;
						    for ($j=$flag; $slot_to < $close_time; $j++)
						    // for ($j=$flag; $j < 35; $j++) 
						    {
						        $prev_slot_to = $slot_to;
						        if($slot_to_timestamp == '') # FIRST SLOT
						        {
						            $slot_from_timestamp = strtotime("+".$open_time_hrs." hours +".$open_time_min." minutes",$date_timestamp);
						            $slot_from_timestamp = strtotime("+".$buffer_time." minutes",$slot_from_timestamp);
						        }else
						        {
						            $slot_exp = explode(":",$slot_to);
						            $slot_to_hrs = $slot_exp[0];
						            $slot_to_mins = $slot_exp[1];
						            $slot_from_timestamp = strtotime("+".$slot_to_hrs." hours +".$slot_to_mins." minutes",$date_timestamp);
						        }
						        $slot_from = date('H:i', $slot_from_timestamp);
						        $slot_to_timestamp = strtotime("+".$dynamic_window_time." minutes",$slot_from_timestamp);
						        $slot_to = date('H:i', $slot_to_timestamp);
						        if(($slot_from <= $close_time) && ($slot_from >= $prev_slot_to) && ($slot_to <= $close_time) && ($slot_to > $slot_from))
						        {
						            if($close_time == '23:00' || $close_time == '23:30')
						            {
						                $pre_exp = explode(":",$prev_slot_to);
						                $prev_slot_to_hr = $pre_exp[0];
						                $prev_slot_to_min = $pre_exp[1];
						                if(($prev_slot_to_hr == '23') && ($prev_slot_to_min >= '00' || $prev_slot_to_min < '30'))
						                {
						                    $slot_to = $close_time;    
						                }else
						                {
						                    // $slot[$j] = $slot_from."-".$slot_to;       
								            if((count($offline_data) > 0) && ($offline_data[0]['offline_tag'] == 1))
							            	{
							            		// echo "STEP 2<hr>";
							            		// echo $slot_from."-".$slot_to;
							            		// echo "<br>slot_from_timestamp".$slot_from_timestamp;
							            		// echo "<br>offline_from".$offline_data[0]['offline_from'];
							            		// echo "<br>slot_to_timestamp".$slot_to_timestamp;
							            		// echo "<br>offline_to".$offline_data[0]['offline_to'];
							            		// echo "<hr>";
							            		// if($slot_from_timestamp >= $offline_data[0]['offline_from'] && $slot_to_timestamp <= $offline_data[0]['offline_to'] )
							            		// if($offline_data[0]['offline_from'] >= $slot_from_timestamp && $offline_data[0]['offline_to'] >= $slot_to_timestamp )
							            		if(($slot_from_timestamp > $offline_data[0]['offline_from'] && $slot_from_timestamp < $offline_data[0]['offline_to']) || ($slot_to_timestamp > $offline_data[0]['offline_from'] && $slot_to_timestamp < $offline_data[0]['offline_to']))
							            		{
							            			// echo "<br> STEP 2 DO NOT SEND THIS ".$slot_from."-".$slot_to;
							            		}else
							            		{
							            			// echo "<br>current_time IS ".$current_time;
							            			// echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            			// echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
							            			# Do not pass the timestamp that are already passed
							            			if($current_time > $slot_from_timestamp)
							            			{
							            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
							            			}else
							            			{
								            			$slot[$flag] = $slot_from."-".$slot_to;		
								            			$flag++;
							            			}
							            		}
							            	}else
							            	{
							            		// echo "<br>current_time IS ".$current_time;
							            		// 	echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            		// 	echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
							            		# Do not pass the timestamp that are already passed
						            			if($current_time > $slot_from_timestamp)
						            			{
						            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
						            			}else
						            			{
							            			$slot[$flag] = $slot_from."-".$slot_to;		
							            			$flag++;
						            			}
							            	}
						                }
						            }else
						            {
						            	// $slot[$j] = $slot_from."-".$slot_to;		
						            	if((count($offline_data) > 0) && ($offline_data[0]['offline_tag'] == 1))
						            	{
						            		// echo "<hr>";
						            		// echo $slot_from."-".$slot_to;
						            		// echo "<br>slot_from_timestamp".$slot_from_timestamp;
						            		// echo "<br>offline_from".$offline_data[0]['offline_from'];
						            		// echo "<br>slot_to_timestamp".$slot_to_timestamp;
						            		// echo "<br>offline_to".$offline_data[0]['offline_to'];
						            		// echo "<hr>";
						            		// if($slot_from_timestamp >= $offline_data[0]['offline_from'] && $slot_to_timestamp <= $offline_data[0]['offline_to'] )
						            		// if($offline_data[0]['offline_from'] >= $slot_from_timestamp && $offline_data[0]['offline_to'] >= $slot_to_timestamp )
						            		if(($slot_from_timestamp > $offline_data[0]['offline_from'] && $slot_from_timestamp < $offline_data[0]['offline_to']) || ($slot_to_timestamp > $offline_data[0]['offline_from'] && $slot_to_timestamp < $offline_data[0]['offline_to']))
						            		{
						            			// echo "<br> STEP 3 DO NOT SEND THIS ".$slot_from."-".$slot_to;
						            		}else
						            		{
						            			// echo "<br>current_time IS ".$current_time;
							            		// 	echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            		// 	echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
						            			# Do not pass the timestamp that are already passed
						            			if($current_time > $slot_from_timestamp)
						            			{
						            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
						            			}else
						            			{
							            			$slot[$flag] = $slot_from."-".$slot_to;		
							            			$flag++;
						            			}
						            		}
						            	}else
						            	{
						            		// echo "<br>current_time IS ".$current_time;
							            	// 		echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            	// 		echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
						            		# Do not pass the timestamp that are already passed
					            			if($current_time > $slot_from_timestamp)
					            			{
					            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
					            			}else
					            			{
						            			$slot[$flag] = $slot_from."-".$slot_to;		
						            			$flag++;
					            			}
						            	}
						            	
						            }
						        }else
						        {
						            $slot_to = $close_time;
						        }

						        if($j >= 200)
						        {
						            $data['status']		=201;
						            $data['message']	="INFINITY ERROR";
						            $data['data']		=array();
						        }
						    }
						    if(count($slot) > 0)
						    {
							   	$data['status']		=200;
					            $data['message']	=$this->lang->line('success');
					            $data['data']		=$slot;
						    }else
						    {
						    	$data['status']		=201;
					            // $data['message']	='abc_no_data_found';
					            $data['message']	=$this->lang->line('no_data_found');
					            $data['data']		=array();
						    }
						}else
						{
						    $open_time_hrs.":".$open_time_min;
						    $close_time_exp = explode(":",$close_time);
						    $close_time_hrs = $close_time_exp[0];
						    $close_time_mins = $close_time_exp[1];
						    
						    $slot_to = '00:00';
						    $prev_slot_to = $slot_to;
						    $flag = 0;
						    for ($j=0; $slot_to < $close_time; $j++) 
						    // for ($j=$flag; $j < 25; $j++) 
						    {
						        $prev_slot_to = $slot_to;
						        if($slot_to_timestamp == '') # FIRST SLOT
						        {
						            $slot_from_timestamp = strtotime("+".$open_time_hrs." hours +".$open_time_min." minutes",$date_timestamp);
						            $slot_from_timestamp = strtotime("+".$buffer_time." minutes",$slot_from_timestamp);
						        }else
						        {
						            $slot_exp = explode(":",$slot_to);
						            $slot_to_hrs = $slot_exp[0];
						            $slot_to_mins = $slot_exp[1];
						            $slot_from_timestamp = strtotime("+".$slot_to_hrs." hours +".$slot_to_mins." minutes",$date_timestamp);
						        }
						        $slot_from = date('H:i', $slot_from_timestamp);
						        $slot_to_timestamp = strtotime("+".$dynamic_window_time." minutes",$slot_from_timestamp);
						        $slot_to = date('H:i', $slot_to_timestamp);
						        if(($slot_from <= $close_time) && ($slot_from >= $prev_slot_to) && ($slot_to <= $close_time) && ($slot_to > $slot_from))
						        {
						            if($close_time == '23:00' || $close_time == '23:30')
						            {
						                $pre_exp = explode(":",$prev_slot_to);
						                $prev_slot_to_hr = $pre_exp[0];
						                $prev_slot_to_min = $pre_exp[1];
						                if(($prev_slot_to_hr == '23') && ($prev_slot_to_min >= '00' || $prev_slot_to_min > '30'))
						                {
						                    $slot_to = $close_time;    
						                }else
						                {
						                    // $slot[$j] = $slot_from."-".$slot_to;       
						                    if((count($offline_data) > 0) && ($offline_data[0]['offline_tag'] == 1))
							            	{
							            		// echo "STEP 4 <hr>";
							            		// echo $slot_from."-".$slot_to;
							            		// echo "<br>slot_from_timestamp".$slot_from_timestamp;
							            		// echo "<br>offline_from".$offline_data[0]['offline_from'];
							            		// echo "<br>slot_to_timestamp".$slot_to_timestamp;
							            		// echo "<br>offline_to".$offline_data[0]['offline_to'];
							            		// echo "<hr>";
							            		// if($slot_from_timestamp >= $offline_data[0]['offline_from'] && $slot_to_timestamp <= $offline_data[0]['offline_to'] )
							            		// if($offline_data[0]['offline_from'] >= $slot_from_timestamp && $offline_data[0]['offline_to'] >= $slot_to_timestamp )
							            		if(($slot_from_timestamp > $offline_data[0]['offline_from'] && $slot_from_timestamp < $offline_data[0]['offline_to']) || ($slot_to_timestamp > $offline_data[0]['offline_from'] && $slot_to_timestamp < $offline_data[0]['offline_to']))
							            		{
							            			// echo "<br> STEP 4 DO NOT SEND THIS ".$slot_from."-".$slot_to;
							            		}else
							            		{
							            			// echo "<br>current_time IS ".$current_time;
							            			// echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            			// echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
							            			# Do not pass the timestamp that are already passed
							            			if($current_time > $slot_from_timestamp)
							            			{
							            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
							            			}else
							            			{
								            			$slot[$flag] = $slot_from."-".$slot_to;		
								            			$flag++;
							            			}
							            		}
							            	}else
							            	{
							            		// echo "<br>current_time IS ".$current_time;
							            		// 	echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            		// 	echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
							            		# Do not pass the timestamp that are already passed
						            			if($current_time > $slot_from_timestamp)
						            			{
						            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
						            			}else
						            			{
							            			$slot[$flag] = $slot_from."-".$slot_to;		
							            			$flag++;
						            			}
							            	}
						                }
						            }else
						            {
						                // $slot[$j] = $slot_from."-".$slot_to;
						                if((count($offline_data) > 0) && ($offline_data[0]['offline_tag'] == 1))
						            	{
						            		// echo "STEP 5<hr>";
						            		// echo $slot_from."-".$slot_to;
						            		// echo "<br>slot_from_timestamp".$slot_from_timestamp;
						            		// echo "<br>offline_from".$offline_data[0]['offline_from'];
						            		// echo "<br>slot_to_timestamp".$slot_to_timestamp;
						            		// echo "<br>offline_to".$offline_data[0]['offline_to'];
						            		// echo "<hr>";
						            		// if($slot_from_timestamp >= $offline_data[0]['offline_from'] && $slot_to_timestamp <= $offline_data[0]['offline_to'] )
						            		// if($offline_data[0]['offline_from'] >= $slot_from_timestamp && $offline_data[0]['offline_to'] >= $slot_to_timestamp )
						            		if(($slot_from_timestamp > $offline_data[0]['offline_from'] && $slot_from_timestamp < $offline_data[0]['offline_to']) || ($slot_to_timestamp > $offline_data[0]['offline_from'] && $slot_to_timestamp < $offline_data[0]['offline_to']))
						            		{
						            			// echo "<br> STEP 5 DO NOT SEND THIS ".$slot_from."-".$slot_to;
						            		}else
						            		{
						            			// echo "<br>current_time IS ".$current_time;
							            		// 	echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            		// 	echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
						            			# Do not pass the timestamp that are already passed
						            			if($current_time > $slot_from_timestamp)
						            			{
						            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
						            			}else
						            			{
							            			$slot[$flag] = $slot_from."-".$slot_to;		
							            			$flag++;
						            			}
						            		}
						            	}else
						            	{
						            		// echo "<br>current_time IS ".$current_time;
							            	// 		echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            	// 		echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
						            		# Do not pass the timestamp that are already passed
					            			if($current_time > $slot_from_timestamp)
					            			{
					            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
					            			}else
					            			{
						            			$slot[$flag] = $slot_from."-".$slot_to;		
						            			$flag++;
					            			}
						            	}
						            }
						        }else
						        {
						            $slot_to = $close_time;
						        }
						        if($j >= 200)
						        {
						            $data['status']		=201;
						            $data['message']	="INFINITY ERROR";
						            $data['data']		=array();
						        }
						    }
						    if(count($slot) > 0)
						    {
							   	$data['status']		=200;
					            $data['message']	=$this->lang->line('success');
					            $data['data']		=$slot;
						    }else
						    {
						    	$data['status']		=201;
					            $data['message']	=$this->lang->line('no_data_found');
					            // $data['message']	='def_no_data_found';
					            $data['data']		=array();
						    }
						}

		    		}
		    	} # END OF proceed_further = 1 
            }
	    # REST_Controller provide this method to send responses
	    $this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    # Below is the common function to explode the open close time and return
	public function get_open_close_time($date , $open_close_time)
	{
		$exp_all = explode("-",$open_close_time);
        $open_time = $exp_all[0];
        $open_time_exp = explode(":",$open_time); # 11:30
        $open_time_hr = $open_time_exp[0]; # 11
        $open_time_min = $open_time_exp[1]; # 30

        $open_time = $date + ($open_time_hr * 60 * 60); # Adding hours
        $open_time = $open_time + ($open_time_min * 60); # ADD MINUTES

        $close_time = $exp_all[1];
        $close_time_exp = explode(":",$close_time); # 11:30
        $close_time_hr = $close_time_exp[0]; # 11
        $close_time_min = $close_time_exp[1]; # 30

        $close_time = $date + ($close_time_hr * 60 * 60); # Adding hours
        $close_time = $close_time + ($close_time_min * 60); # ADD MINUTES
        return array('open_time' => $open_time , 'close_time' => $close_time);
	}

	public function get_open_close_breakstart_breakend_timings($open_close_time,$break_start_end_time)
	{
		$exp_all = explode("-",$open_close_time); # 10:30-11:00
        $open_time = $exp_all[0]; # 10:30
        $close_time = $exp_all[1]; # 11:00

        $brk_exp_all = explode("-",$break_start_end_time); # 12:30-13:00
        $break_start_time = $brk_exp_all[0]; # 12:30
        $break_end_time = $brk_exp_all[1]; # 13:00
		return array('open_time' => $open_time , 'close_time' => $close_time , 'break_start_time' => $break_start_time , 'break_end_time' => $break_end_time);
	}
    # This function will be called from customer side app so we need to take restaurant id from mobile team
    # This function is used to get the booking timeslot
    public function bkpget_dynamic_timeslots_get()
    {
    	try
        {
	    	$rest_id = !empty($_GET['rest_id'])?($_GET['rest_id']):'';
	    	$date_timestamp = !empty($_GET['date_timestamp'])?($_GET['date_timestamp']):''; 
	    	
            if($rest_id == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }else if($date_timestamp == ''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('date_timestamp_missing');
                $data['data']		=array();
            }else
            {
            	date_default_timezone_set('Asia/Singapore');
	    		// echo "SINGAPORE CURRENT TIME IS ".time();
	    		$current_time = time();
            	# First of all the timestamp that we get from mobile team should be as per the Singaporet timezone. Because we have to validate that this timestamp exists in restaurant offline table? Also we are adding 8 hours to the DB entry as entry in DB is as per UTC
		    	# From rest_offline table we will only check for offline_tag is 2 or 3 because its a complete day close 1 - Hour 2 - Day 3 - Multiple days
		    	$offline_data = $this->Common->getData('rest_offline','*','rest_id = "'.$rest_id.'"');
		    	if(count($offline_data) > 0)
		    	{
		    		$proceed_further = 0; # 0 No 1 Yes
		    		$offline_from = $offline_data[0]['offline_from'];
		    		$offline_to = $offline_data[0]['offline_to'];
		    		if($offline_data[0]['offline_tag'] != 1)
		    		{
		    			# ######## FOR INDIA UNCOMMENT BELOW ########
						// $offline_from_val = $offline_from - (5*60*60); # DEDUCT 5 HOURS
						// $offline_from = $offline_from_val - (30 *60 ); # AND THEN 30 MINUTES

						// $offline_to_val = $offline_to - (5*60*60); # DEDUCT 5 HOURS
						// $offline_to = $offline_to_val - (30 *60 ); # AND THEN 30 MINUTES
						# ######## FOR INDIA ########

		    			# Why we are subtracting because UTC and singapore having 8  hours difference and the localtime passed as timestamp will be 8 hours less than the UTC so either dedcut 8 hours from UTC or add 8 hours to the given timestamp
						# ######## FOR SINGAPORE UNCOMMENT BELOW ######### 
						$offline_from = $offline_from - (8*60*60); # DEDUCT 8 HOURS
						$offline_to = $offline_to - (8*60*60); # DEDUCT 8 HOURS
						# ######## FOR SINGAPORE #########
			    		if($date_timestamp >= $offline_from  && $date_timestamp <= $offline_to)
			    		{
			    			$data['status']		=201;
					        // $data['message']	=$this->lang->line('no_data_found');
					        $data['message']	="Restaurant is set to Offline and not receiving order on this day";
					        $data['data']		=array();
			    		}else
			    		{
			    			$proceed_further = 1;
			    		}
		    		}else
		    		{
		    			$proceed_further = 1;
		    		}
		    	}else # OPEN
	    		{
    			    $proceed_further = 1;
	    		}
	    		// echo "<br>proceed_further ".$proceed_further;
	    		if($proceed_further == 1)
		    	{
		    		# Get delivery time of the resturant
			    	$rest_basic_details = $this->Common->getData('restaurants','*','id = "'.$rest_id.'"');

			    	$settings_data = $this->Common->getData('settings','value','name = "basic_delivery_time" OR name = "basic_preparation_time" OR name = "window_time"');
			    	$basic_delivery_time = $time[0]['value'];
			    	$basic_preparation_time = $time[1]['value'];

			    	if($rest_basic_details[0]['delivery_time'] != '' && $rest_basic_details[0]['follow_global_del_time'] == 0)
			    	{
			    		# That means restaurant has its own set delivery time
			    		$delivery_time = $rest_basic_details[0]['delivery_time'];
			    	}else
			    	{
			    		$delivery_time = $settings_data[0]['value'];
			    	}

			    	# Get preparation time of the resturant
			    	if($rest_basic_details[0]['preparation_time'] != '' && $rest_basic_details[0]['preparation_time'] != 0)
			    	{
			    		# That means restaurant has its own set preparation_time
			    		$preparation_time = $rest_basic_details[0]['preparation_time'];
			    	}else
			    	{
			    		$preparation_time = $settings_data[1]['value'];
			    	}

			    	# BUFFER TIME = DELIVERY TIME + PREPARTION TIME
			    	$buffer_time = $delivery_time + $preparation_time;
			    	# DYNAMIC WINDOW TIME
			    	$dynamic_window_time = $settings_data[2]['value'];

			    	# Get Time mode
			    	if($rest_basic_details[0]['time_mode'] == 1) # 1 - for every day, 2 - for Specific day
			    	{
			    		// echo "QQQQ";
			    		$open_time = $rest_basic_details[0]['open_time'];
			    		$close_time = $rest_basic_details[0]['close_time'];
			    		if($rest_basic_details[0]['break_start_time'] != '')
			    		{
				    		$break_start_time = $rest_basic_details[0]['break_start_time'];
				    		$break_end_time = $rest_basic_details[0]['break_end_time'];
			    		}else
			    		{
			    			$break_start_time = '';
			    			$break_end_time = '';
			    		}
			    		$proceed_further_more = 1;
			    	}else if($rest_basic_details[0]['time_mode'] == 2)
			    	{
			    		// echo "WWWWWWWw";
			    		# Get what is the Day of the passed date_timestamp
			    		# COMMENT THIS TIMEZONE VALUE
			    		// date_default_timezone_set('Asia/Kolkata');
			    		// date_default_timezone_set('Asia/Singapore');
			    		// echo "SINGAPORE CURRENT TIME IS ".time();
			    		// $current_time = time();
			    		$weekday = date('l', $date_timestamp);
			    		$weekday = strtolower($weekday);
			    		$rest_time_daywise = $this->Common->getData('rest_time_daywise','*','rest_id = "'.$rest_id.'"');

			    		if(count($rest_time_daywise) > 0)
			    		{
				    		if($weekday == 'monday'){
				    			$full_day_close_status = $rest_time_daywise[0]['mon_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['mon_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['mon_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['mon_break_start_end_time'];
				    		}elseif($weekday == 'tuesday'){
				    			$full_day_close_status = $rest_time_daywise[0]['tue_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['tue_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['tue_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['tue_break_start_end_time'];
				    		}elseif($weekday == 'wednesday'){
				    			$full_day_close_status = $rest_time_daywise[0]['wed_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['wed_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['wed_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['wed_break_start_end_time'];
				    		}elseif($weekday == 'thursday'){
				    			$full_day_close_status = $rest_time_daywise[0]['thu_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['thu_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['thu_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['thu_break_start_end_time'];
				    		}elseif($weekday == 'friday'){
				    			$full_day_close_status = $rest_time_daywise[0]['fri_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['fri_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['fri_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['fri_break_start_end_time'];
				    		}elseif($weekday == 'saturday'){
				    			$full_day_close_status = $rest_time_daywise[0]['sat_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['sat_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['sat_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['sat_break_start_end_time'];
				    		}elseif($weekday == 'sunday'){
				    			$full_day_close_status = $rest_time_daywise[0]['sun_close_status'];
				    			$open_close_time = $rest_time_daywise[0]['sun_open_close_time'];
				    			$brk_status = $rest_time_daywise[0]['sun_break_status'];
				    			$brk_start_brk_end = $rest_time_daywise[0]['sun_break_start_end_time'];
				    		}

				    		if($full_day_close_status == 2) # 2- on this day restaurant will be closed, 1 - restaurant will be opend
				    		{
				    			// echo "12121211";
				    			$proceed_further_more = 0;
				    			# Restaurant is closed on this day
				    			// $data['message']	="Restaurant is closed on (".ucfirst($weekday).")";
				    			$data['status']		=201;
						        // $data['message']	=$this->lang->line('no_data_found');
						        $data['message']	="Restaurant is closed on ".ucfirst($weekday)."";
						        $data['data']		=array();
						        // exit();
				    		}else
				    		{
				    			// echo "33333";
				    			$proceed_further_more = 1;
				    			$open_close_time = explode("-",$open_close_time);
				    			$open_time = $open_close_time[0];
								$close_time = $open_close_time[1];

								if($brk_status == 1) # 2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time) and 0 when restaurant is closed in full day
								{
									$brk_start_brk_end = explode("-",$brk_start_brk_end);
					    			$break_start_time = $brk_start_brk_end[0];
									$break_end_time = $brk_start_brk_end[1];
								}else
								{
									# NO BREAK HOURS
									$break_start_time = '';
									$break_end_time = '';
								}
				    		}
			    		}
			    	} # END OF TIME MODE
		    		// echo "<br>proceed_further_more".$proceed_further_more;
		    		if($proceed_further_more == 1)
		    		{
		    			# REMOVE THIS TIMEZONE SET BEFORE GO ON LIVE
						// date_default_timezone_set('Asia/Kolkata');
						// $dynamic_window_time = 15;
			    		echo "<br> OPEN TIME IS ".$open_time;
						echo "<br> CLOSE TIME IS ".$close_time;
						echo "<br> WINDOW IS ".$dynamic_window_time;
						echo "<br> DELIVERY TIME IS ".$delivery_time;
						echo "<br> PREPARATION TIME IS ".$preparation_time;
						echo "<br>BREAK START AT ".$break_start_time;
						echo "<br>BREAK ENDS AT ".$break_end_time;
						echo "<br>BUFFER TIME ".$buffer_time;

			    		$exp = explode(":",$open_time);
						$open_time_hrs = $exp[0];
						$open_time_min = $exp[1];
						$slot_to_timestamp = '';
						$last_slot_timestamp = $dynamic_window_time + $delivery_time ;
						$slot_to = '00:00';
						if($break_start_time != '')
						{
						    $close_time_exp = explode(":",$break_start_time);
						    $close_time_hrs = $close_time_exp[0];
						    $close_time_mins = $close_time_exp[1];
						    $close_time_timsetamp_fresh = strtotime("+".$close_time_hrs." hours +".$close_time_mins." minutes",$date_timestamp);
						    $close_time_timsetamp = strtotime("+".$last_slot_timestamp." minutes",$close_time_timsetamp_fresh);
						    $final_close_time_loop = date('H:i', $close_time_timsetamp);
						    $final_close_time_cond = strtotime("+".$delivery_time." minutes",$close_time_timsetamp_fresh);
						    $final_close_time_cond = date('H:i', $final_close_time_cond);
						    $flag = 0;
						    for ($i=0; $slot_to < $break_start_time ; $i++) 
						    {
						        if($slot_to_timestamp == '') # FIRST SLOT
						        {
						            $slot_from_timestamp = strtotime("+".$open_time_hrs." hours +".$open_time_min." minutes",$date_timestamp);
						            $slot_from_timestamp = strtotime("+".$buffer_time." minutes",$slot_from_timestamp);
						        }else
						        {
						            $slot_exp = explode(":",$slot_to);
						            $slot_to_hrs = $slot_exp[0];
						            $slot_to_mins = $slot_exp[1];
						            $slot_from_timestamp = strtotime("+".$slot_to_hrs." hours +".$slot_to_mins." minutes",$date_timestamp);
						        }
						        $slot_from = date('H:i', $slot_from_timestamp);
						        $slot_to_timestamp = strtotime("+".$dynamic_window_time." minutes",$slot_from_timestamp);
						        $slot_to = date('H:i', $slot_to_timestamp);
						        if($slot_from < $break_start_time && $slot_to <= $break_start_time)
						        {
						            // echo "<br>SEND TO ARRAY ".$slot_from." - ".$slot_to."<br>";
						            // $slot[$i] = $slot_from."-".$slot_to;
						            // $flag++;
						            if((count($offline_data) > 0) && ($offline_data[0]['offline_tag'] == 1))
					            	{
					            		// echo "STEP 1<hr>";
					            		// echo $slot_from."-".$slot_to;
					            		echo "<br>slot_from_timestamp".$slot_from_timestamp;
					            		echo "<br>offline_from".$offline_data[0]['offline_from'];
					            		echo "<br>slot_to_timestamp".$slot_to_timestamp;
					            		echo "<br>offline_to".$offline_data[0]['offline_to'];
					            		echo "<hr>";
					            		// if($slot_from_timestamp >= $offline_data[0]['offline_from'] && $slot_to_timestamp <= $offline_data[0]['offline_to'] )
					            		// if($offline_data[0]['offline_from'] >= $slot_from_timestamp && $offline_data[0]['offline_to'] >= $slot_to_timestamp )
					            		if(($slot_from_timestamp > $offline_data[0]['offline_from'] && $slot_from_timestamp < $offline_data[0]['offline_to']) || ($slot_to_timestamp > $offline_data[0]['offline_from'] && $slot_to_timestamp < $offline_data[0]['offline_to']))
					            		{
					            			// echo "<br> STEP 1 DO NOT SEND THIS ".$slot_from."-".$slot_to;
					            		}else
					            		{
					            			// echo "<br>current_time IS ".$current_time;
					            			// echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
					            			// echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
					            			# Do not pass the timestamp that are already passed
					            			if($current_time > $slot_from_timestamp)
					            			{
					            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
					            			}else
					            			{
						            			$slot[$flag] = $slot_from."-".$slot_to;		
						            			$flag++;
					            			}
					            		}
					            	}else
					            	{
					            		// echo "<br>current_time IS ".$current_time;
				            			// echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
				            			// echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
					            		# Do not pass the timestamp that are already passed
				            			if($current_time > $slot_from_timestamp)
				            			{
				            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
				            			}else
				            			{
					            			$slot[$flag] = $slot_from."-".$slot_to;		
					            			$flag++;
				            			}
					            	}
						        }else
						        {
						            // echo "<br>DO NOT SEND TO ARRAY ".$slot_from." - ".$slot_to."<br>";
						        }
						    }

						    # AFTER BREAK

						    $slot_to_timestamp = '';
						    $exp = explode(":",$break_end_time);
						    $open_time_hrs = $exp[0];
						    $open_time_min = $exp[1];
						    $open_time_hrs.":".$open_time_min;
						    $close_time_exp = explode(":",$close_time);
						    $close_time_hrs = $close_time_exp[0];
						    $close_time_mins = $close_time_exp[1];
						    $slot_to = '00:00';
						    $prev_slot_to = $slot_to;
						    
						    // $key = $flag;
						    for ($j=$flag; $slot_to < $close_time; $j++)
						    // for ($j=$flag; $j < 35; $j++) 
						    {
						        $prev_slot_to = $slot_to;
						        if($slot_to_timestamp == '') # FIRST SLOT
						        {
						            $slot_from_timestamp = strtotime("+".$open_time_hrs." hours +".$open_time_min." minutes",$date_timestamp);
						            $slot_from_timestamp = strtotime("+".$buffer_time." minutes",$slot_from_timestamp);
						        }else
						        {
						            $slot_exp = explode(":",$slot_to);
						            $slot_to_hrs = $slot_exp[0];
						            $slot_to_mins = $slot_exp[1];
						            $slot_from_timestamp = strtotime("+".$slot_to_hrs." hours +".$slot_to_mins." minutes",$date_timestamp);
						        }
						        $slot_from = date('H:i', $slot_from_timestamp);
						        $slot_to_timestamp = strtotime("+".$dynamic_window_time." minutes",$slot_from_timestamp);
						        $slot_to = date('H:i', $slot_to_timestamp);
						        if(($slot_from <= $close_time) && ($slot_from >= $prev_slot_to) && ($slot_to <= $close_time) && ($slot_to > $slot_from))
						        {
						            if($close_time == '23:00' || $close_time == '23:30')
						            {
						                $pre_exp = explode(":",$prev_slot_to);
						                $prev_slot_to_hr = $pre_exp[0];
						                $prev_slot_to_min = $pre_exp[1];
						                if(($prev_slot_to_hr == '23') && ($prev_slot_to_min >= '00' || $prev_slot_to_min < '30'))
						                {
						                    $slot_to = $close_time;    
						                }else
						                {
						                    // $slot[$j] = $slot_from."-".$slot_to;       
								            if((count($offline_data) > 0) && ($offline_data[0]['offline_tag'] == 1))
							            	{
							            		// echo "STEP 2<hr>";
							            		// echo $slot_from."-".$slot_to;
							            		// echo "<br>slot_from_timestamp".$slot_from_timestamp;
							            		// echo "<br>offline_from".$offline_data[0]['offline_from'];
							            		// echo "<br>slot_to_timestamp".$slot_to_timestamp;
							            		// echo "<br>offline_to".$offline_data[0]['offline_to'];
							            		// echo "<hr>";
							            		// if($slot_from_timestamp >= $offline_data[0]['offline_from'] && $slot_to_timestamp <= $offline_data[0]['offline_to'] )
							            		// if($offline_data[0]['offline_from'] >= $slot_from_timestamp && $offline_data[0]['offline_to'] >= $slot_to_timestamp )
							            		if(($slot_from_timestamp > $offline_data[0]['offline_from'] && $slot_from_timestamp < $offline_data[0]['offline_to']) || ($slot_to_timestamp > $offline_data[0]['offline_from'] && $slot_to_timestamp < $offline_data[0]['offline_to']))
							            		{
							            			// echo "<br> STEP 2 DO NOT SEND THIS ".$slot_from."-".$slot_to;
							            		}else
							            		{
							            			// echo "<br>current_time IS ".$current_time;
							            			// echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            			// echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
							            			# Do not pass the timestamp that are already passed
							            			if($current_time > $slot_from_timestamp)
							            			{
							            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
							            			}else
							            			{
								            			$slot[$flag] = $slot_from."-".$slot_to;		
								            			$flag++;
							            			}
							            		}
							            	}else
							            	{
							            		// echo "<br>current_time IS ".$current_time;
							            		// 	echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            		// 	echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
							            		# Do not pass the timestamp that are already passed
						            			if($current_time > $slot_from_timestamp)
						            			{
						            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
						            			}else
						            			{
							            			$slot[$flag] = $slot_from."-".$slot_to;		
							            			$flag++;
						            			}
							            	}
						                }
						            }else
						            {
						            	// $slot[$j] = $slot_from."-".$slot_to;		
						            	if((count($offline_data) > 0) && ($offline_data[0]['offline_tag'] == 1))
						            	{
						            		// echo "<hr>";
						            		// echo $slot_from."-".$slot_to;
						            		// echo "<br>slot_from_timestamp".$slot_from_timestamp;
						            		// echo "<br>offline_from".$offline_data[0]['offline_from'];
						            		// echo "<br>slot_to_timestamp".$slot_to_timestamp;
						            		// echo "<br>offline_to".$offline_data[0]['offline_to'];
						            		// echo "<hr>";
						            		// if($slot_from_timestamp >= $offline_data[0]['offline_from'] && $slot_to_timestamp <= $offline_data[0]['offline_to'] )
						            		// if($offline_data[0]['offline_from'] >= $slot_from_timestamp && $offline_data[0]['offline_to'] >= $slot_to_timestamp )
						            		if(($slot_from_timestamp > $offline_data[0]['offline_from'] && $slot_from_timestamp < $offline_data[0]['offline_to']) || ($slot_to_timestamp > $offline_data[0]['offline_from'] && $slot_to_timestamp < $offline_data[0]['offline_to']))
						            		{
						            			// echo "<br> STEP 3 DO NOT SEND THIS ".$slot_from."-".$slot_to;
						            		}else
						            		{
						            			// echo "<br>current_time IS ".$current_time;
							            		// 	echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            		// 	echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
						            			# Do not pass the timestamp that are already passed
						            			if($current_time > $slot_from_timestamp)
						            			{
						            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
						            			}else
						            			{
							            			$slot[$flag] = $slot_from."-".$slot_to;		
							            			$flag++;
						            			}
						            		}
						            	}else
						            	{
						            		// echo "<br>current_time IS ".$current_time;
							            	// 		echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            	// 		echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
						            		# Do not pass the timestamp that are already passed
					            			if($current_time > $slot_from_timestamp)
					            			{
					            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
					            			}else
					            			{
						            			$slot[$flag] = $slot_from."-".$slot_to;		
						            			$flag++;
					            			}
						            	}
						            	
						            }
						        }else
						        {
						            $slot_to = $close_time;
						        }

						        if($j >= 200)
						        {
						            $data['status']		=201;
						            $data['message']	="INFINITY ERROR";
						            $data['data']		=array();
						        }
						    }
						    if(count($slot) > 0)
						    {
							   	$data['status']		=200;
					            $data['message']	=$this->lang->line('success');
					            $data['data']		=$slot;
						    }else
						    {
						    	$data['status']		=201;
					            // $data['message']	='abc_no_data_found';
					            $data['message']	=$this->lang->line('no_data_found');
					            $data['data']		=array();
						    }
						}else
						{
						    $open_time_hrs.":".$open_time_min;
						    $close_time_exp = explode(":",$close_time);
						    $close_time_hrs = $close_time_exp[0];
						    $close_time_mins = $close_time_exp[1];
						    
						    $slot_to = '00:00';
						    $prev_slot_to = $slot_to;
						    $flag = 0;
						    for ($j=0; $slot_to < $close_time; $j++) 
						    // for ($j=$flag; $j < 25; $j++) 
						    {
						        $prev_slot_to = $slot_to;
						        if($slot_to_timestamp == '') # FIRST SLOT
						        {
						            $slot_from_timestamp = strtotime("+".$open_time_hrs." hours +".$open_time_min." minutes",$date_timestamp);
						            $slot_from_timestamp = strtotime("+".$buffer_time." minutes",$slot_from_timestamp);
						        }else
						        {
						            $slot_exp = explode(":",$slot_to);
						            $slot_to_hrs = $slot_exp[0];
						            $slot_to_mins = $slot_exp[1];
						            $slot_from_timestamp = strtotime("+".$slot_to_hrs." hours +".$slot_to_mins." minutes",$date_timestamp);
						        }
						        $slot_from = date('H:i', $slot_from_timestamp);
						        $slot_to_timestamp = strtotime("+".$dynamic_window_time." minutes",$slot_from_timestamp);
						        $slot_to = date('H:i', $slot_to_timestamp);
						        if(($slot_from <= $close_time) && ($slot_from >= $prev_slot_to) && ($slot_to <= $close_time) && ($slot_to > $slot_from))
						        {
						            if($close_time == '23:00' || $close_time == '23:30')
						            {
						                $pre_exp = explode(":",$prev_slot_to);
						                $prev_slot_to_hr = $pre_exp[0];
						                $prev_slot_to_min = $pre_exp[1];
						                if(($prev_slot_to_hr == '23') && ($prev_slot_to_min >= '00' || $prev_slot_to_min > '30'))
						                {
						                    $slot_to = $close_time;    
						                }else
						                {
						                    // $slot[$j] = $slot_from."-".$slot_to;       
						                    if((count($offline_data) > 0) && ($offline_data[0]['offline_tag'] == 1))
							            	{
							            		// echo "STEP 4 <hr>";
							            		// echo $slot_from."-".$slot_to;
							            		// echo "<br>slot_from_timestamp".$slot_from_timestamp;
							            		// echo "<br>offline_from".$offline_data[0]['offline_from'];
							            		// echo "<br>slot_to_timestamp".$slot_to_timestamp;
							            		// echo "<br>offline_to".$offline_data[0]['offline_to'];
							            		// echo "<hr>";
							            		// if($slot_from_timestamp >= $offline_data[0]['offline_from'] && $slot_to_timestamp <= $offline_data[0]['offline_to'] )
							            		// if($offline_data[0]['offline_from'] >= $slot_from_timestamp && $offline_data[0]['offline_to'] >= $slot_to_timestamp )
							            		if(($slot_from_timestamp > $offline_data[0]['offline_from'] && $slot_from_timestamp < $offline_data[0]['offline_to']) || ($slot_to_timestamp > $offline_data[0]['offline_from'] && $slot_to_timestamp < $offline_data[0]['offline_to']))
							            		{
							            			// echo "<br> STEP 4 DO NOT SEND THIS ".$slot_from."-".$slot_to;
							            		}else
							            		{
							            			// echo "<br>current_time IS ".$current_time;
							            			// echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            			// echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
							            			# Do not pass the timestamp that are already passed
							            			if($current_time > $slot_from_timestamp)
							            			{
							            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
							            			}else
							            			{
								            			$slot[$flag] = $slot_from."-".$slot_to;		
								            			$flag++;
							            			}
							            		}
							            	}else
							            	{
							            		// echo "<br>current_time IS ".$current_time;
							            		// 	echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            		// 	echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
							            		# Do not pass the timestamp that are already passed
						            			if($current_time > $slot_from_timestamp)
						            			{
						            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
						            			}else
						            			{
							            			$slot[$flag] = $slot_from."-".$slot_to;		
							            			$flag++;
						            			}
							            	}
						                }
						            }else
						            {
						                // $slot[$j] = $slot_from."-".$slot_to;
						                if((count($offline_data) > 0) && ($offline_data[0]['offline_tag'] == 1))
						            	{
						            		// echo "STEP 5<hr>";
						            		// echo $slot_from."-".$slot_to;
						            		// echo "<br>slot_from_timestamp".$slot_from_timestamp;
						            		// echo "<br>offline_from".$offline_data[0]['offline_from'];
						            		// echo "<br>slot_to_timestamp".$slot_to_timestamp;
						            		// echo "<br>offline_to".$offline_data[0]['offline_to'];
						            		// echo "<hr>";
						            		// if($slot_from_timestamp >= $offline_data[0]['offline_from'] && $slot_to_timestamp <= $offline_data[0]['offline_to'] )
						            		// if($offline_data[0]['offline_from'] >= $slot_from_timestamp && $offline_data[0]['offline_to'] >= $slot_to_timestamp )
						            		if(($slot_from_timestamp > $offline_data[0]['offline_from'] && $slot_from_timestamp < $offline_data[0]['offline_to']) || ($slot_to_timestamp > $offline_data[0]['offline_from'] && $slot_to_timestamp < $offline_data[0]['offline_to']))
						            		{
						            			// echo "<br> STEP 5 DO NOT SEND THIS ".$slot_from."-".$slot_to;
						            		}else
						            		{
						            			// echo "<br>current_time IS ".$current_time;
							            		// 	echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            		// 	echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
						            			# Do not pass the timestamp that are already passed
						            			if($current_time > $slot_from_timestamp)
						            			{
						            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
						            			}else
						            			{
							            			$slot[$flag] = $slot_from."-".$slot_to;		
							            			$flag++;
						            			}
						            		}
						            	}else
						            	{
						            		// echo "<br>current_time IS ".$current_time;
							            	// 		echo "<br>slot_from_timestamp IS ".$slot_from_timestamp;
							            	// 		echo "<br>slot_to_timestamp IS ".$slot_to_timestamp;
						            		# Do not pass the timestamp that are already passed
					            			if($current_time > $slot_from_timestamp)
					            			{
					            				// echo "<br>DO NOT SEND PASSED SLOT ".$slot_from." - ".$slot_to."<br>";
					            			}else
					            			{
						            			$slot[$flag] = $slot_from."-".$slot_to;		
						            			$flag++;
					            			}
						            	}
						            }
						        }else
						        {
						            $slot_to = $close_time;
						        }
						        if($j >= 200)
						        {
						            $data['status']		=201;
						            $data['message']	="INFINITY ERROR";
						            $data['data']		=array();
						        }
						    }
						    if(count($slot) > 0)
						    {
							   	$data['status']		=200;
					            $data['message']	=$this->lang->line('success');
					            $data['data']		=$slot;
						    }else
						    {
						    	$data['status']		=201;
					            // $data['message']	=$this->lang->line('no_data_found');
					            $data['message']	='def_no_data_found';
					            $data['data']		=array();
						    }
						}

		    		}
		    	} # END OF proceed_further = 1 
            }
	    # REST_Controller provide this method to send responses
	    $this->response($data, $data['status']);

        } catch (\Exception $e) {

            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

    public function get_dc_promotion_get()
    {
    	# We have to check if any order is being made for future date then is there any promo available which has validity and fall in the same date.
    	# Ex today date is aug 11 and customer makes an order on 11 aug for 15 aug and there is promo ABC which has a validity of 14 aug to 16 aug so at this point ABC promo muse come after checking all other validations
    	# That is why we have added extra param as $pickup_time. In case of order now $pickup_time will be passed as NA and in rest two case it will be passed with valid value. We also need not to check it in case of order now.
    	try{
    		$tokenData = $this->verify_request();
    		$restaurant_id = !empty($_GET['restaurant_id'])?$this->db->escape_str($_GET['restaurant_id']):'';
    		$item_total = !empty($_GET['item_total'])?$this->db->escape_str($_GET['item_total']):'';
    		$pickup_time = !empty($_GET['pickup_time'])?$this->db->escape_str($_GET['pickup_time']):'';
    		
    		if($tokenData === false){
                $status = parent::HTTP_UNAUTHORIZED;
                $data['status']	 = $status;
                $data['message']	=$this->lang->line('unauthorized_access');
            }else if($restaurant_id == ''){
                $data['status']		=201;
                $data['message']	= $this->lang->line('rest_id_missing');
                $data['data']		= array();
            }else if($item_total == ''){
                $data['status']		=201;
                $data['message']	= $this->lang->line('item_total_missing');
                $data['data']		= array();
            }else if($pickup_time == ''){
                $data['status']		=201;
                $data['message']	= $this->lang->line('pickup_time_from_missing');
                $data['data']		= array();
            }else
            {
            	$delivery_promotion = $this->get_delivery_charge_promotion($restaurant_id , $item_total , $tokenData, $pickup_time);
			    $response['delivery_charge_promotion'] = $delivery_promotion;

			    $data['status']		=200;
	            $data['message']	=$this->lang->line('success');
	            $data['data']		=$response;
            }

    		$this->response($data, $data['status']);

    	} catch (\Exception $e) {

            //make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
    }

	# Below function is used to get all promotional banner
	public function get_banner_list_get()
	{
		try{

			$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
    		$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
    		$page = $page * $limit;
    		# Get Advertisement Banners
		    $ad_banners = $this->Common->getData('ad_banners','*','status = 1','','','id','DESC',$limit,$page);

        	$response = array();
        	$response = $ad_banners;

	    	$data['status']		=200;
            $data['message']	=$this->lang->line('success');
            $data['data']		=$response;
    		
    		$this->response($data, $data['status']);

    	} catch (\Exception $e) {
            # make error log
            log_message('error', $e);

            $data['status']		=500;
            $data['message']	=$this->lang->line('internal_server_error');
            $data['data']		=array(); 

            $this->response($data, $data['status']);
        }
	}


    # This api will be used in customer application for restro review and feedback screen
    public function restaurant_review_screen_cust_app_get()
    {
    	try{
 			$page = !empty($_GET['page'])?$this->db->escape_str($_GET['page']):0;
 			$limit = !empty($_GET['limit'])?$this->db->escape_str($_GET['limit']):MOBILE_PAGE_LIMIT;
			$page = $page * $limit;
			$restaurant_id = !empty($_GET['restaurant_id'])?$this->db->escape_str($_GET['restaurant_id']):'';
 			$tokenData = $this->verify_request();

			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }else if($restaurant_id ==''){
                $data['status']		=201;
                $data['message']	=$this->lang->line('rest_id_missing');
                $data['data']		=array();
            }
            else
		    {
		    	// $all_rating = $this->Common->getData('ratings','ratings.*,restaurants.id,restaurants.rest_name,restaurants.avg_rating,restaurants.rest_pin_address,restaurants.rest_unit_number,restaurants.rest_street_address,restaurants.rest_postal_code,users.latitude,users.longitude,users.fullname','ratings.to_id = "'.$tokenData->id.'"',array('users','restaurants'),array('users.id = ratings.to_id' , 'restaurants.admin_id = ratings.to_id'),'','',$limit,$page);
		    	$all_rating = $this->Common->getData('ratings','ratings.*,restaurants.admin_id,restaurants.id,restaurants.rest_name,restaurants.avg_rating,restaurants.rest_pin_address,restaurants.rest_unit_number,restaurants.rest_street_address,restaurants.rest_postal_code,users.latitude,users.longitude,users.fullname','ratings.to_id = "'.$restaurant_id.'"',array('restaurants','users'),array('restaurants.id = ratings.to_id','ratings.from_user_id = users.id'),'','',$limit,$page);
		    	// echo $this->db->last_query();

		    	if(count($all_rating) > 0)
		    	{
			    	$data['status']		=200;
					$data['message']	=$this->lang->line('success');
					$data['data']		=$all_rating; 
		    	}else
		    	{
		    		$data['status']     =201;
                    $data['message']    =$this->lang->line('no_data_found');
                    $data['data']       =array();
		    	}
		    }
 			# REST_Controller provide this method to send responses
			$this->response($data, $data['status']);
 		} catch (\Exception $e) {

			# make error log
			log_message('error', $e);
			$data['status']		=500;
			$data['message']	=$this->lang->line('internal_server_error');
			$data['data']		=array(); 
			$this->response($data, $data['status']);
		}
    }


    # Logout function start
    # This function is used to logout the user
    public function logout_post()
    {
    	try{
    		$tokenData = $this->verify_request();
			if($tokenData === false){
		        $status = parent::HTTP_UNAUTHORIZED;
		        $data['status']	 = $status;
		        $data['message']	=$this->lang->line('unauthorized_access');
		    }else
		    {
		    	$this->Common->updateData('users',array('is_online' => 0 , 'device_token' => '' , 'device_id' => ''),array('id' => $tokenData->id));
		    	$data['status']		=200;
				$data['message']	=$this->lang->line('logout_success');
				$data['data']		=array();
		    }
		    # REST_Controller provide this method to send responses
			$this->response($data, $data['status']);
    	} catch (\Exception $e) {

			# make error log
			log_message('error', $e);

			$data['status']		=500;
			$data['message']	=$this->lang->line('internal_server_error');
			$data['data']		=array(); 

			$this->response($data, $data['status']);
		}
    }
    # Logout function end


    public function lalamove_quotation_place_order($body) 
    {
	    // $key = 'be9812303d424e11811afec2dd2e627f'; #LIVE
	    // $secret = 'MC8CAQACBQC5IZplAgMBAAECBQC4PkhBAgMAzVECAwDm1QIDAIwhAgMAs90C'; #LIVE
	    $key = 'da10edb6ed6936c7baccba922c308776'; #SANDBOX
	    $secret = 'b3aVjAP/dvbi4vxy0PfeaBpakpPMBtlqDPgwZJJ6tQKlHoyGGm+2EFCf9AK7sQfR'; #SANDBOX
	    $time = time() * 1000;
	    $method = 'POST';
	    $path = '/v2/quotations';
	    $region = 'SG';
	    $order_body = $body;
	    $body = json_encode($body, true);
	    $_encryptBody = '';
	    $_encryptBody = "{$time}\r\n{$method}\r\n/v2/quotations\r\n\r\n{$body}";
	    $signature = hash_hmac("sha256", $_encryptBody, $secret);
	    $token = $key . ':' . $time . ':' . $signature;
	    // echo "<pre> BODY PRINT";
	    // print_r($body);
	    $curl_response = $this->initiate_curlfn($path, $body, $token, $region);
	    // echo "<pre> 9406 ";
	    //print_r($curl_response);
	    $quotation_response = json_decode($curl_response);
	    if (!empty($quotation_response)) {
	        if (isset($quotation_response->message)) {
	            # That means we have some error
	            // echo "LALAMOVE ORder placing failed due to ".$quotation_response->message;
	            return array('lalamove_order_id' => '', 'lalamove_order_amount' => '', 'failed_reason' => $quotation_response->message, 'lalamove_track_link' => '');
	            exit();
	        } else {
	            $amount = $quotation_response->totalFee;
	            $currency = $quotation_response->totalFeeCurrency;
	            # Now place Lalamove Order
	            $path = '/v2/orders';
	            $region = 'SG';
	            # Here we need to add quotedTotalFee array
	            // echo "TO ORDER ";
	            $order_body['quotedTotalFee'] = array("amount" => $amount, "currency" => $currency);
	            $order_body['sms'] = false;
	            $order_body['pod'] = false;
	            $order_body['fleetOption'] = "FLEET_ALL";
	            // print_r($order_body);
	            $order_body = json_encode($order_body);
	            // echo "JSON PRINTING";
	            // print_r($order_body);
	            // die;
	            $_encryptBody = '';
	            $_encryptBody = "{$time}\r\n{$method}\r\n/v2/orders\r\n\r\n{$order_body}";
	            //$_encryptBody = $time."\r\n".$method."\r\n".$path."\r\n\r\n".json_encode((object)$body);
	            $signature = hash_hmac("sha256", $_encryptBody, $secret);
	            // echo $time,PHP_EOL;
	            // echo $signature,PHP_EOL;
	            $token = $key . ':' . $time . ':' . $signature;
	            $curl_response = $this->initiate_curlfn($path, $order_body, $token, $region);
	            // echo "<pre> CURLRESPONSE 9433 ";
	            // print_r($curl_response);
	            // die;
	            $order_response = json_decode($curl_response);
	            // echo "<pre> 9431 line";
	            // print_r($order_response);
	            // die;
	            if (!empty($order_response)) {
	                if (isSet($order_response->message)) {
	                    # SOMETHING FAILED
	                    return array('lalamove_order_id' => '', 'lalamove_order_amount' => $amount, 'failed_reason' => $order_response->message, 'lalamove_track_link' => '');
	                } else {
	                    $lalamove_order_id = $order_response->orderRef;
	                    # Also we need shareLink(track link)
	                    $order_detail = $this->lalamove_order_deails($lalamove_order_id);
	                    // echo "<pre> TRACKLINK";
	                    // print_r($order_detail);
	                    if ($order_detail == null) {
	                        return array('lalamove_order_id' => $lalamove_order_id, 'lalamove_order_amount' => $amount, 'failed_reason' => '', 'lalamove_track_link' => '');
	                    } else {
	                        $track_link = $order_detail->shareLink;
	                        return array('lalamove_order_id' => $lalamove_order_id, 'lalamove_order_amount' => $amount, 'failed_reason' => '', 'lalamove_track_link' => $track_link);
	                    }
	                }
	            }
	        }
	    }
	}

	public function initiate_curlfn($path, $body, $token, $region) {
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_URL => 'https://rest.sandbox.lalamove.com'.$path, # SANDBOX
        // CURLOPT_URL => 'https://rest.lalamove.com'.$path, # LIVE
        CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 3, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_POSTFIELDS => $body, CURLOPT_HTTPHEADER => array("Content-type: application/json; charset=utf-8", "Authorization: hmac " . $token, "Accept: application/json", "X-LLM-Country: " . $region),));
        // print_r($body);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    # FOR GET METHOD
    public function initiate_curlfnget($path, $body, $token, $region) {
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_URL => 'https://rest.sandbox.lalamove.com'.$path, # SANDBOX
        // CURLOPT_URL => 'https://rest.lalamove.com'.$path, # LIVE
        CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 3, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET', CURLOPT_POSTFIELDS => $body, CURLOPT_HTTPHEADER => array("Content-type: application/json; charset=utf-8", "Authorization: hmac " . $token, "Accept: application/json", "X-LLM-Country: " . $region),));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    public function lalamove_order_deails($lalamove_order_id) {
        // $key = 'be9812303d424e11811afec2dd2e627f'; #LIVE
        // $secret = 'MC8CAQACBQC5IZplAgMBAAECBQC4PkhBAgMAzVECAwDm1QIDAIwhAgMAs90C'; #LIVE
        $key = 'da10edb6ed6936c7baccba922c308776'; #SANDBOX
        $secret = 'b3aVjAP/dvbi4vxy0PfeaBpakpPMBtlqDPgwZJJ6tQKlHoyGGm+2EFCf9AK7sQfR'; #SANDBOX
        $time = time() * 1000;
        $method = 'GET';
        $path = '/v2/orders/' . $lalamove_order_id;
        $region = 'SG';
        $body = array();
        $body = json_encode($body, true);
        $_encryptBody = "{$time}\r\n{$method}\r\n/v2/orders/" . $lalamove_order_id . "\r\n\r\n{$body}";
        $signature = hash_hmac("sha256", $_encryptBody, $secret);
        $token = $key . ':' . $time . ':' . $signature;
        $curl_response = $this->initiate_curlfnget($path, $body, $token, $region);
        // echo "<pre> chekcing CULR";
        // print_r($curl_response);die;
        $response = json_decode($curl_response);
        return $response;
    }

    /* CARD ACTIVITY */
    public function save_card_post()
    {
    	try
    	{
	    	$tokenData = $this->verify_request();
	    	$card_number = !empty($_POST['card_number'])?$this->db->escape_str($_POST['card_number']):'';
	    	$card_exp_month = !empty($_POST['card_exp_month'])?$this->db->escape_str($_POST['card_exp_month']):'';
	    	$card_exp_year = !empty($_POST['card_exp_year'])?$this->db->escape_str($_POST['card_exp_year']):'';
	    	$card_cvv = !empty($_POST['card_cvv'])?$this->db->escape_str($_POST['card_cvv']):'';
			
			if($tokenData === false){
	            $status = parent::HTTP_UNAUTHORIZED;
	            $data['status']	 = $status;
	            $data['message']	= $this->lang->line('unauthorized_access');
	        }else if($card_number == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('card_number_missing');
                $data['data']		=array();
			}else if($card_exp_month == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('card_exp_month_missing');
                $data['data']		=array();
			}else if($card_exp_year == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('card_exp_year_missing');
                $data['data']		=array();
			}else if($card_cvv == '')
			{
				$data['status']		=201;
                $data['message']	=$this->lang->line('card_cvv_missing');
                $data['data']		=array();
			}else
	        {
		    	try {
		    			require_once(APPPATH.'libraries/stripe-php/init.php');
				        $stripe_api_key = $this->config->item('stripe_secret');
				        \Stripe\Stripe::setApiKey($stripe_api_key);

		    			$query = "SELECT `*` FROM `users` WHERE `id` = ".$tokenData->id;
		    			$check_stripe_id = $this->Common->custom_query($query , 'get');

				   		if(isset($check_stripe_id[0]['stripe_id']) && $check_stripe_id[0]['stripe_id'] != '')
				   		{
				   			$cust_id = $check_stripe_id[0]['stripe_id'];
				       		
				   		}else
				   		{
				   			$customer = \Stripe\Customer::create(array(
								  'email' => $tokenData->email
								));

				   			$cust_id = $customer->id;
							$this->Common->updateData('users',array('stripe_id' => $cust_id), 'id = "'.$tokenData->id.'"');
				   		}

				        $token = \Stripe\Token::create([
						  'card' => [
						    'number' => $card_number,
						    'exp_month' => $card_exp_month,
						    'exp_year' => $card_exp_year,
						    'cvc' => $card_cvv,
						  ],
						]);

						\Stripe\Customer::createSource(
						  	$cust_id,
						  	['source' => $token->id]
						);

						$data['status']		= 200;
			            $data['message']	= 'Card saved Successfully';
			            $data['data']		= array();
			            $this->response($data, $data['status']);
					} catch (Error $e) 
					{
						$data['status']		= 'error';
			            $data['message']	= $e->getMessage();
			            $data['data']		= array();
			            $this->response($data, $data['status']);

					}
				$this->response($data, $data['status']);
	        }
	        $this->response($data, $data['status']);
		} catch (\Exception $e) {
	        //make error log
	        log_message('error', $e);

	        $data['status']		=500;
	        $data['message']	=$this->lang->line('internal_server_error');
	        $data['data']		=array(); 

	        $this->response($data, $data['status']);
	    }
	}

    /* RETRIEVE CUSTOMER CARDS */
    public function retrieve_cust_card_get()
    {
    	try
    	{
	    	$tokenData = $this->verify_request();
			if($tokenData === false)
			{
	            $status = parent::HTTP_UNAUTHORIZED;
	            $data['status']	 = $status;
	            $data['message']	= $this->lang->line('unauthorized_access');
	        }else
	        {
		    	try {
			    		$check_stripe_id = $this->Common->getData('users','stripe_id','id = "'.$tokenData->id.'"');
				   		if(isset($check_stripe_id[0]['stripe_id']) && $check_stripe_id[0]['stripe_id'] != '')
				   		{
				   			$cust_id = $check_stripe_id[0]['stripe_id'];
				   			require_once(APPPATH.'libraries/stripe-php/init.php');
					        $stripe_api_key = $this->config->item('stripe_secret');
					        \Stripe\Stripe::setApiKey($stripe_api_key);
					        
							$cards = \Stripe\Customer::allSources(
								$cust_id,
								['object' => 'card', 'limit' => 10]
							);

							$data['status']		= 200;
				            $data['message']	= 'Data get Successfully';
				            $data['data']		= $cards;
				   		}else
				   		{
				       		$data['status']		= 200;
				            $data['message']	= 'No card saved by this customer';
				            $data['data']		= array();
				   		}
					} catch (Error $e) 
					{
						$data['status']		= 'error';
			            $data['message']	= $e->getMessage();
			            $data['data']		= array();
			            $this->response($data, $data['status']);
				  // http_response_code(500);
				  // return array('status' => 'error' , 'msg' => $e->getMessage());
					}
				$this->response($data, $data['status']);
	        }
	        $this->response($data, $data['status']);
		} catch (\Exception $e) {
	        //make error log
	        log_message('error', $e);

	        $data['status']		=500;
	        $data['message']	=$this->lang->line('internal_server_error');
	        $data['data']		=array(); 

	        $this->response($data, $data['status']);
	    }
	}
}