<?php
//__NM__Security helper functions__NM__FUNCTION__NM__//
function is_sha1($value)
{
	//return !empty($value) && (bool) preg_match('/^[a-f0-9]{40}$/i', $value);
	return !empty($value) && ctype_xdigit($value) && (strlen($value) == 40);
}
?>