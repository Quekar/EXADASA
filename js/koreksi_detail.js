document.addEventListener('DOMContentLoaded', function () {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const questionCards = document.querySelectorAll('.question-card');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            tabBtns.forEach(b => b.classList.remove('tab-btn--active'));
            this.classList.add('tab-btn--active');
            const filter = this.dataset.filter;

            questionCards.forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = '';
                    card.style.animation = 'fadeSlideIn 0.3s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });


    const data = window.koreksiData || {};
    const grading = {};
    const koreksiBtns = document.querySelectorAll('.koreksi-btn');
    const btnSimpan = document.getElementById('btnSimpanNilai');
    const warningEl = document.getElementById('warningBelumLengkap');
    const badgeStatus = document.getElementById('badgeStatusKoreksi');

    if (!btnSimpan) return; 

    koreksiBtns.forEach(btn => {
        if (btn.classList.contains('koreksi-btn--active')) {
            const no = btn.dataset.no;
            const value = btn.dataset.value;
            const skor = parseInt(btn.dataset.skor);
            grading[no] = { value, skor };
        }
    });
    updateKoreksiSummary();

    koreksiBtns.forEach(btn => {
        btn.addEventListener('click', async function () {
            const no = this.dataset.no;
            const value = this.dataset.value;
            const skor = parseInt(this.dataset.skor);
            const idBankSoal = this.dataset.idBankSoal;
            const idUjianSiswa = this.dataset.idUjianSiswa;
            const dirname = this.dataset.dirname;

            const res = await fetch(`${dirname}koreksi/koreksiUjian`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_bank_soal: idBankSoal, id_ujian_siswa: idUjianSiswa, koreksi: value, jawaban: null })
            });

            const data = await res.json();
            console.log(data);
            if(!data) {
                showToast('error','Gagal mengubah koreksi.');
                return;
            }

            const parent = this.closest('.koreksi-actions__buttons');
            parent.querySelectorAll('.koreksi-btn').forEach(b => {
                b.classList.remove('koreksi-btn--active');
            });

            this.classList.add('koreksi-btn--active');

            grading[no] = { value, skor };

            const card = this.closest('.question-card');
            card.classList.remove('question-card--graded-benar', 'question-card--graded-salah');
            card.classList.add('question-card--graded-' + value);

            updateKoreksiSummary();
        });
    });

    function updateKoreksiSummary() {
        const gradedCount = Object.keys(grading).length;
        let totalBenar = 0;
        let totalSalah = 0;
        let skorTotal = 0;

        Object.values(grading).forEach(g => {
            if (g.value === 'benar') {
                totalBenar++;
                skorTotal += g.skor;
            } else {
                totalSalah++;
            }
        });

        const belum = data.totalSoal - gradedCount;
        const persen = data.skorMax > 0 ? Math.round((skorTotal / data.skorMax) * 100) : 0;

        const totalBenarEl = document.getElementById('totalBenarKoreksi');
        const totalSalahEl = document.getElementById('totalSalahKoreksi');
        const totalBelumEl = document.getElementById('totalBelumKoreksi');
        const skorKoreksiEl = document.getElementById('skorKoreksi');
        const persenKoreksiEl = document.getElementById('persenKoreksi');

        if (totalBenarEl) totalBenarEl.textContent = totalBenar;
        if (totalSalahEl) totalSalahEl.textContent = totalSalah;
        if (totalBelumEl) totalBelumEl.textContent = belum;
        if (skorKoreksiEl) skorKoreksiEl.textContent = skorTotal;
        if (persenKoreksiEl) persenKoreksiEl.textContent = persen;

        if (belum > 0) {
            if (warningEl) warningEl.style.display = 'flex';
            btnSimpan.disabled = true;
            if (badgeStatus) {
                badgeStatus.innerHTML = '<i class="ph ph-clock"></i> Belum Lengkap';
                badgeStatus.className = 'simpan-nilai__badge simpan-nilai__badge--pending poppins-medium';
            }
        } else {
            if (warningEl) warningEl.style.display = 'none';
            btnSimpan.disabled = false;
            if (badgeStatus) {
                badgeStatus.innerHTML = '<i class="ph ph-check-circle"></i> Siap Disimpan';
                badgeStatus.className = 'simpan-nilai__badge simpan-nilai__badge--ready poppins-medium';
            }
        }
    }

    btnSimpan.addEventListener('click', async function () {
        if (this.disabled) return;

        const gradedCount = Object.keys(grading).length;
        if (gradedCount < data.totalSoal) {
            alert('Masih ada soal yang belum dikoreksi!');
            return;
        }

        let totalBenar = 0;
        let totalSalah = 0;
        let skorTotal = 0;

        Object.values(grading).forEach(g => {
            if (g.value === 'benar') {
                totalBenar++;
                skorTotal += g.skor;
            } else {
                totalSalah++;
            }
        });

        const persen = data.skorMax > 0 ? Math.round((skorTotal / data.skorMax) * 100) : 0;

        this.disabled = true;
        const originalHTML = this.innerHTML;
        this.innerHTML = '<i class="ph ph-spinner"></i> Menyimpan...';
        this.classList.add('btn-simpan-nilai--loading');

        try {
            const response = await fetch(data.baseUrl + 'koreksi/simpanNilaiKoreksi', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_ujian_siswa: data.idUjianSiswa,
                    id_ujian: data.idUjian,
                    nisn: data.nisn,
                    total_benar: totalBenar,
                    total_salah: totalSalah,
                    nilai: persen
                })
            });

            const result = await response.json();

            if (result.success) {
                this.innerHTML = '<i class="ph ph-check-circle"></i> Nilai Tersimpan!';
                this.classList.remove('btn-simpan-nilai--loading');
                this.classList.add('btn-simpan-nilai--success');
                if (badgeStatus) {
                    badgeStatus.innerHTML = '<i class="ph ph-check-circle"></i> Tersimpan';
                    badgeStatus.className = 'simpan-nilai__badge simpan-nilai__badge--saved poppins-medium';
                }

                if (typeof showToast === 'function') {
                    showToast('success', 'Nilai berhasil disimpan!');
                }

                setTimeout(() => {
                    window.location.href = data.baseUrl + 'koreksi';
                }, 1000);
            } else {
                throw new Error(result.message || 'Gagal menyimpan');
            }
        } catch (error) {
            this.innerHTML = '<i class="ph ph-warning"></i> Gagal! Coba Lagi';
            this.classList.remove('btn-simpan-nilai--loading');
            this.classList.add('btn-simpan-nilai--error');

            if (typeof showToast === 'function') {
                showToast('error', error.message || 'Gagal menyimpan nilai');
            }

            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.classList.remove('btn-simpan-nilai--error');
                this.disabled = false;
            }, 3000);
        }
    });
});
