<div class="container jurusan">
    <div class="jurusan-header">
        <div class="jurusan-title">
            <h1 class="poppins-bold">Kelola Jurusan</h1>
            <p class="poppins-regular">Kelola informasi jurusan dan lihat distribusi populasi siswa.</p>
        </div>
        <button class="btn-primary" id="btnTambahJurusan">
            <i class="ph ph-plus"></i>
            Tambah Jurusan
        </button>
    </div>

    <div class="jurusan-stats-grid">
        <?php
        $colors = ['icon-blue', 'icon-purple', 'icon-pink', 'icon-red', 'icon-green', 'icon-cyan'];
        $i = 0;
        foreach ($data['stats'] as $stat):
            $colorClass = $colors[$i % count($colors)];
            $i++;
            ?>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon <?= $colorClass ?>">
                        <i class="ph ph-graduation-cap"></i>
                    </div>
                    <div class="stat-value">
                        <span class="stat-number"><?= $stat['total_siswa'] ?></span>
                        <span class="stat-label">Total Siswa</span>
                    </div>
                </div>
                <div class="stat-footer">
                    <span class="stat-major-name"><?= $stat['id_jurusan'] ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="jurusan-content">
        <div class="content-header">
            <i class="ph ph-list-bullets"></i>
            <h2 class="poppins-semibold">Daftar Jurusan</h2>
        </div>
        <div class="jurusan-table-wrapper">
            <table class="jurusan-table">
                <thead>
                    <tr>
                        <th>Jurusan</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['jurusan'] as $j): ?>
                        <tr>
                            <td>
                                <div class="major-info">
                                    <span class="major-id"><?= $j['id_jurusan'] ?></span>
                                    <span class="major-name poppins-medium"><?= $j['nama_jurusan'] ?></span>
                                </div>
                            </td>
                            <td>
                                <p class="major-desc poppins-regular">
                                    <?= !empty($j['deskripsi']) ? $j['deskripsi'] : '<i style="color: #94a3b8;">Tidak ada deskripsi.</i>' ?>
                                </p>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-icon btn-edit tampilModalUbah" data-id="<?= $j['id_jurusan'] ?>"
                                        title="Edit">
                                        <i class="ph ph-pencil-simple"></i>
                                    </button>
                                    <a href="<?= Constant::DIRNAME ?>jurusan/hapus/<?= $j['id_jurusan'] ?>"
                                        class="btn-danger"
                                        style="text-decoration: none;"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus jurusan ini?')"
                                        title="Hapus">
                                        <i class="ph ph-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data['jurusan'])): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="ph ph-folder-open"
                                    style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                                Belum ada data jurusan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM -->
    <div class="modal-overlay" id="modalJurusan">
        <div class="modal-container">
            <button class="modal-close" id="closeModal">
                <i class="ph ph-x"></i>
            </button>
            <div class="modal-header">
                <h2 class="poppins-bold" id="formModalLabel">Tambah Jurusan</h2>
                <p class="poppins-regular">Lengkapi detail informasi jurusan di bawah ini.</p>
            </div>

            <form action="<?= Constant::DIRNAME ?>jurusan/tambah" method="POST">
                <div class="form-grid">
                    <div class="form-input">
                        <label for="id_jurusan">Singkatan Jurusan <span style="color: red;">*</span></label>
                        <input type="text" name="id_jurusan" id="id_jurusan" class="poppins-regular"
                            placeholder="MISAL: RPL" required>
                    </div>
                    <div class="form-input">
                        <label for="nama_jurusan">Nama Lengkap Jurusan <span style="color: red;">*</span></label>
                        <input type="text" name="nama_jurusan" id="nama_jurusan" class="poppins-regular"
                            placeholder="Misal: Rekayasa Perangkat Lunak" required>
                    </div>
                    <div class="form-input full-width">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi_field" rows="4" class="poppins-regular"
                            placeholder="Deskripsi singkat mengenai jurusan..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel poppins-medium" id="btnBatal">Batal</button>
                    <button type="submit" class="btn-submit poppins-medium">Simpan Jurusan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalJurusan');
        const btnTambah = document.getElementById('btnTambahJurusan');
        const btnClose = document.getElementById('closeModal');
        const btnBatal = document.getElementById('btnBatal');
        const form = modal.querySelector('form');
        const modalLabel = document.getElementById('formModalLabel');
        const submitBtn = modal.querySelector('.btn-submit');
        const idInput = document.getElementById('id_jurusan');

        const openModal = () => {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        const closeModal = () => {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        };

        btnTambah.addEventListener('click', () => {
            modalLabel.innerHTML = 'Tambah Jurusan';
            submitBtn.innerHTML = 'Simpan Jurusan';
            form.setAttribute('action', '<?= Constant::DIRNAME ?>jurusan/tambah');
            idInput.readOnly = false;
            form.reset();
            openModal();
        });

        btnClose.addEventListener('click', closeModal);
        btnBatal.addEventListener('click', closeModal);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        const editBtns = document.querySelectorAll('.tampilModalUbah');
        editBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                modalLabel.innerHTML = 'Ubah Jurusan';
                submitBtn.innerHTML = 'Update Jurusan';
                form.setAttribute('action', '<?= Constant::DIRNAME ?>jurusan/edit');
                idInput.readOnly = true;

                fetch("<?= Constant::DIRNAME ?>jurusan/getubah", {
                    method: 'POST',
                    body: JSON.stringify({ id_jurusan: id })
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('id_jurusan').value = data.id_jurusan;
                        document.getElementById('nama_jurusan').value = data.nama_jurusan;
                        document.getElementById('deskripsi_field').value = data.deskripsi;
                        openModal();
                    });
            });
        });
    });
</script>