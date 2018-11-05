<?php
include( "db_config.php" );


$head_title = "搜索";
include("include/head_.php");
$top_title = "搜索";
//$top_url = '<span style="text-align: right; width: auto; margin-right:2%;" onClick="window.location.href=\'my_subject_add.php\'">新科目发布<span>';
$return_url = "..";
include("include/top_navigate.php");
?>

<?php
$keyword = @$_GET['keyword'];
if (!$keyword) {
    $keyword = "";
}

?>

<div style="margin: 5px; margin-top: 53px">
        <input type="hidden" name="menu" value="busines">
        <input type="search" name="keyword" style="background-color:rgba(255,255,255,0.8);text-overflow: ellipsis;white-space: nowrap;overflow: hidden;padding-left: 28px;width: 92%;margin: 0px" placeholder="请输入你要查找的关键词" onkeydown="enterKey(event)" value="<?php echo $keyword;?>">
        <img style="width: 18px;vertical-align: text-bottom;" onclick="searchSubject()" src="img/magnifying-glass.png">
</div>


<div class="clearfix"></div>
<div class="businesses_blog_list">
    <?php 
        $query_list = "SELECT * FROM subject_list where sub_title like '%{$keyword}%' and delete_status != 0 ORDER BY sub_id desc";

        $result_list = mysqli_query($mysqli, $query_list);
        if ($result_list && $keyword != '')
        {
            while( $row_list = mysqli_fetch_assoc($result_list) ){ 
                $tlid = $row_list['sub_id'];
                $follow_totalNumber = false;
                $sql_follow = mysqli_query($mysqli, "SELECT count(*) FROM follow_list where fl_tid = '{$tlid}'");
                
                if($sql_follow)
                {
                    $follow_rs = mysqli_fetch_array($sql_follow,MYSQLI_NUM);
                    $follow_totalNumber = $follow_rs[0];
                }
                if ($follow_totalNumber) {
                    $follow_img = "img/on_ok.png";
                } else {
                    $follow_img = "img/off_ok.png";
                }
                $sub_cate_type = $row_list['sub_cate_type'];
                $sub_cate_cid = $row_list['sub_cate_cid'];
                $query_cate = "SELECT * FROM item_cate where ic_cid = '{$sub_cate_cid}' and ic_type = '{$sub_cate_type}'";
                if ($result_cate = mysqli_query($mysqli,$query_cate)) {
                    $row_cate = mysqli_fetch_assoc($result_cate);
                }
    ?>
                <div class="mui-card" style="margin: 1px 0px; padding: 0px;box-shadow: none;" id="subject_<?php echo $row_list['sub_id'];?>">
                    
                    <div class="mui-card-media card businesses_blog_list_small mui-pull-left" style="width: 30%;padding: 10px" ><a class="mui-media-object" href="lecture_list.php?sub_id=<?php echo $row_list['sub_id'];?>" target="_self"><img src="<?php echo $row_list['sub_picture'];?>" alt=""></a></div>
                    <div class="mui-card-content mui-pull-left" style="padding: 6px; width: 70%">
                        
                            <p class="businesses_blog_list_title mui-h4" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;"><?php echo $row_list['sub_title'];?></p>
                            <p class="businesses_blog_list_title mui-h5" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;"><?php echo $row_list['sub_desc'];?></p>
                            <p class="businesses_blog_list_title mui-h5"><?php echo $row_list['sub_time'];?></p>
                            <p class="businesses_blog_list_cate" style="width: 100%">
                                    <span style="width: 20%;padding: 6px;background-color: #ff655e"><?php echo $row_cate['ic_name'];?></span>
                                    <span style="width: 20%; float: right;"><?php echo $row_list['sub_lecture_count'];?>集</span><img src="img/series.png" style="vertical-align: top;width: 24px; float: right;">
                                    <span style="width: 20%; float: right;margin: 2px 5px"><?php echo $row_list['sub_play_cnt'];?></span><img src="img/reciever.png" style="vertical-align: top;width: 24px; float: right;">
                            </p> 
                        
                    </div>
                </div>
    <?php 
            }
        }
    ?>
</div>
<script type="text/javascript">
function searchSubject() {
    $.get("post/search_result.php",
      {
        keyword: $( "[name='keyword']" ).val().trim()
      },
      function(data,status){
        if (data) {
            //alert("删除成功！");
            $(".businesses_blog_list").html(data);
        }
      });
    
}

function enterKey(e)
{
    var keynum;
    if(window.event) // IE
        keynum = e.keyCode;
    else if(e.which) // Netscape/Firefox/Opera
        keynum = e.which;
    //if (keynum == 13)
        searchSubject();
}
</script>
</body>
</html>
