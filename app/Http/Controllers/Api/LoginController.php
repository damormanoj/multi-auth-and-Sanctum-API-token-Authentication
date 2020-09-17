<?php

namespace App\Http\Controllers\API;

use Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ExistingClass;
use Illuminate\Support\Facades\Auth;
use App\Services\PushService;
use Validator;
use Hash;

class LoginController extends Controller
{

    public function __construct($foo = null)
    {
        $this->PushService    = new PushService();
        $this->driver         = new ExistingClass();
    }
    /*
    --------------------------------------------------------------------
    | LOGIN
    --------------------------------------------------------------------
    */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email'           => 'required',
            'password'        => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors()->toArray();
            return response()->json([
                'status'    => Controller::HTTP_BAD_REQUEST,
                'message'   => reset($errors)[0],
                'object'    => (object) []
            ]);
        }
        $email      = $request->email;
        $password   = $request->password;
        $data   =   User::where(function($q) use($email,$password)
                                        {
                                            $q->where('email', $email);
                                        }
                                )
                    ->orWhere(function($q) use($email,$password)
                                        {
                                            $q->where('email', $email);
                                        }
                                )
                    ->first();
        if(empty($data))
        {
            $output["message"] = trans('lang.not_register_user');
            $output["status"]  = 400;
            return response()->json($output ,200);
        }

        //  if(!$data->email_verified_at)
        // {
        //     $output["message"] = trans('lang.verify_email');
        //     $output["status"]  = 400;
        //     return response()->json($output ,200);
        // }

        if($data->deleted_at)
        {
            $output["message"] = trans('lang.delete_user');
            $output["status"]  = 400;
            return response()->json($output ,200);
        }

        $checkPwd = Hash::check($request->password,$data->password);

        if ($checkPwd != 1)
        {
            $output["message"] = trans('lang.invalid');
            $output["status"] = 400;
            return response()->json($output ,200);
        }

        // $data->device_type   =  $request->device_type;
        // $data->device_token  =  $request->device_token;
        $data->save();
        $data->token         = $data->createToken('my-token')->plainTextToken;

        if (!empty($data->image))
        {
            $profileImg = $data->image;
        }else{
            $profileImg = "user.png";
        }

        $msgData                  = array();
        $message                  = "You have login Successfully.";

        //$this->PushService->sendPushNotification('1',$message,$msgData);

        return response()->json([

            'status'    => Controller::HTTP_OK,
            'message'   => trans('lang.login_success'),
            'object'    => $data

        ]);

    }
    /*
    --------------------------------------------------------------------
    | FOR LOGOUT
    --------------------------------------------------------------------
    */
    public function doLogout(Request $request)
    {

        $user                       = auth('api')->user();
        $userData                   = User::find($user->id);
        $userData->device_token     = "";
        $userData->device_type      = "";
        $userData->save();
        // Delete token
        $request->user()->token()->revoke();

        return response()->json([

                'status' => 200,
                'message' => "logout successfully",
                'object' => (object) []
            ]);
    }



}
