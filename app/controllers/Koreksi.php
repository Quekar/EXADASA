<?php

class Koreksi extends Controller
{
    public function index()
    {
        if ($_SESSION['user']['role'] === "siswa") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        $data["koreksi_list"] = $this->model('Koreksi_model')->getAllKoreksi();
        $data["kategori_soal"] = $this->model('Koreksi_model')->getKategoriSoal();
        $data["kelas"] = $this->model('Koreksi_model')->getKelas();

        $data["title"] = "Koreksi";
        $data["css"] = "style.koreksi";

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('koreksi/index', $data);
        $this->view('templates/footer');
    }

    public function detail($id = null)
    {
        if ($id === null) {
            Flasher::setFlash('ID ujian tidak valid!', 'error');
            header('location: ' . Constant::DIRNAME . (($_SESSION['user']['role'] == 'siswa') ? 'hasilujian' : 'koreksi'));
            exit;
        }

        $ujian_siswa = $this->model('Koreksi_model')->getDetailUjianSiswa($id);
        if (!$ujian_siswa) {
            Flasher::setFlash('Ujian siswa tidak ditemukan.', 'error');
            header('location: ' . Constant::DIRNAME . (($_SESSION['user']['role'] == 'siswa') ? 'hasilujian' : 'koreksi'));
            exit;
        }

        $data["student_id"] = $id;

        $nilai = $this->model('Koreksi_model')->getNilaiSiswa($id);
        $status_koreksi = 'pending';
        if ($nilai) {
            $status_koreksi = $nilai['publik'] == 1 ? 'published' : 'corrected';
        }

        $masuk = new DateTime($ujian_siswa['waktu_masuk']);
        $selesai = new DateTime($ujian_siswa['waktu_selesai']);
        $diff = $masuk->diff($selesai);
        $durasi = "";
        if ($diff->h > 0)
            $durasi .= $diff->h . " jam ";
        if ($diff->i > 0)
            $durasi .= $diff->i . " menit ";
        if ($diff->s > 0)
            $durasi .= $diff->s . " detik";
        if ($durasi == "")
            $durasi = "0 detik";

        $words = explode(" ", $ujian_siswa['nama_lengkap']);
        $inisial = "";
        foreach ($words as $w) {
            $inisial .= strtoupper(substr($w, 0, 1));
            if (strlen($inisial) >= 2)
                break;
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

        $raw_questions = $this->model('Koreksi_model')->getJawabanDetail($id, $ujian_siswa['id_ujian']);


        $questions = [];
        $no = 1;
        foreach ($raw_questions as $q) {
            $status = ($q['jawaban_siswa'] === $q['kunci']) ? 'benar' : 'salah';
            if ($q["jawaban_benar"])
                $status = ($q["jawaban_benar"] === $q["jawaban_siswa"]) ? 'benar' : 'salah';
            if ($q["benar"])
                $status = 'benar';

            $skor = ($status === 'benar') ? $q['skor_max'] : 0;
            $opsi = [];
            if ($q['ja'])
                $opsi['A'] = $q['ja'];
            if ($q['jb'])
                $opsi['B'] = $q['jb'];
            if ($q['jc'] != null)
                $opsi['C'] = $q['jc'];
            if ($q['jd'])
                $opsi['D'] = $q['jd'];

            $map = ['ja' => 'A', 'jb' => 'B', 'jc' => 'C', 'jd' => 'D'];
            $jawaban_siswa_mapped = $q['jawaban_siswa'] ? ($map[$q['jawaban_siswa']] ?? '') : '';
            $kunci_mapped = $q['kunci'] ? ($map[$q['kunci']] ?? '') : '';

            $questions[] = [
                'no' => $no++,
                'gambar' => $q['gambar'],
                'soal' => $q['pertanyaan'],
                'opsi' => $opsi,
                'jawaban_siswa' => $jawaban_siswa_mapped,
                'kunci' => $kunci_mapped,
                'skor_max' => $q['skor_max'],
                'skor' => $skor,
                'status' => $status,
                'id_bank_soal' => $q['id_bank_soal'],
                'id_ujian_siswa' => $q['id_ujian_siswa'],
            ];
        }


        $data["questions"] = $questions;
        $data["totalSoal"] = count($questions);
        $data["benar"] = count(array_filter($questions, fn($q) => $q['status'] === 'benar'));
        $data["salah"] = count(array_filter($questions, fn($q) => $q['status'] === 'salah'));
        $data["skorTotal"] = array_sum(array_map(fn($q) => $q['skor'] ?? 0, $questions));
        $data["skorMax"] = array_sum(array_map(fn($q) => $q['skor_max'], $questions));
        $data["persentase"] = $data["skorMax"] > 0 ? round(($data["skorTotal"] / $data["skorMax"]) * 100) : 0;

        $data["title"] = "Koreksi Detail";
        $data["css"] = "style.koreksi.detail";

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('koreksi/detail', $data);
        $this->view('templates/footer');
    }

    public function simpanNilaiKoreksi()
    {
        header('Content-Type: application/json');

        if ($_SESSION['user']['role'] !== "petugas") {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['id_ujian_siswa'], $input['id_ujian'], $input['nisn'], $input['total_benar'], $input['total_salah'], $input['nilai'])) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }

