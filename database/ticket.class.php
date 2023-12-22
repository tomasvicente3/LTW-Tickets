<?php

declare(strict_types=1);

require_once(__DIR__ . '/../database/department.class.php');
require_once(__DIR__ . '/../database/agent.class.php');
require_once(__DIR__ . '/../database/client.class.php');
require_once(__DIR__ . '/../database/date.class.php');
require_once(__DIR__ . '/../database/change.class.php');
require_once(__DIR__ . '/../database/faqEntry.class.php');
require_once(__DIR__ . '/../database/message.class.php');

//Higest priority, open and most recent first
function sortTicket(Ticket $a, Ticket $b): int
{
    if ($a->priority !== $b->priority) {
        $translation = ['very high' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];
        return $translation[$a->priority] > $translation[$b->priority] ? -1 : 1;
    } else if ($a->status !== $a->status) {
        $translation = ['open' => 3, 'assigned' => 2, 'closed' => 1];
        return $translation[$a->status] > $translation[$b->status] ? -1 : 1;
    }
    return - (strcmp($a->date->getFullDate(true), $b->date->getFullDate(true)));
}

//most recent first
function sortTicketByDate(Ticket $a, Ticket $b): int
{
    return - (strcmp($a->date->getFullDate(true), $b->date->getFullDate(true)));
}

//higher priority, then most recent first
function sortTicketByPriority(Ticket $a, Ticket $b): int
{
    if ($a->priority !== $b->priority) {
        $translation = ['very high' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];
        return $translation[$a->priority] > $translation[$b->priority] ? -1 : 1;
    }
    return - (strcmp($a->date->getFullDate(true), $b->date->getFullDate(true)));
}

function sortTicketByStatus(Ticket $a, Ticket $b): int
{
    if ($a->status !== $a->status) {
        $translation = ['open' => 3, 'assigned' => 2, 'closed' => 1];
        return $translation[$a->status] > $translation[$b->status] ? -1 : 1;
    }
    return - (strcmp($a->date->getFullDate(true), $b->date->getFullDate(true)));
}


class Ticket
{
    public int $id;
    public Date $date;
    public string $title;
    public string $description;
    public array $hashtags;
    public string $status;
    public string $priority;
    public ?Department $department;
    public ?Agent $agent;
    public Client $client;
    public ?FAQEntry $faqAnswer;
    public ?string $answer;

    public array $messages;
    public array $changes;

    public function __construct(
        int $id,
        Date $date,
        string $title,
        string $description,
        array $hashtags,
        string $status,
        string $priority,
        ?Department $department,
        ?Agent $agent,
        Client $client,
        ?string $answer = null,
        ?FAQEntry $faqAnswer = null
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->title = $title;
        $this->description = $description;
        $this->hashtags = $hashtags;
        $this->status = $status;
        $this->priority = $priority;
        $this->department = $department;
        $this->agent = $agent;
        $this->client = $client;
        $this->answer = $answer;
        $this->faqAnswer = $faqAnswer;
    }

