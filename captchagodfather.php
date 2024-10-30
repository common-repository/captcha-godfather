<?php
/*
Plugin Name: CAPTCHA-Godfather
Plugin URI: http://www.web-developers.net/blog/?page_id=33
Description: An anti-spam plug-in which does more than simple image verification.
Version: 1.3.2
Author: Jan Hvizdak
Author URI: http://services-seo.net/blog/
*/


/*  Copyright 2008  Jan Hvizdak  (email : postmaster@services-seo.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 $protection_cookie  = generate_session_id();

 $captcha_folder     = @str_replace('\\', '/', dirname(__FILE__));
 $captcha_thisfolder = @explode('/', $captcha_folder);
 $image_path         = "../wp-content/plugins/".end($captcha_thisfolder)."/";
 $image_path_live    = "/wp-content/plugins/".end($captcha_thisfolder)."/";

 $captcha_table      = $table_prefix . "captcha_god";
 $cg_settings_table  = $table_prefix . "captcha_god_settings";
 $wordsfil_table     = $table_prefix . "captcha_god_words";
 $sig_text           = "spam protection by web-developers.net";
 $antispammessage    = "Enter the following characters/numbers into the box below, please!";

 if(function_exists('add_action'))
  add_action('admin_menu', 'ca_add_pages');

 function ca_add_pages()
  {
   add_management_page('CAPTCHA-Godfather Plug-In Management', 'CAPTCHA-Godfather', 8, 'captchagodfather', 'captcha_godfather');
  }

 function captchatext($type,$limit1,$limit2)
  {
   $captcha = "";

   if($limit1==$limit2)
    {
     if($type==1)
      {
       for($i=1;$i<=$limit1;$i++)
        $captcha .= chr(rand(48,57));
      }
     if($type==2)
      {
       for($i=1;$i<=$limit1;$i++)
        $captcha .= chr(rand(97,122));
      }
     if($type==3)
      {
       for($i=1;$i<=$limit1;$i++)
        if(rand(1,2)==1)
         $captcha .= chr(rand(97,122));
          else
           $captcha .= chr(rand(48,57));
      }
    }
     else
      {
       if($type==1)
        {
         $r = rand($limit1,$limit2);
         for($i=1;$i<=$r;$i++)
          $captcha .= chr(rand(48,57));
        }
       if($type==2)
        {
         $r = rand($limit1,$limit2);
         for($i=1;$i<=$r;$i++)
          $captcha .= chr(rand(97,122));
        }
       if($type==3)
        {
         $r = rand($limit1,$limit2);
         for($i=1;$i<=$r;$i++)
          if(rand(1,2)==1)
           $captcha .= chr(rand(97,122));
            else
             $captcha .= chr(rand(48,57));
        }
      }

   return strtolower($captcha);
  }

 function captcha_godfather()
  {
   global $wpdb, $captcha_table, $cg_settings_table, $captcha_thisfolder, $image_path, $sig_text, $antispammessage, $wordsfil_table;

   install_captcha_godfather();

   if($_POST['update']=='1')
    {
     $table_name       = $cg_settings_table;

     $new_verification = intval($_POST['verification']);
     $sql = "UPDATE ".$table_name." set setting_value = '$new_verification' where setting_id = '1' limit 1;";
     $wpdb->query($sql);

     $new_verification_length = intval($_POST['verification_length']);
     $sql = "UPDATE ".$table_name." set setting_value = '$new_verification_length' where setting_id = '2' limit 1;";
     $wpdb->query($sql);

     $new_verification_length_fixed = intval($_POST['fixed_length']);
     if($new_verification_length_fixed!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$new_verification_length_fixed' where setting_id = '3' limit 1;";
       $wpdb->query($sql);
      }

     $new_verification_length_random_b = intval($_POST['random_from']);
     if($new_verification_length_random_b!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$new_verification_length_random_b' where setting_id = '4' limit 1;";
       $wpdb->query($sql);
      }

     $new_verification_length_random_e = intval($_POST['random_to']);
     if($new_verification_length_random_e!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$new_verification_length_random_e' where setting_id = '5' limit 1;";
       $wpdb->query($sql);
      }

     $new_seconds = intval($_POST['seconds']);
     if($new_seconds!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$new_seconds' where setting_id = '6' limit 1;";
       $wpdb->query($sql);
      }

     $new_font = htmlspecialchars(addslashes($_POST['font_type']));
     if($new_font!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$new_font' where setting_id = '7' limit 1;";
       $wpdb->query($sql);
      }

     $new_font_size = intval($_POST['captchasize']);
     if($new_font_size!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$new_font_size' where setting_id = '8' limit 1;";
       $wpdb->query($sql);
      }

     $new_font_colour = htmlspecialchars(addslashes($_POST['captchacolour']));
     if($new_font_colour!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$new_font_colour' where setting_id = '9' limit 1;";
       $wpdb->query($sql);
      }

     $new_font_bgcolour = htmlspecialchars(addslashes($_POST['captchabgcolour']));
     if($new_font_bgcolour!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$new_font_bgcolour' where setting_id = '10' limit 1;";
       $wpdb->query($sql);
      }

     $new_signature = htmlspecialchars(addslashes($_POST['signature']));
     if($new_signature!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$new_signature' where setting_id = '11' limit 1;";
       $wpdb->query($sql);
      }

     $new_pingtrack = intval($_POST['pingtrack']);
     if($new_pingtrack!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$new_pingtrack' where setting_id = '12' limit 1;";
       $wpdb->query($sql);
      }

     $showapproved = intval($_POST['showapproved']);
     if($new_pingtrack!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$showapproved' where setting_id = '14' limit 1;";
       $wpdb->query($sql);
      }

     $wordsfiltering = intval($_POST['wordsfiltering']);
     if($wordsfiltering!='')
      {
       $sql = "UPDATE ".$table_name." set setting_value = '$wordsfiltering' where setting_id = '15' limit 1;";
       $wpdb->query($sql);
      }

     $words_to_filter = htmlspecialchars(addslashes($_POST['words_filter']));
     if($words_to_filter!='')
      {
       $sql = "UPDATE ".$wordsfil_table." set f_words = '$words_to_filter';";
       $wpdb->query($sql);
      }
    }

   //read settings
   $t1                    = $cg_settings_table;
   $verification          = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '1';");
   $verification_length   = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '2';");

   $fixed_verification    = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '3';");

   $custom_verification_s = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '4';");
   $custom_verification_e = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '5';");

   $seconds               = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '6';");

   $captchafont           = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '7';");
   $captchafontsize       = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '8';");
   $captchacolour         = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '9';");
   $captchabgcolour       = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '10';");

   $signature             = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '11';");
   $pingtrack             = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '12';");
   $caught                = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '13';");
   $show_for_approved     = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '14';");
   $words_filtering       = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '15';");

   $words_in_filter       = $wpdb->get_var("SELECT f_words FROM $wordsfil_table limit 1;");

   if($verification_length==1)
    {
     $limit1 = 4;
     $limit2 = 10;
    }
   if($verification_length==2)
    {
     $limit1 = $fixed_verification;
     $limit2 = $fixed_verification;
    }
   if($verification_length==3)
    {
     $limit1 = $custom_verification_s;
     $limit2 = $custom_verification_e;
    }

   for($i=1;$i<=3;$i++)
    if($i==$verification)
     $checked[1][$i] = "checked = \"checked\"";
      else
       $checked[1][$i] = "";

   for($i=1;$i<=3;$i++)
    if($i==$verification_length)
     $checked[2][$i] = "checked = \"checked\"";
      else
       $checked[2][$i] = "";

   if($pingtrack==1)
    {
     $checked_ping_1 = "checked = \"checked\"";
     $checked_ping_2 = "";
    }
     else
      {
       $checked_ping_2 = "checked = \"checked\"";
       $checked_ping_1 = "";
      }

   if($signature==1)
    {
     $checked_sig_1 = "checked = \"checked\"";
     $checked_sig_2 = "";
    }
     else
      {
       $checked_sig_2 = "checked = \"checked\"";
       $checked_sig_1 = "";
      }

   if($show_for_approved==1)
    {
     $checked_sfa_1 = "checked = \"checked\"";
     $checked_sfa_2 = "";
    }
     else
      {
       $checked_sfa_2 = "checked = \"checked\"";
       $checked_sfa_1 = "";
      }

   if($words_filtering==1)
    {
     $checked_wff_1 = "checked = \"checked\"";
     $checked_wff_2 = "";
    }
     else
      {
       $checked_wff_2 = "checked = \"checked\"";
       $checked_wff_1 = "";
      }

   echo "<div class=\"wrap\">";
   echo "<h2>CAPTCHA-Godfather</h2>";
   echo "<font style='font-size: 10px;'>brought to you by <a href=\"http://www.web-developers.net/\">web-developers.net</a>;</font> <font style='font-size: 10px; color: green;'>[report problems <a href=\"http://services-seo.net/blog/?page_id=2\" target=\"_blank\">here</a>]</font><font style='font-size: 10px;'><br /><em>Since the version 1.3.0 the cookie based verification should work under every installation. However, it is recommended to activate this plug-in, then log out and try to post some comment on your own blog! If anything doesn't work, use the previously-mentioned links and spread a word about problems, please!</em></font><br /><br />";
   if($caught>1)
    echo "<em>caught ".$caught." spam comments</em><br />";
   echo "<form action=\"\" method=\"post\">
          <strong>Please, specify the type of verification below</strong><br />
          <input type=\"radio\" name=\"verification\" value=\"1\" ".$checked[1][1]." />Numbers only (recommended)<br />
          <input type=\"radio\" name=\"verification\" value=\"2\" ".$checked[1][2]." />Letters only<br />
          <input type=\"radio\" name=\"verification\" value=\"3\" ".$checked[1][3]." />Numbers and characters<br /><br />
          <strong>Specify the length of the verification string</strong><br />
          <input type=\"radio\" name=\"verification_length\" value=\"1\" ".$checked[2][1]." />Random (between 4 and 10 characters, recommended)<br />
          <input type=\"radio\" name=\"verification_length\" value=\"2\" ".$checked[2][2]." />Fixed: <input type=\"text\" value=\"".$fixed_verification."\" name=\"fixed_length\" /> characters<br />
          <input type=\"radio\" name=\"verification_length\" value=\"3\" ".$checked[2][3]." />Random: between <input type=\"text\" value=\"".$custom_verification_s."\" name=\"random_from\" /> and <input type=\"text\" value=\"".$custom_verification_e."\" name=\"random_to\" /> characters<br /><br />
          <strong>Visitors must spend at least</strong><br />
          <input type=\"text\" name=\"seconds\" value=\"".$seconds."\" /> seconds on a page before submitting their comments. If a spam bot tries to call the submission form, it will be ignored thanks to this restriction. Recommended value for this option is 30 seconds and more.<br /><br />
          <strong>Choose the font below</strong> (more fonts can be downloaded from <a href=\"http://www.webpagepublicity.com/free-fonts.html\" target=\"_blank\">webpagepublicity.com/free-fonts.html</a> although we are not related to that website in any way)<br />";

   $tx = $captcha_table;
   if($handle = opendir($image_path))
    {
     while (false !== ($file = readdir($handle)))
      {
       if ($file != "." && $file != "..")
        {
         if(strpos($file,".ttf")!==false)
          {
           if($captchafont==(str_replace(".ttf","",urlencode($file))))
            $checked = "checked = \"checked\"";
             else
              $checked = "";
           $last_id = $wpdb->get_var("SELECT id FROM $tx ORDER BY id ASC LIMIT 1;");
           $text_final = captchatext($verification,$limit1,$limit2);
           echo "<input type=\"radio\" name=\"font_type\" value=\"".str_replace(".ttf","",urlencode($file))."\" ".$checked." /><img src=\"".$image_path."captcha.php?font=".str_replace(".ttf","",urlencode($file))."&amp;new=".$last_id."&amp;s=".$captchafontsize."&amp;c=".$captchacolour."&amp;bg=".$captchabgcolour."\" alt=\"Sample verification\" /><br />";
          }
        }
      }
     closedir($handle);
    }

   echo "New fonts can be integrated easily into this plug-in. Simply download some free <strong>ttf</strong> font and upload it into this plug-in's folder. Names of files should be <strong>font1.ttf, font2.ttf, ... , font999, ... , fontN</strong> where N is always a number.<br /><br />
          <strong>Specify the font size for verification strings</strong><br />
          <input type=\"text\" name=\"captchasize\" value=\"".$captchafontsize."\" /><br /><br />
          <strong>Specify the colour below</strong> (one cool tool is <a href=\"http://houseof3d.com/pete/applets/tools/colors/\" target=\"_blank\">here</a>)<br />
          HEXADECIMAL value: #<input type=\"text\" name=\"captchacolour\" value=\"".$captchacolour."\" /><br /><br />
          <strong>Specify the background colour of text below</strong> (one cool tool is <a href=\"http://houseof3d.com/pete/applets/tools/colors/\" target=\"_blank\">here</a>)<br />
          HEXADECIMAL value: #<input type=\"text\" name=\"captchabgcolour\" value=\"".$captchabgcolour."\" /><br /><br />
          <strong>Additional options</strong><br />
          Put a small signature above the CAPTCHA image? It will contain <strong><em>".$sig_text."</em></strong> only. It will also point to <a href=\"http://www.web-developers.net/\" target=\"_blank\">web-developers.net</a> where we are creating plug-ins for <a href=\"http://wordpress.org\" target=\"_blank\">WordPress</a>. (<strong>recommended</strong>)<br />
          <input type=\"radio\" name=\"signature\" value=\"1\" ".$checked_sig_1." />Yes<br />
          <input type=\"radio\" name=\"signature\" value=\"0\" ".$checked_sig_2." />No<br /><br />
          Check trackbacks and pingbacks as ordinary comments? (<strong>strongly</strong> recommended)<br />
          <input type=\"radio\" name=\"pingtrack\" value=\"1\" ".$checked_ping_1." />No<br />
          <input type=\"radio\" name=\"pingtrack\" value=\"2\" ".$checked_ping_2." />Yes<br /><br />
          Show the verification code for users whose comments have been approved?<br />
          <input type=\"radio\" name=\"showapproved\" value=\"1\" ".$checked_sfa_1." />No<br />
          <input type=\"radio\" name=\"showapproved\" value=\"2\" ".$checked_sfa_2." />Yes<br /><br />
          Additionally you may activate the words-filter<br />
          <input type=\"radio\" name=\"wordsfiltering\" value=\"1\" ".$checked_wff_1." />No<br />
          <input type=\"radio\" name=\"wordsfiltering\" value=\"2\" ".$checked_wff_2." />Yes<br /><br />
          If the above-shown option is enabled, then you can define the words below. Divide them by an empty space. For example: \"viagra cialis porn tramadol phentermine\"<br />
          <textarea name=\"words_filter\" rows=\"5\" cols=\"50\">".$words_in_filter."</textarea><br />
          <input type=\"hidden\" name=\"update\" value=\"1\" />
          <input type=\"submit\" value=\"Update settings!\" />
         </form><br /><strong>Here below is a preview of a randomly generated verification code how it appears on your blog</strong>:<br />";
   $text_final = captchatext($verification,$limit1,$limit2);
   $tx         = $captcha_table;
   echo "<div class=\"storycontent\"><small>";
   if($signature==1)
    echo "<a href=\"http://www.web-developers.net/\" target=\"_blank\" style=\"text-decoration: none; border: 0px;\"><img src=\"".$image_path."captcha.php?font=".str_replace(".ttf","",urlencode($captchafont))."&amp;new=0&amp;s=10&amp;c=".$captchacolour."&amp;bg=".$captchabgcolour."\" alt=\"anti-spam developed by web-developers.net; the UK web design company\" style=\"border: 0px; padding: 2px; margin: 0px;\" /></a><br />";
   $last_id = $wpdb->get_var("SELECT id FROM $tx ORDER BY id ASC LIMIT 1;");
   echo $antispammessage."<br /><img src=\"".$image_path."captcha.php?font=".str_replace(".ttf","",urlencode($captchafont))."&amp;new=".$last_id."&amp;s=".$captchafontsize."&amp;c=".$captchacolour."&amp;bg=".$captchabgcolour."\" alt=\"Sample verification\" style=\"padding: 2px; margin: 0px;\"/>";
   echo "<br /><input type=\"text\" name=\"captcha\" value=\"\" />";
   echo "</small></div>";
   echo "</div>";
  }

 function generate_session_id()
  {
   $cookie_value = md5(rand(1,1000));
   if(!isset($_COOKIE['captchaprotection']))
    setcookie("captchaprotection" , $cookie_value , time()+3600*24 , str_replace($_REQUEST['QUERY_STRING'],"",$_REQUEST['REQUEST_URI']));
     else
      $cookie_value = $_COOKIE['captchaprotection'];
   $this_cookie = $cookie_value;
   return $this_cookie;
  }

 function clear_data()
  {
   global $wpdb, $captcha_table;

   $t1 = $captcha_table;
   $t  = time();
   $o_t= $t - 3600*24*5;

   $sql = "DELETE FROM ".$t1." WHERE ( (timestamp < '$o_t' ) AND ( id > '1' ) );";
   $wpdb->query($sql);
  }

 function generate_image()
  {
   clear_data();
   global $wpdb, $captcha_table, $cg_settings_table, $captcha_thisfolder, $image_path_live, $sig_text, $antispammessage, $protection_cookie, $user_ID, $current_site;

   $t1                      = $cg_settings_table;
   $show_for_approved       = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '14';");
   $my_all_time             = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '6';");

   if( ( (isset($user_ID)) && ($show_for_approved=='2') ) || (!isset($user_ID) ) )

    {

     //read settings
     $t1                    = $cg_settings_table;
     $verification          = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '1';");
     $verification_length   = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '2';");

     $fixed_verification    = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '3';");

     $custom_verification_s = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '4';");
     $custom_verification_e = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '5';");

     $seconds               = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '6';");

     $captchafont           = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '7';");
     $captchafontsize       = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '8';");
     $captchacolour         = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '9';");
     $captchabgcolour       = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '10';");

     $signature             = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '11';");

     if($verification_length==1)
      {
       $limit1 = 4;
       $limit2 = 10;
      }
     if($verification_length==2)
      {
       $limit1 = $fixed_verification;
       $limit2 = $fixed_verification;
      }
     if($verification_length==3)
      {
       $limit1 = $custom_verification_s;
       $limit2 = $custom_verification_e;
      }

     $text_final = captchatext($verification,$limit1,$limit2);

     $session_id = $protection_cookie;
     $stamp      = time();
     $ip         = $_SERVER['REMOTE_ADDR'];

     $t1 = $captcha_table;

     $sql     = "INSERT INTO ".$t1." values ( 'NULL' , '$session_id' , '$text_final' , '$stamp' , '$ip' );";
     $wpdb->query($sql);
     $last_id = $wpdb->get_var("SELECT id FROM $t1 ORDER BY id DESC LIMIT 1;");

     $echo_spam = "<div class=\"storycontent\"><small>";
     $dir   = explode("/",dirname(__FILE__));
     $count = count($dir);
     $image_path_live = get_option('siteurl')."/".$dir[$count-3]."/".$dir[$count-2]."/".$dir[$count-1]."/";
     if($signature==1)
      $echo_spam .= "<a href=\"http://www.web-developers.net/\" target=\"_blank\" style=\"text-decoration: none; border: 0px;\"><img src=\"".$image_path_live."captcha.php?font=".str_replace(".ttf","",urlencode($captchafont))."&amp;new=0&amp;s=10&amp;c=".$captchacolour."&amp;bg=".$captchabgcolour."\" alt=\"anti-spam developed by web-developers.net; the UK web design company\" style=\"border: 0px; padding: 2px; margin: 0px;\" /></a><br />";
     $echo_spam .= $antispammessage."<br /><a href=\"#\" onclick=\'window.open(".chr(34)."".$image_path_live."captcha.php?font=".str_replace(".ttf","",urlencode($captchafont))."&amp;new=".$last_id."&amp;s=80&amp;c=000000&amp;bg=FFFFFF".chr(34).",".chr(34)."mywindow".chr(34).",".chr(34)."width=600,height=200".chr(34).")\'><img src=\"".$image_path_live."captcha.php?font=".str_replace(".ttf","",urlencode($captchafont))."&amp;new=".$last_id."&amp;s=".$captchafontsize."&amp;c=".$captchacolour."&amp;bg=".$captchabgcolour."\" alt=\"CAPTCHA verification\" style=\"padding: 2px; margin: 0px;\"/></a><br />click on the image to open it in a new window with white background, black text colour and large font size";
     $echo_spam .= "<br /><input type=\"text\" name=\"captcha\" value=\"\" /><p id=\"d3\">a</p></small></div>";

     //echo "<p id=\"d3\"></p>";

     echo "<script type=\"text/javascript\">
           <!--
            var oldHTML = document.getElementById('commentform').innerHTML;
            document.getElementById('commentform').innerHTML = '".$echo_spam."' + oldHTML;

            var i = -1;
            function display()
             {
              i += 1;
              if(i<".$my_all_time.")
               {
                document.getElementById('d3').innerHTML = ".$my_all_time." - i + ' second(s) remaining until your comment becomes valid (spam protection).';
                setTimeout('display()',1000);
               }
                else
                 {
                  document.getElementById('d3').innerHTML = 'Now you can submit the comment :) .';
                 }
             }
            display();
           -->
           </script>";
   }
  }

 function verification_test($incoming_comment)
  {
   global $wpdb, $captcha_table, $protection_cookie, $cg_settings_table, $user_ID, $wordsfil_table;

   $t1                      = $cg_settings_table;
   $show_for_approved       = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '14';");
   $words_in_filter         = $wpdb->get_var("SELECT f_words FROM $wordsfil_table limit 1;");
   $filtering_words         = $wpdb->get_var("SELECT setting_value FROM $t1 WHERE setting_id = '15';");

   if( ( (isset($user_ID)) && ($show_for_approved=='2') ) || (!isset($user_ID) ) )

    {

     $my_ip      = $_SERVER['REMOTE_ADDR'];
     $my_cookie  = $protection_cookie;
     $my_time    = time();
     $my_captcha = $_POST['captcha'];

     $find_it    = "";

     $table_name = $cg_settings_table;
     $my_all_time= $my_time - $wpdb->get_var("SELECT setting_value FROM $table_name WHERE setting_id = '6';");
     $pings      = $wpdb->get_var("SELECT setting_value FROM $table_name WHERE setting_id = '12';");

     $spam_body = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
                   <html xmlns=\"http://www.w3.org/1999/xhtml\" dir=\"ltr\">
                    <head>
                     <title>Antispam protection</title>
                     <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
                     <meta name=\"description\" content=\"A page with submitted inputs.\" />
                    </head>
                    <body>
                     <h1>Your message didn't pass the spam protection!</h1>
                     Make sure that:
                     <ul>
                      <li>You entered the verification code properly</li>
                      <li>Your browser allows cookies</li>
                      <li>Your IP address didn't change while writing the comment</li>
                      <li>You spent more than ".($wpdb->get_var("SELECT setting_value FROM $table_name WHERE setting_id = '6';"))." seconds on the page before you submitted the comment form</li>
                     </ul>
                     Feel free to return back and re-submit the form properly. In case that your browser refreshes the previous page after clicking on the back button, here is what you submitted:
                     <textarea cols=\"100\" rows=\"20\">".htmlspecialchars(stripslashes($incoming_comment['comment_content']))."</textarea>
                    </body>
                    </html>";

     $table_name = $captcha_table;

     $find_it = $wpdb->get_var("SELECT id FROM $table_name WHERE ( ( session_id = '$my_cookie' ) AND ( captcha = '$my_captcha' ) AND ( address = '$my_ip' ) AND ( timestamp <= '$my_all_time' ) );");

     if( ( ($incoming_comment['comment_type']=='pingback') || ($incoming_comment['comment_type']=='trackback') ) && ($pings=='1') )
      $find_it = "something";

     if($find_it!='')
      {
       $present = 0;
       $filters = explode(" ",$words_in_filter);
       if($filtering_words=='2')
        {
         for($i=0;$i<count($filters);$i++)
          if( (@strpos($incoming_comment['comment_content'],$filters[$i]) ) !==false )
           {
            $present  = 1;
            $bad_word = $filters[$i];
            $find_it  = "";
            break;
           }
        }
       if($present==0)
        $find_it = "something";
      }

     if($find_it=='')
      {
       $table_name = $cg_settings_table;
       $sql        = "UPDATE ".$table_name." set setting_value = setting_value + 1 where setting_id = '13' limit 1;";
       $wpdb->query($sql);
       if($present==1)
        {
         $spam_body = str_replace("<ul>","<ul><li>You used the <strong>".$bad_word."</strong> word which is not allowed</li>",$spam_body);
        }
       die ($spam_body);
      }
    }
   return $incoming_comment;
  }

 function install_captcha_godfather()
  {
   global $wpdb, $captcha_table, $cg_settings_table, $wordsfil_table;

   $table_name = $captcha_table;

   if($wpdb->get_var("show tables like '$table_name'") != $table_name)
    {
     $sql = "CREATE TABLE ".$table_name." (
               id int(11) NOT NULL auto_increment,
               session_id varchar(250) NOT NULL,
               captcha varchar(250) NOT NULL,
               timestamp int(11) NOT NULL,
               address varchar(100) NOT NULL,
               PRIMARY KEY (id),
               KEY session_id (session_id),
               KEY captcha (captcha),
               KEY timestamp (timestamp),
               KEY address (address)
              );";
      $wpdb->query($sql);

      $r1  = md5(rand(1,1000));
      $r2  = time();
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '$r1' , 'test0123456789' , '$r2' , '100.100.100.100' );";
      $wpdb->query($sql);
    }

   $table_name = $wordsfil_table;

   if($wpdb->get_var("show tables like '$table_name'") != $table_name)
    {
     $sql = "CREATE TABLE ".$table_name." (
             f_words TEXT NOT NULL ,
             FULLTEXT (
              f_words
             )
            );";
      $wpdb->query($sql);

      $sql = "INSERT INTO ".$table_name." values ( 'viagra cialis porn tramadol phentermine' );";
      $wpdb->query($sql);
    }

   $table_name = $cg_settings_table;

   if($wpdb->get_var("show tables like '$table_name'") != $table_name)
    {
     $sql = "CREATE TABLE ".$table_name." (
               id int(11) NOT NULL auto_increment,
               setting_id int(11) NOT NULL,
               setting_value varchar(250) NOT NULL,
               PRIMARY KEY  (id),
               KEY setting_id (setting_id),
               KEY setting_value (setting_value)
              );";
      $wpdb->query($sql);

      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '1' , '1' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '2' , '1' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '3' , '4' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '4' , '5' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '5' , '12' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '6' , '15' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '7' , 'font1' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '8' , '20' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '9' , '000000' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '10' , 'FFFFFF' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '11' , '0' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '12' , '2' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '13' , '0' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '14' , '1' );";
      $wpdb->query($sql);
      $sql = "INSERT INTO ".$table_name." values ( 'NULL' , '15' , '1' );";
      $wpdb->query($sql);

      echo "<script type=\"text/javascript\" language=\"javascript\">
            <!--
             alert (\"This plugin requires the phpGD library! If you cannot see any verification image, then phpGD is missing.\");
            -->
            </script>";
    }
  }

 function uninstall_captcha_godfather()
  {
   global $wpdb, $captcha_table, $cg_settings_table, $wordsfil_table;

   $j     = 1;
   $t[$j] = $captcha_table;     $j++;
   $t[$j] = $cg_settings_table; $j++;
   $t[$j] = $wordsfil_table;    $j++;

   for($i=1;$i<$j;$i++)
    {
     if($wpdb->get_var("SHOW TABLES LIKE '$t[$i]'") == $t[$i])
      {
       $sql = "DROP TABLE " . $t[$i];
       $wpdb->query($sql);
      }
    }
  }

 if(function_exists('add_filter'))
  {
   install_captcha_godfather();
   add_action('comment_form', 'generate_image');
   add_filter('preprocess_comment', 'verification_test');
  }
?>