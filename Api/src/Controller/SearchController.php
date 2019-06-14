<?php

namespace App\Controller;

use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
	public function index()
	{
		return $this->json([
			'message' => 'Welcome to your new controller!',
			'path' => 'src/Controller/SearchController.php',
		]);
	}
	/**
	 * @Route("/Search", name="search")
	 */
	public function     Search(Request $request)
	{

	}
}
