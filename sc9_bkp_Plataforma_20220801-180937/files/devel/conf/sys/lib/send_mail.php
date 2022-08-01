<?php
//__NM____NM__FUNCTION__NM__//
function send_mail($email_stu, $email_inst, $course, $con){

$email_command="SELECT smtp, user, password, from_mail
		FROM tb_email
		WHERE idmail = 1";
sc_lookup(dsss, $email_command);

$smtp = {dsss[0][0]};
$usr = {dsss[0][1]};
$pw = base64_decode({dsss[0][2]});
$from = {dsss[0][3]};
$to = $email_stu;
$subject = "Class Registration";

$sql_command = "SELECT course_title
		FROM course_catalog
		WHERE id_curso = ".$course." ";
sc_lookup(ds, $sql_command);
$course_title = {ds[0][0]};

$sql_command = "SELECT id_instructor
		FROM instructors
		where email_address = '".$email_inst."' ";
sc_lookup(ds1, $sql_command);
$id_instructorss = {ds1[0][0]};

$sql_command = "SELECT start_time, start_date
		FROM classes
		WHERE id_curso = ".$course." and id_instructor = ".$id_instructorss." ";
sc_lookup(ds2, $sql_command);
$start_time = {ds2[0][0]};
$start_date = {ds2[0][1]};


$sql_command = "SELECT full_name, id_student
		FROM students
		WHERE email_address = '".$email_stu."' ";
sc_lookup(ds3, $sql_command);
$name = {ds3[0][0]};
$id_student = {ds3[0][1]};








switch($con)
{
	case 1:
		$message = "<br/>You have registered in the ".$course_title." course successfully, awaiting approval from the respective supervisor";
		$message2 = "<br/>".$name." has registered in the ".$course_title." course ";
		sc_mail_send ($smtp, $usr, $pw, $from, $email_inst, $subject, $message2, 'H', '', '', '587');
		break;
	case 2:
		$message = "<br/>Your reservation in the ".$course_title." course has been confirmed.
		<br/>
		This Course will get started at ".$start_time." on the day ".$start_date."
		<br/>
		See you there!
		";
		break;
	case 3:
		$message = "<br/>Your registration in the ".$course_title." course has been cancelled.";
		$message2 = "<br/>".$name." has unregistered in the ".$course_title." Course";
		$subject = "Class Unregistration";
		sc_mail_send ($smtp, $usr, $pw, $from, $email_inst, $subject, $message2, 'H', '', '', '587');
		break;
}

sc_mail_send ($smtp, $usr, $pw, $from, $to, $subject, $message, 'H', '', '', '587');

}
?>