<?
include_once('DataAccess/MySQLDataAccessor.php');

class TransactionLogic
{
    public function AddTransaction($transactionTypeID, $transactionNote, $punchCardID, $classEnrollmentID, $isVolunteer, $attendanceID, $price)
    {
        global $dataAccessor;
        $transactionID = $dataAccessor->AddTransaction($transactionTypeID, $transactionNote, $punchCardID, $classEnrollmentID, $isVolunteer, $attendanceID, $price);
        return $transactionID;
    }
    
    public function DeleteTransaction($transactionID)
    {
        global $dataAccessor;
        $success = $dataAccessor->DeleteTransaction($transactionID);
        return $success;
    }
    
    public function UpdateTransaction($transactionID, $transactionTypeID, $transactionNote, $punchCardID, $classEnrollmentID, $isVolunteer, $attendanceID, $price)
    {
        global $dataAccessor;
        $success = $dataAccessor->UpdateTransaction($transactionID, $transactionTypeID, $transactionNote, $punchCardID, $classEnrollmentID, $isVolunteer, $attendanceID, $price);
        return $success;
    }
    
    public function GetTransactions($attendanceID)
    {
        global $dataAccessor;
        $transactions = $dataAccessor->GetTransactions($attendanceID);
        return $transactions;
    }
    
    public function AddPayment($transactionID, $paymentTypeID, $paymentAmount)
    {
        global $dataAccessor;
        $paymentID = $dataAccessor->AddPayment($transactionID, $paymentTypeID, $paymentAmount);
        return $paymentID;
    }
    
    public function DeletePayment($paymentID)
    {
        global $dataAccessor;
        $success = $dataAccessor->DeletePayment($paymentID);
        return $success;
    }
    
    public function GetPayments($transactionID)
    {
        global $dataAccessor;
        $payments = $dataAccessor->GetPayments($transactionID);
        return $payments;
    }
}
?>