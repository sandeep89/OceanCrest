<?php
session_start();
/*****************************************************************************
/*Copyright (C) 2006 Tony Iha Kazungu
/*****************************************************************************
Hotel Management Information System (HotelMIS Version 1.0), is an interactive system that enables small to medium
sized hotels take guests bookings and make hotel reservations.  It could either be uploaded to the internet or used
on the hotel desk computers.  It keep tracks of guest bills and posting of receipts.  Hotel reports can alos be
produce to make work of the accounts department easier.

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License,
or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA or 
check for license.txt at the root folder
/*****************************************************************************
For any details please feel free to contact me at taifa@users.sourceforge.net
Or for snail mail. P. O. Box 938, Kilifi-80108, East Africa-Kenya.
/*****************************************************************************/
error_reporting(E_ALL & ~E_NOTICE);
include_once("login_check.inc.php");
include_once ("queryfunctions.php");
include_once ("functions.php");
$conn=db_connect(HOST,USER,PASS,DB,PORT);
access("guest"); //check if user is allowed to access this page
if (isset($_GET["search"])){
	find($_GET["search"]);
}	

//consider having this as a function in the functions.php
if (isset($_POST['Navigate'])){
	//echo $_SESSION["strOffSet"];
	$nRecords=num_rows(mkr_query("select * from guests",$conn),$conn);
	paginate($nRecords);
	free_result($results);
	find($_SESSION["strOffSet"]);	
}

if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {
		case 'Add Guest':
			// instantiate form validator object
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('lastname','Please enter Guests First Name');
			$fv->validateEmpty('firstname','Please enter Guests Last Name');
			$fv->validateEmpty('pp_id_no','Passport No. or ID. No. must be entered.');
			$fv->validateEmpty('countrycode','Please select country');
			//if (!empty($_POST["email"])) $fv->validateEmail('email','Please enter a valid email address');

			if($fv->checkErrors()){
				// display errors
				echo "<div align=\"center\">";
				echo '<h2>Resubmit the form after correcting the following errors:</h2>';
				echo $fv->displayErrors();
				echo "</div>";
			}
			else {
				$firstname=$_POST["firstname"];
				$middlename=$_POST["middlename"];
				$lastname=$_POST["lastname"];			
				$countrycode= $_POST["countrycode"];
				$pp_no=($_POST["identification_no"]==ppno) ? "'" . $_POST["pp_id_no"] . "'" : 'NULL';
				$idno=($_POST["identification_no"]==idno) ?  "'" . $_POST["pp_id_no"] . "'" : 'NULL';
				$pobox=$_POST["pobox"];
				$town=$_POST["town"];
				$postal_code=$_POST["postal_code"];
				$phone=$_POST["phone"];
				$email=$_POST["email"];
				$mobilephone=$_POST["mobilephone"];
				
				$sql="INSERT INTO guests (lastname,firstname,middlename,pp_no,idno,countrycode,pobox,town,postal_code,phone,email,mobilephone)
		 				VALUES('$lastname','$firstname','$middlename',$pp_no,$idno,'$countrycode','$pobox','$town','$postal_code','$phone','$email','$mobilephone')";
				$results=mkr_query($sql,$conn);		
				if ((int) $results==0){
					//should log mysql errors to a file instead of displaying them to the user
					echo 'Invalid query: ' . mysql_errno($conn). "<br>" . ": " . mysql_error($conn). "<br>";
					echo "Guests record NOT ADDED.";  //return;
				}else{
					echo "<div align=\"center\"><h1>Guests record successful added.</h1></div>";
				}				
			}						
			break;
		case 'List':

			break;
		case 'Find':
			//check if user is searching using name, payrollno, national id number or other fields
			find($_POST["search"]);
			break;
	}
}

