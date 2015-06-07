<?

include_once('DataAccess/MySQLDataAccessor.php');
include_once('BusinessLogic/cashBoxLogic.php');

class EventLogic
{
    public static function GetAllEvents()
    {
        global $dataAccessor;
        return $dataAccessor->GetAllEvents();
    }
    
    public static function AddEvent($eventTypeID, $eventName, $eventDate)
    {
        global $dataAccessor;
        if ($eventTypeID == "" || $eventName == "" || $eventDate == "")
        {
            return null;
        }
        
        $cashboxLogStartID = CashboxLogLogic::AddCashboxLog(null, null);
        $cashboxLogEndID = CashboxLogLogic::AddCashboxLog(null, null);
        
        $eventID = $dataAccessor->AddEvent($eventTypeID, $eventName, $cashboxLogStartID, $cashboxLogEndID, $eventDate);   
        return $eventID;
    }
    
    public static function UpdateEvent($eventID, $eventTypeID, $eventName, $eventDate)
    {
        global $dataAccessor;
               
        $success = $dataAccessor->UpdateEvent($eventID, $eventTypeID, $eventName, $eventDate);
        return $success;
    }
    
    public static function DeleteEvent($eventID)
    {
        // get rid of associated cashbox logs
        $event = EventLogic::GetEvent($eventID);
        CashboxLogLogic::DeleteCashboxLog($event->CashboxLogStartID);
        CashboxLogLogic::DeleteCashboxLog($event->CashboxLogEndID);
    
        // delete the event
        global $dataAccessor;
        $success = $dataAccessor->DeleteEvent($eventID);
        return $success;
    }
    
    public static function GetEvent($eventID)
    {
        global $dataAccessor;
        return $dataAccessor->GetEvent($eventID);
    }
}

class AttendanceLogic
{
    // Return list of Customers
    public static function GetEventAttendance($eventID)
    {
        global $dataAccessor;
        return $dataAccessor->GetEventAttendance($eventID);
    }
    // Return list of Events
    public static function GetCustomerAttendance($customerID)
    {
        global $dataAccessor;
        $events = $dataAccessor->GetCustomerAttendance($customerID);
        return $events;
    }
    public static function AddAttendance($eventID, $customerID)
    {
        global $dataAccessor;
        if ($customerID == "" || $eventID == "")
        {
            return null;
        }
        $attendanceID = $dataAccessor->AddAttendance($eventID, $customerID);
        return $attendanceID;
    }
    
    public static function DeleteAttendance($attendanceID)
    {
        global $dataAccessor;
        $success = $dataAccessor->DeleteAttendance($attendanceID);
        return $success;
    }
}

?>