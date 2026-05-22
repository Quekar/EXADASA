<?php

class Ujian_model
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getUjianHariIni(string $id_kelas = null)
    {
        try {
            if ($id_kelas) {
                $this->db->query(
                    "SELECT * FROM ujian
                     WHERE DATE(jadwal_mulai) = CURDATE()
                       AND status = 'aktif'
                       AND (
                           id_kelas IS NULL
                           OR JSON_CONTAINS(id_kelas, JSON_QUOTE(:id_kelas))
                       )
                     ORDER BY jadwal_mulai ASC"
                );
                $this->db->bind('id_kelas', $id_kelas);
            } else {
                $this->db->query(
                    "SELECT * FROM ujian
                     WHERE DATE(jadwal_mulai) = CURDATE()
                     ORDER BY jadwal_mulai ASC"
                );
            }

            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getUjianTerdekat(string $id_kelas = null)
    {
        try {

            if ($id_kelas) {
                $this->db->query(
                    "SELECT * FROM ujian
                 WHERE jadwal_mulai > NOW()
                   AND status = 'aktif'
                   AND (
                       id_kelas IS NULL
                       OR JSON_CONTAINS(id_kelas, JSON_QUOTE(:id_kelas))
                   )
                 ORDER BY jadwal_mulai ASC LIMIT 1"
                );
                $this->db->bind('id_kelas', $id_kelas);
            } else {
                $this->db->query(
                    "SELECT * FROM ujian
                 WHERE jadwal_mulai > NOW()
                 ORDER BY jadwal_mulai ASC LIMIT 1"
                );
            }

            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function countSelesai($nisn)
    {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM ujian_siswa WHERE nisn = :nisn AND status = 'selesai'");
            $this->db->bind('nisn', $nisn);
            return $this->db->single()['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getAllUjian()
    {
        try {
            $this->db->query('SELECT u.*, k.tingkat, j.nama_jurusan, JSON_LENGTH(u.id_kelas) as jml_kelas 
                              FROM ujian u 
                              LEFT JOIN kelas k ON k.id_kelas = JSON_UNQUOTE(JSON_EXTRACT(u.id_kelas, "$[0]"))
                              LEFT JOIN jurusan j ON k.id_jurusan = j.id_jurusan 
                              ORDER BY u.created_at DESC');
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllKelas()
    {
        try {
            $this->db->query('SELECT k.id_kelas, k.tingkat, j.nama_jurusan 
                              FROM kelas k 
                              JOIN jurusan j ON k.id_jurusan = j.id_jurusan');
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getUjianById($id)
    {
        try {
            $this->db->query('SELECT * FROM ujian WHERE id_ujian = :id');
            $this->db->bind('id', $id);
            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getSoalByUjianId($id)
    {
        try {
            $this->db->query('SELECT bs.* FROM bank_soal bs 
                          JOIN ujian_soal us ON bs.id_bank_soal = us.id_bank_soal 
                          WHERE us.id_ujian = :id');
            $this->db->bind('id', $id);

            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllBankSoal()
    {
        try {
            $this->db->query("SELECT bs.*, ks.nama_kategori 
                              FROM bank_soal bs 
                              LEFT JOIN kategori_soal ks ON bs.id_kategori = ks.id_kategori 
                              ORDER BY bs.created_at DESC");
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllKategori()
    {
        try {
            $this->db->query("SELECT * FROM kategori_soal ORDER BY nama_kategori ASC");
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function tambahUjian($data)
    {
        try {
            $this->db->beginTransaction();

            $id_ujian = uniqid('uj_', true);
            $id_user = $data['id_user'] ?? 1;
            $id_kelas = json_encode($data['id_kelas'] ?? []);
            $waktu = sprintf('%02d:%02d:00', floor($data['waktu_pengerjaan'] / 60), $data['waktu_pengerjaan'] % 60);
            $penilaian = strtolower($data['penilaian']);
            $status = strtolower($data['status']);

            $this->db->query("INSERT INTO ujian (id_ujian, id_user, nama_ujian, deskripsi_ujian, id_kelas, kode_ujian, jadwal_mulai, jadwal_selesai, waktu_pengerjaan, penilaian, status) 
                      VALUES (:id, :id_user, :nama, :deskripsi, :id_kelas, :kode, :mulai, :selesai, :waktu, :penilaian, :status)");
            $this->db->bind('id', $id_ujian);
            $this->db->bind('id_user', $id_user);
            $this->db->bind('nama', $data['nama_ujian']);
            $this->db->bind('deskripsi', $data['deskripsi_ujian']);
            $this->db->bind('id_kelas', $id_kelas);
            $this->db->bind('kode', $data['kode_ujian']);
            $this->db->bind('mulai', $data['jadwal_mulai']);
            $this->db->bind('selesai', $data['jadwal_selesai']);
            $this->db->bind('waktu', $waktu);
            $this->db->bind('penilaian', $penilaian);
            $this->db->bind('status', $status);
            $this->db->execute();

            $unique_soal_ids = [];

            if (isset($data['selected_soal'])) {
                foreach ($data['selected_soal'] as $id_bs) {
                    $unique_soal_ids[$id_bs] = true;
                }
            }

            if (isset($data['soal_text'])) {
                foreach ($data['soal_text'] as $index => $text) {
                    if (empty($text))
                        continue;
                    $id_bank_soal = uniqid('bs_', true);
                    $answer_map = ['A' => 'ja', 'B' => 'jb', 'C' => 'jc', 'D' => 'jd'];
                    $answer = $answer_map[$data['jawaban_benar'][$index]] ?? null;

                    if ($answer) {
                        $this->db->query("INSERT INTO bank_soal (id_bank_soal, pertanyaan, ja, jb, jc, jd, answer) 
                                          VALUES (:id, :pertanyaan, :ja, :jb, :jc, :jd, :answer)");
                        $this->db->bind('id', $id_bank_soal);
                        $this->db->bind('pertanyaan', $text);
                        $this->db->bind('ja', $data['opsi_a'][$index]);
                        $this->db->bind('jb', $data['opsi_b'][$index]);
                        $this->db->bind('jc', $data['opsi_c'][$index]);
                        $this->db->bind('jd', $data['opsi_d'][$index]);
                        $this->db->bind('answer', $answer);
                        $this->db->execute();
                    } else {
                        $this->db->query("INSERT INTO bank_soal (id_bank_soal, pertanyaan, ja, jb, jc, jd) 
                                          VALUES (:id, :pertanyaan, :ja, :jb, :jc, :jd)");
                        $this->db->bind('id', $id_bank_soal);
                        $this->db->bind('pertanyaan', $text);
                        $this->db->bind('ja', $data['opsi_a'][$index]);
                        $this->db->bind('jb', $data['opsi_b'][$index]);
                        $this->db->bind('jc', $data['opsi_c'][$index]);
                        $this->db->bind('jd', $data['opsi_d'][$index]);
                        $this->db->execute();
                    }

                    $unique_soal_ids[$id_bank_soal] = true;
                }
            }

            foreach (array_keys($unique_soal_ids) as $id_soal) {
                $this->db->query("INSERT INTO ujian_soal (id_ujian, id_bank_soal, point) VALUES (:id_ujian, :id_bank_soal, 1)");
                $this->db->bind('id_ujian', $id_ujian);
                $this->db->bind('id_bank_soal', $id_soal);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateUjian($data)
    {
        try {
            $this->db->beginTransaction();

            $id_kelas = json_encode($data['id_kelas'] ?? []);
            $waktu = sprintf('%02d:%02d:00', floor($data['waktu_pengerjaan'] / 60), $data['waktu_pengerjaan'] % 60);
            $penilaian = strtolower($data['penilaian']);
            $status = strtolower($data['status']);

            $query = "UPDATE ujian SET nama_ujian = :nama, deskripsi_ujian = :deskripsi, id_kelas = :id_kelas, 
                             kode_ujian = :kode, jadwal_mulai = :mulai, jadwal_selesai = :selesai, 
                             waktu_pengerjaan = :waktu, penilaian = :penilaian, status = :status 
                      WHERE id_ujian = :id";

            $this->db->query($query);
            $this->db->bind('id', $data['id_ujian']);
            $this->db->bind('nama', $data['nama_ujian']);
            $this->db->bind('deskripsi', $data['deskripsi_ujian']);
            $this->db->bind('id_kelas', $id_kelas);
            $this->db->bind('kode', $data['kode_ujian']);
            $this->db->bind('mulai', $data['jadwal_mulai']);
            $this->db->bind('selesai', $data['jadwal_selesai']);
            $this->db->bind('waktu', $waktu);
            $this->db->bind('penilaian', $penilaian);
            $this->db->bind('status', $status);
            $this->db->execute();

            $unique_soal_ids = [];

            if (isset($data['selected_soal'])) {
                foreach ($data['selected_soal'] as $id_bs) {
                    $unique_soal_ids[$id_bs] = true;
                }
            }

            if (isset($data['soal_text'])) {
                foreach ($data['soal_text'] as $index => $text) {
                    if (empty($text))
                        continue;

                    $id_bank_soal = $data['id_bank_soal_manual'][$index] ?? '';
                    $answer_map = ['A' => 'ja', 'B' => 'jb', 'C' => 'jc', 'D' => 'jd'];
                    $answer = $answer_map[$data['jawaban_benar'][$index]] ?? null;

                    if ($answer) {
                        if (!empty($id_bank_soal)) {
                            $this->db->query("UPDATE bank_soal SET pertanyaan = :pertanyaan, ja = :ja, jb = :jb, jc = :jc, jd = :jd, answer = :answer WHERE id_bank_soal = :id");
                        } else {
                            $id_bank_soal = uniqid('bs_', true);
                            $this->db->query("INSERT INTO bank_soal (id_bank_soal, pertanyaan, ja, jb, jc, jd, answer) VALUES (:id, :pertanyaan, :ja, :jb, :jc, :jd, :answer)");
                        }
                    } else {
                        if (!empty($id_bank_soal)) {
                            $this->db->query("UPDATE bank_soal SET pertanyaan = :pertanyaan, ja = :ja, jb = :jb, jc = :jc, jd = :jd WHERE id_bank_soal = :id");
                        } else {
                            $id_bank_soal = uniqid('bs_', true);
                            $this->db->query("INSERT INTO bank_soal (id_bank_soal, pertanyaan, ja, jb, jc, jd) VALUES (:id, :pertanyaan, :ja, :jb, :jc, :jd)");
                        }
                    }

                    $this->db->bind('id', $id_bank_soal);
                    $this->db->bind('pertanyaan', $text);
                    $this->db->bind('ja', $data['opsi_a'][$index]);
                    $this->db->bind('jb', $data['opsi_b'][$index]);
                    $this->db->bind('jc', $data['opsi_c'][$index]);
                    $this->db->bind('jd', $data['opsi_d'][$index]);
                    if($answer) $this->db->bind('answer', $answer);
                    $this->db->execute();

                    $unique_soal_ids[$id_bank_soal] = true;
                }
            }

            $this->db->query("DELETE FROM ujian_soal WHERE id_ujian = :id");
            $this->db->bind('id', $data['id_ujian']);
            $this->db->execute();

            foreach (array_keys($unique_soal_ids) as $id_soal) {
                $this->db->query("INSERT INTO ujian_soal (id_ujian, id_bank_soal, point) VALUES (:id_ujian, :id_bank_soal, 1)");
                $this->db->bind('id_ujian', $data['id_ujian']);
                $this->db->bind('id_bank_soal', $id_soal);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function hapusUjian($id)
    {
        try {
            $this->db->beginTransaction();
            $this->db->query("DELETE FROM ujian_soal WHERE id_ujian = :id");
            $this->db->bind('id', $id);
            $this->db->execute();

            $this->db->query("DELETE FROM ujian WHERE id_ujian = :id");
            $this->db->bind('id', $id);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}