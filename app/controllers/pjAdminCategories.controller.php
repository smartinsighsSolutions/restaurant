<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminCategories extends pjAdmin
{
	private $imageFillColor = array(255, 255, 255);
	
	private $imageSizes = array(372, 228);
	
	public function pjActionCheckCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && isset($_POST['locale']))
		{
			$locale = $_POST['locale'];
				
			$value = pjObject::escapeString($_POST['i18n'][$locale]['name']);
				
			$pjCategoryModel = pjCategoryModel::factory();
				
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$pjCategoryModel->where('t1.id !=', $_POST['id']);
			}
			$pjCategoryModel->where("t1.id IN(SELECT TL.foreign_id FROM `".pjMultiLangModel::factory()->getTable()."` AS TL WHERE TL.model='pjCategory' AND TL.field='name' AND TL.content = '".$value."' AND TL.locale='$locale')");
			echo $pjCategoryModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionIndex&err=ACT05");
			}
			if (isset($_POST['category_create']))
			{
				$pjCategoryModel = pjCategoryModel::factory();
				$data = array();
				$data['order'] = $pjCategoryModel->getLastOrder($_POST['parent_id']);
				
				$id = $pjCategoryModel->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				
				if ($id !== false && (int) $id > 0)
				{
					$err = 'ACT03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjCategory', 'data');
					}
					if (isset($_FILES['image']))
					{
						if($_FILES['image']['error'] == 0)
						{
							if(getimagesize($_FILES['image']["tmp_name"]) != false)
							{
								$Image = new pjImage();
								if ($Image->getErrorCode() !== 200)
								{
									$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
									if ($Image->load($_FILES['image']))
									{
										$resp = $Image->isConvertPossible();
										if ($resp['status'] === true)
										{
											$hash = md5(uniqid(rand(), true));
											$image_path = PJ_UPLOAD_PATH . 'categories/' . $id . '_' . $hash . '.' . $Image->getExtension();
																							
											$Image->loadImage($_FILES['image']["tmp_name"]);
											$Image->resizeSmart($this->imageSizes[0], $this->imageSizes[1]);
											$Image->saveImage($image_path);
					
											$pjCategoryModel->reset()->where('id', $id)->limit(1)->modifyAll(array('image'=>$image_path));
												
										}
									}
								}
							}else{
								$err = 'ACT09';
							}
						}else if($_FILES['image']['error'] != 4){
							$err = 'ACT09';
						}
					}
					
				} else {
					$err = 'ACT04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCategories&action=pjActionIndex&err=$err");
			} else {
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				
				$parent_arr = pjCategoryModel::factory()
					->select('t1.*, t2.content as name')
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->where('parent_id IS NULL')
					->findAll()
					->getData();
				
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				$this->set('parent_arr', $parent_arr);
		
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminCategories.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionDeleteCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjCategoryModel = pjCategoryModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();
			$arr = $pjCategoryModel->find($_GET['id'])->getData();
				
			if ($pjCategoryModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				if (file_exists(PJ_INSTALL_PATH . $arr['image'])) {
					@unlink(PJ_INSTALL_PATH . $arr['image']);
				}
				$pjMultiLangModel->where('model', 'pjCategory')->where('foreign_id', $_GET['id'])->eraseAll();
				
				$sub_arr = $pjCategoryModel
					->reset()
					->where('parent_id', $arr['id'])
					->findAll()
					->getData();
				foreach($sub_arr as $v)
				{
					if (file_exists(PJ_INSTALL_PATH . $v['image'])) {
						@unlink(PJ_INSTALL_PATH . $v['image']);
					}
					$pjCategoryModel->reset()->setAttributes(array('id' => $v['id']))->erase();
					$pjMultiLangModel->reset()->where('model', 'pjCategory')->where('foreign_id', $v['id'])->eraseAll();
				}
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteCategoryBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjCategoryModel = pjCategoryModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$arr = $pjCategoryModel->whereIn('id', $_POST['record'])->findAll()->getData();
				foreach($arr as $v)
				{
					if (file_exists(PJ_INSTALL_PATH . $v['image'])) {
						@unlink(PJ_INSTALL_PATH . $v['image']);
					}
				}
				$pjCategoryModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				$pjMultiLangModel->reset()->where('model', 'pjCategory')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				
				$sub_arr = $pjCategoryModel
					->reset()
					->whereIn('parent_id', $_POST['record'])
					->findAll()
					->getData();
				foreach($sub_arr as $v)
				{
					if (file_exists(PJ_INSTALL_PATH . $v['image'])) {
						@unlink(PJ_INSTALL_PATH . $v['image']);
					}
					$pjCategoryModel->reset()->setAttributes(array('id' => $v['id']))->erase();
					$pjMultiLangModel->reset()->where('model', 'pjCategory')->where('foreign_id', $v['id'])->eraseAll();
				}
			}
		}
		exit;
	}
	
	public function pjActionGetCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCategoryModel = pjCategoryModel::factory()
				->select("t1.id, t1.parent_id, t2.content as name, t1.order,
						(SELECT COUNT(*) FROM `".pjProductModel::factory()->getTable()."` AS TP WHERE TP.category_id=t1.id) AS cnt_products,
						(CASE
						    WHEN t1.parent_id=t1.id OR t1.parent_id IS NULL THEN t1.`order`
						    WHEN t1.parent_id<>t1.id THEN (SELECT t4.`order` FROM `".pjCategoryModel::factory()->getTable()."` AS `t4` WHERE t4.id=t1.parent_id)
						END) AS 'the_order'")
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCategory' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjCategoryModel->where('t2.content LIKE', "%$q%");
				$pjCategoryModel->orWhere('t3.content LIKE', "%$q%");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjCategoryModel->where('t1.status', $_GET['status']);
			}

			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			$pjCategoryModel->orderBy("the_order ASC, t1.order ASC, `$column` $direction");
			
			$total = count($pjCategoryModel->findAll()->getData());
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjCategoryModel
				->limit($rowCount, $offset)
				->findAll()
				->getData();
				
			$order_arr = array();
			foreach($data as $k => $v)
			{
				if(empty($v['parent_id']))
				{
					$order_arr['parent'][] = $v['order'];
				}else{
					$order_arr['child'][$v['parent_id']][] = $v['order'];
				}
			}
			foreach($data as $k => $v)
			{
				$v['up'] = 1;
				$v['down'] = 1;
				if(empty($v['parent_id']))
				{
					if($v['order'] == min($order_arr['parent']))
					{
						$v['up'] = 0;
					}
					if($v['order'] == max($order_arr['parent']))
					{
						$v['down'] = 0;
					}
					if(count($order_arr['parent']) == 1)
					{
						$v['up'] = 0;
						$v['down'] = 0;
					}
				}else{
					if($v['order'] == min($order_arr['child'][$v['parent_id']]))
					{
						$v['up'] = 0;
					}
					if($v['order'] == max($order_arr['child'][$v['parent_id']]))
					{
						$v['down'] = 0;
					}
					if(count($order_arr['child'][$v['parent_id']]) == 1)
					{
						$v['up'] = 0;
						$v['down'] = 0;
					}
				}
				$data[$k] = $v;
			}
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminCategories.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCategoryModel = pjCategoryModel::factory();
			if (!in_array($_POST['column'], $pjCategoryModel->getI18n()))
			{
				$pjCategoryModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjCategory', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		$post_max_size = pjUtil::getPostMaxSize();
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
		{
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionIndex&err=ACT06");
		}
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['category_update']))
			{
				$pjCategoryModel = pjCategoryModel::factory();
				
				$err = 'ACT01';
				
				$arr = $pjCategoryModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCategories&action=pjActionIndex&err=ACT08");
				}
				
				$data = array();
				if (isset($_FILES['image']))
				{
					if($_FILES['image']['error'] == 0)
					{
						if(getimagesize($_FILES['image']["tmp_name"]) != false)
						{
							if(!empty($arr['image']))
							{
								@unlink(PJ_INSTALL_PATH . $arr['image']);
							}
							$Image = new pjImage();
							if ($Image->getErrorCode() !== 200)
							{
								$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
								if ($Image->load($_FILES['image']))
								{
									$resp = $Image->isConvertPossible();
									if ($resp['status'] === true)
									{
										$hash = md5(uniqid(rand(), true));
										$image_path = PJ_UPLOAD_PATH . 'categories/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
				
										$Image->loadImage($_FILES['image']["tmp_name"]);
										$Image->resizeSmart($this->imageSizes[0], $this->imageSizes[1]);
										$Image->saveImage($image_path);
										$data['image'] = $image_path;
									}
								}
							}
						}else{
							$err = 'ACT10';
						}
					}else if($_FILES['image']['error'] != 4){
						$err = 'ACT10';
					}
				}
				
				$pjCategoryModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjCategory', 'data');
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionIndex&err=" . $err);
				
			} else {
				$arr = pjCategoryModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCategories&action=pjActionIndex&err=ACT08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjCategory');
				$this->set('arr', $arr);
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file']; 
				}
				
				$parent_arr = pjCategoryModel::factory()
					->select('t1.*, t2.content as name')
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->where('parent_id IS NULL')
					->where('t1.id <>', $_GET['id'])
					->findAll()
					->getData();
				
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				$this->set('parent_arr', $parent_arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminCategories.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
				
			$pjCategoryModel = pjCategoryModel::factory();
			$arr = $pjCategoryModel->find($_GET['id'])->getData();
				
			if(!empty($arr))
			{
				if(!empty($arr['image']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['image']);
				}
	
				$data = array();
				$data['image'] = ':NULL';
				$pjCategoryModel->reset()->where(array('id' => $_GET['id']))->limit(1)->modifyAll($data);
	
				$response['code'] = 200;
			}else{
				$response['code'] = 100;
			}
				
			pjAppController::jsonResponse($response);
		}
	}
	
	public function pjActionSetOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
	
			$pjCategoryModel = pjCategoryModel::factory();
			$arr = $pjCategoryModel->find($_POST['id'])->getData();
			if(empty($arr['parent_id']))
			{
				switch ($_POST['direction'])
				{
					case 'up':
						
						$prev_arr = array();
						$order = $arr['order'] - 1;
						while(empty($prev_arr))
						{
							$prev_arr = $pjCategoryModel->reset()->where('t1.order', $order)->where('t1.parent_id IS NULL')->findAll()->getData();
							$order--;
						}
						if(count($prev_arr) > 0)
						{
							$prev_arr = $prev_arr[0];
							$pjCategoryModel->reset()->where(array('id' => $arr['id']))->limit(1)->modifyAll(array('order' => $prev_arr['order']));
							$sub_arr = $pjCategoryModel->reset()->where('t1.parent_id', $arr['id'])->findAll()->getData();
							if(count($sub_arr) > 0)
							{
								foreach($sub_arr as $k => $v)
								{
									$pjCategoryModel->reset()->where(array('id' => $v['id']))->limit(1)->modifyAll(array('order' => ($prev_arr['order'] + $k + 1)));
								}
							}
							$pjCategoryModel->reset()->where(array('id' => $prev_arr['id']))->limit(1)->modifyAll(array('order' => $arr['order']));
							$sub_arr = $pjCategoryModel->reset()->where('t1.parent_id', $prev_arr['id'])->findAll()->getData();
							if(count($sub_arr) > 0)
							{
								foreach($sub_arr as $k => $v)
								{
									$pjCategoryModel->reset()->where(array('id' => $v['id']))->limit(1)->modifyAll(array('order' => ($arr['order'] + $k + 1)));
								}
							}
						}
						break;
					case 'down':
						$next_arr = array();
						$order = $arr['order'] + 1;
						while(empty($next_arr))
						{
							$next_arr = $pjCategoryModel->reset()->where('t1.order', $order)->where('t1.parent_id IS NULL')->findAll()->getData();
							$order++;
						}
						if(count($next_arr) > 0)
						{
							$next_arr = $next_arr[0];
							$pjCategoryModel->reset()->where(array('id' => $arr['id']))->limit(1)->modifyAll(array('order' => $next_arr['order']));
							$sub_arr = $pjCategoryModel->reset()->where('t1.parent_id', $arr['id'])->findAll()->getData();
							if(count($sub_arr) > 0)
							{
								foreach($sub_arr as $k => $v)
								{
									$pjCategoryModel->reset()->where(array('id' => $v['id']))->limit(1)->modifyAll(array('order' => ($next_arr['order'] + $k + 1)));
								}
							}
							$pjCategoryModel->reset()->where(array('id' => $next_arr['id']))->limit(1)->modifyAll(array('order' => $arr['order']));
							$sub_arr = $pjCategoryModel->reset()->where('t1.parent_id', $next_arr['id'])->findAll()->getData();
							if(count($sub_arr) > 0)
							{
								foreach($sub_arr as $k => $v)
								{
									$pjCategoryModel->reset()->where(array('id' => $v['id']))->limit(1)->modifyAll(array('order' => ($arr['order'] + $k + 1)));
								}
							}
						}
						break;
				}
			}else{
				switch ($_POST['direction'])
				{
					case 'up':
						$prev_arr = array();
						$order = $arr['order'] - 1;
						while(empty($prev_arr))
						{
							$prev_arr = $pjCategoryModel->reset()->where('t1.order', $order)->where('t1.parent_id', $arr['parent_id'])->findAll()->getData();
							$order--;
						}
						if(count($prev_arr) > 0)
						{
							$prev_arr = $prev_arr[0];
							$pjCategoryModel->reset()->where(array('id' => $arr['id']))->limit(1)->modifyAll(array('order' => $prev_arr['order']));
							$pjCategoryModel->reset()->where(array('id' => $prev_arr['id']))->limit(1)->modifyAll(array('order' => $arr['order']));
						}
						break;
					case 'down':
						$next_arr = array();
						$order = $arr['order'] + 1;
						while(empty($next_arr))
						{
							$next_arr = $pjCategoryModel->reset()->where('t1.order', $order)->where('t1.parent_id', $arr['parent_id'])->findAll()->getData();
							$order++;
						}
						if(count($next_arr) > 0)
						{
							$next_arr = $next_arr[0];
							$pjCategoryModel->reset()->where(array('id' => $arr['id']))->limit(1)->modifyAll(array('order' => $next_arr['order']));
							$pjCategoryModel->reset()->where(array('id' => $next_arr['id']))->limit(1)->modifyAll(array('order' => $arr['order']));
						}
						break;
				}
			}
		}
	}
}
?>