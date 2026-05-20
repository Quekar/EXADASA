<?php

class Koreksi extends Controller
{
    public function index()
    {
        if ($_SESSION['user']['role'] !== "petugas") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        $data["title"] = "Koreksi";
        $data["css"] = "style.koreksi";
        
        $data["koreksi_list"] = $this->model('Koreksi_model')->getAllKoreksi();

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('koreksi/index', $data);
        $this->view('templates/footer');
    }

    public function detail($id = null)
    {
        if ($_SESSION['user']['role'] !== "petugas") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        if ($id === null) {
            header('location: ' . Constant::DIRNAME . 'koreksi');
            exit;
        }

        $koreksiModel = $this->model('Koreksi_model');
        
        $ujian_siswa = $koreksiModel->getDetailUjianSiswa($id);
        if (!$ujian_siswa) {
            header('location: ' . Constant::DIRNAME . 'koreksi');
            exit;
        }

        $data["title"] = "Koreksi Detail";
        $data["css"] = "style.koreksi.detail";
        $data["student_id"] = $id;

        $nilai = $koreksiModel->getNilaiSiswa($id);
        $status_koreksi = 'pending';
        if ($nilai) {
            $status_koreksi = $nilai['publik'] == 1 ? 'published' : 'corrected';
        }

        $masuk = new DateTime($ujian_siswa['waktu_masuk']);
        $selesai = new DateTime($ujian_siswa['waktu_selesai']);
        $diff = $masuk->diff($selesai);
        $durasi = "";
        if ($diff->h > 0) $durasi .= $diff->h . " jam ";
        if ($diff->i > 0) $durasi .= $diff->i . " menit ";
        if ($diff->s > 0) $durasi .= $diff->s . " detik";
        if ($durasi == "") $durasi = "0 detik";

        $words = explode(" ", $ujian_siswa['nama_lengkap']);
        $inisial = "";
        foreach ($words as $w) {
            $inisial .= strtoupper(substr($w, 0, 1));
            if (strlen($inisial) >= 2) break;
        }
        
        $data["student"] = [
            'id_ujian_siswa' => $ujian_siswa['id_ujian_siswa'],
            'nisn' => $ujian_siswa['nisn'],
            'id_ujian' => $ujian_siswa['id_ujian'],
            'nama' => $ujian_siswa['nama_lengkap'],
            'kelas' => $ujian_siswa['id_kelas'],
            'ujian' => $ujian_siswa['nama_ujian'],
            'waktu_submit' => date('Y-m-d H:i', strtotime($ujian_siswa['waktu_selesai'])),
            'durasi' => trim($durasi),
            'status' => $status_koreksi,
            'inisial' => $inisial,
            'av' => 'av-blue',
        ];

        $raw_questions = $koreksiModel->getJawabanDetail($id, $ujian_siswa['id_ujian']);
        
        $questions = [];
        $no = 1;
        foreach ($raw_questions as $q) {
            $status = ($q['jawaban_siswa'] === $q['kunci']) ? 'benar' : 'salah';
            $skor = ($status === 'benar') ? $q['skor_max'] : 0;
            
            $opsi = [];
            if ($q['ja']) $opsi['A'] = $q['ja'];
            if ($q['jb']) $opsi['B'] = $q['jb'];
            if ($q['jc']) $opsi['C'] = $q['jc'];
            if ($q['jd']) $opsi['D'] = $q['jd'];

            $map = ['ja' => 'A', 'jb' => 'B', 'jc' => 'C', 'jd' => 'D'];
            $jawaban_siswa_mapped = $q['jawaban_siswa'] ? ($map[$q['jawaban_siswa']] ?? '') : '';
            $kunci_mapped = $q['kunci'] ? ($map[$q['kunci']] ?? '') : '';
            
            $questions[] = [
                'no' => $no++,
                'soal' => $q['pertanyaan'],
                'opsi' => $opsi,
                'jawaban_siswa' => $jawaban_siswa_mapped,
                'kunci' => $kunci_mapped,
                'skor_max' => $q['skor_max'],
                'skor' => $skor,
                'status' => $status
            ];
        }

        $data["questions"] = $questions;
        $data["totalSoal"] = count($questions);
        $data["benar"] = count(array_filter($questions, fn($q) => $q['status'] === 'benar'));
        $data["salah"] = count(array_filter($questions, fn($q) => $q['status'] === 'salah'));
        $data["skorTotal"] = array_sum(array_map(fn($q) => $q['skor'] ?? 0, $questions));
        $data["skorMax"] = array_sum(array_map(fn($q) => $q['skor_max'], $questions));
        $data["persentase"] = $data["skorMax"] > 0 ? round(($data["skorTotal"] / $data["skorMax"]) * 100) : 0;



        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('koreksi/detail', $data);
        $this->view('templates/footer');
    }

    public function simpanNilaiKoreksi()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        if ($_SESSION['user']['role'] !== "petugas") {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['id_ujian_siswa'], $input['id_ujian'], $input['nisn'], $input['total_benar'], $input['total_salah'], $input['nilai'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }

        $koreksiModel = $this->model('Koreksi_model');

        $nilaiData = [
            'id_ujian' => $input['id_ujian'],
            'id_ujian_siswa' => $input['id_ujian_siswa'],
            'nisn' => $input['nisn'],
            'total_benar' => intval($input['total_benar']),
            'total_salah' => intval($input['total_salah']),
            'nilai' => intval($input['nilai']),
            'publik' => 0
        ];

        $result = $koreksiModel->simpanAtauUpdateNilai($nilaiData);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Nilai berhasil disimpan']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan nilai']);
        }
        exit;
    }

    public function publish($id = null)
    {
        if ($_SESSION['user']['role'] !== "petugas" && $_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        if ($id === null) {
            header('location: ' . Constant::DIRNAME . 'koreksi');
            exit;
        }

        $koreksiModel = $this->model('Koreksi_model');
        if ($koreksiModel->setPublishStatus($id, 1)) {
            $pengguna = $_SESSION['user']['username'];
            $this->model('Dashboard_model')->insertLog($pengguna, 'Mempublikasikan hasil ujian siswa (ID: ' . $id . ')');
            Flasher::setFlash("Hasil Ujian Berhasil Dipublish", "success");
        } else {
            Flasher::setFlash("Hasil Ujian Gagal Dipublish", "error");
        }
        header('location: ' . Constant::DIRNAME . 'koreksi');
        exit;
    }

    public function unpublish($id = null)
    {
        if ($_SESSION['user']['role'] !== "petugas" && $_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        if ($id === null) {
            header('location: ' . Constant::DIRNAME . 'koreksi');
            exit;
        }

        $koreksiModel = $this->model('Koreksi_model');
        if ($koreksiModel->setPublishStatus($id, 0)) {
            $pengguna = $_SESSION['user']['username'];
            $this->model('Dashboard_model')->insertLog($pengguna, 'Menyembunyikan hasil ujian siswa (ID: ' . $id . ')');
            Flasher::setFlash("Hasil Ujian Berhasil Disembunyikan", "success");
        } else {
            Flasher::setFlash("Hasil Ujian Gagal Disembunyikan", "error");
        }
        header('location: ' . Constant::DIRNAME . 'koreksi');
        exit;
    }
}
