<?php  
 
if (isset($merchant_rest_detail) && $merchant_rest_detail != "" && !empty($merchant_rest_detail)) {

    //check restaurant has logo  image image 
    if($merchant_rest_detail[0]['logo_image'] != ""){
         $logo_image = trim(base_url().$merchant_rest_detail[0]['logo_image']);
    }else{
         $logo_image = base_url().'assets/images/mr_merchant_pic.png';//default
    }

    // check restaurant has banner image  
    if($merchant_rest_detail[0]['logo_image'] != ""){
        $banner_image = trim(base_url().$merchant_rest_detail[0]['banner_image']);
    }else{
        $banner_image = base_url().'assets/images/default_merchant_restaurant_banner.png';//default
    }

    // check restaurant has mobile banner image  
    if($merchant_rest_detail[0]['mobile_banner_image'] != ""){
        $mobile_banner_image = trim(base_url().$merchant_rest_detail[0]['mobile_banner_image']);
    }else{
        $mobile_banner_image = base_url().'assets/images/default_merchant_restaurant_banner.png';//default
    }

    $email = $merchant_rest_detail[0]['email'];
    $mobile = $merchant_rest_detail[0]['mobile'];

    $rest_pin_address = $merchant_rest_detail[0]['rest_pin_address'];// we use always pin address becouse lat long makeing according to this address on everywhere

    //open close time 
    $open_time = $merchant_rest_detail[0]['open_time'];
    $close_time = $merchant_rest_detail[0]['close_time'];

    // break time it must be  between on open and close time
    $break_start_time = $merchant_rest_detail[0]['break_start_time'];
    $break_end_time = $merchant_rest_detail[0]['break_end_time'];

    //Restaurant accept type ----------Start---------------
    if($merchant_rest_detail[0]['is_order_now_accept'] !="" && $merchant_rest_detail[0]['is_order_now_accept'] == 1){
        $is_order_now_accept_value = '<p class="restaurant-accept" style="cursor: pointer;">Order Now</p>';
    }else{
        $is_order_now_accept_value = '';
    }

    if($merchant_rest_detail[0]['is_self_pickup_accept'] !="" && $merchant_rest_detail[0]['is_self_pickup_accept'] == 1){
        $is_self_pickup_accept_value = '<p class="restaurant-accept" style="cursor: pointer;">Self Pickup</p>';
    }else{
        $is_self_pickup_accept_value = '';
    }

    if($merchant_rest_detail[0]['is_order_later_accept'] !="" && $merchant_rest_detail[0]['is_order_later_accept'] == 1){
        $is_order_later_accept_value = '<p class="restaurant-accept" style="cursor: pointer;">Order For Later</p>';
    }else{
        $is_order_later_accept_value = '';
    }

    if($merchant_rest_detail[0]['is_dinein_accept'] !="" && $merchant_rest_detail[0]['is_dinein_accept'] == 1){
        $is_dinein_accept_value = '<p class="restaurant-accept" style="cursor: pointer;">Dinein</p>';
    }else{
        $is_dinein_accept_value = '';
    }
    //Restaurant accept type ----------End---------------

    // delivery handle by
    $delivery_handled_by = $merchant_rest_detail[0]['delivery_handled_by'];

    //1 - restaurant 2 - By Kerala Eats
    if($delivery_handled_by == 1){
        $delivery_handled_by_value = 'Restaurant';
    }else{
        $delivery_handled_by_value = 'Kerala Eats';
    }

    //restaurant document
    $uploaded_document = explode(',', $merchant_rest_detail[0]['uploaded_document']);
    $res_document = "";
    $count = 1;
    if(count($uploaded_document) > 0)
    {
      foreach ($uploaded_document as $value)
      {
      
        if($value !="" ){
            $document_img = base_url($value);
            $doc_exp = explode("merchant_documents/", $value);
            $check_extention =  explode(".", $doc_exp[1]);
            if($check_extention[1] == 'pdf' || $check_extention[1] == 'doc'){//document file 
              
                $res_document .= '<div class="col-md-3">
                                     <a class="rest_doc" href="'.$document_img.'" target="_blank">Document '.$count.'</a>
                                </div>';
            }else{// img
               $res_document .= '<div class="col-md-3 admin-profile-img">
                                    <a href="'.$document_img.'" target="_blank"><img id="disp_img" src="'.$document_img.'" alt="Restaurant Documents" /></a>
                                 </div> ';
            }


         }else{
            $document_img = base_url('assets/images/default_document.png');

            $res_document .= '<div class="col-md-3 admin-profile-img">
                              <a href="'.$document_img.'" target="_blank"><img id="disp_img" src="'.$document_img.'" alt="Restaurant Documents" /></a>
                           </div> ';
         }
         
         $count++;
        
      }
    } 


    $rest_name = $merchant_rest_detail[0]['rest_name'];
    $res_description = $merchant_rest_detail[0]['res_description'];
    $rest_pin_address = $merchant_rest_detail[0]['rest_pin_address'];
    $rest_unit_number = $merchant_rest_detail[0]['rest_unit_number'];
    $rest_postal_code = $merchant_rest_detail[0]['rest_postal_code'];

    $business_type = $merchant_rest_detail[0]['business_type'];
     if($business_type  == 1){//If restaurant's business type is food then this column will contain value. So value 0 (if not applicable) 1 (Restaurant) 2 (Kitchen)
        $food_type_enable_disable = "";
      }else{
        $food_type_enable_disable = "disabled=''";
      }
     
    $food_type = $merchant_rest_detail[0]['food_type'];

    
    



}else{
    $logo_image = base_url().'assets/images/mr_merchant_pic.png';//default
    $banner_image = base_url().'assets/images/default_merchant_restaurant_banner.png';//default
    $email  = "";
    $mobile  = "";
    $rest_pin_address  = "";

    $open_time  = "";
    $close_time  = "";

    $break_start_time  = "";
    $break_end_time  = "";

     
    $is_order_later_accept_value = 'Order Now';
    $is_self_pickup_accept_value = "";
    $is_dinein_accept_value = "";
    $delivery_handled_by_value = "";

    $document_img = base_url('assets/images/default_document.png');
    $res_document .= '<div class="col-md-3 admin-profile-img">
                      <a href="'.$document_img.'" target="_blank"><img id="disp_img" src="'.$document_img.'" alt="Restaurant Documents" /></a>
                   </div> ';

    
    $rest_name = "";
    $res_description = "";
    $rest_pin_address = "";
    $rest_unit_number = "";
    $rest_postal_code = "";

    $food_type = "";
    $food_type_enable_disable = "disabled=''";
}
?>
<?php

  $merchant_category_list= "";
  if (isset($merchant_category) && $merchant_category != "" && !empty($merchant_category)) {

      foreach ($merchant_category as $value) {
        $merchant_category_id = $value['merchant_category_id'];
        $category_name = $value['category_name'];


         if($merchant_category_id == $business_type ){
              $select = 'selected';
          }else{
              $select = '';
          }

        $merchant_category_list.= '<option value="'.$merchant_category_id.'" '.$select.'>'.$category_name.'</option>';
      }

      
  }
