<!DOCTYPE HTML>
<html>
<head>
<title>Police Emergency Service System</title>

<?php	
if (!isset($_POST["btnProcessCall"]) && !isset($_POST["btnDispatch"]))
	header("Location: logcall.php");
?>

<?php	
if (isset($_POST["btnDispatch"]))
{
	$con = mysql_connect("localhost","Aliah","Asdfghjkl1234");
	if(!$con)
	{
		die('Cannot connect to database :'.mysql_error());
	}
	mysql_select_db("22_aliah_pessdb",$con);
	 
	$patrolcarDispatched = $_POST["chkPatrolcar"];	
	$numOfPatrocarDispatched = count($patrolcarDispatched);

	$incidentStatus;
	if ($numOfPatrocarDispatched > 0) {
		$incidentStatus='2';	
	} else {
		$incidentStatus='1';	
	}
	
	
	$sql="INSERT INTO incident (callerName,phoneNumber,incidentTypeId," .
	"incidentLocation,incidentDesc, incidentStatusId) " .
	"VALUES('".$_POST['callerName'] . "','" . $_POST['contactNo'] . 
	"','" . $_POST['incidentType']. "','" . $_POST['location'] .
	"','" . $_POST['incidentDesc'] ."','" . $incidentStatus . "')";


	if(!mysql_query($sql,$con))
	{
		die("Error1:".mysql_error());
	}
	//retrieve new incremental key for incidentId
	$incidentId=mysql_insert_id($con);
	
	
	for($i=0; $i < $numOfPatrocarDispatched; $i++)
	{
		$sql= "UPDATE patrolcar SET patrolcarStatusId='1' WHERE patrolcarId='" . $patrolcarDispatched[$i] . "'";
		
		if(!mysql_query($sql,$con))
		{
			die("Error2:".mysql_error());
		}	
		
		$sql="INSERT INTO dispatch(incidentId,patrolcarId,timeDispatched) VALUES " .
		"('" . $incidentId . "','" . $patrolcarDispatched[$i] . "',NOW())";
		
		if(!mysql_query($sql,$con))
		{
			die("Error3:".mysql_error());
		}	
	}
	mysql_close($con);

?>

<?php } ?>

</head>

<body>
<?php require_once 'header.php'; ?>
<br><br>
<form name="form1" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
<fieldset>
<legend>Incident Detail</legend>

<table class="ContentStyle">
		
	<tr>
		<td>Caller's Name :</td>
		<td><?php echo $_POST['callerName'] ?>
			<input type="hidden" name="callerName" id="callerName"
			value="<?php echo $_POST['callerName'] ?>"></td>
	</tr>
	<tr>
		<td>Contact No :</td>
		<td><?php echo $_POST['contactNo']?> <input
			type="hidden" name="contactNo" id="contactNo"
			value="<?php echo $_POST['contactNo']?>"></td>
	</tr>
	<tr>
		<td>Location :</td>
		<td><?php echo $_POST['location'] ?> <input
			type="hidden" name="location" id="location"
			value="<?php echo $_POST['location'] ?>"></td>
	</tr>
	<tr>
		<td>Incident Type :</td>
		<td><?php echo $_POST['incidentType'] ?> <input
			type="hidden" name="incidentType" id="incidentType"
			value="<?php echo $_POST['incidentType'] ?>"></td>
	
	
	<tr>
		<td>Description :</td>
		<td><textarea name="incidentDesc" cols="45" 
				rows="5" readonly id="incidentDesc"><?php echo $_POST['incidentDesc'] ?></textarea>
			 <input name="incidentDesc" type="hidden"
			id="incidentDesc" value="<?php echo $_POST['incidentDesc'] ?>"></td>
	</tr>
</table>
<br><br>
<?php

$con=mysql_connect("localhost","Aliah","Asdfghjkl1234");
if(!$con)
{
	die('Cannot connect to database:'.mysql_error());
}

//select a table in the database
mysql_select_db("22_aliah_pessdb",$con);

$sql ="SELECT patrolcarId,statusDesc FROM patrolcar JOIN patrolcar_status
ON patrolcar.patrolcarStatusId=patrolcar_status.statusId
WHERE patrolcar.patrolcarStatusId='2' OR patrolcar.patrolcarStatusId='3'";

$result = mysql_query($sql,$con);
$incidentArray;
$count=0;

while($row=mysql_fetch_array($result))
{
	$patrolcarArray[$count]=$row;
	$count++;
}

if(!mysql_query($sql,$con))
{
	die('Error:'.mysql_error());
}
mysql_close($con);

?>

<table width="40%" border="1" align="center" cellpadding="4"
cellspacing="8">
<tr>
<td width="20%">&nbsp;</td>
<td width="51%">Patrol Car ID</td>
<td width="29%">Status</td>
<tr>

<?php
$i=0;
while($i<$count){
?>
<tr>
<td class="td_label"><input type="checkbox" name="chkPatrolcar[]" value="<?php echo 
$patrolcarArray[$i]['patrolcarId']?>"></td>
<td><?php echo $patrolcarArray[$i]['patrolcarId']?></td>
<td><?php echo $patrolcarArray[$i]['statusDesc']?></td>
</tr>

<?php $i++;
} ?>

</table>


<table width="80%" border="0" align="center" cellpadding="4"
cellspacing="4">
	<tr>
		<td><input type="reset"
			name="btnCancel" id="btnCancel" value="Reset"></td>
		<td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
			type="submit" name="btnDispatch" id="btnDispatch" value="Dispatch">
		</td>
	</tr>
</table>

</fieldset>
</form>
</body>
</html>
