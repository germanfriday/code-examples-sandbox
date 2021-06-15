Created for: Macromedia/Adobe Developer Center
Article title: Using File Object for Video on Demand and MP3 Playback
URL: http://www.macromedia.com/devnet/flashmediaserver/articles/on_demand_player.html
Created by: Robert Sandie

Demonstrates: File Object, FLVPlayback, DataGrid, MediaPlayback, AS2 Class Structure

--Installation--

Unzip package into following directories:

FileObj -> ..\Flash Media Server 2\applications\
MyCollection -> ..\Flash Media Server 2\applications\
OnDemandPlayer -> can go anywhere


--Flash Media Server Setup--

Set up your Vhost.xml, which you can find in your conf\_defaultRoot_\_defaultVhost_\ subfolder in the application Flash Media Server 2 folder:

<VirtualDirectory>
   <Streams>/approot; C:\Program Files\Macromedia\Flash Media Server 2\applications\MyCollection\streams\_definst_\
   </Streams>
</Virtual Directory>


