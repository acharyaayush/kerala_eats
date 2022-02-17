<?php

if (empty($cms_data[0]['page_image'])) 
{
    $popup_image = base_url('assets/images/default_ad_banner.jpg');
    $exist_popup_image = "";
    } else {
        
        $popup_image = base_url().$cms_data[0]['page_image'];
        $exist_popup_image = $cms_data[0]['page_image'];

        //$profile_image = base_url($header['user_data']);
        
    }
?>
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Content Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
                <div class="breadcrumb-item">Edit Content Management</div>
            </div>
        </div>
        <div class="edit-cms-section">
             <?php $this->load->view("validation");?>
            <div class="row">
                <div class="col-md-12">
                     <form method="POST" action="<?php echo base_url('admin/Update_CMS')?>" enctype="multipart/form-data">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label>Page Type:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" value="<?php if(!empty($cms_data)){echo $cms_data[0]['page_key'];} ?>" maxlength="200" required="" placeholder="Enter Page Type" class="form-control boxed check_space valid_url"  disabled=""/>
                                <input type="hidden" name="page_key" value="<?php if(!empty($cms_data)){echo $cms_data[0]['page_key'];} ?>">
                            </div>
                        </div>
                        <?php 
                            if($cms_data[0]['page_key'] != 'offer_popup') 
                        { ?>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label>Page Title:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" value="<?php if(!empty($cms_data)){echo $cms_data[0]['page_name'];} ?>" maxlength="200" required=""  placeholder="Enter Page Title" name="page_name"  class="form-control boxed check_space valid_url" />
                                </div>
                            </div>
                        <?php } else{ ?>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label>URL:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="url" value="<?php if(!empty($cms_data)){echo $cms_data[0]['page_name'];} ?>" maxlength="200" required=""  placeholder="Enter Redirection URL" name="page_name"  class="form-control boxed check_space valid_url" />
                                </div>
                            </div>
                        <?php }
                        ?>
                        <?php if($cms_data[0]['page_key'] != 'offer_popup') 
                        { ?>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label>Page Content:</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea class="content_editor check_space ckeditor" name="page_value" required="">
                                        <?php if(!empty($cms_data)){echo $cms_data[0]['page_value'];} ?>
                                    </textarea>
                                </div>
                            </div>
                        <?php 
                        } else
                        { ?>
                            <textarea name="page_value" style="display: none !important;">NA</textarea>
                       <?php }
                        ?>
                        <?php if($cms_data[0]['page_key'] == 'offer_popup')
                        { ?>
                            <div class="form-group row">
                                <div class="col-md-3">
                                   <label>Offer Popup Image (374 × 696 px)</label>
                                </div>
                                <div class="col-md-8 admin-profile-img">
                                    <div class="row">
                                       <img id="disp_img" src="<?php echo $popup_image; ?>"  alt="admin profile" accept=".png, .jpg, .jpeg">
                                        <div class="img-add">                                      
                                            <input type="file" class="d-none" accept="image/x-png,image/jpeg, image/jpg" id="file" name="offer_popup_pic" onchange="document.getElementById('disp_img').src = window.URL.createObjectURL(this.files[0])" value="<?php echo  $exist_popup_image;?>">
                                            <label for="file"><i class="fas fa-pencil-alt"></i></label>
                                        </div>
                                        <span class="error" id="unfill_image"></span>
                                    </div>
                                </div>
                            </div>
                        <?php 
                        }
                        ?>
                        <div class="form-group row">
                            <div class="col-md-3"></div>
                            <div class="col-md-8">
                                <input type="hidden" name="page_primary_id" value="<?php if(!empty($cms_data)){echo $cms_data[0]['id'];} ?>"/>
                                <button type="submit" class="btn btn-primary change-password-btns mr-10">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="<?php echo base_url()?>assets/js/jquery.richtext.min.js"></script>


<script type="text/javascript">
    $(".content_editor").richText({
        imageUpload: false,
        fileUpload: false,
        videoEmbed: false,
        heading: false,
    });
    $(".content_editor_editcat").richText({
        imageUpload: false,
        fileUpload: false,
        videoEmbed: false,
    });
</script>
