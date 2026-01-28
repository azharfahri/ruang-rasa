@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-body">

        <h4 class="mb-4">Inventory Cabang</h4>

        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="60">No</th>
                    <th>Produk</th>
                    <th width="120">Stok</th>
                    <th width="120">Status</th>
                    <th width="200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->stock }}</td>
                        <td>
                            <span class="badge bg-{{ $item->isAvailable() ? 'success' : 'danger' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('cashier.penyimpanan.stock', $item) }}" method="POST" class="d-flex gap-2" class="confirm-submit" data-type="update">
                                @csrf
                                @method('PUT')

                                <input type="number"
                                    name="stock"
                                    class="form-control"
                                    min="0"
                                    value="{{ $item->stock }}"
                                    required>

                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Inventory kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>
@endsection
