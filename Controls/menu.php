<!-- START MENU -->

<?
include_once("BusinessLogic/pageLogic.php");

$pages = PageLogic::GetPages();

for ($i = 0; $i < count($pages); $i++)
{
    $page = $pages[$i];
    $url = $page->URL;
    $name = $page->PageName;
    print ("<a href=\"$url\">$name</a><br />");
}

?>

<!-- END MENU -->