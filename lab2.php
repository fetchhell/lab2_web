<html>
<style>
   p {
    border: 1px solid red;
    padding: 10px;
   }
</style>
<body>

        
<?php

/*-------------------------- Insert messages ----------------------------------- */
function insert_message($textbox, $fk_theme_id, $message_id, $start_message)
{
      $query_max  = "SELECT MAX(message_id) + 1 as max_element FROM messages";
      $result_max = mysql_query($query_max) or die ("Не удалось найти максимальный элемент !". mysql_error());
      $line_max   = mysql_fetch_array($result_max, MYSQL_ASSOC);  
  
      {
          $query_add = "INSERT INTO messages(message_id, message_box, deleted, fk_theme_id, start_message) 
                        VALUES ('".$line_max['max_element']."',
                        '".$textbox."', 0,
                        '".$fk_theme_id."','".$start_message."')";

          mysql_query($query_add) or die ("Не удалось выполнить запрос !". mysql_error());
      }
      if($start_message !== 1)
      {
          $query_max = "SELECT MAX(id) + 1 as max_element FROM next_messages";
          $result_max_next = mysql_query($query_max) or die ("Не удалось найти максимальный элемент !". mysql_error());
          $line_max_next   = mysql_fetch_array($result_max_next, MYSQL_ASSOC); 
 
          $query_add = "INSERT INTO next_messages(id, next_id, fk_message_id) VALUES ('".$line_max_next['max_element']."','".$line_max['max_element']."','".$message_id."')";
          mysql_query($query_add) or die ("Не удалось выполнить запрос !".mysql_error());
      } 
} 

/*-------------------------- Update messages ----------------------------------- */
function update_message($textbox, $message_id)
{  
   {
      $query_update = "UPDATE messages SET message_box = '".$textbox."' WHERE message_id = '".$message_id."'";
      mysql_query($query_update) or die ("Не удалось выполнить запрос на обновление !". mysql_error());
   }
}

/*-------------------------- Delete messages ----------------------------------- */
function delete_message($message_id, $level)
{
   $query = "SELECT next_id FROM next_messages INNER JOIN messages ON fk_message_id = message_id and message_id = '".$message_id."'";
   $result= mysql_query($query) or die ("Запрос не выполнен: " . mysql_error());

   while($next_messages =  mysql_fetch_array($result, MYSQL_ASSOC))
   {
     delete_message($next_messages['next_id'], $level + 1);
   }
   
   $query_verify  = "SELECT deleted FROM messages WHERE message_id = '".$message_id."'";
   $result_verify = mysql_query($query_verify) or die ("Не удалось выполнить запрос !".mysql_error());
   $line_verify   = mysql_fetch_array($result_verify, MYSQL_ASSOC);

   if($line_verify['deleted'] !== 1)
   {
     $query_delete = "UPDATE messages SET deleted = 1 WHERE message_id = '".$message_id."'";
     mysql_query($query_delete) or die ("Не удалось выполнить запрос !".mysql_error());
   }
   else
      echo "Сообщение уже было удалено !";   
} 

/*-------------------------- Insert tags --------------------------------------- */
function insert_tag($message_id, $textbox)
{
   $query_max  = "SELECT MAX(tag_id) + 1 as max_element FROM tag";
   $result_max = mysql_query($query_max) or die ("Не удалось найти максимальный элемент !". mysql_error());
   $line_max   = mysql_fetch_array($result_max, MYSQL_ASSOC); 

   $max_element = 1;
   if($line_max['max_element'] !== NULL)
   { 
      $max_element = $line_max['max_element'];
   }   

   $query_verify  = "SELECT tag_name, tag.deleted FROM tag INNER JOIN messages WHERE tag_name = '".$textbox."' AND fk_message_id = message_id  AND  fk_message_id = '".$message_id."'";
   $result_verify = mysql_query($query_verify) or die ("Не удалось выполнить запрос !". mysql_error());

   $exist = 0;
   while($line_verify = mysql_fetch_array($result_verify, MYSQL_ASSOC)){

       if(!empty($line_verify['tag_name']) && $line_verify['deleted'] == 0) { echo "Тег с именем '".$line_verify['tag_name']."' уже существует !"; $exist = 1; break; }
   }

   if($exist == 0){ 
         $query_add = "INSERT INTO tag (tag_id, tag_name, fk_message_id, deleted) VALUES ('".$max_element."','".$textbox."','".$message_id."', 0)";
         mysql_query($query_add) or die ("Не удалось выполнить запрос !". mysql_error());
   }
}

