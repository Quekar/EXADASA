<?php

class Register_model
{
    private object $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function register(array $data)
    {
        try {
            $username = htmlspecialchars(stripcslashes($data["username"]));
            $password = $data["password"];
            $nama_lengkap = htmlspecialchars(stripcslashes($data["nama_lengkap"]));
            $role = $data["role"];

            $user = $this->getUserByUsername($username);
            if ($user)
                throw new PDOException("Username sudah terdaftar");

            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            if ($role == "siswa") {
                $nisn = htmlspecialchars(stripcslashes($data["nisn"]));
                $email = htmlspecialchars(stripcslashes($data["email"]));
                $id_kelas = $data["kelas"];
                $id_jurusan = $data["jurusan"];

                $this->db->beginTransaction();
                $this->db->query("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
                $this->db->bind("username", $username);
                $this->db->bind("password", $password_hash);
                $this->db->bind("role", $role);
                $this->db->execute();
                
                $this->db->query("INSERT INTO siswa (nisn, nama_lengkap, email) VALUES (:nisn, :nama_lengkap, :email)");
                $this->db->bind("nisn", $nisn);
                $this->db->bind("nama_lengkap", $nama_lengkap);
                $this->db->bind("email", $email);
                $this->db->execute();

                $this->db->query("INSERT INTO data_siswa (nisn, id_kelas, id_jurusan) VALUES (:nisn, :id_kelas, :id_jurusan)");
                $this->db->bind("nisn", $nisn);
                $this->db->bind("id_kelas", $id_kelas);
                $this->db->bind("id_jurusan", $id_jurusan);
                $this->db->execute();
                $this->db->commit();

                return $this->db->rowCount();
            } else if ($role == "petugas") {
                $nip = htmlspecialchars(stripcslashes($data["nip"]));
                $email = htmlspecialchars(stripcslashes($data["email"]));

                $this->db->beginTransaction();
                $this->db->query("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
                $this->db->bind("username", $username);
                $this->db->bind("password", $password_hash);
                $this->db->bind("role", $role);
                $this->db->execute();
                
                $this->db->query("INSERT INTO petugas (nip, nama_lengkap, email) VALUES (:nip, :nama_lengkap, :email)");
                $this->db->bind("nip", $nip);
                $this->db->bind("nama_lengkap", $nama_lengkap);
                $this->db->bind("email", $email);
                $this->db->execute();
                $this->db->commit();

                return $this->db->rowCount();
            } else {
                $this->db->query("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
                $this->db->bind("username", $username);
                $this->db->bind("password", $password_hash);
                $this->db->bind("role", "admin");
                $this->db->execute();
                
                return $this->db->rowCount();
            }
        } catch (PDOException $e) {
            try { $this->db->rollBack(); } catch (Exception $ex) {}
            return false;
        }
    }
    public function getUserByUsername(string $username)
    {
        try {
            $this->db->query("SELECT * FROM users WHERE username = :username");
            $this->db->bind("username", $username);
            return $this->db->single();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function importSiswaFromCSV($file)
    {
        try {
            $filepath = $file['tmp_name'];
            if (($handle = fopen($filepath, "r")) !== FALSE) {
                $firstLine = fgets($handle);
                if ($firstLine === FALSE) {
                    fclose($handle);
                    return false;
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
                    'nisn' => -1,
                    'nama_lengkap' => -1,
                    'email' => -1,
                    'tingkat' => -1,
                    'jurusan' => -1
                ];
                
                foreach ($headers as $index => $header) {
                    if (in_array($header, ['nisn', 'nomor induk', 'nis'])) {
                        $field_map['nisn'] = $index;
                    } elseif (in_array($header, ['nama_lengkap', 'nama lengkap', 'nama', 'name'])) {
                        $field_map['nama_lengkap'] = $index;
                    } elseif (in_array($header, ['email', 'surel', 'mail'])) {
                        $field_map['email'] = $index;
                    } elseif (in_array($header, ['tingkat', 'kelas', 'tingkat kelas', 'tingkat_kelas', 'grade'])) {
                        $field_map['tingkat'] = $index;
                    } elseif (in_array($header, ['jurusan', 'singkatan jurusan', 'id_jurusan', 'id jurusan', 'major'])) {
                        $field_map['jurusan'] = $index;
                    }
                }
                
                if ($field_map['nisn'] === -1) $field_map['nisn'] = 0;
                if ($field_map['nama_lengkap'] === -1) $field_map['nama_lengkap'] = 1;
                if ($field_map['email'] === -1) $field_map['email'] = 2;
                if ($field_map['tingkat'] === -1) $field_map['tingkat'] = 3;
                if ($field_map['jurusan'] === -1) $field_map['jurusan'] = 4;
                
                $this->db->beginTransaction();
                
                while (($row = fgetcsv($handle, 0, $separator)) !== FALSE) {
                    if (empty($row) || count($row) < 2) continue;
                    
                    $nisn = isset($row[$field_map['nisn']]) ? htmlspecialchars(trim($row[$field_map['nisn']])) : '';
                    if (empty($nisn)) continue;
                    
                    $nama_lengkap = isset($row[$field_map['nama_lengkap']]) ? htmlspecialchars(trim($row[$field_map['nama_lengkap']])) : '';
                    $email = isset($row[$field_map['email']]) ? htmlspecialchars(trim($row[$field_map['email']])) : '';
                    
                    $tingkat_raw = isset($row[$field_map['tingkat']]) ? strtoupper(trim($row[$field_map['tingkat']])) : '';
                    $tingkat = 10;
                    if (in_array($tingkat_raw, ['X', '10'])) $tingkat = 10;
                    elseif (in_array($tingkat_raw, ['XI', '11'])) $tingkat = 11;
                    elseif (in_array($tingkat_raw, ['XII', '12'])) $tingkat = 12;
                    else {
                        preg_match('/\d+/', $tingkat_raw, $matches);
                        if (!empty($matches)) {
                            $num = intval($matches[0]);
                            if ($num >= 10 && $num <= 12) $tingkat = $num;
                        }
                    }
                    
                    $jurusan_raw = isset($row[$field_map['jurusan']]) ? trim($row[$field_map['jurusan']]) : '';
                    if (empty($jurusan_raw)) continue;
                    
                    $this->db->query("SELECT id_jurusan FROM jurusan WHERE id_jurusan = :query OR singkatan_jurusan = :query OR nama_jurusan = :query LIMIT 1");
                    $this->db->bind("query", $jurusan_raw);
                    $jurusan_row = $this->db->single();
                    
                    if (!$jurusan_row) continue;
                    
                    
                    $id_jurusan = $jurusan_row['id_jurusan'];
                    
                    $this->db->query("SELECT id_kelas FROM kelas WHERE id_jurusan = :id_jurusan AND tingkat = :tingkat LIMIT 1");
                    $this->db->bind("id_jurusan", $id_jurusan);
                    $this->db->bind("tingkat", $tingkat);
                    $kelas_row = $this->db->single();
                    
                    $id_kelas = $kelas_row ? $kelas_row['id_kelas'] : ($tingkat . '-' . $id_jurusan);
                    
                    $this->db->query("SELECT * FROM users WHERE username = :username");
                    $this->db->bind("username", $nisn);
                    if ($this->db->single()) {
                        continue;
                    }
                    
                    $password_hash = password_hash($nisn, PASSWORD_BCRYPT);
                    $this->db->query("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
                    $this->db->bind("username", $nisn);
                    $this->db->bind("password", $password_hash);
                    $this->db->bind("role", "siswa");
                    $this->db->execute();
                    
                    $this->db->query("INSERT INTO siswa (nisn, nama_lengkap, email) VALUES (:nisn, :nama_lengkap, :email)");
                    $this->db->bind("nisn", $nisn);
                    $this->db->bind("nama_lengkap", $nama_lengkap);
                    $this->db->bind("email", $email);
                    $this->db->execute();
                    
                    $this->db->query("INSERT INTO data_siswa (nisn, id_kelas, id_jurusan) VALUES (:nisn, :id_kelas, :id_jurusan)");
                    $this->db->bind("nisn", $nisn);
                    $this->db->bind("id_kelas", $id_kelas);
                    $this->db->bind("id_jurusan", $id_jurusan);
                    $this->db->execute();
                }
                
                fclose($handle);
                $this->db->commit();
                return true;
            }
        } catch (Exception $e) {
            try { $this->db->rollBack(); } catch (Exception $ex) {}
            return false;
        }
        return false;
    }
}