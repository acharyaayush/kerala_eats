<?php
// FOR ONLY EDIT MODE----------------START-----------------------
 
$select_promotion_level_list = "";
$if_promo_for_all_rest = ""; // edit time check only
$edit_multiple_restaurant_id  = array();
$is_forever = 1; # DEFAULT
if (isset($promotion_data_by_code_name) && $promotion_data_by_code_name != "" && !empty($promotion_data_by_code_name)) {
      //print_r($promotion_data_by_code_name);
      $value = $promotion_data_by_code_name[0];
       
      $promo_type = $value['promo_type'];  //1 - Flat 2 - Percent  
      $code_name = $value['code_name'];  
      $discount_value = $value['discount_value'];  
      $desciption = $value['desciption'];  
      $valid_from = $value['valid_from'];  
      $valid_till = $value['valid_till'];  
      
      $min_value = $value['min_value'];  
      $max_discount = $value['max_discount'];  
      $max_delivery_discount = $value['max_delivery_discount'];  
      $promo_status = $value['promo_status']; 
      $edit_level_id = $value['level_id'];  
      $applied_on_id = $value['applied_on_id'];  
      $if_promo_for_all_rest = $value['if_promo_for_all_rest'];  //If promo code is for all restaurant then 1 value will be go , and we dont need to multi entry for all restaruant  
      $added_by = $value['added_by'];  
      $is_auto_apply = $value['is_auto_apply'];  
      $allow_multiple_time_use = $value['allow_multiple_time_use'];  
      $max_allowed_times = $value['max_allowed_times']; 

      //Epcho time convert --------
         #Promo Code valid Form---------START-------
        if($valid_from != 0 || $valid_from == ""){
            $from_date = new DateTime("@$valid_from");  // convert UNIX timestamp to PHP DateTime
            $code_from_date = $from_date->format('d-m-Y H:i');
        }else{//for forever
             $code_from_date  = "";
        }
         
         #Promo Code valid Form---------END-------

         #Promo Code valid Till---------START-------
         if($valid_till != 0 || $valid_till == ""){
            $till_date = new DateTime("@$valid_till");  // convert UNIX timestamp to PHP DateTime
           $code_till_date =  $till_date->format('d-m-Y H:i');
         }else{//for forever
              $code_till_date  = "";
         }
         
         #Promo Code valid Till---------END-------

         if($edit_level_id == 1){
            $show_max_delivery_charge_field_class = '';
         }else{
            $show_max_delivery_charge_field_class = 'd-none';
         }

         # Get whether promo is limited or forever
         // echo $value['valid_from'];
         // echo "<br>";
         // echo $value['valid_from']
         if($value['valid_from'] != 0)
         {
            // echo "11111111";
            $is_forever = 2; # It has a validity
         }else
         {
            // echo "2222222";
            $is_forever = 1; # Its a forever promotion
         }
      

      foreach ($promotion_data_by_code_name as $column_name => $column_value) {
        array_push( $edit_multiple_restaurant_id, $column_value['restaurant_id']);
      }

      $disable_promo_code = "disabled = 'disabled'";
}else{
      $promo_type= "";  
      $code_name= "";  
      $discount_value= "";  
      $desciption= "";  
      $code_from_date= "";  
      $code_till_date= "";  
      $edit_multiple_restaurant_id= "";  
      $min_value= "";  
      $max_discount= "";  
      $max_delivery_discount  = "";
      $promo_status= "";
      $edit_level_id= "";  
      $applied_on_id= "";  
      $added_by= "";  
      $is_auto_apply= "";  
      $allow_multiple_time_use= "";  
      $max_allowed_times= ""; 

      $disable_promo_code = ""; 
      $show_max_delivery_charge_field_class = 'd-none';
}
// FOR ONLY EDIT MODE----------------END-----------------------


//------------------ For Add - EDIT BOTH--------------------------------------
//check logged user is merchant (role 2) or super admin(1)--

// geting applied on select option value-------START-----------
$select_promotion_level_list = "";
if (isset($promotion_level_list) && $promotion_level_list != "" && !empty($promotion_level_list)) {
    
    $count = 1;
    foreach ($promotion_level_list as $value) {

         $level_id = $value['level_id'];
         $type = $value['type'];
        

         if( $edit_level_id  == $level_id){
            $selected = 'selected';
            
         }else{
             $selected = '';
             
         }
         
           //for super admin
           if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){
                $select_promotion_level_list.= '<option value="'.$level_id.'" '.$selected.'>'.$type.'</option>';
           }else if($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2){//for merchant only logged restaurant
               if($level_id == 3 || $level_id == 4){
                   $select_promotion_level_list.= '<option value="'.$level_id.'" '.$selected.'>'.$type.'</option>';
               }
          }
         
      }
}
// geting applied on select option value-------END-----------

