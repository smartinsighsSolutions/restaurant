<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjOfferProductModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'offers_products';
	
	protected $schema = array(
		array('name' => 'offer_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'product_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'size_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjOfferProductModel($attr);
	}
}
?>