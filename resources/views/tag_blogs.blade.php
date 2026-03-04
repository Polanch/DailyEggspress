@extends('layouts.layout')

@section('content')
    <div class="home-container">
        <div class="the-header">
            <span class="tagline">
                <h1>Tag: #{{ $tag }}</h1>
                <p class="no-date-update">{{ count($blogs) }} blog{{ count($blogs) !== 1 ? 's' : '' }} found</p>
            </span>
            <span class="line"></span>
        </div>
        <div class="side-bar">
            <div class="popular-box">
                <h1>Similar Blogs</h1>
                <ul class="pop-list">
                    @forelse($similarBlogs ?? collect() as $blog)
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
                                        <span class="comment-count">💬 {{ $blog->commentCount ?? 0 }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li>
                            <div class="pop-content">
                                <h3>No blogs yet</h3>
                            </div>
                        </li>
                    @endforelse
                </ul>
                <button id="pop-more">View Similar Blogs <img src="/images/right.png" id="right-icn"></button>
            </div>
            <div class="random-box">
                <h1>More Blogs</h1>
                <ul class="rand-list">
                    @forelse($moreBlogs ?? collect() as $blog)
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
                                        <span class="comment-count">💬 {{ $blog->commentCount ?? 0 }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li>
                            <div class="pop-content">
                                <h3>No blogs yet</h3>
                            </div>
                        </li>
                    @endforelse
                </ul>
                <button id="rand-more">View More Blogs <img src="/images/right.png" id="right-icn"></button>
            </div>
            <div class="all-tags">
                <h1>More Tags</h1>
                <div class="tags-wrap">
                    @if(isset($tags) && count($tags))
                        @foreach(array_slice($tags, 0, 24) as $tagName)
                            <a href="{{ url('/tags/' . $tagName) }}" class="tag-link">{{ $tagName }}</a>
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
                    <a href="" class="egg-social"><img src="/images/soc1.png" id="soc-icn"></a>
                    <a href="" class="egg-social"><img src="/images/soc2.png" id="soc-icn"></a>
                    <a href="" class="egg-social"><img src="/images/soc3.png" id="soc-icn"></a>
                    <a href="" class="egg-social"><img src="/images/soc4.png" id="soc-icn"></a>
                    <a href="" class="egg-social"><img src="/images/soc5.png" id="soc-icn"></a>
                    <a href="" class="egg-social"><img src="/images/soc7.png" id="soc-icn"></a>
                    <img src="/images/basket.png" id="bask">
                </div>
            </div>
            <div class="side-bar-footer"></div>
        </div> 
        <div class="big-blog-box">
            <div class="big-blog-banner">
                <img src="{{ $topBlog && $topBlog->thumbnail ? asset($topBlog->thumbnail) : '/thumbnails/something10.jfif' }}" id="banner-hero">
            </div>
            <div class="big-blog-info">
                <div class="big-blog-image">
                    <img src="{{ $topBlog && $topBlog->thumbnail ? asset($topBlog->thumbnail) : '/thumbnails/something10.jfif' }}" class="blog-thumbnail">
                </div>
                <div class="big-blog-details">
                    <h3>{{ $topBlog ? $topBlog->blog_title : 'No Blog Available' }}</h3>
                    <div class="latest-tags">
                        @if($topBlog && is_array($topBlog->tags) && count($topBlog->tags))
                            @foreach($topBlog->tags as $blogTag)
                                <span class="tag">{{ $blogTag }}</span>
                            @endforeach
                        @endif
                    </div>
                    <p>{{ $topBlog ? \Illuminate\Support\Str::limit(strip_tags($topBlog->blog_content), 300) : 'Sample blog description...' }}</p>
                    <div class="big-latest-footer">
                        <p>{{ $topBlog ? $topBlog->created_at->format('F j, Y') : 'January 26, 2027' }}</p>
                        <p>{{ $topBlog ? $topBlog->created_at->format('g:i a') : '8:00 PM' }} GMT+8</p>
                        <a href="{{ $topBlog ? url('/blogs/' . $topBlog->id) : '#' }}" class="read-btn">Read More</a>
                    </div>
                </div>
            </div>
            <div class="big-blog-content">
                <h1>{{ $topBlog ? $topBlog->blog_title : 'No Blog Available' }}</h1>
                <h4>
                    By: 
                    @if($topBlog && $topBlog->user)
                        {{ $topBlog->user->first_name . ' ' . $topBlog->user->last_name }}
                    @else
                        Unknown Author
                    @endif
                </h4>
                <p class="view-count"><img src="/images/view.png" class="eyecon">{{ $topBlog ? $topBlog->views_count ?? 0 : 0 }} views</p>

                {!! $topBlog ? $topBlog->blog_content : '' !!}
                @if($topBlog && is_array($topBlog->tags) && count($topBlog->tags))
                    <p style="margin-top: 1rem; margin-bottom: 2rem; font-size: 14px;">
                        @foreach($topBlog->tags as $tag)
                            <a href="{{ url('/tags/' . $tag) }}" style="color: #E8B400; text-decoration: none; margin-right: 0.5rem;">#{{ $tag }}</a>
                        @endforeach
                    </p>
                @endif

                @if($topBlog)
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
                @endif
            </div>
        </div>
        <div class="blog-boxes">
            <div class="the-blog-header">
                <span class="line"></span>
                <span class="tagline">
                    <h1>All Posts ~ #{{ $tag }}</h1>
                    <p>The Daily Eggspress</p>
                </span>
                <span class="line"></span>
            </div>
            @if(isset($blogs) && $blogs->count())
                @foreach($blogs as $blog)
                    <div class="blog-box">
                        <div class="latest-image-box">
                            <img src="{{ $blog->thumbnail ? asset($blog->thumbnail) : '/thumbnails/something10.jfif' }}" class="blog-thumbnail">
                        </div>
                        <div class="latest-content-box">
                            <h3>{{ $blog->blog_title }}</h3>
                            <div class="latest-tags">
                                @if(is_array($blog->tags) && count($blog->tags))
                                    @foreach(array_slice($blog->tags, 0, 3) as $blogTag)
                                        <span class="tag">{{ $blogTag }}</span>
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
                        <h3>No blogs found with tag: #{{ $tag }}</h3>
                        <p>Try searching for a different tag.</p>
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
