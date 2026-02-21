@extends('layouts.admin_layout')

@section('content')
    <div class="workspace">
        <div class="workspace-header">
            <h1 class="admin-header"><img src="/images/menu1.png" class="admin-h-icn">Dashboard<span class="slash">/</span>Workspace<span class="slash">/</span> <span id="hh">Create Blog</span></h1>
            <h3 class="admin-subheader">Create Blog</h3>
        </div>
        <div class="workspace-editor">
            <div class="editor-left">
                <div class="drop-image-box" id="dropImageBox">
                    <input type="file" id="dropImageInput" accept="image/*" style="display:none">
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
                <div class="editor-form-window">
                    <form action="" method="post">
                        @csrf
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
                                    </div>
                                    <div id="editor" class="editor-content" contenteditable="true" aria-label="Post editor" spellcheck="true"></div>
                                    <!-- hidden field to submit HTML content -->
                                    <textarea id="content" name="content" style="display:none"></textarea>
                                    <!-- hidden image input for inserting images -->
                                    <input type="file" id="editor-image-input" accept="image/*" style="display:none">
                                </div>
                            </div>
                            <div class="tool-footer">
                                <button type="submit" name="action" value="Draft" class="edit-btn-save">Save as Draft</button>
                                <button type="submit" name="action" value="Published" class="edit-btn-publish">Publish</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="editor-right">
                <h1>yes</h1>
            </div>
        </div>
    </div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('tag-input');
    const addBtn = document.getElementById('add-tag-btn');
    const list = document.getElementById('tag-display-list');
    const hiddenContainer = document.getElementById('tags-hidden-container');
    let idCounter = 0;

    function addTag(text) {
        text = (text || '').trim();
        if (!text) return;
        // prevent duplicates
        const existing = Array.from(hiddenContainer.querySelectorAll('input[name="tags[]"]')).some(i => i.value === text);
        if (existing) { input.value = ''; return; }

        const id = 'tag-' + (++idCounter);

        const li = document.createElement('li');
        li.dataset.id = id;
        const span = document.createElement('span');
        span.textContent = text;
        li.appendChild(span);

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'remove-tag-btn';
        btn.setAttribute('aria-label', 'Remove tag');
        btn.textContent = '×';
        btn.addEventListener('click', function () { removeTag(id); });
        li.appendChild(btn);

        list.appendChild(li);

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'tags[]';
        hidden.value = text;
        hidden.id = 'hidden-' + id;
        hidden.dataset.id = id;
        hiddenContainer.appendChild(hidden);

        input.value = '';
        input.focus();
    }

    function removeTag(id) {
        const li = list.querySelector('li[data-id="' + id + '"]');
        if (li) li.remove();
        const hidden = hiddenContainer.querySelector('input[data-id="' + id + '"]');
        if (hidden) hidden.remove();
    }

    addBtn.addEventListener('click', function () { addTag(input.value); });
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); addTag(input.value); } });
});
</script>