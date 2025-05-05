<?php
namespace Tray\Lib\Datagrid;

class DGForeignKey {
	public $name = 'act';
	private $att = [
		"table"=>"", 
		"field_key"=>"", 
		"field_name"=>"",
		"view_type"=>"dropdownlist", 
		"order_by_field"=>"",
		"order_type"=>"ASC",
		"where"=>"",
		"input"=>array()
	];
	function __construct($name,$type='lookup',$arg = array()) {
		$this->name = $name;
		if($type == 'lookup') {
			$LKObj = new DGLookupAtt($arg);
		
			$this->att = array_merge($this->att,$LKObj->get());
		}
		else {
			if(count($arg)>0) {
				$this->att = array_merge($this->att,$arg);
			}
		}
	}
	function Get() {
		
		return $this->att;
	}
}