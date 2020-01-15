<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15 0000
 * Time: 21:52
 */

namespace App\Api;
use App\Lib\Common;
use Core\Container\Mapping\Bean;
use Psr\Http\Message\UploadedFileInterface;


/**
 * Class ToolControoler
 * @package App\Api
 * @Bean()
 */
class ToolController
{
    /**
     * 生成保存路径
     * @param $obj
     * @param $type
     * @param $clientName
     * @return string
     */
    public function getFullPath($obj , $type , $clientName)
    {
        $no = Common::makeSerialNo();
        $dir = App::getAlias('@upload');
        $path = "/upload/".$type."/".date("Ymd");
        if (!file_exists($dir.$path)) {
            mkdir($dir.$path, 0777, true);
        }
        if(in_array($type , ['jpeg','jpg','png','avi','mp3']))
        {
            $str = $path."/".$no.".".$type;
        }else
        {
            $str = $path."/".$clientName;
        }
        return $str;
    }
    /**
     * RequestMapping(route="/api/im/image")
     */
    public function uploadImage()
    {
        //获取文件
        $file = request()->file('file');
        if(empty($file))
            return $this->error([],'缺少文件');
        if (!($file instanceof UploadedFileInterface))
            throw new FileException(['文件异常']);
        $type = explode('.',$file->getClientFilename());
        $type = array_pop($type);
        $clientName = $file->getClientFilename();
        $src = $this->getFullPath($file,$type , $clientName);
        $file->moveTo('@upload'.$src);
        chmod(App::getAlias('@upload').$src,0777);
        return $this->success(compact('src'),'',0);
    }

}