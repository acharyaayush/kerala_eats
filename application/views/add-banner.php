<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Ad Banner</h1>
         <span>
            <div class="col-md-2">
               <!-- Pass 1 as parameter to call add user form -->
               <!-- <a href="#" class="btn btn-primary">Create Order</a> -->
            </div>
         </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Ad Banner</div>
         </div>
      </div>
      <div class="add-banner-section">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                  <div class="card-header user_tables">
                     <div class="row">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-3">
                           <div class="mb-3 users_btns" style="float: right;">
                              <label class="switch promocode-status">
                              <input type="checkbox">
                              <span class="slider round"></span>
                              </label>
                              <a href="#" class="btn btn-primary" data-target="#ad_banner_popup" data-toggle="modal"> Add Banner</a>
                           </div>
                        </div>
                     </div>
                     <!-- <div class="mb-3 users_btns">
                        <button class="btn btn-primary search_user_list_data" type="button">Search</button>
                        <a href="#" class="btn btn-primary"> Add Banner</a>
                        <button class="btn btn-primary export_user_csv" type="button">Import/Export</button>
                        </div> -->
                  </div>
                  <div class="card-body table-flip-scroll orders-tables tb-scroll">
                     <table class="table">
                        <thead>
                           <tr>
                              <th>Image</th>
                              <th>Mobile Image Web</th>
                              <th>Mobile Image</th>
                              <th>Name</th>
                              <th>Text</th>
                              <th>Restaurants ID</th>
                              <th>Restaurants Name</th>
                              <th>External Link</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td><img src="<?php echo base_url(); ?>assets/img/banner.jpg"></td>
                              <td><img src="<?php echo base_url(); ?>assets/img/banner.jpg"></td>
                              <td><img src="<?php echo base_url(); ?>assets/img/banner.jpg"></td>
                              <td>test</td>
                              <td>test</td>
                              <td>3213265</td>
                              <td>demo</td>
                              <td>-</td>
                              <td>
                                 <div class="action_icons_dropdown">
                                    <i class="fa fa-ellipsis-v"></i>
                                    <div class="dropdown-content">
                                       <a href="" class="" data-target="#edit_banner_popup" data-toggle="modal"><i class="fas fa-pencil-alt"></i></a>
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

      <!-- Modal for Add Banner -->
   <div class="modal fade" id="ad_banner_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog read_more_popup" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Add Banner</h5>
               <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form>
               <div class="modal-body add_category">
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Name</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" name="name" placeholder="Enter Name" value="" required="">
                        </div>
                     </div>
                  </div>
                  <div class="form-group restaurants_id">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Restaurants ID </label> <label class="switch promocode-status">
                              <input type="checkbox">
                              <span class="slider round"></span>
                              </label>
                              <p class="banner_text">Link the banner to a Restaurants. When the Customer will click on this banner he/she will be redirected to this Restaurants</p>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group has-search">
                            <span class="fa fa-search form-control-feedback"></span>
                            <input  type="text" name="restaurants_id" placeholder="Type to search" value="" required="" disabled="" style="cursor: not-allowed;" class="form-control">
                          </div>
                        </div>
                     </div>
                  </div>
                   <div class="form-group external-links">
                     <div class="row">
                        <div class="col-md-6">
                           <label>External Link </label> <label class="switch promocode-status">
                              <input type="checkbox">
                              <span class="slider round"></span>
                              </label>
                              <p class="banner_text">Link the banner to an external link. When the Customer will click on this banner, he/she will be redirected to this link.</p>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group has-search">
                           <span class="form-control-feedback"> https://</span>
                            <input  type="text" name="external-link" placeholder="www.example.com" value="" required="" disabled="" style="cursor: not-allowed;" class="form-control">
                          </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Text </label>
                              <p class="banner_text">You can enter here your own description/note about the banner for your use.</p>
                        </div>
                        <div class="col-md-6">
                            <input  type="text" name="text" placeholder="Enter Text" value="" required="">
                        </div>
                     </div>
                  </div>
                   <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Banner Image For Web (1920x360 px) </label>
                        </div>
                        <div class="col-md-6 ad_banner_image">
                             <img id="disp_img" src="<?php 
                              if(empty($header['user_data']))
                                 {
                                    echo base_url('assets/img/avatar/avatar-1.png'); 
                                 }else
                                 {
                                    echo base_url($header['user_data']);
                                 }
                               ?>"  alt="">
                           <div class="">                                       
                              <input type="file" id="file" name="file" onchange="document.getElementById('disp_img').src = window.URL.createObjectURL(this.files[0])">
                              <label for="file"><span>Drag & Drop images, or </span><br><span style="color: #f35d18; cursor: pointer;">BROWSE </span><br>
                              <span>from computer</span></label>
                           </div>
                        </div>
                     </div>
                  </div>

                   <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Banner Image For Mobile Web (768x384 px)</label>
                        </div>
                        <div class="col-md-6 ad_banner_image">
                             <img id="disp_img" src="<?php 
                              if(empty($header['user_data']))
                                 {
                                    echo base_url('assets/img/avatar/avatar-1.png'); 
                                 }else
                                 {
                                    echo base_url($header['user_data']);
                                 }
                               ?>"  alt="">
                           <div class="">                                       
                              <input type="file" id="file" name="file" onchange="document.getElementById('disp_img').src = window.URL.createObjectURL(this.files[0])">
                              <label for="file"><span>Drag & Drop images, or </span><br><span style="color: #f35d18; cursor: pointer;">BROWSE </span><br>
                              <span>from computer</span></label>
                           </div>
                        </div>
                     </div>
                  </div>
                   <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Banner Image For Native Mobile Apps <br>(1920x480 px)* / (768x384 px)**</label>
                        </div>
                        <div class="col-md-6 ad_banner_image">
                             <img id="disp_img" src="<?php 
                              if(empty($header['user_data']))
                                 {
                                    echo base_url('assets/img/avatar/avatar-1.png'); 
                                 }else
                                 {
                                    echo base_url($header['user_data']);
                                 }
                               ?>"  alt="">
                           <div class="">                                       
                              <input type="file" id="file" name="file" onchange="document.getElementById('disp_img').src = window.URL.createObjectURL(this.files[0])">
                              <label for="file"><span>Drag & Drop images, or </span><br><span style="color: #f35d18; cursor: pointer;">BROWSE </span><br>
                              <span>from computer</span></label>
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

         <!-- Modal for Edit Banner -->
   <div class="modal fade" id="edit_banner_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog read_more_popup" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Edit Banner</h5>
               <button type="button" class="close close_btnn" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form>
               <div class="modal-body add_category">
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Name</label>
                        </div>
                        <div class="col-md-6">
                           <input type="text" name="name" placeholder="Enter Name" value="" required="">
                        </div>
                     </div>
                  </div>
                  <div class="form-group restaurants_id">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Restaurants ID </label> <label class="switch promocode-status">
                              <input type="checkbox">
                              <span class="slider round"></span>
                              </label>
                              <p class="banner_text">Link the banner to a Restaurants. When the Customer will click on this banner he/she will be redirected to this Restaurants</p>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group has-search">
                            <span class="fa fa-search form-control-feedback"></span>
                            <input  type="text" name="restaurants_id" placeholder="Type to search" value="" required="" disabled="" style="cursor: not-allowed;" class="form-control">
                          </div>
                        </div>
                     </div>
                  </div>
                   <div class="form-group external-links">
                     <div class="row">
                        <div class="col-md-6">
                           <label>External Link </label> <label class="switch promocode-status">
                              <input type="checkbox">
                              <span class="slider round"></span>
                              </label>
                              <p class="banner_text">Link the banner to an external link. When the Customer will click on this banner, he/she will be redirected to this link.</p>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group has-search">
                           <span class="form-control-feedback"> https://</span>
                            <input  type="text" name="external-link" placeholder="www.example.com" value="" required="" disabled="" style="cursor: not-allowed;" class="form-control">
                          </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Text </label>
                              <p class="banner_text">You can enter here your own description/note about the banner for your use.</p>
                        </div>
                        <div class="col-md-6">
                            <input  type="text" name="text" placeholder="Enter Text" value="" required="">
                        </div>
                     </div>
                  </div>
                   <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Banner Image For Web (1920x360 px) </label>
                        </div>
                        <div class="col-md-6 ad_banner_image">
                             <img id="disp_img" src="<?php 
                              if(empty($header['user_data']))
                                 {
                                    echo base_url('assets/img/avatar/avatar-1.png'); 
                                 }else
                                 {
                                    echo base_url($header['user_data']);
                                 }
                               ?>"  alt="">
                           <div class="">                                       
                              <input type="file" id="file" name="file" onchange="document.getElementById('disp_img').src = window.URL.createObjectURL(this.files[0])">
                              <label for="file"><span>Drag & Drop images, or </span><br><span style="color: #f35d18; cursor: pointer;">BROWSE </span><br>
                              <span>from computer</span></label>
                           </div>
                        </div>
                     </div>
                  </div>

                   <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Banner Image For Mobile Web (768x384 px)</label>
                        </div>
                        <div class="col-md-6 ad_banner_image">
                             <img id="disp_img" src="<?php 
                              if(empty($header['user_data']))
                                 {
                                    echo base_url('assets/img/avatar/avatar-1.png'); 
                                 }else
                                 {
                                    echo base_url($header['user_data']);
                                 }
                               ?>"  alt="">
                           <div class="">                                       
                              <input type="file" id="file" name="file" onchange="document.getElementById('disp_img').src = window.URL.createObjectURL(this.files[0])">
                              <label for="file"><span>Drag & Drop images, or </span><br><span style="color: #f35d18; cursor: pointer;">BROWSE </span><br>
                              <span>from computer</span></label>
                           </div>
                        </div>
                     </div>
                  </div>
                   <div class="form-group">
                     <div class="row">
                        <div class="col-md-6">
                           <label>Banner Image For Native Mobile Apps <br>(1920x480 px)* / (768x384 px)**</label>
                        </div>
                        <div class="col-md-6 ad_banner_image">
                             <img id="disp_img" src="<?php 
                              if(empty($header['user_data']))
                                 {
                                    echo base_url('assets/img/avatar/avatar-1.png'); 
                                 }else
                                 {
                                    echo base_url($header['user_data']);
                                 }
                               ?>"  alt="">
                           <div class="">                                       
                              <input type="file" id="file" name="file" onchange="document.getElementById('disp_img').src = window.URL.createObjectURL(this.files[0])">
                              <label for="file"><span>Drag & Drop images, or </span><br><span style="color: #f35d18; cursor: pointer;">BROWSE </span><br>
                              <span>from computer</span></label>
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