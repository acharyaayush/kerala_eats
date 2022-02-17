 <?php
   //print_r($product_variant_data_for_place_order);
    //print_r($this->session->userdata('selected_product_id_for_order'));
     $final_item_list_for_place_order = "";
      $varaint_type_html = "";
      
      $total_of_variant_price = 0;
     
      if(isset($product_variant_data_for_place_order) && $product_variant_data_for_place_order && !empty($product_variant_data_for_place_order)){
         foreach ($product_variant_data_for_place_order as $item_value) {
               $product_id = $item_value['product_id'];
               $product_name = trim($item_value['product_name']);
               $product_quantity = $item_value['product_quantity'];
               $product_unit_price = $item_value['product_unit_price'];
            
               if($item_value['product_image'] !="" ){
                  $product_image = base_url($item_value['product_image']);
               }else{
                  $product_image = base_url('assets/images/default_product_image.png');
               }
              

               $final_item_list_for_place_order.= '<tr>
                			                                 <td class="profile-img"><img src="'.$product_image.'" /></td>
                			                                 <td colspan="4">
                			                                    <h3>'.$product_name.'</h3>
                			                                    <p>S$<span class="product_price_'.$product_id.'" >'.$product_unit_price.'</span></p>';    

			 
               $variant_is_available_total =  $item_value['variant_is_available'];
              if($variant_is_available_total>0){
                     $final_item_list_for_place_order.= ' <button type="button" class="btn btn-primary select_variant_for_order" selected_product_id="'.$product_id.'"  data-toggle="modal" data-target="#select_variant_modal" type="button" data-backdrop="static" data-keyboard="false">Add Variant</button><br>';
                }
             
               if(!empty($item_value['variants'])){// if any varaint available  in order
                   $counter = 0;
                  foreach ($item_value['variants'] as $varaint_value) {
                        $variant_name = $varaint_value['variant_name'];
                        $variant_type_name = $varaint_value['variant_type_name'];
                        $variant_type_id = $varaint_value['variant_type_id'];
                        $variant_type_price = $varaint_value['variant_price'];
                        $single_select = $varaint_value['single_select'];//1 - single select 2 multi select
                        $variant_id = $varaint_value['variant_id'];
                      
                        //$variant_name_html = ' <p>'.$variant_name.' <button type="button" class="btn btn-primary select_variant_for_order" selected_product_id="'.$product_id.'"  data-toggle="modal" data-target="#select_variant_modal" type="button" data-backdrop="static" data-keyboard="false">Add</button> <br> ';

                        $final_item_list_for_place_order.= '<strong>- '. $variant_type_name.'</strong><br>S$<span class="variant_type_price_'.$product_id.'"" variant_type_id="'.$variant_type_id.'" variant_id="'.$variant_id.'" check_variant_selection_if_single="'.$single_select.'">'.$variant_type_price.'</span> </p>';//variant_type_id_'.$variant_type_id.'

                        $total_of_variant_price = $total_of_variant_price+ $variant_type_price;

                  }
               }else{
                    $total_of_variant_price = 0;
                    $single_select  = 1;   
               }

             $product_unit_price = $item_value['product_unit_price'];

	            if($product_quantity != ""){
	            	$total = ($product_unit_price+$total_of_variant_price)*$product_quantity;
	            	$product_quantity  = $product_quantity;
	            }else{
	            	$total = ($product_unit_price+$total_of_variant_price)*1;
	            	$product_quantity = 1;
	            }
             

             $final_item_list_for_place_order.='</td>
					                                 <td>
					                                    <div class="quantity buttons_added">
					                                       <input type="button" value="-" class="minus minus_qunatity" product_id="'.$product_id.'"/>
					                                       <input type="number" value="'.$product_quantity.'" step="1" min="1" max="" name="quantity" title="Qty" class="input-text qty text  quantity_'.$product_id.'" size="4" pattern="" inputmode="" id="qty_val_liquid_liquid" />
					                                       <input type="button" value="+" class="plus plus_qunatity" product_id="'.$product_id.'"/>
					                                    </div>
					                                 </td>
					                                 <td class="cart-price">S$ <span id="plus_minus_total_'.$product_id.'" product_id="'.$product_id.'" class="total_amount_of_items" product_unit_price="'.$product_unit_price.'" variant_is_available_total = '.$variant_is_available_total.' >'. $total.'</span></td>
					                                 <td class="remove_item" ><i class="fas fa-trash-alt order_product_change_status" order_product_change_status="2" style="cursor: pointer;" product_id="'.$product_id.'"></i></td>
					                            </tr>'; 
            }
      }
?>

 <tbody  class="cart-item-details">
     <?php echo $final_item_list_for_place_order;?>
 </tbody>