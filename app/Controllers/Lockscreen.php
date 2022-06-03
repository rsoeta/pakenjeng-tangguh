<?php

namespace App\Controllers;

class Lockscreen extends BaseController
{
	public function index()
	{
		$data = [
			'title' => 'Access denied',
		];
		return view('lockscreen', $data);
	}

	public function maintenance()
	{
		return view('maintenance');
	}
}
