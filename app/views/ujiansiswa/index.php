<?php
$listUjian = $data['listUjian']  ?? [];
$statusMap = $data['statusMap']  ?? [];
$DIRNAME   = Constant::DIRNAME;
$now       = time();
?>

<div class="container ujiansiswa-container">

    <div class="ujiansiswa-header">
        <div class="ujiansiswa-title">
            <h1 class="poppins-bold">Ujian Saya</h1>
            <p class="poppins-regular">Daftar ujian sesuai kelas dan mata pelajaran kamu.</p>
        </div>
        <div class="ujiansiswa-search">
            <div class="search-wrapper">
                <i class="ph ph-magnifying-glass search-icon"></i>
                <input type="text" id="searchUjian"
                       placeholder="Cari ujian..."
                       class="poppins-regular"
                       oninput="filterUjian(this.value)">
            </div>
        </div>
    </div>

    <div class="ujian-grid" id="ujianGrid">

        <?php if (empty($listUjian)): ?>
            <div class="ujian-empty" id="ujianEmpty" style="grid-column: 1/-1; display: flex;">
                <i class="ph ph-clipboard-text"></i>
                <p class="poppins-medium">Belum ada ujian yang tersedia untuk kelas kamu.</p>
            </div>
        <?php else: ?>
            <?php foreach ($listUjian as $ujian):
                $id_ujian    = $ujian['id_ujian'];
                $namaUjian   = htmlspecialchars($ujian['nama_ujian']);
                $mapelLabel  = htmlspecialchars($ujian['deskripsi_ujian'] ?? '-');
                $hasKode     = !empty(trim($ujian['kode_ujian'] ?? ''));
                
                $wParts      = explode(':', $ujian['waktu_pengerjaan']);
                $menitTotal  = ((int)$wParts[0] * 60) + (int)$wParts[1];
                
                $tsMulai     = strtotime(str_replace('T', ' ', $ujian['jadwal_mulai']));
                $tsSels      = strtotime(str_replace('T', ' ', $ujian['jadwal_selesai']));
                
                $prog        = $statusMap[$id_ujian] ?? ['status' => null, 'is_scored' => false];
                $pSiswa      = $prog['status'];
                $isScored    = $prog['is_scored'];

                $status = 'aktif';
                if ($pSiswa === 'selesai') {
                    $status = 'selesai';
                } elseif ($now < $tsMulai) {
                    $status = 'belum_mulai';
                } elseif ($now > $tsSels) {
                    $status = 'berakhir';
                }

                $badgeMap = [
                    'selesai'     => ['class' => 'badge-berakhir', 'label' => 'Selesai',     'icon' => 'card-icon--muted'],
                    'belum_mulai' => ['class' => 'badge-draft',    'label' => 'Belum Mulai', 'icon' => 'card-icon--draft'],
                    'berakhir'    => ['class' => 'badge-berakhir', 'label' => 'Berakhir',    'icon' => 'card-icon--muted'],
                    'aktif'       => ['class' => 'badge-aktif',    'label' => 'Aktif',       'icon' => '']
                ];
                $style = $badgeMap[$status];
            ?>

            <div class="ujian-card"
                 data-nama="<?= strtolower($namaUjian) ?>"
                 data-mapel="<?= strtolower($mapelLabel) ?>">

                <div class="card-top">
                    <div class="card-icon <?= $style['icon'] ?>">
                        <i class="ph ph-clipboard-text"></i>
                    </div>
                    <span class="badge <?= $style['class'] ?> poppins-semibold"><?= $style['label'] ?></span>
                </div>

                <div class="card-body">
                    <p class="card-mapel poppins-semibold"><?= strtoupper($mapelLabel) ?></p>
                    <h3 class="card-nama poppins-bold"><?= $namaUjian ?></h3>

                    <div class="card-detail">
                        <span class="detail-item poppins-regular">
                            <i class="ph ph-clock"></i>
                            <?= $menitTotal ?> menit
                        </span>
                        <span class="detail-item poppins-regular">
                            <i class="ph <?= $hasKode ? 'ph-key' : 'ph-check-circle' ?>"></i>
                            <?= $hasKode ? 'Kode diperlukan' : 'Langsung Mulai' ?>
                        </span>
                    </div>

                    <div class="card-jadwal poppins-regular">
                        <i class="ph ph-calendar-dots"></i>
                        <?= date('d M Y H:i', $tsMulai) ?>
                    </div>
                </div>

                <div class="card-footer">
                    <?php if ($status === 'selesai'): ?>
                        <?php if ($isScored): ?>
                            <a href="<?= $DIRNAME ?>hasilUjian" class="card-footer__btn btn-lihat-hasil poppins-semibold">
                                Lihat Hasil
                            </a>
                        <?php else: ?>
                            <button style="cursor: not-allowed;" class="card-footer__btn btn-lihat-hasil poppins-semibold" disabled title="Menunggu petugas mempublish nilai">
                                Menunggu Koreksi
                            </button>
                        <?php endif; ?>
                    <?php elseif ($pSiswa === 'proses'): ?>
                        <button class="card-footer__btn btn-lanjutkan poppins-semibold"
                                onclick="<?= $hasKode ? "bukaModalKode('$id_ujian', '".addslashes($namaUjian)."')" : "mulaiTanpaKode('$id_ujian')" ?>">
                            <i class="ph ph-play"></i> Lanjutkan
                        </button>
                    <?php elseif ($status === 'aktif'): ?>
                        <button class="card-footer__btn btn-masuk-ujian poppins-semibold"
                                onclick="<?= $hasKode ? "bukaModalKode('$id_ujian', '".addslashes($namaUjian)."')" : "mulaiTanpaKode('$id_ujian')" ?>">
                            Masuk Ujian
                        </button>
                    <?php else: ?>
                        <button class="card-footer__btn btn-masuk-ujian poppins-semibold" disabled>
                            <?= $status === 'belum_mulai' ? 'Belum Dimulai' : 'Sudah Berakhir' ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <div class="ujian-empty" id="searchEmpty" style="display: none;">
        <i class="ph ph-magnifying-glass"></i>
        <p class="poppins-medium">Ujian tidak ditemukan.</p>
    </div>

