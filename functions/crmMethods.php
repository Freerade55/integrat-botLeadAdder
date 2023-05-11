<?php


//  Выводит по id сущность, можно передать любую. Сделку, компанию и тд
function getEntity(string $entity_type, int $id): array
{
    switch ($entity_type) {
        case CRM_ENTITY_CONTACT:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts/$id?with=leads";
            break;
        case CRM_ENTITY_LEAD:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads/$id?with=contacts";
            break;
        case CRM_ENTITY_COMPANY:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/companies/$id?with=contacts";
            break;
    }


    $result = json_decode(connect($link), true);

    if (empty($result)) {
        return [];
    } else {
        return $result;
    }


}





//  Ищет сущность по строке, можно передать любую. Сделку, компанию и тд.
function searchEntity(string $entity_type, string $search): array
{


    switch ($entity_type) {
        case CRM_ENTITY_CONTACT:
            $query = [
                "with" => "leads",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts?" . http_build_query($query);
            break;
        case CRM_ENTITY_LEAD:
            $query = [
                "with" => "contacts",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads?" . http_build_query($query);
            break;
        case CRM_ENTITY_COMPANY:
            $query = [
                "with" => "contacts",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/companies?" . http_build_query($query);
            break;
    }


    $result = json_decode(connect($link), true);

    if (empty($result)) {
        return [];
    } else {
        return $result;
    }

}





// добавление контакта
function addContact(string $contactName, string $phone) {

    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts";



    $queryData = array(

        [

            "name" => $contactName,

            "responsible_user_id" => CRM_RESPONSIBLE_ID,

            "custom_fields_values" => [
                [
                    "field_id" => 249109,
                    "values" => [
                        [
                            "value" => $phone,
                            "enum_id" => 484141
                        ]
                    ]
                ]

            ]



        ]



    );




    return json_decode(connect($link, METHOD_POST, $queryData), true);




}










// добавление сделки
function addLead(int $contact_Id) {

    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads";

    $queryData = array(

        [

            "name" => "Сделка по акции \"Приведи друга\"",

            "responsible_user_id" => CRM_RESPONSIBLE_ID,
            "pipeline_id" => CRM_PIPELINE_ID,

            "_embedded" => [

                "contacts" => [
                    [
                        "id" => $contact_Id
                    ]
                ],


                "tags" => [

                    [
                        "id" => CRM_TAG_ID
                    ]

                ]




            ],
            "custom_fields_values" => [
                [
                    "field_id" => 685149,
                    "values" => [
                        [
                            "enum_id" => 1171668
                        ]
                    ]
                ]

            ]



            ]




    );






    return json_decode(connect($link, METHOD_POST, $queryData), true);




}




// добавление задачи для компаний
function addTask(int $leadId)
{



    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/tasks";


    $now = time();
    $newTime = $now + (15 * 60);


    $queryData = array(
        [
            "text" => "связаться с клиентом",
            "entity_id" => $leadId,
            "complete_till" => $newTime,
            "entity_type" => "leads",
            "responsible_user_id" => CRM_RESPONSIBLE_ID

        ]
    );
    connect($link, METHOD_POST, $queryData);

}










function addNote(string $common, int $entity_id, string $ownerPhone, string $ownerName, string $userStatus) {

    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/$common/$entity_id/notes";

    if($userStatus == "Да") {

        $userStatus = "Сотрудник";
    } else {
        $userStatus = "Лид";
    }


    $queryData = array(

        [

            "note_type" => "common",
            "params" => [

                "text" => "Сделка создана по программе \"Приведи друга\" \n \n
                    Вознаграждение для: \n \n
                    Фамилия и имя - $ownerName ($userStatus)\n \n
                    Номер телефона - $ownerPhone \n \n"

            ]


        ]




    );


    return json_decode(connect($link, METHOD_POST, $queryData), true);










}






