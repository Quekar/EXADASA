<?php
$student = $data['student'];
$questions = $data['questions'];
$totalSoal = $data['totalSoal'];
$benar = $data['benar'];
$salah = $data['salah'];
$skorTotal = $data['skorTotal'];
$skorMax = $data['skorMax'];
$persentase = $data['persentase'];
?>

<div class="page-content container">

    <div class="detail-topbar">
        <a href="<?= Constant::DIRNAME ?>koreksi" class="btn-back poppins-medium">
            <i class="ph ph-arrow-left"></i>
            Kembali
        </a>
        <div class="detail-topbar__status">
            <?php if ($student['status'] === 'published'): ?>
                <span class="badge badge--published poppins-medium"><i class="ph ph-check-circle"></i> Published</span>
            <?php elseif ($student['status'] === 'corrected'): ?>
                <span class="badge badge--corrected poppins-medium"><i class="ph ph-checks"></i> Corrected</span>
            <?php elseif ($student['status'] === 'pending'): ?>
                <span class="badge badge--pending poppins-medium"><i class="ph ph-clock"></i> Menunggu Koreksi</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="student-card">
        <div class="student-card__left">
            <div class="student-card__avatar <?= $student['av'] ?> poppins-semibold"><?= $student['inisial'] ?></div>
            <div class="student-card__info">
                <h2 class="student-card__name poppins-semibold"><?= $student['nama'] ?></h2>
                <p class="student-card__class poppins-regular"><?= $student['kelas'] ?></p>
            </div>
        </div>
        <div class="student-card__meta">
            <div class="meta-item">
                <i class="ph ph-clipboard-text"></i>
                <div>
                    <span class="meta-item__label poppins-regular">Ujian</span>
                    <span class="meta-item__value poppins-medium"><?= $student['ujian'] ?></span>
                </div>
            </div>
            <div class="meta-item">
                <i class="ph ph-calendar-check"></i>
                <div>
                    <span class="meta-item__label poppins-regular">Waktu Submit</span>
                    <span class="meta-item__value poppins-medium"><?= $student['waktu_submit'] ?></span>
                </div>
            </div>
            <div class="meta-item">
                <i class="ph ph-timer"></i>
                <div>
                    <span class="meta-item__label poppins-regular">Durasi</span>
                    <span class="meta-item__value poppins-medium"><?= $student['durasi'] ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="score-summary">
        <div class="score-summary__card score-summary__card--total">
            <div class="score-summary__icon">
                <i class="ph ph-chart-pie-slice"></i>
            </div>
            <div class="score-summary__detail">
                <span class="score-summary__label poppins-regular">Total Soal</span>
                <span class="score-summary__value poppins-bold"><?= $totalSoal ?></span>
            </div>
        </div>
        <div class="score-summary__card score-summary__card--correct">
            <div class="score-summary__icon">
                <i class="ph ph-check-circle"></i>
            </div>
            <div class="score-summary__detail">
                <span class="score-summary__label poppins-regular">Benar</span>
                <span class="score-summary__value poppins-bold"><?= $benar ?></span>
            </div>
        </div>
        <div class="score-summary__card score-summary__card--wrong">
            <div class="score-summary__icon">
                <i class="ph ph-x-circle"></i>
            </div>
            <div class="score-summary__detail">
                <span class="score-summary__label poppins-regular">Salah</span>
                <span class="score-summary__value poppins-bold"><?= $salah ?></span>
            </div>
        </div>
        <div class="score-summary__card score-summary__card--score">
            <div class="score-summary__icon">
                <i class="ph ph-trophy"></i>
            </div>
            <div class="score-summary__detail">
                <span class="score-summary__label poppins-regular">Skor</span>
                <span class="score-summary__value poppins-bold"><?= $skorTotal ?> / <?= $skorMax ?> <small
                        class="poppins-regular">(<?= $persentase ?>%)</small></span>
            </div>
        </div>
    </div>

    <div class="questions-section">
        <div class="questions-section__header">
            <h3 class="poppins-semibold"><i class="ph ph-list-numbers"></i> Daftar Jawaban Siswa</h3>
            <div class="questions-section__tabs">
                <button type="button" class="tab-btn tab-btn--active poppins-medium" data-filter="all">Semua</button>
                <button type="button" class="tab-btn poppins-medium" data-filter="benar">Benar</button>
                <button type="button" class="tab-btn poppins-medium" data-filter="salah">Salah</button>
            </div>
        </div>

        <?php foreach ($questions as $q): ?>
            <div class="question-card" data-status="<?= $q['status'] ?>">
                <div class="question-card__header">
                    <div class="question-card__number">
                        <span class="q-number poppins-bold"><?= $q['no'] ?></span>
                        <span class="q-type q-type--pg poppins-medium">Pilihan Ganda</span>
                    </div>
                    <div class="question-card__points">
                        <span class="poppins-medium">Bobot: <?= $q['skor_max'] ?> poin</span>
                    </div>
                </div>

                <div class="question-card__body">
                    <div class="question-card__soal">
                        <p class="poppins-regular"><?= $q['soal'] ?></p>
                    </div>

                    <div class="pg-answer-section">
                        <div class="pg-options">
                            <?php foreach ($q['opsi'] as $huruf => $teks): ?>
                                <?php
                                $isSelected = ($q['jawaban_siswa'] === $huruf);
                                $isCorrect = ($q['kunci'] === $huruf);
                                $optClass = '';
                                if ($isSelected && $isCorrect)
                                    $optClass = 'pg-opt--correct-selected';
                                elseif ($isSelected && !$isCorrect)
                                    $optClass = 'pg-opt--wrong-selected';
                                elseif ($isCorrect)
                                    $optClass = 'pg-opt--correct';
                                ?>
                                <div class="pg-opt <?= $optClass ?> poppins-regular">
                                    <span class="pg-opt__letter poppins-semibold"><?= $huruf ?></span>
                                    <span class="pg-opt__text"><?= $teks ?></span>
                                    <?php if ($isSelected && $isCorrect): ?>
                                        <i class="ph ph-check-circle pg-opt__icon pg-opt__icon--correct"></i>
                                    <?php elseif ($isSelected && !$isCorrect): ?>
                                        <i class="ph ph-x-circle pg-opt__icon pg-opt__icon--wrong"></i>
                                    <?php elseif ($isCorrect): ?>
                                        <i class="ph ph-check pg-opt__icon pg-opt__icon--key"></i>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="pg-result <?= $q['status'] === 'benar' ? 'pg-result--correct' : 'pg-result--wrong' ?>">
                            <?php if ($q['status'] === 'benar'): ?>
                                <i class="ph ph-check-circle"></i>
                                <span class="poppins-semibold">Benar</span>
                                <span class="pg-result__score poppins-bold">+<?= $q['skor'] ?> poin</span>
                            <?php else: ?>
                                <i class="ph ph-x-circle"></i>
                                <span class="poppins-semibold">Salah</span>
                                <span class="pg-result__score poppins-bold">0 poin</span>
                            <?php endif; ?>
                        </div>
                    </div>

                
                    <div class="koreksi-actions">
                        <span class="koreksi-actions__label poppins-medium"><i class="ph ph-pencil-simple"></i> Penilaian Petugas:</span>
                        <div class="koreksi-actions__buttons">
                            <button type="button" class="koreksi-btn koreksi-btn--benar <?= $q['status'] === 'benar' ? 'koreksi-btn--active' : '' ?> poppins-semibold" data-no="<?= $q['no'] ?>" data-value="benar" data-skor="<?= $q['skor_max'] ?>">
                                <i class="ph ph-check-circle"></i> Benar
                            </button>
                            <button type="button" class="koreksi-btn koreksi-btn--salah <?= $q['status'] === 'salah' ? 'koreksi-btn--active' : '' ?> poppins-semibold" data-no="<?= $q['no'] ?>" data-value="salah" data-skor="<?= $q['skor_max'] ?>">
                                <i class="ph ph-x-circle"></i> Salah
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <div class="simpan-nilai-section" id="simpanNilaiSection">
        <div class="simpan-nilai-card">
            <div class="simpan-nilai__header">
                <h3 class="poppins-semibold"><i class="ph ph-exam"></i> Ringkasan Koreksi</h3>
                <span class="simpan-nilai__badge poppins-medium" id="badgeStatusKoreksi">
                    <i class="ph ph-clock"></i> Belum Lengkap
                </span>
            </div>
            <div class="simpan-nilai__stats">
                <div class="simpan-nilai__stat simpan-nilai__stat--benar">
                    <i class="ph ph-check-circle"></i>
                    <div>
                        <span class="stat-label poppins-regular">Benar</span>
                        <span class="stat-value poppins-bold" id="totalBenarKoreksi">0</span>
                    </div>
                </div>
                <div class="simpan-nilai__stat simpan-nilai__stat--salah">
                    <i class="ph ph-x-circle"></i>
                    <div>
                        <span class="stat-label poppins-regular">Salah</span>
                        <span class="stat-value poppins-bold" id="totalSalahKoreksi">0</span>
                    </div>
                </div>
                <div class="simpan-nilai__stat simpan-nilai__stat--belum">
                    <i class="ph ph-hourglass"></i>
                    <div>
                        <span class="stat-label poppins-regular">Belum Dinilai</span>
                        <span class="stat-value poppins-bold" id="totalBelumKoreksi"><?= $totalSoal ?></span>
                    </div>
                </div>
                <div class="simpan-nilai__stat simpan-nilai__stat--skor">
                    <i class="ph ph-trophy"></i>
                    <div>
                        <span class="stat-label poppins-regular">Skor</span>
                        <span class="stat-value poppins-bold"><span id="skorKoreksi">0</span> / <?= $skorMax ?> <small class="poppins-regular">(<span id="persenKoreksi">0</span>%)</small></span>
                    </div>
                </div>
            </div>
            <div class="simpan-nilai__warning" id="warningBelumLengkap">
                <i class="ph ph-warning-circle"></i>
                <span class="poppins-medium">Masih ada soal yang belum dikoreksi. Tandai semua soal terlebih dahulu.</span>
            </div>
            <div class="simpan-nilai__actions">
                <button type="button" class="btn-simpan-nilai poppins-semibold" id="btnSimpanNilai" disabled>
                    <i class="ph ph-floppy-disk"></i> Simpan Nilai
                </button>
            </div>
        </div>
    </div>

    <div class="bottom-action-bar">
        <div class="bottom-action-bar__left">
            <div class="score-preview">
                <div class="score-preview__item">
                    <i class="ph ph-check-circle" style="color: #16a34a;"></i>
                    <span class="poppins-regular">Benar:</span>
                    <span class="poppins-bold"><?= $benar ?></span>
                </div>
                <div class="score-preview__divider"></div>
                <div class="score-preview__item">
                    <i class="ph ph-x-circle" style="color: #ef4444;"></i>
                    <span class="poppins-regular">Salah:</span>
                    <span class="poppins-bold"><?= $salah ?></span>
                </div>
                <div class="score-preview__divider"></div>
                <div class="score-preview__item score-preview__item--total">
                    <i class="ph ph-trophy" style="color: var(--color-primary);"></i>
                    <span class="poppins-medium">Skor:</span>
                    <span class="poppins-bold"><?= $skorTotal ?> / <?= $skorMax ?></span>
                    <span class="score-preview__persen poppins-semibold">(<?= $persentase ?>%)</span>
                </div>
            </div>
        </div>
        <div class="bottom-action-bar__right">
            <a href="<?= Constant::DIRNAME ?>koreksi" class="btn-back-bottom poppins-semibold">
                <i class="ph ph-arrow-left"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<script>
    window.koreksiData = {
        idUjianSiswa: '<?= $student['id_ujian_siswa'] ?>',
        idUjian: '<?= $student['id_ujian'] ?>',
        nisn: '<?= $student['nisn'] ?>',
        totalSoal: <?= $totalSoal ?>,
        skorMax: <?= $skorMax ?>,
        baseUrl: '<?= Constant::DIRNAME ?>'
    };
</script>
<script src="<?= Constant::DIRNAME ?>js/koreksi_detail.js" defer></script>