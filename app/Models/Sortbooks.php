<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sortbooks extends Model
{
    protected $table='sortbooks';
    protected $primaryKey='id_penataan';
    protected $fillable=['id_buku','id_rak','kolom','baris','jumlah','sumber','id_user'];

    protected $dates=['insert_date','modified_date'];

    public $timestamps=true;

    public const CREATED_AT = 'insert_date';
    public const UPDATED_AT = 'modified_date';

    //relasi
    public function books()
    {
        return $this->belongsTo(books::class,'id_buku');
    }

    public function racks()
    {
        return $this->belongsTo(Racks::class,'id_rak');
    }

    // Accessor buat tampil nama user (kalo perlu relasi)
    public function getUserNameAttribute()
    {
        return $this->name;  // Atau query User::where('name', $this->name)->first()->name;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');  // id_user -> users.id
    }
}
