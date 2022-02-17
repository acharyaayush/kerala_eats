<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php echo APP_NAME .' | '?><?php if(isset($title)){echo $title;}?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
    </head>

    <body>
        <div class="auth">
            <div class="auth-container login_form forgot_password_form">
                <div class="card">
                    <header class="auth-header">
                        <h1 class="auth-title">
                            <img src="<?php echo base_url(); ?>/assets/img/logo.png" />
                        </h1>
                    </header>
                    <div class="auth-content">
                        <p class="text-xs-center">Set New Password</p>
                        
                        <form id="reset_password_form" action="<?php echo base_url('admin/resetPasswordUpdate'); ?>" method="post" onsubmit="return validatechngpwdform();" method="POST">
                            
                            <div class="form-group"> 
                                <label for="password">New Password</label> 
                                <input type="password" class="form-control underlined np_password" name="password" id="password" placeholder="Your new password" required> 
                            </div>

                            <div class="form-group"> 
                                <label for="password">Confirm New Password</label> 
                                <input type="password" class="form-control underlined cnp_password" name="confirm_password" id="confirm_password" placeholder="Your confirm new password" required> 
                            </div>
                            <div class="col-sm-10"> 
                                <div class="alert d-none">
                                     <span id="pwd_error"></span><br>
                                </div>
                               
                            </div>
                            <input type="hidden" name="email" value="<?php echo $user_data['email'];?>">
                            <input type="hidden" name="token" value="<?php echo $user_data['token'];?>">
                            <input type="hidden" name="role" value="<?php echo $user_data['role'];?>">  
                            <div class="form-group">
                                <button type="submit" name="reset_password_update" value="submit"  class="btn btn-block btn-primary" id="final_submit">Update</button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="text-xs-center return-login">
                    <a href="<?php echo base_url(); ?>admin/login" class="btn btn-secondary rounded btn-sm"> Return to login </a>
                </div>
            </div>
        </div>
         <script src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
         <script src="<?php echo base_url();?>assets/js/custom.js"></script>
        
    </body>
</html>
