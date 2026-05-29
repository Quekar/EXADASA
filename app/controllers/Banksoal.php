<?php

class Banksoal extends Controller {
    public function index() {   
        $data["title"] = "Bank Soal";
        $data["css"] = "style.banksoal"; 
        $data['soal'] = $this->model('Banksoal_model')->getAllSoal();
        $data['kategori'] = $this->model('Banksoal_model')->getAllKategori();

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('banksoal/index', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        if($_SESSION['user']['role'] !== "petugas" && $_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        $pengguna = $_SESSION['user']['username'];

        if (isset($_POST['id_bank_soal']) && !empty($_POST['id_bank_soal'])) {
            if ($this->model('Banksoal_model')->updateSoal($_POST, $_FILES)) {
                $this->model('Dashboard_model')->insertLog($pengguna, 'Mengubah soal bank soal');
                Flasher::setFlash("Soal Berhasil Diupdate", "success");
            } else {
                Flasher::setFlash("Soal Gagal Diupdate", "error");
            }
        } else {
            if ($this->model('Banksoal_model')->tambahSoal($_POST, $_FILES)) {
                $this->model('Dashboard_model')->insertLog($pengguna, 'Menambah soal baru ke bank soal');
                Flasher::setFlash("Soal Berhasil Ditambahkan", "success");
            } else {
                Flasher::setFlash("Soal Gagal Ditambahkan", "error");
            }
        }
        header('location: ' . Constant::DIRNAME . 'banksoal');
    }

    public function hapus($id) {
        if($_SESSION['user']['role'] !== "petugas" && $_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        if ($this->model('Banksoal_model')->hapusSoal($id)) {
            $pengguna = $_SESSION['user']['username'];
            $this->model('Dashboard_model')->insertLog($pengguna, 'Menghapus soal dari bank soal (ID: ' . $id . ')');
            Flasher::setFlash("Soal Berhasil Dihapus", "success");
        } else {
            Flasher::setFlash("Soal Gagal Dihapus", "error");
        }
        header('location: ' . Constant::DIRNAME . 'banksoal');
    }

    public function tambah_kategori() {
        if($_SESSION['user']['role'] !== "petugas" && $_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        if ($this->model('Banksoal_model')->tambahKategori($_POST['nama_kategori'])) {
            $pengguna = $_SESSION['user']['username'];
            $this->model('Dashboard_model')->insertLog($pengguna, 'Menambah kategori soal: ' . ($_POST['nama_kategori'] ?? ''));
            Flasher::setFlash("Kategori Berhasil Ditambahkan", "success");
        } else {
            Flasher::setFlash("Kategori Gagal Ditambahkan", "error");
        }
        header('location: ' . Constant::DIRNAME . 'banksoal');
    }
}