    public function fillMessages(PDO $db): void
    {
        $this->messages = array();
        $stmt = $db->prepare('
                SELECT *
                FROM Message
                WHERE idTicket = ?
            ');
        $stmt->execute(array($this->id));

        $raw = $stmt->fetchAll();

        foreach ($raw as $message) {
            $curr = new Message(
                intval($message['id']),
                Client::extractClientWithId($db, intval($message['idClient'])),
                $message['content'],
                new Date($message['date'])
            );
            array_push($this->messages, $curr);
            usort($this->messages, function (Message $a, Message $b) {
                return (strcmp($a->date->getFullDate(true), $b->date->getFullDate(true)));
            });
        }
    }


    public function fillChanges(PDO $db): void
    {
        $this->changes = array();
        $stmt = $db->prepare('
                SELECT *
                FROM Change
                WHERE ticket = ?
            ');
        $stmt->execute(array($this->id));
        $raw = $stmt->fetchAll();


        foreach ($raw as $change) {
            $author = Agent::extractAgentWithId($db, intval($change['author']));
            if (!$author) {
                $author = Client::extractClientWithId($db, intval($change['author']));
            }

            $curr = new Change(
                intval($change['id']),
                new Date($change['date']),
                $change['type'],
                $change['oldValue'],
                $change['newValue'],
                $author,
                intval($change['ticket'])
            );
            array_push($this->changes, $curr);
            usort($this->changes, function (Change $a, Change $b) {
                return - (strcmp($a->date->getFullDate(true), $b->date->getFullDate(true)));
            });
        }
    }


    static function extractTicketWithId(PDO $db, int $id): ?Ticket
    {
        $stmt = $db->prepare('
                SELECT *
                FROM Ticket
                WHERE id = ?
            ');
        $stmt->execute(array($id));
        $ticket = $stmt->fetch();

        if ($ticket) {
            $ticket = new Ticket(
                intval($ticket['id']),
                new Date($ticket['date']),
                $ticket['title'],
                $ticket['description'],
                $ticket['hashtag'] ? explode(" ", $ticket['hashtag']) : [],
                $ticket['status'],
                $ticket['priority'],
                Department::extractDepartmentWithId($db, intval($ticket['department'])),
                Agent::extractAgentWithId($db, intval($ticket['agent'])),
                Client::extractClientWithId($db, intval($ticket['client'])),
                $ticket['answer'],
                FAQEntry::extractFAQWithId($db, intval($ticket['faqAnswer']))
            );
            $ticket->fillMessages($db);
            $ticket->fillChanges($db);
            return $ticket;
        }
        return null;
    }

    static function extractTicketsWithClient(PDO $db, int $clientId): array
    {
        $result = array();
        $stmt = $db->prepare('
                SELECT *
                FROM Ticket
                WHERE client = ?
            ');

        $stmt->execute(array($clientId));

        $raw = $stmt->fetchAll();

        foreach ($raw as $ticket) {
            $curr = new Ticket(
                intval($ticket['id']),
                new Date($ticket['date']),
                $ticket['title'],
                $ticket['description'],
                $ticket['hashtag'] ? explode(" ", $ticket['hashtag']) : [],
                $ticket['status'],
                $ticket['priority'],
                Department::extractDepartmentWithId($db, intval($ticket['department'])),
                Agent::extractAgentWithId($db, intval($ticket['agent'])),
                Client::extractClientWithId($db, intval($ticket['client'])),
                $ticket['answer'],
                FAQEntry::extractFAQWithId($db, intval($ticket['faqAnswer']))
            );
            $curr->fillMessages($db);
            $curr->fillChanges($db);
            array_push($result, $curr);
            usort($result, 'sortTicket');
        }
        return $result;
    }


    static function extractTicketsWithDepartment(PDO $db, int $departmentId): array
    {
        $result = array();
        $stmt = $db->prepare('
                SELECT *
                FROM Ticket
                WHERE department = ?
            ');
        $stmt->execute(array($departmentId));

        $raw = $stmt->fetchAll();

        foreach ($raw as $ticket) {
            $curr = new Ticket(
                intval($ticket['id']),
                new Date($ticket['date']),
                $ticket['title'],
                $ticket['description'],
                $ticket['hashtag'] ? explode(" ", $ticket['hashtag']) : [],
                $ticket['status'],
                $ticket['priority'],
                Department::extractDepartmentWithId($db, intval($ticket['department'])),
                Agent::extractAgentWithId($db, intval($ticket['agent'])),
                Client::extractClientWithId($db, intval($ticket['client'])),
                $ticket['answer'],
                FAQEntry::extractFAQWithId($db, intval($ticket['faqAnswer']))
            );
            $curr->fillMessages($db);
            $curr->fillChanges($db);
            array_push($result, $curr);
        }
        usort($result, 'sortTicket');
        return $result;
    }

    static function extractTicketsWithAgent(PDO $db, int $agentId): array
    {
        $result = array();
        $stmt = $db->prepare('
                SELECT *
                FROM Ticket
                WHERE agent = ?
            ');
        $stmt->execute(array($agentId));

        $raw = $stmt->fetchAll();

        foreach ($raw as $ticket) {
            $curr = new Ticket(
                intval($ticket['id']),
                new Date($ticket['date']),
                $ticket['title'],
                $ticket['description'],
                $ticket['hashtag'] ? explode(" ", $ticket['hashtag']) : [],
                $ticket['status'],
                $ticket['priority'],
                Department::extractDepartmentWithId($db, intval($ticket['department'])),
                Agent::extractAgentWithId($db, intval($ticket['agent'])),
                Client::extractClientWithId($db, intval($ticket['client'])),
                $ticket['answer'],
                FAQEntry::extractFAQWithId($db, intval($ticket['faqAnswer']))
            );
            $curr->fillMessages($db);
            $curr->fillChanges($db);
            array_push($result, $curr);
        }
        usort($result, 'sortTicket');
        return $result;
    }

    static function filterByStatus(array $tickets, string $status): array
    {
        $result = array();
        if (!in_array($status, ['open', 'assigned', 'closed'])) return $result;
        foreach ($tickets as $ticket) {
            if ($ticket->status === $status) array_push($result, $ticket);
        }
        usort($result, 'sortTicket');
        return $result;
    }

    static function filterByPriority(array $tickets, string $priority): array
    {
        $result = array();
        if (!in_array($priority, ['low', 'medium', 'high', 'very high'])) return $result;
        foreach ($tickets as $ticket) {
            if ($ticket->priority === $priority) array_push($result, $ticket);
        }
        usort($result, 'sortTicket');
        return $result;
    }

    static function filterByHashtag(array $tickets, string $hashtag): array
    {
        $result = array();
        foreach ($tickets as $ticket) {
            foreach ($ticket->hashtags as $curr) {
                if ($curr === $hashtag) {
                    array_push($result, $ticket);
                    break;
                }
            }
        }
        usort($result, 'sortTicket');
        return $result;
    }

    static function filterByDepartment(array $tickets, string $department): array
    {
        $result = array();
        foreach ($tickets as $ticket) {
            if ($ticket->department->name === $department) array_push($result, $ticket);
        }
        usort($result, 'sortTicket');
        return $result;
    }

    static function filterByAgent(array $tickets, string $agent): array
    {
        $result = array();
        foreach ($tickets as $ticket) {
            if ($ticket->agent == null) continue;
            if ($ticket->agent->username === $agent) array_push($result, $ticket);
        }
        usort($result, 'sortTicket');
        return $result;
    }

    static function createTicket(PDO $db, string $title, string $description, array $hashtags, string $status, string $priority, Client $client, ?Department $department = null, ?Agent $agent = null): ?Ticket
    {
        if (!in_array($status, array("open", "assigned", "closed"))) return null;
        if (!in_array($priority, array("low", "medium", "high", "very high"))) return null;

        $stmt = $db->prepare("
            INSERT INTO Ticket(title, date, description, hashtag, status, priority, department, agent, client)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $dbHashtags = implode(" ", $hashtags);
        if (empty($dbHashtags)) $dbHashtags = null;

        $stmt->execute(array($title, date("Y-m-d H:i"), $description, $dbHashtags, $status, $priority, $department ? $department->id : NULL, $agent ? $agent->id : NULL, $client ? $client->id : NULL));
        return Ticket::extractTicketsWithClient($db, $client->id)[0];
    }

    static function getAllHashtags(PDO $db): array
    {
        $stmt = $db->prepare("SELECT hashtag FROM Ticket");
        $stmt->execute();
        $raw = $stmt->fetchAll();

        $hashtags = [];
        foreach ($raw as $array) {
            if ($array['hashtag']) $hashtags = array_merge($hashtags, explode(" ", $array['hashtag']));
        }
        $hashtags = array_unique($hashtags, SORT_REGULAR);
        return array_values($hashtags);
    }

    public function addAnswer(PDO $db, ?string $answer = null, ?FAQEntry $faqAnswer = null): void
    {

        $stmt = $db->prepare("
            UPDATE Ticket SET answer= ?, faqAnswer = ? WHERE id = ?
        ");

        $stmt->execute(array($answer, $faqAnswer->id, $this->id));
    }

    static public function getAllTickets(PDO $db): array
    {
        $result = array();
        $stmt = $db->prepare('
                SELECT *
                FROM Ticket
            ');
        $stmt->execute();

        $raw = $stmt->fetchAll();

        foreach ($raw as $ticket) {
            $curr = new Ticket(
                intval($ticket['id']),
                new Date($ticket['date']),
                $ticket['title'],
                $ticket['description'],
                $ticket['hashtag'] ? explode(" ", $ticket['hashtag']) : [],
                $ticket['status'],
                $ticket['priority'],
                Department::extractDepartmentWithId($db, intval($ticket['department'])),
                Agent::extractAgentWithId($db, intval($ticket['agent'])),
                Client::extractClientWithId($db, intval($ticket['client'])),
                $ticket['answer'],
                FAQEntry::extractFAQWithId($db, intval($ticket['faqAnswer']))
            );
            $curr->fillMessages($db);
            $curr->fillChanges($db);
            array_push($result, $curr);
        }
        usort($result, 'sortTicket');
        return $result;
    }
}