?>
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Profile</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
                <div class="breadcrumb-item">Profile</div>
            </div>
        </div>
        <div class="profile-body">
            <div class="row">
                <div class="col-4 col-md-4 col-lg-4 profile-details">
                    <form class="profile-form">
                        <div class="form-group">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label><img src="<?php echo $logo_image; ?>"/></label>
                                </div>
                                <div class="col-md-8">
                                    <h4 class="text-capitalize"><?php echo $rest_name;?></h4>
                                    <p><?php echo $res_description;?></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Email</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="email" name="email" value="<?php echo  $email;?>"  required="" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Phone no. </label>
                                </div>
                                <div class="col-md-7">
                                    <input type="tel" name="phone" value="+65 <?php echo $mobile;?>"  required="" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Address</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" name="address" value="<?php echo $rest_pin_address;?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Opening & Closing Time</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" name="time" value="<?php echo $open_time;?> - <?php echo $close_time;?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Break Time</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" name="break-time" value="<?php echo $break_start_time;?> - <?php echo $break_end_time;?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>What Restaurant Accept</label>
                                </div>
                                <div class="col-md-7">
                                    <?php echo $is_order_now_accept_value;?>
                                    <?php echo $is_self_pickup_accept_value;?>
                                    <?php echo $is_order_later_accept_value;?>
                                    <?php echo $is_dinein_accept_value;?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Who will handle the delivery</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="type" name="handle-delivery" value="By <?php echo $delivery_handled_by_value;?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <ul class="nav nav-pills tab-mdl radio_btns" role="tablist">
                                    <li class="nav-item">
                                        <a class="tb-dot nav-link active"  data-toggle="pill" href="#profile">
                                            <input type="radio" name="name" class="pr-tabInput" />         <label for="optProfile">Profile</label>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="tb-dot nav-link" data-toggle="pill" href="#editProfile">
                                            <input    name="name" type="radio" class="pr-tabInput" />
                                            <label for="optEditProfile">Edit Profile</label>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="tb-dot nav-link" data-toggle="pill" href="#changePassword">
                                            <input name="name" type="radio" class="pr-tabInput" />
                                            <label for="optChangePassword">Change Password</label>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-8">
                    <div class="tab-content">
                        <!-- Profile Part -->
                        <div id="profile" class="tab-pane active">
                            <div class="banner-image">
                                <img src="<?php echo $banner_image; ?>"/>
                            </div>
                            <div class="rating-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="rating-part">
                                            <h2 class="rating-title">
                                                William <span class="star-rating">5 <i class="fas fa-star"></i></span>
                                            </h2>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiselit sed do eiusd tempor incididuntut labore et dolore magn lorem ipsum dolor sit amet aliqua.</p>
                                            <p class="rating-date">
                                                22 Jun 2019 <span class="rating-btns"><!-- <button style="cursor: pointer;">Reply</button> --></span>
                                            </p>
                                            <h2 class="rating-title">
                                                John Doe <span class="star-rating">4 <i class="fas fa-star"></i></span>
                                            </h2>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiselit sed do eiusd tempor incididuntut labore et dolore magn lorem ipsum dolor sit amet aliqua.</p>
                                            <p class="rating-date">
                                                22 Jun 2019 <span class="rating-btns"><!-- <button style="cursor: pointer;">Reply</button> --></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-5">
                                        <div class="overall-rating">
                                            <h2 class="rating-title">
                                                Overall Rating <span class="star-rating"><i class="fas fa-star"></i> 4.5 </span>
                                            </h2>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiselit sed do eiusd tempor incididuntut labore et dolore magn lorem ipsum dolor sit amet aliqua.</p>
                                        </div>
                                        <div class="document-part">
                                            <h3>Document</h3>
                                            <?php echo $res_document;?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Profile Part End -->
                        <!-- Edit Profile -->
                        <div id="editProfile" class="tab-pane fade">
                            <div class="edit-profile-section">
                                <form>
                                    <h1>
                                        Edit Profile
                                        <span class="change-password-head">
                                            <button type="button" class="btn btn-primary change-password-btns mr-10">Save Changes</button> <button type="button" class="btn btn-secondary change-password_cancel">Cancel</button>
                                        </span>
                                    </h1>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                     <label style="font-size: 12px!important;font-weight: 800;">Logo  For Web and Mobile  (1000X350 px) <i class="fas fa-info-circle"data-toggle="tooltip" data-placement="top" title="Upload a square image that represents the business." style="color: #666; font-size: 17px;"></i></label> 
                                                </div>
                                                <div class="col-md-8 user-img">
                                                    <img
                                                        id="logo_image"
                                                        src="<?php echo $logo_image;?>"
                                                        alt=""
                                                    />
                                                    <div class="img-add">
                                                        <input type="file" id="file" name="file" onchange="document.getElementById('logo_image').src = window.URL.createObjectURL(this.files[0])" />
                                                        <label for="file"><i class="fas fa-pencil-alt"></i></label>
                                                    </div>
                                                     <span class="error" id="unfill_logo_image"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                     <label  style="font-size: 12px!important ;font-weight: 800;">Banner Image For Web (1920x360 px) <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top"  title="Upload an image which represents the business. This will be displayed on the store home page." style="color: #666; font-size: 17px;" ></i></label>
                                                </div>
                                                <div class="col-md-8 user-img">
                                                    <img
                                                        id="banner_image"
                                                        src="<?php echo $banner_image;?>"
                                                        alt=""
                                                    />
                                                    <div class="img-add">
                                                        <input type="file" id="file2" name="file" onchange="document.getElementById('banner_image').src = window.URL.createObjectURL(this.files[0])" />
                                                        <label for="file2"><i class="fas fa-pencil-alt"></i></label>
                                                    </div>
                                                    <span class="error" id="unfill_banner_image"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label  style="font-size: 12px!important ;font-weight: 800;">Banner Image For Mobile Web (768x384 px) <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top"  title="Upload an image which represents the business. This will be displayed on the store home page." style="color: #666; font-size: 17px;" ></i></label>
                                                </div>
                                                <div class="col-md-8 user-img">
                                                    <img
                                                        id="mobile_banner_image"
                                                        src="<?php echo $mobile_banner_image;?>"
                                                        alt=""
                                                    />
                                                    <div class="img-add">
                                                        <input type="file" id="file3" name="file" onchange="document.getElementById('mobile_banner_image').src = window.URL.createObjectURL(this.files[0])" />
                                                        <label for="file3"><i class="fas fa-pencil-alt"></i></label>
                                                    </div>
                                                     <span class="error" id="unfill_mobile_banner_image"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Restaurant Name</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" name="restaurant-name" placeholder="Enter restaurant name" value="<?php echo $rest_name;?>" required="" class="check_space text-capitalize" />

                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Description</label>
                                                </div>
                                                <div class="col-md-8">
                                                     <textarea maxlength="360" minlength="50" type="text" class="check_space description_length w-100" name="description" placeholder="Description" value="<?php echo $res_description;?>" required="" id="res_description"><?php echo $res_description;?></textarea> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Email</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="email" name="email" placeholder="Enter email" value="<?php echo  $email;?>" required="" class="check_space" />
                                                    <span class="error" id="res_invalid_email"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Phone Number</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="tel" name="phone-number" placeholder="Enter phone no" value="<?php echo  $mobile;?>" required="" class="check_space contact_number" maxlength="12" />
                                                    <span class="error" id="unfill_res_phone"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Address</label>
                                                </div>
                                                <div class="col-md-8 address-fields">
                                                    
                                                    <input type="text" name="unit-number" placeholder="Unit Number" value="<?php echo  $rest_unit_number;?>" required="" class="check_space" /><br />
                                                    <span class="error" id="unfill_unit_number"></span>

                                                    <input type="text" name="street-number" placeholder="Street Number" value="<?php echo  $rest_pin_address;?>" required="" class="check_space" /><br />
                                                    <span class="error" id="unfill_postal_code"></span>

                                                    <input type="text" name="postal-code" placeholder="Postal Code" value="<?php echo  $rest_postal_code;?>" required="" class="check_space" />
                                                    <span class="error" id="unfill_pin_address"></span>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Business Type</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select class="custom-select userstatus wv_filter_box_height form-control"  id="select_merchant_cateogry">
                                                        <option value="">Select Business Type</option>
                                                        <?php echo $merchant_category_list;?>
                                                    </select>
                                                    <span class="error" id="unselect_business_type"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Food Type</label>
                                                </div>
                                                <div class="col-md-8">
                                                     <select class="custom-select userstatus wv_filter_box_height form-control"  id="select_food_type" <?php echo $food_type_enable_disable;?>>
                                                    <option value="">Select Food Type</option>
                                                    <option value="1"  <?php if($food_type == 1){ echo 'selected';}?>>Restaurant</option>
                                                    <option value="2"  <?php if($food_type == 2){ echo 'selected';}?>>Homemade</option>
                                                </select>
                                                   <span class="error" id="unselect_food_type"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Opening & Closing Time</label>
                                                </div>
                                                <div class="col-md-8 open-time-label">
                                                    <label>Open Time</label>
                                                    <label>Close Time </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="all-days"> All Day</label>
                                                </div>
                                                <div class="col-md-8 time-sec">
                                                    <input type="time" id="open_time" name="open-time" value="<?php echo $open_time?>" required="" class="check_space" />
                                                    <span class="error" id="unfill_open_time"></span>

                                                    <input type="time" id="close_time" name="close-time" placeholder="Select Time" value="<?php echo $close_time?>" required="" class="check_space" />
                                                    <span class="error" id="unfill_close_time"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-8 open-time-label">
                                                    <label>Break Start</label>
                                                    <label>Break End </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="all-days"> Break Time</label>
                                                </div>
                                                <div class="col-md-8 time-sec">
                                                    <input type="time" id="break_start_time" name="break-start" value="<?php echo $break_start_time?>" required="" class="check_space" />
                                                     <span class="error" id="unfill_break_start_time"></span>

                                                    <input id="break_end_time" type="time" name="break-end" placeholder="Select Time" value="<?php echo $break_end_time?>" required="" class="check_space" />
                                                    <span class="error" id="unfill_break_end_time"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>What Restaurant Accept</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <label class="order-now-btns"><input type="checkbox" <?php if($merchant_rest_detail[0]['is_order_now_accept'] && $merchant_rest_detail[0]['is_order_now_accept'] == 1){echo 'checked=""';}?> /><span class="label">Order Now</span></label>
                                                    <label class="order-now-btns"><input type="checkbox" <?php if($merchant_rest_detail[0]['is_order_later_accept'] && $merchant_rest_detail[0]['is_order_later_accept'] == 1){echo 'checked=""';}?>/><span class="label">Order For Later</span></label>
                                                    <label class="order-now-btns"><input type="checkbox" <?php if($merchant_rest_detail[0]['is_self_pickup_accept'] && $merchant_rest_detail[0]['is_self_pickup_accept'] == 1){echo 'checked=""';}?>/><span class="label">Self Pickup</span></label>
                                                    <label class="order-now-btns"><input type="checkbox" <?php if($merchant_rest_detail[0]['is_dinein_accept'] && $merchant_rest_detail[0]['is_dinein_accept'] == 1){echo 'checked=""';}?>/><span class="label">Dine In</span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Who Will Handle TheDelivery</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <label class="handle-delivery">
                                                        By Restaurant <span class="commissions">Commission (2%)</span>
                                                        <input type="radio" value="1" <?php if($delivery_handled_by  == 0 || $delivery_handled_by  == 1 ){echo 'checked = ""';}?> name="delivery_handle_by"/>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                    <label class="handle-delivery">
                                                        By Kerala Eats <span class="commissions">Commission (5%)</span>
                                                        <input type="radio" name="delivery_handle_by"  value="2"  <?php if($delivery_handled_by  == 2){echo 'checked = ""';}?>/>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Documents</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="imgUp">
                                                        <div class="imagePreview" ></div>
                                                        <label class="btn btn-primary">
                                                            <img src="<?php echo base_url(); ?>assets/img/plusIcon.png" alt="EroticEvents" />
                                                            <input type="file" class="uploadFile img" value="Upload Photo" id="event_image_input"   style="width: 0px; height: 0px; overflow: hidden;" />
                                                            
                                                        </label>
                                                    </div>
                                                    <div class="imgUp">
                                                        <div class="imagePreview"></div>
                                                        <label class="btn btn-primary">
                                                            <img src="<?php echo base_url(); ?>assets/img/plusIcon.png" alt="EroticEvents" />
                                                            <input type="file" class="uploadFile img" value="Upload Photo" style="width: 0px; height: 0px; overflow: hidden;" />
                                                        </label>
                                                    </div>
                                                    <div class="imgUp">
                                                        <div class="imagePreview"></div>
                                                        <label class="btn btn-primary">
                                                            <img src="<?php echo base_url(); ?>assets/img/plusIcon.png" alt="EroticEvents" />
                                                            <input type="file" class="uploadFile img" value="Upload Photo" style="width: 0px; height: 0px; overflow: hidden;" />
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="form-group">
                                             <button type="button" class="btn btn-primary change-password-btns mr-10">Save Changes</button>
                                             <button type="button" class="btn btn-secondary change-password_cancel">Cancel</button>
                                          </div> -->
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Edit Profile End -->

                        <!-- Change Password -->
                        <div id="changePassword" class="tab-pane fade">
                            <div class="change-password-body">
                                
                                    <h1>
                                        Change Password
                                        <!-- <span class="change-password-head">
                                            <button type="button" class="btn btn-primary change-password-btns mr-10">Save Changes</button> <button type="button" class="btn btn-secondary change-password_cancel">Cancel</button>
                                        </span> -->
                                    </h1>
                                    <div class="card-body">
                                        <form method="POST" id="hide_after_match_old_pwd">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="password">Old Password</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input id="old_password" type="password" class="form-control pwstrength old_password" name="password" required placeholder="Enter old password"/>
                                                        <span class="error" id="old_password_error"></span>
                                                        <span class="text-success" id="old_password_success"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group text-right">
                                             <button type="button" id="MatchOldPasswordSubmit" class="btn btn-primary change-password-btns mr-10">Next</button>
                                             <a  href="<?php echo base_url();?>admin/profile" type="button" class="btn btn-secondary change-password_cancel">Cancel</a>
                                          </div>
                                        </form>
                                        <form method="POST" class ="d-none" id="ChangePasswordSubmit" method="POST" action="<?php echo base_url('admin/UpdatePassword')?>">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="password">New Password</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input id="new_password" type="password" class="form-control pwstrength np_password" name="new_password" required placeholder="Enter new password"/>
                                                        <span class="error" id="np_password_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="password-confirm">Confirm Password</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input id="confirm_password" type="password" class="form-control cnp_password" name="confirm_password" required placeholder="Enter confirm password"/>
                                                        <span class="text-success" id="confirm_password_success"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group text-right">
                                             <button type="submit" class="btn btn-primary change-password-btns mr-10">Save Changes</button>
                                             <a href="<?php echo base_url();?>admin/profile" type="button" class="btn btn-secondary change-password_cancel">Cancel</a>
                                          </div>
                                        </form>
                                    </div>
                            
                            </div>
                        </div>
                        <!-- Change Password End -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
