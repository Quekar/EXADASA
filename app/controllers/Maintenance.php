<?php

class Maintenance extends Controller {
    public function index() {
        $data["title"] = "Maintenance";
        $data["css"] = "style.maintenance";
        
        $this->view('templates/header', $data);
        $this->view('maintenance/index', $data);
        $this->view('templates/footer');
    }
}