<?php

class BWGControllerOptions_bwg {

  public function execute() {
    $task = ((isset($_POST['task'])) ? esc_html(stripslashes($_POST['task'])) : '');
    
    if($task != ''){
      if(!WDWLibrary::verify_nonce('options_bwg')){
        die('Sorry, your nonce did not verify.');
      }
    }

    if (method_exists($this, $task)) {
      $this->$task();
    }
    else {
      $this->display();
    }
  }

  public function display() {
    require_once WD_BWG_DIR . "/admin/models/BWGModelOptions_bwg.php";
    $model = new BWGModelOptions_bwg();

    require_once WD_BWG_DIR . "/admin/views/BWGViewOptions_bwg.php";
    $view = new BWGViewOptions_bwg($model);
    $view->display();
  }
  
  public function reset() {
    require_once WD_BWG_DIR . "/admin/models/BWGModelOptions_bwg.php";
    $model = new BWGModelOptions_bwg();

    require_once WD_BWG_DIR . "/admin/views/BWGViewOptions_bwg.php";
    $view = new BWGViewOptions_bwg($model);
    echo WDWLibrary::message('Changes must be saved.', 'wd_error');
    $view->display(true);
  }

  public function save() {
    $this->save_db();
    $this->display();
  }
  
  public function save_db() {
    $row = new WD_BWG_Options();
    if (isset($_POST['old_images_directory'])) {
      $row->old_images_directory = esc_html(stripslashes($_POST['old_images_directory']));
    }
    if (isset($_POST['images_directory'])) {
      $row->images_directory = esc_html(stripslashes($_POST['images_directory']));
      if (!is_dir(ABSPATH . $row->images_directory) || (is_dir(ABSPATH . $row->images_directory . '/photo-gallery') && $row->old_images_directory && $row->old_images_directory != $row->images_directory)) {
        if (!is_dir(ABSPATH . $row->images_directory)) {
          echo WDWLibrary::message('Uploads directory doesn\'t exist. Old value is restored.', 'wd_error');
        }
        else {
          echo WDWLibrary::message('Warning: "photo-gallery" folder already exists in uploads directory. Old value is restored.', 'wd_error');
        }
        if ($row->old_images_directory) {
          $row->images_directory = $row->old_images_directory;
        }
        else {
          $upload_dir = wp_upload_dir();
          if (!is_dir($upload_dir['basedir'] . '/photo-gallery')) {
            mkdir($upload_dir['basedir'] . '/photo-gallery', 0777);
          }
          $row->images_directory = str_replace(ABSPATH, '', $upload_dir['basedir']);
        }
      }
    }
    else {
      $upload_dir = wp_upload_dir();
      if (!is_dir($upload_dir['basedir'] . '/photo-gallery')) {
        mkdir($upload_dir['basedir'] . '/photo-gallery', 0777);
      }
      $row->images_directory = str_replace(ABSPATH, '', $upload_dir['basedir']);
    }

    foreach ($row as $name => $value) {
      if ($name == 'autoupdate_interval') {
        $autoupdate_interval = (isset($_POST['autoupdate_interval_hour']) && isset($_POST['autoupdate_interval_min']) ? ((int) $_POST['autoupdate_interval_hour'] * 60 + (int) $_POST['autoupdate_interval_min']) : null);
        /*minimum autoupdate interval is 1 min*/
        $autoupdate_interval = isset($autoupdate_interval) && $autoupdate_interval >= 1 ? $autoupdate_interval : 1;
      }
      else if ($name != 'images_directory' && isset($_POST[$name])) {
        $row->$name = esc_html(stripslashes($_POST[$name]));
      }
    }

    $save = update_option('wd_bwg_options', json_encode($row), 'no');

    if (isset($_POST['watermark']) && $_POST['watermark'] == "image_set_watermark") {
      $this->image_set_watermark();
    }

    if ($save) {
      if ($row->old_images_directory && $row->old_images_directory != $row->images_directory) {
        rename(ABSPATH . $row->old_images_directory . '/photo-gallery', ABSPATH . $row->images_directory . '/photo-gallery');
      }
      if (!is_dir(ABSPATH . $row->images_directory . '/photo-gallery')) {
        mkdir(ABSPATH . $row->images_directory . '/photo-gallery', 0777);
      }
      if (isset($_POST['recreate']) && $_POST['recreate'] == "resize_image_thumb") {
        $this->resize_image_thumb();
        echo WDWLibrary::message(__('All thumbnails are successfully recreated.', 'bwg_back'), 'wd_updated');
      }
      else {
        echo WDWLibrary::message(__('Item Succesfully Saved.', 'bwg_back'), 'wd_updated');
      }
    }
  }
  
