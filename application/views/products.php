
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
    <style type="text/css">
img {
  display: block;
  max-width: 100%;
}
.preview {
  overflow: hidden;
  width: 160px; 
  height: 160px;
  margin: 10px;
  border: 1px solid red;
}
.modal-lg{
  max-width: 1000px !important;
}
</style>

<?php
  //For Restaurant Select box------------------------START-------------
  $restaurant_list = "";
 
  if (isset($resturant_details) && $resturant_details != "" && !empty($resturant_details)) {

      $count = 1;
      foreach ($resturant_details as $value) {
        $restaurant_id = $value['restaurant_id'];
        $restaurant_name = $value['rest_name'];


        if($count == 1 || $restaurant_id == trim($selected_restaurant_id)){
              $select = "selected";  
                
        }else{
             $select = "";
            
        }
        $restaurant_list.= '<option value="'.$restaurant_id.'" '.$select.'>'.$restaurant_name.'</option>';
         $count++;
      }
  }else{
    $restaurant_list = '<option value="">No Restaurant available </option>';
  }
   //For Restaurant Select box------------------------END-------------


  //For category Select box------------------------START-------------
  $category_list = "";
 
 
  if (isset($category_select_box_data) && $category_select_box_data != "" && !empty($category_select_box_data)) {

      
      foreach ($category_select_box_data as $value) {
        $category_id = $value['category_id'];
        $category_name = $value['category_name'];


        if($category_id == trim($selected_category_id)){
              $select_category = "selected";  
                
        }else{
             $select_category = "";
            
        }

        $category_list.= '<option value="'.$category_id.'" '.$select_category.'>'.$category_name.'</option>';
         
      }
  }else{
    $category_list = '<option value="">No Category available </option>';
  }
   //For category Select box------------------------END-------------
?>
<?php
//For redirect import import csv -  when we import csv , succusfully import , redirect on last exist current  url-----becouse in we use category and restaurant id in selectbox by useing products controller with passing parameters
$currentURL = current_url(); 
$params   = $_SERVER['QUERY_STRING']; 

$fullURL = $currentURL. $params; 

