 var promotion_mode_type;

 //back function --start ---
 function goBack() {
    window.history.back();
  }
 //back function --end ---

 var email_status = false;
/* # To close the validation message box when click on cross icon function-- START--*/

    $( document ).ready(function() {
        var close = document.getElementsByClassName("closebtn");
        // alert()
        var i;
        $(".closebtn").click(function(){
            $(".alert").hide();
        });
        for (i = 0; i < close.length; i++) {
          close[i].onclick = function(){
            var div = this.parentElement;
            div.style.opacity = "0";
            setTimeout(function(){ div.style.display = "none"; }, 600);
          }
        }
    });

  /* # To close the validation message box when click on cross icon function --END--*/

/*New Password and confirm password match validation ------START-----*/
function validatechngpwdform()
    {
        var np_len = $(".np_password").val().length;
        var cnp_len = $(".cnp_password").val().length;
        var np_val = $(".np_password").val();
        var cnp_val = $(".cnp_password").val();
        var special_chars = "<>@!#$%^&*()_+[]{}?:;|'\"\\,./~`-=";
        if(np_len > 12 || cnp_len > 12 || np_len < 6 || cnp_len < 6)
        {
            $(".alert").removeClass('d-none');
            $(".np_password").css('border-color','red');
            $("#pwd_error").html('Password length must be in between 6 to 12 characters');
            return false;
        }else if(np_val != cnp_val)
        {
            $(".alert").removeClass('d-none');
            $(".np_password").css('border-color','red');
            $("#pwd_error").html('New password and confirm new password does not match');
            return false;
        }else if(np_val.match(/[A-Z]/) && np_val.match(/\d/) && np_val.match(/[A-z]/) && np_val.match(/[~!,@#%&_\$\^\*\?\-]/))
        {
            
            $(".alert").removeClass('d-none'); 
            $(".alert").addClass('success'); 
            $(".np_password").css('border-color','green');
            $("#pwd_error").html('Please wait while we are processing your request...');

            setTimeout(function(){ 
               $("#final_submit").attr("disabled",true);
             }, 1000);

            return true;
            
        }else
        {   $(".alert").removeClass('d-none');
            $(".np_password").css('border-color','red');
            $("#pwd_error").html('Password must include alphanumeric, special characters and capital letters');
            return false;
        }
 }
  /*New Password and confirm password match validation ------END-----*/

/*First name last name validation ----START----*/
    // # UT To limit the first name and last name characters to 20 chars
    $(".first_name_length").keypress(function(e){
        var length = $(".first_name_length").val().length;
        if(length >= 20)
        {
            return false;
        }
    });
    
    $(".last_name_length").keypress(function(e){
        var length = $(".last_name_length").val().length;
        if(length >= 20)
        {
            return false;
        }
    });
    $(".full_name_length").keypress(function(e){
        var length = $(".full_name_length").val().length;
        if(length >= 40)
        {
            return false;
        }
    });
    // # UT To limit the first name and last name characters to 20 chars
/*First name last name validation ----END----*/

// Below function does not allow to enter space at first position --START--
   $(".check_space").keydown(function(e){
        if (e.which === 32 &&  e.target.selectionStart === 0) {
            return false;
        }
    });
// Below function does not allow to enter space at first position --END--

// Below function  allow to enter only alphabets ------START-----
  $(".only_alphabets").keypress(function(event){
            var inputValue = event.charCode;
            if(!(inputValue >= 65 && inputValue <= 122) && (inputValue != 32 && inputValue != 0)){
                event.preventDefault();
            }
        });
// Below function  allow to enter only alphabets ------END----- 

// # UT : Contact number should work for only numbers and backspace --START---
    $(".contact_number").keypress(function(e){
        var charCode = (e.which) ? e.which : event.keyCode;
        if (charCode != 45 && charCode > 31 && (charCode < 48 || charCode > 57))
        {
            return false;
        }else
        {
            return true;
        }

    });
// Contact number should work for only numbers and backspace --END---

// UT : To check email validity
function isValidEmail()
{
    var email = $(".valid_email").val();
    var length = email.length;
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(!regex.test(email) || length >= 100) {
        $("#valid_email_des").css('border-color','red');
        $("#invalid_email").html('Invalid email');
        return false;
    }else{
        $("#valid_email_des").css('border-color','#ccc');
        $("#invalid_email").html('');
        return true;
    }
}

//validate Email if after .com any type eny text ----START----
function validateEmail(emailField,email_valid,invalid_email){//email_valid =  input field id, invalid_email = error show id
        var email_reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if (email_reg.test(emailField.value) == false) 
        {
            $(email_valid).css('border-color','red');
            $(invalid_email).html('Invalid email');
            $('#submit_btn').attr('type','button');
            email_status = false;

             
            return false;
        }
        $(email_valid).css('border-color','#ccc');
        $(invalid_email).html('');
        email_status = true;
        return true;

}
//validate Email if after .com any type eny text ----END----

//If any error are exist in a page then will not  submit form ---START

$("#submit_btn").click(function(){
    //Retrieves html string
    var htmlString = $('body').html().toString();

    //The indexOf() method returns the position of the first occurrence of a specified value in a string. This //method returns -1 if the value to search for never occurs.
    var index = htmlString.indexOf("Please Fill Vaild Email ID");

    if (index != -1){
         $('html, body').animate({
            scrollTop: $("#error_find").offset().top
        }, 2000);
    }else{
        $('#submit_btn').attr('type','submit');
    }
        
});
//If any error are exist in a page then will not  submit form ---END

//Add resturant data submit ---START----
$( document ).ready(function() {

    //For discription lenght should be 360 --- START-----
    function check_description_length(){//email_valid =  input field id, invalid_email = error show id
           var length = $(".description_length").val().length;
            if(length >= 360)
            {
                $(this).css('border-color','red');
                $('#invalid_description_length').html('Invalid description length. It should have a maximum of 360 characters.');
                return false;
            }else{
                $(this).css('border-color','#ccc');
                 return true;
            }

}
$(".description_length").keypress(function(e){
        check_description_length();
}); 

//Show merchant detail on change select option ----START------

 $('body').on('change', '#select_merchant', function() {
    var user_id = $(this).val();
     $.ajax({
            url: BASE_URL+'admin/selected_user_detail/',//merchant detail
            data: { 
                user_id: user_id,
            },
            type: 'post',
            success: function(response){

              if(response != 0){
                 var data = JSON.parse(response);
                  
                  $('#res_email_valid').val(data.email);
                  $('#res_phone').val(data.mobile);
                }
            },
            
        });
 });
//Show merchant detail on change select option ----END------

//validate postal code input
$(".postal_code_length, #minimum_quantity, #maximum_quantity,#discount_value,#max_allowed_time,#minimum_order_amount,#referrer_discount_value,#referrer_max_discount_value,#referee_discount_value,#max_discount_value,input[name='rest_commission_value'],input[name='delivery_per_km_charge'],input[name='rest_order_preparation_time'],input[name='edit_order_preparation_time'],input[name='basic_delivery_time'],input[name='basic_preparation_time'],input[name='outstanding_amount_for_deduct']").keypress(function(e){
      var charCode = (e.which) ? e.which : event.keyCode    

      if (String.fromCharCode(charCode).match(/[^0-9]/g))    

      return false;   
}); 
//unit number--validation
/*var unit_number_validate_status = false;
$(".pin_address_validation").keypress(function(e){
       var pin_address_validation = $(this).val(); 

              var regex = /([-#0-9])/;    
              if(!regex.test(pin_address_validation)){    
                  
                  unit_number_validate_status = false;
                  
              }  else{

                  unit_number_validate_status = true;
              }

      //return false; 
     
});*/

//check length of max discount value
$("#max_discount_value").keypress(function(e){
    var length = $("#max_discount_value").val().length;
    if(length >= 8)
    {
        return false;
    }
});

//Add and edit  resturant data submit ----------------START----------------

   // if select business type food then foodtype will be visible other wise it will be disabled
  $('body').on('change', '#select_merchant_cateogry', function() {
    //If restaurant's business type is food then this column will contain value. So value 0 (if not applicable) 1 (Restaurant) 2 (Kitchen)
        var business_type = $(this).val();
       
        if(business_type == 1){// 1 for food, 2 -grocery , 3 -alcohol 
            $('#select_food_type').removeAttr('disabled');
        }else{
            $('#select_food_type').attr('disabled','disabled');
            $('#select_food_type').val('');
        }
  });

  // image file size validation
  var logo_image_check_validation = true;
    $("#file").bind("change", function () {
         var logo_image_size =(this.files[0].size);
         
        if(logo_image_size > 1000000) {// 1 MB
           $('#unfill_logo_image').text('Image Size should be less then 1 MB');
           logo_image_check_validation = false;
        }else{
           $('#unfill_logo_image').text('');
            logo_image_check_validation = true;
        }

        if(logo_image_check_validation == true){
             var file = $(this)[0].files[0];
              img = new Image();
              var imgwidth = 0;
              var imgheight = 0;
              var maxwidth = 1000;
              var maxheight = 350;

              img.src = _URL.createObjectURL(file);
                  img.onload = function() {
                   imgwidth = this.width;
                   imgheight = this.height;
                 
                   $("#width").text(imgwidth);
                   $("#height").text(imgheight);
                   if(imgwidth <= maxwidth && imgheight <= maxheight){
                 
                    var formData = new FormData();
                    formData.append('fileToUpload', $('#file')[0].files[0]);

                    $('#unfill_logo_image').text('');
                    logo_image_check_validation = true;
                  }else{
                    $('#unfill_logo_image').text("Image size must be ("+maxwidth+"X"+maxheight+" PX)");
                    logo_image_check_validation = false;
                  }
            }
        } 
    });
 
   var banner_image_check_validation = true;
    var _URL = window.URL || window.webkitURL;
   $('#file2').bind('change', function() {// restaurant banner image
        var banner_image_size =(this.files[0].size);
        if(banner_image_size > 1000000) {// 1 MB
            $('#unfill_banner_image').text('Image Size should be less then 1 MB');
           banner_image_check_validation = false;
        }else{
            $('#unfill_banner_image').text('');
            banner_image_check_validation = true;
        }

        if(banner_image_check_validation == true){
             var file = $(this)[0].files[0];
              img = new Image();
              var imgwidth = 0;
              var imgheight = 0;
              var maxwidth = 1920;
              var maxheight = 360;

              img.src = _URL.createObjectURL(file);
                  img.onload = function() {
                   imgwidth = this.width;
                   imgheight = this.height;
                 
                   $("#width").text(imgwidth);
                   $("#height").text(imgheight);
                   if(imgwidth <= maxwidth && imgheight <= maxheight){
                 
                    var formData = new FormData();
                    formData.append('fileToUpload', $('#file')[0].files[0]);

                    $('#unfill_banner_image').text('');
                    banner_image_check_validation = true;
                  }else{
                    $('#unfill_banner_image').text("Image size must be ("+maxwidth+"X"+maxheight+" PX)");
                    banner_image_check_validation = false;
                  }
            }
        }     
    });

    var mobile_banner_image_check_validation = true;
   $('#file3').bind('change', function() {// restaurant  mobile banner image
         var mobile_banner_image_size =(this.files[0].size);
        if(mobile_banner_image_size > 1000000) {// 1 MB
            $('#unfill_mobile_banner_image').text('Image Size should be less then 1 MB');
           mobile_banner_image_check_validation = false;
        }else{
            $('#unfill_mobile_banner_image').text('');
            mobile_banner_image_check_validation = true;
        }

        if(mobile_banner_image_check_validation == true){
             var file = $(this)[0].files[0];
              img = new Image();
              var imgwidth = 0;
              var imgheight = 0;
              var maxwidth = 768;
              var maxheight = 384;
              
              img.src = _URL.createObjectURL(file);
                  img.onload = function() {
                   imgwidth = this.width;
                   imgheight = this.height;
                 
                   $("#width").text(imgwidth);
                   $("#height").text(imgheight);
                   if(imgwidth <= maxwidth && imgheight <= maxheight){
                 
                    var formData = new FormData();
                    formData.append('fileToUpload', $('#file')[0].files[0]);

                    $('#unfill_mobile_banner_image').text('');
                    mobile_banner_image_check_validation = true;
                  }else{
                    $('#unfill_mobile_banner_image').text("Image size must be ("+maxwidth+"X"+maxheight+" PX)");
                    mobile_banner_image_check_validation = false;
                  }
            }
        } 
    });


   //check open  close time validation------------------------START---------------
   var open_close_time_validate_status = true;
   $('body').on('change', '#open_time', function() {
    //Open time less then close time
    // close time greater then open time
      var open_time = $(this).val();
      var close_time = $('#close_time').val();
       if(open_time>close_time){
            open_close_time_validate_status = false;
            $('#open_time').css('border-color','red');
            $('#unfill_open_time').text('Open time should be smaller');
        }else{
           open_close_time_validate_status = true;
           $('#close_time').css('border-color','#ccc');
           $('#unfill_close_time').text('');

           $('#open_time').css('border-color','#ccc');
           $('#unfill_open_time').text('');
        }
   });
   $('body').on('change', '#close_time', function() {
    //Open time less then close time
    // close time greater then open time
      var close_time = $(this).val();
      var open_time = $('#open_time').val();


       if(close_time<open_time){
           open_close_time_validate_status = false;
           $('#close_time').css('border-color','red');
            $('#unfill_close_time').text('Close time should be Greater');
        }else{
            open_close_time_validate_status = true;
           $('#open_time').css('border-color','#ccc');
           $('#unfill_open_time').text('');

           $('#close_time').css('border-color','#ccc');
           $('#unfill_close_time').text('');
        }
   });
   //check open close validation------------------------END---------------

   //check break time validation-------------------------START----------------

   function common_function_break_start_time_validation(break_start_time, open_time, break_end_time,close_time){
        if(break_start_time >= open_time && break_start_time <= close_time){
          //between
         if(break_start_time>break_end_time){
            break_time_validate_status  = false;
            $('#break_start_time').css('border-color','red');
            $('#unfill_break_start_time').text('Time should be smaller');
         }else{
            break_time_validate_status  = true;
            break_time_validate_status  = true;
            $('#break_start_time').css('border-color','ccc');
            $('#unfill_break_start_time').text('');

            $('#break_end_time').css('border-color','ccc');
             $('#unfill_break_end_time').text('');
         }
       }else{
         // alert('not between');
          break_time_validate_status  = false;
         $('#break_start_time').css('border-color','red');
         $('#unfill_break_start_time').text('Time should be between open and close time');
       } 

        return break_time_validate_status;
   }
   var break_time_validate_status = true;
    $('body').on('change', '#break_start_time', function() {
   
      var break_start_time = $(this).val();

      var open_time = $('#open_time').val();
      var close_time = $('#close_time').val();

      var break_end_time = $('#break_end_time').val();

    common_function_break_start_time_validation(break_start_time, open_time, break_end_time,close_time);
   });

//comman function for check break time validation

function common_function_break_end_time_validation(break_end_time, open_time, break_start_time,close_time){
     if(break_end_time >= open_time && break_end_time <= close_time){
        //alert('between');
        
         if(break_end_time<break_start_time){
             break_time_validate_status  = false;
             $('#break_end_time').css('border-color','red');
             $('#unfill_break_end_time').text('Time should be Greater');
         }else{
             break_time_validate_status  = true;
             break_time_validate_status  = true;
            $('#break_start_time').css('border-color','ccc');
            $('#unfill_break_start_time').text('');

             $('#break_end_time').css('border-color','ccc');
             $('#unfill_break_end_time').text('');
         }
       }else{
        //alert('not between');
         break_time_validate_status  = false;
         $('#break_end_time').css('border-color','red');
         $('#unfill_break_end_time').text('Time should be between open and close time');
       }

       return break_time_validate_status;

}

  $('body').on('change', '#break_end_time', function() {
   
      var break_end_time = $(this).val();

      var open_time = $('#open_time').val();
      var close_time = $('#close_time').val();

      var break_start_time = $('#break_start_time').val();

      common_function_break_end_time_validation(break_end_time, open_time, break_start_time,close_time);
   });
   //check break time validation-------------------------END----------------

//get latitude and long lontitude from restaurant steert(pin address)
  $('body').on('click', '#get_res_lat_long', function() {
        var pin_address = $('#pin_address').val();

        var getLocation =  function(address) {
          var geocoder = new google.maps.Geocoder();
          geocoder.geocode( { 'address': address}, function(results, status) {

          if (status == google.maps.GeocoderStatus.OK) {
              var latitude = results[0].geometry.location.lat();
              var longitude = results[0].geometry.location.lng();
              $('#res_latitude').val(latitude);
              $('#res_longtitude').val(longitude);
              console.log(latitude, longitude);
              } 
          }); 
        }
        getLocation(pin_address);
        setTimeout(function(){  $('#add_edit_res_submit').trigger('click'); }, 1000);
  });

  $('body').on('click', '#add_edit_res_submit', function() {
 
        //edit mode --------------------------
        var edit_restaurant_id = $('#restaurant_id').val();//it will use at the time off edit mode
        var  merchant_id = $('input[name="admin_id"]').val();
        var logo_image;
        var banner_image;

        var banner_method_mode;
        var logo_method_mode;

        var banner_new_upload;
        var logo_new_upload;
         //edit mode --------------------------

        var user_id = $('#select_merchant').val();// merchant user , role = 2
        var res_email_valid = $('#res_email_valid').val();
        var res_phone = $('#res_phone').val();
        var postal_code = $('#postal_code').val();

        var pin_address = $('#pin_address').val();
        var res_latitude = $('#res_latitude').val();
        var res_longtitude = $('#res_longtitude').val();
      

        var unit_number = $('#unit_number').val();
        var street_address = $('#street_address').val();
        var res_name = $('#res_name').val();

       //delivery handle by 
        var delivery_handled_by = $('#delivery_handled_by:checked').val();



        //restaurant accept type
        var selected_rest_accept_type = [];
        var check_accept_type_select_or_not = 0;
          $.each($(".selected_rest_accept_type:checked"), function(){
              selected_rest_accept_type.push($(this).attr('id'));
              check_accept_type_select_or_not++;
          });

        if(check_accept_type_select_or_not == 0){
            $('#unselect_accept_type').text('Please Select accept type');
        }else{
            $('#unselect_accept_type').text('');
        }

        var res_open_time_fill_status = true;
        var res_close_time_fill_status = true;

        var res_break_start_fill_status = true;
        var res_break_end_fill_status = true;
       
        // restaurant time 
        if(mode_type == 1){
           //open close time------------------START-------
            var open_time = $('#open_time').val();
            var close_time = $('#close_time').val();
            
             if(open_time == ""){
                 $('#open_time').css('border-color','red');
                 $('#unfill_open_time').text('Please Select open time');
                 res_open_time_fill_status = false;
                 
             }else if(open_close_time_validate_status == true){
                 if(open_time == close_time){
                     res_open_time_fill_status = false;
                     $('#open_time').css('border-color','red');
                     $('#unfill_open_time').text('Open time and Close time should not be equal');
                 }else{
                     $('#open_time').css('border-color','#ccc');
                    $('#unfill_open_time').text('');
                     res_open_time_fill_status = true;
                 }
             }

             if(close_time == ""){
                 $('#close_time').css('border-color','red');
                 $('#unfill_close_time').text('Please Select close time');
                  res_close_time_fill_status = false;
                 
             }else if(open_close_time_validate_status == true){
                 if(open_time == close_time){
                      $('#close_time').css('border-color','red');
                      $('#unfill_close_time').text('Open time and Close time should not be equal');
                      res_close_time_fill_status = false;
                }else{
                    $('#close_time').css('border-color','#ccc');
                    $('#unfill_close_time').text('');
                    res_close_time_fill_status = true;
                 }
             }

            
              //open close time------------------END-----
              
               var break_start_time = $('#break_start_time').val();
               var break_end_time = $('#break_end_time').val();

              
             
             

             // check break  start and end time if given then both values should be  fill  other wise both value  will be  blank
             if(break_start_time =="" && break_end_time==""){
                res_break_start_fill_status = true;
                res_break_end_fill_status = true;
                 $('#break_start_time').css('border-color','#ccc');
                 $('#unfill_break_start_time').text('');

                 $('#break_end_time').css('border-color','#ccc');
                $('#unfill_break_end_time').text('');
             }else{
                  // check break  start and end time if given then both values should be  fill  other wise both value  will be  blank
                   if(break_start_time!=""){
                    res_break_start_fill_status  =  common_function_break_start_time_validation(break_start_time, open_time, break_end_time,close_time);
                     }

                 if((break_end_time == break_start_time && break_start_time!="") || (break_start_time =="" && break_end_time!="")){
                       res_break_start_fill_status = false;
                    $('#break_start_time').css('border-color','red');
                    $('#unfill_break_start_time').text('Start time and end time should not be equal');
                 }else if(break_time_validate_status == true){
                     res_break_start_fill_status = true;
                      $('#break_start_time').css('border-color','#ccc');
                      $('#unfill_break_start_time').text('');
                 }
              

                 if((break_end_time == break_start_time && break_end_time!="") || (break_end_time =="" && break_start_time!="")){
                    $('#break_end_time').css('border-color','red');
                    $('#unfill_break_end_time').text('Start time and end time should not be equal');
                    res_break_end_fill_status = false;
                 }else if(break_time_validate_status == true){
                    $('#break_end_time').css('border-color','#ccc');
                    $('#unfill_break_end_time').text('');
                    res_break_end_fill_status = true;
                 }

                if(break_end_time!=""){
                    res_break_end_fill_status = common_function_break_end_time_validation(break_end_time, open_time, break_start_time,close_time);
                 }

                 //check break time is not equal , same like open and close time 
                 if((res_break_end_fill_status == true || res_break_start_fill_status == true) && (break_start_time!="" || break_end_time!="")){

                    if(open_time == break_start_time || close_time == break_start_time){
                         $('#break_start_time').css('border-color','red');
                         $('#unfill_break_start_time').text('Time should be between open and close time');
                        res_break_end_fill_status = false;
                    }else if(open_time == break_end_time || close_time == break_end_time){
                        $('#break_end_time').css('border-color','red');
                        $('#unfill_break_end_time').text('Time should be between open and close time');
                         res_break_end_fill_status = false;
                    }else{
                         res_break_end_fill_status = true;
                    }
                 }
             }


        }else{
            open_close_time_validate_status == true;
            break_time_validate_status = true;

            res_open_time_fill_status = true;
            res_close_time_fill_status = true;

              res_break_start_fill_status = true;
              res_break_end_fill_status = true;
        }
  

        var business_type = $('#select_merchant_cateogry').val();//business type
        var food_type = $('#select_food_type').val();

        var res_description = $('#res_description').val();

        var fd = new FormData();
        var file1 = $('#file')[0].files;//uploded logo_image
        var file2 = $('#file2')[0].files;//uploded banner image
        var file3 = $('#file3')[0].files;//uploded mobile image

        var mode_url;//check type add mode or edit mode

       /* if(file1.length == 0 && exist_logo_image == "" || logo_image_check_validation == false){
          $('#unfill_logo_image').text('Please Select Valid Logo Image');
        }else{
              
          $('#unfill_logo_image').text('');
        }

        if(file2.length == 0 && exist_banner_image == ""  || banner_image_check_validation == false){
          $('#unfill_banner_image').text('Please Select Valid Banner Image');
        }else{
              
          $('#unfill_banner_image').text('');
        }

        if(file3.length == 0 && exist_mobile_banner_image == "" || mobile_banner_image_check_validation == false){
          $('#unfill_mobile_banner_image').text('Please Select Valid Mobile Image');
        }else{
          $('#unfill_mobile_banner_image').text('');
        }*/

        if(user_id == ""){
            $('#select_merchant').css('border-color','red');
            $('#unfill_name').text('Please Select Merchant Name');
        }else{
             $('#select_merchant').css('border-color','#ccc');
             $('#unfill_name').text('');
        }

        if(res_email_valid == ""){
            $('#res_email_valid').css('border-color','red');
            $('#res_invalid_email').text('Please Select Merchant for Email ID');
        }else{
            $('#res_email_valid').css('border-color','#ccc');
            $('#res_invalid_email').text('');
        }
    
        if(res_phone == ""){
             $('#res_phone').css('border-color','red');
            $('#unfill_res_phone').text('Please Select Merchant for Contact Number');
        }else{
             $('#res_phone').css('border-color','#ccc');
            $('#unfill_res_phone').text('');
        }

        if(postal_code == ""){
            $('#postal_code').css('border-color','red');
            $('#unfill_postal_code').text('Please Fill Restaurant Postal Code');
        }else{
            $('#postal_code').css('border-color','#ccc');
            $('#unfill_postal_code').text('');
        }

        if(pin_address == ""){
            $('#pin_address').css('border-color','red');
            $('#unfill_pin_address').text('Please Fill Restaurant Pin Address');
        }else{
            $('#pin_address').css('border-color','#ccc');
            $('#unfill_pin_address').text('');
        }

         if( mode_type == 2){
            //edit time mode email check on click
             var pin_address_validation = $('.pin_address_validation').val(); 
        }

        if(unit_number == ""){
            $('#unit_number').css('border-color','red');
            $('#unfill_unit_number').text('Please Fill Vaild Unit Number');
        }else{
            $('#unit_number').css('border-color','#ccc');
            $('#unfill_unit_number').text('');
        }

        if(res_name == ""){
             $('#res_name').css('border-color','red');
            $('#unfill_res_name').text('Please Fill Restaurant Name');
        }else{
            $('#res_name').css('border-color','#ccc');
            $('#unfill_res_name').text('');
        }   
       

       if(business_type == "" ){//If restaurant's business type is food then this column will contain value. So value 0 (if not applicable) 1 (Restaurant) 2 (Kitchen)
             $('#select_merchant_cateogry').css('border-color','red');
            $('#unselect_business_type').text('Please Select Business Type');
        }else{
            $('#select_merchant_cateogry').css('border-color','#ccc');
            $('#unselect_business_type').text('');
        }   
 
        if(food_type == "" && business_type == 1){//business_type should be food//If restaurant's business type is food then this column will contain value. So value 0 (if not applicable) 1 (Restaurant) 2 (Kitchen)
             $('#select_food_type').css('border-color','red');
            $('#unselect_food_type').text('Please Select Food Type');
        }else{
            $('#select_food_type').css('border-color','#ccc');
            $('#unselect_food_type').text('');
        }  
      

        if(res_description == ""){
            $('#res_description').css('border-color','red');
            $('#invalid_description_length').text('Please Fill Discription about Restaurant');
        }else{
             $('#res_description').css('border-color','#ccc');
            $('#invalid_description_length').text('');
        }

        //Edit time checking--------START-----------------
        var upload_logo_image_status = true;
        var upload_banner_image_status = true;
        var upload_mobile_banner_image_status = true;

          ///////////////////////////////////
         if(exist_logo_image != "" && file1.length == 0){// pre LOGO image 
             fd.append('logo_image',exist_logo_image);
             upload_logo_image_status = false;


           }else if(file1.length > 0){
            
             fd.append('logo_image',file1[0]);
              upload_logo_image_status = true;
           }else{
              //upload_logo_image_status = false; // if image upload is mendatory
           }

            ///////////////////////////////////
           if(exist_banner_image != "" && file2.length == 0){// pre BANNER image 
             fd.append('banner_image',exist_banner_image);
             upload_banner_image_status = false;


           }else if(file2.length > 0){
            
              fd.append('banner_image',file2[0]);
              upload_banner_image_status = true;
           }else{
              //upload_banner_image_status = false; // if image upload is mendatory
           }


           ///////////////////////////////////
          if(exist_mobile_banner_image != "" && file3.length == 0){// pre BANNER image 
             fd.append('mobile_banner_image',exist_mobile_banner_image);
             upload_mobile_banner_image_status = false;

           }else if(file3.length > 0){// pre Mobile BANNER image 
            
              fd.append('mobile_banner_image',file3[0]);
              upload_mobile_banner_image_status = true;
           }else{
              //upload_mobile_banner_image_status = false; // if image upload is mendatory
           }
         //Edit time checking------------------END-------------------
 
      if(user_id != "" && res_phone != "" && res_email_valid != "" && res_name != "" && business_type != "" && ((food_type != "" && business_type == 1 )|| (food_type ==""  && business_type != 1)) && res_description != "" && postal_code != "" && pin_address != "" &&  unit_number != ""  && (upload_logo_image_status == true || exist_logo_image !="") &&  (upload_banner_image_status == true || exist_banner_image !="")  &&  (upload_mobile_banner_image_status == true || exist_mobile_banner_image !="") && check_accept_type_select_or_not >0 && delivery_handled_by != "" && res_open_time_fill_status == true  && res_close_time_fill_status == true  && logo_image_check_validation == true && banner_image_check_validation == true && mobile_banner_image_check_validation == true && res_latitude !="" && res_longtitude !="" && res_break_start_fill_status == true && res_break_end_fill_status == true){

          $(this).attr('disabled','disabled');
          //swal("Wait..", "Please wait  and Don't do any action while we are processing your request!");
          swal({
              title: 'Wait..',
              text: "Please wait  and Don't do any action while we are processing your request!",
              type: 'Wait',
              buttons: false,
              confirmButtonText: 'Yes, delete it!'
            });
          
           fd.append('user_id',user_id);
           fd.append('restaurant_name',res_name);

           fd.append('business_type',business_type);
           fd.append('food_type',food_type);

           fd.append('description',res_description);
           fd.append('postal_code',postal_code);

           fd.append('pin_address',pin_address);
           fd.append('res_latitude',res_latitude);
           fd.append('res_longtitude',res_longtitude);

           fd.append('unit_number',unit_number);
           fd.append('rest_accept_type',selected_rest_accept_type);// order now, self pick up, order for later or dine in
           fd.append('delivery_handled_by',delivery_handled_by);//  1 - restaurant 2 - By Kerala Eats
           
            fd.append('open_time',open_time);
            fd.append('close_time',close_time);
             fd.append('break_start_time',break_start_time);
            fd.append('break_end_time',break_end_time);

           if(mode_type == 1){
             mode_url = 'add_restaurant_controller/';//add mode
           }else if(edit_restaurant_id != "" &&  mode_type == 2 ){ 

             mode_url = 'edit_restaurant_controller/';//edit mode
              restaurant_id = edit_restaurant_id;
               //edit id
              fd.append('restaurant_id',restaurant_id);
           }else{
              restaurant_id = "";
           }
           //controller is spepreate beacause there will availabel so many if condtion they may create confusion or issues......

           $.ajax({
              url:  BASE_URL+'admin/'+mode_url,
              type: 'post',
              data: fd,
              contentType: false,
              processData: false,
              success: function(response){
                
                 if(response == 1 && mode_type == 1){
                     //swal('Success', 'Restaurant added Successfully', 'success');
                      window.location.replace(BASE_URL+'admin/restaurant_list');
                 }

                 if(response == 1 && mode_type == 2){
                    // swal('Success', 'Restaurant Updated Successfully', 'success');
                     window.location.replace(BASE_URL+'admin/add_edit_restaurant/2/'+edit_restaurant_id+'/'+merchant_id);
                 }

                 if(response == 2 && mode_type == 2){
                   swal('Dont Worry...', 'Nothing Changed!');
                 }

                 if(response == 4 && response == 2){
                   swal('Oops...', 'Internal server error', 'error');
                 }

                 if(response == 5){
                    swal('Sorry...', 'This merchant already has a restaurant!', 'error');
                 }

                 if(response == 3){
                  swal('Oops...', 'Something Went wrong!', 'error');
                 }
              },
           });//ajax end
           $(this).removeAttr('disabled');
        }else{
        //swal('Oops...', 'You are missing a required field ', 'error');
        
        }//parrent if for all input fill fillable check
  });
//Add and edit resturant data submit -----------------END-----------------


});//on load end

//Add resturant data submit ---END----

//Enable /Disable(active/inactive) toggle of Restaurant------ START------
 
  $('body').on('change', '.res_status', function() {

      var restaurant_input_id  = $(this).attr('id');//input id

      var restaurant_input_id_array = restaurant_input_id.split("_");
      var restaurant_id = restaurant_input_id_array[2];

      var restaurant_name_id = '#res_name_'+restaurant_id;
      var restaurant_name = $(restaurant_name_id).text();

      var checked = $(this).attr('checked');

      var new_res_table_url = res_table_url.replace("table","1");// if action mode enable disable then it will repalce table to  1 same as  2 in delete case
      
      var active_status = 1;
      var msg_status;

      if (typeof checked !== 'undefined' && checked !== false) {
          //checked attribute exist means --- restaurant is already active
          active_status = 2; // 2 - disable in database

      }else{
           //checked attribute not exist means --- restaurant is not active
          active_status = 1; // 1 - enable in database
         
      }

     
      // Ajax-------SATRT------------
        $.ajax({
                url: res_active_inactive_url,
                data: { 
                    restaurant_id: restaurant_id, enable_disable_status:active_status
                },
                type: 'post',
                success: function(response){
                   if(response == 1){
                      if(active_status == 1){
                        // successfully actived
                         $('#'+restaurant_input_id).attr('checked','checked');
                      }
                      if(active_status == 2){
                         $('#'+restaurant_input_id).removeAttr('checked','checked');
                         // successfully inactived
                      }
                       $( "#res_table" ).load(new_res_table_url);
                     }

                   if(response == 0){
                      swal('Oops...', 'Something went wrong!', 'error');
                   }
                },
                
            });
    // Ajax-------END------------
    
});
//Enable /Disable (active/inactive)toggle of Restaurant------ END------


//Delete Restaurant -----------------------START---------------------

$('body').on('click', '.res_delete', function() {

     $('#action_mode').val('delete');

     var new_res_table_url = res_table_url.replace("table","2");// if action mode then it will repalce table to  2 same as  1 in enable disable  case


     var restaurant_input_id  = $(this).attr('id');//input id

      var restaurant_input_id_array = restaurant_input_id.split("_");
      var restaurant_id = restaurant_input_id_array[2];

      var restaurant_name_id = '#res_name_'+restaurant_id;
      var restaurant_name = $(restaurant_name_id).text();

         swal({
          title: "Are you sure to delete this Restaurant permanently?",
          text: "Once deleted, You will not be able to recover the action!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
              if (willDelete) {
                
                    // Ajax-------SATRT------------
                        $.ajax({
                        url: res_delete_url,
                        data: { 
                            restaurant_id: restaurant_id
                        },
                        type: 'post',
                        success: function(response){
                           if(response == 1){
                             
                            $('#res_row_'+restaurant_id).attr('checked','checked');

                             swal(" Restaurant ("+restaurant_name+") has been successfully Delete!", {
                                icon: "success",
                              });

                             setTimeout(function(){
                                  $( "#res_table" ).load(new_res_table_url);
                                }, 2000); //refresh every 2 seconds
                             }
                            setTimeout(function(){
                               $( ".closebtn" ).trigger('click');
                               
                            }, 3000); //refresh every 3 seconds

                           if(response == 0){
                              swal('Oops...', 'Something went wrong!', 'error');
                           }
                        },
                        
                    });
      
                  // Ajax-------END------------
              }
            });
});

//Delete Restaurant -----------------------END----------------------

//Show Restaurant ---------Update Commission of restaruant -------START-----
 $('body').on('click', '#rest_commission_submit', function() {
     var rest_commission_type = $('input[name="rest_commission_type"]:checked').val();
     var  rest_commission_value = $('input[name="rest_commission_value"]').val();
     var  selected_restaurant_id = $('input[name="restaurant_id"]').val();
       
        if(rest_commission_type == "" || rest_commission_type == 0 || rest_commission_type == undefined){
            $('#unfill_rest_commission_type').text('Please Select Commission type');     
        }else{
            $('#unfill_rest_commission_type').text('');
        }

        if((rest_commission_value == "" || rest_commission_value == 0)&& (rest_commission_type != "" || rest_commission_type != 0)){
            $('input[name="rest_commission_value"]').css('border-color','red');
            $('#unfill_rest_commission_value').text('Please Fill Commission value');
        }else{
            $('input[name="rest_commission_value"]').css('border-color','#ccc');
            $('#unfill_rest_commission_value').text('');
        }
       
      if(rest_commission_type != "" && rest_commission_type != 0 && rest_commission_value !="" && rest_commission_value != 0){
            // Ajax-------SATRT------------
                $.ajax({
                url: BASE_URL+'admin/update_restaurant_commission',
                data: { 
                    rest_commission_type: rest_commission_type,rest_commission_value:rest_commission_value,selected_restaurant_id:selected_restaurant_id
                },
                type: 'post',
                success: function(response){
                   if(response == 1){
                     /* swal("Commission Updated Successfully!", {
                        icon: "success",
                      });*/
                     }
                   if(response == 0){
                      swal('Oops...', 'Something went wrong!', 'error');
                   }
                },
                
            });
          // Ajax-------END------------
      }
 });
//Show Restaurant --------Update Commission of restaruant --------END-----

//Show Restaurant --------Change Offline/Online Status of Restaurant--------START-----

//go online --------------------------START------------------
  $('body').on('click', '#rest_going_online', function() {
      var  selected_restaurant_id = $('input[name="restaurant_id"]').val();
       var offline_tag = 0;
            var offline_type = $(this).val();// it should be 0
             if(selected_restaurant_id != 0 && offline_type != ""){
               $.ajax({
                    url: BASE_URL+'admin/update_rest_online',
                    data: { 
                           
                        selected_restaurant_id:selected_restaurant_id,
                        offline_type:offline_type
                    },
                    type: 'post',
                    success: function(response){
                       if(response == 1){
                              $('#rest_going_online').attr('data-target','#offline_online_restaurant_popup'); 
                              $('#rest_going_online').attr('data-toggle','modal'); 
                              $('#rest_going_online').val(1); 
                              $('#rest_going_online').prop('checked',true); 
                          
                           //$('#offline_online_restaurant_popup').modal('hide');
                         }
                         if(response == 0 && response !=""){
                           swal('Oops...', 'Internal server error', 'error');
                         } // update issue
                    },
                });
          
         }else {
              //missing value
             $('#rest_going_online').prop('checked',false); 
         }
  });
//go online --------------------------END------------------
//go offline --------------------------START------------------
  $('body').on('click', '#rest_online_offline_save', function() {
         var  selected_restaurant_id = $('input[name="restaurant_id"]').val();
       
            var offline_tag =  $('#offline_tag').val()
            var offline_type =  $('#offline_type').val();;

                  // geting final offline value ------
            if(offline_tag == 1){
               var offline_tag_value = $('#hours_offline_value').val();

                //required fields
                if(offline_tag_value == ""){
                   $('#hours_offline_value').css('border-color','red');
                   $('#unfill_hours_offline_value').text('Please Select Time');
                }else{
                   $('#hours_offline_value').css('border-color','#ccc');
                   $('#unfill_hours_offline_value').text('');
                }

            }else  if(offline_tag == 2){

               var days_offline_fromdate = $('#days_offline_fromdate').val();
              
               var offline_tag_value = days_offline_fromdate;

              //required fields
               if(offline_tag_value == ""){
                   $('#days_offline_fromdate').css('border-color','red');
                   $('#unfill_days_offline_fromdate').text('Please Select Day');
                }else{
                   $('#days_offline_fromdate').css('border-color','#ccc');
                   $('#unfill_days_offline_fromdate').text('');
                }

            }else  if(offline_tag == 3){

               //from date and time
               var offline_rest_fromdate = $('#offline_rest_fromdate').val();
              //till date and time
               var offline_rest_tilldate = $('#offline_rest_tilldate').val();

                //required fields--------------start-----------
                 if(offline_rest_fromdate == ""){
                     $('#offline_rest_fromdate').css('border-color','red');
                     $('#unfill_offline_rest_fromdate').text('Please Select Day');
                  }else{
                     $('#offline_rest_fromdate').css('border-color','#ccc');
                     $('#unfill_offline_rest_fromdate').text('');
                  }

                 if(offline_rest_tilldate == ""){
                     $('#offline_rest_tilldate').css('border-color','red');
                     $('#unfill_offline_rest_tilldate').text('Please Select Day');
                  }else{
                     $('#offline_rest_tilldate').css('border-color','#ccc');
                     $('#unfill_offline_rest_tilldate').text('');
                  }
                   //required fields--------------end-----------

                  if(offline_rest_fromdate != "" && offline_rest_tilldate != ""){
                      var offline_tag_value = offline_rest_fromdate+','+offline_rest_tilldate;
                  }else{
                      var offline_tag_value = "";
                  }
            }
           
             if(selected_restaurant_id != "" && offline_type != "" && offline_tag !="" && offline_tag_value !="" ){
                   $.ajax({
                        url: BASE_URL+'admin/update_rest_offline',
                        data: { 
                            offline_tag:offline_tag,
                            offline_value:offline_tag_value, 
                            selected_restaurant_id:selected_restaurant_id,
                            offline_type:offline_type
                        },
                        type: 'post',
                        success: function(response){
                           if(response == 1){
                                 $('#rest_going_online').prop('checked',false); 
                                     $('#rest_going_online').val(0); 
                                     $('#rest_going_online').removeAttr('data-target'); 
                                     $('#offline_online_restaurant_popup').modal('hide'); 
                                     $('#offline_online_restaurant_popup').find("input,select").val('').end();
                                    $('.offline_hour_btn_clr').trigger('click');
                                
                             }
                            if(response == 0){
                               swal('Oops...', 'Internal server error', 'error');
                             } // update issue
                        },
                    });
                 }else {
                    //missing value
                   $('#rest_going_online').prop('checked',true); 
                 }
  });
//go offline --------------------------END------------------
 //if click on close button then toggle should be show enable
 $('body').on('click', '.close_res_offline_modal', function() {
     $('#rest_going_online').prop('checked',true); 
     $('.offline_hour_btn_clr').trigger('click');
     $('#offline_online_restaurant_popup').modal('hide'); 
 });
//Show Restaurant --------Change Offline/Online Status of Restaurant--------END-----


//Show Restaurant Update open close and break time------------------------START-------------
 $('body').on('change', '.rest_time_mode', function() {
        var rest_time_mode = $('.rest_time_mode:checked').val();// 1 every day, 2 - specific day
       if(rest_time_mode == 2){
            $('#show_speecific_day_mode').removeClass('d-none');
            $('#show_every_day_mode').addClass('d-none');
       }else{
            $('#show_every_day_mode').removeClass('d-none');
            $('#show_speecific_day_mode').addClass('d-none');
       }
 });

 $('body').on('click', '.break_time', function() {
        var break_time_day = $(this).attr('id');
        var break_time_mode = $(this).attr('mode');//  2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)
        
        if(break_time_mode == '2'){//break time want to add
             switch(true) {
                  case break_time_day == 'mon':
                       $('#mon_break_tr').removeClass('d-none');
                       $(this).html('Yes <i class="fas fa-caret-up"></i>');
                       $(this).attr('mode','1');
                       $(this).removeClass('btn-primary');
                       $(this).addClass('btn-success');
                    break;
                  case break_time_day == 'tue':
                      $('#tue_break_tr').removeClass('d-none');
                      $(this).html('Yes <i class="fas fa-caret-up"></i>');
                       $(this).attr('mode','1');
                       $(this).removeClass('btn-primary');
                       $(this).addClass('btn-success');
                    break;
                     case break_time_day == 'wed':
                        $('#wed_break_tr').removeClass('d-none');
                          $(this).html('Yes <i class="fas fa-caret-up"></i>');
                       $(this).attr('mode','1');
                       $(this).removeClass('btn-primary');
                       $(this).addClass('btn-success');
                    break;
                     case break_time_day == 'thu':
                        $('#thu_break_tr').removeClass('d-none');
                          $(this).html('Yes <i class="fas fa-caret-up"></i>');;
                      $(this).attr('mode','1');
                       $(this).removeClass('btn-primary');
                       $(this).addClass('btn-success');
                    break;
                     case break_time_day == 'fri':
                         $('#fri_break_tr').removeClass('d-none');
                         $(this).html('Yes <i class="fas fa-caret-up"></i>');
                        $(this).attr('mode','1');
                       $(this).removeClass('btn-primary');
                       $(this).addClass('btn-success');
                    break;
                     case break_time_day == 'sat':
                        $('#sat_break_tr').removeClass('d-none');
                         $(this).html('Yes <i class="fas fa-caret-up"></i>');
                       $(this).attr('mode','1');
                       $(this).removeClass('btn-primary');
                       $(this).addClass('btn-success');
                    break;
                     case break_time_day == 'sun':
                     $('#sun_break_tr').removeClass('d-none');
                       $(this).html('Yes <i class="fas fa-caret-up"></i>');
                       $(this).attr('mode','1');
                       $(this).removeClass('btn-primary');
                       $(this).addClass('btn-success');
                }
        }else{//break time dont  want to add
             switch(true) {
                  case break_time_day == 'mon':
                       $('#mon_break_tr').addClass('d-none');
                       $(this).html('No <i class="fas fa-caret-down"></i>');
                      $(this).attr('mode','2');
                       $(this).addClass('btn-primary');
                       $(this).removeClass('btn-success');
                    break;
                  case break_time_day == 'tue':
                      $('#tue_break_tr').addClass('d-none');
                         $(this).html('No <i class="fas fa-caret-down"></i>');
                      $(this).attr('mode','2');
                       $(this).addClass('btn-primary');
                       $(this).removeClass('btn-success');
                    break;
                     case break_time_day == 'wed':
                        $('#wed_break_tr').addClass('d-none');
                         $(this).html('No <i class="fas fa-caret-down"></i>');
                      $(this).attr('mode','2');
                       $(this).addClass('btn-primary');
                       $(this).removeClass('btn-success');
                    break;
                     case break_time_day == 'thu':
                        $('#thu_break_tr').addClass('d-none');
                         $(this).html('No <i class="fas fa-caret-down"></i>');
                      $(this).attr('mode','2');
                       $(this).addClass('btn-primary');
                       $(this).removeClass('btn-success');
                    break;
                     case break_time_day == 'fri':
                         $('#fri_break_tr').addClass('d-none');
                          $(this).html('No <i class="fas fa-caret-down"></i>');
                      $(this).attr('mode','2');
                       $(this).addClass('btn-primary');
                       $(this).removeClass('btn-success');
                    break;
                     case break_time_day == 'sat':
                        $('#sat_break_tr').addClass('d-none');
                         $(this).html('No <i class="fas fa-caret-down"></i>');
                      $(this).attr('mode','2');
                       $(this).addClass('btn-primary');
                       $(this).removeClass('btn-success');
                    break;
                     case break_time_day == 'sun':
                     $('#sun_break_tr').addClass('d-none');
                       $(this).html('No <i class="fas fa-caret-down"></i>');
                      $(this).attr('mode','2');
                       $(this).addClass('btn-primary');
                       $(this).removeClass('btn-success');
                }
        }
 });

//start----------
 // when check on close day checkboax then value will be for close day that will be 2 other wise for open day it will be 1 or 0
 //check monday day is close day
  $('body').on('click', '#mon_close_status', function() {
    if($(this).prop("checked") == true){
         $('#mon_close_status').val('2'); 

        //remove mendatory
        // remove open -close  time
        $('#mon_open_time').val('');
        $('#mon_open_time').css('border-color','#ccc');
        $('#unfill_mon_open_time').text('');

        $('#mon_close_time').val('');
        $('#mon_close_time').css('border-color','#ccc');
        $('#unfill_mon_close_time').text('');

        // remove break start and close time
        $('#mon_break_start_time').val('');
        $('#mon_break_start_time').css('border-color','#ccc');
        $('#unfill_mon_break_start_time').text('');

        $('#mon_break_end_time').val('');
        $('#mon_break_end_time').css('border-color','#ccc');
        $('#unfill_mon_break_end_time').text('');

        //break status will be none
        $('#mon_break_tr').addClass('d-none');
        $('#mon').html('No <i class="fas fa-caret-down"></i>');
        $('#mon').attr('mode','2');
        $('#mon').addClass('btn-primary');
        $('#mon').removeClass('btn-success');

        $('#mon').attr('disabled','');

     }else{
         $('#mon_close_status').val('1'); 
        $('#mon').removeAttr('disabled');
     }
  });
  //check tuesday day is close day
  $('body').on('click', '#tue_close_status', function() {
    if($(this).prop("checked") == true){
         $('#tue_close_status').val('2'); 
           //remove mendatory
            // remove open -close  time
        $('#tue_open_time').val('');
        $('#tue_open_time').css('border-color','#ccc');
        $('#unfill_tue_open_time').text('');

         $('#tue_close_time').val('');
        $('#tue_close_time').css('border-color','#ccc');
        $('#unfill_tue_close_time').text('');

        // remove break start and close time
        $('#tue_break_start_time').val('');
        $('#tue_break_start_time').css('border-color','#ccc');
        $('#unfill_tue_break_start_time').text('');

        $('#tue_break_end_time').val('');
        $('#tue_break_end_time').css('border-color','#ccc');
        $('#unfill_tue_break_end_time').text('');

         //break status will be none
        $('#tue_break_tr').addClass('d-none');
        $('#tue').html('No <i class="fas fa-caret-down"></i>');
        $('#tue').attr('mode','2');
        $('#tue').addClass('btn-primary');
        $('#tue').removeClass('btn-success');

         $('#tue').attr('disabled','');
     }else{
         $('#tue_close_status').val('1'); 
         $('#tue').removeAttr('disabled'); 
     }
  });
  //check wednesday day is close day
  $('body').on('click', '#wed_close_status', function() {
    if($(this).prop("checked") == true){
         $('#wed_close_status').val('2'); 
          //remove mendatory
           // remove open -close  time
        $('#wed_open_time').val('');
        $('#wed_open_time').css('border-color','#ccc');
        $('#unfill_wed_open_time').text('');

        $('#wed_close_time').val('');
        $('#wed_close_time').css('border-color','#ccc');
        $('#unfill_wed_close_time').text('');

        // remove break start and close time
        $('#wed_break_start_time').val('');
        $('#wed_break_start_time').css('border-color','#ccc');
        $('#unfill_wed_break_start_time').text('');

        $('#wed_break_end_time').val('');
        $('#wed_break_end_time').css('border-color','#ccc');
        $('#unfill_wed_break_end_time').text('');

         //break status will be none
        $('#wed_break_tr').addClass('d-none');
        $('#wed').html('No <i class="fas fa-caret-down"></i>');
        $('#wed').attr('mode','2');
        $('#wed').addClass('btn-primary');
        $('#wed').removeClass('btn-success');

        $('#wed').attr('disabled','');

        
     }else{
         $('#wed_close_status').val('1'); 
         $('#wed').removeAttr('disabled'); 
     }
  });
  //check thursday day is close day
  $('body').on('click', '#thu_close_status', function() {
    if($(this).prop("checked") == true){
         $('#thu_close_status').val('2'); 
         //remove mendatory
          // remove open -close  time
         $('#thu_open_time').val('');
        $('#thu_open_time').css('border-color','#ccc');
        $('#unfill_thu_open_time').text('');

        $('#thu_close_time').val('');
        $('#thu_close_time').css('border-color','#ccc');
        $('#unfill_thu_close_time').text('');


        // remove break start and close time
        $('#thu_break_start_time').val('');
        $('#thu_break_start_time').css('border-color','#ccc');
        $('#unfill_thu_break_start_time').text('');

        $('#thu_break_end_time').val('');
        $('#thu_break_end_time').css('border-color','#ccc');
        $('#unfill_thu_break_end_time').text('');

         //break status will be none
        $('#thu_break_tr').addClass('d-none');
        $('#thu').html('No <i class="fas fa-caret-down"></i>');
        $('#thu').attr('mode','2');
        $('#thu').addClass('btn-primary');
        $('#thu').removeClass('btn-success');

        $('#thu').attr('disabled','');

        
     }else{
         $('#thu_close_status').val('1');
         $('#thu').removeAttr('disabled'); 
     }
  });
  //check firday day is close day
  $('body').on('click', '#fri_close_status', function() {
    if($(this).prop("checked") == true){
         $('#fri_close_status').val('2'); 
         //remove mendatory
          // remove open -close  time
        $('#fri_open_time').val('');
        $('#fri_open_time').css('border-color','#ccc');
        $('#unfill_fri_open_time').text('');

        $('#fri_close_time').val('');
         $('#fri_close_time').css('border-color','#ccc');
        $('#unfill_fri_close_time').text('');

          // remove break start and close time
        $('#fri_break_start_time').val('');
        $('#fri_break_start_time').css('border-color','#ccc');
        $('#unfill_fri_break_start_time').text('');

        $('#fri_break_end_time').val('');
        $('#fri_break_end_time').css('border-color','#ccc');
        $('#unfill_fri_break_end_time').text('');

         //break status will be none
        $('#fri_break_tr').addClass('d-none');
        $('#fri').html('No <i class="fas fa-caret-down"></i>');
        $('#fri').attr('mode','2');
        $('#fri').addClass('btn-primary');
        $('#fri').removeClass('btn-success');

        $('#fri').attr('disabled','');

        
     }else{
         $('#fri_close_status').val('1'); 
         $('#fri').removeAttr('disabled');
     }
  });
  //check saturday day is close day
  $('body').on('click', '#sat_close_status', function() {
    if($(this).prop("checked") == true){
         $('#sat_close_status').val('2'); 
        //remove mendatory
         // remove open -close  time
        $('#sat_open_time').val('');
        $('#sat_open_time').css('border-color','#ccc');
        $('#unfill_sat_open_time').text('');

         $('#sat_close_time').val('');
         $('#sat_close_time').css('border-color','#ccc');
        $('#unfill_sat_close_time').text('');

        // remove break start and close time
        $('#sat_break_start_time').val('');
        $('#sat_break_start_time').css('border-color','#ccc');
        $('#unfill_sat_break_start_time').text('');

        $('#sat_break_end_time').val('');
        $('#sat_break_end_time').css('border-color','#ccc');
        $('#unfill_sat_break_end_time').text('');

         //break status will be none
        $('#sat_break_tr').addClass('d-none');
        $('#sat').html('No <i class="fas fa-caret-down"></i>');
        $('#sat').attr('mode','2');
        $('#sat').addClass('btn-primary');
        $('#sat').removeClass('btn-success');

        $('#sat').attr('disabled','');
        
     }else{
         $('#sat_close_status').val('1'); 
        $('#sat').removeAttr('disabled');
     }
  });
  //check sunday day is close day
  $('body').on('click', '#sun_close_status', function() {
    if($(this).prop("checked") == true){
         $('#sun_close_status').val('2'); 
         //remove mendatory
          // remove open -close  time
         $('#sun_open_time').val('');
         $('#sun_open_time').css('border-color','#ccc');
        $('#unfill_sun_open_time').text('');

         $('#sun_close_time').val('');
         $('#sun_close_time').css('border-color','#ccc');
        $('#unfill_sun_close_time').text('');


        // remove break start and close time
        $('#sun_break_start_time').val('');
        $('#sun_break_start_time').css('border-color','#ccc');
        $('#unfill_sun_break_start_time').text('');

        $('#sun_break_end_time').val('');
        $('#sun_break_end_time').css('border-color','#ccc');
        $('#unfill_sun_break_end_time').text('');

         //break status will be none
        $('#sun_break_tr').addClass('d-none');
        $('#sun').html('No <i class="fas fa-caret-down"></i>');
        $('#sun').attr('mode','2');
        $('#sun').addClass('btn-primary');
        $('#sun').removeClass('btn-success');

        $('#sun').attr('disabled','');

        
     }else{
         $('#sun_close_status').val('1'); 
          $('#sun').removeAttr('disabled');
     }
  });
//////close------




//check open  close time validation------------------------START---------------

   var edit_open_close_time_validate_status = true;
   $('body').on('change', '#edit_open_time', function() {
    //Open time less then close time
    // close time greater then open time
      var open_time = $(this).val();
      var close_time = $('#edit_close_time').val();

       if(open_time>close_time){

            edit_open_close_time_validate_status = false;
            $('#edit_open_time').css('border-color','red');
            $('#unfill_edit_open_time').text('Open time should be smaller');
        }else{
           edit_open_close_time_validate_status = true;
           $('#edit_close_time').css('border-color','#ccc');
           $('#unfill_edit_close_time').text('');

           $('#edit_open_time').css('border-color','#ccc');
           $('#unfill_edit_open_time').text('');
        }
   });
   $('body').on('change', '#edit_close_time', function() {
    //Open time less then close time
    // close time greater then open time
      var close_time = $(this).val();
      var open_time = $('#edit_open_time').val();


       if(close_time<open_time){
           edit_open_close_time_validate_status = false;
           $('#edit_close_time').css('border-color','red');
            $('#unfill_edit_close_time').text('Close time should be Greater');
        }else{
            edit_open_close_time_validate_status = true;
           $('#edit_open_time').css('border-color','#ccc');
           $('#unfill_edit_open_time').text('');

           $('#edit_close_time').css('border-color','#ccc');
           $('#unfill_edit_close_time').text('');
        }
   });
   //check open close validation------------------------END---------------

   //check break time validation---------EDIT TIME----------------START----------------
   //comman function for check edit break start time 
    function common_edit_break_start_time_check(break_start_time,open_time,break_end_time,close_time){
         if(break_start_time >= open_time && break_start_time <= close_time){
             // alert(' between');
             if(break_start_time>break_end_time){
                edit_break_time_validate_status  = false;
                $('#edit_break_start_time').css('border-color','red');
                $('#unfill_edit_break_start_time').text('Time should be smaller');
             }else{
                edit_break_time_validate_status  = true;
                $('#edit_break_start_time').css('border-color','ccc');
                $('#unfill_edit_break_start_time').text('');

                $('#edit_break_end_time').css('border-color','ccc');
                 $('#unfill_edit_break_end_time').text('');
             }
           }else{
             // alert('not between');
              edit_break_time_validate_status  = false;
             $('#edit_break_start_time').css('border-color','red');
             $('#unfill_edit_break_start_time').text('Time should be between open and close time');
           }

        return edit_break_time_validate_status;
    }
   var edit_break_time_validate_status = true;
    $('body').on('change', '#edit_break_start_time', function() {
   
      var break_start_time = $(this).val();

      var open_time = $('#edit_open_time').val();
      var close_time = $('#edit_close_time').val();

      var break_end_time = $('#edit_break_end_time').val();

     common_edit_break_start_time_check(break_start_time,open_time,break_end_time,close_time)
   });


// edit break time function for check vaidaltion
function common_edit_break_end_time_check(break_end_time,open_time,break_start_time,close_time){
     if(break_end_time >= open_time && break_end_time <= close_time){
        //alert('between');
        
         if(break_end_time<break_start_time){
             edit_break_time_validate_status  = false;
             $('#edit_break_end_time').css('border-color','red');
             $('#unfill_edit_break_end_time').text('Time should be Greater');
         }else{
             edit_break_time_validate_status  = true;
             edit_break_time_validate_status  = true;
            $('#edit_break_start_time').css('border-color','ccc');
            $('#unfill_edit_break_start_time').text('');

             $('#edit_break_end_time').css('border-color','ccc');
             $('#unfill_edit_break_end_time').text('');
         }
       }else{
        //alert('not between');
         edit_break_time_validate_status  = false;
         $('#edit_break_end_time').css('border-color','red');
         $('#unfill_edit_break_end_time').text('Time should be between open and close time');
       }

       return edit_break_time_validate_status;
}
  $('body').on('change', '#edit_break_end_time', function() {
   
      var break_end_time = $(this).val();

      var open_time = $('#edit_open_time').val();
      var close_time = $('#edit_close_time').val();

      var break_start_time = $('#edit_break_start_time').val();

      common_edit_break_end_time_check(break_end_time,open_time,break_start_time,close_time);
   });
   //check break time validation-------------------------END----------------

 //update time------
  $('body').on('click', '#rest_open_close_time_break_submit', function() {

     var rest_time_mode = $('.rest_time_mode:checked').val();// 1 every day, 2 - specific day
     var  merchant_id = $('input[name="admin_id"]').val();
      var  selected_restaurant_id = $('input[name="restaurant_id"]').val();

     var final_filled_status = [];

     if(rest_time_mode == 2){// 1 every day, 2 - specific day
            // Day wise open - close time----------------START----------------
            //monday
             var mon_open_time = $('#mon_open_time').val();
             var mon_close_time = $('#mon_close_time').val();
            // alert(mon_open_time);
            if(mon_open_time == "" && $('#mon_close_status').prop("checked") == false){
                 $('#mon_open_time').css('border-color','red');
                 $('#unfill_mon_open_time').text('Please Select open time');
                  final_filled_status.push(false);
             }else{
                $('#mon_open_time').css('border-color','#ccc');
                $('#unfill_mon_open_time').text('');
             }

             if(mon_close_time == "" && $('#mon_close_status').prop("checked") == false){
                 $('#mon_close_time').css('border-color','red');
                 $('#unfill_mon_close_time').text('Please Select close time');
                  final_filled_status.push(false);
             }else{
                $('#mon_close_time').css('border-color','#ccc');
                $('#unfill_mon_close_time').text('');
                 final_filled_status.push(true);
             }

             if($('#mon_close_status').prop("checked") == false){
                var  mon_open_close_time = mon_open_time +'-'+ mon_close_time;
             }else{
                 var  mon_open_close_time = ""
             }

            

             //tuesday
             var tue_open_time = $('#tue_open_time').val();
             var tue_close_time = $('#tue_close_time').val();

            if(tue_open_time == "" && $('#tue_close_status').prop("checked") == false){
                 $('#tue_open_time').css('border-color','red');
                 $('#unfill_tue_open_time').text('Please Select open time');
                  final_filled_status.push(false);
             }else{
                $('#tue_open_time').css('border-color','#ccc');
                $('#unfill_tue_open_time').text('');
                 final_filled_status.push(true);
             }

             if(tue_close_time == "" && $('#tue_close_status').prop("checked") == false){
                 $('#tue_close_time').css('border-color','red');
                 $('#unfill_tue_close_time').text('Please Select close time');
                  final_filled_status.push(false);
             }else{
                $('#tue_close_time').css('border-color','#ccc');
                $('#unfill_tue_close_time').text('');
                 final_filled_status.push(true);
             }

            if($('#tue_close_status').prop("checked") == false){
                  var  tue_open_close_time = tue_open_time +'-'+ tue_close_time;
             }else{
                 var  tue_open_close_time = ""
             }

             //wednesday
             var wed_open_time = $('#wed_open_time').val();
             var wed_close_time = $('#wed_close_time').val();

             if(wed_open_time == "" && $('#wed_close_status').prop("checked") == false){
                 $('#wed_open_time').css('border-color','red');
                 $('#unfill_wed_open_time').text('Please Select open time');
                  final_filled_status.push(false);
             }else{
                $('#wed_open_time').css('border-color','#ccc');
                $('#unfill_wed_open_time').text('');
                 final_filled_status.push(true);
             }

             if(wed_close_time == "" && $('#wed_close_status').prop("checked") == false){
                 $('#wed_close_time').css('border-color','red');
                 $('#unfill_wed_close_time').text('Please Select close time');
                  final_filled_status.push(false);
             }else{
                $('#wed_close_time').css('border-color','#ccc');
                $('#unfill_wed_close_time').text('');
                 final_filled_status.push(true);
             }

            if($('#wed_close_status').prop("checked") == false){
                   var  wed_open_close_time = wed_open_time +'-'+ wed_close_time;
             }else{
                 var  wed_open_close_time = ""
             }
            

              //thursday
             var thu_open_time = $('#thu_open_time').val();
             var thu_close_time = $('#thu_close_time').val();

             if(thu_open_time == "" && $('#thu_close_status').prop("checked") == false){
                 $('#thu_open_time').css('border-color','red');
                 $('#unfill_thu_open_time').text('Please Select open time');
                  final_filled_status.push(false);
             }else{
                $('#thu_open_time').css('border-color','#ccc');
                $('#unfill_thu_open_time').text('');
                 final_filled_status.push(true);
             }

             if(thu_close_time == "" && $('#thu_close_status').prop("checked") == false){
                 $('#thu_close_time').css('border-color','red');
                 $('#unfill_thu_close_time').text('Please Select close time');
                  final_filled_status.push(false);
             }else{
                $('#thu_close_time').css('border-color','#ccc');
                $('#unfill_thu_close_time').text('');
                 final_filled_status.push(true);
             }

              if($('#thu_close_status').prop("checked") == false){
                  var  thu_open_close_time = thu_open_time +'-'+ thu_close_time;
             }else{
                 var  thu_open_close_time = ""
             }
           

            //firday
             var fri_open_time = $('#fri_open_time').val();
             var fri_close_time = $('#fri_close_time').val();

             if(fri_open_time == "" && $('#fri_close_status').prop("checked") == false){
                 $('#fri_open_time').css('border-color','red');
                 $('#unfill_fri_open_time').text('Please Select open time');
                  final_filled_status.push(false);
             }else{
                $('#fri_open_time').css('border-color','#ccc');
                $('#unfill_fri_open_time').text('');
                 final_filled_status.push(true);
             }

             if(fri_close_time == "" && $('#fri_close_status').prop("checked") == false){
                 $('#fri_close_time').css('border-color','red');
                 $('#unfill_fri_close_time').text('Please Select close time');
                  final_filled_status.push(false);
             }else{
                $('#fri_close_time').css('border-color','#ccc');
                $('#unfill_fri_close_time').text('');
                 final_filled_status.push(true);
             }

            if($('#fri_close_status').prop("checked") == false){
                   var  fri_open_close_time = fri_open_time +'-'+ fri_close_time;
             }else{
                 var  fri_open_close_time = ""
             }
           

             //saturday
             var sat_open_time = $('#sat_open_time').val();
             var sat_close_time = $('#sat_close_time').val();

             if(sat_open_time == "" && $('#sat_close_status').prop("checked") == false){
                 $('#sat_open_time').css('border-color','red');
                 $('#unfill_sat_open_time').text('Please Select open time');
                  final_filled_status.push(false);
             }else{
                $('#sat_open_time').css('border-color','#ccc');
                $('#unfill_sat_open_time').text('');
                 final_filled_status.push(true);
             }

             if(sat_close_time == ""  && $('#sat_close_status').prop("checked") == false){
                 $('#sat_close_time').css('border-color','red');
                 $('#unfill_sat_close_time').text('Please Select close time');
                  final_filled_status.push(false);
             }else{
                $('#sat_close_time').css('border-color','#ccc');
                $('#unfill_sat_close_time').text('');
                 final_filled_status.push(true);
             }

              if($('#sat_close_status').prop("checked") == false){
                 var  sat_open_close_time = sat_open_time +'-'+ sat_close_time;
             }else{
                 var  sat_open_close_time = ""
             }
             

              //sunday
             var sun_open_time = $('#sun_open_time').val();
             var sun_close_time = $('#sun_close_time').val();

              if(sun_open_time == ""  && $('#sun_close_status').prop("checked") == false){
                 $('#sun_open_time').css('border-color','red');
                 $('#unfill_sun_open_time').text('Please Select open time');
                  final_filled_status.push(false);
             }else{
                $('#sun_open_time').css('border-color','#ccc');
                $('#unfill_sun_open_time').text('');
                 final_filled_status.push(true);
             }

             if(sun_close_time == ""  && $('#sun_close_status').prop("checked") == false){
                 $('#sun_close_time').css('border-color','red');
                 $('#unfill_sun_close_time').text('Please Select close time');
                  final_filled_status.push(false);
             }else{
                $('#sun_close_time').css('border-color','#ccc');
                $('#unfill_sun_close_time').text('');
                 final_filled_status.push(true);
             }

              if($('#sun_close_status').prop("checked") == false){
                 var  sun_open_close_time = sun_open_time +'-'+ sun_close_time;
             }else{
                 var  sun_open_close_time = ""
             }
           
            //Day wise  open - close time----------------END----------------

            //Dady Wise Break Time------------------------START---------------------

            //monday
             var mon_break_start_time = $('#mon_break_start_time').val();
             var mon_break_end_time = $('#mon_break_end_time').val();
             var mon_break_time_mode =  $('#mon').attr('mode');//  2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)
            
                 if(mon_break_time_mode == 1){
                     if(mon_break_start_time == ""){
                         $('#mon_break_start_time').css('border-color','red');
                         $('#unfill_mon_break_start_time').text('Please Select Start time');
                          final_filled_status.push(false);
                     }else{
                        $('#mon_break_start_time').css('border-color','#ccc');
                        $('#unfill_mon_break_start_time').text('');
                         final_filled_status.push(true);
                     }

                     if(mon_break_end_time == ""){
                         $('#mon_break_end_time').css('border-color','red');
                         $('#unfill_mon_break_end_time').text('Please Select End time');
                          final_filled_status.push(false);
                     }else{
                        $('#mon_break_end_time').css('border-color','#ccc');
                        $('#unfill_mon_break_end_time').text('');
                         final_filled_status.push(true);
                     }

                  var  mon_break_start_end_time = mon_break_start_time +'-'+ mon_break_end_time;
               }else{
                   var  mon_break_start_end_time = "";
               }

            //tuesday
            var tue_break_start_time = $('#tue_break_start_time').val();
             var tue_break_end_time = $('#tue_break_end_time').val();
             var tue_break_time_mode =  $('#tue').attr('mode');//  2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)

                 if(tue_break_time_mode == 1){
                      if(tue_break_start_time == ""){
                         $('#tue_break_start_time').css('border-color','red');
                         $('#unfill_tue_break_start_time').text('Please Select Start time');
                          final_filled_status.push(false);
                     }else{
                        $('#tue_break_start_time').css('border-color','#ccc');
                        $('#unfill_tue_break_start_time').text('');
                         final_filled_status.push(true);
                     }

                     if(tue_break_end_time == ""){
                         $('#tue_break_end_time').css('border-color','red');
                         $('#unfill_tue_break_end_time').text('Please Select End time');
                          final_filled_status.push(false);
                     }else{
                        $('#tue_break_end_time').css('border-color','#ccc');
                        $('#unfill_tue_break_end_time').text('');
                         final_filled_status.push(true);
                     }
                   var  tue_break_start_end_time = tue_break_start_time +'-'+ tue_break_end_time;
               }else{
                     var  tue_break_start_end_time = "";
               }
         

            //wednesday
            var wed_break_start_time = $('#wed_break_start_time').val();
             var wed_break_end_time = $('#wed_break_end_time').val();
             var wed_break_time_mode =  $('#wed').attr('mode');//  2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)

                 if(wed_break_time_mode == 1){
                      if(wed_break_start_time == ""){
                         $('#wed_break_start_time').css('border-color','red');
                         $('#unfill_wed_break_start_time').text('Please Select Start time');
                          final_filled_status.push(false);
                     }else{
                        $('#wed_break_start_time').css('border-color','#ccc');
                        $('#unfill_wed_break_start_time').text('');
                         final_filled_status.push(true);
                     }

                     if(wed_break_end_time == ""){
                         $('#wed_break_end_time').css('border-color','red');
                         $('#unfill_wed_break_end_time').text('Please Select End time');
                          final_filled_status.push(false);
                     }else{
                        $('#wed_break_end_time').css('border-color','#ccc');
                        $('#unfill_wed_break_end_time').text('');
                         final_filled_status.push(true);
                     }
                var  wed_break_start_end_time = wed_break_start_time +'-'+ wed_break_end_time;
               }else{
                var  wed_break_start_end_time = "";
               }
             

            //Thursday
             var thu_break_start_time = $('#thu_break_start_time').val();
             var thu_break_end_time = $('#thu_break_end_time').val();
             var thu_break_time_mode =  $('#thu').attr('mode');//  2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)

                 if(thu_break_time_mode == 1){
                      if(thu_break_start_time == ""){
                         $('#thu_break_start_time').css('border-color','red');
                         $('#unfill_thu_break_start_time').text('Please Select Start time');
                          final_filled_status.push(false);
                     }else{
                        $('#thu_break_start_time').css('border-color','#ccc');
                        $('#unfill_thu_break_start_time').text('');
                         final_filled_status.push(true);
                     }

                     if(thu_break_end_time == ""){
                         $('#thu_break_end_time').css('border-color','red');
                         $('#unfill_thu_break_end_time').text('Please Select End time');
                          final_filled_status.push(false);
                     }else{
                        $('#thu_break_end_time').css('border-color','#ccc');
                        $('#unfill_thu_break_end_time').text('');
                         final_filled_status.push(true);
                     }
                 var   thu_break_start_end_time = thu_break_start_time +'-'+ thu_break_end_time;
               }else{
                  var   thu_break_start_end_time = "";
               }
            

             //Firday
             var fri_break_start_time = $('#fri_break_start_time').val();
             var fri_break_end_time = $('#fri_break_end_time').val();
             var fri_break_time_mode =  $('#fri').attr('mode');//  2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)

                 if(fri_break_time_mode == 1){
                      if(fri_break_start_time == ""){
                         $('#fri_break_start_time').css('border-color','red');
                         $('#unfill_fri_break_start_time').text('Please Select Start time');
                          final_filled_status.push(false);
                     }else{
                        $('#fri_break_start_time').css('border-color','#ccc');
                        $('#unfill_fri_break_start_time').text('');
                         final_filled_status.push(true);
                     }

                     if(fri_break_end_time == ""){
                         $('#fri_break_end_time').css('border-color','red');
                         $('#unfill_fri_break_end_time').text('Please Select End time');
                          final_filled_status.push(false);
                     }else{
                        $('#fri_break_end_time').css('border-color','#ccc');
                        $('#unfill_fri_break_end_time').text('');
                         final_filled_status.push(true);
                     }
                 var  fri_break_start_end_time = fri_break_start_time +'-'+  fri_break_end_time;
               }else{
                    var  fri_break_start_end_time = "";
               }
            

             //Saturday
            var sat_break_start_time = $('#sat_break_start_time').val();
             var sat_break_end_time = $('#sat_break_end_time').val();
             var sat_break_time_mode =  $('#sat').attr('mode');//  2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)
              
                 if(sat_break_time_mode == 1){
                      if(sat_break_start_time == ""){
                         $('#sat_break_start_time').css('border-color','red');
                         $('#unfill_sat_break_start_time').text('Please Select Start time');
                          final_filled_status.push(false);
                     }else{
                        $('#sat_break_start_time').css('border-color','#ccc');
                        $('#unfill_sat_break_start_time').text('');
                         final_filled_status.push(true);
                     }

                     if(sat_break_end_time == ""){
                         $('#sat_break_end_time').css('border-color','red');
                         $('#unfill_sat_break_end_time').text('Please Select End time');
                          final_filled_status.push(false);
                     }else{
                        $('#sat_break_end_time').css('border-color','#ccc');
                        $('#unfill_sat_break_end_time').text('');
                         final_filled_status.push(true);
                     }
                    var  sat_break_start_end_time = sat_break_start_time +'-'+  sat_break_end_time;
               }else{
                     var    sat_break_start_end_time = "";
               }
              

             //Suandy
             var sun_break_start_time = $('#sun_break_start_time').val();
             var sun_break_end_time = $('#sun_break_end_time').val();
             var sun_break_time_mode =  $('#sun').attr('mode');//  2- no(means admin dont want to add break time) , 1 yes(means admin want to add break time)

                 if(sun_break_time_mode == 1){
                      if(sun_break_start_time == ""){
                         $('#sun_break_start_time').css('border-color','red');
                         $('#unfill_sun_break_start_time').text('Please Select Start time');
                          final_filled_status.push(false);
                     }else{
                        $('#sun_break_start_time').css('border-color','#ccc');
                        $('#unfill_sun_break_start_time').text('');
                         final_filled_status.push(true);
                     }

                     if(sun_break_end_time == ""){
                         $('#sun_break_end_time').css('border-color','red');
                         $('#unfill_sun_break_end_time').text('Please Select End time');
                          final_filled_status.push(false);
                     }else{
                        $('#sun_break_end_time').css('border-color','#ccc');
                        $('#unfill_sun_break_end_time').text('');
                         final_filled_status.push(true);
                     }
                 var   sun_break_start_end_time = sun_break_start_time +'-'+  sun_break_end_time;
               }else{
                 var   sun_break_start_end_time = "";
               }
            
            //Dady Wise Break Time------------------------END---------------------

            //Close day ------------------------START-----------------------
             var mon_close_status = $('#mon_close_status').val();
             var tue_close_status = $('#tue_close_status').val();
             var wed_close_status = $('#wed_close_status').val();
             var thu_close_status = $('#thu_close_status').val();
             var fri_close_status = $('#fri_close_status').val();
             var sat_close_status = $('#sat_close_status').val();
             var sun_close_status = $('#sun_close_status').val();
            //Close day -------------------------END-----------------------
 
             var TimeData = {
                 //open -close time
                 mon_open_close_time:mon_open_close_time,
                 tue_open_close_time:tue_open_close_time,
                 wed_open_close_time:wed_open_close_time,
                 thu_open_close_time:thu_open_close_time,
                 fri_open_close_time:fri_open_close_time,
                 sat_open_close_time:sat_open_close_time,
                 sun_open_close_time:sun_open_close_time,

                 //break time
                mon_break_status:mon_break_time_mode,
                mon_break_start_end_time:mon_break_start_end_time,

                tue_break_status:tue_break_time_mode,
                tue_break_start_end_time:tue_break_start_end_time,

                wed_break_status:wed_break_time_mode,
                wed_break_start_end_time:wed_break_start_end_time,

                thu_break_status:thu_break_time_mode,
                thu_break_start_end_time:thu_break_start_end_time,

                fri_break_status:fri_break_time_mode,
                fri_break_start_end_time:fri_break_start_end_time,

                sat_break_status:sat_break_time_mode,
                sat_break_start_end_time:sat_break_start_end_time,

                sun_break_status:sun_break_time_mode,
                sun_break_start_end_time:sun_break_start_end_time,

                selected_restaurant_id:selected_restaurant_id,
                rest_time_mode:rest_time_mode,

                //restaruant close//off day
                mon_close_status:mon_close_status,
                tue_close_status:tue_close_status,
                wed_close_status:wed_close_status,
                thu_close_status:thu_close_status,
                fri_close_status:fri_close_status,
                sat_close_status:sat_close_status,
                sun_close_status:sun_close_status,


              };
 
     }else if(rest_time_mode == 1){// 1 every day, 2 - specific day

           // open -close time----------------START----------------
             var open_time = $('#edit_open_time').val();
             var close_time = $('#edit_close_time').val();

            
            if(open_time == ""){
                 $('#edit_open_time').css('border-color','red');
                 $('#unfill_edit_open_time').text('Please Select open time');
                  final_filled_status.push(false);
             }else if(edit_open_close_time_validate_status == true){
                 if(open_time == close_time){
                     $('#edit_open_time').css('border-color','red');
                     $('#unfill_edit_open_time').text('Open time and Close time should not be equal');
                     final_filled_status.push(false);
                 }else{
                     $('#edit_open_time').css('border-color','#ccc');
                    $('#unfill_edit_open_time').text('');
                    final_filled_status.push(true);
                     
                 }
             }


             if(close_time == ""){
                 $('#edit_close_time').css('border-color','red');
                 $('#unfill_edit_close_time').text('Please Select close time');
                  final_filled_status.push(false);
             }else if(edit_open_close_time_validate_status == true){
                 if(open_time == close_time){
                     $('#edit_close_time').css('border-color','red');
                     $('#unfill_edit_close_time').text('Open time and Close time should not be equal');
                     final_filled_status.push(false);
                 }else{
                     $('#edit_close_time').css('border-color','#ccc');
                    $('#unfill_edit_close_time').text('');
                     final_filled_status.push(true);
                 }
             }
             // open -close time----------------END----------------

             // Break  time----------------START----------------
              var break_start_time = $('#edit_break_start_time').val();
              var break_end_time = $('#edit_break_end_time').val();

              var  break_time_validate_status  = true;


             if(break_end_time != "" || break_end_time !=""){
                if(break_start_time == break_end_time ){
                   
                       break_time_validate_status = false;
                 }else if(edit_break_time_validate_status == true){
                       break_time_validate_status = true;
                      $('#edit_break_start_time').css('border-color','#ccc');
                      $('#unfill_edit_break_start_time').text('');
                 }

                  if(break_start_time!=""){
                   edit_open_close_time_validate_status =  common_edit_break_start_time_check(break_start_time,open_time,break_end_time,close_time)
                 }

                 /*if(break_end_time == ""){
                     $('#edit_break_end_time').css('border-color','red');
                     $('#unfill_edit_break_end_time').text('Please Select close time');
                         break_time_validate_status = false;
                 }else*/ 

                 

                 if(break_end_time == break_start_time){
                    $('#edit_break_end_time').css('border-color','red');
                    $('#unfill_edit_break_end_time').text('Start time and end time should not be equal');
                        break_time_validate_status = false;
                 }else if(edit_break_time_validate_status == true){
                    $('#edit_break_end_time').css('border-color','#ccc');
                    $('#unfill_edit_break_end_time').text('');
                       break_time_validate_status = true;
                 }


                 if(break_end_time!=""){
                    edit_open_close_time_validate_status =   common_edit_break_end_time_check(break_end_time,open_time,break_start_time,close_time);
                 }
                 //check break time is not equal , same like open and close time 
                 if(break_time_validate_status == true ){
                    if((open_time == break_start_time || close_time == break_start_time) || (open_time > break_start_time || close_time < break_end_time) ||  (close_time < break_start_time)){
                         $('#edit_break_start_time').css('border-color','red');
                         $('#unfill_edit_break_start_time').text('Time should be between open and close time');
                        edit_break_time_validate_status = false;
                    }else if(open_time == break_end_time || close_time == break_end_time){
                        $('#edit_break_end_time').css('border-color','red');
                        $('#unfill_edit_break_end_time').text('Time should be between open and close time');
                         edit_break_time_validate_status = false;
                    }else{
                         edit_break_time_validate_status = true;
                          $('#edit_break_start_time').css('border-color','#ccc');
                          $('#unfill_edit_break_start_time').text('');

                         $('#edit_break_end_time').css('border-color','#ccc');
                         $('#unfill_edit_break_end_time').text('');
                    }

                 }else{
                      edit_break_time_validate_status = false;
                      $('#edit_break_start_time').css('border-color','red');
                      $('#unfill_edit_break_start_time').text('Start time and end time should not be equal');

                      $('#edit_break_end_time').css('border-color','red');
                      $('#unfill_edit_break_end_time').text('Start time and end time should not be equal');
                 }
             }

             //if break time clear after filled value then we  have do empty exist value from the input
             if(break_start_time == ""){
                $('#edit_break_start_time').val('').prop('disabled', false);
             }
             if(break_end_time == ""){
                 $('#edit_break_end_time').val('').prop('disabled', false);
             }

             if(break_start_time == "" && break_end_time == ""){
                edit_break_time_validate_status = true;
             }

             // Break  time----------------END----------------
              var TimeData = {
                //open - close time
                 open_time:open_time,
                 close_time:close_time,
                 break_start_time:break_start_time,
                 break_end_time:break_end_time,
                 selected_restaurant_id:selected_restaurant_id,
                 rest_time_mode:rest_time_mode
              };
     }

    // break time --------------------START---------------------

    // break time --------------------END---------------------

    var check_empty_form_value = final_filled_status.includes(false);
 
    if(check_empty_form_value == false  && edit_open_close_time_validate_status == true && edit_break_time_validate_status == true){
        // Ajax-------SATRT------------
        $.ajax({
            url: BASE_URL+'admin/update_res_open_close_break_time',
            data: TimeData,
            type: 'post',
            success: function(response){
               if(response == 1){
                  // window.location.replace(BASE_URL+'admin/show_restaurant/'+selected_restaurant_id);
                   window.location.replace(BASE_URL+'admin/add_edit_restaurant/2/'+selected_restaurant_id+'/'+merchant_id);
                 }
               if(response == 0){
                  swal('Oops...', 'Something went wrong!', 'error');
               }
            },
            
        });
      // Ajax-------END------------
    }

  });
//Show Restaurant Update open close and break time------------------------END-------------

//Show Restaurant ---------Update Delivery Charge per km if hanlde by restaurant -------START-----
 $('body').on('click', '#rest_delivery_per_km_charge_submit', function() {

     var delivery_per_km_charge = $('input[name="delivery_per_km_charge"]').val();
     var  selected_restaurant_id = $('input[name="restaurant_id"]').val();

        if(delivery_per_km_charge == ""){
            $('input[name="delivery_per_km_charge"]').css('border-color','red');
            $('#unfill_delivery_charge').text('Please Fill Delivery Charge');
        }else{
            $('input[name="delivery_per_km_charge"]').css('border-color','#ccc');
            $('#unfill_delivery_charge').text('');
        }
       
      if(delivery_per_km_charge !="" ){//&& delivery_per_km_charge != 0
            // Ajax-------SATRT------------
                $.ajax({
                url: BASE_URL+'admin/update_rest_delivery_per_km_charge',
                data: { 
                    delivery_per_km_charge:delivery_per_km_charge,selected_restaurant_id:selected_restaurant_id
                },
                type: 'post',
                success: function(response){
                   if(response == 1){
                     /* swal("Delivery Charge Updated Successfully!", {
                        icon: "success",
                      });*/
                     }
                   if(response == 0){
                      swal('Oops...', 'Something went wrong!', 'error');
                   }
                },
                
            });
          // Ajax-------END------------
      }
 });
//Show Restaurant --------Update  Delivery Charge per km if hanlde by restaurant--------END-----

//Show Restaurant ---------Update Order Preparation Time  and delivery Time of restaruant -------START-----
 $('body').on('click', '#rest_order_prepration_and_delivery_time_submit', function() {

     var rest_order_preparation_time = $('input[name="rest_order_preparation_time"]').val();
     var rest_order_delivery_time = $('input[name="rest_order_delivery_time"]').val();
     var  selected_restaurant_id = $('input[name="restaurant_id"]').val();

        if(rest_order_preparation_time == "" || rest_order_preparation_time == 0){
            $('input[name="rest_order_preparation_time"]').css('border-color','red');
            $('#unfill_preparation_time').text('Please Fill preparation time');
        }else{
            $('input[name="rest_order_preparation_time"]').css('border-color','#ccc');
            $('#unfill_preparation_time').text('');
        }

        if(rest_order_delivery_time == "" || rest_order_delivery_time == 0){
            $('input[name="rest_order_delivery_time"]').css('border-color','red');
            $('#unfill_delivery_time').text('Please Fill Delivery time');
        }else{
            $('input[name="rest_order_delivery_time"]').css('border-color','#ccc');
            $('#unfill_delivery_time').text('');
        }
       
      if(rest_order_preparation_time !="" && rest_order_preparation_time != 0 && rest_order_delivery_time && rest_order_delivery_time != 0){
            // Ajax-------SATRT------------
                $.ajax({
                url: BASE_URL+'admin/update_order_preparation_and_delivery_time',
                data: { 
                    rest_order_preparation_time:rest_order_preparation_time,rest_order_delivery_time:rest_order_delivery_time,selected_restaurant_id:selected_restaurant_id
                },
                type: 'post',
                success: function(response){
                   if(response == 1){

                     /* swal("Order Preparation Time Updated Successfully!", {
                        icon: "success",
                      });*/
                     }
                   if(response == 0){
                      swal('Oops...', 'Something went wrong!', 'error');
                   }
                },
                
            });
          // Ajax-------END------------
      }
 });
//Show Restaurant --------Update Order Preparation Time  and delivery  Time of restaruant --------END-----

// Restaurant search ----------------------START------------------

    // if select business type food then foodtype will be visible other wise it will be disabled
      $('body').on('change', '#business_type', function() {

        //If restaurant's business type is food then this column will contain value. So value 0 (if not applicable) 1 (Restaurant) 2 (Kitchen)
            var business_type = $(this).val();
           
            if(business_type == 1){// 1 for food, 2 -grocery , 3 -alcohol 
                $('#food_type').removeAttr('disabled');
            }else{
                $('#food_type').attr('disabled','disabled');
                $('#food_type').val('');
            }
      });

$(".search_key").keydown(function(e){
      if(e.keyCode === 13)
      {
        $('#search_restaurant_list_data').trigger( "click");
      }
  });
 
  $('body').on('click', '#search_restaurant_list_data', function() {
      var from_date = $('#fromdate').val();
      var to_date = $('#todate').val();
      var resstatus = $('.resstatus').val();
      var resrating = $('.res_rating').val();
      var business_type = $('#business_type').val();
      var food_type = $('#food_type').val();
      var search =$('.search_key').val();
      
      if(from_date == '')
      {
         from_date= "all";
      }
      if(to_date==''){
         to_date= "all";
      }

      if(resstatus==''){
         resstatus= "all";
      }
      if(search == '')
      {
          search = "all";
      }else{

           search = search.trim();
        }
      
      if(resrating == '')
      {
          resrating = "all";
      }

      if(business_type == '')
      {
          business_type = "all";
      }


      if(food_type == '')
      {
          food_type = "all";
      }


    if(from_date!="all" || to_date!="all" || resstatus!="all" || search != "all" || resrating != "all" ||  business_type != "all" || food_type != "all"){
        
           // $( "#res_table" ).load(res_search_table_url+from_date+'/'+to_date+'/'+resstatus+'/'+search+'/'+resrating+'/');//load only table on a restaurant page 
             window.location.replace(res_search_table_url+from_date+'/'+to_date+'/'+resstatus+'/'+search+'/'+resrating+'/'+business_type+'/'+food_type+'/');//load only table on a restaurant page 
      }else{
            //$( "#res_table" ).load(res_search_table_url);
            window.location.replace(res_search_table_url);
      }
  });

// Restaurant search ----------------------END------------------

//Export Restaurant Csv -------START---------------
 $('body').on('click', '.export_restaurant_csv', function() {
  
        var from_date = $('#fromdate').val();
        var to_date = $('#todate').val();
        var resstatus = $('.resstatus').val();
        var resrating = $('.res_rating').val();

        var keyword =$('.search_key').val();

        if(from_date== '')
        {
           from_date="all";
        }
        if(to_date==''){
           to_date="all";
        }
        if(resstatus==''){
           resstatus="all";
        }
        if(resrating==''){
           resrating="all";
        }
         
        if(keyword==''){
           keyword="all";
        }else{
           keyword = keyword.trim();
        }

        if(from_date!="all" || to_date!="all" || resstatus!="all" || resrating!="all"|| keyword!="all"){
            window.location.replace(BASE_URL+'admin/exportRestaurantCSV/'+from_date+'/'+to_date+'/'+resstatus+'/'+resrating+'/'+keyword+'/');
        }else{
            window.location.replace(BASE_URL+'admin/exportRestaurantCSV/');
        }
    });
 
//Export Restaurant Csv -------END---------------

//Promo Code Search filter --------------------START----------------
$(".search_key").keydown(function(e){//on enter
      if(e.keyCode === 13)
      {
        $('#search_promo_code_list_data').trigger( "click");
      }
  });
$('body').on('click', '#search_promo_code_list_data', function() {
      var from_date = $('#fromdate').val();
      var to_date = $('#todate').val();
      var promo_code_status = $('.promo_code_status').val();
      var promo_code_type = $('.promo_code_type').val();
      var promo_code_mode = $('.promo_code_mode').val();
      var search =$('.search_key').val();
      
      if(from_date == '')
      {
         from_date= "all";
      }
      if(to_date==''){
         to_date= "all";
      }

      if(promo_code_status==''){
         promo_code_status= "all";
      }
      if(search == '')
      {
          search = "all";
      }else{
          
           search = search.trim();
        }
      
      if(promo_code_type == '')
      {
          promo_code_type = "all";
      }

     if(promo_code_mode == '')
      {
          promo_code_mode = "all";
      }

    if(from_date!="all" || to_date!="all" || promo_code_status!="all" || search != "all" || promo_code_type!= "all" || promo_code_mode != "all"){
        
           // $( "#promo_code_table" ).load(promo_code_search_table_url+from_date+'/'+to_date+'/'+promo_code_status+'/'+search+'/'+promo_code_rating+'/');//load only table on a promo_code_taurant page 
             window.location.replace(BASE_URL+'admin/promo_codes/0/'+from_date+'/'+to_date+'/'+promo_code_status+'/'+search+'/'+promo_code_type+'/'+promo_code_mode+'/');//load only table on a promo_code_taurant page 
      }else{
            //$( "#promo_code_table" ).load(promo_code_search_table_url);
            window.location.replace(BASE_URL+'admin/promo_codes/');
      }
  });
//Promo Code Search filter --------------------END--------0---------

//Promo Code Export CSV -----------------------START------------
 $('body').on('click', '.export_promo_code_csv', function() {

 var from_date = $('#fromdate').val();
      var to_date = $('#todate').val();
      var promo_code_status = $('.promo_code_status').val();
      var promo_code_type = $('.promo_code_type').val();
      var promo_code_mode = $('.promo_code_mode').val();
      var search =$('.search_key').val();
      
      if(from_date == '')
      {
         from_date= "all";
      }
      if(to_date==''){
         to_date= "all";
      }

      if(promo_code_status==''){
         promo_code_status= "all";
      }
      if(search == '')
      {
          search = "all";
      }else{

           search = keyword.trim();

           search = search.trim();

        }
      
      if(promo_code_type == '')
      {
          promo_code_type = "all";
      }

     if(promo_code_mode == '')
      {
          promo_code_mode = "all";
      }

    if(from_date!="all" || to_date!="all" || promo_code_status!="all" || search != "all" || promo_code_type!= "all" || promo_code_mode != "all"){
        
           // $( "#promo_code_table" ).load(promo_code_search_table_url+from_date+'/'+to_date+'/'+promo_code_status+'/'+search+'/'+promo_code_rating+'/');//load only table on a promo_code_taurant page 
             window.location.replace(BASE_URL+'admin/exportPromotionCSV/0/'+from_date+'/'+to_date+'/'+promo_code_status+'/'+search+'/'+promo_code_type+'/'+promo_code_mode+'/');//load only table on a promo_code_taurant page 
      }else{
            //$( "#promo_code_table" ).load(promo_code_search_table_url);
            window.location.replace(BASE_URL+'admin/exportPromotionCSV/');
      }
  
 });
//Promo Code Export CSV -----------------------END------------

//Enable /Disable(active/inactive) toggle of Promo Code------ START------
  $('body').on('change', '.promo_code_status', function() {

      var promo_code_input_id  = $(this).attr('id');
      var promo_code_name  = $(this).attr('promo_code_status');
      
      var checked = $(this).attr('checked');
      
      var new_promo_code_table_url = promo_code_table_url.replace("table","1");// if action mode enable disable then it will repalce table to  1 same as  2 in delete case
      
      var active_status = 1;
      var msg_status;

      if (typeof checked !== 'undefined' && checked !== false) {
          //checked attribute exist means --- Promo Code is already active
          active_status = 2; // 2 - disable in database

      }else{
           //checked attribute not exist means --- Promo Code is not active
          active_status = 1; // 1 - enable in database
      }
        // Ajax-------SATRT------------

          $.ajax({
              url: promo_code_active_inactive_url,
              data: { 
                  promo_code_name: promo_code_name, enable_disable_status:active_status
              },
              type: 'post',
              success: function(promo_code_ponse){
                 if(promo_code_ponse == 1){
                    
                    if(active_status == 1){

                       $('#'+promo_code_input_id).attr('checked','checked');
                       //successfully actived
                    }

                    if(active_status == 2){
                       $('#'+promo_code_input_id).removeAttr('checked','checked');
                        //successfully inactived
                    }

                    //alert(new_promo_code_table_url);

                   $( "#promo_code_table" ).load(new_promo_code_table_url);

                     setTimeout(function(){
                         $( ".closebtn" ).trigger('click');
                         
                      }, 3000); //refresh every 3 seconds
                   }

                 if(promo_code_ponse == 0){
                    swal('Oops...', 'Something went wrong!', 'error');
                 }
              },
          });
    // Ajax-------END------------
});
//Enable /Disable (active/inactive)toggle of Promo Code------ END------

//Delete Promo Code -----------------------START---------------------

$('body').on('click', '.promo_code_delete', function() {

     $('#action_mode').val('delete');

      var new_promo_code_table_url = promo_code_table_url.replace("table","2");// if action mode then it will repalce table to  2 same as  1 in enable disable  case

      var promo_code_name  = $(this).attr('promo_code_status');

      swal({
          title: "Are you sure to delete this Promo Code permanently?",
          text: "Once deleted, You will not be able to recover the action!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
              if (willDelete) {
                    // Ajax-------SATRT------------
                    
                      $.ajax({
                          url: promo_code_delete_url,
                          data: { 
                              promo_code_name: promo_code_name
                          },
                          type: 'post',
                          success: function(response){
                             if(response == 1){
                                 swal(" Promo Code  ("+promo_code_name+") has been successfully Delete!", {
                                icon: "success",
                              });

                               //console.log("new"+new_promo_code_table_url+'====old'+ promo_code_table_url);

                               setTimeout(function(){
                                     $( "#promo_code_table" ).load(new_promo_code_table_url);
                                  }, 2000); //refresh every 2 seconds
                               }
                              setTimeout(function(){
                                 $( ".closebtn" ).trigger('click');
                                 
                              }, 3000); //refresh every 3 seconds

                             if(response == 0){
                                
                                swal('Oops...', 'Something went wrong!', 'error');
                             }
                          },
                          
                      });//ajax end
                  // Ajax-------END------------
              }
            });
});

//Delete Promo Code -----------------------END----------------------

$(document).ready(function(){
  //For promo level id (promo applied on ) when click on delivery or resetaurant than user can select multiple restaurants but when user select prodouct, category than user only can select one restaurant, and other thing when select global then user cant select any restaurant 
  //reffrence --  https://www.w3schools.com/howto/tryit.asp?filename=tryhow_css_js_dropdown_filter

  //geting products---------------------by restaurant id ---------start------
  function common_get_products_by_restaurant_id(selected_restaurant_id) {
    //for show product select option-----according selected restaurant
        $.ajax({
              url: BASE_URL+'admin/show_products_according_selected_restaurant',//get product detail
              data:{restaurant_id:selected_restaurant_id},
              type: 'post',
              success: function(response){

                $('#SelectProductDropdown').empty();
                  var select_product_box;
                if(response != 0){

                   var json = JSON.parse(response,true);
                   
                     var checked;
                     var required;
                     var count = 1;
                   
                     $.each(json,function(index,json){
                         
                         select_product_box =  $('#SelectProductDropdown').append( '<label class="enabled-label">'+json.product_name+'<input type="checkbox" class="selected_product_id" id="product_id_'+json.product_id+'" value="'+json.product_id+'" '+checked+'name="selected_product_id[]" '+required+'><span class="checkmark_check"></span> </label>');
                         count++;
                    });  
                     
                }else{
                  select_product_box =  $('#SelectProductDropdown').append( '<label class="enabled-label"> No data Found</label>');
                }
                return select_product_box;
              },
          });//ajax end
  }
  //geting products---------------------by restaurant id ---------end------

  //geting categories---------------------by restaurant id ---------start------
  function common_get_categories_by_restaurant_id(selected_restaurant_id) {
    //for show category select option-----according selected restaurant

        $.ajax({
              url: BASE_URL+'admin/show_categories_according_selected_restaurant',//get category detail
              data:{restaurant_id:selected_restaurant_id},
              type: 'post',
              success: function(response){

                $('#SelectCategoryDropdown').empty();
                  var select_category_box;
                if(response != 0){

                   var json = JSON.parse(response,true);
                 
                   
                     var checked;
                     var required;
                     var count = 1;
                   
                     $.each(json,function(index,json){
                         /* if(count == 1){
                              checked = 'checked="checked"';
                              required = 'required=""';
                          }else{
                              checked = "";
                              required = "";
                          }*/
                          
                         select_category_box =  $('#SelectCategoryDropdown').append( '<label class="enabled-label">'+json.category_name+'<input type="checkbox" class="selected_category_id" id="category_id_'+json.category_id+'" value="'+json.category_id+'"  name="selected_category_id[]" ><span class="checkmark_check"></span> </label>');
                         count++;
                    });  
                     
                }else{
                  select_category_box =  $('#SelectCategoryDropdown').append( '<label class="enabled-label"> No data Found</label>');
                }
                return select_category_box;
              },
          });//ajax end
  }
  //geting categories---------------------by restaurant id ---------end------
 
  //getting  exists products according to promo code name from promtion table ----- edit mode ---- start

   function common_getting_restauant_product_or_category_id_by_promo_code_name(selected_restaurant_id,applied_on){
    
       $.ajax({

          url: BASE_URL+'admin/show_restaurant_product_or_category_according_promo_code_name',//get  product or category detail according to applied on value and promo code name
          data:{promotion_code_name:edit_promotion_code_name, checked_restaurant_id:selected_restaurant_id,level_id:applied_on},
          type: 'post',
          success: function(response){
            if(response != 0){
               var json = JSON.parse(response,true);
                 $.each(json,function(index,json){
                     
                    if(applied_on == 1 || applied_on == 2){//product dropdown 
                      $('#restaurant_id_'+json.restaurant_id).attr('checked','checked');
                    }else if(applied_on == 3){//product dropdown 
                      $('#product_id_'+json.applied_on_id).attr('checked','checked');
                    }else if(applied_on == 4){//category dropdown 
                      $('#category_id_'+json.applied_on_id).attr('checked','checked');
                    }

                    if(logged_in_restaurant_id != ""){
                        $('#restaurant_id_'+json.restaurant_id).attr('checked','checked');
                    }
                }); 
            } 

             //merchant logged restaurant id 
             //when change level id  than its response will be 0 at the edit time because data not avaialble corresponding of selected level id
             if(response == 0 && logged_in_restaurant_id != ""){
                $('#restaurant_id_'+logged_in_restaurant_id).attr('checked','checked');
              }
              
          },
      });//ajax end
  }
  //getting  exists products according to promo code name from promtion table ----- edit mode ---- end

   // comman geting selected value for restaurant , prdoduct and category 
   // if user is in add mode of promtion then it will on click of applied on value , other wise if user in edit mode then it will work on load according to selected applied on value
   function comman_select_restaurant_product_category_options(applied_on) {
        switch (true) {
          case applied_on ==1 || applied_on == 2 || applied_on == 8 : // delivery or resetaurant and self pickup
              $('#for_select_restaurant').removeClass('d-none');
              $('#select_restaurant #SelectRestaurantDropdown input').attr('type','checkbox');
              $('#for_select_prodcut').addClass('d-none');
              $('#for_select_category').addClass('d-none');
              
              var selected_restaurant_length = $('.selected_restaurant_id:checked').length;
              if(selected_restaurant_length == 0){
                $('#SelectRestaurantInput,#SelectRestaurantDropdown').css('border-color','red');
                $('#unselect_restaurant').text('Please Select Restaurant');
              }else{
                  $('#SelectRestaurantInput,#SelectRestaurantDropdown').css('border-color','#ccc');
                  $('#unselect_restaurant').text('');
              }

              $('#SelectRestaurantInput').trigger('click');
 
            break;
         
          case  applied_on == 3: // prodouct

              if(logged_in_restaurant_id != ""){// merchant is logged in means they cant add more restaurant
                $('#for_select_restaurant').addClass('d-none');
              }else{// super admin logged in
                  $('#for_select_restaurant').removeClass('d-none');
              }
            
              $('#for_select_prodcut').removeClass('d-none');
              $('#for_select_category').addClass('d-none');
               
              $('#select_restaurant #SelectRestaurantDropdown input').attr('type','radio');
            
              if(logged_in_restaurant_id != ""){//merchant is logged in
                   var selected_restaurant_id = logged_in_restaurant_id;
               }else{//super admin is logged in
                  var selected_restaurant_id = $('.selected_restaurant_id:checked').val();
               }
           
             if(selected_restaurant_id == undefined || selected_restaurant_id == null){

                   $('#SelectProductDropdown').empty();
                   $('#SelectProductDropdown').append('<label class="enabled-label">Please select a restaurant id</label>');
               }else{
                  //geting products list and show it in to drop down with search
                   common_get_products_by_restaurant_id(selected_restaurant_id);
               }

              // edit mode only
              if(promotion_mode_type == 2 && edit_promotion_code_name != ""){
                    common_getting_restauant_product_or_category_id_by_promo_code_name(selected_restaurant_id,applied_on);
               }

               $('#SelectProductInput').trigger('click');

            break;

          case  applied_on == 4: // category

              if(logged_in_restaurant_id != ""){// merchant is logged in means they cant add more restaurant
                $('#for_select_restaurant').addClass('d-none');
              }else{// super admin logged in
                  $('#for_select_restaurant').removeClass('d-none');
              }
           
              $('#select_restaurant #SelectRestaurantDropdown input').attr('type','radio');
              $('#for_select_prodcut').addClass('d-none');
              $('#for_select_category').removeClass('d-none');

              if(logged_in_restaurant_id != ""){//merchant is logged in
                   var selected_restaurant_id = logged_in_restaurant_id;
               }else{//super admin is logged in
                   var selected_restaurant_id = $('.selected_restaurant_id:checked').val();
               }

               if(selected_restaurant_id == undefined || selected_restaurant_id == null){
                    $('#SelectCategoryDropdown').empty();
                    $('#SelectCategoryDropdown').append( '<label class="enabled-label">Please select a restaurant id</label>');
               }else{
                    //geting products list and show it in to drop down with search
                    common_get_categories_by_restaurant_id(selected_restaurant_id);
               }

                // edit mode only
                if(promotion_mode_type == 2 && edit_promotion_code_name != ""){
                    common_getting_restauant_product_or_category_id_by_promo_code_name(selected_restaurant_id,applied_on);
               }

              $('#SelectCategoryInput').trigger('click');

            break;

          default:

           $('#for_select_restaurant').addClass('d-none');
           $('#select_restaurant #SelectRestaurantDropdown input').attr('type','checkbox');
           $('#for_select_prodcut').addClass('d-none');
            $('#for_select_category').addClass('d-none');
             
        }//switch case end
   }

   // work on edit mode of promotion 
   if(promotion_mode_type == 2 && edit_level_id != ""){
      comman_select_restaurant_product_category_options(edit_level_id);//edit_level_id is applied on id 
   } 
 
   $('body').on('change', '#applied_on,.selected_restaurant_id', function() {
      var applied_on = $('#applied_on').val();



      //add /edit -----mode
      comman_select_restaurant_product_category_options(applied_on);

      //check if applied on deilvery then max delivery charge value feild will be show but its not  mandatory
      if(applied_on == 1){
        $('#show_max_delivery_charge_field').removeClass('d-none');
      }else{
          $('#show_max_delivery_charge_field').addClass('d-none');
      }

      //---------------------edit mode start---------------------------
          //getting resataurant at the time edit when chanae level id (applied on). because at that time input change checkbox and radio then , at the time of product and catgory selection checked restauant data loss thats why we need to click on applied on 1 or 2 geting reataurant again and show checked. 
         if(promotion_mode_type == 2 && edit_promotion_code_name != ""){
               $('.selected_restaurant_id').removeAttr('checked'); 
               if(logged_in_restaurant_id != ""){//merchant is logged in
                   var selected_restaurant_id = logged_in_restaurant_id;
               }else{//super admin is logged in
                   var selected_restaurant_id = "";
               }
              
               common_getting_restauant_product_or_category_id_by_promo_code_name(selected_restaurant_id,applied_on);
              
           }
    
       // we need to removed checked because when we change  applied on to 3 or 4 from 1 or 2 then we lost exist checkd restaurant id's and same also when  checked new resaturant.
       // and for this we again fetch data by ajax for restaurant but it containt already checked attr it works on load but its not load when change applied on value or cheked new restaurant
       // the purpose reset check attr remove to add attr.
       if($(event.target).is('.selected_restaurant_id') && promotion_mode_type == 2 && edit_promotion_code_name != "") {//check wich element is clicked
           //alert(event.target.id + ' was clicked.');
          $('.selected_restaurant_id').removeAttr('checked');
       }
      //---------------------edit mode end---------------------------
      
   });

 }); //document ready close

//date validation check start-----------------
  function isDate(txtDate) {
           var currVal = txtDate;
           if (currVal == '')
               return false;
           var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/; 
           var dtArray = currVal.match(rxDatePattern);

           if (dtArray == null)
               return false;
           
           dtMonth = dtArray[1];
           dtDay = dtArray[3];
           dtYear = dtArray[5];

           if (dtMonth < 1 || dtMonth > 12)
               return false;
           else if (dtDay < 1 || dtDay > 31)
               return false;
           else if ((dtMonth == 4 || dtMonth == 6 || dtMonth == 9 || dtMonth == 11) && dtDay == 31)
               return false;
           else if (dtMonth == 2) {
               var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
               if (dtDay > 29 || (dtDay == 29 && !isleap))
                   return false;
           }
           return true;
       }
 
$('body').on('change', '.date_valid', function() {
       var date =  $(this).val();
       var date_input_id =  $(this).attr('id');

    function valid_date(date,date_input_id)
       {
               if (isDate(date) == false) {
                   $('#'+date_input_id).val('');
                  
               } else {
                    $('#'+date_input_id).val(date);
                   
               }     
       }
       valid_date(date,date_input_id);
  });
//date validation check end-----------------

//select all resaturnat on one time ----------------START------------------
$('body').on('click', '#select_all_restaurant', function() {
      if($(this).prop("checked") == true){
         $('.selected_restaurant_id').prop('checked',true); 
     }else{
         $('.selected_restaurant_id').prop('checked',false); 
     }
});
//select all resaturnat on one time ----------------END------------------
var if_promo_for_all_rest;
if(if_promo_for_all_rest == 1){
    $('#select_all_restaurant').trigger('click');
}
 
//Add and Edit Promotion -------------------START-----------------------

//promo use type check is forever or limited
$('body').on('change', '#promo_use_type', function() {
     var promo_use_type = $('#promo_use_type:checked').val();
      if(promo_use_type == 2){//1 - forever , 2 - limited
        $('#if_promo_use_type_limit').removeClass('d-none');
      }else{
        $('#if_promo_use_type_limit').addClass('d-none');
      }
});
$('body').on('click', '#add_promotion_submit', function() {
 
     var selected_restaurant_id = [];
      $.each($(".selected_restaurant_id:checked"), function(){
          selected_restaurant_id.push($(this).val());
      });
      //alert("selected_restaurant_id are: " + selected_restaurant_id.join(", "));

     var selected_product_id = [];
      $.each($(".selected_product_id:checked"), function(){
          selected_product_id.push($(this).val());
      });

       var selected_category_id = [];
      $.each($(".selected_category_id:checked"), function(){
          selected_category_id.push($(this).val());
      });

      if($('#select_all_restaurant').prop("checked") == true){
         var check_selected_all_restaurant = 1;//promo select for all restaruant
      }else{
         var check_selected_all_restaurant = 0;//promo select for some restaruant
      }

      var promo_use_type = $('#promo_use_type:checked').val();
 

       var check_PromotionFormData_is_blank = [];
         
       var PromotionFormData = {

          promotion_type : $('#promotion_type').val(),
          promo_code : $('#promo_code').val(),
          discount_value : $('#discount_value').val(),
          description : $('#description').val(),
          promo_code_start_date : $('#promo_code_start_date').val(),
          promo_code_end_date : $('#promo_code_end_date').val(),
          max_discount_value : $('#max_discount_value').val(),
          max_delivery_charge : $('#max_delivery_charge').val(),//  if promo applied on deilvery charge and max discount value is given then value will be insert otherwise it will be 0
          max_allowed_time : $('#max_allowed_time').val(),
          minimum_order_amount : $('#minimum_order_amount').val(),
          promotion_applicaion_mode : $('#promotion_applicaion_mode').val(),
          allow_single_user : $('#allow_single_user:checked').val(),
          applied_on : $('#applied_on').val(),
          selected_restaurant_id : selected_restaurant_id,
          selected_product_id : selected_product_id,
          selected_category_id : selected_category_id,
          check_selected_all_restaurant : check_selected_all_restaurant,

          //for edit time ----
          edit_promotion_code_name:edit_promotion_code_name
      };
     
     if(PromotionFormData.promotion_type == ''){
          $('#promotion_type').css('border-color','red');
          $('#unselect_promotion_type').text('Please Select Promotion Type');
          check_PromotionFormData_is_blank.push(false);
      }else{
         $('#promotion_type').css('border-color','#ccc');
         $('#unselect_promotion_type').text('');
          check_PromotionFormData_is_blank.push(true);

      }

      if(PromotionFormData.promo_code == ''){
        $('#promo_code').css('border-color','red');
        $('#unfill_promo_code').text('Please Fill Promo Code Name');
          check_PromotionFormData_is_blank.push(false);
      }else{
         $('#promo_code').css('border-color','#ccc');
         $('#unfill_promo_code').text('');
         check_PromotionFormData_is_blank.push(true);
      }


      if(PromotionFormData.discount_value == ''){
        $('#discount_value').css('border-color','red');
        $('#unfill_discount_value').text('Please Fill Discount Value');
         check_PromotionFormData_is_blank.push(false);
      }else{
           $('#discount_value').css('border-color','#ccc');
           $('#unfill_discount_value').text('');
           check_PromotionFormData_is_blank.push(true);
      }
     
     /* if(PromotionFormData.description.length < 20){// or if blank
        $('#description').css('border-color','red');
        $('#unfill_description').text('Please Fill Discription. Discription  should be contain minimum 20 characters.');
         check_PromotionFormData_is_blank.push(false);
      }else{
         
           $('#description').css('border-color','#ccc');
           $('#unfill_description').text('');
           check_PromotionFormData_is_blank.push(true);
      }*/

        if(promo_use_type == 2){//limited
             if(PromotionFormData.promo_code_start_date == ''){
                $('#promo_code_start_date').css('border-color','red');
                $('#unfill_start_date').text('Please Select Start Date');
                 check_PromotionFormData_is_blank.push(false);
              }else{
                   $('#promo_code_start_date').css('border-color','#ccc');
                   $('#unfill_start_date').text('');
                   check_PromotionFormData_is_blank.push(true);
              }

              if(PromotionFormData.promo_code_end_date == ''){
                $('#promo_code_end_date').css('border-color','red');
                $('#unfill_end_date').text('Please Select End Date');
                 check_PromotionFormData_is_blank.push(false);
              }else{
                   $('#promo_code_end_date').css('border-color','#ccc');
                   $('#unfill_end_date').text('');
                   check_PromotionFormData_is_blank.push(true);
              }
        }

   

     /* if(PromotionFormData.max_discount_value == ''){
        $('#max_discount_value').css('border-color','red');
        $('#unfill_max_discount_value').text('Please Fill Maximum Discount Value');
         check_PromotionFormData_is_blank.push(false);
      }else{
           $('#max_discount_value').css('border-color','#ccc');
           $('#unfill_max_discount_value').text('');
           check_PromotionFormData_is_blank.push(true);
      }*/
/*
      if(PromotionFormData.max_allowed_time == ''){
        $('#max_allowed_time').css('border-color','red');
        $('#unfill_max_allowed_time').text('Please Fill in how long the user user can use the promo code');
         check_PromotionFormData_is_blank.push(false);
      }else{
           $('#max_allowed_time').css('border-color','#ccc');
           $('#unfill_max_allowed_time').text('');
           check_PromotionFormData_is_blank.push(true);
      }*/

      if(PromotionFormData.minimum_order_amount == ''){
        $('#minimum_order_amount').css('border-color','red');
        $('#unfill_minimum_order_amount').text('Please Fill minimum order amount');
         check_PromotionFormData_is_blank.push(false);
      }else{
           $('#minimum_order_amount').css('border-color','#ccc');
           $('#unfill_minimum_order_amount').text('');
           check_PromotionFormData_is_blank.push(true);
      }

      if(PromotionFormData.promotion_applicaion_mode == ''){
        $('#promotion_applicaion_mode').css('border-color','red');
        $('#unselect_applicaion_mode').text('Please select applicaton mode ');
         check_PromotionFormData_is_blank.push(false);
      }else{
           $('#promotion_applicaion_mode').css('border-color','#ccc');
           $('#unselect_applicaion_mode').text('');
           check_PromotionFormData_is_blank.push(true);
      }
 /*
      if(PromotionFormData.allow_single_user == '' || PromotionFormData.allow_single_user == undefined || PromotionFormData.allow_single_user == null){
        $('#allow_single_user').css('border-color','red');
        $('#unselect_allow_single_user').text('Please select that a single user can use this promo code for multiple times.');
        
         check_PromotionFormData_is_blank.push(false);
      }else{
           $('#allow_single_user').css('border-color','#ccc');
           $('#unselect_allow_single_user').text('');
           check_PromotionFormData_is_blank.push(true);
      }*/



      if(PromotionFormData.applied_on == ''){
        $('#applied_on').css('border-color','red');
        $('#unselect_applied_on').text('Please select on which you want to applied on');
         check_PromotionFormData_is_blank.push(false);
      }else{
           $('#applied_on').css('border-color','#ccc');
           $('#unselect_applied_on').text('');
           check_PromotionFormData_is_blank.push(true);
      }

      if(PromotionFormData.selected_restaurant_id == '' && (PromotionFormData.applied_on == '1' || PromotionFormData.applied_on == '2' || PromotionFormData.applied_on == '3' || PromotionFormData.applied_on == '4')){
        $('#SelectRestaurantInput,#SelectRestaurantDropdown').css('border-color','red');
        $('#unselect_restaurant').text('Please Select Restaurant');
         check_PromotionFormData_is_blank.push(false);
      }else{
          $('#SelectRestaurantInput,#SelectRestaurantDropdown').css('border-color','#ccc');
          $('#unselect_restaurant').text('');
          check_PromotionFormData_is_blank.push(true);
      }
  
       if(PromotionFormData.selected_product_id == '' && PromotionFormData.applied_on == '3'){
        $('#SelectProductInput,#SelectProductDropdown').css('border-color','red');
        $('#unselect_product').text('Please Select Product');
         check_PromotionFormData_is_blank.push(false);
      }else{
          $('#SelectProductInput,#SelectProductDropdown').css('border-color','#ccc');
          $('#unselect_product').text('');
          check_PromotionFormData_is_blank.push(true);
      }

      if(PromotionFormData.selected_category_id == ''  && PromotionFormData.applied_on == '4'){
        $('#SelectCategoryInput,#SelectCategoryDropdown').css('border-color','red');
        $('#unselect_category').text('Please Select Category');
         check_PromotionFormData_is_blank.push(false);
      }else{
          $('#SelectCategoryInput,#SelectCategoryDropdown').css('border-color','#ccc');
          $('#unselect_category').text('');
          check_PromotionFormData_is_blank.push(true);
      }

     //alert(check_PromotionFormData_is_blank);
     var check_empty_form_value = check_PromotionFormData_is_blank.includes(false);

     if(check_empty_form_value == false){//check_empty_form_value value is false
        //its means in array , false does not exist  and its ready to post data to controller
         swal({
                title: 'Wait..',
                text: "Please wait  and Don't do any action while we are processing your request!",
                type: 'Wait',
                buttons: false,
              }); 

          $.ajax({
              url:  BASE_URL+"admin/add_edit_promotion_details/"+promotion_mode_type,
              type: 'post',
              data: PromotionFormData,
              dataType: 'json',
             
              success: function(response){
                
                 if(response == 1 && promotion_mode_type == 1){
                    
                     /*setTimeout(function(){ 
                        swal('Success', 'Promo Code added Successfully', 'success');
                      }, 3000);*/
                     setTimeout(function(){ 
                         window.location.replace(BASE_URL+'admin/promo_codes');
                      }, 3000);
                 }

                 if(response == 1 && promotion_mode_type == 2){
                   /*  setTimeout(function(){ 
                       swal('Success', 'Promo Code Updated Successfully', 'success');
                      }, 3000);*/
                     setTimeout(function(){ 
                        window.location.replace(BASE_URL+'admin/promo_codes');
                      }, 3000);
                 }

                 if(response == 2 && promotion_mode_type == 2){
                    swal('Dont Worry...', 'Nothing Changed!');
                 }

                 if(response == 3){
                  swal('Sorry...', 'This promo code name already exists please fill other name!', 'error');
                    $('#promo_code').css('border-color','red');
                    $('#unfill_promo_code').text('Please Fill Other Promo Code Name');
                 }

                  if(response == 4){
                     swal('Sorry...', 'May some be missing some field or Internal server error!', 'error');
                 }
              },
           });//ajax end

     }else{
         //its means in array , false is exist  and its not ready to post data to controller
          //swal('Oops...', 'You are missing a required field ', 'error');
     }

});
//Add and Edit Promotion -------------------END-----------------------
 

//Set Discount ----------------------------START--------------------

$('body').on('click', '.set_edit_discount', function() {

   var button_id = $(this).attr('id');
 
   var  restaurant_id = $('#select_restaurant_id_for_discount').children("option:selected").val();

     if(button_id == 'set_discount'){
         $('#discount_set_edit_mode').val('1'); //1  = set mode , 2 = edit mode
         $('.set_edit_title').text('Set Discount');
     }else if(button_id == 'edit_discount'){
       
        $('#discount_set_edit_mode').val('2'); //1  = set mode , 2 = edit mode
        $('.set_edit_title').text('Edit Discount');
     }

    if(restaurant_id == ''){
        $('#select_restaurant_id_for_discount').css('border-color','red');
        $('#unselect_restaurant').text('Please Select Restaurant');
    }else{
       $('#select_restaurant_id_for_discount').css('border-color','#ccc');
       $('#unselect_restaurant').text('');
    }

});

$('body').on('click', '#discount_submit', function() {

     var check_DiscountFormData_is_blank = [];
     
     var DiscountFormData = {

         restaurant_id : $('#select_restaurant_id_for_discount').children("option:selected").val(),
         discount_name: $('#discount_name').val(),
         discount_value: $('#discount_value').val(),
         description: $('#description').val(),
         max_amount: $('#max_amount').val(),
         discount_start_date: $('#discount_start_date').val(),
         discount_end_date: $('#discount_end_date').val(),

         //for set or edit
         discount_set_edit_mode: $('#discount_set_edit_mode').val(), //1  = set mode , 2 = edit mode

         //for edit time ----
         edit_discount_id :  $('#edit_discount_id').val(),
    };
 
     if(DiscountFormData.restaurant_id == ''){
          $('#select_restaurant_id_for_discount').css('border-color','red');
          $('#unselect_restaurant').text('Please Select Restaurant');
          check_DiscountFormData_is_blank.push(false);
      }else{
         $('#select_restaurant_id_for_discount').css('border-color','#ccc');
         $('#unselect_restaurant').text('');
          check_DiscountFormData_is_blank.push(true);
      }

      if(DiscountFormData.discount_name == ''){
          $('#discount_name').css('border-color','red');
          $('#unfill_discount_name').text('Please Fill Discount Name');
          check_DiscountFormData_is_blank.push(false);
      }else{
         $('#discount_name').css('border-color','#ccc');
         $('#unfill_discount_name').text('');
          check_DiscountFormData_is_blank.push(true);
      }

      if(DiscountFormData.discount_value == ''){
          $('#discount_value').css('border-color','red');
          $('#unfill_discount_value').text('Please Fill Discount Value in Percent');
          check_DiscountFormData_is_blank.push(false);
      }else{
         $('#discount_value').css('border-color','#ccc');
         $('#unfill_discount_value').text('');
          check_DiscountFormData_is_blank.push(true);
      }

      if(DiscountFormData.description.length =="" ){// or if blank
        $('#description').css('border-color','red');
        $('#unfill_description').text('Please Fill Discription.');
         check_DiscountFormData_is_blank.push(false);
      }else{
         $('#description').css('border-color','#ccc');
         $('#unfill_description').text('');
         check_DiscountFormData_is_blank.push(true);
      }

     if(DiscountFormData.max_amount == ''){
        $('#max_amount').css('border-color','red');
        $('#unfill_max_amount').text('Please Fill Maximum Amount.');
         check_DiscountFormData_is_blank.push(false);
      }else{
         $('#max_amount').css('border-color','#ccc');
         $('#unfill_max_amount').text('');
         check_DiscountFormData_is_blank.push(true);
      }

     if(DiscountFormData.discount_start_date == ''){
        $('#discount_start_date').css('border-color','red');
        $('#unfill_start_date').text('Please Select Start Date.');
         check_DiscountFormData_is_blank.push(false);
      }else{
         $('#discount_start_date').css('border-color','#ccc');
         $('#unfill_start_date').text('');
         check_DiscountFormData_is_blank.push(true);
      }

     if(DiscountFormData.discount_end_date == ''){
        $('#discount_end_date').css('border-color','red');
        $('#unfill_end_date').text('Please Select End Date.');
         check_DiscountFormData_is_blank.push(false);
      }else{
         $('#discount_end_date').css('border-color','#ccc');
         $('#unfill_end_date').text('');
         check_DiscountFormData_is_blank.push(true);
      }

      var check_empty_form_value = check_DiscountFormData_is_blank.includes(false);

      if(check_empty_form_value == false){//check_empty_form_value value is false
       //its means in array , false does not exist  and its ready to post data to controller
         swal({
                title: 'Wait..',
                text: "Please wait  and Don't do any action while we are processing your request!",
                type: 'Wait',
                buttons: false,
              }); 

          $.ajax({
              url:  BASE_URL+"admin/set_edit_discount/"+DiscountFormData.discount_set_edit_mode,
              type: 'post',
              data: DiscountFormData,
              dataType: 'json',
             
              success: function(response){
                
                 if(response == 1 && DiscountFormData.discount_set_edit_mode == 1){
                    
                     setTimeout(function(){ 
                        swal('Success', 'Discount added Successfully', 'success');
                      }, 1000);
                     setTimeout(function(){ 
                         window.location.replace(BASE_URL+'admin/discount/'+DiscountFormData.restaurant_id);
                      }, 3000);
                 }

                 if(response == 1 && DiscountFormData.discount_set_edit_mode == 2){
                     setTimeout(function(){ 
                       swal('Success', 'Discount  Updated Successfully', 'success');
                      }, 3000);
                     setTimeout(function(){ 
                        window.location.replace(BASE_URL+'admin/discount/'+DiscountFormData.restaurant_id);
                      }, 4000);
                 }

                 if(response == 2 && DiscountFormData.discount_set_edit_mode == 2){
                   // swal('Dont Worry...', 'Nothing Changed!');
                 }
 
                 if(response == 3){
                  swal('Sorry...', 'May some be missing some field or Internal server error!', 'error');
                 }
              },
           });//ajax end

      }else{
         //its means in array , false is exist  and its not ready to post data to controller
         // swal('Oops...', 'You are missing a required field ', 'error');
     }
});
//Set Discount ----------------------------END--------------------

//Change status ( Enable/Disable) of discount   -----------------------START---------------------
$('body').on('change', '.discount_status', function() {
        
        var discount_status_value = $(this).val();
        var edit_discount_id = $(this).attr('edit_discount_id');
     
        var discount_status;
        if(discount_status_value == 2){
            discount_status = "Disable";
            chanegd_status_value = 1;
        }else if(discount_status_value == 1){
           discount_status = "Enable";
            chanegd_status_value = 2;
        }

       // Ajax-------SATRT------------
        $.ajax({
        url: BASE_URL+'admin/active_inactive_discount',
        data: { 
            discount_id: edit_discount_id,discount_status_value:discount_status_value
        },
        type: 'post',
        success: function(response){
            if(response == 1){
              /* swal("Discount Successfully "+discount_status+"", {
                  icon: "success",
                });*/
               $('.discount_status').text(discount_status);
               $('.discount_status').attr('discount_status',chanegd_status_value);
             }
           
           if(response == 0){
              swal('Oops...', 'Internal server error', 'error');
           }
        },
    });
    // Ajax-------END------------
});

///Change status ( Enable/Disable) of discount   -----------------------END----------------------

// delete discount ---------------------------START------------------------
$('body').on('click', '.delete_discount', function() {

        var edit_discount_id = $(this).attr('edit_discount_id');
        var  restaurant_id = $('#select_restaurant_id_for_discount').children("option:selected").val();

        swal({
            title: "Are you sure to delete this Discount permanently?",
            text: "Once deleted, You will not be able to recover the action!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              
                  // Ajax-------SATRT------------
                    $.ajax({
                    url: BASE_URL+'admin/delete_discount',
                    data: { 
                        discount_id: edit_discount_id,
                    },
                    type: 'post',
                    success: function(response){
                        if(response == 1){
                           swal("Discount Successfully Delete", {
                              icon: "success",
                            });
                            setTimeout(function(){ 
                              window.location.replace(BASE_URL+'admin/discount/'+restaurant_id);
                            }, 2000);
                         }
                       
                       if(response == 0){
                          swal('Oops...', 'Internal server error', 'error');
                       }
                    },
                });
                // Ajax-------END------------
            }
          });
      
});
// delete discount ---------------------------END------------------------

// Referral Submit/ Saveing-------------------------START--------------------------
$('body').on('click', '#referral_submit', function() {

     var check_ReferralFormData_is_blank = [];

     var  edit_referral_id =  $('#edit_referral_id').val();

    var add_edit_mode;
     if(edit_referral_id != ""){
        add_edit_mode = 2;// always edit mode after once insert
     }else if(edit_referral_id == ""){
       add_edit_mode = 1;// first it will 1 
     }
     
     var ReferralFormData = {

         language: $('#language').val(),
         referral_type: $('#referral_type').val(),

         // for referrer-----------------
         referrer_discount_value: $('#referrer_discount_value').val(),
         referrer_max_discount_value: $('#referrer_max_discount_value').val(),
         referrer_discription: $('#referrer_discription').val(),

         // for referee-----------------
         referee_discount_value: $('#referee_discount_value').val(),
         referee_max_discount_value: $('#max_discount_value').val(),
         referee_discription: $('#referee_discription').val(),

         minimum_order_amount: $('#minimum_order_amount').val(),

         //for set or edit
         referral_set_edit_mode: add_edit_mode, //1  = set mode , 2 = edit mode

         //for edit time ----
         edit_referral_id :  $('#edit_referral_id').val(),
    };
    
     if(ReferralFormData.language == ''){
          $('#language').css('border-color','red');
          $('#unselect_language').text('Please Select Language');
          check_ReferralFormData_is_blank.push(false);
      }else{
         $('#language').css('border-color','#ccc');
         $('#unselect_language').text('');
          check_ReferralFormData_is_blank.push(true);
      }

      if(ReferralFormData.referral_type == ''){
          $('#referral_type').css('border-color','red');
          $('#unselect_referral_type').text('Please Select Referral Type');
          check_ReferralFormData_is_blank.push(false);
      }else{
         $('#referral_type').css('border-color','#ccc');
         $('#unselect_referral_type').text('');
          check_ReferralFormData_is_blank.push(true);
      }

      if(ReferralFormData.referrer_discount_value == ''){
          $('#referrer_discount_value').css('border-color','red');
          $('#unfill_referrer_discount_value').text('Please Fill Referrer Discount in Percent');
          check_ReferralFormData_is_blank.push(false);
      }else{
         $('#referrer_discount_value').css('border-color','#ccc');
         $('#unfill_referrer_discount_value').text('');
          check_ReferralFormData_is_blank.push(true);
      }

      if(ReferralFormData.referrer_max_discount_value == ''){
          $('#referrer_max_discount_value').css('border-color','red');
          $('#unfill_referrer_max_discount_value').text('Please Fill Max Referrer Discount Value');
          check_ReferralFormData_is_blank.push(false);
      }else{
         $('#referrer_max_discount_value').css('border-color','#ccc');
         $('#unfill_referrer_max_discount_value').text('');
          check_ReferralFormData_is_blank.push(true);
      }

      if(ReferralFormData.referrer_discription.length < 20){// or if blank
        $('#referrer_discription').css('border-color','red');
        $('#unfill_referrer_discription').text('Please Fill Referrer Discription. Discription should be contain minimum 20 characters.');
         check_ReferralFormData_is_blank.push(false);
      }else{
         $('#referrer_discription').css('border-color','#ccc');
         $('#unfill_referrer_discription').text('');
         check_ReferralFormData_is_blank.push(true);
      }

     if(ReferralFormData.referee_discount_value == ''){
        $('#referee_discount_value').css('border-color','red');
        $('#unfill_referee_discount_value').text('Please Fill Max Referee Discount Value.');
         check_ReferralFormData_is_blank.push(false);
      }else{
         $('#referee_discount_value').css('border-color','#ccc');
         $('#unfill_referee_discount_value').text('');
         check_ReferralFormData_is_blank.push(true);
      }

     if(ReferralFormData.referee_max_discount_value == ''){
        $('#referee_max_discount_value').css('border-color','red');
        $('#unfill_referee_max_discount_value').text('Please Fill Max Referee Discount Value.');
         check_ReferralFormData_is_blank.push(false);
      }else{
         $('#referee_max_discount_value').css('border-color','#ccc');
         $('#unfill_referee_max_discount_value').text('');
         check_ReferralFormData_is_blank.push(true);
      }
     
      if(ReferralFormData.referee_discription.length < 20){// or if blank
        $('#referee_discription').css('border-color','red');
        $('#unfill_referee_discription').text('Please Fill Referee Discription. Discription should be contain minimum 20 characters.');
         check_ReferralFormData_is_blank.push(false);
      }else{
         $('#referee_discription').css('border-color','#ccc');
         $('#unfill_referee_discription').text('');
         check_ReferralFormData_is_blank.push(true);
      }

      if(ReferralFormData.minimum_order_amount == ''){
        $('#minimum_order_amount').css('border-color','red');
        $('#unfill_minimum_order_amount').text('Please Fill Minimum Order Amount.');
         check_ReferralFormData_is_blank.push(false);
      }else{
         $('#minimum_order_amount').css('border-color','#ccc');
         $('#unfill_minimum_order_amount').text('');
         check_ReferralFormData_is_blank.push(true);
      }

      var check_empty_form_value = check_ReferralFormData_is_blank.includes(false);

      if(check_empty_form_value == false){//check_empty_form_value value is false
       //its means in array , false does not exist  and its ready to post data to controller
         swal({
                title: 'Wait..',
                text: "Please wait  and Don't do any action while we are processing your request!",
                type: 'Wait',
                buttons: false,
              }); 

          $.ajax({
              url:  BASE_URL+"admin/add_edit_Referral/"+ReferralFormData.referral_set_edit_mode,
              type: 'post',
              data: ReferralFormData,
              dataType: 'json',
             
              success: function(response){
                
                 if(response == 1 && ReferralFormData.referral_set_edit_mode == 1){
                    
                     setTimeout(function(){ 
                        swal('Success', 'Referral added Successfully', 'success');
                      }, 1000);
                     setTimeout(function(){ 
                         window.location.replace(BASE_URL+'admin/referral/');
                      }, 3000);
                 }

                 if(response == 1 && ReferralFormData.referral_set_edit_mode == 2){
                     setTimeout(function(){ 
                       swal('Success', 'Referral  Updated Successfully', 'success');
                      }, 3000);
                     setTimeout(function(){ 
                        window.location.replace(BASE_URL+'admin/referral/');
                      }, 4000);
                 }

                 if(response == 2 && ReferralFormData.referral_set_edit_mode == 2){
                    //swal('Dont Worry...', 'Nothing Changed!');
                 }

                 if(response == 3){
                  swal('Sorry...', 'May some be missing some field or Internal server error!', 'error');
                 }
              },
           });//ajax end

      }else{
         //its means in array , false is exist  and its not ready to post data to controller
          swal('Oops...', 'You are missing a required field', 'error');
     }
});
 
// Referral Submit/ Saveing--------------------------END--------------------------

//Change status ( Enable/Disable) of Referral   -----------------------START---------------------
$('body').on('change', '.referral_status', function() {
        
        var referral_status_value = $(this).val();
        var edit_referral_id = $(this).attr('edit_referral_id');
        
        var referral_status;
        if(referral_status_value == 2){
            referral_status = "Disable";
            chanegd_status_value = 1;
        }else if(referral_status_value == 1){
           referral_status = "Enable";
            chanegd_status_value = 2;
        }

          // Ajax-------SATRT------------
                  
                    $.ajax({
                    url: BASE_URL+'admin/active_inactive_referral',
                    data: { 
                        referral_id: edit_referral_id,referral_status_value:referral_status_value
                    },
                    type: 'post',
                    success: function(response){
                        if(response == 1){
                           swal("Referral Successfully "+referral_status+"", {
                              icon: "success",
                            });
                           $('.referral_status').text(referral_status);
                           $('.referral_status').attr('referral_status',chanegd_status_value);
                         }
                       
                       if(response == 0){
                          swal('Oops...', 'Internal server error', 'error');
                       }
                    },
                });
                // Ajax-------END------------
});

///Change status ( Enable/Disable) of Referral   -----------------------END----------------------

// Add / Edit Ad Banner -----------------------------START-----------------

// For Restaurant---------------------------START---------------
// Check if Restaurant Toggle is enabled then admin can select restaurant otherwise it will be disabled and restaurant value will be blank.
$('body').on('change', '#check_restaurant_enable', function() {
     var isEnable = $(this).is(':checked');
    if(isEnable == true){
         //alert('true');
         $('#SelectBannerRestaurantInput').removeAttr('disabled');
         $('#SelectBannerRestaurantDropdown').removeClass('d-none');
    }else{
          //alert('false');
          $('#SelectBannerRestaurantInput').attr('disabled','disabled');
          $('#SelectBannerRestaurantDropdown').addClass('d-none');
          $('#SelectBannerRestaurantInput').val('');
          $('#SelectBannerRestaurantInput').attr('selected_rest_id','');

           // remove error text which appear on submit click
          $('#SelectBannerRestaurantInput').css('border-color','#ccc');
           $('#unselect_restaurant').text('');
    }

      // either restaurant toggle can enable either external link
      var iExternalLinkToogleEnable = $('#check_external_link_enable').is(':checked');
      if(iExternalLinkToogleEnable == true){
       $('#check_external_link_enable').trigger('click');
     }
});

// If the restaurant is select then its value will be shown in the restaurant search input and that attr value (selected_rest_id)  will get at submission time.
$('body').on('change', '.select_restaurant_id_for_ad_banner', function() {
    var selected_restaurant_id = $(this).val();
    var selected_restaurant_name = $(this).parent().text();
    $('#SelectBannerRestaurantInput').val(selected_restaurant_name);
    $('#SelectBannerRestaurantInput').attr('selected_rest_id',selected_restaurant_id);//this value will be go for submition
});
// For Restaurant---------------------------END---------------

//For external link -------------------------START------------------------
//Check if external link Toggle is enable then admin can add external link otherwise it will be disable  and external link value will be blank
$('body').on('change', '#check_external_link_enable', function() {
     var isEnable = $(this).is(':checked');
    if(isEnable == true){
       //alert('true');
        $('#external_link').removeAttr('disabled');
    }else{
        //alert('false');
        $('#external_link').attr('disabled','disabled');
        $('#external_link').val('');

       // remove error text which appear on submit click
        $('#external_link').css('border-color','#ccc');
        $('#unfill_external_link').text('');
    }
    
      // either restaurant toggle can enable either external link
     var isRestaurantToogleEnable = $('#check_restaurant_enable').is(':checked');
     if(isRestaurantToogleEnable == true){
       $('#check_restaurant_enable').trigger('click');
     }
});
//For external link -------------------------END------------------------

//for click on add or edit button for add or edit banner -----
$('body').on('click', '.add_edit_ad_banner_popup', function() {

     var mode = $(this).attr('mode');

    $('#ad_banner_add_edit_mode').val(mode);// 1  - add ad banner or 2 - edit ad bnanner 

    // remove error---
    $('input').removeAttr('style');
    $('.error').empty();
   
    if(mode == 1){// add
        //add
        // change modle title
        $('.ad_banner_title').text('Add Ad Banner');

        $('#ad_banner_name').val('');
        $('#ad_banner_description').val('');

     // For web banner display and input
      $('#disp_img1').attr('src',BASE_URL+'assets/images/default_ad_banner.jpg');
      $('#file1').attr('value','');

      // For web Mobile banner display and input
       $('#disp_img2').attr('src',BASE_URL+'assets/images/default_ad_banner.jpg');
       $('#file2').attr('value','');

       // For  Mobile banner display and input
       $('#disp_img3').attr('src',BASE_URL+'assets/images/default_ad_banner.jpg');
      $('#file3').attr('value','');

       // either restaurant toggle can enable either external link
       var isRestaurantToogleEnable = $('#check_restaurant_enable').is(':checked');
       if(isRestaurantToogleEnable == true){
         $('#check_restaurant_enable').trigger('click');
       }

        // either restaurant toggle can enable either external link
        var iExternalLinkToogleEnable = $('#check_external_link_enable').is(':checked');
        if(iExternalLinkToogleEnable == true){
         $('#check_external_link_enable').trigger('click');
     }
    }else if(mode == 2){//edit
       // change modle title
        $('.ad_banner_title').text('Edit Ad Banner');

         var  edit_ad_banner_id = $(this).attr('edit_id');
         $('#edit_ad_banner_id').val(edit_ad_banner_id);
          
         $.ajax({
              url:  BASE_URL+'admin/get_ad_banner_detail',
              type: 'post',
              data: {ad_banner_id:edit_ad_banner_id},
              success: function(response){
                
                if(response != 0){
                   var data = JSON.parse(response,true);

                      $('#ad_banner_name').val(data.ad_name);

                     // either restaurant toggle can enable either external link
                     // if restaurant has selected
                      if(data.restaurant_id != 0){
                        $('#SelectBannerRestaurantInput').removeAttr('disabled');
                        $('#SelectBannerRestaurantDropdown').removeClass('d-none');
                        $('#restaurant_id_'+data.restaurant_id ).attr('checked','checked');
                        var selected_restaurant_name =$('#restaurant_id_'+data.restaurant_id).parent().text();
                         $('#SelectBannerRestaurantInput').val(selected_restaurant_name.trim());
                         $('#SelectBannerRestaurantInput').attr('selected_rest_id',data.restaurant_id);
                         //restaurant toggle enabled
                         $('#check_restaurant_enable').trigger('click');
                      }

                     // either restaurant toggle can enable either external link
                      //if external link have
                      if(data.external_ink != ""){
                        $('#check_external_link_enable').trigger('click');
                         $('#external_link').val(data.external_ink);
                         //restaurant toggle enabled
                      }
                      
                      $('#ad_banner_description').val(data.ad_description);

                     // For web banner display and input
                      $('#disp_img1').attr('src',BASE_URL+data.ad_image);
                      $('#file1').attr('value',data.ad_image);
 
                      // For web Mobile banner display and input
                       $('#disp_img2').attr('src',BASE_URL+data.ad_image_web);
                       $('#file2').attr('value',data.ad_image_web);
 
                       // For  Mobile banner display and input
                       $('#disp_img3').attr('src',BASE_URL+data.ad_image_mobile);
                      $('#file3').attr('value',data.ad_image_mobile);
                }

              },
                
        });//ajax end
    }
});

//submition -----------------------
$('body').on('click', '#add_edit_banner_submit', function() {
     
    // for image--------------START--------------------
     var web_banner_image =  $('#file1')[0].files;//uploded web banner image
     var web_mobile_banner_image = $('#file2')[0].files;//uploded web  mobile banner image
     var mobile_banner_image = $('#file3')[0].files;//uploded mobile banner image
    // for image--------------END--------------------

      // edit time check  exist image   ----start-----
      var exist_web_banner_image = $('#file1').attr('value');
      if(exist_web_banner_image != undefined || exist_web_banner_image != null ){
          exist_web_banner_image = $('#file1').attr('value');
      }else{
          exist_web_banner_image = "";
      }

      var exist_web_mobile_banner_image = $('#file2').attr('value');
      if(exist_web_mobile_banner_image != undefined || exist_web_mobile_banner_image != null ){
          exist_web_mobile_banner_image = $('#file2').attr('value');
      }else{
          exist_web_mobile_banner_image = "";
      }

      var exist_mobile_banner_image = $('#file3').attr('value');
      if(exist_mobile_banner_image != undefined || exist_mobile_banner_image != null ){
         exist_mobile_banner_image = $('#file3').attr('value');

      }else{
          exist_mobile_banner_image = "";
      }
       
      // edit time check  exist image   ----end-----

      var ad_banner_name = $('#ad_banner_name').val();
      var selected_restaurant_id= $('#SelectBannerRestaurantInput').attr('selected_rest_id');
    
      var  external_link = $('#external_link').val();
      var  ad_banner_description = $('#ad_banner_description').val();
      
      //check mode 
      var ad_banner_add_edit_mode  = $('#ad_banner_add_edit_mode').val();// 1 for add . 2 for edit
       
       //for edit time ----
      var  edit_ad_banner_id = $('#edit_ad_banner_id').val();

      var check_AdBannerFormData_is_blank = [];
      var AdBannerFormData = new FormData();
     
 
     if(ad_banner_name == ""){
           $('#ad_banner_name').css('border-color','red');
          $('#unfill_banner_name').text('Please Fill Banner Name');
           check_AdBannerFormData_is_blank.push(false);
      }else{
          $('#ad_banner_name').css('border-color','#ccc');
          $('#unfill_banner_name').text('');
           check_AdBannerFormData_is_blank.push(true);
            AdBannerFormData.append('ad_banner_name',ad_banner_name);
      } 

       if(ad_banner_description == ""){
           $('#ad_banner_description').css('border-color','red');
          $('#unfill_description').text('Please Fill Banner Description Name');
           check_AdBannerFormData_is_blank.push(false);
      }else{
          $('#ad_banner_description').css('border-color','#ccc');
          $('#unfill_description').text('');
          check_AdBannerFormData_is_blank.push(true);
           AdBannerFormData.append('ad_banner_description',ad_banner_description);
      } 

      var isRestaurantToogleEnable = $('#check_restaurant_enable').is(':checked');
          if(selected_restaurant_id == "" && isRestaurantToogleEnable == true){
              $('#SelectBannerRestaurantInput').css('border-color','red');
              $('#unselect_restaurant').text('Please Select Restaurant');
               check_AdBannerFormData_is_blank.push(false);
          }else{
              $('#SelectBannerRestaurantInput').css('border-color','#ccc');
              $('#unselect_restaurant').text('');
              check_AdBannerFormData_is_blank.push(true);
               AdBannerFormData.append('selected_restaurant_id',selected_restaurant_id);
          }
       
    var iExternalLinkToogleEnable = $('#check_external_link_enable').is(':checked');
      if(external_link == "" && iExternalLinkToogleEnable == true){
           $('#external_link').css('border-color','red');
          $('#unfill_external_link').text('Please Fill External Link');
           check_AdBannerFormData_is_blank.push(false);
      }else{
          $('#external_link').css('border-color','#ccc');
          $('#unfill_external_link').text('');
          check_AdBannerFormData_is_blank.push(true);
           AdBannerFormData.append('external_link',external_link);
      } 
 
 
        if(web_banner_image.length == 0 && exist_web_banner_image == ""){
          $('#unselect_banner_web_image').text('Please Select banner Image for web');
           check_AdBannerFormData_is_blank.push(false);
        }else{
              
          $('#unselect_banner_web_image').text('');
          check_AdBannerFormData_is_blank.push(true);

        } 

        if(web_mobile_banner_image.length == 0 && exist_web_mobile_banner_image == ""){
          $('#unselect_banner_web_mobile_image').text('Please Select banner Image for web in mobile view');
           check_AdBannerFormData_is_blank.push(false);
        }else{
          $('#unselect_banner_web_mobile_image').text('');
          check_AdBannerFormData_is_blank.push(true);
        } 

         if(mobile_banner_image.length == 0 && exist_mobile_banner_image == ""){
          $('#unselect_banner_mobile_image').text('Please Select banner Image for mobile application');
           check_AdBannerFormData_is_blank.push(false);
        }else{
              
          $('#unselect_banner_mobile_image').text('');
          check_AdBannerFormData_is_blank.push(true);
        } 

         //Edit time checking--------START-----------------
        var upload_web_banner_image_status = false;
        var upload_web_mobile_banner_image_status = false;
        var upload_mobile_banner_image_status = false;


          ///////////////////////////////////
         if(exist_web_banner_image != "" && web_banner_image.length == 0){// pre WEB image 
             
             AdBannerFormData.append('web_banner_image',exist_web_banner_image);
             upload_web_banner_image_status = false;
           }else if(web_banner_image.length > 0){
             
              //web_banner_image = web_banner_image[0];
               AdBannerFormData.append('web_banner_image',web_banner_image[0]);
              upload_web_banner_image_status = true;
           }else{
              upload_web_banner_image_status = false;
           }

            ///////////////////////////////////
           if(exist_web_mobile_banner_image != "" && web_mobile_banner_image.length == 0){// pre WEB MOBILE  image 
            
             AdBannerFormData.append('web_mobile_banner_image',exist_web_mobile_banner_image);
             upload_web_mobile_banner_image_status = false;

           }else if(web_mobile_banner_image.length > 0){
              AdBannerFormData.append('web_mobile_banner_image',web_mobile_banner_image[0]);
              upload_web_mobile_banner_image_status = true;
           }else{
              upload_web_mobile_banner_image_status = false;
           }

           ///////////////////////////////////
          if(exist_mobile_banner_image != "" && mobile_banner_image.length == 0){// pre MOBILE image 
            mobile_banner_image = exist_mobile_banner_image;
             AdBannerFormData.append('mobile_banner_image',exist_mobile_banner_image);
             upload_mobile_banner_image_status = false;

           }else if(mobile_banner_image.length > 0){// pre Mobile BANNER image 
            
              AdBannerFormData.append('mobile_banner_image',mobile_banner_image[0]);
              upload_mobile_banner_image_status = true;
           }else{
              upload_mobile_banner_image_status = false;
           }

           // edit id-----
            AdBannerFormData.append('edit_ad_banner_id',edit_ad_banner_id);
         //Edit time checking------------------END-------------------


      var check_empty_form_value = check_AdBannerFormData_is_blank.includes(false);

      if(check_empty_form_value == false &&  (upload_web_banner_image_status == true || exist_web_banner_image !="") && (upload_web_mobile_banner_image_status == true || exist_web_mobile_banner_image !="")  && (upload_mobile_banner_image_status == true || exist_mobile_banner_image !="")){

     
      //check_empty_form_value value is false
       //its means in array , false does not exist  and its ready to post data to controller
         swal({
                title: 'Wait..',
                text: "Please wait  and Don't do any action while we are processing your request!",
                type: 'Wait',
                buttons: false,
              }); 

          $.ajax({
              url:  BASE_URL+"admin/add_edit_AdBanner/"+ad_banner_add_edit_mode,
              type: 'post',
              data: AdBannerFormData,
              contentType: false,
              processData: false,
             
              success: function(response){
                
                if(response == 1 && ad_banner_add_edit_mode == 1){
                   
                     setTimeout(function(){ 
                          swal('Success', 'Ad Banner added Successfully', 'success');
                      }, 2000);
                      setTimeout(function(){ 
                         window.location.replace(BASE_URL+'admin/ad_banner_list');
                      }, 3000);
                 }

                 if(response == 1 && ad_banner_add_edit_mode == 2){
                   
                     setTimeout(function(){ 
                           swal('Success', 'Ad Banner Updated Successfully', 'success');
                      }, 2000);
                      setTimeout(function(){ 
                         window.location.replace(BASE_URL+'admin/ad_banner_list');
                      }, 3000);
                 }

                 if(response == 2 && ad_banner_add_edit_mode == 2){
                   // swal('Dont Worry...', 'Nothing Changed!');
                 }

                 if(response == 6){
                    swal('Oops...', 'Internal server error', 'error');
                 }

                 if(response == 5){
                   swal('Sorry...', 'This Restaurant have Banner already you can edit!', 'error');
                 }

                 if(response == 3){
                    swal("Oops...", "Ad banner is already exists for Selected Restaurant. You can edit!");
                 }

                   if(response == 4 ){
                    swal('Sorry...', 'May some be missing some field or Internal server error!', 'error');
                   }
              },
           });//ajax end

      }else{
         //its means in array , false is exist  and its not ready to post data to controller
         // swal('Oops...', 'You are missing a required field', 'error');
     }

});
// Add / Edit Ad Banner -----------------------------END--------------------

//Delete Ad Banner -----------------------START---------------------
$('body').on('click', '.ad_banner_delete', function() {

      var new_ad_banner_table_url = ad_banner_table_url.replace("table","2");// if action mode then it will repalce table to  2 // it is becouse when delete success then it will give new url which will comapre segment with serach filter

      var ad_banner_input_id  = $(this).attr('id');//input id

      var ad_banner_input_id_array = ad_banner_input_id.split("_");
      var ad_banner_id = ad_banner_input_id_array[2];

      var ad_banner_name_id = '#ad_banner_name_'+ad_banner_id;
      var ad_banner_name = $(ad_banner_name_id).text();

        swal({
            title: "Are you sure to delete this Ad Banner permanently?",
            text: "Once deleted, You will not be able to recover the action!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              
                  // Ajax-------SATRT------------
                  
                    $.ajax({
                    url: BASE_URL+'admin/delete_ad_banner',
                    data: { 
                        ad_banner_id: ad_banner_id
                    },
                    type: 'post',
                    success: function(response){
                      
                        if(response == 1){
                         
                        /* swal("Poof! Your imaginary file has been deleted!", {
                            icon: "success",
                          });*/

                       /*  setTimeout(function(){
                               $( "#ad_banners_table" ).load(new_ad_banner_table_url);
                            }, 2000); //refresh every 2 seconds*/
                             $( "#ad_banners_table" ).load(new_ad_banner_table_url);
                         }
                       
                       if(response == 0){
                          swal('Oops...', 'Internal server error', 'error');
                       }
                    },
                    
                });
                // Ajax-------END------------
            } 
          });
      
});
//Delete Ad Banner-----------------------END----------------------

//  Ad Banner (Enable / Disable)-----------------------START---------------------
$('body').on('click', '.ad_banner_status', function() {

      var new_ad_banner_table_url = ad_banner_table_url.replace("table","1");// if action mode then it will repalce table to  2 // it is becouse when _status success then it will give new url which will comapre segment with serach filter

      var ad_banner_input_id  = $(this).attr('id');//input id

      var ad_banner_input_id_array = ad_banner_input_id.split("_");
      var ad_banner_id = ad_banner_input_id_array[3];
 
      var ad_banner_name_id = '#ad_banner_name_'+ad_banner_id;
    
      var ad_banner_name = $(ad_banner_name_id).text();

      var status_value = $('#ad_banner_status_'+ad_banner_id).val();

         // Ajax-------SATRT------------
            $.ajax({
            url: BASE_URL+'admin/enable_disable_ad_banner',
            data: { 
                ad_banner_id: ad_banner_id,status_value:status_value
            },
            type: 'post',
            success: function(response){
              
                if(response == 1){
                   //success Status Has been Changed
                   $( "#ad_banners_table" ).load(new_ad_banner_table_url);
                 }
               
               if(response == 0){
                  swal('Oops...', 'Internal server error', 'error');
               }
            },
            
        });
        // Ajax-------END------------
});

//  Ad Banner (Enable / Disable)------------------------END----------------------

//Ad banner filter----------------------START----------------------------
$(".search_key").keydown(function(e){
      if(e.keyCode === 13)
      {
        $('#search_ad_banner_list_data').trigger( "click");
      }
  });
$('body').on('click', '#search_ad_banner_list_data', function() {
      var from_date = $('#fromdate').val();
      var to_date = $('#todate').val();
      var ad_banner_status_search = $('.ad_banner_status_search').val();
      var search_restaurant_id =$('#search_restaurant_id').val();
      var search =$('.search_key').val();
      
      if(from_date == '')
      {
         from_date= "all";
      }
      if(to_date==''){
         to_date= "all";
      }

      if(ad_banner_status_search==''){
         ad_banner_status_search= "all";
      }

      if(search_restaurant_id==''){
         search_restaurant_id= "all";
      }

      if(search == '')
      {
          search = "all";
      }else{
           search = search.trim();
      }

    if(from_date!="all" || to_date!="all" || ad_banner_status_search!="all" || search_restaurant_id!="all"  || search != "all"){
        
             window.location.replace(BASE_URL+'admin/ad_banner_list/0/'+from_date+'/'+to_date+'/'+ad_banner_status_search+'/'+search_restaurant_id+'/'+search+'/');//load only table on a promo_code_taurant page 
      }else{
            window.location.replace(BASE_URL+'admin/ad_banner_list/');
      }
  });

//Ad banner filter-----------------------END----------------------------

//User search filter----------------------START----------------------------
$(".search_key").keydown(function(e){//on enter
      if(e.keyCode === 13)
      {
        $('#search_user_list_data').trigger( "click");
      }
  });

$('body').on('click', '#search_user_list_data', function() {
      var from_date = $('#fromdate').val();
      var to_date = $('#todate').val();
      var userstatus = $('.userstatus').val();
      var search =$('.search_key').val();
      
      if(from_date == '')
      {
         from_date= "all";
      }
      if(to_date==''){
         to_date= "all";
      }

      if(userstatus==''){
         userstatus= "all";
      }
      if(search == '')
      {
          search = "all";
      }else{
           search = search.trim();
      }

    if(from_date!="all" || to_date!="all" || userstatus!="all" || search != "all"){
        
             window.location.replace(BASE_URL+'admin/AllUser/'+selected_user_role+'/0/'+from_date+'/'+to_date+'/'+userstatus+'/'+search+'/');//load only table on a promo_code_taurant page 
      }else{
            window.location.replace(BASE_URL+'admin/AllUser/'+selected_user_role+'/');
      }
  });

//User search filter-----------------------END----------------------------

//Export User Csv -------START---------------
 $('body').on('click', '.export_user_csv', function() {
 
      var from_date = $('#fromdate').val();
      var to_date = $('#todate').val();
      var userstatus = $('.userstatus').val();
      var search =$('.search_key').val();
      
      if(from_date == '')
      {
         from_date= "all";
      }
      if(to_date==''){
         to_date= "all";
      }

      if(userstatus==''){
         userstatus= "all";
      }
      if(search == '')
      {
          search = "all";
      }else{
           search = search.trim();
      }

        if(from_date!="all" || to_date!="all" || userstatus!="all" ||  search!="all"){
            window.location.replace(BASE_URL+'admin/exportUserCSV/'+selected_user_role+'/'+from_date+'/'+to_date+'/'+userstatus+'/'+search+'/');
        }else{
            window.location.replace(BASE_URL+'admin/exportUserCSV/'+selected_user_role);
        }
    });
 
//Export User Csv -------END---------------
 
//Submit Super Account Setting -------------------------START-----------------------
$('body').on('click', '#account_setting_submit', function() {

    var upload_image_status = false;// image upload or not (this is use for edit time)

    
    var fullname = $("#fullname").val();
    var email = $("#profile_email_valid").val();
    var mobile = $("#mobile").val();
    var address = $("#address").val();

    //edit time 
    var edit_exist_image = $('#file').attr('value');
   
     var fd = new FormData();
     var files = $('#file')[0].files;

     if(files.length == 0 && edit_exist_image == ""){
          $('#unfill_image').text('Please Select Merchant Image');
      }else{
          
           $('#unfill_image').text('');
      }
     
     if(fullname == ""){
          $('#fullname').css('border-color','red');
          $('#unfill_name').text('Please Fill Fullname');
      }else{
          $('#fullname').css('border-color','#ccc');
          $('#unfill_name').text('');
      }
 
       if( email != ""){
            //edit time mode email check on click
             var regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;    
              if(!regex.test(email)){    
                  
                  email_status = false;
                 return regex.test(email);    
              }  else{

                  email_status = true;
              }
        }

          if(email == "" || email_status == false){
              $('#email').css('border-color','red');
              $('#profile_invalid_email').text('Please Fill Valid  Email ID');
          }else{
               $('#email').css('border-color','#ccc');
               $('#profile_invalid_email').text('');
          }    

          if(mobile == ""){
              $('#mobile').css('border-color','red');
              $('#invalid_phone').text('Please Fill  Contact Number');
          }else{
              $('#mobile').css('border-color','#ccc');
              $('#invalid_phone').text('');
          }

           if(address == ""){
              $('#address').css('border-color','red');
              $('#unfill_address').text('Please Fill  Address');
          }else{
              $('#address').css('border-color','#ccc');
              $('#unfill_address').text('');
          }
         

        if(edit_exist_image != "" && files.length == 0){// pre image 
           fd.append('edit_exist_image',edit_exist_image);
           upload_image_status = false;


         }else if(files.length > 0){
          
           fd.append('admin_profile_image',files[0]);
            upload_image_status = true;
         }else{
            upload_image_status = false;
         }
       

        // Check file selected or not
        if(fullname != "" && email != ""  && mobile != "" && address!= "" && email_status == true && (upload_image_status == true || edit_exist_image !="")){
            
             swal({
                  title: 'Wait..',
                  text: "Please wait  and Don't do any action while we are processing your request!",
                  type: 'Wait',
                  buttons: false,
                });
          
           fd.append('fullname',fullname);
           fd.append('email',email);
           fd.append('mobile',mobile);
           fd.append('address',address);
           

           $.ajax({
              url:  BASE_URL+'admin/update_admin_account_settings/',
              type: 'post',
              data: fd,
              contentType: false,
              processData: false,
              success: function(response){

                 if(response == 1){
                     swal('Success', 'Account Setting Updated Successfully', 'success');
                     setTimeout(function(){ 
                         window.location.replace(BASE_URL+'admin/setting/2');
                      }, 2000);
                 }

                 if(response == 2){
                    swal("Oops...", "Email id   already exists!");
                 }

                 if(response == 3){
                   //swal("Nothing has changed!");
                 }

                   if(response == 0){
                   swal('Oops...', 'Something went wrong!', 'error');
                 }


                 if(response == 6){
                     swal('Sorry...', 'May some be missing some field or Internal server error!', 'error');
                 }
              },
           });//ajax end
        }else{
            swal('Oops...', 'You are missing a required field ', 'error');
        }
});
//Submit Super Account Setting ------------------END---------------------------

// Merchant add and edit details-------------------START---------------------------

$( document ).ready(function() {
    setTimeout(function(){
       $( ".closebtn" ).trigger('click');
       
    }, 3000); //refresh every 3 seconds
});

 //delete/remove  selected image if dont want to upload

 //if image seleted then delete button will be visible

/*
$('body').on('click', '.addimageplus', function() {
     if ($('#file')[0].files.length === 0) {
               alert('no file selected');
            } else {
               alert('Some file is selected');
               $('.delete_selected_cat_img').removeClass('d-none');
            }
});*/
   $('body').on('click', '.delete_selected_cat_img', function() {
        $('#disp_img').attr('src',BASE_URL+'assets/images/mr_merchant_pic.png');
        $(this).closest("div.img-add").find("input[type='file']").val('');
        $('#edit_exist_image').val('');
   });
$('body').on('click', '#merchant_submit', function() {

    var upload_image_status = false;// image upload or not (this is use for edit time)

    var mode_type = $("#type").val(); // type 1 = add, 2 = edit mode
    var fullname = $("#fullname").val();
    var email = $("#email").val();
    var mobile = $("#mobile").val();
    var merchant_id = $("#user_id").val(); // user id and merchant id is same
    
     var fd = new FormData();
     var files = $('#file')[0].files;

     /*if(files.length == 0 && edit_exist_image == ""){
          $('#unfill_image').text('Please Select Merchant Image');
      }else{
          
           $('#unfill_image').text('');
      }*/
     
     if(fullname == ""){
          $('#fullname').css('border-color','red');
          $('#unfill_name').text('Please Fill Merchant Full Name');
      }else{
          $('#fullname').css('border-color','#ccc');
          $('#unfill_name').text('');
      }

       if( mode_type == 2){
            //edit time mode email check on click
             var regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;    
              if(!regex.test(email)){    
                  
                  email_status = false;
                 return regex.test(email);    
              }  else{

                  email_status = true;
              }
        }

          if(email == "" || email_status == false){
              $('#email').css('border-color','red');
              $('#invalid_email').text('Please Fill Valid Merchant Email ID');
          }else{
               $('#email').css('border-color','#ccc');
               $('#invalid_email').text('');
          }    

          if(mobile == ""){
              $('#mobile').css('border-color','red');
              $('#invalid_phone').text('Please Fill Merchant Contact Number');
          }else{
              $('#mobile').css('border-color','#ccc');
              $('#invalid_phone').text('');
          }
          if(merchant_id != "" &&  mode_type == 2){
               user_id = merchant_id
          }else{
               user_id = "";
          }

        var edit_exist_image = $('#edit_exist_image').val();

        if(edit_exist_image != "" && files.length == 0){// pre image 
           fd.append('merchant_profile_image',edit_exist_image);
           upload_image_status = false;


         }else if(files.length > 0){
          
           fd.append('merchant_profile_image',files[0]);
            upload_image_status = true;
         }else{
            upload_image_status = true;
         }
       

        // Check file selected or not
        if(fullname != "" && email != ""  && mobile != "" && email_status == true && (upload_image_status == true || edit_exist_image !="")){
            
             swal({
                  title: 'Wait..',
                  text: "Please wait  and Don't do any action while we are processing your request!",
                  type: 'Wait',
                  buttons: false,
                });
          
           fd.append('fullname',fullname);
           fd.append('email',email);
           fd.append('mobile',mobile);
           fd.append('user_id',user_id);
           

           $.ajax({
              url:  BASE_URL+'admin/add_edit_merchant_details/'+mode_type+'/',
              type: 'post',
              data: fd,
              contentType: false,
              processData: false,
              success: function(response){
                
                 if(response == 1 && mode_type == 1){
                     //swal('Success', 'Merchant added Successfully', 'success');
                     window.location.replace(BASE_URL+'admin/AllUser/2');
                 }

                 /*if(response == 0 && mode_type == 1){
                     swal('Success', 'Merchant added Successfully but Mail could not be sent. Make sure your email ID is correct.', 'success');
                     setTimeout(function(){ 
                         window.location.replace(BASE_URL+'admin/AllUser/2');
                      }, 4000);
                 }*/

                 if(response == 1 && mode_type == 2){
                     //swal('Success', 'Merchant Updated Successfully', 'success');
                    window.location.replace(BASE_URL+'admin/AllUser/2');
                 }

                 if(response == 2){
                    swal("Oops...", "User is already exists!");
                 }

                 if(response == 4){
                   swal('Oops...', 'Something went wrong!', 'error');
                 }

                 if(response == 5){
                  swal('Oops...', 'Internal server error', 'error');
                 }

              },
           });//ajax end
        }else{
            swal('Oops...', 'You are missing a required field ', 'error');
        }
});
// Merchant add and edit details-------------------END---------------------------

//Change status USER (Customer/Merchant) -----------------------START---------------------
$('body').on('change', '.user_status', function() {

      var new_user_table_url = user_table_url.replace("table","2");// if action mode then it will repalce table to  2 // it is becouse when delete success then it will give new url which will comapre segment with serach filter
      ////by default table value is 0
     
      var user_input_name  = $(this).attr('name');
      var user_input_id  = $(this).attr('id');//input id

      // var user_status_value = $('#'+user_input_id).val();
      var user_status_value = $(this).val();
      // alert(user_status_value)
      var user_input_id_array = user_input_name.split("_");
      var user_id = user_input_id_array[2];

      var user_name_id = '#user_name_'+user_id;
      var user_name = $(user_name_id).text();

         // Ajax-------SATRT------------
            $.ajax({
            url: user_edit_status_url,
            data: { 
                user_id: user_id,user_status_value:user_status_value
            },
            type: 'post',
            success: function(response){
              
                if(response == 1){
                     //success
                     $( "#all_users_table" ).load(new_user_table_url);
                 }
               
               if(response == 0){
                  swal('Oops...', 'Internal server error', 'error');
               }
            },
            
        });
        // Ajax-------END------------
});

///Change status USER  (Customer/Merchant)-----------------------END----------------------

//Delete USER (Customer/Merchant) -----------------------START---------------------
$('body').on('click', '.user_delete', function() {

      var new_user_table_url = user_table_url.replace("table","2");// if action mode then it will repalce table to  2 // it is becouse when delete success then it will give new url which will comapre segment with serach filter

      var user_input_id  = $(this).attr('id');//input id

      var user_input_id_array = user_input_id.split("_");
      var user_id = user_input_id_array[2];

      var user_name_id = '#user_name_'+user_id;
      var user_name = $(user_name_id).text();

        swal({
            title: "Are you sure to delete this user permanently?",
            text: "Once deleted, You will not be able to recover the action!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              
                  // Ajax-------SATRT------------
                  
                    $.ajax({
                    url: user_delete_url,
                    data: { 
                        user_id: user_id
                    },
                    type: 'post',
                    success: function(response){
                      
                        if(response == 1){
                         
                         /* swal("Poof! Your imaginary file has been deleted!", {
                            icon: "success",
                          });*/

                           $( "#all_users_table" ).load(new_user_table_url);
                         }
                       
                       if(response == 0){
                          swal('Oops...', 'Internal server error', 'error');
                       }
                    },
                    
                });
                // Ajax-------END------------
            } 
          });
      
});

