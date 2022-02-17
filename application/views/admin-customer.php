<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Customer</h1>
         <span>
            <div class="col-md-2">
               <!-- Pass 1 as parameter to call add user form -->
               <!--  <a href="#" class="btn btn-primary">Create Order</a> -->
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
                                <button class="btn btn-secondary  export_user_csv" type="button">Export CSV</button>
                            </div>
                        </div>
                  <div class="card-body table-flip-scroll tb-scroll customerTableListing">
                     <table class="table ">
                        <thead>
                           <tr>
                              <th>ID</th>
                              <th>Profile</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Phone</th>
                              <th>Last Used Platform</th>
                              <th>Registration Date</th>
                              <th>Wallet Balance</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td><a href="<?php echo base_url(); ?>admin/customer_details">3116654</a></td>
                              <td class="profile-img"><img src="<?php echo base_url(); ?>assets/img/avatar/avatar-1.png" /></td>
                              <td>test</td>
                              <td>admin@gmail.com</td>
                              <td>+6523456789</td>
                              <td>Android</td>
                              <td>16/02/2021</td>
                              <td>S$0.00</td>
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
                           <div class="form-group  admin-input-field">
                              <div class="row">
                                 <div class="col-md-12">
                                    <label>First Name</label>
                                 </div>
                                 <div class="col-md-12">
                                    <input type="text" name="fname" value="" placeholder="Enter first name" required="" />
                                 </div>
                              </div>
                           </div>
                           <div class="form-group admin-input-field">
                              <div class="row">
                                 <div class="col-md-12">
                                    <label>Last Name</label>
                                 </div>
                                 <div class="col-md-12">
                                    <input type="text" name="lname" value="" placeholder="Enter last name" required="" />
                                 </div>
                              </div>
                           </div>
                           <div class="form-group admin-input-field">
                              <div class="row">
                                 <div class="col-md-12">
                                    <label>Email</label>
                                 </div>
                                 <div class="col-md-12">
                                    <input type="email" name="email" value="admin@gmail.com" placeholder="Enter email address"  required="" disabled="" style="cursor: not-allowed;" />
                                 </div>
                              </div>
                           </div> 
                           <div class="form-group admin-input-field phone-number-field">
                              <div class="row">
                                 <div class="col-md-12">
                                    <label>Phone</label>
                                 </div>
                                 <div class="col-md-12">
                                    <input id="phone" type="tel" name="phone" placeholder="Enter Phone Number" required="" class="form-control form-control-sm rounded-0 contact_number check_space">
                                      <div class="input-group-append">
                                    </div>
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
   <!-- End Edit Customer Modal -->
   <!-- Country wise phone number field -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.6/js/intlTelInput.min.js"></script>
<script type="text/javascript">
   let telInput = $("#phone")

// initialize
telInput.intlTelInput({
    initialCountry: 'auto',
    preferredCountries: ['us','gb','br','ru','cn','es','it'],
    autoPlaceholder: 'aggressive',
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.6/js/utils.js",
    geoIpLookup: function(callback) {
        fetch('https://ipinfo.io/json', {
            cache: 'reload'
        }).then(response => {
            if ( response.ok ) {
                 return response.json()
            }
            throw new Error('Failed: ' + response.status)
        }).then(ipjson => {
            callback(ipjson.country)
        }).catch(e => {
            callback('sg')
        })
    }
})

let telInput2 = $("#phone2")

// initialize
telInput2.intlTelInput({
    initialCountry: 'sg',
    preferredCountries: ['us','gb','br','ru','cn','es','it'],
    autoPlaceholder: 'aggressive',
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.6/js/utils.js"
})
</script>
 