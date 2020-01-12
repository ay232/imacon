<?php
// Метод исправляет в JPEG файле ошибочный заголовок
//НЕВЕРНО: 	31 FF D8 FF
//ВЕРНО:	FF D8 FF
// JPEG с неверным заголовком не открывает браузер.

$start_dir = __DIR__."/images/";
//*************************************************************
//В этот массив включай папки с изображениями, он сам обработает все входящий в них подпапки и файлы

$array_or_directories = [
    __DIR__."/images/000000_099999/",
    __DIR__."/images/400000_449999/",
    __DIR__."/images/700000_749999/",
];

//print_r("<pre style='font-size: 16px; font-family: Arial; color: black;'>");
//
//print_r("</pre>");
set_time_limit(1000);
ini_set('memory_limit', '512M');
foreach ($array_or_directories as $current_directory) {
    logfile("***************************************************************************************************");
    logfile("ОБРАБОТКА НОВОЙ ДИРЕКТОРИИ: {$current_directory}");
    logfile("***************************************************************************************************");

    $files_to_convert = getAllFilenamesFromDir($current_directory);
    $start_time = microtime();
    $files_count = count($files_to_convert);

    logfile("Начало обработки {$files_count} файлов.");
    foreach ($files_to_convert as $file_to_convert) {

//        if (!patchJPEG($file_to_convert)) {
//            logfile("Файл '{$file_to_convert}' не был сконвертирован - произошла ошибка!");
//        } else {
//            logfile("Файл '{$file_to_convert}' был успешно сконвертирован.");
//        }
    }
    unset($files_to_convert);
    $end_time = microtime();
    $time_spend = ($end_time - $start_time)*1000000;
    $secs_per_file = $time_spend / $files_count;
    logfile("Обработка окончена. Затрачено времени всего {$time_spend}, по {$secs_per_file} на 1 файл.");
//patchJPEG($start_dir.'400000.jpg');
}
die('Готово!');




function getAllFilenamesFromDir($path){
    $dirs=scandir($path);
    $files_array = [];
    foreach ($dirs as $dir) {
        if (($dir==".") or ($dir==".."))
        {
            continue;
        }
        if ( is_dir($path.$dir) )
        {
            $new_array = getAllFilenamesFromDir($path.$dir."/");
            if (array_count_values($new_array)>0)
            {
                $files_array = array_merge($new_array, $files_array);
            }
        }else{
            $files_array[]=$path.$dir;
            if ($dir=="none") {
                continue;
            }
        }
    }
    return $files_array;
}

function patchJPEG($path)
{
    if (!isset($path) or !file_exists($path)) {
        return false;

    }
    $fn=basename($path);
    $file_data = file_get_contents($path);
    $first_symbol = substr($file_data,0,1);
    if (ord($first_symbol) == hexdec('FF'))
    {
        //Файл начинается корректно

        logfile("Файл '{$fn}' не требует исправления.");
        unset($file_data);
        return true;
    }
    if (ord($first_symbol) != hexdec('31'))
    {
        logfile("Файл '{$fn}' не может быть исправлен.");
        return false;
    }


    $file_data = substr($file_data, 1);
    unlink($path);
    file_put_contents($path,$file_data, FILE_APPEND);
    unset($file_data);
    logfile("Файл '{$fn}' был исправлен.",true);
    return true;
//    $file_data = substr_replace($file_data,char)


}

function logfile($text)
{
    $now = getdate();
    $logstring = "День: ".$now['yday'].". Время: ".$now['hours'].":".$now['minutes'].":".$now['seconds'].". ";
    $logstring.=$text."\n";

    file_put_contents(__DIR__."/log.txt", $logstring, FILE_APPEND);
    return true;
}

function logging($text)
{
    $now = getdate();
    print_r('<pre>');
    print_r($text."<br>");
    print_r('/<pre>');
    return true;
}