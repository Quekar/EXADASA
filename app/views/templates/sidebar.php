<section class="wrapper">
    <aside id="sidebar">
        <header>
            <a href="#hero" style="all: inherit; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                <?php if (isset($data["konfigurasi"]["logo"]) && $data['konfigurasi']['logo']): ?>
                    <img src="<?= Constant::DIRNAME . 'asset/img/' . $data["konfigurasi"]["logo"] ?>" alt="Logo" style="width: 40px; height: 40px; object-fit: contain;">
                <?php else: ?>
                    <i class="ph ph-graduation-cap bg-icon-primary" style="font-size: 20px; color: #fff;"></i>
                <?php endif; ?>
                <div class="title-group">
                    <h1 class="poppins-semibold"><?= $data["konfigurasi"]["nama_sistem"] ?? "SMART EXAM PLATFORM" ?></h1>
                    <p class="poppins-light"><?= $data["konfigurasi"]["nama_sekolah"] ?? "SMART EXAM PLATFORM" ?></p>
                </div>
            </a>
        </header>
        <section class="box-menu">
            <p class="poppins-medium" style="font-size: 12px; color: var(--color-muted-foreground)">MENU</p>
            <a href="<?= Constant::DIRNAME ?>dashboard"
                class="menu <?= $data["title"] == "Dashboard" ? "active" : "" ?>">
                <i class="ph ph-house" style="font-size: 20px;"></i>
                <p class="poppins-regular" style="font-size: 14px;">Dashboard</p>
            </a>
            <?php if ($_SESSION['user']['role'] == "petugas" || $_SESSION['user']['role'] == "admin"): ?>
                <a href="<?= Constant::DIRNAME ?>ujian" class="menu <?= $data["title"] == "Ujian" ? "active" : "" ?>">
                    <i class="ph ph-clipboard-text" style="font-size: 20px;"></i>
                    <p class="poppins-regular" style="font-size: 14px;">Ujian</p>
                </a>
                <a href="<?= Constant::DIRNAME ?>banksoal" class="menu <?= $data["title"] == "Bank Soal" ? "active" : "" ?>">
                    <i class="ph ph-database" style="font-size: 20px;"></i>
                    <p class="poppins-regular" style="font-size: 14px;">Bank Soal</p>
                </a>
            <?php endif; ?>
            <?php if ($_SESSION['user']['role'] == "admin"): ?>
                <a href="<?= Constant::DIRNAME ?>pengguna" class="menu <?= $data["title"] == "Pengguna" ? "active" : "" ?>">
                    <i class="ph ph-users" style="font-size: 20px;"></i>
                    <p class="poppins-regular" style="font-size: 14px;">Pengguna</p>
                </a>
                <a href="<?= Constant::DIRNAME ?>jurusan" class="menu <?= $data["title"] == "Jurusan" ? "active" : "" ?>">
                    <i class="ph ph-briefcase" style="font-size: 20px;"></i>
                    <p class="poppins-regular" style="font-size: 14px;">Jurusan</p>
                </a>
                <a href="<?= Constant::DIRNAME ?>pengumuman"
                    class="menu <?= $data["title"] == "Pengumuman" ? "active" : "" ?>">
                    <i class="ph ph-megaphone" style="font-size: 20px;"></i>
                    <p class="poppins-regular" style="font-size: 14px;">Pengumuman</p>
                </a>
            <?php endif; ?>
            <?php if ($_SESSION['user']['role'] == "petugas"): ?>
                <a href="<?= Constant::DIRNAME ?>koreksi" class="menu <?= $data["title"] == "Koreksi" ? "active" : "" ?>">
                    <i class="ph ph-file-text" style="font-size: 20px;"></i>
                    <p class="poppins-regular" style="font-size: 14px;">Koreksi</p>
                </a>
                <!-- <a href="<?= Constant::DIRNAME ?>monitoring"
                    class="menu <?= $data["title"] == "Monitoring" ? "active" : "" ?>">
                    <i class="ph ph-eye" style="font-size: 20px;"></i>
                    <p class="poppins-regular" style="font-size: 14px;">Monitoring</p>
                </a> -->
            <?php endif; ?>
            <?php if ($_SESSION['user']['role'] == "siswa"): ?>
                <a href="<?= Constant::DIRNAME ?>ujianSiswa"
                    class="menu <?= $data["title"] == "Ujian Siswa" ? "active" : "" ?>">
                    <i class="ph ph-clipboard-text" style="font-size: 20px;"></i>
                    <p class="poppins-regular" style="font-size: 14px;">Ujian Saya</p>
                </a>
                <a href="<?= Constant::DIRNAME ?>hasilUjian"
                    class="menu <?= $data["title"] == "Hasil Ujian" ? "active" : "" ?>">
                    <i class="ph ph-files" style="font-size: 20px;"></i>
                    <p class="poppins-regular" style="font-size: 14px;">Hasil Ujian</p>
                </a>
            <?php endif; ?>

            <a href="<?= Constant::DIRNAME ?>profile" class="menu <?= $data["title"] == "Profile" ? "active" : "" ?>">
                <i class="ph ph-user" style="font-size: 20px;"></i>
                <p class="poppins-regular" style="font-size: 14px;">Profile</p>
            </a>

            <?php if ($_SESSION['user']['role'] == "admin"): ?>
                <p class="poppins-medium" style="font-size: 12px; color: var(--color-muted-foreground); margin-top: 20px;">
                    SISTEM</p>
                <a href="<?= Constant::DIRNAME ?>pengaturan"
                    class="menu <?= $data["title"] == "Pengaturan" ? "active" : "" ?>">
                    <i class="ph ph-gear" style="font-size: 20px;"></i>
                    <p class="poppins-regular" style="font-size: 14px;">Pengaturan</p>
                </a>
                <a href="<?= Constant::DIRNAME ?>log" class="menu <?= $data["title"] == "Log Aktivitas" ? "active" : "" ?>">
                    <i class="ph ph-megaphone" style="font-size: 20px;"></i>
                    <p class="poppins-regular" style="font-size: 14px;">Log Aktivitas</p>
                </a>
            <?php endif; ?>
        </section>
        <a href="<?= Constant::DIRNAME ?>dashboard/logout" class="btn-logout">
            <i class="ph ph-sign-out" style="font-size: 20px;"></i>
            <p class="poppins-regular" style="font-size: 14px;">Logout</p>
        </a>
    </aside>
    <main>