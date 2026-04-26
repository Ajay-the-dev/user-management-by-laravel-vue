<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\StudentDoc;
use stdClass;
class DocumentController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf,docx,jpg,jpeg,png|max:10240',
            ]);
            
            
            if ($request->hasFile('file')) {

                $file = $request->file('file');
                $localFileName = bin2hex(random_bytes(6));
                
                $fileName =  $localFileName. '.' . $file->getClientOriginalExtension();
                $fullPath = $file->storeAs('/documents', $fileName, 'public');
                if($fullPath === false)
                {
                    return response()->json(['error' => 'upload failed'], 500);
                }
    
                return response()->json(['message' => 'Image uploaded!', 'url' => $fullPath,'fileName'=>$fileName]);
            }
    
            return response()->json(['message' => 'No file uploaded'], 400);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function documentEntryForUser(Request $request)
    {
        try {

            $url = $request->input('url');
            $userId = $request->input('userId');
            $title = $request->input('title') ? $request->input('title') : 'Document';


            if($url === '' || $userId === '' )
            {
                return response()->json(['message' => "user or doc not found","status"=>1], 400);
            }
            else{
                $doc =StudentDoc::create([
                    'userId' => $userId,
                    'url' => $url,
                    'title' => $title
                ]);
                return response()->json(['message' => "Uploaded successfully","status"=>1, "data" => $doc], 200);
            }
            
        } catch (\Throwable $th) {
            return response()->json(['message'=>"Something went wrong while saving upload data",'error' => $th->getMessage(),"status"=>0], 500);
        }
    }

    public function getDocumentsByUser(Request $request,$id)
    {
        try {
            if(!$id)
            {
                return response()->json(['message'=>"userId not there",'error' => $th->getMessage(),"status"=>0], 402);
            }
            else{
                $query = StudentDoc::query();
                $query->where('userId', $id);
                $entries = $query->orderBy('id','desc')->get();
                return response()->json(['message'=>"Fetched successfully",'data' => $entries,"status"=>1], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message'=>"Something went wrong while fetching uploads",'error' => $th->getMessage(),"status"=>0], 500);
        }
    }

    public function remove(Request $request, $id)
    {
        try {

            $doc = StudentDoc::findOrFail($id);
            if($doc)
            {
                $doc->delete();

                $res = new stdClass();
                $res->status = 1;
                $res->message = 'Deleted successfully';
                $res->data = '';
                return response()->json($res);
            }
            else{
                
                $res = new stdClass();
                $res->status = 0;
                $res->message = 'Doc not found';
                $res->data = $id;
                return response()->json($res);
            }
        } catch (\Throwable $th) {
            $res = new stdClass();
            $res->status = 0;
            $res->message = 'Something went wrong while deleting doc';
            $res->data = $th->getMessage();
            return response()->json($res);
        }
    }
}
