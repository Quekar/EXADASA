<?php

class Pengaturan_model {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getKonfigurasi() {
        $this->db->query('SELECT * FROM konfigurasi_sistem LIMIT 1');
        return $this->db->single();
    }

    public function updateKonfigurasi($data) {
        $this->db->query('SELECT id_konfigurasi_sistem FROM konfigurasi_sistem WHERE id_konfigurasi_sistem = 1');
        $cek = $this->db->single();

        if ($cek) {
            $query = "UPDATE konfigurasi_sistem SET 
                        nama_sistem = :nama_sistem,
                        nama_sekolah = :nama_sekolah,
                        tahun_ajaran = :tahun_ajaran,
                        copyright = :copyright
                      WHERE id_konfigurasi_sistem = 1";
        } else {
            $query = "INSERT INTO konfigurasi_sistem (id_konfigurasi_sistem, nama_sistem, nama_sekolah, tahun_ajaran, copyright) 
                      VALUES (1, :nama_sistem, :nama_sekolah, :tahun_ajaran, :copyright)";
        }
        
        $this->db->query($query);
        $this->db->bind('nama_sistem', $data['nama_sistem']);
        $this->db->bind('nama_sekolah', $data['nama_sekolah']);
        $this->db->bind('tahun_ajaran', $data['tahun_ajaran']);
        $this->db->bind('copyright', $data['copyright']);
        
        $this->db->execute();
        return $this->db->rowCount();
    }
}