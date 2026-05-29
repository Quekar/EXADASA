<?php

class Dashboard extends Controller
{
    public function index()
    {
        $data["title"] = "Dashboard";
        $data["css"] = "style.dashboard";

        $data['pengumuman'] = $this->model('Pengumuman_model')->getAll();

        if ($_SESSION['user']['role'] == 'siswa') {
            $nisn = $_SESSION['user']['username'];

            $data['user_siswa'] = $this->model('Siswa_model')->getByNISN($nisn);

            $dataSiswa = $this->model('UjianSiswa_model')->getDataSiswa($nisn);
            $id_kelas = $dataSiswa['id_kelas'] ?? null;

            $data['list_ujian'] = $this->model('Ujian_model')->getUjianHariIni($id_kelas);
            $data['ujian_mendatang'] = $this->model('Ujian_model')->getUjianTerdekat($id_kelas);
            $data['statusMap'] = $this->model('UjianSiswa_model')->getStatusPengerjaanSiswa($nisn);

            $data['statistik'] = [
                'ujian_selesai' => $this->model('Ujian_model')->countSelesai($nisn),
                'ujian_hari_ini' => count($data['list_ujian']),
                'total_pengumuman' => count($data['pengumuman'])
            ];

        } elseif ($_SESSION['user']['role'] == 'petugas') {
            $dashboardModel = $this->model('Dashboard_model');

            $data['statistik'] = [
                'total_siswa' => $dashboardModel->countSiswa(),
                'total_ujian' => $dashboardModel->countUjian(),
                'ujian_hari_ini' => $dashboardModel->countUjianHariIni(),
                'total_pengumuman' => $dashboardModel->countPengumuman()
            ];

            $data['list_ujian'] = $this->model('Ujian_model')->getUjianHariIni();

            $data['koreksi_menunggu'] = $dashboardModel->getKoreksiMenunggu(5);

        } else {
            $dashboardModel = $this->model('Dashboard_model');

            $data['statistik'] = [
                'total_petugas' => $dashboardModel->countPetugas(),
                'total_kelas' => $dashboardModel->countKelas(),
                'total_siswa' => $dashboardModel->countSiswa(),
                'total_ujian' => $dashboardModel->countUjian(),
                'ujian_hari_ini' => $dashboardModel->countUjianHariIni(),
                'total_pengumuman' => $dashboardModel->countPengumuman()
            ];

            $data['log_aktivitas'] = $dashboardModel->getRecentLog(10);
        }

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('dashboard/index', $data);
        $this->view('templates/footer');
    }

    public function logout()
    {
        if (isset($_SESSION['user'])) {
            $pengguna = $_SESSION['user']['username'] ?? 'Unknown';
            $this->model('Dashboard_model')->insertLog($pengguna, 'Logout dari sistem');
        }

        session_destroy();
        unset($_SESSION['user']);
        setcookie("token", "", time() - 7 * 24 * 60 * 60, "/");
        setcookie("key", "", time() - 7 * 24 * 60 * 60, "/");
        unset($_COOKIE["token"]);
        unset($_COOKIE["key"]);
        header('location: ' . Constant::DIRNAME . 'login');
        exit;
    }

    public function logoutSession()
    {
        if (isset($_SESSION['user'])) {
            $pengguna = $_SESSION['user']['username'] ?? 'Unknown';
            $this->model('Dashboard_model')->insertLog($pengguna, 'Logout dari sistem');
        }
        
        session_destroy();
        unset($_SESSION['user']);
        setcookie("token", "", time() - 7 * 24 * 60 * 60, "/");
        setcookie("key", "", time() - 7 * 24 * 60 * 60, "/");
        unset($_COOKIE["token"]);
        unset($_COOKIE["key"]);
        echo "Session berhasil dihapus";
    }
}