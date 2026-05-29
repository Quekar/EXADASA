<?php

class Maintenance extends Controller {
    public function index() {
        $isMaintenance = $this->model('Pengaturan_model')->getKonfigurasi();
        if(!((bool) $isMaintenance["maintenance"])) {
            header('location: ' . Constant::DIRNAME);
            exit;
        } 

        $data["title"] = "Maintenance";
        $data["css"] = "style.maintenance";
        
        $this->view('templates/header', $data);
        $this->view('maintenance/index', $data);
        $this->view('templates/footer');
    }
}