$last_url_before_import =  str_replace("/index.php","",$fullURL); 
?>

 
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Products</h1>
            <span>
                <div class="col-md-2">
                    <!-- Pass 1 as parameter to call add user form -->
                    <!-- <a href="<?php echo base_url('admin/addEditUserView/1')?>" class="btn btn-primary">Add User</a> -->
                </div>
            </span>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
                <div class="breadcrumb-item">Products</div>
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
                            <div class="d-flex">
                                <div class="product-filterForm">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>From Date</label>
                                           <input  min="2020-01-01" autocomplete="off"placeholder="dd-mm-yyyy" id="fromdate" name="fromdate" max="" class="form-control fromdate " value="<?php if($fromdate != '' && $fromdate != 'all'){echo $fromdate;} ?>" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>To Date</label>
                                           <input  min="2020-01-01" autocomplete="off"placeholder="dd-mm-yyyy"  id="todate" name="todate" max="" class="form-control todate" value="<?php if($todate != '' && $todate != 'all'){echo $todate;} ?>" />
                                        </div>
                                         <div class="form-group col-md-4">
                                          <label>Status</label>
                                          <select class="custom-select products_mode wv_filter_box_height form-control" name="products_status" id="products_status">
                                              <option value="">Select Status</option>
                                              <option value="1" <?php if($product_status == '1' && $product_status != 'all'){echo "selected";} ?>>Enabled</option>
                                              <option value="2" <?php if($product_status == '2' && $product_status != 'all'){echo "selected";} ?>>Disabled</option>
                                          </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                          <label>Food Type</label>
                                          <select class="custom-select products_mode wv_filter_box_height form-control" name="product_food_type" id="product_food_type">
                                              <option value="">Select Food Type</option>
                                              <option value="1" <?php if($product_food_type == '1' && $product_food_type != 'all'){echo "selected";} ?>>Veg</option>
                                              <option value="2" <?php if($product_food_type == '2' && $product_food_type != 'all'){echo "selected";} ?>>Non Veg</option>
                                          </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <form action="javascript:void(0);">
                                                <label>Search</label>
                                                <input type="search" name="search" class="form-control search_key wv_filter_box_height"  placeholder="Search" value="<?php if($search != '' && $search != 'all'){echo $search;} ?>" />
                                            </form>
                                        </div>
                                        <?php 

                                        //  if merchant is logged in, then this condition will check and only merchant restaurant prodcuts will show. if this blank that means super admin is logged in and then all resataurant will show
                                        if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){
                                         ?>
                                         <div class="form-group col-md-4">
                                          <label>Restaurant</label>
                                          <select class="custom-select  wv_filter_box_height form-control search_data" name="restaurant_list" id="restaurant_list">
                                               <option value="" disabled="">Select Restaurant</option>
                                              <?php echo $restaurant_list;?>
                                          </select>
                                        </div>
                                        <?php
                                         }

                                        ?>
                                        <div class="form-group col-md-4">
                                          <label>Category</label>
                                          <select class="custom-select  wv_filter_box_height form-control search_data" name="category_list" id="category_list">
                                              <option value="" disabled="">Select Category</option> 
                                              <?php echo $category_list; // for on load only it will be on change of restaurat by ajax?>
                                          </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <div class="users_btns search_clear_btns  d-flex mt-20">
                                                <button class="btn btn-primary search_data" id="search_product_data"  type="button">Search</button>
                                                <a href="<?php echo base_url() ?>admin/products/<?php echo $selected_restaurant_id_url;?>" class="btn btn btn-secondary m-0 clear_btns" > Clear</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 
                            </div>
                        </div>
 
                         <?php $this->load->view("validation");?>

                        <?php
                           if(isset($this->logged_in_restaurant_id ) && $this->logged_in_restaurant_id == "" && $this->role == 2){
                                $style_disable = 'style= "pointer-events: none;"';
                                $msg =   "<h6><b><span class='text-danger'>You can't add products because you did not register for a restaurant yet or not added you for your restaurant by admin.</span></b></h6>";
                            }else{
                                 $style_disable = "";
                                  $msg  = "";
                            }
                          ?>
                        
                        <div class="card-body" <?php echo $style_disable;?>>
                          <?php echo $msg ;?>
                            <div class="row" id="product_and_category_list_table">
                                <?php 
                                    $this->load->view("product_and_category_list_table");
                                    //this is seprate for only table load by js by any action event
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         
    </section>

   <!-- import data modal START -->
    <div class="modal fade" id="import_export_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog read_more_popup" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import/Export</h5>
                    <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
               <div class="modal-body">
                 <?php 
                    //  if merchant is logged in, then this condition will check and only merchant restaurant prodcuts will show and only add category will add only only logged on restaurant od. if this blank that means super admin is logged in.
                      if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){
                         
                     ?>
                  <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Restaurant Name</label>
                            </div>
                            <div class="col-md-8">
                                 <h6  class="selected_restaurant_name"> Selected Restaurant  </h6>
                            </div>
                        </div>
                    </div>
                    <?php
                      }
                    ?>
                    <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Category Name</label>
                                </div>
                                <div class="col-md-8">
                                     <h6  class="selected_category_name"></h6>
                                     <span class="text-danger null_category_error"></span>
                                </div>
                            </div>
                        </div>
                    <!--Nav Tab start-->
                    <div>
                        <ul id="tabs" class="nav nav-tabs importExportPopup" role="tablist">
                            <li class="nav-item">
                                <a id="tab_category" href="#category" class="nav-link active" data-toggle="tab" role="tab">Category</a>
                            </li>
                            <li class="nav-item">
                                <a id="tab_product" href="#product" class="nav-link" data-toggle="tab" role="tab">Product</a>
                            </li>
                            <li class="nav-item">
                                <a id="tab_combined" href="#combined" class="nav-link" data-toggle="tab" role="tab">Combined</a>
                            </li>
                        </ul>

                        <div id="content" class="tab_combinedontent" role="tablist">
                            <div class="card tab-pane fade show active" role="tabpanel" aria-labelledby="tab_category">
                                <!-- Note: New place of `data-parent` -->
                                <div class="collapse show" data-parent="#content" role="tabpanel" aria-labelledby="heading-A">
                                    <div class="card-body">
                                       <!-- [Tab content A] -->
                                       <div class="text-center">
                                            <button class="btn btn-primary btn-lg export_products_csv" export_type ="category" type="button">Export <i class="fas fa-download"></i></button>
                                       </div>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?php echo base_url();?>admin/uploadData" method="post" enctype="multipart/form-data">
                                             <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label> Upload <span class="export_label">Category</span> :</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <input type="file" name="uploadFile" value="" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required="" class="import_disable" />
                                                        <input type="hidden" name="import_type" id="import_type" value="category"/>
                                                         <input type="hidden" name="selected_restaurant_id" id="selected_restaurant_id"  />
                                                        <input type="hidden" name="selected_category_id" id="selected_category_id"  />
                                                        <input type="hidden" name="last_url_before_import" id="last_url_before_import" value="<?php echo $last_url_before_import;?>" />
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="text-right">
                                                        <a href="<?php echo base_url();?>assets/sample_csv/product_csv/category.csv" download="" class="btn btn-secondary btn-lg sample_products_csv" type="button">Sample Download <i class="fas fa-download"></i></a>    
                                                      </div>
                                                </div>
                                                 <div class="col-sm-6">
                                                    <div class="text-left">
                                                          <!--  <input class="btn btn-primary btn-lg" type="submit" name="submit" value="Upload" /> -->
                                                           <button class="btn btn-primary btn-lg import_disable" name="submit" type="submit" value="Upload" id="show_import_loader">Import <i class="fas fa-file-import"></i></button>
                                                      </div>
                                                      <div class="text-right d-none" id="import_process_loader">
                                                         <img style="width: 40px;
    margin-top: -66px;" src="<?php echo base_url('assets/images/preview-chat-loader-2.gif')?>" />
                                                      </div>
                                                </div>
                                                
                                                <div id="alert_for_category_disabled_time">
                                                   
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Nav Tab end-->
 
                    
                </div><!--modal body-->
               
            </div>
        </div>
    </div>
  <!-- import data  modal end -->

  <!-- Modal for Offline /online Category START -->
  <div class="modal fade" id="offline_online_category_or_product_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title  enable_disable_product_category_title" id="exampleModalLabel"></h5><!--// same  modal use for product offline online-->
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                   <label>Select Day</label>
                                   <input min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" id="days_offline_fromdate" name="days_offline_fromdate" class="form-control date_valid" value=""> 
                                   &nbsp;<span class="error" id="unfill_days_offline_value"></span>
                                </div>
                              </div>
                          </div>
                          <div id="select_multiple_days" class="d-none">
                            <div class="row">
                               <div class="form-group col-md-6">
                                  <label>From Date</label>
                                  <input min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" id="offline_product_category_fromdate" name="fromdate" max="" class="form-control date_valid" value=""> 
                                   &nbsp;<span class="error" id="unfill_offline_product_category_fromdate"></span>
                                  
                                </div>
                                 <div class="form-group col-md-6">
                                   <label>Till Date</label>
                                   <input min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" id="offline_product_category_tilldate" name="todate" max="" class="form-control date_valid" value="" > 
                                    &nbsp;<span class="error" id="unfill_offline_product_category_tilldate"></span>
                                </div>
                                
                              </div>
                              
                        </div>
                      </div>
                    </div>
                  </div>
              </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <input type="hidden" id="offline_tag" value="1"/><!--it will change on click on tag button by default its 1 for hours-->
          <input type="hidden" id="offline_tag_value" value=""/>
          <input type="hidden" id="offline_mode_id" value=""/>
          <button type="button" class="btn btn-primary  offline_online_category_or_product_submit" >Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal for Offline /online Category END -->
  
    <!-- Modal for Add category -->
    <div class="modal fade" id="add_edit_category_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl read_more_popup" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title category_modal_title" id="exampleModalLabel"></h5>
                    <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- this input above to form because modal form reset checking every second- and this input must be fillable ---->
                <input type="hidden" id="cat_mode_type" value="" />
                <input type="hidden" id="edit_category_image_exist" value="" />
                <input type="hidden" id="edit_category_id" value="" />
                <form id="add_edit_category_form">
                    <div class="modal-body add_category">
                       <?php 
                            //  if merchant is logged in, then this condition will check and only merchant restaurant prodcuts will show and only add category will add only only logged on restaurant od. if this blank that means super admin is logged in.
                              if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){
                                 
                             ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Restaurant Name</label>
                                </div>
                                <div class="col-md-8">
                                     <h6  class="selected_restaurant_name"> Selected Restaurant </h6>
                                </div>
                            </div>
                        </div>
                        <?php
                          }
                        ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Category Name</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="category_name" placeholder="Enter category name" id="category_name" value="" required="">
                                    &nbsp;<span class="error" id="unfill_category_name"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Description</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea maxlength="360" minlength="50" name="description" placeholder="Description" id="category_description" value="" required=""></textarea>
                                      &nbsp;<span class="error" id="unfill_category_discription"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                         <div class="row">
                            <div class="col-md-4">
                               <label>Category Image</label>
                            </div>
                             <div class="col-md-8 user-img">
                                    <img id="cat_disp_img" src="" alt="">
                                    <div class="img-add">
                                        <input type="file" id="file" name="product_cateogry_image" onchange="document.getElementById('cat_disp_img').src = window.URL.createObjectURL(this.files[0])" accept="image/x-png,image/jpeg, image/jpg" required="">
                                        <label for="file" class="addimageplus"><i class="fas fa-plus" ></i></label>
                                         <label class="delete_selected_cat_img" style="
                                            left: 20px;
                                        "><i class="fas fa-trash" style="color: red;"></i></label>
                                         &nbsp;<span class="error" id="unfill_product_image"></span>
                                    </div>
                                </div>
                           <!--  <div class="col-md-8 admin-profile-img">
                               <img id="cat_disp_img" src=""  alt="">
                                     <div class="img-add">               
                                        <input type="file" id="file" name="product_cateogry_image" onchange="document.getElementById('cat_disp_img').src = window.URL.createObjectURL(this.files[0])" accept="image/x-png,image/jpeg, image/jpg" required="">
                                        <label for="file" class="addimageplus"><i class="fas fa-plus" ></i></label>
                                        <label class="delete_selected_cat_img" style="
                                            right: 184px;
                                        "><i class="fas fa-trash"></i></label>
                                     </div>
                                     &nbsp;<span class="error" id="unfill_category_image"></span>
                            </div> -->
                         </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal" >Cancel</button>
                        <button type="reset" id="modal_data_reset_btn" class="d-none">Reset</button>
                        
                        <button type="button" id="category_submit" class="btn btn-primary modal_btns">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end modal-->



  <!-- Modal for Add  Edit Product  ---------START -------------------->
     <!-- this input above to form because modal form reset checking every second- and this input must be fillable ---->
    <input type="hidden" id="product_mode_type" value="" />
    <input type="hidden" id="edit_product_image_exist" value="" />
    <input type="hidden" id="edit_product_id" value="" />

    <div class="modal fade" id="add_edit_product_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg read_more_popup" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title product_modal_title" id="exampleModalLabel">Add Product</h5>
                    <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body add_category">

                         <div class="form-group admin-input-field">
                             <div class="row">
                               <?php 
                                  //  if merchant is logged in, then this condition will check and only merchant restaurant prodcuts will show and only add category will add only only logged on restaurant od. if this blank that means super admin is logged in.
                                    if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){

                                      $class_for_category = 'col-md-6';
                                       
                                   ?>
                                <div class="col-md-6">
                                    <label>Restaurant Name</label><br>
                                     <h6  class="selected_restaurant_name"> Selected Restaurant </h6>
                                </div>
                              <?php
                                 }else{
                                   $class_for_category = 'col-md-12';
                                 }
                               ?>
                                <div class="<?php echo $class_for_category;?>">
                                          <label style="width: 100%">Category Name</label><br>
                                          <h6  class="selected_category_name"></h6>
                                          <span class="text-danger null_category_error"></span>
                                </div>
                             </div>
                          </div>


                         <div class="form-group admin-input-field">
                             <div class="row">
                                <div class="col-md-6">
                                   <label>Product Name</label>
                                    <input type="text" name="product_name" placeholder="Enter Product Name" class="check_space" id="product_name" value="" required="">
                                    &nbsp;<span class="error" id="unfill_product_name"></span>
                                </div>
                                <div class="col-md-6">
                                   <label>Food Type</label>
                                       <select class="custom-select products_mode wv_filter_box_height form-control" name="food_type" id="food_type">
                                          <option value="">Select Food Type</option>
                                          <option value="1"  id="veg_select">Veg</option>
                                          <option value="2" selected="" id="default_select_non">Non Veg</option>
                                      </select>
                                       &nbsp;<span class="error" id="unselect_food_type"></span>
                                </div>
                             </div>
                          </div>
                         
                     
                         <div class="form-group admin-input-field">
                            <div class="row">
                                 <div class="col-md-6">
                                    <label>Product Price</label>
                                     <!--  <span class="currency">S$</span> -->
                                   
                                     <input type='number' id="product_price" step='0.01'   placeholder='0.00' class="check_space" />
                                     &nbsp;<span class="error" id="unfill_product_price"></span>
                                </div>
                                <div class="col-md-6">
                                   <label>Offer Price</label>
                                    <!-- <span class="currency">S$</span> -->
                                    <input type="number" step='0.01'  placeholder='0.00'  class="check_space" name="product_offer_price" id="product_offer_price"  value="" required="">
                                      &nbsp;<span class="error" id="unfill_product_offer_price"></span>
                                </div>
                            </div>
                        </div>

                         <div class="form-group admin-input-field">
                             <div class="row">
                                <div class="col-md-6">
                                   <label>Minimum Quantity </label>
                                   <input type="text" class="check_space" id="minimum_quantity" maxlength="3" minlength="1" name="minimum_quantity" placeholder="Minimum Quantity" value="1" required="">
                                    &nbsp;<span class="error" id="unfill_minimum_quantity"></span>
                                </div>
                               <!--  <div class="col-md-6">
                                   <label>Maximum Quantity</label>
                                   <input type="text" maxlength="3" minlength="1" class="check_space" id="maximum_quantity" name="maximum_quantity" placeholder="Maximum Quantity" value="" required="">
                                    &nbsp;<span class="error" id="unfill_maximum_quantity"></span>
                                </div> -->
                             </div>
                          </div>

                        <div class="form-group admin-input-field">
                            <div class="row">
                                 <div class="col-md-6">
                                   <label>Short Description</label>
                                      <textarea maxlength="100" minlength="50" class="check_space" name="product_short_discription" placeholder="Short Description" id="product_short_discription" value="" required=""></textarea>
                                     &nbsp;<span class="error" id="unfill_product_short_discription"></span>
                                </div>
                                 <div class="col-md-6">
                                   <label>Long Description</label>
                                   <textarea  maxlength="360" minlength="50" class="check_space" name="product_long_discription" placeholder="long Description" id="product_long_discription"  required=""></textarea>
                                     &nbsp;<span class="error" id="unfill_product_long_discription"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="cropme" style="width: 405px; height: 200px;"></div>
                        </div>
                        <!-- <form method="post">
    <input type="file" name="image" class="image">
    </form> -->

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Product Image</label>
                                </div>
                                <div class="col-md-8 user-img">
                                    <img id="product_disp_img" src="" alt="">
                                    <div class="img-add">
                                        <input type="file" id="product_image" name="product_image" onchange="document.getElementById('product_disp_img').src = window.URL.createObjectURL(this.files[0])" accept="image/x-png,image/jpeg, image/jpg" >
                                        <label for="product_image " class="addimageplus"><i class="fas fa-plus"></i></label>
                                         <label class="delete_selected_product_img" style="
                                            left: 20px;
                                        "><i class="fas fa-trash" style="color: red;"></i></label>
                                         &nbsp;<span class="error" id="unfill_product_image"></span>
                                    </div>
                                </div>
                                 
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary modal_btns" id="product_submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal for Add  Edit Product  END -->

   <!-- import data modal START -->
    <div class="modal" id="edit_variant_name" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered read_more_popup" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Variant Name</h5>
                    <button type="button" class="close close_btnn close_edit_variant_name_modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
               <div class="modal-body">
                   <table class="table category_tables" id="category_table">
                                <thead>
                                    <tr> 
                                      <th colspan="5"></th>
                                      <th colspan="1"><input class="form-control" id="search_variant" type="text" placeholder="Search Variant.."></th>
                                    </tr>
                                    <tr>
                                        <th colspan="5">Variant Name</th>
                                        <th colspan="1" class="text-right">Edit</th>
                                    </tr>
                                </thead>
                                <tbody id="variant_table_data">
                                    <?php $this->load->view("variant_name_edit");?>
                                  </tbody>
                                </table>
                </div><!--modal body-->
            </div>
        </div>
    </div>
  <!-- import data  modal end -->


   <!-- import data modal START -->
    <div class="modal" id="edit_variant_type_name" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered read_more_popup" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Variant Type Name</h5>
                    <button type="button" class="close close_btnn close_edit_variant_type_name_modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
               <div class="modal-body">
                   <table class="table category_tables" id="category_table">
                      <thead>
                          <tr> 
                            <th colspan="4">
                              <select class="custom-select  form-control" name="" id="select_variant_for_edit_type">
                              </select>
                            </th>
                            <th colspan="2"><input class="form-control" id="search_variant_type" type="text" placeholder="Search Variant Type.."></th>
                          </tr>
                          <tr>
                              <th colspan="5">Variant  Type Name</th>
                              <th colspan="1" class="text-right">Edit</th>
                          </tr>
                      </thead>
                      <tbody id="variant_type_table_data">
                          <?php $this->load->view("variant_type_name_edit");?>
                        </tbody>
                      </table>
 
                </div><!--modal body-->
            </div>
        </div>
    </div>
  <!-- import data  modal end -->

  <!-- MODAL for ADD VARIANT TYPE -------------START------------->
        <div class="modal" id="add_edit_variant_in_product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"><!--id add_variant_type_modal for add variant type , edit_variant_type_modal for edit it will be add when click on edit_variant_type_modal_btn or add_variant_type_modal_btn-->
        <div class="modal-dialog modal-lg modal-dialog-centered read_more_popup" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title variant_modal_title" id="exampleModalLabel">Add variants/add-ons</h5>
                    <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body">
                      <div class="form-group admin-input-field">
                         <div class="row">
                            <div class="col-md-6">
                                   <p class="text-danger">Note - If the variant is not in the list then you can add it from here.</p>
                                    <div class="row">
                                      <div class="col-sm-12">
                                            <label>Variants/Add-ons Name: </label>
                                        </div>
                                        <div class="col-sm-7">
                                             <input type="text"  minlength="5" maxlength="40" class="check_space text-capitalize" id="variant_name"> 
                                             &nbsp;<span class="error" id="unfill_variant_name"></span>
                                        </div>
                                        <div class="text-center col-sm-2 pl-2 mr-10">
                                             <button type="button"  class="btn btn-primary add_variant_btn" id="add_variant">Add</button>
                                        </div>
                                         <div class="text-center col-sm-2">
                                             <button type="button" class="btn btn-primary edit_variant_name" data-target="#edit_variant_name" data-toggle="modal" id="add_variant_type_submit">Edit</button>
                                        </div>
                                      </div>

                                 <div class="form-group admin-input-field" id="select_variant_for_product">
                                     <?php $this->load->view("variant_select_for_product");?>
                                       &nbsp;<span class="error" id="unselect_variant"></span>
                                 </div>
                            </div>
                             <div class="col-md-6">
                                   <p class="text-danger">Note - If the variant type is not in the list then you can add it from here.</p>
                                    <div class="row">
                                      <div class="col-sm-12">
                                            <label>Variant Type: </label>
                                        </div>
                                        <div class="col-sm-7">
                                             <input type="text"  minlength="5" maxlength="40" class="check_space text-capitalize" id="variant_type_name"> 
                                             &nbsp;<span class="error" id="unfill_variant_type_name"></span>
                                        </div>
                                        <div class="text-center col-sm-2 pl-2 mr-10">
                                            <button type="button" class="btn btn-primary add_variant_btn" id="add_variant_type_submit">Add</button>
                                        </div>
                                           <div class="text-center col-sm-2">
                                             <button type="button" class="btn btn-primary edit_variant_type_name" data-target="#edit_variant_type_name" data-toggle="modal">Edit</button>
                                        </div>
                                      </div>

                                 <div class="form-group admin-input-field" id="select_variant_type_for_product">
                                    <?php $this->load->view("variant_type_select.php");?>
                                      &nbsp;<span class="error" id="unselect_variant_type"></span>
                                 </div>
                            </div>
                        </div>
                        </div>
                        <div class="form-group admin-input-field">
                            <div class="row">
                                <div class="col-sm-4"> 
                                   <label>Type</label>
                                </div>
                                 <div class="col-sm-4"> 
                                    <label>Price </label>
                                </div>
                                 <div class="col-sm-4"> 
                                    <label>Default </label>
                                </div>
                            </div>
                            <div id="selected_variant_type_for_add_price" add_edit_mode="">
                                <p class="error"> Please select variant type</p>
                                 <!--element will be add by jquery-->
                            </div>
                             <div class="form-group admin-input-field text-right">
                              <div class="row">
                                 <div class="col-sm-12 text-right d-none" id="show_clear_default_btn"> 
                                   <h6><a href="javascript:void(0)" id="clear_default_selection" class="text-danger">Clear Default </a></h6>
                                 </div>
                              </div>
                            </div>
                        </div>
                       <div class="form-group admin-input-field">
                          <label>Select Type</label>
                            <div class="row">
                               <div class="col-md-5">
                                  <div class="d-flex">
                                       <label class="enabled-label" data-children-count="1">Single
                                         <input type="radio" id="single_select" class="variant_select_type" value="1" name="variant_select_type_for_order" checked="">
                                            <span class="checkmark"></span>
                                         </label>`
                                         <label class="enabled-label"  data-children-count="1">Multiple
                                         <input type="radio"  id="multi_select" class="variant_select_type"  name="variant_select_type_for_order" value="2">
                                             <span class="checkmark"></span>
                                         </label>
                                  </div>
                               </div>
                                <div class="col-md-7">
                                  <div class="row d-none" id="give_select_variant_limit">
                                        <div class="col-md-8">
                                             <label class="enabled-label" data-children-count="1">Customer Can Select Excatly 
                                               <input type="checkbox" value="1" name="" id="select_limit_of_variant">
                                                  <span class="checkmark_check"></span>
                                               </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" disabled="" minlength="1" maxlength="5" class="check_space" id="variant_select_limit"> 
                                              &nbsp;<span class="error" id="unfill_variant_select_limit"></span>
                                        </div>
                                     </div>
                                  </div>
                            </div>
                        </div>
                         <div class="form-group admin-input-field">
                            <label>Is Variant Mandatory</label>
                              <div class="row">
                                  <div class="col-md-5">
                                  <div class="d-flex">
                                       <label class="enabled-label" data-children-count="1">Yes
                                         <input type="radio" id="yes_mandatory" class="is_variant_mandatory" value="1" name="is_variant_mandatory">
                                            <span class="checkmark"></span>
                                         </label>`
                                         <label class="enabled-label"  data-children-count="1">No
                                         <input type="radio" id="no_mandatory" class="is_variant_mandatory" value="0" name="is_variant_mandatory"  checked="">
                                             <span class="checkmark"></span>
                                         </label>
                                  </div>
                               </div>
                              </div>
                        </div>
                    </div>
                    <div class="modal-footer add-edit-variant-popup">
                     <div class="text-left">
                          <button type="button"  data-target="#view_avaible_variant_in_product" data-toggle="modal"  class="btn btn-primary view_avaible_variant_in_product view-edit-delete-variant" id="view_product_variant">View / Edit/ Delete Add-ons</button>
                      </div>
                       <div class="text-right">
                         <input type="hidden" id="selected_product_for_add_variant"/>
                          <input type="hidden" id="selected_variant_id_for_product"/>
                         
                          <button type="button"  class="btn btn-primary add_variant_btn" id="submit_product_variant">Save</button>
                           <button type="button" class="btn btn-secondary modal_btns"  data-dismiss="modal" aria-label="Close">Cancel</button>
                      </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- MODAL for ADD VARIANT TYPE -------------END------------->

  <!-- MODAL for ADD VARIANT TYPE -------------START------------->
        <div class="modal" id="view_avaible_variant_in_product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"><!--id add_variant_type_modal for add variant type , edit_variant_type_modal for edit it will be add when click on edit_variant_type_modal_btn or add_variant_type_modal_btn-->
        <div class="modal-dialog modal-lg modal-dialog-centered read_more_popup" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Available variants/add-ons</h5>
                    <button type="button" class="close close_btnn close_edit_delete_variant_modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12" id="show_product_variant">
                                <?php $this->load->view("variant_show_all_variant_in_product.php");?>
                             
                        </div> 
                    </div>
                    <!-- <div class="modal-footer">
                       <button type="button" class="btn btn-secondary modal_btns">Cancel</button>
                    </div> -->
                </form>
            </div>
        </div>
    </div>
    <!-- MODAL for ADD VARIANT TYPE -------------END------------->
    


