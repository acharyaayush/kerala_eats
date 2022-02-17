<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Web extends CI_Controller {

   function __construct(){
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model("Common");
    }

    public function index()
    {
        $this->load->view('web/index');
    }
    # This function is used to load the cms footer pages by page-key start
    public function cms($page_key)
    {
        if($page_key != '')
        {
            $pageData['page_data'] = $this->Common->getData('cms','*','page_key = "'.$page_key.'"');
            $page_key = strtolower(trim($page_key));
            if($page_key == 'termsandconditions')
            {
                $pageData['pageName'] = 'web/term_conditions';
                $this->load->view('terms_and_conditions',$pageData);
            }elseif($page_key == 'privacypolicy'){
                $pageData['pageName'] = 'web/privacy_policy';
                $this->load->view('privacy',$pageData);
            }elseif($page_key == 'aboutus'){
                // $pageData['pageName'] = 'web/about_us';
            }elseif($page_key == 'contactus'){
                // $pageData['pageName'] = 'web/contact_us';
            }elseif($page_key == 'faq'){
                // $pageData['pageName'] = 'web/faq';
            }
            // $pageData['pageTitle'] = 'Index'; // Pass page title every time with respective to the page
            // $this->load->view('web/masterpage',$pageData);
        }
    }
    # This function is used to load the cms footer pages by page-key end
	
}
