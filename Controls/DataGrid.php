<script type="text/javascript">

function setDelete(id, formID)
{
	// confirm deletion
	var r = confirm("Are you sure you want to delete?");
	if (r == false)
	{
		return;
	}

	// set the ID to delete
	$("#deleteID").val(id);
	
	// call Delete routine
	$("#action").val("Delete");
	$("#"+formID).submit();
}
function setEdit(id)
{
	// cancel any other edits and set the ID to edit
    if ($("#editID").val() != null && $("#editID").val().length > 0)
    {
        setCancel($("#editID").val());
    }
    $("#editID").val(id);
    
	// get the row and mark as selected
    var row = $("#row_" + id);
    $(row).addClass("selected");
	
	// switch to edit mode
    $(row).find("span").hide();
    $(row).find("td > input").show();
	$(row).find("td > select").show();
    $(row).find("input[name=Button_DeleteItem]").hide();
    $(row).find("input[name=Button_EditItem]").hide();
    $(row).find("input[name=Button_CancelItem]").show();
    $(row).find("input[name=Button_SaveItem]").show();
}
function setCancel(id)
{
	// cancel the edit
    $("#editID").val(null);

	// get the row and mark as not selected
    var row = $("#row_" + id);
    $(row).removeClass("selected");
	
	// switch to view mode
    $(row).find("span").show();
    $(row).find("td > input").hide();
	$(row).find("td > select").hide();
    $(row).find("input[name=Button_DeleteItem]").show();
    $(row).find("input[name=Button_EditItem]").show();
    $(row).find("input[name=Button_CancelItem]").hide();
    $(row).find("input[name=Button_SaveItem]").hide();
}
function setAdd(formID, validateFormCallback)
{
	// validate
	if (!validateFormCallback())
	{
		return;
	}
	
	// call Add routine
	$("#action").val("Add");
	$("#"+formID).submit();
}

function setSave(id, validateEditCallback, storeEditCallback, formID)
{
	// get the row
	var row = $("#row_" + id);
	
	// validate the name
	if (!validateEditCallback(row))
	{
		return;
	}
	
	// confirm save
	var r = confirm("Are you sure you want to save?");
	if (r == false)
	{
		return;
	}

	// store values in the form
	storeEditCallback(row);
	
	// call the Save routine
	$("#action").val("Save");
	$("#"+formID).submit();
}
</script>

