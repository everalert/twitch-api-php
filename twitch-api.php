<?php
	/**
		TwitchTV API Wrapper
		Nathaniel Coughran / EverAlert
		Based on https://github.com/justintv/Twitch-API
	**/
	
	class TwitchAPI {
	
		private $apiVersion;
		private $clientId;
		//private $clientSecret;
		//private $authToken;
	
		const API_URI = 'https://api.twitch.tv/kraken';
		
		const METHOD_GET = 'GET';
		const METHOD_POST = 'POST';
		const METHOD_PUT = 'PUT';
		const METHOD_DELETE = 'DELETE';
		
		const PERIOD_WEEK = 'week';
		const PERIOD_MONTH = 'month';
		const PERIOD_ALL = 'all';
		const DIRECTION_ASC = 'asc';
		const DIRECTION_DESC = 'desc';
		const BOOL_TRUE = 'true';
		const BOOL_FALSE = 'false';
		const TYPE_SUGGEST = 'suggest';
		const SORTBY_CREATED = 'created_at';
		const SORTBY_BROADCAST = 'last_broadcast';
		
		const DEF_MAIN = 'everalert';
		const DEF_ALT = 'hoshizoramiyuki';
		const DEF_TEAM = 'srl';
		const DEF_GAME = 'The Legend of Zelda: Ocarina of Time';
		const DEF_SEARCH = 'The Legend of Zelda: Ocarina of Time';
		const DEF_VIDEO = 'c4312576';
		const DEF_TYPE = self::TYPE_SUGGEST;
		
		const PATTERN_USER = '/^[a-z0-9_]+$/i';
		const PATTERN_CHANNEL = '/^[a-z0-9_]+$/i';
		const PATTERN_TEAM = '/^[a-z0-9_]+$/i';
		const PATTERN_VIDEO = '/^[a-c][0-9]+$/i';
		//const PATTERN_URL;
		
		function __construct($apiVersion=2,$clientId=null) {
			$this->setApiVersion($apiVersion);
			$this->setClientId($clientId);
		}
		
		
		// GENERAL
		
		// API Calls
		
		private function api($url,$method) {
			$context = stream_context_create(
				array(
					'https'=>array(
						'method'=>$method,
						'header'=>'Accept: application/vnd.twitchtv'.($this->apiVersion>0?'.v'.$this->apiVersion:null).'+json'.
							($this->clientId!=null?"\r\nClient-ID: ".$this->clientId:null)
					)
				)
			);
			$data = @file_get_contents($url,false,$context);
			if ($http_response_header[5]=='Status: 200 OK') {
				return json_decode($data,true);
			} else {
				return false;
			}
		}
		
		public function raw($url=self::API_URI,$method=self::METHOD_GET) {
			return $this->api($url,$method);
		}
		
		// Input Validation
		
		private function validateAccount($c=NULL) {
			return preg_match(self::PATTERN_CHANNEL,$c)?$c:NULL;
		}
		
		private function validateTeam($t=self::DEF_TEAM) {
			return preg_match(self::PATTERN_TEAM,$t)?$t:self::DEF_TEAM;
		}
		
		private function validateVideo($c=self::DEF_VIDEO) {
			return preg_match(self::PATTERN_VIDEO,$c)?$c:self::DEF_VIDEO;
		}
		
		private function validateString($s=self::DEF_SEARCH) {
			return urlencode($s);
		}
		
		// Settings
		
		public function setApiVersion($version=2) {
			$this->apiVersion = intval($version)>=0&&intval($version)<=3?intval($version):2;
		}
		
		public function setClientId($id=null) {
			$this->clientId = $id;
		}
		
		
		// USERS
		
		public function user() {} // get auth
		
		public function userFollowing($user=self::DEF_MAIN,$limit=NULL,$offset=NULL,$direction=NULL,$sortby=NULL) {
			global $user,$limit,$offset,$direction,$sortby;
			return $this->api(self::API_URI.'/users/'.$this->validateAccount($user).'/follows/channels'.
				'?limit='.$limit.'&offset='.$offset.'&direction='.$direction.'&sortby='.$sortby,self::METHOD_GET);
		}
		
		public function userFollowsUser($user=self::DEF_MAIN,$target=self::DEF_MAIN_ALT) {
			global $user,$target;
			return $this->api(self::API_URI.'/users/'.$this->validateAccount($user).'/follows/channels/'.$this->validateAccount($target),self::METHOD_GET);
		}
		
		public function userFollow() {} // put auth
		
		public function userUnfollow() {} // delete auth
		
		public function userBlocking() {} // get auth
		
		public function userBlock() {} // put auth
		
		public function userUnblock() {} // delete auth
		
		public function getUser($user=self::DEF_MAIN) {
			return $this->api(self::API_URI.'/users/'.$this->validateAccount($user),self::METHOD_GET);
		}
		
		public function getUserSubscribedChannel() {} // get auth
		
		
		// CHANNELS
		
		public function channel() {} // get auth
		
		public function channelUpdate() {} // put auth
		
		public function channelStreamKey() {} // delete auth
		
		public function channelCommercial() {} // post auth
		
		public function getChannel($channel=self::DEF_MAIN) {
			return $this->api(self::API_URI.'/channels/'.$this->validateAccount($channel),self::METHOD_GET);
		}
		
		public function getChannelVideos($channel=self::DEF_MAIN,$limit=NULL,$offset=NULL,$broadcasts=NULL) {
			global $channel,$limit,$offset,$broadcasts;
			return $this->api(self::API_URI.'/channels/'.$this->validateAccount($channel).'/videos'.
				'?limit='.$limit.'&offset='.$offset.'&broadcasts='.$broadcasts,self::METHOD_GET);
		}
		
		public function getChannelFollows($channel=self::DEF_MAIN,$limit=NULL,$offset=NULL,$direction=NULL) {
			global $channel,$limit,$offset,$direction;
			return $this->api(self::API_URI.'/channels/'.$this->validateAccount($channel).'/follows'.
				'?limit='.$limit.'&offset='.$offset.'&direction='.$direction,self::METHOD_GET);
		}
		
		public function getChannelEditors() {} // get auth
		
		public function getChannelSubscriptions() {} // get auth
		
		public function getChannelSubscribedUser() {} // get auth
		
		
		// CHAT
		
		public function getChat($channel=self::DEF_MAIN) {
			return $this->api(self::API_URI.'/chat/'.$this->validateAccount($channel),self::METHOD_GET);
		}
		
		public function getChatEmoticons() {
			return $this->api(self::API_URI.'/chat/emoticons',self::METHOD_GET);
		}
		
		
		// GAMES
		
		public function games($limit=NULL,$offset=NULL,$hls=NULL) {
			global $limit,$offset,$hls;
			return $this->api(self::API_URI.'/games/top'.
				'?limit='.$limit.'&offset='.$offset.'&hls='.$hls,self::METHOD_GET);
		}
		
		
		// INGESTS
		
		public function ingests() {
			return $this->api(self::API_URI.'/ingests/',self::METHOD_GET);
		}
		
		
		// ROOT
		
		public function root() {
			return $this->api(self::API_URI.'/',self::METHOD_GET);
		}
		
		
		// SEARCH
		
		public function searchChannels($query=self::DEF_SEARCH,$limit=NULL,$offset=NULL) {
			global $query,$limit,$offset;
			return $this->api(self::API_URI.'/search/channels'.
				'?query='.$this->validateString($query).'&limit='.$limit.'&offset='.$offset,self::METHOD_GET);
		}
		
		public function searchStreams($query=self::DEF_SEARCH,$limit=NULL,$offset=NULL,$hls=NULL) {
			global $query,$limit,$offset,$hls;
			return $this->api(self::API_URI.'/search/streams?query='.$this->validateString($query).
				'&limit='.$limit.'&offset='.$offset.'&hls='.$hls,self::METHOD_GET);
		}
		
		public function searchGames($query=self::DEF_SEARCH,$type=self::DEF_TYPE,$limit=NULL,$offset=NULL,$live=NULL) {
			global $query,$type,$limit,$offset,$live;
			return $this->api(self::API_URI.'/search/games'.
				'?query='.$this->validateString($query).'&type='.$type.
				'&limit='.$limit.'&offset='.$offset.'&live='.$live,self::METHOD_GET);
		}
		
		
		// STREAMS
		
		public function streams($game=NULL,$channel=NULL,$limit=NULL,$offset=NULL,$embeddable=NULL,$hls=NULL,$client_id=NULL) {
			global $game,$channel,$limit,$offset,$embeddable,$hls,$client_id;
			return $this->api(self::API_URI.'/streams?'.
				($game!=NULL?'game='.$this->validateString($game).'&':NULL).
				($channel!=NULL?'channel='.$this->validateAccount($channel).'&':NULL).
				($client_id!=NULL?'client_id='.$client_id.'&':NULL).
				'limit='.$limit.'&offset='.$offset.'&embeddable='.$embeddable.'&hls='.$hls,self::METHOD_GET);
		}
		
		public function streamsFeatured($limit=NULL,$offset=NULL,$hls=NULL) {
			global $limit,$offset,$hls;
			return $this->api(self::API_URI.'/streams/featured'.
				'?limit='.$limit.'&offset='.$offset.'&hls='.$hls,self::METHOD_GET);
		}
		
		public function streamsSummary($limit=NULL,$offset=NULL,$hls=NULL) {
			global $limit,$offset,$hls;
			return $this->api(self::API_URI.'/streams/summary'.
				'?limit='.$limit.'&offset='.$offset.'&hls='.$hls,self::METHOD_GET);
		}
		
		public function streamsFollowed() {} // get auth
		
		public function getStream($channel=self::DEF_MAIN) {
			return $this->api(self::API_URI.'/streams/'.$this->validateAccount($channel),self::METHOD_GET);
		}
		
		
		// TEAMS
		
		public function teams($limit=NULL,$offset=NULL) {
			global $limit,$offset;
			return $this->api(self::API_URI.'/teams'.
				'?limit='.$limit.'&offset='.$offset,self::METHOD_GET);
		}
		
		public function getTeam($team=self::DEF_TEAM) {
			return $this->api(self::API_URI.'/teams/'.$this->validateTeam($team),self::METHOD_GET);
		}
		
		
		// VIDEOS
		
		public function videos($limit=NULL,$offset=NULL,$game=NULL,$period=NULL) {
			global $limit,$offset,$game,$period;
			return $this->api(self::API_URI.'/videos/top?'.
				($game!=NULL?'game='.$this->validateString($game).'&':NULL).
				($period!=NULL?'period='.$period.'&':NULL).
				'limit='.$limit.'&offset='.$offset,self::METHOD_GET);
		}
		
		public function videosFollowed() {} // get auth v3-only
		
		public function getVideo($video=self::DEF_VIDEO) {
			return $this->api(self::API_URI.'/videos/'.$this->validateVideo($video),self::METHOD_GET);
		}
		
		
		// AUTH, LOGIN, ACCT CREATION, ETC?
		
	}
?>