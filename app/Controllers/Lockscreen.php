<?php

namespace App\Controllers;

class Lockscreen extends BaseController
{
	public function index()
	{
		$data = [
			'title' => 'Access denied',
		];
		session()->setFlashdata("warning", "This is error message");
		return view('lockscreen');
	}

	public function maintenance()
	{
		return view('maintenance2');
	}
}