<?
	include_once("Controls/Lookup.php");

	abstract class DataColumnType
	{
		const NumberColumn = 0;
		const StringColumn = 1;
		const DateColumn = 2;
		const CheckboxColumn = 3;
		const LookupColumn = 4;
	}
	
	class DataColumn
	{
		public function __construct($colName, $colType, $sourceProperty)
		{
			$this->ColumnName = $colName;
			$this->ColumnType = $colType;
			$this->DataSourcePropertyName = $sourceProperty;
		}
		
		public function SetupLookup($tableName, $valueField)
		{
			$this->LookupTableName = $tableName;
			$this->LookupValueField = $valueField;
		}
	
		// required
		public $ColumnName; 			// e.g. "First Name"
		public $ColumnType; 			// e.g. DataColumnType::StringColumn
		public $DataSourcePropertyName; // e.g. "FirstName"
		
		// optional 
		public $ReadOnly = false; 		// e.g. false
		public $Size = 0;				// optional, for text boxes
		public $LookupTableName = null; // optional, for drop-down lists
		public $LookupValueField = null;// optional, for drop-down lists
        public $Hyperlink = null;       // for linking to sub-pages
	}
	
	class DataGrid
	{
		public $DataSource;				// e.g. array of Customers
		public $DataSourceClassName;	// Customer
		public $IDPropertyName;			// CustomerID
		public $DataColumns = array();	// e.g. array of DataColumns
		public $FormID;					// e.g. CustomerForm	
		public $ValidateEditCallback;
		public $StoreEditCallback;
        public $ShowEdit = true;
        public $ShowDelete = true;
		
		public function AddColumn($colName, $colType, $sourceProperty)
		{
			$this->DataColumns[] = new DataColumn($colName, $colType, $sourceProperty);
			return end($this->DataColumns);
		}
		
		public function Render()
		{
			// setup reflection
			$rClass = new ReflectionClass($this->DataSourceClassName);
			$rID = $rClass->getProperty($this->IDPropertyName);
			
			print("<table class=\"data\">\n");
			
			// header row
			print("<tr>\n");
			print("<th>Action</th>\n");
			for ($c = 0; $c < count($this->DataColumns); $c++)
			{
				$col = $this->DataColumns[$c];
				print("<th>{$col->ColumnName}</th>\n");
			}
			print("</tr>\n");
			
			// data rows
			for ($i = 0; $i < count($this->DataSource); $i++)
			{
				$obj = $this->DataSource[$i];
				$id = $rID->getValue($obj);
			
				// print row ID
				print("<tr id=\"row_$id\">\n");
				
				// print action buttons
				print("<th>");
                if ($this->ShowDelete)
                {
                    print("<input onclick=\"setDelete($id,'{$this->FormID}');\" type=\"button\" name=\"Button_DeleteItem\" value=\"Delete\" />\n");
                }
                if ($this->ShowEdit)
                {
                    print("<input onclick=\"setEdit($id);\" type=\"button\" name=\"Button_EditItem\" value=\"Edit\" />");
                    print("<input onclick=\"setCancel($id);\" type=\"button\" name=\"Button_CancelItem\" value=\"Cancel\" style=\"display:none;\" />");
                    print("<input onclick=\"setSave($id,{$this->ValidateEditCallback},{$this->StoreEditCallback},'{$this->FormID}');\" type=\"button\" name=\"Button_SaveItem\" value=\"Save\" style=\"display:none;\" />");
                }
				print("</th>\n");
				
				// print data
				for ($c = 0; $c < count($this->DataColumns); $c++)
				{
					$col = $this->DataColumns[$c];
					$rCol = $rClass->getProperty($col->DataSourcePropertyName);
					$value = $rCol->getValue($obj);
					$lookup = null;
					
                    if ($col->ColumnType == DataColumnType::LookupColumn)
                    {
						$lookup = new Lookup($col->LookupTableName, $col->DataSourcePropertyName, $col->LookupValueField);
						$lookup->SelectedKey = $value;
						$lookup->Load();
					
                        $display = $lookup->GetValue($value);
                    }
                    else
                    {
                        $display = $value;
                    }
                    if ($col->Hyperlink != null)
                    {
                        $display = "<a href=\"{$col->Hyperlink}?ID=$id\">$display</a>";
                    }
					
					// determine column type attribute and value attribute
					$typeAttribute = "";
					$valueAttribute = "";
					$sizeAttribute = "";
					switch ($col->ColumnType)
					{
						case DataColumnType::StringColumn:
							$typeAttribute = "text";
							$valueAttribute = "value=\"$value\"";
							$sizeAttribute = "size=\"10\"";
							break;
						case DataColumnType::NumberColumn:
							$typeAttribute = "text";
							$valueAttribute = "value=\"$value\"";
							$sizeAttribute = "size=\"2\"";
							break;
						case DataColumnType::DateColumn:
							$typeAttribute = "date";
							$valueAttribute = "value=\"$value\"";
							break;
						case DataColumnType::CheckboxColumn:
							$typeAttribute = "checkbox";
							$valueAttribute = ($value == true) ? "checked=\"checked\"" : "";
							break;
					}
					
					// override size if provided
					if ($col->Size > 0)
					{
						$sizeAttribute = "size=\"{$col->Size}\"";
					}
					
					// determine disabled attribute
					$disabledAttribute = ($col->ReadOnly) ? "disabled=\"disabled\"" : "";
					
					// print the value label and edit control
                    print("<td>");
                    print("<span>$display</span>");
					if ($col->ColumnType == DataColumnType::LookupColumn)
					{
						print("<select name=\"{$col->DataSourcePropertyName}\" style=\"display:none;\" $disabledAttribute>");
						$lookup->Render();
						print("</select>");
					}
					else
					{
						print("<input type=\"$typeAttribute\" name=\"{$col->DataSourcePropertyName}\" style=\"display:none;\" $valueAttribute $disabledAttribute $sizeAttribute />");
					}
                    print("</td>");
				}
				
				print("</tr>");
			}
			print("</table>\n");
		}
	}

?>