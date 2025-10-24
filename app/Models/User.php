<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use mysql_xdevapi\Table;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
protected $table='users';
protected $primaryKey='id_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name','email','password','role','status','no_telp','alamat',
        'ktp_number_enc','ktp_hash','ktp_photo_path','photo_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password','remember_token','ktp_number_enc'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getKtpNumberAttribute()
    {
        if ($this->ktp_number_enc) {
            try {
                return decrypt($this->ktp_number_enc);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function sortbooks()
    {
        return $this->hasMany(sortbooks::class, 'id_penataan');
    }

    public function borrowing()
    {
        return $this->hasMany(peminjaman::class, 'id_user');
    }

}
