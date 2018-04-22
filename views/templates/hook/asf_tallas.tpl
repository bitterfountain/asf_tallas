


{if isset($combinations)}

	<div id="comprar{$id_product}" class="bcompra" >	
		<a class="compradirecta" id="compradirecta{$id_product}" title="{l s='Add to your cart the selected size of this product'}" href="" rel="" data-idproduct="{$id_product}" >
			{l s='Buy'}
		</a>
		<br />
		<span id="msg{$id_product}" class="avisotalla oculto">{l s='Select one size from below before buy.'}</span>
	</div>

	<div id="info{$id_product}" class="msginfo" >	
							
	</div>		

	<div id="combs{$id_product}" class="combinations"> 
			{foreach $combinations as $comb}
					{*<th>{$comb.color_name}</th>*}
					{foreach $comb.tallas as $talla}									
						<div class="combinacion {$talla.class_talla}" >
							<button class="tallasn" href="#" data-idproduct="{$comb.id_product}" data-idproductattribute="{$talla.id_product_attribute}" title="{$talla.title_out_stock}" >
								{$talla.attribute_name}
							</button>
						</div>
					{/foreach}
			{/foreach}

	</div>



	
{/if}

