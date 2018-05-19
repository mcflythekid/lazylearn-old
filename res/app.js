var ctx = "";

var $app = ((e)=>{
	
	// storage-------------------------
	e.getData = ()=>{
		var data = JSON.parse(localStorage.getItem('data'));
		if (data) return data;
		return {};
	};
	e.setData = (data)=>{
		localStorage.setItem('data', JSON.stringify(data));
	};
	
	// init -----------------------------------------
	e.init = ()=>{
		$(document).ready(()=>{
			initPing();
		});
	};
	var initPing = ()=>{
		var ping = (in_cb, out_cb)=>{
			$tool.axios(ctx + "/user/ping.api.php").then((res)=>{
				if (res.data.status === 'ok'){
					in_cb(res.data.data);
				} else if (res.data.status === 'error'){
					out_cb();
				}
			}).catch((err)=>{
				console.log(err);
			});
		};
		ping($top.renderIn, $top.renderOut);
		setInterval(()=>{
			ping($top.renderIn, $top.renderOut);
		}, 10000);
	};
	
	// auth ----------------------------
	e.logout = ()=>{
		$tool.axios.post(ctx + "/user/logout.api.php").then((res)=>{
			if (res.data.status === 'ok'){
				e.setData({});
				$top.renderOut();
				window.location = ctx + "/user/login.php";
			}
		}).catch((err)=>{
			console.log(err);
		});
	};
	e.require_authed = ()=>{
		if (!e.getData().token){
			window.location = ctx + "/user/login.php";
		}
	};


	
	return e;
	
})({});
$app.init();