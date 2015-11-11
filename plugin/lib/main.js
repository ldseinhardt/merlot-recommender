var pageMod = require("sdk/page-mod").PageMod({
  include: "*://www.merlot.org/merlot/viewMember.htm*",
  contentStyleFile: "./css/merlot.css",
  contentScriptFile: ["./js/jquery.js", "./js/merlot.js"]
});