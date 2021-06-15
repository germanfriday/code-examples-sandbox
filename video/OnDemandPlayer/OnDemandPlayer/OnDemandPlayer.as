import mx.video.*;
import mx.controls.DataGrid;
import mx.controls.MediaPlayback;
//
class OnDemandPlayer {
	
	private var serverName = "localhost";
	//Directory Where the File Object is stored.. set by default in /Applications/
	private var appName = "FileObj";
	private var folderName = "/approot";
	private var videoName = "MyCollection";
	//Components
	private var list:DataGrid;
	private var flv_playback:mx.video.FLVPlayback;
	private var mp3Player:mx.controls.MediaPlayback;
	// Netconnect information
	private var nc:NetConnection;
	private var ns:NetStream;
	//
	private var onResult:Function;
	private var onStatus:Function;
	private var owner:Object;
	//DataGrid Array
	private var myDP_array:Array;

	public function OnDemandPlayer() {
		_global.style.setStyle("themeColor", "haloBlue");
		_global.style.setStyle("fontFamily", "Verdana");
		_global.style.setStyle("fontSize", 10);
		//
		myDP_array = new Array();
		//Assign players to root list
		list = _root.list;
		flv_playback = _root.flv_playback;
		mp3Player = _root.mp3Player;
		//
		list.rowHeight = 20;
		list.dataProvider = myDP_array;
		list.addEventListener("change", this);
		//
		makeConnection();
	}
	//List's change event
	public function change(evt:Object):Void {
		//declare mp3 or flv path... if flv path take out extension
		var media_path = "rtmp://"+serverName+"/"+videoName+"/"+(list.selectedItem.Name.split(".flv").join(""));
		//trace(media_path);
		if (media_path.lastIndexOf(".mp3") != -1) {
			mp3Playback(media_path, list.selectedItem.Length);
		} else {
			flvPlayback(media_path);
		}
	}
	//For initial FileObject Query, must make a connection.
	public function makeConnection():Void {
		nc = new NetConnection();
		nc.connect("rtmp://"+serverName+"/"+appName);
		nc.owner = this;
		nc.onStatus = function(info) {
			if (info.code == "NetConnection.Connect.Success") {
				owner.dir();
			}
		};
		createFileObj();
	}
	public function flvPlayback(playback):Void {
		flv_playback.play(playback);
		mp3Player.stop();
	}
	public function mp3Playback(playback, mp3length):Void {
		mp3Player.contentPath = playback;
		mp3Player.totalTime = mp3length;
		mp3Player.play();
		flv_playback.stop();
	}
	//Gives the Flash Media Server the folderName you wish to connect to
	private function createFileObj():Void {
		nc.call("createFileObj", null, folderName);
	}
	//Gathers data obtained from the Flash Media Server
	private function dirResult(folderName, owner):Void {
		this.onResult = function(retVal) {
			//
			for (var i = 0; i<retVal.length; i++) {
				//Takes FLV link out of File Object that is returned.
				var flv_name = (retVal[i].name).substr((retVal[i].name).lastIndexOf("/")+1);
				//Sets the array to your FLV
				var index = flv_name.lastIndexOf(".");
				//
				var stream_name = flv_name.substring(index + 1, flv_name.length) + ":" + owner.folderName + "/" + flv_name.substring(0, index);
				//Gathers the length for each of the streams
					var streamlength = owner.nc.call("getStreamLength", null, stream_name);
				//Adds each stream information myDP_array which is then put into datagrid
				owner.myDP_array.addItem({Name:flv_name, Length:retVal[i].streamlength, CreationTime:retVal[i].creationTime, LastModified:retVal[i].lastModified, Size:retVal[i].length});
			}
			//
		};
		this.onStatus = function(errorVal) {
			trace("Error : "+folderName+" "+errorVal.code+"\n");
		};
	}
	private function dir():Void {
		//Calls server to obtain directory list and gives value to dirResult
		nc.call("dir", new dirResult("dir", this));
	}
}
