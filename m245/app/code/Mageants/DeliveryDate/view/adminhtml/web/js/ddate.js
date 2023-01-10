
define([
    'jquery',
    ], function ($) {
    $("#delivery_date").on('change', function(){
        $date = $(this).val();
        var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

		var a = new Date($date);
		var day  = weekday[a.getDay()].toLowerCase();
        var slots = JSON.parse($("#dayslotsslots").val());
        $("#delivery_timeslot").empty(); 
        //console.log(slots[day]);
        $.each(slots[day], function (index,values) {
                var timingSlot = values.start_hour+':'+values.start_min+' - ' + values.end_hour + ':' + values.end_min;
                var option = new Option(timingSlot, timingSlot)
                $("#delivery_timeslot").append(option);
        });
    });       
});
