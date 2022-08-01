<?php
//__NM____NM__FUNCTION__NM__//
	
//FUNÇÃO QUE BUSCA IMAGEM NA APO_LOGO 
function busca_logo() { 

//SELECT DE CONSULTA     
$vrv_SQL = "SELECT photo FROM employees"; 

//DISPARA CONSULTA     
sc_select(vrv_IMG, $vrv_SQL); 

//ISOLA OBJETO SUPRESSÃO DE ERROS     
$vrv_IMAGEM = $vrv_IMG->fields[0]; 

//MACRO DE CONVERSÃO DE IMAGENS  
$vrv_TEMP = nm_conv_img_access(substr($vrv_IMAGEM, 0, 12)); 
    
//VALIDAÇÃO DE PREFIXOS E SUFIXOS INJETADOS PELO SC (NM)     
if (substr($vrv_TEMP, 0, 4) == "*nm*") {
	
	$vrv_IMAGEM = nm_conv_img_access($vrv_IMAGEM); 
	
} 

if (substr($vrv_IMAGEM, 0, 4) == "*nm*") {
	
	$vrv_IMAGEM = substr($vrv_IMAGEM, 4);
	$vrv_IMAGEM = base64_decode($vrv_IMAGEM);
	
} 
    
$vrv_BM = strpos($vrv_IMAGEM, "BM"); 

if (!$vrv_BM === FALSE && $vrv_BM == 78) {
	
	$vrv_IMAGEM = substr($vrv_IMAGEM, $vrv_BM); 
	
}  

//RETORNO DA IMAGEM 
return $vrv_IMAGEM; 
     
} 

?>