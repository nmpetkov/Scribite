<?php

/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license MIT
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */
class ModulePlugin_Scribite_CodeMirror_Plugin extends Scribite_PluginHandler_AbstractPlugin
{
    /**
     * Provide plugin meta data.
     *
     * @return array Meta data.
     */
    protected function getMeta()
    {
        return array(
            'displayname' => $this->__('CodeMirror'),
            'description' => $this->__('CodeMirror editor.'),
            'version' => '5.17.0',
            'url' => 'http://codemirror.net/',
            'license' => 'custom',
        );
    }

    public function install()
    {
        ModUtil::setVars($this->serviceId, $this->getDefaults());

        return true;
    }

    public function uninstall()
    {
        ModUtil::delVar($this->serviceId);

        return true;
    }

    public static function getOptions()
    {
        return array(
            'editorModeItems' => self::getEditorModes(),
            'themesItems' => self::getThemes()
        );
    }

    // read editor modes from codemirror and load names into array
    private static function getEditorModes()
    {
        $modes = array();
        $modesDirectory = opendir('modules/Scribite/plugins/CodeMirror/vendor/CodeMirror/mode');
        while (false !== ($f = readdir($modesDirectory))) {
            if (in_array($f, array('.', '..')) || preg_match('/[.]/', $f)) {
                continue;
            }
            $modes[] = array(
                'text' => $f,
                'value' => $f
            );
        }

        closedir($modesDirectory);
        usort($modes, function ($a, $b) {
            return strcmp(strtolower($a['text']), strtolower($b['text']));
        });

        return $modes;
    }

    // read themes from codemirror and load names into array
    private static function getThemes()
    {
        $themes = array();
        $themesDirectory = opendir('modules/Scribite/plugins/CodeMirror/vendor/CodeMirror/theme');
        while (false !== ($f = readdir($themesDirectory))) {
            if (in_array($f, array('.', '..')) || !preg_match('/[.]/', $f)) {
                continue;
            }
            $f = str_replace('.css', '', $f);
            $themes[] = array(
                'text' => $f,
                'value' => $f
            );
        }

        closedir($themesDirectory);
        usort($themes, function ($a, $b) {
            return strcmp(strtolower($a['text']), strtolower($b['text']));
        });

        return $themes;
    }

    public static function getDefaults()
    {
        return array(
            'showLineNumbers' => true,
            'lineWrapping' => true,
            'editorMode' => 'htmlmixed',
            'themes' => array('default')
        );
    }
}
