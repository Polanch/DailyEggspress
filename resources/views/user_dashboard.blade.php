@extends('layouts.user_layout')

@section('content')
    <div class="home-container">
        <div class="the-header">
            <span class="tagline">
                <h1>Latest Updates!</h1>
                <p>{{ $blog ? $blog->created_at->format('F j, Y | g:i a') : now()->format('F j, Y | g:i a') }}</p>
            </span>
            <span class="line"></span>
        </div>

        <div class="side-bar">
            <div class="popular-box">
                <h1>Popular Blogs</h1>
                <ul class="pop-list">
                    @forelse($popularBlogs as $popularBlog)
                        <li>
                            <a href="{{ route('user.blog.view', $popularBlog->id) }}">
                                <div class="pop-image">
                                    <img src="{{ $popularBlog->thumbnail ? asset($popularBlog->thumbnail) : '/thumbnails/something10.jfif' }}" class="blog-thumbnail">
                                </div>
                                <div class="pop-content">
                                    <h3>{{ $popularBlog->blog_title }}</h3>
                                    <span class="pop-date"><p>{{ $popularBlog->created_at->format('F j, Y | g:i a') }}</p></span>
                                    <div class="blog-stats">
                                        <span class="view-count"><img src="/images/view.png" class="eyecon">{{ $popularBlog->views_count ?? 0 }}</span>
                                        <span class="like-count">👍 {{ $popularBlog->likeCount ?? 0 }}</span>
                                        <span class="dislike-count">👎 {{ $popularBlog->dislikeCount ?? 0 }}</span>
                                        <span class="comment-count">💬 {{ $popularBlog->commentCount ?? 0 }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li><div class="pop-content"><h3>No blogs yet</h3></div></li>
                    @endforelse
                </ul>
                <button id="pop-more">View All Popular Blogs <img src="/images/right.png" id="right-icn"></button>
            </div>

            <div class="random-box">
                <h1>Random Blogs</h1>
                <ul class="rand-list">
                    @forelse($randomBlogs as $randomBlog)
                        <li>
                            <a href="{{ route('user.blog.view', $randomBlog->id) }}">
                                <div class="pop-image">
                                    <img src="{{ $randomBlog->thumbnail ? asset($randomBlog->thumbnail) : '/thumbnails/something10.jfif' }}" class="blog-thumbnail">
                                </div>
                                <div class="pop-content">
                                    <h3>{{ $randomBlog->blog_title }}</h3>
                                    <span class="pop-date"><p>{{ $randomBlog->created_at->format('F j, Y | g:i a') }}</p></span>
                                    <div class="blog-stats">
                                        <span class="view-count"><img src="/images/view.png" class="eyecon">{{ $randomBlog->views_count ?? 0 }}</span>
                                        <span class="like-count">👍 {{ $randomBlog->likeCount ?? 0 }}</span>
                                        <span class="dislike-count">👎 {{ $randomBlog->dislikeCount ?? 0 }}</span>
                                        <span class="comment-count">💬 {{ $randomBlog->commentCount ?? 0 }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li><div class="pop-content"><h3>No blogs yet</h3></div></li>
                    @endforelse
                </ul>
                <button id="rand-more">View Random Blogs <img src="/images/right.png" id="right-icn"></button>
            </div>

            <div class="all-tags">
                <h1>Popular Tags</h1>
                <div class="tags-wrap">
                    @forelse($tags as $tag)
                        <a href="{{ url('/tags/' . $tag) }}" class="tag-link">{{ $tag }}</a>
                    @empty
                        <span class="tag-link">Blog</span>
                    @endforelse
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
            @if($blog)
                <div class="big-blog-banner">
                    <img src="{{ $blog->thumbnail ? asset($blog->thumbnail) : '/thumbnails/something10.jfif' }}" id="banner-hero">
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
                            <p>{{ $mostLikedBlog ? $mostLikedBlog->created_at->format('g:i A') : now()->format('g:i A') }}</p>
                            <a href="{{ $mostLikedBlog ? route('user.blog.view', $mostLikedBlog->id) : '#' }}" class="read-btn">Read More</a>
                        </div>
                    </div>
                </div>

                <div class="big-blog-content">
                    <h1>{{ $blog->blog_title }}</h1>
                    <h4>
                        By:
                        @if($blog->user)
                            {{ $blog->user->first_name . ' ' . $blog->user->last_name }}
                        @else
                            Unknown Author
                        @endif
                    </h4>
                    <p class="view-count"><img src="/images/view.png" class="eyecon">{{ $blog->views_count ?? 0 }} views</p>

                    {!! $blog->blog_content !!}
                    @if(is_array($blog->tags) && count($blog->tags))
                        <p style="margin-top: 1rem; margin-bottom: 2rem; font-size: 14px;">
                            @foreach($blog->tags as $tag)
                                <a href="{{ url('/tags/' . $tag) }}" style="color: #E8B400; text-decoration: none; margin-right: 0.5rem;">#{{ $tag }}</a>
                            @endforeach
                        </p>
                    @endif
                </div>

                <div class="user-blog-actions">
                    <div class="reaction-row">
                        <button type="button" class="reaction-btn {{ $userReaction === 'like' ? 'active' : '' }}" data-reaction="like" data-blog-id="{{ $blog->id }}" data-csrf="{{ csrf_token() }}">👍 Like (<span class="like-count">{{ $likeCount }}</span>)</button>
                        <button type="button" class="reaction-btn {{ $userReaction === 'dislike' ? 'active' : '' }}" data-reaction="dislike" data-blog-id="{{ $blog->id }}" data-csrf="{{ csrf_token() }}">👎 Dislike (<span class="dislike-count">{{ $dislikeCount }}</span>)</button>
                    </div>

                    <div class="comment-box">
                        <h3>Comments</h3>
                        
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-error">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        
                        <form action="{{ route('user.blog.comment', $blog->id) }}" method="POST" class="comment-form">
                            @csrf
                            <textarea name="comment" rows="3" placeholder="Write your comment..." required></textarea>
                            <div class="char-counter"><span class="char-count">0</span>/1000</div>
                            <button type="submit" class="read-btn">Post Comment</button>
                        </form>

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
                                    <button type="button" class="read-btn reply-btn" data-comment-id="{{ $comment->id }}">Reply</button>

                                                                    <form action="{{ route('user.blog.comment', $blog->id) }}" method="POST" class="reply-form" style="display: none;" data-comment-id="{{ $comment->id }}">
                                                                        @csrf
                                                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                                        <textarea name="comment" rows="2" placeholder="Write your reply..." required></textarea>
                                                                        <div class="char-counter"><span class="char-count">0</span>/1000</div>
                                                                        <div class="reply-actions">
                                                                            <button type="submit" class="read-btn">Post Reply</button>
                                                                            <button type="button" class="read-btn cancel-reply-btn">Cancel</button>
                                                                        </div>
                                                                    </form>

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
            @else
                <div class="big-blog-content">
                    <h1>No Published Blogs Yet</h1>
                    <p>Once there are published blogs, they will appear here.</p>
                </div>
            @endif
        </div>

        <div class="the-footer">
            <p>&copy; 2024 The Daily Eggspress. All rights reserved.</p>
        </div>
    </div>
@endsection
