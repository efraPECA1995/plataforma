<?php
//__NM__Library to record log__NM__FUNCTION__NM__//
 
function record_log($app, $login, $action){ 
    $date = date('Y-m-d H:i:s'); 
    $ip = $_SERVER['REMOTE_ADDR']; 
    sc_exec_sql("INSERT INTO application_logs ( 
           Application_Name, Date_Time, Login,  
            IP_User, Action_Held)  
           VALUES ('$app', '$date', '$login',  
                '$ip', '$action')"); 
} 
?>