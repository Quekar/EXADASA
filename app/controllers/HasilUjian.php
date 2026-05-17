<?php

class HasilUjian extends Controller {
    public function index() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== "siswa") {
            header('location: ' . Constant::DIRNAME . 'login');
            exit;
        }

        $username = $_SESSION['user']['username'];

        $data["title"] = "Hasil Ujian";
        $data["css"] = "style.hasil.ujian";

        $model = $this->model('Hasilujian_model');
        $data["stats"] = $model->getStatistikSiswa($username);

        $riwayat = $model->getRiwayatUjianSiswa($username);

        foreach ($riwayat as $key => $value) {
            $rankingData = $model->getPeringkatSiswa($value['id_ujian'], $username);
            $riwayat[$key]['peringkat'] = $rankingData['rank'];
            $riwayat[$key]['total_peserta'] = $rankingData['total_peserta'];
        }

        $data["riwayat"] = $riwayat;

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('hasilujian/index', $data);
        $this->view('templates/footer');
    }
}