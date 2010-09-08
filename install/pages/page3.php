<?php if( !defined('CHEVINSTALL') ) die('This page cannot be directly accessed!'); ?>
<div id="admin-install">
    <h2>Create Database Tables</h2>
    <?php 
        $dbresult = create_db_tables(); 
        if( $dbresult['status'] ) printf(($dbresult['warn'] ? $warning_format:$success_format), $dbresult['msg']);
        if( !$dbresult['status'] ) printf($error_format, $dbresult['msg']);
        if( $dbresult['status'] ) { ?>
    <p>
        The database has been successfully setup!  <br />
        You <strong>MUST</strong> delete the install directory before you can proceed.
    </p>
    <?php } ?>
</div>
