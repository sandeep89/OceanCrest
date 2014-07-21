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

access("reservation"); //check if user is allowed to access this page
$conn=db_connect(HOST,USER,PASS,DB,PORT);

if (isset($_GET["search"]) && !empty($_GET["search"])){
	$reservation = find($_GET["search"]);
}

if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {
		case 'Reserve Now':
					$name_of_guest = !empty($_POST["name_of_guest"]) ? "'" . $_POST["name_of_guest"] . "'" : 'NULL';
					$contact_num = !empty($_POST["contact_num"]) ? "'" . $_POST["contact_num"] . "'" : 'NULL';
					$alt_contact_num = !empty($_POST["alt_contact_num"]) ? "'" . $_POST["alt_contact_num"] . "'" : 'NULL';
					$checkin_date = !empty($_POST["checkin_date"]) ? "'" . $_POST["checkin_date"] . "'" : 'NULL';
					$checkout_date = !empty($_POST["checkout_date"]) ? "'" . $_POST["checkout_date"] . "'" : 'NULL';
					$num_of_nights = !empty($_POST["num_of_nights"]) ? "'" . $_POST["num_of_nights"] . "'" : 'NULL';
					$num_of_adults = !empty($_POST["num_of_adults"]) ? "'" . $_POST["num_of_adults"] . "'" : 'NULL';
					$num_of_children = !empty($_POST["num_of_children"]) ? "'" . $_POST["num_of_children"] . "'" : 'NULL';
					$num_of_rooms = !empty($_POST["num_of_rooms"]) ? "'" . $_POST["num_of_rooms"] . "'" : 'NULL';
					$coming_from = !empty($_POST["coming_from"]) ? "'" . $_POST["coming_from"] . "'" : 'NULL';
					$reservation_date = !empty($_POST["reservation_date"]) ? "'" . $_POST["reservation_date"] . "'" : 'NULL';
					$booked_by = !empty($_POST["booked_by"]) ? "'" . $_POST["booked_by"] . "'" : 'NULL';
				$sql="INSERT INTO act_reservation (name_of_guest,contact_num,alt_contact_num,checkin_date,checkout_date,
					num_of_nights,num_of_adults,num_of_children,num_of_rooms,coming_from,reservation_date,booked_by)
					 VALUES($name_of_guest,$contact_num,$alt_contact_num,$checkin_date,$checkout_date,$num_of_nights,$num_of_adults,$num_of_children,
					 	$num_of_rooms,$coming_from,$reservation_date,$booked_by)";
				$results=mkr_query($sql,$conn);

				if ((int) $results==0){
					//should log mysql errors to a file instead of displaying them to the user
					echo 'Invalid query: ' . mysql_errno($conn). "<br>" . ": " . mysql_error($conn). "<br>";
					echo "Reservation NOT MADE.";  //return;
				}else{
          //$reservation = fetch_object($results);
          $reservationId = mysql_insert_id();

          /*
              Audit table descriptions
              audit_type = 1/2 (reservation/booking)
              autdit_num = (reservation/booking) id
              audit_val = (0,1,2) (created, confirmed, deleted)
          */
          

          // create a audit log for this reservations
          $sql = "INSERT INTO act_audit(date, changed_by, audit_type, audit_num, audit_val) VALUES
                  (now(), $booked_by, 1, $reservationId, '0')";

          $results=mkr_query($sql,$conn);
          var_dump($results);
					echo "<div align=\"center\"><h1>Reservation successfull.</h1></div>";					
				}							
			break;
      default:
        echo "Please provide valid action";
	}
}

