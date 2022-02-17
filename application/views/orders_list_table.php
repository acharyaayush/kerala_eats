<?php
 
$order_list_td = "";
if (isset($orders_data) && !empty($orders_data)) {

   foreach ($orders_data as $value) {
       $order_id = $value['id'];
       $order_number_id = $value['order_number'];
       $order_status = $value['order_status'];
       $cancelled_by = $value['cancelled_by'];
       $customer_name = $value['customer_name'];
       $customer_user_number_id = $value['user_number_id'];
       $restaurant_id = $value['restaurant_id'];
       $rest_delivery_time  =  $value['delivery_time']; 
       $customer_id = $value['user_id'];
       $delivery_name = $value['delivery_name'];
       $delivery_email = $value['delivery_email'];
       $delivery_mobile = $value['delivery_mobile'];
       $delivery_handled_by = $value['delivery_handled_by'];
       $delivery_address = $value['delivery_address'];
       $restaurant_name = stripslashes($value['rest_name']);
       $payment_mode = $value['payment_mode'];
       $paid_status = $value['paid_status'];
       $is_paid_to_restaurant = $value['is_paid_to_restaurant'];
       $is_cutlery_needed = $value['is_cutlery_needed'];
       $promo_code_is_applied = $value['promo_subtotal_is_applied'];
       $promo_code_is_applied_on_delivery = $value['promo_dc_is_applied'];
       $order_type = $value['order_type'];// 1 - Order Now, 2 - Self Pickup,3 - Order For Later, 4 - Dine In
       $order_type_name = $value['order_type_name'];
       $business_category = $value['business_category_name'];
       $is_pickup_time_changed = $value['is_pickup_time_changed']; 
        $is_order_customized = $value['is_order_customized'];
        if ($is_order_customized == 1)
        {
            $total_amount = $value['total_amount'];//item total + DC |(after promo if any)
        }
        else
        {
            $total_amount = $value['total_amount_paid'] + $value['wallet_debited_value'];
        }
       $updated_by = $value['updated_by'];//1 - super admin, 2 - by merchant (who customize orderd items)
       $order_place_time = $value['created_at'];
       $order_schedule_time = $value['schedule_time'];// if order type 2 or 3 then schedule time will be go and admin can edit also
       $res_admin_id  =  $value['admin_id'];

       $preparation_time_when_ordered = $value['preparation_time_when_ordered'];
       // if($order_status  == 0 && $value['preparation_time_when_accepted'] == ''){ // if order os pending 
       if($value['preparation_time_when_accepted'] == ''){ // if order os pending 
        $preparation_time_when_ordered  =  $value['preparation_time_when_ordered']; 
       }else{// if after  order accepted 
        $preparation_time_when_ordered  =  $value['preparation_time_when_accepted']; 
       }

       # If restaurant has not set any delivery time then we need to take value from settings table
       if($rest_delivery_time == 0 || $rest_delivery_time == '')
       {
            $basic_delv_time = $this->Common->getData('settings','value','name = "basic_delivery_time"');
            $rest_delivery_time = $basic_delv_time[0]['value'];
       }
       
      //  0 -Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed 6 Preparing 7 Ready, 9 -delete
      switch ($order_status) {
        case 0:
          
            $status = "Placed and Pending";
            $pending_status_checked = 'selected';
            $accepted_status_checked = '';
            $rejected_status_checked = '';
            $dispatched_status_checked = '';
            $cancel_status_checked = '';
            $completed_status_checked = '';
            $ready_status_checked = '';
            $preparing_status_checked = '';

          break;
        case 1:
          
            $status = "Accepted";
            $pending_status_checked = '';
            $accepted_status_checked = 'selected';
            $rejected_status_checked = '';
            $dispatched_status_checked = '';
            $cancel_status_checked = '';
            $completed_status_checked = '';
            $ready_status_checked = '';
            $preparing_status_checked = '';
          break;
        case 2:
          
            $status = "Rejected";
            $pending_status_checked = '';
            $accepted_status_checked = '';
            $rejected_status_checked = 'selected';
            $dispatched_status_checked = '';
            $cancel_status_checked = '';
            $completed_status_checked = '';
            $ready_status_checked = '';
            $preparing_status_checked = '';
          break;
        case 3:
          
            $status = "Dispatched";
            $pending_status_checked = '';
            $accepted_status_checked = '';
            $rejected_status_checked = '';
            $dispatched_status_checked = 'selected';
            $cancel_status_checked = '';
            $completed_status_checked = '';
            $ready_status_checked = '';
            $preparing_status_checked = '';
          break;
        case 4:
          
           $status = 'Cancelled';
           $cancel_status_checked = 'selected';
           $accepted_status_checked = '';
           $rejected_status_checked = '';
           $dispatched_status_checked = '';
           $pending_status_checked = '';
           $completed_status_checked = '';
           $ready_status_checked = '';
           $preparing_status_checked = '';

          break;

      case 5:
          
         $status = 'Completed';
         $completed_status_checked = 'selected';
         $pending_status_checked = '';
         $accepted_status_checked = '';
         $rejected_status_checked = '';
         $dispatched_status_checked = '';
         $cancel_status_checked = '';
         $ready_status_checked = '';
         $preparing_status_checked = '';

          break;

       case 6:
          
         $status = 'Preparing';
         $preparing_status_checked = 'selected';
         $completed_status_checked = '';
         $pending_status_checked = '';
         $accepted_status_checked = '';
         $rejected_status_checked = '';
         $dispatched_status_checked = '';
         $cancel_status_checked = '';
         $ready_status_checked = '';

          break;

       case 7:
          
         $status = 'Ready';
         $ready_status_checked = 'selected';
         $preparing_status_checked = '';
         $completed_status_checked = '';
         $pending_status_checked = '';
         $accepted_status_checked = '';
         $rejected_status_checked = '';
         $dispatched_status_checked = '';
         $cancel_status_checked = '';

          break;

        default:
         $status = "Pending";
         $pending_status_checked = 'selected';
         $preparing_status_checked = '';
         $ready_status_checked = '';
         $accepted_status_checked = '';
         $rejected_status_checked = '';
         $dispatched_status_checked = '';
         $cancel_status_checked = '';
         $completed_status_checked ='';

      }// switch case end


      //new and pendind order will be highlight ---
      if($order_status == 0){
          $highltight_pending_order_class= 'highltight_pending_order';
      }else{
        $highltight_pending_order_class = '';
      }

      //For Cancel by 1 - cancelled by admin 2 - Auto cancelled
      if($cancelled_by == 1){
         $cancelled_by_name = "By Admin";
      
      }else if($cancelled_by == 2){
          $cancelled_by_name = "Auto Cancelled";
      }else{
          $cancelled_by_name = "-";
      }


      // For delivery handle by 1 - restaurant 2 - By Kerala Eats-----
      if($delivery_handled_by == 1){
         $delivery_handled = "Restaurant";
      
      }else if($delivery_handled_by == 2){
          $delivery_handled = "Kerala Eats";
      }

      // For order place time--------
      date_default_timezone_set('Asia/Singapore');
      //Epcho time convert --------
     // $order_place_time_date  = date("d-m-Y  H:i",$order_place_time);// convert UNIX timestamp to PHP DateTime
     $order_place_time_date  = date("d-m-Y  h:i A",$order_place_time);// convert UNIX timestamp to PHP DateTime
    // $order_place_current_time  = date("H:i",$order_place_time);
    $order_place_current_time  = date("h:i A",$order_place_time);

 
       //Epcho time convert --------
       if($order_schedule_time != 0){
            $order_schedule_time = new DateTime("@$order_schedule_time");  // convert UNIX timestamp to PHP DateTime
            // $order_schedule_time_and_date = $order_schedule_time->format('d-m-Y H:i');
            $order_schedule_time_and_date = $order_schedule_time->format('d-m-Y h:i A');
           
            
       }else{
           $order_schedule_time_and_date = "-";
       }

      
     //# 1 : Stripe 2 : Hitpay 3 : only wallet used----------------------
     if($payment_mode == 1){
        $payment_mode = "Credit/Debit";//Stripe

     // }else if($delivery_handled_by == 2){
     //    $payment_mode = "PayNow";//Hitpay
     // }else if($delivery_handled_by == 3){
     //     $payment_mode = "Wallet";
     // }

     }else if($payment_mode == 2){
        $payment_mode = "PayNow";//Hitpay
     }else if($payment_mode == 3){
         $payment_mode = "Wallet";
     }


      //For paid status 0 - Unpaid and 1 - Paid---------------------------
      if($paid_status == 0){
         $paid_status = "Unpaid";
      
      }else if($paid_status == 1){
         $paid_status = "Paid";
      }

      //For Promo Code auto applied 
     if($promo_code_is_applied  == 1){
         $promo_code_is_applied_status = "Yes";
      
      }else if($promo_code_is_applied == 2){
         $promo_code_is_applied_status = "No";
      }else{
          $promo_code_is_applied_status = "No";
      }

      //For Promo Code auto applied  on delivery
     if($promo_code_is_applied_on_delivery == 1){
         $promo_code_is_applied_on_delivery_status = "Yes";
      
      }else if($promo_code_is_applied_on_delivery == 2){
         $promo_code_is_applied_on_delivery_status = "No";
      }else{
          $promo_code_is_applied_on_delivery_status = "No";
      }

      //For is paid to restauant "Indicates whether admin paid to restaurant manually. 1 - Yes 2 -No(means pending)"
     if($is_paid_to_restaurant == 1){

         $checkbox_checked = "checked = 'checked'";
         $is_paid_to_restaurant_status_value = "0";
      
      }else if($is_paid_to_restaurant == 0){
         $checkbox_checked = "";
         $is_paid_to_restaurant_status_value = "1";
      }else{
         $checkbox_checked = "";
        $is_paid_to_restaurant_status_value = "1";
      }

      // For is cutlery  need 
      if($is_cutlery_needed  == 1){
         $is_cutlery_needed = "Yes";
      
      }else if($is_cutlery_needed == 2){
         $is_cutlery_needed = "No";
      }else{
          $is_cutlery_needed = "No";
      }


      if($updated_by == 1){
          $customize_by = 'Super Admin';
      }else  if($updated_by == 2){
          $customize_by = 'Restaurant';
      }else{
          $customize_by = '-';
      }

        //Epcho time convert -------- 
        $pickup_time_from = $value['pickup_time_from'];
         if($pickup_time_from != 0){
             //Epcho time convert --------
           
           // $final_pickup_time_from =  date("d-m-Y  H:i",$pickup_time_from);// convert UNIX timestamp to PHP DateTime
           $final_pickup_time_from =  date("d-m-Y  h:i A",$pickup_time_from);// convert UNIX timestamp to PHP DateTime
           $final_pickup_date_from =  date("d-m-Y",$pickup_time_from);// convert UNIX timestamp to PHP DateTime
           // $pickup_time_from_for_range = date("H:i",$pickup_time_from);
           $pickup_time_from_for_range = date("h:i A",$pickup_time_from);
           
         }else{
          $final_pickup_time_from = "";
          $final_pickup_date_from  = date("d-m-Y",$order_place_time);// convert UNIX timestamp to PHP DateTime
          $pickup_time_from_for_range = "";
         }
       //Epcho time convert --------
        $pickup_time_to = $value['pickup_time_to'];
        if($pickup_time_to != 0){
          // $final_pickup_time_to =date("d-m-Y  H:i",$pickup_time_to);// convert UNIX timestamp to PHP DateTime
          $final_pickup_time_to =date("d-m-Y  h:i A",$pickup_time_to);// convert UNIX timestamp to PHP DateTime

           // $final_pickup_time_only_to =date("H:i",$pickup_time_to);// convert UNIX timestamp to PHP DateTime
           $final_pickup_time_only_to =date("h:i A",$pickup_time_to);// convert UNIX timestamp to PHP DateTime
        }else{
          $final_pickup_time_to = "-";
          $final_pickup_time_only_to = "";
        }

        
    // calcualtion of pickup time range  -----------START----------------
    # check order type
    if($order_type == 1){//order for Now
        #calcualtion = orderd time + (Preparation  Time when orderd)
        
       //  //$del_and_pre_time_total  = $preparation_time_when_ordered;
       //   $mint_convert_in_hours = intdiv($preparation_time_when_ordered, 60).':'. ($preparation_time_when_ordered % 60);
       // // echo '<br>';
       //  $pickup_time_range = strtotime($order_place_current_time) + strtotime($mint_convert_in_hours);
       //  $final_pickup_range  =  date("H:i",$pickup_time_range);

       //  /*  $pickup_time_range = new DateTime("@$pickup_time_range");
       //  $final_pickup_range = $pickup_time_range->format('H:i');*/

       //  //we  gettting diffrence thats why we calculate by hours
       //  $time = strtotime($final_pickup_range);

       //  //getting estimated time accroding to the customer app --start----
       //  $estimated_time  = $preparation_time_when_ordered + $rest_delivery_time;
       //  $estimated_mint_convert_in_hours = intdiv($estimated_time, 60).':'. ($estimated_time % 60);
       //  $final_estimated_time = strtotime($order_place_current_time) + strtotime($estimated_mint_convert_in_hours);
       //  $convert_schedule_time  =  date("H:i",$final_estimated_time);

       //  //we  gettting diffrence thats why we calculate by hours
       //  $time2 = strtotime($convert_schedule_time);
       //  $schedule_time = date("d-m-Y H:i", strtotime('+7 hour +60 minutes', $time2));
       //  //getting estimated time accroding to the customer app --end----

        if($is_pickup_time_changed == 1)
        {
            if ($rest_delivery_time != "")
            {
                $mint_convert_in_hours = intdiv($rest_delivery_time, 60) . ':' . ($rest_delivery_time % 60);

                $pickup_time_range = strtotime($pickup_time_from_for_range) - strtotime($mint_convert_in_hours);
                $pickup_time_range_change = new DateTime("@$pickup_time_range");
                // $final_pickup_range = $pickup_time_range_change->format('H:i');
                $final_pickup_range = $pickup_time_range_change->format('h:i A');
            }
            else
            {
                $final_pickup_range = $pickup_time_from_for_range;
            }
            $schedule_time = $final_pickup_date_from . ' ' . $pickup_time_from_for_range;
        }else
        {
            $final_pickup_range = strtotime('+' . $preparation_time_when_ordered . 'minutes', $order_place_time);
            $sch1 = strtotime('+' . $rest_delivery_time . 'minutes', $final_pickup_range);
            $final_pickup_range = date("h:i A", $final_pickup_range);
            $schedule_time = date("d-m-Y h:i A", $sch1);
        }


        // $f_pickup_range = strtotime('+'.$preparation_time_when_ordered.'minutes',$order_place_time);
        // $sch1 = strtotime('+'.$rest_delivery_time.'minutes',$f_pickup_range);
        // // $schedule_time = date("d-m-Y H:i", $sch1);
        // $schedule_time = date("d-m-Y h:i A", $sch1);

        // // $final_pickup_range = date("H:i", $f_pickup_range);
        // $final_pickup_range = date("h:i A", $f_pickup_range);

    }else if($order_type == 2){//Self Pickup
        #calcualtion =  only we need to pick from  time (dont need to calcualtion)

        $final_pickup_range = $pickup_time_from_for_range;

        $schedule_time =  $final_pickup_date_from.' '.$pickup_time_from_for_range.' to '. $final_pickup_time_only_to;
        

    }else if($order_type == 3){//Order For Later
        #calcualtion = pick from  time - Delivery Time

        if($rest_delivery_time !=""){
            $mint_convert_in_hours = intdiv($rest_delivery_time, 60).':'. ($rest_delivery_time % 60);

            $pickup_time_range = strtotime($pickup_time_from_for_range) - strtotime($mint_convert_in_hours);
            $pickup_time_range_change = new DateTime("@$pickup_time_range");
            // $final_pickup_range = $pickup_time_range_change->format('H:i');
            $final_pickup_range = $pickup_time_range_change->format('h:i A');

            
        }else{
            $final_pickup_range = $pickup_time_from_for_range;
        }

        $schedule_time =  $final_pickup_date_from.' '.$pickup_time_from_for_range.' to '. $final_pickup_time_only_to;
    }
    // calcualtion of pickup time range  -----------END----------------


       $order_list_td.= ' <tr class="'.$highltight_pending_order_class.'">
                              <td><a href="'. base_url().'admin/order_single/'.$order_id.'" id="order_number_id_'.$order_id.'">'.$order_number_id.'</a></td>
                              <td>
                               <select class="custom-select  wv_filter_box_height form-control order_status"   name="order_status_'.$order_id.'">
                                   <option value="0"  '.$pending_status_checked.'>Pending</option>
                                   <option value="1" '.$accepted_status_checked.'>Accepted</option>
                                   <option value="2" '.$rejected_status_checked.'>Rejected</option>
                                   <option value="3" '.$dispatched_status_checked.'>Dispatched</option>
                                   <option value="4" '.$cancel_status_checked.'>Cancelled</option>
                                   <option value="5" '.$completed_status_checked.'>Completed</option>
                                   <option value="6" '.$preparing_status_checked.'>Preparing</option>
                                   <option value="7" '.$ready_status_checked.'>Ready</option>
                                </select>
                         </td>
                          </td>
                              <td><a href="'.base_url('admin/add_edit_restaurant/2/'.$restaurant_id.'/'.$res_admin_id.'').'">'.$restaurant_name.'</a></td>';
//we are handling order details and customer order detail on user_details page from one controller                             
if($this->uri->segment(2) == 'orders' && $this->uri->segment(4) == 0){//only orders page 
      $order_list_td.=    ' <td><a href="'.base_url('admin/user_details/3/'.$customer_id .'').'">'.$customer_name.'</a></td>
      <td>'.$delivery_mobile.'</td>
                             ';

  }
      
$order_list_td.=    ' <td>'.$order_place_time_date.'</td>';

       if($this->uri->segment(2) == 'orders' && $this->uri->segment(4) == 0){//only orders page 

        $order_list_td.=    '<td>'.$final_pickup_date_from.' '. $final_pickup_range.'</td>';
       }
         
         $order_list_td.=     '
                              <td>'.$schedule_time.'</td>';


        if($this->uri->segment(2) == 'orders' && $this->uri->segment(4) == 0){//only orders page                       
           $order_list_td.=     ' <td>'.$order_type_name.'</td>
                              <td>'.$business_category.'</td>
                              <td>'.$delivery_handled.'</td>';
                             
          }                    
         $order_list_td.=     '<td>'.$payment_mode.'</td>';

if($this->uri->segment(2) == 'orders' && $this->uri->segment(4) == 0){//only orders page 
          $order_list_td.=    '
<td>S$ '.$total_amount.'</td>
                               <td>'.$promo_code_is_applied_status.'</td>
                               <td>'.$paid_status.'</td>

                              <td class="addresTd">'.$delivery_address.'</td>';

      if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){// only super admin can see and update
       $order_list_td.= '       <td>
                                   <label class="enabled-label" data-children-count="1">
                                      
                                     <input type="checkbox" class="is_paid_to_restaurant_status" value="'.$is_paid_to_restaurant_status_value.'" selected_order_id = "'.$order_id.'" '.$checkbox_checked.'>
                                        <span class="checkmark_check"></span>
                                      </label>
                                </td>';
     }

       $order_list_td.= ' <td>'.$cancelled_by_name.'</td>
                              <td>
                                 <div class="action_icons_dropdown">
                                    <i class="fa fa-ellipsis-h"></i>
                                    <div class="dropdown-content">
                                       <a class="" href="'. base_url().'admin/order_single/'.$order_id.'" id="order_number_id_'.$order_id.'" data-toggle="tooltip" title="Show Details">View </a>
                                       <a href="javascript:void(0)" class="order_delete" id="order_delete_'.$order_id.'" data-id="" data-toggle="tooltip" title="Delete"
                                          >Delete
                                       </a>
                                    </div>
                                 </div>
                              </td>';
    }
      $order_list_td.= '</tr>';
             

   }

}else{
  $order_list_td = "<tr><td colspan='11' class='no-records'>No Records Found </td></tr>";
}
?>
<thead>
   <tr>
      <th> Order ID</th>
      <th>Order Status</th>
      <th>Restaurant</th>
   <?php if($this->uri->segment(2) == 'orders' && $this->uri->segment(4) == 0){?>
      <th>Customer Name</th>
    <?php
       }
   ?>
   <?php if($this->uri->segment(2) == 'orders' && $this->uri->segment(4) == 0){?>
      <th>Customer Number </th>
   <?php
       }
   ?>    
      <th>Order Time </th>
   <?php if($this->uri->segment(2) == 'orders' && $this->uri->segment(4) == 0){?>
      <th>Pick-up Time</th>
   <?php
       }
   ?> 
       <th>Schedule Time</th>
   <?php if($this->uri->segment(2) == 'orders' && $this->uri->segment(4) == 0){?>
      <th>Order Type </th>
      <th>Business Type</th>
      <th>Delivery Handel By</th>
   <?php
       }
   ?>
      <th>Payment Method</th>
  <?php if($this->uri->segment(2) == 'orders' && $this->uri->segment(4) == 0){?>
      <th>Amount</th>
      <th>Promo Code Applied </th>
      <th>Payout Status </th>
    
       <th class="addresTd">Address</th>
      
       
         <?php 
          //  if merchant is logged in, then this condition will check and only merchant- restaurant orders will show. if this blank that means super admin is logged in and then all resataurant will show 
         //only super admin can see this field
          if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){
        ?>
      <th>Is Paid to Restaurant</th>
        <?php
          }
         ?>
      <th>Cancel By</th>
      <th>Action</th>
    <?php
       }
   ?>
   </tr>
</thead>
<tbody>
   <?php echo  $order_list_td;?>
</tbody>