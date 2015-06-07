<!-- START HEADER -->
<html>

<?
ini_set('display_errors',1); 
 error_reporting(E_ALL);

include_once('BusinessLogic/pageLogic.php');
?>
<head>
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<title>SwingCville Admin Panel</title>
<link rel="stylesheet" type="text/css" href="/style.css" />
</head>

<body>
    <div id="leftColumn">
    <h1>SwingCville Admin Panel</h1>
    <?
    include('menu.php');
    ?>
    </div>
    
    <div id="content">
    <h1>
    <?
    $pageName = PageLogic::GetPageName($_SERVER['REQUEST_URI']);
    print($pageName);
    ?>
    </h1>
    
<!-- END HEADER -->