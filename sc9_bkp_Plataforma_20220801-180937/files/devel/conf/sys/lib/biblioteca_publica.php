<?php
//__NM__Biblioteca Publica__NM__FUNCTION__NM__//
function FormataHoras($segundos) {
   
   $horas = (int) ($segundos/3600);
   $min = (int) (($segundos % 3600) / 60);
   $seg = $segundos - (($horas * 3600) + ($min * 60));
   return sprintf('%02d:%02d:%02d',$horas,$min,$seg);   
}
function GeraSenhaAleatoria() {
  $CaracteresAceitos = 'abcdxywzABCDZYWZ0123456789';

  $max = strlen($CaracteresAceitos)-1;

  $password = null;

  for($i=0; $i < 8; $i++) {

   $password .= $CaracteresAceitos{mt_rand(0, $max)};

  }
  return $password;
}

function PrimeiroDiaMes() {
  $mes = date("m");
  $ano = date("Y");
  return mktime(0,0,0,$mes,1,$ano);
}
function UltimoDiaMes() {
  $mes = date("m");
  $ano = date("Y");
  $dia = date("t");
  return mktime(0,0,0,$mes,$dia,$ano);
}
function funarrayvenc($dtinicio,$dtfim,$dia,$intervalo) {
  if (strlen($dia) < 2) 
	  $dia = '0'.$dia; 
  $dt = $dtinicio;
  $vetvenc = array();
  $mes = substr($dt,4,2);
  $ano = substr($dt,0,4);
  $svenc = "$ano-$mes-$dia";
  $vetvenc[] = $svenc;
  $fim = substr($dtfim,0,4)."-".substr($dtfim,4,2)."-".substr($dtfim,6,2);
  
  while ($svenc < $fim) {
     switch($intervalo) {
       case "M":
          $mes = $mes+1;
          break;
       case "B":
          $mes = $mes+2;
          break;
       case "T":
          $mes = $mes+3;
          break; 
       case "S":
          $mes = $mes+6;
          break;
       case "A":
          $mes = $mes+12;
          break;
     }
     
     if ($mes > 12) {
         $ano++;
         $mes = $mes-12;
        }
        $svenc = "$ano-";
        if ($mes < 10)
            $svenc .= "0$mes-$dia";
        else
            $svenc .= "$mes-$dia";
        if ($svenc < $fim)
           $vetvenc[] = $svenc;
        else
           break;    
           
  }
  
  return $vetvenc;
}

function getPeriodoAtual() {
  $ano = date('Y');
  $mes = date('n');
  if ($mes < 7)
     $semestre = 1;
  else
     $semestre = 2;
  return $ano."-".$semestre;
}

function SubtraiPeriodo($periodo,$qtd) {
  $periodoatual = $periodo;
  for ($i = 1; $i <= $qtd; $i++) {
     $ano = substr($periodoatual,0,4);
     $semestre = substr($periodoatual,5,1);
     if ($semestre == 2)
        $semestre--;
     else {
        $ano--;
        $semestre = 2;
     }
     $periodoatual = $ano."-".$semestre;

  }
  return $periodoatual;
}

function SomaPeriodo($periodo,$qtd) {
  $periodoatual = $periodo;
  for ($i = 1; $i <= $qtd; $i++) {
     $ano = intval(substr($periodoatual,0,4));
     $semestre = intval(substr($periodoatual,5,1));

     if ($semestre == 1)
        $semestre++;
     else {
        $ano++;
        $semestre = 1;
     }
     $periodoatual = $ano."-".$semestre;

  }
  return $periodoatual;
}

function SomaAnoMes($anomes,$qtd) {
  $anomesatual = $anomes;
  
  for ($i = 1; $i <= $qtd; $i++) {
     $ano = intval(substr($anomesatual,0,4));
     $mes = intval(substr($anomesatual,4,2));
     $mes = intval($mes + 1);
     
     if ($mes > 12) {
        $ano++;
        $mes = 1;
     }   
     if ($mes < 10) 
        $smes = "0".$mes;
     else
        $smes = $mes;   
     $anomesatual = strval($ano).strval($smes);
     
  }
  return $anomesatual;
}

function textTodec($texto) {
   $aux = substr($texto,0,strlen($texto)-2).".".substr($texto,-2);
   return floatval($aux);
   
}

function MesExtenso($mes) {
  $nomemes = "";
  switch($mes) {
         case 1:
           $nomemes = "Janeiro";
           break; 
         case 2:
           $nomemes = "Fevereiro";
           break; 
         case 3:
           $nomemes = "Marco";
           break; 
         case 4:
           $nomemes = "Abril";
           break; 
         case 5:
           $nomemes = "Maio";
           break; 
         case 6:
           $nomemes = "Junho";
           break; 
         case 7:
           $nomemes = "Julho";
           break; 
         case 8:
           $nomemes = "Agosto";
           break; 
         case 9:
           $nomemes = "Setembro";
           break; 
         case 10:
           $nomemes = "Outubro";
           break; 
         case 11:
           $nomemes = "Novembro";
           break; 
         case 12:
           $nomemes = "Dezembro";
           break; 
       } 
       return $nomemes;
        
   }

  function funlinhabol_transporte($linha,$valor,$vencimento,$percjuros,$percmulta,$servico)  {
     
      $vlrmulta = $valor * $percmulta / 100;
	  $vlrjuros = $valor * $percjuros / 100;
	  $vlrjuros_dia = $vlrjuros / 30;
	  
      $txt = $linha;
	  
      $txt = str_replace('|%juros|',$percjuros,$txt);
	  $txt = str_replace('|%juros_dia|',number_format($vlrjuros_dia, 2, ',', ''),$txt);
      $txt = str_replace('|multa|',number_format($vlrmulta, 2, ',', ''),$txt);
      $txt = str_replace('|%multa|',$percmulta,$txt);
	  $txt = str_replace('|servico|',$servico,$txt);

	  return $txt;

  }

function smtpmailer($host,$user,$pass,$porta,$para, $de, $de_nome, $assunto, $corpo) {
      require_once($_SERVER['DOCUMENT_ROOT'].'/PHPMailer_5.2.1/class.phpmailer.php');
      global $error;
      $mail = new PHPMailer();
      $mail->IsSMTP();        // Ativar SMTP
      $mail->SMTPDebug = 0;        // Debugar: 1 = erros e mensagens, 2 = mensagens apenas
     $mail->SMTPAuth = true;        // Autenticação ativada
     $mail->SMTPSecure = 'ssl';    // SSL REQUERIDO pelo GMail
     $mail->Host = $host;    // SMTP utilizado
     $mail->Port = $porta;          // A porta 465 deverá estar aberta em seu servidor
     $mail->Username = $user;
     $mail->Password = $pass;
     $mail->SetFrom($de, $de_nome);
     $mail->Subject = $assunto;
     $mail->Body = $corpo;
     $mail->AddAddress($para);
     if(!$mail->Send()) {
         $error = 'Mail error: '.$mail->ErrorInfo;
         return false;
     } else {
         $error = 'Mensagem enviada!';
         return true;
     }
 } 
?>