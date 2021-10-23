<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 06 16
 * @since       PHPBoost 4.0 - 2013 08 04
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class AdminServerStatusServersListController extends AdminController
{
	private $lang;
	private $view;
	private $config;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		if ($request->get_value('regenerate_status', false))
		{
			ServerStatusService::check_servers_status(true);
			$this->view->put('MESSAGE_HELPER', MessageHelper::display(LangLoader::get_message('warning.process.success', 'warning-lang'), MessageHelper::SUCCESS, 5));
		}

		$this->update_servers($request);

		$servers_number = 0;
		foreach ($this->config->get_servers_list() as $id => $server)
		{
			$this->view->assign_block_vars('servers', array(
				'C_ICON'    => $server->has_medium_icon(),
				'C_DISPLAY' => $server->is_displayed(),
				'ID'        => $id,
				'NAME'      => $server->get_name(),
				'ICON'      => $server->get_medium_icon(),
				'U_EDIT'    => ServerStatusUrlBuilder::edit_server($id)->rel()
			));
			$servers_number++;
		}

		$this->view->put_all(array(
			'C_SERVERS' => $servers_number,
			'C_SEVERAL_SERVERS' => $servers_number > 1
		));

		return new AdminServerStatusDisplayResponse($this->view, $this->lang['server.management']);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'ServerStatus');
		$this->view = new FileTemplate('ServerStatus/AdminServerStatusServersListController.tpl');
		$this->view->add_lang(array_merge(
			$this->lang,
			LangLoader::get('common-lang'),
			LangLoader::get('form-lang')
		));
		$this->config = ServerStatusConfig::load();
	}

	private function update_servers(HTTPRequestCustom $request)
	{
		if ($request->get_value('submit', false))
		{
			$this->update_position($request);
			$this->view->put('MESSAGE_HELPER', MessageHelper::display(LangLoader::get_message('warning.success.position.update', 'warning-lang'), MessageHelper::SUCCESS, 5));
		}
	}

	private function update_position(HTTPRequestCustom $request)
	{
		$servers = $this->config->get_servers_list();
		$sorted_servers_list = array();

		$servers_list = json_decode(TextHelper::html_entity_decode($request->get_value('tree')));
		foreach($servers_list as $position => $tree)
		{
			$sorted_servers_list[$position + 1] = $servers[$tree->id];
		}
		$this->config->set_servers_list($sorted_servers_list);

		ServerStatusConfig::save();
	}
}
?>
