<div class="container pengumuman">
    <header class="pengumuman-header">
        <div class="pengumuman-title">
            <h1 class="poppins-semibold">Pengumuman</h1>
            <p class="poppins-regular">Tampilkan informasi penting di dashboard siswa & petugas.</p>
        </div>
        <button class="btn-primary" id="btnTambahPengumuman">
            <i class="ph ph-plus"></i>
            Tambah pengumuman
        </button>
    </header>
    <section class="box-card">
        <?php if(count($data["pengumuman"]) > 0): ?>
        <?php foreach ($data['pengumuman'] as $p) : ?>
            <div class="card">
                <div class="card-header">
                    <i class="ph ph-megaphone icon-title"></i>
                    <div class="btn-group">
                        <button class="btn-edit" data-id="<?= $p['id_pengumuman']; ?>">
                            <i class="ph ph-pencil"></i>
                        </button>
                        <a href="<?= Constant::DIRNAME ?>pengumuman/hapus/<?= $p['id_pengumuman'] ?>" 
                        class="btn-danger" 
                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')"
                        style="display: flex; justify-content: center; align-items: center; text-decoration: none;">
                            <i class="ph ph-trash"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="info-time">
                        <i class="ph ph-calendar-blank"></i> <?= date('Y-m-d', strtotime($p['created_at'])); ?>
                    </p>
                    <h1 class="title">
                        <?= $p['title']; ?>
                    </h1>
                    <p class="deskripsi">
                        <?= $p['deskripsi']; ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
        <?php else: ?>
            <div style="display: flex; justify-content: center; align-items: center; flex-direction: column; grid-column: 1/-1; margin: 80px 0;">
                <i class="ph ph-megaphone" style="font-size: 28px; color: var(--color-muted-foreground);"></i>
                <p style="padding: 20px; color: #64748b;">Belum ada pengumuman.</p>
            </div>
        <?php endif; ?>
    </section>
</div>

<div class="modal-overlay" id="modalPengumuman" style="display: none;">
    <div class="modal-container">
        <button class="modal-close" id="closeModal">
            <i class="ph ph-x"></i>
        </button>
        <div class="modal-header">
            <h2 class="poppins-bold" id="formModalLabel">Tambah Pengumuman</h2>
            <p class="poppins-regular">Lengkapi detail informasi pengumuman di bawah ini.</p>
        </div>

        <form action="<?= Constant::DIRNAME ?>pengumuman/tambah" method="POST">
            <input type="hidden" name="id_pengumuman" id="id_pengumuman">

            <div class="form-grid">
                <div class="form-input full-width">
                    <label for="title">Judul Pengumuman <span style="color: red;">*</span></label>
                    <input type="text" name="title" id="title" class="poppins-regular"
                        placeholder="Misal: UTS Semester Genap..." required>
                </div>
                <div class="form-input full-width">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi_field" rows="4" class="poppins-regular"
                        placeholder="Deskripsi singkat mengenai pengumuman..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel poppins-medium" id="btnBatal">Batal</button>
                <button type="submit" class="btn-submit poppins-medium">Simpan Pengumuman</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalPengumuman');
        const btnTambah = document.getElementById('btnTambahPengumuman');
        const btnClose = document.getElementById('closeModal');
        const btnBatal = document.getElementById('btnBatal');
        const form = modal.querySelector('form');
        
        const modalLabel = document.getElementById('formModalLabel');
        const submitBtn = modal.querySelector('.btn-submit');

        const openModal = () => {
            modal.style.display = 'flex'; 
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        const closeModal = () => {
            modal.style.display = 'none'; 
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        };

        btnTambah.addEventListener('click', () => {
            modalLabel.innerHTML = 'Tambah Pengumuman';
            submitBtn.innerHTML = 'Simpan Pengumuman';
            form.setAttribute('action', '<?= Constant::DIRNAME ?>pengumuman/tambah');
            form.reset();
            openModal();
        });

        btnClose.addEventListener('click', closeModal);
        btnBatal.addEventListener('click', closeModal);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        const editBtns = document.querySelectorAll('.btn-edit');
        editBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                modalLabel.innerHTML = 'Ubah Pengumuman';
                submitBtn.innerHTML = 'Update Pengumuman';
                form.setAttribute('action', '<?= Constant::DIRNAME ?>pengumuman/edit');

                fetch("<?= Constant::DIRNAME ?>pengumuman/getubah", {
                    method: 'POST',
                    body: JSON.stringify({ id_pengumuman: id })
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('id_pengumuman').value = data.id_pengumuman;
                        document.getElementById('title').value = data.title;
                        document.getElementById('deskripsi_field').value = data.deskripsi;
                        openModal();
                    });
            });
        });
    });
</script>