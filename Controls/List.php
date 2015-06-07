<?

include_once("DataAccess/MySQLDataAccessor.php");
include_once("DataModels/lookupItem.php");

class Lookup
{
	public $SelectedKey = "";

	public $LookupItems = array();
	
	public function __construct($dataSource, $dataSourceClassName, $keyFieldName, $valueFieldName)
	{   
        // setup reflection
        $rClass = new ReflectionClass($dataSourceClassName);
        $rKey = $rClass->getProperty($keyFieldName);
        $rValue = $rClass->getProperty($valueFieldName);
        
        for ($i = 0; $i < count($dataSource); $i++)
        {
            $obj = $this->DataSource[$i];
            $key = $rKey->getValue($obj);
            $value = $rValue->getValue($obj);
            
            $lookupItem = new LookupItem();
            $lookupItem->Key = $key;
            $lookupItem->Value = $value;
            $this->LookupItems[] = $lookupItem;
        }
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