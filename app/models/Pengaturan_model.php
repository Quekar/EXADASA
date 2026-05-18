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

    public function ubahKonfigurasi($data, $files) {
        try {
            $nama_sistem = $data['nama_sistem'];
            $nama_sekolah = $data['nama_sekolah'];
            $tahun_ajaran = $data['tahun_ajaran'];
            $copyright = $data['copyright'];
            $mode_maintenance = isset($data['mode_maintenance']) ? 1 : 0;
            $icon = $data["icon_old"];
            $logo = $data["logo_old"];

            if(isset($files['icon_new']) && $files['icon_new']['error'] === UPLOAD_ERR_OK) {
                $icon = $this->uploadFile($files['icon_new']);
            }

            if(isset($files['logo_new']) && $files['logo_new']['error'] === UPLOAD_ERR_OK) {
                $logo = $this->uploadFile($files['logo_new']);
            }

            $konfigurasiOld = $this->getKonfigurasi();
            if($konfigurasiOld) {
                $this->db->query('UPDATE konfigurasi_sistem SET nama_sistem = :nama_sistem, nama_sekolah = :nama_sekolah, tahun_ajaran = :tahun_ajaran, copyright = :copyright, maintenance = :maintenance, logo = :logo, icon = :icon WHERE id_konfigurasi_sistem = 1');
                $this->db->bind('nama_sistem', $nama_sistem);
                $this->db->bind('nama_sekolah', $nama_sekolah);
                $this->db->bind('tahun_ajaran', $tahun_ajaran);
                $this->db->bind('copyright', $copyright);
                $this->db->bind('maintenance', $mode_maintenance);
                $this->db->bind('logo', $logo);
                $this->db->bind('icon', $icon);
            } else {
                $this->db->query('INSERT INTO konfigurasi_sistem (nama_sistem, nama_sekolah, tahun_ajaran, copyright, maintenance, logo, icon) VALUES (:nama_sistem, :nama_sekolah, :tahun_ajaran, :copyright, :maintenance, :logo, :icon)');
                $this->db->bind('nama_sistem', $nama_sistem);
                $this->db->bind('nama_sekolah', $nama_sekolah);
                $this->db->bind('tahun_ajaran', $tahun_ajaran);
                $this->db->bind('copyright', $copyright);
                $this->db->bind('maintenance', $mode_maintenance);
                $this->db->bind('logo', $logo);
                $this->db->bind('icon', $icon);
            }

            $this->db->execute();
            return $this->db->rowCount();
        } catch(PDOException $e) {
            var_dump($e);
            exit;
            return false;
        }
    }

    public function uploadFile($data) {
        try {
            $nama_file = $data["name"];
            $path_file = $data["full_path"];
            $size_file = $data["size"];
            $error_file = $data["error"];
            $temp_file = $data["tmp_name"];

            $extensi_valid = ["png","jpg","jpeg","webp", "PNG", "JPG", "WEBP"];
            $extensi = pathinfo($path_file, PATHINFO_EXTENSION);
            if(in_array($extensi, $extensi_valid) == false) {
                Flasher::setFLash("Extension tidak valid", "error");
                header("Location: " . Constant::DIRNAME . "pengaturan");
                exit;
            }

            //cek size 
            if($size_file > 1000000) {
                Flasher::setFLash("Ukuran file tidak boleh lebih dari 1 MB", "error");
                header("Location: " . Constant::DIRNAME . "pengaturan");
                exit;
            }

            $nama_file_baru = uniqid() . "." . $extensi;
            move_uploaded_file($temp_file, "asset/img/" . $nama_file_baru);
            
            return $nama_file_baru;
        } catch(PDOException $e) {
            return false;
        }
    }

}