### pdf
````
// ubuntu install pdf dependence
apt-cache search libXrender
apt-get install libfontconfig1 libxrender1
chown -R root:root vendor/nitmedia/wkhtml2pdf/src/Nitmedia/Wkhtml2pdf/lib
````

#### Backend

- 添加新的预警活动
- 在新的预警活动中添加需要参加的人，默认添加整个公司的人，也可以通过单独添加或者上传excel添加。
- 每天定期request公司人员名单，如果有新员工就添加到数据库中。
- 设计扫二维码签到功能。

#### Frontend

- 发生预警的时候，扫描员工二维码，确定员工是否到来。
- 导出统计表格供相关部门使用。