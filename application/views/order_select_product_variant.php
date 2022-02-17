 
<?php
  //show products according to selected category -----------------START----------
 
//print_r($get_checked_variant_data);
$selectd_variant_type_array = array();
     if(!empty($get_checked_variant_data)){
        foreach ($get_checked_variant_data as  $value) {
          array_push($selectd_variant_type_array,$value['variant_type_id']);
        }
     } 

  $final_selectd_variant_type_array = array_values(array_unique($selectd_variant_type_array));

  $select_product_list = "";
  $product_variant_detail_tr = "";
  $selected_product_list_for_order = "";
  //echo '<pre>';
 // print_r($product_variant_data);
  if (isset($product_variant_data) && $product_variant_data != "" && !empty($product_variant_data)) {
      foreach ($product_variant_data as $value) {

             $variant_name = $value['variant_name'];
             $variant_id = $value['variant_id'];
             $product_id = $value['product_id'];
             $product_name = $value['product_name'];
             $product_price = $value['product_price'];
             $multi_select_limit = $value['multi_select_limit'];
             $is_mandatory = $value['is_mandatory'];



             $single_select = $value['single_select'];//1 - single select 2 multi select

            $remove_selection_btn =  '<button type="button" class="btn btn-danger remove_selection_from_radio" variant_id="'.$variant_id.'" product_id="'.$product_id.'" title="Remove Selection"><i class="fas fa-minus remove_selection_from_radio"></i></button>';

             //check variant select as single or multiple
              if($single_select == 1){
                  $variant_single_multi_type = 'radio'; //Single select then selection type will be work as radio button
                  $radio_view  = 'checkmark';
                  $remove_selection_from_radio = $remove_selection_btn ;
               }else if($single_select == 2){
                   $variant_single_multi_type = 'checkbox'; //multi select then selection type will be work as checkbox button
                    $remove_selection_from_radio = "";
                    $radio_view  = 'checkmark_check';
               }else{
                  $variant_single_multi_type = 'radio'; //Single select
                  $remove_selection_from_radio =  $remove_selection_btn ;
                   $radio_view  = 'checkmark';
               }
             

                $product_variant_detail_tr.= '<tr ><td colspan="3"><strong> <span id="variant_name_'.$variant_id.'">'. $variant_name.' </span> - '.  $remove_selection_from_radio.'</strong></td> </tr>';


                $count_multi_selected_checked = 0;
                $count_total_mendatory_variant = 0;
                foreach ($value['variant_type'] as $varaint_type_value) {
                        $variant_type_id = $varaint_type_value['variant_type_id'];
                         $variant_type_name = $varaint_type_value['variant_type_name'];
                         $variant_type_price = $varaint_type_value['variant_type_price'];
                      
                       if(in_array($variant_type_id, $final_selectd_variant_type_array)){
                          $checkbox_checked = 'checked="checked"';
                          $order_variant_type_status = 0;// selected for order and gona to unselect
                           $count_multi_selected_checked++;
                           

                           //if any one is selected then mendotory checked value will be 0 (ex -  variant is mendotory and any one option is selected then  mendotory falg value will be 0 other wise 1) for count and checked purpose only
                           $is_mandatory_flag = 0;//becouse its already check when order placed

                         
                       }else{
                           $checkbox_checked = "";
                            $order_variant_type_status = 1;// not selected for order but gona to select 

                             //if any one is selected then mendotory checked value will be 0 (ex -  variant is mendotory and any one option is selected then  mendotory falg value will be 0 other wise 1) for count and checked purpose only
                           $is_mandatory_flag = 1;// selection is mendotory
                           

                       }

                       if($is_mandatory > 0){
                         $count_total_mendatory_variant++;
                       }
                        $product_variant_detail_tr.= ' <tr>
                                                          <td colspan="4">
                                                              <h3>'.$variant_type_name.'</h3>
                                                           </td>
                                                           <td class="cart-price">S$'.$variant_type_price.'</td>
                                                           <td>
                                                               <label class="enabled-label">
                                                                <input type="'.$variant_single_multi_type.'" order_product_change_status= "'.$order_variant_type_status.'" class="order_product_change_status check_multi_limit_'.$variant_id.'" value="product_id_'.$product_id.',variant_type_id_'.$variant_type_id.',variant_id_'.$variant_id.'"  '.$checkbox_checked .' variant_type_price = "'.$variant_type_price.'" name="selection_type_'.$variant_id.'" check_variant_selection_if_single="'.$single_select.'"  select_limit="'.$multi_select_limit.'" variant_id_for_selection_count="'.$variant_id.'"  multi_checked_total="'.$count_multi_selected_checked.'"   is_mandatory="'.$is_mandatory.'" is_mandatory_flag="'.$is_mandatory_flag.'"   total_mendatory_variant="'.$count_total_mendatory_variant.'"/>
                                                                  <span class="'.$radio_view.'"></span>
                                                                </label>
                                                           </td>
                                                      </tr>';
                  }



           
      }
  }else{
    $select_product_list = 'No Product available';
    $selected_product_list_for_order  = "<tr><td colspan='3'>No Selected Products</td></tr>";
  }
    //show products according to selected category -----------------END----------
?>
 
<tbody  class="cart-item-details">
   <?php echo $product_variant_detail_tr;?>
</tbody> 