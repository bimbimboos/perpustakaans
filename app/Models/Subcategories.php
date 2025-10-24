<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class subcategories extends Model
{
    protected $table='subcategories';
    protected $primaryKey='id_subkategori';
    public $timestamps=false;
    protected $fillable=['nama_subkategori'];
}
