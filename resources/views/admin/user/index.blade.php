@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between mb-3">
            <h4>User</h4>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                + Tambah User
            </a>
        </div>

        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Cabang</th>
                    <th width="160">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                    <td>{{ $user->branch->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user) }}"
                           class="btn btn-sm btn-warning">Edit</a>

                        <form method="POST"
                              action="{{ route('users.destroy', $user) }}"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Hapus user?')">
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
