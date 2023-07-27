
/**
 * Sidebar Navigation
 */
var navs = document.querySelectorAll('#nav-sidebar .primary-link');
navs.forEach(function (nav) {
	nav.addEventListener('click', function (e) {
		e.preventDefault();
		var caret = nav.querySelector('.nav-caret'),
			secondaryNav = nav.parentNode.querySelector('.secondary');

		if (secondaryNav.classList.contains('hidden')) {
			secondaryNav.classList.remove('hidden');
			caret.classList.remove('fa-caret-right');
			caret.classList.add('fa-caret-down');
		} else {
			secondaryNav.classList.add('hidden');
			caret.classList.remove('fa-caret-down');
			caret.classList.add('fa-caret-right');
		}
	});
});

/**
 * Generate Coupon
 */
//var generateCoupon = document.querySelectorAll('#generateCoupon');
$(document).on('click', '#generateCoupon', function (e) {
	e.preventDefault();
	$.ajax({
		type: "POST",
		url: "superuser/coupons?get-coupon",
		data: {
			user_id: '{{session.user_id}}'
		},
		success: function (data) {
			toastr.success("Coupon generated", "Success")
			$('#coupon').val(data);
		},
		error: function (data) {
			toastr.error(data.responseText ? data.responseText : data, "error")
		}
	})
});


$(document).on('change', '#no_limit', function (e) {
	if ($('#no_limit').is(':checked')) {
		$('#user_allowed').attr('readonly', true);
	} else {
		$('#user_allowed').attr('readonly', false);
	}
});

/**
 * View deal onclick function
 */
document.querySelectorAll('.deal-row td').forEach(function (td) {
	if (td.lang != "action") {
		var row = td.parentElement;
		td.addEventListener('click', function (e) {
			e.preventDefault();
			var id = row.dataset.id;
			window.location.href = '/deals/view?id=' + id;
			console.log("Yes");
		});
	}
});

/**
 * View user onclick function
 */
document.querySelectorAll('.user-row').forEach(function (row) {
	row.addEventListener('click', function (e) {
		e.preventDefault();
		var id = row.dataset.id;
		window.location.href = '/admin/users?id=' + id;
	});
});
document.querySelectorAll('.user-row .link').forEach(function (link) {
	link.addEventListener('click', function (e) {
		e.stopPropagation();
	});
});

/**
 * View sales person onclick function
 */
document.querySelectorAll('.sale-row').forEach(function (row) {
	row.addEventListener('click', function (e) {
		e.preventDefault();
		var id = row.dataset.id;
		window.location.href = '/admin/sales?id=' + id;
	});
});
document.querySelectorAll('.sale-row .link').forEach(function (link) {
	link.addEventListener('click', function (e) {
		e.stopPropagation();
	});
});

/**
 * View desk manager onclick function
 */
document.querySelectorAll('.desk-row').forEach(function (row) {
	row.addEventListener('click', function (e) {
		e.preventDefault();
		var id = row.dataset.id;
		window.location.href = '/admin/desk?id=' + id;
	});
});
document.querySelectorAll('.desk-row .link').forEach(function (link) {
	link.addEventListener('click', function (e) {
		e.stopPropagation();
	});
});

/**
 * View finance person onclick function
 */
document.querySelectorAll('.finance-row').forEach(function (row) {
	row.addEventListener('click', function (e) {
		e.preventDefault();
		var id = row.dataset.id;
		window.location.href = '/admin/finance?id=' + id;
	});
});
document.querySelectorAll('.finance-row .link').forEach(function (link) {
	link.addEventListener('click', function (e) {
		e.stopPropagation();
	});
});

/**
 * View lenders onclick function
 */
document.querySelectorAll('.lender-row').forEach(function (row) {
	row.addEventListener('click', function (e) {
		e.preventDefault();
		var id = row.dataset.id;
		window.location.href = '/admin/lending?id=' + id;
	});
});
document.querySelectorAll('.lender-row .link').forEach(function (link) {
	link.addEventListener('click', function (e) {
		e.stopPropagation();
	});
});

/**
 * View flooring companies onclick function
 */
document.querySelectorAll('.flooring-row').forEach(function (row) {
	row.addEventListener('click', function (e) {
		e.preventDefault();
		var id = row.dataset.id;
		window.location.href = '/admin/flooring?id=' + id;
	});
});
document.querySelectorAll('.flooring-row .link').forEach(function (link) {
	link.addEventListener('click', function (e) {
		e.stopPropagation();
	});
});

/**
 * View gap provider onclick function
 */
document.querySelectorAll('.gap-row').forEach(function (row) {
	row.addEventListener('click', function (e) {
		e.preventDefault();
		var id = row.dataset.id;
		window.location.href = '/admin/gap?id=' + id;
	});
});
document.querySelectorAll('.gap-row .link').forEach(function (link) {
	link.addEventListener('click', function (e) {
		e.stopPropagation();
	});
});

/**
 * View warranty provider onclick function
 */
document.querySelectorAll('.warranty-row').forEach(function (row) {
	row.addEventListener('click', function (e) {
		e.preventDefault();
		var id = row.dataset.id;
		window.location.href = '/admin/warranty?id=' + id;
	});
});
document.querySelectorAll('.warranty-row .link').forEach(function (link) {
	link.addEventListener('click', function (e) {
		e.stopPropagation();
	});
});

/**
 * View customers onclick function
 */
document.querySelectorAll('.customer-row').forEach(function (row) {
	row.addEventListener('click', function (e) {
		e.preventDefault();
		var id = row.dataset.id;
		window.location.href = '/superuser/customers?id=' + id;
	});
});

function changeList(id) {
	var summary = "#summary-" + id;
	var deallist = "#deallist-" + id;
	var subTotalList = "#subTotalList-" + id;
	if ($(summary).hasClass('display-show')) {
		$(summary).removeClass("display-show");
		$(deallist).removeClass("display-hide");
		$(subTotalList).removeClass("display-hide");

		$(summary).addClass("display-hide");
		$(deallist).addClass("display-show");
		$(subTotalList).addClass("display-show");
	} else {
		$(summary).removeClass("display-hide");
		$(deallist).removeClass("display-show");
		$(subTotalList).removeClass("display-show");
		$(summary).addClass("display-show");
		$(deallist).addClass("display-hide");
		$(subTotalList).addClass("display-hide");
	}
}
function sortColumn(id, sortdir) {
	var params = new URLSearchParams(window.location.search);
	if (params.has("sortid")) {
		params.set('sortid', id);
	} else {
		params.append('sortid', id);
	}

	if (params.has('sortdir')) {
		params.set('sortdir', sortdir);
	} else {
		params.append("sortdir", sortdir);
	}
	// window.location.search = params;
	window.history.replaceState({}, '', `${location.pathname}?${params}`);
	location.reload();
}

