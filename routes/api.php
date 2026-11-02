<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountyController;


/**
 * @api {get} /api/cities Városok listázása
 * @apiName GetCities
 * @apiGroup Cities
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Az összes város lekérése lapozással.
 * Keresés és megye szerinti szűrés is lehetséges.
 * A rendezés városnév, majd irányítószám szerint történik.
 *
 * @apiQuery {String} [search] Keresés városnév, irányítószám, lakosság vagy megyenév alapján.
 * @apiQuery {Number} [county_id] Szűrés megye azonosítója alapján.
 * @apiQuery {Number} [per_page=50] Találatok száma: 25, 50, 100 vagy 200.
 * @apiQuery {Number} [page=1] Oldalszám.
 *
 * @apiSuccess {Number} current_page Aktuális oldal.
 * @apiSuccess {Object[]} data Városok listája.
 * @apiSuccess {Number} total Találatok száma.
 *
 * @apiSuccessExample {json} Sikeres válasz (200):
 * {
 *   "current_page": 1,
 *   "data": [
 *     {
 *       "id": 1,
 *       "zip_code": 2600,
 *       "city": "Vác",
 *       "id_county": 1,
 *       "population": 35000,
 *       "county": {
 *         "id": 1,
 *         "name": "Pest",
 *         "population": 35000,
 *         "badge": "https://example.com/pest.png"
 *       }
 *     }
 *   ],
 *   "per_page": 50,
 *   "total": 1
 * }
 */

/**
 * @api {get} /api/cities/:id Egy város lekérése
 * @apiName GetCity
 * @apiGroup Cities
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Egy város adatainak lekérése az azonosítója alapján.
 * A válasz tartalmazza a hozzá tartozó megyét is.
 *
 * @apiParam {Number} id Város azonosítója.
 *
 * @apiSuccess {Number} id Város azonosítója.
 * @apiSuccess {Number} zip_code Irányítószám.
 * @apiSuccess {String} city Város neve.
 * @apiSuccess {Number} id_county Megye azonosítója.
 * @apiSuccess {Number} population Lakosságszám.
 * @apiSuccess {Object} county Megye adatai.
 *
 * @apiSuccessExample {json} Sikeres válasz (200):
 * {
 *   "id": 1,
 *   "zip_code": 2600,
 *   "city": "Vác",
 *   "id_county": 1,
 *   "population": 35000,
 *   "county": {
 *     "id": 1,
 *     "name": "Pest",
 *     "population": 35000,
 *     "badge": "https://example.com/pest.png"
 *   }
 * }
 *
 * @apiError (404) NotFound A város nem található.
 */

/**
 * @api {post} /api/cities Város létrehozása
 * @apiName CreateCity
 * @apiGroup Cities
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Új város létrehozása.
 * A művelethez tokenes hitelesítés szükséges.
 * A megye lakosságszáma automatikusan újraszámolódik.
 *
 * @apiHeader {String} Authorization Bearer token.
 * @apiHeader {String} Content-Type application/json.
 *
 * @apiBody {Number} zip_code Kötelező irányítószám (1000-9999).
 * @apiBody {String} city Kötelező városnév (maximum 50 karakter).
 * @apiBody {Number} id_county Létező megye azonosítója.
 * @apiBody {Number} population Lakosságszám (minimum 0).
 *
 * @apiParamExample {json} Kérés:
 * {
 *   "zip_code": 2600,
 *   "city": "Vác",
 *   "id_county": 1,
 *   "population": 35000
 * }
 *
 * @apiSuccess {String} message Sikeres létrehozás üzenete.
 * @apiSuccess {Object} data Létrehozott város.
 *
 * @apiSuccessExample {json} Sikeres válasz (201):
 * {
 *   "message": "A település sikeresen létrehozva.",
 *   "data": {
 *     "id": 1,
 *     "zip_code": 2600,
 *     "city": "Vác",
 *     "id_county": 1,
 *     "population": 35000
 *   }
 * }
 *
 * @apiError (422) ValidationError Hibás vagy hiányzó adatok.
 */

/**
 * @api {put} /api/cities/:id Város módosítása
 * @apiName UpdateCity
 * @apiGroup Cities
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Egy meglévő város módosítása.
 * Minden mező megadása kötelező.
 * Megyeváltáskor a régi és az új megye lakossága
 * is automatikusan újraszámolódik.
 *
 * @apiHeader {String} Authorization Bearer token.
 * @apiHeader {String} Content-Type application/json.
 *
 * @apiParam {Number} id Város azonosítója.
 *
 * @apiBody {Number} zip_code Irányítószám (1000-9999).
 * @apiBody {String} city Város neve (maximum 50 karakter).
 * @apiBody {Number} id_county Megye azonosítója.
 * @apiBody {Number} population Lakosságszám (minimum 0).
 *
 * @apiParamExample {json} Kérés:
 * {
 *   "zip_code": 2600,
 *   "city": "Vác",
 *   "id_county": 1,
 *   "population": 36000
 * }
 *
 * @apiSuccessExample {json} Sikeres válasz (200):
 * {
 *   "message": "A település sikeresen módosítva.",
 *   "data": {
 *     "id": 1,
 *     "zip_code": 2600,
 *     "city": "Vác",
 *     "id_county": 1,
 *     "population": 36000
 *   }
 * }
 *
 * @apiError (404) NotFound A város nem található.
 * @apiError (422) ValidationError Hibás vagy hiányzó adatok.
 */

