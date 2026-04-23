<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,jpg|max:2048',
            ]);
    
            
            if ($request->hasFile('image')) {

                $file = $request->file('image');
                $localFileName = bin2hex(random_bytes(6));
                
                $fileName =  $localFileName. '.' . $file->getClientOriginalExtension();
                $fullPath = $file->storeAs('/images', $fileName, 'public');
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

    public function getPicture(Request $request)
    {
        try {
            $path = $request->input('path') ;
            $fileName = $request->input('fileName');
            $extensions = ['png', 'jpg', 'jpeg'];
            foreach ($extensions as $ext) {
                $relativePath = $path . '/' . $fileName . '.' . $ext;
                if (Storage::disk('public')->exists($relativePath)){
                    $publicPath =  Storage::url($relativePath);
                    return response()->json(['message'=> 'link collected successfully','url'=> $publicPath]);
                }
            }

            return response()->json(['message'=> 'link not found','url'=> null],404);

        
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function deletePicture(Request $request)
    {
        try {
            $fileName = $request->input('fileName');
            $extensions = ['png', 'jpg', 'jpeg'];
            $relativePath = '/images/' . $fileName;

            if (Storage::disk('public')->exists($relativePath)){
                Storage::disk('public')->delete($relativePath);
                return response()->json(['message'=> 'image deleted successfully']);
            }
            else{
                return response()->json(['message'=> 'image not found'],200);
            }
        
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function saveFromUrl(Request $request,ImageService $imageService)
    {
        try {
            $fileName = $request->fileName;
            $path = $request->path;
            $url = $request->url;
            $response = $imageService->saveFromUrl($request->url, $fileName, $path);
            return response()->json(['location' => $response]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
