<? 
include('Controls/header.php');
include_once('BusinessLogic/classLogic.php');
include_once('Controls/DataGrid.php');
include_once('Controls/Lookup.php');

$classID = $_GET["ID"];
$currentClass = ClassLogic::GetClass($classID);
print("<h1>Class: {$currentClass->ClassName}</h1>");
print("<b>ClassID: </b>{$currentClass->ClassID}<br />");
print("<b>Start Date: </b>{$currentClass->StartDate}<br />");
print("<b>End Date: </b>{$currentClass->EndDate}<br />");

// handle action buttons
if (isset($_POST['action']) && $_POST['action'] == "Add") 
{	
    ClassLogic::EnrollInClass(
        $classID,
        $_POST['customerID']);
     
}
else if (isset($_POST['action']) && $_POST['action'] == "Delete")
{
    ClassLogic::DeleteEnrollment($_POST['deleteID']);
}

?>

<script>

function validate(classID, customerID)
{
	if (classID == "" || customerID == "")
	{
		alert("Please enter all fields.");
		return false;
	}
	return true;
}
function validateForm()
{
	var classID = $("#classID").val();
	var customerID = $("#customerID").val();
	return validate(classID, customerID);
}

function validateEdit(row)
{
	var classID = $(row).find("input[name=ClassID]").val();
	var customerID = $(row).find("input[name=CustomerID]").val();
	return validate(classID, customerID);
}

function storeEdit(row)
{
	// store values in the form
	$("#classID").val( $(row).find("input[name=ClassID]").val() );
	$("#customerID").val( $(row).find("input[name=CustomerID]").val() );
}


</script>

<form id="enrollmentForm" method="post">
    <input type="hidden" id="deleteID" name="deleteID" />
    <input type="hidden" id="editID" name="editID" />
	<input type="hidden" id="action" name="action" />

	<fieldset>
		<legend>Enroll in Class</legend>
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
				<td><input type="button" onclick="setAdd('enrollmentForm',validateForm);" value="Add"></td>
			</tr>
		</table>
	</fieldset>

</form>

<?

// setup datagrid
$classGrid = new DataGrid();
$classGrid->DataSource = ClassLogic::GetClassRoster($classID);
$classGrid->DataSourceClassName = "ClassEnrollment";
$classGrid->IDPropertyName = "ClassEnrollmentID";
$classGrid->FormID = "enrollmentForm";
$classGrid->ValidateEditCallback = "validateEdit";
$classGrid->StoreEditCallback = "storeEdit";

// setup data columns
$idCol = $classGrid->AddColumn("ClassEnrollmentID", DataColumnType::NumberColumn, "ClassEnrollmentID");
$idCol->ReadOnly = true;
$classGrid->AddColumn("Customer", DataColumnType::StringColumn, "Name");

// draw!
$classGrid->ShowEdit = false;
$classGrid->Render();

include('Controls/footer.php');
?>