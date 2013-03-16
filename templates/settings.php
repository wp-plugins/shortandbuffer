<div class="wrap">
    <h2>ShortAndBuffer</h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('shortandbuffer-group'); ?>
        <?php @do_settings_fields('shortandbuffer-group'); ?>
        <?php do_settings_sections('shortandbuffer'); ?>
        <?php @submit_button(); ?>
    </form>
</div>
