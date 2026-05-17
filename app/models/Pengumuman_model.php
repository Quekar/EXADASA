<?php
class Pengumuman_model {
    private $db;
    public function __construct() {
        $this->db = new Database;
    }

    public function getAll() {
        $this->db->query('SELECT * FROM pengumuman ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    public function tambahDataPengumuman($data) {
        $query = "INSERT INTO pengumuman (id_user, title, deskripsi, created_at) 
                  VALUES (:id_user, :title, :deskripsi, NOW())";

        $id_user = $_SESSION['user']['id_user'] ?? $_SESSION['user']['id'] ?? 1;
        
        $this->db->query($query);
        $this->db->bind('id_user', $id_user);
        $this->db->bind('title', $data['title']);
        $this->db->bind('deskripsi', $data['deskripsi']);
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusDataPengumuman($id) {
    $query = "DELETE FROM pengumuman WHERE id_pengumuman = :id";
    $this->db->query($query);
    $this->db->bind('id', $id);
    
    $this->db->execute();
    return $this->db->rowCount();
    }

    public function getPengumumanById($id) {
        $this->db->query('SELECT * FROM pengumuman WHERE id_pengumuman = :id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function ubahDataPengumuman($data) {
        $query = "UPDATE pengumuman SET 
                    title = :title, 
                    deskripsi = :deskripsi 
                WHERE id_pengumuman = :id_pengumuman";
                
        $this->db->query($query);
        $this->db->bind('title', $data['title']);
        $this->db->bind('deskripsi', $data['deskripsi']);
        $this->db->bind('id_pengumuman', $data['id_pengumuman']);
        
        $this->db->execute();
        return $this->db->rowCount();
    }
}