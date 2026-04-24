<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fee;


class FeeController extends Controller
{
    public function index()
    {
        $fees = Fee::with('batches')->get();
        return response()->json(["data"=>$fees,"status"=>1]);
    }
}
