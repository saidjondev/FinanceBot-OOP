<?php

require_once 'database.php';
require_once 'Finance.php';
require_once 'Bot.php';
require_once 'User.php';

$token = "8859260442:AAFsBbf5YuFofc3CNarsseYQ_DdAfRf9ADI";

// 1. Dasturda faqat shu yerda bitta va yagona kabel tortiladi (Singleton)
$db = Database::getInstance('', '', '', '', '');

// 2. Qolgan xodimlar shu yagona ulanishni ishlatadi
$finance = new Finance($db);
$userModel = new User($db);
$bot = new Bot($token);

echo "Bot ishlab ketdi!\n";

$lastUpdateId = 0;

while (true) {
    $updates = $bot->getUpdates($lastUpdateId);

    if (isset($updates['result'])) {
        foreach ($updates['result'] as $update) {
            $lastUpdateId = $update['update_id'] + 1;

            if (isset($update['message']['text'])) {
                $chatId = $update['message']['chat']['id'];
                $text = trim($update['message']['text']);
                
                $firstName = $update['message']['from']['first_name'] ?? 'Mijoz';

                // Mijoz kelishi bilan darhol pasport stoli (User.php) ishlaydi
                $userModel->registerIfNotExists($chatId, $firstName);

                if ($text === '/start') {
                    $bot->sendMessage($chatId, "Salom <b>{$firstName}!</b> \nDaromadni + bilan, xarajatni - bilan yozing.\nMisol: <b>+ 500000 oylik</b>");
                    continue; 
                }

                if ($text === '/balans') {
                    $balance = $finance->getBalance($chatId);
                    $bot->sendMessage($chatId, "💰 Hozirgi qoldiq: <b>{$balance} so'm</b>");
                    continue;
                }

                if (strpos($text, '+') === 0 || strpos($text, '-') === 0) {
                    $sign = substr($text, 0, 1); 
                    $cleanText = trim(substr($text, 1)); 
                    $parts = explode(' ', $cleanText, 2);

                    if (count($parts) === 2 && is_numeric($parts[0])) {
                        $amount = (float)$parts[0];
                        $category = $parts[1];
                        
                        $type = ($sign === '+') ? 'daromad' : 'xarajat';

                        $finance->addTransaction($chatId, $amount, $category, $type);
                        
                        $bot->sendMessage($chatId, "✅ <b>{$type}</b> saqlandi: {$amount} so'm");
                    } else {
                        $bot->sendMessage($chatId, "❌ Xato format.");
                    }
                }
            }
        }
    }
    sleep(1);
}