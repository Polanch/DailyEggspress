document.addEventListener('DOMContentLoaded', function () {
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

document.addEventListener('DOMContentLoaded', function () {
	const options = document.querySelectorAll('.t-option');
	if (!options || options.length === 0) return;

	options.forEach(btn => {
		btn.addEventListener('click', function () {
			options.forEach(b => b.classList.remove('active'));
			this.classList.add('active');
		});
	});
});

 document.addEventListener('DOMContentLoaded', function () {
	const options = Array.from(document.querySelectorAll('.t-option'));
	if (!options.length) return;

	options.forEach(btn => {
		btn.addEventListener('click', function () {
			const current = document.querySelector('.t-option.active');
			const clicked = this;
			if (current === clicked) return;

			// animate exit on current active
			if (current) {
				// add exiting class to trigger slide-out
				current.classList.add('exiting');

				const onAnimEnd = (e) => {
					// wait for our underline-out animation
					if (e.animationName === 'underline-out') {
						current.classList.remove('active', 'exiting');
						current.removeEventListener('animationend', onAnimEnd);
					}
				};

				current.addEventListener('animationend', onAnimEnd);
			}

			// add active to clicked (will trigger slide-in)
			clicked.classList.add('active');
		});
	});
});

(function(){
  const box = document.getElementById('dropImageBox');
  const input = document.getElementById('dropImageInput');
  const removeBtn = box.querySelector('.remove-image');
  const placeholder = box.querySelector('.placeholder');

  function setPreview(src){
    if(src){
      box.style.backgroundImage = `url(${src})`;
      placeholder.style.display = 'none';
      removeBtn.style.display = '';
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
    reader.onload = e => setPreview(e.target.result);
    reader.readAsDataURL(file);
    // TODO: upload file to server via fetch/FormData if desired
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
    input.value = '';
    setPreview(null);
  });
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
	const justifyLeftBtn = document.getElementById('justify-left-btn'); if(justifyLeftBtn) justifyLeftBtn.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); exec('justifyLeft'); });
	const justifyCenterBtn = document.getElementById('justify-center-btn'); if(justifyCenterBtn) justifyCenterBtn.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); exec('justifyCenter'); });
	const justifyRightBtn = document.getElementById('justify-right-btn'); if(justifyRightBtn) justifyRightBtn.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); exec('justifyRight'); });
	const justifyFullBtn = document.getElementById('justify-full-btn'); if(justifyFullBtn) justifyFullBtn.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); exec('justifyFull'); });

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

		removeBtn.addEventListener('click', function(e){ e.stopPropagation(); wrapper.remove(); });

		// resize by dragging handle
		let dragging = false, startX = 0, startW = 0;
		handle.addEventListener('mousedown', function(ev){ ev.preventDefault(); ev.stopPropagation(); dragging = true; startX = ev.clientX; startW = wrapper.getBoundingClientRect().width; document.body.style.userSelect='none'; });
		window.addEventListener('mousemove', function(ev){ if(!dragging) return; const dx = ev.clientX - startX; const newW = Math.max(24, startW + dx); wrapper.style.width = newW + 'px'; });
		window.addEventListener('mouseup', function(ev){ if(dragging){ dragging = false; document.body.style.userSelect=''; } });
	}

	function deselectAllWrappers(){ Array.from(editor.querySelectorAll('.img-wrapper.selected')).forEach(w=>{ w.classList.remove('selected'); const tb = w.querySelector('.image-toolbar'); if(tb) tb.style.display='none'; }); }

	// wrap images inserted by file input, and attach controls
	if(t4 && imageInput){ t4.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); imageInput.click(); });
		imageInput.addEventListener('change', function(){ const f = this.files && this.files[0]; if(!f) return; const reader = new FileReader(); reader.onload = function(ev){
				editor.focus();
				// insert wrapped image HTML so we can control it
				const html = '<div class="img-wrapper flow-none" contenteditable="false"><img src="'+ev.target.result+'" alt="image"></div><p><br></p>';
				try{ document.execCommand('insertHTML', false, html); }
				catch(err){ const rng = document.createRange(); rng.selectNodeContents(editor); rng.collapse(false); const sel = window.getSelection(); sel.removeAllRanges(); sel.addRange(rng); document.execCommand('insertHTML', false, html); }
				// attach controls to the newly inserted wrapper
				setTimeout(()=>{ const wrappers = editor.querySelectorAll('.img-wrapper'); const w = wrappers[wrappers.length-1]; if(w) attachImageControls(w); }, 0);
			}; reader.readAsDataURL(f); this.value=''; }); }

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