  function bwg_hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);
    if (strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    }
    else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
    }
    $rgb = array($r, $g, $b);
    return $rgb;
  }
  
  function bwg_imagettfbboxdimensions($font_size, $font_angle, $font, $text) {
    $box = @ImageTTFBBox($font_size, $font_angle, $font, $text) or die;
    $max_x = max(array($box[0], $box[2], $box[4], $box[6]));
    $max_y = max(array($box[1], $box[3], $box[5], $box[7]));
    $min_x = min(array($box[0], $box[2], $box[4], $box[6]));
    $min_y = min(array($box[1], $box[3], $box[5], $box[7]));
    return array(
      "width"  => ($max_x - $min_x),
      "height" => ($max_y - $min_y)
    );
  }

  public function image_set_watermark() {
    global $wpdb;
    global $WD_BWG_UPLOAD_DIR;
    global $wd_bwg_options;
    $images = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'bwg_image');
    switch ($wd_bwg_options->built_in_watermark_type) {
      case 'text':
        foreach ($images as $image) {
            $this->set_text_watermark(ABSPATH . $WD_BWG_UPLOAD_DIR . $image->image_url, ABSPATH . $WD_BWG_UPLOAD_DIR . $image->image_url, $wd_bwg_options->built_in_watermark_text, $wd_bwg_options->built_in_watermark_font, $wd_bwg_options->built_in_watermark_font_size, '#' . $wd_bwg_options->built_in_watermark_color, $wd_bwg_options->built_in_watermark_opacity, $wd_bwg_options->built_in_watermark_position);
        }
        break;
      case 'image':
        $watermark_path = str_replace(site_url() . '/', ABSPATH, $wd_bwg_options->built_in_watermark_url);
        foreach ($images as $image) {
          $this->set_image_watermark(ABSPATH . $WD_BWG_UPLOAD_DIR . $image->image_url, ABSPATH . $WD_BWG_UPLOAD_DIR . $image->image_url, $watermark_path, $wd_bwg_options->built_in_watermark_size, $wd_bwg_options->built_in_watermark_size, $wd_bwg_options->built_in_watermark_position);
        }
        break;
    }
  }

  function set_text_watermark($original_filename, $dest_filename, $watermark_text, $watermark_font, $watermark_font_size, $watermark_color, $watermark_transparency, $watermark_position) {
    if (!file_exists($original_filename)) {
      return;
    }
    $original_filename = htmlspecialchars_decode($original_filename, ENT_COMPAT | ENT_QUOTES);
    $dest_filename = htmlspecialchars_decode($dest_filename, ENT_COMPAT | ENT_QUOTES);

    $watermark_transparency = 127 - ($watermark_transparency * 1.27);
    list($width, $height, $type) = getimagesize($original_filename);
    $watermark_image = imagecreatetruecolor($width, $height);
    $watermark_color = $this->bwg_hex2rgb($watermark_color);
    $watermark_color = imagecolorallocatealpha($watermark_image, $watermark_color[0], $watermark_color[1], $watermark_color[2], $watermark_transparency);
    $watermark_font = WD_BWG_DIR . '/fonts/' . $watermark_font;
    $watermark_font_size = $height * $watermark_font_size / 500;
    $watermark_position = explode('-', $watermark_position);
    $watermark_sizes = $this->bwg_imagettfbboxdimensions($watermark_font_size, 0, $watermark_font, $watermark_text);

    $top = $height - 5;
    $left = $width - $watermark_sizes['width'] - 5;
    switch ($watermark_position[0]) {
      case 'top':
        $top = $watermark_sizes['height'] + 5;
        break;
      case 'middle':
        $top = ($height + $watermark_sizes['height']) / 2;
        break;
    }
    switch ($watermark_position[1]) {
      case 'left':
        $left = 5;
        break;
      case 'center':
        $left = ($width - $watermark_sizes['width']) / 2;
        break;
    }
    @ini_set('memory_limit', '-1');
    if ($type == 2) {
      $image = imagecreatefromjpeg($original_filename);
      imagettftext($image, $watermark_font_size, 0, $left, $top, $watermark_color, $watermark_font, $watermark_text);
      imagejpeg ($image, $dest_filename, 100);
      imagedestroy($image);  
    }
    elseif ($type == 3) {
      $image = imagecreatefrompng($original_filename);
      imagettftext($image, $watermark_font_size, 0, $left, $top, $watermark_color, $watermark_font, $watermark_text);
      imageColorAllocateAlpha($image, 0, 0, 0, 127);
      imagealphablending($image, FALSE);
      imagesavealpha($image, TRUE);
      imagepng($image, $dest_filename, 9);
      imagedestroy($image);
    }
    elseif ($type == 1) {
      $image = imagecreatefromgif($original_filename);
      imageColorAllocateAlpha($watermark_image, 0, 0, 0, 127);
      imagecopy($watermark_image, $image, 0, 0, 0, 0, $width, $height);
      imagettftext($watermark_image, $watermark_font_size, 0, $left, $top, $watermark_color, $watermark_font, $watermark_text);
      imagealphablending($watermark_image, FALSE);
      imagesavealpha($watermark_image, TRUE);
      imagegif($watermark_image, $dest_filename);
      imagedestroy($image);
    }
    imagedestroy($watermark_image);
    @ini_restore('memory_limit');
  }

  function set_image_watermark($original_filename, $dest_filename, $watermark_url, $watermark_height, $watermark_width, $watermark_position) {
    if (!file_exists($original_filename)) {
      return;
    }
    $original_filename = htmlspecialchars_decode($original_filename, ENT_COMPAT | ENT_QUOTES);
    $dest_filename = htmlspecialchars_decode($dest_filename, ENT_COMPAT | ENT_QUOTES);
    $watermark_url = htmlspecialchars_decode($watermark_url, ENT_COMPAT | ENT_QUOTES);
  
    list($width, $height, $type) = getimagesize($original_filename);
    list($width_watermark, $height_watermark, $type_watermark) = getimagesize($watermark_url);
    $watermark_width = $width * $watermark_width / 100;
    $watermark_height = $height_watermark * $watermark_width / $width_watermark;
     
    $watermark_position = explode('-', $watermark_position);
    $top = $height - $watermark_height - 5;
    $left = $width - $watermark_width - 5;
    switch ($watermark_position[0]) {
      case 'top':
        $top = 5;
        break;
      case 'middle':
        $top = ($height - $watermark_height) / 2;
        break;
    }
    switch ($watermark_position[1]) {
      case 'left':
        $left = 5;
        break;
      case 'center':
        $left = ($width - $watermark_width) / 2;
        break;
    }
    @ini_set('memory_limit', '-1');
    if ($type_watermark == 2) {
      $watermark_image = imagecreatefromjpeg($watermark_url);        
    }
    elseif ($type_watermark == 3) {
      $watermark_image = imagecreatefrompng($watermark_url);
    }
    elseif ($type_watermark == 1) {
      $watermark_image = imagecreatefromgif($watermark_url);      
    }
    else {
      return false;
    }
    
    $watermark_image_resized = imagecreatetruecolor($watermark_width, $watermark_height);
    imagecolorallocatealpha($watermark_image_resized, 255, 255, 255, 127);
    imagealphablending($watermark_image_resized, FALSE);
    imagesavealpha($watermark_image_resized, TRUE);
    imagecopyresampled ($watermark_image_resized, $watermark_image, 0, 0, 0, 0, $watermark_width, $watermark_height, $width_watermark, $height_watermark);
        
    if ($type == 2) {
      $image = imagecreatefromjpeg($original_filename);
      imagecopy($image, $watermark_image_resized, $left, $top, 0, 0, $watermark_width, $watermark_height);
      if ($dest_filename <> '') {
        imagejpeg ($image, $dest_filename, 100); 
      } else {
        header('Content-Type: image/jpeg');
        imagejpeg($image, null, 100);
      };
      imagedestroy($image);  
    }
    elseif ($type == 3) {
      $image = imagecreatefrompng($original_filename);
      imagecopy($image, $watermark_image_resized, $left, $top, 0, 0, $watermark_width, $watermark_height);
      imagealphablending($image, FALSE);
      imagesavealpha($image, TRUE);
      imagepng($image, $dest_filename, 9);
      imagedestroy($image);
    }
    elseif ($type == 1) {
      $image = imagecreatefromgif($original_filename);
      $tempimage = imagecreatetruecolor($width, $height);
      imagecopy($tempimage, $image, 0, 0, 0, 0, $width, $height);
      imagecopy($tempimage, $watermark_image_resized, $left, $top, 0, 0, $watermark_width, $watermark_height);
      imagegif($tempimage, $dest_filename);
      imagedestroy($image);
      imagedestroy($tempimage);
    }
    imagedestroy($watermark_image);
    @ini_restore('memory_limit');
  }
  
  
  public function image_recover_all() {
    global $wpdb;
    global $wd_bwg_options;
    $thumb_width = $wd_bwg_options->upload_thumb_width;
    $thumb_height = $wd_bwg_options->upload_thumb_height;
    $image_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'bwg_image');
    foreach ($image_ids_col as $image_id) {
        $this->recover_image($image_id, $thumb_width, $thumb_height);
    }
    $this->display();
  }
  
  public function recover_image($id, $thumb_width, $thumb_height) {
    global $WD_BWG_UPLOAD_DIR;
    global $wpdb;
    $image_data = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'bwg_image WHERE id="%d"', $id));
    $filename = htmlspecialchars_decode(ABSPATH . $WD_BWG_UPLOAD_DIR . $image_data->image_url, ENT_COMPAT | ENT_QUOTES);
    $thumb_filename = htmlspecialchars_decode(ABSPATH . $WD_BWG_UPLOAD_DIR . $image_data->thumb_url, ENT_COMPAT | ENT_QUOTES);
   
    if (file_exists($filename) && file_exists($thumb_filename)) {
      copy(str_replace('/thumb/', '/.original/', $thumb_filename), $filename);
      list($width_orig, $height_orig, $type_orig) = getimagesize($filename);
      $percent = $width_orig / $thumb_width;
      $thumb_height = $height_orig / $percent;
      @ini_set('memory_limit', '-1');
      if ($type_orig == 2) {
        $img_r = imagecreatefromjpeg($filename);
        $dst_r = ImageCreateTrueColor($thumb_width, $thumb_height);
        imagecopyresampled($dst_r, $img_r, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);
        imagejpeg($dst_r, $thumb_filename, 100);
        imagedestroy($img_r);
        imagedestroy($dst_r);
      }
      elseif ($type_orig == 3) {
        $img_r = imagecreatefrompng($filename);
        $dst_r = ImageCreateTrueColor($thumb_width, $thumb_height);
        imageColorAllocateAlpha($dst_r, 0, 0, 0, 127);
        imagealphablending($dst_r, FALSE);
        imagesavealpha($dst_r, TRUE);
        imagecopyresampled($dst_r, $img_r, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);
        imagealphablending($dst_r, FALSE);
        imagesavealpha($dst_r, TRUE);
        imagepng($dst_r, $thumb_filename, 9);
        imagedestroy($img_r);
        imagedestroy($dst_r);
      }
      elseif ($type_orig == 1) {
        $img_r = imagecreatefromgif($filename);
        $dst_r = ImageCreateTrueColor($thumb_width, $thumb_height);
        imageColorAllocateAlpha($dst_r, 0, 0, 0, 127);
        imagealphablending($dst_r, FALSE);
        imagesavealpha($dst_r, TRUE);
        imagecopyresampled($dst_r, $img_r, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);
        imagealphablending($dst_r, FALSE);
        imagesavealpha($dst_r, TRUE);
        imagegif($dst_r, $thumb_filename);
        imagedestroy($img_r);
        imagedestroy($dst_r);
      }
      @ini_restore('memory_limit');
    }
  }

   public function resize_image_thumb() {
    global $WD_BWG_UPLOAD_DIR;
    global $wpdb;
    global $wd_bwg_options;
    $img_ids = $wpdb->get_results('SELECT id, thumb_url FROM ' . $wpdb->prefix . 'bwg_image');
    foreach ($img_ids as $img_id) {
        $file_path = str_replace("thumb", ".original", htmlspecialchars_decode(ABSPATH . $WD_BWG_UPLOAD_DIR . $img_id->thumb_url, ENT_COMPAT | ENT_QUOTES));
	      $new_file_path = htmlspecialchars_decode(ABSPATH . $WD_BWG_UPLOAD_DIR . $img_id->thumb_url, ENT_COMPAT | ENT_QUOTES);
        list($img_width, $img_height, $type) = @getimagesize(htmlspecialchars_decode($file_path, ENT_COMPAT | ENT_QUOTES));
        if (!$img_width || !$img_height) {
          continue;
        }
        $max_width = $wd_bwg_options->upload_thumb_width;
        $max_height = $wd_bwg_options->upload_thumb_height;
        $scale = min(
          $max_width / $img_width,
          $max_height / $img_height
        );
        @ini_set('memory_limit', '-1');
        if (!function_exists('imagecreatetruecolor')) {
          error_log('Function not found: imagecreatetruecolor');
          return FALSE;
        }
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        $dst_x = 0;
        $dst_y = 0;
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        switch ($type) {
          case 2:
            $src_img = @imagecreatefromjpeg($file_path);
            $write_image = 'imagejpeg';
            $image_quality = isset($wd_bwg_options->jpeg_quality) ? $wd_bwg_options->jpeg_quality : 75;
            break;
          case 1:
            @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
            $src_img = @imagecreatefromgif($file_path);
            $write_image = 'imagegif';
            $image_quality = null;
            break;
          case 3:
            @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
            @imagealphablending($new_img, false);
            @imagesavealpha($new_img, true);
            $src_img = @imagecreatefrompng($file_path);
            $write_image = 'imagepng';
            $image_quality = isset($wd_bwg_options->png_quality) ? $wd_bwg_options->png_quality : 9;
            break;
          default:
            $src_img = null;
            break;
        }
        $success = $src_img && @imagecopyresampled(
          $new_img,
          $src_img,
          $dst_x,
          $dst_y,
          0,
          0,
          $new_width,
          $new_height,
          $img_width,
          $img_height
        ) && $write_image($new_img, $new_file_path, $image_quality);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($src_img);
        @imagedestroy($new_img);
        @ini_restore('memory_limit');
	  }
  }

}