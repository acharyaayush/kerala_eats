<?php $this->load->view('email/includes/header'); ?>
<tr>
    <td>
        Dear <?php echo $restaurant_name;?> ,
    </td>
</tr>
<tr>
    <td>Greetings from <?php echo APP_NAME;?>.You're receiving this mail because you signed up for an account on <?php echo APP_NAME;?> as a Merchant.</td>
</tr>
<tr>
    <td><?php echo APP_NAME;?> welcome and thank you for regiserting with us.</td>
</tr>
<tr>
    <td>Your Account Details Are:</td>
</tr>
<tr>
    <td>Restaurant name : <?php echo $restaurant_name;?></td>
</tr>
<tr>
    <td>Email : <?php echo $email;?></td>
</tr>
<tr>
    <td>Kind Regards,</td>
</tr>
<tr>
    <td>
        Team <?php echo APP_NAME;?>
    </td>
</tr>
<tr>
    <td>
        <a href="<?php echo $website_url;?>"><?php echo $website_url;?></a>
    </td>
</tr>

<?php
//footer view load
$this->load->view('email/includes/footer');
?>