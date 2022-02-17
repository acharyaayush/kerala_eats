<?php
if (isset($discount_data) && $discount_data != "" && !empty($discount_data)) {
    //print_r($discount_data);
    $discount_data_tr = "";
    foreach ($discount_data as $value) {
        $discount_id = $value['discount_id'];
        $code_name = $value['code_name'];
        $discount_value = $value['discount_value'];
        $max_discount = $value['max_discount'];
        $desciption = $value['desciption'];
        $valid_from = $value['valid_from'];
        $valid_till = $value['valid_till'];
        $discount_restaurant_id = $value['restaurant_id'];
        $promo_status = $value['promo_status'];

        //Epcho time convert --------
         #Promo Code valid Form---------START-------
         $from_date = new DateTime("@$valid_from");  // convert UNIX timestamp to PHP DateTime
         $discount_from_date = $from_date->format('d-m-Y H:i');
         #Promo Code valid Form---------END-------

         #Promo Code valid Till---------START-------
         $till_date = new DateTime("@$valid_till");  // convert UNIX timestamp to PHP DateTime
         $discount_till_date =  $till_date->format('d-m-Y H:i');
         #Promo Code valid Till---------END-------


         if($promo_status == 1){
            $enable_disable_status = 'checked = "checked"';
            $enable_disable_value = "2";
         }else  if($promo_status == 2){
            $enable_disable_status = "";
            $enable_disable_value = "1";
         }
      
        
        $discount_data_tr .= '<tr>
                                 <td>'.$code_name.'</td>
                                 <td>'.$discount_value.'</td>
                                 <td>'.$max_discount.'</td>
                                 <td>'.$desciption .'</td>
                                 <td>'.$discount_from_date.'</td>
                                 <td>'.$discount_till_date.'</td>
                            </tr>';
    }
}else{
   $discount_data_tr = "<tr><td colspan='6' class='no-records'>No Records Found </td></tr>";
   $discount_data = "";
   // for edit mode 
   $discount_id  = "";
   $code_name = "";
   $discount_value = "";
   $max_discount = "";
   $desciption = "";
   $discount_from_date = "";
   $discount_till_date = "";
   $enable_disable_status = "";
   $enable_disable_value = "";
}
?>
<?php
  //For Restaurant Select box------------------------START-------------
  $restaurant_list = "";
  if (isset($resturant_details) && $resturant_details != "" && !empty($resturant_details)) {

      $count = 1;
      foreach ($resturant_details as $value) {
        $restaurant_id = $value['restaurant_id'];
        $restaurant_name = $value['rest_name'];

        if(($count == 1 ) || $restaurant_id == trim($selected_restaurant_id)){
              $select = "selected";  
                
        }else{
             $select = "";
        }
        
        $restaurant_list.= '<option value="'.$restaurant_id.'" '.$select.'>'.$restaurant_name.'</option>';
         $count++;
      }
  }else{
     $restaurant_list = '<option value="">Not available active restaurants</option>';
  }
   //For Restaurant Select box------------------------END-------------
?>
<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Discount</h1>
          <span>
         <div class="col-md-2">
            <!-- Pass 1 as parameter to call add user form -->
