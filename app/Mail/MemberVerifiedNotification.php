<?php

namespace App\Mail;

use App\Models\Members;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberVerifiedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $verificationCode;

    public function __construct(Members $member, string $verificationCode)
    {
        $this->member = $member;
        $this->verificationCode = $verificationCode;
    }

    public function build()
    {
        return $this
            ->subject('✅ Akun Perpustakaan Anda Telah Diverifikasi!')
            ->view('emails.member-verified')
            ->with([
                'memberName' => $this->member->name,
                'verificationCode' => $this->verificationCode,
                'loginUrl' => route('login'),
            ]);
    }
}
