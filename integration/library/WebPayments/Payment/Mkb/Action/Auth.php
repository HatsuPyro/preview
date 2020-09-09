<?php


namespace Pyrobyte\WebPayments\Payment\Mkb\Action;


use Pyrobyte\WebPayments\Request;

class Auth extends \Pyrobyte\WebPayments\Payment\ActionAbstract
{
    public $resultClass = '\Pyrobyte\WebPayments\Payment\Mkb\Result\Auth';

    private $oupfi3gnom = '';
    private $pv234yen4xdx3ks = '';
    private $sid = '';
    private $nonce = '';
    private $salt = '';
    private $iterationsCount = '';
    private $clientKey = '';
    private $authMessage = '';
    private $saltedPassword = '';

    public function run()
    {
        $response = $this->stepSix();

        if (!preg_match('/Мои продукты/', $response->getContent())) {
            $this->stepOne();
            $this->stepTwo();
            $clientProof = $this->getClientProof();
            $this->stepThree($clientProof);
            $this->stepFour();
            $this->stepFive();
            $response = $this->stepSix();
        }

        $this->checkCard($response);

        $result = new $this->resultClass($response);
        $result->result = true;
        return $result;
    }

    private function checkCard($response) {
        $card = $this->client->getCard();
        $patternCard = '~'.substr($card, 0, 1) . '.+' . substr($card, strlen($card) - 4, 4) . '~';

        preg_match_all('~[\d]\*\*\*[\d]{4}~U', $response->getContent(), $matches);

        if (count($matches[0]) != 1) {
            throw new \Exception('В ответе нет данных карты');
        }

        if(!preg_match($patternCard, $matches[0][0])) {
            throw new \Exception('В ответе нет данных карты');
        }

    }

    private function stepOne()
    {
        $request = new Request(
            'https://online.mkb.ru/',
            Request::METHOD_GET,
            []
        );

        $request->setHtmlHeaders([]);
        $response = $this->request($request);
        $this->getFormData($response->getContent());
    }

    private function getFormData($html) {
        preg_match('/name="oupfi3gnom".+value="(.+)"/', $html, $matches);
        $this->oupfi3gnom = $matches[1];

        preg_match('/name="pv234yen4xdx3ks".+value="(.+)"/', $html, $matches);
        $this->pv234yen4xdx3ks = $matches[1];

        preg_match("/ClientKey: '(.+)',/U", $html, $matches);
        $this->clientKey = $matches[1];
    }

    private function stepTwo()
    {
        $rand = bin2hex(random_bytes(32));
        $request = new Request(
            'https://online.mkb.ru/auth?m=l',
            Request::METHOD_POST,
            [
                "l" => mb_convert_encoding($this->client->getAccount(), "UTF-8"),
                "nc" => mb_convert_encoding($rand, "UTF-8"),
            ]
        );

        $request->setHtmlHeaders([
            'Host' => 'online.mkb.ru',
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Content-type' => 'application/json; charset=utf-8',
            'X-Requested-With' => 'XMLHttpRequest',
            'Content-Length' => 87,
            'Origin' => 'https://online.mkb.ru',
            'Connection' => 'keep-alive',
            'Referer' => 'https://online.mkb.ru/',
            'TE' => 'Trailers'
        ]);
        $response = $this->request($request);
        $json = json_decode(json_decode($response->getContent())->d);
        $this->nonce = $json->NonceS;
        $this->salt = $json->Salt;
        $this->iterationsCount = $json->IterationsCount;
        $this->sid = $json->SessionId;
    }

    private function getClientProof() {
        $n = $this->client->getAccount();
        $t = md5($this->client->getPassword());
        $i = $this->salt;
        $r = $this->iterationsCount;
        $u = $this->nonce;
        $f = $this->sid;

        $this->saltedPassword = $this->hi($t, $i , $r);

        $e = $this->getHmac($this->saltedPassword, $this->clientKey);
        $o = $this->getHash($e);
        $this->authMessage = $this->getAuthMessage($n, $u, $i, $r, $f);
        $s = $this->getHmac($o, $this->authMessage);
        $clientProof = $this->xorHEX($e, $s);

        return $clientProof;
    }

