<?php
/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\User\Model;

use Windwalker\Core\Model\DatabaseModel;
use Windwalker\Form\Form;
use Windwalker\User\Form\ResetFieldDefinition;

/**
 * The ResetModel class.
 *
 * @since  {DEPLOY_VERSION}
 */
class ResetModel extends DatabaseModel
{
	/**
	 * getForm
	 *
	 * @return  Form
	 */
	public function getForm()
	{
		return $this->fetch('reset.form', function()
		{
			$form = new Form;

			$form->defineFormFields(new ResetFieldDefinition);

			return $form;
		});
	}
}
