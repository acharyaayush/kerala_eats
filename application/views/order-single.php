<?php

if (isset($single_order_data) && !empty($single_order_data))
{

    $order_id = $single_order_data[0]['id'];
    $order_number_id = $single_order_data[0]['order_number'];

    $customer_id = $single_order_data[0]['user_id'];
    $restaurant_id = $single_order_data[0]['restaurant_id'];

    $pickup_time_from = $single_order_data[0]['pickup_time_from'];
    $pickup_time_to = $single_order_data[0]['pickup_time_to'];
    $delivery_handled_by = $single_order_data[0]['delivery_handled_by'];
    $admin_commission = $single_order_data[0]['admin_commission'];
    $restaurant_commission = $single_order_data[0]['restaurant_commission'];
    $is_paid_to_restaurant = $single_order_data[0]['is_paid_to_restaurant'];
    $order_status = $single_order_data[0]['order_status']; // 0 - Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed rest we will mention soon(if any), 6 - Delete
    $is_pickup_time_changed = $single_order_data[0]['is_pickup_time_changed']; 
    $preparation_time_when_ordered = $single_order_data[0]['preparation_time_when_ordered'];
    // if($order_status  == 0 && $single_order_data[0]['preparation_time_when_accepted'] == ''){ // if order os pending
    if ($single_order_data[0]['preparation_time_when_accepted'] == '')
    { // if order os pending
        $preparation_time_when_accepted = $single_order_data[0]['preparation_time_when_ordered'];
    }
    else
    { // if after  order accepted
        $preparation_time_when_accepted = $single_order_data[0]['preparation_time_when_accepted'];
    }

    $payment_mode = $single_order_data[0]['payment_mode'];

    $outstanding_amount = $single_order_data[0]['outstanding_amount'];
    $who_will_pay_outstanding_amount_any_customized = $single_order_data[0]['who_will_pay_outstanding_amount']; //2- restaurant will pay to customer, 3 - customer will pay to restaurant,default 0

    $remaining_outstanding_amount =  $single_order_data[0]['outstanding_amount'];
    $is_paid_outstanding_amount = $single_order_data[0]['is_paid_outstanding_amount']; //If "who_will_pay_outstanding_amount" value is grater then o , than need to check outstanding is paid or not , if paid the value will be 1
    $is_cutlery_needed = $single_order_data[0]['is_cutlery_needed'];

    $cancelled_by = $single_order_data[0]['cancelled_by'];
    $order_schedule_time = $single_order_data[0]['schedule_time'];
    $paid_status = $single_order_data[0]['paid_status'];
    $is_order_customized = $single_order_data[0]['is_order_customized'];
    $promo_dc_is_applied = $single_order_data[0]['promo_dc_is_applied'];
    if ($is_order_customized == 1)
    {
        $grand_total_amount = $single_order_data[0]['total_amount'];
    }
    else
    {
        $grand_total_amount = $single_order_data[0]['total_amount_paid'] + $single_order_data[0]['wallet_debited_value'];
    }

    $total_amount_paid_through_gateway = $single_order_data[0]['total_amount_paid'];
    $wallet_debited_value = $single_order_data[0]['wallet_debited_value'];

    $grand_total_amount = number_format($grand_total_amount, 2, '.', '');

    $actual_dc_amount = $single_order_data[0]['actual_dc_amount'];
    $dc_amount = $single_order_data[0]['dc_amount'];

    /*if($actual_dc_amount == '0.00' && $promo_dc_is_applied == 1){

       $dc_discount_value = number_format($promo_code_if_applied_on_delivery[0]['discount_value'],2);
        if ($promo_code_if_applied_on_delivery[0]['promo_type'] == 1)
        {
            $actual_dc_amount = $dc_discount_value+$dc_amount;
        }
        else if ($promo_code_if_applied_on_delivery[0]['promo_type'] == 2)
        {
            $calcualte_actual_delivery_charge = $dc_amount*$dc_discount_value/100;
            $actual_dc_amount = $calcualte_actual_delivery_charge+$dc_amount;
        }
        else
        {
            $actual_dc_amount = $dc_amount;
        }
        $actual_dc_amount = number_format($actual_dc_amount,1,'.','');
    }else{
        $actual_dc_amount = $dc_amount;
    }*/

    /*if($actual_dc_amount == '0.00' && $promo_dc_is_applied == 1){
        $dc_discount_value = number_format($promo_code_if_applied_on_delivery[0]['discount_value'],2);
        $calcualte_actual_delivery_charge = $dc_amount*$dc_discount_value/100;
        $actual_dc_amount = $calcualte_actual_delivery_charge+$dc_amount;
    }if($actual_dc_amount == '0.00' && $promo_dc_is_applied == 2){
        $actual_dc_amount = $dc_amount;
    }*/

    //echo $actual_dc_amount;
    $sub_total = $single_order_data[0]['sub_total'];
    $item_quantity = $single_order_data[0]['item_quantity'];

    $track_link = $single_order_data[0]['track_link'];
    $lalamove_order_id = $single_order_data[0]['lalamove_order_id'];
    $lalamove_order_status = $single_order_data[0]['lalamove_order_status']; //  1 - success 2 - Fail 3 -Not applicable that means it is selfpickup
    if ($lalamove_order_status == 1)
    {
        $lalamove_order_status_val = "Success";
    }
    else if ($lalamove_order_status == 2)
    {
        $lalamove_order_status_val = "Fail";
    }
    else if ($lalamove_order_status == 3)
    {
        $lalamove_order_status_val = "Not Applicable";
    }
    else
    {
        $lalamove_order_status_val = "";
    }

    # Ordering platform (1 for iOS and 2 for android)
      if($single_order_data[0]['ordering_platform'] == 1)
      {
        $ordering_platform = "iOS";
      }elseif($single_order_data[0]['ordering_platform'] == 2)
      {
        $ordering_platform = "Android";
      }else
      {
        $ordering_platform = "NA";
      }

      # actual_dc_amount 
      $lalamove_original_dc = $single_order_data[0]['actual_dc_amount'];

    $delivery_address = $single_order_data[0]['delivery_address'];
    $delivery_street_address = $single_order_data[0]['delivery_street_address'];
    $delivery_postal_code = $single_order_data[0]['delivery_postal_code'];
    $delivery_unit_number = $single_order_data[0]['delivery_unit_number'];
    $delivery_latitude = $single_order_data[0]['delivery_latitude'];
    $delivery_longitude = $single_order_data[0]['delivery_longitude'];

    $delivery_name = $single_order_data[0]['delivery_name'];
    $delivery_email = $single_order_data[0]['delivery_email'];
    $delivery_mobile = $single_order_data[0]['delivery_mobile'];

    $remark = $single_order_data[0]['remark'];
    $order_place_time = $single_order_data[0]['created_at'];

    $customer_number_id = $single_order_data[0]['user_number_id'];
    $customer_name = $single_order_data[0]['customer_name'];
    $customer_email = $single_order_data[0]['email'];
    $customer_contact_no = $single_order_data[0]['mobile'];
    $actual_promo_subtotal_discounted_value = $single_order_data[0]['promo_subtotal_discounted_value'];

    $restaurant_name = $single_order_data[0]['rest_name'];
    $res_admin_id = $single_order_data[0]['admin_id'];
    $rest_delivery_time = $single_order_data[0]['delivery_time'];
    # If restaurant has not set any delivery time then we need to take value from settings table
    if ($rest_delivery_time == 0 || $rest_delivery_time == '')
    {
        $basic_delv_time = $this
            ->Common
            ->getData('settings', 'value', 'name = "basic_delivery_time"');
        $rest_delivery_time = $basic_delv_time[0]['value'];
    }

    $order_type_name = $single_order_data[0]['order_type_name'];
    $order_type = $single_order_data[0]['order_type']; // 1 - Order Now, 2 - Self Pickup,3 - Order For Later, 4 - Dine In
    $order_schedule_time = $single_order_data[0]['schedule_time'];

    $business_category_name = $single_order_data[0]['business_category_name'];
    $checkout_status_by_admin = $single_order_data[0]['checkout_status_by_admin']; //1 - orderd product data customized successfuly done by admin(merchant or admin), after checkout orders table will be update with total calculation after customization.
    $admin_checkout_delivery_charge_if_change = $single_order_data[0]['admin_checkout_delivery_charge_if_change']; //if delivery address change by admin or restaunt , then delivery charge also will be change that we handle to show on checkout page only one time, after checkout this value will be 0 , only we take this value temprory purpose
    

    // For order place time--------
    //Epcho time convert --------
    date_default_timezone_set('Asia/Singapore');
    // $order_place_time_date  = date("d-m-Y  H:i",$order_place_time);// convert UNIX timestamp to PHP DateTime
    $order_place_time_date = date("d-m-Y  h:i A", $order_place_time); // convert UNIX timestamp to PHP DateTime
    // $order_place_current_time  = date("H:i",$order_place_time);
    $order_place_current_time = date("h:i A", $order_place_time);

    // For order schedule time--------
    //Epcho time convert --------
    //Epcho time convert --------
    $pickup_time_from = $single_order_data[0]['pickup_time_from'];
    if ($pickup_time_from != 0)
    {
        //Epcho time convert --------
        // $final_pickup_time_from =  date("d-m-Y  H:i",$pickup_time_from);// convert UNIX timestamp to PHP DateTime
        $final_pickup_time_from = date("d-m-Y  h:i A", $pickup_time_from); // convert UNIX timestamp to PHP DateTime
        $final_pickup_date_from = date("d-m-Y", $pickup_time_from); // convert UNIX timestamp to PHP DateTime
        // $pickup_time_from_for_range = date("H:i",$pickup_time_from);
        $pickup_time_from_for_range = date("h:i A", $pickup_time_from);
    }
    else
    {
        $final_pickup_time_from = "";
        $final_pickup_date_from = date("d-m-Y", $order_place_time); // convert UNIX timestamp to PHP DateTime
        $pickup_time_from_for_range = "";
    }
    //Epcho time convert --------
    $pickup_time_to = $single_order_data[0]['pickup_time_to'];
    if ($pickup_time_to != 0)
    {
        // $final_pickup_time_to =date("d-m-Y  H:i",$pickup_time_to);// convert UNIX timestamp to PHP DateTime
        $final_pickup_time_to = date("d-m-Y  h:i A", $pickup_time_to); // convert UNIX timestamp to PHP DateTime
        $final_pickup_date_to = date("d-m-Y", $pickup_time_to); // convert UNIX timestamp to PHP DateTime
        // $final_pickup_time_only_to =date("H:i",$pickup_time_to);// convert UNIX timestamp to PHP DateTime
        $final_pickup_time_only_to = date("h:i A", $pickup_time_to); // convert UNIX timestamp to PHP DateTime
    }
    else
    {
        $final_pickup_time_to = "";
        $final_pickup_date_to = date("d-m-Y", $order_place_time); // convert UNIX timestamp to PHP DateTime
        $final_pickup_time_only_to = "";
    }

    // calcualtion of pickup time range  -----------START----------------
    # check order type
    if ($order_type == 1) # TAG_EDITPICKUPTIME : New column in db added
    { 
        # If order now and pickup time not changed then we need to calculate using create at but if pickup is changed then we need to calculate using updaed pikcup time
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
            $final_pickup_range = strtotime('+' . $preparation_time_when_accepted . 'minutes', $order_place_time);
            $sch1 = strtotime('+' . $rest_delivery_time . 'minutes', $final_pickup_range);
            $final_pickup_range = date("h:i A", $final_pickup_range);
            $schedule_time = date("d-m-Y h:i A", $sch1);
        }

        //order for Now
        #calcualtion = orderd time + (Preparation  Time when orderd)
        //$del_and_pre_time_total  = $preparation_time_when_ordered;
        // $mint_convert_in_hours = intdiv($preparation_time_when_ordered, 60).':'. ($preparation_time_when_ordered % 60);
        // echo "chkkkk".$mint_convert_in_hours;
        // echo '<br>';
        // $pickup_time_range = strtotime($order_place_current_time) + strtotime($mint_convert_in_hours);
        // $final_pickup_range = strtotime('+'.$preparation_time_when_ordered.'minutes',strtotime($order_place_current_time));
        // echo "cccc".$order_place_time;
        // $final_pickup_range = strtotime('+'.$preparation_time_when_ordered.'minutes',$order_place_time);
        
        # To make schedule time
        
        // echo "qqqqqqq".$sch1;
        # Add 8 hours more because created date in db is as per UTC timezone
        // $sch1 = strtotime($sch1);
        // $final_pickup_range  =  date("H:i",$pickup_time_range);
        /*  $pickup_time_range = new DateTime("@$pickup_time_range");
         $final_pickup_range = $pickup_time_range->format('H:i');*/

        //we  gettting diffrence thats why we calculate by hours
        // $time = strtotime($final_pickup_range);
        // $final_pickup_range = date("H:i", strtotime('+7 hour +60 minutes', $time));
        // $final_pickup_range = date("H:i", $final_pickup_range);
        

        //getting estimated time accroding to the customer app --start----
        // $estimated_time  = $preparation_time_when_ordered + $rest_delivery_time;
        // $estimated_mint_convert_in_hours = intdiv($estimated_time, 60).':'. ($estimated_time % 60);
        // $final_estimated_time = strtotime($order_place_current_time) + strtotime($estimated_mint_convert_in_hours);
        // $convert_schedule_time  =  date("H:i",$final_estimated_time);
        //we  gettting diffrence thats why we calculate by hours
        // $time2 = strtotime($convert_schedule_time);
        // $schedule_time = date("d-m-Y H:i", strtotime('+7 hour +60 minutes', $time2));
        // $schedule_time = date("d-m-Y H:i", $sch1);
        
        //getting estimated time accroding to the customer app --end----
        
    }
    else if ($order_type == 2)
    { //Self Pickup
        #calcualtion =  only we need to pick from  time (dont need to calcualtion)
        $final_pickup_range = $pickup_time_from_for_range;

        $schedule_time = $final_pickup_date_from . ' ' . $pickup_time_from_for_range . ' to ' . $final_pickup_time_only_to;

    }
    else if ($order_type == 3)
    { //Order For Later
        #calcualtion = pick from  time - Delivery Time
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

        $schedule_time = $final_pickup_date_from . ' ' . $pickup_time_from_for_range . ' to ' . $final_pickup_time_only_to;
    }
    // calcualtion of pickup time range  -----------END----------------
    //# 1 : Stripe 2 : Hitpay 3 : only wallet used----------------------
    if ($payment_mode == 1)
    {
        $payment_mode = "Credit/Debit"; //Stripe
        
    }
    else if ($payment_mode == 2)
    {
        $payment_mode = "PayNow"; //Hitpay
        
    }
    else if ($payment_mode == 3)
    {
        $payment_mode = "Wallet";
    }

    if ($order_type == 1)
    { //(If order type is "Order Now")
        $schedule_edit_mode = "";
    }
    else
    {
        $schedule_edit_mode = "d-none";
    }

    if ($order_schedule_time == "" || $order_schedule_time == 0)
    {
        $final_order_schedule_time = "";
    }
    else
    {
        /* $order_schedule_time_change = new DateTime("@$order_schedule_time");  // convert UNIX timestamp to PHP DateTime
         $final_order_schedule_time = $order_schedule_time_change->format('m-d-Y  H:i');
         $final_order_schedule_time = date('d-m-Y H:i',strtotime('+5 hour +30 minutes +1 seconds',strtotime($final_order_schedule_time)));*/

        $order_schedule_time = new DateTime("@$order_schedule_time"); // convert UNIX timestamp to PHP DateTime
        // $final_order_schedule_time = $order_schedule_time->format('d-m-Y H:i');
        $final_order_schedule_time = $order_schedule_time->format('d-m-Y h:i A');
        //$final_order_schedule_time = date('d-m-Y H:i',strtotime('+5 hour +30 minutes +1 seconds',strtotime($final_order_schedule_time)));
        
    }

    // For is cutlery  need
    if ($is_cutlery_needed == 1)
    {
        $is_cutlery_needed = "Yes";

    }
    else if ($is_cutlery_needed == 2)
    {
        $is_cutlery_needed = "No";
    }
    else
    {
        $is_cutlery_needed = "No";
    }

    //Check who will pay outstanding amount if customized order data
    if ($who_will_pay_outstanding_amount_any_customized == 3 && $outstanding_amount > 0)
    {
        $who_will_pay_outstanding = "(Customer will pay)";
    }
    else
    {
        $who_will_pay_outstanding = "";
    }

    if ($who_will_pay_outstanding_amount_any_customized == 2 || $who_will_pay_outstanding_amount_any_customized == 3)
    { // 2 or 3
        if ($is_paid_outstanding_amount == 1)
        {
            $is_paid_outstanding_amount = "Paid";
        }
        else if ($is_paid_outstanding_amount == 0)
        {
            $is_paid_outstanding_amount = "Un Paid";
        }
    }
    else
    {
        $is_paid_outstanding_amount = "-";
    }

    //order_product_details----------------For Products item -------------------------
    $item_list = "";
    $varaint_type_html = "";
    $items_total = 0;
    $total_item_quantity = 0;
    //print_r($order_product_details);
    $total_of_variant_price = 0;
    if ($order_product_details && !empty($order_product_details))
    {
        foreach ($order_product_details as $item_value)
        {
            $product_id = $item_value['product_id'];
            $product_name = trim($item_value['product_name']);
            $product_quantity = $item_value['product_quantity'];
            $product_unit_price = $item_value['product_unit_price'];
            if ($item_value['product_image'] != "")
            {
                $product_image = base_url($item_value['product_image']);
            }
            else
            {
                $product_image = base_url('assets/images/default_product_image.png');
            }

            $item_list .= '<tr>
                                 <td class="profile-img"><img src="' . $product_image . '" /></td>
                                 <td colspan="4">
                                    <h3>' . $product_name . '</h3>
                                    <p>S$<span class="product_price_' . $product_id . '">' . $product_unit_price . '</span></p>';

            if (!empty($item_value['variants']))
            { // if any varaint available  in order
                foreach ($item_value['variants'] as $varaint_value)
                {
                    $variant_name = $varaint_value['variant_name'];
                    $variant_type_name = $varaint_value['variant_type_name'];
                    $variant_type_id = $varaint_value['variant_type_id'];
                    $variant_type_price = $varaint_value['variant_price'];

                    //$variant_name_html = ' <p>'.$variant_name.'<br> <strong>';
                    $item_list .= '<p> - ' . $variant_type_name . '</strong><br>&nbsp; S$<span class="">' . $variant_type_price . '</span>  </p>'; //variant_type_id_'.$variant_type_id.'
                    $total_of_variant_price = $total_of_variant_price + $variant_type_price;
                }
            }
            else
            {
                $total_of_variant_price = 0;
            }

            $quantity_total = ($product_unit_price + $total_of_variant_price) * $product_quantity;

            $items_sub_total = $quantity_total + $items_total;
            $items_total = number_format($items_sub_total, 2);

            $item_list .= '</td>
                                 <td>
                                    ' . $product_quantity . '
                                 </td>
                                 <td class="cart-price">S$ <span>' . $quantity_total . '</span></td>
                            </tr>';

            $total_item_quantity++;
        }
    }

}
else
{
    redirect(base_url('admin/errors_404'));
    $item_list = '<tr> No item availabel</tr>';
}

