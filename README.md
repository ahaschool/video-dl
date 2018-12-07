## Install
install the wrapper using [Composer](http://getcomposer.org/):
```
composer require ahaschool/video-dl
```
Add composer script at your root composer.josn
```
"scripts": {
    ...
    "post-install-cmd": "chmod +x vendor/ahaschool/video-dl/bin/youtube-dl"
 }
```

## Get youtube video url list
```php
<?php
require __DIR__ . '/vendor/autoload.php';

use Ahaschool\Videodl\Youtube\Channel;

$chl = new Channel('user/byjusclasses/videos');
$items = $chl->initVideoItems()->getItems();
```

## Download youtube video by video key
```php
<?php
require __DIR__ . '/vendor/autoload.php';

use Ahaschool\Videodl\Youtube\Download;

$dl = new Download('./');
try {
    $video = $dl->dl('WPOoNZbT-1w');
} catch (\Exception $e) {
    echo $e->getMessage();
}
```
