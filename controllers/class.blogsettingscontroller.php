<?php if (!defined('APPLICATION')) exit();

class BlogSettingsController extends BlogController {
	
	public $Uses = array('Form');
	public $AdminView = True;
	
	public function Initialize() {
		parent::Initialize();
		if ($this->DeliveryType() == DELIVERY_TYPE_ALL) {
			$this->AddSideMenu();
		}
	}
	
	public function Index() {
		$this->Permission('Blog.Settings.Manage');
		$this->ConfigurationModule = new ConfigurationModule($this);

		$CategoryModel = new CategoryModel();
		$CategoryData = $CategoryModel->GetAll('TreeLeft');
		
		if ($this->Form->IsPostBack()) {
			// $FormValues = $this->Form->FormValues();
			// $this->Form->FormValues($FormValues);
		}
		
		$Schema = array(
			'Blog.CategoryID' => array(
				'Items' => $CategoryData,
				'Options' => array('TextField' => 'Name', 'ValueField' => 'CategoryID'),
				'LabelCode' => 'Forum\'s category', 
				'Control' => 'Dropdown'),
		);

		$this->ConfigurationModule->Schema($Schema);
		$this->ConfigurationModule->Initialize();
		
		$this->Title(T('Settings'));
		$this->Render();
	}
}