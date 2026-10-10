@php
    $actionRoute = $actionRoute ?? route('signature.store');
@endphp

<div x-data="signaturePad()" x-init="init()">
    <div class="inline-flex gap-1 mb-4 p-1 bg-stone-100 rounded-full">
        <button type="button" @click="mode = 'draw'"
                :class="mode === 'draw' ? 'bg-stone-900 text-white' : 'text-stone-600 hover:text-stone-900'"
                class="px-4 py-1.5 rounded-full text-sm font-medium transition">
            {{ __('Dessiner') }}
        </button>
        <button type="button" @click="mode = 'upload'"
                :class="mode === 'upload' ? 'bg-stone-900 text-white' : 'text-stone-600 hover:text-stone-900'"
                class="px-4 py-1.5 rounded-full text-sm font-medium transition">
            {{ __('Importer une image') }}
        </button>
    </div>

    <form x-ref="form" method="POST" action="{{ $actionRoute }}" enctype="multipart/form-data">
        @csrf

        <div x-show="mode === 'draw'">
            <canvas
                x-ref="canvas"
                class="w-full h-56 sm:h-72 rounded-2xl ring-1 ring-stone-200 bg-stone-50 touch-none"
                @pointerdown="start($event)"
                @pointermove="draw($event)"
                @pointerup="stop()"
                @pointerleave="stop()"
                @pointercancel="stop()"
            ></canvas>

            <input type="hidden" name="signature_data" x-bind:value="dataUrl">

            <p class="mt-2 text-sm text-red-600" x-show="emptyError" x-cloak>
                {{ __('Veuillez dessiner votre signature avant de l\'enregistrer.') }}
            </p>

            <div class="mt-4">
                <x-secondary-button type="button" @click="clear()">
                    {{ __('Effacer') }}
                </x-secondary-button>
            </div>
        </div>

        <div x-show="mode === 'upload'" x-cloak>
            <input type="file" name="signature_file" x-ref="fileInput" accept="image/png,image/jpeg"
                   @change="onFileChange($event)"
                   class="block w-full text-sm text-stone-600 border border-stone-300 rounded-md file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium file:bg-stone-100 file:text-stone-700 hover:file:bg-stone-200">

            <p class="mt-2 text-xs text-stone-500">{{ __('PNG ou JPEG, 5 Mo maximum.') }}</p>

            <img x-show="previewUrl" x-cloak :src="previewUrl" alt="{{ __('Aperçu') }}" class="mt-3 border border-stone-200 rounded-md bg-stone-50 max-h-56" style="max-width: 100%;">

            <p class="mt-2 text-sm text-red-600" x-show="fileError" x-cloak>
                {{ __('Veuillez sélectionner une image avant de l\'enregistrer.') }}
            </p>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <x-primary-button type="button" @click="save()">
                {{ __('Enregistrer la signature') }}
            </x-primary-button>
        </div>
    </form>
</div>

