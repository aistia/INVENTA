{{-- Lightbox zoom in/out untuk foto.
     Otomatis aktif untuk semua foto di area konten halaman (di dalam <main>), termasuk foto profil,
     bukti kerusakan, dan bukti pengembalian, juga foto yang dibungkus link ke gambar itu sendiri.
     Tidak aktif untuk: gambar di header / sidebar (supaya avatar kecil tetap menjadi link ke profil),
     gambar yang dibungkus link ke halaman lain, dan gambar ber-atribut  data-no-zoom.
     Gambar di luar <main> bisa ikut aktif dengan menambahkan atribut  data-zoom  di tag <img>-nya.
     Dipasang lewat @include('layouts._zoom') di layout admin dan siswa. --}}
<style>
    main img:not([data-no-zoom]), img[data-zoom] { cursor: zoom-in; }
    #lightbox button:focus-visible, #lightbox a:focus-visible { outline: 2px solid #a78bfa; outline-offset: 2px; }
    @media print { #lightbox { display: none !important; } }
</style>

<div id="lightbox" hidden role="dialog" aria-modal="true" aria-label="Pratinjau foto"
     style="position:fixed; inset:0; z-index:9999; background:rgba(2,6,23,.92); touch-action:none; user-select:none; -webkit-user-select:none;">

    <div id="lbStage" style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; overflow:hidden;">
        <img id="lbImg" alt="" draggable="false"
             style="max-width:92vw; max-height:78vh; object-fit:contain; transform-origin:center center; will-change:transform; -webkit-user-drag:none;">
    </div>

    <p id="lbCaption" class="absolute top-4 left-4 right-20 text-xs text-white/80 truncate pointer-events-none"></p>

    <button type="button" data-lb="tutup" aria-label="Tutup"
        class="absolute top-3 right-3 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center">
        <span class="material-symbols-outlined">close</span>
    </button>

    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-1 bg-slate-900/80 rounded-full px-2 py-1.5 text-white">
        <button type="button" data-lb="kecil" aria-label="Perkecil"
            class="w-9 h-9 rounded-full hover:bg-white/15 flex items-center justify-center">
            <span class="material-symbols-outlined">zoom_out</span>
        </button>
        <span id="lbSkala" class="min-w-[52px] text-center text-xs font-semibold tabular-nums">100%</span>
        <button type="button" data-lb="besar" aria-label="Perbesar"
            class="w-9 h-9 rounded-full hover:bg-white/15 flex items-center justify-center">
            <span class="material-symbols-outlined">zoom_in</span>
        </button>
        <span class="w-px h-5 bg-white/20 mx-1"></span>
        <button type="button" data-lb="reset" aria-label="Sesuaikan ke layar" title="Sesuaikan ke layar"
            class="w-9 h-9 rounded-full hover:bg-white/15 flex items-center justify-center">
            <span class="material-symbols-outlined">fit_screen</span>
        </button>
        <a id="lbBuka" href="#" target="_blank" rel="noopener" aria-label="Buka di tab baru" title="Buka di tab baru"
            class="w-9 h-9 rounded-full hover:bg-white/15 flex items-center justify-center text-white">
            <span class="material-symbols-outlined">open_in_new</span>
        </a>
    </div>

    <p class="hidden sm:block absolute bottom-1.5 left-0 right-0 text-center text-[10.5px] text-white/50 pointer-events-none">
        Scroll atau cubit untuk zoom &bull; geser untuk memindahkan &bull; klik dua kali untuk memperbesar &bull; Esc untuk menutup
    </p>
</div>

<script>
    (function () {
        const box = document.getElementById('lightbox');
        if (!box) { return; }

        const stage = document.getElementById('lbStage');
        const img = document.getElementById('lbImg');
        const label = document.getElementById('lbSkala');
        const caption = document.getElementById('lbCaption');
        const linkBuka = document.getElementById('lbBuka');
        const kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const MIN = 0.5;
        const MAX = 6;
        let s = 1, tx = 0, ty = 0;
        let bodyTerkunciSebelumnya = false;

        const pointers = new Map();
        let jarakGeser = 0, downDiGambar = false, pinchJarak = 0, pinchSkala = 1;

        function terapkan(animasi) {
            if (s <= 1) { tx = 0; ty = 0; }
            img.style.transition = (animasi && !kurangiGerak) ? 'transform .15s ease-out' : 'none';
            img.style.transform = 'translate(' + tx + 'px,' + ty + 'px) scale(' + s + ')';
            label.textContent = Math.round(s * 100) + '%';
            stage.style.cursor = s > 1 ? 'grab' : 'default';
        }

        // Zoom dengan titik (cx, cy) di layar tetap berada di bawah kursor/jari
        function zoomDi(cx, cy, skalaBaru, animasi) {
            skalaBaru = Math.min(MAX, Math.max(MIN, skalaBaru));
            const r = stage.getBoundingClientRect();
            const px = cx - (r.left + r.width / 2);
            const py = cy - (r.top + r.height / 2);
            const k = skalaBaru / s;
            tx = px - (px - tx) * k;
            ty = py - (py - ty) * k;
            s = skalaBaru;
            terapkan(animasi);
        }

        function zoomTengah(faktor) {
            const r = stage.getBoundingClientRect();
            zoomDi(r.left + r.width / 2, r.top + r.height / 2, s * faktor, true);
        }

        function reset() {
            s = 1; tx = 0; ty = 0;
            terapkan(true);
        }

        function buka(src, alt) {
            img.src = src;
            img.alt = alt || '';
            caption.textContent = alt || '';
            linkBuka.href = src;
            s = 1; tx = 0; ty = 0;
            terapkan(false);
            bodyTerkunciSebelumnya = document.body.classList.contains('overflow-hidden');
            document.body.classList.add('overflow-hidden');
            box.hidden = false;
        }

        function tutup() {
            box.hidden = true;
            img.removeAttribute('src');
            pointers.clear();
            if (!bodyTerkunciSebelumnya) { document.body.classList.remove('overflow-hidden'); }
        }

        function bisaZoom(el) {
            if (!el || el.tagName !== 'IMG' || !el.getAttribute('src')) { return false; }
            if (box.contains(el)) { return false; }
            if (el.closest('header, aside, nav, [data-no-zoom]')) { return false; }
            if (el.hasAttribute('data-zoom')) { return true; }
            if (!el.closest('main')) { return false; }

            // Gambar yang dibungkus link ke halaman lain tetap berfungsi sebagai link biasa
            const a = el.closest('a[href]');
            if (a) {
                const href = a.href;
                const keGambar = href === el.src
                    || /\/storage\//.test(href)
                    || /\.(png|jpe?g|gif|webp|avif|bmp)(\?|#|$)/i.test(href);
                if (!keGambar) { return false; }
            }
            return true;
        }

        // Cari foto yang diklik. Kalau yang kena klik lapisan di atas foto (mis. overlay atau ikon),
        // cari foto di bawah titik klik, di dalam pembungkus yang sama.
        function cariGambar(e) {
            const t = e.target;
            if (!t || !t.closest) { return null; }

            const langsung = t.closest('img');
            if (langsung) { return bisaZoom(langsung) ? langsung : null; }
            if (t.closest('button, input, select, textarea, label, summary')) { return null; }

            const pembungkus = (t.parentElement && t.parentElement.parentElement) || t.parentElement;
            if (!pembungkus) { return null; }

            const tumpukan = document.elementsFromPoint(e.clientX, e.clientY);
            for (let i = 0; i < tumpukan.length; i++) {
                const el = tumpukan[i];
                if (el.tagName === 'IMG' && pembungkus.contains(el) && bisaZoom(el)) { return el; }
            }
            return null;
        }

        // Klik pada foto -> buka lightbox (menggantikan link "buka di tab baru" bawaan)
        document.addEventListener('click', function (e) {
            const el = cariGambar(e);
            if (!el) { return; }
            e.preventDefault();
            e.stopPropagation();
            buka(el.getAttribute('data-zoom-src') || el.currentSrc || el.src, el.getAttribute('alt') || '');
        }, true);

        // Tombol di dalam lightbox
        box.addEventListener('click', function (e) {
            const tombol = e.target.closest('[data-lb]');
            if (!tombol) { return; }
            const aksi = tombol.getAttribute('data-lb');
            if (aksi === 'tutup') { tutup(); }
            else if (aksi === 'besar') { zoomTengah(1.4); }
            else if (aksi === 'kecil') { zoomTengah(1 / 1.4); }
            else if (aksi === 'reset') { reset(); }
        });

        // Scroll mouse / pinch trackpad
        stage.addEventListener('wheel', function (e) {
            e.preventDefault();
            const laju = e.ctrlKey ? 0.01 : 0.0015;
            zoomDi(e.clientX, e.clientY, s * Math.exp(-e.deltaY * laju), false);
        }, { passive: false });

        // Geser (drag) dan cubit (pinch) dengan pointer events
        stage.addEventListener('pointerdown', function (e) {
            stage.setPointerCapture(e.pointerId);
            pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
            if (pointers.size === 1) {
                jarakGeser = 0;
                downDiGambar = (e.target === img);
            } else if (pointers.size === 2) {
                const p = Array.from(pointers.values());
                pinchJarak = Math.hypot(p[0].x - p[1].x, p[0].y - p[1].y);
                pinchSkala = s;
            }
            if (s > 1) { stage.style.cursor = 'grabbing'; }
        });

        stage.addEventListener('pointermove', function (e) {
            const p = pointers.get(e.pointerId);
            if (!p) { return; }

            if (pointers.size === 2) {
                p.x = e.clientX; p.y = e.clientY;
                const t = Array.from(pointers.values());
                const jarak = Math.hypot(t[0].x - t[1].x, t[0].y - t[1].y);
                if (pinchJarak > 0) {
                    zoomDi((t[0].x + t[1].x) / 2, (t[0].y + t[1].y) / 2, pinchSkala * jarak / pinchJarak, false);
                }
                jarakGeser = 99;
                return;
            }

            const dx = e.clientX - p.x;
            const dy = e.clientY - p.y;
            p.x = e.clientX; p.y = e.clientY;
            jarakGeser += Math.abs(dx) + Math.abs(dy);
            if (s > 1) { tx += dx; ty += dy; terapkan(false); }
        });

        function lepas(e) {
            if (!pointers.has(e.pointerId)) { return; }
            const sendirian = pointers.size === 1;
            pointers.delete(e.pointerId);
            if (sendirian && e.type === 'pointerup' && jarakGeser < 6 && !downDiGambar) { tutup(); }
            if (pointers.size < 2) { pinchJarak = 0; }
            if (s > 1) { stage.style.cursor = 'grab'; }
        }
        stage.addEventListener('pointerup', lepas);
        stage.addEventListener('pointercancel', lepas);

        // Klik dua kali: perbesar di titik itu / kembali ke ukuran layar
        stage.addEventListener('dblclick', function (e) {
            if (s > 1.05) { reset(); } else { zoomDi(e.clientX, e.clientY, 2.5, true); }
        });

        // Keyboard (fase capture supaya Esc tidak ikut menutup panel lain di bawahnya)
        window.addEventListener('keydown', function (e) {
            if (box.hidden) { return; }
            if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); tutup(); }
            else if (e.key === '+' || e.key === '=') { e.preventDefault(); zoomTengah(1.25); }
            else if (e.key === '-' || e.key === '_') { e.preventDefault(); zoomTengah(1 / 1.25); }
            else if (e.key === '0') { e.preventDefault(); reset(); }
        }, true);
    })();
</script>