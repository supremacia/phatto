<?php

/**
 * ATT: under construction...
 *
 *
 *  edited: 
 *	2018/02/10 03:41:00
 * 
 */

namespace Phatto;
use Lib\Database;

class Model
{
	private $db;
	private $table;


	function __construct($table)
	{
		$this->db = new Database;
		$this->table = $this->db->load($table);

		$this->table->find(36);



		e('-- stop');

		$this->table->delete();
		$this->table->unDelete();


		e($this->table);
		
		$this->table->set('user', 2)
					->set('company', 1)
					->set('source', 'user')
					->set('msg', 'Testando insert')
					->save();

		echo '<h2>Fazendo UpDate</h2>';

		$this->table->set('msg', 'Mudei para testando update...')
					->save();

		e($this->table);
	}
}