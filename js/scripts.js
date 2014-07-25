//common script file
function nights(){
var date2=document.getElementById('checkout_date').value;
var date1=document.getElementById('checkin_date').value;

if(date1 && date2){
	var dateCompare = compareDates(date2, date1);

	if(dateCompare == 1){
		date2=date2.split("-");
		date2=date2[1]+"/"+date2[2]+"/"+date2[0];
		date2 = new Date(date2);

		date1=date1.split("-");
		date1=date1[1]+"/"+date1[2]+"/"+date1[0];
		date1 =new Date(date1);


		var diff = new Date(date2.getTime() - date1.getTime());
		diff = diff.getUTCDate() - 1;
		document.getElementById('num_of_nights').value=diff;
	} else{

		alert("Please provide correct checkin and end checkout date");
		document.getElementById('num_of_nights').value='';
	}
}else{
	document.getElementById('num_of_nights').value='';
}
}

function validateBooking()
{
    if(!IsEmpty(document.getElementById('guest_name'),'Please enter name of guest'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('age'),'Please enter age'))
    {
        return false;
    }
    if(!Isnumber(document.getElementById('age'), 'Please enter valid numeric value for age'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('no_adults'),'Please enter number of adults'))
    {
        return false;
    }
    if(!Isnumber(document.getElementById('no_adults'),'Please enter a non-zero numeric value for adults',true))
    {
        return false;
    }
    if(!Isnumber(document.getElementById('no_child'),'Please enter valid numeric value for children'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('address'),'Please enter guest address'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('identification_doc'),'Please select identification document type'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('id_no'),'Please enter document ID number'))
    {
        return false;
    }
    /*if(document.getElementById('duration_stay_india'))
    {
        if(!IsNumber(document.getElementById('duration_stay_india'),'Please enter valid numeric value for duration of stay in India'))
        {
            return false;
        }
    }*/
    if(!IsEmpty(document.getElementById('mobile_num'),'Please enter mobile number'))
    {
        return false;
    }
    if(!Isnumber(document.getElementById('mobile_num'),'Please enter valid numeric value for mobile number'))
    {
        return false;
    }
    if(!Isnumber(document.getElementById('landline_num'),'Please enter valid numeric value for alternate contact number'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('checkin_date'),'Please select check-in date'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('checkout_date'),'Please select checkout date'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('arrived_from'),'Please enter place arrived from'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('purpose_visit'),'Please enter purpose of visit'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('roomid'),'Please select a room'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('advance_amt'),'Please enter advance amount'))
    {
        return false;
    }
    if(!Isnumber(document.getElementById('advance_amt'),'Please enter valid numeric value for advance amount'))
    {
        return false;
    }
}

function validateReservation()
{
    if(!IsEmpty(document.getElementById('name_of_guest'),'Please enter name of guest'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('contact_num'),'Please enter primary contact number'))
    {
        return false;
    }
    if(!Isnumber(document.getElementById('contact_num'), 'Please enter valid numeric value for primary contact number'))
    {
        return false;
    }
    if(!Isnumber(document.getElementById('alt_contact_num'),'Please enter valid numeric value for alternate contact number'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('coming_from'),'Please enter details for coming from'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('checkin_date'),'Please select check-in date'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('checkout_date'),'Please select checkout date'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('num_of_adults'),'Please enter number of adults'))
    {
        return false;
    }
    if(!Isnumber(document.getElementById('num_of_adults'),'Please enter a non-zero numeric value for adults',true))
    {
        return false;
    }
    if(!Isnumber(document.getElementById('num_of_children'),'Please enter valid numeric value for children'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('num_of_rooms'),'Please enter number of rooms to be reserved'))
    {
        return false;
    }
    if(!Isnumber(document.getElementById('num_of_rooms'),'Please enter valid numeric value for number of rooms to be reserved'))
    {
        return false;
    }
    if(!IsEmpty(document.getElementById('booked_by'),'Please enter booked by details'))
    {
        return false;
    }
}


function IsEmpty(fld,msg)
{
    fld.value = Trim(fld.value);

    if((fld.value == "" || fld.value.length == 0) && (msg == ''))
    {
        return false;
    }
    if(fld.value == "" || fld.value.length == 0)
    {
        alert(msg);
        fld.focus();
        return false;
    }
    return true;
}

function Isnumber(fld,msg,boolNonZero)
{
    fld.value = Trim(fld.value);

    if(boolNonZero)
        var regex = /^[1-9]*$/;
    else
        var regex = /^[0-9]*$/;

    if(!regex.test(fld.value))
    {
        alert(msg);
        fld.focus();
        return false;
    }
    return true;
}

function Trim(fld)
{
    while(''+fld.charAt(0)==' ')
        fld=fld.substring(1,fld.length);
    while(''+fld.charAt(fld.length-1)==' ')
        fld=fld.substring(0,fld.length-1);
    //9 - horizontal tab
    //10 - line feed
    //13 - carriage return
    while(''+fld.charCodeAt(0)==13 || ''+fld.charCodeAt(0)==10 || ''+fld.charCodeAt(0)==9)
        fld=fld.substring(1,fld.length);
    while(''+fld.charCodeAt(fld.length-1)==13 || ''+fld.charCodeAt(fld.length-1)==10 || ''+fld.charCodeAt(fld.length-1)==9)
        fld=fld.substring(0,fld.length-1);
    return fld;
}