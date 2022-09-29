<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\Warehouse;

class Elo extends Controller
{
    public function index(){
        $loc = 118;

        $location = Location::findOrFail($loc);
        $location->load(['warehouse' => fn($q) => $q->with('store')]);
        $wid = $location->warehouse->store->id;

        $idwrhs = Warehouse::where("_store",$wid)->select("id")->get();

        $location->load([
            'parent',
            'products' => fn($q) => $q->with([
                    'stocks' => fn($q) => $q->with([ 'warehouse' ])->whereIn("_warehouse", $idwrhs)
                ])
                ->where( "product_locations.deleted_at",null )
                ->select('id','short_code','code','barcode','description'),
        ]);

        return 'Ok';
        // return response()->json($user);
    }
}
