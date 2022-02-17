<?php
//Basic Setting----------------
 
if (isset($settings_data) && $settings_data != "") {

   $smtp_email = $settings_data[0]['value'];
   $smtp_password = $settings_data[1]['value'];

   $android_version = $settings_data[2]['value'];
   $ios_version = $settings_data[3]['value'];

   $merchant_play_store_url = $settings_data[4]['value'];
   $merchant_app_store_url = $settings_data[5]['value'];

   $customer_play_store_url = $settings_data[6]['value'];
   $customer_app_store_url = $settings_data[7]['value'];

   $support_email = $settings_data[8]['value'];
   $support_call = $settings_data[9]['value'];

   $cashback_validity = $settings_data[10]['value'];
   $auto_cancel_order = $settings_data[11]['value'];
   $basic_delivery_time = $settings_data[12]['value'];

   $kerela_eats_commission = $settings_data[13]['value'];
   $restaurant_commission = $settings_data[14]['value'];

   // socail media url and enable / disable --START----
   #1 = Enable , 2= Disable 
   $facebook_url = $settings_data[15]['value'];
   $fb_status = $settings_data[15]['status'];

   $instagram_url = $settings_data[16]['value'];
   $insta_status = $settings_data[16]['status'];

   $google_url = $settings_data[17]['value'];
   $google_status = $settings_data[17]['status'];
    // socail media url and enable / disable --END----

   $website_url = $settings_data[18]['value'];
   $basic_preparation_time = $settings_data[19]['value'];

   $company_name = $settings_data[20]['value'];
   $country_name = $settings_data[21]['value'];
   
   
   # GET APP VERSION FOR BOTH PLATFORM
   $android_version_merchant = $settings_data[23]['value'];
   $ios_version_merchant = $settings_data[24]['value'];

   # GET cashback_validity from settings
   $cashback_validity = $settings_data[10]['value'];
   $window_time = $settings_data[25]['value'];
}

?>
<?php
//Account Setting------------
if (isset($admin_information) && $admin_information != "") {
    
   
    
    // split admin full name 
    $fullname   = $admin_information[0]['fullname'];
  
    
    $admin_id    = $admin_information[0]['id'];
    $admin_number_id    = $admin_information[0]['number_id'];
    $email       = $admin_information[0]['email'];
    $mobile      = $admin_information[0]['mobile'];
    $profile_pic = $admin_information[0]['profile_pic'];
    $user_street_address = $admin_information[0]['user_street_address'];
    
    if (empty($profile_pic) && empty($header['user_data'])) {
        
        $profile_image = base_url('assets/img/avatar/avatar-1.png');
        $exist_profile_image = "";
    } else {
        
        $profile_image = base_url().$profile_pic;
        $exist_profile_image = $profile_pic;

        //$profile_image = base_url($header['user_data']);
        
    }
}

?>

