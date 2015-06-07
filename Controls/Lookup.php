<?

include_once("DataAccess/MySQLDataAccessor.php");
include_once("DataModels/lookupItem.php");

class Lookup
{
	public $TableName;
	public $KeyFieldName;
	public $ValueFieldName;

	public $Order = "";
	public $SelectedKey = "";

	public $LookupItems = array();
	
	public function __construct($tableName, $keyFieldName, $valueFieldName)
	{
		$this->TableName = $tableName;
		$this->KeyFieldName = $keyFieldName;
		$this->ValueFieldName = $valueFieldName;
	}
	
	public function Load()
	{
		global $dataAccessor;
		$this->LookupItems = $dataAccessor->LoadLookup($this->TableName, $this->KeyFieldName, $this->ValueFieldName, $this->Order);
	}
	
	public function GetValue($key)
	{
		for ($i = 0; $i < count($this->LookupItems); $i++)
		{
			if ($this->LookupItems[$i]->Key == $key)
			{
				return $this->LookupItems[$i]->Value;
			}
		}
		return null;
	}
	
	public function Render()
	{
		if (count($this->LookupItems) == 0)
		{
			$this->Load();
		}
		
		print("<option value=\"\"></option>");
		for ($i = 0; $i < count($this->LookupItems); $i++)
		{
			$lookupItem = $this->LookupItems[$i];
			$selectedAttribute = ($this->SelectedKey == $lookupItem->Key) ? "selected=\"selected\"" : "";
			print("<option $selectedAttribute value=\"{$lookupItem->Key}\">{$lookupItem->Value}</option>\n");
		}
	}
}

?>