    private function getAuthMessage($n, $t, $i, $r, $u) {
        $e = '';
        for ($f = 0; $f < strlen($n); $f++) {
            $e .= $this->byteToHex($this->jsCharCodeAt($n, $f));
        }

        $e .= $t;
        $e .= $i;
        $e .= $this->int2bytes($r);

        for ($f = 0; $f < strlen($u); $f++) {
            $e .= $this->byteToHex($this->jsCharCodeAt($u, $f));
        }

        return $e;
    }

    private function int2bytes($n) {
        return '' . $this->byteToHex(($n >> 24) & 255) . $this->byteToHex(($n >> 16) & 255) . $this->byteToHex(($n >> 8) & 255) . $this->byteToHex($n & 255);
    }

    private function jsCharCodeAt($str, $index) {
        $utf16 = mb_convert_encoding($str, 'UTF-16LE', 'UTF-8');
        return ord($utf16[$index*2]) + (ord($utf16[$index*2+1]) << 8);
    }

    private function hi($n, $t, $i) {
        $intOne = '00000001';
        $e = $t . $intOne;
        $r = $this->getHmac($n, $e);
        $u = $r;

        for ($f =1; $f < $i; $f++) {
            $u = $this->getHmac($n, $u);
            $r = $this->xorHEX($r, $u);
        }
        return $r;
    }

    private function getHash($n) {
       return hash('sha512', hex2bin($n));
    }

    private function byteToHex($n){
        $hexChar = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F"];
        $result = $hexChar[($n >> 4) & 15] . $hexChar[$n & 15];
        return $result;
    }
    private function hex2byte($n){
        $result = intval($n, 16);
        return $result;
    }

    private function xorHEX($n, $t){
        $r = '';
        for ($i = 0; $i < strlen($n); $i += 2) {
            $r .= $this->byteToHex($this->hex2byte($n[$i] . $n[$i + 1]) ^ $this->hex2byte($t[$i] . $t[$i + 1]));
        }

        return $r;
    }

    private function getHmac($n, $i) {
        return hash_hmac('sha512', hex2bin($i), hex2bin($n));
    }

