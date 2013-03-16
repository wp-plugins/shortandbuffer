<?php
require_once(sprintf("%s/CloudApp/API.php", dirname(__FILE__)));
require_once(sprintf("%s/CloudApp/Exception.php", dirname(__FILE__)));
require_once(sprintf("%s/buffer.php", dirname(__FILE__)));

use CloudApp\API as CloudApp;

class SaB {
  private $_postId = null;
  private $_postObject = null;
  private $_postLink = null;
  private $_postShortLink = null;
  private $_buffer = null;
  
  public function postId($id) {
    if(isset($id)) {
      $this->_postId = $id;
      $this->_postObject = get_post($id);        
    } else {
      return $id;
    }
  }
    
  public function postType() {
    return $this->_postObject->post_type;
  }
    
  public function isPost() {
    return $this->postType() === "post";
  }
    
  public function postTitle() {
    return $this->_postObject->post_title;
  }
    
  public function postLink() {
    if(!isset($this->_postLink)) {
      $this->_postLink = post_permalink($this->postId);
    }
    return $this->_postLink;
  }
    
  public function postShortLink() {
    if(!isset($this->_postShortLink)) {
      $this->shortenUrl($this->postLink(), $this->postTitle());
    }
    return $this->_postShortLink;
  }
    
  public function authorInfo() {
    $authorInfo = null;
      
    if($this->_postObject->post_author >= 0) {
      $authorInfo = get_userdata($this->_postObject->post_author);              
    } else {
      $authorInfo = get_userdata(get_current_user_id());
    }
      
    return $authorInfo;
  }
    
  public function shortenUrl($url, $title) {
    if(!isset($url) || !isset($title)) {
      return null;
    }
    $cloud = new CloudApp(get_option('cloudapp_username'), get_option('cloudapp_password'), 'ShortAndBuffer');
    $cloud->addBookmark($url, $title);
          
    $lastItems = $cloud->getItems();
    if(sizeof($lastItems) == 0) {
      // error
      wp_die(__('Could not retrieve CloudApp URL!'));
      return;
    }
      
    $this->_postShortLink = $lastItems[0]->url;
    return $this->_postShortLink;
  }
    
  public function processUpdateTemplate($updateTemplate) {
    $authorInfo = null;
    $authorInfo = $this->authorInfo();

    $replaces = array(
      '{{title}}' => $this->postTitle(),
      '{{link}}' => $this->postShortLink(),
      '{{author_firstname}}' => $authorInfo->user_firstname,
      '{{author_lastname}}' => $authorInfo->user_lastname,
      '{{author_nickname}}' => $authorInfo->nickname,
      '{{author_displayname}}' => $authorInfo->display_name,
      '{{author_twitter}}' => '@' . $authorInfo->twitter
    );

    foreach($replaces as $replaceVariable => $replaceValue) {
      $updateTemplate = str_replace($replaceVariable, $replaceValue, $updateTemplate);
    }
              
    return $updateTemplate;
  }
    
  public function bufferUpdate() {
    shortandbuffer_set_buffer_session();
      
    $this->buffer = new bufferApp(get_option('buffer_clientid'), get_option('buffer_clientsecret'), get_option('buffer_callback'));
    if (!$this->buffer->ok) {
      // error
      wp_die(__('buffer does not seem to be connected!'));
      return;
    } else {
      $updateTemplate = get_option('buffer_update_text');
      $update = $this->postTitle() . ' ' . $this->postShortLink();
        
      if(isset($updateTemplate) && $updateTemplate !== "") {
        $update = $this->processUpdateTemplate($updateTemplate);
      }
            
      $bufferUpdate = array(
        'text' => $update,
        'media[link]' => $postShortLink,
        'media[description]' => $postTitle
      );
           
      $profiles = $this->buffer->go('/profiles');
      if(is_array($profiles)) {
        $counter = 0;
        foreach($profiles as $profile) {
          if(get_option('buffer_profiles_' . $profile->id) != "true") continue;
          $bufferUpdate['profile_ids[' . $counter . ']'] = $profile->id;
          $counter++;
        }
      }

      if(get_option('buffer_options_now') == "true") {
        $bufferUpdate['now'] = "true";
      }
        
      $this->buffer->go('/updates/create', $bufferUpdate);
    }
  }
}

?>