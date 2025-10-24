<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class publisher extends Model
{
    protected $table='publisher';
    protected $primaryKey='id_penerbit';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps=false;
    protected $fillable=['nama_penerbit','alamat','no_telepon','email'];


}
