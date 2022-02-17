<!-- Main Content -->
<?php
 $restaurant_list = "";
 
  if (isset($resturant_details) && $resturant_details != "" && !empty($resturant_details)) 
  {
        $count = 1;
        $rest_id = $this->uri->segment(6);
        // echo "rest_id IS ".$rest_id;
        
        foreach ($resturant_details as $value) {
        $restaurant_id = $value['restaurant_id'];
        $restaurant_name = stripslashes($value['rest_name']);

        if($rest_id != 0)
        {

        }else
        {

        }

        if($restaurant_id == $rest_id)
        {
              $select = "selected";  
                
        }else{
             $select = "";
            
        }
        $restaurant_list.= '<option value="'.$restaurant_id.'" '.$select.'>'.$restaurant_name.'</option>';
         $count++;
      }
  }
  else
  {
    $restaurant_list = '<option value="">No Restaurant available </option>';
  }
?>
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Reservation</h1>
         <span>
            <div class="col-md-2">
               <!-- Pass 1 as parameter to call add user form -->
               <!--  <a href="#" class="btn btn-primary">Create Order</a> -->
            </div>
         </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Reservation</div>
         </div>
      </div>
      <div class="section-body">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                <div class="col-5"></div>
                    <div class="col-2">
                    <button class="btn btn-primary filter_button" id="" type="button">Filter</button>
                  </div>
                   <div class="card-header user_tables">
                        <!-- <div class="d-flex"> -->
                            <div class="product-filterForm">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>From Date</label>
                                       <input  min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" id="fromdate" name="fromdate" max="" class="form-control fromdate " value="<?php if($fromdate != '' && $fromdate != 'all'){echo $fromdate;} ?>" />
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>To Date</label>
                                       <input  min="2020-01-01" autocomplete="off"placeholder="dd-mm-yyyy"  id="todate" name="todate" max="" class="form-control todate" value="<?php if($todate != '' && $todate != 'all'){echo $todate;} ?>" />
                                    </div>
                                     <div class="form-group col-md-3">
                                      <label>Accept Status</label>
                                      <select class="custom-select products_mode wv_filter_box_height form-control" name="accept_status" id="accept_status">
                                          <option value="">Select Accept Status</option>
                                          <option value="0" <?php if($accept_status == '0' && $accept_status != 'all'){echo "selected";} ?>>Pending</option>
                                          <option value="1" <?php if($accept_status == '1' && $accept_status != 'all'){echo "selected";} ?>>Accepted</option>
                                          <option value="2" <?php if($accept_status == '2' && $accept_status != 'all'){echo "selected";} ?>>Rejected</option>
                                      </select>
                                    </div>
                                    <?php 

                                    //  if merchant is logged in, then this condition will check and only merchant restaurant prodcuts will show. if this blank that means super admin is logged in and then all resataurant will show
                                    if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){
                                     ?>
                                     <div class="form-group col-md-3">
                                      <label>Restaurant</label>
                                      <select class="custom-select  wv_filter_box_height form-control search_data" name="restaurant_id" id="restaurant_id">
                                           <option value="0" selected="" >All Restaurant</option>
                                          <?php echo $restaurant_list;?>
                                      </select>
                                    </div>
                                    <?php
                                     }

                                    ?>
                                    <div class="form-group col-md-4">
                                        <div class="users_btns search_clear_btns  d-flex mt-20">
                                            <button class="btn btn-primary search_data" id="search_reservation"  type="button">Search</button>
                                            <a href="<?php echo base_url('admin/reservations')?>" class="btn btn btn-secondary m-0 clear_btns" > Clear</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- </div> -->
                    </div>
                    <div class="card-body table-flip-scroll orders-tables">
                     <table class="table ">
                        <thead>
                           <tr>
                              <th>Booking ID</th>
                              <th>Customer ID</th>
                              <th>Customer Name</th>
                              <th>Restaurant Name</th>
                              <th>Reservation Date</th>
                              <th>Booked on</th>
                              <th>Time slot</th>
                              <th>No of People</th>
                              <th>Accept Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>

                            <?php
                            if(count($reservation_data) > 0)
                            {
                                // print_r($reservation_data);
                                date_default_timezone_set('Asia/Singapore');
                                foreach($reservation_data as $dine_in)
                                { 
                                    ?>
                                    <tr>
                                      <td><?php echo $dine_in['booking_id']; ?></td>
                                      <td><a href="<?php echo base_url('admin/user_details/3/'.$dine_in['customer_id'].'');?>" ><?php echo $dine_in['customer_number_id']; ?></a></td>
                                      <td><a href="<?php echo base_url('admin/user_details/3/'.$dine_in['customer_id'].'');?>" ><?php echo $dine_in['fullname']; ?></a></td>
                                      <td><a href="<?php echo base_url('admin/add_edit_restaurant/2/'.$dine_in['id'].'/'.$dine_in['admin_id']);?>" ><?php echo stripslashes($dine_in['rest_name']) ; ?></td>
                                      <td><?php echo date('Y-m-d',$dine_in['booking_date']) ; ?></td>
                                      <td><?php echo date('Y-m-d H:i',$dine_in['created_at']) ; ?></td>
                                      <td><?php echo date('H:i',$dine_in['time_slot']) ; ?></td>
                                      <td><?php echo $dine_in['no_of_people']; ?></td>
                                      <td><?php if($dine_in['is_accepted'] == 0){echo "Pending";}elseif($dine_in['is_accepted'] == 1){echo "Accepted";}elseif($dine_in['is_accepted'] == 2){echo "Rejected";}elseif($dine_in['is_accepted'] == 3){echo "Cancelled By Customer";}  ?></td>
                                      <td> 
                                          <div class="d-flex">
                                            <?php
                                            if($dine_in['is_accepted'] != 3)
                                            { ?>
                                                <a class="btn btn-primary btn-action mr-2" title="Edit" onclick="edit_reservation('<?php echo $dine_in['reservation_id']; ?>');" ><i class="fas fa-pencil-alt"></i></a>
                                            <?php 
                                            }
                                            if($dine_in['is_accepted'] == 1) # ACCEPTED
                                            { ?>
                                                <a href="<?php echo base_url('admin/accept_reject_reservation/2/'.$dine_in['reservation_id']) ?>" class="btn btn-danger btn-action" id="" data-id="" data-toggle="tooltip" title="Reject"> <i class="fas fa-times"></i> </a>
                                            <?php 
                                            }elseif($dine_in['is_accepted'] == 0) # PENDING
                                            { ?>
                                                <a href="<?php echo base_url('admin/accept_reject_reservation/1/'.$dine_in['reservation_id']) ?>" class="btn btn-success btn-action mr-2" id="" data-id="" data-toggle="tooltip" title="Accept"> <i class="fas fa-check"></i> </a>
                                                <a href="<?php echo base_url('admin/accept_reject_reservation/2/'.$dine_in['reservation_id']) ?>" class="btn btn-danger btn-action" id="" data-id="" data-toggle="tooltip" title="Reject"> <i class="fas fa-times"></i> </a>

                                            <?php
                                            }elseif($dine_in['is_accepted'] == 2) # REJECTED
                                            { ?>
                                                <a href="<?php echo base_url('admin/accept_reject_reservation/1/'.$dine_in['reservation_id']) ?>" class="btn btn-success btn-action mr-2" id="" data-id="" data-toggle="tooltip" title="Accept"> <i class="fas fa-check"></i> </a>
                                            <?php 
                                            }elseif($dine_in['is_accepted'] == 3) # CANCELLED
                                            { 
                                                echo "No action available";
                                            }
                                            ?>
                                          </div>
                                      </td>
                                   </tr>         
                                <?php
                                }
                            }else
                            { ?>
                                <tr>
                                    <td colspan="8" style="text-align:center;">No data</td>
                                </tr>
                                
                            <?php
                            }
                            ?>
                        </tbody>
                     </table>
                      <nav class="text-xs-right">
                        <?php if (isset($links)) { ?>
                            <?php echo $links; ?>
                        <?php } ?>
                    </nav>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>

