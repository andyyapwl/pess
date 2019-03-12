<html>
   <?php
      include 'header.php';
      if (!isset($_POST["btnSearch"])){	
      	?>
   <form name="form1" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
      <table width="80%" border="0" align="center" cellpadding="4"
         cellspacing="4">
         <tr>
            <td width="25%" class="td_label">Patrol Car ID :</td>
            <td width="25%" class="td_Data"><input type="text"
               name="patrolCarId" id="patrolCarId"></td>
            </td>
         </tr>
       
         <td class="td_Data"><input type="submit" name="btnSearch"
            id="btnSearch" value="Search"></td>
         </tr>
      </table>
   </form>
   <?php 
      } else {
      
      $con = mysql_connect("localhost","Aliah","Asdfghjkl1234");
      if (!$con)
      {
      	die('Cannot connect to database : ' . mysql_error());
      }
      
      mysql_select_db("22_aliah_pessdb", $con);
      
     
      $sql = "SELECT * FROM patrolcar WHERE patrolcarId='".$_POST['patrolCarId']."'";
      
      $result = mysql_query($sql,$con);
      
      $patrolCarId;
      
      $patrolCarStatusId;
      
      if (mysql_num_rows($result)>0){
      
      while($row = mysql_fetch_array($result))
      {
      	
      	$patrolCarId = $row['patrolcarId'];
      	$patrolCarStatusId = $row['PatrolcarStatusId'];
      }
      
      }
      else
      {
      	echo ("<script type='text/javascript'> 
      
      			alert ('Partol Car No not found!');
      			
      	        window.location= 'update.php';
      			
      		   </script>");
      }
      
    
      $sql = "SELECT * FROM patrolcar_status";
      
      $result = mysql_query($sql,$con);
      
      $patrolCarStatusMaster;
      
      
      
      while($row = mysql_fetch_array($result))
      {
     
      	$patrolCarStatusMaster[$row['statusId']] = $row['statusDesc'];
      }
      
      
      
      mysql_close($con);
      
      ?>
   <!-- display a form to update patrol car status
      also update incident status when patrol car status is FREE -->
   <form name="form2" method="post"
      action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
      <table width="80%" border="0" align="center" cellpadding="4"
         cellspacing="4">
         <tr>
            <td width="25%" class="td_label">Patrol Car ID :</td>
            <td width="25%" class="td_Data"><?php echo $_POST["patrolCarId"]?>
               <input
                  type="hidden" name="patrolCarId" id="patrolCarId"
                  value="<?php echo $_POST["patrolCarId"]?>">
            </td>
         </tr>
         <tr>
            <td class="td_label">Status :</td>
            <td class="td_Data">
               <select name="patrolCarStatus"
                  id="$patrolCarStatus">
                  <?php foreach( $patrolCarStatusMaster as $key => $value){ ?>
                  <option value="<?php echo $key ?>"
                     <?php if ($key== $patrolCarStatusId) {?> selected="selected"
                     <?php }?>>
                     <?php echo $value ?>
                  </option>
                  <?php } ?>
               </select>
            </td>
         </tr>
      </table>
      <br />
      <table width="80%" border="0" align="center" cellpadding="4"
         cellspacing="4">
         <tr>
            <td width="46%" class="td_label"><input type="reset"
               name="btnCancel" id="btnCancel" value="Reset"></td>
            <td width="54%" 
               class="td_Data">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
               type="submit" name="btnUpdate" id="btnUpdate" value="Update"></td>
         </tr>
      </table>
   </form>
   <?php } ?>
   <?php
      //Codes to update tables
      
      if (isset($_POST["btnUpdate"])){
      
      // retrieve patrol car status and patrolcarstatus
      // connect to a database
      $con = mysql_connect("localhost","Aliah","Asdfghjkl1234");
      
      if (!$con)
      {
      die('Cannot connect to database : ' . mysql_error());
      }
      
      // select a table in the database
      mysql_select_db("22_aliah_pessdb", $con);
      
      // update patrol car status
      $sql = "UPDATE patrolcar SET PatrolcarStatusId='".$_POST["patrolCarStatus"]."'
      WHERE patrolcarId='".$_POST["patrolCarId"]."' ";
      
      if (!mysql_query($sql,$con))
      {
      die('Error4: ' . mysql_error());
      }
      
      // if patrol car status is on-site (4) then capture the time of arrival
      if ($_POST["patrolCarStatus"]=='4'){
      
      $sql = "UPDATE dispatch SET timeArrived=NOW()
      WHERE timeArrived is NULL AND patrolcarId='".$_POST["patrolCarId"]."' ";
      
      if (!mysql_query($sql,$con))
      {
      die('Error4: ' . mysql_error());
      }
      
      } elseif ($_POST["patrolCarStatus"]=='3'){	// else if patrol car status is FREE then capture the time of completion
      
      // First, retrieve the incident ID from dispatch table handled by that patrol car
      $sql = "SELECT incidentId FROM dispatch WHERE timeCompleted IS NULL AND patrolcarId='".$_POST["patrolCarId"]."'";
      
      $result = mysql_query($sql,$con);
      
      $incidentId;
      
      while($row = mysql_fetch_array($result))
      {
      	$incidentId = $row['incidentId'];
      }
      
      //echo $incidentId;
      
      // Now then can update dispatch
      $sql = "UPDATE dispatch SET timeCompleted=NOW()
      WHERE timeCompleted is NULL AND patrolcarId='".$_POST["patrolCarId"]."' ";
      
      if (!mysql_query($sql,$con))
      {
      	die('Error4: ' . mysql_error());
      }
      
      // Last but not least, update incident in incident table to completed (3) all patrol car attended to it are FREE now
      $sql = "UPDATE incident SET incidentStatusId='3' WHERE incidentId='$incidentId'
      AND incidentId NOT IN (SELECT incidentId FROM dispatch WHERE timeCompleted IS NULL)";
      
      if (!mysql_query($sql,$con))
      {
      	die('Error5: ' . mysql_error());
      }
      }
      
      mysql_close($con);
      
      ?>
   <script type="text/javascript">window.location="./logcall.php";</script>
   <?php } ?>
</html>