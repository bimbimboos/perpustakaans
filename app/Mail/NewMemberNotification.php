<?php

namespace App\Mail;

use App\Models\Members;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewMemberNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $verificationUrl;

    public function __construct(Members $member)
    {
        $this->member = $member;

        // ✅ PERBAIKAN: Ganti route name yang benar
        $this->verificationUrl = route('members.verify', [
            'id_member' => $member->id_member,  // ✅ Ganti 'id' jadi 'id_member'
            'token' => sha1($member->email . $member->created_at)
        ]);
    }

    public function build()
    {
        return $this
            ->subject('🔔 Pendaftar Member Baru - ' . $this->member->name)
            ->view('emails.new-member-admin')
            ->with([
                'memberName' => $this->member->name,
                'memberEmail' => $this->member->email,
                'memberPhone' => $this->member->no_telp,
                'memberAddress' => $this->member->alamat,
                'registeredAt' => $this->member->created_at->format('d F Y H:i'),
                'verifyUrl' => $this->verificationUrl,
            ]);
    }
}
