@extends('layouts.admin_layout')

@section('content')
    <div class="admin-users">
		<div class="users-header">
			<h1 class="admin-header"><img src="/images/menu6.png" class="admin-h-icn">Dashboard<span class="slash">/</span> <span id="hh">Users</span></h1>
			<h3 class="admin-subheader">Users Management</h3>
		</div>
		
		@if(session('success'))
			<div class="posts-alert success">
				{{ session('success') }}
			</div>
		@endif
		
		@if(session('error'))
			<div class="posts-alert error">
				{{ session('error') }}
			</div>
		@endif
		
		<!-- Search and Filter Section -->
		<div class="users-filters">
			<div class="filter-group">
				<label for="userSearch">Search User:</label>
				<input type="text" id="userSearch" class="user-search-input" placeholder="Search by name, username, or email...">
			</div>
			
			<div class="filter-group">
				<label for="ageFilter">Filter by Age:</label>
				<input type="number" id="ageFilter" class="filter-input" placeholder="Enter age" min="1" max="120">
			</div>
			
			<div class="filter-group">
				<label for="activeFilter">Status:</label>
				<select id="activeFilter" class="filter-select">
					<option value="">All Users</option>
					<option value="online">Online Only</option>
					<option value="offline">Offline Only</option>
				</select>
			</div>
			
			<div class="filter-group">
				<label for="sortFilter">Sort By:</label>
				<select id="sortFilter" class="filter-select">
					<option value="newest">Newest First</option>
					<option value="oldest">Oldest First</option>
					<option value="name">Name (A-Z)</option>
					<option value="comments">Most Comments</option>
				</select>
			</div>
		</div>

		<!-- Users Grid -->
		<div class="users-grid" id="usersGrid">
			@foreach($users as $user)
				<div class="user-tile {{ $user->role === 'banned' ? 'banned-user' : '' }}">
					<div class="user-profile-pic">
						<img src="{{ $user->profile_picture ? (Str::startsWith($user->profile_picture, 'storage/') ? asset($user->profile_picture) : asset('storage/' . $user->profile_picture)) : '/images/empty.png' }}" alt="{{ $user->username }}">
						@if($user->role === 'banned')
							<img src="/images/banned.png" class="banned-overlay" alt="Banned">
						@endif
						<div class="online-indicator {{ $user->is_online ? 'online' : 'offline' }}"></div>
					</div>
					
					<div class="user-info">
						<h3 class="user-name">{{ $user->first_name }} {{ $user->last_name }}</h3>
						<p class="user-username">{{ '@' . $user->username }}</p>
						<p class="user-email">{{ $user->email }}</p>
						<p class="user-birthday">Birthday: {{ \Carbon\Carbon::parse($user->birthday)->format('M d, Y') }}</p>
						<p class="user-age">Age: {{ \Carbon\Carbon::parse($user->birthday)->age }} years old</p>
					</div>
					
					<div class="user-stats">
						<div class="stat-item">
							<span class="stat-label">Last Login:</span>
							<span class="stat-value">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never' }}</span>
						</div>
						<div class="stat-item">
							<span class="stat-label">Comments:</span>
							<span class="stat-value">{{ $user->blog_comments_count }}</span>
						</div>
						<div class="stat-item">
							<span class="stat-label">Status:</span>
							<span class="stat-value {{ $user->is_online ? 'status-online' : 'status-offline' }}">
								{{ $user->is_online ? 'Online' : 'Offline' }}
							</span>
						</div>
					</div>
					
					<div class="user-actions">
						@if($user->role === 'banned')
							<button class="user-btn banned-btn" disabled>Banned</button>
							<a href="{{ route('admin.users.appeal', $user->id) }}" class="user-btn appeal-btn">
								View Appeal
							</a>
						@else
							@if($user->email_verified_at)
								<button class="user-btn activated-btn" disabled>Activated</button>
							@else
								<button class="user-btn unverified-btn" disabled>Unverified</button>
							@endif
							<form method="POST" action="{{ route('admin.users.ban', $user->id) }}" onsubmit="return confirm('Are you sure you want to ban this user?');">
								@csrf
								<button type="submit" class="user-btn ban-btn">Ban User</button>
							</form>
						@endif
						<form method="POST" action="{{ route('admin.users.delete', $user->id) }}" onsubmit="return confirm('WARNING: This will permanently delete this user account and all their data. This action cannot be undone. Are you sure?');">
							@csrf
							@method('DELETE')
							<button type="submit" class="user-btn delete-btn">Remove User</button>
						</form>
					</div>
				</div>
			@endforeach
		</div>

		<!-- Pagination -->
		<div class="users-pagination">
			@if($users->currentPage() > 1)
				<button class="pagination-btn" data-page="{{ $users->currentPage() - 1 }}">Previous</button>
			@else
				<button class="pagination-btn" disabled>Previous</button>
			@endif
			
			<span class="page-info">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
			
			@if($users->currentPage() < $users->lastPage())
				<button class="pagination-btn" data-page="{{ $users->currentPage() + 1 }}">Next</button>
			@else
				<button class="pagination-btn" disabled>Next</button>
			@endif
		</div>
    </div>

	<script>
		let searchTimeout;
		const searchInput = document.getElementById('userSearch');
		const ageFilter = document.getElementById('ageFilter');
		const activeFilter = document.getElementById('activeFilter');
		const sortFilter = document.getElementById('sortFilter');
		const usersGrid = document.getElementById('usersGrid');

		// Debounced search function
		function performSearch() {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(() => {
				const search = searchInput.value;
				const age = ageFilter.value;
				const active = activeFilter.value;
				const sort = sortFilter.value;
				
				fetchUsers(search, age, active, sort);
			}, 300);
		}

		searchInput.addEventListener('input', performSearch);
		ageFilter.addEventListener('input', performSearch);
		activeFilter.addEventListener('change', performSearch);
		sortFilter.addEventListener('change', performSearch);

		function fetchUsers(search = '', age = '', active = '', sort = 'newest') {
			const url = new URL('{{ route("admin.users.search") }}');
			url.searchParams.append('search', search);
			url.searchParams.append('age', age);
			url.searchParams.append('active', active);
			url.searchParams.append('sort', sort);

			fetch(url)
				.then(response => response.json())
				.then(data => {
					renderUsers(data.users);
					updatePagination(data.current_page, data.last_page);
				})
				.catch(error => console.error('Error:', error));
		}

		function renderUsers(users) {
			if (users.length === 0) {
				usersGrid.innerHTML = '<div class="no-users">No users found.</div>';
				return;
			}

			usersGrid.innerHTML = users.map(user => {
				// Handle paths that may or may not already include 'storage/' prefix
				let profilePic = '/images/empty.png';
				if (user.profile_picture) {
					if (user.profile_picture.startsWith('storage/')) {
						profilePic = `/${user.profile_picture}`;
					} else {
						profilePic = `/storage/${user.profile_picture}`;
					}
				}
				const isBanned = user.role === 'banned';
				const bannedClass = isBanned ? 'banned-user' : '';
				const bannedOverlay = isBanned ? '<img src="/images/banned.png" class="banned-overlay" alt="Banned">' : '';
				const onlineClass = user.is_online ? 'online' : 'offline';
				const userActions = isBanned 
					? `<div class="user-actions">
							<button class="user-btn banned-btn" disabled>Banned</button>
							<a href="/admin/users/${user.id}/appeal" class="user-btn appeal-btn">View Appeal</a>
							<form method="POST" action="/admin/users/${user.id}" onsubmit="return confirm('WARNING: This will permanently delete this user account and all their data. This action cannot be undone. Are you sure?');">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="_method" value="DELETE">
								<button type="submit" class="user-btn delete-btn">Remove User</button>
							</form>
						</div>`
					: `<div class="user-actions">
							${user.email_verified_at ? '<button class="user-btn activated-btn" disabled>Activated</button>' : '<button class="user-btn unverified-btn" disabled>Unverified</button>'}
							<form method="POST" action="/admin/users/${user.id}/ban" onsubmit="return confirm('Are you sure you want to ban this user?');">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<button type="submit" class="user-btn ban-btn">Ban User</button>
							</form>
							<form method="POST" action="/admin/users/${user.id}" onsubmit="return confirm('WARNING: This will permanently delete this user account and all their data. This action cannot be undone. Are you sure?');">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="_method" value="DELETE">
								<button type="submit" class="user-btn delete-btn">Remove User</button>
							</form>
						</div>`;
				const statusText = user.is_online ? 'Online' : 'Offline';
				const statusClass = user.is_online ? 'status-online' : 'status-offline';
				const birthday = new Date(user.birthday);
				const age = Math.floor((new Date() - birthday) / (365.25 * 24 * 60 * 60 * 1000));
				
				return `
					<div class="user-tile ${bannedClass}">
						<div class="user-profile-pic">
							<img src="${profilePic}" alt="${user.username}">
							${bannedOverlay}
							<div class="online-indicator ${onlineClass}"></div>
						</div>
						
						<div class="user-info">
							<h3 class="user-name">${user.first_name} ${user.last_name}</h3>
							<p class="user-username">@${user.username}</p>
							<p class="user-email">${user.email}</p>
							<p class="user-birthday">Birthday: ${birthday.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</p>
							<p class="user-age">Age: ${age} years old</p>
						</div>
						
						<div class="user-stats">
							<div class="stat-item">
								<span class="stat-label">Last Login:</span>
								<span class="stat-value">${user.last_login_at}</span>
							</div>
							<div class="stat-item">
								<span class="stat-label">Comments:</span>
								<span class="stat-value">${user.comments_count}</span>
							</div>
							<div class="stat-item">
								<span class="stat-label">Status:</span>
								<span class="stat-value ${statusClass}">${statusText}</span>
							</div>
						</div>
						
						${userActions}
					</div>
				`;
			}).join('');
		}

		function updatePagination(currentPage, lastPage) {
			// Pagination logic can be added here if needed
		}
	</script>
@endsection
