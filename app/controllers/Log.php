<?php

class Log extends Controller
{
    public function index()
    {
        
        $logs = $this->model('Dashboard_model')->getAllLog();
        
        $data["logs"] = array_map(function($log) {
            $log['aksi'] = $this->getAksiLabel($log['deskripsi']);
            return $log;
        }, $logs);

        $data["title"] = "Log Aktivitas";
        $data["css"]   = "style.log";

        $this->view('templates/header', $data);
        $this->view('templates/sidebar', $data);
        $this->view('templates/navbar', $data);
        $this->view('log/index', $data);
        $this->view('templates/footer');
    }

    private function getAksiLabel(string $deskripsi): array 
    {
        $lower = strtolower($deskripsi);
        if (str_contains($lower, 'login'))     return ['label' => 'Login',   'class' => 'badge-info'];
        if (str_contains($lower, 'logout'))    return ['label' => 'Logout',  'class' => 'badge-secondary'];
        if (str_contains($lower, 'submit'))    return ['label' => 'Submit',  'class' => 'badge-success'];
        if (str_contains($lower, 'membuat') || str_contains($lower, 'menambah') || str_contains($lower, 'mendaftar'))
                                               return ['label' => 'Tambah',  'class' => 'badge-primary'];
        if (str_contains($lower, 'mengubah'))  return ['label' => 'Edit',    'class' => 'badge-warning'];
        if (str_contains($lower, 'menghapus')) return ['label' => 'Hapus',   'class' => 'badge-danger'];
        
        return ['label' => 'Aksi', 'class' => 'badge-secondary'];
    }
}