/**Custom JavaScript Table AJAX */
var UserCustomTable = $(".UserCustomTable");
if (UserCustomTable !== null) {
	var UserTableCollect = (UserCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "admin/users?table-list",
		"pageLength": 50,
		"createdRow": function (row, data, dataIndex) {
			if (data[3] == "1") {
				$(row).addClass('important');
			}
			$(row).css({ "font-size": "14px", "padding-left": "5px", "padding-right": "5px" });
		},
		"columnDefs": [
			{ "targets": [0, 1], width: '40%' },
			{
				"targets": 1,
				"createdCell": function (td, cellData, rowData, row, col) {
					var name = rowData[1] + ' ' + rowData[5];
					$(td).html(name);
				},
				width: '10%%'
			},
			{
				"targets": 2,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[2] == '1') {
						color = 'green';

					} else if (rowData[2] == '0') {
						color = 'red';

					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html('<i class="fas fa-circle" style="color: ' + color + '"></i>');
				},
				width: '10%%'
			},
			{
				"targets": 3,
				"createdCell": function (td, cellData, rowData, row, col) {
					var action;
					var color;
					var textAction;
					if (rowData['4'] > '0') {
						action = '<i class="fas fa-ban"></i>';
						color = 'red';
						textAction = "Inactive";
					} else {
						action = '<i class="fas fa-check"></i>';
						color = 'green';
						textAction = "Approve";
					}
					// $(td).html('<a class="link p-1" href="admin/users?id='+cellData+'&action=reset">Reset</a>');
					var html = `<a href="admin/users?id=${cellData}&action=reset" title="Reset Password" class="new-btn btn" style="padding-right:15px; padding-left:15px"><i class="fas fa-undo"></i></a>`;
					html += `<a style="color:${color}" href="admin/users?id=${rowData[3]}&action=toggle" title="${textAction}" class="new-btn btn" style="padding-right:15px; padding-left:15px">${action}</a>`;
					html += `<a href="/admin/users?id=${rowData[3]}" class="new-btn btn" style="padding-right:15px; padding-left:15px" title="Edit"><i class="fas fa-edit"></i></a>`;
					$(td).html(html);
				},
				width: '10%%'
			}
			// {
			// 	"targets": 4,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		var action;
			// 		if(cellData > '0') {
			// 			action = '<i class="fas fa-ban"></i>';
			// 		} else {
			// 			action = '<i class="fas fa-check"></i>';
			// 		}
			// 		$(td).css('width', '40px');
			// 		$(td).css('vertical-align', 'middle');
			// 		$(td).html('<a style="color:" href="admin/users?id='+rowData[3]+'&action=toggle" class="new-btn btn p-1">'+action+'</a>');
			// 	}
			// },
			// {
			// 	"targets": 5,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		$(td).css('vertical-align', 'middle');
			// 		$(td).html('<a href="/admin/users?id='+rowData[3]+'" class="new-btn btn p-1"><i class="fas fa-edit"></i></a>');
			// 	}
			// }
		]
	});
	$(UserTableCollect.column(2).header()).css('width', '400');
	$(UserTableCollect.column(2).header()).style = '400';
	$(UserTableCollect.column(3).header()).css('width', '400');
	$('#userStatus').off('change');
	$('#userStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "admin/users?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		UserTableCollect.search(str).draw();
	})
}

var SalesTable = $(".SalesTable");
if (SalesTable !== null) {
	var SalesTableCollection = (SalesTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "admin/sales?table-list",
		"pageLength": 10,
		"columnDefs": [
			{ "targets": [0], width: '90%' },
			{
				"targets": 1,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[1] == '1') {
						color = 'green';
					} else if (rowData[1] == '0') {
						color = 'red';
					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html(`<i class="fas fa-circle" style="color:${color}"></i>`);
				},
				"width": '5%'
			},
			{
				"targets": 2,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					var icon;
					var textAction;
					if (rowData[1] == '1') {
						color = 'red';
						icon = 'ban';
						textAction = 'Cancel';
					} else if (rowData[1] == '0') {
						color = 'green';
						icon = 'check';
						textAction = 'Approve';
					}
					$(td).css('vertical-align', 'middle');
					var html = `<a title=${textAction} style="color:${color}" href="admin/sales?id=${cellData}&action=toggle" class="new-btn btn">
									<i class='fas fa-${icon}'></i>
								</a>`;
					html += `<a title="Edit Sales Person"  href="/admin/sales?id=${rowData[2]}" class="new-btn btn"><i class="fas fa-edit"></i></a>`;
					$(td).html(html);
				}
			}
			// {
			// 	"targets": 3,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		$(td).css('vertical-align', 'middle');
			// 		$(td).html('<a href="/admin/sales?id='+rowData[2]+'" class="new-btn btn"><i class="fas fa-edit"></i></a>');
			// 	}
			// }
		]
	});
	$('#salesDropdownStatus').off('change');
	$('#salesDropdownStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "admin/sales?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		SalesTableCollection.search(str).draw();
	})
}

var CouponCustomTable = $(".CouponCustomTable");
if (CouponCustomTable !== null) {
	var CouponTableCollect = (CouponCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "superuser/coupons?table-list",
		"pageLength": 50,
		"createdRow": function (row, data, dataIndex) {
			if (data[3] == "1") {
				$(row).addClass('important');
			}
			$(row).css({ "font-size": "14px", "padding-left": "5px", "padding-right": "5px" });
		},
		"columnDefs": [
			{ "targets": 0, width: '25%' },
			{
				"targets": 1,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (new Date(rowData[6]) >= new Date()) {
						color = 'green';
					} else {
						color = 'red';
					}
					$(td).css('vertical-align', 'middle');
					$(td).html('<span>' + rowData[1] + '</span> &nbsp;&nbsp;&nbsp;  <span style="color: ' + color + '">' + rowData[6] + '</span>');
				},
				width: '25%'
			},
			{ "targets": 2, width: '10%' },
			{
				"targets": 4,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[4] == '1') {
						color = 'green';

					} else if (rowData[4] == '0') {
						color = 'red';

					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html('<i class="fas fa-circle" style="color: ' + color + '"></i>');
				},
				width: '10%'
			},
			{
				"targets": 3,
				"createdCell": function (td, cellData, rowData, row, col) {
					if (rowData[7] == '1') {
						sign = '$';

					} else if (rowData[7] == '2') {
						sign = '%';

					}
					$(td).css('vertical-align', 'middle');
					//$(td).css('text-align', 'center');
					$(td).html('(' + sign + ')' + cellData);
				},
				width: '10%'
			},
			{
				"targets": 5,
				"createdCell": function (td, cellData, rowData, row, col) {
					var action;
					var color;
					var textAction;
					// $(td).html('<a class="link p-1" href="admin/users?id='+cellData+'&action=reset">Reset</a>');
					var html = ``;
					html += `<a href="/superuser/coupons?id=${rowData[5]}" class="new-btn btn" style="padding-right:15px; padding-left:15px" title="Edit"><i class="fas fa-edit"></i></a>`;
					//html += `<a href="/admin/coupons?id=${rowData[5]}&action=delete" style="padding-right:15px; padding-left:15px;" title="Delete"><i class="fas fa-trash"></i></a>`;
					html += `<a title="Delete" href="javascript:;" onclick="deleteCoupon(${rowData[5]})">
								<i class="fas fa-trash"></i>
							</a>`;
					$(td).css('text-align', 'center');
					$(td).html(html);
				},
				width: '10%'
			}
		]
	});
	$(CouponTableCollect.column(2).header()).css('width', '400');
	$(CouponTableCollect.column(2).header()).style = '400';
	console.log(CouponTableCollect.column(0).header());
	$(CouponTableCollect.column(3).header()).css('width', '400');
	$('#couponStatus').off('change');
	$('#couponStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "superuser/coupons?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully“", "Success")
				//console.log(data);
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		CouponTableCollect.search(str).draw();
	})
}

var CustomerCustomTable = $(".CustomerCustomTable");
if (CustomerCustomTable !== null) {
	var CustomerTable = (CustomerCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "superuser/customers?table-list",
		"pageLength": 10,
		"columnDefs": [
			{
				"targets": 6,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[6] == '1') {
						color = 'green';

					} else if (rowData[6] == '0') {
						color = 'red';

					}
					$(td).css('vertical-align', 'middle');
					$(td).html('<i class="fas fa-circle" style="color: ' + color + '"></i>');
				}
			},
			{
				"targets": 7,
				"createdCell": function (td, cellData, rowData, row, col) {
					$(td).css('vertical-align', 'middle');
					$(td).html('<a href="/superuser/customers?id=' + rowData[7] + '" class="new-btn btn"><i class="fas fa-edit"></i></a>');
				}
			}
		]
	});
	$('#customerDropdownStatus').off('change');
	$('#customerDropdownStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "superuser/customers?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully“", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		CustomerTable.search(str).draw();
	})
}

