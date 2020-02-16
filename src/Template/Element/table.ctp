<?php

	echo $this->Html->script( ['PaginateAjax./js/lista'], ['block' => true] );

	echo '<div style="display: none;">';

	echo $this->Form->control('pagina', ['value'=> @$pagina]);

	echo $this->Form->control('ultima', ['value'=> (@$pagina+1)]);

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
