<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Racks extends Model
    {
        protected $table='racks';
        protected $primaryKey='id_rak';
        public $timestamps=false;
        protected $fillable=['barcode','nama','kolom','baris','kapasitas','id_lokasi','id_kategori'];
        public function rackslocation()
        {
            return $this->belongsTo(Rackslocation::class,'id_lokasi', 'id_lokasi');
        }
        public function categories()
        {
            return $this->belongsTo(Categories::class, 'id_kategori');
        }

        public function sortbooks()
        {
            return $this->hasMany(Sortbooks::class,'id_rak');
        }
    }
