<?php

  if (isset($variant_type_detail) && $variant_type_detail != "" && !empty($variant_type_detail)) {
     
      $select_variant_type_list = "";
    
      $select_variant_counter = 1;
      foreach ($variant_type_detail as $value) {

           $variant_type_id = $value['variant_type_id'];
          $variant_type_name = $value['variant_type_name'];

          //only this check for edit time 
          if(isset($selected_variant_type_id_array)){
            if (in_array($variant_type_id, $selected_variant_type_id_array))
              {
                 $checked = 'checked="checked"';
              }
            else
              {
                $checked = '';
              }
            
          }else{
               $checked = '';
          }
        
          $select_variant_type_list.= '<label class="enabled-label">'.$variant_type_name.'
                                            <input type="checkbox" id="variant_type_id_'.$variant_type_id.'" class="selected_variant_type_id"  name="variant_type_id" value="'.$variant_type_id.'" variant_type_name="'.$variant_type_name.'" '.$checked .'>
                                               <span class="checkmark_check"></span>
                                         </label>';
      }
  }else{
      $select_variant_type_list =  "No Records Found";
  }
?>
 
<label>Select Variant Type</label>
    <div id="for_select_variant" ><!--For variant-->
     <div id="select_variant">
         <div class="select_dropdown">
           <input type="text" onclick="SearchDropdownFunction('SelectvarianttypeDropdown')"  placeholder="Search variant Type..." id="SelectvariantTypeInput" onkeyup="filterFunction('SelectvarianttypeDropdown','SelectvariantTypeInput')">
           <div id="SelectvarianttypeDropdown" class="select_dropdown-content show">
               <?php echo  $select_variant_type_list;?>
           </div>
         </div>
    </div>
  &nbsp;<span class="error" id="unselect_variant"></span>
</div>
 
 