/*-------------------------- Update tags --------------------------------------- */
function  update_tag($tag_id, $textbox)
{
   // TODO : проверить, есть ли тег с таким же названием в БД 

   $query_fk  = "SELECT fk_message_id FROM tag WHERE tag_id = '".$tag_id."'";
   $result_fk = mysql_query($query_fk) or die ("Не удалось выполнить запрос!".mysql_error());
   $list_fk   = mysql_fetch_array($result_fk, MYSQL_ASSOC);

   $query_verify  = "SELECT * FROM tag WHERE tag_name = '".$textbox."' AND fk_message_id = '".$list_fk['fk_message_id']."' AND deleted = 0";
   $result_verify = mysql_query($query_verify) or die ("Не удалось выполнить запрос!".mysql_error());
   $list_verify   = mysql_fetch_array($result_verify, MYSQL_ASSOC);     

   if(empty($list_verify))
   {
      $query_update = "UPDATE tag SET tag_name = '".$textbox."' WHERE tag_id = '".$tag_id."'";
      mysql_query($query_update) or die ("Не удалось выполнить запрос !". mysql_error());
   }
   else
     echo "Тег с именем '".$textbox."' уже существует !"; 
}

/*-------------------------- Delete tags --------------------------------------- */
function  delete_tag($tag_id)
{
   $query_verify  = "SELECT deleted FROM tag WHERE tag_id = '".$tag_id."'";
   $result_verify = mysql_query($query_verify) or die ("Не удалось выполнить запрос !".mysql_error());
   $line_verify   = mysql_fetch_array($result_verify, MYSQL_ASSOC);

   if($line_verify['deleted'] !== 1)
   {
     $query_delete = "UPDATE tag SET tag_name = \"\", deleted = 1 WHERE tag_id = '".$tag_id."'";
     mysql_query($query_delete) or die ("Не удалось выполнить запрос !".mysql_error());
   }
    else
      echo "Тег уже был удален !";
}


