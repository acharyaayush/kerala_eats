<?php

#getting limited charcater from the sentense
function charlimit($string, $limit) {
   $overflow = (strlen($string) > $limit ? true : false);
   return substr($string, 0, $limit) . ($overflow === true ? "....." : '');
}

$faq_list_tr = "";
if(!empty($faq_data)){
   foreach ($faq_data as  $value) {
      $faq_id = $value['id'];
      $question = stripslashes($value['question']);
      $answer = stripslashes($value['answer']);
      $created_at = $value['created_at'];
      $status = $value['status'];

      $created_at  = date("d-m-Y",$created_at);// convert UNIX timestamp to PHP DateTime

      #only showing few charcater from the discription
      $answer =  charlimit($answer, 200);

      $faq_list_tr .= '<tr>
                           <td>'.$question.'</td>
                           <td>'.$answer.'</td>
                           <td>'.$created_at.'</td>
                           <td>
                              <div class="d-flex">
                                 <a href="javascript:void(0);" class="add_edit_faq  mr-2" data-toggle="modal" id="" data-target="#add_edit_faq_modal" mode="2" faq_id="'.$faq_id.'">Edit</a>
                                 <a  href="javascript:void(0);" class="text-danger faq_delete" faq_id="'.$faq_id.'">Delete</a>
                               </div>
                           </td>
                        </tr>';
   }
}else{
   $faq_list_tr = "<tr><td class='text-center' colspan='4' class='no-records'>No Records Found </td></tr>";
}
echo $faq_list_tr;
?>