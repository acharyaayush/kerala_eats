<?php 

$ad_banners_list_td = "";
if (isset($ad_banners_list) && !empty( $ad_banners_list)) {//parent IF--START--

  // echo "<pre>DATA PRINTING";
  // print_r($ad_banners_list);die;
  foreach ($ad_banners_list as $value) {
   
   $ad_banner_id = $value['id'];
   $restaurant_id = $value['restaurant_id'];
   $ad_image = $value['ad_image']; 
   $ad_image_web = $value['ad_image_web']; 
   $ad_image_mobile = $value['ad_image_mobile']; 
   $ad_name = $value['ad_name']; 
   $ad_description = $value['ad_description']; 
   $external_ink = $value['external_ink']; 
   $status = $value['status']; 

   //For Destop web  image -- 
   if($ad_image != 'assets/images/ad_banners/web_banner_image/')
   {
      if($ad_image != ""  && empty($header['user_data'])){
         # We need to check whether its an image or a video
         $mime = mime_content_type($ad_image);
         if(strstr($mime, "video/")){
             // this code for video
            $ad_image = '<video width="200" height="150" controls><source src="'.base_url().$ad_image.'" type="video/mov"></video>';
         }else if(strstr($mime, "image/")){
             // this code for image
            $ad_image = '<img src="'.base_url().$ad_image.'">';
         }

          // $ad_image = '<img src="'.base_url().$ad_image.'">'; 

      }else{
          $ad_image =  'No image';
      }
   }else
   {
      $ad_image =  'No image';
   }

   //For Mobile web image -- 
   if($ad_image_web != 'assets/images/ad_banners/web_mobile_banner_image/')
   {
      if($ad_image_web != ""  && empty($header['user_data'])){
         
         $ad_image_web = '<img src="'.base_url().$ad_image_web.'">'; 

      }else{
          $ad_image_web =  'No image';
      }      
   }else
   {
      $ad_image_web =  'No image';
   }

   //For Mobile image -- 
   if($ad_image_mobile != "" && empty($header['user_data'])){
        
         $ad_image_mobile = '<img src="'.base_url().$ad_image_mobile.'">'; 

      }else{
         
          $ad_image_mobile =  'No image';
      }

   //For Exrtnal link 
   if($external_ink  != ""){
      $external_ink = $external_ink;
   }else{
      $external_ink  = "-";
   }

   $ad_banners_list_td.=   '<tr>
                              <td>'.$start.'</td>
                              <td>'.$ad_image.'</td>
                              <td>'.$ad_image_web.'</td>
                              <td>'.$ad_image_mobile.'</td>
                              <td id="ad_banner_name_'.$ad_banner_id.'">'.$ad_name.'</td>
                              <td>'.$ad_description.'</td>';

   foreach ($restaurant_list as $restaurantvalue) {
        $restaurantlist_id = $restaurantvalue['id'];
        if($restaurant_id == $restaurantlist_id && $restaurant_id != 0){
             $ad_banners_list_td.=   '<td><a  href="'.base_url().'admin/add_edit_restaurant/2/'.$restaurant_id.'/'.$restaurantvalue['admin_id'].'/" ">'. $restaurantvalue['rest_name'].'</a></td>';
        }
   }
 
   if($restaurant_id == 0 ){
       $ad_banners_list_td.=   '<td> - </td>';
   }

   if($status == 1){
      $banner_enable = 'checked=""';
      $ad_banner_status_value = 2;// for gona disable
    }if($status == 2){
     $banner_enable = '';
      $ad_banner_status_value = 1;// for gona enable
   }

   $ad_banners_list_td.=   '<td>'.$external_ink.'</td>
                            <td> 
                                <label class="switch promocode-status">
                                    <input class="ad_banner_status" type="checkbox" '.$banner_enable.' id="ad_banner_status_'.$ad_banner_id.'" value="'.$ad_banner_status_value.'">
                                     <span class="slider round"></span>
                                </label>
                              </td>
                              <td>
                                 <div class="action_icons_dropdown">
                                    <i class="fa fa-ellipsis-v"></i>
                                    <div class="dropdown-content">
                                       <a href="" class="add_edit_ad_banner_popup" data-target="#add_edit_ad_banner_popup" data-toggle="modal" mode="2" edit_id="'.$ad_banner_id.'" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" title="Edit">Edit</a>
                                       <!--<a class="" href="" data-toggle="tooltip" title="Show Details"> <i class="fa fa-eye"></i> </a>-->
                                       <a  href="javascript:void(0)" class="ad_banner_delete" id="ad_banner_'.$ad_banner_id.'" data-id="" data-toggle="tooltip" title="Delete"
                                          >
                                       Delete
                                       </a>
                                    </div>
                                 </div>
                              </td>
                           </tr>';
                           $start++;
   }//Foreach loop ---End---

}else{
    $ad_banners_list_td = "<tr><td colspan='9'class='no-records'>No Records Found </td></tr>";
}//parent IF--END--
?>
<thead>
   <tr>
      <th>S No.</th>
      <th>Image</th>
      <th>Mobile Image Web</th>
      <th>Mobile Image</th>
      <th>Name</th>
      <th>Text</th>
      <th>Restaurants Name</th>
      <th>External Link</th>
      <th>Status</th>
      <th>Action</th>
   </tr>
</thead>
<tbody>
   <?php echo $ad_banners_list_td;?>
</tbody>