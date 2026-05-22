<div class="container profile-container">
    <header class="profile-header">
        <h1>Profile Saya</h1>
        <p>Kelola informasi akun dan kata sandimu.</p>
    </header>

    <form action="<?= Constant::DIRNAME ?>profile/update" method="POST" enctype="multipart/form-data"
        class="profile-content">
        <div class="profile-avatar">
            <div class="avatar-border">
                <div class="avatar-circle" style="text-transform: uppercase; <?= (isset($data['user']['foto']) && $data['user']['foto']) ? "" : "background: var(--color-gradient-primary);" ?>">
                    <?php if (isset($data['user']['foto']) && $data['user']['foto']): ?>
                        <img src="<?= Constant::DIRNAME ?>asset/img/<?= $data['user']['foto'] ?>" alt="profile"
                            style="object-fit: contain; width: 100%; aspect-ratio: 1 / 1;">
                    <?php else: ?>
                        <?= $data['user']['nama_lengkap'][0] ?>
                    <?php endif; ?>
                </div>
                <?php if ($_SESSION['user']['role'] != 'admin'): ?>
                    <label for="input-foto" class="upload-icon" style="cursor: pointer;">
                        <i class="ph ph-camera" title="Ganti Foto"></i>
                        <input type="hidden" name="foto_old" value="<?= $data["user"]["foto"] ?>">
                        <input type="file" id="input-foto" name="foto_new" accept="image/*" style="display: none;">
                    </label>
                <?php endif; ?>
            </div>

            <div class="profile-user">
                <h2><?= $data['user']['nama_lengkap'] ?></h2>
                <p><?= $_SESSION['user']['role'] == "siswa" ? $data['user']['kelas'] . " " . $data['user']['jurusan'] . " • Siswa" : ($_SESSION['user']['role'] == "petugas" ? "Petugas" : "Admin") ?>
                </p>
            </div>
            <?php if ($_SESSION['user']['role'] == 'siswa'): ?>
                <div class="profile-user-info">
                    <div class="info-list">
                        <strong><?= $data['stats']['total_ujian'] ?? 0 ?></strong>
                        <span>Ujian</span>
                    </div>
                    <div class="info-list">
                        <strong><?= number_format($data['stats']['rata_rata'] ?? 0, 1) ?></strong>
                        <span>Rata-Rata Nilai</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="profile-form">
            <div class="form-card">
                <div>
                    <div class="form-label">
                        <h3>Informasi Akun</h3>
                        <div class="form-group">
                            <?php if ($_SESSION['user']['role'] != 'admin'): ?>
                                <div class="form-input">
                                    <label class="poppins-medium">Nama Lengkap</label>
                                    <input type="text" id="name" name="nama_lengkap" class="poppins-regular"
                                        value="<?= $data['user']['nama_lengkap'] ?>" required>
                                </div>
                            <?php endif; ?>
                            <div class="form-input">
                                <label class="poppins-medium">Username</label>
                                <input type="text" id="username" name="username" class="poppins-regular"
                                    value="<?= $data['user']['username'] ?>" required readonly>
                            </div>
                            <?php if ($_SESSION['user']['role'] != 'admin'): ?>
                                <div class="form-input">
                                    <label class="poppins-medium">Email</label>
                                    <input type="email" id="email" name="email" class="poppins-regular"
                                        value="<?= $data['user']['email'] ?>" required>
                                </div>
                            <?php endif; ?>
                            <?php if ($_SESSION['user']['role'] == 'siswa'): ?>
                                <div class="form-input">
                                    <label class="poppins-medium">Kelas</label>
                                    <input type="text" id="kelas" name="kelas" class="poppins-regular"
                                        value="<?= $data['user']['kelas'] . " " . $data['user']['jurusan'] ?>" disabled>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="form-divider">

                    <div class="form-password">
                        <h3>Ganti Kata Sandi</h3>
                        <div class="form-group">
                            <div class="form-input">
                                <label class="poppins-medium">Kata Sandi Lama</label>
                                <input type="password" id="current_password" class="poppins-regular"
                                    name="current_password" placeholder="Masukkan kata sandi lama">
                            </div>
                            <div class="form-input">
                                <label class="poppins-medium">Kata Sandi Baru</label>
                                <input type="password" id="new_password" class="poppins-regular" name="new_password"
                                    placeholder="Masukkan kata sandi baru">
                            </div>
                            <div class="form-input">
                                <label class="poppins-medium">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" id="confirm_password" class="poppins-regular"
                                    name="confirm_password" placeholder="Konfirmasi kata sandi baru">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="reset" class="btn-secondary">Reset</button>
                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('input-foto').addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.querySelector('.avatar-circle');
                if (img.tagName === 'IMG') {
                    img.src = e.target.result;
                } else {
                    const newImg = document.createElement('img');
                    newImg.src = e.target.result;
                    newImg.className = 'avatar-circle';
                    newImg.style.objectFit = 'contain';
                    img.parentNode.replaceChild(newImg, img);
                }
            }
            reader.readAsDataURL(this.files[0]);

        }
    });
</script>