var ViewDealsCustomTable = $(".ViewDealsCustomTable");
if (ViewDealsCustomTable !== null) {
	var permisson = '';
	var viewDealsTable = (ViewDealsCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"autoWidth": false,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": {
			"url": "deals/view?table-list",
			"dataSrc": function (json) {
				//Make your callback here.
				permisson = json.delete_permission;
				return json.data;
			}
		},
		"pageLength": 50,
		order: [[1, 'desc']],
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
			$(nRow).attr('class', 'deal-row');
			$(nRow).attr('data-id', aData[11]);
			$(nRow).attr('data-permission', aData[9]);
			//$(nRow).attr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td').attr('onclick', 'changeDealStyle(' + aData[11] + ')');
			$(nRow).find('td:eq(9)').removeAttr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td:eq(10)').removeAttr('onclick', 'changeDealStyle(this)');

		},
		"columnDefs": [
			// {
			// 	"targets": 9,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		var color;
			// 		rowData[9] == 'none' ? color = "green" : color = "red";
			// 		var html = `<i class="fas fa-circle" style="color:${color}"></i>`;
			// 		$(td).html(html);
			// 	}
			// },
			{
				"targets": 8,
				"createdCell": function (td, cellData, rowData, row, col) {
					var str = rowData[8] + ', ' + rowData[12];
					$(td).html(str != ', ' ? str : '');
				}
			},
			{
				"targets": 9,
				"createdCell": function (td, cellData, rowData, row, col) {

					$(td).html(`<a href="javascript:;" onclick="viewNotes(${rowData[11]})">
					<i class="fas fa-sticky-note"></i>
					</a>`);
				}
			},
			{
				"targets": 10,
				"createdCell": function (td, cellData, rowData, row, col) {
					var rowValue = [];
					if (rowData[10] == '0') {
						rowValue.push("green");
						rowValue.push("unlock");
						rowValue.push("lock");
					} else if (rowData[10] == '1') {
						rowValue.push("red");
						rowValue.push("lock");
						rowValue.push("unlock");
					}
					var html = `<a title=${rowValue[2]} href="javascript:void(0);" onclick="lockOrUnlockDeal(${rowData[11]})">`;
					html += `<input type="text" hidden id="${rowData[11]}_permission" value=${rowValue[2]}>`;
					html += `<i class="fas fa-${rowValue[1]}" style="color:${rowValue[0]}"></i></a>`;
					if (permisson != 'no') {
						html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
								<i class="fas fa-trash"></i>
							</a>`;
					}
					$(td).html(html);
				}
			}
		]
	});
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		viewDealsTable.search(str).draw();
	});


	$("#deal_filter_form").submit(function (e) {
		e.preventDefault();
		var route = $(this).attr('data-route');
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: "deals/" + route + "?dropdown",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (data) {
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				console.log(data)
			}
		})
	});

	$('#clearFilterBtn').off('click');
	$('#clearFilterBtn').click(function (e) {
		e.preventDefault();
		var route = $(this).attr('data-route');
		$.ajax({
			type: "POST",
			url: "deals/" + route + "?clear-filters",
			data: {
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				location.reload();
			},
			error: function (data) {

			}
		})
	});



	$('.deals-links a').off('click');
	$('.deals-links a').click(function (e) {
		e.preventDefault();
		var route = $(this).attr('href');
		$.ajax({
			type: "POST",
			url: route + "?clear-filters",
			data: {
				route: route,
				user_id: '{{session.user_id}}'
			},
			dataType: 'json',
			success: function (data) {
				window.location.href = document.location.origin + data.url;
			},
			error: function (data) {

			}
		})
	});

	$('.reports-links a').off('click');
	$('.reports-links a').click(function (e) {
		e.preventDefault();
		var route = $(this).attr('href');
		$.ajax({
			type: "POST",
			url: route + "?reportUrl",
			data: {
				route: route,
				page: 'report',
			},
			dataType: 'json',
			success: function (data) {
				window.location.href = document.location.origin + data.url;
			},
			error: function (data) { }
		})
	});


	$('#filterDealsBtn').off('click');
	$('#filterDealsBtn').click(function (e) {
		e.preventDefault();
		var val = $('.include_locked_unlocked select').val();
		var route = $(this).attr('data-route');
		$.ajax({
			type: "POST",
			url: "deals/" + route + "?dropdown",
			data: {
				status: val,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});


	$('#dealsDropdownStatus').off('change');
	$('#dealsDropdownStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "deals/view?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});
}

var ViewPendingDealsCustomTable = $(".ViewPendingDealsCustomTable");
if (ViewPendingDealsCustomTable !== null) {
	var permisson = '';
	var CustomerTable = (ViewPendingDealsCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"autoWidth": false,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		//"ajax": "deals/pending?table-list",
		"ajax": {
			"url": "deals/pending?table-list",
			"dataSrc": function (json) {
				//Make your callback here.
				permisson = json.delete_permission;
				return json.data;
			}
		},
		"pageLength": 50,
		order: [[1, 'desc']],
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
			$(nRow).attr('class', 'deal-row');
			$(nRow).attr('data-id', aData[11]);
			$(nRow).attr('data-permission', aData[9]);
			//$(nRow).attr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td').attr('onclick', 'changeDealStyle(' + aData[11] + ')');
			$(nRow).find('td:eq(9)').removeAttr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td:eq(10)').removeAttr('onclick', 'changeDealStyle(this)');
		},
		"columnDefs": [
			// {
			// 	"targets": 9,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		var color;
			// 		rowData[9] == 'none' ? color = "green" : color = "red";
			// 		var html = `<i class="fas fa-circle" style="color:${color}"></i>`;
			// 		$(td).html(html);
			// 	}
			// },
			{
				"targets": 8,
				"createdCell": function (td, cellData, rowData, row, col) {
					var str = rowData[8] + ', ' + rowData[12];
					$(td).html(str != ', ' ? str : '');
				}
			},
			{
				"targets": 9,
				"createdCell": function (td, cellData, rowData, row, col) {

					$(td).html(`<a href="javascript:;" onclick="viewNotes(${rowData[11]})">
					<i class="fas fa-sticky-note"></i>
					</a>`);
				}
			},
			{
				"targets": 10,
				"createdCell": function (td, cellData, rowData, row, col) {
					var rowValue = [];
					if (rowData[10] == '0') {
						rowValue.push("green");
						rowValue.push("unlock");
						rowValue.push("lock");
					} else if (rowData[10] == '1') {
						rowValue.push("red");
						rowValue.push("lock");
						rowValue.push("unlock");
					}
					var html = `<a title=${rowValue[2]} href="javascript:;" onclick="lockOrUnlockDeal(${rowData[11]})">`;
					html += `<input type="text" hidden id="${rowData[11]}_permission" value=${rowValue[2]}>`;
					html += `<i class="fas fa-${rowValue[1]}" style="color:${rowValue[0]}"></i></a>`;
					if (permisson != 'no') {
						html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
								<i class="fas fa-trash"></i>
							</a>`;
					}
					// html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
					// 			<i class="fas fa-trash"></i>
					// 		</a>`;
					$(td).html(html);
				}
			}
		]
	});
}

