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

    public function tambahSoal(array $data)
    {
        try {
            $id = uniqid('bs_', true);
            $answer = isset($data["jawaban_benar"]) ? $data["jawaban_benar"] : null;

            if ($answer) {
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

            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateSoal($data)
    {
        try {
            $answer_map = ['A' => 'ja', 'B' => 'jb', 'C' => 'jc', 'D' => 'jd'];
            $answer = $answer_map[$data['jawaban_benar']] ?? $data['jawaban_benar'];

            $this->db->query("UPDATE bank_soal SET pertanyaan = :pertanyaan, id_kategori = :id_kategori, 
                              ja = :ja, jb = :jb, jc = :jc, jd = :jd, answer = :answer 
                              WHERE id_bank_soal = :id");
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
}
