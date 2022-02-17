<canvas id="reportGraph" style="margin-top: 12px;"></canvas>
<!-- <pre> -->
<?php //print_r($report_chart);?>
<script type="text/javascript">
     var MONTH_NAME_LABLE =  <?php echo json_encode($report_chart['month_name']); ?>;
     var with_commission_data =  <?php echo json_encode($report_chart['with_commission']); ?>;
     var with_out_commission_data =  <?php echo json_encode($report_chart['with_out_commission']); ?>;
     var gross_sale_data =  <?php echo json_encode($report_chart['gross_sale']); ?>;
</script>
<script>
    var ctx = document.getElementById('reportGraph').getContext('2d');
    
    var with_commission_bg_color = ctx.createLinearGradient(0, 0, 0, 400);
    with_commission_bg_color.addColorStop(0, 'rgba(188,241,236,.8)');
    with_commission_bg_color.addColorStop(1, 'rgba(188,241,236,0.1)');
    
    var without_commission_bg_color = ctx.createLinearGradient(0, 0, 0, 400);
    without_commission_bg_color.addColorStop(0, 'rgba(255,90,0,1)');
    without_commission_bg_color.addColorStop(1, 'rgba(255,90,0,1)');
    
    var gross_sale_bg_color = ctx.createLinearGradient(0, 0, 0, 400);
    gross_sale_bg_color.addColorStop(0, 'rgba(144,140,189,.8)');
    gross_sale_bg_color.addColorStop(1, 'rgba(144,140,189,0.1)');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [{
                data: with_commission_data,
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
                data: with_out_commission_data,
                label: 'Without Commission',
                
                backgroundColor: 'transparent', 
                borderWidth: 3,
                borderColor: '#ff5a00',
                pointBorderWidth: 0,
                pointBorderColor: '#ff5a00',
                pointRadius: 3,
                pointBackgroundColor: '#ff5a00',
                pointHoverBackgroundColor: '#ff5a00',
            }, {
                data: gross_sale_data,
                label: 'Gross Sales',
                
                backgroundColor: 'transparent', 
                borderWidth: 3,
                borderColor: '#908cbd',
                pointBorderWidth: 0,
                pointBorderColor: '#908cbd',
                pointRadius: 3,
                pointBackgroundColor: '#908cbd',
                pointHoverBackgroundColor: '#908cbd',
            }],
            labels: MONTH_NAME_LABLE
            /*data: [20, 50, 100, 75, 25, 0,30, 70, 60,50, 30, 70, 60],
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
                    data: [10, 90, 80, 50, 90, 20,30, 70, 60,50, 30, 70, 60],
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
                    data: [0, 17, 50, 30, 70, 60,50, 30, 70, 60,30, 70, 60,50, 30, 70, 60],
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
                labels: ['Jan 2020 ', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jan 2021', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jan 2022', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jan 2023', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Feb', 'Mar', 'Apr', 'May', 'Jun']*/
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
</script>
