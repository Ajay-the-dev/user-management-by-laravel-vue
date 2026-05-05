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
        $res = new stdClass();
        try {

            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $res->status = 0;
                $res->message = 'Validation failed';
                $res->data = $validator->errors();
                return response()->json($res, 422);
            }

            if(Auth::attempt(["username" => $request->username, "password" => $request->password]))
            {
                $user = Auth::user();
                $tokenName = $request->header('User-Agent') ?? 'web-access-token';
                $token = $user->createToken($tokenName)->plainTextToken;

                $user->load('batches');
                $res->status = 1;
                $res->message = 'Validated successfully';
                $res->data = $user;
                $res->token = $token;
                return response()->json($res);
            }
            else{
                $res->status = 0;
                $res->message = 'Invalid username/password';
                $res->data = '';
                return response()->json($res);
            }

        } catch (\Throwable $th) {
            $res->status = 0;
            $res->message = 'Something went wrong while loggin in';
            $res->error = $th->getMessage();
            return response()->json($res);
        }
    }

    public function store(Request $request)
    {
        try {

                // return response()->json($request->all());

                $validate = Validator::make($request->all(), [
                    'data.name'                 => 'required|string|max:255',
                    'data.username'             => 'required|string|min:3|max:30|unique:users,username',
                    'data.email'                => 'required|email|unique:users,email',
                    'data.mobile'               => 'nullable|string',
                    'data.dob'                  => 'required|date',
                    'data.gender'               => 'required|in:male,female,other',
                    'data.password'             => 'nullable',
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
                    'address'                   => 'nullable',
                    'address.*.street'          => 'nullable|string',
                    'address.*.city'            => 'nullable|string',
                    'address.*.country'         => 'nullable|string',
                    'parentDetails'             => 'nullable|array|min:1',
                    'parentDetails.*.name'      => 'nullable|string',
                    'parentDetails.*.relation'  => 'nullable|string',
                    'parentDetails.*.mobile'     => 'nullable',
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

                $role = $request->input('isStaff') === 1 ? 'STAFF' : 'STUDENT';

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
                $res->message = 'New user added successfully';
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
                    $query = User::with(['batches','StudentDoc']);
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
                    'data.name'                 => 'required|string|max:255',
                    'data.username'             => 'required|string|min:3|max:30',
                    'data.email'                => 'required|email',
                    'data.mobile'               => 'nullable|string',
                    'data.dob'                  => 'required|date',
                    'data.gender'               => 'required|in:male,female,other',
                    'data.password'             => 'nullable',
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
                    'address'                   => 'nullable',
                    'address.*.street'          => 'nullable|string',
                    'address.*.city'            => 'nullable|string',
                    'address.*.country'         => 'nullable|string',
                    'parentDetails'             => 'nullable|array|min:1',
                    'parentDetails.*.name'      => 'nullable|string',
                    'parentDetails.*.relation'  => 'nullable|string',
                    'parentDetails.*.mobile'     => 'nullable',
                    'data.insuranceStatus'           => 'nullable|string',
                    'data.insuranceExpiryDate'       => 'nullable|date',
                    'data.university'                =>  'nullable|string',
                    'data.visaType'                  => 'nullable|string',
                    'data.visaStatus'                => 'nullable|string',
                    ]);
    
                if ($validate->fails()) {
                        $res = new stdClass();
                        $res->status = 0;
                        $res->message = 'Validation failed';
                        $res->data = $validate->errors();
                        return response()->json($res);
                }

                $user->role = $request->input('isStaff') === 1 ? 'STAFF' : 'STUDENT';
                $user->name = $request->input('data.name');
                $user->username = $request->input('data.username');
                $user->email = $request->input('data.email');
                $user->dob = $request->input('data.dob');
                $user->mobile = $request->input('data.mobile');
                $user->gender = $request->input('data.gender');
                $user->address = $request->input('address');
                $user->batchId = $request->input('data.batchId');
                $user->course = $request->input('data.course');
                $user->rollNo = $request->input('data.rollNo');
                $user->university = $request->input('data.university');
                $user->visaType = $request->input('data.visaType');
                $user->visaStatus = $request->input('data.visaStatus');
                $user->designation = $request->input('data.designation');
                $user->department = $request->input('data.department');
                $user->location = $request->input('data.location');
                $user->parentDetails = $request->input('parentDetails');
                $user->passportNumber = $request->input('data.passportNumber');
                $user->passportExpiryDate = $request->input('data.passportExpiryDate');
                $user->passportIssuingCountry = $request->input('data.passportIssuingCountry');
                $user->passportIssueDate = $request->input('data.passportIssueDate');
                $user->insuranceStatus = $request->input('data.insuranceStatus');
                $user->insuranceExpiryDate = $request->input('data.insuranceExpiryDate');
                if ($request->input('data.password')) {
                    $user->password = bcrypt($request->input('data.password'));
                }
                if ($request->input('data.profile_picture')) {
                    $user->profile_picture = $request->input('data.profile_picture');
                }

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


    public function getUserById(Request $request,$user="")
    {
        try {
            $id = $user ? $user : $request->id;
            $user = User::findOrFail($id)->load('batches');
            $res = new stdClass();
            $res->status = 1;
            $res->message = 'fetched successfully';
            $res->data = $user;
            return response()->json($user);
            
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error while checking username', 'error' => $th->getMessage(), 'status' => 0], 500);
        }
    }

    public function getStudentsByBatch(Request $request,$id)
    {
        try{
            if(!$id)
            {
                return response()->json([
                'status' => 0,
                'message' => 'Batch ID is required'
                ], 400);
            }
            else{
                $query = User::query($id);
                $query->where('batchId', $id);
                $entries = $query->orderBy('id','asc')->get();
                $res = new stdClass();
                $res->status = 1;
                $res->message = 'fetched successfully';
                $res->data = $entries;
                return response()->json($res);
            }
        }
         catch (\Throwable $th) {
            return response()->json(['message' => 'error while fetching students', 'error' => $th->getMessage(), 'status' => 0], 500);
        }
    }
}
