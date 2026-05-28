<?php
$ujian = $data['ujian'];
$soalList = $data['soalList'];
$jawabanSiswa = $data['jawabanSiswa'];
$sesiUjian = $data['sesiUjian'];
$sisaWaktu = (int) $data['sisaWaktu'];
$totalSoal = (int) $data['totalSoal'];
$id_ujian_siswa = $sesiUjian['id_ujian_siswa'];
$id_ujian = $ujian['id_ujian'];

$DIRNAME = Constant::DIRNAME;
?>

<div class="exam-shell">

    <header class="exam-topbar">
        <div class="exam-topbar__left">
            <span class="exam-mapel poppins-regular"><?= htmlspecialchars($ujian['deskripsi_ujian'] ?? '') ?></span>
            <h1 class="exam-title poppins-semibold"><?= htmlspecialchars($ujian['nama_ujian']) ?></h1>
        </div>

        <div class="exam-topbar__center">
            <span class="exam-soal-info poppins-medium" id="soalInfo">Soal 1 / <?= $totalSoal ?></span>
            <span class="exam-dijawab poppins-medium" id="dijawabBadge">
                <?= count($jawabanSiswa) ?> dijawab
            </span>
        </div>

        <div class="exam-topbar__right">
            <div class="exam-timer <?= $sisaWaktu < 300 ? 'exam-timer--danger' : '' ?>" id="timerBox">
                <i class="ph ph-clock"></i>
                <span id="timerDisplay">--:--:--</span>
            </div>
            <button class="exam-btn-fullscreen" id="btnFullscreen" title="Fullscreen">
                <i class="ph ph-arrows-out"></i>
            </button>
            <button class="exam-btn-submit poppins-semibold" id="btnSubmit">
                <i class="ph ph-paper-plane-tilt"></i> Submit
            </button>
        </div>
    </header>

    <div class="exam-body">
        <div class="exam-content-wrapper">

            <main class="exam-main" id="examMain">
                <?php foreach ($soalList as $idx => $soal):
                    $no = $idx + 1;
                    $id_soal = $soal['id_bank_soal'];
                    $jawabanDipilih = $jawabanSiswa[$id_soal] ?? null;
                    $isLast = ($no === $totalSoal);
                    $opsi = [
                        'ja' => $soal['ja'],
                        'jb' => $soal['jb'],
                        'jc' => $soal['jc'],
                        'jd' => $soal['jd'],
                    ];
                    $huruf = ['ja' => 'A', 'jb' => 'B', 'jc' => 'C', 'jd' => 'D'];
                    ?>
                    <div class="soal-card <?= $no === 1 ? 'soal-card--active' : '' ?>" id="soal-<?= $no ?>"
                        data-no="<?= $no ?>" data-id="<?= htmlspecialchars($id_soal) ?>">

                        <div class="soal-label">
                            <span class="soal-badge poppins-semibold">Soal <?= $no ?></span>
                            <span class="soal-instruksi poppins-regular">Pilih satu jawaban yang paling tepat.</span>
                        </div>

                        <p class="soal-teks poppins-regular">
                            Soal <?= $no ?>. <?= htmlspecialchars($soal['pertanyaan']) ?>
                        </p>

                        <?php if (!empty($soal['gambar'])): ?>
                            <div class="soal-gambar">
                                <img src="<?= $DIRNAME ?>asset/img/soal/<?= htmlspecialchars($soal['gambar']) ?>"
                                    alt="Gambar soal <?= $no ?>">
                            </div>
                        <?php endif; ?>

                        <div class="soal-opsi">
                            <?php foreach ($opsi as $key => $teksOpsi):
                                if ($teksOpsi === null || $teksOpsi === '')
                                    continue;
                                $selected = ($jawabanDipilih === $key);
                                ?>
                                <label class="opsi-item <?= $selected ? 'opsi-item--selected' : '' ?>" data-key="<?= $key ?>">
                                    <input type="radio" name="jawaban_<?= $no ?>" value="<?= $key ?>"
                                        <?= $selected ? 'checked' : '' ?> class="opsi-radio"
                                        data-soal-id="<?= htmlspecialchars($id_soal) ?>"
                                        data-sesi="<?= htmlspecialchars($id_ujian_siswa) ?>" />
                                    <span class="opsi-letter poppins-semibold"><?= $huruf[$key] ?></span>
                                    <span class="opsi-teks poppins-regular"><?= htmlspecialchars($teksOpsi) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="soal-nav">
                            <button class="btn-prev poppins-semibold" onclick="pindahSoal(<?= $no - 1 ?>)"
                                <?= $no === 1 ? 'disabled' : '' ?>>
                                <i class="ph ph-caret-left"></i> Sebelumnya
                            </button>

                            <?php if ($isLast): ?>
                                <button class="btn-next btn-next--submit poppins-semibold" onclick="bukaModalSubmit()">
                                    <i class="ph ph-paper-plane-tilt"></i> Submit
                                </button>
                            <?php else: ?>
                                <button class="btn-next poppins-semibold" onclick="pindahSoal(<?= $no + 1 ?>)">
                                    Selanjutnya <i class="ph ph-caret-right"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </main>

            <aside class="exam-sidebar">
                <div class="exam-sidebar__card">
                    <h3 class="exam-sidebar__title poppins-semibold">
                        <i class="ph ph-list-numbers"></i>
                        Navigasi Soal
                    </h3>

                    <div class="nav-grid" id="navGrid">
                        <?php foreach ($soalList as $idx => $soal):
                            $no = $idx + 1;
                            $id_soal = $soal['id_bank_soal'];
                            $sudahDijawab = isset($jawabanSiswa[$id_soal]);
                            $cls = $sudahDijawab ? 'nav-btn--answered' : '';
                            if ($no === 1)
                                $cls .= ' nav-btn--current';
                            ?>
                            <button class="nav-btn poppins-semibold <?= trim($cls) ?>" id="navBtn-<?= $no ?>"
                                onclick="pindahSoal(<?= $no ?>)"><?= $no ?></button>
                        <?php endforeach; ?>
                    </div>

                    <div class="nav-divider"></div>

                    <div class="nav-legend">
                        <div class="legend-item">
                            <span class="legend-dot legend-dot--current"></span>
                            <span class="poppins-regular">Sedang dilihat</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot legend-dot--answered"></span>
                            <span class="poppins-regular">Sudah dijawab</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot legend-dot--unanswered"></span>
                            <span class="poppins-regular">Belum dijawab</span>
                        </div>
                    </div>

                    <div class="nav-divider"></div>
                    <div class="exam-warning">
                        <i class="ph ph-warning"></i>
                        <p class="poppins-regular">Jangan refresh atau pindah tab. Aktivitas akan tercatat oleh
                            pengawas.</p>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</div>

