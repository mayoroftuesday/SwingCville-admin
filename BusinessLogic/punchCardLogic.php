<?

include_once('DataAccess/MySQLDataAccessor.php');

class PunchCardLogic
{
    public static function GetAllPunchCards()
    {
        global $dataAccessor;
        return $dataAccessor->GetAllPunchCards();
    }
    
    public static function AddPunchCard($customerID, $date, $cardNumber)
    {
        global $dataAccessor;
        if ($customerID == "" || $date == "" || $cardNumber == "")
        {
            return null;
        }
        
        $punchCardID = $dataAccessor->AddPunchCard($customerID, $date, $cardNumber);   
        return $punchCardID;
    }
    
    public static function UpdatePunchCard($punchCardID, $customerID, $date, $cardNumber)
    {
        global $dataAccessor;
               
        $success = $dataAccessor->UpdatePunchCard($punchCardID, $customerID, $date, $cardNumber);
        return $success;
    }
    
    public static function DeletePunchCard($punchCardID)
    {
        global $dataAccessor;
        $success = $dataAccessor->DeletePunchCard($punchCardID);
        return $success;
    }
}

?>