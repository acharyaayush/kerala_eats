<?php
 //print_r($variant_type_detail);
$variant_type_detail_tr = "";
if (isset($variant_type_detail) && $variant_type_detail != "" && !empty($variant_type_detail)) {
    foreach ($variant_type_detail as $value) {

        $variant_id = $value['variant_id'];
        $variant_type_id = $value['variant_type_id'];
        $variant_type_name = $value['variant_type_name'];
        $restaurant_id = $value['restaurant_id'];
 
        $variant_type_status = $value['variant_type_status'];//1 - Enable 2 - Disable 3 - Deleted 

         
        
         // enable/disable check status
         if($variant_type_status == 1){
            $variant_type_enable = 'checked=""';
            $variant_type_value = 2;//For disable 

         }else if($variant_type_status == 2){
            $variant_type_enable = '';
            $variant_type_value = 1;//For Enable 
         }
      
 
         $variant_type_detail_tr .= ' <tr>
                                        <td colspan="5" class="variant_type_name" id="variant_type_name_'.$variant_type_id.'"><span id="variant_type_span">'.$variant_type_name.'</span><span class="d-none" id="variant_type_edit_input"><input value="'.$variant_type_name.'" id="edit_vairant_type_name_'.$variant_type_id.'"> <button type="button" class="btn btn-primary" id="edit_variant_type_save" edit_variant_type_id="'.$variant_type_id.'"; variant_id="'.$variant_id.'">Save</button> <button type="button" class="btn btn-danger edit_variant_type_close" id="edit_variant_type_close'.$variant_type_id.'" edit_variant_type_id="'.$variant_type_id.'"><i class="fas fa-times" aria-hidden="true"></i></button></span>&nbsp;<span class="error" id="unfill_edit_variant_type_name"></span></td>
                                        '; 
 
                                    
 
        $variant_type_detail_tr .= '
                                <td colspan="1" class="text-right">
                                      <button type="button" class="btn edit_variant_type_name_btn" id="edit_variant_type_'.$variant_type_id.'" edit_variant_type_id="'.$variant_type_id.'"><i class="fas fa-pencil-alt"></i></button>
                                </td>
                            </tr>';
    }
}else{
    $variant_type_detail_tr .=  '<tr>
                              <td colspan="10" class="no-records">No Records Found 
                              <br>';
 

     $variant_type_detail_tr .=   ' </td>
                          </tr>';
}
?>
 
<?php echo $variant_type_detail_tr;?>
 
