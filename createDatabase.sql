/**
	> source ./createDatabase.sql;
	> show databases;

	> create database `db_name`;
	> drop database `db_name`;
	> show tables;
	> alter table `table_name` type=InnoDB;
    > truncate table `table_name`;

	// 表结构
	> describe `table_name`;
	> alter table `table_name` rename `new_table_name`;
	// 删除字段
	> alter table `table_name` drop column `field_name`;
	// 增加一个新列
	> alter table `table_name` add `field_name` int(5) default 0 not null comment '说明' after `field_name`;
	// 修改列类型
	> alter table `table_name` change `column_name` not null default '' comment '说明';
	// 加主关键字的索引
	> alter table `table_name` add index `emp_name` (`name`);
    // 表数据
    > delete from 表名 where 表达式
    > insert into `table_name` (username, password, tel, status) VALUES ('admin', '123456', '15011100011', 0);
    权限：
		1、创建一个具有root权限，可从任何IP登录的用户sina，密码为password
		> grant all privileges on *.* to sina@% identified by 'password';

		2、创建一个具有"数据操作"、"结构操作"权限，只能从192.168.1.***登录的用户sina，密码为password
		> grant select, insert, update, delete, file, create, drop, index, alter, create temporary tables, create view, show view, create routine, alter routine, execute on *.* to sina@192.168.1.% identified by 'password';

		3、创建一个只拥有"数据操作"权限，只能从192.168.1.24登录，只能操作rewin数据库的zhangyan表的用户sina，密码为password
		> grant select, insert, update, delete ON  rewin.zhangyan to sina@192.168.1.24 identified by password;

		4、创建一个拥有"数据操作"、"结构操作"权限，可从任何IP登录，只能操作rewin数据库的用户sina，密码为zhangyan
		> grant select, insert, update, delete, create, drop, index, alter, create temporary tables, create view, show view, create routine, alter routine, execute on rewin.* to sina@% identified by password;

		5、删除用户
		drop user sina@%;

	6.MySQL中将字符串aaa批量替换为bbb的SQL语句
	> update `table_name` set `field_name` = REPLACE (`field_name`, 'aaa', 'bbb');
 */
create database `mihua_loan`;

