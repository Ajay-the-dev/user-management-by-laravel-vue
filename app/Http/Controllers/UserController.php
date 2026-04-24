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
                    'data.name'                 => 'required|string|max:255',
                    'data.username'             => 'required|string|min:3|max:30|unique:users,username',
                    'data.email'                => 'required|email|unique:users,email',
                    'data.mobile'               => 'nullable|string',
                    'data.dob'                  => 'required|date',
                    'data.gender'               => 'required|in:male,female,other',
                    'data.password'             => 'required|string|min:8',
                    'data.batchId'              => 'nullable|exists:batches,id',
                    'data.course'               => 'nullable|string',
                    'data.rollNo'               => 'nullable|string',
                    'data.designation'          => 'nullable|string',
                    'data.location'             => 'nullable|string',
                    'data.department'           => 'nullable|string',
                    'data.passportNumber'       => 'nullable|string',
                    'data.passportExpiryDate'   => 'nullable|date',
                    'data.passportIssuingCountry' => 'nullable|string',
                    'data.passportIssueDate'    => 'nullable|date',
                    'address'                   => 'nullable|array|min:1',
                    'address.*.street'          => 'required|string',
                    'address.*.city'            => 'required|string',
                    'address.*.country'         => 'required|string',
                    'parentDetails'             => 'nullable|array|min:1',
                    'parentDetails.*.name'      => 'required|string',
                    'parentDetails.*.relation'  => 'required|string',
                    'parentDetails.*.mobile'     => 'required',
                    'data.insuranceStatus'           => 'nullable|string',
                    'data.insuranceExpiryDate'       => 'nullable|date',
                    'data.university'                =>  'nullable|string',
                    'data.visaType'                  => 'nullable|string',
                    'data.visaStatus'                => 'nullable|string',
                ]);

                if ($validate->fails()) {
                    $res = new stdClass();
                    $res->status = 0;
                    $res->message = 'validation failed';
                    $res->data = $validate->errors();
                    return response()->json($res);
                }

                $role = $request->input('isStaff') === '1' ? 'STAFF' : 'STUDENT';

                $entry = User::create([
                    "name" => $request->input('data.name'),
                    "username" => $request->input('data.username'),
                    "email" => $request->input('data.email'),
                    "password" => bcrypt($request->input('data.password')),
                    "mobile" => $request->input('data.mobile'),
                    "dob" => $request->input('data.dob'),
                    "gender" => $request->input('data.gender'),
                    "address" => $request->input('address'),
                    "batchId" => $request->input('data.batchId'),
                    "role" => $role,
                    "course" => $request->input('data.course'),
                    "rollNo" => $request->input('data.rollNo'),
                    "university" => $request->input('data.university'),
                    "visaType" => $request->input('data.visaType'),
                    "visaStatus" => $request->input('data.visaStatus'),
                    "designation" => $request->input('data.designation'),
                    "department" => $request->input('data.department'),
                    "parentDetails" => $request->input('parentDetails'),
                    "passportNumber" => $request->input('data.passportNumber'),
                    "passportExpiryDate" => $request->input('data.passportExpiryDate'),
                    "passportIssuingCountry" => $request->input('data.passportIssuingCountry'),
                    "passportIssueDate" => $request->input('data.passportIssueDate'),
                    "insuranceStatus" => $request->input('data.insuranceStatus'),
                    "insuranceExpiryDate" => $request->input('data.insuranceExpiryDate'),
                    "profile_picture"   =>  $request->input('data.profile_picture') ? $request->input('data.profile_picture') : null,
                    "batchId" => $request->input('data.batchId'),
                    "location" => $request->input('data.location')
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
                $isStaff = $request->query('isStaff');
                $query = User::query();
                
                if ($isStaff == "1") {
                    $query->whereIn('role', ['STAFF', 'ADMIN']);
                } else if ($isStaff == "0") {
                    $query = User::with('batches');
                    $query->where('role', 'STUDENT');
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
                        'data.email'     => 'email',
                        'data.username'  => 'required|string|min:3|max:30',
                        'data.mobile'    => 'digits:10-15',
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

    public function findByName(Request $request)
    {
        try {
            
            $name = $request->input('name');
            $isStaff = $request->input('isStaff');
            $query = User::query();
                
            if ($isStaff == "1") {
                $query->whereIn('role', ['STAFF', 'ADMIN']);
            } else if ($isStaff == "0") {
                $query = User::with('batches');
                $query->where('role', 'STUDENT');
            }

            if ($name) {
                $users = $query->where('name', 'like', "%$name%")->paginate(10);
                return response()->json(['data' => $users, 'status' => 1], 200);
            } else {
                return response()->json(['message' => "name is required","status" => 1], 200);
            }

        } catch (\Throwable $e) {
            return response()->json(['message' => 'error while finding batch', 'error' => $e->getMessage()], 500);
        }
    }

    public function isChecking(Request $request)
    {
        try {
            $username = $request->input('username');
            $field = $request->input('field') ? $request->input('field') : 'username';
            if ($username) {
                $exists = User::where($field, $username)->exists();
                return response()->json(['exists' => $exists, 'status' => 1], 200);
            } else {
                return response()->json(['message' => "username is required", 'status' => 0], 200);
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => 'error while checking username', 'error' => $e->getMessage(), 'status' => 0], 500);
        }
    }


    public function getUserById(Request $request)
    {
        try {

        $id = $request->id;
        $user = User::findOrFail($id);
        $res = new stdClass();
        $res->status = 1;
        $res->message = 'fetched successfully';
        $res->data = $user;
        return response()->json($user);
            
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error while checking username', 'error' => $th->getMessage(), 'status' => 0], 500);
        }
    }
}
