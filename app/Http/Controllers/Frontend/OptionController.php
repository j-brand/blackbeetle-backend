<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Option;

class OptionController extends Controller
{

    public function getLocation()
    {
        $option = Option::where('option', 'my_location')->first();
        return response()->json($option);
    }
}
