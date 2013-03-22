<?php
class CalendarSWidget extends CWidget
{
    /**
     * month
     * @see init()
     * @var integer
     */
    public $month;
    
    /**
     * year
     * @see init()
     * @var integer
     */
    public $year;
    
    /**
     * defines html table class
     * @var string
     */
    public $htmlClass = "calendar";
    
    /**
     * format of the date
     * see for formats date()
     * @var string
     */
    public $format = "Y-m-d";
    
    /**
     * current day
     * @var ICalendarSDay
     */
    private $_current;
    
    /**
     * Week starts with Mon or Sun
     * @see init()
     * @var string
     */
    public $weekStart = "Mon";
    
    /**
     * name of day when week ends
     * Sat or Sun
     * @var string
     */
    private $_weekEnd;
    
    /**
     * format for the header cell day names:
     * - wide 
     * - abbreviated 
     * - narrow
     * - none
     * these values are taken from locale files (except none:)
     * @var string
     */
    public $headerFormat = "wide";
    
    /**
     * which class will be used to draw day
     * @var string
     */
    public $className = "CalendarSDayEmpty";
    
    /**
     * initializes calendar
     * reads configuration
     * @return void
     */
    public function init ()
    {
        Yii::import ("application.extensions.calendar-s.ICalendarSDay", true);
        Yii::import ("application.extensions.calendar-s.CalendarSDayEmpty", true);
        
        if ($this->className == "CalendarSDayNameDaysLV")
        {
            Yii::import ("application.extensions.calendar-s.CalendarSDayNameDaysLV", true);
        }
        
        if (!$this->month || !is_numeric($this->month))
        {
            $this->month = date("n");
        }
        
        if (!$this->year || !is_numeric($this->year))
        {
            $this->year = date("Y");
        }
        
        if (!in_array ($this->weekStart, array ("Mon", "Sun")))
        {
            $this->weekStart = "Mon";
        }
    }
    
    /**
     * draw calendar
     * outputs html
     * @return void
     */
    public function run ()
    {
        echo "<table class=\"". $this->htmlClass ."\">";
        
        if ($this->headerFormat != "none")
        {
            echo "<tr>";
            for ($i=0; $i<7; $i++)
            {
                echo "<th>". $this->_dayName ($i) ."</th>";
            }
            echo "</tr>";
        }
        
        while ($this->_nextDay())
        {
            if ($this->_isRowStart ())
            {
                echo "<tr>";
            }
            
            echo "<td". $this->_classes () ."><div class=\"day\">". 
                $this->_current->format($this->format) . "</div>" . 
                $this->_current->getHtml() . "</td>";
            
            if ($this->_isRowEnd ())
            {
                echo "</tr>";
            }
        }
        
        echo "</table>";
    }
    
    /**
     * day
     * @return boolean
     */
    private function _nextDay ()
    {
        // first request
        if ($this->_current == null)
        {
            $dateTime = new DateTime ($this->year . "-" . $this->month . "-" . "01");
            while ($dateTime->format("D") != $this->weekStart)
            {
                $dateTime->sub(new DateInterval('P1D'));
                $end = clone $dateTime;
                $end->sub (new DateInterval('P1D'));
                $this->_weekEnd = $end->format ("D");
            }
            $this->_current = new $this->className ($dateTime);
            return true;
        }
        
        $dateTime = new DateTime ($this->_current->format("Y-m-d"));
        $dateTime->add(new DateInterval('P1D'));
        $this->_current = new $this->className ($dateTime);
        
        if ($this->_current->format("D") == $this->weekStart && $this->month != $this->_current->format("n"))
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * check if new table row is started
     * week starts
     * @return boolean
     */
    private function _isRowStart ()
    {
        return $this->_current->format("D") == $this->weekStart;
    }
    
    /**
     * check if table row need to be closed
     * week ends
     * @return boolean
     */
    private function _isRowEnd ()
    {
        return $this->_current->format("D") == $this->_weekEnd;
    }
    
    /**
     * html td classes of the day
     * @return string
     */
    private function _classes ()
    {
        $classes = array ();
        if ($this->_current->format("n") != $this->month)
        {
            $classes[] = "other";
        }
        
        if ($this->_current->format("Y-m-d") == date ("Y-m-d"))
        {
            $classes[] = "current";
        }
        
        if ($this->_current->format("N") == 6 || $this->_current->format("N") == 7)
        {
            $classes[] = "weekend";
        }
        
        if ($this->_current->isExtra())
        {
            $classes[] = "extra";
        }
        
        if ($classes)
        {
            return " class=\"" . implode (" ", $classes) . "\"";
        }
        return "";
    }
    
    /**
     * header day names
     * @param integer $day
     */
    private function _dayName ($day)
    {
        $days = Yii::app()->getLocale()->getWeekDayNames ($this->headerFormat);
        if ($this->weekStart == "Mon")
        {
            $tmp = Yii::app()->getLocale()->getWeekDayNames ($this->headerFormat);
            array_shift ($days);
            array_push ($days, $tmp[0]);
        }
        return $days[$day];
    }
}