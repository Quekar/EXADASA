<div class="container banksoal-container">
    <header class="banksoal-header">
        <div class="banksoal-title">
            <h1 class="poppins-semibold">Bank Soal</h1>
            <p class="poppins-regular">Kelola pertanyaan, impor dari CSV, atau kategori soal.</p>
        </div>
        <div class="header-actions">
            <button class="btn-icon btn-white" onclick="openModal('modalKategori')">
                <i class="ph ph-tag"></i> Kelola Kategori
            </button>
            <button class="btn-icon btn-primary" onclick="openModal('modalSoal')">
                <i class="ph ph-plus-circle"></i> Soal Baru
            </button>
        </div>
    </header>

    <div class="search-filter-card">
        <div class="search-input-wrapper">
            <i class="ph ph-magnifying-glass"></i>
            <input type="text" id="searchSoal" placeholder="Cari soal..." onkeyup="filterSoal()">
        </div>
        <select class="filter-select" id="filterKategori" onchange="filterSoal()">
            <option value="">Semua Kategori</option>
            <?php foreach ($data['kategori'] as $k): ?>
                <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
            <?php endforeach; ?>
        </select>
        <div style="display: flex; align-items: center; padding: 0 10px; color: #64748b; font-weight: 500;">
            <span id="soalCount" style="margin-right: 5px;"><?= count($data['soal']) ?></span> soal
        </div>
    </div>
    <div class="soal-grid" id="soalGrid">
        <?php if (count($data['soal']) > 0): ?>
            <?php foreach ($data['soal'] as $index => $s): ?>
                <div class="soal-card" data-kategori="<?= $s['id_kategori'] ?>" data-text="<?= strtolower($s['pertanyaan']) ?>">
                    <div class="soal-card-header">
                        <span class="soal-number">No. <?= $index + 1 ?></span>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="soal-badge"><?= $s['nama_kategori'] ?? 'Tanpa Kategori' ?></span>
                            <div class="soal-actions">
                                <button class="btn-action btn-edit"
                                    onclick="editSoal(<?= htmlspecialchars(json_encode($s)) ?>)">
                                    <i class="ph ph-pencil-simple"></i>
                                </button>
                                <button class="btn-action btn-delete"
                                    onclick="if(confirm('Hapus soal ini?')) window.location.href='<?= Constant::DIRNAME ?>banksoal/hapus/<?= $s['id_bank_soal'] ?>'">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="soal-text poppins-medium">
                        <?= $s['pertanyaan'] ?>
                    </div>
                    <div class="options-list">
                        <div class="option-item <?= $s['answer'] == 'ja' ? 'correct' : '' ?>">
                            <span class="option-label">A.</span> <?= $s['ja'] ?>
                        </div>
                        <div class="option-item <?= $s['answer'] == 'jb' ? 'correct' : '' ?>">
                            <span class="option-label">B.</span> <?= $s['jb'] ?>
                        </div>
                        <div class="option-item <?= $s['answer'] == 'jc' ? 'correct' : '' ?>">
                            <span class="option-label">C.</span> <?= $s['jc'] ?>
                        </div>
                        <div class="option-item <?= $s['answer'] == 'jd' ? 'correct' : '' ?>">
                            <span class="option-label">D.</span> <?= $s['jd'] ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="poppins-medium" style="text-align: center; grid-column: 1/3; padding: 20px;">
                <i class="ph ph-magnifying-glass"
                    style="font-size: 48px; color: var(--color-muted-foreground); margin-bottom: 24px; display: inline-block;"></i>
                <h1 class="poppins-semibold"
                    style="font-size: 18px; color: var(--color-muted-foreground); width: 400px; margin: auto;">Belum ada
                    soal di bank soal. Silahkan tambahkan soal baru atau impor dari CSV.</h1>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="modalSoal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalSoalTitle">Tambah Soal Baru</h2>
            <button class="btn-close" onclick="closeModal('modalSoal')">&times;</button>
        </div>
        <form action="<?= Constant::DIRNAME ?>banksoal/simpan" method="POST">
            <div class="modal-body">
                <input type="hidden" name="id_bank_soal" id="form_id_bank_soal">
                <div class="form-group">
                    <label>Pertanyaan</label>
                    <textarea name="pertanyaan" id="form_pertanyaan" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="id_kategori" id="form_id_kategori" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($data['kategori'] as $k): ?>
                            <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Opsi Jawaban</label>
                    <div style="display: grid; gap: 10px;">
                        <label for="" style="display: flex; align-items: center; gap: 10px;">
                            <span
                                style="display: inline-block; width: 40px; height: 40px; border-radius: 10px; background-color: var(--color-primary); color: white; display: flex; align-items: center; justify-content: center;">A</span>
                            <input type="text" name="ja" id="form_ja" class="form-control" placeholder="Opsi A"
                                required>
                        </label>
                        <label for="" style="display: flex; align-items: center; gap: 10px;">
                            <span
                                style="display: inline-block; width: 40px; height: 40px; border-radius: 10px; background-color: var(--color-primary); color: white; display: flex; align-items: center; justify-content: center;">B</span>
                            <input type="text" name="jb" id="form_jb" class="form-control" placeholder="Opsi B"
                                required>
                        </label>
                        <label for="" style="display: flex; align-items: center; gap: 10px;">
                            <span
                                style="display: inline-block; width: 40px; height: 40px; border-radius: 10px; background-color: var(--color-primary); color: white; display: flex; align-items: center; justify-content: center;">C</span>
                            <input type="text" name="jc" id="form_jc" class="form-control" placeholder="Opsi C"
                                required>
                        </label>
                        <label for="" style="display: flex; align-items: center; gap: 10px;">
                            <span
                                style="display: inline-block; width: 40px; height: 40px; border-radius: 10px; background-color: var(--color-primary); color: white; display: flex; align-items: center; justify-content: center;">D</span>
                            <input type="text" name="jd" id="form_jd" class="form-control" placeholder="Opsi D"
                                required>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Jawaban Benar</label>
                    <div class="radio-group">
                        <label class="radio-item">
                            <input type="radio" name="jawaban_benar" value="ja" id="form_ans_ja"> A
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="jawaban_benar" value="jb" id="form_ans_jb"> B
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="jawaban_benar" value="jc" id="form_ans_jc"> C
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="jawaban_benar" value="jd" id="form_ans_jd"> D
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-icon btn-white" onclick="closeModal('modalSoal')">Batal</button>
                <button type="submit" class="btn-icon btn-primary">Simpan Soal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kelola Kategori -->
