<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


use stdClass;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            if(Auth::attempt(["username"=>$request->username, "password"=>$request->password]))
            {
                $res = new stdClass();
                $user = Auth::user();
                $res->status = 1;
                $res->message = 'Validated successfully';
                $res->data = $user;
                return response()->json($res);
            }
            else{
                $res = new stdClass();
                $user = Auth::user();
                $res->status = 0;
                $res->message = 'Invalid username/password';
                $res->data = '';
                return response()->json($res);
            }



        } catch (\Throwable $th) {
            $res = new stdClass();
            $res->status = 0;
            $res->message = 'Something went wrong while loggin in';
            $res->data = $request->all();
            return response()->json($res);
        }
    }

    public function store(Request $request)
    {
        try {

                $validate = Validator::make($request->all(), [
                    'data.name'      => 'required|string|max:255',
                    'data.lname'     => 'required|string|max:255',
                    'data.gender'    => 'required|in:male,female,other',
                    'data.dob'       => 'required|date|before_or_equal:today',
                    'data.email'     => 'required|email',
                    'data.username'  => 'required|string|min:3|max:30',
                    'data.mobile'    => 'required|digits:10',
                    'data.password'  => 'required|string|min:8',
                    'address'                      => 'required|array|min:1',
                    'address.*.officeType'        => 'required|string',
                    'address.*.street'            => 'required|string',
                    'address.*.city'              => 'required|string',
                    'address.*.state'             => 'required|string',
                    'address.*.country'           => 'required|string',
                ]);

                if ($validate->fails()) {
                    $res = new stdClass();
                    $res->status = 0;
                    $res->message = 'validation failed';
                    $res->data = $validate->errors();
                    return response()->json($res);
                }

                // return response()->json($request->data['name']);
                
                $entry = User::create([
                    "name" => $request->input('data.name'),
                    "username" => $request->input('data.username'),
                    "email" => $request->input('data.email'),
                    "password" => bcrypt($request->input('data.password')),
                    "mobile" => $request->input('data.mobile'),
                    "dob" => $request->input('data.dob'),
                    "gender" => $request->input('data.gender'),
                    "address" => json_encode($request->input('address'))
                ]);

                
                $res = new stdClass();
                $res->status = 1;
                $res->message = 'Data stored successfully';
                $res->data = $entry;
                return response()->json($res);
           
        } catch (\Throwable $th) {
            $res = new stdClass();
            $res->status = 0;
            $res->message = 'Something went wrong storing';
            $res->data = $th->getMessage();
            return response()->json($res);
        }
    }

    public function index (Request $request)
    {
        try {

                $users = User::orderBy('created_at', 'desc')->paginate(10);

                $res = new stdClass();
                $res->status = 1;
                $res->message = 'Data fetched successfully';
                $res->data = $users;
                return response()->json($res);
        } catch (\Throwable $th) {
            $res = new stdClass();
            $res->status = 0;
            $res->message = 'Something went wrong while getting users';
            $res->data = $th->getMessage();
            return response()->json($res);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $user = User::findOrFail($id);

            if($user)
            {
                $validate = Validator::make($request->all(), [
                        'data.name'      => 'required|string|max:255',
                        'data.gender'    => 'required|in:male,female,other',
                        'data.dob'       => 'required|date|before_or_equal:today',
                        'data.email'     => 'required|email',
                        'data.username'  => 'required|string|min:3|max:30',
                        'data.mobile'    => 'required|digits:10',
                        'address'                      => 'required|array|min:1',
                        'address.*.officeType'        => 'required|string',
                        'address.*.street'            => 'required|string',
                        'address.*.city'              => 'required|string',
                        'address.*.state'             => 'required|string',
                        'address.*.country'           => 'required|string',
                    ]);
    
                if ($validate->fails()) {
                        $res = new stdClass();
                        $res->status = 0;
                        $res->message = 'Validation failed';
                        $res->data = $validate->errors();
                        return response()->json($res);
                }


                $user->name = $request->input('data.name');
                $user->username = $request->input('data.username');
                $user->email = $request->input('data.email');
                $user->dob = $request->input('data.dob');
                $user->mobile = $request->input('data.mobile');
                $user->gender = $request->input('data.gender');
                $user->address = $request->input('address');

                $user->save();




                $res = new stdClass();
                $res->status = 1;
                $res->message = 'Updated successfully';
                $res->data = $id;
                return response()->json($res);
            }
            else{
                $res = new stdClass();
                $res->status = 0;
                $res->message = 'User not found to update !';
                $res->data = $id;
                return response()->json($res);
            }

            
        } catch (\Throwable $th) {
            $res = new stdClass();
            $res->status = 0;
            $res->message = 'Something went wrong while updating user';
            $res->data = $th->getMessage();
            return response()->json($res);
        }
    }

    public function remove(Request $request, $id)
    {
        try {

            $user = User::findOrFail($id);
            if($user)
            {
                $user->delete();

                $res = new stdClass();
                $res->status = 1;
                $res->message = 'Deleted successfully';
                $res->data = '';
                return response()->json($res);
            }
            else{
                
                $res = new stdClass();
                $res->status = 0;
                $res->message = 'User not found';
                $res->data = $id;
                return response()->json($res);
            }
        } catch (\Throwable $th) {
            $res = new stdClass();
            $res->status = 0;
            $res->message = 'Something went wrong while deleting user';
            $res->data = $th->getMessage();
            return response()->json($res);
        }
    }

    public function filterByParams(Request $request)
    {
        try {
                
                $query = User::query();
                $param = $request->param;
                $value = $request->value;

                if(!empty($value))
                {
                    if($param === 'id' || $param === 'gender')
                    {
                        $query->where($param, $value);
                    }
                    else{
                        $query->where($param, 'LIKE', "%{$value}%");
                    }
                }

                $users = $query->orderBy('created_at', 'desc')->paginate(10);

                $res = new stdClass();
                $res->status = 1;
                $res->message = 'Data fetched successfully';
                $res->data = $users;
                return response()->json($res);
        } catch (\Throwable $th) {
            $res = new stdClass();
            $res->status = 0;
            $res->message = 'Something went wrong while filtering user';
            $res->data = $th->getMessage();
            return response()->json($res);
        }
    }

    public function resetPassword(Request $request, $id)
    {
        try {

                $user = User::findOrFail($id);
                if($user)
                {
                    $user->password =  bcrypt($user->username);
                    $user->save();
                    $res = new stdClass();
                    $res->status = 1;
                    $res->message = 'Updated successfully';
                    $res->data = $id;

                }
                else{
                    $res = new stdClass();
                    $res->status = 0;
                    $res->message = 'User not found for password reset';
                    $res->data = $id;
                    return response()->json($res);
                }

                $res = new stdClass();
                $res->status = 1;
                $res->message = 'Password reset completed successfully';
                $res->data = $user;
                return response()->json($res);

        } catch (\Throwable $th) {
            $res = new stdClass();
            $res->status = 0;
            $res->message = 'Something went wrong while resetting password';
            $res->data = $th->getMessage();
            return response()->json($res);
        }
    }
}
