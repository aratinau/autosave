<?php
require_once('connection_sql.php');

$sql = "SELECT * FROM textarea";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
if (isset($_POST['textarea']))
{
	$sql = "SELECT COUNT(*) from textarea WHERE id='1'";
	$result = mysqli_query($link, $sql);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if ($row['COUNT(*)'] == 0) // c'est un INSERT
	{
		$sql = "INSERT INTO textarea VALUES('', '".addslashes(htmlentities($_POST['textarea']))."', NOW(), '".$_POST['tmp_id'].')';
	}
	else
	{
		$fuck = (int)$_POST['tmp_id'];
		$sql = "UPDATE textarea SET content='".addslashes(htmlentities($_POST['textarea']))."', datetime=NOW(), tmp_id='".$fuck."' WHERE id='1'";
	}
	mysqli_query($link, $sql);
}
