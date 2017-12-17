<?php

namespace GHank\WSNotice\lib;

use \swoole_table;
use GHank\WSNotice\SL;

class SwooleModel
{
	protected $tableName;
	protected $where = [];

	public function where($key, $op, $value)
	{
		$this->where[] = [
			'key' => $key,
			'op' => $op,
			'value' => $value
		];

		return $this;
	}

	public function select()
	{
		$dataArr = [];
		$tableName = $this->tableName . 'Table';
		$table = isset(SL::$app->$tableName) ? SL::$app->$tableName : '';

		if ($table instanceof swoole_table) {
			foreach (SL::$app->server->connections as $fd) {
				$whereFlag = true;
				$data = $table->get($fd);

				foreach ($this->where as $w) {
					if (!isset($data[$w['key']])) {
						$whereFlag = false;
						break;
					}

					switch ($w['op']) {
						case '=':
						case 'eq':
						case 'EQ':
							if ($data[$w['key']] != $w['value']) {
								$whereFlag = false;
								break;
							}
							break;
						case '>':
						case 'rt':
						case 'RT':
							if ($data[$w['key']] <= $w['value']) {
								$whereFlag = false;
								break;
							}
							break;
						case '<':
						case 'lt':
						case 'LT':
							if ($data[$w['key']] >= $w['value']) {
								$whereFlag = false;
								break;
							}
							break;
						default:
							break;
					}
				}

				if (!$whereFlag) {
					continue;
				}

				$dataArr[] = $data;
			}
		}

		return $dataArr;
	}

	public function find()
	{
		$data = $this->select();
		if (count($data) > 0) {
			return $data[0];
		}

		return [];
	}
}