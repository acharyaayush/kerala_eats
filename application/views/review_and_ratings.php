<!-- Main Content -->
<?php
 $restaurant_list = "";
 
  if (isset($resturant_details) && $resturant_details != "" && !empty($resturant_details)) 
  {
        $count = 1;
        $rest_id = $this->uri->segment(3);
        // echo "rest_id IS ".$rest_id;
        
        foreach ($resturant_details as $value) {
        $restaurant_id = $value['restaurant_id'];
        $restaurant_name = stripslashes($value['rest_name']);

        if($rest_id != 0)
        {

        }else
        {

        }

        if($restaurant_id == $rest_id)
        {
              $select = "selected";  
                
        }else{
             $select = "";
            
        }
        $restaurant_list.= '<option value="'.$restaurant_id.'" '.$select.'>'.$restaurant_name.'</option>';
         $count++;
      }
  }
  else
  {
    $restaurant_list = '<option value="">No Restaurant available </option>';
  }

?>
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Rating and Review</h1>
         <span>
            <div class="col-md-2">
            </div>
         </span>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Rating</div>
         </div>
      </div>
      <div class="section-body">
         <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
               <div class="card">
                <div class="col-5"></div>
                <div class="col-2">
                    <button class="btn btn-primary filter_button" id="" type="button">Filter</button>
                  </div>
                   <div class="card-header user_tables">
                        <!-- <div class="d-flex"> -->
                            <div class="product-filterForm">
                                <div class="row">
                                    <?php 

                                    //  if merchant is logged in, then this condition will check and only merchant restaurant prodcuts will show. if this blank that means super admin is logged in and then all resataurant will show
                                    if(!$this->logged_in_restaurant_id && $this->logged_in_restaurant_id == "" && $this->role == 1){
                                     ?>
                                     <div class="form-group col-md-3">
                                      <label>Restaurant</label>
                                      <select class="custom-select  wv_filter_box_height form-control search_data" name="restaurant_id" id="restaurant_id">
                                           <option value="0" selected="" >All Restaurant</option>
                                          <?php echo $restaurant_list;?>
                                      </select>
                                    </div>
                                    <?php
                                     }

                                    ?>
                                    <div class="form-group col-md-4">
                                        <div class="users_btns search_clear_btns  d-flex mt-20">
                                            <button class="btn btn-primary search_data" id="filter_review"  type="button">Search</button>
                                            <a href="<?php echo base_url('admin/review_listnig/0/')?>" class="btn btn btn-secondary m-0 clear_btns" > Clear</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- </div> -->
                    </div>
                    <div class="card-body table-flip-scroll orders-tables">
                     <table class="table ">
                        <thead>
                           <tr>
                              <th>Id</th>
                              <th>Restaurant Name</th>
                              <th>Customer name</th>
                              <th>Review</th>
                              <th>Rating</th>
                              <th>Review date</th>
                              <th>Action</th>
                              
                           </tr>
                        </thead>
                        <tbody>

                            <?php
                            if(count($review_data) > 0)
                            {
                                foreach($review_data as $review)
                                { 
                                    date_default_timezone_set('Asia/Singapore');
                                    ?>
                                    <tr>
                                      <td><?php echo $start; ?></td>
                                      <td><a href="<?php echo base_url('admin/add_edit_restaurant/2/'.$review['rest_id'].'/'.$review['admin_id']) ?>"><?php echo stripslashes($review['rest_name']); ?></a></td>
                                      <td><a href="<?php echo base_url('admin/user_details/3/'.$review['user_id']) ?>"><?php echo $review['fullname']?></a></td>
                                      <td><?php echo $review['review'] ; ?></td>
                                      <td><?php
                                          switch ($review['given_rating']) {
                                            case 1:
                                              //echo $avg_rating;
                                              $rating = '<span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star"></span>
                                                      <span class="fa fa-star "></span>
                                                      <span class="fa fa-star"></span>
                                                      <span class="fa fa-star"></span>';

                                              break;
                                            case 1.5:
                                              //echo $avg_rating;
                                             $rating = '<span class="fa fa-star-half-alt checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star "></span>
                                                      <span class="fa fa-star"></span>
                                                      <span class="fa fa-star"></span>';
                                              break;
                                            case 2:
                                              //echo $avg_rating;
                                             $rating = '<span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star "></span>
                                                      <span class="fa fa-star"></span>
                                                      <span class="fa fa-star"></span>';
                                              break;
                                            case 2.5:
                                              //echo $avg_rating;
                                             $rating = '<span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star-half-alt checked"></span>
                                                      <span class="fa fa-star"></span>
                                                      <span class="fa fa-star"></span>
                                                      <span class="fa fa-star"></span>';
                                              break;
                                            case 3:
                                              //echo $avg_rating;
                                             $rating = '<span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star"></span>
                                                      <span class="fa fa-star"></span>';
                                              break;
                                            case 3.5:
                                              //echo $avg_rating;
                                             $rating = '<span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star-half-alt checked"></span>
                                                      <span class="fa fa-star"></span>
                                                      <span class="fa fa-star"></span>';
                                              break;
                                            case 4:
                                             // echo $avg_rating;
                                             $rating = '<span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star"></span>';
                                              break;
                                            case 4.5:
                                              //echo $avg_rating;
                                             $rating = '<span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star-half-alt checked"></span>
                                                      <span class="fa fa-star"></span>';
                                              break;
                                            case 5:
                                              //echo $avg_rating;
                                             $rating = '<span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>
                                                      <span class="fa fa-star checked"></span>';
                                              break;


                                            default:
                                              //echo $avg_rating;
                                               $rating = '<span class="fa fa-star"></span>
                                                      <span class="fa fa-star"></span>
                                                      <span class="fa fa-star "></span>
                                                      <span class="fa fa-star"></span>
                                                      <span class="fa fa-star"></span>';
                                          }
                                       echo $rating;?>
                                       </td>
                                       <td><?php echo date('Y-m-d H:i',$review['created_at']) ; ?></td>
                                       <td>
                                            <a href="javascript:void(0)" class="review_delete" id="review_<?=$review['id']?>" data-id="" data-toggle="tooltip" title="Delete">Delete</a>
                                      </td>
                                   </tr>         
                                <?php
                                $start++;
                                }
                            }else
                            { ?>
                                <tr>
                                    <td colspan="8" style="text-align:center;">No data</td>
                                </tr>
                                
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
</div>

