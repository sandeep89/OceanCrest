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
access("billing"); //check if user is allowed to access this page
$conn=db_connect(HOST,USER,PASS,DB,PORT);

/*if (isset($_GET["search"])){
	$search=$_GET["search"];
	find($search);
}*/

if (isset($_GET['action'])){
	$action=$_GET['action'];
	$search=$_GET['search'];
	switch ($action) {
		case 'remove':
			//before deleting make sure bill has not been printed - todo
			$bill->book_id=$_GET['billno'];
			$sql="delete from act_transactions where trans_no='$search'";
			$results=mkr_query($sql,$conn);
			$msg[0]="Sorry item not deleted";
			$msg[1]="Item successful deleted";
			AddSuccess($results,$conn,$msg);
			//go to original billno - get value from hidden field
			find($bill->book_id);
			break;
		case 'search':
			$search=$_GET["search"];
			find($search);
			break;
		case 'checkout':
			$search=$_GET["search"];
			$booking = findBooking($search);
			$bill->book_id = $booking->booking_id;
			$bill->billno = $booking->booking_id;
			$bill->roomno = get_roomno($booking->roomid);
			$bill->checkin_date = $booking->checkin_date;
			$bill->checkout_date = $booking->checkout_date;
			$bill->guest = $booking->name_of_guest;
			$bill->address = $booking->address;

			break;
}		
}

if (isset($_POST['Submit'])){
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$action=$_POST['Submit'];
	switch ($action) {
		case 'Update':
			// instantiate form validator object
			$fv=new formValidator(); //from functions.php
			//$fv->validateEmpty('doc_no','Please enter document number.');			
			$fv->validateEmpty('trans_date','Please enter date');
			$fv->validateEmpty('details','Please enter details');			
			if($fv->checkErrors()){
				// display errors
				echo "<div align=\"center\">";
				echo '<h2>Resubmit the form after correcting the following errors:</h2>';
				echo $fv->displayErrors();
				echo "</div>";
				//search current record
			}
			else {
                $bill->book_id=$_POST["billno"];
				$doc_type=$_POST["doc_type"];
				//$doc_no=$_POST["doc_no"];			
				$trans_date= $_POST["trans_date"];
				$details=$_POST["details"];	
				$amount=$_POST["amount"];
				$create_date = date("Y-m-d");
				//$dr= !empty($_POST["dr"]) ? $_POST["dr"] : 'NULL';				
				//$cr= !empty($_POST["cr"]) ? $_POST["cr"] : 'NULL';								
				//$sql="INSERT INTO transactions (billno,doc_type,doc_no,doc_date,details,dr,cr)
				// VALUES($billno,'$doc_type',$doc_no,'$doc_date',$details,$dr,$cr)";
				$sql="INSERT INTO act_transactions (bill_no,trans_date,details,amount,create_date)
					VALUES($bill->book_id,'$trans_date',$details,$amount,'$create_date')";

				$results=mkr_query($sql,$conn);		
				$msg[0]="Sorry item not posted";
				$msg[1]="Item successful posted";
				AddSuccess($results,$conn,$msg);
				find($bill->book_id); //go back to bill after updating it
				//$search=$billno;
			}
			break;
		case 'Check Out Guest':
			//Check if bill has been cleared,Change room status to vacant,print bill,mark booking status and update checkout date - to add checkoutby,codatetime in booking
			$roomno=$_POST["roomno"];
            $bill->book_id=$_POST["book_id"];
			$userid=$_SESSION["userid"];

			//change room status to vacant
			$sql="Update rooms set status='V' where roomno=$roomno";
			$results=mkr_query($sql,$conn);		
			$msg[0]="Sorry room not marked as vacant";
			$msg[1]="Room <b>$roomno</b> marked as vacant";
			AddSuccess($results,$conn,$msg);

            $sql="Update act_booking set status=2 where booking_id = $bill->book_id";
            $results=mkr_query($sql,$conn);

            //Update booking status and update checkout date - to add checkoutby,codatetime in booking
			$sql="Update booking set checkoutby=$userid,codatetime=now() where book_id=$bill->book_id";
			$results=mkr_query($sql,$conn);		
			$msg[0]="Sorry checkout details not updated.";
			$msg[1]="Checkout date and time updated.";
			AddSuccess($results,$conn,$msg);

			//print bill
			
			break;
		case 'Find':
			//check if user is searching using name, payrollno, national id number or other fields
			$search=$_POST["search"];
			find($search);
			break;
	}
}

