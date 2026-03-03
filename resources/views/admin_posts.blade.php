@extends('layouts.admin_layout')

@section('content')
    <div class="posts">
        <div class="posts-header">
             <h1 class="admin-header"><img src="/images/menu4.png" class="admin-h-icn">Dashboard<span class="slash">/</span> <span id="hh">Posts</span></h1>
            <h3 class="admin-subheader">Posts</h3>
        </div>

        @if (session('success'))
            <div class="posts-alert success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="posts-alert error">{{ session('error') }}</div>
        @endif

        @php
            $requestedTab = request('tab');
            if (in_array($requestedTab, ['published', 'scheduled'], true)) {
                $defaultTab = $requestedTab;
            } else {
                $defaultTab = $publishedBlogs->isNotEmpty() ? 'published' : 'scheduled';
            }
        @endphp

        <div class="posts-switch" data-active-tab="{{ $defaultTab }}">
            <span class="posts-switch-indicator" aria-hidden="true"></span>
            <button type="button" class="posts-switch-btn" data-tab="published">Published</button>
            <button type="button" class="posts-switch-btn" data-tab="scheduled">Scheduled</button>
        </div>

        <div class="posts-section posts-panel {{ $defaultTab === 'published' ? 'active' : '' }}" data-tab-panel="published">
            <div class="posts-grid">
                @forelse ($publishedBlogs as $blog)
                    <article class="post-tile">
                        <div class="post-tile-top">
                            <span class="status-badge status-published">Published</span>
                            <p class="post-date">{{ $blog->updated_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <div class="post-thumb-wrap">
                            <img
                                src="{{ $blog->thumbnail ? asset($blog->thumbnail) : asset('images/thumbnails/something10.jfif') }}"
                                alt="{{ $blog->blog_title }} thumbnail"
                                class="post-thumb"
                            >
                        </div>

                        <h5 class="post-title">{{ $blog->blog_title }}</h5>

                        <div class="post-metrics">
                            <span>Views: {{ $blog->views_count }}</span>
                            <span>Likes: {{ $blog->likes_count }}</span>
                            <span>Dislikes: {{ $blog->dislikes_count }}</span>
                            <span>Comments: {{ $blog->comments_count }}</span>
                        </div>

                        <div class="post-actions post-actions-bottom">
                            <a href="{{ route('blogs.view', $blog->id) }}" class="post-btn link">View</a>

                            <div class="post-form-row">
                                <form action="{{ route('blogs.trash', $blog->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="post-btn secondary">Move to Trash</button>
                                </form>

                                <form action="{{ route('blogs.destroy', $blog->id) }}" method="POST" onsubmit="return confirm('Delete this published blog permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="post-btn danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="posts-empty">No published blogs yet.</p>
                @endforelse
            </div>
        </div>

        <div class="posts-section posts-panel {{ $defaultTab === 'scheduled' ? 'active' : '' }}" data-tab-panel="scheduled">
            <div class="posts-grid">
                @forelse ($scheduledBlogs as $blog)
                    <article class="post-tile">
                        <div class="post-tile-top">
                            <span class="status-badge status-scheduled">Scheduled</span>
                            <p class="post-date">{{ optional($blog->scheduled_at)->format('M d, Y h:i A') }}</p>
                        </div>

                        <div class="post-thumb-wrap">
                            <img
                                src="{{ $blog->thumbnail ? asset($blog->thumbnail) : asset('images/thumbnails/something10.jfif') }}"
                                alt="{{ $blog->blog_title }} thumbnail"
                                class="post-thumb"
                            >
                        </div>

                        <h5 class="post-title">{{ $blog->blog_title }}</h5>

                        <div class="post-countdown" data-countdown="{{ optional($blog->scheduled_at)->toIso8601String() }}">
                            <p class="countdown-label">Time before posting</p>
                            <p class="countdown-time">--:--:--</p>
                        </div>

                        <div class="post-actions">
                            <form action="{{ route('blogs.reschedule', $blog->id) }}" method="POST" class="post-form-single">
                                @csrf
                                @method('PATCH')
                                <input
                                    id="scheduled-at-{{ $blog->id }}"
                                    type="datetime-local"
                                    name="scheduled_at"
                                    value="{{ optional($blog->scheduled_at)->format('Y-m-d\TH:i') }}"
                                    class="reschedule-picker"
                                    required
                                >
                                <button type="button" class="post-btn reschedule-trigger" data-picker-id="scheduled-at-{{ $blog->id }}">Reschedule</button>
                            </form>

                            <div class="post-form-row">
                                <form action="{{ route('blogs.trash', $blog->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="post-btn secondary">Move to Trash</button>
                                </form>

                                <form action="{{ route('blogs.destroy', $blog->id) }}" method="POST" onsubmit="return confirm('Permanently delete this scheduled blog?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="post-btn danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="posts-empty">No scheduled blogs yet.</p>
                @endforelse
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const postsSwitch = document.querySelector('.posts-switch');
            if (postsSwitch) {
                const switchButtons = postsSwitch.querySelectorAll('.posts-switch-btn');
                const panels = document.querySelectorAll('.posts-panel');

                const setActiveTab = (tab) => {
                    postsSwitch.setAttribute('data-active-tab', tab);
                    switchButtons.forEach((button) => {
                        button.classList.toggle('active', button.dataset.tab === tab);
                    });
                    panels.forEach((panel) => {
                        panel.classList.toggle('active', panel.dataset.tabPanel === tab);
                    });
                };

                switchButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        setActiveTab(button.dataset.tab);
                    });
                });

                setActiveTab(postsSwitch.getAttribute('data-active-tab') || 'scheduled');
            }

            const countdownItems = document.querySelectorAll('[data-countdown]');
            const rescheduleButtons = document.querySelectorAll('.reschedule-trigger');

            rescheduleButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const pickerId = button.getAttribute('data-picker-id');
                    const picker = pickerId ? document.getElementById(pickerId) : null;
                    const form = button.closest('form');

                    if (!picker || !form) {
                        return;
                    }

                    const submitOnSelect = () => {
                        if (picker.value) {
                            form.submit();
                        }
                    };

                    picker.addEventListener('change', submitOnSelect, { once: true });

                    if (typeof picker.showPicker === 'function') {
                        picker.showPicker();
                    } else {
                        picker.focus();
                        picker.click();
                    }
                });
            });

            const updateCountdown = () => {
                countdownItems.forEach((item) => {
                    const targetDateRaw = item.getAttribute('data-countdown');
                    const output = item.querySelector('.countdown-time');
                    if (!targetDateRaw || !output) {
                        return;
                    }

                    const targetDate = new Date(targetDateRaw).getTime();
                    const now = new Date().getTime();
                    const diff = targetDate - now;

                    if (diff <= 0) {
                        output.textContent = 'Posting soon';
                        return;
                    }

                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
                    const minutes = Math.floor((diff / (1000 * 60)) % 60);
                    const seconds = Math.floor((diff / 1000) % 60);

                    output.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                });
            };

            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    </script>
@endsection