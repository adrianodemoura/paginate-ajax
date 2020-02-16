<?= $this->Form->create('Form1', ['url'=>['action'=>'lista']] ); ?>

<div>Form 1</div>

<div>
	<?= $this->element('PaginateAjax.table', ['acoes'=> @$acoes] ) ?>
</div>

<div>
	<div>
		<?= $this->element('PaginateAjax.navigate') ?>
	</div>

	<div>
		<?= $this->element('PaginateAjax.info') ?>
	</div>
</div>

<?= $this->Form->end(); ?>