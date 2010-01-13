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

// Dont allow direct linking
defined( '_JEXEC' ) or die();


$kunena_db = &JFactory::getDBO();
$kunena_config =& CKunenaConfig::getInstance();

# Check for Editor rights  $kunena_config->annmodid
$user_fields = @explode(',', $kunena_config->annmodid);

if (in_array($kunena_my->id, $user_fields) || CKunenaTools::isAdmin()) {
    $is_editor = true;
    }
else {
    $is_editor = false;
    }
?>

<?php
// BEGIN: BOX ANN
$kunena_db->setQuery("SELECT id, title, sdescription, description, created, published, showdate FROM #__fb_announcement WHERE published='1' ORDER BY created DESC", 0, 1);

$anns = $kunena_db->loadObjectList();
check_dberror("Unable to load announcements.");
if (count($anns) == 0) return;
$ann = $anns[0];
$annID = $ann->id;
$anntitle = stripslashes($ann->title);

$kunena_emoticons = smile::getEmoticons(0);
$annsdescription = stripslashes(smile::smileReplace($ann->sdescription, 0, $kunena_config->disemoticons, $kunena_emoticons));
$annsdescription = nl2br($annsdescription);

$anndescription = stripslashes(smile::smileReplace($ann->description, 0, $kunena_config->disemoticons, $kunena_emoticons));
$anndescription = nl2br($anndescription);

$anncreated = KUNENA_timeformat(strtotime($ann->created));
$annpublished = $ann->published;
$annshowdate = $ann->showdate;

if ($annID > 0) {
?>
    <!-- ANNOUNCEMENTS BOX -->
<div class="fb__bt_cvr1">
<div class="fb__bt_cvr2">
<div class="fb__bt_cvr3">
<div class="fb__bt_cvr4">
<div class="fb__bt_cvr5">
    <table class = "fb_blocktable" id = "fb_announcement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "fb_title_cover fbm">
                        <span class = "fb_title fbl"><?php echo $anntitle; ?></span>
                    </div>

                    <img id = "BoxSwitch_announcements__announcements_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>

        <tbody id = "announcements_tbody">
            <?php
            if ($is_editor) {
            ?>

                    <tr class = "fb_sth">
                        <th class = "th-1 fb_sectiontableheader fbm" align="left">
                            <?php echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'edit', $annID), _ANN_EDIT, _ANN_EDIT,'nofollow'); ?> |
                            <?php echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'delete', $annID), _ANN_DELETE, _ANN_DELETE,'nofollow'); ?> |
							<?php echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'add'), _ANN_ADD, _ANN_ADD,'nofollow'); ?> |
							<?php echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'show'), _ANN_CPANEL, _ANN_CPANEL, 'nofollow'); ?>
                        </th>
                    </tr>

            <?php
                }
            ?>

                <tr class = "fb_sectiontableentry2">
                    <td class = "td-1 fbm" align="left">
                        <?php
                        if ($annshowdate > 0) {
                        ?>

                            <div class = "anncreated">
<?php echo $anncreated; ?>
                            </div>

                        <?php
                            }
                        ?>

                        <div class = "anndesc">
<?php echo $annsdescription; ?>

<?php
if (!empty($anndescription)) {
?>

    &nbsp;&nbsp;&nbsp;<?php echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'read', $annID), _ANN_READMORE, _ANN_READMORE,'follow'); ?>

<?php
    }
?>
                        </div>
                    </td>
                </tr>
        </tbody>
    </table>
    </div>
</div>
</div>
</div>
</div>
    <!-- / ANNOUNCEMENTS BOX -->

<?php
    }
// FINISH: BOX ANN
?>
