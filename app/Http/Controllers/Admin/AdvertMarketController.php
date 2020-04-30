<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdvertAdd;
use App\Library\Logs;
use App\Library\Tools;
use App\Models\Advert;
use App\Models\DeviceScene;
use App\Models\MerchantAdvert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * 广告市场
 * Class AdvertMarketController
 * @package App\Http\Controllers\Admin
 */
class AdvertMarketController extends Controller
{
    /**
     * 上传文件
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:01
     */
    public function uploads(Request $request)
    {
        $file = $request->file('file');
        // 判断图片有效性
        if (!$file->isValid()) {
            return ['code' => 500, 'message' => '上传文件无效..'];
        }
        $Folder = "/";
        try {
            if (strpos($file->getMimeType(), "video") !== false) {
                $Folder = "videos";
            } elseif (strpos($file->getMimeType(), "image") !== false) {
                $Folder = "images";
            }
            $disk   = Storage::disk('oss');
            $rename = time() . rand(1000, 9999) . "." . $file->getClientOriginalExtension();
            // 上传文件到images目录并且重命名
            $file_path         = $disk->putFileAs($Folder, $file, $rename);
            $data['file_path'] = $file_path;
            if ($Folder == "images") {
                $res = Tools::getFileInfo($file_path);
                if ($res != false) {
                    $data['height'] = isset($res['ImageHeight']['value']) ? $res['ImageHeight']['value'] : '';
                    $data['width']  = isset($res['ImageWidth']['value']) ? $res['ImageWidth']['value'] : '';
                }
            }
            // 返回图片绝对路径
            return ['code' => 200, 'message' => '上传成功', 'data' => $data];
        } catch (\Exception $e) {
            \Log::debug($e);
            \Log::debug('文件上传失败');
            return ['code' => 500, 'message' => '上传失败'];

        }

    }


    /**
     * 删除上传的文件
     * @param Request $request
     * @return string
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:01
     */
    public function delete_file(Request $request)
    {
        $ad_file = $request->get('ad_file');
        if (!empty($ad_file)) {
            $disk   = Storage::disk('oss');
            $exists = $disk->has($ad_file);
            if ($exists) {
                $res = $disk->delete($ad_file);
                if ($res) {
                    return 'success';
                } else {
                    return 'fail';
                }
            }
        } else {
            return 'error';
        }
    }

    /**
     * 添加广告
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:01
     */
    public function advert_add(Request $request)
    {
        $scene = DeviceScene::getList([]);
        return view('admin.advert.advert_add', ['scene' => $scene]);
    }


    /**
     * 添加广告
     * @param AdvertAdd $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:01
     */
    public function advert_add_data(AdvertAdd $request)
    {
        $data          = $request->except('_token');
        $admin_data    = $request->get('admin_data');
        $data['price'] = $data['price'] * 100;
        if (empty($data['scene'])) {
            $data['scene'] = 0;
        } else {
            $data['scene'] = implode(',', $data['scene']);
        }
        DB::beginTransaction();
        try {
            $res = Advert::AddData($data);
            Logs::Operation(1, $admin_data['id'], "添加了一条广告，广告的信息如下：" . json_encode($res));
            DB::commit();
            return ['code' => 200, 'message' => '操作成功！'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '操作失败！'];
        }
    }

    /**
     * 广告列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:01
     */
    public function advert_list(Request $request)
    {
        $data['advert_list'] = Advert::getPaginate();
        return view('admin.advert.advert_list', $data);
    }

    /**
     * 修改广告状态
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:01
     */
    public function advert_status(Request $request)
    {
        $id            = $request->get('id');
        $advert_status = $request->get('status');
        $admin_data    = $request->get('admin_data');
        if ($advert_status == 0) {
            $tips = "待审核";
        } elseif ($advert_status == 1) {
            $tips = "已上架";
        } elseif ($advert_status == 2) {
            $tips = "已下架";
        } else {
            $tips = "未知状态";
        }
        DB::beginTransaction();
        try {
            Advert::EditData(['id' => $id], ['status' => $advert_status]);
            Logs::Operation(1, $admin_data['id'], "修改了广告ID为【{$id}】的状态为【{$tips}】");
            DB::commit();
            return ['code' => 200, 'message' => '操作成功！'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 500, 'message' => '操作失败，请稍后再试！'];
        }
    }

    /**
     * 编辑广告=>页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:01
     */
    public function advert_edit(Request $request)
    {
        $advert_id = $request->get('advert_id');
        $advert    = Advert::getOne(['id' => $advert_id]);
        $scene     = DeviceScene::getList([]);
        if (empty($advert)) {
            return view('admin.common.tips', ['msg' => "数据不存在"]);
        } else {
            return view('admin.advert.advert_edit', ['advert' => $advert, 'scene' => $scene]);
        }
    }


    /**
     * 编辑广告=>数据提交
     * @param AdvertAdd $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:02
     */
    public function advert_edit_data(AdvertAdd $request)
    {
        $data = $request->except(['_token', 'advert_id']);
        if (empty($data['scene'])) {
            $data['scene'] = 0;
        } else {
            $data['scene'] = implode(',', $data['scene']);
        }
        $advert_id     = $request->get('advert_id');
        $admin_data    = $request->get('admin_data');
        $data['price'] = $data['price'] * 100;
        DB::beginTransaction();
        try {
            $res = Advert::EditData(['id' => $advert_id], $data);
            MerchantAdvert::EditData(['advert_id' => $advert_id], $data);
            Logs::Operation(1, $admin_data['id'], "编辑了广告id为【{$advert_id}】的广告信息，广告的信息如下：" . json_encode($res));
            DB::commit();
            return ['code' => 200, 'message' => '操作成功！'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '操作失败！'];
        }
    }
}
