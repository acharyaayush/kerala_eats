<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hitpay_redirect extends CI_Controller {

   function __construct(){
        parent::__construct();
        date_default_timezone_set('UTC');
    }

    public function index()
    {
    	$pageData['ref'] = $_GET['reference'];
    	$pageData['status'] = $_GET['status'];
    	$this->load->view('hitpay_redirect',$pageData);
    }

    public function success_hitpay()
	{
		
	}
	
}
