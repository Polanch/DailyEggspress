@extends('layouts.layout')

@section('content')
    <div class="home-container">
        <div class="the-header">
            <span class="tagline">
                <h1>Latest Updates!</h1>
                <p></p>
            </span>
            <span class="line"></span>
        </div>
        <div class="side-bar">
            <div class="popular-box">
                <h1>Popular Blogs</h1>
                <ul class="pop-list">
                    @forelse($popularBlogs ?? collect() as $blog)
                        <li>
                            <a href="{{ url('/blogs/' . $blog->id) }}">
                                <div class="pop-image">
                                    <img src="{{ $blog->thumbnail ? asset($blog->thumbnail) : '/thumbnails/something10.jfif' }}" class="blog-thumbnail">
                                </div>
                                <div class="pop-content">
                                    <h3>{{ $blog->blog_title }}</h3>
                                    <span class="pop-date"><p>{{ $blog->created_at->format('F j, Y | g:i a') }}</p></span>
                                    <div class="blog-stats">
                                        <span class="view-count"><img src="/images/view.png" class="eyecon">{{ $blog->views_count ?? 0 }}</span>
                                        <span class="like-count">👍 {{ $blog->likeCount ?? 0 }}</span>
                                        <span class="dislike-count">👎 {{ $blog->dislikeCount ?? 0 }}</span>                                        <span class="comment-count">💬 {{ $randomBlog->commentCount ?? 0 }}</span>                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li>
                            <a href="">
                                <div class="pop-image">
                                    <img src="/thumbnails/something.jpg" class="blog-thumbnail">
                                </div>
                                <div class="pop-content">
                                    <h3>Sample Title here</h3>
                                    <span class="pop-date"><p>January 30, 2028 | 8:00 p.m.</p></span>
                                    <span class="view-count"><img src="/images/view.png" class="eyecon">300</span>
                                </div>
                            </a>
                        </li>
                    @endforelse
                </ul>
                <button id="pop-more">View All Popular Blogs <img src="/images/right.png" id="right-icn"></button>
            </div>
            <div class="random-box">
                <h1>Random Blogs</h1>
                <ul class="rand-list">
                    @forelse($randomBlogs ?? collect() as $blog)
                        <li>
                            <a href="{{ url('/blogs/' . $blog->id) }}">
                                <div class="pop-image">
                                    <img src="{{ $blog->thumbnail ? asset($blog->thumbnail) : '/thumbnails/something10.jfif' }}" class="blog-thumbnail">
                                </div>
                                <div class="pop-content">
                                    <h3>{{ $blog->blog_title }}</h3>
                                    <span class="pop-date"><p>{{ $blog->created_at->format('F j, Y | g:i a') }}</p></span>
                                    <div class="blog-stats">
                                        <span class="view-count"><img src="/images/view.png" class="eyecon">{{ $blog->views_count ?? 0 }}</span>
                                        <span class="like-count">👍 {{ $blog->likeCount ?? 0 }}</span>
                                        <span class="dislike-count">👎 {{ $blog->dislikeCount ?? 0 }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li>
                            <a href="">
                                <div class="pop-image">
                                    <img src="/thumbnails/something7.jpg" class="blog-thumbnail">
                                </div>
                                <div class="pop-content">
                                    <h3>Random Title 1</h3>
                                    <span class="pop-date"><p>January 30, 2028 | 8:00 p.m.</p></span>
                                    <span class="view-count"><img src="/images/view.png" class="eyecon">120</span>
                                </div>
                            </a>
                        </li>
                    @endforelse
                </ul>
                <button id="rand-more">View Random Blogs <img src="/images/right.png" id="right-icn"></button>
            </div>
            <div class="all-tags">
                <h1>Popular Tags</h1>
                <div class="tags-wrap">
                    @if(isset($tags) && count($tags))
                        @foreach(array_slice($tags, 0, 24) as $tag)
                            <a href="{{ url('/tags/' . $tag) }}" class="tag-link">{{ $tag }}</a>
                        @endforeach
                    @endif
                </div>
                <button id="tags-more">View All Tags <img src="/images/right.png" id="right-icn"></button>
            </div>
            <div class="short-message">
                <h1>Get to know me</h1>
                <h3>Yahallo!~ 😜</h3>
                <p><img src="/images/pfp1.jpg" id="pfp">My name is John Lloyd F. Olipani but you can also call me Polanch. I'm a broke freelancer and this blog is gonna be a slow one since I don't usually eggspress *ba dumm tss* my thoughts and opinions on things. This blog will probably contain a lot of anime and manga and occasionally..maybe some politics?</br></br></p>
            </div>
            <div class="basket">
                <div class="basket-container">
                    <p>Follow me</p>
                    <p>on socials</p>
                    <a href="" class="egg-social"><img src="images/soc1.png" id="soc-icn"></a>
                    <a href="" class="egg-social"><img src="images/soc2.png" id="soc-icn"></a>
                    <a href="" class="egg-social"><img src="images/soc3.png" id="soc-icn"></a>
                    <a href="" class="egg-social"><img src="images/soc4.png" id="soc-icn"></a>
                    <a href="" class="egg-social"><img src="images/soc5.png" id="soc-icn"></a>
                    <a href="" class="egg-social"><img src="images/soc7.png" id="soc-icn"></a>
                    <img src="/images/basket.png" id="bask">
                </div>
            </div>
            <div class="side-bar-footer"></div>
        </div> 
        <div class="big-blog-box">
            <div class="big-blog-banner">
                <img src="{{ $latestBlog && $latestBlog->thumbnail ? asset($latestBlog->thumbnail) : '/thumbnails/something10.jfif' }}" id="banner-hero">
            </div>
            <div class="big-blog-info">
                <div class="big-blog-image">
                    <img src="{{ $mostLikedBlog && $mostLikedBlog->thumbnail ? asset($mostLikedBlog->thumbnail) : '/thumbnails/something4.jpg' }}" class="blog-thumbnail">
                </div>
                <div class="big-blog-details">
                    <h3>{{ $mostLikedBlog ? $mostLikedBlog->blog_title : 'No Blog Available' }}</h3>
                    <div class="latest-tags">
                        @if($mostLikedBlog && is_array($mostLikedBlog->tags) && count($mostLikedBlog->tags))
                            @foreach($mostLikedBlog->tags as $tag)
                                <span class="tag">{{ $tag }}</span>
                            @endforeach
                        @else
                            <span class="tag">Most Liked</span>
                        @endif
                    </div>
                    <p>{{ $mostLikedBlog ? \Illuminate\Support\Str::limit(strip_tags($mostLikedBlog->blog_content), 240) : 'Most liked blog preview will appear here.' }}</p>
                    <div class="big-latest-footer">
                        <p>{{ $mostLikedBlog ? $mostLikedBlog->created_at->format('F j, Y') : now()->format('F j, Y') }}</p>
                        <p>{{ $mostLikedBlog ? $mostLikedBlog->created_at->format('g:i A') : now()->format('g:i A') }} GMT+8</p>
                        <a href="{{ $mostLikedBlog ? url('/blogs/' . $mostLikedBlog->id) : '#' }}" class="read-btn">Read More</a>
                    </div>
                </div>
            </div>
            <div class="big-blog-content">
                <h1>{{ $latestBlog ? $latestBlog->blog_title : 'No Blog Available' }}</h1>
                <h4>
                    By: 
                    @if($latestBlog && $latestBlog->user)
                        {{ $latestBlog->user->first_name . ' ' . $latestBlog->user->last_name }}
                    @else
                        Unknown Author
                    @endif
                </h4>
                {!! $latestBlog ? $latestBlog->blog_content : '' !!}
                @if($latestBlog && is_array($latestBlog->tags) && count($latestBlog->tags))
                    <p style="margin-top: 1rem; margin-bottom: 2rem; font-size: 14px;">
                        @foreach($latestBlog->tags as $tag)
                            <a href="{{ url('/tags/' . $tag) }}" style="color: #E8B400; text-decoration: none; margin-right: 0.5rem;">#{{ $tag }}</a>
                        @endforeach
                    </p>
                @endif

                @if($latestBlog)
                    <div class="reaction-row guest-reaction-row">
                        <button type="button" class="reaction-btn" disabled>👍 Like (<span class="like-count">{{ $likeCount ?? 0 }}</span>)</button>
                        <button type="button" class="reaction-btn" disabled>👎 Dislike (<span class="dislike-count">{{ $dislikeCount ?? 0 }}</span>)</button>
                    </div>

                    <p class="member-only-note">
                        Must be a member to join the conversation.
                        <a href="{{ url('/login?form=register') }}" class="member-only-link">Join Here</a>
                    </p>

                    <div class="comment-box">
                        <h3>Comments</h3>
                        <ul class="comment-list">
                                @forelse($comments as $comment)
                                    <li class="comment-item">
                                        <div class="comment-header">
                                            @if($comment->user && $comment->user->profile_picture)
                                                <img src="{{ Str::startsWith($comment->user->profile_picture, 'storage/') ? asset($comment->user->profile_picture) : asset('storage/' . $comment->user->profile_picture) }}" alt="{{ $comment->user->first_name }}" class="comment-avatar">
                                            @else
                                                <img src="/images/empty.png" alt="User" class="comment-avatar">
                                            @endif
                                            <div class="comment-user-info">
                                                <p class="comment-user">
                                                    {{ $comment->user ? $comment->user->first_name . ' ' . $comment->user->last_name : 'User' }}
                                                </p>
                                                <span class="comment-date">{{ $comment->created_at->format('M d, Y h:i a') }}</span>
                                            </div>
                                        </div>
                                        <p class="comment-text">{{ $comment->comment }}</p>

                                        @if($comment->replies->count() > 0)
                                            <ul class="reply-list">
                                                @foreach($comment->replies as $reply)
                                                    <li class="comment-item reply-item">
                                                        <div class="comment-header">
                                                            @if($reply->user && $reply->user->profile_picture)
                                                                <img src="{{ Str::startsWith($reply->user->profile_picture, 'storage/') ? asset($reply->user->profile_picture) : asset('storage/' . $reply->user->profile_picture) }}" alt="{{ $reply->user->first_name }}" class="comment-avatar">
                                                            @else
                                                                <img src="/images/empty.png" alt="User" class="comment-avatar">
                                                            @endif
                                                            <div class="comment-user-info">
                                                                <p class="comment-user">
                                                                    {{ $reply->user ? $reply->user->first_name . ' ' . $reply->user->last_name : 'User' }}
                                                                </p>
                                                                <span class="comment-date">{{ $reply->created_at->format('M d, Y h:i a') }}</span>
                                                            </div>
                                                        </div>
                                                        <p class="comment-text">{{ $reply->comment }}</p>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @empty
                                    <li class="comment-item">
                                        <p class="comment-text">No comments yet. Be the first to comment.</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="blog-boxes">
            <div class="the-blog-header">
                <span class="line"></span>
                <span class="tagline">
                    <h1>Weekly Highlights</h1>
                    <p>The Daily Eggspress</p>
                </span>
                <span class="line"></span>
            </div>
            @if(isset($weeklyBlogs) && $weeklyBlogs->count())
                @foreach($weeklyBlogs as $blog)
                    <div class="blog-box">
                        <div class="latest-image-box">
                            <img src="{{ $blog->thumbnail ? asset($blog->thumbnail) : '/thumbnails/something10.jfif' }}" class="blog-thumbnail">
                        </div>
                        <div class="latest-content-box">
                            <h3>{{ $blog->blog_title }}</h3>
                            <div class="latest-tags">
                                @if(is_array($blog->tags) && count($blog->tags))
                                    @foreach(array_slice($blog->tags, 0, 3) as $tag)
                                        <span class="tag">{{ $tag }}</span>
                                    @endforeach
                                @else
                                    <span class="tag">Blog</span>
                                @endif
                            </div>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($blog->blog_content), 170) }}</p>
                            <div class="latest-footer">
                                <p>{{ $blog->created_at->format('F j, Y | g:i a') }}</p>
                                <a href="{{ url('/blogs/' . $blog->id) }}" class="read-btn">Read More</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="blog-box">
                    <div class="latest-content-box">
                        <h3>No published blogs yet</h3>
                        <p>Weekly highlights will appear here once you publish posts.</p>
                    </div>
                </div>
            @endif
            <div class="blog-box-footer"></div>
        </div>
        <div class="the-footer">
            <p>&copy; 2024 The Daily Eggspress. All rights reserved.</p>
        </div>
    </div>
@endsection