// geting restaurant list on load ----- and product and category select value will get by on click applied on by ajax -----start-----
$select_restaurants_list = "";

if (isset($restaurant_list) && $restaurant_list != "" && !empty($restaurant_list)) {
    
     
    foreach ($restaurant_list as $value) {

         $restaurant_id = $value['id'];
         $rest_name = stripslashes(trim($value['rest_name']));
         $admin_id = $value['admin_id'];

         if($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2){//for merchant only logged restaurant
               $checked = 'checked="checked"';
               $disabled = 'disabled = "disabled"';
               $hide_restaurant = 'd-none';
            }else{
                 $checked = '';
                 $disabled = '';
                 $hide_restaurant = '';
               //check restaurant which is exist in promtion table
                 if(!empty($edit_multiple_restaurant_id)){
                    if (in_array($restaurant_id, $edit_multiple_restaurant_id))
                      {
                          $checked = 'checked="checked"';
                      }
                    else
                      {
                          $checked = '';
                      }
                 }else{
                     $checked = '';
                 }
            }
           
         $select_restaurants_list.= '<label class="enabled-label">'.$rest_name.'
                                          <input type="checkbox" class="selected_restaurant_id" id="restaurant_id_'.$restaurant_id.'" name="restaurant_id" value="'.$restaurant_id.'"   '.$checked.' '.$disabled.'>
                                             <span class="checkmark_check"></span>
                                       </label>';
        
      }
}
// geting restaurant list on load ----- and product and category select value will get by on click applied on by ajax -----end-----

?>


