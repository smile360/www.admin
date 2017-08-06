<?php
namespace Common\Normal;
use Common\Normal\Httpclient;
/**
 * 微信操作基类
 * */
class Wechat{

    /**消息类型**/
    const MSGTYPE_TEXT = 'text';
    const MSGTYPE_IMAGE = 'image';
    const MSGTYPE_LOCATION = 'location';
    const MSGTYPE_LINK = 'link';
    const MSGTYPE_EVENT = 'event';
    const MSGTYPE_MUSIC = 'music';
    const MSGTYPE_NEWS = 'news';
    const MSGTYPE_VOICE = 'voice';
    const MSGTYPE_VIDEO = 'video';
    /**请求地址**/
    const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
    const API_FILE_URL_PREFIX = 'http://file.api.weixin.qq.com/cgi-bin';
    const AUTH_URL = '/token?grant_type=client_credential&';
    /**菜单**/
    const MENU_CREATE_URL = '/menu/create?';
    const MENU_GET_URL = '/menu/get?';
    const MENU_DELETE_URL = '/menu/delete?';
    /**二维码**/
    const QRCODE_CREATE_URL = '/qrcode/create?'; //请求
    const QR_SCENE = 0;
    const QR_LIMIT_SCENE = 1;
    const QRCODE_IMG_URL = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';//获取
    /**用户分组**/
    const USER_GET_URL = '/user/get?';
    const USER_INFO_URL = '/user/info?';
    const GROUP_GET_URL = '/groups/get?';
    const GROUP_CREATE_URL = '/groups/create?';
    const GROUP_UPDATE_URL = '/groups/update?';
    const GROUP_MEMBER_UPDATE_URL = '/groups/members/update?';
    /**客服消息**/
    const CUSTOM_SEND_URL = '/message/custom/send?';
    /**授权**/
    const OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
    const OAUTH_AUTHORIZE_URL = '/authorize?';
    const OAUTH_TOKEN_PREFIX = 'https://api.weixin.qq.com/sns/oauth2';
    const OAUTH_TOKEN_URL = '/access_token?';
    const OAUTH_REFRESH_URL = '/refresh_token?';
    const OAUTH_USERINFO_URL = 'https://api.weixin.qq.com/sns/userinfo?';
    /**pay**/
    const PAY_DELIVERNOTIFY = 'https://api.weixin.qq.com/pay/delivernotify?';
    const PAY_ORDERQUERY = 'https://api.weixin.qq.com/pay/orderquery?';
    /**模板发送 htx: 模板需要在微信后台添加  可以获得模板id字符串  有现成的 也可以自己加 **/
    const TEMPLATE_MESSAGE_URL = 'https://api.weixin.qq.com/cgi-bin/message/template/send?';
    /**多客服聊天 估计是用不上**/
    const CUSTOM_SERVICE_GET_RECORD = '/customservice/getrecord?';
    const CUSTOM_SERVICE_GET_KFLIST = '/customservice/getkflist?';
    const CUSTOM_SERVICE_GET_ONLINEKFLIST = '/customservice/getkflist?';
    /**微信sdk**分享**/
    const JS_API_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=';
    const TICKETKEY = 'wechat_api_js_ticket';
    const TOKENKEY = 'wechat_api_token_key';
    //微信生存周期单位秒
    const WX_TOKEN_LIFE  = '300';

    private $token;
    private $appid;
    private $appsecret;
    private $access_token;
    private $user_token;
    private $partnerid;
    private $partnerkey;
    private $paysignkey;
    private $_msg;
    private $_funcflag = false;
    private $_receive;
    public $debug = false;
    public $errCode = 40001;
    public $errMsg = "no access";
    private $_logcallback;
    private $_cache;
    private $js_ticket;
    public static $default = 'default';
    public static $instances = array();
	
	var $request; 

