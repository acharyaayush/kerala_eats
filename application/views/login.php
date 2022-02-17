<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title> <?php echo APP_NAME .' | '?> Login</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css">
        <link rel="icon" href="<?php echo base_url(); ?>/assets/img/favicon.png" type="image" sizes="16x16">

    </head>
    <body>
    <div class="auth">
            <div class="auth-container login_form">
                <div class="card">
                    <header class="auth-header">
                        <h1 class="auth-title">
                            <img src="<?php echo base_url(); ?>/assets/img/logo.png">
                           </h1>
                    </header>
                    <div class="auth-content">
                        <p class="text-xs-center">LOGIN TO CONTINUE</p>
                        <form  id="login-form" action="<?php echo base_url('admin/auth')?>" method="post">

                            <div class="form-group"> 
                                <label for="username">Email</label> 
                                <input type="email" class="form-control underlined" name="email" id="username" placeholder="Your email address" required value=" <?php if(isset($_COOKIE["loginId"])) { echo $_COOKIE["loginId"]; } ?>"> 
                            </div>
                            <div class="form-group"> 
                                <label for="password">Password</label> 
                                <input type="password" class="form-control underlined" name="password" id="password" placeholder="Your password" required value="<?php if(isset($_COOKIE["loginPass"])) { echo $_COOKIE["loginPass"]; } ?>" minlength="6" maxlength="20" > 
                            </div>
                            <div class="form-group remember_me_txt"> <label for="remember">
                               <input type="checkbox" name="remember" <?php if(isset($_COOKIE["loginId"])) { ?> checked="checked" <?php } ?>> <span>Remember me</span>
                                <!-- <input class="checkbox" id="remember" type="checkbox">  -->
                                
                                </label> <a href="<?php echo base_url(); ?>admin/forgot_password" class="forgot-btn pull-right">Forgot password?</a> 
                            </div>
                             <?php $this->load->view("validation");?>
                            <div class="form-group"> 
                                <button type="submit" class="btn btn-block btn-primary">Login</button> 
                            </div>
                        </form>
                    </div>
                </div>
                <!-- <div class="text-xs-center">
                    <a href="<?php echo base_url();?>" class="btn btn-secondary rounded btn-sm"> <i class="fa fa-arrow-left"></i> Back to Home </a>
                </div> -->
            </div>
        </div>
        <script src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
        <script src="<?php echo base_url();?>assets/js/custom.js"></script>
        
    </body>
</html>