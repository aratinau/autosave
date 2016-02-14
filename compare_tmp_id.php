<?php
// Compare tmp_id
require_once('connection_sql.php');

$sql = "SELECT * FROM textarea WHERE id='".$_GET['id']."'";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
if ($row['tmp_id'] == $_POST['tmp_id'])
{
	echo "1";
}
else
{
	echo "0";
}
