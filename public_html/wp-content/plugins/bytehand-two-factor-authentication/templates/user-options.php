<h3>Настройки двух-факторной авторизации</h3>

	<table class="form-table">

		<tr>
			<th><label for="mobile">Номер мобильного</label></th>

			<td>
        <input type="text" name="mobile" id="mobile" value="<?php echo esc_attr( get_the_author_meta( 'mobile', $data->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Пожалуйста, укажите номер мобильного телефона в формате +79111111111</span>
			</td>
		</tr>

	</table>