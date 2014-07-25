<?php
$conn=db_connect(HOST,USER,PASS,DB,PORT);

if (isset($_POST['Submit'])){
    $action=$_POST['Submit'];
    switch ($action) {
        case 'Book Guest':
            //if guest has not been selected exit
            // instantiate form validator object
            /*$fv=new formValidator(); //from functions.php
            if (empty($_POST["guestid"])){ //if no guest has been selected no point in displaying other errors
                $fv->validateEmpty('guestid','Sorry no guest information is available for booking');
            }else{
                $fv->validateEmpty('no_adults','Please indicate number of people booking');
                $fv->validateEmpty('booking_type','Please indicate if it\'s a Direct booking or Agent booking.');
                $fv->validateEmpty('meal_plan','Please select Meal Plan');
                $fv->validateEmpty('roomid','Please indicate room being booked');
            }

            if($fv->checkErrors()){
                // display errors
                echo "<div align=\"center\">";
                echo '<h2>Resubmit the form after correcting the following errors:</h2>';
                echo $fv->displayErrors();
                echo "</div>";
            }
            else {*/
            $name_of_guest = !empty($_POST["guest_name"]) ? $_POST["guest_name"] : 'NULL';
            $age = !empty($_POST["age"]) ? $_POST["age"] : 'NULL';
            $dependents = !empty($_POST["dependents"]) ? $_POST["dependents"]  : 'NULL';
            $no_adults = !empty($_POST["no_adults"]) ? $_POST["no_adults"]  : 'NULL';
            $no_child = !empty($_POST["no_child"]) ? $_POST["no_child"]  : 'NULL';
            $address = !empty($_POST["address"]) ? $_POST["address"]  : 'NULL';
            $nationality = !empty($_POST["residence_id"]) ? $_POST["residence_id"]  : 'NULL';
            $identification_doc = !empty($_POST["identification_doc"]) ? $_POST["identification_doc"]  : 'NULL';
            $id_no = !empty($_POST["id_no"]) ? $_POST["id_no"]  : 'NULL';
            $mobile_num = !empty($_POST["mobile_num"]) ? $_POST["mobile_num"]  : 'NULL';
            $alt_num = !empty($_POST["alt_num"]) ? $_POST["alt_num"]  : 'NULL';
            $checkin_date = !empty($_POST["checkin_date"]) ? $_POST["checkin_date"]  : 'NULL';
            $checkout_date = !empty($_POST["checkout_date"]) ? $_POST["checkout_date"]  : 'NULL';
            $num_of_nights = !empty($_POST["num_of_nights"]) ? $_POST["num_of_nights"]  : 'NULL';
            $arrived_from = !empty($_POST["arrived_from"]) ? $_POST["arrived_from"]  : 'NULL';
            $emp_india = !empty($_POST["emp_india"]) ? $_POST["emp_india"]  : 'NULL';
            $duration_stay_india = !empty($_POST["duration_stay_india"]) ? $_POST["duration_stay_india"]  : 'NULL';
            $purpose_of_visit = !empty($_POST["purpose_of_visit"]) ? $_POST["purpose_of_visit"]  : 'NULL';
            $roomid = !empty($_POST["roomid"]) ? $_POST["roomid"]  : 'NULL';
            $advance_amt = !empty($_POST["advance_amt"]) ? $_POST["advance_amt"]  : 'NULL';
            $reservationId = !empty($_POST["reservationId"]) ? $_POST["reservationId"]  : 'NULL';

            $sql="INSERT INTO act_booking (name_of_guest,age,dependents,num_of_adults,num_of_children,address,nationality,identification_document,id_doc_num,
                                          mobile_num,landline_num,checkin_date,checkout_date,no_of_nights,arrived_from,employed_in_india,duration_of_stay_in_india,
                                          purpose_of_visit,room_no,advance)
				  VALUES('".addslashes($name_of_guest)."','".$age."','".addslashes($dependents)."','".$no_adults."','".$no_child."','".addslashes($address)."','".$nationality."'
				        ,'".$identification_doc."','".addslashes($id_no)."','".$mobile_num."','".$alt_num."','".$checkin_date."','".$checkout_date."','".$num_of_nights."','".addslashes($arrived_from)."'
				        ,'".$emp_india."','".addslashes($duration_stay_india)."','".addslashes($purpose_of_visit)."','".$roomid."','".$advance_amt."')";
            $results=mkr_query($sql,$conn);
            if ((int) $results==0){
                //should log mysql errors to a file instead of displaying them to the user
                echo 'Invalid query: ' . mysql_errno($conn). "<br>" . ": " . mysql_error($conn). "<br>";
                echo "Guests NOT BOOKED.";  //return;
            }else{
                //update reservation status if a registration is confirmed
                if($reservationId != ''){
                    $sql = "UPDATE act_reservation SET status=2 where reservation_id = $reservationId";
                    $results=mkr_query($sql,$conn);

                    //$sql = "UPDATE rooms SET status='B' where roomid = $roomid";
                    //$results=mkr_query($sql,$conn);
                    // create a audit log for the reservation
                    // Need to check why we do not have a user name who is confirming the booking
                }
                 echo "<div align=\"center\"><h1>Guests successful checked in.</h1></div>";

                    $sql="INSERT INTO bills (book_id,billno,date_billed) select
                    booking.booking_id,booking.booking_id,booking.checkin_date from act_booking as booking where booking.billed=0";
                    $results=mkr_query($sql,$conn);
                    $msg[0]="Sorry no bill created";
                    $msg[1]="Bill successfull created";
                    AddSuccess($results,$conn,$msg);

                    //if bill succesful created update billed to 1 in bookings- todo
                    $sql="Update act_booking set billed=1 where billed=0"; //get the actual updated book_id, currently this simply updates all bookings
                    $results=mkr_query($sql,$conn);
                    $msg[0]="Sorry Booking not updated";
                    $msg[1]="Booking successful updated";
                    AddSuccess($results,$conn,$msg);

                    //mark room as booked
                    $sql="Update rooms set status='B' where roomid=$roomid"; //get the actual updated book_id, currently this simply updates all bookings
                    $results=mkr_query($sql,$conn);
                    $msg[0]="Sorry room occupation not marked";
                    $msg[1]="Room marked as occupied";
                    AddSuccess($results,$conn,$msg);
            }
            break;
        case 'Find':
            //check if user is searching using name, payrollno, national id number or other fields
            $search=$_POST["search"];
            find($search);
            $sql="Select guests.guestid,guests.lastname,guests.firstname,guests.middlename,guests.pp_no,
			guests.idno,guests.countrycode,guests.pobox,guests.town,guests.postal_code,guests.phone,
			guests.email,guests.mobilephone,countries.country
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode where pp_no='$search'
			LIMIT $strOffSet,1";
            $results=mkr_query($sql,$conn);
            $bookings=fetch_object($results);
            break;
    }
}

?>