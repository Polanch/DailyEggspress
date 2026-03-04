@extends('layouts.admin_layout')

@section('content')
    <div class="home-header">
        <h1 class="admin-header"><img src="/images/menu1.png" class="admin-h-icn">Dashboard<span class="slash">/</span> <span id="hh">Home</span></h1>
        <h3 class="admin-subheader">Dashboard</h3>
        
        <div class="home-box">
            <img src="/images/home1.png" class="h-icns">
            <p>Total Blogs</p>
            <h2>{{ $totalBlogs }}&nbsp;<span class="add-status">+{{ $blogsThisWeek }} this week</span></h2>
        </div>
        <div class="home-box">
            <img src="/images/home4.png" class="h-icns">
            <p>Total Users</p>
            <h2>{{ $totalUsers }}&nbsp;<span class="add-status">+{{ $usersThisWeek }} this week</span></h2>
        </div>
        <div class="home-box">
            <img src="/images/home2.png" class="h-icns">
            <p>Comments</p>
            <h2>{{ $totalComments }}&nbsp;<span class="add-status">+{{ $commentsThisWeek }} this week</span></h2>
        </div>
        <div class="home-box">
            <img src="/images/home3.png" class="h-icns">
            <p>Total Views</p>
            <h2>{{ $totalViews }}&nbsp;<span class="add-status">+{{ $viewsThisWeek }} this week</span></h2>
        </div>
    </div>

    <div class="home-body">
        <div class="dashboard-grid">
            <!-- Blog Posts Chart -->
            <div class="dashboard-card card-large">
                <h3 class="card-title"><img src="/images/d1.png">Blog Posts (Last 6 Months)</h3>
                <div class="chart-container">
                    <div class="bar-chart">
                        @php
                            $maxCount = max(array_values($monthlyData)) ?: 1;
                        @endphp
                        @foreach($monthlyData as $month => $count)
                            <div class="bar-group">
                                <div class="bar-wrapper">
                                    <div class="bar" style="height: {{ $maxCount > 0 ? ($count / $maxCount * 100) : 0 }}%">
                                        <span class="bar-value">{{ $count }}</span>
                                    </div>
                                </div>
                                <span class="bar-label">{{ date('M', strtotime($month . '-01')) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Content Status -->
            <div class="dashboard-card card-tall">
                <h3 class="card-title"><img src="/images/d2.png">Content Status</h3>
                <div class="stats-grid-vertical">
                    <div class="stat-item">
                        <div class="stat-icon published"><img src="/images/d2-1.png"></div>
                        <div class="stat-info">
                            <span class="stat-label">Published</span>
                            <span class="stat-value">{{ $blogsByStatus['published'] }}</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon draft"><img src="/images/d2-2.png"></div>
                        <div class="stat-info">
                            <span class="stat-label">Drafts</span>
                            <span class="stat-value">{{ $blogsByStatus['draft'] }}</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon scheduled"><img src="/images/d2-3.png"></div>
                        <div class="stat-info">
                            <span class="stat-label">Scheduled</span>
                            <span class="stat-value">{{ $blogsByStatus['scheduled'] }}</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon trash"><img src="/images/d2-4.png"></div>
                        <div class="stat-info">
                            <span class="stat-label">Trash</span>
                            <span class="stat-value">{{ $blogsByStatus['trash'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most Popular Blogs -->
            <div class="dashboard-card card-wide">
                <h3 class="card-title"><img src="/images/d3.png">Most Popular Blogs</h3>
                <div class="popular-list">
                    @forelse($popularBlogs as $blog)
                        <div class="popular-item">
                            <div class="popular-info">
                                <span class="popular-title">{{ Str::limit($blog->blog_title, 35) }}</span>
                                <span class="popular-date">{{ $blog->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="popular-stats">
                                <span class="view-badge"><img src="">{{ $blog->views_count }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="no-data">No blogs yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Engagement Stats -->
            <div class="dashboard-card card-medium">
                <h3 class="card-title"><img src="/images/d4.png">Engagement Stats</h3>
                <div class="engagement-grid-compact">
                    <div class="engagement-item-compact">
                        <div class="engagement-icon-small"><img src="/images/d4-1.png"></div>
                        <div class="engagement-info-compact">
                            <span class="engagement-value-compact">{{ $totalLikes }}</span>
                            <span class="engagement-label-compact">Likes</span>
                        </div>
                    </div>
                    <div class="engagement-item-compact">
                        <div class="engagement-icon-small"><img src="/images/d4-2.png"></div>
                        <div class="engagement-info-compact">
                            <span class="engagement-value-compact">{{ $totalDislikes }}</span>
                            <span class="engagement-label-compact">Dislikes</span>
                        </div>
                    </div>
                    <div class="engagement-item-compact">
                        <div class="engagement-icon-small"><img src="/images/d4-3.png"></div>
                        <div class="engagement-info-compact">
                            <span class="engagement-value-compact">{{ $averageViewsPerBlog }}</span>
                            <span class="engagement-label-compact">Avg Views</span>
                        </div>
                    </div>
                    <div class="engagement-item-compact">
                        <div class="engagement-icon-small"><img src="/images/d4-4.png"></div>
                        <div class="engagement-info-compact">
                            <span class="engagement-value-compact">{{ $averageCommentsPerBlog }}</span>
                            <span class="engagement-label-compact">Avg Comments</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Comments -->
            <div class="dashboard-card card-wide">
                <h3 class="card-title"><img src="">Recent Comments</h3>
                <div class="activity-list activity-compact">
                    @forelse($recentComments->take(3) as $comment)
                        <div class="activity-item">
                            <div class="activity-icon"><img src=""></div>
                            <div class="activity-content">
                                <span class="activity-user">{{ $comment->user ? $comment->user->first_name . ' ' . $comment->user->last_name : 'Unknown' }}</span>
                                <span class="activity-text">{{ Str::limit($comment->comment, 40) }}</span>
                                <span class="activity-time">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="no-data">No comments yet</p>
                    @endforelse
                </div>
            </div>

            <!-- User Registrations Chart -->
            <div class="dashboard-card card-medium">
                <h3 class="card-title"><img src="">User Registrations (Last 6 Months)</h3>
                <div class="chart-container chart-compact">
                    <div class="bar-chart">
                        @php
                            $maxUserCount = max(array_values($userMonthlyData)) ?: 1;
                        @endphp
                        @foreach($userMonthlyData as $month => $count)
                            <div class="bar-group">
                                <div class="bar-wrapper">
                                    <div class="bar bar-users" style="height: {{ $maxUserCount > 0 ? ($count / $maxUserCount * 100) : 0 }}%">
                                        <span class="bar-value">{{ $count }}</span>
                                    </div>
                                </div>
                                <span class="bar-label">{{ date('M', strtotime($month . '-01')) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Popular Tags -->
            <div class="dashboard-card card-full">
                <h3 class="card-title"><img src="">Popular Tags</h3>
                <div class="tags-cloud">
                    @forelse($topTags as $tag => $count)
                        <div class="tag-item">
                            <span class="tag-name">{{ $tag }}</span>
                            <span class="tag-count">{{ $count }}</span>
                        </div>
                    @empty
                        <p class="no-data">No tags yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Users -->
            <div class="dashboard-card card-medium">
                <h3 class="card-title"><img src="">New User Registrations</h3>
                <div class="activity-list activity-compact">
                    @forelse($recentUsers->take(3) as $user)
                        <div class="activity-item">
                            <div class="activity-icon"><img src=""></div>
                            <div class="activity-content">
                                <span class="activity-user">{{ $user->first_name }} {{ $user->last_name }}</span>
                                <span class="activity-text">{{ Str::limit($user->email, 30) }}</span>
                                <span class="activity-time">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="no-data">No users yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Blog Posts -->
            <div class="dashboard-card card-wide">
                <h3 class="card-title"><img src="">Recent Blog Posts</h3>
                <div class="recent-blogs-list">
                    @forelse($recentBlogs->take(4) as $blog)
                        <div class="recent-blog-item">
                            <div class="recent-blog-info">
                                <span class="recent-blog-title">{{ Str::limit($blog->blog_title, 40) }}</span>
                                <span class="recent-blog-author">By {{ $blog->user ? $blog->user->first_name . ' ' . $blog->user->last_name : 'Unknown' }}</span>
                            </div>
                            <div class="recent-blog-meta">
                                <span class="recent-blog-views"><img src="">{{ $blog->views_count }}</span>
                                <span class="recent-blog-date">{{ $blog->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="no-data">No blogs yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
