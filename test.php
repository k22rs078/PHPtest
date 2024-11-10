<?php

// 定数の設定
define('INSTAGRAM_BUSINESS_ID', '17841466391205389');
define('ACCESS_TOKEN', 'EAAxQ4uG4CvIBO2UjCZCHCQdVDG9LyYvRZCPIuNEZC8eIAKGZAHmfYigvlEUQcYrqKZAa35whqQvvWHKoEyiLSuuDydVBt2tda0GFH8QiFXZCBMVwj26GrW0KwzVboTsxB53uPSZC7C6dZBOVzYvbZC3p8g14ZCNaOUyNLnUfrHcXUPhIrSW5ON7dWFGzF4dLRtlCmG');

$instagram_accounts = [
    'mikadukian',
    'cafe_lunch_smile',
    'payanpayancafe',
    'casa20171106',
    'genkai_yokocho',
    'okahachi_cafe',
    'nakamarushoyukattomoyan',
    'michinoeki.munakata',
    'katsugyo_center',
    'munakata_chayu'
];

$result = [];

// 各アカウントからメディアデータを取得
foreach ($instagram_accounts as $account_name) {
    // Instagram Graph APIのエンドポイントURLを構築
    $url = 'https://graph.facebook.com/v20.0/' . INSTAGRAM_BUSINESS_ID . '?fields=business_discovery.username(' . $account_name . '){media.limit(5){caption,media_url,permalink,timestamp,username}}';
    $url .= '&access_token=' . ACCESS_TOKEN;

    // cURLセッションを初期化
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // APIリクエストを実行し、レスポンスを取得
    $response = curl_exec($ch);

    // エラーチェックと処理
    if ($response === false) {
        $result[] = ['account_name' => $account_name, 'error' => 'cURLエラー: ' . curl_error($ch)];
        curl_close($ch);
        continue;
    }

    // cURLセッションをクローズ
    curl_close($ch);

    // JSONデータをPHPの連想配列に変換
    $data = json_decode($response, true);

    // データが取得できた場合の処理
    if (isset($data['business_discovery']['media']['data'])) {
        $mediaData = $data['business_discovery']['media']['data'];
        $mediaList = [];
        foreach ($mediaData as $media) {
            $caption = mb_convert_encoding($media['caption'], 'UTF-8', 'auto');

            // メディア情報を配列に追加
            $mediaList[] = [
                'caption' => $caption,
                'media_url' => $media['media_url'],
                'permalink' => $media['permalink'],
                'timestamp' => $media['timestamp'],
                'username' => $media['username']
            ];
        }

        $result[] = [
            'account_name' => $account_name,
            'media' => $mediaList
        ];
    } else {
        $result[] = [
//            'account_name' => $account_name,
            'error' => 'No media found'
        ];
    }
}

// JSON形式で出力
header('Content-Type: application/json');
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>
