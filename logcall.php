<!DOCTYPE HTML>
<html>
<head>
<title>Police Emergency Service System</title>
<script type="text/javascript">
function validateForm()
{
	var x=document.forms["frmLogCall"]["callerName"].value;
	if (x==null || x=="")
  	{
  		alert("Caller Name is required.");
  		return false;
  	}
  	// may add code for validating other inputs
}
</script>
</head>
<body>
<?php 
require_once 'header.php';
?>
<?php	
	$con = mysql_connect("localhost","Aliah","Asdfghjkl1234");
	if(!$con)
	{
		die('Cannot connect to database :'.mysql_error());
	}
	mysql_select_db("22_aliah_pessdb",$con);
	$result=mysql_query("SELECT * FROM incidenttype");
	$incidentType;

	while($row=mysql_fetch_array($result))
	{
		$incidentType[$row['IncidentTypeId']]=$row['IncidentTypeDesc'];
	}
	mysql_close($con);
?>
<br><br>
<fieldset>
<legend>Log call</legend>
<form name="frmLogCall" method="post"
	onSubmit="return validateForm()" action="dispatch.php">
	
	<table class="ContentStyle">


		<tr>
			<td>Caller's Name :</td>
			<td><input type="text" name="callerName" id="callerName">
			</td>
		</tr>
		<tr>
			<td>Contact No :</td>
			<td><input type="text" name="contactNo" id="contactNo">
			</td>
		</tr>
		<tr>
			<td>Location :</td>
			<td><input type="text" name="location" id="location">
			</td>
		</tr>
		<tr>
			<td>Incident Type :</td>
			<td>
				<select name="incidentType" id="incidentType">
	
					<?php // populate a combo box with $incidentType
						foreach( $incidentType as $key => $value){ 
					?>
							<option value="<?php echo $key ?>">
								<?php echo $value ?>
							</option>
					<?php 
						} 
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Description :</td>
			<td><textarea name="incidentDesc" id="incidentDesc" cols="45"
					rows="5"></textarea>
			</td>
		</tr>
		<tr>
			<td><input type="reset" name="btnCancel" id="btnCancel"
				value="Reset">
			</td>
			<td>&nbsp;&nbsp;<input type="submit"
				name="btnProcessCall" id="btnProcessCall" value="Process Call...">
			</td>
		</tr>
	</table>

</form>
</fieldset>
</body>
</html>