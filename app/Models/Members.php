<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Members extends Model
{
    use SoftDeletes;

    protected $table = 'members';
    protected $primaryKey = 'id_member'; // ← PENTING: Ganti dari id_user
    public $incrementing = true;

    protected $fillable = [
        'id_user',
        'name',
        'tempat_lahir',
        'tanggal_lahir',
        'email',
        'alamat',
        'no_telp',
        'no_hp_ortu',
        'agama',
        'institusi',
        'alamat_institusi',
        'jenjang_pendidikan',
        'ktp_number_enc',
        'ktp_hash',
        'ktp_photo_path',
        'photo_path',
        'ktp_verified_at',
        'role',
        'status',
        'verification_code',
        'tahun_pembuatan',
        'admin_verified_at',
        'verified_by',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'ktp_verified_at' => 'datetime',
        'admin_verified_at' => 'datetime',
    ];

    // Relasi ke User yang verifikasi
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by', 'id_user');
    }

    // Relasi ke User pemilik
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Generate kode verifikasi 6 digit
    public static function generateVerificationCode()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    // Enkripsi nomor KTP
    public function setKtpNumber($ktpNumber)
    {
        $this->ktp_number_enc = Crypt::encryptString($ktpNumber);
        $this->ktp_hash = hash('sha256', $ktpNumber);
    }

    // Dekripsi nomor KTP
    public function getKtpNumber()
    {
        return $this->ktp_number_enc ? Crypt::decryptString($this->ktp_number_enc) : null;
    }
    public function borrowing()
    {
        return $this->hasMany(\App\Models\Borrowing::class, 'id_member', 'id_member');
    }

}
