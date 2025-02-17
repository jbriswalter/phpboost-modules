<?php
/**
 * @copyright   &copy; 2005-2025 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 07 17
 * @since       PHPBoost 3.0 - 2012 11 15
 * @contributor mipel <mipel@phpboost.com>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class DictionarySetup extends DefaultModuleSetup
{
	private $messages;
	private $querier;
	public static $dictionary_table;
	public static $dictionary_cat_table;

	public static function __static()
	{
		self::$dictionary_table = PREFIX . 'dictionary';
		self::$dictionary_cat_table = PREFIX . 'dictionary_cat';
	}

	public function __construct()
	{
		$this->querier = PersistenceContext::get_querier();
	}

	public function install()
	{
		$this->drop_tables();
		$this->create_tables();
	}

	public function upgrade($installed_version)
	{
		$columns = PersistenceContext::get_dbms_utils()->desc_table(PREFIX . 'dictionary');

		if ($columns['word']['key'] == 'word')
		{
			PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'dictionary ADD FULLTEXT KEY `title` (`word`)');
			PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'dictionary DROP KEY `word`');
		}
		if ($columns['description']['key'] == 'description')
		{
			PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'dictionary ADD FULLTEXT KEY `contents` (`description`)');
			PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'dictionary DROP KEY `description`');
		}

		//Delete old files
		$file = new File(Url::to_rel('/dictionary/admin_dictionary.php'));
		$file->delete();
		$file = new File(Url::to_rel('/dictionary/dictionary.inc.php'));
		$file->delete();
		$file = new File(Url::to_rel('/dictionary/dictionary_begin.php'));
		$file->delete();
		$file = new File(Url::to_rel('/dictionary/templates/admin_dictionary.tpl'));
		$file->delete();
		$file = new File(Url::to_rel('/dictionary/templates/dictionary_search_form.tpl'));
		$file->delete();

		return '6.0.0';
	}

	public function uninstall()
	{
		$this->drop_tables();
		$this->delete_configuration();
	}

	private function drop_tables()
	{
		PersistenceContext::get_dbms_utils()->drop(array(self::$dictionary_table, self::$dictionary_cat_table));
	}

	private function delete_configuration()
	{
		ConfigManager::delete('dictionary');
	}

	private function create_tables()
	{
		$this->create_dictionary_table();
		$this->create_dictionary_cat_table();
		$this->insert_data();
	}

	private function create_dictionary_table()
	{
		$fields = array(
			'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'word' => array('type' => 'string', 'length' => 255, 'default' => "''"),
			'cat' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'description' => array('type' => 'text', 'length' => 65000, 'notnull' => 1),
			'approved' => array('type' => 'integer', 'length' => 1, 'default' => 0),
			'user_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'timestamp' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0)
		);
		$options = array(
			'primary' => array('id'),
			'indexes' => array(
				'description' => array('type' => 'fulltext', 'fields' => 'description'),
				'word' => array('type' => 'fulltext', 'fields' => 'word')
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$dictionary_table, $fields, $options);
	}

	private function create_dictionary_cat_table()
	{
		$fields = array(
			'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'name' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'images' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''")
		);
		$options = array(
			'primary' => array('id')
		);

		PersistenceContext::get_dbms_utils()->create_table(self::$dictionary_cat_table, $fields, $options);
	}

	private function insert_data()
	{
		$this->messages = LangLoader::get('install', 'dictionary');
		$this->insert_dictionary_cat_data();
		$this->insert_dictionary_data();
	}

	private function insert_dictionary_cat_data()
	{
		$this->querier->insert(self::$dictionary_cat_table, array(
			'id' => 1,
			'name' => $this->messages['category.1.name'],
			'images' => ''
		));
		$this->querier->insert(self::$dictionary_cat_table, array(
			'id' => 2,
			'name' => $this->messages['category.2.name'],
			'images' => ''
		));
		$this->querier->insert(self::$dictionary_cat_table, array(
			'id' => 3,
			'name' => $this->messages['category.3.name'],
			'images' => ''
		));
	}

	private function insert_dictionary_data()
	{
		$this->querier->insert(self::$dictionary_table, array(
			'id' => 1,
			'word' => $this->messages['word.1.name'],
			'cat' => 1,
			'description' => $this->messages['word.1.description'],
			'approved' => 1,
			'user_id' => 1,
			'timestamp' => time()
		));
		$this->querier->insert(self::$dictionary_table, array(
			'id' => 2,
			'word' => $this->messages['word.2.name'],
			'cat' => 1,
			'description' => $this->messages['word.2.description'],
			'approved' => 1,
			'user_id' => 1,
			'timestamp' => time()
		));
		$this->querier->insert(self::$dictionary_table, array(
			'id' => 3,
			'word' => $this->messages['word.3.name'],
			'cat' => 2,
			'description' => $this->messages['word.3.description'],
			'approved' => 1,
			'user_id' => 1,
			'timestamp' => time()
		));
		$this->querier->insert(self::$dictionary_table, array(
			'id' => 4,
			'word' => $this->messages['word.4.name'],
			'cat' => 2,
			'description' => $this->messages['word.4.description'],
			'approved' => 1,
			'user_id' => 1,
			'timestamp' => time()
		));
		$this->querier->insert(self::$dictionary_table, array(
			'id' => 5,
			'word' => $this->messages['word.5.name'],
			'cat' => 3,
			'description' => $this->messages['word.5.description'],
			'approved' => 1,
			'user_id' => 1,
			'timestamp' => time()
		));
	}
}
?>
