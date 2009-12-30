<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

defined( '_JEXEC' ) or die('Restricted access');

$kunena_config =& CKunenaConfig::getInstance();
//Some initial thingies needed anyway:
if (!isset($htmlText)) $htmlText = '';
if (!isset($setFocus)) $setFocus = 0;
if (!isset($no_image_upload)) $no_image_upload = 0;
if (!isset($no_file_upload)) $no_file_upload = 0;
$authorName = stripslashes($authorName);

CKunenaPolls::call_javascript_form();

include_once(KUNENA_PATH_LIB .DS. 'kunena.bbcode.js.php');

//keep session alive while editing
JHTML::_('behavior.keepalive');
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable<?php echo $objCatInfo->class_sfx; ?>" id="fb_postmessage"  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th colspan = "2">
                <div class = "fb_title_cover fbm">
                    <span class = "fb_title fbl"> <?php echo _POST_MESSAGE; ?>"<?php echo kunena_htmlspecialchars(stripslashes($objCatInfo->name)); ?>"</span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody id = "fb_post_message">
        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
            <td class = "fb_leftcolumn">
                <strong><?php echo _GEN_NAME; ?></strong>:
            </td>

            <?php
            if (($kunena_config->regonly == "1" || $kunena_config->changename == '0') && $kunena_my->id != "" && !$kunena_is_moderator) {
                echo "<td><input type=\"hidden\" name=\"fb_authorname\" size=\"35\" class=\"" . $boardclass . "inputbox postinput\"  maxlength=\"35\" value=\"$authorName\" /><b>$authorName</b></td>";
            }
            else
            {
                if ($registeredUser == 1) {
                    echo "<td><input type=\"text\" name=\"fb_authorname\" size=\"35\"  class=\"" . $boardclass . "inputbox postinput\"  maxlength=\"35\" value=\"$authorName\" /></td>";
                }
                else
                {
                    echo "<td><input type=\"text\" name=\"fb_authorname\" size=\"35\"  class=\"" . $boardclass . "inputbox postinput\"  maxlength=\"35\" value=\"\" />";
                    echo "<script type=\"text/javascript\">document.postform.fb_authorname.focus();</script></td>";
                    $setFocus = 1;
                }
            }
            ?>
        </tr>

        <?php
        if ($kunena_config->askemail)
        {
            echo '<tr class = "'. $boardclass . 'sectiontableentry2"><td class = "fb_leftcolumn"><strong>' . _GEN_EMAIL . ' *</strong>:</td>';
            if (($kunena_config->regonly == "1" || $kunena_config->changename == '0') && $kunena_my->id != "" && !$kunena_is_moderator) {
                echo "<td>$my_email</td>";
            }
            else
            {
                echo "<td><input type=\"text\" name=\"email\"  size=\"35\" class=\"" . $boardclass . "inputbox postinput\" maxlength=\"35\" value=\"$my_email\" /></td>";
            }
            echo '</tr>';
        }
        ?>

        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
            <?php
            if (!$fromBot)
            {
            ?>

                <td class = "fb_leftcolumn">
                    <strong><?php echo _GEN_SUBJECT; ?></strong>:
                </td>

                <td>
                    <input type = "text" class = "<?php echo $boardclass; ?>inputbox postinput" name = "subject" size = "35" maxlength = "<?php echo $kunena_config->maxsubject;?>" value = "<?php echo $resubject;?>" />
                </td>

            <?php
            }
            else
            {
            ?>

                <td class = "fb_leftcolumn">
                    <strong><?php echo _GEN_SUBJECT; ?></strong>:
                </td>

                <td>
                    <input type = "hidden" class = "inputbox" name = "subject" size = "35" maxlength = "<?php echo $kunena_config->maxsubject;?>" value = "<?php echo $resubject;?>" /><?php echo $resubject; ?>
                </td>

            <?php
            }
            ?>

            <?php
            if ($setFocus == 0 && $id == 0 && !$fromBot)
            {
                echo "<script type=\"text/javascript\">document.postform.subject.focus();</script>";
                $setFocus = 1;
            }
            ?>
        </tr>

            <?php
            if ($parentid == 0)
            {
?>
        <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
            <td class = "fb_leftcolumn">
                <strong><?php echo _GEN_TOPIC_ICON; ?></strong>:
            </td>

            <td class = "fb-topicicons">
                <?php
                $topicToolbar = smile::topicToolbar(0, $kunena_config->rtewidth);
                echo $topicToolbar;
                ?>
            </td>
        </tr>
            <?php
            }
            ?>

        <?php
        if ($kunena_config->rtewidth == 0) {
            $useRte = 0;
        }
        else {
            $useRte = 1;
        }

        $fbTextArea = smile::fbWriteTextarea('message', $htmlText, $kunena_config->rtewidth, $kunena_config->rteheight, $useRte, $kunena_config->disemoticons);
        echo $fbTextArea;

        if ($setFocus == 0) {
            echo '<tr><td style="display:none;"><script type="text/javascript">document.postform.message.focus();</script></td></tr>';
        }

        //check if this user is already subscribed to this topic but only if subscriptions are allowed
        if ($kunena_config->allowsubscriptions == 1)
        {
            if ($id == 0) {
                $fb_thread = -1;
            }
            else
            {
                $kunena_db->setQuery("SELECT thread FROM #__fb_messages WHERE id='{$id}'");
                $fb_thread = $kunena_db->loadResult();
            }

            $kunena_db->setQuery("SELECT thread FROM #__fb_subscriptions WHERE userid='{$kunena_my->id}' AND thread='{$fb_thread}'");
            $fb_subscribed = $kunena_db->loadResult();

            if ($fb_subscribed == "" || $id == 0) {
                $fb_cansubscribe = 1;
            }
            else {
                $fb_cansubscribe = 0;
            }
        }
