<table>
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
