<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\batches;
use Illuminate\Support\Facades\Validator;


class BatchController extends Controller
{
    public function index()
    {
        try {
            $batches = batches::orderBy('id', 'desc')->paginate(10);
            return response()->json(['data' => $batches]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Unable to fetch batches', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $name = $request->input('name');
            $description = $request->input('description');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $status = $request->input('status');
            $semester = $request->input('semester');

            $validate = Validator::make($request->all(), [
                        'name'      => 'required|string|max:255',
                        'description'     => 'required|string|max:255',
                        'start_date'    => 'required|date',
                        'end_date'      => 'required|date|after_or_equal:start_date',
                        'status'        => 'required|in:active,inactive',
                        'semester'      => 'required|integer|min:0|max:10',
                    ]);
            if ($validate->fails()) {
                return response()->json(['errors' => $validate->errors()], 422);
            }

            $batch = batches::create([
                'name' => $name,
                'description' => $description,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'semester' => $semester,
                'updated_by' => 1,
                'created_by' => 1
            ]);
            return response()->json(['message' => 'Batch created', 'data' => $batch, 'status' => 1], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Unable to create batch', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $name = $request->input('name');
            $description = $request->input('description');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $status = $request->input('status');
            $semester = $request->input('semester');
            $validate = Validator::make($request->all(), [
                        'name'      => 'required|string|max:255',
                        'description'     => 'required|string|max:255',
                        'start_date'    => 'required|date',
                        'end_date'      => 'required|date|after_or_equal:start_date',
                        'status'        => 'required|in:active,inactive',
                        'semester'      => 'required|integer|min:0|max:10',
                    ]);
            if ($validate->fails()) {
                return response()->json(['errors' => $validate->errors()], 422);
            }

            $updated = batches::where('id', $id)->update([
                'name' => $name,
                'description' => $description,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'semester' => $semester,
                'updated_by' => 1
            ]);
            return response()->json(['message' => "$name updated","status" => 1,"data"=>$updated], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Unable to update batch', 'error' => $e->getMessage()], 500);
        }
    }

    public function remove($id)
    {
        try {
            if ($id) {
                batches::where('id', $id)->delete();
                return response()->json(['message' => "Batch removed","status" => 1], 200);
            } else {
                return response()->json(['message' => "Batch id is required"], 400);
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Unable to remove batch', 'error' => $e->getMessage()], 500);
        }
    }

    public function findByName(Request $request)
    {
        try {
            
            $name = $request->input('name');
            if ($name) {
                $batch = batches::where('name', 'like', "%$name%")->paginate(10);
                return response()->json(['data' => $batch, 'status' => 1], 200);
            } else {
                return response()->json(['message' => "Batch name is required","status" => 1], 200);
            }

        } catch (\Throwable $e) {
            return response()->json(['message' => 'error while finding batch', 'error' => $e->getMessage()], 500);
        }
    }
}
