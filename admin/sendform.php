<html><body>
Welcome <?php echo $_POST["email"]; ?><br>
Your message is: <?php echo $_POST["subject"]; ?>
<?php

file_put_contents("email.txt", $_POST["email"]."\n\n".$_POST["subject"]);

 ?>
</body></html>
