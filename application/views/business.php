<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Business</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
                <div class="breadcrumb-item">Business</div>
            </div>
        </div>
        <div class="business-body">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header order-status">
                            <h4>Order Status</h4>
                        </div>
                        <div class="card-body order-status-graph">
                            <div class="row">
                                <div class="col-md-12">
                                    <canvas id="orderStatus"></canvas>
<!--                                    <div id="orderStatus" class="piecharts" style="width: 300px; height: 250px;"></div>-->
                                </div>
                                <div class="col-md-12">
                                    <div class="chart-completed-score">
                                        <h5>90%</h5>
                                        <p>Order Completed</p>
                                    </div>
                                    <div class="chart-cancelled-score">
                                        <h5>10%</h5>
                                        <p>Order Cancelled</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header product_sales">
                            <h4>Product Sales</h4>
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
                        <div class="card-body">
                            <div id="spline-area-chart-example"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header product_sales">
                        <h4>Revenue Chart</h4>
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
                    <div class="card-body">
                        <div id="spline-chart-example"></div>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
</div>

<!-- Revenue Chart JS -->
<script src="<?php echo base_url(); ?>assets/js/d3.v5.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/pluscharts.js"></script>
<script type="text/javascript">
//    orderStatus
    window.onload = function() {
    var ctx = document.getElementById('orderStatus').getContext('2d')
    var myPieChart = new Chart(ctx, {
        type: 'pie',
        data : {
            datasets: [{
                data: [30, 8],
                backgroundColor: [
                    '#42DDCD',
                    '#F04370'
                ],
            }],

            // These labels appear in the legend and in the tooltips when hovering different arcs
            labels: [
                'Completed',
                'Cancelled'
            ]
        },
//        options: options
    });
    }
    pluscharts.draw({
        drawOn: "#spline-chart-example",
        type: "spline",
        dataset: {
            data: [{
                    label: 10,
                    value: 20
                },
                {
                    label: 20,
                    value: 50
                },
                {
                    label: 30,
                    value: 30
                },
                {
                    label: 40,
                    value: 10
                },
                {
                    label: 50,
                    value: 100
                },
                {
                    label: 60,
                    value: 60
                },
                {
                    label: 70,
                    value: 80
                },
                {
                    label: 80,
                    value: 50
                },
                {
                    label: 100,
                    value: 70
                }
            ],
            lineColor: "#fa5a02",
            lineWidth: 2,
            legendLabel: "Revenue Chart"
        },
        options: {
            text: {
                display: false,
                color: "#6c478c"
            },
            points: {
                display: true,
                radius: 3
            },
            axes: {
                x: {
                    display: true,
                    scale: 3,
                    min: 0,
                    max: 100
                },
                y: {
                    display: true,
                    scale: 3,
                    min: 0,
                    max: 100
                }
            },
            legends: {
                display: true,
                width: 20,
                height: 20
            },
            size: {
                width: '1500', //give 'container' if you want width and height of initiated container
                height: '600'
            }
        }
    })

</script>

<script type="text/javascript">
    pluscharts.draw({
        drawOn: "#spline-area-chart-example",
        type: "spline-area",
        dataset: {
            data: [{
                    label: 10,
                    value: 20
                },
                {
                    label: 20,
                    value: 30
                },
                {
                    label: 30,
                    value: 50
                },
                {
                    label: 40,
                    value: 35
                },
                {
                    label: 50,
                    value: 30
                },
                {
                    label: 60,
                    value: 60
                },
                {
                    label: 70,
                    value: 75
                },
                {
                    label: 80,
                    value: 40
                },
                {
                    label: 90,
                    value: 60
                },
                {
                    label: 100,
                    value: 70
                },
                {
                    label: 110,
                    value: 90
                },
                {
                    label: 120,
                    value: 60
                },
                {
                    label: 130,
                    value: 80
                },
                {
                    label: 140,
                    value: 50
                },
                {
                    label: 150,
                    value: 60
                },
                {
                    label: 160,
                    value: 20
                }
            ],
            lineColor: "#fa5a02",
            lineWidth: 2,
            fillColor: "#f9b5c2",
            legendLabel: "Product Sales"
        },
        options: {
            text: {
                display: false,
                color: "#6c478c"
            },
            points: {
                display: true,
                radius: 3
            },
            axes: {
                x: {
                    display: true,
                    scale: 3,
                    min: 0,
                    max: 160
                },
                y: {
                    display: true,
                    scale: 3,
                    min: 0,
                    max: 100
                }
            },
            legends: {
                display: true,
                width: 20,
                height: 20
            },
            size: {
                width: '1000', //give 'container' if you want width and height of initiated container
                height: '400'
            }
        }
    })

</script>
