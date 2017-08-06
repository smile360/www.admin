<?php  
namespace Common\Normal;
use Common\Normal\Httpclient;

/**
 * @todo  短信类;
 * @author  Darker.song
 * @date  2015-12-24 17:00:00
 * */
class Sms{
	
	/*必填参数。用户账号 */
	const name = '15969065929';
	
	/*必填参数。（web平台：基本资料中的接口密码）*/
	const pwd = 'BD6D4DEA27F38E9FE9112457DC4B';
	
	const static_url = 'http://web.cr6868.com/asmx/smsservice.aspx?';
	
	/*接口返回状态码;*/
	var $stateCode = array(
		'-1'=> '系统异常',
		'0' => '提交成功',
		'1' => '含有敏感词汇',
		'2' => '余额不足',
		'3' => '没有号码',
		'4' => '包含sql语句',
		'10' => '账号不存在',
		'11' => '账号注销',
		'12' => '账号停用',
		'13' => 'IP鉴权失败',
		'14' => '格式错误'
	);
	
	/**
	 * @param  array  $config参数数组，包括验证码, 手机号...
	 * @ps 签名 暂时用的MD5(用户名+密码+时间戳)
	 * @return  0/1; 
	 * @ps  0 发送成功;1 发送失败;
	 * */
	public function send($mobile, $content)
	{
		if(empty($mobile) || empty($content))
		{
			return;
		}
		
		$params = array();
		$argv = array( 
			'name'		=>self::name,
			'pwd'		=>self::pwd,
			'content'	=> $content,
			'mobile'	=> $mobile,
			'stime'		=>'',
			'sign'		=>'E库宝',
			'type'		=>'pt',
			'extno'		=>''
		);
		
		foreach ($argv as $key => $value) { 
			$sub = $key . '=' . urlencode($argv[$key]);
			array_push($params, $sub);
		}
		
		$url = self::static_url . join('&', $params);
		$client = new Httpclient();
		$res = $client->quickPost($url, "");
		$state = substr($res, 0, 1 );
		$msg = $this->getCode($state);
		$data = array('code' => $state, 'msg' => $msg);
		
		return $data;
	}
	
	private function getCode($key)
	{
		return $this->stateCode[$key];
	}
}