<div class="modal fade edit-customer" id="edit_reservation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog read_more_popup" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit reservation <span id="booking_id"></span> </h5>
            <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>

        <form method="POST">
           <div class="modal-body">
              <div class="admin_profile">
                 <div class="form-group  admin-input-field">
                   <div class="row">
                       <div class="col-md-4">
                          <label>Date</label>
                       </div>
                       <div class="col-md-8">
                          <input type="text" required="" value="" id="datepicker">
                       </div>
                    </div>
                    <br>
                    <div class="row">
                       <div class="col-md-4">
                          <label>Slot</label>
                       </div>
                       <div class="col-md-8">
                        <select class="form-control" id="dd_time_slot">
                            <?php
                            $starttime = '00:00';  // your start time
                            $endtime = '23:30';  // End time
                            $duration = '30';  // split by 30 mins
                            $array_of_time = array();
                            $start_time    = strtotime ($starttime); //change to strtotime
                            $end_time      = strtotime ($endtime); //change to strtotime

                            $add_mins  = $duration * 60;

                            while ($start_time <= $end_time) // loop between time
                            {
                               $array_of_time[] = date ("H:i", $start_time);
                               $start_time += $add_mins; // to check endtie=me
                            } 
                            foreach($array_of_time as $key=>$val)
                            { 
                                ?>
                                <option id="time_slot_<?php echo $val;?>" value="<?= $val ?>"><?php echo $val; ?></option>      
                            <?php 
                            }
                            ?>
                        </select>
                       </div>
                    </div>
                    <br>
                    <div class="row">
                       <div class="col-md-4">
                          <label>Number of People</label>
                       </div>
                       <div class="col-md-8">
                          <input type="number" min="1" required="" step="1" name="no_of_people" id="no_of_people" value="" placeholder="Number of people" required="" class="check_space" />
                          &nbsp;
                          <input type="hidden" id="reservation_id" value="">
                          <input type="hidden" id="user_id" value="">
                          <!-- <input type="hidden" id="booking_id" value=""> -->
                       </div>
                    </div>
                 </div>
              </div>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Close</button>
              <button type="button" onclick="" id="edit_reservation_submit" class="btn btn-primary modal_btns">Save</button>
           </div>
        </form>
      </div>
   </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="Stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>

<script type="text/javascript">
    $('#datepicker').datepicker({
        // format: 'yyyy-mm-dd'
        // minDate:new Date(),
        dateFormat : 'yy-mm-dd',
        // timeFormat : ' HH',
        // showMinute : false,
        // showOn : 'focus'
    });
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
  <script type="text/javascript">
     var $j = jQuery.noConflict();
    $j("#fromdate").datepicker({
        dateFormat: 'yy-mm-dd',
        // numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate());
            $j("#todate").datepicker("option", "minDate", dt);
        }
    });
    $j("#todate").datepicker({
        dateFormat: 'yy-mm-dd',
        // numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() - 1);
            // $j("#fromdate").datepicker("option", "maxDate", dt);
        }
    });          
    
  </script>