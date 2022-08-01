<?php
//__NM__Funcao para envio de email com anexo__NM__FUNCTION__NM__//
function enviar_email($mail_smtp, $mail_usuario="", $mail_senha="", $mail_de, $mail_para, $mail_assunto, $mail_msg_texto="", $mail_msg_html="", $mail_cc="", $mail_bcc="", $mail_imagem="")
{

include_once($this->Ini->path_third . "/email/smtp.class.inc");

$mail_msg_texto = empty($mail_msg_texto) ? "Mensagem em texto nao informada." : $mail_msg_texto;

$mail_mensagem = 
"This is a multi-part message in MIME format.

--%boundary_main%
Content-Type: text/plain; charset=iso-8859-1; format=flowed
Content-Transfer-Encoding: 8bit

$mail_msg_texto

--%boundary_main%
Content-Type: multipart/related;
	boundary=\"%boundary_texto%\"

--%boundary_texto%
Content-Type: text/html; charset=iso-8859-1
Content-Transfer-Encoding: 7bit

$mail_msg_html

--%boundary_texto%
$mail_imagem";




$boundary_main		 = "=_NM_MAIL_" . md5('mail'  . date("YmdHis"));
$boundary_texto		 = "=_NM_MAIL_" . md5('mime'  . date("YmdHis"));
$mail_mensagem		 = str_replace("%boundary_main%", $boundary_main, $mail_mensagem);
$mail_mensagem		 = str_replace("%boundary_texto%", $boundary_texto, $mail_mensagem);


$nm_mail		 = new SMTP($mail_smtp, $mail_usuario, $mail_senha);
$header			 = @$nm_mail->make_header($mail_de, $mail_para, $mail_assunto, "3", $mail_cc, $mail_bcc);
$header			.= "Reply-To: $mail_de \r\n";
$header			.= "MIME-Version: 1.0 \r\n";
$header			.= "Content-Type: multipart/alternative; \r\n";
$header			.= "	boundary=\"". $boundary_main ."\" \r\n";

@$nm_mail->smtp_send($mail_de, $mail_para, $header, $mail_mensagem, $mail_cc, $mail_bcc);

}
?>