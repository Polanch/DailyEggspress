@extends('layouts.admin_layout')

@section('content')
    <div class="trash">
        <div class="posts-header">
            <h1 class="admin-header"><img src="/images/menu4.png" class="admin-h-icn">Dashboard<span class="slash">/</span> <span id="hh">Trash</span></h1>
            <h3 class="admin-subheader">Trash</h3>
        </div>

        @if (session('success'))
            <div class="trash-alert success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="trash-alert error">{{ session('error') }}</div>
        @endif

        @if ($trashBlogs->isEmpty())
            <div class="trash-empty">
                <p>No blogs in trash</p>
            </div>
        @else
            <!-- Filter Section -->
            <div class="trash-filters">
                <div class="trash-search-wrapper">
                    <label for="trashSearch">Search Blog</label>
                    <input 
                        type="text" 
                        id="trashSearch" 
                        class="trash-search" 
                        placeholder="Search by title or author..."
                    >
                </div>

                <div class="trash-sort-wrapper">
                    <label for="trashSort">Sort by</label>
                    <select id="trashSort" class="trash-sort">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="oldest_created">Oldest Created</option>
                        <option value="title">Title A-Z</option>
                    </select>
                </div>
            </div>

            <!-- Trash List -->
            <div class="trash-list" id="trashList">
                @forelse ($trashBlogs as $blog)
                    <div class="trash-item" data-blog-id="{{ $blog->id }}">
                        <div class="trash-item-thumbnail">
                            <img 
                                src="{{ $blog->thumbnail ? asset($blog->thumbnail) : asset('images/empty.png') }}" 
                                alt="{{ $blog->blog_title }}"
                                class="trash-thumb"
                            >
                        </div>

                        <div class="trash-item-content">
                            <h4 class="trash-item-title">{{ $blog->blog_title }}</h4>
                            <p class="trash-item-author">
                                by {{ trim($blog->user?->first_name ?? '') . ' ' . trim($blog->user?->last_name ?? '') ?: '@' . $blog->user?->username }}
                            </p>
                            <p class="trash-item-date">
                                <span class="date-label">Deleted:</span> {{ $blog->updated_at->format('M d, Y h:i A') }}
                            </p>
                        </div>

                        <div class="trash-item-actions">
                            <form action="{{ route('admin.trash.restore', $blog->id) }}" method="POST" class="trash-restore-form" style="display: inline;">
                                @csrf
                                <button type="submit" class="trash-restore-btn" title="Restore to drafts">Restore</button>
                            </form>

                            <form action="{{ route('admin.trash.delete', $blog->id) }}" method="POST" class="trash-delete-form" style="display: inline;" onsubmit="return confirm('Are you sure you want to permanently delete this blog? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="trash-delete-btn" title="Permanently delete">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($trashBlogs->hasPages())
                <div class="trash-pagination">
                    @if ($trashBlogs->onFirstPage())
                        <button class="trash-pagination-btn" disabled>← Previous</button>
                    @else
                        <a href="{{ $trashBlogs->previousPageUrl() }}" class="trash-pagination-btn">← Previous</a>
                    @endif

                    <span class="trash-pagination-info">
                        Page {{ $trashBlogs->currentPage() }} of {{ $trashBlogs->lastPage() }}
                    </span>

                    @if ($trashBlogs->hasMorePages())
                        <a href="{{ $trashBlogs->nextPageUrl() }}" class="trash-pagination-btn">Next →</a>
                    @else
                        <button class="trash-pagination-btn" disabled>Next →</button>
                    @endif
                </div>
            @endif
        @endif
    </div>

    <script>
        let trashSearchTimeout;
        const trashSearch = document.getElementById('trashSearch');
        const trashSort = document.getElementById('trashSort');
        const trashList = document.getElementById('trashList');

        function fetchTrashBlogs(search = '', sort = 'newest', page = 1) {
            const url = `{{ route('admin.trash.search') }}?search=${encodeURIComponent(search)}&sort=${encodeURIComponent(sort)}&page=${page}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    renderTrashBlogs(data.blogs);
                    updateTrashPagination(data);
                })
                .catch(error => console.error('Error:', error));
        }

        function renderTrashBlogs(blogs) {
            if (blogs.length === 0) {
                trashList.innerHTML = '<div class="trash-empty"><p>No blogs found</p></div>';
                return;
            }

            let html = '';
            blogs.forEach(blog => {
                html += `
                    <div class="trash-item" data-blog-id="${blog.id}">
                        <div class="trash-item-thumbnail">
                            <img src="${blog.thumbnail}" alt="${blog.blog_title}" class="trash-thumb">
                        </div>

                        <div class="trash-item-content">
                            <h4 class="trash-item-title">${blog.blog_title}</h4>
                            <p class="trash-item-author">by ${blog.author}</p>
                            <p class="trash-item-date">
                                <span class="date-label">Deleted:</span> ${blog.updated_at}
                            </p>
                        </div>

                        <div class="trash-item-actions">
                            <form action="/admin/trash/${blog.id}/restore" method="POST" class="trash-restore-form" style="display: inline;">
                                {{ csrf_field() }}
                                <button type="submit" class="trash-restore-btn" title="Restore to drafts">Restore</button>
                            </form>

                            <form action="/admin/trash/${blog.id}" method="POST" class="trash-delete-form" style="display: inline;" onsubmit="return confirm('Are you sure you want to permanently delete this blog? This action cannot be undone.');">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="trash-delete-btn" title="Permanently delete">Delete</button>
                            </form>
                        </div>
                    </div>
                `;
            });

            trashList.innerHTML = html;
        }

        function updateTrashPagination(data) {
            const paginationContainer = document.querySelector('.trash-pagination');
            if (!paginationContainer) return;

            let html = '';

            if (data.current_page === 1) {
                html += '<button class="trash-pagination-btn" disabled>← Previous</button>';
            } else {
                html += `<a href="?page=${data.current_page - 1}" class="trash-pagination-btn">← Previous</a>`;
            }

            html += `<span class="trash-pagination-info">Page ${data.current_page} of ${data.last_page}</span>`;

            if (data.current_page < data.last_page) {
                html += `<a href="?page=${data.current_page + 1}" class="trash-pagination-btn">Next →</a>`;
            } else {
                html += '<button class="trash-pagination-btn" disabled>Next →</button>';
            }

            paginationContainer.innerHTML = html;
        }

        if (trashSearch) {
            trashSearch.addEventListener('input', function() {
                clearTimeout(trashSearchTimeout);
                trashSearchTimeout = setTimeout(() => {
                    fetchTrashBlogs(this.value, trashSort.value);
                }, 300);
            });
        }

        if (trashSort) {
            trashSort.addEventListener('change', function() {
                fetchTrashBlogs(trashSearch?.value || '', this.value);
            });
        }
    </script>

    <style>
        .trash {
            padding: 20px;
        }

        .trash-alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .trash-alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .trash-alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .trash-empty {
            text-align: center;
            padding: 40px 20px;
            color: #999;
            font-size: 16px;
        }

        .trash-filters {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .trash-search-wrapper,
        .trash-sort-wrapper {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .trash-search-wrapper label,
        .trash-sort-wrapper label {
            font-weight: 600;
            color: #333;
            font-size: 13px;
        }

        .trash-search,
        .trash-sort {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .trash-search:focus,
        .trash-sort:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .trash-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .trash-item {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 20px;
            align-items: center;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .trash-item:hover {
            background-color: #fff;
            border-color: #bbb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .trash-item-thumbnail {
            width: 120px;
            height: 80px;
            overflow: hidden;
            border-radius: 6px;
            background-color: #e9ecef;
        }

        .trash-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .trash-item-content {
            min-width: 0;
        }

        .trash-item-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin: 0 0 6px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .trash-item-author {
            font-size: 13px;
            color: #666;
            margin: 0 0 6px 0;
        }

        .trash-item-date {
            font-size: 12px;
            color: #999;
            margin: 0;
        }

        .date-label {
            font-weight: 600;
            color: #666;
        }

        .trash-item-actions {
            display: flex;
            gap: 10px;
            white-space: nowrap;
        }

        .trash-restore-btn,
        .trash-delete-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .trash-restore-btn {
            background-color: #28a745;
            color: white;
        }

        .trash-restore-btn:hover {
            background-color: #218838;
        }

        .trash-delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .trash-delete-btn:hover {
            background-color: #c82333;
        }

        .trash-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            padding: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .trash-pagination-btn {
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: white;
            color: #333;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .trash-pagination-btn:hover:not(:disabled) {
            border-color: #007bff;
            color: #007bff;
            background-color: #f0f8ff;
        }

        .trash-pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .trash-pagination-info {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .trash-filters {
                grid-template-columns: 1fr;
            }

            .trash-item {
                grid-template-columns: 100px 1fr;
                column-gap: 15px;
                row-gap: 10px;
            }

            .trash-item-thumbnail {
                width: 100px;
                height: 70px;
                grid-row: 1 / 3;
            }

            .trash-item-content {
                grid-column: 2;
                grid-row: 1 / 3;
            }

            .trash-item-actions {
                grid-column: 1 / 3;
                width: 100%;
            }

            .trash-restore-btn,
            .trash-delete-btn {
                flex: 1;
            }
        }
    </style>
@endsection