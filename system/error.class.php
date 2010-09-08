<?php
/**
 * Chevereto Error Class
 *
 * This class is responsible for handling and processing caught exceptions
 * and errors.
 * It also provides localized error handling for plug-in authors to handle errors
 * however they choose that occur within their code.
 *
 * PHP5
 *
 * @package     Chevereto
 * @author      David Miles <david@amereservant.com>
 * @version     2.0
 * @since       2.0
 * @license     http://creativecommons.org/licenses/MIT/ MIT License
 */
class Chev_Error
{
   /**
    * Errors Array - Stores all of the global errors
    *
    * @staticvar   array
    * @access      private
    * @since       2.0
    */
    private static $_errors;

   /**
    * Local Errors - Local errors that only pertain to that instance ( used by plug-ins )
    *
    * @var      array
    * @access   private
    * @since    2.0
    */
    private $_local_errors;
    
    public function __construct()
    {
    }
    
   /**
    * Add Global Error
    *
    * Adds errors to the static property {@link $_errors} so they can be called back
    * later on.
    *
    * @param    string      $code       A user-defined code for the error.  This can
    *                                   be used for grouping different types of errors.
    * @param    string      $message    The error message
    * @return   void
    * @access   public
    * @static
    * @since    2.0
    */
    public static function add_global_error( $code, $message )
    {
        if( DEBUG ) echo '<pre>'. $message .'</pre>';
        self::$_errors[$code][] = $message;
        return;
    }
    
   /**
    * Add Local Error
    *
    * Used to add an error to a single instance of the Error class.
    * This can be used by plugins to localize errors and not conflict with the
    * core's error system.
    *
    * @param    string      $code       A user-defined code for the error.  This can
    *                                   be used for grouping different types of errors.
    * @param    string      $message    The error message
    * @return   void
    * @access   public
    * @since    2.0
    */
    public function add_local_error( $code, $message )
    {
        if( DEBUG ) echo '<pre>'. $message .'</pre>';
        $this->_local_errors[$code][] = $message;
    }
    
   /**
    * Get Global Error(s) - Based on error code
    *
    * Used to retrieve any errors matching the provided code in the global 
    * {@link $_errors} array.
    *
    * @param    string      $code   The error code for the error(s) wishing to retrieve
    * @return   array               The current array of errors in the {@link $_errors} property
    *                               or false if no errors matching the $code are found.
    * @access   public
    * @static
    * @since    2.0
    */
    public static function get_global_error( $code )
    {
        if( empty(self::$_errors[$code]) ) return false;
        return self::$_errors[$code];
    }
    
   /**
    * Get Local Error(s) - Based on error code
    *
    * Used to retrieve any errors matching the provided code in the local
    * {@link $_local_errors} array.
    *
    * @param    string      $code   The error code for the error(s) wishing to retrieve
    * @return   array               The current array of errors in the {@link $_local_errors}
    *                               property or false if no errors matching the $code are found.
    * @access   public
    * @since    2.0
    */
    public function get_local_error( $code )
    {
        if( empty($this->_local_errors[$code]) ) return false;
    }
    
   /**
    * Get Local Error Codes
    *
    * Returns all of the error codes for the {@link $_local_errors} property.
    *
    * @param    void
    * @return   array       An array of all the error codes if any are set, or an empty
    *                       array if none have been set.
    * @access   public
    * @since    2.0
    */
    public function get_local_error_codes()
    {
        if( empty($this->_local_errors) ) return array();
        return array_keys( $this->_local_errors );
    }
    
   /**
    * Get Global Error Codes
    *
    * Returns all of the error codes for the {@link $_errors} property.
    *
    * @param    void
    * @return   array       An array of all the error codes if any are set, or an empty
    *                       array if none have been set.
    * @access   public
    * @static
    * @since    2.0
    */
    public static function get_global_error_codes()
    {
        if( empty(self::$_errors) ) return array();
        return array_keys( self::$_errors );
    }
    
   /**
    * Get All Global Errors
    *
    * Returns an array with all errors in the {@link $_errors} property.
    *
    * @param    void
    * @return   array       An array of all the errors currently set.
    * @access   public
    * @static
    * @since    2.0
    */
    public static function get_all_global_errors()
    {
        if( empty( self::$_errors ) ) return array();
        return self::$_errors;
    }
    
   /**
    * Get All Local Errors
    *
    * Returns an array with all errors in the {@link $_local_errors} property.
    *
    * @param    void
    * @return   array       An array of all the errors currently set for an instance.
    * @access   public
    * @since    2.0
    */
    public static function get_all_local_errors()
    {
        if( empty( self::$_errors ) ) return array();
        return self::$_errors;
    }
}

/**
 * /////  Procedural Functions  /////
 *
 * These should be used instead of directly accessing the class methods!
 *
 * If the $obj parameter is passed an object, then the functions will reference
 * the local instance of the error class instead of the global static one.
 */

function add_error( $code, $message, $obj=null )
{
    if( is_object( $obj ) ) return $obj->add_local_error( $code, $message );
    return Chev_Error::add_global_error( $code, $message );
}

function get_error( $code, $obj=null )
{
    if( is_object( $obj ) ) return $obj->get_local_error( $code );
    return Chev_Error::get_global_error( $code );
}

function get_all_errors( $obj=null )
{
    if( is_object( $obj ) )  return $obj->get_all_local_errors();
    return Chev_Error::get_all_global_errors();
}

function get_error_codes( $obj=null )
{
    if( is_object( $obj ) )  return $obj->get_local_error_codes();
    return Chev_Error::get_global_error_codes();
}
