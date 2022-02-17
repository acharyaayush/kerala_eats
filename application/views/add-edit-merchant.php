<?php

   if(isset($type) && $type == 2 && isset($user_date) && $user_date != ""){
      //print_r($user_date);

      $user_id = $user_date[0]['id'];// merchant id , role = 2
      $fullname = $user_date[0]['fullname'];
      $email = $user_date[0]['email'];
      $profile_pic = $user_date[0]['profile_pic'];
      $mobile = $user_date[0]['mobile'];

      if($profile_pic != "" && empty($header['user_data'])){
          $profile_pic_path = base_url($profile_pic); 
          $profile_pic_exist = $profile_pic; 
      }else{
          $profile_pic_path = base_url('assets/images/mr_merchant_pic.png'); 
          $profile_pic_exist = "";
      }

   }else{
      $fullname = "";
      $email = "";
      $profile_pic_path = base_url('assets/images/mr_merchant_pic.png'); 
      $mobile = ""; 
      $user_id = ""; 
      $profile_pic_exist = "";

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
               <form method="POST"  action="" enctype="multipart/form-data">
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-3">
                           <label>Profile Image</label>
                        </div>
                        <div class="col-md-7 admin-profile-img">
                           <img id="disp_img" src="<?php echo $profile_pic_path;?>"  alt="">
                                 <div class="img-add">               
                                    <input type="file" id="file" name="Merchant_profile_image" onchange="document.getElementById('disp_img').src = window.URL.createObjectURL(this.files[0])" accept="image/x-png,image/jpeg, image/jpg" required="">
                                    <label for="file"><i class="fas fa-pencil-alt"></i></label>
                                    <label class="delete_selected_cat_img" style="
                                            left: 126px;
                                        "><i class="fas fa-trash"></i></label>
                                 </div>
                                 &nbsp;<span class="error" id="unfill_image"></span>
                        </div>
                     </div>
                  </div>
                  <div class="form-group  admin-input-field">
                     <div class="row">
                        <div class="col-md-3">
                           <label>Full Name</label>
                        </div>
                        <div class="col-md-7">
                           <input type="text"  name="fullname" id="fullname" value="<?php echo $fullname;?>" placeholder="Enter full name" required="" class="check_space full_name_length only_alphabets" />
                            &nbsp;<span class="error" id="unfill_name"></span>
                        </div>
                     </div>
                  </div>
                  <div class="form-group admin-input-field">
                     <div class="row">
                        <div class="col-md-3">
                           <label>Email</label>
                        </div>
                        <div class="col-md-7">
                           <input type="email" id="email"  id="email_valid" onblur="validateEmail(this,'#email_valid','#invalid_email')" name="email" value="<?php echo $email;?>" placeholder="Enter email address" required="" class="text-lowercase" />
                             &nbsp;<span class="error" id="invalid_email"></span>
                        </div>
                     </div>
                  </div>
                  <div class="form-group admin-input-field">
                     <div class="row">
                        <div class="col-md-3">
                           <label>Phone</label>
                        </div>
                        <div class="col-md-7">
                           <input type="text" maxlength="9" minlength="8" id="mobile" name="mobile" value="<?php echo $mobile; ?>" placeholder="Enter Mobile number" class="contact_number" required="" />
                            &nbsp;<span class="error" id="invalid_phone"></span>
                        </div>
                     </div>
                  </div>
                  <div class="form-group admin-buttons">
                     <input type="hidden" id="type" value="<?php echo $type;?>" />
                     <input type="hidden" name="merchant_id" id="user_id" value="<?php echo $user_id;?>" />
                     <input type="hidden" name="" id="edit_exist_image" value="<?php echo $profile_pic_exist;?>" />
                     <button type="button" id="merchant_submit" class="btn btn-primary change-password-btns mr-10">Save Changes</button>
                     <a href="<?php echo base_url('admin/AllUser/2');?>"  class="btn btn-secondary change-password_cancel">Cancel</a>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </section>
</div>
 