<?php
//__NM__Log Aplicação__NM__NFUNCTION__NM__//
//__NM____NM__NFUNCTION__NM__//
/*
///////////////////////////////////////////////////////////////////////
//Desenvolvido por Line Brasil Informática
//Consultor: Wilson Barbosa
//Data/Hora da versão: 13/10/07 - 02:50h
//
//Instruções: ''detalhes importantes''
// - Para formulário do tipo MULTIPLOS REGISTROS no LOG serão apresentados os registros EXCLUÍDOS/ALTERADOS simultâneamente.
//   Os registros que foram excluídos serão apresentados com um fundo na cor VERMELHA.
//
// - Para o perfeito funcionamento desta LIB, o desenvolvedor deverá ter uma atenção especial às
//   inúmeras manupilações possíveis durante o desenvolvimento das aplicações.
//   Regra GERAL: só serão armazenados valores atribuídos aos campos das aplicações através do padrão
//   {NOME_DO_CAMPO}
//
//   Como exemplo, observe o código abaixo. Este código foi extraído de uma aplicação do tipo formulário.
//   Note que esta aplicação tem particularidades. O INSERT está sendo acionado através do evento OnCall.
//
//   ============ Início do código =========================
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
//   ============ Final do código =========================
//
// Note que a LINHA8 seria DISPENSÁVEL, uma vez que o campo não faz parte do FORM e terá seu valor
// atribuído através da variável "$codhornew"
// Porém, para o PERFEITO FUNCIONAMENTO DO LOG, É NECESSÁRIO incluir a LINHA8 no código deste evento OnCall.
// Note também que está atribuição DEVERÁ OCORRER NECESSARIAMENTE antes da chamada à LINHA10.
// Se isto não for feito, o conteúdo do LOG para o campo {CodiHora} será = '-vazio-'
//
//Obs:
// 1) a variável de sessão 'ativarlog' deverá ser setada nos formulários de controle, ANTES DE CADA CHAMADA à macro sc_exec_sql(), responsáveis por incluir/alterar/excluir dados em tabelas
// 2) a variável de sessão 'ativarlog' também deverá ser setada em consultas (ou formulários/controles), para gravar a operação 'browser', ANTES DE CADA CHAMADA à sc_include("logaplicacao.php","grp"), SEMPRE NO EVENTO OnInit ou OnLoad
//   Automaticamente, o include se encarregará de destruir a variável de sessão após gravar o LOG do tipo BROWSER. Não é necessário manipulá-la.
// 3) Eventos válidos para chamar esta LIB: (nos demais eventos o ScriptCase NÃO INTERPRETA a macro sc_include())
//    - Consultas: A Cada Registro, Antes do Select, Quebra, OnCall
//    - Formulários: OnLoad, OnSubmit, OnCall
//    - Controle: OnLoad, OnSubmit, OnCall
// 4) Só serão gravados os LOGs dos campos da aplicação que está sendo executada, desde que estejam cadastrados na tabela TbCampoAplic. Serão apresentados de acordo com o campo "ordem" informado nesta tabela (ordenação ASC)
// 5) Campos do tipo FOTO não poderão existir na tabela TbCampoAplic; Caso contrário ocorrerá um erro na hora de gravar/exibir o LOG.
//
// Exemplos de chamadas à lib:
// a) Para gravar a operação BROWSER nos formulários:
//    - No evento OnLoad: (Só funciona em formulários 'Único Registro')
//      $_SESSION['ativarlog']='BROWSER';
//      sc_include("lib_logaplicacao.php","pub");//
//    Obs: A operação BROWSER não armazena o conteúdo do registro visualizado. Apenas informa a data/hora/usuário/IP de acesso.
// b) Para gravar as operações UPDATE/DELETE/INSERT nos formulários ('Único Registro ou Múltiplis Registros'):
//        UPDATE: insira no evento OnAfterUpdate a chamada à LIB através da macro sc_include:
//               sc_include("lib_logaplicacao.php","grp");
//        DELETE: insira no evento OnAfterDelete a chamada à LIB através da macro sc_include:
//               sc_include("lib_logaplicacao.php","grp");
//        INSERT: insira no evento OnAfterInsert a chamada à LIB através da macro sc_include:
//               sc_include("lib_logaplicacao.php","grp");
//    Obs: não se preocupe! Nos casos das aplicações do tipo formulário, a LIB identifica AUTOMATICAMENTE os conteúdos dos campos independentemente se fazem parte da aplicação ou se terão seus valores atribuídos no BD
//    Dica 1: Se NÃO QUISER que o LOG seja gravado, por exemplo, na operação INSERT, basta não incluir no evento OnAfterInsert a chamada à include lib_logaplicacao.
// c) Para gravar a operação BROWSER nas consultas:
//    - No evento "OnInit":
//      $_SESSION['ativarlog']='BROWSER';
//      sc_include("lib_logaplicacao.php","pub");
// d) Em formulários de CONTROLE, para gravar operação de INSERT, DELETE ou UPDATE:
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

//OUTPUT DE DEPURAÇÃO
//if (isset($_SESSION['ativarlog'])){
//   print "<script>window.alert('".$_SESSION['ativarlog']."');</script>";
//}else{
//   print "<script>window.alert('Variável de Sessão NÃO DEFINIDA');</script>";
//}

//OUTPUT DE DEPURAÇÃO
//print "<pre>";
//if (isset($_SESSION['ativarlog'])){
//   var_dump($_SESSION['ativarlog']);
//}
//var_dump($_REQUEST);
//print "</pre>";


//OUTPUT DE DEPURAÇÃO
//if (sc_after_insert){
//   print "<BR>EVENTO APÓS INCLUSÃO<BR>";
//}
//if (sc_after_update){
//   print "<BR>EVENTO APÓS ALTERAÇÃO<BR>";
//}
//if (sc_after_delete){
//   print "<BR>EVENTO APÓS EXCLUSÃO<BR>";
//}
//var_dump($_REQUEST);

if (sc_after_insert || sc_after_update || sc_after_delete || isset($_SESSION['ativarlog'])){
//verifica a existencia da variável de sessão 'ativarlog'
  if (isset($_SESSION['ativarlog'])){
     $_SESSION['ativarlog']=strtoupper($_SESSION['ativarlog']);
     $conteudo_valido="INSERT, UPDATE, DELETE, BROWSER";
     $pos = strpos($conteudo_valido, $_SESSION['ativarlog']);
     if ($pos === false) {
        print "<script>window.alert('Operação CANCELADA!\\nParâmetros válidos para LOG: $conteudo_valido');</script>";
        die;
     }
  }

//variavel para controle da não exibição dos labels dos campos no caso de formulário multiplo registro
  static $vez;
  $vez++;

//variável para controle das mensagens de erro
  $erro='N';
  $msg_alerta='';

//DEFINE A OPERAÇÃO
  if (sc_after_insert || (isset($_SESSION['ativarlog']) && $_SESSION['ativarlog']=='INSERT')){
     $operacao='INSERT';
  }elseif (sc_after_update || (isset($_SESSION['ativarlog']) && $_SESSION['ativarlog']=='UPDATE')){
     $operacao='UPDATE';
  }elseif (sc_after_delete || (isset($_SESSION['ativarlog']) && $_SESSION['ativarlog']=='DELETE')){
     $operacao='DELETE';
  }else{
     $operacao='BROWSER';
  }

//identifica o IP do usuário LOGADO
  $varip=$_SERVER['REMOTE_ADDR'];

//identifica a aplicação que está sendo executada
  $aplicacao=substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],"/")+1,strlen($_SERVER['PHP_SELF']));
  $aplicacao=str_replace(".php","",$aplicacao);

//OUTPUT DE DEPURAÇÃO
//var_dump($aplicacao);

//define o owner do DB, se houver.
//  $db_owner="dbo.";
  $db_owner="";


//encontra o ID da aplicação que está sendo executada
//  $sql="SELECT IdAplic FROM ".$db_owner."TbAplic WHERE NomeApli= '$aplicacao'";
  $sql="SELECT apliid FROM ".$db_owner."tblaplicativo WHERE ApliNome = '$aplicacao'";
  sc_lookup(rstaplicacao,"$sql");
  if(!empty({rstaplicacao})){
     $id_aplic = {rstaplicacao[0][0]};
  }else{ //assume que não é para gravar LOG, ao invés de gerar uma msg de erro
     $erro='S';
     $msg_alerta.='Falha na gravação do LOG: Aplicação".$aplicacao."não cadastrada\\n';
  }

//encontra os labels para os campos
//Não estranhe a 'repetição' nos campos da select. Devido a um BUG do ScriptCase a coluna 0 às vezes retorna '0' (dependendo da onde é chamado) o que resultaria num erro grave para a LIB
  $sql="SELECT Campo, Label,Campo, Label FROM  ".$db_owner."TblCampoAplic  WHERE (ApliId = '$id_aplic') ORDER BY Ordem";

//OUTPUT DE DEPURAÇÃO
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

//OUTPUT DE DEPURAÇÃO
//print "<pre>";
//var_dump($array_campos);
//print "</pre>";
//var_dump($array_labels);

  }else{
     $erro='S';
     $msg_alerta.='Falha na gravação do LOG: dicionário de dados não cadastrado\\n';
  }

//compõe o conteúdo a ser gravado no campo LogText
  if ($vez==1){ //so monta os nomes das colunas se for a primeira vez.
     $_SESSION['txt_to_log']="<table width=100% border=1>";
     $_SESSION['txt_to_log'].="<tr>";
     $limite=sizeof($array_labels);
     for ($a=0;$a<$limite;$a++){
         $_SESSION['txt_to_log'].="<td bgcolor=#046467><FONT size=2 color=#FFFFFF face=Tahoma, Arial, sans-serif><B>".$array_labels[$a]."</B></FONT></td>";
     }
     $_SESSION['txt_to_log'].="</tr>";
  }

//inicializa a variavel responsavel por identificar o último elemento do formulario multiplos registros (o número de registros a serem exibidos de cada vez)
  $ultimo_elemento=0;

//checa se o formulário é do tipo múltiplos registros. Por segurança, testa se TODOS os campos tiveram o sequencial adicionado ao nome automaticamente pelo ScriptCase
  if (isset($_REQUEST["sc_contr_vert"])){
     $multiplos_registros='S';
     if (sc_after_insert){
        $ultimo_elemento=sizeof($_REQUEST['sc_check_vert']);
     }elseif(sc_after_update || sc_after_delete){
        $ultimo_elemento=$_REQUEST["sc_contr_vert"]-1; //versao anterior à v152
     }
  }else{
     $ultimo_elemento=1;
     $multiplos_registros='N';
  }
//OUTPUT DE DEPURAÇÃO
//print "=======>$ultimo_elemento => $multiplos_registros<br>";
//die;

//antes da v152.
//No caso de INCLUSAO de multiplos registros, a variável precisa ser redefinida para gravação correta
//  if ($multiplos_registros=='S' && $ultimo_elemento==0 && sc_before_insert){ 
//     $ultimo_elemento=sizeof($_REQUEST['sc_check_vert']);
//  }

//OUTPUT DE DEPURAÇÃO
//print "<pre>";
//var_dump($_REQUEST);
//print "</pre>";
//die;


//OUTPUT DE DEPURAÇÃO
//var_dump($multiplos_registros);
//var_dump($ultimo_elemento);


//detecta a necessidade de auto_definição das variáveis $_REQUEST 
//Necessário, pois na V3 a geração de varsxconteúdo é dinâmica e aleatória na edição de FORM UNICO REGISTRO
if ($multiplos_registros=='N'){
   //atribui os conteúdos dos campso do form aos campos do LOG
   for ($xyz=0;$xyz<sizeof($array_campos);$xyz++){
        $nome_var=$array_campos[$xyz];
        $_REQUEST[$nome_var]=$this->$nome_var;
   }
//OUTPUT DE DEPURAÇÃO
//print "<pre>";
//var_dump($_REQUEST);
//print "</pre>";
//die;
}else{
   //atribui os conteúdos dos campos do form aos campos do LOG
   for ($xyz=0;$xyz<sizeof($array_campos);$xyz++){
        $nome_var=$array_campos[$xyz];
        $nome_var_seq=$nome_var.$vez;
        //se o campo faz parte do form, resgata o valor e atribui ao campo do LOG
        if (isset($this->$nome_var_seq)){
           $_REQUEST[$nome_var_seq]=$this->$nome_var_seq;
        }else{ //caso contrário, identifica o "valor no banco de dados" definido na aplicação
           $_REQUEST[$nome_var_seq]=$this->$nome_var;
        }
   }
//OUTPUT DE DEPURAÇÃO
//   print "<pre>";
//   var_dump($_REQUEST);
//   print "</pre>";
}


  $limite=sizeof($array_campos);
  if ($multiplos_registros=='S'){ //Se for MULTIPLOS REGISTROS
     if ($operacao!="BROWSER"){ //se o LOG for para INCLUSÃO/ALTERAÇÃO/EXCLUSÃO
        $_SESSION['txt_to_log'].="<tr>";
        for ($a=0;$a<$limite;$a++){
            $indice=$array_campos[$a].$vez;
            $indice=strtolower($indice);
//OUTPUT DE DEPURAÇÃO
//print "$indice: $_REQUEST[$indice]<br>";
            if(sc_after_delete){
              if (array_key_exists($indice, $_REQUEST)){ //verifica o conteúdo do campo do registro que acabou de ser deletado
                 $_SESSION['txt_to_log'].="<td bgcolor=red><FONT size=2 color=#004080 face=Tahoma, Arial, sans-serif>".$_REQUEST[$indice]."</FONT></td>";
              }
            }elseif(sc_after_update){
               if (array_key_exists($indice, $_REQUEST)){ //verifica se o conteúdo do campo foi enviado pelo formulário
                  if (strlen(trim($_REQUEST[$indice]))==0 || strtoupper($_REQUEST[$indice])=="NULL" || $_REQUEST[$indice]==null || $_REQUEST[$indice]==NULL){
                     if (isset($$indice)){ //se o campo não estiver selecionado para a aplicação (um campo chave sendo incrementado via variável, por exemplo), atribuirá o valor associado ao campo (vide instruções: ''detalhes importantes'')
                        $_REQUEST[$indice]=$$indice;
                     }else{
                        $_REQUEST[$indice]="-vazio-"; //corrige a omissão da coluna na hora de montar a <TD> do campo
                     }
                  }
                  $_SESSION['txt_to_log'].="<td><FONT size=2 color=#004080 face=Tahoma, Arial, sans-serif>".$_REQUEST[$indice]."</FONT></td>";
               }
            }elseif(sc_after_insert){
               if ($_REQUEST['sc_check_vert'][$vez]==$vez){ //na inclusão, vetor inicializa em 1
                  $_SESSION['txt_to_log'].="<td><FONT size=2 color=#004080 face=Tahoma, Arial, sans-serif>".$_REQUEST[$indice]."</FONT></td>";
               }
            }
        }
        $_SESSION['txt_to_log'].="</tr>";
     }else{ //Se o LOG for para REGISTROS ACESSADOS
        $_SESSION['txt_to_log'].="<tr>";
        for ($a=0;$a<$limite;$a++){
            $indice=$array_campos[$a].$vez;
            $indice=strtolower($indice); //verifica se a variável existe antes de atribuir o conteúdo
            if (isset($indice)){
               $conteudo=$$indice;
               if (strlen(trim($conteudo))==0 || strtoupper($conteudo)=="NULL" || strtoupper($conteudo)==NULL || strtoupper($conteudo)==null){
                  $conteudo="-vazio-"; //corrige a omissão da coluna na hora de montar a <TD> do campo
               }
               $_SESSION['txt_to_log'].="<td><FONT size=2 color=#004080 face=Tahoma, Arial, sans-serif>".$conteudo."</FONT></td>";
            }
        }
        $_SESSION['txt_to_log'].="</tr>";
     }
  }else{ //Se for UNICO REGISTRO
     if ($operacao!="BROWSER"){ //se o LOG for para INCLUSÃO/ALTERAÇÃO/EXCLUSÃO
        $_SESSION['txt_to_log'].="<tr>";
        for ($a=0;$a<$limite;$a++){
            $indice=strtolower($array_campos[$a]);

//OUTPUT DE DEPURAÇÃO
//print "$indice: $_REQUEST[$indice]<br>";

            if (array_key_exists($indice, $_REQUEST)){ //verifica se o conteúdo do campo foi enviado pelo formulário
               if (strlen(trim($_REQUEST[$indice]))==0 || strtoupper($_REQUEST[$indice])=="NULL" || $_REQUEST[$indice]==null || $_REQUEST[$indice]==NULL){
                  if (isset($$indice)){ //se o campo não estiver selecionado para a aplicação (um campo chave sendo incrementado via variável, por exemplo), atribuirá o valor associado ao campo (vide instruções: ''detalhes importantes'')
                     $_REQUEST[$indice]=$$indice;
                  }else{
                     $_REQUEST[$indice]="-vazio-"; //corrige a omissão da coluna na hora de montar a <TD> do campo
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
           if (isset($$indice)){ //verifica se a variável existe antes de atribuir o conteúdo
              $conteudo=$$indice;
              if (strlen(trim($conteudo))==0 || strtoupper($conteudo)=="NULL" || strtoupper($conteudo)==NULL || strtoupper($conteudo)==null){
                  $conteudo="-vazio-"; //corrige a omissão da coluna na hora de montar a <TD> do campo
               }
               $_SESSION['txt_to_log'].="<td><FONT size=2 color=#004080 face=Tahoma, Arial, sans-serif>".$conteudo."</FONT></td>";
           }
        }
        $_SESSION['txt_to_log'].="</tr>";
     }
  }

//OUTPUT DE DEPURAÇÃO
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
//     $msg_alerta.='Falha na gravação do LOG: impossível identificar o usuário\\n';
//  }

    //$vl_idusuar=[varlogin];
    $vl_idusuar = [var_usucod];
    $vl_idusuar = $_SESSION['var_usucod'];
    
//OUTPUT DE DEPURAÇÃO
//print "<br>==> VEZ: $vez ------ ULTIMO: $ultimo_elemento<br>";
//print "<br>==> erro: $erro ------ Multiplos: $multiplos_registros<br>";

  if ($erro=='N' && $multiplos_registros=='N' || $erro=='N' && $multiplos_registros=='S' && $vez==$ultimo_elemento){
     $_SESSION['txt_to_log'].="</table>";
     if ($operacao!="BROWSER"){ // grava o conteúdo dos campos
        $txt=$_SESSION['txt_to_log'];
     }else{ //Não grava o conteúdo dos campos
        $txt='';
     }
//OUTPUT DE DEPURAÇÃO
//print "<br>txt(".strlen(trim($txt))."): $txt <br>";
     if (substr($txt,1,5)=="table" || strlen(trim($txt))==0){ //NECESSÁRIO PARA IMPEDIR GRAVAR DUAS VEZES QUANDO FOI SOLICITADO GRAVAR LOG do evento BORWSER (onload) + INSERT/UPDATE/DELETE (Onsubmit)
        $sql_log="insert into ".$db_owner."TblLogAplic (DataLog,HoraLog,IdUsuar,OperLog,TabeLog,ApliId,LogText,Ip)
                        VALUES
                        ('$vl_datalog','$vl_horalog','$vl_idusuar','$operacao','','$id_aplic','$txt','$varip')";

//OUTPUT DE DEPURAÇÃO
//print $sql_log;
//die;
        sc_exec_sql("$sql_log");
        sc_commit_trans();
     }
     if (isset($_SESSION['ativarlog'])){ //destrói a variável de sessão
        unset($_SESSION['ativarlog']);
     }
     if (isset($_SESSION['txt_to_log'])){ //limpa a variável de sessão responsável por armazenar o conteúdo dos campos no LOG
        $_SESSION['txt_to_log']='';
     }
  }else{ //se não encontrou dados necessários para a tabela LOG, exibe msg de alerta e não grava o LOG
     if ($erro=='S'){
        print "<script>window.alert('$msg_alerta');</script>";
     }
  }
}
?>