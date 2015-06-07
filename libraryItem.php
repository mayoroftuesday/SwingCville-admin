<? 
include('Controls/header.php');
include_once('BusinessLogic/libraryLogic.php');
include_once('Controls/DataGrid.php');
include_once('Controls/Lookup.php');

$libraryItemID = $_GET["ID"];
$currentLibraryItem = LibraryItemLogic::GetLibraryItem($libraryItemID);
print("<h1>Library Item: {$currentLibraryItem->ItemName}</h1>");
print("<b>Item Type: </b>{$currentLibraryItem->LibraryItemTypeID}<br />");
print("<b>Date Added: </b>{$currentLibraryItem->DateAdded}<br />");


// handle action buttons
if (isset($_POST['action']) && $_POST['action'] == "Add") 
{	
    LibraryItemLogic::CheckoutItem(
        $libraryItemID,
        $_POST['customerID']);
     
}

else if (isset($_POST['action']) && $_POST['action'] == "Delete")
{
    LibraryItemLogic::DeleteLibraryCheckout($_POST['deleteID']);
}
else if (isset($_POST['action']) && $_POST['action'] == "Save")
{
    LibraryItemLogic::UpdateLibraryCheckout(
		$_POST['editID'],
		$libraryItemID,
		$_POST['customerID'],
        $_POST['dateCheckedOut'],
		$_POST['dateReturned'],
		$_POST['dateDue']);
}

?>

<script>

function validate()
{
	return true;
}
function validateForm()
{
	return validate();
}

function validateEdit(row)
{
	return validate();
}

function storeEdit(row)
{
	// store values in the form
	$("#customerID").val( $(row).find("input[name=CustomerID]").val() );
	$("#dateCheckedOut").val( $(row).find("input[name=DateCheckedOut]").val() );
	$("#dateReturned").val( $(row).find("input[name=DateReturned]").val() );
	$("#dateDue").val( $(row).find("input[name=DateDue]").val() );
}


</script>
<form id="libraryCheckoutForm" method="post">
    <input type="hidden" id="deleteID" name="deleteID" />
    <input type="hidden" id="editID" name="editID" />
	<input type="hidden" id="action" name="action" />
	<input type="hidden" id="dateCheckedOut" name="dateCheckedOut" />
	<input type="hidden" id="dateReturned" name="dateReturned" />
	<input type="hidden" id="dateDue" name="dateDue" />

	<fieldset>
		<legend>Check out</legend>
		<table>
			<tr>
				<td>
					<select id="customerID" name="customerID">
					<?
						$lookup = new Lookup("Customer", "CustomerID", "CONCAT(FirstName, ' ', LastName)");
						$lookup->Render();
					?>
					</select>
					<br />
					<label for="customerID">Customer</label>
				</td>
				<td><input type="button" onclick="setAdd('libraryCheckoutForm',validateForm);" value="Check Out"></td>
			</tr>
		</table>
	</fieldset>

</form>

<?

// setup datagrid
$checkoutGrid = new DataGrid();
$checkoutGrid->DataSource = LibraryItemLogic::GetLibraryCheckouts($libraryItemID);
$checkoutGrid->DataSourceClassName = "LibraryCheckout";
$checkoutGrid->IDPropertyName = "LibraryCheckoutID";
$checkoutGrid->FormID = "libraryCheckoutForm";
$checkoutGrid->ValidateEditCallback = "validateEdit";
$checkoutGrid->StoreEditCallback = "storeEdit";

// setup data columns
$idCol = $checkoutGrid->AddColumn("LibraryCheckoutID", DataColumnType::NumberColumn, "LibraryCheckoutID");
$idCol->ReadOnly = true;
$customerCol = $checkoutGrid->AddColumn("CustomerID", DataColumnType::NumberColumn, "CustomerID");
$customerCol->ReadOnly = true;
$customerNameCol = $checkoutGrid->AddColumn("Customer", DataColumnType::StringColumn, "CustomerName");
$customerNameCol->ReadOnly = true;
$customerEmailCol = $checkoutGrid->AddColumn("Email", DataColumnType::StringColumn, "CustomerEmail");
$customerEmailCol->ReadOnly = true;
$dateCheckedOutCol = $checkoutGrid->AddColumn("Date Checked Out", DataColumnType::DateColumn, "DateCheckedOut");
$dateCheckedOutCol->ReadOnly = true;
$checkoutGrid->AddColumn("Date Returned", DataColumnType::DateColumn, "DateReturned");
$dateDueCol = $checkoutGrid->AddColumn("Date Due", DataColumnType::DateColumn, "DateDue");
$dateDueCol->ReadOnly = true;

// draw!
$checkoutGrid->Render();

include('Controls/footer.php');
?>