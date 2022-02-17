 <?php

   //print_r($promo_code_list);
   if (isset($promo_code_list) && !empty($promo_code_list)) {//parent IF--START--
     $promo_code_list_td = "";      
     foreach ($promo_code_list as $value) {//Foreach --START--

         $promo_code_id = $value['id'];
         $promo_code_promo_type = $value['promo_type'];
         $promo_code_code_name  = $value['code_name'];
         $promo_code_code_name  = stripslashes($promo_code_code_name);
         $promo_code_discount_value  = $value['discount_value'];
         $promo_code_desciption  = $value['desciption'];
         $promo_code_valid_from  = $value['valid_from'];
         $promo_code_valid_till  = $value['valid_till'];
         $promo_code_min_value  = $value['min_value'];
         $promo_code_max_discount = $value['max_discount'];
         $promo_code_promo_status  = $value['promo_status'];
         $promo_code_level_id  = $value['level_id'];
         $promo_code_promo_level_type = $value['type'];
         $promo_code_is_auto_apply  = $value['is_auto_apply'];
         $promo_code_promo_used_times  = $value['promo_used_times'];
         $promo_code_added_by  = $value['added_by'];// first who is added supper admin or merchant

       // checking who added firt time
         $added_by = "";
        if($this->logged_in_restaurant_id && $this->logged_in_restaurant_id != "" && $this->role == 2){
              if($promo_code_added_by  == 2){
                  $added_by = 'You';
               } if($promo_code_added_by  == 1){
                   $added_by = 'Master Admin';
               }
         }else if($promo_code_added_by  == 1){
            $added_by = 'You';
         } else if($promo_code_added_by  == 2){
             $added_by = 'Merchant';
         }

         //For Promo Code = 1 - Flat 2 - Percent  
         switch ($promo_code_promo_type ) {
               case 1:
                $promo_type = 'Flat';
                break;

               case 2:
                $promo_type = 'Percent';
                break;

              default:
                $promo_type = '';
            }

      //Epcho time convert --------
         #Promo Code valid Form---------START-------
        if($promo_code_valid_from != 0 || $promo_code_valid_from == ""){
            $from_date = new DateTime("@$promo_code_valid_from");  // convert UNIX timestamp to PHP DateTime
            $code_from_date = $from_date->format('Y-m-d');// H:i:s A
        }else{//for forever
             $code_from_date  = "Forever";
        }
        
         #Promo Code valid Form---------END-------

         #Promo Code valid Till---------START-------
         if($promo_code_valid_till != 0 || $promo_code_valid_till == ""){
             $till_date = new DateTime("@$promo_code_valid_till");  // convert UNIX timestamp to PHP DateTime
            $code_till_date =  $till_date->format('Y-m-d');//H:i:s A
         }else{//for forever
              $code_till_date  = "Forever";
         }
        
         #Promo Code valid Till---------END-------

      
      //Promo code enable  disable #1 = Enable , 2 = Disable , 3 = Deleted
         if($promo_code_promo_status == 1 ){
            $promo_code_enable = 'checked=""';//Enable
            
         }else{
            $promo_code_enable = '';//Disable
         }


      // Promo code auto apply or not.# 1 - Auto apply 2 - Not auto apply
         if($promo_code_is_auto_apply == 1 ){
            $auto_apply = 'Auto Apply';
            
         }else{
            $auto_apply = 'Not auto apply';
         }

         $promo_code_list_td.=  '<tr>
                                    <td>
                                      '.$start.'
                                    </td>
                                    <td id="promo_code_name_'.$promo_code_id.'">'.$promo_code_code_name.'</td>
                                    <td>'.$promo_type.'</td>
                                    <td>'.$promo_code_discount_value.'</td>
                                    <td>'.$promo_code_max_discount.'</td>
                                    <td>'.$promo_code_min_value.'</td>
                                    <td>'.$code_from_date.'</td>
                                    <td>'.$code_till_date.'</td>
                                    <td>
                                       <div class="iffyTip hideText2">'.$promo_code_desciption.'</div>
                                    </td>
                                    <td>'.$auto_apply.'</td>
                                    <td>'.$promo_code_promo_level_type.'</td>
                                    <td>'.$promo_code_promo_used_times.'</td>
                                     <td>'.$added_by.'</td>
                                    <td><label class="switch promocode-status">
                                       <input type="checkbox" '.$promo_code_enable.'  class="promo_code_status" id="promo_code_status_'.$promo_code_id.'" promo_code_status="'.$promo_code_code_name.'">
                                       <span class="slider round"></span>
                                       </label>
                                    </td>
                                    <td>
                                       <a href="'.base_url().'admin/add_edit_promotion/2/'.$promo_code_code_name.'" class="" >Edit</a>
                                       <a style="cursor:pointer" class="promo_code_delete" id="promo_code_delete_'.$promo_code_code_name.'" promo_code_status="'.$promo_code_code_name.'" data-id="" data-toggle="tooltip" title="Delete">
                                       Delete
                                       </a>
                                    </td>
                                 </tr>';
                                 $start++;
     }//Foreach --END--
            
   }else{
     $promo_code_list_td = "<tr><td colspan='15' class='no-records'>No Records Found </td></tr>";
   }//parent IF--END--
?>


 <thead>
  <tr>
    <th>S.No</th>
    <th>Code</th>
    <th>Type</th>
    <th>Value</th>
    <th>Maximum Discount</th>
    <th>Minimum Order Amount</th>
    <th>Start Date</th>
    <th>End Date</th>
    <th>Description</th>
    <th>Promo Application Mode</th>
    <th>Promo Applied On</th>
    <th>Promo Used (No. Of Times)</th>
     <th>First Time Added By</th>
    <th>Status</th>
   
    <th>Action</th>
  </tr>
</thead>
<tbody>
  <?php echo $promo_code_list_td;?>
</tbody>