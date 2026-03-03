// Helper to get CSRF token from meta tag
function getCsrfToken() {
	const token = document.querySelector('meta[name="csrf-token"]');
	return token ? token.getAttribute('content') : '';
}
// Example usage for image upload fetch
function uploadBlogImage(formData) {
	return fetch('/blog-image-upload', {
		method: 'POST',
		headers: {
			'X-CSRF-TOKEN': getCsrfToken()
		},
		body: formData,
		credentials: 'same-origin' // Ensure cookies/session are sent
	});
}

// Replace your fetch('/blog-image-upload', ...) calls with uploadBlogImage(formData)
// and ensure your HTML <head> includes:
// <meta name="csrf-token" content="{{ csrf_token() }}">
document.addEventListener('DOMContentLoaded', function () {
	// Auto-dismiss alerts after 2 seconds
	var alerts = document.querySelectorAll('.alert, .trash-alert, .posts-alert, .appeal-alert, .alert-success, .alert-error, .alert-danger');
	alerts.forEach(function(alert) {
		setTimeout(function() {
			alert.style.transition = 'opacity 0.25s ease';
			alert.style.opacity = '0';
			setTimeout(function() {
				alert.style.display = 'none';
			}, 250);
		}, 2000);
	});

	const menuBtn = document.querySelector('.menu-btn');
	if (!menuBtn) return;

	// Initialize aria state
	menuBtn.setAttribute('aria-pressed', 'false');

	menuBtn.addEventListener('click', function (e) {
		// Toggle the `active` class on the body (or any page wrapper)
		const isActive = document.body.classList.toggle('active');

		// Reflect state on the button for accessibility
		menuBtn.setAttribute('aria-pressed', String(!!isActive));

		// Optionally toggle a class on the button for visual change
		menuBtn.classList.toggle('is-active', !!isActive);
	});

		// Close sidebar when clicking outside it (only on small screens)
		document.addEventListener('click', function (e) {
			// Only act if sidebar is currently open
			if (!document.body.classList.contains('active')) return;
			// Restrict behavior to small screens matching the CSS media query
			if (!window.matchMedia('(max-width: 600px)').matches) return;

			// If the click was inside the sidebar or on the menu button, do nothing
			if (e.target.closest('.side-nav') || e.target.closest('.menu-btn')) return;

			// Otherwise close the sidebar
			document.body.classList.remove('active');
			menuBtn.setAttribute('aria-pressed', 'false');
			menuBtn.classList.remove('is-active');
		});
});

// Tab option selection with animation
document.addEventListener('DOMContentLoaded', function () {
	const tBox = document.querySelector('.t-box');
	const options = Array.from(document.querySelectorAll('.t-option'));
	if (!options.length || !tBox) return;

	// Disable transitions initially
	tBox.classList.add('no-animation');
	
	// Enable transitions after a short delay
	setTimeout(() => {
		tBox.classList.remove('no-animation');
	}, 100);

	options.forEach(btn => {
		btn.addEventListener('click', function () {
			const clicked = this;
			const current = options.find(opt => opt.classList.contains('active'));
			
			// If clicking the same button, do nothing
			if (current === clicked) return;
			
			// Clear all exiting classes
			options.forEach(opt => opt.classList.remove('exiting'));
			
			// If there's a currently active button, remove it and mark as exiting
			if (current) {
				current.classList.add('exiting');
				current.classList.remove('active');
				
				// Remove exiting class after transition completes
				setTimeout(() => {
					current.classList.remove('exiting');
				}, 300);
			}
			
			// Add active class to clicked button
			clicked.classList.remove('exiting');
			clicked.classList.add('active');
		});
	});
});


