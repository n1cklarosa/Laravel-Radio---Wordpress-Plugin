!function(){"use strict";var e=window.wp.element;(0,window.wp.blocks.registerBlockType)("myradio-click/program-list",{edit:function(){let a=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],t=(new Date).getDay();return(0,e.createElement)("div",{id:"program-list",class:"program-list"},(0,e.createElement)("div",{class:"program-list-wrapper"},(0,e.createElement)("div",{class:"days-list"},(0,e.createElement)("ul",{class:"weekday-toggles"},a.map(((a,l)=>(0,e.createElement)("li",{key:`listeitem${l}`},(0,e.createElement)("button",{class:`weekday-toggle weekday-toggle${l} ${l===t?"active":""}`,"data-day":l},a)))))),(0,e.createElement)("div",{class:"program-list-programs"},"`",a.map(((a,l)=>(0,e.createElement)("div",{key:`dayitem${l}`,class:`weekday-list ${l===t?"active":""} weekday${l}`}," "))))))},save:function(){let a=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],t=(new Date).getDay();return(0,e.createElement)("div",{id:"program-list",class:"program-list"},(0,e.createElement)("div",{class:"program-list-wrapper"},(0,e.createElement)("div",{class:"days-list"},(0,e.createElement)("ul",{class:"weekday-toggles"},a.map(((a,l)=>(0,e.createElement)("li",{key:`listeitem${l}`},(0,e.createElement)("button",{class:`weekday-toggle weekday-toggle${l} ${l===t?"active":""}`,"data-day":l},a)))))),(0,e.createElement)("div",{class:"program-list-programs"},"`",a.map(((a,l)=>(0,e.createElement)("div",{key:`dayitem${l}`,class:`weekday-list ${l===t?"active":""} weekday${l}`}," "))))))}})}();