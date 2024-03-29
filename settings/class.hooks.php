<?php if (!defined('APPLICATION')) exit();

class BlogHooks implements Gdn_IPlugin {

	protected $_InBlog;

	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu =& $Sender->EventArguments['SideMenu'];
		$Menu->AddLink('Site Settings', T('Blog Settings'), 'blogsettings', 'Blog.Settings.Manage', array());
	}

	public function Base_Render_Before($Sender) {
		$Session = Gdn::Session();
		if ($Sender->Menu) {
			$Sender->Menu->AddLink('Blog', T('Blog'), '/blog/page', FALSE);
		}
	}

	public function PageController_BeforeBeginBlog_Handler($Sender) {
		$this->_InBlog = TRUE;
	}

	public function DiscussionController_BeforeDiscussionRender_Handler($Sender) {
		$CategoryID = $Sender->CategoryID;
		$this->_InBlog = ($Sender->CategoryID == C('Blog.CategoryID'));
		if ($this->_InBlog) {
			$Sender->CssClass .= ' BlogStory';
			$Sender->AddCssFile('blog.css', 'blog');
		}
	}

	public function CategoriesController_BeforeGetDiscussions_Handler($Sender) {
		if ($this->_InBlog) {
			$PerPage =& $Sender->EventArguments['PerPage'];
			$PerPage = C('Blog.Posts.PerPage', 5);
		}
	}

	public function CategoriesController_BeforeCategoriesRender_Handler($Sender) {
		if ($this->_InBlog) {
			$CountDiscussions = $Sender->Data('CountDiscussions');
			$Limit = $Sender->Data('_Limit');
			$Page =  GetValue(1, $Sender->RequestArgs);
			list($Offset, $Limit) = OffsetLimit($Page, $Limit);
			$PagerFactory = new Gdn_PagerFactory();
			$Pager = $PagerFactory->GetPager('Pager', $Sender);
			$Pager->ClientID = 'Pager';
			$Pager->Configure($Offset, $Limit, $CountDiscussions, 'blog/page/%s');
			PagerModule::Current($Pager);
		}
	}
	

	public function CategoriesController_Render_Before($Sender) {
		if ($this->_InBlog) {
			$Sender->CssClass .= ' BlogPage';
			$Sender->AddCssFile('blog.css', 'blog');
		}
	}

	
	public function CategoriesController_BeforeDiscussionName_Handler($Sender) {
		if ($this->_InBlog) {
			$CssClass =& $Sender->EventArguments['CssClass'];
			$CssClass .= ' BlogStory';
		}
	}

	// public function DiscussionModel_BeforeSaveDiscussion_Handler($DiscussionModel) {
	// 	$FormValues = $DiscussionModel->EventArguments['FormValues'];
	// }

	public function CategoriesController_AfterDiscussionContent_Handler($Sender) {
		if ($this->_InBlog) {
			$Discussion = $Sender->EventArguments['Discussion'];
			$Image = '';
			if ($Discussion->StoryImage) {
				// $Image = Thumbnail($Discussion->StoryImage, array(
				// 	'alt' => $Discussion->Name,
				// 	'width' => 200,
				// 	'class' => 'StoryImage'
				// ));
				
				$Image = Img($Discussion->StoryImage, array(
					'width' => 200,
					'alt' => $Discussion->Name,
					'class' => 'StoryImage'
				));
			}

			$MaxLength = C('Blog.Posts.CutLength', 250);
			$TextBody = $Discussion->Body;
			if ($MaxLength > 1) $TextBody = SliceString($TextBody, $MaxLength);
			$HtmlBody = Gdn_Format::Html($TextBody);
			$Body = $Image . ' ' . $HtmlBody;
			
			echo Wrap($Body, 'div', array('class' => 'Body Clear'));
		}
		//d($Sender);
	}
	// 
	// public function CategoriesController_Afterdiscussiontitle_Handler($Sender) {
	// 	echo Wrap('Afterdiscussiontitle');
	// 	//d($Sender);
	// }

	// public function CategoriesController_BeforediscussionMeta_Handler($Sender) {
	// 	echo Wrap('BeforediscussionMeta');
	// 	//d($Sender);
	// }

	// public function CategoriesController_AftercountMeta_Handler($Sender) {
	// 	echo Wrap('BeforediscussionMeta');
	// 	//d($Sender);
	// }

	// public function DiscussionModel_BeforeGetID_Handler($Sender) {
	// 	$Where = array();
	// 	$this->_DiscussionModelBeforeGet($Where);
	// 	$Sender->SQL->Where($Where);
	// }

	// public function LogModel_BeforeRestore_Handler($Sender) {
	// 	d($Sender->EventArguments);
	// }

	public function DiscussionController_AfterDiscussionBody_Handler($Sender) {
		$Discussion = $Sender->EventArguments['Discussion'];
		$Attributes = $Discussion->Attributes;
		$SourceUrl = GetValue('SourceUrl', $Attributes);
		if ($SourceUrl) {
			echo '<p>Источник: ', Anchor($SourceUrl, $SourceUrl, '', array('rel' => 'nofollow')) . '</p>';
		}
	}

	protected function _DiscussionModelBeforeGet(&$Where) {
		if (!$this->_InBlog) {
			$Where['d.CategoryID <>'] = C('Blog.CategoryID');
		} else {
			//$Where['d.IsPublished'] = 1;
		}
	}
	
	public function DiscussionModel_BeforeGetCount_Handler($Sender) {
		$this->_DiscussionModelBeforeGet($Sender->EventArguments['Wheres']);
	}
	
	public function DiscussionModel_BeforeGet_Handler($Sender) {
		$Wheres =& $Sender->EventArguments['Wheres'];
		$this->_DiscussionModelBeforeGet($Wheres);
	}

	public function PostController_BeforeDiscussionRender_Handler($Sender) {
		$this->_InBlog = ($Sender->CategoryID == C('Blog.CategoryID'));
		if ($this->_InBlog) {
			//$Sender->AddJsFile('applications/blog/js/post.js');
		}
	}

	public function PostController_AfterDiscussionFormOptions_Handler($Sender) {
		if (in_array($Sender->RequestMethod, array('discussion', 'editdiscussion'))) {
			echo '<div class="P">';
			if (class_exists('MorfPlugin')) {
				echo $Sender->Form->Label('Story Image', 'StoryImage');
				echo $Sender->Form->UploadBox('StoryImage');
			} else {
				//echo $Sender->Form->Label('Story Image', 'StoryImage');
				//echo $Sender->Form->Input('StoryImage', 'file');
			}
			echo '</div>';
		}
	}

	public function DiscussionModel_DeleteDiscussion_Handler($Sender) {
		$DiscussionID = $Sender->EventArguments['DiscussionID'];
		$StoryImage = Gdn::SQL()
			->Select('StoryImage')
			->From('Discussion')
			->Where('DiscussionID', $DiscussionID)
			->Get()
			->Value('StoryImage');
		if (is_file($StoryImage)) {
			unlink($StoryImage);
		}
	}

	public function Setup() {
		include PATH_APPLICATIONS . DS . 'blog/settings/structure.php';
	}
}