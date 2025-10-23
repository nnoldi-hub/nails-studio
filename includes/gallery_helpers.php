<?php
// Shared gallery helper functions: image validation, optimization, thumbnails, placeholders

// Ensure placeholder exists (100x100 png)
function gh_ensure_placeholder($placeholderPath) {
    if (!file_exists($placeholderPath)) {
        $base64 = 'iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAQAAAB1y0hLAAAACXBIWXMAAAsTAAALEwEAmpwYAAABHklEQVR4nO3SsQ2CUBRF0W+QcwIR2kawABbACJYgIVjgwCwhfgpL2pmq9pm8Zc7vnkHAAAAAAAAAMC3fddt+33fbdvt+2zbbrtt22233bZt2+223bZtt9323bZtO5brPfRZ17bb6z3UWde226s91FnXtturPZRZ17bbqz3UWde226s91Fk13+gBDOs/fAAAAAADgXwXPAAGwqPy7AAAAAElFTkSuQmCC';
        @file_put_contents($placeholderPath, base64_decode($base64));
    }
}

function gh_valid_image($path) {
    return ($path && file_exists($path) && @getimagesize($path) !== false);
}

// Optimize (resize/compress) existing image in place
function gh_optimize_image($srcPath, $maxWidth = 1400, $quality = 82) {
    if (!file_exists($srcPath)) return false;
    $info = @getimagesize($srcPath); if (!$info) return false;
    [$width,$height] = $info; $mime = $info['mime'] ?? '';
    if ($width <= 0 || $height <= 0) return false;
    if ($width > $maxWidth) { $newWidth = $maxWidth; $newHeight = (int) (($maxWidth/$width)*$height); } else { $newWidth=$width; $newHeight=$height; }
    switch($mime) {
        case 'image/jpeg': case 'image/jpg': $im=@imagecreatefromjpeg($srcPath); break;
        case 'image/png': $im=@imagecreatefrompng($srcPath); break;
        case 'image/gif': $im=@imagecreatefromgif($srcPath); break;
        case 'image/webp': if(function_exists('imagecreatefromwebp')) { $im=@imagecreatefromwebp($srcPath); break; }
        default: return false;
    }
    if(!$im) return false;
    if ($newWidth === $width) {
        $canvas=$im;
    } else {
        $canvas=imagecreatetruecolor($newWidth,$newHeight);
        if (in_array($mime,['image/png','image/gif'])) {
            imagecolortransparent($canvas, imagecolorallocatealpha($canvas,0,0,0,127));
            imagealphablending($canvas,false); imagesavealpha($canvas,true);
        }
        imagecopyresampled($canvas,$im,0,0,0,0,$newWidth,$newHeight,$width,$height);
        imagedestroy($im);
    }
    $ext = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));
    $ok=false;
    switch($ext){
        case 'jpg': case 'jpeg': $ok=imagejpeg($canvas,$srcPath,$quality); break;
        case 'png': $pngQ=(int)round((100-$quality)/11.111); $ok=imagepng($canvas,$srcPath,min(max($pngQ,0),9)); break;
        case 'gif': $ok=imagegif($canvas,$srcPath); break;
        case 'webp': if(function_exists('imagewebp')) $ok=imagewebp($canvas,$srcPath,$quality); break;
    }
    if($canvas) imagedestroy($canvas);
    return $ok;
}

