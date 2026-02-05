@extends('admin.layouts.master')
@section('title', __('Bản đồ trạm sạc'))

@section('page-content')

    <div class="row mt-4">
        {{-- MAP --}}
        <div class="col-md-8">
            <div class="card dashboard-card map-card">
                <div class="card-body position-relative">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0 text-start" style="font-size: 16px; color: #333;">
                                <i class="fa fa-map-marker-alt text-danger me-2"></i>
                                Bản đồ trạm sạc & Tình trạng Pin
                            </h6>
                            <small class="text-muted">
                                Hiển thị số lượng pin đang được thuê trên tổng số hộc
                            </small>
                        </div>
                        <button type="button"
                                class="btn btn-sm btn-primary shadow-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#importShopLocationModal">
                            <i class="fa fa-upload me-1"></i> Import Vị trí
                        </button>
                    </div>

                    <div class="position-relative">
                        <div class="map-search-box">
                            <i class="fa fa-search"></i>
                            <input type="text" id="mapSearchInput"
                                   placeholder="Tìm kiếm trạm sạc"
                                   autocomplete="off">
                        </div>

                        <div id="dashboardMap" style="height:750px; width:100%;"></div>
                    </div>

                </div>
            </div>
        </div>

        {{-- TOP SHOP --}}
        <div class="col-md-4">
            @include('admin.map.top-shop-panel')
        </div>
    </div>

    {{-- MODAL IMPORT --}}
    <div class="modal fade" id="importShopLocationModal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <form method="POST" action="{{ route('admin.dashboard.importShopLocations') }}" enctype="multipart/form-data"
                  class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Import Shop Location (JSON)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning small">
                        ⚠ Import sẽ:
                        <ul class="mb-0">
                            <li>Cập nhật shop có trong file</li>
                            <li>Xóa shop KHÔNG còn trong file</li>
                        </ul>
                    </div>
                    <input type="file" name="json_file" class="form-control" accept=".json" required>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button class="btn btn-primary">Import & cập nhật</button>
                </div>
            </form>
        </div>
    </div>

@stop


