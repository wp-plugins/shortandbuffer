<?php
if(!class_exists('ShortAndBuffer_Settings'))
{
	class ShortAndBuffer_Settings	{
    private $buffer;
    
		public function __construct()	{
      shortandbuffer_set_buffer_session();

      $this->buffer = new bufferApp(get_option('buffer_clientid'), get_option('buffer_clientsecret'), get_option('buffer_callback'));
      
      
      add_action('admin_init', array(&$this, 'admin_init'));
      add_action('admin_menu', array(&$this, 'add_menu'));
		}

    public function admin_init() {
    	register_setting('shortandbuffer-group', 'cloudapp_username');
    	register_setting('shortandbuffer-group', 'cloudapp_password');
    	register_setting('shortandbuffer-group', 'buffer_clientid');
    	register_setting('shortandbuffer-group', 'buffer_clientsecret');
    	register_setting('shortandbuffer-group', 'buffer_callback');
    	register_setting('shortandbuffer-group', 'buffer_update_text');

      // CloudApp
    	add_settings_section(
  	    'shortandbuffer-section-cloudapp', 
  	    'ShortAndBuffer CloudApp Settings', 
  	    array(&$this, 'settings_section_shortandbuffer_cloudapp'), 
  	    'shortandbuffer'
    	);
        	
      add_settings_field(
        'shortandbuffer-cloudapp_username', 
        'CloudApp username', 
        array(&$this, 'settings_field_input_text'), 
        'shortandbuffer', 
        'shortandbuffer-section-cloudapp',
        array(
          'field' => 'cloudapp_username'
        )
      );
      add_settings_field(
        'shortandbuffer-cloudapp_password', 
        'CloudApp password', 
        array(&$this, 'settings_field_input_password'), 
        'shortandbuffer', 
        'shortandbuffer-section-cloudapp',
        array(
          'field' => 'cloudapp_password'
        )
      );
      
        
      // buffer    
    	add_settings_section(
  	    'shortandbuffer-section-buffer', 
  	    'ShortAndBuffer buffer Settings', 
  	    array(&$this, 'settings_section_shortandbuffer_buffer'), 
  	    'shortandbuffer'
    	);      
      add_settings_field(
        'shortandbuffer-buffer_clientid', 
        'buffer Client ID', 
        array(&$this, 'settings_field_input_text'), 
        'shortandbuffer', 
        'shortandbuffer-section-buffer',
        array(
          'field' => 'buffer_clientid'
        )
      );

      add_settings_field(
        'shortandbuffer-buffer_clientsecret', 
        'buffer Client Secret', 
        array(&$this, 'settings_field_input_text'), 
        'shortandbuffer', 
        'shortandbuffer-section-buffer',
        array(
          'field' => 'buffer_clientsecret'
        )
      );

      add_settings_field(
        'shortandbuffer-buffer_callback', 
        'buffer Callback URL (has to be this URL you are currently looking at)', 
        array(&$this, 'settings_field_input_text'), 
        'shortandbuffer', 
        'shortandbuffer-section-buffer',
        array(
          'field' => 'buffer_callback'
        )
      );
      
      if(isset($this->buffer) && method_exists($this->buffer, "go")) {
      	add_settings_section(
    	    'shortandbuffer-section-buffer-profiles', 
    	    'ShortAndBuffer Active buffer Profiles', 
    	    array(&$this, 'settings_section_shortandbuffer_buffer_profiles'), 
    	    'shortandbuffer'
      	);      
        
        
        $this->settings_section_shortandbuffer_buffer_list_profiles();
      }
      
    	add_settings_section(
  	    'shortandbuffer-section-buffer-update', 
  	    'ShortAndBuffer buffer Update', 
  	    array(&$this, 'settings_section_shortandbuffer_buffer_update'), 
  	    'shortandbuffer'
    	);      
      add_settings_field(
        'shortandbuffer-buffer_update_text', 
        'buffer Update Message', 
        array(&$this, 'settings_field_input_text'), 
        'shortandbuffer', 
        'shortandbuffer-section-buffer-update',
        array(
          'field' => 'buffer_update_text'
        )
      );

    	add_settings_section(
  	    'shortandbuffer-section-buffer-options', 
  	    'ShortAndBuffer buffer Options', 
  	    array(&$this, 'settings_section_shortandbuffer_buffer_options'), 
  	    'shortandbuffer'
    	);      
      add_settings_field(
        'shortandbuffer-buffer_options_now', 
        'Share buffers immediately', 
        array(&$this, 'settings_field_input_checkbox'), 
        'shortandbuffer', 
        'shortandbuffer-section-buffer-options',
        array(
          'field' => 'buffer_options_now',
          'check_id' => 'options_now'
        )
      );          
      
    }
    
    public function settings_section_shortandbuffer_buffer_list_profiles() {
      $profiles = $this->buffer->go('/profiles');
      if (is_array($profiles)) {
        foreach ($profiles as $profile) {
        	register_setting('shortandbuffer-group', 'buffer_profiles_' . $profile->id);
          add_settings_field(
            'shortandbuffer-buffer_profiles_' . $profile->id, 
            $profile->service_username . ' (' . $profile->service . ')', 
            array(&$this, 'settings_field_input_checkbox'), 
            'shortandbuffer', 
            'shortandbuffer-section-buffer-profiles',
            array(
              'field' => 'buffer_profiles',
              'check_id' => $profile->id
            )
          );          
        }
      }      
    }
   
    public function settings_section_shortandbuffer_cloudapp() {
      echo 'Configure your CloudApp account here.';
    }

    public function settings_section_shortandbuffer_buffer() {
      echo 'Configure your buffer account here.&nbsp;';

      if (!$this->buffer->ok) {
        echo '<a href="' . $this->buffer->get_login_url() . '">Please connect to buffer first!</a>';
      } else {
        update_option('buffer_accesstoken', $_SESSION['oauth']['buffer']['access_token']);
        echo 'Great! Your access token is: <code>' . get_option('buffer_accesstoken') . '</code>';
      }
    }

    public function settings_section_shortandbuffer_buffer_profiles() {
      echo 'Check the buffer profiles that should be published to.';
    }

    public function settings_section_shortandbuffer_buffer_update() {
      echo 'Customize how your buffer update should look like. The following variables are available:<br/>';
      echo '<br/>';
      echo '{{title}} the post title<br/>';
      echo '{{link}} the shortened URL to the post<br/>';
      echo '{{author_firstname}} the author\'s first name<br/>';
      echo '{{author_lastname}} the author\'s last name<br/>';
      echo '{{author_nickname}} the author\'s nickname<br/>';
      echo '{{author_displayname}} the author\'s display name<br/>';
      echo '{{author_twitter}} the author\'s twitter handle, if available<br/>';
      echo '<br/>';      
    }

    public function settings_section_shortandbuffer_buffer_options() {
      echo 'buffer options.';
    }
    
    public function settings_field_input_text($args) {
        $field = $args['field'];
        $value = get_option($field);
        echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value);
    }

    public function settings_field_input_password($args) {
        $field = $args['field'];
        $value = get_option($field);
        echo sprintf('<input type="password" name="%s" id="%s" value="%s" />', $field, $field, $value);
    }
    
    public function settings_field_input_checkbox($args) {
      $field = $args['field'];
      $check_id = $args['check_id'];
      
      
      $isActive = false;
      $val = get_option($field . '_' . $check_id);
      if($val == "true") {
        $isActive = true;
      }
      
      echo sprintf('<input type="checkbox" name="%s_%s" id="%s_%s" value="true" %s/>', $field, $check_id, $field, $check_id, ($isActive ? 'checked' : ''));
    }
        
    public function add_menu() {
    	add_options_page(
  	    'ShortAndBuffer Settings', 
  	    'ShortAndBuffer', 
  	    'manage_options', 
  	    'shortandbuffer', 
  	    array(&$this, 'plugin_settings_page')
    	);
    }
    
    public function plugin_settings_page() {
    	if(!current_user_can('manage_options'))	{
    		wp_die(__('You do not have sufficient permissions to access this page.'));
    	}
	
    	include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
    }
  }
}
