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
                        <p class="text-xs-center">PASSWORD RECOVER</p>
                        <p class="text-center">Enter your email address to recover your password.</p>
                        <form id="login-form" action="<?php echo base_url('admin/forgot_password_controller')?>" method="post" novalidate="">
                            <div class="form-group">
                                <label for="username">Email</label>
                                <input type="email" class="form-control underlined" name="email" id="username" placeholder="Your email address" required value="" />
                            </div>
                             <?php $this->load->view("validation");?>
                            <div class="form-group">
                                <button type="submit" class="btn btn-block btn-primary">Reset</button>
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
