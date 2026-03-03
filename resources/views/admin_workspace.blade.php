@extends('layouts.admin_layout')

@section('content')
    @php
        $allCount = $blogs->count();
        $draftCount = $blogs->where('blog_status', 'draft')->count();
        $scheduledCount = $blogs->where('blog_status', 'scheduled')->count();
        $publishedCount = $blogs->where('blog_status', 'published')->count();
        $trashCount = $blogs->where('blog_status', 'trash')->count();
    @endphp

    <div class="workspace">
        <div class="workspace-header">
            <h1 class="admin-header"><img src="/images/menu2.png" class="admin-h-icn">Dashboard<span class="slash">/</span><span id="hh">Workspace</span></h1>
            <h3 class="admin-subheader">Workspace</h3>
        </div>
        <div class="workspace-body">
            <div class="tool-header">
                <div class="search-bar">
                    <img src="/images/search.png" class="admin-h-icn">
                    <input type="text" id="search-input" placeholder="Search">
                </div>
                <a href="/admin/workspace/create" class="create-blog-btn">Create Blog</a>
            </div>
            <div class="tool-subheader">
                <div class="t-box">
                    <button class="t-option active" data-tab="all">All ({{ $allCount }})</button>
                    <button class="t-option" data-tab="draft">Drafts ({{ $draftCount }})</button>
                    <button class="t-option" data-tab="scheduled">Scheduled ({{ $scheduledCount }})</button>
                    <button class="t-option" data-tab="published">Published ({{ $publishedCount }})</button>
                    <button class="t-option" data-tab="trash">Trash ({{ $trashCount }})</button>
                </div>
                
                <div class="filter-box">
                    <select name="" id="filter-select">
                        <option value="newest">Newest</option>
                        <option value="oldest">Oldest</option>
                    </select>
                    <div class="f-box">
                        <button><img src="/images/f1.png" class="admin-h-icn" id="list-style-btn"></button>
                        <button><img src="/images/f2.png" class="admin-h-icn" id="grid-style-btn"></button>
                    </div>
                    <div class="f-box">
                        <button><img src="/images/f3.png" class="admin-h-icn" id="header-previous-btn"></button>
                        <button><img src="/images/f4.png" class="admin-h-icn" id="header-next-btn"></button>
                    </div>
                </div>
            </div>
            <div class="tool-body">
                <div class="tool-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Title</th>
                                <th class="col-date">Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="blog-table-body">
                            @forelse($blogs as $blog)
                                @php
                                    $authorName = trim(($blog->user->first_name ?? '') . ' ' . ($blog->user->last_name ?? ''));
                                @endphp
                                <tr class="blog-row" 
                                    data-status="{{ $blog->blog_status }}"
                                    data-title="{{ strtolower($blog->blog_title) }}"
                                    data-author="{{ strtolower($authorName) }}"
                                    data-date="{{ $blog->created_at->timestamp }}">
                                    <td><span class="status-badge status-{{ $blog->blog_status }}">{{ ucfirst($blog->blog_status) }}</span></td>
                                    <td class="tt">{{ $blog->blog_title }}</td>
                                    <td>
                                        @if($blog->blog_status === 'published')
                                            {{ $blog->created_at->format('m-d-y') }}
                                        @elseif($blog->blog_status === 'scheduled' && $blog->scheduled_at)
                                            {{ $blog->scheduled_at->format('m-d-y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="t-actions">
                                        <button class="action-edit" onclick="window.location.href='{{ url('/admin/workspace/create') }}?edit={{ $blog->id }}'">
                                            @if($blog->blog_status === 'published')
                                                Edit
                                            @elseif($blog->blog_status === 'scheduled')
                                                Reschedule
                                            @else
                                                Continue
                                            @endif
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="no-results">
                                    <td colspan="4" style="text-align: center; padding: 2rem; color: #666;">No blogs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="table-footer">
                        <div class="tf-left" id="results-count">Showing {{ $blogs->count() }} out of {{ $blogs->count() }}</div>
                        <div class="tf-right">
                            <button class="page-btn page-prev" title="Previous">&lt;</button>
                            <button class="page-btn page-next" title="Next">&gt;</button>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tool-footer"></div>
        </div>
    </div>

    <script>
        // Real-time filtering and search
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const filterSelect = document.getElementById('filter-select');
            const tabButtons = document.querySelectorAll('.t-option');
            const blogRows = Array.from(document.querySelectorAll('.blog-row'));
            const resultsCount = document.getElementById('results-count');
            const tbody = document.getElementById('blog-table-body');
            const prevBtn = document.querySelector('.page-btn.page-prev');
            const nextBtn = document.querySelector('.page-btn.page-next');
            const headerPrevBtn = document.getElementById('header-previous-btn');
            const headerNextBtn = document.getElementById('header-next-btn');
            const totalBlogs = blogRows.length;

            let currentTab = 'all';
            let currentSort = 'newest';
            let currentPage = 1;
            const itemsPerPage = 5;

            // Search functionality
            searchInput.addEventListener('input', function() {
                currentPage = 1;
                filterAndSort();
            });

            // Filter dropdown
            filterSelect.addEventListener('change', function() {
                currentSort = this.value;
                currentPage = 1;
                filterAndSort();
            });

            // Tab filtering
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    currentTab = this.getAttribute('data-tab');
                    currentPage = 1;
                    filterAndSort();
                });
            });

            function goToPreviousPage() {
                currentPage = Math.max(1, currentPage - 1);
                filterAndSort();
            }

            function goToNextPage() {
                currentPage = currentPage + 1;
                filterAndSort();
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    if (this.disabled) return;
                    goToPreviousPage();
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    if (this.disabled) return;
                    goToNextPage();
                });
            }

            if (headerPrevBtn) {
                headerPrevBtn.addEventListener('click', function() {
                    if (this.disabled) return;
                    goToPreviousPage();
                });
            }

            if (headerNextBtn) {
                headerNextBtn.addEventListener('click', function() {
                    if (this.disabled) return;
                    goToNextPage();
                });
            }

            function filterAndSort() {
                const searchTerm = searchInput.value.toLowerCase();
                let filteredRows = [];

                blogRows.forEach(row => {
                    const status = row.getAttribute('data-status');
                    const title = row.getAttribute('data-title');
                    const author = row.getAttribute('data-author');

                    // Check if row matches current tab
                    const matchesTab = currentTab === 'all' || status === currentTab;

                    // Check if row matches search term
                    const matchesSearch = searchTerm === '' || 
                                        title.includes(searchTerm) || 
                                        author.includes(searchTerm);

                    if (matchesTab && matchesSearch) {
                        filteredRows.push(row);
                    }
                });

                // Sort filtered rows
                if (currentSort === 'oldest') {
                    filteredRows.sort((a, b) => {
                        const dateA = parseInt(a.getAttribute('data-date'));
                        const dateB = parseInt(b.getAttribute('data-date'));
                        return dateA - dateB;
                    });
                } else {
                    filteredRows.sort((a, b) => {
                        const dateA = parseInt(a.getAttribute('data-date'));
                        const dateB = parseInt(b.getAttribute('data-date'));
                        return dateB - dateA;
                    });
                }

                // Reorder rows in the DOM using the sorted order
                filteredRows.forEach(row => {
                    tbody.appendChild(row);
                });

                // Pagination calculations
                const totalFiltered = filteredRows.length;
                const totalPages = Math.max(1, Math.ceil(totalFiltered / itemsPerPage));
                if (currentPage > totalPages) currentPage = totalPages;

                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const pageRows = filteredRows.slice(startIndex, endIndex);

                // Hide all rows first, then show only current page rows
                blogRows.forEach(row => {
                    row.style.display = 'none';
                });
                pageRows.forEach(row => {
                    row.style.display = '';
                });

                // Update counter
                if (totalFiltered === 0) {
                    resultsCount.textContent = `Showing 0 out of ${totalBlogs}`;
                } else {
                    const rangeStart = startIndex + 1;
                    const rangeEnd = Math.min(startIndex + pageRows.length, totalFiltered);
                    resultsCount.textContent = `Showing ${rangeStart}-${rangeEnd} out of ${totalFiltered}`;
                }

                // Update pagination buttons
                if (prevBtn) prevBtn.disabled = currentPage <= 1 || totalFiltered === 0;
                if (nextBtn) nextBtn.disabled = currentPage >= totalPages || totalFiltered === 0;
                if (headerPrevBtn) headerPrevBtn.disabled = currentPage <= 1 || totalFiltered === 0;
                if (headerNextBtn) headerNextBtn.disabled = currentPage >= totalPages || totalFiltered === 0;

                // Show "no results" message if needed
                const noResults = tbody.querySelector('.no-results');
                if (totalFiltered === 0 && !noResults) {
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results';
                    noResultsRow.innerHTML = '<td colspan="5" style="text-align: center; padding: 2rem; color: #666;">No blogs found</td>';
                    tbody.appendChild(noResultsRow);
                } else if (totalFiltered > 0 && noResults) {
                    noResults.remove();
                }
            }

            filterAndSort();
        });
    </script>
@endsection


