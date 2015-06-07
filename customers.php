<? 
include('Controls/header.php');
include_once('BusinessLogic/customerLogic.php');
include_once('Controls/DataGrid.php');

// handle action buttons
if (isset($_POST['action']) && $_POST['action'] == "Add") 
{	
    CustomerLogic::AddCustomer(
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['email'],
        isset($_POST['inMailingList']) ? true : false,
        $_POST['balance'],
        $_POST['birthday'],
        isset($_POST['isStudent']) ? true : false);
     
}
else if (isset($_POST['action']) && $_POST['action'] == "Delete")
{
    CustomerLogic::DeleteCustomer($_POST['deleteID']);
}
else if (isset($_POST['action']) && $_POST['action'] == "Save")
{
    CustomerLogic::UpdateCustomer(
		$_POST['editID'],
		$_POST['firstName'],
		$_POST['lastName'],
        $_POST['email'],
        isset($_POST['inMailingList']) ? true : false,
        $_POST['balance'],
        $_POST['birthday'],
        isset($_POST['isStudent']) ? true : false);
}
?>

<script>

function validate(firstName, lastName)
{
	if (firstName == "" || lastName == "")
	{
		alert("Please enter a first and last name.");
		return false;
	}
	return true;
}
function validateForm()
{
	var firstName = $("#firstName").val();
	var lastName = $("#lastName").val();
	return validate(firstName, lastName);
}
function validateEdit(row)
{
	// validate the name
	var firstName = $(row).find("input[name=FirstName]").val();
	var lastName = $(row).find("input[name=LastName]").val();
	return validate(firstName, lastName);
}
function storeEdit(row)
{
	// store values in the form
	$("#firstName").val( $(row).find("input[name=FirstName]").val() );
	$("#lastName").val( $(row).find("input[name=LastName]").val() );
	$("#email").val( $(row).find("input[name=Email]").val() );
	$("#inMailingList").prop('checked', $(row).find("input[name=InMailingList]").prop('checked') );
	$("#balance").val( $(row).find("input[name=Balance]").val() );
	$("#birthday").val( $(row).find("input[name=Birthday]").val() );
	$("#isStudent").prop('checked', $(row).find("input[name=IsStudent]").prop('checked') );
}

</script>

<form id="customerForm" method="post">
    <input type="hidden" id="deleteID" name="deleteID" />
    <input type="hidden" id="editID" name="editID" />
	<input type="hidden" id="action" name="action" />

	<fieldset>
		<legend>Add New Customer</legend>
		<table>
			<tr>
				<td><input id="firstName" name="firstName" type="text" /><br /><label for="firstName">First Name</label></td>
				<td><input id="lastName" name="lastName" type="text" /><br /><label for="lastName">Last Name</label></td>
				<td><input id="email" name="email" type="text" /><br /><label for="email">Email Address</label></td>
				<td><input id="inMailingList" name="inMailingList" type="checkbox" /><label for="inMailingList">In Mailing List?</label></td>
			</tr>
			<tr>  
				<td><input id="balance" name="balance" type="text" value=0 /><br /><label for="balance">Balance</label></td>
				<td><input id="birthday" name="birthday" type="date" /><br /><label for="birthday">Birthday</label></td>
				<td><input id="isStudent" name="isStudent" type="checkbox" /><label for="isStudent">Is Student</label></td>
				<td><input type="button" onclick="setAdd('customerForm',validateForm);" value="Add"></td>
			</tr>
		</table>
	</fieldset>

</form>

<?

// setup datagrid
$customerGrid = new DataGrid();
$customerGrid->DataSource = CustomerLogic::GetAllCustomers();
$customerGrid->DataSourceClassName = "Customer";
$customerGrid->IDPropertyName = "CustomerID";
$customerGrid->FormID = "customerForm";
$customerGrid->ValidateEditCallback = "validateEdit";
$customerGrid->StoreEditCallback = "storeEdit";

// setup data columns
$idCol = $customerGrid->AddColumn("CustomerID", DataColumnType::NumberColumn, "CustomerID");
$idCol->ReadOnly = true;
$firstNameCol = $customerGrid->AddColumn("First Name", DataColumnType::StringColumn, "FirstName");
$firstNameCol->Hyperlink = "customer.php";
$customerGrid->AddColumn("Last Name", DataColumnType::StringColumn, "LastName");
$emailCol = $customerGrid->AddColumn("Email Address", DataColumnType::StringColumn, "Email");
$emailCol->Size = 20;
$customerGrid->AddColumn("In Mailing List?", DataColumnType::CheckboxColumn, "InMailingList");
$customerGrid->AddColumn("Balance", DataColumnType::NumberColumn, "Balance");
$customerGrid->AddColumn("Birthday", DataColumnType::DateColumn, "Birthday");
$customerGrid->AddColumn("Is Student?", DataColumnType::CheckboxColumn, "IsStudent");

// draw!
$customerGrid->Render();

include('Controls/footer.php');
?>