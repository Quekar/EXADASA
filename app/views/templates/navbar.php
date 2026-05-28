<nav>
    <button id="btn-sidebar" onclick="handleOpenSidebar(event)" style="all: unset; margin-left: 10px;">
        <i class="ph ph-list" style="font-size: 20px; cursor: pointer;"></i>
    </button>

    <div class="group-navbar">

        <?php if ($_SESSION['user']['role'] === 'siswa'): ?>
        <div class="notif-wrapper" id="notifWrapper">
            <button class="notif-bell" id="notifBell" aria-label="Notifikasi">
                <i class="ph ph-bell"></i>
                <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
            </button>

            <div class="notif-panel" id="notifPanel">
                <div class="notif-panel__header">
                    <span class="notif-panel__title poppins-semibold">Notifikasi</span>
                    <button class="notif-panel__mark-all poppins-medium" id="btnMarkAll">
                        Tandai semua dibaca
                    </button>
                </div>
                <div class="notif-panel__list" id="notifList">
                    <div class="notif-empty">
                        <i class="ph ph-bell-slash"></i>
                        <p class="poppins-regular">Belum ada notifikasi.</p>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
        <i class="ph ph-bell" style="font-size: 20px; cursor: pointer;"></i>
        <?php endif; ?>

        <div class="profil-navbar">
            <div class="title">
                <h2 class="poppins-semibold"><?= $_SESSION['user']['nama_lengkap'] ?></h2>
                <p class="poppins-light"><?= $_SESSION['user']['role'] ?></p>
            </div>
            <div class="img" style="background-color: <?= isset($_SESSION['user']['foto']) && $_SESSION['user']['foto'] ? '' : 'var(--color-primary);' ?> overflow: hidden;">
                <?php if (!empty($_SESSION['user']['foto'])): ?>
                    <img src="<?= Constant::DIRNAME ?>asset/img/<?= $_SESSION['user']['foto'] ?>"
                         style="width: 100%; object-fit: cover; aspect-ratio: 1 / 1;" alt="profil">
                <?php else: ?>
                    <span class="poppins-semibold"
                          style="margin: auto; color: #fff; text-transform: uppercase;"><?= $_SESSION['user']['nama_lengkap'][0] ?></span>
                <?php endif; ?>
            </div>
        </div>

    </div>
</nav>

<?php if ($_SESSION['user']['role'] === 'siswa'): ?>
<script>
(function () {
    const DIRNAME    = "<?= Constant::DIRNAME ?>";
    const bell       = document.getElementById('notifBell');
    const panel      = document.getElementById('notifPanel');
    const badge      = document.getElementById('notifBadge');
    const list       = document.getElementById('notifList');
    const btnMarkAll = document.getElementById('btnMarkAll');
    let open = false;

    function timeAgo(dateStr) {
        const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
        if (diff < 60)    return diff + ' detik lalu';
        if (diff < 3600)  return Math.floor(diff / 60) + ' menit lalu';
        if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
        return Math.floor(diff / 86400) + ' hari lalu';
    }

    function updateBadge(count) {
        if (count > 0) {
            badge.style.display = 'flex';
            badge.textContent   = count > 99 ? '99+' : count;
        } else {
            badge.style.display = 'none';
        }
    }

    function renderNotifs(notifs) {
        if (!notifs.length) {
            list.innerHTML = `
                <div class="notif-empty">
                    <i class="ph ph-bell-slash"></i>
                    <p class="poppins-regular">Belum ada notifikasi.</p>
                </div>`;
            return;
        }

        list.innerHTML = notifs.map(n => `
            <div class="notif-item ${n.is_read == 0 ? 'notif-item--unread' : ''}"
                 data-id="${n.id_notifikasi}"
                 data-link="${n.link || ''}">
                <div class="notif-item__dot"></div>
                <div class="notif-item__body">
                    <p class="notif-item__title poppins-semibold">${n.title}</p>
                    <p class="notif-item__msg poppins-regular">${n.message}</p>
                    <span class="notif-item__time poppins-regular">${timeAgo(n.created_at)}</span>
                </div>
            </div>
        `).join('');

        list.querySelectorAll('.notif-item').forEach(el => {
            el.addEventListener('click', function () {
                const id   = this.dataset.id;
                const link = this.dataset.link;

                fetch(DIRNAME + 'notification/markRead', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_notifikasi: parseInt(id) })
                }).catch(() => {});

                this.classList.remove('notif-item--unread');
                const dot = this.querySelector('.notif-item__dot');
                if (dot) dot.style.opacity = '0';

                const current = parseInt(badge.textContent) || 0;
                if (current > 0) updateBadge(current - 1);

                if (link) {
                    window.location.href = DIRNAME + link;
                }
            });
        });
    }

    function loadNotifs() {
        fetch(DIRNAME + 'notification/getNotifications')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                updateBadge(data.unread);
                renderNotifs(data.notifs);
            })
            .catch(() => {});
    }

    loadNotifs();
    setInterval(function () {
        fetch(DIRNAME + 'notification/unreadCount')
            .then(r => r.json())
            .then(d => updateBadge(d.count))
            .catch(() => {});
    }, 30000);

    bell.addEventListener('click', function (e) {
        e.stopPropagation();
        open = !open;
        panel.classList.toggle('notif-panel--open', open);
        if (open) loadNotifs();
    });

    document.addEventListener('click', function (e) {
        if (open && !document.getElementById('notifWrapper').contains(e.target)) {
            open = false;
            panel.classList.remove('notif-panel--open');
        }
    });

    btnMarkAll.addEventListener('click', function (e) {
        e.stopPropagation();
        fetch(DIRNAME + 'notification/markAllRead', { method: 'POST' })
            .then(r => r.json())
            .then(() => {
                updateBadge(0);
                list.querySelectorAll('.notif-item--unread').forEach(el => {
                    el.classList.remove('notif-item--unread');
                    const dot = el.querySelector('.notif-item__dot');
                    if (dot) dot.style.opacity = '0';
                });
            })
            .catch(() => {});
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && open) {
            open = false;
            panel.classList.remove('notif-panel--open');
        }
    });
})();
</script>
<?php endif; ?>