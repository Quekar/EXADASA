<div class="page-content container">
    <div class="page-header">
        <div>
            <h1 class="page-header__title poppins-semibold">Koreksi &amp; Hasil Ujian</h1>
            <p class="page-header__subtitle poppins-regular">
                Periksa jawaban, beri nilai/feedback, dan publish hasil ke siswa.
            </p>
        </div>
        <?php if ($_SESSION['user']['role'] == 'petugas' || $_SESSION['user']['role'] == 'admin'): ?>
            <div class="page-header__actions">
                <button class="btn-primary poppins-medium">
                    <i class="ph ph-file-xls"></i> Export Excel
                </button>
                <button class="btn-primary poppins-medium">
                    <i class="ph ph-printer"></i> Cetak PDF
                </button>
            </div>
        <?php endif; ?>
    </div>


    <div class="filter-card">
        <div class="filter-card__selects">
            <div class="filter-search">
                <i class="ph ph-magnifying-glass"></i>
                <input id="filter-koreksi" type="text" class="poppins-regular" placeholder="Cari siswa..." />
            </div>
            <div class="group-select">
                <div class="select-wrap">
                    <select id="filter-mapel" class="form-select poppins-regular">
                        <option value="" disabled selected>Mata Pelajaran</option>
                        <?php foreach ($data["kategori_soal"] as $kategori): ?>
                            <option value="<?= $kategori["id_kategori"] ?>"><?= $kategori["nama_kategori"] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="ph ph-caret-down select-caret"></i>
                </div>
                <div class="select-wrap">
                    <select id="filter-kelas" class="form-select poppins-regular">
                        <option value="">Kelas</option>
                         <?php foreach ($data["kelas"] as $kelas): ?>
                            <option value="<?= $kelas["id_kelas"] ?>"><?= $kelas["kelas"] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="ph ph-caret-down select-caret"></i>
                </div>
            </div>
        </div>
    </div>


    <div class="table-card">
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="poppins-medium">Nama Siswa</th>
                        <th class="poppins-medium">Kelas</th>
                        <th class="poppins-medium">Skor</th>
                        <th class="poppins-medium">Benar/Salah</th>
                        <th class="poppins-medium">Waktu Submit</th>
                        <th class="poppins-medium">Status</th>
                        <th class="poppins-medium th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-row">

                    <?php foreach ($data['koreksi_list'] as $k): ?>
                        <?php
                        $id = $k['id_ujian_siswa'];
                        $nama = $k['nama_lengkap'];
                        $kelas = $k['id_kelas'];
                        $skor = $k['nilai'];
                        $benar = $k['total_benar'];
                        $salah = $k['total_salah'];
                        $submit = date('Y-m-d H:i', strtotime($k['waktu_selesai']));

                        $status = 'pending';
                        if ($k['id_nilai_siswa']) {
                            $status = $k['publik'] == 1 ? 'published' : 'corrected';
                        }

                        $words = explode(" ", $nama);
                        $inisial = "";
                        foreach ($words as $w) {
                            $inisial .= strtoupper(substr($w, 0, 1));
                            if (strlen($inisial) >= 2)
                                break;
                        }
                        $av = 'av-blue';
                        ?>
                        <tr class="data-table__row">

                            <td>
                                <div class="siswa-cell">
                                    <div class="avatar <?= $av ?> poppins-semibold"><?= $inisial ?></div>
                                    <span class="poppins-medium"><?= $nama ?></span>
                                </div>
                            </td>

                            <td class="poppins-regular td-muted"><?= $kelas ?></td>

                            <td>
                                <?php if ($skor !== null): ?>
                                    <strong class="td-skor poppins-semibold"><?= $skor ?></strong>
                                <?php else: ?>
                                    <span class="td-muted">&mdash;</span>
                                <?php endif; ?>
                            </td>

                            <td class="poppins-regular td-muted">
                                <?= $benar !== null ? "$benar / $salah" : '&mdash;' ?>
                            </td>

                            <td class="poppins-regular td-muted"><?= $submit ?></td>

                            <td>
                                <?php if ($status === 'published'): ?>
                                    <span class="badge badge-published poppins-medium">published</span>
                                <?php elseif ($status === 'corrected'): ?>
                                    <span class="badge badge-corrected poppins-medium">corrected</span>
                                <?php elseif ($status === 'pending'): ?>
                                    <span class="badge badge-pending poppins-medium">pending</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="aksi-cell">
                                    <a href="<?= Constant::DIRNAME ?>koreksi/detail/<?= $id ?>" class="icon-btn"
                                        title="Lihat Detail">
                                        <i class="ph ph-eye"></i>
                                    </a>
                                    <?php if ($status === 'published'): ?>
                                        <a href="<?= Constant::DIRNAME ?>koreksi/unpublish/<?= $id ?>"
                                            class="icon-btn icon-btn--orange" title="Sembunyikan">
                                            <i class="ph ph-eye-slash"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($status === 'corrected' || $status === 'pending'): ?>
                                        <a href="<?= Constant::DIRNAME ?>koreksi/publish/<?= $id ?>"
                                            class="icon-btn poppins-medium" title="Publish">
                                            <i class="ph ph-paper-plane-tilt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>

    </main>
