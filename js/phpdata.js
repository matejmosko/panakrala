define(function() {

  let getdata = function() {
    return fetchJson("http://localhost/~gnaag/panakrala/functions.php?script=getData");
  };

  function fetchJson(url) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        //console.log(this.responseText);
        let data = JSON.parse(this.responseText);
        console.log(data);
        return data;
      }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
  }

  return {
    getdata: getdata
  }
});
