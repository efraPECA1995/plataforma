<?php
//__NM__Aplica Tradução__NM__FUNCTION__NM__//
Function TraduzLabel($ling,$apl) {
 //$apl=$_SESSION['nm_session']['app']['cod'];
 //echo "$apl $ling";
 //Você pode aqui traduzir as mensagens de erro atribuindo as mesmas a um váriavel conforme o valor de $ling
 If ($ling=="PT-BR") $sotitulo=" AND (Label='@titulo')"; 
 else $sotitulo=""; //Lingua original dosistema não precisa retraduzir
 sc_select(ds, "SELECT Label,Label_Traduzido FROM Dicionario WHERE (Lingua = '$ling') AND (Aplicacao = '$apl') $sotitulo");
 if (FALSE === {ds}) {sc_erro_mensagem("Ocorreu um erro no acesso ao banco de dados(Dicionario).<BR>");}
 elseif (count({ds}) == 0) {sc_erro_mensagem("Não existe dicionário para essa língua.<BR>");}
 else {while (!$ds->EOF) {
                [xxLABEL]=strtolower($ds->fields[0]);
                $traduzido=$ds->fields[1];
 		If ([xxLABEL]=="@titulo") { [TITULOAPL]=$traduzido;}
		else { sc_label([xxLABEL])=$traduzido; }
                //echo "[xxLABEL]=$traduzido <br>";
		$ds->MoveNext();
         }
	$ds->Close();
      }
}
?>