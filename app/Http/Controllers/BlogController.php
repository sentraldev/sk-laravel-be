<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogDetailResource;
use App\Http\Resources\BlogSummaryResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * GET /api/v1/blogs
     * Paginated list of published blogs.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 10;

        $query = Blog::query()
            ->with(['creator:id,name'])
            ->where('is_published', true)
            ->orderByDesc('created_at');

        $paginator = $query->paginate($perPage)->appends($request->query());

        return BlogSummaryResource::collection($paginator);
    }

    /**
     * GET /api/v1/blogs/recent
     * Five most recent published blogs (no pagination).
     */
    public function recent(): AnonymousResourceCollection
    {
        $items = Blog::query()
            ->with(['creator:id,name'])
            ->where('is_published', true)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return BlogSummaryResource::collection($items);
    }

    /**
     * GET /api/v1/blogs/{slug}
     * Blog detail by slug (published only).
     */
    public function detail(string $slug)
    {
        $blog = Blog::query()
            ->with(['creator:id,name'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (! $blog) {
            return response()->json([
                'message' => 'Blog not found',
            ], 404);
        }

        return new BlogDetailResource($blog);
    }
}