//Delete USER (Customer/Merchant)-----------------------END----------------------

//get latitude and long lontitude from Customer street(pin address)
  $('body').on('click', '#get_customer_lat_long', function() {
        var pin_address = $('#pin_address').val();

        var getLocation =  function(address) {
          var geocoder = new google.maps.Geocoder();
          geocoder.geocode( { 'address': address}, function(results, status) {

          if (status == google.maps.GeocoderStatus.OK) {
              var latitude = results[0].geometry.location.lat();
              var longitude = results[0].geometry.location.lng();
              $('#customer_latitude').val(latitude);
              $('#customer_longtitude').val(longitude);
              //console.log(latitude, longitude);
              } 
          }); 
        }
        getLocation(pin_address);
        setTimeout(function(){  $('#customer_edit_submit').trigger('click'); }, 1000);
  });
//Customer edit submit--------START-----------

$('body').on('click', '#customer_edit_submit', function() {
   
  
    var fullname = $("#customer_name").val();
    var email = $("#customer_email").val();
    var mobile = $("#phone").val();
    var customer_id = $("#customer_id").val(); // user id and merchant id is same
    var customer_pin_address =  $("#pin_address").val();
    var unit_number =  $("#unit_number").val();
    var postal_code =  $("#postal_code").val();
    var customer_latitude =  $("#customer_latitude").val();
    var customer_longtitude =  $("#customer_longtitude").val();
    
     
     if(fullname == ""){
          $('#customer_name').css('border-color','red');
          $('#unfill_name').text('Please Fill Merchant First Name');
      }else{
          $('#customer_name').css('border-color','#ccc');
          $('#unfill_name').text('');
      }
        
        if(email != ""){
           var regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;    
              if(!regex.test(email)){    
                  
                  email_status = false;
                 return regex.test(email);    
              }  else{

                  email_status = true;
              }
        }

          if(email == "" || email_status == false ){
              $('#customer_email').css('border-color','red');
              $('#invalid_email').text('Please Fill Valid Merchant Email ID');
          }else{
               $('#customer_email').css('border-color','#ccc');
               $('#invalid_email').text('');
          }    

          if(mobile == ""){
              $('#phone').css('border-color','red');
              $('#invalid_phone').text('Please Fill Merchant Contact Number');
          }else{
              $('#phone').css('border-color','#ccc');
              $('#invalid_phone').text('');
          }

          if(customer_id != ""){
               user_id = customer_id
          }else{
               user_id = "";
          }

        if(customer_pin_address == ""){
          $('#pin_address').css('border-color','red');
          $('#unfill_pin_address').text('Please Fill Customer Street Address');
        }else{
          $('#pin_address').css('border-color','#ccc');
          $('#unfill_pin_address').text('');
        }

        if(unit_number == ""){
          $('#unit_number').css('border-color','red');
          $('#unfill_unit_number').text('Please Fill Unit Number');
        }else{
          $('#unit_number').css('border-color','#ccc');
          $('#unfill_unit_number').text('');
        }

        if(postal_code == ""){
          $('#postal_code').css('border-color','red');
          $('#unfill_postal_code').text('Please Fill Postal Code');
        }else{
          $('#postal_code').css('border-color','#ccc');
          $('#unfill_postal_code').text('');
        }
       
       

        // Check file selected or not
        if(fullname != "" && email != ""  && mobile != "" && email_status == true && customer_pin_address !="" && unit_number !="" && postal_code !="" && customer_latitude !="" && customer_longtitude != ""){
           
           $.ajax({
              url:  BASE_URL+'admin/edit_customer_details/',
              type: 'post',
              data: {fullname :fullname , email:email, mobile:mobile, user_id:user_id,customer_pin_address:customer_pin_address,unit_number:unit_number,postal_code:postal_code,customer_latitude:customer_latitude,customer_longtitude:customer_longtitude},
             
              success: function(response){

                 if(response == 1){
                    //swal('Success', 'Customer ('+fullname+') Updated Successfully', 'success');
                     window.location.href= ''+BASE_URL+'admin/AllUser/3/';
                 }

                 if(response == 3){
                    swal("Oops...", "User is already exists!");
                 }
                  
                 if(response == 2){
                  swal('Oops...', 'Internal server error', 'error');
                 }

              },
           });
        }else{
            //swal('Oops...', 'You are missing a required field ', 'error');
        }
});
//Customer edit submit--------END-----------

