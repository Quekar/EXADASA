<?php

class Hasilujian_model
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getStatistikSiswa($username)
    {
        try {
            $db = $this->db;
            $db->query("SELECT 
                                AVG(CASE WHEN ns.publik = 1 THEN ns.nilai END) as rata_nilai, 
                                COUNT(CASE WHEN ns.publik = 1 THEN 1 END) as ujian_selesai,
                                COUNT(CASE WHEN ns.publik = 0 THEN 1 END) as menunggu_hasil
                              FROM nilai_siswa ns
                              JOIN siswa s ON ns.nisn = s.nisn
                              WHERE s.nisn = :username");
            $db->bind('username', $username);
            return $db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getRiwayatUjianSiswa($username)
    {
        try {
            $db = $this->db;
            $db->query("SELECT 
        ns.*, 
        uj.nama_ujian, 
        uj.id_kelas,
        k.nama_kategori AS nama_mapel,
        ujs.id_ujian_siswa
    FROM nilai_siswa ns
    JOIN ujian uj ON ns.id_ujian = uj.id_ujian
    JOIN ujian_siswa ujs ON ns.id_ujian_siswa = ujs.id_ujian_siswa
    JOIN siswa s ON ns.nisn = s.nisn
    LEFT JOIN (
        SELECT 
            us.id_ujian,
            MIN(bs.id_kategori) AS id_kategori
        FROM ujian_soal us
        JOIN bank_soal bs ON bs.id_bank_soal = us.id_bank_soal
        GROUP BY us.id_ujian
    ) mapel ON mapel.id_ujian = uj.id_ujian
    LEFT JOIN kategori_soal k ON k.id_kategori = mapel.id_kategori
    WHERE ns.nisn = :username 
    AND ns.publik = 1
    ORDER BY ns.created_at DESC");
            $db->bind('username', $username);
            return $db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPeringkatSiswa($id_ujian, $username)
    {
        try {
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
        } catch (PDOException $e) {
            return [
                'rank' => 0,
                'total_peserta' => 0
            ];
        }
    }
}