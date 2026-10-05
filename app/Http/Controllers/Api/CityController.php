<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\County;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * @api {get} /api/cities Városok listázása
     * @apiName GetCities
     * @apiGroup Cities
     * @apiVersion 1.0.0
     *
     * @apiQuery {String} [search] Keresés városnév, irányítószám, lakosság vagy megyenév alapján.
     * @apiQuery {Number} [county_id] Szűrés megye azonosítója alapján.
     * @apiQuery {Number} [per_page=50] Találatok száma oldalanként: 25, 50, 100 vagy 200.
     *
     * @apiSuccess {Object[]} data Városok.
     */
    public function index(Request $request)
    {
        $query = City::with('county');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('city', 'like', '%' . $search . '%')
                    ->orWhere('zip_code', 'like', '%' . $search . '%')
                    ->orWhere('population', 'like', '%' . $search . '%')
                    ->orWhereHas('county', function ($countyQuery) use ($search) {
                        $countyQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('county_id')) {
            $query->where('id_county', $request->county_id);
        }

        $perPage = (int) $request->get('per_page', 50);
        $allowedPerPage = [25, 50, 100, 200];

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 50;
        }

        return response()->json(
            $query->orderBy('city')
                ->orderBy('zip_code')
                ->paginate($perPage)
        );
    }

    /**
     * @api {get} /api/cities/:id Egy város lekérése
     * @apiName GetCity
     * @apiGroup Cities
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Város azonosítója.
     */
    public function show(City $city)
    {
        return response()->json($city->load('county'));
    }

    /**
     * @api {post} /api/cities Város létrehozása
     * @apiName CreateCity
     * @apiGroup Cities
     * @apiVersion 1.0.0
     * @apiHeader {String} Authorization Bearer token.
     *
     * @apiBody {Number} zip_code Irányítószám.
     * @apiBody {String} city Város neve.
     * @apiBody {Number} id_county Megye azonosítója.
     * @apiBody {Number} population Lakosságszám.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'zip_code' => 'required|integer|min:1000|max:9999',
            'city' => 'required|string|max:50',
            'id_county' => 'required|exists:counties,id',
            'population' => 'required|integer|min:0',
        ]);

        $city = City::create($validated);

        $this->updateCountyPopulation($city->id_county);

        return response()->json([
            'message' => 'A település sikeresen létrehozva.',
            'data' => $city->load('county'),
        ], 201);
    }

    /**
     * @api {put} /api/cities/:id Város módosítása
     * @apiName UpdateCity
     * @apiGroup Cities
     * @apiVersion 1.0.0
     * @apiHeader {String} Authorization Bearer token.
     *
     * @apiParam {Number} id Város azonosítója.
     * @apiBody {Number} zip_code Irányítószám.
     * @apiBody {String} city Város neve.
     * @apiBody {Number} id_county Megye azonosítója.
     * @apiBody {Number} population Lakosságszám.
     */
    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'zip_code' => 'required|integer|min:1000|max:9999',
            'city' => 'required|string|max:50',
            'id_county' => 'required|exists:counties,id',
            'population' => 'required|integer|min:0',
        ]);

        $oldCountyId = $city->id_county;

        $city->update($validated);

        $this->updateCountyPopulation($oldCountyId);

        if ((int) $oldCountyId !== (int) $city->id_county) {
            $this->updateCountyPopulation($city->id_county);
        }

        return response()->json([
            'message' => 'A település sikeresen módosítva.',
            'data' => $city->fresh()->load('county'),
        ]);
    }

    /**
     * @api {delete} /api/cities/:id Város törlése
     * @apiName DeleteCity
     * @apiGroup Cities
     * @apiVersion 1.0.0
     * @apiHeader {String} Authorization Bearer token.
     *
     * @apiParam {Number} id Város azonosítója.
     */
    public function destroy(City $city)
    {
        $countyId = $city->id_county;

        $city->delete();

        $this->updateCountyPopulation($countyId);

        return response()->json([
            'message' => 'A település sikeresen törölve.'
        ]);
    }

    private function updateCountyPopulation($countyId): void
    {
        $county = County::find($countyId);

        if (!$county) {
            return;
        }

        $county->population = City::where('id_county', $countyId)
            ->sum('population');

        $county->save();
    }
}
