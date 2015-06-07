<?

include_once('DataAccess/MySQLDataAccessor.php');

class CashboxLogLogic
{
    public static function GetAllCashboxLogs()
    {
        global $dataAccessor;
        return $dataAccessor->GetAllCashboxLogs();
    }
    public static function GetCashboxLog($cashboxLogID)
    {
        global $dataAccessor;
        return $dataAccessor->GetCashboxLog($cashboxLogID);
    }
	public static function GetLatestCashboxCount($date)
	{
		global $dataAccessor;
		return $dataAccessor->GetLatestCashboxCount($date);
	}
	
    
    public static function AddCashboxLog($cashboxCount, $cashboxNote)
    {
        global $dataAccessor;
        $cashboxLogID = $dataAccessor->AddCashboxLog($cashboxCount, $cashboxNote, date("Y-m-d H:i:s"));
        return $cashboxLogID;
    }
    
    public static function UpdateCashboxLog($cashboxLogID, $cashboxCount, $cashboxNote)
    {
        global $dataAccessor;
               
        $success = $dataAccessor->UpdateCashboxLog($cashboxLogID, $cashboxCount, $cashboxNote);
        return $success;
    }
    
    public static function DeleteCashboxLog($cashboxLogID)
    {
        global $dataAccessor;
        $success = $dataAccessor->DeleteCashboxLog($cashboxLogID);
        return $success;
    }
}

?>