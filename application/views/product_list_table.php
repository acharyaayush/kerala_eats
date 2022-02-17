<?php

if (isset($product_detail) && $product_detail != "" && !empty($product_detail)) {
    //print_r($product_detail);
    $product_detail_tr = "";
    foreach ($product_detail as $value) {
        $product_id = $value['product_id'];
        $product_name = $value['product_name'];
        $product_short_desc =  strip_tags($value['short_desc']);
        $price = $value['price'];
        $product_image = $value['product_image'];
        $product_status = $value['product_status'];//1 - Enable 2 - Disable 3 - Deleted 
        $category_status = $value['category_status'];//1 - Enable 2 - Disable 3 - Deleted 

        $offline_status = $value['offline_status'];//1 - offline value exit in product offline table, 0 - not exist
        $selected_offline_tag = $value['selected_offline_tag'];// if offline value exist

         // For logo image
         if($product_image != "" && empty($header['user_data'])){
            $product_image = base_url().$product_image;
 
         }else{
            $product_image =  base_url('assets/images/default_product_image.png');
         }
         
         /*if($product_status == 1){
            $product_enable = 'checked=""';
            $data_target = 'data-target="#offline_online_category_or_product_popup"';
            $disable_class = "";

         }else if($product_status == 2){
            $product_enable = '';
            $data_target =  '';
            $disable_class = "switch_on_off_ajax_response";
         }else{
            $product_enable = '';
            $data_target =  '';
            $disable_class = '';
         }*/

          #we dont need to select hours becouse it will work for today date so  for update case
          #Ex. - if day and multiday selected for future date not for today then it can be update becouse today it will be show enable till selected date so when user click on toggle button then popupwill show and selected date will be show if offline value  exist in table

         if($offline_status == 0){
            $product_enable = 'checked=""';
            $data_target = 'data-target="#offline_online_category_or_product_popup"';
            $disable_class = "";

            $status_mode_type = 1;

         }else if($offline_status == 1){
            $product_enable = '';
            $data_target =  '';
            $disable_class = "switch_on_off_ajax_response";

            $status_mode_type = 2;
         }else{
            $product_enable = '';
            $data_target =  '';
            $disable_class = '';

            $status_mode_type = 2;
         }


         $cat_offline_status = $value['cat_offline_status'];//1 - offline value exit in product offline table, 0 - not exist
        $cat_selected_offline_tag = $value['cat_selected_offline_tag'];// if offline value exist

        if(($cat_offline_status ==  1 || $cat_offline_status ==  2 || $cat_offline_status ==  3) && $cat_selected_offline_tag == 1){
        // if category is offline then products can't be enable
            $product_disable = "disabled";
         }else{
            $product_disable = "";
        }

        $product_detail_tr .= '<tr class="product_section" id="'.$product_id.'" style="cursor:pointer;">
                                <td class="profile-img category_img"><img src="'.$product_image.'"></td>
                                <td>
                                    <h3 id="product_name_'.$product_id.'">'.$product_name.'</h3>
                                     <p>'. $product_short_desc.'</p>
                                </td>
                                <td>SGD '.$price.'</td>
                                <td><label class="switch_on_off_ajax if_category_is_disable">
                                        <input type="checkbox" '.$product_enable.' id="product_status_'.$product_id.'" class="product_status"  status_mode_type="'.$status_mode_type.'" '.$data_target.' data-toggle="modal"  data-backdrop="static" data-keyboard="false"" '.$product_disable.' selected_offline_tag="'.$selected_offline_tag.'">
                                        <span class="slider_toggle_after_ajax round '.$disable_class.'"></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="action_icons_dropdown">
                                        <i class="fa fa-ellipsis-v"></i>
                                        <div class="dropdown-content">
                                            <a href="" class="product_mode_type_btn" mode_type_modal="2" data-target="#add_edit_product_popup" data-toggle="modal" id="edit_product_'.$product_id.'" data-toggle="tooltip" title="Edit">Edit</a>
                                            <!--<a class="" href="" data-toggle="tooltip" title="Show Details"> <i class="fa fa-eye"></i> </a>-->
                                            <a  href="javascript:void(0)"   data-id="" data-toggle="tooltip" title="Delete" class="product_delete" id="product_delete_'.$product_id.'" >
                                              Delete</i>
                                            </a>
                                            <a href="" class="add_edit_variant_type" mode_type_modal="2" data-target="#add_edit_variant_in_product" data-toggle="modal" edit_product_id="'.$product_id.'" data-toggle="tooltip" title="Add Variants">Variants/ Add-ons</a>
                                             
                                        </div>
                                    </div>
                                </td>
                            </tr>';
    } 
}else{
    $product_detail_tr = " <tr><td colspan='10' class='no-records'>No Records Found </td></tr>";
}
?>
<?php
    // check if category is offline then user cant add product in that category , so we are disable Add button product and show message by alert

    // use at the time of category is enable
    $add_product_btn = '<button class="btn btn-primary mr-2 product_mode_type_btn"  mode_type_modal="1" data-target="#add_edit_product_popup" data-toggle="modal" data-backdrop="static" data-keyboard="false" style="cursor: pointer;">Add Product</button>';

    // use at the time of category is disable
    $add_product_btn_disable = '<button class="btn btn-primary mr-2" id="disable_add_product_btn">Add Product</button>';    

    if(isset($selected_category_id_status)){

      if($selected_category_id_status == 1){
        $category_offline_disable_add_product =  $add_product_btn;
      }else{
        $category_offline_disable_add_product =  $add_product_btn_disable;
      }
    }else{
       $category_offline_disable_add_product =  $add_product_btn_disable;
    }
 ?>
<thead>
    <tr>
        <th colspan="2">Product (<?php echo $total_records_of_products;?>)</th>
        <th colspan="3" class="text-right add_product">
            <!-- <i class="fas fa-search"></i> -->
            <div class="d-flex">
                 <?php echo  $category_offline_disable_add_product;?>
                <button class="btn btn-secondary"  data-target="#import_export_popup" data-toggle="modal" data-backdrop="static" data-keyboard="false" >Import/Export</button>
            </div>
        </th>
    </tr>
</thead>
<tbody class="product_row_position">
    <?php echo $product_detail_tr;?>
</tbody>

