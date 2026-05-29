<?php

class Pengguna_model
{
    private object $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllSiswa()
    {
        try {
            $this->db->query("SELECT s.foto, s.nisn, s.nama_lengkap, s.email, u.role, k.tingkat as kelas, j.singkatan_jurusan as jurusan FROM users as u JOIN siswa as s ON s.nisn = u.username JOIN data_siswa as ds ON ds.nisn = s.nisn JOIN kelas as k ON k.id_kelas = ds.id_kelas JOIN jurusan as j ON j.id_jurusan = ds.id_jurusan ORDER BY u.created_at ASC");
            $this->db->execute();
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllPetugas()
    {
        try {
            $this->db->query("SELECT p.foto, p.nip, p.nama_lengkap, p.email, u.role FROM users as u JOIN petugas as p ON p.nip = u.username ORDER BY u.created_at ASC");
            $this->db->execute();
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllAdmin()
    {
        try {
            $this->db->query("SELECT u.username, u.role FROM users as u WHERE u.role = 'admin' ORDER BY u.created_at ASC");
            $this->db->execute();
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function hapus(string $username)
    {
        try {
            $this->db->query("DELETE FROM users WHERE username = :username");
            $this->db->bind('username', $username);
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return false;
        }
    }


}
