<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class RedirectingController extends AbstractController
{
	/**
	 * @Route("/{url}/", name="redirecting")
	 * @param Request $request
	 * @return RedirectResponse
	 */
    public function redirectTrailingSlash(Request $request): RedirectResponse
	{
		$pathInfo = $request->getPathInfo();
		$requestUri = $request->getRequestUri();

		$url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

		return $this->redirect($url, 308);
    }
}
