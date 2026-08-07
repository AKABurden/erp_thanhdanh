function totalProductionsQuotes() {
  tb = '#tb-productions-quotes tbody tr:not("[class^=not-tr]")';
  var table = $(tb).length;
  var stt = 0;
  var total_quantity = 0;
  var total_amount = 0;
  var freight_insurance = intVal($(".freight_insirance").val());
  // var charge = intVal($('.total-charge').text());
  var charge = 0;

  count_errors = 0;
  for (ii = 0; ii < table; ii++) {
    stt++;
    element = $(tb)[ii];
    $(element).find(".stt").html(stt);
    quantity = intVal($(element).find(".quantity").val());
    price = intVal($(element).find(".price").val());
    amount = quantity * price;
    $(element).find(".td-total-amount").html(tnhFormatMoney(amount));
    total_quantity += quantity;
    total_amount += amount;
  }
  $(".total-quantity").html(tnhFormatNumber(total_quantity));
  $(".total-amount").html(tnhFormatMoney(total_amount));

  total = charge + total_amount;
  $(".grand-total").html(tnhFormatMoney(total));

  grand_total = total;
  tax_rate = intVal(
    $("select.tax_id").select2().find(":selected").data("rate"),
  );
  tax_amount = 0;
  if (tax_rate > 0) {
    tax_amount = grand_total * (tax_rate / 100);
  }
  grand_total = total + tax_amount;
  $(".td-grand-total-all").html(tnhFormatMoney(grand_total));
}

function totalCharge() {
  tb = '#tb-charge tbody tr:not("[class^=not-tr]")';
  var table_charge = $(tb).length;

  var stt_charge = 0;
  var total_quantity_charge = 0;
  var total_amount_charge = 0;
  var total_items = intVal($(".total-amount").text());
  for (ii = 0; ii < table_charge; ii++) {
    stt_charge++;
    element = $(tb)[ii];
    $(element).find(".stt-charge").html(stt_charge);
    quantity_charge = intVal($(element).find(".quantity_charge").val());
    price_charge = intVal($(element).find(".price_charge").val());
    amount_charge = quantity_charge * price_charge;
    $(element)
      .find(".td-total-amount-charge")
      .html(tnhFormatMoney(amount_charge));
    total_quantity_charge += quantity_charge;
    total_amount_charge += amount_charge;
  }
  $(".total-charge").html(tnhFormatMoney(total_amount_charge));
  grand_total = total_amount_charge + total_items;
  $(".grand-total").html(tnhFormatMoney(grand_total));
}

function totalPayment() {
  tb = '#table-payment tbody tr:not("[class^=not-tr]")';
  var table_payment = $(tb).length;
  var stt_payment = 0;
  pattern = [
    "of contract value advance payment by T/T after signing the contract",
    "by T/T before shipment and test run finished at the Seller factory",
    "balance after The Seller set up the machines in the Buyer factory",
  ];
  for (ii = 0; ii < table_payment; ii++) {
    stt_payment++;
    element = $(tb)[ii];
    if (ii < 3) {
      $(element).find(".name_payment").val(pattern[ii]);
    }
    $(element).find(".stt-payment").html(stt_payment);
  }
}

function formatTable(result) {
  if (!result.id) return result.text; // optgroup
  tr = "";
  if (result) {
    tr += '<td style="width: 33%;">' + fld(result.date) + "</td>";
    tr += '<td style="width: 33%;">' + result.text + "</td>";
    tr += '<td style="width: 33%;">' + result.customer_name + "</td>";
  }
  tableSelect =
    '<table class="tnh-table table-bordered dont-responsive-table">' +
    "<tbody>" +
    tr;
  "</tbody>" + "</table>";
  return tableSelect;
}

function addTabs(counterQuotes_index, data) {
  var tabs = $("#tab-items");
  var ul = tabs.find("ul");
  var content = tabs.find(".tab-content");
  $(".li-" + counterQuotes_index).remove();
  $(".content-" + counterQuotes_index).remove();
  if (typeof data != "undefined" && data != null) {
    html = data.info ? data.info : html_element["html"];
    $(
      '<li role="presentation" class="li-' +
        counterQuotes_index +
        '"><a href="#' +
        counterQuotes_index +
        '" aria-controls="home" role="tab" data-toggle="tab">' +
        data.text +
        "</a></li>",
    ).appendTo(ul);
    $(
      '<div role="tabpanel" class="tab-pane content-' +
        counterQuotes_index +
        '" id="' +
        counterQuotes_index +
        '">' +
        '<div class="row">' +
        '<div class="col-md-12">' +
        '<div class="col-md-12">' +
        '<textarea name="info[' +
        counterQuotes_index +
        ']" id="info' +
        counterQuotes_index +
        '" class="form-control info" rows="3">' +
        html +
        "</textarea>" +
        "</div>" +
        "</div>" +
        "</div>" +
        "</div>",
    ).appendTo(content);
  } else {
    // $('.li-'+counterQuotes_index).remove();
    // $('.content-'+counterQuotes_index).remove();
  }
  tinymce.remove("#info" + counterQuotes_index + "");
  init_editor("#info" + counterQuotes_index + "");
}

