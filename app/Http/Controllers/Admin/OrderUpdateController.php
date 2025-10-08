<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class OrderUpdateController
{
    public function updateShops(Request $request): JsonResponse
    {
        $mappings = [
            'Bia Thu Trà (MB - HN - CG)' => 'Bia Thu Trà (MB-HN-CG)',
            'Bệnh viện Xanh Pôn (MB - HN - CVA)' => 'Bệnh viện Xanh Pôn (MB-HN-BD)',
            'Night Street Coffee (MB_HN_TH)' => 'Night Street Coffee (MB-HN-TH)',
            'Tzuli Coffee ( MB - HN - TX )' => 'Tzuli Coffee (MB-HN-TX)',
            'Lagaia Spa' => 'Lagaia Spa (MB-HN-YH)',
            'Bệnh viện quân Y 110 ( MB-BN-TC)' => 'Bệnh viện quân Y 110 (MB-BN-TC)',
            'Cafe Phúc Lai (MB_HN_BD)' => 'Cafe Phúc Lai (MB-HN-BD)',
            'Ta Gaming 2' => 'Ta Gaming 2 (MB-HN-YH)',
            'Nhà Hàng 29 ( MB-BN-VC)' => 'Nhà Hàng 29 (MB-BN-VC)',
            'AHA cafe Nguyễn hữu Huân (MB_HN_HK)' => 'AHA cafe Nguyễn hữu Huân (MB-HN-HK)',
            'Tim Baber( MB- HN HĐ)' => 'Tim Babershop (MB-HN-HĐ)',
            'Cơm Niêu Ngọc Linh' => 'Cơm Niêu Ngọc Linh (MB-HN-GL)',
            'Lẩu nấm hoàng kim ( MB-HN-CG)' => 'Lẩu nấm hoàng kim (MB-HN-CG)',
            'Lana Billiards (MB_HN_BD)' => 'Lana Billiards (MB-HN-BD)',
            'The Lounge (MB - HN - CG)' => 'The Lounge (MB-HN-CG)',
            'Dragon Poker (MB - HN - CG)' => 'Dragon Poker (MB-HN-CG)',
            'Nhà hàng Thẩm (MB-HN- CG)' => 'Nhà hàng Thẩm (MB-HN-CG)',
            'Timbaber( HN- MB- CG)' => 'Tim Babershop (HN-MB-CG)',
            'Timbaber ( MB- HN- ĐĐ)' => 'Tim Babershop (MB-HN-ĐĐ)',
            'Chè ngon 85 Hàng Bạc (MB_HN_HK)' => 'Chè ngon 85 Hàng Bạc (MB-HN-HK)',
            'Hà Nội Cà Phê ( MB- HN- GL )' => 'Hà Nội Cà Phê (MB-HN-GL)',
            'Nhà hàng Quốc Dân ( MB - HN - DD )' => 'Nhà hàng Quốc Dân (MB-HN-DD)',
            'HADU SUSHI ( MB- HN - HBT)' => 'HADU SUSHI (MB-HN-HBT)',
            'Chao Quán(MB- HN- GL)' => 'Chao Quán (MB-HN-GL)',
            'CAFE - Ăn Sáng( MB- HN - DVH)' => 'CAFE - Ăn Sáng (MB-HN-CG)',
            'AHA cafe cửa nam (MB_HN_HK)' => 'AHA cafe cửa nam (MB-HN-HK)',
            'Aha coffee Hàng Chuối (MB_HN_HBT)' => 'Aha coffee Hàng Chuối (MB-HN-HBT)',
            'D.O.C cofffee music ( MB-HN-TH)' => 'D.O.C coffee music (MB-HN-TH)',
            'Aha Coffee Phan Kế Bính (MB_HN_BD)' => 'Aha Coffee Phan Kế Bính (MB-HN-BD)',
            'Tran Beauty Center ( MB- HN- BD )' => 'Tran Beauty Center (MB-HN-BD)',
            'HADU SUSHI ( MB- HN - HD )' => 'HADU SUSHI (MB-HN-HD)',
            'AHA Cafe hàng buồm (MB_HN_HK)' => 'AHA Cafe hàng buồm (MB-HN-HK)',
            'Khrua baan thai - 358 thái hà ( MB-HN-DD)' => 'Khrua baan thai - 358 thái hà (MB-HN-DD)',
            'Chè ngon 93 hàng bạc (MN_HN_HK)' => 'Chè ngon 93 hàng bạc (MB-HN-HK)',
            'Niêu quán (MB_HN_BD)' => 'Niêu quán (MB-HN-BD)',
            'Cà Phê Chuông Vàng' => 'Cà Phê Chuông Vàng (MB-HN-BD)',
            'Hảo La ( MB-HN-CG )' => 'Hảo La (MB-HN-CG)',
            'Hà Nội Roastery ( MB-HN-TH)' => 'Hà Nội Roastery (MB-HN-TH)',
            'Meo Billiards Pool Club ( MB-HN- GL)' => 'Meo Billiards Pool Club (MB-HN-GL)',
            'Nhà hàng chay Mộc An ( MB-HN-DD )' => 'Nhà hàng chay Mộc An (MB-HN-DD)',
            'Đạt Coffee & Tea ( MB-HN-HBT )' => 'Đạt Coffee & Tea (MB-HN-HBT)',
            'The Sipping Bar ( MB_HN_BD)' => 'The Sipping Bar (MB-HN-BD)',
            'HADU SUSHI( MB- HN- CG )' => 'HADU SUSHI(MB-HN-CG)',
            'Chao Quán ( MB-HN-DD)' => 'Chao Quán (MB-HN-DD)',
            'Onro coffee (MB_HN_BD)' => 'Onro coffee (MB-HN-BD)',
            'Chất coffee ( MB-BN-VC)' => 'Chất coffee (MB-BN-VC)',
            'Hidden alley Hà Noi (MB_HN_HK)' => 'Hidden alley Hà Noi (MB-HN-HK)',
            'Seoho photo ( MB-HN-TH)' => 'Seoho photo (MB-HN-TH)',
            'Kun Thai ( MB-BN-VC)' => 'Kun Thai (MB-BN-VC)',
            'AHA coffee Tô Hiến Thành (MB_HN_HBT)' => 'AHA coffee Tô Hiến Thành (MB-HN-HBT)',
            'Nhà hàng zozo đường láng (MB_HN_VN)' => 'Nhà hàng zozo đường láng (MB-HN-VN)',
            'Bánh tráng cuốn thịt heo Vân Béo ( MB-HN-TX)' => 'Bánh tráng cuốn thịt heo Vân Béo (MB-HN-TX)',
            'Khách Sạn Bảo Anh ( MB-HN-TL)' => 'Khách Sạn Bảo Anh (MB-HN-TL)',
            'Cafe 1983 (MB_HN_BD)' => 'Cafe 1983 (MB-HN-BD)',
            'Saymee Coffee (MB_HN_CG)' => 'Saymee Coffee (MB-HN-CG)',
            'AHA cafe cửa đông (MB_HN_HK)' => 'AHA cafe cửa đông (MB-HN-HK)',
            'With Us Cafe (MB - HN - BĐ)' => 'With Us Cafe (MB-HN-BĐ)',
            'Kho test(MB-HN-Ngoc Hoi)' => 'Kho test (MB-HN-HM)',
            'Good Juice Cafe (MB_HN_BD)' => 'Good Juice Cafe (MB-HN-BD)',
            'La Boong 87 Đội Cấn (MB_HN_BD)' => 'La Boong 87 Đội Cấn (MB-HN-BD)',
            'Homestay 114 Ngọc Hà (MB_HN_BD)' => 'Homestay 114 Ngọc Hà (MB-HN-BD)',
            'Lake view cafe (MB_HN_TH)' => 'Lake view cafe (MB-HN-TH)',
            'Bia hơi Lợi Hói (MB_HN_BD)' => 'Bia hơi Lợi Hói (MB-HN-BD)',
            'Karaoke zozo trần kim xuyến (MB_HN_VN)' => 'Karaoke zozo trần kim xuyến (MB-HN-VN)',
            'Nhà hàng Lan béo vườn cau (MB_HN_BD)' => 'Nhà hàng Lan béo vườn cau (MB-HN-BD)',
            'Linh Vy Cafe (Cafe B52) - Di tích hồ máy bay B52 (MB_HN_BD)' => 'Linh Vy Cafe (Cafe B52) - Di tích hồ máy bay B52 (MB-HN-BD)',
            'Nhà cafe (MB_HN_BD)' => 'Nhà cafe (MB-HN-BD)',
            'Lana bia (MB_HN_VN)' => 'Lana bia (MB-HN-BD)',
            'The Lavish (MB - HN - HK)' => 'The Lavish (MB-HN-HK)',
            'The little plan (MB_HN_HK)' => 'The little plan (MB-HN-HK)',
            'OZ Coffee (MB_HN_BD)' => 'OZ Coffee (MB-HN-BD)',
            'Nhà hàng Ánh Tuyết (MB_HN_BD)' => 'Nhà hàng Ánh Tuyết (MB-HN-BD)',
            'Cafe Chim (MB_HN_BD)' => 'Cafe Chim (MB-HN-BD)',
            'Low coffee (MB_HN_BD)' => 'Low coffee (MB-HN-BD)',
            'New world bi a ( MB_ HN_BD)' => 'New world bi a (MB-HN-BD)',
            'Bia Vân Bảo Khánh Trần Thánh Tông (MB - HN - HBT)' => 'Bia Vân Bảo Khánh Trần Thánh Tông (MB-HN-HBT)',
            'Bia nhớ (MB_HN_BD)' => 'Bia nhớ (MB-HN-BD)',
            'Son cafe (MB_HN_TH)' => 'Son cafe (MB-HN-TH)',
            'Cafe Tò Te (MB_HN_BD)' => 'Cafe Tò Te (MB-HN-BD)',
            'Cửa hàng mỳ Naruto Ramen (MB_HN_TH)' => 'Cửa hàng mỳ Naruto Ramen (MB-HN-TH)',
            'Moon Cafe (MB_HN_TH)' => 'Moon Cafe (MB-HN-TH)',
            'Coc Roastery (MB_HN_TH)' => 'Coc Roastery (MB-HN-TH)',
            '2A Coffee (MB - HN - BĐ)' => '2A Coffee (MB-HN-BĐ)',
            'Lotus Lounge nhà ga T1 (MB - HN - NB)' => '	Lotus Lounge nhà ga T1 (MB-HN-NB)',
            'Lotus Lounge nhà ga T2 (MB - HN - NB)' => 'Lotus Lounge nhà ga T2 (MB-HN-NB)',
            'Ikigai Garden Cafe ( MB-HN-CG)' => 'Ikigai Garden Cafe (MB-HN-CG)',
            'Lõi cà phê ( MB-HN-CG)' => 'Lõi cà phê (MB-HN-CG)',
            'Umee Coffee ( MB-HN-CG)' => 'Umee Coffee (MB-HN-CG)',
            'Linka cafe(MB-HN-CG)' => 'Linka cafe (MB-HN-CG)',
            'Infinity coffee( MB-HN-CG)' => 'Infinity coffee (MB-HN-CG)',
            'Nhà A1 tầng 1 (MB - HN -CVA)' => 'Nhà A1 tầng 1 (MB-HN-BD)',
            'A Phú Bilard Shop' => 'A Phú Bilard Shop (MB-HN-DD)',
            'Center point poker club' => 'Center point poker club (MB-HN-TX)',
            'B96 Arena Pool' => 'B96 Arena Pool (MB-HN-HM)',
            'Laboong Trần Bình (MB-HN-CG)' => '	La Boong Trần Bình (MB-HN-CG)',
            'Nha Khoa Bạch Mai ( MB-HN-BTL)' => 'Nha Khoa Bạch Mai (MB-HN-BTL)',
            'Koh Yam ( MB-HN-TH )' => 'Koh Yam (MB-HN-TH)',
            'Ô Trống coffee ( MB-HN-CG)' => 'Ô Trống coffee (MB-HN-CG)',
            'Paradise Pickleball Club' => 'Paradise Pickleball Club (MB-HN-LB)',
            'D beauty center 1985' => 'D Beauty Center 1985 (MB-HN-HD)',
            'AHA cafe đường thành (MB_HN_HK)' => 'AHA cafe đường thành (MB-HN-HK)',
            'Timbaber ( MB-HN- HBT)' => 'Tim Babershop (MB-HN-HBT)',
            'Ẩm Thực Quê' => '	Ẩm Thực Quê (MB-HN-LB)',
            'Futa hà Sơn Bắc Vinh' => 'Futa hà Sơn Bắc Vinh (MB-NA-V)',
            'Bia Cường Hói (MB - HN - Trích Sài)' => 'Bia Cường Hói (MB-HN-TH)',
        ];

        $updatedCount = 0;

        foreach ($mappings as $old => $new) {
            $count = DB::table('orders')
                ->where('rental_shop', $old)
                ->orWhere('return_shop', $old)
                ->update([
                    'rental_shop' => DB::raw("CASE WHEN rental_shop = '$old' THEN '$new' ELSE rental_shop END"),
                    'return_shop' => DB::raw("CASE WHEN return_shop = '$old' THEN '$new' ELSE return_shop END")
                ]);
            $updatedCount += $count;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đã cập nhật {$updatedCount} bản ghi trong bảng orders.",
        ]);
    }
}
