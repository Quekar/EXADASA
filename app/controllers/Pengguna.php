<?php

class Pengguna extends Controller
{
    public function index()
    {
        $data["jurusan"] = $this->model('Jurusan_model')->getAllJurusan();
        $data["siswa"] = $this->model('Pengguna_model')->getAllSiswa();
        $data["petugas"] = $this->model('Pengguna_model')->getAllPetugas();
        $data["admin"] = $this->model('Pengguna_model')->getAllAdmin();

        $data["title"] = "Pengguna";
        $data["css"] = "style.pengguna";
        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('pengguna/index', $data);
        $this->view('templates/footer');
    }

    public function tambah()
    {
        if ($_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        $role = $_POST['role'];
        $data = [];

        if ($role == 'siswa') {
            $data = [
                'role' => 'siswa',
                'nisn' => $_POST['nisn'],
                'nama_lengkap' => $_POST['nama_lengkap'],
                'email' => $_POST['email'],
                'jurusan' => $_POST['jurusan'],
                'kelas' => $_POST['kelas'],
                'username' => $_POST['nisn'],
                'password' => $_POST['nisn']
            ];
        } else if ($role == 'petugas') {
            $data = [
                'role' => 'petugas',
                'nip' => $_POST['nip'],
                'nama_lengkap' => $_POST['nama_lengkap_petugas'],
                'email' => $_POST['email_petugas'],
                'username' => $_POST['nip'],
                'password' => $_POST['password_petugas']
            ];
        } else if ($role == 'admin') {
            $data = [
                'role' => 'admin',
                'username' => $_POST['username_admin'],
                'password' => $_POST['password_admin'],
                'nama_lengkap' => $_POST['username_admin']
            ];
        }


        if ($this->model('Register_model')->register($data)) {
            $this->model('Dashboard_model')->insertLog($_SESSION['user']['username'], 'Mendaftarkan pengguna baru: ' . $data['username']);
            Flasher::setFlash('Pengguna berhasil ditambahkan', 'success');
            header('location: ' . Constant::DIRNAME . 'pengguna');
            exit;
        } else {
            Flasher::setFlash('Pengguna gagal ditambahkan', 'error');
            header('location: ' . Constant::DIRNAME . 'pengguna');
            exit;
        }
    }
}
