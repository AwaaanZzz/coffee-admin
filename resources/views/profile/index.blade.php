@extends('layouts.app')

@section('title', 'Profil Saya')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Home</a>
    <i data-lucide="chevron-right"></i>
    <span>Profil Saya</span>
@endsection

@section('content')
<div class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Profil Saya</h1>
            <p class="page-subtitle">Kelola informasi akun dan kata sandi Anda</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column: Profile Card -->
        <div class="col-lg-4">
            <div class="card card-modern">
                <div class="card-body card-body-modern text-center py-5">
                    <div class="mb-4 position-relative d-inline-block">
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'Admin') . '&background=random' }}" alt="Profile Avatar" class="rounded-circle border border-3 border-white shadow" style="width: 120px; height: 120px; object-fit: cover;">
                        <button class="btn btn-sm btn-accent rounded-circle position-absolute bottom-0 end-0 shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;" onclick="document.getElementById('avatarUpload').click()">
                            <i data-lucide="camera" style="width: 16px; height: 16px;"></i>
                        </button>
                    </div>
                    <h4 class="card-title-modern mb-1">{{ auth()->user()->name ?? 'Administrator' }}</h4>
                    <p class="text-muted mb-3">{{ auth()->user()->email ?? 'admin@coffee.com' }}</p>
                    <div class="badge badge-modern bg-primary-subtle text-primary mb-4 px-3 py-2">
                        {{ auth()->user()->role ?? 'Super Admin' }}
                    </div>
                    
                    <ul class="list-group list-group-flush text-start mt-4">
                        <li class="list-group-item bg-transparent px-0 border-bottom-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i data-lucide="calendar" class="me-2" style="width:16px;height:16px"></i> Bergabung</span>
                            <span class="fw-medium">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d M Y') : '1 Jan 2023' }}</span>
                        </li>
                        <li class="list-group-item bg-transparent px-0 border-bottom-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i data-lucide="check-circle" class="me-2" style="width:16px;height:16px"></i> Status</span>
                            <span class="fw-medium text-success">Aktif</span>
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
                    <h5 class="card-title-modern m-0">Informasi Profil</h5>
                </div>
                <div class="card-body card-body-modern">
                    <form action="{{ url('profile') }}" method="POST" enctype="multipart/form-data" class="form-modern">
                        @csrf
                        @method('PUT')
                        <input type="file" id="avatarUpload" name="avatar" class="d-none" accept="image/*">
                        
                        <div class="row g-3">
                            <div class="col-md-6 form-group-modern">
                                <label class="form-label form-label-modern">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control form-control-modern @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 form-group-modern">
                                <label class="form-label form-label-modern">Email</label>
                                <input type="email" name="email" class="form-control form-control-modern @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12 form-group-modern">
                                <label class="form-label form-label-modern">Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control form-control-modern" value="{{ old('phone', auth()->user()->phone ?? '') }}">
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-accent">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Form -->
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <h5 class="card-title-modern m-0">Ubah Password</h5>
                </div>
                <div class="card-body card-body-modern">
                    <form action="{{ url('profile/password') }}" method="POST" class="form-modern">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-12 form-group-modern">
                                <label class="form-label form-label-modern">Password Saat Ini</label>
                                <input type="password" name="current_password" class="form-control form-control-modern @error('current_password') is-invalid @enderror" required>
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 form-group-modern">
                                <label class="form-label form-label-modern">Password Baru</label>
                                <input type="password" name="password" class="form-control form-control-modern @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 form-group-modern">
                                <label class="form-label form-label-modern">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-modern" required>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-outline-modern me-2" type="reset">Batal</button>
                                <button type="submit" class="btn btn-accent">Update Password</button>
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
    document.getElementById('avatarUpload').addEventListener('change', function(e) {
        if(e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.rounded-circle').src = e.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
});
</script>
@endsection
