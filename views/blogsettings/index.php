<?php if (!defined('APPLICATION')) die(); 


$CategoriesUse = C('Vanilla.Categories.Use');

?>

<h1><?php echo $this->Data('Title');?></h1>

<?php if ($CategoriesUse): ?>

<?php $this->ConfigurationModule->Render(); ?>

<?php endif; ?>

<div class="Info">
<p>Чтобы использовать блог, необходимо включить использование категорий форума.</p>
<p><?php echo T('Currently, using categories is:') ?> <?php echo T($CategoriesUse ? 'ENABLED' : 'DISABLED');?></p>
<p><?php echo Anchor(T('Manage Categories'), '/vanilla/settings/managecategories');?></p>
</div>

