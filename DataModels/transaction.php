<?

class TransactionType
{
    public $TransactionTypeID;
    public $TransactionName;
    public $DefaultPrice;
    public $DefaultStudentPrice;
}

class Transaction
{
    public $TransactionID;
    public $TransactionTypeID;
    public $TransactionNote;
    public $PunchCardID;
    public $ClassEnrollmentID;
    public $IsVolunteer;
    public $AttendanceID;
    public $Price;
}

?>