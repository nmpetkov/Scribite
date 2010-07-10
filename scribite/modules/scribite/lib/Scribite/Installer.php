<?php
/**
 * Zikula Application Framework
 *
 * @copyright  (c) Zikula Development Team
 * @link       http://www.zikula.org
 * @version    $Id$
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author     sven schomacker <hilope@gmail.com>
 * @category   Zikula_Extension
 * @package    Utilities
 * @subpackage scribite!
 */

class Scribite_Installer extends Zikula_Installer
{
    public function install()
    {
        // check for Zikula version, this sversion only works with 1.3.0 and above
        // and create the system init hook
        if (System::VERSION_NUM < '1.3.0' ) {
            LogUtil::registerError($this->__('This version of scribite! only works with Zikula 1.3.x and higher. Please upgrade your Zikula version or use scribite! version 2.x or 3.x .'));
            return false;
        }

        if (!DBUtil::createTable('scribite')) {
            return false;
        }

        if (!ModUtil::registerHook('zikula', 'systeminit', 'GUI', 'scribite', 'user', 'run')) {
            return LogUtil::registerError($this->__('Error creating Hook!'));
        }
        ModUtil::apiFunc('Modules', 'admin', 'enablehooks', array('callermodname' => 'zikula', 'hookmodname' => 'scribite'));
        LogUtil::registerStatus($this->__('<strong>scribite!</strong> was activated as core hook. You can check settings <a href="index.php?module=Modules&type=admin&func=hooks&id=0">here</a>!<br />The template plugin from previous versions of scribite! can be removed from templates.'));

        // create the default data for the module
        $this->defaultdata();

        // Initialisation successful
        return true;
    }

