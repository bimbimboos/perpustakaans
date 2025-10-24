<?php
// app/Models/Member.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class Members extends Model
{
    use Notifiable;

    protected $table = 'members';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'name',
        'email',
        'password',
        'alamat',
        'no_telp',
        'ktp_number_enc',
        'ktp_hash',
        'ktp_photo_path',
        'photo_path',
        'ktp_verified_at',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'ktp_number_enc', // Jangan expose encrypted data di JSON
    ];

    protected $casts = [
        'ktp_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // TIDAK gunakan accessor otomatis untuk decrypt (security risk!)
    // Gunakan method eksplisit hanya saat diperlukan

    /**
     * Decrypt nomor KTP (gunakan dengan hati-hati, hanya untuk admin)
     */
    public function getDecryptedKtpNumber(): ?string
    {
        if (empty($this->ktp_number_enc)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->ktp_number_enc);
        } catch (\Exception $e) {
            \Log::error('KTP decryption failed', ['member_id' => $this->id_user]);
            return null;
        }
    }

    /**
     * Set encrypted KTP number dan hash
     */
    public function setKtpNumber(string $ktpNumber): void
    {
        $this->ktp_number_enc = Crypt::encryptString($ktpNumber);
        $this->ktp_hash = hash('sha256', $ktpNumber);
    }

    /**
     * Check apakah KTP sudah diverifikasi
     */
    public function isKtpVerified(): bool
    {
        return !is_null($this->ktp_verified_at);
    }

    /**
     * Get masked KTP untuk display (contoh: 3201****1234)
     */
    public function getMaskedKtp(): ?string
    {
        $ktp = $this->getDecryptedKtpNumber();
        if (!$ktp || strlen($ktp) < 8) {
            return '****';
        }

        return substr($ktp, 0, 4) . str_repeat('*', strlen($ktp) - 8) . substr($ktp, -4);
    }

    /**
     * Get URL untuk KTP photo (signed URL)
     */
    public function getKtpPhotoUrl(int $expiresInMinutes = 5): ?string
    {
        if (!$this->ktp_photo_path) {
            return null;
        }

        // Untuk local disk, gunakan controller route
        return route('members.ktp.download', [
            'member' => $this->id_user,
            'expires' => now()->addMinutes($expiresInMinutes)->timestamp,
            'signature' => hash_hmac('sha256', $this->id_user . $this->ktp_photo_path, config('app.key'))
        ]);
    }

    /**
     * Hapus file KTP dari storage
     */
    public function deleteKtpFiles(): void
    {
        if ($this->ktp_photo_path && Storage::disk('private')->exists($this->ktp_photo_path)) {
            Storage::disk('private')->delete($this->ktp_photo_path);
        }

        if ($this->photo_path && Storage::disk('private')->exists($this->photo_path)) {
            Storage::disk('private')->delete($this->photo_path);
        }
    }

    // Relations
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'id_user', 'id_user');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('ktp_verified_at');
    }
    public function getS3KtpPhotoUrl(int $expiresInMinutes = 5): ?string
    {
        if (!$this->ktp_photo_path) {
            return null;
        }

        // Gunakan disk s3_private
        $disk = Storage::disk('s3_private');

        if (!$disk->exists($this->ktp_photo_path)) {
            return null;
        }

        // Generate temporary signed URL (expires dalam 5 menit)
        return $disk->temporaryUrl(
            $this->ktp_photo_path,
            now()->addMinutes($expiresInMinutes),
            [
                'ResponseContentDisposition' => 'attachment; filename="ktp_' . $this->name . '.jpg"',
                'ResponseContentType' => 'image/jpeg',
            ]
        );
    }
}
