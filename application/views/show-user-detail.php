<?php 
 
if (isset($user_detail) && $user_detail[0] != "") {

        $user_id = $user_detail[0]['id'];
        $user_role = $user_detail[0]['role'];
        $number_id = $user_detail[0]['number_id'];
        $user_fullname = $user_detail[0]['fullname'];
        $user_email = $user_detail[0]['email'];
        $user_contact_no = $user_detail[0]['mobile'];
        $user_profile_pic = $user_detail[0]['profile_pic'];
        $user_device_type = $user_detail[0]['device_type'];
        $user_pin_address = $user_detail[0]['user_pin_address'];
        $user_unit_number = $user_detail[0]['user_unit_number'];
        $user_street_address = $user_detail[0]['user_street_address'];
        $user_postal_code = $user_detail[0]['user_postal_code'];
        $user_status = $user_detail[0]['status'];
        $user_registerd_date = $user_detail[0]['created_at'];
        $hear_about_text = $user_detail[0]['hear_about_text'];
         
         // For logo image
         if($user_profile_pic != "" && empty($header['user_data'])){
            $user_profile_pic = base_url().$user_profile_pic;
 
         }else{
            $user_profile_pic =  base_url('assets/img/avatar/avatar-1.png');
         }

            // 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
            switch ($user_status) {
              case 0:
                
                 $status = "Pending";
             
                break;
              case 1:
                
                 $status = "Approved";
                 
                break;
              case 2:
                
                 $status = "Rejected";
                 
                break;
              case 3:
                
                $status = "Inactive";
              
                break;
              case 4:
                
                $status = 'Verified by OTP but Approval is Pending';
                break;

              default:
                 $status = "Pending";
            }


          // 1 - Web 2 - Android 3 - iOS | Update on every Login
          switch ($user_device_type) {
            case 1:
              
               // $user_device_type = "Web";
               $user_device_type = "Android";

              break;
            case 2:
              
               // $user_device_type = "Android";
               $user_device_type = "iOS";
              break;
            case 3:
              
               // $user_device_type = "iOS";
               $user_device_type = "Web";
              break;

            default:
               $user_device_type = "Null";
          }

}else{
   redirect(base_url('admin/AllUser/'.$user_role.''));

        $user_role = "";
        $number_id =  "";
        $user_fullname =  "";
        $user_email =  "";
        $user_contact_no =  "";
        $user_profile_pic =  "";
        $user_device_type =  "";
        $user_pin_address =  "";
        $user_unit_number =  "";
        $user_street_address =  "";
        $user_postal_code =  "";
        $user_status =  "";
        $user_registerd_date =  "";
}

?>
<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1><?php echo $pageTitle;?></h1>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item"><?php echo $pageTitle;?></div>
         </div>
      </div>
      <div class="add-restaurant">
         <form method="POST">
            <div class="card-body">
               <div class="form-group admin-input-field">
                  <div class="row">
                     <div class="col-md-3">
                        <label>ID</label>
                        <input type="text" name="name" value="<?php echo $number_id;?>" required="" disabled="" style="cursor: not-allowed;" />
                     </div>
                     <div class="col-md-3">
                        <label>Name</label>
                        <input type="text" name="name"  value="<?php echo  $user_fullname;?>" disabled="" required="" />
                     </div>
                     <div class="col-md-3">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo $user_email;?>" disabled="" required="" />
                     </div>
                     <div class="col-md-3">
                        <label>Phone </label>
                        <input type="tel" name="phone"  value="<?php echo $user_contact_no;?>" disabled="" required="" class="contact_number" />
                     </div>
                  </div>
                  <br>
                  <div class="row">
                     <div class="col-md-3">
                        <label>Last Used Platform</label>
                        <input type="text" name="device"  value="<?php echo  $user_device_type;?>" disabled="" required="" />
                     </div>
                     <div class="col-md-3">
                        <label>Status</label>
                        <input type="email" name="email"  value="<?php echo $status ;?>" disabled="" required="" />
                     </div>
                      <div class="col-md-3">
                        <label>Unit Number</label>
                        <input type="text" name="address"value="<?php echo $user_unit_number;?>" disabled="" required="" />
                     </div>
                     <div class="col-md-3">
                        <label>Postal code </label>
                        <input type="text" name="display-address" value="<?php echo $user_postal_code;?>" disabled="" required="" />
                     </div>
                  </div>
                  <?php if($user_role == 3){?>
                   <br>
                  <div class="row">
                     <div class="col-md-4 admin-input-field">
                        <label> Address Line 1</label><!--(Pin address as Street Address)-->
                        <input type="text" name="address"   value="<?php echo $user_pin_address;?>" disabled="" required="" />
                     </div>
                     <div class="col-md-4">
                        <label>BLK /House /Apartment No. </label>
                        <input type="text" name="user_street_address"   value="<?php echo $user_street_address;?>" disabled="" required="" />
                     </div>
                     <div class="col-md-4">
                        <label>Wallet Balance <a href="<?php echo base_url('admin/wallet_history/'.$user_id)?>">See wallet history</a></label>
                        <input type="text" name="wallet_balance"  value="S$ <?php echo  $wallet_balance;?>" disabled="" required="" />
                     </div>
                  </div>
                  <br>
                  <div class="row">
                     <div class="col-md-4">
                        <label>Hear about us from</label>
                        <input type="text" name=""  value="<?php echo  $hear_about_text;?>" disabled="" required="" />
                     </div>
                  </div>
                  <?php }?>
               </div>
             <?php if($user_role == 2){?>
               <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 admin-input-field">
                        <label>Street Address </label>
                        <input type="text" name="address"   value="<?php echo $user_pin_address;?>" disabled="" required="" />
                     </div>
                     <div class="col-md-3 admin-profile-img">
                        <label>Logo <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="Upload a square image that represents the business." style="color: #666; font-size: 17px;"></i></label>
                        <img id="disp_img" src="<?php  echo $user_profile_pic ;?>" alt="User Profile Image" />
                        <div class="img-add">
                           <input type="file" accept="image/x-png,image/jpeg, image/jpg" id="file" name="file" onchange="document.getElementById('disp_img').src = window.URL.createObjectURL(this.files[0])" />
                        </div>
                     </div>
                  </div>
               </div>
                 <?php }?>
               <div class="form-group">
                  <button type="button" class="btn btn-primary change-password-btns mr-10" onclick="goBack()">Back</button>
                  <button type="button" class="btn btn-primary change-password-btns mr-10 customer_id_for_add_less_money" data-toggle="modal" data-target="#add_money_modal" user_id="<?=$user_id?>" user_number_id="<?=$number_id?>">Add Money</button>
                  <button type="button" class="btn btn-primary change-password-btns mr-10 customer_id_for_add_less_money" data-toggle="modal" data-target="#deduct_money_modal" user_id="<?=$user_id?>" user_number_id="<?=$number_id?>">Deduct Money</button>
               </div>

               <!--<div class="form-group">
                  <button type="button" class="btn btn-primary change-password-btns mr-10">Save</button>-->
                  <!-- <button type="button" class="btn btn-secondary change-password_cancel">Cancel</button> -->
               <!--</div>-->
            </div>
         </form>
          <?php if($user_role == 3){?>
          <h4>Orders details</h4>
          <div class="card-body table-flip-scroll orders-tables tb-scroll" style="padding: 20px 0px;">
             <table class="table" id="customer_orders_table">
                <?php $this->load->view('orders_list_table');?>
             </table>
              <nav class="text-xs-right">
                  <?php if (isset($links)) { ?>
                      <?php echo $links; ?>
                  <?php } ?>
              </nav>
          </div>
          <?php }?>
      </div>
   </section>
