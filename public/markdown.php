<?php
header("Content-Type: text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
/**
 * 用于从MySQL数据库元数据生成markdown（特定于Github）的脚本
 *
 * 如何使用：
 * 将此文件放在服务器上，然后通过Web浏览器访问它。
 * 输入数据库信息，然后开始运行
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    global $markdown, $markdown_html, $config;

    $config = $_POST;

    markdown_append('## 表', true);

    try {

        $DB = new PDO(sprintf("mysql:host=%s;dbname=%s", $config['host'], $config['name']), $config['user'], $config['password']);

        $table = $config['name'];
        $query = $DB->prepare('SELECT table_name, table_comment FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = :table');
        $query->bindParam(':table', $table, PDO::PARAM_STR);
        $query->execute();

        $tables = $query->fetchAll(PDO::FETCH_ASSOC);

        markdown_format_table_list_markdown($tables);
        markdown_format_table_list_html($tables);

        $reverse_null = $config['reverse_null'][0];

        if ($reverse_null) {
            $config['columns'][] = "Required";
        }

        foreach ($tables as $table) {
            $table = array_values($table)[0];

            markdown_format_table_heading_markdown($table);
            markdown_format_table_heading_html($table);

            $info_sql = 'SHOW FULL COLUMNS FROM ' . $table;

            $query = $DB->prepare($info_sql);
            $query->execute();

            $results = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as &$result) {
                if ($reverse_null) {
                    $result = replace_key_function($result, "Null", "Required");
                }
                foreach ($result as $column_name => $column) {
                    if (!in_array($column_name, $config['columns'])) {
                        unset($result[$column_name]);
                    }
                }
            }

            markdown_format_table_markdown($results);
            markdown_format_table_html($results);
        }
        $now = '文档生成于：' . (new DateTime('now'))->format('Y-m-d H:i:s');
        markdown_append($now);
        markdown_append_html($now);

    } catch (Exception $e) {
        print_r($e);
    }

    markdown_result_template($markdown);

} else {
    markdown_form_template();
}

/**
 * @param $array
 * @param $key1
 * @param $key2
 * @return array|false
 */
function replace_key_function($array, $key1, $key2)
{
    $keys = array_keys($array);
    $index = array_search($key1, $keys);

    if ($index !== false) {
        $keys[$index] = $key2;
        $array = array_combine($keys, $array);
    }

    return $array;
}


/**
 * @param $string
 * @param bool $newline
 */
function markdown_append($string, $newline = false)
{
    global $markdown;
    $markdown .= $string;
    if ($newline) {
        $markdown .= PHP_EOL;
    }
}

/**
 * @param string $string
 * @param bool $newline
 */
function markdown_append_html($string, $newline = false)
{
    global $markdown_html;
    $markdown_html .= $string;
    if ($newline) {
        $markdown_html .= PHP_EOL;
    }
}

/**
 * @param array $tables List of table names
 */
function markdown_format_table_list_markdown($tables)
{
    markdown_append('|表名|说明', true);
    markdown_append('|---|---|', true);

    foreach ($tables as $table) {

        markdown_append('|' . sprintf('[%s](#%s)', $table['table_name'], $table['table_name']) . '|');
        markdown_append($table['table_comment'] . '|', true);
    }
}

/**
 * @param string $table Table name
 */
function markdown_format_table_heading_markdown($table)
{
    markdown_append('', true);
    markdown_append('# ' . $table, true);
    markdown_append('', true);
}

/**
 * @param array $data Results from SHOW FULL COLUMNS
 */
function markdown_format_table_markdown($data)
{
    global $config;
    // markdown 的字段标题
    $title = array_keys($data[0]);
    //实例二：字符串替换数组键值
    $title = str_replace("Field", "字段", $title, $i);
    $title = str_replace("Type", "数据类型", $title, $i);
    $title = str_replace("Collation", "字符集", $title, $i);
    $title = str_replace("Null", "允许空值", $title, $i);
    $title = str_replace("Key", "主键", $title, $i);
    $title = str_replace("Default", "默认值", $title, $i);
    $title = str_replace("Extra", "额外（是否自动递增）", $title, $i);
    $title = str_replace("Privileges", "权限", $title, $i);
    $title = str_replace("Comment", "说明", $title, $i);
    markdown_append('|' . implode('|', $title) . '|', true);
    markdown_append('|');

    for ($i = 0; $i < count($data[0]); $i++) {
        markdown_append('---|');
    }

    markdown_append('', true);

    foreach ($data as $result) {
        markdown_append('|');
        foreach ($result as $column_name => $column_data) {
            if ($config['bold_field'][0] == 1 && $column_name == 'Field') {
                $column_data = '**' . $column_data . '**';
            }
            if ($config['reverse_null'][0] == 1 && $column_name == "Required") {
                $column_data = $column_data == "N" ? "Y" : "N";
            }
            markdown_append($column_data . '|');
        }
        markdown_append('', true);
    }
}

/**
 * @param array $tables List of table names
 */
function markdown_format_table_list_html($tables)
{
    markdown_append_html('<table>', true);
    markdown_append_html('<thead>', true);
    markdown_append_html('<tr><th>Table Name</th><th>Table Comment</th></tr>', true);
    markdown_append_html('</thead>', true);

    markdown_append_html('<tbody>', true);
    foreach ($tables as $table) {
        markdown_append_html('<tr>', true);
        markdown_append_html('<td>' . sprintf('<a href="#%s">%s</a>', $table['table_name'], $table['table_name']) . '</td>', true);
        markdown_append_html('<td>' . $table['table_comment'] . '</td>', true);
        markdown_append_html('</tr>', true);
    }
    markdown_append_html('</tbody>', true);
    markdown_append_html('</table>', true);
}

/**
 * @param string $table Table name
 */
function markdown_format_table_heading_html($table)
{
    markdown_append_html(sprintf('<h1 id="%s">%s</h1>', $table, $table), true);
}

/**
 * @param array $data Results from SHOW FULL COLUMNS
 */
function markdown_format_table_html($data)
{

    global $config;

    $comments = array();

    if ($config['comment_row'][0] == 1) {
        foreach ($data as $key => &$result) {
            if (array_key_exists('Comment', $result)) {
                $comment = $result['Comment'];
                $comments[$key] = $comment;
                unset($result['Comment']);
            }
        }
    }

    markdown_append_html('<table>', true);
    markdown_append_html('<thead>', true);
    markdown_append_html('<tr><th>' . implode('</th><th>', array_keys($data[0])) . '</th></tr>', true);
    markdown_append_html('</thead>', true);

    markdown_append_html('<tbody>', true);

    foreach ($data as $key => $result) {
        markdown_append_html('<tr>', true);

        foreach ($result as $column_key => $column_data) {
            if ($config['bold_field'][0] == 1 && $column_key == 'Field') {
                $column_data = '<b>' . $column_data . '</b>';
            }
            markdown_append_html('<td>' . $column_data . '</td>', true);
        }

        markdown_append_html('</tr>', true);

        if ($config['comment_row'][0] == 1 && !empty($comments[$key])) {
            markdown_append_html('<tr><td colspan="' . count($result) . '">' . $comments[$key] . '</td></tr>', true);
        }
    }

    markdown_append_html('</tbody>', true);
    markdown_append_html('</table>', true);
}

/**
 * @param string $markdown
 */
function markdown_result_template($markdown) {
global $markdown_html, $config;
?><!DOCTYPE html>
<html>
<head>
    <title>MySQL Markdown</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tabs a').click(function (e) {
                e.preventDefault()
                $(this).tab('show')
            });

            $.ajax({
                type: "POST",
                dataType: "html",
                processData: false,
                url: "https://api.github.com/markdown/raw",
                data: $("textarea").val(),
                contentType: "text/plain",
                success: function (data) {
                    if (!data) {
                        $("#preview").html("<h4>No preview available</h4>");
                    }
                    $("#preview").html(data);
                    $("#preview table").addClass('table');
                },
                error: function (jqXHR, textStatus, error) {
                    $("#preview").html("<h4>No preview available</h4>");
                    console.log(jqXHR, textStatus, error);
                }
            });

            $("#html_preview table").addClass("table");
        });
    </script>
