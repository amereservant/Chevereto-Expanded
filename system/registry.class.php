<?php
/**
 * Registry Class
 *
 * Holds all of the global variables so they can be used throughout the script
 * without having to declare a bunch of variables global.
 * It also centralizes all of the global variables and can easily be dumped for 
 * debugging/development.
 *
 * PHP5
 *
 * @package     Chevereto
 * @author      David Miles <david@amereservant.com>
 * @version     2.0
 * @since       2.0
 * @license     http://creativecommons.org/licenses/MIT/ MIT License
 */
class Registry
{
   /**
    * Holds all of the variables for callback.
    *
    * @staticvar    array
    * @access       private
    * @since        2.0
    */
    private static $_vars;
    
   
   /**
    * Set User Variable
    *
    * This should be used by plug-ins and themes ONLY when non-local variables
    * are needed.  That way variable name collisions are avoided.
    *
    * The $plugin_name parameter should be unique and it's highly recommended to use
    * the plug-in/theme's name as this value.
    *
    * @param    string  $plugin_name    The plug-in or theme's name, used to group variables
    * @param    string  $key            The name of the key the variable will be referenced by.
    * @param    mixed   $value          The value for the variable.
    * @param    bool    $overridable    Determines whether or not the variable can be
    *                                   overridden elsehwere.  If set to false, the
    *                                   value can only be set once.
    * @return   bool                    True on success, False on fail
    * @access   public
    * @static
    * @since    2.0
    */
    public static function set_var( $plugin_name, $key, $value, $overridable=true )
    {
        list( $plugin_name, $key, $value, $overridable ) = execute_hook( 'set_var', $plugin_name, $key, $value, $overridable );
        
        if( isset(self::$_vars['user'][$plugin_name][$key]['value']) && 
            self::$_vars['user'][$plugin_name][$key]['override'] == false )
        {
            add_error( 'plugin', 'The value `'. $key .'` has already been set and isn\'t overridable!' );
            return false;
        }
        self::$_vars['user'][$plugin_name][$key]['value']    = $value;
        self::$_vars['user'][$plugin_name][$key]['override'] = $overridable;
        return true;
    }
    
   /**
    * Get User Variable
    *
    * Used to retrieve the user variable.
    *
    * @param    string  $plugin_name    The plug-in or theme's name to get value from
    * @param    string  $key            The name of the key for the variable being retrieved
    * @return   mixed                   The data on success, false if it isn't set.
    * @access   public
    * @static
    * @since    2.0
    */
    public static function get_var( $plugin_name, $key )
    {
        if( !isset(self::$_vars['user'][$plugin_name][$key]) )
        {
            add_error( 'debug', 'The value for plugin name `'. $plugin_name .'` and key `'.
                                $key .'` is not set!');
            return false;
        }
        return self::$_vars['user'][$plugin_name][$key]['value'];
    }
    
   /**
    * Get System Variable
    *
    * This is used to access system variables from the system array that have been 
    * set by {@link get_system_var()} method.
    *
    * It also adds a 'debug' error message if a system variable isn't set when 
    * being called.
    * Also note care should be used if retrieving a bool value since !isset() will
    * also return false.
    *
    * @param    string  $section    The section read from.  Example: 'hooks', 'actions', 'query', etc.
    * @param    string  $item       The item to get.  Example: 'css'
    * @param    bool                Is this function being used to determine if the system
    *                               variable is being called to see if the item isset?
    * @return   mixed               Depends on the data set to the variable being requested.
    * @access   public
    * @static
    * @since    2.0
    */
    public static function get_system_var( $section, $item, $isset=false )
    {
        if( !isset( self::$_vars['system'][$section][$item] ) )
        {
            if( !$isset )
            {
                add_error( 'debug', 'The system variable `'. $item .'` in section `'. $section .
                                    '` is not set!' );
            }
            return false;
        }
        elseif( $isset ) return true;
        
        return self::$_vars['system'][$section][$item];
    }
    
