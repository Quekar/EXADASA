<?php

class Koreksi_model
{
    private object $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllKoreksi()
    {
        try {
            $query = "SELECT us.id_ujian_siswa, us.id_ujian, us.nisn, us.status, us.waktu_selesai, us.waktu_masuk,
                             s.nama_lengkap, s.foto,
                             ds.id_kelas, 
                             ns.id_nilai_siswa, ns.total_benar, ns.total_salah, ns.nilai, ns.publik,
                             u.nama_ujian
                      FROM ujian_siswa us
                      JOIN siswa s ON us.nisn = s.nisn
                      JOIN data_siswa ds ON us.nisn = ds.nisn
                      JOIN ujian u ON us.id_ujian = u.id_ujian
                      LEFT JOIN nilai_siswa ns ON us.id_ujian_siswa = ns.id_ujian_siswa
                      WHERE us.status IN ('selesai', 'timeout')
                      ORDER BY us.waktu_selesai DESC";

            $this->db->query($query);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getDetailUjianSiswa($id_ujian_siswa)
    {
        try {
            $query = "SELECT us.*, s.nama_lengkap, s.foto, ds.id_kelas, u.nama_ujian, u.waktu_pengerjaan
                      FROM ujian_siswa us
                      JOIN siswa s ON us.nisn = s.nisn
                      JOIN data_siswa ds ON us.nisn = ds.nisn
                      JOIN ujian u ON us.id_ujian = u.id_ujian
                      WHERE us.id_ujian_siswa = :id_ujian_siswa";

            $this->db->query($query);
            $this->db->bind('id_ujian_siswa', $id_ujian_siswa);
            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getJawabanDetail($id_ujian_siswa, $id_ujian)
    {
        try {
            $query = "SELECT usoal.point as skor_max, bs.id_bank_soal, bs.pertanyaan, bs.ja, bs.jb, bs.jc, bs.jd, bs.answer as kunci,
                             js.answer as jawaban_siswa, js.jawaban_benar, js.benar, js.id_ujian_siswa
                      FROM ujian_soal usoal
                      JOIN bank_soal bs ON usoal.id_bank_soal = bs.id_bank_soal
                      LEFT JOIN jawaban_siswa js ON js.id_bank_soal = bs.id_bank_soal AND js.id_ujian_siswa = :id_ujian_siswa
                      WHERE usoal.id_ujian = :id_ujian";
            $this->db->query($query);
            $this->db->bind('id_ujian_siswa', $id_ujian_siswa);
            $this->db->bind('id_ujian', $id_ujian);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getNilaiSiswa($id_ujian_siswa)
    {
        try {
            $this->db->query("SELECT * FROM nilai_siswa WHERE id_ujian_siswa = :id_ujian_siswa");
            $this->db->bind('id_ujian_siswa', $id_ujian_siswa);
            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function simpanNilai($data)
    {
        try {
            $id_nilai_siswa = 'NS-' . uniqid();
            $query = "INSERT INTO nilai_siswa 
                      (id_nilai_siswa, id_ujian, id_ujian_siswa, nisn, total_benar, total_salah, nilai, publik) 
                      VALUES 
                      (:id_nilai_siswa, :id_ujian, :id_ujian_siswa, :nisn, :total_benar, :total_salah, :nilai, :publik)";

            $this->db->query($query);
            $this->db->bind('id_nilai_siswa', $id_nilai_siswa);
            $this->db->bind('id_ujian', $data['id_ujian']);
            $this->db->bind('id_ujian_siswa', $data['id_ujian_siswa']);
            $this->db->bind('nisn', $data['nisn']);
            $this->db->bind('total_benar', $data['total_benar']);
            $this->db->bind('total_salah', $data['total_salah']);
            $this->db->bind('nilai', $data['nilai']);
            $this->db->bind('publik', isset($data['publik']) ? $data['publik'] : 0);

            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateNilai($data)
    {
        try {
            $query = "UPDATE nilai_siswa 
                      SET total_benar = :total_benar, 
                          total_salah = :total_salah, 
                          nilai = :nilai
                      WHERE id_ujian_siswa = :id_ujian_siswa";

            $this->db->query($query);
            $this->db->bind('id_ujian_siswa', $data['id_ujian_siswa']);
            $this->db->bind('total_benar', $data['total_benar']);
            $this->db->bind('total_salah', $data['total_salah']);
            $this->db->bind('nilai', $data['nilai']);

            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function simpanAtauUpdateNilai($data)
    {
        $existing = $this->getNilaiSiswa($data['id_ujian_siswa']);
        if ($existing) {
            return $this->updateNilai($data);
        } else {
            return $this->simpanNilai($data);
        }
    }

    public function ubahUjianSiswa($data)
    {
        try {
            $idBankSoal = $data["id_bank_soal"];
            $idUjianSiswa = $data["id_ujian_siswa"];
            $isBenar = $data["koreksi"] == "benar" ? 1 : 0;
            $jawaban = $data["jawaban"] ?? null;

            if ($jawaban) {
                $this->db->query("UPDATE jawaban_siswa SET benar = :benar, jawaban_benar = :jawaban WHERE id_ujian_siswa = :id_ujian_siswa AND id_bank_soal = :id_bank_soal");
                $this->db->bind("benar", $isBenar);
                $this->db->bind("jawaban", $jawaban);
                $this->db->bind("id_ujian_siswa", $idUjianSiswa);
                $this->db->bind("id_bank_soal", $idBankSoal);
            } else {
                $this->db->query("UPDATE jawaban_siswa SET benar = :benar WHERE id_ujian_siswa = :id_ujian_siswa AND id_bank_soal = :id_bank_soal");
                $this->db->bind("benar", $isBenar);
                $this->db->bind("id_ujian_siswa", $idUjianSiswa);
                $this->db->bind("id_bank_soal", $idBankSoal);
            }

            $this->db->execute();
            return true;
        } catch (PDOException $e) {
            return $e;
            // return false;
        }
    }

    public function setPublishStatus($id_ujian_siswa, $publik)
    {
        try {
            $existing = $this->getNilaiSiswa($id_ujian_siswa);
            if (!$existing) {
                $ujian_siswa = $this->getDetailUjianSiswa($id_ujian_siswa);
                if (!$ujian_siswa)
                    return false;

                $raw_questions = $this->getJawabanDetail($id_ujian_siswa, $ujian_siswa['id_ujian']);
                $total_benar = 0;
                $total_salah = 0;
                $skorTotal = 0;
                $skorMax = 0;

                foreach ($raw_questions as $q) {
                    $isCorrect = ($q['jawaban_siswa'] === $q['kunci']);
                    if ($isCorrect) {
                        $total_benar++;
                        $skorTotal += $q['skor_max'];
                    } else {
                        $total_salah++;
                    }
                    $skorMax += $q['skor_max'];
                }

                $persentase = $skorMax > 0 ? round(($skorTotal / $skorMax) * 100) : 0;

                $nilaiData = [
                    'id_ujian' => $ujian_siswa['id_ujian'],
                    'id_ujian_siswa' => $id_ujian_siswa,
                    'nisn' => $ujian_siswa['nisn'],
                    'total_benar' => $total_benar,
                    'total_salah' => $total_salah,
                    'nilai' => $persentase,
                    'publik' => $publik
                ];
                return $this->simpanNilai($nilaiData);
            }

            $query = "UPDATE nilai_siswa SET publik = :publik WHERE id_ujian_siswa = :id_ujian_siswa";
            $this->db->query($query);
            $this->db->bind('publik', $publik);
            $this->db->bind('id_ujian_siswa', $id_ujian_siswa);
            $this->db->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
