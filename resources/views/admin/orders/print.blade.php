<!DOCTYPE html>
<html lang="en">
<head>
    <title>In Danh sách Banner</title>
    <meta charset="UTF-8">
    <meta name=description content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <style>
        body {margin: 20px}
    </style>
</head>
<body>
<table class="table table-bordered table-condensed table-striped">
    <thead>
    <tr>
        <th>Thứ tự</th>
        <th>Tiêu đề</th>
        <th>Trạng thái</th>
        <th>Đường dẫn</th>
        <th>Phần</th>
        <th>Vị trí</th>
        <th>Thời gian tạo</th>
    </tr>
    </thead>
    <tbody>
        @foreach($data as $banner)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $banner->title }}</td>
                <td>@if($banner->status == \App\Enums\BannerState::Active) {{ __('Hoạt động') }} @elseif($banner->status == \App\Enums\BannerState::Pending) {{ __('Chờ phê duyệt') }} @else {{ __('Hủy') }} @endif</td>
                <td>{{ $banner->link }}</td>
                <td>{{ $banner->section }}</td>
                <td>{{ $banner->position }}</td>
                <td>{{ formatDate($banner->created_at) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
