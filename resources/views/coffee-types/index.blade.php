@extends('layouts.app')
@section('title', 'Jenis Kopi')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <span>Jenis Kopi</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Jenis Kopi</h3>
            <p class="page-subtitle">Kelola master data jenis kopi.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('coffee-types.create') }}" class="btn btn-accent">
                <i data-lucide="plus"></i> Tambah Jenis Kopi
            </a>
        </div>
    </div>

    <div class="card-modern">
        <div class="card-body-modern">
            <div class="table-responsive">
                <table class="table-modern w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Kopi</th>
                            <th>Kategori</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coffeeTypes as $coffee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $coffee->name }}</td>
                                <td>
                                    <span class="badge-modern {{ $coffee->category === 'robusta' ? 'bg-dark text-white' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($coffee->category) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('coffee-types.edit', $coffee) }}" class="btn btn-table-action text-warning" title="Edit">
                                            <i data-lucide="edit"></i>
                                        </a>
                                        <form action="{{ route('coffee-types.destroy', $coffee) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus jenis kopi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-table-action text-danger" title="Hapus">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state text-center py-5">
                                        <i data-lucide="coffee" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                                        <h5>Belum ada jenis kopi.</h5>
                                        <p class="text-muted">Tambahkan master data jenis kopi.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();
    });
</script>
@endsection
