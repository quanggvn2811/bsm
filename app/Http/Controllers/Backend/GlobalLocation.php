<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GlobalLocation extends Controller
{
    const DEFAULT_GHN_TOKEN_API = 'f366af84-942b-11ef-9f82-9e2183930748';

    public function province(Request $request)
    {
        $provinceApiUrl = 'https://provinces.open-api.vn/api?depth=2';

        $response = file_get_contents($provinceApiUrl);

        return response()->json(['status' => 'success', 'provinces' => $response]);

    }

    public function getDistrictByCode(Request $request, $districtCode, $depth = 2)
    {
        $url = 'https://provinces.open-api.vn/api/d/' . $districtCode . '?depth=' . $depth;

        $response = file_get_contents($url);

        return response()->json(['status' => 'success', 'district' => $response]);

    }
}
