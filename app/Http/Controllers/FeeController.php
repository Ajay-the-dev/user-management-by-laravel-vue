<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fee;
use App\Models\FeeStudent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use stdClass;


class FeeController extends Controller
{
    /**
     * get all fees paginated
     */
    public function index()
    {
        try {
            $fees = Fee::with('batches')->get();
        } catch (\Throwable $th) {
            $res = new stdClass();
            $res->status = 0;
            $res->message = 'Something went wrong while fetching batches';
            $res->data = $request->all();
            return response()->json($res);
        }
        return response()->json(["data"=>$fees,"status"=>1]);
    }

    /**
     * get fees by batch id
     */
    public function getFeesByBatch(Request $request,$id)
    {
        try {
            $query = Fee::query();
            $query->where('batchId',$id)->get();
            if(!$id)
            {
                return response()->json([
                'status' => 0,
                'message' => 'Batch ID is required'
                ], 400);
            }
            $entries = $query->orderBy('id','desc')->get();
            return response()->json(["message"=>"found succesfully","status"=>1,"data"=>$entries]);
        } catch (\Throwable $th) {
            $res = new stdClass();
            $res->status = 0;
            $res->message = 'Something went wrong while fetching batches with fees';
            $res->data = json_encode($th);
            return response()->json($res);
        }
    }

    /**
     * insert a transaction
     */
    public function insertPayment(Request $request)
    {
        try {
            $feeId = $request->input('feeId');
            $userId = $request->input('userId');
            $payment = json_encode($request->input('payment'));
            $amount = $request->input('paid_amount') ?  (float)$request->input('paid_amount'):0;


            $fee = Fee::findOrFail($feeId);

            if(!$fee)
            {
                $res = new stdClass();
                $res->status = 0;
                $res->message = 'Fee rule not found';
                $res->data = json_encode($th);
                return response()->json($res);
            }
            else{

                $allocatedFee = (float)$fee->amount;

                //overpaid
                if($allocatedFee < $amount)
                {
                    $res = new stdClass();
                    $res->status = 0;
                    $res->message = 'Overplay restriction!';
                    $res->data = json_encode($th);
                    return response()->json($res);
                }
                else{

                    $paidFee = (float) FeeStudent::where('userId', $userId)->where('feeId', $feeId)->sum('paid_amount') ?? 0;
                    if(($paidFee + $amount) > $allocatedFee)
                    {
                        $res = new stdClass();
                        $res->status = 0;
                        $res->message = 'Overpay blocked by system or Already paid';
                        // $res->data = [""];
                        return response()->json($res);
                    }
                    $entry =  FeeStudent::create([
                            "feeId" => $feeId,
                            "userId" => $userId,
                            "paid_amount"=>$amount,
                            "payment" =>$payment,
                            "created_by" =>1
                        ]);

                    $res = new stdClass();
                    $res->status = 1;
                    $res->message = 'Fee paid successfully';
                    $res->data = $entry;
                    return response()->json($res);
                }
                

            }

        } catch (\Throwable $th) {
            $res = new stdClass();
            $res->status = 0;
            $res->message = 'Something went wrong while paying fees';
            $res->data = json_encode($th);
            return response()->json($res);
        }
    }

    /**
     * get student fee summary by  student id
     */
    public function getStudentFeeSummary(Request $request,$id)
    {
        try {
            $summary = DB::table('fees as f')
                ->join('fee_students as fs', 'f.id', '=', 'fs.feeId')
                ->select(
                    'f.id',
                    'f.type',
                    'f.amount as total_allocated',
                    'f.due',
                    DB::raw('SUM(fs.paid_amount) as total_paid'),
                    DB::raw('(f.amount - SUM(fs.paid_amount)) as balance')
                )
                ->where('fs.userId', $id)
                ->groupBy('f.id', 'f.type', 'f.amount','f.due')
                ->get();

            return response()->json([
                'status' => 1,
                'data' => $summary
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'Error fetching fee summary',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * get fee details by fee id
     */
    public function getFeeSummaryByFeeId(Request $request)
    {
        try {

            $userId = $request->input('userId');
            $feeId = $request->input('feeId');
            $amount = FeeStudent::where('userId', $userId)->where('feeId', $feeId)->sum('paid_amount') ?? 0;
            return response()->json(["data"=>$amount]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'Error fetching fee summary',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * get recent 5 fee entries
     */
    public function getRecentFee(Request $request)
    {
        try {

            $students = FeeStudent::with(['fee','student'])
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
            return response()->json([
                'status' => 1,
                'message' => 'fetched successfully',
                'data' => $students
            ]);
        } catch (\Throwable $th) {
             return response()->json([
                'status' => 0,
                'message' => 'Error fetching fee summary',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * get total assigned fee of current year
     */
    public function getTotalAssigned(Request $request)
    {
        try {
          $results = DB::table('fees as fs')
            ->join('batches as b', 'b.id', '=', 'fs.batchId')
            ->join('users as u', 'u.batchId', '=', 'b.id')
            ->select(
                'fs.id as fee',
                'b.id as batch',
                'fs.amount',
                DB::raw('COUNT(u.id) as user_count'),
                DB::raw('COUNT(u.id) * fs.amount as total_revenue')
            )
            ->whereYear('fs.created_at', now()->year) 
            ->groupBy('fs.id', 'b.id', 'fs.amount')
            ->orderBy('fs.id')
            ->get();
            $grandTotal = $results->sum('total_revenue');
            return response()->json([
                'status' => 1,
                'message' => 'fetched successfully',
                'data' => $grandTotal
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'Error fetching fee summary',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    /**
     *  consolidated year wise report for current year
     */
    public function getYearlyReportWithRecents(Request $request)
    {
        try {

            $currentYear = date('Y');
            $recentStudents = [];

            $getRecentStudents = $this->getRecentFee($request);
            $recentData = (Object)$getRecentStudents->getData(true);

            if($recentData)
            {
                $recentStudents = $recentData->data;
            }

            $getAssignedFees = $this->getTotalAssigned($request);
            $assignedFeeData = (Object)$getAssignedFees->getData();
            $assignedFees = $assignedFeeData->data;
            $totalCollected = FeeStudent::whereYear('created_at', $currentYear)->sum('paid_amount');

            $data = json_encode([
                'year' => $currentYear,
                'totalAmount' => $totalCollected,
                'totalAssigned' => $assignedFees,
                'recentRecords' => $recentStudents
            ]);

    
            return response()->json(["message"=>"fetched successfully","data" => json_decode($data),"status" => 1 ]);
        } catch (\Throwable $th) {
             return response()->json([
                'status' => 0,
                'message' => 'Error fetching fee summary',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * get active fee headers
     */
    public function getActiveFees(Request $request)
    {
        try {
            $activeFees = Fee::where('status', 'ACTIVE')->latest()->with('batches')->get();
            return response()->json(["message"=>"fetched successfully","data" => $activeFees,"status" => 1 ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'Error fetching active fees',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    /**
     *  store fees headers
     */
    public function storeFeeHeader(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'batchId' => 'required|integer|exists:batches,id',
            'type'    => 'required|string|max:255',
            'amount'  => 'required|numeric|min:0',
            'status' => 'required|in:ACTIVE,INACTIVE,ARCHIVE',
            'due'     => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $fee = Fee::create([
                'batchId'   => $request->batchId,
                'type'      => $request->type,
                'amount'    => $request->amount,
                'status'    => $request->status,
                'due'       => $request->due,
                'created_by'=> auth()->id(),
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Fee created successfully',
                'data' => $fee
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
