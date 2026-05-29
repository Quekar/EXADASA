<?php

class Kerjakanujian extends Controller
{
    public function index($id_ujian = null)
    {

        if ($id_ujian === null) {
            Flasher::setFlash('ID ujian tidak valid!', 'error');
            header('location: ' . Constant::DIRNAME . 'ujianSiswa');
            exit;
        }

        $ujian = $this->model('Kerjakanujian_model')->getUjianById($id_ujian);
        if (!$ujian) {
            Flasher::setFlash('Ujian tidak ditemukan.', 'error');
            header('location: ' . Constant::DIRNAME . 'ujiansiswa');
            exit;
        }

        $now = time();
        $mulai = strtotime($ujian['jadwal_mulai']);
        $selesai = strtotime($ujian['jadwal_selesai']);

        if ($now < $mulai || $now > $selesai) {
            Flasher::setFlash('Ujian tidak tersedia saat ini.', 'error');
            header('location: ' . Constant::DIRNAME . 'ujiansiswa');
            exit;
        }

        $sesiUjian = $this->model('Kerjakanujian_model')->getSesiUjian($id_ujian, $_SESSION['user']['username']);
        if (!$sesiUjian) {
            $this->model('Kerjakanujian_model')->buatSesiUjian($id_ujian, $_SESSION['user']['username']);
            $sesiUjian = $this->model('Kerjakanujian_model')->getSesiUjian($id_ujian, $_SESSION['user']['username']);
        }

        if ($sesiUjian['status'] === 'selesai') {
            Flasher::setFlash('Kamu sudah menyelesaikan ujian ini.', 'info');
            header('location: ' . Constant::DIRNAME . 'hasilujian');
            exit;
        }

        $soalList = $this->model('Kerjakanujian_model')->getSoalUjian($id_ujian);
        $jawabanSiswa = $this->model('Kerjakanujian_model')->getJawabanSiswa($sesiUjian['id_ujian_siswa']);
        $waktuMasuk = strtotime($sesiUjian['waktu_masuk']);
        $durasiDetik = $this->waktuKeDetik($ujian['waktu_pengerjaan']);
        $batasWaktu = $waktuMasuk + $durasiDetik;
        $sisaWaktu = max(0, $batasWaktu - $now);

        $data['ujian'] = $ujian;
        $data['soalList'] = $soalList;
        $data['jawabanSiswa'] = $jawabanSiswa;
        $data['sesiUjian'] = $sesiUjian;
        $data['sisaWaktu'] = $sisaWaktu;
        $data['totalSoal'] = count($soalList);

        $data['title'] = 'Kerjakan Ujian';
        $data['css'] = 'style.kerjakanujian';

        $this->view('templates/header', $data);
        $this->view('kerjakanujian/index', $data);
        $this->view('templates/footer');
    }

    public function simpanJawaban()
    {
        header('Content-Type: application/json');

        if ($_SESSION['user']['role'] !== 'siswa') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);

        $id_ujian_siswa = $body['id_ujian_siswa'] ?? null;
        $id_bank_soal = $body['id_bank_soal'] ?? null;
        $answer = $body['answer'] ?? null;

        if (!$id_ujian_siswa || !$id_bank_soal || !$answer) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }

        $valid = ['ja', 'jb', 'jc', 'jd'];
        if (!in_array($answer, $valid)) {
            echo json_encode(['success' => false, 'message' => 'Jawaban tidak valid']);
            exit;
        }

        $result = $this->model('Kerjakanujian_model')->simpanJawaban($id_ujian_siswa, $id_bank_soal, $answer);
        echo json_encode(['success' => (bool) $result]);
        exit;
    }

    public function submit()
    {
        header('Content-Type: application/json');

        if ($_SESSION['user']['role'] !== 'siswa') {
            echo json_encode(['success' => false]);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $id_ujian_siswa = $body['id_ujian_siswa'] ?? null;
        $id_ujian = $body['id_ujian'] ?? null;

        if (!$id_ujian_siswa || !$id_ujian) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }

        $nisn = $_SESSION['user']['username'];
        $sesi = $this->model('Kerjakanujian_model')->getSesiById($id_ujian_siswa);
        if ($sesi && $sesi['status'] === 'selesai') {
            echo json_encode(['success' => true]);
            exit;
        }

        $this->model('Kerjakanujian_model')->selesaikanUjian($id_ujian_siswa);
        $ujian = $this->model('Kerjakanujian_model')->getUjianById($id_ujian);
        if ($ujian && strtolower($ujian['penilaian']) === 'otomatis') {
            $this->model('Kerjakanujian_model')->hitungNilaiOtomatis($id_ujian, $id_ujian_siswa, $nisn);
        }

        $namaUjian = is_array($ujian) ? ($ujian['nama_ujian'] ?? $id_ujian) : $id_ujian;
        $this->model('Dashboard_model')->insertLog($nisn, 'Submit ujian: ' . $namaUjian);

        echo json_encode(['success' => true]);
        exit;
    }

    private function waktuKeDetik(string $waktu): int
    {
        $parts = explode(':', $waktu);
        return (int) ($parts[0]) * 3600 + (int) ($parts[1]) * 60 + (int) ($parts[2] ?? 0);
    }
}