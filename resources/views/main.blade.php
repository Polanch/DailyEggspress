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
                    <li>
                        <a href="">
                            <div class="pop-image">
                                <img src="/thumbnails/something2.jpg" class="blog-thumbnail">
                            </div>
                            <div class="pop-content">
                                <h3>Sample Title here</h3>
                                <span class="pop-date"><p>January 30, 2028 | 8:00 p.m.</p></span>
                                <span class="view-count"><img src="/images/view.png" class="eyecon">300</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <div class="pop-image">
                                <img src="/thumbnails/something3.jpg" class="blog-thumbnail">
                            </div>
                            <div class="pop-content">
                                <h3>Sample Title here</h3>
                                <span class="pop-date"><p>January 30, 2028 | 8:00 p.m.</p></span>
                                <span class="view-count"><img src="/images/view.png" class="eyecon">300</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <div class="pop-image">
                                <img src="https://i.pinimg.com/1200x/c3/bb/d7/c3bbd77eec1de74eb5bf783938180cb2.jpg" class="blog-thumbnail">
                            </div>
                            <div class="pop-content">
                                <h3>Sample Title here</h3>
                                <span class="pop-date"><p>January 30, 2028 | 8:00 p.m.</p></span>
                                <span class="view-count"><img src="/images/view.png" class="eyecon">300</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <div class="pop-image">
                                <img src="https://i.pinimg.com/1200x/c3/bb/d7/c3bbd77eec1de74eb5bf783938180cb2.jpg" class="blog-thumbnail">
                            </div>
                            <div class="pop-content">
                                <h3>Sample Title here</h3>
                                <span class="pop-date"><p>January 30, 2028 | 8:00 p.m.</p></span>
                                <span class="view-count"><img src="/images/view.png" class="eyecon">300</span>
                            </div>
                        </a>
                    </li>
                </ul>
                <button id="pop-more">View All Popular Blogs <img src="/images/right.png" id="right-icn"></button>
            </div>
            <div class="random-box">
                <h1>Random Blogs</h1>
                <ul class="rand-list">
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
                    <li>
                        <a href="">
                            <div class="pop-image">
                                <img src="/thumbnails/something9.jpg" class="blog-thumbnail">
                            </div>
                            <div class="pop-content">
                                <h3>Random Title 2</h3>
                                <span class="pop-date"><p>January 12, 2027 | 6:00 p.m.</p></span>
                                <span class="view-count"><img src="/images/view.png" class="eyecon">87</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <div class="pop-image">
                                <img src="https://i.pinimg.com/1200x/c3/bb/d7/c3bbd77eec1de74eb5bf783938180cb2.jpg" class="blog-thumbnail">
                            </div>
                            <div class="pop-content">
                                <h3>Random Title 3</h3>
                                <span class="pop-date"><p>December 21, 2026 | 2:00 p.m.</p></span>
                                <span class="view-count"><img src="/images/view.png" class="eyecon">45</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <div class="pop-image">
                                <img src="https://i.pinimg.com/1200x/c3/bb/d7/c3bbd77eec1de74eb5bf783938180cb2.jpg" class="blog-thumbnail">
                            </div>
                            <div class="pop-content">
                                <h3>Random Title 3</h3>
                                <span class="pop-date"><p>December 21, 2026 | 2:00 p.m.</p></span>
                                <span class="view-count"><img src="/images/view.png" class="eyecon">45</span>
                            </div>
                        </a>
                    </li>
                </ul>
                <button id="rand-more">View Random Blogs <img src="/images/right.png" id="right-icn"></button>
            </div>
            <div class="all-tags">
                <h1>Popular Tags</h1>
                <div class="tags-wrap">
                    @if(isset($tags) && count($tags))
                        @foreach($tags as $tag)
                            <a href="{{ url('/tags/' . ($tag->slug ?? $tag)) }}" class="tag-link">{{ $tag->name ?? $tag }}</a>
                        @endforeach
                    @else
                        <a href="" class="tag-link">Anime</a>
                        <a href="" class="tag-link">Manga</a>
                        <a href="" class="tag-link">Games</a>
                        <a href="" class="tag-link">Internet</a>
                        <a href="" class="tag-link">Hobbies</a>
                        <a href="" class="tag-link">Review</a>
                        <a href="" class="tag-link">Tips</a>
                        <a href="" class="tag-link">Tutorial</a>
                        <a href="" class="tag-link">Anime</a>
                        <a href="" class="tag-link">Manga</a>
                        <a href="" class="tag-link">Games</a>
                        <a href="" class="tag-link">Internet</a>
                        <a href="" class="tag-link">Hobbies</a>
                        <a href="" class="tag-link">Review</a>
                        <a href="" class="tag-link">Tips</a>
                        <a href="" class="tag-link">Tutorial</a>
                        <a href="" class="tag-link">Anime</a>
                        <a href="" class="tag-link">Manga</a>
                        <a href="" class="tag-link">Games</a>
                        <a href="" class="tag-link">Internet</a>
                        <a href="" class="tag-link">Hobbies</a>
                        <a href="" class="tag-link">Review</a>
                        <a href="" class="tag-link">Tips</a>
                        <a href="" class="tag-link">Tutorial</a>
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
                    <img src="/thumbnails/something4.jpg" class="blog-thumbnail">
                </div>
                <div class="big-blog-details">
                    <h3>Sample Title Here</h3>
                    <div class="latest-tags">
                        <span class="tag">Games</span>
                        <span class="tag">Anime</span>
                        <span class="tag">Manga</span> 
                        <span class="tag">Lifestyle</span>
                        <span class="tag">Hobbies</span>  
                    </div>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusantium numquam dolor, perferendis enim earum fugit nobis est beatae quo mollitia perspiciatis minus quaerat officiis! Quaerat voluptate ut magnam odit ducimus maxime accusantium, illum blanditiis aperiam, nesciunt totam porro nihil. Iste, architecto maxime ullam laborum incidunt suscipit dolor et deserunt soluta.</p>
                    <div class="big-latest-footer">
                        <p>January 26, 2027</p>
                        <p>8:00 PM GMT+8</p>
                        <a href="" class="read-btn">Read More</a>
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
            <div class="blog-box">
                <div class="latest-image-box">
                    <img src="/thumbnails/something5.jpg" class="blog-thumbnail">
                </div>
                <div class="latest-content-box">
                    <h3>Title Goes Here Anime 2026</h3>
                    <div class="latest-tags">
                        <span class="tag">Games</span>
                        <span class="tag">Anime</span>
                        <span class="tag">Manga</span>
                    </div>
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Totam, natus. Eius quo, commodi incidunt nemo possimus accusantium ad laudantium corrupti.</p>
                    <div class="latest-footer">
                        <p>January 26, 2001 | 9:00 p.m.</p>
                        <a href="" class="read-btn">Read More</a>
                    </div>
                </div>
            </div>
            <div class="blog-box">
                <div class="latest-image-box">
                    <img src="/thumbnails/something6.jpg" class="blog-thumbnail">
                </div>
                <div class="latest-content-box">
                    <h3>Title Goes Here Anime 2026</h3>
                    <div class="latest-tags">
                        <span class="tag">Games</span>
                        <span class="tag">Anime</span>
                        <span class="tag">Manga</span>
                    </div>
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Totam, natus. Eius quo, commodi incidunt nemo possimus accusantium ad laudantium corrupti.</p>
                    <div class="latest-footer">
                        <p>January 26, 2001 | 9:00 p.m.</p>
                        <a href="" class="read-btn">Read More</a>
                    </div>
                </div>
            </div>
            <div class="blog-box">
                <div class="latest-image-box">
                    <img src="https://i.pinimg.com/1200x/c3/bb/d7/c3bbd77eec1de74eb5bf783938180cb2.jpg" class="blog-thumbnail">
                </div>
                <div class="latest-content-box">
                    <h3>Title Goes Here Anime 2026</h3>
                    <div class="latest-tags">
                        <span class="tag">Games</span>
                        <span class="tag">Anime</span>
                        <span class="tag">Manga</span>
                    </div>
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Totam, natus. Eius quo, commodi incidunt nemo possimus accusantium ad laudantium corrupti.</p>
                    <div class="latest-footer">
                        <p>January 26, 2001 | 9:00 p.m.</p>
                        <a href="" class="read-btn">Read More</a>
                    </div>
                </div>
            </div>
            <div class="blog-box">
                <div class="latest-image-box">
                    <img src="/thumbnails/something8.jpg" class="blog-thumbnail">
                </div>
                <div class="latest-content-box">
                    <h3>Title Goes Here Anime 2026</h3>
                    <div class="latest-tags">
                        <span class="tag">Games</span>
                        <span class="tag">Anime</span>
                        <span class="tag">Manga</span>
                    </div>
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Totam, natus. Eius quo, commodi incidunt nemo possimus accusantium ad laudantium corrupti.</p>
                    <div class="latest-footer">
                        <p>January 26, 2001 | 9:00 p.m.</p>
                        <a href="" class="read-btn">Read More</a>
                    </div>
                </div>
            </div> 
            <div class="blog-box-footer"></div>
        </div>
        <div class="the-footer">
            <p>&copy; 2024 The Daily Eggspress. All rights reserved.</p>
        </div>
    </div>
@endsection