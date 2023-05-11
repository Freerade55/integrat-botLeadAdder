<?php
require ROOT . "/logs/logs.php";
require ROOT . "/functions/display-errors.php";


require ROOT . "/vendor/autoload.php";
require ROOT . "/functions/connectToCrm.php";
require ROOT . "/functions/refreshToken.php";
require ROOT . "/functions/crmMethods.php";
require ROOT . "/functions/functions.php";


const
BOT_TOKEN = "6186364177:AAFre6Z1mm1ImoB_jy9SAqPwP9-h7D0bljM",
CRM_ENTITY_LEAD = "lead",
CRM_ENTITY_CONTACT = "contact",
CRM_ENTITY_COMPANY = "company",
API_URL = "https://api.telegram.org/bot".BOT_TOKEN."/",
METHOD_POST = "POST",
CRM_RESPONSIBLE_ID = 6274669,
CRM_PIPELINE_ID = 3444277,
CRM_TAG_ID = 587252,
METHOD_PATCH = "PATCH";

$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();


