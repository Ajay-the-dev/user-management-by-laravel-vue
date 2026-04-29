<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fee;
use App\Models\FeeStudent;
use Illuminate\Support\Facades\DB;

use stdClass;


class FeeController extends Controller
{
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

    public function getRecentFee(Request $request)
    {
        try {

                $students = FeeStudent::with('fee','student')
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
    public function getYearlyReport()
    {
        $currentYear = date('Y');

        $recentStudents = $this->getRecentFee();
        $totalCollected = FeeStudent::whereYear('created_at', $currentYear)
            ->sum('paid_amount');

        return response()->json([
            'year' => $currentYear,
            'total_amount' => $totalCollected,
            'recent_records' => $recentStudents
        ]);
    }
}
