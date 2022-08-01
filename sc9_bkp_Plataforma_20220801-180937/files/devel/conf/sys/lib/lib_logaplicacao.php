<?php
//__NM__Log Aplica��o__NM__NFUNCTION__NM__//
//__NM____NM__NFUNCTION__NM__//
/*
///////////////////////////////////////////////////////////////////////
//Desenvolvido por Line Brasil Inform�tica
//Consultor: Wilson Barbosa
//Data/Hora da vers�o: 13/10/07 - 02:50h
//
//Instru��es: ''detalhes importantes''
// - Para formul�rio do tipo MULTIPLOS REGISTROS no LOG ser�o apresentados os registros EXCLU�DOS/ALTERADOS simult�neamente.
//   Os registros que foram exclu�dos ser�o apresentados com um fundo na cor VERMELHA.
//
// - Para o perfeito funcionamento desta LIB, o desenvolvedor dever� ter uma aten��o especial �s
//   in�meras manupila��es poss�veis durante o desenvolvimento das aplica��es.
//   Regra GERAL: s� ser�o armazenados valores atribu�dos aos campos das aplica��es atrav�s do padr�o
//   {NOME_DO_CAMPO}
//
//   Como exemplo, observe o c�digo abaixo. Este c�digo foi extra�do de uma aplica��o do tipo formul�rio.
//   Note que esta aplica��o tem particularidades. O INSERT est� sendo acionado atrav�s do evento OnCall.
//
//   ============ In�cio do c�digo =========================
//LINHA1: if([varnew] == "0"){
//LINHA2:   sc_lookup(rstcodigo,"SELECT MAX(CodiHora) FROM TbHorar WHERE CodiHora< 9996");
//LINHA3:   if(empty({rstcodigo})){
//LINHA4:     $codhornew = 1;
//LINHA5:   }else{
//LINHA6:     $codhornew = {rstcodigo[0][0]}+1;
//LINHA7:   }
//LINHA8:   {CodiHora}=$codhornew;
//LINHA9:   $_SESSION['ativarlog']="INSERT";
//LINHA10:  sc_include("lib_logaplicacao.php","grp");
//LINHA11:  sc_exec_sql("insert into TbHorar(CodiHora,DescHora,TurnHora,JornHora)
//LINHA12:               values($codhornew,'{DescHora}','{TurnHora}','{JornHora}')");
//LINHA13:  $varcodhor = $codhornew;
//LINHA14:  [varnew] = "1";
//LINHA15: }
//   ============ Final do c�digo =========================
//
// Note que a LINHA8 seria DISPENS�VEL, uma vez que o campo n�o faz parte do FORM e ter� seu valor
// atribu�do atrav�s da vari�vel "$codhornew"
// Por�m, para o PERFEITO FUNCIONAMENTO DO LOG, � NECESS�RIO incluir a LINHA8 no c�digo deste evento OnCall.
// Note tamb�m que est� atribui��o DEVER� OCORRER NECESSARIAMENTE antes da chamada � LINHA10.
// Se isto n�o for feito, o conte�do do LOG para o campo {CodiHora} ser� = '-vazio-'
//
//Obs:
// 1) a vari�vel de sess�o 'ativarlog' dever� ser setada nos formul�rios de controle, ANTES DE CADA CHAMADA � macro sc_exec_sql(), respons�veis por incluir/alterar/excluir dados em tabelas
// 2) a vari�vel de sess�o 'ativarlog' tamb�m dever� ser setada em consultas (ou formul�rios/controles), para gravar a opera��o 'browser', ANTES DE CADA CHAMADA � sc_include("logaplicacao.php","grp"), SEMPRE NO EVENTO OnInit ou OnLoad
//   Automaticamente, o include se encarregar� de destruir a vari�vel de sess�o ap�s gravar o LOG do tipo BROWSER. N�o � necess�rio manipul�-la.
// 3) Eventos v�lidos para chamar esta LIB: (nos demais eventos o ScriptCase N�O INTERPRETA a macro sc_include())
//    - Consultas: A Cada Registro, Antes do Select, Quebra, OnCall
//    - Formul�rios: OnLoad, OnSubmit, OnCall
//    - Controle: OnLoad, OnSubmit, OnCall
// 4) S� ser�o gravados os LOGs dos campos da aplica��o que est� sendo executada, desde que estejam cadastrados na tabela TbCampoAplic. Ser�o apresentados de acordo com o campo "ordem" informado nesta tabela (ordena��o ASC)
// 5) Campos do tipo FOTO n�o poder�o existir na tabela TbCampoAplic; Caso contr�rio ocorrer� um erro na hora de gravar/exibir o LOG.
//
// Exemplos de chamadas � lib:
// a) Para gravar a opera��o BROWSER nos formul�rios:
//    - No evento OnLoad: (S� funciona em formul�rios '�nico Registro')
//      $_SESSION['ativarlog']='BROWSER';
//      sc_include("lib_logaplicacao.php","pub");//
//    Obs: A opera��o BROWSER n�o armazena o conte�do do registro visualizado. Apenas informa a data/hora/usu�rio/IP de acesso.
// b) Para gravar as opera��es UPDATE/DELETE/INSERT nos formul�rios ('�nico Registro ou M�ltiplis Registros'):
//        UPDATE: insira no evento OnAfterUpdate a chamada � LIB atrav�s da macro sc_include:
//               sc_include("lib_logaplicacao.php","grp");
//        DELETE: insira no evento OnAfterDelete a chamada � LIB atrav�s da macro sc_include:
//               sc_include("lib_logaplicacao.php","grp");
//        INSERT: insira no evento OnAfterInsert a chamada � LIB atrav�s da macro sc_include:
//               sc_include("lib_logaplicacao.php","grp");
//    Obs: n�o se preocupe! Nos casos das aplica��es do tipo formul�rio, a LIB identifica AUTOMATICAMENTE os conte�dos dos campos independentemente se fazem parte da aplica��o ou se ter�o seus valores atribu�dos no BD
//    Dica 1: Se N�O QUISER que o LOG seja gravado, por exemplo, na opera��o INSERT, basta n�o incluir no evento OnAfterInsert a chamada � include lib_logaplicacao.
// c) Para gravar a opera��o BROWSER nas consultas:
//    - No evento "OnInit":
//      $_SESSION['ativarlog']='BROWSER';
//      sc_include("lib_logaplicacao.php","pub");
// d) Em formul�rios de CONTROLE, para gravar opera��o de INSERT, DELETE ou UPDATE:
//    - SEMPRE no evento OnLoad
//      - Exemplo INSERT:
//        $_SESSION['ativarlog']='INSERT';
//        sc_include("lib_logaplicacao.php","pub");
//        sc_exec_sql("insert into tabela (cpo1,cpo2) values ($cpo1,$cpo2)");
//      - Exemplo UPDATE:
//        $_SESSION['ativarlog']='UPDATE';
//        sc_include("lib_logaplicacao.php","pub");
//        sc_exec_sql("update tabela set cpo1='$cpo1',cpo2='$cpo2' where chave=1);
//      - Exemplo DELETE:
//        $_SESSION['ativarlog']='DELETE';
//        sc_include("lib_logaplicacao.php","pub");
//        sc_exec_sql("delete from tabela where chave=1);
///////////////////////////////////////////////////////////////////////
*/