//geting customer id and nubmer id
$('body').on('click', '.customer_id_for_add_less_money', function() {
    var customer_id = $(this).attr('user_id');
    var user_number_id = $(this).attr('user_number_id');

    $('#customer_id_for_add_amount').val(customer_id);
    $('#customer_id_for_deduct_amount').val(customer_id);
    $('.customer_number_id').text(user_number_id);
});
//Customer Money add in wallet ------------------START---------------
//add money -----------------
$('body').on('click', '#customer_credit_amount_submit', function() {
    // alert(1111111);
      var new_user_table_url = user_table_url.replace("table","2");// if action mode then it will repalce table to  2 // it is becouse when delete success then it will give new url which will comapre segment with serach filter

       var customer_id = $("#customer_id_for_add_amount").val();
       var customer_credit_amount = $("#customer_credit_amount").val();

        if(customer_credit_amount == ""){
            $('#customer_credit_amount').css('border-color','red');
            $('#unfill_customer_credit_amount').text('Please Fill Amount');
        }else{
            $('#customer_credit_amount').css('border-color','#ccc');
            $('#unfill_customer_credit_amount').text('');
        }
 
        if(customer_id != "" && customer_credit_amount != ""){
           
           $("#customer_credit_amount_submit").attr('disabled','disabled');

           $.ajax({
              url:  BASE_URL+'admin/add_money_in_customer_wallet/',
              type: 'post',
              data: {customer_id :customer_id , customer_credit_amount:customer_credit_amount},
             
              success: function(response){

                 if(response == 1){
                   // swal('Success', 'Money Added Successfully!', 'success');
                     swal({
                        title: "Success",
                        icon: "success",
                        text: "Money Added Successfully!",
                        timer: 2000,
                        showConfirmButton: true
                      });
                     $('#customer_credit_amount_submit').removeAttr('disabled');
                     setTimeout(function(){ 
                            $('#add_money_modal').modal('hide');
                            $('#add_money_modal').find("input").val('').end();
                            $('.customer_number_id').text();
                            $( "#all_users_table" ).load(new_user_table_url);
                        }, 500);
                    
                 }
                  
                 if(response == 2 || response == 0){
                   swal('Oops...', 'Internal server error', 'error');
                 }

              },
           });
        }else{
            //swal('Oops...', 'You are missing a required field ', 'error');
        }
});

