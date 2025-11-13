<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MemberVerificationController extends Controller
{
    /**
     * 1️⃣ Tampilkan halaman verifikasi member
     */
    public function index()
    {
        $members = Member::where('status', 'pending')
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('admin.member-verification.index', compact('members'));
    }

    /**
     * 2️⃣ Lihat detail member
     */
    public function show($id)
    {
        $member = Member::with('user')->findOrFail($id);
        return view('admin.member-verification.show', compact('member'));
    }

    /**
     * 3️⃣ APPROVE - Verifikasi member
     */
    public function approve($id)
    {
        $member = Member::findOrFail($id);
        $verificationCode = strtoupper(Str::random(6));

        $member->update([
            'status' => 'verified',
            'verification_code' => $verificationCode,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Member berhasil diverifikasi!');
    }

    /**
     * 4️⃣ REJECT - Tolak pendaftaran member
     */
    public function reject($id, Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $member = Member::findOrFail($id);
        $member->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Member ditolak dengan alasan: ' . $request->rejection_reason);
    }

    /**
     * 5️⃣ SUSPEND - Suspend member
     */
    public function suspend($id, Request $request)
    {
        $request->validate([
            'suspend_reason' => 'nullable|string|max:500'
        ]);

        $member = Member::findOrFail($id);
        $member->update([
            'status' => 'suspended',
            'suspend_reason' => $request->suspend_reason,
            'suspended_at' => now(),
            'suspended_by' => Auth::id(),
        ]);

        return redirect()->back()->with('warning', 'Member berhasil disuspend.');
    }

    /**
     * 6️⃣ ACTIVATE - Aktifkan kembali member
     */
    public function activate($id)
    {
        $member = Member::findOrFail($id);
        $member->update([
            'status' => 'active',
            'suspend_reason' => null,
            'suspended_at' => null,
        ]);

        return redirect()->back()->with('success', 'Member berhasil diaktifkan kembali!');
    }

    /**
     * 7️⃣ BULK APPROVE
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:members,id'
        ]);

        foreach ($request->member_ids as $memberId) {
            $member = Member::find($memberId);

            if ($member && $member->status === 'pending') {
                $member->update([
                    'status' => 'verified',
                    'verification_code' => strtoupper(Str::random(6)),
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->back()->with('success', count($request->member_ids) . ' member berhasil diverifikasi!');
    }

    /**
     * 8️⃣ CHECK STATUS - Untuk user
     */
    public function checkStatus()
    {
        $member = Member::where('user_id', Auth::id())->first();

        if (!$member) {
            return response()->json([
                'status' => 'not_registered',
                'message' => 'Anda belum terdaftar sebagai member.'
            ]);
        }

        return response()->json([
            'status' => $member->status,
            'verification_code' => $member->verification_code,
            'message' => $this->getStatusMessage($member->status)
        ]);
    }

    private function getStatusMessage($status)
    {
        return match($status) {
            'pending' => 'Menunggu verifikasi admin.',
            'verified', 'active' => 'Member aktif dan terverifikasi.',
            'rejected' => 'Pendaftaran ditolak.',
            'suspended' => 'Akun disuspend.',
            'inactive' => 'Akun tidak aktif.',
            default => 'Status tidak diketahui.'
        };
    }

    /**
     * 9️⃣ STATISTICS - Statistik dashboard
     */
    public function statistics()
    {
        $stats = [
            'pending' => Member::where('status', 'pending')->count(),
            'verified' => Member::where('status', 'verified')->count(),
            'active' => Member::where('status', 'active')->count(),
            'rejected' => Member::where('status', 'rejected')->count(),
            'suspended' => Member::where('status', 'suspended')->count(),
            'total' => Member::count(),
        ];

        return response()->json($stats);
    }
}