//For Show Category list  accroding to restaurant id(from order table) for select produc --------START--------
$select_product_list = "";
if (isset($product_data) && $product_data != "" && !empty($product_data))
{

    foreach ($product_data as $value)
    {
        $product_id = trim($value['product_id']);
        $product_name = $value['product_name'];
        $offer_price = $value['offer_price'];

        if($value['offer_price'] != 0){
            $price = $value['offer_price'];
        }else{
            $price = $value['price'];
        }

        if (in_array($product_id, $orderd_product))
        {
            $checkbox_checked = 'checked="checked"'; // select which orderd product belong to this cateogry
            $selected_product_status = 0; // selected for order and gona to unselect
            
        }
        else
        {
            $checkbox_checked = "";
            $selected_product_status = 1; // not selected for order but gona to select
            
        }

        $select_product_list .= '<label class="enabled-label">' . $product_name . '
                                      <input type="checkbox" class="select_product_id_for_order" id="product_id_' . $product_id . '" name="product_id" value="' . $product_id . '" selected_product_status= "' . $selected_product_status . '" ' . $checkbox_checked . '  unit_price = "' . $price . '">
                                         <span class="checkmark_check"></span>
                                   </label>';
    }
}
else
{
    $select_product_list = 'No Category available';

}
//For Show Category list accroding to restaurant id for select product---------------END------------

