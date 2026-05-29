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
}