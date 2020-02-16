<?= $this->Form->create('Form1', ['url'=>['action'=>'lista']] ); ?>

<div>Form 1</div>

<?= $this->element('PaginateAjax.table', ['acoes'=> @$acoes, 'pagina'=>@$pagina] ) ?>

<div>
	<?= $this->element('PaginateAjax.navigate') ?>

	<?= $this->element('PaginateAjax.info') ?>
</div>

<?= $this->Form->end(); ?>