    public function __construct($options) {
        $this->token = isset($options['token']) ? $options['token'] : '';
        $this->appid = isset($options['appid']) ? $options['appid'] : '';
        $this->appsecret = isset($options['appsecret']) ? $options['appsecret'] : '';
        $this->partnerid = isset($options['partnerid']) ? $options['partnerid'] : '';
        $this->partnerkey = isset($options['partnerkey']) ? $options['partnerkey'] : '';
        $this->paysignkey = isset($options['paysignkey']) ? $options['paysignkey'] : '';
        $this->debug = isset($options['debug']) ? $options['debug'] : false;
        $this->_logcallback = isset($options['logcallback']) ? $options['logcallback'] : false;
		$this->request = new Httpclient();
    }
    /**
     * For weixin server validation
     */
    public function checkSignature() {
        $signature = isset($_GET["signature"]) ? $_GET["signature"] : '';
        $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : '';
        $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : '';
        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * For weixin server validation
     * @param bool $return 是否返回
     */
    public function valid($return = false) {
        $echoStr = isset($_GET["echostr"]) ? $_GET["echostr"] : '';
        if ($return) {
            if ($echoStr) {
                if ($this->checkSignature())
                    return $echoStr;
                else
                    return false;
            } else
                return $this->checkSignature();
        } else {
            if ($echoStr) {
                if ($this->checkSignature())
                    die($echoStr);
                else
                    die('no access');
            } else {
                if ($this->checkSignature())
                    return true;
                else
                    die('no access');
            }
        }
        return false;
    }
    /**
     * 设置发送消息
     * @param array $msg 消息数组
     * @param bool $append 是否在原消息数组追加
     */
    public function Message($msg = '', $append = false) {
        if (is_null($msg)) {
            $this->_msg = array();
        } elseif (is_array($msg)) {
            if ($append)
                $this->_msg = array_merge($this->_msg, $msg);
            else
                $this->_msg = $msg;
            return $this->_msg;
        } else {
            return $this->_msg;
        }
    }
    public function setFuncFlag($flag) {
        $this->_funcflag = $flag;
        return $this;
    }
    private function log($log) {
        if ($this->debug && function_exists($this->_logcallback)) {
            if (is_array($log))
                $log = print_r($log, true);
            return call_user_func($this->_logcallback, $log);
        }
    }
    /**
     * 获取微信服务器发来的信息
     */
    public function getRev() {
        if ($this->_receive)
            return $this;
        $postStr = file_get_contents("php://input");
        $this->log($postStr);
        if (!empty($postStr)) {
            $this->_receive = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        return $this;
    }
    /**
     * 获取消息发送者
     */
    public function getRevFrom() {
        if (isset($this->_receive['FromUserName']))
            return $this->_receive['FromUserName'];
        else
            return false;
    }

    /**
     * 获取消息接受者
     */
    public function getRevTo() {
        if (isset($this->_receive['ToUserName']))
            return $this->_receive['ToUserName'];
        else
            return false;
    }

    /**
     * 获取接收消息的类型
     */
    public function getRevType() {
        if (isset($this->_receive['MsgType']))
            return $this->_receive['MsgType'];
        else
            return false;
    }

    /**
     * 获取消息ID
     */
    public function getRevID() {
        if (isset($this->_receive['MsgId']))
            return $this->_receive['MsgId'];
        else
            return false;
    }

    /**
     * 获取消息发送时间
     */
    public function getRevCtime() {
        if (isset($this->_receive['CreateTime']))
            return $this->_receive['CreateTime'];
        else
            return false;
    }

    /**
     * 获取接收消息内容正文
     */
    public function getRevContent() {
        if (isset($this->_receive['Content']))
            return $this->_receive['Content'];
        else if (isset($this->_receive['Recognition'])) //获取语音识别文字内容，需申请开通
            return $this->_receive['Recognition'];
        else
            return false;
    }
    /**
     * 获取接收消息图片
     */
    public function getRevPic() {
        if (isset($this->_receive['PicUrl']))
            return $this->_receive['PicUrl'];
        else
            return false;
    }
    /**
     * 获取接收消息链接
     */
    public function getRevLink() {
        if (isset($this->_receive['Url'])) {
            return array(
                'url' => $this->_receive['Url'],
                'title' => $this->_receive['Title'],
                'description' => $this->_receive['Description']
            );
        } else
            return false;
    }
    /**
     * 获取接收地理位置
     */
    public function getRevGeo() {
        if (isset($this->_receive['Location_X'])) {
            return array(
                'x' => $this->_receive['Location_X'],
                'y' => $this->_receive['Location_Y'],
                'scale' => $this->_receive['Scale'],
                'label' => $this->_receive['Label']
            );
        } else
            return false;
    }
    /**
     * 获取上报地理位置事件
     */
    public function getRevEventGeo() {
        if (isset($this->_receive['Latitude'])) {
            return array(
                'x' => $this->_receive['Latitude'],
                'y' => $this->_receive['Longitude'],
                'precision' => $this->_receive['Precision'],
            );
        } else
            return false;
    }
    /**
     * 获取接收事件推送
     */
    public function getRevEvent() {
        if (isset($this->_receive['Event'])) {
            return array(
                'event' => $this->_receive['Event'],
                'key' => $this->_receive['EventKey'],
            );
        } else
            return false;
    }
    /**
     * 获取接收语言推送
     */
    public function getRevVoice() {
        if (isset($this->_receive['MediaId'])) {
            return array(
                'mediaid' => $this->_receive['MediaId'],
                'format' => $this->_receive['Format'],
            );
        } else
            return false;
    }
    /**
     * 获取接收视频推送
     */
    public function getRevVideo() {
        if (isset($this->_receive['MediaId'])) {
            return array(
                'mediaid' => $this->_receive['MediaId'],
                'thumbmediaid' => $this->_receive['ThumbMediaId']
            );
        } else
            return false;
    }
    /**
     * 获取接收TICKET
     */
    public function getRevTicket() {
        if (isset($this->_receive['Ticket'])) {
            return $this->_receive['Ticket'];
        } else
            return false;
    }
    /**
     * 获取二维码的场景值
     */
    public function getRevSceneId() {
        if (isset($this->_receive['EventKey'])) {
            return str_replace('qrscene_', '', $this->_receive['EventKey']);
        } else {
            return false;
        }
    }
    public static function xmlSafeStr($str) {
        return '<![CDATA[' . preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', $str) . ']]>';
    }
    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function data_to_xml($data) {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml .= "<$key>";
            $xml .= ( is_array($val) || is_object($val)) ? self::data_to_xml($val) : self::xmlSafeStr($val);
            list($key, ) = explode(' ', $key);
            $xml .= "</$key>";
        }
        return $xml;
    }
    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public function xml_encode($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8') {
        if (is_array($attr)) {
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<{$root}{$attr}>";
        $xml .= self::data_to_xml($data, $item, $id);
        $xml .= "</{$root}>";
        return $xml;
    }
    /**
     * 设置回复消息
     * Examle: $obj->text('hello')->reply();
     * @param string $text
     */
    public function text($text = '') {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_TEXT,
            'Content' => $text,
            'CreateTime' => time(),
            'FuncFlag' => $FuncFlag
        );
        $this->Message($msg);
        return $this;
    }
    /**
     * 设置回复音乐
     * @param string $title
     * @param string $desc
     * @param string $musicurl
     * @param string $hgmusicurl
     */
    public function music($title, $desc, $musicurl, $hgmusicurl = '') {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => self::MSGTYPE_MUSIC,
            'Music' => array(
                'Title' => $title,
                'Description' => $desc,
                'MusicUrl' => $musicurl,
                'HQMusicUrl' => $hgmusicurl
            ),
            'FuncFlag' => $FuncFlag
        );
        $this->Message($msg);
        return $this;
    }
    /**
     * 设置回复图文
     * @param array $newsData
     * 数组结构:
     *  array(
     *  	"0"=>array(
     *  		'Title'=>'msg title',
     *  		'Description'=>'summary text',
     *  		'PicUrl'=>'http://www.domain.com/1.jpg',
     *  		'Url'=>'http://www.domain.com/1.html'
     *  	),
     *  	"1"=>....
     *  )
     */
    public function news($newsData = array()) {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $count = count($newsData);
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_NEWS,
            'CreateTime' => time(),
            'ArticleCount' => $count,
            'Articles' => $newsData,
            'FuncFlag' => $FuncFlag
        );
        $this->Message($msg);
        return $this;
    }
    /**
     *
     * 回复微信服务器, 此函数支持链式操作
     * @example $this->text('msg tips')->reply();
     * @param string $msg 要发送的信息, 默认取$this->_msg
     * @param bool $return 是否返回信息而不抛出到浏览器 默认:否
     */
    public function reply($msg = array(), $return = false) {
        if (empty($msg))
            $msg = $this->_msg;
        $xmldata = $this->xml_encode($msg);
        $this->log($xmldata);
        if ($return)
            return $xmldata;
        else
            echo $xmldata;
    }
	
    /**
     * 通用auth验证方法，暂时仅用于菜单更新操作
     * @param string $appid
     * @param string $appsecret
     */
    public function checkAuth($appid = '', $appsecret = '') {
        if (!$appid || !$appsecret) {
            $appid = $this->appid;
            $appsecret = $this->appsecret;
        }
        $result = $this->request->quickGet(self::API_URL_PREFIX . self::AUTH_URL . 'appid=' . $appid . '&secret=' . $appsecret);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->access_token = $json['access_token'];
            // S('access_token',$json['access_token'] ,$json['expires_in']-100 );
            $expire = $json['expires_in'] ? intval($json['expires_in']) - 100 : 3600;
            return $this->access_token;
        }
        return false;
    }
	
    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
    public function js_encode($array){
    	$res = str_replace('\/','/',json_encode($array));
    	return preg_replace("#\\\u([0-9a-f]{4}+)#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $res);
    }
    
    public function createMenu($data) {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->request->quickPost(self::API_URL_PREFIX . self::MENU_CREATE_URL . 'access_token=' . $this->access_token, $this->js_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 获取菜单
     * @return array('menu'=>array(....s))
     */
    public function getMenu() {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->request->quickGet(self::API_URL_PREFIX . self::MENU_GET_URL . 'access_token=' . $this->access_token);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 删除菜单
     * @return boolean
     */
    public function deleteMenu() {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->request->quickGet(self::API_URL_PREFIX . self::MENU_DELETE_URL . 'access_token=' . $this->access_token);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 根据媒体文件ID获取媒体文件
     * @param string $media_id 媒体文件id
     * @return raw data
     */
    public function getMedia($media_id) {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->request->quickGet(self::API_FILE_URL_PREFIX . self::MEDIA_GET_URL . 'access_token=' . $this->access_token . '&media_id=' . $media_id);
        if ($result) {
            $json = json_decode($result, true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $result;
        }
        return false;
    }

    /**
     * 创建二维码ticket
     * @param int $scene_id 自定义追踪id
     * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)
     * @param int $expire 临时二维码有效期，最大为1800秒
     * @return array('ticket'=>'qrcode字串','expire_seconds'=>1800)
     */
    public function getQRCode($scene_id, $type = 0, $expire = 1800) {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $data = array(
            'action_name' => $type ? "QR_LIMIT_SCENE" : "QR_SCENE",
            'expire_seconds' => $expire,
            'action_info' => array('scene' => array('scene_id' => $scene_id))
        );
        if ($type == 1) {
            unset($data['expire_seconds']);
        }
        $result = $this->request->quickPost(self::API_URL_PREFIX . self::QRCODE_CREATE_URL . 'access_token=' . $this->access_token, $this->js_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取二维码图片
     * @param string $ticket 传入由getQRCode方法生成的ticket参数
     * @return string url 返回http地址
     */
    public function getQRUrl($ticket) {
        return self::QRCODE_IMG_URL . $ticket;
    }

    /**
     * 批量获取关注用户列表
     * @param unknown $next_openid
     */
    public function getUserList($next_openid = '') {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->request->quickGet(self::API_URL_PREFIX . self::USER_GET_URL . 'access_token=' . $this->access_token . '&next_openid=' . $next_openid);
        if ($result) {
            $json = json_decode($result, true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取关注者详细信息
     * @param string $openid
     * @return array
     */
    public function getUserInfo($openid) {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->request->quickGet(self::API_URL_PREFIX . self::USER_INFO_URL . 'access_token=' . $this->access_token . '&openid=' . $openid);
        if ($result) {
            $json = json_decode($result, true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取用户分组列表
     * @return boolean|array
     */
    public function getGroup() {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->request->quickGet(self::API_URL_PREFIX . self::GROUP_GET_URL . 'access_token=' . $this->access_token);
        if ($result) {
            $json = json_decode($result, true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 新增自定分组
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function createGroup($name) {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $data = array(
            'group' => array('name' => $name)
        );
        $result = $this->request->quickPost(self::API_URL_PREFIX . self::GROUP_CREATE_URL . 'access_token=' . $this->access_token, $this->js_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 更改分组名称
     * @param int $groupid 分组id
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function updateGroup($groupid, $name) {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $data = array(
            'group' => array('id' => $groupid, 'name' => $name)
        );
        $result = $this->request->quickPost(self::API_URL_PREFIX . self::GROUP_UPDATE_URL . 'access_token=' . $this->access_token, $this->js_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 移动用户分组
     * @param int $groupid 分组id
     * @param string $openid 用户openid
     * @return boolean|array
     */
    public function updateGroupMembers($groupid, $openid) {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $data = array(
            'openid' => $openid,
            'to_groupid' => $groupid
        );
        $result = $this->request->quickPost(self::API_URL_PREFIX . self::GROUP_MEMBER_UPDATE_URL . 'access_token=' . $this->access_token, $this->js_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 发送客服消息
     * @param array $data 消息结构{"touser":"OPENID","msgtype":"news","news":{...}}
     * @return boolean|array
     */
    public function sendCustomMessage($data) {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->request->quickPost(self::API_URL_PREFIX . self::CUSTOM_SEND_URL . 'access_token=' . $this->access_token, $this->js_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * oauth 授权跳转接口
     * @param string $callback 回调URI
     * @return string
     */
    public function getOauthRedirect($callback, $state = '', $scope = 'snsapi_userinfo') {
        return self::OAUTH_PREFIX . self::OAUTH_AUTHORIZE_URL . 'appid=' . $this->appid . '&redirect_uri=' . urlencode($callback) . '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
    }

    /*
     * 通过code获取Access Token
     * @return array {access_token,expires_in,refresh_token,openid,scope}
     */
    /****bird*2016-3-8*修改*****/
    public function getOauthAccessToken() {
        $cache = F('TOKEN_WECHAT'); // array
        //if ($cache['token'] == null || time() - $cache['time'] > self::WX_TOKEN_LIFE) {
            $code = isset($_GET['code']) ? $_GET['code'] : '';
            if (!$code)
                return false;
            $result = $this->request->quickGet(self::OAUTH_TOKEN_PREFIX . self::OAUTH_TOKEN_URL . 'appid=' . $this->appid . '&secret=' . $this->appsecret . '&code=' . $code . '&grant_type=authorization_code#wechat_redirect');
            if ($result) {
                $json = json_decode($result, true);
                if (!$json || !empty($json['errcode'])) {
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return false;
                }
                $this->user_token = $json['access_token'];
                $cache = array(
                    'token' => $json['access_token'],
                    'time'  => time()
                );
                F('TOKEN_WECHAT', $cache);
                return $json;
            }
            return false;
        //}
        //return $cache['token'];
    }

    /**
     * 刷新access token并续期
     * @param string $refresh_token
     * @return boolean|mixed
     */
    public function getOauthRefreshToken($refresh_token) {
        $result = $this->request->quickGet(self::OAUTH_TOKEN_PREFIX . self::OAUTH_REFRESH_URL . 'appid=' . $this->appid . '&grant_type=refresh_token&refresh_token=' . $refresh_token);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->user_token = $json['access_token'];
            return $json;
        }
        return false;
    }

    /**
     * 获取授权后的用户资料
     * @param string $access_token
     * @param string $openid
     * @return array {openid,nickname,sex,province,city,country,headimgurl,privilege}
     */
    public function getOauthUserinfo($access_token, $openid) {
        $result = $this->request->quickGet(self::OAUTH_USERINFO_URL . 'access_token=' . $access_token . '&openid=' . $openid.'&lang=zh_CN');
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取签名
     * @param array $arrdata 签名数组
     * @param string $method 签名方法
     * @return boolean|string 签名值
     */
    public function getSignature($arrdata, $method = "sha1") {
        if (!function_exists($method))
            return false;
        ksort($arrdata);
        $paramstring = "";
        foreach ($arrdata as $key => $value) {
            if (strlen($paramstring) == 0)
                $paramstring .= $key . "=" . $value;
            else
                $paramstring .= "&" . $key . "=" . $value;
        }
        $paySign = $method($paramstring);
        return $paySign;
    }

    /**
     * 生成随机字串
     * @param number $length 长度，默认为16，最长为32字节
     * @return string
     */
    public function generateNonceStr($length = 16) {
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

    /**
     * 生成订单package字符串
     * @param string $out_trade_no 必填，商户系统内部的订单号,32个字符内,确保在商户系统唯一
     * @param string $body 必填，商品描述,128 字节以下
     * @param int $total_fee 必填，订单总金额,单位为分
     * @param string $notify_url 必填，支付完成通知回调接口，255 字节以内
     * @param string $spbill_create_ip 必填，用户终端IP，IPV4字串，15字节内
     * @param int $fee_type 必填，现金支付币种，默认1:人民币
     * @param string $bank_type 必填，银行通道类型,默认WX
     * @param string $input_charset 必填，传入参数字符编码，默认UTF-8，取值有UTF-8和GBK
     * @param string $time_start 交易起始时间,订单生成时间,格式yyyyMMddHHmmss
     * @param string $time_expire 交易结束时间,也是订单失效时间
     * @param int $transport_fee 物流费用,单位为分
     * @param int $product_fee 商品费用,单位为分,必须保证 transport_fee + product_fee=total_fee
     * @param string $goods_tag 商品标记,优惠券时可能用到
     * @param string $attach 附加数据，notify接口原样返回
     * @return string
     */
    public function createPackage($out_trade_no, $body, $total_fee, $notify_url, $spbill_create_ip, $fee_type = 1, $bank_type = "WX", $input_charset = "UTF-8", $time_start = "", $time_expire = "", $transport_fee = "", $product_fee = "", $goods_tag = "", $attach = "") {
        $arrdata = array("bank_type" => $bank_type, "body" => $body, "partner" => $this->partnerid, "out_trade_no" => $out_trade_no, "total_fee" => $total_fee, "fee_type" => $fee_type, "notify_url" => $notify_url, "spbill_create_ip" => $spbill_create_ip, "input_charset" => $input_charset);
        if ($time_start)
            $arrdata['time_start'] = $time_start;
        if ($time_expire)
            $arrdata['time_expire'] = $time_expire;
        if ($transport_fee)
            $arrdata['transport_fee'] = $transport_fee;
        if ($product_fee)
            $arrdata['product_fee'] = $product_fee;
        if ($goods_tag)
            $arrdata['goods_tag'] = $goods_tag;
        if ($attach)
            $arrdata['attach'] = $attach;
        ksort($arrdata);
        $paramstring = "";
        foreach ($arrdata as $key => $value) {
            if (strlen($paramstring) == 0)
                $paramstring .= $key . "=" . $value;
            else
                $paramstring .= "&" . $key . "=" . $value;
        }
        $stringSignTemp = $paramstring . "&key=" . $this->partnerkey;
        $signValue = strtoupper(md5($stringSignTemp));
        $package = http_build_query($arrdata) . "&sign=" . $signValue;
        return $package;
    }

    /**
     * 支付签名(paySign)生成方法
     * @param string $package 订单详情字串
     * @param string $timeStamp 当前时间戳（需与JS输出的一致）
     * @param string $nonceStr 随机串（需与JS输出的一致）
     * @return string 返回签名字串
     */
    public function getPaySign($package, $timeStamp, $nonceStr) {
        $arrdata = array("appid" => $this->appid, "timestamp" => $timeStamp, "noncestr" => $nonceStr, "package" => $package, "appkey" => $this->paysignkey);
        $paySign = $this->getSignature($arrdata);
        return $paySign;
    }

    /**
     * 回调通知签名验证
     * @param array $orderxml 返回的orderXml的数组表示，留空则自动从post数据获取
     * @return boolean
     */
    public function checkOrderSignature($orderxml = '') {
        if (!$orderxml) {
            $postStr = file_get_contents("php://input");
            if (!empty($postStr)) {
                $orderxml = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            } else
                return false;
        }
        $arrdata = array('appid' => $orderxml['AppId'], 'appkey' => $this->paysignkey, 'timestamp' => $orderxml['TimeStamp'], 'noncestr' => $orderxml['NonceStr'], 'openid' => $orderxml['OpenId'], 'issubscribe' => $orderxml['IsSubscribe']);
        $paySign = $this->getSignature($arrdata);
        if ($paySign != $orderxml['AppSignature'])
            return false;
        return true;
    }

    /**
     * 发货通知
     * @param string $openid 用户open_id
     * @param string $transid 交易单号
     * @param string $out_trade_no 第三方订单号
     * @param int $status 0:发货失败；1:已发货
     * @param string $msg 失败原因
     * @return boolean|array
     */
    public function sendPayDeliverNotify($openid, $transid, $out_trade_no, $status = 1, $msg = 'ok') {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $postdata = array(
            "appid" => $this->appid,
            "appkey" => $this->paysignkey,
            "openid" => $openid,
            "transid" => strval($transid),
            "out_trade_no" => strval($out_trade_no),
            "deliver_timestamp" => strval(time()),
            "deliver_status" => strval($status),
            "deliver_msg" => $msg,
        );
        $postdata['app_signature'] = $this->getSignature($postdata);
        $postdata['sign_method'] = 'sha1';
        unset($postdata['appkey']);
        $result = $this->request->quickPost(self::PAY_DELIVERNOTIFY . 'access_token=' . $this->access_token, $this->js_encode($postdata));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /*
     * 查询订单信息
     * @param string $out_trade_no 订单号
     * @return boolean|array
     */

    public function getPayOrder($out_trade_no) {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $sign = strtoupper(md5("out_trade_no=$out_trade_no&partner={$this->partnerid}&key={$this->partnerkey}"));
        $postdata = array(
            "appid" => $this->appid,
            "appkey" => $this->paysignkey,
            "package" => "out_trade_no=$out_trade_no&partner={$this->partnerid}&sign=$sign",
            "timestamp" => strval(time()),
        );
        $postdata['app_signature'] = $this->getSignature($postdata);
        $postdata['sign_method'] = 'sha1';
        unset($postdata['appkey']);
        $result = $this->request->quickPost(self::PAY_ORDERQUERY . 'access_token=' . $this->access_token, $this->js_encode($postdata));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'] . json_encode($postdata);
                return false;
            }
            return $json["order_info"];
        }
        return false;
    }

    /**
     * 获取收货地址JS的签名
     * @param string $appId
     * @param string $url
     * @param int $timeStamp
     * @param string $nonceStr
     * @param string $user_token
     * @return Ambigous <boolean, string>
     */
    public function getAddrSign($url, $timeStamp, $nonceStr, $user_token = '') {
        if (!$user_token)
            $user_token = $this->user_token;
        if (!$user_token) {
            $this->errMsg = 'no user access token found!';
            return false;
        }
        $url = htmlspecialchars_decode($url);
        $arrdata = array(
            'appid' => $this->appid,
            'url' => $url,
            'timestamp' => strval($timeStamp),
            'noncestr' => $nonceStr,
            'accesstoken' => $user_token
        );
        return $this->getSignature($arrdata);
    }

    /**
     * 获取多客服会话记录
     * @param array $data 数据结构{"starttime":123456789,"endtime":987654321,"openid":"OPENID","pagesize":10,"pageindex":1,}
     * @return boolean|array
     */
    public function getCustomServiceMessage($data) {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->request->quickPost(self::API_URL_PREFIX . self::CUSTOM_SERVICE_GET_RECORD . 'access_token=' . $this->access_token, $this->js_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 转发多客服消息
     * Examle: $obj->transfer_customer_service($customer_account)->reply();
     * @param string $customer_account 转发到指定客服帐号：test1@test
     */
    public function transfer_customer_service($customer_account = '') {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => 'transfer_customer_service',
        );
        if (!$customer_account) {
            $msg['TransInfo'] = array('KfAccount' => $customer_account);
        }
        $this->Message($msg);
        return $this;
    }

    /**
     * 获取多客服客服基本信息
     * @param
     * @return array
     */
    public function getCustomServiceKFlist() {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->request->quickGet(self::API_URL_PREFIX . self::CUSTOM_SERVICE_GET_KFLIST . 'access_token=' . $this->access_token);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取多客服在线客服接待信息
     * @param
     * @return array {
    "kf_online_list": [
    {
    "kf_account": "test1@test", //客服账号@微信别名
    "status": 1,				//客服在线状态 1：pc在线，2：手机在线,若pc和手机同时在线则为 1+2=3
    "kf_id": "1001",			//客服工号
    "auto_accept": 0,			//客服设置的最大自动接入数
    "accepted_case": 1 			//客服当前正在接待的会话数
    }
    ]
    }
     */
    public function getCustomServiceOnlineKFlist() {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->request->quickGet(self::API_URL_PREFIX . self::CUSTOM_SERVICE_GET_ONLINEKFLIST . 'access_token=' . $this->access_token);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    public function sendTemplateMessage($openid, $templateId, $data, $url = '', $topcolor = '#FF0000') {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $post_data = array(
            'touser' => $openid,
            'template_id' => $templateId,
            'url' => $url,
            'topcolor' => $topcolor,
            'data' => $data,
        );
        $result = $this->request->quickPost(self::TEMPLATE_MESSAGE_URL . 'access_token=' . $this->access_token, $this->js_encode($post_data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    public function get_orderquery(){
        if (!$this->access_token && !$this->checkAuth())
            return false;

        $result = $this->request->quickPost(self::PAY_ORDERQUERY. 'access_token=' . $this->access_token, $this->js_encode($post_data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    public function GetSignPackage(){
        if (!$this->access_token && !$this->checkAuth())
            return false;

        $result = $this->request->quickGet(self::JS_API_TICKET.  $this->access_token.'&type=jsapi');
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->js_ticket = $json['ticket'];
            $expire = $json['expires_in'] ? intval($json['expires_in']) - 100 : 3600;
            return $this->js_ticket;
        }
        return false;
    }


}