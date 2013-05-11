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

        case 'rename':
            $oldname = $_POST['file'];
            $newname = $_POST['folder'].'/'.$_POST['newname'];
            rename($oldname,$newname);
        break;
        case 'makedir':
            mkdir($_POST['folder'].'/'.$_POST['foldername']);
        break;
        case 'delete':
            try{ 

                if(is_dir($_POST['file'])){
                   function deleteDirectory($dir) { 
    if (!file_exists($dir)) return true; 
    if (!is_dir($dir) || is_link($dir)) return unlink($dir); 
        foreach (scandir($dir) as $item) { 
            if ($item == '.' || $item == '..') continue; 
            if (!deleteDirectory($dir . "/" . $item)) { 
                chmod($dir . "/" . $item, 0777); 
                if (!deleteDirectory($dir . "/" . $item)) return false; 
            }; 
        } 
        return rmdir($dir); 
    } 
                    deleteDirectory($_POST['file']);
                }else{
                    unlink($_POST['file']);
                }
                
            }catch(Exception $e){
                
            }
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
        <div class="fluid-row">
                    <form width="200" action="/index.php?folder=<?=$curent_folder?>" method="POST">
                    <input type="hidden" name="action" value="makedir">
                    <input type="hidden" name="folder" value="<?=$abs_path?>">
                    <input type="text" name="foldername" placeholder="Folder name">
                    <input type="submit" class="btn btn-success " value="new dir">
                    </form>
                    </div>
    </div>
    <table class="table">
    	<tbody>
    <?php
    foreach($files as $file){
         $actions = array(
                'rename'=> '
                    <div class="fluid-row">
                    <form width="200" action="/index.php?folder='.$curent_folder.'" method="POST">
                    <input type="hidden" name="action" value="rename">
                    <input type="hidden" name="file" value="'.$abs_path.'\\'.$file.'">
                    <input type="hidden" name="folder" value="'.$abs_path.'">
                    <input type="text" name="newname" value="'.$file.'">
                    <input type="submit" class="btn btn-success " value="rename">
                    </form>
                    </div>
                    ',
                'delete'=> '<div class="fluid-row">
                    <form width="200" action="/index.php?folder='.$curent_folder.'" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="file" value="'.$abs_path.'\\'.$file.'">
                    <input type="hidden" name="folder" value="'.$abs_path.'">
                    <input type="submit" class="btn btn-danger " value="delete">
                    </form>
                    </div>'
                );
    	?> 
    	<tr><td style="max-width: 60%;">
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
    	<a class="btn" style="max-width:400px;" href="index.php?folder=<?php echo urlencode($folder_url);?>"><i class="icon-hdd"></i> <?=$file?></a>
    	 </td><td>
         <?php  if($file !='<i class="icon-arrow-up"></i> up') echo implode('',$actions); ?>
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
            $addon = null;
                    $action = null;
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
    				$action = '
    				<form width="200" action="/index.php?folder='.$curent_folder.'" method="POST">
    				<input type="hidden" name="action" value="unzip">
    				<input type="hidden" name="file" value="'.$abs_path.'\\'.$file.'">
    				<input type="hidden" name="folder" value="'.$abs_path.'">
    				<input type="submit" class="btn btn-info " value="unzip">
    				</form>
                    ';
    			break;
    			default: 
    				$icon = 'icon-ban-circle';
    				$addon = null;
                    $action = null;
    			break;
    		}
           
            if($action!=null){
                $action.=implode(' ',$actions);
            }else{
                 $action=implode(' ',$actions);
            }
    		?>
    	<a class="btn btn-warning" href="<?php echo $url_path;?>"><i class="<?=$icon?>"></i><?=$file?> - <?=$file_type?> - DD</a>
    </td>
    <td>
    	<?php
            if($action != null) echo $action;

    		if($addon !=null){
                echo '</td></tr><tr colspan="2"><td>'.$addon;
            }else{
                echo ' ';
            }
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