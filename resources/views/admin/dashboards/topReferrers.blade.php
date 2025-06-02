<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th class="w-100">{{ __('Link') }}</th>
            <th>{{ __('Lượt xem') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($topReferrers as $ref)
            <tr>
                <td>
                    <a target="_blank" href="https://{{ $ref['url'] }}" class="text-default font-weight-semibold letter-icon-title">{{ $ref['url'] }}</a>
                </td>
                <td>
                    <span class="text-muted font-size-sm">{{ formatNumber($ref['pageViews']) }}</span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
