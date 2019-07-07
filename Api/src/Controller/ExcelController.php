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
	App\Repository\ArticleRepository,
	App\Entity\Category,
	App\Controller\CategoryController,
	App\Repository\CategoryRepository;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelController extends AbstractController
{
	/**
	 * @Route("/excel", name="excel", methods={"GET"})
	 */
	public function		index() {
		$fileName = "CyrilCorpComputers.xlsx";
		$sheets = [
			// ["title" => "Users", "fc" => "fillUser"],
			// ["title" => "UserOrders", "fc" => "fillOrders"],
			// ["title" => "Articles", "fc" => "fillArticles"],
			["title" => "Category", "fc" => "fillCategory"],
		];
		$activeSheet = true;
		
		$spreadsheet = new Spreadsheet();
		foreach ($sheets as $value) {
			$sheet = $activeSheet ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
			$activeSheet = false;
			$sheet->setTitle($value['title']);
			$sheet = $this->{$value['fc']}($sheet);
		}
		
		$writer = new Xlsx($spreadsheet);
		$publicDirectory = $this->getParameter('kernel.project_dir') . '/public';
		$excelFilepath =  $publicDirectory.'/'.$fileName;
		$writer->save($excelFilepath);
		return $this->json(["file" => $fileName], 201);
	}

	private function	fillUser ($sheet) {
		$headers = ["Email", "Role", "Address"];
		self::writeHeaders($sheet, $headers);

		$rUser = $this->getDoctrine()->getRepository(User::class)->findAll();
		$cellRow = 1;

		foreach ($rUser as $user) {
			$cellCol = 1;
			$cellRow++;
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($user->getEmail());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($user->getRoles()[0]);
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue(self::address($user->getAddress()));
		}
		$lastCol = chr(64+$cellCol);

		self::autoSize($sheet, range("A", $lastCol));
		return ($sheet);
	}

	private function	fillOrders ($sheet) {
		$headers = ["User email", "Sent at", "To address", "Price"];
		self::writeHeaders($sheet, $headers);
		
		$rOrders = $this->getDoctrine()->getRepository(UserOrder::class)->findAll();
		$cellRow = 1;
		
		foreach ($rOrders as $order) {
			$cellCol = 1;
			$cellRow++;
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($order->getUser()->getEmail());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($order->getSend());
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue("Adresse");
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($order->getPrice())
				->getStyle()
				->getNumberFormat()
				->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR);
		}
		$lastCol = chr(64+$cellCol);
		
		//total
		$form = "=SUM(D2:D".$cellRow.")";
		$sheet->getCellByColumnAndRow(1, ++$cellRow)->setValue("TOTAL");
		$sheet->getCellByColumnAndRow(4, $cellRow)->setValue($form)->getStyle()
			->getNumberFormat()
			->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR);

		//beautify
		$sheet->getStyle("A".$cellRow.":D".$cellRow)->getBorders()
			->getTop()
			->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)
			->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'));
		$sheet->getStyle("A".$cellRow.":D".$cellRow)->getFont()->setBold(true);

		self::autoSize($sheet, range("A", $lastCol));
		return ($sheet);
	}

	private function	fillArticles ($sheet) {
		$headers = ["Category", "Title", "Description", "Price", "In stock", "Nb views", "Kg"];
		self::writeHeaders($sheet, $headers);

		$rArticle = $this->getDoctrine()->getRepository(Article::class)->findAll();
		$cellRow = 1;

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
		$lastCol = chr(64+$cellCol);

		self::autoSize($sheet, range("A", $lastCol));
		return ($sheet);
	}

	private function	fillCategory ($sheet) {
		$headers = ["Name"];
		self::writeHeaders($sheet, $headers);
		
		$rCategory = $this->getDoctrine()->getRepository(Category::class);
		$rootCats = $rCategory->findBy(['parent' => null]);
		foreach ($rootCats as $rootCat)
			$cats[] = $rootCat->rec_nestedJsonSerialize();
		$cellRow = 1;
		dd($cats);
		foreach ($cats as $category) {
			$cellCol = 1;
			$cellRow++;
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($category['name']);
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($category['artCount']);
			if (count($category['sub']))
				$sheet = $this->recursiveCategory($sheet, $cellRow, $cellCol, $category['name'], $category['sub']);
		}
		$lastCol = chr(64+$cellCol);

		self::autoSize($sheet, range("A", $lastCol));
		return ($sheet);
	}


	private function	recursiveCategory($sheet, &$cellRow, $cellCol, $parent, $categories) {
		foreach ($categories as $category) {
			$cellRow++;
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($category['name']);
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue($category['artCount']);
			if (count($category['sub']))
				$this->recursiveCategory($sheet, $cellRow, $cellCol+1, $category['name'], $category['sub']);
		}
		return ($sheet);
	}

	private function	address($address) {
		if ($address)
			return $address->getStreet().", ".$address->getPc();
		else
			return "Undefined";
	}

	private function	autoSize(&$sheet, $range) {
		foreach ($range as $col)
			$sheet->getColumnDimension($col)->setAutoSize(true);
	}

	private function	writeHeaders(&$sheet, $headers) {
		foreach ($headers as $col => $title)
			$sheet->getCellByColumnAndRow($col+1, 1)->setValue($title);
		$sheet->getStyle("A1:".chr(64+count($headers))."1")
			->getFont()->setBold(true);
	}
}

/* 
BORDER STYLES:
	BORDER_NONE             = 'none';
	BORDER_DASHDOT          = 'dashDot';
	BORDER_DASHDOTDOT       = 'dashDotDot';
	BORDER_DASHED           = 'dashed';
	BORDER_DOTTED           = 'dotted';
	BORDER_DOUBLE           = 'double';
	BORDER_HAIR             = 'hair';
	BORDER_MEDIUM           = 'medium';
	BORDER_MEDIUMDASHDOT    = 'mediumDashDot';
	BORDER_MEDIUMDASHDOTDOT = 'mediumDashDotDot';
	BORDER_MEDIUMDASHED     = 'mediumDashed';
	BORDER_SLANTDASHDOT     = 'slantDashDot';
	BORDER_THICK            = 'thick';
	BORDER_THIN             = 'thin';
*/


/* $styleArray = array(
	'font' => array(
		'bold' => true,
		'italic' => true
	),
	'borders' => array(
		'outline' => array(
			'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
			'color' => array('argb' => 'FFFF0000'),
		),
		'top' => array(
			'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
			'color' => array(
				'rgb' => '808080'
			)
		)
	)
);
$sheet->getStyle('B3')->applyFromArray($styleArray); */

/* //USER SHEET
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
self::fillArticles($sheet); */
