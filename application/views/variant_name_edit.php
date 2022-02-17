<?php
 //print_r($variant_detail);
if (isset($variant_detail) && $variant_detail != "" && !empty($variant_detail)) {
   
    $variant_detail_tr = "";
    foreach ($variant_detail as $value) {

        $variant_id = $value['variant_id'];
        $variant_name = $value['variant_name'];
        $restaurant_id = $value['restaurant_id'];
 
        $variant_status = $value['variant_status'];//1 - Enable 2 - Disable 3 - Deleted 

         
        
         // enable/disable check status
         if($variant_status == 1){
            $variant_enable = 'checked=""';
            $variant_value = 2;//For disable 

         }else if($variant_status == 2){
            $variant_enable = '';
            $variant_value = 1;//For Enable 
         }
      
        $variant_detail_tr .= ' <tr>
                                    <td colspan="5" class="variant_name" id="variant_name_'.$variant_id.'"><span id="variant_span">'.$variant_name.'</span><span class="d-none" id="variant_edit_input"><input value="'.$variant_name.'" id="edit_vairant_name_'.$variant_id.'"> &nbsp;<span class="error" id="unfill_edit_variant_name"></span><button type="button" class="btn btn-primary" id="edit_vairant" edit_variant_id="'.$variant_id.'">Save</button> <button type="button" class="btn btn-danger edit_variant_close" id="edit_variant_close'.$variant_id.'" edit_variant_id="'.$variant_id.'"><i class="fas fa-times" aria-hidden="true"></i></button></span></td>
                                    '; 
                                    
 
        $variant_detail_tr .= '
                                <td colspan="1" class="text-right">
                                      <button type="button" class="btn edit_variant" id="edit_variant_'.$variant_id.'" edit_variant_id="'.$variant_id.'"><i class="fas fa-pencil-alt"></i></button>
                                </td>
                            </tr>';
    }
}else{
    $variant_detail_tr .=  '<tr>
                              <td colspan="10" class="no-records">No Records Found 
                              <br>';
 

     $variant_detail_tr .=   ' </td>
                          </tr>';
}
?>
 
<?php echo $variant_detail_tr;?>
 
