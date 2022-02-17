<?php
// For search only-------Restaurant Search  --- START  -------------
 $restaurant_search_list = "";
 
if (isset($restaurant_list) && $restaurant_list != "" && !empty($restaurant_list)) {
     
    foreach ($restaurant_list as $value) {

         $search_restaurant_id = $value['id'];
         $search_rest_name = stripslashes(trim($value['rest_name']));

       // for only search -------start
     if($search_restaurant_id == trim($selected_restaurant_id)){
              $select = "selected";  
                
        }else{
             $select = "";
        }
        $restaurant_search_list.= '<option value="'.$search_restaurant_id.'" '.$select.' >'.$search_rest_name.'</option>';// for only search -------end
      }
}
// For search only-------Restaurant Search  ---- END  ------------- 

// For search only-------Customer Search  --- START  -------------
 
$customer_search_list = "";
 
if (isset($customer_list) && $customer_list != "" && !empty($customer_list)) {
     
    foreach ($customer_list as $value) {

         $search_customer_id = $value['id'];
         $search_customer_name = trim($value['fullname']);
         $search_customer_number_id = $value['number_id'];
       

       // for only search -------start
     if($search_customer_id == trim($selected_customer_id)){
              $select = "selected";  
                
        }else{
             $select = "";
        }
        $customer_search_list.= '<option value="'.$search_customer_id.'" '.$select.'>'.$search_customer_name.'  ('.$search_customer_number_id.')</option>';// for only search -------end
      }
}
// For search only-------Customer Search  ---- END  ------------- 

// For order type search -----------------------START------------
 
$order_type_search_list = "";
 
if (isset($rest_accept_types) && $rest_accept_types != "" && !empty($rest_accept_types)) {
     
    foreach ($rest_accept_types as $value) {

         $rest_accept_id = $value['id'];
         $rest_accept_name = trim($value['name']);

       // for only search -------start
      if($rest_accept_id == trim($selected_order_type)){
              $select = "selected";  
                
        }else{
             $select = "";
        }
        $order_type_search_list.= '<option value="'.$rest_accept_id.'" '.$select.' >'.$rest_accept_name.' </option>';// for only search -------end
      }
}
// For order type search -----------------------END------------

// For Business Category search -----------------------START------------
 
$business_category_list = "";
 