var ViewUnfundedDealsCustomTable = $(".UnfundedPendingDealsCustomTable");
if (ViewUnfundedDealsCustomTable !== null) {
	var permisson = '';
	var CustomerTable = (ViewUnfundedDealsCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"autoWidth": false,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": {
			"url": "deals/unfunded?table-list",
			"dataSrc": function (json) {
				//Make your callback here.
				permisson = json.delete_permission;
				return json.data;
			}
		},
		"pageLength": 50,
		order: [[0, 'desc']],
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
			$(nRow).attr('class', 'deal-row');
			$(nRow).attr('data-id', aData[11]);
			$(nRow).attr('data-permission', aData[9]);
			//$(nRow).attr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td').attr('onclick', 'changeDealStyle(' + aData[11] + ')');
			$(nRow).find('td:eq(9)').removeAttr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td:eq(10)').removeAttr('onclick', 'changeDealStyle(this)');
		},
		"columnDefs": [
			{
				"targets": 8,
				"createdCell": function (td, cellData, rowData, row, col) {
					var str = rowData[8] + ', ' + rowData[12];
					$(td).html(str != ', ' ? str : '');
				}
			},
			{
				"targets": 9,
				"createdCell": function (td, cellData, rowData, row, col) {

					$(td).html(`<a href="javascript:;" onclick="viewNotes(${rowData[11]})">
					<i class="fas fa-sticky-note"></i>
					</a>`);
				}
			},
			{
				"targets": 10,
				"createdCell": function (td, cellData, rowData, row, col) {
					var rowValue = [];
					if (rowData[10] == '0') {
						rowValue.push("green");
						rowValue.push("unlock");
						rowValue.push("lock");
					} else if (rowData[10] == '1') {
						rowValue.push("red");
						rowValue.push("lock");
						rowValue.push("unlock");
					}
					var html = `<a title=${rowValue[2]} href="javascript:;" onclick="lockOrUnlockDeal(${rowData[11]})">`;
					html += `<input type="text" hidden id="${rowData[11]}_permission" value=${rowValue[2]}>`;
					html += `<i class="fas fa-${rowValue[1]}" style="color:${rowValue[0]}"></i></a>`;
					if (permisson != 'no') {
						html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
								<i class="fas fa-trash"></i>
							</a>`;
					}
					// html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
					// 			<i class="fas fa-trash"></i>
					// 		</a>`;
					$(td).html(html);
				}
			}
		]
	});
}

var ViewfundedDealsCustomTable = $(".fundedPendingDealsCustomTable");
if (ViewfundedDealsCustomTable !== null) {
	var permisson = '';
	var CustomerTable = (ViewfundedDealsCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"autoWidth": false,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": {
			"url": "deals/funded?table-list",
			"dataSrc": function (json) {
				//Make your callback here.
				permisson = json.delete_permission;
				return json.data;
			}
		},
		"pageLength": 50,
		order: [[0, 'desc']],
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
			$(nRow).attr('class', 'deal-row');
			$(nRow).attr('data-id', aData[12]);
			$(nRow).attr('data-permission', aData[10]);
			//$(nRow).attr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td').attr('onclick', 'changeDealStyle(' + aData[12] + ')');
			$(nRow).find('td:eq(10)').removeAttr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td:eq(11)').removeAttr('onclick', 'changeDealStyle(this)');
		},
		"columnDefs": [
			{
				"targets": 8,
				"createdCell": function (td, cellData, rowData, row, col) {
					var str = rowData[8] + ', ' + rowData[13];
					$(td).html(str != ', ' ? str : '');
				}
			},
			{
				"targets": 10,
				"createdCell": function (td, cellData, rowData, row, col) {

					$(td).html(`<a href="javascript:;" onclick="viewNotes(${rowData[12]})">
					<i class="fas fa-sticky-note"></i>
					</a>`);
				}
			},
			{
				"targets": 11,
				"createdCell": function (td, cellData, rowData, row, col) {
					var rowValue = [];
					if (rowData[11] == '0') {
						rowValue.push("green");
						rowValue.push("unlock");
						rowValue.push("lock");
					} else if (rowData[11] == '1') {
						rowValue.push("red");
						rowValue.push("lock");
						rowValue.push("unlock");
					}
					var html = `<a title=${rowValue[2]} href="javascript:;" onclick="lockOrUnlockDeal(${rowData[12]})">`;
					html += `<input type="text" hidden id="${rowData[12]}_permission" value=${rowValue[2]}>`;
					html += `<i class="fas fa-${rowValue[1]}" style="color:${rowValue[0]}"></i></a>`;
					if (permisson != 'no') {
						html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[12]})">
								<i class="fas fa-trash"></i>
							</a>`;
					}
					// html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
					// 			<i class="fas fa-trash"></i>
					// 		</a>`;
					$(td).html(html);
				}
			}
		]
	});
}


//<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;' />
var closeDealsCustomTable = $(".closeDealsCustomTable");
if (closeDealsCustomTable !== null) {
	var permisson = '';
	var CustomerTable = (closeDealsCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"autoWidth": false,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": {
			"url": "deals/close?table-list",
			"dataSrc": function (json) {
				//Make your callback here.
				permisson = json.delete_permission;
				return json.data;
			}
		},
		"pageLength": 50,
		order: [[1, 'desc']],
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
			$(nRow).attr('class', 'deal-row');
			$(nRow).attr('data-id', aData[10]);
			//$(nRow).attr('data-permission', aData[9]);
			//$(nRow).attr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td').attr('onclick', 'changeDealStyle(' + aData[10] + ')');
			$(nRow).find('td:eq(9)').removeAttr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td:eq(10)').removeAttr('onclick', 'changeDealStyle(this)');
		},
		"columnDefs": [
			{
				"targets": 0,
				"createdCell": function (td, cellData, rowData, row, col) {
					$(td).html(rowData[0] == 1 ? 'Closed' : 'Opened');
				}
			},
			{
				"targets": 8,
				"createdCell": function (td, cellData, rowData, row, col) {
					var str = rowData[8] + ', ' + rowData[11];
					$(td).html(str != ', ' ? str : '');
				}
			},
			{
				"targets": 9,
				"createdCell": function (td, cellData, rowData, row, col) {

					$(td).html(`<a href="javascript:;" onclick="viewNotes(${rowData[10]})">
					<i class="fas fa-sticky-note"></i>
					</a>`);
				}
			},
			{
				"targets": 10,
				"createdCell": function (td, cellData, rowData, row, col) {
					var rowValue = [];
					if (rowData[9] == '0') {
						rowValue.push("green");
						rowValue.push("unlock");
						rowValue.push("lock");
					} else if (rowData[9] == '1') {
						rowValue.push("red");
						rowValue.push("lock");
						rowValue.push("unlock");
					}
					var html = `<a title=${rowValue[2]} href="javascript:;" onclick="lockOrUnlockDeal(${rowData[10]})">`;
					html += `<input type="text" hidden id="${rowData[10]}_permission" value=${rowValue[2]}>`;
					html += `<i class="fas fa-${rowValue[1]}" style="color:${rowValue[0]}"></i></a>`;
					if (permisson != 'no') {
						html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[10]})">
								<i class="fas fa-trash"></i>
							</a>`;
					}
					// html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
					// 			<i class="fas fa-trash"></i>
					// 		</a>`;
					$(td).html(html);
				}
			}
		]
	});
}
var deleteDealsCustomTable = $(".deleteDealsCustomTable");
if (deleteDealsCustomTable !== null) {
	var permisson = '';
	var CustomerTable = (deleteDealsCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"autoWidth": false,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": {
			"url": "deals/delete?table-list",
			"dataSrc": function (json) {
				//Make your callback here.
				permisson = json.delete_permission;
				return json.data;
			}
		},
		"pageLength": 50,
		order: [[1, 'desc']],
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
			$(nRow).attr('class', 'deal-row');
			$(nRow).attr('data-id', aData[10]);
			//$(nRow).attr('data-permission', aData[9]);
			//$(nRow).attr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td').attr('onclick', 'changeDealStyle(' + aData[10] + ')');
			$(nRow).find('td:eq(9)').removeAttr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td:eq(10)').removeAttr('onclick', 'changeDealStyle(this)');
		},
		"columnDefs": [
			{
				"targets": 0,
				"createdCell": function (td, cellData, rowData, row, col) {
					$(td).html(rowData[0] == 1 ? 'Closed' : 'Opened');
				}
			},
			{
				"targets": 8,
				"createdCell": function (td, cellData, rowData, row, col) {
					var str = rowData[8] + ', ' + rowData[11];
					$(td).html(str != ', ' ? str : '');
				}
			},
			{
				"targets": 9,
				"createdCell": function (td, cellData, rowData, row, col) {

					$(td).html(`<a href="javascript:;" onclick="viewNotes(${rowData[10]})">
					<i class="fas fa-sticky-note"></i>
					</a>`);
				}
			},
			{
				"targets": 10,
				"createdCell": function (td, cellData, rowData, row, col) {
					var rowValue = [];
					if (rowData[9] == '0') {
						rowValue.push("green");
						rowValue.push("unlock");
						rowValue.push("lock");
					} else if (rowData[9] == '1') {
						rowValue.push("red");
						rowValue.push("lock");
						rowValue.push("unlock");
					}
					var html = `<a title=${rowValue[2]} href="javascript:;" onclick="lockOrUnlockDeal(${rowData[10]})">`;
					html += `<input type="text" hidden id="${rowData[10]}_permission" value=${rowValue[2]}>`;
					html += `<i class="fas fa-${rowValue[1]}" style="color:${rowValue[0]}"></i></a>`;
					if (permisson != 'no') {
						html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[10]})">
								<i class="fas fa-trash"></i>
							</a>`;
					}
					// html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
					// 			<i class="fas fa-trash"></i>
					// 		</a>`;
					$(td).html(html);
				}
			}
		]
	});
}


