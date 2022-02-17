<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title><?php echo APP_NAME .' | '?><?php if(isset($title)){echo $title;}?> <?php if(isset($pageTitle)){echo $pageTitle;}?></title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/all.min.css">
  <link rel="icon" href="<?php echo base_url(); ?>/assets/img/favicon.png" type="image" sizes="16x16">

  <link rel="stylesheet" href="https://res.cloudinary.com/dxfq3iotg/raw/upload/v1569006288/BBBootstrap/choices.min.css?version=7.0.0">
  <script src="https://res.cloudinary.com/dxfq3iotg/raw/upload/v1569006273/BBBootstrap/choices.min.js?version=7.0.0"></script>

  <!-- CSS Libraries -->
<?php
if ($this->uri->segment(2) == "" || $this->uri->segment(2) == "index") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/summernote/summernote-bs4.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">
<?php
}elseif ($this->uri->segment(2) == "index_0") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/weather-icon/css/weather-icons.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/weather-icon/css/weather-icons-wind.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/summernote/summernote-bs4.css">
<?php
}elseif ($this->uri->segment(2) == "bootstrap_card") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/chocolat/dist/css/chocolat.css">
<?php
}elseif ($this->uri->segment(2) == "bootstrap_modal") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/prism/prism.css">
<?php
}elseif ($this->uri->segment(2) == "components_gallery") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/chocolat/dist/css/chocolat.css">
<?php
}elseif ($this->uri->segment(2) == "components_multiple_upload") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/dropzonejs/dropzone.css">
<?php
}elseif ($this->uri->segment(2) == "components_statistic") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/flag-icon-css/css/flag-icon.min.css">
<?php
}elseif ($this->uri->segment(2) == "components_user") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-social/bootstrap-social.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">
<?php
}elseif ($this->uri->segment(2) == "forms_advanced_form") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/jquery-selectric/selectric.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">
<?php
}elseif ($this->uri->segment(2) == "forms_editor") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/summernote/summernote-bs4.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/codemirror/lib/codemirror.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/codemirror/theme/duotone-dark.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/jquery-selectric/selectric.css">
<?php
}elseif ($this->uri->segment(2) == "modules_calendar") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fullcalendar/fullcalendar.min.css">
<?php
}elseif ($this->uri->segment(2) == "modules_datatables") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/datatables/datatables.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css">
<?php
}elseif ($this->uri->segment(2) == "modules_ion_icons") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/ionicons/css/ionicons.min.css">
<?php
}elseif ($this->uri->segment(2) == "modules_owl_carousel") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">
<?php
}elseif ($this->uri->segment(2) == "modules_toastr") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/izitoast/css/iziToast.min.css">
<?php
}elseif ($this->uri->segment(2) == "modules_vector_map") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/izitoast/css/iziToast.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/flag-icon-css/css/flag-icon.min.css">
<?php
}elseif ($this->uri->segment(2) == "modules_weather_icon") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/weather-icon/css/weather-icons.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/weather-icon/css/weather-icons-wind.min.css">
<?php
}elseif ($this->uri->segment(2) == "auth_login") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-social/bootstrap-social.css">
<?php
}elseif ($this->uri->segment(2) == "auth_register") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/jquery-selectric/selectric.css">
<?php
}elseif ($this->uri->segment(2) == "features_post_create") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/summernote/summernote-bs4.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/jquery-selectric/selectric.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">
<?php
}elseif ($this->uri->segment(2) == "features_posts") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/jquery-selectric/selectric.css">
<?php
}elseif ($this->uri->segment(2) == "features_profile") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-social/bootstrap-social.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/summernote/summernote-bs4.css">
<?php
}elseif ($this->uri->segment(2) == "features_setting_detail") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/codemirror/lib/codemirror.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/codemirror/theme/duotone-dark.css">
<?php
}elseif ($this->uri->segment(2) == "features_tickets") { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/summernote/summernote-bs4.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/chocolat/dist/css/chocolat.css">
<?php
} ?>

  <!-- Template CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/components.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css">
 <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/pie/jquery.simple-bar-graph.min.css" />
 <link rel="stylesheet" href="<?php echo base_url()?>assets/css/richtext.min.css">
 <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.6/css/intlTelInput.css">

 <!-- <link rel="stylesheet" type="text/css" href="https://www.pluscharts.com/pluscharts/src/css/pluscharts.css"> -->
 <!-- <link rel="stylesheet" type="text/css" href="https://www.pluscharts.com/pluscharts/demo/demo.css"> -->
 <link rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/css/pluscharts.css">

