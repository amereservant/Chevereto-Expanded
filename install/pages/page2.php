<?php if( !defined('CHEVINSTALL') ) die('This page cannot be directly accessed!'); ?>
<div id="admin-install">
	<?php echo $res_msg; ?>
	<?php if( !$write_config ) { ?>
	<form action="install/configdl.php" method="post" enctype="multipart/form-data">
	<p>
	    You must either download and copy a generated <strong>config.php</strong> file <br />
	    or manually create the file <strong>config.php</strong> and put the following in it:
	    <br />
	    <textarea name="config_create_file" cols="80" rows="25" readonly="readonly"><?php echo $result['config']; ?></textarea>
	</p>
	<p style="text-align:center"><button type="submit" class="install-btn">Download</button> <button type="button" onClick='window.location="?install_page=3"' class="install-btn">Continue</button></p>
	</form>
	<?php } elseif( $write_config && isset($result['status']) && $result['status'] || $config_file_exists ) { 
	    printf($success_format, 'Configuration File Successfully Created!'); ?>
	<p style="text-align:center"><button type="button" onClick='window.location="?install_page=3"' class="install-btn">Continue</button></p>
	<?php } else { ?>
    <form action="" method="post" enctype="multipart/form-data">
   	    <h2>Admin Login</h2>
        <p>
            <label for="admin-username">Username:</label>
            <input name="username" id="admin-username" value="<?php echo isset($_POST['username']) ? $_POST['username']:''; ?>" type="text" />
		</p>
		<p>
            <label for="admin-email">Email:</label>
            <input name="email" id="admin-email" type="text" />
        </p>
        <p>
            <label for="admin-password">Password:</label>
            <input name="password" id="admin-password" type="password" />
        </p>
        <p>
            <label for="confirm-password">Confirm Password:</label>
            <input name="confirm-password" id="confirm-password" type="password" />
        </p>
        <h2>Site Information</h2>
        <p class="description">Please verify the site URL and make any neccessary changes.</p>
        <p>
            <label for="site-url">Site URL:</label>
            <input name="site-url" id="site-url" value="<?php echo isset($_POST['site-url']) ? $_POST['site-url'] : $base_url ; ?>" />
        </p>
        <h2>Database Information</h2>
        <p class="description"> Please specify either valid MySQL details or a SQLite filename. <br />
        	Do NOT fill both in since only one can be used!
        </p>
        <?php if( !$mysql ) { ?><div style="display:none"> <?php } ?>
        <h3>MySQL</h3>
        <p>
        	<label for="mysql-user">Username:</label>
            <input name="mysql-user" id="mysql-user" value="<?php echo isset($_POST['mysql-user']) ? $_POST['mysql-user']:''; ?>" type="text" />
        </p>
        <p>
        	<label for="mysql-password">Password:</label>
            <input name="mysql-password" id="mysql-password" value="<?php echo isset($_POST['mysql-password']) ? $_POST['mysql-password']:''; ?>" type="password" />
        </p>
        <p>
        	<label for="mysql-dbname">Database:</label>
            <input name="mysql-dbname" id="mysql-dbname" value="<?php echo isset($_POST['mysql-dbname']) ? $_POST['mysql-dbname']:''; ?>" type="text" />
        </p>
        <p>
        	<label for="mysql-host">Host:</label>
            <input name="mysql-host" id="mysql-host" value="<?php echo isset($_POST['mysql-host']) ? $_POST['mysql-host']:'localhost'; ?>" type="text" />
        </p>
        <?php if( !$mysql ) { ?></div> <?php } ?>
        <?php if( !$sqlite || !$sqlite_path_writeable ) { ?><div style="display:none"> <?php } ?>
        <h3>SQLITE</h3>
        <p class="description"> ONLY enter the file name, no paths or file extension.</p>
        <p>
        	<label for="sqlite">SQLite Filename:</label>
            <input name="sqlite" id="sqlite" value="<?php echo isset($_POST['sqlite']) ? $_POST['sqlite']:''; ?>" type="text"  /><span class="smallext"> .sdb</span>
        </p>
        <?php if( !$sqlite || !$sqlite_path_writeable ) { ?></div> <?php 
        printf($warning_format, 'SQLite database is unavailable.'); 
        } ?>
        <br />
        <p class="center">
        	<input type="hidden" value="<?php echo $submitkey; ?>" name="submitkey" />
        	<button type="submit" class="install-btn">Install</button>
       	</p>
    </form>
  <?php } ?>
</div>