</div>
     <!-- MODAL for Cropp -------------START------------->
     <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">PHP Crop Image Before Upload using Cropper JS - LaravelCode</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="img-container">
            <div class="row">
                <div class="col-md-8">
                    <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
                </div>
                <div class="col-md-4">
                    <div class="preview"></div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="crop">Crop</button>
      </div>
    </div>
  </div>
</div>
     

<div class="container mt-4">
        <input type="file" name="file" id="img-crop" accept="image/*" />
    </div>

    <div id="imageModel" class="modal fade bd-example-modal-lg" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Crop and Resize Image</h4>
                </div>
                <div class="modal-body">
                    <div id="img_prev" style="width:400px;"></div>
                    <button class="btn btn-primary btn-block crop_my_image">Store</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL for Cropp -------------END------------->
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
            $j("#todate,#fromdate").datepicker("option", "minDate", dt);
        }
    });
    $j("#todate,").datepicker({
        dateFormat: 'yy-mm-dd',
        // numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() - 1);
            // $j("#fromdate").datepicker("option", "maxDate", dt);
        }
    });

    $j( ".row_position" ).sortable({
        scroll: true, scrollSpeed: 3,
        delay: 150,  
        stop: function() {  
            var selectedData = new Array();  
            $j('.row_position>tr').each(function() {  
                selectedData.push($(this).attr("id"));  
            });  
            updateOrder(selectedData);  
        }  
    });  
  
    function updateOrder(data) {  
        // console.log("data varis"+data);
        // alert("UPDATEORDER"+BASE_URL);
        $.ajax({  
            url:BASE_URL+'admin/update_category_product_poistion_order',  
            type:'POST',  
            data:{position:data , type : 1},  // type 1 # FOR CATEGORY POSITIONING
            success:function(reseponse){  
                console.log(reseponse);
                // alert('your change successfully saved');  
            }  
        })  
    }  
    $j( ".product_row_position" ).sortable({
        scroll: true, scrollSpeed: 3,
        delay: 150,  
        stop: function() {  
            var selectedData = new Array();  
            $j('.product_row_position>tr').each(function() {  
                selectedData.push($(this).attr("id"));  
            });  
            updateProductOrder(selectedData);  
        }  
    });  
  
    function updateProductOrder(data) {  
        // console.log("data varis"+data);
        // alert("UPDATEORDER"+BASE_URL);
        $.ajax({  
            url:BASE_URL+'admin/update_category_product_poistion_order',  
            type:'POST',  
            data:{position:data , type : 2},  // type 2 # FOR PRODUCT POSITIONING
            success:function(reseponse){  
                // console.log("PRODUCT RESPNSE "+reseponse);
                // alert('your change successfully saved');  
            }  
        })  
    }
