
<div style="width:40%;padding:0 0 0 0; float:left;">
<form action="options.php" style="margin-left:10px;width:100%" method="post">
        <?php 
        settings_fields( 'woony_plugin_options' );
        do_settings_sections( 'woony_widget' ); 
        echo "<br>";
        ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save changes' ); ?>" />
</form>
</div>
<div style="width:50%;padding:0 0 0 0;float:right;">
        <div style="background-image: url('<?php echo plugins_url().'/widget-woony-talk/assets/background-image.png'?>');
background-size: cover; max-height:400px; min-height:400px; min-width:600px; max-width:600px; padding-top: 100px; padding-bottom: 100px; text-align: center;">
                <h1 style="font-size:26px; color:#ED1C53; padding-top: 10px; margin:10px;">Talk with your visitors!</h1>
                <p style="font-size:20px; color: white;">No account yet?<br/>Get one here, it's free and takes less than a minute.<br/>
                <a style="font-size: 20px; color:white;" href="https://woony.me/sign-up" target="_blank">woony.me/signup</a></p>
        </div>
</div>
<div style="clear:both;"></div>