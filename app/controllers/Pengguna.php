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

        $role = $_POST['role'] ?? '';
        $data = [];

        if ($role == 'siswa') {
            if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
                if ($this->model('Register_model')->importSiswaFromCSV($_FILES['csv_file'])) {
                    $this->model('Dashboard_model')->insertLog($_SESSION['user']['username'], 'Mengimpor siswa dari CSV');
                    Flasher::setFlash('Siswa berhasil diimpor dari CSV', 'success');
                    header('location: ' . Constant::DIRNAME . 'pengguna');
                    exit;
                } else {
                    Flasher::setFlash('Siswa gagal diimpor dari CSV', 'error');
                    header('location: ' . Constant::DIRNAME . 'pengguna');
                    exit;
                }
            }

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

    public function hapus($username) {
        if($_SESSION['user']['role'] !== 'admin') {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        if(empty($username)) {
            Flasher::setFlash('Data tidak valid!', 'error');
            header('location: ' . Constant::DIRNAME . 'pengguna');
            exit;
        }

        if($this->model("Pengguna_model")->hapus($username) > 0) {
            $this->model("Dashboard_model")->insertLog($_SESSION['user']['username'], 'Menghapus pengguna: ' . $username);
            Flasher::setFlash('Pengguna berhasil dihapus', 'success');
            header('location: ' . Constant::DIRNAME . 'pengguna');
            exit;
        } else {
            Flasher::setFlash('Pengguna gagal dihapus', 'error');
            header('location: ' . Constant::DIRNAME . 'pengguna');
            exit;
        }
    }

    public function unduh_template_csv() {
        if ($_SESSION['user']['role'] !== "admin") {
            header('location: ' . Constant::DIRNAME . 'dashboard');
            exit;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=template_import_siswa.csv');
        
        $output = fopen('php://output', 'w');
        
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['NISN', 'Nama Lengkap', 'Email', 'Tingkat Kelas (10/11/12)', 'Singkatan Jurusan (RPL/TKJ/dll)']);
        
        $majors = $this->model('Jurusan_model')->getAllJurusan();
        $sample_major = !empty($majors) ? $majors[0]['singkatan_jurusan'] : 'RPL';
        
        fputcsv($output, ['2417051049', 'M. Rafly Saputra', '966raflisaputra@gmail.com', '12', $sample_major]);
        fputcsv($output, ['2417051011', 'M. Surya Gymnastyar', 'surya@gmai.com', '12', $sample_major]);
        fputcsv($output, ['2417051020', 'Rheal', 'rheal@gmai.com', '12', $sample_major]);
        fputcsv($output, ['2417051030', 'Andhika', 'andika@gmai.com', '12', $sample_major]);

        fclose($output);
        exit;
    }
}
