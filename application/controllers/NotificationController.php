<?php


//use App\Controllers\BaseController;
class NotificationController extends CI_Controller 

{
	#browser notification -----IOS------START-----------
    public function send()
    {
        $device_token = $this->request->getVar("device_token");
    
        return $this->sendNotification($device_token, array(
            "title" => "Sample Message",
            "body" => "This is Test message body"
        ));
    }

    public function sendNotification($device_token, $message)
    {
            $SERVER_API_KEY = 'AAAAV2StWI8:APA91bEMg0DDBoEO0kVMjyqKLnB9WGURRlt0TtoeaImPJcZfFS__XsTj-FeHGPrzSEiHfF8_AYXWAx_UxPHIt5_AU98r6ojH93XjIS1OgEjUBLu-EJcZANG7WpXdiAHYBPEv4Y-axLbH';

        // payload data, it will vary according to requirement
        $data = [
            "to" => $device_token, // for single device id
            "data" => $message
        ];
        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);
      
        curl_close($ch);
        print_r($response);
      
        return $response;

    }
    #browser notification -----IOS------END-----------
}