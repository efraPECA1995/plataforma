<?php
//__NM__Algemene functies__NM__FUNCTION__NM__//

     
     function GEN_VulUrenGetal($uren_code)
     {
         $pos = strpos($uren_code, 'U') ;
         if ($pos == true) 
         {
             $uren_getal = substr($uren_code, 0, $pos) ;
             $uren_getal = str_replace(",",".", $uren_getal) ;
         }
         else
         {
             $uren_getal = 0 ;
         }

         return $uren_getal ;
     }
     
     function GEN_OpenModules($role)
     {
         // set accessablity items
         sc_reset_apl_conf();
         
         $check_sql = "SELECT SC_MODID from SC_APPS_ACCESS WHERE SC_APPID='MUTFORMPERS' AND ".
                      "SC_ROLEID='$role' AND SC_ACCESS='Y'";
         sc_select(genrs, $check_sql);
         while(!$genrs->EOF)
         {
           // unpack data
           $mods = explode(';', $genrs->fields[0]);
             
           // speed! (enable all modules in the list)
           $max = sizeof($mods);
           for ($i=0; $i<$max; $i++) {     
              sc_apl_status ($mods[$i], 'on');
           }
           $genrs->MoveNext();
         }
         $genrs->Close();
     }    
     
     function GEN_StartRole($role)
     {
         // spring naar de gewenste module ivm. rol na aanloggen.    

         switch($role) 
         {
           case 'MG':
               // test of de persoon wel als mutatiemaker is geregistreerd.
               $check_sql = "SELECT RUG_EMPLID_HRA FROM SCRIPTCASE.PS_RUG_HRA_PRESELECT WHERE ".
                            "RUG_EMPLID_PRE = [glob_user] and RUG_MUTEERDER = 'J'" ;
 //                           "RUG_EMPLID_PRE='".[glob_user] . "'";
               sc_lookup(genrs, $check_sql);
         
               if (isset({genrs[0][0]}))     // Row found
                {
                  // Werk potentiele delegatie tabel bij
                  AddDelegateOptions([glob_user], 'MUTFORMPERS', $role);
                  // start menu manager
 //                 sc_redir('menu_mg.php','','_parent');
                }
               else     // No row found
                {
                  sc_error_message('Foutieve rol, aanloggen mislukt. Neem contact op met uw HR-Adviseur.');
                }
               break;
           case 'AD':
               // test of persoon adviseur is.
               $check_sql = "SELECT RUG_NAME_FORMAL_HRA FROM PS_RUG_HRA "
                         . " WHERE RUG_EMPLID_HRA = '" . [glob_user] . "' and ACTIVE = 'Y'";
               sc_lookup(genrs, $check_sql);
         
               if (isset({genrs[0][0]}))     // Row found
                {
                  // Werk potentiele delegatie tabel bij
                  AddDelegateOptions([glob_user], 'MUTFORMPERS', $role);
                  // start menu adviseur
 //                 sc_redir('menu_ad.php','','_parent');
                }
                 else     // No row found
               {
                  sc_error_message('Foutieve rol, aanloggen mislukt / Failing role, login failed.');
               }
             
               break;
           case 'CT':
               // Werk potentiele delegatie tabel bij
               AddDelegateOptions([glob_user], 'MUTFORMPERS', $role);
 //              sc_redir('menu_ct.php','','_parent');
               break;
           case 'AM':
               // test of persoon administrateur is.
               $check_sql = "SELECT RUG_NAME_FORMAL_ADMIN FROM PS_RUG_ADMIN "
                         . " WHERE RUG_EMPLID_ADMIN = '" . [glob_user] . "' and ACTIVE = 'Y'";
               sc_lookup(genrs, $check_sql);
         
               if (isset({genrs[0][0]}))     // Row found
                {
                  // Werk potentiele delegatie tabel bij
                  AddDelegateOptions([glob_user], 'MUTFORMPERS', $role);
                  // start menu adviseur
 //                 sc_redir('menu_am.php','','_parent');
                }
                 else     // No row found
               {
                  sc_error_message('Foutieve rol, aanloggen mislukt / Failing role, login failed.');
               }
             
               break;
            
          default:
             sc_error_message('error in role, contact support');
         }
     }    
         
     function GEN_uploaddocs($mutnr)
     {
         $select_sql = "SELECT UPLOADFORM FROM MUTF_UPLOADFORMS WHERE MUTNR = '$mutnr'" ;
         sc_select(genrs, $select_sql) ;    
         while(!$genrs->EOF)
         {
             [glob_formulieren] = [glob_formulieren].$genrs->fields[0].'<br>' ;
             $genrs->MoveNext();
         }
         $genrs->Close() ;
     }
     
     function GEN_UserAccess($uid, $pwd)
     {
         //connect to server using ldap bind
		 $firstchar = strtoupper(substr($uid,0,1)) ;
		 if ($firstchar == 'P')
		 {
		 	$ldaprdn  = "cn=".$uid.",ou=staff,o=rug,c=nl";     // ldap rdn or dP114397,ou=staff,o=rug,c=nl";     // ldap rdn or dn
		 }
		 else
		 {
		    $ldaprdn  = "cn=".$uid.",ou=student,o=rug,c=nl";     // ldap rdn or dP114397,ou=staff,o=rug,c=nl";     // ldap rdn or dn
		 }
         $ldappass = $pwd;  // associated password
  
         //connect to ldap server
         $ldapconn = ldap_connect("ldaps://sv1.id.rug.nl:636")
             or die("Could not connect to LDAP server.");
          
         if (ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3)) {
           //echo "Using LDAPv3\n";
         }

         $stusyspass = GEN_GetStuurWaarde(999) ;
         $ussyspass  = md5($ldappass) ;
         //ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
         if ($ldapconn) 
         {
             //check if in production database
             $check_sql = "select ora_database_name from dual" ;
             sc_lookup(genrs, $check_sql);
             //this statement always succeeds sinc eit is oracle unless there is something really really wrong then it fails a few lines further anyway
             $DBNAME = {genrs[0][0]};
             //if we are debugging then allow any login with the password letmeinNOW! PIFPRD* connections are on production        
             if (substr($DBNAME,0,6) != 'PIFPRD')
             {
                 if ($ussyspass == $stusyspass or $ldappass == 'x') 
                 {
                     $ldapbind = true ;
                 }
                 else
                 {
                     // binding to ldap server    
                     $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
                 }
             }
             else
             {        //pifprd access so ONLY allow the backdoor or the right password
                 if ($ussyspass == $stusyspass) 
                 {
                     GEN_WriteLog($uid, 'DIGIFORM', 'DIGIFORM', 'Inloggen als systemuser', 'Inloggen als systemuser') ;
                     $ldapbind = true ;
                 }
                 else
                 {
                     // binding to ldap server    
                     $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
                 }
             }
             // verify binding
             if ($ldapbind) 
             {
                 //echo "LDAP bind successful...\n";
             } 
             else 
             {
                 //sc_error_message('LDAP bind failed...: '.ldap_error($ldapconn) . "\n");
                 sc_error_message('<br><br><b>Login mislukt / login failed</b>');
             }
         }
     }

     function GEN_SystemBlocked()
     {
         if (sc_get_language() == 'nl')
         {
             $stuurmelding = GEN_GetStuurWaarde(13) ;
         }
         else
         {
             $stuurmelding = GEN_GetStuurWaarde(14) ;
         }
         $stuur12 = GEN_GetStuurWaarde(12) ;
         if ($stuur12 != 'ONBEKEND')
         {
             $blokdatum         = date("Y-m-d H:i:s", strtotime($stuur12)) ;
             $displaydatum     = '<b>'.date("d-m-Y H:i:s", strtotime($stuur12)).'</b><br><br>' ;
             $sysdatum         = date("Y-m-d H:i:s") ;
                 
             if ($blokdatum < $sysdatum)
             {
                 sc_error_message('<br><br><b>'.$stuurmelding.'<br><br>'.$displaydatum.'</b><br><br>') ;
                 sc_error_exit() ;
             }
         }
     }

 // Get current database name

     function GEN_GetDatabaseName()
     {
         $check_sql = "select ora_database_name from dual" ;
         sc_lookup(genrs, $check_sql);
         
         if (isset({genrs[0][0]})){
             [DBNAME] = {genrs[0][0]};
         }else{
             [DBNAME] = '';
         }
     }

 // Haal de naam op van een userid
     function GEN_GetUserName($uid_in)
     {
         $check_sql = "SELECT NAME_FORMAL FROM PS_RUG_MEDEWERKERS "
                    . " WHERE EMPLID = '" . $uid_in . "'";
         sc_lookup(genrs, $check_sql);

         $uidname = 'ONBEKEND' ;
         if (isset({genrs[0][0]}))     // Row found
         {
             $uidname = {genrs[0][0]} ;
         }

         return $uidname ;
     }

 // Haal de faculteit op van een userid
     function GEN_GetFaculteit($uid)
     {
         $check_sql = "SELECT RUG_FACULTEIT FROM PS_RUG_MEDEWERKERS ".
                      "WHERE EMPLID = '$uid'" ;
         sc_lookup(genrs, $check_sql);

         $faculteit = 'ONBEKEND' ;
         if (isset({genrs[0][0]}))     // Row found
         {
             $faculteit = {genrs[0][0]} ;
         }

         return $faculteit ;
     }

 // Write debug message in table DEBUG_MESSAGES
     function GEN_write_debug_message($debug_message)
     {
         $datum = date("Y-m-d H:i:s");
         $insert_sql = "INSERT INTO SCRIPTCASE.DEBUG_MESSAGES (DATETIME, MESSAGE) VALUES (TO_DATE('$datum', 'YYYY-MM-DD HH24:MI:SS'), '$debug_message')" ;
         sc_exec_sql($insert_sql);
     }

 // Write debug message in table RO_LOG
     function GEN_write_ro_log($gesprek_id, $emplid, $actie, $actie_engels)
     {
         $datum = date("Y-m-d H:i:s");
         $insert_sql = "insert into RO_LOG (GESPREK_ID, EMPLID, ACTIE, ACTIE_ENGELS, DATUM) values 
                 ($gesprek_id, $emplid, '$actie', '$actie_engels', TO_DATE('$datum', 'YYYY-MM-DD HH24:MI:SS'))" ;
         sc_exec_sql($insert_sql);
     }

 // Haal de waarde op van een stuurgegeven
     function GEN_GetStuurWaarde($stuurnr)
     {
         $stuur_sql = "SELECT WAARDE FROM STUURGEGEVENS WHERE STUURNR = '$stuurnr'" ;
         sc_lookup(genrs, $stuur_sql);

         $stuurwaarde = 'ONBEKEND' ;
         if (isset({genrs[0][0]}))     // Row found
         {
             $stuurwaarde = {genrs[0][0]} ;
         }

         return $stuurwaarde ;
     }

 // Haal de tekst op van een 'message'
     function GEN_GetMessageText($appid, $messageid, $lang)
     {
         $tmp_lang = strtoupper($lang) ;
         $stuur_sql = "select MESSAGETEXT from SC_MESSAGE where APPID = '$appid' and MESSAGEID = '$messageid' and LANID = '$tmp_lang'" ;
         sc_lookup(genrs, $stuur_sql);

         $tekst_uit = 'ONBEKEND' ;
         if (isset({genrs[0][0]}))     // Row found
         {
             $tekst_uit = {genrs[0][0]} ;
         }

         return $tekst_uit ;
     }

 // Haal de tekst op van een 'message' PER FACULTEIT !!
     function GEN_GetMessageTextFac($appid, $messageid, $lang, $faculteit)
     {
         $tmp_lang = strtoupper($lang) ;
		 $zoek_fac = '%'.$faculteit.'%' ;

		 $stuur_sql = "select MESSAGETEXT from SC_MESSAGE where APPID = '$appid' and MESSAGEID = '$messageid' ".
			 		  "and LANID = '$tmp_lang' and RUG_FACULTEIT like '$zoek_fac'" ;
         sc_lookup(genrs, $stuur_sql);

         $tekst_uit = 'ONBEKEND' ;
         if (isset({genrs[0][0]}))     // Row found
         {
             $tekst_uit = {genrs[0][0]} ;
         }

         return $tekst_uit ;
     }

 // Bewaar een error in MUTF_LOG
     function GEN_WriteLog($username,$application,$creator,$action,$description)
     {
         $ipadres = get_client_ip() ;
         $insert_sql="INSERT INTO SCRIPTCASE.MUTF_LOG(INSERTED_DATE,USERNAME,APPLICATION,CREATOR,IP_USER,ACTION,DESCRIPTION) VALUES".
                     "(TO_DATE(sysdate,'YYYY-MM-DD HH24:MI:SS'),'$username','$application','$creator','$ipadres','$action','$description')";
         sc_exec_sql($insert_sql);
     }

     function get_client_ip() 
     {
         $ipaddress = '';

         if (getenv('HTTP_CLIENT_IP'))
             $ipaddress = getenv('HTTP_CLIENT_IP');
         else if(getenv('HTTP_X_FORWARDED_FOR'))
             $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
         else if(getenv('HTTP_X_FORWARDED'))
             $ipaddress = getenv('HTTP_X_FORWARDED');
         else if(getenv('HTTP_FORWARDED_FOR'))
             $ipaddress = getenv('HTTP_FORWARDED_FOR');
         else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
         else if(getenv('REMOTE_ADDR'))
             $ipaddress = getenv('REMOTE_ADDR');
         else
             $ipaddress = 'UNKNOWN';

         return $ipaddress;
     }
 ?>