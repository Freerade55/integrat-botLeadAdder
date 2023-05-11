<?php

const ROOT = __DIR__;

require ROOT . "/functions/require.php";


$update = file_get_contents("php://input");

$update = json_decode($update, true);

logs($update);

//$message = $update["message"];
//$user_id = strval($message["from"]["id"]);
//$chat_id = $message["chat"]["id"];

$message = $update["message"];
$user_id = strval($message["from"]["id"]);
$chat_id = $message["chat"]["id"];





if(!empty($message["text"])) {

    $memcached = new Memcached();
    $memcached->addServer("localhost", 11217);


    if (empty($memcached->get($user_id))) {

        $memcached->set($user_id, "start");
        sendMessage($chat_id, "Добрый день. \nВас приветствует \"Строительная компания \"Гарантия\". Для дальнейшего оформления участия в акции \"Приведи друга\" просим вас ответить на вопрос. Вы являетесь работником СК \"Гарантия\" или \"Ангажемент\"?", true);


    } else {


        if ($message["text"] == "Да") {

            $memcached->set($user_id, "friendPhone");
            $memcached->set($user_id . "_userStatus", $message["text"]);

            sendMessage($chat_id, "Ознакомьтесь с условиями:\n•Передавая контактные данные, вы автоматически соглашаетесь на обработку персональных данных СК «Гарантия» и подтверждаете, что рекомендованное лицо предоставило разрешение на передачу и обработку своих персональных данных;\n•На момент передачи контакта друга, ваш друг не обращался в Строительную компанию \"Гарантия\", либо с последнего обращения прошло более 100 дней;\n•Рекомендованный друг или знакомый заключает договор на покупку квартиры в течении 90 дней от даты передачи его контактов;\n• Выплата вознаграждения производится после оформления сделки рекомендованным лицом.");

            sendMessage($chat_id, "Укажите номер телефона друга (Пример: 79271130011)");


        } else if ($message["text"] == "Нет") {

            $memcached->set($user_id, "friendPhone");
            $memcached->set($user_id . "_userStatus", $message["text"]);
            sendMessage($chat_id, "Ознакомьтесь с условиями:\n•Передавая контактные данные, вы автоматически соглашаетесь на обработку персональных данных СК «Гарантия» и подтверждаете, что рекомендованное лицо предоставило разрешение на передачу и обработку своих персональных данных;\n•На момент передачи контакта друга, ваш друг не обращался в Строительную компанию \"Гарантия\", либо с последнего обращения прошло более 100 дней;\n•Рекомендованный друг или знакомый заключает договор на покупку квартиры в течении 90 дней от даты передачи его контактов;\n• Выплата вознаграждения производится после оформления сделки рекомендованным лицом.");

            sendMessage($chat_id, "Укажите номер телефона друга (Пример: 79271130011)");


        } else if ($message["text"] == "Заново") {





            $memcached->set($user_id, "start");
            sendMessage($chat_id, "Добрый день. \nВас приветствует \"Строительная компания \"Гарантия\". Для дальнейшего оформления участия в акции \"Приведи друга\" просим вас ответить на вопрос. Вы являетесь работником СК \"Гарантия\" или \"Ангажемент\"?", true);


        } else if ($memcached->get($user_id) == "start") {

            $memcached->set($user_id, "start");
            sendMessage($chat_id, "Добрый день. \nВас приветствует \"Строительная компания \"Гарантия\". Для дальнейшего оформления участия в акции \"Приведи друга\" просим вас ответить на вопрос. Вы являетесь работником СК \"Гарантия\" или \"Ангажемент\"?", true);

        } else {


            ////        если запрос второй
            if ($memcached->get($user_id) == "friendPhone") {


//            проверка номера
                $checkNumbRes = checkNumber($message["text"]);

//          номер не соответствует
                if ($checkNumbRes !== true) {

                    $memcached->set($user_id, "friendPhone");

                    sendMessage($chat_id, $checkNumbRes);


                } else {

                    // номер прошел проверку
                    $numberExist = searchEntity(CRM_ENTITY_CONTACT, $message["text"]);

                    if (!empty($numberExist)) {


                        $memcached->set($user_id, "friendPhone");


                        sendMessage($chat_id, "Данный номер уже зарегистрирован в системе");

                        sendMessage($chat_id, "Укажите номер телефона друга (Пример: 79271130011)");


                    } else {

                        $memcached->set($user_id . "_friendPhone", $message["text"]);

                        $memcached->set($user_id, "noNumber");
                        sendMessage($chat_id, "Укажите Фамилию и Имя друга (Пример: Иванова Екатерина)");


                    }


                }


            } else if ($memcached->get($user_id) == "noNumber") {


                $memcached->set($user_id, "phoneOwner");


                $memcached->set($user_id . "_friendName", $message["text"]);

                sendMessage($chat_id, "Укажите ВАШ номер телефона (Пример: 79271130017)");


            } else if ($memcached->get($user_id) == "phoneOwner") {

                $checkNumbRes = checkNumber($message["text"]);


                if ($checkNumbRes !== true) {
//                если номер не проходит проверку

                    $memcached->set($user_id, "phoneOwner");

                    sendMessage($chat_id, $checkNumbRes);



                } else {

                    $memcached->set($user_id."_phoneOwner", $message["text"]);

                    $memcached->set($user_id, "ownerName");

                    sendMessage($chat_id, "Укажите ВАШИ Фамилию и Имя (Пример: Зотова Мария)");

                }


            } else if($memcached->get($user_id) == "ownerName") {



                $memcached->set($user_id."_ownerName", $message["text"]);


                $friendPhone = $memcached->get($user_id."_friendPhone");
                $friendName = $memcached->get($user_id."_friendName");
                $phoneOwner = $memcached->get($user_id."_phoneOwner");
                $ownerName = $memcached->get($user_id."_ownerName");
                $userStatus = $memcached->get($user_id."_userStatus");





                $contactAddRes = addContact($friendName, $friendPhone);

                if(!empty($contactAddRes["_embedded"]["contacts"][0]["id"])) {

                    $contactId = $contactAddRes["_embedded"]["contacts"][0]["id"];


                    $leadAddRes = addLead($contactId);

                    if(!empty($leadAddRes["_embedded"]["leads"][0]["id"])) {


                        addTask($leadAddRes["_embedded"]["leads"][0]["id"]);
                        addNote("leads", $leadAddRes["_embedded"]["leads"][0]["id"], $phoneOwner, $ownerName, $userStatus);




                        $memcached->set($user_id, "friendPhone");


                        sendMessage($chat_id, "Заявка успешно отправлена. С вами и вашим другом в ближайшее время свяжется менеджер СК \"Гарантия\". При возникновении дополнительных вопросов, обращайтесь в офис продаж по тел.: +7 (800) 700-90-93");

                        sendMessage($chat_id, "Окружайте себя близкими и рекомендуйте надежного застройщика. Используйте этот макет для рассылки своим друзьям.");

                        sendFile($chat_id);



                        sendMessage($chat_id, "Укажите номер телефона друга (Пример: 79271130011)");


                    } else {
                        $memcached->set($user_id, "friendPhone");


                        sendMessage($chat_id, "Что-то пошло не так");

                        sendMessage($chat_id, "Укажите Фамилию и Имя друга (Пример: Иванова Екатерина)");




                    }


                } else {
                    $memcached->set($user_id, "friendPhone");


                    sendMessage($chat_id, "Что-то пошло не так");

                    sendMessage($chat_id, "Укажите Фамилию и Имя друга (Пример: Иванова Екатерина)");


                }




            }


        }

    }

}







