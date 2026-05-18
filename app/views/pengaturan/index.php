<div class="container pengaturan">
    <header class="pengaturan-header">
        <div class="pengaturan-title">
            <h1 class="poppins-semibold">Pengaturan</h1>
            <p class="poppins-regular">Branding, identitas sekolah, and konfigurasi global.</p>
        </div>
    </header>

    <form action="<?= Constant::DIRNAME ?>pengaturan/ubah" method="POST" enctype="multipart/form-data">
        <section class="container-card">
            <section class="card-sistem">
                <div class="card-header">
                    <i class="ph ph-building-office"></i>
                    <h4>Identitas Sistem</h4>
                </div>
                <div class="form-group">
                    <div class="form-input">
                        <label for="name-sistem" class="poppins-medium">Nama Sistem</label>
                        <input type="text" id="name-sistem" name="nama_sistem" class="poppins-regular" 
                            value="<?= $data['konfigurasi']['nama_sistem'] ?? 'EXADASA'; ?>" required>
                    </div>
                    <div class="form-input">
                        <label class="poppins-medium">Nama Sekolah</label>
                        <input type="text" id="name-scholl" name="nama_sekolah" class="poppins-regular" 
                            value="<?= $data['konfigurasi']['nama_sekolah'] ?? 'SMANDASA'; ?>" required>
                    </div>
                    <div class="form-input">
                        <label class="poppins-medium">Tahun Ajaran</label>
                        <input type="text" id="tahunAjaran" name="tahun_ajaran" class="poppins-regular"
                            value="<?= $data['konfigurasi']['tahun_ajaran'] ?? ''; ?>" required>
                    </div>
                    <div class="form-input">
                        <label class="poppins-medium">Footer</label>
                        <textarea id="footer" name="copyright" class="poppins-regular"><?= $data['konfigurasi']['copyright'] ?? 'Copyright © 2023 SMANDASA. All rights reserved.'; ?></textarea>
                    </div>
                </div>
            </section>
            <section class="card-brand">
                <div class="card-header">
                    <i class="ph ph-palette"></i>
                    <h4>Identitas Sistem</h4>
                </div>
                <div class="form-group">
                    <div class="form-input">
                        <label for="name-sistem" class="poppins-medium">Logo Aplikasi</label>
                        <div class="logo-sistem" style="<?= empty($data['konfigurasi']['logo']) ? "background: var(--color-gradient-primary);" : "" ?>">
                            <input type="hidden" name="logo_old" value="<?= $data['konfigurasi']['logo'] ?>">
                            <img id="preview-logo" src="<?= Constant::DIRNAME."asset/img/".$data['konfigurasi']['logo'] ?>" alt="Pratinjau Logo" style="display: <?= isset($data['konfigurasi']['logo']) ? 'block' : 'none' ?>; width: 100%; object-fit: contain; aspect-ratio: 1 / 1;">                          
                            <i class="ph ph-building-office" id="icon-placeholder-logo" style="all: unset; display: <?= isset($data['konfigurasi']['logo']) ? 'none' : 'block' ?>"></i>
                        </div>
                        <div class="btn-upload">
                            <i class="ph ph-upload" style="all: unset;"></i>
                            <span>Upload Logo</span>
                            <input type="file" id="upload-logo" style="position: absolute; inset: 0; opacity: 0;" name="logo_new" class="poppins-regular">
                        </div>
                    </div>
                    <div class="form-input">
                        <label for="name-sistem" class="poppins-medium">Icon Aplikasi</label>
                        <div class="logo-sistem" style="<?= empty($data['konfigurasi']['icon']) ? "background: var(--color-gradient-primary);" : "" ?>">
                            <input type="hidden" name="icon_old" value="<?= $data['konfigurasi']['icon'] ?>">
                            <img id="preview-icon" src="<?= Constant::DIRNAME."asset/img/".$data['konfigurasi']['icon'] ?>" alt="Pratinjau Icon" style="display: <?= isset($data['konfigurasi']['icon']) ? 'block' : 'none' ?>; width: 100%; object-fit: contain; aspect-ratio: 1 / 1;">
                            <i class="ph ph-building-office" id="icon-placeholder-icon" style="all: unset; display: <?= isset($data['konfigurasi']['icon']) ? 'none' : 'block' ?>"></i>
                        </div>
                        <div class="btn-upload" >
                            <i class="ph ph-upload" style="all: unset;"></i>
                            <span>Upload Icon</span>
                            <input type="file" id="upload-icon" style="position: absolute; inset: 0; opacity: 0;" name="icon_new" class="poppins-regular">
                        </div>
                    </div>
                </div>
            </section>
        </section>

        <section class="card-sistem" style="margin-top: 20px;">
            <h1 class="poppins-semibold" style="margin-bottom: 18px;">Mode Sistem</h1>
            <div class="card-group">
                <div class="box-card">
                    <div class="info-card">
                        <h3>Mode Maintenance</h3>
                        <p>Tutup sementara akses untuk siswa & petugas.</p>
                    </div>
                    <input type="checkbox" name="mode_maintenance" id="mode-maintenance" <?= ($data['konfigurasi']['maintenance'] ?? false) ? 'checked' : ''; ?>>
                </div>
            </div>
            <div class="btn-group">
                <button type="button" class="btn-secondary" onclick="window.location.reload();">Batal</button>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </section>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadLogo = document.getElementById('upload-logo');
        const uploadIcon = document.getElementById('upload-icon');

        const previewImage = (inputElement, imgPreviewId, placeholderId) => {
            const file = inputElement.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgPreview = document.getElementById(imgPreviewId);
                    const placeholder = document.getElementById(placeholderId);
                    
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                    if(placeholder) placeholder.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        };

        if(uploadLogo) {
            uploadLogo.addEventListener('change', function() {
                previewImage(this, 'preview-logo', 'icon-placeholder-logo');
            });
        }

        if(uploadIcon) {
            uploadIcon.addEventListener('change', function() {
                previewImage(this, 'preview-icon', 'icon-placeholder-icon');
            });
        }

        const boxColors = document.querySelectorAll('.color-sistem .box-color');
        boxColors.forEach(box => {
            box.addEventListener('click', function () {
                boxColors.forEach(b => b.classList.remove('active-color'));
                this.classList.add('active-color');
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
            });
        });
    });
</script>