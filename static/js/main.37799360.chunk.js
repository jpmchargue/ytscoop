/*! For license information please see main.37799360.chunk.js.LICENSE.txt */
(this.webpackJsonpytscoop=this.webpackJsonpytscoop||[]).push([[0],{14:function(e,t,s){},15:function(e,t,s){},16:function(e,t,s){"use strict";s.r(t);var a=s(0),r=s(1),n=s.n(r),c=s(4),i=s.n(c),o=(s(14),s(5)),l=s(6),d=s(2),h=s(8),u=s(7),m=function(e){Object(h.a)(s,e);var t=Object(u.a)(s);function s(e){var a;return Object(o.a)(this,s),(a=t.call(this,e)).state={url:"",loading:!1,receivedStreams:!1,streams:null,error:""},a.handleURL=a.handleURL.bind(Object(d.a)(a)),a.handleSubmit=a.handleSubmit.bind(Object(d.a)(a)),a}return Object(l.a)(s,[{key:"browserIsValid",value:function(){return!!window.chrome}},{key:"handleURL",value:function(e){this.setState({url:e.target.value})}},{key:"handleSubmit",value:function(e){var t=this;this.setState({loading:!0,receivedStreams:!1,error:""});var s=new FormData;s.append("url",this.state.url),fetch("http://ytscoop.com/api.php",{method:"POST",body:s}).then((function(e){return e.json()})).then((function(e){"Invalid URL"===e[0]?t.setState({loading:!1,error:"The URL wasn't recognized as a YouTube watch page URL."}):"Unclear error"===e[0]?t.setState({loading:!1,error:"That video couldn't be processed-- it may be private or deleted."}):t.setState({loading:!1,receivedStreams:!0,streams:e,error:""})}))}},{key:"renderLoading",value:function(){return Object(a.jsx)("div",{className:"loading",children:"Loading..."})}},{key:"renderStreams",value:function(){return Object(a.jsxs)("div",{children:[Object(a.jsxs)("div",{className:"streams",children:[Object(a.jsxs)("div",{className:"stream_wrapper",children:[Object(a.jsx)("span",{className:"stream_name",children:"Best Video (no audio)"}),Object(a.jsx)("div",{className:"stream_desc",children:this.state.streams[1].desc}),Object(a.jsxs)("div",{className:"stream_interface",children:["PLEASE DO NOT REUSE THIS LINK. Abuse of the source URLs could force the website to close down.",Object(a.jsx)("audio",{src:this.state.streams[1].url,controls:!0})]})]}),Object(a.jsxs)("div",{className:"stream_wrapper",children:[Object(a.jsx)("span",{className:"stream_name",children:"Best Audio (no video)"}),Object(a.jsx)("div",{className:"stream_desc",children:this.state.streams[2].desc}),Object(a.jsxs)("div",{className:"stream_interface",children:["PLEASE DO NOT REUSE THIS LINK. Abuse of the source URLs could force the website to close down.",Object(a.jsx)("audio",{src:this.state.streams[2].url,controls:!0})]})]}),Object(a.jsxs)("div",{className:"stream_wrapper",children:[Object(a.jsx)("span",{className:"stream_name",children:"Best Combined"}),Object(a.jsx)("div",{className:"stream_desc",children:this.state.streams[0].desc}),Object(a.jsxs)("div",{className:"stream_interface",children:["PLEASE DO NOT REUSE THIS LINK. Abuse of the source URLs could force the website to close down.",Object(a.jsx)("audio",{src:this.state.streams[0].url,controls:!0})]})]})]}),Object(a.jsx)("div",{className:"explanation1",children:"To download a stream, click on the three dots to the right of its controls, then click 'Download'."}),Object(a.jsxs)("div",{className:"explanation2",children:["YouTube stores multiple 'streams' for every video, with varying formats and levels of quality.",Object(a.jsx)("br",{}),"Only some streams contain both video and audio, and such streams are generally lower in quality than streams that contain only one or the other."]})]})}},{key:"render",value:function(){var e=this;return this.browserIsValid()?Object(a.jsx)("div",{className:"main_wrapper",children:Object(a.jsxs)("div",{className:"main",children:[Object(a.jsx)("link",{rel:"preconnect",href:"https://fonts.gstatic.com"}),Object(a.jsx)("link",{href:"https://fonts.googleapis.com/css2?family=Oswald:wght@500&family=Roboto:wght@300&display=swap",rel:"stylesheet"}),Object(a.jsx)("div",{className:"title",children:"YouTube Scoop"}),Object(a.jsx)("div",{className:"subtitle",children:"A clean, no-frills tool for downloading YouTube videos."}),Object(a.jsx)("input",{id:"url_input",type:"text",placeholder:"enter a video URL",value:this.state.url,onChange:this.handleURL}),Object(a.jsx)("br",{}),this.state.loading?this.renderLoading():Object(a.jsx)("button",{className:"url_submit",onClick:function(){return e.handleSubmit()},children:"Go"}),""!=this.state.error?Object(a.jsx)("div",{className:"warning",children:this.state.error}):null,this.state.receivedStreams?this.renderStreams():null]})}):Object(a.jsx)("div",{className:"main_wrapper",children:Object(a.jsxs)("div",{className:"main",children:[Object(a.jsx)("link",{rel:"preconnect",href:"https://fonts.gstatic.com"}),Object(a.jsx)("link",{href:"https://fonts.googleapis.com/css2?family=Oswald:wght@500&family=Roboto:wght@300&display=swap",rel:"stylesheet"}),Object(a.jsx)("div",{className:"title",children:"YouTube Scoop"}),Object(a.jsx)("div",{className:"subtitle",children:"A clean, no-frills tool for downloading YouTube videos."}),Object(a.jsxs)("div",{className:"warning",children:["Your browser is not currently supported by YouTube Scoop.",Object(a.jsx)("br",{}),"Please use Google Chrome, Microsoft Edge, or a Chrome-based browser."]})]})})}}]),s}(r.Component);s(15);var j=function(){return document.addEventListener("contextmenu",(function(e){return e.preventDefault()})),Object(a.jsx)("div",{className:"App",children:Object(a.jsx)(m,{})})},b=function(e){e&&e instanceof Function&&s.e(3).then(s.bind(null,17)).then((function(t){var s=t.getCLS,a=t.getFID,r=t.getFCP,n=t.getLCP,c=t.getTTFB;s(e),a(e),r(e),n(e),c(e)}))};i.a.render(Object(a.jsx)(n.a.StrictMode,{children:Object(a.jsx)(j,{})}),document.getElementById("root")),b()}},[[16,1,2]]]);
//# sourceMappingURL=main.37799360.chunk.js.map