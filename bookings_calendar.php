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

if (isset($_GET["search"])){
	find($_GET["search"]);
}
$guestid=$_POST['guestid'];


if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {
		case 'Update':
			// instantiate form validator object
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('booking_type','Please indicate if it\'s a Direct booking or Agent booking.');
			$fv->validateEmpty('meal_plan','Please select Meal Plan');
			//$fv->validateEmpty('countrycode','Please select country');
			//$fv->validateEmpty('pp_no','Agents name must be entered');

			if($fv->checkErrors()){
				// display errors
				echo "<div align=\"center\">";
				echo '<h2>Resubmit the form after correcting the following errors:</h2>';
				echo $fv->displayErrors();
				echo "</div>";
			}
			else {
				$booking_type=$_POST["booking_type"];
				$meal_plan=$_POST["meal_plan"];
				$no_adults=$_POST["no_adults"];			
				$no_child= $_POST["no_child"];
				$checkin_date= "'" . $_POST["checkin_date"] . "'" ;
				$checkout_date=!empty($_POST["checkout_date"]) ? "'" . $_POST["checkout_date"] . "'" : 'NULL';
				$residence_id=$_POST["residence_id"];
				$payment_mode=$_POST["payment_mode"];
				$agents_ac_no=!empty($_POST["agents_ac_no"]) ? $_POST["agents_ac_no"] : 'NULL';
				$roomid=$_POST["roomid"];
				$checkedin_by=1; //$_POST["checkedin_by"];
				$invoice_no=!empty($_POST["invoice_no"]) ? $_POST["invoice_no"] : 'NULL';
			
				$sql="INSERT INTO booking (guestid,booking_type,meal_plan,no_adults,no_child,checkin_date,checkout_date,
					residence_id,payment_mode,agents_ac_no,roomid,checkedin_by,invoice_no)
				 VALUES($guestid,'$booking_type','$meal_plan',$no_adults,$no_child,$checkin_date,$checkout_date,
					'$residence_id',$payment_mode,$agents_ac_no,$roomid,$checkedin_by,$invoice_no)";
				$results=mkr_query($sql,$conn);
				if ((int) $results==0){
					//should log mysql errors to a file instead of displaying them to the user
					echo 'Invalid query: ' . mysql_errno($conn). "<br>" . ": " . mysql_error($conn). "<br>";
					echo "Guests NOT BOOKED.";  //return;
				}else{
					echo "<div align=\"center\"><h1>Guests successful checked in.</h1></div>";
				}				
			}
			find($guestid);
			break;
		case 'List':
			$guestid=$_POST['guestid'];
			$sql="Select guests.guestid,guests.lastname,guests.firstname,guests.middlename,booking.checkin_date,booking.checkout_date,
			booking.meal_plan,booking.no_adults,booking.no_child,booking.roomid,booking.checkedin_by,rooms.roomno
			From booking
			Inner Join guests ON booking.guestid = guests.guestid
			Inner Join rooms ON booking.roomid = rooms.roomid
			Where booking.guestid = '$guestid'";
			$results=mkr_query($sql,$conn);
			echo "<table align=\"center\">";
			//get field names to create the column header
			echo "<tr bgcolor=\"#009999\">
				<th></th>
				<th>Guest</th>
				<th>Meal Plan</th>
				<th>Check-In Date</th>
				<th>Check-Out Date</th>
				<th>Adults</th>
				<th>Children</th>
				<th>Room No.</th>
				</tr>";
			//end of field header
			//get data from selected table on the selected fields
			while ($booking = fetch_object($results)) {
				//alternate row colour
				$j++;
				if($j%2==1){
					echo "<tr bgcolor=\"#CCCCCC\">";
					}else{
					echo "<tr bgcolor=\"#EEEEF8\">";
				}
					echo "<td><a href=\"billings.php?search=$guest->guestid\"><img src=\"images/button_signout.png\" width=\"16\" height=\"16\" border=\"0\" title=\"bill guest\"/></a></td>";
					echo "<td>" . trim($booking->firstname) .' '. trim($booking->middlename) .' '. trim($booking->lastname) . "</td>";
					echo "<td>" . $booking->meal_plan . "</td>";
					echo "<td>" . $booking->checkin_date . "</td>";
					echo "<td>" . $booking->checkout_date . "</td>";
					echo "<td>" . $booking->no_adults . "</td>";
					echo "<td>" . $booking->no_child . "</td>";
					echo "<td>" . $booking->roomno . "</td>";					
				echo "</tr>"; //end of - data rows
			} //end of while row
			echo "</table>";
			break;
		case 'Find':
			//check if user is searching using name, payrollno, national id number or other fields
			$search=$_POST["search"];
			find($search);
			$sql="Select guests.guestid,guests.lastname,guests.firstname,guests.middlename,guests.pp_no,
			guests.idno,guests.countrycode,guests.pobox,guests.town,guests.postal_code,guests.phone,
			guests.email,guests.mobilephone,countries.country
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode where pp_no='$search'";
			$results=mkr_query($sql,$conn);
			$bookings=fetch_object($results);
			break;
	}
}