</div>

<div class="modal-overlay" id="modalOverlay" onclick="tutupModalOnOverlay(event)">
    <div class="modal-box" role="dialog" aria-modal="true">
        <div class="modal-header">
            <div class="modal-title-group">
                <i class="ph ph-key modal-key-icon"></i>
                <h2 class="poppins-bold">Kode Akses Ujian</h2>
            </div>
            <button class="modal-close" onclick="tutupModalKode()">
                <i class="ph ph-x"></i>
            </button>
        </div>

        <div class="modal-body">
            <p class="modal-desc poppins-regular">
                Masukkan kode akses untuk memulai ujian <strong id="modalNamaUjian">—</strong>.
            </p>

            <div class="form-input">
                <input type="text" id="inputKodeAkses" placeholder="CONTOH: KODE123" 
                       class="poppins-regular" style="text-transform: uppercase;"
                       oninput="this.classList.remove('input-error'); pesanError.textContent=''">
            </div>
            <p id="pesanError" style="font-size: 13px; color: #ef4444; margin-top: 4px; min-height: 18px;"></p>

            <div class="modal-warning">
                <span class="warning-icon">⚠️</span>
                <p class="poppins-regular">Setelah mulai, ujian akan masuk mode <strong>fullscreen</strong>.</p>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-secondary poppins-semibold" onclick="tutupModalKode()">Batal</button>
            <button class="btn-primary poppins-semibold" id="btnMulaiUjian" onclick="submitKode()">
                <span id="btnMulaiLabel">Mulai Ujian</span>
                <span id="btnMulaiLoader" style="display:none;"><i class="ph ph-circle-notch spin"></i></span>
            </button>
        </div>
        <input type="hidden" id="hiddenIdUjianModal">
    </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin 0.8s linear infinite; display:inline-block; }
.card-footer__btn { width: 100%; padding: 11px 20px; border-radius: 12px; font-size: 0.92rem; cursor: pointer; transition: all 0.25s ease; border: none; display: block; text-align: center; text-decoration: none; }
.btn-masuk-ujian { background: var(--color-gradient-primary); color: #fff; }
.btn-masuk-ujian:disabled { opacity: 0.45; cursor: not-allowed; }
.btn-lanjutkan { background: linear-gradient(135deg, #f97316, #ea580c); color: #fff; }
.btn-lihat-hasil { background: transparent; color: var(--color-foreground); border: 1.5px solid #d0d9e6; }
.btn-lihat-hasil:hover:not(:disabled) { background: #f1f5f9; }
.input-error { border-color: #ef4444 !important; }
</style>

<script>
const DIRNAME_US = "<?= $DIRNAME ?>";
const pesanError = document.getElementById('pesanError');

function bukaModalKode(id, nama) {
    document.getElementById('hiddenIdUjianModal').value = id;
    document.getElementById('modalNamaUjian').textContent = nama;
    document.getElementById('inputKodeAkses').value = '';
    pesanError.textContent = '';
    document.getElementById('modalOverlay').classList.add('active');
    setTimeout(() => document.getElementById('inputKodeAkses').focus(), 200);
}

function tutupModalKode() {
    document.getElementById('modalOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

function tutupModalOnOverlay(e) { if (e.target === document.getElementById('modalOverlay')) tutupModalKode(); }

function mulaiTanpaKode(id) { submitKodeInternal(id, ''); }

async function submitKode() {
    const kode = document.getElementById('inputKodeAkses').value.trim();
    const id = document.getElementById('hiddenIdUjianModal').value;
    if (!kode) {
        document.getElementById('inputKodeAkses').classList.add('input-error');
        pesanError.textContent = 'Kode tidak boleh kosong.';
        return;
    }
    submitKodeInternal(id, kode);
}

async function submitKodeInternal(id, kode) {
    const btnLabel = document.getElementById('btnMulaiLabel');
    const btnLoader = document.getElementById('btnMulaiLoader');
    
    btnLabel.style.display = 'none';
    btnLoader.style.display = 'inline-block';

    try {
        const res = await fetch(DIRNAME_US + 'ujianSiswa/cekKode', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_ujian: id, kode: kode })
        });
        const data = await res.json();

        if (data.success) {
            window.location.href = data.redirect;
        } else {
            pesanError.textContent = data.message;
            btnLabel.style.display = 'inline-block';
            btnLoader.style.display = 'none';
        }
    } catch (err) {
        pesanError.textContent = 'Terjadi kesalahan jaringan.';
        btnLabel.style.display = 'inline-block';
        btnLoader.style.display = 'none';
    }
}

function filterUjian(q) {
    const query = q.toLowerCase().trim();
    const cards = document.querySelectorAll('.ujian-card');
    let visible = 0;

    cards.forEach(card => {
        const match = !query || card.dataset.nama.includes(query) || card.dataset.mapel.includes(query);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    document.getElementById('searchEmpty').style.display = (visible === 0 && query) ? 'flex' : 'none';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') tutupModalKode();
    if (e.key === 'Enter' && document.getElementById('modalOverlay').classList.contains('active')) submitKode();
});
</script>