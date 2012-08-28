<?php if (!defined('APPLICATION')) exit();

class PageController extends BlogController {
	
	public function Index($Page = 'p1') {
		$BlogCategoryID = C('Blog.CategoryID');
		$this->FireEvent('BeforeBeginBlog');
		Gdn::Dispatcher()->Dispatch("vanilla/categories/$BlogCategoryID/$Page");
	}
}