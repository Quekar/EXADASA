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

        if (count($questions) > 0) {
            $nilaiData = [
                'id_ujian' => $ujian_siswa['id_ujian'],
                'id_ujian_siswa' => $ujian_siswa['id_ujian_siswa'],
                'nisn' => $ujian_siswa['nisn'],
                'total_benar' => $data["benar"],
                'total_salah' => $data["salah"],
                'nilai' => $data["persentase"],
                'publik' => $nilai ? $nilai['publik'] : 0
            ];
            $koreksiModel->simpanAtauUpdateNilai($nilaiData);
        }

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('koreksi/detail', $data);
        $this->view('templates/footer');
    }
}
