<?php 
if (isset($restaurant_admin_detail) && $restaurant_admin_detail[0] != "") {
     
        $restaurant_id = $restaurant_admin_detail[0]['id'];
        $merchant_number_id = $restaurant_admin_detail[0]['number_id'];
        $merchant_fullname = $restaurant_admin_detail[0]['fullname'];
        $rest_admin_email = $restaurant_admin_detail[0]['email'];
        $rest_admin_contact_no = $restaurant_admin_detail[0]['mobile'];
        $rest_pin_address = $restaurant_admin_detail[0]['rest_pin_address'];
        $rest_postal_code = $restaurant_admin_detail[0]['rest_postal_code'];
        $rest_unit_number = $restaurant_admin_detail[0]['rest_unit_number'];
        $rest_street_address = $restaurant_admin_detail[0]['rest_street_address'];//restaurant address
        $rest_name = $restaurant_admin_detail[0]['rest_name'];
        $business_type = $restaurant_admin_detail[0]['business_type'];
        $food_type = $restaurant_admin_detail[0]['food_type'];
        $res_admin_id = $restaurant_admin_detail[0]['admin_id'];
        $res_description = strip_tags($restaurant_admin_detail[0]['res_description']);
        $logo_image = $restaurant_admin_detail[0]['logo_image'];
        $res_logo_image = trim(base_url().$restaurant_admin_detail[0]['logo_image']);
        $banner_image = $restaurant_admin_detail[0]['banner_image'];
        $res_banner_image = trim(base_url().$restaurant_admin_detail[0]['banner_image']);
        $res_mobile_banner_image = trim(base_url().$restaurant_admin_detail[0]['mobile_banner_image']);
        $mobile_banner_image = $restaurant_admin_detail[0]['mobile_banner_image'];
        $res_time_mode = $restaurant_admin_detail[0]['time_mode'];//1 - for every day, 2 - for Specific day (if value 1 then open close and break time will be insert this table , if value 2 then open-close and break time will be insert in rest_time_daywise table )

        $open_time = $restaurant_admin_detail[0]['open_time'];
        $close_time = $restaurant_admin_detail[0]['close_time'];
        $break_start_time = $restaurant_admin_detail[0]['break_start_time'];
        $break_end_time = $restaurant_admin_detail[0]['break_end_time'];

        //offline online status
         $is_going_offline = $restaurant_admin_detail[0]['is_going_offline'];

          //if delivery handle by restaruant then per_km_charge
         $rest_del_per_km_charge = $restaurant_admin_detail[0]['per_km_charge'];// If delivery handled by restaurant then this will contain per kilo meter charge value  

        //restaurant document
        $uploaded_document = explode(',', $restaurant_admin_detail[0]['uploaded_document']);
        $res_document = "";
        if(count($uploaded_document) > 0)
        {
          foreach ($uploaded_document as $value)
          {
          
            if($value !="" ){
                $document_img = base_url($value);
             }else{
                $document_img = base_url('assets/images/default_document.png');
             }
               
             $res_document .= '<div class="col-md-3 admin-profile-img">
                                  <a href="'.$document_img.'" target="_blank"><img id="disp_img" src="'.$document_img.'" alt="Restaurant Documents" /></a>
                                  <div class="img-add">
                                     <input type="file" id="file" name="file"/>
                                  </div>
                               </div> ';
          }
        }    

        //commission setup
        $commission_type = $restaurant_admin_detail[0]['commission_type'];
        $commission_value = trim($restaurant_admin_detail[0]['commission_value']);

        $order_preparation_time = trim($restaurant_admin_detail[0]['preparation_time']);
        $order_delivery_time = trim($restaurant_admin_detail[0]['delivery_time']);

      // match restaurant_id and restaurant_admin_id  with db table -- IF START---
         // For logo image 
         $res_logo_image = $restaurant_admin_detail[0]['logo_image'];

         if($res_logo_image != "" && empty($header['user_data'])){
            $res_logo_image = base_url().$res_logo_image;
 
         }else{
             $res_logo_image =  base_url('assets/img/avatar/avatar-1.png');
         }

         //For Banner image
         $res_banner_image = $restaurant_admin_detail[0]['banner_image'];

         if($res_banner_image != "" && empty($header['user_data'])){
             $res_banner_image = base_url().$res_banner_image;
 
         }else{
               $res_banner_image =  base_url('assets/img/avatar/avatar-1.png');
         }

         //For Banner image
          $res_mobile_banner_image = $restaurant_admin_detail[0]['mobile_banner_image'];

         if($res_mobile_banner_image != "" && empty($header['user_data'])){
             $res_mobile_banner_image = base_url().$res_mobile_banner_image;
 
         }else{
               $res_mobile_banner_image =  base_url('assets/img/avatar/avatar-1.png');
         }


       
        //Day wise  data show
         if(!empty($day_wise_rest_time)){

            //open -cose time-  start--------------------
          if($day_wise_rest_time[0]['mon_open_close_time'] != ""){
             $mon_open_close_time =  explode('-',$day_wise_rest_time[0]['mon_open_close_time']);
             $mon_open_time = $mon_open_close_time[0];
             $mon_close_time = $mon_open_close_time[1];
          }else{
             $mon_open_time = "";
             $mon_close_time = "";
          }


           if($day_wise_rest_time[0]['tue_open_close_time'] != ""){
             $tue_open_close_time =  explode('-',$day_wise_rest_time[0]['tue_open_close_time']);
             $tue_open_time = $tue_open_close_time[0];
             $tue_close_time = $tue_open_close_time[1];
           }else{
             $tue_open_time = "";
             $tue_close_time = "";
           }

           if($day_wise_rest_time[0]['wed_open_close_time'] != ""){
              $wed_open_close_time =  explode('-',$day_wise_rest_time[0]['wed_open_close_time']);
              $wed_open_time = $wed_open_close_time[0];
              $wed_close_time = $wed_open_close_time[1];
           }else{
             $wed_open_time = "";
             $wed_close_time = "";
           }

           if($day_wise_rest_time[0]['thu_open_close_time'] != ""){
               $thu_open_close_time =  explode('-',$day_wise_rest_time[0]['thu_open_close_time']);
            $thu_open_time = $thu_open_close_time[0];
            $thu_close_time = $thu_open_close_time[1];
           }else{
             $thu_open_time = "";
             $thu_close_time = "";
           }

          if($day_wise_rest_time[0]['fri_open_close_time'] != ""){
             $fri_open_close_time =  explode('-',$day_wise_rest_time[0]['fri_open_close_time']);
            $fri_open_time = $fri_open_close_time[0];
            $fri_close_time = $fri_open_close_time[1];
           }else{
             $fri_open_time = "";
             $fri_close_time = "";
           }
           
           if($day_wise_rest_time[0]['sat_open_close_time'] != ""){
             $sat_open_close_time =  explode('-',$day_wise_rest_time[0]['sat_open_close_time']);
            $sat_open_time = $sat_open_close_time[0];
            $sat_close_time = $sat_open_close_time[1];

           }else{
             $sat_open_time = "";
             $sat_close_time = "";
           }
           
            if($day_wise_rest_time[0]['sun_open_close_time'] != ""){
              $sun_open_close_time =  explode('-',$day_wise_rest_time[0]['sun_open_close_time']);
            $sun_open_time = $sun_open_close_time[0];
            $sun_close_time = $sun_open_close_time[1];

           }else{
             $sun_open_time = "";
             $sun_close_time = "";
           }
            //open -cose time-  end--------------------

            //Break Start end  time -  start--------------------
            $mon_break_status = $day_wise_rest_time[0]['mon_break_status'];//0 - no(means admin dont want to add break time , 1 - admin want to add break time)
            if($day_wise_rest_time[0]['mon_break_start_end_time'] !="" ){
               $mon_break_start_end_time =  explode('-',$day_wise_rest_time[0]['mon_break_start_end_time']);
              $mon_break_start_time = $mon_break_start_end_time[0];
              $mon_break_end_time = $mon_break_start_end_time[1];
            }else{
               $mon_break_start_time  = "";
               $mon_break_end_time =  "";
            }
           
              $tue_break_status = $day_wise_rest_time[0]['tue_break_status'];//0 - no(means admin dont want to add break time , 1 - admin want to add break time)
             if($day_wise_rest_time[0]['tue_break_start_end_time'] !="" ){
              $tue_break_start_end_time =  explode('-',$day_wise_rest_time[0]['tue_break_start_end_time']);
              $tue_break_start_time = $tue_break_start_end_time[0];
              $tue_break_end_time = $tue_break_start_end_time[1];
            }else{
              $tue_break_start_time = "";
              $tue_break_end_time = "";
            }

            
            $wed_break_status = $day_wise_rest_time[0]['wed_break_status'];//0 - no(means admin dont want to add break time , 1 - admin want to add break time)
            if($day_wise_rest_time[0]['wed_break_start_end_time'] !="" ){
              $wed_break_start_end_time =  explode('-',$day_wise_rest_time[0]['wed_break_start_end_time']);
              $wed_break_start_time = $wed_break_start_end_time[0];
              $wed_break_end_time = $wed_break_start_end_time[1];
            }else{
              $wed_break_start_time  = "";
              $wed_break_end_time  = "";
            }

             $thu_break_status = $day_wise_rest_time[0]['thu_break_status'];//0 - no(means admin dont want to add break time , 1 - admin want to add break time)
             if($day_wise_rest_time[0]['thu_break_start_end_time'] !="" ){
               $thu_break_start_end_time =  explode('-',$day_wise_rest_time[0]['thu_break_start_end_time']);
              $thu_break_start_time = $thu_break_start_end_time[0];
              $thu_break_end_time = $thu_break_start_end_time[1];
             }else{
              $thu_break_start_time  = "";
              $thu_break_end_time = "";
             }

               $fri_break_status = $day_wise_rest_time[0]['fri_break_status'];//0 - no(means admin dont want to add break time , 1 - admin want to add break time)
              if($day_wise_rest_time[0]['fri_break_start_end_time'] !="" ){
                $fri_break_start_end_time =  explode('-',$day_wise_rest_time[0]['fri_break_start_end_time']);
                $fri_break_start_time = $fri_break_start_end_time[0];
                $fri_break_end_time = $fri_break_start_end_time[1];
              }else{
                $fri_break_start_time  = "";
                $fri_break_end_time = "";
             }

              $sat_break_status = $day_wise_rest_time[0]['sat_break_status'];//0 - no(means admin dont want to add break time , 1 - admin want to add break time)
              if($day_wise_rest_time[0]['sat_break_start_end_time'] !="" ){
                 $sat_break_start_end_time =  explode('-',$day_wise_rest_time[0]['sat_break_start_end_time']);
                  $sat_break_start_time = $sat_break_start_end_time[0];
                  $sat_break_end_time = $sat_break_start_end_time[1];
              }else{
                 $sat_break_start_time  = "";
                 $sat_break_end_time = "";
              }
            
              $sun_break_status = $day_wise_rest_time[0]['sun_break_status'];//0 - no(means admin dont want to add break time , 1 - admin want to add break time)
              if($day_wise_rest_time[0]['sun_break_start_end_time']!="" ){
                 $sun_break_start_end_time =  explode('-',$day_wise_rest_time[0]['sun_break_start_end_time']);
                  $sun_break_start_time = $sun_break_start_end_time[0];
                  $sun_break_end_time = $sun_break_start_end_time[1];
              }else{
                 $sun_break_start_time  = "";
                 $sun_break_end_time = "";
              }
          //Break Start end  time-  end--------------------


          //close day of restaruant ------START-------------
             $mon_close_status = $day_wise_rest_time[0]['mon_close_status'];  // 2- on this day restaurant will be closed, 0,1 - restaurant will be opend
             $tue_close_status = $day_wise_rest_time[0]['tue_close_status'];  // 2- on this day restaurant will be closed, 0,1 - restaurant will be opend
             $wed_close_status = $day_wise_rest_time[0]['wed_close_status'];  // 2- on this day restaurant will be closed, 0,1 - restaurant will be opend
             $thu_close_status = $day_wise_rest_time[0]['thu_close_status'];  // 2- on this day restaurant will be closed, 0,1 - restaurant will be opend
             $fri_close_status = $day_wise_rest_time[0]['fri_close_status'];  // 2- on this day restaurant will be closed, 0,1 - restaurant will be opend
             $sat_close_status = $day_wise_rest_time[0]['sat_close_status'];  // 2- on this day restaurant will be closed, 0,1 - restaurant will be opend
             $sun_close_status = $day_wise_rest_time[0]['sun_close_status'];  // 2- on this day restaurant will be closed, 0,1 - restaurant will be opend
          //close day of restaruant ------END-------------
            
         }else{
              //open close time ----start-----
              $mon_open_time = "";
              $mon_close_time = "";

              $tue_open_time = "";
              $tue_close_time = "";

              $wed_open_time = "";
              $wed_close_time = "";

              $thu_open_time = "";
              $thu_close_time = "";

              $fri_open_time = "";
              $fri_close_time = "";

              $sat_open_time  = "";
              $sat_close_time = "";

              $sun_open_time = "";
              $sun_close_time = "";

              //open close time ----end-----


              //break time----start---
              $mon_break_status = 0;
              $mon_break_start_time  = "";
              $mon_break_end_time = "";

              $tue_break_status = 0;
              $tue_break_start_time  = "";
              $tue_break_end_time  = "";

              $wed_break_status = 0;
              $wed_break_start_time  = "";
              $wed_break_end_time  = "";

              $thu_break_status = 0;
              $thu_break_start_time  = "";
              $thu_break_end_time  = "";

              $fri_break_status = 0;
              $fri_break_start_time  = "";
              $fri_break_end_time  = "";

              $sat_break_status = 0;
              $sat_break_start_time   = "";
              $sat_break_end_time  = "";

              $sun_break_status = 0;
              $sun_break_start_time  = "";
              $sun_break_end_time  = "";
              //break time----end---

              //close day of restaurant----start---
              $mon_close_status = 1;// 2- on this day restaurant will be closed, 0,1 - restaurant will be opend

              $tue_close_status = 1;// 2- on this day restaurant will be closed, 0,1 - restaurant will be opend

              $wed_close_status = 1;// 2- on this day restaurant will be closed, 0,1 - restaurant will be opend

              $thu_close_status = 1;// 2- on this day restaurant will be closed, 0,1 - restaurant will be opend

              $fri_close_status = 1;// 2- on this day restaurant will be closed, 0,1 - restaurant will be opend

              $sat_close_status = 1;// 2- on this day restaurant will be closed, 0,1 - restaurant will be opend

              $sun_close_status = 1;// 2- on this day restaurant will be closed, 0,1 - restaurant will be opend
              //close day of restaurant----end---
         }
         

}else{
   redirect(base_url('admin/restaurant_list'));
}

