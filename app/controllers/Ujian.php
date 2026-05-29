<?php

class Ujian extends Controller {
    public function index() {
        $data["halaman"] = "index";
        $data['ujian'] = $this->model('Ujian_model')->getAllUjian();

        $data["title"] = "Ujian";
        $data["css"] = "style.ujian"; 
        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('ujian/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        if($_SESSION['user']['role'] === "siswa") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        $data["title"] = "Buat Ujian Baru";
        $data["css"] = "style.tambah.ujian";
        $data["halaman"] = "tambah";     

        $data['kelas'] = $this->model('Ujian_model')->getAllKelas();
        $data['bank_soal'] = $this->model('Ujian_model')->getAllBankSoal();
        $data['kategori'] = $this->model('Ujian_model')->getAllKategori();

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('ujian/index', $data); 
        $this->view('templates/footer');
    }

    public function edit($id = null) {
        if($_SESSION['user']['role'] === "siswa") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        if(!$id) {
            Flasher::setFlash("ID ujian tidak valid", "error");
            header('location: ' . Constant::DIRNAME . 'ujian');
            exit;
        }

        $data["halaman"] = "edit";         
        $data['kelas'] = $this->model('Ujian_model')->getAllKelas();
        $data['ujian'] = $this->model('Ujian_model')->getUjianById($id);
        $data['soal'] = $this->model('Ujian_model')->getSoalByUjianId($id);
        $data['bank_soal'] = $this->model('Ujian_model')->getAllBankSoal();
        $data['kategori'] = $this->model('Ujian_model')->getAllKategori();

        
        $data["title"] = "Edit Ujian";
        $data["css"] = "style.tambah.ujian";

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('ujian/index', $data); 
        $this->view('templates/footer');
    }

    public function simpan() {
        if($_SESSION['user']['role'] === "siswa") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        $_POST['id_user'] = $_SESSION['user']['id'] ?? $_SESSION['user']['username'];
        $pengguna = $_SESSION['user']['username'];

        if (isset($_POST['id_ujian'])) {
            if ($this->model('Ujian_model')->updateUjian($_POST, $_FILES)) {
                $this->model('Dashboard_model')->insertLog($pengguna, 'Mengubah ujian: ' . ($_POST['nama_ujian'] ?? $_POST['id_ujian']));
                Flasher::setFlash("Ujian Berhasil Diupdate", "success");
                header('location: ' . Constant::DIRNAME . 'ujian');
                exit;
            } else {
                Flasher::setFlash("Ujian Gagal Diupdate", "error");
                header('location: ' . Constant::DIRNAME . 'ujian');
                exit;
            }
        } else {
            if ($this->model('Ujian_model')->tambahUjian($_POST, $_FILES)) {
                $this->model('Dashboard_model')->insertLog($pengguna, 'Membuat ujian: ' . ($_POST['nama_ujian'] ?? ''));
                Flasher::setFlash("Ujian Berhasil Ditambahkan", "success");
                header('location: ' . Constant::DIRNAME . 'ujian');
                exit;
            } else {
                Flasher::setFlash("Ujian Gagal Ditambahkan", "error");
                header('location: ' . Constant::DIRNAME . 'ujian');
                exit;
            }
        }
    }

    public function hapus($id = null) {
        if($_SESSION['user']['role'] === "siswa") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        if(!$id) {
            Flasher::setFlash("ID ujian tidak valid", "error");
            header('location: ' . Constant::DIRNAME . 'ujian');
            exit;
        }

        if ($this->model('Ujian_model')->hapusUjian($id)) {
            $this->model('Dashboard_model')->insertLog($_SESSION['user']['username'], 'Menghapus ujian (ID: ' . $id . ')');
            Flasher::setFlash("Ujian Berhasil Dihapus", "success");
            header('location: ' . Constant::DIRNAME . 'ujian');
            exit;
        } else {
            Flasher::setFlash("Ujian Gagal Dihapus", "error");
            header('location: ' . Constant::DIRNAME . 'ujian');
            exit;
        }
    }
}