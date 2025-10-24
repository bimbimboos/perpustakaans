<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class racks extends Model
{
    protected $table='racks';
    protected $primaryKey='id_rak';
    public $timestamps=false;
    protected $fillable=['barcode','nama','kolom','baris','kapasitas','id_lokasi','id_kategori'];
    public function rackslocation()
    {
        return $this->belongsTo(rackslocation::class,'id_lokasi');
    }
    public function categories()
    {
        return $this->belongsTo(categories::class, 'id_kategori');
    }

    public function sortbooks()
    {
        return $this->hasMany(sortbooks::class,'id_rak');
    }
}