?>
<?php
  if (isset($merchant_category) && $merchant_category != "" && !empty($merchant_category)) {

      foreach ($merchant_category as $value) {
        $merchant_category_id = $value['merchant_category_id'];
        $category_name = $value['category_name'];


         if($merchant_category_id == $business_type ){
              $merchant_category = $category_name;
          }
      }
  }
?>
<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Show Restaurant</h1>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Show Restaurant</div>
         </div>
      </div>
      <div class="add-restaurant">
         <form method="POST">
            <div class="card-body">
               <div class="form-group admin-input-field">
                  <div class="row">
                     <div class="col-md-3">
                        <label>Merchant ID</label>
                        <input type="text" name="name"   value="<?php echo $merchant_number_id;?>" required="" disabled="" style="cursor: not-allowed;" />
                     </div>
                     <div class="col-md-3">
                        <label>Name</label>
                        <input type="text" name="name" value="<?php echo $merchant_fullname;?>" disabled="" required="" />
                     </div>
                     <div class="col-md-3">
                        <label>Email</label>
                        <input type="email" name="email"  value="<?php echo $rest_admin_email;?>" disabled="" required="" />
                     </div>
                     <div class="col-md-3">
                        <label>Phone </label>
                        <input type="tel" name="phone"   value="<?php echo $rest_admin_contact_no;?>" disabled="" required="" class="contact_number" />
                     </div>
                  </div>
               </div>
               <div class="form-group admin-input-field">
                  <div class="row">
                     <div class="col-md-3">
                        <label>Unit Number</label>
                        <input type="text" name="address"  value="<?php echo $rest_unit_number;?>" disabled="" required="" />
                     </div>
                     <div class="col-md-3">
                        <label>Postal code </label>
                        <input type="text" name="display-address"  value="<?php echo $rest_postal_code;?>" disabled="" required="" />
                     </div>
                     <div class="col-md-6">
                        <label>Street Address </label>
                        <input type="text" name="address"   value="<?php echo $rest_pin_address;?>" disabled="" required="" />
                     </div>
                  <!--    <div class="col-md-3">
                        <label>Street Address </label>
                        <input type="text" name="display-address"  value="<?php //echo $rest_street_address;?>" disabled="" required="" />
                     </div> -->
                  </div><br>
                  <div class="row"> 
                      <div class="col-md-3">
                        <label>Restaurant Name </label>
                        <input type="text" name="restaurant-name" value="<?php echo $rest_name;?>" disabled="" required="" />
                     </div>
                      <div class="col-md-3">
                        <label>Business Type </label>
                        <input type="text" name="restaurant-name"  value="<?php echo $merchant_category;?>" disabled="" required="" />
                     </div>
                      <div class="col-md-3">
                        <label>Food Type</label>
                        <input type="text" name="restaurant-name"  value="<?php if($food_type == 1){ echo 'Restaurant';} if($food_type == 2){ echo 'Kitchen';}?> " disabled="" required="" />
                     </div>
                      <div class="col-md-3">
                        <label>Menu</label>
                        <a  href="<?php echo base_url()."admin/products/0/all/all/all/all/all/".$restaurant_id."/".$rest_current_category_id."" //paramert will be change if change in code or realted to link on products page ?>" class="btn btn-primary change-password-btns mr-10" target="_blank">See Catalogue</a>
                     </div>
                  </div><br>
                   <div class="row">
                      <div class="col-md-8"><br>
                        <label>Description (Max 360 Characters)</label>
                       <textarea rows="10" type="text" name="description" value="" required="" disabled="" style="height: 47px;"><?php echo $res_description?></textarea> 
                     </div>
                     <div class="col-md-4 text-center"><br>
                      <label>Is Receiving order (Offline/ Online Status)</label>
                       <label class="switch promocode-status" data-children-count="1">
                            <input type="checkbox" id="rest_going_online" <?php if($is_going_offline == 0){echo 'checked="checked"'; echo 'data-toggle="modal" data-target="#offline_online_restaurant_popup" data-backdrop="static" data-keyboard="false"'; echo ' value="1"';}else {echo ' value="0"';}?>>
                            <span class="slider round"></span>
                        </label>
                     </div>
                  </div>
               </div>
                <div class="form-group">
                  <div class="row">
                     <div class="col-md-3 admin-profile-img">
                        <label>Logo <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="Upload a square image that represents the business." style="color: #666; font-size: 17px;"></i></label>
                        <a href="<?php  echo $res_logo_image ;?>" target="_blank"><img id="disp_img" src="<?php  echo $res_logo_image ;?>" alt="Restaurant Logo image" /></a>
                     </div>
                     <div class="col-md-3 admin-profile-img">
                        <label> Banner Image For Web <i
                           class="fas fa-info-circle"
                           data-toggle="tooltip"
                           data-placement="top"
                           title=" Upload an image which represents the business. This will be displayed on the store home page."
                           style="color: #666; font-size: 17px;"
                           ></i></label>
                         <a href="<?php  echo $res_banner_image ;?>" target="_blank"> <img id="disp_img" src="<?php echo $res_banner_image;?>" alt="Restaurant Banner image" /></a>
                        
                     </div>
                      <div class="col-md-3 admin-profile-img">
                        <label>Mobile Banner Image <i
                           class="fas fa-info-circle"
                           data-toggle="tooltip"
                           data-placement="top"
                           title=" Upload an image which represents the business. This will be displayed on the store home page."
                           style="color: #666; font-size: 17px;"
                           ></i></label>
                         <a href="<?php  echo $res_mobile_banner_image ;?>" target="_blank"> <img id="disp_img"
                           src="<?php  echo $res_mobile_banner_image ;?>"
                           alt="Restaurant Banner imag"
                           /></a>
                     </div> 
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                      <label>Documents</label>
                      <?php echo  $res_document;?>
                  </div>
               </div>
               <div class="form-group admin-input-field">
                  <div class="row">
                     <label>Delivery Per KM Charge</label>   
                      <div class="col-md-4"> 
                          <input type="text" name="delivery_per_km_charge" maxlength="6" value="<?php echo $rest_del_per_km_charge;?>" required=""  placeholder=""/>
                          <span class="error" id="unfill_delivery_charge"></span>
                      </div>
                       <div class="col-md-4">
                          <button type="button" class="btn btn-primary change-password-btns mr-10 chargesavebtn" id="rest_delivery_per_km_charge_submit">Save</button>
                       </div>
                  </div>
               </div>
                <div class="form-group admin-input-field">
                  <div class="row">
                      <div class="col-md-4"> 
                         <label>Order Preparation Time (in minutes)</label>   
                          <input type="text" name="rest_order_preparation_time" maxlength="6" value="<?php echo $order_preparation_time;?>" required=""  placeholder="EX. 20, 80,120 etc."/>
                          <span class="error" id="unfill_preparation_time"></span>
                      </div>
                      <div class="col-md-4"> 
                          <label>Order Delivery Time (in minutes)</label>   
                          <input type="text" name="rest_order_delivery_time" maxlength="6" value="<?php echo $order_delivery_time;?>" required=""  placeholder="EX. 20, 80,120 etc."/>
                          <span class="error" id="unfill_delivery_time"></span>
                      </div>
                       <div class="col-md-4">
                          <button type="button" class="btn btn-primary change-password-btns mr-10 saveButtonSpace chargesavebtn" id="rest_order_prepration_and_delivery_time_submit">Save</button>
                       </div>
                  </div>
               </div>
                <div class="form-group admin-input-field">
                   <div class="row">
                     <label>Commission Setup</label>
                      <div class="col-md-6">
                            <div class="d-flex">
                               <label class="enabled-label">Set fixed Amount (S$)
                               <input type="radio" value="1" name="rest_commission_type" <?php if($commission_type == 1 || $commission_type == 0){echo "checked='checked'";}?>>
                               <span class="checkmark"></span>
                               </label>
                               <label class="enabled-label">Set a Percentage (%)
                               <input type="radio"  name="rest_commission_type" value="2" <?php if($commission_type == 2){echo "checked='checked'";}?>>
                               <span class="checkmark"></span>
                               </label>
                            </div>
                             <span class="error" id="unfill_rest_commission_type"></span>
                      </div>
                       <div class="col-md-3">
                           <input type="text" name="rest_commission_value" maxlength="3" value="<?php echo $commission_value;?>" required="" />
                            <span class="error" id="unfill_rest_commission_value"></span>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary change-password-btns mr-10 chargesavebtn" id="rest_commission_submit">Save</button>
                        </div>
                   </div>
                </div>
              <div class="form-group admin-input-field">
                  <div class="row">
                     <label>Open And Close Time</label>
                     <div class="col-md-5">
                        <div class="d-flex">
                             <label class="enabled-label">Every Day
                               <input type="radio" value="1" class="rest_time_mode" id="rest_time_mode" name="rest_time_mode" <?php if($res_time_mode ==1 || $res_time_mode ==0){ echo 'checked=""';}?>>
                                  <span class="checkmark"></span>
                               </label>
                               <label class="enabled-label">Specific Days
                               <input type="radio"  class="rest_time_mode" id="rest_open_close_time_specific_day" name="rest_time_mode" value="2" <?php if($res_time_mode == 2){ echo 'checked=""';}?>>
                                   <span class="checkmark"></span>
                               </label>
                        </div>
                     </div>
                      <div class="col-md-7">
                        <div class="<?php if($res_time_mode == 2){ echo 'd-none';}?>" id="show_every_day_mode">
                          <table class="table">
                              <tbody class="cart-item-details">
                                   <tr>
                                      <th>All Day</th>
                                      <td>Open Time 
                                         <input type="time" class="form-control"  id="open_time" value="<?php  echo "$open_time"; ?>"/>
                                          <span class="error" id="unfill_open_time"></span>
                                      </td>
                                      <td>Close Time
                                          <input type="time" class="form-control" id="close_time" name="close-time" placeholder="Select Time" required="" class="check_space"  value="<?php  echo "$close_time"; ?>">
                                          <span class="error" id="unfill_close_time"></span>
                                      </td>
                                   </tr>
                                   <!-----Break on Mon--- START----->
                                    <tr id="break_time">
                                        <th>Break </th>
                                        <td>Start Time 
                                           <input type="time" class="form-control" id="break_start_time" name="open-time" value="<?php  echo "$break_start_time"; ?>" required="" class="check_space"/>
                                        </td>
                                        <td>End Time
                                            <input type="time" id="break_end_time" class="form-control" name="close-time" placeholder="Select Time" value="<?php  echo "$break_end_time"; ?>" required="" class="check_space"/>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <!-----Break on Mon--- END----->
                                </tbody>
                              </table>
                          </div>
                         
                     <div class="<?php if($res_time_mode == 1 || $res_time_mode == 0){ echo 'd-none';}?>" id="show_speecific_day_mode">
                        <table class="table">
                            <tbody class="cart-item-details">
                                 <tr>
                                    <th>Mon</th>
                                    <td>Open Time 
                                       <input type="time" class="form-control"  id="mon_open_time" value="<?php  echo "$mon_open_time"; ?>"/>
                                        <span class="error" id="unfill_mon_open_time"></span>
                                    </td>
                                    <td>Close Time
                                        <input type="time" class="form-control" id="mon_close_time" name="close-time" placeholder="Select Time" required="" class="check_space"  value="<?php  echo "$mon_close_time"; ?>">
                                        <span class="error" id="unfill_mon_close_time"></span>
                                    </td>
                                    <td>Break <br>
                                        <button <?php if($mon_close_status == 2) {echo 'disabled=""';} ?> type="button" class="btn <?php if( $mon_break_status == 2 || $mon_break_status == 0){echo 'btn-primary';}else{ echo 'btn-success';}?> break_time" mode="<?php if( $mon_break_status == 0 || $mon_break_status == 2){echo '2';}else{ echo '1';}?>" id="mon">  <?php if( $mon_break_status == 0 || $mon_break_status == 2){echo 'No <i class="fas fa-caret-down"></i>';}else{ echo 'Yes <i class="fas fa-caret-up"></i>';}?></button>
                                    </td>
                                   <td>Close Day
                                         <label class="enabled-label"> <br>
                                            <input type="checkbox" id="mon_close_status" name="close_day" value="<?php  echo $mon_close_status;?>" <?php if($mon_close_status == 2){echo 'checked=""';}?>>
                                               <span class="checkmark_check"></span>
                                         </label>
                                    </td>
                                 </tr>
                                 <!-----Break on Mon--- START----->
                                <tr id="mon_break_tr" class="<?php if( $mon_break_status == 0 || $mon_break_status == 2){echo 'd-none';}?>">
                                    <th> </th>
                                    <td>Start Time 
                                       <input type="time" class="form-control" id="mon_break_start_time" name="open-time"   value="<?php  echo "$mon_break_start_time"; ?>"required="" class="check_space">
                                       <span class="error" id="unfill_mon_break_start_time"></span>
                                    </td>
                                    <td>End Time
                                        <input type="time" class="form-control" name="close-time" placeholder="Select Time" value="<?php  echo "$mon_break_end_time"; ?>" id="mon_break_end_time" required="" class="check_space">
                                         <span class="error" id="unfill_mon_break_end_time"></span>
                                    </td>
                                     
                                </tr>
                                <!-----Break on Mon--- END----->
                                <tr>
                                    <th>Tue</th>
                                    <td>Open Time 
                                       <input type="time" class="form-control" id="tue_open_time" name="open-time" required="" class="check_space" value="<?php  echo "$tue_open_time"; ?>">
                                        <span class="error" id="unfill_tue_open_time"></span>
                                    </td>
                                    <td>Close Time
                                        <input type="time" class="form-control" id="tue_close_time" name="close-time" placeholder="Select Time"  value="<?php  echo "$tue_close_time"; ?>" required="" class="check_space">
                                        <span class="error" id="unfill_tue_close_time"></span>
                                    </td>
                                    <td>Break <br>
                                        <button  <?php if($tue_close_status == 2) {echo 'disabled=""';} ?> type="button" class="btn <?php if( $tue_break_status == 2 || $tue_break_status == 0){echo 'btn-primary';}else{ echo 'btn-success';}?> break_time" mode="<?php if( $tue_break_status == 0 || $tue_break_status == 2){echo '2';}else{ echo '1';}?>" id="tue"> <?php if( $tue_break_status == 0 || $tue_break_status == 2){echo 'No <i class="fas fa-caret-down"></i>';}else{ echo 'Yes <i class="fas fa-caret-up"></i>';}?></button>
                                    </td>
                                    <td>Close Day
                                         <label class="enabled-label"> <br>
                                            <input type="checkbox" name="close_day"  id="tue_close_status" value="<?php  echo $tue_close_status;?>" <?php if($tue_close_status == 2){echo 'checked=""';}?>>
                                               <span class="checkmark_check"></span>
                                         </label>
                                    </td>
                                 </tr>
                                <!-----Break on Tue--- START----->
                               <tr id="tue_break_tr" class="<?php if(  $tue_break_status == 0 || $tue_break_status == 2){echo 'd-none';}?> ">
                                    <th> </th>
                                    <td>Start Time 
                                       <input type="time" class="form-control" id="tue_break_start_time" name="open-time" value="<?php  echo "$tue_break_start_time"; ?>"required="" class="check_space">
                                        <span class="error" id="unfill_tue_break_start_time"></span>
                                    </td>
                                    <td>End Time
                                        <input type="time" class="form-control" name="close-time" placeholder="Select Time" value="<?php  echo "$tue_break_end_time"; ?>" id="tue_break_end_time" required="" class="check_space">
                                         <span class="error" id="unfill_tue_break_end_time"></span>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <!-----Break on Tue--- END----->
                                 <tr>
                                    <th>Wed</th>
                                    <td>Open Time 
                                       <input type="time" class="form-control" id="wed_open_time" name="open-time" value="<?php  echo "$wed_open_time"; ?>" required="" class="check_space">
                                         <span class="error" id="unfill_wed_open_time"></span>
                                    </td>
                                    <td>Close Time
                                        <input type="time" class="form-control" id="wed_close_time" name="close-time" placeholder="Select Time"  value="<?php  echo "$wed_close_time"; ?>" required="" class="check_space">
                                          <span class="error" id="unfill_wed_close_time"></span>
                                    </td>
                                    <td>Break <br>
                                        <button  <?php if($wed_close_status == 2) {echo 'disabled=""';} ?> type="button" class="btn <?php if( $wed_break_status == 2 || $wed_break_status == 0){echo 'btn-primary';}else{ echo 'btn-success';}?> break_time" mode="<?php if($wed_break_status == 0 || $wed_break_status == 2){echo '2';}else{ echo '1';}?>" id="wed"> <?php if( $wed_break_status == 0 || $wed_break_status == 2){echo 'No <i class="fas fa-caret-down"></i>';}else{ echo 'Yes <i class="fas fa-caret-up"></i>';}?></button>
                                    </td>
                                    <td>Close Day
                                         <label class="enabled-label"> <br>
                                            <input type="checkbox" id="wed_close_status" name="close_day" value="<?php  echo $wed_close_status;?>" <?php if($wed_close_status == 2){echo 'checked=""';}?>>
                                               <span class="checkmark_check"></span>
                                         </label>
                                    </td>
                                 </tr>
                                <!-----Break on Wed--- START----->
                                <tr id="wed_break_tr" class="<?php if( $wed_break_status == 0 || $wed_break_status == 2){echo 'd-none';}?>">
                                    <th> </th>
                                    <td>Start Time 
                                       <input type="time" class="form-control" id="wed_break_start_time" name="open-time" value="<?php  echo "$wed_break_start_time"; ?>" required="" class="check_space">
                                        <span class="error" id="unfill_wed_break_start_time"></span>
                                    </td>
                                    <td>End Time
                                        <input type="time" class="form-control" name="close-time" placeholder="Select Time" value="<?php  echo "$wed_break_end_time"; ?>" id="wed_break_end_time" required="" class="check_space">
                                         <span class="error" id="unfill_wed_break_end_time"></span>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <!-----Break on Wed--- END----->
                                 <tr>
                                    <th>Thu</th>
                                    <td>Open Time 
                                       <input type="time" class="form-control" id="thu_open_time" name="open-time" value="<?php  echo "$thu_open_time"; ?>" required="" class="check_space">
                                        <span class="error" id="unfill_thu_open_time"></span>
                                    </td>
                                    <td>Close Time
                                        <input type="time" class="form-control" id="thu_close_time" name="close-time" placeholder="Select Time" value="<?php  echo "$thu_close_time"; ?>" required="" class="check_space">
                                         <span class="error" id="unfill_thu_close_time"></span>
                                    </td>
                                    <td>Break <br>
                                        <button <?php if($thu_close_status == 2) {echo 'disabled=""';} ?>  type="button" class="btn <?php if( $thu_break_status == 2 || $thu_break_status == 0){echo 'btn-primary';}else{ echo 'btn-success';}?> break_time" mode="<?php if( $thu_break_status == 0 || $thu_break_status == 2){echo '2';}else{ echo '1';}?>" id="thu"> <?php if( $thu_break_status == 0 || $thu_break_status == 2){echo 'No <i class="fas fa-caret-down"></i>';}else{ echo 'Yes <i class="fas fa-caret-up"></i>';}?></button>
                                    </td>
                                    <td>Close Day
                                         <label class="enabled-label"> <br>
                                            <input type="checkbox" id="thu_close_status"  name="close_day" value="<?php  echo $thu_close_status;?>" <?php if($thu_close_status == 2){echo 'checked=""';}?>>
                                               <span class="checkmark_check"></span>
                                         </label>
                                    </td>
                                 </tr>
                                <!-----Break on Thu--- START----->
                                <tr id="thu_break_tr" class="<?php if(  $thu_break_status == 0 || $thu_break_status == 2){echo 'd-none';}?>">
                                    <th> </th>
                                    <td>Start Time 
                                       <input type="time" class="form-control" id="thu_break_start_time" name="open-time" value="<?php  echo "$thu_break_start_time"; ?>" required="" class="check_space">
                                       <span class="error" id="unfill_thu_break_start_time"></span>
                                    </td>
                                    <td>End Time
                                        <input type="time" class="form-control" name="close-time" placeholder="Select Time" value="<?php  echo "$thu_break_end_time"; ?>"  id="thu_break_end_time" required="" class="check_space">
                                        <span class="error" id="unfill_thu_break_end_time"></span>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <!-----Break on Thu--- END----->
                                 <tr>
                                    <th>Fri</th>
                                    <td>Open Time 
                                       <input type="time" class="form-control" id="fri_open_time" name="open-time" value="<?php  echo "$fri_open_time"; ?>"  required="" class="check_space">
                                        <span class="error" id="unfill_fri_open_time"></span>
                                    </td>
                                    <td>Close Time
                                        <input type="time" class="form-control"  id="fri_close_time" name="close-time" placeholder="Select Time" value="<?php  echo "$fri_close_time"; ?>" required="" class="check_space">
                                         <span class="error" id="unfill_fri_close_time"></span>
                                    </td>
                                    <td>Break <br>
                                        <button <?php if($fri_close_status == 2) {echo 'disabled=""';} ?>  type="button" class="btn <?php if( $fri_break_status == 2 || $fri_break_status == 0){echo 'btn-primary';}else{ echo 'btn-success';}?> break_time" mode="<?php if( $fri_break_status == 0 || $fri_break_status == 2){echo '2';}else{ echo '1';}?>" id="fri"> <?php if( $fri_break_status == 0 || $fri_break_status == 2){echo 'No <i class="fas fa-caret-down"></i>';}else{ echo 'Yes <i class="fas fa-caret-up"></i>';}?></button>
                                    </td>
                                    <td>Close Day
                                         <label class="enabled-label"> <br>
                                            <input type="checkbox" id="fri_close_status"  name="close_day" value="<?php  echo $fri_close_status;?>" <?php if($fri_close_status == 2){echo 'checked=""';}?>>
                                               <span class="checkmark_check"></span>
                                         </label>
                                    </td>
                                 </tr>
                                <!-----Break on Fri--- START----->
                                 <tr id="fri_break_tr" class="<?php if( $fri_break_status == 0 || $fri_break_status == 2){echo 'd-none';}?>">
                                    <th> </th>
                                    <td>Start Time 
                                       <input type="time" class="form-control" id="fri_break_start_time" name="open-time"  value="<?php  echo "$fri_break_start_time"; ?>" required="" class="check_space">
                                       <span class="error" id="unfill_fri_break_start_time"></span>
                                    </td>
                                    <td>End Time
                                        <input type="time" class="form-control" name="close-time" placeholder="Select Time" value="<?php  echo "$fri_break_end_time"; ?>" id="fri_break_end_time" required="" class="check_space">
                                        <span class="error" id="unfill_fri_break_end_time"></span>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <!-----Break on Fri--- END-----> <tr>
                                    <th>Sat</th>
                                    <td>Open Time 
                                       <input type="time" class="form-control" id="sat_open_time" name="open-time" value="<?php  echo "$sat_open_time"; ?>" required="" class="check_space">
                                        <span class="error" id="unfill_sat_open_time"></span>
                                    </td>
                                    <td>Close Time
                                        <input type="time" class="form-control" id="sat_close_time" name="close-time" placeholder="Select Time" value="<?php  echo "$sat_close_time"; ?>" required="" class="check_space">
                                         <span class="error" id="unfill_sat_close_time"></span>
                                    </td>
                                    <td>Break <br>
                                        <button <?php if($sat_close_status == 2) {echo 'disabled=""';} ?> type="button" class="btn <?php if( $sat_break_status == 2 || $sat_break_status == 0){echo 'btn-primary';}else{ echo 'btn-success';}?> break_time" mode="<?php if( $sat_break_status == 0 || $sat_break_status == 2){echo '2';}else{ echo '1';}?>"  id="sat"> <?php if( $sat_break_status == 0 || $sat_break_status == 2){echo 'No <i class="fas fa-caret-down"></i>';}else{ echo 'Yes <i class="fas fa-caret-up"></i>';}?></button>
                                    </td>
                                    <td>Close Day
                                         <label class="enabled-label"> <br>
                                            <input type="checkbox" id="sat_close_status" name="close_day" value="<?php  echo $sat_close_status;?>" <?php if($sat_close_status == 2){echo 'checked=""';}?>>
                                               <span class="checkmark_check"></span>
                                         </label>
                                    </td>
                                 </tr>
                                <!-----Break on Sat--- START----->
                                <tr id="sat_break_tr" class="<?php if( $sat_break_status == 0 || $sat_break_status == 2){echo 'd-none';}?>">
                                    <th> </th>
                                    <td>Start Time 
                                       <input type="time" class="form-control" id="sat_break_start_time" name="open-time"  value="<?php  echo "$sat_break_start_time"; ?>" required="" class="check_space">
                                         <span class="error" id="unfill_sat_break_start_time"></span>
                                    </td>
                                    <td>End Time
                                        <input type="time" id="sat_break_end_time" class="form-control" name="close-time" placeholder="Select Time" value="<?php  echo "$sat_break_end_time"; ?>" required="" class="check_space">
                                         <span class="error" id="unfill_sat_break_end_time"></span>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <!-----Break on Sat--- END----->
                               <th>Sun</th>
                                    <td>Open Time 
                                       <input type="time" class="form-control" id="sun_open_time" name="open-time" value="<?php  echo "$sun_open_time"; ?>" required="" class="check_space">
                                        <span class="error" id="unfill_sun_open_time"></span>
                                    </td>
                                    <td>Close Time
                                        <input type="time" class="form-control" id="sun_close_time"  name="close-time" placeholder="Select Time" value="<?php  echo "$sun_close_time"; ?>" required="" class="check_space">
                                         <span class="error" id="unfill_sun_close_time"></span>
                                    </td>
                                    <td>Break <br>
                                        <button <?php if($sun_close_status == 2) {echo 'disabled=""';} ?> type="button" class="btn <?php if( $sun_break_status == 2 || $sun_break_status == 0){echo 'btn-primary';}else{ echo 'btn-success';}?> break_time" mode="<?php if( $sun_break_status == 0 || $sun_break_status == 2){echo '2';}else{ echo '1';}?>"  id="sun"> <?php if( $sun_break_status == 0 || $sun_break_status == 2){echo 'No <i class="fas fa-caret-down"></i>';}else{ echo 'Yes <i class="fas fa-caret-up"></i>';}?></button>
                                    </td>
                                    <td>Close Day
                                         <label class="enabled-label"> <br>
                                            <input type="checkbox" id="sun_close_status" name="close_day" value="<?php  echo $sun_close_status;?>" <?php if($sun_close_status == 2){echo 'checked=""';}?>>
                                               <span class="checkmark_check"></span>
                                         </label>
                                    </td>
                                 </tr>
                                <!-----Break on Sun--- START----->
                                <tr id="sun_break_tr" class="<?php if( $sun_break_status == 0 || $sun_break_status == 2){echo 'd-none';}?>">
                                    <th> </th>
                                    <td>Start Time 
                                       <input type="time" class="form-control" id="sun_break_start_time" name="open-time" value="<?php  echo "$sun_break_start_time"; ?>" required="" class="check_space">
                                         <span class="error" id="unfill_sun_break_start_time"></span>
                                    </td>
                                    <td>End Time
                                        <input type="time" id="sun_break_end_time" class="form-control" name="close-time" placeholder="Select Time" value="<?php  echo "$sun_break_end_time"; ?>"  required="" class="check_space">
                                         <span class="error" id="unfill_sun_break_end_time"></span>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <!-----Break on Sun--- END----->
                      </table>
                     </div>
                       <div class="text-right">
                           <button type="button" class="btn btn-primary change-password-btns mr-10" id="rest_open_close_time_break_submit">Save</button>
                        </div>
                  </div>
              </div>
              
                <input type="hidden" name="restaurant_id" value="<?php echo $restaurant_id;?>" />
               <div class="form-group">
                  <button type="button" class="btn btn-primary change-password-btns mr-10" onclick="goBack()">Back</button>
               </div>
               <!--<div class="form-group">
                  <button type="button" class="btn btn-primary change-password-btns mr-10">Save</button>-->
                  <!-- <button type="button" class="btn btn-secondary change-password_cancel">Cancel</button> -->
               <!--</div>-->
            </div>
         </form>
      </div>
   </section>
