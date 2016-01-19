<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->
<form action="{FORM_ACTION}" method="post" class="confirm-reload">
	<input name="save" type="hidden" value="1" />
	<div class="row">
		<div class="col-sm-24 col-md-18">
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover">
					<colgroup>
						<col class="w200" />
						<col />
					</colgroup>
					<tbody>
						<tr>
							<td class="text-right"> {LANG.title}</td>
							<td><input class="w300 form-control pull-left" type="text" value="{DATA.title}" name="title" id="idtitle" maxlength="255" /></td>
						</tr>
						
						<tr>
							<td class="text-right">{LANG.image}</td>
							<td><input class="w300 form-control pull-left" type="text" name="image" id="image" value="{DATA.image}" style="margin-right: 5px"/> <input type="button" value="Browse server" name="selectimg" class="btn btn-info"/></td>
						</tr>
						
						<tr>
							<td class="text-right">Số điện thoại</td>
							<td><input class="w300 form-control" type="text" name="phone" id="phone" value="{DATA.phone}"/></td>
						</tr>
							<tr>
							<td class="text-right">Số điện thoại để nhấn gọi </td>
							<td><input class="w300 form-control" type="text" name="phone1" id="phone1" value="{DATA.phone1}"/>yêu cầu viết liền và giống số trên</td>
						</tr>
						
					</tbody>
				</table>
			</div>
		</div>
		
	</div>
	<div class="row text-center"><input type="submit" value="{LANG.save}" class="btn btn-primary"/></div>
</form>
<script type="text/javascript">
var uploads_dir_user = '{UPLOADS_DIR_USER}';
$("#titlelength").html($("#idtitle").val().length);
$("#idtitle").bind('keyup paste', function() {
	$("#titlelength").html($(this).val().length);
});


</script>
<!-- BEGIN: get_alias -->
<script type="text/javascript">
	$(document).ready(function() {
		$('#idtitle').change(function() {
			get_alias('{ID}');
		});
	});
</script>
<!-- END: get_alias -->
<!-- END: main -->