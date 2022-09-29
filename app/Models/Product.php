<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = "products";

    public function relateds(){
        return $this->hasMany('App\Models\ProductAdditionalBarcode','_product','id');
    }

    public function locations(){
        return $this->hasManyThrough('App\Models\Location','App\Models\ProductLocation','_product','id','id','_location');
    }

    public function stocks(){
        return $this->hasMany('App\Models\ProductStock','_product','id');
    }
}


/**
 *
 * "SQLSTATE[42S22]:
 * Column not found: 1054 Unknown column 'product_stock.id' in 'where clause'
 * (SQL: select * from `product_stock` where `product_stock`.`id` in (?))"
 */
