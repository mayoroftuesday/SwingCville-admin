<h1>Tests</h1>

<?

ini_set('display_errors',1); 
error_reporting(E_ALL);

echo '<p>Current PHP version: ' . phpversion() . '</p>';

include_once('DataAccess/MySQLDataAccessor.php');
include_once('BusinessLogic/pageLogic.php');
include_once('BusinessLogic/customerLogic.php');
include_once('BusinessLogic/classLogic.php');
include_once('BusinessLogic/cashBoxLogic.php');
include_once('BusinessLogic/punchCardLogic.php');
include_once('BusinessLogic/libraryLogic.php');
include_once('BusinessLogic/eventsLogic.php');
include_once('BusinessLogic/transactionLogic.php');

// ========================================================================
// Database Tests
// ========================================================================
function db_ConnectionTest()
{
    $output = "";
    
    try
    {
        $mysqli = new mysqli('mysql410.ixwebhosting.com', 'C289934_admin', 'Swingout0', 'C289934_admin', 3306);

        // This is the "official" OO way to do it, BUT $connect_error was broken until PHP 5.2.9 and 5.3.0.
        if ($mysqli->connect_error) 
        {
            $output = 'Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error();
        }

        // Use this instead of $connect_error if you need to ensure compatibility with PHP versions prior to 5.2.9 and 5.3.0.
        if (mysqli_connect_error()) 
        {
            $output = 'Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error();
        }

        $mysqli->close();
        
        if ($output == "")
        {
            $output = "Success";
        }
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function db_QueryTest()
{
    global $dataAccessor;
    $output = "";
    
    try
    {
        $mysqli = $dataAccessor->GetSQLConnection();
        $result = $mysqli->query("SELECT * FROM MenuItem");
        $output = ($result->num_rows == 7) ? "Success" : "Fail";
        $result->close();
        $mysqli->close();
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}

// ========================================================================
// MySQLDataAccessor Tests
// ========================================================================
function MySQLDataAccessor_LoadLookupTest()
{
	global $dataAccessor;
    $output = "";
    try
    {
        $lookupItems = $dataAccessor->LoadLookup("EventType", "EventTypeID", "EventTypeName", "");
        $output = (count($lookupItems) > 0) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function MySQLDataAccessor_GetSQLConnectionTest()
{
    global $dataAccessor;
    $output = "";

    try
    {
        $mysqli = $dataAccessor->GetSQLConnection();

        // This is the "official" OO way to do it, BUT $connect_error was broken until PHP 5.2.9 and 5.3.0.
        if ($mysqli->connect_error) 
        {
            $output = 'Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error();
        }

        // Use this instead of $connect_error if you need to ensure compatibility with PHP versions prior to 5.2.9 and 5.3.0.
        if (mysqli_connect_error()) 
        {
            $output = 'Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error();
        }

        $mysqli->close();
        
        if ($output == "")
        {
            $output = "Success";
        }
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function MySQLDataAccessor_GetAllCustomersTest()
{
    global $dataAccessor;
    $output = "";
    try
    {
        $customers = $dataAccessor->GetAllCustomers();
        $output = (count($customers) > 0) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function MySQLDataAccessor_AddAndDeleteCustomerTest()
{
    global $dataAccessor;
    $output = "";
    try
    {
        $insertID = $dataAccessor->AddCustomer("Joe", "Schmoe", "jschmoe@gmail.com", false, 0, "2000-12-31", false);
        $affectedRows = $dataAccessor->DeleteCustomer($insertID);
        $output = ($insertID != null && $insertID > 0 && $affectedRows != null && $affectedRows > 0) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function MySQLDataAccessor_UpdateCustomerTest()
{
    global $dataAccessor;
    $output = "";
    try
    {
        $customerID = $dataAccessor->AddCustomer("Joe", "Schmoe", "jschmoe@gmail.com", false, 0, "2000-12-31", false);
        $success = $dataAccessor->UpdateCustomer($customerID, "Joe2", "Schmoe2", "jschmoe2@gmail.com", true, 1, "2001-01-01", true);
        CustomerLogic::DeleteCustomer($customerID);
        $output = ($success == true) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function MySQLDataAccessor_GetPagesTest()
{
    global $dataAccessor;
    $output = "";
    try
    {
        $pages = $dataAccessor->GetPages();
        $output = (count($pages) == 7) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function MySQLDataAccessor_GetPageNameTest()
{
    global $dataAccessor;
    $output = "";
    try
    {
        $pageName = $dataAccessor->GetPageName("events.php");
        $output = ($pageName == "Events") ? "Success" : "Fail ($pageName)";
    }
    catch(Exception $e)
    {
        $output = $e;
    }  
    
    testLog(__FUNCTION__, $output);
}

// ========================================================================
// ClassLogic Tests
// ========================================================================
function ClassLogic_AddAndDeleteClassTest()
{
    $output = "";
    try
    {
        $classID = ClassLogic::AddClass("Lindy Hop lvl1", "2000-12-01", "2000-12-31");
        $success = ClassLogic::DeleteClass($classID);
        $output = ($classID != null && $classID > 0 && $success == true) ? "Success" : "ClassID = $classID, Success = $success";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function ClassLogic_UpdateClassTest()
{
    $output = "";
    try
    {
        $classID = ClassLogic::AddClass("Lindy Hop lvl1", "2000-12-01", "2000-12-31");
        $success = ClassLogic::UpdateClass($classID, "Lindy Hop lvl2", "2001-12-01", "2001-12-31");
        ClassLogic::DeleteClass($classID);
        $output = ($success == true) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function ClassLogic_GetAllClassesTest()
{
    $output = "";
    try
    {
        $classes = ClassLogic::GetAllClasses();
        $output = (count($classes) > 0) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}

function ClassLogic_EnrollmentTest()
{
    $output = "";
    try
    {
    
        $classID = ClassLogic::AddClass("Lindy Hop lvl1", "2000-12-01", "2000-12-31");
        $customerID = CustomerLogic::AddCustomer("Joe", "Schmoe", "jschmoe@gmail.com", false, 0, "2000-12-31", false);
        $enrollmentID = ClassLogic::EnrollInClass($classID, $customerID);

        $roster = ClassLogic::GetClassRoster($classID);
        $success = (count($roster) == 1 && $roster[0]->CustomerID == $customerID);

        $classes = ClassLogic::GetCustomerClasses($customerID);
        $success = ($success && count($classes) == 1 && $classes[0]->ClassID == $classID);

        $unenrollSuccess = ClassLogic::UnenrollInClass($classID, $customerID);
        $success = ($success && $unenrollSuccess);

        $roster = ClassLogic::GetClassRoster($classID);
        $success = (count($roster) == 0);

        $classes = ClassLogic::GetCustomerClasses($customerID);
        $success = ($success && count($classes) == 0);

        CustomerLogic::DeleteCustomer($customerID);
        ClassLogic::DeleteClass($classID);

        $output = ($success == true) ? "Success": "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }

    testLog(__FUNCTION__, $output);
}

function ClassLogic_GetCurrentClassesTest()
{
    $output = "";
    try
    {
        $today = date("Y-m-d");// current date
        $today2 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($today)) . " +1 day"));
        $nextmonth = date("Y-m-d", strtotime(date("Y-m-d", strtotime($today)) . " +1 month"));
        $nextmonth2 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($nextmonth)) . " +1 day"));
        $lastmonth = date("Y-m-d", strtotime(date("Y-m-d", strtotime($today)) . " -1 month"));
        $lastmonth2 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($lastmonth)) . " +1 day"));
        
        $classAID = ClassLogic::AddClass("Class A", $today, $today2);
        $classBID = ClassLogic::AddClass("Class B", $nextmonth, $nextmonth2);
        $classCID = ClassLogic::AddClass("Class C", $lastmonth, $lastmonth2);
        
        $classes = ClassLogic::GetCurrentClasses();
        
        $success = false;
        for ($i = 0; $i < count($classes); $i++)
        {
            if ($classes[$i]->ClassID == $classAID) 
            {
                $success = true;
            }
            if ($classes[$i]->ClassID == $classBID || $classes[$i]->ClassID == $classCID)
            {
                $success = false;
                break;
            }
        }
        
        $output = ($success == true) ? "Success": "Fail";
        
        ClassLogic::DeleteClass($classAID);
        ClassLogic::DeleteClass($classBID);
        ClassLogic::DeleteClass($classCID);
    }
    catch (Exception $e)
    {
        $output = $e;
    }

    testLog(__FUNCTION__, $output);
}

// ========================================================================
// CustomerLogic Tests
// ========================================================================
function CustomerLogic_AddAndDeleteCustomerTest()
{
    $output = "";
    try
    {
        $customerID = CustomerLogic::AddCustomer("Joe", "Schmoe", "jschmoe@gmail.com", false, 0, "2000-12-31", false);
        $success = CustomerLogic::DeleteCustomer($customerID);
        $output = ($customerID != null && $customerID > 0 && $success == true) ? "Success" : "CustomerID = $customerID, Success = $success";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function CustomerLogic_UpdateCustomerTest()
{
    $output = "";
    try
    {
        $customerID = CustomerLogic::AddCustomer("Joe", "Schmoe", "jschmoe@gmail.com", false, 0, "2000-12-31", false);
        $success = CustomerLogic::UpdateCustomer($customerID, "Joe2", "Schmoe2", "jschmoe2@gmail.com", true, 1, "2001-01-01", true);
        CustomerLogic::DeleteCustomer($customerID);
        $output = ($success == true) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function CustomerLogic_GetAllCustomersTest()
{
    $output = "";
    try
    {
        $customers = CustomerLogic::GetAllCustomers();
        $output = (count($customers) > 0) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}

// ========================================================================
// PageLogic Tests
// ========================================================================
function PageLogic_GetPagesTest()
{
    $output = "";
    try
    {
        $pages = PageLogic::GetPages();
        $output = (count($pages) == 7) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function PageLogic_GetPageNameTest()
{
    $output = "";
    try
    {
        $pageName = PageLogic::GetPageName("/events.php");
        $output = ($pageName == "Events") ? "Success" : "Fail";
    }
    catch(Exception $e)
    {
        $output = $e;
    }  
    
    testLog(__FUNCTION__, $output);
}
function PageLogic_GetPageNameTest2()
{
    $output = "";
    try
    {
        $pageName = PageLogic::GetPageName("/");
        $output = ($pageName == "Home") ? "Success" : "Fail";
    }
    catch(Exception $e)
    {
        $output = $e;
    }  
    
    testLog(__FUNCTION__, $output);
}
function PageLogic_GetPageNameTest3()
{
    $output = "";
    try
    {
        $pageName = PageLogic::GetPageName("");
        $output = ($pageName == "Home") ? "Success" : "Fail";
    }
    catch(Exception $e)
    {
        $output = $e;
    }  
    
    testLog(__FUNCTION__, $output);
}

// ========================================================================
// CashboxLogLogic Tests
// ========================================================================
function CashboxLogLogic_AddAndDeleteCashboxLogTest()
{
    $output = "";
    try
    {
        $cashboxLogID = CashboxLogLogic::AddCashboxLog(999.9, "Random number");
        $success = CashboxLogLogic::DeleteCashboxLog($cashboxLogID);
        $output = ($cashboxLogID != null && $cashboxLogID > 0 && $success == true) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function CashboxLogLogic_GetLatestCashboxCountTest()
{
	$output = "";
	try
	{
		$cashboxLogID = CashboxLogLogic::AddCashboxLog(1492.88, "Random number");
		$latestAmount = CashboxLogLogic::GetLatestCashboxCount();
        CashboxLogLogic::DeleteCashboxLog($cashboxLogID);
        $output = ($latestAmount == 1492.88) ? "Success" : $latestAmount;
	}
	catch (Exception $e)
	{
		$output = $e;
	}
	
	testLog(__FUNCTION__, $output);
}
function CashboxLogLogic_UpdateCashboxLogTest()
{
    $output = "";
    try
    {
        $cashboxLogID = CashboxLogLogic::AddCashboxLog(999.9, "Random number");
        $success = CashboxLogLogic::UpdateCashboxLog($cashboxLogID, 1000.20, "Random number tomorrow");
        CashboxLogLogic::DeleteCashboxLog($cashboxLogID);
        $output = ($success == true) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function CashboxLogLogic_GetAllCashboxLogsTest()
{
    $output = "";
    try
    {
        $cashboxes = CashboxLogLogic::GetAllCashboxLogs();
        $output = (count($cashboxes) > 0) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}

// ========================================================================
// PunchCardLogic Tests
// ========================================================================
function PunchCardLogic_AddAndDeletePunchCardTest()
{
    $output = "";
    try
    {
        $punchCardID = PunchCardLogic::AddPunchCard(1, "2000-12-31", 99);
        $success = PunchCardLogic::DeletePunchCard($punchCardID);
        $output = ($punchCardID != null && $punchCardID > 0 && $success == true) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function PunchCardLogic_UpdatePunchCardTest()
{
    $output = "";
    try
    {
        $punchCardID = PunchCardLogic::AddPunchCard(1, "2000-12-31", 99);
        $success = PunchCardLogic::UpdatePunchCard($punchCardID, 2, "2001-12-01", 100);
        PunchCardLogic::DeletePunchCard($punchCardID);
        $output = ($success == true) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function PunchCardLogic_GetAllPunchCardsTest()
{
    $output = "";
    try
    {
        $punchCards = PunchCardLogic::GetAllPunchCards();
        $output = (count($punchCards) > 0) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}

// ========================================================================
// LibraryItem Tests
// ========================================================================
function LibraryItemLogic_AddAndDeleteLibraryItemTest()
{
    $output = "";
    try
    {
        $libraryItemID = LibraryItemLogic::AddLibraryItem("Cool CD", 1, "2000-12-31");
        $success = LibraryItemLogic::DeleteLibraryItem($libraryItemID);
        $output = ($libraryItemID != null && $libraryItemID > 0 && $success == true) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function LibraryItemLogic_UpdateLibraryItemTest()
{
    $output = "";
    try
    {
        $libraryItemID = LibraryItemLogic::AddLibraryItem("Cool CD", 1, "2000-12-31");
        $success = LibraryItemLogic::UpdateLibraryItem($libraryItemID, "Cool CD 2", 2, "2001-01-01");
        LibraryItemLogic::DeleteLibraryItem($libraryItemID);
        $output = ($success == true) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function LibraryItemLogic_GetAllLibraryItemsTest()
{
    $output = "";
    try
    {
        $libraryItems = LibraryItemLogic::GetAllLibraryItems();
        $output = (count($libraryItems) > 0) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}

// ========================================================================
// Event Tests
// ========================================================================
function EventLogic_AddAndDeleteEventTest()
{
    $output = "";
    try
    {
        $eventID = EventLogic::AddEvent(1, "Test event", "2000-12-31");
        $success = EventLogic::DeleteEvent($eventID);
        $output = ($eventID != null && $eventID > 0 && $success == true) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function EventLogic_UpdateEventTest()
{
    $output = "";
    try
    {
        $eventID = EventLogic::AddEvent(1, "Test event", "2000-12-31");
        $success = EventLogic::UpdateEvent($eventID, 2, "Test event 2", "2001-01-01");
        EventLogic::DeleteEvent($eventID);
        $output = ($success == true) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function EventLogic_GetAllEventsTest()
{
    $output = "";
    try
    {
        $events = EventLogic::GetAllEvents();
        $output = (count($events) > 0) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
function EventLogic_GetEventTest()
{
	$output = "";
	try
	{
		$event = EventLogic::GetEvent($eventID);
		EventLogic::DeleteEvent($eventID);
		$output = ($event->EventName = "GetEvent Test Event") ? "Success" : "Fail";
	}
	catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}
// ========================================================================
// Attendance Tests
// ========================================================================

function AttendanceLogic_Test()
{
    $output = "";
    try
    {
        // add two events A and B
        $eventA = EventLogic::AddEvent(1, "GetEvent Test Event 1", "2000-12-31");
        $eventB = EventLogic::AddEvent(1, "GetEvent Test Event 2", "2000-12-31");
        
        // add two customers X and Y
        $customerX = CustomerLogic::AddCustomer("Joe", "Schmoe", "jschmoe@gmail.com", false, 0, "2000-12-31", false);
        $customerY = CustomerLogic::AddCustomer("Jane", "Schmoe", "jschmoe@gmail.com", false, 0, "2000-12-31", false);
        
        // add attendance records for the customers at the two events (A at X, B at both X and Y)
        $attendanceAX = AttendanceLogic::AddAttendance($eventA, $customerX);
        $attendanceBX = AttendanceLogic::AddAttendance($eventB, $customerX);
        $attendanceBY = AttendanceLogic::AddAttendance($eventB, $customerY);
        
        // check to make sure the events have the correct number of attendees (A has 1, B has 2)
        $attendanceAtA = AttendanceLogic::GetEventAttendance($eventA);
        $attendanceAtB = AttendanceLogic::GetEventAttendance($eventB);
        $success = (count($attendanceAtA) == 1) && (count($attendanceAtB) == 2);
        
        // check to make sure the customers have the correct number of events in their histories (X has 2, Y has 1)
        $customerXHistory = AttendanceLogic::GetCustomerAttendance($customerX);
        $customerYHistory = AttendanceLogic::GetCustomerAttendance($customerY);
        $success = $success && (count($customerXHistory) == 2) && (count($customerYHistory) == 1);
        
        // delete event B
        EventLogic::DeleteEvent($eventB);
        
        // recheck to make sure the customers have the correct number of events in their histories (X has 1, Y has none)
        $customerXHistory = AttendanceLogic::GetCustomerAttendance($customerX);
        $customerYHistory = AttendanceLogic::GetCustomerAttendance($customerY);
        $success = $success && (count($customerXHistory) == 1) && (count($customerYHistory) == 0);
        
        // delete A's remaining attendance record at X
        $success = $success && AttendanceLogic::DeleteAttendance($attendanceAX);
        
        // check to make sure the attendance at A is 0
        $attendanceAtA = AttendanceLogic::GetEventAttendance($eventA);
        $success = $success && (count($attendanceAtA) == 0);
        
        // check to make sure customer X has 0 events
        $customerXHistory = AttendanceLogic::GetCustomerAttendance($customerX);
        $success = $success && (count($customerXHistory) == 0);

        // delete the events and customers
        EventLogic::DeleteEvent($eventA);
        CustomerLogic::DeleteCustomer($customerX);
        CustomerLogic::DeleteCustomer($customerY);
        
        $output = ($success == true) ? "Success": "Fail";
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
    
}

// ========================================================================
// Transactions Tests
// ========================================================================

function TransactionLogic_AddUpdateDeleteTransactionTest()
{
    $output = "";
    try
    {
        $transactionID = TransactionLogic::AddTransaction(1, "This is a transaction", 1, 1, true, 1, 5);
        $updateSuccess = TransactionLogic::UpdateTransaction($transactionID, 2, "This is another transaction", 2, 2, false, 2, 10);
        $deleteSuccess = TransactionLogic::DeleteTransaction($transactionID);
        
        $output = ($updateSuccess && $deleteSuccess) ? "Success" : "Fail";
    }
    catch (Exception $e)
    {   
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}

function TransactionLogic_GetTransactionsTest()
{
    $output = "";
    try
    {
        // hell, if all this shit works, I'll be a happy man
        $eventID = EventLogic::AddEvent(1, "GetEvent Test Event 1", "2000-12-31");
        $customerID = CustomerLogic::AddCustomer("Joe", "Schmoe", "jschmoe@gmail.com", false, 0, "2000-12-31", false);
        $attendanceID = AttendanceLogic::AddAttendance($eventID, $customerID);
        $transactionID = TransactionLogic::AddTransaction(1, "Test transaction", null, null, false, $attendanceID, 5);
        $transactions = TransactionLogic::GetTransactions($attendanceID);
        $success = count($transactions) == 1 && $transactions[0]->TransactionID == $transactionID;
        
        $output = ($success == true) ? "Success" : "Fail";
        
        TransactionLogic::DeleteTransaction($transactionID);
        AttendanceLogic::DeleteAttendance($attendanceID);
        CustomerLogic::DeleteCustomer($customerID);
        EventLogic::DeleteEvent($eventID);
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    testLog(__FUNCTION__, $output);
}

function TransactionLogic_AddDeletePaymentTest()
{
    $output = "";
    try
    {
        $paymentID = TransactionLogic::AddPayment(1, 1, 1);
        $success = TransactionLogic::DeletePayment($paymentID);
        $output = ($success == true) ? "Success" : "Fail";
        
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}

function TransactionLogic_GetPaymentsTest()
{
    $output = "";
    try
    {
        // hell, if all this shit works, I'll be a happy man
        $eventID = EventLogic::AddEvent(1, "GetEvent Test Event 1", "2000-12-31");
        $customerID = CustomerLogic::AddCustomer("Joe", "Schmoe", "jschmoe@gmail.com", false, 0, "2000-12-31", false);
        $attendanceID = AttendanceLogic::AddAttendance($eventID, $customerID);
        $transactionID = TransactionLogic::AddTransaction(1, "Test transaction", null, null, false, $attendanceID, 5);
        $paymentID = TransactionLogic::AddPayment($transactionID, 1, 1);
        $payments = TransactionLogic::GetPayments($transactionID);
        
        $success = count($payments) == 1 && $payments[0]->PaymentID == $paymentID;
        
        $output = ($success == true) ? "Success" : "Fail";
        
        TransactionLogic::DeletePayment($paymentID);
        TransactionLogic::DeleteTransaction($transactionID);
        AttendanceLogic::DeleteAttendance($attendanceID);
        CustomerLogic::DeleteCustomer($customerID);
        EventLogic::DeleteEvent($eventID);
    }
    catch (Exception $e)
    {
        $output = $e;
    }
    
    testLog(__FUNCTION__, $output);
}


// ========================================================================
// Test Rendering Functions
// ========================================================================
function testLog($testName, $result)
{
    print("<tr><th>$testName</th><td>$result</td></tr>\n");
}
function testLogHeader($section)
{
    print("<tr><th colspan=2 bgcolor=gray>$section</th></tr>");
}

// ========================================================================
// Run the tests!
// ========================================================================
print("<table>");
/*
testLogHeader("Database");
db_ConnectionTest();
db_QueryTest($dataAccessor);

testLogHeader("MySQLDataAccessor");
MySQLDataAccessor_GetSQLConnectionTest();
MySQLDataAccessor_GetPageNameTest();
MySQLDataAccessor_GetPagesTest();
MySQLDataAccessor_GetAllCustomersTest();
MySQLDataAccessor_AddAndDeleteCustomerTest();
MySQLDataAccessor_UpdateCustomerTest();
MySQLDataAccessor_LoadLookupTest();

testLogHeader("PageLogic");
PageLogic_GetPageNameTest();
PageLogic_GetPageNameTest2();
PageLogic_GetPageNameTest3();
PageLogic_GetPagesTest();

testLogHeader("CustomerLogic");
CustomerLogic_GetAllCustomersTest();
CustomerLogic_AddAndDeleteCustomerTest();
CustomerLogic_UpdateCustomerTest();

testLogHeader("ClassLogic");
ClassLogic_GetAllClassesTest();
ClassLogic_AddAndDeleteClassTest();
ClassLogic_UpdateClassTest();
ClassLogic_EnrollmentTest();
ClassLogic_GetCurrentClassesTest();

testLogHeader("CashboxLogic");
CashboxLogLogic_GetAllCashboxLogsTest();
CashboxLogLogic_GetLatestCashboxCountTest();
CashboxLogLogic_AddAndDeleteCashboxLogTest();
CashboxLogLogic_UpdateCashboxLogTest();

testLogHeader("PunchCardLogic");
PunchCardLogic_GetAllPunchCardsTest();
PunchCardLogic_AddAndDeletePunchCardTest();
PunchCardLogic_UpdatePunchCardTest();

testLogHeader("LibraryItemLogic");
LibraryItemLogic_GetAllLibraryItemsTest();
LibraryItemLogic_AddAndDeleteLibraryItemTest();
LibraryItemLogic_UpdateLibraryItemTest();

testLogHeader("EvetLogic");
EventLogic_GetAllEventsTest();
EventLogic_AddAndDeleteEventTest();
EventLogic_UpdateEventTest();
EVentLogic_GetEventTest();

testLogHeader("AttendanceLogic");
AttendanceLogic_Test();
*/
testLogHeader("TransactionLogic");
TransactionLogic_AddUpdateDeleteTransactionTest();
TransactionLogic_GetTransactionsTest();
TransactionLogic_AddDeletePaymentTest();
TransactionLogic_GetPaymentsTest();

print("</table>");


?>