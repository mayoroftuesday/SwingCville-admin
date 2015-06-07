<? 
include('Controls/header.php');
include_once('BusinessLogic/eventsLogic.php');
include_once('Controls/DataGrid.php');

// handle action buttons
if (isset($_POST['action']) && $_POST['action'] == "Add") 
{	
    EventLogic::AddEvent(
        $_POST['eventTypeID'],
        $_POST['eventName'],
        $_POST['eventDate']);
     
}
else if (isset($_POST['action']) && $_POST['action'] == "Delete")
{
    EventLogic::DeleteEvent($_POST['deleteID']);
}
else if (isset($_POST['action']) && $_POST['action'] == "Save")
{
    EventLogic::UpdateEvent(
		$_POST['editID'],
        $_POST['eventTypeID'],
		$_POST['eventName'],
        $_POST['eventDate']);
}
?>

<script>

function validate(eventTypeID, eventName, eventDate)
{
	if (eventTypeID == "" || eventName == "" || eventDate == "")
	{
		alert("Please enter all fields.");
		return false;
	}
	return true;
}
function validateForm()
{
	var eventTypeID = $("#eventTypeID").val();
	var eventName = $("#eventName").val();
	var eventDate = $("#eventDate").val();
	return validate(eventTypeID, eventName, eventDate);
}

function validateEdit(row)
{
	var eventTypeID = $(row).find("select[name=EventTypeID] option:selected").val();
	var eventName = $(row).find("input[name=EventName]").val();
	var eventDate = $(row).find("input[name=EventDate]").val();
	return validate(eventTypeID, eventName, eventDate);
}

function storeEdit(row)
{
	// store values in the form
	$("#eventTypeID").val( $(row).find("select[name=EventTypeID] option:selected").val() );
	$("#eventName").val( $(row).find("input[name=EventName]").val() );
	$("#eventDate").val( $(row).find("input[name=EventDate]").val() );
}


</script>

<form id="eventForm" method="post">
    <input type="hidden" id="deleteID" name="deleteID" />
    <input type="hidden" id="editID" name="editID" />
	<input type="hidden" id="action" name="action" />

	<fieldset>
		<legend>Add New Event</legend>
		<table>
			<tr>
				<td>
                    <select id="eventTypeID" name="eventTypeID">
					<?
						$lookup = new Lookup("EventType", "EventTypeID", "EventTypeName");
						$lookup->Render();
					?>
					</select>
					<br />
					<label for="eventTypeID">Event Type</label>
                </td>
				<td><input id="eventName" name="eventName" type="text" /><br /><label for="eventName">Event Name</label></td>
				<td><input id="eventDate" name="eventDate" type="date" /><br /><label for="eventDate">Event Date</label></td>
				<td><input type="button" onclick="setAdd('eventForm',validateForm);" value="Add"></td>
			</tr>
		</table>
	</fieldset>

</form>

<?

// setup datagrid
$eventGrid = new DataGrid();
$eventGrid->DataSource = EventLogic::GetAllEvents();
$eventGrid->DataSourceClassName = "Event";
$eventGrid->IDPropertyName = "EventID";
$eventGrid->FormID = "eventForm";
$eventGrid->ValidateEditCallback = "validateEdit";
$eventGrid->StoreEditCallback = "storeEdit";

// setup data columns
$idCol = $eventGrid->AddColumn("EventID", DataColumnType::NumberColumn, "EventID");
$idCol->ReadOnly = true;
$idCol->Hyperlink = "event.php";
$typeCol = $eventGrid->AddColumn("Event Type", DataColumnType::LookupColumn, "EventTypeID");
$typeCol->SetupLookup("EventType", "EventTypeName");
$eventNameCol = $eventGrid->AddColumn("Event Name", DataColumnType::StringColumn, "EventName");
$eventNameCol->Hyperlink = "event.php";
$eventGrid->AddColumn("CashboxLogStartID", DataColumnType::NumberColumn, "CashboxLogStartID");
$eventGrid->AddColumn("CashboxLogEndID", DataColumnType::NumberColumn, "CashboxLogEndID");
$eventGrid->AddColumn("Event Date", DataColumnType::DateColumn, "EventDate");

// draw!
$eventGrid->Render();

include('Controls/footer.php');
?>