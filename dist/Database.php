<?php


class Database
{
    public static $mySqli;

    public static function loadConfig(string $configFile)
    {
        require_once($configFile);
    }

    public static function deleteQuery(string $query): bool
    {
        if (Database::connect())
        {
            $result = Database::$mySqli->query($query);
            Database::disconnect();
            return $result; // result only tells us if the SQL statement could be processed and not if something was actually deleted
        }
        else
        {
            echo "Could not connect in deleteQuery!";
            return false;
        }
    }

    public static function insertQuery(string $query): int
    {
        if (Database::connect())
        {
            $result = Database::$mySqli->query($query);
            $id = Database::$mySqli->insert_id;
            Database::disconnect();
            return $id; // We return the id of the newly inserted row
        }
        else
        {
            echo "Could not connect in insertQuery!";
            return 0;
        }
    }

    public static function selectQuery(string $query): ?mysqli_result
    {
        if (Database::connect())
        {
            $result = Database::$mySqli->query($query);
            Database::disconnect();
            return $result; // a mysqli result object -> later fetch_assoc
        }
        else
        {
            echo "Could not connect in selectQuery!";
            return null;
        }
    }

    public static function updateQuery(string $query): bool
    {
        if (Database::connect())
        {
            $result = Database::$mySqli->query($query);
            Database::disconnect();
            return $result;
        }
        else
        {
            echo "Could not connect in updateQuery!";
            return false;
        }
    }

    private static function connect(): bool
    {
        Database::$mySqli = @new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

        if (Database::$mySqli->connect_error)
        {

            return false;
        }
        return true;
    }

    private static function disconnect()
    {
        if (Database::$mySqli != null)
        {
            Database::$mySqli->close();
        }
    }
}