(function(){
	const box = document.getElementById('dropImageBox');
	if (!box) return; // Only run this block if the image box exists (i.e., on create blog page)
	const input = document.getElementById('dropImageInput');
	const removeBtn = box.querySelector('.remove-image');
	const placeholder = box.querySelector('.placeholder');
	const oldThumbnailInput = document.getElementById('old-thumbnail');
	let currentThumbnailPath = null; // Track the current thumbnail for deletion

	function setPreview(src){
		if(src){
			box.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(${src})`;
			box.style.backgroundSize = 'cover';
			box.style.backgroundPosition = 'center';
			placeholder.style.display = 'none';
			removeBtn.style.display = '';
			removeBtn.textContent = 'Change Image';
		} else {
			box.style.backgroundImage = '';
			placeholder.style.display = '';
			removeBtn.style.display = 'none';
		}
	}

	function handleFiles(files){
		if(!files || !files[0]) return;
		const file = files[0];
		if(!file.type.startsWith('image/')) return alert('Please select an image.');
		const reader = new FileReader();
		reader.onload = e => {
			setPreview(e.target.result);
			// If there's a current thumbnail, mark it for deletion
			if(currentThumbnailPath && oldThumbnailInput){
				oldThumbnailInput.value = currentThumbnailPath;
			}
		};
		reader.readAsDataURL(file);
		// Set the file to the input for form submission
		// Create a DataTransfer to assign the file to the input
		const dt = new DataTransfer();
		dt.items.add(file);
		input.files = dt.files;
	}

  box.addEventListener('click', ()=> input.click());
  input.addEventListener('change', ()=> handleFiles(input.files));

  ['dragenter','dragover'].forEach(ev=>{
    box.addEventListener(ev, e=>{
      e.preventDefault(); e.stopPropagation();
      box.classList.add('dragover');
    });
  });
  ['dragleave','drop'].forEach(ev=>{
    box.addEventListener(ev, e=>{
      e.preventDefault(); e.stopPropagation();
      box.classList.remove('dragover');
    });
  });
	box.addEventListener('drop', e=>{
		const dt = e.dataTransfer;
		if(dt && dt.files && dt.files.length) handleFiles(dt.files);
	});

  removeBtn.addEventListener('click', e=>{
    e.stopPropagation();
    input.click();
  });
	// Debug: log input status before form submit
	const form = input.closest('form');
	if (form) {
		form.addEventListener('submit', function(e) {
			console.log('Submitting form...');
			console.log('Input present in form:', form.contains(input));
			console.log('Input files:', input.files);
			if (input.files.length === 0 && input.value === '') {
				// No file selected, allow submit
				return;
			}
		});
	}

	// Expose setter for edit mode
	window.setCurrentThumbnailPath = function(path) {
		currentThumbnailPath = path;
	};
})();

(function(){
	// new rich editor using contenteditable and execCommand fallbacks
	const t5Btn = document.getElementById('t5-btn');
	const palette = document.getElementById('color-palette');
	const editor = document.getElementById('editor');
	const hiddenContent = document.getElementById('content');
	const underline = t5Btn ? t5Btn.querySelector('.underline') : null;
	const imageInput = document.getElementById('editor-image-input');

	if(!editor) return;

	// focus editor on load
	editor.addEventListener('focus', ()=>{
		document.execCommand('styleWithCSS', false, false);
	});

	// --- palette placement & behavior (unchanged placement logic) ---
	function measureAndPlace(){
		palette.style.display = 'flex';
		palette.style.visibility = 'hidden';
		palette.classList.remove('below','above','left','right');
		const btnRect = t5Btn.getBoundingClientRect();
		const palRect = palette.getBoundingClientRect();
		const spaceBelow = window.innerHeight - btnRect.bottom;
		const spaceAbove = btnRect.top;
		const spaceRight = window.innerWidth - btnRect.right;
		const spaceLeft = btnRect.left;
		let placement = 'below';
		if(spaceBelow >= palRect.height + 10) placement = 'below';
		else if(spaceAbove >= palRect.height + 10) placement = 'above';
		else if(spaceRight >= palRect.width + 10) placement = 'right';
		else if(spaceLeft >= palRect.width + 10) placement = 'left';
		palette.classList.add(placement);
		palette.style.visibility = '';
	}

	t5Btn.addEventListener('click', function(e){
		e.stopPropagation();
		const isOpen = palette.classList.contains('open');
		if(isOpen){
			palette.classList.remove('open','below','above','left','right');
			palette.setAttribute('aria-hidden','true');
			palette.style.display = '';
		} else {
			palette.classList.add('open');
			palette.setAttribute('aria-hidden','false');
			measureAndPlace();
		}
	});

	document.addEventListener('click', function(){
		palette.classList.remove('open','below','above','left','right');
		palette.setAttribute('aria-hidden','true');
		palette.style.display = '';
		// also close table picker if open
		try{
			if(typeof tablePicker !== 'undefined' && tablePicker){
				tablePicker.classList.remove('open');
				tablePicker.setAttribute('aria-hidden','true');
				tablePicker.style.display = '';
			}
		}catch(e){/* ignore */}
	});

	window.addEventListener('resize', function(){ if(palette.classList.contains('open')) measureAndPlace(); });

	// color pick: use document.execCommand('foreColor') to color selection
	palette.addEventListener('click', function(e){
		e.stopPropagation();
		const sw = e.target.closest('.color-swatch');
		if(!sw) return;
		const color = sw.getAttribute('data-color');
		editor.focus();
		try{ document.execCommand('foreColor', false, color); }catch(err){}
		if(underline) underline.style.backgroundColor = color;
		Array.from(palette.querySelectorAll('.color-swatch')).forEach(s=>s.classList.remove('selected'));
		sw.classList.add('selected');
	});

	// hex input behavior: apply color immediately using execCommand
	const hexInput = document.getElementById('color-hex-input');
	if(hexInput){
		hexInput.addEventListener('keydown', function(ev){ if(ev.key === 'Enter'){ ev.preventDefault(); this.blur(); } ev.stopPropagation(); });
		hexInput.addEventListener('click', function(ev){ ev.stopPropagation(); });
		function normalizeHex(v){ if(!v) return null; v=v.trim(); if(!v.startsWith('#')) v='#'+v; if(/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(v)){ if(v.length===4){ const r=v[1],g=v[2],b=v[3]; v='#'+r+r+g+g+b+b;} return v.toUpperCase(); } return null; }
		hexInput.addEventListener('input', function(){ const val = normalizeHex(this.value); if(val){ editor.focus(); try{ document.execCommand('foreColor', false, val); }catch(err){} if(underline) underline.style.backgroundColor = val; Array.from(palette.querySelectorAll('.color-swatch')).forEach(s=>s.classList.remove('selected')); } });
	}

	// toolbar commands using execCommand
	function exec(cmd, value){
		editor.focus();
		try{ document.execCommand(cmd, false, value); }
		catch(err){ console.warn('execCommand failed', cmd); }
		editor.focus();
		if(typeof updateToolbarState === 'function') setTimeout(updateToolbarState, 0);
	}

	// bold/italic/underline wired to execCommand
	const boldBtn = document.getElementById('bold-btn'); if(boldBtn) boldBtn.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); exec('bold'); });
	const italicBtn = document.getElementById('italic-btn'); if(italicBtn) italicBtn.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); exec('italic'); });
	const underlineBtn = document.getElementById('underline-btn'); if(underlineBtn) underlineBtn.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); exec('underline'); });
	
	// Helper function to apply text alignment (works in tables too)
	function applyTextAlign(alignment) {
		const sel = window.getSelection();
		if(!sel.rangeCount) return;
		
		// Find if cursor is inside a table cell
		let node = sel.anchorNode;
		let cell = null;
		
		while(node && node !== editor) {
			if(node.nodeType === 1 && (node.tagName === 'TD' || node.tagName === 'TH')) {
				cell = node;
				break;
			}
			node = node.parentNode;
		}
		
		if(cell) {
			// Apply alignment to the table cell
			cell.style.textAlign = alignment;
			editor.focus();
		} else {
			// Apply to paragraph/div using execCommand
			const commands = {
				'left': 'justifyLeft',
				'center': 'justifyCenter',
				'right': 'justifyRight',
				'justify': 'justifyFull'
			};
			exec(commands[alignment]);
		}
	}
	
	const justifyLeftBtn = document.getElementById('justify-left-btn'); 
	if(justifyLeftBtn) justifyLeftBtn.addEventListener('click', function(e){ 
		e.preventDefault(); 
		e.stopPropagation(); 
		applyTextAlign('left'); 
	});
	
	const justifyCenterBtn = document.getElementById('justify-center-btn'); 
	if(justifyCenterBtn) justifyCenterBtn.addEventListener('click', function(e){ 
		e.preventDefault(); 
		e.stopPropagation(); 
		applyTextAlign('center'); 
	});
	
	const justifyRightBtn = document.getElementById('justify-right-btn'); 
	if(justifyRightBtn) justifyRightBtn.addEventListener('click', function(e){ 
		e.preventDefault(); 
		e.stopPropagation(); 
		applyTextAlign('right'); 
	});
	
	const justifyFullBtn = document.getElementById('justify-full-btn'); 
	if(justifyFullBtn) justifyFullBtn.addEventListener('click', function(e){ 
		e.preventDefault(); 
		e.stopPropagation(); 
		applyTextAlign('justify'); 
	});

	// Font size increase/decrease
	const fontSizes = [12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72]; // Standard document font sizes
	
	const fontSizeIncreaseBtn = document.getElementById('font-size-increase-btn');
	const fontSizeDecreaseBtn = document.getElementById('font-size-decrease-btn');
	
	if(fontSizeIncreaseBtn) {
		fontSizeIncreaseBtn.addEventListener('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			const sel = window.getSelection();
			if(sel.rangeCount > 0 && !sel.isCollapsed){
				const range = sel.getRangeAt(0);
				const span = document.createElement('span');
				
				// Get current font size or default to 16
				const parentElement = range.commonAncestorContainer.parentElement;
				const currentSize = parseInt(window.getComputedStyle(parentElement).fontSize) || 16;
				
				// Find next larger size
				let newSize = fontSizes.find(size => size > currentSize) || fontSizes[fontSizes.length - 1];
				
				span.style.fontSize = newSize + 'px';
				try{
					range.surroundContents(span);
				}catch(err){
					// Fallback
					document.execCommand('fontSize', false, '7');
				}
			}
			editor.focus();
		});
	}
	
	if(fontSizeDecreaseBtn) {
		fontSizeDecreaseBtn.addEventListener('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			const sel = window.getSelection();
			if(sel.rangeCount > 0 && !sel.isCollapsed){
				const range = sel.getRangeAt(0);
				const span = document.createElement('span');
				
				// Get current font size or default to 16
				const parentElement = range.commonAncestorContainer.parentElement;
				const currentSize = parseInt(window.getComputedStyle(parentElement).fontSize) || 16;
				
				// Find next smaller size
				let newSize = fontSizes.slice().reverse().find(size => size < currentSize) || fontSizes[0];
				
				span.style.fontSize = newSize + 'px';
				try{
					range.surroundContents(span);
				}catch(err){
					// Fallback
					document.execCommand('fontSize', false, '1');
				}
			}
			editor.focus();
		});
	}

	// Font family selector
	const fontFamilySelect = document.getElementById('font-family-select');
	if(fontFamilySelect) {
		fontFamilySelect.addEventListener('change', function(e){
			const fontFamily = this.value;
			if(fontFamily){
				editor.focus();
				const sel = window.getSelection();
				if(sel.rangeCount > 0 && !sel.isCollapsed){
					const range = sel.getRangeAt(0);
					const span = document.createElement('span');
					span.style.fontFamily = fontFamily;
					try{
						range.surroundContents(span);
					}catch(err){
						// Fallback to execCommand
						document.execCommand('fontName', false, fontFamily);
					}
				}
				editor.focus();
			}
			// Reset select to default
			this.value = '';
		});
	}

	// lists: unordered and ordered
	const t1 = document.getElementById('t1-btn'); if(t1) t1.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); exec('insertUnorderedList'); });
	const t2 = document.getElementById('t2-btn'); if(t2) t2.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); exec('insertOrderedList'); });

	// t3: table picker (interactive grid) and insertion
	const t3 = document.getElementById('t3-btn');
	const tablePicker = document.getElementById('table-picker');
	const pickerGrid = document.getElementById('picker-grid');
	const pickerDims = document.getElementById('picker-dims');
	// build an 8x6 grid by default
	function buildPicker(cols = 8, rows = 6){
		pickerGrid.innerHTML = '';
		pickerGrid.style.gridTemplateColumns = 'repeat(' + cols + ', 20px)';
		for(let r=1; r<=rows; r++){
			for(let c=1; c<=cols; c++){
				const cell = document.createElement('div');
				cell.className = 'picker-cell';
				cell.dataset.col = String(c);
				cell.dataset.row = String(r);
				pickerGrid.appendChild(cell);
			}
		}
	}
	buildPicker(8,6);

	let currentCols = 1, currentRows = 1;
	pickerGrid.addEventListener('mouseover', function(e){
		const cell = e.target.closest('.picker-cell');
		if(!cell) return;
		const col = parseInt(cell.dataset.col,10)||1;
		const row = parseInt(cell.dataset.row,10)||1;
		currentCols = col; currentRows = row;
		Array.from(pickerGrid.children).forEach(ch=>{
			const c = parseInt(ch.dataset.col,10)||0;
			const r = parseInt(ch.dataset.row,10)||0;
			if(c<=col && r<=row) ch.classList.add('hover'); else ch.classList.remove('hover');
		});
		if(pickerDims) pickerDims.textContent = col + ' x ' + row;
	});

	pickerGrid.addEventListener('click', function(e){
		const cell = e.target.closest('.picker-cell');
		if(!cell) return;
		const cols = parseInt(cell.dataset.col,10)||1;
		const rows = parseInt(cell.dataset.row,10)||1;
		editor.focus();
		// build table with a header row (thead) so header styling can be applied
		let tableHtml = '<table class="inserted-table" border="1" cellpadding="6" style="width:100%; border-collapse:collapse;">';
		// header
		tableHtml += '<thead><tr>';
		for(let cc=0; cc<cols; cc++) tableHtml += '<th>&nbsp;</th>';
		tableHtml += '</tr></thead>';
		// body (remaining rows, if any)
		tableHtml += '<tbody>';
		for(let rr=1; rr<rows; rr++){
			tableHtml += '<tr>';
			for(let cc=0; cc<cols; cc++) tableHtml += '<td>&nbsp;</td>';
			tableHtml += '</tr>';
		}
		tableHtml += '</tbody></table><p><br></p>';
		// simple insertion: insert table HTML at caret
		try{ document.execCommand('insertHTML', false, tableHtml); }
		catch(err){
			// fallback: append at end
			try{ const rng = document.createRange(); rng.selectNodeContents(editor); rng.collapse(false); const sel = window.getSelection(); sel.removeAllRanges(); sel.addRange(rng); document.execCommand('insertHTML', false, tableHtml); }
			catch(e){ editor.insertAdjacentHTML('beforeend', tableHtml); }
		}
		// close picker
		tablePicker.classList.remove('open'); tablePicker.setAttribute('aria-hidden','true'); tablePicker.style.display = '';
	});

	if(t3){
		t3.addEventListener('click', function(e){
			e.preventDefault(); e.stopPropagation();
			const isOpen = tablePicker.classList.contains('open');
			if(isOpen){ tablePicker.classList.remove('open'); tablePicker.setAttribute('aria-hidden','true'); tablePicker.style.display = ''; }
			else { tablePicker.classList.add('open'); tablePicker.setAttribute('aria-hidden','false'); tablePicker.style.display = 'block'; }
		});
	}

	// t4: image insert via hidden file input (wrap image, add resize handles and toolbar)
	const t4 = document.getElementById('t4-btn');
	function wrapExistingImages(){
		const imgs = Array.from(editor.querySelectorAll('img'));
		imgs.forEach(img =>{
			if(img.closest('.img-wrapper')) return; // already wrapped
			const wrapper = document.createElement('div');
			wrapper.className = 'img-wrapper flow-none';
			wrapper.setAttribute('contenteditable','false');
			img.parentNode.insertBefore(wrapper, img);
			wrapper.appendChild(img);
			attachImageControls(wrapper);
			// ensure caret is not placed before the wrapped image when focusing/typing
			try{ setCaretAfter(wrapper); }catch(e){}
		});
	}

	function attachImageControls(wrapper){
		if(wrapper.__controlsAttached) return; wrapper.__controlsAttached = true;
		// add resize handle
		const handle = document.createElement('div'); handle.className = 'img-handle';
		wrapper.appendChild(handle);

		// toolbar (float left/center/right, width input, remove)
		const tb = document.createElement('div'); tb.className = 'image-toolbar';
		const leftBtn = document.createElement('button'); leftBtn.type='button'; leftBtn.title='Float left'; leftBtn.textContent='◧';
		const centerBtn = document.createElement('button'); centerBtn.type='button'; centerBtn.title='Center'; centerBtn.textContent='⎯';
		const rightBtn = document.createElement('button'); rightBtn.type='button'; rightBtn.title='Float right'; rightBtn.textContent='◨';
		const removeBtn = document.createElement('button'); removeBtn.type='button'; removeBtn.title='Remove'; removeBtn.textContent='✕';
		tb.appendChild(leftBtn); tb.appendChild(centerBtn); tb.appendChild(rightBtn); tb.appendChild(removeBtn);
		wrapper.appendChild(tb);

		// show/hide toolbar when wrapper is selected
		wrapper.addEventListener('click', function(e){ e.stopPropagation(); deselectAllWrappers(); wrapper.classList.add('selected'); tb.style.display = 'flex'; });

		// float actions
		leftBtn.addEventListener('click', function(e){ e.stopPropagation(); wrapper.classList.remove('flow-none','flow-right'); wrapper.classList.add('flow-left'); wrapper.style.width = ''; });
		rightBtn.addEventListener('click', function(e){ e.stopPropagation(); wrapper.classList.remove('flow-none','flow-left'); wrapper.classList.add('flow-right'); wrapper.style.width = ''; });
		centerBtn.addEventListener('click', function(e){ e.stopPropagation(); wrapper.classList.remove('flow-left','flow-right'); wrapper.classList.add('flow-none'); wrapper.style.marginLeft = 'auto'; wrapper.style.marginRight = 'auto'; });

		// removed manual width input: sizing is done by dragging the handle

		removeBtn.addEventListener('click', function(e) {
			e.stopPropagation();
			const wrapper = removeBtn.closest('.img-wrapper');
			if (!wrapper) return;
			const filename = wrapper.dataset.filename;
			if (filename) {
				fetch('/remove-image', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': getCsrfToken()
					},
					body: JSON.stringify({ filename })
				})
				.then(res => res.json())
				.then(data => {
					if (data.success) {
						wrapper.remove();
					} else {
						alert('Failed to delete image: ' + (data.message || 'Unknown error'));
					}
				})
				.catch(() => alert('Failed to delete image.'));
			} else {
				wrapper.remove();
			}
		});

		// resize by dragging handle
		let dragging = false, startX = 0, startW = 0;
		handle.addEventListener('mousedown', function(ev){ ev.preventDefault(); ev.stopPropagation(); dragging = true; startX = ev.clientX; startW = wrapper.getBoundingClientRect().width; document.body.style.userSelect='none'; });
		window.addEventListener('mousemove', function(ev){ if(!dragging) return; const dx = ev.clientX - startX; const newW = Math.max(24, startW + dx); wrapper.style.width = newW + 'px'; });
		window.addEventListener('mouseup', function(ev){ if(dragging){ dragging = false; document.body.style.userSelect=''; } });
	}

	function deselectAllWrappers(){ Array.from(editor.querySelectorAll('.img-wrapper.selected')).forEach(w=>{ w.classList.remove('selected'); const tb = w.querySelector('.image-toolbar'); if(tb) tb.style.display='none'; }); }

	// wrap images inserted by file input, and attach controls
	if(t4 && imageInput){ t4.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); imageInput.click(); });
		imageInput.addEventListener('change', function(){
			const f = this.files && this.files[0];
			if(!f) return;
			const formData = new FormData();
			formData.append('image', f);
			// CSRF token for Laravel
			const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
			fetch('/blog-image-upload', {
				method: 'POST',
				headers: token ? { 'X-CSRF-TOKEN': token } : {},
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if(data.url){
					editor.focus();
					const html = `<div class="img-wrapper flow-none" contenteditable="false" data-filename="${data.filename}"><img src="${data.url}" alt="image"></div><p><br></p>`;
					try{ document.execCommand('insertHTML', false, html); }
					catch(err){
						const rng = document.createRange(); rng.selectNodeContents(editor); rng.collapse(false);
						const sel = window.getSelection(); sel.removeAllRanges(); sel.addRange(rng);
						document.execCommand('insertHTML', false, html);
					}
					setTimeout(()=>{
						const wrappers = editor.querySelectorAll('.img-wrapper');
						const w = wrappers[wrappers.length-1];
						if(w) attachImageControls(w);
					}, 0);
				} else {
					alert('Image upload failed.');
				}
			})
			.catch(()=>{ alert('Image upload failed.'); });
			this.value='';
		});
	}

	// helper: set caret position immediately after a node
	function setCaretAfter(node){
		if(!node) return;
		const range = document.createRange();
		const sel = window.getSelection();
		range.setStartAfter(node);
		range.collapse(true);
		sel.removeAllRanges();
		sel.addRange(range);
		// ensure editor has focus so typing continues
		editor.focus();
	}

	// click outside to deselect
	document.addEventListener('click', function(){ deselectAllWrappers(); });

	// on load, wrap any existing images and attach controls
	setTimeout(wrapExistingImages, 0);

	// Tab handling: indent/outdent inside lists
	editor.addEventListener('keydown', function(e){
		if(e.key === 'Tab'){
			e.preventDefault();
			if(e.shiftKey){ exec('outdent'); } else { exec('indent'); }
		}
	});

	// toolbar active state updater (for B/I/U toggles)
	function updateToolbarState(){
		try{
			const boldState = document.queryCommandState('bold');
			const italicState = document.queryCommandState('italic');
			const underlineState = document.queryCommandState('underline');
			const justifyLeftState = document.queryCommandState('justifyLeft');
			const justifyCenterState = document.queryCommandState('justifyCenter');
			const justifyRightState = document.queryCommandState('justifyRight');
			const justifyFullState = document.queryCommandState('justifyFull');
			if(boldBtn) boldBtn.classList.toggle('active', !!boldState);
			if(italicBtn) italicBtn.classList.toggle('active', !!italicState);
			if(underlineBtn) underlineBtn.classList.toggle('active', !!underlineState);
			if(justifyLeftBtn) justifyLeftBtn.classList.toggle('active', !!justifyLeftState);
			if(justifyCenterBtn) justifyCenterBtn.classList.toggle('active', !!justifyCenterState);
			if(justifyRightBtn) justifyRightBtn.classList.toggle('active', !!justifyRightState);
			if(justifyFullBtn) justifyFullBtn.classList.toggle('active', !!justifyFullState);
		}catch(e){ /* ignore */ }
	}

	// update state on selection changes and keyboard events
	editor.addEventListener('keyup', updateToolbarState);
	editor.addEventListener('mouseup', updateToolbarState);
	document.addEventListener('selectionchange', function(){ if(document.activeElement === editor) updateToolbarState(); });

	// initial update
	setTimeout(updateToolbarState, 0);

	// before submit, inline list paddings and copy editor HTML into hidden textarea
	function inlineListStyles(container){
		// for each list element, copy its computed padding-left into an inline style
		const lists = container.querySelectorAll('ul,ol');
		lists.forEach(list => {
			try{
				const cs = window.getComputedStyle(list);
				if(cs && cs.paddingLeft){
					list.style.paddingLeft = cs.paddingLeft;
				}
				// also inline list-style-position to be safe
				if(cs && cs.listStylePosition){ list.style.listStylePosition = cs.listStylePosition; }
				// inline list-style-type as well
				if(cs && cs.listStyleType){ list.style.listStyleType = cs.listStyleType; }
			}catch(e){ /* ignore */ }
		});
	}

	const form = editor.closest('form');
	if(form){
		form.addEventListener('submit', function(){
			// inline styles so saved HTML preserves visual indentation even without the editor stylesheet
			inlineListStyles(editor);
			hiddenContent.value = editor.innerHTML;
		});
	}

	// set initial content if any (existing server-side drafts)
	if(hiddenContent && hiddenContent.value){ editor.innerHTML = hiddenContent.value; }
})();


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

	if (typeof addBtn !== 'undefined' && addBtn) {
		addBtn.addEventListener('click', function () { addTag(input.value); });
	}
	if (typeof input !== 'undefined' && input) {
		input.addEventListener('keydown', function (e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				addTag(input.value);
			}
		});
	}
});

document.addEventListener('DOMContentLoaded', function () {
	console.log('admin_script.js loaded');
	// Use the blog editor form by id for reliable targeting
	const form = document.getElementById('blog-editor-form');
	console.log('Form found:', form);
	if (!form) return;
	let hasUnsaved = false;

	// Listen for changes in title input
	const titleInput = form.querySelector('[name="title"]');
	if (titleInput) {
		titleInput.addEventListener('input', function() {
			if (!hasUnsaved) {
				hasUnsaved = true;
				console.log('Title changed, hasUnsaved set to true');
			}
		});
	}

    // Listen for changes in textarea (hidden content)
    const contentTextarea = form.querySelector('[name="content"]');
	if (contentTextarea) {
		contentTextarea.addEventListener('input', function() {
			if (!hasUnsaved) {
				hasUnsaved = true;
				console.log('Content textarea changed, hasUnsaved set to true');
			}
		});
	}

    // Listen for changes in contenteditable editor
    const editorDiv = document.getElementById('editor');
	if (editorDiv) {
		editorDiv.addEventListener('input', function() {
			if (!hasUnsaved) {
				hasUnsaved = true;
				console.log('Editor input, hasUnsaved set to true');
			}
		});
		editorDiv.addEventListener('keyup', function() {
			if (!hasUnsaved) {
				hasUnsaved = true;
				console.log('Editor keyup, hasUnsaved set to true');
			}
		});
	}

    // Listen for tag changes
	document.getElementById('add-tag-btn')?.addEventListener('click', function() {
		if (!hasUnsaved) {
			hasUnsaved = true;
			console.log('Tag added, hasUnsaved set to true');
		}
	});
	document.getElementById('tag-input')?.addEventListener('input', function() {
		if (!hasUnsaved) {
			hasUnsaved = true;
			console.log('Tag input changed, hasUnsaved set to true');
		}
	});

    // Listen for image changes (thumbnail upload)
    const dropImageInput = document.getElementById('dropImageInput');
	if (dropImageInput) {
		dropImageInput.addEventListener('change', function() {
			if (!hasUnsaved) {
				hasUnsaved = true;
				console.log('Image input changed, hasUnsaved set to true');
			}
		});
	}
    // Listen for image removal
    const removeImageBtn = document.querySelector('.remove-image');
	if (removeImageBtn) {
		removeImageBtn.addEventListener('click', function() {
			if (!hasUnsaved) {
				hasUnsaved = true;
				console.log('Image removed, hasUnsaved set to true');
			}
		});
	}

    // Listen for manual input on the form (fallback)
	form.addEventListener('input', function () {
		if (!hasUnsaved) {
			hasUnsaved = true;
			console.log('Form input, hasUnsaved set to true');
		}
	});

    // Reset hasUnsaved on form submit

	let isSubmitting = false;

	// Set minimum datetime to now (prevent selecting past dates)
	const scheduledAtInput = document.getElementById('scheduled_at');
	const scheduleSection = document.getElementById('schedule-section');
	
	function setMinDateTime() {
		if (scheduledAtInput) {
			const now = new Date();
			// Format: YYYY-MM-DDTHH:MM
			const year = now.getFullYear();
			const month = String(now.getMonth() + 1).padStart(2, '0');
			const day = String(now.getDate()).padStart(2, '0');
			const hours = String(now.getHours()).padStart(2, '0');
			const minutes = String(now.getMinutes()).padStart(2, '0');
			const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
			scheduledAtInput.setAttribute('min', minDateTime);
		}
	}
	
	setMinDateTime();
	// Update min time every minute
	setInterval(setMinDateTime, 60000);

	// Also set isSubmitting to true on click of submit buttons, with a short timeout to ensure it is set before beforeunload
	const draftBtn = form.querySelector('button[name="action"][value="Draft"]');
	const publishBtn = form.querySelector('button[name="action"][value="Published"]');
	const scheduleBtn = form.querySelector('button[name="action"][value="Scheduled"]');
	function removeBeforeUnload() {
		window.removeEventListener('beforeunload', beforeUnloadHandler);
	}
	if (draftBtn) {
		draftBtn.addEventListener('click', function() {
			hasUnsaved = false;
			removeBeforeUnload();
			isSubmitting = true;
			console.log('Draft button clicked, hasUnsaved = false, isSubmitting = true');
		});
	}
	if (publishBtn) {
		publishBtn.addEventListener('click', function() {
			hasUnsaved = false;
			removeBeforeUnload();
			isSubmitting = true;
			console.log('Publish button clicked, hasUnsaved = false, isSubmitting = true');
		});
	}
	if (scheduleBtn) {
		scheduleBtn.addEventListener('click', function(e) {
			const scheduledAtInput = document.getElementById('scheduled_at');
			if (!scheduledAtInput || !scheduledAtInput.value) {
				e.preventDefault();
				alert('Please select a date and time to schedule your post.');
				return false;
			}
			
			// Validate that the selected date is in the future
			const selectedDate = new Date(scheduledAtInput.value);
			const now = new Date();
			if (selectedDate <= now) {
				e.preventDefault();
				alert('Please select a future date and time for scheduling.');
				return false;
			}
			
			hasUnsaved = false;
			removeBeforeUnload();
			isSubmitting = true;
			console.log('Schedule button clicked, hasUnsaved = false, isSubmitting = true');
		});
	}
	form.addEventListener('submit', function() {
		hasUnsaved = false;
		removeBeforeUnload();
		isSubmitting = true;
		console.log('Form submitted, hasUnsaved set to false, isSubmitting = true');
	});

    // Confirmation dialog on page unload/navigation
	function beforeUnloadHandler(e) {
		if (hasUnsaved && !isSubmitting) {
			console.log('beforeunload triggered, hasUnsaved = true');
			const message = 'You have unsaved changes. Your draft will be lost if you leave this page.';
			e.preventDefault();
			e.returnValue = message;
			return message;
		}
	}
	window.addEventListener('beforeunload', beforeUnloadHandler);
});

document.querySelectorAll('.draft-action-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const options = this.parentElement.querySelector('.delete-options');
        const isActive = options.classList.contains('active');
        // Close all delete-options
        document.querySelectorAll('.delete-options.active').forEach(opt => opt.classList.remove('active'));
        // Toggle this one
        if (!isActive) {
            options.classList.add('active');
        }
        // If isActive, it stays closed (toggled off)
    });
});

// Close on outside click
document.addEventListener('click', function() {
    document.querySelectorAll('.delete-options.active').forEach(opt => opt.classList.remove('active'));
});

// ===== EDIT DRAFT FUNCTIONALITY =====
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-draft-btn');
    const form = document.getElementById('blog-editor-form');
	if (!form) return;
    const titleInput = form.querySelector('[name="title"]');
    const contentTextarea = form.querySelector('[name="content"]');
    const editor = document.getElementById('editor');
    const tagInput = document.getElementById('tag-input');
    const dropImageBox = document.getElementById('dropImageBox');
    const tagDisplayList = document.getElementById('tag-display-list');
    const tagsHiddenContainer = document.getElementById('tags-hidden-container');
    const headerSpan = document.getElementById('hh');
    const subheaderLabel = document.getElementById('form-mode-label');
    const draftBtn = form.querySelector('button[name="action"][value="Draft"]');
    const publishBtn = form.querySelector('button[name="action"][value="Published"]');
    const scheduleBtn = form.querySelector('button[name="action"][value="Scheduled"]');
    
    let currentEditingBlogId = null;

    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const blogId = this.getAttribute('data-blog-id');
            loadBlogForEditing(blogId);
        });
    });

	const queryParams = new URLSearchParams(window.location.search);
	const editBlogId = queryParams.get('edit');
	if (editBlogId) {
		loadBlogForEditing(editBlogId);
	}

    function loadBlogForEditing(blogId) {
        // Fetch blog data via AJAX
        fetch(`/admin/blogs/${blogId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to load blog');
            return response.json();
        })
        .then(blog => {
            currentEditingBlogId = blog.id;
            populateFormWithBlog(blog);
			switchToEditMode(blog.id, blog.status);
            scrollToForm();
        })
        .catch(error => {
            console.error('Error loading blog:', error);
            alert('Failed to load blog. Please try again.');
        });
    }

    function populateFormWithBlog(blog) {
        // Set title
        titleInput.value = blog.title || '';

        // Set content in editor
        contentTextarea.value = blog.content || '';
        editor.innerHTML = blog.content || '';

        // Clear and repopulate tags
        tagDisplayList.innerHTML = '';
        tagsHiddenContainer.innerHTML = '';
        if (blog.tags && Array.isArray(blog.tags) && blog.tags.length > 0) {
            blog.tags.forEach(tag => {
                addTagProgrammatically(tag);
            });
        }

        // Set scheduled_at if exists
        const scheduledAtInput = document.getElementById('scheduled_at');
        if (scheduledAtInput && blog.scheduled_at) {
            scheduledAtInput.value = blog.scheduled_at;
        } else if (scheduledAtInput) {
            scheduledAtInput.value = '';
        }

        // Set thumbnail if exists
        if (blog.thumbnail) {
            const imageBox = document.getElementById('dropImageBox');
            const placeholder = imageBox.querySelector('.placeholder');
            const removeBtn = imageBox.querySelector('.remove-image');
            
            imageBox.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(${blog.thumbnail})`;
            imageBox.style.backgroundSize = 'cover';
            imageBox.style.backgroundPosition = 'center';
            placeholder.style.display = 'none';
            removeBtn.style.display = '';
            removeBtn.textContent = 'Change Image';
            
            // Store the current thumbnail path for potential deletion
            window.setCurrentThumbnailPath(blog.thumbnail);
            
            // Clear the file input so we don't re-upload unless changed
            const dropImageInput = document.getElementById('dropImageInput');
            if (dropImageInput) {
                dropImageInput.value = '';
            }
        }
    }

    function addTagProgrammatically(text) {
        // Reuse the tag adding logic from existing code
        text = (text || '').trim();
        if (!text) return;

        // Check for duplicates
        const existing = Array.from(tagsHiddenContainer.querySelectorAll('input[name="tags[]"]')).some(i => i.value === text);
        if (existing) return;

        const idCounter = tagsHiddenContainer.querySelectorAll('input[name="tags[]"]').length;
        const id = 'tag-' + (idCounter + 1);

        // Create list item
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
        btn.addEventListener('click', function() {
            removeTagProgrammatically(id);
        });
        li.appendChild(btn);
        tagDisplayList.appendChild(li);

        // Create hidden input
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'tags[]';
        hidden.value = text;
        hidden.id = 'hidden-' + id;
        hidden.dataset.id = id;
        tagsHiddenContainer.appendChild(hidden);
    }

    function removeTagProgrammatically(id) {
        const li = tagDisplayList.querySelector('li[data-id="' + id + '"]');
        if (li) li.remove();
        const hidden = tagsHiddenContainer.querySelector('input[data-id="' + id + '"]');
        if (hidden) hidden.remove();
    }

	function switchToEditMode(blogId, blogStatus) {
        // Change header
        headerSpan.textContent = 'Edit Blog';
        subheaderLabel.textContent = 'Edit Draft';

        // Update form action and method
        form.action = `/admin/blogs/${blogId}`;
        form.method = 'post'; // Laravel uses POST with _method for PATCH
        
        // Add/update the hidden _method field for PATCH
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PATCH';

		const isPublished = blogStatus === 'published';
		const isScheduled = blogStatus === 'scheduled';

		// Default edit behavior (draft edits): keep all action buttons
		if (draftBtn) {
			draftBtn.style.display = '';
			draftBtn.textContent = 'Save Changes';
			draftBtn.value = 'Draft';
		}
		if (publishBtn) {
			publishBtn.style.display = '';
			publishBtn.textContent = 'Publish Now';
			publishBtn.value = 'Published';
		}
		if (scheduleBtn) {
			scheduleBtn.style.display = '';
			scheduleBtn.textContent = 'Reschedule';
			scheduleBtn.value = 'Scheduled';
		}

		// Requested behavior:
		// - Published: only Save Changes button
		// - Scheduled (Reschedule): only Save Changes button
		if (isPublished) {
			if (draftBtn) draftBtn.style.display = 'none';
			if (scheduleBtn) scheduleBtn.style.display = 'none';
			if (publishBtn) {
				publishBtn.style.display = '';
				publishBtn.textContent = 'Save Changes';
				publishBtn.value = 'Published';
			}
		}

		if (isScheduled) {
			if (draftBtn) draftBtn.style.display = 'none';
			if (publishBtn) publishBtn.style.display = 'none';
			if (scheduleBtn) {
				scheduleBtn.style.display = '';
				scheduleBtn.textContent = 'Save Changes';
				scheduleBtn.value = 'Scheduled';
			}
		}

        // Store blog ID in form for reference
        form.dataset.editingBlogId = blogId;
    }

    function switchToCreateMode() {
        // Change header back
        headerSpan.textContent = 'Create Blog';
        subheaderLabel.textContent = 'Create Blog';

        // Reset form action
        form.action = form.getAttribute('data-store-route') || '/admin/blogs';
        form.method = 'post';

        // Remove _method field
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) {
            methodInput.remove();
        }

        // Reset button labels
        if (draftBtn) {
			draftBtn.style.display = '';
            draftBtn.textContent = 'Save as Draft';
			draftBtn.value = 'Draft';
        }
        if (publishBtn) {
			publishBtn.style.display = '';
            publishBtn.textContent = 'Publish';
			publishBtn.value = 'Published';
        }
        if (scheduleBtn) {
			scheduleBtn.style.display = '';
            scheduleBtn.textContent = 'Schedule';
			scheduleBtn.value = 'Scheduled';
        }

        // Clear blog ID and old_thumbnail
        form.dataset.editingBlogId = '';
        currentEditingBlogId = null;
        const oldThumbnailInput = document.getElementById('old-thumbnail');
        if (oldThumbnailInput) {
            oldThumbnailInput.value = '';
        }
        
        // Clear scheduled_at field
        const scheduledAtInput = document.getElementById('scheduled_at');
        if (scheduledAtInput) {
            scheduledAtInput.value = '';
        }
    }

    function scrollToForm() {
        const editorLeft = document.querySelector('.editor-left');
        if (editorLeft) {
            editorLeft.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Optional: Add a "Create New" button to switch back to create mode
    // You can manually click on the menu or call switchToCreateMode()
});

// Real-time Draft Search and Sorting
document.addEventListener('DOMContentLoaded', function() {
    const draftSearchInput = document.getElementById('draft-search-input');
    const draftSortSelect = document.getElementById('draft-sort-select');
    const draftListUl = document.getElementById('draft-list-ul');
    
    if (!draftSearchInput || !draftSortSelect || !draftListUl) return;
    
    const draftItems = Array.from(document.querySelectorAll('.draft-item'));
    
    // Search functionality
    draftSearchInput.addEventListener('input', function() {
        filterAndSortDrafts();
    });
    
    // Sort functionality
    draftSortSelect.addEventListener('change', function() {
        filterAndSortDrafts();
    });
    
    function filterAndSortDrafts() {
        const searchTerm = draftSearchInput.value.toLowerCase().trim();
        const sortType = draftSortSelect.value;
        
        // Filter drafts
        let visibleDrafts = draftItems.filter(item => {
            const title = item.getAttribute('data-title') || '';
            const author = item.getAttribute('data-author') || '';
            
            if (searchTerm === '') return true;
            
            return title.includes(searchTerm) || author.includes(searchTerm);
        });
        
        // Sort drafts
        visibleDrafts.sort((a, b) => {
            switch(sortType) {
                case 'oldest':
                    return parseInt(a.getAttribute('data-date')) - parseInt(b.getAttribute('data-date'));
                case 'newest':
                    return parseInt(b.getAttribute('data-date')) - parseInt(a.getAttribute('data-date'));
                case 'az':
                    return a.getAttribute('data-title').localeCompare(b.getAttribute('data-title'));
                case 'za':
                    return b.getAttribute('data-title').localeCompare(a.getAttribute('data-title'));
                default:
                    return parseInt(b.getAttribute('data-date')) - parseInt(a.getAttribute('data-date'));
            }
        });
        
        // Hide all items first
        draftItems.forEach(item => {
            item.style.display = 'none';
        });
        
        // Show and reorder visible items
        visibleDrafts.forEach(item => {
            item.style.display = '';
            draftListUl.appendChild(item);
        });
        
        // Show "no results" message if needed
        let noResultsMsg = draftListUl.querySelector('.no-results-msg');
        
        if (visibleDrafts.length === 0) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('li');
                noResultsMsg.className = 'no-results-msg';
                noResultsMsg.style.cssText = 'padding: 2rem; text-align: center; color: #666; list-style: none;';
                noResultsMsg.textContent = 'No drafts found';
                draftListUl.appendChild(noResultsMsg);
            }
        } else {
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    }
});
