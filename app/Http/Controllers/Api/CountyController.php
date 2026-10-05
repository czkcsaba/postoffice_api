<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\County;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CountyController extends Controller
{
    /**
     * @api {get} /api/counties Megyék listázása
     * @apiName GetCounties
     * @apiGroup Counties
     * @apiVersion 1.0.0
     *
     * @apiQuery {String} [search] Keresés megyenév vagy lakosság alapján.
     */
    public function index(Request $request)
    {
        $query = County::withCount('cities');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('population', 'like', '%' . $search . '%');
            });
        }

        return response()->json(
            $query->orderBy('name')->get()
        );
    }

    /**
     * @api {get} /api/counties/:id Egy megye lekérése
     * @apiName GetCounty
     * @apiGroup Counties
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Megye azonosítója.
     */
    public function show(County $county)
    {
        return response()->json(
            $county->load(['cities' => function ($query) {
                $query->orderBy('city')->orderBy('zip_code');
            }])
        );
    }

    /**
     * @api {post} /api/counties Megye létrehozása
     * @apiName CreateCounty
     * @apiGroup Counties
     * @apiVersion 1.0.0
     * @apiHeader {String} Authorization Bearer token.
     *
     * @apiBody {String} name Megye neve.
     * @apiBody {String} badge Címer URL.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:counties,name',
            'badge' => 'required|url|max:2048',
        ]);

        $county = County::create([
            'name' => $validated['name'],
            'population' => 0,
            'badge' => $validated['badge'],
        ]);

        return response()->json([
            'message' => 'A megye sikeresen létrehozva.',
            'data' => $county,
        ], 201);
    }

    /**
     * @api {put} /api/counties/:id Megye módosítása
     * @apiName UpdateCounty
     * @apiGroup Counties
     * @apiVersion 1.0.0
     * @apiHeader {String} Authorization Bearer token.
     *
     * @apiParam {Number} id Megye azonosítója.
     * @apiBody {String} name Megye neve.
     * @apiBody {String} badge Címer URL.
     */
    public function update(Request $request, County $county)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('counties', 'name')->ignore($county->id),
            ],
            'badge' => 'required|url|max:2048',
        ]);

        $county->update($validated);

        return response()->json([
            'message' => 'A megye sikeresen módosítva.',
            'data' => $county->fresh(),
        ]);
    }

    /**
     * @api {delete} /api/counties/:id Megye törlése
     * @apiName DeleteCounty
     * @apiGroup Counties
     * @apiVersion 1.0.0
     * @apiHeader {String} Authorization Bearer token.
     *
     * @apiParam {Number} id Megye azonosítója.
     */
    public function destroy(County $county)
    {
        if ($county->cities()->exists()) {
            return response()->json([
                'message' => 'A megye nem törölhető, mert tartozik hozzá település.'
            ], 409);
        }

        $county->delete();

        return response()->json([
            'message' => 'A megye sikeresen törölve.'
        ]);
    }
}
