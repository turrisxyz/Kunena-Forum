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
$kunena_app =& JFactory::getApplication();
$document =& JFactory::getDocument();
$kunena_config =& CKunenaConfig::getInstance();

$document->setTitle(_ANN_ANNOUNCEMENTS . ' - ' . stripslashes($kunena_config->board_title));

# Check for Editor rights  $kunena_config->annmodid
$do = JRequest::getVar("do", "");
$id = intval(JRequest::getVar("id", ""));
$user_fields = @explode(',', $kunena_config->annmodid);

if (in_array($kunena_my->id, $user_fields) || CKunenaTools::isAdmin()) {
    $is_editor = true;
    }
else {
    $is_editor = false;
    }

// BEGIN: READ ANN
if ($do == "read") {
    $kunena_db->setQuery("SELECT id, title, sdescription, description, created, published, showdate FROM #__fb_announcement WHERE id='{$id}' AND published='1'");
    $anns_ = $kunena_db->loadObjectList();
    	check_dberror("Unable to load announcements.");

    $ann = $anns_[0];
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

    if ($annpublished > 0) {
?>

        <table class = "fb_blocktable" id = "fb_announcement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th>
                        <div class = "fb_title_cover fbm">
                            <span class = "fb_title fbl"> <?php echo $kunena_app->getCfg('sitename'); ?> <?php echo _ANN_ANNOUNCEMENTS; ?></span>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody id = "announcement_tbody">
                <tr class = "fb_sth fbs">
                    <th class = "th-1 fb_sectiontableheader" align="left" >
                        <?php
                        if ($is_editor) {
                        	echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'edit', $annID), _ANN_EDIT, _ANN_EDIT, 'follow').' | ';
                    		echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'delete', $annID), _ANN_DELETE, _ANN_DELETE, 'follow').' | ';
                    		echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'add'), _ANN_ADD, _ANN_ADD, 'follow').' | ';
                    		echo CkunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'show'), _ANN_CPANEL, _ANN_CPANEL, 'follow');
                        }
                        ?>
                    </th>
                </tr>

                <tr>
                    <td class = "fb_anndesc" valign="top">
                        <h3> <?php echo $anntitle; ?> </h3>

                        <?php
                        if ($annshowdate > 0) {
                        ?>

                            <div class = "anncreated fbs">
<?php echo $anncreated; ?>
                            </div>

                        <?php
                            }
                        ?>

    <div class = "anndesc">
<?php echo !empty($anndescription) ? $anndescription : $annsdescription; ?>
    </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php
        }
    }

