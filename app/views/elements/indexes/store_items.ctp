<table class="top_heading">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('Odběratel', 'StoreItem.purchaser_name')?></th>
			<th><?php echo $this->Paginator->sort('Produkt', 'Product.name')?></th>
			<th><?php echo $this->Paginator->sort('Množství', 'StoreItem.quantity')?></th>
			<th>Zásoba</th>
			<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
			<th><?php echo $this->Paginator->sort('Kč/J<br/>bez DPH', 'StoreItem.price', array('escape' => false))?></th>
			<th><?php echo $this->Paginator->sort('Kč', 'StoreItem.item_total_price')?></th>
			<th>Posl. prodej</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$odd = '';
		foreach ($stores as $store) {
			$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		?>
		<tr<?php echo $odd?>>
			<td><?php echo $this->Html->link($store['StoreItem']['purchaser_name'], array('controller' => 'purchasers', 'action' => 'view', $store['Purchaser']['id'], 'tab' => 9))?></td>
			<td><?php echo $store['Product']['name']?></td>
			<td align="right"><?php echo $store['StoreItem']['quantity']?></td>
			<td align="right"><?php
				$style = '';
				if ($store['StoreItem']['week_reserve'] > 9) {
					$style = ' style="color:red;font-weight:bold"';
				}
				?>
				<span<?php echo $style?>><?php echo $store['StoreItem']['week_reserve']?></span>
			</td>
			<td><?php echo $store['Unit']['shortcut']?></td>
			<td align="right"><?php echo $store['StoreItem']['price']?></td>
			<td align="right"><?php echo $store['StoreItem']['item_total_price']?></td>
			<td align="right"><?php echo $store['StoreItem']['last_sale_date']?></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<th>Celkem</th>
			<th>&nbsp;</th>
			<th align="right"><?php echo $store_items_quantity?></th>
			<th colspan="3">&nbsp;</th>
			<th align="right"><?php echo $store_items_price?></th>
			<th colspan="2">&nbsp;</th>
		</tr>
	</tfoot>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>