//OUTPUT DE DEPURA��O
//if (isset($_SESSION['ativarlog'])){
//   print "<script>window.alert('".$_SESSION['ativarlog']."');</script>";
//}else{
//   print "<script>window.alert('Vari�vel de Sess�o N�O DEFINIDA');</script>";
//}

//OUTPUT DE DEPURA��O
//print "<pre>";
//if (isset($_SESSION['ativarlog'])){
//   var_dump($_SESSION['ativarlog']);
//}
//var_dump($_REQUEST);
//print "</pre>";


//OUTPUT DE DEPURA��O
//if (sc_after_insert){
//   print "<BR>EVENTO AP�S INCLUS�O<BR>";
//}
//if (sc_after_update){
//   print "<BR>EVENTO AP�S ALTERA��O<BR>";
//}
//if (sc_after_delete){
//   print "<BR>EVENTO AP�S EXCLUS�O<BR>";
//}
//var_dump($_REQUEST);

if (sc_after_insert || sc_after_update || sc_after_delete || isset($_SESSION['ativarlog'])){
//verifica a existencia da vari�vel de sess�o 'ativarlog'
  if (isset($_SESSION['ativarlog'])){
     $_SESSION['ativarlog']=strtoupper($_SESSION['ativarlog']);
     $conteudo_valido="INSERT, UPDATE, DELETE, BROWSER";
     $pos = strpos($conteudo_valido, $_SESSION['ativarlog']);
     if ($pos === false) {
        print "<script>window.alert('Opera��o CANCELADA!\\nPar�metros v�lidos para LOG: $conteudo_valido');</script>";
        die;
     }
  }

//variavel para controle da n�o exibi��o dos labels dos campos no caso de formul�rio multiplo registro
  static $vez;
  $vez++;

//vari�vel para controle das mensagens de erro
  $erro='N';
  $msg_alerta='';

//DEFINE A OPERA��O
  if (sc_after_insert || (isset($_SESSION['ativarlog']) && $_SESSION['ativarlog']=='INSERT')){
     $operacao='INSERT';
  }elseif (sc_after_update || (isset($_SESSION['ativarlog']) && $_SESSION['ativarlog']=='UPDATE')){
     $operacao='UPDATE';
  }elseif (sc_after_delete || (isset($_SESSION['ativarlog']) && $_SESSION['ativarlog']=='DELETE')){
     $operacao='DELETE';
  }else{
     $operacao='BROWSER';
  }

//identifica o IP do usu�rio LOGADO
  $varip=$_SERVER['REMOTE_ADDR'];

//identifica a aplica��o que est� sendo executada
  $aplicacao=substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],"/")+1,strlen($_SERVER['PHP_SELF']));
  $aplicacao=str_replace(".php","",$aplicacao);

