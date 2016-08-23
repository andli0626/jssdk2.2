var wxJSSDK = {			// 声明微信全局变量，防止污染外部环境
		
	version : "1.0",	// 版本号
	appName : "", 		// 使用当前库的开发者，可以配置应用名字
	isReady : false,	// 微信JS SDK是否初始化完毕
	access_token : "",	// 令牌
	ticket : "",		// 微信临时票据
	
	config : {
		debug : true, 													// 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
		appId : 'wxbb878801f06451f2', 									// 必填，公众号的唯一标识
		timestamp : Math.ceil(new Date().getTime() / 1000).toString(), 	// 必填，生成签名的时间戳
		nonceStr : 'html5waibao_wxJSSDK', 								// 必填，生成签名的随机串
		signature : '',													// 必填，签名，见附录1
		jsApiList : [ "onMenuShareTimeline" ]							// 必填，需要使用的JS接口列表，所有JS接口列表见附录2
	
	},
	
	/** 函数功能：初始化 **/
	init : function() {
		if (!wx) {			// 验证是否存在微信的js组件
			alert("微信接口调用失败，请检查是否引入微信js！");
			return;
		}
		var that = this;	// 保存当前作用域，方便回调函数使用
		
		// 获取令牌
		this.wx_get_token(function(data) {
			if (data.access_token) {
				// 缓存
				Cookie.Set("access_token", data.access_token, 3600);
				that.access_token = data.access_token;
			}
			// 获取票据
			that.wx_get_ticket(function(data) {
				if (data.ticket) {
					// 缓存
					Cookie.Set("ticket", data.ticket, 3600);
					that.ticket = data.ticket
				}
				// 获取签名
				that.wx_get_signature(function(data) {
					that.config.signature = data;
					that.initWx(function() {	// 初始化微信接口
						
						// 初始化完成后的执行
						
					});
				});
			});
		});
	},
	// 获取令牌
	wx_get_token : function(call) {
		this.access_token = Cookie.Get("access_token");
		if (!Cookie.Get("access_token")) {
			$.get("./API/wx_get_token.php", {}, function(data) {
				debugger
				call && call(data);
			}, "json");
			return;
		}
		call && call({});
	},
	// 获取票据
	wx_get_ticket : function(call) {
		this.ticket = Cookie.Get("ticket");
		if (!this.ticket) {
			$.get("./API/wx_get_jsapi_ticket.php", {
				access_token : this.access_token
			}, function(data) {
				debugger
				call && call(data);
			}, "json");
			return;
		}
		call && call({});
	},
	// 获取签名
	wx_get_signature : function(call) {
		$.get("./API/wx_get_signature.php", {
			ticket : this.ticket, 				// 必填，生成签名的时间戳
			timestamp : this.config.timestamp, 	// 必填，生成签名的时间戳
			nonceStr : this.config.nonceStr, 	// 必填，生成签名的随机串
			url : window.location.href			
		
		}, function(data) {
			debugger
			call && call(data);
		});
	},
	
	/** 初始化微信接口 **/
	initWx : function(call, errorCall) {
		var that = this;
		wx.config(this.config);	// 初始化配置
		
		wx.ready(function() {
			that.isReady = true;
			console.log("初始化成功")
			call && call();
		});
		
		wx.error(function(res) {
			that.isReady = "false";
			errorCall && errorCall();
		});
	}
}

/** 执行初始化 **/
wxJSSDK.init();