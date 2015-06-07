<?

include_once("DataModels/menuItem.php");
include_once("DataModels/customer.php");
include_once("DataModels/class.php");
include_once("DataModels/classEnrollment.php");
include_once("DataModels/cashBoxLog.php");
include_once("DataModels/punchCard.php");
include_once("DataModels/lookupItem.php");
include_once("DataModels/libraryItem.php");
include_once("DataModels/libraryCheckout.php");
include_once("DataModels/event.php");
include_once("DataModels/transaction.php");
include_once("DataModels/payment.php");

class MySQLDataAccessor
{
	// ========================================================================
	// Basic Functionality
	// ========================================================================
    public function GetSQLConnection()
    {
        return new mysqli(
            'mysql410.ixwebhosting.com', 
            'C289934_admin', 
            'Swingout0', 
            'C289934_admin', 
            3306);
    }

	// ========================================================================
	// Lookup Functions
	// ========================================================================
	public function LoadLookup($tableName, $keyField, $valueField, $order)
	{
		if ($order == "")
		{
			$order = $valueField;
		}
		$mysqli = $this->GetSQLConnection();
        $result = $mysqli->query("SELECT $keyField AS `Key`, $valueField AS `Value` FROM $tableName ORDER BY $order");
		if( !$result)
			die($mysqli->error);
        $i = 0;
		$lookupItems = array();
        while($lookupItem = $result->fetch_object("LookupItem"))
        {
            $lookupItems[$i++] = $lookupItem;
        }
		$result->close();
		$mysqli->close();
        return $lookupItems;
	}
	
	// ========================================================================
	// Page Functions
	// ========================================================================
    public function GetPageName($uri)
    {
        $mysqli = $this->GetSQLConnection();

        $statement = $mysqli->prepare("SELECT PageName FROM MenuItem WHERE URL = ?");
        $statement->bind_param("s", $uri);
        $statement->execute();
        $statement->bind_result($pageName);
        $statement->fetch();
        $statement->close();
        $mysqli->close();

        return $pageName;
    }

    public function GetPages()
    {
        $mysqli = $this->GetSQLConnection();
        $result = $mysqli->query("SELECT * FROM MenuItem ORDER BY FormOrder ASC");
        $i = 0;
		$menuItems = array();
        while($menuItem = $result->fetch_object("MenuItem"))
        {
            $menuItems[$i++] = $menuItem;
        }
		$result->close();
		$mysqli->close();
        return $menuItems;
    }

