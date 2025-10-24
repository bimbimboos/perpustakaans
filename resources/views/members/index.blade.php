@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header with Actions --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1 fw-semibold">Member Perpustakaan</h2>
                <p class="text-muted mb-0">Kelola data anggota perpustakaan</p>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Command Bar (Microsoft 365 Style) --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- New Button --}}
                    <button class="btn btn-primary btn-command" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="fas fa-plus me-2"></i>Baru
                    </button>

                    {{-- View Button (Detail) --}}
                    <button class="btn btn-outline-secondary btn-command" id="btnView" disabled onclick="viewSelected()">
                        <i class="fas fa-eye me-2"></i>Lihat
                    </button>

                    {{-- Edit Button --}}
                    <button class="btn btn-outline-secondary btn-command" id="btnEdit" disabled onclick="editSelected()">
                        <i class="fas fa-edit me-2"></i>Edit
                    </button>

                    <div class="vr"></div>

                    {{-- Delete Button --}}
                    <button class="btn btn-outline-danger btn-command" id="btnDelete" disabled onclick="deleteSelected()">
                        <i class="fas fa-trash me-2"></i>Hapus
                    </button>

                    <div class="ms-auto d-flex gap-2 align-items-center">
                    <span id="selectedCount" class="text-muted small" style="display: none;">
                        <strong>0</strong> dipilih
                    </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('members.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                                <input type="text"
                                       name="search"
                                       class="form-control border-start-0 ps-0"
                                       placeholder="Cari nama, email, atau telepon..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="verified" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua Verifikasi</option>
                                <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Terverifikasi</option>
                                <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Belum Verifikasi</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-dark w-100">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Members Table --}}
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="membersTable">
                    <thead class="bg-light">
                    <tr>
                        <th style="width: 40px;" class="border-0">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th class="border-0">ID</th>
                        <th class="border-0">Member</th>
                        <th class="border-0">Kontak</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">KTP</th>
                        <th class="border-0">Terdaftar</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($members as $member)
                        <tr class="member-row" data-id="{{ $member->id_user }}">
                            <td>
                                <input type="checkbox" class="form-check-input member-checkbox" value="{{ $member->id_user }}">
                            </td>
                            <td class="text-muted">#{{ $member->id_user }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $member->name }}</div>
                                        <small class="text-muted">{{ $member->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $member->no_telp ?? '-' }}</td>
                            <td>
                                @if($member->status === 'active')
                                    <span class="badge rounded-pill bg-success-subtle text-success border-0">
                                        <i class="fas fa-circle" style="font-size: 6px;"></i> Active
                                    </span>
                                @elseif($member->status === 'inactive')
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border-0">
                                        <i class="fas fa-circle" style="font-size: 6px;"></i> Inactive
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border-0">
                                        <i class="fas fa-circle" style="font-size: 6px;"></i> Suspended
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($member->ktp_verified_at)
                                    <span class="badge rounded-pill bg-primary-subtle text-primary border-0">
                                        <i class="fas fa-check-circle"></i> Verified
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-warning-subtle text-warning border-0">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $member->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                    <p class="mb-0">Belum ada data member</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($members->hasPages())
                <div class="card-footer bg-white border-top">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus me-2"></i>Tambah Member Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No. Telepon</label>
                                <input type="text" name="no_telp" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Nomor KTP <span class="text-danger">*</span></label>
                                <input type="text" name="ktp_number" class="form-control" required maxlength="16">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Upload KTP</label>
                                <input type="file" name="ktp_photo" class="form-control" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Foto Profil</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-dark border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i>Edit Member
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4" id="editModalBody">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Bulk Delete Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus <strong id="deleteCount">0</strong> member yang dipilih?</p>
                    <p class="text-muted small mb-0 mt-2">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <form id="bulkDeleteForm" method="POST" action="{{ route('members.bulk-delete') }}" style="display: inline;">
                        @csrf
                        <div id="deleteIdsContainer"></div>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .avatar-circle {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: linear-gradient(135deg, #0078d4 0%, #106ebe 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 16px;
            }

            .btn-command {
                font-size: 14px;
                padding: 6px 16px;
                font-weight: 500;
                border-radius: 4px;
            }

            .member-row {
                cursor: pointer;
                transition: background-color 0.15s ease;
            }

            .member-row:hover {
                background-color: #f8f9fa !important;
            }

            .member-row.selected {
                background-color: #e7f3ff !important;
            }

            .vr {
                width: 1px;
                background-color: #dee2e6;
                opacity: 1;
            }

            .bg-success-subtle { background-color: #d1e7dd !important; }
            .bg-danger-subtle { background-color: #f8d7da !important; }
            .bg-warning-subtle { background-color: #fff3cd !important; }
            .bg-primary-subtle { background-color: #cfe2ff !important; }
            .bg-secondary-subtle { background-color: #e2e3e5 !important; }
        </style>
    @endpush

    @push('scripts')
        <script>
            let selectedIds = new Set();

            // Select All Checkbox
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.member-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                    toggleRowSelection(cb.closest('tr'), this.checked);
                    if (this.checked) {
                        selectedIds.add(parseInt(cb.value));
                    } else {
                        selectedIds.delete(parseInt(cb.value));
                    }
                });
                updateCommandBar();
            });

            // Individual Checkbox
            document.querySelectorAll('.member-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const id = parseInt(this.value);
                    const row = this.closest('tr');

                    if (this.checked) {
                        selectedIds.add(id);
                        toggleRowSelection(row, true);
                    } else {
                        selectedIds.delete(id);
                        toggleRowSelection(row, false);
                        document.getElementById('selectAll').checked = false;
                    }
                    updateCommandBar();
                });
            });

            // Row Click (select)
            document.querySelectorAll('.member-row').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.type !== 'checkbox') {
                        const checkbox = this.querySelector('.member-checkbox');
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change'));
                    }
                });
            });

            function toggleRowSelection(row, selected) {
                if (selected) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
            }

            function updateCommandBar() {
                const count = selectedIds.size;
                const countDisplay = document.getElementById('selectedCount');
                const btnView = document.getElementById('btnView');
                const btnEdit = document.getElementById('btnEdit');
                const btnDelete = document.getElementById('btnDelete');

                if (count > 0) {
                    countDisplay.style.display = 'inline';
                    countDisplay.querySelector('strong').textContent = count;
                    btnDelete.disabled = false;

                    // View dan Edit hanya untuk 1 item
                    if (count === 1) {
                        btnView.disabled = false;
                        btnEdit.disabled = false;
                    } else {
                        btnView.disabled = true;
                        btnEdit.disabled = true;
                    }
                } else {
                    countDisplay.style.display = 'none';
                    btnView.disabled = true;
                    btnEdit.disabled = true;
                    btnDelete.disabled = true;
                }
            }

            function viewSelected() {
                const id = Array.from(selectedIds)[0];
                window.location.href = `/members/${id}`;
            }

            function editSelected() {
                const id = Array.from(selectedIds)[0];
                const modal = new bootstrap.Modal(document.getElementById('editModal'));

                // Fetch member data via AJAX endpoint
                fetch(`/members/${id}/edit-data`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to fetch');
                        return response.json();
                    })
                    .then(data => {
                        const form = document.getElementById('editForm');
                        form.action = `/members/${id}`;

                        document.getElementById('editModalBody').innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama</label>
                            <input type="text" name="name" class="form-control" value="${data.name || ''}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="${data.email || ''}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control" value="${data.no_telp || ''}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2">${data.alamat || ''}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" ${data.status === 'active' ? 'selected' : ''}>Active</option>
                                <option value="inactive" ${data.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                <option value="suspended" ${data.status === 'suspended' ? 'selected' : ''}>Suspended</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Foto Profil Baru</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                    </div>
                `;

                        modal.show();
                    })
                    .catch(error => {
                        alert('Gagal memuat data member');
                        console.error(error);
                    });
            }

            function deleteSelected() {
                const count = selectedIds.size;
                const idsArray = Array.from(selectedIds);

                document.getElementById('deleteCount').textContent = count;

                // Generate hidden inputs for each ID (Laravel expects ids[])
                const container = document.getElementById('deleteIdsContainer');
                container.innerHTML = '';

                idsArray.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    container.appendChild(input);
                });

                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            }
        </script>
    @endpush
@endsection
