<?php

class Hasilujian extends Controller {
    public function index() {

        $data["stats"] = $this->model('Hasilujian_model')->getStatistikSiswa($_SESSION['user']['username']);
        $riwayat = $this->model('Hasilujian_model')->getRiwayatUjianSiswa($_SESSION['user']['username']);
        foreach ($riwayat as $key => $value) {
            $rankingData = $this->model('Hasilujian_model')->getPeringkatSiswa($value['id_ujian'], $_SESSION['user']['username']);
            $riwayat[$key]['peringkat'] = $rankingData['rank'];
            $riwayat[$key]['total_peserta'] = $rankingData['total_peserta'];
        }
        $data["riwayat"] = $riwayat;

        $data["title"] = "Hasil Ujian";
        $data["css"] = "style.hasil.ujian";

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('hasilujian/index', $data);
        $this->view('templates/footer');
    }
}