function addListPrice(_this) {
  cTr = $(_this).closest("tr");
  cTrChonse = cTr;
  cItemsId = cTr.find("input.items_id").val();

  if (!cItemsId) {
    bootbox.alert("Vui lòng chọn mặt hàng.");
    return;
  }

  customers = $("#customers").val();
  if (!customers) {
    bootbox.alert("Vui lòng chọn khách hàng.");
    return;
  }

  quote_stage_id = cTr.find("input.quote_stage_id").val();
  if (!quote_stage_id) {
    bootbox.alert("Vui lòng chọn bảng giá công đoạn khách hàng");
    return;
  }

  quote_item_id = cTr.find(".quote_item_id").val();
  cQuantity = cTr.find(".quantity").val();
  cCounter = cTr.find(".counterQuotes").val();
  cdataJson = cTr.find(".data_json").val();

  var dataPOST = {};
  dataPOST["cQuantity"] = cQuantity;
  dataPOST["cItemsId"] = cItemsId;
  dataPOST["quote_item_id"] = quote_item_id;
  dataPOST["cdataJson"] = cdataJson;
  dataPOST["customers"] = customers;
  dataPOST["quote_stage_id"] = quote_stage_id;
  dataPOST[token] = hash;

  link = site.base_url + "admin/handling_price/handlingPriceQuotes";
  $.ajax({
    url: link,
    type: "POST",
    dataType: "html",
    data: dataPOST,
  })
    .done(function (data) {
      $(".modal-select2").select2("close");
      $("#tnhModal").html(data);
    })
    .fail(function () {
      console.log("error");
    });
  $("#tnhModal").modal({ backdrop: "static", keyboard: false });
}

