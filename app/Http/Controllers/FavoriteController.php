<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()->get();

        return response()->json([
            'success' => true,
            'data'    => $favorites,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_id'     => 'required|string',
            'team_name'   => 'required|string',
            'team_logo'   => 'nullable|string',
            'league_id'   => 'nullable|string',
            'league_name' => 'nullable|string',
        ]);

        $already = Favorite::where('user_id', $request->user()->id)
            ->where('team_id', $request->team_id)
            ->exists();

        if ($already) {
            return response()->json([
                'success' => false,
                'message' => 'Tim sudah ada di favorit',
            ], 409);
        }

        $favorite = Favorite::create([
            'user_id'     => $request->user()->id,
            'team_id'     => $request->team_id,
            'team_name'   => $request->team_name,
            'team_logo'   => $request->team_logo,
            'league_id'   => $request->league_id,
            'league_name' => $request->league_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tim berhasil ditambahkan ke favorit',
            'data'    => $favorite,
        ], 201);
    }

    public function destroy(Request $request, $teamId)
    {
        $deleted = Favorite::where('user_id', $request->user()->id)
            ->where('team_id', $teamId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Tim tidak ditemukan di favorit',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tim berhasil dihapus dari favorit',
        ]);
    }
}