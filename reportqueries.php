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
/*
to put export feature use somehting like below
	<li><a href="#" onClick ="$('#customers').tableExport({type:'json',escape:'false'});"> <img src='icons/json.png' width='24px'> JSON</a></li>
								<li><a href="#" onClick ="$('#customers').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"> <img src='icons/json.png' width='24px'> JSON (ignoreColumn)</a></li>
								<li><a href="#" onClick ="$('#customers').tableExport({type:'json',escape:'true'});"> <img src='icons/json.png' width='24px'> JSON (with Escape)</a></li>
								<li class="divider"></li>
								<li><a href="#" onClick ="$('#customers').tableExport({type:'xml',escape:'false'});"> <img src='icons/xml.png' width='24px'> XML</a></li>
								<li><a href="#" onClick ="$('#customers').tableExport({type:'sql'});"> <img src='icons/sql.png' width='24px'> SQL</a></li>
								<li class="divider"></li>
								<li><a href="#" onClick ="$('#customers').tableExport({type:'csv',escape:'false'});"> <img src='icons/csv.png' width='24px'> CSV</a></li>
								<li><a href="#" onClick ="$('#customers').tableExport({type:'txt',escape:'false'});"> <img src='icons/txt.png' width='24px'> TXT</a></li>
								<li class="divider"></li>				
								
								<li><a href="#" onClick ="$('#customers').tableExport({type:'excel',escape:'false'});"> <img src='icons/xls.png' width='24px'> XLS</a></li>
								<li><a href="#" onClick ="$('#customers').tableExport({type:'doc',escape:'false'});"> <img src='icons/word.png' width='24px'> Word</a></li>
								<li><a href="#" onClick ="$('#customers').tableExport({type:'powerpoint',escape:'false'});"> <img src='icons/ppt.png' width='24px'> PowerPoint</a></li>
								<li class="divider"></li>
								<li><a href="#" onClick ="$('#customers').tableExport({type:'png',escape:'false'});"> <img src='icons/png.png' width='24px'> PNG</a></li>
								<li><a href="#" onClick ="$('#customers').tableExport({type:'pdf',escape:'false'});"> <img src='icons/pdf.png' width='24px'> PDF</a></li>
								

*/
error_reporting(E_ALL & ~E_NOTICE);
include_once ("queryfunctions.php");
include_once ("functions.php");
$conn=db_connect(HOST,USER,PASS,DB,PORT);

//bookedguests();
$gueststatus = $_POST["button"];
switch ($gueststatus){
	case "all":
		//call same function only thing to change is sql statement and actions
		$sql="Select guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as guest,guests.pp_no,
			guests.idno,guests.pobox,guests.town,guests.postal_code,guests.phone,
			guests.email,guests.mobilephone,countries.country
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode";
		guestslist($sql);
		//allguests();		
		break;
	case "booked":
		$sql="Select guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as guest,guests.pp_no,
			guests.idno,guests.pobox,guests.town,guests.postal_code,guests.phone,
			guests.email,guests.mobilephone,countries.country,booking.codatetime
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode
			Inner Join booking ON guests.guestid = booking.guestid
			Where isnull(booking.codatetime)";
		guestslist($sql);
		break;		
	case "reserved":
		$sql="Select guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as guest,guests.pp_no,
			guests.idno,guests.pobox,guests.town,guests.postal_code,guests.phone,
			guests.email,guests.mobilephone,countries.country,reservation.reserve_checkindate
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode
			Inner Join reservation ON guests.guestid = reservation.guestid
			Where reservation.reserve_checkindate >= current_date()"; //date variable user to select a date for arrivals to do
		break;
	case "arrivals":
		$sql="Select guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as guest,guests.pp_no,
			guests.idno,guests.pobox,guests.town,guests.postal_code,guests.phone,
			guests.email,guests.mobilephone,countries.country,reservation.reserve_checkindate
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode
			Inner Join reservation ON guests.guestid = reservation.guestid
			Where reservation.reserve_checkindate >= current_date()";
		break;
	case "departures":
		$sql="Select guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as guest,guests.pp_no,
			guests.idno,guests.pobox,guests.town,guests.postal_code,guests.phone,
			guests.email,guests.mobilephone,countries.country
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode
			Inner Join booking ON guests.guestid = booking.guestid
			Where booking.checkout_date=current_date()";
		guestslist($sql);
		break;
	case "dep_summ":
		$sql="Select rooms.roomno as RoomNo,transactions.doc_no as DocNo,transactions.doc_type,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) AS Name,
		transactions.dr as Debit,transactions.cr as Credit,details.item as Remarks,transactions.doc_date as DocDate
		From transactions
		left Join details ON transactions.details = details.itemid
		left Join bills ON transactions.billno = bills.billno
		Inner Join booking ON bills.book_id = booking.book_id
		Inner Join guests ON booking.guestid = guests.guestid
		Inner Join rooms ON booking.roomid = rooms.roomid";
		//Where transactions.details = '$details' and transactions.doc_date = '$date'
		echo "<table>
		<tr><td><h2>Departmental Summary Control Sheet</h2></td></tr>
		<tr>
		<td>Department: <select name=\"itemid\" id=\"itemid\"\">
        <option value=\"All\" >All</option>";
        populate_select("details","itemid","item",$details);
      	echo "</select></td>
		<td>Date:<input name=\"date\" id=\"date\" type=\"text\" size=\"10\" readonly=\"true\">
		 <small><a href=\"javascript:showCal('Calendar8')\"> <img src=\"images/ew_calendar.gif\" width=\"16\" height=\"15\" border=\"0\"/></a></small>
		</td>
		<td><input name=\"submit\" type=\"submit\" value=\"Submit\"></td>
		</tr>
		</table>";
		getdata();
		break;
	case "canceled_reservations":
		$sql = "select * from act_reservation where status=0";
		reservationlist($sql);
	
}				

