<?php
//knopuz_bot
//Shaka_rj
//qadam malumotlari uchun shu papkada 'qadam' fayli yaratiladi
//tranzaksiya malumotlari uchun shu papkada trans papkasi yaratiladi
//sovg'alar berilganini bilish uchun sovga papkasi yaratiladi
//tokenlar. bottoken telegramdagi botning tokeni. token1 va token2 biznes.knop.uz tomonidan berilgan tokenlar.
$bottoken = "KAKAKAKAKAKAKAKAKKAKA";
$token1 = "LALALALALALALALALALA";
$token2 = "PAPAPAPAPAPAPAPAPAPA";

//bot foydalanuvchi malumotlari
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$text = $message->text;
$chat_id = $message->chat->id;

//qadamni yozish
function qadamY($soni){
	$json = json_decode(file_get_contents("qadam"));
	$json -> {$GLOBALS['chat_id']} = $soni;
	file_put_contents("qadam", json_encode($json));
}

//Qadamni aniqlash
$qadam = json_decode(file_get_contents("qadam")) -> {$chat_id};

function getHisob(){
    $chat_id = $GLOBALS['chat_id'];
    $json = json_decode(file_get_contents("https://api.knop.uz/getHisob?hisob=2:$chat_id"));
    return $json -> {'ok'};
}

function getHisob2($hisob){
    $json = json_decode(file_get_contents("https://api.knop.uz/getHisob?hisob=$hisob"));
    return $json;
}

function yaratish(){
    $chat_id = $GLOBALS['chat_id'];
    $ismi = $GLOBALS['ismi'];
    $token1 = $GLOBALS['token1'];
    $json = json_decode(file_get_contents("https://api.knop.uz/Yaratish?hisob=2&ichki_hisob=$chat_id&token=$token1&nomi=$ismi"));
    return $json -> {'ok'};
}

//tranzaksiya qilish uchun
function tranzaksiya($ga, $qiymat){
    $chat_id = $GLOBALS['chat_id'];
    $token1 = $GLOBALS['token1'];
    $token2 = $GLOBALS['token2'];
    $json = json_decode(file_get_contents("https://api.knop.uz/Yuborish?dan=2:$chat_id&ga=$ga&qiymat=$qiymat&token=$token1&token2=$token2"));
    return $json;
}

function send($metod,$datas=[]){
    $datas['chat_id'] = $GLOBALS['chat_id'];
    $datas['parse_mode'] = "Markdown";
    $token = $GLOBALS['bottoken'];
    $url = "https://api.telegram.org/bot$token/".$metod;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}

function nom($nom){
    $nom = str_replace(" ","%20",$nom);
    $chat_id = $GLOBALS['chat_id'];
    $token1 = $GLOBALS['token1'];
    $json = json_decode(file_get_contents("https://api.knop.uz/Nom?nom=$nom&hisob=2:$chat_id&token=$token1"));
    return $json -> {'ok'};
}

function sovga(){
	$chat_id = $GLOBALS['chat_id'];
	if (!file_exists("sovga/$chat_id")){
		$text = $GLOBALS['text'];
		if ($text == "knopuz_bot"){
			$chat_id = $GLOBALS['chat_id'];
			$token1 = $GLOBALS['token1'];
			$token2 = $GLOBALS['token2'];
			$json = json_decode(file_get_contents("https://api.knop.uz/Yuborish?dan=2:1&ga=2:$chat_id&qiymat=100&token=$token1&token2=$token2"));
			if ($json -> {'ok'}){
				send("sendMessage", ["chat_id" => $chat_id, "text" => "Sizga sovg'a sifatida 100 sum berildi"]);
				file_put_contents("sovga/$chat_id", "ok");
			}
		}
	}
}
$key1 = json_encode(['resize_keyboard' => true,	
    'keyboard' => [
        [['text' => 'Balans'],['text' => 'Hisobim']],
        [['text' => 'Pul yuborish'],['text' => 'Ism almashtirish']]
    ]
]);
$key2 = json_encode(['resize_keyboard' => true,	
    'keyboard' => [
        [['text' => 'Bekor qilish']],
    ]
]);

