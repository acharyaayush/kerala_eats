 <div class="total-earning-box">
    <div class="kl-ern">
        <p class="earning-heading"> Total Earnings</p>
    </div>
    <div>
        <p class="earning-price">S$<?php echo $total_earning_array[0]['total_earning'];?> </p>
        <p class="total-earning-order"><?php echo $total_earning_array[1]['total_order'];?> Order</p>
    </div>
    <!-- <div class="row">
  <div class="col-md-6">
     <p class="earning-heading"> Total Earnings</p>
  </div>
  <div class="col-md-6">
     <p class="earning-price">$350.00</p>
     <p class="total-earning-order">300 Order</p>
  </div>
</div> -->
</div>
<div class="total-earning-box">
    <div class="kl-ern">
        <p class="earning-heading"> Gross Sales</p>
    </div>
    <div>
        <p class="earning-price">S$<?php echo $total_earning_array[2]['gross_sale'];?></p>
        <p class="total-earning-order"><?php echo $total_earning_array[3]['total_gross_order'];?> Order</p>
    </div>
    <!-- <div class="row">
  <div class="col-md-6">
     <p class="earning-heading"> Gross Sales</p>
  </div>
  <div class="col-md-6">
     <p class="earning-price">$350.00</p>
     <p class="total-earning-order">300 Order</p>
  </div>
</div> -->
</div>
<div class="total-earning-box">
    <div class="kl-ern">
        <p class="earning-heading"> Cancelled Order</p>
    </div>
    <div>
        <p class="earning-price">S$<?php  if($total_earning_array[4]['total_cancel_order_amount'] !=""){ echo $total_earning_array[4]['total_cancel_order_amount']; }else{ echo "0.00"; }?> </p>
        <p class="total-earning-order"><?php echo $total_earning_array[5]['total_cancel_order'];?> Order</p>
    </div>
    <!-- <div class="row">
  <div class="col-md-6">
     <p class="earning-heading"> Cancelled Order</p>
  </div>
  <div class="col-md-6">
     <p class="earning-price">$350.00</p>
     <p class="total-earning-order">30 Order</p>
  </div>
</div> -->
</div>