<div id="modalKategori" class="modal">
    <div class="modal-content" style="width: 450px;">
        <div class="modal-header">
            <h2>Kelola Kategori</h2>
            <button class="btn-close" onclick="closeModal('modalKategori')">&times;</button>
        </div>
        <div class="modal-body">
            <form action="<?= Constant::DIRNAME ?>banksoal/tambah_kategori" method="POST"
                style="margin-bottom: 20px; display: flex; gap: 10px;">
                <input type="text" name="nama_kategori" class="form-control" placeholder="Nama kategori baru..."
                    required>
                <button type="submit" class="btn-icon btn-primary">Tambah</button>
            </form>
            <div style="max-height: 200px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 10px;">Nama Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['kategori'] as $k): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px;"><?= $k['nama_kategori'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-icon btn-white" onclick="closeModal('modalKategori')">Tutup</button>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'block';
        if (id === 'modalSoal') {
            document.getElementById('modalSoalTitle').innerText = 'Tambah Soal Baru';
            document.getElementById('form_id_bank_soal').value = '';
            document.querySelector('#modalSoal form').reset();
        }
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function editSoal(soal) {
        openModal('modalSoal');
        document.getElementById('modalSoalTitle').innerText = 'Edit Soal';
        document.getElementById('form_id_bank_soal').value = soal.id_bank_soal;
        document.getElementById('form_pertanyaan').value = soal.pertanyaan;
        document.getElementById('form_id_kategori').value = soal.id_kategori;
        document.getElementById('form_ja').value = soal.ja;
        document.getElementById('form_jb').value = soal.jb;
        document.getElementById('form_jc').value = soal.jc;
        document.getElementById('form_jd').value = soal.jd;
        document.getElementById('form_ans_' + soal.answer).checked = true;
    }

    function filterSoal() {
        const search = document.getElementById('searchSoal').value.toLowerCase();
        const kategori = document.getElementById('filterKategori').value;
        const cards = document.querySelectorAll('.soal-card');
        let count = 0;

        cards.forEach(card => {
            const textMatch = card.getAttribute('data-text').includes(search);
            const katMatch = !kategori || card.getAttribute('data-kategori') === kategori;

            if (textMatch && katMatch) {
                card.style.display = 'block';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        document.getElementById('soalCount').innerText = count;
    }

    // Close modal on click outside
    window.onclick = function (event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
        }
    }
</script>