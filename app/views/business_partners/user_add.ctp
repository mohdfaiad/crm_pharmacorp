<h1>Přidat obchodního partnera</h1>
<ul>
	<li><?php echo $html->link('Zpět na seznam obchodních partnerů', array('controller' => 'business_partners', 'action' => 'index'))?></li>
	<li><?php echo $html->link('Dohledat v systému ARES', array('controller' => 'business_partners', 'action' => 'ares_search'))?></li>
</ul>

<?php echo $form->create('BusinessPartner', array('url' => array('controller' => 'business_partners', 'action' => 'add')))?>
<table class="left_heading">
	<tr>
		<th>Název<sup>*</sup></th>
		<td colspan="7"><?php echo $form->input('BusinessPartner.name', array('label' => false, 'size' => 100))?></td>
	</tr>
	<tr>
		<th>Titul před</th>
		<td><?php echo $form->input('BusinessPartner.degree_before', array('label' => false, 'size' => 10))?></td>
		<th>Jméno</th>
		<td><?php echo $form->input('BusinessPartner.first_name', array('label' => false, 'size' => 20))?></td>
		<th>Příjmení</th>
		<td><?php echo $form->input('BusinessPartner.last_name', array('label' => false, 'size' => 30))?></td>
		<th>Titul za</th>
		<td><?php echo $form->input('BusinessPartner.degree_after', array('label' => false, 'size' => 10))?></td>
	</tr>
	<tr>
		<th>IČ<sup>*</sup></th>
		<td colspan="3"><?php echo $form->input('BusinessPartner.ico', array('label' => false, 'size' => 30))?></td>
		<th>DIČ</th>
		<td colspan="3"><?php echo $form->input('BusinessPartner.dic', array('label' => false, 'size' => 30))?></td>
	</tr>
	<tr>
		<th>Email</th>
		<td colspan="3"><?php echo $form->input('BusinessPartner.email', array('label' => false, 'size' => 30))?></td>
		<th>Telefon</th>
		<td colspan="3"><?php echo $form->input('BusinessPartner.phone', array('label' => false, 'size' => 30))?></td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td colspan="7"><?php echo $form->input('BusinessPartner.note', array('label' => false, 'rows' => 5, 'cols' => 70))?></td>
	</tr>
	<tr>
		<th>Provozní doba</th>
		<td colspan="7"><?php echo $form->input('BusinessPartner.opening_hours', array('label' => false, 'rows' => 7, 'cols' => 70))?></td>
	</tr>
	<tr>
		<td colspan="8">Adresa sídla</td>
	</tr>
	<tr>
		<th>Ulice</th>
		<td colspan="3"><?php echo $form->input('Address.0.street', array('label' => false, 'size' => 30))?></td>
		<th>Č. pop.<sup>*</sup></th>
		<td><?php echo $form->input('Address.0.number', array('label' => false, 'size' => 10))?></td>
		<th>Č. or.</th>
		<td><?php echo $form->input('Address.0.o_number', array('label' => false, 'size' => 10))?></td>
	</tr>
	<tr>
		<th>Město<sup>*</sup></th>
		<td colspan="3"><?php echo $form->input('Address.0.city', array('label' => false, 'size' => 30))?></td>
		<th>PSČ</th>
		<td colspan="3"><?php echo $form->input('Address.0.zip', array('label' => false, 'size' => 10))?></td>
	</tr>
	<tr>
		<th>Okres</th>
		<td colspan="7"><?php echo $form->input('Address.0.region', array('label' => false, 'size' => 30))?></td>
	</tr>
<?php if ($user['User']['id'] != 18) { ?>	
	<tr>
		<td colspan="8">Odběratel</td>
	</tr>
	<tr>
		<th colspan="4">Vytvořit odběratele se stejnými hodnotami</th>
		<td colspan="4"><?php echo $form->input('Purchaser.same', array('label' => false, 'type' => 'checkbox'))?></td>
	</tr>
<?php } ?>
</table>

<?php
	echo $form->hidden('Address.0.address_type_id', array('value' => 1));
	echo $form->submit('Uložit');
	echo $form->end();
?>

<ul>
	<li><?php echo $html->link('Zpět na seznam obchodních partnerů', array('controller' => 'business_partners', 'action' => 'index'))?></li>
</ul>