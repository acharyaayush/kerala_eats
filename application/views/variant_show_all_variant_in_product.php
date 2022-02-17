<?php
 $product_variant_list = "";
  if (isset($product_variant_detail) && $product_variant_detail != "" && !empty($product_variant_detail)) {
      
      foreach ($product_variant_detail as $value) {

          $variant_id = $value['variant_id'];
          $variant_name = $value['variant_name'];
          $variant_type_array = $value['variant_type'];
          $select_type = $value['single_select'];//1 - single select 2 multi select
          $multi_select_limit = $value['multi_select_limit'];

          if($multi_select_limit>0){
               $multi_select_limit_value =  $multi_select_limit;
          }else{
            $multi_select_limit_value = 'No limit set';
          }

          if( $select_type == 1){
               $select_type_value = 'Single';
          }else{
               $select_type_value = 'Multi';
          }
          

          $counter = 1;
          foreach ($variant_type_array as $type_value) {
              $variant_type_name = $type_value['variant_type_name'];
              $variant_type_price = $type_value['variant_type_price'];
              $default_variant_status = $type_value['default_variant_status'];
              $is_mandatory = $type_value['is_mandatory'];

              if($default_variant_status == 1){
                 $default_variant_status_value = 'Yes'; 
              }else{
                 $default_variant_status_value = 'No'; 
              }

              if($is_mandatory == 1){
                 $is_mandatory_value = 'Yes'; 
              }else{
                 $is_mandatory_value = 'No'; 
              }

                $product_variant_list .= ' <tr>';
                      if($counter == 1){
                        $product_variant_list .= '<td rowspan="'.count($variant_type_array).'"><h6>'.$variant_name.'</h6></td>';

                      }
                $product_variant_list .= ' <td><p>'.$variant_type_name.'</p></td>
                                           <td>S$ '.$variant_type_price .'</td> 
                                           <td>'.$default_variant_status_value.'</td> 
                                           <td>'.$is_mandatory_value.'</td>   ';
            if($counter == 1){
        
                $product_variant_list .=' <td rowspan="'.count($variant_type_array).'">'.$select_type_value.'</td>
                                          <td rowspan="'.count($variant_type_array).'">'.$multi_select_limit_value.'</td>
                                          <td rowspan="'.count($variant_type_array).'">
                                             <a href="" data-target="#add_edit_variant_in_product" data-toggle="modal" class="close_view_variant_modal" variant_id="'.$variant_id.'" product_id="'.$selected_product_id.'">Edit </a>&nbsp;&nbsp;
                                             <a style="cursor:pointer" class="delete_selected_product_variant text-danger" id="" data-id="" data-toggle="tooltip" title="" data-original-title="Delete" variant_id="'.$variant_id.'" product_id="'.$selected_product_id.'"> 
                                             Delete
                                             </a>
                                          </td>';
                 }

             $product_variant_list .=  '</tr> ';
            $counter++;
          }

      }
  }else{
      $product_variant_list =  "<tr><td colspan='6' class='no-records'>No Records Found </td></tr> ";
  }
 
?>
<table class="table table-bordered">
                      
      <tbody class="cart-item-details">
            <tr>
              <th>Variant</th>
              <th>Variant Type</th>
              <th>Price</th>
              <th>Default</th>
               <th>Is Mandatory</th>
              <th>Select Type</th>
              <th>Select Limit</th>
             
              <th>Action</th>
            </tr>
           <?php   echo $product_variant_list;?>
</table>
 
 