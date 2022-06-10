<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>General</title>
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
    </style>
</head>
<body>
<form method="get">
    <input type="text" name="producer">
    <input type="submit" value="Process">
</form>
</body>
</html>

<?php

class DBControlSQLI
{
    const DB_HOST = 'localhost';
    const DB_USER = 'root';
    const DB_PASSWORD = 'шз';
    const DB_NAME = 'Football_List';

    private static $db = null;
    public $connection;

    function __construct()
    {
        $this->connection = new mysqli(self::DB_HOST, self::DB_USER, self::DB_PASSWORD, self::DB_NAME);
        if ($this->connection->connect_errno)
            exit("Can't connect to DB".$this->DB_NAME.". ".$this->connection->connect_error);

        $this->connection->set_charset("utf8");
    }

    public static function GetControl(): DBControlSQLI
    {
        if (self::$db == null)
            self::$db = new DBControlSQLI();

        return self::$db;
    }
}

setcookie("visitedSites[2]", "http://192.168.159.129/labsVT/lab5/generalTask.php", 0, "/labsVT/Lab6", "192.168.159.129");

if (isset($_GET["producer"])) {

    $control = DBControlSQLI::GetControl();
    $producer = $_GET["producer"];
    $producersMatch = $control->connection->query("SELECT * FROM Producers WHERE last_name = '$producer'");

    if(count((array)mysqli_fetch_array($producersMatch)) == 0)
        $producersMatch = $control->connection->query("SELECT * FROM Producers");

    echo "<table rules='all'>";
    echo "<tr><td>Title</td><td>Year</td><td>Description</td><td>Rating</td><td>Prod. last name</td>
          <td>Prod. first name</td><td>Age</td><td>Contact</td></tr>";

    $films = $control->connection->query("SELECT producer_id, title, year, description, rating FROM Films ORDER BY year");

    $producersOutInfo = array();
    foreach ($producersMatch as $value)
        $producersOutInfo[$value["id"]] = $value;

    foreach ($films as $film)
    {
        if (!array_key_exists($film["producer_id"], $producersOutInfo))
            continue;

        echo "<tr>";
        foreach ($film as $key => $field)
            if ($key != "producer_id")
                echo "<td>" . $field . "</td>";

        foreach ($producersOutInfo[$film["producer_id"]] as $key => $field)
            if ($key != "id")
                echo "<td>" . $field . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
