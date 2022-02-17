<canvas id="balance-chart" height="220"></canvas>

<script type="text/javascript">
    //total sales chart data
    var TOTAL_SALES_MONTHS =  <?php echo json_encode($total_sale_chart_array[0]); ?>;
    var TOTAL_SALES_MONTHS_VALUES =  <?php echo json_encode($total_sale_chart_array[1]); ?>;
</script>
 
<script src="<?php echo base_url(); ?>assets/js/chart.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js//index.js"></script>