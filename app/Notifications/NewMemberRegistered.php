<?php

namespace App\Notifications;

use App\Models\Members;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMemberRegistered extends Notification
{
    use Queueable;

    private $member;

    public function __construct(Members $member)
    {
        $this->member = $member;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // HAPUS 'mail' dulu, fokus ke database aja
        return ['database'];
    }

    /**
     * Get the array representation of the notification (untuk database).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'member_id' => $this->member->id_user,
            'member_name' => $this->member->name,
            'member_email' => $this->member->email,
            'message' => "Member baru '{$this->member->name}' telah terdaftar",
            'action_url' => route('members.show', $this->member),
        ];
    }
}
