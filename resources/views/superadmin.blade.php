    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Superadmin</title>
        <link rel="stylesheet" href="{{ asset('CSS/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('CSS/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('fontawesome-free-6.7.2-web/css/all.css') }}">
        <link rel="stylesheet" href="{{ url('CSS/superadmin.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
        <!-- Add these to your head section if not already present -->
        <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
        <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    </head>

    <body>
        <!-- Header -->
        <nav class="navbar navbar-expand navbar-light fixed-top border-bottom px-3">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('assets/fti-ukdw.png') }}" width="40" height="40" class="me-2"
                        alt="">
                    <img src="{{ asset('assets/logo-ukdw.png') }}" width="40" height="40" alt="">
                </div>
                <div class="settingbtn d-flex gap-2">
                    <a href="{{ route('superadmin') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="bi bi-gear"></i> <span class="d-none d-md-inline">Super Admin</span>
                    </a>
                    <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger rounded-pill">
                        <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline">Logout</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-menu">
                <!-- Dashboard Item -->
                <div class="menu-item">
                    <a href="dashboard" class="menu-link active">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </div>

                <!-- Dokumen Item -->
                <div class="menu-item">
                    <a class="menu-link" data-bs-toggle="collapse" href="#dokumenMenu">
                        <i class="bi bi-files"></i> Dokumen <i class="bi bi-chevron-down float-end"></i>
                    </a>
                    <div class="collapse show submenu" id="dokumenMenu">
                        <div class="submenu-item">
                            <a href="#" class="submenu-link">Input Rekap Kerja Sama</a>
                            <a href="#" class="submenu-link">Data Dokumen Kerja Sama</a>
                            <a href="#" class="submenu-link">Laporan Pelaksaan Kerja Sama</a>
                            <a href="#" class="submenu-link">Form Evaluasi Kepuasan Mitra (Kinerja
                                Mahasiswa/Dosen)</a>
                            <a href="#" class="submenu-link">Form Evaluasi Kepuasan Mitra</a>
                            <a href="#" class="submenu-link">Cetak Laporan SPMI Kerja Sama Baru</a>
                            <a href="#" class="submenu-link">Cetak Laporan SPMI Kerja Sama</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toggle Button -->
        <button class="toggle-btn" id="sidebarToggle">
            <i class="bi bi-list" id="toggleIcon"></i>
        </button>

        <!-- Main Content -->
        <!-- Main Content -->
        <main class="main-content p-3" id="mainContent">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2>User Management</h2>
                    </div>
                </div>
                <div class="row">
                    <!-- Users Table Column -->
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Existing Users</h5>
                                <span class="badge bg-primary">{{ $users->count() }} users</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-outline-primary change-password-btn"
                                                                title="Edit" data-bs-target="#staticBackdrop"
                                                                data-bs-toggle="modal"
                                                                data-user-id="{{ $user->id }}"
                                                                data-user-name="{{ $user->name }}">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger delete-user-btn"
                                                                title="Delete" data-user-id="{{ $user->id }}">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Create User Form Column -->
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Tambah Staff</h5>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <form action="{{ route('superadmin.store_user') }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email"
                                            class="form-control @error('email') is-invalid @enderror" id="email"
                                            name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" required placeholder="Minimal 6 Karakter">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Konfirmasi
                                            Password</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required placeholder="Minimal 6 Karakter">
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-person-plus me-1"></i> Tambah Staff
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- Footer -->
        <footer class="fixed-bottom py-2 text-center text-white">
            <p class="mb-0">&copy; Fakultas Teknologi Informasi.</p>
        </footer>
        <!-- Modal for Edit Password -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="changePasswordForm" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="EditPass">Ganti Password</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="userId" name="user_id">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                    required>
                                <div class="invalid-feedback" id="passwordError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Konfirmasi Password
                                    Baru</label>
                                <input type="password" class="form-control" id="new_password_confirmation"
                                    name="new_password_confirmation" required>
                                <div class="invalid-feedback" id="confirmPasswordError"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Ganti Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Toast Notification Container - Centered -->
        <div class="position-fixed top-50 start-50 translate-middle" style="z-index: 11">
            <div id="successToast" class="toast align-items-center" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body bg-success text-white rounded">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <span id="toastMessage">Password berhasil diubah!</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Delete User Functionality with SweetAlert2
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.closest('tr').querySelector('td:nth-child(2)').textContent;
                    const userRow = this.closest('tr');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: `Anda akan menghapus user ${userName}!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading indicator
                            Swal.fire({
                                title: 'Menghapus...',
                                html: 'Sedang menghapus user',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            fetch(`{{ route('superadmin.delete_user', '') }}/${userId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.close();
                                    if (data.success) {
                                        Swal.fire({
                                            title: 'Terhapus!',
                                            text: data.message,
                                            icon: 'success',
                                            timer: 3000,
                                            timerProgressBar: true,
                                            showConfirmButton: false
                                        });
                                        // Remove the row from table
                                        userRow.remove();
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: data.message,
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                })
                                .catch(error => {
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan saat menghapus user',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                    console.error('Error:', error);
                                });
                        }
                    });
                });
            });
            // Modal Button
            const modalpass = document.getElementById('staticBackdrop');
            modalpass.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');

                const modalTitle = modalpass.querySelector('.modal-title');
                modalTitle.textContent = 'Ganti Password ' + userName;

                document.getElementById('userId').value = userId;
            });

            // Handle form submission
            document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const successToast = new bootstrap.Toast(document.getElementById('successToast'));
                const toastMessage = document.getElementById('toastMessage');

                fetch("{{ route('superadmin.change_password') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update and show success toast
                            toastMessage.textContent = data.message || 'Password berhasil diubah!';
                            document.querySelector('#successToast .toast-body').classList.remove('bg-danger');
                            document.querySelector('#successToast .toast-body').classList.add('bg-success');
                            successToast.show();

                            // Set timer to hide after 3 seconds
                            setTimeout(() => {
                                successToast.hide();
                            }, 2000);

                            // Close the modal
                            var modal = bootstrap.Modal.getInstance(document.getElementById('staticBackdrop'));
                            modal.hide();

                            // Reset the form
                            this.reset();
                        } else {
                            // Show error message in the same toast but with different style
                            const toastBody = document.querySelector('#successToast .toast-body');
                            toastBody.classList.remove('bg-success');
                            toastBody.classList.add('bg-danger');
                            toastMessage.textContent = data.message || 'Password gagal diubah!';
                            successToast.show();

                            // Set timer to hide after 3 seconds
                            setTimeout(() => {
                                successToast.hide();
                            }, 3000);

                            // Reset style after hiding
                            successToast._element.addEventListener('hidden.bs.toast', function() {
                                toastBody.classList.remove('bg-danger');
                                toastBody.classList.add('bg-success');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const toastBody = document.querySelector('#successToast .toast-body');
                        toastBody.classList.remove('bg-success');
                        toastBody.classList.add('bg-danger');
                        toastMessage.textContent = 'Terjadi kesalahan saat mengubah password';
                        successToast.show();

                        // Set timer to hide after 3 seconds
                        setTimeout(() => {
                            successToast.hide();
                        }, 2000);

                        // Reset style after hiding
                        successToast._element.addEventListener('hidden.bs.toast', function() {
                            toastBody.classList.remove('bg-danger');
                            toastBody.classList.add('bg-success');
                        });
                    });
            });
            // Sidebar Toggle
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('sidebar');
                const sidebarToggle = document.getElementById('sidebarToggle');
                const mainContent = document.getElementById('mainContent');
                const toggleIcon = document.getElementById('toggleIcon');

                // Toggle sidebar
                sidebarToggle.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        // Mobile behavior
                        sidebar.classList.toggle('show');
                        sidebarToggle.classList.toggle('show');
                    } else {
                        // Desktop behavior
                        sidebar.classList.toggle('collapsed');
                        sidebarToggle.classList.toggle('collapsed');
                        mainContent.classList.toggle('full-width');

                        // Toggle icon
                        if (sidebar.classList.contains('collapsed')) {
                            toggleIcon.classList.remove('bi-list');
                            toggleIcon.classList.add('bi-chevron-right');
                        } else {
                            toggleIcon.classList.remove('bi-chevron-right');
                            toggleIcon.classList.add('bi-list');
                        }
                    }
                });

                // Auto-close sidebar on mobile when clicking a link
                const navLinks = document.querySelectorAll('.menu-link, .submenu-link');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 992) {
                            sidebar.classList.remove('show');
                            sidebarToggle.classList.remove('show');
                        }
                    });
                });
            });
        </script>
    </body>

    </html>
