<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Past Orders</h1>
         <span>
            <div class="col-md-2">
               <!-- Pass 1 as parameter to call add user form -->
               <!-- <a href="<?php echo base_url('admin/addEditUserView/1')?>" class="btn btn-primary">Add User</a> -->
            </div>
         </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Past Orders</div>
         </div>
      </div>
      <div class="section-body">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                  <div class="card-header user_tables">
                     <div class="row">
                        <div class="form-group col-md-3">
                           <label>From Date</label>
                           <input type="date" id="fromdate" name="fromdate" max="" class="form-control fromdate wv_filter_box_height" value="" />
                        </div>
                        <div class="form-group col-md-3">
                           <label>To Date</label>
                           <input type="date" id="todate" name="todate" max="" class="form-control todate wv_filter_box_height" value="" />
                        </div>
                        <div class="form-group col-md-3">
                           <label>Select Delivery Mode:</label>
                           <select class="custom-select userstatus wv_filter_box_height form-control" name="userstatus">
                              <option value="">Select Delivery Mode:</option>
                              <option value="1">Take Away</option>
                              <option value="2">Home Delivery</option>
                              <option value="2">Pick and Drop</option>
                           </select>
                        </div>
                        <div class="form-group col-md-2">
                           <form action="javascript:void(0);">
                              <label>Search</label>
                              <input type="search" name="search" class="form-control search_customer wv_filter_box_height" placeholder="Search" id="search_user_list_data" value="" />
                           </form>
                        </div>
                        <div class="form-group col-md-1 users_btns">
                           <form action="javascript:void(0);">
                              <button class="btn btn-primary search_user_list_data" type="button">Search</button>
                           </form>
                        </div>
                     </div>
                     <div class="mb-3 users_btns">
                        <!-- <button class="btn btn-primary search_user_list_data" type="button">Search</button> -->
                        <a href="#" class="btn btn-primary"> Clear</a>
                        <button class="btn btn-primary export_user_csv" type="button">Export Csv</button>
                     </div>
                  </div>
                  <div class="card-body table-flip-scroll">
                     <table class="table ">
                        <thead>
                           <tr>
                              <th>Order ID</th>
                              <th>Status</th>
                              <th>Customer</th>
                              <th>Delivery Mode</th>
                              <th>Order Time </th>
                              <th>Schedule Time</th>
                              <th>Payment Method</th>
                              <th>Address</th>
                              <th>Amount</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td><a href="<?php echo base_url(); ?>admin/order_single">3213265</a></td>
                              <td>Complete</td>
                              <td>John Doe</td>
                              <td>Home Delivery</td>
                              <td>01-02-2021 | 4:01 PM</td>
                              <td>01-02-2021 07:36 PM</td>
                              <td>Online</td>
                              <td>10A, Lorem ipsum </td>
                              <td>10A, Lorem ipsum </td>
                              <td>
                                 <div class="action_icons_dropdown">
                                    <i class="fa fa-ellipsis-h"></i>
                                    <div class="dropdown-content">
                                       <a href="" class="" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                       <a id="swal-6" class=""  id="" data-id="" data-toggle="tooltip" title="Make Inactive"> <i class="fa fa-unlock"></i> </a>
                                       <a class="" href="" data-toggle="tooltip" title="Show Details"> <i class="fa fa-eye"></i> </a>
                                       <a class="" id="" data-id="" data-toggle="tooltip" title="Delete"
                                          >
                                       <i class="fas fa-trash-alt" style="cursor: pointer;"></i>
                                       </a>
                                    </div>
                                 </div>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>