//OUTPUT DE DEPURA��O
//var_dump($aplicacao);

//define o owner do DB, se houver.
//  $db_owner="dbo.";
  $db_owner="";


//encontra o ID da aplica��o que est� sendo executada
//  $sql="SELECT IdAplic FROM ".$db_owner."TbAplic WHERE NomeApli= '$aplicacao'";
  $sql="SELECT apliid FROM ".$db_owner."tblaplicativo WHERE ApliNome = '$aplicacao'";
  sc_lookup(rstaplicacao,"$sql");
  if(!empty({rstaplicacao})){
     $id_aplic = {rstaplicacao[0][0]};
  }else{ //assume que n�o � para gravar LOG, ao inv�s de gerar uma msg de erro
     $erro='S';
     $msg_alerta.='Falha na grava��o do LOG: Aplica��o".$aplicacao."n�o cadastrada\\n';
  }

//encontra os labels para os campos
//N�o estranhe a 'repeti��o' nos campos da select. Devido a um BUG do ScriptCase a coluna 0 �s vezes retorna '0' (dependendo da onde � chamado) o que resultaria num erro grave para a LIB
  $sql="SELECT Campo, Label,Campo, Label FROM  ".$db_owner."TblCampoAplic  WHERE (ApliId = '$id_aplic') ORDER BY Ordem";

//OUTPUT DE DEPURA��O
//var_dump($sql);

  sc_lookup(rstcampos,"$sql");
  $array_campos=array();
  $array_labels=array();
  if (!empty({rstcampos})){
     $limite=sizeof({rstcampos});
     $campos=array();
     for ($i=0;$i<$limite;$i++){
        $array_campos[$i]={rstcampos[$i][2]};
        $array_labels[$i]={rstcampos[$i][3]};
     }

//OUTPUT DE DEPURA��O
//print "<pre>";
//var_dump($array_campos);
//print "</pre>";
//var_dump($array_labels);

  }else{
     $erro='S';
     $msg_alerta.='Falha na grava��o do LOG: dicion�rio de dados n�o cadastrado\\n';
  }

//comp�e o conte�do a ser gravado no campo LogText
  if ($vez==1){ //so monta os nomes das colunas se for a primeira vez.
     $_SESSION['txt_to_log']="<table width=100% border=1>";
     $_SESSION['txt_to_log'].="<tr>";
     $limite=sizeof($array_labels);
     for ($a=0;$a<$limite;$a++){
         $_SESSION['txt_to_log'].="<td bgcolor=#046467><FONT size=2 color=#FFFFFF face=Tahoma, Arial, sans-serif><B>".$array_labels[$a]."</B></FONT></td>";
     }
     $_SESSION['txt_to_log'].="</tr>";
  }