<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1> <?php echo $pageTitle;?>  </h1>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item"><?php echo $pageTitle;?> </div>
         </div>
      </div>
      <div class="add-restaurant">
         <div class="container">
            <div class="alert d-none" id="form_submit_status">
                <span class="closebtn">&times;</span>  
                <span id="submit_status"></span>
            </div> 
            <div class="card-body">
               <form  autocomplete="off" enctype="multipart/form-data" id="promotion_form">
               <div class="form-group admin-input-field">
                  <div class="row">
                     <div class="col-md-6">
                        <label>Promotion Type* </label>
                        <select name="promotion_type" id="promotion_type" required="">
                           <option value="">Select Type</option>
                           <option value="1" <?php if(isset($promo_type) && $promo_type =="1"){echo "selected";}?>>Flat</option>
                           <option value="2"  <?php if(isset($promo_type) && $promo_type =="2"){echo "selected";}?>>Percent</option>
                        </select>
                        &nbsp;<span class="error" id="unselect_promotion_type"></span>
                     </div>
                     <div class="col-md-6">
                        <label>Promotion Name* </label>
                        <input name="promo_code" maxlength="50" id="promo_code" type="text" class="check_space text-uppercase" onkeypress="return blockSpecialChar(event)" required="" placeholder="EX. FLAT20" value="<?php echo $code_name;?>" <?php echo  $disable_promo_code;?> required="">
                         &nbsp;<span class="error" id="unfill_promo_code"></span>
                     </div>
                  </div>
               </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Discount Value*</label>
                     <input name="discount_value" maxlength="3" id="discount_value"  type="text" class="check_space" required="" placeholder="Enter Discount Value" value="<?php echo $discount_value;?>" required="">
                     &nbsp;<span class="error" id="unfill_discount_value"></span>
                  </div>
                  <div class="col-md-6">
                     <label>Description  </label>
                     <textarea type="text" minlength="20"  maxlength="150" id="description"  name="description" placeholder="Description"     required="" class="check_space"><?php echo $desciption;?></textarea> 
                      &nbsp;<span class="error" id="unfill_description"></span>
                  </div>
               </div>
            </div>
             <div class="form-group admin-input-field">
               <div class="row">
                  <label>Use Type</label>
                 <div class="d-flex">
                     <label class="enabled-label">Forever
                        <input type="radio" id="promo_use_type" class="forever_promo" value="1" name="promo_use_type" required="" >
                      <span class="checkmark"></span>
                     </label>
                     <label class="enabled-label">Limited
                        <input type="radio" id="promo_use_type" class="limited_promo" value="2"  name="promo_use_type" >
                        <span class="checkmark" required=""></span>
                        <input type="hidden" id="cur_use_type" value="<?php echo $is_forever ?>">
                     </label>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field d-none" id="if_promo_use_type_limit">
               <div class="row">
                  <div class="col-md-6">
                     <label>From </label>
                     <input type="text" id="promo_code_start_date" name="promo_code_start_date" min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" value="<?php echo $code_from_date;?>"  required="" class="check_space date_valid">
                      &nbsp;<span class="error" id="unfill_start_date"></span>
                  </div>
                  <div class="col-md-6">
                     <label>Till </label>
                     <input type="text" id="promo_code_end_date" name="promo_code_end_date" min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" value="<?php echo $code_till_date;?>" required=""  class="check_space date_valid">
                       &nbsp;<span class="error" id="unfill_end_date"></span>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Maximum Discount Value </label>

                     <span class="currency">S$</span>
                     <input type="number" id="max_discount_value"  name="max_discount_value" placeholder=""  required="" class="check_space maximum_value" step="any" value="<?php echo $max_discount;?>">
                     &nbsp;<span class="error" id="unfill_max_discount_value"></span>
                  </div>
                  <div class="col-md-6">
                     <label>Maximum No Of Allowed Time(S) </label>
                     <input type="text" maxlength="4"  id="max_allowed_time" name="max_allowed_time" placeholder="Enter Maximum No Of Allowed User(S)"   required="" value="<?php echo $max_allowed_times;?>" >
                     &nbsp;<span class="error" id="unfill_max_allowed_time"></span>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Minimum Order Amount* </label>
                     <span class="currency">S$</span>
                     <input type="text" maxlength="4" id="minimum_order_amount" name="minimum_order_amount" placeholder="" value="<?php echo $min_value;?>" required="" class="maximum_value">
                       &nbsp;<span class="error" id="unfill_minimum_order_amount"></span>
                  </div>
                  <div class="col-md-6">
                     <label>Promotion Application Mode* </label>
                     <select id="promotion_applicaion_mode" name="promotion_applicaion_mode" required="">
                        <option value="">Is promo code auto apply?</option>
                        <option value="1" <?php if(isset($is_auto_apply) && $is_auto_apply =="1"){echo "selected";}?>>Yes</option>
                        <option value="2"  <?php if(isset($is_auto_apply) && $is_auto_apply =="2"){echo "selected";}?>>No</option>
                     </select>
                     &nbsp;<span class="error" id="unselect_applicaion_mode"></span>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                 <div class="col-md-6">
                     <label>Allow Single User To Use The Code Multiple Times?</label>
                     <label class="enabled-label">Yes
                        <input type="radio" id="allow_single_user" value="1"  <?php if(isset($allow_multiple_time_use) && $allow_multiple_time_use =="1"){echo 'checked="checked"';}?> name="allow_single_user" required="">
                      <span class="checkmark"></span>
                     </label>
                     <label class="enabled-label">No
                        <input type="radio" id="allow_single_user" value="2"  name="allow_single_user"  <?php if(isset($allow_multiple_time_use) && ($allow_multiple_time_use =="2" || $allow_multiple_time_use =="" || $allow_multiple_time_use ==0)) {echo 'checked="checked"';}?>>
                        <span class="checkmark" required=""></span>
                     </label>
                      &nbsp;<span class="error" id="unselect_allow_single_user"></span>
                  </div>
                  <div class="col-md-6">
                     <label>Promotion Applied On*</label>
                     <select id="applied_on" name="applied_on" required="">
                        <option value="">Select on which want to applied on </option>
                         <?php echo $select_promotion_level_list;?>
                     </select>
                      &nbsp;<span class="error" id="unselect_applied_on"></span>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field <?php echo $show_max_delivery_charge_field_class;?>" id="show_max_delivery_charge_field">
               <div class="row">
                   <div class="col-md-6">
                     <label>Maximum Delivery Charge </label>
                     <span class="currency">S$</span>
                     <input type="number" id="max_delivery_charge"  name="max_delivery_charge" placeholder=""  required="" class="check_space maximum_value" step="any" value="<?php echo $max_delivery_discount ;?>">
                     &nbsp;<span class="error" id="unfill_max_delivery_value"></span>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6 <?php echo $hide_restaurant;?>">
                       <div id="for_select_restaurant" class="d-none"><!--For Restaurant-->
                        <label>Assign Restaurants*</label>
                           <div id="select_restaurant">
                                 <div class="select_dropdown">
                                   <input type="text" onclick="SearchDropdownFunction('SelectRestaurantDropdown')"  placeholder="Search Restaurant..." id="SelectRestaurantInput" onkeyup="filterFunction('SelectRestaurantDropdown','SelectRestaurantInput')" <?php echo $disabled;?>>
                                   <div id="SelectRestaurantDropdown" class="select_dropdown-content">
                                    <label class="enabled-label" data-children-count="1">All
                                          <input type="checkbox" class="selected_restaurant_id" id="select_all_restaurant" name="restaurant_id" value="">
                                             <span class="checkmark_check"></span>
                                       </label>
                                       <?php echo  $select_restaurants_list;?>
                                   </div>
                                 </div>
                            </div>
                        &nbsp;<span class="error" id="unselect_restaurant"></span>
                     </div>
                     
                  </div>
                  <div class="col-md-6">
                      <!--will show product or category if applied_on value product or category by ajax -->
                      <div id="for_select_prodcut" class="d-none"><!--For Products-->
                         <label>Assign Products*</label>
                              <div id="select_product">
                                <div class="select_dropdown">
                                <input type="text" onclick="SearchDropdownFunction('SelectProductDropdown')"  placeholder="Search Product..." id="SelectProductInput" onkeyup="filterFunction('SelectProductDropdown','SelectProductInput')">
                                <div id="SelectProductDropdown" class="select_dropdown-content">
                                    
                                </div>
                              </div>
                            </div>
                              &nbsp;<span class="error" id="unselect_product"></span>
                      </div>
                       <div id="for_select_category" class="d-none"><!--For Category-->
                          <label>Assign Category*</label>
                                <div id="select_category">
                                  <div class="select_dropdown">
                                  <input type="text" onclick="SearchDropdownFunction('SelectCategoryDropdown')"  placeholder="Search Category..." id="SelectCategoryInput" onkeyup="filterFunction('SelectCategoryDropdown','SelectCategoryInput')">
                                  <div id="SelectCategoryDropdown" class="select_dropdown-content">
                                      
                                  </div>
                                </div>
                           </div>
                          &nbsp;<span class="error" id="unselect_category"></span>
                      </div>
                  </div>
               </div>
            </div>
           <div class="form-group">
                  <button type="button" id="add_promotion_submit" class="btn btn-primary change-password-btns mr-10">Save</button>
                  <a  href="<?php echo base_url('admin/promo_codes');?>" type="reset" class="btn btn-secondary change-password_cancel">Cancel</a>
               </div>
            </form>
            </div>
         </div>
      </div>
   </section>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="Stylesheet" type="text/css" />


