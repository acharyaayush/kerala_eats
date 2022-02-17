<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Promo Codes</h1>
      <span>
        <div class="col-md-2">
             <a href="<?php echo base_url('admin/add_edit_promotion/1')?>" class="btn btn-primary search_user_list_data text-right">Add Promotion</a>
        </div>
    </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Promo Codes</div>
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
                                      <input  min="2020-01-01" autocomplete="off"placeholder="dd-mm-yyyy" id="fromdate" name="fromdate" max="" class="form-control fromdate " value="<?php if($fromdate != '' && $fromdate != 'all'){echo $fromdate;} ?>" /> -
                                     
                                  </div>
                                  <div class="form-group col-md-3">
                                      <label>To Date</label>
                                      <input  min="2020-01-01" autocomplete="off"placeholder="dd-mm-yyyy"  id="todate" name="todate" max="" class="form-control todate" value="<?php if($todate != '' && $todate != 'all'){echo $todate;} ?>" />
                                  </div>
                                  <div class="form-group col-md-3">
                                      <label>Status</label>
                                      <select class="custom-select promo_code_status wv_filter_box_height form-control" name="promo_code_status">
                                          
                                          <option value="">Select Status</option>
                                          <option value="1" <?php if($promo_code_status == '1' && $promo_code_status != 'all'){echo "selected";} ?>>Enable</option>
                                          <option value="2"<?php if($promo_code_status == '2' && $promo_code_status != 'all'){echo "selected";} ?>>Disabled</option>
                                      </select>
                                  </div>
                                  <div class="form-group col-md-3">
                                      <label>Type</label>
                                      <select class="custom-select promo_code_type wv_filter_box_height form-control" name="promo_code_type">
                                          <option value="">Select Type</option>
                                          <option value="1"<?php if($promo_code_type == '1' && $promo_code_type != 'all'){echo "selected";} ?>>FLAT</option>
                                          <option value="2" <?php if($promo_code_type == '2' && $promo_code_type != 'all'){echo "selected";} ?>>Percent</option>
                                      </select>
                                  </div>
                                  <div class="form-group col-md-3">
                                      <label>Promo Application Mode</label>
                                      <select class="custom-select promo_code_mode wv_filter_box_height form-control" name="promo_code_type">
                                          <?php echo $promo_code_mode_select ;?>
                                          <option value="">Select Mode</option>
                                          <option value="1"  <?php if($promo_code_mode == '1' && $promo_code_mode != 'all'){echo "selected";} ?>>Auto Apply</option>
                                          <option value="2"  <?php if($promo_code_mode == '2' && $promo_code_mode != 'all'){echo "selected";} ?>>Not Auto Apply</option>
                                      </select>
                                  </div>
                               
                                  <div class="form-group col-md-3">
                                      <form action="javascript:void(0);">
                                          <label>Search</label>
                                          <input type="search" name="search" class="form-control search_key wv_filter_box_height" placeholder="Search" id="search_user_list_data" value="<?php if($search != '' && $search != 'all'){echo $search;} ?>" />
                                      </form>
                                  </div>
                                  <div class="form-group col-md-6">
                                    <div class="search_clear_btns users_btns">
                                      <form action="javascript:void(0);">
                                          <button class="btn btn-primary m-0 mr-2 search_user_list_data" type="button" id="search_promo_code_list_data">Search</button>
                                           <a href="<?php echo base_url() ?>admin/promo_codes" class="btn btn-secondary mr-2 clear_btns">Clear</a>
                                  <button class="btn btn-secondary clear_btns export_promo_code_csv" type="button">Export CSV</button>
                              </form>
                          </div>
                                  </div>
                              </div>
                          </div>
                          
                      </div>

                  </div>
                  <?php $this->load->view("validation");?>
                 <div class="card-body catg-tab table-flip-scroll orders-tables tb-scroll">
                     <table class="table" id="promo_code_table">
                      
                            <?php 
                              $this->load->view("promo_code_list_table");
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