function addImportQuotes() {
  var form = $("#quotes"),
    formData = new FormData();
  // formParams = form.serializeArray();

  _customers = $("#customers").val();
  if (!_customers) {
    alert_float("danger", "Vui lòng chọn khách hàng");
    return;
  }

  $.each(form.find('input[type="file"]'), function (i, tag) {
    $.each($(tag)[0].files, function (i, file) {
      formData.append(tag.name, file);
    });
  });

  formData.append(csrfData["token_name"], csrfData["hash"]);
  formData.append("excel", 1);
  formData.append("customers", _customers);
  // $.each(formParams, function(i, val) {
  //     formData.append(val.name, val.value);
  // });

  $(".div-errors-excel").html("");
  $.ajax({
    url: site.base_url + "admin/quotes/import_quotes",
    type: "POST",
    dataType: "JSON",
    cache: false,
    contentType: false,
    processData: false,
    data: formData,
  })
    .done(function (data) {
      if (data.result) {
        if (data.errors) {
          $(".div-errors-excel").html(`<div class="alert alert-danger fade in">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                    <div>${data.errors}</div>
                </div>`);
        }

        if (data.dataItems) {
          $.each(data.dataItems, function (index, value) {
            json_item = value.json_item;

            images = value.images;
            if (!images) {
              images = site.base_url + "assets/images/tnh/no_image.png";
            }

            unit_name = value.unit_name;

            tdNumber = '<div class="stt text-center"></div>';
            tdCode =
              '<div class="td-code mbot10"><input type="hidden" name="counterQuotes[' +
              counterQuotes +
              ']" id="counterQuotes" class="form-control counterQuotes" value="' +
              counterQuotes +
              '">\
                            <input type="text" name="items_id[' +
              counterQuotes +
              ']" id="items_' +
              counterQuotes +
              '" class="items_id" style="width: 100%;" data-placeholder="' +
              lang_core["choose"] +
              '" value="' +
              value.items_id +
              '"></div>' +
              '<div class="type-item"></div>\
                            <div class="row-options">\
                                <a href="' +
              site.base_url +
              "admin/products/add_product" +
              '" class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Thêm thành phẩm</a>\
                            </div>';
            tdImage =
              '<div class="td-image">' +
              '<div class="preview_image" style="width: auto;">' +
              '<div class="display-block contract-attachment-wrapper img">' +
              '<div style="width:45px;">' +
              '<a href="' +
              images +
              '" data-lightbox="customer-profile" class="display-block mbot5">' +
              '<div class="">' +
              '<img src="' +
              images +
              '" style="border-radius: 50%">' +
              "</div>" +
              "</a>" +
              "</div>" +
              "</div>" +
              "</div>" +
              "</div>";
            tdQuote_stage =
              '<div class="td-quote_stage mbot10">' +
              '<input type="text" name="quote_stage_id[' +
              counterQuotes +
              ']" id="quote_stage_id_' +
              counterQuotes +
              '" class="quote_stage_id" style="width: 100%;" data-placeholder="' +
              lang_core["choose"] +
              '" value="' +
              value.quote_stage_id +
              '">' +
              "</div>";
            tdName =
              '<div class="td-item-name">' +
              lang_core["product_name"] +
              "</div>";
            tdTechnicalExplanation = `<div class="td-technical-explanation">
                        <textarea name="technical_explanation[${counterQuotes}]" class="form-control technical_explanation" placeholder="Diễn giải & thông số kỹ thuật" rows="3"></textarea>
                    </div>`;

            tdNum =
              '<div class="td-num"><input type="text" placeholder="" name="num[' +
              counterQuotes +
              ']" id="num[]" class="form-control num" style="width: 100%;" value=""></div>';
            tdOrigin =
              '<div class="td-origin"><input type="text" placeholder="' +
              lang_core["origin"] +
              '" name="origin[' +
              counterQuotes +
              ']" id="origin[]" class="form-control origin" style="width: 100px;" value=""></div>';
            tdUnit = '<div class="td-unit">' + unit_name + "</div>";
            tdQuantity =
              '<div class="td-quantity"><input type="text" name="quantity[' +
              counterQuotes +
              ']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="0"></div>';

            tdMOQ = `<div class="td-quantity" style="display: flex; align-items: center;">
                        <div style="width: 70px;">Từ</div>
                        <input type="text" name="moq[${counterQuotes}]" class="form-control moq number-format" style="width: 100%;" value="0">
                        <div style="width: 100px; margin-left: 5px; margin-right: 5px;"> - đến</div> 
                        <input type="text" name="moq_to[${counterQuotes}]" class="form-control moq_to number-format" style="width: 100%;" value="0">
                    </div>`;

            // __dataJson = JSON.stringify(value.dataJson);
            __dataJson = value.dataJson;
            tdPrice = `<div class="td-price">
                        <input type="hidden" name="data_json[${counterQuotes}]" class="form-control data_json" value='${__dataJson}'>
                        <input type="text" name="price[${counterQuotes}]" id="price[]" class="form-control price money-format" style="width: 100%;" value="${tnhFormatNumber(value.g)}">
                        <div class="mtop5"><i onclick="addListPrice(this)" class="btn btn-primary addListPrice">Chi tiết tính giá</i></div>
                    </div>`;

            tdTotalAmount = '<div class="td-total-amount text-right"></div>';

            tdDiscountPrecent =
              '<div class="td-discount"><input type="text" name="discount_precent_item[' +
              counterQuotes +
              ']" class="form-control discount_precent_item number-format" style="width: 100%;" value="0"></div>';

            tdLeadTime =
              '<div class="td-lead-time"><input type="number" name="leadtime[' +
              counterQuotes +
              ']" id="leadtime[]" class="form-control leadtime number-unformat" style="width: 100%;" value="0"></div>';
            tdNote =
              '<div class="td-note"><textarea name="note_items[' +
              counterQuotes +
              ']" id="note_items[]" class="form-control" rows="3"></textarea></div>';
            tdActions =
              '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

            rowNode = dt.row
              .add([
                tdNumber,
                tdCode,
                tdImage,
                tdQuote_stage,
                tdTechnicalExplanation,
                tdUnit,
                tdMOQ,
                tdPrice,
                tdDiscountPrecent,
                tdLeadTime,
                tdNote,
                tdActions,
              ])
              .draw(false)
              .node();
            ajaxSelectParamsCallback(
              $("#items_" + counterQuotes + ""),
              "admin/products/searchProductAndGoods",
              value.items_id,
              false,
              false,
              json_item,
            );
            ajaxSelectCallBack(
              $("#quote_stage_id_" + counterQuotes + ""),
              "admin/quotes/search_quote_stage/" + $("#customers").val(),
              value.quote_stage_id,
            );
            counterQuotes++;
          });
        }
        alert_float("success", data.message);
        totalProductionsQuotes();
      } else {
        alert_float("danger", data.message);
      }
    })
    .fail(function () {
      alert_float("danger", "Vui lòng xóa file chọn lại");
    });
}

