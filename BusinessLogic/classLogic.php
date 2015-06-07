<?

include_once('DataAccess/MySQLDataAccessor.php');

class ClassLogic
{
    public static function GetAllClasses()
    {
        global $dataAccessor;
        return $dataAccessor->GetAllClasses();
    }
    
    public static function GetClass($classID)
    {
        global $dataAccessor;
        $class = $dataAccessor->GetClass($classID);
        return $class;
    }
    
    public static function AddClass($className, $startDate, $endDate)
    {
        global $dataAccessor;
        if ($className == "" || $startDate == "" || $endDate == "")
        {
            return null;
        }
        
        $classID = $dataAccessor->AddClass($className, $startDate, $endDate);   
        return $classID;
    }
    
    public static function UpdateClass($classID, $className, $startDate, $endDate)
    {
        global $dataAccessor;
               
        $success = $dataAccessor->UpdateClass($classID, $className, $startDate, $endDate);
        return $success;
    }
    
    public static function DeleteClass($classID)
    {
        global $dataAccessor;
        $success = $dataAccessor->DeleteClass($classID);
        return $success;
    }
    
    public static function EnrollInClass($classID, $customerID)
    {
        global $dataAccessor;
        $enrollmentID = $dataAccessor->EnrollInClass($classID, $customerID);
        return $enrollmentID;
    }
    
    public static function DeleteEnrollment($enrollmentID)
    {
        global $dataAccessor;
        $success = $dataAccessor->DeleteEnrollment($enrollmentID);
        return $success;
    }
    
    public static function UnenrollInClass($classID, $customerID)
    {
        global $dataAccessor;
        $success = $dataAccessor->UnenrollInClass($classID, $customerID);
        return $success;
    }
    
    public static function GetClassRoster($classID)
    {
        global $dataAccessor;
        $customers = $dataAccessor->GetClassRoster($classID);
        return $customers;
    }
    
    public static function GetCustomerClasses($customerID)
    {
        global $dataAccessor;
        $classes = $dataAccessor->GetCustomerClasses($customerID);
        return $classes;
    }
    
    public static function GetCurrentClasses()
    {
        global $dataAccessor;
        $classes = $dataAccessor->GetCurrentClasses();
        return $classes;
    }
}

?>