?>

               <!-- preview -->
        <tr class = "<?php echo $boardclass; ?>sectiontableentry2" id="previewContainer" style="display:none;">
           <td class = "fb_leftcolumn">
                <strong><?php echo _PREVIEW; ?></strong>:
            </td>
           <td>
  <div class="previewMsg" id="previewMsg" style="height:<?php echo $kunena_config->rteheight;?>px;overflow:auto;"></div>
            </td>
        </tr>
<!-- /preview -->
<?php
        if (($kunena_config->allowimageupload || ($kunena_config->allowimageregupload && $kunena_my->id != 0) || $kunena_is_moderator) && $no_image_upload == "0")
        {
        ?>

            <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
                <td class = "fb_leftcolumn">
                    <strong><?php echo _IMAGE_SELECT_FILE; ?></strong>
                </td>

                <td>
                    <input type = 'file' class = 'fb_button' name = 'attachimage' onmouseover = "javascript:kunenaShowHelp('<?php @print(_IMAGE_DIMENSIONS).": ".$kunena_config->imagewidth."x".$kunena_config->imageheight." - ".$kunena_config->imagesize." KB";?>')" />
                    <input type = "button" class = "fb_button" name = "addImagePH" value = "<?php @print(_POST_ATTACH_IMAGE);?>" style = "cursor:auto; width: 4em" onclick = "bbfontstyle(' [img/] ','');" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_IMGPH);?>')" />
                </td>
            </tr>

        <?php
        }
        ?>

        <?php
        if (($kunena_config->allowfileupload || ($kunena_config->allowfileregupload && $kunena_my->id != 0) || $kunena_is_moderator) && $no_file_upload == "0")
        {
        ?>

            <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
                <td class = "fb_leftcolumn">
                    <strong><?php echo _FILE_SELECT_FILE; ?></strong>
                </td>

                <td>
                    <input type = 'file' class = 'fb_button' name = 'attachfile' onmouseover = "javascript:kunenaShowHelp('<?php @print(_FILE_TYPES).": ".$kunena_config->filetypes." - ".$kunena_config->filesize." KB";?>')" style = "cursor:auto" />
                    <input type = "button" class = "fb_button" name = "addFilePH" value = "<?php @print(_POST_ATTACH_FILE);?>" style = "cursor:auto; width: 4em" onclick = "bbfontstyle(' [file/] ','');" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_FILEPH);?>')" />
                </td>
            </tr>

        <?php
        }

        if ($kunena_my->id != 0 && $kunena_config->allowsubscriptions == 1 && $fb_cansubscribe == 1 && !$editmode)
        {
        ?>

            <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
                <td class = "fb_leftcolumn">
                    <strong><?php echo _POST_SUBSCRIBE; ?></strong>:
                </td>

                <td>
                    <?php
                    if ($kunena_config->subscriptionschecked == 1)
                    {
                    ?>

                            <input type = "checkbox" name = "subscribeMe" value = "1" checked/>

                            <i><?php echo _POST_NOTIFIED; ?></i>

                    <?php
                    }
                    else
                    {
                    ?>

                        <input type = "checkbox" name = "subscribeMe" value = "1" />

                        <i><?php echo _POST_NOTIFIED; ?></i>

                    <?php
                    }
                    ?>
                </td>
            </tr>
        <?php }
        $catsallowed = explode(',',$fbConfig->pollallowedcategories);
        if (in_array($catid, $catsallowed)){
        //Check if it's is a new thread and show the poll
         if($fbConfig->pollenabled == "1" && $id == "0" ) {
         ?>
            <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
                <td class = "fb_leftcolumn">
                    <strong><?php echo _KUNENA_POLL_ADD; ?></strong>
                </td>
                <td>
                    <?php echo _KUNENA_POLL_TITLE; ?> <input type = "text" id = "poll_title" name = "poll_title" value="<?php if(isset($polldatasedit[0]->title)) { echo $polldatasedit[0]->title; } ?>" />&nbsp; <?php echo _KUNENA_POLL_TIME_TO_LIVE; ?> <input type = "text" id = "poll_time_to_live" name = "poll_time_to_live" value="<?php if(isset($polldatasedit[0]->polltimetolive)) { echo $polldatasedit[0]->polltimetolive; } ?>" />

                    <!-- The field hidden allow to know the options number chooses by the user -->
                    <?php if($editmode != "1"){ ?>
                    <input type="hidden" name="number_total_options" id="numbertotal">
                    <?php } ?>
                    <input type = "button" class = "fb_button" value = "<?php echo _KUNENA_POLL_ADD_OPTION; ?>" onclick = "javascript:new_field(<?php echo $fbConfig->pollnboptions; ?>);">
                    <input type = "button" class = "fb_button" value = "<?php echo _KUNENA_POLL_REM_OPTION; ?>" onclick = "javascript:delete_field();">
                </td>
            </tr>
           <?php }
           }
           // Begin captcha . Thanks Adeptus
		if ($kunena_config->captcha == 1 && $kunena_my->id < 1) { ?>
        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
            <td class = "fb_leftcolumn">&nbsp;<strong><?php echo _KUNENA_CAPDESC; ?></strong>&nbsp;</td>
            <td align="left" valign="middle" height="35px">&nbsp;<input name="txtNumber" type="text" id="txtNumber" value="" class="fb_button" style="vertical-align:top" size="15">
			<img src="index.php?option=com_kunena&func=showcaptcha" alt="" />
		 </td>
         </tr>
        <?php
		}
		// Finish captcha
		if(($editmode == "1") && $fbConfig->pollenabled == "1") {
		      $catsallowed = explode(',',$fbConfig->pollallowedcategories);
        if (in_array($catid, $catsallowed)){
		      //This query is need because, in this part i haven't access to the variable $parent
		      //I need to determine if the post if a parent or not for display the form for the poll
          $mesparent = CKunenaPolls::get_parent($id);
          $polloptions = CKunenaPolls::get_total_options($id);
          if($mesparent->parent == "0"){
        ?>
        <tr id="fb_post_edit_poll">
        <input type="hidden" name="number_total_options" id="numbertotalr" value="<?php echo $polloptions; ?>">
        <script type="text/javascript">var number_field="<?php echo $polloptions+1; ?>";</script>
            <td id="fb_post_options" colspan = "2" style = "text-align: center;">
            <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
                <td class = "fb_leftcolumn" >
            <strong><?php echo _KUNENA_POLL_TITLE; ?></strong>
            </td> <td>
            <input type = text" id = "poll_title" name = "poll_title" value="<?php if(isset($polldatasedit[0]->title)) { echo $polldatasedit[0]->title; } ?>" />
            </td>
            </tr>
            <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
                <td class = "fb_leftcolumn" >
            <strong><?php echo _KUNENA_POLL_TIME_TO_LIVE; ?></strong>
            </td> <td>
            <input type = text" id = "poll_time_to_live" name = "poll_time_to_live" value="<?php if(isset($polldatasedit[0]->polltimetolive)) { echo $polldatasedit[0]->polltimetolive; } ?>" /><input type = "button" class = "fb_button" value = "<?php echo _KUNENA_POLL_ADD_OPTION; ?>" onclick = "javascript:new_field(<?php echo $fbConfig->pollnboptions; ?>);">

                    <input type = "button" class = "fb_button" value = "<?php echo _KUNENA_POLL_REM_OPTION; ?>" onclick = "javascript:delete_field();">
            </td>
            </tr>
                <?php
                  if(isset($polloptions)) {
                    $nboptions = "1";
                    for($i=0;$i < $polloptions;$i++){
                      echo "<tr class=\"".$boardclass."sectiontableentry2\" id=\"option".$nboptions."\"><td style=\"font-weight: bold\" class=\"fb_leftcolumn\">Option ".$nboptions."</td><td><input type=\"text\" id=\"field_option".$i."\" name=\"field_option".$i."\" value=\"".$polldatasedit[$i]->text."\" /></td></tr>";
                      $nboptions++;
                    }
                  }
                ?>
            </td>
          </tr>
          <?php }
            }
          } ?>
        <tr id="fb_post_buttons_tr">
            <td id="fb_post_buttons" colspan = "2" style = "text-align: center;">
                <input type="submit" name="submit"  class="fb_button" value="<?php @print(' '._GEN_CONTINUE.' ');?>" onclick="return submitForm()" onmouseover = "javascript:jQuery('input[name=helpbox]').val('<?php @print(_KUNENA_EDITOR_HELPLINE_SUBMIT);?>')" />
                <input type="button" name="preview" class="fb_button" value="<?php @print(' '._PREVIEW.' ');?>"      onClick="fbGetPreview(document.postform.message.value,<?php echo KUNENA_COMPONENT_ITEMID?>);" onmouseover = "javascript:jQuery('input[name=helpbox]').val('<?php @print(_KUNENA_EDITOR_HELPLINE_PREVIEW);?>')" />
                <input type="button" name="cancel"  class="fb_button" value="<?php @print(' '._GEN_CANCEL.' ');?>"   onclick="javascript:window.history.back();" onmouseover = "javascript:jQuery('input[name=helpbox]').val('<?php @print(_KUNENA_EDITOR_HELPLINE_CANCEL);?>')" />
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<input type="hidden" value="<?php echo JURI::base(true).'/components/com_kunena/template/default';?>" name="templatePath" />
<input type="hidden" value="<?php echo JURI::base(true);?>/" name="kunenaPath" />
</form>

</td>

</tr>

<tr>
    <td>
        <?php
        if ($kunena_config->askemail) {
            echo $kunena_config->showemail == '0' ? "<em>* - " . _POST_EMAIL_NEVER . "</em>" : "<em>* - " . _POST_EMAIL_REGISTERED . "</em>";
        }
        ?>
    </td>
</tr>

<tr>
    <td style = "text-align: left;">
        <br/>

    <?php
    $no_upload = "0"; //reset the value.. you just never know..

    if ($kunena_config->showhistory == 1) {
        listThreadHistory($id, $kunena_config, $kunena_db);
    }
?>
