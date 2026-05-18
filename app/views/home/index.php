<nav>
    <div class="container navbar">
        <section class="title">
            <a href="<?= Constant::DIRNAME ?>" style="all: inherit; cursor: pointer;">
                <i class="ph ph-graduation-cap bg-icon-primary" style="font-size: 20px; color: #fff;"></i>
                <div class="title-group">
                    <h1 class="poppins-semibold"><?= $data["konfigurasi"]["nama_sistem"] ?? "PLATFORM CBT" ?></h1>
                    <p class="poppins-light"><?= $data["konfigurasi"]["nama_sekolah"] ?? "SMART EXAM PLATFORM" ?></p>
                </div>
            </a>
        </section>
        <section class="menu">
            <a href="<?= Constant::DIRNAME ?>#service">Fitur</a>
            <a href="<?= Constant::DIRNAME ?>#service">Untuk Siapa</a>
            <a href="<?= Constant::DIRNAME ?>login">Mulai</a>
        </section>
        <section class="btn-menu">
            <a href="<?= Constant::DIRNAME ?>login" style="all: inherit; cursor: pointer;">
                <button class="btn-primary poppins-semibold">
                    Masuk
                    <i class="ph ph-arrow-right"></i>
                </button>
            </a>
        </section>
    </div>
</nav>

<section id="hero">
    <div class="container hero">
        <div class="left">
            <p class="poppins-medium" style="font-size: 14px; color: var(--color-accent-foreground)">
                <i class="ph ph-sparkle"></i>
                Monitoring Real-Time
            </p>
            <h1 class="poppins-bold">Ujian Online <span style="color: var(--color-primary)">Modern</span> Untuk Sekolah
                Anda</h1>
            <p class="poppins-regular" style="font-size: 18px; color: var(--color-muted-foreground)">
                Platform CBT lengkap dengan manajemen ujian, koreksi otomatis & manual, monitoring siswa real-time, dan
                ranking kelas — semua dalam satu dashboard yang elegan.
            </p>
            <div class="box-btn">
                <button class="btn-primary poppins-semibold">
                    Mulai Ujian
                    <i class="ph ph-arrow-right"></i>
                </button>
                <button class="btn-secondary poppins-semibold">
                    Lihat Fitur
                    <i class="ph ph-arrow-right"></i>
                </button>
            </div>
            <div class="box-service">
                <div class="box">
                    <i class="ph ph-check"></i>
                    <span>3 Role pengguna</span>
                </div>
                <div class="box">
                    <i class="ph ph-check"></i>
                    <span>Mobile friendly</span>
                </div>
                <div class="box">
                    <i class="ph ph-check"></i>
                    <span>Import CSV soal</span>
                </div>
                <div class="box">
                    <i class="ph ph-check"></i>
                    <span>Export PDF/Excel</span>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="box-img">
                <img src="<?= Constant::DIRNAME ?>asset/img/hero-exam.jpg ?>" alt="hero" width="100%">
            </div>
        </div>
    </div>
</section>

<section id="service">
    <div class="container service">
        <header>
            <h1 class="poppins-semibold" style="font-size: 30px; text-align: center; line-height: 40px;">Semua Yang
                Dibutuhkan Untuk Ujian
                Modern</h1>
            <p class="poppins-regular"
                style="font-size: 14px; text-align: center; color: var(--color-muted-foreground)">Dirancang untuk
                pengalaman terbaik
                bagi admin, petugas, maupun siswa.</p>
        </header>
        <div class="box-service">
            <div class="box">
                <i class="ph ph-shield-check bg-icon-primary" style="font-size: 20px; color: #fff;"></i>
                <h1 class="poppins-semibold" style="font-size: 16px;">Aman & Anti-Cheat</h1>
                <p class="poppins-regular" style="font-size: 14px; color: var(--color-muted-foreground);">Fullscreen
                    mode, anti-refresh, deteksi keluar halaman, dan kode akses per ujian.</p>
            </div>
            <div class="box">
                <i class="ph ph-monitor-arrow-up bg-icon-primary" style="font-size: 20px; color: #fff;"></i>
                <h1 class="poppins-semibold" style="font-size: 16px;">Monitoring Real-Time</h1>
                <p class="poppins-regular" style="font-size: 14px; color: var(--color-muted-foreground);">Pantau
                    progress siswa yang sedang mengerjakan ujian secara live.</p>
            </div>
            <div class="box">
                <i class="ph ph-file-arrow-up bg-icon-primary" style="font-size: 20px; color: #fff;"></i>
                <h1 class="poppins-semibold" style="font-size: 16px;">Koreksi Otomatis & Manual</h1>
                <p class="poppins-regular" style="font-size: 14px; color: var(--color-muted-foreground);">Pilihan ganda
                    dikoreksi otomatis, essay dikoreksi manual oleh petugas.</p>
            </div>
            <div class="box">
                <i class="ph ph-chart-line bg-icon-primary" style="font-size: 20px; color: #fff;"></i>
                <h1 class="poppins-semibold" style="font-size: 16px;">Analitik & Ranking</h1>
                <p class="poppins-regular" style="font-size: 14px; color: var(--color-muted-foreground);">Statistik
                    kelas, ranking siswa, dan export hasil ke Excel/PDF.</p>
            </div>
        </div>
        <div class="box-service">
            <div class="box">
                <p class="poppins-medium" style="font-size: 14px; color: var(--color-primary);">ROLE 1</p>
                <h1 class="poppins-semibold" style="font-size: 16px;">Siswa</h1>
                <p class="poppins-regular" style="font-size: 14px; color: var(--color-muted-foreground);">Kerjakan ujian
                    dengan timer, lihat hasil & ranking, akses dari HP.</p>
            </div>
            <div class="box">
                <p class="poppins-medium" style="font-size: 14px; color: var(--color-primary);">ROLE 2</p>
                <h1 class="poppins-semibold" style="font-size: 16px;">Monitoring Real-Time</h1>
                <p class="poppins-regular" style="font-size: 14px; color: var(--color-muted-foreground);">Buat ujian,
                    kelola bank soal, koreksi essay, monitor siswa.</p>
            </div>
            <div class="box">
                <p class="poppins-medium" style="font-size: 14px; color: var(--color-primary);">ROLE 3</p>
                <h1 class="poppins-semibold" style="font-size: 16px;">Koreksi Otomatis & Manual</h1>
                <p class="poppins-regular" style="font-size: 14px; color: var(--color-muted-foreground);">Kelola
                    pengguna, pengumuman, branding sekolah, dan activity log.</p>
            </div>
        </div>
    </div>
</section>

<footer id="footer">
    <div class="container footer">
        <p class="poppins-medium" style="font-size: 14px; color: var(--color-muted-foreground); margin: auto;"><?= $data["konfigurasi"]["copyright"] ?? "Copyright all reversed" ?> - Platform Ujian Online Sekolah</p>
    </div>
</footer>

<script>
    const nav = document.querySelector("nav .navbar");
    window.addEventListener("scroll", function () {
        if (window.scrollY > 15) {
            nav.classList.add("shadow-navbar");
        } else {
            nav.classList.remove("shadow-navbar");
        }
    });
</script>