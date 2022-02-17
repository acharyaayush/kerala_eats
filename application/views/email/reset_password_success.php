<?php
//header view load
$this->load->view('email/includes/header');
?>
<tr>
    <td>Hi <?php echo $first_name; ?></td>
</tr>
<tr>
    <td>Your account password has been reset.</td>
</tr>
<tr>
    <td>If you didn't reset the password yourself, contact to administrator.</td>
</tr>
<tr>
    <td>
        <h4 style="margin:15px 0 0;"><?php echo APP_NAME;?></h4>
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