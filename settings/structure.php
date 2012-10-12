<?php if (!defined('APPLICATION')) exit();

if (!isset($Drop)) $Drop = FALSE;
if (!isset($Explicit)) $Explicit = FALSE;

$Database = Gdn::Database();
$SQL = $Database->SQL();
$Construct = $Database->Structure();
$Validation = new Gdn_Validation();
$Construct->Reset();
$Px = $Construct->DatabasePrefix();

// $Construct->Table('MovieSequence')
// 	->Engine('MyISAM')
// 	->PrimaryKey('MovieSequenceID')
// 	->Column('Name', 'char(20)')
// 	->Set($Explicit, $Drop);

// Permissions.
$PermissionModel = Gdn::PermissionModel();
$PermissionModel->Define(array(
	'Blog.Settings.Manage'
));

$Construct
	->Table('Discussion')
	->Column('Name', 'varchar(250)', FALSE, 'fulltext')
	->Column('StoryImage', 'varchar(250)', NULL)
	->Set(FALSE, FALSE);