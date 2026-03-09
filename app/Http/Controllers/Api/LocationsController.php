<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocationsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $type = $request->get('type');
            $parentId = $request->get('parent_id');

            switch ($type) {
                case 'countries':
                    return $this->getCountries();
                case 'states':
                    return $this->getStates($parentId);
                case 'cities':
                    return $this->getCities($parentId);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid location type'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getCountries(): JsonResponse
    {
        // Sample countries data - in a real app, this would come from a database
        $countries = [
            ['id' => 1, 'name' => 'United States', 'code' => 'US'],
            ['id' => 2, 'name' => 'Canada', 'code' => 'CA'],
            ['id' => 3, 'name' => 'United Kingdom', 'code' => 'GB'],
            ['id' => 4, 'name' => 'Australia', 'code' => 'AU'],
            ['id' => 5, 'name' => 'Germany', 'code' => 'DE'],
            ['id' => 6, 'name' => 'France', 'code' => 'FR'],
            ['id' => 7, 'name' => 'Italy', 'code' => 'IT'],
            ['id' => 8, 'name' => 'Spain', 'code' => 'ES'],
            ['id' => 9, 'name' => 'Netherlands', 'code' => 'NL'],
            ['id' => 10, 'name' => 'Belgium', 'code' => 'BE'],
        ];

        return response()->json([
            'success' => true,
            'data' => ['countries' => $countries]
        ]);
    }

    private function getStates($countryId): JsonResponse
    {
        // Sample states/provinces data based on country
        $statesByCountry = [
            1 => [ // US
                ['id' => 1, 'name' => 'California', 'country_id' => 1],
                ['id' => 2, 'name' => 'Texas', 'country_id' => 1],
                ['id' => 3, 'name' => 'New York', 'country_id' => 1],
                ['id' => 4, 'name' => 'Florida', 'country_id' => 1],
                ['id' => 5, 'name' => 'Illinois', 'country_id' => 1],
            ],
            2 => [ // Canada
                ['id' => 6, 'name' => 'Ontario', 'country_id' => 2],
                ['id' => 7, 'name' => 'Quebec', 'country_id' => 2],
                ['id' => 8, 'name' => 'British Columbia', 'country_id' => 2],
                ['id' => 9, 'name' => 'Alberta', 'country_id' => 2],
            ],
            3 => [ // UK
                ['id' => 10, 'name' => 'England', 'country_id' => 3],
                ['id' => 11, 'name' => 'Scotland', 'country_id' => 3],
                ['id' => 12, 'name' => 'Wales', 'country_id' => 3],
                ['id' => 13, 'name' => 'Northern Ireland', 'country_id' => 3],
            ],
        ];

        $states = $statesByCountry[$countryId] ?? [];

        return response()->json([
            'success' => true,
            'data' => ['states' => $states]
        ]);
    }

    private function getCities($stateId): JsonResponse
    {
        // Sample cities data based on state/province
        $citiesByState = [
            1 => [ // California
                ['id' => 1, 'name' => 'Los Angeles', 'state_id' => 1],
                ['id' => 2, 'name' => 'San Francisco', 'state_id' => 1],
                ['id' => 3, 'name' => 'San Diego', 'state_id' => 1],
                ['id' => 4, 'name' => 'Sacramento', 'state_id' => 1],
            ],
            2 => [ // Texas
                ['id' => 5, 'name' => 'Houston', 'state_id' => 2],
                ['id' => 6, 'name' => 'Dallas', 'state_id' => 2],
                ['id' => 7, 'name' => 'Austin', 'state_id' => 2],
                ['id' => 8, 'name' => 'San Antonio', 'state_id' => 2],
            ],
            3 => [ // New York
                ['id' => 9, 'name' => 'New York City', 'state_id' => 3],
                ['id' => 10, 'name' => 'Buffalo', 'state_id' => 3],
                ['id' => 11, 'name' => 'Albany', 'state_id' => 3],
            ],
            6 => [ // Ontario
                ['id' => 12, 'name' => 'Toronto', 'state_id' => 6],
                ['id' => 13, 'name' => 'Ottawa', 'state_id' => 6],
                ['id' => 14, 'name' => 'Hamilton', 'state_id' => 6],
            ],
        ];

        $cities = $citiesByState[$stateId] ?? [];

        return response()->json([
            'success' => true,
            'data' => ['cities' => $cities]
        ]);
    }
}
