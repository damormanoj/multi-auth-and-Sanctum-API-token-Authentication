<?php

namespace App\Services;

use DB;
use File;
use DateTime;
use App\Models\User;  

Class ServiceClass {

	public function trackLocation($lat='',$long='',$distance='',$user_type='',$vehicle_type=''){
    	
        	$truk_type = $vehicle_type;
            $distance  = ($distance)?$distance:50;
            $user_type = ($user_type)?$user_type:1;

            $Query     = DB::table("users")
                        ->select("users.*"
                        ,DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                        * cos(radians(users.latitude)) 
                        * cos(radians(users.longitude) - radians(" . $long . ")) 
                        + sin(radians(" .$lat. ")) 
                        * sin(radians(users.latitude))) AS distance"))
                        ->where('users.user_type', $user_type)  
                        ->groupBy("users.id")
                        ->having('distance', '<', $distance)
                        ->get();
            

            return  $Query;            
	    }
     /*
    ====================================================================
    | FOR TRACK REQUEST
    ====================================================================
    */    
    public function getUserRequest($lat='',$long='',$distance=''){
        
        
        $distance  = ($distance)?$distance:50;
        $Query     = DB::table("user_requests")
                    ->select("user_requests.*"
                    ,DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                    * cos(radians(user_requests.pickup_latitude)) 
                    * cos(radians(user_requests.pickup_longitude) - radians(" . $long . ")) 
                    + sin(radians(" .$lat. ")) 
                    * sin(radians(user_requests.pickup_latitude))) AS distance"))
                    ->groupBy("user_requests.id")
                    ->having('distance', '<', $distance)
                    ->get();
        

        return  $Query; 
    }    
}    

