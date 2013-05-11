<?php
/*
*
MediaWatch main logic
*/
header('Content-Type: text/html; charset=cp1251');
if(isset($_POST['action'])){
	switch($_POST['action']){
		case 'unzip':
		$filename = str_replace($_POST['folder'], '', $_POST['file']);
		preg_match_all('/.[^.]*$/', $filename, $ext);
		$filename = str_replace($ext[0][0],'',$filename);
		$cmd = '7z x "'.$_POST['file'].'" -o"'.$_POST['folder'].'/'.$filename.'" -y  ';
		$answer = shell_exec($cmd);
		break;
	}
}
 $root = $_SERVER['DOCUMENT_ROOT'];
 $curent_folder = '/';
 if(isset($_GET['folder'])){
   	$curent_folder = $_GET['folder'];
 }
 $abs_path = $root.'\\'.$curent_folder;
 $files_ = scandir($abs_path);
 $files = array();
 $files[]='..';
 foreach($files_ as $f){
 	if($f!='..' && $f!='.')$files[]=$f;
 }
 $levels = explode('\\', $curent_folder);
 if($levels[count($levels)-1]!='') $levels[]='';
 $render_path = implode('/',array_slice($levels,1,-1));



?>




<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="/css/bootstrap.css">

</head>
<body>

<div class="container text-center">
	<div class="masthead">
        <h3 class="muted">MediaWatch</h3>
        <h4> <?=$render_path?></h4>
    </div>
    <table class="table">
    	<tbody>
    <?php
    foreach($files as $file){
    	?> 
    	<tr><td width="100%">
    	<?php
    	$is_dir = is_dir($abs_path.'/'.$file);
    	
    	if($is_dir){
    		if($file==".."){
    			$root_level = array_slice($levels,1,-1);
    			$root_level = array_slice($root_level,0,-1);
    			//var_dump($root_level);
    		if(count($root_level)==1){
    			$root_level = array('',$root_level[0] ,'');
    		}

    			//var_dump($root_level);
    			$folder_url = implode('\\', $root_level);
    			$file = '<i class="icon-arrow-up"></i> up';
    		
    	}else{
    		$folder_url = $curent_folder.'\\'.$file;
    	}
    	if(substr($folder_url,0,1)!='\\')$folder_url = '\\'.$folder_url;
    		?>
    	<a class="btn" href="index.php?folder=<?php echo urlencode($folder_url);?>"><i class="icon-hdd"></i> <?=$file?></a>
    	<br>
    	<?php
    	}else{
    		$url_path = $curent_folder.'/'.$file;
    		$url_path = str_replace('\\','/', $url_path);
    		$url_path = preg_replace('/[\/]+/', '/', $url_path);

    		preg_match_all('/.[^.]*$/', $file, $ext);
    		if(count($ext[0])>0){
    			$ext = str_replace('.','',strtolower($ext[0][0]));
    		}else{
    			$ext = NULL;
    		}
    		$images = array('jpg', 'jpeg', 'png', 'gif');
    		$videos = array('avi', 'mpg', 'wmv');
    		$music = array('mp3','wav', 'ogg');
    		$archive = array('zip','rar','7z');
    		$file_type = 'other';
    		if(in_array($ext, $images)) $file_type = 'image';
    		if(in_array($ext, $videos)) $file_type = 'video';
    		if(in_array($ext, $music)) $file_type = 'music';
    		if(in_array($ext, $archive)) $file_type = 'archive';
    		switch($file_type){
    			case 'image': 
    				$icon = 'icon-camera"'; 
    				$addon = '<img src="'.$url_path.'" class="img-polaroid">';

    			break;
    			case 'video': $icon = 'icon-film"'; 
    				$addon = '<video src="'.$url_path.'" width="320" height="240" preload="none" controls></video>';
    			break;
    			case 'music': $icon = 'icon-music"'; break;
    			case 'archive': $icon = 'icon-briefcase';
    				$addon = '
    				<form width="200" action="/index.php?folder='.$curent_folder.'" method="POST">
    				<input type="hidden" name="action" value="unzip">
    				<input type="hidden" name="file" value="'.$abs_path.'\\'.$file.'">
    				<input type="hidden" name="folder" value="'.$abs_path.'">
    				<input type="submit" class="btn btn-info " value="unzip">
    				</form>


    				<?php';
    			break;
    			default: 
    				$icon = 'icon-ban-circle';
    				$addon = null;
    			break;
    		}

    		?>
    	<a class="btn btn-warning" href="<?php echo $url_path;?>"><i class="<?=$icon?>"></i><?=$file?> - <?=$file_type?></a>
    	<?php
    		if($addon !=null) echo '<br>'.$addon;
    	}
    	?> 
    </td></tr>
    	<?php
    }
    //var_dump($levels);
    ?>
</tbody>
</table>
</div>
</body>

</html>