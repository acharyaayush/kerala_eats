<?php
//Promotion CashBack  Setting----------------
 print_r($settings_data);
if (isset($settings_data) && $settings_data != "") {
   $order_cashback = $settings_data[0]['value'];//1 : Flat 2 percent
   $order_cashback_type = $settings_data[0]['type'];
}

?>

<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Promotion Cashback</h1>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Promotion Cashback</div>
         </div>
      </div>
      <div class="setting-section">
         
         <?php $this->load->view("validation");?>
       
            <div class="row">
               <div class="col-md-12">
                  <form class="algn-setform" action="<?php echo base_url('admin/update_order_cashback_of_promotion/') ?>" Method="POST">
                     
                     <div class="form-group row">
                        <div class="col-md-12">
                           <label>Order Cashback:</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" maxlength="5" value="<?php echo $order_cashback;?>" required="" name="order_cashback" placeholder="Facebook Page URL" class="form-control boxed check_space">
                        </div>
                        <div class="col-md-6">
                           <div class="enable-disabled">
                              <div class="row">
                                 <label class="enabled-label">Flat
                                 <input type="radio"  value="1" checked="checked" name="order_cashback_type" <?php if($order_cashback_type == '1'){echo 'checked="checked"';} ?>>
                                 <span class="checkmark"></span>
                                 </label>
                                 <label class="enabled-label">Percent
                                 <input type="radio" name="order_cashback_type" value="2" <?php if($order_cashback_type == '2'){echo 'checked="checked"';} ?>>
                                 <span class="checkmark"></span>
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="form-group">
                        <button type="submit"  id="submit_btn" class="btn btn-primary change-password-btns mr-10">Save</button>
                        <a href="<?php echo ''.base_url("admin/promotion_cashback").'';?>" type="button" class="btn btn-secondary change-password_cancel">Cancel</a>
                     </div>
                  </form>
               </div>
            </div>
      </div>
   </section>
</div>
<script src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
 
<script type="text/javascript">

$( document ).ready(function() {
      var account_setting = "<?php echo  $this->uri->segment(3);?>";
      
      if(account_setting == 2){
         $('#account_tab').trigger('click');
      }
});
</script>