?>

<?php
//check if any promo code applied on sub total
if (!empty($promo_code_if_applied_on_subtotal) && isset($promo_code_if_applied_on_subtotal))
{

    $promo_code_on_total_name = $promo_code_if_applied_on_subtotal[0]['code_name'];
    $promo_code_on_total_discount_value = $promo_code_if_applied_on_subtotal[0]['discount_value'];
    $promo_code_discount_type = $promo_code_if_applied_on_subtotal[0]['promo_type']; //1 - Flat, 2 - Percent
    //For view only-----------START-----------
    $promo_subtotal_discounted_value = 'S$ - ' . $single_order_data[0]['promo_subtotal_discounted_value']; //From order table
    //For view only-----------END-----------
    //For checkout section -----------------START-----------
    $min_value_of_discount_apply_on_total = $promo_code_if_applied_on_subtotal[0]['min_value'];

    //Check items_total amount is equal or grater , if it is less then promo code will not ne apply
    if ($items_total >= number_format($min_value_of_discount_apply_on_total, 2))
    {

        if ($promo_code_discount_type == 1 && $items_total >= number_format($promo_code_on_total_discount_value, 2))
        {

            $items_total_after_promo_code_applied = number_format($promo_code_on_total_discount_value);

        }
        else if ($promo_code_discount_type == 2)
        {

            $items_total_after_promo_code_applied = $items_total * number_format($promo_code_on_total_discount_value) / 100;

        }
        else
        {
            $items_total_after_promo_code_applied = 0;
        }

    }
    else
    {
        $items_total_after_promo_code_applied = 0;
    }
    $min_value_on_total_hint = "(Minimum item total should be -" . $min_value_of_discount_apply_on_total . ")";
    //For checkout section ------------------END-------------
    
}
else
{
    $promo_code_on_total_name = " - ";
    $promo_code_on_total_discount_value = " - ";
    $min_value_of_discount_apply_on_total = " - ";

    $promo_subtotal_discounted_value = " - ";

    $items_total_after_promo_code_applied = 0;

    $min_value_on_total_hint = "";
}

// if delivery address changed by admin or restaurant then delivery charge also will be change for checkout page only
if ($checkout_status_by_admin == 1 && $admin_checkout_delivery_charge_if_change != '0.00')
{
    $actual_delivery_charge = $admin_checkout_delivery_charge_if_change;
}
else
{
    //$actual_delivery_charge = $dc_amount;
    $actual_delivery_charge = $actual_dc_amount;
}

//echo $actual_delivery_charge;

