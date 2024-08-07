<?php

namespace App\Http\Controllers\Unused;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogPostController extends Controller
{
    public function sendError($errorData, $message, $status)
    {
        $response = [];
        $response['message'] = $message;
        //        $response['status'] = $status;
        if (!empty($errorData)) {
            $errorDataArray = json_decode($errorData);
            $newErrorDataArr = [];
            foreach ($errorDataArray as $item => $value) {
                $newErrorDataArr[$item] = $value[0];
            }

            $response['errors'] = $newErrorDataArr;
        }
        return response()->json($response, $status);
    }

    /**
 * @OA\Schema(
 *     schema="Blog",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="content", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 */

/**
 * @OA\Get(
 *     path="/blogs",
 *     summary="Get a list of blog posts",
 *     description="Retrieve a paginated list of blog posts.",
 *     operationId="getBlogPosts",
 *     tags={"Blogs"},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number for pagination",
 *         required=false,
 *         @OA\Schema(type="integer", default=1),
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of posts per page",
 *         required=false,
 *         @OA\Schema(type="integer", default=5),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of blog posts retrieved successfully",
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="No blog posts found",
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid page or per_page parameter",
 *     ),
 * )
 *
 * @return \Illuminate\Http\JsonResponse
 */


    public function index()
    {
        $posts = Blog::paginate(5);
        if (!empty($posts)){
        return response()->json(['data'=>$posts]);
        }
        return response()->json(['message'=>'index function']);
    }


    /**
 * @OA\Get(
 *     path="/blogs/{slug}",
 *     summary="Get a blog post by slug",
 *     description="Retrieve a blog post by its slug.",
 *     operationId="getBlogBySlug",
 *     tags={"Blogs"},
 *     @OA\Parameter(
 *         name="slug",
 *         in="path",
 *         description="Slug of the blog post to retrieve",
 *         required=true,
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Blog post retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="meta_title", type="string"),
 *             @OA\Property(property="meta_description", type="string"),
 *             @OA\Property(property="content", type="string"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Blog not found",
 *     ),
 * )
 *
 * @param string $slug
 * @return \Illuminate\Http\JsonResponse
 */
    public function show_blog($slug = '')
    {
        $data = Blog::where('slug', $slug)->first();

        if (!$data) {
            return response()->json([
                'message' => 'Blog not found',
            ], 404);
        }

        $title = $data->title;
        $meta_title = $data->meta_title;
        $meta_description = $data->meta_description;
        $content = $data->content;

        return response()->json([
            'title' => $title,
            'meta_title' => $meta_title,
            'meta_description' => $meta_description,
            'content' => $content,
        ]);
    }

/**
 * @OA\Post(
 *     path="/blogs/store",
 *     summary="Create a new blog post",
 *     description="Create a new blog post with the provided data.",
 *     operationId="createBlog",
 *     tags={"Blogs"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Blog post data",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(property="title", type="string", example="Sample Blog Title"),
 *                 @OA\Property(property="meta_title", type="string", example="Sample Meta Title"),
 *                 @OA\Property(property="meta_description", type="string", example="Sample Meta Description"),
 *                 @OA\Property(property="slug", type="string", example="sample-blog-title"),
 *                 @OA\Property(property="image", type="string", format="binary", description="Image file for the blog post"),
 *                 @OA\Property(property="content", type="string", example="Sample blog content..."),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Blog post created successfully",
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity: Validation failed or missing data",
 *     ),
 * )
 *
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\Http\JsonResponse
 */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'meta_title' => 'required|max:255',
            'image' => 'required',
            'meta_description' => 'required|max:255',
            'slug' => 'nullable|max:255|unique:blogs',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $data = new Blog();
        $data->title = $request->input('title');
        $data->meta_title = $request->input('meta_title');
        $data->meta_description = $request->input('meta_description');
        $data->slug = $request->input('slug');
        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/blog_images', $imageName);
            $data->image = 'blog_images/' . $imageName;
        }
        $data->content = $request->input('content');
        $data->save();

        return response()->json(['message'=>'blog created','data' => $data], 201);
    }


    /**
 * @OA\Get(
 *     path="/blogs/edit/{id}",
 *     summary="Edit a blog post",
 *     description="Retrieve a specific blog post for editing by providing its ID.",
 *     operationId="editBlog",
 *     tags={"Blogs"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the blog post to edit",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Blog post retrieved successfully for editing",
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Blog post not found",
 *     ),
 * )
 *
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */

    public function edit($id)
    {
        $data=Blog::find($id);
        return response()->json(['data'=>$data]);
    }



    /**
 * @OA\Post(
 *     path="/blogs/{id}",
 *     summary="Update a blog post",
 *     description="Update a specific blog post by providing its ID and data to be updated.",
 *     operationId="updateBlog",
 *     tags={"Blogs"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the blog post to update",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Updated blog post data",
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(property="title", type="string", example="Updated Blog Title"),
 *                 @OA\Property(property="meta_title", type="string", example="Updated Meta Title"),
 *                 @OA\Property(property="meta_description", type="string", example="Updated Meta Description"),
 *                 @OA\Property(property="slug", type="string", example="updated-blog-title"),
 *                 @OA\Property(property="content", type="string", example="Updated blog content..."),
 *                 @OA\Property(property="image", type="string", format="binary", description="Updated image file for the blog post"),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Blog post updated successfully",
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation failed or missing data",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="object", example="Validation error or missing data"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Blog post not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Blog not found"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Internal Server Error"),
 *         )
 *     ),
 * )
 *
 * @param \Illuminate\Http\Request $request
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'string|max:255',
            'meta_title' => 'string|max:255',
            'meta_description' => 'string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error', 400);
        }

        try {
            $data = Blog::findOrFail($id);

            foreach ($request->all() as $key => $value) {
                if (array_key_exists($key, $rules)) {
                    if (!is_null($value) && $value !== '') {
                        if ($key === 'image') {
                            $imageName = $request->file('image')->getClientOriginalName();
                            $request->file('image')->storeAs('public/blog_images', $imageName);
                            $data->image_path = 'blog_images/' . $imageName;
                        } else {
                            $data->$key = $value;
                        }
                    }
                }
            }

            $data->save();

            return response()->json(['message' => 'Data updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Blog not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
 * @OA\Delete(
 *     path="/blogs/delete/{id}",
 *     summary="Delete a blog post",
 *     description="Delete a specific blog post by providing its ID.",
 *     operationId="deleteBlog",
 *     tags={"Blogs"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the blog post to delete",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Blog post deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Blog post deleted successfully"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Blog post not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Blog not found"),
 *         )
 *     ),
 * )
 *
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */

    public function destroy($id)
    {
        $blogPost=Blog::findOrFail($id);
        $blogPost->delete();
        return response()->json(['success', 'Blog post deleted successfully']);
    }


/**
 * @OA\Put(
 *     path="/blogs/restore-blog/{id}",
 *     summary="Restore a soft-deleted blog post",
 *     description="Restore a soft-deleted blog post by providing its ID.",
 *     operationId="restoreBlog",
 *     tags={"Blogs"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the soft-deleted blog post to restore",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Blog post restored successfully",
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Blog post not found in soft-deleted records",
 *     ),
 * )
 *
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */

    public function restore_blog($id)
    {
        $data = Blog::onlyTrashed()->find($id);

        if (!$data) {
            return response()->json([
                'message' => 'blog not found in soft-deleted records',
            ], 404);
        }
        $data->restore();

        return response()->json([
            'message' => 'Blogs has been restored',
            'user' => $data,
        ], 200);
    }

}
