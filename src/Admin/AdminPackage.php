<?php
/**
 * Part of Admin project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Admin;

use Lyrasoft\Luna\Helper\LunaHelper;
use Phoenix\Asset\Asset;
use Phoenix\DataMapper\DataMapperResolver;
use Phoenix\Language\TranslatorHelper;
use Phoenix\Record\RecordResolver;
use Phoenix\Script\BootstrapScript;
use Phoenix\Uri\Uri;
use Symfony\Component\Yaml\Yaml;
use Windwalker\Core\Package\AbstractPackage;
use Windwalker\Debugger\Helper\DebuggerHelper;
use Windwalker\Event\Dispatcher;
use Windwalker\Filesystem\File;
use Windwalker\Filesystem\Folder;
use Windwalker\Form\FieldHelper;
use Windwalker\Form\ValidatorHelper;
use Windwalker\Warder\Helper\UserHelper;
use Windwalker\Warder\Helper\WarderHelper;

if (!defined('ADMIN_ROOT'))
{
	define('ADMIN_ROOT', __DIR__);
}

/**
 * The AdminPackage class.
 *
 * @since  1.0
 */
class AdminPackage extends AbstractPackage
{
	/**
	 * initialise
	 *
	 * @throws  \LogicException
	 * @return  void
	 */
	public function initialise()
	{
		// Prepare Resolvers
		RecordResolver::addNamespace(__NAMESPACE__ . '\Record');
		DataMapperResolver::addNamespace(__NAMESPACE__ . '\DataMapper');
		FieldHelper::addNamespace(__NAMESPACE__ . '\Field');
		ValidatorHelper::addNamespace(__NAMESPACE__ . 'Validator');

		parent::initialise();
	}

	/**
	 * prepareExecute
	 *
	 * @return  void
	 */
	protected function prepareExecute()
	{
		$this->checkAccess();

		// Assets
		BootstrapScript::css();
		BootstrapScript::script();
		Asset::addStyle('admin/css/admin.css');

		// Language
		TranslatorHelper::loadAll($this, 'ini');
	}

	/**
	 * checkAccess
	 *
	 * @return  void
	 */
	protected function checkAccess()
	{
		if (!UserHelper::authorise() /* && User::get()->group == 2 */)
		{
			UserHelper::goToLogin(Uri::full());
		}
	}

	/**
	 * postExecute
	 *
	 * @param string $result
	 *
	 * @return  string
	 */
	protected function postExecute($result = null)
	{
		if (WINDWALKER_DEBUG)
		{
			if (class_exists('Windwalker\Debugger\Helper\DebuggerHelper'))
			{
				DebuggerHelper::addCustomData('Language Orphans', '<pre>' . TranslatorHelper::getFormattedOrphans() . '</pre>');
			}

			// Un comment this line, Translator will export all orphans to /cache/language
			if ($this->app->get('language.debug'))
			{
				TranslatorHelper::dumpOrphans('ini');
			}
		}

		return $result;
	}

	/**
	 * registerListeners
	 *
	 * @param Dispatcher $dispatcher
	 *
	 * @return  void
	 */
	public function registerListeners(Dispatcher $dispatcher)
	{
		parent::registerListeners($dispatcher);
	}

	/**
	 * loadRouting
	 *
	 * @return  mixed
	 */
	public function loadRouting()
	{
		$routes = parent::loadRouting();

		foreach (Folder::files(__DIR__ . '/Resources/routing') as $file)
		{
			if (File::getExtension($file) == 'yml')
			{
				$routes = array_merge($routes, Yaml::parse(file_get_contents($file)));
			}
		}

		// Merge other routes here...
		$routes = array_merge($routes, WarderHelper::getAdminRouting());
		$routes = array_merge($routes, LunaHelper::getAdminRouting());

		return $routes;
	}
}