<!-- Modal for Add Promotion -->
<div class="modal fade add-edit-promotion" id="add_promotion_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog read_more_popup" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Promotion</h5>
            <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Promotion Type </label>
                     <select>
                        <option value="">Select Type</option>
                        <option value="">Discount</option>
                        <option value="">Flat</option>
                     </select>
                  </div>
                  <div class="col-md-6">
                     <label>Promotion Name </label>
                     <input name="promo_code" type="text" class="" required="" placeholder="Enter Promotion Name">
                  </div>
               </div>
            </div>
            <div class="form-group adm


            in-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Discount (%) </label>
                     <input name="discount" type="text" class="" required="" placeholder="Enter Discount value">
                  </div>
                  <div class="col-md-6">
                     <label>Description (Max 150 Characters) * </label>
                     <textarea type="text" name="description" placeholder="Description" value="" required=""></textarea> 
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>From </label>
                     <input type="datetime-local" name="fdate" placeholder="" value="" required="">
                  </div>
                  <div class="col-md-6">
                     <label>Till </label>
                     <input type="datetime-local" name="till" placeholder="" value="" required="">
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Maximum Discount Value </label>
                     <span class="currency">S$</span>
                     <input type="text" name="maximum-discount" placeholder="" value="" required="">
                  </div>
                  <div class="col-md-6">
                     <label>Maximum No Of Allowed User(S) </label>
                     <input type="text" name="no-of-allowed-user" placeholder="Enter Maximum No Of Allowed User(S)" value="" required="">
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Minimum Order Amount </label>
                     <span class="currency">S$</span>
                     <input type="text" name="maximum-discount" placeholder="" value="" required="">
                  </div>
                  <div class="col-md-6">
                     <label>Promotion Application Mode </label>
                     <select>
                        <option>Public</option>
                        <option>Hidden</option>
                        <option>Auto Apply</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Allow Single User To Use The Code Multiple Times?</label>
                     <label class="enabled-label">Yes
                     <input type="radio"  value="1" checked="checked" name="single-user">
                     <span class="checkmark"></span>
                     </label>
                     <label class="enabled-label">No
                     <input type="radio"  value="1"  name="single-user">
                     <span class="checkmark"></span>
                     </label>
                  </div>
                  <div class="col-md-6">
                     <label>Allow Loyalty Points To Be Redeemed If This Promo Code Is Applied?</label>
                     <label class="enabled-label">Yes
                     <input type="radio"  value="1"  name="loyalty-points-redeemed">
                     <span class="checkmark"></span>
                     </label>
                     <label class="enabled-label">No
                     <input type="radio"  value="1" checked="checked" name="loyalty-points-redeemed">
                     <span class="checkmark"></span>
                     </label>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Allow Loyalty Points To Be Earned If This Promo Code Is Applied? </label>
                     <label class="enabled-label">Yes
                     <input type="radio"  value="1" checked="checked" name="loyalty-points-earned">
                     <span class="checkmark"></span>
                     </label>
                     <label class="enabled-label">No
                     <input type="radio"  value="1"  name="loyalty-points-earned">
                     <span class="checkmark"></span>
                     </label>
                  </div>
                  <div class="col-md-6">
                     <label>Promotion Applied On</label>
                     <select>
                        <option>Subtotal</option>
                        <option>Delivery Charge</option>
                        <option>Product</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Promo Applicable On Order No. </label>
                     <input type="number" value="" name="order-number" required="">
                  </div>
                  <div class="col-md-6">
                     <label>Assign Restaurants</label>
                     <select>
                        <option>Marmaris</option>
                        <option>Spice Junction</option>
                        <option>HindArab</option>
                        <option>Homemade Heaven</option>
                        <option>Merchant Storea</option>
                        <option>Swaadhisht</option>
                        <option>Pre-order Onam Sadhya</option>
                        <option>Alankar Restaurant &amp; Catering</option>
                        <option>My Kitchen</option>
                        <option>Curry Magic</option>
                        <option>Premaas Cuisine</option>
                        <option>Paradise Biryani</option>
                        <option>Pepper Castle </option>
                        <option>Pepper Castle</option>
                     </select>
                  </div>
               </div>
            </div>
         </div>
   <div class="modal-footer">
      <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary modal_btns">Add</button>
   </div>
      </div>
   </div>
</div>