/**
 * @api {patch} /api/cities/:id Város módosítása PATCH metódussal
 * @apiName PatchCity
 * @apiGroup Cities
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Egy város módosítása PATCH metódussal.
 * Jelenleg ugyanazokat az adatokat várja, mint a PUT.
 * Mind a négy mező megadása kötelező.
 *
 * @apiHeader {String} Authorization Bearer token.
 * @apiHeader {String} Content-Type application/json.
 *
 * @apiParam {Number} id Város azonosítója.
 *
 * @apiBody {Number} zip_code Irányítószám.
 * @apiBody {String} city Város neve.
 * @apiBody {Number} id_county Megye azonosítója.
 * @apiBody {Number} population Lakosságszám.
 *
 * @apiParamExample {json} Kérés:
 * {
 *   "zip_code": 2600,
 *   "city": "Vác",
 *   "id_county": 1,
 *   "population": 37000
 * }
 *
 * @apiSuccessExample {json} Sikeres válasz (200):
 * {
 *   "message": "A település sikeresen módosítva."
 * }
 *
 * @apiError (404) NotFound A város nem található.
 * @apiError (422) ValidationError Hibás vagy hiányzó adatok.
 */

/**
 * @api {delete} /api/cities/:id Város törlése
 * @apiName DeleteCity
 * @apiGroup Cities
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Egy város törlése azonosító alapján.
 * Törlés után a megye lakossága újraszámolódik.
 *
 * @apiHeader {String} Authorization Bearer token.
 *
 * @apiParam {Number} id Törlendő város azonosítója.
 *
 * @apiSuccessExample {json} Sikeres válasz (200):
 * {
 *   "message": "A település sikeresen törölve."
 * }
 *
 * @apiError (404) NotFound A város nem található.
 */

/**
 * @api {get} /api/counties Megyék listázása
 * @apiName GetCounties
 * @apiGroup Counties
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Az összes megye lekérése név szerint rendezve.
 * A válasz tartalmazza a megyékhez tartozó
 * városok számát is.
 *
 * @apiQuery {String} [search] Keresés megyenév vagy lakosság alapján.
 *
 * @apiSuccess {Object[]} counties Megyék listája.
 * @apiSuccess {Number} counties.id Megye azonosítója.
 * @apiSuccess {String} counties.name Megye neve.
 * @apiSuccess {Number} counties.population Lakosságszám.
 * @apiSuccess {String} counties.badge Címer URL.
 * @apiSuccess {Number} counties.cities_count Városok száma.
 *
 * @apiSuccessExample {json} Sikeres válasz (200):
 * [
 *   {
 *     "id": 1,
 *     "name": "Pest",
 *     "population": 35000,
 *     "badge": "https://example.com/pest.png",
 *     "cities_count": 1
 *   }
 * ]
 */

/**
 * @api {get} /api/counties/:id Egy megye lekérése
 * @apiName GetCounty
 * @apiGroup Counties
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Egy megye lekérése azonosító alapján.
 * A válasz a hozzá tartozó városokat is tartalmazza.
 * A városok név és irányítószám szerint rendezettek.
 *
 * @apiParam {Number} id Megye azonosítója.
 *
 * @apiSuccess {Number} id Megye azonosítója.
 * @apiSuccess {String} name Megye neve.
 * @apiSuccess {Number} population Lakosságszám.
 * @apiSuccess {String} badge Címer URL.
 * @apiSuccess {Object[]} cities Megyéhez tartozó városok.
 *
 * @apiSuccessExample {json} Sikeres válasz (200):
 * {
 *   "id": 1,
 *   "name": "Pest",
 *   "population": 35000,
 *   "badge": "https://example.com/pest.png",
 *   "cities": [
 *     {
 *       "id": 1,
 *       "zip_code": 2600,
 *       "city": "Vác",
 *       "id_county": 1,
 *       "population": 35000
 *     }
 *   ]
 * }
 *
 * @apiError (404) NotFound A megye nem található.
 */