//Customer Money add in wallet ------------------END---------------

//Customer Money deduct from wallet ------------------START---------------
//add money -----------------
$('body').on('click', '#customer_debit_amount_submit', function() {
      var new_user_table_url = user_table_url.replace("table","2");// if action mode then it will repalce table to  2 // it is becouse when delete success then it will give new url which will comapre segment with serach filter

       var customer_id = $("#customer_id_for_deduct_amount").val();
       var customer_debit_amount = $("#customer_debit_amount").val();

        if(customer_debit_amount == ""){
            $('#customer_debit_amount').css('border-color','red');
            $('#unfill_customer_debit_amount').text('Please Fill Amount');
        }else{
            $('#customer_debit_amount').css('border-color','#ccc');
            $('#unfill_customer_debit_amount').text('');
        }
 
        if(customer_id != "" && customer_debit_amount != ""){
           $("#customer_debit_amount_submit").attr('disabled','disabled');
           $.ajax({
              url:  BASE_URL+'admin/deduct_money_from_customer_wallet/',
              type: 'post',
              data: {customer_id :customer_id , customer_debit_amount:customer_debit_amount},
             
              success: function(response){

                 if(response == 1){
                    //swal('Success', 'Money Deducted Successfully!', 'success');
                    swal({
                        title: "Success",
                        icon: "success",
                        text: "Money Deducted Successfully!",
                        timer: 2000,
                        showConfirmButton: true
                      });
                    $('#customer_debit_amount_submit').removeAttr('disabled');
                    setTimeout(function(){ 
                        $('#deduct_money_modal').modal('hide');
                        $('#deduct_money_modal').find("input").val('').end();
                        $('.customer_number_id').text();
                        $( "#all_users_table" ).load(new_user_table_url);
                    });
                 }
                  
                 if(response == 2 || response == 0){
                   swal('Oops...', 'Internal server error', 'error');
                 }
              },
           });
        }else{
            //swal('Oops...', 'You are missing a required field ', 'error');
        }
});