if ($text == "/start"){
    if (!getHisob()){
        if (yaratish()){
            send("sendMessage", ["text" => "Salom siz tizimda muvaffaqiyatli ruyxatdan o'tdingiz. Sizning hisob raqam: 2:$chat_id", "reply_markup" => $key1]);
        } else {
            send("sendMessage", ["text" => "Tizimda ruyxatdan o'tishda muammo. /start bosing yoki biz bilan bog'laning."]);
        }
    } else {
        send("sendMessage", ["text" => "Salom. Sizning hisob raqamingiz: *2:$chat_id*", "reply_markup" => $key1]);
    }
    qadamY(0);
    exit();
} elseif ($text == "Bekor qilish"){
    qadamY(0);
    send("sendMessage", ["text" => "Amaliyot bekor qilindi", "reply_markup" => $key1]);
    exit();
}
if ($qadam < 1){
	sovga();
    if ($text == 'Balans'){
        $json = json_decode(file_get_contents("https://api.knop.uz/getBalans?hisob=2:$chat_id"));
        $balans = $json -> {'balans'};
        $balans = number_format($balans, 0, '', ' ');
        send("sendMessage", ["text" => "Sizning balans: *$balans* sum."]);
    } elseif ($text == 'Hisobim'){
        $json = json_decode(file_get_contents("https://api.knop.uz/getHisob?hisob=2:$chat_id"));
        $ismi = $json -> {'nomi'};
        send("sendMessage", ["text" => "Sizning ismingiz: *$ismi* Sizning hisob raqamingiz: *2:$chat_id*"]);
    } elseif ($text == "Ism almashtirish"){
        qadamY(2);
        send("sendMessage", ["text" => "Yozmoqchi bo'lgan ismingizni kitiring."]);
    } elseif ($text == "Pul yuborish"){
        qadamY(3);
        send("sendMessage", ["text" => "Junatish uchun hisob raqmani kiriting.", "reply_markup" => $key2]);
    }
} elseif ($qadam == 2){
    if (nom($text)){
        qadamY(0);
        send("sendMessage", ["text" => "Nom o'rnatildi"]);
    } else {
        qadamY(0);
        send("sendMessage", ["text" => "Nom o'zgarmadi $text"]);
    }
} elseif ($qadam == 3){
    $json = getHisob2($text);
    if ($json -> {'ok'}){
        $tashqi_nom = $json -> {'tashqi_nomi'};
        $ichki_nom = $json -> {'nomi'};
        file_put_contents("trans/$chat_id", json_encode(['ga' => $text]));
        send("sendMessage", ["text" => "Hisobning tashqi nomi: *$tashqi_nom*
Hisobning ichki nomi: *$ichki_nom* 
O'tkazma summasini kiriting:"]);
        qadamY(4);
    } else {
        send("sendMessage", ["text" => "Hisob raqam noto'g'ri. Qayta kiritib ko'ring."]);
    }
} elseif ($qadam == 4){
    if (is_numeric($text) and $text == (floor($text)) and $text < 9999 and $text >= 1){
        $ga = json_decode(file_get_contents("trans/$chat_id")) -> {'ga'};
        $json = tranzaksiya($ga, $text);
        if ($json -> {'ok'}){
            qadamY(0);
            $id = $json -> {'id'};
            send("sendMessage", ["text" => "$ga hisob raqamiga, $text sum o'tkazildi.%0ATranzaksiya raqami: $id", "reply_markup" => $key1]);
        } else {
            qadamY(0);
            send("sendMessage", ["text" => "Pul yuborilmadi.", "reply_markup" => $key1]);
            unlink("trans/$chat_id");
        }
    } else {
        send("sendMessage", ["text" => "Notug'ri raqam: 1 dan 9999 gacha butun sonlar kiriting."]);
    }
}
?>
