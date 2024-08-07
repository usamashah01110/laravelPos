<?php

namespace App\Services;

use App\Interfaces\FilterInterface;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

use Illuminate\Http\Request;
class FilterServices implements FilterInterface
{
   public function get_filters($filterData)
    {
        $filterHtml = view('filter',compact('filterData'))->render();

        return $filterHtml;
    }

    public function apply_filters($filterParameters)
    {
      // Define variables for sorting, role, and email
            $sortBy = null;
            $sortOrder = 'asc'; // Default to ascending
            $role = null;
            $email = null;
            $page = request('page', 1); // Get the page parameter from the request, default to 1
            $per_page = request('per_page', 10); // Get the per_page parameter from the request, default to 10

            $filteredData = User::query();

            $filtersParameters = $filterParameters->filtersParameters;
            // Loop through each filter in $filtersParameters
            foreach ($filtersParameters as $filter) {
                $label = $filter['label'];
                $operator = $filter['operator'];
                $status = $filter['status'];
                $name = $filter['name'];

                // Check for each specific filter and apply it to the query
                if ($label === 'first_name' && $operator === 'like' && $name) {
                    $filteredData->where('first_name', 'like', '%' . $name . '%'); // Make it case-insensitive
                } elseif ($label === 'email' && $operator === 'like' && $name) {
                    $filteredData->where('email', 'like', '%' . $name . '%'); // Make it case-insensitive
                } elseif ($label === 'status') {
                    $filteredData->where($label, '=', $status); //
                } elseif ($label === 'dateValue' && $operator === 'like' && $name) {
                    $filteredData->whereDate('created_at', $name);
                } elseif ($label === 'first_name') {
                    $sortBy = $label;
                } elseif ($label === 'sort_order') {
                    $sortOrder = in_array(strtolower($label), ['asc', 'desc']) ? strtolower($label) : 'asc';
                } elseif ($label === 'role') {
                    $role = $filter['role'];
                }
            }

            // Apply sorting
            if ($sortBy) {
                $filteredData->orderBy($sortBy, $sortOrder);
            }

            // Apply role filter
            if ($role) {
                $filteredData->where('role', '=', $role);
            }

            // Execute the query with pagination
            $filteredData = $filteredData->paginate($per_page, ['*'], 'page', $page);

            // You can access the paginated data using $filteredData->items()

            // Optionally, you can also access pagination information
            $paginationInfo = [
                'current_page' => $filteredData->currentPage(),
                'per_page' => $filteredData->perPage(),
                'total' => $filteredData->total(),
                'last_page' => $filteredData->lastPage(),
            ];



        // $allowedColumns = [
        //         'id',
        //         'first_name',
        //         'last_name',
        //         'email',
        //         'phone',
        //         'role',
        //         'email_verified',
        //         'phone_verified',
        //         'qr_verified',
        //         'status',
        //     ];
        //    // Get the columns of the table
        //    $tableName = 'users'; // Replace with your actual table name
        //    $allColumns = Schema::getColumnListing($tableName);

        //     $columns = array_intersect($allColumns, $allowedColumns);
        //     // Create the HTML table
        //     $htmlTable = '<table class="table table-bordered">';
        //     $htmlTable .= '<thead>';
        //     $htmlTable .= '<tr>';

        //     // Generate table headers using dynamic column names
        //     foreach ($columns as $column) {
        //         $htmlTable .= '<th>' . ucfirst(str_replace('_', ' ', $column)) . '</th>';
        //     }

        //     $htmlTable .= '</tr>';
        //     $htmlTable .= '</thead>';
        //     $htmlTable .= '<tbody>';

        //     foreach ($filteredData as $row) {
        //         $htmlTable .= '<tr>';

        //         // Generate table cells using dynamic column names
        //         foreach ($columns as $column) {
        //             $htmlTable .= '<td>' . $row[$column] . '</td>';
        //         }

        //         $htmlTable .= '</tr>';
        //     }

        //     $htmlTable .= '</tbody>';
        //     $htmlTable .= '</table';

            return [
                'data' => $filteredData->items(),
                'pagination' => $paginationInfo,
            ];
    }


}