    public function upgrade($oldversion)
    {
        // check for Zikula version, this sversion only works with 1.3.0 and above
        // and create the system init hook
        if (version_compare(System::VERSION_NUM, '1.3.0') == -1) {
            LogUtil::registerError($this->__('This version from scribite! only works with Zikula 1.3.x and higher. Please upgrade your Zikula version or use scribite! version 2.x or 3.x .'));
            return false;
        }

        switch($oldversion) {
            case '1.0':
            // no changes made

            case '1.1':
            // delete old paths
                $this->delVar('xinha_path');
                $this->delVar('tinymce_path');
                // set new path
                $this->setVar('editors_path', 'javascript/scribite_editors');

            case '1.2':
                if (!DBUtil::createTable('scribite'))
                {
                    return false;
                }
                // create the default data for the module
                scribite_defaultdata();
                // del old module vars
                $this->delVar('editor');
                $this->delVar('editor_activemodules');

            case '1.21':
            // create new values
                $this->setVar('openwysiwyg_barmode', 'full');
                $this->setVar('openwysiwyg_width', '400');
                $this->setVar('openwysiwyg_height', '300');
                $this->setVar('xinha_statusbar', 1);

            case '1.3':
            // create new values
                $this->setVar('openwysiwyg_barmode', 'full');
                $this->setVar('openwysiwyg_width', '400');
                $this->setVar('openwysiwyg_height', '300');
                $this->setVar('xinha_statusbar', 1);

            case '2.0':
            // create new values
                $this->setVar('DefaultEditor', '-');
                $this->setVar('nicedit_fullpanel', 1);
                // fill some vars with defaults
                if (!$this->getVar('xinha_converturls')) {
                    $this->setVar('xinha_converturls', 1);
                }
                if (!$this->getVar('xinha_showloading')) {
                    $this->setVar('xinha_showloading', 1);
                }
                if (!$this->getVar('xinha_activeplugins')) {
                    $this->setVar('xinha_activeplugins', 'a:2:{i:0;s:7:"GetHtml";i:1;s:12:"SmartReplace";}');
                }
                if (!$this->getVar('tinymce_activeplugins')) {
                    $this->setVar('tinymce_activeplugins', '');
                }
                if (!$this->getVar('fckeditor_autolang')) {
                    $this->setVar('fckeditor_autolang', 1);
                }
                //create new module vars for crpCalendar
                $item = array('modname'   => 'crpCalendar',
                        'modfuncs'  => 'a:2:{i:0;s:3:"new";i:1;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:22:"crpcalendar_event_text";}',
                        'modeditor' => '-');
                if (!DBUtil::insertObject($item, 'scribite', false, 'mid')) {
                    LogUtil::registerError($this->__('Error! Could not update module configuration.'));
                    return '2.0';
                }

            case '2.1':
            //create new module vars for Content
                $record = array(array('modname'   => 'content',
                                'modfuncs'  => 'a:1:{i:0;s:5:"dummy";}',
                                'modareas'  => 'a:1:{i:0;s:5:"dummy";}',
                                'modeditor' => '-'));
                DBUtil::insertObjectArray($record, 'scribite', 'mid');

            case '2.2':
            //create new module vars for Blocks #14
                $record = array(array('modname'   => 'Blocks',
                                'modfuncs'  => 'a:1:{i:0;s:6:"modify";}',
                                'modareas'  => 'a:1:{i:0;s:14:"blocks_content";}',
                                'modeditor' => '-'));
                DBUtil::insertObjectArray($record, 'scribite', 'mid');
                // check for Zikula 1.1.x version
                if (System::VERSION_NUM < '1.1.0' ) {
                    LogUtil::registerError($this->__('This version from scribite! only works with Zikula 1.1.x and higher. Please upgrade your Zikula version or use scribite! version 2.x .'));
                    return '2.2';
                }
                // create systeminit hook - new in Zikula 1.1.0
                if (!ModUtil::registerHook('zikula', 'systeminit', 'GUI', 'scribite', 'user', 'run')) {
                    LogUtil::registerError($this->__('Error creating Hook!'));
                    return '2.2';
                }
                ModUtil::apiFunc('Modules', 'admin', 'enablehooks', array('callermodname' => 'zikula', 'hookmodname' => 'scribite'));
                LogUtil::registerStatus($this->__('<strong>scribite!</strong> was activated as core hook. You can check settings <a href="index.php?module=Modules&type=admin&func=hooks&id=0">here</a>!<br />The template plugin from previous versions of scribite! can be removed from templates.'));

            case '3.0':
            //create new module vars for Newsletter and Web_Links
                $record = array(array('modname'   => 'Newsletter',
                                'modfuncs'  => 'a:1:{i:0;s:11:"add_message";}',
                                'modareas'  => 'a:1:{i:0;s:7:"message";}',
                                'modeditor' => '-'),
                        array('modname'   => 'crpVideo',
                                'modfuncs'  => 'a:2:{i:0;s:3:"new";i:1;s:6:"modify";}',
                                'modareas'  => 'a:1:{i:0;s:13:"video_content";}',
                                'modeditor' => '-'),
                        array('modname'   => 'Web_Links',
                                'modfuncs'  => 'a:3:{i:0;s:8:"linkview";i:1;s:7:"addlink";i:2;s:17:"modifylinkrequest";}',
                                'modareas'  => 'a:1:{i:0;s:11:"description";}',
                                'modeditor' => '-'));
                DBUtil::insertObjectArray($record, 'scribite', 'mid');

                // set vars for YUI Rich Text Editor
                if (!$this->getVar('yui_type')) {
                    $this->setVar('yui_type', 'Simple');
                }
                if (!$this->getVar('yui_width')) {
                    $this->setVar('yui_width', 'auto');
                }
                if (!$this->getVar('yui_height')) {
                    $this->setVar('yui_height', '300');
                }
                if (!$this->getVar('yui_dombar')) {
                    $this->setVar('yui_dombar', true);
                }
                if (!$this->getVar('yui_animate')) {
                    $this->setVar('yui_animate', true);
                }
                if (!$this->getVar('yui_collapse')) {
                    $this->setVar('yui_collapse', true);
                }

            case '3.1':
            // modify Profile module
                $originalconfig = ModUtil::apiFunc('Scribite', 'user', 'getModuleConfig', array('modulename' => "Profile"));
                $newconfig = array('mid'        => $originalconfig['mid'],
                        'modulename' => 'Profile',
                        'modfuncs'   => "modify",
                        'modareas'   => "prop_signature,prop_extrainfo,prop_yinterests",
                        'modeditor'  => $originalconfig['modeditor']);
                $modupdate = ModUtil::apiFunc('Scribite', 'admin', 'editmodule', $newconfig);

            case '3.2':
            // set new editors folder
                $this->setVar('editors_path', 'modules/Scribite/includes');
                LogUtil::registerStatus($this->__('<strong>Caution!</strong><br />All editors have moved to /modules/Scribite/includes in preparation for upcoming features of Zikula. Please check all your settings!<br />If you have adapted files from editors you have to check them too.<br /><br /><strong>Dropped support for FCKeditor and TinyMCE</strong><br />For security reasons these editors will not be supported anymore. Please change editors to an other editor.'));

            case '4.0':

            case '4.1':

            case '4.2':
                $this->setVar('nicedit_xhtml', 1);
            case '4.2.1':


        }

        // clear the cache folders
        $view = new Zikula_View('Scribite');
        $view->clear_compiled_tpl();
        $view->clear_all_cache();

        return true;
    }

