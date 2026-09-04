<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\MasterSdki;
use App\Models\MasterSiki;
use App\Models\MasterSlki;
use App\Models\MasterSpoProcedure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function sdkiSlkiSiki(Request $request): JsonResponse
    {
        $query = MasterSdki::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $sdki = $query->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Kamus data 3S PPNI berhasil diambil.',
            'data'    => $sdki->items(),
            'meta'    => [
                'current_page' => $sdki->currentPage(),
                'last_page'    => $sdki->lastPage(),
                'total'        => $sdki->total(),
            ],
        ]);
    }

    public function spoProcedures(Request $request): JsonResponse
    {
        $query = MasterSpoProcedure::with('course');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('sub_cpmk')) {
            $query->where('sub_cpmk_reference', $request->sub_cpmk);
        }

        $procedures = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Katalog prosedur SPO berhasil diambil.',
            'data'    => $procedures,
        ]);
    }

    public function courses(): JsonResponse
    {
        $courses = Course::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar stase kurikulum aktif.',
            'data'    => $courses,
        ]);
    }
}
