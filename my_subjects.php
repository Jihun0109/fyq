<?php
include( "db_config.php" );
if ( !$member_login ) {
    echo "<script> alert('请先登陆帐号');parent.location.href='index.php'; </script>";
    exit;
}
$query_merchant = "SELECT * FROM merchant_entry where me_user = '{$member_login}'";
$result_merchant = mysqli_query($mysqli, $query_merchant);

$query_college = "SELECT * FROM college_list where cl_phone = '{$member_login}'";
$result_college = mysqli_query($mysqli, $query_college);
//print_r($result_merchant);

if ($result_merchant->num_rows > 0 || $result_college->num_rows > 0)
{
    $row_merchant = mysqli_fetch_assoc($result_merchant);
    if ($row_merchant['me_state'] == '0') 
    {
        echo "<script> alert('专家入驻审核中！');parent.location.href='index.php'; </script>";
        exit;
    }
}
else
{
    echo "<script> alert('专家入驻后才能操作！');parent.location.href='merchant_entry.php'; </script>";
    exit;
}

$head_title = "科目列表";
include("include/head_.php");
$top_title = "科目列表";
$top_url = '<span style="text-align: right; width: auto; margin-right:2%;" onClick="window.location.href=\'my_subject_add.php\'">新科目发布<span>';
$return_url = "..";
include("include/top_navigate_commodity.php");
$row_college = mysqli_fetch_assoc($result_college);
?>

<img src="<?php echo $row_college['cl_bg'];?>" style="width: 100%;height: 120px;filter: blur(2px);top:48px;position: absolute;">
<div style="position: relative;margin-top: 80px;">
    <img src="<?php echo $row_college['cl_logo'];?>" style="width: 100px;height: 100px; box-shadow: 0px 0px 15px 3px rgba(0,0,0,.3);border: solid white 2px;float: left;margin: 18px 20px">
    <div style="padding-top: 15px;">
        <p style="padding: 0px;color: white;font-size: 18px; margin: 3px;"><?php echo $row_college['cl_name'];?></p>
        <p class="mui-ellipsis" style="padding: 0px;font-size: 15px;color: white;margin: 3px;"><?php echo $row_college['cl_province'];?> <?php echo $row_college['cl_city'];?> <?php echo $row_college['cl_area'];?></p>
    </div>
    <div style="color: #333;background-color: #efeff4;border-radius: 8px;">
        <p class="businesses_blog_list_cate" style="margin-bottom: auto;">
            <img src="img/series.png" style="vertical-align: middle;width: 22px;"><span style="margin: 0px 5px"><?php echo $row_college['cl_allfollow'];?></span>
            <span style="float: right;padding: 0px 5px;background-color: #ff655e;color: white;border-radius: 3px;margin-right: 10px;margin-top: 6px;line-height: 1.7;"><?php echo $row_college['ic_name'];?></span>
        </p>
        <p><img src="img/reciever.png" style="vertical-align: top;width: 24px;"><span style="width: 20%; margin: 2px 5px"><?php echo $row_college['cl_allsales'];?></span></p>
    </div>
</div>
<div class="clearfix"></div>
<div class="businesses_blog_list">
    <?php 
        $query = "SELECT * FROM college_list where cl_phone = '{$member_login}'";
       
        if ($result = mysqli_query($mysqli, $query))
        {
            $row = mysqli_fetch_assoc($result);
        }
        $tid = $row['cl_id'];   
        $query_list = "SELECT * FROM subject_list where sub_teacher_id = '{$tid}'  and delete_status != 0 ORDER BY sub_id desc";
        $result_list = mysqli_query($mysqli, $query_list);
        if ($result_list && $tid != '')
        {
            while( $row_list = mysqli_fetch_assoc($result_list) ){ 
                $tlid = $row_list['sub_id'];
                $follow_totalNumber = false;
                $sql_follow = mysqli_query($mysqli, "SELECT count(*) FROM follow_list where fl_phone = '{$member_login}' and fl_tid = '{$tlid}'");
                
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
                    
                    <div class="mui-card-media card businesses_blog_list_small mui-pull-left" style="width: 30%;padding: 10px" ><a class="mui-media-object" href="lecture_list.php?sub_id=<?php echo $row_list['sub_id'];?>&type=teacher" target="_self"><img src="<?php echo $row_list['sub_picture'];?>" alt=""></a></div>
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
                    <div class="mui-pull-right businesses_blog_list_cate" style="margin-bottom: 5%; margin-right: 2%;">
                        <!-- <div class="mui-btn mui-btn-primary"><a style="color: #FFFFFF;" href="javascript:resubject('<?php echo $row_list['sub_id'];?>');">重新发布</a></div> -->
                        <div class="mui-btn mui-btn-primary"><a style="color: #FFFFFF;" href="my_subject_alter.php?sub_id=<?php echo $row_list['sub_id'];?>">修改</a></div>
                        <div class="mui-btn" onClick="subject_del('<?php echo $row_list['sub_id'];?>')">删除</div>
                    </div>
                </div>
    <?php 
            }
        }
    ?>
</div>
</body>
</html>
<script type="text/javascript">
function subject_del(del_id) {
     if(confirm("确定?")){
        $.post("post/subject_del.php",
          {
            subject_del_id:del_id
          },
          function(data,status){
            if (data) {
                alert("删除成功！");
                $("#subject_"+del_id).remove();
            }
          });
     }else{
         console.log("取消");
     }
    
}

function resubject(re_id)
{
    $.post("post/subject_re.php",
          {
            subject_id:re_id
          },
          function(data,status){
            if (data == '1') {
                alert("重新发布成功！");
            }
            else if (data == '2')
            {
                alert("请10分钟后重新发布！");
            }
            else
            {
                alert("系统繁忙，请稍后重新发布！");
            }
          });
}
</script>