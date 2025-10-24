<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    protected $table = 'books';
    protected $primaryKey = 'id_buku';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['judul', 'id_penerbit','pengarang','tahun_terbit','id_kategori','isbn', 'id_subkategori', 'barcode','jumlah'];

    public function bookitems()
    {
        return $this->hasMany(Bookitems::class, 'id_buku');
    }

    // Relasi balik ke sortbooks (INI YANG HILANG – tambahin ini!)
    public function sortbooks()
    {
        return $this->hasMany(Sortbooks::class, 'id_buku', 'id_buku');
    }

    // Relasi ke penerbit
    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'id_penerbit');
    }

    // Relasi ke kategori
    public function categories()
    {
        return $this->belongsTo (Categories::class, 'id_kategori');
    }

    // Relasi ke sub kategori
    public function subcategories()
    {
        return $this->belongsTo(subCategories::class, 'id_subkategori');
    }

    // Accessor: Hitung sisa available (total jumlah - sum penataan)
    public function getAvailableAttribute()
    {
        return $this->jumlah - $this->sortbooks()->sum('jumlah');
    }
}
