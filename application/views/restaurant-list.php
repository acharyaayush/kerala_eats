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

<!-- <--Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Restaurant</h1>
      <span>
        <div class="col-md-2">
            <!-- Pass 1 as parameter to call add user form -->
            <a href="<?php echo base_url(); ?>admin/add_edit_restaurant/1" class="btn btn-primary">Add Restaurant</a>
        </div>
    </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Restaurant</div>
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
                                      <select class="custom-select resstatus wv_filter_box_height form-control" name="resstatus">
                                          <option value="">Select Status</option>
                                          <option value="1" <?php if($resturant_status == '1' && $resturant_status != 'all'){echo "selected";} ?>>Active</option>
                                          <option value="2" <?php if($resturant_status == '2' && $resturant_status != 'all'){echo "selected";} ?>>Inactive</option>
                                      </select>
                                  </div>
                                   <div class="form-group col-md-3">
                                      <label>Rating</label>
                                      <select class="custom-select res_rating wv_filter_box_height form-control" name="userstatus">
                                          <?php echo $resturant_rating ;?>
                                            <option value="">Select Rating</option>
                                            <option value="1" <?php if($resturant_rating == '1'&& $resturant_rating != 'all'){echo "selected";} ?>>1</option>
                                            <option value="1.5" <?php if($resturant_rating == '1.5'&& $resturant_rating != 'all'){echo "selected";} ?>>1.5</option>
                                            <option value="2" <?php if($resturant_rating == '2'&& $resturant_rating != 'all'){echo "selected";} ?>>2</option>
                                            <option value="2.5" <?php if($resturant_rating == '2.5'&& $resturant_rating != 'all'){echo "selected";} ?>>2.5</option>
                                            <option value="3" <?php if($resturant_rating == '3'&& $resturant_rating != 'all'){echo "selected";} ?>>3</option>
                                            <option value="3.5" <?php if($resturant_rating == '3.5'&& $resturant_rating != 'all'){echo "selected";} ?>>3.5</option>
                                            <option value="4" <?php if($resturant_rating == '4'&& $resturant_rating != 'all'){echo "selected";} ?>>4</option>
                                            <option value="4.5" <?php if($resturant_rating == '4.5'&& $resturant_rating != 'all'){echo "selected";} ?>>4.5</option>
                                            <option value="5" <?php if($resturant_rating == '5'&& $resturant_rating != 'all'){echo "selected";} ?>>5</option>
                                      </select>
                                  </div>

                                   <div class="form-group col-md-3">
                                      <label>Business Type</label>
                                      <select class="custom-select wv_filter_box_height form-control" name="business_type" id="business_type">
                                          <option value="">Select Business Type</option>
                                            <?php echo $merchant_category_list;?>
                                      </select>
                                  </div>

                                  <div class=" form-group col-md-3">
                                     <label>Food Type</label>
                                      <select class="custom-select  wv_filter_box_height form-control"  id="food_type"  <?php if($food_type == 0 || $food_type == 'all' || $food_type == "" ){ echo 'disabled="disabled"';}?>>
                                          <option value="">Select Food Type</option>
                                          <option value="1"  <?php if($food_type == 1){ echo 'selected';}?>>Restaurant</option>
                                          <option value="2"  <?php if($food_type == 2){ echo 'selected';}?>>Homemade</option>
                                      </select>
                                      <span class="error" id="unselect_food_type"></span>
                                  </div>
                                  <div class="form-group col-md-6">
                                      <form action="javascript:void(0);">
                                          <label>Search</label>
                                          <input type="search" name="search" class="form-control search_key wv_filter_box_height" placeholder="Search" value="<?php if($search != '' && $search != 'all'){echo $search;} ?>" />
                                      </form>
                                  </div>
                              </div>
                          </div>
                          <div class="form-group users_btns">
                              <form action="javascript:void(0);">
                                  <button class="btn btn-primary m-0 mr-2 search_user_list_data" type="button" id="search_restaurant_list_data">Search</button>
                                   <a href="<?php echo base_url() ?>admin/restaurant_list" class="btn btn-secondary mr-2 clear_btns">Clear</a>
                          <button class="btn btn-secondary clear_btns export_restaurant_csv" type="button">Export CSV</button>
                              </form>
                          </div>
                      </div>
                  </div>
                   <?php $this->load->view("validation");?>
                  <div class="card-body catg-tab table-flip-scroll orders-tables tb-scroll customerTableListing">
                     <table class="table" id="res_table">
                          <?php 
                              $this->load->view("restaurant_list_table");
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

<?php

  $current_url =  current_url();
  $parameter_url = explode("restaurant_list/0", $current_url);// if action is load like enable/disable or delete
  if(isset($parameter_url[1])){
     $new_url_for_mode = 'admin/restaurant_list/table/'.$parameter_url[1].'';
  }else{
     $new_url_for_mode = 'admin/restaurant_list/table/';
  }

?>
<script type="text/javascript">
  var res_active_inactive_url = '<?php echo  base_url('admin/active_inactive_restaurant/'); ?>';
  var res_delete_url = '<?php echo  base_url('admin/delete_restaurant/'); ?>';
  var res_table_url = '<?php echo  base_url(''.$new_url_for_mode .''); ?>';
  var res_search_table_url = '<?php echo  base_url('admin/restaurant_list/0/'); ?>';
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
 

