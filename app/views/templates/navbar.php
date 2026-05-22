<nav>
    <button id="btn-sidebar" onclick="handleOpenSidebar(event)" style="all: unset; margin-left: 10px;">
        <i class="ph ph-list" style="font-size: 20px; cursor: pointer;"></i>
    </button>
    <!-- <div class="input-navbar form-input">
        <input type="text" name="search" id="search" placeholder="Cari disini..." style="padding-right: 40px;" class="poppins-regular">
        <i class="ph ph-magnifying-glass"></i>
    </div> -->
    <div class="group-navbar">
        <i class="ph ph-bell"></i>
        <div class="profil-navbar">
            <div class="title">
                <h2 class="poppins-semibold"><?= $_SESSION['user']['nama_lengkap'] ?></h2>
                <p class="poppins-light"><?= $_SESSION['user']['role'] ?></p>
            </div>
            <div class="img" style="background-color: <?= isset($_SESSION['user']['foto']) && $_SESSION['user']['foto'] ? "" : "var(--color-primary);" ?> overflow: hidden;">
                <?php if (!empty($_SESSION['user']['foto'])): ?>
                    <img src="<?= Constant::DIRNAME ?>asset/img/<?= $_SESSION['user']['foto'] ?>" style="width: 100%; object-fit: cover; aspect-ratio: 1 / 1;" alt="profil">
                <?php else: ?>
                    <span class="poppins-semibold"
                        style="margin: auto; color: #fff; text-transform: uppercase;"><?= $_SESSION['user']['nama_lengkap'][0] ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>