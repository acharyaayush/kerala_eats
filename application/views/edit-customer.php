<?php

   if(isset($user_date) && $user_date != ""){
      //print_r($user_date);

      $user_id = $user_date[0]['id'];// Customer id , role = 3
      $fullname = $user_date[0]['fullname'];
      $email = $user_date[0]['email'];
      $mobile = $user_date[0]['mobile'];
      $user_pin_address = $user_date[0]['user_pin_address'];
      $user_street_address = $user_date[0]['user_street_address'];
      $user_postal_code = $user_date[0]['user_postal_code'];
      $user_unit_number = $user_date[0]['user_unit_number'];
      $latitude = $user_date[0]['latitude'];
      $longitude = $user_date[0]['longitude'];

   }else{
      $fullname = "";
      $email = "";
      $mobile = ""; 
      $user_id = ""; 
      $user_pin_address = ""; 
      $user_street_address = ""; 
      $user_postal_code = "";
      $user_unit_number = "";
      $latitude = "";
      $longitude = "";
   }
?>

<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1><?php echo  $title;?></h1>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item"><?php echo  $title;?></div>
         </div>
      </div>
      <div class="admin_profile">
         <div class="container">
            <div class="card-body">
                <form method="POST">
                    <div class="modal-body">
                       <div class="admin_profile">
                          <div class="form-group  admin-input-field">
                             <div class="row">
                                <div class="col-md-3">
                                   <label>First Name*</label>
                                </div>
                                <div class="col-md-7">
                                   <input type="text" name="fullname" id="customer_name" value="<?php echo $fullname;?>" placeholder="Enter first name" required="" class="check_space full_name_length only_alphabets" />
                                   &nbsp;<span class="error" id="unfill_name"></span>
                                </div>
                             </div>
                          </div>
                          
                          <div class="form-group admin-input-field">
                             <div class="row">
                                <div class="col-md-3">
                                   <label>Email*</label>
                                </div>
                                <div class="col-md-7">
                                   <input type="email" name="email" id="customer_email" value="<?php echo $email;?>" placeholder="Enter email address"  required=""  onblur="validateEmail(this,'#email_valid','#invalid_email')"/>
                                    &nbsp;<span class="error" id="invalid_email"></span>
                                </div>
                             </div>
                          </div> 
                          <div class="form-group admin-input-field phone-number-field">
                             <div class="row">
                                <div class="col-md-3">
                                   <label>Phone*</label>
                                </div>
                                <div class="col-md-7">
                                   <input id="phone" type="tel"  maxlength="9" minlength="8" name="phone" placeholder="Enter Phone Number" required="" class="form-control form-control-sm rounded-0 contact_number check_space" value="<?php echo $mobile;?>">
                                    &nbsp;<span class="error" id="invalid_phone"></span>
                                     <div class="input-group-append">
                                   </div>
                                </div>
                             </div>
                          </div>
                           <div class="form-group admin-input-field phone-number-field">
                             <div class="row">
                                <div class="col-md-3">
                                   <label>Address Line 1*</label><!--(Pin address as Street Address)-->
                                </div>
                                <div class="col-md-7">
                                    <input type="text" name="pin_address" placeholder="Enter Street Address" value="<?php echo $user_pin_address;?>"  class="check_space"  id="pin_address">
                                    <span class="error" id="unfill_pin_address"></span>
                                     <div class="input-group-append">
                                   </div>
                                </div>
                             </div>
                          </div>
                          <div class="form-group admin-input-field phone-number-field">
                             <div class="row">
                                <div class="col-md-3">
                                   <label>BLK /House /Apartment No.*</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text"  name="customer_street_address" placeholder="Enter BLK /House /Apartment No." value="<?php echo $user_street_address;?>"  class="check_space"  id="customer_street_address">
                                    <span class="error" id="unfill_street_address"></span>
                                     <div class="input-group-append">
                                   </div>
                                </div>
                             </div>
                          </div>
                          <div class="form-group admin-input-field phone-number-field">
                             <div class="row">
                                <div class="col-md-3">
                                   <label>Postal Code*</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text"  name="postal_code" placeholder="Enter Postal Code" value="<?php echo $user_postal_code;?>"  class="check_space"  id="postal_code">
                                    <span class="error" id="unfill_postal_code"></span>
                                     <div class="input-group-append">
                                   </div>
                                </div>
                             </div>
                          </div>
                           <div class="form-group admin-input-field phone-number-field">
                             <div class="row">
                                <div class="col-md-3">
                                   <label>Unit Number*</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text"  name="unit_number" placeholder="Enter Unit Number" value="<?php echo $user_unit_number;?>"  class="check_space"  id="unit_number">
                                    <span class="error" id="unfill_unit_number"></span>
                                     <div class="input-group-append">
                                   </div>
                                </div>
                             </div>
                          </div>
                       </div>
                    </div>
                    <div class="form-group admin-buttons">
                       <input type="hidden" value="<?php  echo $user_id;?>" id="customer_id"/>
                       <input type="hidden" value="<?php  echo $latitude;?>" id="customer_latitude"/>
                       <input type="hidden" value="<?php  echo $longitude;?>" id="customer_longtitude"/>
                        <a href="<?php echo base_url('admin/AllUser/3');?>"  class="btn btn-secondary change-password_cancel">Cancel</a>
                       <button type="button"  id="get_customer_lat_long" class="btn btn-primary modal_btns">Save</button>
                       <button type="button"  id="customer_edit_submit" class="btn btn-primary modal_btns d-none">Save</button>
                    </div>
                 </form>
            </div>
         </div>
      </div>
   </section>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key= AIzaSyAZNYje65H5kEiuMuF_gFmDwloZLmuIv-I&sensor=false&libraries=places"></script>
  <script type="text/javascript">
 // for restaurant add-edit page-----------START ----AUTO COMPLETE ADDRES it is usering for delvery charge calculation----------
    function initialize() {
      var input = document.getElementById('pin_address');
      new google.maps.places.Autocomplete(input);
    }

    google.maps.event.addDomListener(window, 'load', initialize);
// for restaurant add-edit page-----------END----------
  </script>

 