<?php
if (isset($order_data) && !empty($order_data)) {
    $order_number_id  =  $order_data[0]['order_number']; 
    $restaurant_name  =  $order_data[0]['rest_name'];  
    $rest_pin_address  =  $order_data[0]['rest_pin_address'];  
    $rest_postal_code  =  $order_data[0]['rest_postal_code'];  
    $rest_unit_number  =  $order_data[0]['rest_unit_number'];  
    $order_type_name  =  $order_data[0]['order_type_name']; 
    $order_type  =  $order_data[0]['order_type']; 
    $is_order_customized = $order_data[0]['is_order_customized'];
    if ($is_order_customized == 1)
    {
        $total_amount  =  $order_data[0]['total_amount']; 
    }
    else
    {
        $total_amount  =  $order_data[0]['total_amount_paid'] + $order_data[0]['wallet_debited_value']; 
    }
    
    
    $sub_total  =  $order_data[0]['sub_total']; 
    $dc_amount  =  $order_data[0]['dc_amount']; 

    $delivery_name  =  $order_data[0]['delivery_name'];  
    $delivery_email  =  $order_data[0]['delivery_email'];  
    $delivery_mobile  =  $order_data[0]['delivery_mobile'];  

    $delivery_address  =  $order_data[0]['delivery_address'];  
    $delivery_street_address  =  $order_data[0]['delivery_street_address'];  
    $delivery_postal_code  =  $order_data[0]['delivery_postal_code'];  
    $delivery_unit_number  =  $order_data[0]['delivery_unit_number'];  
    $order_place_time  =  $order_data[0]['created_at']; 
    $order_status  =  $order_data[0]['order_status']; 
    $special_instruction  =  $order_data[0]['remark']; 

    $rest_delivery_time  =  $order_data[0]['delivery_time'];  

    # If restaurant has not set any delivery time then we need to take value from settings table
   if($rest_delivery_time == 0 || $rest_delivery_time == '')
   {
        $basic_delv_time = $this->Common->getData('settings','value','name = "basic_delivery_time"');
        $rest_delivery_time = $basic_delv_time[0]['value'];
   }
    $preparation_time_when_ordered = $order_data[0]['preparation_time_when_ordered'];

    if($order_data[0]['preparation_time_when_accepted'] == ''){ // if order os pending 
    $preparation_time_when_accepted  =  $order_data[0]['preparation_time_when_ordered']; 
   }else{// if after  order accepted 
    $preparation_time_when_accepted  =  $order_data[0]['preparation_time_when_accepted']; 
   }
   $preparation_time_when_accepted = (int)$preparation_time_when_accepted;
   
    $actual_promo_subtotal_discounted_value  =  $order_data[0]['promo_subtotal_discounted_value'];  

    $is_cutlery_needed  =  $order_data[0]['is_cutlery_needed'];  
     // For is cutlery  need 
      if($is_cutlery_needed  == 1){//yes
         $is_cutlery_needed_display = "";
      
      }else if($is_cutlery_needed == 2){
         $is_cutlery_needed_display = "is_cutlery_display";
      }else{
          $is_cutlery_needed_display = "is_cutlery_display";
      }

    $payment_mode  =  $order_data[0]['payment_mode'];  
    //# 1 : Stripe 2 : Hitpay 3 : only wallet used----------------------
    if($payment_mode == 1){
      $payment_mode = "Credit/Debit";//Stripe
    }else if($payment_mode == 2){
      $payment_mode = "PayNow";//Hitpay
    }else if($payment_mode == 3){
       $payment_mode = "Wallet";
    }

    date_default_timezone_set('Asia/Singapore');
    // $order_place_time_date  = date("d-m-Y  H:i",$order_place_time);// convert UNIX timestamp to PHP DateTime
    $order_place_time_date  = date("d-m-Y  h:i A",$order_place_time);// convert UNIX timestamp to PHP DateTime
    // $order_place_current_time  = date("H:i",$order_place_time);
    $order_place_current_time  = date("h:i A",$order_place_time);

    //getting pick up time
    //Epcho time convert -------- start------
    $pickup_time_from =  $order_data[0]['pickup_time_from'];
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
    //Epcho time convert -------- end------

    //Epcho time convert --------
    $pickup_time_to =  $order_data[0]['pickup_time_to'];
    if($pickup_time_to != 0){
        // $final_pickup_time_to =date("d-m-Y  H:i",$pickup_time_to);// convert UNIX timestamp to PHP DateTime
        $final_pickup_time_to =date("d-m-Y  h:i A",$pickup_time_to);// convert UNIX timestamp to PHP DateTime
        // $final_pickup_time_only_to =date("H:i",$pickup_time_to);// convert UNIX timestamp to PHP DateTime
        $final_pickup_time_only_to =date("h:i A",$pickup_time_to);// convert UNIX timestamp to PHP DateTime

    }else{
        $final_pickup_time_to = "";
        $final_pickup_time_only_to = "";
    }

      // calcualtion of pickup time range  -----------START----------------
    # check order type
    if($order_type == 1){//order for Now
        #calcualtion = orderd time + (Preparation  Time when orderd)
        
       //  //$del_and_pre_time_total  = $preparation_time_when_ordered;
       //   // $mint_convert_in_hours = intdiv($preparation_time_when_ordered, 60).':'. ($preparation_time_when_ordered % 60);
       //   $mint_convert_in_hours = intdiv($preparation_time_when_accepted, 60).':'. ($preparation_time_when_accepted % 60);
       // // echo '<br>';
       //  $pickup_time_range = strtotime($order_place_current_time) + strtotime($mint_convert_in_hours);
       //  $final_pickup_range  =  date("H:i",$pickup_time_range);

       //  /*  $pickup_time_range = new DateTime("@$pickup_time_range");
       //  $final_pickup_range = $pickup_time_range->format('H:i');*/

       //  //we  gettting diffrence thats why we calculate by hours
       //  $time = strtotime($final_pickup_range);
       //  $final_pickup_range = date("H:i", strtotime('+7 hour +60 minutes', $time));

       //   //getting estimated time accroding to the customer app --start----
       //  // $estimated_time  = $preparation_time_when_ordered + $rest_delivery_time;
       //  $estimated_time  = $preparation_time_when_accepted + $rest_delivery_time;
       //  $estimated_mint_convert_in_hours = intdiv($estimated_time, 60).':'. ($estimated_time % 60);
       //  $final_estimated_time = strtotime($order_place_current_time) + strtotime($estimated_mint_convert_in_hours);
       //  $convert_schedule_time  =  date("H:i",$final_estimated_time);

       //  //we  gettting diffrence thats why we calculate by hours
       //  $time2 = strtotime($convert_schedule_time);
       //  $schedule_time = date("d-m-Y H:i", strtotime('+7 hour +60 minutes', $time2));

        $f_pickup_range = strtotime('+'.$preparation_time_when_accepted.'minutes',$order_place_time);
        $sch1 = strtotime('+'.$rest_delivery_time.'minutes',$f_pickup_range);
        // $schedule_time = date("d-m-Y H:i", $sch1);
        $schedule_time = date("d-m-Y h:i A", $sch1);

        // $final_pickup_range = date("H:i", $f_pickup_range);
        $final_pickup_range = date("h:i A", $f_pickup_range);
        //getting estimated time accroding to the customer app --end----

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
            $final_pickup_range = $pickup_time_range_change->format('h:i');

            
        }else{
            $final_pickup_range = $pickup_time_from_for_range;
        }

        $schedule_time =  $final_pickup_date_from.' '.$pickup_time_from_for_range.' to '. $final_pickup_time_only_to;
    }
    // calcualtion of pickup time range  -----------END----------------

    if($order_type  == 2){//If order type is "Self Pickup" 
      $schedule_pickup_lable = 'Pick-up';
    }else if($order_type  == 3){//If order type is "Order For Later" 
      $schedule_pickup_lable = 'Schedule';
    }else{
     $schedule_pickup_lable = 'Schedule';
    }

     


    $item_list = "";
    $total_of_variant_price = 0;
    if($order_product_details && !empty($order_product_details)){
         foreach ($order_product_details as $item_value) {
           $product_id = $item_value['product_id'];
           $product_name = trim($item_value['product_name']);
           $product_quantity = $item_value['product_quantity'];
           $product_unit_price = $item_value['product_unit_price'];
            $item_list.= '<tr class="table-head">
                            <td>'.$product_name.'';
                             
            if(!empty($item_value['variants'])){// if any varaint available  in order
                  foreach ($item_value['variants'] as $varaint_value) {
                        $variant_name = $varaint_value['variant_name'];
                        $variant_type_name = $varaint_value['variant_type_name'];
                        $variant_type_id = $varaint_value['variant_type_id'];
                        $variant_type_price = $varaint_value['variant_price'];
                      
                        //$variant_name_html = ' <p>'.$variant_name.'<br> <strong>';
                        $item_list .='<p>- '. $variant_type_name.'</p> ';//variant_type_id_'.$variant_type_id.'
                 
                        
                       $total_of_variant_price = $total_of_variant_price+ $variant_type_price;
                  }
               }else{
                    $total_of_variant_price = 0;    
               }

            $quantity_total = ($product_unit_price+$total_of_variant_price)* $product_quantity ;                 
            // $item_list.= '<p class="product_price"> S$'. $product_unit_price.'</p></td>
            $item_list.= '<p class="product_price"></p></td>
                            <td>'.$product_quantity.'</td>
                            <td style="text-align:right;">S$'.$quantity_total.'</td>
                       </tr>';
         }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Order Invoice</title>
    <!-- <link href="//db.onlinewebfonts.com/c/94ac1dcd1fd4d1429cd877cd9d99e480?family=Tanseek+Modern+Pro+Arabic" rel="stylesheet" type="text/css"/> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet"> -->
<link href="https://fonts.googleapis.com/css2?family=PT+Mono&display=swap" rel="stylesheet">

<style type="text/css">
    *{ color-adjust: exact;  -webkit-print-color-adjust: exact; print-color-adjust: exact; }

/*@page {  height: 3000px }*/
body{
    font-family: 'Dancing Script', cursive;

    /*Invoice css*/
}
.invoice-content {
    text-align: center;
    font-family: 'PT Mono', monospace;
    line-height: 27px;
    margin-bottom: 26px;
    margin-top: 0px;
    border-bottom: 1px dashed;
    padding-bottom: 35px;

    /*font-family: "Tanseek Modern Pro Arabic";*/
}
.invoice-content p {
    margin-bottom: 8px;
    font-size:23px;
    line-height: 20px;
    font-weight: 600;
    margin-top: 0px;
    font-family: 'PT Mono', monospace !important;

}
h1.order-name {
    font-size: 35px;
    font-weight: 600;
    margin-bottom: 18px;
    line-height: 45px;
    margin-top: 18px;
    font-family: 'PT Mono', monospace !important;
}
p.food-name {
    line-height: 19px;
    font-weight: 600;
    margin: 10px 0 13px !important;
}
h1.order-number {
    font-size: 50px;
    margin-top: 25px;
    font-weight: 600;
    margin-bottom: 10px;
}
.main-content {
    /*margin-top: 80px;*/
    max-width: 900px;
    margin: 50px auto;
    /*margin-bottom: 80px;*/

}
.order-address h4 {
    font-size: 20px;
    margin: 0;
    font-weight: 600;
    line-height: 36px;
}
.order-address {
    font-family: 'PT Mono', monospace;
    width: 75%;
}
.invoice-details{
    margin-top: 10px;
}
.invoice-details table {
    width: 100%;
}
.invoice-details th {
    text-align: left;
}
.table-head th {
    border-top: 1px dashed #333;
    padding-top: 9px;
    border-bottom: 1px dashed #333;
    padding-bottom: 9px;
    font-size: 30px;
    font-weight: 600;
}
.invoice-details {
    font-family: 'PT Mono', monospace;
}
.table-head td {
    /*border-top: 1px dashed #333;*/
    padding-top: 12px;
    border-bottom: 1px dashed #333;
    padding-bottom: 12px;
    font-size: 26px;
    font-weight: 600;
}
p.product_price {
    font-weight: 600;
    color: gray;
    margin-top: 9px;
    margin-bottom: 3px;
}
.total-amounts {
    font-family: 'PT Mono', monospace;
}
.total-amounts td {
    border-top: 1px dashed #333;
    padding-top: 22px;
    /*border-bottom: 1px dashed #333;*/
    padding-bottom: 22px;
    font-size: 30px;
    font-weight: 600;
    /*margin-top: 10px;*/
}
.total-orders td {
    padding-top: 19px;
    font-size: 22px;
    font-weight: 500;
    line-height: 18px;
}
.footer-section {
    border-bottom: 1px dashed #333;
    border-top: 1px dashed #333;
}
.footer-section h2{
    text-align: center;
    margin-bottom: 22px;
    margin-top: 22px;
    font-size: 33px;
    font-weight: 600;
    font-family: 'PT Mono', monospace;
}
@media only screen and (max-width: 768px){
.invoice-content h2 {
    font-size: 19px;
    line-height: inherit !important;
}
}
h1.restaurants-name {
    text-transform: uppercase;
    font-size: 40px;
    margin-bottom: 3px;
}
p.order-types {
    background: #efefef;
    max-width: 200px;
    margin: 20px auto 30px;
    padding: 16px;
    border-radius: 5px;
}
.order-cutlery-section {
    display: inline-flex;
    width: 100%;
    align-items: center;
}
.cutlery-image img {
    max-width: 85px;
    text-align: center;
    margin: 0 auto 20px;
}
.cutlery-image {
    width: 25%;
    text-align: center;
}
.cutlery-image h3 {
    margin-top: 0;
    font-size: 20px;
    font-family: 'PT Mono', monospace;
}
.is_cutlery_display{
    display: none;
}
.pickuptime{
    margin-bottom: 15px;
    font-size: 33px !important;
    margin-top: 15px;
    background: #efefef;
    padding: 13px;
    border-radius: 5px; 
    margin-top: 12px !important;
    margin-bottom: 12px !important;   
}
.pickup-wrapper {
    text-align: center;
}
.pickup-wrapper h4{
    font-family: 'PT Mono', monospace;
}
.d-flex {
    display: flex;
    /*align-items: center;*/
    position: relative;
}
.d-flex h4 {
    /*width: 40%;*/
    width: 60%;
    /*font-size: 24px;*/
    
}
.d-flex span {
    position: relative;
    width: 10%;
}
.d-flex span:before {
    content: ":";
    position: absolute;
    font-size: 25px;
    top: 2px;
    left: 5px;
}
.d-flex p {
     width: 55%;
     font-size: 18px;
         line-height: 30px;
         margin-top: 0px;
         margin-bottom: 10px;
}
.itemWidth{
width:60%;
}

</style>
</head>
<body>
<div class="main-content">
<div class="container">
    <div class="invoice-content">
        <h1 class="restaurants-name">Kerala Eats</h1>
        <h1 class="order-name" style="font-family: 'PT Mono', monospace !important;"><?php echo stripslashes($restaurant_name) ;?> </h1>
        <p class="food-name" style="font-family: 'PT Mono', monospace !important;"><?php echo $rest_pin_address.', '.$rest_postal_code.', '.$rest_unit_number?> </p>
        <p>+65<?php echo $rest_contact_number[0]['mobile'];?></p>
        <p class="order-types" style="background: #efefef !important;"><?php echo $order_type_name;?> </p>
        <h1 class="order-number" style="font-family: 'PT Mono', monospace;">#<?php echo $order_number_id;?></h1>
    </div>
    <div class="order-cutlery-section">
          <div class="order-address">
            
            <!-- <h4 ><?php echo $delivery_name;?>, +65<?php echo $delivery_mobile;?></h4> -->
            <!-- <h4>+65<?php echo $delivery_mobile;?></h4> -->
            <div class="d-flex">
                <h4> Delivery Address </h4>
                <span></span>
                <p> <?php echo $delivery_name;?>, +65<?php echo $delivery_mobile;?><br>
                    <?php echo  $delivery_address.',  '.$delivery_postal_code.', '.$delivery_unit_number; ?></p>
            </div>
            <div class="d-flex">
                <h4> Payment Method  </h4>
                <span></span>
                <p> <?php echo $payment_mode;?> </p>
            </div>
            <?php if($special_instruction != '' && $special_instruction != 'NA')
            { ?>
                <div class="d-flex">
                    <h4>Special Instructions</h4>
                    <span></span>
                    <p> <?php echo $special_instruction;?> </p>
                </div>
            <?php
            } ?>
            
            <div class="d-flex">
                <h4> Schedule Time </h4>
                <span></span>
                <p> <?php echo $schedule_time; ?> </p>
            </div>
            <!-- <h4 ><?php //echo  $delivery_address.',  '.$delivery_postal_code.', '.$delivery_unit_number; ?></h4>
            <h4 ><strong>Payment Method :</strong> <?php //echo $payment_mode;?></h4>
            <h4><strong>Schedule Time : </strong> <?php //echo $schedule_time; ?> </h4> -->
        </div>
        <div class="cutlery-image <?php echo $is_cutlery_needed_display;?>">
            <!-- <h3>Do you need cutlery?</h3> -->
            <img src='data:image/png;base64,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'/>
            <h3> Please Provide Cutleries </h3>
        </div>
    </div>
    <div class="pickup-wrapper">
        <h4 style="font-size: 24px;" class="pickuptime" <?php //echo $schedule_time_show;?>><strong>Pick-Up Time : </strong> <?php echo $final_pickup_date_from.' '. $final_pickup_range; ?> </h4> 
    </div>
    <div class="invoice-details">
        <table style="width: 100%">
            <thead>
                <tr class="table-head">
                <th class="itemWidth">Item</th>
                <th>Qty</th>
                <th style="text-align: right;" >Sub Total</th>
            </tr>
            </thead>
            <tbody>
                <?php echo $item_list;?>
               <tr class="total-orders">
                <td colspan="2">Sub Total:</td>
                <td style="text-align: right;">S$<?php echo $sub_total;?></td>
               </tr>
               <tr class="total-orders">
                <td colspan="2">Promo Applied:</td>
                <td style="text-align: right;"> <?php if($actual_promo_subtotal_discounted_value != 0){echo "- S$".$actual_promo_subtotal_discounted_value; }else{echo "NA";} ?></td>
               </tr>
               <!-- <tr class="total-orders">
                <td colspan="2">Full Address with postal code and unit <br>number:</td>
                <td style="text-align: right;"><?php echo  $delivery_address.',  '.$delivery_postal_code.', '.$delivery_unit_number; ?></td>
               </tr> -->
              <!--  <tr class="total-orders">
                <td colspan="2">Do you need cutlery?:</td>
                <td style="text-align: right;"><?php echo $is_cutlery_needed;?></td>
               </tr> -->
              <!-- <tr class="total-orders">
                <td colspan="2">Read and understood that the average</td>
                <td>Yes Please proceed</td>
               </tr> 
               <tr class="total-orders">
                <td colspan="2">delivery time for today is 1.15 hours:</td>
 
                <td>with my order</td>
               </tr>  -->
               <tr class="total-orders">
                <td colspan="2" style="padding-bottom: 19px;">Delivery Charges:</td>
                <td style="text-align: right;">S$<?php echo $dc_amount;?></td>
               </tr>
               <tr class="total-amounts">
                <td colspan="2">Total Amount</td>
                <td style="text-align: right;">S$<?php echo $total_amount;?> </td>
               </tr>
            </tbody>
        </table>
        <div class="footer-section">
            <h2>------------ Thank You ------------</h2>
        </div>
    </div>
</div>
</div>
<button style="display: none;" id="order_details_print">dffsfsdf</button>
<script type="text/javascript">
    var BASE_URL = "<?php echo base_url();?>";
</script>
<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/custom.js"></script>
</body>
</html>

