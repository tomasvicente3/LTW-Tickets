<?php

declare(strict_types=1);

function generateRandomToken()
{
  return bin2hex(openssl_random_pseudo_bytes(32));
}

class Session
{
  private array $messages;

  public function __construct()
  {
    //Commented because on occasion login does not work with this
    /*session_set_cookie_params([
      'lifetime' => 86400,
      'path' => '/',
      'domain' => $_SERVER['HTTP_HOST'],
      'secure' => true,
      'httponly' => true,
    ]);*/
    session_start();

    if (!isset($_SESSION['csrf'])) {
      $_SESSION['csrf'] = generateRandomToken();
    }

    $this->messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : array();
    unset($_SESSION['messages']);
  }

  public function isLoggedIn(): bool
  {
    return isset($_SESSION['id']);
  }

  public function logout()
  {
    session_destroy();
  }

  public function getId(): ?int
  {
    return isset($_SESSION['id']) ? $_SESSION['id'] : null;
  }

  public function getName(): ?string
  {
    return isset($_SESSION['name']) ? $_SESSION['name'] : null;
  }

  private function setId(int $id)
  {
    $_SESSION['id'] = $id;
  }

  private function setName(string $name)
  {
    $_SESSION['name'] = $name;
  }

  public function addMessage(string $type, string $text)
  {
    $_SESSION['messages'][] = array('type' => $type, 'text' => $text);
  }

  public function getMessages(): ?array
  {
    return $this->messages;
  }

  private function setClearance(string $clearance)
  {
    $_SESSION['clearance'] = $clearance;
  }

  public function getClearance(): ?string
  {
    return $_SESSION['clearance'];
  }

  public function updateSessionOnAgent(Agent $client)
  {
    $this->setId($client->id);
    $this->setName($client->getFullName());
    if ($client->isAdmin) {
      $this->setClearance("admin");
    } else {
      $this->setClearance("agent");
    }
  }

  public function updateSessionOnClient(Client $client)
  {
    $this->setId($client->id);
    $this->setName($client->getFullName());
    $this->setClearance("client");
  }

  public function updateSession(Client $client)
  {
    $this->setId($client->id);
    $this->setName($client->getFullName());
  }

  public function setArray(array $array, string $arrayName, string $flag)
  {
    $_SESSION[$arrayName] = $array;
    $_SESSION[$flag] = true;
  }

  public function removeArray(string $arrayName, string $flag)
  {
    unset($_SESSION[$arrayName]);
    unset($_SESSION[$flag]);
  }
}