<!-- Start GA -->

<!-- Order Status Chart -->
 <!-- <script src="<?php echo base_url(); ?>assets/js/loader.js"></script> -->
<!-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> -->
<!-- <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Language', 'Speakers (in millions)'],
          ['Order Completed',  90],
          ['Order Cancelled',  10]
        ]);


      var options = {
        legend: 'none',
        pieSliceText: 'label',
        title: '',
        pieStartAngle: 100,
      };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
      }
</script> -->


<!-- datetime picker Script -->


<!-- <script src="<?php //echo base_url();?>assets/ckeditor/ckeditor.js"></script> -->
<!-- 
 <script src="<?php echo base_url(); ?>assets/js/page/index-0.js"></script>
 <script src="<?php echo base_url(); ?>assets/js/canvasjs.min.js"></script>
 <script src="<?php echo base_url(); ?>assets/js/popper.min.js"></script>
 <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script> -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
<!-- <script src="assets/js/pie/jquery.simple-bar-graph.min.js"></script> -->
<!-- <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>  -->
<!-- <script  src="assets/js/pie/pieChart.js"></script>
<script>
$(document).ready(function () {
  $('input[name="intervaltype"]').click(function () {
      $(this).tab('show');
      $(this).removeClass('active');
  });
})
</script>
<script>
$(document).ready(function () {
    $(".check_space").keydown(function(e){
        if (e.which === 32 &&  e.target.selectionStart === 0) {
            return false;
        }
    });
})
</script> -->
<!-- <script>
$(document).ready(function () {
     $('#example-one').simpleBarGraph({
               data: [
             { key: 'Jan', value: 100 },
             { key: 'Feb', value: 95 },
             { key: 'Mar', value: 96 },
             { key: 'Apr', value: 44 },
             { key: 'May', value: 32 },
             { key: 'Jun', value: 55 },
             { key: 'Jul', value: 55 },
             { key: 'Aug', value: 55 },
             { key: 'Sep', value: 55 },
             { key: 'Oct', value: 55 },
             { key: 'Nov', value: 55 },
             { key: 'Dec', value: 91 }
         ],height: "200px",
               barsColor: '#222',
           });
})
</script> -->

<!-- Tooltip Script -->
<!-- <script>
 $(document).on('mouseenter', ".iffyTip", function () {
     var $this = $(this);
     if (this.offsetWidth < this.scrollWidth && !$this.attr('title')) {
         $this.tooltip({
             title: $this.text(),
             placement: "top"
         });
         $this.tooltip('show');
     }
 });
$('.hideText').css('width',$('.hideText').parent().width());
</script>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-94034622-3');
</script> -->
<!-- /END GA -->


<script type="text/javascript">
    var BASE_URL = "<?php echo base_url();?>";
</script>
 <script src="<?php echo base_url(); ?>assets/modules/sweetalert/sweetalert.min.js"></script>
         <script src="<?php echo base_url();?>assets/ckeditor/ckeditor.js"></script>
         
<script type="text/javascript">
    var BASE_URL = "<?php echo base_url();?>";
</script>
<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-messaging.js"></script>
<script src="<?php echo base_url();?>assets/js/firebase/firebase.js"></script>


<style>

nav.navbar.navbar-expand-lg.main-navbar{
<?php
  if($this->role ==1){

  ?>
  background-color:  #FF5A00;
  <?php }else if($this->role ==2){?>
  background-color: #F04370;

  <?php }?>
}
.main-sidebar .sidebar-menu li a:hover{
  <?php
  if($this->role ==1){

  ?>
  background-color: transparent;
  color: #FF5A00;
  <?php }else if($this->role ==2){?>
  background-color: transparent;
  color: #f04370;

  <?php }?>
}

