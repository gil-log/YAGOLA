<?
require_once('config.php');

class MySQLConnection{

    public $host;
    public $id;
    public $pass;
    public $db;
    public $conn;
    public $sql;

    function __construct($DB_INFO)
    {
        $this->host = $DB_INFO['host'];
        $this->id = $DB_INFO['id'];
        $this->pass = $DB_INFO['pass'];
        $this->db = $DB_INFO['db'];
        $this->conn = new mysqli(
            $this->host,
            $this->id,
            $this->pass,
            $this->db
        );
        mysqli_set_charset($this->conn, "utf8");
        mysqli_query($this->conn, "set session character_set_connection=utf8;");
        mysqli_query($this->conn, "set session character_set_results=utf8;");
        mysqli_query($this->conn, "set session character_set_client=utf8;");
    }

    function excute($sql){
        $this->sql = $sql;

        if( ($result = mysqli_query($this->conn, $this->sql)) === false ){
            echo '********DB Excute ERROR********' . chr(10);
            echo 'time :' . date("Y-m-d H:i:s"). 'error : ' . $this->conn->error . ', errno : ' . $this->conn->errno . chr(10);
            echo 'sql : ' . $this->sql . chr(10);
            echo '*******************************' . chr(10);
        }

        return $result;
    }

    function excuteArray($sql)
    {
        $resultSet = $this->excute($sql);

        if( $resultSet !== false )
        {
            $rowNumber = 0;
            while( $row = mysqli_fetch_array($resultSet) )
            {
                foreach( $row as $key => $value )
                {
                    $arrResult[$rowNumber][$key] = $value;
                }
                $rowNumber++;
            }

            if( isset($arrResult) ) return $arrResult;
        }

        return false;
    }

    function quit() {
        mysqli_close($this->conn);
    }
}