<!--end modal-->
<!-- Modal for Edit Promotion -->
<div class="modal fade add-edit-promotion" id="edit_promotion_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog read_more_popup" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit Promotion</h5>
            <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
           <div class="modal-body">
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Promotion Type </label>
                     <select>
                        <option value="">Select Type</option>
                        <option value="">Discount</option>
                        <option value="">Flat</option>
                     </select>
                  </div>
                  <div class="col-md-6">
                     <label>Promotion Name </label>
                     <input name="promo_code" type="text" class="" required="" placeholder="Enter Promotion Name">
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Discount (%) </label>
                     <input name="discount" type="text" class="" required="" placeholder="Enter Discount value">
                  </div>
                  <div class="col-md-6">
                     <label>Description (Max 150 Characters) * </label>
                     <textarea type="text" name="description" placeholder="Description" value="" required=""></textarea> 
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>From </label>
                     <input type="datetime-local" name="fdate" placeholder="" value="" required="">
                  </div>
                  <div class="col-md-6">
                     <label>Till </label>
                     <input type="datetime-local" name="till" placeholder="" value="" required="">
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Maximum Discount Value </label>
                     <span class="currency">S$</span>
                     <input type="text" name="maximum-discount" placeholder="" value="" required="">
                  </div>
                  <div class="col-md-6">
                     <label>Maximum No Of Allowed User(S) </label>
                     <input type="text" name="no-of-allowed-user" placeholder="Enter Maximum No Of Allowed User(S)" value="" required="">
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Minimum Order Amount </label>
                     <span class="currency">S$</span>
                     <input type="text" name="maximum-discount" placeholder="" value="" required="">
                  </div>
                  <div class="col-md-6">
                     <label>Promotion Application Mode </label>
                     <select>
                        <option>Public</option>
                        <option>Hidden</option>
                        <option>Auto Apply</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Allow Single User To Use The Code Multiple Times?</label>
                     <label class="enabled-label">Yes
                     <input type="radio"  value="1" checked="checked" name="single-user">
                     <span class="checkmark"></span>
                     </label>
                     <label class="enabled-label">No
                     <input type="radio"  value="1"  name="single-user">
                     <span class="checkmark"></span>
                     </label>
                  </div>
                  <div class="col-md-6">
                     <label>Allow Loyalty Points To Be Redeemed If This Promo Code Is Applied?</label>
                     <label class="enabled-label">Yes
                     <input type="radio"  value="1"  name="loyalty-points-redeemed">
                     <span class="checkmark"></span>
                     </label>
                     <label class="enabled-label">No
                     <input type="radio"  value="1" checked="checked" name="loyalty-points-redeemed">
                     <span class="checkmark"></span>
                     </label>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Allow Loyalty Points To Be Earned If This Promo Code Is Applied? </label>
                     <label class="enabled-label">Yes
                     <input type="radio"  value="1" checked="checked" name="loyalty-points-earned">
                     <span class="checkmark"></span>
                     </label>
                     <label class="enabled-label">No
                     <input type="radio"  value="1"  name="loyalty-points-earned">
                     <span class="checkmark"></span>
                     </label>
                  </div>
                  <div class="col-md-6">
                     <label>Promotion Applied On</label>
                     <select>
                        <option>Subtotal</option>
                        <option>Delivery Charge</option>
                        <option>Product</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="form-group admin-input-field">
               <div class="row">
                  <div class="col-md-6">
                     <label>Promo Applicable On Order No. </label>
                     <input type="number" value="" name="order-number" required="">
                  </div>
                  <div class="col-md-6">
                     <label>Assign Restaurants</label>
                     <select>
                        <option>Marmaris</option>
                        <option>Spice Junction</option>
                        <option>HindArab</option>
                        <option>Homemade Heaven</option>
                        <option>Merchant Storea</option>
                        <option>Swaadhisht</option>
                        <option>Pre-order Onam Sadhya</option>
                        <option>Alankar Restaurant &amp; Catering</option>
                        <option>My Kitchen</option>
                        <option>Curry Magic</option>
                        <option>Premaas Cuisine</option>
                        <option>Paradise Biryani</option>
                        <option>Pepper Castle </option>
                        <option>Pepper Castle</option>
                     </select>
                  </div>
               </div>
            </div>
         </div>
        <div class="modal-footer">
           <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Close</button>
           <button type="button" class="btn btn-primary modal_btns">Save</button>
       </div>
      </div>
   </div>
</div>
<!--end modal
</div>-->
<?php

  $current_url =  current_url();
  $parameter_url = explode("promo_codes/0/", $current_url);// if action is load like enable/disable or delete

  if(isset($parameter_url[1])){
   $new_promo_code_url_for_mode = 'admin/promo_codes/table/'.$parameter_url[1].'';
  }else{
    $new_promo_code_url_for_mode = 'admin/promo_codes/table/';
  }

?>
 
<script type="text/javascript">
  var promo_code_active_inactive_url = '<?php echo  base_url('admin/active_inactive_promo_code/'); ?>';
  var promo_code_delete_url = '<?php echo  base_url('admin/delete_Promo_Code/'); ?>';
 var promo_code_table_url = '<?php echo  base_url(''.$new_promo_code_url_for_mode.''); ?>';
</script>
