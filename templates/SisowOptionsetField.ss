<ul $AttributesHTML>
	<% loop $Options %>
		<li class="$Class">
			<input id="$ID" class="radio" name="$Name" type="radio" value="$Value"<% if $isChecked %> checked<% end_if %><% if $isDisabled %> disabled<% end_if %> />
			<label for="$ID"><img src="resources/hestec/silverstripe-sisowmethod/client/images/gateway-icons/$Value\.png">$Title.RAW</label>
		</li>
	<% end_loop %>
</ul>