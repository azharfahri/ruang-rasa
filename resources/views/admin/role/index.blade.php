@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Role</h4>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">+ Tambah</a>
        </div>

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th width="60">No</th>
                    <th>Nama</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($roles as $role)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        <a href="{{ route('roles.edit',$role) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('roles.destroy',$role) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Hapus role?')" class="btn btn-sm btn-danger">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection
