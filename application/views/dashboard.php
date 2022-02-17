<?php

  //Total Customer Count
  if (isset($total_customer) && $total_customer[0] != "") {
    $total_customer_value =  $total_customer[0]['total_customer'];
  }

  //Total Restaurant Admin Count
  if (isset($total_restaurant_admin) && $total_restaurant_admin[0] != "") {
    $total_restaurant_admin_value =  $total_restaurant_admin[0]['total_restaurant_admin'];
  }

  //Total Restarant Count
  if (isset($total_restaurant) && $total_restaurant[0] != "") {
    $total_restaurant_value =  $total_restaurant[0]['total_restaurant'];
  }

  //Total Order Count
  if (isset($total_order) && $total_order[0] != "") {
    $total_order_value =  $total_order[0]['total_order'];
  }

  //Total Cancelled Order Count
  if (isset($total_cancel_order) && $total_cancel_order[0] != "") {
    $total_cancel_order_value =  $total_cancel_order[0]['total_cancel_order'];
  }

  //Total Pending Order Count
  if (isset($total_pending_order) && $total_pending_order[0] != "") {
    $total_pending_order_value =  $total_pending_order[0]['total_pending_order'];
  }

  //Total Dispatched Order Count
  if (isset($total_dispatched_order) && $total_dispatched_order[0] != "") {
    $total_dispatched_order_value =  $total_dispatched_order[0]['total_dispatched_order'];
  }

  //Total Completed Order Count
  if (isset($total_completed_order) && $total_completed_order[0] != "") {
    $total_completed_order_value =  $total_completed_order[0]['total_completed_order'];
  }


