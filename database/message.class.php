<?php
    declare(strict_types=1);

    class Message{
        public int $id;
        public Client $client;
        public string $content;
        public Date $date;

        public function __construct(int $id, Client $client, string $content, Date $date){
            $this->id = $id;
            $this->client = $client;
            $this->content = $content;
            $this->date = $date;
        }

        static function createMessage(PDO $db, Ticket $ticket, string $content, int $clientId) : void {
            date_default_timezone_set("Europe/Lisbon");

            $stmt = $db->prepare('
                INSERT INTO Message (idClient, idTicket, content, date)
                VALUES (?, ?, ?, ?)
            ');
            $stmt->execute(array($clientId, $ticket->id, $content, date("Y-m-d H:i")));

            $ticket->fillMessages($db);
        }

    }

?>