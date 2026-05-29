<?php

class Dashboard_model
{
    private object $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function countPetugas()
    {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'petugas'");
            return $this->db->single()['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countKelas()
    {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM kelas");
            return $this->db->single()['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countSiswa()
    {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'siswa'");
            return $this->db->single()['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countUjian()
    {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM ujian");
            return $this->db->single()['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countUjianHariIni()
    {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM ujian WHERE DATE(jadwal_mulai) = CURDATE()");
            return $this->db->single()['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countPengumuman()
    {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM pengumuman");
            return $this->db->single()['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getRecentLog(int $limit = 10): array
    {
        try {
            $this->db->query(
                "SELECT pengguna, deskripsi, created_at 
                 FROM log_aktivitas 
                 ORDER BY created_at DESC 
                 LIMIT $limit"
            );
            return $this->db->resultSet() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllLog(): array
    {
        try {
            $this->db->query(
                "SELECT id_log_aktivitas, pengguna, deskripsi, created_at 
                 FROM log_aktivitas 
                 ORDER BY created_at DESC"
            );
            return $this->db->resultSet() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function insertLog(string $pengguna, string $deskripsi): void
    {
        try {
            $this->db->query(
                "INSERT INTO log_aktivitas (pengguna, deskripsi) VALUES (:pengguna, :deskripsi)"
            );
            $this->db->bind('pengguna', $pengguna);
            $this->db->bind('deskripsi', $deskripsi);
            $this->db->execute();
        } catch (PDOException $e) {
        }
    }

    public function getKoreksiMenunggu(int $limit = 5): array
    {
        try {
            $this->db->query(
                "SELECT u.nama_ujian, COUNT(us.id_ujian_siswa) AS jml_menunggu
             FROM ujian_siswa us
             JOIN ujian u ON u.id_ujian = us.id_ujian
             WHERE us.status = 'selesai'
               AND u.penilaian = 'manual'
               AND NOT EXISTS (
                   SELECT 1 FROM nilai_siswa ns
                   WHERE ns.id_ujian_siswa = us.id_ujian_siswa
               )
             GROUP BY us.id_ujian, u.nama_ujian
             ORDER BY MAX(us.waktu_selesai) DESC
             LIMIT :limit"
            );
            $this->db->bind('limit', (string)$limit);
            return $this->db->resultSet() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }
}