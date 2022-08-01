<?php
//__NM____NM__FUNCTION__NM__//
	
function additens_menuhorizontal () {

	?>

	<link rel="stylesheet" type="text/css" href="<?php echo sc_url_library('sys', 'bootstrap', 'bootstrap.min.css'); ?>" />
	<script type="text/javascript" src="<?php echo sc_url_library('sys', 'bootstrap', 'bootstrap.min.js'); ?>">
	<script type="text/javascript" src="<?php echo sc_url_library('sys', 'bootstrap', 'jquery.min.js'); ?>">
		
	<ul class="nav navbar-nav navbar-right">
      <li><a href="#"><span class="glyphicon glyphicon-user"></span> Cadastrar </a></li>
      <li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Logar </a></li>
    </ul>

	<?php
	
}
?>