<script>
    function signaturePad() {
        return {
            mode: 'draw',
            drawing: false,
            hasDrawn: false,
            emptyError: false,
            fileError: false,
            previewUrl: '',
            dataUrl: '',
            ctx: null,
            points: [],
            strokeWidth: 2.4,
            init() {
                this.resize();
                window.addEventListener('resize', () => this.resize(true));
            },

            // Sizes the canvas' pixel buffer to match its on-screen size at the device's
            // pixel ratio (crisp on HiDPI screens) and redraws the guide baseline, or the
            // signature already drawn so far if the window is resized mid-signature.
            resize(keepContent = false) {
                const canvas = this.$refs.canvas;
                const width = canvas.clientWidth;
                const height = canvas.clientHeight;
                if (! width || ! height) return;

                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const snapshot = (keepContent && this.hasDrawn) ? canvas.toDataURL('image/png') : null;

                canvas.width = width * ratio;
                canvas.height = height * ratio;

                this.ctx = canvas.getContext('2d');
                this.ctx.scale(ratio, ratio);
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';

                if (snapshot) {
                    const img = new Image();
                    img.onload = () => this.ctx.drawImage(img, 0, 0, width, height);
                    img.src = snapshot;
                } else {
                    this.drawGuides();
                }
            },

            drawGuides() {
                const canvas = this.$refs.canvas;
                const w = canvas.clientWidth;
                const h = canvas.clientHeight;

                this.ctx.save();
                this.ctx.strokeStyle = '#d6d3d1';
                this.ctx.lineWidth = 1;
                this.ctx.beginPath();
                this.ctx.moveTo(w * 0.08, h * 0.72);
                this.ctx.lineTo(w * 0.92, h * 0.72);
                this.ctx.stroke();

                this.ctx.fillStyle = '#a8a29e';
                this.ctx.font = '16px "Playfair Display", serif';
                this.ctx.textAlign = 'center';
                this.ctx.fillText('{{ __('Signez ici') }}', w / 2, h * 0.55);
                this.ctx.restore();
            },

            pos(event) {
                const rect = this.$refs.canvas.getBoundingClientRect();
                return { x: event.clientX - rect.left, y: event.clientY - rect.top };
            },

            start(event) {
                this.drawing = true;
                this.$refs.canvas.setPointerCapture(event.pointerId);

                if (! this.hasDrawn) {
                    this.hasDrawn = true;
                    this.emptyError = false;
                    this.ctx.clearRect(0, 0, this.$refs.canvas.clientWidth, this.$refs.canvas.clientHeight);
                }

                const p = this.pos(event);
                this.points = [p, p];
            },

            // Smooths the raw pointer trail into a curve through the midpoints of each
            // consecutive pair of points, with the stroke width easing toward a target
            // derived from pointer speed — a slower stroke reads as a firmer, thicker line.
            draw(event) {
                if (! this.drawing) return;

                const p = this.pos(event);
                this.points.push(p);
                if (this.points.length > 4) this.points.shift();
                if (this.points.length < 3) return;

                const [p0, p1, p2] = this.points.slice(-3);
                const mid1 = { x: (p0.x + p1.x) / 2, y: (p0.y + p1.y) / 2 };
                const mid2 = { x: (p1.x + p2.x) / 2, y: (p1.y + p2.y) / 2 };

                const speed = Math.hypot(p2.x - p1.x, p2.y - p1.y);
                const targetWidth = Math.max(1.4, Math.min(3.6, 3.4 - speed * 0.2));
                this.strokeWidth += (targetWidth - this.strokeWidth) * 0.35;

                this.ctx.strokeStyle = '#111827';
                this.ctx.lineWidth = this.strokeWidth;
                this.ctx.beginPath();
                this.ctx.moveTo(mid1.x, mid1.y);
                this.ctx.quadraticCurveTo(p1.x, p1.y, mid2.x, mid2.y);
                this.ctx.stroke();
            },

            stop() {
                this.drawing = false;
                this.points = [];
            },

            clear() {
                this.hasDrawn = false;
                this.emptyError = false;
                this.points = [];
                this.strokeWidth = 2.4;
                this.ctx.clearRect(0, 0, this.$refs.canvas.clientWidth, this.$refs.canvas.clientHeight);
                this.drawGuides();
            },

            onFileChange(event) {
                const file = event.target.files[0];
                this.fileError = false;
                this.previewUrl = file ? URL.createObjectURL(file) : '';
            },

            save() {
                if (this.mode === 'draw') {
                    if (! this.hasDrawn) {
                        this.emptyError = true;
                        return;
                    }
                    this.dataUrl = this.$refs.canvas.toDataURL('image/png');
                    this.$nextTick(() => this.$refs.form.requestSubmit());
                    return;
                }

                if (! this.$refs.fileInput.files.length) {
                    this.fileError = true;
                    return;
                }
                this.$refs.form.requestSubmit();
            },
        };
    }
</script>
