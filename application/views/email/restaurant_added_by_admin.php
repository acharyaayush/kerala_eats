<?php $this->load->view('email/includes/header'); ?>
<tr>
    <td>
        Dear <?php echo $name;?> ,
    </td>
</tr>
<tr>
    <td>Greetings from <?php echo APP_NAME;?>. You're receiving this mail because you are registered by <?php echo APP_NAME;?>  administrator as a Merchant for your restaurant <b>"<?php echo $restaurant_name;?>"</b>.</td>
</tr>
<tr>
    <td><?php echo APP_NAME;?> welcome and thank you for your interest with us.</td>
</tr>
<tr>
    <td>Your Account Details Are:</td>
</tr>
<tr>
    <td>Restaurant name : <?php echo $restaurant_name;?><br>Email : <?php echo $email;?><br>Password : <?php echo $password;?><br>Login Url : <?php echo base_url('admin/login/')?></td>
</tr>
 
<tr>
    <td>Kind Regards,<br>Team <?php echo APP_NAME;?><br><a href="<?php echo $website_url;?>"><?php echo $website_url;?></a></td>
</tr>

<?php
//footer view load
$this->load->view('email/includes/footer');
?>