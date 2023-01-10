var timeslot = window.checkoutConfig.shipping.delivery_date.timeslot;

var holidays =window.checkoutConfig.shipping.delivery_date.holidays;

var disableDays =window.checkoutConfig.shipping.delivery_date.disabledays;

var dateformat=window.checkoutConfig.shipping.delivery_date.dateformat;

var arrivalcomment=window.checkoutConfig.shipping.delivery_date.arrivalcomment;

var cutofftime=window.checkoutConfig.shipping.delivery_date.cutofftime;

var processtime=window.checkoutConfig.shipping.delivery_date.processtime;

var cutofftime1=cutofftime.split(",");

var currentdate = new Date();
var minDate=0;

if(processtime==0 || processtime=="" || processtime==null){
    var d = new Date(
        currentdate.getFullYear(), currentdate.getMonth(), currentdate.getDate(),
        cutofftime1[0].replace(/\b0/g,''),cutofftime1[1].replace(/\b0/g,'')
    );
    
    if(currentdate < d){
        minDate=0;
    } else {
        minDate=1;
    }
}

var timeslotArr=[];
var selectedDate='';

const monthNames = ["January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"];

const dayNames = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday","saturday"];

function getTimeslotArray(date){

    const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];

    const dayNames = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday","saturday"];

    var timeslot = window.checkoutConfig.shipping.delivery_date.timeslot;

    // const date=new Date(monthNames[currentdate.getMonth()]+' '+ currentdate.getFullYear()+' '+currentdate.getDate()+' '+'20:00');
    var timeslotArr=[];
    var selectedDate='';

    var timeslotArrcount=0;
    var timeArray=[];
    
    if(jQuery.inArray(dayNames[date.getDay()],timeslot)){
        jQuery.each(timeslot[dayNames[date.getDay()]], function(key,val) {
            timeArray[timeslotArrcount++]=val['start_hour']+':'+val['start_min']+'-'+val['end_hour']+':'+val['end_min'];
        });
    }

    jQuery.each(timeArray, function(key,val) {
        var endTime=val.split("-");
        endTime=endTime[1];
        
        var currentDate = new Date(jQuery.now());
        var endDate = new Date(monthNames[currentdate.getMonth()]+' '+ currentdate.getFullYear()+' '+currentdate.getDate()+' '+endTime);

        var count=0;
        if(currentDate.getTime() < endDate.getTime()){
            timeslotArr[count++]=val;
        }
    });
    return timeslotArr;
}

var newDate;

if(processtime != 0 && processtime != "" && processtime != null){
    // timeslotArr=getTimeslotArray(new Date(currentdate.setDate(currentdate.getDate()+ parseInt(processtime))));
    minDate = minDate + parseInt(processtime);
    // alert(new Date(currentdate.setDate(currentdate.getDate()+ parseInt(processtime))));
    newDate = new Date(currentdate.setDate(currentdate.getDate()+ parseInt(processtime)));
} else {
    timeslotArr = getTimeslotArray(new Date(currentdate.setDate(currentdate.getDate()+ minDate)));
    if(timeslotArr.length<=0){
        newDate=new Date(currentdate.setDate(currentdate.getDate()+1));
        timeslotArr=getTimeslotArray(newDate);
        minDate=1;
    } else {
        newDate=currentdate;
        timeslotArr=getTimeslotArray(newDate);
        /*minDate=0;*/
    }
}

