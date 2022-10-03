<?php
//2011-3-11

//这些域名将禁止访问,
//我已经添加一些了，下面这些域名存在网页浏览器功能，如果嵌套使用可能出现未预料的情况...
//并不是故意屏蔽某些站,请谅解!!
//必须是小写的。。。

//可以把这里当黑名单

$b_set['forbid'] = array(
				$b_set['host'],
				'c.139.com',
				'w.159.com',
				'browser.ggg.cn',
				'old.ggg.cn',
				'wap.netgets.net',
				'jiuwap.com',
				'www.jiuwap.com',
				'wap.jiuwap.com',
				'bbs.jiuwap.com',
				'm.jiuwap.com',
				);