//Customer Money deduct from wallet ------------------END---------------

//Products Restaurant Select box on change- product and category list change --START--

$(document).ready(function(){


    // For restaurant selection on products page------------start-----
    $('#restaurant_list').trigger('click');

    var selected_restaurant_id = $('#restaurant_list').children("option:selected").val();
    var selected_restaurant_name = $('#restaurant_list').children("option:selected").text();
     
    $('.selected_restaurant_name').text(selected_restaurant_name);
      

    $('#selected_restaurant_id').val(selected_restaurant_id);
      // For restaurant selection on products page------------end-----



    // For category selection on products page------------start-----
    $('#category_list').trigger('click');

    var selected_category_id = $('#category_list').children("option:selected").val();
    var selected_category_name = $('#category_list').children("option:selected").text();

    if(selected_category_id == ""){
        selected_category_name_error = "You cannot create a product because you do not have a category in the selected restaurant. Please create a category first.";
        $('#tab_product').addClass('disabled');
        $('#tab_product').css('pointer-events','none');
        //$('#tab_combined').addClass('disabled');
       // $('#tab_combined').css('pointer-events','none');
        $('#product_submit').attr('disabled','disabled');

    }else{
      selected_category_name_error = "";
       $('#product_submit').removeAttr('disabled');
    }
   
     
    $('.selected_category_name').text(selected_category_name);
    $('.null_category_error').text(selected_category_name_error);
      

    $('#selected_category_id').val(selected_category_id);
    // For category selection on products page------------end-----

      
    /* $('html, body').animate({
            scrollTop: $("#restaurant_list").offset().top
        }, 1000);
      */
    var product_page_url   = window.location.href;  
    
    var url_part = product_page_url.split('/'+selected_restaurant_id+'/');
    var old_cat_id = url_part[url_part.length-1];
   
 
});

