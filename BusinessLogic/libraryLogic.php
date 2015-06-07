<?

include_once('DataAccess/MySQLDataAccessor.php');

class LibraryItemLogic
{
    public static function GetAllLibraryItems()
    {
        global $dataAccessor;
        return $dataAccessor->GetAllLibraryItems();
    }
    
	public static function GetLibraryItem($libraryItemID)
	{
		global $dataAccessor;
        return $dataAccessor->GetLibraryItem($libraryItemID);
	}
	
    public static function AddLibraryItem($itemName, $libraryItemTypeID, $dateAdded)
    {
        global $dataAccessor;
        if ($itemName == "" || $libraryItemTypeID == "" || $dateAdded == "")
        {
            return null;
        }
        
        $libraryItemID = $dataAccessor->AddLibraryItem($itemName, $libraryItemTypeID, $dateAdded);   
        return $libraryItemID;
    }
    
    public static function UpdateLibraryItem($libraryItemID, $itemName, $libraryItemTypeID, $dateAdded)
    {
        global $dataAccessor;
               
        $success = $dataAccessor->UpdateLibraryItem($libraryItemID, $itemName, $libraryItemTypeID, $dateAdded);
        return $success;
    }
    
    public static function DeleteLibraryItem($libraryItemID)
    {
        global $dataAccessor;
        $success = $dataAccessor->DeleteLibraryItem($libraryItemID);
        return $success;
    }
	
	public static function CheckoutItem($libraryItemID, $customerID)
	{
		global $dataAccessor;
		
		// get due date based on type
		$libraryItemTypes = $dataAccessor->LoadLookup("LibraryItemType", "LibraryItemTypeID", "CheckoutLength", null);
		$libraryItem = LibraryItemLogic::GetLibraryItem($libraryItemID);
		$checkoutLength = 0;
		for ($i = 0; $i < count($libraryItemTypes); $i++)
		{
			if ($libraryItemTypes[$i]->Key == $libraryItem->LibraryItemTypeID)
			{
				$checkoutLength = $libraryItemTypes[$i]->Value;
				break;
			}
		}
		$dueDate = date("Y-m-d", strtotime(date("Y-m-d") . " +$checkoutLength days"));
		
		$libraryCheckoutID = $dataAccessor->AddLibraryCheckout(
			$libraryItemID, 
			$customerID,
			date("Y-m-d"),
			null,
			$dueDate);
			
		return $libraryCheckoutID;
	}
	
	public static function UpdateLibraryCheckout($libraryCheckoutID, $libraryItemID, $customerID, $dateCheckedOut, $dateReturned, $dueDate)
	{
		global $dataAccessor;
		$success = $dataAccessor->UpdateLibraryCheckout(
			$libraryCheckoutID, 
			$libraryItemID, 
			$customerID,
			$dateCheckedOut,
			$dateReturned,
			$dueDate);
		return $success;
	}
	
	public static function DeleteLibraryCheckout($libraryCheckoutID)
	{
		global $dataAccessor;
		$success = $dataAccessor->DeleteLibraryCheckout($libraryCheckoutID);
		return $success;
	}
	
	public static function GetLibraryCheckouts($libraryItemID)
	{
		global $dataAccessor;
		$libraryCheckouts = $dataAccessor->GetLibraryCheckouts($libraryItemID);
		return $libraryCheckouts;
	}
}

?>