var flooredDealsCustomTable = $(".flooredDealsCustomTable");
if (flooredDealsCustomTable !== null) {
	var permisson = '';
	var CustomerTable = (flooredDealsCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"autoWidth": false,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": {
			"url": "deals/floored?table-list",
			"dataSrc": function (json) {
				//Make your callback here.
				permisson = json.delete_permission;
				return json.data;
			}
		},
		"pageLength": 50,
		order: [[1, 'desc']],
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
			$(nRow).attr('class', 'deal-row');
			$(nRow).attr('data-id', aData[6]);
			$(nRow).attr('data-permission', aData[7]);
			//$(nRow).attr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td').attr('onclick', 'changeDealStyle(' + aData[6] + ')');
			$(nRow).find('td:eq(6)').removeAttr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td:eq(7)').removeAttr('onclick', 'changeDealStyle(this)');
		},
		"columnDefs": [
			{
				"targets": 5,
				"createdCell": function (td, cellData, rowData, row, col) {
					var str = rowData[8] + ', ' + rowData[9];
					$(td).html(str != ', ' ? str : '');
				}
			},
			{
				"targets": 6,
				"createdCell": function (td, cellData, rowData, row, col) {

					$(td).html(`<a href="javascript:;" onclick="viewNotes(${rowData[6]})">
					<i class="fas fa-sticky-note"></i>
					</a>`);
				}
			},
			{
				"targets": 7,
				"createdCell": function (td, cellData, rowData, row, col) {
					var rowValue = [];
					if (rowData[5] == '0') {
						rowValue.push("green");
						rowValue.push("unlock");
						rowValue.push("lock");
					} else if (rowData[5] == '1') {
						rowValue.push("red");
						rowValue.push("lock");
						rowValue.push("unlock");
					}
					var html = `<a title=${rowValue[2]} href="javascript:;" onclick="lockOrUnlockDeal(${rowData[6]})">`;
					html += `<input type="text" hidden id="${rowData[6]}_permission" value=${rowValue[2]}>`;
					html += `<i class="fas fa-${rowValue[1]}" style="color:${rowValue[0]}"></i></a>`;
					if (permisson != 'no') {
						html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[6]})">
								<i class="fas fa-trash"></i>
							</a>`;
					}
					// html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
					// 			<i class="fas fa-trash"></i>
					// 		</a>`;
					$(td).html(html);
				}
			}
		]
	});
}


var approvedDealsCustomTable = $(".approvedDealsCustomTable");
if (approvedDealsCustomTable !== null) {
	var permisson = '';
	var CustomerTable = (approvedDealsCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"autoWidth": false,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": {
			"url": "deals/approved?table-list",
			"dataSrc": function (json) {
				//Make your callback here.
				permisson = json.delete_permission;
				return json.data;
			}
		},
		"pageLength": 50,
		order: [[0, 'desc']],
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
			$(nRow).attr('class', 'deal-row');
			$(nRow).attr('data-id', aData[11]);
			$(nRow).attr('data-permission', aData[9]);
			//$(nRow).attr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td').attr('onclick', 'changeDealStyle(' + aData[11] + ')');
			$(nRow).find('td:eq(9)').removeAttr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td:eq(10)').removeAttr('onclick', 'changeDealStyle(this)');
		},
		"columnDefs": [
			{
				"targets": 8,
				"createdCell": function (td, cellData, rowData, row, col) {
					var str = rowData[8] + ', ' + rowData[12];
					$(td).html(str != ', ' ? str : '');
				}
			},
			{
				"targets": 9,
				"createdCell": function (td, cellData, rowData, row, col) {

					$(td).html(`<a href="javascript:;" onclick="viewNotes(${rowData[11]})">
					<i class="fas fa-sticky-note"></i>
					</a>`);
				}
			},
			{
				"targets": 10,
				"createdCell": function (td, cellData, rowData, row, col) {
					var rowValue = [];
					if (rowData[10] == '0') {
						rowValue.push("green");
						rowValue.push("unlock");
						rowValue.push("lock");
					} else if (rowData[10] == '1') {
						rowValue.push("red");
						rowValue.push("lock");
						rowValue.push("unlock");
					}
					var html = `<a title=${rowValue[2]} href="javascript:;" onclick="lockOrUnlockDeal(${rowData[11]})">`;
					html += `<input type="text" hidden id="${rowData[11]}_permission" value=${rowValue[2]}>`;
					html += `<i class="fas fa-${rowValue[1]}" style="color:${rowValue[0]}"></i></a>`;
					if (permisson != 'no') {
						html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
								<i class="fas fa-trash"></i>
							</a>`;
					}
					// html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
					// 			<i class="fas fa-trash"></i>
					// 		</a>`;
					$(td).html(html);
				}
			}
		]
	});
}

