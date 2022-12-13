<?php
echo $javascript->link(array('jquery-1.3.2.min','lib/prototype'),false);?>
<style type="text/css">
/*.messagebox-calender {
margin:10px auto;
width:161px;
}*/
.calendar .cal-week {
/* background:transparent url(/img/calender-header-bg.gif) no-repeat scroll 0 0; */
height:19px;
width:98%;
}
.calendar a {
color:#000000;text-decoration:none!important;
}
.calendar .cal-week li {
float:left;
list-style-type:none;
padding-top:4px;
text-align:center;
width:14%;
}
.calendar .cal-date_6 {
height:114px;
}
.calendar .cal-date li {
float:left;
list-style-type:none;
padding:2px 0;
text-align:center;
width:14%;
}
.calendar .cal-date {
/* background:transparent url(/img/calender-bottom-bg.gif) no-repeat scroll left bottom; */
background:#E3E9FF;
height:117px;
width:98%;
}
.current{
	font-weight:bold;
}
.selected_date{
background:#FFF7D7!important;
border:1px solid #FAD163!important;
}
</style>
<script type="text/javascript">
/** for parent message **/
var glb_month = '<?php echo strtolower(date('F'));?>';
var glb_year = '<?php echo date('Y');?>';
function updateCalender(year,month){ 
	glb_month = month;
	glb_year = year;
	/** update the agenda div **/
	

	var postUrl = SITE_URL+'users/Calendar/'+year+'/'+month;
	jQuery.ajax({
	cache:false,
	async: false,
	type: "GET",
	url: postUrl,
	success: function(msg){ 
		jQuery('#calendar_div').html(msg);
	}

  });
	
/** update the agenda div **/
}

function updateDate(date,month,year,day,monthnumber){
// alert(date);alert(month);alert(year);alert(day);alert(monthnumber);
	var display_day;
	if(day == 0){
		display_day = 'Sunday';
	} else if(day == 1){
		display_day = 'Monday';
	} else if(day == 2){
		display_day = 'Tuesday';
	} else if(day == 3){
		display_day = 'Wednesday';
	} else if(day == 4){
		display_day = 'Thursday';
	} else if(day == 5){
		display_day = 'Friday';
	} else if(day == 6){
		display_day = 'Saturday';
	}
	var sup = "";
	if (date == 1 || date == 21 || date ==31)
	{
		sup = "st";
	}
	else if (date == 2 || date == 22)
	{
		sup = "nd";
	}
	else if (date == 3 || date == 23)
	{
		sup = "rd";
	}
	else
	{
		sup = "th";
	}
	//var curr_month = d.getMonth();
	//var curr_year = d.getFullYear();
	//display_date = curr_date +  + m_names[curr_month] + " " + curr_year;
	var display_date = display_day+' '+date+"<SUP>" + sup + "</SUP>"+' '+month+', '+year;
	var event_date = year+'-'+monthnumber+'-'+date;

	jQuery('#displaydate').html(display_date);
	jQuery('#EventEventdate').val(event_date);
	
	for(var day=1;day<=31;day++){
		jQuery('#date_'+day).removeClass('selected_date');
	}
	jQuery('#date_'+date).addClass('selected_date');
}
</script>
<!--mid Content Start-->
<div class="mid-content">
	<!---<?php //echo $this->element('useraccount/user_settings_breadcrumb');?> --->
	<!--Setting Tabs Widget Start-->
	<div class="row breadcrumb-widget">
		<?php echo $this->element('useraccount/tab');?>
		<!--Tabs Content Start-->
		<div class="tabs-content">
			<!--Form Widget Start-->
			<div class="my-account-form-widget">
				<ul>
					<li>Easily create personal reminders of events to help you prepare for special occasions with the help of your Choiceful.com account</li>
					<li>Simply, select a date from calendar on the left hand side, enter the event details and we'll keep you informed of them by sending an email as the date approaches</li>
				</ul>
				 <!--Three Column Widget Start-->
				<div class="three-col-widget" id = "calendar_div">
					<?php echo $this->element('useraccount/calendar');?>
					<div class="clear"></div>
				</div>
				<!--Three Column Widget Closed-->
			</div>
			<!--Form Widget Closed-->
		</div>
		<!--Tabs Content Closed-->
	</div>
	<!--Setting Tabs Widget Closed-->
</div>
<!--- <div class="footer_line"></div>  -->
<!--mid Content Closed-->