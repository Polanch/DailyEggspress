<?php

namespace App\Http\Controllers;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\BlogReaction;
use App\Models\BlogView;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;



class BlogController extends Controller
{
    public function showAdminComments(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 10;

        $query = $this->buildAdminCommentsQuery($search)->latest();
        $total = $query->count();
        $comments = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return view('admin_comments', [
            'initialComments' => $comments,
            'initialQuery' => $search,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
        ]);
    }

    public function searchAdminComments(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
        ]);

        $search = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 10;

        $query = $this->buildAdminCommentsQuery($search)->latest();
        $total = $query->count();
        $comments = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function (BlogComment $comment) {
                $authorName = trim((string) (($comment->user?->first_name ?? '') . ' ' . ($comment->user?->last_name ?? '')));
                if ($authorName === '') {
                    $authorName = $comment->user?->username ?? 'Unknown user';
                }

                return [
                    'id' => $comment->id,
                    'author' => $authorName,
                    'blog_title' => $comment->blog?->blog_title ?? 'Untitled blog',
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at?->format('M d, Y h:i A') ?? '',
                    'created_at_short' => $comment->created_at?->format('M d, Y') ?? '',
                    'user_role' => $comment->user?->role ?? 'user',
                    'profile_picture' => $comment->user?->profile_picture ?? null,
                ];
            })
            ->values();

        return response()->json([
            'comments' => $comments,
            'total' => $total,
            'currentPage' => $page,
            'perPage' => $perPage,
        ]);
    }

    public function banCommentUser($id)
    {
        $comment = BlogComment::with(['user', 'blog'])->findOrFail($id);

        if ((int) $comment->blog->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $targetUser = $comment->user;
        if (!$targetUser) {
            return redirect()->back()->with('error', 'Cannot ban this user.');
        }

        if ($targetUser->role === 'admin') {
            return redirect()->back()->with('error', 'Admin account cannot be banned.');
        }

        $targetUser->update([
            'role' => 'banned',
            'banned_comment_id' => $comment->id,
            'banned_comment_text' => $comment->comment,
            'appeal_message' => null,
            'appealed_at' => null,
        ]);

        return redirect()->back()->with('success', 'User has been banned.');
    }

    public function deleteAdminComment($id)
    {
        $comment = BlogComment::with('blog')->findOrFail($id);

        if ((int) $comment->blog->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }

    private function buildAdminCommentsQuery(string $search)
    {
        return BlogComment::query()
            ->with([
                'user:id,first_name,last_name,username,role',
                'blog:id,user_id,blog_title',
            ])
            ->whereNull('parent_id')
            ->whereHas('blog', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('comment', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%')
                                ->orWhere('username', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('blog', function ($blogQuery) use ($search) {
                            $blogQuery->where('blog_title', 'like', '%' . $search . '%');
                        });
                });
            });
    }

    public function showAdminPosts()
    {
        $blogs = Blog::where('user_id', Auth::id())
            ->whereIn('blog_status', ['published', 'scheduled'])
            ->withCount([
                'reactions as likes_count' => function ($query) {
                    $query->where('reaction_type', 'like');
                },
                'reactions as dislikes_count' => function ($query) {
                    $query->where('reaction_type', 'dislike');
                },
                'comments as comments_count' => function ($query) {
                    $query->whereNull('parent_id');
                },
            ])
            ->orderByRaw("CASE WHEN blog_status = 'scheduled' THEN 0 ELSE 1 END")
            ->orderByDesc('scheduled_at')
            ->orderByDesc('created_at')
            ->get();

        $scheduledBlogs = $blogs->where('blog_status', 'scheduled')->values();
        $publishedBlogs = $blogs->where('blog_status', 'published')->values();

        return view('admin_posts', compact('scheduledBlogs', 'publishedBlogs'));
    }

    // Show all blogs in workspace
    public function index()
    {
        $blogs = Blog::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();
        return view('admin_workspace', compact('blogs'));
    }

    public function store(Request $request)
    {
        // Sanitize tags: keep only non-empty strings
        $tags = array_filter(
            $request->input('tags', []),
            fn($tag) => is_string($tag) && trim($tag) !== ''
        );

        $request->merge(['tags' => array_values($tags)]); // reindex for validation

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '.webp';
            $thumbnailDir = storage_path('app/public/thumbnails');
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0777, true);
            }
            $image = Image::read($file);
            $image->toWebp(90)->save($thumbnailDir . '/' . $filename);
            $thumbnailPath = 'storage/thumbnails/' . $filename;
        }

        $action = $request->input('action');
        $status = $action === 'Published' ? 'published' : ($action === 'Scheduled' ? 'scheduled' : 'draft');
        $scheduledAt = $action === 'Scheduled' ? $request->input('scheduled_at') : null;
        
        $blog = Blog::create([
            'user_id' => Auth::id(),
            'thumbnail' => $thumbnailPath,
            'tags' => $request->input('tags', []),
            'blog_title' => $request->input('title'),
            'blog_content' => $request->input('content'),
            'blog_status' => $status,
            'scheduled_at' => $scheduledAt,
        ]);

        $message = $status === 'draft' ? 'Draft Saved Successfully' : ($status === 'scheduled' ? 'Blog scheduled successfully!' : 'Blog created successfully!');
        return redirect('/admin/workspace/create')->with('success', $message);
    }
    public function showHome()
    {
        // Redirect logged-in users to their dashboard
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin' || $user->role === 'moderator') {
                return redirect('/admin/dashboard');
            }
            return redirect('/user/dashboard');
        }

        $latestBlog = Blog::where('blog_status', 'published')->with('user')->latest()->first();

        if (!$latestBlog) {
            return view('main', [
                'latestBlog' => null,
                'weeklyBlogs' => collect(),
                'popularBlogs' => collect(),
                'randomBlogs' => collect(),
                'tags' => collect(),
                'mostLikedBlog' => null,
                'comments' => collect(),
                'likeCount' => 0,
                'dislikeCount' => 0,
                'userReaction' => null,
            ]);
        }

        $this->recordBlogView($latestBlog);

        $weeklyBlogs = Blog::where('blog_status', 'published')
            ->when($latestBlog, function ($query) use ($latestBlog) {
                $query->where('id', '!=', $latestBlog->id);
            })
            ->with('user')
            ->latest()
            ->take(4)
            ->get();

        $popularBlogs = Blog::where('blog_status', 'published')
            ->where('id', '!=', $latestBlog->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($blog) {
                $blog->likeCount = BlogReaction::where('blog_id', $blog->id)
                    ->where('reaction_type', 'like')
                    ->count();
                $blog->dislikeCount = BlogReaction::where('blog_id', $blog->id)
                    ->where('reaction_type', 'dislike')
                    ->count();
                $blog->commentCount = BlogComment::where('blog_id', $blog->id)
                    ->whereNull('parent_id')
                    ->count();
                return $blog;
            });

        $randomBlogs = Blog::where('blog_status', 'published')
            ->where('id', '!=', $latestBlog->id)
            ->inRandomOrder()
            ->take(4)
            ->get()
            ->map(function ($blog) {
                $blog->likeCount = BlogReaction::where('blog_id', $blog->id)
                    ->where('reaction_type', 'like')
                    ->count();
                $blog->dislikeCount = BlogReaction::where('blog_id', $blog->id)
                    ->where('reaction_type', 'dislike')
                    ->count();
                $blog->commentCount = BlogComment::where('blog_id', $blog->id)
                    ->whereNull('parent_id')
                    ->count();
                return $blog;
            });

        // Get all tags from published blogs and count their usage
        $allBlogs = Blog::where('blog_status', 'published')->get();
        $tagsCount = [];
        foreach ($allBlogs as $blog) {
            if (is_array($blog->tags)) {
                foreach ($blog->tags as $tag) {
                    if (isset($tagsCount[$tag])) {
                        $tagsCount[$tag]++;
                    } else {
                        $tagsCount[$tag] = 1;
                    }
                }
            }
        }
        // Sort by most used (descending)
        arsort($tagsCount);
        $tags = array_keys($tagsCount);

        // Get the most liked blog for big-blog-info section
        $mostLikedBlog = Blog::where('blog_status', 'published')
            ->with('user')
            ->get()
            ->map(function ($b) {
                $b->likeCount = BlogReaction::where('blog_id', $b->id)
                    ->where('reaction_type', 'like')
                    ->count();
                return $b;
            })
            ->sortByDesc('likeCount')
            ->first();

        // Get comments for the latest blog
        $comments = BlogComment::where('blog_id', $latestBlog->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        // Get reaction counts for the latest blog
        $likeCount = BlogReaction::where('blog_id', $latestBlog->id)
            ->where('reaction_type', 'like')
            ->count();

        $dislikeCount = BlogReaction::where('blog_id', $latestBlog->id)
            ->where('reaction_type', 'dislike')
            ->count();

        // Get user's reaction if logged in
        $userReaction = null;
        if (Auth::check()) {
            $reaction = BlogReaction::where('blog_id', $latestBlog->id)
                ->where('user_id', Auth::id())
                ->first();
            $userReaction = $reaction ? $reaction->reaction_type : null;
        }

        return view('main', compact('latestBlog', 'weeklyBlogs', 'popularBlogs', 'randomBlogs', 'tags', 'mostLikedBlog', 'comments', 'likeCount', 'dislikeCount', 'userReaction'));
    }

    public function showPublicBlog($id)
    {
        $blog = Blog::where('blog_status', 'published')
            ->with('user')
            ->findOrFail($id);

        $this->recordBlogView($blog);

        // Get the most liked blog for big-blog-info section
        $mostLikedBlog = Blog::where('blog_status', 'published')
            ->with('user')
            ->get()
            ->map(function ($b) {
                $b->likeCount = BlogReaction::where('blog_id', $b->id)
                    ->where('reaction_type', 'like')
                    ->count();
                return $b;
            })
            ->sortByDesc('likeCount')
            ->first();

        $popularBlogs = Blog::where('blog_status', 'published')
            ->where('id', '!=', $blog->id)
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($b) {
                $b->likeCount = BlogReaction::where('blog_id', $b->id)
                    ->where('reaction_type', 'like')
                    ->count();
                $b->dislikeCount = BlogReaction::where('blog_id', $b->id)
                    ->where('reaction_type', 'dislike')
                    ->count();
                $b->commentCount = BlogComment::where('blog_id', $b->id)
                    ->whereNull('parent_id')
                    ->count();
                return $b;
            });

        $randomBlogs = Blog::where('blog_status', 'published')
            ->where('id', '!=', $blog->id)
            ->inRandomOrder()
            ->take(4)
            ->get()
            ->map(function ($b) {
                $b->likeCount = BlogReaction::where('blog_id', $b->id)
                    ->where('reaction_type', 'like')
                    ->count();
                $b->dislikeCount = BlogReaction::where('blog_id', $b->id)
                    ->where('reaction_type', 'dislike')
                    ->count();
                $b->commentCount = BlogComment::where('blog_id', $b->id)
                    ->whereNull('parent_id')
                    ->count();
                return $b;
            });

        $tags = Blog::where('blog_status', 'published')
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->filter(fn ($tag) => is_string($tag) && trim($tag) !== '')
            ->map(fn ($tag) => trim($tag))
            ->unique()
            ->values()
            ->take(24);

        $comments = BlogComment::where('blog_id', $blog->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        $likeCount = BlogReaction::where('blog_id', $blog->id)
            ->where('reaction_type', 'like')
            ->count();

        $dislikeCount = BlogReaction::where('blog_id', $blog->id)
            ->where('reaction_type', 'dislike')
            ->count();

        return view('blog', compact('blog', 'mostLikedBlog', 'popularBlogs', 'randomBlogs', 'tags', 'comments', 'likeCount', 'dislikeCount'));
    }

    public function showUserHome()
    {
        $latestBlog = Blog::where('blog_status', 'published')->latest()->first();

        if (!$latestBlog) {
            return view('user_dashboard', [
                'blog' => null,
                'popularBlogs' => collect(),
                'randomBlogs' => collect(),
                'tags' => collect(),
                'comments' => collect(),
                'likeCount' => 0,
                'dislikeCount' => 0,
                'userReaction' => null,
            ]);
        }

        return redirect()->route('user.blog.view', $latestBlog->id);
    }

    public function showUserBlog($id)
    {
        $blog = Blog::where('blog_status', 'published')
            ->with('user')
            ->findOrFail($id);

        $this->recordBlogView($blog);

        // Get the most liked blog for big-blog-info section
        $mostLikedBlog = Blog::where('blog_status', 'published')
            ->with('user')
            ->get()
            ->map(function ($b) {
                $b->likeCount = BlogReaction::where('blog_id', $b->id)
                    ->where('reaction_type', 'like')
                    ->count();
                return $b;
            })
            ->sortByDesc('likeCount')
            ->first();

        $popularBlogs = Blog::where('blog_status', 'published')
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($b) {
                $b->likeCount = BlogReaction::where('blog_id', $b->id)
                    ->where('reaction_type', 'like')
                    ->count();
                $b->dislikeCount = BlogReaction::where('blog_id', $b->id)
                    ->where('reaction_type', 'dislike')
                    ->count();
                $b->commentCount = BlogComment::where('blog_id', $b->id)
                    ->whereNull('parent_id')
                    ->count();
                return $b;
            });

        $randomBlogs = Blog::where('blog_status', 'published')
            ->where('id', '!=', $blog->id)
            ->inRandomOrder()
            ->take(4)
            ->get()
            ->map(function ($b) {
                $b->likeCount = BlogReaction::where('blog_id', $b->id)
                    ->where('reaction_type', 'like')
                    ->count();
                $b->dislikeCount = BlogReaction::where('blog_id', $b->id)
                    ->where('reaction_type', 'dislike')
                    ->count();
                $b->commentCount = BlogComment::where('blog_id', $b->id)
                    ->whereNull('parent_id')
                    ->count();
                return $b;
            });

        $tags = Blog::where('blog_status', 'published')
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->filter(fn ($tag) => is_string($tag) && trim($tag) !== '')
            ->map(fn ($tag) => trim($tag))
            ->unique()
            ->values()
            ->take(24);

        $comments = BlogComment::where('blog_id', $blog->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        $likeCount = BlogReaction::where('blog_id', $blog->id)
            ->where('reaction_type', 'like')
            ->count();

        $dislikeCount = BlogReaction::where('blog_id', $blog->id)
            ->where('reaction_type', 'dislike')
            ->count();

        $userReaction = BlogReaction::where('blog_id', $blog->id)
            ->where('user_id', Auth::id())
            ->value('reaction_type');

        return view('user_dashboard', compact(
            'blog',
            'mostLikedBlog',
            'popularBlogs',
            'randomBlogs',
            'tags',
            'comments',
            'likeCount',
            'dislikeCount',
            'userReaction'
        ));
    }

    private function recordBlogView(Blog $blog): void
    {
        $windowStart = now()->subDay();
        $userId = Auth::id();

        if ($userId) {
            $alreadyViewed = BlogView::where('blog_id', $blog->id)
                ->where('user_id', $userId)
                ->where('viewed_at', '>=', $windowStart)
                ->exists();

            if ($alreadyViewed) {
                return;
            }

            BlogView::create([
                'blog_id' => $blog->id,
                'user_id' => $userId,
                'viewed_at' => now(),
            ]);

            $blog->increment('views_count');
            $blog->refresh();

            return;
        }

        $guestToken = session('guest_view_token');
        if (!$guestToken) {
            $guestToken = (string) Str::uuid();
            session(['guest_view_token' => $guestToken]);
        }

        $alreadyViewed = BlogView::where('blog_id', $blog->id)
            ->where('guest_token', $guestToken)
            ->where('viewed_at', '>=', $windowStart)
            ->exists();

        if ($alreadyViewed) {
            return;
        }

        BlogView::create([
            'blog_id' => $blog->id,
            'guest_token' => $guestToken,
            'viewed_at' => now(),
        ]);

        $blog->increment('views_count');
        $blog->refresh();
    }

    public function showTagBlogs($tag)
    {
        $blogs = Blog::where('blog_status', 'published')
            ->get()
            ->filter(function ($blog) use ($tag) {
                return is_array($blog->tags) && in_array($tag, $blog->tags);
            })
            ->values()
            ->map(function ($blog) {
                $blog->likeCount = BlogReaction::where('blog_id', $blog->id)
                    ->where('reaction_type', 'like')
                    ->count();
                $blog->dislikeCount = BlogReaction::where('blog_id', $blog->id)
                    ->where('reaction_type', 'dislike')
                    ->count();
                $blog->commentCount = BlogComment::where('blog_id', $blog->id)
                    ->whereNull('parent_id')
                    ->count();
                return $blog;
            });

        $topBlog = $blogs->sortByDesc('likeCount')->first();

        // Initialize default values for comments and reactions
        $comments = collect();
        $likeCount = 0;
        $dislikeCount = 0;
        $userReaction = null;

        // If there's a topBlog, fetch its comments and reactions
        if ($topBlog) {
            $comments = BlogComment::where('blog_id', $topBlog->id)
                ->whereNull('parent_id')
                ->with(['user', 'replies.user'])
                ->latest()
                ->get();

            $likeCount = BlogReaction::where('blog_id', $topBlog->id)
                ->where('reaction_type', 'like')
                ->count();

            $dislikeCount = BlogReaction::where('blog_id', $topBlog->id)
                ->where('reaction_type', 'dislike')
                ->count();

            if (Auth::check()) {
                $userReaction = BlogReaction::where('blog_id', $topBlog->id)
                    ->where('user_id', Auth::id())
                    ->value('reaction_type');
            }
        }

        // Get similar blogs - blogs that share tags with any blog in this collection
        $allTagsInCollection = [];
        foreach ($blogs as $blog) {
            if (is_array($blog->tags)) {
                $allTagsInCollection = array_merge($allTagsInCollection, $blog->tags);
            }
        }
        $allTagsInCollection = array_unique($allTagsInCollection);

        $similarBlogs = Blog::where('blog_status', 'published')
            ->get()
            ->filter(function ($blog) use ($allTagsInCollection, $tag, $topBlog) {
                if (!is_array($blog->tags)) return false;
                // Exclude the top blog from similar blogs
                if ($topBlog && $blog->id === $topBlog->id) return false;
                // Check if blog has any tags from our collection, but isn't just the main tag
                foreach ($blog->tags as $blogTag) {
                    if (in_array($blogTag, $allTagsInCollection) && $blogTag !== $tag) {
                        return true;
                    }
                }
                return false;
            })
            ->map(function ($blog) {
                $blog->likeCount = BlogReaction::where('blog_id', $blog->id)
                    ->where('reaction_type', 'like')
                    ->count();
                $blog->dislikeCount = BlogReaction::where('blog_id', $blog->id)
                    ->where('reaction_type', 'dislike')
                    ->count();
                $blog->commentCount = BlogComment::where('blog_id', $blog->id)
                    ->whereNull('parent_id')
                    ->count();
                return $blog;
            })
            ->sortByDesc('views_count')
            ->take(5);

        // Get more blogs - least viewed blogs
        $moreBlogs = Blog::where('blog_status', 'published')
            ->get()
            ->filter(function ($blog) use ($topBlog) {
                // Exclude the top blog from more blogs
                return !$topBlog || $blog->id !== $topBlog->id;
            })
            ->map(function ($blog) {
                $blog->likeCount = BlogReaction::where('blog_id', $blog->id)
                    ->where('reaction_type', 'like')
                    ->count();
                $blog->dislikeCount = BlogReaction::where('blog_id', $blog->id)
                    ->where('reaction_type', 'dislike')
                    ->count();
                $blog->commentCount = BlogComment::where('blog_id', $blog->id)
                    ->whereNull('parent_id')
                    ->count();
                return $blog;
            })
            ->sortBy('views_count')
            ->take(5);

        // Get all unique tags from blogs collection
        $allTags = [];
        foreach ($blogs as $blog) {
            if (is_array($blog->tags)) {
                $allTags = array_merge($allTags, $blog->tags);
            }
        }
        $tags = array_unique($allTags);

        // Check if user is authenticated and use appropriate view
        if (Auth::check()) {
            return view('tag_blogs_user', compact('blogs', 'tag', 'topBlog', 'similarBlogs', 'moreBlogs', 'tags', 'comments', 'likeCount', 'dislikeCount', 'userReaction'));
        }
        
        return view('tag_blogs', compact('blogs', 'tag', 'topBlog', 'similarBlogs', 'moreBlogs', 'tags', 'comments', 'likeCount', 'dislikeCount'));
    }

    public function saveReaction(Request $request, $id)
    {
        $request->validate([
            'reaction_type' => 'required|in:like,dislike',
        ]);

        $blog = Blog::where('blog_status', 'published')->findOrFail($id);

        $existingReaction = BlogReaction::where('blog_id', $blog->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReaction && $existingReaction->reaction_type === $request->input('reaction_type')) {
            $existingReaction->delete();
            $userReaction = null;
        } else {
            BlogReaction::updateOrCreate(
                [
                    'blog_id' => $blog->id,
                    'user_id' => Auth::id(),
                ],
                [
                    'reaction_type' => $request->input('reaction_type'),
                ]
            );
            $userReaction = $request->input('reaction_type');
        }

        $likeCount = BlogReaction::where('blog_id', $blog->id)
            ->where('reaction_type', 'like')
            ->count();

        $dislikeCount = BlogReaction::where('blog_id', $blog->id)
            ->where('reaction_type', 'dislike')
            ->count();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'userReaction' => $userReaction,
                'likeCount' => $likeCount,
                'dislikeCount' => $dislikeCount,
            ]);
        }

        return redirect()->route('user.blog.view', $blog->id);
    }

    public function saveComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
                    'parent_id' => 'nullable|integer|exists:blog_comments,id',
        ]);

        $blog = Blog::where('blog_status', 'published')->findOrFail($id);

        $commentText = $request->input('comment');
        
        BlogComment::create([
            'blog_id' => $blog->id,
            'user_id' => Auth::id(),
            'comment' => $commentText,
                    'parent_id' => $request->input('parent_id'),
        ]);

        return redirect()->route('user.blog.view', $blog->id)
            ->with('success', 'Your comment has been posted successfully!');
    }
    // AJAX image upload for editor
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|max:5120', // max 5MB
            ]);

            $file = $request->file('image');
            $filename = uniqid('blog_', true) . '.webp';
            $galleryDir = storage_path('app/public/blog_gallery');
            
            if (!file_exists($galleryDir)) {
                mkdir($galleryDir, 0777, true);
            }
            
            $image = Image::read($file);
            $image->toWebp(90);
            $image->save($galleryDir . '/' . $filename);
            
            $url = asset('storage/blog_gallery/' . $filename);

            return response()->json(['url' => $url, 'filename' => $filename]);
        } catch (\Exception $e) {
            Log::error('Image upload error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
    public function removeImage(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);
        $filePath = 'blog_gallery/' . $request->input('filename');
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'File not found.'], 404);
    }
    // Permanently delete a draft
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        if ($blog->user_id !== Auth::id()){
            abort(403, 'Unauthorized action.');
        }

        // Delete thumbnail
        $thumbnailPath = public_path($blog->thumbnail);
        if ($blog->thumbnail && file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }

        // Delete editor images from HTML content
        $content = $blog->blog_content;
        // Use regex to find all image src paths
        preg_match_all('/<img[^>]+src="([^">]+)"/i', $content, $matches);
        foreach ($matches[1] as $imgPath) {
            // Only delete if path is in blog_gallery
            if (strpos($imgPath, 'storage/blog_gallery/') !== false) {
                $filePath = public_path(str_replace(asset(''), '', $imgPath));
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $blog->delete();
        return redirect()->back()->with('success', 'Blog permanently deleted.');
    }

    public function moveToTrash($id)
    {
        $blog = Blog::findOrFail($id);
        if ($blog->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $blog->blog_status = 'trash';
        $blog->save();
        return redirect()->back()->with('success', 'Blog moved to trash.');
    }

    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $blog = Blog::findOrFail($id);
        if ($blog->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($blog->blog_status !== 'scheduled') {
            return redirect()->back()->with('error', 'Only scheduled blogs can be rescheduled.');
        }

        $blog->scheduled_at = $request->input('scheduled_at');
        $blog->save();

        return redirect()->route('admin.posts', ['tab' => 'scheduled'])->with('success', 'Blog rescheduled successfully.');
    }
    // Show drafts and scheduled posts for the authenticated user
    public function showDrafts()
    {
        $drafts = Blog::whereIn('blog_status', ['draft', 'scheduled'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();
        return view('admin_workspace_createblog', compact('drafts'));
    }

    // Get a single blog's data as JSON (for edit form population)
    public function show($id)
    {
        $blog = Blog::findOrFail($id);
        if ($blog->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return response()->json([
            'id' => $blog->id,
            'title' => $blog->blog_title,
            'content' => $blog->blog_content,
            'thumbnail' => $blog->thumbnail ? asset($blog->thumbnail) : null,
            'tags' => $blog->tags ?? [],
            'status' => $blog->blog_status,
            'scheduled_at' => $blog->scheduled_at ? $blog->scheduled_at->format('Y-m-d\TH:i') : null,
        ]);
    }

    // Update an existing blog (edit mode)
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        if ($blog->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Sanitize tags: keep only non-empty strings
        $tags = array_filter(
            $request->input('tags', []),
            fn($tag) => is_string($tag) && trim($tag) !== ''
        );
        $request->merge(['tags' => array_values($tags)]);

        // Only validate scheduled_at as 'after:now' if it's being changed
        $validationRules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ];
        
        // If scheduled_at is provided and different from current, validate it's in the future
        if ($request->has('scheduled_at') && $request->input('scheduled_at')) {
            $newScheduledAt = $request->input('scheduled_at');
            $currentScheduledAt = $blog->scheduled_at ? $blog->scheduled_at->format('Y-m-d\TH:i') : null;
            if ($newScheduledAt !== $currentScheduledAt) {
                $validationRules['scheduled_at'] = 'date|after:now';
            }
        }

        $request->validate($validationRules);

        $thumbnailPath = $blog->thumbnail;
        
        // Handle new thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if a new one is being uploaded
            $oldThumbnailPath = $request->input('old_thumbnail');
            if ($oldThumbnailPath) {
                $fullOldPath = public_path($oldThumbnailPath);
                if (file_exists($fullOldPath)) {
                    unlink($fullOldPath);
                }
            }
            
            // Create new thumbnail
            $file = $request->file('thumbnail');
            $filename = time() . '.webp';
            $thumbnailDir = storage_path('app/public/thumbnails');
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0777, true);
            }
            $image = Image::read($file);
            $image->toWebp(90)->save($thumbnailDir . '/' . $filename);
            $thumbnailPath = 'storage/thumbnails/' . $filename;
        }

        $action = $request->input('action');
        $newStatus = $action === 'Published' ? 'published' : ($action === 'Scheduled' ? 'scheduled' : 'draft');
        $scheduledAt = $action === 'Scheduled' ? $request->input('scheduled_at') : null;
        
        $blog->update([
            'thumbnail' => $thumbnailPath,
            'tags' => $request->input('tags', []),
            'blog_title' => $request->input('title'),
            'blog_content' => $request->input('content'),
            'blog_status' => $newStatus,
            'scheduled_at' => $scheduledAt,
        ]);

        if ($action === 'Published') {
            $message = 'Blog published successfully!';
        } elseif ($action === 'Scheduled') {
            $message = 'Blog scheduled successfully!';
        } else {
            $message = 'Draft updated successfully!';
        }
        
        return redirect('/admin/workspace/create')->with('success', $message);
    }

    public function showAdminUsers(Request $request)
    {
        $users = $this->buildAdminUsersQuery()->paginate(12);
        
        // Check online status for each user
        foreach ($users as $user) {
            $user->is_online = $this->isUserOnline($user->id);
        }
        
        return view('admin_users', compact('users'));
    }

    public function searchAdminUsers(Request $request)
    {
        $search = $request->input('search', '');
        $ageFilter = $request->input('age', '');
        $activeFilter = $request->input('active', '');
        $sort = $request->input('sort', 'newest');
        
        $query = $this->buildAdminUsersQuery($sort);
        
        // Search by name, username, or email
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by age
        if ($ageFilter) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) = ?', [$ageFilter]);
        }
        
        $users = $query->paginate(12);
        
        // Check online status and apply active filter
        $filteredUsers = collect();
        foreach ($users as $user) {
            $user->is_online = $this->isUserOnline($user->id);
            
            // Apply active filter if set
            if ($activeFilter === 'online' && !$user->is_online) {
                continue;
            }
            if ($activeFilter === 'offline' && $user->is_online) {
                continue;
            }
            
            $filteredUsers->push($user);
        }
        
        return response()->json([
            'users' => $filteredUsers->map(function($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'profile_picture' => $user->profile_picture,
                    'role' => $user->role,
                    'birthday' => $user->birthday,
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never',
                    'comments_count' => $user->blog_comments_count,
                    'is_online' => $user->is_online,
                    'banned_comment_id' => $user->banned_comment_id,
                    'email_verified_at' => $user->email_verified_at ? true : false,
                ];
            }),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'total' => $users->total(),
        ]);
    }

    private function buildAdminUsersQuery($sort = 'newest')
    {
        $query = User::withCount('blogComments');
        
        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name':
                $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
                break;
            case 'comments':
                $query->orderByDesc('blog_comments_count');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        return $query;
    }

    private function isUserOnline($userId)
    {
        // Check if user has an active session in the last 5 minutes
        $fiveMinutesAgo = now()->subMinutes(5)->timestamp;
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '>=', $fiveMinutesAgo)
            ->exists();
    }

    public function showUserAppeal($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role !== 'banned') {
            return redirect()->route('admin.users')->with('error', 'This user is not banned.');
        }
        
        $bannedComment = null;
        if ($user->banned_comment_id) {
            $bannedComment = BlogComment::with(['blog', 'user'])->find($user->banned_comment_id);
        }
        
        $appealMessage = $user->appeal_message;
        $appealedAt = $user->appealed_at;
        
        return view('admin_users_appeal', compact('user', 'bannedComment', 'appealMessage', 'appealedAt'));
    }

    public function banUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot ban an admin account.');
        }
        
        if ($user->role === 'banned') {
            return redirect()->back()->with('error', 'User is already banned.');
        }
        
        $user->update([
            'role' => 'banned',
            'banned_comment_id' => null,
            'banned_comment_text' => 'Banned by admin (direct action)',
            'appeal_message' => null,
            'appealed_at' => null,
        ]);
        
        return redirect()->back()->with('success', 'User has been banned successfully.');
    }

    public function unbanUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot unban an admin account.');
        }

        if ($user->role !== 'banned') {
            return redirect()->back()->with('error', 'This user is not banned.');
        }

        $user->update([
            'role' => 'user',
            'banned_comment_id' => null,
            'banned_comment_text' => null,
            'appeal_message' => null,
            'appealed_at' => null,
        ]);

        return redirect()->back()->with('success', 'User has been unbanned successfully.');
    }

    public function deleteUser($id)
    {
        // Only admins can delete users
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized: Only administrators can delete users.');
        }
        
        $user = User::findOrFail($id);
        
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot delete an admin account.');
        }
        
        if ((int) $id === (int) Auth::id()) {
            return redirect()->back()->with('error', 'Cannot delete your own account.');
        }
        
        $username = $user->username;
        $user->delete();
        
        return redirect()->route('admin.users')->with('success', "User '{$username}' has been permanently deleted.");
    }

    public function toggleModeratorRole($id)
    {
        // Only admins can promote/demote users
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized: Only administrators can manage moderator roles.');
        }
        
        $user = User::findOrFail($id);
        
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot modify an admin account role.');
        }
        
        if ($user->role === 'banned') {
            return redirect()->back()->with('error', 'Cannot promote a banned user. Please unban them first.');
        }
        
        if ((int) $id === (int) Auth::id()) {
            return redirect()->back()->with('error', 'Cannot modify your own role.');
        }
        
        // Toggle between moderator and user
        if ($user->role === 'moderator') {
            $user->update(['role' => 'user']);
            return redirect()->back()->with('success', "User '{$user->username}' has been demoted to user.");
        } else {
            $user->update(['role' => 'moderator']);
            return redirect()->back()->with('success', "User '{$user->username}' has been promoted to moderator.");
        }
    }

    public function showAdminTrash()
    {
        $trashBlogs = Blog::where('blog_status', 'trash')
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->paginate(12);

        return view('admin_trash', [
            'trashBlogs' => $trashBlogs,
        ]);
    }

    public function searchAdminTrash(Request $request)
    {
        $search = $request->input('search', '');
        $sort = $request->input('sort', 'newest');

        $query = Blog::where('blog_status', 'trash')->with('user');

        // Search by title or author name
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('blog_title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($subQ) use ($search) {
                      $subQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%")
                           ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $query->orderBy('updated_at', 'asc');
                break;
            case 'oldest_created':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title':
                $query->orderBy('blog_title', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('updated_at', 'desc');
                break;
        }

        $blogs = $query->paginate(12);

        $formattedBlogs = $blogs->getCollection()->map(function(Blog $blog) {
            $authorName = trim((string) (($blog->user?->first_name ?? '') . ' ' . ($blog->user?->last_name ?? '')));
            if ($authorName === '') {
                $authorName = '@' . ($blog->user?->username ?? 'Unknown');
            }

            return [
                'id' => $blog->id,
                'blog_title' => $blog->blog_title,
                'author' => $authorName,
                'created_at' => $blog->created_at?->format('M d, Y h:i A') ?? '',
                'updated_at' => $blog->updated_at?->format('M d, Y h:i A') ?? '',
                'thumbnail' => $blog->thumbnail ? asset($blog->thumbnail) : asset('images/empty.png'),
            ];
        });

        return response()->json([
            'blogs' => $formattedBlogs,
            'current_page' => $blogs->currentPage(),
            'per_page' => $blogs->perPage(),
            'total' => $blogs->total(),
            'last_page' => $blogs->lastPage(),
        ]);
    }

    public function restoreFromTrash($id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->blog_status !== 'trash') {
            return redirect()->back()->with('error', 'This blog is not in trash.');
        }

        $blog->blog_status = 'draft';
        $blog->save();

        return redirect()->back()->with('success', 'Blog restored to drafts.');
    }

    public function deleteTrashBlog($id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->blog_status !== 'trash') {
            return redirect()->back()->with('error', 'This blog is not in trash.');
        }

        // Delete thumbnail
        $thumbnailPath = public_path($blog->thumbnail);
        if ($blog->thumbnail && file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }

        // Delete editor images from HTML content
        $content = $blog->blog_content;
        preg_match_all('/<img[^>]+src="([^">]+)"/i', $content, $matches);
        foreach ($matches[1] as $imgPath) {
            if (strpos($imgPath, 'storage/blog_gallery/') !== false) {
                $filePath = public_path(str_replace(asset(''), '', $imgPath));
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $title = $blog->blog_title;
        $blog->delete();

        return redirect()->back()->with('success', "Blog '{$title}' has been permanently deleted.");
    }

}