$(document).ready(function(){

  function category_select_box() {
       // $('#search_product_data').trigger('click');
      var restaurant_id = $('#restaurant_list').val();
      if(restaurant_id === undefined || restaurant_id === null){// merchant and role 2 logged in
          restaurant_id = selected_restaurant_id;
      }else{// super admin is logged in
         restaurant_id = $('#restaurant_list').val();
      }
       
       var handle_mode = 2;//js_mode
    

      $('#category_list').empty();
      $('#category_list').html('<option value="">Select Category</option>');
        $.ajax({
            url: BASE_URL+'admin/show_category_according_selected_restaurant/'+restaurant_id+'/'+handle_mode,//merchant detail
           
            type: 'post',
            success: function(response){
              if(response != 0){
                 var json = JSON.parse(response);
               
                   var selectbox;
                   var selected;
                   var count = 1;
                   $.each(json,function(index,json){
                    
                        if(count == 1){
                            selected = "selected";
                        }else{
                             selected = "";
                        }
                        
                       selectbox =  $('#category_list').append("<option value="+ json.category_id+" "+selected+">"+json.category_name+"</option>");
                       count++;
                  });  

                  return selectbox;
              }
            },
        });
    }

    $('body').on('change', '#restaurant_list', function() {
        $(document).ajaxComplete(function(){
          $('#search_product_data').trigger('click');
        });
        category_select_box();
    });

    $('body').on('change', '#category_list', function() {
        $('#search_product_data').trigger('click');
    });

    var product_search_by_tr_click = "";
    $('body').on('click', '.category_list_by_tr', function() {//if click on category  tr
        product_search_by_tr_click =  $(this).attr('category_id_tr');
        $('#search_product_data').trigger('click');
    });


  //Products Search filter --------------------START----------------
  $(".search_key").keydown(function(e){//on enter
      if(e.keyCode === 13)
      {
        $('#search_product_data').trigger( "click");
      }
  });
    $('body').on('click', '#search_product_data', function() {

         //same changes will be in #restaurant_list on change
        var from_date = $('#fromdate').val();
        var to_date = $('#todate').val();
        var product_status = $('#products_status').val();
        var product_food_type = $('#product_food_type').val();
        var search =$('.search_key').val();

        var restaurant_id =$('#restaurant_list').val();
        if(restaurant_id === undefined || restaurant_id === null){// merchant and role 2 logged in
            restaurant_id = selected_restaurant_id;
        }else{// super admin is logged in
           restaurant_id = $('#restaurant_list').val();
        }
       
        if(product_search_by_tr_click != ""){
             var category_id = product_search_by_tr_click;//if admin click on category tr
        }else{
             var category_id =$('#category_list').val();// if admin click on category dropdown
        }
        
        if(from_date == '')
        {
           from_date= "all";
        }
        if(to_date==''){
           to_date= "all";
        }

        if(product_status==''){
           product_status= "all";
        }

        if(product_food_type==''){
           product_food_type= "all";
        }

        if(search == '')
        {
            search = "all";
        }else{

             search = search.trim();
        }

        if(restaurant_id ==''){
           restaurant_id = 0;
        }

        if(category_id ==''){
           category_id = 0;
        }

      if(from_date!="all" || to_date!="all" || product_status!="all" | product_food_type!="all"  || search != "all" || restaurant_id != 0 || category_id != 0){
            
              window.location.replace(BASE_URL+'admin/products/0/'+from_date+'/'+to_date+'/'+product_status+'/'+product_food_type+'/'+search+'/'+restaurant_id+'/'+category_id+'/');
        }else{
            
              window.location.replace(BASE_URL+'admin/products/');
        }
    });
  //Products Restaurant Select box on change- product and category list change ---End---
   // });
  //Products Search filter --------------------END--------0---------

  //product status change ----Enable/Disable-----------------START-------------

  $('body').on('change', '.product_status', function() {
      //selected product id , which you want to go online /offline
        var products_input_id  = $(this).attr('id');//input id
        $('#offline_mode_id').val(products_input_id);
        
        // same  modal use for category and product offline online 
        //  differentiate id with submit id , it will change when click on category toggle same as product toggle for disable(offline)
        $('.offline_online_category_or_product_submit').attr('id','product_online_offline_save');

        // checking of offline values are exist in products_offline table then we need to show data in modal
        var offline_value_exist_status  = $(this).attr('offline_value_exist_status');
        var status_mode_type  = $(this).attr('status_mode_type');
        
         if(offline_value_exist_status == 1 && status_mode_type == 2){

            var product_input_id_array = products_input_id.split("_");
            var product_id = product_input_id_array[2];
            // need to update value
            //alert('hh'+product_id);

            //geting product offline data -------------START-------
            $.ajax({
                    url: BASE_URL+'admin/GET_ProductOffline_Data/',//merchant detail
                    data: { 
                        product_id: product_id,
                    },
                    type: 'post',
                    success: function(response){
                      if(response != 0){
                         var data = JSON.parse(response);
                          $('#service_provider_fullname').val(data.fullname);
                           
                        }
                    },
                    
                });
            //geting product offline data -------------END--------
         }

        
  });
  $('body').on('click', '.if_category_is_disable', function() {
     // check category is disable (if this class has disabled means related category is offline, than user cant it enable or disable )
        var is_category_offline = $('.product_status').attr('disabled');
        if (typeof is_category_offline !== typeof undefined && is_category_offline !== false) {
            swal('Sorry...', 'You cannot do products online because the category is currently offline', 'warning');
        }
  });
  // When category is offline then user cant add product on that particular category
  //------------START--------------
  $('body').on('click', '#disable_add_product_btn', function() {
    swal('Sorry...', 'You cannot Add product because the category is not available or may be currently offline', 'warning');
  });
  // When category is offline then user cant add product on that particular category
   //------------END--------------
  $('body').on('click', '#product_online_offline_save, .product_status', function() {

        var offline_tag = $('#offline_tag').val();
       
        var restaurant_id = $('#restaurant_list').val();

          if(restaurant_id === undefined || restaurant_id === null){// merchant and role 2 logged in
              restaurant_id = selected_restaurant_id;
          }else{// super admin is logged in
             restaurant_id = $('#restaurant_list').val();
          }


      //selected Product id which you want to go online /offline
       if($(event.target).is('.product_status')) {//check wich element is clicked
            //alert(event.target.id + ' was clicked.');
            product_input_id = event.target.id;
            $('.enable_disable_product_category_title').text('Disable Product');// same  modal use for product offline online
            
        }else{
            var product_input_id  = $('#offline_mode_id').val();//input id
        }

       
       
        var product_input_id_array = product_input_id.split("_");
        var product_id = product_input_id_array[2];
        var product_name_id = '#product_name_'+product_id;

        var product_name = $(product_name_id).text();

        //offline_type 1 - DISABLE 2 - ENABLE
        var status_mode_type = $('#'+product_input_id).attr('status_mode_type');
        var offline_type = status_mode_type;//prodcut status - if its 1 then it will go for offline, if its 2 then it will go online 


        // geting final offline value ------
         
        if(offline_tag == 1){
           var offline_tag_value = $('#hours_offline_value').val();

            //required fields
            if(offline_tag_value == ""){
               $('#hours_offline_value').css('border-color','red');
               $('#unfill_hours_offline_value').text('Please Select Time');
            }else{
               $('#hours_offline_value').css('border-color','#ccc');
               $('#unfill_hours_offline_value').text('');
            }

        }else  if(offline_tag == 2){

           var days_offline_fromdate = $('#days_offline_fromdate').val();
          
           var offline_tag_value = days_offline_fromdate;

          //required fields
           if(offline_tag_value == ""){
               $('#days_offline_fromdate').css('border-color','red');
               $('#unfill_days_offline_fromdate').text('Please Select Day');
            }else{
               $('#days_offline_fromdate').css('border-color','#ccc');
               $('#unfill_days_offline_fromdate').text('');
            }

        }else  if(offline_tag == 3){
           //from date and time
           var offline_product_fromdate = $('#offline_product_category_fromdate').val();

          //till date and time
           var offline_product_tilldate = $('#offline_product_category_tilldate').val();
          

            //required fields--------------start-----------
             if(offline_product_fromdate == ""){
                 $('#offline_product_category_fromdate').css('border-color','red');
                 $('#unfill_offline_product_category_fromdate').text('Please Select Day');
              }else{
                 $('#offline_product_category_fromdate').css('border-color','#ccc');
                 $('#unfill_offline_product_category_fromdate').text('');
              }

             if(offline_product_tilldate == ""){
                 $('#offline_product_category_tilldate').css('border-color','red');
                 $('#unfill_offline_product_category_tilldate').text('Please Select Day');
              }else{
                 $('#offline_product_category_tilldate').css('border-color','#ccc');
                 $('#unfill_offline_product_category_tilldate').text('');
              }
               //required fields--------------end-----------

               //alert(offline_product_tilldate);

              if(offline_product_fromdate != "" && offline_product_tilldate != ""){
                  var offline_tag_value = offline_product_fromdate+','+offline_product_tilldate;
              }else{
                  var offline_tag_value = "";
              }
        }
        
        if(offline_tag == ""){
           $('#unselect_offline_tag').text('Please Select Any Tag');
        }else{
           
           $('#unselect_offline_tag').text('');
        }


         var new_product_table_url = product_table_url.replace("table","1");// if action mode enable disable then it will repalce table to  1 same as  2 in delete case

  
         if((offline_tag != "" && offline_tag_value != "" && product_id != "" && restaurant_id != "" && offline_type != "" && offline_type == 1) || (offline_type == 2)){

           $.ajax({
                url: product_active_inactive_url,
                data: { 
                   
                    offline_tag:offline_tag,
                    offline_value:offline_tag_value, 
                    product_id: product_id,
                    restaurant_id:restaurant_id,
                    offline_type:offline_type
                },
                type: 'post',
                success: function(response){
                   if(response == 1){

                       //after resopose have to must reset data of this modal--start--
                       $('#offline_tag').val('1');//default
                       $('#offline_mode_id').val('');

                       $('#days_offline_fromdate').val('');
                       $('#hours_offline_value').val('');
                       $('#offline_product_category_fromdate').val('');
                       $('#offline_product_fromtime').val('00:00:00');
                       $('#offline_product_category_tilldate').val('');
                       $('#offline_product_tilltime').val('11:59:59');
                        //after resopose have to must reset data of this modal--end---

                          var load =  $( "#product_and_category_list_table" ).load(new_product_table_url);

                            if(offline_type == 2){
                               $('#'+product_input_id).attr('checked','checked');
                                //successfully Enabled

                            }

                            if(offline_type == 1){
                               $('#'+product_input_id).removeAttr('checked','checked');
                              //successfully Disabled
                            }

                       $('#offline_online_category_or_product_popup').modal('hide');
                     }else{
                       swal('Oops...', 'Internal server error', 'error');
                     }// 2 offline_tag_missing, 3//offline_value_missing, //offline_type_missing,  4//offline_type_missing, 5//product_id_missing, 6//restaurant_id_missing,7 invalid_offline_tag,0 - query or update realted issue
                },
                
            });
          
         }else if(offline_type == 2){
             swal('Oops...', 'You are missing a required field ', 'error');
         }
  });

  //product status change ----Enable/Disable-----------------END-------------

  //Delete product  -----------------------START---------------------
  $('body').on('click', '.product_delete', function() {

        var new_product_table_url = product_table_url.replace("table","2");// if action mode then it will repalce table to  2 // it is becouse when delete success then it will give new url which will comapre segment with serach filter

        var product_input_id  = $(this).attr('id');//input id

        var product_input_id_array = product_input_id.split("_");
        var product_id = product_input_id_array[2];

        var product_name_id = '#product_name_'+product_id;
        var product_name = $(product_name_id).text();

          swal({
              title: "Are you sure to delete this product permanently?",
              text: "Once deleted, You will not be able to recover the action!",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) {
                
                    // Ajax-------SATRT------------
                    
                      $.ajax({
                      url: product_delete_url,
                      data: { 
                          product_id: product_id
                      },
                      type: 'post',
                      success: function(response){
                        
                          if(response == 1){
                           
                          /* swal("Poof! Your imaginary file has been deleted!", {
                              icon: "success",
                            });*/
                             $( "#product_and_category_list_table" ).load(new_product_table_url);
                          }
                         
                         if(response == 0){
                            swal('Oops...', 'Internal server error', 'error');
                         }
                      },
                      
                  });
                  // Ajax-------END------------
              }
            });
        
  });

  //Delete product -----------------------END----------------------

//Category status change ----Enable/Disable-----------------START-------------
 

 //change select box accroding to selected offline tag-------START---------
   $('body').on('click', '.offline_tag', function() {
     var offline_tag =  $(this).attr('offline_tag');//  1 - Hour 2 - Day 3 - Multiple days
     $(this).removeClass('btn-primary');
     $(this).addClass('btn-secondary');
      if(offline_tag == 1){
         $('#select_days').addClass('d-none');
         $('#select_multiple_days').addClass('d-none');
         $('#select_hours').removeClass('d-none');

         //final value send for offline/online
         $('#offline_tag').val('1');//by default it's 1  for hours on input box
         $('.offline_lable_name').text('For Hours');
         
         
          $('.offline_day_btn_clr').addClass('btn-primary');
          $('.offline_multi_day_btn_clr').addClass('btn-primary');
         
      }

      if(offline_tag == 2){

        $('#select_hours').addClass('d-none');
        $('#select_multiple_days').addClass('d-none');
        $('#select_days').removeClass('d-none');

        //final value send for offline/online
         $('#offline_tag').val('2');
        $('.offline_lable_name').text('For Single Day');

         $('.offline_hour_btn_clr').addClass('btn-primary');
         $('.offline_multi_day_btn_clr').addClass('btn-primary');

      }

      if(offline_tag == 3){
         $('#select_hours').addClass('d-none');
         $('#select_days').addClass('d-none');
         $('#select_multiple_days').removeClass('d-none');

          //final value send for offline/online
         $('#offline_tag').val('3');
         $('.offline_lable_name').text('For Multi Days');

          $('.offline_hour_btn_clr').addClass('btn-primary');
          $('.offline_day_btn_clr').addClass('btn-primary');
         
      }
   });
 //change select box accroding to selected offline tag-------END---------

  $('body').on('change', '.category_status', function() {
        //selected category id which you want to go online /offline
        var category_input_id  = $(this).attr('id');//input id
        $('#offline_mode_id').val(category_input_id);

        // same  modal use for category and product offline online 
        //  differentiate id with submit id , it will change when click on category toggle same as product toggle for disable(offline)
        $('.offline_online_category_or_product_submit').attr('id','category_online_offline_save');
    
  });

  $('#toggle-event').on("change", function(e) {
    var isClicked = $('#toggle-event').is(':checked');
    if(isClicked == true){
         $('#modalTurnOff').show();
         $('.toggle').addClass('off');
    }
});
  $('body').on('click', '#category_online_offline_save, .category_status', function() {

        var offline_tag = $('#offline_tag').val();
       
        var restaurant_id = $('#restaurant_list').val();

          if(restaurant_id === undefined || restaurant_id === null){// merchant and role 2 logged in
              restaurant_id = selected_restaurant_id;
          }else{// super admin is logged in
             restaurant_id = $('#restaurant_list').val();
          }


      //selected category id which you want to go online /offline
       if($(event.target).is('.category_status')) {//check wich element is clicked
            //alert(event.target.id + ' was clicked.');
            category_input_id = event.target.id;
            $('.enable_disable_product_category_title').text('Disable Category');// same  modal use for product offline online
        }else{
             
            var category_input_id  = $('#offline_mode_id').val();//input id
        }

       
        var category_input_id_array = category_input_id.split("_");
        var category_id = category_input_id_array[2];
        var category_name_id = '#category_name_'+category_id;
        var category_name = $(category_name_id).text();


        //offline_type 1 - DISABLE 2 - ENABLE
        var status_mode_type = $('#'+category_input_id).attr('status_mode_type');
        var offline_type = status_mode_type;//prodcut status - if its 1 then it will go for offline, if its 2 then it will go online 


        // geting final offline value ------
         
        if(offline_tag == 1){
           var offline_tag_value = $('#hours_offline_value').val();

            //required fields
            if(offline_tag_value == ""){
               $('#hours_offline_value').css('border-color','red');
               $('#unfill_hours_offline_value').text('Please Select Time');
            }else{
               $('#hours_offline_value').css('border-color','#ccc');
               $('#unfill_hours_offline_value').text('');
            }

        }else  if(offline_tag == 2){

           var days_offline_fromdate = $('#days_offline_fromdate').val();
           var offline_tag_value = days_offline_fromdate;

          //required fields
           if(offline_tag_value == ""){
               $('#days_offline_fromdate').css('border-color','red');
               $('#unfill_days_offline_fromdate').text('Please Select Day');
            }else{
               $('#days_offline_fromdate').css('border-color','#ccc');
               $('#unfill_days_offline_fromdate').text('');
            }

        }else  if(offline_tag == 3){
           //from date and time
           var offline_product_category_fromdate = $('#offline_product_category_fromdate').val();

          //till date and time
           var offline_category_tilldate = $('#offline_product_category_tilldate').val();

            //required fields--------------start-----------
             if(offline_product_category_fromdate == ""){
                 $('#offline_product_category_fromdate').css('border-color','red');
                 $('#unfill_offline_product_category_fromdate').text('Please Select Day');
              }else{
                 $('#offline_product_category_fromdate').css('border-color','#ccc');
                 $('#unfill_offline_product_category_fromdate').text('');
              }

             if(offline_category_tilldate == ""){
                 $('#offline_product_category_tilldate').css('border-color','red');
                 $('#unfill_offline_category_tilldate').text('Please Select Day');
              }else{
                 $('#offline_product_category_tilldate').css('border-color','#ccc');
                 $('#unfill_offline_category_tilldate').text('');
              }
               //required fields--------------end-----------

              if(offline_product_category_fromdate != "" && offline_category_tilldate != ""){
                  var offline_tag_value = offline_product_category_fromdate+','+offline_category_tilldate;
              }else{
                  var offline_tag_value = "";
              }
        }
        
        if(offline_tag == ""){
           $('#unselect_offline_tag').text('Please Select Any Tag');
        }else{
           
           $('#unselect_offline_tag').text('');
        }

        //alert(offline_tag_value);


         var new_category_table_url = category_table_url.replace("table","1");// if action mode enable disable then it will repalce table to  1 same as  2 in delete case

         if((offline_tag != "" && offline_tag_value != "" && category_id != "" && restaurant_id != "" && offline_type != "" && offline_type == 1) || (offline_type == 2)){

           $.ajax({
                url: category_active_inactive_url,
                data: { 
                    offline_tag:offline_tag,
                    offline_value:offline_tag_value, 
                    category_id: category_id,
                    restaurant_id:restaurant_id,
                    offline_type:offline_type
                },
                type: 'post',
                success: function(response){
                   if(response == 1){

                       //after resopose have to must reset data of this modal--start--
                       $('#offline_tag').val('1');//default
                       $('#offline_mode_id').val('');

                       $('#days_offline_fromdate').val('');
                       $('#hours_offline_value').val('');
                       $('#offline_product_category_fromdate').val('');
                       $('#offline_category_fromtime').val('00:00:00');
                       $('#offline_product_category_tilldate').val('');
                       $('#offline_category_tilltime').val('11:59:59');
                        //after resopose have to must reset data of this modal--end---
                      
                           $( "#product_and_category_list_table" ).load(new_category_table_url);

                          if(offline_type == 2){
                             $('#'+category_input_id).attr('checked','checked');
                               //successfully Enabled

                                //enable_add_import_button  for product
                              $('#alert_for_category_disabled_time').html('');
                             $('.import_disable').removeAttr('disabled');
                          }

                          if(offline_type == 1){
                             $('#'+category_input_id).removeAttr('checked','checked');
                            
                              //successfully Disabled
                              
                             //disable_add_import_button  for product
                              $('#alert_for_category_disabled_time').html('<div class="alert"><strong>You cannot Add product because the category is currently offline</strong> </div> ');
                              $('.import_disable').attr('disabled','disabled');
                          }

                       $('#offline_online_category_or_product_popup').modal('hide');
                     }else{
                       swal('Oops...', 'Internal server error', 'error');
                     }// 2 offline_tag_missing, 3//offline_value_missing, //offline_type_missing,  4//offline_type_missing, 5//category_id_missing, 6//restaurant_id_missing,7 invalid_offline_tag,0 - query or update realted issue

                },
            });
          
         }else if(offline_type == 2){
             swal('Oops...', 'You are missing a required field ', 'error');
         }

  });

  //Category status change ----Enable/Disable-----------------END-------------

  //Delete category  -----------------------START---------------------
  $('body').on('click', '.category_delete', function() {

        var new_category_table_url = category_table_url.replace("table","2");// if action mode then it will repalce table to  2 // it is becouse when delete success then it will give new url which will comapre segment with serach filter

        var category_input_id  = $(this).attr('id');//input id

        var category_input_id_array = category_input_id.split("_");
        var category_id = category_input_id_array[2];

        var category_name_id = '#category_name_'+category_id;
        var category_name = $(category_name_id).text();
   
          swal({
              title: "Are you sure to delete this category permanently?",
              text: "Once deleted, You will not be able to recover the action!",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) {
                
                    // Ajax-------SATRT------------
                    
                      $.ajax({
                      url: category_delete_url,
                      data: { 
                          category_id: category_id
                      },
                      type: 'post',
                      success: function(response){
                        
                          if(response == 1){
                           
                             /*  swal("Poof! Your imaginary file has been deleted!", {
                              icon: "success",
                            });*/

                          /* setTimeout(function(){
                                 //$( "#product_and_category_list_table" ).load(new_category_table_url);
                                 $(document).ajaxComplete(function(){
                                   $('#search_product_data').trigger('click');
                                  });
                                  category_select_box();
                              }, 2000); //refresh every 2 seconds*/
                               $(document).ajaxComplete(function(){
                                   $('#search_product_data').trigger('click');
                                  });
                                  category_select_box();
                             
                           }
                         
                         if(response == 0){
                            swal('Oops...', 'Internal server error', 'error');
                         }
                      },
                      
                  });
                  // Ajax-------END------------
              }
            });
        
  });

  //Delete category -----------------------END----------------------

  //add - edit category DEATILS -----------------------START-------
   ///////////////////////////////////
  function reset_modal_form(BASE_URL){
      setInterval(function(){ 
        var is_madal_open = $(".modal").hasClass("show");
          if(is_madal_open == false){
              $("#add_edit_category_form").trigger('reset');//form reset

              $("#cat_disp_img").attr('src',BASE_URL+'assets/images/default_product_image.png');

              $('#category_name').css('border-color','#ccc');
              $('#unfill_category_image').text('');

              $('#unfill_category_name').text('');
               $('#category_description').css('border-color','#ccc');

              $('#unfill_category_discription').text('');
          }
          }, 1000);
  }
   
  reset_modal_form(BASE_URL);
  //////////////////////////////////


  $('body').on('click', '.cat_mode_type_btn', function() {

       var cat_mode_type = $(this).attr('mode_type_modal');
       $('#cat_mode_type').val(cat_mode_type);

      
       //comman modal changes according to add and edit
       if(cat_mode_type == 2){
         $('.category_modal_title').text('Edit Category');
          $("#cat_disp_img").attr('src','');
           //Fetch data on edit modal click --------------START------------
             var edit_category_input_id = $(this).attr('id');
             var edit_category_id = edit_category_input_id.split('edit_category_');
              $('#edit_category_id').val(edit_category_id[1]);

                $.ajax({
                    url: BASE_URL+'admin/selected_category_detail/',//merchant detail
                    data: { 
                        category_id: edit_category_id[1],
                    },
                    type: 'post',
                    success: function(response){
                      if(response != 0){
                         var data = JSON.parse(response);
                          $('#category_name').val(data.category_name);
                          $('#category_description').val(data.description);
                          $('#file').attr('value',data.category_image);
                          $('#edit_category_image_exist').attr('value',data.category_image);

                          if(data.category_image != ""){
                            $('#cat_disp_img').attr('src',BASE_URL+data.category_image);
                          }else{
                            $('#cat_disp_img').attr('src',BASE_URL+'assets/images/default_product_image.png');
                          }
                          
                        }
                    },
                    
                });
           //Fetch data on edit modal click --------------END--------------

       }else{//ADD CATEGORY
        $("#file").val('');
         $('#edit_category_image_exist').val('');
         $('.category_modal_title').text('Add Category');
       }
  });

 //delete/remove  selected image if dont want to upload
   $('body').on('click', '.delete_selected_cat_img', function() {
        $('#cat_disp_img').attr('src',BASE_URL+'assets/images/default_product_image.png');
        $(this).closest("div.img-add").find("input[type='file']").val('');
        $('#edit_category_image_exist').val('');
   });

  $('body').on('click', '#category_submit', function() {

      var upload_category_image_status = false;// image upload or not (this is use for edit time)

      var mode_type = $("#cat_mode_type").val(); // type 1 = add, 2 = edit mode
      var edit_category_image_exist = $("#edit_category_image_exist").val(); // type 1 = add, 2 = edit mode

      var category_name = $("#category_name").val();
      var category_description = $("#category_description").val();
      
      var category_id = $("#edit_category_id").val(); // user id and category id is same
      
       var fd = new FormData();
       var files = $('#file')[0].files;

       /*if(files.length == 0 && edit_category_image_exist == ""){
            $('#unfill_category_image').text('Please Select Category Image');
        }else{
            
             $('#unfill_category_image').text('');
        }*/
       
       if(category_name == ""){
            $('#category_name').css('border-color','red');
            $('#unfill_category_name').text('Please Fill Category  Name');
        }else{
            $('#category_name').css('border-color','#ccc');
            $('#unfill_category_name').text('');
        }

        /*
            if(category_description == ""){
                $('#category_description').css('border-color','red');
                $('#unfill_category_discription').text('Please Fill Category Description');
            }else{
                 $('#category_description').css('border-color','#ccc');
                 $('#unfill_category_discription').text('');
            }  */  
   
            if(category_id != "" &&  mode_type == 2){
                 category_id = category_id
            }else{
                 category_id = "";
            }

          if(edit_category_image_exist != "" && files.length == 0){// pre image 
             fd.append('category_image',edit_category_image_exist);
             upload_category_image_status = false;


           }else if(files.length > 0){
            
             fd.append('category_image',files[0]);
              upload_category_image_status = true;
           }else{
              upload_category_image_status = true; // before it was false but as pr the it is not mendotoery thats why  it tru
           }
         

          // Check file selected or not
          if(category_name != ""   && (upload_category_image_status == true || edit_category_image_exist !="" )&&selected_restaurant_id != ""){
            
             fd.append('category_name',category_name);
             fd.append('category_description',category_description);
             fd.append('category_id',category_id);
             fd.append('selected_restaurant_id',selected_restaurant_id);
             
             var new_category_table_url = category_table_url.replace("table","1");
             $.ajax({
                url:  BASE_URL+'admin/add_edit_category_details/'+mode_type+'/',
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(response){
                  
                   if(response == 1 && mode_type == 1){
                       //swal('Success', 'category added Successfully!', 'success');

                       setTimeout(function(){ 
                            $(document).ajaxComplete(function(){
                               $('#search_product_data').trigger('click');
                              });
                             category_select_box();
                        }, 1000);
                     

                       $('#add_edit_category_popup').modal('hide');
                       reset_modal_form(BASE_URL);
                   }


                   if(response == 1 && mode_type == 2){
                      // swal('Success', 'Category Updated Successfully!', 'success');
                        setTimeout(function(){ 
                        $( "#product_and_category_list_table" ).load(new_category_table_url);
                        }, 1000);

                       $('#add_edit_category_popup').modal('hide');
                       
                   }

                    if(response == 0 && mode_type == 2){
                        //swal('Dont Worry...', 'Nothing Changed!');
                   }

                   if(response == 4){
                     swal('Oops...', 'Something went wrong!', 'error');
                   }

                   if(response == 5 || response == 2){
                     swal('Oops...', 'Internal server error', 'error');
                   }

                },
             });//ajax end
          }else if(selected_restaurant_id ==""){
              swal('Oops...', 'Please Select Restaurant', 'error');
          }else{
              //swal('Oops...', 'You are missing a required field ', 'error');
          }
  });
  // category add and edit details-------------------END---------------------------
  //Product Export CSV -----------------------START------------
  $('body').on('click', '.export_products_csv', function() {

        var export_type = $(this).attr('export_type');// category, product, combine
       

        var from_date = $('#fromdate').val();
        var to_date = $('#todate').val();
        var product_status = $('#products_status').val();
        var search =$('.search_key').val();
        var restaurant_id =$('#restaurant_list').val();
        var category_id =$('#category_list').val();
        
        if(from_date == '')
        {
           from_date= "all";
        }

        if(to_date==''){
           to_date= "all";
        }

        if(product_status==''){
           product_status= "all";
        }

        if(search == '')
        {
            search = "all";
        }else{
             search = keyword.trim();
          }


        if(restaurant_id ==''){
           restaurant_id = 0;
        }
   
        if(category_id ==''){
           category_id = 0;
        }

      if(from_date!="all" || to_date!="all" || product_status!="all" || search != "all" || export_type != "all"){
             
               window.location.replace(BASE_URL+'admin/exportProductsCSV/0/'+from_date+'/'+to_date+'/'+product_status+'/'+search+'/'+restaurant_id+'/'+category_id+'/'+export_type+'/');//load only table on a products page 
        }else{
             
              window.location.replace(BASE_URL+'admin/exportProductsCSV/');
        }
    
   });
  //Product Export CSV -----------------------END------------


  //Change import type of product csv -------------START--------------

   $('body').on('click', '#tab_category', function() {
          $('#import_type').val('category');//by default value  = category
          $('.export_products_csv').attr('export_type','category');//by default value  = category
          $('.export_label').text('Category');
          $('.sample_products_csv').attr('href',BASE_URL+'assets/sample_csv/product_csv/category.csv');//by default value  = category

          $('#alert_for_category_disabled_time').html('');
          $('.import_disable').removeAttr('disabled');
   });

   $('body').on('click', '#tab_product', function() {
          $('#import_type').val('product');
           $('.export_products_csv').attr('export_type','product');
           $('.export_label').text('Product');
           $('.sample_products_csv').attr('href',BASE_URL+'assets/sample_csv/product_csv/product.csv');
   });

    $('body').on('click', '#tab_combined', function() {
          $('#import_type').val('combined');
           $('.export_label').text('Combined');
           $('.export_products_csv').attr('export_type','combined');
           $('.sample_products_csv').attr('href',BASE_URL+'assets/sample_csv/product_csv/combined.csv');
   });
    
     // check if category is diabaled then cant import products
    $('body').on('click', '#tab_combined, #tab_product', function() {
         
            var selected_category_id = $('#category_list').children("option:selected").val();
            var selected_category_id_status = $('#category_status_'+selected_category_id).attr('status_mode_type');
           if(selected_category_id_status == 2){
              $('#alert_for_category_disabled_time').html('<div class="alert"><strong>You cannot Add product because the category is currently offline</strong> </div> ');
              $('.import_disable').attr('disabled','disabled');
           }else{
               $('#alert_for_category_disabled_time').html('');
               $('.import_disable').removeAttr('disabled');
           }
    });


    //show load when file importing
     $('body').on('click', '#show_import_loader', function() {
        var uploadFile = $('input[name="uploadFile"]').val();
        if(uploadFile !=""){
            $('#import_process_loader').removeClass('d-none'); 
           
        }
     });
    

  //Change import type of product csv -------------END--------------

  // product add and edit details-------------------START---------------------------

  ///////////////////////////////////
  function reset_modal_form(BASE_URL){
      setInterval(function(){ 
        var is_madal_open = $(".modal").hasClass("show");
          if(is_madal_open == false){
              $("#add_edit_product_form").trigger('reset');//form reset

              $("#cat_disp_img").attr('src',BASE_URL+'assets/images/default_product_image.png');

              $('#product_name').css('border-color','#ccc');
              $('#unfill_product_image').text('');

              $('#unfill_product_name').text('');
               $('#product_description').css('border-color','#ccc');

              $('#unfill_product_discription').text('');
          }
          }, 1000);
  }
   
  reset_modal_form(BASE_URL);
  //////////////////////////////////


  $('body').on('click', '.product_mode_type_btn', function() {

       var product_mode_type = $(this).attr('mode_type_modal');
       $('#product_mode_type').val(product_mode_type);
        
       //remove error-----start
         $('.error').empty();
         $('input,textarea,select').removeAttr('style');
          ;
        //remove error-----end
      
       //comman modal changes according to add and edit
       if(product_mode_type == 2){
         $('.product_modal_title').text('Edit Product');
           //Fetch data on edit modal click --------------START------------
             var edit_product_input_id = $(this).attr('id');
             var edit_product_id = edit_product_input_id.split('edit_product_');
              $('#edit_product_id').val(edit_product_id[1]);

                $.ajax({
                    url: BASE_URL+'admin/selected_product_detail/',//merchant detail
                    data: { 
                        product_id: edit_product_id[1],
                    },
                    type: 'post',
                    success: function(response){
                      if(response != 0){
                         var data = JSON.parse(response);
                          $('#product_name').val(data.product_name);

                         // $('#food_type').attr('value',data.is_veg);
                           
                            $("#food_type option").removeAttr('selected');//remove edit time seleted value
                            if(data.is_veg == 2 || data.is_veg == 0){
                                $("#default_select_non").attr('selected','selected');//default select non veg
                            }else{
                                $("#veg_select").attr('selected','selected');//veg select
                            }
                         
                       
                          $('#product_price').val(data.price);
                          $('#product_offer_price').val(data.offer_price);
                          $('#minimum_quantity').val(data.min_qty);
                          $('#maximum_quantity').val(data.max_qty);

                          $('#product_short_discription').val(data.short_desc);
                          $('#product_long_discription').val(data.long_desc);
                          if(data.product_image !=""){
                             $('#product_disp_img').attr('src',BASE_URL+data.product_image);
                          }else{
                             $('#product_disp_img').attr('src',BASE_URL+'assets/images/default_product_image.png');
                          }
                         
                          $('#edit_product_image_exist').val(data.product_image);
                        }
                    },
                    
                });
           //Fetch data on edit modal click --------------END--------------


       }else{//ADD product
        if($("#food_type option:selected").val() == 1){
            $("#food_type option").removeAttr('selected');//remove edit time seleted value
            $("#default_select_non").attr('selected','selected');//default select non veg
        }
        
         $('#add_edit_product_popup').find("input,textarea,input[type=file]").val('').end().find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
        
        //reset form data ----------START--------------
          
          $('#product_disp_img').attr('src',BASE_URL+'assets/images/default_product_image.png');
          $('#edit_product_image_exist').val('');
          $('#edit_product_id').val('');
        //reset form data ----------END--------------
         
         $('.product_modal_title').text('Add Product');
       }
  });

  //validate 
   
  function offer_price_validat(){
      var product_price = parseFloat($("#product_price").val());
      var product_offer_price = parseFloat($("#product_offer_price").val());

      if(product_offer_price >= product_price){
        $('#product_offer_price').css('border-color','red');
        $('#unfill_product_offer_price').text('Offer price must be less than the actual price');

        return  false;
      }else{
         $('#product_offer_price').css('border-color','#ccc');
         $('#unfill_product_offer_price').text('');
        return true;
      }
  }
  $("#product_offer_price").keyup(function(e){
       offer_price_validat();
  });

   //delete/remove  selected image if dont want to upload
   $('body').on('click', '.delete_selected_product_img', function() {
        $('#product_disp_img').attr('src',BASE_URL+'assets/images/default_product_image.png');
        $(this).closest("div.img-add").find("input[type='file']").val('');
        $('#edit_product_image_exist').val('');
   });
   
  $('body').on('click', '#product_submit', function() {
      var upload_product_image_status = false;// image upload or not (this is use for edit time)

      var mode_type = $("#product_mode_type").val(); // type 1 = add, 2 = edit mode
      var edit_product_image_exist = $("#edit_product_image_exist").val(); // type 1 = add, 2 = edit mode

      var selected_category_id = $('#category_list').children("option:selected").val();
       
      var product_name = $("#product_name").val();
      var product_price = $("#product_price").val();
      var product_offer_price = parseFloat($("#product_offer_price").val());
      var minimum_quantity = $("#minimum_quantity").val();
      var maximum_quantity = $("#maximum_quantity").val();
      var product_short_discription = $("#product_short_discription").val();
      var product_long_discription = $("#product_long_discription").val();
      var food_type = $("#food_type").val();
      
      var product_id = $("#edit_product_id").val(); // user id and product id is same
      
       var fd = new FormData();
       var files = $('#product_image')[0].files;
       
       if(product_name == ""){
            $('#product_name').css('border-color','red');
            $('#unfill_product_name').text('Please Fill Product  Name');
        }else{
            $('#product_name').css('border-color','#ccc');
            $('#unfill_product_name').text('');
        }

        if(product_price == ""){
            $('#product_price').css('border-color','red');
            $('#unfill_product_price').text('Please Fill Product Price');
        }else{
            $('#product_price').css('border-color','#ccc');
            $('#unfill_product_price').text('');
        }

        /*if(product_offer_price == ""){
            $('#product_offer_price').css('border-color','red');
            $('#unfill_product_offer_price').text('Please Fill Product Offer Price');
        }else{
            $('#product_offer_price').css('border-color','#ccc');
            $('#unfill_product_offer_price').text('');
        }
*/
        /*if(minimum_quantity == ""){
            $('#minimum_quantity').css('border-color','red');
            $('#unfill_minimum_quantity').text('Please Fill Product Minimum Quantity');
        }else{
            $('#minimum_quantity').css('border-color','#ccc');
            $('#unfill_minimum_quantity').text('');
        }

        if(maximum_quantity == ""){
            $('#maximum_quantity').css('border-color','red');
            $('#unfill_maximum_quantity').text('Please Fill Product Maximum Quantity');
        }else{
            $('#maximum_quantity').css('border-color','#ccc');
            $('#unfill_maximum_quantity').text('');
        }*/


       /* if(product_short_discription == ""){
            $('#product_short_discription').css('border-color','red');
            $('#unfill_product_short_discription').text('Please Fill product Description');
        }else{
             $('#product_short_discription').css('border-color','#ccc');
             $('#unfill_product_short_discription').text('');
        }  

        if(product_long_discription == ""){
            $('#product_long_discription').css('border-color','red');
            $('#unfill_product_long_discription').text('Please Fill product Description');
        }else{
             $('#product_long_discription').css('border-color','#ccc');
             $('#unfill_product_long_discription').text('');
        } */

        if(food_type == ""){
            $('#food_type').css('border-color','red');
            $('#unselect_food_type').text('Please Select Food Type');
        }else{
             $('#food_type').css('border-color','#ccc');
             $('#unselect_food_type').text('');
        }  


        if(product_id != "" &&  mode_type == 2){
             product_id = product_id
        }else{
             product_id = "";
        }

          if(edit_product_image_exist != "" && files.length == 0){// pre image 
             fd.append('product_image',edit_product_image_exist);
             upload_product_image_status = false;


           }else if(files.length > 0){
            
             fd.append('product_image',files[0]);
              upload_product_image_status = true;
           }else{
              upload_product_image_status = true;// before it was mendtory but as prt the client it not mendorty  
           }

           /*if(files.length == 0 && edit_product_image_exist == ""){
                $('#unfill_product_image').text('Please Select Product Image');
            }else{
                
                 $('#unfill_product_image').text('');
                  upload_product_image_status = true;
            }*/

           var offer_price_validat_status = offer_price_validat();
          
          // Check file selected or not
          if(product_name != "" && product_price != "" && food_type != "" && (minimum_quantity != "" ||minimum_quantity == "") && (maximum_quantity != "" || maximum_quantity == "") && offer_price_validat_status == true && (upload_product_image_status == true || edit_product_image_exist !="" ) && selected_restaurant_id != "" && selected_category_id != "" && mode_type !="" && (product_offer_price!="" || product_offer_price == "" || product_offer_price == 0)){

             $(this).attr('disabled','disabled');
               
             //swal("Wait..", "Please wait while we are processing your request!");
            
             fd.append('product_name',product_name);
             fd.append('food_type',food_type);
             fd.append('product_price',product_price);
             fd.append('product_offer_price',product_offer_price);
             fd.append('minimum_quantity',minimum_quantity);
             fd.append('maximum_quantity',maximum_quantity);
             fd.append('product_short_discription',product_short_discription);
             fd.append('product_long_discription',product_long_discription);
             fd.append('product_id',product_id);

             fd.append('selected_restaurant_id',selected_restaurant_id);
             fd.append('selected_category_id',selected_category_id);

             
             var new_product_table_url = product_table_url.replace("table","1");

             $.ajax({
                url:  BASE_URL+'admin/add_edit_product_details/'+mode_type+'/',
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(response){
                  
                   if(response == 1 && mode_type == 1){
                      //Product Added Successfully
                      $( "#product_and_category_list_table" ).load(new_product_table_url);
                       $('#add_edit_product_popup').modal('hide');
                       reset_modal_form(BASE_URL);
                   }

                   if(response == 1 && mode_type == 2){
                      //Product Updated Successfully swal('Success', 'Product Updated Successfully!', 'success');
                       $( "#product_and_category_list_table" ).load(new_product_table_url);
                       $('#add_edit_product_popup').modal('hide');
                   }

                    if(response == 0 && mode_type == 2){
                       // swal('Dont Worry...', 'Nothing Changed!');
                   }

                   if(response == 4){
                     swal('Oops...', 'Something went wrong!', 'error');
                   }

                   if(response == 5 || response == 2){
                     swal('Oops...', 'Internal server error', 'error');
                   }
                   $('#product_submit').removeAttr('disabled','disabled');
                 
                },
             });//ajax end

          }else if(selected_restaurant_id ==""){
              swal('Oops...', 'Please Select Restaurant', 'error');
          }else{
             // swal('Oops...', 'You are missing a required field ', 'error');
          }
  });
 });
// product add and edit details-------------------END---------------------------

//product page - variant--------------------START-----------------------------
//Getting product id for add variant ------------------START---------------
$('body').on('click', '.add_edit_variant_type', function() {
    var edit_product_id =  $(this).attr('edit_product_id');
    $('#selected_product_for_add_variant').val(edit_product_id);
});
//Getting product id for add variant ------------------END---------------
// Add Variant on products page----------------------------START----------------------------
$('body').on('click', '#add_variant', function() {

     var variant_name = $('#variant_name').val();

       if(variant_name == ""){
            $('#variant_name').css('border-color','red');
            $('#unfill_variant_name').text('Please Fill variant name');
        }else{
             $('#variant_name').css('border-color','#ccc');
             $('#unfill_variant_name').text('');
        }  

        if(variant_name != ""){
              // Ajax-------SATRT------------
                    $.ajax({
                    url: BASE_URL+'admin/add_variant_submit/',
                    data: { 
                        variant_name: variant_name
                    },
                    type: 'post',
                    success: function(response){
                      
                        if(response == 1){
                             /*swal("Variant Added Successfully !", {
                                icon: "success",
                              });*/

                            $( "#select_variant_for_product" ).load(BASE_URL+'admin/get_all_variant_selection');
                            $('#variant_name').val('');
                         }
                       
                       if(response == 0){
                          swal('Oops...', 'Internal server error', 'error');
                       }

                        if(response == 2){
                         swal('Oops...', 'You are missing a required field ', 'error');
                       }
                       if(response == 3){
                         swal('Oops...', 'Variant name is already exists!', 'error');
                       }
                    },
                    
                });
                // Ajax-------END------------
        }else{
            //swal('Oops...', 'You are missing a required field ', 'error');
        }
});
// Add Variant on products page-----------------------------------END----------------------------

//---------Add Variant Type -------------------------START--------------------------
$('body').on('click', '#add_variant_type_submit', function() {

     var variant_type_name = $('#variant_type_name').val();
      
     var selected_variant_id = $('#selected_variant_id_for_product').val();

    if(selected_variant_id == ""){
        $('#unselect_variant').text('Please Select Variant');
    }else{
        $('#unselect_variant').text('');
    }
        
   if(variant_type_name == ""){
        $('#variant_type_name').css('border-color','red');
        $('#unfill_variant_type_name').text('Please Fill variant type name');
    }else{
         $('#variant_type_name').css('border-color','#ccc');
         $('#unfill_variant_type_name').text('');
    } 

        if(variant_type_name != "" && selected_variant_id != ""){
              // Ajax-------SATRT------------
                    $.ajax({
                    url: BASE_URL+'admin/add_variant_type_submit/',
                    data: { 
                        variant_type_name: variant_type_name,variant_id:selected_variant_id
                    },
                    type: 'post',
                    success: function(response){
                        if(response == 1){
                            /* swal("Variant Type Added Successfully !", {
                                icon: "success",
                              });*/
                             $('#variant_type_name').val('');
                             $( "#select_variant_type_for_product" ).load(BASE_URL+'admin/get_all_variant_type_selection/'+selected_variant_id+'');
                         }
                       
                       if(response == 0){
                          swal('Oops...', 'Internal server error', 'error');
                       }

                        if(response == 2){
                         swal('Oops...', 'You are missing a required field ', 'error');
                       }
                       if(response == 3){
                         swal('Oops...', 'variant_type name is already exists!', 'error');
                       }
                    },
                    
                });
                // Ajax-------END------------
        }
});
//---------Add Variant Type -------------------------END--------------------------

//Show variant types according to selected variant on products page -----------------START-------------
$('body').on('click', '.selected_variant_id', function() {
        // at the time of edit  we need to clear privous selected variant types
       $('#selected_variant_type_for_add_price').empty();

        var  selected_variant_id = $(".selected_variant_id:radio[name=variant_id]:checked").val();
        var  selected_variant_name = $(".selected_variant_id:radio[name=variant_id]:checked").parent().text();
        $('#SelectvariantInput').val(selected_variant_name);
        
        $( "#select_variant_type_for_product" ).load(BASE_URL+'admin/get_all_variant_type_selection/'+selected_variant_id+'');

        $('#selected_variant_id_for_product').val(selected_variant_id);
});
//Show variant types according to selected variant on products page -----------------END-------------

//Show Selected Variant Types for add price ---------------------START-----------------------

//commaon append function for add and edit time
function common_variant_type_add_price_append(variant_type_name,variant_type_id,variant_type_price,default_variant_status,$edit_mode_check){
 
  if($edit_mode_check == 1){
      var variant_type_price = variant_type_price;
      if(default_variant_status == 1){
        var default_checked = "checked=''";
      }
      var selected_variant_input_check_id = '<input type="hidden" id="check_variant_type_'+variant_type_id+'"/>';
  }else{
     var variant_type_price = "";
     var default_checked = "";
      var selected_variant_input_check_id = "";
  }
    

    $("#selected_variant_type_for_add_price").append('  <div class="row vairant_type_input" append_id="'+variant_type_id+'">'
                                   +' <div class="col-sm-4"> '
                                      +' '+variant_type_name+''
                                  +'  </div>'
                                   +'  <div class="col-sm-4"><span class="currency variant_currency">S$</span>'
                                   +'     <input type="number" min="1" minlength="1" maxlength="5" class="check_space variant_price_input" id="variant_type_price_'+variant_type_id+'" value="'+variant_type_price+'">&nbsp;<span class="error" id="unfill_variant_type_price_'+variant_type_id+'"></span>'
                                   +' </div>'
                                   +'  <div class="col-sm-4"> '
                                   +' <div class="row">'
                                   +'   <div class="col-sm-4">'
                                   +'     <label class="enabled-label" data-children-count="1">'
                                    +'         <input type="radio" name="variant_type_set_as_default" class="default_variant_type_id" value="'+variant_type_id+'" '+default_checked+'>'
                                    +'         <span class="checkmark"></span>'
                                    +'      </label>'
                                     +' </div>'
                                     +'  <div class="col-sm-8 text-right">'
                                     +'    <a class="remove_added_variant_type_element remove_edit_time_append_'+variant_type_id+'" id="'+variant_type_id+'" data-id="" data-toggle="tooltip" title="" data-original-title="Delete">'
                                     +'      <i class="fas fa-trash-alt" style="cursor: pointer; color: red;"></i>'
                                      +'     </a>'
                                   +'   </div>'
                                    +'   </div>'
                                   +' </div>'+selected_variant_input_check_id
                              +'</div>');
}

