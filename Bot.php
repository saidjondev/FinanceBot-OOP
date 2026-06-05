<?php

class Bot {
    private $token;

    public function __construct($token) {
        $this->token = $token;
    }

    public function sendMessage($chatId, $text) {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        file_get_contents($url, false, stream_context_create($options));
    }

    public function getUpdates($offset) {
        $url = "https://api.telegram.org/bot{$this->token}/getUpdates?offset={$offset}&timeout=30";
        return json_decode(file_get_contents($url), true);
    }
}