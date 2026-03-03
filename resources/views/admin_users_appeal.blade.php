@extends('layouts.admin_layout')

@section('content')
    <div class="admin-users-appeal">
		<div class="posts-header">
			<h1 class="admin-header">
                <img src="/images/menu6.png" class="admin-h-icn">Dashboard<span class="slash">/</span> <a href="{{ route('admin.users') }}" style="color: inherit; text-decoration: none;">Users</a><span class="slash">/</span> <span id="hh">Appeal</span></h1>
			<h3 class="admin-subheader">User Ban Appeal</h3>
		</div>
		
		<div class="appeal-container">
			<div class="user-info-section">
				<h2>User Information</h2>
				<div class="user-details">
					<div class="user-avatar">
						<img src="{{ $user->profile_picture ? (Str::startsWith($user->profile_picture, 'storage/') ? asset($user->profile_picture) : asset('storage/' . $user->profile_picture)) : '/images/empty.png' }}" alt="{{ $user->username }}">
					</div>
					<div class="user-data">
						<p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
					<p><strong>Username:</strong> {{ '@' . $user->username }}</p>
					<p><strong>Email:</strong> {{ $user->email }}</p>
					<p><strong>Banned Since:</strong> {{ $bannedComment ? $bannedComment->created_at->format('M d, Y h:i A') : $user->updated_at->format('M d, Y h:i A') }}</p>
					</div>
				</div>
			</div>
			
			<div class="banned-comment-section">
				<h2>Ban Reason</h2>
				<div class="comment-box">
					@if($bannedComment)
						<p class="comment-blog"><strong>Blog:</strong> {{ $bannedComment->blog?->title ?? 'N/A' }}</p>
						<p class="comment-text">{{ $user->banned_comment_text ?? $bannedComment->comment }}</p>
					@else
						<p class="comment-text">{{ $user->banned_comment_text ?? 'User was banned directly by admin.' }}</p>
					@endif
				</div>
			</div>
			
			<div class="appeal-section">
				<h2>Appeal Message</h2>
				@if($appealedAt)
					<div class="appeal-box">
						<p class="appeal-text">{{ $appealMessage }}</p>
						<p class="appeal-date">
							<img src="/images/checkmark.png" alt="Submitted" style="width: 16px; height: 16px; vertical-align: middle;">
							Submitted on {{ $appealedAt->format('M d, Y h:i A') }}
						</p>
					</div>
				@else
					<div class="no-appeal">
						<p>This user has not submitted an appeal yet.</p>
					</div>
				@endif
			</div>
			
			<div class="appeal-actions">
				<form action="{{ route('admin.users.unban', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to unban this user?');">
					@csrf
					<button type="submit" class="btn-unban">Unban User</button>
				</form>
				<a href="{{ route('admin.users') }}" class="btn-back">Back to Users</a>
			</div>
		</div>
    </div>
@endsection
