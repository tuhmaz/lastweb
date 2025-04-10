<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Traits\ApiDatabaseTrait;

class NewsController extends Controller
{
    use ApiDatabaseTrait;

    public function index(Request $request, $country)
    {
        try {
            $this->switchDatabase($country);

            $news = News::with(['category', 'comments'])
                ->select('id', 'category_id', 'title', 'slug', 'content', 'meta_description', 'keywords', 
                        'image', 'alt', 'author_id', 'is_active', 'is_featured', 'views', 'country', 
                        'created_at', 'updated_at')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'data' => [
                    'items' => $news->items(),
                    'pagination' => [
                        'current_page' => $news->currentPage(),
                        'last_page' => $news->lastPage(),
                        'per_page' => $news->perPage(),
                        'total' => $news->total()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching news: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $country, $id)
    {
        try {
            $this->switchDatabase($country);

            $news = News::with(['category', 'comments'])
                ->select('id', 'category_id', 'title', 'slug', 'content', 'meta_description', 'keywords', 
                        'image', 'alt', 'author_id', 'is_active', 'is_featured', 'views', 'country', 
                        'created_at', 'updated_at')
                ->findOrFail($id);

            // زيادة عدد المشاهدات
            $news->increment('views');

            return response()->json([
                'status' => true,
                'data' => [
                    'item' => $news
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching news: ' . $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    public function getCategories(Request $request, $country)
    {
        try {
            $this->switchDatabase($country);

            $categories = Category::select('id', 'name', 'slug', 'is_active', 'country', 'created_at', 'updated_at')
                ->where('is_active', true)
                ->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'items' => $categories
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching categories: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getNewsByCategory(Request $request, $country, $categorySlug)
    {
        try {
            $this->switchDatabase($country);

            $category = Category::where('slug', $categorySlug)->firstOrFail();

            $news = News::with(['category', 'comments'])
                ->where('category_id', $category->id)
                ->select('id', 'category_id', 'title', 'slug', 'content', 'meta_description', 'keywords', 
                        'image', 'alt', 'author_id', 'is_active', 'is_featured', 'views', 'country', 
                        'created_at', 'updated_at')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'data' => [
                    'category' => $category,
                    'items' => $news->items(),
                    'pagination' => [
                        'current_page' => $news->currentPage(),
                        'last_page' => $news->lastPage(),
                        'per_page' => $news->perPage(),
                        'total' => $news->total()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching news by category: ' . $e->getMessage()
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    public function addComment(Request $request, $country)
    {
        try {
            $this->switchDatabase($country);

            $request->validate([
                'news_id' => 'required|exists:news,id',
                'content' => 'required|string|min:3'
            ]);

            $comment = $request->user()->comments()->create([
                'news_id' => $request->news_id,
                'content' => $request->content
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Comment added successfully',
                'data' => [
                    'item' => $comment
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error adding comment: ' . $e->getMessage()
            ], 500);
        }
    }
}
