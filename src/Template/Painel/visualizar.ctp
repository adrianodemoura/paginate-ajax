<?php ?>

<div>
    <dl>
        <dd>Código</dd>
        <dd><?= @$this->request->data[0]->id ?></dd>
    <dl>

    <dl>
        <dd>Município: </dd>
        <dd><?= @$this->request->data[0]->nome ?></dd>
    <dl>
</div>


<div>
<?= $this->Html->link( __('Voltar'), ['action'=>'index'] ); ?>
</div>