<!---For time picker with date you need to only replace only datepickerv with datetimepicker---->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>

 <!-- Javascript -->
  <script type="text/javascript">
      var $j2 = jQuery.noConflict();       
         $j2("#promo_code_start_date").datepicker({//datetimepicker for time show with date
           dateFormat: "dd-mm-yy",
           minDate: 0,
          onSelect: function (date) {
              var dt2 = $j2('#promo_code_end_date');
              var startDate = $j2(this).datepicker('getDate');
              var minDate = $j2(this).datepicker('getDate');
              /*if (dt2.datepicker('getDate') == null){
                dt2.datepicker('setDate', minDate);
              }  */            
              //dt2.datepicker('option', 'maxDate', '0');
              dt2.datepicker('option', 'minDate', minDate);
          }
        });
        $j2('#promo_code_end_date').datepicker({//datetimepicker
            dateFormat: "dd-mm-yy",
            minDate: 0
        });           
    
  </script>
  <script type="text/javascript">
     var if_promo_for_all_rest = '<?php echo $if_promo_for_all_rest; ?>';//for edit time check only 
     var promotion_mode_type = '<?php echo $mode_type; ?>';
     var edit_level_id = '<?php echo $edit_level_id; ?>';//applied on value
     // var is_forever = '<?php echo $is_forever; ?>';//applied on value
     var edit_promotion_code_name = '<?php if(isset($edit_by_promotion_code_name)){
      echo trim($edit_by_promotion_code_name);
     }else{
      $edit_by_promotion_code_name = "";
     } ?>';//promotion code name pass for edit 
  </script>
   <?php 

      //  if merchant is logged in, then this condition will check and only merchant restaurant podcuts and category will show. if this blank that means super admin is logged in and then all resataurant will show.
        if($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2){
       ?>
    <script>// if merchant is logged in ----
       var logged_in_restaurant_id = "<?php echo $this->logged_in_restaurant_id;?>";
    </script>
    <?php
      }else{
    ?>
    <script>//if super admin is logged in 
      var logged_in_restaurant_id = "";//SUPER admin is logged in
    </script>
     <?php
      }
    ?>