.main-sidebar .sidebar-menu li.active a.adminMenuItem {
  <?php
  if($this->role ==1){

  ?>
  color: #FF5A00 !important;
  <?php }else if($this->role ==2){?>
  color: #f04370 !important;

  <?php }?>
}

.main-sidebar .sidebar-menu li ul.dropdown-menu li.active > a{
  <?php
  if($this->role ==1){

  ?>
  color: #FF5A00 !important;
  <?php }else if($this->role ==2){?>
  color: #f04370 !important;

  <?php }?>
}
 body.sidebar-mini .main-sidebar .sidebar-menu > li.active > a.adminMenuItem{
  <?php
  if($this->role ==1){

  ?>
  box-shadow: 0 4px 8px #ff5a008a !important;
  background-color: #ff5a00 !important;
  color: #fff !important;
  <?php }else if($this->role ==2){?>
  box-shadow: 0 4px 8px #f043709e !important;
  background-color: #F04370 !important;
  color: #fff !important;

  <?php }?>
}
.section .section-header .btn{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00;
  border-color: #ff5a00;
  box-shadow: 0 2px 6px #ff5a00a1;
  <?php }else if($this->role ==2){?>
  background: #F04370;
  border-color: #F04370;
  box-shadow: 0 2px 6px #f04370b0;

  <?php }?>
}
.section .section-header .btn:hover{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00c9 !important;
  border-color: #ff5a00 !important;
  box-shadow: 0 2px 6px #ff5a00a1 !important;
  <?php }else if($this->role ==2){?>
  background: #f04370d9 !important;
  border-color: #F04370 !important;
  box-shadow: 0 2px 6px #f04370b0 !important;

  <?php }?>
}
.add_product button{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00 !important;
  border-color: #ff5a00 !important;
  box-shadow: none !important;
  <?php }else if($this->role ==2){?>
  background: #F04370 !important;
  border-color: #F04370 !important;
  box-shadow: none !important;

  <?php }?>
}
.add_variant_btn{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00 !important;
  border-color: #ff5a00 !important;
  box-shadow: 0 2px 6px #ff5a00a1 !important;
  <?php }else if($this->role ==2){?>
  background: #F04370 !important;
  border-color: #F04370 !important;
  box-shadow: 0 2px 6px #f04370b0 !important;

  <?php }?>
}
.user_tables .users_btns .btn.btn-primary{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00 !important;
  border-color: #ff5a00 !important;
  box-shadow: 0 2px 6px #ff5a00a1 !important;
  <?php }else if($this->role ==2){?>
  background: #F04370 !important;
  border-color: #F04370 !important;
  box-shadow: 0 2px 6px #f04370b0 !important;

  <?php }?>
}
button.btn.btn-primary.filter_button{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00 !important;
  border-color: #ff5a00 !important;
  box-shadow: none !important;
  <?php }else if($this->role ==2){?>
  background: #F04370 !important;
  border-color: #F04370 !important;
  box-shadow: none !important;

  <?php }?>
}
.add_product button:hover{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00c9 !important;
  <?php }else if($this->role ==2){?>
  background: #f04370d9 !important;
  <?php }?>
}
.button.btn.btn-primary.modal_btns:hover{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00c9 !important;
  <?php }else if($this->role ==2){?>
  background: #f04370d9 !important;
  <?php }?>
}
.btn-primary:active, .btn-primary:hover, .btn-primary.disabled:active, .btn-primary.disabled:hover{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00 !important;
  <?php }else if($this->role ==2){?>
  background: #F04370 !important;
  <?php }?>
}
.change-password-btns{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00;
  border-color: #ff5a00;
  box-shadow: 0 2px 6px #ff5a00a1;
  <?php }else if($this->role ==2){?>
  background: #F04370;
  border-color: #F04370;
  box-shadow: 0 2px 6px #f04370b0;

  <?php }?>
}
.table a {

  <?php
  if($this->role ==1){

  ?>
  color: #ff5a00;
  <?php }else if($this->role ==2){?>
  color: #F04370;
  <?php }?>
}
.promocode-status input:checked + .slider {
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00;
  <?php }else if($this->role ==2){?>
  background: #F04370;
  <?php }?>
}
.enabled-label input:checked ~ .checkmark {
  <?php
  if($this->role ==1){

  ?>
  border: 3px solid #ff5a00;
  <?php }else if($this->role ==2){?>
  border: 3px solid #F04370;
  <?php }?>
}
.enabled-label .checkmark:after {
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00;
  <?php }else if($this->role ==2){?>
  background: #F04370;
  <?php }?>
}
.enabled-label .checkmark_check:after {
  <?php
  if($this->role ==1){

  ?>
  color: #ff5a00;
  <?php }else if($this->role ==2){?>
  color: #F04370;
  <?php }?>
}
.checkmark_check {
  <?php
  if($this->role ==1){

  ?>
  border: 3px solid #ff5a00;
  <?php }else if($this->role ==2){?>
  border: 3px solid #F04370;
  <?php }?>
}
.breadcrumb-item a {
  <?php
  if($this->role ==1){

  ?>
  color: #ff5a00;
  <?php }else if($this->role ==2){?>
  color: #F04370;
  <?php }?>
}
button.btn.btn-primary.modal_btns{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00;
  border-color: #ff5a00;
  box-shadow: 0 2px 6px #ff5a00a1;
  <?php }else if($this->role ==2){?>
  background: #F04370;
  border-color: #F04370;
  box-shadow: 0 2px 6px #f04370b0;

  <?php }?>
}
.order-list-icons i.fas.fa-print{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00;
  <?php }else if($this->role ==2){?>
  background: #F04370;

  <?php }?>
}
.linked_colr_a{
  <?php
  if($this->role ==1){

  ?>
  color: #ff5a00;
  <?php }else if($this->role ==2){?>
  color: #F04370;

  <?php }?>
}
.customer-details i.fas.fa-pencil-alt{
  <?php
  if($this->role ==1){

  ?>
  color: #ff5a00 !important;
  <?php }else if($this->role ==2){?>
  color: #F04370 !important;

  <?php }?>
}
button.add-item-btns{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00;
  border-color: #ff5a00;
  box-shadow: 0 2px 6px #ff5a00a1;
  <?php }else if($this->role ==2){?>
  background: #F04370;
  border-color: #F04370;
  box-shadow: 0 2px 6px #f04370b0;

  <?php }?>
}
.modal-header{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00;
  <?php }else if($this->role ==2){?>
  background: #F04370;

  <?php }?>
}
.btn-primary:focus, .btn-primary.disabled:focus{
  <?php
  if($this->role ==1){

  ?>
  background: #ff5a00 !important;
  <?php }else if($this->role ==2){?>
  background: #F04370 !important;

  <?php }?>
}
.setting-section .tab button.active{
  <?php
  if($this->role ==1){

  ?>
  border-bottom: 3px solid #ff5a00;
  <?php }else if($this->role ==2){?>
  border-bottom: 3px solid #F04370;

  <?php }?>
}
body:not(.sidebar-mini) .sidebar-style-2 .sidebar-menu > li.active > a:before{
  <?php
  if($this->role ==1){

  ?>
  background-color: #ff5a00;
  <?php }else if($this->role ==2){?>
  background-color: #F04370;

  <?php }?>
}
label.addimageplus i.fas.fa-plus{
  <?php
  if($this->role ==1){

  ?>
  color: #ff5a00;
  <?php }else if($this->role ==2){?>
  color: #F04370;

  <?php }?>
}

