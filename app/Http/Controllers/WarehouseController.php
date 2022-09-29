<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Location;
use App\Models\WarehouseType;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index(Request $request){
        $sid = $request->route('sid');
        $rol = $request->fixeds->rol;

        $constraints = [['_store',$sid]];

        if($rol == 16){// para vendedores, solo mostraremos almacen del tipo exhibicion
            array_push($constraints,['_type',2]);
        }elseif($rol == 12){// para almacenistas, solo mostraremos almacenes del tipo general
            array_push($constraints,['_type',1]);
        }

        $warehouses = Warehouse::with(['type'])->where($constraints)->get();
        $types = WarehouseType::all();

        return response()->json(["warehouses"=>$warehouses,"types"=>$types,"rol"=>$rol]);
    }

    public function open(Request $request){
        $wid = $request->route('wid');

        $warehouse = Warehouse::find($wid);

        return response()->json(["warehouse"=>$warehouse]);
    }

    public function structure(Request $request){
        $wid = $request->route('wid');

        $sections = Location::where([ ["root",0],["_warehouse",$wid] ])->get();
        return response()->json(["sections"=>$sections]);
    }

    public function sectionate(Request $request){
        $sibsSave = [];
        $wid = $request->route('wid');
        $siblings = $request->siblings;// cantidad de hermanos acrear
        $name = $request->name;
        $alias = $request->alias;
        $group = $request->group;
        $isnewgroup = $group?false:true; // indica si es un gupo nuevo
        $groupname = $isnewgroup ? "$name,$alias":$group["rawgroup"];// construimos el nombre del grupo
        $currentSiblings = Location::where([
            ["root", 0],
            ["_warehouse", $wid],
            ["_group", $groupname]
        ])->count();// traemos la cantidad de miembros actuales del grupo
        $resgroup = $isnewgroup ? "Se creara el grupo $groupname con $siblings miembros" : "Se integraran $siblings miembros al grupo $groupname";

        $x = $currentSiblings ? ($currentSiblings+1) : ($siblings>1 ? 1 : null);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            for ($i=1; $i<=$siblings ; $i++) {
                $row = new Location([
                    "name"      => "$name $x",
                    "alias"     => "$alias$x",
                    "path"      => "$alias$x",
                    "root"      => 0,
                    "deep"      => 0,
                    "_warehouse"=> $wid,
                    "_group"    => $groupname,
                ]);
                $row->save();
                $row->fresh();
                $sibsSave[] = $row;
                $x ? $x++ : null;
            }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return response()->json([
            "getted"=>$request->all(),
            "idwrh"=>$wid,
            "newgroup"=>$isnewgroup,
            "groupname"=>$groupname,
            "currentSiblings"=>$currentSiblings,
            "createds"=>$sibsSave,
            "resgroup"=>$resgroup,
        ]);
    }

    public function create(){
        return response()->json("You try to create a warehouse");
    }
}
