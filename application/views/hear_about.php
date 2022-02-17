<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Hear about</h1>
      <span>
        <div class="col-md-2">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_more_source">Add More</button>
        </div>
    </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Hear about</div>
         </div>
      </div>
      <div class="section-body">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                <div class="col-5"></div>
                      <div class="col-2">
                        <!-- <button class="btn btn-primary filter_button" id="" type="button">Filter</button> -->
                      </div>
                 <!--  <div class="card-header user_tables">
                      <div class="promotionFileter">
                          <div class="promotionFilterFields">
                              <div class="row">
                                  <div class="form-group col-md-3">
                                      <label>From Date</label>
                                      <input  min="2020-01-01" autocomplete="off"placeholder="dd-mm-yyyy" id="fromdate" name="fromdate" max="" class="form-control fromdate " value="<?php if($fromdate != '' && $fromdate != 'all'){echo $fromdate;} ?>" /> -
                                     
                                  </div>
                                  <div class="form-group col-md-3">
                                      <label>To Date</label>
                                      <input  min="2020-01-01" autocomplete="off"placeholder="dd-mm-yyyy"  id="todate" name="todate" max="" class="form-control todate" value="<?php if($todate != '' && $todate != 'all'){echo $todate;} ?>" />
                                  </div>
                                  <div class="form-group col-md-3">
                                      <label>Status</label>
                                      <select class="custom-select promo_code_status wv_filter_box_height form-control" name="promo_code_status">
                                          
                                          <option value="">Select Status</option>
                                          <option value="1" <?php if($promo_code_status == '1' && $promo_code_status != 'all'){echo "selected";} ?>>Enable</option>
                                          <option value="2"<?php if($promo_code_status == '2' && $promo_code_status != 'all'){echo "selected";} ?>>Disabled</option>
                                      </select>
                                  </div>
                                  <div class="form-group col-md-3">
                                      <label>Type</label>
                                      <select class="custom-select promo_code_type wv_filter_box_height form-control" name="promo_code_type">
                                          <option value="">Select Type</option>
                                          <option value="1"<?php if($promo_code_type == '1' && $promo_code_type != 'all'){echo "selected";} ?>>FLAT</option>
                                          <option value="2" <?php if($promo_code_type == '2' && $promo_code_type != 'all'){echo "selected";} ?>>Percent</option>
                                      </select>
                                  </div>
                                  <div class="form-group col-md-3">
                                      <label>Promo Application Mode</label>
                                      <select class="custom-select promo_code_mode wv_filter_box_height form-control" name="promo_code_type">
                                          <?php echo $promo_code_mode_select ;?>
                                          <option value="">Select Mode</option>
                                          <option value="1"  <?php if($promo_code_mode == '1' && $promo_code_mode != 'all'){echo "selected";} ?>>Auto Apply</option>
                                          <option value="2"  <?php if($promo_code_mode == '2' && $promo_code_mode != 'all'){echo "selected";} ?>>Not Auto Apply</option>
                                      </select>
                                  </div>
                               
                                  <div class="form-group col-md-3">
                                      <form action="javascript:void(0);">
                                          <label>Search</label>
                                          <input type="search" name="search" class="form-control search_key wv_filter_box_height" placeholder="Search" id="search_user_list_data" value="<?php if($search != '' && $search != 'all'){echo $search;} ?>" />
                                      </form>
                                  </div>
                                  <div class="form-group col-md-6">
                                    <div class="search_clear_btns users_btns">
                                      <form action="javascript:void(0);">
                                          <button class="btn btn-primary m-0 mr-2 search_user_list_data" type="button" id="search_promo_code_list_data">Search</button>
                                           <a href="<?php echo base_url() ?>admin/promo_codes" class="btn btn-secondary mr-2 clear_btns">Clear</a>
                                  <button class="btn btn-secondary clear_btns export_promo_code_csv" type="button">Export CSV</button>
                              </form>
                          </div>
                                  </div>
                              </div>
                          </div>
                          
                      </div>

                  </div> -->
                  <?php $this->load->view("validation");?>
                 <div class="card-body catg-tab table-flip-scroll orders-tables tb-scroll">
                     <table class="table" id="">
                        <thead>
                           <tr>
                              <th>S.No</th>
                              <th>Name</th>
                              <th>Count</th>
                              <th>Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php
                              if(!empty($hear_about_us))
                              {
                                 $i = 1;
                                 foreach ($hear_about_us as $hear_about)
                                 { ?>
                                    <tr>
                                        <td><?php echo $i;?></td>
                                        <td><?php echo $hear_about['name'];?></td>
                                        <td><?php echo $hear_about['fcount'];?></td>
                                        <td>
                                          <?php if($hear_about['hear_abt_id'] != 1) 
                                          { ?>
                                            <label class="switch promocode-status"><input type="checkbox" id="<?php echo $hear_about['hear_abt_id'] ?>" class="change_hear_abt_status" name="<?php echo $hear_about['status'] ?>" <?php if($hear_about['status'] == '1'){ echo 'checked';} ?>><span class="slider round"></span>
                                            </label>
                                          <?php 
                                          } 
                                          ?>
                                        </td>
                                        <td>
                                          <?php if($hear_about['hear_abt_id'] != 1) 
                                          { ?>

                                            <a style="cursor:pointer" onclick="edit_more_source('<?php echo $hear_about['hear_abt_id'] ?>' , '<?php echo $hear_about['name'] ?>');">Edit</a>
                                            <a style="cursor:pointer" onclick="delete_hear_about(<?php echo $hear_about['hear_abt_id'] ?>)" data-id="" data-toggle="tooltip" title="Delete"><br>Delete</br>
                                            </a>
                                            <?php
                                          }
                                            ?>
                                        </td>
                                    </tr>    
                                <?php
                                $i++;
                                }
                              }else
                              { ?>
                                 <tr><td colspan="5">No Records Found </td></tr>
                              <?php
                              }
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
            </div>
         </div>
      </div>
   </section>




