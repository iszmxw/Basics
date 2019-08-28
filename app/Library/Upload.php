<?php

namespace App\Library;

class Upload
{
    /**
     * 本地上传图片方法
     * @param $request
     * @param $field
     * @param $upload_path
     * @param $file_name
     * @return array
     */
    public static function images($request, $field, $upload_path, $file_name)
    {
        $file = $request->file($field);
        if (empty($file)) {
            return ['code' => 50000, 'message' => '请您选择要上传的文件!'];
        }
        //检验一下上传的文件是否有效
        if ($file->isValid()) {
            if ($file->getSize() > $file_size = 5000 * 1024) {
                return ['code' => 50000, 'message' => '上传文件过大,不能大于5M'];
            }
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, ['png', 'jpg', 'gif'])) {
                return ['code' => 50000, 'message' => '请上传png、jpg、gif格式的图片'];
            }
            //重命名文件,文件名加上后缀
            $NewFileName = $file_name . '.' . $ext;
            // 上传文件并判断
            $path = $file->move(public_path() . '/' . $upload_path, $NewFileName);
            $img_url = '/' . $upload_path . $NewFileName;
            if ($path->isFile()) {
                return ['code' => 20000, 'path' => $img_url, 'complete_path' => asset($img_url), 'message' => 'ok'];
            }
        } else {
            return ['code' => 50000, 'message' => '上传文件无效'];
        }
    }


    /**
     * 下载远程图片保存到本地
     * @param $url // 远程图片地址
     * @param string $save_dir 需要保存的地址
     * @param string $filename 保存文件名
     * @return array
     */
    public static function download($url, $save_dir = './public/upload/iszmxw/', $filename = '')
    {
        $ext = strrchr($url, '.');
        if (trim($save_dir) == '')
            $save_dir = './';

        if (trim($filename) == '') {//保存文件名
            $allowExt = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
            if (!in_array($ext, $allowExt))
                return array('file_name' => '', 'save_path' => '', 'error' => 3);

            $filename = time() . $ext;
        }
        if (0 !== strrpos($save_dir, '/'))
            $save_dir .= '/';

        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true))
            return array('file_name' => '', 'save_path' => '', 'error' => 5);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);
        if (empty($filename)) {
            $filename = pathinfo($url, PATHINFO_BASENAME);
        }
        $resource = fopen($save_dir . $filename, 'a');
        fwrite($resource, $file);
        fclose($resource);
        unset($file, $url);
        return ['file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0];
    }
}