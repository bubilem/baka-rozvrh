$(document).ready(function () {
  var section = new URLSearchParams(location.search).get("section");
  if (!section) {
    console.log("No section.");
    return;
  }
  $("title").html(section + " :: rozvrh hodin");
  var jsonTime = 0;
  function ajaxSync() {
    $(this).load("sync.php?section=" + section, function (response, status) {
      if (status == "success") {
        var responseLog = JSON.parse(response);
        console.log("sync", responseLog);
        if (!responseLog.jsonTime || jsonTime != responseLog.jsonTime) {
          jsonTime = responseLog.jsonTime;
          $("main").load("rozvrh-content.php?section=" + section, function (response, status) {
            if (status == "success") {
              console.log("timetable");
            } else {
              console.log("timetable error");
            }
          });
        }
      } else {
        console.log("sync error.");
      }
    });
  }
  ajaxSync();
  setInterval(ajaxSync, 60000);
});
