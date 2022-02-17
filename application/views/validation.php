<?php

 
    if($this->session->flashdata('success')!='')
    { ?>
        <div class="alert success">
            <span class="closebtn">&times;</span>  
            <strong>Success !</strong> <?php echo $this->session->flashdata('success');?>
        </div>               
    <?php
    }elseif($this->session->flashdata('error')!='')
    { ?>
        <div class="alert">
            <span class="closebtn">&times;</span>  
            <strong>Error !</strong> <?php echo $this->session->flashdata('error');?>
        </div> 
    <?php
    }
?>