function reservationlist($sql){
	//global $gueststatus;
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$results=mkr_query($sql,$conn);
	echo "<table id=\"canceled-reservation\"border=\"1\" align=\"center\">";
	//get field names to create the column header
	echo "<thead>";
	echo "<tr bgcolor=\"#009999\">
				<th colspan=\"1\">Action</th>
        		<th>Registration Id</th>
        		<th>Reserved by</th>
				<th>Guest Name</th>
				<th>Check-In Date</th>
				<th>Check-Out Date</th>
				<th>Nights</th>
				<th>Adults</th>
				<th>Children</th>
				</tr>";
	echo "</thead>";
	echo "<tbody>";
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
					echo "<td>" . $reservation->reservation_id. "</td>";	
					echo "<td>" . $reservation->booked_by. "</td>";				
					echo "<td>" . trim($reservation->name_of_guest) . "</td>";
					echo "<td>" . $reservation->checkin_date . "</td>";
					echo "<td>" . $reservation->checkout_date . "</td>";
					echo "<td>" . $reservation->num_of_nights . "</td>";			
					echo "<td>" . $reservation->num_of_adults . "</td>";
					echo "<td>" . $reservation->num_of_children . "</td>";
				echo "</tr>"; //end of - data rows
			} //end of while row
	echo "</tbody>";
			echo "</table>";
			echo "<a href=\"#\" onClick =\"$('#canceled-reservation').tableExport({type:'excel',escape:'false'});\"> Export to Excel</a>";
		

}
function guestslist($sql){
	//global $gueststatus;
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$results=mkr_query($sql,$conn);
	echo "<table align=\"center\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th colspan=\"4\">Action</th>
		<th>Guest</th>
		<th>PP. No./ID. No.</th>
		<th>Mobile</th>
		<th>Phone</th>
		<th>Email</th>
		<th>P. O. Box</th>
		<th>Town-Postal code</th>
		</tr>";
	//end of field header
	//get data from selected table on the selected fields
	while ($guest = fetch_object($results)) {
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
			echo "<td><a href=\"guests.php?search=$guest->guestid\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view guests details\"/></a></td>";
			echo "<td><a href=\"bookings.php?search=$guest->guestid\"><img src=\"images/bed.jpg\" width=\"16\" height=\"16\" border=\"0\" title=\"book guest\"/></a></td>";
			echo "<td><a href=\"reservations.php?search=$guest->guestid\"><img src=\"images/bed2.jpg\" width=\"16\" height=\"16\" border=\"0\" title=\"guest reservtion\"/></a></td>";
			echo "<td><a href=\"billings.php?search=$guest->guestid\"><img src=\"images/button_signout.png\" width=\"16\" height=\"16\" border=\"0\" title=\"bill guest\"/></a></td>";
			echo "<td>" . trim($guest->guest) . "</td>";
			echo "<td>" . $guest->pp_no . "/" .$guest->idno . "</td>";
			echo "<td>" . $guest->mobilephone . "</td>";
			echo "<td>" . $guest->phone . "</td>";
			echo "<td>" . $guest->email . "</td>";
			echo "<td>" . $guest->pobox . "</td>";					
			echo "<td>" . $guest->town . '-' . $guest->postal_code . "</td>";
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "</table>";
}

function getdata(){
	global $sql,$conn;
	$results=mkr_query($sql,$conn);
	/*$totRows = mysql_query("SELECT FOUND_ROWS()"); //get total number of records in the select query irrespective of the LIMIT clause
	$totRows = mysql_result($totRows , 0);
	$_SESSION["nRecords"]=$totRows;	
	$_SESSION["totPages"]=ceil($totRows/$strRows);
	$_SESSION["RowsDisplayed"]=$strRows;*/
	echo "<table align=\"center\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th>Action</th>";
		while ($i < mysql_num_fields($results)) {
				$meta = mysql_fetch_field($results, $i);
				$field=$meta->name;
				echo "<th>" . $field . "</th>";
				$i++;
			}		
		"</tr>";
	//end of field header
	if  ((int)$results!==0){
	//get data from selected table on the selected fields
	while ($row = fetch_object($results)) {
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
			echo "<td><a href=\"reportqueries.php?search=$row->ID\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view\"/></a></td>";
			$i = 0;
			while ($i < mysql_num_fields($results)) {
				$meta = mysql_fetch_field($results, $i);
				$field=$meta->name;
				echo "<td>" . $row->$field . "</td>";
				$i++;
			}
			//			
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "</table>";
	}
	free_result($results);
}
"Select
rooms.roomno,
guests.lastname,
guests.firstname,
guests.middlename,
booking.checkin_date,
booking.checkout_date,
booking.bk_date
From
rooms
Inner Join booking ON rooms.roomid = booking.roomid
Inner Join guests ON booking.guestid = guests.guestid
Where
year(booking.checkin_date ) = '2006' AND
month(booking.checkin_date ) = '1'
Order By
booking.checkin_date Asc";
?>