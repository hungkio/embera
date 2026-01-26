<div class="card dashboard-card h-100">
    <div class="card-body">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="fa fa-chart-line me-2 text-primary"></i>
                Top Shop Giao Dịch
            </h6>
        </div>

        {{-- FILTER FORM --}}
        <form id="topShopFilterForm" class="mb-3">
            <div class="top-filter d-flex gap-2">
                <input type="radio" class="btn-check" name="range" id="range_today" value="today" checked>
                <label class="filter-pill" for="range_today">
                    <i class="fa fa-calendar-day"></i>
                    Hôm nay
                </label>

                <input type="radio" class="btn-check" name="range" id="range_week" value="week">
                <label class="filter-pill" for="range_week">
                    <i class="fa fa-calendar-week"></i>
                    Tuần
                </label>

                <input type="radio" class="btn-check" name="range" id="range_month" value="month">
                <label class="filter-pill" for="range_month">
                    <i class="fa fa-calendar-alt"></i>
                    Tháng
                </label>

                <input type="radio" class="btn-check" name="range" id="range_all" value="all">
                <label class="filter-pill" for="range_all">
                    <i class="fa fa-infinity"></i>
                    Tất cả
                </label>
            </div>
        </form>

        {{-- LIST --}}
        <div id="topShopList">
            @for ($i = 1; $i <= 5; $i++)
                <div class="top-shop-item d-flex align-items-center py-2 border-bottom">
                    {{-- STT --}}
                    <div class="top-rank me-3">
                        {{ $i }}
                    </div>

                    {{-- INFO --}}
                    <div class="flex-grow-1">
                        <div class="fw-semibold text-truncate">Shop name</div>
                        <small class="text-muted">— giao dịch</small>
                    </div>

                    {{-- COUNT --}}
                    <span class="badge bg-primary rounded-pill">0</span>
                </div>
            @endfor
        </div>

    </div>
</div>