@push('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        /* Fix lỗi ảnh bản đồ Bootstrap */
        .leaflet-pane img, .leaflet-tile, .leaflet-marker-icon, .leaflet-marker-shadow {
            max-width: none !important;
            max-height: none !important;
        }

        .map-search-box {
            position: absolute; top: 10px; left: 50px;
            z-index: 1000; width: 220px;
            background: #fff; border-radius: 4px; padding: 6px 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            display: flex; align-items: center; gap: 8px;
            border: 1px solid #eee;
        }
        .map-search-box input { border: none; outline: none; width: 100%; font-size: 12px; }
        .map-search-box i { font-size: 12px; color: #999; }

        /* Marker */
        .app-pin-marker {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 11px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.3);
            border: 2px solid #fff;
            transition: transform 0.2s;
        }
        .app-pin-marker:hover { transform: scale(1.15); z-index: 999; }
        .app-pin-marker.low { background: #2eca6a; }
        .app-pin-marker.mid { background: #ffc107; color: #333; }
        .app-pin-marker.high { background: #dc3545; }

        /* Popup */
        .leaflet-popup-content-wrapper { padding: 0; overflow: hidden; border-radius: 6px; box-shadow: 0 5px 15px rgba(0,0,0,0.15); }
        .leaflet-popup-content { margin: 0 !important; width: 240px !important; }

        .station-card { font-family: 'Segoe UI', sans-serif; }
        .station-header { background: #f8f9fa; padding: 8px 12px; border-bottom: 1px solid #eee; }
        .station-name { font-weight: 600; font-size: 13px; color: #333; margin-bottom: 2px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;}
        .station-stats { display: flex; padding: 10px; background: #fff; }
        .stat-box { flex: 1; text-align: center; border-right: 1px solid #f0f0f0; }
        .stat-box:last-child { border-right: none; }

        .stat-num { display: block; font-size: 15px; font-weight: 700; line-height: 1.2; }
        .stat-rent .stat-num { color: #2eca6a; }
        .stat-return .stat-num { color: #405189; }
        .stat-label { font-size: 9px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }

        /* Top shop styles */
        .top-shop-item { font-size: 13px; }
        .top-rank {
            width: 26px; height: 26px; border-radius: 50%;
            background: #f1f3f5; color: #333;
            font-weight: 700; font-size: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .top-shop-item:nth-child(1) .top-rank { background: #ffd700; }
        .top-shop-item:nth-child(2) .top-rank { background: #c0c0c0; }
        .top-shop-item:nth-child(3) .top-rank { background: #cd7f32; }

        /* ===== TOP SHOP FILTER (MODERN PILL STYLE) ===== */
        /* ===============================
           TOP SHOP FILTER BUTTON STYLE
        ================================ */
        .top-filter {
            display: flex;
            justify-content: center;
            gap: 6px;
            padding: 6px;
            background: #f8f9fb;
            border-radius: 999px;
            border: 1px solid #e6e8ec;
        }

        .filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;

            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;

            color: #495057;
            background: transparent;

            border-radius: 999px;
            cursor: pointer;
            white-space: nowrap;
            user-select: none;

            transition: all .25s ease;
        }

        .filter-pill i {
            font-size: 12px;
            opacity: .7;
        }

        /* Hover */
        .filter-pill:hover {
            background: rgba(13,110,253,0.08);
            color: #0d6efd;
        }

        /* Active */
        .btn-check:checked + .filter-pill {
            background: linear-gradient(135deg, #0d6efd, #3b82f6);
            color: #fff;
            box-shadow: 0 6px 14px rgba(13,110,253,.25);
        }

        .btn-check:checked + .filter-pill i {
            opacity: 1;
        }

        /* Focus (tab / click) */
        .filter-pill:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(13,110,253,.25);
        }

        .btn-check:checked + .filter-pill i { opacity: 1; }
        /* ===== TOP SHOP LIST - CLEAN ALIGN ===== */
        #topShopList .top-shop-item{
            padding: 10px 6px !important;
            gap: 10px;
        }

        #topShopList .top-rank{
            width: 28px;
            height: 28px;
            font-size: 12px;
        }

        #topShopList .shop-title{
            font-size: 13px;
            font-weight: 600;
            line-height: 1.2;
            margin-bottom: 2px;
            max-width: 100%;
        }

        #topShopList .shop-sub{
            font-size: 11px;
            color: #6c757d;
            display: flex;
            gap: 8px;
            align-items: center;
            line-height: 1.1;
        }

        #topShopList .shop-sub .dot{
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #cbd3da;
            display: inline-block;
        }

        #topShopList .revenue-badge{
            min-width: 92px;
            text-align: right;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 10px;
        }

        #topShopList .top-shop-item:last-child{
            border-bottom: none !important;
        }
        /* Ẩn hoàn toàn radio input */
        #topShopFilterForm .btn-check {
            display: none;
        }
        #topShopFilterForm {
            display: flex;
            justify-content: center;
        }

    </style>
@endpush


@push('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapEl = document.getElementById('dashboardMap');
            if (!mapEl) return;

            const MAP_SHOPS_URL = @json(route('admin.dashboard.map-shops'));
            const TOP_SHOPS_URL = @json(route('admin.dashboard.top-shops'));

            const map = L.map('dashboardMap').setView([21.0285, 105.8542], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const markersLayer = L.layerGroup().addTo(map);
            let shopsData = [];

            window.renderMarkers = function (list) {
                markersLayer.clearLayers();
                if (!list || !list.length) return;

                const bounds = L.latLngBounds();

                list.forEach(shop => {
                    const lat = parseFloat(shop.lat);
                    const lng = parseFloat(shop.lng);
                    if (isNaN(lat) || isNaN(lng) || lat === 0 || lng === 0) return;

                    const renting = Number(shop.renting) || 0;
                    const total   = Number(shop.total) || 0;
                    const returning = Math.max(total - renting, 0);
                    const percent = total > 0 ? (renting / total) * 100 : 0;

                    let level = 'low';
                    if (percent >= 80) level = 'high';
                    else if (percent >= 50) level = 'mid';

                    const icon = L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div class="app-pin-marker ${level}">${renting}</div>`,
                        iconSize: [40, 40],
                        iconAnchor: [20, 20],
                        popupAnchor: [0, -20]
                    });

                    const popupContent = `
                        <div class="station-card">
                            <div class="station-header">
                                <div class="station-name">${shop.shop_name ?? 'Không tên'}</div>
                            </div>
                            <div class="station-stats">
                                <div class="stat-box stat-rent">
                                    <span class="stat-num">${renting}</span>
                                    <span class="stat-label">Đang thuê</span>
                                </div>
                                <div class="stat-box stat-return">
                                    <span class="stat-num">${returning}</span>
                                    <span class="stat-label">Còn trống</span>
                                </div>
                            </div>
                        </div>
                    `;

                    L.marker([lat, lng], { icon, interactive: true })
                        .bindPopup(popupContent, { closeButton: true, autoPan: true })
                        .addTo(markersLayer);

                    bounds.extend([lat, lng]);
                });

                if (list.length > 0) map.fitBounds(bounds, { padding: [50, 50] });
            }

            async function loadMapData() {
                try {
                    const res = await fetch(MAP_SHOPS_URL, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    shopsData = Array.isArray(data) ? data : (data.data || []);
                    renderMarkers(shopsData);
                } catch (err) {
                    console.error("Lỗi tải map:", err);
                }
            }

            // search
            const searchInput = document.getElementById('mapSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const keyword = this.value.toLowerCase().trim();
                    if (!keyword) return renderMarkers(shopsData);
                    const filtered = shopsData.filter(s =>
                        (s.shop_name && s.shop_name.toLowerCase().includes(keyword)) ||
                        (s.address && s.address.toLowerCase().includes(keyword))
                    );
                    renderMarkers(filtered);
                });
            }

            // top shop
            const topListEl = document.getElementById('topShopList');

            function formatMoney(v) {
                return new Intl.NumberFormat('vi-VN').format(v) + ' ₫';
            }

            function renderTopShop(items) {
                if (!topListEl) return;

                if (!items || items.length === 0) {
                    topListEl.innerHTML = `<div class="text-muted small p-2">Không có dữ liệu</div>`;
                    return;
                }

                topListEl.innerHTML = items.map((it, idx) => `
                    <div class="top-shop-item d-flex align-items-center border-bottom">
                        <div class="top-rank me-1">${idx + 1}</div>

                        <div class="flex-grow-1 overflow-hidden">
                            <div class="shop-title text-truncate">${it.shop_name}</div>

                            <div class="shop-sub">
                                <span>${it.total_orders} giao dịch</span>
                                <span class="dot"></span>
                                <span>${formatMoney(it.total_revenue)}</span>
                            </div>
                        </div>

                        <span class="badge bg-success rounded-pill revenue-badge">
                            ${formatMoney(it.total_revenue)}
                        </span>
                    </div>
                `).join('');
            }

            async function loadTopShop(range) {
                if (!topListEl) return;
                topListEl.innerHTML = `<div class="text-muted small p-2">Đang tải...</div>`;
                try {
                    const url = `${TOP_SHOPS_URL}?range=${encodeURIComponent(range)}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const json = await res.json();
                    renderTopShop(json.data || []);
                } catch (err) {
                    console.error('[TopShop] error', err);
                    topListEl.innerHTML = `<div class="text-danger small p-2">Không tải được dữ liệu</div>`;
                }
            }

            document.querySelectorAll('#topShopFilterForm input[name="range"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    loadTopShop(this.value);
                });
            });

            loadMapData();
            const checked = document.querySelector('#topShopFilterForm input[name="range"]:checked');
            loadTopShop(checked ? checked.value : 'today');

            @if(session('imported'))
                setTimeout(() => loadMapData(), 500);
            @endif
        });
    </script>
@endpush
