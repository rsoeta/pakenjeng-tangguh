<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

// Import model yang diperlukan
use App\Models\Dtks\AuthModel;
use App\Models\GenModel;

class BaseController extends Controller
{
	protected $AuthModel;
	protected $GenModel;

	/**
	 * Instance of the main Request object.
	 *
	 * @var IncomingRequest|CLIRequest
	 */
	protected $request;

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation.
	 *
	 * @var array
	 */
	protected $helpers = ['date', 'form', 'url', 'opdtks', 'dtsen_helper'];

	/**
	 * Constructor.
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		$this->AuthModel = new AuthModel();
		$this->GenModel  = new GenModel();

		// Ambil data global
		$user_login  = $this->AuthModel->getUserId() ?? ['fullname' => 'Guest', 'user_image' => 'default.png'];
		$statusRole  = $this->GenModel->getStatusRole() ?? [];

		// ✅ Simpan ke shared data untuk semua view
		$renderer = service('renderer');
		$renderer->setVar('user_login', $user_login);
		$renderer->setVar('statusRole', $statusRole);
	}

	protected function showError($message, $title = 'Terjadi Kesalahan')
	{
		return view('errors/html/error_general', [
			'title'   => $title,
			'message' => $message
		]);
	}

	protected function render(string $view, array $data = [])
	{
		if (! isset($data['title'])) {
			$data['title'] = '';
		}

		return view($view, $data);
	}
	// Optional helper render agar bisa include variabel user_login otomatis
	// protected function render($view, $data = [])
	// {
	// 	$data['user_login'] = $this->AuthModel->getUserId();
	// 	$data['statusRole'] = $this->GenModel->getStatusRole();
	// 	return view($view, $data);
	// }

}
