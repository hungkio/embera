<table>
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
