    </main>
</div><!-- /.main-wrapper -->

<!-- Mobile Bottom Nav -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.06)]">
    <div class="flex items-center">
        <?php
        $bottomNav = [
            ['index',   SITE_URL.'/admin/index.php',   'fa-tachometer-alt', 'Home'],
            ['berita',  SITE_URL.'/admin/berita.php',  'fa-newspaper',      'Berita'],
            ['kegiatan',SITE_URL.'/admin/kegiatan.php','fa-calendar-check', 'Kegiatan'],
            ['bidang',  SITE_URL.'/admin/bidang.php',  'fa-sitemap',        'Bidang'],
        ];
        foreach ($bottomNav as [$key, $url, $icon, $label]):
            $isActive = ($key === 'bidang')
                ? (strpos($adminPage,'bidang') === 0 || strpos($adminPage,'anggota') === 0)
                : (strpos($adminPage, $key) === 0);
        ?>
        <a href="<?= $url ?>"
           class="flex-1 flex flex-col items-center gap-1 py-3 text-center transition-colors <?= $isActive ? 'text-accent' : 'text-gray-400 hover:text-darkblue' ?>">
            <i class="fas <?= $icon ?> text-base"></i>
            <span class="text-[9px] font-bold uppercase tracking-wider"><?= $label ?></span>
        </a>
        <?php endforeach; ?>
        <button onclick="openSidebar()"
                class="flex-1 flex flex-col items-center gap-1 py-3 text-center text-gray-400 hover:text-darkblue transition-colors">
            <i class="fas fa-ellipsis-h text-base"></i>
            <span class="text-[9px] font-bold uppercase tracking-wider">More</span>
        </button>
    </div>
</nav>

<script>
// ── Sidebar open/close ──────────────────────────────────────────
function openSidebar() {
    document.getElementById('adminSidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('adminSidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.add('hidden');
    document.body.style.overflow = '';
}
const sidebarClose = document.getElementById('sidebarClose');
if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);

// ── Delete Confirmation ─────────────────────────────────────────
document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm(this.getAttribute('data-confirm'))) e.preventDefault();
    });
});

// ── Image Preview on File Input ─────────────────────────────────
document.querySelectorAll('input[data-preview]').forEach(input => {
    input.addEventListener('change', function() {
        const preview = document.getElementById(this.getAttribute('data-preview'));
        if (!preview) return;
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
            reader.readAsDataURL(this.files[0]);
        }
    });
});

// ── Flash auto-dismiss ──────────────────────────────────────────
document.querySelectorAll('.flash-auto').forEach(el => {
    setTimeout(() => el.style.opacity = '0', 3500);
    setTimeout(() => el.remove(), 4000);
});
</script>
</body>
</html>
