<?php

class Banksoal_model
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllSoal()
    {
        $this->db->query("SELECT bs.*, ks.nama_kategori 
                          FROM bank_soal bs 
                          LEFT JOIN kategori_soal ks ON bs.id_kategori = ks.id_kategori 
                          ORDER BY bs.created_at DESC");
        return $this->db->resultSet();
    }

    public function getSoalById($id)
    {
        $this->db->query("SELECT * FROM bank_soal WHERE id_bank_soal = :id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function getAllKategori()
    {
        $this->db->query("SELECT * FROM kategori_soal ORDER BY nama_kategori ASC");
        return $this->db->resultSet();
    }

    public function tambahSoal(array $data, array $file)
    {
        try {
            $id = uniqid('bs_', true);
            $answer = isset($data["jawaban_benar"]) ? $data["jawaban_benar"] : null;
            $gambar = null;

            if($file['form_gambar']['error'] == 0) {
                $gambar = $this->uploadFile($file['form_gambar']);
            }

            if ($answer) {
                if($gambar) {
                    $this->db->query("INSERT INTO bank_soal (id_bank_soal, gambar, pertanyaan, id_kategori, ja, jb, jc, jd, answer) 
                                      VALUES (:id, :gambar, :pertanyaan, :id_kategori, :ja, :jb, :jc, :jd, :answer)");
                    $this->db->bind('id', $id);
                    $this->db->bind('gambar', $gambar);
                    $this->db->bind('pertanyaan', $data['pertanyaan']);
                    $this->db->bind('id_kategori', $data['id_kategori']);
                    $this->db->bind('ja', $data['ja']);
                    $this->db->bind('jb', $data['jb']);
                    $this->db->bind('jc', $data['jc']);
                    $this->db->bind('jd', $data['jd']);
                    $this->db->bind('answer', $answer);
                } else {
                    $this->db->query("INSERT INTO bank_soal (id_bank_soal, pertanyaan, id_kategori, ja, jb, jc, jd, answer) 
                                      VALUES (:id, :pertanyaan, :id_kategori, :ja, :jb, :jc, :jd, :answer)");
                    $this->db->bind('id', $id);
                    $this->db->bind('pertanyaan', $data['pertanyaan']);
                    $this->db->bind('id_kategori', $data['id_kategori']);
                    $this->db->bind('ja', $data['ja']);
                    $this->db->bind('jb', $data['jb']);
                    $this->db->bind('jc', $data['jc']);
                    $this->db->bind('jd', $data['jd']);
                    $this->db->bind('answer', $answer);
                }
            } else {
                if($gambar) {
                    $this->db->query("INSERT INTO bank_soal (id_bank_soal, gambar, pertanyaan, id_kategori, ja, jb, jc, jd) 
                                      VALUES (:id, :gambar, :pertanyaan, :id_kategori, :ja, :jb, :jc, :jd)");
                    $this->db->bind('id', $id);
                    $this->db->bind('gambar', $gambar);
                    $this->db->bind('pertanyaan', $data['pertanyaan']);
                    $this->db->bind('id_kategori', $data['id_kategori']);
                    $this->db->bind('ja', $data['ja']);
                    $this->db->bind('jb', $data['jb']);
                    $this->db->bind('jc', $data['jc']);
                    $this->db->bind('jd', $data['jd']);
                } else {
                    $this->db->query("INSERT INTO bank_soal (id_bank_soal, pertanyaan, id_kategori, ja, jb, jc, jd) 
                                      VALUES (:id, :pertanyaan, :id_kategori, :ja, :jb, :jc, :jd)");
                    $this->db->bind('id', $id);
                    $this->db->bind('pertanyaan', $data['pertanyaan']);
                    $this->db->bind('id_kategori', $data['id_kategori']);
                    $this->db->bind('ja', $data['ja']);
                    $this->db->bind('jb', $data['jb']);
                    $this->db->bind('jc', $data['jc']);
                    $this->db->bind('jd', $data['jd']);
                }
            }

            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateSoal(array $data, array $file)
    {
        try {
            $answer = isset($data["jawaban_benar"]) ? $data["jawaban_benar"] : null;
            $gambar = $data['form_gambar_old'] ?? null;

            if($file['form_gambar']['error'] == 0) {
                $gambar = $this->uploadFile($file['form_gambar'], $gambar);
            }

            if($answer) {
                if ($gambar) {
                    $this->db->query("UPDATE bank_soal SET pertanyaan = :pertanyaan, id_kategori = :id_kategori, 
                                      ja = :ja, jb = :jb, jc = :jc, jd = :jd, answer = :answer, gambar = :gambar 
                                      WHERE id_bank_soal = :id");
                    $this->db->bind('gambar', $gambar);
                } else {
                    $this->db->query("UPDATE bank_soal SET pertanyaan = :pertanyaan, id_kategori = :id_kategori, 
                                      ja = :ja, jb = :jb, jc = :jc, jd = :jd, answer = :answer 
                                      WHERE id_bank_soal = :id");
                }
            } else {
                if ($gambar) {
                    $this->db->query("UPDATE bank_soal SET pertanyaan = :pertanyaan, id_kategori = :id_kategori, 
                                      ja = :ja, jb = :jb, jc = :jc, jd = :jd, gambar = :gambar 
                                      WHERE id_bank_soal = :id");
                    $this->db->bind('gambar', $gambar);
                } else {
                    $this->db->query("UPDATE bank_soal SET pertanyaan = :pertanyaan, id_kategori = :id_kategori, 
                                      ja = :ja, jb = :jb, jc = :jc, jd = :jd 
                                      WHERE id_bank_soal = :id");
                }
            }

            $this->db->bind('id', $data['id_bank_soal']);
            $this->db->bind('pertanyaan', $data['pertanyaan']);
            $this->db->bind('id_kategori', $data['id_kategori']);
            $this->db->bind('ja', $data['ja']);
            $this->db->bind('jb', $data['jb']);
            $this->db->bind('jc', $data['jc']);
            $this->db->bind('jd', $data['jd']);
            $this->db->bind('answer', $answer);
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function hapusSoal($id)
    {
        try {
            $this->db->beginTransaction();
            $this->db->query("DELETE FROM ujian_soal WHERE id_bank_soal = :id");
            $this->db->bind('id', $id);
            $this->db->execute();

            $this->db->query("DELETE FROM bank_soal WHERE id_bank_soal = :id");
            $this->db->bind('id', $id);
            $this->db->execute();
            $this->db->commit();

            return $this->db->rowCount();
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Kategori CRUD
    public function tambahKategori($nama)
    {
        $id = uniqid('kat_', true);
        $this->db->query("INSERT INTO kategori_soal (id_kategori, nama_kategori) VALUES (:id, :nama)");
        $this->db->bind('id', $id);
        $this->db->bind('nama', $nama);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function uploadFile(array $data, $fileLama = null)
    {
        try {
            $path_file = $data["full_path"];
            $size_file = $data["size"];
            $temp_file = $data["tmp_name"];

            $extensi_valid = ["png", "jpg", "jpeg", "webp", "PNG", "JPG", "WEBP"];
            $extensi = pathinfo($path_file, PATHINFO_EXTENSION);
            if (in_array($extensi, $extensi_valid) == false) {
                Flasher::setFLash("Extension tidak valid", "error");
                header("Location: " . Constant::DIRNAME . "ujian/tambah");
                exit;
            }

            //cek size 
            if ($size_file > 1000000) {
                Flasher::setFLash("Ukuran file tidak boleh lebih dari 1 MB", "error");
                header("Location: " . Constant::DIRNAME . "ujian/tambah");
                exit;
            }

            if ($fileLama) {
                $path_file = "asset/img/" . $fileLama;
                if (file_exists($path_file))
                    unlink($path_file);
            }

            $nama_file_baru = uniqid() . "." . $extensi;
            move_uploaded_file($temp_file, "asset/img/" . $nama_file_baru);

            return $nama_file_baru;
        } catch (PDOException $e) {
            return false;
        }
    }
}
