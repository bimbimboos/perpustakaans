@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1 fw-semibold">Notifikasi</h2>
                        <p class="text-muted mb-0">Semua pemberitahuan sistem</p>
                    </div>
                    @if($notifications->total() > 0)
                        <button onclick="markAllAsRead()" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-check-double me-1"></i> Tandai Semua Dibaca
                        </button>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    @forelse($notifications as $notification)
                        <div class="notification-item {{ $notification->read_at ? '' : 'unread' }} d-flex align-items-start gap-3 p-3 border-bottom">
                            <div class="notification-icon flex-shrink-0">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1 fw-semibold">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</h6>
                                        @if(isset($notification->data['member_name']))
                                            <p class="mb-1 text-muted small">{{ $notification->data['member_name'] }}</p>
                                            <p class="mb-0 text-muted small">{{ $notification->data['member_email'] ?? '' }}</p>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        @if(!$notification->read_at)
                                            <span class="badge bg-primary rounded-pill mb-2">Baru</span>
                                        @endif
                                        <p class="mb-0 text-muted small">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    @if(isset($notification->data['action_url']))
                                        <a href="{{ $notification->data['action_url'] }}"
                                           class="btn btn-sm btn-primary"
                                           onclick="markAsRead('{{ $notification->id }}', event)">
                                            <i class="fas fa-eye me-1"></i> Lihat Detail
                                        </a>
                                    @endif
                                    @if(!$notification->read_at)
                                        <button onclick="markAsRead('{{ $notification->id }}')"
                                                class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-check me-1"></i> Tandai Dibaca
                                        </button>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-bell-slash fa-3x mb-3 d-block"></i>
                            <h5 class="mb-1">Belum Ada Notifikasi</h5>
                            <p class="mb-0">Anda akan menerima pemberitahuan ketika ada aktivitas baru</p>
                        </div>
                    @endforelse
                </div>

                @if($notifications->hasPages())
                    <div class="mt-3">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .notification-item {
                transition: background-color 0.2s;
            }

            .notification-item.unread {
                background-color: rgba(0, 123, 255, 0.05);
                border-left: 3px solid #007bff !important;
            }

            .notification-icon {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #007bff, #0056d8);
                color: white;
                font-size: 20px;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function markAsRead(notificationId, event = null) {
                if (event) event.preventDefault();

                fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page untuk update tampilan
                            if (event && event.currentTarget.href) {
                                window.location.href = event.currentTarget.href;
                            } else {
                                location.reload();
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function markAllAsRead() {
                fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        </script>
    @endpush
@endsection
