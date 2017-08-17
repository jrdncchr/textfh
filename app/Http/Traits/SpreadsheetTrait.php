<?php

namespace App\Http\Traits;

use App\Contact;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

trait SpreadsheetTrait {

	public function bulkImport($path) 
	{
        $objPHPExcel = PHPExcel_IOFactory::load($path);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        $last_inserted = Contact::orderBy('id', 'desc')->first()->id;
        $id = $last_inserted == null ? 0 : $last_inserted;
        $id++;

        $duplicates = [];
        $contacts = array();
        for ($row = 1; $row <= $highestRow; ++ $row) {
            $contact = array();
            $contact['id'] = $id;
            $id++;
            $contact['row'] = $row;
            for ($col = 0; $col < $highestColumnIndex; ++$col) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $val = $cell->getValue();
                if (!$val) {
                    $val = '';
                }
                switch ($col) {
                    case 0 : $contact['name'] = $val; break;
                    case 1 : $contact['phone_no'] = $val; break;
                }
            }
            $contact['created_at'] = date('Y-m-d H:i:s');

            if (Contact::where('phone_no', '=', $contact['phone_no'])->count() > 0) {
			   	$duplicates[] = $contact;
			} else {
            	$contacts[] = $contact;
			}
        }
        
        $unique = array();
		foreach ($contacts as $contact) {
			$isDuplicate = false;
			foreach ($unique as $u) {
				if ($contact['phone_no'] == $u['phone_no']) {
					$contact['duplicate_of'] = $u['row'];
					$duplicates[] = $contact;
					$isDuplicate = true;
				}
			}
			if (!$isDuplicate) {
				$unique[] = $contact;
			}
		}

		$unique = array_filter($unique, function($k, $v) {
			unset($v['row']);
			return true;
		}, ARRAY_FILTER_USE_BOTH);

		$contacts = [];
		foreach ($unique as $u) {
			unset($u['row']);
			$contacts[] = $u;
		}

		Contact::insert($contacts);

		return [
			'inserts' => sizeof($contacts),
			'duplicates' => $duplicates
		];
	}

}