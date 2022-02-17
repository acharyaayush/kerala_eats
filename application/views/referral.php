<?php

// there should be only one data of referral , /promotion_mode_status = 3
//promotion_mode_status =   1- Promo Code, 2- Discount, 3 - Referral
if (isset($referral_data) && $referral_data != "" && !empty($referral_data)) {
    //print_r($referral_data);
    $referral_data_tr = "";
    foreach ($referral_data as $value) {

        $referral_id = $value['referral_id'];
        $language = $value['language'];
        $promo_type = $value['promo_type'];//1 - Flat 2 - Percent  

        //for refrreer-----------
        $referrer_discount_value = $value['referrer_discount_value'];
        $referrer_max_discount = $value['referrer_max_discount'];
        $referrer_discription = $value['referrer_discription'];

         //for referee-----------
        $referee_discount_value = $value['referee_discount_value'];
        $referee_max_discount = $value['referee_max_discount'];
        $referee_discription = $value['referee_discription'];

        $min_value = $value['min_value'];
        $promo_status = $value['promo_status'];


         if($promo_status == 1){
            $enable_disable_status = 'checked = "checked" ';
            $enable_disable_value = "2";
         }else  if($promo_status == 2){
            $enable_disable_status = "";
            $enable_disable_value = "1";
         }
    }
}else{
   
        $referral_data = "";
        $language = "";

        $referral_id = "";
        $promo_type = "";

        //for refrreer-----------
        $referrer_discount_value = "";
        $referrer_max_discount = "";
        $referrer_discription = "";

        //for referee-----------
        $referee_discount_value = "";
        $referee_max_discount = "";
        $referee_discription = "";

        $min_value = "";
        $promo_status = "";

        $enable_disable_status = "";
        $enable_disable_value = "";
    }
?>
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Referral</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
                <div class="breadcrumb-item">Referral</div>
            </div>
        </div>
        <div class="admin_profile">
            <div class="container">
                <?php $this->load->view("validation");?>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group admin-input-field">
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <label>Language</label>
                                </div>
                                <div class="col-md-7">
                                    <select id="language">
                                        <option value = "1" <?php if($language == 1){ echo "selected";}?>>English</option>
                                    </select>
                                     &nbsp;<span class="error" id="unselect_language"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group admin-input-field">
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <label>Referral type</label>
                                </div>
                                <div class="col-md-7">
                                    <select id="referral_type">
                                        <option value="">Select Type</option>
                                        <option value="1" <?php if($promo_type == 1){ echo 'selected';}?>>Flat</option>
                                        <option value="2"  <?php if($promo_type == 2){ echo 'selected';}?>>Percent</option>
                                    </select>
                                      &nbsp;<span class="error" id="unselect_referral_type"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group admin-input-field">
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <label>Referrer discount(%)</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" maxlength="3" class="check_space" name="referrer_discount_value" value="<?php echo $referrer_discount_value;?>" placeholder="Referrer discount(%)" required="" id="referrer_discount_value"/>
                                    &nbsp;<span class="error" id="unfill_referrer_discount_value"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group admin-input-field">
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <label>Referrer maximum discount value</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" maxlength="3" class="check_space"  name="referrer-maximum" value="<?php echo $referrer_max_discount;?>" placeholder="Enter Referrer maximum discount value" required="" id="referrer_max_discount_value"/>
                                    &nbsp;<span class="error" id="unfill_referrer_max_discount_value"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group admin-input-field">
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <label>Referrer description</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text"  minlength="20"  maxlength="100" name="referrer-description" value="<?php echo $referrer_discription;?>" placeholder="Enter Referrer description" id="referrer_discription"/>
                                    &nbsp;<span class="error" id="unfill_referrer_discription"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group admin-input-field">
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <label>Referee discount(%)</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" maxlength="3" class="check_space"  name="referee-discount(%)" value="<?php echo $referee_discount_value;?>" placeholder="Enter Referee discount(%)" id="referee_discount_value"/>
                                     &nbsp;<span class="error" id="unfill_referee_discount_value"></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group admin-input-field">
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <label>Referee maximum discount value</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" name="referee-maximum" value="<?php echo $referee_max_discount;?>" placeholder="Enter Referee maximum discount value" required="" id="max_discount_value"/>
                                     &nbsp;<span class="error" id="unfill_referee_max_discount_value"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group admin-input-field">
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <label>
                                        Referee description
                                        <i class="fas fa-info-circle"
                                            data-toggle="tooltip"
                                            title=" Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."
                                            style="color: #666; font-size: 17px;"
                                        ></i>
                                    </label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text"  minlength="20"  maxlength="100" name="referee-description" value="<?php echo $referee_discription;?>" placeholder="Enter Referee description" required="" id="referee_discription" />
                                     &nbsp;<span class="error" id="unfill_referee_discription"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group admin-input-field">
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <label>Minimum Order amount</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text"  maxlength="4" name="minimum-order" value="<?php echo $min_value;?>" placeholder="Enter Minimum Order amount" required="" id="minimum_order_amount"/>
                                     &nbsp;<span class="error" id="unfill_minimum_order_amount"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group admin-input-field">
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <label>Status</label>
                                </div>
                                <div class="col-md-7">
                                    <label class="switch promocode-status">
                                        <input type="checkbox" class="referral_status"   value="<?php echo $enable_disable_value;?>"  edit_referral_id= "<?php echo $referral_id;?>" <?php echo  $enable_disable_status;?>/>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="form-group admin-buttons">
                                    <input type="hidden"  id="edit_referral_id" value="<?php echo $referral_id; ?>" />
                                    <button type="button" class="btn btn-primary change-password-btns" id="referral_submit">Save</button>
                                    <a type="button" href="<?php echo base_url('admin/referral/');?>" class="btn btn-secondary change-password_cancel" style="margin-right: 10px;">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>