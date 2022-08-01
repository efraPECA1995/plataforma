<?php
//__NM__Emails de envio ao criar licença, comum a mais de uma aplicação__NM__FUNCTION__NM__//
function email_agradecimento($param_licenca_nome_contato, $nome_vendedor, $celular_vendedor, $email_vendedor = 'comercial@scriptcase.com.br')
{

$licenca_nome_contato = $param_licenca_nome_contato;

sc_lookup(fone_rep,"SELECT
                       telefone_residencial
                    FROM
                       nm_clientes
                    WHERE
                       id_cliente = [var_id_rep]");
                    
$fone_representante = {fone_rep[0][0]};

if( strlen($fone_representante) <= 5 )
{
   $fone_representante = '(81) 3087-0300';
}


$str_email_agradecimento = "
<html><head><title>ScriptCase</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'></head>
<body><p align='center'><img src=\"". $this->Ini->root . $this->Ini->path_img_global ."/sys__NM__logo_SC.png\" width='244' height='45'></p><p align='center'><font color='#003366' size='3' face='Arial, Helvetica, sans-serif'>Caro(a) Sr(a). " . $licenca_nome_contato . "</font> </p><p><font color='#000066' size='2' face='Arial, Helvetica, sans-serif'> Agradecemos a Oportunidade que nos foi dada, quando da apresentação do software ScriptCase na sua empresa. Estamos disponibilizando uma cópia da ferramenta, para que você possa avaliar o ScriptCase da melhor maneira possível. Incluiremos em um email seguinte os dados da sua licença mais algumas dicas e informações úteis, sobre procedimentos de instalação e acesso aos canais de suporte.</font></p><p><font color='#000066' size='2' face='Arial, Helvetica, sans-serif'>Ficamos a disposição.<br><br>Cordialmente,<br>" . $nome_vendedor . "<br>Departamento Comercial<br>NetMake Informática Ltda<br>"
. $fone_representante . " / " . $celular_vendedor . "<br>www.scriptcase.com.br <br><br>obs.: Peço gentileza, confirmar o recebimento desta mensagem.</font></p></body></html>";

return $str_email_agradecimento;

}


function email_licenca_dicas($var_contato, $var_serial, $login, $senha, $celular_vendedor, $nome_vendedor, $email_vendedor = 'comercial@scriptcase.com.br')
{

sc_lookup(fone_rep,"SELECT
                       telefone_residencial
                    FROM
                       nm_clientes
                    WHERE
                       id_cliente = [var_id_rep]");
                    
$fone_representante = {fone_rep[0][0]};

if( strlen($fone_representante) <= 5 )
{
   $fone_representante = '(81) 3087-0300';
}


$str_email_licenca_dicas = "
<html>
<title>Guarde este email</title>
<body>
<p align=\"center\"><img src=\"". $this->Ini->root . $this->Ini->path_img_global ."/sys__NM__logo_SC.png\" width=\"244\" height=\"45\"></p>
<p><font color=\"#000066\" face=\"Arial, Helvetica, sans-serif\">Prezado(a) <font size=\"4\">$var_contato</font>.</font></p>
<p><font color=\"#000066\" face=\"Arial, Helvetica, sans-serif\"><br><font size=\"2\">Segue abaixo, o número serial da sua licença do ScriptCase, para avaliação: </font></font><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\">
<br>Serial: <font size=\"4\">$var_serial</font><br>
<br>Caso tenha dúvidas relativas ao ScriptCase (instalação, registro, uso), acesse o nosso <a href=\"http://www.netmake.com.br/site/support/support.php?install_problem=1\" target=\"_blank\">SUPORTE TÉCNICO</a>, pelo endereço: http://www.netmake.com.br/site/support/support.php?install_problem</font></p>
<p></p><p><font color=\"#000066\" size=\"4\" face=\"Arial, Helvetica, sans-serif\"><strong>Download:</strong></font></p>
<p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\"><a href=\"http://www.netmake.com.br/site/download/download.php\" target=\"_blank\">Faça o download do ScriptCase clicando aqui, ou pelo endereço</a>: http://www.netmake.com.br/site/download/download.php</font></p>
<p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\">São disponibilizadas três opções para download, instalador windows e arquivos fonte windows e linux.
<br>Recomendamos a opção INSTALADOR DO SCRIPTCASE, para windows, por ser automática e bastante rápida a instalação.</font></p>
<p><font color=\"#000066\" size=\"4\" face=\"Arial, Helvetica, sans-serif\"><strong>Registro da licença:</strong></font></p><p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\"><br>Após download e instalação, acesse o Módulo Configuração/Administração/Licenças.
<br>Informe o número serial de avaliação para o ScriptCase.
<br>Serial de Avaliação: <font size=\"4\">$var_serial</font></font></p>
<p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\">O registro do ScriptCase pode ser feito de 2 formas:</font></p>
<p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\">On-Line (via internet, mais rápida): clicar na opção Configuração / Administração / Licenças / Registro On-Line.
<br>Basta informar o serial da Licença e confirmar. </font></p>
<p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\">Obs.: Este processo pode não se concretizar, quando o usuário está logado num ambiente com restrições de firewall, sendo recomendável o processo Off-Line.
<br><br>Off-Line (troca de arquivos via email, prazo máximo 48h): clicar na opção Configuração / Administração / Licenças / Solicitação.
<br>Será gerado pelo ScriptCase um arquivo scriptcase.req o qual deverá ser enviado ao email licenca@netmake.com.br
<br>Em seguida, procederemos o registro e enviaremos ao seu email o arquivo scriptcase.lic, o qual deverá será carregado em Configuração / Administração / Licenças / Instalação.
<br></font></p>
<p><font color=\"#000066\" size=\"4\" face=\"Arial, Helvetica, sans-serif\"><strong>Dicas Uteis:</strong></font></p>
<p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\">1)Veja como conectar com o seu banco de dados.
<br>Tutorial de apoio a conexão com os principais bancos de dados, caso seu banco não esteja nesse material utilize o nosso suporte.</font></p>
<p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\"> Endereço: http://www.scriptcase.com.br/suporte/conexoes/</font></p>
<p></p>
<p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\">2)Aprenda você mesmo a usar o ScriptCase.
<br>Assista e faça download do curso básico de ScriptCase fazendo o download diretamente do nosso site.</font></p>
<p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\"> Endereço: http://www.scriptcase.com.br/cursos/</font></p>
<p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\">3)Complemente seu aprendizado com nossos exemplos.
<br>Exemplos demonstrando recursos do scriptcase acompanhados de vídeos explicativos.</font></p><p><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\"> Endereço: http://www.scriptcase.com.br/exemplos/
<br></font></p><p><font color=\"#000066\" size=\"4\" face=\"Arial, Helvetica, sans-serif\"><strong>Dúvidas comerciais:</strong></font>
<br><font color=\"#000066\" size=\"2\" face=\"Arial, Helvetica, sans-serif\">Email: ". $email_vendedor ." - Fones: " . $fone_representante . " / $celular_vendedor - $nome_vendedor</font>
<br></p>
</body>
</html>";

return $str_email_licenca_dicas;

}

?>