<?php
namespace Tray\Core;
abstract class BaseModel {
	protected $ModelName	= null;
	public    $DB			= null;
	public function __construct($thisClass,&$db) {
		$this->ModelName 	  = substr(get_class($thisClass), 0, stripos(get_class($thisClass), 'Model'));
		$this->DB			  = $db;
		$this->ResultAs('BOTH');
	}
	public function ResultAs($type) {
		if($type == 'NUM') {
			$this->DB->SetFetchMode(ADODB_FETCH_NUM);
		}
		elseif($type == 'ASSOC') {
			$this->DB->SetFetchMode(ADODB_FETCH_ASSOC);
		}
		elseif($type == 'BOTH') {
			$this->DB->SetFetchMode(ADODB_FETCH_BOTH);
		}
		//else $this->DB->SetFetchMode(ADODB_FETCH_BOTH);
		
	}
	public function QueryTotalRow($sql,$input) {
		$stmt 	 = $this->DB->Prepare($sql);
		$rs 	 = $this->DB->Execute($stmt, $input);
		if($rs) {
			return $rs->RecordCount();
		}
		else return 0;
	}
	public function getAsOptionArray($arr,$key,$val) {
		$newArr = array();
		if(is_array($arr) && count($arr)>0) {
			foreach ($arr as $item) {
				$newArr[$item[$key]] = $item[$val];
			}
		}
		return $newArr;
	}
	function RebuildDBArray($MyDataArray) {
		$CF_Arr = [];
		if(count($MyDataArray) > 0) {
			$ArrKeys  = array_keys($MyDataArray);
			$ArrVal   = array_values($MyDataArray[$ArrKeys[0]]);
			$TotalVal = count($ArrVal);
			foreach($ArrKeys as $field) {
				for($i=0;$i<$TotalVal;$i++) {
				$CF_Arr[$i][$field] = $MyDataArray[$field][$i];
				
				}
			}
			
		}
		return $CF_Arr;
	}
	function RebuildUIArray($MyArray) {
		$UI_Arr = [];
		if(count($MyArray) > 0) {
			$ArrKeys  = array_keys($MyArray);
			$TotalVal = count($ArrKeys);
			for($i=0;$i<$TotalVal;$i++) {
				foreach($MyArray[$i] as $field=>$fval) {
					$UI_Arr[$field][$i] = $fval;
				}
			}
		}
		return $UI_Arr;
	}
	public function GetCSVData($Filename,$isIndex=true) {
		if($isIndex) {
			$csv = array_map('str_getcsv', file($Filename, FILE_SKIP_EMPTY_LINES));
			unset($csv[0]);
		}
		else {
			$file = fopen($Filename, 'r');
			// Headers
			$headers = fgetcsv($file);
			// Rows
			$csv = [];
			while (($row = fgetcsv($file)) !== false)
			{
				$item = [];
				foreach ($row as $key => $value)
					$item[$headers[$key]] = $value ?: null;
				$csv[] = $item;
			}
			fclose($file);
		}
		return $csv;
	}
}
?>