</script> 
 <!-- Javascript -->
 <!---For time picker with date you need to only replace only datepickerv with datetimepicker---->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
  <script type="text/javascript">
      var $j2 = jQuery.noConflict();       
         $j2("#offline_product_category_fromdate").datepicker({
          dateFormat: "dd-mm-yy",
           minDate: 0,
          onSelect: function (date) {
              var dt2 = $j2('#offline_product_category_tilldate');
              var startDate = $j2(this).datepicker('getDate');
              var minDate = $j2(this).datepicker('getDate');
              if (dt2.datepicker('getDate') == null){
                dt2.datepicker('setDate', minDate);
              }              
              //dt2.datepicker('option', 'maxDate', '0');
              dt2.datepicker('option', 'minDate', minDate);
          }
        });
        $j2('#offline_product_category_tilldate').datepicker({
            dateFormat: "dd-mm-yy",
            minDate: 0
        });           
    
  </script>
 <!-- Javascript -->
  <script type="text/javascript">
      var $j3 = jQuery.noConflict();       
         $j3("#days_offline_fromdate").datepicker({//datetimepicker
          dateFormat: "dd-mm-yy",
           minDate: 0,
        });
  </script>
 
<script type="text/javascript">
    function create_custom_dropdowns() {
        $('select').each(function(i, select) {
            if (!$(this).next().hasClass('dropdown-select')) {
                $(this).after('<div class="dropdown-select wide ' + ($(this).attr('class') || '') + '" tabindex="0"><span class="current"></span><div class="list"><ul></ul></div></div>');
                var dropdown = $(this).next();
                var options = $(select).find('option');
                var selected = $(this).find('option:selected');
                dropdown.find('.current').html(selected.data('display-text') || selected.text());
                options.each(function(j, o) {
                    var display = $(o).data('display-text') || '';
                    dropdown.find('ul').append('<li class="option ' + ($(o).is(':selected') ? 'selected' : '') + '" data-value="' + $(o).val() + '" data-display-text="' + display + '">' + $(o).text() + '</li>');
                });
            }
        });

        $('.dropdown-select ul').before('<div class="dd-search"><input id="txtSearchValue" autocomplete="off" onkeyup="filter()" class="dd-searchbox" type="text"></div>');
    }

    // Event listeners

    // Open/close
    $(document).on('click', '.dropdown-select', function(event) {
        if ($(event.target).hasClass('dd-searchbox')) {
            return;
        }
        $('.dropdown-select').not($(this)).removeClass('open');
        $(this).toggleClass('open');
        if ($(this).hasClass('open')) {
            $(this).find('.option').attr('tabindex', 0);
            $(this).find('.selected').focus();
        } else {
            $(this).find('.option').removeAttr('tabindex');
            $(this).focus();
        }
    });

    // Close when clicking outside
    $(document).on('click', function(event) {
        if ($(event.target).closest('.dropdown-select').length === 0) {
            $('.dropdown-select').removeClass('open');
            $('.dropdown-select .option').removeAttr('tabindex');
        }
        event.stopPropagation();
    });

    function filter() {
        var valThis = $('#txtSearchValue').val();
        $('.dropdown-select ul > li').each(function() {
            var text = $(this).text();
            (text.toLowerCase().indexOf(valThis.toLowerCase()) > -1) ? $(this).show(): $(this).hide();
        });
    };
    // Search

    // Option click
    $(document).on('click', '.dropdown-select .option', function(event) {
        $(this).closest('.list').find('.selected').removeClass('selected');
        $(this).addClass('selected');
        var text = $(this).data('display-text') || $(this).text();
        $(this).closest('.dropdown-select').find('.current').text(text);
        $(this).closest('.dropdown-select').prev('select').val($(this).data('value')).trigger('change');
    });

    // Keyboard events
    $(document).on('keydown', '.dropdown-select', function(event) {
        var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
        // Space or Enter
        //if (event.keyCode == 32 || event.keyCode == 13) {
        if (event.keyCode == 13) {
            if ($(this).hasClass('open')) {
                focused_option.trigger('click');
            } else {
                $(this).trigger('click');
            }
            return false;
            // Down
        } else if (event.keyCode == 40) {
            if (!$(this).hasClass('open')) {
                $(this).trigger('click');
            } else {
                focused_option.next().focus();
            }
            return false;
            // Up
        } else if (event.keyCode == 38) {
            if (!$(this).hasClass('open')) {
                $(this).trigger('click');
            } else {
                var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
                focused_option.prev().focus();
            }
            return false;
            // Esc
        } else if (event.keyCode == 27) {
            if ($(this).hasClass('open')) {
                $(this).trigger('click');
            }
            return false;
        }
    });

    $(document).ready(function() {
        create_custom_dropdowns();
    });

