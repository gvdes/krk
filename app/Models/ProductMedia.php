<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    use HasFactory;
    protected $table = "product_files_attrs";
    protected $guarded = [];
    public $timestamps = false;
}