function find($search){
	global $conn,$guests;
	$search=$search;

  $strOffSet=!empty($_POST["strOffSet"]) ? $_POST["strOffSet"] : 0; //offset value peacked on all pages with pagination - logical error
	
	//check on wether search is being done on idno/ppno/guestid/guestname
	$sql="Select * from act_reservation where reservation_id='$search'
		LIMIT $strOffSet,1";

	$results=mkr_query($sql,$conn);
	$reservation=fetch_object($results);
  return $reservation;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css/new.css" rel="stylesheet" type="text/css">
<title>OCEAN CREST RESERVATION SYSTEMS</title>

<script language="javascript" src="js/cal2.js"></script>
<script language="javascript" src="js/scripts.js"></script>
<script language="javascript" src="js/jquery-2.1.1.min.js"></script>
<script language="javascript" src="js/cal_conf2.js"></script>

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

function loadHTMLPost(URL, destination, button){
    dest = destination;
	//if (document.getElementById('roomid')==""){
		room = document.getElementById('roomid').value;
		str ='roomid='+ room;
		//get occupancy (no of adults/children) - should not be empty
		str =str + '&no_adults=' + (document.getElementById('no_adults').value);
		//get meal plan - should not be empty
		for (i=0;i<document.reservation.meal_plan.length;i++){
		if (document.reservation.meal_plan[i].checked==true)
			//alert(document.bookings.meal_plan[i].value);
			str =str + '&meal_plan=' + document.reservation.meal_plan[i].value;
		}

		//get direct/agent booking - should not be empty
		for (i=0;i<document.reservation.booking_type.length;i++){
		if (document.reservation.booking_type[i].checked==true)
			//alert(document.bookings.booking_type[i].value);
			str =str + '&booking_type=' + document.reservation.booking_type[i].value;
		}
		//alert(document.getElementById['agent']);	
		/*if (document.getElementById('agent')!==null){
			s=document.getElementById['agent'].value;
		}*/
	//}
	var str = str + '&button=' + button;
	
	if (window.XMLHttpRequest){
        request = new XMLHttpRequest();
        request.onreadystatechange = processStateChange;
        request.open("POST", URL, true);
        request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
		request.send(str);
    } else if (window.ActiveXObject) {
        request = new ActiveXObject("Microsoft.XMLHTTP");
        if (request) {
            request.onreadystatechange = processStateChange;
            request.open("POST", URL, true);
            request.send();
        }
    }
}

$(document).ready(function() {
  if(!$("#reservation_date").val()){
    $("#reservation_date").val(getCurrentDate());
  }
});

//-->	 
</script>
<style type="text/css">
<!--
.style1 {color: #000000}

.buttonlink{
    background-color: grey;
    color: white;
    display: block;
    height: 40px;
    line-height: 40px;
    text-decoration: none;
    width: 150px;
    text-align: center;
}

-->
</style>
</head>

<body >
<form action="reservations.php" method="post" name="reservation" enctype="multipart/form-data">
<table width="100%"  border="0" cellpadding="1" bgcolor="#66CCCC" align="center">
  <tr valign="top">
    <td width="19%" bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellpadding="1">
	  
	  <tr>
    <td width="15%" bgcolor="#66CCCC">
		<table cellspacing=0 cellpadding=0 width="100%" align="left" bgcolor="#FFFFFF">
      <tr>
        <td width="110" align="center"><a href="index.php"><img src="images/OpenCrest.gif" width="100%0" height="100%" border="0"/><br>
          Home</a></td>
      </tr>
      <tr><td>&nbsp; </td>
      </tr>
      <tr>
        <td align="center">
		<?php signon(); ?>
		</td></tr>
	  </table></td></tr>
	  	<?php require_once("menu_header.php"); ?>
    </table>
	</td>
    
    <td width="81%" bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="1">
      <tr>
        <td align="center"></td>
      </tr>
      <tr>
        <td>
		<H2>RESERVATION DETAILS</H2>
		</td>
      </tr>
      <tr>
        <td><table width="86%"  border="0" cellpadding="1" align="left">
    <tr>
    	<?php
    		if($reservation->reservation_id != ''){
    			?>
    			<td width="20%">Reservation Id </td>
     			<td><input id="reservation_id" type="text"  maxlength="100" readonly="" value="<?php echo $reservation->reservation_id; ?>"/></td>
    			<?php
    		}
    	?>
    </tr>
    <tr>
      <td width="20%">Guest Name* </td>
      <td width="25%"><input type="text" id="name_of_guest" name="name_of_guest" value="<?php echo trim($reservation->name_of_guest); ?>" maxlength="100" /></td>
      <td width="20%">Contact Number*</td>
      <td width="45%"><input type="text" id="contact_num" name="contact_num" maxlength="15" value="<?php echo $reservation->contact_num; ?>" /></td>
    </tr>
    <tr>
      <td>Coming From* </td>
      <td><input type="text" id="coming_from" name="coming_from" value="<?php echo trim($reservation->coming_from); ?>" /></td>
      <td>Alternate Number</td>
      <td><input type="text" id="alt_contact_num" name="alt_contact_num" maxlength="15" value="<?php echo $reservation->alt_contact_num; ?>"/></td>
    </tr>
    <tr>
      <td>Date of arrival* </td>
      <td><input type="text" name="checkin_date" id="checkin_date" onblur="nights()" readonly="" value="<?php echo $reservation->checkin_date; ?>"/>
          <a href="javascript:showCal('Calendar1')"> <img src="images/ew_calendar.gif" width="16" height="15" border="0"/></a></td>
      <td>Departure Date*</td>
      <td><input type="text" name="checkout_date" id="checkout_date" onblur="nights()" readonly="" value="<?php echo $reservation->checkout_date; ?>"/>
          <small><a href="javascript:showCal('Calendar2')"> <img src="images/ew_calendar.gif" width="16" height="15" border="0"/></a></small></td>
    </tr>
    <tr>
      <td>Number of nights*</td>
      <td><input type="text" name="num_of_nights" id="num_of_nights" value="<?php echo $reservation->num_of_nights; ?>" size="10"/></td>
    </tr>
    <tr>
      <td>Number of Guests* </td>
      <td colspan="4"><table width="62%"  border="0" cellpadding="1">
          <tr>
            <td >Adults <br />
                <input type="text" id="num_of_adults" name="num_of_adults" id="no_adults" value="<?php echo $reservation->num_of_adults; ?>" size="10"/></td>
            <td >Children <br />
                <input type="text" id="num_of_children" name="num_of_children" size="10" value="<?php echo $reservation->num_of_children; ?>" /></td>
          </tr>
      </table></td>
    </tr>
<tr>
      <td>Number of Rooms* </td>
	 <td><input type="text" name="num_of_rooms" id="num_of_rooms" size="10" value="<?php echo $reservation->num_of_rooms; ?>" /></td></td>
    </tr>

    <tr>
      <td colspan="2"><h2>Booking Taken By</h2></td>
    </tr>
    <tr>
      <td>Name*</td>
      <td><input type="text" id="booked_by" name="booked_by" value="<?php echo $reservation->booked_by; ?>"/></td>
    </tr>
    <tr>
      <td>Date</td>
      <td><input type="text" name="reservation_date" id="reservation_date" readonly="" value="<?php echo $reservation->reservation_date; ?>"/>
    </tr>
    <tr>
      <?php
        if($reservation->reservation_id){
          ?>
            <td><a class="buttonlink" href="bookings.php?confirmReservation=<?php echo $reservation->reservation_id ?>">Confirm Booking</a></td>
          <?php
          }
          else{?>
            <td><input class="buttonlink" type="submit" name="Submit" value="Reserve Now" /></td>    
          <?php
        }
      ?>
      
    </tr>
  </table>
		</td>
		
      </tr>
    </table></td>
  </tr>
</table>
</form>
</body>
</html>
