<?php if (!defined('APPLICATION')) exit();

class PageController extends BlogController {
	
	public function Index() {
		$BlogCategoryID = C('Blog.CategoryID');
		$this->FireEvent('BeforeBeginBlog');
		Gdn::Dispatcher()->Dispatch('vanilla/categories/'.$BlogCategoryID);
	}
}