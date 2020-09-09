<?php

class Lang_hook
{

  public $language;
  function __construct()
  {
    // code...
  }

  public function display_lang()
  {
    $display_lang = get_cookie('display_lang');
    $this->language = empty($display_lang) ? 'thai' : $display_lang;
    $this->lang->load($this->language, $this->language);
  }
}

 ?>
