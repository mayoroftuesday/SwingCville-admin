<? 
include('Controls/header.php');

include_once('BusinessLogic/eventsLogic.php');
include_once('Controls/Lookup.php');
include_once('BusinessLogic/cashBoxLogic.php');

$eventID = null;
if (isset($_GET['ID']))
{
    $eventID = $_GET['ID'];
}
else
{
    $eventID = $_POST['eventID'];
}

$event = EventLogic::GetEvent($eventID);

$eventTypeLookup = new Lookup("EventType", "EventTypeID", "EventTypeName");
$eventTypeLookup->Load();
$eventType = $eventTypeLookup->GetValue($event->EventTypeID);

if (isset($_POST['action']))
{
    switch ($_POST['action'])
    {
        case "update_cashbox":
            CashboxLogLogic::UpdateCashboxLog($_POST['cashboxLogID'], $_POST['cashboxCount'], $_POST['cashboxNote']);
            break;
    }
}

$cashboxStart = CashboxLogLogic::GetCashboxLog($event->CashboxLogStartID);
$cashboxEnd = CashboxLogLogic::GetCashboxLog($event->CashboxLogEndID);

?>

<style>

.editShow { display: none; }

</style>

<script type="text/javascript" language="javascript">

function EditCashbox(rowID)
{
    $("#cashboxTable .editHide").show();
    $("#cashboxTable .editShow").hide();
    
    $("#" + rowID + " .editHide").hide();
    $("#" + rowID + " .editShow").show();
}

function CancelCashbox(rowID)
{
    $("#" + rowID + " .editHide").show();
    $("#" + rowID + " .editShow").hide();
}

function UpdateCashbox(rowID)
{
    var cashboxLogID = $("#" + rowID + " input[name=cashboxLogID]").val();
    var cashboxCount = $("#" + rowID + " input[name=cashboxCount]").val();
    var cashboxNote = $("#" + rowID + " input[name=cashboxNote]").val();
    
    $("#cashboxLogID").val(cashboxLogID);
    $("#cashboxCount").val(cashboxCount);
    $("#cashboxNote").val(cashboxNote);
    
    $("#action").val("update_cashbox");
    $("#eventForm").submit();
}

</script>

<form id="eventForm" method="post">
    <input type="hidden" name="action" id="action" />
    <input type="hidden" name="eventID" id="eventID" value="<? print($eventID); ?>" />
    
    <input type="hidden" name="cashboxLogID" id="cashboxLogID" />
    <input type="hidden" name="cashboxCount" id="cashboxCount" />
    <input type="hidden" name="cashboxNote" id="cashboxNote" />
 </form>  

