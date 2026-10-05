# PostOffice API

Laravel alapú REST API magyarországi megyék és városok kezeléséhez.

Az API ugyanazokat az adatokat használja, mint a webes PostOffice alkalmazás.  
A lekérdezések nyilvánosak, az adatmódosító műveletekhez Bearer token szükséges.

## API token

Az `.env` fájlba:

```env
API_TOKEN=postoffice-2026-secret
```

Módosító kérésnél a fejléc:

```text
Authorization: Bearer postoffice-2026-secret
Accept: application/json
Content-Type: application/json
```

## Használható végpontok

| Metódus | Végpont | Leírás | Token |
|---|---|---|---|
| GET | `/api/cities` | Városok listázása | Nem |
| GET | `/api/cities/{id}` | Egy város lekérése | Nem |
| POST | `/api/cities` | Új város létrehozása | Igen |
| PUT / PATCH | `/api/cities/{id}` | Város módosítása | Igen |
| DELETE | `/api/cities/{id}` | Város törlése | Igen |
| GET | `/api/counties` | Megyék listázása | Nem |
| GET | `/api/counties/{id}` | Egy megye lekérése a városaival | Nem |
| POST | `/api/counties` | Új megye létrehozása | Igen |
| PUT / PATCH | `/api/counties/{id}` | Megye módosítása | Igen |
| DELETE | `/api/counties/{id}` | Megye törlése | Igen |

## Keresés

Város keresése:

```text
GET /api/cities?search=Szeged
```

A keresés a város nevében, irányítószámban, lakosságban és a megye nevében is keres.

Megye keresése:

```text
GET /api/counties?search=Csongrád
```

## Megyék szerinti szűrés

Például a 6-os azonosítójú megye városai:

```text
GET /api/cities?county_id=6
```

A keresés és a szűrés egyszerre is használható:

```text
GET /api/cities?search=Szeged&county_id=6
```

## Lapozás

A városok listája lapozott. Használható értékek:

```text
25, 50, 100, 200
```

Példa:

```text
GET /api/cities?per_page=25
```

## Város létrehozása

```json
{
    "zip_code": 6720,
    "city": "Szeged",
    "id_county": 6,
    "population": 157000
}
```

## Megye létrehozása

```json
{
    "name": "Teszt megye",
    "badge": "https://example.com/badge.png"
}
```

A megye `population` mezőjét nem kell kézzel megadni.  
A rendszer a hozzá tartozó városok lakosságából automatikusan újraszámolja.

## APIDOC dokumentáció

Telepítés:

```bash
npm install --save-dev apidoc
```

Dokumentáció generálása:

```bash
npx apidoc -i app/Http/Controllers/Api -o public/apidoc
```

Ezután a dokumentáció:

```text
http://127.0.0.1:8000/apidoc/
```

## Indítás

```bash
php artisan serve
```

A route-ok ellenőrzése:

```bash
php artisan route:list --path=api
```
