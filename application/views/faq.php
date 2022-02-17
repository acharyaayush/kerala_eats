<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>FAQ</h1>
         <span>
            <div class="col-md-2">
               <!-- Pass 1 as parameter to call add user form -->
               <button type="button" class="btn btn-primary add_faq_btn  add_edit_faq" data-toggle="modal" id="" data-target="#add_edit_faq_modal" mode="1">Add FAQ</button>
            </div>
         </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">FAQ</div>
         </div>
      </div>
      <?php $this->load->view("validation");?>
      <div class="faq-section">
         <div class="table-responsive">
            <table class="table table-striped table-bordered">
               <thead>
                  <tr>
                     <th>Question</th>
                     <th style="width: 45%;">Answer</th>
                     <th>Created Date</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody id="faq_table">
                  <?php 
                   $this->load->view("faq-table-list");
                  ?>
               </tbody>
            </table>
            <nav class="text-xs-right">
              <?php if (isset($links)) { ?>
                  <?php echo $links; ?>
              <?php } ?>
            </nav>
         </div>
      </div>
   </section>
   <!-- ============================================================== -->
   <!-- Add FAQ Modal -->
   <!-- ============================================================== -->
   <div class="modal fade add-edit-faq-modal" id="add_edit_faq_modal">
      <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title modal_title_name"><i class="fa fa-puls"></i> Add FAQ</h4>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form name="item" action="<?php echo base_url('admin/Create_Update_FAQ');?>" method="post">
               <div class="modal-body">
                  <div class="form-group row">
                     <div class="col-sm-12">
                        <label class="form-control-label text-xs-right">Question
                        </label>
                        <input type="text" required="" placeholder="Enter Question" name=" question" class="form-control boxed" id="question">
                     </div>
                     <div class="col-sm-12">
                        <label class="form-control-label text-xs-right">Answer
                        </label>
                        <textarea type="text" required="" placeholder="Enter Answer" name="answer"
                           class="form-control boxed" id="answer"></textarea>
                     </div>
                  </div>
                  <div class="modal-footer">
                     <input type="hidden" value="" id="form_mode" name="mode"/>
                     <input type="hidden" value="" id="edit_faq_id" name="edit_faq_id"/>
                     <button type="submit" class="btn btn-primary">Submit
                     </button>
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  </div>
               </div>
            </form>
         </div>
         <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
   </div>
   <!-- ============================================================== -->
   <!-- End Add FAQ Modal -->
   <!-- ============================================================== -->
</div>

<?php

  $current_url =  current_url();
  $parameter_url = explode("faq/0", $current_url);// if action is load like enable/disable or delete
  if(isset($parameter_url[1])){
     $new_url_for_mode = 'admin/faq/table/'.$parameter_url[1].'';
  }else{
     $new_url_for_mode = 'admin/faq/table/';
  }

?>
<script type="text/javascript">
  var faq_table_url = '<?php echo  base_url(''.$new_url_for_mode .''); ?>';
</script>