define([
    'jquery',
    'ko',
    'uiComponent',
    'jquery/jquery.cookie',
    'Magento_Ui/js/model/messageList',
    'Magento_Ui/js/modal/modal', 
    'mage/calendar',
     'domReady!'
], function ($, ko, Component,Cookie,messageList,modal) {
    'use strict';
    var timeslotArray=ko.observableArray(timeslotArr);

    /* utility functions */
    function nationalDays(date) {
        var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
        // for (var i = 0; i < holidays.length; i++) {
            // if($.inArray((m+1) + '-' + d + '-' + y,holidays) != -1 || new Date() > date) {
            if($.inArray((m+1) + '-' + d + '-' + y,holidays) != -1) {
                return [false,"","Holidays"];
            }else{
                var cutofftime1=cutofftime.split(",");
                var currentdate = new Date();
                
                if(processtime==0 || processtime=="" || processtime==null){
                    
                    var d = new Date(
                        currentdate.getFullYear(), currentdate.getMonth(), currentdate.getDate(),
                        cutofftime1[0].replace(/\b0/g,''),cutofftime1[1].replace(/\b0/g,''),cutofftime1[2].replace(/\b0/g,'')
                        );
                    
                    if(currentdate<d){
                        return [true];
                    } else {
                        if(date<d){
                            return [false];
                        }else{
                            return [true];
                        }
                    }
                } else {
                    var dt=currentdate.setDate(currentdate.getDate() + parseInt(processtime));
                    if(date<dt){
                        return [false];
                    } else {
                        return [true];
                    }
                }
            }

        // }
        // return [true];
    }

    function specificDays(date) {
        var day = date.getDay();
        if(disableDays.indexOf(day)!==-1){
            return [false,"","Week-off"];
        }else{
            return [true];
        }
    }
    function noWeekendsOrHolidays(date) {
        var day = date.getDay();
        if(disableDays){
            if(disableDays.indexOf(day)!==-1){
                /*console.log(specificDays(date)+' . '+ date);*/
                return specificDays(date);
            }
        }
        // console.log(nationalDays(date)+' . '+ date);
        return nationalDays(date);
    }
    
    ko.bindingHandlers.datepicker = {
        init: function(element, valueAccessor, allBindingsAccessor) {
            var $el = $(element);
            if($.cookie('delivery_date')){
                $el.val($.cookie('delivery_date'))
                
                
            }
            
            //initialize datepicker with some oko.observableArray() options

            var options = allBindingsAccessor().datepickerOptions || {};

            // $el.datepicker(options);

            //handle the field changing
            ko.utils.registerEventHandler(element, "change", function() {

                var observable = valueAccessor();
                observable($el.datepicker("getDate"));
            });
            
            //handle disposal (if KO removes by the template binding)
            ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
                $el.datepicker("destroy");
            });
            var options = {
                            minDate: minDate,
                            // dateFormat: 'dd-mm-yy',
                            dateFormat: dateformat,
                            beforeShowDay:noWeekendsOrHolidays,
                        };
            // ko.bindingHandlers.options.init(element,valueAccessor, allBindingsAccessor);
            $el.datepicker(options);
        },
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.utils.unwrapObservable(valueAccessor()),
                $el = $(element),
                current = $el.datepicker("getDate");
                selectedDate=current;
                
                if(current!==null) {
                    selectedDate=current;
                } else {
                    selectedDate='';
                }

                var timeslotArrcount=0;
                var timeArray=[];
                if(selectedDate!=''){
                if(jQuery.inArray(dayNames[selectedDate.getDay()],timeslot)){
                    jQuery.each(timeslot[dayNames[selectedDate.getDay()]], function(key,val) {
                        timeArray[timeslotArrcount++]=val['start_hour']+':'+val['start_min']+' - '+val['end_hour']+':'+val['end_min'];
                    });
                }

                $.each( timeslotArray, function( key, value ) {
                    timeslotArray.splice(key, 1);
                });

                jQuery.each(timeArray, function(key,val) {
                    var endTime=val.split("-");
                    endTime=endTime[1];
                    
                    var currentDate = new Date(jQuery.now());
                    var endDate = new Date(monthNames[selectedDate.getMonth()]+' '+ selectedDate.getFullYear()+' '+selectedDate.getDate()+' '+endTime);

                    var count=0;
                    if(currentDate.getTime() < endDate.getTime()){
                        
                        timeslotArray.push(ko.observable(val));                        
            

                    }
                });
                }

                //ko.bindingHandlers.options.update(element,valueAccessor, allBindings, viewModel, bindingContext);

            /*if (value - current !== 0) {
                $el.datepicker("setDate", value);
            }*/

        },
    };

    return Component.extend({

        defaults: {
            template: 'Mageants_DeliveryDate/delivery-date-block'
        },
       
        checkComment: function(data, event){
            $("div.comment_message").remove();
            $("#delivery_comment").css("border-radius", "");
       

            if(event.target.value.length>49){                
                $('#delivery_comment').attr('style', "margin-top: 10px;border-radius: 1px; border:#ed8380 1px solid;");
                $("#delivery_comment").parent().after("<div class='comment_message' style='color:#e02b27;font-size:1.2rem;margin-top:7px;'>Please enter less or equal than 50 character.</div>");
                     // $("#delivery_timeslot").val('sss');
            } else {
                     // $("#delivery_timeslot").val('sss');
                $("div.comment_message").remove();
                $("#delivery_comment").css("border-radius", "");
                $("#delivery_comment").css("border", "");                   
            }
            return true;

        },

        displayComment: ko.observable((arrivalcomment == "0") ? false : true),
        myDate: ko.observable(new Date("06/22/2013")),
        myOptions : timeslotArray
    });

});