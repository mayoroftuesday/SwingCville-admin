<? 
include('Controls/header.php');
include_once('BusinessLogic/classLogic.php');
include_once('Controls/DataGrid.php');

// handle action buttons
if (isset($_POST['action']) && $_POST['action'] == "Add") 
{	
    ClassLogic::AddClass(
        $_POST['className'],
        $_POST['startDate'],
        $_POST['endDate']);
     
}
else if (isset($_POST['action']) && $_POST['action'] == "Delete")
{
    ClassLogic::DeleteClass($_POST['deleteID']);
}
else if (isset($_POST['action']) && $_POST['action'] == "Save")
{
    ClassLogic::UpdateClass(
		$_POST['editID'],
		$_POST['className'],
		$_POST['startDate'],
        $_POST['endDate']);
}
?>

<script>

function validate(className, startDate, endDate)
{
	if (className == "" || startDate == "" || endDate == "")
	{
		alert("Please enter all fields.");
		return false;
	}
	return true;
}
function validateForm()
{
	var className = $("#className").val();
	var startDate = $("#startDate").val();
	var endDate = $("#endDate").val();
	return validate(className, startDate, endDate);
}

function validateEdit(row)
{
	var className = $(row).find("input[name=ClassName]").val();
	var startDate = $(row).find("input[name=StartDate]").val();
	var endDate = $(row).find("input[name=EndDate]").val();
	return validate(className, startDate, endDate);
}

function storeEdit(row)
{
	// store values in the form
	$("#className").val( $(row).find("input[name=ClassName]").val() );
	$("#startDate").val( $(row).find("input[name=StartDate]").val() );
	$("#endDate").val( $(row).find("input[name=EndDate]").val() );
}


</script>

<form id="classForm" method="post">
    <input type="hidden" id="deleteID" name="deleteID" />
    <input type="hidden" id="editID" name="editID" />
	<input type="hidden" id="action" name="action" />

	<fieldset>
		<legend>Add New Class</legend>
		<table>
			<tr>
				<td><input id="className" name="className" type="text" /><br /><label for="className">Class Name</label></td>
				<td><input id="startDate" name="startDate" type="date" /><br /><label for="startDate">Start Date</label></td>
				<td><input id="endDate" name="endDate" type="date" /><br /><label for="endDate">End Date</label></td>
				<td><input type="button" onclick="setAdd('classForm',validateForm);" value="Add"></td>
			</tr>
		</table>
	</fieldset>

</form>

<?

// setup datagrid
$classGrid = new DataGrid();
$classGrid->DataSource = ClassLogic::GetAllClasses();
$classGrid->DataSourceClassName = "LessonSeries";
$classGrid->IDPropertyName = "ClassID";
$classGrid->FormID = "classForm";
$classGrid->ValidateEditCallback = "validateEdit";
$classGrid->StoreEditCallback = "storeEdit";

// setup data columns
$idCol = $classGrid->AddColumn("ClassID", DataColumnType::NumberColumn, "ClassID");
$idCol->ReadOnly = true;
$classNameCol = $classGrid->AddColumn("Class Name", DataColumnType::StringColumn, "ClassName");
$classNameCol->Hyperlink = "class.php";
$classGrid->AddColumn("Start Date", DataColumnType::DateColumn, "StartDate");
$classGrid->AddColumn("End Date", DataColumnType::DateColumn, "EndDate");

// draw!
$classGrid->Render();

include('Controls/footer.php');
?>