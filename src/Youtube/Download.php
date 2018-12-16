<?php
/**
 * Created by IntelliJ IDEA.
 * User: zfm
 * Date: 2018/12/4
 * Time: 11:52 AM
 */

namespace Ahaschool\Videodl\Youtube;

use YoutubeDl\YoutubeDl;
use YoutubeDl\Exception\CopyrightException;
use YoutubeDl\Exception\NotFoundException;
use YoutubeDl\Exception\PrivateVideoException;

class Download
{
    /**
     * YouTubeDL cmd path
     */
    const YTB_DL_CMD_PATH = __DIR__.'/../../bin/youtube-dl';

    /**
     * YouTube下载对象
     * @var null|YoutubeDl
     */
    private $YoutubeDl = null;

    /**
     * 初始化函数
     * Download constructor.
     * @param $path
     * @param array $init
     */
    public function __construct($path, $init = ['continue' => true, 'format' => 'bestvideo'])
    {
        $this->YoutubeDl = new YoutubeDl($init);
        $this->YoutubeDl->setBinPath(self::YTB_DL_CMD_PATH);
        $this->YoutubeDl->setDownloadPath($path);
    }

    /**
     * @param $key
     * @return \YoutubeDl\Entity\Video|\YoutubeDl\Entity\Video[]
     * @throws \Exception
     */
    public function dl($key)
    {
        if (!$key) {
            throw new \Exception('key not allow null');
        }
        try {
            return $this->YoutubeDl->download("https://www.youtube.com/watch?v={$key}");
            // $video->getFile(); // \SplFileInfo instance of downloaded file
        } catch (NotFoundException $e) {
            throw new \Exception($e->getMessage());
            // Video not found
        } catch (PrivateVideoException $e) {
            throw new \Exception($e->getMessage());
            // Video is private
        } catch (CopyrightException $e) {
            throw new \Exception($e->getMessage());
            // The YouTube account associated with this video has been terminated due to multiple third-party notifications of copyright infringement
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
            // Failed to download
        }
    }
}
