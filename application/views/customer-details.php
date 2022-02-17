<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Customer Details</h1>
         <span>
            <div class="col-md-2">
               <!-- Pass 1 as parameter to call add user form -->
               <!--  <a href="#" class="btn btn-primary">Create Order</a> -->
            </div>
         </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Customer Details</div>
         </div>
      </div>
       <div class="section-body customer-details-sec">
         <div class="row">
            <div class="col-md-3">
               <p>ID</p>
               <h3>3116654</h3>
            </div>
            <div class="col-md-3">
               <p>Name</p>
               <h3>admin</h3>
            </div>
            <div class="col-md-3">
               <p>Email</p>
               <h3>admin@gmail.com</h3>
            </div>
            <div class="col-md-3">
               <p>Phone</p>
               <h3>+654289625</h3>
            </div>
         </div>
          <div class="row">
            <div class="col-md-3">
               <p>Registration Date</p>
               <h3>11-03-2021 10:11 AM</h3>
            </div>
            <div class="col-md-3">
               <p>Outstanding Amount</p>
               <h3>S$0.00</h3>
            </div>
            <div class="col-md-3">
               <p><a href="<?php echo base_url(); ?>admin/wallet">Wallet Balance</a></p>
               <h3>S$0.00</h3>
            </div>
         </div>
       </div>
       <div class="promo-code-list">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                  
                  <div class="card-header user_tables">
                  <div class="row">
                     <div class="form-group col-md-12 admin-input-field">
                      <h1 class="product_wise_heading" style="font-size: 20px;">Order Details</h1>
                     </div>
                    
                  </div>
               </div>
                  <div class="card-body table-flip-scroll orders-tables">
                     <table class="table">
                        <thead>
                           <tr>
                              <th>Order ID</th>
                              <th>Status</th>
                              <th>Customer</th>
                              <th>Delivery Mode</th>
                              <th>Order Time </th>
                              <th>Schedule Time</th>
                              <th>Payment Method</th>
                              <th>Order Preparation Time(In Minutes) <i class="fas fa-info-circle"
                              data-toggle="tooltip" data-placement="top"
                              title="It is the time which a restaurant takes to prepare an order and can be edited before accepting that order." style="color: #666; font-size: 17px;"
                              ></i></th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td>5114562</td>
                              <td>Completed</td>
                              <td>test</td>
                              <td>Home delivery</td>
                              <td>11-03-2021 9:40 AM</td>
                              <td>11-03-2021 01:00 PM</td>
                              <td>STRIPE</td>
                              <td>0</td>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>