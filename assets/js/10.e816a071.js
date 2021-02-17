(window.webpackJsonp=window.webpackJsonp||[]).push([[10],{375:function(t,e,a){"use strict";a.r(e);var s=a(42),n=Object(s.a)({},(function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("ContentSlotsDistributor",{attrs:{"slot-key":t.$parent.slotKey}},[a("h2",{attrs:{id:"creating-your-own-strategy"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#creating-your-own-strategy"}},[t._v("#")]),t._v(" Creating your own strategy")]),t._v(" "),a("p",[t._v("All strategies must implement the "),a("a",{attrs:{href:"https://github.com/matchory/herodot/blob/main/src/Contracts/ExtractionStrategy.php",target:"_blank",rel:"noopener noreferrer"}},[a("code",[t._v("ExtractionStrategy")]),t._v(" interface"),a("OutboundLink")],1),t._v(". It\nrequires strategies to implement one central "),a("code",[t._v("handle")]),t._v(" method. It receives the resolved route, and the endpoint instance used to collect information about this\nroute. The job of strategies is analyzing the route, and applying modifications to the endpoint."),a("br"),t._v("\nThis might look like the following:")]),t._v(" "),a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("handle")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),t._v("ResolvedRouteInterface "),a("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$route")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" Endpoint "),a("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$endpoint")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),t._v(" Endpoint "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("if")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$route")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("getHandlerReflector")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("getAttributes")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),t._v("Hidden"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$endpoint")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("setHidden")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n    \n    "),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$endpoint")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])]),a("p",[t._v("Here, we use reflection to check whether the route handler method has the "),a("code",[t._v("Hidden")]),t._v(" attribute set, and if so, mark the endpoint as hidden accordingly. The\nstrategy will be invoked for every route found in the application (and matching your "),a("RouterLink",{attrs:{to:"/guide/configuration.html#route-inclusion-patterns"}},[t._v("inclusion rules")]),t._v("), so with\nthese 7 lines of code we have just added support for hidden routes to our documentation! ...which is already built-in, of course 😉")],1),t._v(" "),a("p",[t._v("Strategies don't have to be exhaustive: All of them work on the same endpoint instance, and work together to add pieces of information, one by one.")]),t._v(" "),a("h3",{attrs:{id:"defining-the-priority"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#defining-the-priority"}},[t._v("#")]),t._v(" Defining the priority")]),t._v(" "),a("p",[t._v("All strategies are required to implement the "),a("code",[t._v("getPriority")]),t._v(" method, which must return an integer priority. Strategies will be sorted by priority, the highest\nlast, the lowest first. This is the mechanism making sure the "),a("a",{attrs:{href:"#override-strategy"}},[t._v("Override strategy")]),t._v(" is run last, unless you choose to overrule it (which we\ndon't recommend).")]),t._v(" "),a("p",[t._v("For reference, this is the priority map of the built-in strategies:")]),t._v(" "),a("table",[a("thead",[a("tr",[a("th",{staticStyle:{"text-align":"left"}},[t._v("Strategy")]),t._v(" "),a("th",{staticStyle:{"text-align":"right"}},[t._v("Priority")])])]),t._v(" "),a("tbody",[a("tr",[a("td",{staticStyle:{"text-align":"left"}},[a("a",{attrs:{href:"#docblock-strategy"}},[t._v("Override Strategy")])]),t._v(" "),a("td",{staticStyle:{"text-align":"right"}},[t._v("999")])]),t._v(" "),a("tr",[a("td",{staticStyle:{"text-align":"left"}},[a("a",{attrs:{href:"#attribute-strategy"}},[t._v("Attribute Strategy")])]),t._v(" "),a("td",{staticStyle:{"text-align":"right"}},[t._v("120")])]),t._v(" "),a("tr",[a("td",{staticStyle:{"text-align":"left"}},[a("a",{attrs:{href:"#openapi-strategy"}},[t._v("OpenAPI Strategy")])]),t._v(" "),a("td",{staticStyle:{"text-align":"right"}},[t._v("90")])]),t._v(" "),a("tr",[a("td",{staticStyle:{"text-align":"left"}},[a("a",{attrs:{href:"#return-type-hint-strategy"}},[t._v("Return Type Hint Strategy")])]),t._v(" "),a("td",{staticStyle:{"text-align":"right"}},[t._v("60")])]),t._v(" "),a("tr",[a("td",{staticStyle:{"text-align":"left"}},[a("a",{attrs:{href:"#docblock-strategy"}},[t._v("DocBlock Strategy")])]),t._v(" "),a("td",{staticStyle:{"text-align":"right"}},[t._v("30")])])])]),t._v(" "),a("p",[t._v("You can fit your strategy in wherever you see fit, but make sure no other strategy overrides your modifications to the endpoints!")]),t._v(" "),a("h3",{attrs:{id:"depending-on-other-strategies"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#depending-on-other-strategies"}},[t._v("#")]),t._v(" Depending on other strategies")]),t._v(" "),a("p",[t._v("Sometimes, it's essential your strategy is run after another one. For these cases, you can declare dependencies: Herodot will make sure your strategy is invoked\nafter all dependencies have been executed. This is achieved using a dependency graph in the background, which sorts out any chain of complex dependencies, but\ntake care not to form cycles: Herodot will bail out in that case.")]),t._v(" "),a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[t._v("\n"),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("getDependencies")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("?")]),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("array")]),t._v("\n"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n        AttributeStrategy"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n        MyCustomStrategy"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])]),a("p",[t._v("If your strategy does not have any dependencies (which is a good thing), you may simply return "),a("code",[t._v("null")]),t._v(" or an empty array here.")]),t._v(" "),a("blockquote",[a("p",[t._v("Note that dependencies "),a("em",[t._v("will")]),t._v(" interfere with priorities. This is by design and allows shifting priorities as required by your documentation, not by the code.\nMost of the time, this won't be a problem though.")])])])}),[],!1,null,null,null);e.default=n.exports}}]);