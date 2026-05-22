<?php


class App
{
	private string|object $class = "Home", $method = "index";
	private array $params = [];
	private array $protected = ['Dashboard', 'Profile', 'Koreksi', 'Monitoring', 'Banksoal', 'Hasilujian', 'Jurusan', 'Pengaturan', 'Pengguna', 'Pengumuman', 'Ujian', 'Ujiansiswa', 'Log'];

	public function __construct()
	{
		$url = $this->parseURL();
		$dir = 'app/controllers/';
		$halaman = ucfirst(strtolower($url[0]));


		if (file_exists($dir . $halaman . '.php')) {
			$this->class = $halaman;
			unset($url[0]);
		}

		$this->isMaintenance($this->class);
		
		$this->authentication($this->class);

		require_once $dir . $this->class . '.php';
		$this->class = new $this->class();

		if (isset($url[1])) {
			if (method_exists($this->class, $url[1])) {
				$this->method = $url[1];
				unset($url[1]);
			}
		}

		if ($url) {
			$this->params = array_values($url);
		}

		call_user_func_array([$this->class, $this->method], $this->params);
	}

	public function parseURL()
	{
		if (isset($_GET['url'])) {
			$url = $_GET['url'];
			$url = trim($url, '/');
			$url = filter_var($url, FILTER_SANITIZE_URL);
			$url = explode('/', $url);
			return $url;
		} else {
			return [$this->class];
		}
	}

	public function authentication(string $halaman)
	{
		if (!isset($_SESSION['user']) && in_array($halaman, $this->protected)) {
			Flasher::setFLash("Silahkan login terlebih dahulu", "error");
			header('location: ' . Constant::DIRNAME . 'login');
			exit;
		}

		if (isset($_SESSION['user']) && in_array($halaman, ["Login", "Register"])) {
			Flasher::setFLash("Silahkan logout terlebih dahulu", "error");
			header('location: ' . Constant::DIRNAME . 'dashboard');
			exit;
		}
	}

	public function isMaintenance(string $halaman)
	{
		$db = "app/models/";
		$class = "Pengaturan_model";

		require_once $db . $class . ".php";
		$model = new $class();

		$data = $model->getKonfigurasi();
		if($halaman != "Maintenance" && $data["maintenance"] == 1 && $_SESSION["user"]["role"] != "admin") {
			header('location: ' . Constant::DIRNAME . 'maintenance');
			exit;
		}
	}
}