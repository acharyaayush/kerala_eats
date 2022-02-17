<?php
   defined('BASEPATH') OR exit('No direct script access allowed');
 ?>
 
<div class="main-sidebar sidebar-style-2">
   <aside id="sidebar-wrapper">
      <div class="sidebar-brand logo">
         <?php if($this->role && $this->role == 2) { ?>
         <a href="<?php echo base_url(); ?>admin/index"><img src="<?php echo base_url(); ?>/assets/images/merchant-logo.png"></a>
         <?php } ?>
         <?php if($this->role && $this->role == 1) { ?>
         <a href="<?php echo base_url(); ?>admin/index"><img src="<?php echo base_url(); ?>/assets/img/logo.png"></a>
         <?php } ?>
      </div>
      <div class="sidebar-brand sidebar-brand-sm favicons">
         <a href="<?php echo base_url(); ?>admin/index"><img src="<?php echo base_url(); ?>/assets/img/favicon.png"></a>
      </div>
      <ul class="sidebar-menu">

        <?php if($this->role && $this->role == 2) { ?>
        <!-- Restaurant Admin Side bar menu -->

        <!--  <li class="active <?php echo $this->uri->segment(2) == '' ? 'active' : ''; ?>">
            <a class="nav-link has-dropdown" data-toggle="dropdown" href="<?php echo base_url(); ?>"><i class="fas fa-bullhorn"></i> <span>Restaurants</span></a>
            <ul class="dropdown-menu"> -->
              <!--  <li class=" <?php echo $this->uri->segment(2) == 'index' || $this->uri->segment(2) == 'index' || $this->uri->segment(2) == 'index' ? 'active' : ''; ?>">
                  <a href="<?php echo base_url(); ?>admin/index"><i class="fas fa-fire"></i><span>Dashboard</span></a>
               </li> -->
               <li class="<?php echo $this->uri->segment(2) == 'index' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/index"><i class="fas fa-fire"></i> <span>Dashboard</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'products' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/products/<?php if(isset($this->selected_restaurant_id_url)){echo $this->selected_restaurant_id_url;}?>"><i class="fas fa-list-ol"></i> <span>Products</span></a></li>
               <li class=" <?php echo $this->uri->segment(2) == 'orders' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem "  href="<?php echo base_url(); ?>admin/orders"><i class="fas fa-shopping-basket"></i> <span>Orders</span><span id="update_blinker"></span></a></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'business' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/business"><i class="fas fa-business-time"></i> <span>Business</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'promotions' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/promo_codes"><i class="fas fa-bullhorn"></i> <span>Promotions</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'add_edit_restaurant' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url('admin/add_edit_restaurant/2/'.$this->logged_in_restaurant_id.'/'.$this->id); ?>"><i class="far fa-user"></i> <span>Profile</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'reservations' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/reservations"><i class="fas fa-chair"></i> <span>Reservations</span><span id="update_blinker_reservation"></span></a></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'review_listnig' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/review_listnig"><i class="fas fa-star"></i> <span>Ratings and reviews</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'login' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
               <!--  <ul class="dropdown-menu"> -->
              <!--  <li class="<?php echo $this->uri->segment(2) == 'past_orders' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo base_url(); ?>admin/past_orders"><i class="far fa-hand-point-right"></i> <span>Past Orders</span></a></li> -->
           <!--  </ul>
         </li> -->
         <?php } ?>
        
        <?php if($this->role && $this->role == 1) { ?>

         <!-- Master Admin Side bar menu -->
        <!--  <li class="active <?php echo $this->uri->segment(2) == '' ? 'active' : ''; ?>">
            <a class="nav-link has-dropdown" data-toggle="dropdown" href="<?php echo base_url(); ?>"><i class="fas fa-bullhorn"></i> <span>Admin</span></a>
            <ul class="dropdown-menu"> -->
               <li class="<?php echo $this->uri->segment(2) == 'index' || $this->uri->segment(2) == '' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/index"><i class="fas fa-fire"></i> <span>Dashboard</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'restaurant_list' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/restaurant_list"><i class="fas fa-utensils"></i> <span>Restaurant</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'admin_product' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/products/<?php if(isset($this->selected_restaurant_id_url)){echo $this->selected_restaurant_id_url;}?>"><i class="fas fa-list-ol"></i> <span>Products</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'promo_codes' || $this->uri->segment(2) == 'referral' || $this->uri->segment(2) == 'ad_banner_list' || $this->uri->segment(2) == 'discount' || $this->uri->segment(2) == 'promotion_cashback' ? 'active' : ''; ?>">
                  <a class="nav-link adminMenuItem has-dropdown" data-toggle="dropdown" href="<?php echo base_url(); ?>"><i class="fas fa-bullhorn"></i> <span>Promotions</span></a>
                  <ul class="dropdown-menu">
                     <li class="<?php echo $this->uri->segment(2) == 'promo_codes' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo base_url(); ?>admin/promo_codes"><i class="far fa-hand-point-right"></i> <span>Promo Codes</span></a></li>
                     <li class="<?php echo $this->uri->segment(2) == 'referral' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo base_url(); ?>admin/referral"><i class="far fa-hand-point-right"></i> <span>Referral</span></a></li>
                     <li class="<?php echo $this->uri->segment(2) == 'ad_banner_list' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo base_url(); ?>admin/ad_banner_list"><i class="far fa-hand-point-right"></i> <span>Ad Banner</span></a></li>
                     <li class="<?php echo $this->uri->segment(2) == 'discount' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo base_url(); ?>admin/discount/<?php if(isset($this->selected_restaurant_id)){ echo $this->selected_restaurant_id;}?>"><i class="far fa-hand-point-right"></i> <span>Discount</span></a></li>
                     <li class="<?php echo $this->uri->segment(2) == 'promotion_cashback' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo base_url(); ?>admin/promotion_cashback"><i class="far fa-hand-point-right"></i> <span>Cashback</span></a></li>
                  </ul>
               </li>
               <li class="<?php echo $this->uri->segment(2) == 'setting' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/setting"><i class="fas fa-cogs"></i> <span>Setting</span></a></li>
               <!-- <li class="<?php echo $this->uri->segment(2) == 'orders' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/orders"><i class="fas fa-shopping-basket"></i> <span>Orders</span><div class="blinking"><p class="circ"></p></div></a></li> -->
               <li class="<?php echo $this->uri->segment(2) == 'orders' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/orders"><i class="fas fa-shopping-basket"></i> <span>Orders</span><span id="update_blinker"></span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'hear_about' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/hear_about"><i class="fas fa-cogs"></i> <span>Hear about</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'AllUser' ? 'active' : ''; ?>">
                  <a class="nav-link adminMenuItem has-dropdown" data-toggle="dropdown" href="<?php echo base_url(); ?>"><i class="far fa-user"></i> <span>All User</span></a>
                  <ul class="dropdown-menu">
                     <li class="<?php echo $this->uri->segment(3) == '3' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo base_url(); ?>admin/AllUser/3/all/"><i class="far fa-hand-point-right"></i> <span>Customer</span></a></li>
                      <li class="<?php echo $this->uri->segment(3) == '2' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo base_url(); ?>admin/AllUser/2/all/"><i class="far fa-hand-point-right"></i> <span>Merchant</span></a></li>
                     
                  </ul>
               </li>
                <li class="<?php echo $this->uri->segment(2) == 'analytics' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/analytics"><i class="fas fa-business-time"></i> <span>Analytics</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'reservations' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/reservations"><i class="fas fa-chair"></i> <span>Reservations</span><span id="update_blinker_reservation"></span></a></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'review_listnig' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/review_listnig"><i class="fas fa-star"></i> <span>Ratings and reviews</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'faq' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/faq"><i class="fas fa-info-circle"></i> <span>FAQ</span></a></li>
               <li class="<?php echo $this->uri->segment(2) == 'cms' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/cms"><i class="far fa-newspaper"></i> <span>Content Management</span></a></li>
                <li class="<?php echo $this->uri->segment(2) == 'login' ? 'active' : ''; ?>"><a class="nav-link adminMenuItem" href="<?php echo base_url(); ?>admin/logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>

           <!--  </ul>
          </li> -->
          <?php } ?>
      </ul>
   </aside>
</div>