</div>

<script>
    const filterMapel = document.getElementById("filter-mapel");
    const filterKelas = document.getElementById("filter-kelas");
    const filterKoreksi = document.getElementById("filter-koreksi");

    filterMapel.addEventListener('change', async function () {
        const value = this.value;

        const res = await fetch("<?= Constant::DIRNAME ?>koreksi/getKoreksiByMapel", {
            method: "POST",
            body: JSON.stringify({ id_kategori: value, id_kelas: filterKelas.value, nama_siswa: filterKoreksi.value  })
        });

        const data = await res.json();
        if (data) templateKoreksi(data);
    });

    filterKelas.addEventListener('change', async function () {
        const value = this.value;

        const res = await fetch("<?= Constant::DIRNAME ?>koreksi/getKoreksiByMapel", {
            method: "POST",
            body: JSON.stringify({ id_kelas: value, id_kategori: filterMapel.value, nama_siswa: filterKoreksi.value })
        });

        const data = await res.json();
        if (data) templateKoreksi(data);
    })

    filterKoreksi.addEventListener('keyup', async function () {
        const value = this.value;

        const res = await fetch("<?= Constant::DIRNAME ?>koreksi/getKoreksiByMapel", {
            method: "POST",
            body: JSON.stringify({ nama_siswa: value, id_kelas: filterKelas.value, id_kategori: filterMapel.value})
        });

        const data = await res.json();
        console.log(data);
        if (data) templateKoreksi(data);
    })



    function templateKoreksi(data) {
        const tableRow = document.getElementById("table-row");
        let template = ``;
        data.forEach(k => {
            const id = k.id_ujian_siswa;
            const nama = k.nama_lengkap;
            const kelas = k.id_kelas;
            const skor = k.nilai;
            const benar = k.total_benar;
            const salah = k.total_salah;

            const submit = new Date(k.waktu_selesai)
                .toISOString()
                .slice(0, 16)
                .replace("T", " ");

            let status = "pending";

            if (k.id_nilai_siswa) {
                status = k.publik == 1 ? "published" : "corrected";
            }

            const words = nama.split(" ");

            let inisial = "";

            for (const w of words) {
                inisial += w.substring(0, 1).toUpperCase();
                if (inisial.length >= 2) break;
            }

            const av = 'av-blue';
            template += `
            <tr class="data-table__row">
                            <td>
                                <div class="siswa-cell">
                                    <div class="avatar <?= $av ?> poppins-semibold">${inisial}</div>
                                    <span class="poppins-medium">${nama}</span>
                                </div>
                            </td>

                            <td class="poppins-regular td-muted">${kelas}</td>

                            <td>
                                ${skor ? 
                                    `<strong class="td-skor poppins-semibold">${skor}</strong>`
                                    : 
                                    `<span class="td-muted">&mdash;</span>`
                                }
                            </td>

                            <td class="poppins-regular td-muted">
                                ${benar ? `${benar} / ${salah}` : '&mdash;'}
                            </td>

                            <td class="poppins-regular td-muted">${submit}</td>

                            <td>
                                ${(status === 'published') ?
                                    `<span class="badge badge-published poppins-medium">published</span>`
                                    : status === 'corrected' ?
                                    `<span class="badge badge-corrected poppins-medium">corrected</span>`
                                    : `<span class="badge badge-pending poppins-medium">pending</span>`
                                }
                            </td>

                            <td>
                                <div class="aksi-cell">
                                    <a href="<?= Constant::DIRNAME ?>koreksi/detail/${id}" class="icon-btn"
                                        title="Lihat Detail">
                                        <i class="ph ph-eye"></i>
                                    </a>
                                    ${status === 'published' ?
                                        `<a href="<?= Constant::DIRNAME ?>koreksi/unpublish/${id}" class="icon-btn icon-btn--orange" title="Sembunyikan">
                                            <i class="ph ph-eye-slash"></i>
                                        </a>` : ''
                                    }

                                    ${status === 'corrected' || status === 'pending' ?
                                        `<a href="<?= Constant::DIRNAME ?>koreksi/publish/${id}" class="icon-btn poppins-medium" title="Publish">
                                            <i class="ph ph-paper-plane-tilt"></i>
                                        </a>` : ''
                                    }
                                </div>
                            </td>
                        </tr>
            `;
        });

        tableRow.innerHTML = template;
    }


</script>