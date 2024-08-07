<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PagePermission;
use Nette\Schema\ValidationException;

class UserPermissionController extends Controller
{
    /**
     * Display a form for assigning permissions.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $availableSlugs = array_keys(get_user_slugs());
            $assignedSlugs = $user->allowedPageSlugs();
            $message = "User permissions fetched successfully.";
            return ApiResponseHelper::sendSuccessResponse(compact('user', 'availableSlugs', 'assignedSlugs'), $message, 200);
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([$e->getMessage()], 'user not found.', 500);
        }
    }

    /**
     * Update the user's permissions.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);
            // Validate the incoming request
            $validated = $request->validate([
                'page_slugs' => 'array',
                'page_slugs.*' => 'string|exists:page_permissions,page_slug',
            ]);
            // Sync the page permissions
            $user->pagePermissions()->delete();
            $pagePermissions = array_map(function ($slug) use ($user) {
                return [
                    'user_id' => $user->id,
                    'page_slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $validated['page_slugs']);
            PagePermission::insert($pagePermissions);
            return ApiResponseHelper::sendSuccessResponse($pagePermissions, "User permissions updated successfully.", 201);
        } catch (ValidationException $e) {
            return ApiResponseHelper::sendErrorResponse($e->errors(), "Failed to create user permissions.", 422);
        } catch (\Exception $e) {
            if (method_exists($e, 'errors')) {
                return ApiResponseHelper::sendErrorResponse($e->errors(), "Failed to create user permissions.", 422);
            } else {
                return ApiResponseHelper::sendErrorResponse($e->getMessage(), "Failed to create user permissions.", 500);
            }
        }

    }
}