<!-- # ADD POPUP # -->
<div class="modal fade" id="add_more_source" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add More Source</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?php echo base_url('admin/add_edit_hear_about/1')?>" method="post">
        <div class="modal-body">
          <input type="text" class="form-control boxed" required="" name="hear_about_txt" placeholder="Place name here">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <!-- <button type="submit">Save Changes</button> -->
          <button type="submit" class="btn btn-primary" >Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- # EDIT POPUP # -->

<div class="modal fade" id="edit_more_source_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit More Source</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?php echo base_url('admin/add_edit_hear_about/2')?>" method="post">
        <div class="modal-body">
          <input type="text" class="form-control boxed" required="" id="edit_source_txt" name="hear_about_txt" placeholder="Place name here">
          <input type="hidden" id="edit_source_txt_id" name="hear_about_txt_id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <!-- <button type="submit">Save Changes</button> -->
          <button type="submit" class="btn btn-primary" >Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  var BASE_URL = "<?php echo base_url();?>";
  function edit_more_source(id,name)
  {
    // alert(name)
    $('#edit_more_source_modal').modal('show');
    $("#edit_source_txt").val(name);
    $("#edit_source_txt_id").val(id);
  }

  function delete_hear_about(id)
  {
    // alert("wwwwwwwwww "+id);
    swal({
          title: "Are you sure to delete this permanently?",
          text: "Once deleted, You will not be able to recover the action!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
              if (willDelete) {
                    // Ajax-------SATRT------------
                    
                      $.ajax({
                          url: BASE_URL+'admin/delete_hear_about',
                          data: { 
                              id: id
                          },
                          type: 'post',
                          success: function(response){
                            window.location.replace(BASE_URL+'admin/hear_about/');
                          },
                          
                      });//ajax end
                  // Ajax-------END------------
              }
            });
  }


</script>