?>
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
        <?php if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){?>
         <div class="row">
          
         <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?php echo base_url(); ?>admin/AllUser/2/all/">
              <div class="card card-statistic-1">
                 <div class="card-icon bg-primary">
                    <i class="far fa-user"></i>
                 </div>
                
                 <div class="card-wrap">
                    <div class="card-header">
                       <h4>Total Merchant </h4>
                    </div>
                    <div class="card-body">
                       <?php echo $total_restaurant_admin_value;?>
                    </div>
                 </div>
              </div>
            </a>
         </div>

         <div class="col-lg-3 col-md-6 col-sm-6 col-12">
           <a href="<?php echo base_url(); ?>admin/restaurant_list">
            <div class="card card-statistic-1">
               <div class="card-icon bg-danger">
                  <i class="far fa-newspaper"></i>
               </div>
               <div class="card-wrap">
                  <div class="card-header">
                     <h4>Total Restaurant</h4>
                  </div>
                  <div class="card-body">
                     <?php echo $total_restaurant_value;?>
                  </div>
               </div>
            </div>
           </a>
         </div>
         <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <a href="<?php echo base_url(); ?>admin/orders">
            <div class="card card-statistic-1">
               <div class="card-icon bg-warning">
                  <i class="far fa-file"></i>
               </div>
               <div class="card-wrap">
                  <div class="card-header">
                     <h4>Total Order</h4>
                  </div>
                  <div class="card-body">
                     <?php echo $total_order_value;?>
                  </div>
               </div>
            </div>
            </a>
         </div>
         <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <a href="<?php echo base_url(); ?>admin/AllUser/3/all/">
            <div class="card card-statistic-1">
               <div class="card-icon bg-success">
                  <i class="fas fa-circle"></i>
               </div>
               <div class="card-wrap">
                  <div class="card-header">
                     <h4>Total Customer</h4>
                  </div>
                  <div class="card-body">
                     <?php echo $total_customer_value ;?>
                  </div>
               </div>
            </div>
            </a>
         </div>
      </div>

      <div class="total-completed-process-order">
         <h4>Orders</h4>
      <div class="row">
         <div class="col-lg-3 col-md-12 col-12 col-sm-12">
            <a class="text-decoration-none" href="<?php echo base_url(); ?>admin/orders/0/0/all/all/all/all/all/all/4/">
                <div class="order-completed-process">
                   <div class="col-12 cancel">
                      <p class="number count"><?php echo $total_cancel_order_value;?></p>
                      <p  class="subheading">Cancelled</p>
                   </div>
                </div>
            </a>
         </div>
         <div class="col-lg-3 col-md-12 col-12 col-sm-12">
            <a class="text-decoration-none" href="<?php echo base_url(); ?>admin/orders/0/0/all/all/all/all/all/all/0/">
                <div class="order-completed-process">
                   <div class="col-12 pending">
                      <p class="number count"><?php echo $total_pending_order_value;?></p>
                      <p  class="subheading">Pending</p>
                   </div>
                </div>
            </a>
         </div>
         <div class="col-lg-3 col-md-12 col-12 col-sm-12">
            <a  class="text-decoration-none" href="<?php echo base_url(); ?>admin/orders/0/0/all/all/all/all/all/all/3/">
                <div class="order-completed-process">
                   <div class="col-12 total">
                      <p class="number count"><?php echo $total_dispatched_order_value;?></p>
                      <p  class="subheading">Dispatched</p>
                   </div>
                </div>
            </a>
         </div>
         <div class="col-lg-3 col-md-12 col-12 col-sm-12">
            <a class="text-decoration-none" href="<?php echo base_url(); ?>admin/orders/0/0/all/all/all/all/all/all/5/">
            <div class="order-completed-process">
               <div class="col-12 complete">
                  <p class="number count"><?php echo $total_completed_order_value;?></p>
                  <p  class="subheading">Completed</p>
               </div>
            </div>
            </a>
         </div>
      </div>
    </div>
    <?php }?>
        <div class="row">
            <div class="col-lg-4 col-md-12 col-12 col-sm-12">
                <div class="total-sales">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-7 col-md-12 col-12 col-sm-12">
                                     <h4>Total Sales</h4>
                                </div>
                                <div class="col-lg-5 col-md-12 col-12 col-sm-12">
                                    <select class="custom-select wv_filter_box_height form-control" id="filter_total_sale_by_year">
                                        <?php 
                                            for ($years= 2021; $years <= date('Y'); $years++) { 
                                               echo '<option value="2021">'.$years.'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                       
                        <div class="graph-part card-statistic">
                            <div class="card-chart" id="total_sales_chart">
                                <?php $this->load->view('dashboard-total-sales-chart');?>
                            </div>
                            <p class="total-order-price"> S$<?php echo  number_format($total_sales,2,'.','');?></p>
                            <p class="order-number"> <?php echo $total_sales_order;?> Order</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-12 col-sm-12">
                <div class="total-sales">
                    <div class="card">
                        <div class="card-header" style="margin-bottom: 13px;">
                            <h4>Last Month Sales</h4>
                        </div>
                        <div class="graph-part card-statistic">
                            <div class="card-chart">
                                <canvas id="sales-chart" height="220px"></canvas>
                            </div>
                            <!-- <div id="chartContainer" style="height: 210px; width: 450px"></div> -->
                            <p class="total-order-price"> S$<?php echo number_format($last_month_sale_total,2,'.','');?></p>
                            <p class="order-number"> <?php echo $last_month_order;?> Order</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-12 col-sm-12">
                <div class="total-sales">
                    <div class="card">
                        <div class="card-header" style="margin-bottom: 13px;">
                            <h4>Last Week Sales</h4>
                        </div>
                        <div class="graph-part card-statistic">
                            <div class="card-chart">
                                <canvas id="week-sales" height="220px"></canvas>
                            </div>
                            <p class="total-order-price"> S$<?php echo number_format($last_week_sale_total,2,'.','');?></p>
                            <p class="order-number"> <?php echo $last_week_order;?> Order</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-12 col-12 col-sm-12">
                <div id="earning_report_data">
                    <?php $this->load->view('dashboard-total-earning-cancel-gross-sale');?>
                </div>
            </div>
            <div class="col-lg-9 col-md-12 col-12 col-sm-12">
                <div class="report-section">
                    <div class="card">
                        <div class="card-header">
                            <h4>Report</h4>
                            <div class="card-header-action">
                                <span class="business_date_field">
                                    <div class="datepikerInputP">

                                        <input  type="text" min="2021-01-01" autocomplete="off" placeholder="yyyy-mm-dd" id="fromdate" name="fromdate" max="" class="form-control fromdate wv_filter_box_height" value="<?php echo $this->dashboard_earning_report_from_date;?>"/>
                                        <span class="datepickerIcon">
                                            <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 8.11133H17" stroke="#FF7E37" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <rect x="14" y="3" width="3" height="15" fill="#D6D6D6" />
                                                <path d="M15.2222 2.77783H2.77778C1.79594 2.77783 1 3.57377 1 4.55561V17.0001C1 17.9819 1.79594 18.7778 2.77778 18.7778H15.2222C16.2041 18.7778 17 17.9819 17 17.0001V4.55561C17 3.57377 16.2041 2.77783 15.2222 2.77783Z" stroke="#050305" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M12.5552 1V4.55556" stroke="#050305" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M5.44482 1V4.55556" stroke="#050305" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>
                                    <span class="error" id="error_in_from_date" style="position: absolute;top: 46px;"></span>

                                    <div class="datepikerInputP">
                                        <input autocomplete="off"placeholder="yyyy-mm-dd" id="todate" name="todate" max="" class="form-control todate wv_filter_box_height" value="<?php echo $this->dashboard_earning_report_to_date;?>" />
                                        <span class="datepickerIcon">
                                            <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 8.11133H17" stroke="#FF7E37" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <rect x="14" y="3" width="3" height="15" fill="#D6D6D6" />
                                                <path d="M15.2222 2.77783H2.77778C1.79594 2.77783 1 3.57377 1 4.55561V17.0001C1 17.9819 1.79594 18.7778 2.77778 18.7778H15.2222C16.2041 18.7778 17 17.9819 17 17.0001V4.55561C17 3.57377 16.2041 2.77783 15.2222 2.77783Z" stroke="#050305" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M12.5552 1V4.55556" stroke="#050305" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M5.44482 1V4.55556" stroke="#050305" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>
                                    <button class="btn" type="button" id="filter_chart_by_date">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div id="report_chart">
                            <?php $this->load->view('dashboard-earning-report-chart');?>
                        </div>
                    </div>
<!--                    <div id="container" style="height: 500px;"></div>-->
                    <!-- <div class="statistic-details mt-sm-4">
                  <div class="statistic-details-item">
                     <span class="text-muted">
                     <span class="text-primary"><i class="fas fa-caret-up"></i></span> 7%
                     </span>
                     <div class="detail-value">$243</div>
                     <div class="detail-name">Today's Sales</div>
                  </div>
                  <div class="statistic-details-item">
                     <span class="text-muted">
                     <span class="text-danger"><i class="fas fa-caret-down"></i></span> 23%
                     </span>
                     <div class="detail-value">$2,902</div>
                     <div class="detail-name">This Week's Sales</div>
                  </div>
                  <div class="statistic-details-item">
                     <span class="text-muted">
                     <span class="text-primary"><i class="fas fa-caret-up"></i></span>9%
                     </span>
                     <div class="detail-value">$12,821</div>
                     <div class="detail-name">This Month's Sales</div>
                  </div>
                  <div class="statistic-details-item">
                     <span class="text-muted">
                     <span class="text-primary"><i class="fas fa-caret-up"></i></span> 19%
                     </span>
                     <div class="detail-value">$92,142</div>
                     <div class="detail-name">This Year's Sales</div>
                  </div>
               </div> -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-md-12 col-12 col-sm-12">
            </div>
        </div>
</div>
</section>
</div>

<!-- 
<script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
  <script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
  <script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script> -->
<!-- <link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
<link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet"> -->
<script type="text/javascript">
    //last month chart data
    var LAST_MONTH_CHART_DATES =  <?php echo json_encode($this->last_month_chart_array[0]); ?>;
    var LAST_MONTH_CHART_DATES_VALUES =  <?php echo json_encode($this->last_month_chart_array[1]); ?>;

    //last week chart data
    var LAST_WEEK_CHART_DATES =  <?php echo json_encode($this->last_week_chart_array[0]); ?>;
    var LAST_WEEK_CHART_DATES_VALUES =  <?php echo json_encode($this->last_week_chart_array[1]); ?>;
</script>
<div style="display:none">
    <canvas id="myChart" height="1" width="1"></canvas>
</div>
<script src="<?php echo base_url(); ?>assets/js/chart.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js//index.js"></script>


<!--date filter only---------------->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="Stylesheet" type="text/css" />
<script type="text/javascript">
     
    var min_date_selectection = new Date(2021,1-1,1);
    
    var current_year = new Date().getFullYear();
    var $j = jQuery.noConflict();
    $j("#fromdate").datepicker({
        dateFormat: 'yy-mm-dd',
       // minDate: min_date_selectection,
        //maxDate: ''+current_year+'-12-31',
        // numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate());
            $j("#todate").datepicker("option", "minDate", dt);
        }
    });
    $j("#todate").datepicker({
        dateFormat: 'yy-mm-dd',
        //minDate: min_date_selectection,
       // maxDate: ''+current_year+'-12-31',
        // numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() - 1);
            // $j("#fromdate").datepicker("option", "maxDate", dt);
        }
    });
</script>
