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

if (isset($_GET['action'])){
	$action=$_GET['action'];
	$search=$_GET['search'];
	switch ($action) {
		case 'remove':
			//before deleting check if deposit had been made and mark for refund - todo
			//release reserved room - todo
			$sql="update act_reservation set status=0 where reservation_id='$search'";
			$results=mkr_query($sql,$conn);
			$msg[0]="Sorry reservation not deleted";
			$msg[1]="Reservation Cancelled Successfully";
			AddSuccess($results,$conn,$msg);
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
function confirmDeletion(id){

 	var confirmation = confirm("Are you sure you want to delete this reservation!");
    if (confirmation == true) {
         window.location.assign("reservation_list.php?search="+ id +"&action=remove");
    } 
}
//-->	 
</script>
<script language="JavaScript" src="js/highlight.js" type="text/javascript"></script>
</head>

<body>
<form action="reservation.php" method="post" enctype="multipart/form-data">
<table width="100%"  border="0" cellpadding="1" align="center" bgcolor="#66CCCC">
<tr valign="top">

    <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="1">
            <?php require_once("menu_bar_header.php"); ?>

            <tr>
                <td>
                    <table border="0" cellpadding="6" cellspacing="6">
                        <tr>
                            <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="1">

                                    <tr>
                                        <td>
                                            <h2>Reservation List </h2>
                                        </td>
                                        <td align="right">
                                            <a href="home.php">Back to Dashboard</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <?php
                                            if(isset($_GET["msgSuccess"]) && !empty($_GET["msgSuccess"])){
                                                echo "<div align=\"left\"><h1>Reservation Successfully Created</h1></div>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><div id="Requests">
                                                <?php
                                                $sql="Select * from act_reservation where status=1";
                                                $conn=db_connect(HOST,USER,PASS,DB,PORT);
                                                $results=mkr_query($sql,$conn);

                                                echo "<table>";
                                                //get field names to create the column header
                                                echo "<tr bgcolor=\"#009999\">
                                                <th colspan=\"3\">Action</th>
                                                <th>Registration Id</th>
                                                <th>Guest Name</th>
                                                <th>Check-In Date</th>
                                                <th>Check-Out Date</th>
                                                <th>Nights</th>
                                                <th>Adults</th>
                                                <th>Children</th>
                                                </tr>";
                                                //end of field header
                                                //get data from selected table on the selected fields
                                                while ($reservation = fetch_object($results)) {
                                                    //alternate row colour
                                                    $j++;
                                                    if($j%2==1){
                                                        echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
                                                    }else{
                                                        echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
                                                    }
                                                    echo "<td><a href=\"reservations.php?search=$reservation->reservation_id\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view/edit reservation\"/></a></td>";
                                                    echo "<td><a href=\"bookings.php?confirmReservation=$reservation->reservation_id\"><img src=\"images/bed.jpg\" width=\"16\" height=\"16\" border=\"0\" title=\"book guest\"/></a></td>";
                                                    echo "<td><a href=\"#\" onclick=\"confirmDeletion($reservation->reservation_id)\"><img src=\"images/button_remove.png\" width=\"16\" height=\"16\" border=\"0\" title=\"delete reservation\"/></a></td>";
                                                    echo "<td>" . $reservation->reservation_id. "</td>";
                                                    echo "<td>" . trim($reservation->name_of_guest) . "</td>";
                                                    echo "<td>" . $reservation->checkin_date . "</td>";
                                                    echo "<td>" . $reservation->checkout_date . "</td>";
                                                    echo "<td>" . $reservation->num_of_nights . "</td>";
                                                    echo "<td>" . $reservation->num_of_adults . "</td>";
                                                    echo "<td>" . $reservation->num_of_children . "</td>";
                                                    echo "</tr>"; //end of - data rows
                                                } //end of while row
                                                echo "</table>";
                                                ?>
                                            </div></td>
                                    </tr>

                                </table></td>

                        </tr>
                    </table>
                </td>
            </tr>

            <!--<td width="20%">

                </td>
                    <table width="20%" border="1" cellpadding="1" cellspacing="5">
                        <tr>
                            <td bgcolor="#66CCCC">
                                <table cellspacing=0 cellpadding=0 border="0" width="100%" align="left" bgcolor="#FFFFFF">
                                    <tr><td align="center"><a href="index.php"><img src="images/OpenCrest.gif" width="100%" height="100%" border="0"/><br>Home</a></td></tr>
                                    <tr><td width="110"> Username:<br><input name="username" type="text" width="10"></input> </td></tr>
                                    <tr><td> Password: <br><input name="password" type="password" width="10"></input></td></tr>
                                    <tr>
                                        <td align="center">
                                            <?php signon(); ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
-->

            <!--<tr><td align="center"><div onclick="loadHTML('futures.php','RequestDetails')" style="cursor:pointer"><h2>Futures</h2></div></td></tr>		-->

</tr>
  </table>
	</td>
  </tr>
   <?php require_once("footer1.php"); ?>
</table>
</form>
</body>
</html>