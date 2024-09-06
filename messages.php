<?php // Example 11: messages.php
  require_once 'header.php';
  
  if (!$loggedin) die("</div></body></html>");

  if (isset($_GET['view'])) $view = $_GET['view'];
  else                      $view = $user;

  if (isset($_POST['text']))
  {
    // $text = sanitizeString($_POST['text']);
    $text = $_POST['text'];


    if ($text != "")
    {
      $pm   = substr($_POST['pm'],0,1);
      $time = time();
      queryMysql("INSERT INTO messages VALUES(NULL, '$user',
        '$view', '$pm', $time, '$text')");
    }
  }

  if ($view != "")
  {
    echo <<<_END
      <form method='post' action='messages.php?view=$view&r=$randstr'>
        <fieldset data-role="controlgroup" data-type="horizontal">
          <legend>Type here to leave a message</legend>
          <input type='hidden' name='pm' id='public' value='0' checked='checked'>
        </fieldset>
      <textarea name='text'></textarea>
      <input data-transition='slide' type='submit' value='Post Message'>
    </form><br>
_END;

    date_default_timezone_set('America/Chicago');

    if (isset($_GET['erase']))
    {
      $erase = sanitizeString($_GET['erase']);
      queryMysql("DELETE FROM messages WHERE id='$erase' AND recip='$user'");
    }
    
    // $query  = "SELECT * FROM messages WHERE recip='$view' ORDER BY time DESC";
    $query  = "SELECT * FROM messages ORDER BY time DESC";

    $result = queryMysql($query);
    $num    = $result->rowCount();

    while ($row = $result->fetch())
    {
      if ($row['pm'] == 0 || $row['auth'] == $user || $row['recip'] == $user)
      {
        echo date('M jS \'y g:ia:', $row['time']);
        echo "<b> " . $row['auth']. "</b> ";

        if ($row['pm'] == 0)
          echo "wrote: &quot;" . $row['message'] . "&quot; ";
        // Line below is not necessary anymore
        else
          echo "whispered: <span class='whisper'>&quot;" .
            $row['message']. "&quot;</span> ";

        if ($row['recip'] == $user)
          echo "[<a href='messages.php?view=$view" .
               "&erase=" . $row['id'] . "&r=$randstr'>erase</a>]";

        echo "<br>";
      }
    }
  }

  if (!$num)
    echo "<br><span class='info'>No messages yet</span><br><br>";

  echo "<br><a data-role='button'
        href='messages.php?view=$view&r=$randstr'>Refresh messages</a>";
?>

    </div><br>
  </body>
</html>
