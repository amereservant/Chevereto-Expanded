<?php
if( !defined('CHEVINSTALL') ) die('This page cannot be directly accessed!');
if( defined( 'SITE_URL' ) )
{ ?>
<div id="admin-install">
    <?php printf($error_format, 'Installation already complete!'); ?>
    <p>You need to delete the '<strong>install</strong>' directory before being able to proceed.</p>
</div>
<?php exit(); } ?>
<div id="admin-install">
	<?php echo $res_msg; ?>
	<div id="compcheck">
   	    <h2 id="first-h2">Compatibility Check</h2>
        <p>
            <span class="param">PDO extension</span>
            <?php echo $pdo_ext ? $pass_span : $fail_span; ?>
        </p>
        <p>
            <span class="param">SQLite Support</span>
            <?php echo $sqlite ? $pass_span : $fail_span; ?>
        </p>
        <p>
            <span class="param">MySQL Support</span>
            <?php echo $mysql ? $pass_span : $fail_span; ?>
        </p>
        <p>
            <span class="param">Database Directory Writable</span>
            <?php echo $sqlite_path_writeable ? $pass_span : $fail_span; ?>
        </p>
        <p>
            <span class="param">Config File Directory Writable</span>
            <?php echo $config_path_writeable ? $pass_span : $fail_span; ?>
        </p>
        <?php
            if( !$pdo_ext ) printf( $error_format, 'PDO extension MUST be installed first!' );
            if( !$mysql && !$sqlite ) printf( $error_format, 'You must have either mysql or sqlite PDO driver installed!' );
            if( $mysql && !$sqlite ) printf( $warning_format, 'SQLite database type will not be available.' );
            if( !$mysql && $sqlite ) printf( $warning_format, 'MySQL database type will not be available.' );
            if( !$sqlite_path_writeable && $sqlite ) printf( $warning_format, 'SQLite unavailable until database folder is made writable!' );
            if( !$config_path_writeable ) printf( $warning_format, 'You will need to manually create the configuration file!' );
         ?>
        <br />
        <?php if( !$pdo_ext || (!$mysql && !$sqlite) ) { ?>
        <h3>Correct ALL <span style="color:#940000">Errors</span> and you can proceed.  <span style="color:#754E00">Warnings</span> are OK.</h3>
        <?php } else { ?>
        <div class="button"><a href="?install_page=2">next</a></div>
        <?php } ?>
    </div>
</div>
