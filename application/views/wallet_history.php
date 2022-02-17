<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1><?php echo $pageTitle;?></h1>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item"><?php echo $pageTitle;?></div>
         </div>
      </div>
      <div class="section-body">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                  
                  <div class="add-restaurant">
                     <!-- <h4>Total Balance : <span style="color:lightgreen;"><?php echo "S$".$wallet_balance;?><span></span></h4><h4>Total Money Added : <?php echo "S$".$total_money_added;?></h4><h4>Total Cashback : <?php echo "S$".$total_cashback;?></h4> -->
                     <div class="row">
                         <div class="col-md-3">
                             <div class="total_balance">
                                 <h4>Total Balance <span style="color:lightgreen;">
                                         <?php echo "S$".$wallet_balance;?><span></span></h4>
                             </div>
                         </div>
                         <div class="col-md-3">
                             <div class="total_money_cashback">
                                 <h4>Total Money Added <span>
                                         <?php echo "S$".$total_money_added;?>
                                     </span></h4>
                             </div>
                         </div>
                         <div class="col-md-3">
                             <div class="total_money_cashback">
                                 <h4>Total Cashback <span>
                                         <?php echo "S$".$total_cashback;?>
                                     </span></h4>
                             </div>
                         </div>
                         <div class="col-md-3">
                             <div class="total_money_cashback">
                                 <h4>Profile Id <span><a href="<?php echo base_url('admin/user_details/3/'.$user_id)?>"><?php echo "10".$user_id;?></a>
                                     </span></h4>
                             </div>
                         </div>
                     </div>
                     <input type="hidden" id="wallet_user_id" value="<?php echo $user_id; ?>">
                     <div class="col-5"></div>
                  <div class="col-2">
                     <button class="btn btn-primary filter_button" id="" type="button">Filter</button>
                  </div>
                  <div class="card-header user_tables">
                     <div class="promotionFileter">
                        <div class="promotionFilterFields">
                           <div class="row">
                              <div class="form-group col-md-3">
                                   <label>From Date</label>
                                   <input  min="2020-01-01" autocomplete="off" placeholder="dd-mm-yyyy" id="fromdate" name="fromdate" max="" class="form-control fromdate wv_filter_box_height" value="<?php if($fromdate != '' && $fromdate != 'all'){echo $fromdate;} ?>" />
                               </div>
                               <div class="form-group col-md-3">
                                   <label>To Date</label>
                                   <input autocomplete="off"placeholder="dd-mm-yyyy" id="todate" name="todate" max="" class="form-control todate wv_filter_box_height" value="<?php if($todate != '' && $todate != 'all'){echo $todate;} ?>" />
                               </div>
                               <div class="form-group col-md-3">
                                   <label>Type</label>
                                   <select class="custom-select wallet_type wv_filter_box_height form-control" name="wallet_type">
                                       <option value="">Select Type</option>
                                       <option value="1" <?php if($type == '1' && $type != 'all'){echo "selected";} ?>>Cashback</option>
                                        <option value="2" <?php if($type == '2' && $type != 'all'){echo "selected";} ?>>Money Added</option>
                                   </select>
                               </div>
                           </div>
                        </div>
                        <div class="form-group users_btns">
                           <form action="javascript:void(0);">
                              <button class="btn btn-primary m-0 search_user_wallet_list_data mr-2" id="search_user_wallet_list_data" type="button">Search</button>
                               <a href="<?php echo base_url('admin/wallet_history/'.$user_id.'/all/all/all/') ?>" class="btn btn-secondary clear_btns mr-2">Clear</a>
                              <!-- <button class="btn btn-secondary clear_btns  export_user_csv" type="button">Export CSV</button> -->
                           </form>
                        </div>
                     </div>
                  </div>
                     <div class="card-body table-flip-scroll orders-tables tb-scroll" style="padding: 20px 0px;">
                         <table class="table" id="">
                           <thead>
                              <tr>
                                 <th>Order Number</th>
                                 <!-- <th>User Id</th> -->
                                 <th>Debited amount</th>
                                 <th>Credited amount</th>
                                 <th>Type</th>
                                 <th>Valid Till</th>
                                 <th>Comments</th>
                                 <th>Wallet Date</th>
                              </tr>
                              <?php
                                 if(!empty($all_transactions))
                                 { 
                                    date_default_timezone_set('Asia/Singapore') ;
                                    foreach($all_transactions as $wallet)
                                    { ?>
                                       <tr>
                                          <td> 
                                             <?php 
                                             if($wallet['order_id'] == 0)
                                                {
                                                   echo "NA";
                                                }
                                                else 
                                                { ?> 
                                                   <a href="<?php echo base_url('admin/order_single/'.$wallet['order_id']);?>"><?php echo "KE10".$wallet['order_id'];?></a> 
                                                <?php
                                                } ?>
                                          </td>
                                          <!-- <td>10<?php echo $wallet['user_id'];?></td> -->
                                          <td><?php if($wallet['debited'] > 0){?> <span style="color: red"><?php echo "-S$".$wallet['debited']; ?></span> <?php }else{echo "S$".$wallet['debited'];} ?></td>
                                          <td><?php if($wallet['credited'] > 0){?> <span style="color: green"><?php echo "+S$".$wallet['credited']; ?></span> <?php }else{echo "S$".$wallet['credited'];} ?></td>
                                          <td><?php if($wallet['type'] == 1){echo "Cashback";}elseif($wallet['type'] == 2){echo "Credited";}else{echo "Debited";} ?></td>
                                          <td><?php if($wallet['valid_till'] != '' && $wallet['valid_till'] != 0){echo date("Y-m-d",$wallet['valid_till']); }else {echo 'NA';} ?></td>
                                          <td><?php echo $wallet['comments']; ?></td>
                                          <td><?php echo date("M d , Y h:i A",$wallet['wallet_date']);?></td>
                                       </tr>
                                    <?php
                                    }
                                    ?>
                                 <?php
                                 }else
                                 { ?>
                                    <tr><td colspan="7" style="text-align:center;">No Data found</td></tr>
                                 <?php
                                 }
                              ?>
                           </thead>
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
      </div>
   </section>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="Stylesheet" type="text/css" />
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
