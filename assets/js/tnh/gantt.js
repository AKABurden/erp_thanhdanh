$(document).ready(function() {
    gantt = $("#gantt").gantt({
        source: gantt_data,
        itemsPerPage: 25,
        months: app.months_json,
        navigate: "scroll",
        onRender: function () {
            $("#gantt .leftPanel .name .fn-label:empty").parents(".name").css("background", "initial"), $("#gantt .leftPanel .spacer").html('<span class="gantt_project_name"><i class="fa fa-cubes"></i> ' + $(".project-name").text() + "</span>");
            var e = $('input[name="project_percent"]').val();
            $("#gantt .leftPanel .spacer").append('<div style="padding:10px 20px 10px 20px;"><div class="progress mtop5 progress-bar-mini"><div class="progress-bar progress-bar-success no-percent-text" role="progressbar" aria-valuenow="' + e + '" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="' + e + '"></div></div></div>'), init_progress_bars()
        },
        onItemClick: function (e) {
            init_task_modal(e.task_id)
        },
        onAddClick: function (e, t) {
            var a = new DateFormatter, i = new Date(+e), n = a.formatDate(i, app.options.date_format);
            new_task(admin_url + "tasks/task?rel_type=project&rel_id=" + project_id + "&start_date=" + n)
        }
    });
});