<div class="modal-overlay" id="modalSubmit">
    <div class="modal-box-exam">
        <div class="modal-icon-wrap">
            <i class="ph ph-paper-plane-tilt"></i>
        </div>
        <h2 class="poppins-bold">Yakin ingin submit?</h2>
        <p class="poppins-regular" id="modalSubmitDesc">
            Kamu baru menjawab <strong id="jumlahDijawab">0</strong> dari <strong><?= $totalSoal ?></strong> soal.
            Jawaban yang belum dipilih tidak akan mendapat nilai.
        </p>
        <div class="modal-btn-group">
            <button class="modal-btn-cancel poppins-semibold" onclick="tutupModal()">Kembali</button>
            <button class="modal-btn-confirm poppins-semibold" id="btnConfirmSubmit">
                <i class="ph ph-check"></i> Submit Sekarang
            </button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalTimeout" style="display:none;">
    <div class="modal-box-exam modal-box-exam--danger">
        <div class="modal-icon-wrap modal-icon-wrap--danger">
            <i class="ph ph-timer"></i>
        </div>
        <h2 class="poppins-bold">Waktu Habis!</h2>
        <p class="poppins-regular">Waktu pengerjaan ujian telah berakhir. Jawaban kamu akan otomatis dikumpulkan.</p>
        <div class="modal-btn-group">
            <button class="modal-btn-confirm poppins-semibold" id="btnTimeoutSubmit">
                <i class="ph ph-check"></i> OK, Kumpulkan
            </button>
        </div>
    </div>
</div>

<input type="hidden" id="hiddenIdUjian" value="<?= htmlspecialchars($id_ujian) ?>">
<input type="hidden" id="hiddenIdUjianSiswa" value="<?= htmlspecialchars($id_ujian_siswa) ?>">
<input type="hidden" id="hiddenSisaWaktu" value="<?= $sisaWaktu ?>">
<input type="hidden" id="hiddenTotalSoal" value="<?= $totalSoal ?>">
<input type="hidden" id="hiddenJawabanAwal" value="<?= htmlspecialchars(json_encode(array_keys($jawabanSiswa))) ?>">
<input type="hidden" id="hiddenDirname" value="<?= $DIRNAME ?>">

