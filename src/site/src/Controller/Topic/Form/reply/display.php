<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Form\Reply;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\KunenaPrivate\Message;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use function defined;

/**
 * Class ComponentTopicControllerFormReplyDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentTopicControllerFormReplyDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $captchaHtml = null;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Topic/Edit';
	private $headerText;
	private $topic;
	private $me;
	/**
	 * @var mixed
	 * @since version
	 */
	private $message;

	/**
	 * Prepare topic reply form.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();

		$id    = $this->input->getInt('id');
		$mesid = $this->input->getInt('mesid');
		$quote = $this->input->getBool('quote', false);

		$saved = $this->app->getUserState('com_kunena.postfields');

		$this->me = KunenaUserHelper::getMyself();
		$template = KunenaFactory::getTemplate();

		if (!$mesid)
		{
			$this->topic = KunenaTopicHelper::get($id);
			$parent      = KunenaMessageHelper::get($this->topic->first_post_id);
		}
		else
		{
			$parent      = KunenaMessageHelper::get($mesid);
			$this->topic = $parent->getTopic();
		}

		if ($this->config->readOnly)
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		$doc = Factory::getApplication()->getDocument();

		foreach ($doc->_links as $key => $value)
		{
			if (is_array($value))
			{
				if (array_key_exists('relation', $value))
				{
					if ($value['relation'] == 'canonical')
					{
						$canonicalUrl               = $this->topic->getUrl();
						$doc->_links[$canonicalUrl] = $value;
						unset($doc->_links[$key]);
						break;
					}
				}
			}
		}

		$uri = trim(strtok($this->topic->getUrl(), '?'));
		$doc->addHeadLink($uri, 'canonical');

		$category = $this->topic->getCategory();

		if ($parent->isAuthorised('reply') && $this->me->canDoCaptcha())
		{
			$captchaDisplay = KunenaTemplate::getInstance()->recaptcha();
			$captchaEnabled = true;
		}
		else
		{
			$captchaEnabled = false;
		}

		$parent->tryAuthorise('reply');

		$arraypollcatid = [];
		KunenaTemplate::getInstance()->addScriptOptions('com_kunena.pollcategoriesid', json_encode($arraypollcatid));

		// Run event.
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'reply');

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.topic', &$this->topic, &$params, 0]);

		$this->headerText = Text::_('COM_KUNENA_BUTTON_MESSAGE_REPLY') . ': ' . $this->topic->subject;

		// Can user edit topic icons?
		if ($this->config->topicIcons && $this->topic->isAuthorised('edit'))
		{
			$topicIcons = $template->getTopicIcons(false, $saved ? $saved['icon_id'] : $this->topic->icon_id);
		}

		list($this->topic, $this->message) = $parent->newReply($saved ? $saved : ['quote' => $quote]);
		$action = 'post';

		$privateMessage       = new KunenaMessage;
		$privateMessage->body = $saved ? $saved['private'] : $privateMessage->body;

		$allowedExtensions = KunenaAttachmentHelper::getExtensions($category);

		$postAnonymous       = $saved ? $saved['anonymous'] : !empty($category->postAnonymous);
		$subscriptionsChecked = $saved ? $saved['subscribe'] : $this->config->subscriptionsChecked == 1;
		$this->app->setUserState('com_kunena.postfields', null);

		$canSubscribe = $this->canSubscribe();
	}

	/**
	 * Can user subscribe to the topic?
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	protected function canSubscribe()
	{
		if (!$this->me->userid || !$this->config->allowSubscriptions
			|| $this->config->topicSubscriptions == 'disabled'
		)
		{
			return false;
		}

		return !$this->topic->getUserTopic()->subscribed;
	}

	/**
	 * Prepare document.
	 *
	 * @return  void|boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareDocument(): bool
	{
		$menu_item = $this->app->getMenu()->getActive();

		$this->setMetaData('robots', 'nofollow, noindex');

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle($this->headerText);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$this->setDescription($this->headerText);
			}
		}
	}
}