</script>
<?php

  $current_url =  current_url();
  $parameter_url = explode("products/0/", $current_url);// if action is load like enable/disable or delete

  if(isset($parameter_url[1])){
   $new_url_for_mode = 'admin/products/table/'.$parameter_url[1].''; //table value change when click on enable/disable or delete
  }else{
    $new_url_for_mode = 'admin/products/table/';
  }

?>
<script>
    //product--------------------
    var product_active_inactive_url = '<?php echo  base_url('admin/active_inactive_product/'); ?>';//enable/disable (offline/online)
    var product_delete_url = '<?php echo  base_url('admin/delete_product/'); ?>';
    var product_table_url = '<?php echo  base_url(''.$new_url_for_mode.''); ?>';

    //category-------------------
    var category_table_url = '<?php echo  base_url(''.$new_url_for_mode.''); ?>';
    var category_active_inactive_url = '<?php echo  base_url('admin/active_inactive_category/'); ?>';//enable/disable (offline/online)
    var category_delete_url = '<?php echo  base_url('admin/delete_category/'); ?>';

    var selected_category_id ="<?php echo $selected_category_id;?>";

   //category add-edit---------------
</script>
     <?php 

      //  if merchant is logged in, then this condition will check and only merchant restaurant prodcuts will show. if this blank that means super admin is logged in and then all resataurant will show
        if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){
       ?>
    <script>//if super admin is logged in 
       var selected_restaurant_id = "<?php echo $selected_restaurant_id;?>";//$selected_restaurant_id varible set from admin on load
    </script>
    <?php
      }else{
    ?>
    <script>// if merchant is logged in ----
       var selected_restaurant_id = "<?php echo $this->logged_in_restaurant_id;?>";
    </script>
     <?php
      }
    ?>



