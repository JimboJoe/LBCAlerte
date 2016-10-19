<?php

$url = !empty($_GET["aurl"]) ? $_GET["aurl"] : null;

$logger = Logger::getLogger("main");

$content = $client->request($url);

try {
    $parser = \AdService\ParserFactory::factory($url);
} catch (\AdService\Exception $e) {
    $logger->err($e->getMessage());
}

$ad = $parser->processAd(
    $content,
    parse_url($url, PHP_URL_SCHEME)
);

$ad_stored = $storage->fetchById($ad->getId());
if (!$ad_stored) {
    $ad_stored = new \App\BackupAd\Ad();
    $ad_stored->setFromArray($ad->toArray());
}

$storage->save($ad_stored);
header("LOCATION: ./?mod=annonce&a=view&id=".$ad->getId()); exit;