function find($search){
	global $conn,$guests;
	$search=$search;
	$strOffSet=!empty($_POST["strOffSet"]) ? $_POST["strOffSet"] : 0;
	//check on wether search is being done on idno/ppno/guestid/guestname
	$sql="Select guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as guest,guests.pp_no,
		guests.idno,guests.countrycode,guests.pobox,guests.town,guests.postal_code,guests.phone,
		guests.email,guests.mobilephone,countries.country
		From guests
		Inner Join countries ON guests.countrycode = countries.countrycode where guests.guestid='$search'
		LIMIT $strOffSet,1";
	$results=mkr_query($sql,$conn);
	$guests=fetch_object($results);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css/new.css" rel="stylesheet" type="text/css">
<title>OCEAN CREST RESERVATION SYSTEMS</title>

<script type="text/javascript">
<!--
var request;
var dest;

function loadHTML(URL, destination){
    dest = destination;
	if (window.XMLHttpRequest){
        request = new XMLHttpRequest();
        request.onreadystatechange = processStateChange;
        request.open("GET", URL, true);
        request.send(null);
    } else if (window.ActiveXObject) {
        request = new ActiveXObject("Microsoft.XMLHTTP");
        if (request) {
            request.onreadystatechange = processStateChange;
            request.open("GET", URL, true);
            request.send();
        }
    }
}

function processStateChange(){
    if (request.readyState == 4){
        contentDiv = document.getElementById(dest);
        if (request.status == 200){
            response = request.responseText;
            contentDiv.innerHTML = response;
        } else {
            contentDiv.innerHTML = "Error: Status "+request.status;
        }
    }
}

function loadHTMLPost(URL, destination){
    dest = destination;
	if (window.XMLHttpRequest){
        request = new XMLHttpRequest();
        request.onreadystatechange = processStateChange;
        request.open("POST", URL, true);
        request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
      	request.setRequestHeader("Content-length", parameters.length);
      	request.setRequestHeader("Connection", "close");
		request.send("good");
    } else if (window.ActiveXObject) {
        request = new ActiveXObject("Microsoft.XMLHTTP");
        if (request) {
            request.onreadystatechange = processStateChange;
            request.open("POST", URL, true);
            request.send();
        }
    }
}

function BookGuest(){
	if(document.getElementById('guestid').value==""){
		alert("Please select a guest to check in");
	}else{
		//check if guest with same id/pp no has been checked in.
		guestid=document.getElementById('guestid').value;
		self.location='bookings.php?search='+guestid
	}	
}

function ReserveGuest(){
	if(document.getElementById('guestid').value==""){
		alert("Please select a guest to make a reservation");
	}else{
		guestid=guestid=document.getElementById('guestid').value;
		self.location='reservations.php?search='+guestid
	}	
}

//-->	 
</script>
</head>

<body>
<form action="guests.php" method="post" enctype="multipart/form-data">
<table width="100%"  border="0" cellpadding="1" align="center" bgcolor="#66CCCC">
  <tr valign="top">
    <td width="17%" bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellpadding="1">	  
	  <tr>
    <td width="15%" bgcolor="#66CCCC">
		<table cellspacing=0 cellpadding=0 width="100%" align="left" bgcolor="#FFFFFF">
      <tr><td width="110" align="center"><a href="index.php"><img src="images/OpenCrest.gif" width="100%" height="100%" border="0"/><br>
          Home</a> </td>
      </tr>
      <tr><td> <br>
          </input></td>
      </tr>
      <tr>
        <td align="center">
		  <?php signon(); ?>		
		</td>
      </tr>
	  </table></td></tr>
		<?php require_once("menu_header.php"); ?>				
    </table>
	</td>
    
    <td width="65%" bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="1">
      <tr>
        <td width="13%" align="center"></td>
      </tr>
      <tr>
        <td>
		<h2>GUESTS</h2>
		</td>
		<td width="87%"><table border="1" cellpadding="0">
          <tr>
            <td width="78">
			<input type="submit" name="Navigate" id="First" style="cursor:pointer" title="first page" value="<<"/>
            <input type="submit" name="Navigate" id="Previous" style="cursor:pointer" title="previous page" value="<"/>
            </td>
            <td width="241" align="center" bgcolor="#FFFFFF"><?php echo trim($guests->guest); ?> </td>
            <td width="79">
			<input type="submit" name="Navigate" id="Next" style="cursor:pointer" title="next page" value=">"/>
            <input type="submit" name="Navigate" id="Last" style="cursor:pointer" title="last page" value=">>"/>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td colspan="2"><div id="Requests">
          <table width="94%"  border="0" cellpadding="1">
            <tr>
              <td width="25%">Guest Id. </td>
              <td width="24%"><input type="text" name="guestid" id="guestid" value="<?php echo trim($guests->guestid); ?>" readonly=""/></td>
              <td width="24%">&nbsp;</td>
              <td width="27%">&nbsp;</td>
            </tr>
            <tr>
              <td>Guest</td>
              <td>Last<br>
                <input type="text" name="lastname" id="lastname" value="<?php echo trim($guests->lastname); ?>" /></td>
              <td>First<br><input type="text" name="firstname" value="<?php echo trim($guests->firstname); ?>" /></td>
              <td>Middle<br><input type="text" name="middlename" value="<?php echo trim($guests->middlename); ?>" /></td>
            </tr>
            <tr>
              <td ><p>
                <label><input type="radio" name="identification_no" value="ppno" <?php echo (!is_null($guests->pp_no) ? "checked=\"checked\"" : ""); ?> /> PP. No.</label>
                <label><input type="radio" name="identification_no" value="idno" <?php echo (!is_null($guests->idno) ? "checked=\"checked\"" : ""); ?> /> ID. No.</label>
              </p>
                </td>
				<td><input type="text" name="pp_id_no" value="<?php echo (!is_null($guests->pp_no) ? $guests->pp_no : $guests->idno); ?>" /></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
			</tr>
            <tr>
              <td>Country</td>
              <td colspan="3"><select name="countrycode">
                <option value="">Select Country</option>
				<?php populate_select("countries","countrycode","country",$guests->countrycode);?>
              </select></td>
            </tr>
            <tr>
              <td>Telephone<br>(Area Code-Phone No.) </td>
              <td><input type="text" name="phone" id="phone" value="<?php echo trim($guests->phone); ?>" /></td>
              <td>Mobile</td>
              <td><input type="text" name="mobilephone" id="mobilephone" value="<?php echo trim($guests->mobilephone); ?>" /></td>
            </tr>
            <tr>
              <td>E-mail</td>
              <td><input type="text" name="email" value="<?php echo trim($guests->email); ?>" /></td>
              <td>Fax</td>
              <td><input type="text" name="fax" value="<?php echo trim($guests->fax); ?>" /></td>
            </tr>
            <tr>
              <td>P. o. Box</td>
              <td><input type="text" name="pobox" value="<?php echo trim($guests->pobox); ?>" /></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>Town</td>
              <td><input type="text" name="town" value="<?php echo trim($guests->town); ?>" /></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>Postal code</td>
              <td><input type="text" name="postal_code" value="<?php echo trim($guests->postal_code); ?>" /></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="button" name="button" value="Guests Chek-In" onclick="BookGuest()"/></td>
              <td><input type="button" name="button" value="Reserve Now" onclick="ReserveGuest()"/></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          </table>
                </div></td>
		
      </tr>
	  <tr bgcolor="#66CCCC" >
        <td align="left" colspan="2"><div id="RequestDetails"></div>
		</td>
      </tr>
    </table></td>
	<td width="18%" bgcolor="#FFFFFF">
	
	<table width="100%"  border="0" cellpadding="1">	  
	  <tr>
    <td width="15%" bgcolor="#66CCCC">
	
	<table width="100%"  border="0" cellpadding="1" bgcolor="#FFFFFF" >
       <tr>
        <td>Image</td>
      </tr>
	  <tr>
        <td><input type="submit" name="Submit" value="Add Guest"/></td>
      </tr>
      <tr>
        <td><input type="button" name="Submit" value="Guests List" onclick="self.location='guests_list.php'"/></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>	  
      <tr>
        <td>
            <label> Search By:<br />
            <input type="radio" name="optFind" value="Name" />
        Guest Name</label>
            <br />
            <label>
            <input type="radio" name="optFind" value="Payrollno" />
        ID. No./PP. No </label>
            <br>
        <input type="text" name="search" width="100" /><br>
        <input type="submit" name="Submit" value="Find"/>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	</td></tr></table>
	</td>
  </tr>
   <?php require_once("footer1.php"); ?>
</table>
</form>
</body>
</html>