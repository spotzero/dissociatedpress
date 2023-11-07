<?php
include "dissociated-press.php";
$corpus = "";
if (isset($_POST['corpus'])){
	$corpus = htmlspecialchars($_POST['corpus']);
	$dis = new dissociatedpress();
	$result = $dis->dissociate($corpus);
}



print "
<html>
<body>
";

if (isset($result)) {
	print "
Result:<br/>
<textarea name='corpus' rows='12' cols='128'>$result</textarea><br/>
"	;

}


print "
<form method='post' target='_self'>
Corpus:<br/>
<textarea name='corpus' rows='12' cols='128'>$corpus</textarea><br/>
<input type='submit'/>
</form>
</body>
</html>";

?>
