<table>
    <thead>
        <tr>
            <th>Thứ tự</th>
            <th>Tiêu đề</th>
            <th>Lượt xem</th>
            <th>Trạng thái</th>
            <th>Thời gian tạo</th>
        </tr>
    </thead>
    <tbody>
@foreach($data as $page)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $page->title }}</td>
        <td>{{ $page->view }}</td>
        <td>@if($page->status == \App\Enums\PageState::Active) {{ __('Hoạt động') }} @elseif($page->status == \App\Enums\PageState::Pending) {{ __('Chờ phê duyệt') }} @else {{ __('Hủy') }} @endif</td>
        <td>{{ formatDate($page->created_at) }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
