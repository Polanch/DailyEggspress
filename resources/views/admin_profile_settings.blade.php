@extends('layouts.admin_layout')

@section('content')
    <style>
        .profile-settings {
            max-width: 800px;
            padding: 2rem;
            background: #F0DED2;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin: 20px auto;
        }

        .profile-settings h2 {
            margin-bottom: 2rem;
            color: #23130B;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 2rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #23130B;
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
            box-sizing: border-box;
        }

        .form-group input[type="file"] {
            padding: 0.5rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #E8B400;
            box-shadow: 0 0 0 3px rgba(232, 180, 0, 0.1);
        }

        .profile-pic-preview {
            margin-top: 1rem;
            max-width: 200px;
        }

        .profile-pic-preview img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            border: 2px solid #E8B400;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-save {
            background: #E8B400;
            color: #23130B;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .btn-save:hover {
            background: #d4a000;
        }

        .btn-cancel {
            background: #ddd;
            color: #23130B;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cancel:hover {
            background: #ccc;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .current-pic-section {
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f8f8;
            border-radius: 4px;
        }

        .current-pic-section p {
            color: #666;
            margin-bottom: 1rem;
        }

        .current-pic-section img {
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            border: 2px solid #E8B400;
        }

        .help-text {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.5rem;
        }
    </style>

    <div class="profile-settings">
        <h2>Profile Settings - {{ ucfirst(Auth::user()->role) }}</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="current-pic-section">
                <p><strong>Current Profile Picture:</strong></p>
                @if(Auth::user()->profile_picture)
                    <img src="{{ asset(Auth::user()->profile_picture) }}" alt="Current Profile Picture">
                @else
                    <img src="/images/user.png" alt="Default Profile Picture">
                @endif
            </div>

            <div class="form-group">
                <label for="profile_picture">Upload New Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(event)">
                <div class="help-text">Supported formats: JPG, PNG, GIF, WebP. Max 5MB. Image will be converted to WebP.</div>
            </div>

            <div id="preview-container" style="display: none;">
                <p style="color: #23130B; margin-bottom: 1rem;"><strong>Preview:</strong></p>
                <div class="profile-pic-preview">
                    <img id="preview-image" src="" alt="Preview">
                </div>
            </div>

            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="{{ Auth::user()->first_name }}" placeholder="Enter first name">
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="{{ Auth::user()->last_name }}" placeholder="Enter last name">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" placeholder="Enter email" readonly style="background: #f8f8f8; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ Auth::user()->username }}" placeholder="Username" readonly style="background: #f8f8f8; cursor: not-allowed;">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save">Save Changes</button>
                <a href="/admin/dashboard" class="btn-cancel">Back to Dashboard</a>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview-image');
                    const container = document.getElementById('preview-container');
                    preview.src = e.target.result;
                    container.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
