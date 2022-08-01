<?php
//__NM__Grava logs de auditoria__NM__FUNCTION__NM__//
function grava_log($aplicacao, $usuario, $acao){
	$ip = $_SERVER['REMOTE_ADDR'];
	sc_exec_sql("insert into seg_auditoria (
			aplicacao, acao, fk_id_seg_usuarios,
			ip_usuario)
		values ('$aplicacao', '$acao', '$usuario',
		 '$ip')");
		}
?>