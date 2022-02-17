<!-- Main Content --> 
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1><?php echo $pageTitle;?></h1>
        <?php
            if(isset($selected_user_role) && $selected_user_role == 2){
           ?>
         <span>
            <div class="col-md-2">
               <!-- Pass 1 as parameter to call add user form -->
               <a href="<?php echo base_url(); ?>admin/add_edit_merchant/1" class="btn btn-primary">Add <?php echo $pageTitle;?></a>
            </div>
         </span>
         <?php        
            }
        ?>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item"><?php echo $pageTitle;?></div>
         </div>
      </div>
      <div class="section-body">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                <div class="col-5"></div>
                      <div class="col-2">
                        <button class="btn btn-primary filter_button" id="" type="button">Filter</button>
                      </div>
                  <div class="card-header user_tables">
                            <div class="promotionFileter">
                                <div class="promotionFilterFields">
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label>From Date</label>
                                            <input  min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" id="fromdate" name="fromdate" max="" class="form-control fromdate wv_filter_box_height" value="<?php if($fromdate != '' && $fromdate != 'all'){echo $fromdate;} ?>" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>To Date</label>
                                            <input autocomplete="off"placeholder="dd-mm-yyyy" id="todate" name="todate" max="" class="form-control todate wv_filter_box_height" value="<?php if($todate != '' && $todate != 'all'){echo $todate;} ?>" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Status</label>
                                            <select class="custom-select userstatus wv_filter_box_height form-control" name="userstatus">
                                                <option value="">Select Status</option>
                                                <option value="0" <?php if($user_status == '0' && $user_status != 'all'){echo "selected";} ?>>Pending</option>
                                                <option value="1" <?php if($user_status == '1' && $user_status != 'all'){echo "selected";} ?>>Approved</option>
                                                 <option value="2" <?php if($user_status == '2' && $user_status != 'all'){echo "selected";} ?>>Rejected</option>
                                                  <option value="3" <?php if($user_status == '3' && $user_status != 'all'){echo "selected";} ?>>Inactive</option>
                                                   <option value="4" <?php if($user_status == '4' && $user_status != 'all'){echo "selected";} ?>>Verified by OTP but Approval is Pending</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <form action="javascript:void(0);">
                                                <label>Search</label>
                                                <input type="search" name="search" class="form-control search_key wv_filter_box_height" placeholder="Search"  value="<?php if($search != '' && $search != 'all'){echo $search;} ?>"/>
                                            </form>
                                        </div>

                                    </div> 
                                </div>
                                <div class="form-group users_btns">
                                    <form action="javascript:void(0);">
                                        <button class="btn btn-primary m-0 search_user_list_data mr-2" id="search_user_list_data" type="button">Search</button>
                                        <a href="<?php echo base_url() ?>admin/AllUser/<?php echo $selected_user_role;?>" class="btn btn-secondary clear_btns mr-2">Clear</a>
                                <button class="btn btn-secondary clear_btns  export_user_csv" type="button">Export CSV</button>
                                    </form>
                                </div>
                            </div>

                            
                        </div>  

                        <?php $this->load->view("validation");?>

                        <div class="card-body table-flip-scroll customerTableListing catg-tab  tb-scroll">
                             <table class="table" id="all_users_table">
                                 <?php 
                                      $this->load->view("users_list_table");
                                      //this is seprate for only table load by js by any action event
                                 ?>
                             </table>
                             <nav class="text-xs-right">
                                <?php if (isset($links)) { ?>
                                    <?php echo $links; ?>
                                <?php } ?>
                            </nav>
                        </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>
<!-- Edit Customer Modal -->
<div class="modal fade edit-customer" id="" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"><!-- id fill add by class on click-->
   <div class="modal-dialog read_more_popup" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit Customer</h5>
            <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>

        <form method="POST">
           <div class="modal-body">
              <div class="admin_profile">
                 <div class="form-group  admin-input-field">
                    <div class="row">
                       <div class="col-md-12">
                          <label>First Name</label>
                       </div>
                       <div class="col-md-12">
                          <input type="text" name="fullname" id="customer_name" value="" placeholder="Enter first name" required="" class="check_space full_name_length only_alphabets" />
                          &nbsp;<span class="error" id="unfill_name"></span>
                       </div>
                    </div>
                 </div>
                 
                 <div class="form-group admin-input-field">
                    <div class="row">
                       <div class="col-md-12">
                          <label>Email</label>
                       </div>
                       <div class="col-md-12">
                          <input type="email" name="email" id="customer_email" value="" placeholder="Enter email address"  required=""  onblur="validateEmail(this,'#email_valid','#invalid_email')"/>
                           &nbsp;<span class="error" id="invalid_email"></span>
                       </div>
                    </div>
                 </div> 
                 <div class="form-group admin-input-field phone-number-field">
                    <div class="row">
                       <div class="col-md-12">
                          <label>Phone</label>
                       </div>
                       <div class="col-md-12">
                          <input id="phone" type="tel"  maxlength="9" minlength="8" name="phone" placeholder="Enter Phone Number" required="" class="form-control form-control-sm rounded-0 contact_number check_space">
                           &nbsp;<span class="error" id="invalid_phone"></span>
                            <div class="input-group-append">
                          </div>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
           <div class="modal-footer">
              <input type="hidden" value="" id="customer_id"/>
              <button type="button"  class="btn btn-secondary modal_btns" data-dismiss="modal">Close</button>
              <button type="button"  id="customer_edit_submit" class="btn btn-primary modal_btns">Save</button>
           </div>
        </form>
      </div>
   </div>
</div>
<!-- End Edit Customer Modal-->

<!--Add Money in to customer wallet -------------START--------->

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

<?php

   $current_url =  current_url();
  $parameter_url = explode("AllUser/".$selected_user_role."/0", $current_url);// if action is load like enable/disable or delete
  ////by default table value is 0
 

  if(isset($parameter_url[1])){

   $new_users_url_for_mode = 'admin/AllUser/'.$selected_user_role.'/table/'.$parameter_url[1].'';
  }else{

    $new_users_url_for_mode = 'admin/AllUser/'.$selected_user_role.'/table/';
  }

?>
<script type="text/javascript">
  var selected_user_role = '<?php echo $selected_user_role;?>';
  var user_delete_url = '<?php echo base_url('admin/delete_user/'.$selected_user_role.''); ?>';// for both customer and merchant
  var user_edit_status_url = '<?php echo base_url('admin/edit_user_status/'.$selected_user_role.''); ?>';// for both customer and merchant
  var user_table_url = '<?php echo  base_url(''.$new_users_url_for_mode.''); ?>';
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="Stylesheet" type="text/css" />
<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j("#fromdate").datepicker({
        dateFormat: 'yy-mm-dd',
        // numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate());
            $j("#todate").datepicker("option", "minDate", dt);
        }
    });
    $j("#todate").datepicker({
        dateFormat: 'yy-mm-dd',
        // numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() - 1);
            // $j("#fromdate").datepicker("option", "maxDate", dt);
        }
    });
</script>


