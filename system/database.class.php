<?php
/**
 * Database Class
 *
 * This class uses the PDO extension to allow flexibility in which database type
 * Chevereto can use and because, well, PDO is chévere.
 *
 * PHP5
 *
 * @package     Chevereto
 * @author      David Miles <david@amereservant.com>
 * @version     2.0
 * @since       2.0
 * @license     http://creativecommons.org/licenses/MIT/ MIT License
 */
class Chev_PDO
{
   /**
    * Database Handle/Object
    *
    * @var      object
    * @access   protected
    * @since    2.0
    */
    protected $dbh;
    
   /**
    * Database Instance (Singleton)
    *
    * @staticvar    object
    * @access       protected
    * @since        2.0
    */
    protected static $instance;
    
   /**
    * Last Database Error
    *
    * @var      string
    * @access   public
    * @since    2.0
    */
    public $last_error;
    
   /**
    * Class Constructor
    *
    * Only ONE database type should be specified in config.php, otherwise it'll throw an
    * exception indicating this error.
    * The username/password must be empty if using SQLITE, otherwise the SQLITE filename
    * must be empty if using MySQL.
    *
    * @param    void
    * @return   instance
    * @access   public
    */
    public function __construct( )
    {
        if( defined( 'DB_USER' ) )
        {
        if( strlen( DB_USER ) > 0 && strlen( DB_PASS ) > 0 && strlen( SQLITE_FILE ) > 0 )
        {
            throw new Exception('You must specify at least one valid database type!');
        }
        
        if( ( strlen( DB_USER ) > 0 || strlen( DB_PASS ) > 0 ) && strlen( SQLITE_FILE ) > 0 )
        {
            throw new Exception('Only ONE database type can be specified!');
        }
        }
        try
        {
            if( strlen( DB_USER ) > 0 && strlen( DB_PASS ) > 0 )
            {
                $this->dbh = new PDO('mysql:dbname='. DB_NAME .';host='. DB_HOST,
                    DB_USER, DB_PASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            }
            else
            {
                $sqlite_file = SYS_PATH . CHEV_SEP .'database'. CHEV_SEP . SQLITE_FILE .'.sdb';
                $this->dbh = new PDO('sqlite:'. $sqlite_file, '', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            }
        }
        catch(PDOException $e)
        {
            echo 'Database Error: '. $e->getMessage();
            exit();
        }
    }
    
   /**
    * Get Singleton Instance
    *
    * Checks if {@link $instance} is already an instance of this class and if not,
    * it sets it, then returns the instance.
    *
    * @param    void
    * @return   object
    * @static
    * @access   public
    * @since    2.0
    */
    public static function instance()
    {
        if( !(self::$instance instanceof self) ) self::$instance = new self;
        return self::$instance;
    }
   
   /**
    * RAW Execute
    *
    * Executes a RAW SQL statement and returns the number of rows affected by it.
    *
    * @param    string  $sql    SQL statement to exectute
    * @return   int             Number of rows affected, false on failure
    * @access   public
    * @since    2.0
    */
    public function raw_exec( $sql )
    {
        try
        {
            $count = $this->dbh->exec( $sql );
        }
        catch( PDOException $e )
        {
            $this->_debug_error( __LINE__, $e, ' "%s"  LINE: %d');
            return false;
        }
        return $count;
    }
    
   /**
    * Add New User
    *
    * This creates the encrypted password parts and adds the new user to the database.
    *
    * @param    string  $user   The new user's name
    * @param    mixed   $pass   The new user's password or an 'already-encrypted' password array
    * @param    string  $email  The new user's email
    * @return   bool            True on success, false if user couldn't be added
    * @since    2.0
    * @access   public
    * @TODO     Add email validation?
    */
    public function add_new_user( $user, $pass, $email )
    {
        if( strlen($user) < 2 || ( !is_array($pass) && strlen($pass) < 2 ) || strlen($email) < 5 ||
            (is_array($pass) && count($pass) < 1 ) )
        {
            add_error( 'debug', 'A valid username/password/email wasn\'t provided! LINE: '. __LINE__ );
            return false;
        }
        
        if( $this->user_exists( $user, $email, true ) )
        {
            add_error( 'debug', 'The user already exists!  LINE: '. __LINE__ );
            return false;
        }
        
        if( isset($pass['salt1']) && isset($pass['salt2']) && isset($pass['password']) && isset($pass['pattern']) )
        {
            $passwd = $pass;
        }
        else
        {
            $passwd = encrypt_password( $pass );
        }
        
        $time = time();
        
        try
        {
            $sth = $this->dbh->prepare("INSERT INTO users (created_on, email, username, password, salt1, salt2, pattern) " .
                "VALUES (:date, :email, :user, :pass, :salt1, :salt2, :pattern)");
            $sth->bindParam(':date', $time, PDO::PARAM_INT, 10);
            $sth->bindParam(':email', $email, PDO::PARAM_STR, 120);
            $sth->bindParam(':user', $user, PDO::PARAM_STR, 50);
            $sth->bindParam(':pass', $passwd['password'], PDO::PARAM_STR, 40);
            $sth->bindParam(':salt1', $passwd['salt1'], PDO::PARAM_STR, 12);
            $sth->bindParam(':salt2', $passwd['salt2'], PDO::PARAM_STR, 10);
            $sth->bindParam(':pattern', $passwd['pattern'], PDO::PARAM_STR, 22);
            
            $sth->execute();
            $results = $sth->rowCount();
        }
        catch(PDOException $e)
        {
            $this->__debug_error( __LINE__, $e );
            return false;
        }
        return $results >= 1;
    }
    
   /**
    * Check If User Exists
    *
    * This checks to see if the user exists either by username or password.
    *
    * @param    string  $username   The username to check
    * @param    string  $email      The email to check
    * @param    bool    $use_email  Should the search use the email parameter?
    * @return   bool                True if the user exists, false if not
    * @since    2.0
    * @access   public
    */
    public function user_exists( $username, $email='', $use_email=false )
    {
        if( strlen($username) < 2 ) return false;
        
        try
        {
            if( strlen($email) < 5 || !$use_email )
            {
                $sth = $this->dbh->prepare('SELECT id FROM users WHERE username = :username');
                $sth->bindParam(':username', $username, PDO::PARAM_STR, 50);
            }
            else
            {
                $sth = $this->dbh->prepare('SELECT id FROM users WHERE username = :username OR email = :email');
                $sth->bindParam(':username', $username, PDO::PARAM_STR, 50);
                $sth->bindParam(':email', $email, PDO::PARAM_STR, 120);
            }
            $sth->execute();
            $result = $sth->fetchAll();
        }
        catch(PDOException $e)
        {
            $this->_debug_error( __LINE__, $e );
            return false;
        }
        return count($result) > 0;
    }
            
   /**
    * DEBUG Output
    *
    * Simply handles any error output if DEBUG is set to true in the config.php file
    * and also responsible for setting the {@link $last_error} property.
    *
    * @param    int     $line       The value from __LINE__ for which line the error occured.
    * @param    obj     $e          The caught PDOException object
    * @param    string  $format     A string containing a sprintf formatted string for the error.
    * @return   void
    * @since    2.0
    * @access   private
    */
    private function _debug_error( $line, $e, $format=null )
    {
        $msg = !empty($format) ? $format : 'DATABASE ERROR: %s LINE: %d';
        
        $errinfo = $this->dbh->errorInfo();
        $error = sprintf($format, $errinfo[2], $line);
        $this->last_error = $error;
        
        if( DEBUG === true ) echo $error;
        return;
    }
}

/**
 * /////  Procedural Functions  /////
 *
 * These should be used instead of directly accessing the class methods when possible!
 *
 */

function db_raw_exec( $sql )
{
    return Chev_PDO::instance()->raw_exec( $sql );
}

function add_new_user( $user, $pass, $email )
{
    return Chev_PDO::instance()->add_new_user( $user, $pass, $email );
}

function user_exists( $username, $email='', $use_email=false )
{
    return Chev_PDO::instance()->user_exists( $username, $email='', $use_email=false );
}

function db_get_last_error()
{
    return Chev_PDO::instance()->last_error;
}
