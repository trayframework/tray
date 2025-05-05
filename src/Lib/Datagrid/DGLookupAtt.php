<?php
namespace Tray\Lib\Datagrid;

class DGLookupAtt {
	private $att = [
		"table"=>"gp_sys_lookup_var", 
		"field_key"=>"cv_lookup_code", 
		"field_name"=>"cv_lookup_value",
		"view_type"=>"dropdownlist", 
		"order_by_field"=>"cv_lookup_value",
		"order_type"=>"ASC",
		"where"=>"cv_id_mst=10",
		"input"=>array()
	];
	function __construct($arg = array()) {
		$MstId = $arg['mst_id'];
		if($arg['where'] != "") {
			$where = ' AND '.$arg['where'];
		}
		
		$this->att["where"] = "cv_id_mst='".$MstId."'".$where;
	}
	function get() {
		return $this->att;
	}
}