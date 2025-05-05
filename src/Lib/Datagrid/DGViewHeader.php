<?php
namespace Tray\Lib\Datagrid;

class DGViewHeader {
	//Type : label,image
	public $name = 'header';
	private $att = [
		'title'=>"Module",
		'type'=>"label",
		'css'=>"lalign",
		"on_js_event"=>"",
		"field_data"=>"",
		"field_data3"=>"",
		"target_path"=>"", 
		"default"=>'',
		"image_width"=>"",
		"image_height"=>"",
		"width"=>"",
	];
	function __construct($name,$arg = array()) {
		$this->name = $name;
		if(count($arg)>0) {
			$this->att = array_merge($this->att,$arg);
		}
	}
	function Get() {
		return $this->att;
	}
}