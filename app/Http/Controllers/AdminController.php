<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use App\Models\BlogComment;
use App\Models\BlogView;
use App\Models\BlogReaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function showDashboard()
    {
        // Overview Stats
        $totalBlogs = Blog::where('blog_status', 'published')->count();
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $totalComments = BlogComment::count();
        $totalViews = BlogView::count();

        // Trends (compare with last week)
        $blogsThisWeek = Blog::where('blog_status', 'published')
            ->where('created_at', '>=', now()->subWeek())
            ->count();
        $usersThisWeek = User::where('role', '!=', 'admin')
            ->where('created_at', '>=', now()->subWeek())
            ->count();
        $commentsThisWeek = BlogComment::where('created_at', '>=', now()->subWeek())->count();
        $viewsThisWeek = BlogView::where('created_at', '>=', now()->subWeek())->count();

        // Content Status Breakdown
        $blogsByStatus = [
            'published' => Blog::where('blog_status', 'published')->count(),
            'draft' => Blog::where('blog_status', 'draft')->count(),
            'scheduled' => Blog::where('blog_status', 'scheduled')->count(),
            'trash' => Blog::where('blog_status', 'trash')->count(),
        ];

        // Blog Posts per Month (last 6 months)
        $blogsByMonth = Blog::where('blog_status', 'published')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill in missing months with 0
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthlyData[$month] = 0;
        }
        foreach ($blogsByMonth as $data) {
            $monthlyData[$data->month] = $data->count;
        }

        // Most Popular Blogs (top 5 by views)
        $popularBlogs = Blog::where('blog_status', 'published')
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get(['id', 'blog_title', 'views_count', 'created_at']);

        // Recent Comments (last 5)
        $recentComments = BlogComment::with(['user', 'blog'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Users (last 5 registrations)
        $recentUsers = User::where('role', '!=', 'admin')
            ->latest()
            ->take(5)
            ->get(['id', 'first_name', 'last_name', 'email', 'created_at']);

        // Recent Blogs (last 5)
        $recentBlogs = Blog::where('blog_status', 'published')
            ->with('user')
            ->latest()
            ->take(5)
            ->get(['id', 'blog_title', 'user_id', 'views_count', 'created_at']);

        // Engagement Stats
        $totalLikes = BlogReaction::where('reaction_type', 'like')->count();
        $totalDislikes = BlogReaction::where('reaction_type', 'dislike')->count();
        $averageViewsPerBlog = $totalBlogs > 0 ? round($totalViews / $totalBlogs, 1) : 0;
        $averageCommentsPerBlog = $totalBlogs > 0 ? round($totalComments / $totalBlogs, 1) : 0;

        // Top Tags
        $topTags = Blog::where('blog_status', 'published')
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->filter(fn ($tag) => is_string($tag) && trim($tag) !== '')
            ->map(fn ($tag) => trim($tag))
            ->countBy()
            ->sortDesc()
            ->take(8);

        // User Registrations per Month (last 6 months)
        $usersByMonth = User::where('role', '!=', 'admin')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill in missing months
        $userMonthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $userMonthlyData[$month] = 0;
        }
        foreach ($usersByMonth as $data) {
            $userMonthlyData[$data->month] = $data->count;
        }

        return view('admin_dashboard', compact(
            'totalBlogs',
            'totalUsers',
            'totalComments',
            'totalViews',
            'blogsThisWeek',
            'usersThisWeek',
            'commentsThisWeek',
            'viewsThisWeek',
            'blogsByStatus',
            'monthlyData',
            'popularBlogs',
            'recentComments',
            'recentUsers',
            'recentBlogs',
            'totalLikes',
            'totalDislikes',
            'averageViewsPerBlog',
            'averageCommentsPerBlog',
            'topTags',
            'userMonthlyData'
        ));
    }
}
