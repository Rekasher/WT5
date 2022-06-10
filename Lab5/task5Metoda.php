<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Lab5</title>
    <style>
        table{
            margin: 20px auto;
            border: 2px black solid;
            width: 1200px;
            text-align: left;
        }

        table tr{
            text-align: center;
        }

        table tr:first-child{
            background: #CD214F;
            color: #ffffdd;
            font-size: 18px;
        }
        table td {
            padding: 5px;
        }
        h2{
            margin: 0;
        }

        .submit-btn{
            width: 300px;
            text-align: center;
            margin: 0 auto;
            font-size: 18px;
            background: black;
            color: white;
            border-width: 2px;
            border-color: #CD214F;
            display: block;
        }
        .submit-btn:hover{
            background: #CD214F;
        }
        .inputAmount{
            display: block;
            margin: 10px auto;
            width: 290px;
        }
    </style>
</head>
<body>
</body>
</html>

<?php

class DBControlPDO
{
    const DB_HOST = 'mysql:dbname=Cinema;host=127.0.0.1';
    const DB_USER = 'root';
    const DB_PASSWORD = 'Vangaganga2003!';

    private static $db = null;
    public $connection;

    function __construct()
    {
        try
        {
            $this->connection = new PDO(self::DB_HOST, self::DB_USER, self::DB_PASSWORD);
        }
        catch(Exception $ex)
        {
            exit($ex->getMessage());
        }

        $this->connection->exec("SET NAMES utf8");
    }

    public static function GetControl(): DBControlPDO
    {
        if (self::$db == null)
            self::$db = new DBControlPDO();

        return self::$db;
    }

}

class DBInteract
{

    private static function GetSelectHtml($selectName): string
    {
        return "<select name='$selectName'>
                            <option value='TEXT'>TEXT</option>
                            <option value='INT'>INT</option>
                         </select>";
    }

    private static function IsTableExists(DBControlPDO $controlPDO, $tableName): bool
    {
        try
        {
            $res = $controlPDO->connection->query("SHOW TABLES LIKE '$tableName'");
            if ($res->rowCount() == 0)
                return false;
        }
        catch (Exception $exception)
        {
            return false;
        }

        return true;
    }

    public static function GetForm(DBControlPDO $controlPDO, $tableName)
    {
        echo "<form method='post'>
              <input class='submit-btn' type='submit' value='GO!'>";
        if(self::IsTableExists($controlPDO, $tableName) == true)
        {
            echo "<input class='inputAmount' type='text' name='instanceAmount'>";
            $columnsCount = $controlPDO->connection->query("SELECT * FROM $tableName")->columnCount();
            $columnNames = array();
            foreach ($controlPDO->connection->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='$tableName'") as $name)
                $columnNames[] = $name['COLUMN_NAME'];

            echo "<table rules='all'>
                    <tr>";

            for($i = 1; $i <= $columnsCount; $i++)
                echo "<td><h2>".$columnNames[$i-1]."</h2>".self::GetSelectHtml($columnNames[$i-1])."</td>";

            echo "</tr>";

            $elems = $controlPDO->connection->query("SELECT * FROM $tableName");
            foreach ($elems as $row)
            {
                echo "<tr>";
                foreach ($columnNames as $key)
                    echo "<td>".$row[$key]."</td>";

                echo "</tr>";
            }

            echo "</table>";
        }
        else
        {
            echo "<input class='inputAmount' type='text' name='columnsAmount'>";
        }
        echo "</form>";
    }

    public static function CreateTable(DBControlPDO $controlPDO, $tableName, $columnsAmount)
    {

        $keysTable = "Column1 text,";
        for ($i = 2; $i <= $columnsAmount; $i++)
        {
            $keysTable = $keysTable . " Column" . $i . " text,";
        }
        $keysTable = mb_substr($keysTable,0,-1);

        $insertCommand = "CREATE TABLE ".$tableName." ( ".$keysTable." )";
        if($controlPDO->connection->exec($insertCommand) === false)
            echo $controlPDO->connection->errorInfo()[2];

    }
}

setcookie("visitedSites[4]", "http://192.168.159.129/labsVT/lab5/task5Metoda.php", 0, "/labsVT/Lab6", "192.168.159.129");

if (!isset($_GET['tableName']) && count($_POST) == 0)
    echo "<form method='get'>
          <input type='text' name='tableName'>
          <input type='submit' value='Process'>
          </form>";
else
{
    if (isset($_GET['tableName']))
    {
        $control = DBControlPDO::GetControl();
        DBInteract::GetForm($control, $_GET['tableName']);
        if (isset($_POST['columnsAmount']))
        {
            DBInteract::CreateTable($control, $_GET['tableName'], $_POST['columnsAmount']);
            header("Refresh:0");
        }
        else
        {
            if (isset($_POST['instanceAmount']))
            {
                $keysTable = "";
                $valuesTable = "";
                foreach ($_POST as $key => $elem)
                {
                    if($key != "instanceAmount") {
                        $keysTable = $keysTable . " " . $key . ",";
                        $valuesTable = $valuesTable . " " . GetValue($elem) . ",";
                    }
                }
                $keysTable = mb_substr($keysTable,0,-1);
                $valuesTable = mb_substr($valuesTable,0,-1);

                $insertCommand = "INSERT INTO ".$_GET['tableName']." (".$keysTable.") VALUES (".$valuesTable.") ";

                for ($i = 1; $i <= $_POST['instanceAmount']; $i++)
                    if($control->connection->exec($insertCommand) === false)
                        echo $control->connection->errorInfo()[2];

                header("Refresh:0");
            }
        }
    }
}

function GetValue($type)
{
    if ($type=="TEXT")
    {
        $resStr = "";
        $strLength = rand(5, 15);

        for($i = 1; $i <= $strLength; $i++)
            $resStr = $resStr . chr(rand(97, 122));

        return "'" . $resStr . "'";
    }
    else if($type=="INT")
        return rand(1,1000000);

}
