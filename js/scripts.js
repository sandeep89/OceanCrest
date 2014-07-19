//common script file
function nights(){
var date2=document.getElementById('checkoutdate').value;
var date1=document.getElementById('checkindate').value;

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
		document.getElementById('no_nights').value=diff;
	} else{

		alert("Please provide correct checkin and end checkout date");
		document.getElementById('no_nights').value='';
	}
}else{
	document.getElementById('no_nights').value='';
}
}