// Create thumbnail (does not overwrite if exists and valid)
function gh_create_thumbnail($originalPath, $thumbPath, $maxWidth = 400, $quality = 80) {
    if (gh_valid_image($thumbPath)) return true; // already good
    if (!file_exists($originalPath)) return false;
    $info = @getimagesize($originalPath); if(!$info) return false;
    [$width,$height]=$info; $mime=$info['mime']??'';
    if ($width <= $maxWidth) { // copy smaller images
        return @copy($originalPath, $thumbPath);
    }
    $newWidth = $maxWidth; $newHeight = (int)(($maxWidth/$width)*$height);
    switch($mime){
        case 'image/jpeg': case 'image/jpg': $im=@imagecreatefromjpeg($originalPath); break;
        case 'image/png': $im=@imagecreatefrompng($originalPath); break;
        case 'image/gif': $im=@imagecreatefromgif($originalPath); break;
        case 'image/webp': if(function_exists('imagecreatefromwebp')) { $im=@imagecreatefromwebp($originalPath); break; }
        default: return false;
    }
    if(!$im) return false;
    $canvas=imagecreatetruecolor($newWidth,$newHeight);
    if (in_array($mime,['image/png','image/gif'])) { imagecolortransparent($canvas, imagecolorallocatealpha($canvas,0,0,0,127)); imagealphablending($canvas,false); imagesavealpha($canvas,true);}    
    imagecopyresampled($canvas,$im,0,0,0,0,$newWidth,$newHeight,$width,$height);
    $ext=strtolower(pathinfo($thumbPath, PATHINFO_EXTENSION));
    $ok=false;
    switch($ext){
        case 'jpg': case 'jpeg': $ok=imagejpeg($canvas,$thumbPath,$quality); break;
        case 'png': $pngQ=(int)round((100-$quality)/11.111); $ok=imagepng($canvas,$thumbPath,min(max($pngQ,0),9)); break;
        case 'gif': $ok=imagegif($canvas,$thumbPath); break;
        case 'webp': if(function_exists('imagewebp')) $ok=imagewebp($canvas,$thumbPath,$quality); break;
    }
    imagedestroy($canvas); imagedestroy($im);
    return $ok;
}

// Process uploaded file: returns [success(bool), message_or_filename]
function gh_process_upload($fileArray, $destDir) {
    if (!isset($fileArray) || $fileArray['error'] === UPLOAD_ERR_NO_FILE) {
        return [false, 'Nu a fost selectat niciun fișier'];
    }
    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        return [false, 'Eroare la upload (cod '.$fileArray['error'].')'];
    }
    $allowed = ['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
    $mime = mime_content_type($fileArray['tmp_name']);
    if (!in_array($mime,$allowed)) return [false,'Tip de fișier nepermis: '.htmlspecialchars($mime)];
    $ext = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION)); if ($ext==='jpeg') $ext='jpg';
    if (!is_dir($destDir)) @mkdir($destDir,0775,true);
    if (!is_writable($destDir)) return [false,'Directorul nu este inscriptibil'];
    $name = 'gallery_'.date('Ymd_His').'_'.bin2hex(random_bytes(4)).'.'.$ext;
    $full = rtrim($destDir,'/').'/'.$name;
    if (!move_uploaded_file($fileArray['tmp_name'],$full)) {
        @file_put_contents(__DIR__.'/../upload_debug.log', date('c')." FAIL move_uploaded_file to $full\n", FILE_APPEND);
        return [false,'Nu s-a putut muta fișierul încărcat'];
    }
    // Validate actually an image
    if (!gh_valid_image($full)) {
        @file_put_contents(__DIR__.'/../upload_debug.log', date('c')." INVALID IMAGE $full (mime=$mime)\n", FILE_APPEND);
        @unlink($full); return [false,'Fișierul nu este o imagine validă']; }
    gh_optimize_image($full);
    // Thumbnail
    $thumbDir = rtrim($destDir,'/').'/thumbs';
    if (!is_dir($thumbDir)) @mkdir($thumbDir,0775,true);
    $thumbPath = $thumbDir.'/'.$name;
    gh_create_thumbnail($full, $thumbPath);
    @file_put_contents(__DIR__.'/../upload_debug.log', date('c')." OK $name\n", FILE_APPEND);
    return [true,$name];
}

// Get display image (original or placeholder); $baseDirRel is relative path from current script to gallery dir (e.g. '../assets/images/gallery/')
function gh_get_display_image($filename, $baseDirRel, $placeholderRel) {
    gh_ensure_placeholder($placeholderRel);
    if ($filename) {
        $full = rtrim($baseDirRel,'/').'/'.$filename;
        if (gh_valid_image($full)) return $full;
    }
    return $placeholderRel;
}

// Get thumbnail if exists, else original or placeholder
function gh_get_thumb_or_original($filename, $baseDirRel, $placeholderRel) {
    gh_ensure_placeholder($placeholderRel);
    if ($filename) {
        $base = rtrim($baseDirRel,'/');
        $thumb = $base.'/thumbs/'.$filename;
        $orig  = $base.'/'.$filename;
        if (gh_valid_image($thumb)) return $thumb;
        if (gh_valid_image($orig)) return $orig;
    }
    return $placeholderRel;
}

?>