//check if any promo code applied on delivery charges
if (!empty($promo_code_if_applied_on_delivery) && isset($promo_code_if_applied_on_delivery))
{
    
    $promo_code_on_delivery_name = $promo_code_if_applied_on_delivery[0]['code_name'];
    $promo_code_on_delivery_discount_value = $promo_code_if_applied_on_delivery[0]['discount_value'];
    $promo_code_delivery_discount_type = $promo_code_if_applied_on_delivery[0]['promo_type'];

    $promo_dc_discounted_value = 'S$ - ' . $single_order_data[0]['promo_dc_discounted_value']; //From order table
    //For checkout section -----------------START-----------
    $min_value_of_discount_apply_on_delivery = $promo_code_if_applied_on_delivery[0]['min_value'];

    //Check items_total amount is equal or grater , if it is less then promo code will not ne apply


    if ($items_total >= number_format($min_value_of_discount_apply_on_delivery, 2))
    {
        
        if ($promo_code_delivery_discount_type == 1 && $actual_delivery_charge >= number_format($promo_code_on_delivery_discount_value, 2))
        {
            
            $delivery_total_after_promo_code_applied = $actual_delivery_charge - number_format($promo_code_on_delivery_discount_value, 2);

        }
        else if ($promo_code_delivery_discount_type == 2) # PERCENT
        {
                // 12.50 * 40/100 = 5
                // 12.50- 5 = 7.5
            $delivery_total_after_promo_code_applied = $actual_delivery_charge * number_format($promo_code_on_delivery_discount_value, 2) / 100;
            $delivery_total_after_promo_code_applied = $actual_delivery_charge - $delivery_total_after_promo_code_applied;
        }
        else
        {
            $delivery_total_after_promo_code_applied = 0;
        }
    }
    else
    {
        
        $delivery_total_after_promo_code_applied = 0;
    }
    if($min_value_of_discount_apply_on_delivery > 0){
        $min_value_on_total_hint_dc = "(Minimum item total should be - " . $min_value_of_discount_apply_on_delivery . ")";
    }else{
        $min_value_on_total_hint_dc = "";
    }
    
    $min_value_on_dc_available = 1;
}
else
{
    $promo_code_on_delivery_name = " - ";
    $promo_code_on_delivery_discount_value = " - ";
    $min_value_of_discount_apply_on_delivery = " - ";

    $promo_dc_discounted_value = " - ";

    $delivery_total_after_promo_code_applied = 0;
    $min_value_on_total_hint_dc = "";
    $min_value_on_dc_available = 0;
}

//check if promo code applied on self pick up -------START------------
#If promo code applied on self pick up then  delivery charge will not be apply
if ($order_type == 2)
{ // selef pick up
    $actual_delivery_charge = 0;
    $actual_dc_amount = 0;
    $delivery_total_after_promo_code_applied = 0;
}

//check if promo code applied on self pick up -------End------------
//Checkout section ----------------Start----------------
//checking max discount value for items total
$max_item_discount_will_show = '';
if (!empty($promo_code_if_applied_on_subtotal) && isset($promo_code_if_applied_on_subtotal))
{
    
    $max_discount_value = number_format($promo_code_if_applied_on_subtotal[0]['max_discount'], 2);
    // if max discount value is exist and its is less then calculated discount value then max discount value will be apply
    # if calculated discount value is less then max discount value then calculated value will be apply
    if ($max_discount_value > '0' || $max_discount_value > '0.00')
    {
        if ($max_discount_value < $items_total_after_promo_code_applied)
        {
            $discount_value_for_apply = $max_discount_value;
            $max_item_discount_will_show = '<br> (Maximum item discount - S$  '.$discount_value_for_apply.')';
        }
        else if ($max_discount_value >= $items_total_after_promo_code_applied)
        {
            $discount_value_for_apply = $items_total_after_promo_code_applied;
        }
    }
    else
    {
        $discount_value_for_apply = $items_total_after_promo_code_applied;
    }
    $is_discount_applied_on_item = 1;

}
else
{
    $discount_value_for_apply = 0;
    $is_discount_applied_on_item = 0;
}

// echo "<br>dc after promo applied is ".$delivery_total_after_promo_code_applied;

//checking max delivery discount value
$max_delivery_discount_will_show = "";
if (!empty($promo_code_if_applied_on_delivery) && isset($promo_code_if_applied_on_delivery))
{

     $max_delivery_discount_value = number_format($promo_code_if_applied_on_delivery[0]['max_delivery_discount'], 2);
    // echo "<br>max_delivery_discount_value ".$max_delivery_discount_value;
   
    // if max discount value is exist and its is less then calculated discount value then max discount value will be apply
    # if calculated discount value is less then max discount value then calculated value will be apply
    if ($max_delivery_discount_value > '0' || $max_delivery_discount_value > '0.00')
    {
        if ($max_delivery_discount_value < $delivery_total_after_promo_code_applied)
        {
            $max_delivery_discount_apply = $max_delivery_discount_value;
            $max_delivery_discount_will_show = '<br> (Maximum delivery charge - S$  '.$max_delivery_discount_apply.')';
        }
        else if ($max_delivery_discount_value > $delivery_total_after_promo_code_applied)
        {
            $max_delivery_discount_apply = $delivery_total_after_promo_code_applied;
            $max_delivery_discount_will_show = '';
        }
    }
    else
    {
        $max_delivery_discount_apply = $delivery_total_after_promo_code_applied;
    }
    $is_discount_applied_on_dc = 1;
    
}
else
{
    $is_discount_applied_on_dc = 0;
    $max_delivery_discount_apply = 0;
}

// echo "FINAL ".$max_delivery_discount_apply;

//echo $items_total.'-'.$discount_value_for_apply;
$subtotal_of_items_checkout = $items_total - $discount_value_for_apply;

if ($max_delivery_discount_apply >0)#if delivery disount value is available
{
    $subtotal_of_delivery = $max_delivery_discount_apply;
    
}else{
    $subtotal_of_delivery = $actual_delivery_charge;
}
 
// echo "<br>subtotal_of_delivery".$subtotal_of_delivery;

/*if($dc_amount > $subtotal_of_delivery){
     $subtotal_of_delivery_checkout = number_format($dc_amount)-$subtotal_of_delivery;// new delivery charge minus paided delivery charge
   }else{
     $subtotal_of_delivery_checkout = $subtotal_of_delivery-number_format($dc_amount);// new delivery charge minus paided delivery charge
   }*/
if($is_discount_applied_on_dc == 0){
    $discount_applied_on_dc = 0;
    $discount_applied_on_dc_updated = 0;
}else{
    //$discount_applied_on_dc = $subtotal_of_delivery;
    $discount_applied_on_dc = $delivery_total_after_promo_code_applied;
    $discount_applied_on_dc_updated = $delivery_total_after_promo_code_applied;
}

#CHSDC
$is_delivery_address_changed = $single_order_data[0]['is_delivery_address_changed'];
if($is_delivery_address_changed == 1)
{
    $discount_applied_on_dc_updated = $dc_amount;
}

if($is_discount_applied_on_item == 0){
    $discount_value_for_apply_for_show = 0;
}else{
    $discount_value_for_apply_for_show = $items_total_after_promo_code_applied;
}

// echo "<br>actual_delivery_charge".$actual_delivery_charge;

//echo 'hhhhhhhhhh'.$delivery_total_after_promo_code_applied;
//echo 'ghgfh'.$actual_delivery_charge.'-'.$subtotal_of_delivery;

# WHY WE SUBTRACTED MAX DC FROM ORIGINAL LALAMOVE CHARGE IN BELOW LINE??????
# $subtotal_of_delivery_checkout = number_format($actual_delivery_charge,2,'.','') - number_format($subtotal_of_delivery,2,'.','');

$subtotal_of_delivery_checkout = number_format($subtotal_of_delivery,2,'.','');

$grand_total_of_checkout = number_format($subtotal_of_items_checkout,2,'.','') + number_format($subtotal_of_delivery_checkout,2,'.','');

//outstanding amount checking -------------START---------------------
if ($grand_total_amount > $grand_total_of_checkout)
{
     // echo "<br>QQQQQQq";
    // if prevouse total amount is grater then current total after customize
    #Restaurant will pay to customer
    $outstanding_amount_after_customize = $grand_total_amount - $grand_total_of_checkout;
    // $outstanding_amount_after_customize = $remaining_outstanding_amount - $outstanding_amount_after_customize;
    $outstanding_amount_after_customize = $outstanding_amount_after_customize - $remaining_outstanding_amount;
    $who_will_pay_outstanding_amount = 2; //role - Restaurant
    
}
else if ($grand_total_of_checkout > $grand_total_amount)
{
    // echo "WWWWWWWWWw";
    // if prevouse total amount is less then  current total after customize
    #Customer will pay to Restaurant
    $outstanding_amount_after_customize = $grand_total_of_checkout - $grand_total_amount;
    $outstanding_amount_after_customize = $remaining_outstanding_amount + $outstanding_amount_after_customize;
    $who_will_pay_outstanding_amount = 3; //role - customer
}
else
{
    // echo "UUUUUUU";
    $outstanding_amount_after_customize = "";
    $who_will_pay_outstanding_amount = "";
}
//echo  '==='.$outstanding_amount_after_customize.'=='.$remaining_outstanding_amount;
 

