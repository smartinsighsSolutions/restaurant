<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCategoryModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'categories';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'parent_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'image', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'order', 'type' => 'int', 'default' => ':NULL')
	);
	
	public $i18n = array('name', 'description');
	
	public static function factory($attr=array())
	{
		return new pjCategoryModel($attr);
	}
	
	public function getLastOrder($parent_id)
	{
		$order = 1;
		$this->reset();
		if($parent_id == ':NULL')
		{
			$this->where("t1.parent_id IS NULL");
		}else{
			$this->where("t1.parent_id", $parent_id);
		}
		$arr = $this
			->orderBy("`order` DESC")
			->limit(1)
			->findAll()
			->getData();
		if(!empty($arr))
		{
			$order = $arr[0]['order'] + 1;
		}else{
			$_arr = $this->reset()->find($parent_id)->getData();
			$order = $_arr['order'] + 1;
		}
		return $order;
	}
}
?>