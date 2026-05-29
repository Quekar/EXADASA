<?php

class Pengaturan extends Controller {
    public function index() {

        $data["konfigurasi"] = $this->model('Pengaturan_model')->getKonfigurasi();

        $data["title"] = "Pengaturan";
        $data["css"] = "style.pengaturan";

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('pengaturan/index', $data);
        $this->view('templates/footer');
    }

    public function ubah() {
        if($_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }
        
        if($this->model("Pengaturan_model")->ubahKonfigurasi($_POST, $_FILES) > 0) {
            Flasher::setFlash("Konfigurasi sistem berhasil diubah", "success");
        } else {
            Flasher::setFlash("Konfigurasi sistem gagal diubah", "error");
        }
        header('Location: ' . Constant::DIRNAME . 'pengaturan');
        exit;
    }
}