// echo "grand_total_of_checkout".$grand_total_of_checkout;
// echo "<br>grand_total_of_checkout".$grand_total_of_checkout;

//outstanding amount checking -------------End---------------------
//Checkout section ----------------End----------------

?>

<!-- Main Content -->
<div class="main-content">
   <section class="section" >
      <div class="section-header">
         <h1>Order Single</h1>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Order Single</div>
         </div>
      </div>

      <div id="print_section">
      <div class="order-ids-filter">
         <div class="row">
            <div class="form-group col-md-4">
               <h2>Orders Id : <?php echo $order_number_id; ?></h2>
            </div>
            <div class="form-group col-md-3">
               <h2>Amount : S$<?php echo $grand_total_amount; ?></h2>
            </div>
            <div class="form-group col-md-3 order-single-status">
               <label>Status</label>
                 <select class="custom-select  wv_filter_box_height form-control order_status"  name="order_status_<?php echo $order_id; ?>" order_single_status="1">
                     <option value="0" <?php if ($order_status == '0' && $order_status != 'all')
{
    echo "selected";
} ?>>Pending</option>
                     <option value="1" <?php if ($order_status == '1' && $order_status != 'all')
{
    echo "selected";
} ?>>Accepted</option>
                     <option value="2" <?php if ($order_status == '2' && $order_status != 'all')
{
    echo "selected";
} ?>>Rejected</option>
                     <option value="3" <?php if ($order_status == '3' && $order_status != 'all')
{
    echo "selected";
} ?>>Dispatched</option>
                     <option value="4" <?php if ($order_status == '4' && $order_status != 'all')
{
    echo "selected";
} ?>>Cancelled</option>
                     <option value="5" <?php if ($order_status == '5' && $order_status != 'all')
{
    echo "selected";
} ?>>Completed</option>
                      <option value="6" <?php if ($order_status == '6' && $order_status != 'all')
{
    echo "selected";
} ?>>Preparing</option>
                      <option value="7" <?php if ($order_status == '7' && $order_status != 'all')
{
    echo "selected";
} ?>>Ready</option>
                  </select>
            </div>
            <div class="form-group col-md-2 order-list-icons">
               
               <a id="order_invoice" style="cursor: pointer;" onclick="window.open('<?php echo base_url('admin/Order_Invoice/' . $order_id); ?>')"><i class="fas fa-print"></i></a> 
            </div>
         </div>
      </div>
      <div class="single-orders-section">
         <div class="row">
            <div class="col-md-6">
               <div class="row customer-orders">
                  <div class="col-md-6">
                     <div class="customer-details">
                        <h2>Customer Details</h2>
                        <div class="details-sec">
                           <p>Customer Name</p>
                           <h4> <a href="<?php echo base_url('admin/user_details/3/' . $customer_id . ''); ?>" class="linked_colr_a"><?php echo $customer_name; ?></a></h4>
                            <p>Delivery Name</p>
                            <h4><?php echo $delivery_name; ?></h4>
                           <p>Delivery Phone no.</p>
                           <h4><?php echo $delivery_mobile; ?></h4>
                           <p>Delivery Email</p>
                           <h4><?php echo $delivery_email; ?></h4>
                           <p>Delivery Address   
                            <?php if ($order_status == 0)
{ ?><button class="btn" type="button" id="edit_customer_order_address"><i class="fas fa-pencil-alt" style="color: #F04370;"></i></button></p><?php
} ?>
                           <h4 id="updated_address"><?php echo $delivery_address . ',   '.$delivery_street_address.', '.$delivery_postal_code . ', ' . $delivery_unit_number; ?></h4>
                           <p>Promo Code Applied On Sub Total</p>
                           <h4><?php echo $promo_code_on_total_name; ?></h4>
                           <p>Promo Code Applied On Delivery</p>
                           <h4><?php echo $promo_code_on_delivery_name; ?></h4>
                           <p>Customer Rating</p>
                           <h4>NA</h4>
                           <p>Customer Review</p>
                           <h4>-</h4>
                          <p>Customer Wallet Amount</p>
                           <h4><?php echo "S$ ".$wallet_balance; ?></h4>
                           <p>Ordering App</p>
                           <h4><?php echo $ordering_platform; ?></h4>
                           <p>Lalamove Delivery Charge (Original)</p>
                           <h4><?php echo "S$ ".$lalamove_original_dc; ?></h4>
                           <p>Amount paid from <?= $payment_mode?></p>
                           <h4><?php echo "S$ ".$total_amount_paid_through_gateway; ?></h4>
                           <p>Wallet debited value</p>
                           <h4><?php echo "S$ ".$wallet_debited_value; ?></h4>
                           <p>Is address changed?</p>
                           <h4><?php if($single_order_data[0]['is_delivery_address_changed'] == 1 ){echo "Yes";}else{echo "No";} ?></h4>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="customer-details">
                        <h2>Order Summary</h2>
                        <div class="details-sec">
                          <p>Restaurant Name</p>
                           <h4> <a href="<?php echo base_url('admin/add_edit_restaurant/2/' . $restaurant_id . '/' . $res_admin_id . ''); ?>" class="linked_colr_a"><?php echo stripslashes($restaurant_name); ?></a></h4>
                           <p>Delivery Mode</p>
                           <h4><?php echo $order_type_name; ?></h4>
                           <p>Payment Method</p>
                           <h4><?php echo $payment_mode; ?></h4>
                            <p>Transaction ID</p>
                             <h4 class="order_track_link_break"><?php echo $order_transaction_id; ?></h4>
                           <p>Outstanding Amount</p>
                           <h4 id="updated_outstanding_amount">S$ <?php echo $outstanding_amount; ?> 
                           <?php