//inicializa a variavel responsavel por identificar o �ltimo elemento do formulario multiplos registros (o n�mero de registros a serem exibidos de cada vez)
  $ultimo_elemento=0;

//checa se o formul�rio � do tipo m�ltiplos registros. Por seguran�a, testa se TODOS os campos tiveram o sequencial adicionado ao nome automaticamente pelo ScriptCase
  if (isset($_REQUEST["sc_contr_vert"])){
     $multiplos_registros='S';
     if (sc_after_insert){
        $ultimo_elemento=sizeof($_REQUEST['sc_check_vert']);
     }elseif(sc_after_update || sc_after_delete){
        $ultimo_elemento=$_REQUEST["sc_contr_vert"]-1; //versao anterior � v152
     }
  }else{
     $ultimo_elemento=1;
     $multiplos_registros='N';
  }
//OUTPUT DE DEPURA��O
//print "=======>$ultimo_elemento => $multiplos_registros<br>";
//die;

//antes da v152.
//No caso de INCLUSAO de multiplos registros, a vari�vel precisa ser redefinida para grava��o correta
//  if ($multiplos_registros=='S' && $ultimo_elemento==0 && sc_before_insert){ 
//     $ultimo_elemento=sizeof($_REQUEST['sc_check_vert']);
//  }

//OUTPUT DE DEPURA��O
//print "<pre>";
//var_dump($_REQUEST);
//print "</pre>";
//die;


//OUTPUT DE DEPURA��O
//var_dump($multiplos_registros);
//var_dump($ultimo_elemento);


