<?php 
include("db_config.php");
$head_title = "我的订单";
include("include/head_.php");
$top_title = "我的订单";
$return_url = "..";

$top_navigate_return = '<a href="/" target="_self"><img src="/img/return_top.png" alt="返回"></a>';
?>

<div class="top_navigate"> 
	<span>
		<?php echo $top_navigate_return;?>
	</span> 
	<span><?php echo $top_title;?></span> 
</div>
<div class="my_order_cate" style="margin-top: 48px;">
  <ul style="display: flex;">
    <li class="my_order_cate_on">全部订单</li>
	<li><a href="my_order.php" target="_self">待付款</a></li>
	<li><a href="my_ordership.php" target="_self">待发货</a></li>
	<li><a href="my_orderend.php" target="_self">待收货</a></li>
	<li><a href="my_ordercomplete.php" target="_self">待评价</a></li>
	<!-- <li><a href="my_orderoff.php" target="_self">已取消</a></li> -->
  </ul>
</div>
<div class="my_order_cate_list">
<?php 
$member_id = $member_login;
$query = "SELECT * FROM payment_list left join teacher_list on payment_list.pay_shop = teacher_list.tl_id where pay_member = '{$member_id}' and pay_status <> '10' and if(pay_status = '0', teacher_list.delete_status !=0, (teacher_list.delete_status = 0 or teacher_list.delete_status = 1) )";
$query .= " and ship_status != '-1' order by pay_id desc;";
// $query .= " and ship_status != '-1' and pay_cate = 'busines' order by pay_id desc;";

include("include/myorder_list.php");
?>
</div>
<script type="text/javascript">
	function order_off(shop_id) {
		$.post("post/order_off.php",
		  {
			shop_id:shop_id
		  },
		  function(data,status){
			if (data == 1) {
				$("#payment_"+shop_id).fadeOut(300,function(){
					$(this).remove();
				});
			}
		  });
	}
	function order_del(shop_id) {
		$.post("post/order_del.php",
		  {
			shop_id:shop_id
		  },
		  function(data,status){
			if (data == 1) {
				$("#payment_"+shop_id).fadeOut(300,function(){
					$(this).remove();
				});
			}
		  });
	}
	function receive(shop_id) {
		if(window.confirm("你收到货吗？")){
			$.post("post/order_receive.php",
			  {
				shop_id:shop_id
			  },
			  function(data,status){
				if (data == 1) {
					$("#payment_"+shop_id).fadeOut(300,function(){
						$(this).remove();
					});
				}
			  });
		}
	}
</script>
<?php 
include("include/foot_.php");
?>
