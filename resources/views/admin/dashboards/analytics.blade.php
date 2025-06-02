<div class="row">
    <div class="col-lg-7">
        <div class="chart-container">
            <canvas id="area_basic" class="chart has-fixed-height"
                    data-stats="{{ json_encode($stats, JSON_HEX_APOS) }}"></canvas>
        </div>
    </div>

    <div class="col-lg-5">
        <div id="world-map" class="h-100" data-country-stats="{{ json_encode($countryStats, JSON_HEX_APOS) }}"></div>
    </div>
    <div class="col-lg-12 mt-4">
        <div class="row">
            <div class="col-sm-6 col-xl-3">
                <div class="card card-body card-box-analytics">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="font-weight-semibold mb-0">{{ $total['ga:sessions'] }}</h4>
                            <span class="text-uppercase font-size-sm text-muted">{{ __('Phiên') }}</span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <i class="fal fa-history fa-2x text-success-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-body card-box-analytics">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="font-weight-semibold mb-0">{{ $total['ga:users'] }}</h4>
                            <span class="text-uppercase font-size-sm text-muted">{{ __('Người xem') }}</span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <i class="fad fa-users-medical fa-2x text-primary-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-body card-box-analytics">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="font-weight-semibold mb-0">{{ $total['ga:pageviews'] }}</h4>
                            <span class="text-uppercase font-size-sm text-muted">{{ __('Số lượt xem') }}</span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <i class="fad fa-eye fa-2x text-blue-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-body card-box-analytics">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="font-weight-semibold mb-0">{{ round($total['ga:bounceRate'], 2) }}%</h4>
                            <span class="text-uppercase font-size-sm text-muted">{{ __('Tỷ lệ thoát') }}</span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <i class="fal fa-chart-line-down fa-2x text-danger-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-body card-box-analytics">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="font-weight-semibold mb-0">{{ round($total['ga:percentNewSessions'], 2) }}%</h4>
                            <span class="text-uppercase font-size-sm text-muted">{{ __('Phần trăm phiên mới') }}</span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <i class="far fa-newspaper fa-2x text-blue-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-body card-box-analytics">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="font-weight-semibold mb-0">{{ round($total['ga:pageviewsPerVisit'], 2) }}</h4>
                            <span class="text-uppercase font-size-sm text-muted">{{ __('Trang / Phiên') }}</span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <i class="far fa-user-clock fa-2x text-orange-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-body card-box-analytics">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="font-weight-semibold mb-0">{{ gmdate('H:i:s', $total['ga:avgSessionDuration']) }}</h4>
                            <span class="text-uppercase font-size-sm text-muted">{{ __('Thời gian trung bình xem') }}</span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <i class="fal fa-business-time fa-2x text-primary-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-body card-box-analytics">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="font-weight-semibold mb-0">{{ $total['ga:newUsers'] }}</h4>
                            <span class="text-uppercase font-size-sm text-muted">{{ __('Người xem mới') }}</span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <i class="far fa-users-medical fa-2x text-primary-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    var stats = $('#area_basic').data('stats')

    let xAxis = [];
    let statPageview = [];
    let statVisitor = [];

    $.each(stats, (index, el) => {
        xAxis.push(el.axis);
        statPageview.push(el.pageViews);
        statVisitor.push(el.visitors);
    });
    let max_value = (Math.max(...Object.values(statPageview)) - Math.max(...Object.values(statVisitor))) > 0 ? Math.max(...Object.values(statPageview)) : Math.max(...Object.values(statVisitor))
    let dataset = [
        {
            label: 'Người xem',
            data: statVisitor,
            backgroundColor: 'rgb(46,199,201)',
            borderColor: 'rgb(46,199,201)',
            borderWidth: 2
        },
        {
            label: 'Số lượt xem',
            data: statPageview,
            backgroundColor: 'rgb(187,168,224)',
            borderColor: 'rgb(187,168,224)',
            borderWidth: 2
        }
    ];

    var ctx = document.getElementById('area_basic').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: xAxis,
            datasets: dataset
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    suggestedMin: 0,
                    suggestedMax: max_value
                }
            },
            interaction: {
                mode: 'index',
                intersect: false,
            },
        },
    });

    $(function () {
        let mapSelector = $('#world-map');
        let country_stats = mapSelector.data('country-stats');
        let visitorsData = {};

        $.each(country_stats, (index, el) => {
            visitorsData[el[0]] = el[1];
        });

        mapSelector.vectorMap({
            map: 'world_mill',
            backgroundColor: 'transparent',
            regionStyle: {
                initial: {
                    fill: '#e4e4e4',
                    'fill-opacity': 1,
                    stroke: 'none',
                    'stroke-width': 0,
                    'stroke-opacity': 1
                }
            },
            series: {
                regions: [{
                    values: visitorsData,
                    scale: ['#45a5f5', '#2f5ec4'],
                    normalizeFunction: 'polynomial'
                }]
            },
            onRegionTipShow: (e, el, code) => {
                if (typeof visitorsData[code] !== 'undefined') {
                    el.html(el.html() + ': ' + visitorsData[code] + ' ' + 'Người xem');
                }
            }
        });
    })


</script>