var dt = "";
$(document).ready(function () {
  // init_editor('textarea[name="note_internal"]');
  // init_editor('textarea[name="note"]');
  $("#note_default").select2();
  $("#tax_id").select2();
  $(document).on("change", ".file_import_quotes", function () {
    if ($(this).val()) {
      $(".btn-import-submit").click();
      $("#file_import_quotes").val("");
    }
  });
  ajaxSelectFormatTableCallBack(
    "#pre_reference_no",
    "admin/quotes/searchPreReferenceNoQuotes",
  );
  ajaxSelectMultipleCallBack(
    "#productions_plan",
    "admin/manufactures/searchProductionsPlanForOrders",
    0,
  );
  ajaxSelectCustomerFormatTableCallBack(
    "#customers",
    "admin/clients/searchCustomers",
    $("#customers").val(),
  );

  $("#customers").change(function (event) {
    customer_id = $(this).val();
    data = event.added;
    if (data) {
      if (data.allowed_vat >= 1) {
        taxId = $('#tax_id option[data-rate="10.00"]').val();
        $("#tax_id").val(taxId).trigger("change");
      }

      $("#bale_parameters").val(data.bale_parameters);
    }
    ajaxSelectParamsCallback(
      "#person_contact",
      "admin/clients/searchContract",
      0,
      { customer_id: customer_id },
      true,
    );
    ajaxSelectParamsCallback(
      "#address_delivery",
      "admin/clients/searchQuotesAddressDelivery",
      0,
      { customer_id: customer_id },
      true,
    );
    ajaxSelectParamsCallback(
      "#quotation_request_id",
      "admin/quotes/searchQuotationRequest",
      $("#quotation_request_id").val(),
      { client_id: customer_id },
      true,
    );
  });

  $(document).on("click", ".add-address-delivery", function (event) {
    event.preventDefault();
    el = this;
    customer_id = $("#customers").val();
    link = "javascript:void(0)";
    if (customer_id) {
      link = site.base_url + "admin/clients/addShipping/" + customer_id;
      $.ajax({
        url: link,
        type: "GET",
        dataType: "html",
        data: {
          token: hash,
        },
      })
        .done(function (data) {
          $("#tnhModal").html(data);
        })
        .fail(function () {
          console.log("error");
        });
      $("#tnhModal").modal({ backdrop: "static", keyboard: true });
      // $(el).attr('href', link);
    } else {
      bootbox.alert(lang_orders["tnh_please_chosen_customer"]);
      // $(el).attr('href', link);
    }
  });

  dt = $("#tb-productions-quotes").DataTable({
    language: lang_datatables,
    searching: false,
    ordering: false,
    paging: false,
    info: false,
    // 'fixedHeader': true,
    // scrollY: true,
    // scrollY: '150px',
    // scrollX: true,
    fnRowCallback: function (nRow, aData, iDisplayIndex) {},
    preDrawCallback: function (settings) {
      pageScrollPos = window.scrollY;
    },
    drawCallback: function (settings) {
      $(document).scrollTop(pageScrollPos);
    },
  });

  var dtCharge = $("#tb-charge").DataTable({
    language: lang_datatables,
    searching: false,
    ordering: false,
    paging: false,
    info: false,
    fnRowCallback: function (nRow, aData, iDisplayIndex) {},
  });

  $(".add-row").on("click", function (event) {
    console.log("Thêm dòng: " + counterQuotes);
    event.preventDefault();
    tdNumber = '<div class="stt text-center"></div>';
    tdCode =
      '<div class="td-code mbot10"><input type="hidden" name="counterQuotes[' +
      counterQuotes +
      ']" id="counterQuotes" class="form-control counterQuotes" value="' +
      counterQuotes +
      '">\
                <input type="text" name="items_id[' +
      counterQuotes +
      ']" id="items_' +
      counterQuotes +
      '" class="items_id" style="width: 100%;" data-placeholder="' +
      lang_core["choose"] +
      '" value=""></div>' +
      '<div class="type-item"></div>\
                <div class="row-options">\
                    <a href="' +
      site.base_url +
      "admin/products/add_product" +
      '" class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Thêm thành phẩm</a>\
                </div>';

    tdImage =
      '<div class="td-image">' +
      '<div class="preview_image" style="width: auto;">' +
      '<div class="display-block contract-attachment-wrapper img">' +
      '<div style="width:45px;">' +
      '<a href="' +
      site.base_url +
      'assets/images/tnh/no_image.png" data-lightbox="customer-profile" class="display-block mbot5">' +
      '<div class="">' +
      '<img src="' +
      site.base_url +
      'assets/images/tnh/no_image.png" style="border-radius: 50%">' +
      "</div>" +
      "</a>" +
      "</div>" +
      "</div>" +
      "</div>" +
      "</div>";
    tdQuote_stage =
      '<div class="td-quote_stage mbot10">' +
      '<input type="text" name="quote_stage_id[' +
      counterQuotes +
      ']" id="quote_stage_id_' +
      counterQuotes +
      '" class="quote_stage_id" style="width: 100%;" data-placeholder="' +
      lang_core["choose"] +
      '" value="">' +
      "</div>";

    tdName =
      '<div class="td-item-name">' + lang_core["product_name"] + "</div>";
    tdTechnicalExplanation = `<div class="td-technical-explanation">
            <textarea name="technical_explanation[${counterQuotes}]" class="form-control technical_explanation" placeholder="Diễn giải & thông số kỹ thuật" rows="3"></textarea>
        </div>`;

    tdNum =
      '<div class="td-num"><input type="text" placeholder="" name="num[' +
      counterQuotes +
      ']" id="num[]" class="form-control num" style="width: 100%;" value=""></div>';
    tdUnit = '<div class="td-unit"></div>';
    tdOrigin =
      '<div class="td-origin"><input type="text" placeholder="' +
      lang_core["origin"] +
      '" name="origin[' +
      counterQuotes +
      ']" id="origin[]" class="form-control origin" style="width: 100px;" value=""></div>';
    tdUnit = '<div class="td-unit"></div>';
    tdQuantity =
      '<div class="td-quantity"><input type="text" name="quantity[' +
      counterQuotes +
      ']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="0"></div>';

    tdMOQ = `<div class="td-quantity" style="display: flex; align-items: center;">
            <div style="width: 70px;">Từ</div>
            <input type="text" name="moq[${counterQuotes}]" class="form-control moq number-format" style="width: 100%;" value="0">
            <div style="width: 100px; margin-left: 5px; margin-right: 5px;"> - đến</div> 
            <input type="text" name="moq_to[${counterQuotes}]" class="form-control moq_to number-format" style="width: 100%;" value="0">
        </div>`;

    tdPrice = `<div class="td-price">
            <input type="hidden" name="data_json[${counterQuotes}]" class="form-control data_json" value="">
            <input type="text" name="price[${counterQuotes}]" id="price[]" class="form-control price money-format" style="width: 100%;" value="0">
            <div class="mtop5"><i onclick="addListPrice(this)" class="btn btn-primary addListPrice">Chi tiết tính giá</i></div>
        </div>`;

    tdTotalAmount = '<div class="td-total-amount text-right"></div>';

    tdDiscountPrecent =
      '<div class="td-discount"><input type="text" name="discount_precent_item[' +
      counterQuotes +
      ']" class="form-control discount_precent_item number-format" style="width: 100%;" value="0"></div>';

    tdLeadTime =
      '<div class="td-lead-time"><input type="number" name="leadtime[' +
      counterQuotes +
      ']" id="leadtime[]" class="form-control leadtime number-unformat" style="width: 100%;" value="0"></div>';
    tdNote =
      '<div class="td-note"><textarea name="note_items[' +
      counterQuotes +
      ']" id="note_items[]" class="form-control" rows="3"></textarea></div>';
    tdActions =
      '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

    rowNode = dt.row
      .add([
        tdNumber,
        tdCode,
        tdImage,
        tdQuote_stage,
        tdTechnicalExplanation,
        tdUnit,
        tdMOQ,
        tdPrice,
        tdDiscountPrecent,
        tdLeadTime,
        tdNote,
        tdActions,
      ])
      .draw(false)
      .node();

    // var newTr = $('<tr role="row" class="odd"></tr>');
    // newTr.append('<td>'+tdNumber+'</td>');
    // newTr.append('<td>'+tdCode+'</td>');
    // newTr.append('<td>'+tdImage+'</td>');
    // newTr.append('<td>'+tdQuote_stage+'</td>');
    // newTr.append('<td>'+tdTechnicalExplanation+'</td>');
    // newTr.append('<td>'+tdUnit+'</td>');
    // newTr.append('<td>'+tdMOQ+'</td>');
    // newTr.append('<td>'+tdPrice+'</td>');
    // newTr.append('<td>'+tdDiscountPrecent+'</td>');
    // newTr.append('<td>'+tdLeadTime+'</td>');
    // newTr.append('<td>'+tdNote+'</td>');
    // newTr.append('<td>'+tdActions+'</td>');
    // $('table#tb-productions-quotes tbody').prepend(newTr);

    // selectAjax($('select#items_'+ counterQuotes +''), false, 'admin/products/searchProducts', false, attrs = 'products');
    ajaxSelectCallBack(
      $("#items_" + counterQuotes + ""),
      "admin/products/searchProductAndGoods",
      0,
    );
    ajaxSelectCallBack(
      $("#quote_stage_id_" + counterQuotes + ""),
      "admin/quotes/search_quote_stage/" + $("#customers").val(),
      0,
    );
    formatNumberPlugin();
    formatMoneyPlugin();
    counterQuotes++;
    totalProductionsQuotes();
  });

  $("#customers").change(function () {
    // ajaxSelectCallBack($('input.quote_stage_id'), 'admin/quotes/search_quote_stage/' + $('#customers').val(), 0); // tạm đóng
  });

  $(document).on("change", ".quote_stage_id", function (event) {
    var tr = $(this).closest("tr");
    tr.find(".addListPrice").click();
  });

  $(document).on("change", ".items_id", function (event) {
    event.preventDefault();
    data = event.added;
    sl = this;
    tr = $(sl).closest("tr");
    item_id = $(sl).val();
    counterQuotes_index = tr.find(".counterQuotes").val();
    if (item_id) {
      tr = $(sl).closest("tr");
      name = data.item_name;
      images = data.images;
      unit = data.unit_name;
      price_sell = data.price_sell;
      type_item = item_id.split("__")[1];
      if (images) {
        tr.find(".td-image a").attr("href", site.base_url + images);
        tr.find(".td-image img").attr("src", site.base_url + images);
      } else {
        tr.find(".td-image a").attr(
          "href",
          site.base_url + "assets/images/tnh/no_image.png",
        );
        tr.find(".td-image img").attr(
          "src",
          site.base_url + "assets/images/tnh/no_image.png",
        );
      }
      tr.find(".td-item-name").html(name);
      tr.find(".td-unit").html(unit);
      tr.find(".price").val(tnhFormatMoney(price_sell));

      if (type_item == "products") {
        tr.find(".type-item").html(
          '<span class="label label-success">' +
            lang_core[type_item] +
            "</span>",
        );
      } else if (type_item == "items") {
        tr.find(".type-item").html(
          '<span class="label label-primary">' +
            lang_core[type_item] +
            "</span>",
        );
      }

      lastrow = $("#tb-productions-quotes tbody tr")[
        $("#tb-productions-quotes tbody tr").length - 1
      ];
      if ($(lastrow).find(".items_id").select2("val")) {
        $(".add-row").click();
      }
    } else {
      tr.find(".td-item-name").html(lang_core["product_name"]);
      tr.find(".td-image a").attr(
        "href",
        site.base_url + "assets/images/tnh/no_image.png",
      );
      tr.find(".td-image img").attr(
        "src",
        site.base_url + "assets/images/tnh/no_image.png",
      );
      tr.find(".td-unit").html("");
      tr.find(".type-item").html("");
    }
    addTabs(counterQuotes_index, data);
  });

  $(document).on("click", ".add-row-payment", function (event) {
    event.preventDefault();
    tdSTT = '<td class="stt-payment text-center"></td>';
    tdPayment =
      '<td><input type="number" name="payment[]" id="payment[]" class="form-control payment" style="width: 100%;" placeholder="" value="0"></td>';
    tdName =
      '<td><input type="text" name="name_payment[]" id="name_payment[]" class="form-control name_payment" style="width: 100%;" placeholder="" value=""></td>';
    tdActions =
      '<td class="text-center"><i class="fa fa-remove btn btn-danger remove-row-payment"></i></td>';
    tr =
      "<tr>" + tdSTT + "" + tdPayment + "" + tdName + "" + tdActions + "</tr>";
    $("#table-payment").append(tr);
    totalPayment();
  });

  $(document).on(
    "change",
    ".quantity, .price, .freight_insirance, .tax_id",
    function (event) {
      totalProductionsQuotes();
    },
  );

  $(document).on("change", ".quantity_charge, .price_charge", function (event) {
    event.preventDefault();
    totalCharge();
  });

  $(document).on("click", ".remove-row", function (event) {
    event.preventDefault();
    tr = $(this).closest("tr");
    counterQuotes_index = tr.find(".counterQuotes").val();
    dt.row($(this).parents("tr")).remove().draw();
    totalProductionsQuotes();
    // item_id = $(sl).val();
    addTabs(counterQuotes_index, null);
  });

  $(document).on("click", ".remove-row-charge", function (event) {
    event.preventDefault();
    dtCharge.row($(this).parents("tr")).remove().draw();
    totalCharge();
  });

  $(document).on("click", ".remove-row-payment", function (event) {
    event.preventDefault();
    $(this).closest("tr").remove();
    totalPayment();
  });

  $(document).on("click", ".add-row-foot", function (event) {
    event.preventDefault();
    // $('.add-row').click();
  });

  $(document).on("change", "#currencies", function (event) {
    amount_to_vnd = $(this).select2().find(":selected").data("amount_to_vnd");
    $("#amount_to_vnd").val(tnhFormatMoney(amount_to_vnd));
  });

  $("#currencies").select2();
  init_editor("#note");
  if (edit == 0) {
    $(".add-row").click();
    // $('.add-row-charge').click();
    $(document).on("click", ".referesh-reference", function (event) {
      event.preventDefault();
      pre_quote_id = $("#pre_reference_no").val();
      $.ajax({
        url: site.base_url + "admin/quotes/refereshReferenceQuotes",
        type: "GET",
        dataType: "JSON",
        data: {
          token: hash,
          pre_quote_id: pre_quote_id,
          referesh: 1,
        },
      })
        .done(function (data) {
          if (data) {
            $("#reference_no").val(data.reference_no);
            alert_float("success", data.message);
          } else {
            alert_float("danger", "fail");
          }
        })
        .fail(function () {
          console.log("error");
        });
    });
  } else if (edit == 1) {
    customer_id = $("#customers").val();
    console.log(customer_id);
    ajaxSelectParamsCallback(
      "#person_contact",
      "admin/clients/searchContract",
      $("#person_contact").val(),
      { customer_id: customer_id },
      true,
    );
    ajaxSelectParamsCallback(
      "#address_delivery",
      "admin/clients/searchQuotesAddressDelivery",
      $("#address_delivery").val(),
      { customer_id: customer_id },
      true,
    );
    for (i = 0; i < counterQuotes; i++) {
      ajaxSelectCallBack(
        $("#items_" + i + ""),
        "admin/products/searchProductAndGoods",
        $("#items_" + i + "").val(),
      );
      ajaxSelectCallBack(
        $("#quote_stage_id_" + i + ""),
        "admin/quotes/search_quote_stage/" + $("#customers").val(),
        $("#quote_stage_id_" + i).val(),
      );
      init_editor("#info" + i + "");
    }
  }

  ajaxSelectParamsCallback(
    "#quotation_request_id",
    "admin/quotes/searchQuotationRequest",
    $("#quotation_request_id").val(),
    { client_id: $("#customers").val() },
    true,
  );

  appValidateForm(
    $("#quotes"),
    {
      reference_no: "required",
      date: "required",
      customers: "required",
      currencies: "required",
      amount_to_vnd: "required",
      id_branch: "required",
    },
    db,
  );

  function db(form) {
    if (count_errors > 0) {
      alert_float("danger", lang_core["check_date_enter"]);
      return;
    }
    $(".add-quotes").attr("disabled", "disabled");
    // tinymce.get('note').save();
    // tinymce.get('list_parts_origin').save();

    // for (var i = 0; i < counterQuotes; i++) {
    //     tinymce.get('info'+i+'').save();
    // }

    for (var i = 0; i < tinymce.editors.length; i++) {
      tinymce.editors[i].save();
    }

    // tinymce.get('note').save();
    // var data = $(form).serialize();
    var url = form.action;
    var form = $(form),
      formData = new FormData(),
      formParams = form.serializeArray();

    $.each(form.find('input[type="file"]'), function (i, tag) {
      $.each($(tag)[0].files, function (i, file) {
        formData.append(tag.name, file);
      });
    });
    $.each(formParams, function (i, val) {
      formData.append(val.name, val.value);
    });

    $.ajax({
      // url : site.base_url+'admin/business_plan/add',
      url: url,
      type: "POST",
      dataType: "JSON",
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
    })
      .done(function (data) {
        if (data.result) {
          alert_float("success", data.message);
          window.location.href = site.base_url + "admin/quotes";
        } else {
          alert_float("danger", data.message);
          $(".add-quotes").removeAttr("disabled", "disabled");
        }
      })
      .fail(function () {
        alert_float("danger", lang_core["errors"]);
        $(".add-quotes").removeAttr("disabled", "disabled");
      });
    return false;
  }

  $("body").on("change", "#SearchQR", function (e) {
    var code = $(this).val();
    if (code) {
      $.ajax({
        url: site.base_url + "admin/quotes/searchQR",
        type: "POST",
        dataType: "JSON",
        data: {
          code: code,
          csrf_token_name: hash,
        },
      })
        .done(function (data) {
          if (data.result) {
            alert_float("success", data.message);
            createTrItemAuto(data.items);
          } else {
            alert_float("danger", data.message);
          }
        })
        .fail(function () {});
    }
    $("#SearchQR").val("");
  });

  function createTrItemAuto(item) {
    tdNumber = '<div class="stt text-center"></div>';
    if (item.type == "product") {
      var itemTag =
        '<span class="label label-success">' +
        lang_core["products"] +
        "</span>";
    } else {
      var itemTag =
        '<span class="label label-primary">' + lang_core[item.type] + "</span>";
    }
    tdCode =
      '<div class="td-code mbot10"><input type="hidden" name="counterQuotes[' +
      counterQuotes +
      ']" id="counterQuotes" class="form-control counterQuotes" value="' +
      counterQuotes +
      '">\
                <input type="text" name="items_id[' +
      counterQuotes +
      ']" id="items_' +
      counterQuotes +
      '" class="items_id" style="width: 100%;" data-placeholder="' +
      lang_core["choose"] +
      '" value=""></div>' +
      '<div class="type-item">' +
      itemTag +
      "</div>\
                ";

    tdImage =
      '<div class="td-image">' +
      '<div class="preview_image" style="width: auto;">' +
      '<div class="display-block contract-attachment-wrapper img">' +
      '<div style="width:45px;">' +
      '<a href="' +
      item.avatar +
      '" data-lightbox="customer-profile" class="display-block mbot5">' +
      '<div class="">' +
      '<img src="' +
      item.avatar +
      '" style="border-radius: 50%">' +
      "</div>" +
      "</a>" +
      "</div>" +
      "</div>" +
      "</div>" +
      "</div>";
    tdQuote_stage =
      '<div class="td-quote_stage mbot10">' +
      '<input type="text" name="quote_stage_id[' +
      counterQuotes +
      ']" id="quote_stage_id_' +
      counterQuotes +
      '" class="quote_stage_id" style="width: 100%;" data-placeholder="' +
      lang_core["choose"] +
      '" value="">' +
      "</div>";

    tdName =
      '<div class="td-item-name">' + lang_core["product_name"] + "</div>";
    tdTechnicalExplanation = `<div class="td-technical-explanation">
            <textarea name="technical_explanation[${counterQuotes}]" class="form-control technical_explanation" placeholder="Diễn giải & thông số kỹ thuật" rows="3"></textarea>
        </div>`;

    tdNum =
      '<div class="td-num"><input type="text" placeholder="" name="num[' +
      counterQuotes +
      ']" id="num[]" class="form-control num" style="width: 100%;" value=""></div>';
    tdOrigin =
      '<div class="td-origin"><input type="text" placeholder="' +
      lang_core["origin"] +
      '" name="origin[' +
      counterQuotes +
      ']" id="origin[]" class="form-control origin" style="width: 100px;" value=""></div>';
    var unit_name = item.unit_name;
    if (item.unit_name == null) {
      unit_name = "";
    }
    tdUnit = '<div class="td-unit">' + unit_name + "</div>";
    tdQuantity =
      '<div class="td-quantity"><input type="text" name="quantity[' +
      counterQuotes +
      ']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="0"></div>';

    tdMOQ = `<td><div class="td-quantity" style="display: flex; align-items: center;">
            <div style="width: 70px;">Từ</div>
            <input type="text" name="moq[${counterQuotes}]" class="form-control moq number-format" style="width: 100%;" value="0">
            <div style="width: 100px; margin-left: 5px; margin-right: 5px;"> - đến</div> 
            <input type="text" name="moq_to[${counterQuotes}]" class="form-control moq_to number-format" style="width: 100%;" value="0">
        </div></td>`;

    tdPrice = `<td><div class="td-price">
            <input type="hidden" name="data_json[${counterQuotes}]" class="form-control data_json" value="">
            <input type="text" name="price[${counterQuotes}]" id="price[]" class="form-control price money-format" style="width: 100%;" value="${item.price_sell}">
            <div class="mtop5"><i onclick="addListPrice(this)" class="btn btn-primary addListPrice">Chi tiết tính giá</i></div>
        </div></td>`;

    tdTotalAmount = '<div class="td-total-amount text-right"></div>';

    tdDiscountPrecent =
      '<div class="td-discount"><input type="text" name="discount_precent_item[' +
      counterQuotes +
      ']" class="form-control discount_precent_item number-format" style="width: 100%;" value="0"></div>';

    tdLeadTime =
      '<div class="td-lead-time"><input type="number" name="leadtime[' +
      counterQuotes +
      ']" id="leadtime[]" class="form-control leadtime number-unformat" style="width: 100%;" value="0"></div>';
    tdNote =
      '<div class="td-note"><textarea name="note_items[' +
      counterQuotes +
      ']" id="note_items[]" class="form-control" rows="3"></textarea></div>';
    tdActions =
      '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

    // if (!$('#tb-productions-quotes tbody tr.items_id').find('input[value=hau]').length) {
    //     $("#tb-productions-quotes tbody tr").last().find(".remove-row").trigger("click");
    // }
    // $('#tb-productions-quotes tbody tr.items_id input[value=""]').each(function() {
    //     $(this).closest('tr').find('.remove-row').click();
    // });
    $("#tb-productions-quotes tbody tr input.items_id").each(function () {
      var selectValue = $(this).val();
      if (selectValue === "") {
        console.log($(this));
        $(this).closest("tr").find(".remove-row").click();
      }
    });
    rowNode = dt.row
      .add([
        tdNumber,
        tdCode,
        tdImage,
        tdQuote_stage,
        tdTechnicalExplanation,
        tdUnit,
        tdMOQ,
        tdPrice,
        tdDiscountPrecent,
        tdLeadTime,
        tdNote,
        tdActions,
      ])
      .draw(false)
      .node();

    // var newTr = $('<tr role="row" class="odd"></tr>');
    // newTr.append(tdNumber);
    // newTr.append(tdCode);
    // newTr.append(tdImage);
    // newTr.append(tdQuote_stage);
    // newTr.append(tdTechnicalExplanation);
    // newTr.append(tdUnit);
    // newTr.append(tdMOQ);
    // newTr.append(tdPrice);
    // newTr.append(tdDiscountPrecent);
    // newTr.append(tdLeadTime);
    // newTr.append(tdNote);
    // newTr.append(tdActions);
    // $('table#tb-productions-quotes tbody').prepend(newTr);

    if (item.type == "product") {
      item.type = "products";
    } else if (item.type == "materials") {
      item.type = "items";
    }
    item_id = item.id + "__" + item.type;
    ajaxSelectCallBack(
      $("#items_" + counterQuotes + ""),
      "admin/products/searchProductAndGoods",
      item_id,
    );
    ajaxSelectCallBack(
      $("#quote_stage_id_" + counterQuotes + ""),
      "admin/quotes/search_quote_stage/" + $("#customers").val(),
      0,
    );
    formatNumberPlugin();
    formatMoneyPlugin();
    counterQuotes++;
    totalProductionsQuotes();
    // $('#items_'+(counterQuotes-1)+'').change();

    // $('.add-row').click();
  }
});
