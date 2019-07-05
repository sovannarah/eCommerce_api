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

use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
		// return new BinaryFileResponse($excelFilepath);
		return $this->json(["file" => 'CyrilCorpComputers.xlsx']);
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
			$sheet->getCellByColumnAndRow($cellCol++, $cellRow)->setValue(self::fillAddress($user->getAddress()));
		}
		// self::autoSize($sheet);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
	}

	private function	fillOrders (&$sheet)
	{
		//headers
		$sheet->setCellValue('A1', 'User email')->setCellValue('B1', 'Sent at')
			->setCellValue('C1', 'To adress')->setCellValue('D1', 'Price');
		$sheet->getStyle("A1:D1")->getFont()->setBold(true);
		
		$rOrders = $this->getDoctrine()->getRepository(UserOrder::class)->findAll();
		$cellRow = 1;
		$cellCol = 1;
		
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
		
		//total
		$form = "=SUM(D2:D".$cellRow.")";
		$sheet->getCellByColumnAndRow(1, ++$cellRow)->setValue("TOTAL");
		$sheet->getCellByColumnAndRow(4, $cellRow)->setValue($form)->getStyle()
			->getNumberFormat()
			->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR);

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
		); */
		// $sheet->getStyle('B3')->applyFromArray($styleArray);

		$sheet->getStyle("A".$cellRow.":D".$cellRow)->getBorders()
			->getTop()
			->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)
			->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'));
		$sheet->getStyle("A".$cellRow.":D".$cellRow)->getFont()->setBold(true);

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

	private function fillAddress($address) {
		if ($address)
			return $address->getStreet().", ".$address->getPc();
		else
			return "Undefined";
	}

	/* private function autoSize(&$sheet) {

	} */
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
