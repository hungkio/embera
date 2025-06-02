<!DOCTYPE html>
<html lang="en">
<head>
    <title>In Danh sách Trang</title>
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
        <th>Thời gian tạo</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $menu)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $menu->name }}</td>
            <td>@if($menu->status == \App\Domain\Menu\Models\Menu::STATUS_SHOW) {{ __('Hoạt động') }} @else {{ __('Ẩn') }} @endif</td>
            <td>{{ formatDate($menu->created_at) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