var deferredPaymentsDealsCustomTable = $(".deferredPaymentsDealsCustomTable");
if (deferredPaymentsDealsCustomTable !== null) {
	var permisson = '';
	var CustomerTable = (deferredPaymentsDealsCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"autoWidth": false,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": {
			"url": "deals/deferred?table-list",
			"dataSrc": function (json) {
				//Make your callback here.
				permisson = json.delete_permission;
				return json.data;
			}
		},
		"pageLength": 50,
		order: [[3, 'desc']],
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
			$(nRow).attr('class', 'deal-row');
			$(nRow).attr('data-id', aData[7]);
			$(nRow).attr('data-permission', aData[9]);
			//$(nRow).attr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td').attr('onclick', 'changeDealStyle(' + aData[7] + ')');
			$(nRow).find('td:eq(6)').removeAttr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td:eq(7)').removeAttr('onclick', 'changeDealStyle(this)');
			$(nRow).find('td:eq(8)').removeAttr('onclick', 'changeDealStyle(this)');
		},
		"columnDefs": [
			{
				"targets": 3,
				"createdCell": function (td, cellData, rowData, row, col) {
					const date = new Date(cellData)
					if (isNaN(date)) {
						$(td).html("");
					} else {
						var month = date.getMonth() + 1;
						month = month < 10 ? '0' + month : month
						var day = date.getDate();
						day = day < 10 ? '0' + day : day;
						const year = date.getFullYear();
						const formattedDate = `${month}/${day}/${year}`

						$(td).html(formattedDate);
					}

				}
			},
			{
				"targets": 6,
				"createdCell": function (td, cellData, rowData, row, col) {
					$(td).html(`<a href="/deferred?id=${rowData[7]}">
					Mark Paid
					</a>`);
				}
			},
			{
				"targets": 7,
				"createdCell": function (td, cellData, rowData, row, col) {

					$(td).html(`<a href="javascript:;" onclick="viewNotes(${rowData[7]})">
					<i class="fas fa-sticky-note"></i>
					</a>`);
				}
			},
			{
				"targets": 8,
				"createdCell": function (td, cellData, rowData, row, col) {
					var rowValue = [];
					if (rowData[8] == '0') {
						rowValue.push("green");
						rowValue.push("unlock");
						rowValue.push("lock");
					} else if (rowData[8] == '1') {
						rowValue.push("red");
						rowValue.push("lock");
						rowValue.push("unlock");
					}
					var html = `<a title=${rowValue[2]} href="javascript:;" onclick="lockOrUnlockDeal(${rowData[7]})">`;
					html += `<input type="text" hidden id="${rowData[7]}_permission" value=${rowValue[2]}>`;
					html += `<i class="fas fa-${rowValue[1]}" style="color:${rowValue[0]}"></i></a>`;
					if (permisson != 'no') {
						html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[7]})">
								<i class="fas fa-trash"></i>
							</a>`;
					}
					$(td).html(html);
				}
			}
		]
	});
}


var importedDealsCustomTable = $(".importedDealsCustomTable");
if (importedDealsCustomTable !== null) {
	var permisson = '';
	var CustomerTable = (importedDealsCustomTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"autoWidth": false,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": {
			"url": "deals/imported?table-list",
			"dataSrc": function (json) {
				//Make your callback here.
				permisson = json.delete_permission;
				return json.data;
			}
		},
		"pageLength": 50,
		order: [[7, 'desc']],
		// "fnCreatedRow": function( nRow, aData, iDataIndex ) {
		// 	$(nRow).attr('class', 'deal-row');
		// 	$(nRow).attr('data-id', aData[8]);
		// 	//$(nRow).attr('onclick', 'changeDealStyle(this)');
		// 	$(nRow).find('td').attr('onclick', 'changeDealStyle('+aData[11]+')');
		// 	$(nRow).find('td:eq(9)').removeAttr('onclick', 'changeDealStyle(this)');
		// 	$(nRow).find('td:eq(10)').removeAttr('onclick', 'changeDealStyle(this)');
		// }
		"columnDefs": [
			{
				"targets": 8,
				"orderable": false,
				"createdCell": function (td, cellData, rowData, row, col) {
					$(td).html(`<a href="javascript:;" onclick="convertToDeal(${rowData[8]})">
											<i class="fa fa-exchange-alt" aria-hidden="true"></i>
								</a>`);
				}
			},
			{
				"targets": 9,
				"orderable": false,
				"createdCell": function (td, cellData, rowData, row, col) {
					if (checkImportDataSelected(rowData[9])) {
						$(td).html(`<input type="checkbox" id="convert_selector_${rowData[9]}" onclick="toggleImportDataSelector(${rowData[9]})"
									 checked
								/>`);
					} else {
						$(td).html(`<input type="checkbox" id="convert_selector_${rowData[9]}" onclick="toggleImportDataSelector(${rowData[9]})"
								/>`);
					}

				}
			}
			// {
			// 	"targets": 10,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		var rowValue = [];
			// 		if ( rowData[10] == '0' ) {
			// 			rowValue.push("green");
			// 			rowValue.push("unlock");
			// 			rowValue.push("lock");
			// 		} else if ( rowData[10] == '1' ) {
			// 			rowValue.push("red");
			// 			rowValue.push("lock");
			// 			rowValue.push("unlock");
			// 		} 
			// 		var html = `<a title=${rowValue[2]} href="javascript:;" onclick="lockOrUnlockDeal(${rowData[11]})">`;
			// 		html += `<input type="text" hidden id="${rowData[11]}_permission" value=${rowValue[2]}>`;
			// 		html += `<i class="fas fa-${rowValue[1]}" style="color:${rowValue[0]}"></i></a>`;
			// 		if(permisson != 'no'){
			// 			html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
			// 					<i class="fas fa-trash"></i>
			// 				</a>`;
			// 		}
			// 		// html += `<a title="Delete" href="javascript:;" onclick="deleteDeal(${rowData[11]})">
			// 		// 			<i class="fas fa-trash"></i>
			// 		// 		</a>`;
			// 		$(td).html(html);
			// 	}
			// }
		]
	});
}


var GapTable = $(".GapTable");
if (GapTable !== null) {
	var GapTableCollection = (GapTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "admin/gap?table-list",
		"pageLength": 10,
		"columnDefs": [
			{ "targets": [0], width: '90%' },
			{
				"targets": 1,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[1] == '1') {
						color = 'green';
					} else if (rowData[1] == '0') {
						color = 'red';
					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html(`<i class="fas fa-circle" style="color:${color}"></i>`);
				},
				"width": '5%'
			},
			{
				"targets": 2,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					var icon;
					var textAction;
					if (rowData[1] == '1') {
						color = 'red';
						icon = 'ban';
						textAction = 'Cancel';
					} else if (rowData[1] == '0') {
						color = 'green';
						icon = 'check';
						textAction = 'Approve';
					}
					$(td).css('vertical-align', 'middle');
					var html = `<a title=${textAction} style="color:${color}" href="admin/gap?id=${cellData}&action=toggle" class="new-btn btn">
									<i class='fas fa-${icon}'></i>
								</a>`;
					html += `<a title=${textAction}  href="/admin/gap?id=${rowData[2]}" class="new-btn btn"><i class="fas fa-edit"></i></a>`;
					$(td).html(html);
				}
			}
			// {
			// 	"targets": 3,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		$(td).css('vertical-align', 'middle');
			// 		$(td).html('<a href="/admin/gap?id='+rowData[2]+'" class="new-btn btn"><i class="fas fa-edit"></i></a>');
			// 	}
			// }
		]
	});
	$('#gapDropdownStatus').off('change');
	$('#gapDropdownStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "admin/gap?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		GapTableCollection.search(str).draw();
	})
}

var MiscTable = $(".MiscTable");
if (MiscTable !== null) {
	var CustomerTable = (MiscTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "admin/misc?table-list",
		"pageLength": 10,
		"columnDefs": [
			{ "targets": [0], width: '90%' },
			{
				"targets": 1,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[1] == '1') {
						color = 'green';
					} else if (rowData[1] == '0') {
						color = 'red';
					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html(`<i class="fas fa-circle" style="color:${color}"></i>`);
				},
				"width": '5%'
			},
			{
				"targets": 2,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					var icon;
					var textAction;
					if (rowData[1] == '1') {
						color = 'red';
						icon = 'ban';
						textAction = 'Cancel';
					} else if (rowData[1] == '0') {
						color = 'green';
						icon = 'check';
						textAction = 'Approve';
					}
					$(td).css('vertical-align', 'middle');
					var html = `<a title=${textAction} style="color:${color}" href="admin/misc?id=${cellData}&action=toggle" class="new-btn btn">
									<i class='fas fa-${icon}'></i>
								</a>`;
					html += `<a title=${textAction}  href="/admin/misc?id=${rowData[2]}" class="new-btn btn"><i class="fas fa-edit"></i></a>`;
					$(td).html(html);
				}
			}
			// {
			// 	"targets": 3,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		$(td).css('vertical-align', 'middle');
			// 		$(td).html('<a href="/admin/misc?id='+rowData[2]+'" class="new-btn btn"><i class="fas fa-edit"></i></a>');
			// 	}
			// }
		]
	});
	$('#miscDropdownStatus').off('change');
	$('#miscDropdownStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "admin/misc?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});
}

var FlooringTable = $(".FlooringTable");
if (FlooringTable !== null) {
	var FlooringTableCollection = (FlooringTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "admin/flooring?table-list",
		"pageLength": 10,
		"columnDefs": [
			{ "targets": [0], width: '90%' },
			{
				"targets": 1,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[1] == '1') {
						color = 'green';
					} else if (rowData[1] == '0') {
						color = 'red';
					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html(`<i class="fas fa-circle" style="color:${color}"></i>`);
				},
				"width": '5%'
			},
			{
				"targets": 2,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					var icon;
					var textAction;
					if (rowData[1] == '1') {
						color = 'red';
						icon = 'ban';
						textAction = 'Cancel';
					} else if (rowData[1] == '0') {
						color = 'green';
						icon = 'check';
						textAction = 'Approve';
					}
					$(td).css('vertical-align', 'middle');
					var html = `<a title=${textAction} style="color:${color}" href="admin/flooring?id=${cellData}&action=toggle" class="new-btn btn">
									<i class='fas fa-${icon}'></i>
								</a>`;
					html += `<a title=${textAction}  href="/admin/flooring?id=${rowData[2]}" class="new-btn btn"><i class="fas fa-edit"></i></a>`;
					$(td).html(html);
				}
			}
			// {
			// 	"targets": 3,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		$(td).css('vertical-align', 'middle');
			// 		$(td).html('<a href="/admin/flooring?id='+rowData[2]+'" class="new-btn btn"><i class="fas fa-edit"></i></a>');
			// 	}
			// }
		]
	});
	$('#flooringDropdownStatus').off('change');
	$('#flooringDropdownStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "admin/flooring?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		FlooringTableCollection.search(str).draw();
	})
}

