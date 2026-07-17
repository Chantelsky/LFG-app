<?php

namespace App\Http\Controllers;

use App\Services\IgdbService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function search(Request $request, IgdbService $igdb)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = $igdb->search($query);

        return response()->json($results);
    }
}
