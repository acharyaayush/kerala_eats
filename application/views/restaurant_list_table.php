<?php

if (isset($restaurant_list) && $restaurant_list != "") {
 
  $restaurant_list_td = "";

  foreach ($restaurant_list as $value) {
      
      $rest_id = $value['id'];
      $rest_admin_email = $value['email'];
      $rest_admin_contact_no = $value['mobile'];
      $rest_name = stripslashes($value['rest_name']);
      // $rest_street_address = $value['rest_street_address'];
      $rest_street_address = stripslashes($value['rest_pin_address']);
      $avg_rating = $value['avg_rating'];
      $rest_status = $value['rest_status'];
      $offline_status = $value['offline_status'];
      $res_admin_id = $value['admin_id'];// Restaurant admin id 
      $is_bestseller = $value['is_best_seller'];// Restaurant admin id 
      $is_trending = $value['is_trending'];// Restaurant admin id 

      if($rest_status == 1){
          $checked = "checked = ''";
      }else{ 
        $checked  = "";
      }

      if($is_bestseller == 1){
          $checked_best = "checked = ''";
      }else{ 
        $checked_best  = "";
      }

      if($is_trending == 1){
          $checked_trend = "checked = ''";
      }else{ 
        $checked_trend  = "";
      }

      // for start rating
      switch ($avg_rating) {
        case 1:
          //echo $avg_rating;
          $star = '<span class="fa fa-star checked"></span>
                  <span class="fa fa-star"></span>
                  <span class="fa fa-star "></span>
                  <span class="fa fa-star"></span>
                  <span class="fa fa-star"></span>';

          break;
        case 1.5:
          //echo $avg_rating;
         $star = '<span class="fa fa-star-half-alt checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star "></span>
                  <span class="fa fa-star"></span>
                  <span class="fa fa-star"></span>';
          break;
        case 2:
          //echo $avg_rating;
         $star = '<span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star "></span>
                  <span class="fa fa-star"></span>
                  <span class="fa fa-star"></span>';
          break;
        case 2.5:
          //echo $avg_rating;
         $star = '<span class="fa fa-star checked"></span>
                  <span class="fa fa-star-half-alt checked"></span>
                  <span class="fa fa-star"></span>
                  <span class="fa fa-star"></span>
                  <span class="fa fa-star"></span>';
          break;
        case 3:
          //echo $avg_rating;
         $star = '<span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star"></span>
                  <span class="fa fa-star"></span>';
          break;
        case 3.5:
          //echo $avg_rating;
         $star = '<span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star-half-alt checked"></span>
                  <span class="fa fa-star"></span>
                  <span class="fa fa-star"></span>';
          break;
        case 4:
         // echo $avg_rating;
         $star = '<span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star"></span>';
          break;
        case 4.5:
          //echo $avg_rating;
         $star = '<span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star-half-alt checked"></span>
                  <span class="fa fa-star"></span>';
          break;
        case 5:
          //echo $avg_rating;
         $star = '<span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>';
          break;


        default:
          //echo $avg_rating;
           $star = '<span class="fa fa-star"></span>
                  <span class="fa fa-star"></span>
                  <span class="fa fa-star "></span>
                  <span class="fa fa-star"></span>
                  <span class="fa fa-star"></span>';
      }
      //For restaurant enable disable // -- 1  - Enable , 2 - Disable , 3 - Delete
      
      //For Restaurant admin's email id and contact number

      //  <!--<td>'/*.$logo_image.*/'</td-->
      // href="'.base_url().'admin/show_restaurant/'.$rest_id.'" id="res_name_'.$rest_id.'"
      $restaurant_list_td.= '<tr id="res_row_'.$rest_id.'">
                                <td>
                                  '.$start.'
                                </td>
                                <td><a href="'.base_url().'admin/add_edit_restaurant/2/'.$rest_id.'/'.$res_admin_id.'" >'.$rest_name.'</a></td>
                                <td>'.$rest_street_address.'</td>
                                <td>'.$rest_admin_contact_no.'</td>
                                <td>'.$rest_admin_email.'</td>
                                <!-- <td>
                                  '.$star.'
                                </td> -->
                                <td>'.$offline_status.'</td>

                                <td><label class="switch promocode-status">
                                <input type="checkbox" '.$rest_status.' class="res_status" id="res_status_'.$rest_id.'" '. $checked.'>
                                <span class="slider round"></span>
                                </label></td>
                                <td><label class="enabled-label" data-children-count="1"><input type="checkbox" '.$checked_best.' class="action_restro_best_seller" id="best_'.$rest_id.'" name="" value="'.$rest_id.'"><span class="checkmark_check" style="top: -19px;"></span></label></td>
                                <td><label class="enabled-label" data-children-count="1"><input type="checkbox" '.$checked_trend.' class="action_restro_trending" id="trend_'.$rest_id.'" name="" value="'.$rest_id.'"><span class="checkmark_check" style="top: -19px;"></span></label></td>
                                <td><div class="action_icons_dropdown">
                                <i class="fa fa-ellipsis-h"></i>
                                <div class="dropdown-content">
                                <a href="'.base_url().'admin/add_edit_restaurant/2/'.$rest_id.'/'.$res_admin_id.'" class="">Edit</a>
                                <a  class="res_delete"   href="javascript:void(0)" id="res_delete_'.$rest_id.'" data-id="" data-toggle="tooltip" title="Delete"
                                >Delete</a>
                                </div>
                                </div>
                                </td>

                            </tr>';
                            $start++;
  }
 // <a class="" href="'.base_url().'admin/show_restaurant/'.$rest_id.'" id="res_name_'.$rest_id.'" data-toggle="tooltip" title="Show Details"> View</a>
}else{
   $restaurant_list_td = "<tr><td colspan='9' class='no-records'>No Records Found </td></tr>";
}
?>
<thead>
   <tr>
      <th>S.No</th>
      <th>Restaurant Name</th>
      <th>Address</th>
      <th>Phone No.</th>
      <th>Email</th>
      <!-- <th>Logo</th> -->
      <!-- <th width="14%">Rating</th> -->
      <th>Is Receiving order</th>
      <th>Status</th>
      <th>Is Best Seller</th>
      <th>Is Trending</th>
      <th>Action</th>
   </tr>
</thead>
<tbody id="restaurant_table_data">
     <?php echo $restaurant_list_td;?>
</tbody>
 