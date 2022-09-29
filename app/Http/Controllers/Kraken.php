<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class Kraken extends Controller
{
    public function trySignin(Request $request){
        $nick = $request->nick;
        $pass = $request->pass;

        $user = User::with([
                        'store',
                        'stores',
                        'state',
                        'rol',
                        'modules' => fn($q) => $q->with([ 'permission','module' ])
                    ])
                    ->where('nick',$nick)
                    ->orWhere('celphone',$nick)
                    ->orWhere('email',$nick)
                    ->firstOrFail();

        if(Hash::check($pass,$user->password)){

            $user->modules->map(function($m){ $m->module->details = json_decode($m->module->details); return $m; });

            if($user->_state<=2){
                $data = [ "uid"=>$user->id, "rol"=>$user->_rol ];// esta data es la que se encrypta en el token
                $token = $this->genToken($data);
                return response()->json([ "account"=>$user, "token"=>$token ]);
            } return response()->json(["state"=>$user->state],401);// cuenta bloqueada o archivada
        } return response("credenciales erroneas!", 404);// password incorrecto
    }

    private function genToken($data){
        $data["ini"] = Carbon::now();
        $data["exp"] = Carbon::now()->add(3,"day");
        return Crypt::encryptString(json_encode($data));
    }

    public function setPassword(Request $request){
        $uid = $request->fixeds->uid;
        $newPass = Hash::make($request->newpass);

        $user = User::find($uid);

        $user->password = $newPass;
        $user->_state = 2;
        $user->change_password = 0;
        $user->save();

        $user->fresh();

        $user->load([
            'store',
            'stores',
            'state',
            'rol',
            'modules' => fn($q) => $q->with([ 'permission','module' ])
        ]);

        return response()->json($user);
    }

    public function joinat(Request $request){
        return response()->json($request->all());
    }
}
