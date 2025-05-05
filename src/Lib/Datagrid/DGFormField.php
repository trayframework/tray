<?php
namespace Tray\Lib\Datagrid;

class DGFormField {
	public $name = 'act';
	private $att = [
		"title"=>"dg_module", 
		"type"=>"textbox",
		"width"=>"360px",
		"req_type"=>"st",
		"readonly"=>false,
		"max_length"=>'255', 
		"default"=>""
	];
	function __construct($name,$arg = array()) {
		$this->name = $name;
		if(count($arg)>0) {
			$this->att = array_merge($this->att,$arg);
		}
	}
	function isRequire(bool $str) {
		if($str)	
		$this->att['req_type'] = "rt";
		else $this->att['req_type'] = "st";
	}
	function Param($arr) {
		if(count($arr)>0) {
			$this->att['param'] = $arr;
		}
	}
	function Set($arr) {
		if(count($arr)>0) {
			$this->att = array_merge($this->att,$arr);
		}
	}
	function Access(bool $access) {
		if($access) {
			$this->att['access'] = $access;
		}
	}
	function Get() {
		return $this->att;
	}
}