input#before_crop_image {
    position: absolute;
    left: 0;
    width: 118px;
    height: 97px;
    z-index: 99;
    opacity: 0;
}
</style>
</head>

<?php
if ($this->uri->segment(2) == "layout_transparent") {
  $this->load->view('dist/_partials/layout-2');
  $this->load->view('dist/_partials/sidebar-2');
}elseif ($this->uri->segment(2) == "layout_top_navigation") {
  $this->load->view('dist/_partials/layout-3');
  $this->load->view('dist/_partials/navbar');
}elseif ($this->uri->segment(2) != "auth_login" && $this->uri->segment(2) != "auth_forgot_password"&& $this->uri->segment(2) != "auth_register" && $this->uri->segment(2) != "auth_reset_password" && $this->uri->segment(2) != "errors_503" && $this->uri->segment(2) != "errors_403" && $this->uri->segment(2) != "errors_404" && $this->uri->segment(2) != "errors_500" && $this->uri->segment(2) != "utilities_contact" && $this->uri->segment(2) != "utilities_subscribe") {
  $this->load->view('dist/_partials/layout');
  $this->load->view('dist/_partials/sidebar');
}
?>

 <?php if(isset($pageName)){
    $this->load->view($pageName);
  }?>

  
<footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2021 <div class="bullet"></div> Design By <a href="#"></a>
        </div>
        <div class="footer-right">
          
        </div>
      </footer>
    </div>
  </div>

