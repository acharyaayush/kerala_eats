<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/Bcrypt.php';
class Admin extends CI_Controller {
    function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->library('session');
        $this->load->model('Common');
        $this->lang->load('english', 'english');
        $this->id = $this->session->userdata('adminId');
        $this->logged_in_restaurant_id = $this->session->userdata('logged_in_restaurant_id');
        $this->logged_in_restaurant_name = $this->session->userdata('logged_in_restaurant_name');
        $this->fullname = $this->session->userdata('fullname');
        $this->profile_image = $this->session->userdata('profile_image');
        $this->role = $this->session->userdata('role');
        $this->load->helper('url', 'form');
        //$this->load->library('encrypt');
        $this->load->library("pagination");
        //For Sidebar product menu url , passing restaurant id and category id --------------------start-----------------------
        //get restaurant id ------
        //  if merchant is logged in then this condition will check and only merchant restaurant prodcuts will show if this blank that means super admin is logged in
        if ($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2) {
            $restaurant_id = $this->logged_in_restaurant_id;
            $resturant_query = "SELECT `id` as 'restaurant_id' FROM `restaurants` WHERE  `id` = " . $restaurant_id . " AND  `rest_status` != 3";
        } else { // IF super admin is logged in
            $resturant_query = "SELECT `id` as 'restaurant_id' FROM `restaurants` WHERE  `rest_status` != 3  ORDER BY `id` DESC LIMIT 1";
        }
        $resturant_details = $this->Common->custom_query($resturant_query, 'get');
        if (empty($resturant_details)) {
            $selected_restaurant_id = 0;
        } else {
            $selected_restaurant_id = $resturant_details[0]['restaurant_id'];
        }
        //get cateegory id----
        $category_query = "SELECT `id` as 'category_id' FROM `categories` WHERE `category_status` != 3 AND `restaurant_id` = " . $selected_restaurant_id . " ORDER BY `id` DESC LIMIT 1";
        $category_detail = $this->Common->custom_query($category_query, 'get');
        if (empty($category_detail)) {
            $selected_category_id = 0;
        } else {
            $selected_category_id = $category_detail[0]['category_id'];
        }
        //$this->session->set_flashdata('selected_restaurant_id_url', '0/all/all/all/all/all/'.$selected_restaurant_id.'/'.$selected_category_id.'');
        $this->selected_restaurant_id_url = '0/all/all/all/all/all/' . $selected_restaurant_id . '/' . $selected_category_id . '';
        //For Sidebar product menu url , passing restaurant id and category id ------------------------------END----------------------------
        // comman geting data For discount page or side menu ------START-----------_
        $resturant_details = $this->Common->getData('restaurants', 'id as restaurant_id,rest_name', 'rest_status = 1', '', '', 'id', 'DESC');
        $this->resturant_details = $resturant_details; //for discount page show restaurant list in select option
        if (!empty($resturant_details)) {
            $this->selected_restaurant_id = $resturant_details[0]['restaurant_id']; // for side menu discount menu passing parameter selected restaurant beacouse we geting discount data by selected restaurant
            
        }
        // comman geting data For discount page or side menu ------END-----------_
        //comman getting variant data from variants table for products and variant_list controller ----START-------
        // products controller - when page load then showing variant
        //variant_list controller - when add variant then table load by ajax
        if ($this->role == 2 && $this->logged_in_restaurant_id != "") {
            $varirants_query_part = 'AND restaurant_id = ' . $this->logged_in_restaurant_id . ' AND added_by = 2 '; //added by : 1 - by super admin, 2 - Merchant/Restaurant
            
        } elseif ($this->role == 1 && $this->logged_in_restaurant_id == "") {
            $varirants_query_part = "";
        } else {
            $varirants_query_part = "";
        }
        $this->comman_get_variant_detail = $this->Common->getData('variants', 'variants.*', 'variant_status != 3 ' . $varirants_query_part . '', '', '', 'variant_id', 'DESC');
        //comman getting variant data from variants table fot products and variant_list controller ----END-------
        //getting dashboard chart data ----------start--------
        $this->dashboard_earning_report_from_date = date('Y') . '-01-01';
        $this->dashboard_earning_report_to_date = date('Y') . '-' . date('m') . '-' . date('d');
        #passing current year on load
        $this->last_month_chart_array = get_dashboard_last_month_sale_chart_data($this->logged_in_restaurant_id, $this->role);
        $this->last_week_chart_array = get_dashboard_last_week_sale_chart_data($this->logged_in_restaurant_id, $this->role);
        //getting dashboard chart data ----------end--------
        
    }
    # Get wallet balance start
    # This function will get the existing wallet amount with a check of validity also.
    public function get_wallet_balance($user_id) {
        # Here we are checking validity in query itself using IFELSE
        # If valid_till != '' that means validity exists so get amount only of those whose validity is not expired hence we used UNIX_TIMESTAMP() <= valid_till
        $result = "SELECT FORMAT((sum(COALESCE(credited, 0))-sum(COALESCE(debited, 0))),2) AS wallet_balance FROM wallet WHERE IF (valid_till != 0 , UNIX_TIMESTAMP() <= valid_till AND user_id = $user_id , user_id = $user_id) ";
        $query = $this->db->query($result);
        if ($query) {
            return $query->result_array();
        } else {
            return array();
        }
    }
    # Get wallet balance end
    //User Management Function
    public function customer() {
        $data = array('title' => "Customer Management", 'pageName' => "customer");
        $this->load->view('masterpage', $data);
    }
    //Master Admin Dashboard Function
    public function index() {
        if ($this->id) {
            $data = array('title' => "Dashboard", 'pageName' => "dashboard");
            //Total Customer Count
            $pageData['total_customer'] = $this->Common->getData('users', 'count(id) as total_customer', 'role = 3'); // Role 3 = Customer
            //Total Restaurant Admin Count
            $pageData['total_restaurant_admin'] = $this->Common->getData('users', 'count(id) as total_restaurant_admin', 'role = 2 AND status != 5'); // Role 2 = Restaurant admin
            //Total Restarant Count
            $pageData['total_restaurant'] = $this->Common->getData('restaurants', 'count(id) as total_restaurant', '`rest_status NOT IN(3)'); // Total with Enable and Disable
            //Total Order Count with placed ,pending, accepted, rejected
            $pageData['total_order'] = $this->Common->getData('orders', 'count(id) as total_order', 'order_status != 9');
            //Getting Total Cancelled orders
            $pageData['total_cancel_order'] = $this->Common->getData('orders', 'count(id) as total_cancel_order', 'order_status = 4');
            //Getting Pending Total Orders
            $pageData['total_pending_order'] = $this->Common->getData('orders', 'count(id) as total_pending_order', 'order_status = 0');
            //Getting Dispatched Total Orders
            $pageData['total_dispatched_order'] = $this->Common->getData('orders', 'count(id) as total_dispatched_order', 'order_status = 3');
            //Getting Completed Total Orders
            $pageData['total_completed_order'] = $this->Common->getData('orders', 'count(id) as total_completed_order', 'order_status = 5');
            if ($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2) {
                $restaurant_id = $this->logged_in_restaurant_id;
                $query_part = ' AND restaurant_id = ' . $restaurant_id . '';
            } else {
                $query_part = "";
            }
            //total sale data--------------------START--------------------------
            $query = "SELECT count(`id`) as total_order , SUM(`dc_amount`+`sub_total`) - SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_sales FROM `orders` WHERE order_status != 2 " . $query_part;
            $sales = $this->Common->custom_query($query, 'get');
            $pageData['total_sales'] = $sales[0]['total_sales'];
            $pageData['total_sales_order'] = $sales[0]['total_order'];
            //total sale data--------------------END--------------------------
            //last month data ------------------------START---------------------
            $last_month_start_day_date = date('d-m-Y', strtotime("first day of previous month"));
            $last_month_start_date = strtotime($last_month_start_day_date . ' 00:00:00');
            $last_month_end_day_date = date("d-m-Y", strtotime("last day of previous month"));
            $last_month_end_date = strtotime($last_month_end_day_date . ' 00:00:00');
            $last_month_sale_query = 'SELECT count(`id`) as total_order , SUM(`dc_amount`+`sub_total`) - SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_sales FROM `orders` WHERE order_status != 2 AND ( `created_at` between "' . $last_month_start_date . '" AND "' . $last_month_end_date . '") ' . $query_part . '';
            $last_month_sale = $this->Common->custom_query($last_month_sale_query, 'get');
            $pageData['last_month_sale_total'] = $last_month_sale[0]['total_sales'];
            $pageData['last_month_order'] = $last_month_sale[0]['total_order'];
            //last month data --------------------------END------------------------
            #last week data ------------------------START--------------------------
            #getting last week sales
            $last_week_start_day_date = date('d-m-Y', strtotime('last sunday -7 days'));
            $last_week_start_date = strtotime($last_week_start_day_date . ' 00:00:00');
            $last_week_end_day_date = date("d-m-Y", strtotime('saturday', strtotime('last week')));
            $last_week_end_date = strtotime($last_week_end_day_date . ' 00:00:00');
            $last_week_sale_query = 'SELECT count(`id`) as total_order , SUM(`dc_amount`+`sub_total`) - SUM((`dc_amount`+`sub_total`)*(`admin_commission`))/100 as total_sales FROM `orders` WHERE order_status != 2 AND ( `created_at` between "' . $last_week_start_date . '" AND "' . $last_week_end_date . '") ' . $query_part . '';
            $last_week_sale = $this->Common->custom_query($last_week_sale_query, 'get');
            $pageData['last_week_sale_total'] = $last_week_sale[0]['total_sales'];
            $pageData['last_week_order'] = $last_week_sale[0]['total_order'];
            #last week data -----------------------END-----------------------
            $pageData['total_earning_array'] = total_earning_report_data($this->logged_in_restaurant_id, $this->role, $this->dashboard_earning_report_from_date, $this->dashboard_earning_report_to_date);
            $pageData['report_chart'] = earning_report_chart($this->logged_in_restaurant_id, $this->role, $this->dashboard_earning_report_from_date, $this->dashboard_earning_report_to_date);
            $year = date('Y');
            $pageData['total_sale_chart_array'] = get_dashboard_total_sales_according_year($this->logged_in_restaurant_id, $this->role, $year);
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = 'Dashboard';
            $pageData['pageName'] = 'dashboard';
            $this->load->view('masterpage', $pageData);
        } else {
            $this->load->view('login');
        }
    }
    //total sales chart on dashboard
    public function total_sales_chart($year) {
        $pageData['total_sale_chart_array'] = get_dashboard_total_sales_according_year($this->logged_in_restaurant_id, $this->role, $year);
        $this->load->view('dashboard-total-sales-chart', $pageData);
    }
    //report chart on dashboard
    public function earning_report_chart($view_mode, $fromdate, $todate) {
        if ($view_mode == 1) {
            $pageData['total_earning_array'] = total_earning_report_data($this->logged_in_restaurant_id, $this->role, $fromdate, $todate);
            $this->load->view('dashboard-total-earning-cancel-gross-sale', $pageData);
        } else if ($view_mode == 2) {
            $pageData['report_chart'] = earning_report_chart($this->logged_in_restaurant_id, $this->role, $fromdate, $todate);
            $this->load->view('dashboard-earning-report-chart', $pageData);
        }
    }
    //Analytics Function
    public function analytics() {
        $data = array('title' => "Analytics", 'pageName' => "analytics");
        $this->load->view('masterpage', $data);
        /*$data = array(
        'title' => "Analytics",
        'pageName' => "coming_soon"
        );
        $this->load->view('masterpage', $data);*/
    }
    //Orders Function
    public function orders($table = '', $customer_id = "", $fromdate = 'all', $todate = 'all', $delivery_handle_by = 'all', $payment_mode = 'all', $paid_status = 'all', $is_paid_to_restaurant = 'all', $order_status = 'all', $search_restaurant_id = 'all', $search_customer_id = 'all', $order_accept_type = 'all', $business_category_id = 'all', $is_cutlery_needed = 'all', $is_promocode_auto_applied = 'all', $is_promocode_auto_applied_on_delivery = 'all', $search_key = 'all', $schedule_dt = 'all', $schedule_dt_to = 'all') {
        if ($this->id != '' && $this->role == 1 || $this->role == 2) {
            //search filter -----START-----------
            $pageData['fromdate'] = $fromdate;
            $pageData['todate'] = $todate;
            $pageData['delivery_handle_by'] = $delivery_handle_by; //1 - restaurant 2 - By Kerala Eats
            $pageData['payment_mode'] = $payment_mode; // 1 : Stripe 2 : Hitpay
            $pageData['paid_status'] = $paid_status; // 0 - Unpaid and 1 - Paid
            $pageData['is_paid_to_restaurant'] = $is_paid_to_restaurant;
            $pageData['order_status'] = $order_status; // 0 - Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed  , 6 - Delete
            $pageData['selected_restaurant_id'] = $search_restaurant_id;
            $pageData['selected_customer_id'] = $search_customer_id;
            $pageData['selected_order_type'] = $order_accept_type;
            $pageData['selected_business_category_id'] = $business_category_id;
            $pageData['is_cutlery_needed'] = $is_cutlery_needed; //1  - yes , 2 -no  , by default  no
            $pageData['is_promocode_auto_applied'] = $is_promocode_auto_applied; // is Promo Code auto applied 1  - yes , 2 -no  , by default  no
            $pageData['is_promocode_auto_applied_on_delivery'] = $is_promocode_auto_applied_on_delivery; // is  auto applied Promo Code apply on delivery 1  - yes , 2 -no  , by default  no
            $pageData['schedule_dt'] = $schedule_dt;
            $pageData['schedule_dt_to'] = $schedule_dt_to;
            $search_key = urldecode($search_key);
            $search_key = trim($search_key);
            $pageData['search'] = $search_key;
            $query_part = "";
            $fromDateNew = strtotime($fromdate . ' 00:00:00');
            $toDateNew = strtotime($todate . ' 24:00:00');
            $sc_start = strtotime($schedule_dt . ' 00:00:00');
            $sc_end = strtotime($schedule_dt_to . ' 23:59:59');
            $table_data = $this->uri->segment(3);
            if ($fromdate != 'all' || $todate != 'all' || $delivery_handle_by != 'all' || $payment_mode != 'all' || $paid_status != 'all' || $is_paid_to_restaurant != 'all' || $order_status != 'all' || $search_restaurant_id != 'all' || $search_customer_id != 'all' || $order_accept_type != 'all' || $business_category_id != 'all' || $is_cutlery_needed != 'all' || $is_promocode_auto_applied != 'all' || $is_promocode_auto_applied_on_delivery != 'all' || $search_key != 'all' || $schedule_dt != 'all' || $schedule_dt_to != 'all') {
                if ($fromdate != "all" && $todate == "all") {
                    $query_part.= ' AND `orders.created_at` >= "' . strtotime($fromdate) . '"';
                }
                if ($todate != "all" && $fromdate == "all") {
                    $query_part.= ' AND `orders.created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
                }
                if ($fromdate != "all" && $todate != "all") {
                    $query_part.= ' AND (orders.created_at between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                }
                if ($delivery_handle_by != "all") {
                    $query_part.= ' AND `orders.delivery_handled_by` = "' . $delivery_handle_by . '"'; //1 - restaurant 2 - By Kerala Eats
                    
                }
                if ($payment_mode != "all") {
                    $query_part.= ' AND `orders.payment_mode` = "' . $payment_mode . '"'; //1 : Stripe 2 : Hitpay
                    
                }
                if ($paid_status != "all") {
                    $query_part.= ' AND `orders.paid_status` = "' . $paid_status . '"'; //0 - Unpaid and 1 - Paid
                    
                }
                if ($is_paid_to_restaurant != "all") {
                    $query_part.= ' AND `orders.is_paid_to_restaurant` = "' . $is_paid_to_restaurant . '"'; //	Indicates whether admin paid to restaurant manually. 1 - Yes 2 -No(means pending)
                    
                }
                if ($order_status != "all") {
                    $query_part.= ' AND `orders.order_status` = "' . $order_status . '"'; //  //  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
                    
                }
                if ($search_restaurant_id != "all") {
                    $query_part.= ' AND `orders.restaurant_id` = "' . $search_restaurant_id . '"'; //primary id of the restaurant against which order is made
                    
                }
                if ($search_customer_id != "all") {
                    $query_part.= ' AND `orders.user_id` = "' . $search_customer_id . '"'; //id of the user who placed the order
                    
                }
                if ($order_accept_type != "all") {
                    $query_part.= ' AND `orders.order_type` = "' . $order_accept_type . '"'; //Value will come from rest_accept_types table (Ex. - order now, self pickup, dine in etc.)
                    
                }
                if ($business_category_id != "all") {
                    $query_part.= ' AND `orders.business_category` = "' . $business_category_id . '"'; //	Value will come from merchant_categories table (Ex. - Food , grocery, Alcohol etc.)
                    
                }
                if ($is_cutlery_needed != "all") {
                    $query_part.= ' AND `orders.is_cutlery_needed` = "' . $is_cutlery_needed . '"'; //1 - YES 2 - No, by default no
                    
                }
                if ($is_promocode_auto_applied != "all") {
                    $query_part.= ' AND `orders.promo_subtotal_is_applied` = "' . $is_promocode_auto_applied . '"'; //	i.e. Is any promotion auto applied on item total so 1 - YES and 2 - NO
                    
                }
                if ($is_promocode_auto_applied_on_delivery != "all") {
                    $query_part.= ' AND `orders.promo_dc_is_applied` = "' . $is_promocode_auto_applied_on_delivery . '"'; //i.e. Is any promotion auto applied on delivery charges so 1 - YES and 2 - No
                    
                }
                if ($search_key != "all") {
                    $query_part.= ' AND  (`orders.order_number` LIKE "%' . $search_key . '%" OR   `orders.promo_subtotal_discounted_value` LIKE "%' . $search_key . '%" OR  `orders.promo_dc_discounted_value` LIKE "%' . $search_key . '%" OR  `rest_accept_types.name` LIKE "%' . $search_key . '%" OR  `orders.pickup_time_from` LIKE "%' . $search_key . '%"  OR  `orders.pickup_time_to` LIKE "%' . $search_key . '%"  OR  `orders.admin_commission` LIKE "%' . $search_key . '%"  OR  `orders.	restaurant_commission` LIKE "%' . $search_key . '%"  OR  `orders.delivery_handled_by` LIKE "%' . $search_key . '%" OR `merchant_categories.category_name` like  "%' . $search_key . '%"  OR  `orders.total_amount` LIKE "%' . $search_key . '%"  OR  `orders.dc_amount` LIKE "%' . $search_key . '%"  OR  `orders.sub_total` LIKE "%' . $search_key . '%"  OR  `orders.	item_quantity` LIKE "%' . $search_key . '%" OR  `orders.track_link` LIKE "%' . $search_key . '%" OR  `orders.delivery_address` LIKE "%' . $search_key . '%"  OR  `orders.delivery_name` LIKE "%' . $search_key . '%" OR  `orders.delivery_email` LIKE "%' . $search_key . '%" OR  `orders.delivery_mobile` LIKE "%' . $search_key . '%" OR  `orders.remark` LIKE "%' . $search_key . '%" OR `users.fullname` LIKE "%' . $search_key . '%" OR `users.email` LIKE "%' . $search_key . '%" OR `users.number_id` LIKE "%' . $search_key . '%" OR `restaurants.rest_name` LIKE "%' . $search_key . '%")';
                }
                #if search by Schedule Date From only
                if ($schedule_dt != "all" && $schedule_dt_to == "all") {
                    $query_part.= " AND IF (`orders`.`order_type` = '1' , `orders`.`created_at`  >= '" . $sc_start . "' , `orders`.`pickup_time_from` >= '" . $sc_start . "')";
                }
                #if search by Schedule Date To only
                if ($schedule_dt == "all" && $schedule_dt_to != "all") {
                    $query_part.= " AND IF (`orders`.`order_type` = '1' , `orders`.`created_at`  <= '" . $sc_start . "' , `orders`.`pickup_time_from` <= '" . $sc_start . "')";
                }
                #if search by both Schedule Date From and Schedule Date To only
                if ($schedule_dt != "all" && $schedule_dt_to != "all") {
                    $query_part.= " AND IF (`orders`.`order_type` = '1' , `orders`.`created_at` BETWEEN '" . $sc_start . "' AND '" . $sc_end . "' , `orders`.`pickup_time_from` BETWEEN '" . $sc_start . "' AND '" . $sc_end . "')";
                    // $query_part .= " AND IF(`orders`.`order_type` = 1 , DATE_FORMAT(FROM_UNIXTIME(`orders`.`created_at`), '%Y-%m-%d') = '".$schedule_dt."' , DATE_FORMAT(FROM_UNIXTIME(`orders`.`pickup_time_from`), '%Y-%m-%d') = '".$schedule_dt."'  )";
                    // $query_part .= " CASE WHEN `orders`.`order_type` != 1 THEN AND DATE_FORMAT(FROM_UNIXTIME(`orders`.`pickup_time_from`), '%Y-%m-%d') = '".$schedule_dt."' ELSE AND DATE_FORMAT(FROM_UNIXTIME(`orders`.`created_at`), '%Y-%m-%d') = '".$schedule_dt."' END ";
                    // $query_part .= " AND DATE_FORMAT(FROM_UNIXTIME(`orders`.`pickup_time_from`), '%Y-%m-%d') = '".$schedule_dt."'";
                    
                }
            } // End of $fromdate!='all' || $todate !='all'  || $delivery_handle_by !='all' || $payment_mode!='all' || $paid_status!='all' || $is_paid_to_restaurant!='all' || $order_status!='all' || $search_restaurant_id!='all' || $search_customer_id!='all' || $order_accept_type!='all' || $business_category_id!='all' || $is_cutlery_needed!='all' || $is_promocode_auto_applied!='all' || $is_promocode_auto_applied_on_delivery!='all' || $search_key !='all'
            // echo "QUERYPART".$query_part;die;
            //pagination ---------------------START-------------------
            // $page = ($this->uri->segment(20)) ? ($this->uri->segment(20) - 1) : 0;
            $page = ($this->uri->segment(22)) ? ($this->uri->segment(22) - 1) : 0;
            if ($page > 0) {
                $page_offset = $page * ADMIN_PER_PAGE_RECORDS;
                $pagination_query_part = ADMIN_PER_PAGE_RECORDS;
            } else {
                $page_offset = $page;
                $pagination_query_part = ADMIN_PER_PAGE_RECORDS;
            }
            //  if merchant is logged in then this condition will check and only merchant restaurant prodcuts will show if this blank that means super admin is logged in
            $query_part_for_customer = "";
            if ($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2 || $this->role == 2) {
                $restaurant_id = $this->logged_in_restaurant_id;
                $query_part.= ' AND `orders`.`restaurant_id` = "' . $restaurant_id . '"';
                $query_part_for_customer.= ' AND `orders`.`restaurant_id` = "' . $restaurant_id . '"';
            } else {
                $query_part_for_customer = "";
            }
            //customer detail page for show ing customer order details----
            //we are handling order details and customer order detail on user_details page from one controller
            if ($customer_id != 0) { //for user_details detail page
                $query_customer_order_detail = ' AND `orders.user_id` = "' . $customer_id . '"';
            } else {
                $query_customer_order_detail = "";
            }
            $pageData['orders_data'] = $this->Common->getData('orders', 'orders.*,users.number_id as user_number_id,users.fullname as customer_name,users.email,restaurants.admin_id,restaurants.rest_name,restaurants.delivery_time,rest_accept_types.name as order_type_name,merchant_categories.category_name as business_category_name,transactions.wallet_debited_value,transactions.total_amount_paid', 'orders.order_status != 9 ' . $query_part . ' ' . $query_customer_order_detail . ' ', array('users', 'restaurants', 'rest_accept_types', 'merchant_categories', 'transactions'), array('orders.user_id = users.id', 'orders.restaurant_id = restaurants.id', 'orders.order_type = rest_accept_types.id', 'orders.business_category = merchant_categories.id', 'orders.id = transactions.order_id'), 'orders.id', 'DESC', $pagination_query_part, $page_offset); //order status - 	//  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
            // echo "QUERYY".$this->db->last_query();
            // $pagination_query_part, $page_offset only for pagination records
            $total_records = count($this->Common->getData('orders', 'restaurants.rest_name,rest_accept_types.name as order_type_name,merchant_categories.category_name as business_category_name', 'orders.order_status != 9' . $query_part . '  ' . $query_customer_order_detail . ' ', array('users', 'restaurants', 'rest_accept_types', 'merchant_categories'), array('orders.user_id = users.id', 'orders.restaurant_id = restaurants.id', 'orders.order_type = rest_accept_types.id', 'orders.business_category = merchant_categories.id')));
            $url = base_url('admin/orders/0/0/' . $fromdate . '/' . $todate . '/' . $delivery_handle_by . '/' . $payment_mode . '/' . $paid_status . '/' . $is_paid_to_restaurant . '/' . $order_status . '/' . $search_restaurant_id . '/' . $search_customer_id . '/' . $order_accept_type . '/' . $business_category_id . '/' . $is_cutlery_needed . '/' . $is_promocode_auto_applied . '/' . $is_promocode_auto_applied_on_delivery . '/' . $search_key . '/' . $schedule_dt . '/' . $schedule_dt_to . '/'); //by default table value is 0 SEGMENT 3
            # Pass parameter to common pagination and create pagination function start
            $this->create_pagination($url, $total_records, ADMIN_PER_PAGE_RECORDS);
            $pageData['links'] = $this->pagination->create_links();
            $pageData['start'] = ($page * ADMIN_PER_PAGE_RECORDS) + 1;
            //pagination ---------------------END-------------------
            // getting restaurant list For search-------------START------------
            $pageData['restaurant_list'] = $this->Common->getData('restaurants', 'id,admin_id,rest_name', '  rest_status != 3', '', '', 'id', 'DESC');
            // getting restaurant list For search -------------END------------
            // getting Customer list For search-------------START------------
            $query = "SELECT `users`.`id`,`users`.`number_id`,`users`.`fullname` FROM `users` JOIN  `orders` ON `orders`. `user_id` = `users`.`id` WHERE `users`.`status` != 5 " . $query_part_for_customer . " GROUP BY `users`.`id`"; //DESC
            $pageData['customer_list'] = $this->Common->custom_query($query, "get");
            // getting Customer list For search -------------END------------
            // getting Order TYPE For search-------------START------------
            $pageData['rest_accept_types'] = $this->Common->getData('rest_accept_types', 'id,name', '   status = 1', '', '', 'id', 'DESC');
            // getting Order TYPE list For search -------------END------------
            // getting Business Category For search-------------START------------
            $pageData['merchant_categories'] = $this->Common->getData('merchant_categories', 'id,category_name', 'status = 1', '', '', 'id', 'DESC');
            // getting Business Category list For search -------------END------------
            $data = array('title' => "Orders", 'pageName' => "orders");
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = "Orders";
            $pageData['pageName'] = 'orders';
            $pageData['session_id'] = $this->id;
            if ($table_data == "2" || $customer_id != 0) {
                // if any action tiriger like, delete or change order status then is url excute by ajax
                $this->load->view('orders_list_table', $pageData); // this table will show accroding to selected role 2 = merchant, 3 = customer
                
            } else {
                $this->load->view('masterpage', $pageData);
            }
        } else {
            $this->load->view('login');
        }
    }
    //Export Orders Csv ---------------------------------START----------------------
    public function Export_Order_CSV($fromdate = 'all', $todate = 'all', $delivery_handle_by = 'all', $payment_mode = 'all', $paid_status = 'all', $is_paid_to_restaurant = 'all', $order_status = 'all', $search_restaurant_id = 'all', $search_customer_id = 'all', $order_accept_type = 'all', $business_category_id = 'all', $is_cutlery_needed = 'all', $is_promocode_auto_applied = 'all', $is_promocode_auto_applied_on_delivery = 'all', $search_key = 'all', $schedule_dt = 'all', $schedule_dt_to = 'all') {
        $query_part = "";
        $sc_start = strtotime($schedule_dt . ' 00:00:00');
        $sc_end = strtotime($schedule_dt_to . ' 23:59:59');
        if ($fromdate != 'all' || $todate != 'all' || $delivery_handle_by != 'all' || $payment_mode != 'all' || $paid_status != 'all' || $is_paid_to_restaurant != 'all' || $order_status != 'all' || $search_restaurant_id != 'all' || $search_customer_id != 'all' || $order_accept_type != 'all' || $business_category_id != 'all' || $is_cutlery_needed != 'all' || $is_promocode_auto_applied != 'all' || $is_promocode_auto_applied_on_delivery != 'all' || $search_key != 'all' || $schedule_dt != 'all' || $schedule_dt_to != 'all') {
            if ($fromdate != "all" && $todate == "all") {
                $query_part.= ' AND `orders.created_at` >= "' . strtotime($fromdate) . '"';
            }
            if ($todate != "all" && $fromdate == "all") {
                $query_part.= ' AND `orders.created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
            }
            if ($fromdate != "all" && $todate != "all") {
                $query_part.= ' AND (orders.created_at between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
            }
            if ($delivery_handle_by != "all") {
                $query_part.= ' AND `orders.delivery_handled_by` = "' . $delivery_handle_by . '"'; //1 - restaurant 2 - By Kerala Eats
                
            }
            if ($payment_mode != "all") {
                $query_part.= ' AND `orders.payment_mode` = "' . $payment_mode . '"'; //1 : Stripe 2 : Hitpay
                
            }
            if ($paid_status != "all") {
                $query_part.= ' AND `orders.paid_status` = "' . $paid_status . '"'; //0 - Unpaid and 1 - Paid
                
            }
            if ($is_paid_to_restaurant != "all") {
                $query_part.= ' AND `orders.is_paid_to_restaurant` = "' . $is_paid_to_restaurant . '"'; //	Indicates whether admin paid to restaurant manually. 1 - Yes 2 -No(means pending)
                
            }
            if ($order_status != "all") {
                $query_part.= ' AND `orders.order_status` = "' . $order_status . '"'; //  //  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
                
            }
            if ($search_restaurant_id != "all") {
                $query_part.= ' AND `orders.restaurant_id` = "' . $search_restaurant_id . '"'; //primary id of the restaurant against which order is made
                
            }
            if ($search_customer_id != "all") {
                $query_part.= ' AND `orders.user_id` = "' . $search_customer_id . '"'; //id of the user who placed the order
                
            }
            if ($order_accept_type != "all") {
                $query_part.= ' AND `orders.order_type` = "' . $order_accept_type . '"'; //Value will come from rest_accept_types table (Ex. - order now, self pickup, dine in etc.)
                
            }
            if ($business_category_id != "all") {
                $query_part.= ' AND `orders.business_category` = "' . $business_category_id . '"'; //	Value will come from merchant_categories table (Ex. - Food , grocery, Alcohol etc.)
                
            }
            if ($is_cutlery_needed != "all") {
                $query_part.= ' AND `orders.is_cutlery_needed` = "' . $is_cutlery_needed . '"'; //1 - YES 2 - No, by default no
                
            }
            if ($is_promocode_auto_applied != "all") {
                $query_part.= ' AND `orders.promo_subtotal_is_applied` = "' . $is_promocode_auto_applied . '"'; //	i.e. Is any promotion auto applied on item total so 1 - YES and 2 - NO
                
            }
            if ($is_promocode_auto_applied_on_delivery != "all") {
                $query_part.= ' AND `orders.promo_dc_is_applied` = "' . $is_promocode_auto_applied_on_delivery . '"'; //i.e. Is any promotion auto applied on delivery charges so 1 - YES and 2 - No
                
            }
            if ($search_key != "all") {
                $query_part.= ' AND  (`orders.order_number` LIKE "%' . $search_key . '%" OR   `orders.promo_subtotal_discounted_value` LIKE "%' . $search_key . '%" OR  `orders.promo_dc_discounted_value` LIKE "%' . $search_key . '%" OR  `rest_accept_types.name` LIKE "%' . $search_key . '%" OR  `orders.pickup_time_from` LIKE "%' . $search_key . '%"  OR  `orders.pickup_time_to` LIKE "%' . $search_key . '%"  OR  `orders.admin_commission` LIKE "%' . $search_key . '%"  OR  `orders.	restaurant_commission` LIKE "%' . $search_key . '%"  OR  `orders.delivery_handled_by` LIKE "%' . $search_key . '%" OR `merchant_categories.category_name` like  "%' . $search_key . '%"  OR  `orders.total_amount` LIKE "%' . $search_key . '%"  OR  `orders.dc_amount` LIKE "%' . $search_key . '%"  OR  `orders.sub_total` LIKE "%' . $search_key . '%"  OR  `orders.	item_quantity` LIKE "%' . $search_key . '%" OR  `orders.track_link` LIKE "%' . $search_key . '%" OR  `orders.delivery_address` LIKE "%' . $search_key . '%"  OR  `orders.delivery_name` LIKE "%' . $search_key . '%" OR  `orders.delivery_email` LIKE "%' . $search_key . '%" OR  `orders.delivery_mobile` LIKE "%' . $search_key . '%" OR  `orders.remark` LIKE "%' . $search_key . '%" OR `users.fullname` LIKE "%' . $search_key . '%" OR `users.email` LIKE "%' . $search_key . '%" OR `users.number_id` LIKE "%' . $search_key . '%" OR `restaurants.rest_name` LIKE "%' . $search_key . '%")';
            }
            if ($schedule_dt != "all" && $schedule_dt_to != 'all') {
                $query_part.= " AND IF (`orders`.`order_type` = '1' , `orders`.`created_at` BETWEEN '" . $sc_start . "' AND '" . $sc_end . "' , `orders`.`pickup_time_from` BETWEEN '" . $sc_start . "' AND '" . $sc_end . "')";
                // $query_part .= " AND IF(`orders`.`order_type` = 1 , DATE_FORMAT(FROM_UNIXTIME(`orders`.`created_at`), '%Y-%m-%d') = '".$schedule_dt."' , DATE_FORMAT(FROM_UNIXTIME(`orders`.`pickup_time_from`), '%Y-%m-%d') = '".$schedule_dt."'  )";
                // $query_part .= " CASE WHEN `orders`.`order_type` != 1 THEN AND DATE_FORMAT(FROM_UNIXTIME(`orders`.`pickup_time_from`), '%Y-%m-%d') = '".$schedule_dt."' ELSE AND DATE_FORMAT(FROM_UNIXTIME(`orders`.`created_at`), '%Y-%m-%d') = '".$schedule_dt."' END ";
                // $query_part .= " AND DATE_FORMAT(FROM_UNIXTIME(`orders`.`pickup_time_from`), '%Y-%m-%d') = '".$schedule_dt."'";
                
            }
        } else {
            $query_part = "";
        } // End of $fromdate!='all' || $todate !='all'.......
        // file name
        $filename = 'order_list_' . date('Ymd') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv;");
        $OrderData = $this->Common->getData('orders', 'orders.*,orders.created_at AS order_place_time ,users.*,users.number_id as user_number_id,users.fullname as customer_name,users.email,restaurants.*,restaurants.rest_name,restaurants.delivery_time,rest_accept_types.name as order_type_name,merchant_categories.category_name as business_category_name,transactions.total_amount_paid,transactions.wallet_debited_value', 'orders.order_status != 9' . $query_part . ' ', array('users', 'restaurants', 'rest_accept_types', 'merchant_categories', 'transactions'), array('orders.user_id = users.id', 'orders.restaurant_id = restaurants.id', 'orders.order_type = rest_accept_types.id', 'orders.business_category = merchant_categories.id', 'transactions.order_id = orders.id'), 'orders.id', 'DESC');
        // echo "<pre>";
        // print_r($OrderData);
        // die;
        // file creation
        $file = fopen('php://output', 'w');
        if (!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1) { // only super admin can see and update
            $is_paid_to_restaurant_column = "Is Paid to Restaurant";
        } else {
            $is_paid_to_restaurant_column = "";
        }
        # Excel Column Name
        $header = array("Order ID", "Ordered Date/time", "Schedule date/time", "Pickup date/time", "Restaurant Name", "Customer Name", "Customer ID", "Customer Number", "Customer Email Id", "Delivery Address", "Sub total", "Discount (Promotion applied)", "Delivery Charge(customer paid)", "Lalamove Delivery Charge (Original)", "Total Amount (customer paid)", "Order Preparation Time", "Order Type", "Business Type", "Payment_status", "Promo code Applied (Subtotal)", "Promo code Applied (Delivery Charge)", "Merchant Commission %", "Merchant Commission", "is_paid_to_restaurant", "Delivery Name", "Delivery Email", "Delivery Mobile No.", "Order Status", "Cancelled By", "Delivery Handled by", "Is Cutlery Needed", "Payment Mode", "Code name Applied (Subtotal)", "Code Name Applied (Delivery Charge)", "" . $is_paid_to_restaurant_column . "", "Ordering Platform");
        fputcsv($file, $header);
        if (count($OrderData) > 0) {
            foreach ($OrderData as $key => $line) {
                //	0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
                switch ($line['order_status']) {
                    case 0:
                        $status = "Placed and Pending";
                    break;
                    case 1:
                        $status = "Accepted";
                    break;
                    case 2:
                        $status = "Rejected";
                    break;
                    case 3:
                        $status = "Dispatched";
                    break;
                    case 4:
                        $status = 'Cancelled';
                    break;
                    case 5:
                        $status = 'Completed';
                    break;
                    case 6:
                        $status = 'Preparing';
                    break;
                    case 7:
                        $status = 'Ready';
                    break;
                    default:
                        $status = "Pending";
                } // switch case end
                //orderd date
                if ($line['created_at'] != '') {
                    $createdAt = date('d/m/Y', $line['created_at']);
                } else {
                    $createdAt = 'NA';
                }
                //For Cancel by 1 - cancelled by admin 2 - Auto cancelled
                if ($line['cancelled_by'] == 1) {
                    $cancelled_by = "By Admin";
                } else if ($line['cancelled_by'] == 2) {
                    $cancelled_by = "Auto Cancelled";
                }
                // For delivery handle by 1 - restaurant 2 - By Kerala Eats-----
                if ($line['delivery_handled_by'] == 1) {
                    $delivery_handled = "Restaurant";
                } else if ($line['delivery_handled_by'] == 2) {
                    $delivery_handled = "Kerala Eats";
                }
                //For payment mode  1 : Stripe 2 : Hitpay----------------------
                if ($line['payment_mode'] == 1) {
                    $payment_mode = "Stripe (Online)";
                } else if ($line['payment_mode'] == 2) {
                    $payment_mode = "Hitpay (Online)";
                } else if ($line['payment_mode'] == 3) {
                    $payment_mode = "Wallet used";
                }
                //For Promo Code auto applied
                if ($line['promo_subtotal_is_applied'] == 1) {
                    $promo_code_is_applied_status = "Yes";
                    $promo_subtotal_code_id = $line['promo_subtotal_code_id'];
                    if ($promo_subtotal_code_id != 0) {
                        $promo_code_is_applied_status = $this->Common->getData('promotions', 'code_name', 'id = ' . $promo_subtotal_code_id . '');
                        $promo_code_is_applied_status = $promo_code_is_applied_status[0]['code_name'];
                    } else {
                        $promo_code_is_applied_status = "NA";
                    }
                } else if ($line['promo_subtotal_is_applied'] == 2) {
                    $promo_code_is_applied_status = "NA";
                } else {
                    $promo_code_is_applied_status = "NA";
                }
                //For Promo Code auto applied  on delivery
                if ($line['promo_dc_is_applied'] == 1) {
                    // $promo_code_is_applied_on_delivery_status = "Yes";
                    $promo_dc_code_id = $line['promo_dc_code_id'];
                    if ($promo_dc_code_id != 0) {
                        $promo_code_is_applied_on_delivery_status = $this->Common->getData('promotions', 'code_name', 'id = ' . $promo_dc_code_id . '');
                        $promo_code_is_applied_on_delivery_status = $promo_code_is_applied_on_delivery_status[0]['code_name'];
                    } else {
                        $promo_code_is_applied_on_delivery_status = 'NA';
                    }
                } else if ($line['promo_dc_is_applied'] == 2) {
                    $promo_code_is_applied_on_delivery_status = "NA";
                } else {
                    $promo_code_is_applied_on_delivery_status = "NA";
                }
                if (!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1) { // only super admin can see and update
                    //For is paid to restauant "Indicates whether admin paid to restaurant manually. 1 - Yes 2 -No(means pending)"
                    if ($line['is_paid_to_restaurant'] == 1) {
                        $is_paid_to_restaurant_status_value = "Yes";
                    } else if ($line['is_paid_to_restaurant'] == 0) {
                        $is_paid_to_restaurant_status_value = "No";
                    }
                } else {
                    $is_paid_to_restaurant_status_value = "";
                }
                // For is cutlery  need
                if ($line['is_cutlery_needed'] == 1) {
                    $is_cutlery_needed = "Yes";
                } else if ($line['is_cutlery_needed'] == 2) {
                    $is_cutlery_needed = "No";
                } else {
                    $is_cutlery_needed = "No";
                }
                # Getting value of Order type
                $order_type = '';
                if ($line['order_type'] == 1) #1 : Order now 2 : self pickup 3 : Order for later
                {
                    $order_type = 'Order Now';
                } elseif ($line['order_type'] == 2) {
                    $order_type = 'Self Pickup';
                } elseif ($line['order_type'] == 3) {
                    $order_type = 'Order For Later';
                }
                # Getting value for Restaurant Business type
                $business_type = '';
                if ($line['business_category_name'] == 'F&B' && $line['food_type'] == 1) {
                    $business_type = 'Restaurant';
                } elseif ($line['business_category_name'] == 'F&B' && $line['food_type'] == 2) {
                    $business_type = 'Homemade';
                } elseif ($line['business_category_name'] == 2) {
                    $business_type = 'Grocery';
                } elseif ($line['business_category_name'] == 3) {
                    $business_type = 'Alcohol';
                }
                # Getting payment status value
                $payment_status = '';
                if ($line['paid_status'] == 1) {
                    $payment_status = "Success";
                } else {
                    $payment_status = "Failed";
                }
                # Getting is_paid_to_restaurant value
                if ($line['is_paid_to_restaurant'] == 0) {
                    $is_paid_to_restaurant = 'No';
                } else {
                    $is_paid_to_restaurant = 'Yes';
                }
                # Getting subtotal value
                $subtotal = $line['sub_total'];
                $total_amount = $line['total_amount_paid'] + $line['wallet_debited_value'];
                # Getting value of merchant commission
                $merchant_commission = $line['restaurant_commission'];
                $mr_com = ($subtotal * $merchant_commission) / 100;
                # Getting Discount (Promotion applied)
                // $promotion_applied = $promo_code_applied_subtotal + $promo_code_applied_dc;
                $promotion_applied = $line['promo_subtotal_discounted_value'] + $line['promo_dc_discounted_value'];
                # Getting value of $schedule_dt,$pickup_time
                # SCHEDULE TIME
                $schedule_dt = "OK";
                $order_place_time = $line['order_place_time'];
                $rest_delivery_time = $line['delivery_time'];
                # If restaurant has not set any delivery time then we need to take value from settings table
                if ($rest_delivery_time == 0 || $rest_delivery_time == '') {
                    $basic_delv_time = $this->Common->getData('settings', 'value', 'name = "basic_delivery_time"');
                    $rest_delivery_time = $basic_delv_time[0]['value'];
                }
                date_default_timezone_set('Asia/Singapore');
                $order_place_time_date = date("d-m-Y  h:i A", $order_place_time);
                $pickup_time_from = $line['pickup_time_from'];
                if ($pickup_time_from != 0) {
                    $final_pickup_time_from = date("d-m-Y  h:i A", $pickup_time_from); // convert UNIX timestamp to PHP DateTime
                    $final_pickup_date_from = date("d-m-Y", $pickup_time_from); // convert UNIX timestamp to PHP DateTime
                    $pickup_time_from_for_range = date("h:i A", $pickup_time_from);
                } else {
                    $final_pickup_time_from = "";
                    $final_pickup_date_from = date("d-m-Y", $order_place_time); // convert UNIX timestamp to PHP DateTime
                    $pickup_time_from_for_range = "";
                }
                $pickup_time_to = $line['pickup_time_to'];
                if ($pickup_time_to != 0) {
                    $final_pickup_time_to = date("d-m-Y  h:i A", $pickup_time_to); // convert UNIX timestamp to PHP DateTime
                    $final_pickup_date_to = date("d-m-Y", $pickup_time_to); // convert UNIX timestamp to PHP DateTime
                    $final_pickup_time_only_to = date("h:i A", $pickup_time_to); // convert UNIX timestamp to PHP DateTime
                    
                } else {
                    $final_pickup_time_to = "";
                    $final_pickup_date_to = date("d-m-Y", $order_place_time); // convert UNIX timestamp to PHP DateTime
                    $final_pickup_time_only_to = "";
                }
                if ($line['order_type'] == 1) # ORDER NOW
                {
                    if ($line['preparation_time_when_accepted'] == '') # if order is pending
                    {
                        $preparation_time_when_accepted = $line['preparation_time_when_ordered'];
                    } else
                    # If after  order accepted
                    {
                        $preparation_time_when_accepted = $line['preparation_time_when_accepted'];
                    }
                    $final_pickup_range = strtotime('+' . $preparation_time_when_accepted . 'minutes', $order_place_time);
                    $sch1 = strtotime('+' . $rest_delivery_time . 'minutes', $final_pickup_range);
                    $final_pickup_range = date("h:i A", $final_pickup_range);
                    $schedule_time = date("d-m-Y h:i A", $sch1);
                    // echo "sch1 is ".$sch1;
                    // echo "<br>schedule_time is ".$schedule_time;
                    // echo "<br>rest_delivery_time is ".$rest_delivery_time;
                    // echo "<br>final_pickup_range is ".$final_pickup_range;
                    // echo "<br>preparation_time_when_accepted is ".$preparation_time_when_accepted;
                    // echo "<br>order_place_time is ".$order_place_time;
                    // die;
                    
                } elseif ($line['order_type'] == 2) {
                    $final_pickup_range = $pickup_time_from_for_range;
                    $schedule_time = $final_pickup_date_from . ' ' . $pickup_time_from_for_range . ' to ' . $final_pickup_time_only_to;
                } elseif ($line['order_type'] == 3) {
                    if ($rest_delivery_time != "") {
                        $mint_convert_in_hours = intdiv($rest_delivery_time, 60) . ':' . ($rest_delivery_time % 60);
                        $pickup_time_range = strtotime($pickup_time_from_for_range) - strtotime($mint_convert_in_hours);
                        $pickup_time_range_change = new DateTime("@$pickup_time_range");
                        $final_pickup_range = $pickup_time_range_change->format('h:i A');
                    } else {
                        $final_pickup_range = $pickup_time_from_for_range;
                    }
                    $schedule_time = $final_pickup_date_from . ' ' . $pickup_time_from_for_range . ' to ' . $final_pickup_time_only_to;
                }
                # Ordering platform (1 for iOS and 2 for android)
                if ($line['ordering_platform'] == 1) {
                    $ordering_platform = "iOS";
                } elseif ($line['ordering_platform'] == 2) {
                    $ordering_platform = "Android";
                } else {
                    $ordering_platform = "NA";
                }
                # actual_dc_amount
                $lalamove_original_dc = $line['actual_dc_amount'];
                // $header = array("Order ID","Ordered Date/time","Schedule date/time","Pickup date/time","Restaurant Name","Customer Name","Customer  ID","Customer Number","Customer Email Id","Delivery Address","Sub total","Discount (Promotion applied)","Delivery Charge(customer paid)","Total Amount (customer paid)","Order Preparation Time","Order Type","Business Type","Payment_status","Promo code Applied (Subtotal)","Promo code Applied (Delivery Charge)","Merchant Commission %","Merchant Commission","is_paid_to_restaurant","Delivery Name","Delivery Email","Delivery Mobile No.","Order Status","Cancelled By","Delivery Handled by","Is Cutlery Needed","Payment Mode","Code name Applied (Subtotal)","Code Name Applied (Delivery Charge)","".$is_paid_to_restaurant_column ."");
                # Excel Column values
                $data_array = array($line['order_number'], $order_place_time_date, $schedule_time, $final_pickup_date_from . " " . $final_pickup_range, $line['rest_name'], $line['customer_name'], $line['user_number_id'], $line['mobile'], $line['email'], $line['delivery_address'], $subtotal, $promotion_applied, $line['dc_amount'], $lalamove_original_dc, $total_amount, $line['preparation_time_when_accepted'], $order_type, $business_type, $payment_status, $line['promo_subtotal_discounted_value'], $line['promo_dc_discounted_value'], $line['restaurant_commission'], $mr_com, $is_paid_to_restaurant, $line['delivery_name'], $line['delivery_email'], $line['delivery_mobile'], $status, $cancelled_by, $delivery_handled, $is_cutlery_needed, $payment_mode, $promo_code_is_applied_status, $promo_code_is_applied_on_delivery_status, $is_paid_to_restaurant_status_value, $ordering_platform);
                fputcsv($file, $data_array);
            }
        }
        fclose($file);
        exit;
    } //end of function
    //Export Orders Csv ---------------------------------END----------------------
    //-------------------VARIANT ADD IN PRODUCT --------------START----------------
    //Get All variants for select to add in  [product]---------------START------
    public function get_all_variant_selection() {
        $pageData['variant_detail'] = $this->Common->getData('variants', '*', 'variant_status = 1', '', '', 'variant_id', 'DESC');
        $this->load->view('variant_select_for_product', $pageData);
    }
    //Get All variants for select to add in  [product]---------------END------
    public function get_all_variant_type_selection($selected_variant_id = "", $selected_product_id = "", $edit_mode = "") {
        if ($edit_mode == 1) { // edit mode and need to show select varaint type according to select variant
            // getting selectd variant type which is choosed for product
            $selected_variant_type_ids = $this->Common->getData('variant_types_for_products', 'variant_type_id', '	status	 = 1 AND  variant_id = ' . $selected_variant_id . ' AND product_id = ' . $selected_product_id . '', '', '', 'variant_type_id', 'DESC');
            // need to create simple index array
            $selected_variant_type_id_array = array();
            foreach ($selected_variant_type_ids as $value) {
                # code...
                array_push($selected_variant_type_id_array, $value['variant_type_id']);
            }
            $pageData['selected_variant_type_id_array'] = $selected_variant_type_id_array;
        } else { // add time  varaint type list show only wheck select any variant
            $pageData['selected_variant_type_id_array'] = Array();
        }
        $pageData['variant_type_detail'] = $this->Common->getData('variant_types', '*', '	variant_type_status	 = 1 AND  variant_id = ' . $selected_variant_id . '', '', '', 'variant_type_id', 'DESC');
        $this->load->view('variant_type_select', $pageData);
    }
    //Add Variant --------------START----------------------
    public function add_variant_submit() {
        $variant_name = $this->db->escape_str(trim(ucfirst($this->input->post('variant_name'))));
        if ($this->role == 1) {
            $restaurant_id = ""; //  Super admin if loggin in the database it will be 0
            
        } else if ($this->role == 2) {
            $restaurant_id = $this->logged_in_restaurant_id; //restaurant id  of the restaurant table
            
        }
        if ($variant_name != "" && !empty($variant_name)) {
            $insert_array = ['variant_name' => $variant_name, 'admin_id' => $this->id, // both id will go of admin or merchant
            'restaurant_id' => $restaurant_id, 'added_by' => $this->role, //	1 - by super admin, 2 - Merchant/Restaurant
            'created_at' => time(), 'updated_at' => time() ];
            $query = 'SELECT `variant_id` FROM `variants` WHERE `variant_status` != 3 AND `variant_name` = "' . $variant_name . '"';
            $check_data_is_exist = $this->Common->custom_query($query, "get");
            if (!empty($check_data_is_exist)) {
                echo 3; // name is already exist
                
            } else {
                $insert_status = $this->Common->insertData('variants', $insert_array);
                if ($insert_status > 0) {
                    echo 1;
                } else {
                    echo 0;
                }
            }
        } else {
            echo 2; // variant name is blank
            
        }
    }
    //Add Variant --------------END----------------------
    //Add variant type --------------START----------------------
    public function add_variant_type_submit() {
        $variant_type_name = $this->db->escape_str(trim(ucfirst($this->input->post('variant_type_name'))));
        $variant_id = $this->db->escape_str(trim($this->input->post('variant_id')));
        if (!empty($variant_type_name) && !empty($variant_id)) {
            if ($this->role == 1) {
                $restaurant_id = ""; //  Super admin if loggin in or added the table it will be 0
                
            } else if ($this->role == 2) {
                $restaurant_id = $this->logged_in_restaurant_id; //restaurant id  of the
                
            }
            $insert_array = ['variant_type_name' => $variant_type_name, 'restaurant_id' => $restaurant_id, 'added_by' => $this->role, //	1 - by super admin, 2 - Merchant/Restaurant
            'variant_id' => $variant_id, 'created_at' => time(), 'created_at' => time() ];
            $query = 'SELECT `variant_type_id` FROM `variant_types` WHERE `variant_type_status` != 3 AND `variant_type_name` = "' . $variant_type_name . '" AND `variant_id` = ' . $variant_id . '';
            $check_data_is_exist = $this->Common->custom_query($query, "get");
            if (!empty($check_data_is_exist)) {
                echo 3; // name is already exist
                
            } else {
                $insert_status = $this->Common->insertData('variant_types', $insert_array);
                if ($insert_status > 0) {
                    echo 1;
                } else {
                    echo 0;
                }
            }
        } else {
            echo 2; // variant_type name is blank
            
        }
    }
    //Add variant type  --------------END----------------------
    // Add variant For Product With price------------------START-----------------
    public function add_variant_for_product() {
        $add_edit_mode = $this->db->escape_str(trim($this->input->post('add_edit_mode'))); //// 1 - for edit , blank add time
        $selected_variant_id = $this->db->escape_str(trim($this->input->post('selected_variant_id')));
        $selected_product_id = $this->db->escape_str(trim($this->input->post('selected_product_id')));
        $is_variant_mandatory = $this->db->escape_str(trim($this->input->post('is_variant_mandatory')));
        $default_variant_type_id = $this->db->escape_str(trim($this->input->post('default_variant_type_id')));
        $variant_select_type = $this->db->escape_str(trim($this->input->post('variant_select_type'))); //by default is 1(1 for single, 2 for multi select)
        $variant_select_limit = $this->db->escape_str(trim($this->input->post('variant_select_limit'))); //	"single_select" column value is 2 , and this column value is 0 then user can select unlimited variant types, but value grater then 0 , (means how many variant type can be select by user in a variant)
        $json_of_variant_type = stripslashes($this->db->escape_str(trim($this->input->post('json_of_variant_type'))));
        $array_of_variant_type = json_decode($json_of_variant_type, true);
        if ($add_edit_mode == 1) { // edit mode
            #check  which varaint type is exist and now which select
            #if exist variant type is not selected now then we need to unselect (do status unselect) from table
            //getting current selected varaint type
            $new_select_variant_type_array = array();
            foreach ($array_of_variant_type as $edit_variant) {
                array_push($new_select_variant_type_array, $edit_variant['variant_type_id']);
            }
            $exists_product_status = $this->Common->getData('variant_types_for_products', 'variant_type_id,variant_type_price', 'product_id = ' . $selected_product_id . ' AND variant_id = ' . $selected_variant_id . '', '', '', 'variant_type_product_id', 'DESC');
            foreach ($exists_product_status as $value) {
                if (in_array($value['variant_type_id'], $new_select_variant_type_array) == false) {
                    // this variant will be unselect
                    $update_array = ['status' => 2, // 1 - selected , 2 - unselected  (default its 2) // if status is 2 then it will be do 1 and same if 1  then it will be 2
                    'default_variant_status' => 0, 'single_select' => 1, 'updated_at' => time() ];
                    $edit_update_status = $this->Common->updateData('variant_types_for_products', $update_array, 'product_id = ' . $selected_product_id . ' AND variant_id = ' . $selected_variant_id . ' AND variant_type_id = ' . $value['variant_type_id'] . '');
                }
            }
            // default value update as 0  / previous one before final update and insert
            $update_array = ['default_variant_status' => 0, 'single_select' => 1, 'updated_at' => time() ];
            $edit_update_status = $this->Common->updateData('variant_types_for_products', $update_array, 'product_id = ' . $selected_product_id . ' AND variant_id = ' . $selected_variant_id . '');
        }
        if ($selected_variant_id != "" && $selected_product_id != "" && ($default_variant_type_id != "" || $default_variant_type_id == "") && ($variant_select_type != "" || $variant_select_type == "") && !empty($array_of_variant_type)) { //variant_select_type bu default it will be 1
            //check in table - 'variant_types_for_products' , variant and variant types  avaiable for selected product
            # if available then we will do only update status // 1 - selected , 2 - unselected  (default its 2)
            #if not avaiable then we will insert data with vairant id , vairant type id , product price.
            foreach ($array_of_variant_type as $value) {
                $exists_product_status = $this->Common->getData('variant_types_for_products', 'status', 'product_id = ' . $selected_product_id . ' AND variant_id = ' . $selected_variant_id . ' AND variant_type_id = ' . $value['variant_type_id'] . '', '', '', 'variant_type_product_id', 'DESC');
                if (!empty($exists_product_status)) {
                    $update_array = ['status' => 1, // 1 - selected , 2 - unselected  (default its 2) // if status is 2 then it will be do 1 and same if 1  then it will be 2
                    'variant_type_price' => $value['variant_type_price'], 'single_select' => $variant_select_type, //1 - single select 2 multi select
                    'is_mandatory' => $is_variant_mandatory, //	0 No 1 Yes
                    'updated_at' => time() ];
                    $update_status = $this->Common->updateData('variant_types_for_products', $update_array, 'product_id = ' . $selected_product_id . ' AND variant_id = ' . $selected_variant_id . ' AND variant_type_id = ' . $value['variant_type_id'] . ''); //default_variant_status = if 1 then this variant type will be set as default for product. and only one variant type will be set as default for product
                    $insert_status = 0;
                } else {
                    $insert_array = ['variant_id' => $selected_variant_id, 'variant_type_id' => $value['variant_type_id'], 'variant_type_price' => $value['variant_type_price'], 'single_select' => $variant_select_type, //1 - single select 2 multi select
                    'product_id' => $selected_product_id, 'added_by' => $this->role, 'is_mandatory' => $is_variant_mandatory, //	0 No 1 Yes
                    'status' => 1, 'created_at' => time(), 'updated_at' => time() ];
                    $insert_status = $this->Common->insertData('variant_types_for_products', $insert_array);
                    $update_status = 0;
                }
            } //foreach loop end
            //update default variant type -------START
            if ($default_variant_type_id != "") {
                $default_variant_status = 1;
                $default_query_part = 'AND variant_type_id = ' . $default_variant_type_id . '';
            } else {
                $default_variant_status = 0;
                $default_query_part = '';
            }
            $update_deafult_value = ['default_variant_status' => $default_variant_status, //default_variant_status = if 1 then this variant type will be set as default for product. and only one variant type 	echo 'dfdf';will be set as default for product
            'updated_at' => time() ];
            $default_insert_update_status = $this->Common->updateData('variant_types_for_products', $update_deafult_value, 'product_id = ' . $selected_product_id . ' AND variant_id = ' . $selected_variant_id . ' AND  status = 1 ' . $default_query_part . ''); //default_variant_status = if 1 then this variant type will be set as default for product. and only one variant type will be set as default for product
            //update default variant type -------END
            //update Multi SELECT limit -------START
            if ($variant_select_type == 2 && $variant_select_limit > 0) { //	"single_select" column value is 2 , and this column value is 0 then user can select unlimited variant types, but value grater then 0 , (means how many variant type can be select by user in a variant)
                $variant_select_limit = $variant_select_limit;
            } else {
                $variant_select_limit = 0;
            }
            $update_select_limit_value = ['multi_select_limit' => $variant_select_limit, 'updated_at' => time() ];
            $insert_update_status = $this->Common->updateData('variant_types_for_products', $update_select_limit_value, 'product_id = ' . $selected_product_id . ' AND variant_id = ' . $selected_variant_id . ' AND  status = 1'); //	"single_select" column value is 2 , and this column value is 0 then user can select unlimited variant types, but value grater then 0 , (means how many variant type can be select by user in a variant)
            //update Multi SELECT limit -------END
            if ($insert_status > 0 || $default_insert_update_status > 0 || $update_status > 0 || $insert_update_status > 0 || $edit_update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 2;
        }
    }
    // Add variant For Product With price------------------END-----------------
    //Get All variant in product if avaiable-------------------START----------------
    public function select_variant_product_detail($selected_variant_id = "", $selected_product_id = "", $edit_mode = "") { //if only view mode then  $selected_variant_id  and $edit_mode will be 0  , we will get value in edot mode $edit_mode == 1
        $variant_data_query = 'SELECT `variant_types_for_products`.`variant_id`,`variant_types_for_products`.`single_select`,`variant_types_for_products`.`multi_select_limit`, `variants`.`variant_name` FROM `variant_types_for_products` INNER JOIN `variants` ON `variants`.`variant_id` = `variant_types_for_products`.`variant_id` WHERE `product_id` = ' . $selected_product_id . ' AND `variant_types_for_products`.`status` = 1 GROUP BY `variant_types_for_products`.`variant_id` ORDER BY  `variant_types_for_products`.`variant_id`  DESC'; //status = 1 - selected, 2 - unselected	for product
        $get_variant_data = $this->Common->custom_query($variant_data_query, "get");
        if (!empty($get_variant_data)) {
            foreach ($get_variant_data as $key => $value) {
                $get_variant_type_data = $this->Common->getData('variant_types_for_products', 'variant_types_for_products.variant_type_price,variant_types_for_products.default_variant_status,variant_types_for_products.is_mandatory,variant_types.variant_type_name,', 'product_id = ' . $selected_product_id . ' AND variant_types.variant_id = ' . $value['variant_id'] . '  AND  variant_types_for_products.status = 1', array('variant_types'), array('variant_types.variant_type_id = variant_types_for_products.variant_type_id'), 'variant_types_for_products.variant_type_product_id', 'DESC'); //status = 1 - selected, 2 - unselected	for product
                if (!empty($get_variant_data)) {
                    $get_variant_data[$key]['variant_type'] = $get_variant_type_data;
                } else {
                    $get_variant_data[$key]['variant_type'] = array();
                }
            }
        }
        if ($edit_mode == 1) { //edit part
            $pageData['edit_product_variant_detail'] = $get_variant_type_data;
            $pageData['selected_variant_id_for_edit'] = $selected_variant_id;
            $pageData['variant_detail'] = $this->comman_get_variant_detail; //get all variants
            //edit mode on hold right now----------------------
            $this->load->view('variant_select_for_product', $pageData);
        } else { //view part only
            $pageData['product_variant_detail'] = $get_variant_data;
            $pageData['selected_product_id'] = $selected_product_id;
            $this->load->view('variant_show_all_variant_in_product', $pageData);
        }
    }
    //Get All variant in product if avaiable-------------------END----------------
    //Get variant name for edit ----------------------START------------
    public function get_varaint_name_for_edit($for_variant_type_edit = "") {
        $pageData['variant_detail'] = $this->Common->getData('variants', '*', 'variant_status = 1', '', '', 'variant_id', 'DESC');
        if ($for_variant_type_edit == 1) { // for edit variant type name after select variant
            if (!empty($pageData['variant_detail'])) {
                foreach ($pageData['variant_detail'] as $value) {
                    echo '<option  value="' . $value['variant_id'] . '">' . $value['variant_name'] . '</option>';
                }
            }
        } else { // for edit variant name
            $this->load->view('variant_name_edit', $pageData);
        }
    }
    //Get variant name for edit ----------------------END------------
    //Get variant name for edit ----------------------START------------
    public function get_varaint_type_name_for_edit($variant_id = "") {
        $pageData['variant_type_detail'] = $this->Common->getData('variant_types', '*', 'variant_type_status = 1 AND variant_id = ' . $variant_id . '', '', '', 'variant_type_id', 'DESC');
        $this->load->view('variant_type_name_edit', $pageData);
    }
    //Get variant name for edit ----------------------END------------
    //Edit Variant name --------------START----------------------
    public function edit_variant_name_submit() {
        $edit_vairant_name = $this->db->escape_str(trim(ucfirst($this->input->post('edit_vairant_name'))));
        $edit_variant_id = $this->db->escape_str(trim($this->input->post('edit_variant_id')));
        $query = 'SELECT `variant_id` FROM `variants` WHERE `variant_status` != 3  AND `variant_name` = "' . $edit_vairant_name . '" AND `variant_id` !=  ' . $edit_variant_id . '';
        $check_data_is_exist = $this->Common->custom_query($query, "get");
        if (!empty($check_data_is_exist)) {
            echo 3; // name is already exist
            
        } else { //new data
            $check_name_is_changed = $this->Common->getData('variants', 'variant_name', 'variant_id = ' . $edit_variant_id . ' AND `variant_status` != 3');
            if ($edit_vairant_name == $check_name_is_changed[0]['variant_name']) {
                echo 4; // nothing changed
                
            } else {
                if ($edit_vairant_name != "" && !empty($edit_vairant_name)) {
                    $update_array = ['variant_name' => $edit_vairant_name, 'updated_at' => time() ];
                    $update_status = $this->Common->updateData('variants', $update_array, 'variant_id = "' . $edit_variant_id . '"');
                    if ($update_status > 0) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                } else {
                    echo 2; // variant name is blank
                    
                }
            }
        }
    }
    //Edit Variant name--------------END----------------------
    //Get varaint type data for edit ------------------------START--------------
    public function get_varaint_type_data_for_edit() {
        $selected_variant_id = $this->db->escape_str(trim($this->input->post('selected_variant_id')));
        $selected_product_id = $this->db->escape_str(trim($this->input->post('selected_product_id')));
        $get_variant_type_data = $this->Common->getData('variant_types_for_products', 'variant_types_for_products.variant_type_price,variant_types_for_products.variant_type_id,variant_types_for_products.is_mandatory,variant_types_for_products.	single_select,variant_types_for_products.multi_select_limit,variant_types_for_products.default_variant_status,variant_types.variant_type_name,', 'product_id = ' . $selected_product_id . ' AND variant_types.variant_id = ' . $selected_variant_id . '  AND  variant_types_for_products.status = 1', array('variant_types'), array('variant_types.variant_type_id = variant_types_for_products.variant_type_id'), 'variant_types_for_products.variant_type_product_id', 'DESC'); //status = 1 - selected, 2 - unselected	for product
        if (count($get_variant_type_data) > 0) {
            echo json_encode($get_variant_type_data);
        } else {
            echo 0;
        }
    }
    //Get varaint type data for edit ------------------------END--------------
    //Edit Variant name --------------START----------------------
    public function edit_variant_type_name_submit() {
        $edit_vairant_type_name = $this->db->escape_str(trim(ucfirst($this->input->post('edit_vairant_type_name'))));
        $edit_variant_type_id = $this->db->escape_str(trim($this->input->post('edit_variant_type_id')));
        $query = 'SELECT `variant_type_id` FROM `variant_types` WHERE `variant_type_status` != 3  AND `variant_type_name` = "' . $edit_vairant_type_name . '" AND `variant_type_id` =  ' . $edit_variant_type_id . '';
        $check_data_is_exist = $this->Common->custom_query($query, "get");
        if (!empty($check_data_is_exist)) {
            echo 3; // name is already exist
            
        } else { //new data
            $check_name_is_changed = $this->Common->getData('variant_types', 'variant_type_name', 'variant_type_id = ' . $edit_variant_type_id . ' AND `variant_type_status` != 3');
            if ($edit_vairant_type_name == $check_name_is_changed[0]['variant_type_name']) {
                echo 4; // nothing changed
                
            } else {
                if ($edit_vairant_type_name != "" && !empty($edit_vairant_type_name)) {
                    $update_array = ['variant_type_name' => $edit_vairant_type_name, 'updated_at' => time() ];
                    $update_status = $this->Common->updateData('variant_types', $update_array, 'variant_type_id = "' . $edit_variant_type_id . '"');
                    if ($update_status > 0) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                } else {
                    echo 2; // variant name is blank
                    
                }
            }
        }
    }
    //Edit Variant name--------------END----------------------
    // DELETE   seleted variant which is choosed for product -----START----
    public function delete_selected_product_variant() {
        // we do only change select status not permanent delete (2 - unselected)
        $selected_variant_id = $this->db->escape_str(trim($this->input->post('selected_variant_id')));
        $selected_product_id = $this->db->escape_str(trim($this->input->post('selected_product_id')));
        $update_array = ['status' => 2, // 1 - selected , 2 - unselected  (default its 2) // if status is 2 then it will be do 1 and same if 1  then it will be 2
        'default_variant_status' => 0, 'is_mandatory' => 0, 'single_select' => 1, 'updated_at' => time() ];
        $update_status = $this->Common->updateData('variant_types_for_products', $update_array, 'product_id = ' . $selected_product_id . ' AND variant_id = ' . $selected_variant_id . '');
        if ($update_status > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
    // DELETE   seleted variant which is choosed for product -----END----
    //-------------------VARIANT ADD IN PRODUCT --------------END----------------
    //Change order status-------- START----------
    //	//  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
    public function edit_order_status() {
        $order_id = $this->db->escape_str(trim($this->input->post('order_id')));
        $order_status = $this->db->escape_str(trim($this->input->post('order_status_value')));
        $send_order_status = $order_status;
        $order_type = $this->Common->getData('orders', 'order_type', 'id = "' . $order_id . '" ');
        $order_type = $order_type[0]['order_type'];
        if ($order_id != "" && $this->id) {
            if ($order_type == 1 && $order_status == 1) # ORDER NOW AND ACCEPT STATUS That means it is order now so we will send from pending to preparing directly (i.e. 0 TO 6 ) else we will send to accepted state only (i.e. 0 to 1)
            {
                // $order_status = 6;
                $send_order_status = 6;
            }
            $update_array = [
            // 'order_status'=> $order_status,//	//  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
            'order_status' => $send_order_status, //	//  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
            'updated_at' => time() ];
            # update data in orders table
            $update_status = $this->Common->updateData('orders', $update_array, "id = " . $order_id . "");
            // after change status send mail and push notification to the customer
            $user_id = $this->Common->getData('orders', 'total_amount,order_number,user_id,delivery_email,delivery_name', 'id = "' . $order_id . '"');
            $delivery_name = $user_id[0]['delivery_name'];
            $delivery_email = $user_id[0]['delivery_email'];
            $order_number_id = $user_id[0]['order_number'];
            if ($update_status > 0) {
                //	//  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
                switch ($order_status) {
                    case 0:
                        $status = "Placed and Pending";
                    break;
                    case 1:
                        $status = "Accepted";
                    break;
                    case 2:
                        $status = "Rejected";
                    break;
                    case 3:
                        $status = "Dispatched";
                    break;
                    case 4:
                        $status = 'Cancelled';
                    break;
                    case 5:
                        $status = 'Completed';
                    break;
                    case 6:
                        $status = 'Preparing';
                    break;
                    case 7:
                        $status = 'Ready';
                    break;
                    default:
                        $status = "Pending";
                } // switch case end
                //Send mail --------------------START-------------------------
                if ($delivery_email != "") {
                    //FOR mail footer --------
                    $mail_data['name'] = trim($delivery_name);
                    $mail_data['header_title'] = APP_NAME . ' :  Order Status of ' . $order_number_id . '';
                    $mail_data['email'] = $delivery_email;
                    $mail_data['order_status'] = $status;
                    $mail_data['order_number_id'] = $order_number_id;
                    $email = $delivery_email;
                    $subject = "Order - " . $order_number_id . " is " . $status . " ";
                    # Get Social urls from Database settings table
                    $social_urls = $this->get_social_urls();
                    $mail_data['facebook_url'] = $social_urls['facebook'];
                    $mail_data['google_url'] = $social_urls['google'];
                    $mail_data['insta_url'] = $social_urls['insta'];
                    $mail_data['website_url'] = $social_urls['website'];
                    # load template view
                    $message = $this->load->view('email/order_change_status_mail_to_customer', $mail_data, TRUE);
                    //echo $message;die;
                    $mail_success_status = send_mail($email, $subject, $message);
                    # mail send code end
                    
                }
                //Send mail --------------------END-------------------------
                # send notification code------------------- start-----------------------
                // UPDATED STATUS : 	//  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
                $tokens = $this->Common->getData('users', 'device_token', "id=" . $user_id[0]['user_id']);
                $notification_data_fields = array('message' => 'Order ' . $order_number_id . ' status changed to ' . $status, 'title' => NOTIFICATION_TITLE,
                # 0 => pending , 1 => processed , 2 => Packing, 3 => shipped 4 => Completed 5 => Unfulfilled  6 => Delivered
                'order_id' => $order_id, 'notification_type' => 'ORDER_STATUS_UPDATED');
                if (!empty($tokens)) {
                    foreach ($tokens as $tk) {
                        $token = $tk['device_token'];
                        if ($token != "") {
                            sendPushNotification($token, $notification_data_fields, IOS_BUNDLE_ID_CUSTOMER);
                        }
                    }
                }
                # send notification code-------------------- end-----------------------
                $insertData = ['title' => 'Order ' . $order_number_id . ' status changed to ' . $status, 'to_user_id' => $user_id[0]['user_id'], 'type' => 1, # 1 for order related
                'order_id' => $order_id, 'is_read' => 1, 'created_at' => time(), 'updated_at' => time(), ];
                $this->Common->insertData('notifications', $insertData);
                $order_data = $this->Common->getData('orders', '*', 'id = "' . $order_id . '" ');
                # Now insert notification to Database FOR MERCHANT ID
                $merchant_id = $this->Common->getData('restaurants', 'admin_id', 'id = "' . $order_data[0]['restaurant_id'] . '"');
                $merchant_id = $merchant_id[0]['admin_id'];
                # GET device token of merchant id to send push
                $tokens = $this->Common->getData('users', 'device_token', "id=" . $merchant_id);
                $notification_data_fields = array('message' => 'Order ' . $order_number_id . ' status changed to ' . $status, 'title' => NOTIFICATION_TITLE,
                # 0 => pending , 1 => processed , 2 => Packing, 3 => shipped 4 => Completed 5 => Unfulfilled  6 => Delivered
                'order_id' => $order_id, 'notification_type' => 'ORDER_STATUS_UPDATED');
                if (!empty($tokens)) {
                    foreach ($tokens as $tk) {
                        $token = $tk['device_token'];
                        if ($token != "") {
                            sendPushNotification($token, $notification_data_fields, IOS_BUNDLE_ID_CUSTOMER);
                        }
                    }
                }
                # send notification code-------------------- end-----------------------
                $insertData = ['title' => 'Order ' . $order_number_id . ' status changed to ' . $status, 'to_user_id' => $merchant_id, 'type' => 1, # 1 for order related
                'order_id' => $order_id, 'is_read' => 1, 'created_at' => time(), 'updated_at' => time(), ];
                $this->Common->insertData('notifications', $insertData);
                # Now we need to generate lalamove quotation and place order to lalamove
                if ($order_status == 1) # Only when admin ACCEPTS order 1 - lalamove place order 2 - send cashback to user
                {
                    $order_type = $order_data[0]['order_type'];
                    $restaurant_id = $order_data[0]['restaurant_id'];
                    $user_id = $order_data[0]['user_id'];
                    $sr_order_number = $order_data[0]['order_number'];
                    $pickup_time = $order_data[0]['pickup_time_from'];
                    # We need to check for preparation_time_when_accepted if it is empty then send preparation_time_when_accepted same as preparation_time_when_ordered else nothing need to do
                    if ($order_data[0]['preparation_time_when_accepted'] == '') {
                        $pre_time = $order_data[0]['preparation_time_when_ordered'];
                        $this->Common->updateData('orders', array('preparation_time_when_accepted' => $pre_time), 'id = "' . $order_id . '"');
                    }
                    $some_checks = $this->Common->getData('restaurants', '*', 'id = "' . $restaurant_id . '"');
                    if ($order_type != 2 && $some_checks[0]['delivery_handled_by'] == 2) # That is when it is not a Self pickup order and delivery need to be handeled by Kerala eats
                    {
                        # Get requesterContact information
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
                        # requesterContact : Contact person at pick up point
                        # stops : The index of waypoint in stops this information associates with, has to be >= 1, since the first stop's Delivery Info is tided to requesterContact
                        if ($order_type == 1) # Order now so dont pass scheduleAt
                        {
                            $lalamove_support_number = LALAMOVE_SUPPORT_NUMBER;
                            $body = array(
                            // "scheduleAt" => 'asaas', // ISOString with the format YYYY-MM-ddTHH:mm:ss.000Z at UTC time
                            "serviceType" => "MOTORCYCLE", // string to pick the available service type
                            "specialRequests" => array(), // array of strings available for the service type
                            "requesterContact" => array("phone" => $req_phone, // Phone number format must follow the format of your country
                            "name" => $lalamove_rest_details[0]['rest_name'],), "stops" => array(array("location" => array("lat" => $lalamove_rest_details[0]['latitude'], "lng" => $lalamove_rest_details[0]['longitude']), "addresses" => array("en_SG" => array("displayString" => $lalamove_rest_details[0]['rest_pin_address'] . ", " . $lalamove_rest_details[0]['rest_unit_number'] . ", " . $lalamove_rest_details[0]['rest_postal_code'], "country" => "SG"
                            // Country code must follow the country you are at
                            ))), array("location" => array("lat" => $delivery_latitude, "lng" => $delivery_longitude), "addresses" => array("en_SG" => array("displayString" => $delivery_address . ", " . $unit_number . ", " . $postal_code, "country" => "SG"
                            // Country code must follow the country you are at
                            )))), "deliveries" => array(array("toStop" => 1, "toContact" => array("phone" => $to_stop_phone, // Phone number format must follow the format of your country
                            "name" => $delivery_name,), "remarks" => "1. Kerala Eats Food Order ID: [" . $sr_order_number . "] \n2. Customer Name: " . $delivery_name . " \n3. Support Number: " . $lalamove_support_number . "\n4. Tips pay by Kerala Eats")));
                            // echo "<pre> 3020";
                            
                        } else # 3 : Order for later
                        {
                            $lalamove_support_number = LALAMOVE_SUPPORT_NUMBER;
                            // $pickup_time = $pickup_time - 27900; # 8 hours and 15 minutes minus
                            // $start = new DateTime(date('r', $pickup_time));
                            // $start = $start->format('Y-m-d\TH:i:s\Z');
                            $less_15_mint = $pickup_time - 900; //we need to less 15 mint
                            $start = new DateTime(date('r', $less_15_mint));
                            $start = $start->format('Y-m-d\TH:i:s\Z');
                            $final_pickup_time = $start;
                            $body = array("scheduleAt" => $final_pickup_time, // ISOString with the format YYYY-MM-ddTHH:mm:ss.000Z at UTC time ## We will make order 15 minutes before for lalamove
                            "serviceType" => "MOTORCYCLE", // string to pick the available service type
                            "specialRequests" => array(), // array of strings available for the service type
                            "requesterContact" => array("phone" => $req_phone, // Phone number format must follow the format of your country
                            "name" => $lalamove_rest_details[0]['rest_name'],), "stops" => array(array("location" => array("lat" => $lalamove_rest_details[0]['latitude'], "lng" => $lalamove_rest_details[0]['longitude']), "addresses" => array("en_SG" => array("displayString" => $lalamove_rest_details[0]['rest_pin_address'] . ", " . $lalamove_rest_details[0]['rest_unit_number'] . ", " . $lalamove_rest_details[0]['rest_postal_code'], "country" => "SG"
                            // Country code must follow the country you are at
                            ))), array("location" => array("lat" => $delivery_latitude, "lng" => $delivery_longitude), "addresses" => array("en_SG" => array("displayString" => $delivery_address . ", " . $unit_number . ", " . $postal_code, "country" => "SG"
                            // Country code must follow the country you are at
                            )))), "deliveries" => array(array("toStop" => 1, "toContact" => array("phone" => $to_stop_phone, // Phone number format must follow the format of your country
                            "name" => $delivery_name,), "remarks" => "1. Kerala Eats Food Order ID: " . $sr_order_number . " \n2. Customer Name: " . $delivery_name . " \n3. Support Number: " . $lalamove_support_number . "\n4. Tips pay by Kerala Eats")));
                        }
                        // echo "<pre> 5390 line number";
                        // echo json_encode($body);
                        // print_r($body);
                        // die;
                        # Now we need to get some information regarding lalamove ordering
                        $lalamove_order_response = $this->lalamove_quotation_place_order($body);
                        // echo "<pre> FINAL LALAMOE RESPNSE ";
                        // print_r($lalamove_order_response);
                        // Sample response : array('lalamove_order_id' => $lalamove_order_id , 'lalamove_order_amount' => $amount,'failed_reason' => '' , 'lalamove_track_link' => $track_link);
                        /*Array
                        (
                        [lalamove_order_id] => 172800508026
                        [lalamove_order_amount] => 11.80
                        [failed_reason] =>
                        [lalamove_track_link] => https://share.sandbox.lalamove.com?SG100210602153645628310010070780999&lang=en_SG&version=2&sign=471d582a8cbc30d6172d546bb67eab8d
                        )*/
                        if ($lalamove_order_response['failed_reason'] == '') {
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
                    $update_array = ['order_number' => $sr_order_number, 'lalamove_order_id' => $lalamove_order_id, 'track_link' => $track_link, 'lalamove_order_status' => $lalamove_order_status, 'lalamove_order_failed_reason' => $lalamove_order_failed_reason, ];
                    $this->Common->updateData('orders', $update_array, 'id = "' . $order_id . '"');
                    /* Lalamove part ends here */
                }
                // echo "order status".$order_status;
                if ($order_status == 5) # COMPLETED
                {
                    $cashback_user_id = $order_data[0]['user_id'];
                    $sub_total = $order_data[0]['sub_total'];
                    $dc_amount = $order_data[0]['dc_amount'];
                    $amount = $sub_total + $dc_amount;
                    # Make Mendatory entry for order cashback when admin accepts the Order
                    $cashback_val = $this->getOrderCashbackValue($amount);
                    $cashback_val = number_format($cashback_val, 2, '.', '');
                    # Valid till
                    $valid_till = $this->Common->getData('settings', '*', 'name = "cashback_validity"');
                    $valid_till = $valid_till[0]['value'];
                    $valid_till = strtotime("+$valid_till days");
                    $insert_cashback = ['user_id' => $cashback_user_id, 'order_id' => $order_id, 'wallet_date' => time(), 'debited' => 0, 'credited' => $cashback_val, 'type' => 1, 'added_by' => 1, # as it is a cashback on order that means added by admin (kerala eats)
                    'valid_till' => $valid_till, 'created_at' => time(), 'updated_at' => time(), ];
                    // echo "cashback insert print<pre>";
                    // print_r($insert_cashback);
                    // die;
                    $this->Common->insertData('wallet', $insert_cashback);
                    # Now again send another notification for cashback received to user
                    $notification_data_fields = array('message' => 'Received Cashback of S$' . $cashback_val . ' for order ' . $order_number_id, 'title' => NOTIFICATION_CASHBACK_RECVD, 'notification_type' => 'ORDER_CASHBACK');
                    $dev_tokens = $this->Common->getData('users', 'device_token', "id=" . $user_id[0]['user_id']);
                    if (!empty($dev_tokens)) {
                        foreach ($dev_tokens as $tk) {
                            $token = $tk['device_token'];
                            if ($token != "") {
                                sendPushNotification($token, $notification_data_fields, IOS_BUNDLE_ID_CUSTOMER);
                            }
                        }
                    }
                    # Now insert notification to Database FOR MERCHANT ID
                    $insertData = ['title' => 'Received Cashback of S$' . $cashback_val . ' for order ' . $order_number_id, 'to_user_id' => $cashback_user_id, 'type' => 1, # 1 for order related
                    'order_id' => $order_id, 'is_read' => 1, 'created_at' => time(), 'updated_at' => time(), ];
                    $this->Common->insertData('notifications', $insertData);
                }
                // echo "111111111111111";
                // die;
                if ($order_status == 4) { #CANCELLED
                    $this->Common->updateData('orders', array('cancelled_by' => $this->role), "id = " . $order_id . ""); //for who is  cancel order
                    // customer paid amount will be add in to  customer wallet
                    //wallet -------------------START----------------------
                    $insert_wallet_table = [
                    // 'user_id' =>  $user_id ,
                    'user_id' => $user_id[0]['user_id'], 'order_id' => $order_id, 'wallet_date' => time(),
                    // 'credited' =>  $total_amount,
                    'credited' => $user_id[0]['total_amount'], 'type' => 2, //1 - Cashback 2 - Money Added 3 debited
                    'added_by' => $this->role, //1 - By Admin 2 - By Customer
                    'created_at' => time(), 'updated_at' => time(), ];
                    $this->Common->insertdata('wallet', $insert_wallet_table);
                    // echo 1;
                    //wallet -------------------END----------------------
                    
                }
                echo 1; //success for updated status
                
            } else {
                echo 0;
            }
        } else {
            echo 2; //// order id not pass
            
        }
    }
    // 0 - Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed  , 6 - Delete
    //Change order status  ------ END------
    // order  Delete -------- START----------
    public function delete_order($selected_role = "") {
        $order_id = $this->db->escape_str(trim($this->input->post('order_id')));
        $update_array = ['order_status' => 9, //  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
        'updated_at' => time() ];
        # update data in orders table
        $update_status = $this->Common->updateData('orders', $update_array, "id = " . $order_id . "");
        if ($update_status > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
    //order  Delete ------ END------
    // change status of is_paid_to_restaurant  -------- START----------
    public function is_paid_to_restaurant_status_update($selected_role = "") {
        $order_id = $this->db->escape_str(trim($this->input->post('order_id')));
        $is_paid_to_restaurant_status_value = $this->db->escape_str(trim($this->input->post('is_paid_to_restaurant_status_value')));
        if ($order_id != "") {
            $update_array = ['is_paid_to_restaurant' => $is_paid_to_restaurant_status_value, // //For is paid to restauant "Indicates whether admin paid to restaurant manually. 1 - Yes 2 -No(means pending)"
            'updated_at' => time() ];
            # update data in orders table
            $update_status = $this->Common->updateData('orders', $update_array, "id = " . $order_id . "");
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 2; // order id is missing
            
        }
    }
    // change status of is_paid_to_restaurant  -------- END----------
    // comman function for getting final order  items------------START--------
    public function comman_show_orderd_products_function($order_id = "", $selected_product_id = "", $load_mode = "") {
        // when  admin will customize orderd data means at the time of load mode 2 , will get selected_product_id value other wise it will be blank
        if ($selected_product_id != "") {
            $query_part = 'AND  product_id = ' . $selected_product_id . '';
        } else {
            $query_part = ""; // on first load  we will get all orderd data
            
        }
        // getting Ordered Products -------------START------------
        $order_product_details = $this->Common->getData('order_product_details', 'order_product_details.order_id,order_product_details.product_id,order_product_details.product_quantity,order_product_details.product_unit_price,order_product_details.product_name,products.product_image,products.category_id', 'order_id = "' . $order_id . '" AND status != 1 ' . $query_part . '', array('products'), array('products.id = order_product_details.product_id')); // '.$query_part.' //status = 1- delete , default - 0
        $product_detail_array = $order_product_details;
        # Now we will check  any variant available , orderd for product is
        if (!empty($product_detail_array)) {
            foreach ($product_detail_array as $key => $value) {
                $order_product_varirant_query = "SELECT order_product_variant_details.variant_name,order_product_variant_details.variant_type_name,variant_types.variant_type_id,variant_types.variant_id,order_product_variant_details.variant_price
						FROM order_product_variant_details 
						INNER JOIN orders ON orders.id = order_product_variant_details.order_id 
						INNER JOIN `variants` ON `variants`.`variant_id` = `order_product_variant_details`.`variant_id` 
						INNER JOIN `variant_types` ON `variant_types`.`variant_type_id` = `order_product_variant_details`.`variant_type_id` 
						WHERE   `order_product_variant_details`.`status` !=1 AND `order_product_variant_details`.`order_id` = " . $order_id . " AND `order_product_variant_details`.`product_id` = " . $value['product_id']; //`order_product_variant_details`.`status` = 1 for delete
                $order_product_varirant_data = $this->Common->custom_query($order_product_varirant_query, 'get');
                if (count($order_product_varirant_data) > 0) {
                    $product_detail_array[$key]['variants'] = $order_product_varirant_data;
                } else {
                    $product_detail_array[$key]['variants'] = array();
                }
            }
        }
        return $product_detail_array;
        // getting Ordered Products -------------END------------
        
    }
    // comman function for getting final order  items------------END--------
    //Calcuate Delivery Charges -------------------START---------------------
    public function calculate_delivery_charge($restaurant_id = "") {
        # Check for Delivery Charge start
        # Here we are currently doing when delivery is handled by the restaurant.
        # Table: restaurants - Column : delivery_handled_by (DB_del_handel_status 1 - restaurant 2 - By Kerala Eats ie lalamove) and column per_km_charge value
        # First we have to pass lat long of restaturant and lat long of the destination to calculate distance between two lat longs
        # Get admin id of the restaurant
        $some_checks = $this->Common->getData('restaurants', 'admin_id,delivery_handled_by,per_km_charge', 'id = "' . $restaurant_id . '"');
        if ($some_checks[0]['delivery_handled_by'] == 1) # DB_del_handel_status 1 - restaurant 2 - By Kerala Eats ie lalamove
        {
            $per_km_charge = $some_checks[0]['per_km_charge'];
        } else {
            $per_km_charge = PER_KM_CHARGE; // STATIC_CODE_NEED_TO_CHANGE
            
        }
        return $per_km_charge;
    }
    //Calcuate Delivery Charges -------------------END---------------------
    //Order Single Function-------------------------------------
    public function order_single($order_id = "") {
        if ($this->id && ($this->role == 1 || $this->role == 2)) {
            // $pageData['single_order_data'] = $this->Common->getData('orders','orders.*,users.number_id as user_number_id,users.fullname as customer_name,users.email,users.mobile,restaurants.rest_name,restaurants.admin_id,restaurants.delivery_time,rest_accept_types.name as order_type_name,merchant_categories.category_name as business_category_name','orders.order_status != 9 AND orders.id = '.$order_id .'',array('users','restaurants','rest_accept_types','merchant_categories'),array('orders.user_id = users.id','orders.restaurant_id = restaurants.id','orders.order_type = rest_accept_types.id','orders.business_category = merchant_categories.id'),'orders.id');//order status - 	//  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
            $pageData['single_order_data'] = $this->Common->getData('orders', 'transactions.wallet_debited_value,transactions.total_amount_paid,orders.*,users.number_id as user_number_id,users.fullname as customer_name,users.email,users.mobile,restaurants.rest_name,restaurants.admin_id,restaurants.delivery_time,rest_accept_types.name as order_type_name,merchant_categories.category_name as business_category_name', 'orders.order_status != 9 AND orders.id = ' . $order_id . '', array('users', 'restaurants', 'rest_accept_types', 'merchant_categories', 'transactions'), array('orders.user_id = users.id', 'orders.restaurant_id = restaurants.id', 'orders.order_type = rest_accept_types.id', 'orders.business_category = merchant_categories.id', 'orders.id = transactions.order_id'), 'orders.id'); //order status - 	//  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
            //get transaction id from transactions table --------------START-------------
            $order_transaction_id = $this->Common->getData('transactions', 'number', 'order_id = ' . $order_id . '');
            if (!empty($order_transaction_id)) {
                $pageData['order_transaction_id'] = $order_transaction_id[0]['number'];
            } else {
                $pageData['order_transaction_id'] = "-";
            }
            //get transaction id from transactions table --------------END-------------
            //check  if promo code applied on Sub Total-------START--------
            // if applied on product, category , restaruant
            $promo_subtotal_is_applied = $pageData['single_order_data'][0]['promo_subtotal_is_applied']; //	i.e. Is any promotion auto applied on item total so 1 - YES and 2 - NO
            $promo_code_id = $pageData['single_order_data'][0]['promo_subtotal_code_id']; //primary id of the promo code which is applied (promotion table)
            if (($promo_subtotal_is_applied == 1 || $promo_subtotal_is_applied == 2) && $promo_code_id > 0) { // on restaurant, product or category
                // get level on which  promo code applied
                $promo_code_level_id_and_mode_status = $this->Common->getData('promotions', 'level_id,	promotion_mode_status', 'id = ' . $promo_code_id . '');
                if (!empty($promo_code_level_id_and_mode_status)) {
                    $promotion_mode_status = $promo_code_level_id_and_mode_status[0]['promotion_mode_status']; // - 1- Promo Code, 2- Discount, 3 - Referral
                    
                } else {
                    $promotion_mode_status = "";
                }
                if ($promotion_mode_status == 1 || $promotion_mode_status == 2) { // 1- promo code and //2-  discount
                    //getting promo code name, type,discount_value and minimum value-----START----
                    $pageData['promo_code_if_applied_on_subtotal'] = $this->Common->getData('promotions', 'promo_type,code_name,discount_value,min_value,max_discount', 'id = ' . $promo_code_id . '');
                } /*else if($promotion_mode_status == 3){// Referral
                // as for now we dont check refferal
                }*/
                //getting promo code name, type , ,discount_value and minimum value-----END----
                
            } else {
                $pageData['promo_code_if_applied_data'] = "";
            }
            //check  if promo code applied on Sub Total-------END--------
            //check  if promo code applied on delivery charges -------------START--------
            $promo_dc_is_applied = $pageData['single_order_data'][0]['promo_dc_is_applied']; //i.e. Is any promotion auto applied on delivery charges so 1 - YES and 2 - No
            if ($promo_dc_is_applied == 1) {
                $promo_dc_code_id = $pageData['single_order_data'][0]['promo_dc_code_id']; //	primary id of the promo code which is applied
                //getting promo code name, type,discount_value and minimum value-----START----
                $pageData['promo_code_if_applied_on_delivery'] = $this->Common->getData('promotions', 'promo_type,code_name,discount_value,min_value,max_delivery_discount', 'id = ' . $promo_dc_code_id . '');
                //getting promo code name, type , ,discount_value and minimum value-----END----
                
            } else {
                $pageData['promo_code_if_applied_on_delivery'] = "";
            }
            //check  if promo code applied on delivery charges -------------END--------
            // getting Ordered Products -------------START------------
            $pageData['order_product_details'] = $this->comman_show_orderd_products_function($order_id, '', '1'); //order id,selected_product_id (Dont need to give on first time view), load mode check
            // getting Ordered Products -------------END------------
            //getting only category for Add item time-------------START-------------------
            $restaurant_id = $pageData['single_order_data']['0']['restaurant_id'];
            //create array for getting selected orderd category for showing for first time modal open
            $product_id_with_variant_id_of_orderd_session_array = array();
            $orderd_product = array();
            foreach ($pageData['order_product_details'] as $order_items) {
                array_push($orderd_product, $order_items['product_id']);
                if (!empty($order_items['variants'])) { // if any varaint available  in order
                    foreach ($order_items['variants'] as $varaint_value) {
                        $variant_name = $varaint_value['variant_name'];
                        $variant_type_name = $varaint_value['variant_type_name'];
                        $variant_type_id = $varaint_value['variant_type_id'];
                        $variant_type_price = $varaint_value['variant_price'];
                    }
                } else {
                    $variant_name = "";
                    $variant_type_name = "";
                    $variant_type_id = "";
                    $variant_type_price = "";
                }
                //$this->Common->deleteData('admin_customize_product_variant_cart','product_id ='.$order_items['product_id'].' AND variant_type_id = '.$variant_type_id.'');
                //empty data
                if ($pageData['single_order_data'][0]['checkout_status_by_admin'] == 0) {
                    $this->Common->deleteData('admin_customize_product_cart', 'order_id =' . $order_id . '');
                    $this->Common->deleteData('admin_customize_product_variant_cart', 'order_id =' . $order_id . '');
                }
            }
            $pageData['orderd_product'] = array_unique($orderd_product);
            $pageData['product_data'] = $this->Common->getData('products', 'id as product_id,	product_name,price,offer_price', 'product_status = 1 AND restaurant_id = ' . $restaurant_id . '', '', '', 'id', 'DESC');
            //getting only category for Add item time-------------END-------------------
            //Getting Delivery Charge-------------------START------------
            /*	$delivery_latitude = $user_delivery_add[0]['delivery_latitude'];
             $delivery_longitude = $user_delivery_add[0]['delivery_longitude'];*/
            //$pageData['delivery_charge'] =  $this->calculate_delivery_charge($restaurant_id);
            //Getting Delivery Charge-------------------END------------
            # Get wallet balance of customer ------start------
            $user_id = $pageData['single_order_data'][0]['user_id'];
            $wallet_balance = $this->get_wallet_balance($user_id);
            $wallet_balance = str_replace(",", "", $wallet_balance[0]['wallet_balance']);
            // $wallet_balance = (int)$wallet_balance[0]['wallet_balance'];
            $total_balance = number_format($wallet_balance, 2, '.', '');
            $pageData['wallet_balance'] = $total_balance;
            # Get wallet balance of customer ------END------
            $data = array('title' => "Order Single", 'pageName' => "order-single");
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = 'Order Single';
            $pageData['pageName'] = 'order-single';
            $this->load->view('masterpage', $pageData);
        } else {
            $this->load->view('login');
        }
    }
    // lalamove related comman function --------START---------
    public function initiate_curlfn($path, $body, $token, $region) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        //CURLOPT_URL => 'https://rest.sandbox.lalamove.com'.$path, # SANDBOX
        CURLOPT_URL => 'https://rest.lalamove.com' . $path, # LIVE
        CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 3, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_POSTFIELDS => $body, CURLOPT_HTTPHEADER => array("Content-type: application/json; charset=utf-8", "Authorization: hmac " . $token, "Accept: application/json", "X-LLM-Country: " . $region),));
        // print_r($body);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    public function initiate_curlfnget($path, $body, $token, $region) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        /* CURLOPT_URL => 'https://rest.sandbox.lalamove.com'.$path, # SANDBOX*/
        CURLOPT_URL => 'https://rest.lalamove.com' . $path, # LIVE
        CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 3, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET', CURLOPT_POSTFIELDS => $body, CURLOPT_HTTPHEADER => array("Content-type: application/json; charset=utf-8", "Authorization: hmac " . $token, "Accept: application/json", "X-LLM-Country: " . $region),));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    public function lalamove_order_deails($lalamove_order_id) {
        $key = 'be9812303d424e11811afec2dd2e627f'; #LIVE
        $secret = 'MC8CAQACBQC5IZplAgMBAAECBQC4PkhBAgMAzVECAwDm1QIDAIwhAgMAs90C'; #LIVE
        /*$key = 'da10edb6ed6936c7baccba922c308776'; #SANDBOX
         $secret = 'b3aVjAP/dvbi4vxy0PfeaBpakpPMBtlqDPgwZJJ6tQKlHoyGGm+2EFCf9AK7sQfR'; #SANDBOX*/
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
    public function lalamove_quotation_generate($body) {
        $key = 'be9812303d424e11811afec2dd2e627f'; #LIVE
        $secret = 'MC8CAQACBQC5IZplAgMBAAECBQC4PkhBAgMAzVECAwDm1QIDAIwhAgMAs90C'; #LIVE
        /*$key = 'da10edb6ed6936c7baccba922c308776'; #SANDBOX
         $secret = 'b3aVjAP/dvbi4vxy0PfeaBpakpPMBtlqDPgwZJJ6tQKlHoyGGm+2EFCf9AK7sQfR'; #SANDBOX*/
        $time = time() * 1000;
        $method = 'POST';
        $path = '/v2/quotations';
        $region = 'SG';
        $order_body = $body;
        $body = json_encode($body, true);
        $_encryptBody = '';
        $_encryptBody = "{$time}\r\n{$method}\r\n/v2/quotations\r\n\r\n{$body}";
        //$_encryptBody = $time."\r\n".$method."\r\n".$path."\r\n\r\n".json_encode((object)$body);
        $signature = hash_hmac("sha256", $_encryptBody, $secret);
        $token = $key . ':' . $time . ':' . $signature;
        // echo "<pre> BODY PRINT";
        // print_r($body);
        $curl_response = $this->initiate_curlfn($path, $body, $token, $region);
        // echo "<pre> 9406 ";
        // print_r($curl_response);
        $quotation_response = json_decode($curl_response);
        if (!empty($quotation_response)) {
            if (isSet($quotation_response->message)) {
                # That means we have some error
                // echo "LALAMOVE ORder placing failed due to ".$quotation_response->message;
                return array('lalamove_order_id' => '', 'lalamove_order_amount' => '', 'failed_reason' => $quotation_response->message, 'lalamove_track_link' => '');
                exit();
            } else {
                $amount = $quotation_response->totalFee;
                $currency = $quotation_response->totalFeeCurrency;
                return array('lalamove_order_id' => '', 'lalamove_order_amount' => $amount, 'failed_reason' => '', 'lalamove_track_link' => '');
            }
        }
    }
    public function lalamove_quotation_place_order($body) {
        $key = 'be9812303d424e11811afec2dd2e627f'; #LIVE
        $secret = 'MC8CAQACBQC5IZplAgMBAAECBQC4PkhBAgMAzVECAwDm1QIDAIwhAgMAs90C'; #LIVE
        /*$key = 'da10edb6ed6936c7baccba922c308776'; #SANDBOX
         $secret = 'b3aVjAP/dvbi4vxy0PfeaBpakpPMBtlqDPgwZJJ6tQKlHoyGGm+2EFCf9AK7sQfR'; #SANDBOX*/
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
    # lalamove related comman function --------END---------
    # This function is used to calculate distance between two lat long (One lat long is of user and second is of restaurant)
    # calculate_distance_between_latlong Start
    public function calculate_distance_between_latlong($lat1, $lat2, $lon1, $lon2, $unit = 'K') {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        // if ($unit == "K") {
        return number_format(($miles * 1.609344), 2);
        // }
        
    }
    # calculate_distance_between_latlong End
    // update order delvery address -----------------------START-------------------
    public function update_order_delivery_address_with_delivery_charge() {
        $selected_order_id = $this->db->escape_str(trim($this->input->post('selected_order_id')));
        $order_number_id = $this->db->escape_str(trim($this->input->post('order_number_id')));
        $restaurant_id = $this->db->escape_str(trim($this->input->post('ordered_restaurant_id')));
        $order_type = $this->db->escape_str(trim($this->input->post('order_type')));
        $delivery_handled_by = $this->db->escape_str(trim($this->input->post('delivery_handled_by')));
        $delivery_postal_code = $this->db->escape_str(trim($this->input->post('delivery_postal_code')));
        $delivery_unit_number = $this->db->escape_str(trim($this->input->post('delivery_unit_number')));
        $pin_address = $this->db->escape_str(trim($this->input->post('delivery_street_address')));
        $latitude = $this->db->escape_str(trim($this->input->post('delivery_latitude')));
        $longitude = $this->db->escape_str(trim($this->input->post('delivery_longitude')));
        $delivery_mobile = $this->db->escape_str(trim($this->input->post('delivery_mobile')));
        $delivery_name = $this->db->escape_str(trim($this->input->post('delivery_name')));
        $pickup_time = $this->db->escape_str(trim($this->input->post('pickup_time_from')));
        // check order and check who handling the delivery
        # LALAMOVE will be included only if $order_type is NOT selfpickup and if delivery is handeled by Kerala eats
        # Here we need to check for schedleAt. omit this if you are placing an immediate order
        # order_type = 1 : Order now 2 : self pickup 3 : Order for later 4 : Dine In
        #order type should not be self pickup , becouse in selfpicup condition delivery charge will be 0 means will not be applied
        if ($order_type != 2 && $delivery_handled_by == 2) { //delivery_handled_by by kerala eatss
            //getting restatuant detail ----- on where book orderd----start----
            $lalamove_rest_details = $this->Common->getData('restaurants', 'restaurants.rest_pin_address,restaurants.rest_name AS rest_name,users.mobile,users.latitude,users.longitude', 'restaurants.id = "' . $restaurant_id . '"', array('users'), array('users.id = restaurants.admin_id'));
            $req_phone = $lalamove_rest_details[0]['mobile'];
            $req_phone = '+65' . $req_phone;
            # requesterContact : Contact person at pick up point
            //getting restatuant detail ----- on where book orderd----end----
            //customer mobile number -----START----
            $to_stop_phone = $delivery_mobile;
            $to_stop_phone = '+65' . $to_stop_phone;
            # stops : The index of waypoint in stops this information associates with, has to be >= 1, since the first stop's Delivery Info is tided to
            //customer mobile number -----END----
            if ($order_type == 1) # Order now so dont pass scheduleAt
            {
                $lalamove_support_number = LALAMOVE_SUPPORT_NUMBER;
                $body = array(
                // "scheduleAt" => gmdate('Y-m-d\TH:i:s\Z', time() + 60 * 30), // ISOString with the format YYYY-MM-ddTHH:mm:ss.000Z at UTC time
                "serviceType" => "MOTORCYCLE", // string to pick the available service type
                "specialRequests" => array(), // array of strings available for the service type
                "requesterContact" => array("phone" => $req_phone, // Phone number format must follow the format of your country
                "name" => $lalamove_rest_details[0]['rest_name'],), "stops" => array(array("location" => array("lat" => $lalamove_rest_details[0]['latitude'], "lng" => $lalamove_rest_details[0]['longitude']), "addresses" => array("en_SG" => array("displayString" => $lalamove_rest_details[0]['rest_pin_address'], "country" => "SG"
                // Country code must follow the country you are at
                ))), array("location" => array("lat" => $latitude, "lng" => $longitude), "addresses" => array("en_SG" => array("displayString" => $pin_address, "country" => "SG"
                // Country code must follow the country you are at
                )))), "deliveries" => array(array("toStop" => 1, "toContact" => array("phone" => $to_stop_phone, // Phone number format must follow the format of your country
                "name" => $delivery_name,), "remarks" => "1. Kerala Eats Food Order ID: [" . $order_number_id . "] \n2. Customer Name: " . $delivery_name . " \n3. Support Number: " . $lalamove_support_number . "\n4. Tips pay by Kerala Eats")));
                // echo "<pre> 3020";
                
            } else # 3 : Order for later
            {
                $lalamove_support_number = LALAMOVE_SUPPORT_NUMBER;
                //echo $pickup_time;
                $less_15_mint = $pickup_time - 900; //we need to less 15 mint
                $start = new DateTime(date('r', $less_15_mint));
                $start = $start->format('Y-m-d\TH:i:s\Z');
                $final_pickup_time = $start;
                $body = array("scheduleAt" => $final_pickup_time, // ISOString with the format YYYY-MM-ddTHH:mm:ss.000Z at UTC time ## We will make order 15 minutes before for lalamove
                "serviceType" => "MOTORCYCLE", // string to pick the available service type
                "specialRequests" => array(), // array of strings available for the service type
                "requesterContact" => array("phone" => $req_phone, // Phone number format must follow the format of your country
                "name" => $lalamove_rest_details[0]['rest_name'],), "stops" => array(array("location" => array("lat" => $lalamove_rest_details[0]['latitude'], "lng" => $lalamove_rest_details[0]['longitude']), "addresses" => array("en_SG" => array("displayString" => $lalamove_rest_details[0]['rest_pin_address'], "country" => "SG"
                // Country code must follow the country you are at
                ))), array("location" => array("lat" => $latitude, "lng" => $longitude), "addresses" => array("en_SG" => array("displayString" => $pin_address, "country" => "SG"
                // Country code must follow the country you are at
                )))), "deliveries" => array(array("toStop" => 1, "toContact" => array("phone" => $to_stop_phone, // Phone number format must follow the format of your country
                "name" => $delivery_name,), "remarks" => "1. Kerala Eats Food Order ID: [" . $order_number_id . "] \n2. Customer Name: " . $delivery_name . " \n3. Support Number: " . $lalamove_support_number . "\n4. Tips pay by Kerala Eats")));
            }
            //print_r($body);
            # Now we need to get  delivery charge accroding to address
            $lalamove_order_response = $this->lalamove_quotation_generate($body);
            //print_r($lalamove_order_response);
            if ($lalamove_order_response['failed_reason'] == '') {
                $lalamove_order_amount = $lalamove_order_response['lalamove_order_amount']; //delivery charge of lalamove and for order
                
            } else {
                $lalamove_order_amount = '';
            }
            //update lalamove address detail in order table if address order address change by admin
            $update_array = ['delivery_address' => $pin_address, 'delivery_postal_code' => $delivery_postal_code, 'delivery_unit_number' => $delivery_unit_number, 'delivery_latitude' => $latitude, 'delivery_longitude' => $longitude, 'checkout_status_by_admin' => 1, 'admin_checkout_delivery_charge_if_change' => $lalamove_order_amount, 'updated_at' => time() ];
            # update data in orders table
            $update_status = $this->Common->updateData('orders', $update_array, "id = " . $selected_order_id);
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        } else if ($order_type != 2 && $delivery_handled_by == 1) { //delivery_handled_by  by restaruant
            //getting restaturant letitude and longtitude --start----
            $some_checks = $this->Common->getData('restaurants', 'admin_id,delivery_handled_by,per_km_charge,business_type AS business_category,food_type', 'id = "' . $restaurant_id . '"');
            $rest_lat_lng = $this->Common->getData('users', 'latitude,longitude', 'id = "' . $some_checks[0]['admin_id'] . '"');
            $rest_lat = $rest_lat_lng[0]['latitude'];
            $rest_lng = $rest_lat_lng[0]['longitude'];
            //getting restaturant letitude and longtitude --end----
            //calcualting distance between restaruant and deilvery address
            $distance_in_km = $this->calculate_distance_between_latlong($latitude, $rest_lat, $longitude, $rest_lng);
            // now we care getting restaruant latitude or longtitude for distanace calculate
            // restaurant lat, long
            $some_checks = $this->Common->getData('restaurants', 'admin_id,per_km_charge,business_type AS business_category,food_type', 'id = "' . $restaurant_id . '"');
            $rest_lat_lng = $this->Common->getData('users', 'latitude,longitude', 'id = "' . $some_checks[0]['admin_id'] . '"');
            $rest_lat = $rest_lat_lng[0]['latitude'];
            $rest_lng = $rest_lat_lng[0]['longitude'];
            // calcilate disatance
            $distance_in_km = $this->calculate_distance_between_latlong($latitude, $rest_lat, $longitude, $rest_lng);
            $per_km_charge = $some_checks[0]['per_km_charge'];
            if ($per_km_charge != "0.00") {
                $rest_delivery_charge = floatval($distance_in_km) * floatval($per_km_charge);
            } else {
                $rest_delivery_charge = floatval($distance_in_km) * floatval(PER_KM_CHARGE);
            }
            $update_array = ['delivery_street_address' => $pin_address, 'delivery_postal_code' => $delivery_postal_code, 'delivery_unit_number' => $delivery_unit_number, 'delivery_latitude' => $latitude, 'delivery_longitude' => $longitude, 'checkout_status_by_admin' => 1, // for checkout modal show for update delivery amount with item calculation
            'admin_checkout_delivery_charge_if_change' => $rest_delivery_charge, 'updated_at' => time() ];
            # update data in orders table
            $update_status = $this->Common->updateData('orders', $update_array, "id = " . $selected_order_id);
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 2; // self pick up
            
        }
    }
    // update order delvery address -----------------------END-------------------
    //EDIT Order track link-------------------START------------------
    public function update_order_track_link() {
        $selected_order_id = $this->db->escape_str(trim($this->input->post('selected_order_id')));
        $edit_track_link = $this->db->escape_str(trim($this->input->post('edit_track_link')));
        $lalamove_order_id = $this->db->escape_str(trim($this->input->post('lalamove_order_id')));
        $update_array = ['track_link' => $edit_track_link, 'lalamove_order_id' => $lalamove_order_id, 'updated_at' => time() ];
        # update data in orders table
        $update_status = $this->Common->updateData('orders', $update_array, "id = " . $selected_order_id);
        if ($update_status > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
    //EDIT Order track link -------------------END------------------
    //EDIT Order remark-------------------START------------------
    public function update_order_remark() {
        $selected_order_id = $this->db->escape_str(trim($this->input->post('selected_order_id')));
        $edit_remark = $this->db->escape_str(trim($this->input->post('edit_remark')));
        $update_array = ['remark' => $edit_remark, 'updated_at' => time() ];
        # update data in orders table
        $update_status = $this->Common->updateData('orders', $update_array, "id = " . $selected_order_id);
        if ($update_status > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
    //EDIT Order remark-------------------END------------------
    //EDIT Order Preparation Time After accepted -------------------START------------------
    public function update_order_preparation_time_after_accept() {
        // echo "<pre>";
        // print_r($_POST);
        $selected_order_id = $this->db->escape_str(trim($this->input->post('selected_order_id')));
        $edit_order_preparation_time = $this->db->escape_str(trim($this->input->post('edit_order_preparation_time')));
        $get_exist_preparation_time_when_accepted = $this->Common->getData("orders", "preparation_time_when_accepted", "id = " . $selected_order_id . "");
        // echo $this->db->last_query();
        // echo "otheree <br>";
        // print_r($get_exist_preparation_time_when_accepted);
        //check if preparation_time_when_accepted  is not exist (same) then it will be update other wise no will be change
        if ($get_exist_preparation_time_when_accepted[0]['preparation_time_when_accepted'] == $edit_order_preparation_time) {
            echo 2; //nothing changed
            
        } else {
            $update_array = ['preparation_time_when_accepted' => $edit_order_preparation_time, //Ex 30 so 30 minutes
            'updated_at' => time() ];
            # update data in orders table
            $update_status = $this->Common->updateData('orders', $update_array, "id = " . $selected_order_id);
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }
    //EDIT Order Preparation Time After accepted-------------------END------------------
    // ##### OLD CODE ###### GS EDIT Order update _pick_up Time -------------------START------------------
    // public function update_order_pick_up_time(){
    // 	$selected_order_id = $this->db->escape_str(trim($this->input->post('selected_order_id')));
    // 	$delivery_time = $this->db->escape_str(trim($this->input->post('delivery_time')));
    // 	$final_pickup_range_for_calculation = $this->db->escape_str(trim($this->input->post('final_pickup_range')));
    // 	$order_type = $this->db->escape_str(trim($this->input->post('order_type')));
    // 	   $edit_pick_up_from = $this->db->escape_str($this->input->post('edit_pick_up_from'));
    // 	   $final_pickup_date_from = $this->db->escape_str($this->input->post('final_pickup_date_from'));
    // 	/* if(strpos($edit_pick_up_from, 'PM') !== false){
    // 	     $final_pick_up_from_time  = str_replace("PM","",$edit_pick_up_from);
    // 	} else if(strpos($edit_pick_up_from, 'AM') !== false){
    // 	   $final_pick_up_from_time  =  str_replace("AM","",$edit_pick_up_from);
    // 	}else{
    // 		$final_pick_up_from_time  =  $edit_pick_up_from;
    // 	}*/
    // 	 $edit_pick_up_to = $this->db->escape_str($this->input->post('edit_pick_up_to'));
    // 	/*if(strpos($edit_pick_up_to, 'PM') !== false){
    // 	     $final_pick_up_to_time  = str_replace("PM","",$edit_pick_up_to);
    // 	} else if(strpos($edit_pick_up_to, 'AM') !== false){
    // 	   $final_pick_up_to_time  =  str_replace("AM","",$edit_pick_up_to);
    // 	}else{
    // 		$final_pick_up_to_time  =  $edit_pick_up_to;
    // 	}*/
    // 	 date_default_timezone_set('Asia/Singapore');
    // 	 $pickup_time_from =  strtotime($edit_pick_up_from);//ex.  25/03/2021 12:59:59
    // 	 $pickup_time_to =  strtotime($edit_pick_up_to);//ex.  25/03/2021 12:59:59
    // 	  $final_pickup_range =  strtotime($final_pickup_range_for_calculation);//ex.  25/03/2021 12:59:59
    // 	if($order_type == 2){//Self Pickup
    //        #calcualtion =  only we need to pick from  time (dont need to calcualtion)
    //         $final_pick_up_from = $pickup_time_from;
    //        $pickup_time_from_range = $pickup_time_from;
    //     }else if($order_type == 3){//Order For Later
    //         #calcualtion = pick from  time - Delivery Time
    //         if($delivery_time !=""){
    //             $delivery_time_convert_in_hours = intdiv($delivery_time, 60).':'. ($delivery_time % 60);
    //             $pickup_time_from_range = $pickup_time_from + strtotime($delivery_time_convert_in_hours);
    //            $final_pickup_time_from = new DateTime("@$pickup_time_from_range");
    //           	  $final_pickup_time_from = $final_pickup_time_from->format('H:i');
    //           	  //strtotime($final_pickup_date_from.'  '.$final_pickup_time_from);
    //         }else{
    //             $final_pickup_time_from = $pickup_time_from;
    //         }
    //          $final_pick_up_from   = strtotime($final_pickup_date_from.' '.$final_pickup_time_from);
    //     }
    //     $Pickup_To_time_block = intdiv(Pickup_To_time_block, 60).':'. (Pickup_To_time_block % 60);
    //     $pickup_time_to_after_calculate = $final_pick_up_from + strtotime($Pickup_To_time_block);
    //     //for check  --only
    //     $final_pickup_time_to = new DateTime("@$pickup_time_to_after_calculate");
    //       $final_pickup_time_to = $final_pickup_time_to->format('H:i');
    //      $final_pick_up_to =  $edit_pick_up_to.' '.$final_pickup_time_to;
    //      $final_pick_up_to =  strtotime($edit_pick_up_to.' '.$final_pickup_time_to);
    //     // $final_pick_up_to = $pickup_time_to.'  '.$pickup_time_to_after_calculate;
    //     echo strtotime('+30 minutes', $final_pick_up_from);
    // 	$update_array = [
    // 	                'pickup_time_from' => $final_pick_up_from,
    //             		  'pickup_time_to' => $final_pick_up_to,
    // 	                 'updated_at' => time()
    // 	            ];
    // 	  # update data in orders table
    // 	 $update_status =  $this->Common->updateData('rders',$update_array,"id = ".$selected_order_id);
    // 	  if($update_status > 0){
    // 	    echo 1;
    // 	  }else{
    // 	    echo 0;
    // 	  }
    //     }
    ######### NEW CODE CHS ###### (replica of above with new changes)
    public function update_order_pick_up_time() {
        $selected_order_id = $this->db->escape_str(trim($this->input->post('selected_order_id')));
        $delivery_time = $this->db->escape_str(trim($this->input->post('delivery_time')));
        $delivery_time = (int)$delivery_time;
        $final_pickup_range_for_calculation = $this->db->escape_str(trim($this->input->post('final_pickup_range')));
        $order_type = $this->db->escape_str(trim($this->input->post('order_type')));
        $edit_pick_up_from = $this->db->escape_str($this->input->post('edit_pick_up_from'));
        $final_pickup_date_from = $this->db->escape_str($this->input->post('final_pickup_date_from'));
        date_default_timezone_set('Asia/Singapore');
        $edit_pick_up_to = $this->db->escape_str($this->input->post('edit_pick_up_to'));
        $pickup_time_to = strtotime($edit_pick_up_to);
        $final_pickup_range = strtotime($final_pickup_range_for_calculation);
        if ($order_type == 2) //Self Pickup
        {
            #calcualtion =  only we need to pick from time (dont need to calcualtion)
            $final_pick_up_from = strtotime($edit_pick_up_from);
            $block = TIME_RANGE_BLOCK; # Constant
            $final_pick_up_to = strtotime('+' . $block . 'minutes', $final_pick_up_from);
        } else if ($order_type == 3) # Order For Later
        {
            #calcualtion = pick from  time - Delivery Time
            if ($delivery_time != "") {
                $final_pick_up_from = strtotime('+' . $delivery_time . 'minutes', strtotime($edit_pick_up_from));
                # Add 30 minutes as a static block value to the pickup from time to make pickup time to. Ex after calculation pickup time is 10.30 so we will add more 30 minites for pickup time to so it will become 11
                $block = TIME_RANGE_BLOCK; # Constant
                $final_pick_up_to = strtotime('+' . $block . 'minutes', $final_pick_up_from);
            } else {
                $final_pick_up_from = strtotime($edit_pick_up_from);
            }
        }
        $update_array = ['pickup_time_from' => $final_pick_up_from, 'pickup_time_to' => $final_pick_up_to, 'updated_at' => time() ];
        # update data in orders table
        $update_status = $this->Common->updateData('orders', $update_array, "id = " . $selected_order_id);
        if ($update_status > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
    //EDIT Order update _pick_up Time-------------------END------------------
    //EDIT Order update Schedule Time -------------------START------------------
    public function update_order_schedule_time() {
        $selected_order_id = $this->db->escape_str(trim($this->input->post('selected_order_id')));
        $edit_order_schedule_time = $this->db->escape_str(trim($this->input->post('edit_order_schedule_time')));
        $edit_order_schedule_time = $offline_value = str_replace('/', '-', $edit_order_schedule_time);
        $order_schedule_time = strtotime($edit_order_schedule_time); //timstamp
        $update_array = ['schedule_time' => $order_schedule_time, 'updated_at' => time() ];
        # update data in orders table
        $update_status = $this->Common->updateData('orders', $update_array, "id = " . $selected_order_id);
        if ($update_status > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
    //EDIT Order update Schedule Time-------------------END------------------
    #getting variant name
    public function common_get_variant_name($variant_id) {
        $get_variant_name = $this->Common->getData('variants', 'variant_name', 'variant_id = "' . $variant_id . '"');
        if (!empty($get_variant_name)) {
            return $get_variant_name[0]['variant_name'];
        } else {
            return $variant_name = "";
        }
    }
    public function common_get_variant_type_name($variant_type_id) {
        $get_variant_type_name = $this->Common->getData('variant_types', 'variant_type_name', 'variant_type_id = "' . $variant_type_id . '"');
        if (!empty($get_variant_type_name)) {
            return $variant_type_name = $get_variant_type_name[0]['variant_type_name'];
        } else {
            return $variant_type_name = "";
        }
    }
    //Add and Remove orderd item submit-------------------------START------------
    public function confirm_and_update_place_order() { 
        $data_for_order = stripslashes($this->db->escape_str(trim($this->input->post('data_for_order'))));
        $selected_order_id = stripslashes($this->db->escape_str(trim($this->input->post('selected_order_id'))));
        $order_data_array = json_decode($data_for_order, true);
        //echo 'exist';
        $remove_duplicate_final_order_items_array = array_unique($order_data_array, SORT_REGULAR);
        //print_r($remove_duplicate_final_order_items_array );
        $new_product_array = array();
        $new_product_varaint_array = array();
        $final_insert_update_status = array();
        //print_r($remove_duplicate_final_order_items_array);
        $delete_variant_product_id = 0;
        if ($remove_duplicate_final_order_items_array) {
            foreach ($remove_duplicate_final_order_items_array as $new_order_data) {
                $check_exist_product_and_quantity = $this->Common->getData('order_product_details', 'product_id,product_quantity', 'order_id = "' . $selected_order_id . '" AND product_id = ' . $new_order_data['product_id'] . ' AND status != 1'); // status 1  for delete
                #$check_product_and_quantity is not empty
                //print_r($check_exist_product_and_quantity);
                if (!empty($check_exist_product_and_quantity)) {
                    #check if product id is not exist then new data will be insert for order
                    #check if product id is  exist  but quantity is updated then  exist quantity will be update of orderd product.
                    //echo $check_exist_product_and_quantity[0]['product_quantity'].'==='.$new_order_data['product_quantity'];
                    if ($new_order_data['product_id'] == $check_exist_product_and_quantity[0]['product_id'] && $new_order_data['product_quantity'] != $check_exist_product_and_quantity[0]['product_quantity']) {
                        //update quantity in exststing orderd product
                        $update_order_detail_array = array('product_quantity' => $new_order_data['product_quantity'], 'updated_at' => time());
                        $update_status = $this->Common->updateData('order_product_details', $update_order_detail_array, 'product_id = ' . $new_order_data['product_id'] . ' AND order_id = ' . $selected_order_id . '');
                        if ($update_status > 0) {
                            array_push($final_insert_update_status, 1);
                        } else {
                            array_push($final_insert_update_status, 0);
                        }
                    }
                } else {
                    $get_product_name = $this->Common->getData('products', 'product_name', '	id = "' . $new_order_data['product_id'] . '"');
                    //insert new product for order
                    $insert_data = array('product_id' => $new_order_data['product_id'], 'product_name' => $get_product_name[0]['product_name'], 'product_quantity' => $new_order_data['product_quantity'], 'order_id' => $selected_order_id, 'product_unit_price' => $new_order_data['product_unit_price'], 'created_at' => time(), 'updated_at' => time());
                    $insert_status = $this->Common->insertData('order_product_details', $insert_data);
                    if ($insert_status > 0) {
                        array_push($final_insert_update_status, 1);
                    } else {
                        array_push($final_insert_update_status, 0);
                    }
                }
                $check_exist_product_variant = $this->Common->getData('order_product_variant_details', 'product_id,variant_id,variant_type_id', 'order_id = "' . $selected_order_id . '" AND product_id = ' . $new_order_data['product_id'] . '  AND variant_id = ' . $new_order_data['variant_id'] . ' AND variant_type_id = ' . $new_order_data['variant_type_id'] . ' AND status != 1');
                if (isSet($new_order_data['check_variant_selection_if_single']) && $new_order_data['variant_id'] != 0) {
                    if ($new_order_data['check_variant_selection_if_single'] == 1) {
                        //----------if variant is avaiable but not seleted ---START ----
                        //if variants is available in product but did not select then it will be delete from order_product_variant_details
                        # for we have commented on also in custom.js ( $('body').on('click', '#confirm_order_place_order', function(e) { in else part (//if in future  variant type is avaiable.......))
                        # variant selection  will be compulsory then this code may remove or you need to check
                        #delete =   as for now  will not do delete only will do status 1
                        $this->Common->updateData('order_product_variant_details', array('status' => 1), 'product_id = ' . $new_order_data['product_id'] . ' AND order_id = ' . $selected_order_id . '');
                        //----------if variant is avaiable but not seleted ---END ----
                        #if it is 1(radio) then we have to  delete previous variant  of product beacouse only one vairant can select for one product
                        $check_exist_product_variant_if_single_select = $this->Common->getData('order_product_variant_details', 'product_id,variant_id,variant_type_id', 'order_id = "' . $selected_order_id . '" AND product_id = ' . $new_order_data['product_id'] . '  AND  variant_id = ' . $new_order_data['variant_id'] . '  AND status != 1'); // AND variant_id = '.$new_order_data['variant_id'].'
                        if ($check_exist_product_variant_if_single_select > 0) {
                            //delete =   as for now  will not do delete only will do status 1
                            $delete_status = $this->Common->updateData('order_product_variant_details', array('status' => 1), 'product_id = ' . $new_order_data['product_id'] . ' AND order_id = ' . $selected_order_id . ' AND  variant_id = ' . $new_order_data['variant_id'] . ' ');
                            #getting variant name and variant type name
                            $variant_name = $this->common_get_variant_name($new_order_data['variant_id']);
                            $variant_type_name = $this->common_get_variant_type_name($new_order_data['variant_type_id']);
                            //insert single variant type  if selected for product
                            $insert_variant_data = array('order_id' => $selected_order_id, 'product_id' => $new_order_data['product_id'], 'variant_id' => $new_order_data['variant_id'], 'variant_type_id' => $new_order_data['variant_type_id'], 'variant_name' => $variant_name, 'variant_type_name' => $variant_type_name, 'variant_price' => $new_order_data['variant_type_unit_price'], 'created_at' => time(), 'updated_at' => time());
                            $insert_variant_status = $this->Common->insertData('order_product_variant_details', $insert_variant_data);
                            if ($insert_variant_status > 0) {
                                array_push($final_insert_update_status, 1);
                            }
                        }
                    } else if ($new_order_data['check_variant_selection_if_single'] == 2) {
                        #if it is 2(checkbox) then  muliple vairant can insert for one prdouct
                        //check variant if change for selected product then order_product_variant_details will be update ony
                        //check if new variant is selcted for product then new variant will be insert order_product_variant_details
                        #getting variant name and variant type name
                        $variant_name = $this->common_get_variant_name($new_order_data['variant_id']);
                        $variant_type_name = $this->common_get_variant_type_name($new_order_data['variant_type_id']);
                        if (empty($check_exist_product_variant)) {
                            //insert multi variant type if selected for product
                            $insert_variant_data = array('order_id' => $selected_order_id, 'product_id' => $new_order_data['product_id'], 'variant_id' => $new_order_data['variant_id'], 'variant_type_id' => $new_order_data['variant_type_id'], 'variant_name' => $variant_name, 'variant_type_name' => $variant_type_name, 'variant_price' => $new_order_data['variant_type_unit_price'], 'created_at' => time(), 'updated_at' => time());
                            $insert_variant_status = $this->Common->insertData('order_product_variant_details', $insert_variant_data);
                            if ($insert_variant_status > 0) {
                                array_push($final_insert_update_status, 1);
                            }
                        }
                    }
                }else if(!isSet($new_order_data['check_variant_selection_if_single'])&& $new_order_data['variant_id'] == 0 && $new_order_data['variant_type_id'] == 0){

                      $delete_status =  $this->Common->updateData('order_product_variant_details',array('status'=>1),'product_id = '.$new_order_data['product_id'].' AND order_id = '.$selected_order_id.'');
                       array_push($final_insert_update_status, 1);
                      //echo $this->db->last_query();
                }
                
                array_push($new_product_array, $new_order_data['product_id']);
                if ($new_order_data['variant_type_id'] > 0) {
                    array_push($new_product_varaint_array, $new_order_data['variant_type_id']);
                }
                //if end of remove_duplicate_final_order_items_array
                $exist_order_product = $this->Common->getData('order_product_details', 'product_id', 'order_id = "' . $selected_order_id . '" AND status != 1'); // status 1  for delete
                foreach ($exist_order_product as $product_value) {
                    if (in_array($product_value['product_id'], $new_product_array) == false) {
                        //delete orderd product
                        //delete =   as for now  will not do delete only will do status 1
                        $delete_status = $this->Common->updateData('order_product_details', array('status' => 1), 'product_id = ' . $product_value['product_id'] . ' AND order_id = ' . $selected_order_id . '');
                        if ($delete_status > 0) {
                            array_push($final_insert_update_status, 1);
                        }
                    }
                }
                // echo 'uuu'.$new_order_data['variant_id'];
                $exist_order_product_variant = $this->Common->getData('order_product_variant_details', 'variant_type_id', 'order_id = "' . $selected_order_id . '" AND status != 1'); // status 1  for delete
                // print_r($exist_order_product_variant);
                foreach ($exist_order_product_variant as $product_variant_value) {
                    if (in_array($product_variant_value['variant_type_id'], $new_product_varaint_array) == false) {
                        //delete orderd product variants
                        //delete =   as for now  will not do delete only will do status 1
                        //echo $delete_variant_product_id;
                        $delete_status = $this->Common->updateData('order_product_variant_details', array('status' => 1), 'product_id = ' . $new_order_data['product_id'] . ' AND order_id = ' . $selected_order_id . ' AND variant_type_id = ' . $product_variant_value['variant_type_id'] . ' AND ' . $new_order_data['variant_id'] . '');
                        if ($delete_status > 0) {
                            array_push($final_insert_update_status, 1);
                        }
                    }
                }
                // print_r($final_insert_update_status);
                
            } //foreach $remove_duplicate_final_order_items_array
            if (!empty($final_insert_update_status) && in_array(0, $final_insert_update_status) == false) {
                if (in_array(0, $final_insert_update_status) == false) {
                    $update_status = $this->Common->updateData('orders', array('checkout_status_by_admin' => 1), 'id = ' . $selected_order_id . '');
                    if ($update_status > 0) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                } else {
                    echo 0; //somthing went wrong
                    
                }
            } else { //
                echo 2; //nothing changed
                
            }
        } else { //els of $order_product_exist_check
            echo 3; // not exist
            
        }
    }
    //Add and Remove orderd item submit-------------------------END------------
    //Checkout Order items after Customize----------------START--------------------
    public function checkout_after_customize_submit() {
        $selected_order_id = $this->db->escape_str(trim($this->input->post('selected_order_id')));
        $subtotal_of_items = $this->db->escape_str(trim($this->input->post('subtotal_of_items_checkout')));
        $subtotal_of_delivery = $this->db->escape_str(trim($this->input->post('subtotal_of_delivery_checkout')));
        $items_total_after_promo_code_applied = $this->db->escape_str(trim($this->input->post('items_total_after_promo_code_applied')));
        $delivery_total_after_promo_code_applied = $this->db->escape_str(trim($this->input->post('delivery_total_after_promo_code_applied')));
        $grand_total = $this->db->escape_str(trim($this->input->post('grand_total_of_checkout')));
        $item_quantity = $this->db->escape_str(trim($this->input->post('item_quantity')));
        $outstanding_amount_after_customize = $this->db->escape_str(trim($this->input->post('outstanding_amount_after_customize')));
        $who_will_pay_outstanding_amount = $this->db->escape_str(trim($this->input->post('who_will_pay_outstanding_amount')));
        $customer_id = $this->db->escape_str(trim($this->input->post('customer_id')));
        $update_order_table = ['promo_subtotal_discounted_value' => $items_total_after_promo_code_applied, // if any other wise 0
        'promo_dc_discounted_value' => $delivery_total_after_promo_code_applied, // if any other wise 0
        'sub_total' => $subtotal_of_items, //after promo code applied if any
        'total_amount' => $grand_total, 'dc_amount' => $subtotal_of_delivery, //after promo code applied applied if any
        'item_quantity' => $item_quantity, 'outstanding_amount' => $outstanding_amount_after_customize, 'who_will_pay_outstanding_amount' => $who_will_pay_outstanding_amount, //2- restaurant will pay to customer, 3 - customer will pay to restaurant,default 0
        'is_paid_outstanding_amount' => 0, 'updated_by' => $this->role, 'is_order_customized' => 1, //if order customized by admin then 1 will be go here
        'checkout_status_by_admin' => 0, 'admin_checkout_delivery_charge_if_change' => 0, 'updated_at' => time(), ];
        $update_status = $this->Common->updateData('orders', $update_order_table, 'id= ' . $selected_order_id . '');
        if ($update_status > 0) {
            if ($who_will_pay_outstanding_amount == 2) { // means outstandiing will automatic add in customer wallet from restaurant side
                //wallet -------------------START----------------------
                $insert_wallet_table = ['user_id' => $customer_id, 'order_id' => $selected_order_id, 'wallet_date' => time(), 'credited' => $outstanding_amount_after_customize, 'type' => 2, //1 - Cashback 2 - Money Added 3 debited
                'added_by' => $this->role, //1 - By Admin 2 - By Customer
                'created_at' => time(), 'updated_at' => time(), ];
                $insert_status = $this->Common->insertdata('wallet', $insert_wallet_table);
                //wallet -------------------END----------------------
                if ($insert_status > 0) {
                    //update  is_paid_outstanding_amount paid status
                    $paid_status = ['outstanding_amount' => 0, 'is_paid_outstanding_amount' => 1, //1 for paid ,0  for unpaid
                    'updated_at' => time(), ];
                    $update_status = $this->Common->updateData('orders', $paid_status, 'id = ' . $selected_order_id . '');
                    if ($update_status > 0) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                } else {
                    echo 0;
                }
            } else {
                echo 1;
            }
        } else {
            echo 0;
        }
    }
    //Checkout Order items after Customize----------------END--------------------
    // For  order_select_product_variants page, to  show variants according to selected product -------START-------------
    public function select_product_variants_for_order($selected_product_id = "", $order_id = "", $varirant_edit_mode = "") {
        if ($selected_product_id != "" && $selected_product_id != "variant_mode") {
            $product_variant_query = 'SELECT `variant_types_for_products`.`variant_id`,`variant_types_for_products`.`product_id`,`variant_types_for_products`.`single_select`,`variant_types_for_products`.`multi_select_limit`,`variant_types_for_products`.`is_mandatory`,`variants`.`variant_name`,`products`.`product_name`,`products`.`price` as `product_price` FROM `variant_types_for_products` INNER JOIN `variants` ON `variants`.`variant_id` = `variant_types_for_products`.`variant_id` INNER JOIN `products` ON `products`.`id` = `variant_types_for_products`.`product_id`  WHERE `product_id` = ' . $selected_product_id . ' AND `variant_types_for_products`.`status` = 1 GROUP BY `variant_types_for_products`.`variant_id` ORDER BY  `variant_types_for_products`.`variant_id`  DESC'; //status = 1 - selected, 2 - unselected	for product
            $product_variant_data = $this->Common->custom_query($product_variant_query, "get");
            if (!empty($product_variant_data)) {
                foreach ($product_variant_data as $key => $value) {
                    $get_variant_type_data = $this->Common->getData('variant_types_for_products', 'variant_types_for_products.variant_type_price,variant_types_for_products.default_variant_status,variant_types.variant_type_name,variant_types.variant_type_id', 'product_id = ' . $selected_product_id . ' AND variant_types.variant_id = ' . $value['variant_id'] . '  AND  variant_types_for_products.status = 1', array('variant_types'), array('variant_types.variant_type_id = variant_types_for_products.variant_type_id'), 'variant_types_for_products.variant_type_product_id', 'DESC'); //status = 1 - selected, 2 - unselected	for product
                    if (!empty($product_variant_data)) {
                        $product_variant_data[$key]['variant_type'] = $get_variant_type_data;
                    } else {
                        $product_variant_data[$key]['variant_type'] = array();
                    }
                }
            }
            /*echo '<pre>';
             print_r($product_variant_data);*/
            /*	$product_variant_query ='SELECT `variant_types_for_products`.`variant_id`, `variants`.`variant_name`, `variant_types_for_products`.`product_id`, `variant_types_for_products`.`single_select`, `products`.`product_name`, `products`.`price` as `product_price` FROM `variant_types_for_products` INNER JOIN `products` ON `products`.`id` = `variant_types_for_products`.`product_id` INNER JOIN `variants` ON `variants`.`variant_id` = `variant_types_for_products`.`variant_id` WHERE `variant_types_for_products`.`product_id` =  '.$selected_product_id.' AND `variant_types_for_products`.`status` = 1 GROUP BY `variant_types_for_products`.`variant_id`';// status = 	1 - selected, 2 - unselected (selected meand variant available for the product)
            
            $product_variant_data = $this->Common->custom_query($product_variant_query,'get');
            
            # Now will get variant type
            if(!empty($product_variant_data)){
            foreach ($product_variant_data as $key => $value)
            {
            $product_variant_type_data = $this->Common->getData('variant_types','variant_types.variant_type_id,variant_types_for_products.variant_type_price,variant_types.variant_type_name','variant_types.variant_type_status = 1  AND variant_types.variant_id = '.$value['variant_id'].'',array('variant_types_for_products'),array('variant_types_for_products.variant_type_id = variant_types.variant_type_id'),'variant_types.variant_type_id','DESC');// status = 	1 - selected, 2 - unselected (selected meand variant available for the product)
            
            
            if(count($product_variant_type_data) > 0)
            {
            
            $product_variant_data[$key]['variants'] = $product_variant_type_data;
            }else
            {
            $product_variant_data[$key]['variants'] = array();
            }
            }
            }
            */
            if (empty($product_variant_data)) {
                //no varaint available in product
                $pageData['no_varirant_only_product_data'] = $this->Common->getData('products', 'id as product_id, product_name,price,offer_price,min_qty,max_qty,is_veg,product_image', 'id = ' . $selected_product_id . ' AND product_status = 1');
            } else {
                // variant available
                $pageData['get_checked_variant_data'] = $this->Common->getData('admin_customize_product_variant_cart', 'variant_type_id', 'product_id = ' . $selected_product_id . ' AND order_id ="' . $order_id . '"');
                $pageData['product_variant_data'] = $product_variant_data;
            }
        } else {
            $pageData['product_variant_data'] = "";
        }
        $this->load->view('order_select_product_variant', $pageData);
    }
    // For order_select_product_variants page, to  show variants according to selected product -------END-------------
    //set customize order data as temporary -------------START----------------
    public function set_temporary_data_for_order_customization() {
        // insert selected product in to admin_customize_product_cart ---start-----
        #when select any product that time this will be work
        $selected_order_id = $this->db->escape_str(trim($this->input->post('selected_order_id')));
        $selected_product_id = $this->db->escape_str(trim($this->input->post('selected_product_id')));
        $product_unit_price = $this->db->escape_str(trim($this->input->post('product_unit_price')));
        $selected_product_status = $this->db->escape_str(trim($this->input->post('selected_product_status')));
        if (isset($selected_product_status) && $selected_product_status != "") {
            if ($selected_order_id != "" && $selected_product_id != "" && $product_unit_price != "" && $selected_order_id != "" && $selected_product_status == 1) {
                $check_customize_data = $this->Common->getData('admin_customize_product_cart', 'product_id', 'product_id = ' . $selected_product_id . ' AND order_id = ' . $selected_order_id . '');
                if (empty($check_customize_data)) {
                    $check_exist_product_price = $this->Common->getData('order_product_details','   product_unit_price','product_id = '.$selected_product_id .' AND order_id = '.$selected_order_id.' AND status = 0');
                    if(!empty($check_exist_product_price)){# if product unselect then select but product is exist when customer has order then we have to take exist price
                        $product_unit_price = $check_exist_product_price[0]['product_unit_price'];
                    }

                    $insert_product_data = array('order_id' => $selected_order_id, 'product_id' => $selected_product_id, 'product_unit_price' => $product_unit_price, 'product_quantity' => 1);
                    //print_r($insert_variant_data );
                    $insert_customize_order_variant_status = $this->Common->insertData('admin_customize_product_cart', $insert_product_data);
                    if ($insert_customize_order_variant_status > 0) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                }
            } else if ($selected_product_status == 0) {
                $delete_status = $this->Common->deleteData('admin_customize_product_cart', 'product_id =' . $selected_product_id . ' AND order_id = ' . $selected_order_id . '');
                if ($delete_status > 0) {
                    echo 1;
                }
            }
        }
        // insert selected product in to admin_customize_product_cart ---END-----
        #when select /unselect any types that time this will be work -----START---------
        // insert selected product variant in to admin_customize_product_variant_cart
        $order_product_change_status = $this->db->escape_str(trim($this->input->post('order_product_change_status'))); // 1 = selected for order , 0 = not selected for order
        $product_id_with_variant_id = $this->db->escape_str(trim($this->input->post('product_id_with_variant_id')));
        $variant_type_price = $this->db->escape_str(trim($this->input->post('variant_type_price')));
        $check_variant_selection_if_single = $this->db->escape_str(trim($this->input->post('check_variant_selection_if_single'))); //value if 1 then it is "Single select" then selection type will be work as radio button, value if 2 then "multi select" then selection type will be work as checkbox button
        if (isset($order_product_change_status) && $order_product_change_status != 2 && !empty($product_id_with_variant_id)) {
            $product_id_with_variant_id_expload = explode("_", $product_id_with_variant_id);
            $product_id_expload = explode(",variant", $product_id_with_variant_id_expload[2]);
            $variant_type_id_expload = explode(",variant", $product_id_with_variant_id_expload[5]);
            $selected_product_v_id = $product_id_expload[0];
            $selectd_variant_type_id = $variant_type_id_expload[0];
            $selectd_variant_id = $product_id_with_variant_id_expload[7];
            if ($selected_product_v_id != "" && $selectd_variant_type_id != "" && $order_product_change_status == 1) {
                if ($check_variant_selection_if_single == 1) { //single
                    //value 1 -  it is "Single select" then selection type will be work as radio button
                    #we have to delete perevious selected variant beacouse only 1 variant should be  enter in table if it is radio selection
                    #one product have only one variant in table
                    //echo 'radio';
                    $check_customize_data_if_selection_single = $this->Common->getData('admin_customize_product_variant_cart', 'variant_type_id', 'product_id = ' . $selected_product_v_id . ' AND variant_id = ' . $selectd_variant_id . '');
                    //if exist pervious variant for single product then it will be delete
                    if (!empty($check_customize_data_if_selection_single)) {
                        $delete_status = $this->Common->deleteData('admin_customize_product_variant_cart', 'product_id =' . $selected_product_v_id . ' AND order_id = ' . $selected_order_id . ' AND variant_id = ' . $selectd_variant_id . '');
                    }
                    // after delete if any new unique entry will be insert for one product
                    $insert_variant_data = array('order_id' => $selected_order_id, 'product_id' => $selected_product_v_id, 'variant_id' => $selectd_variant_id, 'variant_type_id' => $selectd_variant_type_id, 'variant_price' => $variant_type_price,);
                    $insert_customize_order_variant_status = $this->Common->insertData('admin_customize_product_variant_cart', $insert_variant_data);
                    if ($insert_customize_order_variant_status > 0 || $delete_status > 0) {
                        echo 1;
                    }
                } else if ($check_variant_selection_if_single == 2) { //multi
                    //value 2 - "multi select" then selection type will be work as checkbox button
                    #we can enter  multiple entries for one product in table
                    //echo 'checkbox';
                    $check_customize_data = $this->Common->getData('admin_customize_product_variant_cart', 'variant_type_id', 'product_id = ' . $selected_product_v_id . ' AND variant_type_id = ' . $selectd_variant_type_id . ' AND order_id = ' . $selected_order_id . ' AND variant_id = ' . $selectd_variant_id . '');
                    if (empty($check_customize_data)) {
                        $insert_variant_data = array('order_id' => $selected_order_id, 'product_id' => $selected_product_v_id, 'variant_id' => $selectd_variant_id, 'variant_type_id' => $selectd_variant_type_id, 'variant_price' => $variant_type_price,);
                        $insert_customize_order_variant_status = $this->Common->insertData('admin_customize_product_variant_cart', $insert_variant_data);
                        if ($insert_customize_order_variant_status > 0) {
                            echo 1;
                        }
                    }
                }
            } else if ($order_product_change_status == 0) {
                $delete_status = $this->Common->deleteData('admin_customize_product_variant_cart', 'product_id =' . $selected_product_v_id . ' AND variant_type_id = ' . $selectd_variant_type_id . ' AND order_id = ' . $selected_order_id . ' AND variant_id = ' . $selectd_variant_id . '');
                if ($delete_status > 0) {
                    echo 1;
                }
            }
        }
        #when select /unselect  any variant types that time this will be work -----END---------
        //if delete items/ products form the selection  when click on delete icon ---START---
        if (isset($order_product_change_status) && $order_product_change_status == 2) { // 1 = selected for order , 0 = not selected for order, 2 deleted fron selection
            $product_id_for_delete = $this->db->escape_str(trim($this->input->post('product_id_for_delete')));
            $delete_status = $this->Common->deleteData('admin_customize_product_cart', 'product_id =' . $product_id_for_delete . ' AND order_id = ' . $selected_order_id . '');
            $delete_status = $this->Common->deleteData('admin_customize_product_variant_cart', 'product_id =' . $product_id_for_delete . ' AND order_id = ' . $selected_order_id . '');
            echo 1;
        }
        //if delete items/ products form the selection  when click on delete icon ---END---
        //Remove check from the radio button if dont want to select that variant-----START---
        $remove_selection_from_radio = $this->db->escape_str(trim($this->input->post('remove_selection_from_radio')));
        if ($remove_selection_from_radio == 1) {
            $selected_variant_id = $this->db->escape_str(trim($this->input->post('selected_variant_id')));
            $selected_product_id = $this->db->escape_str(trim($this->input->post('selected_product_id')));
            $selected_order_id = $this->db->escape_str(trim($this->input->post('selected_order_id')));
            $delete_status = $this->Common->deleteData('admin_customize_product_variant_cart', 'product_id =' . $selected_product_id . ' AND order_id = ' . $selected_order_id . ' AND variant_id = ' . $selected_variant_id . '');
            if ($delete_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
        //Remove check from the radio button if dont want to select that variant-----END---
        
    }
    //set customize order data as temporary -------------END----------------
    //if close modal then delete temporary orderd data from admin_customize_product_cart and admin_customize_product_variant_cart
    public function delete_temp_customize_order_data($order_id = "") {
        //at the time when admin will close/cancel popup modal  or dont want to customize orderd data
        $this->Common->deleteData('admin_customize_product_cart', 'order_id =' . $order_id . '');
        $this->Common->deleteData('admin_customize_product_variant_cart', 'order_id =' . $order_id . '');
    }
    //Get data from temporary stored  and show for final place order--------------START------
    public function show_selected_products_for_final_order($order_id = "", $mode = "") { // mode 1 for first load , 2 for when items add or remove
        $check_exist_order_items_data = $this->comman_show_orderd_products_function($order_id, '', $mode); //order id,selected_product_id (Dont need to give on first time view), load mode check
        foreach ($check_exist_order_items_data as $item_value) {
            if ($mode == 1) {
                $check_customize_product_data = $this->Common->getData('admin_customize_product_cart', 'product_id', 'product_id = ' . $item_value['product_id'] . '  AND order_id = ' . $order_id . '');
                //insert product temporay data for customize
                if (empty($check_customize_product_data)) {
                    $insert_product_data = array('order_id' => $order_id, 'product_id' => $item_value['product_id'], 'product_unit_price' => $item_value['product_unit_price'], 'product_quantity' => $item_value['product_quantity']);
                    //print_r($insert_variant_data );
                    $insert_customize_order_variant_status = $this->Common->insertData('admin_customize_product_cart', $insert_product_data);
                }
            }
            if (!empty($item_value['variants'])) {
                foreach ($item_value['variants'] as $varaint_value) {
                    if ($mode == 1) {
                        //insert  product variant temporay data for customize if any
                        $check_customize_data = $this->Common->getData('admin_customize_product_variant_cart', 'variant_type_id', 'product_id = ' . $item_value['product_id'] . ' AND variant_type_id = ' . $varaint_value['variant_type_id'] . '  AND order_id = ' . $order_id . '');
                        if (empty($check_customize_data)) {
                            $insert_variant_data = array('order_id' => $order_id, 'product_id' => $item_value['product_id'], 'variant_id' => $varaint_value['variant_id'], 'variant_type_id' => $varaint_value['variant_type_id'], 'variant_price' => $varaint_value['variant_price']);
                            //print_r($insert_variant_data );
                            $insert_customize_order_variant_status = $this->Common->insertData('admin_customize_product_variant_cart', $insert_variant_data);
                        }
                    }
                }
            }
        }
        // getting Ordered Products -------------START------------
        $admin_customize_product_cart = $this->Common->getData('admin_customize_product_cart', 'admin_customize_product_cart.order_id,admin_customize_product_cart.product_id,admin_customize_product_cart.product_quantity,admin_customize_product_cart.product_unit_price,products.product_name,products.product_image,products.category_id', 'order_id = "' . $order_id . '" ', array('products'), array('products.id = admin_customize_product_cart.product_id')); // '.$query_part.' //status = 1- delete , default - 0
        $customize_product_detail_array = $admin_customize_product_cart;
        # Now we will check  any variant available , orderd for product is
        if (!empty($customize_product_detail_array)) {
            foreach ($customize_product_detail_array as $key => $value) {
                //for check only product has variant or not
                $variant_is_available = $this->Common->getData('variant_types_for_products', 'product_id', 'product_id = ' . $value['product_id'] . ' AND status = 1'); // status = 	1 - selected, 2 - unselected
                if (!empty($variant_is_available)) {
                    $customize_product_detail_array[$key]['variant_is_available'] = count($variant_is_available);
                } else {
                    $customize_product_detail_array[$key]['variant_is_available'] = 0;
                }
                //get selected variant for showing
                $order_product_varirant_query = "SELECT variants.variant_name,variant_types.variant_type_name,variant_types.variant_type_id,variant_types.variant_id,admin_customize_product_variant_cart.variant_price
						FROM admin_customize_product_variant_cart 
						INNER JOIN orders ON orders.id = admin_customize_product_variant_cart.order_id 
						INNER JOIN `variants` ON `variants`.`variant_id` = `admin_customize_product_variant_cart`.`variant_id` INNER JOIN `variant_types` ON `variant_types`.`variant_type_id` = `admin_customize_product_variant_cart`.`variant_type_id` 
						WHERE `admin_customize_product_variant_cart`.`order_id` = " . $order_id . " AND `admin_customize_product_variant_cart`.`product_id` = " . $value['product_id'] . " GROUP BY `admin_customize_product_variant_cart`.`variant_type_id`";
                $order_product_varirant_data = $this->Common->custom_query($order_product_varirant_query, 'get');
                if (count($order_product_varirant_data) > 0) {
                    foreach ($order_product_varirant_data as $key2 => $value2) {
                        $variant_is_single_or_multi = $this->Common->getData('variant_types_for_products', '	single_select', 'product_id = ' . $value['product_id'] . ' AND variant_id = ' . $value2['variant_id'] . ' AND variant_type_id = ' . $value2['variant_type_id'] . '');
                        if (count($variant_is_single_or_multi) > 0) {
                            $order_product_varirant_data[$key2]['single_select'] = $variant_is_single_or_multi[0]['single_select'];
                        }
                    }
                    $customize_product_detail_array[$key]['variants'] = $order_product_varirant_data;
                } else {
                    $customize_product_detail_array[$key]['variants'] = array();
                }
            }
        }
        $pageData['product_variant_data_for_place_order'] = $customize_product_detail_array;
        $this->load->view('order_selected_products_with_if_any_varaints', $pageData);
    }
    //Get data from temporary  and show for final place order--------------END------
    //Past-Orders Function
    public function past_orders() {
        $data = array('title' => "Past Orders", 'pageName' => "past-orders");
        $this->load->view('masterpage', $data);
    }
    //Business Function
    public function business() {
        /*$data = array(
        'title' => "Business",
        'pageName' => "business"
        );
        $this->load->view('masterpage', $data);*/
        $data = array('title' => "Business", 'pageName' => "coming_soon");
        $this->load->view('masterpage', $data);
    }
    //Change Password Function
    public function change_password() {
        $data = array('title' => "Change Password", 'pageName' => "change-password");
        $this->load->view('masterpage', $data);
    }
    //Profile Function
    public function profile() {
        if ($this->id && $this->role == 2) # Only merchant can do this
        {
            $pageData['merchant_category'] = $this->Common->getData('merchant_categories', 'id as merchant_category_id,category_name', " status = 1 AND status != 5");
            if (!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 2) {
                // user has no restaurant id
                # we need to get  only user data
                $pageData['merchant_rest_detail'] = array();
            } else {
                // user has restaurant id
                # we need to get user data with resturant data
                $pageData['merchant_rest_detail'] = $this->Common->getData('restaurants', 'restaurants.*,users.number_id,users.email,users.fullname,users.user_street_address,users.mobile', 'restaurants.id = ' . $this->logged_in_restaurant_id . ' AND restaurants.rest_status NOT IN(3)', array('users'), array('restaurants.admin_id = users.id'), '', '', '');
            }
            $pageData['pageTitle'] = "Profile";
            $pageData['pageName'] = 'profile';
            $this->load->view('masterpage', $pageData);
        } else {
            $this->load->view('login');
        }
        /*$data = array(
        'title' => "Profile",
        'pageName' => "coming_soon"
        );
        $this->load->view('masterpage', $data);*/
    }
    //Setting Function
    public function setting() {
        if ($this->id && $this->role == 1) # Only admin can do this
        {
            $data = array('title' => "Setting", 'pageName' => "setting");
            //Basic Settings
            $pageData['settings_data'] = $this->Common->getData('settings', '*');
            //Account Settings
            $pageData['admin_information'] = $this->Common->getData('users', 'id,number_id,fullname,email,mobile,profile_pic,user_street_address', 'id ="' . $this->id . '" AND role = 1'); //Super Admin role - 1
            $pageTitle = 'Edit Setting';
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = $pageTitle;
            $pageData['pageName'] = 'setting';
            $this->load->view('masterpage', $pageData);
        } else {
            $this->load->view('login');
        }
    }
    //FAQ Function ----------view-------------START-------------
    public function faq($table_data = "") {
        if ($this->id && $this->role == 1) # Only admin can do this
        {
            $common_query = "SELECT *  FROM `faq` WHERE   `status` != 3 ORDER BY  `id` DESC";
            #db_status = 1 - Enable, 2 - Disable, 3 - Delete
            $page = ($this->uri->segment(4)) ? ($this->uri->segment(4) - 1) : 0;
            if ($page > 0) {
                $page_offset = $page * ADMIN_PER_PAGE_RECORDS;
            } else {
                $page_offset = $page;
            }
            $query = "" . $common_query . " LIMIT " . ADMIN_PER_PAGE_RECORDS . " OFFSET " . $page_offset . " ";
            $pageData['faq_data'] = $this->Common->custom_query($query, 'get');
            $query = "" . $common_query . "";
            $total_records = count($this->Common->custom_query($query, "get"));
            $url = base_url('admin/faq/0/'); //by default table value is 0
            # Pass parameter to common pagination and create pagination function start
            $this->create_pagination($url, $total_records, ADMIN_PER_PAGE_RECORDS);
            $pageData['links'] = $this->pagination->create_links();
            $pageData['start'] = ($page * ADMIN_PER_PAGE_RECORDS) + 1;
            //pagination  ---End----
            $pageData['pageTitle'] = "FAQ";
            $pageData['pageName'] = 'faq';
            if ($table_data == "1" || $table_data == "2") {
                // if any action tiriger like, delete or enable disable then is url excute by ajax
                $this->load->view('faq-table-list', $pageData);
            } else {
                $this->load->view('masterpage', $pageData);
            }
        } else {
            $this->load->view('login');
        }
    }
    //FAQ Function ----------view-------------END-------------
    //Add/Edit FAQ-------------START---------------------------
    public function Create_Update_FAQ() {
        if ($this->id) {
            $question = $this->db->escape_str(trim($this->input->post('question')));
            $answer = $this->db->escape_str(trim($this->input->post('answer')));
            $mode = $this->db->escape_str(trim($this->input->post('mode'))); //1 = add , 2 - edit
            $insert_update_array = ['question' => $question, 'answer' => $answer, 'updated_at' => time() ];
            if ($question != "" && $answer != "") {
                if ($mode == 1) {
                    $insert_update_array['created_at'] = time();
                    $insert_status = $this->Common->insertData('faq', $insert_update_array);
                    if ($insert_status > 0) {
                        $this->session->set_flashdata('success', 'FAQ created successfully');
                    } else {
                        $this->session->set_flashdata('error', 'Internal Server Error !');
                    }
                } else if ($mode == 2) {
                    $edit_faq_id = $this->db->escape_str(trim($this->input->post('edit_faq_id')));
                    $update_status = $this->Common->updateData('faq', $insert_update_array, 'id = "' . $edit_faq_id . '"');
                    if ($update_status > 0) {
                        $this->session->set_flashdata('success', 'FAQ updated successfully');
                    } else {
                        $this->session->set_flashdata('error', 'Internal Server Error !');
                    }
                }
            } else {
                $this->session->set_flashdata('error', 'Some fields are missing!'); //Some fields are missing
                
            }
            header("location:" . base_url('admin/faq'));
        } else {
            $this->load->view('login');
        }
    }
    //Add/Edit FAQ-------------END-----------------------------
    //Delete FAQ------------START--------------
    public function DeleteFAQStatus() {
        if ($this->id) {
            $faq_id = $this->db->escape_str(trim($this->input->post('faq_id')));
            if ($faq_id != "") {
                $update_array = ['status' => 3, //1 - Enable, 2 - Disable, 3 = delete
                'updated_at' => time(), ];
                $update_status = $this->Common->updateData('faq', $update_array, 'id = "' . $faq_id . '"');
                if ($update_status > 0) {
                    echo 1; //success
                    
                } else {
                    echo 0; //not success
                    
                }
            } else {
                header("location:" . base_url('admin/faq'));
            }
        } else {
            echo 2; //session out
            
        }
    }
    //Delete FAQ------------END--------------
    //Get FAQ data for edit ------------------START-------
    public function GET_FAQ_Data() {
        $faq_id = $this->db->escape_str(trim($this->input->post('faq_id')));
        $category_data = $this->Common->getData('faq', '*', 'status != 3 AND id = ' . $faq_id . '');
        //db_status = 1 - Enable, 2 - Disable, 3 = delete
        if (count($category_data) > 0) {
            echo json_encode($category_data[0]);
        } else {
            echo 0;
        }
    }
    //Get FAQ data for edit ------------------END----------
    //CMS Function ----------view-------------START-------------
    public function cms() {
        if ($this->id && $this->role == 1) # Only admin can do this
        {
            $pageData['pageTitle'] = "Content Management";
            $pageData['pageName'] = 'cms';
            $pageData['cms_data'] = $this->Common->getData('cms', '*', 'id IN (1,2)'); //1 for Terms and conditions in table, //2 for privacy policy in table
            $this->load->view('masterpage', $pageData);
        } else {
            $this->load->view('login');
        }
    }
    //CMS Function ----------view-------------END-------------
    //Edit CMS Function --- view  and edit --------START-------------
    public function edit_cms($page_primary_id = "") {
        if ($this->id) {
            if ($page_primary_id != "") {
                $pageData['pageTitle'] = "Content Management";
                $pageData['pageName'] = 'edit-cms';
                $pageData['cms_data'] = $this->Common->getData('cms', '*', 'id = ' . $page_primary_id . ''); //1 for Terms and conditions in table
                $this->load->view('masterpage', $pageData);
            } else {
                header('location:' . base_url('admin/cms'));
            }
        } else {
            $this->load->view('login');
        }
    }
    //Edit CMS Function --- view  and edit --------END-------------
    //Enable /Disable(active/inactive) toggle of Restaurant------ START------
    public function active_inactive_CMS() {
        if ($this->id) {
            $cms_id = $this->db->escape_str(trim($this->input->post('cms_id')));
            $enable_disable_status = $this->db->escape_str(trim($this->input->post('enable_disable_status')));
            $update_array = ['status' => $enable_disable_status, //1 - Enable 2 - Disable
            'updated_at' => time() ];
            # update data in cms table
            $update_status = $this->Common->updateData('cms', $update_array, "id = " . $cms_id);
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 2; //session expired
            
        }
    }
    //Enable /Disable(active/inactive) toggle of Restaurant------ END------
    # createBreadcrumb function start
    # This Function is used to create breadcrumb by adding title and value of href
    public function createBreadcrumb($arr) {
        foreach ($arr as $key => $value) {
            $this->breadcrumbcomponent->add($key, $value);
        }
    }
    # createBreadcrumb function end
    # common function to return the end string of URL that will be used for adding class as active to side bar item.
    public function getUrlPart() {
        return $_SERVER['REQUEST_URI'];
    }
    #Admin Profile / And fetch data Function --------START-------
    public function admin_profile() {
        if ($this->id) {
            $data = array('title' => "Admin Profile", 'pageName' => "admin-profile");
            $pageData['admin_information'] = $this->Common->getData('users', 'id,fullname,email,mobile,profile_pic', 'id ="' . $this->id . '" AND role = 1'); //Super Admin role - 1
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = 'My Profile';
            $pageData['pageName'] = 'admin-profile';
            $this->load->view('masterpage', $pageData);
        } else {
            $this->load->view('login');
        }
    }
    //Admin Profile / And fetch data Function --------END-------
    //Admin Customer Function
    public function admin_customer() {
        $data = array('title' => "Customer", 'pageName' => "admin-customer");
        $this->load->view('masterpage', $data);
    }
    //Admin Customer Details Function
    public function customer_details() {
        $data = array('title' => "Customer Details", 'pageName' => "customer-details");
        $this->load->view('masterpage', $data);
    }
    //Reservations Function
    public function reservations($fromdate = 'all', $todate = 'all', $accept_status = 'all', $restaurant_id = 0) {
        if ($this->id) {
            $pageData['fromdate'] = $fromdate;
            $pageData['todate'] = $todate;
            $pageData['accept_status'] = $accept_status;
            $pageData['selected_restaurant_id'] = $restaurant_id;
            $fromDateNew = strtotime($fromdate . ' 00:00:00');
            $toDateNew = strtotime($todate . ' 24:00:00');
            $query_part = "";
            if ($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2 || $this->role == 2) {
                $restaurant_id = $this->logged_in_restaurant_id;
                $query_part.= ' AND `table_reservations`.`restaurant_id` = "' . $restaurant_id . '"';
                $query_part_for_category.= ' AND `restaurant_id` = "' . $restaurant_id . '"';
            }
            if ($fromdate != 'all' || $todate != 'all') {
                if ($fromdate != "all" && $todate != "all") {
                    $query_part.= ' AND (`table_reservations`.`created_at` between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                }
            }
            if ($accept_status != "all") {
                $query_part.= ' AND `table_reservations`.`is_accepted` = "' . $accept_status . '"';
            }
            if ($restaurant_id != 0) {
                $query_part.= ' AND `table_reservations`.`restaurant_id` = "' . $restaurant_id . '"';
            }
            $common_query = "SELECT table_reservations.*,table_reservations.id as reservation_id,restaurants.rest_name,restaurants.id,restaurants.admin_id,users.fullname,users.id as customer_id,users.number_id as customer_number_id FROM table_reservations INNER JOIN users ON users.id = table_reservations.user_id INNER JOIN restaurants ON restaurants.id = table_reservations.restaurant_id  WHERE table_reservations.id != 0 " . $query_part . " ORDER BY table_reservations.id DESC";
            $page = ($this->uri->segment(7)) ? ($this->uri->segment(7) - 1) : 0;
            if ($page > 0) {
                // $page_offset = $page * ADMIN_PER_PAGE_RECORDS;
                $page_offset = $page * ADMIN_PER_PAGE_RECORDS;
            } else {
                $page_offset = $page;
            }
            $url = base_url('admin/reservations/' . $fromdate . '/' . $todate . '/' . $accept_status . '/' . $restaurant_id . '/');
            $total_records = count($this->Common->custom_query($common_query, "get"));
            # Pass parameter to common pagination and create pagination function start
            $this->create_pagination($url, $total_records, ADMIN_PER_PAGE_RECORDS);
            $pageData['links'] = $this->pagination->create_links();
            $pageData['start'] = ($page * ADMIN_PER_PAGE_RECORDS) + 1;
            $query = "" . $common_query . " LIMIT " . $page * ADMIN_PER_PAGE_RECORDS . " , " . ADMIN_PER_PAGE_RECORDS . " ";
            $pageData['reservation_data'] = $this->Common->custom_query($query, 'get');
            // echo "QUERYYYY".$this->db->last_query();
            $resturant_query = "SELECT `id` as 'restaurant_id',`rest_name` FROM `restaurants` WHERE  `rest_status` != 3  ORDER BY `id` DESC";
            $pageData['resturant_details'] = $this->Common->custom_query($resturant_query, 'get');
            // $pageData['reservation_data'] = $this->Common->getData('table_reservations','restaurants.rest_name,restaurants.id,table_reservations.*,table_reservations.id as reservation_id,users.fullname,users.id','',array('users','restaurants'),array('users.id = table_reservations.user_id','restaurants.id = table_reservations.restaurant_id'));
            $data = array('title' => "Reservations", 'pageName' => "reservations");
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = 'Reservations';
            $pageData['pageName'] = 'reservations';
            $this->load->view('masterpage', $pageData);
        } else {
            $this->load->view('login');
        }
    }
    # Function to accept reservation
    public function accept_reject_reservation($type, $reservation_id) {
        # $type = 1 for Accept and 2 for Reject
        if ($this->id) {
            if ($type == 1) # ACCEPT
            {
                $this->Common->updateData('table_reservations', array('is_accepted' => 1), 'id = "' . $reservation_id . '"');
            } elseif ($type == 2) # REJECT
            {
                $this->Common->updateData('table_reservations', array('is_accepted' => 2), 'id = "' . $reservation_id . '"');
            }
            header('location:' . base_url('admin/reservations'));
        } else {
            $this->load->view('login');
        }
    }
    public function get_reservation_data_by_id() {
        if ($this->id) {
            $reservation_id = $this->input->post('reservation_id');
            $details = $this->Common->getData('table_reservations', '*', 'id = "' . $reservation_id . '"');
            if (count($details) > 0) {
                date_default_timezone_set('Asia/Singapore');
                $book_date = date('Y-m-d', $details[0]['booking_date']);
                $time_slot = date('H:i', $details[0]['time_slot']);
                $details[0]['booking_date'] = $book_date;
                $details[0]['time_slot'] = $time_slot;
                echo json_encode($details[0]);
            } else {
                echo 0;
            }
        } else {
            $this->load->view('login');
        }
    }
    # Function to update the reservation
    public function update_reservation() {
        if ($this->id) {
            // print_r($_POST);
            # We need to create two timestamp. One for date:00:00 and one for slot
            # This is coming in UTC so make it as singapore by addiing 8 hours and make it as INDIA by adding 5 hours and 30 minutes
            // echo $this->input->post('date');
            // echo $this->input->post('dd_time_slot');
            date_default_timezone_set('Asia/Singapore');
            $book_date_timestamp = strtotime($this->input->post('date'));
            ############ FOR SINGAPORE ##########
            // $book_date_timestamp = $book_date_timestamp + (8*60*60);
            ########### FOR INDIA ###########
            // $book_date_timestamp = $book_date_timestamp + (5*60*60);
            // $book_date_timestamp = $book_date_timestamp + (30*60);
            // echo "book_date_timestamp".$book_date_timestamp;
            $time_slot = $this->input->post('dd_time_slot');
            $time_sl = explode(":", $time_slot);
            $dd_time_slot = strtotime('+' . $time_sl[0] . 'hours', $book_date_timestamp);
            $dd_time_slot = strtotime('+' . $time_sl[1] . 'minutes', $dd_time_slot);
            // echo "dd_time_slot".$dd_time_slot;
            # We need to check the availability of the restaurant for selected date and time slot
            $reservation_id = $this->input->post('reservation_id');
            $restaurant_id = $this->Common->getData('table_reservations', 'restaurant_id', 'id = "' . $reservation_id . '"');
            $restaurant_id = $restaurant_id[0]['restaurant_id'];
            $rest_basic_details = $this->Common->getData('restaurants', '*', 'id = "' . $restaurant_id . '"');
            $offline_data = $this->Common->getData('rest_offline', '*', 'rest_id = "' . $restaurant_id . '"');
            // echo "<br><pre>";
            // print_r($offline_data);
            if (count($offline_data) > 0) {
                $offline_from = $offline_data[0]['offline_from'];
                $offline_to = $offline_data[0]['offline_to'];
                $proceed_further = 0; # 0 No 1 Yes
                $proceed_further_more = 0; # 0 No 1 Yes
                if ($offline_data[0]['offline_tag'] != 1) {
                    # BELOW SCENE IS HAPPENING WHEN TAGE IS NOT 1
                    # Timestamp of offline from and offline to is going into the database as per the UTC timezone and created_at and update_at going as per the local timezone so the value in $book_date_timestamp and $time_slot will be given as per the local timezone (If device is on India then india local time will be given and if device is in singapore then singapore local time will be given)
                    # So here I am having problem because India and UTC having 5 hours and 30 minutes difference and UTC and Singapore is 8 hours difference,
                    # So currently in order to proceed further I am deducting 5 hours and 30 minutes from DB offline values (from and to) and then I have to disable 5 ho urs and 30 mins code and enable 8 hours when send code on live
                    # ######## FOR INDIA UNCOMMENT BELOW ########
                    $offline_from_val = $offline_from - (5 * 60 * 60); # DEDUCT 5 HOURS
                    $offline_from = $offline_from_val - (30 * 60); # AND THEN 30 MINUTES
                    $offline_to_val = $offline_to - (5 * 60 * 60); # DEDUCT 5 HOURS
                    $offline_to = $offline_to_val - (30 * 60); # AND THEN 30 MINUTES
                    # ######## FOR INDIA ########
                    # ######## FOR SINGAPORE UNCOMMENT BELOW #########
                    // $offline_from = $offline_from - (8*60*60); # DEDUCT 8 HOURS
                    // $offline_to = $offline_to - (8*60*60); # DEDUCT 8 HOURS
                    # ######## FOR SINGAPORE ##########
                    // echo "offline_from IS ".$offline_from;
                    // echo "<br>offline_to IS ".$offline_to;
                    // echo "<br>dd_time_slot IS ".$dd_time_slot;
                    if ($book_date_timestamp >= $offline_from && $book_date_timestamp <= $offline_to) # $book_date_timestamp is midnight timestamp like 00:00
                    {
                        $proceed_further = 0; # Restaurant is set to Offline and not offering dinein on this day
                        echo 2;
                    } else {
                        $proceed_further = 1;
                    }
                } else {
                    // echo " <br>M I HERE ";
                    // echo "<br>dd_time_slot is ".$dd_time_slot;
                    // echo "<br>offline_from is ".$offline_from;
                    // echo "<br>offline_to is ".$offline_to;
                    if ($dd_time_slot >= $offline_from && $dd_time_slot <= $offline_to) # HOURS $dd_time_slot is exact timestamp with hours like 23aug11:30 (24 hours format)
                    {
                        $proceed_further = 0; # Restaurant is set to Offline and not offering dinein in this duration
                        echo 3;
                    } else {
                        // echo " <br>QQQQQQQQ ";
                        $proceed_further = 1;
                    }
                }
            } else # OPEN # No entry in offline table
            {
                $proceed_further = 1;
            }
            // echo "<br>proceed_further".$proceed_further;
            // die;
            if ($proceed_further == 1) {
                # Get Time mode
                if ($rest_basic_details[0]['time_mode'] == 1) # 1 - for every day, 2 - for Specific day
                {
                    // echo "QQQQ";
                    $open_time = $rest_basic_details[0]['open_time'];
                    $close_time = $rest_basic_details[0]['close_time'];
                    $break_start_time = $rest_basic_details[0]['break_start_time'];
                    $break_end_time = $rest_basic_details[0]['break_end_time'];
                    $open_time_exp = explode(":", $open_time); # 11:30
                    $open_time_hr = $open_time_exp[0]; # 11
                    $open_time_min = $open_time_exp[1]; # 30
                    $open_time = $book_date_timestamp + ($open_time_hr * 60 * 60); # Adding hours
                    $open_time = $open_time + ($open_time_min * 60); # ADD MINUTES
                    $close_time_exp = explode(":", $close_time); # 11:30
                    $close_time_hr = $close_time_exp[0]; # 11
                    $close_time_min = $close_time_exp[1]; # 30
                    $close_time = $book_date_timestamp + ($close_time_hr * 60 * 60); # Adding hours
                    $close_time = $close_time + ($close_time_min * 60); # ADD MINUTES
                    $break_start_exp = explode(":", $break_start_time);
                    $break_start_hr = $break_start_exp[0];
                    $break_start_min = $break_start_exp[1];
                    $break_end_exp = explode(":", $break_end_time);
                    $break_end_hr = $break_end_exp[0];
                    $break_end_min = $break_end_exp[1];
                    # So here we need to hours to the $book_date_timestamp param
                    $break_from = $book_date_timestamp + ($break_start_hr * 60 * 60); # Adding hours
                    $break_from = $break_from + ($break_start_min * 60); # ADD MINUTES
                    // echo "<br>break_from is ".$break_from = $break_from + ($break_start_min * 60); # ADD MINUTES
                    $break_to = $book_date_timestamp + ($break_end_hr * 60 * 60); # Adding HOURS
                    $break_to = $break_to + ($break_end_min * 60);
                    if ($dd_time_slot >= $open_time && $dd_time_slot <= $close_time) {
                        $proceed_further_more = 0; # Restaurant is not open for the selected time
                        echo 4;
                        # Not open
                        
                    } elseif ($dd_time_slot >= $break_from && $dd_time_slot <= $break_to) {
                        $proceed_further_more = 0; # Restaurant is on break during this time
                        # On Break
                        echo 5;
                    } else {
                        $proceed_further_more = 1;
                    }
                } else if ($rest_basic_details[0]['time_mode'] == 2) {
                    // echo "WWWWWWWw";
                    # Get what is the Day of the passed date_timestamp
                    $weekday = date('l', $book_date_timestamp);
                    $weekday = strtolower($weekday);
                    // echo "weekday is ".$weekday;
                    $rest_time_daywise = $this->Common->getData('rest_time_daywise', '*', 'rest_id = "' . $restaurant_id . '"');
                    if (count($rest_time_daywise) > 0) {
                        if ($weekday == 'monday') {
                            $full_day_close_status = $rest_time_daywise[0]['mon_close_status'];
                            $open_close_time = $rest_time_daywise[0]['mon_open_close_time'];
                            $brk_status = $rest_time_daywise[0]['mon_break_status'];
                            $brk_start_brk_end = $rest_time_daywise[0]['mon_break_start_end_time'];
                        } elseif ($weekday == 'tuesday') {
                            $full_day_close_status = $rest_time_daywise[0]['tue_close_status'];
                            $open_close_time = $rest_time_daywise[0]['tue_open_close_time'];
                            $brk_status = $rest_time_daywise[0]['tue_break_status'];
                            $brk_start_brk_end = $rest_time_daywise[0]['tue_break_start_end_time'];
                        } elseif ($weekday == 'wednesday') {
                            $full_day_close_status = $rest_time_daywise[0]['wed_close_status'];
                            $open_close_time = $rest_time_daywise[0]['wed_open_close_time'];
                            $brk_status = $rest_time_daywise[0]['wed_break_status'];
                            $brk_start_brk_end = $rest_time_daywise[0]['wed_break_start_end_time'];
                        } elseif ($weekday == 'thursday') {
                            $full_day_close_status = $rest_time_daywise[0]['thu_close_status'];
                            $open_close_time = $rest_time_daywise[0]['thu_open_close_time'];
                            $brk_status = $rest_time_daywise[0]['thu_break_status'];
                            $brk_start_brk_end = $rest_time_daywise[0]['thu_break_start_end_time'];
                        } elseif ($weekday == 'friday') {
                            $full_day_close_status = $rest_time_daywise[0]['fri_close_status'];
                            $open_close_time = $rest_time_daywise[0]['fri_open_close_time'];
                            $brk_status = $rest_time_daywise[0]['fri_break_status'];
                            $brk_start_brk_end = $rest_time_daywise[0]['fri_break_start_end_time'];
                        } elseif ($weekday == 'saturday') {
                            $full_day_close_status = $rest_time_daywise[0]['sat_close_status'];
                            $open_close_time = $rest_time_daywise[0]['sat_open_close_time'];
                            $brk_status = $rest_time_daywise[0]['sat_break_status'];
                            $brk_start_brk_end = $rest_time_daywise[0]['sat_break_start_end_time'];
                        } elseif ($weekday == 'sunday') {
                            $full_day_close_status = $rest_time_daywise[0]['sun_close_status'];
                            $open_close_time = $rest_time_daywise[0]['sun_open_close_time'];
                            $brk_status = $rest_time_daywise[0]['sun_break_status'];
                            $brk_start_brk_end = $rest_time_daywise[0]['sun_break_start_end_time'];
                        }
                        // echo "<br>brk_status is ".$brk_status;
                        if ($full_day_close_status == 2) # 2- on this day restaurant will be closed, 1 - restaurant will be opend
                        {
                            $proceed_further_more = 0; # Restaurant is closed on this (".$weekday.") day
                            echo 6;
                            # Restaurant is closed on this day
                            
                        } else {
                            if ($brk_status == 1) # 2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time) and 0 when restaurant is closed in full day
                            {
                                $brk_start_brk_end = explode("-", $brk_start_brk_end);
                                $break_start_time = $brk_start_brk_end[0];
                                $break_end_time = $brk_start_brk_end[1];
                                $break_start_exp = explode(":", $break_start_time);
                                $break_start_hr = $break_start_exp[0];
                                $break_start_min = $break_start_exp[1];
                                $break_end_exp = explode(":", $break_end_time);
                                $break_end_hr = $break_end_exp[0];
                                $break_end_min = $break_end_exp[1];
                                # So here we need to hours to the $book_date_timestamp param
                                $break_from = $book_date_timestamp + ($break_start_hr * 60 * 60); # Adding hours
                                $break_from = $break_from + ($break_start_min * 60); # ADD MINUTES
                                // echo "<br>break_from is ".$break_from = $break_from + ($break_start_min * 60); # ADD MINUTES
                                $break_to = $book_date_timestamp + ($break_end_hr * 60 * 60); # Adding HOURS
                                $break_to = $break_to + ($break_end_min * 60);
                                // echo "<br>dd_time_slot is ".$dd_time_slot;
                                if ($dd_time_slot >= $break_from && $dd_time_slot <= $break_to) {
                                    $proceed_further_more = 0; # Restaurant is on break during these hours
                                    echo 7;
                                    // echo "<br>HERE";
                                    # Running on break
                                    
                                } else {
                                    $proceed_further_more = 1;
                                }
                            } else {
                                $proceed_further_more = 1;
                            }
                        }
                    }
                }
            }
            if ($proceed_further_more == 1) {
                # Before updated we need to check how many number of people are entered and we need to compare with max capacity and seats left
                /*$query = "SELECT SUM(no_of_people) AS no_of_booked_seats FROM table_reservations WHERE restaurant_id = ".$restaurant_id." and time_slot = ".$dd_time_slot." and is_accepted NOT IN(2,3)"; # 0 - Action pending 1 - Accepted by merchant 2 - Rejected by merchant 3 - cancelled by customer
                $check_existing_data = $this->Common->custom_query($query,'get');
                echo "<pre>";
                print_r($check_existing_data);*/
                $max_capacaity = $rest_basic_details[0]['max_capacity'];
                // echo "<br>max_capacaity".$max_capacaity;
                // $no_of_booked_seats = $check_existing_data[0]['no_of_booked_seats'];
                // echo "<br>no_of_booked_seats".$no_of_booked_seats;
                $seats_left = $max_capacaity - $no_of_booked_seats;
                $no_of_people = $this->input->post('no_of_people');
                // echo "<br>no_of_people".$no_of_people;
                if ($no_of_people <= $max_capacaity) # OK WE CAN BOOK
                {
                    $this->Common->updateData('table_reservations', array('booking_date' => $book_date_timestamp, 'no_of_people' => $this->input->post('no_of_people'), 'time_slot' => $dd_time_slot), 'id = "' . $this->input->post('reservation_id') . '"');
                    // echo "<br>PRINT UPDATEQUERY".$this->db->last_query();
                    # Send notification code start
                    $token = $this->Common->getData('users', 'device_token', 'id=' . $this->input->post('user_id'));
                    $token = $token[0]['device_token'];
                    $notification_data_fields = array('message' => "Dinein " . $this->input->post('booking_id') . " " . $this->lang->line('dinein_accepted'), 'title' => NOTIFICATION_TITLE_DINEIN_UPDATED, 'order_id' => $this->input->post('booking_id'), 'notification_type' => 'ORDER_STATUS_UPDATED');
                    # We have seperate bundle ids for customer and merchant. So if push needs to be sent to customer then use IOS_BUNDLE_ID_CUSTOMER constant
                    if ($token != "") {
                        sendPushNotification($token, $notification_data_fields, IOS_BUNDLE_ID_CUSTOMER);
                    }
                    # send notification code end
                    # Now insert notification to Database
                    $insertData = ['title' => "Dinein " . $this->input->post('booking_id') . " " . $this->lang->line('dinein_accepted'), 'to_user_id' => $this->input->post('user_id'), 'type' => 1, 'order_id' => $this->input->post('booking_id'), 'is_read' => 1, 'created_at' => time(), 'updated_at' => time(), ];
                    $this->Common->insertData('notifications', $insertData);
                    echo 1; # SUCCESS
                    
                } else {
                    echo 9; # Reached to max capacity
                    
                }
            } else {
                echo 8;
            }
        } else {
            $this->load->view('login');
        }
    }
    //Wallet Function
    public function wallet() {
        $data = array('title' => "Wallet", 'pageName' => "wallet");
        $this->load->view('masterpage', $data);
    }
    //Restaurant Details Function
    public function show_restaurant($restaurant_id = "") {
        if ($this->id) {
            if ($restaurant_id == "") {
                redirect(base_url('admin/errors_404'));
            } else {
                $data = array('title' => "Restaurant Details", 'pageName' => "show-restaurant");
                $pageData['restaurant_admin_detail'] = $this->Common->getData('restaurants', 'restaurants.*,users.number_id,users.email,users.fullname,users.user_street_address,users.mobile', 'restaurants.id = ' . $restaurant_id . ' AND restaurants.rest_status NOT IN(3)', array('users'), array('restaurants.admin_id = users.id'), '', '', '');
                //if($pageData['restaurant_admin_detail'][0]['time_mode'] == 2){//day wise time selection
                $pageData['day_wise_rest_time'] = $this->Common->getData("rest_time_daywise", "rest_time_daywise.*", "rest_id = " . $restaurant_id . "");
                /*}else{
                $pageData['day_wise_rest_time'] = array();
                }*/
                $pageData['merchant_category'] = $this->Common->getData('merchant_categories', 'id as merchant_category_id,category_name', " status = 1");
                //for showing menu -------------START--------------
                //getting category
                $restaurant_category = $this->Common->getData('categories', 'id as category_id', " category_status = 1 AND restaurant_id=" . $restaurant_id . "", "", "", "id", "DESC", "1");
                if (!empty($restaurant_category)) {
                    $pageData['rest_current_category_id'] = $restaurant_category[0]['category_id'];
                } else {
                    $pageData['rest_current_category_id'] = 0;
                }
                //for showing menu -------------END--------------
                $pageTitle = 'Restaurant Details';
                $this->createBreadcrumb($data);
                $pageData['urlPart'] = $this->getUrlPart();
                $pageData['pageTitle'] = $pageTitle;
                $pageData['pageName'] = 'show-restaurant';
                $this->load->view('masterpage', $pageData);
            }
        } else {
            $this->load->view('login');
        }
    }
    //Add Restaurant Function
    public function add_edit_restaurant($mode_type = "", $restaurant_id = "", $restaurant_admin_id = "") {
        if ($this->id) {
            $pageData['merchant_category'] = $this->Common->getData('merchant_categories', 'id as merchant_category_id,category_name', " status = 1 AND status != 5");
            $pageData['user_name_list'] = $this->Common->getData('users', 'id,fullname', " role = 2 AND status != 5");
            if ($mode_type == 1) {
                $data = array('title' => "Add Restaurant", 'pageName' => "add-edit-restaurant");
                $pageTitle = 'Add Restaurant';
                $this->createBreadcrumb($data);
                $pageData['urlPart'] = $this->getUrlPart();
                $pageData['pageTitle'] = $pageTitle;
                $pageData['pageName'] = 'add-edit-restaurant';
                $pageData['mode_type'] = 1;
                /*
                $exist_merchant = $this->Common->getData('restaurants','admin_id','rest_status NOT IN(3)');//AND users.id = '.$restaurant_admin_id.' AND  restaurants.admin_id = '.$restaurant_admin_id.'
                if(!empty($exist_merchant)){
                foreach ($exist_merchant as $value) {
                // if merchant restauant exist in restauant table then it should not be visible for add restaurant
                $pageData['user_name_list'] = $this->Common->getData('users','id,fullname',"role = 2 AND status != 5 AND id !=".$value['admin_id']."");
                }
                }else{
                $pageData['user_name_list'] = $this->Common->getData('users','id,fullname',"role = 2 AND status != 5");
                }*/
                $this->load->view('masterpage', $pageData);
            }
            if ($mode_type == 2) {
                if (!empty($restaurant_id) && !empty($restaurant_admin_id)) {
                    $data = array('title' => "Add Restaurant", 'pageName' => "add-edit-restaurant");
                    $restaurant_data = $this->Common->getData('restaurants', 'restaurants.*,users.email,users.fullname,users.user_street_address,users.mobile', 'restaurants.id = ' . $restaurant_id . ' AND users.id = ' . $restaurant_admin_id . ' AND  restaurants.admin_id = ' . $restaurant_admin_id . ' AND restaurants.rest_status NOT IN(3)', array('users'), array('restaurants.admin_id = users.id'), '', '', ''); //AND users.id = '.$restaurant_admin_id.' AND  restaurants.admin_id = '.$restaurant_admin_id.'
                    //checking restaurant offline status ------START----------
                    #if restaurant offline status is available in table then we have to manage toggle according to given time and date
                    #ex. hour - select 1 hour then if  it will not do online after 1 hour menully then toggle should be show active(online)  automatice after  1 hour
                    #same as  for single date or multi date
                    if (!empty($restaurant_data)) {
                        foreach ($restaurant_data as $key => $value) {
                            $offline_status_data = $this->Common->getData('rest_offline', 'rest_offline.offline_tag,rest_offline.offline_value,rest_offline.offline_from,rest_offline.offline_to', 'rest_offline.rest_id = "' . $value['id'] . '"', '', '');
                            //print_r($offline_status_data);
                            //we need to check current time (between from and to date)for getting offline status becouse may be possible from and to date is complete but restaurant didnot changed status (untill online , offline entery will be available) that's why
                            if (!empty($offline_status_data)) {
                                //echo time().'>='.$offline_status_data[0]['offline_from'].'===='.time().'<='.$offline_status_data[0]['offline_to'].'<br>';
                                if ((time() >= $offline_status_data[0]['offline_from']) && (time() <= $offline_status_data[0]['offline_to'])) {
                                    $offline_status = "1"; //Offline
                                    
                                } else {
                                    $offline_status = "0"; //Online
                                    
                                }
                                $restaurant_data[$key]['offline_status'] = $offline_status;
                                $restaurant_data[$key]['selected_offline_tag'] = $offline_status_data[0]['offline_tag'];
                            } else {
                                $restaurant_data[$key]['offline_status'] = "0"; //Offline
                                $restaurant_data[$key]['selected_offline_tag'] = 1;
                            }
                        }
                    }
                    //checking restaurant offline status ------END----------
                    $pageData['restaurant_edit'] = $restaurant_data;
                    $pageTitle = 'Edit Restaurant';
                    $this->createBreadcrumb($data);
                    $pageData['urlPart'] = $this->getUrlPart();
                    $pageData['pageTitle'] = $pageTitle;
                    $pageData['pageName'] = 'add-edit-restaurant';
                    $pageData['mode_type'] = 2;
                    //$pageData['user_name_list'] = $this->Common->getData('users','id,fullname'," role = 2 AND status = 1");
                    //for showing menu -------------START--------------
                    //getting category
                    $restaurant_category = $this->Common->getData('categories', 'id as category_id', " category_status = 1 AND restaurant_id=" . $restaurant_id . "", "", "", "id", "DESC", "1");
                    if (!empty($restaurant_category)) {
                        $pageData['rest_current_category_id'] = $restaurant_category[0]['category_id'];
                    } else {
                        $pageData['rest_current_category_id'] = 0;
                    }
                    //for showing menu -------------END--------------
                    //if($pageData['restaurant_admin_detail'][0]['time_mode'] == 2){//day wise time selection
                    $pageData['day_wise_rest_time'] = $this->Common->getData("rest_time_daywise", "rest_time_daywise.*", "rest_id = " . $restaurant_id . "");
                    /*}else{
                    $pageData['day_wise_rest_time'] = array();
                    }*/
                    $this->load->view('masterpage', $pageData);
                } else {
                    header('location:' . base_url('admin/restaurant_list'));
                }
            }
        } else {
            $this->load->view('login');
        }
    }
    // selected merchat get -------------------START-------------
    public function selected_user_detail() {
        $user_id = $this->db->escape_str(trim($this->input->post('user_id')));
        $user_data = $this->Common->getData('users', 'email, mobile', 'id = ' . $user_id . '  AND role = 2 AND status != 5');
        if (count($user_data) > 0) {
            echo json_encode($user_data[0]);
        } else {
            echo 0;
        }
    }
    // selected merchat get --------------------END------------------
    //Login Function
    public function login() {
        if (!$this->id) {
            $data = array('title' => "Login", 'pageName' => "login");
            $this->load->view('login', $data);
        } else {
            header('location:' . base_url('admin'));
        }
    }
    //Forgot Password Function
    public function forgot_password() {
        $data = array('title' => "Forgot Password", 'pageName' => "forgot-password");
        $this->load->view('forgot-password', $data);
    }
    //Reset Password Message Function
    public function reset_password_message() {
        $data = array('title' => "Reset Password");
        $this->load->view('reset_password_message');
    }
    //Referral Function
    public function referral() {
        if ($this->id && $this->role == 1) {
            // getting referral data it should be only one, means there is promotion_mode_status  - 3 value is only one in  the promotions table ------------------
            $pageData['referral_data'] = $this->Common->getData('promotions', 'id as referral_id,language,promo_type,referrer_discount_value,referrer_max_discount,referrer_discription,referee_discount_value,referee_max_discount,referee_discription,min_value,promo_status', 'promo_status != 3 AND promotion_mode_status = 3', '', '', 'id', 'DESC'); //promotion_mode_status = 	1- Promo Code, 2- Discount, 3 - Referral
            $data = array('title' => "Referral", 'pageName' => "referral");
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = 'Referral';
            $pageData['pageName'] = 'referral';
            $this->load->view('masterpage', $pageData);
        } else {
            $this->load->view('login');
        }
    }
    //Add Banner Function
    public function ad_banner_list($table = '', $fromdate = 'all', $todate = 'all', $banner_status = 'all', $search_restaurant_id = 'all', $search_key = 'all') {
        if ($this->id && $this->role == 1) {
            //search filter and pagination------START-----------
            $pageData['fromdate'] = $fromdate;
            $pageData['todate'] = $todate;
            $pageData['banner_status'] = $banner_status; //1 - Enable, 2 - Disable, 3 - Deleted
            $pageData['selected_restaurant_id'] = $search_restaurant_id;
            $search_key = urldecode($search_key);
            $search_key = trim($search_key);
            $pageData['search'] = $search_key;
            $query_part = "";
            $fromDateNew = strtotime($fromdate . ' 00:00:00');
            $toDateNew = strtotime($todate . ' 24:00:00');
            $table_data = $this->uri->segment(3);
            if ($table != "" || $fromdate != "all" || $todate != "all" || $banner_status != "all" || $search_restaurant_id != "all" || $search_key != "all") {
                if ($fromdate != "all" && $todate == "all") {
                    $query_part.= ' AND `created_at` >= "' . strtotime($fromdate) . '"';
                }
                if ($todate != "all" && $fromdate == "all") {
                    $query_part.= ' AND `created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
                }
                if ($fromdate != "all" && $todate != "all") {
                    $query_part.= ' AND (created_at between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                }
                if ($banner_status != "all") {
                    $query_part.= ' AND `status` = "' . $banner_status . '"';
                }
                if ($search_restaurant_id != "all") {
                    $query_part.= ' AND `restaurant_id` = "' . $search_restaurant_id . '"';
                }
                if ($search_key != "all") {
                    $query_part.= ' AND  (`ad_name` LIKE "%' . $search_key . '%" OR  `ad_description` LIKE "%' . $search_key . '%" OR  `external_ink` LIKE "%' . $search_key . '%")';
                }
            }
            // geting all banner-----------START---------------
            // $common_query = $this->Common->getData("ad_banners","*","status != 3 ". $query_part."" ,'','','id','DESC');
            $common_query = "SELECT *  FROM `ad_banners` WHERE   `status` != 3 " . $query_part . " ORDER BY  `id` DESC";
            // geting all banner------------END---------------
            // getting restaurant list -------------START------------
            $pageData['restaurant_list'] = $this->Common->getData('restaurants', 'id,admin_id,rest_name', '  rest_status != 3');
            // getting restaurant list -------------END------------
            $page = ($this->uri->segment(9)) ? ($this->uri->segment(9) - 1) : 0;
            if ($page > 0) {
                $page_offset = $page * ADMIN_PER_PAGE_RECORDS;
            } else {
                $page_offset = $page;
            }
            $query = "" . $common_query . " LIMIT " . ADMIN_PER_PAGE_RECORDS . " OFFSET " . $page_offset . " ";
            $pageData['ad_banners_list'] = $this->Common->custom_query($query, 'get');
            $query = "" . $common_query . "";
            $total_records = count($this->Common->custom_query($query, "get"));
            $url = base_url('admin/ad_banner_list/0/' . $fromdate . '/' . $todate . '/' . $banner_status . '/' . $search_restaurant_id . '/' . $search_key . '/'); //by default table value is 0
            # Pass parameter to common pagination and create pagination function start
            $this->create_pagination($url, $total_records, ADMIN_PER_PAGE_RECORDS);
            $pageData['links'] = $this->pagination->create_links();
            $pageData['start'] = ($page * ADMIN_PER_PAGE_RECORDS) + 1;
            //pagination  ---End----
            $data = array('title' => "Ad Banner List", 'pageName' => "ad-banner-list");
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = 'Ad Banner List';
            $pageData['pageName'] = 'ad-banner-list';
            if ($table_data == 2 || $table_data == 1) {
                $this->load->view('ad_banners_list_table', $pageData);
            } else {
                $this->load->view('masterpage', $pageData);
            }
        } else {
            $this->load->view('login');
        }
    }
    //Discount -----------------------START-------------------------
    public function discount($selected_restaurant_id = "") {
        if ($this->id && $this->role == 1) {
            //For resturant select box view only ------START------
            $pageData['selected_restaurant_id'] = $selected_restaurant_id;
            if ($this->resturant_details) { // check if restaurant is avaiable in select box then show discount other wise not  show by parameter
                $pageData['resturant_details'] = $this->resturant_details;
                if ($selected_restaurant_id != "") {
                    $query_part = 'AND restaurant_id = ' . $selected_restaurant_id . '';
                    //getting discount data accroding selected restaurant id----- start---
                    $discount_data = $this->Common->getData('promotions', 'id as discount_id,code_name,discount_value,desciption,valid_from,valid_till,restaurant_id,max_discount,promo_status', 'promo_status != 3 ' . $query_part . ' AND promotion_mode_status = 2', '', '', 'id', 'DESC');
                    //	promo_status = 1 - Enable 2 - Disable 3 - Deleted, promotion_mode_status = 1- Promo Code, 2- Discount, 3 - Referral
                    //getting discount data accroding selected restaurant id----- end---
                    
                } else {
                    $query_part = "";
                    $discount_data = "";
                }
                $pageData['discount_data'] = $discount_data;
            }
            //For resturant select box view only ------END------
            $data = array('title' => "Discount", 'pageName' => "discount");
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = 'Discount';
            $pageData['pageName'] = 'discount';
            $this->load->view('masterpage', $pageData);
        } else {
            $this->load->view('login');
        }
    }
    //Discount -----------------------END-------------------------
    //Promotion Cash Back ---------------START--------------------------
    //Setting Function
    public function promotion_cashback() {
        if ($this->id && $this->role) {
            $data = array('title' => "Promotion CashBack", 'pageName' => "promotion_cashback");
            //Basic Settings
            $pageData['settings_data'] = $this->Common->getData('settings', '*', 'id = 23'); //23  is prder cashback row
            $pageTitle = 'Promotion CashBack';
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = $pageTitle;
            $pageData['pageName'] = 'promotion_cashback';
            $this->load->view('masterpage', $pageData);
        } else {
            $this->load->view('login');
        }
    }
    //Promotion CashBack ---------------END--------------------------
    #Promotion CashBack Update --------START-------
    public function update_order_cashback_of_promotion() {
        if ($this->id) {
            # Get all post values
            $order_cashback = $this->input->post('order_cashback');
            $order_cashback_type = $this->input->post('order_cashback_type');
            # Here we need to update value as well as type hence need to update array. Below array is for updating values
            $update_value = array('order_cashback' => $order_cashback,);
            foreach ($update_value as $key => $value) {
                $this->Common->updateData('settings', array('value' => $value), 'name = "' . $key . '"');
            }
            $update_status = array('order_cashback' => $order_cashback_type
            //1 : Flat 2 percent
            );
            # Below array is for updating type of cashback
            foreach ($update_status as $key => $value) {
                $this->Common->updateData('settings', array('type' => $value), 'name = "' . $key . '"');
            }
            $this->session->set_flashdata('success', 'CashBack updated successfully');
            header("location:" . base_url('admin/promotion_cashback'));
        } else {
            $this->load->view('login');
        }
    }
    #Promotion CashBack Update --------END-------
    public function layout_default() {
        $data = array('title' => "Layout &rsaquo; Default");
        $this->load->view('dist/layout-default', $data);
    }
    public function layout_transparent() {
        $data = array('title' => "Layout &rsaquo; Transparent Sidebar");
        $this->load->view('dist/layout-transparent', $data);
    }
    public function layout_top_navigation() {
        $data = array('title' => "Layout &rsaquo; Top Navigation");
        $this->load->view('dist/layout-top-navigation', $data);
    }
    public function blank() {
        $data = array('title' => "Blank Page");
        $this->load->view('dist/blank', $data);
    }
    public function bootstrap_alert() {
        $data = array('title' => "Bootstrap Components &rsaquo; Alert");
        $this->load->view('dist/bootstrap-alert', $data);
    }
    public function bootstrap_badge() {
        $data = array('title' => "Bootstrap Components &rsaquo; Badge");
        $this->load->view('dist/bootstrap-badge', $data);
    }
    public function bootstrap_breadcrumb() {
        $data = array('title' => "Bootstrap Components &rsaquo; Breadcrumb");
        $this->load->view('dist/bootstrap-breadcrumb', $data);
    }
    public function bootstrap_buttons() {
        $data = array('title' => "Bootstrap Components &rsaquo; Buttons");
        $this->load->view('dist/bootstrap-buttons', $data);
    }
    public function bootstrap_card() {
        $data = array('title' => "Bootstrap Components &rsaquo; Card");
        $this->load->view('dist/bootstrap-card', $data);
    }
    public function bootstrap_carousel() {
        $data = array('title' => "Bootstrap Components &rsaquo; Carousel");
        $this->load->view('dist/bootstrap-carousel', $data);
    }
    public function bootstrap_collapse() {
        $data = array('title' => "Bootstrap Components &rsaquo; Collapse");
        $this->load->view('dist/bootstrap-collapse', $data);
    }
    public function bootstrap_dropdown() {
        $data = array('title' => "Bootstrap Components &rsaquo; Dropdown");
        $this->load->view('dist/bootstrap-dropdown', $data);
    }
    public function bootstrap_form() {
        $data = array('title' => "Bootstrap Components &rsaquo; Form");
        $this->load->view('dist/bootstrap-form', $data);
    }
    public function bootstrap_list_group() {
        $data = array('title' => "Bootstrap Components &rsaquo; List Group");
        $this->load->view('dist/bootstrap-list-group', $data);
    }
    public function bootstrap_media_object() {
        $data = array('title' => "Bootstrap Components &rsaquo; Media Object");
        $this->load->view('dist/bootstrap-media-object', $data);
    }
    public function bootstrap_modal() {
        $data = array('title' => "Bootstrap Components &rsaquo; Modal");
        $this->load->view('dist/bootstrap-modal', $data);
    }
    public function bootstrap_nav() {
        $data = array('title' => "Bootstrap Components &rsaquo; Nav");
        $this->load->view('dist/bootstrap-nav', $data);
    }
    public function bootstrap_navbar() {
        $data = array('title' => "Bootstrap Components &rsaquo; Navbar");
        $this->load->view('dist/bootstrap-navbar', $data);
    }
    public function bootstrap_pagination() {
        $data = array('title' => "Bootstrap Components &rsaquo; Pagination");
        $this->load->view('dist/bootstrap-pagination', $data);
    }
    public function bootstrap_popover() {
        $data = array('title' => "Bootstrap Components &rsaquo; Popover");
        $this->load->view('dist/bootstrap-popover', $data);
    }
    public function bootstrap_progress() {
        $data = array('title' => "Bootstrap Components &rsaquo; Progress");
        $this->load->view('dist/bootstrap-progress', $data);
    }
    public function bootstrap_table() {
        $data = array('title' => "Bootstrap Components &rsaquo; Table");
        $this->load->view('dist/bootstrap-table', $data);
    }
    public function bootstrap_tooltip() {
        $data = array('title' => "Bootstrap Components &rsaquo; Tooltip");
        $this->load->view('dist/bootstrap-tooltip', $data);
    }
    public function bootstrap_typography() {
        $data = array('title' => "Bootstrap Components &rsaquo; Typography");
        $this->load->view('dist/bootstrap-typography', $data);
    }
    public function components_article() {
        $data = array('title' => "Components &rsaquo; Article");
        $this->load->view('dist/components-article', $data);
    }
    public function components_avatar() {
        $data = array('title' => "Components &rsaquo; Avatar");
        $this->load->view('dist/components-avatar', $data);
    }
    public function components_chat_box() {
        $data = array('title' => "Components &rsaquo; Chat Box");
        $this->load->view('dist/components-chat-box', $data);
    }
    public function components_empty_state() {
        $data = array('title' => "Components &rsaquo; Empty State");
        $this->load->view('dist/components-empty-state', $data);
    }
    public function components_gallery() {
        $data = array('title' => "Components &rsaquo; Gallery");
        $this->load->view('dist/components-gallery', $data);
    }
    public function components_hero() {
        $data = array('title' => "Components &rsaquo; Hero");
        $this->load->view('dist/components-hero', $data);
    }
    public function components_multiple_upload() {
        $data = array('title' => "Components &rsaquo; Multiple Upload");
        $this->load->view('dist/components-multiple-upload', $data);
    }
    public function components_pricing() {
        $data = array('title' => "Components &rsaquo; Pricing");
        $this->load->view('dist/components-pricing', $data);
    }
    public function components_statistic() {
        $data = array('title' => "Components &rsaquo; Statistic");
        $this->load->view('dist/components-statistic', $data);
    }
    public function components_tab() {
        $data = array('title' => "Components &rsaquo; Tab");
        $this->load->view('dist/components-tab', $data);
    }
    public function components_table() {
        $data = array('title' => "Components &rsaquo; Table");
        $this->load->view('dist/components-table', $data);
    }
    public function components_user() {
        $data = array('title' => "Components &rsaquo; User");
        $this->load->view('dist/components-user', $data);
    }
    public function components_wizard() {
        $data = array('title' => "Components &rsaquo; Wizard");
        $this->load->view('dist/components-wizard', $data);
    }
    public function forms_advanced_form() {
        $data = array('title' => "Forms &rsaquo; Advanced Forms");
        $this->load->view('dist/forms-advanced-form', $data);
    }
    public function forms_editor() {
        $data = array('title' => "Forms &rsaquo; Editor");
        $this->load->view('dist/forms-editor', $data);
    }
    public function forms_validation() {
        $data = array('title' => "Forms &rsaquo; Validation");
        $this->load->view('dist/forms-validation', $data);
    }
    public function gmaps_advanced_route() {
        $data = array('title' => "Google Maps &rsaquo; Advanced Route");
        $this->load->view('dist/gmaps-advanced-route', $data);
    }
    public function gmaps_draggable_marker() {
        $data = array('title' => "Google Maps &rsaquo; Draggable Marker");
        $this->load->view('dist/gmaps-draggable-marker', $data);
    }
    public function gmaps_geocoding() {
        $data = array('title' => "Google Maps &rsaquo; Geocoding");
        $this->load->view('dist/gmaps-geocoding', $data);
    }
    public function gmaps_geolocation() {
        $data = array('title' => "Google Maps &rsaquo; Geolocation");
        $this->load->view('dist/gmaps-geolocation', $data);
    }
    public function gmaps_marker() {
        $data = array('title' => "Google Maps &rsaquo; Marker");
        $this->load->view('dist/gmaps-marker', $data);
    }
    public function gmaps_multiple_marker() {
        $data = array('title' => "Google Maps &rsaquo; Multiple Marker");
        $this->load->view('dist/gmaps-multiple-marker', $data);
    }
    public function gmaps_route() {
        $data = array('title' => "Google Maps &rsaquo; Route");
        $this->load->view('dist/gmaps-route', $data);
    }
    public function gmaps_simple() {
        $data = array('title' => "Google Maps &rsaquo; Simple");
        $this->load->view('dist/gmaps-simple', $data);
    }
    public function modules_calendar() {
        $data = array('title' => "Modules &rsaquo; Calendar");
        $this->load->view('dist/modules-calendar', $data);
    }
    public function modules_chartjs() {
        $data = array('title' => "Modules &rsaquo; ChartJS");
        $this->load->view('dist/modules-chartjs', $data);
    }
    public function modules_datatables() {
        $data = array('title' => "Modules &rsaquo; Datatables");
        $this->load->view('dist/modules-datatables', $data);
    }
    public function modules_flag() {
        $data = array('title' => "Modules &rsaquo; Flag");
        $this->load->view('dist/modules-flag', $data);
    }
    public function modules_font_awesome() {
        $data = array('title' => "Modules &rsaquo; Font Awesome");
        $this->load->view('dist/modules-font-awesome', $data);
    }
    public function modules_ion_icons() {
        $data = array('title' => "Modules &rsaquo; Ion Icons");
        $this->load->view('dist/modules-ion-icons', $data);
    }
    public function modules_owl_carousel() {
        $data = array('title' => "Modules &rsaquo; Owl Carousel");
        $this->load->view('dist/modules-owl-carousel', $data);
    }
    public function modules_sparkline() {
        $data = array('title' => "Modules &rsaquo; Sparkline");
        $this->load->view('dist/modules-sparkline', $data);
    }
    public function modules_sweet_alert() {
        $data = array('title' => "Modules &rsaquo; Sweet Alert");
        $this->load->view('dist/modules-sweet-alert', $data);
    }
    public function modules_toastr() {
        $data = array('title' => "Modules &rsaquo; Toastr");
        $this->load->view('dist/modules-toastr', $data);
    }
    public function modules_vector_map() {
        $data = array('title' => "Modules &rsaquo; Vector Map");
        $this->load->view('dist/modules-vector-map', $data);
    }
    public function modules_weather_icon() {
        $data = array('title' => "Modules &rsaquo; Weather Icon");
        $this->load->view('dist/modules-weather-icon', $data);
    }
    public function auth_forgot_password() {
        $data = array('title' => "Forgot Password");
        $this->load->view('dist/auth-forgot-password', $data);
    }
    public function auth_login() {
        $data = array('title' => "Login");
        $this->load->view('dist/auth-login', $data);
    }
    public function auth_register() {
        $data = array('title' => "Register");
        $this->load->view('dist/auth-register', $data);
    }
    public function auth_reset_password() {
        $data = array('title' => "Reset Password");
        $this->load->view('dist/auth-reset-password', $data);
    }
    public function errors_503() {
        $data = array('title' => "503");
        $this->load->view('dist/errors-503', $data);
    }
    public function errors_403() {
        $data = array('title' => "403");
        $this->load->view('dist/errors-403', $data);
    }
    public function errors_404() {
        $data = array('title' => "404");
        $this->load->view('dist/errors-404', $data);
    }
    public function errors_500() {
        $data = array('title' => "500");
        $this->load->view('dist/errors-500', $data);
    }
    public function features_activities() {
        $data = array('title' => "Activities");
        $this->load->view('dist/features-activities', $data);
    }
    public function features_post_create() {
        $data = array('title' => "Post Create");
        $this->load->view('dist/features-post-create', $data);
    }
    public function features_posts() {
        $data = array('title' => "Posts");
        $this->load->view('dist/features-posts', $data);
    }
    public function features_profile() {
        $data = array('title' => "Profile");
        $this->load->view('dist/features-profile', $data);
    }
    public function features_settings() {
        $data = array('title' => "Settings");
        $this->load->view('dist/features-settings', $data);
    }
    public function features_setting_detail() {
        $data = array('title' => "Setting Detail");
        $this->load->view('dist/features-setting-detail', $data);
    }
    public function features_tickets() {
        $data = array('title' => "Tickets");
        $this->load->view('dist/features-tickets', $data);
    }
    public function utilities_contact() {
        $data = array('title' => "Contact");
        $this->load->view('dist/utilities-contact', $data);
    }
    public function utilities_invoice() {
        $data = array('title' => "Invoice");
        $this->load->view('dist/utilities-invoice', $data);
    }
    public function utilities_subscribe() {
        $data = array('title' => "Subscribe");
        $this->load->view('dist/utilities-subscribe', $data);
    }
    public function credits() {
        $data = array('title' => "Credits");
        $this->load->view('dist/credits', $data);
    }
    //------------------------------Development Section Start----------------------------------
    public function auth() {
        # Authentication function ---------------------START---------------------
        # This function used for administrator Authentication
        if (!$this->id) {
            if ($this->input->post('email')) {
                $email = $this->db->escape_str(trim($this->input->post('email')));
                $check_email = $this->Common->getData("users", "*", "email = '$email' and role IN (1,2) and status NOT IN(3,5)");
                //role 1 = super admin, 2  - merchant
                //status = 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
                if (!empty($check_email) && $check_email[0]['role'] == 2) {
                    $get_merchant_restaurant = $this->Common->getData('restaurants', 'id as "resturant_id", rest_name ', 'admin_id = "' . $check_email[0]['id'] . '" AND rest_status != 3');
                    if (!empty($get_merchant_restaurant)) {
                        $logged_in_restaurant_id = $get_merchant_restaurant[0]['resturant_id'];
                        $logged_in_restaurant_name = $get_merchant_restaurant[0]['rest_name'];
                    } else {
                        $logged_in_restaurant_id = "";
                        $logged_in_restaurant_name = "";
                    }
                } else {
                    $logged_in_restaurant_id = "";
                    $logged_in_restaurant_name = "";
                }
                if (!empty($check_email)) {
                    if (!$this->bcrypt->check_password($this->input->post('password'), $check_email[0]['password'])) {
                        $this->session->set_flashdata('error', 'Incorrect login details');
                        $this->load->view('login');
                    } else {
                        # Do not login store admin if not assigned to any store
                        if ($check_email[0]['email'] !== $email) {
                            $this->session->set_flashdata('error', 'Email id not found');
                            header('location:' . base_url('admin'));
                        } else {
                            $admin_data = array('adminId' => $check_email[0]['id'], 'email' => $check_email[0]['email'], 'role' => $check_email[0]['role'], 'fullname' => $check_email[0]['fullname'], 'profile_image' => $check_email[0]['profile_pic'], 'logged_in_restaurant_id' => $logged_in_restaurant_id, 'logged_in_restaurant_name' => $logged_in_restaurant_name,);
                            // remember me
                            if (!empty($this->input->post("remember"))) {
                                setcookie("loginId", $email, time() + (10 * 365 * 24 * 60 * 60));
                                setcookie("loginPass", $this->input->post('password'), time() + (10 * 365 * 24 * 60 * 60));
                            } else {
                                setcookie("loginId", "");
                                setcookie("loginPass", "");
                            }
                            $this->session->set_userdata($admin_data);
                            header('location:' . base_url('admin/orders/')); //default load order page after login as pr client request
                            
                        }
                    }
                } else {
                    $this->session->set_flashdata('error', 'Incorrect login details');
                    $this->load->view('login');
                }
            } else {
                $this->load->view('login');
            }
        } else {
            header('location:' . base_url('admin'));
        }
    }
    # Authentication funcon ---------------------END---------------------
    # This function is used to destroy all the session values associated to admin session
    public function logout() {
        # First we need to empty the device token for this user
        $this->Common->updateData('users', array('device_token' => ''), 'id = "' . $this->id . '"');
        $this->session->sess_destroy();
        //$this->session->set_flashdata('success', 'Logged Out Successfully');
        header('location:' . base_url('admin/login'));
    }
    # logout function end------------------------------------
    // Get Social media link FUNCTION-----START----------
    # get_social_urls method is used to get the footer icon href urls for mail function. Instead of writing same code again and again , we are writing this function and will just call it in single line
    public function get_social_urls() {
        $get_social_urls = $this->Common->getData('settings', 'value', 'name IN("facebook" , "google" , "instagram" , "website")');
        $facebook = isset($get_social_urls[0]) ? $get_social_urls[0]['value'] : '';
        $google = isset($get_social_urls[1]) ? $get_social_urls[1]['value'] : '';
        $insta = isset($get_social_urls[2]) ? $get_social_urls[2]['value'] : '';
        $website = isset($get_social_urls[3]) ? $get_social_urls[3]['value'] : '';
        return array('facebook' => $facebook, 'google' => $google, 'insta' => $insta, 'website' => $website);
    }
    // Get Social media link FUNCTION-----END----------
    #Forgot Password Contoller / Reset password ---------START----------------
    public function forgot_password_controller() {
        $this->session->set_flashdata('success', '');
        $this->session->set_flashdata('error', '');
        $email = $this->db->escape_str(trim($this->input->post('email')));
        $check_email = $this->Common->getData('users', 'id,email,role,fullname', "email='" . $email . "' AND role IN (1,2)  AND status NOT IN (2,3,4,5)");
        // # Role 1 for super admin
        // DB_user_status 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
        //print_r($check_email);
        if (!empty($check_email)) {
            # generate code for forgot password
            $token = generate_token();
            # add 1 day in current date time
            $token_expire_time = strtotime('+1 day');
            # make update array
            $update_array = ['token' => $token, 'token_expire_time' => $token_expire_time, 'updated_at' => time() ];
            # update data in users table
            $this->Common->updateData('users', $update_array, "id = " . $check_email[0]['id']);
            //FOR mail footer --------
            $social_urls = $this->get_social_urls();
            $mail_data['facebook_url'] = $social_urls['facebook'];
            $mail_data['google_url'] = $social_urls['google'];
            $mail_data['insta_url'] = $social_urls['insta'];
            $mail_data['website_url'] = $social_urls['website'];
            $mail_data['url'] = base_url() . 'admin/resetPasswordChange/' . $token . '/' . $check_email[0]['role'];
            $mail_data['user_name'] = $check_email[0]['fullname'];
            $mail_data['header_title'] = APP_NAME . ' Password Reset Instructions';
            $email = $check_email[0]['email'];
            $subject = APP_NAME . " Password Reset Instructions";
            //load template view
            $message = $this->load->view('email/forgot_password', $mail_data, TRUE);
            send_mail($email, $subject, $message);
            $this->session->set_flashdata('success', 'A link to reset password has been sent to your email. Please check');
            header("location:" . base_url('admin'));
        } else {
            $this->session->set_flashdata('error', 'Invalid Email');
            header("location:" . base_url('admin/forgot_password'));
        }
    }
    #Forgot Password Contoller ---------END----------------
    # Reset password function -------------START------------
    # This function is used to open the view page for reseting password
    public function resetPasswordChange($token, $role) {
        $this->session->set_flashdata('success', '');
        $this->session->set_flashdata('error', '');
        if ($token == '' || $role == '') {
            $data['message'] = 'Invalid link.';
            $this->load->view("admin/reset_password_message", $data);
        } else {
            //get user details
            $user_details = $this->Common->getData('users', 'email, token, token_expire_time,role', 'token="' . $token . '" and role = ' . $role . ' and status NOT IN (3,4,5)'); # DB_user_status 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
            if (!empty($user_details)) {
                if ($user_details[0]['token_expire_time'] < time()) {
                    $data['message'] = 'Reset password link expired.';
                    $this->load->view("reset_password_message", $data);
                } else {
                    /*make user data array*/
                    $data['user_data'] = ['email' => $user_details[0]['email'], 'token' => $user_details[0]['token'], 'role' => $user_details[0]['role']];
                    /*load reset_password_change for set new password*/
                    $this->load->view('reset-password-change', $data);
                }
            } else {
                $data['message'] = 'Reset password link expired.';
                $this->load->view("reset_password_message", $data);
            }
        }
    }
    # Reset password function--------- END ----------
    # resetPasswordUpdate function-------- START----------------
    # This function used for set new password
    public function resetPasswordUpdate() {
        //for get first name
        function split_name($name) {
            $name = trim($name);
            $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
            $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));
            return array($first_name, $last_name);
        }
        $this->session->set_flashdata('success', '');
        $this->session->set_flashdata('error', '');
        if ($this->input->post('reset_password_update') != '') {
            $email = $this->db->escape_str(trim($this->input->post('email')));
            $password = $this->bcrypt->hash_password(trim($this->input->post('password')));
            $token = $this->input->post('token');
            $role = $this->input->post('role');
            //get user details
            $user_details = $this->Common->getData("users", "id, email, fullname", "email = '$email' and token = '$token' and role = '$role'");
            if (!empty($user_details)) {
                $update_array = ['password' => $password, 'token' => '', 'token_expire_time' => '', 'updated_at' => time(), ];
                $id = $user_details[0]['id'];
                /*password update*/
                $this->Common->updateData('users', $update_array, 'id="' . $id . '"');
                /*mail send data code start */
                //FOR mail footer --------
                $social_urls = $this->get_social_urls();
                $mail_data['facebook_url'] = $social_urls['facebook'];
                $mail_data['google_url'] = $social_urls['google'];
                $mail_data['insta_url'] = $social_urls['insta'];
                $mail_data['website_url'] = $social_urls['website'];
                $name = split_name($user_details[0]['fullname']);
                //print_r($name);
                $mail_data['first_name'] = $name[0];
                $email = $user_details[0]['email'];
                $subject = APP_NAME . " Account Password Reset";
                //load template view
                $message = $this->load->view('email/reset_password_success', $mail_data, TRUE);
                //send mail
                send_mail($email, $subject, $message);
                /*mail send data code end */
                $data['message'] = 'Password reset successfully';
                $this->load->view("reset_password_message", $data);
            } else {
                /*make user data array*/
                $data['user_data'] = ['email' => $email, 'token' => $token, 'role' => $role];
                $this->session->set_flashdata('error', 'Something went wrong!');
                $this->load->view('reset-password-change', $data);
            }
        } else {
            $data['message'] = 'Something went wrong!';
            $this->load->view("reset_password_message", $data);
        }
    }
    #ResetPasswordUpdate function------------ END ---------
    #Admin Profile details update -----------START--------
    // image resize function----SATRT-----
    public function resize_image($filename) {
        $img_source = './uploads/' . $filename;
        $img_target = './uploads/thumbnails/';
        // image lib settings
        $config = array('image_library' => 'gd2', 'source_image' => $img_source, 'new_image' => $img_target, 'maintain_ratio' => TRUE, 'width' => 128, 'height' => 128);
        // load image library
        $this->load->library('image_lib', $config);
        // resize image
        if (!$this->image_lib->resize()) echo $this->image_lib->display_errors();
        $this->image_lib->clear();
    }
    // image resize function----END-----
    //Super Admin Account  Setting --------------------START--------------
    public function update_admin_account_settings() {
        if ($this->id && $this->role == 1) {
            $fullname = $this->db->escape_str(trim($this->input->post('fullname')));
            $email = $this->db->escape_str(trim($this->input->post('email')));
            $mobile = $this->db->escape_str(trim($this->input->post('mobile')));
            $address = $this->db->escape_str(trim($this->input->post('address')));
            //EDIT TIME
            if (empty($_FILES['admin_profile_image']['name'])) { // image not upload
                $exist_admin_profile_image = $this->db->escape_str(trim($this->input->post('edit_exist_image')));
            } else {
                $exist_admin_profile_image = $_FILES['admin_profile_image']['name']; // only for check puropse
                
            }
            $check_email = $this->Common->getData('users', 'id', '(email = "' . $email . '" OR mobile = "' . $mobile . '") AND status != 5 AND id != "' . $this->id . '"'); # user_status 5 for deleted
            // echo $this->db->last_query();
            // CHECK IF given email is already exist
            if (!empty($check_email)) {
                echo 2; //Email is already exists!
                
            } else { //!empty($check_email
                // check somthing is changed or not
                $get_admin_data = $this->Common->getData("users", "fullname,email,mobile,profile_pic,user_street_address", "id = " . $this->id . " and role = 1");
                $target = array(array('fullname' => $fullname, 'email' => $email, 'mobile' => $mobile, 'profile_pic' => $exist_admin_profile_image, 'user_street_address' => $address));
                if ($target == $get_admin_data) {
                    echo 3; // notging changed
                    
                } else if ($fullname != "" && $email != "" && $mobile != "" && $address != "") {
                    if (!empty($_FILES['admin_profile_image'])) { // if new image is upload
                        #delete previous profile image------- START --------
                        $get_previous_profile_pic = $get_admin_data[0]['profile_pic'];
                        if (!empty($get_previous_profile_pic && file_exists($get_previous_profile_pic))) {
                            //echo "The file $get_previous_profile_pic exists";
                            unlink($get_previous_profile_pic);
                        }
                        #delete previous profile image------- END -------
                        //upload image -------start
                        $exp = explode(".", $_FILES['admin_profile_image']['name']);
                        $ext = end($exp);
                        $st1 = substr(date('ymd'), 0, 3);
                        $st2 = $st1 . rand(1, 100000);
                        $fileName = $st2 . time() . date('ymd') . "." . $ext;
                        $original_image_path = 'assets/img/admin_profile/'; //orignal image path
                        $resize_image_path = 'assets/img/admin_profile/resized_profile_pic/';
                        /* Image upload  */
                        $config['upload_path'] = $original_image_path;
                        $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                        $config['file_name'] = $fileName;
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('admin_profile_image')) {
                            $error_msg = $this->upload->display_errors();
                            $message = strip_tags($error_msg);
                            $this->session->set_flashdata('error', $message);
                        } else {
                            $update_array = ['fullname' => $fullname, 'email' => $email, 'mobile' => $mobile, 'profile_pic' => $resize_image_path . $fileName, 'user_street_address' => $address, 'updated_at' => time() ];
                            $this->Common->updateData('users', $update_array, "id = " . $this->id . ' AND role = 1');
                            # Resize only if NOT SVG
                            if ($ext !== 'svg') {
                                /*Image resize function starts here*/
                                $source_image = $original_image_path . $fileName;
                                $new_image = $resize_image_path . $fileName;
                                $width = PROFILE_IMAGE_RESIZE_WIDTH;
                                $height = PROFILE_IMAGE_RESIZE_WIDTH;
                                # Call this function to rezise the image and place in a new path
                                $this->image_resize($source_image, $new_image, $width, $height);
                                /*Image resize function ends here*/
                            } else {
                                /* Image upload */
                                $config['upload_path'] = $resize_image_path;
                                $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                                $config['file_name'] = $fileName;
                                $this->upload->initialize($config);
                                if (!$this->upload->do_upload('admin_profile_image')) {
                                    $error_msg = $this->upload->display_errors();
                                    $message = strip_tags($error_msg);
                                    $this->session->set_flashdata('error', $message);
                                }
                            }
                            //echo 'success';
                            $update_status = true;
                        }
                        //upload image -------end
                        
                    } //end of !empty($_FILES['admin_profile_image']))
                    // if no new image upload
                    if ($exist_admin_profile_image != "") {
                        $update_array = ['fullname' => $fullname, 'email' => $email, 'mobile' => $mobile, 'updated_at' => time() ];
                        $this->Common->updateData('users', $update_array, "id = " . $this->id . ' AND role = 1');
                        //echo 'success';
                        $update_status = true;
                    }
                    // Final status --------
                    if ($update_status == true) {
                        //user data  set in session after update
                        //session array
                        $get_updated_admin_data = $this->Common->getData("users", "fullname,email,mobile,profile_pic", "id = " . $this->id . " and role = 1");
                        $admin_data = array('email' => $get_updated_admin_data[0]['email'], 'fullname' => $get_updated_admin_data[0]['fullname'], 'mobile' => $get_updated_admin_data[0]['mobile'], 'profile_image' => $get_updated_admin_data[0]['profile_pic'],);
                        $this->session->set_userdata($admin_data);
                        # mail_send code start. This mail sends just a thankyou mail to merchant email Id
                        $mail_data['name'] = trim($fullname);
                        $mail_data['header_title'] = APP_NAME . ' :  Your Profile Details are Updated !';
                        $mail_data['email'] = $email;
                        $email = $email;
                        $subject = "Admin Profile updated " . APP_NAME;
                        # Get Social urls from Database settings table
                        $social_urls = $this->get_social_urls();
                        $mail_data['facebook_url'] = $social_urls['facebook'];
                        $mail_data['google_url'] = $social_urls['google'];
                        $mail_data['insta_url'] = $social_urls['insta'];
                        $mail_data['website_url'] = $social_urls['website'];
                        # load template view
                        $message = $this->load->view('email/admin_profile_update', $mail_data, TRUE);
                        // echo $message;die;
                        $mail_success_status = send_mail($email, $subject, $message);
                        # mail send code end
                        if ($mail_success_status == 1) {
                            $this->session->set_flashdata('success', 'Account Setting Updated successfully');
                            echo 1;
                        } else {
                            echo 0;
                        }
                    } //$update_status == true)
                    
                } else { //else of $target  == $get_admin_data and else if of check all field is filled
                    echo 6; // some vakue is empty
                    
                }
            }
        } else { //$this->id && $this->role == 1
            $this->load->view('login');
        }
    }
    //Super Admin Account  Setting  --------------------END--------------
    #Admin Basis Setting Update --------START-------
    public function update_admin_basic_settings() {
        if ($this->id) {
            # Get all post values
            $facebook_value = $this->input->post('facebook_value');
            $fb_status = $this->input->post('fb_status');
            $insta_value = $this->input->post('insta_value');
            $insta_status = $this->input->post('insta_status');
            $google_plus = $this->input->post('google_plus');
            $google_status = $this->input->post('google_status');
            $android_version = $this->input->post('android_version');
            $ios_version = $this->input->post('ios_version');
            $support_call = $this->input->post('support_call');
            $support_email = $this->input->post('support_email');
            $merchant_play_store_value = $this->input->post('merchant_play_store_value');
            $merchant_app_store_value = $this->input->post('merchant_app_store_value');
            $customer_play_store_value = $this->input->post('customer_play_store_value');
            $customer_app_store_value = $this->input->post('customer_app_store_value');
            $website_url = $this->input->post('website_url');
            $basic_delivery_time = $this->input->post('basic_delivery_time');
            $basic_preparation_time = $this->input->post('basic_preparation_time');
            $kerela_eats_commission = $this->input->post('kerela_eats_commission');
            $restaurant_commission = $this->input->post('restaurant_commission');
            $smtp_email = $this->input->post('smtp_email');
            $smtp_password = $this->input->post('smtp_password');
            $company_name = $this->db->escape_str(trim($this->input->post('company_name')));
            $country_name = $this->db->escape_str(trim($this->input->post('country_name')));
            # GET APP VERSION FOR BOTH PLATFORM
            $android_version_merchant = $this->db->escape_str(trim($this->input->post('android_version_merchant')));
            $ios_version_merchant = $this->db->escape_str(trim($this->input->post('ios_version_merchant')));
            # cashback_validity
            $cashback_validity = $this->db->escape_str(trim($this->input->post('cashback_validity')));
            $window_time = $this->db->escape_str(trim($this->input->post('window_time')));
            # Here we need to update value as well as status hence need to update array. Below array is for updating values
            $update_value = array('facebook' => $facebook_value, 'instagram' => $insta_value, 'google' => $google_plus, 'android_version' => $android_version, 'ios_version' => $ios_version, 'support_call' => $support_call, 'support_email' => $support_email, 'merchant_play_store_url' => $merchant_play_store_value, 'merchant_app_store_url' => $merchant_app_store_value, 'customer_play_store_url' => $customer_play_store_value, 'customer_app_store_url' => $customer_app_store_value, 'website' => $website_url, 'basic_delivery_time' => $basic_delivery_time, 'basic_preparation_time' => $basic_preparation_time, 'kerela_eats_commission' => $kerela_eats_commission, 'restaurant_commission' => $restaurant_commission, 'smtp_email' => $smtp_email, 'smtp_password' => $smtp_password, 'company_name' => $company_name, 'country_name' => $country_name, 'android_version_merchant' => $android_version_merchant, 'ios_version_merchant' => $ios_version_merchant, 'cashback_validity' => $cashback_validity, 'window_time' => $window_time,);
            foreach ($update_value as $key => $value) {
                $this->Common->updateData('settings', array('value' => $value), 'name = "' . $key . '"');
            }
            $update_status = array('facebook' => $fb_status, 'instagram' => $insta_status, 'google' => $google_status,);
            # Below array is for updating status
            foreach ($update_status as $key => $value) {
                $this->Common->updateData('settings', array('status' => $value), 'name = "' . $key . '"');
            }
            $this->session->set_flashdata('success', 'Setting updated successfully');
            header("location:" . base_url('admin/setting'));
        } else {
            $this->load->view('login');
        }
    }
    #Admin Basis Setting Update --------END-------
    //Add Restaurant insert ------------START----------
    public function common_multiple_image_upload_function($uploaded_FILES_array, $upload_destiny) {
        if ($upload_destiny == 'merchant') {
            $path = 'assets/merchant/merchant_';
        }
        if ($upload_destiny == 'ad_banner') {
            $path = 'assets/images/ad_banners/';
        }
        $logo_and_banner_upload_status = array(); //upload status array
        $logo_and_banner_upload_error = array(); //upload error
        $logo_and_banner_file_name = array(); //upload file name
        // UPLOAD foreach ----START---
        foreach ($uploaded_FILES_array as $upload_file_name => $upload_file_value) {
            // Rename uploaded image name --START---
            $image_exp = explode(".", $_FILES[$upload_file_name]['name']);
            $image_ext = end($image_exp);
            $image_st1 = substr(date('ymd'), 0, 3);
            $image_st2 = $image_st1 . rand(1, 100000);
            $new_image_name = $image_st2 . time() . date('ymd') . "_" . $upload_file_name . '.' . $image_ext;
            // Rename uploaded image name -- END---
            // upload settings
            $config = array('upload_path' => $path . $upload_file_name, //$upload_file_name is folder name
            'allowed_types' => 'png|jpg|jpeg', 'max_size' => '1024', 'file_name' => $new_image_name);
            // load upload class
            $this->upload->initialize($config);
            //$this->load->library('upload', $config);
            if (!$this->upload->do_upload($upload_file_name)) {
                //upload fail
                array_push($logo_and_banner_upload_status, "false");
                $upload_error = array('error' => $this->upload->display_errors());
                $logo_and_banner_file_name = "";
                $logo_and_banner_upload_error['Error_in_' . $upload_file_name] = $upload_error;
            } else {
                //upload success
                array_push($logo_and_banner_upload_status, "true");
                $logo_and_banner_file_name['FileNameOf_' . $upload_file_name] = $new_image_name;
                $logo_and_banner_upload_error = "";
            }
        } // UPLOAD foreach ----END---
        return json_encode($logo_and_banner_upload_status) . 'array_split' . json_encode($logo_and_banner_upload_error) . 'array_split' . json_encode($logo_and_banner_file_name);
    }
    public function GeneratePassword($name, $phone) {
        $alphabet = $name . "mnopqrstuwxyzABCDEFGHINOPQRSTUWXYZ0123456789" . $phone;
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0;$i < 10;$i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
        
    }
    public function add_restaurant_controller() {
        $user_id = $this->db->escape_str(trim($this->input->post('user_id')));
        $restaurant_name = $this->db->escape_str(trim($this->input->post('restaurant_name')));
        $business_type = $this->db->escape_str(trim($this->input->post('business_type')));
        $food_type = $this->db->escape_str(trim($this->input->post('food_type')));
        $description = $this->db->escape_str(trim($this->input->post('description')));
        $delivery_handled_by = $this->db->escape_str(trim($this->input->post('delivery_handled_by')));
        $postal_code = $this->db->escape_str(trim($this->input->post('postal_code')));
        $pin_address = $this->db->escape_str(trim($this->input->post('pin_address')));
        $res_latitude = $this->db->escape_str(trim($this->input->post('res_latitude')));
        $res_longtitude = $this->db->escape_str(trim($this->input->post('res_longtitude')));
        $unit_number = $this->db->escape_str(trim($this->input->post('unit_number')));
        $open_time = $this->db->escape_str(trim($this->input->post('open_time')));
        $close_time = $this->db->escape_str(trim($this->input->post('close_time')));
        if (strpos($open_time, 'PM') !== false) { // am , pm goes from client side so thats why we need to check amd remove it
            $final_open_time = str_replace(" PM", "", $open_time);
        } else if (strpos($open_time, 'AM') !== false) {
            $final_open_time = str_replace(" AM", "", $open_time);
        } else {
            $final_open_time = $open_time;
        }
        if (strpos($close_time, 'PM') !== false) {
            $final_close_time = str_replace(" PM", "", $close_time);
        } else if (strpos($close_time, 'AM') !== false) {
            $final_close_time = str_replace(" AM", "", $close_time);
        } else {
            $final_close_time = $close_time;
        }
        //start----------
        $break_start_time = $this->db->escape_str(trim($this->input->post('break_start_time')));
        $break_end_time = $this->db->escape_str(trim($this->input->post('break_end_time')));
        if (strpos($break_start_time, 'PM') !== false) { // am , pm goes from client side so thats why we need to check amd remove it
            $final_break_start_time = str_replace(" PM", "", $break_start_time);
        } else if (strpos($break_start_time, 'AM') !== false) {
            $final_break_start_time = str_replace(" AM", "", $break_start_time);
        } else {
            $final_break_start_time = $break_start_time;
        }
        if (strpos($break_end_time, 'PM') !== false) {
            $final_break_end_time = str_replace(" PM", "", $break_end_time);
        } else if (strpos($break_end_time, 'AM') !== false) {
            $final_break_end_time = str_replace(" AM", "", $break_end_time);
        } else {
            $final_break_end_time = $break_end_time;
        }
        //end-----------
        //Restauant accept type  ------------start-------
        $rest_accept_type = $this->db->escape_str(trim($this->input->post('rest_accept_type'))); // value getting with comma seprated, only we getting here selected(checked) value
        #we need to explode this
        #ex, - order now, self pick up, order for later, dine in
        $rest_accept_type_Array = explode(',', $rest_accept_type);
        $order_now_value = 0;
        $self_pickup_value = 0; // not accept
        $order_later_value = 0; // not accept
        $dinein_value = 0; // not accept
        $check_accept_type_select_or_not = count($rest_accept_type_Array);
        foreach ($rest_accept_type_Array as $value) {
            if ($value == 'order_now') { // if its selecte
                $order_now_value = 1; //accept
                
            }
            if ($value == 'self_pickup') { // if its selecte
                $self_pickup_value = 1; //accept
                
            }
            if ($value == 'order_later') { // if its selecte
                $order_later_value = 1; //accept
                
            }
            if ($value == 'dinein') { // if its selecte
                $dinein_value = 1; //accept
                
            }
        }
        $max_dinein_capacity = $this->db->escape_str(trim($this->input->post('max_dinein_capacity')));
        //Restauant accept type  ------------end-------
        //check image is upload or not
        // check  in folowing threes image that which one is upload or which one not----start---
        if (!isSet($_FILES['logo_image']['name'])) {
            $final_logo_image = ""; //not upload
            $logo_upload_valid = 0;
        } else {
            //  image upload
            $final_logo_image = $_FILES['logo_image']['name'];
            $logo_upload_valid = 1;
        }
        //check banner upload or not
        if (!isSet($_FILES['banner_image']['name'])) {
            $final_banner_image = ""; //not upload
            $banner_upload_valid = 0;
        } else {
            //  image upload
            $final_banner_image = $_FILES['banner_image']['name'];
            $banner_upload_valid = 1;
        }
        //check mobile banner upload or not
        if (!isSet($_FILES['mobile_banner_image']['name'])) {
            $final_mobile_banner_image = ""; //not upload
            $mobile_banner_upload_valid = 0;
        } else {
            // new image upload
            $final_mobile_banner_image = $_FILES['mobile_banner_image']['name'];
            $mobile_banner_upload_valid = 1;
        }
        // check  in ABOVE threes image that which one is upload or exist image----END---
        // only one one user have only one restaurant-----
        $check_exist_user_restaurant = $this->Common->getData('restaurants', 'id', 'admin_id = "' . $user_id . '" AND rest_status != 3');
        if (!empty($check_exist_user_restaurant)) { //check IF ---START----
            echo 5; // This merchant already has a restaurant!
            
        } else {
            #All Form data is required and should not empty
            if ($user_id != "" && $restaurant_name != "" && $business_type != "" && (($max_dinein_capacity != "" && $dinein_value == 1) || ($max_dinein_capacity == "" && $dinein_value == 0) || ($max_dinein_capacity != "" && $dinein_value == 0)) && (($food_type != "" && $business_type == 1) || ($food_type == "" && $business_type != 1)) && $description != "" && $postal_code != "" && $pin_address != "" && $unit_number != "" && $check_accept_type_select_or_not > 0 && $open_time != "" && $close_time != "" && $res_latitude != "" && $res_longtitude != "" && $delivery_handled_by != "") { //Form data is required IF---START---
                //Logo and banner image upload function ---START
                $upload_status = $this->common_multiple_image_upload_function($_FILES, 'merchant');
                $new_upload_status = explode("array_split", $upload_status);
                $logo_and_banner_upload_status = json_decode($new_upload_status[0], true);
                $logo_and_banner_upload_error = json_decode($new_upload_status[1], true);
                $logo_and_banner_file_name = json_decode($new_upload_status[2], true);
                //check logo, banner and  mobile banner are new upload  ------------START-------
                if ($logo_upload_valid == 1 && $banner_upload_valid == 1 && $mobile_banner_upload_valid == 1) {
                    //echo 'three's upload';
                    if ((count(array_unique($logo_and_banner_upload_status)) === 1 && end($logo_and_banner_upload_status) === 'true') && empty($logo_and_banner_upload_error)) {
                        $update_logo_image = 'assets/merchant/merchant_logo_image/' . $logo_and_banner_file_name['FileNameOf_logo_image'] . '';
                        $update_banner_image = 'assets/merchant/merchant_banner_image/' . $logo_and_banner_file_name['FileNameOf_banner_image'] . '';
                        $update_mobile_banner_image = 'assets/merchant/merchant_mobile_banner_image/' . $logo_and_banner_file_name['FileNameOf_mobile_banner_image'];
                    } else {
                        $update_logo_image = '';
                        $update_banner_image = '';
                        $update_mobile_banner_image = '';
                    }
                }
                //check logo, banner and  mobile banner are new upload  ------------END-------
                //check logo, banner and  mobile banner are not upload ,  all are not upload file --------START-------
                if ($logo_upload_valid == 0 && $banner_upload_valid == 0 && $mobile_banner_upload_valid == 0) {
                    //noth not uploaded
                    $update_logo_image = "";
                    $update_banner_image = "";
                    $update_mobile_banner_image = "";
                }
                //check logo, banner and  mobile banner are not upload ,  all are not upload --------END-------
                //check logo upload only and banner and mobile banner are not upload, its all are exist file --------START-------
                if ($logo_upload_valid == 1 && $banner_upload_valid == 0 && $mobile_banner_upload_valid == 0) {
                    //echo 'new logo upload';
                    if ((count(array_unique($logo_and_banner_upload_status)) === 1 && end($logo_and_banner_upload_status) === 'true') && empty($logo_and_banner_upload_error)) {
                        $update_logo_image = 'assets/merchant/merchant_logo_image/' . $logo_and_banner_file_name['FileNameOf_logo_image'] . '';
                        $update_banner_image = "";
                        $update_mobile_banner_image = "";
                    } else {
                        $update_logo_image = '';
                        $update_banner_image = '';
                        $update_mobile_banner_image = '';
                    }
                }
                //check logo upload only and banner and mobile banner are exist file, its all are exist file --------END-------
                //check banner upload only and logo and mobile banner are exist file, its all are exist file --------START-------
                if ($banner_upload_valid == 1 && $mobile_banner_upload_valid == 0 && $logo_upload_valid == 0) {
                    //echo 'new banner upload';
                    if ((count(array_unique($logo_and_banner_upload_status)) === 1 && end($logo_and_banner_upload_status) === 'true') && empty($logo_and_banner_upload_error)) {
                        $update_banner_image = 'assets/merchant/merchant_banner_image/' . $logo_and_banner_file_name['FileNameOf_banner_image'] . '';
                        $update_logo_image = "";
                        $update_mobile_banner_image = $mobile_banner_image;
                    } else {
                        $update_banner_image = '';
                        $update_logo_image = '';
                        $update_mobile_banner_image = '';
                    }
                }
                //check banner upload only and logo and mobile banner are exist file, its all are exist file --------END-------
                //check mobile banner upload only and logo and  banner are exist file, its all are exist file --------START-------
                if ($mobile_banner_upload_valid == 1 && $banner_upload_valid == 0 && $logo_upload_valid == 0) {
                    //echo 'new banner upload';
                    if ((count(array_unique($logo_and_banner_upload_status)) === 1 && end($logo_and_banner_upload_status) === 'true') && empty($logo_and_banner_upload_error)) {
                        $update_mobile_banner_image = 'assets/merchant/merchant_mobile_banner_image/' . $logo_and_banner_file_name['FileNameOf_mobile_banner_image'];
                        $update_banner_image = "";
                        $update_logo_image = "";
                    } else {
                        $update_banner_image = '';
                        $update_logo_image = '';
                        $update_mobile_banner_image = '';
                    }
                }
                //check mobile banner upload only and logo and  banner are exist file, its all are exist file --------END-------
                /*  if($update_logo_image != "" && $update_banner_image != "" && $update_mobile_banner_image != ""){*/
                # Now make entry to RESTAURANT table
                $merchant_insert = ['admin_id' => $user_id, 'rest_name' => trim($restaurant_name), 'business_type' => trim($business_type), 'food_type' => trim($food_type), //default is 0 ,If restaurant's business type is food then this column will contain value. So value 0 (if not applicable) 1 (Restaurant) 2 (Kitchen)
                'res_description' => trim($description), 'rest_pin_address' => trim($pin_address), 'rest_unit_number' => trim($unit_number), 'rest_postal_code' => trim($postal_code), 'is_order_now_accept' => $order_now_value, 'is_self_pickup_accept' => $self_pickup_value, 'is_order_later_accept' => $order_later_value, 'is_dinein_accept' => $dinein_value, 'max_capacity' => $max_dinein_capacity, 'delivery_handled_by' => $delivery_handled_by, //1 - restaurant 2 - By Kerala Eats
                'time_mode' => 1, //	1 - for every day, 2 - for Specific day (if value 1 then open close and break time will be insert this table , if value 2 then open-close and break time will be insert in rest_time_daywise table )
                'open_time' => $final_open_time, 'close_time' => $final_close_time, 'break_start_time' => $final_break_start_time, 'break_end_time' => $final_break_end_time, 'logo_image' => trim($update_logo_image), 'banner_image' => trim($update_banner_image), 'mobile_banner_image' => trim($update_mobile_banner_image), 'created_at' => time(), ];
                //print_r( $merchant_insert);
                $insert_status = $this->Common->insertData('restaurants', $merchant_insert);
                if ($insert_status > 0) {
                    $user_data = $this->Common->getData('users', 'fullname,email', 'status != 5 AND id != "' . $user_id . '"');
                    # mail_send code start. This mail sends just a thankyou mail to merchant email Id
                    $mail_data['name'] = trim($user_data[0]['fullname']);
                    $mail_data['restaurant_name'] = trim($restaurant_name);
                    $mail_data['header_title'] = APP_NAME . ' : Thank you for your intrest !';
                    $mail_data['email'] = trim($user_data[0]['email']);
                    $email = trim($user_data[0]['email']);
                    $subject = "Welcome to " . APP_NAME;
                    # Get Social urls from Database settings table
                    $social_urls = $this->get_social_urls();
                    $mail_data['facebook_url'] = $social_urls['facebook'];
                    $mail_data['google_url'] = $social_urls['google'];
                    $mail_data['insta_url'] = $social_urls['insta'];
                    $mail_data['website_url'] = $social_urls['website'];
                    # load template view
                    $message = $this->load->view('email/restaurant_added_by_admin', $mail_data, TRUE);
                    // echo $message;die;
                    $mail_success_status = send_mail($email, $subject, $message);
                    # mail send code end
                    if ($mail_success_status == 1) {
                        // update pin address same in user table for merchant(mercahnt and restatuant address should be same)
                        $update_array = ['user_pin_address' => trim($pin_address), 'user_unit_number' => trim($unit_number), 'user_postal_code' => trim($postal_code), 'latitude' => $res_latitude, 'longitude' => $res_longtitude, 'updated_at' => time() ];
                        $update_status = $this->Common->updateData('users', $update_array, 'id = "' . $user_id . '"');
                        if ($update_status > 0) {
                            $this->session->set_flashdata('success', 'Restaurant added successfully!');
                            echo 1; //success
                            
                        } else {
                            echo 0; // restaurant insert success , user table update issue
                            
                        }
                    } else {
                        echo 0; //data insert but email not sent
                        
                    }
                } else {
                    echo 2; //not insert data
                    
                }
                //}
                
            } else { //Form data is required ---ELSE END---
                echo 4; //some field is empty
                
            }
        } //chek user restaurant else end----
        
    }
    //Add Restaurant insert ------------END----------
    public function edit_restaurant_controller() {
        $user_id = $this->db->escape_str(trim($this->input->post('user_id')));
        $restaurant_name = $this->db->escape_str(trim($this->input->post('restaurant_name')));
        $business_type = $this->db->escape_str(trim($this->input->post('business_type')));
        $food_type = $this->db->escape_str(trim($this->input->post('food_type')));
        $description = $this->db->escape_str(trim($this->input->post('description')));
        $delivery_handled_by = $this->db->escape_str(trim($this->input->post('delivery_handled_by')));
        $postal_code = $this->db->escape_str(trim($this->input->post('postal_code')));
        $pin_address = $this->db->escape_str(trim($this->input->post('pin_address')));
        $unit_number = $this->db->escape_str(trim($this->input->post('unit_number')));
        $res_latitude = $this->db->escape_str(trim($this->input->post('res_latitude')));
        $res_longtitude = $this->db->escape_str(trim($this->input->post('res_longtitude')));
        //update  id
        $restaurant_id = $this->db->escape_str(trim($this->input->post('restaurant_id')));
        $logo_image = $this->db->escape_str(trim($this->input->post('logo_image'))); //exist image
        $banner_image = $this->db->escape_str(trim($this->input->post('banner_image'))); //exist image
        $mobile_banner_image = $this->db->escape_str(trim($this->input->post('mobile_banner_image'))); //exist image
        // check  in folowing threes image that which one is upload or exist image----start---
        //check logo upload or exist
        if (!isSet($_FILES['logo_image']['name']) != "") {
            // if pre image exist , no any image upload
            $final_logo_image = $logo_image;
            $logo_upload_valid = 0;
        } else {
            // new image upload
            $final_logo_image = $_FILES['logo_image']['name'];
            $logo_upload_valid = 1;
        }
        //check banner upload or exist
        if (!isSet($_FILES['banner_image']['name']) || $banner_image != "") {
            // if pre image exist , no any image upload
            $final_banner_image = $banner_image;
            $banner_upload_valid = 0;
        } else {
            // new image upload
            $final_banner_image = $_FILES['banner_image']['name'];
            $banner_upload_valid = 1;
        }
        //check mobile banner upload or exist
        if (!isSet($_FILES['mobile_banner_image']['name']) || $mobile_banner_image != "") {
            // if pre image exist , no any image upload
            $final_mobile_banner_image = $mobile_banner_image;
            $mobile_banner_upload_valid = 0;
        } else {
            // new image upload
            $final_mobile_banner_image = $_FILES['mobile_banner_image']['name'];
            $mobile_banner_upload_valid = 1;
        }
        // check  in ABOVE threes image that which one is upload or exist image----END---
        //Restauant accept type  ------------start-------
        $rest_accept_type = $this->db->escape_str(trim($this->input->post('rest_accept_type'))); // value getting with comma seprated, only we getting here selected(checked) value
        #we need to explode this
        #ex, - order now, self pick up, order for later, dine in
        $rest_accept_type_Array = explode(',', $rest_accept_type);
        $check_accept_type_select_or_not = count($rest_accept_type_Array);
        $order_now_value = 0;
        $self_pickup_value = 0; // not accept
        $order_later_value = 0; // not accept
        $dinein_value = 0; // not accept
        foreach ($rest_accept_type_Array as $value) {
            if ($value == 'order_now') { // if its selecte
                $order_now_value = 1; //accept
                
            }
            if ($value == 'self_pickup') { // if its selecte
                $self_pickup_value = 1; //accept
                
            }
            if ($value == 'order_later') { // if its selecte
                $order_later_value = 1; //accept
                
            }
            if ($value == 'dinein') { // if its selecte
                $dinein_value = 1; //accept
                
            }
        }
        $max_dinein_capacity = $this->db->escape_str(trim($this->input->post('max_dinein_capacity')));
        //Restauant accept type  ------------end-------
        $get_restaurant_data = $this->Common->getData('restaurants', 'rest_name,business_type,food_type,admin_id,logo_image,banner_image,mobile_banner_image,rest_pin_address,rest_unit_number,rest_postal_code,res_description,is_order_now_accept,is_self_pickup_accept,is_order_later_accept,	is_dinein_accept,max_capacity,delivery_handled_by', 'id = ' . $restaurant_id . ' AND rest_status NOT IN(3)', '', '', '', '');
        $target = array(array('rest_name' => $restaurant_name, 'business_type' => $business_type, 'food_type' => $food_type, 'admin_id' => $user_id, 'logo_image' => $final_logo_image, 'banner_image' => $final_banner_image, 'mobile_banner_image' => $final_mobile_banner_image, 'rest_pin_address' => $pin_address, 'rest_unit_number' => $unit_number, 'rest_postal_code' => $postal_code, 'res_description' => $description, 'is_order_now_accept' => $order_now_value, 'is_self_pickup_accept' => $self_pickup_value, 'is_order_later_accept' => $order_later_value, 'is_dinein_accept' => $dinein_value, 'max_capacity' => $max_dinein_capacity, 'delivery_handled_by' => $delivery_handled_by));
        //custom make array for check if any changes done to confrm
        /* print_r( $get_restaurant_data);
         print_r( $target);*/
        // only one one user have only one restaurant-----
        $check_exist_user_restaurant = $this->Common->getData('restaurants', 'id', 'admin_id = "' . $user_id . '" AND id != "' . $restaurant_id . '" AND rest_status != 3');
        if (!empty($check_exist_user_restaurant)) { //check IF ---START----
            echo 5; // This merchant already has a restaurant!
            
        } else {
            #All Form data is required and should not empty
            if ($user_id != "" && $restaurant_name != "" && $description != "" && $postal_code != "" && $pin_address != "" && $unit_number != "" && $check_accept_type_select_or_not > 0 && $res_latitude != "" && $res_longtitude != "" && $delivery_handled_by != "" && (($max_dinein_capacity != "" && $dinein_value == 1) || ($max_dinein_capacity == "" && $dinein_value == 0) || ($max_dinein_capacity != "" && $dinein_value == 0))) { //Form data is required IF---START--- // && $final_logo_image != '' && $final_banner_image != '' && $final_mobile_banner_image != ''
                //Logo and banner image upload function ---START
                $upload_status = $this->common_multiple_image_upload_function($_FILES, 'merchant');
                $new_upload_status = explode("array_split", $upload_status);
                $logo_and_banner_upload_status = json_decode($new_upload_status[0], true);
                $logo_and_banner_upload_error = json_decode($new_upload_status[1], true);
                $logo_and_banner_file_name = json_decode($new_upload_status[2], true);
                //Logo and banner image upload function ---END
                //check logo, banner and  mobile banner are new upload  ------------START-------
                if ($logo_upload_valid == 1 && $banner_upload_valid == 1 && $mobile_banner_upload_valid == 1) {
                    //echo 'three's upload';
                    if ((count(array_unique($logo_and_banner_upload_status)) === 1 && end($logo_and_banner_upload_status) === 'true') && empty($logo_and_banner_upload_error)) {
                        $update_logo_image = 'assets/merchant/merchant_logo_image/' . $logo_and_banner_file_name['FileNameOf_logo_image'] . '';
                        $update_banner_image = 'assets/merchant/merchant_banner_image/' . $logo_and_banner_file_name['FileNameOf_banner_image'] . '';
                        $update_mobile_banner_image = 'assets/merchant/merchant_mobile_banner_image/' . $logo_and_banner_file_name['FileNameOf_mobile_banner_image'];
                    } else {
                        $update_logo_image = '';
                        $update_banner_image = '';
                        $update_mobile_banner_image = '';
                    }
                }
                //check logo, banner and  mobile banner are new upload  ------------END-------
                //check logo, banner and  mobile banner are not upload , its all are exist file --------START-------
                if ($logo_upload_valid == 0 && $banner_upload_valid == 0 && $mobile_banner_upload_valid == 0) {
                    // echo 'both  exist file';
                    $update_logo_image = $logo_image;
                    $update_banner_image = $banner_image;
                    $update_mobile_banner_image = $mobile_banner_image;
                }
                //check logo, banner and  mobile banner are not upload , its all are exist file --------END-------
                //check logo upload only and banner and mobile banner are exist file, its all are exist file --------START-------
                if ($logo_upload_valid == 1 && $banner_upload_valid == 0 && $mobile_banner_upload_valid == 0) {
                    //echo 'new logo upload';
                    if ((count(array_unique($logo_and_banner_upload_status)) === 1 && end($logo_and_banner_upload_status) === 'true') && empty($logo_and_banner_upload_error)) {
                        #delete previous logo image------- START --------
                        $get_logo_pic = $this->Common->getData("restaurants", "logo_image", "id != " . $restaurant_id . "");
                        $get_previous_logo_pic = $get_logo_pic[0]['logo_image'];
                        if (!empty($get_previous_logo_pic && file_exists($get_previous_logo_pic))) {
                            //echo "The file $get_previous_profile_pic exists";
                            unlink($get_previous_logo_pic);
                        }
                        #delete previous logo image------- END -------
                        $update_logo_image = 'assets/merchant/merchant_logo_image/' . $logo_and_banner_file_name['FileNameOf_logo_image'] . '';
                        $update_banner_image = $banner_image;
                        $update_mobile_banner_image = $mobile_banner_image;
                    } else {
                        $update_logo_image = '';
                        $update_banner_image = '';
                        $update_mobile_banner_image = '';
                    }
                }
                //check logo upload only and banner and mobile banner are exist file, its all are exist file --------END-------
                //check banner upload only and logo and mobile banner are exist file, its all are exist file --------START-------
                if ($banner_upload_valid == 1 && $mobile_banner_upload_valid == 0 && $logo_upload_valid == 0) {
                    //echo 'new banner upload';
                    if ((count(array_unique($logo_and_banner_upload_status)) === 1 && end($logo_and_banner_upload_status) === 'true') && empty($logo_and_banner_upload_error)) {
                        #delete previous banner image------- START --------
                        $get_banner_pic = $this->Common->getData("restaurants", "banner_image", "id != " . $restaurant_id . "");
                        $get_previous_banner_pic = $get_banner_pic[0]['banner_image'];
                        if (!empty($get_previous_banner_pic && file_exists($get_previous_banner_pic))) {
                            unlink($get_previous_banner_pic);
                        }
                        #delete previous banner image------- END -------
                        $update_banner_image = 'assets/merchant/merchant_banner_image/' . $logo_and_banner_file_name['FileNameOf_banner_image'] . '';
                        $update_logo_image = $logo_image;
                        $update_mobile_banner_image = $mobile_banner_image;
                    } else {
                        $update_banner_image = '';
                        $update_logo_image = '';
                        $update_mobile_banner_image = '';
                    }
                }
                //check banner upload only and logo and mobile banner are exist file, its all are exist file --------END-------
                //check mobile banner upload only and logo and  banner are exist file, its all are exist file --------START-------
                if ($mobile_banner_upload_valid == 1 && $banner_upload_valid == 0 && $logo_upload_valid == 0) {
                    //echo 'new banner upload';
                    if ((count(array_unique($logo_and_banner_upload_status)) === 1 && end($logo_and_banner_upload_status) === 'true') && empty($logo_and_banner_upload_error)) {
                        #delete previous banner image------- START --------
                        $get_mobile_banner_pic = $this->Common->getData("restaurants", "mobile_banner_image", "id != " . $restaurant_id . "");
                        $get_previous_mobile_banner_pic = $get_mobile_banner_pic[0]['mobile_banner_image'];
                        if (!empty($get_previous_mobile_banner_pic && file_exists($get_previous_mobile_banner_pic))) {
                            unlink($get_previous_mobile_banner_pic);
                        }
                        #delete previous banner image------- END -------
                        $update_mobile_banner_image = 'assets/merchant/merchant_mobile_banner_image/' . $logo_and_banner_file_name['FileNameOf_mobile_banner_image'];
                        $update_banner_image = $banner_image;
                        $update_logo_image = $logo_image;
                    } else {
                        $update_banner_image = '';
                        $update_logo_image = '';
                        $update_mobile_banner_image = '';
                    }
                }
                //check mobile banner upload only and logo and  banner are exist file, its all are exist file --------END-------
                if ($get_restaurant_data != $target) {
                    /*  if($update_logo_image != "" && $update_banner_image != "" && $update_mobile_banner_image != ""){*/
                    //echo 'All upload and exist status is true';
                    $user_data = $this->Common->getData('users', 'fullname,email', 'status != 5 AND id != "' . $user_id . '"');
                    // print_r($get_restaurant_data);
                    //print_r($target);
                    # Now make entry to RESTAURANT table
                    $merchant_update = ['admin_id' => trim($user_id), 'rest_name' => trim($restaurant_name), 'business_type' => trim($business_type), 'food_type' => trim($food_type), 'res_description' => trim($description), 'rest_pin_address' => trim($pin_address), 'rest_unit_number' => trim($unit_number), 'rest_postal_code' => trim($postal_code), 'logo_image' => trim($update_logo_image), 'banner_image' => trim($update_banner_image), 'mobile_banner_image' => trim($update_mobile_banner_image), 'is_order_now_accept' => $order_now_value, 'is_self_pickup_accept' => $self_pickup_value, 'is_order_later_accept' => $order_later_value, 'is_dinein_accept' => $dinein_value, 'max_capacity' => $max_dinein_capacity, 'delivery_handled_by' => $delivery_handled_by, //1 - restaurant 2 - By Kerala Eats
                    'updated_at' => time(), ];
                    $update_status = $this->Common->updateData('restaurants', $merchant_update, 'id = ' . $restaurant_id . '');
                    if ($update_status > 0) {
                        # mail_send code start. This mail sends just a thankyou mail to merchant email Id
                        $mail_data['name'] = trim($user_data[0]['fullname']);
                        $mail_data['restaurant_name'] = trim($restaurant_name);
                        $mail_data['header_title'] = APP_NAME . ' : Your Restaurant Details are Edited !';
                        $mail_data['email'] = trim($user_data[0]['email']);
                        $email = trim($user_data[0]['email']);
                        $subject = "Your Restaurant Details are Edited by " . APP_NAME . " Administrator";
                        # Get Social urls from Database settings table
                        $social_urls = $this->get_social_urls();
                        $mail_data['facebook_url'] = $social_urls['facebook'];
                        $mail_data['google_url'] = $social_urls['google'];
                        $mail_data['insta_url'] = $social_urls['insta'];
                        $mail_data['website_url'] = $social_urls['website'];
                        # load template view
                        $message = $this->load->view('email/restaurant_edited_by_admin', $mail_data, TRUE);
                        // echo $message;die;
                        $mail_success_status = send_mail($email, $subject, $message);
                        # mail send code end
                        if ($mail_success_status == 1) {
                            // update pin address same in user table for merchant(mercahnt and restatuant address should be same)
                            $update_array = ['user_pin_address' => trim($pin_address), 'user_unit_number' => trim($unit_number), 'user_postal_code' => trim($postal_code), 'latitude' => $res_latitude, 'longitude' => $res_longtitude, 'updated_at' => time() ];
                            $update_status = $this->Common->updateData('users', $update_array, 'id = "' . $user_id . '"');
                            if ($update_status > 0) {
                                $this->session->set_flashdata('success', 'Restaurant Updated successfully!');
                                echo 1; //success
                                
                            } else {
                                echo 0; // restaurant update success , user table update issue
                                
                            }
                        } else {
                            echo 0; //data updated but email not sent
                            
                        }
                    }
                    /*}else{//$update_logo_image != "" && $update_banner_image != "" && $update_mobile_banner_image != ""
                    echo 3;
                    }*/
                } else {
                    echo 2; //no changes
                    
                }
            } else { //Form data is required ---ELSE END---
                echo 4; //some field is empty
                
            }
        }
    }
    //Enable /Disable(active/inactive) toggle of Restaurant------ START------
    public function active_inactive_restaurant() {
        $restaurant_id = $this->db->escape_str(trim($this->input->post('restaurant_id')));
        $enable_disable_status = $this->db->escape_str(trim($this->input->post('enable_disable_status')));
        $update_array = ['rest_status' => $enable_disable_status, //1 - Enable 2 - Disable 3 - Deleted
        'updated_at' => time() ];
        # update data in restaurant table
        $update_status = $this->Common->updateData('restaurants', $update_array, "id = " . $restaurant_id);
        if ($update_status > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
    //Enable /Disable(active/inactive) toggle of Restaurant------ END------
    // Restaurant Delete -------------------- START----------
    public function delete_restaurant() {
        $restaurant_id = $this->db->escape_str(trim($this->input->post('restaurant_id')));
        $update_array = ['rest_status' => 3, // 1 - Enable, 2 - Disable, 3 - Deleted
        'updated_at' => time() ];
        # update data in restaurant table
        $update_status = $this->Common->updateData('restaurants', $update_array, "id = " . $restaurant_id);
        if ($update_status > 0) {
            // getting merchant id  of the this restaurant
            // we need to manage delete status in user table
            $get_merchant_id = $this->Common->getData("restaurants", "admin_id", "id = " . $restaurant_id . ""); // status 3 = delete
            if (!empty($get_merchant_id)) {
                $update_array = ['status' => 5, // 1 - Enable, 2 - Disable, 3 - Deleted
                'updated_at' => time() ];
                # update data in user table
                $update_user_status = $this->Common->updateData('users', $update_array, "id = " . $get_merchant_id[0]['admin_id'] . "");
                if ($update_user_status > 0) {
                    echo 1;
                } else {
                    echo 0;
                }
            } else {
                echo 1; // only restaurnat table will update
                
            }
        } else {
            echo 0;
        }
    }
    //Restaurant Delete ------ END------
    //Show Restaurant ---------Update Commission of restaruant -------START-----
    public function update_restaurant_commission() {
        $selected_restaurant_id = $this->db->escape_str(trim($this->input->post('selected_restaurant_id')));
        $rest_commission_type = $this->db->escape_str(trim($this->input->post('rest_commission_type')));
        $rest_commission_value = $this->db->escape_str(trim($this->input->post('rest_commission_value')));
        $get_exist_commission_res_data = $this->Common->getData("restaurants", "commission_type,commission_value", "id = " . $selected_restaurant_id . "");
        //check if commission type and value is not exist then it will be update other wise no will be change
        if ($get_exist_commission_res_data[0]['commission_type'] == $rest_commission_type && $get_exist_commission_res_data[0]['commission_value'] == $rest_commission_value) {
            echo 2; //nothing changed
            
        } else {
            $update_array = ['commission_type' => $rest_commission_type, //1- flat/fixed, 2 - percent =  default 0
            'commission_value' => $rest_commission_value, 'updated_at' => time() ];
            # update data in restaurant table
            $update_status = $this->Common->updateData('restaurants', $update_array, "id = " . $selected_restaurant_id);
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }
    //Show Restaurant ---------Update Commission of restaruant -------END-----
    // Get product offline data------ START----
    public function GET_RestaurantOffline_Data() {
        $rest_id = $this->db->escape_str(trim($this->input->post('rest_id')));
        $offline_status_data = $this->Common->getData('rest_offline', 'rest_offline.offline_tag,rest_offline.offline_value,rest_offline.offline_from,rest_offline.offline_to', 'rest_offline.rest_id = "' . $rest_id . '"', '', '');
        if (!empty($offline_status_data)) {
            //date_default_timezone_set('Asia/Singapore');
            $offline_data['offline_tag'] = $offline_status_data[0]['offline_tag'];
            $offline_from = $offline_status_data[0]['offline_from'];
            $offline_data['offline_from'] = $offline_from = date("d-m-Y", $offline_from); // convert UNIX timestamp to PHP DateTime
            $offline_to = $offline_status_data[0]['offline_to'];
            $offline_data['offline_to'] = $offline_from = date("d-m-Y", $offline_to); // convert UNIX timestamp to PHP DateTime
            if (count($offline_status_data) > 0) {
                echo json_encode($offline_data);
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }
    // Get product offline data------ END------
    //Show Restaurant ---------Change Status Offline online of restaruant-------START-----
    //Check date for toggle
    public function check_given_date_for_toggle($rest_id = "") {
        $offline_status_data = $this->Common->getData('rest_offline', 'rest_offline.offline_tag,rest_offline.offline_value,rest_offline.offline_from,rest_offline.offline_to', 'rest_offline.rest_id = "' . $rest_id . '"', '', '');
        //print_r($offline_status_data);
        //we need to check current time (between from and to date)for getting offline status becouse may be possible from and to date is complete but restaurant didnot changed status (untill online , offline entery will be available) that's why
        date_default_timezone_set('Asia/Singapore');
        if (!empty($offline_status_data)) {
            if ($offline_status_data[0]['offline_tag'] != 4) {
                if ((time() >= $offline_status_data[0]['offline_from']) && (time() <= $offline_status_data[0]['offline_to'])) {
                    $offline_toggle_status = "1"; //Offline
                    
                } else {
                    $offline_toggle_status = "2"; //Online
                    
                }
            } else # UNLIMITED_OFFLINE
            {
                $offline_toggle_status = "1"; //Offline
                
            }
        } else {
            $offline_toggle_status = "0"; // Somthing went wrong
            
        }
        return $offline_toggle_status;
    }
    //go online-------------
    public function update_rest_online() {
        $offline_type = $this->db->escape_str(trim($this->input->post('offline_type')));
        #offline_type	1 - GOING OFFLINE 2 - COMING BACK ONLINE
        $rest_id = $this->db->escape_str(trim($this->input->post('selected_restaurant_id')));
        if ($offline_type == 0) # COMING BACK ONLINE
        {
            $delete_status = $this->Common->deleteData('rest_offline', 'rest_id = "' . $rest_id . '"');
            if ($delete_status > 0) {
                /*$update_array['is_going_offline'] = 0;
                $update_status = $this->Common->updateData('restaurants',$update_array,"id = ".$rest_id);
                if($update_status > 0){
                echo 1;
                }else{
                echo 0;
                }*/
                echo 1;
            } else {
                echo 0;
            }
        }
    }
    //go offline-------------
    public function update_rest_offline() {
        // echo "TIMEZONE".date_default_timezone_get();
        $offline_type = $this->db->escape_str(trim($this->input->post('offline_type')));
        #offline_type	1 - GOING OFFLINE 2 - COMING BACK ONLINE
        $rest_id = $this->db->escape_str(trim($this->input->post('selected_restaurant_id')));
        if ($offline_type == 1) # GOING OFFLINE
        {
            $offline_tag = $this->db->escape_str(trim($this->input->post('offline_tag')));
            #DB_offline_tag	1 - Hour 2 - Day 3 - Multiple days
            $offline_value = $this->db->escape_str(trim($this->input->post('offline_value')));
            # One important case noted here is that suppose any restaurant went offline till 5 pm now 5 pm is passed and toggle is also on due to front end check but still we have an entry in offline table because we delete entry only when merchant manually come back online. So in going offline also we will first delete data from offlne table for this restaurant and add new entry as per given data
            $this->Common->deleteData('rest_offline', 'rest_id = "' . $rest_id . '"');
            if ($offline_tag == 1) # HOURS : If value is 3 that means go offline FOR NEXT 3 HOURS
            {
                # Value will always be passed in HOURS:MINUTES Format. Ex 4 hours and 30 minutes (4:30) and 4 hours (4:00)
                if (strpos($offline_value, ":") !== false) # We are keeping : to separate the hours and minutes value
                {
                    # That means minutes also exists (Ex 4 hours and 30 minutes 4:30)
                    $hm = explode(":", $offline_value);
                    $hours = $hm[0];
                    $minutes = $hm[1];
                    $offline_from = time();
                    $to_add_h = $hours * (60 * 60); # Convert the hours into seconds.
                    $to_add_m = $minutes * 60; # Convert the minutes into seconds.
                    $offline_till = $offline_from + $to_add_h + $to_add_m;
                    $expiration_date = strtotime("" . $hours . " hours");
                } else # However it won't be used but just kept it here.
                {
                    # Only Hours value exists (Ex Next 4 hours)
                    $hours = $offline_value;
                    $offline_from = time();
                    $to_add = $hours * (60 * 60); # Convert the hours into seconds.
                    $offline_till = $offline_from + $to_add;
                }
            } else if ($offline_tag == 2) # A day i.e. Single timstamp value of selected date
            {
                $offline_value = str_replace('/', '-', $offline_value);
                $todays_date = date("d-m-Y");
                $today = strtotime($todays_date);
                $expiration_date = strtotime($offline_value);
                $offline_from = $expiration_date;
                # For offline_till here mobile team should pass timestamp for selected date. Ex today's date is 22/03/2021 and selected date is 25/03/2021 that means restaurant will be offline on 25/03/2021 so mobile team will pass timstamp for 25/03/2021 00:00:00 (1616630400) and we will set to date of 25/03/2021 as 25/03/2021 12:59:59 that we will add 24 hours to this date.
                $to_add = 24 * (60 * 60); # Convert the hours into seconds.
                $offline_till = $offline_from + $to_add;
                $offline_till = strtotime($offline_value);
                $offline_till = strtotime('+1440 minutes', $offline_till); //24 hours of till date
                $offline_till = strtotime('-1 minutes', $offline_till); //less 1 minut for till date 11:59 becouse from 00:00 start next day
                
            } else if ($offline_tag == 3) # Multiple days i.e. From and To , two timestamp will be passed from mobile team
            {
                # Ex : Today's date is 22nd march and Restaurant is going offline from 23rd to 25th of march so how we will proceed with it? Simple we have two timstamps one is from 23/03/2021 00:00:00 to 25/03/2021 11:59:59 so this type of timestamp must be provided from the mobile team COMMA Separated and we are going to simply explode and update them in DB
                $exp = explode(",", $offline_value);
                $expiration_date_from = $exp[0];
                $expiration_date_till = $exp[1];
                //from date
                $offline_from_date = str_replace('/', '-', $expiration_date_from);
                $todays_date_from = date("d-m-Y");
                $today = strtotime($todays_date_from);
                $offline_from = strtotime($offline_from_date);
                // till date
                $offline_till_date = str_replace('/', '-', $expiration_date_till);
                $todays_date_till = date("d-m-Y");
                $today = strtotime($todays_date_till);
                $offline_till = strtotime($offline_till_date);
                $offline_till = strtotime('+1440 minutes', $offline_till); //24 hours of till date
                $offline_till = strtotime('-1 minutes', $offline_till); //less 1 minut for till date 11:59 becouse from 00:00 start next day
                $expiration_date = $offline_from . ',' . $offline_till;
            } elseif ($offline_tag == 4) #UNLIMITED_OFFLINE
            {
                $expiration_date = 4;
                $offline_from = 4;
                $offline_till = 4;
                $offline_tag = 4;
            } else {
                $data['status'] = 201;
                $data['message'] = $this->lang->line('invalid_offline_tag');
                $data['data'] = array();
            }
            $insert_array['rest_id'] = $rest_id;
            $insert_array['offline_tag'] = $offline_tag;
            $insert_array['offline_value'] = $expiration_date;
            $insert_array['offline_from'] = $offline_from;
            $insert_array['offline_to'] = $offline_till;
            $insert_array['created_at'] = time();
            $insert_array['updated_at'] = time();
            $insert_status = $this->Common->insertData('rest_offline', $insert_array);
            if ($insert_status > 0) {
                //check given date  with current date for toggle
                #Ex. - if day and multiday selected for future date not for today then toggle should be enable for today. other wise disable
                $toggle_status = $this->check_given_date_for_toggle($rest_id);
                echo $toggle_status;
            } else {
                echo 0;
            }
        }
    }
    //Show Restaurant ---------Change Status Offline online of restaruant-------END-----
    ///Show Restaurant ---------Update OPEN CLOSE /BREAK TIME restaruant -------START-----
    public function update_res_open_close_break_time() {
        $selected_restaurant_id = $this->db->escape_str(trim($this->input->post('selected_restaurant_id')));
        $rest_time_mode = $this->db->escape_str(trim($this->input->post('rest_time_mode'))); //1 - for every day, 2 - for Specific day (if value 1 then open close and break time will be insert this table , if value 2 then open-close and break time will be insert in rest_time_daywise table )
        if ($rest_time_mode == 2) {
            //1 - for every day, 2 - for Specific day (if value 1 then open close and break time will be insert this table , if value 2 then open-close and break time will be insert in rest_time_daywise table )
            //Day Wise open close rime-----------------start------------------
            $mon_open_close_time = $this->db->escape_str(trim($this->input->post('mon_open_close_time')));
            $tue_open_close_time = $this->db->escape_str(trim($this->input->post('tue_open_close_time')));
            $wed_open_close_time = $this->db->escape_str(trim($this->input->post('wed_open_close_time')));
            $thu_open_close_time = $this->db->escape_str(trim($this->input->post('thu_open_close_time')));
            $fri_open_close_time = $this->db->escape_str(trim($this->input->post('fri_open_close_time')));
            $sat_open_close_time = $this->db->escape_str(trim($this->input->post('sat_open_close_time')));
            $sun_open_close_time = $this->db->escape_str(trim($this->input->post('sun_open_close_time')));
            //Day Wise open close rime-----------------end------------------
            //Day Wise Break Start End time-----------------start------------------
            $mon_break_status = $this->db->escape_str(trim($this->input->post('mon_break_status'))); //2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)
            $mon_break_start_end_time = $this->db->escape_str(trim($this->input->post('mon_break_start_end_time')));
            $tue_break_status = $this->db->escape_str(trim($this->input->post('tue_break_status'))); //2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)
            $tue_break_start_end_time = $this->db->escape_str(trim($this->input->post('tue_break_start_end_time')));
            $wed_break_status = $this->db->escape_str(trim($this->input->post('wed_break_status'))); //2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)
            $wed_break_start_end_time = $this->db->escape_str(trim($this->input->post('wed_break_start_end_time')));
            $thu_break_status = $this->db->escape_str(trim($this->input->post('thu_break_status'))); //2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)
            $thu_break_start_end_time = $this->db->escape_str(trim($this->input->post('thu_break_start_end_time')));
            $fri_break_status = $this->db->escape_str(trim($this->input->post('fri_break_status'))); //2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)
            $fri_break_start_end_time = $this->db->escape_str(trim($this->input->post('fri_break_start_end_time')));
            $sat_break_status = $this->db->escape_str(trim($this->input->post('sat_break_status'))); //2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)
            $sat_break_start_end_time = $this->db->escape_str(trim($this->input->post('sat_break_start_end_time')));
            $sun_break_status = $this->db->escape_str(trim($this->input->post('sun_break_status'))); //2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)
            $sun_break_start_end_time = $this->db->escape_str(trim($this->input->post('sun_break_start_end_time')));
            //Day Wise Break Start End time-----------------end------------------
            // restaurant close/off day-------------------START-----------
            // echo "STATUS".$this->db->escape_str(trim($this->input->post('mon_close_status')));
            $mon_close_status = $this->db->escape_str(trim($this->input->post('mon_close_status')));
            $tue_close_status = $this->db->escape_str(trim($this->input->post('tue_close_status')));
            $wed_close_status = $this->db->escape_str(trim($this->input->post('wed_close_status')));
            $thu_close_status = $this->db->escape_str(trim($this->input->post('thu_close_status')));
            $fri_close_status = $this->db->escape_str(trim($this->input->post('fri_close_status')));
            $sat_close_status = $this->db->escape_str(trim($this->input->post('sat_close_status')));
            $sun_close_status = $this->db->escape_str(trim($this->input->post('sun_close_status')));
            // restaurant close/off day---------------------END---------
            $update_array = ['time_mode' => 2, 'updated_at' => time() ];
            $update_status = $this->Common->updateData('restaurants', $update_array, "id = " . $selected_restaurant_id);
            if ($update_status > 0) {
                //check  if restaruant is exist in "rest_time_daywise" table
                $get_exist_open_close_day_time = $this->Common->getData("rest_time_daywise", "id", "rest_id = " . $selected_restaurant_id . "");
                if (!empty($get_exist_open_close_day_time)) {
                    // we need only update data
                    $update_time_array = [
                    //open close  time ----start
                    'mon_open_close_time' => $mon_open_close_time, 'tue_open_close_time' => $tue_open_close_time, 'wed_open_close_time' => $wed_open_close_time, 'thu_open_close_time' => $thu_open_close_time, 'fri_open_close_time' => $fri_open_close_time, 'sat_open_close_time' => $sat_open_close_time, 'sun_open_close_time' => $sun_open_close_time,
                    //open close  time ----end
                    //break time----start
                    'mon_break_status' => $mon_break_status, 'mon_break_start_end_time' => $mon_break_start_end_time, 'tue_break_status' => $tue_break_status, 'tue_break_start_end_time' => $tue_break_start_end_time, 'wed_break_status' => $wed_break_status, 'wed_break_start_end_time' => $wed_break_start_end_time, 'thu_break_status' => $thu_break_status, 'thu_break_start_end_time' => $thu_break_start_end_time, 'fri_break_status' => $fri_break_status, 'fri_break_start_end_time' => $fri_break_start_end_time, 'sat_break_status' => $sat_break_status, 'sat_break_start_end_time' => $sat_break_start_end_time, 'sun_break_status' => $sun_break_status, 'sun_break_start_end_time' => $sun_break_start_end_time,
                    //break time----end
                    //restaurant close/off day--------------start
                    'mon_close_status' => $mon_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'tue_close_status' => $tue_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'wed_close_status' => $wed_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'thu_close_status' => $thu_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'fri_close_status' => $fri_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'sat_close_status' => $sat_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'sun_close_status' => $sun_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    //restaurant close/off day--------------end
                    'updated_at' => time() ];
                    $update_time_status = $this->Common->updateData('rest_time_daywise', $update_time_array, " rest_id = " . $selected_restaurant_id);
                    if ($update_time_status > 0) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                } else {
                    //we need to insert new restaurant
                    // we need only update data
                    $insert_time_array = ['rest_id' => $selected_restaurant_id,
                    //open close  time ----start
                    'mon_open_close_time' => $mon_open_close_time, 'tue_open_close_time' => $tue_open_close_time, 'wed_open_close_time' => $wed_open_close_time, 'thu_open_close_time' => $thu_open_close_time, 'fri_open_close_time' => $fri_open_close_time, 'sat_open_close_time' => $sat_open_close_time, 'sun_open_close_time' => $sun_open_close_time,
                    //open close  time ----end
                    //break time----start
                    'mon_break_status' => $mon_break_status, 'mon_break_start_end_time' => $mon_break_start_end_time, 'tue_break_status' => $tue_break_status, 'tue_break_start_end_time' => $tue_break_start_end_time, 'wed_break_status' => $wed_break_status, 'wed_break_start_end_time' => $wed_break_start_end_time, 'thu_break_status' => $thu_break_status, 'thu_break_start_end_time' => $thu_break_start_end_time, 'fri_break_status' => $fri_break_status, 'fri_break_start_end_time' => $fri_break_start_end_time, 'sat_break_status' => $sat_break_status, 'sat_break_start_end_time' => $sat_break_start_end_time, 'sun_break_status' => $sun_break_status, 'sun_break_start_end_time' => $sun_break_start_end_time,
                    //break time----end
                    //restaurant close/off day--------------start
                    'mon_close_status' => $mon_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'tue_close_status' => $tue_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'wed_close_status' => $wed_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'thu_close_status' => $thu_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'fri_close_status' => $fri_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'sat_close_status' => $sat_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    'sun_close_status' => $sun_close_status, //	2- on this day restaurant will be closed, 0,1 - restaurant will be opend
                    //restaurant close/off day--------------end
                    'created_at' => time(), 'updated_at' => time() ];
                    // print_r($insert_time_array);
                    $insert_time_status = $this->Common->insertData('rest_time_daywise', $insert_time_array);
                    if ($insert_time_status > 0) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                }
            } else {
                echo 0;
            }
        } else if ($rest_time_mode == 1) { //Every day
            //1 - for every day, 2 - for Specific day (if value 1 then open close and break time will be insert this table , if value 2 then open-close and break time will be insert in rest_time_daywise table )
            //open-close time
            $open_time = $this->db->escape_str(trim($this->input->post('open_time')));
            $close_time = $this->db->escape_str(trim($this->input->post('close_time')));
            if (strpos($open_time, 'PM') !== false) { // am , pm goes from client side so thats why we need to check amd remove it
                $final_open_time = str_replace(" PM", "", $open_time);
            } else if (strpos($open_time, 'AM') !== false) {
                $final_open_time = str_replace(" AM", "", $open_time);
            } else {
                $final_open_time = $open_time;
            }
            if (strpos($close_time, 'PM') !== false) {
                $final_close_time = str_replace(" PM", "", $close_time);
            } else if (strpos($close_time, 'AM') !== false) {
                $final_close_time = str_replace(" AM", "", $close_time);
            } else {
                $final_close_time = $close_time;
            }
            //break time
            $break_start_time = $this->db->escape_str(trim($this->input->post('break_start_time')));
            $break_end_time = $this->db->escape_str(trim($this->input->post('break_end_time')));
            if (strpos($break_start_time, 'PM') !== false) { // am , pm goes from client side so thats why we need to check amd remove it
                $final_break_start_time = str_replace(" PM", "", $break_start_time);
            } else if (strpos($final_open_time, 'AM') !== false) {
                $final_break_start_time = str_replace(" AM", "", $break_start_time);
            } else {
                $final_break_start_time = $break_start_time;
            }
            if (strpos($break_end_time, 'PM') !== false) {
                $final_break_end_time = str_replace(" PM", "", $break_end_time);
            } else if (strpos($break_end_time, 'AM') !== false) {
                $final_break_end_time = str_replace(" AM", "", $break_end_time);
            } else {
                $final_break_end_time = $break_end_time;
            }
            $update_array = ['open_time' => $final_open_time, 'close_time' => $final_close_time, 'break_start_time' => $final_break_start_time, 'break_end_time' => $final_break_end_time, 'time_mode' => 1, 'updated_at' => time() ];
            $update_status = $this->Common->updateData('restaurants', $update_array, "id = " . $selected_restaurant_id);
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }
    ///Show Restaurant ---------Update OPEN CLOSE /BREAK TIME restaruant -------END-----
    //Show Restaurant ---------Update Delivery Charge per km if hanlde by restaurant-------START-----
    public function update_rest_delivery_per_km_charge() {
        $selected_restaurant_id = $this->db->escape_str(trim($this->input->post('selected_restaurant_id')));
        $delivery_per_km_charge = $this->db->escape_str(trim($this->input->post('delivery_per_km_charge'))); //If delivery handled by restaurant then this will contain per kilo meter charge value
        $get_delivery_per_km_charge = $this->Common->getData("restaurants", "per_km_charge", "id = " . $selected_restaurant_id . "");
        //check if per_km_charge is not exist then it will be update other wise no will be change
        if ($get_delivery_per_km_charge[0]['per_km_charge'] == $delivery_per_km_charge) {
            echo 2; //nothing changed
            
        } else {
            $update_array = ['per_km_charge' => $delivery_per_km_charge, //If delivery handled by restaurant then this will contain per kilo meter charge value
            'updated_at' => time() ];
            # update data in restaurant table
            $update_status = $this->Common->updateData('restaurants', $update_array, "id = " . $selected_restaurant_id);
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }
    //Show Restaurant ---------Update Delivery Charge per km if hanlde by restaurant -------END-----
    //Show Restaurant ---------Update basic Order  Preparation Time  and delivery Time  of restaruant -------START-----
    public function update_order_preparation_and_delivery_time() {
        $selected_restaurant_id = $this->db->escape_str(trim($this->input->post('selected_restaurant_id')));
        $rest_order_preparation_time = $this->db->escape_str(trim($this->input->post('rest_order_preparation_time')));
        $rest_order_delivery_time = $this->db->escape_str(trim($this->input->post('rest_order_delivery_time')));
        /*$get_exist_preparation_time =  $this->Common->getData("restaurants","preparation_time","id = ".$selected_restaurant_id."");
        
        //check if preparation_time is not exist then it will be update other wise no will be change
        if($get_exist_preparation_time[0]['preparation_time'] == $rest_order_preparation_time){
        echo 2;//nothing changed
        }else{*/
        $update_array = ['preparation_time' => $rest_order_preparation_time, //Ex 30 so 30 minutes
        'delivery_time' => $rest_order_delivery_time, //Ex 30 so 30 minutes
        'updated_at' => time() ];
        # update data in restaurant table
        $update_status = $this->Common->updateData('restaurants', $update_array, "id = " . $selected_restaurant_id);
        if ($update_status > 0) {
            echo 1;
        } else {
            echo 0;
        }
        //}
        
    }
    //Show Restaurant ---------Update basic Order  Preparation Time  and delivery Time of restaruant -------END-----
    # Common function for pagination start
    public function create_pagination($base_url, $total_records, $limit_per_page) {
        $config['base_url'] = $base_url;
        $config['total_rows'] = $total_records;
        $config['per_page'] = $limit_per_page;
        // $config["uri_segment"] = $this->uri->total_segments(); # Imp to make it as dynamic because we may have some functions with parameter and some with no parameter
        # CUSTOM START
        $config['num_links'] = 2;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        $config['full_tag_open'] = "<ul class='pagination'>";
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['first_link'] = '<i class="page-item fa fa-angle-double-left"></i>';
        $config['last_tag_open'] = '<li class="page-item lastt">';
        $config['last_tag_close'] = '</li>';
        $config['last_link'] = '<i class="page-item fa fa-angle-double-right"></i>';
        $config['prev_link'] = '<i class="page-item fa fa-angle-left"></i>';
        $config['prev_tag_open'] = '<li class="page-item previouss">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '<i class="page-item fa fa-angle-right"></i>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        # CUSTOM END
        $this->pagination->initialize($config);
        # build paging links
        
    }
    # Common function for pagination end
    # Resturant list Search function --------------------START-------------------
    public function restaurant_list($table_data = '0', $fromdate = 'all', $todate = 'all', $resturant_status = 'all', $search_key = 'all', $res_rating = 'all', $business_type = 'all', $food_type = 'all') {
        if ($this->id) {
            $pageData['fromdate'] = $fromdate;
            $pageData['todate'] = $todate;
            $pageData['resturant_status'] = $resturant_status; //1 - Enable, 2 - Disable, 3 - Deleted
            $pageData['resturant_rating'] = $res_rating;
            $pageData['business_type'] = $business_type;
            $pageData['food_type'] = $food_type;
            $search_key = urldecode($search_key);
            $search_key = trim($search_key);
            $pageData['search'] = $search_key;
            $query_part = "";
            if ($fromdate != "all" || $todate != "all" || $resturant_status != "all" || $search_key != "all" || $res_rating != "all" || $business_type != "all" || $food_type != "all") {
                if ($fromdate != "all" && $todate == "all") {
                    $query_part.= ' AND  `restaurants`.`created_at` >= "' . strtotime($fromdate) . '"';
                }
                if ($todate != "all" && $fromdate == "all") {
                    $query_part.= ' AND  `restaurants`.`created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
                }
                if ($fromdate != "all" && $todate != "all") {
                    $query_part.= ' AND  `restaurants`.`created_at` >= "' . $fromdate . ' 00:00:00" AND `restaurants`.`created_at` <= "' . $todate . ' 23:59:59"';
                }
                if ($resturant_status != "all") {
                    $query_part.= ' AND `restaurants`.`rest_status` = "' . $resturant_status . '"';
                }
                if ($res_rating != "all") {
                    $query_part.= ' AND `restaurants`.`avg_rating` = "' . $res_rating . '"';
                }
                if ($business_type != "all") {
                    $query_part.= ' AND `restaurants`.`business_type` = "' . $business_type . '"'; //	1 - food ,2 - grocery, 3 - alchohal
                    
                }
                if ($food_type != "all") {
                    $query_part.= ' AND `restaurants`.`food_type` = "' . $food_type . '"'; //	  //If restaurant's business type is food then this column will contain value. So value 0 (if not applicable) 1 (Restaurant) 2 (Kitchen)
                    
                }
                if ($search_key != "all") {
                    $query_part.= ' AND (`restaurants`.`rest_name` LIKE "%' . $search_key . '%" OR   `restaurants`.`rest_street_address` LIKE "%' . $search_key . '%" OR   `restaurants`.`rest_pin_address` LIKE "%' . $search_key . '%"  OR   `restaurants`.`rest_postal_code` LIKE "%' . $search_key . '%"  OR  `users`.`fullname` LIKE "%' . $search_key . '%" OR  `users`.`mobile` LIKE "%' . $search_key . '%" OR  `users`.`user_street_address` LIKE "%' . $search_key . '%" OR `users`.`email` LIKE "%' . $search_key . '%" OR  `users`.`user_postal_code` LIKE "%' . $search_key . '%" OR `users`.`email` LIKE "%' . $search_key . '%")';
                }
            }
            if ($this->role == 2) {
                $query_part.= ' AND `users`.`role` = ' . $this->role . '';
            }
            //pagination  ---start----
            $common_query = "SELECT `restaurants`.`id`,`restaurants`.`is_best_seller`,`restaurants`.`is_trending`, `restaurants`.`admin_id`,`restaurants`.`rest_name`,`restaurants`.`open_time`,`restaurants`.`close_time`,`restaurants`.`break_start_time`,`restaurants`.`break_end_time`,`restaurants`.`rest_street_address`,`restaurants`.`rest_pin_address`,`restaurants`.`rest_status`,`restaurants`.`avg_rating`,`users`.`email`,`users`.`mobile`,`users`.`role` FROM `restaurants` JOIN  `users` ON `restaurants`. `admin_id` = `users`.`id`  WHERE `users`.`status` != 3   " . $query_part . " AND `restaurants`.`rest_status` NOT IN(3) ORDER BY `restaurants`.`id` DESC";
            $page = ($this->uri->segment(11)) ? ($this->uri->segment(11) - 1) : 0;
            if ($page > 0) {
                $page_offset = $page * ADMIN_PER_PAGE_RECORDS;
            } else {
                $page_offset = $page;
            }
            $query = "" . $common_query . " LIMIT " . ADMIN_PER_PAGE_RECORDS . " OFFSET " . $page_offset . " ";
            $restaurant_list_data = $this->Common->custom_query($query, 'get');
            if (!empty($restaurant_list_data)) {
                foreach ($restaurant_list_data as $key => $value) {
                    $offline_status_data = $this->Common->getData('rest_offline', 'rest_offline.offline_tag,rest_offline.offline_value,rest_offline.offline_from,rest_offline.offline_to', 'rest_offline.rest_id = "' . $value['id'] . '"', '', '');
                    //we need to check current time (between from and to date)for getting offline status becouse may be possible from and to date is complete but restaurant didnot changed status (untill online , offline entery will be available) that's why
                    date_default_timezone_set('Asia/Singapore');
                    if (!empty($offline_status_data)) {
                        if ((time() >= $offline_status_data[0]['offline_from']) && (time() <= $offline_status_data[0]['offline_to'])) {
                            $offline_status = "No"; //Offline
                            
                        } else {
                            $offline_status = "Yes"; //Online
                            
                        }
                        $restaurant_list_data[$key]['offline_status'] = $offline_status;
                    } else {
                        // we need to check also open and close time with break time during this time is receving order should be no
                        //echo 'open'.$value['open_time'].'=== close'.$value['close_time'];
                        $from = new DateTimeZone('Asia/Singapore');
                        $currDate = new DateTime('now', $from);
                        $current_time = $currDate->format('H:i');
                        //echo $current_time.'>='.$value['open_time'].'===='.$current_time.'<='.$value['close_time'].'<br>';
                        if (($current_time >= $value['open_time']) && ($current_time <= $value['close_time'])) {
                            //check break time
                            $current_time . '>=' . $value['break_start_time'] . '====' . $current_time . '<=' . $value['break_end_time'];
                            if (($current_time >= $value['break_start_time'])) { //&& ($current_time <= $value['break_end_time'])
                                if ($current_time <= $value['break_end_time']) {
                                    $restaurant_list_data[$key]['offline_status'] = "No"; //Offline
                                    
                                } else {
                                    $restaurant_list_data[$key]['offline_status'] = "Yes"; //Online
                                    
                                }
                            } else {
                                $restaurant_list_data[$key]['offline_status'] = "Yes"; //Online
                                
                            }
                        } else {
                            $restaurant_list_data[$key]['offline_status'] = "No"; //Offline
                            
                        }
                    }
                }
            }
            $pageData['restaurant_list'] = $restaurant_list_data;
            $query = "" . $common_query . "";
            $total_records = count($this->Common->custom_query($query, "get"));
            $url = base_url('admin/restaurant_list/' . $table_data . '/' . $fromdate . '/' . $todate . '/' . $resturant_status . '/' . $search_key . '/' . $res_rating . '/' . $business_type . '/' . $food_type);
            # Pass parameter to common pagination and create pagination function start
            $this->create_pagination($url, $total_records, ADMIN_PER_PAGE_RECORDS);
            $pageData['links'] = $this->pagination->create_links();
            $pageData['start'] = ($page * ADMIN_PER_PAGE_RECORDS) + 1;
            //pagination  ---End----
            $data = array('title' => "Restaurant List", 'pageName' => "restaurant-list");
            //geting business type for search
            $pageData['merchant_category'] = $this->Common->getData('merchant_categories', 'id as merchant_category_id,category_name', " status = 1 AND status != 5");
            $pageTitle = 'Restaurant List';
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = $pageTitle;
            $pageData['pageName'] = 'restaurant-list';
            if ($table_data == "1" || $table_data == "2") {
                // if any action tiriger like, delete or enable disable then is url excute by ajax
                $this->load->view('restaurant_list_table', $pageData);
            } else {
                $this->load->view('masterpage', $pageData);
            }
        } else {
            $this->load->view('login');
        }
    }
    # Resturant list Search function --------------------END-------------------
    // Exoport Restaurant csv format file -------------START-----------
    public function exportRestaurantCSV($fromdate = 'all', $todate = 'all', $resturant_status = 'all', $search_key = 'all', $res_rating = 'all', $business_type = 'all', $food_type = 'all', $restaurant_food_type_is_veg = 'all') {
        $query_part = "";
        if ($fromdate != "all" || $todate != "all" || $resturant_status != "all" || $search_key != "all" || $res_rating != "all" || $business_type != "all" || $food_type != "all" || $restaurant_food_type_is_veg != "all") {
            if ($fromdate != "all" && $todate == "all") {
                $query_part.= ' AND  `restaurants`.`created_at` >= "' . strtotime($fromdate) . '"';
            }
            if ($todate != "all" && $fromdate == "all") {
                $query_part.= ' AND `restaurants`.`created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
            }
            if ($fromdate != "all" && $todate != "all") {
                $query_part.= ' AND  `restaurants`.`created_at` >= "' . $fromdate . ' 00:00:00" AND `restaurants`.`created_at` <= "' . $todate . ' 23:59:59"';
            }
            if ($resturant_status != "all") {
                $query_part.= ' AND `restaurants`.`rest_status` = "' . $resturant_status . '"';
            }
            if ($res_rating != "all") {
                $query_part.= ' AND `restaurants`.`avg_rating` = "' . $res_rating . '"';
            }
            if ($business_type != "all") {
                $query_part.= ' AND `restaurants`.`business_type` = "' . $business_type . '"'; //	1 - Veg 2 - Non veg
                
            }
            if ($food_type != "all") {
                $query_part.= ' AND `restaurants`.`food_type` = "' . $food_type . '"'; //	1 - Veg 2 - Non veg
                
            }
            if ($restaurant_food_type_is_veg != "all") {
                $query_part.= ' AND `restaurants`.`is_veg` = "' . $restaurant_food_type_is_veg . '"'; //	1 - Veg 2 - Non veg
                
            }
            if ($search_key != "all") {
                $query_part.= ' AND `restaurants`.`rest_name` LIKE "%' . $search_key . '%" OR   `restaurants`.`rest_street_address` LIKE "%' . $search_key . '%" OR   `restaurants`.`rest_pin_address` LIKE "%' . $search_key . '%"  OR   `restaurants`.`rest_postal_code` LIKE "%' . $search_key . '%"  OR  `users`.`fullname` LIKE "%' . $search_key . '%" OR  `users`.`mobile` LIKE "%' . $search_key . '%" OR  `users`.`user_street_address` LIKE "%' . $search_key . '%" OR `users`.`email` LIKE "%' . $search_key . '%" OR  `users`.`user_postal_code` LIKE "%' . $search_key . '%" OR `users`.`email` LIKE "%' . $search_key . '%"';
            }
        } else {
            $query_part = "";
        }
        // file name
        $filename = 'restaurant_list_' . date('Ymd') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv;");
        $common_query = "SELECT `restaurants`.`id`, `restaurants`.`admin_id`,`restaurants`.`rest_name`,`restaurants`.`rest_street_address`,`restaurants`.`rest_pin_address`,`restaurants`.`rest_postal_code`,`restaurants`.`rest_status`,`restaurants`.`business_type`, `restaurants`.`food_type`,`restaurants`.`is_veg`,`restaurants`.`avg_rating`,`restaurants`.`res_description`,`restaurants`.`created_at`,`users`.`fullname`,`users`.`email`,`users`.`mobile`,`users`.`user_street_address` FROM `restaurants` JOIN  `users` ON `restaurants`. `admin_id` = `users`.`id`  WHERE `users`.`status` != 3  " . $query_part . " AND `restaurants`.`rest_status` NOT IN(3) ORDER BY `restaurants`.`id` DESC";
        $query = "" . $common_query . "";
        $restaurantData = $this->Common->custom_query($query, "get");
        // file creation
        $file = fopen('php://output', 'w');
        $header = array("Restaurant Name", "Name", "Contact Number", "Email", "Merchant Street Address", "Restaurant Street Address", "Restaurant Pin Address", "Restaurant Postal Code", "Rating", "Status", "Business Type", "Food Type", "Food Type is Veg or Non Veg", "Description", "Stripe Account Status", "Registered Date");
        fputcsv($file, $header);
        if (count($restaurantData) > 0) {
            foreach ($restaurantData as $key => $line) {
                if ($line['rest_status'] == "1") {
                    $status = 'Active';
                } else if ($line['rest_status'] == "2") {
                    $status = 'Inactive';
                } else {
                    $status = "NA";
                }
                if ($line['created_at'] != '') {
                    $createdAt = date('d/m/Y', $line['created_at']);
                } else {
                    $createdAt = 'NA';
                }
                if ($line['food_type'] == 0) {
                    $food_type = 'Food';
                }
                if ($line['food_type'] == 1) {
                    $food_type = 'Restaurant';
                }
                if ($line['food_type'] == 2) {
                    $food_type = 'Kitchen';
                }
                if ($line['is_veg'] == 1) {
                    $is_veg = 'Veg';
                }
                if ($line['is_veg'] == 2) {
                    $is_veg = 'Non Veg';
                }
                if ($line['business_type'] == 1) {
                    $merchant_category = 'Food';
                }
                if ($line['business_type'] == 2) {
                    $merchant_category = 'Grocery';
                }
                if ($line['business_type'] == 3) {
                    $merchant_category = 'Alcohol';
                }
                $data_array = array($line['rest_name'], $line['fullname'], $line['mobile'], $line['email'], $line['user_street_address'], $line['rest_street_address'], $line['rest_pin_address'], $line['rest_postal_code'], $line['avg_rating'], $status, $merchant_category, $food_type, $is_veg, $line['res_description'], "", $createdAt);
                fputcsv($file, $data_array);
            }
        }
        fclose($file);
        exit;
    }
    // Exoport Restaurant csv file -------------END--------------
    //Promo Code show and search filter -----------------START-------
    public function promo_codes($table = '', $fromdate = 'all', $todate = 'all', $promo_code_status = 'all', $search_key = 'all', $promo_code_type = 'all', $promo_code_mode = 'all') {
        if ($this->id != '') {
            $pageData['fromdate'] = $fromdate;
            $pageData['todate'] = $todate;
            $pageData['promo_code_status'] = $promo_code_status; //1 - Enable, 2 - Disable, 3 - Deleted
            $pageData['promo_code_type'] = $promo_code_type; //0 - Flat, 1 - Percent
            $pageData['promo_code_mode'] = $promo_code_mode; //1 - Auto apply 2 - Not auto apply
            $search_key = urldecode($search_key);
            $search_key = trim($search_key);
            $pageData['search'] = $search_key;
            $query_part = "";
            $fromDateNew = strtotime($fromdate . ' 00:00:00');
            $toDateNew = strtotime($todate . ' 24:00:00');
            $table_data = $this->uri->segment(3);
            if ($table != "" || $fromdate != "all" || $todate != "all" || $promo_code_status != "all" || $search_key != "all" || $promo_code_type != "all" || $promo_code_mode != "all") {
                if ($fromdate != "all" && $todate == "all") {
                    $query_part.= ' AND `promotions`.`created_at` >= "' . strtotime($fromdate) . '"';
                }
                if ($todate != "all" && $fromdate == "all") {
                    $query_part.= ' AND `promotions`.`created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
                }
                if ($fromdate != "all" && $todate != "all") {
                    $query_part.= ' AND (promotions.created_at between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                }
                if ($promo_code_status != "all") {
                    $query_part.= ' AND `promotions`.`promo_status` = "' . $promo_code_status . '"';
                }
                if ($promo_code_type != "all") {;
                    $query_part.= ' AND `promotions`.`promo_type` = ' . $promo_code_type . '';
                }
                if ($promo_code_mode != "all") {
                    $query_part.= ' AND `promotions`.`is_auto_apply` = ' . $promo_code_mode . '';
                }
                if ($search_key != "all") {
                    //For Promo Code  0 = Flat, 1 = Percent
                    if ($search_key == 'Flat') {
                        $search_key_type = 0;
                    } else if ($search_key == 'Percent') {
                        $search_key_type = 1;
                    } else {
                        $search_key_type = $search_key;
                    }
                    $query_part.= ' AND  `promotions`.`promo_type` LIKE "%' . $search_key_type . '%" OR  `promotions`.`code_name` LIKE "%' . $search_key . '%" OR  `promotions`.`discount_value` LIKE "%' . $search_key . '%" OR  `promotions`.`desciption` LIKE "%' . $search_key . '%"  OR `promotions`.`min_value` LIKE "%' . $search_key . '%"  OR  `promotions`.`max_discount` LIKE "%' . $search_key . '%" OR  `promotions`.`promo_used_times` LIKE "%' . $search_key . '%" OR  `promotion_level`.`type` LIKE "%' . $search_key . '%"';
                }
            }
            //  if merchant is logged in then this condition will check and only merchant restaurant prodcuts will show if this blank that means super admin is logged in
            if ($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2) {
                $restaurant_id = $this->logged_in_restaurant_id;
                $query_part.= ' AND `promotions`.`restaurant_id` = ' . $restaurant_id . '  '; //level_id 1 -Delivery Charge, 2 - RESTAURANT // AND (`promotions`. `level_id`= 2 OR `promotions`. `level_id`= 1)
                
            }
            //pagination  ---start----
            $common_query = "SELECT `promotions`.`id`, `promotions`.`promo_type`,`promotions`.`code_name`,`promotions`.`discount_value`,`promotions`.`promo_status`,`promotions`.`desciption`,`promotions`.`min_value`,`promotions`.`max_discount`,`promotions`. `level_id`,`promotions`. `is_auto_apply`,`promotions`. `promo_used_times`,`promotions`.`valid_from`, `promo_used_times`,`promotions`.`valid_till`,`promotions`.`added_by`,`promotion_level`.`type` FROM `promotions` JOIN  `promotion_level` ON `promotions`. `level_id` = `promotion_level`.`id` WHERE `promotion_level`.`status` = 1  AND `promotions`.`promo_status` NOT IN(3) AND  `promotions`.`promotion_mode_status` = 1 " . $query_part . " GROUP BY `promotions`.`code_name` ORDER BY `promotions`.`id` DESC";
            $page = ($this->uri->segment(10)) ? ($this->uri->segment(10) - 1) : 0;
            if ($page > 0) {
                $page_offset = $page * ADMIN_PER_PAGE_RECORDS;
            } else {
                $page_offset = $page;
            }
            $query = "" . $common_query . " LIMIT " . ADMIN_PER_PAGE_RECORDS . " OFFSET " . $page_offset . " ";
            if (!$this->logged_in_restaurant_id && $this->role == 2) { // if mercahnt is logged in but restauant not regristerd
                $pageData['promo_code_list'] = "";
            } else {
                $pageData['promo_code_list'] = $this->Common->custom_query($query, 'get');
            }
            $query = "" . $common_query . "";
            $total_records = count($this->Common->custom_query($query, "get"));
            $url = base_url('admin/promo_codes/0/' . $fromdate . '/' . $todate . '/' . $promo_code_status . '/' . $search_key . '/' . $promo_code_type . '/' . $promo_code_mode . '/');
            # Pass parameter to common pagination and create pagination function start
            $this->create_pagination($url, $total_records, ADMIN_PER_PAGE_RECORDS);
            $pageData['links'] = $this->pagination->create_links();
            $pageData['start'] = ($page * ADMIN_PER_PAGE_RECORDS) + 1;
            //pagination  ---End----
            $data = array('title' => "Promo Codes", 'pageName' => "promo-codes");
            $pageTitle = 'Promo Codes';
            $this->createBreadcrumb($data);
            $pageData['urlPart'] = $this->getUrlPart();
            $pageData['pageTitle'] = 'Promo Codes';
            $pageData['pageName'] = 'promo-codes';
            if ($table_data == "1" || $table_data == "2") {
                // if any action tiriger like, delete or enable disable then is url excute by ajax
                $this->load->view('promo_code_list_table', $pageData);
            } else {
                $this->load->view('masterpage', $pageData);
            }
        } else {
            $this->load->view('login');
        }
    }
    //Promo Code show and search filter -----------------END-------
    //Promo Code CSV file export -----------------------START----------
    public function exportPromotionCSV($table = '', $fromdate = 'all', $todate = 'all', $promo_code_status = 'all', $search_key = 'all', $promo_code_type = 'all', $promo_code_mode = 'all') {
        $query_part = "";
        if ($table != "" || $fromdate != "all" || $todate != "all" || $promo_code_status != "all" || $search_key != "all" || $promo_code_type != "all" || $promo_code_mode != "all") {
            if ($fromdate != "all" && $todate == "all") {
                $query_part.= ' AND `promotions`.`created_at` >= "' . strtotime($fromdate) . '"';
            }
            if ($todate != "all" && $fromdate == "all") {
                $query_part.= ' AND `promotions`.`created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
            }
            if ($fromdate != "all" && $todate != "all") {
                $query_part.= ' AND (promotions.created_at between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
            }
            if ($promo_code_status != "all") {
                $query_part.= ' AND `promotions`.`promo_status` = "' . $promo_code_status . '"';
            }
            if ($promo_code_type != "all") {;
                $query_part.= ' AND `promotions`.`promo_type` = ' . $promo_code_type . '';
            }
            if ($promo_code_mode != "all") {
                $query_part.= ' AND `promotions`.`is_auto_apply` = ' . $promo_code_mode . '';
            }
            if ($search_key != "all") {
                //For Promo Code  0 = Flat, 1 = Percent
                if ($search_key == 'Flat') {
                    $search_key_type = 0;
                } else if ($search_key == 'Percent') {
                    $search_key_type = 1;
                } else {
                    $search_key_type = $search_key;
                }
                $query_part.= ' AND  `promotions`.`promo_type` LIKE "%' . $search_key_type . '%" OR  `promotions`.`code_name` LIKE "%' . $search_key . '%" OR  `promotions`.`discount_value` LIKE "%' . $search_key . '%" OR  `promotions`.`desciption` LIKE "%' . $search_key . '%"  OR `promotions`.`min_value` LIKE "%' . $search_key . '%"  OR  `promotions`.`max_discount` LIKE "%' . $search_key . '%" OR  `promotions`.`promo_used_times` LIKE "%' . $search_key . '%" OR  `promotion_level`.`type` LIKE "%' . $search_key . '%"';
            }
        }
        // file name
        $filename = 'promotion_list_' . date('Ymd') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv;");
        //  if merchant is logged in then this condition will check and only merchant restaurant prodcuts will show if this blank that means super admin is logged in
        if ($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2) {
            $restaurant_id = $this->logged_in_restaurant_id;
            $query_part.= 'AND `promotions`.`restaurant_id` = ' . $restaurant_id . ' AND `promotions`. `level_id` IN(3,4)';
        }
        $common_query = "SELECT `promotions`.`id`, `promotions`.`promo_type`,`promotions`.`code_name`,`promotions`.`discount_value`,`promotions`.`promo_status`,`promotions`.`desciption`,`promotions`.`min_value`,`promotions`.`max_discount`,`promotions`. `level_id`,`promotions`. `is_auto_apply`,`promotions`. `promo_used_times`,`promotions`.`valid_from`, `promo_used_times`,`promotions`.`valid_till`,`promotions`.`added_by`,`promotions`.`restaurant_id`,`promotions`.`created_at`,`promotion_level`.`type` FROM `promotions` JOIN  `promotion_level` ON `promotions`. `level_id` = `promotion_level`.`id` WHERE `promotion_level`.`status` = 1 " . $query_part . " AND `promotions`.`promo_status` NOT IN(3) GROUP BY `promotions`.`code_name` ORDER BY `promotions`.`id` DESC";
        $query = "" . $common_query . "";
        $PromotionData = $this->Common->custom_query($query, "get");
        // file creation
        $file = fopen('php://output', 'w');
        $header = array("Promo Code", "Type", "Value", "Maximum Discount", "Minimum Order Amount", "Start Date", "End Date", "Description", "Promo Application Mode", "Promo Applied On", "Promo Used (No. Of Times)", "First Time Added By", "Status", "Registered Date");
        fputcsv($file, $header);
        if (count($PromotionData) > 0) {
            foreach ($PromotionData as $key => $line) {
                //for status---------start-----
                if ($line['promo_status'] == "1") {
                    $status = 'Active';
                } else if ($line['promo_status'] == "2") {
                    $status = 'Inactive';
                } else {
                    $status = "NA";
                }
                //for type---------start-----
                if ($line['promo_type'] == "0") {
                    $promo_type = 'Flat';
                } else if ($line['promo_type'] == "1") {
                    $promo_type = 'Percent';
                } else {
                    $promo_type = "NA";
                }
                //for mode---------start-----//Is this promotion is auto apply or not. 1 - Auto apply 2 - Not auto apply
                if ($line['is_auto_apply'] == "1") {
                    $promo_mode = 'Auto apply';
                } else if ($line['is_auto_apply'] == "2") {
                    $promo_mode = 'Not auto apply';
                } else {
                    $promo_mode = "NA";
                }
                //for status---------registerd date-----
                if ($line['created_at'] != '') {
                    $createdAt = date('d/m/Y', $line['created_at']);
                } else {
                    $createdAt = 'NA';
                }
                $promo_code_valid_from = $line['valid_from'];
                $promo_code_valid_till = $line['valid_till'];
                //Epcho time convert --------
                #Promo Code valid Form---------START-------
                $valid_from = new DateTime("@$promo_code_valid_from"); // convert UNIX timestamp to PHP DateTime
                $code_valid_from = $valid_from->format('Y-m-d H:i:s A');
                #Promo Code valid Form---------END-------
                #Promo Code valid Till---------START-------
                $till_date = new DateTime("@$promo_code_valid_till"); // convert UNIX timestamp to PHP DateTime
                $code_till_date = $till_date->format('Y-m-d H:i:s A');
                #Promo Code valid Till---------END-------
                // checking who added firt time
                $added_by = "";
                if ($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2) {
                    if ($line['added_by'] == 2) {
                        $added_by = 'You';
                    }
                    if ($promo_code_added_by == 1) {
                        $added_by = 'Master Admin';
                    }
                } else if ($line['added_by'] == 1) {
                    $added_by = 'You';
                } else if ($line['added_by'] == 2) {
                    $added_by = 'Merchant';
                }
                $data_array = array($line['code_name'], $promo_type, $line['discount_value'], $line['max_discount'], $line['min_value'], $code_valid_from, $code_till_date, $line['desciption'], $promo_mode, $line['type'], $line['promo_used_times'], $added_by, $status, $createdAt);
                //print_r($data_array);
                fputcsv($file, $data_array);
            }
        }
        fclose($file);
        exit;
    }
    //Promo Code CSV file export -----------------------END----------
    //Enable /Disable(active/inactive) toggle of Restaurant------ START------
    public function active_inactive_promo_code() {
        $promo_code_name = $this->db->escape_str(trim($this->input->post('promo_code_name')));
        $enable_disable_status = $this->db->escape_str(trim($this->input->post('enable_disable_status')));
        $update_array = ['promo_status' => $enable_disable_status, //1 - Enable, 2 - Disable, 3 - Deleted
        'updated_at' => time() ];
        # update data in promo code_ table
        $update_status = $this->Common->updateData('promotions', $update_array, 'code_name = "' . $promo_code_name . '"');
        if ($update_status > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
    //Enable /Disable(active/inactive) toggle of Restaurant------ END------
    // Promo Code Delete -------------------- START----------
    public function delete_Promo_Code() {
        $promo_code_name = $this->db->escape_str(trim($this->input->post('promo_code_name')));
        $update_array = ['promo_status' => 3, // 1 - Enable, 2 - Disable, 3 - Deleted
        'updated_at' => time() ];
        # update data in promotion table
        $update_status = $this->Common->updateData('promotions', $update_array, 'code_name = "' . $promo_code_name . '"');
        if ($update_status > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
    //Promo Code Delete ------ END------
    //Select exist restauant/products/category ( from promotion table ) according to selected promo code name in edit mode ------START-----
    public function show_restaurant_product_or_category_according_promo_code_name() {
        $promotion_code_name = $this->db->escape_str(trim($this->input->post('promotion_code_name')));
        $checked_restaurant_id = $this->db->escape_str(trim($this->input->post('checked_restaurant_id')));
        $level_id = $this->db->escape_str(trim($this->input->post('level_id')));
        $query_part = "";
        if ($checked_restaurant_id != "") {
            $query_part.= 'AND `restaurant_id` = "' . $checked_restaurant_id . '" ';
        }
        $query = 'SELECT `restaurant_id`,`applied_on_id`,`if_promo_for_all_rest` FROM `promotions` WHERE `promotions`.`code_name` = "' . $promotion_code_name . '" ' . $query_part . ' AND `level_id` = "' . $level_id . '" AND `promotions`.`promo_status` != 3';
        $exist_applied_on_data = $this->Common->custom_query($query, 'get');
        if (count($exist_applied_on_data) > 0) {
            echo json_encode($exist_applied_on_data);
        } else {
            echo 0;
        }
    }
    //Select exist restauant/products/category ( from promotion table ) according to selected promo code name in edit mode ------END-----
    //Add Edit Promotion/Promocode Function  VIEW-----START--------
    public function add_edit_promotion($type, $promotion_code_name = '') {
        $promotion_code_name = trim($promotion_code_name);
        if ($this->id) {
            $pageData['promotion_level_list'] = $this->Common->getData('promotion_level', 'id as level_id, type', 'status = 1 AND id NOT IN (5,6,7,9)');
            $query_part = "";
            // if merchant is logged in
            if ($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2) {
                $query_part.= 'id = ' . $this->logged_in_restaurant_id . ' AND';
            }
            $pageData['restaurant_list'] = $this->Common->getData('restaurants', 'id,admin_id,rest_name', '' . $query_part . ' rest_status != 3');
            if ($type == 1) {
                $data = array('title' => "Add Promotion ", 'pageName' => "add-edit-promotion",);
                $pageTitle = 'Add Promotion';
                $this->createBreadcrumb($data);
                $pageData['urlPart'] = $this->getUrlPart();
                $pageData['pageTitle'] = $pageTitle;
                $pageData['pageName'] = 'add-edit-promotion';
                $pageData['mode_type'] = 1;
                $this->load->view('masterpage', $pageData);
            } else if ($type == 2 && $promotion_code_name !== "") {
                $query = 'SELECT * FROM `promotions` WHERE `code_name` = "' . $promotion_code_name . '" AND `promo_status` != 3';
                $edit_promotion_code_name = $this->Common->custom_query($query, 'get');
                if (!empty($edit_promotion_code_name)) {
                    $data = array('title' => "Edit Promotion ", 'pageName' => "add-edit-promotion",);
                    $pageTitle = 'Edit Promotion';
                    $this->createBreadcrumb($data);
                    $pageData['promotion_data_by_code_name'] = $edit_promotion_code_name;
                    $pageData['urlPart'] = $this->getUrlPart();
                    $pageData['pageTitle'] = $pageTitle;
                    $pageData['pageName'] = 'add-edit-promotion';
                    $pageData['mode_type'] = 2;
                    $pageData['edit_by_promotion_code_name'] = $promotion_code_name;
                    $this->load->view('masterpage', $pageData);
                } else {
                    redirect(base_url('admin/errors_404')); // given promotion id is not exist in database table
                    
                }
            } else {
                redirect(base_url('admin/errors_404'));
            }
        } else {
            $this->load->view('login');
        }
    }
    //Add Edit Promotion/Promocode  Function VIEW-----END--------
    //Select Products according to selected restaurant id ------START-----
    public function show_products_according_selected_restaurant() {
        $restaurant_id = $this->db->escape_str(trim($this->input->post('restaurant_id')));
        $product_data = $this->Common->getData('products', 'id as product_id,product_name', 'restaurant_id = ' . $restaurant_id . '  AND product_status != 3');
        if (count($product_data) > 0) {
            echo json_encode($product_data);
        } else {
            echo 0;
        }
    }
    //Select Products according to selected restaurant id ------END-----
    //Select cateogry_ according to selected restaurant id its comman for at the time of order add item------START-----
    public function show_categories_according_selected_restaurant() {
        $restaurant_id = $this->db->escape_str(trim($this->input->post('restaurant_id')));
        $cateogry_data = $this->Common->getData('categories', 'id as category_id,category_name', 'restaurant_id = ' . $restaurant_id . '  AND 	category_status != 3');
        if (count($cateogry_data) > 0) {
            echo json_encode($cateogry_data);
        } else {
            echo 0;
        }
    }
    //Select cateogry according to selected restaurant id ------END-----
    public function comman_function_for_applied_on_1_or_2_8($applied_on_value = '', $promotion_table_exist_applied_on_data = '', $selected_restaurant_id = '', $edit_promotion_code_name = '', $data = '', $check_selected_all_restaurant = '') {
        #get which is exist  ot not exist in promotions table exist but not posted now and will remove or delete
        $update_status = "";
        foreach ($promotion_table_exist_applied_on_data as $exist_applied_on_data_value) {
            if (in_array($exist_applied_on_data_value, $selected_restaurant_id)) {
                //exist ----
                # if restaurant id is exist in  promotions table, then only data will update according by promo code name
                //If level id (applied_on_value )is 1 then 0 if level id(applied_on_value) is 2 then restaurant id , if  level id is 8 then 1
                if ($applied_on_value == 1) {
                    $applied_on_id = 0;
                } else if ($applied_on_value == 2) {
                    $applied_on_id = $exist_applied_on_data_value;
                } else if ($applied_on_value == 8) {
                    $applied_on_id = 1;
                }
                $data['applied_on_id'] = $applied_on_id; //push  data
                $update_status = $this->Common->updateData('promotions', $data, 'code_name = "' . $edit_promotion_code_name . '" AND restaurant_id = ' . $exist_applied_on_data_value . '');
            } else {
                //not exist --------
                # if exist resaturant id which is exist in promotions table but , not posted now then it exist resaturant id will delete by exist restaurant id from promotion table by promo code name
                $not_exist_rest_id = $exist_applied_on_data_value;
                $delete_query = 'DELETE FROM `promotions` WHERE `restaurant_id` = "' . $not_exist_rest_id . '" AND `code_name` =  "' . $edit_promotion_code_name . '"';
                $this->Common->custom_query($delete_query);
            }
        }
        # get which is new posted restaurant id which does not exist in  promotions table---
        $restaurant_id_array_which_not_exist = array_diff($selected_restaurant_id, $promotion_table_exist_applied_on_data);
        if (!empty($restaurant_id_array_which_not_exist)) {
            if ($check_selected_all_restaurant == 0) { //promo for some restaurant
                foreach ($restaurant_id_array_which_not_exist as $restaurant_id_value) {
                    // new data-----
                    # if restaurant id is not exist but new data posted ,  then data will insert
                    //If level id (applied_on_value )is 1 then 0 if level id(applied_on_value) is 2 then restaurant id , if  level id is 8 then 1
                    if ($applied_on_value == 1) {
                        $applied_on_id = 0;
                    } else if ($applied_on_value == 2) {
                        $applied_on_id = $restaurant_id_value;
                    } else if ($applied_on_value == 8) {
                        $applied_on_id = 1;
                    }
                    $data['restaurant_id'] = $restaurant_id_value; //push restaurant_id  data
                    $data['applied_on_id'] = $applied_on_id; //push  data
                    // EX.  If super admin add level 3 or 4 and in future merchant change it 3 or 4 or new product or category that time added_by value again insert logged role wise
                    $data['added_by'] = $this->role; //1 : added by master admin 2 : By merchant
                    $insert_status = $this->Common->insertData('promotions', $data);
                }
            } else if ($check_selected_all_restaurant == 1) { // all rest
                $delete_query = 'DELETE FROM `promotions` WHERE `code_name` =  "' . $edit_promotion_code_name . '"';
                $this->Common->custom_query($delete_query);
                $data['if_promo_for_all_rest'] = 1; //	If promo code is for all restaurant then 1 value will be go , and we dont need to multi entry for all restaruant
                $insert_status = $this->Common->insertData('promotions', $data);
            }
        }
        if ($update_status > 0 || $insert_status > 0) {
            echo 1; // success
            
        } else {
            echo 0; // somthing is wrong
            
        }
    }
    //Select cateogry according to selected product id ------END-----
    public function comman_function_for_applied_on_3($applied_on_value = '', $promotion_table_exist_product_array = '', $promotion_table_exist_restaurant_array = '', $selected_product_id = '', $edit_promotion_code_name = '', $data = '', $restaurant_id = '') {
        #get which is exist  ot not exist in promotions table exist but not posted now and will remove or delete
        foreach ($promotion_table_exist_product_array as $exist_applied_on_data_value) {
            if (in_array($exist_applied_on_data_value, $selected_product_id)) {
                //exist ----
                # if product id is exist in (applid on) promotions table, then only data will update according by promo code name
                //If level id is 1 then 0 if level id is 2 then restaurant id if level id is 3 then product id if level id is 4 then category id,If level id is 8 then 1
                $applied_on_id = $exist_applied_on_data_value;
                $data['restaurant_id'] = $restaurant_id; //push restaurant_id  data
                $data['applied_on_id'] = $applied_on_id; //push product id
                $update_status = $this->Common->updateData('promotions', $data, 'code_name = "' . $edit_promotion_code_name . '" AND applied_on_id = ' . $exist_applied_on_data_value . ' AND restaurant_id = "' . $restaurant_id . '"');
            } else {
                //not exist --------
                # if exist resaturant id which is exist in promotions table but , not posted now then it exist resaturant id will delete by exist product id from promotion table by promo code name
                $not_exist_product_id = $exist_applied_on_data_value;
                $delete_query = 'DELETE FROM `promotions` WHERE  `level_id` = "3" AND  `applied_on_id` = "' . $not_exist_product_id . '" AND `restaurant_id` = "' . $restaurant_id . '" AND `code_name` =  "' . $edit_promotion_code_name . '"';
                $this->Common->custom_query($delete_query);
            }
        }
        # get which is new posted product id which does not exist in  promotions table---
        $product_id_array_which_not_exist = array_diff($selected_product_id, $promotion_table_exist_product_array);
        if (!empty($product_id_array_which_not_exist)) {
            foreach ($product_id_array_which_not_exist as $product_id_value) {
                // new data-----
                # if product id is not exist but new data posted ,  then data will insert
                //If level id (applied_on_value )is 1 then 0 if level id(applied_on_value) is 2 then product id
                //
                // check if new restaurant add or add new product in exists restaurant id
                //check is new product added with new restaurant. if given restaurant id is exists in array that means old restaurant and only new product will insert with existing restaurant id , if given restaurant id is not exists in created array that means new product added with new restaurant id than old product data delete with restaurant id according to promo code name and new data insert
                if (!in_array($restaurant_id, $promotion_table_exist_restaurant_array)) {
                    //new restaurant_id and delete with old restaurant id
                    foreach ($promotion_table_exist_restaurant_array as $exists_restaurant_id) {
                        $delete_query = 'DELETE FROM `promotions` WHERE `level_id` = "3" AND `restaurant_id` = "' . $exists_restaurant_id . '" AND `code_name` =  "' . $edit_promotion_code_name . '"';
                        $this->Common->custom_query($delete_query);
                    }
                }
                $applied_on_id = $product_id_value;
                $data['restaurant_id'] = $restaurant_id; //push restaurant_id  data
                $data['applied_on_id'] = $applied_on_id; //push product id
                // EX.  If super admin add level 3 or 4 and in future merchant change it 3 or 4 or new product or category that time added_by value again insert logged role wise
                $data['added_by'] = $this->role; //1 : added by master admin 2 : By merchant
                $insert_status = $this->Common->insertData('promotions', $data);
            }
        }
        if ($update_status > 0 || $insert_status > 0) {
            echo 1; // success
            
        } else {
            echo 0; // somthing is wrong
            
        }
    }
    //Select cateogry according to selected category id ------END-----
    public function comman_function_for_applied_on_4($applied_on_value = '', $promotion_table_exist_category_array = '', $promotion_table_exist_restaurant_array = '', $selected_category_id = '', $edit_promotion_code_name = '', $data = '', $restaurant_id = '') {
        #get which is exist  ot not exist in promotions table exist but not posted now and will remove or delete
        foreach ($promotion_table_exist_category_array as $exist_applied_on_data_value) {
            if (in_array($exist_applied_on_data_value, $selected_category_id)) {
                //exist ----
                # if category id is exist in (applid on) promotions table, then only data will update according by promo code name
                //If level id is 1 then 0 if level id is 2 then restaurant id if level id is 3 then category id if level id is 4 then category id
                $applied_on_id = $exist_applied_on_data_value;
                $data['restaurant_id'] = $restaurant_id; //push restaurant_id  data
                $data['applied_on_id'] = $applied_on_id; //push category id
                $update_status = $this->Common->updateData('promotions', $data, 'code_name = "' . $edit_promotion_code_name . '" AND applied_on_id = ' . $exist_applied_on_data_value . ' AND restaurant_id = "' . $restaurant_id . '"');
            } else {
                //not exist --------
                # if exist resaturant id which is exist in promotions table but , not posted now then it exist resaturant id will delete by exist category id from promotion table by promo code name
                $not_exist_category_id = $exist_applied_on_data_value;
                $delete_query = 'DELETE FROM `promotions` WHERE  `level_id` = "3" AND  `applied_on_id` = "' . $not_exist_category_id . '" AND `restaurant_id` = "' . $restaurant_id . '" AND `code_name` =  "' . $edit_promotion_code_name . '"';
                $this->Common->custom_query($delete_query);
            }
        }
        # get which is new posted category id which does not exist in  promotions table---
        $category_id_array_which_not_exist = array_diff($selected_category_id, $promotion_table_exist_category_array);
        if (!empty($category_id_array_which_not_exist)) {
            foreach ($category_id_array_which_not_exist as $category_id_value) {
                // new data-----
                # if category id is not exist but new data posted ,  then data will insert
                //If level id (applied_on_value )is 1 then 0 if level id(applied_on_value) is 2 then category id
                //
                // check if new restaurant add or add new category in exists restaurant id
                //check is new category added with new restaurant. if given restaurant id is exists in array that means old restaurant and only new category will insert with existing restaurant id , if given restaurant id is not exists in created array that means new category added with new restaurant id than old category data delete with restaurant id according to promo code name and new data insert
                if (!in_array($restaurant_id, $promotion_table_exist_restaurant_array)) {
                    //new restaurant_id and delete with old restaurant id
                    foreach ($promotion_table_exist_restaurant_array as $exists_restaurant_id) {
                        $delete_query = 'DELETE FROM `promotions` WHERE `level_id` = "3" AND `restaurant_id` = "' . $exists_restaurant_id . '" AND `code_name` =  "' . $edit_promotion_code_name . '"';
                        $this->Common->custom_query($delete_query);
                    }
                }
                $applied_on_id = $category_id_value;
                $data['restaurant_id'] = $restaurant_id; //push restaurant_id  data
                $data['applied_on_id'] = $applied_on_id; //push category id
                // EX.  If super admin add level 3 or 4 and in future merchant change it 3 or 4 or new product or category that time added_by value again insert logged role wise
                $data['added_by'] = $this->role; //1 : added by master admin 2 : By merchant
                $insert_status = $this->Common->insertData('promotions', $data);
            }
        }
        if ($update_status > 0 || $insert_status > 0) {
            echo 1; // success
            
        } else {
            echo 0; // somthing is wrong
            
        }
    }
    //Add Edit Promotion/Promocode Function  VIEW-----START--------
    public function add_edit_promotion_details($type) {
        if ($this->id) {
            // echo "<pre>";
            // print_r($_POST);
            // die;
            $promotion_type = $this->db->escape_str(trim($this->input->post('promotion_type')));
            $promo_code = $this->db->escape_str(trim(strtoupper($this->input->post('promo_code'))));
            $promo_code = str_replace(' ', '', $promo_code);
            $discount_value = $this->db->escape_str(trim($this->input->post('discount_value')));
            $description = $this->db->escape_str(trim($this->input->post('description')));
            $promo_code_start_date = $this->db->escape_str(trim($this->input->post('promo_code_start_date')));
            $get_promo_code_end_date = $this->db->escape_str(trim($this->input->post('promo_code_end_date')));
            if ($get_promo_code_end_date != '') # Its a validity promo
            {
                // echo "QQQQQQ";
                $promo_till = strtotime($get_promo_code_end_date);
                $promo_till = strtotime('+1440 minutes', $promo_till); //24 hours of till date
                $promo_code_end_date = strtotime('-1 minutes', $promo_till); //less 1 minut for till date 11:49 becouse from 00:00 start next day
                
            } else {
                // echo "WWWWW";
                $promo_code_end_date = $get_promo_code_end_date;
            }
            // echo "<br>FINAL VALUE".$promo_code_end_date;
            $max_discount_value = $this->db->escape_str(trim($this->input->post('max_discount_value')));
            $max_delivery_charge = $this->db->escape_str(trim($this->input->post('max_delivery_charge'))); //  if promo applied on deilvery charge and max discount value is given then value will be insert otherwise it will be 0
            $max_allowed_time = $this->db->escape_str(trim($this->input->post('max_allowed_time')));
            $minimum_order_amount = $this->db->escape_str(trim($this->input->post('minimum_order_amount')));
            $promotion_applicaion_mode = $this->db->escape_str(trim($this->input->post('promotion_applicaion_mode')));
            $allow_single_user = $this->db->escape_str(trim($this->input->post('allow_single_user')));
            $applied_on = $this->db->escape_str(trim($this->input->post('applied_on')));
            $selected_restaurant_id = $this->db->escape_str($this->input->post('selected_restaurant_id'));
            $selected_product_id = $this->db->escape_str($this->input->post('selected_product_id'));
            $selected_category_id = $this->db->escape_str($this->input->post('selected_category_id'));
            $check_selected_all_restaurant = $this->db->escape_str($this->input->post('check_selected_all_restaurant'));
            $promo_use_type = $this->db->escape_str($this->input->post('promo_use_type'));
            $selected_restaurant_id_count = count($selected_restaurant_id);
            if ($selected_restaurant_id_count == 1) {
                $restaurant_id = $selected_restaurant_id[0];
            }
            if ($max_discount_value == "") {
                $max_discount_value = 0;
            }
            if ($max_allowed_time == "") {
                $max_allowed_time == 0;
            }
            if ($applied_on == 1) {
                $max_delivery_charge = $max_delivery_charge; //  if promo applied on deilvery charge and max discount value is given then value will be insert otherwise it will be 0
                
            } else {
                $max_delivery_charge = 0;
            }
            // echo "type is ".$type;
            // echo "<br>promo_type is ".$promo_type;
            // die;
            if ($type == 2 && $promo_use_type == 1) #EDIT and FOREVER
            {
                $data = ['promo_type' => $promotion_type, //	1 - Flat 2 - Percent
                'code_name' => $promo_code, 'discount_value' => $discount_value, 'desciption' => stripslashes($description), 'valid_from' => 0, # PASS 0
                'valid_till' => 0, # PASS 0
                'min_value' => $minimum_order_amount, 'max_discount' => $max_discount_value, 'max_delivery_discount' => $max_delivery_charge, //  if promo applied on deilvery charge and max discount value is given then value will be insert otherwise it will be 0
                'level_id' => $applied_on, 'is_auto_apply' => $promotion_applicaion_mode, 'allow_multiple_time_use' => $allow_single_user, 'max_allowed_times' => $max_allowed_time, 'promotion_mode_status' => 1, //1- Promo Code, 2- Discount, 3 - Referral
                'created_at' => time(), 'updated_at' => time() ];
            } else {
                $data = ['promo_type' => $promotion_type, //	1 - Flat 2 - Percent
                'code_name' => $promo_code, 'discount_value' => $discount_value, 'desciption' => stripslashes($description), 'valid_from' => strtotime($promo_code_start_date), 'valid_till' => $promo_code_end_date, 'min_value' => $minimum_order_amount, 'max_discount' => $max_discount_value, 'max_delivery_discount' => $max_delivery_charge, //  if promo applied on deilvery charge and max discount value is given then value will be insert otherwise it will be 0
                'level_id' => $applied_on, 'is_auto_apply' => $promotion_applicaion_mode, 'allow_multiple_time_use' => $allow_single_user, 'max_allowed_times' => $max_allowed_time, 'promotion_mode_status' => 1, //1- Promo Code, 2- Discount, 3 - Referral
                'created_at' => time(), 'updated_at' => time() ];
            }
            // echo "<pre>CCCCCC";
            // print_r($data);
            // die;
            if ($promotion_type != "" && $promo_code != "" && $discount_value != "" && ($description != "" || $description == "") && ($promo_code_start_date != "" || $promo_code_start_date == "") && ($promo_code_end_date != "" || $promo_code_end_date == "") && $minimum_order_amount != "" && ($max_discount_value != "" || $max_discount_value == "") && $applied_on != "" && $allow_single_user != "" && $applied_on != "" && ($max_allowed_time != "" || $max_allowed_time == "") && !empty($selected_restaurant_id) && !empty($selected_product_id) && !empty($selected_category_id)) {
                echo 4; //some fields are empty
                
            } else {
                if ($type == 1) { // check type 1 if
                    //check posted code is exists or not because it should be unique
                    $query = 'SELECT  `level_id`,`restaurant_id`,`applied_on_id` FROM `promotions` WHERE `code_name` = "' . $promo_code . '" AND `promo_status` != 3';
                    $exist_code_check = $this->Common->custom_query($query, 'get');
                    if (!empty($exist_code_check)) {
                        echo 3; // Promo Code is exist you cant enter same name
                        
                    } else {
                        // only insert type we added added_by_value because if ex. admin add promo code and than after change by merchant then than it will not update  same as for merchant
                        $data['added_by'] = $this->role; //1 : added by master admin 2 : By merchant
                        //If level id is 1 then 0 if level id is 2 then restaurant id if level id is 3 then product id if level id is 4 then category id,If level id is 8 then 1
                        switch ($applied_on) {
                            case $applied_on == 1: //for delivery charge
                                if ($check_selected_all_restaurant == 0) { //promo for some restaurant
                                    foreach ($selected_restaurant_id as $restaurant_id_value) {
                                        $applied_on_id = 0;
                                        $data['restaurant_id'] = $restaurant_id_value; //push restaurant_id  data
                                        $data['applied_on_id'] = $applied_on_id; //push  data
                                        $insert_status = $this->Common->insertData('promotions', $data);
                                    }
                                } else if ($check_selected_all_restaurant == 1) {
                                    $data['if_promo_for_all_rest'] = 1; //	If promo code is for all restaurant then 1 value will be go , and we dont need to multi entry for all restaruant
                                    $insert_status = $this->Common->insertData('promotions', $data);
                                }
                                break;
                            case $applied_on == 2: //for restaruant
                                if ($check_selected_all_restaurant == 0) { //promo for some restaurant
                                    foreach ($selected_restaurant_id as $restaurant_id_value) {
                                        $applied_on_id = $restaurant_id_value;
                                        $data['restaurant_id'] = $restaurant_id_value; //push restaurant_id  data
                                        $data['applied_on_id'] = $applied_on_id; //push data
                                        $insert_status = $this->Common->insertData('promotions', $data);
                                    }
                                } else if ($check_selected_all_restaurant == 1) {
                                    $data['if_promo_for_all_rest'] = 1; //	If promo code is for all restaurant then 1 value will be go , and we dont need to multi entry for all restaruant
                                    $insert_status = $this->Common->insertData('promotions', $data);
                                }
                                break;
                            case $applied_on == 3: //for product
                                foreach ($selected_product_id as $product_id) {
                                    $applied_on_id = $product_id;
                                    $data['restaurant_id'] = $restaurant_id; //push restaurant_id  data
                                    $data['applied_on_id'] = $applied_on_id; ////push product id
                                    $insert_status = $this->Common->insertData('promotions', $data);
                                }
                                break;
                            case $applied_on == 4: //for category
                                foreach ($selected_category_id as $category_id) {
                                    $applied_on_id = $category_id;
                                    $data['restaurant_id'] = $restaurant_id; //push restaurant_id  data
                                    $data['applied_on_id'] = $applied_on_id; ////push category id
                                    $insert_status = $this->Common->insertData('promotions', $data);
                                }
                                break;
                            case $applied_on == 8: //for self pickup
                                if ($check_selected_all_restaurant == 0) { //promo for some restaurant
                                    foreach ($selected_restaurant_id as $restaurant_id_value) {
                                        $applied_on_id = $restaurant_id_value;
                                        $data['restaurant_id'] = $restaurant_id_value; //push restaurant_id  data
                                        $data['applied_on_id'] = 1; //push data
                                        $insert_status = $this->Common->insertData('promotions', $data);
                                    }
                                } else if ($check_selected_all_restaurant == 1) {
                                    $data['if_promo_for_all_rest'] = 1; //	If promo code is for all restaurant then 1 value will be go , and we dont need to multi entry for all restaruant
                                    $insert_status = $this->Common->insertData('promotions', $data);
                                }
                            } //switch case end
                            if ($insert_status > 0) {
                                $this->session->set_flashdata('success', 'Promotion added successfully!');
                                echo 1;
                            } else {
                                $this->session->set_flashdata('error', 'Internal Server Error');
                                echo 0;
                            }
                        } // promo code is uniquq else exist_code_check close
                        
                    } else if ($type == 2) { // check type 1 if close and type 2 if start
                        // echo "QQQQQQQ";
                        $edit_promotion_code_name = $this->input->post('edit_promotion_code_name');
                        // geting promotion data by promo code
                        # for restaurant which is exist------
                        #for produuct which is exist------(applied on id or level id -  if value is 3)
                        #for category which is exist------(applied on id or level id -  if value is 4)
                        $query = 'SELECT  `level_id`,`restaurant_id`,`applied_on_id` FROM `promotions` WHERE `code_name` = "' . $edit_promotion_code_name . '" AND `promo_status` != 3';
                        $exist_applied_on_data = $this->Common->custom_query($query, 'get');
                        // echo "<pre>LLLLLLL";
                        // print_r($exist_applied_on_data);
                        //check if no changed and all are previous data----START----
                        $check_new_data_is_upload_query = 'SELECT  `promo_type`,`discount_value`,`desciption`,`valid_from`,`valid_till`,`min_value`,`max_discount`,`level_id`,`is_auto_apply`,`allow_multiple_time_use`,`max_allowed_times`,`if_promo_for_all_rest`,`max_delivery_discount` FROM `promotions` WHERE `code_name` = "' . $edit_promotion_code_name . '" AND `promo_status` != 3';
                        $check_new_data_is_upload = $this->Common->custom_query($check_new_data_is_upload_query, 'get');
                        if ($promo_code_start_date == "") {
                            $promo_code_start_date_check = 0; // means code for forever
                            
                        } else {
                            $promo_code_start_date_check = strtotime($promo_code_start_date);
                        }
                        if ($promo_code_end_date == "") {
                            $promo_code_end_date_check = 0; // means code for forever
                            
                        } else {
                            // $promo_code_end_date_check = strtotime($promo_code_end_date);
                            $promo_code_end_date_check = $promo_code_end_date;
                        }
                        $target = array('promo_type' => $promotion_type, 'discount_value' => $discount_value, 'desciption' => $description, 'valid_from' => $promo_code_start_date_check, 'valid_till' => $promo_code_end_date_check, 'min_value' => $minimum_order_amount, 'max_discount' => $max_discount_value, 'level_id' => $applied_on, 'is_auto_apply' => $promotion_applicaion_mode, 'allow_multiple_time_use' => $allow_single_user, 'max_allowed_times' => $max_allowed_time, 'if_promo_for_all_rest' => $check_selected_all_restaurant, 'max_delivery_discount' => $max_delivery_charge);
                        //check if no changed and all are previous data-----END---
                        // for restaurant ----- making simple insdex array
                        $promotion_table_exist_applied_on_data = []; // convert in to simple index array becouse we will compare with posted restaurant array
                        foreach ($exist_applied_on_data as $key => $value) {
                            #action 1 : check if the level id is 3 or 4 then we don't need to create existing restaurant array  for case   1 or 2, because there will be a new entry for restaurant and applied on id will be restaurant id and previous data will be deleted according to promo code name and the array will be empty
                            #action 2 : check if the level id is 2 then we  need to create  exist restaurant array  for case 1 or 2, because there will be work like case 3 or 4, there will be check exist, not exist, and new restaurant added value according to promo code name and the array will be filled with existing restaurant data
                            if ($value['level_id'] == 1 || $value['level_id'] == 2) {
                                array_push($promotion_table_exist_applied_on_data, $value['restaurant_id']);
                            }
                        }
                        #check what new restaurant id  post by the user
                        # which restaurant id exists in the promotions table
                        # if restaurant id exists then only data will update according to by promo code name
                        # if restaurant id does not exist then data will insert
                        # if exist restaurant id which exists in the promotions table but, not posted now then it exists restaurant id will delete by exist restaurant id
                        //If level id is 1 then 0 if level id is 2 then restaurant id if level id is 3 then product id if level id is 4 then category id,If level id is 8 then 1
                        switch ($applied_on) {
                            case $applied_on == 1:
                                // same process will exucute of insert , update or delete  like(case 2: applied on id is 2) only will applied on is change
                                // so if you modifiy code here or case 1 then should will be  same
                                // for this  created comman function
                                if (empty($promotion_table_exist_applied_on_data)) {
                                    //level id 1 - not exist
                                    // delete previous data
                                    $delete_query = 'DELETE FROM `promotions` WHERE `code_name` =  "' . $edit_promotion_code_name . '"';
                                    $this->Common->custom_query($delete_query);
                                    if ($check_selected_all_restaurant == 0) { //promo for some restaurant
                                        // insert new promotion according to level id(applied on)
                                        foreach ($selected_restaurant_id as $restaurant_id_value) {
                                            $applied_on_id = $restaurant_id_value;
                                            $data['restaurant_id'] = $restaurant_id_value; //push restaurant_id  data
                                            $data['applied_on_id'] = $applied_on_id; //push data
                                            // EX.  If super admin add level 3 or 4 and in future merchant change it 3 or 4 or new product or category that time added_by value again insert logged role wise
                                            $data['added_by'] = $this->role; //1 : added by master admin 2 : By merchant
                                            $insert_status = $this->Common->insertData('promotions', $data);
                                            $final_insert_status = $insert_status;
                                        }
                                    } else if ($check_selected_all_restaurant == 1) {
                                        $data['if_promo_for_all_rest'] = 1; //	If promo code is for all restaurant then 1 value will be go , and we dont need to multi entry for all restaruant
                                        $insert_status = $this->Common->insertData('promotions', $data);
                                        $final_insert_status = $insert_status;
                                    }
                                    if ($final_insert_status > 0) {
                                        $this->session->set_flashdata('success', 'Promotion Updated successfully!');
                                        echo 1; //success
                                        
                                    } else {
                                        echo 0; // somthing is wrong
                                        
                                    }
                                } else {
                                    //'level id 1 -  exists and do action  with follwoing function
                                    // check that somthing change or not
                                    $restaurant_id_array_which_new_value = array_diff($selected_restaurant_id, $promotion_table_exist_applied_on_data);
                                    if ($target == $check_new_data_is_upload[0] && empty($restaurant_id_array_which_new_value) && ($selected_restaurant_id == $promotion_table_exist_applied_on_data)) {
                                        echo 2; // nothing changed
                                        
                                    } else {
                                        $this->comman_function_for_applied_on_1_or_2_8($applied_on, $promotion_table_exist_applied_on_data, $selected_restaurant_id, $edit_promotion_code_name, $data, $check_selected_all_restaurant);
                                    }
                                }
                                break;
                            case $applied_on == 2: // if level id is 2 then restaurant id
                                // same process will exucute of insert , update or delete  like(case 1: applied on id is 1) only will applied on is change
                                // so if you modifiy code here or case 1 then should will be  same
                                // for this  created comman function
                                // echo "THIS CASE";
                                if (empty($promotion_table_exist_applied_on_data)) {
                                    // echo "WWWWWW";
                                    //level id 2 - not exist
                                    // delete previous data
                                    $delete_query = 'DELETE FROM `promotions` WHERE `code_name` =  "' . $edit_promotion_code_name . '"';
                                    $this->Common->custom_query($delete_query);
                                    if ($check_selected_all_restaurant == 0) { //promo for some restaurant
                                        // insert new promotion according to level id(applied on)
                                        foreach ($selected_restaurant_id as $restaurant_id_value) {
                                            $applied_on_id = $restaurant_id_value;
                                            $data['restaurant_id'] = $restaurant_id_value; //push restaurant_id  data
                                            $data['applied_on_id'] = $applied_on_id; //push data
                                            // EX.  If super admin add level 3 or 4 and in future merchant change it 3 or 4 or new product or category that time added_by value again insert logged role wise
                                            $data['added_by'] = $this->role; //1 : added by master admin 2 : By merchant
                                            $insert_status = $this->Common->insertData('promotions', $data);
                                            $final_insert_status = $insert_status;
                                        }
                                    } else if ($check_selected_all_restaurant == 1) {
                                        // echo "<br>RRRRRR";
                                        $data['if_promo_for_all_rest'] = 1; //	If promo code is for all restaurant then 1 value will be go , and we dont need to multi entry for all restaruant
                                        $insert_status = $this->Common->insertData('promotions', $data);
                                        // echo "<pre>";
                                        // print_r($data);
                                        // echo "QUERY";
                                        // echo $this->db->last_query();die;
                                        $final_insert_status = $insert_status;
                                    }
                                    die;
                                    if ($final_insert_status > 0) {
                                        $this->session->set_flashdata('success', 'Promotion Updated successfully!');
                                        echo 1; //success
                                        
                                    } else {
                                        echo 0; // somthing is wrong
                                        
                                    }
                                } else {
                                    //'level id 2 -  exists and do action  with follwoing function
                                    // echo "<br>pppppp";
                                    // check that somthing change or not
                                    $restaurant_id_array_which_new_value = array_diff($selected_restaurant_id, $promotion_table_exist_applied_on_data);
                                    if ($target == $check_new_data_is_upload[0] && empty($restaurant_id_array_which_new_value) && ($selected_restaurant_id == $promotion_table_exist_applied_on_data)) {
                                        echo 2; // nothing changed
                                        
                                    } else {
                                        $this->comman_function_for_applied_on_1_or_2_8($applied_on, $promotion_table_exist_applied_on_data, $selected_restaurant_id, $edit_promotion_code_name, $data, $check_selected_all_restaurant);
                                    }
                                }
                                break;
                            case $applied_on == 3:
                                // for product ----- making simple insdex array
                                $promotion_table_exist_product_array = []; // convert in to simple index array becouse we will compare with posted prroduct  array
                                $promotion_table_exist_restaurant_array = []; // convert in to simple index array becouse we will compare with posted restaurant array
                                foreach ($exist_applied_on_data as $key => $value) {
                                    #action 1 : check if the level id is 1,2 or 4 then we don't need to create existing products array  for case 3 , because there will be a new entry for product and applied on id will be product id and previous data will be deleted according to promo code name and the array will be empty
                                    #action 2 : check if the level id is 3 then we  need to create  exist products array  for case 3 , because there will be work like case 1, 2 or 4, there will be check exist, not exist, and new product added value according to promo code name and the array will be filled with existing product data
                                    if ($value['level_id'] == 3) {
                                        array_push($promotion_table_exist_product_array, $value['applied_on_id']); //pushing exists product id
                                        array_push($promotion_table_exist_restaurant_array, $value['restaurant_id']); //pushing exists resturant id
                                        
                                    }
                                }
                                if (empty($promotion_table_exist_product_array)) {
                                    //level id 3 - not exist
                                    $delete_query = 'DELETE FROM `promotions` WHERE `code_name` =  "' . $edit_promotion_code_name . '"';
                                    $this->Common->custom_query($delete_query);
                                    foreach ($selected_product_id as $product_id) {
                                        $applied_on_id = $product_id;
                                        $data['restaurant_id'] = $restaurant_id; //push restaurant_id  data
                                        $data['applied_on_id'] = $applied_on_id; //push data
                                        // EX.  If super admin add level 3 or 4 and in future merchant change it 3 or 4 or new product or category that time added_by value again insert logged role wise
                                        $data['added_by'] = $this->role; //1 : added by master admin 2 : By merchant
                                        $insert_status = $this->Common->insertData('promotions', $data);
                                        $final_insert_status = $insert_status;
                                    }
                                    if ($final_insert_status > 0) {
                                        $this->session->set_flashdata('success', 'Promotion Updated successfully!');
                                        echo 1; //success
                                        
                                    } else {
                                        echo 0; // somthing is wrong
                                        
                                    }
                                } else {
                                    //'level id 3 -  exists and do action  with follwoing function
                                    // its all process same as case 4 (category) but we take indivisual because in future may be changes
                                    // check that somthing change or not
                                    $product_id_array_which_new_value = array_diff($selected_product_id, $promotion_table_exist_product_array);
                                    if ($target == $check_new_data_is_upload[0] && empty($product_id_array_which_new_value) && ($selected_product_id == $promotion_table_exist_product_array)) {
                                        echo 2; // nothing changed
                                        
                                    } else {
                                        $this->comman_function_for_applied_on_3($applied_on, $promotion_table_exist_product_array, $promotion_table_exist_restaurant_array, $selected_product_id, $edit_promotion_code_name, $data, $restaurant_id);
                                    }
                                }
                                break;
                            case $applied_on == 4:
                                // for category ----- making simple insdex array
                                $promotion_table_exist_category_array = []; // convert in to simple index array becouse we will compare with posted prroduct  array
                                $promotion_table_exist_restaurant_array = []; // convert in to simple index array becouse we will compare with posted restaurant array
                                foreach ($exist_applied_on_data as $key => $value) {
                                    #action 1 : check if the level id is 1, 2  or 3 then we don't need to create existing categorys array  for case 4 , because there will be a new entry for category and applied on id will be category id and previous data will be deleted according to promo code name and the array will be empty
                                    #action 2 : check if the level id is 4 then we  need to create  exist categorys array  for case 4 , because there will be work like case 1, 2  or 3, there will be check exist, not exist, and new category added value according to promo code name and the array will be filled with existing category data
                                    if ($value['level_id'] == 4) {
                                        array_push($promotion_table_exist_category_array, $value['applied_on_id']); //pushing exists category id
                                        array_push($promotion_table_exist_restaurant_array, $value['restaurant_id']); //pushing exists resturant id
                                        
                                    }
                                }
                                if (empty($promotion_table_exist_category_array)) {
                                    //level id 4 - not exist
                                    $delete_query = 'DELETE FROM `promotions` WHERE `code_name` =  "' . $edit_promotion_code_name . '"';
                                    $this->Common->custom_query($delete_query);
                                    foreach ($selected_category_id as $category_id) {
                                        $applied_on_id = $category_id;
                                        $data['restaurant_id'] = $restaurant_id; //push restaurant_id  data
                                        $data['applied_on_id'] = $applied_on_id; //push data
                                        // EX.  If super admin add level 3 or 4 and in future merchant change it 3 or 4 or new product or category that time added_by value again insert logged role wise
                                        $data['added_by'] = $this->role; //1 : added by master admin 2 : By merchant
                                        $insert_status = $this->Common->insertData('promotions', $data);
                                        $final_insert_status = $insert_status;
                                    }
                                    if ($final_insert_status > 0) {
                                        $this->session->set_flashdata('success', 'Promotion Updated successfully!');
                                        echo 1; //success
                                        
                                    } else {
                                        echo 0; // somthing is wrong
                                        
                                    }
                                } else {
                                    //'level id 4 -  exists and do action  with follwoing function
                                    // its all process same as case 3 (product) but we take indivisual because in future may be changes
                                    // check that somthing change or not
                                    $category_id_array_which_new_value = array_diff($selected_category_id, $promotion_table_exist_category_array);
                                    if ($target == $check_new_data_is_upload[0] && empty($category_id_array_which_new_value) && ($selected_category_id == $promotion_table_exist_category_array)) {
                                        echo 2; // nothing changed
                                        
                                    } else {
                                        $this->comman_function_for_applied_on_4($applied_on, $promotion_table_exist_category_array, $promotion_table_exist_restaurant_array, $selected_category_id, $edit_promotion_code_name, $data, $restaurant_id);
                                    }
                                }
                                break;
                            case $applied_on == 8:
                                // same process will exucute of insert , update or delete  like(case 8: applied on id is 1) only will applied on is change
                                // so if you modifiy code here or case 8 then should will be  same
                                // for this  created comman function
                                if (empty($promotion_table_exist_applied_on_data)) {
                                    //level id 8 - not exist
                                    // delete previous data
                                    $delete_query = 'DELETE FROM `promotions` WHERE `code_name` =  "' . $edit_promotion_code_name . '"';
                                    $this->Common->custom_query($delete_query);
                                    if ($check_selected_all_restaurant == 0) { //promo for some restaurant
                                        // insert new promotion according to level id(applied on)
                                        foreach ($selected_restaurant_id as $restaurant_id_value) {
                                            $applied_on_id = $restaurant_id_value;
                                            $data['restaurant_id'] = $restaurant_id_value; //push restaurant_id  data
                                            $data['applied_on_id'] = 1; //push data
                                            // EX.  If super admin add level 3 or 4 and in future merchant change it 3 or 4 or new product or category that time added_by value again insert logged role wise
                                            $data['added_by'] = $this->role; //1 : added by master admin 2 : By merchant
                                            $insert_status = $this->Common->insertData('promotions', $data);
                                            $final_insert_status = $insert_status;
                                        }
                                    } else if ($check_selected_all_restaurant == 1) {
                                        $data['if_promo_for_all_rest'] = 1; //	If promo code is for all restaurant then 1 value will be go , and we dont need to multi entry for all restaruant
                                        $insert_status = $this->Common->insertData('promotions', $data);
                                        $final_insert_status = $insert_status;
                                    }
                                    if ($final_insert_status > 0) {
                                        $this->session->set_flashdata('success', 'Promotion Updated successfully!');
                                        echo 1; //success
                                        
                                    } else {
                                        echo 0; // somthing is wrong
                                        
                                    }
                                } else {
                                    //'level id 8 -  exists and do action  with follwoing function
                                    // check that somthing change or not
                                    $restaurant_id_array_which_new_value = array_diff($selected_restaurant_id, $promotion_table_exist_applied_on_data);
                                    if ($target == $check_new_data_is_upload[0] && empty($restaurant_id_array_which_new_value) && ($selected_restaurant_id == $promotion_table_exist_applied_on_data)) {
                                        echo 2; // nothing changed
                                        
                                    } else {
                                        $this->comman_function_for_applied_on_1_or_2_8($applied_on, $promotion_table_exist_applied_on_data, $selected_restaurant_id, $edit_promotion_code_name, $data, $check_selected_all_restaurant);
                                    }
                                }
                            } //switch case end
                            
                        } // check type 2 if close
                        
                    } // else of if not  empty field
                    
            } else {
                $this->load->view('login');
            }
        }
        //Add Edit Promotion/Promocode  Function VIEW-----END--------
        //change discount status  (enable/disable)------------------START----------
        public function active_inactive_discount() {
            $discount_id = $this->db->escape_str(trim($this->input->post('discount_id')));
            $discount_status_value = $this->db->escape_str(trim($this->input->post('discount_status_value')));
            $update_array = ['promo_status' => $discount_status_value, //1 - Enable, 2 - Disable, 3 - Deleted
            'updated_at' => time() ];
            # update data in promo code_ table
            $update_status = $this->Common->updateData('promotions', $update_array, 'id = "' . $discount_id . '"');
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
        //change discount status  (enable/disable)------------------END----------
        //change discount status  (delete)------------------START----------
        public function delete_discount() {
            $discount_id = $this->db->escape_str(trim($this->input->post('discount_id')));
            $update_array = ['promo_status' => 3, //1 - Enable, 2 - Disable, 3 - Deleted
            'updated_at' => time() ];
            # update data in promo code_ table
            $update_status = $this->Common->updateData('promotions', $update_array, 'id = "' . $discount_id . '"');
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
        //change discount status  (delete)------------------END----------
        // SET /EDIT  discount  ---------------------START-----------------
        public function set_edit_discount($type = "") {
            if ($this->id) {
                $restaurant_id = $this->db->escape_str(trim($this->input->post('restaurant_id')));
                $discount_name = $this->db->escape_str(trim($this->input->post('discount_name')));
                $discount_value = $this->db->escape_str(trim($this->input->post('discount_value')));
                $description = $this->db->escape_str(trim($this->input->post('description')));
                $max_amount = $this->db->escape_str(trim($this->input->post('max_amount')));
                $discount_start_date = $this->db->escape_str(trim($this->input->post('discount_start_date')));
                $discount_end_date = $this->db->escape_str(trim($this->input->post('discount_end_date')));
                if ($restaurant_id != "" && $discount_name != "" && $discount_value != "" && $description != "" && $max_amount != "" && $discount_start_date != "" && $discount_end_date != "") {
                    $data = ['promo_type' => 2, //	1 - Flat 2 - Percent (Discount is always %)
                    'code_name' => $discount_name, 'discount_value' => $discount_value, 'desciption' => $description, 'valid_from' => strtotime($discount_start_date), 'valid_till' => strtotime($discount_end_date), 'restaurant_id' => $restaurant_id, 'max_discount' => $max_amount, 'level_id' => 5, //promotion level from promotion_level table 1 - Delivery Charge, 2 -Restaurant, 3- Product, 4- Category, 5 - Global, 6 - Global_Product
                    'promotion_mode_status' => 2, //1- Promo Code, 2- Discount, 3 - Referral
                    'added_by' => $this->role, // this should be 1 , super admin can only set and edit discount and super admin role always be 1
                    'is_auto_apply' => 1, 'created_at' => time(), 'updated_at' => time() ];
                    if ($type == 1) { // set discount
                        $insert_status = $this->Common->insertData('promotions', $data);
                        if ($insert_status > 0) {
                            $this->session->set_flashdata('success', 'Discount added successfully!');
                            echo 1;
                        } else {
                            $this->session->set_flashdata('error', 'Internal Server Error');
                            echo 0;
                        }
                    } else if ($type == 2) { // edit discount
                        $edit_discount_id = $this->db->escape_str(trim($this->input->post('edit_discount_id')));
                        // FOR CHECK new data send or all are exists
                        $discount_data_exists = $this->Common->getData('promotions', 'code_name,discount_value,max_discount,desciption,valid_from,valid_till,restaurant_id', 'promo_status != 3 AND id = ' . $edit_discount_id . ' AND promotion_mode_status = 2');
                        $target = array(array('code_name' => $discount_name, 'discount_value' => $discount_value, 'max_discount' => $max_amount, 'desciption' => $description, 'valid_from' => strtotime($discount_start_date), 'valid_till' => strtotime($discount_end_date), 'restaurant_id' => $restaurant_id));
                        if ($target == $discount_data_exists) {
                            echo 2; //Nothing Changed!
                            
                        } else {
                            $update_status = $this->Common->updateData('promotions', $data, 'restaurant_id = "' . $restaurant_id . '" AND id = "' . $edit_discount_id . '"');
                            if ($update_status > 0) {
                                $this->session->set_flashdata('success', 'Discount Updated successfully!');
                                echo 1;
                            } else {
                                $this->session->set_flashdata('error', 'Internal Server Error');
                                echo 0;
                            }
                        }
                    }
                } else {
                    echo 3; // some or all  field are blank
                    
                }
            } else {
                $this->load->view('admin/login');
            }
        }
        // SET /EDIT  discount  ---------------------END-----------------
        // ADD /EDIT  Referral  ---------------------START-----------------
        public function add_edit_Referral($type = "") {
            if ($this->id && $this->role == 1) {
                $language = $this->db->escape_str(trim($this->input->post('language')));
                $referral_type = $this->db->escape_str(trim($this->input->post('referral_type')));
                $referrer_discount_value = $this->db->escape_str(trim($this->input->post('referrer_discount_value')));
                $referrer_max_discount_value = $this->db->escape_str(trim($this->input->post('referrer_max_discount_value')));
                $referrer_discription = $this->db->escape_str(trim($this->input->post('referrer_discription')));
                $referee_discount_value = $this->db->escape_str(trim($this->input->post('referee_discount_value')));
                $referee_max_discount_value = $this->db->escape_str(trim($this->input->post('referee_max_discount_value')));
                $referee_discription = $this->db->escape_str(trim($this->input->post('referee_discription')));
                $minimum_order_amount = $this->db->escape_str(trim($this->input->post('minimum_order_amount')));
                if ($language != "" && $referral_type != "" && $referrer_discount_value != "" && $referrer_max_discount_value != "" && $referrer_discription != "" && $referee_discount_value != "" && $referee_max_discount_value != "" && $referee_discription != "" && $minimum_order_amount != "" && $type != "") {
                    $data = ['language' => $language, // 1 - english
                    'promo_type' => $referral_type, // 0 - Flat 1 - Percent (Referral is always %)
                    //for referrer-----------
                    'referrer_discount_value' => $referrer_discount_value, 'referrer_max_discount' => $referrer_max_discount_value, 'referrer_discription' => $referrer_discription,
                    //for referee-----------
                    'referee_discount_value' => $referee_discount_value, 'referee_max_discount' => $referee_max_discount_value, 'referee_discription' => $referee_discription, 'min_value' => $minimum_order_amount, 'level_id' => 5, //promotion level from promotion_level table 1 - Delivery Charge, 2 -Restaurant, 3- Product, 4- Category, 5 - Global, 6 - Global_Product
                    'promotion_mode_status' => 3, //1- Promo Code, 2- Referral, 3 - Referral
                    'added_by' => $this->role, // this should be 1 , super admin can only ADD and edit Referral and super admin role always be 1
                    ];
                    if ($type == 1) { // ADD Referral
                        $data['created_at'] = time();
                        $insert_status = $this->Common->insertData('promotions', $data);
                        if ($insert_status > 0) {
                            $this->session->set_flashdata('success', 'Referral added successfully!');
                            echo 1;
                        } else {
                            $this->session->set_flashdata('error', 'Internal Server Error');
                            echo 0;
                        }
                    } else if ($type == 2) { // edit Referral
                        $edit_referral_id = $this->db->escape_str(trim($this->input->post('edit_referral_id')));
                        // FOR CHECK new data send or all are exists
                        $referral_data_exists = $this->Common->getData('promotions', 'promo_type,referrer_discount_value,referrer_max_discount,referrer_discription,referee_discount_value,referee_max_discount,referee_discription,min_value', 'promo_status != 3 AND id = ' . $edit_referral_id . ' AND promotion_mode_status = 3');
                        $target = array(array('promo_type' => $referral_type, 'referrer_discount_value' => $referrer_discount_value, 'referrer_max_discount' => $referrer_max_discount_value, 'referrer_discription' => $referrer_discription, 'referee_discount_value' => $referee_discount_value, 'referee_max_discount' => $referee_max_discount_value, 'referee_discription' => $referee_discription, 'min_value' => $minimum_order_amount));
                        if ($target == $referral_data_exists) {
                            echo 2; // Nothing Changed!
                            
                        } else {
                            $data['updated_at'] = time();
                            $update_status = $this->Common->updateData('promotions', $data, 'id = "' . $edit_referral_id . '"');
                            if ($update_status > 0) {
                                $this->session->set_flashdata('success', 'Referral Updated successfully!');
                                echo 1;
                            } else {
                                $this->session->set_flashdata('error', 'Internal Server Error');
                                echo 0;
                            }
                        }
                    }
                } else {
                    echo 3; // Some or all required field is missing
                    
                }
            } else {
                $this->load->view('admin/login');
            }
        }
        // ADD /EDIT  Referral  ---------------------END-----------------
        //change referral status  (enable/disable)------------------START----------
        public function active_inactive_referral() {
            $referral_id = $this->db->escape_str(trim($this->input->post('referral_id')));
            $referral_status_value = $this->db->escape_str(trim($this->input->post('referral_status_value')));
            $update_array = ['promo_status' => $referral_status_value, //1 - Enable, 2 - Disable, 3 - Deleted
            'updated_at' => time() ];
            # update data in promo code_ table
            $update_status = $this->Common->updateData('promotions', $update_array, 'id = "' . $referral_id . '"');
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
        //change referral status  (enable/disable)------------------END----------
        //Add / edit Ad Banner ------------------------START-----------------
        public function add_edit_AdBanner($type = "") { //ad_banner_add_edit_mode passing from custom.js
            if ($this->id != '' && $this->role == 1) {
                $ad_banner_name = $this->db->escape_str(trim($this->input->post('ad_banner_name')));
                $selected_restaurant_id = $this->db->escape_str(trim($this->input->post('selected_restaurant_id')));
                $external_link = $this->db->escape_str(trim($this->input->post('external_link')));
                $ad_banner_description = $this->db->escape_str(trim($this->input->post('ad_banner_description')));
                $external_link = $this->db->escape_str(trim($this->input->post('external_link')));
                // for edit time -----
                $edit_ad_banner_id = $this->db->escape_str(trim($this->input->post('edit_ad_banner_id')));
                if ($edit_ad_banner_id != "") {
                    $query_part = 'AND id != ' . $edit_ad_banner_id . '';
                } else {
                    $query_part = "";
                }
                //check Ad Banner  is exists or not because it should be unique for every restaurant
                if ($selected_restaurant_id != "") {
                    $exist_banner_for_res_check = $this->Common->getData('ad_banners', 'id', '	restaurant_id = "' . $selected_restaurant_id . '" ' . $query_part . ' AND status != 3');
                } else {
                    $exist_banner_for_res_check = "";
                }
                if (!empty($exist_banner_for_res_check)) {
                    echo 3; // Ad Banner is exist for selected restaurant
                    
                } else { // Ad Banner is not exist for selected restaurant
                    if ($type == 1) { // Add Banner----- START---------
                        #All Form data is required and should not empty
                        # As we do not have website so web banner and web banner in mobile is not mandatory as of now as per client requirement in immediate message board hence commented
                        // if($ad_banner_name != ""   && $ad_banner_description != "" && $_FILES['web_banner_image']['name'] != '' && $_FILES['web_mobile_banner_image']['name'] != '' && $_FILES['mobile_banner_image']['name'] != ''){//Form data is required IF---START---
                        if ($ad_banner_name != "" && $ad_banner_description != "" && $_FILES['mobile_banner_image']['name'] != '') { //Form data is required IF---START---
                            //Logo and banner image upload function ---START
                            $upload_status = $this->common_multiple_image_upload_function($_FILES, 'ad_banner');
                            $new_upload_status = explode("array_split", $upload_status);
                            $ad_banner_upload_status = json_decode($new_upload_status[0], true);
                            $ad_banner_upload_error = json_decode($new_upload_status[1], true);
                            $ad_banner_file_name = json_decode($new_upload_status[2], true);
                            //Logo and banner image upload function ---END
                            //Check LOGO and BANNER  and mobile Banner upload status is true, then data should be insert
                            if ((count(array_unique($ad_banner_upload_status)) === 1 && end($ad_banner_upload_status) === 'true') && empty($ad_banner_upload_error)) { // array all upload status should be same  ---IF START ---
                                # Now make entry to RESTAURANT table
                                $ad_banner_insert = ['restaurant_id' => $selected_restaurant_id, // if select then value be post other wise it will blank
                                'ad_image' => 'assets/images/ad_banners/web_banner_image/' . $ad_banner_file_name['FileNameOf_web_banner_image'], 'ad_image_web' => 'assets/images/ad_banners/web_mobile_banner_image/' . $ad_banner_file_name['FileNameOf_web_mobile_banner_image'], 'ad_image_mobile' => 'assets/images/ad_banners/mobile_banner_image/' . $ad_banner_file_name['FileNameOf_mobile_banner_image'], 'ad_name' => $ad_banner_name, 'ad_description' => $ad_banner_description, 'external_ink' => $external_link, 'created_at' => time(), 'updated_at' => time(), ];
                                $insert_status = $this->Common->insertData('ad_banners', $ad_banner_insert);
                                if ($insert_status > 0) {
                                    $this->session->set_flashdata('success', 'Ad Banner Added successfully!');
                                    echo 1;
                                } else {
                                    $this->session->set_flashdata('error', 'Internal Server Error');
                                    echo 0; //not insert data
                                    
                                }
                            } else { // array all upload status should be same  ---ELSE START ---
                                //echo 'error';
                                //echo 'All upload status is not same';
                                echo 6;
                            } // array all upload status should be same  ---ELSE END ---
                            
                        } else { //Form data is required ---Else---
                            echo 4; // some field is missing
                            
                        }
                        // Add Banner----- End---------
                        
                    } else if ($type == 2) { // Edit Banner----- START---------)
                        //update  id
                        $exist_web_banner_image = $this->db->escape_str(trim($this->input->post('web_banner_image'))); //exist image
                        $exist_web_mobile_banner_image = $this->db->escape_str(trim($this->input->post('web_mobile_banner_image'))); //exist image
                        $exist_mobile_banner_image = $this->db->escape_str(trim($this->input->post('mobile_banner_image'))); //exist image
                        // check new data uploded or not if not uploaded then exist data geting -- START-------
                        $get_exist_ad_banner_data = $this->Common->getData('ad_banners', 'restaurant_id,ad_image,ad_image_web,ad_image_mobile,ad_name,ad_description,	external_ink', 'id = ' . $edit_ad_banner_id . ' AND status NOT IN(3)');
                        if ($selected_restaurant_id == "") {
                            $selected_restaurant_id = 0;
                        }
                        $target = array(array('restaurant_id' => $selected_restaurant_id, 'ad_image' => $exist_web_banner_image, 'ad_image_web' => $exist_web_mobile_banner_image, 'ad_image_mobile' => $exist_mobile_banner_image, 'ad_name' => $ad_banner_name, 'ad_description' => $ad_banner_description, 'external_ink' => $external_link));
                        //custom make array for check if any changes done to confrm
                        // check new data uploded or not if not uploaded then exist data geting -- END--------
                        // check  in folowing threes image that which one is upload or exist image----start---
                        //check logo upload or exist
                        if ($exist_web_banner_image != "") {
                            // if pre image exist , no any image upload
                            $final_logo_image = $exist_web_banner_image;
                            $web_banner_upload_valid = 0;
                        } else {
                            // new image upload
                            $final_logo_image = $_FILES['web_banner_image']['name'];
                            $web_banner_upload_valid = 1;
                        }
                        //check banner upload or exist
                        if ($exist_web_mobile_banner_image != "") {
                            // if pre image exist , no any image upload
                            $final_banner_image = $exist_web_mobile_banner_image;
                            $web_mobile_banner_upload_valid = 0;
                        } else {
                            // new image upload
                            $final_banner_image = $_FILES['web_mobile_banner_image']['name'];
                            $web_mobile_banner_upload_valid = 1;
                        }
                        //check mobile banner upload or exist
                        if ($exist_mobile_banner_image != "") {
                            // if pre image exist , no any image upload
                            $final_mobile_banner_image = $exist_mobile_banner_image;
                            $mobile_banner_upload_valid = 0;
                        } else {
                            // new image upload
                            $final_mobile_banner_image = $_FILES['mobile_banner_image']['name'];
                            $mobile_banner_upload_valid = 1;
                        }
                        // check  in ABOVE threes image that which one is upload or exist image----END---
                        if ($target == $get_exist_ad_banner_data) {
                            echo 2; // nothing changed
                            
                        } else {
                            // Final Update section-----------------------START-----------------------------------
                            //Web, Web mobile and mobile  banner image upload function ---START----
                            $upload_status = $this->common_multiple_image_upload_function($_FILES, 'ad_banner');
                            $new_upload_status = explode("array_split", $upload_status);
                            $ad_banner_upload_status = json_decode($new_upload_status[0], true);
                            $ad_banner_upload_error = json_decode($new_upload_status[1], true);
                            $ad_banner_file_name = json_decode($new_upload_status[2], true);
                            //Web, Web mobile and mobile  banner image upload function ---END----
                            //geting image which gona delete
                            $get_previous_web_banner_image = $get_exist_ad_banner_data[0]['ad_image'];
                            $get_previous_web_mobile_banner_image = $get_exist_ad_banner_data[0]['ad_image_web'];
                            $get_previous_mobile_banner_image = $get_exist_ad_banner_data[0]['ad_image_mobile'];
                            //check Web , web mobile and  mobile banner are new uploaded  ------------START-------
                            if ($web_banner_upload_valid == 1 && $web_mobile_banner_upload_valid == 1 && $mobile_banner_upload_valid == 1) {
                                //echo 'three's upload';
                                if ((count(array_unique($ad_banner_upload_status)) === 1 && end($ad_banner_upload_status) === 'true') && empty($ad_banner_upload_error)) {
                                    $update_web_banner_image = 'assets/images/ad_banners/web_banner_image/' . $ad_banner_file_name['FileNameOf_web_banner_image'] . '';
                                    $update_web_mobile_banner_image = 'assets/images/ad_banners/web_mobile_banner_image/' . $ad_banner_file_name['FileNameOf_web_mobile_banner_image'] . '';
                                    $update_mobile_banner_image = 'assets/images/ad_banners/mobile_banner_image/' . $ad_banner_file_name['FileNameOf_mobile_banner_image'];
                                } else {
                                    $update_web_banner_image = '';
                                    $update_web_mobile_banner_image = '';
                                    $update_mobile_banner_image = '';
                                }
                            }
                            //check Web , web mobile and  mobile banner are new upload  ------------END-------
                            //check Web , web mobile and  mobile banner are not upload , its all are exist file --------START-------
                            if ($web_banner_upload_valid == 0 && $web_mobile_banner_upload_valid == 0 && $mobile_banner_upload_valid == 0) {
                                // echo 'THREES  exist file';
                                $update_web_banner_image = $exist_web_banner_image;
                                $update_web_mobile_banner_image = $exist_web_mobile_banner_image;
                                $update_mobile_banner_image = $exist_mobile_banner_image;
                            }
                            //check Web , web mobile and  mobile banner are not upload , its all are exist file --------END-------
                            //check WEB BANNER upload only and WEB mobile banner and mobile banner are exist file, its all are exist file --------START-------
                            if ($web_banner_upload_valid == 1 && $web_mobile_banner_upload_valid == 0 && $mobile_banner_upload_valid == 0) {
                                //echo 'web banner image upload only';
                                if ((count(array_unique($ad_banner_upload_status)) === 1 && end($ad_banner_upload_status) === 'true') && empty($ad_banner_upload_error)) {
                                    #delete previous banner image------- START --------
                                    if (!empty($get_previous_web_banner_image && file_exists($get_previous_web_banner_image))) {
                                        unlink($get_previous_web_banner_image);
                                    }
                                    #delete previous banner image------- END -------
                                    $update_web_banner_image = 'assets/images/ad_banners/web_banner_image/' . $ad_banner_file_name['FileNameOf_web_banner_image'] . '';
                                    $update_web_mobile_banner_image = $exist_web_mobile_banner_image;
                                    $update_mobile_banner_image = $exist_mobile_banner_image;
                                } else {
                                    $update_web_banner_image = '';
                                    $update_web_mobile_banner_image = '';
                                    $update_mobile_banner_image = '';
                                }
                            }
                            //check WEB BANNER upload only and WEB mobile banner and mobile banner are exist file, its all are exist file --------END-------
                            //check web mobile  banner upload only and web banner and mobile banner are exist file, its all are exist file --------START-------
                            if ($web_mobile_banner_upload_valid == 1 && $mobile_banner_upload_valid == 0 && $web_banner_upload_valid == 0) {
                                //echo 'web mobile banner upload only';
                                if ((count(array_unique($ad_banner_upload_status)) === 1 && end($ad_banner_upload_status) === 'true') && empty($ad_banner_upload_error)) {
                                    #delete previous banner image------- START --------
                                    if (!empty($get_previous_web_mobile_banner_image && file_exists($get_previous_web_mobile_banner_image))) {
                                        unlink($get_previous_web_mobile_banner_image);
                                    }
                                    #delete previous banner image------- END -------
                                    $update_web_mobile_banner_image = 'assets/images/ad_banners/web_mobile_banner_image/' . $ad_banner_file_name['FileNameOf_web_mobile_banner_image'] . '';
                                    $update_web_banner_image = $exist_web_banner_image;
                                    $update_mobile_banner_image = $exist_mobile_banner_image;
                                } else {
                                    $update_web_mobile_banner_image = '';
                                    $update_web_banner_image = '';
                                    $update_mobile_banner_image = '';
                                }
                            }
                            //check web mobile  banner upload only and web banner and mobile banner are exist file, its all are exist file  --------END-------
                            //check mobile banner upload only and web and web mobile  banner are exist file, its all are exist file --------START-------
                            if ($web_mobile_banner_upload_valid == 0 && $mobile_banner_upload_valid == 1 && $web_banner_upload_valid == 0) {
                                //echo 'mobile banner app image upload';
                                if ((count(array_unique($ad_banner_upload_status)) === 1 && end($ad_banner_upload_status) === 'true') && empty($ad_banner_upload_error)) {
                                    if (!empty($get_previous_mobile_banner_image && file_exists($get_previous_mobile_banner_image))) {
                                        unlink($get_previous_mobile_banner_image);
                                    }
                                    #delete previous banner image------- END -------
                                    $update_mobile_banner_image = 'assets/images/ad_banners/mobile_banner_image/' . $ad_banner_file_name['FileNameOf_mobile_banner_image'];
                                    $update_web_banner_image = $exist_web_banner_image;
                                    $update_web_mobile_banner_image = $exist_web_mobile_banner_image;
                                } else {
                                    $update_web_mobile_banner_image = '';
                                    $update_web_banner_image = '';
                                    $update_mobile_banner_image = '';
                                }
                            }
                            //check banner upload only and logo and mobile banner are exist file, its all are exist file --------END-------
                            //check WEB BANNER and WEB mobile banner upload only and mobile banner are exist file, its all are exist file --------START-------
                            if ($web_banner_upload_valid == 1 && $web_mobile_banner_upload_valid == 1 && $mobile_banner_upload_valid == 0) {
                                //echo 'web banner image upload only';
                                if ((count(array_unique($ad_banner_upload_status)) === 1 && end($ad_banner_upload_status) === 'true') && empty($ad_banner_upload_error)) {
                                    #delete previous banner image------- START --------
                                    //web banner-----
                                    if (!empty($get_previous_web_banner_image && file_exists($get_previous_web_banner_image))) {
                                        unlink($get_previous_web_banner_image);
                                    }
                                    //web mobile-----
                                    if (!empty($get_previous_web_mobile_banner_image && file_exists($get_previous_web_mobile_banner_image))) {
                                        unlink($get_previous_web_mobile_banner_image);
                                    }
                                    #delete previous banner image------- END -------
                                    $update_web_banner_image = 'assets/images/ad_banners/web_banner_image/' . $ad_banner_file_name['FileNameOf_web_banner_image'] . '';
                                    $update_web_mobile_banner_image = 'assets/images/ad_banners/web_mobile_banner_image/' . $ad_banner_file_name['FileNameOf_web_mobile_banner_image'] . '';
                                    $update_mobile_banner_image = $exist_mobile_banner_image;
                                } else {
                                    $update_web_banner_image = '';
                                    $update_web_mobile_banner_image = '';
                                    $update_mobile_banner_image = '';
                                }
                            }
                            //check WEB BANNER and WEB mobile banner upload only and mobile banner are exist file, its all are exist file--------End-------
                            //check WEB BANNER and  mobile app banner upload only and web  mobile banner are exist file, its all are exist file--------START-------
                            if ($web_banner_upload_valid == 1 && $web_mobile_banner_upload_valid == 0 && $mobile_banner_upload_valid == 1) {
                                //echo 'web banner image upload only';
                                if ((count(array_unique($ad_banner_upload_status)) === 1 && end($ad_banner_upload_status) === 'true') && empty($ad_banner_upload_error)) {
                                    #delete previous banner image------- START --------
                                    //web banner-----
                                    if (!empty($get_previous_web_banner_image && file_exists($get_previous_web_banner_image))) {
                                        unlink($get_previous_web_banner_image);
                                    }
                                    // mobile app banner----
                                    if (!empty($get_previous_mobile_banner_image && file_exists($get_previous_mobile_banner_image))) {
                                        unlink($get_previous_mobile_banner_image);
                                    }
                                    #delete previous banner image------- END -------
                                    $update_web_banner_image = 'assets/images/ad_banners/web_banner_image/' . $ad_banner_file_name['FileNameOf_web_banner_image'] . '';
                                    $update_web_mobile_banner_image = $exist_web_mobile_banner_image;
                                    $update_mobile_banner_image = 'assets/images/ad_banners/mobile_banner_image/' . $ad_banner_file_name['FileNameOf_mobile_banner_image'];
                                } else {
                                    $update_web_banner_image = '';
                                    $update_web_mobile_banner_image = '';
                                    $update_mobile_banner_image = '';
                                }
                            }
                            //check WEB BANNER and  mobile app banner upload only and web  mobile banner are exist file, its all are exist file--------End-------
                            //check web  mobile banner and  mobile app banner upload only and web  banner are exist file, its all are exist file--------START-------
                            if ($web_banner_upload_valid == 0 && $web_mobile_banner_upload_valid == 1 && $mobile_banner_upload_valid == 1) {
                                //echo 'web banner image upload only';
                                if ((count(array_unique($ad_banner_upload_status)) === 1 && end($ad_banner_upload_status) === 'true') && empty($ad_banner_upload_error)) {
                                    #delete previous banner image------- START --------
                                    //web mobile-----
                                    if (!empty($get_previous_web_mobile_banner_image && file_exists($get_previous_web_mobile_banner_image))) {
                                        unlink($get_previous_web_mobile_banner_image);
                                    }
                                    // mobile app banner----
                                    if (!empty($get_previous_mobile_banner_image && file_exists($get_previous_mobile_banner_image))) {
                                        unlink($get_previous_mobile_banner_image);
                                    }
                                    #delete previous banner image------- END -------
                                    $update_web_banner_image = $exist_web_banner_image;
                                    $update_web_mobile_banner_image = 'assets/images/ad_banners/web_mobile_banner_image/' . $ad_banner_file_name['FileNameOf_web_mobile_banner_image'] . '';
                                    $update_mobile_banner_image = 'assets/images/ad_banners/mobile_banner_image/' . $ad_banner_file_name['FileNameOf_mobile_banner_image'];
                                } else {
                                    $update_web_banner_image = '';
                                    $update_web_mobile_banner_image = '';
                                    $update_mobile_banner_image = '';
                                }
                            }
                            //check WEB BANNER and  mobile app banner upload only and web  mobile banner are exist file, its all are exist file--------End-------
                            if ($update_web_banner_image != "" && $update_web_mobile_banner_image != "" && $update_mobile_banner_image != "") {
                                // all or some upload  or some exist
                                # Now make entry to RESTAURANT table
                                $ad_banner_update = ['restaurant_id' => $selected_restaurant_id, // if select then value be post other wise it will blank
                                'ad_image' => trim($update_web_banner_image), 'ad_image_web' => trim($update_web_mobile_banner_image), 'ad_image_mobile' => trim($update_mobile_banner_image), 'ad_name' => $ad_banner_name, 'ad_description' => $ad_banner_description, 'external_ink' => $external_link, 'updated_at' => time(), ];
                                $update_status = $this->Common->updateData('ad_banners', $ad_banner_update, 'id = ' . $edit_ad_banner_id . '');
                                if ($update_status > 0) {
                                    $this->session->set_flashdata('success', 'Ad Banner Updated successfully!');
                                    echo 1;
                                } else {
                                    $this->session->set_flashdata('error', 'Internal Server Error');
                                    echo 0; //not insert data
                                    
                                }
                            } else { // end of $update_web_banner_image != "" && $update_web_mobile_banner_image != "" && $update_mobile_banner_image != ""
                                echo 7; // missing image path
                                
                            }
                        } // new edited data posted else
                        // Edit Banner----- End---------)
                        
                    }
                } // Else end (// Ad Banner is not exist for selected restaurant )
                
            } else { //check login and role
                $this->load->view('admin/login');
            }
        }
        //Add / edit Ad Banner ------------------------END-----------------
        // Ad  banner delete-----------------------START--------------------
        public function delete_ad_banner() {
            $ad_banner_id = $this->db->escape_str(trim($this->input->post('ad_banner_id')));
            $update_array = ['status' => 3, // 1 - Enable, 2 - Disable, 3 - Deleted
            'updated_at' => time() ];
            # update data in promotion table
            $update_status = $this->Common->updateData('ad_banners', $update_array, "id = " . $ad_banner_id . " ");
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
        // Ad  banner delete-----------------------END---------------------
        // Ad  banner Enable / Disable -----------------------START--------------------
        public function enable_disable_ad_banner() {
            $ad_banner_id = $this->db->escape_str(trim($this->input->post('ad_banner_id')));
            $status_value = $this->db->escape_str(trim($this->input->post('status_value')));
            $update_array = ['status' => $status_value, // 1 - Enable, 2 - Disable, 3 - Deleted
            'updated_at' => time() ];
            # update data in promotion table
            $update_status = $this->Common->updateData('ad_banners', $update_array, "id = " . $ad_banner_id . " ");
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
        // Ad  banner Enable / Disable -----------------------END--------------------
        //Geting ad banner data for edit ---------------------START----------------
        public function get_ad_banner_detail() {
            $ad_banner_id = $this->db->escape_str(trim($this->input->post('ad_banner_id')));
            $ad_banners_data = $this->Common->getData("ad_banners", "*", "id = " . $ad_banner_id . " AND status != 3");
            if (count($ad_banners_data) > 0) {
                echo json_encode($ad_banners_data[0]);
            } else {
                echo 0;
            }
        }
        //Geting ad banner data for edit ---------------------END----------------
        // All user listing / search fillter -------------------START-------------
        public function AllUser($selected_user_role = '', $table = '', $fromdate = 'all', $todate = 'all', $user_status = 'all', $search_key = 'all') {
            if ($this->id != '' && $this->role == 1) {
                //search filter and pagination------START-----------
                $pageData['fromdate'] = $fromdate;
                $pageData['todate'] = $todate;
                $pageData['user_status'] = $user_status; //1 - Enable, 2 - Disable, 3 - Deleted
                $search_key = urldecode($search_key);
                $search_key = trim($search_key);
                $pageData['search'] = $search_key;
                $query_part = "";
                $fromDateNew = strtotime($fromdate . ' 00:00:00');
                $toDateNew = strtotime($todate . ' 24:00:00');
                $table_data = $this->uri->segment(4);
                // check role------------------start------
                if ($selected_user_role == 2) {
                    $user_role_name = 'Merchant';
                    $selected_user_role = 2;
                    if ($table != "" || $fromdate != "all" || $todate != "all" || $user_status != "all" || $search_key != "all") {
                        if ($fromdate != "all" && $todate == "all") {
                            $query_part.= ' AND `created_at` >= "' . strtotime($fromdate) . '"';
                        }
                        if ($todate != "all" && $fromdate == "all") {
                            $query_part.= ' AND `created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
                        }
                        if ($fromdate != "all" && $todate != "all") {
                            $query_part.= ' AND (created_at between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                        }
                        if ($user_status != "all") {
                            $query_part.= ' AND `status` = "' . $user_status . '"';
                        }
                        if ($search_key != "all") {
                            $query_part.= ' AND  (`fullname` LIKE "%' . $search_key . '%" OR  `email` LIKE "%' . $search_key . '%" OR  `mobile` LIKE "%' . $search_key . '%" OR  `user_street_address` LIKE "%' . $search_key . '%"  OR  `user_postal_code` LIKE "%' . $search_key . '%"  OR  `user_pin_address` LIKE "%' . $search_key . '%")';
                        }
                    }
                    //pagination  ---start----
                    $common_query = "SELECT `id`,`role`, `fullname`, `email`, `mobile`, `profile_pic`,  `device_type`,`status` ,`created_at` FROM `users` WHERE `role` = " . $selected_user_role . " " . $query_part . " AND `status` != 5 ORDER BY  `id` DESC";
                } else if ($selected_user_role == 3) {
                    $user_role_name = 'Customer';
                    $selected_user_role = 3;
                    $update_status = false;
                    if ($table != "" || $fromdate != "all" || $todate != "all" || $user_status != "all" || $search_key != "all") {
                        if ($fromdate != "all" && $todate == "all") {
                            $query_part.= ' AND `users`.`created_at` >= "' . strtotime($fromdate) . '"';
                        }
                        if ($todate != "all" && $fromdate == "all") {
                            $query_part.= ' AND `users`.`created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
                        }
                        if ($fromdate != "all" && $todate != "all") {
                            $query_part.= ' AND (`users`.`created_at` between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                        }
                        if ($user_status != "all") {
                            $query_part.= ' AND `users`.`status` = "' . $user_status . '"';
                        }
                        if ($search_key != "all") {
                            $query_part.= ' AND ( `users`.`fullname` LIKE "%' . $search_key . '%" OR  `users`.`email` LIKE "%' . $search_key . '%" OR  `users`.`mobile` LIKE "%' . $search_key . '%" OR  `users`.`user_street_address` LIKE "%' . $search_key . '%"  OR  `users`.`user_postal_code` LIKE "%' . $search_key . '%"  OR  `users`.`user_pin_address` LIKE "%' . $search_key . '%")';
                        }
                    }
                    //pagination  ---start----
                    $common_query = "SELECT `users`.`id` as 'user_id', `users`.`role`,`users`.`number_id`, `users`.`fullname`, `users`.`email`, `users`.`mobile`, `users`.`profile_pic`,  `users`.`device_type`,`users`.`status` ,`users`.`created_at` FROM `users` WHERE `users`.`role` = " . $selected_user_role . " " . $query_part . " AND `users`.`status` != 5 ORDER BY  `users`.`id` DESC"; //JOIN `wallet` ON `users`.`id` = `wallet`.`user_id`
                    
                }
                // check role------------------end------
                $page = ($this->uri->segment(9)) ? ($this->uri->segment(9) - 1) : 0;
                if ($page > 0) {
                    $page_offset = $page * ADMIN_PER_PAGE_RECORDS;
                } else {
                    $page_offset = $page;
                }
                $query = "" . $common_query . " LIMIT " . ADMIN_PER_PAGE_RECORDS . " OFFSET " . $page_offset . " ";
                $user_list_data = $this->Common->custom_query($query, 'get');
                if (!empty($user_list_data)) {
                    foreach ($user_list_data as $key => $value) {
                        if ($selected_user_role == 2) { //if user is merchant
                            $get_merchant_restaurant = $this->Common->getData('restaurants', 'id as restaurant_id,rest_name', 'rest_status = 1 AND admin_id= ' . $value['id'] . '', '', '', 'id', 'DESC');
                            if (!empty($get_merchant_restaurant)) {
                                $user_list_data[$key]['rest_name'] = $get_merchant_restaurant[0]['rest_name'];
                                $user_list_data[$key]['rest_id'] = $get_merchant_restaurant[0]['restaurant_id'];
                            } else {
                                $user_list_data[$key]['rest_name'] = '';
                                $user_list_data[$key]['rest_id'] = '';
                            }
                        } else { //if user is customer
                            $user_list_data[$key]['rest_name'] = '';
                            $user_list_data[$key]['rest_id'] = '';
                        }
                    }
                }
                $pageData['user_list'] = $user_list_data;
                $query = "" . $common_query . "";
                $total_records = count($this->Common->custom_query($query, "get"));
                $url = base_url('admin/AllUser/' . $selected_user_role . '/0/' . $fromdate . '/' . $todate . '/' . $user_status . '/' . $search_key . '/'); //by default table value is 0
                # Pass parameter to common pagination and create pagination function start
                $this->create_pagination($url, $total_records, ADMIN_PER_PAGE_RECORDS);
                $pageData['links'] = $this->pagination->create_links();
                $pageData['start'] = ($page * ADMIN_PER_PAGE_RECORDS) + 1;
                //pagination  ---End----
                //search filter and pagination------END-----------
                $data = array('title' => $user_role_name, 'pageName' => "all-user");
                $pageTitle = $user_role_name;
                $this->createBreadcrumb($data);
                $pageData['urlPart'] = $this->getUrlPart();
                $pageData['pageTitle'] = $user_role_name;
                $pageData['pageName'] = 'all-user';
                $pageData['selected_user_role'] = $selected_user_role;
                /*if($selected_user_role == 2 && $table_data == ""){
                
                }else if($table_data == "2"){
                
                }*/
                if ($table_data == "2") {
                    // if any action tiriger like, delete or enable disable then is url excute by ajax
                    $this->load->view('users_list_table', $pageData); // this table will show accroding to selected role 2 = merchant, 3 = customer
                    
                } else {
                    $this->load->view('masterpage', $pageData);
                }
            } else {
                redirect(base_url('admin/errors_404'));
            }
        }
        // All user listing / search fillter -------------------END-------------
        // Exoport Users csv format file -------------START-----------
        public function exportUserCSV($selected_user_role = '', $fromdate = 'all', $todate = 'all', $user_status = 'all', $search_key = 'all') {
            $query_part = "";
            if ($fromdate != "all" || $todate != "all" || $user_status != "all" || $search_key != "all") {
                if ($fromdate != "all" && $todate == "all") {
                    $query_part.= ' AND `users`.`created_at` >= "' . strtotime($fromdate) . '"';
                }
                if ($todate != "all" && $fromdate == "all") {
                    $query_part.= ' AND `users`.`created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
                }
                if ($fromdate != "all" && $todate != "all") {
                    $query_part.= ' AND (`users`.`created_at` between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                }
                if ($user_status != "all") {
                    $query_part.= ' AND `users`.`status` = "' . $user_status . '"';
                }
                if ($search_key != "all") {
                    $query_part.= ' AND  `users`.`fullname` LIKE "%' . $search_key . '%" OR  `users`.`email` LIKE "%' . $search_key . '%" OR  `users`.`mobile` LIKE "%' . $search_key . '%" OR  `users`.`user_street_address` LIKE "%' . $search_key . '%"  OR  `users`.`user_postal_code` LIKE "%' . $search_key . '%"  OR  `users`.`user_pin_address` LIKE "%' . $search_key . '%"';
                }
            } else {
                $query_part = "";
            }
            // file name
            if ($selected_user_role == 2) {
                $user_name = 'Merchant';
            } else if ($selected_user_role == 3) {
                $user_name = 'Customer';
            }
            $filename = '' . $user_name . '_list_' . date('Ymd') . '.csv';
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
            header("Content-Type: application/csv;");
            $common_query = "SELECT `users`.`id` as 'user_id', `users`.`role`,`users`.`number_id`, `users`.`fullname`, `users`.`email`, `users`.`mobile`, `users`.`profile_pic`,  `users`.`device_type`,`users`.`status` ,`users`.`created_at` FROM `users`  WHERE `users`.`role` = " . $selected_user_role . " " . $query_part . " AND `users`.`status` != 5 ORDER BY  `users`.`id` DESC"; // JOIN `wallet` ON `users`.`id` = `wallet`.`user_id`
            $query = "" . $common_query . "";
            $UserData = $this->Common->custom_query($query, "get");
            // file creation
            $file = fopen('php://output', 'w');
            $header = array("" . $user_name . " Id", "" . $user_name . " Name", "" . $user_name . " Email", "" . $user_name . " Mobile No.", "" . $user_name . " Status", "" . $user_name . " Profile Image", "" . $user_name . " Device Type", "Registered Date");
            fputcsv($file, $header);
            if (count($UserData) > 0) {
                foreach ($UserData as $key => $line) {
                    // For User status --------------------------------------
                    // 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
                    switch ($line['status']) {
                        case 0:
                            $status = "Pending";
                        break;
                        case 1:
                            $status = "Approved";
                        break;
                        case 2:
                            $status = "Rejected";
                        break;
                        case 3:
                            $status = "Inactive";
                        break;
                        case 4:
                            $status = 'Verified by OTP but Approval is Pending';
                        break;
                        default:
                            $status = "Pending";
                    }
                    // For Device --------------------------------------
                    // 1 - Web 2 - Android 3 - iOS | Update on every Login
                    switch ($line['device_type']) {
                        case 1:
                            // $user_device_type = "Web";
                            $user_device_type = "Android";
                        break;
                        case 2:
                            // $user_device_type = "Android";
                            $user_device_type = "iOS";
                        break;
                        case 3:
                            // $user_device_type = "iOS";
                            $user_device_type = "Web";
                        break;
                        default:
                            $user_device_type = "Null";
                    }
                    //Epcho time convert  and for registerd date--------
                    if ($line['created_at'] != '') {
                        $createdAt = date('d/m/Y', $line['created_at']);
                    } else {
                        $createdAt = 'NA';
                    }
                    // For profile image for user---------------------------------------
                    if ($line['profile_pic'] != "") {
                        $profile_img = base_url() . $line['profile_pic'];
                    } else {
                        $profile_img = "";
                    }
                    $data_array = array($line['number_id'], $line['fullname'], $line['email'], $line['mobile'], $status, $profile_img, $user_device_type, $createdAt);
                    fputcsv($file, $data_array);
                }
            }
            fclose($file);
            exit;
        }
        // Exoport Users csv file -------------END--------------
        //Add or Edit Merchant view-----------------------------START-------------
        public function add_edit_merchant($type = '', $user_id = '', $user_role = '') {
            if ($this->id && $this->role == 1) {
                if ($type == 1) { //add mode
                    $data = array('title' => "Add Merchant", 'pageName' => "add-edit-merchant", 'type' => 1,);
                    $this->load->view('masterpage', $data);
                } else if ($type == 2) { //edit mode
                    $pageData['user_date'] = $this->Common->getData('users', 'id,role, fullname, email, mobile, profile_pic,  device_type,status ,created_at', '(id = "' . $user_id . ', role =' . $user_role . '") AND status != 5'); //role  =2
                    $data = array('title' => "Edit Merchant", 'pageName' => "add-edit-merchant", 'type' => 2,);
                    $this->createBreadcrumb($data);
                    $pageData['urlPart'] = $this->getUrlPart();
                    $pageData['pageTitle'] = 'Edit Merchant';
                    $pageData['title'] = 'Edit Merchant';
                    $pageData['type'] = 2;
                    $pageData['pageName'] = 'add-edit-merchant';
                    $this->load->view('masterpage', $pageData);
                }
            } else {
                $this->load->view('admin/login');
            }
        }
        //Add or Edit Merchant view-----------------------------END-------------
        //Edit Customer detail-----------------------------START-------------
        public function edit_customer($user_id = '', $user_role = '') {
            if ($this->id && $this->role == 1) {
                $pageData['user_date'] = $this->Common->getData('users', 'id,role, fullname, email, mobile, profile_pic, user_pin_address,device_type,status,user_unit_number,user_postal_code,latitude,longitude,created_at', 'id = ' . $user_id . '  AND role = ' . $user_role . '  AND status != 5'); //role 3
                $data = array('title' => "Edit Customer", 'pageName' => "edit-customer", 'type' => 2,);
                $this->createBreadcrumb($data);
                $pageData['urlPart'] = $this->getUrlPart();
                $pageData['pageTitle'] = 'Edit Customer';
                $pageData['title'] = 'Edit Customer';
                $pageData['type'] = 2;
                $pageData['pageName'] = 'edit-customer';
                $this->load->view('masterpage', $pageData);
            } else {
                $this->load->view('admin/login');
            }
        }
        //Edit Customer detail-----------------------------END-------------
        # Image resize function start
        public function image_resize($source_image, $new_image, $width, $height) {
            $configer = array('image_library' => 'gd2', 'source_image' => $source_image, 'new_image' => $new_image, 'maintain_ratio' => FALSE, 'width' => $width, 'height' => $height,);
            $this->image_lib->clear();
            $this->image_lib->initialize($configer);
            $this->image_lib->resize();
        }
        # Image resize function end
        //Add and edit Merchant deatils---------------------------START-----------
        public function add_edit_merchant_details($type = '') {
            $fullname = $this->db->escape_str(trim(ucfirst($this->input->post('fullname'))));
            $email = $this->db->escape_str(trim($this->input->post('email')));
            $mobile = $this->db->escape_str(trim($this->input->post('mobile')));
            $user_id = $this->db->escape_str(trim($this->input->post('user_id')));
            //GeneratePassword randamly
            $GeneratedPassword = $this->GeneratePassword($fullname, $mobile);
            if ($user_id != "" && $type == 2) {
                $query_part = "AND id != " . $user_id . "";
            } else {
                $query_part = "";
            }
            if ($this->id && $this->role == 1) {
                //Check user, user's email and mobile no.  should be unique
                $check = $this->Common->getData('users', 'id', '(email = "' . $email . '" OR mobile = "' . $mobile . '") ' . $query_part . ' AND status != 5');
                if (!empty($check)) { //check IF ---START----
                    echo 2;
                } else {
                    if ($type == 1) {
                        //add mode  ------------------------start--------------
                        if (!empty($fullname) && !empty($email) && !empty($mobile)) {
                            if (!empty($_FILES['merchant_profile_image']['name'])) {
                                //if image uploaded
                                $exp = explode(".", $_FILES['merchant_profile_image']['name']);
                                $ext = end($exp);
                                $st1 = substr(date('ymd'), 0, 3);
                                $st2 = $st1 . rand(1, 100000);
                                $fileName = $st2 . time() . date('ymd') . "." . $ext;
                                $original_image_path = 'assets/merchant/merchant_profile_pic/';
                                $resize_image_path = 'assets/merchant/merchant_profile_pic/resized_profile_pic/';
                                /* Image upload  */
                                $config['upload_path'] = $original_image_path;
                                $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                                $config['file_name'] = $fileName;
                                $this->upload->initialize($config);
                                if (!$this->upload->do_upload('merchant_profile_image')) {
                                    $error_msg = $this->upload->display_errors();
                                    $message = strip_tags($error_msg);
                                    $this->session->set_flashdata('error', $message);
                                } else {
                                    $data = ['fullname' => $fullname, 'email' => $email, 'password' => $this->bcrypt->hash_password($GeneratedPassword), 'mobile' => $mobile, 'role' => 2, 'profile_pic' => $resize_image_path . $fileName, 'created_at' => time(), 'updated_at' => time() ];
                                    $latest_user_id = $this->Common->insertData('users', $data);
                                    $ins = $this->Common->getData('users', 'id,status', 'id = "' . $latest_user_id . '"');
                                    $display_id_start = 10000; # Static value and it will be added by the last Id in increasing way
                                    $sr_display_id = $display_id_start + $latest_user_id;
                                    $update_array = ['number_id' => $sr_display_id, ];
                                    $this->Common->updateData('users', $update_array, 'id = "' . $latest_user_id . '"');
                                    # Resize only if NOT SVG
                                    if ($ext !== 'svg') {
                                        /*Image resize function starts here*/
                                        $source_image = $original_image_path . $fileName;
                                        $new_image = $resize_image_path . $fileName;
                                        $width = PROFILE_IMAGE_RESIZE_WIDTH;
                                        $height = PROFILE_IMAGE_RESIZE_WIDTH;
                                        # Call this function to rezise the image and place in a new path
                                        $this->image_resize($source_image, $new_image, $width, $height);
                                        /*Image resize function ends here*/
                                    } else {
                                        /* Image upload */
                                        $config['upload_path'] = $resize_image_path;
                                        $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                                        $config['file_name'] = $fileName;
                                        $this->upload->initialize($config);
                                        if (!$this->upload->do_upload('merchant_profile_image')) {
                                            $error_msg = $this->upload->display_errors();
                                            $message = strip_tags($error_msg);
                                            $this->session->set_flashdata('error', $message);
                                        }
                                    }
                                }
                            } else {
                                //if image not upload
                                $data = ['fullname' => $fullname, 'email' => $email, 'password' => $this->bcrypt->hash_password($GeneratedPassword), 'mobile' => $mobile, 'role' => 2, 'profile_pic' => "", 'created_at' => time(), 'updated_at' => time() ];
                                $latest_user_id = $this->Common->insertData('users', $data);
                                $ins = $this->Common->getData('users', 'id,status', 'id = "' . $latest_user_id . '"');
                                $display_id_start = 10000; # Static value and it will be added by the last Id in increasing way
                                $sr_display_id = $display_id_start + $latest_user_id;
                                $update_array = ['number_id' => $sr_display_id, ];
                                $this->Common->updateData('users', $update_array, 'id = "' . $latest_user_id . '"');
                            }
                            if ($latest_user_id > 0) {
                                # mail_send code start. This mail sends just a thankyou mail to merchant email Id
                                $mail_data['name'] = trim($fullname);
                                $mail_data['header_title'] = APP_NAME . ' : Thank you for your intrest !';
                                $mail_data['email'] = $email;
                                $mail_data['password'] = $GeneratedPassword;
                                $email = $email;
                                $subject = "Welcome to " . APP_NAME;
                                # Get Social urls from Database settings table
                                $social_urls = $this->get_social_urls();
                                $mail_data['facebook_url'] = $social_urls['facebook'];
                                $mail_data['google_url'] = $social_urls['google'];
                                $mail_data['insta_url'] = $social_urls['insta'];
                                $mail_data['website_url'] = $social_urls['website'];
                                # load template view
                                $message = $this->load->view('email/merchant_add_by_admin', $mail_data, TRUE);
                                // echo $message;die;
                                $mail_success_status = send_mail($email, $subject, $message);
                                # mail send code end
                                if ($mail_success_status == 1) {
                                    $this->session->set_flashdata('success', 'Merchant added successfully');
                                    echo 1;
                                } else {
                                    echo 0;
                                }
                            }
                        } else {
                            echo 4;
                        }
                        //add mode  ------------------------end--------------
                        // Edit Mode -----------------------Start-----------
                        
                    } else if ($type == 2 && $user_id != "") { //edit mode
                        $merchant_profile_image = $this->db->escape_str(trim($this->input->post('merchant_profile_image')));
                        $get_user_data = $this->Common->getData("users", "fullname,email,mobile,profile_pic", "id = " . $user_id . " and role = 2");
                        $target = array(array('fullname' => $fullname, 'email' => $email, 'mobile' => $mobile, 'profile_pic' => $merchant_profile_image));
                        if (!empty($fullname) && !empty($email) && !empty($mobile) && !empty($_FILES)) {
                            #delete previous profile image------- START --------
                            $get_previous_profile_pic = $get_user_data[0]['profile_pic'];
                            if (!empty($get_previous_profile_pic && file_exists($get_previous_profile_pic))) {
                                //echo "The file $get_previous_profile_pic exists";
                                unlink($get_previous_profile_pic);
                            }
                            //upload image
                            $exp = explode(".", $_FILES['merchant_profile_image']['name']);
                            $ext = end($exp);
                            $st1 = substr(date('ymd'), 0, 3);
                            $st2 = $st1 . rand(1, 100000);
                            $fileName = $st2 . time() . date('ymd') . "." . $ext;
                            $original_image_path = 'assets/merchant/merchant_profile_pic/';
                            $resize_image_path = 'assets/merchant/merchant_profile_pic/resized_profile_pic/';
                            /* Image upload  */
                            $config['upload_path'] = $original_image_path;
                            $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                            $config['file_name'] = $fileName;
                            $this->upload->initialize($config);
                            if (!$this->upload->do_upload('merchant_profile_image')) {
                                $error_msg = $this->upload->display_errors();
                                $message = strip_tags($error_msg);
                                $this->session->set_flashdata('error', $message);
                            } else {
                                $update_array = ['fullname' => $fullname, 'email' => $email, 'mobile' => $mobile, 'profile_pic' => $resize_image_path . $fileName, 'updated_at' => time() ];
                                $this->Common->updateData('users', $update_array, "id = " . $user_id . ' AND role = 2');
                                # Resize only if NOT SVG
                                if ($ext !== 'svg') {
                                    /*Image resize function starts here*/
                                    $source_image = $original_image_path . $fileName;
                                    $new_image = $resize_image_path . $fileName;
                                    $width = PROFILE_IMAGE_RESIZE_WIDTH;
                                    $height = PROFILE_IMAGE_RESIZE_WIDTH;
                                    # Call this function to rezise the image and place in a new path
                                    $this->image_resize($source_image, $new_image, $width, $height);
                                    /*Image resize function ends here*/
                                } else {
                                    /* Image upload */
                                    $config['upload_path'] = $resize_image_path;
                                    $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                                    $config['file_name'] = $fileName;
                                    $this->upload->initialize($config);
                                    if (!$this->upload->do_upload('merchant_profile_image')) {
                                        $error_msg = $this->upload->display_errors();
                                        $message = strip_tags($error_msg);
                                        $this->session->set_flashdata('error', $message);
                                    }
                                }
                                $this->session->set_flashdata('success', 'Merchant added successfully');
                                //echo 1;
                                $update_status = true;
                            }
                            // if exist image only data change
                            
                        } elseif ($get_user_data == $target) {
                            echo 0; // noting data edit
                            $update_status = false;
                            // else if new image and data upload
                            
                        } else if (!empty($fullname) && !empty($email) && !empty($mobile)) {
                            if ($merchant_profile_image != '') {
                                $update_array = ['fullname' => $fullname, 'email' => $email, 'mobile' => $mobile, 'profile_pic' => $merchant_profile_image, 'updated_at' => time() ];
                            } else {
                                $update_array = ['fullname' => $fullname, 'email' => $email, 'mobile' => $mobile, 'profile_pic' => "", 'updated_at' => time() ];
                            }
                            $this->Common->updateData('users', $update_array, "id = " . $user_id . ' AND role = 2');
                            $this->session->set_flashdata('success', 'Merchant <b>( ' . $fullname . ' )</b> Updated successfully');
                            // echo 1;
                            $update_status = true;
                        }
                        if ($update_status == true) {
                            # mail_send code start. This mail sends just a thankyou mail to merchant email Id
                            $mail_data['name'] = trim($fullname);
                            $mail_data['header_title'] = APP_NAME . ' :  Your Profile Details are Updated !';
                            $mail_data['email'] = $email;
                            $email = $email;
                            $subject = "Welcome to " . APP_NAME;
                            # Get Social urls from Database settings table
                            $social_urls = $this->get_social_urls();
                            $mail_data['facebook_url'] = $social_urls['facebook'];
                            $mail_data['google_url'] = $social_urls['google'];
                            $mail_data['insta_url'] = $social_urls['insta'];
                            $mail_data['website_url'] = $social_urls['website'];
                            # load template view
                            $message = $this->load->view('email/merchant_edited_by_admin', $mail_data, TRUE);
                            // echo $message;die;
                            $mail_success_status = send_mail($email, $subject, $message);
                            # mail send code end
                            if ($mail_success_status == 1) {
                                $this->session->set_flashdata('success', 'Merchant added successfully');
                                echo 1;
                            } else {
                                echo 0;
                            }
                        }
                    } else {
                        echo 5;
                    }
                }
            } else {
                $this->load->view('admin/login');
            }
        }
        //Add and edit Merchant deatils---------------------------END-----------
        //Change user status-------- START----------
        //0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
        public function edit_user_status($selected_role = "") {
            $user_id = $this->db->escape_str(trim($this->input->post('user_id')));
            $user_status = $this->db->escape_str(trim($this->input->post('user_status_value')));
            $update_array = ['status' => $user_status, // 1 - Enable, 2 - Disable, 3 - Deleted
            'updated_at' => time() ];
            # update data in user table
            $update_status = $this->Common->updateData('users', $update_array, "id = " . $user_id . " AND role = " . $selected_role . "");
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
        //0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
        //Change user status Delete ------ END------
        // User (Custoemr / Merchant,  role 2 = Merchant, 3 = Customer) Delete -------- START----------
        public function delete_user($selected_role = "") {
            $user_id = $this->db->escape_str(trim($this->input->post('user_id')));
            $update_array = ['status' => 5, // 1 - Enable, 2 - Disable, 3 - Deleted
            'updated_at' => time() ];
            # update data in user table
            $update_status = $this->Common->updateData('users', $update_array, "id = " . $user_id . " AND role = " . $selected_role . "");
            if ($update_status > 0) {
                //if restaruant is exit for this user id(merchant id) then we have to also delete status mange in restaurants manage
                //check this user is exist in restaurant table
                $get_merchant_restaurant = $this->Common->getData("restaurants", "id", "admin_id = " . $user_id . " and rest_status NOT IN(3)"); // status 3 = delete
                if (!empty($get_merchant_restaurant)) {
                    $update_array = ['rest_status' => 3, // 1 - Enable, 2 - Disable, 3 - Deleted
                    'updated_at' => time() ];
                    # update data in restaurant table
                    $update_res_status = $this->Common->updateData('restaurants', $update_array, "id = " . $get_merchant_restaurant[0]['id']);
                    if ($update_res_status > 0) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                } else {
                    echo 1; // only user table will update
                    
                }
            } else {
                echo 0;
            }
        }
        //User (Custoemr / Merchant,  role 2 = Merchant, 3 = Customer) Delete ------ END------
        //Edit customer details ------------------------START-------------
        public function edit_customer_details() {
            $fullname = $this->db->escape_str(trim($this->input->post('fullname')));
            $email = $this->db->escape_str(trim($this->input->post('email')));
            $mobile = $this->db->escape_str(trim($this->input->post('mobile')));
            $user_id = $this->db->escape_str(trim($this->input->post('user_id')));
            $customer_pin_address = $this->db->escape_str(trim($this->input->post('customer_pin_address')));
            $postal_code = $this->db->escape_str(trim($this->input->post('postal_code')));
            $unit_number = $this->db->escape_str(trim($this->input->post('unit_number')));
            $customer_latitude = $this->db->escape_str(trim($this->input->post('customer_latitude')));
            $customer_longtitude = $this->db->escape_str(trim($this->input->post('customer_longtitude')));
            $get_user_data = $this->Common->getData("users", "fullname,email,mobile,user_pin_address,user_unit_number,user_postal_code,latitude,longitude", "id = " . $user_id . " and role = 3");
            $target = array(array('fullname' => $fullname, 'email' => $email, 'mobile' => $mobile, 'user_pin_address' => $customer_pin_address, 'user_unit_number' => $unit_number, 'user_postal_code' => $postal_code, 'latitude' => $customer_latitude, 'longitude' => $customer_longtitude));
            if ($this->id && $this->role == 1) {
                //Check user, user's email and mobile no.  should be unique
                $check = $this->Common->getData('users', 'id', '(email = "' . $email . '" OR mobile = "' . $mobile . '") AND id != "' . $user_id . '" AND status != 5');
                if (!empty($check)) { //check IF ---START----
                    echo 3;
                } else if ($get_user_data == $target) {
                    echo 0; // noting data edit
                    // else if new image and data upload
                    
                } else if (!empty($fullname) && !empty($email) && !empty($mobile) && $user_id != '' && $customer_pin_address != "" && $unit_number != "" && $customer_latitude != "" && $customer_longtitude != "") {
                    $update_array = ['fullname' => $fullname, 'email' => $email, 'mobile' => $mobile, 'user_pin_address' => $customer_pin_address, 'user_unit_number' => $unit_number, 'user_postal_code' => $postal_code, 'latitude' => $customer_latitude, 'longitude' => $customer_longtitude, 'updated_at' => time() ];
                    $res = $this->Common->updateData('users', $update_array, "id = " . $user_id . ' AND role = 3');
                    //$this->session->set_flashdata('success', 'Customer <b>( '.$fullname.' )</b> Updated successfully');
                    if ($res > 0) {
                        echo 1;
                    } else {
                        echo 2;
                    }
                }
            } else {
                $this->load->view('admin/login');
            }
        }
        //Edit customer details ------------------------END-------------
        // Add Money in to customer wallet ---------------START----------
        public function add_money_in_customer_wallet() {
            $customer_id = $this->db->escape_str(trim($this->input->post('customer_id')));
            $customer_credit_amount = $this->db->escape_str(trim($this->input->post('customer_credit_amount')));
            $customer_credit_comments = $this->db->escape_str(trim($this->input->post('customer_credit_comments')));
            if ($customer_id != "" && $customer_credit_amount != "'") {
                $insert_wallet_table = ['user_id' => $customer_id, 'wallet_date' => time(), 'credited' => $customer_credit_amount, 'type' => 2, //1 - Cashback 2 - Money Added 3 debited
                'added_by' => $this->role, //1 - By Admin 2 - By Customer
                'comments' => $customer_credit_comments, 'created_at' => time(), 'updated_at' => time(), ];
                $insert_status = $this->Common->insertdata('wallet', $insert_wallet_table);
                if ($insert_status > 0) {
                    # send notification code------------------- start-----------------------
                    //getting user device token  for send notification
                    $tokens = $this->Common->getData('users', 'device_token', "id=" . $customer_id);
                    $notification_data_fields = array('message' => 'S$' . $customer_credit_amount . ' added to your wallet', 'title' => NOTIFICATION_MONEY_ADDED, 'notification_type' => 'WALLET_RECHARGE');
                    if (!empty($tokens)) {
                        foreach ($tokens as $tk) {
                            $token = $tk['device_token'];
                            if ($token != "") {
                                sendPushNotification($token, $notification_data_fields, IOS_BUNDLE_ID_CUSTOMER);
                            }
                        }
                    }
                    # send notification code-------------------- end-----------------------
                    # Now insert notification to Database FOR MERCHANT ID
                    $insertData = ['title' => 'S$' . $customer_credit_amount . ' added to your wallet', 'to_user_id' => $customer_id, 'type' => 2, # 2 - Payment related ( basically to restaurant)
                    'is_read' => 1, 'created_at' => time(), 'updated_at' => time(), ];
                    $this->Common->insertData('notifications', $insertData);
                    echo 1;
                } else {
                    echo 0;
                }
            } else {
                echo 2;
            }
        }
        // Add Money in to customer wallet ---------------END----------
        // Dedcut Money from customer wallet ---------------START----------
        public function deduct_money_from_customer_wallet() {
            $customer_id = $this->db->escape_str(trim($this->input->post('customer_id')));
            $customer_debit_amount = $this->db->escape_str(trim($this->input->post('customer_debit_amount')));
            $customer_debit_amount_comments = $this->db->escape_str(trim($this->input->post('customer_debit_amount_comments')));
            if ($customer_id != "" && $customer_debit_amount != "'") {
                $insert_wallet_table = ['user_id' => $customer_id, 'wallet_date' => time(), 'debited' => $customer_debit_amount, 'type' => 3, //1 - Cashback 2 - Money Added 3 debited
                'added_by' => $this->role, //1 - By Admin 2 - By Customer
                'comments' => $customer_debit_amount_comments, 'created_at' => time(), 'updated_at' => time(), ];
                $insert_status = $this->Common->insertdata('wallet', $insert_wallet_table);
                if ($insert_status > 0) {
                    # send notification code------------------- start-----------------------
                    //getting user device token  for send notification
                    $tokens = $this->Common->getData('users', 'device_token', "id=" . $customer_id);
                    $notification_data_fields = array('message' => 'S$' . $customer_debit_amount . ' Deduct from your wallet', 'title' => NOTIFICATION_MONEY_DEDUCTED, 'notification_type' => 'WALLET_DEDUCTION');
                    if (!empty($tokens)) {
                        foreach ($tokens as $tk) {
                            $token = $tk['device_token'];
                            if ($token != "") {
                                sendPushNotification($token, $notification_data_fields, IOS_BUNDLE_ID_CUSTOMER);
                            }
                        }
                    }
                    # send notification code-------------------- end-----------------------
                    # Now insert notification to Database FOR MERCHANT ID
                    $insertData = ['title' => 'S$' . $customer_debit_amount . ' Deduct from your wallet', 'to_user_id' => $customer_id, 'type' => 2, # 2 - Payment related ( basically to restaurant)
                    'is_read' => 1, 'created_at' => time(), 'updated_at' => time(), ];
                    $this->Common->insertData('notifications', $insertData);
                    echo 1;
                } else {
                    echo 0;
                }
            } else {
                echo 2;
            }
        }
        // Dedcut Money from customer wallet ---------------END----------
        //Show particular user detail ----------START--------------
        //Restaurant Details Function
        public function user_details($user_role = '', $user_id = '') {
            if ($this->id && $this->role == 1) {
                if ($user_role == 2) {
                    $pageTitle = 'Merchant Details';
                }
                if ($user_role == 3) {
                    $pageTitle = 'Customer Details';
                }
                $data = array('title' => "User Details", 'pageName' => "show-user-detail");
                $pageData['user_detail'] = $this->Common->getData('users', 'id,role,hear_about_text,number_id, fullname, email, mobile, profile_pic, device_type,status ,user_pin_address,user_unit_number,user_street_address,user_postal_code,created_at', 'id = ' . $user_id . '  AND role = ' . $user_role . '  AND status != 5');
                # Get wallet balance of customer ----start
                $wallet_balance = $this->get_wallet_balance($user_id);
                $wallet_balance = str_replace(",", "", $wallet_balance[0]['wallet_balance']);
                // $wallet_balance = (int)$wallet_balance[0]['wallet_balance'];
                $total_balance = number_format($wallet_balance, 2, '.', '');
                $pageData['wallet_balance'] = $total_balance;
                # Get wallet balance of customer ----end
                $this->createBreadcrumb($data);
                $pageData['urlPart'] = $this->getUrlPart();
                $pageData['pageTitle'] = $pageTitle;
                $pageData['pageName'] = 'show-user-detail';
                $pageData['user_role'] = $user_role;
                $this->load->view('masterpage', $pageData);
            } else {
                $this->load->view('login');
            }
        }
        //Show particular user detail ----------END--------------
        //Show category in selects box ---START------------------
        public function show_category_according_selected_restaurant($restaurant_id, $handle_mode) {
            $category_data_by_res_id = $this->Common->getData('categories', 'id as "category_id",category_name,category_status', 'category_status != 3 AND restaurant_id = ' . $restaurant_id . '', '', '', 'id', 'DESC', '');
            if (count($category_data_by_res_id) > 0) {
                if ($handle_mode == 1) {
                    return $category_data_by_res_id;
                } else {
                    echo json_encode($category_data_by_res_id);
                }
            } else {
                return 0;
            }
        }
        //Show category in selects box ---END------------------
        //Show Products --------------------------START---------------
        public function products($table = '', $fromdate = 'all', $todate = 'all', $product_status = 'all', $product_food_type = 'all', $search_key = 'all', $restaurant_id = 0, $category_id = 0) {
            if ($this->id) {
                //search filter and pagination------START-----------
                $pageData['fromdate'] = $fromdate;
                $pageData['todate'] = $todate;
                $pageData['product_status'] = $product_status; //1 - Enable, 2 - Disable, 3 - Deleted
                $pageData['product_food_type'] = $product_food_type; //1 - veg, 2 - non-veg
                $search_key = urldecode($search_key);
                $search_key = trim($search_key);
                $pageData['search'] = $search_key;
                $query_part = "";
                $query_part_for_category = "";
                $fromDateNew = strtotime($fromdate . ' 00:00:00');
                $toDateNew = strtotime($todate . ' 24:00:00');
                $table_data = $this->uri->segment(3);
                //  if merchant is logged in then this condition will check and only merchant restaurant prodcuts will show if this blank that means super admin is logged in
                if ($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2 || $this->role == 2) {
                    $restaurant_id = $this->logged_in_restaurant_id;
                    $query_part.= ' AND `products`.`restaurant_id` = "' . $restaurant_id . '"';
                    $query_part_for_category.= ' AND `restaurant_id` = "' . $restaurant_id . '"';
                }
                if ($table != "" || $fromdate != "all" || $todate != "all" || $product_status != "all" || $search_key != "all" || $restaurant_id != 0 || $category_id != 0) {
                    if ($fromdate != "all" && $todate == "all") {
                        $query_part.= ' AND `products`.created_at` >= "' . strtotime($fromdate) . '"';
                        $query_part_for_category.= ' AND `created_at` >= "' . strtotime($fromdate) . '"';
                    }
                    if ($todate != "all" && $fromdate == "all") {
                        $query_part.= ' AND `products`.`created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
                        $query_part_for_category.= ' AND `created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
                    }
                    if ($fromdate != "all" && $todate != "all") {
                        $query_part.= ' AND (`products`.created_at between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                        $query_part_for_category.= ' AND (created_at between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                    }
                    if ($product_status != "all") {
                        $query_part.= ' AND `products`.`product_status` = "' . $product_status . '"'; //1 - Enable 2 - Disable 3 - Deleted
                        $query_part_for_category.= ' AND `category_status` = "' . $product_status . '"'; //1 - Enable 2 - Disable 3 - Deleted
                        
                    }
                    if ($product_food_type != "all") {
                        $query_part.= ' AND `products`.`is_veg` = "' . $product_food_type . '"'; //	1 - Veg 2 - Non veg
                        
                    }
                    if ($search_key != "all") {
                        $query_part.= ' AND  (`products`.`product_name` LIKE "%' . $search_key . '%" OR  `products`.`product_name` LIKE "%' . $search_key . '%" OR  `products`.`short_desc` LIKE "%' . $search_key . '%" OR  `products`.`long_desc` LIKE "%' . $search_key . '%")';
                        $query_part_for_category.= ' AND  (`category_name` LIKE "%' . $search_key . '%" OR  `description` LIKE "%' . $search_key . '%")';
                    }
                    if ($restaurant_id != 0) {
                        $query_part.= ' AND `products`.`restaurant_id` = "' . $restaurant_id . '"';
                        $query_part_for_category.= ' AND `restaurant_id` = "' . $restaurant_id . '"';
                    }
                    if ($category_id != 0) {
                        $query_part.= ' AND `category_id` = "' . $category_id . '"';
                        //$query_part_for_category .= ' AND `id` = "'.$category_id.'"';
                        
                    } else {
                        $get_latest_cat_id = $this->Common->getData('categories', 'id', 'category_status != 3', '', '', 'id', 'DESC', '1');
                        if (!empty($get_latest_cat_id)) {
                            $query_part.= ' AND `category_id` = "' . $get_latest_cat_id[0]['id'] . '"';
                        }
                    }
                }
                //pagination  ---start----
                $common_query = "SELECT `products`.`id` as 'product_id',`products`.`product_name`,`products`.`price`,`products`.`product_image`,`products`.`short_desc`,`products`.`product_status`,`products`.`created_at`,`categories`.`category_status`,`categories`.`id` as 'category_id' FROM `products` INNER JOIN  `categories` ON `products`.`category_id` = `categories`.`id`	WHERE  `products`.`product_status` != 3  " . $query_part . "  ORDER BY `products`.`id` DESC ";
                $common_query_for_category = "SELECT  `id` as 'category_id',`category_name`,`category_status`,`category_image` FROM `categories` WHERE  `category_status` != 3  " . $query_part_for_category . "  ORDER BY `id` DESC ";
                $page = ($this->uri->segment(11)) ? ($this->uri->segment(11) - 1) : 0;
                #-------------For product list -----START--------
                if ($page > 0) {
                    $page_offset = $page * ADMIN_PER_PAGE_RECORDS;
                } else {
                    $page_offset = $page;
                }
                $query = "" . $common_query . " "; // LIMIT ".ADMIN_PER_PAGE_RECORDS." OFFSET ".$page_offset."
                $product_detail = $this->Common->custom_query($query, 'get');
                //checking product offline status ------START----------
                #if product offline status is available in table then we have to manage toggle according to given time and date
                #ex. hour - select 1 hour then if  it will not do online after 1 hour menully then toggle should be show active(online)  automatice after  1 hour
                #same as  for single date or multi date
                if (!empty($product_detail)) {
                    foreach ($product_detail as $key => $value) {
                        date_default_timezone_set('Asia/Singapore');
                        $offline_status_data = $this->Common->getData('products_offline', 'products_offline.offline_tag,products_offline.offline_value,products_offline.offline_from,products_offline.offline_to', 'products_offline.product_id = "' . $value['product_id'] . '"', '', '');
                        //print_r($offline_status_data);
                        //we need to check current time (between from and to date)for getting offline status becouse may be possible from and to date is complete but product didnot changed status (untill online , offline entery will be available) that's why
                        if (!empty($offline_status_data)) {
                            //echo time().'>='.$offline_status_data[0]['offline_from'].'===='.time().'<='.$offline_status_data[0]['offline_to'].'<br>';
                            if ((time() >= $offline_status_data[0]['offline_from']) && (time() <= $offline_status_data[0]['offline_to'])) {
                                $offline_status = "1"; //Offline
                                
                            } else {
                                $offline_status = "0"; //Online
                                
                            }
                            $product_detail[$key]['offline_status'] = $offline_status;
                            $product_detail[$key]['selected_offline_tag'] = $offline_status_data[0]['offline_tag'];
                        } else {
                            $product_detail[$key]['offline_status'] = "0"; //Offline
                            $product_detail[$key]['selected_offline_tag'] = 1;
                        }
                        //check category offline status for product toggle
                        $cat_offline_status_data = $this->Common->getData('categories_offline', 'categories_offline.offline_tag,categories_offline.offline_value,categories_offline.offline_from,categories_offline.offline_to', 'categories_offline.category_id = "' . $value['category_id'] . '"', '', '');
                        //print_r($offline_status_data);
                        //we need to check current time (between from and to date)for getting offline status becouse may be possible from and to date is complete but product didnot changed status (untill online , offline entery will be available) that's why
                        if (!empty($cat_offline_status_data)) {
                            //echo time().'>='.$cat_offline_status_data[0]['offline_from'].'===='.time().'<='.$cat_offline_status_data[0]['offline_to'].'<br>';
                            if ((time() >= $cat_offline_status_data[0]['offline_from']) && (time() <= $cat_offline_status_data[0]['offline_to'])) {
                                $offline_status = "1"; //Offline
                                
                            } else {
                                $offline_status = "0"; //Online
                                
                            }
                            $product_detail[$key]['cat_offline_status'] = $offline_status;
                            $product_detail[$key]['cat_selected_offline_tag'] = $cat_offline_status_data[0]['offline_tag']; //offline value exist for future day and selecteg tag
                            
                        } else {
                            $product_detail[$key]['cat_offline_status'] = "0"; //Offline
                            $product_detail[$key]['cat_selected_offline_tag'] = 1; //online for going offline
                            
                        }
                    }
                }
                //checking product offline status ------END----------
                $pageData['product_detail'] = $product_detail;
                $query = "" . $common_query . "";
                $total_records = count($this->Common->custom_query($query, "get"));
                #-----------For product list -----END--------
                #------------For Category list -----START--------
                $query_for_category = "" . $common_query_for_category . ""; // LIMIT ".ADMIN_PER_PAGE_RECORDS."
                $category_detail = $this->Common->custom_query($query_for_category, 'get');
                //checking product offline status ------START----------
                #if product offline status is available in table then we have to manage toggle according to given time and date
                #ex. hour - select 1 hour then if  it will not do online after 1 hour menully then toggle should be show active(online)  automatice after  1 hour
                #same as  for single date or multi date
                if (!empty($category_detail)) {
                    foreach ($category_detail as $key => $value) {
                        date_default_timezone_set('Asia/Singapore');
                        $offline_status_data = $this->Common->getData('categories_offline', 'categories_offline.offline_tag,categories_offline.offline_value,categories_offline.offline_from,categories_offline.offline_to', 'categories_offline.category_id = "' . $value['category_id'] . '"', '', '');
                        //print_r($offline_status_data);
                        //we need to check current time (between from and to date)for getting offline status becouse may be possible from and to date is complete but product didnot changed status (untill online , offline entery will be available) that's why
                        if (!empty($offline_status_data)) {
                            //echo time().'>='.$offline_status_data[0]['offline_from'].'===='.time().'<='.$offline_status_data[0]['offline_to'].'<br>';
                            if ((time() >= $offline_status_data[0]['offline_from']) && (time() <= $offline_status_data[0]['offline_to'])) {
                                $offline_status = "1"; //Offline
                                
                            } else {
                                $offline_status = "0"; //Online
                                
                            }
                            $category_detail[$key]['offline_status'] = $offline_status;
                            $category_detail[$key]['selected_offline_tag'] = $offline_status_data[0]['offline_tag']; //offline value exist for future day and selecteg tag
                            
                        } else {
                            $category_detail[$key]['offline_status'] = "0"; //Offline
                            $category_detail[$key]['selected_offline_tag'] = 1; //online for going offline
                            
                        }
                    }
                }
                //checking product offline status ------END----------
                $pageData['category_detail'] = $category_detail;
                //print_r($pageData['category_detail']);
                $query_for_category = "" . $common_query_for_category . "";
                $total_records_of_category = count($this->Common->custom_query($query_for_category, "get"));
                #---------For Category list -----END--------
                $url = base_url('admin/products/0/' . $fromdate . '/' . $todate . '/' . $product_status . '/' . $product_food_type . '/' . $search_key . '/' . $restaurant_id . '/' . $category_id); //by default table value is 0
                # Pass parameter to common pagination and create pagination function start
                $this->create_pagination($url, $total_records, ADMIN_PER_PAGE_RECORDS);
                $pageData['links'] = $this->pagination->create_links();
                $pageData['start'] = ($page * ADMIN_PER_PAGE_RECORDS) + 1;
                //pagination  ---End----
                //For resturant select box view only ------START------
                $resturant_query = "SELECT `id` as 'restaurant_id',`rest_name` FROM `restaurants` WHERE  `rest_status` != 3  ORDER BY `id` DESC";
                $pageData['resturant_details'] = $this->Common->custom_query($resturant_query, 'get');
                //For resturant select box view only------END------
                $data = array('title' => "Products", 'pageName' => "products");
                $this->createBreadcrumb($data);
                $pageData['urlPart'] = $this->getUrlPart();
                $pageData['pageTitle'] = 'Products';
                $pageData['pageName'] = 'products';
                if (empty($pageData['resturant_details'])) {
                    $selected_restaurant_id = 0;
                } else {
                    $selected_restaurant_id = $pageData['resturant_details'][0]['restaurant_id'];
                }
                // select box of category
                $category_select_box_data = $this->show_category_according_selected_restaurant($restaurant_id, $handle_mode = "1"); //handlemode = 1 = controller, 2 = js mode means call from where
                $pageData['category_select_box_data'] = $category_select_box_data;
                if (empty($category_select_box)) {
                    $selected_category_id = 0;
                } else {
                    $selected_category_id = $category_id;
                }
                //  if merchant is logged in then this condition will check and only merchant restaurant prodcuts will show if this blank that means super admin is logged in
                if ($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2) {
                    $restaurant_id = $this->logged_in_restaurant_id;
                }
                $category_status_data = $this->Common->getData('categories', 'category_status', 'category_status != 3 AND id = ' . $category_id . '');
                if (!empty($category_status_data)) {
                    $pageData['selected_category_id_status'] = $category_status_data[0]['category_status'];
                }
                $pageData['selected_restaurant_id'] = $restaurant_id;
                $pageData['selected_category_id'] = $category_id;
                $pageData['total_records_of_category'] = $total_records_of_category;
                $pageData['total_records_of_products'] = $total_records;
                $pageData['selected_restaurant_id_url'] = '0/all/all/all/all/all/' . $restaurant_id . '/' . $category_id;
                //For variant data Showing
                $pageData['variant_detail'] = $this->comman_get_variant_detail; // products controller - when page load then showing variant.  it ill be showing   modal //add_edit_variant_popup
                //variant_list controller - when add variant then table load by ajax. it ill be showing   modal //add_edit_variant_popup
                //variant_type_list controller - if any variant exists in that product then variant will show with checked variant other wise all data will be show with unchecked - it will be show modal //add_edit_variant_type_popup
                if ($table_data == "1" || $table_data == "2") {
                    // if any action tiriger like, delete or enable disable then is url excute by ajax
                    $this->load->view('product_and_category_list_table', $pageData);
                } else {
                    $this->load->view('masterpage', $pageData);
                }
            } else {
                $this->load->view('login');
            }
        }
        //Show Products --------------------------END---------------
        //Products CSV file export -----------------------START----------
        public function exportProductsCSV($table = '', $fromdate = 'all', $todate = 'all', $product_status = 'all', $search_key = 'all', $restaurant_id = 0, $category_id = 0, $export_type = '') {
            $query_part = "";
            $query_part_for_category = "";
            $query_part_for_products = "";
            if ($table != "" || $fromdate != "all" || $todate != "all" || $product_status != "all" || $search_key != "all" || $restaurant_id != 0 || $category_id != 0) {
                if ($fromdate != "all" && $todate == "all") {
                    $query_part.= ' AND `products`.`created_at` >= "' . strtotime($fromdate) . '"';
                    $query_part_for_category.= ' AND `created_at` >= "' . strtotime($fromdate) . '"';
                }
                if ($todate != "all" && $fromdate == "all") {
                    $query_part.= ' AND `products`.`created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
                    $query_part_for_category.= ' AND `created_at` <= "' . strtotime($fromdate) . ' 00:00:00"';
                }
                if ($fromdate != "all" && $todate != "all") {
                    $query_part.= ' AND (products.created_at between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                    $query_part_for_category.= ' AND (created_at between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                }
                if ($product_status != "all") {
                    $query_part.= ' AND `products`.`product_status` = "' . $product_status . '"';
                    $query_part_for_category.= ' AND `category_status` = "' . $product_status . '"'; //1 - Enable 2 - Disable 3 - Deleted
                    
                }
                if ($search_key != "all") {
                    $query_part.= ' AND  `products`.`product_name` LIKE "%' . $search_key . '%" OR  `products`.`short_desc` LIKE "%' . $search_key . '%" OR  `products`.`long_desc` LIKE "%' . $search_key . '%"  OR  `categories`.`category_name` LIKE "%' . $search_key . '%" OR  `categories`.`description` LIKE "%' . $search_key . '%"';
                    $query_part_for_category.= ' AND  (`category_name` LIKE "%' . $search_key . '%" OR  `description` LIKE "%' . $search_key . '%")';
                }
                if ($restaurant_id != 0) {
                    $query_part.= ' AND `products`.`restaurant_id` = "' . $restaurant_id . '"';
                    $query_part_for_category.= ' AND `restaurant_id` = "' . $restaurant_id . '"';
                }
                if ($export_type == 'product') {
                    $query_part_for_products.= ' AND `products`.`category_id` = "' . $category_id . '"';
                } else {
                    $query_part_for_products = "";
                }
            }
            if ($export_type == 'combined' || $export_type == 'product') {
                if ($category_id != 0) {
                    $query_part_for_category.= ' AND `id` = "' . $category_id . '"';
                } else {
                    $query_part_for_products = "";
                }
                $common_query = "SELECT `products`.`id` as 'product_id', `products`.`product_name`,`products`.`price`,`products`.`short_desc`,`products`.`product_status`,`products`.`long_desc`,`categories`.`id` as 'category_id',`products`.`created_at`,`products`.`created_at` as 'product_created_at',`products`.`min_qty`, `products`.`max_qty`, `products`.`is_veg`,`products`.`product_image`,`categories`.`category_name`,`categories`.`description`,`categories`.`category_status`,`categories`.`created_at` as 'category_created_at',`restaurants`.`id`as 'restaurant_id', `restaurants`.`rest_name` FROM `products` INNER JOIN  `categories` ON `products`. `category_id` = `categories`.`id`  INNER JOIN  `restaurants` ON `products`. `restaurant_id` = `restaurants`.`id` WHERE `categories`.`category_status` != 3  " . $query_part . $query_part_for_products . " AND `products`.`product_status` NOT IN(3) ORDER BY `products`.`id` DESC";
            } else if ($export_type == 'category') {
                $common_query = "SELECT `id` as 'category_id', `restaurant_id`, `category_name`, `description`, `category_image`, `category_status`, `created_at` as 'category_created_at' , `updated_at` FROM `categories` WHERE `category_status` != 3 " . $query_part_for_category . " ORDER BY `id` DESC";
            }
            // file name
            $filename = '' . $export_type . '_' . date('Ymd') . '.csv';
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
            header("Content-Type: application/csv;");
            $query = "" . $common_query . "";
            $ProductsData = $this->Common->custom_query($query, "get");
            // file creation
            $file = fopen('php://output', 'w');
            $header = array("Category Name", "Category Description", "Category Status", "Category Created Date", "Product Name", "Price", "Short Description", "Long  Description", "Minimum Quantity", "Is Veg", "Product Image", "Restaurant Name", "Product Status", "Product Created Date");
            fputcsv($file, $header);
            if (count($ProductsData) > 0) {
                foreach ($ProductsData as $key => $line) {
                    if ($export_type == 'product' || $export_type == 'combined') {
                        if ($line['product_status'] == "1") {
                            $product_status = 'Enable';
                        } else if ($line['product_status'] == "2") {
                            $product_status = 'Disable';
                        } else {
                            $product_status = "NA";
                        }
                        //for ---------registerd date-----
                        if ($line['product_created_at'] != '') {
                            $product_created_at = date('d/m/Y', $line['product_created_at']);
                        } else {
                            $product_created_at = 'NA';
                        }
                        //is veg 1 - Veg,  2 - Non veg
                        if ($line['is_veg'] == "1") {
                            $is_veg = 'Veg';
                        } else if ($line['is_veg'] == "2") {
                            $is_veg = 'Non veg';
                        } else {
                            $is_veg = "NA";
                        }
                        //product image
                        if ($line['product_image'] == "") {
                            $product_image = "";
                        } else {
                            $product_image = base_url() . $line['product_image'];
                        }
                    }
                    if ($export_type == 'combined' || $export_type == 'category') {
                        //check category status
                        if ($line['category_status'] == "1") {
                            $category_status = 'Enable';
                        } else if ($line['category_status'] == "2") {
                            $category_status = 'Disable';
                        } else {
                            $category_status = "NA";
                        }
                        //for ---------registerd date-----
                        if ($line['category_created_at'] != '') {
                            $category_created_at = date('d/m/Y', $line['category_created_at']);
                        } else {
                            $category_created_at = 'NA';
                        }
                    }
                    //echo $status;
                    if ($export_type == 'combined') {
                        $data_array = array($line['category_name'], $line['description'], $line['category_status'], $category_created_at, $line['product_name'], $line['price'], $line['short_desc'], $line['long_desc'], $line['min_qty'], $is_veg, $product_image, $line['rest_name'], $product_status, $product_created_at);
                    } else if ($export_type == 'product') {
                        $data_array = array($line['category_name'], $line['description'], '', '', $line['product_name'], $line['price'], $line['short_desc'], $line['long_desc'], $line['min_qty'], $is_veg, $product_image, $line['rest_name'], $product_status, $product_created_at);
                    } else if ($export_type == 'category') {
                        $data_array = array($line['category_name'], $line['description'], $line['category_status'], $category_created_at, '', '', '', '', '', '', '', '');
                    }
                    //print_r($data_array);
                    fputcsv($file, $data_array);
                }
            }
            fclose($file);
            exit;
        }
        //Products CSV file export -----------------------END----------
        // Get product offline data------ START----
        public function GET_ProductOffline_Data() {
            $product_id = $this->db->escape_str(trim($this->input->post('product_id')));
            $offline_status_data = $this->Common->getData('products_offline', 'products_offline.offline_tag,products_offline.offline_value,products_offline.offline_from,products_offline.offline_to', 'products_offline.product_id = "' . $product_id . '"', '', '');
            date_default_timezone_set('Asia/Singapore');
            if (!empty($offline_status_data)) {
                $offline_data['offline_tag'] = $offline_status_data[0]['offline_tag'];
                $offline_from = $offline_status_data[0]['offline_from'];
                $offline_data['offline_from'] = $offline_from = date("d-m-Y", $offline_from); // convert UNIX timestamp to PHP DateTime
                $offline_to = $offline_status_data[0]['offline_to'];
                $offline_data['offline_to'] = $offline_from = date("d-m-Y", $offline_to); // convert UNIX timestamp to PHP DateTime
                if (count($offline_status_data) > 0) {
                    echo json_encode($offline_data);
                } else {
                    echo 0;
                }
            } else {
                echo 0;
            }
        }
        // Get product offline data------ END------
        //Enable /Disable(active/inactive) toggle of Product------ START------
        public function active_inactive_product() {
            $offline_type = !empty($_POST['offline_type']) ? $this->db->escape_str($_POST['offline_type']) : '';
            #offline_type	1 - DISABLE 2 - ENABLE
            $offline_tag = !empty($_POST['offline_tag']) ? $this->db->escape_str($_POST['offline_tag']) : '';
            #DB_offline_tag	1 - Hour 2 - Day 3 - Multiple days
            $offline_value = !empty($_POST['offline_value']) ? $this->db->escape_str($_POST['offline_value']) : '';
            $product_id = !empty($_POST['product_id']) ? $this->db->escape_str($_POST['product_id']) : '';
            $rest_id = !empty($_POST['restaurant_id']) ? $this->db->escape_str($_POST['restaurant_id']) : '';
            if ($offline_type == 1) # DISABLE
            {
                if ($offline_tag == '') {
                    echo 2; //offline_tag_missing
                    
                } else if ($offline_value == '') {
                    echo 3; //offline_value_missing
                    
                } else if ($offline_type == '') {
                    echo 4; //offline_type_missing
                    
                } else if ($rest_id == '') {
                    echo 6; //restaurant_id_missing
                    
                } else {
                    if ($offline_tag == 1) # HOURS : If value is 3 that means DISABLE FOR NEXT 3 HOURS
                    {
                        # Value will always be passed in HOURS:MINUTES Format. Ex 4 hours and 30 minutes (4:30) and 4 hours (4:00)
                        if (strpos($offline_value, ":") !== false) # We are keeping : to separate the hours and minutes value
                        {
                            # That means minutes also exists (Ex 4 hours and 30 minutes 4:30)
                            $hm = explode(":", $offline_value);
                            $hours = $hm[0];
                            $minutes = $hm[1];
                            $offline_from = time();
                            $to_add_h = $hours * (60 * 60); # Convert the hours into seconds.
                            $to_add_m = $minutes * 60; # Convert the minutes into seconds.
                            $offline_till = $offline_from + $to_add_h + $to_add_m;
                        } else # However it won't be used but just kept it here.
                        {
                            # Only Hours value exists (Ex Next 4 hours)
                            $hours = $offline_value;
                            $offline_from = time();
                            $to_add = $hours * (60 * 60); # Convert the hours into seconds.
                            $offline_till = $offline_from + $to_add;
                        }
                        $final_offline_till = $offline_till;
                        $final_offline_value = $offline_from;
                    } else if ($offline_tag == 2) # A day i.e. Single timstamp value of selected date
                    {
                        $offline_from = strtotime($offline_value);
                        # For offline_till here mobile team should pass timestamp for selected date. Ex today's date is 22/03/2021 and selected date is 25/03/2021 that means restaurant will be offline on 25/03/2021 so mobile team will pass timstamp for 25/03/2021 00:00:00 (1616630400) and we will set to date of 25/03/2021 as 25/03/2021 12:59:59 that we will add 24 hours to this date.
                        $to_add = 24 * (60 * 60); # Convert the hours into seconds.
                        $offline_till = $offline_from + $to_add;
                        $final_offline_till = $offline_till;
                        $final_offline_value = $offline_from;
                    } else if ($offline_tag == 3) # Multiple days i.e. From and To , two timestamp will be passed from mobile team
                    {
                        # Ex : Today's date is 22nd march and Restaurant is going offline from 23rd to 25th of march so how we will proceed with it? Simple we have two timstamps one is from 23/03/2021 00:00:00 to 25/03/2021 11:59:59 so this type of timestamp must be provided from the mobile team COMMA Separated and we are going to simply explode and update them in DB
                        $exp = explode(",", $offline_value);
                        /*$offline_from = $exp[0];
                         $offline_till = $exp[1];*/
                        $offline_from_date = $exp[0];
                        $offline_from = strtotime($offline_from_date);
                        $offline_till_date = $exp[1];
                        $offline_till = strtotime($offline_till_date);
                        $offline_till = strtotime('+1440 minutes', $offline_till); //24 hours of till date
                        $final_offline_till = strtotime('-1 minutes', $offline_till); //less 1 minut for till date 11:49 becouse from 00:00 start next day
                        $final_offline_value = $offline_from . ',' . $final_offline_till;
                    } else {
                        echo 7; //invalid_offline_tag
                        
                    }
                    # Get category id of the product
                    $category = $this->Common->getData('products', 'id,category_id', 'id = "' . $product_id . '"');
                    $category_id = $category[0]['category_id'];
                    $insert_product['rest_id'] = $rest_id;
                    $insert_product['category_id'] = $category_id;
                    $insert_product['product_id'] = $product_id;
                    $insert_product['offline_tag'] = $offline_tag;
                    $insert_product['offline_value'] = $final_offline_value;
                    $insert_product['offline_from'] = $offline_from;
                    $insert_product['offline_to'] = $final_offline_till;
                    $insert_product['created_at'] = time();
                    $insert_product['updated_at'] = time();
                    //check if product is already exist in offline table then we will do only update it
                    $check = $this->Common->getData('products_offline', 'id', 'category_id = "' . $category_id . '" AND product_id = "' . $product_id . '"');
                    if (count($check) > 0) # That means this product already exists
                    {
                        $insert_update_status = $this->Common->updateData('products_offline', $insert_product, 'id = "' . $check[0]['id'] . '"');
                    } else {
                        # insert the data
                        $insert_product['created_at'] = time();
                        $insert_update_status = $this->Common->insertData('products_offline', $insert_product);
                    }
                    if ($insert_update_status > 0) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                    //---------------------------------
                    
                }
            } else if ($offline_type == 2) # TO ENABLE
            {
                $delete_status = $this->Common->deleteData('products_offline', 'rest_id = "' . $rest_id . '" AND product_id = "' . $product_id . '"');
                # If any single product is coming back online then we need to Enable the category also.
                # First check whether category belongs to this product is also in category_offlie table? If yes then delete else no need
                # But we can directly fire delete query
                # Get category id
                $cat_id = $this->Common->getData('products', 'category_id', 'id = "' . $product_id . '"');
                $category_id = $cat_id[0]['category_id'];
                $this->Common->deleteData('categories_offline', 'rest_id = "' . $rest_id . '" AND category_id = "' . $category_id . '"');
                if ($delete_status > 0) {
                    echo 1;
                } else {
                    echo 0;
                }
            }
        }
        //Enable /Disable(active/inactive) toggle of Product------ END------
        // Get category offline data------ START----
        public function GET_CategoryOffline_Data() {
            $category_id = $this->db->escape_str(trim($this->input->post('category_id')));
            $offline_status_data = $this->Common->getData('categories_offline', 'categories_offline.offline_tag,categories_offline.offline_value,categories_offline.offline_from,categories_offline.offline_to', 'categories_offline.category_id = "' . $category_id . '"', '', '');
            date_default_timezone_set('Asia/Singapore');
            if (!empty($offline_status_data)) {
                $offline_data['offline_tag'] = $offline_status_data[0]['offline_tag'];
                $offline_from = $offline_status_data[0]['offline_from'];
                $offline_data['offline_from'] = $offline_from = date("d-m-Y", $offline_from); // convert UNIX timestamp to PHP DateTime
                $offline_to = $offline_status_data[0]['offline_to'];
                $offline_data['offline_to'] = $offline_from = date("d-m-Y", $offline_to); // convert UNIX timestamp to PHP DateTime
                if (count($offline_status_data) > 0) {
                    echo json_encode($offline_data);
                } else {
                    echo 0;
                }
            } else {
                echo 0;
            }
        }
        // Get category offline data------ END------
        //Enable /Disable(active/inactive) toggle of Category------ START------
        public function active_inactive_category() {
            $offline_tag = $this->db->escape_str(trim($this->input->post('offline_tag')));
            $offline_value = $this->db->escape_str(trim($this->input->post('offline_value')));
            $offline_type = $this->db->escape_str(trim($this->input->post('offline_type')));
            $category_id = $this->db->escape_str(trim($this->input->post('category_id')));
            $rest_id = $this->db->escape_str(trim($this->input->post('restaurant_id')));
            if ($offline_type == 1) # DISABLE
            {
                if ($offline_tag == '') {
                    echo 2; //offline_tag_missing
                    
                } else if ($offline_value == '') {
                    echo 3; //offline_value_missing
                    
                } else if ($offline_type == '') {
                    echo 4; //offline_type_missing
                    
                } else if ($category_id == '') {
                    echo 5; //category_id_missing
                    
                } else if ($rest_id == '') {
                    echo 6; //restaurant_id_missing
                    
                } else {
                    if ($offline_tag == 1) # HOURS : If value is 3 that means DISABLE FOR NEXT 3 HOURS
                    {
                        # Value will always be passed in HOURS:MINUTES Format. Ex 4 hours and 30 minutes (4:30) and 4 hours (4:00)
                        if (strpos($offline_value, ":") !== false) # We are keeping : to separate the hours and minutes value
                        {
                            # That means minutes also exists (Ex 4 hours and 30 minutes 4:30)
                            $hm = explode(":", $offline_value);
                            $hours = $hm[0];
                            $minutes = $hm[1];
                            $offline_from = time();
                            $to_add_h = $hours * (60 * 60); # Convert the hours into seconds.
                            $to_add_m = $minutes * 60; # Convert the minutes into seconds.
                            $offline_till = $offline_from + $to_add_h + $to_add_m;
                        } else # However it won't be used but just kept it here.
                        {
                            # Only Hours value exists (Ex Next 4 hours)
                            $hours = $offline_value;
                            $offline_from = time();
                            $to_add = $hours * (60 * 60); # Convert the hours into seconds.
                            $offline_till = $offline_from + $to_add;
                        }
                        $final_offline_till = $offline_till;
                        $final_offline_value = $offline_from;
                    } else if ($offline_tag == 2) # A day i.e. Single timstamp value of selected date
                    {
                        $offline_from = strtotime($offline_value);
                        # For offline_till here Merchant/ Super admin should pass timestamp for selected date. Ex today's date is 22/03/2021 and selected date is 25/03/2021 that means restaurant will be offline on 25/03/2021 so Merchant/ Super admin will pass timstamp for 25/03/2021 00:00:00 (1616630400) and we will set to date of 25/03/2021 as 25/03/2021 12:59:59 that we will add 24 hours to this date.
                        $to_add = 24 * (60 * 60); # Convert the hours into seconds.
                        $offline_till = $offline_from + $to_add;
                        $final_offline_till = $offline_till;
                        $final_offline_value = $offline_from;
                    } else if ($offline_tag == 3) # Multiple days i.e. From and To , two timestamp will be passed from Merchant/ Super admin
                    {
                        # Ex : Today's date is 22nd march and Restaurant is going offline from 23rd to 25th of march so how we will proceed with it? Simple we have two timstamps one is from 23/03/2021 00:00:00 to 25/03/2021 11:59:59 so this type of timestamp must be provided from the Merchant/ Super admin COMMA Separated and we are going to simply explode and update them in DB
                        $exp = explode(",", $offline_value);
                        $offline_from_date = $exp[0];
                        $offline_from = strtotime($offline_from_date);
                        $offline_till_date = $exp[1];
                        $offline_till = strtotime($offline_till_date);
                        $offline_till = strtotime('+1440 minutes', $offline_till); //24 hours of till date
                        $final_offline_till = strtotime('-1 minutes', $offline_till); //less 1 minut for till date 11:49 becouse from 00:00 start next day
                        $final_offline_value = $offline_from . ',' . $final_offline_till;
                    } else {
                        echo 7; //invalid_offline_tag
                        
                    }
                    $insert_array['rest_id'] = $rest_id;
                    $insert_array['category_id'] = $category_id;
                    $insert_array['offline_tag'] = $offline_tag;
                    $insert_array['offline_value'] = $final_offline_value;
                    $insert_array['offline_from'] = $offline_from;
                    $insert_array['offline_to'] = $final_offline_till;
                    $insert_array['updated_at'] = time();
                    //print_r($insert_array);
                    //check if category is already exist in offline table then we will do only update it
                    $check = $this->Common->getData('categories_offline', 'id', 'rest_id = "' . $rest_id . '" AND category_id = "' . $category_id . '"');
                    if (count($check) > 0) # That means this product already exists
                    {
                        $insert_update_status = $this->Common->updateData('categories_offline', $insert_array, 'id = "' . $check[0]['id'] . '"');
                    } else {
                        # insert the data
                        $insert_array['created_at'] = time();
                        $insert_update_status = $this->Common->insertData('categories_offline', $insert_array);
                    }
                    # Now if category is going offline then at the same time products associated with that category will also go offline for the same time.
                    # Get all the products of given category id and restaurant id.
                    $products = $this->Common->getData('products', 'id', 'category_id = "' . $category_id . '" AND restaurant_id = "' . $rest_id . '"');
                    # Here we need to apply a check for stopping multiple entries of same product in below scenario.
                    /*
                    # First we disable product id 5 of category id 1 so for product id 5 there will be an entry to ONLY products_offline table as per enable_disable_product_post API.
                    Now we disable category id 1. So this category Id 1 already contain product id 5 which is already disabled in first step and its entry already exists in products_offline table. So if we again  make same entry then it will be WRONG.
                    So first we have to check whether any entry with this cat id and this prod id already exists then we need to update the ROW ELSE insert the row
                    */
                    $product_arr['rest_id'] = $rest_id;
                    $product_arr['category_id'] = $category_id;
                    $product_arr['offline_tag'] = $offline_tag;
                    $product_arr['offline_value'] = $final_offline_value;
                    $product_arr['offline_from'] = $offline_from;
                    $product_arr['offline_to'] = $final_offline_till;
                    $product_arr['created_at'] = time();
                    $product_arr['updated_at'] = time();
                    # When category is going offline then all products will also go offline for same period of time so need to work for products
                    foreach ($products as $product) {
                        $check = $this->Common->getData('products_offline', 'id', 'category_id = "' . $category_id . '" AND product_id = "' . $product['id'] . '"');
                        if (count($check) > 0) # That means this product already exists
                        {
                            # update it
                            $insert_update_status = $this->Common->updateData('products_offline', $product_arr, 'id = "' . $check[0]['id'] . '"');
                        } else {
                            # insert the data
                            $product_insert_arr = $product_arr;
                            $product_insert_arr['product_id'] = $product['id'];
                            $insert_update_status = $this->Common->insertData('products_offline', $product_insert_arr);
                        }
                    }
                    if ($insert_update_status > 0) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                }
            } else if ($offline_type == 2) # TO ENABLE
            {
                # Delete from categories offline table
                $cat_offline_delete_status = $this->Common->deleteData('categories_offline', 'rest_id = "' . $rest_id . '" AND category_id = "' . $category_id . '"');
                if ($cat_offline_delete_status > 0) {
                    # Delte from products_offline table
                    $products_offline_delete_status = $this->Common->deleteData('products_offline', 'rest_id = "' . $rest_id . '" AND category_id = "' . $category_id . '"');
                    if ($products_offline_delete_status > 0) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                }
            }
        }
        //Enable /Disable(active/inactive) toggle of Category------ END------
        // Product Delete -------------------- START----------
        public function delete_product() {
            $product_id = $this->db->escape_str(trim($this->input->post('product_id')));
            $update_array = ['product_status' => 3, // 1 - Enable, 2 - Disable, 3 - Deleted
            'updated_at' => time() ];
            # update data in promotion table
            $update_status = $this->Common->updateData('products', $update_array, "id = " . $product_id);
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
        //Product Delete ------ END------
        // category Delete -------------------- START----------
        public function delete_category() {
            $category_id = $this->db->escape_str(trim($this->input->post('category_id')));
            $update_array = ['category_status' => 3, // 1 - Enable, 2 - Disable, 3 - Deleted
            'updated_at' => time() ];
            # update data in categories table
            $update_status = $this->Common->updateData('categories', $update_array, "id = " . $category_id);
            if ($update_status > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
        //category Delete ------ END------
        //Import Csv in to Products Table----------START----------
        public function uploadData() {
            //Import code reffrence -https://webprepration.com/import-excel-file-mysql-using-codeigniter/
            if ($this->input->post('submit')) {
                // check import type = category, product or combined wise
                $import_type = $this->db->escape_str(trim($this->input->post('import_type')));
                $selected_restaurant_id = $this->db->escape_str(trim($this->input->post('selected_restaurant_id')));
                if ($selected_restaurant_id == "" && $this->role == 2) { // merchant login
                    $selected_restaurant_id = $this->logged_in_restaurant_id;
                } else if ($this->role == 1) { // if admin login
                    $selected_restaurant_id = $this->db->escape_str(trim($this->input->post('selected_restaurant_id')));
                } else {
                    $selected_restaurant_id = "";
                }
                $selected_category_id = $this->db->escape_str(trim($this->input->post('selected_category_id')));
                $last_url_before_import = $this->db->escape_str(trim($this->input->post('last_url_before_import')));
                $category_insert_id = "";
                //import file-------start-----------
                $path = 'assets/uploads/';
                require_once APPPATH . "/third_party/PHPExcel/Classes/PHPExcel.php";
                $config['upload_path'] = $path;
                $config['allowed_types'] = 'xlsx|xls|csv';
                $config['remove_spaces'] = TRUE;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('uploadFile')) {
                    $error = array('error' => $this->upload->display_errors());
                } else {
                    $data = array('upload_data' => $this->upload->data());
                }
                if (empty($error)) {
                    if (!empty($data['upload_data']['file_name'])) {
                        $import_xls_file = $data['upload_data']['file_name'];
                    } else {
                        $import_xls_file = 0;
                    }
                    $inputFileName = $path . $import_xls_file;
                    try {
                        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                        $objPHPExcel = $objReader->load($inputFileName);
                        $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                        $flag = true;
                        $i = 0;
                        $file_name_array = explode('_', $import_xls_file);
                        if ($import_type == 'category') {
                            if ($selected_restaurant_id != "") {
                                if ($file_name_array[0] == 'category.csv') {
                                    $file_name_array[0] = 'category';
                                }
                                if ($file_name_array[0] == 'category') { // in category tab wrongly uplaod combine or product  file then it will be not run
                                    foreach ($allDataInSheet as $value) {
                                        if ($flag) {
                                            $flag = false;
                                            continue;
                                        }
                                        $category_name = $value['A'];
                                        if ($value['B'] != "") {
                                            $category_description = $value['B'];
                                        } else {
                                            $category_description = ' ';
                                        }
                                        if ($value['C'] != "") {
                                            $category_image_path = $value['C'];
                                        } else {
                                            $category_image_path = "";
                                        }
                                        if ($value['D'] != "" && ($value['D'] == 1 || $value['D'] == 2)) {
                                            $category_status = $value['D'];
                                        } else {
                                            $category_status = 1;
                                        }
                                        // check if product name is already exist then we will only update it othwise will be new insert
                                        // check if category name is already exist then we will only update it othwise will be new insert
                                        $get_category_if_exist_by_name_query = 'SELECT `id` FROM `categories` WHERE `category_name` = "' . $category_name . '"  AND `restaurant_id` = ' . $selected_restaurant_id . '';
                                        $category_if_exist_data = $this->Common->custom_query($get_category_if_exist_by_name_query, 'get');
                                        $cat_data = ['category_name' => $category_name, 'description' => $category_description, 'category_image' => $category_image_path, 'category_status' => $category_status, 'updated_at' => time() ];
                                        if (!empty($category_if_exist_data)) {
                                            // will be overight and update
                                            $exist_category_id = $category_if_exist_data[0]['id'];
                                            $this->Common->updateData('categories', $cat_data, "id = '" . $exist_category_id . "' AND  restaurant_id = '" . $selected_restaurant_id . "'");
                                        } else {
                                            //will be insert
                                            $cat_data['restaurant_id'] = $selected_restaurant_id;
                                            $cat_data['created_at'] = time();
                                            $this->Common->insertData('categories', $cat_data);
                                        }
                                        $result = true;
                                        $result_error = "";
                                    } //foreach
                                    
                                } else {
                                    $result = "";
                                    $result_error = "Make sure you have selected a correct tab (Category, product or combine) for upload related csv file!";
                                    $category_insert_id = "";
                                }
                            } else {
                                $result = "";
                                $result_error = "Make sure you have selected a restaurant!";
                                $category_insert_id = "";
                            }
                        }
                        if ($import_type == 'product') {
                            if ($selected_category_id != "" && $selected_restaurant_id != "") {
                                if ($file_name_array[0] == 'product.csv') {
                                    $file_name_array[0] = 'product';
                                }
                                if ($file_name_array[0] == 'product') { // in product tab wrongly uplaod combine or product  file then it will be not run
                                    foreach ($allDataInSheet as $value) {
                                        if ($flag) {
                                            $flag = false;
                                            continue;
                                        }
                                        $product_name = $value['A'];
                                        $product_price = $value['B'];
                                        if ($value['C'] != "") {
                                            $product_offer_price = $value['C'];
                                        } else {
                                            $product_offer_price = "";
                                        }
                                        if ($value['D'] != "") {
                                            $product_short_discription = $value['D'];
                                        } else {
                                            $product_short_discription = "";
                                        }
                                        if ($value['E'] != "") {
                                            $product_long_discription = $value['E'];
                                        } else {
                                            $product_long_discription = "";
                                        }
                                        if ($value['F'] != "") {
                                            $minimum_quantity = $value['F'];
                                        } else {
                                            $minimum_quantity = "";
                                        }
                                        if ($value['G'] != "") {
                                            $food_type = $value['G'];
                                        } else {
                                            $food_type = 2;
                                        }
                                        if ($value['H'] != "") {
                                            $product_image_path = $value['H'];
                                        } else {
                                            $product_image_path = "";
                                        }
                                        if ($value['I'] != "" && ($value['I'] == 1 || $value['I'] == 2)) {
                                            $product_status = $value['I'];
                                        } else {
                                            $product_status = 1;
                                        }
                                        if ($file_name_array[0] == 'product') { // in product tab wrongly uplaod combine or category  file then it will be not run
                                            // check if product name is already exist then we will only update it othwise will be new insert
                                            $get_product_if_exist_by_name_query = 'SELECT `id` FROM `products` WHERE `product_name` = "' . $product_name . '"  AND `restaurant_id` = ' . $selected_restaurant_id . ' AND product_status != 3 ';
                                            $product_if_exist_data = $this->Common->custom_query($get_product_if_exist_by_name_query, 'get');
                                            $data = ['restaurant_id' => $selected_restaurant_id, 'category_id' => $selected_category_id, 'product_name' => $product_name, 'is_veg' => $food_type, //	1 - Veg 2 - Non veg
                                            'price' => $product_price, 'offer_price' => $product_offer_price, 'min_qty' => $minimum_quantity, //MINIMUM QUANTITY TO ORDER. Default is 1
                                            'short_desc' => $product_short_discription, 'long_desc' => $product_long_discription, 'product_image' => $product_image_path, 'product_status' => $product_status, 'updated_at' => time() ];
                                            if (!empty($product_if_exist_data)) {
                                                // will be overight and update
                                                $exist_product_id = $product_if_exist_data[0]['id'];
                                                $this->Common->updateData('products', $data, "id = '" . $exist_product_id . "' AND  restaurant_id = " . $selected_restaurant_id . "");
                                            } else {
                                                //will be insert
                                                $data['created_at'] = time();
                                                $this->Common->insertData('products', $data);
                                            }
                                            $result = true;
                                            $result_error = "";
                                        } else {
                                            $result = "";
                                            $result_error = "Make sure you have selected a correct tab (Category, product or combine) for upload related csv file!";
                                            $category_insert_id = "";
                                        }
                                    } //foreach end
                                    
                                } else {
                                    $result = "";
                                    $result_error = "Make sure you have selected a correct tab (Category, product or combine) for upload related csv file!";
                                    $category_insert_id = "";
                                } // file name check
                                
                            } else {
                                $result = "";
                                $result_error = "Make sure you have selected a restaurant or category!";
                                $category_insert_id = "";
                            }
                        }
                        if ($import_type == 'combined') {
                            if ($selected_restaurant_id != "") {
                                if ($file_name_array[0] == 'combined.csv') {
                                    $file_name_array[0] = 'combined';
                                }
                                if ($file_name_array[0] == 'combined') { // in combined tab wrongly uplaod category  or product  file then it will be not run
                                    // some data insert  in to category table
                                    foreach ($allDataInSheet as $value) {
                                        if ($flag) {
                                            $flag = false;
                                            continue;
                                        }
                                        //category data
                                        $category_name = $value['A'];
                                        if ($value['B'] != "") {
                                            $category_description = $value['B'];
                                        } else {
                                            $category_description = ' ';
                                        }
                                        if ($value['C'] != "") {
                                            $category_image_path = $value['C'];
                                        } else {
                                            $category_image_path = "";
                                        }
                                        if ($value['D'] != "" && ($value['D'] == 1 || $value['D'] == 2)) {
                                            $category_status = $value['D'];
                                        } else {
                                            $category_status = 1;
                                        }
                                        // check if category name is already exist then we will only update it othwise will be new insert
                                        $get_category_if_exist_by_name_query = 'SELECT `id` FROM `categories` WHERE `category_name` = "' . $category_name . '" AND `restaurant_id` = ' . $selected_restaurant_id . ' AND category_status!= 3';
                                        $category_if_exist_data = $this->Common->custom_query($get_category_if_exist_by_name_query, 'get');
                                        $cat_data = ['category_name' => $category_name, 'description' => $category_description, 'category_image' => $category_image_path, 'category_status' => $category_status, 'updated_at' => time() ];
                                        //print_r( $cat_data);
                                        if (!empty($category_if_exist_data)) {
                                            $exist_category_id = $category_if_exist_data[0]['id'];
                                            // will be overight and update
                                            //UPDATE CATEGORY
                                            $this->Common->updateData('categories', $cat_data, "id = '" . $exist_category_id . "'");
                                        } else {
                                            $exist_category_id = 0;
                                            //will be insert
                                            $cat_data['restaurant_id'] = $selected_restaurant_id;
                                            $cat_data['created_at'] = time();
                                            $this->Common->insertData('categories', $cat_data);
                                            $last_category_insert_id = $this->db->insert_id();
                                        }
                                        //product data
                                        $product_name = $value['E'];
                                        $product_price = $value['F'];
                                        if ($value['G'] != "") {
                                            $product_offer_price = $value['G'];
                                        } else {
                                            $product_offer_price = "";
                                        }
                                        if ($value['H'] != "") {
                                            $product_short_discription = $value['H'];
                                        } else {
                                            $product_short_discription = "";
                                        }
                                        if ($value['I'] != "") {
                                            $product_long_discription = $value['I'];
                                        } else {
                                            $product_long_discription = "";
                                        }
                                        if ($value['J'] != "") {
                                            $minimum_quantity = $value['J'];
                                        } else {
                                            $minimum_quantity = "";
                                        }
                                        if ($value['K'] != "") {
                                            $food_type = $value['K'];
                                        } else {
                                            $food_type = 2;
                                        }
                                        if ($value['L'] != "") {
                                            $product_image_path = $value['L'];
                                        } else {
                                            $product_image_path = "";
                                        }
                                        if ($value['M'] != "" && ($value['M'] == 1 || $value['M'] == 2)) {
                                            $product_status = $value['M'];
                                        } else {
                                            $product_status = 1;
                                        }
                                        // check if product name is already exist then we will only update it othwise will be new insert
                                        $get_product_if_exist_by_name_query = 'SELECT `id` FROM `products` WHERE `product_name` = "' . $product_name . '"  AND `restaurant_id` = ' . $selected_restaurant_id . ' AND product_status != 3 ';
                                        $product_if_exist_data = $this->Common->custom_query($get_product_if_exist_by_name_query, 'get');
                                        if ($exist_category_id != 0) {
                                            $selected_category_id = $exist_category_id;
                                        } else {
                                            $selected_category_id = $last_category_insert_id;
                                        }
                                        $data = ['restaurant_id' => $selected_restaurant_id, 'category_id' => $selected_category_id, 'product_name' => $product_name, 'is_veg' => $food_type, //	1 - Veg 2 - Non veg
                                        'price' => $product_price, 'offer_price' => $product_offer_price, 'min_qty' => $minimum_quantity, //MINIMUM QUANTITY TO ORDER. Default is 1
                                        'short_desc' => $product_short_discription, 'long_desc' => $product_long_discription, 'product_image' => $product_image_path, 'product_status' => $product_status, 'updated_at' => time() ];
                                        //echo '<pre>';
                                        /* print_r($data);*/
                                        //echo  $exist_category_id ;
                                        if (!empty($product_if_exist_data)) {
                                            $exist_product_id = $product_if_exist_data[0]['id'];
                                            // will be overight and update
                                            $this->Common->updateData('products', $data, "id = '" . $exist_product_id . "' AND  restaurant_id = " . $selected_restaurant_id . "");
                                        } else {
                                            //will be insert
                                            $data['created_at'] = time();
                                            $this->Common->insertData('products', $data);
                                        }
                                    }
                                    $result = true;
                                    $result_error = "";
                                } else {
                                    $result = "";
                                    $result_error = "Make sure you have selected a correct tab (Category, product or combine) for upload related csv file!";
                                    $category_insert_id = "";
                                } // file name check
                                
                            } else {
                                $result = "";
                                $result_error = "Make sure you have selected a restaurant!";
                                $category_insert_id = "";
                            }
                        }
                        if ($result) {
                            $this->session->set_flashdata('success', 'Your Product Data Successfully Import!');
                            echo "Imported successfully";
                        } else {
                            $this->session->set_flashdata('error', $result_error);
                            echo "ERROR !";
                        }
                    }
                    catch(Exception $e) {
                        die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
                    }
                } else {
                    $this->session->set_flashdata('error', 'Internal Server Error!');
                    echo $error['error'];
                }
                //import file-------end-----------
                
            }
            $files = glob('assets/uploads/*'); // get all file names
            foreach ($files as $file) { // iterate files
                if (is_file($file)) {
                    unlink($file); // delete file
                    
                }
            }
            if ($category_insert_id != "") {
                $new_url_array_after_category_insert = preg_split('/' . $selected_restaurant_id . '/', $last_url_before_import);
                $old_category_url_parameter = end($new_url_array_after_category_insert);
                $new_url_after_category_insert = $new_url_array_after_category_insert[0] . $selected_restaurant_id . '/' . $category_insert_id;
                $last_url_after_import = $new_url_after_category_insert;
            } else {
                $last_url_after_import = $last_url_before_import;
            }
            redirect($last_url_after_import); // preg_split also do in custom.js becouse of category id (res id / cat id) cat id in last of the url
            
        }
        //Import Csv in to Products Table----------END----------
        //Add and edit category deatils---------------------------START-----------
        public function add_edit_category_details($type = '') {
            $category_name = $this->db->escape_str(trim(ucfirst($this->input->post('category_name'))));
            $category_description = $this->db->escape_str(trim(ucfirst($this->input->post('category_description'))));
            $category_id = $this->db->escape_str(trim(ucfirst($this->input->post('category_id'))));
            $selected_restaurant_id = $this->db->escape_str(trim(ucfirst($this->input->post('selected_restaurant_id'))));
            if ($category_id != "" && $type == 2) {
                $query_part = "AND id != " . $category_id . "";
            } else {
                $query_part = "";
            }
            if ($this->id) {
                if ($type == 1) {
                    //add mode  ------------------------start--------------
                    if (!empty($category_name) && !empty($selected_restaurant_id)) {
                        // check id category name is already is exist for restarunat then we will do only update other wise insert
                        $get_category_if_exist_by_name_query = 'SELECT `id` FROM `categories` WHERE `category_name` = "' . $category_name . '" AND `restaurant_id` = ' . $selected_restaurant_id . ' AND category_status!= 3';
                        $category_if_exist_data = $this->Common->custom_query($get_category_if_exist_by_name_query, 'get');
                        if (!empty($_FILES['category_image']['name'])) {
                            // if category image uploaded
                            $exp = explode(".", $_FILES['category_image']['name']);
                            $ext = end($exp);
                            $st1 = substr(date('ymd'), 0, 3);
                            $st2 = $st1 . rand(1, 100000);
                            $fileName = $st2 . time() . date('ymd') . "." . $ext;
                            $original_image_path = 'assets/images/categories/';
                            $resize_image_path = 'assets/images/categories/resized_category_pic/';
                            /* Image upload  */
                            $config['upload_path'] = $original_image_path;
                            $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                            $config['file_name'] = $fileName;
                            $this->upload->initialize($config);
                            if (!$this->upload->do_upload('category_image')) {
                                $error_msg = $this->upload->display_errors();
                                $message = strip_tags($error_msg);
                                $this->session->set_flashdata('error', $message);
                                echo 2; //upload path must have permission or path should be correct
                                
                            } else {
                                $data = ['restaurant_id' => $selected_restaurant_id, 'category_name' => $category_name, 'description' => $category_description, 'category_image' => $resize_image_path . $fileName, 'updated_at' => time() ];
                                if (!empty($category_if_exist_data)) {
                                    $exist_category_id = $category_if_exist_data[0]['id'];
                                    $latest_category_id = $this->Common->updateData('categories', $data, "id = '" . $exist_category_id . "' AND  restaurant_id = '" . $selected_restaurant_id . "'");
                                } else {
                                    $data['created_at'] = time();
                                    $latest_category_id = $this->Common->insertData('categories', $data);
                                }
                                # Resize only if NOT SVG
                                if ($ext !== 'svg') {
                                    /*Image resize function starts here*/
                                    $source_image = $original_image_path . $fileName;
                                    $new_image = $resize_image_path . $fileName;
                                    $width = PROFILE_IMAGE_RESIZE_WIDTH;
                                    $height = PROFILE_IMAGE_RESIZE_WIDTH;
                                    # Call this function to rezise the image and place in a new path
                                    $this->image_resize($source_image, $new_image, $width, $height);
                                    /*Image resize function ends here*/
                                } else {
                                    /* Image upload */
                                    $config['upload_path'] = $resize_image_path;
                                    $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                                    $config['file_name'] = $fileName;
                                    $this->upload->initialize($config);
                                    if (!$this->upload->do_upload('category_image')) {
                                        $error_msg = $this->upload->display_errors();
                                        $message = strip_tags($error_msg);
                                        $this->session->set_flashdata('error', $message);
                                    }
                                }
                                if ($latest_category_id > 0) {
                                    $this->session->set_flashdata('success', 'category added successfully');
                                    echo 1;
                                } else {
                                    echo 0;
                                }
                            }
                        } else {
                            // if category image not uploaded
                            $data = ['restaurant_id' => $selected_restaurant_id, 'category_name' => $category_name, 'description' => $category_description, 'category_image' => "", 'updated_at' => time() ];
                            if (!empty($category_if_exist_data)) {
                                $exist_category_id = $category_if_exist_data[0]['id'];
                                $latest_category_id = $this->Common->updateData('categories', $data, "id = '" . $exist_category_id . "' AND  restaurant_id = '" . $selected_restaurant_id . "'");
                            } else {
                                $data['created_at'] = time();
                                $latest_category_id = $this->Common->insertData('categories', $data);
                            }
                            if ($latest_category_id > 0) {
                                $this->session->set_flashdata('success', 'category added successfully');
                                echo 1;
                            } else {
                                echo 0;
                            }
                        }
                    } else {
                        echo 4;
                    }
                    //add mode  ------------------------end--------------
                    // Edit Mode -----------------------Start-----------
                    
                } else if ($type == 2 && $category_id != "") { //edit mode
                    $category_image = $this->db->escape_str(trim($this->input->post('category_image')));
                    $get_category_data = $this->Common->getData("categories", "restaurant_id,category_name,description,category_image", "id = " . $category_id . "");
                    $target = array(array('restaurant_id' => $selected_restaurant_id, 'category_name' => $category_name, 'description' => $category_description, 'category_image' => $category_image));
                    if (!empty($category_name) && !empty($selected_restaurant_id) && !empty($_FILES)) {
                        #delete previous profile image------- START --------
                        $get_previous_category_pic = $get_category_data[0]['category_image'];
                        if (!empty($get_previous_category_pic && file_exists($get_previous_category_pic))) {
                            unlink($get_previous_category_pic);
                        }
                        //upload image
                        $exp = explode(".", $_FILES['category_image']['name']);
                        $ext = end($exp);
                        $st1 = substr(date('ymd'), 0, 3);
                        $st2 = $st1 . rand(1, 100000);
                        $fileName = $st2 . time() . date('ymd') . "." . $ext;
                        $original_image_path = 'assets/images/categories/';
                        $resize_image_path = 'assets/images/categories/resized_category_pic/';
                        /* Image upload  */
                        $config['upload_path'] = $original_image_path;
                        $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                        $config['file_name'] = $fileName;
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('category_image')) {
                            $error_msg = $this->upload->display_errors();
                            $message = strip_tags($error_msg);
                            $this->session->set_flashdata('error', $message);
                        } else {
                            $update_array = ['restaurant_id' => $selected_restaurant_id, 'category_name' => $category_name, 'description' => $category_description, 'category_image' => $resize_image_path . $fileName, 'updated_at' => time() ];
                            $this->Common->updateData('categories', $update_array, "id = " . $category_id . '');
                            # Resize only if NOT SVG
                            if ($ext !== 'svg') {
                                /*Image resize function starts here*/
                                $source_image = $original_image_path . $fileName;
                                $new_image = $resize_image_path . $fileName;
                                $width = PROFILE_IMAGE_RESIZE_WIDTH;
                                $height = PROFILE_IMAGE_RESIZE_WIDTH;
                                # Call this function to rezise the image and place in a new path
                                $this->image_resize($source_image, $new_image, $width, $height);
                                /*Image resize function ends here*/
                            } else {
                                /* Image upload */
                                $config['upload_path'] = $resize_image_path;
                                $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                                $config['file_name'] = $fileName;
                                $this->upload->initialize($config);
                                if (!$this->upload->do_upload('category_image')) {
                                    $error_msg = $this->upload->display_errors();
                                    $message = strip_tags($error_msg);
                                    $this->session->set_flashdata('error', $message);
                                }
                            }
                            //echo 1;
                            $update_status = true;
                        }
                        // if exist image only data change
                        
                    } elseif ($get_category_data == $target) {
                        //echo 0; // noting data edit
                        $update_status = false;
                        // else if new image and data upload
                        
                    } else if (!empty($category_name) && !empty($selected_restaurant_id)) {
                        if ($category_image != '') {
                            // exsit image
                            $update_array = ['restaurant_id' => $selected_restaurant_id, 'category_name' => $category_name, 'description' => $category_description, 'category_image' => $category_image, 'updated_at' => time() ];
                        } else if ($category_image == '') {
                            //exit image removed
                            $update_array = ['restaurant_id' => $selected_restaurant_id, 'category_name' => $category_name, 'description' => $category_description, 'category_image' => '', 'updated_at' => time() ];
                        }
                        $this->Common->updateData('categories', $update_array, "id = " . $category_id . '');
                        // echo 1;
                        $update_status = true;
                    }
                    if ($update_status == true) {
                        // Updated successfully
                        echo 1;
                    } else {
                        echo 0;
                    }
                } else {
                    echo 5;
                }
            } else {
                $this->load->view('admin/login');
            }
        }
        //Add and edit category deatils---------------------------END-----------
        // selected merchat get -------------------START-------------
        public function selected_category_detail() {
            $category_id = $this->db->escape_str(trim($this->input->post('category_id')));
            $category_data = $this->Common->getData('categories', ' category_name,description,category_image', 'id = ' . $category_id . '  AND category_status != 3');
            if (count($category_data) > 0) {
                echo json_encode($category_data[0]);
            } else {
                echo 0;
            }
        }
        // selected merchat get --------------------END------------------
        // selected merchat get -------------------START-------------
        public function selected_product_detail() {
            $product_id = $this->db->escape_str(trim($this->input->post('product_id')));
            $product_data = $this->Common->getData('products', 'product_name, price,	offer_price,min_qty,max_qty,is_veg,short_desc,long_desc,product_image,restaurant_id,category_id', 'id = ' . $product_id . '  AND product_status != 3');
            if (count($product_data) > 0) {
                echo json_encode($product_data[0]);
            } else {
                echo 0;
            }
        }
        // selected merchat get --------------------END------------------
        //Add and edit product deatils---------------------------START-----------
        public function add_edit_product_details($type = '') {
            $product_name = $this->db->escape_str(trim(ucfirst($this->input->post('product_name'))));
            $food_type = $this->db->escape_str(trim(ucfirst($this->input->post('food_type'))));
            $product_price = $this->db->escape_str(trim(ucfirst($this->input->post('product_price'))));
            $product_offer_price = $this->db->escape_str(trim(ucfirst($this->input->post('product_offer_price'))));
            $minimum_quantity = $this->db->escape_str(trim(ucfirst($this->input->post('minimum_quantity'))));
            $product_short_discription = $this->db->escape_str(trim(ucfirst($this->input->post('product_short_discription'))));
            $product_long_discription = $this->db->escape_str(trim(ucfirst($this->input->post('product_long_discription'))));
            $product_id = $this->db->escape_str(trim(ucfirst($this->input->post('product_id'))));
            $selected_restaurant_id = $this->db->escape_str(trim(ucfirst($this->input->post('selected_restaurant_id'))));
            $selected_category_id = $this->db->escape_str(trim(ucfirst($this->input->post('selected_category_id'))));
            if ($product_id != "" && $type == 2) {
                $query_part = "AND id != " . $product_id . "";
            } else {
                $query_part = "";
            }
            $update_status = false;
            if ($this->id) {
                if ($type == 1) {
                    //add mode  ------------------------start--------------
                    if (!empty($product_name) && !empty($food_type) && !empty($product_price) && !empty($selected_restaurant_id) && $selected_category_id != "") {
                        // if product name is already is exist then it will be update for restaruant other wise it will be insert
                        $get_product_if_exist_by_name_query = 'SELECT `id` FROM `products` WHERE `product_name` = "' . $product_name . '"  AND `restaurant_id` = ' . $selected_restaurant_id . ' AND product_status != 3 ';
                        $product_if_exist_data = $this->Common->custom_query($get_product_if_exist_by_name_query, 'get');
                        if (!empty($_FILES['product_image']['name'])) {
                            //if image uploaded
                            $exp = explode(".", $_FILES['product_image']['name']);
                            $ext = end($exp);
                            $st1 = substr(date('ymd'), 0, 3);
                            $st2 = $st1 . rand(1, 100000);
                            $fileName = $st2 . time() . date('ymd') . "." . $ext;
                            $original_image_path = 'assets/images/products/';
                            $resize_image_path = 'assets/images/products/resized_product_pic/';
                            /* Image upload  */
                            $config['upload_path'] = $original_image_path;
                            $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                            $config['file_name'] = $fileName;
                            $this->upload->initialize($config);
                            if (!$this->upload->do_upload('product_image')) {
                                $error_msg = $this->upload->display_errors();
                                $message = strip_tags($error_msg);
                                $this->session->set_flashdata('error', $message);
                                echo 2; //upload path must have permission or path should be correct
                                
                            } else {
                                $data = ['restaurant_id' => $selected_restaurant_id, 'category_id' => $selected_category_id, 'product_name' => $product_name, 'is_veg' => $food_type, //	1 - Veg 2 - Non veg
                                'price' => $product_price, 'offer_price' => $product_offer_price, 'min_qty' => $minimum_quantity, //MINIMUM QUANTITY TO ORDER. Default is 1
                                'short_desc' => $product_short_discription, 'long_desc' => $product_long_discription, 'product_image' => $resize_image_path . $fileName, 'updated_at' => time() ];
                                if (!empty($product_if_exist_data)) {
                                    // will be overight and update
                                    $exist_product_id = $product_if_exist_data[0]['id'];
                                    $insert_status = $this->Common->updateData('products', $data, "id = '" . $exist_product_id . "' AND  restaurant_id = " . $selected_restaurant_id . "");
                                } else {
                                    //will be insert
                                    $data['created_at'] = time();
                                    $insert_status = $this->Common->insertData('products', $data);
                                }
                                # Resize only if NOT SVG
                                if ($ext !== 'svg') {
                                    /*Image resize function starts here*/
                                    $source_image = $original_image_path . $fileName;
                                    $new_image = $resize_image_path . $fileName;
                                    $width = PROFILE_IMAGE_RESIZE_WIDTH;
                                    $height = PROFILE_IMAGE_RESIZE_WIDTH;
                                    # Call this function to rezise the image and place in a new path
                                    $this->image_resize($source_image, $new_image, $width, $height);
                                    /*Image resize function ends here*/
                                } else {
                                    /* Image upload */
                                    $config['upload_path'] = $resize_image_path;
                                    $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                                    $config['file_name'] = $fileName;
                                    $this->upload->initialize($config);
                                    if (!$this->upload->do_upload('product_image')) {
                                        $error_msg = $this->upload->display_errors();
                                        $message = strip_tags($error_msg);
                                        $this->session->set_flashdata('error', $message);
                                    }
                                }
                                if ($insert_status > 0) {
                                    $this->session->set_flashdata('success', 'Product added successfully');
                                    echo 1;
                                } else {
                                    echo 0;
                                }
                            }
                        } else {
                            //image not uploaded
                            $data = ['restaurant_id' => $selected_restaurant_id, 'category_id' => $selected_category_id, 'product_name' => $product_name, 'is_veg' => $food_type, //	1 - Veg 2 - Non veg
                            'price' => $product_price, 'offer_price' => $product_offer_price, 'min_qty' => $minimum_quantity, //MINIMUM QUANTITY TO ORDER. Default is 1
                            'short_desc' => $product_short_discription, 'long_desc' => $product_long_discription, 'product_image' => '', 'updated_at' => time() ];
                            if (!empty($product_if_exist_data)) {
                                // will be overight and update
                                $exist_product_id = $product_if_exist_data[0]['id'];
                                $insert_status = $this->Common->updateData('products', $data, "id = '" . $exist_product_id . "' AND  restaurant_id = " . $selected_restaurant_id . "");
                            } else {
                                //will be insert
                                $data['created_at'] = time();
                                $insert_status = $this->Common->insertData('products', $data);
                            }
                            if ($insert_status > 0) {
                                $this->session->set_flashdata('success', 'Product added successfully');
                                echo 1;
                            } else {
                                echo 0;
                            }
                        }
                    } else {
                        echo 4;
                    }
                    //add mode  ------------------------end--------------
                    // Edit Mode -----------------------Start-----------
                    
                } else if ($type == 2 && $product_id != "") { //edit mode
                    $product_image = $this->db->escape_str(trim($this->input->post('product_image')));
                    $get_product_data = $this->Common->getData('products', 'product_name, price,offer_price,min_qty,is_veg,short_desc,long_desc,product_image,restaurant_id,category_id', 'id = ' . $product_id . '  AND product_status != 3');
                    if ($minimum_quantity == "") {
                        $minimum_quantity = '0';
                    }
                    $target = array(array('product_name' => $product_name, 'price' => $product_price, 'offer_price' => $product_offer_price, 'min_qty' => $minimum_quantity, 'is_veg' => $food_type, 'short_desc' => $product_short_discription, 'long_desc' => $product_long_discription, 'product_image' => $product_image, 'restaurant_id' => $selected_restaurant_id, 'category_id' => $selected_category_id));
                    if (!empty($product_name) && !empty($food_type) && !empty($product_price) && !empty($selected_restaurant_id) && !empty($selected_category_id) && !empty($_FILES)) {
                        #delete previous profile image------- START --------
                        $get_previous_product_pic = $get_product_data[0]['product_image'];
                        if (!empty($get_previous_product_pic && file_exists($get_previous_product_pic))) {
                            unlink($get_previous_product_pic);
                        }
                        //upload image
                        $exp = explode(".", $_FILES['product_image']['name']);
                        $ext = end($exp);
                        $st1 = substr(date('ymd'), 0, 3);
                        $st2 = $st1 . rand(1, 100000);
                        $fileName = $st2 . time() . date('ymd') . "." . $ext;
                        $original_image_path = 'assets/images/products/';
                        $resize_image_path = 'assets/images/products/resized_product_pic/';
                        /* Image upload  */
                        $config['upload_path'] = $original_image_path;
                        $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                        $config['file_name'] = $fileName;
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('product_image')) {
                            $error_msg = $this->upload->display_errors();
                            $message = strip_tags($error_msg);
                            $this->session->set_flashdata('error', $message);
                        } else {
                            $update_array = ['restaurant_id' => $selected_restaurant_id, 'category_id' => $selected_category_id, 'product_name' => $product_name, 'is_veg' => $food_type, //	1 - Veg 2 - Non veg
                            'price' => $product_price, 'offer_price' => $product_offer_price, 'min_qty' => $minimum_quantity, //MINIMUM QUANTITY TO ORDER. Default is 1
                            'short_desc' => $product_short_discription, 'long_desc' => $product_long_discription, 'product_image' => $resize_image_path . $fileName, 'updated_at' => time() ];
                            $this->Common->updateData('products', $update_array, "id = " . $product_id . '');
                            # Resize only if NOT SVG
                            if ($ext !== 'svg') {
                                /*Image resize function starts here*/
                                $source_image = $original_image_path . $fileName;
                                $new_image = $resize_image_path . $fileName;
                                $width = PROFILE_IMAGE_RESIZE_WIDTH;
                                $height = PROFILE_IMAGE_RESIZE_WIDTH;
                                # Call this function to rezise the image and place in a new path
                                $this->image_resize($source_image, $new_image, $width, $height);
                                /*Image resize function ends here*/
                            } else {
                                /* Image upload */
                                $config['upload_path'] = $resize_image_path;
                                $config['allowed_types'] = 'jpg|png|jpeg|bmp|svg';
                                $config['file_name'] = $fileName;
                                $this->upload->initialize($config);
                                if (!$this->upload->do_upload('product_image')) {
                                    $error_msg = $this->upload->display_errors();
                                    $message = strip_tags($error_msg);
                                    $this->session->set_flashdata('error', $message);
                                }
                            }
                            //echo 1;
                            $update_status = true;
                        }
                        // if exist image only data change
                        
                    } elseif ($get_product_data == $target) {
                        //echo 0; // noting data edit
                        $update_status = false;
                        // else if new image and data upload
                        
                    } elseif (!empty($product_name) && !empty($food_type) && !empty($product_price) && !empty($selected_restaurant_id) && !empty($selected_category_id)) {
                        if ($product_image != "") {
                            $update_array = ['restaurant_id' => $selected_restaurant_id, 'category_id' => $selected_category_id, 'product_name' => $product_name, 'is_veg' => $food_type, //	1 - Veg 2 - Non veg
                            'price' => $product_price, 'offer_price' => $product_offer_price, 'min_qty' => $minimum_quantity, //MINIMUM QUANTITY TO ORDER. Default is 1
                            'short_desc' => $product_short_discription, 'long_desc' => $product_long_discription, 'product_image' => $product_image, 'updated_at' => time() ];
                        } else {
                            $update_array = ['restaurant_id' => $selected_restaurant_id, 'category_id' => $selected_category_id, 'product_name' => $product_name, 'is_veg' => $food_type, //	1 - Veg 2 - Non veg
                            'price' => $product_price, 'offer_price' => $product_offer_price, 'min_qty' => $minimum_quantity, //MINIMUM QUANTITY TO ORDER. Default is 1
                            'short_desc' => $product_short_discription, 'long_desc' => $product_long_discription, 'product_image' => '', 'updated_at' => time() ];
                        }
                        $this->Common->updateData('products', $update_array, "id = " . $product_id . '');
                        // echo 1;
                        $update_status = true;
                    }
                    /* print_r($get_product_data);
                     print_r($target);*/
                    if ($update_status == true) {
                        // Updated successfully
                        echo 1;
                    } else {
                        echo 0;
                    }
                } else {
                    echo 5; // type not found
                    
                }
            } else {
                $this->load->view('admin/login');
            }
        }
        //Add and edit product deatils---------------------------END-----------
        # Set restaurant best seller or trending
        # This funcion is used to mark a restaurant as best seller and trending
        public function set_restro_seller_trending($checked, $type) {
            if ($this->id && $this->role == 1) # Only admin can do this
            {
                $restaurant_id = $this->input->post('restaurant_id');
                if ($type == 1) # best seller
                {
                    $this->Common->updateData('restaurants', array('is_best_seller' => $checked), 'id = "' . $restaurant_id . '"');
                } else if ($type == 2) # is_trending
                {
                    $this->Common->updateData('restaurants', array('is_trending' => $checked), 'id = "' . $restaurant_id . '"');
                }
            } else {
                $this->load->view('login');
            }
        }
        # This function is used to get that how much cashback customer will get as per the Super admin settings and customer order amount
        public function getOrderCashbackValue($sub_total) {
            $cashback = $this->Common->getData('settings', '*', 'name = "order_cashback"');
            $cashback_type = $cashback[0]['type'];
            $cashback_value = $cashback[0]['value'];
            #  1 : Flat 2 percent
            if ($cashback_type == 1) {
                return $cashback_value;
            } else {
                $ab = ($sub_total * $cashback_value) / 100;
                return $ab;
            }
        }
        //Update CMS Functionality--------------START-----------
        #for  Terms and Condtion
        #for  Privacy Policy
        public function Update_CMS() {
            if ($this->id && $this->role == 1) # Only admin can do this
            {
                $page_name = $this->db->escape_str(trim($this->input->post('page_name')));
                $page_value = $this->db->escape_str(trim($this->input->post('page_value')));
                $page_key = $this->db->escape_str(trim($this->input->post('page_key')));
                $page_primary_id = $this->db->escape_str(trim($this->input->post('page_primary_id')));
                if ($page_name != "" && $page_key != "" && $page_primary_id != "" && $page_value != "") {
                    $update_array = ['page_name' => $this->input->post('page_name'), 'page_value' => $this->input->post('page_value'), 'updated_at' => time(), ];
                    $update_status = $this->Common->updateData('cms', $update_array, 'page_key = "' . $page_key . '" AND id = "' . $page_primary_id . '"');
                    if ($update_status > 0) {
                        $this->session->set_flashdata('success', 'CMS updated successfully');
                    } else {
                        $this->session->set_flashdata('error', 'Internal Server Error !');
                    }
                    header("location:" . base_url('admin/cms'));
                } else {
                    if ($page_value == '') {
                        $this->session->set_flashdata('error', 'Please fill page content !');
                    } else if ($page_name == '') {
                        $this->session->set_flashdata('error', 'Please fill page name! ');
                    } else {
                        $this->session->set_flashdata('error', 'Something went wrong !');
                    }
                    header("location:" . base_url('admin/edit_cms/' . $page_primary_id . ''));
                }
            } else {
                header('location:' . base_url('admin'));
            }
        }
        //Update CMS Functionality--------------END------------
        //Admin/ Merchant Update Password ----------START---------
        #check old password
        public function CheckOldPassword() {
            $old_password = $this->db->escape_str(trim($this->input->post('old_password')));
            $get_old_password = $this->Common->getData('users', 'password', 'id ="' . $this->id . '" AND role = ' . $this->role . ''); //Super Admin role - 1, 2 - merchant
            $exist_password = $get_old_password[0]['password'];
            // match enter old password with exist old password
            if (!$this->bcrypt->check_password($old_password, $exist_password)) {
                echo 0;
            } else {
                echo 1;
            }
        }
        //for get first name
        public function split_name($name) {
            $name = trim($name);
            $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
            $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));
            return array($first_name, $last_name);
        }
        public function UpdatePassword() {
            if ($this->id && ($this->role == 1 || $this->role == 2)) { // role = (1 - admin, 2 - merchant){
                $new_password = $this->db->escape_str(trim($this->input->post('new_password')));
                if ($new_password != "") {
                    $password = $this->bcrypt->hash_password(trim($new_password));
                    $update_array = ['password' => $password, 'updated_at' => time(), ];
                    /*password update*/
                    $update_status = $this->Common->updateData("users", $update_array, "id = " . $this->id . ' AND role = ' . $this->role . '');
                    if ($update_status > 0) {
                        // getting email for send email
                        $get_user_data = $this->Common->getData('users', 'fullname,email', 'id ="' . $this->id . '" AND role = ' . $this->role . ''); //Super Admin role - 1, 2 - cutomer service
                        //FOR mail footer --------
                        $social_urls = $this->get_social_urls();
                        $mail_data['facebook_url'] = $social_urls['facebook'];
                        $mail_data['google_url'] = $social_urls['google'];
                        $mail_data['insta_url'] = $social_urls['insta'];
                        $mail_data['website_url'] = $social_urls['website'];
                        $name = $this->split_name($get_user_data[0]['fullname']);
                        //print_r($name);
                        $mail_data['first_name'] = $name[0];
                        $email = $get_user_data[0]['email'];
                        $subject = APP_NAME . " Account Password Reset";
                        //load template view
                        $message = $this->load->view('email/reset_password_success', $mail_data, TRUE);
                        //send mail
                        $mail_success_status = send_mail($email, $subject, $message);
                        /*mail send data code end */
                        $data['message'] = 'Password reset successfully';
                        if ($mail_success_status == 1) {
                            //echo 'success';
                            $this->session->set_flashdata('success', 'Password updated successfully !');
                            header("location:" . base_url('admin/profile'));
                        } else {
                            //echo 'success';
                            $this->session->set_flashdata('success', 'Password updated successfully but Email not sent !');
                            header("location:" . base_url('admin/profile'));
                        }
                    }
                }
            } else {
                header('location:' . base_url('admin'));
            }
        }
        //Admin/ Merchant  Update Password ----------END---------
        //
        public function Order_Invoice($order_id = "") {
            if ($this->id && ($this->role == 1 || $this->role == 2)) // role = (1 - admin, 2 - merchant)
            {
                // $pageData['order_data'] = $this->Common->getData('orders','orders.*,users.number_id as user_number_id,users.fullname as customer_name,users.email,users.mobile,restaurants.rest_name,restaurants.admin_id,restaurants.rest_pin_address,restaurants.rest_postal_code,restaurants.rest_unit_number,restaurants.delivery_time,rest_accept_types.name as order_type_name,merchant_categories.category_name as business_category_name','orders.order_status != 9 AND orders.id = '.$order_id .'',array('users','restaurants','rest_accept_types','merchant_categories'),array('orders.user_id = users.id','orders.restaurant_id = restaurants.id','orders.order_type = rest_accept_types.id','orders.business_category = merchant_categories.id'),'orders.id');//order status - 	//  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
                //getting restaruant mobile number
                $pageData['order_data'] = $this->Common->getData('orders', 'transactions.wallet_debited_value,transactions.total_amount_paid,orders.*,users.number_id as user_number_id,users.fullname as customer_name,users.email,users.mobile,restaurants.rest_name,restaurants.admin_id,restaurants.rest_pin_address,restaurants.rest_postal_code,restaurants.rest_unit_number,restaurants.delivery_time,rest_accept_types.name as order_type_name,merchant_categories.category_name as business_category_name', 'orders.order_status != 9 AND orders.id = ' . $order_id . '', array('users', 'restaurants', 'rest_accept_types', 'merchant_categories', 'transactions'), array('orders.user_id = users.id', 'orders.restaurant_id = restaurants.id', 'orders.order_type = rest_accept_types.id', 'orders.business_category = merchant_categories.id', 'orders.id = transactions.order_id'), 'orders.id');
                //getting restaruant mobile number
                $pageData['rest_contact_number'] = $this->Common->getData('users', '	mobile', 'id = ' . $pageData['order_data'][0]['admin_id'] . '');
                // getting Ordered Products -------------START------------
                $pageData['order_product_details'] = $this->comman_show_orderd_products_function($order_id, '', '1'); //order id,selected_product_id (Dont need to give on first time view), load mode check
                // getting Ordered Products -------------END------------
                $this->load->view('invoice', $pageData);
            } else {
                header('location:' . base_url('admin'));
            }
        }
        public function browser_notification() {
            /** Google URL with which notifications will be pushed */
            $url = "https://fcm.googleapis.com/fcm/send";
            /** 
             * Firebase Console -> Select Projects From Top Naviagation
             *      -> Left Side bar -> Project Overview -> Project Settings
             *      -> General -> Scroll Down and you will be able to see KEYS
             */
            $subscription_key = "key=AAAA2_hNI7U:APA91bE0SzhrQI_-XTDRodGvsJ_PBx8dOHTI-J4WLBi_75KSZW1E0SOycOffQVvsXXYT8dwIhu_vuq9yvTY2VrH0D4Lk7LBqNwRRrfVK5n-fKNVhDhWc9ewTq-ozKsxyX2Dz_VJbBF8b";
            /** We will need to set the following header to make request work */
            $request_headers = array("Authorization:" . $subscription_key, "Content-Type: application/json");
            /** Data that will be shown when push notifications get triggered */
            $postRequest = ["notification" => ["title" => "New Article", "body" => "Firebase Cloud Messaging for Web using JavaScript", "icon" => "https://c.disquscdn.com/uploads/users/34896/2802/avatar92.jpg", "click_action" => "http://localhost:8888/test"],
            /** Customer Token, As of now I got from console. You might need to pull from database */
            "to" => "fQocFMkyMaJpkRXwk9x1tL:APA91bEVLZ2t1L4MEEzSD9ywO9n6P_JruQylDLctH-vaLcBSATNvlplVN-YJa_cDpNzZ-4XhYWaMIKzM_asozonWziWcEHWhTxJ6EGhYrD7YWppip2FtBbyVcQDaEbz077hcU-G9GN_L"];
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
            echo '<pre>';
            print_r($json);
        }
        # HEAR ABOUT SECTION START
        public function hear_about() {
            if ($this->id && $this->role == 1) // role = (1 - admin, 2 - merchant)
            {
                $pageData['hear_about_us'] = $this->Common->getData('hear_about_us', '*', 'status != 3', '', '', 'id', 'DESC');
                $data = array('title' => "Hear about", 'pageName' => "Hear about");
                $this->createBreadcrumb($data);
                $pageData['urlPart'] = $this->getUrlPart();
                $pageData['pageTitle'] = "Hear about";
                $pageData['pageName'] = 'hear_about';
                $this->load->view('masterpage', $pageData);
            } else {
                header('location:' . base_url('admin'));
            }
        }
        # Below function is used to either addd or edit the source name for hear about
        public function add_edit_hear_about($action) {
            if ($this->id && $this->role == 1) // role = (1 - admin, 2 - merchant)
            {
                if ($action == 1) # add
                {
                    $insert_array = ['name' => $this->input->post('hear_about_txt'), 'created_at' => time(), 'updated_at' => time(), 'status' => 1, # Enabled
                    ];
                    $this->Common->insertData('hear_about_us', $insert_array);
                } else # updated
                {
                    $update_array = ['name' => $this->input->post('hear_about_txt'), 'updated_at' => time(), ];
                    $this->Common->updateData('hear_about_us', $update_array, 'id = "' . $this->input->post('hear_about_txt_id') . '"');
                }
                header('location:' . base_url('admin/hear_about'));
            } else {
                header('location:' . base_url('admin'));
            }
        }
        public function delete_hear_about() {
            if ($this->id && $this->role == 1) // role = (1 - admin, 2 - merchant)
            {
                $update_array = ['status' => 3, # Deleted
                'updated_at' => time(), ];
                $this->Common->updateData('hear_about_us', $update_array, 'id = "' . $this->input->post('id') . '"');
                header('location:' . base_url('admin/hear_about'));
            } else {
                header('location:' . base_url('admin'));
            }
        }
        public function enable_disable_hear_abt_status() {
            if ($this->id && $this->role == 1) {
                if ($this->input->post('status') == 2) # If disabled
                {
                    $status = 1; # make it enable
                    
                } else {
                    $status = 2;
                }
                $update_array = ['status' => $status, 'updated_at' => time(), ];
                $this->Common->updateData('hear_about_us', $update_array, 'id = "' . $this->input->post('hear_abt_id') . '"');
            } else {
                header('location:' . base_url('admin'));
            }
        }
        # This function is used to update current user's device token in Database User table
        public function update_device_token() {
            if ($this->id) {
                $update_array = ['device_token' => $this->input->post('token'), ];
                $this->Common->updateData('users', $update_array, 'id = "' . $this->id . '"');
            } else {
                header('location:' . base_url('admin'));
            }
        }
        public function wallet_history($user_id, $fromdate = 'all', $todate = 'all', $type = 'all') {
            # $type = 1 : Money added 2 : Cashback
            // $admin_per_page = 5;
            if ($this->id) {
                $pageData['fromdate'] = $fromdate;
                $pageData['todate'] = $todate;
                $pageData['type'] = $type;
                $fromDateNew = strtotime($fromdate . ' 00:00:00');
                $toDateNew = strtotime($todate . ' 24:00:00');
                $query_part = "";
                $cust_name = $this->Common->getData('users', 'fullname', 'id = "' . $user_id . '"');
                $cust_name = $cust_name[0]['fullname'];
                if ($fromdate != 'all' || $todate != 'all' || $type != 'all') {
                    if ($fromdate != "all" && $todate != "all") {
                        $query_part.= ' AND (`wallet`.`created_at` between ' . $fromDateNew . ' and ' . $toDateNew . ') ';
                    }
                }
                if ($type != "all") {
                    $query_part.= ' AND `wallet`.`type` = "' . $type . '"';
                }
                # All wallet transaction list
                $common_query = "SELECT * FROM `wallet` WHERE `user_id` = " . $user_id . " " . $query_part . " ORDER BY  `id` DESC";
                $page = ($this->uri->segment(7)) ? ($this->uri->segment(7) - 1) : 0;
                if ($page > 0) {
                    // $page_offset = $page * ADMIN_PER_PAGE_RECORDS;
                    $page_offset = $page * ADMIN_PER_PAGE_RECORDS;
                } else {
                    $page_offset = $page;
                }
                $query = "" . $common_query . " LIMIT " . $page * ADMIN_PER_PAGE_RECORDS . " , " . ADMIN_PER_PAGE_RECORDS . " ";
                $pageData['all_transactions'] = $this->Common->custom_query($query, 'get');
                if (!empty($pageData['all_transactions'])) {
                    foreach ($pageData['all_transactions'] as $key => $value) {
                        if ($value['order_id'] == 0) {
                            $pageData['all_transactions'][$key]['display_name'] = $cust_name;
                        } else {
                            $name = $this->Common->getData('orders', 'restaurants.rest_name', 'orders.id = "' . $value['order_id'] . '"', array('restaurants'), array('restaurants.id = orders.restaurant_id'));
                            $pageData['all_transactions'][$key]['display_name'] = $name[0]['rest_name'];
                        }
                    }
                }
                $query = "" . $common_query . "";
                $total_records = count($this->Common->custom_query($query, "get"));
                $url = base_url('admin/wallet_history/' . $user_id . '/' . $fromdate . '/' . $todate . '/' . $type . '/');
                # Pass parameter to common pagination and create pagination function start
                $this->create_pagination($url, $total_records, ADMIN_PER_PAGE_RECORDS);
                $pageData['links'] = $this->pagination->create_links();
                $pageData['start'] = ($page * ADMIN_PER_PAGE_RECORDS) + 1;
                $wallet_balance = $this->get_wallet_balance($user_id);
                $wallet_balance = str_replace(",", "", $wallet_balance[0]['wallet_balance']);
                // $wallet_balance = (int)$wallet_balance[0]['wallet_balance'];
                $total_balance = number_format($wallet_balance, 2, '.', '');
                if ($total_balance == '') {
                    $total_balance = '0.00';
                }
                $pageData['wallet_balance'] = $total_balance;
                # 1 - Cashback 2 - Money Added 3 - debited
                # Now check how much total of MONEY ADDED i.e. type 2
                $result = "SELECT FORMAT((sum(COALESCE(credited, 0))-sum(COALESCE(debited, 0))),2) AS wallet_balance FROM wallet WHERE IF (valid_till != 0 , UNIX_TIMESTAMP() <= valid_till AND user_id = $user_id , user_id = $user_id) AND type = 2";
                $money_added = $this->Common->custom_query($result, "get");
                if ($money_added[0]['wallet_balance'] == null) {
                    $pageData['total_money_added'] = '0.00';
                } else {
                    $money_added = str_replace(",", "", $money_added[0]['wallet_balance']);
                    $pageData['total_money_added'] = number_format($money_added, 2, '.', '');
                }
                # CASHBACK
                $result = "SELECT FORMAT((sum(COALESCE(credited, 0))-sum(COALESCE(debited, 0))),2) AS wallet_balance FROM wallet WHERE IF (valid_till != 0 , UNIX_TIMESTAMP() <= valid_till AND user_id = $user_id , user_id = $user_id) AND type = 1";
                $cashback = $this->Common->custom_query($result, "get");
                if ($cashback[0]['wallet_balance'] === null) {
                    $pageData['total_cashback'] = '0.00';
                } else {
                    $pageData['total_cashback'] = number_format($cashback[0]['wallet_balance'], 2, '.', '');
                }
                $data = array('title' => "Wallet History", 'pageName' => "Wallet History");
                $pageData['user_id'] = $user_id;
                $this->createBreadcrumb($data);
                $pageData['urlPart'] = $this->getUrlPart();
                $pageData['pageTitle'] = "Wallet History";
                $pageData['pageName'] = 'wallet_history';
                $this->load->view('masterpage', $pageData);
            } else {
                header('location:' . base_url('admin'));
            }
        }
        # This function is used to update the outstanding amount for an order
        public function update_outstanding_amount() {
            if ($this->id) {
                $selected_order_id = $this->db->escape_str(trim($this->input->post('selected_order_id')));
                $edit_outstanding_amnt = $this->db->escape_str(trim($this->input->post('edit_outstanding_amnt')));
                if ($edit_outstanding_amnt > 0) {
                    $update_array = ['outstanding_amount' => $edit_outstanding_amnt,
                    // 'who_will_pay_outstanding_amount'=> 3,
                    'updated_at' => time() ];
                } else {
                    # OUTSTANDING AMOUNT IS 0 THAT MEANS ITS PAID SO is_paid_outstanding_amount = 1
                    $update_array = ['outstanding_amount' => $edit_outstanding_amnt, 'who_will_pay_outstanding_amount' => 0, 'is_paid_outstanding_amount' => 1, 'updated_at' => time() ];
                }
                # update data in orders table
                $update_status = $this->Common->updateData('orders', $update_array, "id = " . $selected_order_id);
                // echo $this->db->last_query();
                
            } else {
                header('location:' . base_url('admin'));
            }
        }
        # This function is used to show the review list to admin
        //Reservations Function
        public function review_listnig($restaurant_id = 0) {
            if ($this->id) {
                $query_part = "";
                # Because only super admin will apply filter for restaurant
                if ($restaurant_id != 0 && $this->role == 1) {
                    $query_part.= ' AND `ratings`.`to_id` = "' . $restaurant_id . '"';
                }
                $common_query = "SELECT `ratings`.*, `users`.`id` as `user_id`, `users`.`fullname`, `restaurants`.`id` as `rest_id`, `restaurants`.`rest_name`,`restaurants`.`admin_id` FROM `ratings` INNER JOIN `users` ON `users`.`id` = `ratings`.`from_user_id` INNER JOIN `restaurants` ON `restaurants`.`id` =  `ratings`.`to_id` WHERE `ratings`.`id` != 0" . $query_part;
                # If merchat is logged in
                if ($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2 || $this->role == 2) {
                    $restaurant_id = $this->logged_in_restaurant_id;
                    $where_for_merchant.= ' AND `ratings`.`to_id` = "' . $restaurant_id . '"';
                    $common_query.= $where_for_merchant;
                }
                $common_query.= " ORDER BY ratings.id DESC";
                $page = ($this->uri->segment(4)) ? ($this->uri->segment(4) - 1) : 0; # For pagination segment
                $url = base_url('admin/review_listnig/' . $restaurant_id . '/');
                $total_records = count($this->Common->custom_query($common_query, "get"));
                $query = "" . $common_query . " LIMIT " . $page * ADMIN_PER_PAGE_RECORDS . " , " . ADMIN_PER_PAGE_RECORDS . " ";
                $pageData['review_data'] = $this->Common->custom_query($query, 'get');
                # Pass parameter to common pagination and create pagination function start
                $this->create_pagination($url, $total_records, ADMIN_PER_PAGE_RECORDS);
                $pageData['links'] = $this->pagination->create_links();
                $pageData['start'] = ($page * ADMIN_PER_PAGE_RECORDS) + 1;
                $resturant_query = "SELECT `id` as 'restaurant_id',`rest_name` FROM `restaurants` WHERE  `rest_status` != 3  ORDER BY `id` DESC";
                $pageData['resturant_details'] = $this->Common->custom_query($resturant_query, 'get');
                $data = array('title' => "Reviews and ratings", 'pageName' => "review_and_ratings");
                $this->createBreadcrumb($data);
                $pageData['urlPart'] = $this->getUrlPart();
                $pageData['pageTitle'] = 'Ratings and reviews';
                $pageData['pageName'] = 'review_and_ratings';
                $this->load->view('masterpage', $pageData);
            } else {
                $this->load->view('login');
            }
        }
    }
    