<?

include_once('DataAccess/MySQLDataAccessor.php');

class CustomerLogic
{
    public static function GetAllCustomers()
    {
        global $dataAccessor;
        return $dataAccessor->GetAllCustomers();
    }
    
    public static function AddCustomer($firstName, $lastName, $email, $inMailingList, $balance, $birthday, $isStudent)
    {
        global $dataAccessor;
        if ($firstName == "" || $lastName == "")
        {
            return null;
        }
        if ($email == "") $email = null;
        if ($balance == "") $balance = 0;
        if ($birthday == "") $birthday = null;
        
        $customerID = $dataAccessor->AddCustomer($firstName, $lastName, $email, $inMailingList, $balance, $birthday, $isStudent);   
        return $customerID;
    }
    
    public static function UpdateCustomer($customerID, $firstName, $lastName, $email, $inMailingList, $balance, $birthday, $isStudent)
    {
        global $dataAccessor;
        if ($email == "") $email = null;
        if ($balance == "") $balance = 0;
        if ($birthday == "") $birthday = null;
        
        $success = $dataAccessor->UpdateCustomer($customerID, $firstName, $lastName, $email, $inMailingList, $balance, $birthday, $isStudent);
        return $success;
    }
    
    public static function DeleteCustomer($customerID)
    {
        global $dataAccessor;
        $success = $dataAccessor->DeleteCustomer($customerID);
        return $success;
    }
}

?>