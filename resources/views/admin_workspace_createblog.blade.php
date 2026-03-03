@extends('layouts.admin_layout')

@section('content')
    @if (session('success'))
        <div id="popup-success" style="position:fixed;top:50px;left:50%;transform:translateX(-50%);width:300px;height:200px;background:#e6ffe6;border:2px solid #16a34a;z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 2px 16px rgba(0,0,0,0.2);">
            <div style="padding:20px;text-align:center;color:#166534;font-size:18px;">{{ session('success') }}</div>
            <button onclick="document.getElementById('popup-success').style.display='none'" style="margin-bottom:20px;padding:8px 24px;background:#16a34a;color:#fff;border:none;border-radius:4px;cursor:pointer;">Okay</button>
        </div>
    @endif
    @if ($errors->any() || session('error'))
        <div id="popup-error" style="position:fixed;top:50px;left:50%;transform:translateX(-50%);width:300px;min-height:200px;max-height:500px;background:#fff0f0;border:2px solid #db2777;z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 2px 16px rgba(0,0,0,0.2);overflow-y:auto;">
            <div style="padding:20px;text-align:center;color:#b91c1c;font-size:18px;">
                @if (session('error'))
                    {{ session('error') }}
                @endif
                @if ($errors->any())
                    <ul style="margin:0;padding:0;list-style:none;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <button onclick="document.getElementById('popup-error').style.display='none'" style="margin-bottom:20px;padding:8px 24px;background:#db2777;color:#fff;border:none;border-radius:4px;cursor:pointer;">I Understand</button>
        </div>
    @endif
    <div class="workspace">
        <div class="workspace-header">
            <h1 class="admin-header"><img src="/images/menu2.png" class="admin-h-icn">Dashboard<span class="slash">/</span>Workspace<span class="slash">/</span> <span id="hh">Create Blog</span></h1>
            <h3 class="admin-subheader" id="form-mode-label">Create Blog</h3>
        </div>
        <div class="workspace-editor">
            <div class="editor-left">
                <div class="editor-form-window">
                    <form id="blog-editor-form" action="{{ route('blogs.store') }}" method="post" enctype="multipart/form-data" data-store-route="{{ route('blogs.store') }}">
                        @csrf
                        <input type="hidden" id="old-thumbnail" name="old_thumbnail" value="">
                        <div class="drop-image-box" id="dropImageBox">
                            <input type="file" id="dropImageInput" name="thumbnail" accept="image/*" style="display:none">
                            <button type="button" class="remove-image" style="display:none">Remove</button>
                            <div class="placeholder">
                                <svg width="64" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="14" rx="2" stroke="#9C6D55" stroke-width="1.5" fill="none"/>
                                    <circle cx="8.5" cy="9.5" r="1.5" fill="#9C6D55"/>
                                    <path d="M21 19l-6-8-5 6-3-4L3 19" stroke="#9C6D55" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div>Click or drop an image here</div>
                            </div>
                        </div>
                        <div class="editor-form-group">
                            <div class="tag-editor">
                                <h1>Add New Tags</h1>
                                <input type="text" id="tag-input" class="tag-input" placeholder="Enter tag and press Add" autocomplete="off">
                                <button type="button" id="add-tag-btn" class="add-tag-btn">Add Tag</button>
                            </div>
                            <div class="tag-display">
                                <h1>Selected Tags</h1>
                                <ul id="tag-display-list" class="tag-display-list"></ul>
                            </div>
                            <div id="tags-hidden-container"></div>
                            <!-- Hidden tags input for form submission -->
                            <input type="hidden" name="tags[]" id="tags-hidden-input">
                        </div>
                        <div class="editor-form-group">
                            <div class="tools">
                                <h1>Draft Post Editor</h1>
                            </div>
                            <div class="tool-body">
                                <div class="tool-item">
                                    <input type="text" id="title" name="title" required placeholder="Enter Title Here">
                                </div>
                                <div class="tool-item">
                                    <div class="tool-item-header">
                                        <button type="button" id="bold-btn">B</button>
                                        <button type="button" id="italic-btn">I</button>
                                        <button type="button" id="underline-btn">U</button>
                                        <button type="button" id="justify-left-btn"><img src="/images/t5.png" class="tool-icn"></button>
                                        <button type="button" id="justify-center-btn"><img src="/images/t6.png" class="tool-icn"></button>
                                        <button type="button" id="justify-right-btn"><img src="/images/t7.png" class="tool-icn"></button>
                                        <button type="button" id="justify-full-btn"><img src="/images/t8.png" class="tool-icn"></button>
                                        <button type="button" id="t1-btn"><img src="/images/t1.png" class="tool-icn"></button>
                                        <button type="button" id="t2-btn"><img src="/images/t2.png" class="tool-icn"></button>
                                        <button type="button" id="t3-btn"><img src="/images/t3.png" class="tool-icn">
                                            <div id="table-picker" class="table-picker" aria-hidden="true">
                                                <div class="picker-panel">
                                                    <div class="picker-grid" id="picker-grid" role="grid" aria-label="Table size picker"></div>
                                                    <div class="picker-footer">
                                                        <span id="picker-dims">1 x 1</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                        <button type="button" id="t4-btn"><img src="/images/t4.png" class="tool-icn"></button>
                                        <button type="button" id="t5-btn">A <span class="underline"></span>
                                            <div id="color-palette" class="color-palette" aria-hidden="true">
                                                <div class="color-swatch" data-color="#23130B" style="background:#23130B"></div>
                                                <div class="color-swatch" data-color="#000000" style="background:#000000"></div>
                                                <div class="color-swatch" data-color="#9C6D55" style="background:#9C6D55"></div>
                                                <div class="color-swatch" data-color="#E8B400" style="background:#E8B400"></div>
                                                <div class="color-swatch" data-color="#D97706" style="background:#D97706"></div>
                                                <div class="color-swatch" data-color="#16A34A" style="background:#16A34A"></div>
                                                <div class="color-swatch" data-color="#2563EB" style="background:#2563EB"></div>
                                                <div class="color-swatch" data-color="#7C3AED" style="background:#7C3AED"></div>
                                                <div class="color-swatch" data-color="#DB2777" style="background:#DB2777"></div>
                                                    <div class="color-swatch" data-color="#FFFFFF" style="background:#FFFFFF; border:1px solid #ddd"></div>

                                                    <input type="text" id="color-hex-input" class="color-hex-input" placeholder="#RRGGBB" maxlength="7" aria-label="Hex color input" autocomplete="off">
                                                </div>
                                        </button>
                                        <button type="button" id="font-size-decrease-btn" title="Decrease Font Size">A-</button>
                                        <button type="button" id="font-size-increase-btn" title="Increase Font Size">A+</button>
                                        <select id="font-family-select" title="Font Family">
                                            <option value="">Font</option>
                                            <option value="'FoodBuka', sans-serif">FoodBuka</option>
                                            <option value="'Jua', sans-serif">Jua</option>
                                            <option value="'Inter', sans-serif">Inter</option>
                                        </select>
                                    </div>
                                    <div id="editor" class="editor-content" contenteditable="true" aria-label="Post editor" spellcheck="true"></div>
                                    <!-- hidden field to submit HTML content -->
                                    <textarea id="content" name="content" style="display:none"></textarea>
                                    <!-- hidden image input for inserting images -->
                                    <input type="file" id="editor-image-input" accept="image/*" style="display:none">
                                </div>
                            </div>
                            <div class="schedule-section" id="schedule-section">
                                <label for="scheduled_at">Schedule Post For:</label>
                                <input type="datetime-local" id="scheduled_at" name="scheduled_at" placeholder="Select date and time">
                            </div>
                            <div class="tool-footer">
                                <button type="submit" name="action" value="Draft" class="edit-btn-save">Save as Draft</button>
                                <button type="submit" name="action" value="Scheduled" class="edit-btn-schedule">Schedule</button>
                                <button type="submit" name="action" value="Published" class="edit-btn-publish">Publish</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="editor-right">
                <span class="draft-list-header">
                    <p>Pending</p>
                    <p>({{ \App\Models\Blog::whereIn('blog_status', ['draft', 'scheduled'])->where('user_id', Auth::id())->count() }})</p>
                    <span class="filter-drafts">
                        <select id="draft-sort-select">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="az">Title A-Z</option>
                            <option value="za">Title Z-A</option>
                        </select>
                    </span>
                </span>
                <div class="draft-search-bar">
                    <input type="text" id="draft-search-input" placeholder="Search Drafts...">
                    <button id="draft-search-btn"><img src="/images/search2.png" class="d-search"></button>
                </div>
                <div class="draft-list">
                    <ul id="draft-list-ul">
                        @foreach ($drafts as $draft)
                        <li class="draft-item" 
                            data-title="{{ strtolower($draft->blog_title) }}"
                            data-author="{{ strtolower($draft->user->first_name . ' ' . $draft->user->last_name) }}"
                            data-date="{{ $draft->created_at->timestamp }}">
                            <div class="draft-thumbnail">
                                @if ($draft->thumbnail && file_exists(public_path($draft->thumbnail)))
                                    <img src="{{ asset($draft->thumbnail) }}" alt="Draft Thumbnail" class="draft-thumb-img">
                                @else
                                    <img src="/images/empty.png" alt="Draft Thumbnail" class="draft-thumb-img">
                                @endif
                            </div>
                            <div class="draft-info">
                                <p class="draft-title">
                                    <span class="title-text">{{ $draft->blog_title }}</span>
                                    @if($draft->blog_status === 'scheduled')
                                        <span class="draft-status-badge scheduled">Scheduled</span>
                                    @endif
                                </p>
                                <p>By:{{ $draft->user->first_name }} {{ $draft->user->last_name }}</p>
                                <p>{{ $draft->created_at->format('M d, Y') }}</p>
                                @if($draft->blog_status === 'scheduled' && $draft->scheduled_at)
                                    <p>Scheduled For</br>{{ $draft->scheduled_at->format('M d, Y h:i a') }}</p>
                                @else
                                    <p>Last Modified</br>{{ $draft->updated_at->format('Y-m-d h:i a') }}</p>
                                @endif
                                <div class="draft-btn-holder">
                                    <button type="button" class="draft-action-btn">
                                        <img src="/images/t9.png" alt="">
                                    </button>
                                    <div class="delete-options">
                                         <form action="{{ route('blogs.trash', $draft->id) }}" method="post">
                                            @csrf
                                            <button type="submit" class="edit-btn">Move to Trash</button>
                                        </form>
                                        <form action="{{ route('blogs.destroy', $draft->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this draft?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn">Delete Draft</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="edit-draft">
                                    <button type="button" class="edit-draft-btn" data-blog-id="{{ $draft->id }}">Edit</button>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    
@endsection

