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
                        <p class="text-center"><?php if(isset($message)){ echo $message; } ?>.</p>
                    </div>
                </div>
                <div class="text-xs-center return-login">
                    <a href="<?php echo base_url(); ?>admin/login" class="btn btn-secondary rounded btn-sm"> Return to login </a>
                </div>
            </div>
        </div>
    </body>
</html>
