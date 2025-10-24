<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categories extends Model
{
    protected $table='categories';
    protected $primaryKey='id_kategori';
    public $timestamps=false;
    protected $fillable=['nama_kategori'];
}