// FINISH: READ ANN
if ($is_editor) {
        ?>
        <!-- announcement-->
        <?php
    // BEGIN: SHOW ANN
    if ($do == "show") {
        ?>
<div class="fb__bt_cvr1">
<div class="fb__bt_cvr2">
<div class="fb__bt_cvr3">
<div class="fb__bt_cvr4">
<div class="fb__bt_cvr5">
            <table class = "fb_blocktable" id = "fb_announcement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
                <thead>
                    <tr>
                        <th colspan = "6">
                            <div class = "fb_title_cover fbm">
                                <span class = "fb_title fbl"> <?php echo $kunena_app->getCfg('sitename'); ?> <?php echo _ANN_ANNOUNCEMENTS; ?> | <?php echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'add'), _ANN_ADD, _ANN_ADD, 'follow'); ?></span>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody id = "announcement_tbody">
                    <tr class = "fb_sth fbs">
                        <th class = "th-1 fb_sectiontableheader"  width="1%" align="center"> <?php echo _ANN_ID; ?>
                        </th>

                        <th class = "th-2 fb_sectiontableheader" width="15%" align="left"> <?php echo _ANN_DATE; ?>
                        </th>

                        <th class = "th-3 fb_sectiontableheader" width="54%" align="left"> <?php echo _ANN_TITLE; ?>
                        </th>

                        <th class = "th-4 fb_sectiontableheader" width="10%"  align="center"> <?php echo _ANN_PUBLISH; ?>
                        </th>

                        <th class = "th-5 fb_sectiontableheader"  width="10%"  align="center"> <?php echo _ANN_EDIT; ?>
                        </th>

                        <th class = "th-6 fb_sectiontableheader" width="10%"  align="center"> <?php echo _ANN_DELETE; ?>
                        </th>
                    </tr>

                    <?php
                    $query = "SELECT id, title, created, published FROM #__fb_announcement ORDER BY created DESC";
                    $kunena_db->setQuery($query);
                    $rows = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load announcements.");

                    $tabclass = array
                    (
                        "sectiontableentry1",
                        "sectiontableentry2"
                    );

                    $k = 0;

                    if (count($rows) > 0) {
                        foreach ($rows as $row) {
                            $k = 1 - $k;
                    ?>

                            <tr class = "fb_<?php echo $tabclass[$k];?>">
                                <td class = "td-1"  align="center">
                                    #<?php echo $row->id; ?>
                                </td>

                                <td class = "td-2" align="left">
<?php echo KUNENA_timeformat(strtotime($row->created)); ?>
                                </td>

                                <td class = "td-3"  align="left">
                                    <?php echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'read', $row->id), stripslashes($row->title), stripslashes($row->title), 'follow'); ?>
                                </td>

                                <td class = "td-4"  align="center">
                                    <?php
                                    if ($row->published > 0) {
                                        echo _ANN_PUBLISHED;
                                        }
                                    else {
                                        echo _ANN_UNPUBLISHED;
                                        }
                                    ?>
                                </td>

                                <td class = "td-5"  align="center">
                                    <?php echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'edit', $row->id), _ANN_EDIT,_ANN_EDIT,'follow'); ?>
                                </td>

                                <td class = "td-6"  align="center">
                                    <?php echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'delete', $row->id), _ANN_DELETE, _ANN_DELETE, 'follow'); ?>
                                </td>
                            </tr>

                    <?php
                            }
                        }
                    ?>
                </tbody>
            </table>
</div>
</div>
</div>
</div>
</div>
            <?php
        }

    // FINISH: SHOW ANN
    // BEGIN: ADD ANN
    if ($do == "doadd") {
        JFilterOutput::objectHTMLSafe ($_POST);
        $title = addslashes(JRequest::getVar("title", ""));
        $description = addslashes(JRequest::getVar('description', '', 'string', JREQUEST_ALLOWRAW));
        $sdescription = addslashes(JRequest::getVar('sdescription', '', 'string', JREQUEST_ALLOWRAW));
        $created = addslashes(JRequest::getVar("created", ""));
        $published = JRequest::getInt("published", 0);
        $ordering = 0;
        $showdate = addslashes(JRequest::getVar("showdate", ""));
        # Clear any HTML
        $query1 = "INSERT INTO #__fb_announcement VALUES ('', '$title', '$sdescription', '$description', " . (($created <> '')?"'$created'":"NOW()") . ", '$published', '$ordering','$showdate')";
        $kunena_db->setQuery($query1);

        $kunena_db->query() or check_dberror("Unable to insert announcement.");
        $kunena_app->redirect(CKunenaLink::GetAnnouncementURL($kunena_config, 'show'), _ANN_SUCCESS_ADD);
    }

    if ($do == "add") {
        if (!$is_editor) {
            die ("Hacking attempt");
            }
		$calendar = JHTML::_('calendar', '', 'created', 'addcreated');
            ?>
<div class="fb__bt_cvr1">
<div class="fb__bt_cvr2">
<div class="fb__bt_cvr3">
<div class="fb__bt_cvr4">
<div class="fb__bt_cvr5">
<table class = "fb_blocktable" id = "fb_announcement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th>
                <div class = "fb_title_cover fbm">
                    <span class = "fb_title fbl"> <?php echo _ANN_ANNOUNCEMENTS; ?>: <?php echo _ANN_ADD; ?> | <?php echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'show'), _ANN_CPANEL, _ANN_CPANEL,'follow'); ?></span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody id = "announcement_tbody">
        <tr>
            <td class = "fb_anndesc" valign="top">
                <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=announcement&amp;do=doadd'); ?>" method = "post" name = "addform">
                    <strong><?php echo _ANN_TITLE; ?>:</strong>

                    <br/>

                    <input type = "text" name = "title" size = "40" maxlength = "150"/>

                    <br/>

                    <strong><?php echo _ANN_SORTTEXT; ?>:</strong>

                    <br/>

                    <textarea id = "textarea1" name = "sdescription" rows = "8" cols = "60" style = "width:95%; height:300px;"></textarea>

                    <br/>

                    <hr/>

                    <strong><?php echo _ANN_LONGTEXT; ?></strong>

                    <br/>

                    <textarea id = "textarea2" name = "description" rows = "30" cols = "60" style = "width:95%; height:550px;"></textarea>

                    <br/>

                    <strong><?php echo _ANN_DATE; ?>:</strong>

					<?php echo $calendar; ?>

                    <br/>

                    <strong><?php echo _ANN_PUBLISH; ?></strong>

                    <select name = "published">
                        <option value = "1"><?php echo _ANN_YES; ?></option>

                        <option value = "0"><?php echo _ANN_NO; ?></option>
                    </select>

                    <br/>

                    <strong><?php echo _ANN_SHOWDATE; ?></strong>

                    <select name = "showdate">
                        <option value = "1"><?php echo _ANN_YES; ?></option>

                        <option value = "0"><?php echo _ANN_NO; ?></option>
                    </select>

                    <br/>

                    <strong><?php echo _ANN_ORDER; ?>:</strong>

                    <input type = "text" name = "ordering" size = "5" value = "0"/>

                    <br/>

                    <INPUT TYPE = 'hidden' NAME = "do" value = "doadd">

                    <input name = "submit" type = "submit" value = "<?php echo _ANN_SAVE; ?>"/>
                </form>
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<?php
        }
    // FINISH: ADD ANN