</div>

  <!-- Modal for Offline /online Category START -->
  <!--after close we do clearn modal input field thats why put here this input file--->
   <input type="hidden" id="offline_tag" value="1"/><!--it will change on click on tag button by default its 1 for hours-->
   <input type="hidden" id="offline_type" value="1"/>
  <div class="modal fade" id="offline_online_restaurant_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Offline Restaurant</h5><!--// same  modal use for product offline online-->
          <button type="button" class="close close_res_offline_modal">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
              <div class="text-center">
                    <div class="btn-group btn-group-md" role="group" aria-label="Basic example">
                      <button type="button" class="btn btn-secondary offline_tag offline_hour_btn_clr" offline_tag="1">Hours</button>
                      &nbsp; &nbsp;<button type="button" class="btn btn-primary offline_tag offline_day_btn_clr" offline_tag="2">Day</button>
                      &nbsp; &nbsp; <button type="button" class="btn btn-primary offline_tag offline_multi_day_btn_clr" offline_tag="3">Multiple Days</button>
                    </div><br>
                     <span class="error" id="unselect_offline_tag"></span>
              </div>
              <br>
              <form>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-sm-4">
                        <label for="Selectday"><b class="offline_lable_name">For Hours</b></label>
                      </div>
                      <div class="col-sm-8">
                         <div id="select_hours">
                            <!-- <input type="time" class="form-control boxed" name="hours" id="hours_offline_value" /> -->

                            <?php
                                 $selectbox_hour = "";
                                for ($hours = 1; $hours <= 24; $hours++) {
                                  $selectbox_hour .=  '<option value="'.$hours.':00">'.$hours.':00</option>';
                                }
                            ?>
                            <select class="form-control" id="hours_offline_value" >
                              <option value="">Select Hours</option>
                              <?php echo $selectbox_hour;?>
                            </select>
                             &nbsp;<span class="error" id="unfill_hours_offline_value"></span>
                         
                         </div>
                         <div id="select_days" class="d-none">
                             <div class="row">
                                <div class="form-group col-md-12">
                                   <label>From Date</label>
                                   <input min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" id="days_offline_fromdate" name="days_offline_fromdate" class="form-control date_valid" value=""> 
                                   &nbsp;<span class="error" id="unfill_days_offline_value"></span>
                                </div>
                              </div>
                          </div>
                          <div id="select_multiple_days" class="d-none">
                            <div class="row">
                               <div class="form-group col-md-6">
                                  <label>From Date</label>
                                  <input min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" id="offline_rest_fromdate" name="fromdate" max="" class="form-control date_valid" value=""> 
                                   &nbsp;<span class="error" id="unfill_offline_rest_fromdate"></span>
                                  
                                </div>
                                 <div class="form-group col-md-6">
                                   <label>Till Date</label>
                                   <input min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" id="offline_rest_tilldate" name="todate" max="" class="form-control date_valid" value="" > 
                                    &nbsp;<span class="error" id="unfill_offline_rest_tilldate"></span>
                                </div>
                              </div>
                        </div>
                      </div>
                    </div>
                  </div>
              </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary change-password-btns" id="rest_online_offline_save" style="padding: .3rem .8rem;">Save</button>
          <button type="button" class="btn btn-secondary close_res_offline_modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal for Offline /online Category END -->

 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="Stylesheet" type="text/css" />

 
 <!-- Javascript -->
 <!---For time picker with date you need to only replace only datepickerv with datetimepicker---->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
  <script type="text/javascript">
      var $j2 = jQuery.noConflict();       
         $j2("#offline_rest_fromdate").datepicker({
          dateFormat: "dd/mm/yy",
           minDate: 0,
          onSelect: function (date) {
              var dt2 = $j2('#offline_rest_tilldate');
              var startDate = $j2(this).datepicker('getDate');
              var minDate = $j2(this).datepicker('getDate');
              if (dt2.datepicker('getDate') == null){
                dt2.datepicker('setDate', minDate);
              }              
              //dt2.datepicker('option', 'maxDate', '0');
              dt2.datepicker('option', 'minDate', minDate);
          }
        });
        $j2('#offline_rest_tilldate').datepicker({
            dateFormat: "dd/mm/yy",
            minDate: 0
        });           
    
  </script>
 <!-- Javascript -->
  <script type="text/javascript">
      var $j3 = jQuery.noConflict();       
         $j3("#days_offline_fromdate").datepicker({//datetimepicker
          dateFormat: "dd/mm/yy",
           minDate: 0,
        });
  </script>