        $nilaiData = [
            'id_ujian' => $input['id_ujian'],
            'id_ujian_siswa' => $input['id_ujian_siswa'],
            'nisn' => $input['nisn'],
            'total_benar' => intval($input['total_benar']),
            'total_salah' => intval($input['total_salah']),
            'nilai' => intval($input['nilai']),
            'publik' => 0
        ];

        $result = $this->model('Koreksi_model')->simpanAtauUpdateNilai($nilaiData);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Nilai berhasil disimpan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan nilai']);
        }

        exit;
    }

    public function publish($id = null)
    {
        if ($_SESSION['user']['role'] === "siswa") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        if ($id === null) {
            Flasher::setFlash("ID ujian tidak valid", "error");
            header('location: ' . Constant::DIRNAME . 'koreksi');
            exit;
        }

        if ($this->model('Koreksi_model')->setPublishStatus($id, 1)) {
            $this->model('Dashboard_model')->insertLog($_SESSION['user']['username'], 'Mempublikasikan hasil ujian siswa (ID: ' . $id . ')');
            Flasher::setFlash("Hasil Ujian Berhasil Dipublish", "success");
        } else {
            Flasher::setFlash("Hasil Ujian Gagal Dipublish", "error");
        }

        header('location: ' . Constant::DIRNAME . 'koreksi');
        exit;
    }

    public function unpublish($id = null)
    {
        if ($_SESSION['user']['role'] === "siswa") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        if ($id === null) {
            header('location: ' . Constant::DIRNAME . 'koreksi');
            exit;
        }

        if ($this->model('Koreksi_model')->setPublishStatus($id, 0)) {
            $this->model('Dashboard_model')->insertLog($_SESSION['user']['username'], 'Menyembunyikan hasil ujian siswa (ID: ' . $id . ')');
            Flasher::setFlash("Hasil Ujian Berhasil Disembunyikan", "success");
        } else {
            Flasher::setFlash("Hasil Ujian Gagal Disembunyikan", "error");
        }
        header('location: ' . Constant::DIRNAME . 'koreksi');
        exit;
    }

    public function koreksiUjian()
    {
        if ($_SESSION['user']['role'] === "siswa") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($this->model('Koreksi_model')->ubahUjianSiswa($data));
    }

    public function getKoreksiByMapel()
    {
        if ($_SESSION['user']['role'] === "siswa") {
            echo json_encode(["success" => false, "message" => "Unauthorized"]);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        echo json_encode($this->model('Koreksi_model')->getKoreksiByMapel($data["id_kategori"], $data["id_kelas"], $data["nama_siswa"]));
    }
}