?>

<?php
    // BEGIN: EDIT ANN
    if ($do == "doedit") {
        JFilterOutput::objectHTMLSafe ($_POST);
        $title = JRequest::getVar("title", "");
        $description = JRequest::getVar('description', '', 'string', JREQUEST_ALLOWRAW);
        $sdescription = JRequest::getVar('sdescription', '', 'string', JREQUEST_ALLOWRAW);
        $created = JRequest::getVar("created", "");
        $published = JRequest::getVar("published", 0);
        $showdate = JRequest::getVar("showdate", "");

        $kunena_db->setQuery("UPDATE #__fb_announcement SET title='$title', description='$description', sdescription='$sdescription',  created=" . (($created <> '')?"'$created'":"NOW()") . ", published='$published', showdate='$showdate' WHERE id=$id");

        if ($kunena_db->query()) {
            $kunena_app->redirect(CKunenaLink::GetAnnouncementURL($kunena_config, 'show'), _ANN_SUCCESS_EDIT);
            }
        check_dberror("Unable to update announcement.");
    }

    if ($do == "edit") {
        $kunena_db->setQuery("SELECT * FROM #__fb_announcement WHERE id='{$id}'");
        $anns = $kunena_db->loadObjectList();
        check_dberror("Unable to load announcements.");

        $ann = $anns[0];
        $annID = $ann->id;
        $anntitle = kunena_htmlspecialchars(stripslashes($ann->title));
        $annsdescription = kunena_htmlspecialchars(stripslashes($ann->sdescription));
        $anndescription = kunena_htmlspecialchars(stripslashes($ann->description));
        $anncreated = $ann->created;
        $annpublished = $ann->published;
        $annordering = $ann->ordering;
        $annshowdate = $ann->showdate;
        $calendar = JHTML::_('calendar', $anncreated, 'created', 'addcreated');
        //$document->addCustomTag('<link rel="stylesheet" type="text/css" media="all" href="' . JURI::root() . '/includes/js/calendar/calendar-mos.css" title="green" />');
?>
<script type = "text/javascript">
    <!--
    function validate_form()
    {
        valid = true;

        if (document.editform.title.value == "")
        {
            alert("Please fill in the 'Title' box.");
            valid = false;
        }

        if (document.editform.sdescription.value == "")
        {
            alert("Please fill in the 'Short Desc' box.");
            valid = false;
        }

        return valid;
    }
            //-->
</script>
<div class="fb__bt_cvr1">
<div class="fb__bt_cvr2">
<div class="fb__bt_cvr3">
<div class="fb__bt_cvr4">
<div class="fb__bt_cvr5">
<table class = "fb_blocktable" id = "fb_announcement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th>
                <div class = "fb_title_cover fbm">
                    <span class = "fb_title fbl"> <?php echo _ANN_ANNOUNCEMENTS; ?>: <?php echo _ANN_EDIT; ?> | <?php echo CKunenaLink::GetSefHrefLink(CKunenaLink::GetAnnouncementURL($kunena_config, 'show'), _ANN_CPANEL, _ANN_CPANEL, 'follow'); ?></span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody id = "announcement_tbody">
        <tr>
            <td class = "fb_anndesc" valign="top">
                <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=announcement&amp;do=doedit'); ?>" method = "post" name = "editform" onSubmit = "return validate_form ( );">
                    <strong>#<?php echo $annID; ?> : <?php echo $anntitle; ?></strong>

                    <br/>

                    <strong><?php echo _ANN_TITLE; ?>:</strong>

                    <br/>

                    <input type = "text" name = "title" size = "40" maxlength = "150" value = "<?php echo $anntitle ;?>"/>

                    <br/>

                    <strong><?php echo _ANN_SORTTEXT; ?>:</strong>

                    <br/>

                    <textarea id = "textarea1" name = "sdescription" rows = "8" cols = "60" style = "width:95%; height:300px;"><?php echo $annsdescription; ?></textarea>

                    <br/>

                    <hr/>

                    <strong><?php echo _ANN_LONGTEXT; ?>:</strong>

                    <br/>

                    <textarea id = "textarea2" name = "description" rows = "30" cols = "60" style = "width:95%; height:550px;"><?php echo $anndescription; ?></textarea>

                    <br/>

                    <strong><?php echo _ANN_DATE; ?>:</strong>

                    <?php echo $calendar;?>

                    <br/>

                    <strong><?php echo _ANN_SHOWDATE; ?>: &nbsp;&nbsp;&nbsp;</strong>

                    <select name = "showdate">
                        <option value = "1"<?php echo ($annshowdate == 1 ? 'selected="selected"' : ''); ?>><?php echo _ANN_YES; ?></option>

                        <option value = "0"<?php echo ($annshowdate == 0 ? 'selected="selected"' : ''); ?>><?php echo _ANN_NO; ?></option>
                    </select>

                    <br/>

                    <strong><?php echo _ANN_PUBLISH; ?>: &nbsp;&nbsp;&nbsp;</strong>

                    <select name = "published">
                        <option value = "1"<?php echo ($annpublished == 1 ? 'selected="selected"' : ''); ?>><?php echo _ANN_YES; ?></option>

                        <option value = "0"<?php echo ($annpublished == 0 ? 'selected="selected"' : ''); ?>><?php echo _ANN_NO; ?></option>
                    </select>

                    <br/>

                    <INPUT TYPE = 'hidden' NAME = "do" value = "doedit"/>

                    <INPUT TYPE = 'hidden' NAME = "id" value = "<?php echo $annID ;?>"/>

                    <input name = "submit" type = "submit" value = "<?php echo _ANN_SAVE; ?>"/>

                    <br/>

                    <br/>

                    <br/>
                </form>
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<?php
        }

    // FINISH: EDIT ANN
    // BEGIN: delete ANN
    if ($do == "delete")
    {
        $query1 = "DELETE FROM #__fb_announcement WHERE id=$id ";
        $kunena_db->setQuery($query1);
        $kunena_db->query() or check_dberror("Unable to delete announcement.");

        $kunena_app->redirect(CKunenaLink::GetAnnouncementURL($kunena_config, 'show'), _ANN_DELETED);
    }
    // FINISH: delete ANN
?>
<!-- /announcement-->
<?php
    }
?>
