<?php

namespace App\Services;

use DB;
use File;
use DateTime;
use App\Models\User;
use App\Models\Notification;
use Edujugon\PushNotification\PushNotification;


Class PushService {
	
	/*
	---------------------------------------------------------------
	| FOR SEND PUSH NOTIFICATION
	---------------------------------------------------------------
	*/
	public function sendPushNotification($user_id='', $message='', $msgData=array(), $extra=array()){
        

        $getUser = User::where('id', $user_id)->where('deleted_at', NULL)->first();
        
        if(!empty($getUser)){
            
            $user_type   =   $getUser->user_type;     
            $deviceType  =   $getUser->device_type;
            $deviceToken =   $getUser->device_token; 

            if(($deviceType == 'android') || ($deviceType == 'Android')){

                $this->sendMessageAndroid($deviceToken, $message, $msgData, $user_type, $extra);

            }elseif(($deviceType == 'ios') || ($deviceType == 'iOS')){
                
                $this->iosPushNotification($deviceToken, $message, $user_type, $extra);
            }
        }

    }
    /*
    ---------------------------------------------------------------
    | FOR SEND iOS PUSH NOTIFICATION
    ---------------------------------------------------------------
    */
    public function iosPushNotification($deviceToken='', $message='', $user_type='', $extra=''){
        
        if($user_type == 1){
            // FOR USER APP   
            $push = new PushNotification('apu');
        }

        if($user_type == 2){
            // FOR DRIVER APP  
            $push = new PushNotification('apn');
        }

        
        
            $data = [
		                'aps' => [
		                    
		                    'alert' =>  [
		                                    'title'     => 'Truck Yaah',
					                        'body'      =>  $message,
					                        'noti_type' =>  1,
					                    ],
                            'sound' => 'default'
		                ]
        			];

        $push->setMessage($data)->setDevicesToken([ $deviceToken ]);
        $push   = $push->send();


        if($push){ return $push;  }else{ return $push->getFeedback(); }
        
    }
    /*
    ---------------------------------------------------------------
    | FOR SEND ANDROID PUSH NOTIFICATION
    ---------------------------------------------------------------
    */
    function sendMessageAndroid($deviceToken, $message, $msgData=array(), $user_type='', $extra=''){
        
        if($user_type == 1){
            // FOR USER APP   
            $user_firebase_api_key   = "AIzaSyDw3whOt6DPPP-RpHWnkYwwZb1SEGv88Pg"; 
        }

        if($user_type == 2){
            // FOR DRIVER APP  
            $user_firebase_api_key   = "AIzaSyAhdnlcy4Z2xnl1HMK0SYnMGWaDGh8C1Gc";
        }

        $firebase_send_url           = 'https://fcm.googleapis.com/fcm/send';
        
        // API access key from Google API's Console
		define( 'API_ACCESS_KEY', $user_firebase_api_key);
        $registrationIds         = array( $deviceToken );
        // prep the bundle
        $msg					 = array
									(
										'message' 	   => $message,
										'title'		   => 'Truck Yaah',
										'noti_type'    => $msgData['noti_type'],
										'vibrate'	   => 1,
										'sound'		   => 1,
                                        // 'booking_id'   => ($extra['booking_id'])?$extra['booking_id']:'',
                                        'booking_id'   => (!empty($extra['booking_id']))?$extra['booking_id']:'',
									);

		$fields                  = array
									(
										'registration_ids' 	=> $registrationIds,
										'data'			    => $msg
									);
		 
		$headers                 = array
									(
										'Authorization: key=' . API_ACCESS_KEY,
										'Content-Type: application/json'
									);
		 
		$ch                      = curl_init();
		curl_setopt( $ch,CURLOPT_URL, $firebase_send_url );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result                 = curl_exec($ch );
		curl_close( $ch );
		$this->insertserviceData($msgData);
        return $result;  
    }

    //FOR SAVE NOTIFICATIONS
    public function insertserviceData($msgData=array()){
        
        Notification::Create($msgData);
        return true;
    }
}	