<?php
/**
 * Created by PhpStorm.
 * User: Harsh Aghicha
 * Date: 2/8/14
 * Time: 8:25 PM
 */
include_once("login_check.inc.php");
include_once ("queryfunctions.php");
include_once ("functions.php");
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
</head>

<body>
<form action="index.php" method="post" enctype="multipart/form-data">
<table width="102%"  border="0" cellpadding="1" bgcolor="#66CCCC" align="center">
  <tr valign="top">

    <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="1">
            <?php require_once("menu_bar_header.php"); ?>
            <tr>
                <td>
                    <table border="0" cellpadding="6" cellspacing="6">
                        <tr>
                            <td width="50%" valign="top">
                                <table>
                                    <tr>
                                        <td><h2>Reservation List </h2></td>
                                        <td>(<a href="reservation_list.php">See All</a>)</td>
                                    </tr>
                                </table>

                                <div id="Requests">
                                    <?php
                                    $sql="Select * from act_reservation where status=1 order by reservation_id desc limit 20";
                                    $conn=db_connect(HOST,USER,PASS,DB,PORT);
                                    $results=mkr_query($sql,$conn);

                                    echo "<table>";
                                    //get field names to create the column header
                                    echo "<tr bgcolor=\"#009999\">
                                    <!--<th colspan=\"3\">Action</th>-->
                                    <th>Sr. No.</th>
                                    <th>Registration Id</th>
                                    <th>Guest Name</th>
                                    <th>Check-In Date</th>
                                    <th>Check-Out Date</th>
                                    <th>Nights</th>
                                    <!--<th>Adults</th>
                                    <th>Children</th>-->
                                    </tr>";
                                    //end of field header
                                    //get data from selected table on the selected fields
                                    $j = 0;
                                    while ($reservation = fetch_object($results)) {
                                        //alternate row colour
                                        $j++;
                                        if($j%2==1){
                                            echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
                                        }else{
                                            echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
                                        }
                                        /*echo "<td><a href=\"reservations.php?search=$reservation->reservation_id\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view/edit reservation\"/></a></td>";
                                        echo "<td><a href=\"bookings.php?confirmReservation=$reservation->reservation_id\"><img src=\"images/bed.jpg\" width=\"16\" height=\"16\" border=\"0\" title=\"book guest\"/></a></td>";
                                        echo "<td><a href=\"#\" onclick=\"confirmDeletion($reservation->reservation_id)\"><img src=\"images/button_remove.png\" width=\"16\" height=\"16\" border=\"0\" title=\"delete reservation\"/></a></td>";*/
                                        echo "<td>" . $j. "</td>";
                                        echo "<td>" . $reservation->reservation_id. "</td>";
                                        echo "<td>" . trim($reservation->name_of_guest) . "</td>";
                                        echo "<td>" . $reservation->checkin_date . "</td>";
                                        echo "<td>" . $reservation->checkout_date . "</td>";
                                        echo "<td>" . $reservation->num_of_nights . "</td>";
                                        /*echo "<td>" . $reservation->num_of_adults . "</td>";
                                        echo "<td>" . $reservation->num_of_children . "</td>";*/
                                        echo "</tr>"; //end of - data rows
                                    } //end of while row
                                    echo "</table>";
                                    ?>
                                </div>
                            </td>
                            <td width="50%" valign="top">
                                <table>
                                    <tr>
                                        <td><h2>Bookings List </h2></td>
                                        <td>(<a href="bookings_list.php">See All</a>)</td>
                                    </tr>
                                </table>
                                <div id="Requests">
                                    <?php
                                    $sql="Select booking.booking_id,booking.name_of_guest as guest,booking.checkin_date,
                                            booking.checkout_date,DATEDIFF(booking.checkout_date,booking.checkin_date) as nights,
                                            booking.num_of_adults,booking.num_of_children,rooms.roomno
                                            From hotelmis.act_booking as booking
                                            Inner Join rooms ON booking.room_no = rooms.roomid WHERE booking.status=1 order by booking.booking_id desc limit 20";
                                    $conn=db_connect(HOST,USER,PASS,DB,PORT);

                                    $results=mkr_query($sql,$conn);

                                    echo "<table>";
                                    //get field names to create the column header
                                    echo "<tr bgcolor=\"#009999\">
                                    <th>Sr. No.</th>
                                    <th>Booking Id</th>
                                    <th>Room No.</th>
                                    <th>Guest</th>
                                    <th>Check-In Date</th>
                                    <th>Check-Out Date</th>
                                    <th>Nights</th>
                                    <!--<th>Adults</th>
                                    <th>Children</th>-->
                                    </tr>";
                                    //end of field header
                                    //get data from selected table on the selected fields
                                    $j = 0;
                                    while ($booking = fetch_object($results)) {
                                        //alternate row colour
                                        $j++;
                                        if($j%2==1){
                                            echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
                                        }else{
                                            echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
                                        }
                                        /*echo "<td><a href=\"bookings.php?search=$booking->booking_id\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view booking details\"/></a></td>";
                                        echo "<td><a href=\"billings.php?action=checkout&search=$booking->booking_id\"><img src=\"images/button_signout.png\" width=\"16\" height=\"16\" border=\"0\" title=\"bill guest\"/></a></td>";*/
                                        echo "<td>" . $j. "</td>";
                                        echo "<td>" . $booking->booking_id . "</td>";
                                        echo "<td>" . $booking->roomno . "</td>";
                                        echo "<td>" . trim($booking->guest) . "</td>";
                                        echo "<td>" . $booking->checkin_date . "</td>";
                                        echo "<td>" . $booking->checkout_date . "</td>";
                                        echo "<td>" . $booking->nights . "</td>";
                                        /*echo "<td>" . $booking->num_of_adults . "</td>";
                                        echo "<td>" . $booking->num_of_children . "</td>";*/
                                        echo "</tr>"; //end of - data rows
                                    } //end of while row
                                    echo "</table>";
                                    ?>
                                </div>
                            </td>
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
</table>
</td>
</tr>
</table></td>
</tr>
<!--<tr><td><a href="www.php.net" target="_blank"><img src="images/php-power-white.gif" width="88" height="31" border="0" /></a><a href="www.mysql.com" target="_blank"><img src="images/powered-by-mysql-88x31.png" width="88" height="31" border="0" /></a></td>
<td>TaifaTech Networks &copy; 2006. Vers 1.0 <a href="http://sourceforge.net"><img src="http://sflogo.sourceforge.net/sflogo.php?group_id=172638&amp;type=1" width="88" height="31" border="0" alt="SourceForge.net Logo" /></a></td>
  </tr>-->
</table>
</form>
</body>
</html>