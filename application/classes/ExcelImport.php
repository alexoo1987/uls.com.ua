<?php defined('SYSPATH') or die('No direct script access.');

class ExcelImport implements PHPExcel_Reader_IReadFilter
{
	private $_startRow = 0;
	private $_endRow = 0;

	public function setRows($startRow, $chunkSize) {
		$this->_startRow    = $startRow;
		$this->_endRow      = $startRow + $chunkSize;
	}

	public function readCell($column, $row, $worksheetName = '') {
		if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {
			return true;
		}
		return false;
	}
}


session_start();

if ($_SESSION['startRow']) $startRow = $_SESSION['startRow'];
else $startRow = 13;

$inputFileType = 'Excel5';
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$chunkSize = 20;
$chunkFilter = new ExcelImport();

while ($startRow <= 65000) {
	$chunkFilter->setRows($startRow,$chunkSize);
	$objReader->setReadFilter($chunkFilter);
	$objReader->setReadDataOnly(true);
	$objPHPExcel = $objReader->load($fileName);
	//Что-то с этими строками делаем
	$startRow += $chunkSize;
	$_SESSION['startRow'] = $startRow;

	unset($objReader);

	unset($objPHPExcel);

}

echo "The End";
unset($_SESSION['startRow']);