function change_tags($line_messages, $level)
{
    $spaces = ""; $name = "tag".$line_messages['message_id'];
    $update_name = "update".$name;
    $delete_name = "delete".$name;    

    $var = "";
    $start = "<form method=\"post\"> 
            Tags: 
            <input type=\"submit\" name=\"".$name."\" value=\"add\"/> 
            <input type=\"submit\" name=\"".$update_name."\" value=\"update\"/>
            <input type=\"submit\" name=\"".$delete_name."\" value=\"del\"/>";
 
 
    for($i = 0; $i < $level; $i++) $spaces .= "&nbsp\t";

    $var .= $spaces;                     
    
    $textbox = $name."message";
    for($i = 0; $i < 3; $i++) $spaces .= $spaces;  
    $finish = $spaces."<input type = \"text\" value =\"\" name = \"".$textbox."\">
                     </form>";

    /* insert tag */
    if(isset($_POST[$name]))
    {
      if(!empty($_POST[$textbox]))
      { 
         insert_tag($line_messages['message_id'], $_POST[$textbox]);                            
      }
      else
        echo "Пустой запрос!"; 

       $_POST[$name]  = NULL;
    }
    
    /* update tag */
    if(isset($_POST[$update_name]))
    {
      if(!empty($_POST[$textbox]))
      {
         $pos = strpos($_POST[$textbox], "/");

         if($pos !== false)
         {
            list($old, $new) = split('/' , $_POST[$textbox]); 
            $query = "SELECT tag_id FROM tag WHERE tag_name = '".$old."' AND fk_message_id = '".$line_messages['message_id']."'"; 
            $result = mysql_query($query) or die ("Не удалось выполнить запрос !:".mysql_query());
            $list = mysql_fetch_array($result, MYSQL_ASSOC);
          
            if(!empty($list)) update_tag($list['tag_id'], $new); 
            else echo "Тега с именем '".$old."' не существует !";
         }
         else echo "Запрос без '/'. Пример old_tag/new_tag .";                            
      }
      else
        echo "Пустой запрос!"; 
  
       $_POST[$name]  = NULL;
    }
   
   
    /* delete tag */
    if(isset($_POST[$delete_name]))
    {
       if(!empty($_POST[$textbox]))
      {
         $query = "SELECT tag_id FROM tag WHERE tag_name = '".$_POST[$textbox]."' AND deleted = 0 AND fk_message_id = '".$line_messages['message_id']."'"; 
         $result = mysql_query($query) or die ("Не удалось выполнить запрос !:".mysql_query()); 
         
         $list = mysql_fetch_array($result, MYSQL_ASSOC);
         if(!empty($list)) delete_tag($list['tag_id']);
         else echo "Тега с именем '".$_POST[$textbox]."' не существует !"; 
      }
      else 
        echo "Пустой запрос !";
    }

    
   /* Print tags for current message_id */
   {
       $query  = "SELECT tag_name FROM tag INNER JOIN messages WHERE fk_message_id = message_id AND message_id = '".$line_messages['message_id']."' AND tag.deleted = 0";
       $result = mysql_query($query); 
        
       while($array = mysql_fetch_array($result, MYSQL_ASSOC)){
             if(!empty($array['tag_name'])) $var.= "#".$array['tag_name'].";";
       }
       $var .= "<br/>\r\n"; 
   }

     /* Если сообщение удалено тег не нужно выводить */
    $query_verify  = "SELECT deleted FROM messages WHERE message_id = '".$line_messages['message_id']."'";
    $result_verify = mysql_query($query_verify) or die ("Не удалось выполнить запрос !:".mysql_error());
    $list_verify   = mysql_fetch_array($result_verify, MYSQL_ASSOC);    

    if($list_verify['deleted'] == 0) echo $start.$var.$finish;
    else
    {
       // TODO : если сообщение удалено, теги тоже надо помечать удаленными
       $query  = "SELECT tag_id FROM tag INNER JOIN messages WHERE fk_message_id = message_id AND message_id = '".$line_messages['message_id']."' AND tag.deleted = 0";
       $result = mysql_query($query);
   
       while($array = mysql_fetch_array($result, MYSQL_ASSOC)){
           delete_tag($array['tag_id']);
       }
    }
}


function change_messages($line_messages, $level)
{
    $spaces = ""; $name = "message".$line_messages['message_id'];
    $update_name = "update".$name;
    $delete_name = "delete".$name;    

    $var = "";
    $start = "<form method=\"post\"> 
            <input type=\"submit\" name=\"".$name."\" value=\"add\"/> 
            <input type=\"submit\" name=\"".$update_name."\" value=\"update\"/>
            <input type=\"submit\" name=\"".$delete_name."\" value=\"del\"/>";
 
    for($i = 0; $i < $level; $i++) $spaces .= "&nbsp\t";

    $var .= $spaces;                     
    
    $textbox = $name."message";
    for($i = 0; $i < 3; $i++) $spaces .= $spaces;  
    $finish = $spaces."<input type = \"text\" value =\"\" name = \"".$textbox."\">
                     </form>";    
        
    if(isset($_POST[$name]))
    {
      if(!empty($_POST[$textbox]))
      {
         insert_message($_POST[$textbox], $line_messages['fk_theme_id'], $line_messages['message_id'], 0);                    
      }
      else
        echo "Пустой запрос!"; 

       $_POST[$name]  = NULL;
    }
    
    if(isset($_POST[$update_name]))
    {
      if(!empty($_POST[$textbox]))
      {
         update_message($_POST[$textbox], $line_messages['message_id']); 
           
         $query = "SELECT message_box FROM messages WHERE message_id = '".$line_messages['message_id']."'";
         $result = mysql_query($query);

         $array = mysql_fetch_array($result, MYSQL_ASSOC); 
         $var.= $array['message_box']."<br/>\r\n";           
      }
      else 
      {
        echo "Пустой запрос !";
        $var.= $line_messages['message_box']."<br/>\r\n"; 
      }

       $_POST[$name]  = NULL;
    }
    else
      $var.= $line_messages['message_box']."<br/>\r\n"; 

    if(isset($_POST[$delete_name]))
    {
        // TODO : delete
        delete_message($line_messages['message_id'], $level);  
    } 

    /* Если сообщение удалено его не нужно выводить */
    $query_verify  = "SELECT deleted FROM messages WHERE message_id = '".$line_messages['message_id']."'";
    $result_verify = mysql_query($query_verify) or die ("Не удалось выполнить запрос !:".mysql_error());
    $list_verify   = mysql_fetch_array($result_verify, MYSQL_ASSOC);

    if($list_verify['deleted'] == 0) echo $start.$var.$finish;
}

