import * as pdfjsLib from 'pdfjs-dist';
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;

const RENDER_SCALE = 1.3;
const SCAN_DURATION_MS = 1600;
const MIN_BOX_WIDTH = 0.03;
const MIN_BOX_HEIGHT = 0.02;

function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
}

function initViewer(root) {
    const fileUrl = root.dataset.fileUrl;
    const pageCount = parseInt(root.dataset.pageCount, 10);
    const signed = root.dataset.signed === '1';
    const suggestion = root.dataset.suggestion ? JSON.parse(root.dataset.suggestion) : null;
    const existing = root.dataset.existing ? JSON.parse(root.dataset.existing) : null;

    const canvas = root.querySelector('#dv-canvas');
    const ctx = canvas.getContext('2d');
    const wrap = root.querySelector('#dv-canvas-wrap');
    const boxEl = root.querySelector('#dv-box');
    const scannerEl = root.querySelector('#dv-scanner');
    const scanningLabel = root.querySelector('#dv-scanning-label');
    const pageLabel = root.querySelector('#dv-page-label');
    const prevBtn = root.querySelector('#dv-prev');
    const nextBtn = root.querySelector('#dv-next');
    // #dv-sign-button and #dv-sign-form live outside the [data-document-viewer] root
    // in the Blade markup, so they must be looked up from the document, not from root.
    const signButton = document.querySelector('#dv-sign-button');
    const form = document.querySelector('#dv-sign-form');
    const captionCheckbox = document.querySelector('#dv-caption-checkbox');

    const hasPlacedSignature = signed && existing !== null;

    let pdfDoc = null;
    let currentPage = hasPlacedSignature ? existing.page : (suggestion ? suggestion.page : 1);
    let hasScanned = false;

    const box = hasPlacedSignature
        ? { x: existing.x, y: existing.y, width: existing.width, height: existing.height }
        : (suggestion ? { x: suggestion.x, y: suggestion.y, width: suggestion.width, height: suggestion.height } : null);

    function positionBox() {
        if (!box) return;
        boxEl.style.left = (box.x * canvas.width) + 'px';
        boxEl.style.top = (box.y * canvas.height) + 'px';
        boxEl.style.width = (box.width * canvas.width) + 'px';
        boxEl.style.height = (box.height * canvas.height) + 'px';
    }

    function revealBox() {
        if (!box) return;
        positionBox();
        boxEl.style.opacity = '1';
    }

    function playScanThenReveal() {
        boxEl.style.opacity = '0';
        positionBox();

        if (scanningLabel) {
            scanningLabel.classList.remove('hidden');
            scanningLabel.classList.add('flex');
        }

        scannerEl.style.display = 'block';
        scannerEl.innerHTML = '<div class="dv-scanner-line"></div>';

        window.setTimeout(() => {
            scannerEl.style.display = 'none';
            if (scanningLabel) {
                scanningLabel.classList.add('hidden');
                scanningLabel.classList.remove('flex');
            }
            revealBox();
        }, SCAN_DURATION_MS);
    }

    function renderPage(num) {
        pdfDoc.getPage(num).then((page) => {
            const viewport = page.getViewport({ scale: RENDER_SCALE });
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            wrap.style.width = viewport.width + 'px';
            wrap.style.height = viewport.height + 'px';

            page.render({ canvasContext: ctx, viewport }).promise.then(() => {
                if (hasPlacedSignature) {
                    revealBox();
                } else if (!signed && !hasScanned) {
                    hasScanned = true;
                    playScanThenReveal();
                } else if (!signed) {
                    revealBox();
                }
                // Otherwise: signed but with no placed signature (uploaded already signed) — nothing to overlay.
            });

            pageLabel.textContent = `Page ${num} / ${pageCount}`;
            prevBtn.disabled = num <= 1;
            nextBtn.disabled = num >= pageCount;
        });
    }

    function goToPage(num) {
        currentPage = clamp(num, 1, pageCount);
        renderPage(currentPage);
    }

    prevBtn.addEventListener('click', () => goToPage(currentPage - 1));
    nextBtn.addEventListener('click', () => goToPage(currentPage + 1));

    if (hasPlacedSignature) {
        boxEl.classList.add('border-green-600', 'bg-green-500/10');
        boxEl.innerHTML = `<img src="${existing.image}" class="w-full h-full object-contain pointer-events-none select-none" alt="Signature">`;
    } else if (!signed) {
        boxEl.classList.add('border-indigo-500', 'bg-indigo-500/10', 'cursor-move');

        let dragging = false;
        let startPointer = { x: 0, y: 0 };
        let startBox = { x: 0, y: 0 };

        boxEl.addEventListener('pointerdown', (event) => {
            dragging = true;
            boxEl.setPointerCapture(event.pointerId);
            startPointer = { x: event.clientX, y: event.clientY };
            startBox = { x: box.x, y: box.y };
        });

        boxEl.addEventListener('pointermove', (event) => {
            if (!dragging) return;

            const deltaX = (event.clientX - startPointer.x) / canvas.width;
            const deltaY = (event.clientY - startPointer.y) / canvas.height;

            box.x = clamp(startBox.x + deltaX, 0, 1 - box.width);
            box.y = clamp(startBox.y + deltaY, 0, 1 - box.height);

            positionBox();
        });

        boxEl.addEventListener('pointerup', () => {
            dragging = false;
        });

        const resizeHandle = boxEl.querySelector('#dv-resize-handle');
        let resizing = false;
        let startResizePointer = { x: 0, y: 0 };
        let startResizeBox = { width: 0, height: 0 };

        resizeHandle.addEventListener('pointerdown', (event) => {
            event.stopPropagation();
            resizing = true;
            resizeHandle.setPointerCapture(event.pointerId);
            startResizePointer = { x: event.clientX, y: event.clientY };
            startResizeBox = { width: box.width, height: box.height };
        });

        resizeHandle.addEventListener('pointermove', (event) => {
            if (!resizing) return;
            event.stopPropagation();

            const deltaX = (event.clientX - startResizePointer.x) / canvas.width;
            const deltaY = (event.clientY - startResizePointer.y) / canvas.height;

            box.width = clamp(startResizeBox.width + deltaX, MIN_BOX_WIDTH, 1 - box.x);
            box.height = clamp(startResizeBox.height + deltaY, MIN_BOX_HEIGHT, 1 - box.y);

            positionBox();
        });

        resizeHandle.addEventListener('pointerup', (event) => {
            event.stopPropagation();
            resizing = false;
        });

        signButton.addEventListener('click', () => {
            form.querySelector('#dv-input-page').value = currentPage;
            form.querySelector('#dv-input-x').value = box.x.toFixed(4);
            form.querySelector('#dv-input-y').value = box.y.toFixed(4);
            form.querySelector('#dv-input-w').value = box.width.toFixed(4);
            form.querySelector('#dv-input-h').value = box.height.toFixed(4);
            form.querySelector('#dv-input-caption').value = captionCheckbox.checked ? '1' : '0';
            form.submit();
        });
    }

    pdfjsLib.getDocument({ url: fileUrl }).promise.then((doc) => {
        pdfDoc = doc;
        goToPage(currentPage);
    }).catch((error) => {
        console.error('Failed to load PDF preview', error);
        wrap.innerHTML = '<p class="p-4 text-sm text-red-600">'
            + "Impossible de charger l'aperçu du document."
            + '</p>';
    });
}

document.querySelectorAll('[data-document-viewer]').forEach(initViewer);