if ($outstanding_amount > 0)
{ ?> <button class="btn" type="button" data-toggle="modal" data-target="#edit_outstanding_amount" data-backdrop="static" data-keyboard="false"><i class="fas fa-pencil-alt" style="color: #F04370;"></i></button> 
                        <?php
} ?>
                            <br><sapn style="font-size: 12px" class="text-danger"><?php echo $who_will_pay_outstanding; ?></sapn></h4>
                           <p>Is Paid Outstanding Amount</p>
                           <h4> <?php echo $is_paid_outstanding_amount; ?></h4>
                           <p>Order Time</p>
                           <h4><?php echo $order_place_time_date; ?></h4>
                           <p>Order Preparation Time (In Minutes) <?php if ($order_status == 0)
{ ?> <button class="btn" type="button"data-toggle="modal" data-target="#edit_order_preparation_time_after_accept" data-backdrop="static" data-keyboard="false"><i class="fas fa-pencil-alt" style="color: #F04370;"></i></button> <?php
} ?> </p>
                           <h4 id="updated_preparation_time"> <?php if ($preparation_time_when_accepted != "")
{
    echo $preparation_time_when_accepted;
}
else
{
    echo "-";
} ?></h4>
                          <!--  <div class="<?php //echo $schedule_edit_mode;
 ?>">
                           <p>Schedule Time <button class="btn" type="button"data-toggle="modal" data-target="#edit_schedule_time" data-backdrop="static" data-keyboard="false"><i class="fas fa-pencil-alt" style="color: #F04370;"></i></button></p>
                           <h4 id="updated_schedule_time"><?php //echo $final_pickup_date_from.' '. $final_pickup_range;
 ?></h4>
                          </div> -->

                           <div>
                           <p>Schedule Time</p>
                           <h4 id="updated_schedule_time"><?php echo $schedule_time; ?></h4>
                          </div>
                        
                           <p>Pick-up Time   <?php if ($order_status == 0 ) // <p>Pick-up Time   <?php if ($order_status == 0 && ($order_type == 2 || $order_type == 3)) This condition is now removed as client wants to edit pickup time of order now type orders also.
{ ?><button class="btn" type="button"data-toggle="modal" data-target="#edit_pickup_time_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-pencil-alt" style="color: #F04370;"></i></button></p><?php
} ?>
                           <h4 id="updated_pickup_time"><?php echo $final_pickup_date_from . ' ' . $final_pickup_range; ?></h4>
                            <p>Do you need Cutlery?</p>
                           <h4><?php echo $is_cutlery_needed; ?></h4>
                           <p>Special Instruction<button class="btn" type="button"data-toggle="modal" data-target="#edit_order_remark_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-pencil-alt" style="color: #F04370;"></i></button></p>
                           <h4><?php echo $remark; ?></h4>
                            <p>Tracking Link <button class="btn" type="button"data-toggle="modal" data-target="#edit_order_tracking_link_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-pencil-alt" style="color: #F04370;"></i></button></p>
                            <h4 class="order_track_link_break"> <?php if ($track_link != "")
{
    echo $track_link;
}
else
{
    echo "-";
} ?></h4>
                            <p>Lalamove Order Id</p>
                            <h4> <?php echo $lalamove_order_id; ?></h4>
                             <p>Lalamove Order Status</p>
                            <h4> <?php echo $lalamove_order_status_val; ?></h4>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-6 cartdetail_part">
               <div class="cart-items">
                  <table class="table">
                     <thead id="ingnore_print_time">
                        <tr>
                           <th colspan="5">
                              <h2>Item Detail</h2>
                           </th>
                           <!-- <th class="text-right" >
                              <h2>Remark</h2>
                           </th> -->
                           <?php if ($order_status == 0 || $order_status == 1 || $order_status == 6 || $order_status == 7)
{ ?>

                             <th class="text-right" colspan="2"><button  class="add-item-btns add_remove_orderd_items_modal_btn" style="cursor: pointer;" data-toggle="modal" data-target="#add_remove_orderd_items_modal" data-backdrop="static" data-keyboard="false">Customize Item</button></th>
                          <?php
} ?>
                        </tr>
                     </thead>
                     <tbody  class="cart-item-details">
                         <tr>
                              <th>Product Image</th>
                              <th colspan="4"> Product Detail</th>
                              <th>Quantity</th>
                              <th>Price</th>
                          </tr>
                         <?php echo $item_list; ?>
                     </tbody>
                  </table>
               </div>
               <div class="total-price">
                  <div class="delivery-discount-price">Item Total <span>S$ <?php echo $sub_total; ?></span></div>
                  <div class="delivery-discount-price">Discount Applied On Item Total<span>- S$ <?php //echo $actual_promo_subtotal_discounted_value; 
                  echo $discount_value_for_apply_for_show ?></span><?php echo $max_item_discount_will_show;?></div>
                  <div class="delivery-discount-price">Delivery Charges  <span> S$ <?php echo number_format($actual_delivery_charge,2,'.','');?></span></div>
                  <div class="delivery-discount-price">Discount Applied On Delivery Charges <span>+ S$ <?php echo number_format($discount_applied_on_dc_updated,2,'.',''); ?></span> <?php echo $max_delivery_discount_will_show;?> </div>
                  <div class="grand-total">Grand Total <span>S$ <?php echo $grand_total_amount; ?></span></div>
               </div>
            </div>
         </div>
      </div>
      </div>
   </section>

  <!----delivery address change section  ----START------>
   <br>  <div id="locationField" class="p-5 shadow p-3 mb-5 bg-white rounded d-none"><!--style="position: absolute;
    top: 194px;
    z-index: 100000;
    width: 74%;"-->
        <div class="form-group  admin-input-field">
            <div class="row mr-1">
                <div class="col-sm-6">
                   <label>Street Address</label>
                    <input id="delivery_address" placeholder="Enter your address" type="text"  value="<?php echo $delivery_address; ?>"></input>
                     <span class="error" id="unfill_delivery_address"></span>

                      <div id="map" style="width: 100%; height: 250px;"></div>
                      <div id="infowindow-content">
                          <img src="" width="16" height="16" id="place-icon">
                          <span id="place-name" class="title"></span><br>
                          <span id="place-address"></span>
                      </div>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-12 pb-3">
                           <label>BLK /House /Apartment No.</label>
                            <input class="field" id="delivery_street_address" name="delivery_street_address"  value="<?php echo $delivery_street_address; ?>"></input>
                             <span class="error" id="unfill_street_address"></span>
                       </div>

                       <div class="col-sm-6">
                           <label>Postal Code</label>
                            <input class="field" id="postal_code" name="postal_code"  value="<?php echo $delivery_postal_code; ?>"></input>
                             <span class="error" id="unfill_postal_code"></span>
                       </div>
                        <div class="col-sm-6">
                          <label>Unit Number</label>
                          <input class="field" id="unit_number"  name="unit_number" value="<?php echo $delivery_unit_number; ?>"></input>
                       </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
         <input id="delivery_latitude" type="hidden"  value="<?php echo $delivery_latitude; ?>"></input>
        <input id="delivery_longitude"  type="hidden"  value="<?php echo $delivery_longitude; ?>"></input>
        <input type="hidden" class="btn btn-primary add-item-btns" value="<?php echo $delivery_address;?>" id="old_delivery_address"/>
        <button type="button" class="btn btn-primary add-item-btns" id="get_lat_long_by_address">Save</button>
      
        <button type="button" class="btn btn-primary d-none" id="updated_order_address">Save</button>
        <button type="button" class="btn btn-secondary modal_btns" style="margin-top: -59px;" id="close_address_popup">Close</button>
       
         <div class="d-none">
            <tr>
                <td class="label">Street address</td>
                <td class="slimField"><input class="field" id="street_number" disabled="true" name="streetnumber"></input>
                </td>
                <td class="wideField" colspan="2"><input class="field" id="route" disabled="true" name="route"></input>
                </td>
            </tr>
            <tr>
                <td class="label">City</td>
                <!-- Note: Selection of address components in this example is typical.
             You may need to adjust it for the locations relevant to your app. See
             https://developers.google.com/maps/documentation/javascript/examples/places-autocomplete-addressform    
             -->
                <td class="wideField" colspan="3"><input class="field" id="locality" disabled="true" name="locality"></input>
                </td>
            </tr>
            <tr>
                <td class="label">State</td>
                <td class="slimField"><input class="field" id="administrative_area_level_1" disabled="true" name="state"></input>
                </td>
                <td class="label">Zip code</td>
                <td class="wideField">
                </td>
            </tr>
            <tr>
                <td class="label">Country</td>
                <td class="wideField" colspan="3"><input class="field" id="country" disabled="true" name="country"></input>
                </td>
            </tr>
          <div>
        </div>
        <div>
        </div>
      </div>
    </div>
    <!----delivery address change section  ----END------>
<div>
  
 

<!-- MODAL for update Order Preparation Time -------------START------------->
  
<div class="modal fade" id="edit_order_preparation_time_after_accept">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
       <h5 class="modal-title" id="exampleModalLabel">Update Order Preparation Time</h5>
        <button type="button" class="close close_btnn" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <form>
              <div class="form-group  admin-input-field">
                <div class="row mr-1 ml-1">
                    <label> Order Preparation Time (In Minutes)</label>
                    <input type="text" maxlength="3" placeholder="EX. 20, 80,120 etc."  value="<?php echo $preparation_time_when_accepted; ?>" class="check_space" name="edit_order_preparation_time"/>
                    <span class="error" id="unfill_order_preparation_time"></span>
                </div>
              </div>
          </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
           <button type="button" class="add-item-btns" id="edit_order_preparation_time_submit" style="margin-bottom:0!important; cursor: pointer; ">Save</button>
          <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal" aria-label="Close">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- MODAL for  update Order Preparation Time-------------END------------->

<!-- MODAL for update Schedule Time-------------START------------->
  
