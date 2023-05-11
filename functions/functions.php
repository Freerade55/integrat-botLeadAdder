<?php
function checkNumber(string $number, $isOwner = null) {


    if (preg_match('/^\d+$/', $number)) {



        if (mb_strlen($number) == 11) {


            return true;

        } else {

            if(isset($isOwner)) {
                return "Проверьте правильность введенного номера или укажите другой номер ВАШ номер телефона (Пример: 79271130011)";

            } else {
                return "Проверьте правильность введенного номера или укажите другой номер телефона друга (Пример: 79271130011)";
            }



        }



    } else {

        if(isset($isOwner)) {
            return "Проверьте правильность введенного номера или укажите другой номер ВАШ номер телефона (Пример: 79271130011)";

        } else {
            return "Проверьте правильность введенного номера или укажите другой номер телефона друга (Пример: 79271130011)";
        }

    }
















}





function sendMessage($chat_id, $text, $startInit = null) {

    // Определяем массив с данными для создания кнопок

    if(isset($startInit)) {


        $reply_markup = [

            "keyboard" => [
                [
                    ["text" => "Да"],

                    ["text" => "Нет"]

                ],

            ],
            "resize_keyboard" => true,

        ];






    } else {

        $reply_markup = [
            "keyboard" => [
                [
                    ["text" => "Заново"]


                ],

            ],
            "resize_keyboard" => true,

        ];



        if($text == "Заявка успешно отправлена") {
            $text = "<b>".$text."</b>";

        }





    }



// Преобразуем массив в JSON-строку
    $encoded_markup = json_encode($reply_markup);


// Отправляем запрос к API Telegram Bot для отправки сообщения с кнопками
    $url = "https://api.telegram.org/bot".BOT_TOKEN."/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML',
        'reply_markup' => $encoded_markup
    ];
    $options = [
        'http' => [
            'method'  => 'POST',
            'content' => http_build_query($data),
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n"
        ]
    ];
    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);



}






function sendFile($chat_id) {


    $token = BOT_TOKEN;

    $arrayQuery = [
        "chat_id" => $chat_id,
        "media" => json_encode([
            ["type" => "document", "media" => "attach://photo"]
        ]),
        "photo" => new CURLFile(ROOT . "/photo.jpg")
    ];


    $ch = curl_init('https://api.telegram.org/bot'. $token .'/sendMediaGroup');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_exec($ch);
    curl_close($ch);




}





