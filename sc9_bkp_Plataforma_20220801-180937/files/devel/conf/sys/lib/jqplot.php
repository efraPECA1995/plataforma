<?php
//__NM__jqPlot php classes helper__NM__FUNCTION__NM__//
class jqPlot {
	public $data = array();
	public $series = array();
	public $seriesDefaults;
	public $axes;
	function __construct() {
		$this->seriesDefaults = new stdClass();
		$this->axes = new stdClass();
	}
}
?>