</div>

  <!-- The Modal -->
 <div class="modal fade edit-customer" id="add_money_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"><!-- id fill add by class on click-->
   <div class="modal-dialog read_more_popup" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Money</h5>
            <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>

        <form method="POST">
           <div class="modal-body">
              <div class="admin_profile">
                 <div class="form-group  admin-input-field">
                   <div class="row">
                       <div class="col-md-4">
                          <label>Customer Id</label>
                       </div>
                       <div class="col-md-8">
                          <label class="customer_number_id"></label>
                       </div>
                    </div>
                    <br>
                    <div class="row">
                       <div class="col-md-4">
                          <label>Amount</label>
                       </div>
                       <div class="col-md-8">
                          <input type="number" step="0.01" name="customer_credit_amount" id="customer_credit_amount" value="" placeholder="Enter Amount" required="" class="check_space" />
                          &nbsp;<span class="error" id="unfill_customer_credit_amount"></span>
                       </div>
                    </div>
                    <div class="row">
                       <div class="col-md-4">
                          <label>Comments</label>
                       </div>
                       <div class="col-md-8">
                          <input type="text" name="add_wallet_comments" id="add_wallet_comments" value="" placeholder="Enter Comments" class="check_space" />
                       </div>
                    </div>
                 </div>
              </div>
           </div>
           <div class="modal-footer">
              <input type="hidden" value="" id="customer_id_for_add_amount"/>
              <button type="button"  class="btn btn-secondary modal_btns" data-dismiss="modal">Close</button>
              <button type="button"  id="customer_credit_amount_submit" class="btn btn-primary modal_btns">Save</button>
           </div>
        </form>
      </div>
   </div>
</div>
<!--Add Money in to customer wallet -------------START--------->

<!--Deduct Money from customer wallet -------------START--------->

  <!-- The Modal -->
 <div class="modal fade edit-customer" id="deduct_money_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"><!-- id fill add by class on click-->
   <div class="modal-dialog read_more_popup" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Dedcut Money</h5>
            <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>

        <form method="POST">
           <div class="modal-body">
              <div class="admin_profile">
                 <div class="form-group  admin-input-field">
                   <div class="row">
                       <div class="col-md-4">
                          <label>Customer Id</label>
                       </div>
                       <div class="col-md-8">
                          <label class="customer_number_id"></label>
                       </div>
                    </div>
                    <br>
                    <div class="row">
                       <div class="col-md-4">
                          <label>Amount</label>
                       </div>
                       <div class="col-md-8">
                          <input type="number" step="0.01" name="customer_debit_amount" id="customer_debit_amount" value="" placeholder="Enter Amount" required="" class="check_space" />
                          &nbsp;<span class="error" id="unfill_customer_debit_amount"></span>
                       </div>
                    </div>
                    <div class="row">
                       <div class="col-md-4">
                          <label>Comments</label>
                       </div>
                       <div class="col-md-8">
                          <input type="text" name="deduct_wallet_comments" id="deduct_wallet_comments" value="" placeholder="Enter Comments" class="check_space" />
                       </div>
                    </div>
                 </div>
              </div>
           </div>
           <div class="modal-footer">
              <input type="hidden" value="" id="customer_id_for_deduct_amount"/>
              <button type="button"  class="btn btn-secondary modal_btns" data-dismiss="modal">Close</button>
              <button type="button"  id="customer_debit_amount_submit" class="btn btn-primary modal_btns">Save</button>
           </div>
        </form>
      </div>
   </div>
</div>
<!--Deduct Money from -------------START--------->

<script type="text/javascript">
  var is_customer_detail_page = 1;
  var customer_id_for_order_detail_get = '<?php echo $user_id;?>';
</script>
