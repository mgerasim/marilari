<?php
/**
* GIT PUSH SCRIPT
*
* Used for automatically deploying websites via github or bitbucket, more deets here:
*
* https://gist.github.com/1809044
*/
 
// The commands
$commands = array(
'mysqldump -u u0033011_default -pzaq12wsx u0033011_default > u0033011_default.out',
'git add .',
'git commit -m push',
'git push origin master',
'ls'
);
 
// Run the commands for output
$output = '';
foreach($commands AS $command){
// Run it
$tmp = shell_exec($command);
// Output
$output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
$output .= htmlentities(trim($tmp)) . "\n";
}
 
// Make it pretty for manual user access (and why not?)
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<title>GIT DUMP DB SCRIPT</title>
</head>
<body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
<pre>
. ____ . ____________________________
|/ \| | |
[| <span style="color: #FF0000;">&hearts; &hearts;</span> |] | Git Push Script v0.1 |
|___==___| / &copy; oodavid 2012 |
|____________________________|
 
<?php echo $output; ?>
</pre>
</body>
</html>