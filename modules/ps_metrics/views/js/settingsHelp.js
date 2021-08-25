(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["settingsHelp"],{"399c":function(t,a,e){"use strict";e.r(a);var s=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{staticClass:"pt-2"},[e("Help",{attrs:{faq:this.$store.state.settings.faq,"readme-url":t.readmeUrl}})],1)},o=[],l=function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("b-container",{staticClass:"m-auto p-0"},[s("b-card",{attrs:{"no-body":""},scopedSlots:t._u([{key:"header",fn:function(){return[s("i",{staticClass:"material-icons"},[t._v("help")]),t._v(t._s(t.$t("help.title"))+" ")]},proxy:!0}])},[s("b-card-body",[s("div",{staticClass:"d-flex"},[s("div",{staticClass:"left-block"},[s("div",{staticClass:"module-desc d-flex mb-4"},[s("div",{staticClass:"module-img mr-3"},[s("img",{attrs:{src:e("cf05"),width:"75",height:"75",alt:""}})]),s("div",[s("b",[t._v(t._s(t.$t("help.allowsYouTo.title")))]),s("ul",{staticClass:"mt-3"},[s("li",[t._v(t._s(t.$t("help.allowsYouTo.connect")))]),s("li",[t._v(t._s(t.$t("help.allowsYouTo.collect")))]),s("li",[t._v(t._s(t.$t("help.allowsYouTo.benefit")))])])])]),s("div",{staticClass:"faq"},[s("h1",[t._v(t._s(t.$t("faq.title")))]),s("div",{staticClass:"separator my-3"}),t.faq&&0!=t.faq.categories.length?t._l(t.faq.categories,(function(a,e){return s("v-collapse-group",{key:e,staticClass:"my-3",attrs:{"only-one-active":!0}},[s("h3",{staticClass:"categorie-title"},[t._v(" "+t._s(a.title)+" ")]),t._l(a.blocks,(function(a,o){return s("v-collapse-wrapper",{key:o,ref:e+"_"+o,refInFor:!0},[s("div",{directives:[{name:"collapse-toggle",rawName:"v-collapse-toggle"}],staticClass:"my-3 question"},[s("a",{on:{click:function(e){return e.preventDefault(),t.onTrackQuestion(a.question)}}},[s("i",{staticClass:"material-icons"},[t._v("keyboard_arrow_right")]),t._v(" "+t._s(a.question))])]),s("div",{directives:[{name:"collapse-content",rawName:"v-collapse-content"}],staticClass:"answer",class:"a"+o},[t._v(" "+t._s(a.answer)+" ")])])}))],2)})):[s("b-alert",{attrs:{variant:"warning",show:""}},[s("p",[t._v(t._s(t.$t("faq.noFaq")))])])]],2)]),s("div",{staticClass:"right-block"},[s("div",{staticClass:"doc"},[s("b",{staticClass:"text-muted"},[t._v(t._s(t.$t("help.help.needHelp")))]),s("br"),s("b-button",{staticClass:"mt-3",attrs:{variant:"primary"},on:{click:function(a){return t.onTrackPdf()}}},[t._v(" "+t._s(t.$t("help.help.downloadPdf"))+" ")])],1),s("div",{staticClass:"contact mt-4"},[s("div",[t._v(t._s(t.$t("help.help.couldntFindAnyAnswer")))]),s("div",{staticClass:"mt-2"},[s("b-button",{attrs:{variant:"link"},on:{click:function(a){return t.onTrackMailto()}}},[t._v(" "+t._s(t.$t("help.help.contactUs"))),s("i",{staticClass:"material-icons"},[t._v("arrow_right_alt")])])],1)])])])])],1)],1)},i=[],n={props:["faq","readmeUrl"],methods:{onTrackQuestion:function(t){this.$segment.track("FAQ",{question:t,module:"ps_metrics"})},onTrackMailto:function(){this.$segment.track("mailto",{module:"ps_metrics"}),window.open("mailto:support-metrics@prestashop.com","_blank")},onTrackPdf:function(){this.$segment.track("download user guide",{module:"ps_metrics"}),window.open(this.readmeUrl,"_blank")}}},c=n,r=(e("c03b"),e("2877")),d=Object(r["a"])(c,l,i,!1,null,"8a21f91c",null),p=d.exports,u={components:{Help:p},computed:{readmeUrl:function(){return this.$store.state.app.readmeUrl}}},f=u,v=Object(r["a"])(f,s,o,!1,null,null,null);a["default"]=v.exports},b51a:function(t,a,e){var s=e("24fb");a=s(!1),a.push([t.i,".separator[data-v-8a21f91c]{height:1px;opacity:.2;background:#6b868f;border-bottom:2px solid #6b868f}.left-block[data-v-8a21f91c]{flex-grow:1}.right-block[data-v-8a21f91c]{padding:15px;min-width:350px;text-align:center}.doc[data-v-8a21f91c]{padding:20px}.answer[data-v-8a21f91c],.doc[data-v-8a21f91c]{background-color:#f7f7f7}.answer[data-v-8a21f91c]{margin:0 15px 10px 15px;padding:15px}.icon-expand[data-v-8a21f91c]{transform:rotate(90deg);transition:all .3s}.v-collapse-content[data-v-8a21f91c]{display:none}.v-collapse-content-end[data-v-8a21f91c]{display:block}",""]),t.exports=a},c03b:function(t,a,e){"use strict";e("fdb2")},cf05:function(t,a,e){t.exports=e.p+"img/logo.png"},fdb2:function(t,a,e){var s=e("b51a");s.__esModule&&(s=s.default),"string"===typeof s&&(s=[[t.i,s,""]]),s.locals&&(t.exports=s.locals);var o=e("499e").default;o("0111e392",s,!0,{sourceMap:!1,shadowMode:!1})}}]);