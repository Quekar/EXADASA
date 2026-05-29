<?php

class Profile extends Controller
{
    public function index()
    {
        $data['user'] = $this->model("Profile_model")->getUserByRole($_SESSION['user']['username'], $_SESSION['user']['role']);
        $data["title"] = "Profile";
        $data["css"] = "style.profile";

        if ($_SESSION['user']['role'] == 'siswa')
            $data['stats'] = $this->model("Profile_model")->getStudentStats($_SESSION['user']['username']);

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('profile/index', $data);
        $this->view('templates/footer');
    }

    public function update()
    {

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