    public function uninstall()
    {
        // drop table
        if (!DBUtil::dropTable('scribite')) {
            return false;
        }

        // Delete any module variables
        $this->delVars();

        // delete the system init hook
        if (!ModUtil::unregisterHook('zikula', 'systeminit', 'GUI', 'scribite', 'user', 'run')) {
            return LogUtil::registerError($this->__('Error deleting Hook!'));
        }
        // Deletion successful
        return true;
    }

    protected function defaultdata()
    {
        // Set editor defaults
        $this->setVar('editors_path', 'modules/Scribite/includes');
        $this->setVar('xinha_language', 'en');
        $this->setVar('xinha_skin', 'blue-look');
        $this->setVar('xinha_barmode', 'reduced');
        $this->setVar('xinha_width', 'auto');
        $this->setVar('xinha_height', 'auto');
        $this->setVar('xinha_style', 'modules/Scribite/config/xinha/editor.css');
        $this->setVar('xinha_statusbar', 1);
        $this->setVar('xinha_converturls', 1);
        $this->setVar('xinha_showloading', 1);
        $this->setVar('xinha_activeplugins', 'a:2:{i:0;s:7:"GetHtml";i:1;s:12:"SmartReplace";}');
        /* deprecated editors
    $this->setVar('tinymce_language', 'en');
    $this->setVar('tinymce_style', 'modules/Scribite/config/tiny_mce/editor.css');
    $this->setVar('tinymce_theme', 'simple');
    $this->setVar('tinymce_width', '75%');
    $this->setVar('tinymce_height', '400');
    $this->setVar('tinymce_dateformat', '%Y-%m-%d');
    $this->setVar('tinymce_timeformat', '%H:%M:%S');
    $this->setVar('tinymce_activeplugins', '');
    $this->setVar('fckeditor_language', 'en');
    $this->setVar('fckeditor_barmode', 'Default');
    $this->setVar('fckeditor_width', '500');
    $this->setVar('fckeditor_height', '400');
    $this->setVar('fckeditor_autolang', 1);
        */
        $this->setVar('openwysiwyg_barmode', 'full');
        $this->setVar('openwysiwyg_width', '400');
        $this->setVar('openwysiwyg_height', '300');
        $this->setVar('nicedit_fullpanel', 0);
        $this->setVar('nicedit_xhtml', 0);
        $this->setVar('yui_type', 'Simple');
        $this->setVar('yui_width', 'auto');
        $this->setVar('yui_height', '300');
        $this->setVar('yui_dombar', true);
        $this->setVar('yui_animate', true);
        $this->setVar('yui_collapse', true);

        // set database module defaults
        $record = array(array('modname'   => 'About',
                        'modfuncs'  => 'a:1:{i:0;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:10:"about_info";}',
                        'modeditor' => '-'),
                array('modname'   => 'Admin_Messages',
                        'modfuncs'  => 'a:2:{i:0;s:3:"new";i:1;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:22:"admin_messages_content";}',
                        'modeditor' => '-'),
                array('modname'   => 'Blocks',
                        'modfuncs'  => 'a:1:{i:0;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:14:"blocks_content";}',
                        'modeditor' => '-'),
                array('modname'   => 'Book',
                        'modfuncs'  => 'a:1:{i:0;s:3:"all";}',
                        'modareas'  => 'a:1:{i:0;s:7:"content";}',
                        'modeditor' => '-'),
                array('modname'   => 'ContentExpress',
                        'modfuncs'  => 'a:3:{i:0;s:0:"";i:1;s:10:"newcontent";i:2;s:11:"editcontent";}',
                        'modareas'  => 'a:1:{i:0;s:4:"text";}',
                        'modeditor' => '-'),
                array('modname'   => 'crpCalendar',
                        'modfuncs'  => 'a:2:{i:0;s:3:"new";i:1;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:22:"crpcalendar_event_text";}',
                        'modeditor' => '-'),
                array('modname'   => 'crpVideo',
                        'modfuncs'  => 'a:2:{i:0;s:3:"new";i:1;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:13:"video_content";}',
                        'modeditor' => '-'),
                array('modname'   => 'cotype',
                        'modfuncs'  => 'a:2:{i:0;s:3:"new";i:1;s:4:"edit";}',
                        'modareas'  => 'a:1:{i:0;s:4:"text";}',
                        'modeditor' => '-'),
                array('modname'   => 'content',
                        'modfuncs'  => 'a:1:{i:0;s:3:"dummy";}',
                        'modareas'  => 'a:1:{i:0;s:4:"dummy";}',
                        'modeditor' => '-'),
                array('modname'   => 'element',
                        'modfuncs'  => 'a:5:{i:0;s:11:"start_topic";i:1;s:9:"add_topic";i:2;s:10:"edit_topic";i:3;s:10:"view_topic";i:4;s:9:"edit_post";}',
                        'modareas'  => 'a:1:{i:0;s:4:"comm";}',
                        'modeditor' => '-'),
                array('modname'   => 'eventia',
                        'modfuncs'  => 'a:2:{i:0;s:3:"new";i:1;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:26:"eventia_course_description";}',
                        'modeditor' => '-'),
                array('modname'   => 'FAQ',
                        'modfuncs'  => 'a:2:{i:0;s:3:"new";i:1;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:9:"faqanswer";}',
                        'modeditor' => '-'),
                array('modname'   => 'htmlpages',
                        'modfuncs'  => 'a:2:{i:0;s:3:"new";i:1;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:17:"htmlpages_content";}',
                        'modeditor' => '-'),
                array('modname'   => 'Mailer',
                        'modfuncs'  => 'a:1:{i:0;s:10:"testconfig";}',
                        'modareas'  => 'a:1:{i:0;s:11:"mailer_body";}',
                        'modeditor' => '-'),
                array('modname'   => 'mediashare',
                        'modfuncs'  => 'a:3:{i:0;s:8:"addmedia";i:1;s:8:"edititem";i:2;s:8:"addalbum";i:3;s:9:"editalbum";}',
                        'modareas'  => 'a:1:{i:0;s:3:"all";}',
                        'modeditor' => '-'),
                array('modname'   => 'News',
                        'modfuncs'  => 'a:3:{i:0;s:3:"new";i:1;s:6:"modify";i:2;s:7:"display";}',
                        'modareas'  => 'a:2:{i:0;s:13:"news_hometext";i:1;s:13:"news_bodytext";}',
                        'modeditor' => '-'),
                array('modname'   => 'Newsletter',
                        'modfuncs'  => 'a:1:{i:0;s:11:"add_message";}',
                        'modareas'  => 'a:1:{i:0;s:7:"message";}',
                        'modeditor' => '-'),
                array('modname'   => 'PagEd',
                        'modfuncs'  => 'a:1:{i:0;s:3:"all";}',
                        'modareas'  => 'a:1:{i:0;s:5:"PagEd";}',
                        'modeditor' => '-'),
                array('modname'   => 'Pages',
                        'modfuncs'  => 'a:2:{i:0;s:3:"new";i:1;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:13:"pages_content";}',
                        'modeditor' => '-'),
                array('modname'   => 'pagesetter',
                        'modfuncs'  => 'a:1:{i:0;s:7:"pubedit";}',
                        'modareas'  => 'a:1:{i:0;s:3:"all";}',
                        'modeditor' => '-'),
                array('modname'   => 'PhotoGallery',
                        'modfuncs'  => 'a:2:{i:0;s:11:"editgallery";i:1;s:9:"editphoto";}',
                        'modareas'  => 'a:1:{i:0;s:17:"photogallery_desc";}',
                        'modeditor' => '-'),
                array('modname'   => 'pncommerce',
                        'modfuncs'  => 'a:1:{i:0;s:8:"itemedit";}',
                        'modareas'  => 'a:1:{i:0;s:15:"ItemDescription";}',
                        'modeditor' => '-'),
                array('modname'   => 'pnForum',
                        'modfuncs'  => 'a:4:{i:0;s:9:"viewtopic";i:1;s:8:"newtopic";i:2;s:8:"editpost";i:3;s:5:"reply";}',
                        'modareas'  => 'a:1:{i:0;s:7:"message";}',
                        'modeditor' => '-'),
                array('modname'   => 'pnhelp',
                        'modfuncs'  => 'a:1:{i:0;s:4:"edit";}',
                        'modareas'  => 'a:1:{i:0;s:4:"text";}',
                        'modeditor' => '-'),
                array('modname'   => 'pnMessages',
                        'modfuncs'  => 'a:2:{i:0;s:5:"newpm";i:1;s:10:"replyinbox";}',
                        'modareas'  => 'a:1:{i:0;s:7:"message";}',
                        'modeditor' => '-'),
                array('modname'   => 'pnWebLog',
                        'modfuncs'  => 'a:2:{i:0;s:10:"addposting";i:1;s:7:"addpage";}',
                        'modareas'  => 'a:1:{i:0;s:9:"xinhatext";}',
                        'modeditor' => '-'),
                array('modname'   => 'Profile',
                        'modfuncs'  => 'a:1:{i:0;s:6:"modify";}',
                        'modareas'  => 'a:3:{i:0;s:14:"prop_signature";i:1;s:14:"prop_extrainfo";i:2;s:15:"prop_yinterests";}',
                        'modeditor' => '-'),
                array('modname'   => 'PostCalendar',
                        'modfuncs'  => 'a:1:{i:0;s:3:"all";}',
                        'modareas'  => 'a:1:{i:0;s:11:"description";}',
                        'modeditor' => '-'),
                array('modname'   => 'Reviews',
                        'modfuncs'  => 'a:2:{i:0;s:3:"new";i:1;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:14:"reviews_review";}',
                        'modeditor' => '-'),
                array('modname'   => 'ShoppingCart',
                        'modfuncs'  => 'a:1:{i:0;s:3:"all";}',
                        'modareas'  => 'a:1:{i:0;s:11:"description";}',
                        'modeditor' => '-'),
                array('modname'   => 'tFAQ',
                        'modfuncs'  => 'a:2:{i:0;s:4:"view";i:1;s:6:"modify";}',
                        'modareas'  => 'a:1:{i:0;s:8:"tfanswer";}',
                        'modeditor' => '-'),
                array('modname'   => 'Web_Links',
                        'modfuncs'  => 'a:3:{i:0;s:8:"linkview";i:1;s:7:"addlink";i:2;s:17:"modifylinkrequest";}',
                        'modareas'  => 'a:1:{i:0;s:11:"description";}',
                        'modeditor' => '-'));
        DBUtil::insertObjectArray($record, 'scribite', 'mid');

    }

}