<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .tooltip-inner {
        text-align: left !important;
    }
    .fc-day-grid-event .fc-content {
        white-space: unset !important;
        overflow: unset !important;
    }

    .fc-event-container a.fc-day-grid-event {
        border: 1px solid #0e5daa !important
    }

    .fc-time {
        color: black;
    }

    table tbody tr td {
        cursor: pointer;
    }
</style>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="H_scroll">
		<div class="panel-body _buttons">
			<span class="bold uppercase fsize18 H_title"><?=$title?></span>
		</div>
	</div>
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body" style="overflow-x: auto;">
						<div id="calendar-pod"></div>
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
	$(document).ready(function() {
		calendar_selector_pod = $("#calendar-pod");
		var nCalendar = {
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
            defaultView: app.options.default_view_calendar,
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
            eventSources: [{
                url: admin_url + "manufactures/getCalendarPod",
                data: function() {
                    var e = {[csrfData['token_name']] : csrfData['hash']};
                    return $("#calendar_filters").find("input:checkbox:checked").map(function() {
                        e[$(this).attr("name")] = !0
                    }).get(), jQuery.isEmptyObject(e) || (e.calendar_filters = !0), e
                },
                type: "POST",
                error: function() {
                    console.error("There was error fetching calendar data")
                }
            }],
            eventLimitClick: function(e, t) {
                $("#calendar").fullCalendar("gotoDate", e.date), $("#calendar").fullCalendar("changeView", "basicDay");
            },
            eventRender: function(e, t) {
                var title = t.find('.fc-title');
                title.html(title.text());
                $(t).tooltip({html: true, title: e._tooltip, container: "body"});
                // t.attr("title", e._tooltip), t.attr("onclick", e.onclick), t.attr("data-toggle", "tooltip"), e.url || t.click(function() {
                //     view_event(e.eventid)
                // })
            },
            // eventMouseover: function (data, event, view) {
            //     tooltip = '<div class="tooltiptopicevent" style="width:auto;height:auto;background:#feb811;position:absolute;z-index:10001;padding:10px 10px 10px 10px ;  line-height: 200%;">' + 'title: ' + ': ' + data.title + '</br>' + 'start: ' + ': ' + data.start + '</div>';

            //     $("body").append(tooltip);
            //     $(this).mouseover(function (e) {
            //         $(this).css('z-index', 10000);
            //         $('.tooltiptopicevent').fadeIn('500');
            //         $('.tooltiptopicevent').fadeTo('10', 1.9);
            //     }).mousemove(function (e) {
            //         $('.tooltiptopicevent').css('top', e.pageY + 10);
            //         $('.tooltiptopicevent').css('left', e.pageX + 20);
            //     });
            // },
            // eventMouseout: function (data, event, view) {
            //     $(this).css('z-index', 8);
            //     $('.tooltiptopicevent').remove();
            // },
            eventClick: function(info) {
            	console.log(info);
			},
            dayClick: function(e, t, a) {
                var i = e.format();
                $.fullCalendar.moment(i).hasTime() || (i += " 00:00");
                var n = 24 == app.options.time_format ? app.options.date_format + " H:i" : app.options.date_format + " g:i A",
                    s = (new DateFormatter).formatDate(new Date(i), n);
                return $("input[name='start'].datetimepicker").val(s), $("#newEventModal").modal("show"), !1
            }
        };

        calendar_selector_pod.fullCalendar(nCalendar);
	});
</script>