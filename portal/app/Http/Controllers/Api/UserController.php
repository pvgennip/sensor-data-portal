<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\User;
use Response;
use Validator;
use Hash;
use Auth;

class UserController extends Controller
{

    public function authenticate(Request $request) 
    {
        return $this->returnToken($request->user(), 200);
    }

    public function login(Request $request) 
    {
        $credentials = array
        (
            'email' => $request->get('email'),
            'password' => $request->get('password')
        );


        if(Auth::attempt($credentials))
        {
            return $this->returnToken($request->user(), 200);
        }
        else
        {
            return Response::json(["message" => "invalid_user"], 400);
        }
    }

    private function returnToken($user, $status)
    {
        return Response::json(["api_token"=>$user->api_token, "name"=>$user->name, "created"=>$user->created_at], $status);
    }


    public function register(Request $request)
    {

        $validator = Validator::make
        (
            $request->all(),
            array
            (
                'email'         => 'bail|required|email|unique:users',
                'password'      => 'required|min:8',
            ),
            array
            (
                'required'      => ':attribute_is_required',
                'unique'        => ':attribute_already_exists',
                'email'         => 'no_valid_email',
                'min'           => 'invalid_password',
            )
        );


        // check if the data is validated
        if($validator->fails())
        {
            return Response::json(["message" => $validator->errors()->first()], 400);
        }
        else // save 'm 
        {
            $user_data = [
                'name'      => $request->get('email'),
                'password'  => Hash::make($request->get('password')),
                'email'     => $request->get('email'),
                'api_token' => str_random(60),
                'remember_token' => str_random(10),
            ];

            // save the user
            $user = User::create($user_data);

            // set the response data
            if($user) 
            {
                return $this->returnToken($user, 201);
            } 
            else
            {
                return Response::json(['message'=>'Could not create user'], 500);
            }
        }
    }




    /* SEND REMINDER */
    // responses: invalid_user, reminder_sent, invalid_password, invalid_token, password_reset
    public function reminder()
    {
        $credentials = array
        (
            'email' => Input::get('email')
        );

        $remind = Password::remind($credentials, function($message)
        {
            $message->subject('Kweecker - Nieuw wachtwoord');
        });
        switch($remind)
        {
            case Password::INVALID_USER:
                $code     = 400;
                $response = array('message' => 'invalid_user');
              break;

            case Password::REMINDER_SENT:
                $code = 200;
                $response = array('message' => 'reminder_sent');
              break;
        }


        // return the response
        return \Response::json($response, $code);
    }




    /* RESET PASSWORD */
    public function reset()
    {
        // get the input
        $email            = Input::get('email');
        $password         = Input::get('password');
        $password_confirm = Input::get('password_confirm');
        $token            = Input::get('token');

        $credentials = array
        (
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password_confirm,
            'token'                 => $token,
        );

        // generate the reset
        $reset = Password::reset($credentials, function($user, $password)
        {
            $user->password = Hash::make($password);
            $user->save();
        });

        // get the response
        switch ($reset)
        {
            case Password::INVALID_PASSWORD:
                $code     = 400;
                $response = array('message' => 'invalid_password');
              break;

            case Password::INVALID_TOKEN:
                $code     = 400;
                $response = array('message' => 'invalid_token');
              break;

            case Password::INVALID_USER:
                $code     = 400;
                $response = array('message' => 'invalid_user');
              break;

            case Password::PASSWORD_RESET:
                $code     = 200;
                $user     = User::where('email', $email)->first();
                $response = array
                (
                    'data'       => array('api_token' => $user->api_token),
                    'offset'     => 0,
                    'count'      => 1,
                    'total'      => 1, 
                );
              break;
        }

        // return the response
        return \Response::json($response, $code);
    }

   
}
