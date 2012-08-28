<?php if (!defined('APPLICATION')) exit();

class BlogController extends Gdn_Controller {
	
	public $AdminView;

	public function Initialize() {
		parent::Initialize();
		if ($this->DeliveryType() == DELIVERY_TYPE_ALL) {
			$this->Head = new HeadModule($this);
			$this->AddJsFile('jquery.js');
			$this->AddJsFile('jquery.livequery.js');
			$this->AddJsFile('global.js');
			if ($this->AdminView) {
				$this->AddCssFile('admin.css');
				$this->MasterView = 'admin';
			} else {
				$this->AddCssFile('style.css');
			}
		}
	}
	
	public function AddSideMenu($CurrentUrl = FALSE) {
		if (!$CurrentUrl) $CurrentUrl = strtolower($this->SelfUrl);
		
		// Only add to the assets if this is not a view-only request
		if ($this->_DeliveryType == DELIVERY_TYPE_ALL) {
			// Configure SideMenu module
			$SideMenu = new SideMenuModule($this);
			$SideMenu->HtmlId = '';
			$SideMenu->HighlightRoute($CurrentUrl);
			$SideMenu->Sort = C('Garden.DashboardMenu.Sort');
		
			// Hook for adding to menu
			$this->EventArguments['SideMenu'] =& $SideMenu;
			$this->FireEvent('GetAppSettingsMenuItems');
		
			// Add the module
			$this->AddModule($SideMenu, 'Panel');
		}
	}

}