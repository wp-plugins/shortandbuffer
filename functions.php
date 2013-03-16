<?php
  function shortandbuffer_set_buffer_session() {
    if(strlen(get_option('buffer_accesstoken'))<6) {
      return;
    } else {
      $_SESSION['oauth'] = array('buffer' => array('access_token'));
      $_SESSION['oauth']['buffer']['access_token'] = get_option('buffer_accesstoken');
    }
  }  
?>
