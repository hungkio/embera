@extends('admin.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Import Pins</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.pins.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file">Chọn file Excel</label>
                    <input type="file" name="file" id="file" class="form-control" accept=".xls,.xlsx" required>
                    <small class="form-text text-muted">File phải có hai cột: imei và serial_number với tiêu đề hàng đầu tiên.</small>
                </div>
                <button type="submit" class="btn btn-primary">Import</button>
                <a href="{{ route('admin.pins.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection
