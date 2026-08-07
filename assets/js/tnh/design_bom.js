function typeDesignBom(type_design_bom, select = '')
{
    options = '<option value=""></option>';
    $.each(type_design_bom, function(index, el) {
        selected = select == index ? 'selected' : '';
        options += '<option '+ selected +' value="'+ index +'">'+ el +'</option>';
    });
    return options;
}

function getUnits(units, select = '')
{
    options = '<option value=""></option>';
    $.each(units, function(index, el) {
        selected = select == el.unitid ? 'selected' : '';
        options += '<option '+ selected +' value="'+ el.unitid +'">'+ el.unit +'</option>';
    });
    return options;
}

function changeMachines(_this) {
    $(_this).on("changed.bs.select", function (e, clickedIndex, newValue, oldValue) {
        machines_id = this.value;
        if (typeof machines_id !== 'undefined' && machines_id > 0) {
            var dataPOST = {};
            dataPOST[csrfData['token_name']] = csrfData['hash'];
            dataPOST['machines_id'] = machines_id;
            $.ajax({
                type: "POST",
                url: site.base_url+'admin/products/changeMachines',
                data: dataPOST,
                dataType: "json",
                success: function (response) {
                    $('.txt-info-machines').html(response.info);
                }
            });
        } else {
            $('.txt-info-machines').html('');
        }
    });
}

function handInputPaperExchange(_this) {
    cTrPE = $(_this).closest('tr');
    ischeckedPE = $(_this).prop('checked');
    if (ischeckedPE) {
        cTrPE.find('.paper_exchange').removeAttr('readonly');
    } else {
        cTrPE.find('.paper_exchange').attr('readonly', 'readonly');
        calPaperExchange(_this);
    }
}

function calPaperExchange(_this) {
    cTrPE = $(_this).closest('tr');
    ischeckedPE = cTrPE.find('.hand_input_paper_exchange').prop('checked');
    if (!ischeckedPE) {
        number_children_size = intVal(cTrPE.find('.number_children_size').val());
        console.log(number_children_size);
        paper_exchange = 0;
        if (number_children_size) {
            paper_exchange = tnhToFixedNumber(1/ number_children_size);
        }
        cTrPE.find('.paper_exchange').val(tnhFormatNumber(paper_exchange));
    }
}

function getMaterialReplace(_this)
{
    cRow = $(_this).closest('tr');
    cIII = cRow.find('.iii').val();
    cKK = cRow.find('.k').val();

    typeMaterial = cRow.find('select.type_design_bom').val();

    trMaterial = '';
    trMaterial+= '<tr class="tnh-item-'+cIII+'-'+cKK+'">';

    trMaterial+= '<td colspan="1">';
    trMaterial+= '<input type="hidden" name="typeMaterial[]" id="typeMaterial" class="form-control typeMaterial" value="'+ typeMaterial +'">';
    trMaterial+= '<input type="hidden" name="cIII[]" id="cIII" class="form-control cIII" value="'+ cIII +'">';
    trMaterial+= '<input type="hidden" name="cKK[]" id="cKK" class="form-control cKK" value="'+ cKK +'">';
    trMaterial+= '</td>';

    trMaterial+= '<td colspan="2">';
    trMaterial+= '<div class="row">';
    trMaterial+= '<div class="col-md-1"><i class="fa fa-magic"></i></div>';
    trMaterial+= '<div class="col-md-11"><input type="text" name="items_replace'+ cIII +'['+cKK+'][]" id="items_replace_'+ kR +'" data-placeholder="'+ lang_bom['choose'] +'" class="modal-select2 it-replace" style="width: 100%;" value=""></div>';
    trMaterial+= '</div>';
    trMaterial+= '</td>';

    trMaterial+= '<td colspan="1">';
    trMaterial+= '<select data-placeholder="'+ lang_bom['choose'] +'" name="units_replace'+ cIII +'['+cKK+'][]" id="units_replace_'+ kR +'[]" class="modal-select2 units-replace" style="width: 100%;"></select>';
    trMaterial+= '</td>';

    trMaterial+= '<td colspan="1">';
    trMaterial+= '<input type="number" name="element_item_number_replace'+cIII+'['+cKK+'][]" class="form-control" value="1">';
    trMaterial+= '</td>';

    if (typeof vProduct != "undefined")
    {
        trMaterial+= '<td colspan="1">';
        trMaterial+= '<input type="number" name="leadtime_replace'+cIII+'['+cKK+'][]" class="form-control" value="1">';
        trMaterial+= '</td>';

        trMaterial+= '<td colspan="1">';
        trMaterial+= '<select name="stage_replace'+cIII+'['+cKK+'][]"  data-live-search="true" data-none-selected-text="" id="stage_replace'+ kR +'" class="form-control">\
            <option value=""></option>\
            '+list_stages+'\
        </select>';
        trMaterial+= '</td>';
    }

    trMaterial+= '<td colspan="1">';
    trMaterial+= '<div class="text-center"><i class="btn btn-danger fa fa-remove remove-element-item-replace"></i></div>';
    trMaterial+= '</td>';
    trMaterial+= '</tr>';

    $(trMaterial).insertAfter(cRow);

    if (typeMaterial == "semi_products") {
        ajaxSelectParamsCallback('#items_replace_'+ kR +'', 'admin/products/searchSelect2SemiProducts', 0);
    } else if (typeMaterial == "semi_products_outside") {
        ajaxSelectParamsCallback('#items_replace_'+ kR +'', 'admin/products/searchSelect2SemiProductsOutside', 0);
    } else {
        ajaxSelectParamsCallback('#items_replace_'+ kR +'', 'admin/items/searchSelect2Materials', 0);
    }

    // ajaxSelectParamsCallback('#items_replace_'+ kR +'', 'admin/items/searchSelect2Materials', 0);
    $('select[name="units_replace'+ cIII +'['+cKK+'][]"]').select2();
    $('#stage_replace'+ kR +'').selectpicker();
    kR++;
}

function changeStage(_this) {
    cTR = $(_this).closest('tr');
    json_stage_criteria = $(_this).find(':selected').data('json_stage_criteria');
    txt_info_stage = '';
    if (typeof json_stage_criteria !== "undefined") {
        $.each(json_stage_criteria, function (indexJ, valueJ) { 
            txt_info_stage+= `<div>Rút kiểm: ${valueJ.withdraw_check} - Tiêu chuẩn kiểm: ${valueJ.test_standards}</div>`;
        });
    }
    cTR.find('.txt-info-stage').html(txt_info_stage);
}

