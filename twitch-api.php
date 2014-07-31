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
		
		const DEFAULT_USER = 'everalert';
		const DEFAULT_USER_ALT = 'hoshizoramiyuki';
		const DEFAULT_CHANNEL = 'everalert';
		const DEFAULT_CHANNEL_ALT = 'hoshizoramiyuki';
		const DEFAULT_TEAM = 'srl';
		const DEFAULT_LIMIT = 10;
		const DEFAULT_LIMIT_GAMES = 10;
		const DEFAULT_LIMIT_VIDEOS = 10;
		const DEFAULT_LIMIT_STREAMS = 25;
		const DEFAULT_LIMIT_TEAMS = 25;
		const DEFAULT_LIMIT_FOLLOWS = 25;
		const DEFAULT_LIMIT_SEARCH = 25;
		const DEFAULT_OFFSET = 0;
		const DEFAULT_BROADCASTS = false;
		const DEFAULT_DIRECTION = 'desc';
		const DEFAULT_HLS = false;
		const DEFAULT_GAME = 'The Legend of Zelda: Ocarina of Time';
		const DEFAULT_SEARCH = 'The Legend of Zelda: Ocarina of Time';
		const DEFAULT_VIDEO = 'c4312576';
		const DEFAULT_PERIOD = self::PERIOD_WEEK;
		const DEFAULT_SORTBY = 'created_at';
		const DEFAULT_TYPE = 'suggest';
		const DEFAULT_LIVE = false;
		
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
		
		private function validateUser($u=self::DEFAULT_USER) {
			return preg_match(self::PATTERN_USER,$u)?$u:self::DEFAULT_USER;
		}
		
		private function validateChannel($c=self::DEFAULT_CHANNEL) {
			return preg_match(self::PATTERN_CHANNEL,$c)?$c:self::DEFAULT_CHANNEL;
		}
		
		private function validateTeam($t=self::DEFAULT_TEAM) {
			return preg_match(self::PATTERN_TEAM,$t)?$t:self::DEFAULT_TEAM;
		}
		
		private function validateVideo($c=self::DEFAULT_VIDEO) {
			return preg_match(self::PATTERN_VIDEO,$c)?$c:self::DEFAULT_VIDEO;
		}
		
		private function validateLimit($l=self::DEFAULT_LIMIT) {
			return is_int($l)&&$l>0&&$l<=100?$l:self::DEFAULT_LIMIT;
		}
		
		private function validateOffset($o=self::DEFAULT_OFFSET) {
			return is_int($o)&&$o>=0?$o:self::DEFAULT_OFFSET;
		}
		
		private function validateBroadcasts($b=self::DEFAULT_BROADCASTS) {
			return (is_bool($b)&&$b==true)||$b=='true'||$b==1?'true':'false';
		}
		
		private function validateLive($l=self::DEFAULT_LIVE) {
			return (is_bool($l)&&$l==true)||$l=='true'||$l==1?'true':'false';
		}
		
		private function validateType($t=self::DEFAULT_TYPE) {
			return self::DEFAULT_TYPE;
		}
		
		private function validateDirection($d=self::DEFAULT_DIRECTION) {
			return $d!='asc'?'desc':'asc';
		}
		
		private function validateSortby($s=self::DEFAULT_SORTBY) {
			return $s!='last_broadcast'?'created_at':'last_broadcast';
		}
		
		private function validatePeriod($p=self::DEFAULT_PERIOD) {
			return $p==self::PERIOD_MONTH||$p==self::PERIOD_ALL?$p:self::DEFAULT_PERIOD;
		}
		
		private function validateGame($g=self::DEFAULT_GAME) {
			return urlencode($g);
		}
		
		private function validateSearch($s=self::DEFAULT_SEARCH) {
			return urlencode($s);
		}
		
		private function validateHls($h=self::DEFAULT_HLS) {
			return (is_bool($h)&&$h==true)||$h=='true'||$h==1?'true':'false';
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
		
		public function userFollowing($user=self::DEFAULT_USER,$limit=self::DEFAULT_LIMIT_FOLLOWS,$offset=self::DEFAULT_OFFSET,$direction=self::DEFAULT_DIRECTION,$sortby=self::DEFAULT_SORTBY) {
			global $direction,$sortby;
			return $this->api(self::API_URI.'/users/'.$this->validateUser($user).'/follows/channels'.
				'?limit='.$this->validateLimit($limit).
				'&offset='.$this->validateOffset($offset).
				'&direction='.$this->validateDirection($direction).
				'&sortby='.$this->validateSortby($sortby),self::METHOD_GET);
		}
		
		public function userFollowsUser($user=self::DEFAULT_USER,$target=self::DEFAULT_USER_ALT) {
			return $this->api(self::API_URI.'/users/'.$this->validateUser($user).'/follows/channels/'.$this->validateUser($target),self::METHOD_GET);
		}
		
		public function userFollow() {} // put auth
		
		public function userUnfollow() {} // delete auth
		
		public function userBlocking() {} // get auth
		
		public function userBlock() {} // put auth
		
		public function userUnblock() {} // delete auth
		
		public function getUser($user=self::DEFAULT_CHANNEL) {
			return $this->api(self::API_URI.'/users/'.$this->validateUser($user),self::METHOD_GET);
		}
		
		public function getUserSubscribedChannel() {} // get auth
		
		
		// CHANNELS
		
		public function channel() {} // get auth
		
		public function channelUpdate() {} // put auth
		
		public function channelStreamKey() {} // delete auth
		
		public function channelCommercial() {} // post auth
		
		public function getChannel($channel=self::DEFAULT_CHANNEL) {
			return $this->api(self::API_URI.'/channels/'.$this->validateChannel($channel),self::METHOD_GET);
		}
		
		public function getChannelVideos($channel=self::DEFAULT_CHANNEL,$limit=self::DEFAULT_LIMIT,$offset=self::DEFAULT_OFFSET,$broadcasts=self::DEFAULT_BROADCASTS) {
			global $broadcasts;
			return $this->api(self::API_URI.'/channels/'.$this->validateChannel($channel).'/videos'.
				'?limit='.$this->validateLimit($limit).
				'&offset='.$this->validateOffset($offset).
				'&broadcasts='.$this->validateBroadcasts($broadcasts),self::METHOD_GET);
		}
		
		public function getChannelFollows($channel=self::DEFAULT_CHANNEL,$limit=self::DEFAULT_LIMIT_FOLLOWS,$offset=self::DEFAULT_OFFSET,$direction=self::DEFAULT_DIRECTION) {
			global $direction;
			return $this->api(self::API_URI.'/channels/'.$this->validateChannel($channel).'/follows'.
				'?limit='.$this->validateLimit($limit).
				'&offset='.$this->validateOffset($offset).
				'&direction='.$this->validateDirection($direction),self::METHOD_GET);
		}
		
		public function getChannelEditors() {} // get auth
		
		public function getChannelSubscriptions() {} // get auth
		
		public function getChannelSubscribedUser() {} // get auth
		
		
		// CHAT
		
		public function getChat($channel=self::DEFAULT_CHANNEL) {
			return $this->api(self::API_URI.'/chat/'.$this->validateChannel($channel),self::METHOD_GET);
		}
		
		public function getChatEmoticons() {
			return $this->api(self::API_URI.'/chat/emoticons',self::METHOD_GET);
		}
		
		
		// GAMES
		
		public function games($limit=self::DEFAULT_LIMIT_GAMES,$offset=self::DEFAULT_OFFSET,$hls=self::DEFAULT_HLS) {
			global $hls;
			return $this->api(self::API_URI.'/games/top'.
				'?limit='.$this->validateLimit($limit).
				'&offset='.$this->validateOffset($offset).
				'&hls='.$this->validateHls($hls),self::METHOD_GET);
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
		
		public function searchChannels($query=self::DEFAULT_SEARCH,$limit=self::DEFAULT_LIMIT_SEARCH,$offset=self::DEFAULT_OFFSET) {
			return $this->api(self::API_URI.'/search/channels'.
				'?query='.$this->validateSearch($query).
				'&limit='.$this->validateLimit($limit).
				'&offset='.$this->validateOffset($offset),self::METHOD_GET);
		}
		
		public function searchStreams($query=self::DEFAULT_SEARCH,$limit=self::DEFAULT_LIMIT_SEARCH,$offset=self::DEFAULT_OFFSET,$hls=self::DEFAULT_HLS) {
			return $this->api(self::API_URI.'/search/streams'.
				'?query='.$this->validateSearch($query).
				'&limit='.$this->validateLimit($limit).
				'&offset='.$this->validateOffset($offset).
				'&hls='.$this->validateHls($hls),self::METHOD_GET);
		}
		
		public function searchGames($query=self::DEFAULT_SEARCH,$type=self::DEFAULT_TYPE,$limit=self::DEFAULT_LIMIT_SEARCH,$offset=self::DEFAULT_OFFSET,$live=self::DEFAULT_LIVE) {
			global $live;
			return $this->api(self::API_URI.'/search/games'.
				'?query='.$this->validateSearch($query).
				'&type='.$this->validateType($type).
				'&limit='.$this->validateLimit($limit).
				'&offset='.$this->validateOffset($offset).
				'&live='.$this->validateLive($live),self::METHOD_GET);
		} // get
		
		
		// STREAMS
		
		public function streams() { //INCOMPLETE
			return $this->api(self::API_URI.'/streams/',self::METHOD_GET);
		}
		
		public function streamsFeatured($limit=self::DEFAULT_LIMIT_STREAMS,$offset=self::DEFAULT_OFFSET,$hls=self::DEFAULT_HLS) {
			global $hls;
			return $this->api(self::API_URI.'/streams/featured'.
				'?limit='.$this->validateLimit($limit).
				'&offset='.$this->validateOffset($offset).
				'&hls='.$this->validateHls($hls),self::METHOD_GET);
		}
		
		public function streamsSummary($limit=self::DEFAULT_LIMIT_STREAMS,$offset=self::DEFAULT_OFFSET,$hls=self::DEFAULT_HLS) {
			global $hls;
			return $this->api(self::API_URI.'/streams/summary'.
				'?limit='.$this->validateLimit($limit).
				'&offset='.$this->validateOffset($offset).
				'&hls='.$this->validateHls($hls),self::METHOD_GET);
		}
		
		public function streamsFollowed() {} // get auth
		
		public function getStream($channel=self::DEFAULT_CHANNEL) {
			return $this->api(self::API_URI.'/streams/'.$this->validateChannel($channel),self::METHOD_GET);
		}
		
		
		// TEAMS
		
		public function teams($limit=self::DEFAULT_LIMIT_TEAMS,$offset=self::DEFAULT_OFFSET) {
			return $this->api(self::API_URI.'/teams'.
				'?limit='.$this->validateLimit($limit).
				'&offset='.$this->validateOffset($offset),self::METHOD_GET);
		}
		
		public function getTeam($team=self::DEFAULT_TEAM) {
			return $this->api(self::API_URI.'/teams/'.$this->validateTeam($team),self::METHOD_GET);
		}
		
		
		// VIDEOS
		
		public function videos($limit=self::DEFAULT_LIMIT_VIDEOS,$offset=self::DEFAULT_OFFSET,$game='',$period=self::DEFAULT_PERIOD) {
			global $game,$period;
			return $this->api(self::API_URI.'/videos/top'.
				'?limit='.$this->validateLimit($limit).
				'&offset='.$this->validateOffset($offset).
				'&game='.$this->validateGame($game).
				'&period='.$this->validatePeriod($period),self::METHOD_GET);
		}
		
		public function videosFollowed() {} // get auth v3-only
		
		public function getVideo($video=self::DEFAULT_VIDEO) {
			return $this->api(self::API_URI.'/videos/'.$this->validateVideo($video),self::METHOD_GET);
		}
		
		
		// AUTH, LOGIN, ACCT CREATION, ETC?
		
	}
?>