<?php

if (isset($category_detail) && $category_detail != "" && !empty($category_detail)) {
    //print_r($category_detail);
    $category_detail_tr = "";
    foreach ($category_detail as $value) {
        $category_id = $value['category_id'];

        if($value['category_image'] !=""){
             $category_image = base_url().$value['category_image'];
        }else{
             $category_image = base_url().'assets/images/default_product_image.png';
        }
       
        $category_name = $value['category_name'];
        $category_status = $value['category_status'];//1 - Enable 2 - Disable 3 - Deleted 

        $offline_status = $value['offline_status'];//1 - offline value exit in product offline table, 0 - not exist
        $selected_offline_tag = $value['selected_offline_tag'];// if offline value exist


        /* if($category_status == 1){
            $category_enable = 'checked=""';
            $data_target = 'data-target="#offline_online_category_or_product_popup"';
            $disable_class = "";
         }else if($category_status == 2){
            $category_enable = '';
            $data_target = '';
            $disable_class = "switch_on_off_ajax_response";
         }else{
            $category_enable = '';
            $data_target = '';
            $disable_class = "switch_on_off_ajax_response";
         }*/

        #we dont need to select hours becouse it will work for today date so  for update case
          #Ex. - if day and multiday selected for future date not for today then it can be update becouse today it will be show enable till selected date so when user click on toggle button then popupwill show and selected date will be show if offline value  exist in table

         if($offline_status == 0){
            $category_enable = 'checked=""';
            $data_target = 'data-target="#offline_online_category_or_product_popup"';
            $disable_class = "";

            $status_mode_type = 1;
          }else if($offline_status == 1){
            $category_enable = '';
            $data_target = '';
            $disable_class = "switch_on_off_ajax_response";
             $status_mode_type = 2;
         }else{
            $category_enable = '';
            $data_target = '';
            $disable_class = "switch_on_off_ajax_response";
             $status_mode_type = 2;
         }
        
        $category_detail_tr .= ' <tr id = "'.$category_id.'">
                                    <td class="profile-img category_list_by_tr"  category_id_tr="'.$category_id.'" style="cursor:pointer;"><img src="'. $category_image.'" /></td>
                                    <td id="category_name_'.$category_id.'" class="category_list_by_tr"  category_id_tr="'.$category_id.'" style="cursor:pointer;">'.$category_name.'</td>
                                    <td><label class="switch_on_off_ajax">
                                            <input type="checkbox" '.$category_enable.' id="category_status_'.$category_id.'" class="category_status" '.$data_target.'status_mode_type="'.$status_mode_type.'"  data-toggle="modal"  data-backdrop="static" data-keyboard="false" selected_offline_tag="'.$selected_offline_tag.'">
                                            <span class="slider_toggle_after_ajax '.$disable_class.' round"></span>
                                        </label>

                                    </td>
                                    <td class="text-center">
                                        <div class="action_icons_dropdown">
                                            <i class="fa fa-ellipsis-v"></i>
                                            <div class="dropdown-content">
                                                <a href="" class="cat_mode_type_btn" mode_type_modal="2" data-target="#add_edit_category_popup" data-toggle="modal" id="edit_category_'.$category_id.'" data-backdrop="static" data-keyboard="false">Edit</a>
                                                <!--a class="" href="" data-toggle="tooltip" title="Show Details"> <i class="fa fa-eye"></i> </a-->
                                                <a class="category_delete"  href="javascript:void(0)"  id="category_delete_'.$category_id.'" data-toggle="tooltip" title="Delete">
                                                     Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>';
    }
}else{
    $category_detail_tr = "<tr><td colspan='3' class='no-records'>No Records Found </td></tr>";
}
?>

<thead>
    <tr>
        <th colspan="2">Category (<?php echo $total_records_of_category;?>)</th>
        <th class="text-center add_product" colspan="2"><button class="btn btn-primary  mr-2 cat_mode_type_btn" mode_type_modal="1" data-target="#add_edit_category_popup" data-toggle='modal' data-backdrop="static" data-keyboard="false" style="cursor: pointer;">Add</button></th>
    </tr>
</thead>
<tbody class="row_position">
    <?php echo $category_detail_tr;?>
</tbody>
