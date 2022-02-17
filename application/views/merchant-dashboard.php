<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-12 col-12 col-sm-12">
                <div class="total-sales">
                    <div class="card">
                        <div class="card-header">
                            <h4>Total Sales</h4>
                        </div>
                        <div class="graph-part card-statistic">
                            <div class="card-chart">
                                <canvas id="balance-chart" height="220"></canvas>
                            </div>
                            <p class="total-order-price"> $5000.00</p>
                            <p class="order-number"> 300 Order</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-12 col-sm-12">
                <div class="total-sales">
                    <div class="card">
                        <div class="card-header">
                            <h4>Last Month Sales</h4>
                        </div>
                        <div class="graph-part card-statistic">
                            <div class="card-chart">
                                <canvas id="sales-chart" height="220px"></canvas>
                            </div>
                            <!-- <div id="chartContainer" style="height: 210px; width: 450px"></div> -->
                            <p class="total-order-price"> $600.00</p>
                            <p class="order-number"> 50 Order</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-12 col-sm-12">
                <div class="total-sales">
                    <div class="card">
                        <div class="card-header">
                            <h4>Last Week Sales</h4>
                        </div>
                        <div class="graph-part card-statistic">
                            <div class="card-chart">
                                <canvas id="week-sales" height="220px"></canvas>
                            </div>
                            <p class="total-order-price"> $200.00</p>
                            <p class="order-number"> 20 Order</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-12 col-12 col-sm-12">
                <div class="total-earning-box">
                    <div class="kl-ern">
                        <p class="earning-heading"> Total Earnings</p>
                    </div>
                    <div>
                        <p class="earning-price">$350.00</p>
                        <p class="total-earning-order">300 Order</p>
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
                        <p class="earning-price">$350.00</p>
                        <p class="total-earning-order">300 Order</p>
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
                        <p class="earning-price">$350.00</p>
                        <p class="total-earning-order">30 Order</p>
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
            </div>
            <div class="col-lg-9 col-md-12 col-12 col-sm-12">
                <div class="report-section">
                    <div class="card">
                        <div class="card-header">
                            <h4>Report</h4>
                            <div class="card-header-action">
                                <span class="business_date_field">
                                    <div class="datepikerInputP">
                                        <input type="text" id="fromdate" name="fromdate" max="" class="form-control datepicker fromdate wv_filter_box_height" value="" />
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
                                    <div class="datepikerInputP">
                                        <input type="text" id="todate" name="todate" max="" class="form-control datepicker fromdate wv_filter_box_height" value="" />
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
                                    <button class="btn" type="button">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <canvas id="reportGraph"></canvas>
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
<link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
<link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet">
<script>
 

    window.onload = function() {
        var ctx = document.getElementById('reportGraph').getContext('2d');
        
        var with_commission_bg_color = ctx.createLinearGradient(0, 0, 0, 400);
        with_commission_bg_color.addColorStop(0, 'rgba(188,241,236,.8)');
        with_commission_bg_color.addColorStop(1, 'rgba(188,241,236,0.1)');
        
        var without_commission_bg_color = ctx.createLinearGradient(0, 0, 0, 400);
        without_commission_bg_color.addColorStop(0, 'rgba(236,116,147,.8)');
        without_commission_bg_color.addColorStop(1, 'rgba(236,116,147,0.1)');
        
        var gross_sale_bg_color = ctx.createLinearGradient(0, 0, 0, 400);
        gross_sale_bg_color.addColorStop(0, 'rgba(144,140,189,.8)');
        gross_sale_bg_color.addColorStop(1, 'rgba(144,140,189,0.1)');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [{
                    data: [20, 50, 100, 75, 25, 0],
                    label: 'With Commission',
                    
                    backgroundColor: 'transparent', 
                    borderWidth: 3,
                    borderColor: '#bcf1ec',
                    pointBorderWidth: 0,
                    pointBorderColor: '#bcf1ec',
                    pointRadius: 3,
                    pointBackgroundColor: '#bcf1ec',
                    pointHoverBackgroundColor: '#bcf1ec',
                }, {
                    data: [10, 90, 80, 50, 90, 20],
                    label: 'Without Commission',
                    
                    backgroundColor: 'transparent', 
                    borderWidth: 3,
                    borderColor: '#ec7493',
                    pointBorderWidth: 0,
                    pointBorderColor: '#ec7493',
                    pointRadius: 3,
                    pointBackgroundColor: '#ec7493',
                    pointHoverBackgroundColor: '#ec7493',
                }, {
                    data: [0, 17, 50, 30, 70, 60],
                    label: 'Gross Sales',
                    
                    backgroundColor: 'transparent', 
                    borderWidth: 3,
                    borderColor: '#908cbd',
                    pointBorderWidth: 0,
                    pointBorderColor: '#908cbd',
                    pointRadius: 3,
                    pointBackgroundColor: '#908cbd',
                    pointHoverBackgroundColor: '#908cbd',
                }
                          ],
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
            },
            options: {
                scales: {
                  yAxes: [{
                    gridLines: {
                      display: false,
                      drawBorder: false,
                    },
                    ticks: {
                      beginAtZero: true,
//                      display: false
                    }
                  }],
                  xAxes: [{
                    gridLines: {
                      drawBorder: false,
                      display: false,
                    },
                    ticks: {
//                      display: false
                    }
                  }]
                },
            }
        });
    };



    //    anychart.onDocumentReady(function() {
    //        // create line chart
    //        var chart = anychart.line();
    //
    //        // set chart padding
    //        chart.padding([10, 20, 5, 20]);
    //
    //        // turn on chart animation
    //        chart.animation(true);
    //
    //        // turn on the crosshair
    //        chart.crosshair(true);
    //
    //        // set chart title text settings
    //        chart.title('');
    //
    //        // set y axis title
    //        chart.yAxis().title('Activity occurrences');
    //
    //        // create logarithmic scale
    //        var logScale = anychart.scales.log();
    //        logScale.minimum(1).maximum(45000);
    //
    //        // set scale for the chart, this scale will be used in all scale dependent entries such axes, grids, etc
    //        chart.yScale(logScale);
    //
    //        // create data set on our data,also we can pud data directly to series
    //        var dataSet = anychart.data.set([
    //            ['Monday', '20', '473', '176'],
    //            ['Tuesday', '490', '389', '440'],
    //            ['Wednesday', '404', '404', '104'],
    //            ['Thursday', '190', '454', '233'],
    //            ['Friday', '15', '187 ', '422'],
    //            ['Saturday', '10', '45', '434'],
    //            ['Sunday', '7', '61', '343']
    //        ]);
    //
    //        // map data for the first series,take value from first column of data set
    //        var firstSeriesData = dataSet.mapAs({
    //            x: 0,
    //            value: 1
    //        });
    //
    //        // map data for the second series,take value from second column of data set
    //        var secondSeriesData = dataSet.mapAs({
    //            x: 0,
    //            value: 2
    //        });
    //
    //        // map data for the third series, take x from the zero column and value from the third column of data set
    //        var thirdSeriesData = dataSet.mapAs({
    //            x: 0,
    //            value: 3
    //        });
    //
    //        // temp variable to store series instance
    //        var series;
    //
    //        // setup first series
    //        series = chart.line(firstSeriesData);
    //        series.name('With Commission');
    //        // enable series data labels
    //        series.labels().enabled(true).anchor('left-bottom').padding(5);
    //        // enable series markers
    //        series.markers(false);
    //
    //        // setup second series
    //        series = chart.line(secondSeriesData);
    //        series.name('Without Commission');
    //        // enable series data labels
    //        series.labels().enabled(true).anchor('left-bottom').padding(5);
    //        // enable series markers
    //        series.markers(false);
    //
    //        // setup third series
    //        series = chart.line(thirdSeriesData);
    //        series.name('Gross Sales');
    //        // enable series data labels
    //        series.labels().enabled(true).anchor('left-bottom').padding(5);
    //        // enable series markers
    //        series.markers(false);
    //        // turn the legend on
    //        chart.legend().enabled(true).fontSize(13).padding([0, 0, 20, 0]);
    //
    //        // set container for the chart and define padding
    //        chart.container('container');
    //        // initiate chart drawing
    //        chart.draw();
    //
    //    });

</script>
<div style="display:none">
    <canvas id="myChart" height="1" width="1"></canvas>
</div>
<!--<script src="<?php echo base_url(); ?>assets/js/chart.min.js"></script>-->
<!--<script src="<?php echo base_url(); ?>assets/js//index.js"></script>-->
