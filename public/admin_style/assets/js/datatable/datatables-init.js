var InitiateSimpleDataTable = function () {
    return {
        init: function () {
            //Datatable Initiating
            var oTable = $('#simpledatatable').dataTable({
                "sDom": "Tflt<'row DTTTFooter'<'col-sm-6'i><'col-sm-6'p>>",
                "iDisplayLength": 5,
                "oTableTools": {
                    "aButtons": [
                        "copy", "csv", "xls", "pdf", "print"
                    ],
                    "sSwfPath": "assets/js/datatable/assets/swf/copy_csv_xls_pdf.swf"
                },
                "language": {
                    "search": "",
                    "sLengthMenu": "_MENU_",
                    "oPaginate": {
                        "sPrevious": "Prev",
                        "sNext": "Next"
                    }
                },
                "aoColumns": [
                  { "bSortable": false },
                  null,
                  { "bSortable": false },
                  null,
                  { "bSortable": false }
                ],
                "aaSorting": []
            });

            //Check All Functionality
            jQuery('#simpledatatable .group-checkable').change(function () {
                var set = $(".checkboxes");
                var checked = jQuery(this).is(":checked");
                jQuery(set).each(function () {
                    if (checked) {
                        $(this).prop("checked", true);
                        $(this).parents('tr').addClass("active");
                    } else {
                        $(this).prop("checked", false);
                        $(this).parents('tr').removeClass("active");
                    }
                });

            });
            jQuery('#simpledatatable tbody tr .checkboxes').change(function () {
                $(this).parents('tr').toggleClass("active");
            });

        }

    };

}();
var InitiateEditableDataTable = function () {
    return {
        init: function () {
            //Datatable Initiating
            var oTable = $('#editabledatatable').dataTable({
                "aLengthMenu": [
                    [5, 15, 20, 100, -1],
                    [5, 15, 20, 100, "All"]
                ],
                "iDisplayLength": 5,
                "sPaginationType": "bootstrap",
                "sDom": "Tflt<'row DTTTFooter'<'col-sm-6'i><'col-sm-6'p>>",
                "oTableTools": {
                    "aButtons": [
				        "copy",
				        "print",
				        {
				            "sExtends": "collection",
				            "sButtonText": "Save <i class=\"fa fa-angle-down\"></i>",
				            "aButtons": ["csv", "xls", "pdf"]
				        }],
                    "sSwfPath": "./assets/js/datatable/assets/swf/copy_csv_xls_pdf.swf"
                },
                "language": {
                    "search": "",
                    "sLengthMenu": "_MENU_",
                    "oPaginate": {
                        "sPrevious": "Prev",
                        "sNext": "Next"
                    }
                },
                "aoColumns": [
                  null,
                  null,
                  null,
                  null,
                  { "bSortable": false }
                ]
            });

            var isEditing = null;

            //Add New Row
            $('#editabledatatable_new').click(function (e) {
                e.preventDefault();
                var aiNew = oTable.fnAddData(['', '', '', '',
                        '<a href="#" class="btn btn-success btn-xs save"><i class="fa fa-edit"></i> Save</a> <a href="#" class="btn btn-warning btn-xs cancel"><i class="fa fa-times"></i> Cancel</a>'
                ]);
                var nRow = oTable.fnGetNodes(aiNew[0]);
                editRow(oTable, nRow);
                isEditing = nRow;
            });

            //Delete an Existing Row
            $('#editabledatatable').on("click", 'a.delete', function (e) {
                e.preventDefault();

                if (confirm("Are You Sure To Delete This Row?") == false) {
                    return;
                }

                var nRow = $(this).parents('tr')[0];
                oTable.fnDeleteRow(nRow);
                alert("Row Has Been Deleted!");
            });

            //Cancel Editing or Adding a Row
            $('#editabledatatable').on("click", 'a.cancel', function (e) {
                e.preventDefault();
                if ($(this).attr("data-mode") == "new") {
                    var nRow = $(this).parents('tr')[0];
                    oTable.fnDeleteRow(nRow);
                } else {
                    restoreRow(oTable, isEditing);
                    isEditing = null;
                }
            });

            //Edit A Row
            $('#editabledatatable').on("click", 'a.edit', function (e) {
                e.preventDefault();

                var nRow = $(this).parents('tr')[0];

                if (isEditing !== null && isEditing != nRow) {
                    restoreRow(oTable, isEditing);
                    editRow(oTable, nRow);
                    isEditing = nRow;
                } else {
                    editRow(oTable, nRow);
                    isEditing = nRow;
                }
            });

            //Save an Editing Row
            $('#editabledatatable').on("click", 'a.save', function (e) {
                e.preventDefault();
                if (this.innerHTML.indexOf("Save") >= 0) {
                    saveRow(oTable, isEditing);
                    isEditing = null;
                    //Some Code to Highlight Updated Row
                }
            });


            function restoreRow(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);

                for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                    oTable.fnUpdate(aData[i], nRow, i, false);
                }

                oTable.fnDraw();
            }

            function editRow(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);
                jqTds[0].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[0] + '">';
                jqTds[1].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[1] + '">';
                jqTds[2].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[2] + '">';
                jqTds[3].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[3] + '">';
                jqTds[4].innerHTML = '<a href="#" class="btn btn-success btn-xs save"><i class="fa fa-save"></i> Save</a> <a href="#" class="btn btn-warning btn-xs cancel"><i class="fa fa-times"></i> Cancel</a>';
            }

            function saveRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate(jqInputs[2].value, nRow, 2, false);
                oTable.fnUpdate(jqInputs[3].value, nRow, 3, false);
                oTable.fnUpdate('<a href="#" class="btn btn-info btn-xs edit"><i class="fa fa-edit"></i> Edit</a> <a href="#" class="btn btn-danger btn-xs delete"><i class="fa fa-trash-o"></i> Delete</a>', nRow, 4, false);
                oTable.fnDraw();
            }

            function cancelEditRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate(jqInputs[2].value, nRow, 2, false);
                oTable.fnUpdate(jqInputs[3].value, nRow, 3, false);
                oTable.fnUpdate('<a href="#" class="btn btn-info btn-xs edit"><i class="fa fa-edit"></i> Edit</a> <a href="#" class="btn btn-danger btn-xs delete"><i class="fa fa-trash-o"></i> Delete</a>', nRow, 4, false);
                oTable.fnDraw();
            }
        }

    };
}();
var InitiateExpandableDataTable = function () {
    return {
        init: function () {
            /* Formatting function for row details */
            function fnFormatDetails(oTable, nTr) {
                var aData = oTable.fnGetData(nTr);
                var sOut = '<table>';
                sOut += '<tr><td rowspan="5" style="padding:0 10px 0 0;"><img src="assets/img/avatars/' + aData[6] + '"/></td><td>Name:</td><td>' + aData[1] + '</td></tr>';
                sOut += '<tr><td>Family:</td><td>' + aData[2] + '</td></tr>';
                sOut += '<tr><td>Age:</td><td>' + aData[3] + '</td></tr>';
                sOut += '<tr><td>Positon:</td><td>' + aData[4] + '</td></tr>';
                sOut += '<tr><td>Others:</td><td><a href="">Personal WebSite</a></td></tr>';
                sOut += '</table>';
                return sOut;
            }

            /*
             * Insert a 'details' column to the table
             */
            var nCloneTh = document.createElement('th');
            var nCloneTd = document.createElement('td');
            nCloneTd.innerHTML = '<i class="fa fa-plus-square-o row-details"></i>';

            $('#expandabledatatable thead tr').each(function () {
                this.insertBefore(nCloneTh, this.childNodes[0]);
            });

            $('#expandabledatatable tbody tr').each(function () {
                this.insertBefore(nCloneTd.cloneNode(true), this.childNodes[0]);
            });

            /*
             * Initialize DataTables, with no sorting on the 'details' column
             */
            var oTable = $('#expandabledatatable').dataTable({
                "sDom": "Tflt<'row DTTTFooter'<'col-sm-6'i><'col-sm-6'p>>",
                "aoColumnDefs": [
                    { "bSortable": false, "aTargets": [0] },
                    { "bVisible": false, "aTargets": [6] }
                ],
                "aaSorting": [[1, 'asc']],
                "aLengthMenu": [
                   [5, 15, 20, -1],
                   [5, 15, 20, "All"]
                ],
                "iDisplayLength": 5,
                "oTableTools": {
                    "aButtons": [
				        "copy",
				        "print",
				        {
				            "sExtends": "collection",
				            "sButtonText": "Save <i class=\"fa fa-angle-down\"></i>",
				            "aButtons": ["csv", "xls", "pdf"]
				        }],
                    "sSwfPath": "assets/js/datatable/assets/swf/copy_csv_xls_pdf.swf"
                },
                "language": {
                    "search": "",
                    "sLengthMenu": "_MENU_",
                    "oPaginate": {
                        "sPrevious": "Prev",
                        "sNext": "Next"
                    }
                }
            });


            $('#expandabledatatable').on('click', ' tbody td .row-details', function () {
                var nTr = $(this).parents('tr')[0];
                if (oTable.fnIsOpen(nTr)) {
                    /* This row is already open - close it */
                    $(this).addClass("fa-plus-square-o").removeClass("fa-minus-square-o");
                    oTable.fnClose(nTr);
                }
                else {
                    /* Open this row */
                    $(this).addClass("fa-minus-square-o").removeClass("fa-plus-square-o");;
                    oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details');
                }
            });

            $('#expandabledatatable_column_toggler input[type="checkbox"]').change(function () {
                var iCol = parseInt($(this).attr("data-column"));
                var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
                oTable.fnSetColumnVis(iCol, (bVis ? false : true));
            });

            $('body').on('click', '.dropdown-menu.hold-on-click', function (e) {
                e.stopPropagation();
            })
        }
    };
}();
var InitiateSearchableDataTable = function () {
    return {
        init: function () {
            var oTable = $('#searchable').dataTable({
                "sDom": "Tflt<'row DTTTFooter'<'col-sm-6'i><'col-sm-6'p>>",
                "aaSorting": [[1, 'asc']],
                "aLengthMenu": [
                   [5, 15, 20, -1],
                   [5, 15, 20, "All"]
                ],
                "iDisplayLength": 10,
                "oTableTools": {
                    "aButtons": [
				        "copy",
				        "print",
				        {
				            "sExtends": "collection",
				            "sButtonText": "Save <i class=\"fa fa-angle-down\"></i>",
				            "aButtons": ["csv", "xls", "pdf"]
				        }],
                    "sSwfPath": "assets/js/datatable/assets/swf/copy_csv_xls_pdf.swf"
                },
                "language": {
                    "search": "",
                    "sLengthMenu": "_MENU_",
                    "oPaginate": {
                        "sPrevious": "Prev",
                        "sNext": "Next"
                    }
                }
            });

            $("tfoot input").keyup(function () {
                /* Filter on the column (the index) of this element */
                oTable.fnFilter(this.value, $("tfoot input").index(this));
            });

        }
    }
}();
var InitiateJsonPDataTable = function () {
    return {
        init: function(){
            var oTable = $('#jsonpdatatable').DataTable({
                "autoWidth": false,
                "dom": "Tflrt<'row DTTTFooter'<'col-sm-12'p<'pull-right margin-right-10'i>>>",
                tableTools: {
                    "aButtons": [
                        {
                            "sExtends": "copy",
                            "sButtonText": "复制到剪贴板"
                        },
                        {
                            "sExtends": "print",
                            "sButtonText": "打印预览"
                        },
                        {
                            "sExtends": "collection",
                            "sButtonText": "保存为 <i class=\"fa fa-angle-down\"></i>",
                            "aButtons": ["csv", "xls", "pdf"]
                        }],
                    "sSwfPath": "assets/js/datatable/assets/swf/copy_csv_xls_pdf.swf"
                },
                "language": {
                    "url": "assets/js/datatable/assets/i18n/Chinese.json"
                },
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "http://datatables.net/examples/server_side/scripts/jsonp.php",
                    "dataType": "jsonp"
                },
                //当处理大数据时，延迟渲染数据，有效提高Datatables处理能力
                "deferRender": true,
                columns: [
                    {"data": null,"orderable": false},
                    {"data": 0},
                    {"data": 1},
                    {"data": 2},
                    {"data": 3},
                    {"data": 4},
                    {"data": 5},
                    {"data": null,"orderable": false}
                ],
                "columnDefs": [
                    {
                        "targets": 0,
                        "defaultContent": ' <div class="checkbox"> <label> <input class="colored-blue" name="checkList" type="checkbox"> <span class="text"></span> </label> </div>'
                    },{
                        "targets": -1,
                        "defaultContent": ' <a href="#" class="btn btn-info btn-xs edit"><i class="fa fa-edit"></i> 编辑</a> <a href="#" class="btn btn-danger btn-xs delete"><i class="fa fa-trash-o"></i> 删除</a>'
                    }
                ],
                "order": [[1, 'asc']] // 指定第二列为升序
            });

            // 新增行
            function addNewRow(){
                bootbox.dialog({
                    message: $("#myModal").html(),
                    title: "新增用户",
                    className: "",
                    buttons: {
                        success: {
                            label: "Send",
                            className: "btn-blue",
                            callback: function () { }
                        },
                        "Save as Draft": {
                            className: "btn-danger",
                            callback: function () { }
                        }
                    }
                });
            }

            // 删除行
            $('#jsonpdatatable').on("click", 'a.delete', function (e) {
                e.preventDefault();
                bootbox.confirm("确定删除这条信息吗?", function (result) {
                    if (result) {
                        var nRow = $(this).parents('tr')[0];
                        //  oTable.fnDeleteRow(nRow);

                        oTable.rows().eq(nRow).remove().draw( false );
                        alert("Row Has Been Deleted!");
                    } else {
                        return false;
                    }
                });
            });

            // 编辑行
            function editRow(){
                bootbox.dialog({
                    message: $("#myModal").html(),
                    title: "新增用户",
                    className: "",
                    buttons: {
                        success: {
                            label: "确定",
                            className: "btn-blue",
                            callback: function () { }
                        },
                        "取消": {
                            className: "btn-default",
                            callback: function () { }
                        }
                    }
                });
            }
            $('#jsonpdatatable').on("click", 'a.edit', function (e) {
                e.preventDefault();

                var nRow = $(this).parents('tr')[0];

                editRow();
            });


            oTable.on('draw', function () {
                //checkbox全选
                var $checkAll = $('[data-toggle="checkAll"]');
                var $checkList = $("input[name='checkList']");
                checkAll($checkAll,$checkList);

                $('#J_tableFootBar').css('display','block');
            });

            // checkbox全选函数
            function checkAll($checkAll,$checkList){
                $checkAll.prop("checked", false);
                $checkAll.on("change", function () {
                    if ($(this).prop("checked") === true) {
                        $checkAll.prop("checked", true);
                        $checkList.prop("checked", $(this).prop("checked"));
                    } else {
                        $checkAll.prop("checked", false);
                        $checkList.prop("checked", false);
                    }
                });
                $checkList.on("change",function(){
                    var $checkedList = $checkList.filter(':checked');
                    var checkboxLen = $checkList.length;
                    var checkedLen = $checkedList.length;
                    $checkAll.prop("checked", checkboxLen == checkedLen);
                });
            }
        }
    }
}();

