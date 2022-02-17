<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Products</h1>
         <span>
            <div class="col-md-2">
               <!-- Pass 1 as parameter to call add user form -->
               <!-- <a href="<?php echo base_url('admin/addEditUserView/1')?>" class="btn btn-primary">Add User</a> -->
            </div>
         </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Products</div>
         </div>
      </div>
      <div class="section-body">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                  <div class="card-header user_tables">
                            <div class="d-flex">
                                <div class="product-filterForm">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>From Date</label>
                                            <input type="date" id="fromdate" name="fromdate" max="" class="form-control fromdate wv_filter_box_height" value="" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>To Date</label>
                                            <input type="date" id="todate" name="todate" max="" class="form-control todate wv_filter_box_height" value="" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <form action="javascript:void(0);">
                                                <label>Search</label>
                                                <input type="search" name="search" class="form-control search_customer wv_filter_box_height" placeholder="Search" id="search_user_list_data" value="" />
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group product-filter">
                                    <div class="users_btns d-flex">
                                        <button class="btn btn-primary search_user_list_data" type="button">Search</button>
                                        <a href="#" class="btn btn-secondary m-0"> Clear</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="col-md-4">
                           
                           <!-- Category Listing -->

                           <table class="table category_tables">
                              <thead>
                                 <tr>
                                    <th>Category (10)</th>
                                    <th><label class="switch_on_off">
                                       <input type="checkbox" checked="checkbox">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </th>
                                    <th class="text-center"><i class="fas fa-plus-circle" data-target="#add_category_popup" data-toggle='modal' style="cursor: pointer;"></i></th>
                                 </tr>
                              </thead>
                              <tbody class="category-scroll">
                                 <tr>
                                    <td>Swaadhisht Special</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox" checked="checked">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td class="text-center">
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit-category_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr>
                                    <td>Malabar Briyani</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox" checked="checked">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td class="text-center">
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit-category_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr>
                                    <td>Thalaseri Briyani</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td class="text-center">
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit-category_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr>
                                    <td>Malabar Briyani</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox" >
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td class="text-center">
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit-category_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr>
                                    <td>Thalaseri Briyani</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td class="text-center">
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit-category_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr>
                                    <td>Malabar Briyani</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox"  checked="checked">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td class="text-center">
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit-category_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr>
                                    <td>Thalaseri Briyani</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox" checked="checked">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td class="text-center">
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit-category_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr>
                                    <td>Malabar Briyani</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox"  checked="checked">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td class="text-center">
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit-category_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr>
                                    <td>Thalaseri Briyani</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox" checked="checked">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td class="text-center">
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit-category_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr>
                                    <td>Thalaseri Briyani</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox" checked="checked">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td class="text-center">
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit-category_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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

                        <!-- Product Listing -->

                        <div class="col-md-8 table-flip-scroll">
                           <table class="table category_tables ">
                              <thead>
                                 <tr>
                                    <th colspan="3">Product (5)</th>
                                    <th colspan="2" class="text-right add_product">
                                       <!-- <i class="fas fa-search"></i> -->
                                        <div class="d-flex">
                                                        <button class="btn btn-primary mr-2" data-target="#add_product_popup" data-toggle="modal">Add Product</button>
                                                        <button class="btn btn-secondary export_user_csv" type="button">Import/Export</button>
                                                    </div>
                                    </th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr class="product_section">
                                    <td class="profile-img"><img src="<?php echo base_url(); ?>assets/img/avatar/avatar-1.png"></td>
                                    <td>
                                       <h3>Bamboo Chicken Briyani</h3>
                                       <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem.</p>
                                    </td>
                                    <td>$15.00</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox" checked="checkbox">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td>
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit_product_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr class="product_section">
                                    <td class="profile-img"><img src="<?php echo base_url(); ?>assets/img/avatar/avatar-1.png"></td>
                                    <td>
                                       <h3>Bamboo Chicken Briyani</h3>
                                       <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem.</p>
                                    </td>
                                    <td>$15.00</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox" checked="checkbox">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td>
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit_product_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr class="product_section">
                                    <td class="profile-img"><img src="<?php echo base_url(); ?>assets/img/avatar/avatar-1.png"></td>
                                    <td>
                                       <h3>Bamboo Chicken Briyani</h3>
                                       <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem.</p>
                                    </td>
                                    <td>$15.00</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td>
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit_product_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr class="product_section">
                                    <td class="profile-img"><img src="<?php echo base_url(); ?>assets/img/avatar/avatar-1.png"></td>
                                    <td>
                                       <h3>Bamboo Chicken Briyani</h3>
                                       <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem.</p>
                                    </td>
                                    <td>$15.00</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox" checked="checkbox">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td>
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit_product_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
                                 <tr class="product_section">
                                    <td class="profile-img"><img src="<?php echo base_url(); ?>assets/img/avatar/avatar-1.png"></td>
                                    <td>
                                       <h3>Bamboo Chicken Briyani</h3>
                                       <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem.</p>
                                    </td>
                                    <td>$15.00</td>
                                    <td><label class="switch_on_off">
                                       <input type="checkbox" checked="checkbox">
                                       <span class="slider_toggle round"></span>
                                       </label>
                                    </td>
                                    <td>
                                       <div class="action_icons_dropdown">
                                          <i class="fa fa-ellipsis-v"></i>
                                          <div class="dropdown-content">
                                             <a href="" class="" data-target="#edit_product_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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
         </div>
      </div>
   </section>

   <!-- Modal for Add category -->
   <div class="modal fade" id="add_category_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog read_more_popup" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Add Category</h5>
               <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form>
               <div class="modal-body add_category">
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4">
                           <label>Category Name</label>
                        </div>
                        <div class="col-md-8">
                           <input type="text" name="category-name" placeholder="Enter category name" value="" required="">
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4"> 
                           <label>Description</label>
                        </div>
                        <div class="col-md-8">
                           <textarea name="description" placeholder="Description" value="" required=""></textarea> 
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary modal_btns">Save</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!--end modal-->

      <!-- Modal for Edit category -->
   <div class="modal fade" id="edit-category_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog read_more_popup" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Edit Category</h5>
               <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form>
               <div class="modal-body add_category">
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4">
                           <label>Category Name</label>
                        </div>
                        <div class="col-md-8">
                           <input type="text" name="category-name" placeholder="Enter category name" value="" required="">
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4"> 
                           <label>Description</label>
                        </div>
                        <div class="col-md-8">
                           <textarea name="description" placeholder="Description" value="" required=""></textarea> 
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary modal_btns">Save</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!--end modal-->

   <!-- Modal for Add Product -->
   <div class="modal fade" id="add_product_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog read_more_popup" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Add Product</h5>
               <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form>
               <div class="modal-body add_category">
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4">
                           <label>Product Name</label>
                        </div>
                        <div class="col-md-8">
                           <input type="text" name="product-name" placeholder="Enter Product Name" value="" required="">
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4">
                           <label>Product Price</label>
                        </div>
                        <div class="col-md-8">
                           <input type="text" name="product-price" placeholder="Enter Product Price" value="" required="">
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4">
                           <label>Offer Price</label>
                        </div>
                        <div class="col-md-8">
                           <input type="text" name="offer-price" placeholder="Enter Offer Price" value="" required="">
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4"> 
                           <label>Description</label>
                        </div>
                        <div class="col-md-8">
                           <textarea name="description" placeholder="Description" value="" required=""></textarea> 
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4"> 
                           <label>Restaurant Image</label>
                        </div>
                        <div class="col-md-8 user-img">
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
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary modal_btns">Save</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!--end modal-->

      <!-- Modal for Edit Product -->
   <div class="modal fade" id="edit_product_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog read_more_popup" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Edit Product</h5>
               <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form>
               <div class="modal-body add_category">
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4">
                           <label>Product Name</label>
                        </div>
                        <div class="col-md-8">
                           <input type="text" name="product-name" placeholder="Enter Product Name" value="" required="">
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4">
                           <label>Product Price</label>
                        </div>
                        <div class="col-md-8">
                           <input type="text" name="product-price" placeholder="Enter Product Price" value="" required="">
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4">
                           <label>Offer Price</label>
                        </div>
                        <div class="col-md-8">
                           <input type="text" name="offer-price" placeholder="Enter Offer Price" value="" required="">
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4"> 
                           <label>Description</label>
                        </div>
                        <div class="col-md-8">
                           <textarea name="description" placeholder="Description" value="" required=""></textarea> 
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4"> 
                           <label>Restaurant Image</label>
                        </div>
                        <div class="col-md-8 user-img">
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
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary modal_btns">Save</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!--end modal-->

