<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = BlogPost::published()
            ->with(['author', 'category'])
            ->orderByDesc('published_at');

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->byTag($request->tag);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(12);
        $featuredPosts = BlogPost::published()->featured()->limit(3)->get();
        $categories = BlogCategory::active()->ordered()->withCount('publishedPosts')->get();
        $popularTags = $this->getPopularTags();

        return view('pages.blog.index', compact('posts', 'featuredPosts', 'categories', 'popularTags'));
    }

    public function show(BlogPost $post): View
    {
        if (!$post->isPublished()) {
            abort(404);
        }

        $post->load(['author', 'category', 'approvedComments.replies', 'approvedComments.user']);
        $post->incrementViews();

        $relatedPosts = $post->getRelatedPosts(3);
        $categories = BlogCategory::active()->ordered()->withCount('publishedPosts')->get();

        return view('pages.blog.show', compact('post', 'relatedPosts', 'categories'));
    }

    public function category(BlogCategory $category): View
    {
        $posts = BlogPost::published()
            ->where('blog_category_id', $category->id)
            ->with(['author', 'category'])
            ->orderByDesc('published_at')
            ->paginate(12);

        $categories = BlogCategory::active()->ordered()->withCount('publishedPosts')->get();
        $popularTags = $this->getPopularTags();

        return view('pages.blog.category', compact('category', 'posts', 'categories', 'popularTags'));
    }

    public function tag(string $tag): View
    {
        $posts = BlogPost::published()
            ->byTag($tag)
            ->with(['author', 'category'])
            ->orderByDesc('published_at')
            ->paginate(12);

        $categories = BlogCategory::active()->ordered()->withCount('publishedPosts')->get();
        $popularTags = $this->getPopularTags();

        return view('pages.blog.tag', compact('tag', 'posts', 'categories', 'popularTags'));
    }

    public function storeComment(Request $request, BlogPost $post)
    {
        if (!$post->allow_comments) {
            return back()->with('error', 'Comments are disabled for this post.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);

        $comment = new BlogComment([
            'blog_post_id' => $post->id,
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        if (auth()->check()) {
            $comment->user_id = auth()->id();
        } else {
            $request->validate([
                'author_name' => 'required|string|max:255',
                'author_email' => 'required|email|max:255',
            ]);
            $comment->author_name = $request->author_name;
            $comment->author_email = $request->author_email;
        }

        $comment->save();

        return back()->with('success', 'Your comment has been submitted and is awaiting moderation.');
    }

    protected function getPopularTags(int $limit = 10): array
    {
        $posts = BlogPost::published()
            ->whereNotNull('tags')
            ->get(['tags']);

        $tags = [];
        foreach ($posts as $post) {
            if (is_array($post->tags)) {
                foreach ($post->tags as $tag) {
                    $tags[$tag] = ($tags[$tag] ?? 0) + 1;
                }
            }
        }

        arsort($tags);
        return array_slice($tags, 0, $limit, true);
    }
}
