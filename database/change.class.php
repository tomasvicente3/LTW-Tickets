<?php

declare(strict_types=1);

class Change
{
    public int $id;
    public Date $date;
    public string $type;
    public ?string $oldValue;
    public ?string $newValue;
    public Client $author;
    public int $ticket;

    public function __construct(int $id, Date $date, string $type, ?string $oldValue, ?string $newValue, Client $author, int $ticket)
    {
        $this->id = $id;
        $this->date = $date;
        $this->type = $type;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
        $this->author = $author;
        $this->ticket = $ticket;
    }

    //Documents a change in a ticket and executes it
    static function createChange(PDO $db, string $type, ?string $newValue, Client $author, Ticket $ticket): bool
    {
        $stmt = $db->prepare('
            INSERT INTO Change (date, type, oldValue, newValue, author, ticket) 
            VALUES (?, ?, ?, ?, ?, ?)
            ');

        switch ($type) {
            case "agent":
                $oldValue = ($ticket->agent ? $ticket->agent->id : null);
                $ticket->agent = Agent::extractAgentWithId($db, intval($newValue));
                $update = $db->prepare("UPDATE Ticket SET agent = ? WHERE id = ?");
                $update->execute(array(intval($newValue), $ticket->id));
                break;

            case "department":
                $oldValue = ($ticket->department ? $ticket->department->id : null);
                $ticket->department = Department::extractDepartmentWithId($db, intval($newValue));
                $update = $db->prepare("UPDATE Ticket SET department = ? WHERE id = ?");
                $update->execute(array(intval($newValue), $ticket->id));
                break;

            case "status":
                $oldValue = $ticket->status;
                if (!in_array($newValue, array("open", "assigned", "closed"))) return false;
                $ticket->status = $newValue;
                $update = $db->prepare("UPDATE Ticket SET status = ? WHERE id = ?");
                $update->execute(array($newValue, $ticket->id));
                break;

            case "hashtag":
                $oldValue = implode(" ", $ticket->hashtags);
                $ticket->hashtags = explode(" ", $newValue);
                $update = $db->prepare("UPDATE Ticket SET hashtag = ? WHERE id = ?");
                $update->execute(array($newValue, $ticket->id));
                break;

            case "priority":
                $oldValue = $ticket->priority;
                if (!in_array($newValue, array("low", "medium", "high", "very high"))) return false;
                $ticket->priority = $newValue;
                $update = $db->prepare("UPDATE Ticket SET priority = ? WHERE id = ?");
                $update->execute(array($newValue, $ticket->id));
                break;

            case "description":
                $oldValue = $ticket->description;
                $ticket->description = $newValue;
                $update = $db->prepare("UPDATE Ticket SET description = ? where id = ?");
                $update->execute(array($newValue, $ticket->id));
                break;

            case "title":
                $oldValue = $ticket->title;
                $ticket->title = $newValue;
                $update = $db->prepare("UPDATE Ticket SET title = ? where id = ?");
                $update->execute(array($newValue, $ticket->id));
                break;

            default:
                return false;
        }

        date_default_timezone_set("Europe/Lisbon");
        $stmt->execute(array(date("Y-m-d H:i"), $type, $oldValue, $newValue, $author->id, $ticket->id));
        $ticket->fillChanges($db);
        return true;
    }
}
