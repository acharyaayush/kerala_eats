<?php
$select_restaurants_list = "";
$restaurant_search_list = "";
 
if (isset($restaurant_list) && $restaurant_list != "" && !empty($restaurant_list)) {
     
    foreach ($restaurant_list as $value) {

         $restaurant_id = $value['id'];
         $rest_name = trim($value['rest_name']);
         $admin_id = $value['admin_id'];
           
         $select_restaurants_list.= '<label class="enabled-label">'.$rest_name.'
                                          <input type="radio" class="select_restaurant_id_for_ad_banner" id="restaurant_id_'.$restaurant_id.'" name="restaurant_id" value="'.$restaurant_id.'" >
                                             <span class="checkmark_check"></span>
                                       </label>';


       // for only search -------start
      if($restaurant_id == trim($selected_restaurant_id)){
              $select = "selected";  
                
        }else{
             $select = "";
        }
        $restaurant_search_list.= '<option value="'.$restaurant_id.'" '.$select.'>'.$rest_name.'</option>'; 
      
          // for only search -------end
      }
}

?> 
<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Ad Banner</h1>
         <span>
            <div class="col-md-2">
                 <a href="#" class="btn btn-primary add_edit_ad_banner_popup" mode="1" data-target="#add_edit_ad_banner_popup" data-toggle="modal"  data-backdrop="static" data-keyboard="false"> Add Banner</a>
            </div>
         </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Ad Banner</div>
         </div>
      </div>
      <div class="add-banner-section">
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
                                            <input  min="2020-01-01" autocomplete="off"placeholder="dd-mm-yyyy" id="fromdate" name="fromdate" max="" class="form-control fromdate wv_filter_box_height" value="<?php if($fromdate != '' && $fromdate != 'all'){echo $fromdate;} ?>" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>To Date</label>
                                            <input autocomplete="off"placeholder="dd-mm-yyyy" id="todate" name="todate" max="" class="form-control todate wv_filter_box_height" value="<?php if($todate != '' && $todate != 'all'){echo $todate;} ?>" />
                                        </div>
                                          <div class="form-group col-md-3">
                                            <label>Status</label>
                                            <select class="custom-select ad_banner_status_search wv_filter_box_height form-control" name="ad_banner_status_search">
                                               <option value="">Select Status</option>
                                                <option value="1" <?php if($banner_status == '1' && $banner_status != 'all'){echo "selected";} ?>>Enable</option>
                                                <option value="2" <?php if($banner_status == '2' && $banner_status != 'all'){echo "selected";} ?>>Disable</option> 
                                              
                                            </select>
                                        </div>
                                         <div class="form-group col-md-3">
                                            <label>Restaurant</label>
                                              <select class="custom-select  wv_filter_box_height form-control search_data" name="restaurant_list" id="search_restaurant_id">
                                                 <option value="">Select Restaurtant</option>
                                                 <?php echo $restaurant_search_list;?>
                                             </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <form action="javascript:void(0);">
                                                <label>Search</label>
                                                <input type="search" name="search" class="form-control search_key wv_filter_box_height" placeholder="Search"  value="<?php if($search != '' && $search != 'all'){echo $search;} ?>"/>
                                            </form>

                                        </div>
                                        <div class="col-md-3 form-group search_clear_btns users_btns">
                                    <form action="javascript:void(0);">
                                        <button class="btn btn-primary m-0 mr-2 search_ad_banner_list_data" id="search_ad_banner_list_data" type="button">Search</button>
                                        <a href="<?php echo base_url() ?>admin/ad_banner_list/" class="btn btn-secondary mr-2 clear_btns">Clear</a>
                                    </form>
                                </div>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="mb-3 users_btns">
                                <!--  <a href="<?php echo base_url() ?>admin/ad_banner_list/" class="btn btn-secondary mr-2">Clear</a> -->
                               <!-- <button class="btn btn-secondary  export_user_csv" type="button">Export Csv</button>-->
                            </div>
                        </div>  
                  
                  <?php $this->load->view("validation");?>
                  <div class="card-bodycatg-tab  table-flip-scroll orders-tables tb-scroll customerTableListing">
                     <table class="table" id="ad_banners_table">
                        <?php 
                             $this->load->view("ad_banners_list_table");
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

      <!-- Modal for Add Banner -->
   <div class="modal fade" id="add_edit_ad_banner_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg read_more_popup" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title ad_banner_title" id="exampleModalLabel"> Ad Banner</h5>
               <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form>
               <div class="modal-body add_category">
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Name</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" maxlength="30" name="name" placeholder="Enter Name" value="" required="" id="ad_banner_name" class="check_space">
                           <span class="error" id="unfill_banner_name"></span>
                        </div>
                     </div>
                  </div>
                  <div class="form-group restaurants_id">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Restaurants ID </label> <label class="switch promocode-status">
                              <input type="checkbox" id="check_restaurant_enable">
                              <span class="slider round"></span>
                              </label>
                              <p class="banner_text">Link the banner to a Restaurants. When the Customer will click on this banner he/she will be redirected to this Restaurants</p>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group has-search">
                              <div class="select_dropdown">
                                <span class="fa fa-search form-control-feedback"></span>
                                <input type="text" onclick="SearchDropdownFunction('SelectBannerRestaurantDropdown')"  placeholder="Search Restaurant" id="SelectBannerRestaurantInput" onkeyup="filterFunction('SelectBannerRestaurantDropdown','SelectBannerRestaurantInput')"  class="form-control check_space SelectEditBannerRestaurantInput" disabled="" value="" selected_rest_id="">
                                <div id="SelectBannerRestaurantDropdown" class="select_dropdown-content SelectEditBannerRestaurantDropdown">
                                     <?php echo  $select_restaurants_list;?>
                                </div>
                              </div>
                                &nbsp;<span class="error" id="unselect_restaurant"></span>
                          </div>
                        </div>
                     </div>
                  </div>
                   <div class="form-group external-links">
                     <div class="row">
                        <div class="col-md-6">
                           <label>External Link </label> <label class="switch promocode-status">
                              <input type="checkbox" id="check_external_link_enable">
                              <span class="slider round"></span>
                              </label>
                              <p class="banner_text">Link the banner to an external link. When the Customer will click on this banner, he/she will be redirected to this link.</p>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group has-search">
                           <span class="form-control-feedback"> https://</span>
                            <input  type="text" name="external-link" placeholder="www.example.com" value="" required="" disabled="" class="form-control" id="external_link">
                             <span class="error" id="unfill_external_link"></span>
                          </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Text </label>
                              <p class="banner_text">You can enter here your own description/note about the banner for your use.</p>
                        </div>
                        <div class="col-md-6">
                            <input  type="text" name="text" placeholder="Enter Text" value="" required=""  minlength="20"  maxlength="200" id="ad_banner_description">
                             <span class="error" id="unfill_description"></span>
                        </div>
                     </div>
                  </div>
                   <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Banner Image For Web (1920x360 px) </label>
                        </div>
                        <div class="col-md-6 ad_banner_image">
                             <img id="disp_img1" src="<?php 
                              if(empty($header['user_data']))
                                 {
                                    echo base_url('assets/img/avatar/avatar-1.png'); 
                                 }else
                                 {
                                    echo base_url($header['user_data']);
                                 }
                               ?>"  alt="">
                           <div class="">                                       
                              <input type="file" id="file1" name="file" onchange="document.getElementById('disp_img1').src = window.URL.createObjectURL(this.files[0])" class="d-none" accept="image/x-png,image/jpeg, image/jpg">
                              <label for="file1"><span>Drag & Drop images, or </span><br><span style="color: #f35d18; cursor: pointer;">BROWSE </span><br>
                              <span>from computer</span></label>
                           </div>
                            <span class="error" id="unselect_banner_web_image"></span>
                        </div>
                     </div>
                  </div>

                   <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Banner Image For Mobile Web (768x384 px)</label>
                        </div>
                        <div class="col-md-6 ad_banner_image">
                             <img id="disp_img2" src="<?php 
                              if(empty($header['user_data']))
                                 {
                                    echo base_url('assets/img/avatar/avatar-1.png'); 
                                 }else
                                 {
                                    echo base_url($header['user_data']);
                                 }
                               ?>"  alt="">
                           <div class="">                                       
                              <input type="file" id="file2" name="file" onchange="document.getElementById('disp_img2').src = window.URL.createObjectURL(this.files[0])" class="d-none" accept="image/x-png,image/jpeg, image/jpg">
                              <label for="file2"><span>Drag & Drop images, or </span><br><span style="color: #f35d18; cursor: pointer;">BROWSE </span><br>
                              <span>from computer</span></label>
                           </div>
                            <span class="error" id="unselect_banner_web_mobile_image"></span>
                        </div>
                     </div>
                  </div>
                   <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Banner Image For Native Mobile Apps <br>(1920x480 px)* / (768x384 px)**</label>
                        </div>
                        <div class="col-md-6 ad_banner_image">
                             <img id="disp_img3" src="<?php 
                              if(empty($header['user_data']))
                                 {
                                    echo base_url('assets/img/avatar/avatar-1.png'); 
                                 }else
                                 {
                                    echo base_url($header['user_data']);
                                 }
                               ?>"  alt="">
                           <div class="">                                       
                              <input type="file" id="file3" name="file" onchange="document.getElementById('disp_img3').src = window.URL.createObjectURL(this.files[0])" class="d-none" accept="image/x-png,image/jpeg, image/jpg">
                              <label for="file3"><span>Drag & Drop images, or </span><br><span style="color: #f35d18; cursor: pointer;">BROWSE </span><br>
                              <span>from computer</span></label>
                           </div>
                             <span class="error" id="unselect_banner_mobile_image"></span>
                        </div>
                     </div>
                  </div>
                 
               </div>
               <div class="modal-footer">
                  <input type="hidden" id="edit_ad_banner_id" value=""/>
                  <input type="hidden" id="ad_banner_add_edit_mode"/>
                  <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary modal_btns" id="add_edit_banner_submit">Save</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!--end modal-->
 
</div>
<?php

  $current_url =  current_url();
  $parameter_url = explode("ad_banner_list/0", $current_url);// if action is load like enable/disable or delete
  ////by default table value is 0
 

  if(isset($parameter_url[1])){

   $new_users_url_for_mode = 'admin/ad_banner_list/table/'.$parameter_url[1].'';
  }else{

    $new_users_url_for_mode = 'admin/ad_banner_list/table/';
  }

?>
<script type="text/javascript">
   // for delete
   var ad_banner_table_url = '<?php echo  base_url(''.$new_users_url_for_mode.''); ?>';
   
   // for edit -------
   var exist_web_banner_image = "";
   var exist_web_mobile_banner_image = "";
   var exist_mobile_banner_image = "";
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