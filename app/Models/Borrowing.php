<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    use HasFactory;

    protected $table = 'borrowing';
    protected $primaryKey = 'id_peminjaman';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_buku',
        'id_item',
        'pinjam',
        'pengembalian',
        'status',
        'kondisi',
        'alamat'
    ];

    // relasi ke user
    public function users()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // relasi ke buku
    public function books()
    {
        return $this->belongsTo(Books::class, 'id_buku');
    }

    // relasi ke buku_item
    public function bookitems()
    {
        return $this->belongsTo(Bookitems::class, 'id_item');
    }
}
