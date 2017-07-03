<?php echo $this->render_table_name($mode); ?>
<div class="xcrud-top-actions">
    <?php echo $this->render_button('save_new','save','create','btn btn-primary','','create,edit') ?>
    <?php echo $this->render_button('save_edit','save','edit','btn btn-default','','create,edit') ?>
    <?php echo $this->render_button('save_return','save','list','btn btn-success','','create,edit') ?>
    <?php echo $this->render_button('return','list','','btn btn-warning') ?>
</div>
<div class="xcrud-view">
<?php echo $this->render_fields_list($mode); ?>
</div>
<div class="xcrud-nav">
    <?php echo $this->render_benchmark(); ?>
</div>
