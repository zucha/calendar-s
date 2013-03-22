<?php
class CalendarSDayEmpty implements ICalendarSDay
{
    protected $_dateTime;
    
    public function __construct ($dateTime)
    {
        $this->_dateTime = $dateTime;
    }
    
    public function getHtml ()
    {
        return "";
    }
    
    public function isExtra ()
    {
        return false;
    }
    
    public function format ($format = "Y-m-d")
    {
        return $this->_dateTime->format ($format);
    }
}