<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShopLocation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardMapController extends Controller
{
    public function index()
    {
        return view('admin.map.index');
    }

    /**
     * API load data map: vị trí + renting/total slots
     */
    public function shops(): JsonResponse
    {
        $shops = ShopLocation::query()
            ->join('shop_rental_stats', 'shop_locations.shop_id', '=', 'shop_rental_stats.shop_id') // ✅ inner join
            ->select([
                'shop_locations.shop_id',
                'shop_locations.shop_name',
                'shop_locations.lat',
                'shop_locations.lng',
                'shop_rental_stats.renting_slots',
                'shop_rental_stats.total_slots',
            ])
            ->whereNotNull('shop_locations.lat')
            ->whereNotNull('shop_locations.lng')
            ->where('shop_locations.lat', '!=', 0)
            ->where('shop_locations.lng', '!=', 0)
            ->where('shop_rental_stats.total_slots', '>', 0) // nếu muốn chỉ hiện shop có hộc
            ->get()
            ->map(function ($s) {
                return [
                    'shop_id'   => (string) $s->shop_id,      // ✅ GIỮ STRING
                    'shop_name' => $s->shop_name,
                    'lat'       => (float) $s->lat,
                    'lng'       => (float) $s->lng,
                    'renting'   => (int) $s->renting_slots,
                    'total'     => (int) $s->total_slots,
                ];
            })
            ->values();

        return response()->json($shops);
    }

    /**
     * API top shop giao dịch theo range: today|week|month|all
     * GET /admin/dashboard/top-shops?range=today
     */
    public function topShops(Request $request): JsonResponse
    {
        $range = $request->get('range', 'today');

        // KPI: doanh thu theo thời điểm thanh toán
        $timeColumn = 'orders.payment_time';

        [$from, $to] = $this->resolveRange($range);

        $q = Order::query()
            ->from('orders')
            ->leftJoin('shop_locations', 'shop_locations.shop_id', '=', 'orders.rental_shop_id')
            ->whereNotNull('orders.rental_shop_id')
            ->where('orders.rental_shop_id', '!=', '')
            ->where('orders.order_status', 'Complete')   // theo dữ liệu bạn cung cấp
            ->where('orders.order_amount', '>', 0)       // ✅ CHỈ LẤY ORDER CÓ TIỀN
            ->selectRaw('
                orders.rental_shop_id as shop_id,
                COALESCE(shop_locations.shop_name, MAX(orders.rental_shop)) as shop_name,
                COUNT(*) as total_orders,
                SUM(orders.order_amount) as total_revenue
            ');

        if ($from && $to) {
            $q->whereBetween($timeColumn, [$from, $to]);
        }

        $data = $q->groupBy('orders.rental_shop_id', 'shop_locations.shop_name')
            ->orderByDesc('total_revenue')   // ✅ sort theo doanh thu
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'shop_id'        => $r->shop_id,
                'shop_name'      => $r->shop_name ?? ('Shop ' . $r->shop_id),
                'total_orders'   => (int) $r->total_orders,
                'total_revenue'  => (float) $r->total_revenue,
            ])
            ->values();

        return response()->json([
            'range' => $range,
            'data'  => $data,
        ]);
    }

    private function resolveRange(string $range): array
    {
        $now = Carbon::now();

        return match ($range) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'week'  => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'all'   => [null, null],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }
}
