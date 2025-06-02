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
    @foreach($data as $post)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $post->title }}</td>
            <td>{{ $post->view }}</td>
            <td>@if($post->status == \App\Enums\PostState::Active) {{ __('Hoạt động') }} @elseif($post->status == \App\Enums\PostState::Pending) {{ __('Chờ phê duyệt') }} @else {{ __('Hủy') }} @endif</td>
            <td>{{ formatDate($post->created_at) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
