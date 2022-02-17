<?php
$select_variant_name = '';//only for edit time
  if (isset($variant_detail) && $variant_detail != "" && !empty($variant_detail)) {
     
      $select_variant_list = "";
    
      $select_variant_counter = 1;
      foreach ($variant_detail as $value) {

           $variant_id = $value['variant_id'];
          $variant_name = $value['variant_name'];

          //only this check for edit time 
          if(isset($selected_variant_id_for_edit) && ($variant_id == $selected_variant_id_for_edit)){
               $checked = 'checked="checked"';
               $select_variant_name = $variant_name;
          }else{
               $checked = '';
          }
        
          $select_variant_list.= '<label class="enabled-label">'.$variant_name.'
                                      <input type="radio" class="selected_variant_id" id="variant_id_'.$variant_id.'" name="variant_id" value="'.$variant_id.'" '.$checked .'>
                                         <span class="checkmark_check" ></span>
                                   </label>';
      }
  }else{
      $select_variant_list =  "No Records Found ";
  }
?>
 
<label>Select Variant</label>
    <div id="for_select_variant_type" ><!--For variant-->
     <div id="select_variant_type">
         <div class="select_dropdown">
           <input type="text" onclick="SearchDropdownFunction('SelectvariantDropdown')"  placeholder="Search variant..." id="SelectvariantInput" onkeyup="filterFunction('SelectvariantDropdown','SelectvariantInput')" value="<?php echo  $select_variant_name;?>">
           <div id="SelectvariantDropdown" class="select_dropdown-content show">
               <?php echo  $select_variant_list;?>
           </div>
         </div>
    </div>
  &nbsp;<span class="error" id="unselect_variant"></span>
</div>
 
 