$(function(){
	$(document).on('click', '.btn-add-element', function(event) {
		event.preventDefault();
		tr_html = '';
		tr_html += '<tr>';
        tr_html += '<input type="hidden" name="i[]" id="i" class="form-control i" value="'+i+'">'
		tr_html += '<td>\
						<div class="text-center">\
							<button type="button" class="btn btn-primary btn-icon btn-add-items">\
								<i class="fa fa-plus"></i>\
							</button>\
						</div>\
					</td>';
      
		tr_html += '<td colspan="2">\
						<input type="text" name="element_name_'+i+'" id="element_name_'+i+'" class="form-control element_name" value="" placeholder="'+lang_bom['tnh_element_name']+'" required="required">\
                        <input type="hidden" name="type_element_'+i+'" class="form-control type_element" value="">\
                        <div class="txt-type-element text-danger mtop5"></div>\
					</td>';

        tr_html += `<td></td>`;
        
		tr_html += '<td>\
						<input type="number" name="element_number_'+i+'" class="form-control hide" value="1">\
					</td>';

        // tr_html += `<td></td>`;
        tr_html += `<td></td>`;
        tr_html += `<td></td>`;
        tr_html += `<td></td>`;

        tr_html += '<td>\
        </td>';
        if (typeof vProduct != "undefined")
        {
            tr_html += '<td></td>';
            // tr_html += '<td></td>';
            // tr_html += '<td></td>';
        }

        // <div class="text-center"><i class="btn btn-danger fa fa-remove remove-element"></i></div>\
		tr_html += '<td>\
					</td>';
		tr_html += '</tr>';

		$('.table-bom tbody').append(tr_html);
        json['element_name_'+i+''] = 'required';
        // appValidateForm($('#add-category'), json, addBOM);
        i++;
	});

	$(document).on('click', '.btn-add-items', function(event) {
		event.preventDefault();
        row_element = $(this).closest('tr');
        i_current = row_element.find('.i').val();
        // <span class="fa fa-plus text-primary mtop10 add-replace" onclick="getMaterialReplace(this)" style="cursor: pointer; display: none;"> Thêm nguyên liệu thay thế</span>\
        tr_html = '';
        tr_html += '<tr class="tr-child-item tnh-item-'+ i_current +'">';
        tr_html += '<td></td>';
        tr_html += '<input type="hidden" name="iii" id="iii" class="form-control iii" value="'+ i_current +'">';
        tr_html += '<input type="hidden" name="k[]" id="k" class="form-control k" value="'+ k +'">';
        tr_html += '<td colspan="1" style="width: 180px;">\
                        <select name="type_design_bom_'+ i_current +'['+ k +']" data-none-selected-text="'+ lang_bom['type'] +'" id="type_design_bom_'+ k +'" class="form-control type_design_bom" required="required">\
                            '+typeDesignBom(type_design_bom, 0)+'\
                        </select>\
                        <div class="td-category-products td-category-products-'+k+' mtop5" style="display: none;">\
                            <select data-none-selected-text="Danh mục" data-live-search="true" id="category_product_search_bom'+ k +'" class="form-control category_product_search_bom">\
                                <option value=""></option>\
                                '+category_product+'\
                            </select>\
                        </div>\
                        <div class="td-category-materials td-category-materials-'+k+' mtop5" style="display: none;">\
                            <select data-none-selected-text="Danh mục" data-live-search="true" id="category_material_search_bom'+ k +'" class="form-control category_material_search_bom">\
                                <option value=""></option>\
                                '+category_material+'\
                            </select>\
                        </div>\
                        <div class="checkbox checkbox-info" style="margin-top: 5px;">\
                                <input type="checkbox" name="face_'+ i_current +'['+ k +']" id="face_'+ i_current +'['+ k +']" value="1">\
                                <label for="face_'+ i_current +'['+ k +']">Mặt trước</label>\
                        </div>\
                        <div class="checkbox checkbox-info">\
                            <input type="checkbox" name="face_after_'+ i_current +'['+ k +']" id="face_after_'+ i_current +'['+ k +']" value="2">\
                            <label for="face_after_'+ i_current +'['+ k +']">Mặt sau</label>\
                        </div>\
                    </td>';
        
        tr_html += '<td colspan="1">\
                        <input type="text" name="items_'+ i_current +'['+ k +']" id="items_'+ k +'" data-placeholder="'+ lang_bom['choose'] +'" class="modal-select2 it" style="width: 100%;" value="" required="required">\
                    </td>';
        tr_html += '<td class="td-unit" colspan="">\
                        <select data-placeholder="'+ lang_bom['choose'] +'" id="units_'+ k +'" name="units_'+ i_current +'['+ k +']" class="modal-select2 units" style="width: 100%;" required></select>\
                    </td>';

        tr_html += `<td colspan="">
            <input type="text" name="landscape_print_size_${i_current}[${k}]" class="form-control landscape_print_size" value="0">
        </td>`;

        // tr_html += `<td colspan="">
        //     <input type="text" name="vertical_print_size_${i_current}[${k}]" class="form-control number-format vertical_print_size" value="0">
        // </td>`;

        tr_html += `<td colspan="">
            <input type="text" name="number_children_size_${i_current}[${k}]" onchange="calPaperExchange(this)" class="form-control number-format number_children_size" value="0">
        </td>`;
        
        tr_html += '<td colspan="">\
            <input type="text" name="element_item_number_'+i_current+'['+ k +']" class="form-control number-format" value="0">\
        </td>';

        tr_html += `<td colspan="">
            <input type="text" name="paper_exchange_${i_current}[${k}]" class="form-control number-format paper_exchange" readonly value="0">
            <div class="checkbox checkbox-info" style="margin-top: 5px !important;">
                <input type="checkbox" name="hand_input_paper_exchange_${i_current}[${k}]" onchange="handInputPaperExchange(this)" id="hand_input_paper_exchange${i_current}[${k}]" class="hand_input_paper_exchange" value="1">
                <label for="hand_input_paper_exchange${i_current}[${k}]">Nhập tay</label>
            </div>
        </td>`;
        
        tr_html += `<td colspan="">
            <input type="text" name="quantity_compensation_${i_current}[${k}]" class="form-control number-format" value="0">
        </td>`;
        
        //view products
        if (typeof vProduct != "undefined")
        {
            // tr_html += '<td colspan="">\
            //     <input type="number" name="leadtime_'+i_current+'['+ k +']" class="form-control" value="1">\
            // </td>';
            tr_html += '<td>\
                <select name="stage_'+i_current+'['+ k +']"  data-live-search="true" onChange="changeStage(this)" data-none-selected-text="" id="stage_'+ i_current +'" class="form-control stage_item '+(row_element.find('.type_element').val() == 1 ? 'stage_items_primary' : '')+'">\
                    <option value=""></option>\
                    '+(row_element.find('.type_element').val() == 1 ? list_stages_primary : list_stages)+'\
                </select><div class="txt-info-stage"></div>\
            </td>';
            // tr_html += `<td>
            //     <select name="machines_${i_current}[${k}]" onchange="changeMachines(this)" data-live-search="true" data-none-selected-text="Máy móc" id="machines_${i_current}" class="form-control ajax-search" >
            //         <option value=""></option>
            //     </select>
            //     <div class="txt-info-machines"></div>
            // </td>`;
        }

        tr_html += '<td colspan="">\
                        <div class="text-center"><i class="btn btn-danger fa fa-remove remove-element-item"></i></div>\
                    </td>';
        tr_html += '</tr>';
        row_element.after(tr_html);
        json['type_design_bom_'+ k +''] = 'required';
        json['items_'+ k +''] = 'required';
        json['units_'+ k +''] = 'required';
        $('#type_design_bom_'+ k +'').selectpicker();
        $('#category_product_search_bom'+ k +'').selectpicker();
        $('#category_material_search_bom'+ k +'').selectpicker();
        $('select[name="units_'+ i_current +'['+ k +']"]').select2();
        // $('#items_'+ k +'').selectpicker();
        if (typeof vProduct != "undefined")
        {
            // $('#stage_'+ i_current +'').selectpicker();
            $('select.stage_item').selectpicker();
            selectAjax($('select#machines_' + i_current), false, 'admin/categories/searchMachines');
        }

        k++;
	});

    $(document).on('change', 'select.type_design_bom', function(event) {
        event.preventDefault();
        row_item = $(this).closest('tr');
        k_current = row_item.find('.k').val();
        type_current = $(this).val();
        row_item.find('.add-replace').hide();

        cIII = row_item.find('.iii').val();
        cKK = row_item.find('.k').val();
        $('.tnh-item-'+cIII+'-'+cKK+'').remove();

        if (type_current == "semi_products") {
            ajaxSelectParamsCallback('#items_'+ k_current +'', 'admin/products/searchSelect2SemiProducts', 0);
            row_item.find('.td-category-products').show();
            row_item.find('.td-category-materials').hide();
            // row_item.find('.add-replace').show();
        } else if (type_current == "semi_products_outside") {
            ajaxSelectParamsCallback('#items_'+ k_current +'', 'admin/products/searchSelect2SemiProductsOutside', 0);
            row_item.find('.td-category-products').show();
            row_item.find('.td-category-materials').hide();
            // row_item.find('.add-replace').show();
        } else {
            ajaxSelectParamsCallback('#items_'+ k_current +'', 'admin/items/searchSelect2Materials', 0);
            row_item.find('.td-category-products').hide();
            row_item.find('.td-category-materials').show();
            // row_item.find('.add-replace').show();
            // $('select#items_'+ k_current +'').val('default').trigger('change');
            // $('select#items_'+ k_current +' option').remove();
            // $('select#items_'+ k_current +'').selectpicker("refresh");
            // $('select#items_'+ k_current +'').trigger('change');
            // selectAjax($('select#items_'+ k_current +''), false, 'admin/items/searchMaterials', 'items/searchMaterials');
            // $('select#items_'+ k_current +'').data('AjaxBootstrapSelect').options.ajax.url = 'items/searchMaterials';
        }
    });

    $(document).on('change', 'select.category_product_search_bom, select.category_material_search_bom', function(event) {
        event.preventDefault();
        row_item = $(this).closest('tr');
        k_current = row_item.find('.k').val();
        category_id_search = $(this).val();
        if (type_current == "semi_products") {
            ajaxSelectParamsCallback('#items_'+ k_current +'', 'admin/products/searchSelect2SemiProducts', 0, {category_id_search: category_id_search});
        } else if (type_current == "semi_products_outside") {
            ajaxSelectParamsCallback('#items_'+ k_current +'', 'admin/products/searchSelect2SemiProductsOutside', 0, {category_id_search: category_id_search});
        } else {
            ajaxSelectParamsCallback('#items_'+ k_current +'', 'admin/items/searchSelect2Materials', 0, {category_id_search: category_id_search});
        }
    });

    $(document).on('change', '.it', function(event) {
        tr = $(this).closest('tr');
        type = tr.find('select.type_design_bom').val();
        iii = tr.find('.iii').val();
        kk = tr.find('.k').val();
        item_id = $(this).val();
        $.ajax({
            url: site.base_url+'admin/products/rowItem',
            type: 'GET',
            dataType: 'json',
            data: {
                token: hash,
                type: type,
                item_id: item_id,
            },
        })
        .done(function(data) {
            if (data) {
                tr.find('select.units').html(getUnits(data.units, data.selected));
                tr.find('select.units').val(data.selected).trigger('change');
            }
        })
        .fail(function() {
            console.log("error");
        });
    });

    $(document).on('change', '.it-replace', function(event) {
        tr = $(this).closest('tr');
        type = tr.find('input.typeMaterial').val();
        cIII = tr.find('.cIII').val();
        cKK = tr.find('.cKK').val();
        item_id = $(this).val();
        $.ajax({
            url: site.base_url+'admin/products/rowItem',
            type: 'GET',
            dataType: 'json',
            data: {
                token: hash,
                type: type,
                item_id: item_id,
            },
        })
        .done(function(data) {
            if (data) {
                tr.find('select.units-replace').html(getUnits(data.units, data.selected));
                tr.find('select.units-replace').val(data.selected).trigger('change');
            }
        })
        .fail(function() {
            console.log("error");
        });
    });

    $(document).on('click', '.remove-element-item', function(event) {
        event.preventDefault();
        cRow = $(this).closest('tr');
        $(this).closest('tr').remove();
        cIII = cRow.find('.iii').val();
        cKK = cRow.find('.k').val();
        $('.tnh-item-'+cIII+'-'+cKK+'').remove();
    });

    $(document).on('click', '.remove-element', function(event) {
        event.preventDefault();
        row_current = $(this).closest('tr');
        i_current = row_current.find('.i').val();
        row_current.remove();
        // $('.tnh-item-'+i_current).remove();
        $('.tnh-item-'+i_current).find('.remove-element-item').click();
    });

    $(document).on('click', '.remove-element-item-replace', function(event) {
        row_current = $(this).closest('tr');
        row_current.remove();
    });

    // $(document).on('click', '.copy-bom', function(event) {
    //     event.preventDefault();
    // });
})