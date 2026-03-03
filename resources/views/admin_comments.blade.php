@extends('layouts.admin_layout')

@section('content')
	<div class="admin-comments">
		<div class="posts-header">
			<h1 class="admin-header"><img src="/images/menu3.png" class="admin-h-icn">Dashboard<span class="slash">/</span> <span id="hh">Comments</span></h1>
			<h3 class="admin-subheader">Comments</h3>
		</div>

		<div class="admin-comments-search-wrap">
			<input
				id="admin-comment-search"
				type="text"
				placeholder="Search by comment, blog title, or user..."
				value="{{ $initialQuery ?? '' }}"
				autocomplete="off"
			>
		</div>

		<div id="admin-comments-results" class="admin-comments-results">
			@php
				$csrfToken = csrf_token();
			@endphp
			@forelse(($initialComments ?? collect()) as $comment)
				@php
					$authorName = trim(($comment->user?->first_name ?? '') . ' ' . ($comment->user?->last_name ?? ''));
					if ($authorName === '') {
						$authorName = $comment->user?->username ?? 'Unknown user';
					}
				@endphp
				<article class="admin-comment-card">
					<div class="admin-comment-main">
						<div class="admin-comment-head">
							@if($comment->user && $comment->user->profile_picture)
								<img src="{{ Str::startsWith($comment->user->profile_picture, 'storage/') ? 
									asset($comment->user->profile_picture) : 
									asset('storage/' . $comment->user->profile_picture) }}" 
									alt="{{ $comment->user->first_name }}" class="admin-comment-avatar">
							@else
								<img src="/images/empty.png" alt="User" class="admin-comment-avatar">
							@endif
							<div class="admin-comment-author-info">
								<p class="admin-comment-author">{{ $authorName }}</p>
								<p class="admin-comment-date">{{ optional($comment->created_at)->format('M d, Y h:i A') }}</p>
							</div>
						</div>
						<p class="admin-comment-blog">On: {{ $comment->blog?->blog_title ?? 'Untitled blog' }}</p>
					<div class="admin-comment-text-wrapper">
						<p class="admin-comment-text {{ strlen($comment->comment) > 150 ? 'truncated' : '' }}" data-full-text="{{ htmlspecialchars($comment->comment, ENT_QUOTES, 'UTF-8') }}">{{ $comment->comment }}</p>
						@if(strlen($comment->comment) > 150)
							<button type="button" class="see-more-btn">See more</button>
						@endif
					</div>
					</div>
				<div class="admin-comment-actions">
					@if($comment->user?->role === 'banned')
						<button type="button" class="post-btn danger" disabled title="User already banned">Banned</button>
					@else
						<form action="{{ route('admin.comments.ban', $comment->id) }}" method="POST">
							@csrf
							<button type="submit" class="post-btn danger">Ban</button>
						</form>
					@endif
					<form action="{{ route('admin.comments.delete', $comment->id) }}" method="POST" onsubmit="return confirm('Delete this comment?');">
							@csrf
							@method('DELETE')
							<button type="submit" class="post-btn secondary">Delete</button>
						</form>
					</div>
				</article>
			@empty
				<p class="admin-comments-empty">No comments found.</p>
			@endforelse
		</div>

	<div id="admin-comments-pagination" class="admin-comments-pagination">
		@php
			$from = $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
			$to = min($currentPage * $perPage, $total);
			$hasNext = $to < $total;
			$hasPrev = $currentPage > 1;
		@endphp
		<div class="pagination-info">
			Showing <span id="pagination-from">{{ $from }}</span>-<span id="pagination-to">{{ $to }}</span> of <span id="pagination-total">{{ $total }}</span>
		</div>
		<div class="pagination-controls">
			<button id="pagination-prev" class="pagination-btn" {{ !$hasPrev ? 'disabled' : '' }}>Previous</button>
			<button id="pagination-next" class="pagination-btn" {{ !$hasNext ? 'disabled' : '' }}>Next</button>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const searchInput = document.getElementById('admin-comment-search');
			const resultsContainer = document.getElementById('admin-comments-results');
			const paginationFromEl = document.getElementById('pagination-from');
			const paginationToEl = document.getElementById('pagination-to');
			const paginationTotalEl = document.getElementById('pagination-total');
			const prevBtn = document.getElementById('pagination-prev');
			const nextBtn = document.getElementById('pagination-next');
			const csrfToken = @json($csrfToken);
			
			let currentPage = {{ $currentPage }};
			let totalComments = {{ $total }};
			let perPage = {{ $perPage }};
			let debounceTimer = null;

			const escapeHtml = (value) => {
				return String(value)
					.replace(/&/g, '&amp;')
					.replace(/</g, '&lt;')
					.replace(/>/g, '&gt;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&#039;');
			};

			const escapeRegex = (value) => {
				return String(value).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
			};

			const highlightMatch = (text, keyword) => {
				const safeText = escapeHtml(text);
				if (!keyword) {
					return safeText;
				}

				const pattern = new RegExp(`(${escapeRegex(keyword)})`, 'ig');
				return safeText.replace(pattern, '<mark class="search-highlight">$1</mark>');
			};

			const updatePagination = (total, page) => {
				totalComments = total;
				currentPage = page;
				
				const from = total > 0 ? ((page - 1) * perPage) + 1 : 0;
				const to = Math.min(page * perPage, total);
				const hasNext = to < total;
				const hasPrev = page > 1;

				if (paginationFromEl) paginationFromEl.textContent = from;
				if (paginationToEl) paginationToEl.textContent = to;
				if (paginationTotalEl) paginationTotalEl.textContent = total;
				
				if (prevBtn) prevBtn.disabled = !hasPrev;
				if (nextBtn) nextBtn.disabled = !hasNext;
			};

			const renderComments = (comments) => {
				if (!resultsContainer) {
					return;
				}

				const keyword = (searchInput?.value || '').trim();

				if (!comments.length) {
					resultsContainer.innerHTML = '<p class="admin-comments-empty">No comments found.</p>';
					return;
				}

				resultsContainer.innerHTML = comments.map((comment) => {
					const banAction = `{{ url('/admin/comments') }}/${comment.id}/ban`;
					const deleteAction = `{{ url('/admin/comments') }}/${comment.id}`;
					const commentText = comment.comment || '';
					const isLong = commentText.length > 150;
					const truncatedClass = isLong ? 'truncated' : '';
					const seeMoreBtn = isLong ? '<button type="button" class="see-more-btn">See more</button>' : '';
					const isBanned = comment.user_role === 'banned';
					const banButtonHtml = isBanned 
						? '<button type="button" class="post-btn danger" disabled title="User already banned">Banned</button>'
						: `<form action="${banAction}" method="POST">
							<input type="hidden" name="_token" value="${csrfToken}">
							<button type="submit" class="post-btn danger">Ban</button>
						</form>`;
					
					// Handle profile picture path
					let profilePictureUrl = '/images/empty.png';
					if (comment.profile_picture) {
						if (comment.profile_picture.startsWith('storage/')) {
							profilePictureUrl = `{{ asset('') }}${comment.profile_picture}`;
						} else {
							profilePictureUrl = `{{ asset('storage/') }}${comment.profile_picture}`;
						}
					}

					return `
						<article class="admin-comment-card">
							<div class="admin-comment-main">
								<div class="admin-comment-head">
									<img src="${profilePictureUrl}" alt="User" class="admin-comment-avatar">
									<div class="admin-comment-author-info">
										<p class="admin-comment-author">${highlightMatch(comment.author, keyword)}</p>
										<p class="admin-comment-date">${escapeHtml(comment.created_at)}</p>
									</div>
								</div>
								<p class="admin-comment-blog">On: ${highlightMatch(comment.blog_title, keyword)}</p>
								<div class="admin-comment-text-wrapper">
									<p class="admin-comment-text ${truncatedClass}" data-full-text="${escapeHtml(commentText)}">${highlightMatch(commentText, keyword)}</p>
									${seeMoreBtn}
								</div>
							</div>
							<div class="admin-comment-actions">
							${banButtonHtml}
								<form action="${deleteAction}" method="POST" onsubmit="return confirm('Delete this comment?');">
									<input type="hidden" name="_token" value="${csrfToken}">
									<input type="hidden" name="_method" value="DELETE">
									<button type="submit" class="post-btn secondary">Delete</button>
								</form>
							</div>
						</article>
					`;
				}).join('');
			};

			const runSearch = async (query, page = 1) => {
				try {
					const response = await fetch(`{{ route('admin.comments.search') }}?q=${encodeURIComponent(query)}&page=${page}`, {
						headers: {
							'Accept': 'application/json',
							'X-Requested-With': 'XMLHttpRequest',
						},
					});

					if (!response.ok) {
						return;
					}

					const payload = await response.json();
					renderComments(payload.comments || []);
					updatePagination(payload.total || 0, payload.currentPage || 1);
				} catch (error) {
					console.error('Comment search failed:', error);
				}
			};

			if (searchInput) {
				searchInput.addEventListener('input', () => {
					const value = searchInput.value || '';
					clearTimeout(debounceTimer);
					debounceTimer = setTimeout(() => {
						currentPage = 1; // Reset to page 1 on new search
						runSearch(value.trim(), 1);
					}, 250);
				});
			}

			if (prevBtn) {
				prevBtn.addEventListener('click', () => {
					if (currentPage > 1) {
						runSearch((searchInput?.value || '').trim(), currentPage - 1);
					}
				});
			}

			if (nextBtn) {
				nextBtn.addEventListener('click', () => {
					const maxPage = Math.ceil(totalComments / perPage);
					if (currentPage < maxPage) {
						runSearch((searchInput?.value || '').trim(), currentPage + 1);
					}
				});
			}

			// Event delegation for "See more" buttons
			if (resultsContainer) {
				resultsContainer.addEventListener('click', (e) => {
					if (e.target.classList.contains('see-more-btn')) {
						const btn = e.target;
						const textWrapper = btn.closest('.admin-comment-text-wrapper');
						const textEl = textWrapper?.querySelector('.admin-comment-text');
						
						if (textEl) {
							const isExpanded = textEl.classList.contains('expanded');
							
							if (isExpanded) {
								textEl.classList.remove('expanded');
								textEl.classList.add('truncated');
								btn.textContent = 'See more';
							} else {
								textEl.classList.remove('truncated');
								textEl.classList.add('expanded');
								btn.textContent = 'See less';
							}
						}
					}
				});
			}
		});
	</script>
@endsection