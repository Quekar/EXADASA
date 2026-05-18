<?php

class Hasilujian_model {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getStatistikSiswa($username) {
        $db = $this->db;
        $db->query("SELECT 
                            AVG(ns.nilai) as rata_nilai, 
                            COUNT(CASE WHEN ns.publik = 1 THEN 1 END) as ujian_selesai,
                            COUNT(CASE WHEN ns.publik = 0 THEN 1 END) as menunggu_hasil
                          FROM nilai_siswa ns
                          JOIN siswa s ON ns.nisn = s.nisn
                          WHERE s.nisn = :username");
        $db->bind('username', $username);
        return $db->single();
    }

    public function getRiwayatUjianSiswa($username) {
        $db = $this->db;
        $db->query("SELECT 
                            ns.*, 
                            uj.nama_ujian, 
                            uj.id_kelas,
                            k.nama_kategori as nama_mapel
                          FROM nilai_siswa ns
                          JOIN ujian uj ON ns.id_ujian = uj.id_ujian
                          JOIN siswa s ON ns.nisn = s.nisn
                          LEFT JOIN kategori_soal k ON uj.id_kelas = k.id_kategori
                          WHERE ns.nisn = :username
                          ORDER BY ns.created_at DESC");
        $db->bind('username', $username);
        return $db->resultSet();
    }

    public function getPeringkatSiswa($id_ujian, $username) {
        $db = $this->db;
        $db->query("SELECT ns.nisn, ns.nilai, u.username 
                          FROM nilai_siswa ns
                          JOIN siswa s ON ns.nisn = s.nisn
                          JOIN users u ON s.nama_lengkap = u.username
                          WHERE ns.id_ujian = :id_ujian 
                          ORDER BY ns.nilai DESC");
        $db->bind('id_ujian', $id_ujian);
        $all_nilai = $db->resultSet();

        $rank = 1;
        $total_peserta = count($all_nilai);

        foreach ($all_nilai as $n) {
            if ($n['username'] == $username) {
                break;
            }
            $rank++;
        }

        return [
            'rank' => $rank,
            'total_peserta' => $total_peserta
        ];
    }
}