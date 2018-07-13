#!/usr/bin/php
<?php
// ------------------------------------------------------------
// 
// txtregist.php
// 
// Ver.01.00	2018.07.11 Let's Encrypt の DNS-01 用のスクリプト。
// 
// certbotから--manual-auth-hookオプションで指定されて呼び出されて、CERTBOT_DOMAIN(domain)とCERTBOT_VALIDATION(validation string)を渡されるので、MyDNS.JPに登録する
// ------------------------------------------------------------
?>
<?php
// ----------------------------------------------------------------------
// MyDNS.JP Parameter
// ----------------------------------------------------------------------
include(__DIR__.'/txtedit.conf');
?>
<?php
// ----------------------------------------------------------------------
// Init Routine
// ----------------------------------------------------------------------
?>
<?php
// タイムゾーン設定
date_default_timezone_set(@date_default_timezone_get());

// 内部文字コード
mb_internal_encoding('UTF-8');

// 出力文字コード
mb_http_output('UTF-8');

// certbotが渡してくる可能性がある変数
$CERTBOT_ENV_LIST = array('CERTBOT_DOMAIN','CERTBOT_VALIDATION','CERTBOT_TOKEN','CERTBOT_CERT_PATH','CERTBOT_KEY_PATH','CERTBOT_SNI_DOMAIN','CERTBOT_AUTH_OUTPUT');

// TXTレコードの編集コマンド
$MYDNSJP_CMD = 'DELETE';
?>
<?php
// ----------------------------------------------------------------------
// Sub Routine
// ----------------------------------------------------------------------
?>
<?php
// ----------------------------------------------------------------------
// Main Routine
// ----------------------------------------------------------------------
?>
<?php
// certbotが渡してくる可能性がある変数を取得
foreach ($CERTBOT_ENV_LIST as $CERTBOT_ENV_NAME)
{
    // 変数を取得
    $CERTBOT_ENV[$CERTBOT_ENV_NAME] = getenv($CERTBOT_ENV_NAME);
}
// 処理用コマンドも追加
$CERTBOT_ENV['EDIT_CMD'] = $MYDNSJP_CMD;

// ヘッダー文字列を生成
$MYDNSJP_HEADERS = array('Content-Type: application/x-www-form-urlencoded',
                         'Authorization: Basic '. base64_encode($MYDNSJP_MASTERID.':'.$MYDNSJP_MASTERPWD),
                         );

// URLエンコードされたクエリ文字列を生成
$MYDNSJP_QUERY = http_build_query($CERTBOT_ENV);

// コンテクストリソースを設定
$POST_OPTIONS = array( 'http' => 
                    array('method' => 'POST',
                          'header' => implode("\r\n", $MYDNSJP_HEADERS),
                          'content' => $MYDNSJP_QUERY)
                );

// 指定したURIに対してコンテクストリソースを投げてコンテンツを取得する。
$MYDNSJP_CONTENTS = file_get_contents($MYDNSJP_URL, false, stream_context_create($POST_OPTIONS));

// --------------------------------
// 以下はデバッグ用
// --------------------------------
$DEBUG = "";
/*
foreach ($CERTBOT_ENV as $CERTBOT_ENV_NAME => $CERTBOT_ENV_VALUE)
{
    if ($CERTBOT_ENV_VALUE === FALSE)
    {
        $DEBUG .= $CERTBOT_ENV_NAME.'=FALSE'."\n";
    }
    else
    {
        $DEBUG .= $CERTBOT_ENV_NAME.'='.$CERTBOT_ENV_VALUE."\n";
    }
}
*/
$DEBUG .= 'MYDNSJP_CONTENTS='.$MYDNSJP_CONTENTS."\n";

$DEBUG_LOG = fopen(__DIR__.'/debug.log', 'a+');
fwrite($DEBUG_LOG, $DEBUG); 
fclose($DEBUG_LOG);

?>