<!-- Croppi -->


<style>
    body .cropper-crop-box {
    min-width: 200px !important;
    max-width: 200px !important;
    min-height: 200px !important;
    max-height: 200px !important;
}

.simple-cropper-images{
  width: 820px;
  margin: 0 auto 20px;
  
}

.cropme{
  float: left;
  background-color: #f1f1f1;
  margin-bottom: 5px;
  margin-right: 5px;
  background-image: url('<?php echo base_url() ?>assets/images/UploadLight.png');
  background-position: center center;
  background-repeat: no-repeat;
  cursor: pointer;
}

.cropme:hover{
  background-image: url('<?php echo base_url() ?>assets/images/UploadDark.png');
}
#fileInput{
  width:0;
  height:0;
  overflow:hidden;
}

#modal{
  z-index: 9999;
  position: fixed;
  top: 0px;
  left: 0px;
  width: 100%;
  height: 100%;
  background-color: #5F5F5F;
  opacity: 0.95;
  display: none;
}

#preview{
  z-index: 9999;
  position: fixed;
  top: 0px;
  left: 0px;
  display: none;
  border: 4px solid #A5A2A2;
  border-radius: 4px;
  float: left;
  font-size: 0px;
  line-height: 0px;
}

#preview .buttons{
  width: 36px;
  position: absolute;
  bottom:0px;
  right: -44px;
}

#preview .buttons .ok{
  border: 4px solid #F5F5F5;
  border-radius: 4px;
  width: 28px;
  height: 28px;
  line-height: 0px;
  font-size: 0px;
  background-image: url('<?php echo base_url() ?>assets/images/Ok.png');
  background-repeat: no-repeat;
}
#preview .buttons .ok:hover{
  background-image: url('<?php echo base_url() ?>assets/images/OkGreen.png');
}

#preview .buttons .cancel{
  margin-bottom: 4px;
  border: 4px solid #F5F5F5;
  border-radius: 4px;
  width: 28px;
  height: 28px;
  line-height: 0px;
  font-size: 0px;
  background-image: url('<?php echo base_url() ?>assets/images/Cancel.png');
  background-repeat: no-repeat;
}

#preview .buttons .cancel:hover{
  background-image: url('<?php echo base_url() ?>assets/images/CancelRed.png');
}
</style>
<!-- Croppi -->

 



 