</div>

<script type="text/javascript">
   function create_custom_dropdowns() {
    $('select').each(function (i, select) {
        if (!$(this).next().hasClass('dropdown-select')) {
            $(this).after('<div class="dropdown-select wide ' + ($(this).attr('class') || '') + '" tabindex="0"><span class="current"></span><div class="list"><ul></ul></div></div>');
            var dropdown = $(this).next();
            var options = $(select).find('option');
            var selected = $(this).find('option:selected');
            dropdown.find('.current').html(selected.data('display-text') || selected.text());
            options.each(function (j, o) {
                var display = $(o).data('display-text') || '';
                dropdown.find('ul').append('<li class="option ' + ($(o).is(':selected') ? 'selected' : '') + '" data-value="' + $(o).val() + '" data-display-text="' + display + '">' + $(o).text() + '</li>');
            });
        }
    });

    $('.dropdown-select ul').before('<div class="dd-search"><input id="txtSearchValue" autocomplete="off" onkeyup="filter()" class="dd-searchbox" type="text"></div>');
}

// Event listeners

// Open/close
$(document).on('click', '.dropdown-select', function (event) {
    if($(event.target).hasClass('dd-searchbox')){
        return;
    }
    $('.dropdown-select').not($(this)).removeClass('open');
    $(this).toggleClass('open');
    if ($(this).hasClass('open')) {
        $(this).find('.option').attr('tabindex', 0);
        $(this).find('.selected').focus();
    } else {
        $(this).find('.option').removeAttr('tabindex');
        $(this).focus();
    }
});

