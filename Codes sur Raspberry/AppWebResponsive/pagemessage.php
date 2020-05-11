<div>
<?PHP if ($Erreur || $Info) { ?>
	<?php if ($Erreur) { ?>  
		   <em class="warning"> <?php echo $ErreurMsg; ?></em>
	<?php } else { ?>
		   <em class="primary"> <?php echo $InfoMsg; ?></em>
	<?php } ?> 
<?php } else { ?>  
	     <p>&nbsp;</p>
<?php } ?>   
</div>