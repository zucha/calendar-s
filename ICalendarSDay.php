<?php
/**
 * instance for CalendarSWidget day
 * @author uldis@sit.lv
 */

interface ICalendarSDay
{
    /**
     * constructor
     * @param DateTime $dateTime
     * @return void
     */
    public function __construct ($dateTime);
    
    /**
     * get html block for the day
     * @return string
     */
    public function getHtml();
    
    /**
     * check if html cell need extra class
     * @return boolean
     */
    public function isExtra ();
    
    /**
     * @see date() for date formation
     * @param string $format
     * @return string
     */
    public function format ($format = "Y-m-d");
}