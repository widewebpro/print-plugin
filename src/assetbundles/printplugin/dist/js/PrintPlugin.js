/*!
 * @project        PrintPlugin
 * @name           PrintPlugin.js
 * @author         
 * @build          Mon, May 31, 2021 1:44 PM ET
 * @release        2503358f081182dd599d9675dc7fd4f36505bc77 [master]
 * @copyright      Copyright (c) 2021 WideWeb
 *
 */!function(t){function e(e){for(var r,i,c=e[0],u=e[1],s=e[2],l=0,f=[];l<c.length;l++)i=c[l],Object.prototype.hasOwnProperty.call(a,i)&&a[i]&&f.push(a[i][0]),a[i]=0;for(r in u)Object.prototype.hasOwnProperty.call(u,r)&&(t[r]=u[r]);for(d&&d(e);f.length;)f.shift()();return o.push.apply(o,s||[]),n()}function n(){for(var t,e=0;e<o.length;e++){for(var n=o[e],r=!0,c=1;c<n.length;c++){var u=n[c];0!==a[u]&&(r=!1)}r&&(o.splice(e--,1),t=i(i.s=n[0]))}return t}var r={},a={0:0},o=[];function i(e){if(r[e])return r[e].exports;var n=r[e]={i:e,l:!1,exports:{}};return t[e].call(n.exports,n,n.exports,i),n.l=!0,n.exports}i.m=t,i.c=r,i.d=function(t,e,n){i.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},i.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},i.t=function(t,e){if(1&e&&(t=i(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)i.d(n,r,function(e){return t[e]}.bind(null,r));return n},i.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return i.d(e,"a",e),e},i.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},i.p="";var c=window.webpackJsonp=window.webpackJsonp||[],u=c.push.bind(c);c.push=e,c=c.slice();for(var s=0;s<c.length;s++)e(c[s]);var d=u;o.push([12,1]),n()}({12:function(t,e,n){"use strict";n.r(e);var r=n(0),a=n.n(r),o=n(1),i=n.n(o),c=function(){var t=i()(a.a.mark((function t(e){var n;return a.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,fetch("/actions/print-plugin/default/get-html?id=".concat(e)).then((function(t){return t.text()})).then((function(t){return JSON.parse(t)})).catch((function(t){return console.error("error",t)}));case 2:return n=t.sent,t.abrupt("return",n);case 4:case"end":return t.stop()}}),t)})));return function(e){return t.apply(this,arguments)}}(),u=function(){var t=i()(a.a.mark((function t(e){var n;return a.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,fetch("/actions/print-plugin/default/get-html?".concat(e)).then((function(t){return t.text()})).then((function(t){return JSON.parse(t)})).catch((function(t){return console.error("error",t)}));case 2:return n=t.sent,t.abrupt("return",n);case 4:case"end":return t.stop()}}),t)})));return function(e){return t.apply(this,arguments)}}(),s=n(5),d=n.n(s),l=n(6),f=n.n(l),p=n(3),m=n.n(p),v=function(){function t(){d()(this,t),this._options={orientation:"portrait",unit:"cm",format:"a4"}}var e,n;return f()(t,[{key:"setOptions",value:function(t){var e,n,r,a,o;"cm"===(null==t?void 0:t.format)||"px"===(null==t?void 0:t.format)?this._options={unit:t.format,orientation:+(null===(e=JSON.parse(null==t?void 0:t.size))||void 0===e?void 0:e.width)<+(null===(n=JSON.parse(null==t?void 0:t.size))||void 0===n?void 0:n.height)?"portrait":"landscape",format:[+(null===(r=JSON.parse(null==t?void 0:t.size))||void 0===r?void 0:r.width),+(null===(a=JSON.parse(null==t?void 0:t.size))||void 0===a?void 0:a.height)]}:this._options={format:null===(o=JSON.parse(null==t?void 0:t.size))||void 0===o?void 0:o.format}}},{key:"createPDF",value:(n=i()(a.a.mark((function t(e){var n,r,o=this;return a.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return(n=document.querySelector(".pdf-container-by-print")).innerHTML=e.trim(),r={allowTaint:!0,useCORS:!0},t.next=5,m()(n,r).then((function(t){var e=t.toDataURL("image/jpeg",1),n=new window.jspdf.jsPDF(o._options),r=n.internal.pageSize.getWidth(),a=n.internal.pageSize.getHeight(),i=t.width,c=t.height,u=i/c>=r/a?r/i:a/c;n.addImage(e,"JPEG",0,0,i*u,c*u),n.save("pdf-file.pdf"),document.querySelector(".pdf-container-by-print").innerHTML=""}));case 5:return t.abrupt("return",!0);case 6:case"end":return t.stop()}}),t)}))),function(t){return n.apply(this,arguments)})},{key:"createJPEG",value:(e=i()(a.a.mark((function t(e){var n,r,o,i=this;return a.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return(n=document.querySelector(".pdf-container-by-print")).innerHTML=e.trim(),r={allowTaint:!0,useCORS:!0},t.next=5,m()(n,r,r.width,r.height).then((function(t){if("px"===i._options.unit){var e=document.createElement("canvas");return e.setAttribute("width",1*i._options.format[0]),e.setAttribute("height",1*i._options.format[1]),e.getContext("2d").drawImage(t,0,0,t.width,t.height,0,0,1*i._options.format[0],1*i._options.format[1]),e.toDataURL("image/jpeg",1)}return t.toDataURL("image/jpeg",1)}));case 5:return o=t.sent,document.querySelector(".pdf-container-by-print").innerHTML="",t.abrupt("return",o);case 8:case"end":return t.stop()}}),t)}))),function(t){return e.apply(this,arguments)})}]),t}();document.addEventListener("DOMContentLoaded",(function(){if(document.getElementById("pdfs-list")){var t=document.getElementById("pdfs-list"),e=new v,n=function(){var t=i()(a.a.mark((function t(e){var n;return a.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,c(e);case 2:return n=t.sent,t.abrupt("return",n||{});case 4:case"end":return t.stop()}}),t)})));return function(e){return t.apply(this,arguments)}}();t.addEventListener("click",(function(t){if(t.target.classList.contains("create-pdf"))try{t.target.classList.add("--disabled"),n(t.target.dataset.id).then((function(n){var r=n.html,a=void 0===r?"<h1>Undefined template</h1>":r,o=n.settings,i=void 0===o?{}:o;e.setOptions(i),e.createPDF(a).then((function(){t.target.classList.remove("--disabled")}))}))}catch(e){console.log("Error!!! ",e),t.target.classList.remove("--disabled")}else if(t.target.classList.contains("create-jpeg"))try{t.target.classList.add("--disabled"),n(t.target.dataset.id).then((function(n){var r=n.html,a=void 0===r?"<h1>Undefined template</h1>":r,o=n.settings,i=void 0===o?{}:o;e.setOptions(i),e.createJPEG(a).then((function(e){var n=document.getElementById("download-link-".concat(t.target.dataset.id));n.href=e,n.click(),t.target.classList.remove("--disabled")}))}))}catch(e){console.log("Error!!! ",e),t.target.classList.remove("--disabled")}}))}}));var h=n(2),g=n.n(h),y=function(t){var e=document.getElementById(t),n=e.firstChild,r=e.offsetWidth,a=n.offsetWidth;if(a>r){var o=r/a;n.style.transform="scale(".concat(o,")")}};document.addEventListener("DOMContentLoaded",(function(){if(document.getElementById("template-redactor-view")){var t=document.getElementById("template-redactor-view"),e=g()(document.getElementsByTagName("select")),n=function(){var e=i()(a.a.mark((function e(n){var i,c,s,d,l;return a.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if("custom"!==n.value){e.next=6;break}i=n.dataset.name,n.closest(".fieldset").querySelector("textarea").classList.add("--active"),n.closest(".fieldset").querySelector("textarea").value=o(i),e.next=16;break;case 6:return document.getElementById("".concat(n.dataset.name,"-textarea")).classList.remove("--active"),c=document.getElementById("template-redactor-view").dataset.id,s=g()(document.getElementsByTagName("select")).reduce((function(t,e){return t[e.dataset.name]=e.value,t}),{}),e.next=11,u("id=".concat(c,"&outputs=")+JSON.stringify(s));case 11:d=e.sent,l=d.html,t.innerHTML=l,g()(document.querySelectorAll("textarea.--active")).forEach((function(t){var e=t.dataset.name;r(e,t.value)})),y("template-redactor-view");case 16:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}();(function(){var t=i()(a.a.mark((function t(e){var n;return a.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,c(e);case 2:return n=t.sent,t.abrupt("return",n.html||{});case 4:case"end":return t.stop()}}),t)})));return function(e){return t.apply(this,arguments)}})()(t.dataset.id).then((function(a){t.classList.add("--active"),t.innerHTML=a,y("template-redactor-view"),g()(document.getElementsByTagName("textarea")).forEach((function(t){var e=t.dataset.name;document.getElementById(e)&&(t.value=o(e)),t.addEventListener("input",(function(){return r(e,t.value)}))})),e.forEach((function(t){"custom"===t.value&&t.closest(".fieldset").querySelector("textarea").classList.add("--active"),t.addEventListener("input",(function(){return n(t)}))}))}));var r=function(t,e){document.querySelector('[data-pp-field="'.concat(t,'"]'))?document.querySelector('[data-pp-field="'.concat(t,'"]')).innerText=e:document.querySelector('[data-pp-background="'.concat(t,'"]'))&&(document.querySelector('[data-pp-background="'.concat(t,'"]')).style.backgroundImage="url(".concat(e,")"))},o=function(t){var e,n,r;return(null===(e=document.querySelector('[data-pp-field="'.concat(t,'"]')))||void 0===e?void 0:e.innerText)||(null===(n=document.querySelector('[data-pp-background="'.concat(t,'"]')))||void 0===n||null===(r=n.style.backgroundImage)||void 0===r?void 0:r.slice(5,-2))||""}}})),document.addEventListener("DOMContentLoaded",(function(){if(document.getElementById("campaign-builder")){var t=document.getElementById("campaign-builder"),e=new v,n=function(){var t=i()(a.a.mark((function t(e,n){var r,o,i,c;return a.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,fetch("/actions/print-plugin/campaign-builder/get-html-campaign?"+"id=".concat(e,"&layout=").concat(n)).then((function(t){return t.json()}));case 2:return r=t.sent,o=r.backHtml,i=r.html,c=r.settings,t.abrupt("return",{backHtml:o,html:i,settings:c}||{});case 7:case"end":return t.stop()}}),t)})));return function(e,n){return t.apply(this,arguments)}}();t.addEventListener("click",(function(t){var r=t.target;if(r.classList.contains("create-pdf")||r.classList.contains("create-jpeg")){r.classList.add("--disabled");try{var a=r.dataset.id,o=r.closest("tr").querySelector('[name="layout"]').value;n(a,o).then((function(t){var n=t.backHtml,a=t.html,o=t.settings,i=void 0===o?{}:o;e.setOptions(i),console.log(n.trim().length),r.classList.contains("create-pdf")?e.createPDF(a).then((function(){n.trim().length&&e.createPDF(n)})):r.classList.contains("create-jpeg")&&e.createJPEG(a).then((function(t){var a=document.getElementById("download-link-".concat(r.dataset.id));a.href=t,a.click(),n.trim().length&&e.createJPEG(n).then((function(t){a.href=t,a.click()}))})),r.classList.remove("--disabled")}))}catch(t){console.log("Error!!! ",t),r.classList.remove("--disabled")}}}))}}))}});
//# sourceMappingURL=PrintPlugin.js.map