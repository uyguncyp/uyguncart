<div class="navbar">
	<div class="navbar-inner">
		<a class="brand" href="#">Latest 10 Order</a>
	</div>
</div>
<table class="table table-bordered" id="OrderTable">
	<thead>
		<tr>
			<th>User ID</th>
			<th>Customer</th>
			<th>Total Price</th>
			<th>Order Added Date</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
<script type="text/javascript">
	function draw_table(data) {
		var html = "";
		for (var i = 0; i < data.length; i++) {
			html += '<tr>';
			html += '<td>'+data[i]['user_id']+'</td>';
			html += '<td>'+data[i]['first_name']+' '+data[i]['last_name']+'</td>';
			html += '<td>$'+data[i]['total_price']+'</td>';
			html += '<td>'+data[i]['added_date']+'</td>';
			html += '<td><a href="'+base_url+'admin/order/get_order/'+data[i]['order_id']+'" class="view" data-toggle="modal" data-target="#OrderDetail">View</a></td>';
			html += '</tr>';
		}
		$("#OrderTable tbody").html(html);
	}
</script>
<div id="OrderDetail" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Order Detail</h3>
	</div>
	<div class="modal-body">

	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>
