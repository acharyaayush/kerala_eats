<!-- Main Content --> 
<div class="main-content customerTableListing">
   <section class="section">
      <div class="section-header">
         <h1>Customer</h1>
         <span>
            <div class="col-md-2">
               <!-- Pass 1 as parameter to call add user form -->
               <a href="<?php echo base_url(); ?>admin/add_customer" class="btn btn-primary">Add Customer</a>
            </div>
         </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Customer</div>
         </div>
      </div>
      <div class="section-body">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                  <div class="card-header user_tables">
                            <div class="promotionFileter">
                                <div class="promotionFilterFields">
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
                                            <label>Status</label>
                                            <select class="custom-select userstatus wv_filter_box_height form-control" name="userstatus">
                                                <option value="">Select Status</option>
                                                <option value="1">Active</option>
                                                <option value="2">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <form action="javascript:void(0);">
                                                <label>Search</label>
                                                <input type="search" name="search" class="form-control search_customer wv_filter_box_height" placeholder="Search" id="search_user_list_data" value="" />
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group users_btns">
                                    <form action="javascript:void(0);">
                                        <button class="btn btn-primary m-0 search_user_list_data" type="button">Search</button>
                                    </form>
                                </div>
                            </div>



                            <div class="mb-3 users_btns">
                                <!-- <button class="btn btn-primary search_user_list_data" type="button">Search</button> -->
                                <a href="#" class="btn btn-secondary mr-2">Clear</a>
                                <button class="btn btn-secondary  export_user_csv" type="button">Export Csv</button>
                            </div>
                        </div>
                  <div class="card-body table-flip-scroll customerTableListing">
                     <table class="table ">
                        <thead>
                           <tr>
                              <th>ID</th>
                              <th>Profile</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Email</th>
                              <th>Status</th>
                              <th>DOB</th>
                              <th>Created Date</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td>1</td>
                              <td class="profile-img"><img src="<?php echo base_url(); ?>assets/img/avatar/avatar-1.png" /></td>
                              <td>test</td>
                              <td>test</td>
                              <td>admin@gmail.com</td>
                              <td>Active</td>
                              <td>17/09/2020</td>
                              <td>16/02/2021</td>
                              <td>
                                 <div class="action_icons_dropdown">
                                    <i class="fa fa-ellipsis-h"></i>
                                    <div class="dropdown-content">
                                       <a href="" class="" data-target="#edit-customer_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
<!-- Edit Customer Modal -->
<div class="modal fade edit-customer" id="edit-customer_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog read_more_popup" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit Customer</h5>
            <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>

      <form method="POST">
         <div class="modal-body">
            <div class="admin_profile">
                        <div class="form-group">
                           <div class="row">
                              <div class="col-md-3">
                                 <label>Profile Image</label>
                              </div>
                              <div class="col-md-7 admin-profile-img">
                                 <img id="disp_img" src="<?php 
                                    if(empty($header['user_data']))
                                       {
                                          echo base_url('assets/img/avatar/avatar-1.png'); 
                                       }else
                                       {
                                          echo base_url($header['user_data']);
                                       }
                                     ?>"  alt="">
                                 <div class="img-add">                                       
                                    <input type="file" id="file" name="file" onchange="document.getElementById('disp_img').src = window.URL.createObjectURL(this.files[0])">
                                    <label for="file"><i class="fas fa-pencil-alt"></i></label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="form-group  admin-input-field">
                           <div class="row">
                              <div class="col-md-3">
                                 <label>First Name</label>
                              </div>
                              <div class="col-md-9">
                                 <input type="text" name="fname" value="" placeholder="Enter first name" required="" />
                              </div>
                           </div>
                        </div>
                        <div class="form-group admin-input-field">
                           <div class="row">
                              <div class="col-md-3">
                                 <label>Last Name</label>
                              </div>
                              <div class="col-md-9">
                                 <input type="text" name="lname" value="" placeholder="Enter last name" required="" />
                              </div>
                           </div>
                        </div>
                        <div class="form-group admin-input-field">
                           <div class="row">
                              <div class="col-md-3">
                                 <label>Email</label>
                              </div>
                              <div class="col-md-9">
                                 <input type="email" name="email" value="" placeholder="Enter email address"  required="" />
                              </div>
                           </div>
                        </div>
                        <div class="form-group admin-input-field">
                           <div class="row">
                              <div class="col-md-3">
                                 <label>Status</label>
                              </div>
                              <div class="col-md-9">
                                 <select>
                                    <option>Active</option>
                                    <option>Inactive</option>
                                 </select>
                              </div>
                           </div>
                        </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary modal_btns">Save</button>
         </div>
       </form>
      </div>
   </div>
</div>
<!-- End Edit Customer Modal