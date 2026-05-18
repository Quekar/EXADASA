<?php 

class Controller {
	public function view(string $view, array $data = []) {
        $data["konfigurasi"] = $this->model('Pengaturan_model')->getKonfigurasi();
		require_once 'app/views/'.$view.'.php';
	}

	public function model(string $model) {
		require_once 'app/models/'.$model.'.php';
		return new $model;
	}
}