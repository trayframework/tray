<?php
namespace Tray\Lib\Datagrid;

class DGFormFields extends DGContainer {
	function __construct($FieldSelStr='')
	{
		parent::__construct();
		if($FieldSelStr != '') {
			$FieldSelList = explode(",",$FieldSelStr);
			if(is_array($FieldSelList)) {
				foreach($FieldSelList as $FieldName) {
					$this->add(new DGFormField(trim($FieldName),["title"=>trim($FieldName)]));
				}
			}
		}
	}
	public function Print() {
		echo '<div class="modal d-block pos-static">
		<div class="modal-dialog" role="document">
		  <div class="modal-content bd-0">
			<div class="modal-header pd-y-20 pd-x-25">
			  <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Preview</h6>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			<div class="modal-body pd-25">';
			
		foreach($this as $property) {
			echo '$DGFormSelObj->'.$property->name.'->Set(["title"=>"DESC_TITLE", "type"=>"textbox"]);<br />';
		}
		echo '</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-secondary pd-x-20">Close</button>
				</div>
			</div>
			</div>
		</div>';
	}
}