<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookitems extends Model
{
    protected $table = 'bookitems';
    protected $primaryKey = 'id_item';
    protected $fillable = ['id_buku', 'kondisi', 'status','sumber','id_rak'];

    protected $dates = ['insert_date', 'modified_date'];

    public $timestamps=true;

    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'modified_date';

    //relasi ke model buku
    public function books()
    {
        return $this->belongsTo(books::class, 'id_buku');
    }

    //relasi ke model rak
    public function racks()
    {
        return $this->belongsTo(racks::class, 'id_rak');
    }

    public function sortbooks()
    {
        // sortbooks model biasanya bernama PenataanBukus atau sortbooks
        return $this->hasMany(sortbooks::class, 'id_buku', 'id_buku');
    }
    public function borrowing()
    {
        return $this->hasMany(Borrowing::class, 'id_item');
    }

}