<div class="modal fade" id="edit_pickup_time_modal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
       <h5 class="modal-title" id="exampleModalLabel">Update Pick-up Time</h5>
        <button type="button" class="close close_btnn" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <form>
              <div class="form-group  admin-input-field">
                <div class="row mr-1 ml-1">
                    <div class="col-md-12">
                          <label> From </label>
                          <input type="text" id="pick_up_from" name="edit_pick_up_from" min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" value="<?php echo $final_pickup_date_from . ' ' . $final_pickup_range; ?>"  required="" class="check_space date_valid">
                            &nbsp;<span class="error" id="unfill_pick_up_from"></span>
                    </div>
                    <input type="hidden" id="delivery_time" value="<?php echo $rest_delivery_time; ?>"/>
                    <input type="hidden" id="order_type" value="<?php echo $order_type; ?>"/>
                    <input type="hidden" id="final_pickup_range" value="<?php echo $final_pickup_range; ?>"/>
                    <input type="hidden" id="final_pickup_date_from" value="<?php echo $final_pickup_date_from; ?>"/>
                    <input type="hidden" id="pick_up_to" name="edit_pick_up_to" min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" value="<?php echo $final_pickup_date_to; ?>"  required="" class="check_space date_valid">

                     <!-- <div class="col-md-6">
                          <label> To </label>
                    <input type="text" id="pick_up_to" name="edit_pick_up_to" min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" value="<?php //echo $final_pickup_time_to;
 ?>"  required="" class="check_space date_valid">
                      &nbsp;<span class="error" id="unfill_pick_up_to"></span>
                    </div> -->
                </div>
              </div>
          </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="add-item-btns" id="edit_pick_up_time_submit" style="margin-bottom:0!important; cursor: pointer; ">Save</button>
          <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal" aria-label="Close">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- MODAL for  update Schedule Time------------END------------->

<!-- MODAL for update Schedule Time-------------START------------->
  
<div class="modal fade" id="edit_schedule_time">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
       <h5 class="modal-title" id="exampleModalLabel">Update Schedule Time</h5>
        <button type="button" class="close close_btnn" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <form>
              <div class="form-group  admin-input-field">
                <div class="row mr-1 ml-1">
                    <label> Order Schedule Time Time </label>
                    <input type="text" id="order_schedule_time" name="edit_order_schedule_time" min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" value="<?php echo $order_place_time_date; ?>"  required="" class="check_space date_valid">
                      &nbsp;<span class="error" id="unfill_schedule_time"></span>
                </div>
              </div>
          </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="add-item-btns" id="edit_order_schedule_time_submit" style="margin-bottom:0!important; cursor: pointer; ">Save</button>
          <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal" aria-label="Close">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- MODAL for  update Schedule Time------------END------------->

<!-- MODAL for update Order Track Link -------------START------------->
<div class="modal fade" id="edit_order_tracking_link_modal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
       <h5 class="modal-title" id="exampleModalLabel">Update Track Link</h5>
        <button type="button" class="close close_btnn" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
          <form>
              <div class="form-group  admin-input-field">
                <div class="row mr-1 ml-1">
                    <label> Lalamove Order Id</label>
                    <input type="text" id="lalamove_order_id" name=""  autocomplete="off"  value="<?php echo $lalamove_order_id; ?>"  required="" class="check_space">
                    <span class="error" id="unfill_lalamove_order_id"></span>
                </div><br>
                <div class="row mr-1 ml-1">
                    <label>Track Link </label>
                     <textarea class="check_space" id="edit_track_link" name="edit_track_link"><?php echo $track_link; ?></textarea>
                    <span class="error" id="unfill_track_link"></span>
                </div>
              </div>
          </form>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
           <button type="button" class="add-item-btns" id="edit_order_track_link_submit" style="margin-bottom:0!important; cursor: pointer; ">Save</button>
          <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal" aria-label="Close">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- MODAL for  update OrderTrack Link------------END------------->

<!-- MODAL for update Order remark -------------START------------->
  
<div class="modal fade" id="edit_order_remark_modal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
       <h5 class="modal-title" id="exampleModalLabel">Update Special Instruction</h5>
        <button type="button" class="close close_btnn" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <form>
              <div class="form-group  admin-input-field">
                <div class="row mr-1 ml-1">
                    <label> Instruction</label>
                     <textarea class="check_space" id="edit_remark" name="edit_remark"><?php if ($remark != "NA")
{
    echo $remark;
} ?></textarea>
                    <span class="error" id="unfill_remark"></span>
                </div>
              </div>
          </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
           <button type="button" class="add-item-btns" id="edit_remark_submit" style="margin-bottom:0!important; cursor: pointer; ">Save</button>
          <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal" aria-label="Close">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- MODAL for  update OrderTrack Link------------END------------->

<!--Modal for add and remove orderd items --------------  START--------------------->
<div class="modal fade" id="add_remove_orderd_items_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"><!-- id fill add by class on click-->
   <div class="modal-dialog modal-lg read_more_popup" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Customize Item</h5>
            <button type="button" class="close close_btnn delete_temp_order_data">  <span aria-hidden="true">&times;</span> </button>
            <button type="button" class="close d-none" id="close_customoize_modal" data-dismiss="modal" aria-label="Close">
            </button>
         </div>

        <form method="POST">
           <div class="modal-body" id="count_category_select_box">
              <div class="form-group admin-input-field"><!--order_add_item-->
                 <div class="row">
                    <div class="col-md-12">
                        <label>Select Product</label>
                            <div id="for_select_category_order" > 
                             <div id="select_variant">
                                 <div class="select_dropdown">
                                   <input type="text" onclick="SearchDropdownFunction('SelectCategoryOrderDropdown')"  placeholder="Search Product......" id="SelectCateoryOrderInput" onkeyup="filterFunction('SelectCategoryOrderDropdown','SelectCateoryOrderInput')">
                                   <div id="SelectCategoryOrderDropdown" class="select_dropdown-content show">
                                       <?php echo $select_product_list; ?>
                                   </div>
                                 </div>
                            </div>
                       </div>
                     </div>
                 </div>
              </div>
               <div class="cart-items table-flip-scroll">
                  <div class=" text-center" id="show_loader_cancel">
                     <img src="<?php echo base_url() . 'assets/images/preview-chat-loader-2.gif'; ?>" width="150"/>
                  </div>
                  <table class="table" id="selected_products_for_order">
                     <?php $this
    ->load
    ->view('order_selected_products_with_if_any_varaints'); ?>
                  </table>
               </div>
               <div class="text-center">
                   <button class="add-item-btns d-none" type="button" id="confirm_order_place_order" style="cursor: pointer;">Confirm</button><!--by default it will be hide after data loaded it will be show-->
               </div>
           </div>
           <div class="modal-footer">
              <input type="hidden" value="" id="customer_id"/>
              <button type="button"  class="btn btn-secondary modal_btns delete_temp_order_data">Close</button>
           </div>
        </form>
      </div>
   </div>
</div>
<!--Modal for add and remove orderd items --------------  END--------------------->

<!-- MODAL for Select vairants -------------START------------->
    <div class="modal" id="select_variant_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered read_more_popup" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title product_modal_title" id="exampleModalLabel">Select variant</h5>
                    <!--  <button type="button" class="close close_btnn close_upper_variant_modal">
                        <span aria-hidden="true">&times;</span>
                    </button>  -->
                </div>
                <form>
                    <div class="modal-body">
                         <table class="table" id="select_product_variants_for_order">
                           <?php $this
    ->load
    ->view('order_select_product_variant'); ?>
                          </table>
                    </div>
                    <div class="modal-footer">
                          <button type="button" class="add-item-btns" id="check_mendetory_variant" check_mendatory_is_checked="2">Done</button>
                         <button type="button" class="btn btn-secondary modal_btns d-none close_upper_variant_modal" >Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- MODAL for Select vairants -------------END------------->

