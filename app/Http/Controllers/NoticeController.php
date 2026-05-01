<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Notice;
use Illuminate\Support\Str;

class NoticeController extends Controller
{
    public function createNotice(Request $request)
    {
        try {
            
            $validate = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'type' => 'required|string',
                'audience' => 'required|string',
                'html' => 'required|string',
                'customBatch' => 'nullable|exists:batches,id']);

            if ($validate->fails()) {
                return response()->json(['errors' => $validate->errors()], 422);
            }

            $title = $request->input('title');
            $type = $request->input('type');
            $audience = $request->input('audience');
            $customBatch = $request->input('customBatch');
            $html = $request->input('html');
            $html = Str::markdown($html);

            $notice = Notice::create([
                "title" => $title,
                "type" => $type,
                "audience" => $audience,
                "customBatch" => $customBatch,
                "html"  => $html
            ]);
            return response()->json(['message' => "notice created successfully","data"=> $notice,"status"=>1], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'error while craeting notice  ', 'error' => $e->getMessage()], 500);
        }
    }

     public function index(Request $request)
    {
        try {
            $query = Notice::query();
            $query = Notice::with(['batches']);
            $notices = $query->orderBy('created_at', 'desc')->paginate(10);
            return response()->json([
                'status' => 1,
                'data' => $notices,
                'message' => "fetched successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error while fetching notice  ', 'error' => $th->getMessage()], 500);
        }
    }

    public function findByTitle(Request $request)
    {
        try {
            $title = $request->input('title', '');
            $notices = Notice::where('title', 'like', '%' . $title . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return response()->json([
                'status' => 1,
                'data' => $notices,
                'message' => "fetched successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error while fetching notice  ', 'error' => $th->getMessage()], 500);
        }
    }

    public function getDetailsById(Request $request, $id)
    {
        try {

            $notice = Notice::findOrFail($id);
            if ($notice) {
                 return response()->json([
                'status' => 1,
                'data' => $notice,
                'message' => "fetched successfully"
                ]);
            }
            else{
                return response()->json(['message' => 'notice not found'], 401);
            }
            
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error while fetching notice details ', 'error' => $th->getMessage()], 500);
        }
    }
}