var LendingTable = $(".LendingTable");
if (LendingTable !== null) {
	var LendingTableCollection = (LendingTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "admin/lending?table-list",
		"pageLength": 10,
		"columnDefs": [
			{ "targets": [0], width: '90%' },
			{
				"targets": 1,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[1] == '1') {
						color = 'green';
					} else if (rowData[1] == '0') {
						color = 'red';
					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html(`<i class="fas fa-circle" style="color:${color}"></i>`);
				},
				"width": '5%'
			},
			{
				"targets": 2,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					var icon;
					var textAction;
					if (rowData[1] == '1') {
						color = 'red';
						icon = 'ban';
						textAction = 'Cancel';
					} else if (rowData[1] == '0') {
						color = 'green';
						icon = 'check';
						textAction = 'Approve';
					}
					$(td).css('vertical-align', 'middle');
					var html = `<a title=${textAction} style="color:${color}" href="admin/lending?id=${cellData}&action=toggle" class="new-btn btn">
									<i class='fas fa-${icon}'></i>
								</a>`;
					html += `<a title=${textAction}  href="/admin/lending?id=${rowData[2]}" class="new-btn btn"><i class="fas fa-edit"></i></a>`;
					$(td).html(html);
				}
			}
			// {
			// 	"targets": 3,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		$(td).css('vertical-align', 'middle');
			// 		$(td).html('<a href="/admin/lending?id='+rowData[2]+'" class="new-btn btn"><i class="fas fa-edit"></i></a>');
			// 	}
			// }
		]
	});
	$('#lendingDropdownStatus').off('change');
	$('#lendingDropdownStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "admin/lending?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		LendingTableCollection.search(str).draw();
	})
}

var FinanceTable = $(".FinanceTable");
if (FinanceTable !== null) {
	var FinanceTableCollection = (FinanceTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "admin/finance?table-list",
		"pageLength": 10,
		"columnDefs": [
			{ "targets": [0], width: '90%' },
			{
				"targets": 1,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[1] == '1') {
						color = 'green';
					} else if (rowData[1] == '0') {
						color = 'red';
					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html(`<i class="fas fa-circle" style="color:${color}"></i>`);
				},
				"width": '5%'
			},
			{
				"targets": 2,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					var icon;
					var textAction;
					if (rowData[1] == '1') {
						color = 'red';
						icon = 'ban';
						textAction = 'Cancel';
					} else if (rowData[1] == '0') {
						color = 'green';
						icon = 'check';
						textAction = 'Approve';
					}
					$(td).css('vertical-align', 'middle');
					var html = `<a title=${textAction} style="color:${color}" href="admin/finance?id=${cellData}&action=toggle" class="new-btn btn">
									<i class='fas fa-${icon}'></i>
								</a>`;
					html += `<a title=${textAction}  href="/admin/finance?id=${rowData[2]}" class="new-btn btn"><i class="fas fa-edit"></i></a>`;
					$(td).html(html);
				}
			}
			// {
			// 	"targets": 3,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		$(td).css('vertical-align', 'middle');
			// 		$(td).html('<a href="/admin/finance?id='+rowData[2]+'" class="new-btn btn"><i class="fas fa-edit"></i></a>');
			// 	}
			// }
		]
	});
	$('#financeDropdownStatus').off('change');
	$('#financeDropdownStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "admin/finance?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		FinanceTableCollection.search(str).draw();
	})
}

var DeskTable = $(".DeskTable");
if (DeskTable !== null) {
	var DeskTableCollection = (DeskTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "admin/desk?table-list",
		"pageLength": 10,
		"columnDefs": [
			{ "targets": [0], width: '90%' },
			{
				"targets": 1,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[1] == '1') {
						color = 'green';
					} else if (rowData[1] == '0') {
						color = 'red';
					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html(`<i class="fas fa-circle" style="color:${color}"></i>`);
				},
				"width": '5%'
			},
			{
				"targets": 2,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					var icon;
					var textAction;
					if (rowData[1] == '1') {
						color = 'red';
						icon = 'ban';
						textAction = 'Cancel';
					} else if (rowData[1] == '0') {
						color = 'green';
						icon = 'check';
						textAction = 'Approve';
					}
					$(td).css('vertical-align', 'middle');
					var html = `<a title=${textAction} style="color:${color}" href="admin/desk?id=${cellData}&action=toggle" class="new-btn btn">
									<i class='fas fa-${icon}'></i>
								</a>`;
					html += `<a title=${textAction}  href="/admin/desk?id=${rowData[2]}" class="new-btn btn"><i class="fas fa-edit"></i></a>`;
					$(td).html(html);
				}
			}
			// {
			// 	"targets": 3,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		$(td).css('vertical-align', 'middle');
			// 		$(td).html('<a href="/admin/desk?id='+rowData[2]+'" class="new-btn btn"><i class="fas fa-edit"></i></a>');
			// 	}
			// }
		]
	});
	$('#deskDropdownStatus').off('change');
	$('#deskDropdownStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "admin/desk?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		DeskTableCollection.search(str).draw();
	})
}



var WarrantyTable = $(".WarrantyTable");
if (WarrantyTable !== null) {
	var CustomerTable = (WarrantyTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "admin/warranty?table-list",
		"pageLength": 10,
		"columnDefs": [
			{ "targets": [0], width: '90%' },
			{
				"targets": 1,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[1] == '1') {
						color = 'green';
					} else if (rowData[1] == '0') {
						color = 'red';
					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html(`<i class="fas fa-circle" style="color:${color}"></i>`);
				},
				"width": '5%'
			},
			{
				"targets": 2,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					var icon;
					var textAction;
					if (rowData[1] == '1') {
						color = 'red';
						icon = 'ban';
						textAction = 'Cancel';
					} else if (rowData[1] == '0') {
						color = 'green';
						icon = 'check';
						textAction = 'Approve';
					}
					$(td).css('vertical-align', 'middle');
					var html = `<a title=${textAction} style="color:${color}" href="admin/warranty?id=${cellData}&action=toggle" class="new-btn btn">
									<i class='fas fa-${icon}'></i>
								</a>`;
					html += `<a title=${textAction}  href="/admin/warranty?id=${rowData[2]}" class="new-btn btn"><i class="fas fa-edit"></i></a>`;
					$(td).html(html);
				}
			}
			// {
			// 	"targets": 3,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		$(td).css('vertical-align', 'middle');
			// 		$(td).html('<a href="/admin/warranty?id='+rowData[2]+'" class="new-btn btn"><i class="fas fa-edit"></i></a>');
			// 	}
			// }
		]
	});
	$('#warrantyDropdownStatus').off('change');
	$('#warrantyDropdownStatus').change(function () {
		$.ajax({
			type: "POST",
			url: "admin/warranty?dropdown",
			data: {
				status: this.value,
				user_id: '{{session.user_id}}'
			},
			success: function (data) {
				toastr.success("Filter has been applied successfully", "Success")
				setTimeout(function () {
					location.reload()
				}, 2000)
			},
			error: function (data) {
				toastr.error(data.responseText ? data.responseText : data, "error")
			}
		})
	});

};



