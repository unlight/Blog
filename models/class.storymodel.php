<?php if (!defined('APPLICATION')) exit();

class StoryModel extends DiscussionModel {

	//protected $ClassName = 'DiscussionModel';
	//
	
	protected $DiscussionModel;

	public function Save($Values, $Settings = False) {

		$CheckDuplicates = GetValue('CheckDuplicates', $Settings);

		if (is_null($this->DiscussionModel)) {
			$this->DiscussionModel = new DiscussionModel();
			$this->DiscussionModel->SpamCheck = False;
		}

		if (isset($Values['Attributes'])) {
			$Values['Attributes'] = Gdn_Format::Serialize($Values['Attributes']);
		}
		$Values = array_map('trim', $Values);
		UsefulModel::SetNullValues($Values);
		$BlogCategoryID = C('Blog.CategoryID');
		$Values['CategoryID'] = $BlogCategoryID;

		$IsInsert = (GetValue('DiscussionID', $Values) === False);

		if (!array_key_exists('InsertUserID', $Values)) {
			$Values['InsertUserID'] = Gdn::UserModel()->GetSystemUserID();
		}

		if ($IsInsert) {
			$this->AddInsertFields($Values);
			$Values['DateLastComment'] = $Values['DateInserted'];
		}

		$this->AddUpdateFields($Values);

		if ($IsInsert) {
			//LogModel::Insert('Pending', 'Discussion', $Values);
			//return UNAPPROVED;
		}
//		var_dump($CheckDuplicates);die;
		if ($CheckDuplicates) {
			//var_dump($Values['Name']);die;
			$Story = $this->DiscussionModel->SQL
				->From($this->DiscussionModel->Name)
				->Where('Name', $Values['Name'])
				->Where('DateInserted', $Values['DateInserted'])
				->Limit(1)
				->Get()
				->FirstRow();
			if ($Story) {
				return False;
			}
		}
		
		//$TaggingPlugin = Gdn::PluginManager()->GetPluginInstance('TaggingPlugin');
		//$Args = array('FormPostValues' => $Values);
		//$TaggingPlugin->DiscussionModel_BeforeSaveDiscussion_Handler($this, $Args);
		//$RowID = $this->DiscussionModel->Save($Values);
		$RowID = $this->DiscussionModel->Save($Values);
		//$TaggingPlugin->DiscussionModel_AfterSaveDiscussion_Handler($this, $Args);

		return $RowID;
	}
}