<?php

declare(strict_types=1);

class Date
{
    private string $year;
    private string $month;
    private string $day;

    private string $hour;
    private string $minute;

    public function __construct(string $string = "")
    {
        if ($string == "") $string = date("Y-m-d H:i");

        $this->year = substr($string, 0, 4);
        $this->month = substr($string, 5, 2);
        $this->day = substr($string, 8, 2);

        $this->hour = strval(substr($string, 11, 2));
        $this->minute = strval(substr($string, 14, 2));
    }

    public function getTime()
    {
        return $this->hour . ":" . $this->minute;
    }

    public function getDate(bool $sort)
    {
        return $sort ? $this->year . '-' . $this->month . '-' . $this->day : $this->day . '-' . $this->month . '-' . $this->year;
    }

    public function getFullDate(bool $sort)
    {
        return $this->getDate($sort) . ' ' . $this->getTime();
    }
}
