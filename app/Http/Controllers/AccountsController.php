<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
// use Tymon\JWTAuth\Contracts\JWTSubject;

class AccountsController extends Controller
{
    public function __construct()
    {

    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function register(Request $params){
        $this->validate($params, [
            'password' => 'required',
            'email' => 'required|email',
            'firstName'=>'required',
            'lastName'=>'required',
        ]);
        $email = $params->input('email');
        $password = $params->input('password');
        $firstName = $params->input('firstName');
        $middleName = $params->input('middleName');
        $lastName = $params->input('lastName');

        $hashedPassword = app('hash')->make($password);
        // To check password
        // $x = app('hash')->check( $params->input('password'),$test);
        // $emailCheck = DB::select('SELECT count(*) as count FROM `users` WHERE `email` = ?',[$email]);
        $emailCheck = DB::table('users')->where('email', $email)->first();
        $messages = [
            'message' => '',
            'status'=>200,
        ];
        if(isset($emailCheck->email)){
            $messages['message'] = 'Email is not unique';
            $messages['status'] = 401;
            return $messages;
        }

        $results = DB::insert('
            INSERT INTO `users`( `email`, `password`, `firstname`, `middlename`, `lastname` )
            VALUES (?,?,?,?,?)
        ', [$email, $hashedPassword, $firstName, $middleName, $lastName]);
        $messages['message'] = 'Succcess';

        return $messages;
    }


    public function login(Request $params){
        $messages = [
            'message' => '',
            'status'=>200,
            'token'=> '',
        ];
        $this->validate($params, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $email = $params->input('email');
        $password = $params->input('password');

        $dbUser = DB::table('users')->where('email', $email)->first();

        if(!isset($dbUser->email)){
            $messages['message'] = 'User does not exist';
            $messages['status'] = 401;
            return $messages;
        }
        // To check password
        $checkPassword = app('hash')->check( $password,$dbUser->password);
        $apikey = base64_encode(Str::random(60));
        $rowCount =  DB::table('api_key')->updateOrInsert(
            ['users_id' => $dbUser->id], ['key' => $apikey],
        );
        if(!$checkPassword or $rowCount == 0){
            $messages['message'] = 'Incorrect Password';
            $messages['status'] = 401;
            return $messages;
        }
        $messages['message'] = 'Login Success';
        $messages['status'] = 200;
        $messages['token'] = $apikey;
        return $messages;
    }

}