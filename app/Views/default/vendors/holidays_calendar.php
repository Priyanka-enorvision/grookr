<?php
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\SystemModel;
use App\Models\ConstantsModel;
use App\Models\HolidaysModel;
//$encrypter = \Config\Services::encrypter();
$SystemModel = new SystemModel();
$RolesModel = new RolesModel();
$UsersModel = new UsersModel();
$ConstantsModel = new ConstantsModel();
$HolidaysModel = new HolidaysModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
if($user_info['user_type'] == 'staff'){
	$not_published = $HolidaysModel->where('company_id',$user_info['company_id'])->where('is_publish',0)->orderBy('holiday_id', 'ASC')->findAll();
	$published = $HolidaysModel->where('company_id',$user_info['company_id'])->where('is_publish',1)->orderBy('holiday_id', 'ASC')->findAll();
} else {
	$not_published = $HolidaysModel->where('company_id',$usession['sup_user_id'])->where('is_publish',0)->orderBy('holiday_id', 'ASC')->findAll();
	$published = $HolidaysModel->where('company_id',$usession['sup_user_id'])->where('is_publish',1)->orderBy('holiday_id', 'ASC')->findAll();
}
?>
<?php
$events_date = date('Y-m');
?>
<script type="text/javascript">
$(document).ready(function(){
	
	/* initialize the calendar
	-----------------------------------------------------------------*/
	// $('#calendar_hr').fullCalendar({
	// 	header: {
	// 		left: 'prev,next today',
	// 		center: 'title',
	// 		right: 'month,agendaWeek,agendaDay,listWeek'
	// 	},
	// 	/*views: {
	// 		listDay: { buttonText: 'list day' },
	// 		listWeek: { buttonText: 'list week' }
	// 	  },*/
	// 	//defaultView: 'agendaWeek',
	// 	themeSystem: 'bootstrap4',
	// 	/*bootstrapFontAwesome: {
	// 	  close: ' ion ion-md-close',
	// 	  prev: ' ion ion-ios-arrow-back scaleX--1-rtl',
	// 	  next: ' ion ion-ios-arrow-forward scaleX--1-rtl',
	// 	  prevYear: ' ion ion-ios-arrow-dropleft-circle scaleX--1-rtl',
	// 	  nextYear: ' ion ion-ios-arrow-dropright-circle scaleX--1-rtl'
	// 	},*/
		  
	// 	eventRender: function(event, element) {
	// 	element.attr('title',event.title).tooltip();
	// 	element.attr('href', event.urllink);
		
	// 	},
	// 	dayClick: function(date, jsEvent, view) {
    //     date_last_clicked = $(this);
	// 		var event_date = date.format();
	// 		$('#exact_date').val(event_date);
	// 		var eventInfo = $("#module-opt");
    //         var mousex = jsEvent.pageX + 20; 
    //         var mousey = jsEvent.pageY + 20; 
    //         var tipWidth = eventInfo.width(); 
    //         var tipHeight = eventInfo.height(); 

    //         var tipVisX = $(window).width() - (mousex + tipWidth);
    //         var tipVisY = $(window).height() - (mousey + tipHeight);

    //         if (tipVisX < 20) { 
    //             mousex = jsEvent.pageX - tipWidth - 20;
    //         } if (tipVisY < 20) {
    //             mousey = jsEvent.pageY - tipHeight - 20;
    //         }
    //         eventInfo.css({ top: mousey, left: mousex });
    //         eventInfo.show(); 
	// 	},
	// 	defaultDate: '<?php echo $events_date;?>',
	// 	eventLimit: true, 
	// 	navLinks: true, 
	// 	selectable: true,
	// 	events: [

	// 		// {
	// 		// 	event_id: 'gov-holiday-1',
	// 		// 	unq: '1',
	// 		// 	title: 'New Year\'s Day',
	// 		// 	start: '2024-01-01',
	// 		// 	end: '2024-01-01',
	// 		// 	color: '#ff5733 !important' 
	// 		// },
	// 		// {
	// 		// 	event_id: 'gov-holiday-2',
	// 		// 	unq: '1',
	// 		// 	title: 'Independence Day',
	// 		// 	start: '2024-07-04',
	// 		// 	end: '2024-07-04',
	// 		// 	color: '#ff5733 !important'
	// 		// },
	// 		// {
	// 		// 	event_id: 'gov-holiday-3',
	// 		// 	unq: '1',
	// 		// 	title: 'Christmas Day',
	// 		// 	start: '2024-12-25',
	// 		// 	end: '2024-12-25',
	// 		// 	color: '#ff5733 !important'
	// 		// },
	// 		<?php foreach($not_published as $ntpublished):?>
	// 		{
	// 			event_id: '<?php echo $ntpublished['holiday_id']?>',
	// 			unq: '0',
	// 			title: '<?php echo $ntpublished['event_name']?>',
	// 			start: '<?php echo $ntpublished['start_date']?>',
	// 			end: '<?php echo $ntpublished['end_date']?>',
	// 			// urllink: '<?php echo site_url().'erp/training-details/'.uencode($ntpublished['holiday_id']);?>',
	// 			color: '#f4c22b !important'
	// 		},
	// 		<?php endforeach;?>
	// 		<?php foreach($published as $tpublished):?>
	// 		{
	// 			event_id: '<?php echo $tpublished['holiday_id']?>',
	// 			unq: '0',
	// 			title: '<?php echo $tpublished['event_name']?>',
	// 			start: '<?php echo $tpublished['start_date']?>',
	// 			end: '<?php echo $tpublished['end_date']?>',
	// 			// urllink: '<?php echo site_url().'erp/training-details/'.uencode($tpublished['holiday_id']);?>',
	// 			color: '#1de9b6 !important'
	// 		},
	// 		<?php endforeach;?>
	// 	]
	// });	
	/* initialize the external events
	-----------------------------------------------------------------*/

	$('#external-events .fc-event').each(function() {

        $(this).css({'backgroundColor': $(this).data('color'), 'borderColor': $(this).data('color')});

		$(this).data('event', {
			title: $.trim($(this).text()), 
			color: $(this).data('color'),
			stick: true 
		});

	});
});
</script>
<script>
$(document).ready(function() {
	var currentYear = new Date().getFullYear();
	
	$('#calendar_hr').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listWeek'
		},
		themeSystem: 'bootstrap4',
		eventRender: function(event, element) {
			element.attr('title', event.title).tooltip();
			element.attr('href', event.urllink);
		},
		dayClick: function(date, jsEvent, view) {
			date_last_clicked = $(this);
			var event_date = date.format();
			$('#exact_date').val(event_date);
			var eventInfo = $("#module-opt");
			var mousex = jsEvent.pageX + 20; 
			var mousey = jsEvent.pageY + 20; 
			var tipWidth = eventInfo.width(); 
			var tipHeight = eventInfo.height(); 

			var tipVisX = $(window).width() - (mousex + tipWidth);
			var tipVisY = $(window).height() - (mousey + tipHeight);

			if (tipVisX < 20) { 
				mousex = jsEvent.pageX - tipWidth - 20;
			} 
			if (tipVisY < 20) {
				mousey = jsEvent.pageY - tipHeight - 20;
			}
			eventInfo.css({ top: mousey, left: mousex });
			eventInfo.show(); 
		},
		defaultDate: '<?php echo $events_date;?>',
		eventLimit: true, 
		navLinks: true, 
		selectable: true,
		events: [
			<?php foreach($not_published as $ntpublished): ?>
			{
				event_id: '<?php echo $ntpublished['holiday_id'] ?>',
				unq: '0',
				title: '<?php echo $ntpublished['event_name'] ?>',
				start: '<?php echo $ntpublished['start_date'] ?>',
				end: '<?php echo $ntpublished['end_date'] ?>',
				color: '#f4c22b !important'
			},
			<?php endforeach; ?>
			
			<?php foreach($published as $tpublished): ?>
			{
				event_id: '<?php echo $tpublished['holiday_id'] ?>',
				unq: '0',
				title: '<?php echo $tpublished['event_name'] ?>',
				start: '<?php echo $tpublished['start_date'] ?>',
				end: '<?php echo $tpublished['end_date'] ?>',
				color: '#1de9b6 !important'
			},
			<?php endforeach; ?>
			
			...getSundays(`${currentYear}-01-01`, `${currentYear}-12-31`)
		]
	});
	
	function getSundays(startDate, endDate) {
		var start = moment(startDate);
		var end = moment(endDate);
		var events = [];

		while (start.day(7).isBefore(end)) {
			events.push({
				title: 'Sunday Holiday',
				start: start.format('YYYY-MM-DD'),
				color: '#ff5733' 
			});
			start.add(1, 'weeks'); 
		}

		return events;
	}
});

</script>
