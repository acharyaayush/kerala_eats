<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Add Customer</h1>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Add Customer</div>
         </div>
      </div>
      <div class="admin_profile">
         <div class="container">
            <div class="card-body">
               <form method="POST">
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
                        <div class="col-md-7">
                           <input type="text" name="fname" value="" placeholder="Enter first name" required="" />
                        </div>
                     </div>
                  </div>
                  <div class="form-group admin-input-field">
                     <div class="row">
                        <div class="col-md-3">
                           <label>Last Name</label>
                        </div>
                        <div class="col-md-7">
                           <input type="text" name="lname" value="" placeholder="Enter last name" required="" />
                        </div>
                     </div>
                  </div>
                     <div class="form-group admin-input-field">
                     <div class="row">
                        <div class="col-md-3">
                           <label>Email</label>
                        </div>
                        <div class="col-md-7">
                           <input type="email" name="email" value="" placeholder="Enter email address" required="" />
                        </div>
                     </div>
                  </div>
                  <div class="form-group admin-buttons">
                     <button type="button" class="btn btn-primary change-password-btns mr-10">Save Changes</button>
                     <button type="button" class="btn btn-secondary change-password_cancel">Cancel</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </section>
</div>