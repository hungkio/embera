<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th class="w-100">{{ __('Tiêu đề') }}</th>
            <th>{{ __('Đã xem') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($mostVisitPages as $page)
            <tr>
                <td>
                    <a target="_blank" href="{{ url($page['url']) }}" class="text-default font-weight-semibold letter-icon-title">{{ $page['pageTitle'] }}</a>
                </td>
                <td>
                    <span class="text-muted font-size-sm">{{ formatNumber($page['pageViews']) }}</span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
