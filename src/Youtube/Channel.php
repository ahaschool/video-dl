<?php
/**
 * Created by IntelliJ IDEA.
 * User: zfm
 * Date: 2018/12/3
 * Time: 3:57 PM
 */
namespace Ahaschool\Videodl\Youtube;

use GuzzleHttp\Client;

class Channel {

    /**
     * 定义基本地址
     */
    const BASE_URL = 'https://www.youtube.com/';

    /**
     * http 请求类
     * @var null | \GuzzleHttp\Client
     */
    private $http = null;

    /**
     * video items
     * @var array
     */
    private $items = [];

    /**
     * 频道
     * @var string
     */
    private $channel = '';

    /**
     * Channel constructor.
     * @param $channel
     */
    public function __construct($channel)
    {
        $this->channel = $channel;
    }

    /**
     * 获取视频条目
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * 获取频道的hash值
     * @return string
     */
    public function getChannelHash()
    {
        return hash('sha1', $this->channel);
    }

    /**
     * 获取频道url
     * @return string
     */
    public function getChannelUrl()
    {
        return self::BASE_URL . $this->channel;
    }

    /**
     * 获取频道
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * 抓取视频信息
     * @return $this
     */
    public function initVideoItems()
    {
        $this->http = new Client([
            'timeout' => 10,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36',
                'x-youtube-client-name' => '1',
                'x-youtube-client-version' => '2.20171011',
            ]
        ]);
        $this->loopGetVideoData();
        return $this;
    }

    /**
     * 循环获取视频地址
     * @param bool $is_ajax
     * @param string $url
     */
    private function loopGetVideoData($is_ajax = false, $url = '')
    {
        if (!$is_ajax) {
            $res = $this->http->get($this->getChannelUrl());
            $content = $res->getBody()->getContents();
            preg_match('/gridRenderer\":\{\"items\":(.*?),\"continuations/', $content, $match);
            $items = isset($match[1]) ? json_decode($match[1], true) : [];
            array_map(function ($item) {
                array_push($this->items, $item['gridVideoRenderer']['navigationEndpoint']['commandMetadata']['webCommandMetadata']['url']);
            }, $items);
            preg_match('/"continuations":(.*?),"trackingParams"/', $content, $next);
            if (isset($next[1])) {
                $arr = json_decode($next[1], true);
                $ctoken = $arr[0]['nextContinuationData']['continuation'];
                $itct = $arr[0]['nextContinuationData']['clickTrackingParams'];
                $url = self::BASE_URL . 'browse_ajax?ctoken=' . $ctoken . '&itct=' . $itct;
                $this->loopGetVideoData(true, $url);
            }
        } else {
            $res = $this->http->get($url);
            $video = \GuzzleHttp\json_decode($res->getBody(), true);
            $arr = $video[1]['response']['continuationContents']['gridContinuation'] ?? [];
            $next = $arr['continuations'][0]['nextContinuationData'] ?? [];
            if (!empty($arr['items']) && !empty($next)) {
                array_map(function ($item) {
                    array_push($this->items, $item['gridVideoRenderer']['navigationEndpoint']['commandMetadata']['webCommandMetadata']['url']);
                }, $arr['items']);
                $ctoken = $next['continuation'] ?? '';
                $itct = $next['clickTrackingParams'] ?? '';
                $url = self::BASE_URL . 'browse_ajax?ctoken=' . $ctoken . '&itct=' . $itct;
                $this->loopGetVideoData(true, $url);
            }
        }
    }


}
