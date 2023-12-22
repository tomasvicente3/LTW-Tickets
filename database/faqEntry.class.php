<?php
    declare(strict_types=1);

require_once(__DIR__ . '/../database/date.class.php');
require_once(__DIR__ . '/../database/agent.class.php');

    class FAQEntry{
        public int $id;
        public string $title;
        public string $content;
        public Date $date;
        public Agent $agent;


        public function __construct(int $id, string $title, string $content, Date $date, Agent $agent){
            $this->id = $id;
            $this->title = $title;
            $this->content = $content;
            $this->date = $date;
            $this->agent = $agent;
        }

        static function createFAQEntry(PDO $db, string $title, string $content, Agent $agent) : bool{
            date_default_timezone_set("Europe/Lisbon");
            $stmt = $db->prepare("
                INSERT INTO FAQEntry (title, content, date, agent)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute(array($title, $content, date("Y-m-d H:i"), $agent->id));
            return true;
        }

        static function extractFAQWithId(PDO $db, ?int $id) : ?FAQEntry {
            if (!$id) return null;
            
            $stmt = $db->prepare('
                SELECT *
                FROM FAQEntry
                WHERE id = ?
            ');
            $stmt->execute(array($id));
            $faq = $stmt->fetch();

            if ($faq){
                return new FAQEntry(
                    intval($faq['id']),
                    $faq['title'],
                    $faq['content'],
                    new Date($faq['date']),
                    Agent::extractAgentWithId($db, intval($faq['agent']))
                );
            }
            return null;
        }

        static function extractFAQWithTitle(PDO $db, string $title): ?FAQEntry{
            $stmt = $db->prepare("
                    SELECT *
                    FROM FAQEntry
                    WHERE title = ?
                ");
            $stmt->execute(array($title));
            $faq = $stmt->fetch();
    
            if ($faq) {
                return new FAQEntry(
                    intval($faq['id']),
                    $faq['title'],
                    $faq['content'],
                    new Date($faq['date']),
                    Agent::extractAgentWithId($db, intval($faq['agent']))
                );
            }
        }

        static function extractFAQs(PDO $db) : array{
            $stmt = $db->prepare("
                SELECT *
                FROM FAQEntry
                LIMIT 50
            ");
            $stmt->execute();
            $faqs = $stmt->fetchAll();

            $result = array();

            foreach ($faqs as $faq){
                $curr = new FAQEntry(
                intval($faq['id']),
                $faq['title'],
                $faq['content'],
                new Date($faq['date']),
                Agent::extractAgentWithId($db, intval($faq['agent']))
                );
                array_push($result, $curr);
            }
            usort($result, function(FAQEntry $a, FAQEntry $b){return strcmp($a->date->getFullDate(true), $b->date->getFullDate(true));});

            return $result;
        }

        public function deleteFAQ(PDO $db) : void {
            $stmt = $db->prepare("
                DELETE FROM FAQEntry WHERE id = ?
            ");
            $stmt->execute(array($this->id));
        }

        static public function getALLFAQTitles(PDO $db): array
        {
            $stmt = $db->prepare("
                    SELECT title
                    FROM FAQEntry
                ");
            $stmt->execute();
            $faqs = $stmt->fetchAll();

            $result = array();
            foreach ($faqs as $array) {
                array_push($result, $array['title']);
            }
            sort($result);
            return $result;
        }
    }
?>