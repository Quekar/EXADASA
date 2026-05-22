<?php

class Profile extends Controller
{
    public function index()
    {
        $role = $_SESSION['user']['role'];
        $username = $_SESSION['user']['username'];

        $data['user'] = $this->model("Profile_model")->getUserByRole($username, $role);
        $data["title"] = "Profile";
        $data["css"] = "style.profile";

        if ($role == 'siswa')
            $data['stats'] = $this->model("Profile_model")->getStudentStats($username);

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('profile/index', $data);
        $this->view('templates/footer');
    }

    public function update() {
        if ($this->model("Profile_model")->updateProfile($_POST, $_FILES, $_SESSION["user"]["role"]) > 0) {
            Flasher::setFlash("Profil berhasil diubah", "success");
            header("Location: " . Constant::DIRNAME . "profile");
            exit;
        } else {
            Flasher::setFlash("Profil gagal diubah", "error");
            header("Location: " . Constant::DIRNAME . "profile");
            exit;
        }
    }
}