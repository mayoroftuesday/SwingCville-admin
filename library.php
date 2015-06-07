<? 
include('Controls/header.php');
include_once('BusinessLogic/libraryLogic.php');
include_once('Controls/Lookup.php');
include_once('Controls/DataGrid.php');

// handle action buttons
if (isset($_POST['action']) && $_POST['action'] == "Add") 
{	
    LibraryItemLogic::AddLibraryItem(
        $_POST['itemName'],
        $_POST['libraryItemTypeID'],
		$_POST['dateAdded']);
     
}
else if (isset($_POST['action']) && $_POST['action'] == "Delete")
{
    LibraryItemLogic::DeleteLibraryItem($_POST['deleteID']);
}
else if (isset($_POST['action']) && $_POST['action'] == "Save")
{
    LibraryItemLogic::UpdateLibraryItem(
		$_POST['editID'],
        $_POST['itemName'],
        $_POST['libraryItemTypeID'],
		$_POST['dateAdded']);
}
?>

<script>

function validate(itemName, libraryItemTypeID, dateAdded)
{
	if (itemName == "" || libraryItemTypeID == "" || dateAdded == "")
	{
		alert("Please enter all fields.");
		return false;
	}
	return true;
}
function validateForm()
{
	var itemName = $("#itemName").val();
	var libraryItemTypeID = $("#libraryItemTypeID option:selected").val();
	var dateAdded = $("#dateAdded").val();
	return validate(itemName, libraryItemTypeID, dateAdded);
}

function validateEdit(row)
{
	var itemName = $(row).find("input[name=ItemName]").val();
	var libraryItemTypeID = $(row).find("select[name=LibraryItemTypeID] option:selected").val();
	var dateAdded = $(row).find("input[name=DateAdded]").val();
	return validate(itemName, libraryItemTypeID, dateAdded);
}

function storeEdit(row)
{
	// store values in the form
	$("#itemName").val( $(row).find("input[name=ItemName]").val() );
	$("#libraryItemTypeID").val( $(row).find("select[name=LibraryItemTypeID] option:selected").val() );
	$("#dateAdded").val( $(row).find("input[name=DateAdded]").val() );
}


</script>

<form id="libraryItemForm" method="post">
    <input type="hidden" id="deleteID" name="deleteID" />
    <input type="hidden" id="editID" name="editID" />
	<input type="hidden" id="action" name="action" />

	<fieldset>
		<legend>Add New Library Item</legend>
		<table>
			<tr>
				<td><input id="itemName" name="itemName" type="text" /><br /><label for="date">Item Name</label></td>
				<td>
					<select id="libraryItemTypeID" name="libraryItemTypeID">
					<?
						$lookup = new Lookup("LibraryItemType", "LibraryItemTypeID", "LibraryItemTypeName");
						$lookup->Render();
					?>
					</select>
					<br />
					<label for="libraryItemTypeID">Library Item Type</label>
				</td>
				
				<td><input id="dateAdded" name="dateAdded" type="date" /><br /><label for="dateAdded">Date Added</label></td>
				<td><input type="button" onclick="setAdd('libraryItemForm',validateForm);" value="Add"></td>
			</tr>
		</table>
	</fieldset>

</form>

<?

// setup datagrid
$libraryItemGrid = new DataGrid();
$libraryItemGrid->DataSource = LibraryItemLogic::GetAllLibraryItems();
$libraryItemGrid->DataSourceClassName = "LibraryItem";
$libraryItemGrid->IDPropertyName = "LibraryItemID";
$libraryItemGrid->FormID = "libraryItemForm";
$libraryItemGrid->ValidateEditCallback = "validateEdit";
$libraryItemGrid->StoreEditCallback = "storeEdit";

// setup data columns
$idCol = $libraryItemGrid->AddColumn("LibraryItemID", DataColumnType::NumberColumn, "LibraryItemID");
$idCol->ReadOnly = true;
$itemNameCol = $libraryItemGrid->AddColumn("Item Name", DataColumnType::StringColumn, "ItemName");
$itemNameCol->Hyperlink = "libraryItem.php";
$customerCol = $libraryItemGrid->AddColumn("Library Item Type", DataColumnType::LookupColumn, "LibraryItemTypeID");
$customerCol->SetupLookup("LibraryItemType", "LibraryItemTypeName");
$libraryItemGrid->AddColumn("Date Added", DataColumnType::DateColumn, "DateAdded");


// draw!
$libraryItemGrid->Render();

include('Controls/footer.php');
?>