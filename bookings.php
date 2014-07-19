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
require_once("login_check.inc.php");
require_once("queryfunctions.php");
require_once("functions.php");

require_once("./process/bookings_proc.php");
//require_once("./functions/bookings_functions.php");

access("booking"); //check if user is allowed to access this page

//if page was visited through a search hyperlin.
if (isset($_GET["search"]) && !empty($_GET["search"])){
	$bookings = find($_GET["search"]);

    $bookingId = $bookings->booking_id;
    $guestName = $bookings->name_of_guest;
    $age = $bookings->age;
    $dependents = ($bookings->dependents!='NULL') ? $bookings->dependents  : '';
    $no_adults = ($bookings->num_of_adults != 'NULL') ? $bookings->num_of_adults : '';
    $no_child = ($bookings->num_of_children != 'NULL') ? $bookings->num_of_children : '';
    $address = $bookings->address;
    $nationality = $bookings->nationality;
    $identification_doc = $bookings->identification_document;
    $idNo = $bookings->id_doc_num;
    $mobile_num = $bookings->mobile_num;
    $alt_contact_num = ($bookings->landline_num != 'NULL') ? $bookings->landline_num : '';
    $checkin_date = $bookings->checkin_date;
    $checkout_date = $bookings->checkout_date;
    $numNights = $bookings->no_nights;
    $arrivedFrom = $bookings->arrived_from;
    $emp_india = $bookings->employed_in_india;
    $duration_stay_india = ($bookings->duration_of_stay_in_india != 'NULL') ? $bookings->duration_of_stay_in_india : '';
    $purpose_of_visit = ($bookings->purpose_of_visit != 'NULL') ? $bookings->purpose_of_visit : '';
    $roomId = $bookings->room_no;
    $advance_amt = ($bookings->advance != 'NULL') ? $bookings->advance : '';
}

//if page was visited for confriming a reservation.
if (isset($_GET["confirmReservation"]) && !empty($_GET["confirmReservation"])){
  $reservation = findReservation($_GET["confirmReservation"]);
    $guestName = $reservation->name_of_guest;
    $checkin_date = $reservation->checkin_date;
    $checkout_date = $reservation->checkout_date;
    $numNights = $reservation->num_of_nights;
    $arrivedFrom = $reservation->coming_from;
    $mobile_num = $reservation->contact_num;
    $alt_contact_num = $reservation->alt_contact_num;
    $no_adults = $reservation->num_of_adults;
    $no_child = $reservation->num_of_children;
}

//consider having this as a function in the functions.php
if (isset($_POST['Navigate'])){
	//echo $_SESSION["strOffSet"];
	$nRecords=num_rows(mkr_query("select * from guests",$conn),$conn);
	paginate($nRecords);
	free_result($results);
	find($_SESSION["strOffSet"]);	
}

$guestid=$_POST['guestid'];  //forgotten what this does