    private function stepThree($clientProof)
    {
        $request = new Request(
            'https://online.mkb.ru/',
            Request::METHOD_POST,
            [
                'form_params' => [
                    "__VIEWSTATE" => "",
                    "__EVENTTARGET" => "btnLoginStandard",
                    '__EVENTARGUMENT' => "",
                    'gjggdp' => '1',
                    'oupfi3gnom' => $this->oupfi3gnom,
                    'pv234yen4xdx3ks' => $this->pv234yen4xdx3ks,
                    'ujefp2er' => '8f01d570f8456be68ca13022de0aa509',
                    'atrgpx4f' => 'a20615b62a9b5bfe2b98aefa22f5c431',
                    'txtLogin' => $this->client->getAccount(),
                    'txtPassword' => '',
                    'fieldCard' => ["",""],
                    'sid' => $this->sid,
                    'l' => $this->client->getAccount(),
                    'cp' => $clientProof,
                    'ns' => $this->nonce,
                    'am' => $this->authMessage,
                    'sp' => $this->saltedPassword,
                    'fieldUsername' => '',
                    'fieldUsername2' => '',
                    'Registration1$tbxCaptR' => '',
                    'ForgotPassword1$tbxCaptFp' => '',
                    'ForgotPassword1$txtDate' => '',
                    'StaticDocumentCategoryRepeater$ctl01$categoryName' => 'Заявления+и+договоры',
                    'StaticDocumentCategoryRepeater$ctl02$categoryName' => 'Тарифы',
                    'StaticDocumentCategoryRepeater$ctl03$categoryName' => 'Памятки+клиенту',
                    'fingerprint' => '[{"key":"user_agent","value":"Mozilla/5.0+(Windows+NT+10.0;+Win64;+x64;+rv:78.0)+Gecko/20100101+Firefox/78.0"},{"key":"language","value":"ru-RU"},{"key":"resolution","value":[1920,1080]},{"key":"available_resolution","value":[1920,1040]},{"key":"timezone_offset","value":-420},{"key":"session_storage","value":1},{"key":"local_storage","value":1},{"key":"cpu_class","value":"unknown"},{"key":"navigator_platform","value":"Win32"},{"key":"do_not_track","value":"unspecified"},{"key":"regular_plugins","value":[]},{"key":"canvas","value":"canvas+winding:yes~canvas+fp:data:image/png;base64,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"},{"key":"webgl","value":"data:image/png;base64,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~extensions:ANGLE_instanced_arrays;EXT_blend_minmax;EXT_color_buffer_half_float;EXT_float_blend;EXT_frag_depth;EXT_shader_texture_lod;EXT_sRGB;EXT_texture_compression_bptc;EXT_texture_filter_anisotropic;OES_element_index_uint;OES_standard_derivatives;OES_texture_float;OES_texture_float_linear;OES_texture_half_float;OES_texture_half_float_linear;OES_vertex_array_object;WEBGL_color_buffer_float;WEBGL_compressed_texture_s3tc;WEBGL_compressed_texture_s3tc_srgb;WEBGL_debug_renderer_info;WEBGL_debug_shaders;WEBGL_depth_texture;WEBGL_draw_buffers;WEBGL_lose_context~webgl+aliased+line+width+range:[1,+1]~webgl+aliased+point+size+range:[1,+1024]~webgl+alpha+bits:8~webgl+antialiasing:yes~webgl+blue+bits:8~webgl+depth+bits:24~webgl+green+bits:8~webgl+max+anisotropy:16~webgl+max+combined+texture+image+units:32~webgl+max+cube+map+texture+size:16384~webgl+max+fragment+uniform+vectors:1024~webgl+max+render+buffer+size:16384~webgl+max+texture+image+units:16~webgl+max+texture+size:16384~webgl+max+varying+vectors:30~webgl+max+vertex+attribs:16~webgl+max+vertex+texture+image+units:16~webgl+max+vertex+uniform+vectors:4096~webgl+max+viewport+dims:[32767,+32767]~webgl+red+bits:8~webgl+renderer:Mozilla~webgl+shading+language+version:WebGL+GLSL+ES+1.0~webgl+stencil+bits:0~webgl+vendor:Mozilla~webgl+version:WebGL+1.0~webgl+vertex+shader+high+float+precision:23~webgl+vertex+shader+high+float+precision+rangeMin:127~webgl+vertex+shader+high+float+precision+rangeMax:127~webgl+vertex+shader+medium+float+precision:23~webgl+vertex+shader+medium+float+precision+rangeMin:127~webgl+vertex+shader+medium+float+precision+rangeMax:127~webgl+vertex+shader+low+float+precision:23~webgl+vertex+shader+low+float+precision+rangeMin:127~webgl+vertex+shader+low+float+precision+rangeMax:127~webgl+fragment+shader+high+float+precision:23~webgl+fragment+shader+high+float+precision+rangeMin:127~webgl+fragment+shader+high+float+precision+rangeMax:127~webgl+fragment+shader+medium+float+precision:23~webgl+fragment+shader+medium+float+precision+rangeMin:127~webgl+fragment+shader+medium+float+precision+rangeMax:127~webgl+fragment+shader+low+float+precision:23~webgl+fragment+shader+low+float+precision+rangeMin:127~webgl+fragment+shader+low+float+precision+rangeMax:127~webgl+vertex+shader+high+int+precision:0~webgl+vertex+shader+high+int+precision+rangeMin:31~webgl+vertex+shader+high+int+precision+rangeMax:30~webgl+vertex+shader+medium+int+precision:0~webgl+vertex+shader+medium+int+precision+rangeMin:31~webgl+vertex+shader+medium+int+precision+rangeMax:30~webgl+vertex+shader+low+int+precision:0~webgl+vertex+shader+low+int+precision+rangeMin:31~webgl+vertex+shader+low+int+precision+rangeMax:30~webgl+fragment+shader+high+int+precision:0~webgl+fragment+shader+high+int+precision+rangeMin:31~webgl+fragment+shader+high+int+precision+rangeMax:30~webgl+fragment+shader+medium+int+precision:0~webgl+fragment+shader+medium+int+precision+rangeMin:31~webgl+fragment+shader+medium+int+precision+rangeMax:30~webgl+fragment+shader+low+int+precision:0~webgl+fragment+shader+low+int+precision+rangeMin:31~webgl+fragment+shader+low+int+precision+rangeMax:30"},{"key":"adblock","value":false},{"key":"has_lied_languages","value":false},{"key":"has_lied_resolution","value":false},{"key":"has_lied_os","value":false},{"key":"has_lied_browser","value":false},{"key":"touch_support","value":[0,false,false]},{"key":"js_fonts","value":["Arial","Arial+Black","Calibri","Cambria","Cambria+Math","Comic+Sans+MS","Consolas","Courier","Courier+New","Georgia","Helvetica","Impact","Lucida+Console","Lucida+Sans+Unicode","Microsoft+Sans+Serif","MS+Gothic","MS+PGothic","MS+Sans+Serif","MS+Serif","Palatino+Linotype","Segoe+Print","Segoe+Script","Segoe+UI","Segoe+UI+Light","Segoe+UI+Semibold","Segoe+UI+Symbol","Tahoma","Times","Times+New+Roman","Trebuchet+MS","Verdana","Wingdings+2","Wingdings+3"]},{"key":"cookie_enabled","value":true},{"key":"loc_time","value":"'.now()->format("d.m.Y").'+'.now()->format("H:i:s").'"}]',
                ],

                'additional_options' => [
                    'timeout' => 10
                ]
            ]
        );

        $request->setHtmlHeaders([
            'Host' => 'online.mkb.ru',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Content-type' => 'application/x-www-form-urlencoded',
            'X-Requested-With' => 'XMLHttpRequest',
            'Content-Length' => '32627',
            'Origin' => 'https://online.mkb.ru',
            'Connection' => 'keep-alive',
            'Referer' => 'https://online.mkb.ru/',
            'Upgrade-Insecure-Requests' => '1',
            'TE' => 'Trailers'
        ]);
        $response = $this->request($request);
    }