//detecta a necessidade de auto_defini��o das vari�veis $_REQUEST 
//Necess�rio, pois na V3 a gera��o de varsxconte�do � din�mica e aleat�ria na edi��o de FORM UNICO REGISTRO
if ($multiplos_registros=='N'){
   //atribui os conte�dos dos campso do form aos campos do LOG
   for ($xyz=0;$xyz<sizeof($array_campos);$xyz++){
        $nome_var=$array_campos[$xyz];
        $_REQUEST[$nome_var]=$this->$nome_var;
   }
//OUTPUT DE DEPURA��O
//print "<pre>";
//var_dump($_REQUEST);
//print "</pre>";
//die;
}else{
   //atribui os conte�dos dos campos do form aos campos do LOG
   for ($xyz=0;$xyz<sizeof($array_campos);$xyz++){
        $nome_var=$array_campos[$xyz];
        $nome_var_seq=$nome_var.$vez;
        //se o campo faz parte do form, resgata o valor e atribui ao campo do LOG
        if (isset($this->$nome_var_seq)){
           $_REQUEST[$nome_var_seq]=$this->$nome_var_seq;
        }else{ //caso contr�rio, identifica o "valor no banco de dados" definido na aplica��o
           $_REQUEST[$nome_var_seq]=$this->$nome_var;
        }
   }
//OUTPUT DE DEPURA��O
//   print "<pre>";
//   var_dump($_REQUEST);
//   print "</pre>";
}


  $limite=sizeof($array_campos);
  if ($multiplos_registros=='S'){ //Se for MULTIPLOS REGISTROS
     if ($operacao!="BROWSER"){ //se o LOG for para INCLUS�O/ALTERA��O/EXCLUS�O
        $_SESSION['txt_to_log'].="<tr>";
        for ($a=0;$a<$limite;$a++){
            $indice=$array_campos[$a].$vez;
            $indice=strtolower($indice);
//OUTPUT DE DEPURA��O
//print "$indice: $_REQUEST[$indice]<br>";
            if(sc_after_delete){
              if (array_key_exists($indice, $_REQUEST)){ //verifica o conte�do do campo do registro que acabou de ser deletado
                 $_SESSION['txt_to_log'].="<td bgcolor=red><FONT size=2 color=#004080 face=Tahoma, Arial, sans-serif>".$_REQUEST[$indice]."</FONT></td>";
              }
            }elseif(sc_after_update){
               if (array_key_exists($indice, $_REQUEST)){ //verifica se o conte�do do campo foi enviado pelo formul�rio
                  if (strlen(trim($_REQUEST[$indice]))==0 || strtoupper($_REQUEST[$indice])=="NULL" || $_REQUEST[$indice]==null || $_REQUEST[$indice]==NULL){
                     if (isset($$indice)){ //se o campo n�o estiver selecionado para a aplica��o (um campo chave sendo incrementado via vari�vel, por exemplo), atribuir� o valor associado ao campo (vide instru��es: ''detalhes importantes'')
                        $_REQUEST[$indice]=$$indice;
                     }else{
                        $_REQUEST[$indice]="-vazio-"; //corrige a omiss�o da coluna na hora de montar a <TD> do campo
                     }
                  }
                  $_SESSION['txt_to_log'].="<td><FONT size=2 color=#004080 face=Tahoma, Arial, sans-serif>".$_REQUEST[$indice]."</FONT></td>";
               }
            }elseif(sc_after_insert){
               if ($_REQUEST['sc_check_vert'][$vez]==$vez){ //na inclus�o, vetor inicializa em 1
                  $_SESSION['txt_to_log'].="<td><FONT size=2 color=#004080 face=Tahoma, Arial, sans-serif>".$_REQUEST[$indice]."</FONT></td>";
               }
            }
        }
        $_SESSION['txt_to_log'].="</tr>";
     }else{ //Se o LOG for para REGISTROS ACESSADOS
        $_SESSION['txt_to_log'].="<tr>";
        for ($a=0;$a<$limite;$a++){
            $indice=$array_campos[$a].$vez;
            $indice=strtolower($indice); //verifica se a vari�vel existe antes de atribuir o conte�do
            if (isset($indice)){
               $conteudo=$$indice;
               if (strlen(trim($conteudo))==0 || strtoupper($conteudo)=="NULL" || strtoupper($conteudo)==NULL || strtoupper($conteudo)==null){
                  $conteudo="-vazio-"; //corrige a omiss�o da coluna na hora de montar a <TD> do campo
               }
               $_SESSION['txt_to_log'].="<td><FONT size=2 color=#004080 face=Tahoma, Arial, sans-serif>".$conteudo."</FONT></td>";
            }
        }
        $_SESSION['txt_to_log'].="</tr>";
     }
  }else{ //Se for UNICO REGISTRO
     if ($operacao!="BROWSER"){ //se o LOG for para INCLUS�O/ALTERA��O/EXCLUS�O
        $_SESSION['txt_to_log'].="<tr>";
        for ($a=0;$a<$limite;$a++){
            $indice=strtolower($array_campos[$a]);

//OUTPUT DE DEPURA��O
//print "$indice: $_REQUEST[$indice]<br>";

            if (array_key_exists($indice, $_REQUEST)){ //verifica se o conte�do do campo foi enviado pelo formul�rio
               if (strlen(trim($_REQUEST[$indice]))==0 || strtoupper($_REQUEST[$indice])=="NULL" || $_REQUEST[$indice]==null || $_REQUEST[$indice]==NULL){
                  if (isset($$indice)){ //se o campo n�o estiver selecionado para a aplica��o (um campo chave sendo incrementado via vari�vel, por exemplo), atribuir� o valor associado ao campo (vide instru��es: ''detalhes importantes'')
                     $_REQUEST[$indice]=$$indice;
                  }else{
                     $_REQUEST[$indice]="-vazio-"; //corrige a omiss�o da coluna na hora de montar a <TD> do campo
                  }
               }
               if (sc_after_delete){
                  $cor='bgcolor=red';
               }else{
                  $cor='';
               }
               $_SESSION['txt_to_log'].="<td $cor><FONT size=2 color=#004080 face=Tahoma, Arial, sans-serif>".$_REQUEST[$indice]."</FONT></td>";
            }
        }
        $_SESSION['txt_to_log'].="</tr>";
     }else{ //Se o LOG for para REGISTROS ACESSADOS
        $_SESSION['txt_to_log'].="<tr>";
        for ($a=0;$a<$limite;$a++){
           $indice=$array_campos[$a];
           $indice=strtolower($indice);
           if (isset($$indice)){ //verifica se a vari�vel existe antes de atribuir o conte�do
              $conteudo=$$indice;
              if (strlen(trim($conteudo))==0 || strtoupper($conteudo)=="NULL" || strtoupper($conteudo)==NULL || strtoupper($conteudo)==null){
                  $conteudo="-vazio-"; //corrige a omiss�o da coluna na hora de montar a <TD> do campo
               }
               $_SESSION['txt_to_log'].="<td><FONT size=2 color=#004080 face=Tahoma, Arial, sans-serif>".$conteudo."</FONT></td>";
           }
        }
        $_SESSION['txt_to_log'].="</tr>";
     }
  }

