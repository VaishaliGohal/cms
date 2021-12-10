<?php
    session_start();

   /*destroying session*/
    session_destroy();
    
    header("Location: ../index.php");  
      
?>
