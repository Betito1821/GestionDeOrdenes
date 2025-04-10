<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    public function onOpen(ConnectionInterface $conn) {
        echo "Nueva conexión: ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Mensaje recibido: {$msg}\n";
    }

    public function onClose(ConnectionInterface $conn) {
        echo "Conexión cerrada: ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$app = new Ratchet\App('localhost', 3000); // Cambia el puerto aquí
$app->route('/chat', new Chat, ['*']);
$app->run();