drop table if exists `mihua_sms`;
CREATE TABLE `mihua_sms`(
    `id` int(10) unsigned not null auto_increment comment '自动编号',
    `phone` varchar(20) not null default '' comment '电话',
    `key` varchar(20) not null default '' comment '验证码',
    `send_type` tinyint not null default 0 comment '短信类型 0:注册 1:找回密码 2:系统通知',
    `timestamp` int(6) not null default 0 comment '短信发送时间',
    `desc` varchar(250) not null default '' comment '发送信息',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;

drop table if exists `mihua_site`;
CREATE TABLE `mihua_site`(
    `id` int(10) unsigned not null auto_increment comment '自动编号',
    `name` varchar(50) not null default '' comment '站点名称',
    `key` varchar(20) not null default '' comment '站点编码',
    `rate` int(6) not null default 0 comment '费率',
    `manage` int(6) not null default 0 comment '管理成本',
    `cost` int(6) not null default 0 comment '手续费',
    `desc` varchar(250) not null default '' comment '站点描述',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
insert into `mihua_site` (`name`, `key`, `rate`, `manage`, `cost`, `desc`) VALUES
  ('米花现金贷', 'mihua_loan', 3, 700, 500, '');

drop table if exists `mihua_period`;
CREATE TABLE `mihua_period`(
    `id` int(10) unsigned not null auto_increment comment '自动编号',
    `sid` int(10) not null default 0 comment '所属站点',
    `title` varchar(50) not null default '' comment '名称',
    `amount` int(6) not null default 0 comment '数量',
    `rate` int(6) not null default 0 comment '费率',
    `desc` varchar(250) not null default '' comment '描述',
    `status` tinyint not null default 0 comment '是否启用 0:未启用 1:启用',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
drop table if exists `mihua_bankroll`;
CREATE TABLE `mihua_bankroll`(
    `id` int(10) unsigned not null auto_increment comment '自动编号',
    `sid` int(10) not null default 0 comment '所属站点',
    `title` varchar(50) not null default '' comment '名称',
    `amount` int(6) not null default 0 comment '数量',
    `rate` int(6) not null default 0 comment '费率',
    `desc` varchar(250) not null default '' comment '描述',
    `status` tinyint not null default 0 comment '是否启用 0:未启用 1:启用',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
-- insert into `mihua_bankroll` (`sid`, `title`, `amount`, `rate`, `desc`, `status`) VALUES ('1','借款5天',5,8,'借款5天服务费将收取总金额的8%',1);

drop table if exists `mihua_users`;      -- 用户表
CREATE TABLE `mihua_users`(
    `id` int(11) unsigned not null auto_increment,
    `username` varchar(50) not null default '' comment '登录名 默认为电话号码,认证成功后为真实用户名',
    `password` varchar(50) not null default '' comment '密码',
    `phone` varchar(20) not null default '' comment '电话',
    `phonecode` varchar(20) not null default '' comment '服务密码',
    `phonestatus` tinyint not null default 0 comment '服务商认证状态 0:未审核 1:重新认证 2:认证中 3:成功认证',
    `idcard` tinyint not null default 0 comment '实名认证状态  0:未审核 1:重新认证 2:认证中 3:成功认证',
    `bankcard` tinyint not null default 0 comment '银行卡号绑定状态  0:未绑定 1:重新绑定 2:绑定中 3:绑定认证',
    `work` tinyint not null default 0 comment '身份认证状态 0:未审核 1:重新认证 2:认证中  3:成功认证',
    `contacts` tinyint not null default 0 comment '联系人状态 0:未审核 1:重新认证 2:认证中  3:成功认证',
    `city` int(6) not null default 0 comment '城市',
    `province` int(6) not null default 0 comment '省份',
    `area` int(6) not null default 0 comment '区|县',
    `address` varchar(250) not null default '' comment '现住地址',
    `invite_code` varchar(20) not null default '' comment '邀请码',
    `email` varchar(120) not null default '' comment '常用邮箱',
    `qq` varchar(20) not null default '' comment '腾讯QQ',
    `degrees` varchar(4) not null default '' comment '学历',
    `marriage` varchar(4) not null default '' comment '婚姻状态',
    `role` int(4) not null default 0 comment '权限 8888为系统管理员',
    `status` tinyint not null default 0 comment '状态 0:未启用  1:黑名单 2:禁用 3:启用',
    `created_time` int(11) not null default 0 comment '创建时间',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
alter table `mihua_users` add `period` varchar(250) default '' not null comment '居住时长' after `address`;
update `mihua_users` set `username`=replace(phone, substring(phone,4,8), '****') where `id` = 7;

drop table if exists `mihua_idcard`;     -- 实名认证信息
CREATE TABLE `mihua_idcard`(
    `id` int(11) unsigned not null auto_increment,
    `uid` int(11) not null default 0 comment '用户编号',
    `no` varchar(50) not null default '' comment '身份证号',
    `username` varchar(250) not null comment '姓名',
    `birthday` int(11) not null default 0 comment '生日 时间戳',
    `gender` tinyint not null default '9' comment '性别 0:女性 1:男性 9:未知',
    `face` varchar(200) not null default 0 comment '手持身份证图',
    `front` varchar(250) not null comment '身份证正面图',
    `back` varchar(250) not null comment '身份证背面图',
    `status` tinyint not null default 0 comment '状态 0:未审核 1:重新认证 2:认证中 3:成功认证 4:信审认证成功',
    `verify_time` int(11) not null default 0 comment '审核时间',
    `verify_uid` int(11) not null default 0 comment '审核人',
    `created_time` int(11) not null default 0 comment '创建时间',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
alter table `mihua_idcard` add `message` varchar(250) default '' not null comment '审核结果' after `verify_uid`;
alter table `mihua_idcard` add `face_id` varchar(50) default '' not null comment '信审编号_手持身份证图' after `face`;
alter table `mihua_idcard` add `front_id` varchar(50) default '' not null comment '信审编号_身份证正面图' after `front`;
alter table `mihua_idcard` add `back_id` varchar(50) default '' not null comment '信审编号_身份证背面图' after `back`;

drop table if exists `mihua_contacts`;   -- 联系人信息
CREATE TABLE `mihua_contacts`(
    `id` int(11) unsigned not null auto_increment,
    `uid` int(11) not null default 0 comment '用户编号',
    `relations` varchar(4) not null default '' comment '关系编号',
    `cname` varchar(50)  not null default 0 comment '姓名',
    `mobile` varchar(20) not null default 0 comment '电话',
    `address` varchar(250) not null default '' comment '详细地址',
    `status` tinyint not null default 0 comment '状态 0:未审核 1:重新认证 2:认证中 3:成功认证',
    `verify_time` int(11) not null default 0 comment '审核时间',
    `verify_uid` int(11) not null default 0 comment '审核人',
    `created_time` int(11) not null default 0 comment '创建时间',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
alter table `mihua_contacts` add `message` varchar(250) default '' not null comment '审核结果' after `verify_uid`;


drop table if exists `mihua_bankcard`;   -- 银行卡绑定信息
CREATE TABLE `mihua_bankcard`(
    `id` int(11) unsigned not null auto_increment,
    `uid` int(11) not null default 0 comment '用户编号',
    `no` varchar(50) not null default '' comment '银行卡号',
    `card_type` tinyint default 0 not null comment '卡类型 默认0:未知 2:储蓄卡 3:信用卡',
    `idcard` varchar(50) not null default '' comment '身份证号',
    `username` varchar(250) not null comment '姓名',
    `bank_code` varchar(20) default '' not null comment '银行编码',
    `bank_name` varchar(50) default '' not null comment '银行名称',
    `bank_province` int(6) not null default 0 comment '银行地址[省]',
    `bank_city` int(6) not null default 0 comment '银行地址[市]',
    `status` tinyint not null default 0 comment '状态 0:未审核 1:重新认证 2:认证中 3:成功认证',
    `agreeno` varchar(20) default '' not null comment '签约协议号',
    `verify_time` int(11) not null default 0 comment '审核时间',
    `verify_uid` int(11) not null default 0 comment '审核人',
    `message` varchar(250) default '' not null comment '审核结果',
    `created_time` int(11) not null default 0 comment '创建时间',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;

alter table `mihua_bankcard` drop column `back_name`;
alter table `mihua_bankcard` add `bank_name` varchar(50) default '' not null comment '银行名称' after `bank_code`;


drop table if exists `mihua_work`;   -- 身份信息
CREATE TABLE `mihua_work`(
    `id` int(11) unsigned not null auto_increment,
    `uid` int(11) not null default 0 comment '用户编号',
    `province` int(6) not null default 0 comment '省份',
    `city` int(6) not null default 0 comment '城市',
    `area` int(6) not null default 0 comment '区|县',
    `address` varchar(250) not null default '' comment '详细地址',
    `company` varchar(250) not null default '' comment '单位名称',
    `phone` varchar(250) not null default '' comment '单位名称联系电话',
    `identity` varchar(4) not null default '' comment '身份 SI01:学生 SI02:在职人员 SI03:企业负责人 SI04:自由职业 SI05:无业 SI06:退休 ',
    `job` varchar(4) not null default 'WT99' comment '职位 WT01:高级领导 WT02:中级领导 WT03:一般员工 WT98:其它 WT99:未知',
    `coordinate` varchar(50) not null default 0 comment '坐标[经纬度] longitude,latitude',
    `chsi` varchar(250) not null default '' comment '学信网图',
    `status` tinyint not null default 0 comment '状态 0:未审核 1:重新认证 2:认证中 3:成功认证',
    `verify_time` int(11) not null default 0 comment '审核时间',
    `verify_uid` int(11) not null default 0 comment '审核人',
    `created_time` int(11) not null default 0 comment '创建时间',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
alter table `mihua_work` add `message` varchar(250) default '' not null comment '审核结果' after `verify_uid`;

drop table if exists `mihua_orders`; -- 订单表
CREATE TABLE `mihua_orders`(
    `id` int(11) unsigned not null auto_increment,
    `uid` int(11) not null default 0 comment '用户编号',
    `no` varchar(50) not null default '' comment '合同编号',
    `purpose` varchar(4) not null default '' comment '借款用途',
    `limit` int(11) not null default 0 comment '申请额度',
    `period` int(11) not null default 0 comment '申请期限 5:5天 10:10天',
    `deposit` int(11) not null default 0 comment '到账金额',
    `rate` int(4) not null default 0 comment '年利率',
    `insurance` int(4) not null default 0 comment '保险费率',
    `term` int(2) not null  default 1 comment '期数',
    `repay_type` tinyint not null default 4 comment '还款方式 1:按月归还 2:按季归还 3:按年归还 4:一次性归还 5:分期返还 9:其它',
    `auditing_time` int(11) not null default 0 comment '审核时间',
    `expire_time` int(11) not null default 0 comment '到期时间',
    `pay_time` int(11) not null default 0 comment '放款时间',
    `expire_cost` int(11) not null default 0 comment '延期总费用',
    `expire_count` int(11) not null default 4 comment '剩余延期次数 默认4次',
    `expire_info` varchar(250) not null default '' comment '延期信息 timestamp,timestamp,timestamp',
    `overdue_cost` int(11) not null default 0 comment '逾期总费用',
    `repay_cost` int(11) not null default 0 comment '还款总金额 申请借款金额＋逾期金额',
    `repay_time` int(11) not null default 0 comment '还款时间',
    `progress` int(4) not null default 0 comment '当前进度 0:待审核 1:审核失败 10:放款中 11:放款成功 20:延期失败 21:延期申请中 22:延期成功 30:逾期中 100:还款成功',
    `message` varchar(250) default '' not null comment '审核结果',
    `status` tinyint not null default 0 comment '状态 0:正常 1:用户确认 2:用户取消',
    `created_time` int(11) not null comment '创建时间',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8 comment='借款订单信息表';


drop table if exists `mihua_deduct`;  -- 扣款记录
CREATE TABLE `mihua_deduct`(
    `id` int(11) unsigned not null auto_increment,
    `uid` int(11) not null default 0 comment '用户编号',
    `oid` int(11) not null default 0 comment '订单编号',
    `days` int(3) default 5 not null comment '续期天数',
    `amount` int(6) not null default 0 comment '金额',
    `pay_type` tinyint not null default 0 comment '扣款类型 0:还款 1:第一次续期 2:第二次续期 3:第三次续期 4:第四次续期',
    `status` tinyint not null default 0 comment '状态 0:提交申请 1:WAITING等待支付 2:PROCESSING银行支付处理中 3:REFUND退款 4:FAILURE扣款失败 9:SUCCESS扣款成功',
    `paybill` varchar(20) default '' not null comment '连连支付订单号',
    `card_no` varchar(20) default '' not null comment '银行名称',
    `bank_code` varchar(10) default '' not null comment '银行编号',
    `bank_name` varchar(50) default '' not null comment '银行名称',
    `settle_date` varchar(20) default '' not null comment '清算日期',
    `desc` varchar(250) default '' not null comment '其他支付结果信息',
    `created_time` int(11) not null default 0 comment '创建时间',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;

drop table if exists `mihua_risk`;  -- 风控审核结果
CREATE TABLE `mihua_risk`(
    `id` int(11) unsigned not null auto_increment,
    `uid` int(11) not null default 0 comment '用户编号',
    `noBusb` varchar(50) not null default 0 comment '申请审核订单号',
    `noBus` varchar(50) not null default 0 comment '信申系统编号',
    `creditLimit` int(6) not null default 0 comment '授信额度',
    `creditTerm` int(6) not null default 0 comment '授信期数',
    `reasonCode` varchar(250) not null default '' comment '决策原因编码',
    `reason` varchar(250) not null default '' comment '决策原因编码',
    `interestCode` varchar(50) not null default '' comment '利率码',
    `feeRateCode` varchar(50) not null default '' comment '费率码',
    `amtDownpay` varchar(50) not null default '' comment '首付款',
    `amtMonthrepay` varchar(50) not null default '' comment '每期还款',
    `dataProd` text not null comment '审批明细数据',
    `status` tinyint not null default 0 comment '状态',
    `created_time` int(11) not null default 0 comment '创建时间',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;


drop table if exists `mihua_gender`;
CREATE TABLE `mihua_gender`(
    `id` int(10) unsigned not null auto_increment comment '性别自动编号',
    `value` tinyint not null default 9 comment '值 0:女性 1:男性 9:未知',
    `key` varchar(2) not null default '' comment '性别编号 F:女性 M:男性 U:未知',
    `lable` varchar(4) not null comment '性别',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
insert into `mihua_gender` (`value`, `key`, `lable`) VALUES
	(0, 'F', '女性'),
	(1, 'M', '男性'),
	(9, 'U', '未知');

drop table if exists `mihua_photo_type`;
CREATE TABLE `mihua_photo_type`(
    `id` int(10) unsigned not null auto_increment comment '照片类型自动编号',
    `key` varchar(4) not null default '' comment '编号',
    `lable` varchar(20) not null comment '值',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
insert into `mihua_photo_type` (`key`, `lable`) VALUES
  ('P01', '申请人照片'),
  ('P02', '身份证正面'),
  ('P03', '身份证反面'),
  ('P04', '代扣银行卡'),
  ('P05', '工作证明或学生证明'),
  ('P06', '社保卡'),
  ('P07', '工资卡及流水'),
  ('P08', '居住证明'),
  ('P09', '户口本'),
  ('P10', '房产证'),
  ('P11', '公安照片'),
  ('P12', '客户与TA合照'),
  ('P13', '购货小票'),
  ('P14', '人脸识别照片'),
  ('P99', '其他');

drop table if exists `mihua_relation`;
CREATE TABLE `mihua_relation`(
    `id` int(10) unsigned not null auto_increment comment '人际关系自动编号',
    `key` varchar(4) not null default '' comment '编号',
    `lable` varchar(10) not null comment '值',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
insert into `mihua_relation` (`key`, `lable`) VALUES
  ('CR01', '配偶'),
  ('CR02', '父亲'),
  ('CR03', '母亲'),
  ('CR04', '兄弟'),
  ('CR05', '姐妹'),
  ('CR06', '子女'),
  ('CR07', '同学'),
  ('CR08', '同事'),
  ('CR09', '朋友'),
  ('CR99', '其他');

drop table if exists `mihua_id_type`;
CREATE TABLE `mihua_id_type`(
    `id` int(10) unsigned not null auto_increment comment '证件类型自动编号',
    `key` varchar(4) not null default '' comment '编号',
    `lable` varchar(20) not null comment '值',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
insert into `mihua_id_type` (`key`, `lable`) VALUES
  ('ID01', '身份证'),
  ('ID02', '户口簿'),
  ('ID03', '护照'),
  ('ID04', '军官证'),
  ('ID05', '士兴证'),
  ('ID06', '港澳居民来往内地通行证'),
  ('ID07', '台湾同胞来往内地通行证'),
  ('ID08', '临时身份证'),
  ('ID09', '外国人居留证'),
  ('ID10', '警官证'),
  ('ID11', '香港身份证'),
  ('ID12', '澳门身份证'),
  ('ID13', '台湾身份证'),
  ('ID99', '其它证件');

drop table if exists `mihua_loan_purpose`;
CREATE TABLE `mihua_loan_purpose`(
    `id` int(10) unsigned not null auto_increment comment '借款用途自动编号',
    `key` varchar(4) not null default '' comment '编号',
    `lable` varchar(4) not null comment '值',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8
insert into `mihua_loan_purpose` (`key`, `lable`) VALUES
  ('LP01', '家用电器'),
  ('LP02', '数码产品'),
  ('LP03', '国内教育'),
  ('LP04', '出境留学'),
  ('LP05', '装修'),
  ('LP06', '婚庆'),
  ('LP07', '旅游'),
  ('LP08', '租赁'),
  ('LP09', '医疗'),
  ('LP10', '美容'),
  ('LP11', '家其'),
  ('LP12', '生活用品'),
  ('LP99', '其它');

drop table if exists `mihua_marital_status`;
CREATE TABLE `mihua_marital_status`(
    `id` int(10) unsigned not null auto_increment comment '婚姻状况自动编号',
    `key` varchar(4) not null default '' comment '编号',
    `lable` varchar(10) not null comment '值',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
insert into `mihua_marital_status` (`key`, `lable`) VALUES
  ('10', '未婚'),
  ('20', '已婚'),
  ('21', '初婚'),
  ('22', '再婚'),
  ('23', '复婚'),
  ('30', '丧偶'),
  ('40', '离婚'),
  ('90', '其它');

drop table if exists `mihua_education_level`;
CREATE TABLE `mihua_education_level`(
    `id` int(10) unsigned not null auto_increment comment '教育程度自动编号',
    `key` varchar(4) not null default '' comment '编号',
    `lable` varchar(10) not null comment '值',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
insert into `mihua_education_level` (`key`, `lable`) VALUES
  ('D01', '小学'),
  ('D02', '初中'),
  ('D03', '高中'),
  ('D04', '专科'),
  ('D05', '专科(高职)'),
  ('D06', '专升本'),
  ('D07', '本科'),
  ('D08', '第二学士学位'),
  ('D09', '硕士研究生'),
  ('D10', '博士研究生'),
  ('D99', '未知');

drop table if exists `mihua_area`;   -- 城市
CREATE TABLE `mihua_area`(
    `id` int(11) unsigned not null auto_increment,
    `key` int(11) not null default 0 comment '城市编号',
    `pid` int(11) not null default 0 comment '上级编号',
    `code` int(11) not null default 0 comment '城市代码',
    `lable` varchar(50)  not null default '' comment '名称',
    `title` varchar(50)  not null default '' comment '名称',
    `level` tinyint not null default 0 comment '级别 0:未知 1:第一级 2:第二级 3:第三级',
    `status` tinyint not null default 0 comment '状态 0:未启用 1:已启用',
    primary key (`id`)
) engine = myisam DEFAULT charset=utf8;