/**
 * @api {post} /api/counties Megye létrehozása
 * @apiName CreateCounty
 * @apiGroup Counties
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Új megye létrehozása.
 * A megye kezdeti lakosságszáma 0.
 * A population mezőt nem kell megadni.
 *
 * @apiHeader {String} Authorization Bearer token.
 * @apiHeader {String} Content-Type application/json.
 *
 * @apiBody {String} name Megye neve (maximum 50 karakter, egyedi).
 * @apiBody {String} badge Érvényes címer URL (maximum 2048 karakter).
 *
 * @apiParamExample {json} Kérés:
 * {
 *   "name": "Pest",
 *   "badge": "https://example.com/pest.png"
 * }
 *
 * @apiSuccessExample {json} Sikeres válasz (201):
 * {
 *   "message": "A megye sikeresen létrehozva.",
 *   "data": {
 *     "id": 1,
 *     "name": "Pest",
 *     "population": 0,
 *     "badge": "https://example.com/pest.png"
 *   }
 * }
 *
 * @apiError (422) ValidationError Hibás adatok vagy már létező megyenév.
 */

/**
 * @api {put} /api/counties/:id Megye módosítása
 * @apiName UpdateCounty
 * @apiGroup Counties
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Egy meglévő megye nevének és címerének módosítása.
 * A lakosságszámot nem lehet közvetlenül módosítani.
 *
 * @apiHeader {String} Authorization Bearer token.
 * @apiHeader {String} Content-Type application/json.
 *
 * @apiParam {Number} id Megye azonosítója.
 *
 * @apiBody {String} name Megye neve (maximum 50 karakter, egyedi).
 * @apiBody {String} badge Címer URL (maximum 2048 karakter).
 *
 * @apiParamExample {json} Kérés:
 * {
 *   "name": "Pest vármegye",
 *   "badge": "https://example.com/pest.png"
 * }
 *
 * @apiSuccessExample {json} Sikeres válasz (200):
 * {
 *   "message": "A megye sikeresen módosítva.",
 *   "data": {
 *     "id": 1,
 *     "name": "Pest vármegye",
 *     "population": 35000,
 *     "badge": "https://example.com/pest.png"
 *   }
 * }
 *
 * @apiError (404) NotFound A megye nem található.
 * @apiError (422) ValidationError Hibás adatok vagy már létező megyenév.
 */

/**
 * @api {patch} /api/counties/:id Megye módosítása PATCH metódussal
 * @apiName PatchCounty
 * @apiGroup Counties
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Egy megye módosítása PATCH metódussal.
 * Jelenleg a name és badge mező egyaránt kötelező.
 *
 * @apiHeader {String} Authorization Bearer token.
 * @apiHeader {String} Content-Type application/json.
 *
 * @apiParam {Number} id Megye azonosítója.
 *
 * @apiBody {String} name Megye neve (egyedi, maximum 50 karakter).
 * @apiBody {String} badge Érvényes címer URL.
 *
 * @apiParamExample {json} Kérés:
 * {
 *   "name": "Pest vármegye",
 *   "badge": "https://example.com/pest.png"
 * }
 *
 * @apiSuccessExample {json} Sikeres válasz (200):
 * {
 *   "message": "A megye sikeresen módosítva."
 * }
 *
 * @apiError (404) NotFound A megye nem található.
 * @apiError (422) ValidationError Hibás vagy hiányzó adatok.
 */

/**
 * @api {delete} /api/counties/:id Megye törlése
 * @apiName DeleteCounty
 * @apiGroup Counties
 * @apiVersion 1.0.0
 *
 * @apiDescription
 * Egy megye törlése azonosító alapján.
 * Csak akkor törölhető, ha nem tartozik hozzá város.
 * Ha van hozzá kapcsolódó település, HTTP 409-es hibát ad.
 *
 * @apiHeader {String} Authorization Bearer token.
 *
 * @apiParam {Number} id Törlendő megye azonosítója.
 *
 * @apiSuccessExample {json} Sikeres válasz (200):
 * {
 *   "message": "A megye sikeresen törölve."
 * }
 *
 * @apiError (404) NotFound A megye nem található.
 *
 * @apiErrorExample {json} Hiba (409):
 * HTTP/1.1 409 Conflict
 * {
 *   "message": "A megye nem törölhető, mert tartozik hozzá település."
 * }
 */


/*
|--------------------------------------------------------------------------
| Nyilvános lekérdezések
|--------------------------------------------------------------------------
*/
Route::get('/cities', [CityController::class, 'index']);
Route::get('/cities/{city}', [CityController::class, 'show']);

Route::get('/counties', [CountyController::class, 'index']);
Route::get('/counties/{county}', [CountyController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Tokenhez kötött adatmódosítások
|--------------------------------------------------------------------------
*/
Route::middleware('api.token')->group(function () {
    Route::post('/cities', [CityController::class, 'store']);
    Route::put('/cities/{city}', [CityController::class, 'update']);
    Route::patch('/cities/{city}', [CityController::class, 'update']);
    Route::delete('/cities/{city}', [CityController::class, 'destroy']);

    Route::post('/counties', [CountyController::class, 'store']);
    Route::put('/counties/{county}', [CountyController::class, 'update']);
    Route::patch('/counties/{county}', [CountyController::class, 'update']);
    Route::delete('/counties/{county}', [CountyController::class, 'destroy']);
});
