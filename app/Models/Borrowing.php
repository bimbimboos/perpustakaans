<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Borrowing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'borrowing';
    protected $primaryKey = 'id_peminjaman';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_member',
        'id_buku',
        'id_item',
        'pinjam',
        'pengembalian',
        'status',
        'kondisi',
        'alamat_peminjam',
        'request_status',
        'approved_by',
        'approved_at',
        'is_extended',
        'catatan',
        'denda',  // ✅ TAMBAHAN BARU untuk denda
    ];

    // ✅ CASTING OTOMATIS
    protected $casts = [
        'is_extended' => 'boolean',
        'pinjam' => 'datetime',
        'pengembalian' => 'datetime',
        'approved_at' => 'datetime',
        'deleted_at' => 'datetime',
        'denda' => 'decimal:2',  // ✅ Cast denda as decimal
    ];

    // ✅ SOFT DELETE
    protected $dates = ['deleted_at'];

    // ========================================
    // RELASI
    // ========================================

    /**
     * Relasi ke User (Peminjam)
     */
    public function users()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Buku
     */
    public function books()
    {
        return $this->belongsTo(Books::class, 'id_buku', 'id_buku');
    }

    /**
     * Relasi ke Item Buku
     */
    public function bookitems()
    {
        return $this->belongsTo(Bookitems::class, 'id_item', 'id_item');
    }

    /**
     * Relasi ke Member
     */
    public function member()
    {
        return $this->belongsTo(Members::class, 'id_member', 'id_member');
    }

    // ========================================
    // ACCESSOR & HELPER METHODS
    // ========================================

    /**
     * Cek apakah peminjaman terlambat
     */
    public function isLate()
    {
        return Carbon::now()->gt($this->pengembalian) &&
            in_array($this->status, ['Dipinjam', 'dipinjam']);
    }

    /**
     * Hitung berapa hari terlambat
     */
    public function getDaysLate()
    {
        if (!$this->isLate()) {
            return 0;
        }

        return Carbon::now()->diffInDays($this->pengembalian);
    }

    /**
     * Get formatted tanggal peminjaman
     */
    public function getFormattedPinjamAttribute()
    {
        return Carbon::parse($this->pinjam)->format('d M Y');
    }

    /**
     * Get formatted tanggal pengembalian
     */
    public function getFormattedPengembalianAttribute()
    {
        return Carbon::parse($this->pengembalian)->format('d M Y');
    }

    /**
     * Get formatted denda
     */
    public function getFormattedDendaAttribute()
    {
        if (!$this->denda || $this->denda == 0) {
            return 'Rp 0';
        }
        return 'Rp ' . number_format($this->denda, 0, ',', '.');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope untuk peminjaman aktif
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Dipinjam', 'dipinjam']);
    }

    /**
     * Scope untuk peminjaman sudah dikembalikan
     */
    public function scopeReturned($query)
    {
        return $query->whereIn('status', ['Dikembalikan', 'dikembalikan']);
    }

    /**
     * Scope untuk peminjaman terlambat
     */
    public function scopeLate($query)
    {
        return $query->active()
            ->where('pengembalian', '<', Carbon::now());
    }

    /**
     * Scope untuk peminjaman user tertentu
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('id_user', $userId);
    }

    /**
     * Scope untuk peminjaman dengan denda
     */
    public function scopeWithFine($query)
    {
        return $query->where('denda', '>', 0);
    }
}