<!--            <a href="" class="btn btn-primary" data-target="#add_discount_popup" data-toggle="modal">Add Discount</a>
 -->         </div>
      </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Discount</div>
         </div>
      </div>
      <div class="promo-code-list">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                  <div class="user_tables">
                  <div class="row ml-2">
                     <div class="form-group col-md-3 admin-input-field">
                       <select id="select_restaurant_id_for_discount" onchange="location = BASE_URL+'admin/discount/'+this.value;">
                           <?php echo $restaurant_list;?>
                       </select>
                        &nbsp;<span class="error" id="unselect_restaurant"></span>
                     </div>
                     <div class="form-group col-md-3">
                        
                     </div>
                     <div class="form-group col-md-2">
                        
                     </div>
                     <div class="form-group col-md-4">
                       
                  </div>
                  </div>
                  <!-- <div class="mb-3 users_btns">
                     <button class="btn btn-primary search_user_list_data" type="button">Search</button>
                     <a href="#" class="btn btn-primary"> Clear</a>
                     <button class="btn btn-primary export_user_csv" type="button">Import/Export</button>
                  </div> -->
               </div>
                <?php $this->load->view("validation");?>
                  <div class="card-body catg-tab table-flip-scroll orders-tables user_tables">
                      <div class="mb-3 users_btns text-right">

                        <?php
                           if($discount_data == ""){

                         ?>

                        <button class="btn btn-primary set_edit_discount" type="button" id="set_discount" data-target="#set_edit_discount_popup" data-toggle="modal">Set Discount</button>

                        <?php }else{?>
                         
                           <button class="btn btn-primary mr-2 set_edit_discount" type="button" data-target="#set_edit_discount_popup" data-toggle="modal" id="edit_discount">Edit Discount</button>

                           <a href="#" class="btn  mr-2 btn-danger delete_discount" edit_discount_id = "<?php echo $discount_id;?>" >   <i class="fas fa-trash-alt" style="cursor: pointer;"></i> Delete</a>
             

                             <label class="switch promocode-status">
                                  <input type="checkbox" class="discount_status" edit_discount_id= "<?php echo $discount_id;?>" value="<?php echo $enable_disable_value;?>" <?php echo $enable_disable_status;?> />
                                  <span class="slider round"></span>
                              </label>

                         <?php }?>
                   
                     </div>
                     <table class="table">
                        <thead>
                           <tr>
                              <th>Name</th>
                              <th>Value</th>
                              <th>Max Amount</th>
                              <th>Description</th>
                              <th>Valid From</th>
                              <th>Valid To</th>
                           </tr>
                        </thead>
                        <tbody>
                            <?php echo $discount_data_tr;?>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!---
      <div class="promo-code-list">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                  
                  <div class="card-header user_tables">
                  <div class="row">
                     <div class="form-group col-md-4 admin-input-field">
                      <h1 class="product_wise_heading">Product Wise Discount</h1>
                     </div>
                     <div class="form-group col-md-3">
                        
                     </div>
                     <div class="form-group col-md-4">
                        
                     </div>
                  -->
                    <!--  <div class="form-group col-md-2 text-right">
                        <div class="mb-3 users_btns">
                       <button class="btn btn-primary search_user_list_data" type="button" data-target="#add_discount_popup" data-toggle="modal">Add Discount</button>
                     </div>
                  </div> -->
           <!--       
                  </div>
               </div>
                  <div class="card-body table-flip-scroll orders-tables">
                     <div class="row">
                     <div class="form-group col-md-3 admin-input-field">
                      
                     </div>
                     <div class="form-group col-md-3">
                        
                     </div>
                     <div class="form-group col-md-4">
                        
                     </div>
                     <div class="form-group col-md-2 text-right user_tables">
                        <div class="mb-3 users_btns">
                       <button class="btn btn-primary search_user_list_data" type="button" data-target="#add_discount_popup" data-toggle="modal">Add Discount</button>
                     </div>
                  </div>
                  </div>
                     <table class="table">
                        <thead>
                           <tr>
                              <th>Name</th>
                              <th>Value</th>
                              <th>Description</th>
                              <th>Valid From</th>
                              <th>Valid To</th>
                              <th>Max Amount</th>
                              <th>Actions</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td>Kerala Eats Special 10% Discount</td>
                              <td>20.00</td>
                              <td>Special discount only for Kerala Eats users</td>
                              <td>Sep 22, 2020</td>
                              <td>Feb 28, 2021</td>
                              <td>100</td>
                              <td>-</td>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>-->
   </section>

 <!-- Modal for Add Discount -->
      <div class="modal fade add-edit-discount-popup" id="add_discount_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="modal-dialog read_more_popup" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Add Discount</h5>
                  <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <form>
               <div class="modal-body add_category">
                  <div class="form-group admin-input-field">
                     <div class="row">
                        <div class="col-md-6"> 
                           <label>Discount Name</label>
                           <input type="text" name="discount-name" placeholder="Enter Discount Name" value="" required="">
                        </div>
                        <div class="col-md-6">
                           <label>Discount (%) </label>
                           <input type="text" name="discount" placeholder="Discount" value="" required="">
                        </div>
                     </div>
                  </div>
                  <div class="form-group admin-input-field">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Description (Max 150 Characters)</label>
                           <textarea type="text" name="description" placeholder="Description" value="" required=""></textarea> 
                        </div>
                        <div class="col-md-6">
                           <label>Max Amount</label>
                           <input type="text" name="max-amount" placeholder="Enter Max Amount" value="" required="">
                        </div>
                     </div>
                  </div> 
                  <div class="form-group admin-input-field">
                     <div class="row">
                        <div class="col-md-6">
                           <label>From </label>
                           <input type="datetime-local" name="fdate" placeholder="Enter category name" value="" required="">
                        </div>
                        <div class="col-md-6">
                           <label>Till </label>
                           <input type="datetime-local" name="till" placeholder="Enter category name" value="" required="">
                        </div>
                     </div>
                  </div>
               </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Cancel</button>
                     <button type="button" class="btn btn-primary modal_btns">Add</button>
                  </div>
               </form>
               </div>
            </div>
      </div>
      <!--end modal-->

      <!-- Modal for Set Discount -->
      <div class="modal fade add-edit-discount-popup" id="set_edit_discount_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="modal-dialog read_more_popup" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title set_edit_title" id="exampleModalLabel"> </h5><!--Title will change onn  click set_edit_title-->
                  <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <form>
               <div class="modal-body add_category">
                  <div class="form-group admin-input-field">
                     <div class="row">
                        <div class="col-md-6"> 
                           <label>Discount Name</label>
                           <input type="text" maxlength="50" name="discount_name"  placeholder="Ex. Kerala Eats Special 10% Discount" class="check_space" value="<?php echo  $code_name;?>" required="" id="discount_name">
                            &nbsp;<span class="error" id="unfill_discount_name"></span>
                        </div>
                        <div class="col-md-6">
                           <label>Discount (%) </label>
                           <input type="text" maxlength="3" class="check_space" name="discount_value" placeholder="Discount" value="<?php echo  $discount_value;?>" required="" id="discount_value">
                           &nbsp;<span class="error" id="unfill_discount_value"></span>
                        </div>
                     </div>
                  </div>
                  <div class="form-group admin-input-field">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Description (Max 150 Characters)</label>
                          <textarea minlength="20"  maxlength="150" class="check_space" type="text" name="description" placeholder="Description" required="" id="description"><?php echo  $desciption;?></textarea> 
                           &nbsp;<span class="error" id="unfill_description"></span>
                        </div>
                        <div class="col-md-6">
                           <label>Max Amount</label>
                           <input  maxlength="10" class="check_space" type="number" name="max_amount" placeholder="Enter Max Amount" value="<?php echo  $max_discount;?>" required="" id="max_amount" step="any">
                            &nbsp;<span class="error" id="unfill_max_amount"></span>
                        </div>
                     </div>
                  </div> 
                  <div class="form-group admin-input-field">
                     <div class="row">
                        <div class="col-md-6">
                           <label>From </label>
                            <input type="text" id="discount_start_date" name="discount_start_date" min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" value="<?php echo  $discount_from_date;?>"  required="" class="check_space date_valid">
                            &nbsp;<span class="error" id="unfill_start_date"></span>
                        </div>
                        <div class="col-md-6">
                           <label>Till </label>
                           <input type="text" id="discount_end_date" name="discount_end_date" min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" value="<?php echo  $discount_till_date;?>" required=""  class="check_space date_valid">
                           &nbsp;<span class="error" id="unfill_end_date"></span>
                        </div>
                     </div>
                  </div>
               </div>
                  <div class="modal-footer">
                     <input type="hidden" name="discount_set_edit_mode" id="discount_set_edit_mode"><!--1  = set mode , 2 = edit mode-->
                     <input type="hidden" name="edit_discount_id" id="edit_discount_id" value="<?php echo  $discount_id;?>"><!--1  = set mode , 2 = edit mode-->
                     <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Cancel</button>
                     <button type="button" id="discount_submit" class="btn btn-primary modal_btns">Save</button>
                  </div>
               </form>
               </div>
            </div>
      </div>
         <!--end modal-->
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
         $j2("#discount_start_date").datepicker({//datepicker
           dateFormat: "dd-mm-yy",
           minDate: 0,
          onSelect: function (date) {
              var dt2 = $j2('#discount_end_date');
              var startDate = $j2(this).datepicker('getDate');
              var minDate = $j2(this).datepicker('getDate');
              /*if (dt2.datepicker('getDate') == null){
                dt2.datepicker('setDate', minDate);
              }  */            
              //dt2.datepicker('option', 'maxDate', '0');
              dt2.datepicker('option', 'minDate', minDate);
          }
        });
        $j2('#discount_end_date').datepicker({//datepicker
            dateFormat: "dd-mm-yy",
            minDate: 0
        });           
    
  </script>