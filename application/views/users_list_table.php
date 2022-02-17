<?php

$user_list_td = "";
if (isset($user_list) && !empty($user_list)) {

    foreach ($user_list as $value) {
        
        $user_role = $value['role'];
        $user_fullname = $value['fullname'];
       
        $user_email = $value['email'];
        $user_contact_no = $value['mobile'];
        $user_profile_pic = $value['profile_pic'];
        $user_device_type = $value['device_type'];
        $user_status = $value['status'];
        $user_registerd_date = $value['created_at'];
        $rest_name = stripslashes($value['rest_name']);
        $rest_id = $value['rest_id'];
        $user_registerd_date = $value['created_at'];
        //$res_admin_id = $value['admin_id'];

         if($selected_user_role == 2){
              $user_id = $value['id'];
              $edit_btn  = ' <a href="'.base_url().'admin/add_edit_merchant/2/'.$user_id.'/'.$user_role.'/" title="Edit" class="">Edit</a>';
          }


          if($selected_user_role == 3){
              $user_id = $value['user_id'];
            //  $user_wallet_balance = $value['credited'];
               $user_number_id = $value['number_id'];

              $edit_btn = '<a  href="'.base_url().'admin/edit_customer/'.$user_id.'/'.$user_role.'/" title="Edit" class="">Edit</a>';
          }


       // 0 - Pending 1 - Approved by admin 2 - Rejected by admin 3 - Inactive 4 - OTP verified and approval is pending 5 - Deleted
      switch ($user_status) {
        case 0:
          
           // $status = "Pending";
           $pending_status_checked = 'selected';
           $approved_status_checked = '';
           $rejected_status_checked = '';
           $inactive_status_checked = '';

          break;
        case 1:
          
           // $status = "Approved";
           $pending_status_checked = '';
           $approved_status_checked = 'selected';
           $rejected_status_checked = '';
           $inactive_status_checked = '';
          break;
        case 2:
          
           // $status = "Rejected";
           $pending_status_checked = '';
           $approved_status_checked = '';
           $rejected_status_checked = 'selected';
           $inactive_status_checked = '';
          break;
        case 3:
          
          // $status = "Inactive";
          $pending_status_checked = '';
          $approved_status_checked = '';
          $rejected_status_checked = '';
          $inactive_status_checked = 'selected';
          break;
        case 4:
          
          // $status = 'Verified by OTP but Approval is Pending';
          $pending_status_checked = 'selected';
           $approved_status_checked = '';
           $rejected_status_checked = '';
           $inactive_status_checked = '';

          break;

        default:
           // $status = "Pending";
            $pending_status_checked = 'selected';
           $approved_status_checked = '';
           $rejected_status_checked = '';
           $inactive_status_checked = '';

      }


      // 1 - Web 2 - Android 3 - iOS | Update on every Login
      switch ($user_device_type) {
        case 1:
          
           // $user_device_type = "Web";
           $user_device_type = "Android";

          break;
        case 2:
          
           // $user_device_type = "Android";
           $user_device_type = "iOS";
          break;
        case 3:
          
           // $user_device_type = "iOS";
           $user_device_type = "Web";
          break;

        default:
           $user_device_type = "Null";
      }

        //Epcho time convert --------
         $registerd_date = new DateTime("@$user_registerd_date");  // convert UNIX timestamp to PHP DateTime
         //$created_date = $registerd_date->format('Y-m-d H:i:s A');
         $created_date = $registerd_date->format('Y-m-d ');

         if($user_profile_pic !=""){
            $profile_img =  base_url().$user_profile_pic;
         }else{
               if($selected_user_role == 3){
                 $profile_img =  base_url().'assets/img/avatar/avatar-1.png';
                }else if($selected_user_role == 2){
                  $profile_img =  base_url().'assets/images/mr_merchant_pic.png';
               }
          }
        

        $user_list_td.='<tr>
                          <td>'.$start.'</td>';

                           if($selected_user_role == 3){

        $user_list_td.= '<td> <a class="" href="'.base_url().'admin/user_details/'.$selected_user_role.'/'.$user_id.'" data-toggle="tooltip" title="Show Details">'.$user_number_id.' </a></td>';
                      
                          }
                         
        $user_list_td.=   '<td class="profile-img"><img src="'.$profile_img.'"/></td>
                          <td id="user_name_'.$user_id.'"><a class="" href="'.base_url().'admin/user_details/'.$selected_user_role.'/'.$user_id.'" data-toggle="tooltip" title="Show Details">'.$user_fullname.'</a></td>
                          <td>'.$user_email.'</td>
                          <td>+65'.$user_contact_no.'</td>';

          if($selected_user_role == 2){
              if($rest_name  == ""){
                $rest_name_html = "No Restaurant";
              }else{
                $rest_name_html = '<a  href="'.base_url().'admin/add_edit_restaurant/2/'.$rest_id.'/'.$user_id.'/">'. $rest_name .'</a>';
              }
                $user_list_td.=  '<td>'.$rest_name_html.'</td>';
           }               

         $user_list_td.=  '<td>
                               <select class="custom-select  wv_filter_box_height form-control user_status" name="user_status_'.$user_id.'">
                                   <option id="user_status_0" value="0" '.$pending_status_checked.'>Pending</option>
                                   <option id="user_status_1" value="1" '.$approved_status_checked.'>Approved</option>
                                   <option id="user_status_2" value="2" '.$rejected_status_checked.'>Rejected</option>
                                   <option id="user_status_3" value="3" '.$inactive_status_checked.'>Inactive</option>
                                </select>
                         </td>';

                   

                      if($selected_user_role == 3){
        $user_list_td.= '<td>'.$user_device_type.' </td>
                          <td><button type="button" class="btn btn-primary customer_id_for_add_less_money" data-toggle="modal" data-target="#add_money_modal" user_id="'.$user_id.'" user_number_id="'.$user_number_id.'">Add Money</button></td>
                           <td><button type="button" class="btn btn-primary customer_id_for_add_less_money" data-toggle="modal" data-target="#deduct_money_modal" user_id="'.$user_id.'" user_number_id="'.$user_number_id.'">Deduct Money</button></td>
                           <td><a class="" href="'.base_url().'admin/wallet_history/'.$user_id.'" data-toggle="tooltip" title="Show Wallet History">Wallet History</a></td>
                        ';
                      }
                         

        $user_list_td.=' <td>'.$created_date.'</td>
                          <td>
                             <div class="action_icons_dropdown">
                                <i class="fa fa-ellipsis-h"></i>
                                <div class="dropdown-content">
                                   '. $edit_btn.'
                                   <a class="" href="'.base_url().'admin/user_details/'.$selected_user_role.'/'.$user_id.'" data-toggle="tooltip" title="Show Details"> View</a>
                                   <a href="javascript:void(0)" class="user_delete"  id="user_delete_'.$user_id.'"  data-id="" data-toggle="tooltip" title="Delete"
                                      >
                                     Delete
                                   </a>
                                </div>
                             </div>
                          </td>
                       </tr>';
                       $start++;
  }
}else{
 $user_list_td = "<tr><td colspan='10' class='no-records'>No Records Found </td></tr>";
}


?>

<thead>
    <tr>
       <th>S.No.</th>
       <?php

         if(isset($selected_user_role) && $selected_user_role == 3){
        ?>
       <th>ID</th>
        <?php } ?>
       <th>Profile</th>
       <th>Name</th>
       <th>Email</th>
       <th>Mobile</th>
       
      <?php

         if(isset($selected_user_role) && $selected_user_role == 2){
        ?>
         <th>Restaurant Name</th>
        <?php } ?>
        <th>Status</th>

       <?php

         if(isset($selected_user_role) && $selected_user_role == 3){
        ?>

       <th>Last Used Platform</th>
       <th>Add Money</th>
       <th>Deduct Money</th>
       <th>Wallet History</th>
      <!--  <th>Wallet Balance</th> -->

        <?php } ?>

       <th>Created Date</th>
       <th>Action</th>
    </tr>
   </thead>
<tbody>
   <?php echo $user_list_td;?>
</tbody>


  