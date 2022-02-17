<?php 
 
#getting limited charcater from the sentense
function charlimit($string, $limit) {
   $overflow = (strlen($string) > $limit ? true : false);
   return substr($string, 0, $limit) . ($overflow === true ? "....." : '');
}

$cms_list_td = "";
if (isset($cms_data) && !empty($cms_data)) {
  foreach ($cms_data as $value) {
      $page_primary_id = $value['id'];
      $page_key    = $value['page_key'];
      $page_name    = $value['page_name'];
      $page_value = strip_tags($value['page_value']);
      $status = $value['status'];//1 - Enable, 2 - Disable  
      $updated_at = $value['updated_at'];

      //convert epoch time on normal date time 
      $updated_at = new DateTime("@$updated_at");  // convert UNIX timestamp to PHP DateTime
      $updated_at = $updated_at->format('d-m-Y');

       #only showing few charcater from the discription
       $page_value =  charlimit($page_value, 200);

      if($status == 1){
          $checked = "checked = ''";
      }else{ 
        $checked  = "";
      }

      $cms_list_td.= '<tr>
                        <td>'.$page_key.' </td>
                        <td>'.$page_name.'</td>
                        <td>'.$page_value.'</td>
                        <td>'.$updated_at.'</td>
                        <td><label class="switch promocode-status">
                                <input type="checkbox" '.$status.' class="cms_status" cms_id="'.$page_primary_id.'" '. $checked.'>
                                <span class="slider round"></span>
                                </label></td>
                        <td>
                          <div class="d-flex">
                               <a href="'.base_url().'admin/edit_cms/'.$page_primary_id.'" class="mr-2"> Edit</a>
                               <!-- <button class="btn btn-danger btn-action"><i class="fas fa-trash-alt"></i></button> -->
                            </div>
                        </td>
                     </tr>';
     } 
}
?>
<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Content Management</h1>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Content Management</div>
         </div>
      </div>
      <div class="faq-section">
         <?php $this->load->view("validation");?>
         <div class="table-responsive">
            <table class="table table-striped table-bordered">
               <thead>
                  <tr>
                     <th>Page Type</th>
                     <th>Title</th>
                     <th style="width: 50%;">Page Content:</th>
                     <th>Updated Date</th>
                     <th>Status</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php echo $cms_list_td;?>
               </tbody>
            </table>
         </div>
      </div>
   </section>
</div>