</head>
<body>
<div class="container" style="margin-top:40px">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">MySQL转换Markdown文档</h2>
                </div>
                <div class="panel-body">

                    <a href="markdown.php" class="btn btn-default">返回</a>
                    <a href="#" onclick="location.reload(true)" class="btn btn-default">重新生成</a>
                    <?php if ($config['comment_row']) : ?>
                        <br><br>
                        <p class="alert alert-warning"><b>注意:</b> 在单独的行上的注释将仅出现在HTML版本中。 Markdown版本将正常包含注释列。</p>
                    <?php endif; ?>
                    <h3>Markdown 结果</h3>
                    <div id="tabs" role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#preview" role="tab"
                                                                      data-toggle="tab">预览</a>
                            </li>
                            <li role="presentation"><a href="#markdown" role="tab" data-toggle="tab">Markdown</a></li>
                            <li role="presentation"><a href="#html" role="tab" data-toggle="tab">HTML</a></li>
                            <li role="presentation"><a href="#html_preview" role="tab" data-toggle="tab">HTML预览</a></li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="preview">
                                <h4>Loading preview...</h4>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="markdown"><textarea rows="30"
                                                                                          class="form-control"><?php echo $markdown ?></textarea>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="html"><textarea rows="30"
                                                                                      class="form-control"><?php echo $markdown_html ?></textarea>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="html_preview"><?php echo $markdown_html ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
}

function markdown_form_template() {
?><!DOCTYPE html>
<html>
<head>
    <title>MySQL转Markdown</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container" style="margin-top:40px">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">MySQL转换Markdown文档</h2>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="post">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">数据库主机</label>
                            <div class="col-sm-9">
                                <input type="text" name="host" class="form-control" placeholder="localhost">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">数据库名称</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">数据库用户</label>
                            <div class="col-sm-9">
                                <input type="text" name="user" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">数据库密码</label>
                            <div class="col-sm-9">
                                <input type="password" name="password" class="form-control" placeholder="">
                            </div>
                        </div>
                        <hr>
                        <h3>选项</h3>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">列</label>
                            <div class="col-sm-9">
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="columns[]" value="Field" checked="checked"> 字段
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="columns[]" value="Type" checked="checked"> 数据类型
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="columns[]" value="Collation"> 字符集
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="columns[]" value="Null" checked="checked"> 允许空值
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="columns[]" value="Key" checked="checked"> 主键
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="columns[]" value="Default" checked="checked"> 默认值
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="columns[]" value="Extra"> 额外（是否自动递增）
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="columns[]" value="Privileges"> 权限
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="columns[]" value="Comment" checked="checked"> 说明
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">粗体显示字段名</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="bold_field[]" value="1"> 是
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="bold_field[]" value="0" checked="checked"> 否
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">将Null显示为必填</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="reverse_null[]" value="1"> 是
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="reverse_null[]" value="0" checked="checked"> 否
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">单独的注释行（仅HTML版本）</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="comment_row[]" value="1"> 是
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="comment_row[]" value="0" checked="checked"> 否
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-default">生成markdown</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html><?php
}

?>




