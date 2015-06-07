<?

include_once('DataAccess/MySQLDataAccessor.php');

class PageLogic
{
    public static function GetPageName($uri)
    {
        global $dataAccessor;
        if (strstr($uri, '/'))
        {
            $uri = substr($uri, 1);
        }
        if ($uri == "")
        {
            return "Home";
        }
        else
        {
            return $dataAccessor->GetPageName($uri);
        }
    }

    public static function GetPages()
    {
        global $dataAccessor;
        return $dataAccessor->GetPages();
    }
}