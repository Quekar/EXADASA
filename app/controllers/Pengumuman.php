<?php

class Pengumuman extends Controller {
    public function index() {
        if($_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        $data["title"] = "Pengumuman";
        $data["css"] = "style.pengumuman";

        $data['pengumuman'] = $this->model('Pengumuman_model')->getAll();

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('pengumuman/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model('Pengumuman_model')->tambahDataPengumuman($_POST) > 0) {
                Flasher::setFlash('Pengumuman berhasil ditambahkan', 'success');
                header('Location: ' . Constant::DIRNAME . 'pengumuman');
                exit;
            } else {
                Flasher::setFlash('Pengumuman gagal ditambahkan', 'error');
                header('Location: ' . Constant::DIRNAME . 'pengumuman');
                exit;
            }
        }
    }

    public function hapus($id) {
        if ($this->model('Pengumuman_model')->hapusDataPengumuman($id) > 0) {
            Flasher::setFlash('Pengumuman berhasil dihapus', 'success');    
            header('Location: ' . Constant::DIRNAME . 'pengumuman');
            exit;
        } else {
            Flasher::setFlash('Pengumuman gagal dihapus', 'error');
            header('Location: ' . Constant::DIRNAME . 'pengumuman');
            exit;
        }
    }

    public function getubah() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['id_pengumuman'])) {
            echo json_encode($this->model('Pengumuman_model')->getPengumumanById($input['id_pengumuman']));
        }
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model('Pengumuman_model')->ubahDataPengumuman($_POST) > 0) {
                Flasher::setFlash('Pengumuman berhasil diubah', 'success');
                header('Location: ' . Constant::DIRNAME . 'pengumuman');
                exit;
            } else {
                Flasher::setFlash('Pengumuman gagal diubah', 'error');
                header('Location: ' . Constant::DIRNAME . 'pengumuman');
                exit;
            }
        }
    }
}