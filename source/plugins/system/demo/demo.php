<?php
/**
 * Joomla! System plugin for Demo Sites
 *
 * @author    Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license   GNU Public License
 * @link      http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import the parent class
jimport('joomla.plugin.plugin');

/**
 * Demo System Plugin
 */
class PlgSystemDemo extends JPlugin
{
	/**
	 * Event onAfterRoute
	 */
	public function onAfterRoute()
	{
		header('X-Robots-Tag: demo, noindex, nofollow', true);
		
		$document = JFactory::getDocument();
		$document->setMetaData('robots', 'demo,noindex,nofollow');
	}

	/**
	 * Event onAfterRender
	 */
	public function onAfterRender()
	{
		// Determine whether or to allow this or not
		if ($this->allowDemoNotice() == false)
		{
			return false;
		}

		// Add the message
		if ($this->params->get('show_message', 1) == 1)
		{
			// Get the body and fetch a list of files
			$body = JResponse::getBody();

			// Add the script declaration
			$body = str_replace('</head>', '<link rel="stylesheet" type="text/css" href="' . $this->getStylesheet() . '" /></head>', $body);
			$body = preg_replace('/\<body([^\>]{0,})\>/', "\\0\n" . $this->getDemoBox(), $body);

			// Double-check the robots-tag
			$body = preg_replace('/\<meta\ name=\"robots\"\ content=\"([^\"]+)\"\ \/\>/', '<meta name="robots" content="demo,noindex,nofollow" />', $body);

			JResponse::setBody($body);
		}
	}

	/**
	 * Helper method to get the HTML of the demo-box
	 *
	 * @return string
	 */
	protected function getDemoBox()
	{
		$text = $this->params->get('message_text', 'This is a demo site');

		return "<div id=\"demobox\"><div>$text</div></div>";
	}

	/**
	 * Get a stylesheet
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	private function getStylesheet($file = 'default.css')
	{
		$application = JFactory::getApplication();
		$folder = 'media/plg_demo/css/';

		// Check for overrides
		$template = $application->getTemplate();

		if (file_exists(JPATH_SITE . '/templates/' . $template . '/html/plg_demo/css/' . $file))
		{
			$folder = 'templates/' . $template . '/html/plg_demo/css/';
		}

		return JURI::root() . $folder . '/' . $file;
	}

	/**
	 * Load the parameters
	 *
	 * @return JParameter
	 */
	protected function getParams()
	{
		return $this->params;
	}

	/**
	 * Check whether it is allowed to show the demo notice
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function allowDemoNotice()
	{
		$application = JFactory::getApplication();

		if ($application->isSite() && $this->params->get('frontend', 1) == 0)
		{
			return false;
		}

		if ($application->isAdmin() && $this->params->get('backend', 1) == 0)
		{
			return false;
		}

		// Don't do anything if format=raw or tmpl=component
		$format = $application->input->getCmd('format');
		$tmpl = $application->input->getCmd('tmpl');

		if ($format == 'raw' || $tmpl == 'component')
		{
			return false;
		}

		// Check for AJAX calls
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			return false;
		}

		return true;
	}
}
