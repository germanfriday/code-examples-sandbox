/*!
 * LiveTweets v2.0- for jQuery 1.9.1
 * Ready For Twitter API 1.1
 * http://codecanyon.net/item/live-tweets-jquery-plugin/154003
 *
 * Copyright 2013, Johan Dorper
 * You need to buy a license if you want use this script.
 * http://codecanyon.net/wiki/buying/howto-buying/licensing/
 *
 * Date: Jan 08 2011
 
 * Edit the tag variable below.
 * Any questions? hello@johandorper.com OR twitter.com/johandorper

 * LiveTweets is a JQuery plugin that lets you easily
 * load in Tweets from Twitter based on a valid Twitter
 * search operator.
 
 * Thx to Fox Junior for the timer Plugin: http://www.foxjunior.eu/
 */
eval(function (p, a, c, k, e, r) { e = function (c) { return (c < a ? '' : e(parseInt(c / a))) + ((c = c % a) > 35 ? String.fromCharCode(c + 29) : c.toString(36)) }; if (!''.replace(/^/, String)) { while (c--) r[e(c)] = k[c] || e(c); k = [function (e) { return r[e] }]; e = function () { return '\\w+' }; c = 1 }; while (c--) if (k[c]) p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c]); return p }('(8($){$.25.2B=8(I){6 J={1D:"#2r",1f:"",Q:5,1C:5,1A:L,1z:L,1w:L,1t:L};6 I=$.1M(J,I);K 1e.Y(8(){1g=$(1e);6 j=[];6 k=0;6 l=0;6 m=0;6 n=N;6 o="22";6 p="1h://1N.2x.T/a/2w/2g/2c.20";6 q="16://1O.19.T/";6 r="1h://2q.2v.T/1Q/?2p=?&2s="+1d(2A.1Y.1Z)+"&1E="+I.1f+"&q=";6 s=N;6 t=5;6 u="";7(I.Q>5)t=I.Q;7(I.Q>12)t=10;6 v=I.1D;6 w=r+1d(v)+"&1p="+t;6 x=8(){$.27(w,8(c){$.Y(c.2e,8(a,b){7(u.1q().1r(b.1a.1u.1q())<0){7(A(b.P)==N){j[m]=b;m++}}});7(n==N){j.2z();z()}l=j.2G})};6 y=8(a){v=a;w=r+1d(v)+"&1p="+t;$(".R").1b();H();x()};$("#1R").1S(8(){y($("#1T").1U())});6 z=8(){7(n==N){7(I.Q>1){1i(6 i=0;i<I.Q;i++){7(j[k]!=14){G(i,\'1j\')}}k=(i-1)}1c{7(j[k]!=14){G(0,\'1j\')}}n=L;k++}7(s==N){1k(8(){7(j[k]!=14){G(k,\'1l\');k++}},(I.1C*1m));s=L}};6 A=8(c){6 d=N;$.Y(j,8(a,b){7(b.P==c){d=L}});K d};6 B=8(a){6 b=/(\\b(16?|2t|2u):\\/\\/[-A-1n-9+&@#\\/%?=~1o|!:,.;]*[-A-1n-9+&@#\\/%=~1o|])/2y;K a.17(b,"<a W=\'V\' S=\'$1\'>$1</a>")};6 C=8(a){6 b=/\\#([a-1s-Z]+)([\\s|\\.\\,:]+)*/g;K a.17(b,"<a W=\'V\' S=\'16://19.T/#1P?q=%23$1\'>#$1 </a>")};6 D=8(a){6 b=/\\@([a-1s-Z]+)([\\s|\\.\\,:]+)*/g;K a.17(b,"<a W=\'V\' S=\'16://19.T/$1\'>@$1 </a>")};6 E=8(a){7(I.1z==L){a=B(a)}7(I.1w==L){a=C(a)}7(I.1t==L){a=D(a)}K a};6 F=8(a){6 b=1v.1V(a);6 c=1W 1v();6 d=c.1X();6 e=1x((d-b)/1m);7(e<0)K N;7(e<=1y)K"21 U";7(e<X)K 1x(e/1y)+" 26 U";7(e<=1.5*X)K"1B 28 U";7(e<23.5*X)K 29.2a(e/X)+" 2b U";7(e<1.5*24*X)K"1B 2d U";6 f=a.2f(\' \');K f[2]+\' \'+f[1]+(f[3]!=c.2h()?\' \'+f[3]:\'\')};6 G=8(a,b){7(j[a]!=14){6 c=j[a].2i;6 d=j[a].1a.1u;6 e=j[a].1a.2j;6 f=j[a].2k;6 g="";7(a==0){g="2l"}7(o=="2m"){e=e.17("2n","")}7(e.1r(\'2o\')>0){6 h=$("<M O=\'R "+g+"\' P=\'15"+j[a].P+"\'><18 1F=\'13\' 1G=\'13\' O=\'1H\' 1I=\'"+p+"\'/><M O=\'1J\'><a  O=\'1K\' W=\'V\' S=\'"+q+d+"\'>@"+d+"</a> "+E(c)+"<11>"+F(f)+"</11></M></M>")}1c{6 h=$("<M O=\'R "+g+"\' P=\'15"+j[a].P+"\'><18 1F=\'13\' 1G=\'13\' O=\'1H\' 1I=\'"+e+"\'/><M O=\'1J\'><a O=\'1K\' W=\'V\' S=\'"+q+d+"\'>@"+d+"</a> "+E(c)+"<11>"+F(f)+"</11></M></M>")}1g.2C(h);7(!I.1A==L)$("#15"+j[a].P+" 18").1b();7(b==\'1l\'){$("#15"+j[a].P).2D().2E(2F)}7($(".1L M.R").2H()>24){6 i=0;$(".1L M.R").Y(8(){7(i>4){$(1e).1b()}i++})}}};6 H=8(){j="";j=[];k=0;l=0;m=0;n=N};7(I.1f==""){2I("2J 2K 2L 2M 2N 2O 1E, 2P 2Q 2R 1i 2S 2T.")}1c{x()}1k(8(){x()},2U)})}})(2V);', 62, 182, '||||||var|if|function||||||||||||||||||||||||||||||||||||||return|true|div|false|class|id|startMessages|tweet|href|com|ago|_blank|target|3600|each|||span||48|undefined|tweet_|https|replace|img|twitter|user|remove|else|escape|this|liveTweetsToken|tweetHolder|http|for|instant|setInterval|normal|1000|Z0|_|rpp|toLowerCase|indexOf|zA|linkUsernames|name|Date|linkHashtags|parseInt|60|convertTextlink|showThumbnails|One|timeBetweenTweets|operator|token|width|height|tweet_foto|src|tweet_text|profile|tweets|extend|a2|www|search|Get|custSearch|click|keyword|val|parse|new|getTime|location|host|png|Seconds|small|||fn|minutes|getJSON|hour|Math|round|hours|default_profile_2_normal|day|statuses|split|images|getFullYear|text|profile_image_url|created_at|first|large|_normal|default_profile|callback|livetweets|google|domain|ftp|file|johandorper|1292975674|twimg|ig|reverse|window|liveTweets|prepend|hide|slideDown|800|length|size|alert|You|need|to|create|your|LiveTweets|read|the|documentation|more|information|20000|jQuery'.split('|'), 0, {}))