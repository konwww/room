function openBorrowWindow() {
    $("#borrow").css("visibility", "visible");
    layui.use("layer", function () {
        var layer = layui.layer;
        layer.open({
            title: "借用信息填写",
            type: 1,
            // maxWidth:"40%",
            closeBtn: 1,
            offset: "auto",
            shadeClose: false,
            content: $("#borrow"),
            end: function () {
                $("#borrow").css("visibility", "hidden")
            }
        });
    })
}
function translationTime(week, time) {
    var result = {};
    week = parseInt(week);
    time = parseInt(time);
    if (week === 1) {
        result["week"] = "周一";
    }
    if (week === 2) {
        result["week"] = "周二";
    }
    if (week === 3) {
        result["week"] = "周三";
    }
    if (week === 4) {
        result["week"] = "周四";
    }
    if (week === 5) {
        result["week"] = "周五";
    }
    if (week === 6) {
        result["week"] = "周六";
    }
    if (week === 7) {
        result["week"] = "周日";
    }
    if (time === 1) {
        result["time"] = "8:00 - 10:00"
    }
    if (time === 2) {
        result["time"] = "10:10 - 12:00"
    }
    if (time === 6) {
        result["time"] = "12:00 - 14:00"
    }
    if (time === 3) {
        result["time"] = "14:00 - 16:00"
    }
    if (time === 4) {
        result["time"] = "16:00 - 18:00"
    }
    if (time === 7) {
        result["time"] = "18:00 - 19:20"
    }
    if (time === 5) {
        result["time"] = "19:30 - 21:10"
    }
    if (time === 8) {
        result["time"] = "21:10 - 22:20"
    }
    return result;
}

function getCurrData(dom) {
    var cid = $(dom).attr("data-cid");
    var week = $(dom).attr("data-week");
    var time = $(dom).attr("data-time");
    return [cid,week,time]
}
function setCurrData(cid,week,time,weekNum) {

    $("#cid").val(cid);
    $("input[name='week']").val(week);
    $("input[name='weekNum']").val(weekNum);
    $("input[name='section']").val(time);
    var result = translationTime(week, time);
    $("#time").val(result["week"] + "    " + result["time"]);
}
function notice(title,msg,type) {
    layui.use(['layer'],function () {
        if (type==='notice' || type===null){
            layui.layer.open({
                title:title,
                content:msg
            })
        }
    })
}
function waitShade() {
    layui.use('layer',function () {
        layui.layer.load()
    })
}
function ajaxError(code,status,msg) {

}
function sendData(data,url){
    $.ajax({
        type:"POST"
        ,data:data
        ,url:url
        ,beforeSend:function () {
            waitShade();
        }
        ,success:function (data) {
            data=JSON.parse(data);
            alert(data["msg"]);
            if (data["code"]===1){
                notice("Success",data["msg"],"notice");
                window.location.reload();
            }else{
                notice("Warning",data["msg"],"warning")
            }
        }
        ,error:function (xhr,errInfo,obj) {
            notice("Error","出现错误,请前往BBS相应版块向管理员反馈","warning");
            //todo 加入错误处理模块
        }

        ,complete:function () {
            layui.use('layer',function () {
                layui.layer.closeAll('loading')
            })
        }
    })
}