function find($search){
	global $conn,$bill;
	$search=$search;
	//search on booking
	//check on wether search is being done on idno/ppno/guestid/guestname
	$sql="Select bills.bill_id,booking.booking_id as book_id,bills.date_billed,bills.billno,booking.name_of_guest as guest,
		booking.address, booking.checkin_date, booking.checkout_date, booking.room_no,rooms.roomno From bills
		Inner Join act_booking as booking ON bills.bill_id = booking.booking_id
		Inner Join rooms ON booking.room_no = rooms.roomid where bills.bill_id ='$search'";

	$results=mkr_query($sql,$conn);
	$bill=fetch_object($results);
}

function findBooking($search){
	global $conn,$booking;
	$search=$search;
	//search on booking
	//check on wether search is being done on idno/ppno/guestid/guestname
	$sql="Select booking_id, name_of_guest, checkin_date, checkout_date, address, room_no as roomid 
		from act_booking where booking_id='$search'";
		
	$results=mkr_query($sql,$conn);
	return fetch_object($results);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
 <link href="css/new.css" rel="stylesheet" type="text/css">
<!-- <link rel="stylesheet" type="text/css" href="css/print.css" media="print" /> -->
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
<script language="JavaScript" src="js/lib/highlight.js" type="text/javascript"></script>
<style media="print" type="text/css">
.no-print{display:None}
</style>
</head>

<body>
<form action="billings.php" method="post" enctype="multipart/form-data" id="billing" name="billing">
<table width="100%"  border="0" cellpadding="1" bgcolor="#66CCCC" align="center">
  <tr valign="top">
    <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="1">

    <?php require_once("menu_bar_header.php"); ?>

    <tr>
    <td>
    <table width="100%" border="0" cellpadding="6" cellspacing="6">
    <tr>
    <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="1">
      <tr>
        <td width="20%">
		<H2>GUEST BILLS</H2>
		</td>
      </tr>
      <tr >
        <td align="center"><div id="RequestDetails"></div>
		</td>
      </tr>
	  <tr>
        <td>
          <table width="100%"  border="0" cellpadding="1">
            <tr >
              <td width="23%">&nbsp;</td>
              <td width="26%">&nbsp;</td>
              <td width="21%">Bill No. </td>
              <td width="30%"><input type="text" name="billno" size="10" value="<?php echo $bill->book_id; ?>"/></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="hidden" name="book_id" value="<?php echo $bill->book_id; ?>"/></td>
              <td>Room No. </td>
              <td><input type="text" name="roomno" size="10" readonly="" value="<?php echo $bill->roomno; ?>"/></td>
            </tr>
            <tr>
              <td>Arrival Date </td>
              <td><input type="text" name="checkin_date" readonly="" value="<?php echo $bill->checkin_date; ?>" size="20"/></td>
              <td>Depature Date </td>
              <td><input type="text" name="checkout_date" readonly="" value="<?php echo $bill->checkout_date; ?>" size="20"/></td>
            </tr>
            <tr>
              <td>Name</td>
              <td colspan="2" bgcolor=""><?php echo $bill->guest; ?></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>Address</td>
              <td><?php
			   echo trim($bill->address);
			   ?>
	   		</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan="4" class="no-print"><?php
              echo "<table>
		<tr>
		<td width=\"30%\">Date</td>
		<td width=\"25%\">Details</td>
		<td width=\"30%\">Amount</td>
	  </tr>
		<tr><td><input type=\"text\" name=\"trans_date\" id=\"trans_date\" size=\"15\"/>
		<a href=\"javascript:showCal('Calendar7')\"> <img src=\"images/ew_calendar.gif\" width=\"16\" height=\"15\" border=\"0\"/>
		</a></td>
		<td><select name=\"details\">
			<option value=\"\">Select Item</option>";
			populate_select("details","itemid","item",0);
		  echo "</select></td>
		<td><input type=\"text\" name=\"amount\" size=\"20\"/></td>
		<td align=\"center\"><input type=\"submit\" name=\"Submit\" value=\"Update\"/></td>
		</tr></table>";
		?>
              </td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr><td colspan="4"><div id="transoption"></div></td></tr>
            <tr>
              <td colspan="4"><div id="showbill">
			  <?php
              //echo $bill->book_id;
				$billno=!empty($_POST['search']) ? $_POST['search'] : $bill->book_id;
				//$billno=!empty($_POST['billid']) ? $_POST['billid'] : 1;
				$sql="Select transactions.trans_no,transactions.trans_date,transactions.details,details.item,details.itemid,transactions.amount,transactions.bill_no
					From act_transactions as transactions
					Inner Join details ON transactions.details = details.itemid
					Where transactions.bill_no = '$billno'";
               
				$results=mkr_query($sql,$conn);
			
			  	echo "<table width=\"100%\"  border=\"0\" cellpadding=\"1\">
                  <tr bgcolor=\"#FF9900\">
                    <th class=\"no-print\">Action</th>
					<th>Date</th>
                    <th>Details</th>
                    <th>Amount</th>				
                  </tr>";
				//get data from selected table on the selected fields
				while ($trans = fetch_object($results)) {
                    if($trans->details == 1)
                        $total=$total - $trans->amount;
                    else
					    $total=$total + $trans->amount;
					//alternate row colour
					$j++;
					if($j%2==1){
						echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
						}else{
						echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
					}
						echo "<td class=\"no-print\"\"><a href=\"billings.php?search=$trans->trans_no&action=remove&billno=$trans->bill_no\"><img src=\"images/button_remove.png\" width=\"16\" height=\"16\" border=\"0\" title=\"bill guest\"/></a></td>";
						echo "<td align=\"center\">" . $trans->trans_date . "</td>";
						echo "<td align=\"center\">" . $trans->item . "</td>";
						echo "<td align=\"center\">" . $trans->amount . "</td>"; //when negative don't show
					echo "</tr>"; //end of - data rows
				} //end of while row
				  echo "</table>"; 
				  echo "<tr><td align=\"right\"><b>TOTAL:</b></td><td><b>Rs. ".$total."</b></td><tr>";?>
				  <button class="no-print" id="printbutton" value="Print Bill" onclick="window.print();return false;" >Print Bill</button>
				 </div> 
			  </td>
            </tr>
          </table></td>
      </tr>
	  <!--<tr >
        <td align="center"><div id="RequestDetails"></div>
		</td>
      </tr>-->
    </table></td>
	<td width="16%" bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellpadding="1">	  
	  <tr>
    <!--<td width="15%" bgcolor="#66CCCC">
	<table width="100%"  border="0" cellpadding="1" bgcolor="#FFFFFF" class="no-print">
       <tr>
        <td>Image</td>
      </tr>
	  <tr>
        <td align="center"><input type="button" name="Submit" value="View Bills" onclick="loadHTML('ajaxfunctions.php?submit=Bills','RequestDetails')"/></td>
	  </tr>
      <tr>
        <td align="center"><input type="submit" name="Submit" value="Check Out Guest"/></td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
        <td>
            <label> Search By:<br />
            <input type="radio" name="optFind" value="Name" />
        Room No. </label>
            <br />
            <label>
            <input type="radio" name="optFind" value="Payrollno" />
        Bill No. </label>
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
	</td>--></tr></table>
	</td>
  </tr>
   <?php require_once("footer1.php"); ?>
</table>
</form>
</body>
</html>