	// ========================================================================
	// Customer Functions
	// ========================================================================    
    public function GetAllCustomers()
    {
        $mysqli = $this->GetSQLConnection();
        $result = $mysqli->query("SELECT * FROM Customer ORDER BY FirstName ASC");
        $i = 0;
		$customers = array();
        while ($customer = $result->fetch_object("Customer"))
        {
            $customers[$i++] = $customer;
        }
		$result->close();
		$mysqli->close();
        return $customers;    
    }
    public function AddCustomer($firstName, $lastName, $email, $inMailingListBoolean, $balance, $birthday, $isStudentBoolean)
    {
        $inMailingList = ($inMailingListBoolean == true) ? 1 : 0;
        $isStudent = ($isStudentBoolean == true) ? 1 : 0;
        
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("INSERT INTO `Customer` (`FirstName`, `LastName`, `Email`, `InMailingList`, `Balance`, `Birthday`, `IsStudent`) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sssissi", $firstName, $lastName, $email, $inMailingList, $balance, $birthday, $isStudentBoolean);
        $stmt->execute();
        $insertID = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();
        
        return $insertID;
    }
    public function UpdateCustomer($customerID, $firstName, $lastName, $email, $inMailingListBoolean, $balance, $birthday, $isStudentBoolean)
    {
        $inMailingList = ($inMailingListBoolean == true) ? 1 : 0;
        $isStudent = ($isStudentBoolean == true) ? 1 : 0;
        
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("UPDATE `Customer` SET `FirstName` = ?, `LastName` = ?, `Email` = ?, `InMailingList` = ?, `Balance` = ?, `Birthday` = ?, `IsStudent` = ? WHERE `CustomerID` = ?");
        $stmt->bind_param("sssissii", $firstName, $lastName, $email, $inMailingList, $balance, $birthday, $isStudentBoolean, $customerID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        
        return ($affectedRows == 1) ? true : false;
    }
    public function DeleteCustomer($customerID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `Customer` WHERE `CustomerID` = ?");
        $stmt->bind_param("i", $customerID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
    }
	
	// ========================================================================
	// Class Functions
	// ========================================================================
    public function GetAllClasses()
    {
        $mysqli = $this->GetSQLConnection();
        $result = $mysqli->query("SELECT * FROM Class ORDER BY StartDate DESC");
        $i = 0;
		$lessons = array();
        while ($lesson = $result->fetch_object("LessonSeries"))
        {
            $lessons[$i++] = $lesson;
        }
		$result->close();
		$mysqli->close();
        return $lessons;    
    }
    public function GetClass($classID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("SELECT `ClassName`, `StartDate`, `EndDate` FROM `Class` WHERE `ClassID` = ?");
        $stmt->bind_param("i", $classID);
        $stmt->execute();
        $stmt->bind_result($className, $startDate, $endDate);
        $stmt->fetch();
        $lesson = new LessonSeries();
		$lesson->ClassID = $classID;
        $lesson->ClassName = $className;
        $lesson->StartDate = $startDate;
        $lesson->EndDate = $endDate;
        return $lesson;
    }
    public function AddClass($className, $startDate, $endDate)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("INSERT INTO `Class` (`ClassName`, `StartDate`, `EndDate`) VALUES (?,?,?)");
        $stmt->bind_param("sss", $className, $startDate, $endDate);
        $stmt->execute();
        $insertID = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();
        
        return $insertID;
    }
    public function UpdateClass($classID, $className, $startDate, $endDate)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("UPDATE `Class` SET `ClassName` = ?, `StartDate` = ?, `EndDate` = ? WHERE `ClassID` = ?");
        $stmt->bind_param("sssi", $className, $startDate, $endDate, $classID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        
        return ($affectedRows == 1) ? true : false;
    }
    public function DeleteClass($classID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `Class` WHERE `ClassID` = ?");
        $stmt->bind_param("i", $classID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
    }	
    
    
    public function EnrollInClass($classID, $customerID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("INSERT INTO `ClassEnrollment` (`ClassID`, `CustomerID`) VALUES (?,?)");
        $stmt->bind_param("ii", $classID, $customerID);
        $stmt->execute();
        $insertID = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();
        
        return $insertID;
    }
    
    public function DeleteEnrollment($classEnrollmentID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `ClassEnrollment` WHERE `ClassEnrollmentID` = ?");
        $stmt->bind_param("i", $classEnrollmentID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
    }
    
    public function UnenrollInClass($classID, $customerID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `ClassEnrollment` WHERE `ClassID` = ? AND CustomerID = ?");
        $stmt->bind_param("ii", $classID, $customerID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
    }
    
    public function GetClassRoster($classID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("
            SELECT ClassEnrollmentID, Customer.CustomerID, CONCAT(FirstName, ' ', LastName) AS Name
            FROM ClassEnrollment INNER JOIN Customer ON ClassEnrollment.CustomerID = Customer.CustomerID 
            WHERE ClassID = ? 
            ORDER BY FirstName, LastName");
        $stmt->bind_param("i", $classID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($classEnrollmentID, $customerID, $name);
       
        $enrollments = array();
        while ($stmt->fetch())
        {
            $enrollment = new ClassEnrollment();
            $enrollment->ClassEnrollmentID = $classEnrollmentID;
            $enrollment->CustomerID = $customerID;
            $enrollment->Name = $name;
            $enrollments[] = $enrollment;
        }
        $stmt->free_result();
        $stmt->close();
        $mysqli->close();
        return $enrollments; 
    }
    
    public function GetCustomerClasses($customerID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("
            SELECT Class.ClassID, ClassName, StartDate, EndDate
            FROM ClassEnrollment 
            INNER JOIN Class ON Class.ClassID = ClassEnrollment.ClassID 
            WHERE CustomerID = ? 
            ORDER BY StartDate DESC") or die(mysqli_error($mysqli));
        $stmt->bind_param("i", $customerID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($classID, $className, $startDate, $endDate);
       
        $classes = array();
        while ($stmt->fetch())
        {
            $class = new LessonSeries();
            $class->ClassID = $classID;
            $class->ClassName = $className;
            $class->StartDate = $startDate;
            $class->EndDate = $endDate;
            $classes[] = $class;
        }
        $stmt->free_result();
        $stmt->close();
        $mysqli->close();
        return $classes; 
    }
    
    public function GetCurrentClasses()
    {
        $mysqli = $this->GetSQLConnection();
        $result = $mysqli->query("
            SELECT Class.ClassID, ClassName, StartDate, EndDate
            FROM Class 
            WHERE EndDate >= CURDATE()
            ORDER BY ClassName");
       
        $events = array();
        while ($event = $result->fetch_object("Event"))
        {
            $events[] = $event;
        }
        
        $result->close();
        $mysqli->close();
        return $events;       
    }
	
	// ========================================================================
	// CashboxLog Functions
	// ========================================================================
	public function GetAllCashboxLogs()
    {
        $mysqli = $this->GetSQLConnection();
        $result = $mysqli->query("SELECT * FROM CashboxLog ORDER BY Date DESC");
        $i = 0;
		$cashboxLogs = array();
        while ($cashboxLog = $result->fetch_object("CashboxLog"))
        {
            $cashboxLogs[$i++] = $cashboxLog;
        }
		$result->close();
		$mysqli->close();
        return $cashboxLogs;    
    }
    public function GetCashboxLog($cashboxLogID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("SELECT `CashboxCount`, `CashboxNote`, `Date` FROM `CashboxLog` WHERE `CashboxLogID` = ?");
        $stmt->bind_param("i", $cashboxLogID);
        $stmt->execute();
        $stmt->bind_result($cashboxCount, $cashboxNote, $date);
        $stmt->fetch();
        $cashbox = new CashboxLog();
		$cashbox->CashboxLogID = $cashboxLogID;
        $cashbox->CashboxCount = $cashboxCount;
        $cashbox->CashboxNote = $cashboxNote;
        $cashbox->Date = $date;
        return $cashbox;
    }
	public function GetLatestCashboxCount($date)
	{
		$mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("SELECT CashboxCount FROM CashboxLog WHERE Date <= ? ORDER BY Date DESC LIMIT 1");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        if ($stmt->num_rows > 0)
        {
            $stmt->bind_result($cashboxCount);
            return $cashboxCount;
        }
        else
        {
            return 0;
        }
	}
    public function AddCashboxLog($cashboxCount, $cashboxNote, $date)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("INSERT INTO `CashboxLog` (`CashboxCount`, `CashboxNote`, `Date`) VALUES (?,?,?)");
        $stmt->bind_param("dss", $cashboxCount, $cashboxNote, $date);
        $stmt->execute();
        $insertID = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();
        
        return $insertID;
    }
    public function UpdateCashboxLog($cashboxLogID, $cashboxCount, $cashboxNote)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("UPDATE `CashboxLog` SET `CashboxCount` = ?, `CashboxNote` = ? WHERE `CashboxLogID` = ?");
        $stmt->bind_param("dsi", $cashboxCount, $cashboxNote, $cashboxLogID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        
        return ($affectedRows == 1) ? true : false;
    }
    public function DeleteCashboxLog($cashboxLogID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `CashboxLog` WHERE `CashboxLogID` = ?");
        $stmt->bind_param("i", $cashboxLogID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
    }
	
	// ========================================================================
	// PunchCard Functions
	// ========================================================================
    public function GetAllPunchCards()
    {
        $mysqli = $this->GetSQLConnection();
        $result = $mysqli->query("SELECT * FROM PunchCard ORDER BY PurchaseDate DESC");
        $i = 0;
		$lessons = array();
        while ($punchCard = $result->fetch_object("PunchCard"))
        {
            $punchCards[$i++] = $punchCard;
        }
		$result->close();
		$mysqli->close();
        return $punchCards;    
    }
    public function AddPunchCard($customerID, $date, $cardNumber)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("INSERT INTO `PunchCard` (`CustomerID`, `PurchaseDate`, `CardNumber`) VALUES (?,?,?)");
        $stmt->bind_param("isi", $customerID, $date, $cardNumber);
        $stmt->execute();
        $insertID = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();
        
        return $insertID;
    }
    public function UpdatePunchCard($punchCardID, $customerID, $date, $cardNumber)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("UPDATE `PunchCard` SET `CustomerID` = ?, `PurchaseDate` = ?, `CardNumber` = ? WHERE `PunchCardID` = ?");
        $stmt->bind_param("isii", $customerID, $date, $cardNumber, $punchCardID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        
        return ($affectedRows == 1) ? true : false;
    }
    public function DeletePunchCard($punchCardID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `PunchCard` WHERE `PunchCardID` = ?");
        $stmt->bind_param("i", $punchCardID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
    }	
	
	// ========================================================================
	// LibraryItem Functions
	// ========================================================================
    public function GetAllLibraryItems()
    {
        $mysqli = $this->GetSQLConnection();
        $result = $mysqli->query("SELECT * FROM LibraryItem ORDER BY ItemName ASC");
        $i = 0;
		$libraryItems = array();
        while ($libraryItem = $result->fetch_object("LibraryItem"))
        {
            $libraryItems[$i++] = $libraryItem;
        }
		$result->close();
		$mysqli->close();
        return $libraryItems;    
    }
	public function GetLibraryCheckouts($libraryItemID)
    {
		$mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("
            SELECT LibraryCheckoutID, Customer.CustomerID, DateCheckedOut, DateReturned, DateDue, CONCAT(FirstName,' ',LastName) AS CustomerName, Email AS CustomerEmail
            FROM LibraryCheckout
			INNER JOIN Customer ON Customer.CustomerID = LibraryCheckout.CustomerID
            WHERE LibraryItemID = ? 
            ORDER BY DateCheckedOut DESC") or die(mysqli_error($mysqli));
        $stmt->bind_param("i", $libraryItemID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($libraryCheckoutID, $customerID, $dateCheckedOut, $dateReturned, $dateDue, $customerName, $customerEmail);
       
        $checkouts = array();
        while ($stmt->fetch())
        {
            $checkout = new LibraryCheckout();
            $checkout->LibraryCheckoutID = $libraryCheckoutID;
            $checkout->LibraryItemID = $libraryItemID;
            $checkout->CustomerID = $customerID;
            $checkout->DateCheckedOut = $dateCheckedOut;
			$checkout->DateReturned = $dateReturned;
			$checkout->DateDue = $dateDue;
			$checkout->CustomerName = $customerName;
			$checkout->CustomerEmail = $customerEmail;
            $checkouts[] = $checkout;
        }
        $stmt->free_result();
        $stmt->close();
        $mysqli->close();
        return $checkouts; 
    }
	public function GetLibraryItem($libraryItemID)
	{
		$mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("SELECT `ItemName`, `LibraryItemTypeID`, `DateAdded` FROM `LibraryItem` WHERE `LibraryItemID` = ?");
        $stmt->bind_param("i", $libraryItemID);
        $stmt->execute();
        $stmt->bind_result($itemName, $libraryItemTypeID, $dateAdded);
        $stmt->fetch();
        $libraryItem = new LibraryItem();
		$libraryItem->LibraryItemID = $libraryItemID;
        $libraryItem->ItemName = $itemName;
        $libraryItem->LibraryItemTypeID = $libraryItemTypeID;
        $libraryItem->DateAdded = $dateAdded;
        return $libraryItem;
	}
    public function AddLibraryItem($itemName, $libraryItemTypeID, $dateAdded)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("INSERT INTO `LibraryItem` (`ItemName`, `LibraryItemTypeID`, `DateAdded`) VALUES (?,?,?)");
        $stmt->bind_param("sis", $itemName, $libraryItemTypeID, $dateAdded);
        $stmt->execute();
        $insertID = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();
        
        return $insertID;
    }
    public function UpdateLibraryItem($libraryItemID, $itemName, $libraryItemTypeID, $dateAdded)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("UPDATE `LibraryItem` SET `ItemName` = ?, `LibraryItemTypeID` = ?, `DateAdded` = ? WHERE `LibraryItemID` = ?");
        $stmt->bind_param("sisi", $itemName, $libraryItemTypeID, $dateAdded, $libraryItemID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        
        return ($affectedRows == 1) ? true : false;
    }
    public function DeleteLibraryItem($libraryItemID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `LibraryItem` WHERE `LibraryItemID` = ?");
        $stmt->bind_param("i", $libraryItemID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
    }	
	public function AddLibraryCheckout($libraryItemID, $customerID, $dateCheckedOut, $dateReturned, $dateDue)
	{
		$mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("INSERT INTO `LibraryCheckout` (`LibraryItemID`, `CustomerID`, `DateCheckedOut`, `DateReturned`, `DateDue`) VALUES (?,?,?,?,?)");
        $stmt->bind_param("iisss", $libraryItemID, $customerID, $dateCheckedOut, $dateReturned, $dateDue);
        $stmt->execute();
        $insertID = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();
        
        return $insertID;
	}
	public function UpdateLibraryCheckout($libraryCheckoutID, $libraryItemID, $customerID, $dateCheckedOut, $dateReturned, $dateDue)
	{
		$mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("UPDATE `LibraryCheckout` SET `LibraryItemID` = ?, `CustomerID` = ?, `DateCheckedOut` = ?, `DateReturned` = ?, `DateDue` = ? WHERE `LibraryCheckoutID` = ?");
        $stmt->bind_param("iisssi", $libraryItemID, $customerID, $dateCheckedOut, $dateReturned, $dateDue, $libraryCheckoutID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        
        return ($affectedRows == 1) ? true : false;
	}
	public function GetLibraryCheckout($libraryCheckoutID)
	{
		$mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("SELECT `LibraryItemID`, `CustomerID`, `DateCheckedOut`, `DateReturned`, `DateDue` FROM `LibraryCheckout` WHERE `LibraryCheckoutID`` = ?");
        $stmt->bind_param("i", $libraryCheckoutID);
        $stmt->execute();
        $stmt->bind_result($libraryItemID, $customerID, $dateCheckedOut, $dateReturned, $dateDue);
        $stmt->fetch();
        $libraryCheckout = new LibraryCheckout();
		$libraryCheckout->LibraryCheckoutID = $libraryCheckoutID;
		$libraryCheckout->LibraryItemID = $libraryItemID;
		$libraryCheckout->CustomerID = $customerID;
		$libraryCheckout->DateCheckedOut = $dateCheckedOut;
		$libraryCheckout->DateReturned = $dateReturned;
		$libraryCheckout->DateDue = $dateDue;
        return $libraryCheckout;
	}
	public function DeleteLibraryCheckout($libraryCheckoutID)
	{
		$mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `LibraryCheckout` WHERE `LibraryCheckoutID` = ?");
        $stmt->bind_param("i", $libraryCheckoutID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
	}
    
    // ========================================================================
	// Event Functions
	// ========================================================================
    public function GetAllEvents()
    {
        $mysqli = $this->GetSQLConnection();
        $result = $mysqli->query("SELECT * FROM Event ORDER BY EventDate DESC");
        $i = 0;
		$events = array();
        while ($event = $result->fetch_object("Event"))
        {
            $events[$i++] = $event;
        }
		$result->close();
		$mysqli->close();
        return $events;    
    }
    public function AddEvent($eventTypeID, $eventName, $cashboxLogStartID, $cashboxLogEndID, $eventDate)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("INSERT INTO `Event` (`EventTypeID`, `EventName`, `CashboxLogStartID`, `CashboxLogEndID`, `EventDate`) VALUES (?,?,?,?,?)");
        $stmt->bind_param("isiis", $eventTypeID, $eventName, $cashboxLogStartID, $cashboxLogEndID, $eventDate);
        $stmt->execute();
        $insertID = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();
        
        return $insertID;
    }
    public function UpdateEvent($eventID, $eventTypeID, $eventName, $eventDate)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("UPDATE `Event` SET `EventTypeID` = ?, `EventName` = ?, `EventDate` = ? WHERE `EventID` = ?");
        $stmt->bind_param("issi", $eventTypeID, $eventName, $eventDate, $eventID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        
        return ($affectedRows == 1) ? true : false;
    }
    public function DeleteEvent($eventID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `Event` WHERE `EventID` = ?");
        $stmt->bind_param("i", $eventID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
    }	
    public function GetEvent($eventID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("SELECT `EventID`, `EventTypeID`, `EventName`, `CashboxLogStartID`, `CashboxLogEndID`, `EventDate` FROM `Event` WHERE `EventID` = ?");
        $stmt->bind_param("i", $eventID);
        $stmt->execute();
        $stmt->bind_result($eventID, $eventTypeID, $eventName, $cashboxLogStartID, $cashboxLogEndID, $eventDate);
        $stmt->fetch();
        $event = new Event();
		$event->EventID = $eventID;
        $event->EventTypeID = $eventTypeID;
        $event->EventName = $eventName;
        $event->CashboxLogStartID = $cashboxLogStartID;
        $event->CashboxLogEndID = $cashboxLogEndID;
        $event->EventDate = $eventDate;
        return $event;
    }
    // ========================================================================
	// Attendance Functions
	// ========================================================================
    public function GetEventAttendance($eventID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("SELECT Customer.CustomerID, FirstName, LastName, Email, InMailingList, Balance, Birthday, IsStudent FROM Attendance INNER JOIN Customer ON Attendance.CustomerID = Customer.CustomerID WHERE EventID = ? ORDER BY FirstName, LastName");
        $stmt->bind_param("i", $eventID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($customerID, $firstName, $lastName, $email, $inMailingList, $balance, $birthday, $isStudent);
       
        $customers = array();
        while ($stmt->fetch())
        {
            $customer = new Customer();
            $customer->CustomerID = $customerID;
            $customer->FirstName = $firstName;
            $customer->LastName = $lastName;
            $customer->Email = $email;
            $customer->InMailingList = $inMailingList;
            $customer->Balance = $balance;
            $customer->Birthday = $birthday;
            $customer->IsStudent = $isStudent;
            $customers[] = $customer;
        }
        $stmt->free_result();
        $stmt->close();
        $mysqli->close();
        return $customers;   
    }
    public function GetCustomerAttendance($customerID)
    {   
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("
            SELECT Event.EventID, EventTypeID, EventName, CashboxLogStartID, CashboxLogEndID, EventDate
            FROM Attendance 
            INNER JOIN Event ON Attendance.EventID = Event.EventID 
            WHERE CustomerID = ? 
            ORDER BY EventDate DESC");
        $stmt->bind_param("i", $customerID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($eventID, $eventTypeID, $eventName, $cashboxLogStartID, $cashboxLogEndID, $eventDate);
       
        $events = array();
        while ($stmt->fetch())
        {
            $event = new Event();
            $event->EventID = $eventID;
            $event->EventTypeID = $eventTypeID;
            $event->EventName = $eventName;
            $event->CashboxLogStartID = $cashboxLogStartID;
            $event->CashboxLogEndID = $cashboxLogEndID;
            $event->EventDate = $eventDate;
            $events[] = $event;
        }
        $stmt->free_result();
        $stmt->close();
        $mysqli->close();
        return $events;     
    }
    public function AddAttendance($eventID, $customerID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("INSERT INTO `Attendance` (`EventID`, `CustomerID`) VALUES (?,?)");
        $stmt->bind_param("ii", $eventID, $customerID);
        $stmt->execute();
        $insertID = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();
        
        return $insertID;
    }
    public function DeleteAttendance($attendanceID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `Attendance` WHERE `AttendanceID` = ?");
        $stmt->bind_param("i", $attendanceID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
    }	
	
    // ========================================================================
	// Transaction Functions
	// ========================================================================
    public function AddTransaction($transactionTypeID, $transactionNote, $punchCardID, $classEnrollmentID, $isVolunteer, $attendanceID, $price)
    {
        $isVolunteerInt = ($isVolunteer == true) ? 1 : 0;
    
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("INSERT INTO `Transaction` (TransactionTypeID, TransactionNote, PunchCardID, ClassEnrollmentID, IsVolunteer, AttendanceID, Price) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("isiiiid", $transactionTypeID, $transactionNote, $punchCardID, $classEnrollmentID, $isVolunteerInt, $attendanceID, $price);
        $stmt->execute();
        $insertID = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();
        
        return $insertID;
    }
    public function DeleteTransaction($transactionID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `Transaction` WHERE `TransactionID` = ?");
        $stmt->bind_param("i", $transactionID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
    }
    public function UpdateTransaction($transactionID, $transactionTypeID, $transactionNote, $punchCardID, $classEnrollmentID, $isVolunteer, $attendanceID, $price)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("UPDATE `Transaction` SET `TransactionTypeID` = ?, `TransactionNote` = ?, `PunchCardID` = ?, `ClassEnrollmentID` = ?, `IsVolunteer` = ?, `AttendanceID` = ?, `Price` = ? WHERE `TransactionID` = ?");
        $stmt->bind_param("isiiiidi", $transactionTypeID, $transactionNote, $punchCardID, $classEnrollmentID, $isVolunteer, $attendanceID, $price, $transactionID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        
        return ($affectedRows == 1) ? true : false;
    }
    public function GetTransactions($attendanceID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("
            SELECT 
                Transaction.TransactionID, TransactionTypeID, TransactionNote, PunchCardID, 
                ClassEnrollmentID, IsVolunteer, Transaction.AttendanceID, Price
            FROM Attendance 
            INNER JOIN Transaction ON Attendance.AttendanceID = Transaction.AttendanceID 
            WHERE Attendance.AttendanceID = ? 
            ORDER BY Transaction.TransactionID") or die($mysqli->error);
        $stmt->bind_param("i", $attendanceID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result(
            $transactionID, $transactionTypeID, $transactionNote, $punchCardID, 
            $classEnrollmentID, $isVolunteer, $attendanceID, $price);
       
        $transactions = array();
        while ($stmt->fetch())
        {
            $transaction = new Transaction();
            $transaction->TransactionID = $transactionID;
            $transaction->TransactionTypeID = $transactionTypeID;
            $transaction->TransactionNote = $transactionNote;
            $transaction->PunchCardID = $punchCardID;
            $transaction->ClassEnrollmentID = $classEnrollmentID;
            $transaction->IsVolunteer = $isVolunteer;
            $transaction->AttendanceID = $attendanceID;
            $transaction->Price = $price;
            $transactions[] = $transaction;
        }
        $stmt->free_result();
        $stmt->close();
        $mysqli->close();
        return $transactions;
    }
    public function AddPayment($transactionID, $paymentTypeID, $paymentAmount)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("INSERT INTO `Payment` (`TransactionID`, `PaymentTypeID`, `PaymentAmount`) VALUES (?,?,?)") or die($mysqli->error);
        $stmt->bind_param("iid", $transactionID, $paymentTypeID, $paymentAmount);
        $stmt->execute();
        $insertID = $stmt->insert_id;
        $stmt->close();
        $mysqli->close();
        
        return $insertID;
    }
    public function DeletePayment($paymentID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("DELETE FROM `Payment` WHERE `PaymentID` = ?");
        $stmt->bind_param("i", $paymentID);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        $mysqli->close();
        return ($affectedRows == 1) ? true : false;
    }
    public function GetPayments($transactionID)
    {
        $mysqli = $this->GetSQLConnection();
        $stmt = $mysqli->prepare("
            SELECT Payment.PaymentID, Payment.TransactionID, PaymentTypeID, PaymentAmount
            FROM Transaction 
            INNER JOIN Payment ON Transaction.TransactionID = Payment.TransactionID 
            WHERE Transaction.TransactionID = ? 
            ORDER BY Payment.PaymentID") or die($mysqli->error);
        $stmt->bind_param("i", $transactionID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($paymentID, $transactionID, $paymentTypeID, $paymentAmount);
       
        $payments = array();
        while ($stmt->fetch())
        {
            $payment = new Payment();
            $payment->PaymentID = $paymentID;
            $payment->TransactionID = $transactionID;
            $payment->PaymentTypeID = $paymentTypeID;
            $payment->PaymentAmount = $paymentAmount;
            $payments[] = $payment;
        }
        $stmt->free_result();
        $stmt->close();
        $mysqli->close();
        return $payments;
    }
}

$dataAccessor = new MySQLDataAccessor();

?>