$('body').on('click', '.selected_variant_type_id', function() {

     var add_edit_mode = $('#selected_variant_type_for_add_price').attr('add_edit_mode');// 1 - for edit , blank add time
     var selected_variant_type_array = [];
     var checked_count = 0;
      $(".selected_variant_type_id:checkbox[name=variant_type_id]:checked").each(function(){
        var variant_type_id = $(this).val();
        var variant_type_name = $(this).attr('variant_type_name');
  
        selected_variant_type_array.push({'variant_type_id': variant_type_id,'variant_type_name':variant_type_name });

        checked_count++;
     });


 
      if(add_edit_mode == ""){
         $("#selected_variant_type_for_add_price").empty();
      }
      
    
     // word on checked 
     var check_edit_time_new_append = [];
     var check_already_append = [];
      var count_editable = 0;
      $.each(selected_variant_type_array, function (index, value) {  


      
        if(add_edit_mode == "" ){
           common_variant_type_add_price_append(value.variant_type_name,value.variant_type_id);
        }else if(add_edit_mode == 1){


          var  exist_variant_type = $('#check_variant_type_'+value.variant_type_id).length;
          if(exist_variant_type == 0){
           
              // if only add new vairant type
             
              $(".vairant_type_input").each(function(){
                 var check_appended = $(this).attr('append_id');
                 check_already_append.push(check_appended);
                
              });

              check_edit_time_new_append.push({'variant_type_id': value.variant_type_id,'variant_type_name':value.variant_type_name });
             
          }
        }

     }); 

      if(checked_count == 0){

          //alert(checked_count);
          // if all checked value is clear wt the time of edit
           $('#selected_variant_type_for_add_price').attr('add_edit_mode','');
      }



      

//edit time --------------------START------------
 var unqiue = "";

 $.each(selected_variant_type_array, function (index2, value2) { 
 
   var check = check_already_append.includes(value2.variant_type_id);
     if(check!="" && check == true){
          //console.log('already exist privous one' + value2.variant_type_id);
     }else{
          //console.log('new selected' + value2.variant_type_id);

          // $("#selected_variant_type_for_add_price").empty();

             for (var i = 0; i < check_already_append.length; i++) {
                
                // console.log(check_already_append[i] +'=='+value2.variant_type_id );
                 if(value2.variant_type_id != check_already_append[i]){
                     unqiue =  true;
                 }
                 else{
                    unqiue =  false;
                 }
             }
            if(unqiue == true){
                common_variant_type_add_price_append(value2.variant_type_name,value2.variant_type_id);
            }

            //alert(count_editable);
             // if unselect exist variant type then make new elements
               /*if(count_editable == 0){
               
                 common_variant_type_add_price_append(value.variant_type_name,value.variant_type_id);
              }*/

              
             
            /*  if (check_already_append.length === 0) {
                 $(".selected_variant_type_id").each(function(){
                     if($(this).prop("checked") == true){
                         alert('blank');
                    }
                  });
              }*/

              //console.log(check_already_append);
     }
 
  });

 
//edit time --------------------END------------

    // work on edit time if variant type unchecked
     $(".selected_variant_type_id").each(function(){
        if(add_edit_mode == 1){
             var variant_type_id = $(this).val();
              if($(this).prop("checked") == false){
                  $('.remove_edit_time_append_'+variant_type_id).trigger('click');
              }
        }
       
     });
});

//for add more------remove added elements----

 $("body").on("click",".remove_added_variant_type_element",function(e){

       $(this).parents('.vairant_type_input').remove();
       var variant_type_id = $(this).attr('id');//variant type id

        $('#variant_type_id_'+variant_type_id).prop('checked',false); 


        //edit exist selected value empty
      


      //the above method will remove the user_data div
  });

//Show Selected Variant Types for add price ---------------------END-----------------------

//Give  selection limit of variant for order ------------------START----------------
 $("body").on("click",".variant_select_type",function(e){
        var variant_select_type = $(".variant_select_type:radio[name=variant_select_type_for_order]:checked").val();
        if(variant_select_type == 2){
            $('#give_select_variant_limit').removeClass('d-none');
        }else{
           $('#give_select_variant_limit').addClass('d-none'); 
        }
 });
 $("body").on("click","#select_limit_of_variant",function(e){

     if($(this).prop("checked") == true){
        //console.log("Checkbox is checked.");
        $('#variant_select_limit').removeAttr('disabled');
    }
    else if($(this).prop("checked") == false){
        //console.log("Checkbox is unchecked.");
          $('#variant_select_limit').val('');  
          $('#variant_select_limit').attr('disabled','disabled');

          //remove error
           $('#variant_select_limit').css('border-color','#ccc');
           $('#unfill_variant_select_limit').text('');
    }
 });

//Give  selection limit of variant for order ------------------END----------------

//Clear default variant selection -----------------START--------------------

  //Show "clear default" button of any radio button selected
 $('body').on('click', '.default_variant_type_id', function() {
    if($(this).prop("checked") == true){
        //alert(true);
        $('#show_clear_default_btn').removeClass('d-none');
    }

  // var  default_variant_type_id_check =  $(".default_variant_type_id:radio[name=variant_type_set_as_default]:checked").val();
  });

$('body').on('click', '#clear_default_selection', function() {
     $(".default_variant_type_id").prop("checked", false);
     $('#show_clear_default_btn').addClass('d-none');
});
//Clear default variant selection -----------------END--------------------

//Submit Variant for product -----------------------START--------------------------
$('body').on('click', '#submit_product_variant', function() {
    var add_edit_mode = $('#selected_variant_type_for_add_price').attr('add_edit_mode');// 1 - for edit , blank add time

    var selected_product_id = $('#selected_product_for_add_variant').val();
    var selected_variant_id = $('#selected_variant_id_for_product').val();
    var  default_variant_type_id_check =  $(".default_variant_type_id:radio[name=variant_type_set_as_default]:checked").val();
    var  is_variant_mandatory =  $(".is_variant_mandatory:checked").val();

 
    var variant_select_type = $(".variant_select_type:radio[name=variant_select_type_for_order]:checked").val();
    
    if(selected_variant_id == ""){
        $('#unselect_variant').text('Please Select Variant');
    }else{
        $('#unselect_variant').text('');
    }

    var variant_select_type_status;
    if(variant_select_type == 2 && $('#select_limit_of_variant').prop("checked") == true){
        var variant_select_limit = $('#variant_select_limit').val();
 

        if(variant_select_limit == "" || variant_select_limit == 0){
            $('#variant_select_limit').css('border-color','red');
            $('#unfill_variant_select_limit').text('Please Fill Limit');

             variant_select_type_status = false;
        }else{
            $('#variant_select_limit').css('border-color','#ccc');
            $('#unfill_variant_select_limit').text('');
             variant_select_type_status = true;
        }
       
    }else{
        $('#variant_select_limit').css('border-color','#ccc');
        $('#unfill_variant_select_limit').text('');
        var variant_select_limit = "";
        variant_select_type_status = true;
    }

    if(default_variant_type_id_check != undefined){
        default_variant_type_id = $(".default_variant_type_id:radio[name=variant_type_set_as_default]:checked").val();
    }else{
        default_variant_type_id = "";
    }

    var array_variant_for_product = [];
    var array_check_all_price_is_fill = [];
    var count_variant_type = 0;
    $(".selected_variant_type_id:checked").each(function(){

           var  variant_type_id = $(this).val();
           var  variant_type_price = $('#variant_type_price_'+variant_type_id).val();

           if(variant_type_price =="" ){
             $('#unfill_variant_type_price_'+variant_type_id).text('Please Select Variant Type Price');
             array_check_all_price_is_fill.push(false);
           }else{
             $('#unfill_variant_type_price_'+variant_type_id).text('');
           }
          
           if(variant_type_id !="" && variant_type_price !="" && variant_type_price != undefined){
            
            array_variant_for_product.push({'variant_type_id': variant_type_id,'variant_type_price':variant_type_price}); 
           }
           count_variant_type++; 
    });

    if(count_variant_type == 0){//if variant type not selected
          $('#unselect_variant_type').text('Please Select Variant Type');
    }else{
          $('#unselect_variant_type').text('');
    }

     var check_empty_price_value = array_check_all_price_is_fill.includes(false);
     
     //alert(array_check_all_price_is_fill);
     if(array_check_all_price_is_fill!="" && check_empty_price_value == true){
       //some price  not filled
          
        var   check_price_is_fill = false;
          
     }else{
         //all price  filled
         var check_price_is_fill = true;
     }
     
     var array_variant_type_data =  JSON.stringify(array_variant_for_product);
     
    if(selected_product_id != "" && selected_variant_id !="" && ((default_variant_type_id_check != undefined && default_variant_type_id !="") || (default_variant_type_id_check == undefined && default_variant_type_id =="")) && variant_select_type_status == true  && (variant_select_limit != "" || variant_select_limit == "")&& array_variant_for_product.length != 0 && check_price_is_fill !="" && check_price_is_fill == true){

         // Ajax-------SATRT------------
            $.ajax({
                    url: BASE_URL+'admin/add_variant_for_product/',
                    data: { 
                        selected_product_id: selected_product_id,selected_variant_id:selected_variant_id,default_variant_type_id:default_variant_type_id,variant_select_type:variant_select_type,json_of_variant_type:array_variant_type_data,variant_select_limit:variant_select_limit,add_edit_mode:add_edit_mode,is_variant_mandatory:is_variant_mandatory
                    },
                    type: 'post',
                    success: function(response){
                        if(response == 1){
                            $('#add_edit_variant_in_product').modal('hide');
                            $('#view_product_variant').trigger('click');
                            
                            //reset variant input
                             $('#add_edit_variant_in_product').find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
                             $('#variant_select_limit').val('');
                             $('#variant_select_limit').attr('disabled');
                             $('#SelectvarianttypeDropdown').empty();
                             $('#selected_variant_type_for_add_price').empty();
                             $("#single_select").prop("checked", true);
                             $("#no_mandatory").prop("checked", true);
                             $('#give_select_variant_limit').addClass('d-none');
                         }
                       
                       if(response == 0){
                          swal('Oops...', 'Internal server error', 'error');
                       }

                        if(response == 2){
                         swal('Oops...', 'You are missing a required field ', 'error');
                       }
                    },
                    
            });
            // Ajax-------END------------
    }
});
//Submit Variant for product -----------------------END--------------------------

// delete seleted variant which is choosed for product-----START------
$('body').on('click', '.delete_selected_product_variant', function() {
     var selected_variant_id = $(this).attr('variant_id');
     var selected_product_id = $(this).attr('product_id');
    
        swal({
            title: "Are you sure to delete this variant permanently?",
            text: "Once deleted, You will not be able to recover the action!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              
                   // Ajax-------SATRT------------
                   $.ajax({
                      url: BASE_URL+'admin/delete_selected_product_variant',
                      data: { 
                          selected_variant_id: selected_variant_id,selected_product_id:selected_product_id
                      },
                      type: 'post',
                      success: function(response){
                        
                          if(response == 1){
                              $( "#show_product_variant" ).load(BASE_URL+'admin/select_variant_product_detail/'+selected_variant_id+'/'+selected_product_id+'/0');
                           }
                         
                         if(response == 0){
                            swal('Oops...', 'Internal server error', 'error');
                         }
                      },
                      
                  });
                  // Ajax-------END------------
            }
          });
});
// delete seleted variant which is choosed for product-----END------

//close variant name edit modal-------------------START------------------
$('body').on('click', '.close_edit_variant_name_modal', function() {
 
      $('#edit_variant_name').modal('hide');
      $('#add_edit_variant_in_product').modal('show');
});
//close variant name edit modal-------------------END------------------

//edit variant name -----------------START------------------
$('body').on('click', '.edit_variant_name', function() {
    $('#add_edit_variant_in_product').modal('hide');
    $( "#variant_table_data" ).load(BASE_URL+'admin/get_varaint_name_for_edit/');
    
});
  $('body').on('click', '.edit_variant', function() {

          var edit_variant_id = $(this).attr('edit_variant_id');
          // hide input box and show variant name if click on other edit button 
          $('.variant_name #variant_span').removeClass('d-none');
          $('.variant_name #variant_edit_input').addClass('d-none');
         
          // when click on edit button then user can edit name at same place where is showing variant name only single value can edit at one time
          $('#variant_name_'+edit_variant_id+' #variant_span').addClass('d-none');
          $('#variant_name_'+edit_variant_id+' #variant_edit_input').removeClass('d-none');
  });
  // close/ cancel edit variant input----------START-----------
  $('body').on('click', '.edit_variant_close', function() {

          var edit_variant_id = $(this).attr('edit_variant_id');

          // hide input box and show variant name if click on other edit button 
          $('#variant_name_'+edit_variant_id+' #variant_span').removeClass('d-none');
          $('#variant_name_'+edit_variant_id+' #variant_edit_input').addClass('d-none');
  });
  // close/ cancel edit variant input----------END-----------

$('body').on('click', '#edit_vairant', function() {
        var edit_variant_id = $(this).attr('edit_variant_id');
       

         var edit_vairant_name = $('#edit_vairant_name_'+edit_variant_id).val();


       if(edit_vairant_name == ""){
            $('#edit_vairant_name').css('border-color','red');
            $('#unfill_edit_variant_name').text('Please Fill variant name');
        }else{
             $('#edit_vairant_name').css('border-color','#ccc');
             $('#unfill_edit_variant_name').text('');
        }  

        if(edit_vairant_name != "" && edit_variant_id != ""){
          
              // Ajax-------SATRT------------
                    $.ajax({
                    url: BASE_URL+'admin/edit_variant_name_submit/',
                    data: { 
                        edit_vairant_name: edit_vairant_name,edit_variant_id:edit_variant_id
                    },
                    type: 'post',
                    success: function(response){
                      
                        if(response == 1){
                           $( "#variant_table_data" ).load(BASE_URL+'admin/get_varaint_name_for_edit');
                                $('#variant_name').val('');

                           //selection div reload after edit     
                            $( "#select_variant_for_product" ).load(BASE_URL+'admin/get_all_variant_selection');
                         }
                       
                       if(response == 0){
                          swal('Oops...', 'Internal server error', 'error');
                       }

                        if(response == 2){
                         swal('Oops...', 'You are missing a required field ', 'error');
                       }

                       if(response == 3){
                         swal('Oops...', 'Variant name is already exists!', 'error');
                       }

                        if(response == 4){
                            //swal("Nothing has changed!");
                            $( "#variant_table_data" ).load(BASE_URL+'admin/get_varaint_name_for_edit');
                       }
                    },
                    
                });
                // Ajax-------END------------
        }else{
            swal('Oops...', 'You are missing a required field or may be Internal server error', 'error');
        }
});

//edit variant name -----------------END------------------

