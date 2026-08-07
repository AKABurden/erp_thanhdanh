<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .tooltip-inner {
        text-align: left !important;
    }
    .fc-month-button {
        display: none !important;
    }
    .fc-today-button {
        display: none;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="H_scroll">
		<div class="panel-body _buttons">
			<span class="bold uppercase fsize18 H_title"><?= $title ?></span>
		</div>
	</div>
	<div class="content">
        <input type="hidden" name="startDate" id="startDate" class="form-control" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="endDate" id="endDate" class="form-control" value="<?= date('Y-m-d', strtotime("+1 days")) ?>">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body" style="overflow-x: auto;">
						<div id="calendar-delivery"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script type="text/javascript">
    var view = '';
	$(document).ready(function() {
		calendar_selector_delivery = $("#calendar-delivery");
        function loadCalendar() {
    		$("#calendar-delivery").fullCalendar({
                themeSystem: "bootstrap3",
                customButtons: {},
                header: {
                    left: "prev,next today",
                    center: "title",
                    right: "month,agendaWeek,agendaDay,viewFullCalendar,calendarFilter"
                },
                editable: !1,
                eventLimit: parseInt(app.options.calendar_events_limit) + 1,
                views: {
                    day: {
                        eventLimit: !1
                    }
                },
                // defaultView: 'basicDay',
                isRTL: "true" == isRTL,
                eventStartEditable: !1,
                timezone: app.options.timezone,
                firstDay: parseInt(app.options.calendar_first_day),
                year: moment.tz(app.options.timezone).format("YYYY"),
                month: moment.tz(app.options.timezone).format("M"),
                date: moment.tz(app.options.timezone).format("DD"),
                loading: function(e, t) {
                    e && $("#calendar .fc-header-toolbar .btn-default").addClass("btn-info").removeClass("btn-default").css("display", "block"), e ? $(".dt-loader").removeClass("hide") : $(".dt-loader").addClass("hide")
                },
                events: function(start, end, timezone, callback) {
                    $.ajax({
                        url: admin_url + "orders/getCalendarOrdersDeliveryData",
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            timezone: timezone,
                            startDate: $('#startDate').val(),
                            endDate: $('#endDate').val(),
                        },
                        success: function(doc) {
                            var events = [];
                            $.each(doc, function(index, el) {
                                events.push({
                                    title: el.title,
                                    start: el.start,
                                    end: el.end,
                                });
                            });
                            // $(doc).find('event').each(function() {
                            //     console.log(this);
                            //     events.push({
                            //         title: $(this).attr('title'),
                            //         start: $(this).attr('start')
                            //     });
                            // });
                            console.log(events);
                            callback(events);
                        }
                    });
                },
                // eventSources: [{
                //     url: admin_url + "orders/getCalendarOrdersDeliveryData",
                //     data: {
                //         "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                //         startDate: $('#startDate').val(),
                //         endDate: $('#endDate').val(),
                //     },
                //     type: "POST",
                //     error: function() {
                //         console.error("There was error fetching calendar data")
                //     }
                // }],
                // eventSources: [{
                //     url: admin_url + "orders/getCalendarOrdersDeliveryData",
                //     data: function() {
                //         var e = {[csrfData['token_name']] : csrfData['hash']};
                //         return $("#calendar_filters").find("input:checkbox:checked").map(function() {
                //             e[$(this).attr("name")] = !0
                //         }).get(), jQuery.isEmptyObject(e) || (e.calendar_filters = !0), e
                //     },
                //     type: "POST",
                //     error: function() {
                //         console.error("There was error fetching calendar data")
                //     }
                // }],
                eventLimitClick: function(e, t) {
                    $("#calendar").fullCalendar("gotoDate", e.date), $("#calendar").fullCalendar("changeView", "basicDay");
                },
                eventRender: function(e, t) {
                    var title = t.find('.fc-title');
                    title.html(title.text());
                    $(t).tooltip({html: true, title: e._tooltip, container: "body"});
                },
                eventClick: function(info) {
    			},
                dayClick: function(e, t, a) {
                    var i = e.format();
                    $.fullCalendar.moment(i).hasTime() || (i += " 00:00");
                    var n = 24 == app.options.time_format ? app.options.date_format + " H:i" : app.options.date_format + " g:i A",
                        s = (new DateFormatter).formatDate(new Date(i), n);
                    return $("input[name='start'].datetimepicker").val(s), $("#newEventModal").modal("show"), !1
                }
            });
        }

        loadCalendar();
        $('.fc-agendaDay-button').click();
        $(document).on('click', '.fc-month-button, .fc-agendaWeek-button, .fc-agendaDay-button, .glyphicon, .fc-today-button, .btn-default', function(event) {
            event.preventDefault();
            view = $('#calendar-delivery').fullCalendar('getView');
            // $("#calendar-delivery").fullCalendar('destroy');
            startDate = moment(view.start._d).format("YYYY-MM-DD");
            endDate = moment(view.end._d).format("YYYY-MM-DD");
            $('#startDate').val(startDate);
            $('#endDate').val(endDate);
            $("#calendar-delivery").fullCalendar('refetchEvents');
        });
	});
</script>