<?php

namespace App\Http\Controllers\Unused;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Subscription;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HandleFilterController extends Controller
{

    public function getUserColoums()
    {
        // Get the current route's URI
        $currentRoute = request()->path();

        // Define a map of route keywords to table names
        $tableMap = [
            'plan' => 'plans',
            'user' => 'users',
            'subscription' => 'subscriptions',
        ];

        // Initialize variables for columns and operators
        $columns = [];
        $operators = ['=', '!=', '>', '<', '>=', '<=', 'LIKE'];

        // Determine the table name based on the route
        foreach ($tableMap as $keyword => $tableName) {
            if (Str::contains($currentRoute, $keyword)) {
                $columns = Schema::getColumnListing($tableName);
                break;
            }
        }

        // Retrieve operators from route parameters if available
        $routeOperators = request()->input('operators');

        if (!empty($routeOperators)) {
            $operators = explode(',', $routeOperators);
        }

        return response()->json([
            'columns' => $columns,
            'operators' => $operators,
        ]);
    }

    public function getFilterResult(Request $request){

            $filters = $request->input('filters'); // Assuming 'filters' is an array of filter objects

            $currentRoute = request()->path();

            // Define a map of route keywords to model names and their respective columns
            $modelMap = [
                'plan' => [
                    'model' => Plan::class,
                    'table' => 'plans',
                ],
                'subscription' => [
                    'model' => Subscription::class,
                    'table' => 'subscriptions',
                ],
                'user' => [
                    'model' => User::class,
                    'table' => 'users',
                ],
            ];

            // Determine the model and table based on the route
            foreach ($modelMap as $keyword => $data) {
                if (Str::contains($currentRoute, $keyword)) {
                    $model = app($data['model']);
                    $table = $data['table'];
                    break;
                }
            }

            if (!isset($model)) {
                return response()->json(['error' => 'Invalid route.']);
            }

            $filteredData = $model::query();

            foreach ($filters as $filter) {
                $column = $filter['column'];
                $filterValue = $filter['value'];
                $operator = isset($filter['operator']) ? $filter['operator'] : 'like';

                if (Schema::hasColumn('users', $column)) {
                    if ($operator === 'like') {
                        $filteredData->orWhere($column, 'like', '%' . $filterValue . '%');
                    } else {
                        $filteredData->orWhere($column, $operator, $filterValue);
                    }
                }
            }

            $filteredData = $filteredData->get();


            return response()->json([
                    'filteredData' => $filteredData,
                    ]);
    }


 public function applyAdditionalFilters(Request $request)
{
    $additionalFilters = $request->input('additional_filters'); // Assuming 'additional_filters' is an array of filter objects
    $filteredData = $request->input('filteredData'); // Get the initial filtered data as a collection of model instances
    $filteredData = collect($filteredData); // Convert the array to a collection

    // Get the current route's URI
    $currentRoute = request()->path();

    // Define a map of route keywords to model names and their respective columns
    $modelMap = [
        'plan' => [
            'model' => Plan::class,
            'table' => 'plans',
        ],
        'subscription' => [
            'model' => Subscription::class,
            'table' => 'subscriptions',
        ],
        'user' => [
            'model' => User::class,
            'table' => 'users',
        ],
    ];

    // Determine the model and table based on the route
    foreach ($modelMap as $keyword => $data) {
        if (Str::contains($currentRoute, $keyword)) {
            $model = app($data['model']);
            $table = $data['table'];
            break;
        }
    }

    if (!isset($model)) {
        return response()->json(['error' => 'Invalid route.']);
    }

    foreach ($additionalFilters as $filter) {
        $column = $filter['column'];
        $filterValue = $filter['value'];
        $operator = isset($filter['operator']) ? $filter['operator'] : 'like';

        // Check if the specified column is valid
        $modelColumns = Schema::getColumnListing($table);
        if (!in_array($column, $modelColumns)) {
            continue; // Skip invalid columns
        }

        $filteredData = $filteredData->filter(function ($item) use ($column, $filterValue, $operator) {
            if ($operator === 'like') {
                return stripos($item->$column, $filterValue) !== false;
            } else {
                switch ($operator) {
                    case '=':
                        return $item->$column == $filterValue;
                    case '!=':
                        return $item->$column != $filterValue;
                    // Add more cases for other operators as needed
                    default:
                        return false;
                }
            }
        });
    }

    $filteredData = $filteredData->values(); // Re-index the collection

    return response()->json([
        'filteredData' => $filteredData,
    ]);
}

}
