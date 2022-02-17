             <!-- Main Content -->
<div class="main-content">
<section class="section">
   <div class="section-header">
      <h1>Change Password</h1>
      <div class="section-header-breadcrumb">
         <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
         <div class="breadcrumb-item">Change Password</div>
      </div>
   </div>
   <div class="change-password-body">
    <div class="container">
     <div class="card-body">
                <form method="POST">
                  <div class="form-group">
                    <label for="password">Old Password</label>
                    <input id="old-password" type="password" class="form-control pwstrength"  name="password"  required placeholder="Enter old password">
                  </div>

                  <div class="form-group">
                    <label for="password">New Password</label>
                    <input id="password" type="password" class="form-control pwstrength"  name="password"  required placeholder="Enter new password">
                    <div id="pwindicator" class="pwindicator">
                      <div class="bar"></div>
                      <div class="label"></div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="password-confirm">Confirm Password</label>
                    <input id="password-confirm" type="password" class="form-control" name="confirm-password"  required placeholder="Enter confirm password">
                  </div>

                  <div class="form-group">
                        <button type="button" class="btn btn-primary change-password-btns mr-10">Save Changes</button>
                      <button type="button" class="btn btn-secondary change-password_cancel">Cancel</button>
                  </div>
                </form>
              </div>
      </div>
    </div>
</section>
</div>