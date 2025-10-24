// Bulk Delete Members
document.addEventListener('DOMContentLoaded', function() {

    // Bulk Delete
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const memberCheckboxes = document.querySelectorAll('.member-checkbox');

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const selectedIds = Array.from(memberCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (selectedIds.length === 0) {
                alert('Pilih minimal 1 member untuk dihapus!');
                return;
            }

            if (confirm(`Yakin ingin menghapus ${selectedIds.length} member?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/members/bulk-delete';

                // CSRF Token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
                form.appendChild(csrfInput);

                // IDs as array
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Select All Checkbox
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            memberCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
    }

    // AJAX Edit - Get member data
    const editButtons = document.querySelectorAll('.edit-member-btn');

    editButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            const memberId = this.dataset.memberId;

            try {
                const response = await fetch(`/members/${memberId}/edit-data`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch data');

                const data = await response.json();

                // Populate modal or form dengan data
                document.getElementById('edit-name').value = data.name;
                document.getElementById('edit-email').value = data.email;
                document.getElementById('edit-no_telp').value = data.no_telp;
                document.getElementById('edit-alamat').value = data.alamat;
                document.getElementById('edit-status').value = data.status;

                // Show modal (sesuaikan dengan modal library yang dipakai)
                // $('#editMemberModal').modal('show');

            } catch (error) {
                console.error('Error:', error);
                alert('Gagal memuat data member!');
            }
        });
    });
});

// Helper: Enable/disable bulk delete button based on selection
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('member-checkbox')) {
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        const anyChecked = document.querySelectorAll('.member-checkbox:checked').length > 0;

        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = !anyChecked;
        }
    }
});
