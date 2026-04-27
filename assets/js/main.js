// ============================================================
// PORTAL INFORMASI KELURAHAN PULOMERAK — MAIN JS
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ── STICKY NAVBAR ──────────────────────────────────────
    const navbar = document.getElementById('mainNav');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // ── MOBILE NAV TOGGLE ──────────────────────────────────
    const navToggle  = document.getElementById('navToggle');
    const navMenu    = document.getElementById('navMenu');
    const navOverlay = document.getElementById('navOverlay');

    if (navToggle && navMenu) {
        function toggleMenu() {
            const isOpen = navMenu.classList.toggle('open');
            navToggle.classList.toggle('open');
            if (navOverlay) navOverlay.classList.toggle('show');
            
            // Lock body and html scroll
            const scrollStyle = isOpen ? 'hidden' : '';
            document.body.style.overflow = scrollStyle;
            document.documentElement.style.overflow = scrollStyle;
        }

        navToggle.addEventListener('click', toggleMenu);

        // Close on overlay or outside click
        document.addEventListener('click', function (e) {
            if (navMenu.classList.contains('open')) {
                if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                    toggleMenu();
                }
            }
        });
        
        if (navOverlay) {
            navOverlay.addEventListener('click', toggleMenu);
        }
    }

    // ── BACK TO TOP ────────────────────────────────────────
    const btn = document.getElementById('backToTop');
    if (btn) {
        window.addEventListener('scroll', function () {
            btn.classList.toggle('show', window.scrollY > 400);
        });
        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ── SCROLL REVEAL ──────────────────────────────────────
    const reveals = document.querySelectorAll('.reveal');
    if (reveals.length && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        reveals.forEach(function (el) { observer.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('visible'); });
    }

    // ── COUNTER ANIMATION ──────────────────────────────────
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length && 'IntersectionObserver' in window) {
        const cObs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    cObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(function (el) { cObs.observe(el); });
    }
    function animateCounter(el) {
        const target   = parseInt(el.dataset.count);
        const duration = 1800;
        const start    = performance.now();
        function update(time) {
            const progress = Math.min((time - start) / duration, 1);
            const eased    = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(eased * target).toLocaleString('id-ID');
            if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    }

    // ── KEGIATAN FILTER ────────────────────────────────────
    const filterBtns  = document.querySelectorAll('[data-filter]');
    const filterItems = document.querySelectorAll('[data-category]');
    if (filterBtns.length && filterItems.length) {
        filterBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                filterBtns.forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
                const filter = btn.dataset.filter;
                filterItems.forEach(function (item) {
                    if (filter === 'all' || item.dataset.category === filter) {
                        item.style.display = '';
                        item.classList.add('reveal');
                        setTimeout(function () { item.classList.add('visible'); }, 50);
                    } else {
                        item.style.display = 'none';
                        item.classList.remove('visible');
                    }
                });
            });
        });
    }

    // ── LIGHTBOX (GALERI) ──────────────────────────────────
    const galeriImgs = document.querySelectorAll('.galeri-item[data-src]');
    if (galeriImgs.length) {
        // Create lightbox
        const lb = document.createElement('div');
        lb.id = 'lightbox';
        lb.style.cssText = `
            display:none; position:fixed; inset:0; z-index:9999;
            background:rgba(0,0,0,0.92); align-items:center; justify-content:center;
            cursor:zoom-out;
        `;
        lb.innerHTML = `
            <div style="max-width:90vw;max-height:90vh;position:relative;">
                <img id="lbImg" src="" alt="" style="max-width:90vw;max-height:85vh;border-radius:12px;display:block;">
                <div id="lbCaption" style="color:#fff;text-align:center;margin-top:12px;font-size:0.9rem;opacity:0.85;"></div>
                <button id="lbClose" style="position:absolute;top:-16px;right:-16px;width:36px;height:36px;
                    background:#fff;border-radius:50%;font-size:1.2rem;color:#333;border:none;cursor:pointer;">✕</button>
            </div>`;
        document.body.appendChild(lb);

        galeriImgs.forEach(function (item) {
            item.addEventListener('click', function () {
                const src     = item.dataset.src;
                const caption = item.dataset.caption || '';
                document.getElementById('lbImg').src = src;
                document.getElementById('lbCaption').textContent = caption;
                lb.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        });
        lb.addEventListener('click', function (e) {
            if (e.target === lb || e.target.id === 'lbClose') {
                lb.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && lb.style.display === 'flex') {
                lb.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    }

    // ── AUTO DISMISS ALERTS ────────────────────────────────
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 4000);
    });

    // ── SEARCH LIVE (Sederhana) ────────────────────────────
    const searchInput = document.getElementById('liveSearch');
    if (searchInput) {
        const items = document.querySelectorAll('.searchable-item');
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            items.forEach(function (item) {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(q) ? '' : 'none';
            });
        });
    }

    // ── DELETE CONFIRM ─────────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = el.dataset.confirm || 'Apakah Anda yakin ingin menghapus data ini?';
            if (!confirm(msg)) e.preventDefault();
        });
    });

    // ── ADMIN SIDEBAR TOGGLE (mobile) ─────────────────────
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar  = document.querySelector('.admin-sidebar');
    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', function () {
            adminSidebar.classList.toggle('open');
        });
    }

    // ── IMAGE PREVIEW (upload) ─────────────────────────────
    const imgInputs = document.querySelectorAll('[data-preview]');
    imgInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            const previewId = this.dataset.preview;
            const preview   = document.getElementById(previewId);
            if (!preview) return;
            const file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // ── TOAST SYSTEM ──────────────────────────────────────
    window.showToast = function(msg, type = 'info') {
        const toastContainer = document.getElementById('toastContainer') || (function() {
            const c = document.createElement('div');
            c.id = 'toastContainer';
            c.style.cssText = 'position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
            document.body.appendChild(c);
            return c;
        })();

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
        toast.innerHTML = `<i class="fas ${icons[type] || icons.info}"></i> <span>${msg}</span>`;
        toastContainer.appendChild(toast);

        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    };

    // ══════════════════════════════════════════════════════════
    // REALTIME ENGINE
    // - Public pages: notification banner on new berita/kegiatan
    // - Admin pages : live stat counters + toast notifications
    // ══════════════════════════════════════════════════════════
    (function initRealtime() {
        const isAdmin  = window.location.pathname.includes('/admin/');
        const siteUrl  = window.SITE_URL || '';
        const apiUrl   = siteUrl + '/api/realtime.php';
        const INTERVAL = 5000; // 5 detik sesuai request

        // ── PUBLIC: Notification Banner ──────────────────────
        let banner = null;
        function showBanner(text, href) {
            if (banner) return; // only one at a time
            banner = document.createElement('div');
            banner.id = 'rt-banner';
            banner.innerHTML = `
                <span><i class="fas fa-circle" style="color:#22c55e;font-size:.55rem;animation:pulse 1.5s infinite;"></i> ${text}</span>
                <div style="display:flex;gap:8px;align-items:center;">
                    <a href="${href}" style="background:#fff;color:#0054A6;padding:5px 14px;border-radius:20px;font-weight:700;font-size:.8rem;text-decoration:none;">Lihat</a>
                    <button onclick="document.getElementById('rt-banner').remove();window._rtBanner=null;" style="background:transparent;border:none;color:#fff;font-size:1.1rem;cursor:pointer;padding:0 4px;">×</button>
                </div>`;
            Object.assign(banner.style, {
                position:'fixed', top:'0', left:'0', right:'0', zIndex:'99999',
                background:'linear-gradient(135deg,#0054A6,#0077e6)',
                color:'#fff', display:'flex', alignItems:'center',
                justifyContent:'space-between', padding:'10px 20px',
                fontSize:'.88rem', boxShadow:'0 2px 16px rgba(0,0,0,0.18)',
                transform:'translateY(-100%)', transition:'transform .35s ease',
                fontFamily:'inherit'
            });
            document.body.appendChild(banner);
            setTimeout(() => banner && (banner.style.transform = 'translateY(0)'), 50);
            window._rtBanner = banner;
        }

        // Tracked Version
        let knownVersion = 0;
        let isFirstPoll  = true;

        // ── ADMIN: Live stat elements (pakai data-stat attribute) ─
        function animateNum(el, to) {
            const from = parseInt(el.textContent.replace(/\./g, '')) || 0;
            if (from === to) return;
            const dur = 600, step = 16;
            const inc = (to - from) / (dur / step);
            let cur = from;
            const t = setInterval(() => {
                cur += inc;
                el.textContent = Math.round(cur >= to ? to : cur).toLocaleString('id-ID');
                if (cur >= to) clearInterval(t);
            }, step);
        }
        function updateAdminStats(counts) {
            Object.entries(counts).forEach(([key, val]) => {
                document.querySelectorAll(`[data-stat="${key}"]`).forEach(el => animateNum(el, val));
            });
        }

        // ── TOAST (admin) ─────────────────────────────────────
        function showToast(msg, type = 'info') {
            const colors = { info:'#0077e6', success:'#16a34a', warn:'#d97706' };
            const t = document.createElement('div');
            t.innerHTML = msg;
            Object.assign(t.style, {
                position:'fixed', bottom:'24px', right:'24px', zIndex:'99999',
                background: colors[type] || colors.info,
                color:'#fff', padding:'12px 20px', borderRadius:'12px',
                fontSize:'.88rem', boxShadow:'0 4px 20px rgba(0,0,0,.2)',
                maxWidth:'320px', lineHeight:'1.5',
                transform:'translateY(20px)', opacity:'0',
                transition:'all .3s ease', fontFamily:'inherit'
            });
            document.body.appendChild(t);
            setTimeout(() => { t.style.transform='translateY(0)'; t.style.opacity='1'; }, 10);
            setTimeout(() => { t.style.opacity='0'; setTimeout(() => t.remove(), 400); }, 5000);
        }

        // ── POLL ─────────────────────────────────────────────
        async function poll() {
            try {
                const res  = await fetch(apiUrl, { cache: 'no-store' });
                if (!res.ok) return;
                const data = await res.json();
                
                if (isFirstPoll) {
                    knownVersion = data.version;
                    isFirstPoll = false;
                    return; // skip first check
                }

                if (isAdmin && data.counts) {
                    updateAdminStats(data.counts);
                }

                // Jika ada perubahan versi (berarti ada input/edit/hapus data di DB)
                if (data.version > knownVersion) {
                    knownVersion = data.version;
                    
                    const path = window.location.pathname;
                    // Jangan refresh jika user sedang mengetik di form
                    if (!path.includes('-add.php') && !path.includes('-edit.php')) {
                        if (isAdmin) {
                            showToast('🔄 Memperbarui data...', 'info');
                        } else {
                            showBanner('Ada pembaruan data, memuat ulang...', '#');
                        }
                        // Refresh otomatis dalam 1.5 detik
                        setTimeout(() => window.location.reload(), 1500);
                    }
                }
            } catch (e) { /* silent fail */ }
        }

        // Start: 3s delay lalu interval
        setTimeout(poll, 3000);
        setInterval(poll, INTERVAL);
    })();

});

