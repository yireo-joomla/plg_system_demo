<?php
/**
 * Joomla! System plugin for Demo Sites
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the parent class
jimport( 'joomla.plugin.plugin' );

/**
 * Demo System Plugin
 */
class plgSystemDemo extends JPlugin
{
    /**
     * Event onAfterRoute
     *
     * @access public
     * @param null
     * @return null
     */
    public function onAfterRoute()
    {
        header('X-Robots-Tag: demo, noindex, nofollow', true);
        $document = JFactory::getDocument();
        $document->setMetaData('robots','demo,noindex,nofollow');
    }

    /**
     * Event onAfterRender
     *
     * @access public
     * @param null
     * @return null
     */
    public function onAfterRender()
    {
        // Determine whether to use in the frontend or backend
        $application = JFactory::getApplication();
        if($application->isSite() && $this->getParams()->get('frontend', 1) == 0) return false;
        if($application->isAdmin() && $this->getParams()->get('backend', 1) == 0) return false;
        if(JRequest::getCmd('tmpl') == 'component') return false;

        // Add the message
        if($this->getParams()->get('show_message', 1) == 1) {

            // Get the body and fetch a list of files
            $body = JResponse::getBody();

            // Add the script declaration
            $body = str_replace('</head>', '<link rel="stylesheet" type="text/css" href="'.$this->getStylesheet().'" /></head>', $body);
            $body = preg_replace('/\<body([^\>]{0,})\>/', "\\0\n".$this->getDemoBox(), $body);

            // Double-check the robots-tag
            $body = preg_replace('/\<meta\ name=\"robots\"\ content=\"([^\"]+)\"\ \/\>/', '<meta name="robots" content="demo,noindex,nofollow" />', $body); 

            JResponse::setBody($body);
        }
    }

    /**
     * Helper method to get the HTML of the demo-box
     *
     * @param null
     * @return string
     */
    protected function getDemoBox()
    {
        $text = $this->getParams()->get('message_text', 'This is a demo site');
        return "<div id=\"demobox\"><div>$text</div></div>";
    }

    /**
     * Get a stylesheet
     *
     * @access private
     * @param null
     * @return null
     */
    private function getStylesheet($file = 'default.css')
    {
        $folder = 'media/plg_demo/css/';

        // Check for overrides
        $template = JFactory::getApplication()->getTemplate();
        if(file_exists(JPATH_SITE.'/templates/'.$template.'/html/plg_demo/css/'.$file)) {
            $folder = 'templates/'.$template.'/html/plg_demo/css/';
        }

        return JURI::root().$folder.'/'.$file;
    }

    /**
     * Load the parameters
     *
     * @access private
     * @param null
     * @return JParameter
     */
    private function getParams()
    {
        return $this->params;
    }
}