function insert_start_messages($line_messages, $level)
{
    $spaces = ""; $name = "theme".$line_messages['theme_id'];

    $var = "<form method=\"post\"> <input type=\"submit\" name=\"".$name."\" value=\"add\"/>"; 
    for($i = 0; $i < $level; $i++) $spaces .= "&nbsp\t";

    $var .= $spaces;           
    $var.= $line_messages['theme_name']."<br/>\r\n";             
    
    $textbox = $name."message";
    for($i = 0; $i < 2; $i++) $spaces .= $spaces;  
    $var .= $spaces."<input type = \"text\" value =\"\" name = \"".$textbox."\">
                     </form>";    
   echo $var;
        
    if(isset($_POST[$name]))
    {
      if(!empty($_POST[$textbox]))
      {
         insert_message($_POST[$textbox], $line_messages['theme_id'], 0, 1);                       
      }
      else
        echo "Пустой запрос!"; 

       $_POST[$name]  = NULL;
    }
}

function show_messages($input, $level)
{
    while($line_messages = mysql_fetch_array($input, MYSQL_ASSOC)){
             change_messages($line_messages, $level);
             change_tags    ($line_messages, $level);
 
             $query = "SELECT next_id FROM next_messages INNER JOIN messages ON fk_message_id = message_id and message_id = '".$line_messages['message_id']."'";
             $result= mysql_query($query) or die ("Запрос не выполнен: " . mysql_error());

             while($next_messages =  mysql_fetch_array($result, MYSQL_ASSOC))
             {
                $query_in   = "SELECT message_id, message_box, fk_theme_id from messages INNER JOIN next_messages WHERE message_id = next_id AND next_id = '".$next_messages['next_id']."'";  
                $result_in  = mysql_query($query_in) or die ("Запрос не выполнен query_in: ".mysql_error());
                  
                show_messages($result_in, $level + 1);   
             }            
          }	                 
}

$con = mysql_connect("127.0.0.1:3306","root","") or die ("Не удалось соединиться с БД: ".mysql_error());
mysql_select_db("blog") or die("Не удалось выбрать базу данных");

/* select themes */
$query_themes  = "SELECT * FROM themes";
$result_themes =  mysql_query($query_themes) or die ("Запрос не удался: " . mysql_error());

while ($line_themes = mysql_fetch_array($result_themes, MYSQL_ASSOC)) {
    echo "<p>".$line_themes['theme_name']."</p>";

    insert_start_messages($line_themes, 1);

    $query_messages = "SELECT message_id, message_box, fk_theme_id FROM messages WHERE fk_theme_id = '".$line_themes['theme_id']."' AND start_message = 1";
    $result_messages= mysql_query($query_messages) or die ("Запрос не удался: " . mysql_error());

    show_messages($result_messages, 1);
    mysql_free_result($result_messages);
}


mysql_free_result($result_themes);
mysql_close($con);
?>

</body>
</html>