function find($search){
	global $conn,$bookings;
	$search=$search;
	$strOffSet=!empty($_POST["strOffSet"]) ? $_POST["strOffSet"] : 0; //offset value peacked on all pages with pagination - logical error
		
	//check on wether search is being done on idno/ppno/guestid/guestname
	$sql="Select booking.booking_id,booking.name_of_guest,booking.age, booking.dependents,booking.num_of_adults,booking.num_of_children,
          booking.address,booking.nationality,booking.identification_document,booking.id_doc_num,booking.mobile_num,booking.landline_num,
          booking.checkin_date,booking.checkout_date,DATEDIFF(booking.checkout_date,booking.checkin_date) as no_nights,booking.arrived_from,
          booking.employed_in_india,booking.duration_of_stay_in_india,booking.purpose_of_visit,booking.room_no,booking.advance
		From act_booking as booking
		where booking.booking_id='$search'";
    //echo $sql;
	$results=mkr_query($sql,$conn);
	$bookings=fetch_object($results);

    //echo '<pre>';
    //print_r($bookings);

    return $bookings;
}
function findReservation($findReservation){
  global $conn;
  $search=$search;
  $strOffSet=!empty($_POST["strOffSet"]) ? $_POST["strOffSet"] : 0; //offset value peacked on all pages with pagination - logical error
    
  //check on wether search is being done on idno/ppno/guestid/guestname
  $sql="Select * from act_reservation where reservation_id = '$findReservation'";
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
		for (i=0;i<document.bookings.meal_plan.length;i++){
		if (document.bookings.meal_plan[i].checked==true)
			//alert(document.bookings.meal_plan[i].value);
			str =str + '&meal_plan=' + document.bookings.meal_plan[i].value;
		}
		
		//get direct/agent booking - should not be empty
		for (i=0;i<document.bookings.booking_type.length;i++){
		if (document.bookings.booking_type[i].checked==true)
			//alert(document.bookings.booking_type[i].value);
			str =str + '&booking_type=' + document.bookings.booking_type[i].value;
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

function RatesPeacker(){
	window.open ('rates.html', 'newwindow', config='height=100,width=400, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, directories=no, status=no');
}

//have this in cal2.js - get date differences
function nights(){
date2=(document.getElementById('departuredate').value);
date1=(document.getElementById('arrivaldate').value);
document.getElementById('no_nights').value=date2-date1;
}
//-->	 
</script>
<script language="javascript" src="js/cal2.js">
/*
Xin's Popup calendar script-  Xin Yang (http://www.yxscripts.com/)
Script featured on/available at http://www.dynamicdrive.com/
This notice must stay intact for use
*/
</script>
<script language="javascript" src="js/cal_conf2.js"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.js"></script>
<script language="javascript" src="js/scripts.js"></script>
</head>

<body>
<form action="bookings.php" method="post" name="bookings" enctype="multipart/form-data">
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
		<H2>GUEST REGISTRATION CARD (BOOKINGS)</H2>
		</td>
      </tr>
      <tr>
        <td><div id="Requests">  <table width="86%" border="0" cellpadding="4" align="left">
     <!-- <tr>
	<td colspan="4"><input type="button" name="Submit" value="Booking List" onclick="self.location='bookings_list.php'"/>
	  <input type="button" name="Submit" value="Booking Calendar" onclick="self.location='bookings_calendar.php'"/></td>
	</tr> -->
    <tr>
        <?php
        if($bookingId != '')
        {
        ?>
            <td width="20%">Booking Id</td>
            <td><?php echo $bookingId; ?></td>
        <?php
        }
        ?>

    </tr>
    <tr>
      <td width="20%">Name of Primary Guest</td>
      <td><input type="text" name="guest_name" value="<?php echo $guestName; ?>" size="30"/></td>
    </tr>
    <tr>
      <td>Age</td>
      <td><input type="text" name="age" value="<?php echo $age; ?>" size="1" maxlength="3"/></td>
    </tr>
    <tr>
        <td width="20%" valign="top">Dependents</td>
        <td><textarea name="dependents" rows="5" cols="24" maxlength="500" /><?php echo $dependents; ?></textarea></td>
    </tr>
    <tr>
        <td>No. of Guests </td>
        <td colspan="4">
            <table border="0" width="30%" cellpadding="1">
                <tr>
                    <td>Adults <br />
                        <input type="text" name="no_adults" id="no_adults" size="10" value="<?php echo $no_adults; ?>"/></td>
                    <td>Children<br />
                        <input type="text" name="no_child" size="10" value="<?php echo $no_child; ?>"/></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="20%" valign="top">Address</td>
        <td><textarea name="address" rows="5" cols="24" maxlength="255" /><?php echo $address; ?></textarea></td>
    </tr>
    <tr>
        <td>Nationality</td>
        <td>
            <select name="residence_id">
                <option value="">Select Country</option>
                <?php populate_select("countries","countrycode","country",$nationality);?>
            </select>
        </td>
    </tr>
    <tr>
        <td>Identification</td>
        <td colspan="4">
            <table border="0" width="45%" cellpadding="1">
                <tr>
                    <td>Document Type <br />
					<?php
					$selectedPP = "";
					$selectedPAN = "";
					$selectedDL = "";
					$selectedAadh = "";
					
					if($identification_doc == "Passport")
						$selectedPP = "selected";
					elseif($identification_doc == "PAN")
						$selectedPAN = "selected";
					elseif($identification_doc == "Driving License")
						$selectedDL = "selected";
					elseif($identification_doc == "Aadhar")
						$selectedAadh = "selected";
					else
						$selected = "selected";
					?>
                        <select name="identification_doc" id="identification_doc" onchange="">
                            <option value="" <?php echo $selected; ?>>Select ID Doc</option>
                            <option value="Passport" <?php echo $selectedPP; ?>>Passport</option>
                            <option value="PAN" <?php echo $selectedPAN; ?>>PAN Card</option>
                            <option value="Driving License" <?php echo $selectedDL; ?>>Driving License</option>
                            <option value="Aadhar" <?php echo $selectedAadh; ?>>Aadhar Card</option>
                        </select>
                    </td>
                    <td>ID Number <br />
                        <input type="text" name="id_no" value="<?php echo $idNo; ?>" maxlength="50"/>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td valign="top">Contact Information </td>
        <td colspan="4">
            <table border="0" width="100%" cellpadding="1">
                <tr>
                    <td width="25%">Mobile</td><td><input type="text" name="mobile_num" id="mobile_num" size="20" value="<?php echo $mobile_num; ?>" maxlength="15"/></td>
                </tr>
                <tr>
                    <td width="25%">Alternate Contact Number </td><td><input type="text" name="alt_num" size="20" maxlength="15" value="<?php echo $alt_contact_num; ?>"/></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="20%">Arrival Date </td>
        <td>
            <input type="text" name="checkin_date" id="checkin_date" readonly="" value="<?php echo $checkin_date; ?>"/>
            <a href="javascript:showCal('Calendar3')"> <img src="images/ew_calendar.gif" width="16" height="15" border="0"/></a>
            &nbsp;&nbsp;&nbsp;
            Departure Date&nbsp;&nbsp;&nbsp;<input type="text" name="checkout_date" id="checkout_date" readonly="" value="<?php echo $checkout_date; ?>" onblur="nights()"/>
            <small><a href="javascript:showCal('Calendar4')"> <img src="images/ew_calendar.gif" width="16" height="15" border="0"/></a></small>
        </td>
    </tr>
    <tr>
        <td>No. of Nights</td>
        <td><input type="text" id="num_of_nights" name="num_of_nights" value="<?php echo $numNights; ?>" size="1" maxlength="2" /></td>
    </tr>
    <tr>
        <td>Arrived From</td>
        <td><input type="text" name="arrived_from" value="<?php echo $arrivedFrom; ?>" size="20" maxlength="100" /></td>
    </tr>
    <tr>
        <td>Employed in India</td>
        <td>
		<?php
		$checkedY = '';
		$checkedN = '';
		
		if($emp_india == 'Y')
			$checkedY = 'checked';
		elseif($emp_india == 'N')
			$checkedN = 'checked';
		?>
            <input type="radio" name="emp_india" id="emp_india" value="Y" <?php echo $checkedY; ?> />Yes
            <input type="radio" name="emp_india" id="emp_india" value="N" <?php echo $checkedN; ?> />No
        </td>
    </tr>
    <tr>
        <td>Duration of Stay in India</td>
        <td><input type="text" name="duration_stay_india" value="<?php echo $duration_stay_india; ?>" size="2" maxlength="3" /> days</td>
    </tr>
    <tr>
        <td>Purpose of visit</td>
        <td><input type="text" name="purpose_of_visit" value="<?php echo $purpose_of_visit; ?>" size="20" maxlength="50" /></td>
    </tr>
     <tr>
         <td valign="top">Room Number </td>
         <td>
             <div id="showrates"></div>
             <select name="roomid" id="roomid" size="6" onchange="loadHTMLPost('ajaxfunctions.php','showrates','GetRates')" multiple>
                 <option value="" >Select Room</option>
                 <?php populate_select("rooms","roomid","roomno",$roomId);?>
             </select>
         </td>
     </tr>
    <tr>
        <td>Advance Amount</td>
        <td>INR <input type="text" name="advance_amt" id="advance_amt" value="<?php echo $advance_amt; ?>" size="5" maxlength="5" /></td>
    </tr>
     <tr>
         <td>&nbsp</td>
         <td colspan="3">
             <table border="0" cellpadding="0" width="20%">
                 <tr>
                     <td><input type="submit" name="Submit" value="Book Guest"/></td>
                     <td><input type="button" name="Submit" value="Prepare Bill" onclick="RatesPeacker()"/></td>
                 </tr>
             </table>
         </td>
     </tr>
  </table>
		</div></td>
		
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