//OUTPUT DE DEPURA��O
//var_dump($_SESSION['txt_to_log']);
//die;



//  if([sc_glo_tpbanco] == "oci805"){
//     sc_lookup(rstdata,"select to_char(current_timestamp,'YYYY-MM-DD HH24:MI:SS') from dual");
//  }else{
//     sc_lookup(rstdata,"select current_timestamp");
//  }
//  $vl_datalog = strftime("%Y-%m-%d",strtotime({rstdata[0][0]}));
//  $vl_horalog = strftime("%H",strtotime({rstdata[0][0]}))*60 + strftime("%M",strtotime({rstdata[0][0]}));
    $vl_datalog=date("Y-m-d");
    $vl_horalog=date("H:i:s");

//  $sql="select idusuar from tbusuar where logiusua = '".[varlogin]."'";
//  sc_lookup(rstusuario,$sql);

//  if(!empty({rstusuario})){
//    $vl_idusuar = {rstusuario[0][0]};
//  }else{
//     $erro='S';
//     $msg_alerta.='Falha na grava��o do LOG: imposs�vel identificar o usu�rio\\n';
//  }

    //$vl_idusuar=[varlogin];
    $vl_idusuar = [var_usucod];
    $vl_idusuar = $_SESSION['var_usucod'];
    
//OUTPUT DE DEPURA��O
//print "<br>==> VEZ: $vez ------ ULTIMO: $ultimo_elemento<br>";
//print "<br>==> erro: $erro ------ Multiplos: $multiplos_registros<br>";

  if ($erro=='N' && $multiplos_registros=='N' || $erro=='N' && $multiplos_registros=='S' && $vez==$ultimo_elemento){
     $_SESSION['txt_to_log'].="</table>";
     if ($operacao!="BROWSER"){ // grava o conte�do dos campos
        $txt=$_SESSION['txt_to_log'];
     }else{ //N�o grava o conte�do dos campos
        $txt='';
     }
//OUTPUT DE DEPURA��O
//print "<br>txt(".strlen(trim($txt))."): $txt <br>";
     if (substr($txt,1,5)=="table" || strlen(trim($txt))==0){ //NECESS�RIO PARA IMPEDIR GRAVAR DUAS VEZES QUANDO FOI SOLICITADO GRAVAR LOG do evento BORWSER (onload) + INSERT/UPDATE/DELETE (Onsubmit)
        $sql_log="insert into ".$db_owner."TblLogAplic (DataLog,HoraLog,IdUsuar,OperLog,TabeLog,ApliId,LogText,Ip)
                        VALUES
                        ('$vl_datalog','$vl_horalog','$vl_idusuar','$operacao','','$id_aplic','$txt','$varip')";

//OUTPUT DE DEPURA��O
//print $sql_log;
//die;
        sc_exec_sql("$sql_log");
        sc_commit_trans();
     }
     if (isset($_SESSION['ativarlog'])){ //destr�i a vari�vel de sess�o
        unset($_SESSION['ativarlog']);
     }
     if (isset($_SESSION['txt_to_log'])){ //limpa a vari�vel de sess�o respons�vel por armazenar o conte�do dos campos no LOG
        $_SESSION['txt_to_log']='';
     }
  }else{ //se n�o encontrou dados necess�rios para a tabela LOG, exibe msg de alerta e n�o grava o LOG
     if ($erro=='S'){
        print "<script>window.alert('$msg_alerta');</script>";
     }
  }
}
?>