function find($search){
	global $conn,$guests;
	$search=$search;
	//check on wether search is being done on idno/ppno/guestid/guestname
	$sql="Select guests.guestid,guests.lastname,guests.firstname,guests.middlename,guests.pp_no,
		guests.idno,guests.countrycode,guests.pobox,guests.town,guests.postal_code,guests.phone,
		guests.email,guests.mobilephone,countries.country
		From guests
		Inner Join countries ON guests.countrycode = countries.countrycode where guests.guestid='$search'";
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

function loadHTMLPost(URL, destination, button){
    dest = destination;
	var str = 'button=' + button;
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

function Nights(){
alert (document.getElementById('arrivaldate').value);
alert (document.getElementById('departuredate').value);
}
//-->	 
</script>
</script>
<script language="javascript" src="js/cal2.js">
/*
Xin's Popup calendar script-  Xin Yang (http://www.yxscripts.com/)
Script featured on/available at http://www.dynamicdrive.com/
This notice must stay intact for use
*/
</script>
<script language="javascript" src="js/cal_conf2.js"></script>
</head>

<body>
<form action="bookings_calendar.php" method="post" name="bookings" enctype="multipart/form-data">
<table width="100%"  border="0" cellpadding="1" bgcolor="#66CCCC" align="center">
  <tr valign="top">
    <td width="19%" bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellpadding="1">
	  
	  <tr>
    <td width="15%" bgcolor="#66CCCC">
		<table cellspacing=0 cellpadding=0 width="100%" align="left" bgcolor="#FFFFFF">
      <tr><td width="110" align="center"><a href="index.php"><img src="images/OpenCrest.gif" width="100%" height="100%" border="0"/><br>
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
        <td>
		<H2>BOOKING CALENDAR</H2>
		</td>
      </tr>
      <tr>
        <td><div id="Requests"> <?php //guests in hotel
	/*$sql="Select rooms.roomno,booking.guestid,guests.lastname,guests.firstname,guests.middlename,booking.checkin_date,booking.checkout_date
	From rooms Inner Join booking ON rooms.roomid = booking.roomid
	Inner Join guests ON booking.guestid = guests.guestid
	Order By booking.checkin_date Asc";
	$results=mkr_query($sql,$conn);*/
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	echo "<table align=\"center\" border=\"1\">";
	echo "<tr><td colspan=\"32\">Guest booking for the Month of January 2006</td></tr>
	<tr><td width=\"67\"><select name=\"year\" id=\"year\" >
	  <option value=\"2006\">2007</option>
	  <option value=\"2006\">2006</option>
	  <option value=\"2005\">2005</option>
	  <option value=\"2004\">2004</option>
	  <option value=\"2003\">2003</option>
	  <option value=\"2002\">2002</option>
	</select></td>
	<td width=\"67\" colspan=\"4\" ><select name=\"month\" id=\"month\" >
	  <option value=\"1\">January</option>
	  <option value=\"2\">February</option>
	  <option value=\"3\">March</option>
	  <option value=\"4\">April</option>
	  <option value=\"5\">May</option>
	  <option value=\"6\">June</option>
	  <option value=\"7\">July</option>
	  <option value=\"8\">August</option>
	  <option value=\"9\">September</option>
	  <option value=\"10\">October</option>
	  <option value=\"11\">November</option>
	  <option value=\"12\">December</option>
	</select></td>
	<td colspan=\"3\"><input type=\"submit\" name=\"Submit\" value=\"Submit\"/></td></tr>";
	echo "<tr bgcolor=\"#009999\">";
	echo "<th>Room No.</th>";
	for($i=1; $i<=31; $i++){
		echo "<th>$i</th>";
	}
	echo "</tr>";
	$month=$_POST["month"];
	$year=$_POST["year"];
	$sqloriginal = "Select rooms.roomno,rooms.status,booking.guestid,guests.lastname,guests.firstname,guests.middlename,DAYOFMONTH(booking.checkin_date) chkin_day,
	 		MONTH(booking.checkin_date) chkin_month, YEAR(booking.checkin_date) chkin_year,booking.checkin_date,booking.checkout_date, DATEDIFF(booking.checkout_date,booking.checkin_date) nights 
         From rooms left Join booking ON rooms.roomid = booking.roomid 
         left Join guests ON booking.guestid = guests.guestid 
         Order By rooms.roomno Asc";
	/*$sql = "Select rooms.roomno,booking.guestid,guests.lastname,guests.firstname,guests.middlename,DAYOFMONTH(booking.checkin_date) chkin_day,
	 		MONTH(booking.checkin_date) chkin_month, YEAR(booking.checkin_date) chkin_year,booking.checkin_date,booking.checkout_date, DATEDIFF(booking.checkout_date,booking.checkin_date) nights
         From rooms left Join booking ON rooms.roomid = booking.roomid 
         left Join guests ON booking.guestid = guests.guestid 
         Where MONTH(booking.checkin_date)='$month' and YEAR(booking.checkin_date)='$year'
         Order By rooms.roomno Asc";*/
		 
		 
		 //Booking and reservation data on same row separate columns;
	$sql = "Select	rooms.roomno,rooms.`status`,booking.guestid,
		concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as b_guest,
		DAYOFMONTH(booking.checkin_date) AS chkin_day,
		MONTH(booking.checkin_date) AS chkin_month,
		YEAR(booking.checkin_date) AS chkin_year,
		booking.checkin_date,booking.checkout_date,
		DATEDIFF(booking.checkout_date,booking.checkin_date) AS b_nights,
		DAYOFMONTH(reservation.reserve_checkindate) AS r_chkin_day,
		MONTH(reservation.reserve_checkindate) AS r_chkin_month,
		YEAR(reservation.reserve_checkindate) AS r_chkin_year,
		reservation.reserve_checkindate,reservation.reserve_checkoutdate,
		DATEDIFF(reservation.reserve_checkoutdate,reservation.reserve_checkindate) AS r_nights,
		reservation.billed,reservation.deposit,
		concat_ws(' ',guests2.firstname,guests2.middlename,guests2.lastname) as r_guest
		From rooms
		Left Join booking ON rooms.roomid = booking.roomid
		Left Join guests ON booking.guestid = guests.guestid
		Left Join reservation ON rooms.roomid = reservation.roomid
		Left Join guests as guests2 ON reservation.guestid = guests2.guestid
		Order By rooms.roomno Asc";
				 
	$results=mkr_query($sql,$conn);
	$numrows=num_rows($results);
	while ($row=fetch_object($results)){
		echo "<tr><td>$row->roomno</td>";	
		//get field names to create the column header
		for($i=1; $i<=31; $i++){
			//check status and set colour
			switch($row->status){
				case "V":
					$color='bgcolor="#FFFFFF"';
					$status="Vacant";
					$statusday=0;
					$statusnights=0;
					$statusguest="";
					break;
				case "R":
					$color='bgcolor="#87CEEB"';
					$status="Reserved";
					$statusday=$row->r_chkin_day;
					$statusnights=$row->r_nights;
					$statusguest=$row->r_guest;					
					break;
				case "B":
					$color='bgcolor="#FFA500"';
					$status="Booked";
					$statusday=$row->chkin_day;
					$statusnights=$row->b_nights;
					$statusguest=$row->b_guest;
					break;
				case "L":
					$color='bgcolor="#FFFF00"';
					$status="Blocked";
					$statusday=0;
					$statusnights=0;
					$statusguest="";					
					break;
			}
			
			//decide whether to show status of locked and vacant rooms;			
			if ($i==$statusday){
				echo "<td $color colspan=\"$statusnights\">$statusguest: $status</td>";
				$i= $i + ($statusnights-1);
			}else{
				echo "<td>&nbsp;</td>";
			}
		}
		echo "</tr>";
	}
	free_result($result);
	echo "</table>";
	 ?> </div></td>
		
      </tr>
	  <tr bgcolor="#66CCCC" >
        <td align="left">
		<div id="booking_list"></div>
		</td>
      </tr>
    </table></td>
  </tr>
   <?php require_once("footer1.php"); ?>
</table>
</form>
</body>
</html>