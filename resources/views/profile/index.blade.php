@extends('layouts.app')

@section('title', 'Profil Saya')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <span>Profil saya</span>
    </div>
@endsection

@section('content')
<div class="page-content">
    <div class="page-header mb-4">
        <div>
            <h3 class="page-title mb-1">Profil saya</h3>
            <p class="page-subtitle text-muted mb-0">Kelola informasi akun dan kata sandi administratif Anda.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: var(--radius-sm, 8px);">
            <i data-lucide="check-circle" style="width: 18px; height: 18px; flex-shrink: 0;"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: var(--radius-sm, 8px);">
            <div class="fw-bold d-flex align-items-center gap-2 mb-1">
                <i data-lucide="alert-circle" style="width: 18px; height: 18px; flex-shrink: 0;"></i>
                <span>Gagal menyimpan data:</span>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column: Profile Card -->
        <div class="col-lg-4">
            <div class="card card-modern">
                <div class="card-body card-body-modern text-center py-4">
                    <div class="mb-3 position-relative d-inline-block">
                        <img src="{{ auth()->user()->avatar ? (str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset(auth()->user()->avatar)) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'Admin') . '&background=1E3A5F&color=fff' }}" alt="Profile Avatar" class="rounded-circle border border-2 border-white" style="width: 96px; height: 96px; object-fit: cover;">
                        <button class="btn btn-sm btn-accent rounded-circle position-absolute bottom-0 end-0" style="width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;" onclick="document.getElementById('avatarUpload').click()" title="Ubah foto profil">
                            <i data-lucide="camera" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>
                    <h5 class="card-title-modern mb-1">{{ auth()->user()->name ?? 'Administrator' }}</h5>
                    <p class="text-muted small mb-3">{{ auth()->user()->email ?? 'admin@coffee.com' }}</p>
                    <span class="badge bg-light text-primary border px-2 py-1" style="font-size: 0.75rem;">
                        {{ auth()->user()->role ?? 'Super Admin' }}
                    </span>
                    
                    <ul class="list-group list-group-flush text-start mt-3">
                        <li class="list-group-item bg-transparent px-0 border-bottom-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small"><i data-lucide="calendar" class="me-1" style="width:14px;height:14px"></i> Bergabung</span>
                            <span class="fw-semibold small">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d M Y') : '1 Jan 2023' }}</span>
                        </li>
                        <li class="list-group-item bg-transparent px-0 border-bottom-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small"><i data-lucide="check-circle" class="me-1" style="width:14px;height:14px"></i> Status</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.72rem;">Aktif</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Column: Forms -->
        <div class="col-lg-8">
            <!-- Profile Info Form -->
            <div class="card card-modern mb-4">
                <div class="card-header card-header-modern">
                    <h5 class="card-title-modern m-0">Informasi profil</h5>
                </div>
                <div class="card-body card-body-modern">
                    <form action="{{ url('profile') }}" method="POST" enctype="multipart/form-data" class="form-modern">
                        @csrf
                        @method('PUT')
                        <input type="file" id="avatarUpload" name="avatar" class="d-none" accept="image/*">
                        
                        <div class="row g-3">
                            <div class="col-md-6 form-group-modern">
                                <label class="form-label form-label-modern">Nama lengkap</label>
                                <input type="text" name="name" class="form-control form-control-modern @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 form-group-modern">
                                <label class="form-label form-label-modern">Alamat email</label>
                                <input type="email" name="email" class="form-control form-control-modern @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12 form-group-modern">
                                <label class="form-label form-label-modern">Nomor telepon</label>
                                <input type="text" name="phone" class="form-control form-control-modern" value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="08xxxxxxxxxx">
                            </div>

                            <div class="col-12 mt-3 text-end">
                                <button type="submit" class="btn btn-accent d-inline-flex align-items-center gap-1.5">
                                    <i data-lucide="check" style="width:15px;height:15px;"></i>
                                    <span>Simpan perubahan</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Form -->
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <h5 class="card-title-modern m-0">Ubah kata sandi</h5>
                </div>
                <div class="card-body card-body-modern">
                    <form action="{{ route('profile.password') }}" method="POST" class="form-modern">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-12 form-group-modern">
                                <label class="form-label form-label-modern fw-semibold">Kata sandi saat ini <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="current_password" id="inputCurrentPassword" class="form-control form-control-modern @error('current_password') is-invalid @enderror" placeholder="Masukkan kata sandi saat ini" required>
                                    <button class="btn btn-outline-secondary btn-sm px-3" type="button" onclick="togglePasswordVisibility('inputCurrentPassword', this)" title="Lihat kata sandi">
                                        <i data-lucide="eye" style="width:15px;height:15px;"></i>
                                    </button>
                                </div>
                                @error('current_password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 form-group-modern">
                                <label class="form-label form-label-modern fw-semibold">Kata sandi baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="inputNewPassword" class="form-control form-control-modern @error('password') is-invalid @enderror @error('new_password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                                    <button class="btn btn-outline-secondary btn-sm px-3" type="button" onclick="togglePasswordVisibility('inputNewPassword', this)" title="Lihat kata sandi">
                                        <i data-lucide="eye" style="width:15px;height:15px;"></i>
                                    </button>
                                </div>
                                <small class="text-muted" style="font-size:0.75rem;">Gunakan minimal 6 karakter kombinasi huruf & angka.</small>
                                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                @error('new_password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 form-group-modern">
                                <label class="form-label form-label-modern fw-semibold">Konfirmasi kata sandi baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="inputConfirmPassword" class="form-control form-control-modern" placeholder="Ketik ulang kata sandi baru" required>
                                    <button class="btn btn-outline-secondary btn-sm px-3" type="button" onclick="togglePasswordVisibility('inputConfirmPassword', this)" title="Lihat kata sandi">
                                        <i data-lucide="eye" style="width:15px;height:15px;"></i>
                                    </button>
                                </div>
                                <small class="text-muted" style="font-size:0.75rem;">Pastikan sama persis dengan kata sandi baru.</small>
                            </div>

                            <div class="col-12 mt-3 text-end">
                                <button type="reset" class="btn btn-outline-modern me-2">Batal</button>
                                <button type="submit" class="btn btn-accent d-inline-flex align-items-center gap-1.5">
                                    <i data-lucide="check" style="width:15px;height:15px;"></i>
                                    <span>Simpan kata sandi baru</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // File upload preview
    const avatarInput = document.getElementById('avatarUpload');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            if(e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarImg = document.querySelector('.rounded-circle');
                    if (avatarImg) avatarImg.src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }
});

function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i data-lucide="eye-off" style="width:15px;height:15px;"></i>';
    } else {
        input.type = 'password';
        btn.innerHTML = '<i data-lucide="eye" style="width:15px;height:15px;"></i>';
    }
    lucide.createIcons();
}
</script>
@endsection
