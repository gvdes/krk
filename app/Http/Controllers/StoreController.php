<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoreController extends Controller
{
    private $sid = null;
    public function __construct(Request $request)
    {
        $this->sid = $sid = $request->route('sid');
    }

    public function index(Request $request){

        return response()->json("Welcome at the store ($this->sid)");
    }
}
