function bodauTiengViet(str) {
    str = str.toLowerCase();
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o');
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y');
    str = str.replace(/đ/g, 'd');
    // str = str.replace(/\W+/g, ' ');
    // str = str.replace(/\s/g, '-');
    return str;
}


function tpanigation(elTable, pageCurrent, iCall = 0)
{
    if (iCall == 0) {
        $(''+elTable+' tbody tr').attr('tsearch','ok');
    }
    numberPage = 10;
    $(''+elTable+' tbody tr[tsearch="notok"]').css('display','none');
    $(''+elTable+' tbody tr[tsearch="ok"]').css('display','block');
    sum = $(''+elTable+' tbody tr[tsearch="ok"]').length;
    numPages = Math.ceil(sum/numberPage);
    start = (pageCurrent - 1) * numberPage;
    end   = numberPage * pageCurrent - 1;
    listRows = $(''+elTable+' tbody tr[tsearch="ok"]');
    for (i = 0; i<listRows.length; i++)
    {
        if(i >= start && i <= end)
        {
            listRows[i].style.display='';
        }
        else{
            listRows[i].style.display = 'none';
        }
    }
    soNut = numPages;
}

function searchTableCustom(elTable, elSearch, elPanigation) {
    $(elSearch).keyup(function(event){
        var search_string = bodauTiengViet($.trim($(elSearch).val()).replace(/ +/g,' ').toLowerCase());
        if (search_string == '') {
            $(''+elTable+' tbody tr').attr('tsearch','ok');
            tpanigation(elTable, 1, 1);
        } else {
            console.log(search_string);
            var listRows = $(''+elTable+' tbody tr');
            $(listRows).attr('tsearch','notok');
            for(i = 0 ; i<listRows.length; i++)
            {
                // var str = bodauTiengViet(listRows[i].children[1].innerHTML.toLowerCase());
                var str = bodauTiengViet(listRows[i].innerHTML.toLowerCase());
                if(str.search(search_string) >=0 )
                {
                    $(listRows[i]).attr('tsearch','ok');
                }
            }
            tpanigation(elTable, 1, 1);
        }
        createPanigation(elTable, elPanigation, 1);
    });
}

function createPanigation(elTable, elPanigation, tdestroy = 0)
{
    if (tdestroy = 1) {
        $(elPanigation).twbsPagination('destroy');
    }
    window.pagObj = $(elPanigation).twbsPagination({
        totalPages: numPages,
        visiblePages: 10,
        first: 'Đầu',
        last: 'Cuối',
        prev: 'Trước',
        next: 'Kế tiếp',
        onPageClick: function (event, page) {
            tpanigation(elTable, page, 1);
        }
    }).on('page', function (event, page) {
        tpanigation(elTable, page, 1);
    });
}