// For Search variant-------------------START-----------------------
$(document).ready(function(){
  $("#search_variant").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#variant_table_data tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
// For Search variant-------------------END-----------------------

// For Search variant-------------------START-----------------------
$(document).ready(function(){
  $("#search_variant_type").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#variant_type_table_data tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
// For Search variant-------------------END-----------------------


//Edit variant type name -------------------START-------------------------
$('body').on('click', '.edit_variant_type_name_btn', function() {

          var edit_variant_type_id = $(this).attr('edit_variant_type_id');
          // hide input box and show variant name if click on other edit button 
          $('.variant_type_name #variant_type_span').removeClass('d-none');
          $('.variant_type_name #variant_type_edit_input').addClass('d-none');
         
          // when click on edit button then user can edit name at same place where is showing variant name only single value can edit at one time
          $('#variant_type_name_'+edit_variant_type_id+' #variant_type_span').addClass('d-none');
          $('#variant_type_name_'+edit_variant_type_id+' #variant_type_edit_input').removeClass('d-none');
  });


  // close/ cancel edit variant input----------START-----------
  $('body').on('click', '.edit_variant_type_close', function() {

          var edit_variant_type_id = $(this).attr('edit_variant_type_id');

          // hide input box and show variant name if click on other edit button 
          $('#variant_type_name_'+edit_variant_type_id+' #variant_type_span').removeClass('d-none');
          $('#variant_type_name_'+edit_variant_type_id+' #variant_type_edit_input').addClass('d-none');
  });
  // close/ cancel edit variant input----------END-----------

$('body').on('click', '.edit_variant_type_name', function() {
   $('#add_edit_variant_in_product').modal('hide');

   //select variant for edit variant type
   $("#select_variant_for_edit_type").load(BASE_URL+'admin/get_varaint_name_for_edit/1');
     setTimeout(function(){
        var first_variant_id = $("#select_variant_for_edit_type option:first").val();
           $( "#variant_type_table_data" ).load(BASE_URL+'admin/get_varaint_type_name_for_edit/'+first_variant_id);
        }, 1000);
});

$('body').on('change', '#select_variant_for_edit_type', function() {
    var variant_id = $(this).val();
    $( "#variant_type_table_data" ).load(BASE_URL+'admin/get_varaint_type_name_for_edit/'+variant_id);
});

$('body').on('click', '#edit_variant_type_save', function() {

        var edit_variant_type_id = $(this).attr('edit_variant_type_id');
        var selected_variant_id = $(this).attr('variant_id');

        var edit_vairant_type_name = $('#edit_vairant_type_name_'+edit_variant_type_id).val();

         if(edit_vairant_type_name == ""){
              $('#edit_vairant_type_name').css('border-color','red');
              $('#unfill_edit_variant_type_name').text('Please Fill variant name');
          }else{
               $('#edit_vairanttype__name').css('border-color','#ccc');
               $('#unfill_edit_variant_type_name').text('');
          }

        if(edit_vairant_type_name != "" && edit_variant_type_id != ""){
          
              // Ajax-------SATRT------------
                $.ajax({
                    url: BASE_URL+'admin/edit_variant_type_name_submit/',
                    data: { 
                        edit_vairant_type_name: edit_vairant_type_name,edit_variant_type_id:edit_variant_type_id
                    },
                    type: 'post',
                    success: function(response){
                      
                        if(response == 1){
                           $( "#variant_type_table_data" ).load(BASE_URL+'admin/get_varaint_type_name_for_edit/'+selected_variant_id);
                                $('#variant_name').val('');

                           //selection div reload after edit     
                           $( "#select_variant_type_for_product" ).load(BASE_URL+'admin/get_all_variant_type_selection/'+selected_variant_id+'');     
                         }
                       
                       if(response == 0){
                          swal('Oops...', 'Internal server error', 'error');
                       }

                        if(response == 2){
                         swal('Oops...', 'You are missing a required field ', 'error');
                       }

                       if(response == 3){
                        // swal('Oops...', 'Variant name is already exists!', 'error');
                         $('#edit_vairant_type_name').css('border-color','red');
                         $('#unfill_edit_variant_type_name').text('Type name is alredy exist');
                       }

                        if(response == 4){
                            //swal("Nothing has changed!");
                            $( "#variant_type_table_data" ).load(BASE_URL+'admin/get_varaint_type_name_for_edit/'+selected_variant_id);
                       }
                    },
                    
                });
                // Ajax-------END------------
        }else{
           // swal('Oops...', 'You are missing a required field or may be Internal server error', 'error');
        }
});

//close variant name edit modal-------------------START------------------
  $('body').on('click', '.close_edit_variant_type_name_modal', function() {
      $('#edit_variant_type_name').modal('hide');
      $('#add_edit_variant_in_product').modal('show');
  });
//close variant name edit modal-------------------END------------------

//edit variant name -----------------END------------------

//Edit variant type name -------------------END-------------------------

//close available variant modal ----------------START------------
  $('body').on('click', '.close_edit_delete_variant_modal', function() {
      $('#view_avaible_variant_in_product').modal('hide');
      $('#add_edit_variant_in_product').modal('show');
  });

//close available variant modal ----------------END------------

//close view_avaible_variant_in_product  modal wehn click on edit-----START----------
$('body').on('click', '.close_view_variant_modal', function() {
    $('#view_avaible_variant_in_product').modal('hide');
    $('.variant_modal_title').text('Edit variants/add-ons');

    var selected_variant_id = $(this).attr('variant_id');
    var selected_product_id = $(this).attr('product_id');

    // show selected(checked variant)
    $( "#select_variant_for_product" ).load(BASE_URL+'admin/select_variant_product_detail/'+selected_variant_id+'/'+selected_product_id+'/1');// 1 passing for edit mode check

    //show selected (checked) varaint type accroding to varaint id
    $( "#select_variant_type_for_product" ).load(BASE_URL+'admin/get_all_variant_type_selection/'+selected_variant_id+'/'+selected_product_id+'/1');// 1 for edit mode 

    $('#selected_variant_type_for_add_price').attr('add_edit_mode','1');
    $('#selected_variant_id_for_product').val(selected_variant_id);

    //show varaint type price  accroding to variant  type id and product id 
      // Ajax-------SATRT------------
     
        $("#selected_variant_type_for_add_price").empty();
        $.ajax({
                url: BASE_URL+'admin/get_varaint_type_data_for_edit/',
                data: { 
                    selected_variant_id:selected_variant_id,selected_product_id:selected_product_id
                },
                type: 'post',
                success: function(response){
                    if(response != 0){
                       var json = JSON.parse(response,true);
                         $.each(json,function(index,json){
                          // for append varaint type detail
                            common_variant_type_add_price_append(json.variant_type_name,json.variant_type_id,json.variant_type_price,json.default_variant_status,1);// 1 for edit mode check
                            
                          // for show variant type is single or multi --start---
                           if(json.single_select == 1){// single
                                $("#single_select").prop("checked", true);
                                $("#multi_select").prop("checked", false);

                              }else if(json.single_select == 2){// multi

                                $("#single_select").prop("checked", false);
                                $("#multi_select").trigger('click');// .prop("checked", true);

                               // if multi then check and show is there any selection limit available

                                 if(json.multi_select_limit > 0){// limit available
                                    $('#select_limit_of_variant').prop("checked", true);
                                    $('#variant_select_limit').val(json.multi_select_limit);
                                    $('#variant_select_limit').removeAttr('disabled');
                                 }

                                 if(json.default_variant_status == 1){
                                    $('#show_clear_default_btn').removeClass('d-none');
                                 }

                              }
                            // for show variant type is single or multi --end---

                            // is variant mandatory ---------- Start--------------
                               if(json.is_mandatory == 1){// mandatory
                                    $("#yes_mandatory").prop("checked", true);
                                    $("#no_mandatory").prop("checked", false);
                               }else{//not  mandatory
                                    $("#yes_mandatory").prop("checked", false);
                                    $("#no_mandatory").prop("checked", true);
                               }
                            // is variant mandatory ---------- End--------------

                        });  
                    }
                },
        });
        // Ajax-------END------------
});
//close view_avaible_variant_in_product  modal wehn click on edit-----END----------

//Show available vairants in selected product --------------------------START--------------------
  $('body').on('click', '.view_avaible_variant_in_product', function() {
       $('#add_edit_variant_in_product').modal('hide');
       var selected_product_id = $('#selected_product_for_add_variant').val();
       var selected_variant_id = 0;
      $( "#show_product_variant" ).load(BASE_URL+'admin/select_variant_product_detail/'+selected_variant_id+'/'+selected_product_id+'/0');
  });
//Show available vairants in selected product --------------------------END--------------------


//product page - variant--------------------END-----------------------------


//order page-------------------------START---------------------------

 //Auto reload orders list in second  ----set time interval ------START-----
 var is_this_orders_page;
/* if(is_this_orders_page == 1){
     setInterval(function(){ $( "#all_Orders_table" ).load(BASE_URL+'admin/orders/2') }, 60000);
 }
*/
 var is_customer_detail_page;
 var customer_id_for_order_detail_get;
 if(is_customer_detail_page ==1){//we are handling order details and customer order detail on user_details page from one controller  
     $( "#customer_orders_table" ).load(BASE_URL+'admin/orders/2/'+customer_id_for_order_detail_get+'');
 }

//Auto reload orders list in second  ----set time interval ------END-----

// Order Search  filter----------------------START----------------------------
$(".order_search_key").keydown(function(e){//on enter
      if(e.keyCode === 13)
      {
         
        $('#search_order_list_data').trigger( "click");
      }
  });
$('body').on('click', '#search_order_list_data,.export_orders_csv', function() {


      var search_mode = $(this).attr('search_mode');// 1  means click on search button  , 2  means click on export button

      var from_date = $('#fromdate').val();
      var to_date = $('#todate').val();
      var delivery_handle_by = $('#delivery_handle_by').val();
      var payment_mode = $('#payment_mode').val();
      var paid_status = $('#paid_status').val();
      var is_paid_to_restaurant = $('#is_paid_to_restaurant').val();
      var order_status = $('#order_status').val();
      var search_restaurant_id = $('#search_restaurant_id').val();
      var search_customer_id = $('#search_customer_id').val();
      var order_accept_type = $('#order_accept_type').val();
      var business_category_id = $('#business_category_id').val();
      var is_cutlery_needed = $('#is_cutlery_needed').val();
      var is_promocode_auto_applied = $('#is_promocode_auto_applied').val();
      var is_promocode_auto_applied_on_delivery = $('#is_promocode_auto_applied_on_delivery').val();
      
      var search =$('.order_search_key').val();
      
      if(from_date == '')
      {
         from_date= "all";
      }

      if(to_date==''){
         to_date= "all";
      }

     if(delivery_handle_by==''){
         delivery_handle_by= "all";
      }

    if(payment_mode==''){
         payment_mode= "all";
      }

      if(paid_status==''){
         paid_status= "all";
      }


      if(is_paid_to_restaurant == '' || is_paid_to_restaurant == undefined){// only Super admin can see and update // if restaurant is logged in that  time its contain undefine  //means its will be value be all when
         is_paid_to_restaurant= "all";
      }

     if(order_status==''){
         order_status= "all";
      }

      if(search_restaurant_id==''){
         search_restaurant_id= "all";
      }

     
    if(search_restaurant_id === undefined || search_restaurant_id === null){// merchant and role 2 logged in
        search_restaurant_id = selected_restaurant_id;//selected_restaurant_id varaible from orders.php
    }else if(search_restaurant_id == ''){// super admin is logged in
       search_restaurant_id = "all";
    }

     if(search_customer_id==''){
         search_customer_id= "all";
      }

     if(order_accept_type==''){
         order_accept_type= "all";
      }

     if(business_category_id==''){
         business_category_id= "all";
      }

     if(is_cutlery_needed==''){
         is_cutlery_needed= "all";
      }
     
     if(is_promocode_auto_applied==''){
         is_promocode_auto_applied= "all";
      }

      if(is_promocode_auto_applied_on_delivery==''){
         is_promocode_auto_applied_on_delivery= "all";
      }
      
      if(search == '')
      {
        search = "all";
      }else{
        search = search.trim();
      }

    if(from_date!='all' || to_date !='all'  || delivery_handle_by !='all' || payment_mode!='all' || paid_status!='all' || is_paid_to_restaurant!='all' || order_status!='all' || search_restaurant_id!='all' || search_customer_id!='all' || order_accept_type!='all' || business_category_id!='all' || is_cutlery_needed!='all' || is_promocode_auto_applied!='all' || is_promocode_auto_applied_on_delivery!='all' || search !='all'){

            if(search_mode == 1){
                var controller_path = 'orders/0/0'; //for order search
            }else if(search_mode == 2){
                var controller_path = 'Export_Order_CSV'; //for order export
            }
        
             window.location.replace(BASE_URL+'admin/'+controller_path+'/'+from_date+'/'+to_date+'/'+delivery_handle_by+'/'+payment_mode+'/'+paid_status+'/'+is_paid_to_restaurant+'/'+order_status+'/'+search_restaurant_id+'/'+search_customer_id+'/'+order_accept_type+'/'+business_category_id+'/'+is_cutlery_needed+'/'+is_promocode_auto_applied+'/'+is_promocode_auto_applied_on_delivery+'/'+search+'/');//load only table on a promo_code_taurant page 
      }else{
            if(search_mode == 1){
                var controller_path = 'orders';//for order search
            }else if(search_mode == 2){
                var controller_path = 'Export_Order_CSV'; //for order export
            }
            window.location.replace(BASE_URL+'admin/'+controller_path+'/');
      }
  });

//Order  Search filter-----------------------END----------------------------


//Change status OF ORDER -----------------------START---------------------
$('body').on('change', '.order_status', function() {
// alert(11111);
    // it work for orers page for all order id's and work for order-single page too for single order id

     var new_order_table_url = order_table_url.replace("table","2");
       /* if($(this).attr('order_single_status') != undefined || $(this).attr('order_single_status') == null && $(this).attr('order_single_status') == 1){//when change status from on order-single page
            var new_order_table_url =  BASE_URL+'admin/order_single/'+order_id+'';

        }else if($(this).attr('order_single_status') == undefined){
         
        }    var new_order_table_url = order_table_url.replace("table","2");// if action mode then it will repalce table to  2 // it is becouse when delete success then it will give new url which will comapre segment with serach filter
      ////by default table value is 0
*/
      

     
      var order_input_name  = $(this).attr('name');

      var order_status_value = $(this).val();// 0 - Placed and Pending 1 - Accepted 2 - Rejected 3 - Dispatched 4 - Cancelled 5 - Completed rest we will mention soon(if any), 6 - Delete
      
      var order_input_id_array = order_input_name.split("_");
      var order_id = order_input_id_array[2];

      var order_number_id_ = '#order_number_id_'+order_id;
      var final_order_number_id = $(order_number_id_).text();
        
       if(final_order_number_id == "") {
            final_order_number_id = order_number_id;
       }
       swal({
              title: 'Wait..',
              text: "Please wait and Don't do any action while we are processing your request!",
              type: 'Wait',
              buttons: false,
              closeOnClickOutside: false,
              confirmButtonText: 'Yes, delete it!'
            });
          // Ajax-------SATRT------------
            $.ajax({
            url: BASE_URL+'admin/edit_order_status/',
            data: { 
                order_id: order_id,order_status_value:order_status_value
            },
            type: 'post',
            success: function(response){
              console.log("RESPONSE"+response);
                if(response == 1){
                //sucess
                
                 if($('.order_status').attr('order_single_status') == 1){
                   window.location.href= ''+BASE_URL+'admin/order_single/'+order_id+'/';
                 }else{
                    setTimeout(function(){
                        $( "#all_Orders_table" ).load(new_order_table_url);
                        }, 500); //refresh every 2 seconds*/
                     }
                    swal.close();
                 }
               
               if(response == 0){
                  swal('Oops...', 'Internal server error', 'error');
               }

                if(response == 2){
                  swal('Oops...', 'Order id is missing', 'error');
               }
            },
            
        });
        // Ajax-------END------------
});

///Change status Order  (Customer/Merchant)-----------------------END----------------------

//Delete order  -----------------------START---------------------
$('body').on('click', '.order_delete', function() {

      var new_order_table_url = order_table_url.replace("table","2");// if action mode then it will repalce table to  2 // it is becouse when delete success then it will give new url which will comapre segment with serach filter

      var order_input_id  = $(this).attr('id');//input id

      var order_input_id_array = order_input_id.split("_");
      var order_id = order_input_id_array[2];

      var order_number_id_ = '#order_number_id_'+order_id;
      var final_order_number_id_ = $(order_number_id_).text();

        swal({
            title: "Are you sure to delete this order permanently?",
            text: "Once deleted, You will not be able to recover the action!  Order ID - "+final_order_number_id_+"",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              
                  // Ajax-------SATRT------------
                    $.ajax({
                    url: BASE_URL+'admin/delete_order',
                    data: { 
                        order_id: order_id
                    },
                    type: 'post',
                    success: function(response){
                      
                        if(response == 1){
                         
                        /* swal("Poof! Your imaginary file has been deleted!", {
                            icon: "success",
                          });*/

                        /* setTimeout(function(){
                               $( "#all_Orders_table" ).load(new_order_table_url);
                            }, 2000); //refresh every 2 seconds*/

                          $( "#all_Orders_table" ).load(new_order_table_url);
                         }
                       
                       if(response == 0){
                          swal('Oops...', 'Internal server error', 'error');
                       }
                    },
                    
                });
                // Ajax-------END------------
            }
          });
      
});

//Delete order -----------------------END----------------------

// Change status of is_paid_to_restaurant -----------------START--------------
$('body').on('click', '.is_paid_to_restaurant_status', function() {

      var new_order_table_url = order_table_url.replace("table","2");// if action mode then it will repalce table to  2 // it is becouse when delete success then it will give new url which will comapre segment with serach filter
      ////by default table value is 0
     
      var selected_order_id  = $(this).attr('selected_order_id');

      var is_paid_to_restaurant_status_value = $(this).val();// //For is paid to restauant "Indicates whether admin paid to restaurant manually. 1 - Yes 2 -No(means pending)"

     

      var order_number_id_ = '#order_number_id_'+selected_order_id;
      var final_order_number_id = $(order_number_id_).text();

      // Ajax-------SATRT------------
        $.ajax({
        url: BASE_URL+'admin/is_paid_to_restaurant_status_update/',
        data: { 
            order_id: selected_order_id,is_paid_to_restaurant_status_value:is_paid_to_restaurant_status_value
        },
        type: 'post',
        success: function(response){
          
            if(response == 1){
               $( "#all_Orders_table" ).load(new_order_table_url);
           }
           if(response == 0){
              swal('Oops...', 'Internal server error', 'error');
           }

            if(response == 2){
              swal('Oops...', 'Order id is missing', 'error');
           }
        },
        
    });
    // Ajax-------END------------
      
});
// Change status of is_paid_to_restaurant -----------------END--------------

//Order single page  - Add and Remove orderd item-----------------START--------------

//--------------Show orderd items by customer ------------------START--------------

//delete temp orderd data which is insert when click on customoze button -----START---
//at the time when admin will close/cancel popup modal  //  or dont want to customize orderd data
$('body').on('click', '.delete_temp_order_data', function() {
    $( "#selected_products_for_order" ).load(BASE_URL+'admin/delete_temp_customize_order_data/'+order_id+'');

    $('#show_loader_cancel').removeClass('d-none');
    $('#confirm_order_place_order').addClass('d-none');

     setTimeout(function(){
         
          $('#confirm_order_place_order').addClass('d-none');
          $('#close_customoize_modal').trigger('click');
    }, 2000); //refresh every 2 seconds*/
});

//delete temp orderd data which is insert when click on customoze button -----END---

//show temprory storage orderd data for customize
$('body').on('click', '.add_remove_orderd_items_modal_btn', function(e) {
     setTimeout(function(){
      $('#show_loader_cancel').addClass('d-none');
      $( "#selected_products_for_order" ).load(BASE_URL+'admin/show_selected_products_for_final_order/'+order_id+'/1');//load in modal - add_remove_orderd_items_modal
      // mode 1 means first load data on when modal open data will get from the order_product_details table
      //set session first time accordind to exist order data 
      // when add remove varaints or product then session  will update 
     
     }, 2000); //refresh every 2 seconds*/
     setTimeout(function(){
        $('#confirm_order_place_order').removeClass('d-none');
      }, 2500); //refresh every 2.5 seconds*/
});

//Show select products for order when select any product--------START---------
$('body').on('click', '.select_product_id_for_order', function() {

      var product_id = $(this).val();

      var selected_product_status = $(this).attr('selected_product_status');// 1 = selected for order , 0 = not selected for order //same as 2  for delete when showing fo
     
      var unit_price = $(this).attr('unit_price'); 
     $.ajax({
        url: BASE_URL+'admin/set_temporary_data_for_order_customization',
        data: { 
             selected_product_id:product_id,selected_product_status:selected_product_status,selected_order_id:order_id,product_unit_price:unit_price
        },
        type: 'post',
        success: function(response){
          
            if(response == 1){

                if(selected_product_status == 0){
                    //Product has been removed from the selection
                    $('#product_id_'+product_id).attr('selected_product_status','1');
                }

                if(selected_product_status == 1){
                    //Product has been added for order
                    $('#product_id_'+product_id).attr('selected_product_status','0');
                }
                 
                //show selected product for order
                $( "#selected_products_for_order" ).load(BASE_URL+'admin/show_selected_products_for_final_order/'+order_id+'/2');
             }
           
           if(response == 0){
              swal('Oops...', 'Internal server error', 'error');
           }
        },
        
    });
    // Ajax-------END------------

    /* $( "#selected_products_for_order" ).load(BASE_URL+'admin/show_selected_products_for_final_order/'+order_id+'/'+product_order_data+'/2');//load select items list for order*/
});
//Show select products for order when select any product--------END---------

//close upper modal
$('body').on('click', '.close_upper_variant_modal', function() {
    $('#select_variant_modal').modal('hide');
    $('body').addClass('modal-open');
 });
//Show Variants accrording to selected product id------------------------START----------
$('body').on('click', '.select_variant_for_order', function(e) {
        var selected_product_id = $(this).attr('selected_product_id');
      
        $( "#select_product_variants_for_order" ).load(BASE_URL+'admin/select_product_variants_for_order/'+selected_product_id);
});
//Show Variants accrording to selected product id------------------------END----------


//remove radio selection  from radio when customize order product variants------START-----

//remove radio selection  from the varinat when its is radio button and dont wana choose thar vairant or variant type of this variant in to selected product
$('body').on('click', '.remove_selection_from_radio', function(e) {

      var selected_variant_id = $(this).attr('variant_id');
      var selected_product_id = $(this).attr('product_id');

     // Ajax-------SATRT------------
        $.ajax({
        url: BASE_URL+'admin/set_temporary_data_for_order_customization',
        data: { 
           selected_variant_id:selected_variant_id,selected_product_id:selected_product_id,selected_order_id:order_id,remove_selection_from_radio:'1'
        },
        type: 'post',
        success: function(response){
          
            if(response == 1){

               /*  swal("Variant Types has been removed from the selection", {
                    icon: "success",
                  });
                 */
                $( "#select_product_variants_for_order" ).load(BASE_URL+'admin/select_product_variants_for_order/'+selected_product_id);//load in modal  = select_product_with_variant_modal

                    $( "#selected_products_for_order" ).load(BASE_URL+'admin/show_selected_products_for_final_order/'+order_id+'/2');//load in modal - add_remove_orderd_items_modal
                    //load in modal - add_remove_orderd_items_modal
                    // mode 2 means items data add or remove then data will be show accroding selected product id  and variant id 
           }
           if(response == 0){
              swal('Oops...', 'Internal server error', 'error');
           }
        },
        
    });
    // Ajax-------END------------

});

//remove radio selection  from radio when customize order product variants------END-----

//save  temporary customize order data according to  select products and variant -----START----
      var total_mendatory_variant = 0;
    //check mendatory variant when close selection variant modal -----START----
    $('body').on('click', '#check_mendetory_variant', function(e) {
         var check_mendatory_is_checked = $(this).attr('check_mendatory_is_checked'); // if 2 meand no changed and modal can be close , is do any changes (checked / unchecked then its value will be 0 or 1 and only in case 0 or 2 modal will be close)
       
          
          $('.order_product_change_status').each(function () { 
                var is_mandatory = $(this).attr('is_mandatory'); 
                total_mendatory_variant = $(this).attr('total_mendatory_variant'); 

                var mandatory_variant_id = $(this).attr('variant_id_for_selection_count');
                var is_mandatory_flag = $('.check_multi_limit_'+mandatory_variant_id).attr('is_mandatory_flag'); //check purpose that is checked on not
                var variant_name = $('#variant_name_'+mandatory_variant_id).text();
                
                if(is_mandatory_flag != undefined && is_mandatory_flag == 1 && total_mendatory_variant>0)  {
                    //alert(is_mandatory+'== for '+ mandatory_variant_id+'variant_name_'+variant_name);
                    swal('Oops...', ''+variant_name+' variant is mandatory', 'warning');

                }
          });

          //check mendaory variant is selected if any avaialable
          if(total_mendatory_variant == 0 && (check_mendatory_is_checked == 2 || check_mendatory_is_checked == 0)){
                $('.close_upper_variant_modal').trigger('click'); 
                //alert(total_mendatory_variant+'=='+check_mendatory_is_checked);
          }else{

          }
          

    });
    //check mendatory variant when close selection variant modal -----END----

 
$('body').on('click', '.order_product_change_status', function(e) {

     var order_product_change_status = $(this).attr('order_product_change_status');// 1 = selected for order , 0 = not selected for order //same as 2  for delete when showing for final palce order

     var check_variant_selection_if_single = $(this).attr('check_variant_selection_if_single'); //value if 1 then it is "Single select" then selection type will be work as radio button, value if 2 then "multi select" then selection type will be work as checkbox button

     // if selection is multi then we need to check it's limit  ---start----
     var select_limit = $(this).attr('select_limit');
     //alert('limit'+select_limit);
    var variant_id_for_selection_count = $(this).attr('variant_id_for_selection_count');
    var multi_checked_total = $(this).attr('multi_checked_total');// check how many check or selected variant at the time of order
   
    var count_check_multi_variant = multi_checked_total;
    $('.check_multi_limit_'+variant_id_for_selection_count+':checked').each(function () { 
                
        var check_variant_id = $(this).attr('variant_id_for_selection_count');

        if(count_check_multi_variant > select_limit && select_limit>0){
           //$('#select_limit_error').text('You can select only '+select_limit+' variant types');
            swal('Oops...', 'You can select only '+select_limit+' variant types', 'warning');
        }

        count_check_multi_variant++;// count newest checked value
    });
     // if selection is multi then we need to check it's limit  ---end----


   //is mendetory flag value change is mendetory variant is selected/ checked---start--
   //if any one is selected then mendotory checked value will be 0 (ex -  variant is mendotory and any one option is selected then  mendotory falg value will be 0 other wise 1)
   if ($(this).prop('checked')==true){ 
        //alert('checked');
        $(this).attr('is_mandatory_flag','0');
        $('#check_mendetory_variant').attr('check_mendatory_is_checked','0');// mandatory variant is  selected or not mendatory

         if(total_mendatory_variant >0){
              total_mendatory_variant--;
         }else{
             total_mendatory_variant = total_mendatory_variant;
         }
         $(this).attr('total_mendatory_variant',total_mendatory_variant);
          
    }else
    {
      
      var is_mandatory = $(this).attr('is_mandatory'); 
      if(is_mandatory == 1){
         $(this).attr('is_mandatory_flag','1');
         $('#check_mendetory_variant').attr('check_mendatory_is_checked','1');// mandatory variant is not selected or its  mendatory

       
         total_mendatory_variant++;
         //alert(total_mendatory_variant);
        $(this).attr('total_mendatory_variant',total_mendatory_variant);
      }else{
         $(this).attr('is_mandatory_flag','0');
         $('#check_mendetory_variant').attr('check_mendatory_is_checked','0');// mandatory variant is  selected or not mendatory

         
          $(this).attr('total_mendatory_variant','0');
     }
       
        //alert('un checked');
    }

   //is mendetory flag value change is mendetory variant is selected/ checked---end--
        

     if(order_product_change_status == 2){// delete product from selected items
        var product_id_for_delete = $(this).attr('product_id');//because it is not input
        product_id_with_variant_id = "";
     }else{//(when will  do variant type select or unselect)
        var product_id_for_delete = "";
        var product_id_with_variant_id = $(this).val();
     }

     var product_id_expload = product_id_with_variant_id.split(",");
     var product_id = product_id_expload[0].split("product_id_");

      var variant_type_price = $(this).attr('variant_type_price');

     //when delete orderd items/product  actully we need to  show confirm that we take seprate
     if(order_product_change_status == 2){
           swal({
                  title: "Are you sure to delete this Item permanently?",
                  text: "Once clicking the confirm and checkout button, you will not be able to recover the action!!",
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
            }).then((willDelete) => {
              if (willDelete) {
                        // Ajax-------SATRT------------
                            $.ajax({
                            url: BASE_URL+'admin/set_temporary_data_for_order_customization',
                            data: { 
                                product_id_with_variant_id: product_id_with_variant_id,order_product_change_status:order_product_change_status,selected_order_id:order_id,variant_type_price:variant_type_price,product_id_for_delete:product_id_for_delete,check_variant_selection_if_single:check_variant_selection_if_single
                            },
                            type: 'post',
                            success: function(response){
                              
                                if(response == 1){
                                    // when will delete items
                                    if(order_product_change_status == 2){
                                         /* swal("Iteam Deleted!", {
                                            icon: "success",
                                          });*/
                                          //uncheck product from checkbox under search product dropdown
                                          $("#product_id_"+product_id_for_delete).prop("checked", false);
                                          $('#product_id_'+product_id_for_delete).attr('selected_product_status','1');
                                    }
                                     
                                     $( "#selected_products_for_order" ).load(BASE_URL+'admin/show_selected_products_for_final_order/'+order_id+'/2');//load in modal - add_remove_orderd_items_modal
                                        //load in modal - add_remove_orderd_items_modal
                                        // mode 2 means items data add or remove then data will be show accroding selected product id  and variant id 
                                 }
                               
                               if(response == 0){
                                  swal('Oops...', 'Internal server error', 'error');
                               }
                            },
                            
                        });
   
                  // Ajax-------END------------
              } else {
                //swal("Your Data is safe!");
              }
            });

    }else{
        // when will  do variant  type select or unselect
        // Ajax-------SATRT------------
        $.ajax({
        url: BASE_URL+'admin/set_temporary_data_for_order_customization',
        data: { 
            product_id_with_variant_id: product_id_with_variant_id,order_product_change_status:order_product_change_status,selected_order_id:order_id,variant_type_price:variant_type_price,product_id_for_delete:product_id_for_delete,check_variant_selection_if_single:check_variant_selection_if_single
        },
        type: 'post',
        success: function(response){
          
            if(response == 1){
                if(order_product_change_status == 0){
                     /* swal("Variants has been removed from the selection", {
                        icon: "success",
                      });*/
                }

                if(order_product_change_status == 1){
                   /* swal("Variants have been added to the order", {
                        icon: "success",
                      });*/
                }
                
                 setTimeout(function(){

                    $( "#select_product_variants_for_order" ).load(BASE_URL+'admin/select_product_variants_for_order/'+product_id[1]);//load in modal  = select_product_with_variant_modal

                    $( "#selected_products_for_order" ).load(BASE_URL+'admin/show_selected_products_for_final_order/'+order_id+'/2');//load in modal - add_remove_orderd_items_modal
                    //load in modal - add_remove_orderd_items_modal
                    // mode 2 means items data add or remove then data will be show accroding selected product id  and variant id 

                }, 2000); //refresh every 2 seconds*/
             }
           
           if(response == 0){
              swal('Oops...', 'Internal server error', 'error');
           }
        },
        
    });
    // Ajax-------END------------
    }
});
 
//save  temporary customize order data according to  select products and variant -----End----

//Calcucation Price of select products for order, if quanity increase or decrease ----------------START------------

    //increase-----------------------
    $('body').on('click', '.plus_qunatity', function(e) {
        var product_id = $(this).attr('product_id');// same value puted as id in to quantity input for geting specific value
  
        var product_unit_price = parseInt($('.product_price_'+product_id).text());
        var quantity = parseInt($('.quantity_'+product_id).val());
        var final_quantity = parseInt(quantity+1);// pluse 1  beacouse bydefault it value 1 and total value is showing for 1 from php side
      
          
          var check_variant_has = $(".variant_type_price_"+product_id).text();
           var total_of_variant_type_price = 0;
         if(check_variant_has != undefined || check_variant_has!= null){
            
             $(".variant_type_price_"+product_id).each(function(){
                var variant_type_price = parseInt($(this).text());
                total_of_variant_type_price = total_of_variant_type_price+variant_type_price;
              });
         }
            
         var total_price_product = product_unit_price+total_of_variant_type_price;
          var product_total = total_price_product*final_quantity;
       
         $('#plus_minus_total_'+product_id).text(product_total);
        
    });

  //decrease
    $('body').on('click', '.minus_qunatity', function(e) {

        var product_id = $(this).attr('product_id');// same value puted as id in to quantity input for geting specific value
  
        var product_unit_price = parseInt($('.product_price_'+product_id).text());
        var quantity = parseInt($('.quantity_'+product_id).val());
        var final_quantity = parseInt(quantity-1);// pluse 1  beacouse bydefault it value 1 and total value is showing for 1 from php side
      
          if(final_quantity == 0){
             final_quantity = 1;
          }else{
            final_quantity = final_quantity;
          }

          var check_variant_has = $(".variant_type_price_"+product_id).text();
           var total_of_variant_type_price = 0;
         if(check_variant_has != undefined || check_variant_has!= null){
            
             $(".variant_type_price_"+product_id).each(function(){
                var variant_type_price = parseInt($(this).text());
                total_of_variant_type_price = total_of_variant_type_price+variant_type_price;
              });
         }
            
         var total_price_product = product_unit_price+total_of_variant_type_price;
         var product_total = total_price_product*final_quantity;
       
         $('#plus_minus_total_'+product_id).text(product_total);
        
    });
//Calcucation Price of select products for order,if quanity increase or decrease ----------------END------------
//---------------Show orderd items by customer ------------------END--------------

//Submit Final order ----------------------------------START--------
    // Products delete and  istert   ------------START-----------
    $('body').on('click', '#confirm_order_place_order', function(e) {

         var array_items_for_order = [];
          //var if_variant_is_available_then_selected_status = [];
          $(".total_amount_of_items").each(function(){

            var  selected_product_id = $(this).attr('product_id');

            var  product_unit_price = $(this).attr('product_unit_price');
            var  variant_is_available_total = $(this).attr('variant_is_available_total');
            var  check_variant_selection_if_single = $(this).attr('check_variant_selection_if_single');//1 - single select ,2 multi select (we need to check only variant insert time)

            var variant_type_check = $(".variant_type_price_"+selected_product_id).text();
           
                //check variant selected if available
              if(variant_is_available_total >0){

                //check variant is avaiable in product then  it should be selected
                 if(variant_type_check != "" && variant_type_check != undefined && variant_type_check != null ){
                     $(".variant_type_price_"+selected_product_id).each(function(){

                        var variant_type_price = $(this).text();
                        var variant_type_id = $(this).attr('variant_type_id');
                        var variant_id = $(this).attr('variant_id');

                        array_items_for_order.push({'product_id': selected_product_id,'product_quantity':$('.quantity_'+selected_product_id).val(),'product_unit_price':product_unit_price,'variant_type_id': variant_type_id,'variant_type_unit_price': variant_type_price,'variant_id':variant_id,'check_variant_selection_if_single':check_variant_selection_if_single});
                  });

                    //if_variant_is_available_then_selected_status.push(1);
                 }else{
                    //if_variant_is_available_then_selected_status.push(0);

                    //if in future  variant type is avaiable in product and it selecteon is complulsory then below push array(array_items_for_order.push....) code remove from here. only above "if_variant_is_available_then_selected_status.push(0);" will be avaialable here and uncomment this variable from every where under this code"
                    //also you need changes in controller  "confirm_and_update_place_order"
                     array_items_for_order.push({'product_id': selected_product_id,'product_quantity':$('.quantity_'+selected_product_id).val(),'product_unit_price':product_unit_price,'variant_type_id': 0,'variant_type_unit_price': 0,'variant_id':0,'check_variant_selection_if_single':check_variant_selection_if_single});  
                 }
              }else{
                 array_items_for_order.push({'product_id': selected_product_id,'product_quantity':$('.quantity_'+selected_product_id).val(),'product_unit_price':product_unit_price,'variant_type_id': 0,'variant_type_unit_price': 0,'variant_id':0,'check_variant_selection_if_single':check_variant_selection_if_single});  

                 // if_variant_is_available_then_selected_status.push(1);
              } 
         });

        //console.log(array_items_for_order);

      /*  if ($.inArray(0, if_variant_is_available_then_selected_status) != -1)
        {
              // varaint is avaialable then it should be selected
               swal('Oops...', 'Please Select Variant', 'error');   
        }else{*/
             var data_for_order =  JSON.stringify(array_items_for_order);

         if(array_items_for_order.length === 0){
            swal('Oops...', 'You have to select any product', 'error');
         } else{
                // Ajax-------SATRT------------
                $.ajax({
                    url: BASE_URL+'admin/confirm_and_update_place_order',
                    data: { 
                        data_for_order: data_for_order,selected_order_id: order_id
                    },
                    type: 'post',
                    success: function(response){
                      
                        if(response == 1){
                         window.location.href= ''+BASE_URL+'admin/order_single/'+order_id+'/';//1 = for checkour modal open
                        }
                       
                       if(response == 0){
                          swal('Oops...', 'Internal server error', 'error');
                       }

                        if(response == 2){
                           //swal('Dont Worry...', 'Nothing Changed!');
                       }

                        if(response == 3){
                          swal('Oops...', 'Something went wrong!', 'error');
                       }
                    },
                    
                });
                // Ajax-------END------------
         }   
        //}//end of if_variant_is_available_then_selected_status
    });
    // Products delete and  istert   ------------START-----------
    //Check out customise items------------------------START---------------------
    var check_checkout_modal;
    if(check_checkout_modal == 1){
        $('#checkout_modal_btn').trigger('click');
    }

    //Checkout Submit After Customize----START-----
    $('body').on('click', '#checkout_after_customize', function(e) {
 
        if(order_id != ""){
            if(subtotal_of_items_checkout != "" && subtotal_of_delivery_checkout !="" && grand_total_of_checkout !="" && delivery_total_after_promo_code_applied != "" && items_total_after_promo_code_applied !="" && item_quantity != "" && (outstanding_amount_after_customize !="" || outstanding_amount_after_customize =="") && (who_will_pay_outstanding_amount !="" || who_will_pay_outstanding_amount =="") && customer_id !=""){
                
                 // Ajax-------SATRT------------
                        $.ajax({
                        url: BASE_URL+'admin/checkout_after_customize_submit',
                        data: { 
                            subtotal_of_items_checkout: subtotal_of_items_checkout,subtotal_of_delivery_checkout:subtotal_of_delivery_checkout,grand_total_of_checkout:grand_total_of_checkout,selected_order_id: order_id,delivery_total_after_promo_code_applied:delivery_total_after_promo_code_applied,items_total_after_promo_code_applied:items_total_after_promo_code_applied,item_quantity:item_quantity,outstanding_amount_after_customize:outstanding_amount_after_customize,who_will_pay_outstanding_amount:who_will_pay_outstanding_amount,customer_id:customer_id
                        },
                        type: 'post',
                        success: function(response){
                          
                            if(response == 1){
                             
                             swal("Checkout Successfully Done", {
                                icon: "success",
                              });

                             setTimeout(function(){
                                   window.location.href= ''+BASE_URL+'admin/order_single/'+order_id+'/';
                                }, 2000); //refresh every 2 seconds*/
                             }
                           
                           if(response == 0 || response == 2){
                              swal('Oops...', 'Internal server error', 'error');
                           }

                            if(response == 3){
                              swal('Oops...', 'Something went wrong!', 'error');
                           }
                        },
                        
                    });
                    // Ajax-------END------------
            }else{
                swal('Oops...', 'Some data is missing', 'error');
            } 
        }else{
            swal('Oops...', 'Order Id is missing', 'error');
        }
         
    });
    //Checkout Submit After Customize----END-----

    //Check out customise items------------------------END---------------------
//Submit Final order ----------------------------------END--------
 

//Order single page  - Add and Remove orderd item-----------------END--------------

//order single page ----------------------Print order view START-------------------------
  $(document).ready(function() {
    $('#order_details_print').trigger('click');
  });
 $('body').on('click', '#order_details_print', function(e) {
        //window.print();
            var event_check = window.print();
          
            if(event_check == undefined){//check if only click print or cancel for only again show privous html
                //alert();
                //$('#ingnore_print_time').removeClass('d-none');
            }
 });

//on ctr+p open invoice page
$(document).keydown(function(event) {
    if (event.ctrlKey==true && (event.which == '80')) { //cntrl + p
        event.preventDefault();
         $('#order_invoice').trigger('click');
    }
});
//order single page ----------------------Print order view END-------------------------

 
// order  delviery address update by latitude and longtiude-----------------START------------

 $('body').on('click', '#get_lat_long_by_address', function() {
       var delivery_street_address = $('#delivery_street_address').val();

     // get latitude and longtiude if select by autocplace
        //Function to covert address to Latitude and Longitude
        var getLocation =  function(address) {
          var geocoder = new google.maps.Geocoder();
          geocoder.geocode( { 'address': address}, function(results, status) {

          if (status == google.maps.GeocoderStatus.OK) {
              var latitude = results[0].geometry.location.lat();
              var longitude = results[0].geometry.location.lng();
              $('#delivery_latitude').val(latitude);
              $('#delivery_longitude').val(longitude);
                //console.log(latitude, longitude);
              } 
          }); 
        }
          //Call the function with address as parameter
        getLocation(delivery_street_address);

        setTimeout(function(){  $('#updated_order_address').trigger('click'); }, 1000);
    
 });

 // show address change div
 $('body').on('click', '#edit_customer_order_address', function() {
    $('#locationField').removeClass('d-none');
     $('html, body').animate({
        scrollTop: $("#locationField").offset().top
    }, 2000);
 
 });
$('body').on('click', '#close_address_popup', function() {
    $('#locationField').addClass('d-none');
 });

 $('body').on('click', '#updated_order_address', function() {
    var delivery_street_address = $('#delivery_street_address').val();
    var delivery_postal_code = $('#postal_code').val();
    var delivery_unit_number = $('#unit_number').val();
    var delivery_latitude = $('#delivery_latitude').val();
    var delivery_longitude = $('#delivery_longitude').val();
 

    if(delivery_street_address == "" && order_type != 2){
        $('#delivery_street_address').css('border-color','red');
        $('#unfill_street_address').text('Please Fill Delivery  Street Address');
    }else{
        $('#delivery_street_address').css('border-color','#ccc');
       $('#unfill_street_address').text('');
    }

    if(delivery_postal_code == "" && order_type != 2){
        $('#postal_code').css('border-color','red');
        $('#unfill_postal_code').text('Please Fill Delivery  Street Address');
    }else{
        $('#postal_code').css('border-color','#ccc');
       $('#unfill_postal_code').text('');
    }

    if(order_type == 2){
         swal('Oops...', 'Order Type Is Self Pickup', 'warning');
    }else{
        if(delivery_street_address != "" && delivery_latitude != "" && delivery_longitude !=""){
           // Ajax-------SATRT------------
            $.ajax({
                url: BASE_URL+'admin/update_order_delivery_address_with_delivery_charge',
                data: { 
                    delivery_street_address:delivery_street_address,delivery_latitude:delivery_latitude,delivery_longitude:delivery_longitude,selected_order_id:order_id,order_type:order_type, delivery_handled_by: delivery_handled_by,delivery_mobile:delivery_mobile,ordered_restaurant_id:ordered_restaurant_id,delivery_name:delivery_name,pickup_time_from:pickup_time_from,delivery_postal_code:delivery_postal_code,delivery_unit_number:delivery_unit_number,order_number_id:order_number_id
                },
                type: 'post',
                success: function(response){
                   if(response == 1){
                       window.location.replace(BASE_URL+'admin/order_single/'+order_id);
                     }
                   if(response == 0){
                      swal('Oops...', 'Something went wrong!', 'error');
                   }
                   if(response == 2){
                      swal('Oops...', 'Order Type Is Self Pickup', 'warning');
                   }
                },
                
            });
          // Ajax-------END------------
        }
    }
    

 
 });
// order  delviery address update by latitude and longtiude---------------------END--------

//order single page -------EDIT Order Preparation Time After accept--------START------------ 
 $('body').on('click', '#edit_order_preparation_time_submit', function() {

     var edit_order_preparation_time = $('input[name="edit_order_preparation_time"]').val();
        if(edit_order_preparation_time == "" ||  edit_order_preparation_time == 0){
            $('input[name="edit_order_preparation_time"]').css('border-color','red');
            $('#unfill_order_preparation_time').text('Please Fill Preparation Time');
        }else{
            $('input[name="edit_order_preparation_time"]').css('border-color','#ccc');
            $('#unfill_order_preparation_time').text('');
        }
       
      if(edit_order_preparation_time !="" && edit_order_preparation_time != 0){
            // Ajax-------SATRT------------
                $.ajax({
                url: BASE_URL+'admin/update_order_preparation_time_after_accept',
                data: { 
                    edit_order_preparation_time:edit_order_preparation_time,selected_order_id:order_id
                },
                type: 'post',
                success: function(response){
                   if(response == 1){
                     
                      /*$('#updated_preparation_time').text(edit_order_preparation_time);
                      $('#edit_order_preparation_time_after_accept').modal('hide');*/
                     // window.location.replace(BASE_URL+'admin/order_single/'+order_id);
                     }
                   if(response == 0){
                      swal('Oops...', 'Something went wrong!', 'error');
                   }
                },
                
            });
          // Ajax-------END------------
      }
 });
//order single page ------EDIT Order Preparation Time After accept-------END------------ 

//order single page -------EDIT Order update pick up Time-------START------------ 
 $('body').on('click', '#edit_pick_up_time_submit', function() {

     var delivery_time = $('#delivery_time').val();
     var order_type = $('#order_type').val();
     var final_pickup_range = $('#final_pickup_range').val();
     var final_pickup_date_from = $('#final_pickup_date_from').val();
     

     var edit_pick_up_from = $('#pick_up_from').val();
        if(edit_pick_up_from == "" ||  edit_pick_up_from == 0){
            $('#pick_up_from').css('border-color','red');
            $('#unfill_pick_up_from').text('Please Fill From Time');
        }else{
            $('#pick_up_from').css('border-color','#ccc');
            $('#unfill_pick_up_from').text('');
        }

      var edit_pick_up_to = $('#pick_up_to').val();
     
        /*if(edit_pick_up_to == "" ||  edit_pick_up_to == 0){
            $('#pick_up_to').css('border-color','red');
            $('#unfill_pick_up_to').text('Please Fill To Time');
        }else{
            $('#pick_up_to').css('border-color','#ccc');
            $('#unfill_pick_up_to').text('');
        }*/
       
      if(edit_pick_up_from !="" && edit_pick_up_from != 0  && edit_pick_up_to != 0){
            // Ajax-------SATRT------------
                $.ajax({
                url: BASE_URL+'admin/update_order_pick_up_time',
                data: { 
                    edit_pick_up_from:edit_pick_up_from,edit_pick_up_to:edit_pick_up_to,selected_order_id:order_id,delivery_time:delivery_time,order_type:order_type,final_pickup_range:final_pickup_range,final_pickup_date_from:final_pickup_date_from
                },
                type: 'post',
                success: function(response){
                   if(response == 1){
                         $('#edit_pickup_time_modal').modal('hide');
                        window.location.replace(BASE_URL+'admin/order_single/'+order_id);
                     }
                   if(response == 0){
                      swal('Oops...', 'Something went wrong!', 'error');
                   }
                },
                
            });
          // Ajax-------END------------
      }
 });
//order single page ------EDIT Order update pick up  Time-------END------------ 


//order single page -------EDIT Order update Schedule Time-------START------------ 
 $('body').on('click', '#edit_order_schedule_time_submit', function() {

     var edit_order_schedule_time = $('input[name="edit_order_schedule_time"]').val();
        if(edit_order_schedule_time == "" ||  edit_order_schedule_time == 0){
            $('input[name="edit_order_schedule_time"]').css('border-color','red');
            $('#unfill_schedule_time').text('Please Fill Preparation Time');
        }else{
            $('input[name="edit_order_schedule_time"]').css('border-color','#ccc');
            $('#unfill_schedule_time').text('');
        }
       
      if(edit_order_schedule_time !="" && edit_order_schedule_time != 0){
            // Ajax-------SATRT------------
                $.ajax({
                url: BASE_URL+'admin/update_order_schedule_time',
                data: { 
                    edit_order_schedule_time:edit_order_schedule_time,selected_order_id:order_id
                },
                type: 'post',
                success: function(response){
                   if(response == 1){
                     /* swal("Preparation Time Updated Successfully!", {
                        icon: "success",
                      });*/
                      window.location.replace(BASE_URL+'admin/order_single/'+order_id);
                      $('#edit_schedule_time').modal('hide');
                      //window.location.replace(BASE_URL+'admin/order_single/'+order_id);
                     }
                   if(response == 0){
                      swal('Oops...', 'Something went wrong!', 'error');
                   }
                },
                
            });
          // Ajax-------END------------
      }
 });
//order single page ------EDIT Order update Schedule Time-------END------------


//order single page -------EDIT Order Customer delivery address--------START------------ 
 $('body').on('click', '#edit_delivery_address_submit', function() {

     var edit_delivery_address = $('textarea[name="edit_delivery_address"]').val();
        if(edit_delivery_address == ""){
            $('textarea[name="edit_delivery_address"]').css('border-color','red');
            $('#unfill_delivery_address').text('Please Fill Delivery Address');
        }else{
            $('textarea[name="edit_delivery_address"]').css('border-color','#ccc');
            $('#unfill_delivery_address').text('');
        }
       
      if(edit_delivery_address !=""){
            // Ajax-------SATRT------------
                $.ajax({
                url: BASE_URL+'admin/update_order_delivery_address',
                data: { 
                    edit_delivery_address:edit_delivery_address,selected_order_id:order_id
                },
                type: 'post',
                success: function(response){
                   if(response == 1){
                      swal("Delivery Address Updated Successfully!", {
                        icon: "success",
                      });
                      $('#updated_address').text(edit_delivery_address);
                       $('#edit_customer_order_address').modal('hide');
                      //window.location.replace(BASE_URL+'admin/order_single/'+order_id);
                     }
                   if(response == 0){
                      swal('Oops...', 'Something went wrong!', 'error');
                   }
                },
                
            });
          // Ajax-------END------------
      }
 });
//order single page ------EDIT Order Customer delivery address -------END------------ 
  
//order single page -------EDIT Order track link --------START------------ 
 $('body').on('click', '#edit_order_track_link_submit', function() {

     var edit_track_link = $('#edit_track_link').val();
        if(edit_track_link == ""){
            $('#edit_track_link').css('border-color','red');
            $('#unfill_track_link').text('Please Fill Track Link');
        }else{
            $('#edit_track_link').css('border-color','#ccc');
            $('#unfill_track_link').text('');
        }

    var lalamove_order_id = $('#lalamove_order_id').val();
        /*if(lalamove_order_id == ""){
            $('#lalamove_order_id').css('border-color','red');
            $('#unfill_lalamove_order_id').text('Please Fill Lalamove Order Id');
        }else{
            $('#lalamove_order_id').css('border-color','#ccc');
            $('#unfill_lalamove_order_id').text('');
        }*/
       
      if(edit_track_link !=""){//&& lalamove_order_id !=""
            // Ajax-------SATRT------------
                $.ajax({
                url: BASE_URL+'admin/update_order_track_link',
                data: { 
                    edit_track_link:edit_track_link,selected_order_id:order_id,lalamove_order_id:lalamove_order_id
                },
                type: 'post',
                success: function(response){
                   if(response == 1){
                    
                       $('#edit_order_tracking_link_modal').modal('hide');
                      window.location.replace(BASE_URL+'admin/order_single/'+order_id);
                     }
                   if(response == 0){
                      swal('Oops...', 'Something went wrong!', 'error');
                   }
                },
                
            });
          // Ajax-------END------------
      }
 });
//order single page ------EDIT Order track link -------END------------ 
  
//order single page -------EDIT Order remark --------START------------ 
 $('body').on('click', '#edit_remark_submit', function() {

     var edit_remark = $('#edit_remark').val();
        if(edit_remark == ""){
            $('#edit_remark').css('border-color','red');
            $('#unfill_remark').text('Please Fill remark');
        }else{
            $('#edit_remark').css('border-color','#ccc');
            $('#unfill_remark').text('');
        }
       
      if(edit_remark !=""){
            // Ajax-------SATRT------------
                $.ajax({
                url: BASE_URL+'admin/update_order_remark',
                data: { 
                    edit_remark:edit_remark,selected_order_id:order_id
                },
                type: 'post',
                success: function(response){
                   if(response == 1){
                       $('#edit_order_tracking_link_modal').modal('hide');
                      window.location.replace(BASE_URL+'admin/order_single/'+order_id);
                     }
                   if(response == 0){
                      swal('Oops...', 'Something went wrong!', 'error');
                   }
                },
                
            });
          // Ajax-------END------------
      }
 });
//order single page ------EDIT Order remark-------END------------ 
$( document ).ready(function() {
    // # UT : Contact number should work for only numbers and backspace
    $(".contact_number").keypress(function(e){
        var charCode = (e.which) ? e.which : event.keyCode;
        if (charCode != 45 && charCode > 31 && (charCode < 48 || charCode > 57))
        {
            return false;
        }else
        {
            return true;
            validateEmail();
        }

    });
});

$(".imgAdd").click(function () {
    $(this)
        .closest(".row")
        .find(".imgAdd")
        .before(
            '<div class="col-sm-2 imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input type="file" class="uploadFile img" value="Upload Photo" style="width:0px;height:0px;overflow:hidden;"></label><i class="fa fa-times del"></i></div>'
        );
});
$(document).on("click", "i.del", function () {
    $(this).parent().remove();
});
$(function () {
    $(document).on("change", ".uploadFile", function () {
        var uploadFile = $(this);
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

        if (/^image/.test(files[0].type)) {
            // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file

            reader.onloadend = function () {
                // set image data as background of div
                //alert(uploadFile.closest(".upimage").find('.imagePreview').length);
                uploadFile
                    .closest(".imgUp")
                    .find(".imagePreview")
                    .addClass("added")
                    .css("background-image", "url(" + this.result + ")");
            };
        }
    });
});


// Quantity Plus and Minus Script (Order-single Page)
function wcqib_refresh_quantity_increments() {
    jQuery("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").each(function (a, b) {
        var c = jQuery(b);
        c.addClass("buttons_added"), c.children().first().before('<input type="button" value="-" class="minus" />'), c.children().last().after('<input type="button" value="+" class="plus" />');
    });
}
String.prototype.getDecimals ||
    (String.prototype.getDecimals = function () {
        var a = this,
            b = ("" + a).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        return b ? Math.max(0, (b[1] ? b[1].length : 0) - (b[2] ? +b[2] : 0)) : 0;
    }),
    jQuery(document).ready(function () {
        wcqib_refresh_quantity_increments();
    }),
    jQuery(document).on("updated_wc_div", function () {
        wcqib_refresh_quantity_increments();
    }),
    jQuery(document).on("click", ".plus, .minus", function () {
        var a = jQuery(this).closest(".quantity").find(".qty"),
            b = parseFloat(a.val()),
            c = parseFloat(a.attr("max")),
            d = parseFloat(a.attr("min")),
            e = a.attr("step");
        (b && "" !== b && "NaN" !== b) || (b = 0),
            ("" !== c && "NaN" !== c) || (c = ""),
            ("" !== d && "NaN" !== d) || (d = 0),
            ("any" !== e && "" !== e && void 0 !== e && "NaN" !== parseFloat(e)) || (e = 1),
            jQuery(this).is(".plus") ? (c && b >= c ? a.val(c) : a.val((b + parseFloat(e)).toFixed(e.getDecimals()))) : d && b <= d ? a.val(d) : b > 0 && a.val((b - parseFloat(e)).toFixed(e.getDecimals())),
            a.trigger("change");
    });

// Setting Tabs Functions
function firstTab(evt, settingName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(settingName).style.display = "block";
  evt.currentTarget.className += " active";
}
// Get the element with id="defaultOpen" and click on it
//document.getElementById("defaultOpen").click();

// Date and Time Picker Js


/*Select dropdown with search filter used in add/edit promotion for select multiple restaurant ----------start*/
function SearchDropdownFunction(select_dropdown_id) {

  if(select_dropdown_id == 'SelectBannerRestaurantDropdown'){// for add banner restaurant selection
    $('#'+select_dropdown_id).toggleClass("show");//if want to toggle use this
  }else{
     $('#'+select_dropdown_id).addClass('show');// here we set by default its open that why using this
  }
  //document.getElementById(select_dropdown_id).classList.toggle("show");//if want to toggle use this
 // $('#'+select_dropdown_id).addClass('show');// here we set by default its open that why using this
}

function filterFunction(select_dropdown_id,SelectInput) {
  var input, filter, ul, li, a, i;
  input = document.getElementById(SelectInput);
  filter = input.value.toUpperCase();
  div = document.getElementById(select_dropdown_id);
  a = div.getElementsByTagName("label");//change element which you want search 
  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      a[i].style.display = "";
    } else {
      a[i].style.display = "none";
    }
  }
}
/*Select dropdown with search filter used in add/edit promotion for select multiple restaurant ----------end*/ 

$(".filter_button").click(function(){
    $(".user_tables").slideToggle();
});


//ORDER detail Address map  ----auto complete---------------------START----------------------


$(document).ready(function() {
    // # Functions aere used to make a restaurant eiher a best seller or a trending start
    $('body').on('click', '.action_restro_best_seller', function() {

       var restaurant_id = $(this).attr('value');
       if ($(this).prop('checked')==true){ 
            var checked = 1;
        }else
        {
          var checked = 0;
        }
        $.ajax({
            url: BASE_URL+'admin/set_restro_seller_trending/'+checked+'/1', // Param 1 means make it as best seller
            data: { 
                restaurant_id: restaurant_id,
            },
            type: 'post'
        });
    });

    $('body').on('click', '.action_restro_trending', function() {
       var restaurant_id = $(this).attr('value');
       if ($(this).prop('checked')==true){ 
            var checked = 1;
        }else
        {
          var checked = 0;
        }
        $.ajax({
            url: BASE_URL+'admin/set_restro_seller_trending/'+checked+'/2', // Param 1 means make it as Trending
            data: { 
                restaurant_id: restaurant_id,
            },
            type: 'post'
        });
    });

    // # Functions aere used to make a restaurant eiher a best seller or a trending END

    /*New Password and confirm password match validation ------START-----*/

       var password_vaidate_status = true;
       
       function comman_function_for_check_confrim_password(np_val, cnp_val){

         var np_len = np_val.length;
            var cnp_len = cnp_val.length;    
            if(np_len > 12 || cnp_len > 12 || np_len < 6 || cnp_len < 6)
            {
              
                $(".np_password").css('border-color','red');
                $("#np_password_error").html('Password length must be in between 6 to 12 characters');
                return 2;
            }else if(np_val != cnp_val)
            {
                 
                $(".np_password").css('border-color','red');
                $("#np_password_error").html('New password and confirm new password does not match');
                return 3;
            }else if(np_val.match(/[A-Z]/) && np_val.match(/\d/) && np_val.match(/[A-z]/) && np_val.match(/[~!,@#%&_\$\^\*\?\-]/))
            {
                
                $(".np_password").css('border-color','green');
                $("#np_password_error").html('');

                return 1;
                
            }else
            {   
                $(".np_password").css('border-color','red');
                $("#np_password_error").html('Password must include alphanumeric, special characters and capital letters');
                return 4;
            }
       }
       
       $(".np_password, .cnp_password").keyup(function(e){
            var np_val = $(".np_password").val();
            var cnp_val = $(".cnp_password").val();

            var pwd_final_status = comman_function_for_check_confrim_password(np_val,cnp_val);
            
            if(pwd_final_status >1){
               password_vaidate_status = false;
            
            }else{
                $(".np_password").css('border-color','#e4e6fc');
                $("#np_password_error").html('');
                password_vaidate_status = true;
            }
        });
        /*New Password and confirm password match validation ------END-----*/

    /*Match old Password with existing password -------START---------*/
    $('body').on('click', '#MatchOldPasswordSubmit', function() {
        var old_password = $('#old_password').val();

        if(old_password !=""){
            //ajax-------- start-------------
            $.ajax({
            url: BASE_URL+'admin/CheckOldPassword/',
            data: { 
                old_password: old_password,
            },
            type: 'post',
                success: function(response){
                  if(response == 1){

                        $(".old_password").css('border-color','green');
                        $("#old_password_error").html('');
                        $("#old_password_success").html('Please wait while we are processing your request...');
                         setTimeout(function(){ 
                             $("#hide_after_match_old_pwd").addClass('d-none');
                            $('#ChangePasswordSubmit').removeClass('d-none');
                        }, 2000);
                        
                    }else{
                      $(".old_password").css('border-color','red');
                      $("#old_password_success").html('');
                      $("#old_password_error").html('Incorrect Password');
                    }
                },

            });
            //ajax-------- end-------------
        }else{
            $(".old_password").css('border-color','red');
            $("#old_password_success").html('');
            $("#old_password_error").html('Please enter your old password');
        }
    }); 
    /*Match old Password with existing password -------END---------*/

    //Update Password  after match old passwoord -----START------
        $("#ChangePasswordSubmit").submit(function( event ) {  
            var old_password = $('#old_password').val();
             var new_password = $('#new_password').val();
             var confirm_password = $('#confirm_password').val();
             //alert(password_vaidate_status);//should be true
     
              if (old_password != "" &&  new_password != "" && confirm_password !="" && password_vaidate_status == true) {  
                 //ready for submit
                  $("#confirm_password_success").html('Please wait while we are processing your request...');
                 return;  
              }else{
                 //alert('invalid');
              } 
               
            event.preventDefault();  
        });  
        //Update Password  after match old passwoord -----END------
});// End of  $(document).ready(function() {    