<!--Modal For Checkout ---------------------------START-------------->
    <div class="modal" id="checkout_customised_item_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"> 
        <div class="modal-dialog modal-lg modal-dialog-centered read_more_popup" role="document">
            <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title product_modal_title" id="exampleModalLabel">Checkout</h5>
                </div>
                <form>
                    <div class="modal-body">
                       <div id="checkout_items" class="row">
                           <div class="col-md-12 cartdetail_part">
                               <div class="cart-items table-flip-scroll">
                                  <table class="table">
                                     <tbody  class="cart-item-details">
                                         <tr>
                                              <th>Product Image</th>
                                              <th colspan="4"> Product Detail</th>
                                              <th>Quantity</th>
                                              <th>Price</th>
                                          </tr>
                                         <?php echo $item_list; ?>
                                     </tbody>
                                  </table>
                               </div>
                               <div class="total-price" id="checkout_total_price_calculation">
                                    <div class="delivery-discount-price">Item Total <span>S$ <?php echo $items_total; ?></span></div>
                                    <div class="delivery-discount-price">Discount <br> <?php echo $min_value_on_total_hint; ?>  <span> - <?php echo $discount_value_for_apply_for_show; ?></span><?php echo $max_item_discount_will_show;?></div>
                                    <div class="delivery-discount-price">Delivery Charge  <span>  S$ <?php echo $actual_delivery_charge; ?></span></div>
                                    <div class="delivery-discount-price">Discount  <br> <?php echo $min_value_on_total_hint_dc; ?><span> + <?php echo $discount_applied_on_dc; ?></span><?php echo $max_delivery_discount_will_show;?></div>
                                    <div class="grand-total">Grand Total <span>S$ <?php echo $grand_total_of_checkout; ?></span></div>
                               </div>
                            </div>
                       </div>
                        <br>
                         <div class="text-center">
                           <button class="add-item-btns" type="button" id="checkout_after_customize" style="cursor: pointer;">Checkout</button>
                       </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
 
<!--Modal For Checkout ---------------------------END-------------->
<!-- MODAL for update OUSTANDING AMOUNT  -------------START------------->
  
<div class="modal fade" id="edit_outstanding_amount">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
       <h5 class="modal-title" id="exampleModalLabel">Update Outstanding Amount</h5>
        <button type="button" class="close close_btnn" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <form>
              <div class="form-group  admin-input-field">
                <div class="row mr-1 ml-1">
                    <label> Amount </label>
                    <span class="currency_popup">S$</span>
                    <input type="text" maxlength="3" placeholder="Outstanding Amount"  value="<?php echo $outstanding_amount; ?>" class="check_space maximum_value" name="edit_outstanding_amnt"/>
                    <span class="error" id="validate_outstanding_amount"></span>
                </div>
              </div>
          </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
           <button type="button" class="add-item-btns" id="edit_outstanding_amount_submit" style="margin-bottom:0!important; cursor: pointer; ">Save</button>
          <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal" aria-label="Close">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- MODAL for  update OUSTANDING AMOUNT -------------END------------->

<button  type="button"  data-toggle="modal" data-target="#checkout_customised_item_modal" id="checkout_modal_btn" data-backdrop="static" data-keyboard="false" class="d-none">Checkout modal button</button><!--D-none click by jquer after customise submit items-->
 
<script type="text/javascript">
   var ordered_restaurant_id = '<?php echo $restaurant_id; ?>';
   var order_id = '<?php echo $order_id; ?>';
   var order_number_id = '<?php echo $order_number_id; ?>';
   var  order_table_url = '<?php echo base_url('admin/order_single/'); ?>'+order_id;
   // for deivlery charge calculate  if address change--start----
   var order_type = '<?php echo $order_type; ?>';
   var delivery_handled_by = '<?php echo $delivery_handled_by; ?>';
   var delivery_mobile = '<?php echo $delivery_mobile; ?>';
   var restaurant_name = '<?php echo $restaurant_name; ?>';
   var delivery_name = '<?php echo $delivery_name; ?>';
   var pickup_time_from = '<?php echo $pickup_time_from; ?>';
   // for deivlery charge calculate if address change --end----
</script>
<?php
if ($checkout_status_by_admin == 1)
{
?>
     <script>
         var check_checkout_modal = '<?php echo $checkout_status_by_admin; ?>';
    </script>";
<?php
}
?>

<script type="text/javascript">
  //Checkout section ----------------Start----------------
  //var subtotal_of_items_checkout = '<?php echo $subtotal_of_items_checkout; ?>'; //after minus discount
  var subtotal_of_items_checkout = '<?php echo $items_total; ?>'; //acutal item totals
  var subtotal_of_delivery_checkout = '<?php echo $subtotal_of_delivery_checkout; ?>';
  var grand_total_of_checkout = '<?php echo $grand_total_of_checkout; ?>';
  var items_total_after_promo_code_applied = '<?php echo $discount_value_for_apply; ?>';
  var delivery_total_after_promo_code_applied = '<?php echo $delivery_total_after_promo_code_applied; ?>';
  var item_quantity = '<?php echo $total_item_quantity; ?>';
  var outstanding_amount_after_customize = '<?php echo $outstanding_amount_after_customize; ?>';
  var who_will_pay_outstanding_amount = '<?php echo $who_will_pay_outstanding_amount; ?>';
  var customer_id = '<?php echo $customer_id; ?>';
  //Checkout section ----------------End----------------
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="Stylesheet" type="text/css" />


<!---For time picker with date you need to only replace only datepickerv with datetimepicker---->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
 <!-- Javascript -->
  <script type="text/javascript">
      var $j3 = jQuery.noConflict();       
         $j3("#order_schedule_time").datetimepicker({//datetimepicker
          dateFormat: "dd/mm/yy",
           minDate: 0,
        });
  </script>

   <!-- Javascript -->
  <script type="text/javascript">
      var $j2 = jQuery.noConflict();       
         $j2("#pick_up_from").datetimepicker({//datetimepicker for time show with date
           dateFormat: "dd-mm-yy",
           //minDate: 0,
          onSelect: function (date) {
            /*  var dt2 = $j2('#pick_up_to');
              var startDate = $j2(this).datepicker('getDate');
              var minDate = $j2(this).datepicker('getDate');
              
              dt2.datepicker('option', 'minDate', minDate);*/
          }
        });
       /* $j2('#pick_up_to').datetimepicker({//datetimepicker
            dateFormat: "dd-mm-yy",
            minDate: 0
        });  */         
    
  </script>
<!--   <script src="https://maps.googleapis.com/maps/api/js?key= AIzaSyAZdfgNYje65H57hjkEiuMuF_gjuFmDwloZLmuIv-I&callback=initMap&libraries=&v=weekly"
      async
    ></script> -->
<script>
    var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
    };



    var input = document.getElementById('delivery_address');

    function initMap() {
        var geocoder;
        var autocomplete;

        geocoder = new google.maps.Geocoder();
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: -33.8688,
                lng: 151.2195
            },
            zoom: 13
        });
        var card = document.getElementById('locationField');
        autocomplete = new google.maps.places.Autocomplete(input);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete.bindTo('bounds', map);

        var infowindow = new google.maps.InfoWindow();
        var infowindowContent = document.getElementById('infowindow-content');
        infowindow.setContent(infowindowContent);
        var marker = new google.maps.Marker({
            map: map,
            anchorPoint: new google.maps.Point(0, -29),
            draggable: true
        });

        autocomplete.addListener('place_changed', function() {
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            console.log(place);

            if (!place.geometry) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                window.alert("No details available for input: '" + place.name + "'");
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17); // Why 17? Because it looks good.
            }
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

            var address = '';
            if (place.address_components) {
                address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }

            infowindowContent.children['place-icon'].src = place.icon;
            infowindowContent.children['place-name'].textContent = place.name;
            infowindowContent.children['place-address'].textContent = address;
            infowindow.open(map, marker);
            fillInAddress();

        });

        function fillInAddress(new_address) { // optional parameter
            if (typeof new_address == 'undefined') {
                var place = autocomplete.getPlace(input);
            } else {
                place = new_address;
            }
            //console.log(place);
            for (var component in componentForm) {
                document.getElementById(component).value = '';
                document.getElementById(component).disabled = false;
            }

            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value = val;
                }
            }
        }

        google.maps.event.addListener(marker, 'dragend', function() {
            geocoder.geocode({
                'latLng': marker.getPosition()
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        console.log(autocomplete);
                        $('#delivery_address').val(results[0].formatted_address);
                        $('#delivery_latitude').val(marker.getPosition().lat());
                        $('#delivery_longitude').val(marker.getPosition().lng());
                        infowindow.setContent(results[0].formatted_address);
                        infowindow.open(map, marker);
                        // google.maps.event.trigger(autocomplete, 'place_changed');
                        fillInAddress(results[0]);
                    }
                }
            });
        });
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAZNYje65H5kEiuMuF_gFmDwloZLmuIv-I&libraries=places&callback=initMap" async defer></script>