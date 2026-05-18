<div class="container hasil-ujian-container">
    <header class="hasil-ujian-header">
        <h1>Hasil Ujian</h1>
        <p>Riwayat dan detail nilai hasil ujianmu.</p>
    </header>

    <div class="hasil-ujian-content">
        <div class="hasil-ujian-card">
            <div class="hasil-ujian-info">
                <span>RATA-RATA NILAI</span>
                <h3><?= number_format($data['stats']['rata_nilai'] ?? 0, 1); ?></h3>
            </div>
            <div class="hasil-ujian-icon icon-green">
                <i class="ph ph-trophy"></i>
            </div>
        </div>

        <div class="hasil-ujian-card">
            <div class="hasil-ujian-info">
                <span>UJIAN SELESAI</span>
                <h3><?= $data['stats']['ujian_selesai'] ?? 0; ?></h3>
            </div>
            <div class="hasil-ujian-icon icon-blue">
                <i class="ph ph-target"></i>
            </div>
        </div>

        <div class="hasil-ujian-card">
            <div class="hasil-ujian-info">
                <span>MENUNGGU HASIL</span>
                <h3><?= $data['stats']['menunggu_hasil'] ?? 0; ?></h3>
            </div>
            <div class="hasil-ujian-icon icon-orange">
                <i class="ph ph-clock"></i>
            </div>
        </div>
    </div>

    <div class="hasil-ujian-list">
        <?php if (!empty($data['riwayat'])) : ?>
            <?php foreach ($data['riwayat'] as $r) : ?>
                
                <?php if ($r['publik'] == 1) : ?>
                    <div class="hasil-ujian-card">
                        <div class="skor-ujian" style="background: conic-gradient(#337ceb <?= round($r['nilai']); ?>%, #f0f4f8 0);">
                            <div class="skor-border">
                                <strong><?= round($r['nilai']); ?></strong>
                                <strong>SKOR</strong>
                            </div>
                        </div>

                        <div class="hasil-ujian-info">
                            <span class="nama-ujian"><?= strtoupper($r['nama_mapel'] ?? 'UMUM'); ?></span>
                            <h4><?= $r['nama_ujian']; ?></h4>
                            <div class="status">
                                <span class="status-benar"><i class="ph ph-check"></i> <?= $r['total_benar']; ?> BENAR</span>
                                <span class="status-salah"><i class="ph ph-x"></i> <?= $r['total_salah']; ?> SALAH</span>
                                <span class="status-rank"><i class="ph ph-trophy"></i> Peringkat <?= $r['peringkat'] . '/' . $r['total_peserta']; ?></span>
                                
                                <?php if ($r['nilai'] >= 75) : ?>
                                    <span class="status-lulus"><i class="ph ph-target"></i> LULUS</span>
                                <?php else : ?>
                                    <span class="status-salah" style="background: #fee2e2; color: #ef4444;"><i class="ph ph-x"></i> REMEDIAL</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button class="btn-detail">Detail Jawaban</button>
                    </div>

                <?php else : ?>
                    <div class="hasil-ujian-card">
                        <div class="hasil-ujian-status">
                            <div class="icon-pending">
                                <div class="icon-box-orange">
                                    <i class="ph ph-clock"></i>
                                </div>
                            </div>
                        </div>

                        <div class="hasil-ujian-pending">
                            <span class="ujian-pending"><?= strtoupper($r['nama_mapel'] ?? 'UMUM'); ?></span>
                            <h4><?= $r['nama_ujian']; ?></h4>
                            <p>Hasil ujian belum tersedia. Silakan tunggu sampai petugas mempublish hasil.</p>
                        </div>

                        <div class="hasil-ujian-action">
                            <span class="action-menunggu">Menunggu Publish</span>
                        </div>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php else : ?>
            <p class="poppins-regular" style="text-align: center; width: 100%; color: #64748b; padding: 20px 0;">Belum ada riwayat ujian.</p>
        <?php endif; ?>
    </div>
</div>