<?php

namespace App\Services;

use DB;
use File;
use DateTime;
use App\Models\User;  

Class ExistingClass {

	public function isNumberisUniqe($mobile_number=''){
    	
    	$existNumber = User::where('mobile_number', $mobile_number)->count();
    	if($existNumber>0){
        	
        	return true;
    	}else{

    		return false;
    	}
	}

	public function profileUpdateMobileNumber($user_id='',$mobile_number=''){
    	
    	$existNumber = User::where('id', '!=', $user_id)
    	                   ->where('mobile_number', $mobile_number)->count();
    	
    	if($existNumber>0){
        	
        	return true;
    	}else{

    		return false;
    	}
	}

	public function isEmailisUniqe($email_address=''){
    	
    	$existEmail = User::where('email', $email_address)->count();
    	if($existEmail>0){
        	
        	return true;
    	}else{

    		return false;
    	}
	}

	public function profileUpdateEmail($user_id='',$email_address=''){
    	
    	$existEmail = User::where('id', '!=', $user_id)
    	                   ->where('email', $email_address)->count();
    	
    	if($existEmail>0){
        	
        	return true;
    	}else{

    		return false;
    	}
	}
        public function profileUpdateUsername($user_id='',$username=''){
    	
    	$existUsername = User::where('id', '!=', $user_id)
    	                   ->where('email', $username)->count();
    	
    	if($existUsername>0){
        	
        	return true;
    	}else{

    		return false;
    	}
	}

}