<?php

/**
 * Encrypt Password
 *
 * This function provides a way to securely encrypt user passwords.
 * Credit goes to {@link http://microsonic.org/2009/04/05/php-password-salt/} since
 * this function derived from there.
 *
 * @param   string  $pass   The password to encrypt
 * @return  array           An array containing 2 salts, the pattern, and encrypted
 *                          password pattern.  These should ALL be stored in a database.
 * @since   2.0
 */
function encrypt_password( $pass )
{
    // This is our first set of possible salt characters. Shuffle so always different all aspects
    $set1 = str_shuffle("!@#$%^&*()_+=-';:,<.>126AaBbJjKkLlSdDsQwWeErqRtTyY");
    
    // Second set. Same thing, different characters though :D
    $set2 = str_shuffle("1234567890`~ZxzxCcVvBb?[]{}pP");
    
   /**
    * Now the loops to actually make the salt characters
    * We'll be using the rand(); function give us random chars from the shuffled sets
    * The for loops are fairly simple.
    * Salt1 = 12 char
    * Salt2 = 10 char
    */
    $salt1 = '';
    $salt2 = '';
    
    for($i=0;$i<12;$i++)
    {
        $salt1 .= $set1[rand() % strlen($set1)-.04];
    }
        
    for($i=0;$i<10;$i++)
    {
      $salt2 .= $set2[rand() % strlen($set2)-.07];
    }    
    
    // Now let's generate a pattern. We'll have only about 4 combinations.
    $part[1] = "{salt1}";
    $part[2] = "{salt2}";
    $part[3] = "{pass}";
    $psort   = array_rand($part,3);
    $pattern = $part[$psort[0]].".".$part[$psort[1]].".".$part[$psort[2]];

    // Now for pass
    $grep = array( "/{salt1}/", "/{salt2}/", "/{pass}/" ); // Identify pattern
    $repl = array( $salt1, $salt2, $pass ); // Make pattern real

    // Now replace the pattern with actual values
    $sendpass = preg_replace( $grep, $repl, $pattern );
    
    return array( 'salt1'    => $salt1, 
                  'salt2'    => $salt2, 
                  'password' => sha1($sendpass),
                  'pattern'  => $pattern );
}

/**
 * Validate Password
 *
 * This function provides a way to check passwords encrypted with the {@link encrypt_password()}
 * function.
 * Credit goes to {@link http://microsonic.org/2009/04/05/php-password-salt/} since
 * this function derived from there.
 *
 * @param   string  $pass       The password to check (unencrypted)
 * @param   array   $encrypt    The array of encrypted data returned from {@link encrypt_password()}
 * @return  bool                True if the password is valid, false if not.
 * @since   2.0
 */
function validate_password( $pass, $encrypt )
{
    // Use the grep and replace arrays again to replace information from pattern!
    $grep = array( "/{salt1}/", "/{salt2}/", "/{pass}/" );        // Identify pattern
    $repl = array( $encrypt['salt1'], $encrypt['salt2'], $pass ); // Make pattern real
    $pwd  = preg_replace( $grep, $repl, $encrypt['pattern'] );    // Generate password how it should be.

    // Now let's make sure the user is properly identifying!
    if( sha1($pwd) != $encrypt['password'] )
    {
      return false;
    }
    return true;
}