<?php $this->load->view('dist/_partials/js'); ?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/jquery.Jcrop.min.css" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.Jcrop.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.SimpleCropper.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css" />

<script>
    
    var BASE_URL = "<?php echo base_url();?>";

var $modal = $('#modal');
// var image = document.getElementById('image');
// var cropper;
  
// $("body").on("change", ".image", function(e){
//     var files = e.target.files;
//     var done = function (url) {
//       image.src = url;
//       $modal.modal('show');
//     };
//     var reader;
//     var file;
//     var url;

//     if (files && files.length > 0) {
//       file = files[0];

//       if (URL) {
//         done(URL.createObjectURL(file));
//       } else if (FileReader) {
//         reader = new FileReader();
//         reader.onload = function (e) {
//           done(reader.result);
//         };
//         reader.readAsDataURL(file);
//       }
//     }
// });

// $modal.on('shown.bs.modal', function () {
//     cropper = new Cropper(image, {
//     aspectRatio: 1,
//     viewMode: 3,
//     preview: '.preview'
//     });
// }).on('hidden.bs.modal', function () {
//    cropper.destroy();
//    cropper = null;
// });

$("#crop").click(function(){
    canvas = cropper.getCroppedCanvas({
      width: 160,
      height: 160,
    });

    canvas.toBlob(function(blob) {
        url = URL.createObjectURL(blob);
        var reader = new FileReader();
         reader.readAsDataURL(blob); 
         reader.onloadend = function() {
            var base64data = reader.result;  
            // alert(BASE_URL)
            $.ajax({
                type: "POST",
                dataType: "json",
                url: BASE_URL+"upload.php",
                data: {image: base64data},
                success: function(data){
                    console.log(data);
                    $modal.modal('hide');
                    alert("success upload image");
                }
              });
         }
    });
})
$('.cropme').simpleCropper();
$(document).ready(function () {
            $image_crop = $('#img_prev').croppie({
                enableExif: true,
                viewport: {
                    width: 600,
                    height: 600,
                    type: 'square' // square
                },
                boundary: {
                    width: 620,
                    height: 620
                }
            });

            $('#img-crop').on('change', function () {
                var reader = new FileReader();
                reader.onload = function (event) {
                    $image_crop.croppie('bind', {
                        url: event.target.result
                    }).then(function () {
                        
                    });
                }
                reader.readAsDataURL(this.files[0]);
                $('#imageModel').modal('show');
            });

            $('.crop_my_image').click(function (event) {
                $image_crop.croppie('result', {
                    type: 'canvas',
                    size: 'viewport'
                }).then(function (response) {
                    $.ajax({
                        type: 'post',
                        url: "<?php echo base_url('ImageCrop/store'); ?>",
                        data: {
                            "image": response
                        },
                        success: function (data) {
                            console.log(data);
                            $('#imageModel').modal('hide');
                        }
                    })
                });
            });
        });
  
</script>