// Close when clicking outside
$(document).on('click', function (event) {
    if ($(event.target).closest('.dropdown-select').length === 0) {
        $('.dropdown-select').removeClass('open');
        $('.dropdown-select .option').removeAttr('tabindex');
    }
    event.stopPropagation();
});

function filter(){
    var valThis = $('#txtSearchValue').val();
    $('.dropdown-select ul > li').each(function(){
     var text = $(this).text();
        (text.toLowerCase().indexOf(valThis.toLowerCase()) > -1) ? $(this).show() : $(this).hide();         
   });
};
// Search

// Option click
$(document).on('click', '.dropdown-select .option', function (event) {
    $(this).closest('.list').find('.selected').removeClass('selected');
    $(this).addClass('selected');
    var text = $(this).data('display-text') || $(this).text();
    $(this).closest('.dropdown-select').find('.current').text(text);
    $(this).closest('.dropdown-select').prev('select').val($(this).data('value')).trigger('change');
});

// Keyboard events
$(document).on('keydown', '.dropdown-select', function (event) {
    var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
    // Space or Enter
    //if (event.keyCode == 32 || event.keyCode == 13) {
    if (event.keyCode == 13) {
        if ($(this).hasClass('open')) {
            focused_option.trigger('click');
        } else {
            $(this).trigger('click');
        }
        return false;
        // Down
    } else if (event.keyCode == 40) {
        if (!$(this).hasClass('open')) {
            $(this).trigger('click');
        } else {
            focused_option.next().focus();
        }
        return false;
        // Up
    } else if (event.keyCode == 38) {
        if (!$(this).hasClass('open')) {
            $(this).trigger('click');
        } else {
            var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
            focused_option.prev().focus();
        }
        return false;
        // Esc
    } else if (event.keyCode == 27) {
        if ($(this).hasClass('open')) {
            $(this).trigger('click');
        }
        return false;
    }
});

$(document).ready(function () {
    create_custom_dropdowns();
});
</script>