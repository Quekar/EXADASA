<section class="auth">
    <section class="container left">
        <header>
            <a href="<?= Constant::DIRNAME ?>" style="all: inherit; cursor: pointer;">
                <i class="ph ph-graduation-cap bg-icon-primary" style="font-size: 20px; color: #fff;"></i>
                <div class="title-group">
                    <h1 class="poppins-semibold"><?= $data["konfigurasi"]["nama_sistem"] ?? "PLATFORM CBT" ?></h1>
                    <p class="poppins-light"><?= $data["konfigurasi"]["nama_sekolah"] ?? "SMART EXAM PLATFORM" ?></p>
                </div>
            </a>
        </header>
        <main>
            <h1 class="poppins-bold">Belajar & Ujian,
                Sekarang Lebih Cerdas.</h1>
            <p class="poppins-regular">
                Platform CBT modern dengan koreksi otomatis, monitoring real-time, dan analitik lengkap untuk sekolah
                Anda.
            </p>
            <div class="box-service">
                <div class="box">
                    <h2 class="poppins-semibold">10K+</h2>
                    <p>Siswa</p>
                </div>
                <div class="box">
                    <h2 class="poppins-semibold">240+</h2>
                    <p>Sekolah</p>
                </div>
                <div class="box">
                    <h2 class="poppins-semibold">95%</h2>
                    <p>Real-Time</p>
                </div>
            </div>
        </main>
        <footer>
            <p class="poppins-regular" style="font-size: 12px; color: var(--color-muted);"><?= $data["konfigurasi"]["copyright"] ?? "Copyrigth all reversed" ?></p>
        </footer>
    </section>
    <section class="container right">
        <form action="<?= Constant::DIRNAME ?>login/login" method="post">
            <header>
                <h1 class="poppins-semibold">Selamat Datang Kembali</h1>
                <p class="poppins-regular">Masukan username dan password anda</p>
            </header>
            <section class="input-group">
                <div class="form-input">
                    <label for="username" class="poppins-medium">Username</label>
                    <input type="text" name="username" id="username" style="padding-right: 40px;" placeholder="Masukkan username..." class="poppins-regular">
                    <i class="ph ph-user"></i>
                </div>
                <div class="form-input">
                    <label for="password" class="poppins-medium">Password</label>
                    <input type="password" name="password" id="password" style="padding-right: 40px;" placeholder="Masukkan password..." class="poppins-regular">
                    <i class="ph ph-eye"></i>
                </div>
            </section>
            <div style="display: flex; align-items: center; justify-content: space-between; margin: 4px 0;">
                <label for="remember" style="font-size: 13px; display: flex; align-items: center;">
                    <input type="checkbox" name="remember" id="remember" style="margin-right: 8px; width: 20px;">
                    Ingat saya
                </label>
                <a href="" class="poppins-regular"
                    style="font-size: 12px; color: var(--color-primary); text-decoration: none;">Lupa
                    password?</a>
            </div>
            <button class="btn-primary poppins-semibold" style="width: 100%;">
                Masuk
            </button>
            <p class="poppins-regular"
                style="font-size: 12px; text-align: center; margin-top: 16px; color: var(--color-muted-foreground);">
                Akun hanya dapat dibuat oleh admin sekolah.</p>
        </form>
    </section>
</section>

<script>
    const eye = document.querySelector(".ph-eye");
    const password = document.querySelector("#password");
    eye.addEventListener("click", function () {
        if (password.type === "password") {
            eye.className = "ph ph-eye-slash";
            password.type = "text";
        } else {
            password.type = "password";
            eye.className = "ph ph-eye";
        }
    });
</script>