<?php if ($data['halaman'] == 'index') : ?>
    <div class="container ujian">
        <div class="ujian-header">
            <div class="ujian-title">
                <h1 class="poppins-semibold">Manajemen Ujian</h1>
                <p class="poppins-regular">Buat dan kelola ujian untuk kelas anda.</p>
            </div>
            <a href="<?= Constant::DIRNAME; ?>ujian/tambah" class="btn-primary" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
                <i class="ph ph-plus"></i>
                Buat Ujian
            </a>
        </div>
        <div class="ujian-table">
            <table>
                <thead>
                    <tr>
                        <th>Nama Ujian</th>
                        <th>Kelas</th>
                        <th>Jadwal</th>
                        <th>Penilaian</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['ujian'])): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">Belum ada data ujian.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['ujian'] as $u): ?>
                            <tr>
                                <td>
                                    <h2 class="poppins-semibold"><?= $u['nama_ujian'] ?></h2>
                                    <div class="ujian-info">
                                        <span><i class="ph ph-clock"></i> <?= $u['waktu_pengerjaan'] ?>m</span>
                                        <span><i class="ph ph-key"></i> <?= $u['kode_ujian'] ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div><?= $u['tingkat'] ?> - <?= $u['nama_jurusan'] ?></div>
                                    <?php if($u['jml_kelas'] > 1): ?>
                                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 500;">+ <?= $u['jml_kelas'] - 1 ?> kelas lainnya</div>
                                    <?php endif; ?>
                                </td>
                                <td><div><?= $u['jadwal_mulai'] ?></div></td>
                                <td><span class="<?= strtolower($u['penilaian']) ?>"><?= $u['penilaian'] ?></span></td>
                                <td><span class="<?= $u['status'] == 'aktif' ? 'active' : 'inactive' ?>"><?= $u['status'] ?></span></td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <a href="<?= Constant::DIRNAME ?>ujian/edit/<?= $u['id_ujian'] ?>" class="btn-edit" style="display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; background: #f0f7ff; color: #3b82f6; border: none; cursor: pointer; text-decoration: none;"><i class="ph ph-pencil"></i></a>
                                        <button onclick="if(confirm('Hapus ujian ini?')) window.location.href='<?= Constant::DIRNAME ?>ujian/hapus/<?= $u['id_ujian'] ?>'" class="btn-danger" style="display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; background: #fff1f2; color: #ef4444; border: none; cursor: pointer;"><i class="ph ph-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($data['halaman'] == 'tambah' || $data['halaman'] == 'edit') : ?>
    <main class="ujian-container poppins-regular container">
        <header class="ujian-header-tambah">
            <div class="header-left">
                <a href="<?= Constant::DIRNAME; ?>ujian" class="btn-back"><i class="ph ph-arrow-left"></i></a>
                <div>
                    <h1><?= $data['halaman'] == 'edit' ? 'Edit Ujian' : 'Buat Ujian Baru' ?></h1>
                    <p>Sesuaikan informasi ujian dan bank soal.</p>
                </div>
            </div>
            <div class="header-right">
                <a href="<?= Constant::DIRNAME; ?>ujian" style="text-decoration: none;" class="btn-secondary">Batal</a>
                <button type="submit" form="form-ujian" class="btn-primary">Simpan Ujian</button>
            </div>
        </header>

        <form id="form-ujian" action="<?= Constant::DIRNAME; ?>ujian/simpan" method="POST" enctype="multipart/form-data">
            <?php if ($data['halaman'] == 'edit'): ?>
                <input type="hidden" name="id_ujian" value="<?= $data['ujian']['id_ujian'] ?>">
            <?php endif; ?>
            <input type="hidden" name="id_user" value="<?= $_SESSION['user']['id'] ?? 1; ?>">
                
            <section class="ujian-card">
                <div class="card-header">
                    <h3><i class="ph ph-info" style="color:var(--color-primary); ;"></i> Informasi Utama</h3>
                </div>
                <div class="card-body">
                    <div class="form-group-row">
                        <div class="form-input">
                            <label>Nama Ujian</label>
                            <input type="text" name="nama_ujian" value="<?= $data['ujian']['nama_ujian'] ?? '' ?>" required>
                        </div>
                    </div>
                    <div class="form-group-row">
                        <div class="form-input">
                            <label>Deskripsi Ujian</label>
                            <textarea name="deskripsi_ujian" rows="2"><?= $data['ujian']['deskripsi_ujian'] ?? '' ?></textarea>
                        </div>
                    </div>
                    <div class="form-group-grid">
                        <div class="form-input">
                            <label>Pilih Kelas</label>
                            <div class="class-selection-container" style="max-height: 150px; overflow-y: auto; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc;">
                                <div style="margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                                    <label style="display: flex; align-items: center; justify-content: flex-start; gap: 8px; cursor: pointer; font-weight: 600;">
                                        <input type="checkbox" id="selectAllKelas" style="width: 20px;" onclick="toggleSelectAll(this)"> Pilih Semua
                                    </label>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
                                    <?php 
                                    $selectedKelas = isset($data['ujian']['id_kelas']) ? json_decode($data['ujian']['id_kelas'], true) : [];
                                    if (!is_array($selectedKelas)) $selectedKelas = [];
                                    foreach($data['kelas'] as $kls) : ?>
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem;">
                                            <input type="checkbox" name="id_kelas[]" class="class-checkbox" style="width: 20px;" value="<?= $kls['id_kelas']; ?>" <?= in_array($kls['id_kelas'], $selectedKelas) ? 'checked' : '' ?>>
                                            <?= $kls['tingkat'] ?> - <?= $kls['nama_jurusan'] ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-input">
                            <label>Kode Ujian</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" name="kode_ujian" id="kode_ujian" value="<?= $data['ujian']['kode_ujian'] ?? '' ?>" required style="flex: 1;">
                                <button type="button" class="btn-primary" onclick="generateKode()" style="padding: 0 15px; border-radius: 12px; font-size: 0.8rem; white-space: nowrap;">
                                     Generate
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-grid">
                        <div class="form-input">
                            <label>Jadwal Mulai</label>
                            <input type="datetime-local" name="jadwal_mulai" value="<?= isset($data['ujian']['jadwal_mulai']) ? date('Y-m-d\TH:i', strtotime($data['ujian']['jadwal_mulai'])) : '' ?>" required>
                        </div>
                        <div class="form-input">
                            <label>Jadwal Selesai</label>
                            <input type="datetime-local" name="jadwal_selesai" value="<?= isset($data['ujian']['jadwal_selesai']) ? date('Y-m-d\TH:i', strtotime($data['ujian']['jadwal_selesai'])) : '' ?>" required>
                        </div>
                    </div>
                    <div class="form-group-grid">
                        <div class="form-input">
                            <label>Waktu (Menit)</label>
                            <?php 
                                $waktu = $data['ujian']['waktu_pengerjaan'] ?? '00:00:00';
                                $parts = explode(':', $waktu);
                                $totalMenit = (isset($parts[0]) && isset($parts[1])) ? ((int)$parts[0] * 60 + (int)$parts[1]) : 0;
                            ?>
                            <input type="number" name="waktu_pengerjaan" value="<?= $totalMenit ?>" required>
                        </div>
                        <div class="form-input">
                            <label>Penilaian</label>
                            <select name="penilaian">
                                <option value="Otomatis" <?= (isset($data['ujian']['penilaian']) && $data['ujian']['penilaian'] == 'Otomatis') ? 'selected' : '' ?>>Otomatis</option>
                                <option value="Manual" <?= (isset($data['ujian']['penilaian']) && $data['ujian']['penilaian'] == 'Manual') ? 'selected' : '' ?>>Manual</option>
                            </select>
                        </div>
                        <div class="form-input">
                            <label>Status</label>
                            <select name="status">
                                <option value="Aktif" <?= (isset($data['ujian']['status']) && $data['ujian']['status'] == 'Aktif') ? 'selected' : '' ?>>Aktif</option>
                                <option value="Nonaktif" <?= (isset($data['ujian']['status']) && $data['ujian']['status'] == 'Nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <div class="input-method-selector" style="display: flex; gap: 10px; margin: 25px 0;">
                <button type="button" id="btn-mode-manual" class="btn-tab active" onclick="switchMode('manual')">
                    <i class="ph ph-pencil-simple"></i> Input Manual
                </button>
                <button type="button" id="btn-mode-bank" class="btn-tab" onclick="switchMode('bank')">
                    <i class="ph ph-database"></i> Pilih dari Bank Soal
                </button>
                <button type="button" id="btn-mode-csv" class="btn-tab" onclick="switchMode('csv')">
                    <i class="ph ph-file-csv"></i> Import CSV
                </button>
            </div>

            <div id="section-bank" style="display: none; margin-bottom: 25px;">
                <div class="ujian-card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h3><i class="ph ph-database" style="color: var(--color-primary);"></i> Pilih dari Bank Soal</h3>
                        <div class="form-input">
                            <select id="filterBankKategori" class="form-control" style="width: 200px;" onchange="filterBankSoal()">
                                <option value="">Semua Kategori</option>
                                <?php foreach($data['kategori'] as $k): ?>
                                    <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <div id="bank-soal-list" style="display: grid; gap: 15px;">
                            <?php 
                            $currentSoalIds = isset($data['soal']) ? array_column($data['soal'], 'id_bank_soal') : [];
                            foreach($data['bank_soal'] as $bs): 
                            ?>
                                <label class="bank-soal-item" data-kategori="<?= $bs['id_kategori'] ?>" style="display: flex; gap: 15px; padding: 15px; border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.3s;">
                                    <input type="checkbox" name="selected_soal[]" value="<?= $bs['id_bank_soal'] ?>" <?= in_array($bs['id_bank_soal'], $currentSoalIds) ? 'checked' : '' ?> style="width: 20px; height: 20px; margin-top: 5px;">
                                    <div>
                                        <div style="font-weight: 600; color: #1e293b; margin-bottom: 5px;"><?= $bs['pertanyaan'] ?></div>
                                        <div style="font-size: 0.8rem; color: #64748b;">
                                            <span style="background: #f1f5f9; padding: 2px 8px; border-radius: 10px;"><?= $bs['nama_kategori'] ?? 'Tanpa Kategori' ?></span>
                                            <span style="margin-left: 10px;">A: <?= $bs['ja'] ?> | B: <?= $bs['jb'] ?> | C: <?= $bs['jc'] ?> | D: <?= $bs['jd'] ?></span>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="section-manual">
                <div id="soal-container">
                    <?php if ($data['halaman'] == 'edit' && !empty($data['soal'])): ?>
                        <?php foreach($data['soal'] as $index => $s): ?>
                            <div class="ujian-card soal-item">
                                <div class="card-header-soal">
                                    <span class="nomor-label">Soal #<?= $index + 1 ?></span>
                                    <button type="button" class="btn-remove-soal" onclick="hapusSoal(this)"><i class="ph ph-trash"></i></button>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" name="id_bank_soal_manual[]" value="<?= $s['id_bank_soal'] ?>">
                                    <div class="form-input" style="margin-bottom: 15px;">
                                        <label>Pertanyaan</label>
                                        <textarea name="soal_text[]" rows="2" required><?= $s['pertanyaan'] ?></textarea>
                                    </div>
                                    <div class="form-input" style="margin-bottom: 15px;">
                                        <label><i class="ph ph-image"></i> Gambar (Opsional)</label>
                                        <input type="file" name="soal_gambar[]" accept="image/*">
                                    </div>
                                    <div class="options-grid">
                                        <?php foreach(['A','B','C','D'] as $opt) : ?>
                                            <div class="option-row">
                                                <input type="radio" name="jawaban_benar[<?= $index ?>]" value="<?= $opt ?>" <?= ($s['answer'] == 'j'.strtolower($opt)) ? 'checked' : '' ?>>
                                                <input type="text" name="opsi_<?= strtolower($opt) ?>[]" value="<?= $s['j'.strtolower($opt)] ?>" placeholder="Opsi <?= $opt ?>" required>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" id="btn-tambah-soal" class="btn-tambah-soal">
                    <i class="ph ph-plus-circle"></i> Tambah Pertanyaan
                </button>
            </div>

            <div id="section-csv" style="display: none;">
                <div class="import-csv-box" style="border: 2px dashed #3b82f6; border-radius: 12px; padding: 40px; text-align: center; background: #f8fafc;">
                    <i class="ph ph-cloud-arrow-up" style="font-size: 48px; color: #3b82f6;"></i>
                    <h3 style="margin-top: 15px;">Upload File CSV</h3>
                    <input type="file" name="file_csv" id="file_csv" accept=".csv" style="display: none;">
                    <button type="button" class="btn-simpan" onclick="document.getElementById('file_csv').click()">Pilih File</button>
                    <div id="csv-file-name" style="margin-top: 10px; color: #10b981;"></div>
                </div>
            </div>
        </form>
    </main>

    <template id="template-soal">
        <div class="ujian-card soal-item">
            <div class="card-header-soal">
                <span class="nomor-label">Soal #1</span>
                <button type="button" class="btn-remove-soal" onclick="hapusSoal(this)"><i class="ph ph-trash"></i></button>
            </div>
            <div class="card-body">
                <input type="hidden" name="id_bank_soal_manual[]" value="">
                <div class="form-input" style="margin-bottom: 15px;">
                    <label>Pertanyaan</label>
                    <textarea name="soal_text[]" rows="2" required></textarea>
                </div>
                <div class="form-input" style="margin-bottom: 15px;">
                    <label><i class="ph ph-image"></i> Gambar (Opsional)</label>
                    <input type="file" name="soal_gambar[]" accept="image/*">
                </div>
                <div class="options-grid">
                    <?php foreach(['A','B','C','D'] as $opt) : ?>
                        <div class="option-row">
                            <input type="radio" name="jawaban_benar[INDEX]" value="<?= $opt ?>">
                            <input type="text" name="opsi_<?= strtolower($opt) ?>[]" placeholder="Opsi <?= $opt ?>" required>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </template>

    <script>
        const container = document.getElementById('soal-container');
        const template = document.getElementById('template-soal');
        const btnTambah = document.getElementById('btn-tambah-soal');

        btnTambah.onclick = () => {
            const clone = template.content.cloneNode(true);
            container.appendChild(clone);
            aturUlangNomor();
        };

        function aturUlangNomor() {
            if (!container) return;
            container.querySelectorAll('.soal-item').forEach((soal, index) => {
                soal.querySelector('.nomor-label').innerText = `Soal #${index + 1}`;
                soal.querySelectorAll('input[type="radio"]').forEach(r => r.name = `jawaban_benar[${index}]`);
            });
        }

        function hapusSoal(btn) {
            if (confirm('Hapus soal ini?')) { 
                btn.closest('.soal-item').remove(); 
                aturUlangNomor(); 
            }
        }

        function switchMode(mode) {
            const sections = {
                'manual': document.getElementById('section-manual'),
                'bank': document.getElementById('section-bank'),
                'csv': document.getElementById('section-csv')
            };

            const buttons = {
                'manual': document.getElementById('btn-mode-manual'),
                'bank': document.getElementById('btn-mode-bank'),
                'csv': document.getElementById('btn-mode-csv')
            };

            Object.keys(sections).forEach(key => {
                if (key === mode) {
                    sections[key].style.display = 'block';
                    buttons[key].classList.add('active');
                    sections[key].querySelectorAll('input, textarea, select').forEach(el => el.disabled = false);
                } else {
                    sections[key].style.display = 'none';
                    buttons[key].classList.remove('active');
                    if (key !== 'bank' || mode === 'csv') {
                        sections[key].querySelectorAll('input, textarea, select').forEach(el => {
                            if (el.name !== 'selected_soal[]') el.disabled = true;
                        });
                    }
                }
            });
        }

        function filterBankSoal() {
            const kategori = document.getElementById('filterBankKategori').value;
            const items = document.querySelectorAll('.bank-soal-item');
            items.forEach(item => {
                if (!kategori || item.getAttribute('data-kategori') === kategori) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function generateKode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 8; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('kode_ujian').value = result;
        }

        function toggleSelectAll(source) {
            checkboxes = document.getElementsByClassName('class-checkbox');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        document.getElementById('file_csv').onchange = function() {
            document.getElementById('csv-file-name').innerText = "File terpilih: " + this.files[0].name;
        };

        window.onload = () => { 
            aturUlangNomor();
        };
    </script>
<?php endif; ?>