<?php
/*##################################################
 *                       AdminServerStatusServerFormController.class.php
 *                            -------------------
 *   begin                : August 4, 2013
 *   copyright            : (C) 2013 Julien BRISWALTER
 *   email                : j1.seth@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

class AdminServerStatusServerFormController extends AdminController
{
	private $tpl;
	
	private $lang;
	/**
	 * @var HTMLForm
	 */
	private $form;
	/**
	 * @var FormButtonDefaultSubmit
	 */
	private $submit_button;
	
	private $config;
	
	private $server;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init($request);
		$this->build_form();
		
		$this->tpl = new StringTemplate('# INCLUDE MSG ## INCLUDE FORM #');
		$this->tpl->add_lang($this->lang);
		
		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			if ($this->save())
				AppContext::get_response()->redirect(ServerStatusUrlBuilder::servers_management());
			else
				$this->tpl->put('MSG', MessageHelper::display($this->lang['message.empty_address'], MessageHelper::ERROR));
		}
		
		$this->tpl->put('FORM', $this->form->display());
		
		return new AdminServerStatusDisplayResponse($this->tpl, !empty($this->id) ? $this->lang['admin.config.servers.title.edit_server'] : $this->lang['admin.config.servers.title.add_server']);
	}
	
	private function init(HTTPRequestCustom $request)
	{
		$this->lang = LangLoader::get('common', 'ServerStatus');
		$this->config = ServerStatusConfig::load();
		$this->id = $request->get_getint('id', 0);
	}
	
	private function build_form()
	{
		$server = $this->get_server();
		$main_lang = LangLoader::get('main');
		
		$form = new HTMLForm(__CLASS__);
		
		$fieldset = new FormFieldsetHTML('server', !empty($this->id) ? $this->lang['admin.config.servers.title.edit_server'] : $this->lang['admin.config.servers.title.add_server']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldTextEditor('name', $this->lang['server.name'], $server->get_name(), array(
			'class' => 'text', 'required' => true)
		));
		
		$fieldset->add_field(new FormFieldRichTextEditor('description', $this->lang['server.description'], $server->get_description(), array(
			'rows' => 4, 'cols' => 47)
		));
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('address_type', $this->lang['server.address_type'], $server->get_address_type(),
			array(
				new FormFieldSelectChoiceOption($this->lang['server.address_type.dns'], AbstractServerStatusServer::DNS),
				new FormFieldSelectChoiceOption($this->lang['server.address_type.ip'], AbstractServerStatusServer::IP)
			),
			array('events' => array('change' => '
				if (HTMLForms.getField("address_type").getValue() == \'' . AbstractServerStatusServer::DNS . '\') {
					HTMLForms.getField("ip_address").disable();
					HTMLForms.getField("dns_address").enable();
				} else {
					HTMLForms.getField("dns_address").disable();
					HTMLForms.getField("ip_address").enable();
				}'
			))
		));
		
		$fieldset->add_field(new FormFieldTextEditor('dns_address', $this->lang['server.address.dns'], $server->address_type_is_dns() ? $server->get_address() : '', array(
			'class' => 'text', 'description' => $this->lang['server.address.dns.explain'], 'hidden' => !$server->address_type_is_dns())
		));
		
		$fieldset->add_field(new FormFieldTextEditor('ip_address', $this->lang['server.address.ip'], $server->address_type_is_ip() ? $server->get_address() : '', array(
			'class' => 'text', 'description' => $this->lang['server.address.ip.explain'], 'hidden' => !$server->address_type_is_ip()),
			array(new FormFieldConstraintRegex('`^((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))$|^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((b((25[0-5])|(1d{2})|(2[0-4]d)|(d{1,2}))b).){3}(b((25[0-5])|(1d{2})|(2[0-4]d)|(d{1,2}))b))|(([0-9A-Fa-f]{1,4}:){0,5}:((b((25[0-5])|(1d{2})|(2[0-4]d)|(d{1,2}))b).){3}(b((25[0-5])|(1d{2})|(2[0-4]d)|(d{1,2}))b))|(::([0-9A-Fa-f]{1,4}:){0,5}((b((25[0-5])|(1d{2})|(2[0-4]d)|(d{1,2}))b).){3}(b((25[0-5])|(1d{2})|(2[0-4]d)|(d{1,2}))b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$`iu'))
		));
		
		$types_properties = $this->get_types_properties();
		$fieldset->add_field(new FormFieldSimpleSelectChoice('type', $this->lang['server.type'], $server->get_type(),
			$types_properties['array_select'],
			array('events' => array('change' => $types_properties['events']))
		));
		
		$fieldset->add_field(new FormFieldFree('preview_icon', $this->lang['server.icon'], '<img id="preview_icon" ' . ($server->has_medium_icon() ? 'src="' . $server->get_medium_icon() . '"' : 'style="display:none"') . ' alt="' . $this->lang['server.icon'] . '" title="' . $this->lang['server.icon'] . '" /><span id="preview_icon_none" ' . ($server->has_medium_icon() ? 'style="display:none"' : '') . '>' . $this->lang['server.icon.none_e'] . '</span>'));
		
		$fieldset->add_field(new FormFieldTextEditor('port', $this->lang['server.port'], $server->get_port(), array(
			'class' => 'text', 'maxlength' => 5, 'size' => 5, 'description' => $this->lang['server.port.explain'], 'required' => true),
			array(new FormFieldConstraintIntegerRange(1, 65535))
		));
		
		$fieldset->add_field(new FormFieldCheckbox('display', $this->lang['server.display'], $server->is_displayed()));
		
		$auth_settings = new AuthorizationsSettings(array(
			new ActionAuthorization($this->lang['admin.authorizations.display_server'], AbstractServerStatusServer::DISPLAY_SERVER_AUTHORIZATIONS),
		));
		
		$auth_settings->build_from_auth_array($server->get_authorizations());
		$fieldset->add_field(new FormFieldAuthorizationsSetter('authorizations', $auth_settings));
		
		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());
		
		$this->form = $form;
	}
	
	private function get_server()
	{
		if ($this->server === null)
		{
			$servers_list = $this->config->get_servers_list();
			
			if (!empty($this->id) && isset($servers_list[$this->id]))
			{
				$this->server = $servers_list[$this->id];
			}
			else
			{
				$this->server = new ServerStatusDefaultServer();
				$this->server->set_port($this->server->get_default_port());
			}
		}
		return $this->server;
	}
	
	private function get_types_properties()
	{
		$array_select = array(new FormFieldSelectChoiceOption('', 'ServerStatusDefaultServerType'));
		$events = 'if (HTMLForms.getField("type").getValue() == "ServerStatusDefaultServerType") {
				HTMLForms.getField("port").setValue(\'0\');
				jQuery(\'#preview_icon\').attr(\'src\', \'\');
				jQuery(\'#preview_icon\').hide();
				jQuery(\'#preview_icon_none\').show();
			}';
		
		$types = ServerStatusService::get_types();
		$types_number = count($types);
		
		foreach ($types as $type_id => $type)
		{
			$array_options = array();
			foreach ($type as $id => $options)
			{
				if ($types_number > 1)
					$array_options[] = new FormFieldSelectChoiceOption($options['name'], $id);
				else
					$array_select[] = new FormFieldSelectChoiceOption($options['name'], $id);
				
				$events .= 'if (HTMLForms.getField("type").getValue() == "' . $id . '") {
					HTMLForms.getField("port").setValue(\'' . $options['default_port'] . '\');
					' . ($options['icon'] ? 'jQuery(\'#preview_icon\').attr(\'src\', \'' . $options['icon'] . '\');
					jQuery(\'#preview_icon\').show();
					jQuery(\'#preview_icon_none\').hide();' : 'jQuery(\'#preview_icon\').attr(\'src\', \'\');
					jQuery(\'#preview_icon\').hide();
					jQuery(\'#preview_icon_none\').show();') . '
				}';
			}
			
			if ($types_number > 1)
				$array_select[] = new FormFieldSelectChoiceGroupOption($this->lang['server.' . $type_id], $array_options);
		}
		
		return array('array_select' => $array_select, 'events' => $events);
	}
	
	private function save()
	{
		$address_type = $this->form->get_value('address_type')->get_raw_value();
		$address = $this->form->get_value($address_type . '_address');
		
		if (empty($address))
			return false;
		
		$server = $this->get_server();
		
		$type = $this->form->get_value('type')->get_raw_value();
		if ($type != $server->get_type())
			$server = new $type();
		
		$server->set_name($this->form->get_value('name'));
		$server->set_rewrited_name($this->form->get_value('name'));
		$server->set_description($this->form->get_value('description'));
		
		$address_type = $this->form->get_value('address_type')->get_raw_value();
		$server->set_address_type($address_type);
		$server->set_address($address);
		$server->set_port($this->form->get_value('port'));
		
		if ((bool)$this->form->get_value('display'))
			$server->displayed();
		else
			$server->not_displayed();
		
		$server->set_authorizations($this->form->get_value('authorizations')->build_auth_array());
		$server->check_status(true);
		
		$servers_list = $this->config->get_servers_list();
		$servers_list[!empty($this->id) ? $this->id : sizeof($servers_list) + 1] = $server;
		
		$this->config->set_servers_list($servers_list);
		
		ServerStatusConfig::save();
		return true;
	}
}
?>
