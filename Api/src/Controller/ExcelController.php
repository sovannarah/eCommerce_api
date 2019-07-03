<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpKernel\Kernel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Entity\User,
	App\Repository\UserRepository,
	App\Entity\UserOrder,
	App\Repository\UserOrderRepository,
	App\Entity\Article,
	App\Repository\ArticleRepository;

/**
 * @Route("/excel", name="excel")
 */
class ExcelController extends AbstractController
{
	/**
	 * @Route("/", name="excel")
	 */
	public function		index()
	{
		$publicDirectory = $this->getParameter('kernel.project_dir') . '/public';

		$spreadsheet = new Spreadsheet();
		//USER SHEET
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Users");
		self::fillUser($sheet);
		//ORDERS SHEET
		$sheet = $spreadsheet->createSheet();
		$sheet->setTitle("Orders");
		self::fillOrders($sheet);
		//ORDERS SHEET
		$sheet = $spreadsheet->createSheet();
		$sheet->setTitle("Articles");
		self::fillArticles($sheet);

		$writer = new Xlsx($spreadsheet);
		$excelFilepath =  $publicDirectory . '/CyrilCorpComputers.xlsx';
		$writer->save($excelFilepath);
		return $this->json("Excel generated succesfully", 201);
	}

	private function	fillUser (&$sheet)
	{
		$rUser = $this->getDoctrine()->getRepository(User::class)->findAll();
		$cellRow = 0;
		$cellCol = 1;

		foreach ($rUser as $user) {
			$cellCol = 1;
			$cellRow++;
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($user->getEmail());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($user->getRoles()[0]);
		}
		// self::autoSize($sheet);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
	}

	private function	fillOrders (&$sheet)
	{
		$rOrders = $this->getDoctrine()->getRepository(UserOrder::class)->findAll();
		$cellRow = 0;
		$cellCol = 1;

		foreach ($rOrders as $order) {
			$cellCol = 1;
			$cellRow++;
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($order->getUser()->getEmail());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($order->getSend());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue("Adresse");
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue("Montant TTC");
		}
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
	}

	private function	fillArticles (&$sheet)
	{
		$rArticle = $this->getDoctrine()->getRepository(Article::class)->findAll();
		$cellRow = 0;
		$cellCol = 1;

		foreach ($rArticle as $article) {
			$cellCol = 1;
			$cellRow++;
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($article->getCategory()->getName());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($article->getTitle());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($article->getDescription());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($article->getPrice());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($article->getStock());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($article->getNbViews());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($article->getKg());
		}
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		// die();
	}

	/* private function autoSize(&$sheet) {

	} */
}
