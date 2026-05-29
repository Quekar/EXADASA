<?php

class Login_model
{
    private object $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function login(array $data)
    {
        $username = htmlspecialchars(stripcslashes($data["username"]));
        $password = stripcslashes($data["password"]); 
        $remember = $data["remember"];

        try {
            $user = $this->getUserByUsername($username);
            if (!$user)
                throw new PDOException("Username tidak ditemukan");

            if (!password_verify($password, $user["password"]))
                throw new PDOException("Password salah");

            $userRole = $this->getUserByRole($user["username"], $user["role"]);

            $sesiUser = [
                "id" => $user["id_user"],
                "foto" => $userRole["foto"] ?? null,
                "username" => $user["username"],
                "nama_lengkap" => $userRole["nama_lengkap"],
                "role" => $user["role"]
            ];

            $_SESSION["user"] = $sesiUser;

            if($remember) {
                setcookie("token", password_hash($user["id_user"], PASSWORD_DEFAULT), time() + 7 * 24 * 60 * 60, "/");
                setcookie("key", json_encode($sesiUser), time() + 7 * 24 * 60 * 60, "/");
            }
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
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
            echo $e->getMessage();
        }
    }

    public function getUserByRole(string $id_user, string $role)
    {
        try {
            if ($role == 'siswa') {
                $this->db->query('SELECT foto, nisn as id_user, nama_lengkap, email FROM siswa WHERE nisn = :id_user');
                $this->db->bind('id_user', $id_user);
                $this->db->execute();
                return $this->db->single();
            } else if ($role == 'petugas') {
                $this->db->query('SELECT foto, nip as id_user, nama_lengkap, email FROM petugas WHERE nip = :id_user');
                $this->db->bind('id_user', $id_user);
                $this->db->execute();
                return $this->db->single();
            } else {
                $this->db->query('SELECT username as nama_lengkap, role FROM users WHERE username = :id_user');
                $this->db->bind('id_user', $id_user);
                $this->db->execute();
                return $this->db->single();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }
}