<table class="layout">
    <tr>
        <th>Event</th>
        <th>Cashbox</th>
    </tr>
    <tr>
        <td>
            <table class="layout_details">
                <tr>
                    <th>Type</th>
                    <th>EventID</th>
                </tr>
                <tr>
                    <td><? print($eventType); ?></td>
                    <td><? print($eventID); ?></td>
                </tr>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                </tr>
                <tr>
                    <td><? print($event->EventName); ?></td>
                    <td><? print($event->EventDate); ?></td>
                </tr>
            </table>
        </td>
        <td>
            <table class="layout_details" id="cashboxTable">
                <tr>
                    <td></td>
                    <th>Expected</th>
                    <th>Actual</th>
                    <th>Note</th>
                    <th>CashboxLogID</th>
                    <td></td>
                </tr>
                </tr>
                <tr id="cashboxStartRow">
                    <th>Start</th>
                    <td>400</td>
                    <td><span class="editHide"><? print($cashboxStart->CashboxCount); ?></span><input class="editShow" name="cashboxCount" type="text" size=3 value="<? print($cashboxStart->CashboxCount); ?>" /></td>
                    <td><span class="editHide"><? print($cashboxStart->CashboxNote); ?></span><input class="editShow" name="cashboxNote" type="text" value="<? print($cashboxStart->CashboxNote); ?>" /></td>
                    <td><input type="hidden" name="cashboxLogID" value="<? print($event->CashboxLogStartID); ?>" /><? print($event->CashboxLogStartID); ?></td>
                    <td>
                        <input class="editHide" type="button" value="Edit" onclick="EditCashbox('cashboxStartRow');" />
                        <input class="editShow" type="button" value="Cancel" onclick="CancelCashbox('cashboxStartRow');" />
                        <input class="editShow" type="button" value="Save" onclick="UpdateCashbox('cashboxStartRow');" />
                    </td>
                </tr>
                <tr id="cashboxEndRow">
                    <th>End</th>
                    <td>550</td>
                    <td><span class="editHide"><? print($cashboxEnd->CashboxCount); ?></span><input class="editShow" name="cashboxCount" type="text" size=3 value="<? print($cashboxEnd->CashboxCount); ?>" /></td>
                    <td><span class="editHide"><? print($cashboxEnd->CashboxNote); ?></span><input class="editShow" name="cashboxNote" type="text" value="<? print($cashboxEnd->CashboxNote); ?>" /></td>
                    <td><input type="hidden" name="cashboxLogID" value="<? print($event->CashboxLogEndID); ?>" /><? print($event->CashboxLogEndID); ?></td>
                    <td>
                        <input class="editHide" type="button" value="Edit" onclick="EditCashbox('cashboxEndRow');" />
                        <input class="editShow" type="button" value="Cancel" onclick="CancelCashbox('cashboxEndRow');" />
                        <input class="editShow" type="button" value="Save" onclick="UpdateCashbox('cashboxEndRow');" />
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="layout">
    <tr>
        <th colspan="2">Customer</th>
        <th colspan="3">Transaction</th>
    </tr>
    <tr>
        <td colspan="2">
            <select>
                <option>New Customer</option>
                <optgroup label="Lesson Roster">
                    <option>Mike Herring</option>
                    <option>Katie Albert</option>
                </optgroup>
                <optgroup label="Regular Customers">
                    <option>Joe Schmoe</option>
                    <option>Nancy Normal</option>
                </optgroup>
            </select>
        </td>
        <td colspan="3">
            <select>
                <option>New Transaction</option>
                <option>301 - Lesson Series</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <table class="layout_details">
                <tr>
                    <th>CustomerID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Balance</th>
                </tr>
                <tr>
                    <td>101</td>
                    <td>Mike</td>
                    <td>Herring</td>
                    <td>$10</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <th>In Mailing List?</th>
                    <th>Birthday</th>
                    <th>Is Student?</th>
                </tr>
                <tr>
                    <td>mherring@gmail.com</td>
                    <td><input type="checkbox" checked="checked" /></td>
                    <td>1984-11-06</td>
                    <td><input type="checkbox" /></td>
                </tr>
            </table>
        </td>
        <td>
            <input type="button" value="Edit" style="height:100%;" />
        </td>
        <td>
            <table class="layout_details">
                <tr>
                    <th>Transaction</th>
                    <th>Volunteer</th>
                    <th>Price</th>
                </tr>
                <tr>
                    <td>
                        <select>
                            <option>Lesson Series</option>
                        </select>
                    </td>
                    <td><input type="checkbox" checked="checked" /></td>
                    <td><input size=3 type="text" value="$20.00" /></td>
                </tr>
                <tr>
                    <th>Series</th>
                    <th colspan="2">Note</th>
                </tr>
                <tr>
                    <td>
                        <select>
                            <option>Lindy Hop II</option>
                        </select>
                    </td>
                    <td colspan=2><input type="text" size="10" /></td>
                </tr>
            </table>
        </td>
        <td>
            <table class="layout_details">
                <tr>
                    <th>Payment</th>
                    <th>Amount</th>
                    <td></td>
                </tr>
                <tr>
                    <td><select><option>Punch Card</option></select></td>
                    <td><input size="3" type="text" value="15.00" /></td>
                    <td><input type="button" value="x" /></td>
                </tr>
                <tr>
                    <td><select><option>Cash</option></select></td>
                    <td><input size="3" type="text" value="5.00" /></td>
                    <td><input type="button" value="x" /></td>
                </tr>
                <tr>
                    <td colspan=2><input type="button" value="Add Payment" /></a></td>
                </tr>
            </table>
        </td>
        <td><input type="button" value="Submit" style="height:100%;" /></td>
    </tr>
</table>

<table class="layout">
    <tr>
        <th>Attendance Record</th>
    </tr>
    <tr>
        <td>
            Total Attendance: 50
        </td>
    </tr>
    <tr>
        <td>
            <table class="layout_details">
            
                <tr>
                    <td></td>
                    <th>CustomerID</th>
                    <th>Customer</th>
                </tr>
                <tr>
                    <td><input type="button" value="x" /></td>
                    <td>101</td>
                    <td>Mike Herring</td>
                </tr>
                <tr>
                    <td><input type="button" value="x" /></td>
                    <td>101</td>
                    <td>Mike Herring</td>
                </tr>
                <tr>
                    <td><input type="button" value="x" /></td>
                    <td>101</td>
                    <td>Mike Herring</td>
                </tr>
                <tr>
                    <td><input type="button" value="x" /></td>
                    <td>101</td>
                    <td>Mike Herring</td>
                </tr>
                <tr>
                    <td><input type="button" value="x" /></td>
                    <td>101</td>
                    <td>Mike Herring</td>
                </tr>
                <tr>
                    <td><input type="button" value="x" /></td>
                    <td>101</td>
                    <td>Mike Herring</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?
include('Controls/footer.php');
?>