<script>

    let soalAktif = 1;
    const totalSoal = parseInt(document.getElementById('hiddenTotalSoal').value);
    const DIRNAME = document.getElementById('hiddenDirname').value;
    const ID_UJIAN = document.getElementById('hiddenIdUjian').value;
    const ID_SESI = document.getElementById('hiddenIdUjianSiswa').value;

    const dijawabSet = new Set();

    try {
        const awal = JSON.parse(document.getElementById('hiddenJawabanAwal').value);
        document.querySelectorAll('.soal-card').forEach(card => {
            const idSoal = card.dataset.id;
            if (awal.includes(idSoal)) {
                dijawabSet.add(parseInt(card.dataset.no));
            }
        });
    } catch (e) { }

    let sisaDetik = parseInt(document.getElementById('hiddenSisaWaktu').value);

    function formatWaktu(detik) {
        const j = Math.floor(detik / 3600);
        const m = Math.floor((detik % 3600) / 60);
        const s = detik % 60;
        return [j, m, s].map(v => String(v).padStart(2, '0')).join(':');
    }

    function tickTimer() {
        if (sisaDetik <= 0) {
            document.getElementById('timerDisplay').textContent = '00:00:00';
            clearInterval(timerInterval);
            submitUjian(true);
            return;
        }
        document.getElementById('timerDisplay').textContent = formatWaktu(sisaDetik);

        if (sisaDetik <= 300) {
            document.getElementById('timerBox').classList.add('exam-timer--danger');
        }
        sisaDetik--;
    }

    const timerInterval = setInterval(tickTimer, 1000);
    tickTimer();

    function pindahSoal(no) {
        if (no < 1 || no > totalSoal) return;

        const lama = document.getElementById('soal-' + soalAktif);
        if (lama) lama.classList.remove('soal-card--active');
        const navLama = document.getElementById('navBtn-' + soalAktif);
        if (navLama) navLama.classList.remove('nav-btn--current');

        soalAktif = no;

        const baru = document.getElementById('soal-' + soalAktif);
        if (baru) baru.classList.add('soal-card--active');
        const navBaru = document.getElementById('navBtn-' + soalAktif);
        if (navBaru) navBaru.classList.add('nav-btn--current');

        document.getElementById('soalInfo').textContent = 'Soal ' + soalAktif + ' / ' + totalSoal;

        if (navBaru) navBaru.scrollIntoView({ block: 'nearest' });
    }

    document.querySelectorAll('.opsi-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            const soalId = this.dataset.soalId;
            const sesi = this.dataset.sesi;
            const answer = this.value;
            const noSoal = soalAktif;

            const soalCard = this.closest('.soal-card');
            soalCard.querySelectorAll('.opsi-item').forEach(lbl => lbl.classList.remove('opsi-item--selected'));
            this.closest('.opsi-item').classList.add('opsi-item--selected');

            dijawabSet.add(noSoal);
            const navBtn = document.getElementById('navBtn-' + noSoal);
            if (navBtn) navBtn.classList.add('nav-btn--answered');

            document.getElementById('dijawabBadge').textContent = dijawabSet.size + ' dijawab';

            fetch(DIRNAME + 'kerjakanUjian/simpanJawaban', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_ujian_siswa: sesi,
                    id_bank_soal: soalId,
                    answer: answer
                })
            }).catch(() => { });
        });
    });

    function bukaModalSubmit() {
        document.getElementById('jumlahDijawab').textContent = dijawabSet.size;
        document.getElementById('modalSubmit').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function tutupModal() {
        document.getElementById('modalSubmit').classList.remove('active');
        document.body.style.overflow = '';
    }

    function submitUjian(autoSubmit = false) {
        document.getElementById('modalSubmit').classList.remove('active');
        const modalTimeout = document.getElementById('modalTimeout');
        if (modalTimeout) modalTimeout.style.display = 'none';

        fetch(DIRNAME + 'kerjakanUjian/submit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_ujian_siswa: ID_SESI, id_ujian: ID_UJIAN })
        })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    window.location.href = DIRNAME + 'hasilUjian';
                }
            })
            .catch(() => {
                window.location.href = DIRNAME + 'hasilUjian';
            });
    }

    document.getElementById('btnSubmit').addEventListener('click', bukaModalSubmit);
    document.getElementById('btnConfirmSubmit').addEventListener('click', () => submitUjian(false));
    document.getElementById('modalSubmit').addEventListener('click', function (e) {
        if (e.target === this) tutupModal();
    });

    document.getElementById('btnTimeoutSubmit').addEventListener('click', () => submitUjian(true));

    document.getElementById('btnFullscreen').addEventListener('click', () => {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(() => { });
            document.getElementById('btnFullscreen').querySelector('i').className = 'ph ph-arrows-in';
        } else {
            document.exitFullscreen().catch(() => { });
            document.getElementById('btnFullscreen').querySelector('i').className = 'ph ph-arrows-out';
        }
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            console.warn('Siswa berpindah tab/window');
        }
    });

    // Auto Fullscreen on first click
    function triggerFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(() => { });
            document.getElementById('btnFullscreen').querySelector('i').className = 'ph ph-arrows-in';
        }
        document.removeEventListener('click', triggerFullscreen);
    }
    document.addEventListener('click', triggerFullscreen);

    window.addEventListener('load', () => {
        document.documentElement.requestFullscreen().catch(() => {
            console.log('Full-Screen di tolak');
        });
    });

</script>