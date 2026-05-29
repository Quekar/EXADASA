<?php

class Jurusan extends Controller
{
    public function index()
    {

        $data["jurusan"] = $this->model('Jurusan_model')->getAllJurusan();
        $data["stats"] = $this->model('Jurusan_model')->getJurusanStats();
        
        $data["title"] = "Jurusan";
        $data["css"] = "style.jurusan";

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('jurusan/index', $data);
        $this->view('templates/footer');
    }

    public function tambah()
    {
        if ($_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        $kode = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        $data = [
            'id_jurusan' => strtoupper(trim($_POST['id_jurusan']).$kode),
            'singkatan_jurusan' => trim($_POST['id_jurusan']),
            'nama_jurusan' => trim($_POST['nama_jurusan']),
            'deskripsi' => trim($_POST['deskripsi'])
        ];

        if ($this->model('Jurusan_model')->tambahJurusan($data) > 0) {
            $pengguna = $_SESSION['user']['username'];
            $this->model('Dashboard_model')->insertLog($pengguna, 'Menambah jurusan: ' . $data['nama_jurusan']);
            Flasher::setFlash('Jurusan berhasil ditambahkan', 'success');
        } else {
            Flasher::setFlash('Jurusan gagal ditambahkan', 'error');
        }

        header('location: ' . Constant::DIRNAME . 'jurusan');
        exit;
    }

    public function edit()
    {
        if ($_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        $data = [
            'id_jurusan' => $_POST['id_jurusan'],
            'nama_jurusan' => $_POST['nama_jurusan'],
            'deskripsi' => $_POST['deskripsi']
        ];

        if ($this->model('Jurusan_model')->updateJurusan($data) > 0) {
            $pengguna = $_SESSION['user']['username'];
            $this->model('Dashboard_model')->insertLog($pengguna, 'Mengubah jurusan: ' . $data['nama_jurusan']);
            Flasher::setFlash('Jurusan berhasil diubah', 'success');
        } else {
            Flasher::setFlash('Jurusan gagal diubah', 'error');
        }
        header('location: ' . Constant::DIRNAME . 'jurusan');
        exit;
    }

    public function hapus($id)
    {
        if ($_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        if ($this->model('Jurusan_model')->hapusJurusan($id) > 0) {
            $pengguna = $_SESSION['user']['username'];
            $this->model('Dashboard_model')->insertLog($pengguna, 'Menghapus jurusan (ID: ' . $id . ')');
            Flasher::setFlash('Jurusan berhasil dihapus', 'success');
        } else {
            Flasher::setFlash('Jurusan gagal dihapus', 'error');
        }
        header('location: ' . Constant::DIRNAME . 'jurusan');
        exit;
    }

    public function getubah()
    {
        $data = json_decode(file_get_contents('php://input'), false);
        echo json_encode($this->model('Jurusan_model')->getJurusanById($data->id_jurusan));
    }

    public function getKelasByJurusan() {
        $data = json_decode(file_get_contents('php://input'), false);
        echo json_encode($this->model('Jurusan_model')->getKelasByJurusan($data->id_jurusan));
    }
}