<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Settings</h1>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Settings</div>
         </div>
      </div>
      <div class="setting-section">
         <div class="tab">
            <button class="tablinks active" onclick="firstTab(event, 'Basic')" id="defaultOpen">Basic</button>
            <button class="tablinks " id="account_tab" onclick="firstTab(event, 'Account')">Account</button>
            <!-- <button class="tablinks" onclick="firstTab(event, 'Extra')">Extra</button> -->
         </div>
         <?php $this->load->view("validation");?>
         <div id="Basic" class="tabcontent" style="display: block;">
            <div class="row">
               <div class="col-md-12">
                  <form class="algn-setform" action="<?php echo base_url('admin/update_admin_basic_settings/') ?>" Method="POST">
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Facebook:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="url" pattern="https?://.+\.com" value="<?php echo $facebook_url;?>" maxlength="200" required="" name="facebook_value" placeholder="Facebook Page URL" class="form-control boxed check_space valid_url">
                        </div>
                        <div class="col-md-4">
                           <div class="enable-disabled">
                              <div class="row">
                                 <label class="enabled-label">Enable
                                 <input type="radio"  value="1" checked="checked" name="fb_status" <?php if($fb_status == '1'){echo 'checked="checked"';} ?>>
                                 <span class="checkmark"></span>
                                 </label>
                                 <label class="enabled-label">Disable
                                 <input type="radio" name="fb_status" value="2" <?php if($fb_status == '2'){echo 'checked="checked"';} ?>>
                                 <span class="checkmark"></span>
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Instagram:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="url" pattern="https?://.+\.com" value="<?php echo $instagram_url;?>" maxlength="200" required="" name="insta_value" placeholder="Instagram Page URL" class="form-control boxed check_space">
                        </div>
                        <div class="col-md-4">
                           <div class="enable-disabled">
                              <div class="row">
                                 <label class="enabled-label">Enable
                                 <input type="radio"  value="1" checked="checked" name="insta_status" <?php if($insta_status == '1'){echo 'checked="checked"';} ?>>
                                 <span class="checkmark"></span>
                                 </label>
                                 <label class="enabled-label">Disable
                                 <input type="radio" name="insta_status" value="2" <?php if($insta_status == '2'){echo 'checked="checked"';} ?>>
                                 <span class="checkmark"></span>
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Google+:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="url" pattern="https?://.+\.com" value="<?php echo $google_url;?>" maxlength="200" required="" name="google_plus" placeholder="Google+ Page URL" class="form-control boxed check_space">
                        </div>
                        <div class="col-md-4">
                           <div class="enable-disabled">
                              <div class="row">
                                 <label class="enabled-label">Enable
                                 <input type="radio"  value="1" checked="checked" name="google_status" <?php if($google_status == '1'){echo 'checked="checked"';} ?>>
                                 <span class="checkmark"></span>
                                 </label>
                                 <label class="enabled-label">Disable
                                 <input type="radio" name="google_status" value="2" <?php if($google_status == '2'){echo 'checked="checked"';} ?>>
                                 <span class="checkmark"></span>
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                     
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Android Version:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" value="<?php echo $android_version; ?>" maxlength="5" required="" name="android_version" placeholder="Minimum allowed version(Android)" class="form-control boxed price_check">
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>iOS Version:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" value="<?php echo $ios_version; ?>" maxlength="5" required="" name="ios_version" placeholder="Minimum allowed version(iOS)" class="form-control boxed price_check">
                        </div>
                     </div>
                     <!--
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Contact Number:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" value="" maxlength="12" required="" name="contact" placeholder="Contact Number" class="form-control boxed contact_number contact_number check_space">
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Bill Amount:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" value="" maxlength="5" required="" name="amount_figure" placeholder="Minimum Amount" class="form-control boxed contact_number check_space">
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Delivery Fee:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" value="" maxlength="5" required="" name="delivery_charges" placeholder="Delivery Fee" class="form-control boxed contact_number check_space">
                        </div>
                     </div>
                     -->
                     <div class="form-group row" id="error_find">
                        <div class="col-md-2">
                           <label>Support Email:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="email" value="<?php echo $support_email; ?>" maxlength="100" title="Email Address" required="" name="support_email" placeholder="Email Address" class="form-control boxed check_space" id="support_email_valid" onblur="validateEmail(this,'#support_email_valid','#support_invalid_email')">
                             <span class="error" id="support_invalid_email"></span>
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Support Contact Number:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" value="<?php echo $support_call; ?>" required="" maxlength="9" minlength="8" name="support_call" placeholder="Contact Number" class="form-control boxed contact_number check_space">
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>SMTP Email:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="email" value="<?php echo $smtp_email; ?>" maxlength="100" title="Email Address" required="" name="smtp_email" placeholder="SMTP Email Address" class="form-control boxed check_space" id="SMTP_email_valid" onblur="validateEmail(this,'#SMTP_email_valid','#SMTP_invalid_email')">
                             <span class="error" id="SMTP_invalid_email"></span>
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>SMTP Password:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" value="<?php echo $smtp_password;?>" title="Email Address" required="" name="smtp_password" placeholder="SMTP Password" class="form-control boxed check_space" >
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Merchant Play store Url:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="url" pattern="https?://.+\.com" value="<?php echo $merchant_play_store_url; ?>" maxlength="200" required="" name="merchant_play_store_value" placeholder="Play store URL" class="form-control boxed check_space valid_url">
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Merchant App store Url:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="url" pattern="https?://.+\.com" value="<?php echo $merchant_app_store_url; ?>" maxlength="200" required="" name="merchant_app_store_value" placeholder="App store URL" class="form-control boxed check_space valid_url">
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Customer Play store Url:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="url" pattern="https?://.+\.com" value="<?php echo $customer_play_store_url; ?>" maxlength="200" required="" name="customer_play_store_value" placeholder="App store URL" class="form-control boxed check_space valid_url">
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Customer App store Url:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="url" pattern="https?://.+\.com" value="<?php echo $customer_app_store_url; ?>" maxlength="200" required="" name="customer_app_store_value" placeholder="App store URL" class="form-control boxed check_space valid_url">
                        </div>
                     </div>
                     
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Website Url:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="url" pattern="https?://.+\.com" value="<?php echo $website_url; ?>" maxlength="200" required="" name="website_url" placeholder="App store URL" class="form-control boxed check_space valid_url">
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Basic Delivery Time (In Miuntes):</label class="mb-0"><span class="text-danger">Ex. 40, 80 etc.</span>
                        </div>
                        <div class="col-md-6">
                           <input type="text" value="<?php echo $basic_delivery_time; ?>" maxlength="6" required="" name="basic_delivery_time"  class="form-control boxed check_space">
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Basic Preparation Time (In Miuntes):</label class="mb-0"><span class="text-danger">Ex. 40, 80 etc.</span>
                        </div>
                        <div class="col-md-6">
                           <input type="text" value="<?php echo $basic_preparation_time; ?>" maxlength="6" required="" name="basic_preparation_time"  class="form-control boxed check_space">
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Kerela Eats Commission:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" value="<?php echo $kerela_eats_commission; ?>" maxlength="5" required="" name="kerela_eats_commission" placeholder="Delivery Fee" class="form-control boxed check_space">
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Restaurant Commission:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" value="<?php echo $restaurant_commission; ?>" maxlength="5" required="" name="restaurant_commission" placeholder="Delivery Fee" class="form-control boxed check_space">
                        </div>
                     </div>

                       <div class="form-group row">
                        <div class="col-md-2">
                           <label>Company Name:</label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" minlength="5" maxlength="50" name="company_name" placeholder="Enter Company Name" required="" value="<?php echo  $company_name;?>" class="form-control boxed check_space" id="company_name">
                        </div>
                     </div>
                    
                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Country:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" maxlength="20" name="country_name" placeholder="Enter country  Name" required="" value="<?php echo  $country_name;?>" class="form-control boxed check_space" id="country_name">
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Android version (Mr Merchant):</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" maxlength="20" name="android_version_merchant" placeholder="Enter Android version for Mr Merchant" required="" value="<?php echo  $android_version_merchant;?>" class="form-control boxed check_space" id="android_version_merchant">
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>iOS version (Mr Merchant):</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" maxlength="20" name="ios_version_merchant" placeholder="Enter iOS version for Mr Merchant" required="" value="<?php echo  $ios_version_merchant;?>" class="form-control boxed check_space" id="ios_version_merchant">
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Cashback Validity</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" maxlength="20" name="cashback_validity" placeholder="Enter Cashback Validity" required="" value="<?php echo  $cashback_validity;?>" class="form-control boxed check_space" id="cashback_validity">
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-md-2">
                           <label>Window Time</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" maxlength="20" name="window_time" placeholder="Enter Cashback Validity" required="" value="<?php echo  $window_time;?>" class="form-control boxed check_space" id="window_time">
                        </div>
                     </div>

                     <div class="form-group">
                        <button type="submit"  id="submit_btn" class="btn btn-primary change-password-btns mr-10">Save</button>
                        <a href="<?php echo ''.base_url("admin/setting").'';?>" type="button" class="btn btn-secondary change-password_cancel">Cancel</a>
                     </div>
                  </form>
               </div>
            </div>
         </div>
         <div id="Account" class="tabcontent">
            <div class="row">
               <div class="col-md-3">
                  <div class="account-detail-head">
                  <h1>Account Details</h1>
               </div>
               </div> 
               <div class="col-md-9">
                  <form method="POST" action="<?php echo base_url('admin/update_super_admin_details') ?>" enctype="multipart/form-data">
                  <div class="account-details-sec">
                     <div class="form-group row">
                        <div class="col-md-4">
                           <label>Profile Image</label>
                        </div>
                        <div class="col-md-8 admin-profile-img">
                            <div class="row">
                               <img id="disp_img" src="<?php echo $profile_image; ?>"  alt="admin profile" accept=".png, .jpg, .jpeg">
                                 <div class="img-add">                                      
                                    <input type="file" class="d-none" accept="image/x-png,image/jpeg, image/jpg" id="file" name="admin_profile_pic" onchange="document.getElementById('disp_img').src = window.URL.createObjectURL(this.files[0])" value="<?php echo  $exist_profile_image;?>">
                                    <label for="file"><i class="fas fa-pencil-alt"></i></label>
                                 </div>
                                    <span class="error" id="unfill_image"></span>
                        </div>
                     </div>
                  </div>
                      <div class="form-group row">
                        <div class="col-md-4">
                           <label>ID:</label>
                        </div>
                        <div class="col-md-8">
                           <label><?php echo $admin_number_id ;?></label>
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-4">
                           <label>Name:</label>
                        </div>
                        <div class="col-md-8">
                           <input type="text"  maxlength="20" value="<?php echo $fullname; ?>"  required="" name="name" placeholder="Enter Name" class="form-control boxed check_space full_name_length only_alphabets" id="fullname">
                           <span class="error" id="unfill_name"></span>
                        </div>
                     </div>
                      <div class="form-group row">
                        <div class="col-md-4">
                           <label>Email:</label>
                        </div>
                        <div class="col-md-8">
                           <input type="email"   maxlength="100"  value="<?php echo $email;?>"  required="" id="profile_email_valid" name="email" placeholder="Enter Email Address" class="form-control boxed check_space valid_email_des" onblur="validateEmail(this,'#profile_email_valid','#profile_invalid_email')">
                             <span class="error" id="profile_invalid_email"></span>
                        </div>
                     </div>
                      <div class="form-group row">
                        <div class="col-md-4">
                           <label>Phone:</label>
                        </div>
                        <div class="col-md-8">
                            <input   maxlength="9" minlength="8" type="tel" name="phone" placeholder="Enter Phone Number" required="" class="form-control form-control-sm rounded-0 contact_number check_space" value="<?php echo $mobile; ?>" id="mobile">
                               <span class="error" id="invalid_phone"></span>
                        <!--   <div class="input-group-append">

                          </div> -->
                        </div>
                     </div>
                    
                     <div class="form-group row">
                        <div class="col-md-4">
                           <label>Address:</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text"  maxlength="20" minlength="100" name="address" placeholder="Enter address" required="" value="<?php echo $user_street_address;?>" class="form-control boxed check_space" id="address">
                             <span class="error" id="unfill_address"></span>
                        </div>
                     </div>
                     <div class="form-group row ">
                        <div class="col-md-12 text-right">
                        <a href="<?php echo ''.base_url("admin/setting/2").'';?>" type="button" class="btn btn-secondary mr-2 change-password_cancel">Cancel</a>
                        <button type="button" class="btn btn-primary modal_btns" id="account_setting_submit">Save</button>
                     </div>
                     </div>
                  </div>
                 </form>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>
  <script src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
<!-- Country wise phone number field -->
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.6/js/intlTelInput.min.js"></script>
<script type="text/javascript">
   let telInput = $("#phone")

// initialize
telInput.intlTelInput({
    initialCountry: 'auto',
    preferredCountries: ['us','gb','br','ru','cn','es','it'],
    autoPlaceholder: 'aggressive',
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.6/js/utils.js",
    geoIpLookup: function(callback) {
        fetch('https://ipinfo.io/json', {
            cache: 'reload'
        }).then(response => {
            if ( response.ok ) {
                 return response.json()
            }
            throw new Error('Failed: ' + response.status)
        }).then(ipjson => {
            callback(ipjson.country)
        }).catch(e => {
            callback('sg')
        })
    }
})

let telInput2 = $("#phone2")

// initialize
telInput2.intlTelInput({
    initialCountry: 'sg',
    preferredCountries: ['us','gb','br','ru','cn','es','it'],
    autoPlaceholder: 'aggressive',
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.6/js/utils.js"
})
</script> -->
<script type="text/javascript">
  

$( document ).ready(function() {
      var account_setting = "<?php echo  $this->uri->segment(3);?>";
      
      if(account_setting == 2){
         $('#account_tab').trigger('click');
      }
});
</script>