    public function stepFour() {
        $request = new Request(
            'https://online.mkb.ru/secure/login.aspx?a=2&returnurl=',
            Request::METHOD_GET,
            []
        );

        $request->setHtmlHeaders([
            'Host' => 'online.mkb.ru',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Connection' => 'keep-alive',
            'Referer' => 'https://online.mkb.ru/',
            'TE' => 'Trailers'
        ]);
        $this->request($request);
    }

    public function stepFive() {
        sleep(5);
        $simboxService = \Pyrobyte\Phone\ServiceFactory::getService();
        $messagesResult = $simboxService->getSms([$this->client->getSimboxId()]);
        $messages = $messagesResult->getMessages();
        $messages = array_slice($messages, 0, 1);
        $smsCode = 0;
        foreach ($messages as $message) {
            if (preg_match('/Никому не говорите код (\d+).+mkb.ru\/mb/imu', $message->get(), $matches)) {
                $smsCode = $matches[1];
            }
        }
        $request = new Request(
            'https://online.mkb.ru/secure/login.aspx?a=2&returnurl=',
            Request::METHOD_POST,
            [
                'form_params' => [
                    "__VIEWSTATE" => "",
                    "__EVENTTARGET" => "btnLoginSMS",
                    '__EVENTARGUMENT' => "",
                    'gjggdp' => '1',
                    'txtCode' => $smsCode,
                    'fieldCard' => ["",""],
                    'fieldUsername' => '',
                    'fieldUsername2' => '',
                    'Registration1$tbxCaptR' => '',
                    'ForgotPassword1$tbxCaptFp' => '',
                    'ForgotPassword1$txtDate' => '',
                    'StaticDocumentCategoryRepeater$ctl01$categoryName' => 'Заявления+и+договоры',
                    'StaticDocumentCategoryRepeater$ctl02$categoryName' => 'Тарифы',
                    'StaticDocumentCategoryRepeater$ctl03$categoryName' => 'Памятки+клиенту',
                    'fingerprint' => '[{"key":"user_agent","value":"Mozilla/5.0+(Windows+NT+10.0;+Win64;+x64;+rv:78.0)+Gecko/20100101+Firefox/78.0"},{"key":"language","value":"ru-RU"},{"key":"resolution","value":[1920,1080]},{"key":"available_resolution","value":[1920,1040]},{"key":"timezone_offset","value":-420},{"key":"session_storage","value":1},{"key":"local_storage","value":1},{"key":"cpu_class","value":"unknown"},{"key":"navigator_platform","value":"Win32"},{"key":"do_not_track","value":"unspecified"},{"key":"regular_plugins","value":[]},{"key":"canvas","value":"canvas+winding:yes~canvas+fp:data:image/png;base64,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"},{"key":"webgl","value":"data:image/png;base64,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~extensions:ANGLE_instanced_arrays;EXT_blend_minmax;EXT_color_buffer_half_float;EXT_float_blend;EXT_frag_depth;EXT_shader_texture_lod;EXT_sRGB;EXT_texture_compression_bptc;EXT_texture_filter_anisotropic;OES_element_index_uint;OES_standard_derivatives;OES_texture_float;OES_texture_float_linear;OES_texture_half_float;OES_texture_half_float_linear;OES_vertex_array_object;WEBGL_color_buffer_float;WEBGL_compressed_texture_s3tc;WEBGL_compressed_texture_s3tc_srgb;WEBGL_debug_renderer_info;WEBGL_debug_shaders;WEBGL_depth_texture;WEBGL_draw_buffers;WEBGL_lose_context~webgl+aliased+line+width+range:[1,+1]~webgl+aliased+point+size+range:[1,+1024]~webgl+alpha+bits:8~webgl+antialiasing:yes~webgl+blue+bits:8~webgl+depth+bits:24~webgl+green+bits:8~webgl+max+anisotropy:16~webgl+max+combined+texture+image+units:32~webgl+max+cube+map+texture+size:16384~webgl+max+fragment+uniform+vectors:1024~webgl+max+render+buffer+size:16384~webgl+max+texture+image+units:16~webgl+max+texture+size:16384~webgl+max+varying+vectors:30~webgl+max+vertex+attribs:16~webgl+max+vertex+texture+image+units:16~webgl+max+vertex+uniform+vectors:4096~webgl+max+viewport+dims:[32767,+32767]~webgl+red+bits:8~webgl+renderer:Mozilla~webgl+shading+language+version:WebGL+GLSL+ES+1.0~webgl+stencil+bits:0~webgl+vendor:Mozilla~webgl+version:WebGL+1.0~webgl+vertex+shader+high+float+precision:23~webgl+vertex+shader+high+float+precision+rangeMin:127~webgl+vertex+shader+high+float+precision+rangeMax:127~webgl+vertex+shader+medium+float+precision:23~webgl+vertex+shader+medium+float+precision+rangeMin:127~webgl+vertex+shader+medium+float+precision+rangeMax:127~webgl+vertex+shader+low+float+precision:23~webgl+vertex+shader+low+float+precision+rangeMin:127~webgl+vertex+shader+low+float+precision+rangeMax:127~webgl+fragment+shader+high+float+precision:23~webgl+fragment+shader+high+float+precision+rangeMin:127~webgl+fragment+shader+high+float+precision+rangeMax:127~webgl+fragment+shader+medium+float+precision:23~webgl+fragment+shader+medium+float+precision+rangeMin:127~webgl+fragment+shader+medium+float+precision+rangeMax:127~webgl+fragment+shader+low+float+precision:23~webgl+fragment+shader+low+float+precision+rangeMin:127~webgl+fragment+shader+low+float+precision+rangeMax:127~webgl+vertex+shader+high+int+precision:0~webgl+vertex+shader+high+int+precision+rangeMin:31~webgl+vertex+shader+high+int+precision+rangeMax:30~webgl+vertex+shader+medium+int+precision:0~webgl+vertex+shader+medium+int+precision+rangeMin:31~webgl+vertex+shader+medium+int+precision+rangeMax:30~webgl+vertex+shader+low+int+precision:0~webgl+vertex+shader+low+int+precision+rangeMin:31~webgl+vertex+shader+low+int+precision+rangeMax:30~webgl+fragment+shader+high+int+precision:0~webgl+fragment+shader+high+int+precision+rangeMin:31~webgl+fragment+shader+high+int+precision+rangeMax:30~webgl+fragment+shader+medium+int+precision:0~webgl+fragment+shader+medium+int+precision+rangeMin:31~webgl+fragment+shader+medium+int+precision+rangeMax:30~webgl+fragment+shader+low+int+precision:0~webgl+fragment+shader+low+int+precision+rangeMin:31~webgl+fragment+shader+low+int+precision+rangeMax:30"},{"key":"adblock","value":false},{"key":"has_lied_languages","value":false},{"key":"has_lied_resolution","value":false},{"key":"has_lied_os","value":false},{"key":"has_lied_browser","value":false},{"key":"touch_support","value":[0,false,false]},{"key":"js_fonts","value":["Arial","Arial+Black","Calibri","Cambria","Cambria+Math","Comic+Sans+MS","Consolas","Courier","Courier+New","Georgia","Helvetica","Impact","Lucida+Console","Lucida+Sans+Unicode","Microsoft+Sans+Serif","MS+Gothic","MS+PGothic","MS+Sans+Serif","MS+Serif","Palatino+Linotype","Segoe+Print","Segoe+Script","Segoe+UI","Segoe+UI+Light","Segoe+UI+Semibold","Segoe+UI+Symbol","Tahoma","Times","Times+New+Roman","Trebuchet+MS","Verdana","Wingdings+2","Wingdings+3"]},{"key":"cookie_enabled","value":true},{"key":"loc_time","value":"'.now()->format("d.m.Y").'+'.now()->format("H:i:s").'"}]',
                ],

                'additional_options' => [
                    'timeout' => 10
                ]
            ]
        );

        $request->setHtmlHeaders([
            'Host' => 'online.mkb.ru',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Content-type' => 'application/x-www-form-urlencoded',
            'Content-Length' => '31525',
            'Origin' => 'https://online.mkb.ru',
            'Connection' => 'keep-alive',
            'Referer' => 'https://online.mkb.ru/secure/login.aspx?a=2&returnurl=',
            'TE' => 'Trailers'
        ]);
        $this->request($request);
    }

    public function stepSix() {
        $request = new Request(
            'https://online.mkb.ru/secure/main.aspx',
            Request::METHOD_GET,
            []
        );

        $request->setHtmlHeaders([
            'Host' => 'online.mkb.ru',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Connection' => 'keep-alive',
            'Referer' => 'https://online.mkb.ru/secure/login.aspx?a=2&returnurl=',
            'TE' => 'Trailers'
        ]);
        $response = $this->request($request);
        return $response;
    }
}