if (isset($merchant_categories) && $merchant_categories != "" && !empty($merchant_categories)) {
     
    foreach ($merchant_categories as $value) {

         $category_id = $value['id'];
         $category_name = trim($value['category_name']);

       // for only search -------start
     if($category_id == trim($selected_business_category_id)){
              $select = "selected";  
                
        }else{
             $select = "";
        }
        $business_category_list.= '<option value="'.$category_id.'" '.$select.'>'.$category_name.' </option>';// for only search -------end
      }
}
// For Business Category  search -----------------------END------------
?> 
<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Orders</h1>
         <span>
            <div class="col-md-2">
               <!-- Pass 1 as parameter to call add user form -->
               <!--  <a href="#" class="btn btn-primary">Create Order</a> -->
            </div>
         </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Orders</div>
         </div>
      </div>
      <div class="section-body">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                <div class="col-5"></div>
                      <div class="col-2">
                        <button class="btn btn-primary filter_button" id="" type="button">Filter</button>
                      </div>
                  <div class="card-header user_tables">
                     <div class="row">
                        <div class="form-group col-md-3">
                           <label>From Date</label>
                           <input type="text" id="fromdate" name="fromdate" max="" class="form-control fromdate wv_filter_box_height" autocomplete="off" placeholder="dd-mm-yyyy" value="<?php if($fromdate != '' && $fromdate != 'all'){echo $fromdate;} ?>" />
                        </div>
                        <div class="form-group col-md-3">
                           <label>To Date</label>
                           <input ype="text" id="todate" name="todate" max="" class="form-control todate wv_filter_box_height"  autocomplete="off" placeholder="dd-mm-yyyy" value="<?php if($todate != '' && $todate != 'all'){echo $todate;} ?>" />
                        </div>
                  
                         <div class="form-group col-md-3">
                           <label> Delivery Handle By</label>
                           <select class="custom-select userstatus wv_filter_box_height form-control"  id="delivery_handle_by" name="delivery_handle_by">
                              <option value="">Select Delivery Handle By</option>
                              <option value="1" <?php if($delivery_handle_by == '1' && $delivery_handle_by != 'all'){echo "selected";} ?>>Restaurant</option>
                              <option value="2" <?php if($delivery_handle_by == '2' && $delivery_handle_by != 'all'){echo "selected";} ?>>Kerala Eats</option>
                           </select>
                        </div>
                        <div class="form-group col-md-3">
                           <label> Payment Method</label>
                           <select class="custom-select userstatus wv_filter_box_height form-control" id="payment_mode" name="payment_mode">
                              <option value="">Select Delivery Handle By</option>
                              <option value="1" <?php if($payment_mode == '1' && $payment_mode != 'all'){echo "selected";} ?>>Online (Stripe)</option>
                              <option value="2" <?php if($payment_mode == '2' && $payment_mode != 'all'){echo "selected";} ?>>Online (Hitpay)</option>
                           </select>
                        </div>
                        <div class="form-group col-md-3">
                           <label> Paid Status</label>
                           <select class="custom-select userstatus wv_filter_box_height form-control"id="paid_status" name="paid_status">
                              <option value="">Select Paid Status</option>
                              <option value="0" <?php if($paid_status == '0' && $paid_status != 'all'){echo "selected";} ?>>Unpaid</option>
                              <option value="1" <?php if($paid_status == '1' && $paid_status != 'all'){echo "selected";} ?>>Paid</option>
                           </select>
                        </div>
                         <?php 
                          //  if merchant is logged in, then this condition will check and only merchant- restaurant orders will show. if this blank that means super admin is logged in and then all resataurant will show 
                         //only super admin can see this field
                          if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){
                        ?>
                        <div class="form-group col-md-3">
                           <label>Is Paid to Restaurant </label>
                           <select class="custom-select userstatus wv_filter_box_height form-control"id="is_paid_to_restaurant" name="is_paid_to_restaurant">
                              <option value="">Select Paid to Restaurant  </option>
                              <option value="1"  <?php if($is_paid_to_restaurant == '1' && $is_paid_to_restaurant != 'all'){echo "selected";} ?>>Yes</option>
                              <option value="2" <?php if($is_paid_to_restaurant == '2' && $is_paid_to_restaurant != 'all'){echo "selected";} ?>>No (Pending)</option>
                           </select>
                        </div>
                         <?php
                             }
                         ?>
                        <div class="form-group col-md-3">
                           <label>Order Status</label>
                           <select class="custom-select userstatus wv_filter_box_height form-control"  id="order_status" name="order_status">
                              <option value="">All</option>
                              <option value="0" <?php if($order_status == '0' && $order_status != 'all'){echo "selected";} ?>>Pending</option>
                              <option value="1" <?php if($order_status == '1' && $order_status != 'all'){echo "selected";} ?>>Accepted</option>
                              <option value="2" <?php if($order_status == '2' && $order_status != 'all'){echo "selected";} ?>>Rejected</option>
                              <option value="3" <?php if($order_status == '3' && $order_status != 'all'){echo "selected";} ?>>Dispatched</option>
                              <option value="4" <?php if($order_status == '4' && $order_status != 'all'){echo "selected";} ?>>Cancelled</option>
                              <option value="5" <?php if($order_status == '5' && $order_status != 'all'){echo "selected";} ?>>Completed</option>
                              <option value="6" <?php if($order_status == '6' && $order_status != 'all'){echo "selected";} ?>>Preparing</option>
                              <option value="7" <?php if($order_status == '7' && $order_status != 'all'){echo "selected";} ?>>Ready</option>
                           </select>
                        </div>
                        <?php 
                          //  if merchant is logged in, then this condition will check and only merchant- restaurant orders will show. if this blank that means super admin is logged in and then all resataurant will show
                          if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){
                        ?>
                        <div class="form-group col-md-3">
                          <label>Restaurant</label>
                            <select class="custom-select  wv_filter_box_height form-control search_data" name="restaurant_list" id="search_restaurant_id">
                               <option value="">Select Restaurtant</option>
                               <?php echo $restaurant_search_list;?>
                           </select>
                      </div>
                       <?php
                           }
                       ?>
                       <div class="form-group col-md-3">
                          <label>Customer</label>
                            <select class="custom-select  wv_filter_box_height form-control search_data" name="search_customer_id" id="search_customer_id">
                               <option value="">Select Customer</option>
                               <?php echo $customer_search_list;?>
                           </select>
                       </div>
                        <div class="form-group col-md-3">
                          <label>Order Accept Type</label>
                            <select class="custom-select  wv_filter_box_height form-control search_data" name="order_accept_type" id="order_accept_type">
                               <option value="">Select Order Type</option>
                               <?php echo $order_type_search_list;?>
                           </select>
                       </div>
                        <div class="form-group col-md-3">
                          <label>Business Category</label>
                            <select class="custom-select  wv_filter_box_height form-control search_data" name="business_category_id" id="business_category_id">
                               <option value="">Select Business</option>
                               <?php echo $business_category_list;?>
                           </select>
                       </div>
                        <div class="form-group col-md-3">
                           <label> Is Cutlery Needed </label>
                           <select class="custom-select userstatus wv_filter_box_height form-control"id="is_cutlery_needed" name="is_cutlery_needed">
                              <option value="">Select Cutlery Needed </option>
                              <option value="1"  <?php if($is_cutlery_needed == '1' && $is_cutlery_needed != 'all'){echo "selected";} ?>>Yes</option>
                              <option value="2"  <?php if($is_cutlery_needed == '2' && $is_cutlery_needed != 'all'){echo "selected";} ?>>No</option>
                           </select>
                        </div>
                       <div class="form-group col-md-4">
                           <label>Is Promo Code auto applied  </label>
                           <select class="custom-select userstatus wv_filter_box_height form-control"id="is_promocode_auto_applied" name="is_promocode_auto_applied">
                              <option value="">Select auto applied Promo Code </option>
                              <option value="1"  <?php if($is_promocode_auto_applied == '1' && $is_promocode_auto_applied != 'all'){echo "selected";} ?>>Yes</option>
                              <option value="2" <?php if($is_promocode_auto_applied == '2' && $is_promocode_auto_applied != 'all'){echo "selected";} ?>>No</option>
                           </select>
                        </div>
                         <div class="form-group col-md-4">
                           <label>Is Promo Code auto applied On Delivery </label>
                           <select class="custom-select userstatus wv_filter_box_height form-control"id="is_promocode_auto_applied_on_delivery" name="is_promocode_auto_applied_on_delivery">
                              <option value="">Select auto applied Promo Code apply on delivery </option>
                              <option value="1" <?php if($is_promocode_auto_applied_on_delivery == '1' && $is_promocode_auto_applied_on_delivery != 'all'){echo "selected";} ?>>Yes</option>
                              <option value="2" <?php if($is_promocode_auto_applied_on_delivery == '2' && $is_promocode_auto_applied_on_delivery != 'all'){echo "selected";} ?>>No</option>
                           </select>
                        </div>
                         <div class="form-group col-md-4">
                              <label>Search</label>
                              <input type="search" name="search" class="form-control order_search_key wv_filter_box_height" placeholder="Search" id="search_user_list_data" value="<?php if($search != '' && $search != 'all'){echo $search;} ?>" />
                        </div>
                        <div class="form-group col-md-3">
                           <label>Schedule Date From</label>
                           <input type="text" id="schedule_dt" name="schedule_dt" max="" class="form-control schedule_dt wv_filter_box_height" autocomplete="off" placeholder="dd-mm-yyyy" value="<?php if($schedule_dt != '' && $schedule_dt != 'all'){echo $schedule_dt;} ?>" />
                        </div>
                        <div class="form-group col-md-3">
                           <label>Schedule Date To</label>
                           <input type="text" id="schedule_dt_to" name="schedule_dt_to" max="" class="form-control schedule_dt_to wv_filter_box_height" autocomplete="off" placeholder="dd-mm-yyyy" value="<?php if($schedule_dt_to != '' && $schedule_dt_to != 'all'){echo $schedule_dt_to;} ?>" />
                        </div>
                        <div class="form-group col-md-6 search_clear_btns  users_btns">
                           <form action="javascript:void(0);">
                              <button class="btn btn-primary" id="search_order_list_data" type="button" search_mode="1">Search</button>
                              <a href="<?php echo (base_url('admin/orders/'));?>" class="btn btn-secondary mr-2 clear_btns"> Clear</a>
                              <button class="btn btn-secondary export_orders_csv clear_btns" type="button" search_mode="2">Export Csv</button>
                           </form>
                        </div>
                     </div>
                     <!-- <div class="row">
                        
                     </div> -->
                  </div>
                  <div class="card-body table-flip-scroll orderpage orders-tables tb-scroll customerTableListing" style="padding: 20px 0px;">
                     <table class="table" id="all_Orders_table">
                        <?php $this->load->view('orders_list_table');?>
                     </table>
                      <nav class="text-xs-right">
                          <?php if (isset($links)) { ?>
                              <?php echo $links; ?>
                          <?php } ?>
                      </nav>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="Stylesheet" type="text/css" />