   /**
    * Set System Variable
    *
    * This should NOT be accessed by any plug-ins or user functions!
    * This method provides a way for core classes to add variables to the system
    * array so the values can be used in other areas.
    *
    * It also adds a 'debug' error message if a system variable is being replaced
    * in order to help locate issues associated with this.
    *
    * @param    string  $section    The section to add to.  Example: 'hooks', 'actions', 'query', etc.
    * @param    string  $item       The item to add to.  An example would be adding
    *                               the css array to the plugin section.  'css' would be the item.
    * @param    mixed   $val        The value to assign the item.
    * @return   void
    * @access   public
    * @static
    * @since    2.0
    */
    public static function set_system_var( $section, $item, $val )
    {
        list( $section, $item, $val ) = execute_hook( 'set_system_var', $section, $item, $val );
        if( isset( self::$_vars['system'][$section][$item] ) )
        {
            // Hooks will be set after being added, so no error is neccessary here.
            if( $section != 'hooks' )
            {
                add_error( 'debug', 'The system variable `'. $section .'` => `'. $item .
                                '` is already set!' );
            }
        }
        self::$_vars['system'][$section][$item] = $val;
    }
    
   /**
    * Unset System Variable
    *
    * Used to remove a system variable.
    *
    * @param    string  $section    The system variable section to remove a variable from
    * @param    string  $item       The item we're unsetting
    * @return   bool                False if variable isn't set, true if it was sucessfully removed
    * @access   public
    * @static
    * @since    2.0
    */
    public static function remove_system_var( $section, $item )
    {
        if( !isset( self::$_vars['system'][$section][$item] ) )
        {
            add_error( 'debug', 'The system variable `'. $section .'` => `'. $item .
                                '` is not set!  Removal failed!' ); 
            return false;
        }
        unset( self::$_vars['system'][$section][$item] );
        return true;
    }
    
    /**
    * Unset User Variable
    *
    * Used to remove a user variable.
    *
    * @param    string  $section    The user variable section to remove a variable from
    * @param    string  $item       The item we're unsetting
    * @return   bool                False if variable isn't set, true if it was sucessfully removed
    * @access   public
    * @static
    * @since    2.0
    */
    public static function remove_var( $section, $item )
    {
        if( !isset( self::$_vars['user'][$section][$item] ) ) 
        {
            add_error( 'debug', 'The user variable `'. $section .'` => `'. $item .
                                '` is not set!  Removal failed!' ); 
            return false;
        }
        unset( self::$_vars['user'][$section][$item] );
        return true;
    }
    
   /**
    * Dump All Registry Variables
    *
    * Used ONLY for development/debugging.  It will display all set variables at
    * the time it is called.
    *
    * @param    void
    * @return   void    prints out a <pre></pre> formatted array.
    * @access   public
    * @static
    * @since    2.0
    */
    public static function dump_vars()
    {
        echo 'Registry Vars:';
        echo '<pre>'. print_r(self::$_vars, true) .'</pre>';
    }
}

/**
 * /////  Procedural Functions  /////
 *
 * These should be used instead of directly accessing the class methods!
 *
 */

function set_var( $plugin_name, $key, $value, $overridable=true )
{
    return Registry::set_var( $plugin_name, $key, $value, $overridable=true );
}

function get_var( $plugin_name, $key )
{
    return Registry::get_var( $plugin_name, $key );
}

function set_system_var( $section, $item, $val )
{
    return Registry::set_system_var( $section, $item, $val );
}

function get_system_var( $section, $item )
{
    return Registry::get_system_var( $section, $item );
}

function system_var_isset( $section, $item )
{
    return Registry::get_system_var( $section, $item, true );
}

function remove_var( $section, $item )
{
    return Registry::remove_var( $section, $item );
}

function remove_system_var( $section, $item )
{
    return Registry::remove_var( $section, $item );
}
