<?php if (!defined('APPLICATION')) exit();

class StoryController extends BlogController {
	
	public function Add() {
		Gdn::Dispatcher()->Dispatch('/post/discussion');
	}

	public function Edit($StoryID = '') {

	}

}