<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j("#fromdate").datepicker({
        dateFormat: 'yy-mm-dd',
        // numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate());
            $j("#todate").datepicker("option", "minDate", dt);
        }
    });
    $j("#todate").datepicker({
        dateFormat: 'yy-mm-dd',
        // numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() - 1);
            // $j("#fromdate").datepicker("option", "maxDate", dt);
        }
    });
    $j("#schedule_dt").datepicker({
        dateFormat: 'yy-mm-dd',
        // numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate());
            $j("#schedule_dt_to").datepicker("option", "minDate", dt);
        }
    });
    $j("#schedule_dt_to").datepicker({
        dateFormat: 'yy-mm-dd',
        // numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() - 1);
            // $j("#schedule_dt_to").datepicker("option", "maxDate", sc_dt_to);
        }
    });
</script>
<?php

  $current_url =  current_url();
  $parameter_url = explode("orders/0/0", $current_url);// if action is load like change status or delete

  if(isset($parameter_url[1])){
    $new_order_url_for_mode = 'admin/orders/table/0'.$parameter_url[1].'';
  }else{
    $new_order_url_for_mode = 'admin/orders/table/0';
  }

?>
 
<script type="text/javascript">
  var  order_delete_url = '<?php echo  base_url('admin/delete_Promo_Code/'); ?>';
  var  order_table_url = '<?php echo  base_url(''.$new_order_url_for_mode.''); ?>';
  var  is_this_orders_page = 1;
</script>


<?php 

  
    //  if merchant is logged in, then this condition will check and only merchant restaurant orders will show. if this blank that means super admin is logged in and then all resataurant will show
      if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){
     ?>
  <script>//if super admin is logged in 
     var selected_restaurant_id = "<?php echo $selected_restaurant_id;?>";//$selected_restaurant_id varible set from admin on load // for search  
     
  </script>
  <?php
    }else{
  ?>
  <script>// if merchant is logged in ----
     var selected_restaurant_id = "<?php echo $this->logged_in_restaurant_id;?>";
  </script>
   <?php
    }
?>
