<? 
include('Controls/header.php');
include_once('BusinessLogic/punchCardLogic.php');
include_once('Controls/Lookup.php');
include_once('Controls/DataGrid.php');

// handle action buttons
if (isset($_POST['action']) && $_POST['action'] == "Add") 
{	
    PunchCardLogic::AddPunchCard(
        $_POST['customerID'],
        $_POST['date'],
		$_POST['cardNumber']);
     
}
else if (isset($_POST['action']) && $_POST['action'] == "Delete")
{
    PunchCardLogic::DeletePunchCard($_POST['deleteID']);
}
else if (isset($_POST['action']) && $_POST['action'] == "Save")
{
    PunchCardLogic::UpdatePunchCard(
		$_POST['editID'],
		$_POST['customerID'],
		$_POST['date'],
		$_POST['cardNumber']);
}
?>

<script>

function validate(customerID, date, cardNumber)
{
	if (customerID == "" || date == "" || cardNumber == "")
	{
		alert("Please enter all fields.");
		return false;
	}
	return true;
}
function validateForm()
{
	var customerID = $("#customerID option:selected").val();
	var date = $("#date").val();
	var cardNumber= $("#cardNumber").val();
	return validate(customerID, date);
}

function validateEdit(row)
{
	var customerID = $(row).find("select[name=CustomerID] option:selected").val();
	var date = $(row).find("input[name=PurchaseDate]").val();
	var cardNumber = $(row).find("input[name=CardNumber]").val();
	return validate(customerID, date);
}

function storeEdit(row)
{
	// store values in the form
	$("#customerID").val( $(row).find("select[name=CustomerID] option:selected").val() );
	$("#date").val( $(row).find("input[name=PurchaseDate]").val() );
	$("#cardNumber").val($(row).find("input[name=CardNumber]").val() );
}


</script>

<form id="punchCardForm" method="post">
    <input type="hidden" id="deleteID" name="deleteID" />
    <input type="hidden" id="editID" name="editID" />
	<input type="hidden" id="action" name="action" />

	<fieldset>
		<legend>Add New Punch Card</legend>
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
				<td><input id="date" name="date" type="date" /><br /><label for="date">Purchase Date</label></td>
				<td><input id="cardNumber" name="cardNumber" type="number" /><br /><label for="cardNumber">Card Number</label></td>
				<td><input type="button" onclick="setAdd('punchCardForm',validateForm);" value="Add"></td>
			</tr>
		</table>
	</fieldset>

</form>

<?

// setup datagrid
$punchCardGrid = new DataGrid();
$punchCardGrid->DataSource = PunchCardLogic::GetAllPunchCards();
$punchCardGrid->DataSourceClassName = "PunchCard";
$punchCardGrid->IDPropertyName = "PunchCardID";
$punchCardGrid->FormID = "punchCardForm";
$punchCardGrid->ValidateEditCallback = "validateEdit";
$punchCardGrid->StoreEditCallback = "storeEdit";

// setup data columns
$idCol = $punchCardGrid->AddColumn("PunchCardID", DataColumnType::NumberColumn, "PunchCardID");
$idCol->ReadOnly = true;
$customerCol = $punchCardGrid->AddColumn("Customer", DataColumnType::LookupColumn, "CustomerID");
$customerCol->SetupLookup("Customer", "CONCAT(FirstName, ' ', LastName)");
$punchCardGrid->AddColumn("Purchase Date", DataColumnType::DateColumn, "PurchaseDate");
$punchCardGrid->AddColumn("Card Number", DataColumnType::NumberColumn, "CardNumber");

// draw!
$punchCardGrid->Render();

include('Controls/footer.php');
?>