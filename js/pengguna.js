document.addEventListener('DOMContentLoaded', function () {
    const container = document.querySelector('.container.pengguna');
    const DIRNAME = container.dataset.dirname;

    const modal = document.getElementById('modalRegistrasi');
    const btnTambah = document.getElementById('btnTambahPengguna');
    const btnClose = document.getElementById('closeModal');
    const btnBatal = document.getElementById('btnBatal');
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    const roleInput = document.getElementById('roleInput');
    const modalSelectJurusan = document.getElementById('modal-select-jurusan');
    const modalSelectKelas = document.getElementById('modal-select-kelas');
    const tableSiswa = document.getElementById('table-siswa');
    const tablePetugas = document.getElementById('table-petugas');
    const tableAdmin = document.getElementById('table-admin');
    const selectRole = document.getElementById('select-role');
    const searchInput = document.getElementById('searchPengguna');

    
    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        const selectedRole = selectRole.value;

        const tables = [
            { el: tableSiswa, role: 'siswa' },
            { el: tablePetugas, role: 'petugas' },
            { el: tableAdmin, role: 'admin' }
        ];

        tables.forEach(({ el, role }) => {
            const allowedByRole = selectedRole === '' || selectedRole === role;
            if (!allowedByRole) {
                el.style.display = 'none';
                return;
            }

            const rows = el.querySelectorAll('tbody tr');
            let hasMatch = false;

            rows.forEach(row => {
                const nameCell = row.querySelector('.info-name');
                if (!nameCell) return;

                const name = nameCell.textContent.toLowerCase();
                if (name.includes(query)) {
                    row.style.display = '';
                    hasMatch = true;
                } else {
                    row.style.display = 'none';
                }
            });

            if (query === '') {
                el.style.display = 'block';
                rows.forEach(row => row.style.display = '');
            } else {
                el.style.display = hasMatch ? 'block' : 'none';
            }
        });
    });

   
    selectRole.addEventListener('change', function () {
        const role = this.value;
        if (role == 'siswa') {
            tableSiswa.style.display = 'block';
            tablePetugas.style.display = 'none';
            tableAdmin.style.display = 'none';
        } else if (role == 'petugas') {
            tableSiswa.style.display = 'none';
            tablePetugas.style.display = 'block';
            tableAdmin.style.display = 'none';
        } else if (role == 'admin') {
            tableSiswa.style.display = 'none';
            tablePetugas.style.display = 'none';
            tableAdmin.style.display = 'block';
        } else {
            tableSiswa.style.display = 'block';
            tablePetugas.style.display = 'block';
            tableAdmin.style.display = 'block';
        }
        searchInput.dispatchEvent(new Event('input'));
    });

    
    modalSelectJurusan.addEventListener('change', async function () {
        const id_jurusan = this.value;
        modalSelectKelas.disabled = false;
        const response = await fetch(DIRNAME + 'jurusan/getKelasByJurusan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id_jurusan })
        });
        const data = await response.json();

        modalSelectKelas.innerHTML = '';
        if (data.length > 0) {
            const option1 = document.createElement('option');
            option1.disabled = true;
            option1.selected = true;
            option1.value = '';
            option1.text = 'Pilih Kelas';
            modalSelectKelas.appendChild(option1);
            data.forEach(kelas => {
                const option = document.createElement('option');
                option.value = kelas.id_kelas;
                option.text = kelas.tingkat;
                modalSelectKelas.appendChild(option);
            });
        } else {
            const option = document.createElement('option');
            option.disabled = true;
            option.selected = true;
            option.value = '';
            option.text = 'Kelas tidak tersedia';
            modalSelectKelas.appendChild(option);
        }
    });

    btnTambah.addEventListener('click', () => {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    const closeModal = () => {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    };

    btnClose.addEventListener('click', closeModal);
    btnBatal.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.getAttribute('data-tab');

            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            tabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === tabId) {
                    content.classList.add('active');
                }
            });

            if (tabId === 'siswa-manual' || tabId === 'siswa-csv') {
                roleInput.value = 'siswa';
            } else if (tabId === 'petugas') {
                roleInput.value = 'petugas';
            } else if (tabId === 'admin') {
                roleInput.value = 'admin';
            }
        });
    });

   
    window.togglePass = function (inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('ph-eye', 'ph-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('ph-eye-slash', 'ph-eye');
        }
    };

    window.updateFileName = function (input) {
        const fileName = document.getElementById('fileName');
        if (input.files.length > 0) {
            fileName.textContent = input.files[0].name;
            fileName.style.color = 'var(--color-primary)';
            fileName.style.fontWeight = '600';
        } else {
            fileName.textContent = 'Klik atau seret file CSV ke sini';
            fileName.style.color = 'var(--color-muted-foreground)';
            fileName.style.fontWeight = '400';
        }
    };
});
