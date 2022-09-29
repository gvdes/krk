<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $hidden = [ 'password' ];

    public function store(){ return $this->hasOne('App\Models\Store','id','_store'); }

    public function stores(){ return $this->belongsToMany('App\Models\Store','user_stores','_user','_store'); }

    // public function modules(){ return $this->hasManyThrough('App\Models\ModuleApp','App\Models\UserModules','_user','id','id','_module'); }
    public function modules(){ return $this->hasMany('App\Models\UserModules','_user'); }

    public function permissions(){ return $this->hasMany('App\Models\UserPermissions','_user'); }

    public function state(){ return $this->hasOne('App\Models\UserStates','id','_state'); }

    public function rol(){ return $this->hasOne('App\Models\UserRol','id','_rol'); }

    public function treeModulesAuths($moduleRoot=0){
        $this->modules = $this->modulesOf($moduleRoot);
        return $this;
    }

    private function modulesOf($moduleRoot){
        return DB::table('user_permissions as UP')
            ->join('modules_app as MA','MA.id','=','UP._module')
            ->where([
                ['UP._user',$this->id],
                ['MA.root',$moduleRoot]
            ])
            ->select( 'UP.*', 'MA.name as name', 'MA.deep as deep', 'MA.root as root', 'MA.path as path' )
            ->get()->map(function($m){
                $m->submodules = $this->modulesOf($m->_module);
                $m->permission = $this->permissionInModule($m->_module);
                return $m;
            });
    }

    private function permissionInModule($module){
        return DB::table('user_permissions as UP')
                        ->join('modules_app as MA','MA.id','=','UP._module')
                        ->join('permissions as P','P.id','=','UP._permission')
                        ->where([
                            ['UP._user',$this->id],
                            ['MA.id',$module]
                        ])->select('P.*')->first();
    }
}
