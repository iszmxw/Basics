<link rel="stylesheet" href="{{asset('admin_style/assets/library/jstree/themes/default/style.min.css')}}"/>
<style>
    #RoleFrom {
        margin-top: 10px;
    }
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="title">编辑角色</h4>
        </div>
        <div class="modal-body">
            <form id="RoleFrom">
                <input type="hidden" id="_token" value="{{csrf_token()}}">
                <input type="hidden" id="role_id" value="{{$role['id']}}">
                <input type="hidden" id="treeData" value="{{route('admin.system.tree_data')}}">
                <input type="hidden" id="EditRole" value="{{route('admin.system.role_edit')}}">
                <div class="form-group">
                    <label>角色名称</label>
                    <input type="text" id="name" name="name" value="{{$role['name']}}" class="form-control"
                           placeholder="角色名称"/>
                </div>
                <div class="form-group">
                    <label>角色描述</label>
                    <textarea type="text" id="desc" name="desc" class="form-control"
                              placeholder="角色描述">{{$role['desc']}}</textarea>
                </div>
                <div class="form-group">
                    <label>
                        <span class="pull-left">
                            <div class="row">
                                <div class="col-sm-12">权限节点</div>
                            </div>
                        </span>
                        <span class="pull-left">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <span class="pull-left">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>
                                        <input name="" type="checkbox" id="checkAllPms" class="colored-success">
                                        <span class="text">选中全部</span>
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <label>
                                        <input name="" type="checkbox" id="expandAllPms" class="colored-success">
                                        <span class="text">展开所有</span>
                                    </label>
                                </div>
                            </div>
                        </span>
                    </label>
                    <div id="RouteTree"></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
            <button type="button" class="btn btn-blue" id="EditData">编辑保存</button>
        </div>
    </div>
</div>
<!--Page Related Scripts-->
<script src="{{asset('admin_style/assets/library/jstree/jstree.min.js?v=').time()}}"></script>
<script>
    $(function () {
        let Document = $('#RouteTree');
        let _token = $("#_token").val();
        let role_id = $("#role_id").val();
        let treeData = [];
        let selectedData = [];
        let selectedParent = [];
        let url = $("#treeData").val();
        let eStat = 1;
        let cStat = 0;
        let data = {_token: _token, role_id: role_id};
        $.post(url, data, function (res) {
            if (res.code === 200) {
                treeData = res.data;
                Document.jstree({
                    "plugins": ["checkbox", "types"],
                    "checkbox": {
                        "keep_selected_style": false,
                    },
                    "types": {
                        "default": {
                            "icon": false  // 关闭默认图标
                        }
                    },
                    "core": {
                        "data": treeData
                    }
                });
                $('#expandAllPms').prop("checked", true);
            } else {
                Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
            }
        });

        // 监听选中的权限
        Document.on('changed.jstree', function (e, data) {
            selectedParent = Document.jstree().get_undetermined();
            selectedData = data.selected.concat(selectedParent);
        });


        // 展开所有按钮点击事件
        $('#expandAllPms').click(function (e) {
            if (!eStat) {
                Document.jstree().open_all();
                eStat = 1;
            } else {
                Document.jstree().close_all();
                eStat = 0;
            }
            e.stopPropagation();
        });

        // 选中所有权限
        $('#checkAllPms').click(function (e) {
            if (!cStat) {
                Document.jstree().select_all();
                cStat = 1;
            } else {
                Document.jstree().deselect_all();
                cStat = 0;
            }
        });

        // 编辑角色
        $("#EditData").click(function () {
            let url = $("#EditRole").val();
            let name = $("#name").val();
            let desc = $("#desc").val();
            let data = {_token: _token, role_id: role_id, name: name, desc: desc, routes: selectedData};
            $.post(url, data, function (res) {
                if (res.code === 200) {
                    Notify(res.message, 'top-right', '5000', 'success', 'fa-check', true);
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000)
                } else {
                    Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
                }
            })
        });

    });
</script>