<? 
include('Controls/header.php');
include_once('BusinessLogic/cashBoxLogic.php');
include_once('Controls/DataGrid.php');

// handle action buttons
if (isset($_POST['action']) && $_POST['action'] == "Add") 
{	
    CashboxLogLogic::AddCashboxLog(
        $_POST['cashboxCount'],
        $_POST['cashboxNote']);
     
}
else if (isset($_POST['action']) && $_POST['action'] == "Delete")
{
    CashboxLogLogic::DeleteCashboxLog($_POST['deleteID']);
}
else if (isset($_POST['action']) && $_POST['action'] == "Save")
{
    CashboxLogLogic::UpdateCashboxLog(
		$_POST['editID'],
        $_POST['cashboxCount'],
        $_POST['cashboxNote']);
}
?>

<script>

function validate(cashboxCount)
{
	if (cashboxCount == "")
	{
		alert("Please enter count.");
		return false;
	}
	if (isNaN(cashboxCount))
	{
		alert("Please enter a valid number for cashbox count.");
		return false;
	}
	return true;
}

function validateForm()
{
	var cashboxCount = $("#cashboxCount").val();
	return validate(cashboxCount);
}

function validateEdit(row)
{
	var cashboxCount = $(row).find("input[name=CashboxCount]").val();
	return validate(cashboxCount);
}

function storeEdit(row)
{
	// store values in the form
	$("#cashboxCount").val( $(row).find("input[name=CashboxCount]").val() );
	$("#cashboxNote").val( $(row).find("input[name=CashboxNote]").val() );
}


</script>

<form id="cashboxLogForm" method="post">
    <input type="hidden" id="deleteID" name="deleteID" />
    <input type="hidden" id="editID" name="editID" />
	<input type="hidden" id="action" name="action" />

	<fieldset>
		<legend>Add New Cashbox Count</legend>
		<table>
			<tr>
				<td><input id="cashboxCount" name="cashboxCount" type="number" /><br /><label for="cashboxCount">Count</label></td>
				<td><input id="cashboxNote" name="cashboxNote" type="text" /><br /><label for="cashboxNote">Note</label></td>
				<td><input type="button" onclick="setAdd('cashboxLogForm',validateForm);" value="Add"></td>
			</tr>
		</table>
	</fieldset>

</form>

<?

// setup datagrid
$cashboxLogGrid = new DataGrid();
$cashboxLogGrid->DataSource = CashboxLogLogic::GetAllCashboxLogs();
$cashboxLogGrid->DataSourceClassName = "CashboxLog";
$cashboxLogGrid->IDPropertyName = "CashboxLogID";
$cashboxLogGrid->FormID = "cashboxLogForm";
$cashboxLogGrid->ValidateEditCallback = "validateEdit";
$cashboxLogGrid->StoreEditCallback = "storeEdit";

// setup data columns
$idCol = $cashboxLogGrid->AddColumn("CashboxLogID", DataColumnType::NumberColumn, "CashboxLogID");
$idCol->ReadOnly = true;
$cashboxLogGrid->AddColumn("Count", DataColumnType::NumberColumn, "CashboxCount");
$cashboxLogGrid->AddColumn("Note", DataColumnType::StringColumn, "CashboxNote");
$dateCol = $cashboxLogGrid->AddColumn("Date", DataColumnType::StringColumn, "Date");
$dateCol->ReadOnly = true;

// draw!
$cashboxLogGrid->Render();

include('Controls/footer.php');
?>