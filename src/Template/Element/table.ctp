<?php

	echo $this->Html->script( ['PaginateAjax./js/lista'], ['block' => true] );

	echo '<div style="display: none;">';

	echo $this->Form->control('pagina');

	echo $this->Form->control('ultima');

	if ( !empty( @$acoes ) )
	{
		$this->Form->unlockField('acoes');
		echo $this->Form->control('acoes', ['type'=>'text', 'value'=> json_encode($acoes)] );
	}

	echo '</div>';
?>

<table class="tablePaginaAjax">
	<thead name='thead'></thead>
	<tbody name='tbody'></tbody>
</table>