var CompanyTable = $(".CompanyTable");
if (CompanyTable !== null) {
	var CompanyTableCollection = (CompanyTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "superuser/customers?table-list",
		"pageLength": 50,
		order: [[0, 'ASC']],
		"columnDefs": [
			{ "targets": [0], width: '90%' },
			{
				"targets": 6,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[6] == '1') {
						color = 'green';
					} else if (rowData[6] == '0') {
						color = 'red';
					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html(`<i class="fas fa-circle" style="color:${color}"></i>`);
				},
				"width": '5%'
			},
			{
				"targets": 5,
				"createdCell": function (td, cellData, rowData, row, col) {
					var date = new Date(rowData[5]);
					if (Object.prototype.toString.call(date) === "[object Date]") {
						// it is a date
						if (isNaN(date)) { // d.getTime() or d.valueOf() will also work
							// date object is not valid
						} else {
							// var month = date.toLocaleString('default', { month: 'long' });
							var month = date.getMonth() + 1;
							month = month < 10 ? '0' + month : month;
							var d = date.getDate();
							d = d < 10 ? '0' + d : d;
							var y = date.getFullYear();
							var t = date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
							$(td).html(month + '/' + d + '/' + y + ' ' + t);
						}
					} else {
						// not a date object
					}
				},
				"width": '5%'
			},
			{
				"targets": 7,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					var icon;
					var textAction;
					if (rowData[6] == '1') {
						color = 'red';
						icon = 'ban';
						textAction = 'Cancel';
					} else if (rowData[6] == '0') {
						color = 'green';
						icon = 'check';
						textAction = 'Approve';
					}
					$(td).css('vertical-align', 'middle');
					// var html = `<a title=${textAction} style="color:${color}" href="admin/desk?id=${cellData}&action=toggle" class="new-btn btn">
					// 				<i class='fas fa-${icon}'></i>
					// 			</a>`;
					html = `<a title=${textAction}  href="/superuser/customers?id=${rowData[7]}" class="new-btn btn"><i class="fas fa-edit"></i></a>`;
					$(td).html(html);
				}
			}
			// {
			// 	"targets": 3,
			// 	"createdCell": function (td, cellData, rowData, row, col) {
			// 		$(td).css('vertical-align', 'middle');
			// 		$(td).html('<a href="/admin/desk?id='+rowData[2]+'" class="new-btn btn"><i class="fas fa-edit"></i></a>');
			// 	}
			// }
		]
	});
	// $('#deskDropdownStatus').off('change');
	// $('#deskDropdownStatus').change(function(){
	// 	$.ajax({
	// 		type:"POST",
	// 		url: "admin/desk?dropdown",
	// 		data: {
	// 			status: this.value,
	// 			user_id: '{{session.user_id}}'
	// 		},
	// 		success:function(data){
	// 			toastr.success("Filter has been applied successfully", "Success")
	// 			setTimeout(function(){
	// 				location.reload()
	// 			}, 2000)
	// 		},
	// 		error:function(data){
	// 			toastr.error(data.responseText ? data.responseText : data,"error")
	// 		}
	// 	})
	// });
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		CompanyTableCollection.search(str).draw();
	})
}


var CustomersBillTable = $(".CustomersBillTable");
if (CustomersBillTable !== null) {
	var CustomersBillTableCollection = (CustomersBillTable).DataTable({
		"dom": "rtip",
		"processing": true,
		"language": {
			"loadingRecords": "&nbsp;",
			"processing": "<img src='_templates/default/components/template/resources/images/loading.gif' style='width:50px;position: relative;top: 50%;transform: translateY(-50%);' />"
		},
		"serverSide": true,
		"ajax": "superuser/customers_bill?table-list",
		"pageLength": 50,
		order: [[0, 'ASC']],
		"columnDefs": [
			{
				"targets": 4,
				"createdCell": function (td, cellData, rowData, row, col) {
					var color;
					if (rowData[4] == '1') {
						color = 'Active';
					} else if (rowData[4] == '0') {
						color = 'Inactive';
					}
					$(td).css('vertical-align', 'middle');
					$(td).css('text-align', 'center');
					$(td).html(color);
				},
			}, {
				"targets": 6,
				"createdCell": function (td, cellData, rowData, row, col) {
					var date = new Date(rowData[6]);
					if (Object.prototype.toString.call(date) === "[object Date]") {
						// it is a date
						if (isNaN(date)) { // d.getTime() or d.valueOf() will also work
							// date object is not valid
						} else {
							// var month = date.toLocaleString('default', { month: 'long' });
							var month = date.getMonth() + 1;
							month = month < 10 ? '0' + month : month;
							var d = date.getDate();
							d = d < 10 ? '0' + d : d;
							var y = date.getFullYear();
							var t = date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
							$(td).html(month + '/' + d + '/' + y + ' ' + t);
						}
					} else {
						// not a date object
					}

				},
			},
		]
	});
	// $('#deskDropdownStatus').off('change');
	// $('#deskDropdownStatus').change(function(){
	// 	$.ajax({
	// 		type:"POST",
	// 		url: "admin/desk?dropdown",
	// 		data: {
	// 			status: this.value,
	// 			user_id: '{{session.user_id}}'
	// 		},
	// 		success:function(data){
	// 			toastr.success("Filter has been applied successfully", "Success")
	// 			setTimeout(function(){
	// 				location.reload()
	// 			}, 2000)
	// 		},
	// 		error:function(data){
	// 			toastr.error(data.responseText ? data.responseText : data,"error")
	// 		}
	// 	})
	// });
	$('#myInputTextField').keyup(function () {
		var str = $.trim($(this).val());
		if (str == '') {
			$(this).val('');
		}
		CustomersBillTableCollection.search(str).draw();
	})
}




function convertToDeal(import_id) {
	if (confirm("Do you want to convert it?")) {
		$.ajax({
			url: '',
			method: 'get',
			dataType: 'json',
			data: {
				import_id: import_id
			},
			success: function (json) {
				try {
					toastr[json.type](json.message, 'Imported Deals');
					let id = json.data;
					setTimeout(() => window.location.href = "/deals/view?id=" + id, 3000)
				} catch (e) {
					toastr["error"](json.message, 'Error not found, contact technical support!');
				}
			}
		});

	}
}


var selectedImportData = []
const toggleImportDataSelector = (import_id) => {
	const checkbox = $(`#convert_selector_${import_id}`)
	if (checkbox.prop('checked')) {
		selectedImportData.push(import_id)
	} else {
		selectedImportData = selectedImportData.filter(item => item !== import_id)
	}

	$('#import_all_btn').prop('disabled', selectedImportData.length === 0)

}

function checkImportDataSelected(import_id) {
	return selectedImportData.includes(parseInt(import_id));
}



$("#import_all_btn").on("click", function () {
	if (selectedImportData.length > 0) {
		console.log('test')
		$("#confirm_import_message").html(`Do you want to convert?(1/${selectedImportData.length})`)
		$("#confirm_import_form").prop("open", true);
	}
})

$("#confirm_ok").on("click", function () {
	const import_id = selectedImportData[0];
	convertOneDeal(import_id, (id) => {
		if (selectedImportData.length > 1) {
			selectedImportData = selectedImportData.slice(1);
			$("#confirm_import_message").html(`Do you want to convert?(1/${selectedImportData.length}`)
		} else {
			$("#confirm_import_form").prop("open", false);
			setTimeout(() => window.location.href = "/deals/view?id=" + id, 3000)
		}
	})
})

$("#confirm_ok_all").on("click", function () {
	import_id = selectedImportData[0]
	const cb = (id) => {
		if (selectedImportData.length > 1) {
			selectedImportData = selectedImportData.slice(1)
			const import_id = selectedImportData[0]
			convertOneDeal(import_id, cb)
		} else {
			$("#confirm_import_form").prop("open", false);
			setTimeout(() => window.location.href = "/deals/view?id=" + id, 3000)
		}
	}
	convertOneDeal(import_id, cb)
})

const convertOneDeal = (import_id, cb) => {
	$.ajax({
		url: '',
		method: 'get',
		dataType: 'json',
		data: {
			import_id: import_id
		},
		success: function (json) {
			try {
				toastr[json.type](json.message, 'Imported Deals');
				let id = json.data;
				cb(id)
			} catch (e) {
				toastr["error"](json.message, 'Error not found, contact technical support!');
			}
		}
	});
}

$("#confirm_cancel").on("click", function () {
	$("#confirm_import_form").prop("open", false);
})


