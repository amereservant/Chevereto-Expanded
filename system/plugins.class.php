<?php
/**
 * Plug-ins Class
 *
 * Loads all plug-ins so they are added to the system.
 */
class Plugins
{
   /**
    * All Loaded Plug-ins
    *
    * @staticvar    array
    * @access       private
    */
    private static $_loaded_plugins;
    
   /**
    * Singleton Instance
    *
    * @staticvar    object
    * @access       private
    */
    private static $_instance;
   
   /**
    * Hooks Data
    *
    * @var      array
    * @access   private
    */
    private $_hooks = array();
    
   /**
    * Get Instance
    *
    * This method creates/returns a Singleton instance of the Plugins class.
    * It is used primarily by the procedural functions for this class.
    *
    * @param    void
    * @return   object      An instance of this class
    * @access   public
    * @since    2.0
    * @static
    */
    public static function get_instance()
    {
        if( !( self::$_instance instanceof self ) ) self::$_instance = new self;
        return self::$_instance;
    } 
    
   /**
    * Add Hook
    *
    * Used to add a new hook and it's name to hook to.
    *
    * @param    string  $name   The name used by plug-ins to hook to.
    * @return   void
    * @access   public
    * @since    2.0
    */
    public function add_hook( $name )
    {
        if( system_var_isset( 'hooks', $name ) !== false )
        {
            add_error( 'plugin', 'The hook `'. $name .'` has already been defined!' );
            return false;
        }
        
        set_system_var( 'hooks', $name, array() );
        return true;
    }
    
   /**
    * Add Multiple Hooks
    *
    * Used to add multiple hooks at one time, such as setting all of the system
    * hooks by providing an array of all hooks in one call.
    *
    * @param    array   $names      An array of all the names for the hooks being added.
    * @return   bool                False if there was an error with ANY of them,
    *                               True if there were no errors.
    * @access   public
    * @since    2.0
    */
    public function add_hooks( $name )
    {
        if( count($name) < 1 ) return false;
        
        $return = true;
        foreach( $name as $hook )
        {
            $result = $this->add_hook( $hook );
            if( $result === false ) $return = false;
        }
        return $return;
    }
    
   /**
    * Remove Hook
    *
    * Used to remove a hook to suppress a hook from being called.
    *
    * @param    string  $name   The name of the hook to remove.
    * @return   bool            False if hook isn't set, true if it was
    * @access   public
    * @since    2.0
    */
    public function remove_hook( $name )
    {
        if( !remove_system_var( 'hooks', $name ) )
        {
            return false;
        }
        return true;
    }
    
   /**
    * Attach To Hook
    *
    * Attaches the specified function to the specified hook so it will be executed
    * when the {@link execute_hook()} method is called.
    *
    * @param    string  $hook       The hook to attach to
    * @param    mixed   $function   The function to attach to the hook.  This can
    *                               either be a function or an array such as
    *                               array( $obj, 'method' ) in it.
    * @param    int     $priority   A number from 1-10 indicating the function's
    *                               execution priority.  1 being the earliest, 10
    *                               being the latest.  10 is default.
    * @return   bool                True if it was sucessfully added, False
    *                               if the hook doesn't exist.
    * @access   public
    * @since    2.0
    */
    public function attach_to_hook( $hook, $function, $priority=10 )
    {
        if( ( $hooks = get_system_var( 'hooks', $hook ) ) === false )
        {
            add_error( 'plugin', 'The hook `'. $hook .'` does not exist!' );
            return false;
        }
        $hooks[$priority][] = $function;
        ksort($hooks);// Order according to priority value
        
        set_system_var( 'hooks', $hook, $hooks );
        return true;
    }
    
   /**
    * Check If Hook Exists
    *
    * This is used to check if a hook has been added or not.
    * It may vary depending on at what point the method is called since some hooks
    * may be set later in the application.
    *
    * @param    string  $name   Name of the hook to check for
    * @return   bool            True if it exists, false if not
    * @access   public
    * @since    2.0
    */
    public function hook_exists( $name )
    {
        return system_var_isset( 'hooks', $name );
    }
    
   /**
    * Execute Hook
    *
    * This is used to execute all attached functions to a particular hook.
    * It should be called from wherever that particular hook is meant to be executed
    * so the functions attached to it perform at the correct time.
    *
    * @param    string  $hook   The hook name to execute functions for
    * @param    mixed   $args   Any parameter values the attached functions take
    * @return   array           Returns the array of $args
    * @access   public
    * @since    2.0
    */
    public function execute_hook( /* polymorphic */ )
    {
        $args = func_get_args();
        $hook = $args[0];
        array_shift($args);
        
        if( !$this->hook_exists( $hook ) ) return $args;
        
        // Get all hooked functions
        $functions = get_system_var( 'hooks', $hook );
        if( count( $functions ) < 1 ) return $args;
        
        // Get all hook filters
        $filters = system_var_isset( 'filters', $hook ) ? get_system_var( 'filters', $hook ) : array();
        
        // Run filters first
        foreach( $filters as $priority )
        {
            foreach( $priority as $func )
            {
                $args = call_user_func_array( $func, $args );
            }
        }
        // Run actions
        foreach( $functions as $priority )
        {
            foreach( $priority as $func )
            {
                $result = call_user_func_array( $func, $args );
            }
        }
        return $args;
    }
    
   /**
    * Add Filter
    *
    * Used to add a new filter to a hook that can alter/add to the args data when
    * the hook is executed.
    * The specified function will be passed ALL args present for that hook.
    *
    * @param    string  $hook       The name of the hook the filter should be attached to.
    * @param    mixed   $function   The name/array of the function to be called as a filter
    * @param    int     $priority   The execution priority.  0 is the earliest, 10 the latest.
    * @return   bool                True if the filter was sucessfully added.
    * @access   public
    * @since    2.0
    */
    public function add_filter( $hook, $function, $priority=10 )
    {
        if( system_var_isset( 'filters', $hook ) !== false )
        {
            $hook_filter = get_system_var('filters', $hook);
        }
        else
        {
            $hook_filter = array();
        }
        $hook_filter[$priority][] = $function;
        
        set_system_var( 'filters', $hook, $hook_filter );
        return true;
    }
    
   /**
    * Get Loaded Plug-ins
    *
    * Used for debugging.  Returns an array of all loaded plug-ins.
    *
    * @param    void
    * @return   array       Array of all loaded plug-ins
    * @access   public
    * @static
    */
    public static function get_loaded_plugins()
    {
        return self::$_loaded_plugins;
    }
}

/**
 * /////  Procedural Functions  /////
 *
 * These should be used instead of directly accessing the class methods!
 *
 */
function add_hook( $name )
{
    $inst = Plugins::get_instance();
    return $inst->add_hook( $name );
}

function add_hooks( $name )
{
    $inst = Plugins::get_instance();
    return $inst->add_hooks( $name );
}

function remove_hook( $name )
{
    $inst = Plugins::get_instance();
    return $inst->remove_hook( $name );
}

function attach_to_hook( $hook, $function, $priority=10 )
{
    $inst = Plugins::get_instance();
    return $inst->attach_to_hook( $hook, $function, $priority );
}

function hook_exists( $name )
{
    $inst = Plugins::get_instance();
    return $inst->hook_exists( $name );
}

function execute_hook( /* polymorphic */ )
{
    $args = func_get_args();
    $inst = Plugins::get_instance();
    return call_user_func_array(array($inst, 'execute_hook'), $args );
}

function add_filter( $hook, $function, $priority=10 )
{
    $inst = Plugins::get_instance();
    return $inst->add_filter( $hook, $function, $priority );
}


