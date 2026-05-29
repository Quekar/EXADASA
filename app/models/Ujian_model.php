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

    public function tambahUjian($data, $file)
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

            if (isset($file['file_csv']) && $file['file_csv']['error'] == 0) {
                $new_csv_soal_ids = $this->importSoalFromCSV($file['file_csv']);
                foreach ($new_csv_soal_ids as $id_bs) {
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
                    $soal_gambar = null;

                    if ($file['soal_gambar']['error'][$index] == 0) {
                        $filterdata = [
                            'name' => $file['soal_gambar']['name'][$index],
                            'full_path' => $file['soal_gambar']['full_path'][$index],
                            'type' => $file['soal_gambar']['type'][$index],
                            'tmp_name' => $file['soal_gambar']['tmp_name'][$index],
                            'error' => $file['soal_gambar']['error'][$index],
                            'size' => $file['soal_gambar']['size'][$index],
                        ];

                        $soal_gambar = $this->uploadFile($filterdata);
                    }

                    if ($answer) {
                        if ($soal_gambar) {
                            $this->db->query("INSERT INTO bank_soal (id_bank_soal, gambar, pertanyaan, ja, jb, jc, jd, answer) 
                                            VALUES (:id, :gambar, :pertanyaan, :ja, :jb, :jc, :jd, :answer)");
                        } else {
                            $this->db->query("INSERT INTO bank_soal (id_bank_soal, pertanyaan, ja, jb, jc, jd, answer) 
                                            VALUES (:id, :pertanyaan, :ja, :jb, :jc, :jd, :answer)");
                        }
                        $this->db->bind('id', $id_bank_soal);
                        if ($soal_gambar)
                            $this->db->bind('gambar', $soal_gambar);
                        $this->db->bind('pertanyaan', $text);
                        $this->db->bind('ja', $data['opsi_a'][$index]);
                        $this->db->bind('jb', $data['opsi_b'][$index]);
                        $this->db->bind('jc', $data['opsi_c'][$index]);
                        $this->db->bind('jd', $data['opsi_d'][$index]);
                        $this->db->bind('answer', $answer);
                        $this->db->execute();
                    } else {
                        if ($soal_gambar) {
                            $this->db->query("INSERT INTO bank_soal (id_bank_soal, gambar, pertanyaan, ja, jb, jc, jd) 
                                            VALUES (:id, :gambar, :pertanyaan, :ja, :jb, :jc, :jd)");
                        } else {
                            $this->db->query("INSERT INTO bank_soal (id_bank_soal, pertanyaan, ja, jb, jc, jd) 
                                            VALUES (:id, :pertanyaan, :ja, :jb, :jc, :jd)");
                        }
                        $this->db->bind('id', $id_bank_soal);
                        if ($soal_gambar)
                            $this->db->bind('gambar', $soal_gambar);
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
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateUjian($data, $file)
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

            if (isset($file['file_csv']) && $file['file_csv']['error'] == 0) {
                $new_csv_soal_ids = $this->importSoalFromCSV($file['file_csv']);
                foreach ($new_csv_soal_ids as $id_bs) {
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
                    $soal_gambar = $data['soal_gambar_old'][$index];
                 
                    if ($file['soal_gambar']['error'][$index] == 0) {
                        $filterdata = [
                            'name' => $file['soal_gambar']['name'][$index],
                            'full_path' => $file['soal_gambar']['full_path'][$index],
                            'type' => $file['soal_gambar']['type'][$index],
                            'tmp_name' => $file['soal_gambar']['tmp_name'][$index],
                            'error' => $file['soal_gambar']['error'][$index],
                            'size' => $file['soal_gambar']['size'][$index],
                        ];
                        $soal_gambar = $this->uploadFile($filterdata);
                    }

                    if ($answer) {
                        if ($soal_gambar) {
                            if (!empty($id_bank_soal)) {
                                $this->db->query("UPDATE bank_soal SET gambar = :gambar, pertanyaan = :pertanyaan, ja = :ja, jb = :jb, jc = :jc, jd = :jd, answer = :answer WHERE id_bank_soal = :id");
                            } else {
                                $id_bank_soal = uniqid('bs_', true);
                                $this->db->query("INSERT INTO bank_soal (id_bank_soal, gambar, pertanyaan, ja, jb, jc, jd, answer) VALUES (:id, :gambar, :pertanyaan, :ja, :jb, :jc, :jd, :answer)");
                            }
                        } else {
                            if (!empty($id_bank_soal)) {
                                $this->db->query("UPDATE bank_soal SET pertanyaan = :pertanyaan, ja = :ja, jb = :jb, jc = :jc, jd = :jd, answer = :answer WHERE id_bank_soal = :id");
                            } else {
                                $id_bank_soal = uniqid('bs_', true);
                                $this->db->query("INSERT INTO bank_soal (id_bank_soal, pertanyaan, ja, jb, jc, jd, answer) VALUES (:id, :pertanyaan, :ja, :jb, :jc, :jd, :answer)");
                            }
                        }
                    } else {
                        if ($soal_gambar) {
                            if (!empty($id_bank_soal)) {
                                $this->db->query("UPDATE bank_soal SET gambar = :gambar, pertanyaan = :pertanyaan, ja = :ja, jb = :jb, jc = :jc, jd = :jd WHERE id_bank_soal = :id");
                            } else {
                                $id_bank_soal = uniqid('bs_', true);
                                $this->db->query("INSERT INTO bank_soal (id_bank_soal, gambar, pertanyaan, ja, jb, jc, jd) VALUES (:id, :gambar, :pertanyaan, :ja, :jb, :jc, :jd)");
                            }
                        } else {
                            if (!empty($id_bank_soal)) {
                                $this->db->query("UPDATE bank_soal SET pertanyaan = :pertanyaan, ja = :ja, jb = :jb, jc = :jc, jd = :jd WHERE id_bank_soal = :id");
                            } else {
                                $id_bank_soal = uniqid('bs_', true);
                                $this->db->query("INSERT INTO bank_soal (id_bank_soal, pertanyaan, ja, jb, jc, jd) VALUES (:id, :pertanyaan, :ja, :jb, :jc, :jd)");
                            }
                        }
                    }

                    $this->db->bind('id', $id_bank_soal);
                    if($soal_gambar) $this->db->bind('gambar', $soal_gambar);
                    $this->db->bind('pertanyaan', $text);
                    $this->db->bind('ja', $data['opsi_a'][$index]);
                    $this->db->bind('jb', $data['opsi_b'][$index]);
                    $this->db->bind('jc', $data['opsi_c'][$index]);
                    $this->db->bind('jd', $data['opsi_d'][$index]);
                    if ($answer) $this->db->bind('answer', $answer);
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

    public function importSoalFromCSV($file)
    {
        $new_ids = [];
        try {
            $filepath = $file['tmp_name'];
            if (($handle = fopen($filepath, "r")) !== FALSE) {
                $firstLine = fgets($handle);
                if ($firstLine === FALSE) {
                    fclose($handle);
                    return [];
                }
                
                if (substr($firstLine, 0, 3) == "\xEF\xBB\xBF") {
                    $firstLine = substr($firstLine, 3);
                }
                
                $separator = ",";
                if (strpos($firstLine, ";") !== FALSE && (strpos($firstLine, ",") === FALSE || strpos($firstLine, ";") < strpos($firstLine, ","))) {
                    $separator = ";";
                }
                
                $headers = str_getcsv($firstLine, $separator);
                $headers = array_map(function($h) {
                    return strtolower(trim(str_replace(['"', "'"], '', $h)));
                }, $headers);
                
                $field_map = [
                    'pertanyaan' => -1,
                    'ja' => -1,
                    'jb' => -1,
                    'jc' => -1,
                    'jd' => -1,
                    'answer' => -1
                ];
                
                foreach ($headers as $index => $header) {
                    if (in_array($header, ['pertanyaan', 'soal', 'question', 'text'])) {
                        $field_map['pertanyaan'] = $index;
                    } elseif (in_array($header, ['ja', 'opsi a', 'opsi_a', 'pilihan a', 'pilihan_a', 'a'])) {
                        $field_map['ja'] = $index;
                    } elseif (in_array($header, ['jb', 'opsi b', 'opsi_b', 'pilihan b', 'pilihan_b', 'b'])) {
                        $field_map['jb'] = $index;
                    } elseif (in_array($header, ['jc', 'opsi c', 'opsi_c', 'pilihan c', 'pilihan_c', 'c'])) {
                        $field_map['jc'] = $index;
                    } elseif (in_array($header, ['jd', 'opsi d', 'opsi_d', 'pilihan d', 'pilihan_d', 'd'])) {
                        $field_map['jd'] = $index;
                    } elseif (in_array($header, ['answer', 'jawaban', 'jawaban benar', 'jawaban_benar', 'kunci', 'kunci jawaban', 'kunci_jawaban'])) {
                        $field_map['answer'] = $index;
                    }
                }
                
                if ($field_map['pertanyaan'] === -1) $field_map['pertanyaan'] = 0;
                if ($field_map['ja'] === -1) $field_map['ja'] = 1;
                if ($field_map['jb'] === -1) $field_map['jb'] = 2;
                if ($field_map['jc'] === -1) $field_map['jc'] = 3;
                if ($field_map['jd'] === -1) $field_map['jd'] = 4;
                if ($field_map['answer'] === -1) $field_map['answer'] = 5;
                
                while (($row = fgetcsv($handle, 0, $separator)) !== FALSE) {
                    if (empty($row) || count($row) < 2) continue;
                    
                    $pertanyaan = isset($row[$field_map['pertanyaan']]) ? trim($row[$field_map['pertanyaan']]) : '';
                    if (empty($pertanyaan)) continue;
                    
                    $ja = isset($row[$field_map['ja']]) ? trim($row[$field_map['ja']]) : '';
                    $jb = isset($row[$field_map['jb']]) ? trim($row[$field_map['jb']]) : '';
                    $jc = isset($row[$field_map['jc']]) ? trim($row[$field_map['jc']]) : '';
                    $jd = isset($row[$field_map['jd']]) ? trim($row[$field_map['jd']]) : '';
                    
                    $ans_raw = isset($row[$field_map['answer']]) ? strtoupper(trim($row[$field_map['answer']])) : '';
                    $answer = null;
                    if (in_array($ans_raw, ['A', 'JA'])) $answer = 'ja';
                    elseif (in_array($ans_raw, ['B', 'JB'])) $answer = 'jb';
                    elseif (in_array($ans_raw, ['C', 'JC'])) $answer = 'jc';
                    elseif (in_array($ans_raw, ['D', 'JD'])) $answer = 'jd';
                    else {
                        if (!empty($ans_raw)) {
                            if ($ans_raw === strtoupper($ja)) $answer = 'ja';
                            elseif ($ans_raw === strtoupper($jb)) $answer = 'jb';
                            elseif ($ans_raw === strtoupper($jc)) $answer = 'jc';
                            elseif ($ans_raw === strtoupper($jd)) $answer = 'jd';
                        }
                    }
                    
                    if (!$answer) $answer = 'ja'; 
                    $id_bank_soal = uniqid('bs_', true);
                    
                    $this->db->query("INSERT INTO bank_soal (id_bank_soal, pertanyaan, ja, jb, jc, jd, answer) 
                                      VALUES (:id, :pertanyaan, :ja, :jb, :jc, :jd, :answer)");
                    $this->db->bind('id', $id_bank_soal);
                    $this->db->bind('pertanyaan', $pertanyaan);
                    $this->db->bind('ja', $ja);
                    $this->db->bind('jb', $jb);
                    $this->db->bind('jc', $jc);
                    $this->db->bind('jd', $jd);
                    $this->db->bind('answer', $answer);
                    $this->db->execute();
                    
                    $new_ids[] = $id_bank_soal;
                }
                fclose($handle);
            }
        } catch (Exception $e) {
        }
        return $new_ids;
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