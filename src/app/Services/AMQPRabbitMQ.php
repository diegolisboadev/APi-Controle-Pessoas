<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class AMQPRabbitMQ {

    public function __construct(){}

    /**
     *
     */
    public function sendMessage(string $mensagem) {
        $canal = $this->getConexaoRabbit()->channel();

        $canal->queue_declare('controlepessoas', false, false, false, false);

        $msg = new AMQPMessage($mensagem); // ['delivery_mode' => 2]
        $canal->basic_publish($msg, '', 'controlepessoas');

        $canal->close();
        $this->getConexaoRabbit()->close();
    }

    /**
     *
     */
    private function getConexaoRabbit() {
        return new AMQPStreamConnection(
            env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD')
        );
    }

}
