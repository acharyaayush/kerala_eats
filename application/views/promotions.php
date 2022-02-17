<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Promotions</h1>
            <span>
                <div class="col-md-2">
                    <!-- Pass 1 as parameter to call add user form -->
                    <button class="btn btn-primary" data-target="#add_promotion_popup" data-toggle="modal">Add Promotion</button>
                </div>
            </span>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
                <div class="breadcrumb-item">Promotions</div>
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
                        <div class="card-body table-flip-scroll">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Min. Order</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Fab20</td>
                                        <td>Flat</td>
                                        <td>20.00</td>
                                        <td>200.00</td>
                                        <td>01-02-2021</td>
                                        <td>30-02-2021</td>
                                        <td>Lorem ipsum dolor sit amet</td>
                                        <td>
                                            <a href="" class="btn btn-primary btn-action mr-1" data-target="#edit_promotion_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
                                            <a class="btn btn-danger btn-action" id="" data-id="" data-toggle="tooltip" title="Delete"> <i class="fas fa-trash"></i> </a>
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
    <!-- Modal for Add Promotion -->
    <div class="modal fade add-edit-promotion" id="add_promotion_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog read_more_popup" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Promotion</h5>
                    <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body admin_add_promotion">
                        <div class="form-group admin-input-field">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Promotional Category</label>
                                    <select>
                                        <option value="">Promotional Category</option>
                                        <option value=""></option>
                                        <option value=""></option>
                                    </select>
                                    <label>Promotion Type </label>
                                    <select>
                                        <option value="">Select Type</option>
                                        <option value="">Discount</option>
                                        <option value="">Flat</option>
                                    </select>
                                    <label>Select Product</label>
                                    <select>
                                        <option value="">Select Product</option>
                                        <option value="">Discount</option>
                                        <option value="">Flat</option>
                                    </select>
                                    <label>Value</label>
                                    <input type="text" name="value" placeholder="Enter Value" required="">
                                    <label>Min. Order</label>
                                    <input type="text" name="min-order" placeholder="Enter Min Order" required="">
                                </div>
                                <div class="col-md-6">
                                    <label>From </label>
                                    <input type="datetime-local" name="fdate" placeholder="" value="" required="">
                                    <label>Till </label>
                                    <input type="datetime-local" name="till" placeholder="" value="" required="">
                                    <label>Description (Max 150 Characters) * </label>
                                    <textarea type="text" name="description" placeholder="Description" value="" required=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary modal_btns" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary modal_btns">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end modal-->
    <!-- Modal for Edit Promotion -->
    <div class="modal fade add-edit-promotion" id="edit_promotion_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog read_more_popup" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Promotion</h5>
                    <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body admin_add_promotion">
                        <div class="form-group admin-input-field">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Promotional Category</label>
                                    <select>
                                        <option value="">Promotional Category</option>
                                        <option value=""></option>
                                        <option value=""></option>
                                    </select>
                                    <label>Promotion Type </label>
                                    <select>
                                        <option value="">Select Type</option>
                                        <option value="">Discount</option>
                                        <option value="">Flat</option>
                                    </select>
                                    <label>Select Product</label>
                                    <select>
                                        <option value="">Select Product</option>
                                        <option value="">Discount</option>
                                        <option value="">Flat</option>
                                    </select>
                                    <label>Value</label>
                                    <input type="text" name="value" placeholder="Enter Value" required="">
                                    <label>Min. Order</label>
                                    <input type="text" name="min-order" placeholder="Enter Min Order" required="">
                                </div>
                                <div class="col-md-6">
                                    <label>From </label>
                                    <input type="datetime-local" name="fdate" placeholder="" value="" required="">
                                    <label>Till </label>
                                    <input type="datetime-local" name="till" placeholder="" value="" required="">
                                    <label>Description (Max 150 Characters) * </label>
                                    <textarea type="text" name="description" placeholder="Description" value="" required=""></textarea>
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
    <!--end modal-->
</div>
