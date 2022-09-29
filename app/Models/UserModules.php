<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserModules extends Model
{
    use HasFactory;

    protected $table = 'user_permissions';

    public function permission(){
        return $this->hasOne('App\Models\Permission','id','_permission');
    }

    public function module(){
        return $this->hasOne('App\Models\ModuleApp','id','_module');
    }

    public function submodules(){
        return $this->hasMany('App\Models\ModuleApp','id');
    }
}
