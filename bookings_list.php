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

if (isset($_POST['Submit'])){
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$action=$_POST['Submit'];
	switch ($action) {
		case 'List':

			return;
			break;
		case 'Find':
			//check if user is searching using name, payrollno, national id number or other fields
			$search=$_POST["search"];
			$sql="Select agentname,agents_ac_no,contact_person,telephone,fax,email,billing_address,town,postal_code,road_street,building From agents where agentcode='$search'";
			$results=mkr_query($sql,$conn);
			$agent=fetch_object($results);
			break;
	}

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
//-->	 
</script>
<script language="JavaScript" src="js/lib/highlight.js" type="text/javascript"></script>
</head>

<body>
<form action="bookings.php" method="post" enctype="multipart/form-data">
<table width="100%"  border="0" cellpadding="1" align="center" bgcolor="#66CCCC">
  <tr valign="top">

    <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="1">
            <tr>
                <td align="center"></td>
            </tr>
            <tr>
                <td>
                    <H4>OCEAN CREST RESERVATION SYSTEMS</H4> </td>
            </tr>
            <tr>
                <td><div id="Requests">
                    </div></td>

            </tr>
            <tr bgcolor="#66CCCC" >
                <td align="left">
                    <div id="RequestDetails"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <table width="100%" border="0" cellpadding="1" cellspacing="5">
                        <tr>
                            <td width="50%">
                                <ul>
                                    <li><a class="menu_link" href="home.php">Home</a></li>
                                    <!--<li><a class="menu_link" href="reservation_list.php">Reservations List</a></li>
                                    <li><a class="menu_link" href="bookings_list.php">Bookings List</a></li>-->
                                    <li><a class="menu_link" href="lookup.php">Expense Management</a></li>
                                    <li><a class="menu_link" href="reports.php">Reports</a></li>
                                    <li><a class="menu_link" href="admin.php">User Account Management</a></li>
                                    <li><a class="menu_link" href="index.php?mode=logout">Logout</a></li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table border="0" cellpadding="6" cellspacing="6">
                        <tr>
                            <td width="50%">
                                <a class="opt_link" href="reservations.php">Make a New Reservation</a>
                            </td>
                            <td width="50%">
                                <a class="opt_link" href="bookings.php">Create a New Booking</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table border="0" cellpadding="6" cellspacing="6">
                        <tr>
                            <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="1">

                                  <tr>
                                    <td>
                                    <h2>Booking List </h2>
                                    </td>
                                  </tr>
                                  <tr>
                                      <td>
                                          <?php
                                          if(isset($_GET["msgSuccess"]) && !empty($_GET["msgSuccess"])){
                                              echo "<div align=\"left\"><h1>Booking Successfully Created</h1></div>";
                                          }
                                          ?>
                                      </td>
                                  </tr>
                                  <tr>
                                    <td><div id="Requests">
                                    <?php
                                        $sql="Select booking.booking_id,booking.name_of_guest as guest,booking.checkin_date,
                                        booking.checkout_date,DATEDIFF(booking.checkout_date,booking.checkin_date) as nights,
                                        booking.num_of_adults,booking.num_of_children,rooms.roomno
                                        From hotelmis.act_booking as booking
                                        Inner Join rooms ON booking.room_no = rooms.roomid WHERE booking.status=1";
                                        $conn=db_connect(HOST,USER,PASS,DB,PORT);

                                        $results=mkr_query($sql,$conn);

                                        echo "<table>";
                                        //get field names to create the column header
                                        echo "<tr bgcolor=\"#009999\">
                                            <th colspan=\"2\">Action</th>
                                            <th>Booking Id</th>
                                            <th>Room No.</th>
                                            <th>Guest</th>
                                            <th>Check-In Date</th>
                                            <th>Check-Out Date</th>
                                            <th>Nights</th>
                                            <th>Adults</th>
                                            <th>Children</th>
                                            </tr>";
                                            //end of field header
                                            //get data from selected table on the selected fields
                                        while ($booking = fetch_object($results)) {
                                        //alternate row colour
                                            $j++;
                                            if($j%2==1){
                                                echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
                                                }else{
                                                echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
                                            }
                                                echo "<td><a href=\"bookings.php?search=$booking->booking_id\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view booking details\"/></a></td>";
                                                echo "<td><a href=\"billings.php?action=checkout&search=$booking->booking_id\"><img src=\"images/button_signout.png\" width=\"16\" height=\"16\" border=\"0\" title=\"bill guest\"/></a></td>";
                                      echo "<td>" . $booking->booking_id . "</td>";
                                      echo "<td>" . $booking->roomno . "</td>";
                                                echo "<td>" . trim($booking->guest) . "</td>";
                                                echo "<td>" . $booking->checkin_date . "</td>";
                                                echo "<td>" . $booking->checkout_date . "</td>";
                                                echo "<td>" . $booking->nights . "</td>";
                                                echo "<td>" . $booking->num_of_adults . "</td>";
                                                echo "<td>" . $booking->num_of_children . "</td>";
                                            echo "</tr>"; //end of - data rows
                                        } //end of while row
                                        echo "</table>";
                                    ?>
                                    </div></td>
                                  </tr>
                                </table></td>

                        </tr>
                    </table></td>
  </tr>
   <?php require_once("footer1.php"); ?>
</table>
</form>
</body>
</html>