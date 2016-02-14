<?php
require_once('connection_sql.php');

$sql = "SELECT * FROM textarea";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
foreach ($result as $elem)
{
	$textarea = stripslashes($elem['content']);
}
if (isset($_POST['textarea']))
{
	//if (!empty($_POST['textarea']))
	//{
		$sql = "SELECT COUNT(*) from textarea WHERE id='1'";
		$result = mysqli_query($link, $sql);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if ($row['COUNT(*)'] == 0) // c'est un INSERT
		{
			$sql = "INSERT INTO textarea VALUES('', '".addslashes($_POST['textarea']).", NOW()')";
		}
		else
		{
			$sql = "UPDATE textarea SET content='".addslashes($_POST['textarea'])."', datetime=NOW() WHERE id='1'";
		}
		mysqli_query($link, $sql);
	//}
}
