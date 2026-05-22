<?php

class Profile_model
{
    private object $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getUserByRole(string $id_user, string $role)
    {
        try {
            if ($role == 'siswa') {
                $this->db->query('SELECT u.password, s.foto, s.nisn as username, s.nama_lengkap, s.email, k.tingkat as kelas, j.singkatan_jurusan as jurusan FROM users as u JOIN siswa as s ON s.nisn = u.username JOIN data_siswa as ds ON ds.nisn = s.nisn JOIN kelas as k ON k.id_kelas = ds.id_kelas JOIN jurusan as j ON j.id_jurusan = ds.id_jurusan WHERE s.nisn = :id_user');
                $this->db->bind('id_user', $id_user);
            } else if ($role == 'petugas') {
                $this->db->query('SELECT u.password, p.foto, p.nip as username, p.nama_lengkap, p.email FROM users as u JOIN petugas as p ON u.username = p.nip WHERE nip = :id_user');
                $this->db->bind('id_user', $id_user);
            } else {
                $this->db->query('SELECT password, username as nama_lengkap, username as username, role FROM users WHERE username = :id_user');
                $this->db->bind('id_user', $id_user);
            }

            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getStudentStats(string $nisn)
    {
        try {
            $this->db->query("SELECT COUNT(*) as total_ujian, AVG(nilai) as rata_rata FROM nilai_siswa WHERE nisn = :nisn");
            $this->db->bind('nisn', $nisn);
            return $this->db->single();
        } catch (PDOException $e) {
            return ['total_ujian' => 0, 'rata_rata' => 0];
        }
    }

    public function updateProfile(array $data, array $file, string $role)
    {
        try {
            $username = $data['username'];
            if ($role == "siswa" || $role == "petugas") {
                $nama_lengkap = $data['nama_lengkap'];
                $email = $data['email'];
                $foto = $data["foto_old"];

                if (isset($file["foto_new"]) && $file["foto_new"]["error"] == UPLOAD_ERR_OK) {
                    $foto = $this->uploadFile($file["foto_new"], $foto);
                }

                if ($role == "siswa") {
                    $this->db->query("UPDATE siswa SET foto = :foto, nama_lengkap = :nama_lengkap, email = :email WHERE nisn = :username");
                } else {
                    $this->db->query("UPDATE petugas SET foto = :foto, nama_lengkap = :nama_lengkap, email = :email WHERE nip = :username");
                }

                $this->db->bind('username', $username);
                $this->db->bind('foto', $foto);
                $this->db->bind('nama_lengkap', $nama_lengkap);
                $this->db->bind('email', $email);
                $this->db->execute();
                if($this->db->rowCount() > 0) $_SESSION['user']["nama_lengkap"] = $nama_lengkap;
            }

            if ($data["current_password"] && $data["new_password"] && $data["confirm_password"]) {
                $password_new = $data["new_password"];
                $password_old = $data["current_password"];
                $confirm_password = $data["confirm_password"];

                if ($password_new != $confirm_password) {
                    Flasher::setFlash("Konfirmasi password tidak sesuai", "error");
                    header("Location: " . Constant::DIRNAME . "profile");
                    exit;
                }

                $user = $this->getUserByRole($username, $role);
                if (password_verify($password_old, $user["password"])) {
                    $result = $this->updatePassword($username, $password_new);
                    if (!$result) {
                        Flasher::setFlash("Password gagal diubah", "success");
                        header("Location: " . Constant::DIRNAME . "profile");
                        exit;
                    } 
                } else {
                    Flasher::setFlash("Password lama tidak sesuai", "error");
                    header("Location: " . Constant::DIRNAME . "profile");
                    exit;
                }
            } 

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function updatePassword(string $username, string $new_password)
    {
        try {
            $hash = password_hash($new_password, PASSWORD_BCRYPT);
            $this->db->query("UPDATE users SET password = :pass WHERE username = :id_user");
            $this->db->bind('pass', $hash);
            $this->db->bind('id_user', $username);
            $this->db->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
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
                header("Location: " . Constant::DIRNAME . "pengaturan");
                exit;
            }

            //cek size 
            if ($size_file > 1000000) {
                Flasher::setFLash("Ukuran file tidak boleh lebih dari 1 MB", "error");
                header("Location: " . Constant::DIRNAME . "pengaturan");
                exit;
            }

            if ($fileLama) {
                $path_file = "asset/img/" . $fileLama;
                if (file_exists($path_file))
                    unlink($path_file);
            }

            $nama_file_baru = uniqid() . "." . $extensi;
            move_uploaded_file($temp_file, "asset/img/" . $nama_file_baru);

            $_SESSION['user']['foto'] = $nama_file_baru;
            return $nama_file_baru;
        } catch (PDOException $e) {
            return false;
        }
    }
}
