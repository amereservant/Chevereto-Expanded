<?php
/**
 * Router Class
 *
 * This class is responsible for processing all request URL parameters so they're
 * accessible by other classes/functions. 
 *
 * PHP5
 *
 * @package     Chevereto
 * @author      David Miles <david@amereservant.com>
 * @version     2.0
 * @since       2.0
 * @license     http://creativecommons.org/licenses/MIT/ MIT License
 */
class Router 
{
   /**
    * Parsed Query Variables - (used within this class ONLY!)
    *
    * @staticvar    array
    * @access       private
    * @since        2.0
    */
    private static $_query_vars;
    
   /**
    * Class Constructor
    *
    * Parses the current request URL and adds the query parameters to the Registry.
    */
    private function __construct()
    {
        $this->_parse_url();
    }
    
   /**
    * Load Router
    *
    * This method is responsible for instantiating this class.
    *
    * @param    void
    * @return   void
    * @access   public
    * @since    2.0
    * @static
    */
    public static function load()
    {
        new self();
    }
    
   /**
    * Responsible for parsing the current request URL and setting the {@link $_query_vars}
    * property with the parsed variables.
    *
    * @param    void
    * @return   void
    * @access   private
    * @since    2.0
    */
    private function _parse_url()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        list( $query ) = execute_hook( 'parse_url', $query );
        self::$_query_vars = $query;
    }
    
   /**
    * Get Query Variables
    *
    * Used to retrieve the set query variables.
    *
    * @param    void
    * @return   array   It will be an empty array if there were no query variables
    *                   or an array of query variables and values.
    * @access   public
    * @static
    * @since    2.0
    */
    public static function get_query_vars()
    {
        return self::$_query_vars;
    }
}

/**
 * /////  Procedural Functions  /////
 *
 * These should be used instead of directly accessing the class methods!
 *
 */

function get_query_vars()
{
    return Router::get_query_vars();
}

/**  TESTING - DELETE THIS!
$path = explode('/', CHEV_PATH);
$req  = explode('/', $_SERVER['REQUEST_URI']);
$params = array_diff($req,$path);
echo '<pre>'. print_r($_SERVER, true) .'</pre>';
echo '<pre>'. print_r($params, true) .'</pre>';
parse_str($_SERVER['QUERY_STRING'], $query);
var_dump($query);
*/

